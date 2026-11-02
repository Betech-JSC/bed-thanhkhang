<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use App\Services\ClassAllocationService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JamstackVietnam\Core\Models\Admin;
use Tests\TestCase;

class CourseCheckoutUiTest extends TestCase
{
    use RefreshDatabase;

    protected ClassAllocationService $allocationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        $this->allocationService = app(ClassAllocationService::class);
    }

    private function createCourseWithClass(string $title = 'Khóa Học Thiết Kế Nội Thất K99', int $price = 6500000): array
    {
        $course = Post::create([
            'type'         => 'COURSE',
            'status'       => 'ACTIVE',
            'price'        => $price,
            'published_at' => now()->subDay(),
        ]);

        $classCode = 'KHTKNT-TEST-' . rand(100, 999);

        $tr = new PostTranslation();
        $tr->post_id = $course->id;
        $tr->locale = 'vi';
        $tr->status_locale = 'ACTIVE';
        $tr->title = $title;
        $tr->slug = 'khoa-hoc-thiet-ke-noi-that-test-' . uniqid();
        $tr->schedule = [
            [
                'class_code'   => $classCode,
                'name'         => 'Lớp Test UI Checkout',
                'day'          => '15/10/2026',
                'time'         => 'T3 + T5 (19h30-21h30)',
                'mentor'       => 'KTS. Nguyễn Văn A',
                'price'        => $price,
                'duration'     => '12 buổi',
                'max_students' => 15,
                'types'        => [['id' => 'Offline', 'title' => 'Offline']],
            ]
        ];
        $tr->save();

        $courseClass = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => $classCode,
            'name'              => 'Lớp Test UI Checkout',
            'start_date'        => '2026-10-15',
            'schedule_time'     => 'T3 + T5 (19h30-21h30)',
            'mentor'            => 'KTS. Nguyễn Văn A',
            'tuition_fee'       => $price,
            'max_students'      => 15,
            'enrolled_students' => 0,
            'status'            => 'OPEN',
            'display_order'     => 1,
        ]);

        return [$course->fresh(['translations']), $courseClass];
    }

    /**
     * Test quy trình đăng ký ghi danh khóa học từ Frontend UI:
     * - Học viên: Nguyễn Lê Nhật Bảo
     * - SĐT: 0901234567
     * - Email: nhatbao.test@gmail.com
     * - Khóa học: Khóa Học Thiết Kế Nội Thất K99
     * 
     * Đảm bảo:
     * 1. Contact được tạo thành công dạng TYPE_CHECKOUT_COURSE.
     * 2. Học viên được phân bổ tự động vào lớp (HELD_PENDING giữ chỗ 24h).
     * 3. Học viên xuất hiện trong danh sách học viên của lớp (Class Roster Table).
     * 4. Admin có thể xem được học viên trong màn hình chi tiết lớp học /admin/course-classes/{id}.
     */
    public function test_frontend_course_checkout_registers_student_and_allocates_to_class_roster()
    {
        [$course, $courseClass] = $this->createCourseWithClass('Khóa Học Thiết Kế Nội Thất K99 - Test Sync 2026', 6500000);

        $studentData = [
            'name'        => 'Nguyễn Lê Nhật Bảo',
            'phone'       => '0901234567',
            'email'       => 'nhatbao.test@gmail.com',
            'waiting'     => 'pay',
            'Bạn có muốn' => 'pay',
            'course'      => [
                'id'          => $course->id,
                'class_id'    => $courseClass->id,
                'title'       => $course->translations->first()->title,
                'slug'        => $course->translations->first()->slug,
                'price'       => 6500000,
                'schedule_id' => 0,
            ],
        ];

        $postPayload = [
            'contact' => [
                'type' => Contact::TYPE_CHECKOUT_COURSE,
                'data' => $studentData,
            ],
        ];

        // Gửi request POST /contacts giống hành vi từ giao diện frontend
        $response = $this->post(route('vi.contact.store'), $postPayload, [
            'Accept' => 'application/json',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        // 1. Kiểm tra Contact tồn tại trong CSDL
        $contact = Contact::where('type', Contact::TYPE_CHECKOUT_COURSE)
            ->where('data->email', 'nhatbao.test@gmail.com')
            ->first();

        $this->assertNotNull($contact, 'Đơn đăng ký của học viên phải được lưu trong DB');
        $this->assertEquals('Nguyễn Lê Nhật Bảo', $contact->data['name'] ?? null);
        $this->assertEquals('0901234567', $contact->data['phone'] ?? null);

        // 2. Kiểm tra học viên được tự động phân bổ vào lớp học tương ứng
        $this->assertEquals($courseClass->id, $contact->class_id, 'Học viên phải được gán đúng class_id');
        $this->assertEquals(Contact::ALLOCATION_HELD_PENDING, $contact->class_allocation_status, 'Học viên mới đăng ký phải ở trạng thái HELD_PENDING');

        // 3. Kiểm tra danh sách học viên trong lớp
        $rosterStudents = $courseClass->students()->get();
        $this->assertTrue($rosterStudents->contains('id', $contact->id), 'Học viên phải có mặt trong danh sách học viên của lớp');

        // 4. Kiểm tra màn hình Admin xem chi tiết lớp học (/admin/course-classes/{id})
        $admin = new Admin();
        $admin->id = 1;
        $admin->name = 'Super Admin';
        $this->actingAs($admin, 'admin');

        $adminResponse = $this->get(route('admin.course-classes.show', ['id' => $courseClass->id]));
        $adminResponse->assertStatus(200);

        // Kiểm tra dữ liệu inertia props truyền xuống view có chứa học viên Nguyễn Lê Nhật Bảo
        $props = $adminResponse->viewData('page')['props'] ?? [];
        $studentsInProps = collect($props['students'] ?? []);
        $foundInRoster = $studentsInProps->firstWhere('name', 'Nguyễn Lê Nhật Bảo');

        $this->assertNotNull($foundInRoster, 'Học viên Nguyễn Lê Nhật Bảo phải xuất hiện trong dữ liệu bảng Roster gửi xuống UI Admin');
        $this->assertEquals('0901234567', $foundInRoster['phone']);
        $this->assertEquals('nhatbao.test@gmail.com', $foundInRoster['email']);
        $this->assertEquals(Contact::ALLOCATION_HELD_PENDING, $foundInRoster['class_allocation_status']);
    }
}
