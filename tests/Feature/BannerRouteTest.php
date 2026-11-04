<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BannerRouteTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->artisan('migrate', [
            '--path' => 'packages/bed-package-essentials/laravel-settings/src/migrations',
            '--realpath' => false,
        ]);
    }

    /**
     * Test config slider positions normalization.
     */
    public function test_slider_positions_are_properly_configured()
    {
        $positions = config('slider.positions');

        $this->assertArrayHasKey('HOME_SLIDER', $positions);
        $this->assertArrayHasKey('BANNER_ABOUT_HERO', $positions);
        $this->assertArrayHasKey('BANNER_PROCESS_HERO', $positions);
        $this->assertArrayHasKey('BANNER_GLOBAL_CTA', $positions);
        $this->assertArrayHasKey('BANNER_HOME_FOUNDER', $positions);

        $this->assertArrayNotHasKey('BANNER_COURSE', $positions);
        $this->assertArrayNotHasKey('BANNER_RESOURCE', $positions);
        $this->assertArrayNotHasKey('BANNER_PRIZE', $positions);

        $this->assertFalse($positions['HOME_SLIDER']['banner']);
        $this->assertTrue($positions['BANNER_ABOUT_HERO']['banner']);
        $this->assertTrue($positions['BANNER_PROCESS_HERO']['banner']);
        $this->assertTrue($positions['BANNER_GLOBAL_CTA']['banner']);
        $this->assertTrue($positions['BANNER_HOME_FOUNDER']['banner']);
    }

    /**
     * Test About page API returns 200 and loads heroBanner.
     */
    public function test_about_page_api_returns_hero_banner()
    {
        $response = $this->getJson('/gioi-thieu');
        $response->assertStatus(200);
        $response->assertJsonStructure(['posts', 'heroBanner']);
    }

    /**
     * Test Process page API returns 200 and loads heroBanner.
     */
    public function test_process_page_api_returns_hero_banner()
    {
        $response = $this->getJson('/quy-trinh');
        $response->assertStatus(200);
        $response->assertJsonStructure(['heroBanner']);
    }

    /**
     * Test About page Inertia request returns About/Index component and shared globalCtaBanner.
     */
    public function test_about_page_inertia_request_loads_props()
    {
        $response = $this->get('/gioi-thieu', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('About/Index', $data['component']);
        $this->assertArrayHasKey('heroBanner', $data['props']);
        $this->assertArrayHasKey('globalCtaBanner', $data['props']);
    }

    /**
     * Test Process page Inertia request returns Process/Index component and shared globalCtaBanner.
     */
    public function test_process_page_inertia_request_loads_props()
    {
        $response = $this->get('/quy-trinh', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('Process/Index', $data['component']);
        $this->assertArrayHasKey('heroBanner', $data['props']);
        $this->assertArrayHasKey('globalCtaBanner', $data['props']);
    }
}
