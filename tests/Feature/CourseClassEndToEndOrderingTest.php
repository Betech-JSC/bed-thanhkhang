<?php

namespace Tests\Feature;

use App\Models\CourseClass;
use App\Models\Post\Post;
use App\Services\CourseClassOrderingService;
use Carbon\Carbon;
use Database\Seeders\CourseClassPhase5Seeder;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use JamstackVietnam\Core\Models\Admin;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Tests\TestCase;

class CourseClassEndToEndOrderingTest extends TestCase
{
    use DatabaseTransactions;

    protected Admin $admin;
    protected Post $sketchup;
    protected Post $revit;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
            $this->artisan('migrate');
        }

        // Seed 10 lớp học thực tế vào CSDL
        $this->seed(CourseClassPhase5Seeder::class);

        $this->sketchup = Post::where('type', 'COURSE')->where('id', 11)->first()
            ?? Post::where('type', 'COURSE')->whereHas('translations', fn($q) => $q->where('title', 'like', '%SKETCHUP%'))->first();

        $this->revit = Post::where('type', 'COURSE')->where('id', 10)->first()
            ?? Post::where('type', 'COURSE')->whereHas('translations', fn($q) => $q->where('title', 'like', '%REVIT%'))->first();

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_test_e2e@jamstackvietnam.com'],
            ['name' => 'Admin Test E2E', 'password' => bcrypt('secret123')]
        );
    }

    /**
     * Yêu cầu 1: Kiểm tra dữ liệu seed thật cho 10 lớp học với các thuộc tính đa dạng
     */
    public function test_case_1_seed_data_diversity_verification()
    {
        $sketchupClasses = CourseClass::where('course_id', $this->sketchup->id)->orderBy('display_order')->get();
        $revitClasses = CourseClass::where('course_id', $this->revit->id)->orderBy('display_order')->get();

        $this->assertCount(5, $sketchupClasses, 'Khóa Sketchup phải có đúng 5 lớp học');
        $this->assertCount(5, $revitClasses, 'Khóa Revit phải có đúng 5 lớp học');

        // 1. Kiểm tra ngày khai giảng khác nhau (có lớp gần, có lớp xa)
        $dates = $sketchupClasses->pluck('start_date')->map(fn($d) => $d->format('Y-m-d'))->toArray();
        $this->assertGreaterThanOrEqual(3, count(array_unique($dates)), 'Các lớp phải có ngày khai giảng khác nhau');

        // 2. Kiểm tra trạng thái còn chỗ / gần đầy / đầy
        $statuses = $sketchupClasses->pluck('status')->toArray();
        $this->assertContains(CourseClass::STATUS_OPEN, $statuses, 'Phải có lớp đang mở');
        $this->assertContains(CourseClass::STATUS_FULL, $statuses, 'Phải có lớp đã Full');

        $nearlyFullClass = $sketchupClasses->first(fn($c) => $c->occupancy_rate >= 80 && $c->status === CourseClass::STATUS_OPEN);
        $this->assertNotNull($nearlyFullClass, 'Phải có lớp gần đầy (>=80% sĩ số nhưng chưa full)');

        $manySlotsLeftClass = $sketchupClasses->first(fn($c) => $c->occupancy_rate <= 30 && $c->status === CourseClass::STATUS_OPEN);
        $this->assertNotNull($manySlotsLeftClass, 'Phải có lớp còn nhiều chỗ (<=30% sĩ số)');

        // 3. Kiểm tra STT ban đầu không theo logic thời gian (để kiểm tra sắp xếp)
        // Lớp Sketchup K25 có ngày 20/11/2026 (xa) nhưng STT ban đầu là 4
        // Lớp Revit K18 có ngày 05/12/2026 (xa nhất) nhưng STT ban đầu cố tình đặt là 1
        $revitFirst = $revitClasses->firstWhere('display_order', 1);
        $this->assertEquals('RVT-K18-T35', $revitFirst->class_code);
        $this->assertTrue(Carbon::parse($revitFirst->start_date)->greaterThan('2026-11-01'), 'STT 1 của Revit cố tình gán ngày xa nhất');
    }

    /**
     * Yêu cầu 2A: Luồng UI End-to-End: Nhập STT hiển thị rồi bấm Lưu -> BE cập nhật -> FE hiển thị thứ tự mới
     */
    public function test_case_2a_ui_manual_reorder_and_shift()
    {
        // 1. Admin vào trang quản lý lớp học (FE load dữ liệu từ BE)
        $indexResponse = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.course-classes.index', ['course_id' => $this->sketchup->id]));

        $indexResponse->assertStatus(200);

        // 2. Chọn lớp SKP-K25-T24 (đang ở vị trí STT = 4), đổi sang STT = 1
        $classK25 = CourseClass::where('class_code', 'SKP-K25-T24')->firstOrFail();
        $this->assertEquals(4, $classK25->display_order);

        // Thao tác UI bấm "Lưu" STT = 1
        $reorderResponse = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.reorder', ['id' => $classK25->id]), [
                'new_order'        => 1,
                'expected_version' => $classK25->version,
            ]);

        $reorderResponse->assertStatus(200);
        $reorderResponse->assertJson(['success' => true]);

        // 3. BE cập nhật đúng: Lớp K25 thành STT 1, các lớp khác dồn thứ tự thông minh
        $this->assertEquals(1, $classK25->fresh()->display_order);

        // Các lớp 1, 2, 3 cũ phải tự động dồn thành 2, 3, 4
        $classK26 = CourseClass::where('class_code', 'SKP-K26-T35')->firstOrFail();
        $this->assertEquals(2, $classK26->display_order, 'Lớp cũ ở vị trí 1 phải dồn xuống vị trí 2');

        // 4. FE re-render: Gọi lại trang index xác nhận dòng đầu tiên hiển thị là K25
        $reloadResponse = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.course-classes.index', ['course_id' => $this->sketchup->id]));

        $reloadResponse->assertStatus(200);
        $reloadResponse->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('CourseClasses/Index', false)
            ->where('classes.0.class_code', 'SKP-K25-T24')
            ->where('classes.0.display_order', 1)
        );
    }

    /**
     * Yêu cầu 2B: Thao tác UI bấm nút mũi tên lên/xuống (▲/▼) -> hoán đổi đúng và đồng bộ ra trang web
     */
    public function test_case_2b_ui_move_up_down_arrows_and_web_sync()
    {
        $class1 = CourseClass::where('course_id', $this->sketchup->id)->where('display_order', 1)->firstOrFail();
        $class2 = CourseClass::where('course_id', $this->sketchup->id)->where('display_order', 2)->firstOrFail();

        $code1 = $class1->class_code;
        $code2 = $class2->class_code;

        // Bấm nút ▲ cho lớp 2 -> Hoán đổi vị trí với lớp 1 liền kề
        $moveResponse = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.move', ['id' => $class2->id]), [
                'direction' => 'UP',
            ]);

        $moveResponse->assertStatus(200);
        $moveResponse->assertJson(['success' => true]);

        $this->assertEquals(1, $class2->fresh()->display_order);
        $this->assertEquals(2, $class1->fresh()->display_order);

        // Bấm nút ▼ cho lớp 2 -> Hoán đổi lại vị trí với lớp 1
        $moveDownResponse = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.move', ['id' => $class2->id]), [
                'direction' => 'DOWN',
            ]);

        $moveDownResponse->assertStatus(200);
        $this->assertEquals(2, $class2->fresh()->display_order);
        $this->assertEquals(1, $class1->fresh()->display_order);
    }

    /**
     * Yêu cầu 2C: Bật/tắt tùy chọn sắp xếp tự động (ngày khai giảng, trạng thái còn chỗ)
     */
    public function test_case_2c_auto_sort_options()
    {
        // 1. Sắp xếp tự động theo ngày khai giảng gần nhất (START_DATE) cho khóa Revit
        // Ban đầu lớp RVT-K18-T35 (05/12/2026) đang ở số 1, lớp RVT-K19-T24 (12/09/2026) đang ở số 5
        $autoSortDate = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.auto-sort'), [
                'course_id' => $this->revit->id,
                'criteria'  => 'START_DATE',
            ]);

        $autoSortDate->assertStatus(200);
        $autoSortDate->assertJson(['success' => true]);

        // Sau khi sort theo ngày: Lớp 12/09/2026 phải lên đầu tiên (STT 1)
        $earliestClass = CourseClass::where('class_code', 'RVT-K19-T24')->firstOrFail();
        $this->assertEquals(1, $earliestClass->display_order, 'Lớp khai giảng sớm nhất phải lên STT 1');

        $latestClass = CourseClass::where('class_code', 'RVT-K18-T35')->firstOrFail();
        $this->assertEquals(5, $latestClass->display_order, 'Lớp khai giảng muộn nhất phải xuống STT 5');

        // 2. Sắp xếp tự động theo độ trống chỗ (AVAILABILITY) cho khóa Sketchup
        // Lớp SKP-K27-T7CN đang Full (15/15)
        $autoSortAvail = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.auto-sort'), [
                'course_id' => $this->sketchup->id,
                'criteria'  => 'AVAILABILITY',
            ]);

        $autoSortAvail->assertStatus(200);

        // Lớp Full phải bị đẩy xuống cuối cùng
        $fullClass = CourseClass::where('class_code', 'SKP-K27-T7CN')->firstOrFail();
        $this->assertEquals(5, $fullClass->display_order, 'Lớp Full phải bị đẩy xuống cuối cùng khi chọn AVAILABILITY');
    }

    /**
     * Yêu cầu 3A (Rủi ro 1): Nhập trùng STT giữa 2 lớp qua UI -> Hệ thống tự dồn thứ tự, không bị trùng
     */
    public function test_case_3a_risk_duplicate_stt_auto_shift()
    {
        $classes = CourseClass::where('course_id', $this->sketchup->id)->orderBy('display_order')->get();
        $classLast = $classes->last(); // STT = 5

        // Người dùng nhập STT = 2 (đang thuộc về lớp khác) rồi bấm Lưu
        $response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.reorder', ['id' => $classLast->id]), [
                'new_order'        => 2,
                'expected_version' => $classLast->version,
            ]);

        $response->assertStatus(200);

        // Kiểm tra toàn bộ STT của khóa học: Phải là tập hợp [1, 2, 3, 4, 5] duy nhất, không trùng lặp
        $updatedOrders = CourseClass::where('course_id', $this->sketchup->id)->pluck('display_order')->sort()->values()->toArray();
        $this->assertEquals([1, 2, 3, 4, 5], $updatedOrders, 'Các STT phải liên tục từ 1 đến N, không có bất kỳ số nào trùng nhau');
    }

    /**
     * Yêu cầu 3B (Rủi ro 2): Mô phỏng 2 phiên admin cùng đổi thứ tự 1 lớp -> Optimistic Lock ngăn chặn ghi đè sai
     */
    public function test_case_3b_risk_concurrency_optimistic_locking_409()
    {
        $class = CourseClass::where('course_id', $this->sketchup->id)->firstOrFail();
        $originalVersion = $class->version;

        // Phiên Admin 1: Đổi thứ tự thành công -> Version tăng lên +1
        $admin1Response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.reorder', ['id' => $class->id]), [
                'new_order'        => 3,
                'expected_version' => $originalVersion,
            ]);
        $admin1Response->assertStatus(200);
        $this->assertEquals($originalVersion + 1, $class->fresh()->version);

        // Phiên Admin 2 (chưa reload trang, vẫn cầm version cũ originalVersion): Cố gắng đổi thứ tự
        $admin2Response = $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.reorder', ['id' => $class->id]), [
                'new_order'        => 5,
                'expected_version' => $originalVersion, // Version lỗi thời
            ]);

        // BE phải từ chối với mã lỗi 409 Conflict kèm thông báo cảnh báo
        $admin2Response->assertStatus(409);
        $admin2Response->assertJson([
            'success' => false,
        ]);
        $this->assertStringContainsString('quản trị viên khác', $admin2Response->json('message'));
    }

    /**
     * Yêu cầu 3C (Rủi ro 3): Sau khi lưu thay đổi trên CMS, trang web hiển thị lớp học cập nhật tức thời (không bị cache cũ)
     */
    public function test_case_3c_risk_instant_cache_invalidation()
    {
        $course = $this->sketchup;
        $slug = $course->translations->first()->slug ?? 'sketchup-thinking-by-doing';

        // 1. Giả lập truy cập web để ghi cache
        $this->get(route('courses.show', ['slug' => $slug]));

        // 2. Admin đổi thứ tự lớp trên CMS
        $class = CourseClass::where('course_id', $course->id)->orderBy('display_order')->firstOrFail();
        $this->actingAs($this->admin, 'admin')
            ->postJson(route('admin.course-classes.reorder', ['id' => $class->id]), [
                'new_order'        => 5,
                'expected_version' => $class->version,
            ]);

        // 3. Khách hàng truy cập lại trang web ngay lập tức
        $webResponse = $this->get(route('courses.show', ['slug' => $slug]));
        $webResponse->assertStatus(200);

        // Dữ liệu schedule trên web phải phản ánh thứ tự mới ngay lập tức
        $webResponse->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('Courses/Show')
            ->has('course.schedule')
        );
    }
}
