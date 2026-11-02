<?php

namespace Tests\Feature;

use App\Jobs\SyncCourseOrderToGoogleSheetJob;
use App\Models\Contact;
use App\Models\ContactStatusLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactCheckoutVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_verifier@jamstackvietnam.com'],
            [
                'name'     => 'Admin Verifier',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Mục 1: Xác minh API Quick Update Note
     * - Viết test gửi request POST tới admin/contact-checkout-courses/{id}/quick-update-note với note mới.
     * - Kiểm tra status 200, response JSON success và kiểm tra database bảng contacts xem data['note'] đã được lưu đúng chưa.
     */
    public function test_quick_update_note_api_saves_to_database_successfully()
    {
        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'  => 'Học Viên Test Note',
                'phone' => '0901112233',
                'email' => 'note_test@example.com',
                'note'  => 'Ghi chú ban đầu lúc nộp đơn',
            ],
        ]);

        $newNote = 'Ghi chú cập nhật từ CMS: Học viên hẹn chuyển khoản vào chiều nay';

        $response = $this->actingAs($this->admin, 'admin')->postJson(
            route('admin.contact-checkout-courses.quick-update-note', ['id' => $contact->id]),
            ['note' => $newNote]
        );

        // 1. Kiểm tra HTTP Status và JSON response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'message' => 'Đã cập nhật ghi chú thành công',
                'note'    => $newNote,
            ]);

        // 2. Kiểm tra database bảng contacts xem data['note'] đã được lưu đúng chưa
        $contact->refresh();
        $this->assertEquals($newNote, $contact->data['note'] ?? null);
        $this->assertEquals($newNote, $contact->data['note_1'] ?? null);
    }

    /**
     * Mục 1 (bổ sung): Xác minh Validation cho API Quick Update Note
     */
    public function test_quick_update_note_validation_fails_when_note_exceeds_max_length()
    {
        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'data'   => ['name' => 'Học Viên Test Validation'],
        ]);

        $tooLongNote = str_repeat('A', 2001);

        $response = $this->actingAs($this->admin, 'admin')->postJson(
            route('admin.contact-checkout-courses.quick-update-note', ['id' => $contact->id]),
            ['note' => $tooLongNote]
        );

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['note']);
    }

    /**
     * Mục 2: Xác minh API Change Status
     * - Gửi request POST admin/contact-checkout-courses/{id}/change-status với trạng thái mới (CONFIRMED).
     * - Kiểm tra status cập nhật chuẩn xác.
     */
    public function test_change_status_api_updates_status_and_records_audit_log()
    {
        Queue::fake();

        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'   => 'Học Viên Test Trạng Thái',
                'phone'  => '0933445566',
                'email'  => 'status_test@example.com',
                'course' => ['title' => 'Khóa Học Kiến Trúc Nội Thất K99', 'price' => 5000000],
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')->postJson(
            route('admin.contact-checkout-courses.change-status', ['id' => $contact->id]),
            [
                'new_status' => Contact::STATUS_CONFIRMED,
                'note'       => 'Học viên đã nộp chuyển khoản học phí đầy đủ',
            ]
        );

        // 1. Kiểm tra HTTP Status và JSON response
        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status'  => Contact::STATUS_CONFIRMED,
            ]);

        // 2. Kiểm tra database status của contact đã đổi sang CONFIRMED
        $contact->refresh();
        $this->assertEquals(Contact::STATUS_CONFIRMED, $contact->status);

        // 3. Kiểm tra Audit Log được ghi lại trong bảng contact_status_logs
        $auditLog = ContactStatusLog::where('contact_id', $contact->id)->latest()->first();
        $this->assertNotNull($auditLog, 'Audit log phải được ghi lại khi đổi trạng thái');
        $this->assertEquals(Contact::STATUS_NEW, $auditLog->old_status);
        $this->assertEquals(Contact::STATUS_CONFIRMED, $auditLog->new_status);
        $this->assertEquals('Học viên đã nộp chuyển khoản học phí đầy đủ', $auditLog->note);
        $this->assertEquals($this->admin->id, $auditLog->changed_by);

        // 4. Xác nhận Job đồng bộ Google Sheet được kích hoạt khi chuyển sang CONFIRMED
        Queue::assertPushed(SyncCourseOrderToGoogleSheetJob::class, function ($job) use ($contact) {
            return $job->contactId === $contact->id;
        });
    }

    /**
     * Mục 3: Xác minh việc loại bỏ hoàn toàn các routes Quản lý lớp học
     * - Gửi request tới admin/course-classes và admin/contact-checkout-courses/class-roster.
     * - Xác minh các route này trả về 404 Not Found.
     */
    public function test_removed_course_class_and_roster_routes_return_404()
    {
        // 1. Route admin/course-classes
        $responseClass = $this->actingAs($this->admin, 'admin')->followingRedirects()->get('/admin/course-classes');
        $this->assertEquals(404, $responseClass->status(), 'Route /admin/course-classes phải trả về 404 Not Found');

        // Direct localized path /vi/admin/course-classes
        $responseClassDirect = $this->actingAs($this->admin, 'admin')->get('/vi/admin/course-classes');
        $this->assertEquals(404, $responseClassDirect->status(), 'Route /vi/admin/course-classes phải trả về 404 Not Found');

        // 2. Route admin/contact-checkout-courses/class-roster
        $responseRoster = $this->actingAs($this->admin, 'admin')->followingRedirects()->get('/admin/contact-checkout-courses/class-roster');
        $this->assertEquals(404, $responseRoster->status(), 'Route /admin/contact-checkout-courses/class-roster phải trả về 404 Not Found');

        // Direct localized path /vi/admin/contact-checkout-courses/class-roster
        $responseRosterDirect = $this->actingAs($this->admin, 'admin')->get('/vi/admin/contact-checkout-courses/class-roster');
        $this->assertEquals(404, $responseRosterDirect->status(), 'Route /vi/admin/contact-checkout-courses/class-roster phải trả về 404 Not Found');
    }

    /**
     * Mục 4: Xác minh dữ liệu trả về cho Index
     * - Truy vấn trang danh sách đơn hàng admin/contact-checkout-courses.
     * - Xác nhận response trả về danh sách có đầy đủ các trường: trường học, phone, email, link_facebook, Ngành học hiện tại, Hiện tại bạn đang là, note, created_at.
     */
    public function test_index_api_returns_all_required_student_fields()
    {
        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'                        => 'Nguyễn Hoàng Sơn',
                'phone'                       => '0987654321',
                'email'                       => 'son.nguyen@gmail.com',
                'link_facebook'               => 'https://facebook.com/son.nguyen.design',
                'Bạn đang, đã học trường nào' => 'Đại học Kiến Trúc TP.HCM',
                'Ngành học hiện tại'          => 'Kiến trúc Nội thất Dân dụng',
                'Hiện tại bạn đang là'        => 'Sinh viên năm 3 ĐH Kiến Trúc',
                'note'                        => 'Cần xếp vào lớp học khung giờ tối thứ 3, 5',
            ],
        ]);

        // Gửi GET request tới route index với Accept: application/json
        $response = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index')
        );

        $response->assertStatus(200);

        $jsonResponse = $response->json();
        $this->assertArrayHasKey('data', $jsonResponse, 'Response phải chứa mảng data phân trang');

        $items = $jsonResponse['data'];
        $item = collect($items)->firstWhere('id', $contact->id);

        $this->assertNotNull($item, 'Đơn hàng vừa tạo phải có mặt trong danh sách trả về');

        // Kiểm tra đầy đủ các trường theo yêu cầu:
        // 1. Số điện thoại
        $this->assertArrayHasKey('phone', $item);
        $this->assertEquals('0987654321', $item['phone']);

        // 2. Email
        $this->assertArrayHasKey('email', $item);
        $this->assertEquals('son.nguyen@gmail.com', $item['email']);

        // 3. Link Facebook
        $this->assertArrayHasKey('link_facebook', $item);
        $this->assertEquals('https://facebook.com/son.nguyen.design', $item['link_facebook']);

        // 4. Trường học
        $this->assertArrayHasKey('school', $item);
        $this->assertEquals('Đại học Kiến Trúc TP.HCM', $item['school']);
        $this->assertEquals('Đại học Kiến Trúc TP.HCM', $item['data']['Bạn đang, đã học trường nào']);

        // 5. Ngành học hiện tại
        $this->assertArrayHasKey('Ngành học hiện tại', $item);
        $this->assertEquals('Kiến trúc Nội thất Dân dụng', $item['Ngành học hiện tại']);
        $this->assertEquals('Kiến trúc Nội thất Dân dụng', $item['major']);

        // 6. Hiện tại bạn đang là
        $this->assertArrayHasKey('Hiện tại bạn đang là', $item);
        $this->assertEquals('Sinh viên năm 3 ĐH Kiến Trúc', $item['Hiện tại bạn đang là']);

        // 7. Ghi chú (note)
        $this->assertArrayHasKey('note', $item);
        $this->assertEquals('Cần xếp vào lớp học khung giờ tối thứ 3, 5', $item['note']);

        // 8. Ngày đăng ký (created_at & formatted_created_at)
        $this->assertArrayHasKey('created_at', $item);
        $this->assertNotNull($item['created_at']);
        $this->assertArrayHasKey('formatted_created_at', $item);
        $this->assertNotNull($item['formatted_created_at']);
    }

    /**
     * Mục 5: Xác minh trường học hỗ trợ fallback sang key 'school'
     */
    public function test_index_api_supports_school_fallback_key()
    {
        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => Contact::STATUS_NEW,
            'data'   => [
                'name'          => 'Phạm Quỳnh Anh',
                'phone'         => '0909988776',
                'email'         => 'quynhanh@example.com',
                'link_facebook' => 'https://facebook.com/quynhanh.arch',
                'school'        => 'Đại học Mỹ Thuật TP.HCM',
                'major'         => 'Thiết kế Nội thất',
                'current_job'   => 'Sinh viên',
            ],
        ]);

        $response = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index')
        );

        $response->assertStatus(200);

        $items = $response->json('data');
        $item = collect($items)->firstWhere('id', $contact->id);

        $this->assertNotNull($item);
        $this->assertEquals('Đại học Mỹ Thuật TP.HCM', $item['school']);
        $this->assertEquals('0909988776', $item['phone']);
        $this->assertEquals('quynhanh@example.com', $item['email']);
        $this->assertEquals('https://facebook.com/quynhanh.arch', $item['link_facebook']);
        $this->assertEquals('Thiết kế Nội thất', $item['major']);
        $this->assertNotNull($item['created_at']);
    }

    /**
     * Mục 6: Xác minh Inertia index page trả về 200 OK với đầy đủ props schema và courses
     */
    public function test_index_page_renders_with_schema_and_courses_props()
    {
        $response = $this->actingAs($this->admin, 'admin')->get(
            route('admin.contact-checkout-courses.index')
        );

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('FormCheckoutCourse/Index')
            ->has('courses')
            ->has('schema')
            ->has('breadcrumbs')
        );
    }
}
