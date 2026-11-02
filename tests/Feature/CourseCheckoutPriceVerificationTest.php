<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CourseCheckoutPriceVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected Post $course;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);

        Mail::fake();
        Notification::fake();
        Queue::fake();

        $this->course = Post::create([
            'type'         => 'COURSE',
            'status'       => 'ACTIVE',
            'price'        => 2600000,
            'published_at' => now()->subDay(),
        ]);

        $tr = new PostTranslation();
        $tr->post_id = $this->course->id;
        $tr->locale = 'vi';
        $tr->status_locale = 'ACTIVE';
        $tr->title = 'Khóa Học Thiết Kế Funky Style';
        $tr->slug = 'khoa-hoc-thiet-ke-funky-style-' . uniqid();
        $tr->schedule = [
            [
                'day'               => '15/10/2026',
                'time'              => 'T3 + T5 (19h30-21h30)',
                'mentor'            => 'KTS. Nguyễn Văn A',
                'price'             => 2600000,
                'duration'          => '12 buổi',
                'types'             => [['id' => 'Offline', 'title' => 'Offline']],
                'schedule_discount' => [
                    ['title' => 'Ưu đãi chuyển khoản sớm', 'price' => 5],
                ],
            ],
            [
                'day'               => '16/10/2026',
                'time'              => 'T4 + T6 (19h30-21h30)',
                'mentor'            => 'KTS. Trần Văn B',
                'price'             => 2400000,
                'duration'          => '12 buổi',
                'types'             => [['id' => 'Online', 'title' => 'Online']],
                'schedule_discount' => [
                    ['title' => 'Ưu đãi chuyển khoản sớm', 'price' => 5],
                ],
            ],
        ];
        $tr->save();
    }

    /**
     * Trường hợp 1: Đăng ký lịch học Offline (giá 2.600.000đ, giảm 5%) -> DB lưu đúng price = 2.470.000đ theo client.
     */
    public function test_offline_schedule_registration_saves_correct_price()
    {
        $payload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Học Viên Offline',
                    'phone'       => '0901112233',
                    'email'       => 'offline_student@example.com',
                    'waiting'     => 'pay',
                    'course'      => [
                        'id'                      => $this->course->id,
                        'title'                   => 'Khóa Học Thiết Kế Funky Style',
                        'schedule_id'             => 0,
                        'schedule_discount_index' => 0,
                        'price'                   => 2470000,
                        'price_original'          => 2600000,
                        'already_learned'         => 'false',
                    ],
                ],
            ],
        ];

        $response = $this->post(route('vi.contact.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount'  => 2470000,
            ]);

        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'offline_student@example.com')
            ->first();

        $this->assertNotNull($contact, 'Đơn đăng ký khóa học phải được tạo thành công');
        $this->assertEquals(2470000, $contact->data['course']['price'] ?? null, 'Giá khóa học Offline sau giảm 5% phải là 2.470.000đ');
        $this->assertEquals(2600000, $contact->data['course']['price_original'] ?? null, 'Giá gốc khóa học Offline phải là 2.600.000đ');
        $this->assertEquals(0, $contact->data['course']['schedule_id'] ?? null, 'Schedule ID phải là 0');
    }

    /**
     * Trường hợp 2: Đăng ký lịch học Online (giá 2.400.000đ, giảm 5%) -> DB lưu đúng price = 2.280.000đ.
     */
    public function test_online_schedule_registration_saves_correct_price()
    {
        $payload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Học Viên Online',
                    'phone'       => '0902223344',
                    'email'       => 'online_student@example.com',
                    'waiting'     => 'pay',
                    'course'      => [
                        'id'                      => $this->course->id,
                        'title'                   => 'Khóa Học Thiết Kế Funky Style',
                        'schedule_id'             => 1,
                        'schedule_discount_index' => 0,
                        'price'                   => 2280000,
                        'price_original'          => 2400000,
                        'already_learned'         => 'false',
                    ],
                ],
            ],
        ];

        $response = $this->post(route('vi.contact.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount'  => 2280000,
            ]);

        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'online_student@example.com')
            ->first();

        $this->assertNotNull($contact, 'Đơn đăng ký khóa học phải được tạo thành công');
        $this->assertEquals(2280000, $contact->data['course']['price'] ?? null, 'Giá khóa học Online sau giảm 5% phải là 2.280.000đ');
        $this->assertEquals(2400000, $contact->data['course']['price_original'] ?? null, 'Giá gốc khóa học Online phải là 2.400.000đ');
        $this->assertEquals(1, $contact->data['course']['schedule_id'] ?? null, 'Schedule ID phải là 1');
    }

    /**
     * Trường hợp 3 (Tái hiện lỗi AutoCAD người dùng phản ánh):
     * Khóa học giá gốc 2.400.000đ, giảm nhóm 5% (120.000đ) => client tính là 2.280.000đ.
     * Người dùng KHÔNG phải học viên cũ (already_learned = 'false').
     * Backend theo chuẩn d5b022d phải lưu đúng 2.280.000đ, KHÔNG được tự ý trừ thêm 150.000đ thành 2.130.000đ.
     */
    public function test_autocad_group_discount_without_old_student_saves_exact_2280000()
    {
        $payload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Học Viên AutoCAD Nhóm',
                    'phone'       => '0903334455',
                    'email'       => 'autocad_group@example.com',
                    'waiting'     => 'pay',
                    'course'      => [
                        'id'                      => $this->course->id,
                        'title'                   => 'FUNKY AUTOCAD',
                        'schedule_id'             => 1,
                        'groups'                  => [
                            'title'    => 'Nhóm 2-3 người | Giảm 5%',
                            'discount' => 5,
                        ],
                        'price'                   => 2280000,
                        'price_original'          => 2400000,
                        'already_learned'         => 'false',
                    ],
                ],
            ],
        ];

        $response = $this->post(route('vi.contact.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount'  => 2280000,
            ]);

        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'autocad_group@example.com')
            ->first();

        $this->assertNotNull($contact);
        $this->assertEquals(2280000, $contact->data['course']['price']);
        $this->assertNotEquals(2130000, $contact->data['course']['price'], 'Không được bị trừ ngầm 150.000đ thành 2.130.000đ');
    }

    /**
     * Trường hợp 4 (Tái hiện lỗi Rhino người dùng phản ánh):
     * Khóa học giá gốc 3.000.000đ, không có ưu đãi nào => client tính là 3.000.000đ.
     * Người dùng KHÔNG phải học viên cũ (already_learned = 'false').
     * Backend theo chuẩn d5b022d phải lưu đúng 3.000.000đ, KHÔNG được tự ý trừ thêm 150.000đ thành 2.850.000đ.
     */
    public function test_rhino_no_discount_saves_exact_3000000()
    {
        $payload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Học Viên Rhino',
                    'phone'       => '0904445566',
                    'email'       => 'rhino_nodiscount@example.com',
                    'waiting'     => 'pay',
                    'course'      => [
                        'id'              => $this->course->id,
                        'title'           => 'RHINO & GRASSHOPPER',
                        'price'           => 3000000,
                        'price_original'  => 3000000,
                        'already_learned' => 'false',
                    ],
                ],
            ],
        ];

        $response = $this->post(route('vi.contact.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount'  => 3000000,
            ]);

        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'rhino_nodiscount@example.com')
            ->first();

        $this->assertNotNull($contact);
        $this->assertEquals(3000000, $contact->data['course']['price']);
        $this->assertNotEquals(2850000, $contact->data['course']['price'], 'Không được bị trừ ngầm 150.000đ thành 2.850.000đ');
    }

    /**
     * Trường hợp 5: Khóa học có chọn ưu đãi học viên cũ thật (already_learned = true)
     * Client tính toán 2.400.000đ - 120.000đ (nhóm 5%) - 150.000đ (học viên cũ) = 2.130.000đ.
     * Backend lưu chính xác 2.130.000đ và already_learned = true.
     */
    public function test_legitimate_old_student_saves_correct_discounted_price()
    {
        $payload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => [
                    'name'        => 'Cựu Học Viên Thật',
                    'phone'       => '0905556677',
                    'email'       => 'old_student@example.com',
                    'waiting'     => 'pay',
                    'course'      => [
                        'id'              => $this->course->id,
                        'title'           => 'FUNKY AUTOCAD',
                        'price'           => 2130000,
                        'price_original'  => 2400000,
                        'already_learned' => true,
                    ],
                ],
            ],
        ];

        $response = $this->post(route('vi.contact.store'), $payload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'amount'  => 2130000,
            ]);

        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'old_student@example.com')
            ->first();

        $this->assertNotNull($contact);
        $this->assertEquals(2130000, $contact->data['course']['price']);
        $this->assertTrue($contact->data['course']['already_learned']);
    }
}
