<?php

namespace Tests\Feature;

use App\Models\PointPackage;
use App\Models\PointTransaction;
use App\Models\Post\Post;
use App\Models\School;
use App\Models\User;
use App\Services\PointService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use JamstackVietnam\Core\Models\Admin;
use Tests\TestCase;

class PointServiceAndNoticeTest extends TestCase
{
    use RefreshDatabase;

    protected PointService $pointService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pointService = app(PointService::class);
    }

    public function test_complete_topup_stores_balance_snapshots_in_meta()
    {
        $user = User::factory()->create([
            'points'        => 50,
            'reward_points' => 20,
            'topup_points'  => 30,
        ]);

        $package = PointPackage::create([
            'name'         => 'Gói 100 Quả',
            'points'       => 100,
            'bonus_points' => 0,
            'price'        => 100000,
            'is_active'    => true,
        ]);

        $transaction = PointTransaction::create([
            'user_id'          => $user->id,
            'type'             => PointTransaction::TYPE_TOPUP,
            'amount'           => 100,
            'balance_before'   => 50,
            'balance_after'    => 150,
            'point_package_id' => $package->id,
            'reference_code'   => 'FKTEST01',
            'status'           => PointTransaction::STATUS_PENDING,
        ]);

        $this->pointService->completeTopup($transaction, [
            'transaction_date' => now()->toIso8601String(),
            'amount_in'        => 100000,
        ], 'SEPAY_01');

        $user->refresh();
        $transaction->refresh();

        $this->assertEquals(150, $user->points);
        $this->assertEquals(20, $user->reward_points);
        $this->assertEquals(130, $user->topup_points);

        $this->assertEquals(20, $transaction->meta['reward_balance_after']);
        $this->assertEquals(130, $transaction->meta['topup_balance_after']);
    }

    public function test_spend_points_prioritizes_reward_points_and_stores_meta()
    {
        $user = User::factory()->create([
            'points'        => 100,
            'reward_points' => 30,
            'topup_points'  => 70,
        ]);

        $resource = Post::factory()->create([
            'type'        => Post::TYPE_RESOURCE,
            'status'      => Post::STATUS_ACTIVE,
            'points_cost' => 50,
        ]);

        $tx = $this->pointService->spendPoints($user, 50, $resource);

        $user->refresh();
        $this->assertEquals(50, $user->points);
        $this->assertEquals(0, $user->reward_points);
        $this->assertEquals(50, $user->topup_points);

        $this->assertEquals(30, $tx->meta['reward_deducted']);
        $this->assertEquals(20, $tx->meta['topup_deducted']);
        $this->assertEquals(0, $tx->meta['reward_balance_after']);
        $this->assertEquals(50, $tx->meta['topup_balance_after']);
    }

    public function test_manual_adjust_with_custom_reason_and_balance_meta()
    {
        $user = User::factory()->create([
            'points'        => 40,
            'reward_points' => 10,
            'topup_points'  => 30,
        ]);

        $admin = Admin::create([
            'name'     => 'Admin Funky',
            'email'    => 'admin_test_' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $customReason = 'Tặng Quả minigame sự kiện 2/9';
        $tx = $this->pointService->manualAdjust($user, 25, $customReason, $admin);

        $user->refresh();
        $this->assertEquals(65, $user->points);
        $this->assertEquals(10, $user->reward_points);
        $this->assertEquals(55, $user->topup_points);

        $this->assertEquals($customReason, $tx->description);
        $this->assertEquals(10, $tx->meta['reward_balance_after']);
        $this->assertEquals(55, $tx->meta['topup_balance_after']);
    }

    public function test_check_and_grant_profile_completion_bonus()
    {
        $school = School::create([
            'name'   => 'Đại học Bách Khoa',
            'status' => School::STATUS_ACTIVE,
        ]);

        $user = User::factory()->create([
            'name'           => 'Nguyen Van A',
            'email'          => 'nguyenvana@example.com',
            'phone'          => '0987654321',
            'zalo'           => 'https://facebook.com/nguyenvana',
            'school_id'      => $school->id,
            'major'          => 'Công nghệ thông tin',
            'current_status' => 'Sinh viên',
            'school_year'    => '2023',
            'points'         => 0,
            'reward_points'  => 0,
            'topup_points'   => 0,
        ]);

        $granted = $this->pointService->checkAndGrantProfileCompletionBonus($user);
        $this->assertTrue($granted);

        $user->refresh();
        $this->assertEquals(10, $user->reward_points);
        $this->assertEquals(10, $user->points);

        // Chạy lại lần 2 không được cộng thêm
        $grantedSecondTime = $this->pointService->checkAndGrantProfileCompletionBonus($user);
        $this->assertFalse($grantedSecondTime);
        $this->assertEquals(10, $user->fresh()->points);
    }

    public function test_transform_admin_returns_detailed_balances()
    {
        $user = User::factory()->create([
            'name'          => 'Le Thi B',
            'email'         => 'lethib@example.com',
            'phone'         => '0912345678',
            'zalo'          => 'https://facebook.com/lethib',
            'funky_note'    => 'Ghi chú đặc biệt cho học viên',
            'reward_points' => 45,
            'topup_points'  => 55,
            'points'        => 100,
        ]);

        $tx = PointTransaction::create([
            'user_id'        => $user->id,
            'type'           => PointTransaction::TYPE_BONUS,
            'amount'         => 20,
            'balance_before' => 80,
            'balance_after'  => 100,
            'description'    => 'Thưởng chuyên cần',
            'status'         => PointTransaction::STATUS_COMPLETED,
            'meta'           => [
                'reward_balance_after' => 45,
                'topup_balance_after'  => 55,
            ],
        ]);

        $data = $tx->transformAdmin();

        $this->assertEquals('Le Thi B', $data['user_name']);
        $this->assertEquals('Ghi chú đặc biệt cho học viên', $data['user_funky_note']);
        $this->assertEquals(45, $data['reward_balance_after']);
        $this->assertEquals(55, $data['topup_balance_after']);
        $this->assertEquals(45, $data['user_reward_points']);
        $this->assertEquals(55, $data['user_topup_points']);
    }

    public function test_admin_can_send_notice_to_student_via_api()
    {
        $admin = Admin::create([
            'name'     => 'Admin Admin',
            'email'    => 'admin_notice_' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $user = User::factory()->create([
            'name'       => 'Tran Van C',
            'funky_note' => null,
        ]);

        $noticeMessage = 'Hệ thống đã thu hồi 10 Quả thưởng do bài viết Facebook ở chế độ riêng tư.';

        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.point-transactions.send-notice'), [
                'user_id' => $user->id,
                'note'    => $noticeMessage,
            ]);

        $response->assertStatus(200);
        $this->assertEquals($noticeMessage, $user->fresh()->funky_note);

        // Test send notice via user endpoint
        $userResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.users.send-notice', ['id' => $user->id]), [
                'note' => 'Cập nhật thông báo mới',
            ]);

        $userResponse->assertStatus(200);
        $this->assertEquals('Cập nhật thông báo mới', $user->fresh()->funky_note);
    }

    public function test_point_transaction_transform_admin_has_user_id_and_reaches_frontend_profile()
    {
        $admin = Admin::create([
            'name'     => 'Super Admin',
            'email'    => 'admin2@jamstackvietnam.com',
            'password' => bcrypt('secret123'),
        ]);

        $user = User::factory()->create([
            'name'       => 'Trần Anh Duy',
            'email'      => 'trananhduy@gmail.com',
            'funky_note' => null,
        ]);

        $tx = PointTransaction::create([
            'user_id'        => $user->id,
            'type'           => PointTransaction::TYPE_BONUS,
            'amount'         => 10,
            'balance_before' => 0,
            'balance_after'  => 10,
            'status'         => PointTransaction::STATUS_COMPLETED,
            'description'    => 'Điểm thưởng đăng nhập hàng ngày',
        ]);

        // 1. Phải có user_id trong transformAdmin để FE table gọi được
        $transformed = $tx->transformAdmin();
        $this->assertArrayHasKey('user_id', $transformed);
        $this->assertEquals($user->id, $transformed['user_id']);

        // 2. Admin gửi thông báo với user_id lấy trực tiếp từ transformAdmin
        $noticeContent = 'Hệ thống đã bù Quả cho bạn do sự cố nạp điểm trước đó.';
        $sendResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.point-transactions.send-notice'), [
                'user_id' => $transformed['user_id'],
                'note'    => $noticeContent,
            ]);

        $sendResponse->assertStatus(200);
        $this->assertEquals($noticeContent, $user->fresh()->funky_note);

        // 3. Phía FE: khi học viên vào Profile, props auth.admin_note và auth.user.funky_note phải nhận đúng nội dung này
        $feResponse = $this->actingAs($user, 'user')
            ->get(route('users.profile.index'));

        $feResponse->assertStatus(200);
        $feResponse->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->where('auth.admin_note', $noticeContent)
            ->where('auth.user.funky_note', $noticeContent)
        );
    }

    public function test_filter_point_transactions_by_user_id_returns_customer_full_history()
    {
        $admin = Admin::create([
            'name'     => 'Super Admin',
            'email'    => 'admin3@jamstackvietnam.com',
            'password' => bcrypt('secret123'),
        ]);

        $userA = User::factory()->create(['name' => 'Duyen Nguyen Thanh', 'email' => 'duyen@gmail.com']);
        $userB = User::factory()->create(['name' => 'Minh Tran', 'email' => 'minh@gmail.com']);

        // Tạo 3 giao dịch cho User A
        for ($i = 0; $i < 3; $i++) {
            PointTransaction::create([
                'user_id'        => $userA->id,
                'type'           => PointTransaction::TYPE_BONUS,
                'amount'         => 10,
                'balance_before' => $i * 10,
                'balance_after'  => ($i + 1) * 10,
                'status'         => PointTransaction::STATUS_COMPLETED,
                'description'    => "GD User A {$i}",
            ]);
        }

        // Tạo 2 giao dịch cho User B
        for ($i = 0; $i < 2; $i++) {
            PointTransaction::create([
                'user_id'        => $userB->id,
                'type'           => PointTransaction::TYPE_BONUS,
                'amount'         => 10,
                'balance_before' => $i * 10,
                'balance_after'  => ($i + 1) * 10,
                'status'         => PointTransaction::STATUS_COMPLETED,
                'description'    => "GD User B {$i}",
            ]);
        }

        // Khi click vào học viên A, lọc theo user_id
        $response = $this->actingAs($admin, 'admin')
            ->get(route('admin.point-transactions.index', ['user_id' => $userA->id]));

        $response->assertStatus(200);
        $response->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('PointTransactions/Index', false)
            ->where('data.total', 3)
            ->where('filters.user_id', (string) $userA->id)
            ->where('filterUser.id', $userA->id)
            ->where('filterUser.name', 'Duyen Nguyen Thanh')
            ->has('data.data', 3)
        );
    }

    public function test_pagination_defaults_to_300_and_supports_custom_per_page()
    {
        $admin = Admin::create([
            'name'     => 'Super Admin',
            'email'    => 'admin4@jamstackvietnam.com',
            'password' => bcrypt('secret123'),
        ]);

        // Kiểm tra mặc định là 300 bản ghi/trang
        $defaultResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.point-transactions.index'));

        $defaultResponse->assertStatus(200);
        $defaultResponse->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('PointTransactions/Index', false)
            ->where('data.per_page', 300)
            ->where('filters.per_page', 300)
        );

        // Kiểm tra chọn tùy biến per_page = 50
        $customResponse = $this->actingAs($admin, 'admin')
            ->get(route('admin.point-transactions.index', ['per_page' => 50]));

        $customResponse->assertStatus(200);
        $customResponse->assertInertia(fn (\Inertia\Testing\AssertableInertia $page) => $page
            ->component('PointTransactions/Index', false)
            ->where('data.per_page', 50)
            ->where('filters.per_page', 50)
        );
    }

    public function test_send_notice_validates_required_note_and_max_length()
    {
        $admin = Admin::create([
            'name'     => 'Admin Test Edge',
            'email'    => 'admin_edge_' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);

        $user = User::factory()->create([
            'name' => 'Edge User',
        ]);

        // 1. Gửi thiếu note qua UserController
        $emptyResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.users.send-notice', ['id' => $user->id]), [
                'note' => '',
            ]);
        $emptyResponse->assertStatus(422);
        $emptyResponse->assertJsonValidationErrors(['note']);

        // 2. Gửi note vượt quá 2000 ký tự qua UserController
        $tooLongResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.users.send-notice', ['id' => $user->id]), [
                'note' => str_repeat('A', 2001),
            ]);
        $tooLongResponse->assertStatus(422);
        $tooLongResponse->assertJsonValidationErrors(['note']);

        // 3. Gửi thiếu note qua PointTransactionController
        $emptyTxResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.point-transactions.send-notice'), [
                'user_id' => $user->id,
                'note'    => '',
            ]);
        $emptyTxResponse->assertStatus(422);
        $emptyTxResponse->assertJsonValidationErrors(['note']);
    }

    public function test_send_notice_fails_for_non_existent_user()
    {
        $admin = Admin::create([
            'name'     => 'Admin Test 404',
            'email'    => 'admin_404_' . uniqid() . '@example.com',
            'password' => bcrypt('secret123'),
        ]);

        // 1. Qua UserController (route model binding / findOrFail)
        $userResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.users.send-notice', ['id' => 9999999]), [
                'note' => 'Thông báo cho user không tồn tại',
            ]);
        $userResponse->assertStatus(404);

        // 2. Qua PointTransactionController (validate exists:users,id)
        $txResponse = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.point-transactions.send-notice'), [
                'user_id' => 9999999,
                'note'    => 'Thông báo cho user không tồn tại',
            ]);
        $txResponse->assertStatus(422);
        $txResponse->assertJsonValidationErrors(['user_id']);
    }

    public function test_send_notice_fails_for_unauthenticated_request()
    {
        $user = User::factory()->create(['name' => 'Guest Target']);

        $response1 = $this->postJson(route('admin.users.send-notice', ['id' => $user->id]), [
            'note' => 'Hacker sending note',
        ]);
        $response1->assertStatus(401);

        $response2 = $this->postJson(route('admin.point-transactions.send-notice'), [
            'user_id' => $user->id,
            'note'    => 'Hacker sending note',
        ]);
        $response2->assertStatus(401);
    }
}
