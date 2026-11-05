<?php

namespace Tests\Feature;

use Database\Seeders\PolicySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PolicyTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(PolicySeeder::class);
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
            'chinh-sach-bao-mat',
            'dieu-khoan-dich-vu',
            'chinh-sach-bao-hanh-doi-tra',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get('/chinh-sach/' . $slug);
            $response->assertStatus(200);
        }
    }

    public function test_policy_show_english_slugs()
    {
        $slugs = [
            'privacy-policy',
            'terms-of-service',
            'warranty-return-policy',
        ];

        foreach ($slugs as $slug) {
            $response = $this->get('/en/policies/' . $slug);
            $response->assertStatus(200);
        }
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
