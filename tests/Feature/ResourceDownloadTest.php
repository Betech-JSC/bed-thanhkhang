<?php

namespace Tests\Feature;

use App\Models\Post\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResourceDownloadTest extends TestCase
{
    use RefreshDatabase;

    public function test_download_free_resource_succeeds_without_points()
    {
        $user = User::factory()->create([
            'points' => 0,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
            'points_cost' => 0,
            'link' => 'https://example.com/test-free.pdf',
        ]);

        $response = $this->actingAs($user, 'user')
            ->post(route('users.resources.download-link', ['id' => $resource->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['link', 'message']);
        $user->refresh();
        $this->assertEquals(0, $user->points);
    }

    public function test_download_paid_resource_fails_if_insufficient_points()
    {
        $user = User::factory()->create([
            'points' => 5,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
            'points_cost' => 10,
            'link' => 'https://example.com/test-paid.pdf',
        ]);

        $response = $this->actingAs($user, 'user')
            ->post(route('users.resources.download-link', ['id' => $resource->id]));

        $response->assertStatus(422);
        $response->assertJsonFragment(['message' => 'Không đủ Quả. Cần 10 Quả, hiện có 5 Quả.']);
        $user->refresh();
        $this->assertEquals(5, $user->points);
    }

    public function test_download_paid_resource_succeeds_if_sufficient_points()
    {
        $user = User::factory()->create([
            'points' => 15,
            'last_login_at' => now(),
        ]);
        $resource = Post::factory()->create([
            'type' => Post::TYPE_RESOURCE,
            'status' => Post::STATUS_ACTIVE,
            'points_cost' => 10,
            'link' => 'https://example.com/test-paid.pdf',
        ]);

        $response = $this->actingAs($user, 'user')
            ->post(route('users.resources.download-link', ['id' => $resource->id]));

        $response->assertStatus(200);
        $response->assertJsonStructure(['link', 'message']);
        $user->refresh();
        $this->assertEquals(5, $user->points);
    }

    public function test_upload_avatar_succeeds()
    {
        $user = User::factory()->create([
            'last_login_at' => now(),
        ]);
        
        \Illuminate\Support\Facades\Storage::fake('uploads');
        $file = \Illuminate\Http\UploadedFile::fake()->image('avatar.jpg');

        $response = $this->actingAs($user, 'user')
            ->post(route('users.profile.upload-avatar'), [
                'avatar' => $file
            ]);

        $response->dump();
        $response->assertStatus(200);
    }
}
