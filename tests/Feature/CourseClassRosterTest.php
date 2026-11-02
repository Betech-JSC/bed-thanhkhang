<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JamstackVietnam\Core\Models\Admin;
use Tests\TestCase;

class CourseClassRosterTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);

        $admin = new Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');
    }

    private function createCourseWithClass(string $title = 'Sketchup Master', string $classCode = 'SKP-K01', string $zaloUrl = 'https://zalo.me/g/testgroup')
    {
        $course = Post::create([
            'type'   => 'COURSE',
            'status' => 'ACTIVE',
            'price'  => 2400000,
        ]);

        $tr = new PostTranslation();
        $tr->post_id = $course->id;
        $tr->locale = 'vi';
        $tr->status_locale = 'ACTIVE';
        $tr->title = $title;
        $tr->slug = 'course-' . uniqid();
        $tr->schedule = [
            [
                'day'                 => '25/10/2026',
                'time'                => 'T2 + T4 (19h30-21h30)',
                'mentor'              => 'KTS. Quân',
                'price'               => 2400000,
                'class_code'          => $classCode,
                'max_students'        => 15,
                'enrolled_students'   => 0,
                'display_order'       => 1,
                'zalo_group_url'      => $zaloUrl,
                'is_countdown_active' => 1,
                'starts_at'           => Carbon::now()->addDays(10)->format('Y-m-d 19:30:00'),
            ]
        ];
        $tr->save();

        $class = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => $classCode,
            'name'              => $title . ' - Lớp 1',
            'start_date'        => Carbon::now()->addDays(10)->toDateString(),
            'schedule_time'     => 'T2 + T4 (19h30-21h30)',
            'tuition_fee'       => 2400000,
            'max_students'      => 15,
            'enrolled_students' => 0,
            'display_order'     => 1,
            'status'            => CourseClass::STATUS_OPEN,
        ]);

        return [$course, $class];
    }

    public function test_class_roster_returns_students_and_summary_and_all_classes()
    {
        [$course, $class] = $this->createCourseWithClass('Sketchup Pro', 'SKP-ROSTER-1');

        $contact1 = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'                  => 'Nguyễn Văn A',
                'phone'                 => '0901234567',
                'email'                 => 'vana@gmail.com',
                'link_facebook'         => 'https://fb.com/vana',
                'Trường bạn đã/đang học'=> 'ĐH Kiến Trúc',
                'Ngành học hiện tại'    => 'Kiến trúc công trình',
                'Hiện tại bạn đang là'  => 'Sinh viên năm 4',
                'paid_installment_1'    => 500000,
                'paid_installment_2'    => 0,
                'paid_amount'           => 500000,
                'debt_amount'           => 1900000,
                'has_joined_group'      => false,
                'note_1'                => 'Xin vào nhóm',
                'note_2'                => '',
                'course'                => [
                    'id'             => $course->id,
                    'title'          => 'Sketchup Pro',
                    'price'          => 2400000,
                    'price_original' => 2400000,
                ],
            ],
        ]);

        $contact2 = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'                  => 'Trần Thị B',
                'phone'                 => '0912345678',
                'email'                 => 'thib@gmail.com',
                'link_facebook'         => 'https://fb.com/thib',
                'Trường bạn đã/đang học'=> 'ĐH Xây Dựng',
                'Ngành học hiện tại'    => 'Kỹ thuật công trình',
                'Hiện tại bạn đang là'  => 'Đã đi làm',
                'paid_installment_1'    => 1000000,
                'paid_installment_2'    => 1400000,
                'paid_amount'           => 2400000,
                'debt_amount'           => 0,
                'has_joined_group'      => true,
                'note_1'                => 'Đã đóng đủ',
                'note_2'                => 'Tặng thêm sách',
                'course'                => [
                    'id'             => $course->id,
                    'title'          => 'Sketchup Pro',
                    'price'          => 2400000,
                    'price_original' => 2400000,
                ],
            ],
        ]);

        $response = $this->getJson(route('admin.contact-checkout-courses.class-roster'));

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'selected_class' => [
                    'class_code'              => 'SKP-ROSTER-1',
                    'max_students'            => 15,
                    'enrolled_students'       => 2,
                    'total_expected_revenue'  => 4800000,
                    'total_collected_revenue' => 2900000,
                    'total_pending_revenue'   => 1900000,
                ],
                'summary' => [
                    'total_expected_revenue'  => 4800000,
                    'total_collected_revenue' => 2900000,
                    'total_pending_revenue'   => 1900000,
                    'total_students'          => 2,
                    'max_students'            => 15,
                ],
            ]);

        $students = $response->json('students');
        $this->assertCount(2, $students);

        $studentA = collect($students)->firstWhere('id', $contact1->id);
        $this->assertNotNull($studentA);
        $this->assertEquals('Nguyễn Văn A', $studentA['name']);
        $this->assertEquals('0901234567', $studentA['phone']);
        $this->assertEquals('vana@gmail.com', $studentA['email']);
        $this->assertEquals('https://fb.com/vana', $studentA['link_facebook']);
        $this->assertEquals('ĐH Kiến Trúc', $studentA['school']);
        $this->assertEquals('Kiến trúc công trình', $studentA['major']);
        $this->assertEquals('Sinh viên năm 4', $studentA['current_job']);
        $this->assertEquals('Sketchup Pro', $studentA['course_title']);
        $this->assertEquals(2400000, $studentA['original_price']);
        $this->assertEquals(2400000, $studentA['final_price']);
        $this->assertEquals(500000, $studentA['paid_installment_1']);
        $this->assertEquals(0, $studentA['paid_installment_2']);
        $this->assertEquals(500000, $studentA['paid_amount']);
        $this->assertEquals(1900000, $studentA['debt_amount']);
        $this->assertFalse($studentA['has_joined_group']);
        $this->assertEquals('Xin vào nhóm', $studentA['note_1']);
        $this->assertNotEmpty($studentA['created_at']);

        $studentB = collect($students)->firstWhere('id', $contact2->id);
        $this->assertNotNull($studentB);
        $this->assertEquals('Trần Thị B', $studentB['name']);
        $this->assertEquals(2400000, $studentB['paid_amount']);
        $this->assertEquals(0, $studentB['debt_amount']);
        $this->assertTrue($studentB['has_joined_group']);
        $this->assertEquals('ĐÃ THANH TOÁN', $studentB['payment_status']);

        $classes = $response->json('classes');
        $this->assertNotEmpty($classes);
        $this->assertEquals('SKP-ROSTER-1', $classes[0]['class_code']);
    }

    public function test_class_roster_filters_by_class_code_and_class_id()
    {
        [$courseA, $classA] = $this->createCourseWithClass('Revit Basic', 'RVT-01');
        [$courseB, $classB] = $this->createCourseWithClass('Revit Advanced', 'RVT-02');

        Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $classB->id,
            'data'     => ['name' => 'Học viên lớp B', 'course' => ['price' => 2800000]],
        ]);

        // Filter by class_code
        $response = $this->getJson(route('admin.contact-checkout-courses.class-roster', ['class_code' => 'RVT-02']));
        $response->assertStatus(200);
        $this->assertEquals('RVT-02', $response->json('selected_class.class_code'));
        $this->assertCount(1, $response->json('students'));
        $this->assertEquals('Học viên lớp B', $response->json('students.0.name'));

        // Filter by class_id
        $responseId = $this->getJson(route('admin.contact-checkout-courses.class-roster', ['class_id' => $classA->id]));
        $responseId->assertStatus(200);
        $this->assertEquals('RVT-01', $responseId->json('selected_class.class_code'));
        $this->assertCount(0, $responseId->json('students'));
    }

    public function test_update_roster_item_recalculates_paid_and_debt_and_sets_paid_status()
    {
        [$course, $class] = $this->createCourseWithClass('Sketchup Pro', 'SKP-UP1');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'               => 'Lê Văn C',
                'paid_installment_1' => 250000,
                'paid_installment_2' => 0,
                'paid_amount'        => 250000,
                'debt_amount'        => 1705000,
                'course'             => [
                    'price' => 1955000,
                ],
            ],
        ]);

        // Đóng nốt đợt 2: 1,705,000
        $response = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'paid_installment_2' => 1705000,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item = $response->json('item');
        $this->assertEquals(250000, $item['paid_installment_1']);
        $this->assertEquals(1705000, $item['paid_installment_2']);
        $this->assertEquals(1955000, $item['paid_amount']);
        $this->assertEquals(0, $item['debt_amount']);
        $this->assertEquals('ĐÃ THANH TOÁN', $item['payment_status']);

        $contactFresh = $contact->fresh();
        $this->assertEquals(1955000, $contactFresh->data['paid_amount']);
        $this->assertEquals(0, $contactFresh->data['debt_amount']);
        $this->assertEquals('PAID', $contactFresh->data['payment_status']);
    }

    public function test_update_roster_item_partial_payment_sets_deposited_status()
    {
        [$course, $class] = $this->createCourseWithClass('3dsMax Master', 'MAX-UP1');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'   => 'Hoàng Văn D',
                'course' => [
                    'price' => 2300000,
                ],
            ],
        ]);

        // Đóng cọc đợt 1: 500,000
        $response = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'paid_installment_1' => 500000,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item = $response->json('item');
        $this->assertEquals(500000, $item['paid_installment_1']);
        $this->assertEquals(0, $item['paid_installment_2']);
        $this->assertEquals(500000, $item['paid_amount']);
        $this->assertEquals(1800000, $item['debt_amount']);
        $this->assertStringContainsString('ĐÃ CỌC', $item['payment_status']);

        $contactFresh = $contact->fresh();
        $this->assertEquals('PARTIAL', $contactFresh->data['payment_status']);
    }

    public function test_update_roster_item_toggle_join_group_and_notes()
    {
        [$course, $class] = $this->createCourseWithClass('Lumion', 'LUM-01');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'             => 'Phạm Văn E',
                'has_joined_group' => false,
                'note_1'           => '',
                'note_2'           => '',
                'course'           => ['price' => 1500000],
            ],
        ]);

        // Toggle join group và thêm notes
        $response = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'has_joined_group' => true,
            'note_1'           => 'Đã liên hệ Zalo xác nhận',
            'note_2'           => 'Tặng thêm voucher 10%',
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item = $response->json('item');
        $this->assertTrue($item['has_joined_group']);
        $this->assertEquals('Đã liên hệ Zalo xác nhận', $item['note_1']);
        $this->assertEquals('Tặng thêm voucher 10%', $item['note_2']);

        $contactFresh = $contact->fresh();
        $this->assertTrue($contactFresh->data['has_joined_group']);
        $this->assertEquals('Đã liên hệ Zalo xác nhận', $contactFresh->data['note_1']);
        $this->assertEquals('Tặng thêm voucher 10%', $contactFresh->data['note_2']);
    }

    public function test_installment_calculation_full_payment_in_installment_1_auto_marks_paid()
    {
        [$course, $class] = $this->createCourseWithClass('Revit Interior', 'RVT-FULL');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'   => 'Trần Thanh Toàn',
                'course' => [
                    'price' => 2400000,
                ],
            ],
        ]);

        // Đóng toàn bộ học phí 2.400.000₫ ngay đợt 1
        $response = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'paid_installment_1' => 2400000,
            'paid_installment_2' => 0,
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $item = $response->json('item');
        $this->assertEquals(2400000, $item['paid_installment_1']);
        $this->assertEquals(0, $item['paid_installment_2']);
        $this->assertEquals(2400000, $item['paid_amount']);
        $this->assertEquals(0, $item['debt_amount']);
        $this->assertEquals('ĐÃ THANH TOÁN', $item['payment_status']);

        $contactFresh = $contact->fresh();
        $this->assertEquals('PAID', $contactFresh->data['payment_status']);
        $this->assertEquals('ĐÃ THANH TOÁN', $contactFresh->data['status_text']);
        $this->assertEquals(0, $contactFresh->data['debt_amount']);
    }

    public function test_installment_progressive_payment_and_auto_recalculation()
    {
        [$course, $class] = $this->createCourseWithClass('Blender 3D', 'BLD-SPLIT');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'   => 'Lý Chia Đợt',
                'course' => [
                    'price' => 3000000,
                ],
            ],
        ]);

        // Bước 1: Đóng đợt 1: 1.000.000₫ -> Còn lại: 2.000.000₫, trạng thái ĐÃ CỌC
        $resStep1 = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'paid_installment_1' => 1000000,
        ]);
        $resStep1->assertStatus(200);
        $item1 = $resStep1->json('item');
        $this->assertEquals(1000000, $item1['paid_installment_1']);
        $this->assertEquals(0, $item1['paid_installment_2']);
        $this->assertEquals(1000000, $item1['paid_amount']);
        $this->assertEquals(2000000, $item1['debt_amount']);
        $this->assertStringContainsString('ĐÃ CỌC', $item1['payment_status']);
        $this->assertEquals('PARTIAL', $contact->fresh()->data['payment_status']);

        // Bước 2: Đóng tiếp đợt 2: 2.000.000₫ -> Tổng thu: 3.000.000₫, Còn lại: 0₫, tự động chuyển sang ĐÃ THANH TOÁN
        $resStep2 = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'paid_installment_2' => 2000000,
        ]);
        $resStep2->assertStatus(200);
        $item2 = $resStep2->json('item');
        $this->assertEquals(1000000, $item2['paid_installment_1']);
        $this->assertEquals(2000000, $item2['paid_installment_2']);
        $this->assertEquals(3000000, $item2['paid_amount']);
        $this->assertEquals(0, $item2['debt_amount']);
        $this->assertEquals('ĐÃ THANH TOÁN', $item2['payment_status']);
        $this->assertEquals('PAID', $contact->fresh()->data['payment_status']);
    }

    public function test_toggle_join_group_can_be_enabled_and_disabled()
    {
        [$course, $class] = $this->createCourseWithClass('V-Ray Next', 'VRAY-TOGGLE');

        $contact = Contact::create([
            'type'     => Contact::TYPE_CHECKOUT_COURSE,
            'status'   => Contact::STATUS_CONFIRMED,
            'class_id' => $class->id,
            'data'     => [
                'name'             => 'Ngô Toggle',
                'has_joined_group' => false,
                'course'           => ['price' => 2000000],
            ],
        ]);

        // Toggle ON
        $resOn = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'has_joined_group' => true,
        ]);
        $resOn->assertStatus(200);
        $this->assertTrue($resOn->json('item.has_joined_group'));
        $this->assertTrue($contact->fresh()->data['has_joined_group']);

        // Toggle OFF
        $resOff = $this->postJson(route('admin.contact-checkout-courses.update-roster-item', ['id' => $contact->id]), [
            'has_joined_group' => false,
        ]);
        $resOff->assertStatus(200);
        $this->assertFalse($resOff->json('item.has_joined_group'));
        $this->assertFalse($contact->fresh()->data['has_joined_group']);
    }

    public function test_zalo_group_url_synchronized_in_roster_api()
    {
        $expectedUrl = 'https://zalo.me/g/synchronize123';
        [$course, $class] = $this->createCourseWithClass('Rhino Parametric', 'RH-SYNC', $expectedUrl);

        $response = $this->getJson(route('admin.contact-checkout-courses.class-roster', ['class_code' => 'RH-SYNC']));
        $response->assertStatus(200);

        $this->assertEquals($expectedUrl, $response->json('selected_class.zalo_group_url'));
        $classes = $response->json('classes');
        $foundClass = collect($classes)->firstWhere('class_code', 'RH-SYNC');
        $this->assertNotNull($foundClass);
        $this->assertEquals($expectedUrl, $foundClass['zalo_group_url']);
    }

    public function test_course_class_resolves_zalo_group_url_from_course_schedule()
    {
        [$course, $class] = $this->createCourseWithClass('Enscape Pro', 'ENS-01', 'https://zalo.me/g/enscapegroup123');

        $this->assertEquals('https://zalo.me/g/enscapegroup123', $class->zalo_group_url);
    }

    public function test_next_opening_schedule_endpoint_returns_future_date()
    {
        [$course, $class] = $this->createCourseWithClass('Rhino Advanced', 'RH-NEXT');

        $response = $this->getJson(route('api.course.getNextOpeningSchedule'));

        $response->assertStatus(200);
        $this->assertNotEmpty($response->json('starts_at'));
        $this->assertNotEmpty($response->json('title'));

        $startsAt = Carbon::parse($response->json('starts_at'));
        $this->assertTrue($startsAt->gte(Carbon::now()->subMinute()));
    }
}
