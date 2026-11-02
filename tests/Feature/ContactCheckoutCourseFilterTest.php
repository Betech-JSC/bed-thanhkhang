<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ContactCheckoutCourseFilterTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_course_filter_test@jamstackvietnam.com'],
            [
                'name'     => 'Admin Course Filter Tester',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Helper tạo đơn hàng đăng ký khóa học cho test case
     */
    protected function createOrder(string $courseTitle, string $status = Contact::STATUS_NEW, array $extraData = []): Contact
    {
        $contact = Contact::create([
            'type'   => 'TYPE_CHECKOUT_COURSE',
            'status' => $status,
            'data'   => array_merge([
                'name'   => 'Học Viên ' . uniqid(),
                'phone'  => '09' . rand(10000000, 99999999),
                'email'  => 'student_' . uniqid() . '@example.com',
                'course' => [
                    'id'    => rand(1, 999),
                    'title' => $courseTitle,
                    'price' => 5000000,
                ],
            ], $extraData),
        ]);

        // Contact::booted() static::creating() tự động gán status = STATUS_NEW.
        // Nếu cần test với status khác (như CONFIRMED, PROCESSED, v.v.), cập nhật lại:
        if ($status !== Contact::STATUS_NEW) {
            $contact->status = $status;
            $contact->saveQuietly();
        }

        return $contact;
    }

    /**
     * Test 1: Trang index trả về prop `courses` chứa danh sách khóa học cho Inertia frontend.
     */
    public function test_index_page_returns_courses_prop_for_inertia_frontend()
    {
        $courseA = 'Khóa Học Kiến Trúc Nội Thất 3Ds Max';
        $courseB = 'Khóa Học Revit Thực Chiến';

        $this->createOrder($courseA);
        $this->createOrder($courseB);

        $response = $this->actingAs($this->admin, 'admin')->get(
            route('admin.contact-checkout-courses.index')
        );

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('FormCheckoutCourse/Index')
            ->has('courses')
            ->has('schema')
            ->has('breadcrumbs')
            ->where('courses', function ($courses) use ($courseA, $courseB) {
                $collection = collect($courses);
                return $collection->contains($courseA) && $collection->contains($courseB);
            })
        );
    }

    /**
     * Test 2: Lọc theo khóa học (`filters[course]`):
     * Tạo đơn hàng đăng ký khóa A và đơn hàng khóa B.
     * Gửi request lọc khóa A -> Assert response JSON data chỉ chứa đơn khóa A, không chứa đơn khóa B.
     */
    public function test_filter_by_course_returns_only_orders_matching_course()
    {
        $courseA = 'Khóa Học SketchUp Nâng Cao';
        $courseB = 'Khóa Học AutoCAD Cơ Bản';

        $orderA = $this->createOrder($courseA, Contact::STATUS_NEW);
        $orderB = $this->createOrder($courseB, Contact::STATUS_NEW);

        $response = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => [
                    'course' => $courseA,
                ],
            ])
        );

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($orderA->id, $returnedIds, 'Danh sách phải chứa đơn hàng của khóa A');
        $this->assertNotContains($orderB->id, $returnedIds, 'Danh sách không được chứa đơn hàng của khóa B');
    }

    /**
     * Test 3: Lọc theo trạng thái (`filters[status]`):
     * Tạo đơn trạng thái `CONFIRMED` và đơn trạng thái `NEW`.
     * Gửi request lọc status `CONFIRMED` -> Assert response JSON data chỉ chứa đơn `CONFIRMED`.
     */
    public function test_filter_by_status_returns_only_orders_matching_status()
    {
        $course = 'Khóa Học Photoshop Hậu Kỳ';

        $orderConfirmed = $this->createOrder($course, Contact::STATUS_CONFIRMED);
        $orderNew = $this->createOrder($course, Contact::STATUS_NEW);

        $response = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => [
                    'status' => Contact::STATUS_CONFIRMED,
                ],
            ])
        );

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($orderConfirmed->id, $returnedIds, 'Danh sách phải chứa đơn hàng trạng thái CONFIRMED');
        $this->assertNotContains($orderNew->id, $returnedIds, 'Danh sách không được chứa đơn hàng trạng thái NEW');

        // Xác minh tất cả bản ghi trả về đều có status = CONFIRMED
        foreach ($response->json('data') as $item) {
            $this->assertEquals(Contact::STATUS_CONFIRMED, $item['status']);
        }
    }

    /**
     * Test 4: Lọc kết hợp Khóa học + Trạng thái:
     * Tạo 4 đơn với các tổ hợp khóa học và trạng thái khác nhau.
     * Gửi request kết hợp cả 2 bộ lọc -> Assert chỉ có đơn thỏa mãn cả 2 tiêu chí được trả về.
     */
    public function test_combined_filter_by_course_and_status()
    {
        $courseA = 'Khóa Học Thiết Kế Quy Hoạch';
        $courseB = 'Khóa Học Diễn Họa Lumion';

        // 1. Khóa A + CONFIRMED (Thỏa mãn cả 2 tiêu chí)
        $orderTarget = $this->createOrder($courseA, Contact::STATUS_CONFIRMED);

        // 2. Khóa A + NEW (Sai trạng thái)
        $orderA_New = $this->createOrder($courseA, Contact::STATUS_NEW);

        // 3. Khóa B + CONFIRMED (Sai khóa học)
        $orderB_Confirmed = $this->createOrder($courseB, Contact::STATUS_CONFIRMED);

        // 4. Khóa B + NEW (Sai cả 2)
        $orderB_New = $this->createOrder($courseB, Contact::STATUS_NEW);

        $response = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => [
                    'course' => $courseA,
                    'status' => Contact::STATUS_CONFIRMED,
                ],
            ])
        );

        $response->assertStatus(200);

        $returnedIds = collect($response->json('data'))->pluck('id')->toArray();

        $this->assertContains($orderTarget->id, $returnedIds, 'Chỉ đơn thỏa mãn cả Khóa A và trạng thái CONFIRMED được trả về');
        $this->assertNotContains($orderA_New->id, $returnedIds, 'Đơn sai trạng thái không được trả về');
        $this->assertNotContains($orderB_Confirmed->id, $returnedIds, 'Đơn sai khóa học không được trả về');
        $this->assertNotContains($orderB_New->id, $returnedIds, 'Đơn sai cả 2 tiêu chí không được trả về');
    }

    /**
     * Test 5: Khi truyền filter rỗng hoặc `ALL`, hệ thống trả về đầy đủ không bị giới hạn.
     */
    public function test_empty_or_all_filters_return_all_orders_without_restriction()
    {
        $courseA = 'Khóa Học Corona Renderer';
        $courseB = 'Khóa Học Blender 3D Kiến Trúc';

        $orderA = $this->createOrder($courseA, Contact::STATUS_NEW);
        $orderB = $this->createOrder($courseB, Contact::STATUS_CONFIRMED);

        // Case 5.1: Không truyền params filters
        $resWithoutFilters = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index')
        );
        $resWithoutFilters->assertStatus(200);
        $idsWithoutFilters = collect($resWithoutFilters->json('data'))->pluck('id')->toArray();
        $this->assertContains($orderA->id, $idsWithoutFilters);
        $this->assertContains($orderB->id, $idsWithoutFilters);

        // Case 5.2: Truyền filters rỗng
        $resEmptyFilters = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => [
                    'course' => '',
                    'status' => '',
                ],
            ])
        );
        $resEmptyFilters->assertStatus(200);
        $idsEmptyFilters = collect($resEmptyFilters->json('data'))->pluck('id')->toArray();
        $this->assertContains($orderA->id, $idsEmptyFilters);
        $this->assertContains($orderB->id, $idsEmptyFilters);

        // Case 5.3: Truyền filters giá trị 'ALL'
        $resAllFilters = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => [
                    'course' => 'ALL',
                    'status' => 'ALL',
                ],
            ])
        );
        $resAllFilters->assertStatus(200);
        $idsAllFilters = collect($resAllFilters->json('data'))->pluck('id')->toArray();
        $this->assertContains($orderA->id, $idsAllFilters);
        $this->assertContains($orderB->id, $idsAllFilters);
    }

    /**
     * Test 6: Kiểm tra tính toàn vẹn dữ liệu đơn hàng khóa học với trường học, liên hệ, ngày đăng ký và bộ lọc.
     */
    public function test_order_data_integrity_with_school_contact_and_registration_fields()
    {
        $course1 = 'Khóa Học Thiết Kế Nhà Phố Chuyên Nghiệp';
        $course2 = 'Khóa Học Kết Cấu Thép Nâng Cao';

        // Đơn 1: Dùng key trường học tiếng Việt 'Bạn đang, đã học trường nào'
        $order1 = $this->createOrder($course1, Contact::STATUS_CONFIRMED, [
            'name'                        => 'Trần Văn Nam',
            'phone'                       => '0912345678',
            'email'                       => 'nam.tran@example.com',
            'link_facebook'               => 'https://facebook.com/nam.tran.design',
            'Bạn đang, đã học trường nào' => 'Đại học Bách Khoa TP.HCM',
            'Ngành học hiện tại'          => 'Kiến trúc Công trình',
            'Hiện tại bạn đang là'        => 'Sinh viên năm cuối',
        ]);

        // Đơn 2: Dùng key trường học 'school' (fallback)
        $order2 = $this->createOrder($course2, Contact::STATUS_NEW, [
            'name'                 => 'Lê Thị Mai',
            'phone'                => '0987654321',
            'email'                => 'mai.le@example.com',
            'link_facebook'        => 'https://facebook.com/mai.le.arch',
            'school'               => 'Đại học Xây Dựng Hà Nội',
            'Ngành học hiện tại'   => 'Kỹ thuật Xây dựng',
            'Hiện tại bạn đang là' => 'Đã đi làm',
        ]);

        // 1. Kiểm tra Inertia index page trả về 200 OK với schema và courses
        $inertiaRes = $this->actingAs($this->admin, 'admin')->get(
            route('admin.contact-checkout-courses.index')
        );
        $inertiaRes->assertStatus(200);
        $inertiaRes->assertInertia(fn (Assert $page) => $page
            ->component('FormCheckoutCourse/Index')
            ->has('courses')
            ->has('schema')
            ->where('courses', function ($courses) use ($course1, $course2) {
                $collection = collect($courses);
                return $collection->contains($course1) && $collection->contains($course2);
            })
        );

        // 2. Kiểm tra JSON API trả về dữ liệu toàn vẹn
        $apiRes = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index')
        );
        $apiRes->assertStatus(200);

        $items = collect($apiRes->json('data'));
        $item1 = $items->firstWhere('id', $order1->id);
        $item2 = $items->firstWhere('id', $order2->id);

        $this->assertNotNull($item1, 'Đơn 1 phải có trong danh sách');
        $this->assertNotNull($item2, 'Đơn 2 phải có trong danh sách');

        // Xác thực trường học (hỗ trợ cả tiếng Việt và fallback 'school')
        $this->assertEquals('Đại học Bách Khoa TP.HCM', $item1['school']);
        $this->assertEquals('Đại học Bách Khoa TP.HCM', $item1['data']['Bạn đang, đã học trường nào']);
        $this->assertEquals('Đại học Xây Dựng Hà Nội', $item2['school']);
        $this->assertEquals('Đại học Xây Dựng Hà Nội', $item2['data']['school']);

        // Xác thực liên hệ: phone, email, link_facebook
        $this->assertEquals('0912345678', $item1['phone']);
        $this->assertEquals('nam.tran@example.com', $item1['email']);
        $this->assertEquals('https://facebook.com/nam.tran.design', $item1['link_facebook']);

        $this->assertEquals('0987654321', $item2['phone']);
        $this->assertEquals('mai.le@example.com', $item2['email']);
        $this->assertEquals('https://facebook.com/mai.le.arch', $item2['link_facebook']);

        // Xác thực ngành học
        $this->assertEquals('Kiến trúc Công trình', $item1['major']);
        $this->assertEquals('Kiến trúc Công trình', $item1['Ngành học hiện tại']);
        $this->assertEquals('Kỹ thuật Xây dựng', $item2['major']);

        // Xác thực ngày đăng ký (created_at và formatted_created_at)
        $this->assertNotNull($item1['created_at']);
        $this->assertNotNull($item1['formatted_created_at']);
        $this->assertNotNull($item2['created_at']);
        $this->assertNotNull($item2['formatted_created_at']);

        // 3. Xác thực bộ lọc theo Khóa học
        $filterCourseRes = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => ['course' => $course1],
            ])
        );
        $filterCourseRes->assertStatus(200);
        $courseIds = collect($filterCourseRes->json('data'))->pluck('id')->toArray();
        $this->assertContains($order1->id, $courseIds);
        $this->assertNotContains($order2->id, $courseIds);

        // 4. Xác thực bộ lọc theo Trạng thái
        $filterStatusRes = $this->actingAs($this->admin, 'admin')->getJson(
            route('admin.contact-checkout-courses.index', [
                'filters' => ['status' => Contact::STATUS_NEW],
            ])
        );
        $filterStatusRes->assertStatus(200);
        $statusIds = collect($filterStatusRes->json('data'))->pluck('id')->toArray();
        $this->assertContains($order2->id, $statusIds);
        $this->assertNotContains($order1->id, $statusIds);
    }
}
