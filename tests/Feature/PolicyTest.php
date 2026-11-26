<?php

namespace Tests\Feature;

use Database\Seeders\ThanhKhangPolicySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ThanhKhangPolicySeeder::class);
    }

    public function test_policies_index_vietnamese()
    {
        $response = $this->get('/chinh-sach');
        $response->assertStatus(200);
    }

    public function test_policies_index_english()
    {
        $response = $this->get('/en/policies');
        $response->assertStatus(200);
    }

    public function test_policy_show_vietnamese_slugs()
    {
        $slugs = [
            'chinh-sach-ban-hang',
            'chinh-sach-bao-hanh',
            'chinh-sach-bao-mat',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get('/chinh-sach/' . $slug);
            $response->assertStatus(200);
        }
    }

    public function test_policy_show_english_slugs()
    {
        $slugs = [
            'sales-policy',
            'warranty-policy',
            'privacy-policy',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get('/en/policies/' . $slug);
            $response->assertStatus(200);
        }
    }

    public function test_policy_show_non_existent_slug_returns_404()
    {
        $this->get('/chinh-sach/slug-khong-ton-tai')->assertStatus(404);
        $this->get('/en/policies/non-existent-slug')->assertStatus(404);
    }

    public function test_policy_show_json_data()
    {
        $response = $this->getJson('/chinh-sach/chinh-sach-bao-mat');
        $response->assertStatus(200);
        $response->assertJsonPath('content.slug', 'chinh-sach-bao-mat');

        $responseEn = $this->getJson('/en/policies/privacy-policy');
        $responseEn->assertStatus(200);
        $responseEn->assertJsonPath('content.slug', 'privacy-policy');
    }
}
