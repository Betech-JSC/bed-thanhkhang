<?php

namespace Tests\Feature;

use App\Models\PointTransaction;
use App\Models\Post\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShareFacebookRewardTest extends TestCase
{
    use RefreshDatabase;

    public function test_share_reward_fails_without_fb_post_url()
    {
        $user = User::factory()->create([
            'points' => 0,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => '',
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Vui lòng nhập/dán liên kết bài đăng Facebook của bạn để nhận thưởng.']);
        $this->assertEquals(0, $user->fresh()->points);
    }

    public function test_share_reward_fails_with_invalid_url_domain()
    {
        $user = User::factory()->create([
            'points' => 0,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $response = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => 'https://google.com/search?q=test',
            ]);

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Liên kết bài đăng Facebook không hợp lệ. Vui lòng kiểm tra lại.']);
        $this->assertEquals(0, $user->fresh()->points);
    }

    public function test_share_reward_succeeds_with_valid_fb_url()
    {
        $user = User::factory()->create([
            'points' => 0,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $validUrl = 'https://www.facebook.com/username/posts/pfbid0239482903';

        $response = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $validUrl,
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true]);

        $user->refresh();
        $this->assertEquals(10, $user->points);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'type' => PointTransaction::TYPE_SHARE_FACEBOOK,
            'amount' => 10,
        ]);
    }

    public function test_cannot_reuse_same_fb_post_url()
    {
        $user1 = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);
        $user2 = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $reusedUrl = 'https://www.facebook.com/permalink.php?story_fbid=123456789';

        // Claim 1
        $this->actingAs($user1, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $reusedUrl,
            ])
            ->assertStatus(200);

        // Claim 2 with same URL by another user
        $response2 = $this->actingAs($user2, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $reusedUrl,
            ]);

        $response2->assertStatus(200);
        $response2->assertJsonFragment([
            'success' => false,
            'message' => 'Liên kết bài đăng Facebook này đã được sử dụng để nhận thưởng trước đây. Vui lòng đăng bài mới và thử lại!',
        ]);

        $this->assertEquals(0, $user2->fresh()->points);
    }

    public function test_user_can_only_claim_once_per_calendar_day()
    {
        $user = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        // First claim today
        $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => 'https://facebook.com/post/111111',
            ])
            ->assertStatus(200);

        $this->assertEquals(10, $user->fresh()->points);

        // Second claim today with different URL
        $response2 = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => 'https://facebook.com/post/222222',
            ]);

        $response2->assertStatus(200);
        $response2->assertJsonFragment([
            'success' => false,
            'claimed_today' => true,
        ]);

        $this->assertEquals(10, $user->fresh()->points);
    }

    public function test_share_reward_points_uses_cms_config()
    {
        $user = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        // Set CMS config value to 25 points
        settings()->group('support_info')->set(['share_facebook_points' => 25]);

        $response = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => 'https://facebook.com/post/cms_test_999',
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['success' => true, 'points_added' => 25]);

        $this->assertEquals(25, $user->fresh()->points);
    }

    public function test_tracking_parameter_normalization_prevents_duplicate_claims()
    {
        $user1 = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);
        $user2 = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $urlWithTracking1 = 'https://www.facebook.com/username/posts/pfbid99999?fbclid=IwAR1234567890abc&utm_source=facebook';
        $urlWithTracking2 = 'https://m.facebook.com/username/posts/pfbid99999?fbclid=DifferentTrackingToken999&utm_medium=cpc';

        // Claim 1 with tracking URL 1
        $this->actingAs($user1, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $urlWithTracking1,
            ])
            ->assertStatus(200);

        $this->assertEquals(10, $user1->fresh()->points);

        // Claim 2 with same post but different mobile domain and tracking token
        $response2 = $this->actingAs($user2, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $urlWithTracking2,
            ]);

        $response2->assertStatus(200);
        $response2->assertJsonFragment([
            'success' => false,
            'message' => 'Liên kết bài đăng Facebook này đã được sử dụng để nhận thưởng trước đây. Vui lòng đăng bài mới và thử lại!',
        ]);

        $this->assertEquals(0, $user2->fresh()->points);
    }

    public function test_generic_facebook_profile_or_home_url_rejected()
    {
        $user = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        $genericProfileUrl = 'https://www.facebook.com/someuser.profile';

        $response = $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => $genericProfileUrl,
            ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'success' => false,
            'message' => 'Liên kết phải là đường dẫn tới bài viết/ảnh/video cụ thể trên Facebook (không dán link trang cá nhân hoặc trang chủ).',
        ]);

        $this->assertEquals(0, $user->fresh()->points);
    }

    public function test_admin_can_revoke_invalid_share_points()
    {
        $admin = \JamstackVietnam\Core\Models\Admin::create([
            'name' => 'Admin Test',
            'email' => 'admin_test_' . uniqid() . '@example.com',
            'password' => bcrypt('password123'),
        ]);
        $user = User::factory()->create(['points' => 0, 'last_login_at' => now()->toDateString()]);

        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
        ]);

        // User claims 10 points
        $this->actingAs($user, 'user')
            ->postJson(route('users.resources.share-facebook', ['id' => $resource->id]), [
                'fb_post_url' => 'https://www.facebook.com/user/posts/revoke_test_100',
            ])
            ->assertStatus(200);

        $this->assertEquals(10, $user->fresh()->points);

        $transaction = PointTransaction::where('user_id', $user->id)
            ->where('type', PointTransaction::TYPE_SHARE_FACEBOOK)
            ->first();

        $this->assertNotNull($transaction);

        // Admin revokes points
        $response = $this->actingAs($admin, 'admin')
            ->postJson(route('admin.point-transactions.revoke-share', ['id' => $transaction->id]));

        $response->assertStatus(200);
        $this->assertEquals(0, $user->fresh()->points);

        $this->assertDatabaseHas('point_transactions', [
            'user_id' => $user->id,
            'type' => PointTransaction::TYPE_MANUAL_SUBTRACT,
            'amount' => -10,
        ]);
    }
}
