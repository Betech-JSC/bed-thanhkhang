<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Database\Seeders\FigmaProductsSeeder;

class SeoPhase2Test extends TestCase
{
    use RefreshDatabase;

    /**
     * 1. Schema LocalBusiness / Organization xuất hiện đầy đủ trong HTML response.
     */
    public function test_organization_and_local_business_schema_exists_in_html()
    {
        $response = $this->get('/gioi-thieu');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Kiểm tra khối JSON-LD LocalBusiness
        $this->assertStringContainsString('LocalBusiness', $content);
        $this->assertStringContainsString('Công ty In ấn 3D HH Maker', $content);
        $this->assertStringContainsString('0906636425', $content);
        $this->assertStringContainsString('Bình Tân', $content);
        $this->assertStringContainsString('info@hhmaker.vn', $content);
        $this->assertStringContainsString('https://hhmaker.vn', $content);
        $this->assertStringContainsString('/hhmaker-og-2026.jpg', $content);
        $this->assertStringContainsString('OpeningHoursSpecification', $content);
        $this->assertStringContainsString('$$', $content);
    }

    /**
     * 2. Schema Product và Offer xuất hiện đúng định dạng trên route sản phẩm.
     */
    public function test_product_and_offer_schema_exists_on_product_show_route()
    {
        $this->seed(FigmaProductsSeeder::class);

        $response = $this->get('/san-pham/mo-hinh-cao-trang-tri');
        $response->assertStatus(200);

        $content = $response->getContent();

        // Kiểm tra Schema Product & Offer
        $this->assertStringContainsString('"@type": "Product"', $content);
        $this->assertStringContainsString('"@type": "Offer"', $content);
        $this->assertStringContainsString('"priceCurrency": "VND"', $content);
        $this->assertStringContainsString('https://schema.org/InStock', $content);
        $this->assertStringContainsString('HH Maker', $content);
        $this->assertStringContainsString('HHM-PRD-', $content);
    }

    /**
     * 3. Schema BreadcrumbList xuất hiện hợp lệ trên các trang sản phẩm, danh mục, tin tức, giới thiệu.
     */
    public function test_breadcrumb_list_schema_exists_on_hierarchical_pages()
    {
        $this->seed(FigmaProductsSeeder::class);

        // Chi tiết sản phẩm
        $resProduct = $this->get('/san-pham/mo-hinh-cao-trang-tri');
        $resProduct->assertStatus(200);
        $contentProduct = $resProduct->getContent();
        $this->assertStringContainsString('"@type": "BreadcrumbList"', $contentProduct);
        $this->assertStringContainsString('Trang chủ', $contentProduct);
        $this->assertStringContainsString('Sản phẩm', $contentProduct);

        // Danh mục sản phẩm
        $resCatalog = $this->get('/san-pham');
        $resCatalog->assertStatus(200);
        $this->assertStringContainsString('BreadcrumbList', $resCatalog->getContent());

        // Giới thiệu
        $resAbout = $this->get('/gioi-thieu');
        $resAbout->assertStatus(200);
        $this->assertStringContainsString('BreadcrumbList', $resAbout->getContent());

        // Tin tức
        $resNews = $this->get('/tin-tuc');
        $resNews->assertStatus(200);
        $this->assertStringContainsString('BreadcrumbList', $resNews->getContent());
    }

    /**
     * 4. Thẻ Hreflang và dynamic html lang attribute xuất hiện chính xác.
     */
    public function test_dynamic_html_lang_and_hreflang_tags()
    {
        // Trang tiếng Việt
        $resVi = $this->get('/');
        $resVi->assertStatus(200);
        $contentVi = $resVi->getContent();

        $this->assertStringContainsString('<html lang="vi">', $contentVi);
        $this->assertStringContainsString('hreflang="vi"', $contentVi);
        $this->assertStringContainsString('hreflang="en"', $contentVi);
        $this->assertStringContainsString('hreflang="x-default"', $contentVi);

        // Trang tiếng Anh
        $resEn = $this->get('/en');
        $this->assertNotEquals(500, $resEn->getStatusCode());
        $this->assertStringContainsString('<html lang="en">', $resEn->getContent());
    }

    /**
     * 5. Các security headers được thiết lập an toàn trên HTTP response.
     */
    public function test_security_headers_are_present()
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'SAMEORIGIN');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    /**
     * 6. Tài nguyên ảnh Hero Banner WebP và SVG Logo được tối ưu hóa dung lượng.
     */
    public function test_hero_banner_webp_and_logo_assets()
    {
        $webpPath = public_path('assets/images/homepage/hero-banner.webp');
        $this->assertFileExists($webpPath);
        $this->assertLessThan(300000, filesize($webpPath), 'Hero Banner WebP phải nhỏ hơn 300 KB');

        $svgLogoPath = public_path('assets/images/header/figma-header-logo.svg');
        $this->assertFileExists($svgLogoPath);
        $this->assertLessThan(20000, filesize($svgLogoPath), 'Logo SVG phải nhỏ hơn 20 KB');
    }
}
