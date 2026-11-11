<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminUserGuideTest extends TestCase
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
            ['email' => 'admin_guide_test@jamstackvietnam.com'],
            [
                'name'     => 'Admin Guide Tester',
                'password' => bcrypt('secret123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Test 1: Khách vãng lai chưa đăng nhập bị chặn truy cập và chuyển hướng về đăng nhập (HTTP 302).
     */
    public function test_guest_is_redirected_to_admin_login(): void
    {
        $responseVi = $this->get('/admin/user-guide');
        $this->assertEquals(302, $responseVi->getStatusCode());

        $responseEn = $this->get('/admin/en/user-guide');
        $this->assertEquals(302, $responseEn->getStatusCode());
    }

    /**
     * Test 2: Quản trị viên truy cập GET /admin/user-guide thành công (HTTP 200) và render Inertia component UserGuide.
     */
    public function test_authenticated_admin_can_access_user_guide_vi(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/user-guide');
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('UserGuide')
        );
    }

    /**
     * Test 3: Quản trị viên truy cập GET /admin/en/user-guide ngôn ngữ Tiếng Anh thành công (HTTP 200).
     */
    public function test_authenticated_admin_can_access_user_guide_en(): void
    {
        $response = $this->actingAs($this->admin, 'admin')->get('/admin/en/user-guide');
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('UserGuide')
        );
    }

    /**
     * Test 4: Route name admin.user-guide.index được đăng ký đúng.
     */
    public function test_route_admin_user_guide_resolves(): void
    {
        $url = route('admin.user-guide.index');
        $this->assertStringContainsString('admin/user-guide', $url);
    }
}
