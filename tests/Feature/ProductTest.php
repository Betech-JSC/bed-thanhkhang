<?php

namespace Tests\Feature;

use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Database\Seeders\FigmaProductsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(FigmaProductsSeeder::class);
    }

    public function test_figma_products_seeder_populates_twenty_one_products_and_four_categories(): void
    {
        $this->assertEquals(4, ProductCategory::whereParent()->count());
        $this->assertEquals(11, ProductCategory::count());
        $this->assertEquals(21, Product::count());

        $featuredCount = Product::where('is_featured', 1)->count();
        $this->assertEquals(16, $featuredCount);
    }

    public function test_home_page_returns_four_featured_products_per_category(): void
    {
        $controller = app(\App\Http\Controllers\Frontend\HomeController::class);
        $response = $controller->index();
        $props = $response->toResponse(request())->getOriginalContent()->getData()['page']['props'];

        $categories = $props['categories'] ?? [];
        $this->assertCount(4, $categories);

        foreach ($categories as $cat) {
            $this->assertTrue($cat['hasBestChoice']);
            $this->assertCount(4, $cat['items']);
            foreach ($cat['items'] as $item) {
                $this->assertTrue((bool) $item['is_featured']);
            }
        }
    }

    public function test_product_index_payload_returns_catalog_products_and_categories(): void
    {
        $controller = app(\App\Http\Controllers\Frontend\ProductController::class);
        $response = $controller->index(request());
        $props = $response->toResponse(request())->getOriginalContent()->getData()['page']['props'];

        $products = $props['products'] ?? [];
        $categories = $props['categories'] ?? [];

        $this->assertGreaterThanOrEqual(16, count($products));
        $this->assertCount(4, $categories);

        foreach ($products as $p) {
            $this->assertNull($p['price']);
            $this->assertNull($p['compare_at_price']);
            $this->assertStringNotContainsStringIgnoringCase('logistics', $p['title']);
        }
    }

    public function test_product_show_payload_returns_seven_specifications_and_four_related_products(): void
    {
        $controller = app(\App\Http\Controllers\Frontend\ProductController::class);
        $response = $controller->show('mo-hinh-cao-trang-tri');
        $props = $response->toResponse(request())->getOriginalContent()->getData()['page']['props'];

        $product = $props['product'] ?? [];
        $specs = $product['specifications'] ?? [];
        $related = $props['related_products'] ?? [];

        $this->assertEquals('Mô hình Cáo trang trí', $product['title']);
        $this->assertCount(7, $specs);

        $specLabels = array_column($specs, 'label');
        $this->assertContains('Công nghệ in', $specLabels);
        $this->assertContains('Vật liệu', $specLabels);
        $this->assertContains('Kích thước mẫu', $specLabels);
        $this->assertContains('Chiều cao lớp in', $specLabels);
        $this->assertContains('Đầu phun', $specLabels);
        $this->assertContains('Mật độ ruột', $specLabels);
        $this->assertContains('Vật liệu hỗ trợ', $specLabels);

        $this->assertCount(4, $related);
        $this->assertEquals('Cam', $product['image_color_label']);
        $this->assertEquals('60 × 31 × 48 mm', $product['sample_size']);
        $this->assertEquals('Nhỏ gọn. Nhiều điều để khám phá.', $product['story_title']);
        $this->assertNotEmpty($product['story_intro']);
    }

    public function test_categories_hierarchy_and_services_listing_and_home_process_steps(): void
    {
        $this->seed(\Database\Seeders\ServiceContentSeeder::class);
        $this->seed(\Database\Seeders\ProcessContentSeeder::class);

        $productController = app(\App\Http\Controllers\Frontend\ProductController::class);
        $prodProps = $productController->index(request())->toResponse(request())->getOriginalContent()->getData()['page']['props'];
        $categories = collect($prodProps['categories'] ?? []);

        $stationery = $categories->firstWhere('slug', 'trang-tri-do-dung');
        $this->assertNotNull($stationery);
        $childSlugs = array_column($stationery['children'] ?? [], 'slug');
        $this->assertContains('ly', $childSlugs);
        $this->assertContains('lo-cam-hoa-de-ban', $childSlugs);
        $this->assertContains('den', $childSlugs);

        $homeController = app(\App\Http\Controllers\Frontend\HomeController::class);
        $homeProps = $homeController->index()->toResponse(request())->getOriginalContent()->getData()['page']['props'];
        $this->assertCount(5, $homeProps['processSteps'] ?? []);

        $serviceController = app(\App\Http\Controllers\Frontend\ServiceController::class);
        $serviceProps = $serviceController->index()->toResponse(request())->getOriginalContent()->getData()['page']['props'];
        $services = collect($serviceProps['services'] ?? []);
        $this->assertCount(3, $services);

        $design = $services->firstWhere('slug', 'thiet-ke-3d');
        $this->assertEquals('Từ phác thảo đến hình khối rõ ràng.', $design['subtitle']);
        $this->assertNotEmpty($design['tags']);
        $this->assertNotEmpty($design['preparation']);
        $this->assertNotEmpty($design['completion']);
        $this->assertEquals('/images/services/service-design-3d.png', $design['image']['url']);
    }
}
