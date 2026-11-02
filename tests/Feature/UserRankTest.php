<?php

namespace Tests\Feature;

use App\Models\Post\Post;
use App\Models\User;
use App\Models\UserRank;
use App\Services\PointService;
use App\Services\SePayService;
use Database\Seeders\UserRankSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
use JamstackVietnam\Core\Models\Admin;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class UserRankTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;
    protected PointService $pointService;

    protected function setUp(): void
    {
        parent::setUp();

        if (config('database.default') === 'sqlite') {
            $this->artisan('migrate', ['--path' => 'database/migrations/blog']);
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $this->admin = Admin::firstOrCreate(
            ['email' => 'admin_test_rank@funky.vn'],
            [
                'name'     => 'Rank Admin',
                'password' => bcrypt('password123'),
                'status'   => Admin::STATUS_ACTIVE,
            ]
        );
        $this->admin->assignRole($role);

        $this->pointService = app(PointService::class);
    }

    public function test_user_rank_seeder_and_model_scopes()
    {
        $this->seed(UserRankSeeder::class);

        $this->assertDatabaseCount('user_ranks', 8);

        $ranks = UserRank::active()->ordered()->get();
        $this->assertCount(8, $ranks);
        $this->assertEquals('Người làm vườn', $ranks->first()->name);
        $this->assertEquals(0, $ranks->first()->min_points);
        $this->assertEquals('/assets/images/ranks/01_Nguoi_lam_vuon.png', $ranks->first()->avatar_icon_url);

        $lastRank = $ranks->last();
        $this->assertEquals('Vua mùa vàng', $lastRank->name);
        $this->assertEquals(10000, $lastRank->min_points);
        $this->assertEquals('/assets/images/ranks/08_Vua_mua_vang.png', $lastRank->avatar_icon_url);
    }

    public function test_user_rank_resolution_and_progression()
    {
        $this->seed(UserRankSeeder::class);

        // 1. User mới có 0 điểm -> Người làm vườn
        $user = User::factory()->create([
            'points' => 0,
            'topup_points' => 0,
            'reward_points' => 0,
            'lifetime_points' => 0,
        ]);

        $rankInfo = $user->rank;
        $this->assertNotNull($rankInfo);
        $this->assertEquals('Người làm vườn', $rankInfo['name']);
        $this->assertEquals('Tiểu nông', $rankInfo['next_rank']['name']);
        $this->assertEquals(100, $rankInfo['points_to_next']);
        $this->assertEquals(0, $rankInfo['progress_percent']);

        // 2. User có 150 points -> Tiểu nông, tiến trình 50% lên Điền chủ (100 -> 200)
        $user->update([
            'points' => 150,
            'topup_points' => 150,
            'lifetime_points' => 150,
        ]);
        $rankInfo = $user->fresh()->rank;
        $this->assertEquals('Tiểu nông', $rankInfo['name']);
        $this->assertEquals('Điền chủ', $rankInfo['next_rank']['name']);
        $this->assertEquals(50, $rankInfo['points_to_next']);
        $this->assertEquals(50, $rankInfo['progress_percent']);

        // 3. User đạt max rank (10,000 points) -> Vua mùa vàng
        $user->update([
            'points' => 12000,
            'topup_points' => 12000,
            'lifetime_points' => 12000,
        ]);
        $rankInfo = $user->fresh()->rank;
        $this->assertEquals('Vua mùa vàng', $rankInfo['name']);
        $this->assertNull($rankInfo['next_rank']);
        $this->assertEquals(0, $rankInfo['points_to_next']);
        $this->assertEquals(100, $rankInfo['progress_percent']);
    }

    public function test_user_rank_progression_attributes()
    {
        $this->test_user_rank_resolution_and_progression();
    }

    public function test_spend_points_preserves_lifetime_points_but_demotes_rank()
    {
        $this->seed(UserRankSeeder::class);

        $user = User::factory()->create([
            'points' => 500,
            'topup_points' => 500,
            'reward_points' => 0,
            'lifetime_points' => 500,
        ]);

        // Rank trước khi tiêu là Thương gia (500 Quả)
        $this->assertEquals('Thương gia', $user->rank['name']);

        $resource = Post::create([
            'title' => 'Tài nguyên mẫu VIP',
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        // Trừ 300 Quả khi tải tài nguyên
        $this->pointService->spendPoints($user, 300, $resource);

        $user->refresh();
        $this->assertEquals(200, $user->points);
        // Lifetime points phải giữ nguyên 500 (bảo lưu tích lũy trọn đời)
        $this->assertEquals(500, $user->lifetime_points);
        // Xác minh rank tự động lùi về 'Điền chủ' - mốc 200 Quả!
        $this->assertEquals('Điền chủ', $user->rank['name']);

        // Kiểm tra thêm: Khi user được cộng lại 300 Quả (ví dụ qua manualAdjust)
        $this->pointService->manualAdjust($user, 300, 'Nạp lại điểm');
        $this->assertEquals(500, $user->fresh()->points);
        // Tự động thăng hạng lại Thương gia!
        $this->assertEquals('Thương gia', $user->fresh()->rank['name']);
    }

    public function test_spend_points_preserves_lifetime_points()
    {
        $this->test_spend_points_preserves_lifetime_points_but_demotes_rank();
    }

    public function test_positive_point_operations_increment_lifetime_points()
    {
        $user = User::factory()->create([
            'points' => 10,
            'topup_points' => 10,
            'reward_points' => 0,
            'lifetime_points' => 10,
            'last_login_at' => null,
        ]);

        // 1. Manual Adjust (cộng điểm)
        $this->pointService->manualAdjust($user, 50, 'Thưởng thi đua', $this->admin);
        $user->refresh();
        $this->assertEquals(60, $user->points);
        $this->assertEquals(60, $user->lifetime_points);

        // 2. Daily Login Bonus
        $result = $this->pointService->grantDailyLoginBonus($user);
        $user->refresh();
        $this->assertTrue($result['granted']);
        $this->assertEquals(70, $user->points);
        $this->assertEquals(70, $user->lifetime_points);
    }

    public function test_admin_user_rank_crud_endpoints()
    {
        $this->actingAs($this->admin, 'admin');

        // 1. Index
        $response = $this->get(route('admin.user-ranks.index'));
        $response->assertStatus(200);

        // 2. Form Create
        $response = $this->get(route('admin.user-ranks.form'));
        $response->assertStatus(200);

        // 3. Store (Create)
        $response = $this->post(route('admin.user-ranks.store'), [
            'name' => 'Chiến thần xứ Quả',
            'min_points' => 20000,
            'description' => 'Cấp bậc huyền thoại vô song',
            'avatar_icon' => [
                'path' => 'assets/images/ranks/08_Nha_vua.png',
                'url' => '/assets/images/ranks/08_Nha_vua.png',
            ],
            'position' => 9,
            'is_active' => true,
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_ranks', [
            'name' => 'Chiến thần xứ Quả',
            'min_points' => 20000,
        ]);

        $createdRank = UserRank::where('name', 'Chiến thần xứ Quả')->first();

        // 4. Store (Update)
        $response = $this->post(route('admin.user-ranks.store', ['id' => $createdRank->id]), [
            'name' => 'Chiến thần xứ Quả VIP',
            'min_points' => 25000,
            'description' => 'Cập nhật mô tả',
            'position' => 9,
            'is_active' => true,
        ]);
        $response->assertSessionHasNoErrors();

        $this->assertDatabaseHas('user_ranks', [
            'id' => $createdRank->id,
            'name' => 'Chiến thần xứ Quả VIP',
            'min_points' => 25000,
        ]);

        // 5. Destroy
        $response = $this->post(route('admin.user-ranks.destroy', ['id' => $createdRank->id]));
        $this->assertDatabaseMissing('user_ranks', [
            'id' => $createdRank->id,
        ]);
    }
}
