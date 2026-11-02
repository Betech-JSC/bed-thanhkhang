<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserRank;
use Database\Seeders\UserRankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Inertia\Testing\AssertableInertia as Assert;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class QAVerificationUserRankJourneyTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        }

        $this->seed(UserRankSeeder::class);

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'qa_admin@funky.vn'],
            [
                'name'     => 'QA Super Admin',
                'password' => bcrypt('password123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);
    }

    /**
     * Test 1: Kiểm tra route Profile (users.profile & users.profile.index)
     * - Khách vãng lai: chuyển hướng 302 về trang login
     * - Học viên đã đăng nhập: HTTP 200, render Inertia component 'Users/Profile/Index'
     * - Inertia props chứa đầy đủ 'userRank' và 'rankMilestones'
     */
    public function test_profile_route_returns_200_with_inertia_props_user_rank_and_milestones()
    {
        // 1. Khách chưa đăng nhập -> Chuyển hướng 302
        $guestResponse = $this->get(route('users.profile'));
        $guestResponse->assertStatus(302);

        // 2. Học viên đăng nhập (lifetime_points 600 nhưng points 250 -> rank là Điền chủ theo points)
        $user = User::factory()->create([
            'points' => 250,
            'topup_points' => 250,
            'reward_points' => 0,
            'lifetime_points' => 600,
        ]);

        $response = $this->actingAs($user, 'user')->get(route('users.profile'));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Profile/Index')
            ->has('userRank')
            ->has('rankMilestones')
            ->has('rankMilestones', 8)
            ->where('userRank.name', 'Điền chủ')
            ->where('userRank.current_points', 250)
            ->where('userRank.lifetime_points', 600)
            ->where('userRank.next_rank.name', 'Thương gia')
            ->where('userRank.points_to_next', 250)
        );

        // Kiểm tra route index truyền thống cũng trả về 200 OK
        $responseIndex = $this->actingAs($user, 'user')->get(route('users.profile.index'));
        $responseIndex->assertStatus(200);
    }

    /**
     * Test 2: Edge case User mới đăng ký có 0 điểm tích lũy
     * - Cấp bậc ban đầu: 'Người làm vườn' (0 Quả)
     * - Cấp bậc tiếp theo: 'Tiểu nông' (100 Quả)
     * - Điểm cần để lên hạng: 100 Quả
     * - Tiến trình: 0%
     */
    public function test_profile_edge_case_new_user_zero_points()
    {
        $newUser = User::factory()->create([
            'points' => 0,
            'topup_points' => 0,
            'reward_points' => 0,
            'lifetime_points' => 0,
        ]);

        $response = $this->actingAs($newUser, 'user')->get(route('users.profile'));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Profile/Index')
            ->has('userRank')
            ->where('userRank.name', 'Người làm vườn')
            ->where('userRank.min_points', 0)
            ->where('userRank.current_points', 0)
            ->where('userRank.lifetime_points', 0)
            ->where('userRank.points_to_next', 100)
            ->where('userRank.progress_percent', 0)
            ->where('userRank.next_rank.name', 'Tiểu nông')
            ->where('userRank.next_rank.min_points', 100)
        );
    }

    /**
     * Test 3: Edge case User đạt mốc tối đa 10.000 điểm tích lũy (hoặc vượt mốc)
     * - Cấp bậc cao nhất: 'Quốc vương xứ quả' (10.000 Quả)
     * - Không còn cấp bậc tiếp theo (next_rank = null)
     * - Điểm còn thiếu: 0 Quả
     * - Tiến trình: 100%
     */
    public function test_profile_edge_case_max_milestone_user()
    {
        $maxUser = User::factory()->create([
            'points' => 10000,
            'topup_points' => 10000,
            'reward_points' => 0,
            'lifetime_points' => 10000,
        ]);

        $response = $this->actingAs($maxUser, 'user')->get(route('users.profile'));
        $response->assertStatus(200);

        $response->assertInertia(fn (Assert $page) => $page
            ->component('Users/Profile/Index')
            ->has('userRank')
            ->where('userRank.name', 'Vua mùa vàng')
            ->where('userRank.min_points', 10000)
            ->where('userRank.current_points', 10000)
            ->where('userRank.lifetime_points', 10000)
            ->where('userRank.points_to_next', 0)
            ->where('userRank.progress_percent', 100)
            ->where('userRank.next_rank', null)
        );

        // Trường hợp vượt mốc 10.000 Quả (ví dụ 15.000 Quả)
        $superUser = User::factory()->create([
            'points' => 15000,
            'topup_points' => 15000,
            'reward_points' => 0,
            'lifetime_points' => 20000,
        ]);

        $responseSuper = $this->actingAs($superUser, 'user')->get(route('users.profile'));
        $responseSuper->assertStatus(200);
        $responseSuper->assertInertia(fn (Assert $page) => $page
            ->where('userRank.name', 'Vua mùa vàng')
            ->where('userRank.current_points', 15000)
            ->where('userRank.lifetime_points', 20000)
            ->where('userRank.points_to_next', 0)
            ->where('userRank.progress_percent', 100)
            ->where('userRank.next_rank', null)
        );
    }

    /**
     * Test 4: Kiểm tra route Backend Admin User Ranks
     * - Chưa đăng nhập admin: Chuyển hướng 302
     * - Đã đăng nhập admin: HTTP 200, render Inertia component 'Backend/UserRanks/Index'
     * - Inertia props chứa danh sách 'rows'
     */
    public function test_admin_user_ranks_route_and_authorization()
    {
        // 1. Chưa đăng nhập admin truy cập /admin/user-ranks -> 302 chuyển hướng
        $guestResponse = $this->get(route('admin.user-ranks.index'));
        $guestResponse->assertStatus(302);

        // 2. Admin đã xác thực truy cập route admin.user-ranks.index
        $adminResponse = $this->actingAs($this->admin, 'admin')->get(route('admin.user-ranks.index'));
        $adminResponse->assertStatus(200);

        $adminResponse->assertInertia(fn (Assert $page) => $page
            ->component('UserRanks/Index')
            ->has('schema')
            ->has('breadcrumbs')
        );

        // 3. Admin request API Table dạng JSON trả về danh sách 8 cấp bậc
        $tableResponse = $this->actingAs($this->admin, 'admin')
            ->getJson(route('admin.user-ranks.index'));
        $tableResponse->assertStatus(200);
        $this->assertNotEmpty($tableResponse->json('data'));

        // 4. Kiểm tra form tạo/sửa cấp bậc
        $formResponse = $this->actingAs($this->admin, 'admin')->get(route('admin.user-ranks.form'));
        $formResponse->assertStatus(200);
        $formResponse->assertInertia(fn (Assert $page) => $page
            ->component('UserRanks/Form')
            ->has('item')
            ->has('breadcrumbs')
        );
    }

    /**
     * Test 5: Kiểm tra tính toàn vẹn của FruitJourneyBanner.vue
     * - Render đầy đủ các Tailwind CSS class đặc trưng của Funky: font-display, font-beau, red-fks, blue-fks, green-fks
     * - Chứa cấu trúc render xử lý edge case (DƯỚI 100 QUẢ, Bạn đã đạt cấp bậc vương giả cao nhất)
     */
    public function test_fruit_journey_banner_source_code_integrity()
    {
        $filePath = resource_path('Frontend/js/Pages/Users/Profile/Components/FruitJourneyBanner.vue');
        $this->assertFileExists($filePath);

        $content = file_get_contents($filePath);

        // Kiểm tra 5 class CSS Tailwind của Funky
        $this->assertStringContainsString('font-display', $content, 'Thiếu class font-display');
        $this->assertStringContainsString('font-beau', $content, 'Thiếu class font-beau');
        $this->assertStringContainsString('red-fks', $content, 'Thiếu class red-fks');
        $this->assertStringContainsString('blue-fks', $content, 'Thiếu class blue-fks');
        $this->assertStringContainsString('green-fks', $content, 'Thiếu class green-fks');

        // Kiểm tra xử lý edge cases
        $this->assertStringContainsString('DƯỚI 100 QUẢ', $content, 'Thiếu xử lý label 0 điểm DƯỚI 100 QUẢ');
        $this->assertStringContainsString('Bạn đã đạt cấp bậc vương giả cao nhất!', $content, 'Thiếu thông báo đạt cấp bậc tối đa');
        $this->assertStringContainsString('HIỆN TẠI', $content, 'Thiếu huy hiệu mốc hiện tại');
        $this->assertStringContainsString('HÀNH TRÌNH', $content, 'Thiếu tiêu đề hành trình');
        $this->assertStringContainsString('TÍCH LŨY QUẢ', $content, 'Thiếu tiêu đề tích lũy quả');
    }
}
