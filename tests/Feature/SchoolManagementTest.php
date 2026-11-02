<?php

namespace Tests\Feature;

use App\Models\School;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SchoolManagementTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        }

        // Đảm bảo role Super Admin và Gate::before được cấp quyền đầy đủ cho Admin
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_test_school@jamstackvietnam.com'],
            [
                'name'     => 'Admin Tester',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    protected function tearDown(): void
    {
        // Dọn dẹp triệt để dữ liệu test để không làm bẩn CSDL
        School::where('short_name', 'TESTU')
            ->orWhere('name', 'like', '%Kiểm thử%')
            ->orWhere('name', 'like', '%Test School%')
            ->forceDelete();

        parent::tearDown();
    }

    /**
     * Test 1: Kiểm tra API lấy danh sách trường học trả về danh sách xếp theo position ASC,
     * trong đó 15 trường đầu tiên tương ứng với 15 chip bấm nhanh tại Frontend Checkout.
     */
    public function test_api_returns_schools_sorted_by_position_and_top_15_chips()
    {
        // Tạo 20 trường với position 1..20, 1 trường position null, và 1 trường INACTIVE
        for ($i = 1; $i <= 20; $i++) {
            School::create([
                'name'       => "Test School {$i}",
                'short_name' => "TS{$i}",
                'type'       => 'DH',
                'status'     => School::STATUS_ACTIVE,
                'position'   => $i,
            ]);
        }

        School::create([
            'name'       => 'Test School No Position',
            'short_name' => 'TSNP',
            'type'       => 'OTHER',
            'status'     => School::STATUS_ACTIVE,
            'position'   => null,
        ]);

        School::create([
            'name'       => 'Test School Inactive',
            'short_name' => 'TSINA',
            'type'       => 'DH',
            'status'     => School::STATUS_INACTIVE,
            'position'   => 1,
        ]);

        // Kiểm tra endpoint API frontend: GET /api/academic-options
        $responseAcademic = $this->getJson(route('api.academic-options'));
        $responseAcademic->assertStatus(200)
            ->assertJsonStructure([
                'schools',
                'majors',
                'currentStatusOptions',
                'schoolYears',
            ]);

        $academicSchools = $responseAcademic->json('schools');
        $this->assertGreaterThanOrEqual(21, count($academicSchools));

        // Kiểm tra endpoint GET /api/schools
        $responseSchools = $this->getJson(route('api.schools'));
        $responseSchools->assertStatus(200)
            ->assertJsonStructure([
                'schools',
                'majors',
                'current_status',
            ]);

        $schools = $responseSchools->json('schools');

        // Không chứa trường INACTIVE
        $names = collect($schools)->pluck('name')->toArray();
        $this->assertNotContains('Test School Inactive', $names);

        // Kiểm tra 15 trường đầu tiên tương ứng với 15 chip bấm nhanh (dynamicSchools.slice(0, 15))
        $top15Chips = array_slice($schools, 0, 15);
        $this->assertCount(15, $top15Chips);

        for ($i = 0; $i < 15; $i++) {
            $expectedIndex = $i + 1;
            $this->assertEquals("Test School {$expectedIndex}", $top15Chips[$i]['name']);
            $this->assertEquals("TS{$expectedIndex}", $top15Chips[$i]['short_name']);
        }

        // Trường không có position phải nằm ở sau các trường có position
        $lastSchool = end($schools);
        $this->assertEquals('Test School No Position', $lastSchool['name']);
    }

    /**
     * Test 2: Admin đăng nhập và truy cập GET /admin/schools trả về status 200 và Inertia page Schools/Index.
     */
    public function test_admin_can_access_schools_index_page()
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.schools.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Schools/Index')
            ->has('schema')
            ->has('breadcrumbs')
        );

        // Kiểm tra cả request table JSON (CMS DataTable)
        $jsonResponse = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.schools.index'));

        $jsonResponse->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'total',
            ]);
    }

    /**
     * Test 3: Admin tạo mới trường học qua POST /admin/schools/store với dữ liệu:
     * name = 'Đại học Kiểm thử Tự động', short_name = 'TESTU', type = 'DH', position = 1, status = 'active'.
     * Kiểm tra trường mới được lưu vào database và API trả về trường mới này trong danh sách ưu tiên.
     */
    public function test_admin_can_create_new_school_via_store_and_syncs_to_api()
    {
        $payload = [
            'name'       => 'Đại học Kiểm thử Tự động',
            'short_name' => 'TESTU',
            'type'       => 'DH',
            'position'   => 1,
            'status'     => 'active',
        ];

        // Gửi request tạo mới trường học từ CMS Admin
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.schools.store'), $payload);

        // HasCrudActions redirect về form khi thành công
        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));

        // Kiểm tra bản ghi đã được lưu đúng vào database
        $this->assertDatabaseHas('schools', [
            'name'       => 'Đại học Kiểm thử Tự động',
            'short_name' => 'TESTU',
            'type'       => 'DH',
            'position'   => 1,
        ]);

        $createdSchool = School::where('short_name', 'TESTU')->first();
        $this->assertNotNull($createdSchool);
        $this->assertEquals(School::STATUS_ACTIVE, $createdSchool->status);

        // Kiểm tra API trả về trường mới này trong danh sách ưu tiên (vị trí đầu tiên do position = 1)
        $apiResponse = $this->getJson(route('api.schools'));
        $apiResponse->assertStatus(200);

        $schools = $apiResponse->json('schools');
        $this->assertNotEmpty($schools);
        $this->assertEquals('Đại học Kiểm thử Tự động', $schools[0]['name']);
        $this->assertEquals('TESTU', $schools[0]['short_name']);

        // Đồng thời kiểm tra cả API academic-options của Frontend Checkout
        $academicResponse = $this->getJson(route('api.academic-options'));
        $academicResponse->assertStatus(200);

        $academicSchools = $academicResponse->json('schools');
        $this->assertNotEmpty($academicSchools);
        $this->assertEquals('Đại học Kiểm thử Tự động', $academicSchools[0]['name']);
    }

    /**
     * Test 4: Xóa bản ghi test sau khi test xong để không làm bẩn database.
     */
    public function test_admin_can_delete_school_and_cleans_up_test_record()
    {
        $school = School::create([
            'name'       => 'Đại học Kiểm thử Cần Xóa',
            'short_name' => 'DEL_TEST',
            'type'       => 'DH',
            'status'     => School::STATUS_ACTIVE,
            'position'   => 99,
        ]);

        $this->assertDatabaseHas('schools', ['id' => $school->id, 'deleted_at' => null]);

        // Admin gọi route destroy để xóa trường học
        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.schools.destroy', ['id' => $school->id]));

        $this->assertTrue(in_array($response->getStatusCode(), [200, 302]));

        // Kiểm tra bản ghi đã bị soft-deleted
        $this->assertSoftDeleted('schools', ['id' => $school->id]);

        // Kiểm tra API frontend không còn trả về trường đã bị xóa
        $apiResponse = $this->getJson(route('api.schools'));
        $apiSchools = $apiResponse->json('schools');
        $schoolIds = collect($apiSchools)->pluck('id')->toArray();
        $this->assertNotContains($school->id, $schoolIds);

        // Dọn dẹp hoàn toàn (forceDelete)
        $school->forceDelete();
        $this->assertDatabaseMissing('schools', ['id' => $school->id]);
    }
}
