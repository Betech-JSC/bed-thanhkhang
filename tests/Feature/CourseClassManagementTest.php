<?php

namespace Tests\Feature;

use App\Models\Contact;
use App\Models\CourseClass;
use App\Models\Post\Post;
use App\Models\Post\PostTranslation;
use App\Services\ClassAllocationService;
use App\Services\CourseClassOrderingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Tests\TestCase;

class CourseClassManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
    }

    private function createDummyCourse(string $title = 'Khóa Học Thử Nghiệm')
    {
        $course = Post::create([
            'type'   => 'COURSE',
            'status' => 'ACTIVE',
            'price'  => 2500000,
        ]);

        $tr = new PostTranslation();
        $tr->post_id = $course->id;
        $tr->locale = 'vi';
        $tr->status_locale = 'ACTIVE';
        $tr->title = $title;
        $tr->slug = 'khoa-hoc-' . uniqid();
        $tr->schedule = [
            [
                'day'          => '15/09/2026',
                'time'         => 'T3 + T5 (19h30-21h30)',
                'mentor'       => 'KTS. Test Mentor',
                'price'        => 2500000,
                'duration'     => '12 buổi',
                'max_students' => 12,
                'types'        => [['id' => 'Offline', 'title' => 'Offline']],
            ]
        ];
        $tr->save();

        return $course->fresh(['translations']);
    }

    public function test_schedule_stores_and_retrieves_max_students_correctly()
    {
        $course = $this->createDummyCourse();
        $tr = $course->translations->first();

        $this->assertNotEmpty($tr->schedule);
        $this->assertEquals(12, $tr->schedule[0]['max_students']);
    }

    public function test_sync_classes_inherits_max_students_from_course()
    {
        $course = $this->createDummyCourse();
        $service = app(ClassAllocationService::class);
        $classes = $service->syncClassesFromCourse($course);

        $this->assertCount(1, $classes);
        $class = $classes->first();
        $this->assertEquals(12, $class->max_students);
        $this->assertEquals('OFFLINE', $class->class_type);
        $this->assertEquals(CourseClass::STATUS_OPEN, $class->status);
    }

    public function test_class_ordering_with_shift_logic()
    {
        $course = $this->createDummyCourse();
        $service = app(CourseClassOrderingService::class);

        $classA = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-A-' . uniqid(),
            'name'          => 'Lớp A',
            'max_students'  => 15,
            'display_order' => 1,
            'status'        => 'OPEN',
            'version'       => 1,
        ]);

        $classB = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-B-' . uniqid(),
            'name'          => 'Lớp B',
            'max_students'  => 15,
            'display_order' => 2,
            'status'        => 'OPEN',
            'version'       => 1,
        ]);

        $classC = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-C-' . uniqid(),
            'name'          => 'Lớp C',
            'max_students'  => 15,
            'display_order' => 3,
            'status'        => 'OPEN',
            'version'       => 1,
        ]);

        // Đổi Lớp C từ vị trí 3 sang vị trí 2 -> Lớp B phải tự dồn thành 3
        $service->reorderClass($classC->id, 2);

        $this->assertEquals(1, $classA->fresh()->display_order);
        $this->assertEquals(2, $classC->fresh()->display_order);
        $this->assertEquals(3, $classB->fresh()->display_order);
    }

    public function test_move_class_up_and_down_arrows()
    {
        $course = $this->createDummyCourse();
        $service = app(CourseClassOrderingService::class);

        $class1 = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-1-' . uniqid(),
            'name'          => 'Lớp 1',
            'display_order' => 1,
            'max_students'  => 15,
            'version'       => 1,
        ]);

        $class2 = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-2-' . uniqid(),
            'name'          => 'Lớp 2',
            'display_order' => 2,
            'max_students'  => 15,
            'version'       => 1,
        ]);

        // Bấm ▲ cho Lớp 2 -> Hoán đổi vị trí với Lớp 1
        $service->moveClass($class2->id, 'UP');
        $this->assertEquals(1, $class2->fresh()->display_order);
        $this->assertEquals(2, $class1->fresh()->display_order);

        // Bấm ▼ cho Lớp 2 -> Hoán đổi lại về vị trí 2
        $service->moveClass($class2->id, 'DOWN');
        $this->assertEquals(2, $class2->fresh()->display_order);
        $this->assertEquals(1, $class1->fresh()->display_order);
    }

    public function test_auto_sort_by_start_date()
    {
        $course = $this->createDummyCourse();
        $service = app(CourseClassOrderingService::class);

        $classLate = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-LATE-' . uniqid(),
            'name'          => 'Lớp Muộn',
            'start_date'    => '2026-11-01',
            'display_order' => 1,
            'max_students'  => 15,
        ]);

        $classEarly = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-EARLY-' . uniqid(),
            'name'          => 'Lớp Sớm',
            'start_date'    => '2026-08-01',
            'display_order' => 2,
            'max_students'  => 15,
        ]);

        $service->autoSort($course->id, 'START_DATE');

        $this->assertEquals(1, $classEarly->fresh()->display_order);
        $this->assertEquals(2, $classLate->fresh()->display_order);
    }

    public function test_optimistic_locking_conflict_on_reorder()
    {
        $course = $this->createDummyCourse();
        $service = app(CourseClassOrderingService::class);

        $class = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CLS-LOCK-' . uniqid(),
            'name'          => 'Lớp Test Lock',
            'display_order' => 1,
            'max_students'  => 15,
            'version'       => 2,
        ]);

        $this->expectException(ConflictHttpException::class);
        $service->reorderClass($class->id, 2, 1); // expected 1 but actual is 2
    }

    public function test_concurrency_lock_on_last_slot_allocation_and_overflow()
    {
        $course = $this->createDummyCourse();
        $service = app(ClassAllocationService::class);

        // Lớp chỉ có đúng 1 slot (max_students = 1)
        $class = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'LAST-SLOT-' . uniqid(),
            'name'              => 'Lớp 1 Slot Duy Nhất',
            'max_students'      => 1,
            'enrolled_students' => 0,
            'display_order'     => 1,
            'status'            => 'OPEN',
        ]);

        $orderA = Contact::create([
            'type'   => 'COURSE',
            'status' => 'CONFIRMED',
            'data'   => ['name' => 'Học Viên A', 'course' => ['price' => 2500000]],
        ]);

        $orderB = Contact::create([
            'type'   => 'COURSE',
            'status' => 'CONFIRMED',
            'data'   => ['name' => 'Học Viên B', 'course' => ['price' => 2500000]],
        ]);

        // Học viên A thanh toán trước -> Chiếm slot chính thức
        $updatedA = $service->allocateStudentToClass($orderA, $class->id);
        $this->assertEquals(Contact::ALLOCATION_ALLOCATED, $updatedA->class_allocation_status);
        $this->assertEquals(1, $class->fresh()->enrolled_students);
        $this->assertEquals(CourseClass::STATUS_FULL, $class->fresh()->status);

        // Học viên B thanh toán sau -> Lớp đã đầy -> Gắn cờ OVERFLOW_PENDING an toàn
        $updatedB = $service->allocateStudentToClass($orderB, $class->id);
        $this->assertEquals(Contact::ALLOCATION_OVERFLOW_PENDING, $updatedB->class_allocation_status);
        $this->assertEquals(1, $class->fresh()->enrolled_students); // Chưa tăng bừa bãi

        // Admin bấm duyệt ngoại lệ (+1 slot) cho Học viên B
        $approvedB = $service->acceptOverflow($updatedB);
        $this->assertEquals(Contact::ALLOCATION_ALLOCATED, $approvedB->class_allocation_status);
        $this->assertEquals(2, $class->fresh()->enrolled_students);
    }

    public function test_admin_change_class_adjusts_counts()
    {
        $course = $this->createDummyCourse();
        $service = app(ClassAllocationService::class);

        $classOld = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'OLD-' . uniqid(),
            'name'              => 'Lớp Cũ',
            'max_students'      => 15,
            'enrolled_students' => 5,
            'display_order'     => 1,
            'status'            => 'OPEN',
        ]);

        $classNew = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'NEW-' . uniqid(),
            'name'              => 'Lớp Mới',
            'max_students'      => 15,
            'enrolled_students' => 2,
            'display_order'     => 2,
            'status'            => 'OPEN',
        ]);

        $student = Contact::create([
            'type'                    => 'COURSE',
            'status'                  => 'CONFIRMED',
            'class_id'                => $classOld->id,
            'class_allocation_status' => Contact::ALLOCATION_ALLOCATED,
            'data'                    => ['name' => 'Nguyễn Chuyển Lớp'],
        ]);

        // Chuyển sang Lớp Mới
        $service->changeClass($student, $classNew->id);

        $this->assertEquals(4, $classOld->fresh()->enrolled_students); // 5 - 1 = 4
        $this->assertEquals(3, $classNew->fresh()->enrolled_students); // 2 + 1 = 3
        $this->assertEquals($classNew->id, $student->fresh()->class_id);
    }

    public function test_controller_endpoints_for_ordering_and_moving()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse();
        $classA = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CTRL-A-' . uniqid(),
            'name'          => 'Class A',
            'display_order' => 1,
            'max_students'  => 10,
            'version'       => 1,
        ]);

        $classB = CourseClass::create([
            'course_id'     => $course->id,
            'class_code'    => 'CTRL-B-' . uniqid(),
            'name'          => 'Class B',
            'display_order' => 2,
            'max_students'  => 10,
            'version'       => 1,
        ]);

        // 1. Move Class B UP
        $respMove = $this->postJson(route('admin.course-classes.move', ['id' => $classB->id]), [
            'direction' => 'UP',
        ]);
        $respMove->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(1, $classB->fresh()->display_order);

        // 2. Reorder Class B back to 2
        $respReorder = $this->postJson(route('admin.course-classes.reorder', ['id' => $classB->id]), [
            'new_order'        => 2,
            'expected_version' => $classB->fresh()->version,
        ]);
        $respReorder->assertStatus(200)->assertJson(['success' => true]);
        $this->assertEquals(2, $classB->fresh()->display_order);
    }

    public function test_controller_allocate_class_action()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse();
        $class = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'ALLOC-TEST-' . uniqid(),
            'name'              => 'Lớp Phân Bổ',
            'max_students'      => 10,
            'enrolled_students' => 0,
            'display_order'     => 1,
        ]);

        $contact = Contact::create([
            'type'   => 'COURSE',
            'status' => 'CONFIRMED',
            'data'   => ['name' => 'Nguyễn Phân Bổ', 'course' => ['price' => 2000000]],
        ]);

        $response = $this->postJson(route('admin.contact-checkout-courses.allocate-class', ['id' => $contact->id]), [
            'class_id' => $class->id,
            'action'   => 'ALLOCATE',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success'                 => true,
                'class_id'                => $class->id,
                'class_allocation_status' => Contact::ALLOCATION_ALLOCATED,
            ]);

        $this->assertEquals(1, $class->fresh()->enrolled_students);
        $this->assertEquals($class->id, $contact->fresh()->class_id);
    }

    public function test_store_creates_class_and_underlying_syncs_to_course_schedule()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse('Khóa Học Khởi Tạo Lớp');
        $classCode = 'TEST-SYNC-STORE-1';
        $zaloUrl = 'https://zalo.me/g/teststore123';

        $payload = [
            'course_id'      => $course->id,
            'class_code'     => $classCode,
            'name'           => 'Lớp Mới Test Sync',
            'start_date'     => '2026-10-15',
            'schedule_time'  => '19h30 - 21h30',
            'duration'       => '12 buổi',
            'mentor'         => 'KTS. Lê Hoàng Kỳ',
            'class_type'     => 'ONLINE',
            'tuition_fee'    => 2800000,
            'max_students'   => 16,
            'status'         => 'OPEN',
            'note'           => 'Lớp học test đồng bộ ngầm',
            'zalo_group_url' => $zaloUrl,
        ];

        $response = $this->postJson(route('admin.course-classes.store'), $payload);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 1. Kiểm tra CourseClass được lưu trong database
        $createdClass = CourseClass::where('class_code', $classCode)->first();
        $this->assertNotNull($createdClass);
        $this->assertEquals('Lớp Mới Test Sync', $createdClass->name);
        $this->assertEquals(16, $createdClass->max_students);
        $this->assertEquals($zaloUrl, $createdClass->zalo_group_url);

        // 2. Kiểm tra phần tử lịch học mới đã được tự động thêm vào post_translations.schedule
        $courseFresh = $course->fresh(['translations']);
        $translation = $courseFresh->translations->first();
        $schedules = $translation->schedule ?? [];

        $matched = collect($schedules)->firstWhere('class_code', $classCode);
        $this->assertNotNull($matched);
        $this->assertEquals('15/10/2026', $matched['day']);
        $this->assertEquals('19h30 - 21h30', $matched['time']);
        $this->assertEquals('KTS. Lê Hoàng Kỳ', $matched['mentor']);
        $this->assertEquals(2800000, $matched['price']);
        $this->assertEquals(16, $matched['max_students']);
        $this->assertEquals($zaloUrl, $matched['zalo_group_url']);
    }

    public function test_update_modifies_underlying_course_schedule()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse('Khóa Học Test Update Sync');
        $classCode = 'TEST-UPDATE-SYNC-1';

        // Tạo lớp ban đầu qua API
        $this->postJson(route('admin.course-classes.store'), [
            'course_id'      => $course->id,
            'class_code'     => $classCode,
            'name'           => 'Lớp Ban Đầu',
            'start_date'     => '2026-10-01',
            'schedule_time'  => '18h00 - 20h00',
            'duration'       => '10 buổi',
            'mentor'         => 'Mentor Cũ',
            'class_type'     => 'ONLINE',
            'tuition_fee'    => 2000000,
            'max_students'   => 12,
            'status'         => 'OPEN',
            'zalo_group_url' => 'https://zalo.me/g/cu123',
        ]);

        $class = CourseClass::where('class_code', $classCode)->firstOrFail();
        $initialVersion = $class->version;

        // Cập nhật lớp học
        $newZaloUrl = 'https://zalo.me/g/moi456';
        $updatePayload = [
            'class_code'     => $classCode,
            'name'           => 'Lớp Đã Cập Nhật',
            'start_date'     => '2026-10-20',
            'schedule_time'  => '19h30 - 21h30',
            'duration'       => '14 buổi',
            'mentor'         => 'Mentor Mới',
            'class_type'     => 'OFFLINE',
            'tuition_fee'    => 3200000,
            'max_students'   => 18,
            'status'         => 'FULL',
            'note'           => 'Đã đổi sang offline',
            'zalo_group_url' => $newZaloUrl,
        ];

        $response = $this->putJson(route('admin.course-classes.update', ['id' => $class->id]), $updatePayload);
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 1. Kiểm tra CourseClass được cập nhật và tăng version
        $classFresh = $class->fresh();
        $this->assertEquals('Lớp Đã Cập Nhật', $classFresh->name);
        $this->assertEquals('OFFLINE', $classFresh->class_type);
        $this->assertEquals(3200000, $classFresh->tuition_fee);
        $this->assertEquals(18, $classFresh->max_students);
        $this->assertEquals(CourseClass::STATUS_FULL, $classFresh->status);
        $this->assertEquals($newZaloUrl, $classFresh->zalo_group_url);
        $this->assertGreaterThan($initialVersion, $classFresh->version);

        // 2. Kiểm tra post_translations.schedule được đồng bộ ngầm
        $courseFresh = $course->fresh(['translations']);
        $translation = $courseFresh->translations->first();
        $schedules = $translation->schedule ?? [];

        $matched = collect($schedules)->firstWhere('class_code', $classCode);
        $this->assertNotNull($matched);
        $this->assertEquals('20/10/2026', $matched['day']);
        $this->assertEquals('19h30 - 21h30', $matched['time']);
        $this->assertEquals('Mentor Mới', $matched['mentor']);
        $this->assertEquals(3200000, $matched['price']);
        $this->assertEquals(18, $matched['max_students']);
        $this->assertEquals($newZaloUrl, $matched['zalo_group_url']);
    }

    public function test_destroy_removes_from_course_schedule_and_deletes_class()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse('Khóa Học Test Delete Sync');
        $classCode = 'TEST-DELETE-SYNC-1';

        $this->postJson(route('admin.course-classes.store'), [
            'course_id'      => $course->id,
            'class_code'     => $classCode,
            'name'           => 'Lớp Sẽ Xóa',
            'start_date'     => '2026-11-01',
            'schedule_time'  => '19h00 - 21h00',
            'class_type'     => 'ONLINE',
            'max_students'   => 15,
            'status'         => 'OPEN',
            'zalo_group_url' => 'https://zalo.me/g/todelete',
        ]);

        $class = CourseClass::where('class_code', $classCode)->firstOrFail();
        $this->assertEquals(0, $class->enrolled_students);

        // Xóa lớp học
        $response = $this->deleteJson(route('admin.course-classes.destroy', ['id' => $class->id]));
        $response->assertStatus(200)->assertJson(['success' => true]);

        // 1. Kiểm tra CourseClass đã bị xóa khỏi DB
        $this->assertNull(CourseClass::where('class_code', $classCode)->first());

        // 2. Kiểm tra schedule đã bị loại bỏ khỏi post_translations.schedule
        $courseFresh = $course->fresh(['translations']);
        $translation = $courseFresh->translations->first();
        $schedules = $translation->schedule ?? [];

        $matched = collect($schedules)->firstWhere('class_code', $classCode);
        $this->assertNull($matched);
    }

    public function test_destroy_blocks_deletion_when_enrolled_students_greater_than_zero()
    {
        $admin = new \JamstackVietnam\Core\Models\Admin();
        $admin->id = 1;
        $admin->name = 'Test Admin';
        $this->actingAs($admin, 'admin');

        $course = $this->createDummyCourse('Khóa Học Test Safe Delete');
        $class = CourseClass::create([
            'course_id'         => $course->id,
            'class_code'        => 'SAFE-DEL-1',
            'name'              => 'Lớp Đã Có Học Viên',
            'max_students'      => 15,
            'enrolled_students' => 3, // Đã có 3 học viên đăng ký
            'status'            => 'OPEN',
            'display_order'     => 1,
        ]);

        // Cố gắng xóa lớp học -> Phải bị chặn với lỗi 422
        $response = $this->deleteJson(route('admin.course-classes.destroy', ['id' => $class->id]));
        $response->assertStatus(422)
            ->assertJson([
                'success' => false,
                'message' => 'Không thể xóa lớp đã có học viên đăng ký!',
            ]);

        // Bản ghi vẫn tồn tại trong CSDL
        $this->assertNotNull(CourseClass::find($class->id));
    }
}
