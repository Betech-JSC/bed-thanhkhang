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
        $this->assertEquals(4, ProductCategory::count());
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
    }
}
