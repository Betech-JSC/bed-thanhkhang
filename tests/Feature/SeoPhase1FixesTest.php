<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;

class SeoPhase1FixesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Kiểm tra robots.txt chứa sitemap của hhmaker.vn và không chứa funkystyle.vn.
     */
    public function test_robots_txt_contains_hhmaker_sitemap_and_no_funkystyle()
    {
        $robotsPath = public_path('robots.txt');
        $this->assertFileExists($robotsPath);

        $content = file_get_contents($robotsPath);
        $this->assertStringContainsString('https://hhmaker.vn/sitemap.xml', $content);
        $this->assertStringNotContainsString('funkystyle.vn', $content);
    }

    /**
     * 2. Kiểm tra các URL triplicate (/about, /about-us, /ve-chung-toi) chuyển hướng 301 về /gioi-thieu.
     */
    public function test_triplicate_about_urls_redirect_301_to_canonical()
    {
        $this->get('/about')->assertStatus(301)->assertRedirect('/gioi-thieu');
        $this->get('/about-us')->assertStatus(301)->assertRedirect('/gioi-thieu');
        $this->get('/ve-chung-toi')->assertStatus(301)->assertRedirect('/gioi-thieu');
        $this->get('/en/about')->assertStatus(301)->assertRedirect('/en/about-us');
        $this->get('/en/ve-chung-toi')->assertStatus(301)->assertRedirect('/en/about-us');
    }

    /**
     * 3. Kiểm tra route Tin tức (/tin-tuc và /en/posts) trả về 200 OK, không còn lỗi 500.
     */
    public function test_news_posts_route_returns_200_without_500_error()
    {
        $response = $this->get('/tin-tuc');
        $this->assertNotEquals(500, $response->getStatusCode());

        $responseEn = $this->get('/en/posts');
        $this->assertNotEquals(500, $responseEn->getStatusCode());

        // Kiểm tra request dạng JSON
        $jsonResponse = $this->getJson('/tin-tuc');
        $this->assertNotEquals(500, $jsonResponse->getStatusCode());
        $jsonResponse->assertJsonStructure(['posts', 'courses', 'seo']);
    }

    /**
     * 4. Kiểm tra middleware chuyển hướng 301 www -> non-www.
     */
    public function test_www_to_non_www_redirects_301()
    {
        $response = $this->withHeaders([
            'Host' => 'www.hhmaker.vn',
        ])->get('http://www.hhmaker.vn/gioi-thieu');

        $response->assertStatus(301);
        $this->assertEquals('https://hhmaker.vn/gioi-thieu', $response->headers->get('Location'));
    }

    /**
     * 5. Kiểm tra XML Sitemap chứa trang chủ và loại trừ các route private/redirect.
     */
    public function test_xml_sitemap_contains_homepage_priority_and_excludes_private_routes()
    {
        $response = $this->get('/sitemap.xml');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Trang chủ có trong sitemap với priority 1 (hoặc 1.0)
        $this->assertStringContainsString('<loc>' . url('/') . '</loc>', $content);
        $this->assertStringContainsString('<priority>1', $content);

        // Các route nhạy cảm/redirect không được xuất hiện
        $this->assertStringNotContainsString('<loc>' . url('/dang-nhap') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>' . url('/dang-ky') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>' . url('/dashboard') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>' . url('/about') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>' . url('/ve-chung-toi') . '</loc>', $content);
        $this->assertStringNotContainsString('<loc>' . url('/robots.txt') . '</loc>', $content);
    }

    /**
     * 6. Kiểm tra trang 404 có hotline HH Maker 0906 636 425 và noindex.
     */
    public function test_error_page_has_noindex_and_correct_hotline()
    {
        $errorVue = file_get_contents(resource_path('Frontend/js/Pages/Error.vue'));
        $this->assertStringContainsString('0906 636 425', $errorVue);
        $this->assertStringContainsString('noindex, nofollow', $errorVue);
        $this->assertStringNotContainsString('+84 283 8722 846', $errorVue);
    }

    /**
     * 7. Kiểm tra branding HH Maker trong OpenGraph & Favicon.
     */
    public function test_home_page_og_image_and_favicon_have_hh_maker_branding()
    {
        $appBlade = file_get_contents(resource_path('Frontend/views/app.blade.php'));

        $this->assertStringContainsString('/og-image.jpg', $appBlade);
        $this->assertStringContainsString('hh-maker-icon.svg', $appBlade);
        $this->assertStringContainsString('og:image', $appBlade);
        $this->assertStringContainsString('twitter:image', $appBlade);
        $this->assertStringContainsString('summary_large_image', $appBlade);
    }
}
