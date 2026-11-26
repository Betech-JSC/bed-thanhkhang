<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Database\Seeders\ThanhKhangDocumentBannerSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentsPageCmsTest extends TestCase
{
    use RefreshDatabase;

    protected function refreshDatabase()
    {
        if (
            config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:'
        ) {
            $this->fail('SAFETY ABORT: RefreshDatabase is strictly restricted to sqlite :memory: only.');
        }

        $this->usingInMemoryDatabase()
            ? $this->refreshInMemoryDatabase()
            : $this->refreshTestDatabase();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('settings')) {
            $this->artisan('migrate', [
                '--path' => 'packages/bed-package-essentials/laravel-settings/src/migrations',
                '--realpath' => false,
            ]);
        }

        if (!Schema::hasTable('products') || !Schema::hasTable('product_categories')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/product',
                '--realpath' => false,
            ]);
        }

        if (!Schema::hasTable('posts')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/blog',
                '--realpath' => false,
            ]);
        }
    }

    public function test_documents_empty_database_state_returns_http_200_with_zero_fallback(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('post_translations')->delete();
        DB::table('posts')->delete();
        DB::table('product_ref_categories')->delete();
        DB::table('product_translations')->delete();
        DB::table('products')->delete();
        DB::table('product_category_translations')->delete();
        DB::table('product_categories')->delete();
        if (Schema::hasTable('meta_pages')) {
            DB::table('meta_pages')->delete();
        }
        DB::table('settings')->delete();

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
        Post::clearPostCaches();
        ProductCategory::clearCategoryCaches();
        Cache::flush();

        // 1. Vietnamese (/tai-lieu) — Inertia + HTML
        $viInertia = $this->get('/tai-lieu', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $viInertia->assertStatus(200);
        $viData = $viInertia->json();

        $this->assertSame('Documents/Index', $viData['component']);
        $this->assertNull($viData['props']['heroBanner']);
        $this->assertSame([], $viData['props']['documents']);
        $this->assertSame([], $viData['props']['product_categories']);
        $this->assertSame([], $viData['props']['categories']);
        $this->assertNull($viData['props']['ctaBanner']);
        $this->assertNull($viData['props']['seo']);

        $viHtml = $this->get('/tai-lieu');
        $viHtml->assertStatus(200);
        $viHtml->assertSee('hreflang="vi" href="http://localhost/tai-lieu"', false);
        $viHtml->assertSee('hreflang="en" href="http://localhost/en/documents"', false);

        // 2. English (/en/documents) — Inertia + HTML
        $enInertia = $this->get('/en/documents', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $enInertia->assertStatus(200);
        $enData = $enInertia->json();

        $this->assertSame('Documents/Index', $enData['component']);
        $this->assertNull($enData['props']['heroBanner']);
        $this->assertSame([], $enData['props']['documents']);
        $this->assertSame([], $enData['props']['product_categories']);
        $this->assertSame([], $enData['props']['categories']);
        $this->assertNull($enData['props']['ctaBanner']);
        $this->assertNull($enData['props']['seo']);

        $enHtml = $this->get('/en/documents');
        $enHtml->assertStatus(200);
        $enHtml->assertSee('hreflang="vi" href="http://localhost/tai-lieu"', false);
        $enHtml->assertSee('hreflang="en" href="http://localhost/en/documents"', false);
    }

    public function test_documents_seeded_banners_category_product_image_fallback_and_en_descriptions(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('product_ref_categories')->delete();
        DB::table('product_translations')->delete();
        DB::table('products')->delete();
        DB::table('product_category_translations')->delete();
        DB::table('product_categories')->delete();
        if (Schema::hasTable('meta_pages')) {
            DB::table('meta_pages')->delete();
        }
        DB::table('settings')->delete();
        Cache::flush();

        $positions = config('slider.positions');
        $this->assertTrue($positions['BANNER_DOCUMENT_HERO']['banner'] ?? null);
        $this->assertTrue($positions['BANNER_DOCUMENT_CTA']['banner'] ?? null);

        // Run seeder twice to prove strict idempotency
        $this->seed(ThanhKhangDocumentBannerSeeder::class);
        $this->seed(ThanhKhangDocumentBannerSeeder::class);

        $this->assertSame(1, Slider::where('position_display', 'BANNER_DOCUMENT_HERO')->count());
        $this->assertSame(1, Slider::where('position_display', 'BANNER_DOCUMENT_CTA')->count());

        // Create root category WITHOUT image, child category, and product WITH image
        $now = now();
        $rootCatId = DB::table('product_categories')->insertGetId([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
            'view_count' => 0,
            'image' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $rootCatId,
                'locale' => 'vi',
                'title' => 'Linh kiện thang cuốn',
                'slug' => 'linh-kien-thang-cuon',
                'seo_slug' => 'linh-kien-thang-cuon',
                'description' => 'Tổng kho xích bậc thang cuốn, xích băng tải hành khách, xích dẫn động và xích tay vịn chính hãng Universal Chains.',
            ],
            [
                'product_category_id' => $rootCatId,
                'locale' => 'en',
                'title' => 'Escalator Spare Parts',
                'slug' => 'escalator-spare-parts',
                'seo_slug' => 'escalator-spare-parts',
                'description' => 'Tổng kho xích bậc thang cuốn, xích băng tải hành khách, xích dẫn động và xích tay vịn chính hãng Universal Chains.',
            ],
        ]);

        // Run migration 000007 to verify legacy Vietnamese EN description is updated non-destructively
        $migration = require base_path('database/migrations/2026_11_26_000007_update_root_product_categories_en_descriptions.php');
        $migration->up();

        // Also create a second root category with empty slug to assert URL does not append '?category='
        $emptySlugCatId = DB::table('product_categories')->insertGetId([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 2,
            'view_count' => 0,
            'image' => null,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $emptySlugCatId,
                'locale' => 'vi',
                'title' => 'Danh mục không slug',
                'slug' => '',
                'seo_slug' => '',
                'description' => 'Mô tả VI',
            ],
            [
                'product_category_id' => $emptySlugCatId,
                'locale' => 'en',
                'title' => 'Slugless Category',
                'slug' => '',
                'seo_slug' => '',
                'description' => 'English description',
            ],
        ]);

        $childCatId = DB::table('product_categories')->insertGetId([
            'parent_id' => $rootCatId,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
            'view_count' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $childCatId,
                'locale' => 'vi',
                'title' => 'Xích thang cuốn',
                'slug' => 'xich-thang-cuon',
                'seo_slug' => 'xich-thang-cuon',
                'description' => 'Xích thang cuốn',
            ],
            [
                'product_category_id' => $childCatId,
                'locale' => 'en',
                'title' => 'Escalator Chains',
                'slug' => 'escalator-chains',
                'seo_slug' => 'escalator-chains',
                'description' => 'Escalator Chains',
            ],
        ]);

        $productId = DB::table('products')->insertGetId([
            'status' => Product::STATUS_ACTIVE,
            'is_featured' => 1,
            'image' => json_encode(['path' => '/assets/images/documents/sample-chain.png', 'alt' => 'Sample Chain']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('product_translations')->insert([
            [
                'product_id' => $productId,
                'locale' => 'vi',
                'title' => 'Xích bậc thang cuốn C1',
                'slug' => 'xich-bac-thang-cuon-c1',
                'seo_slug' => 'xich-bac-thang-cuon-c1',
            ],
            [
                'product_id' => $productId,
                'locale' => 'en',
                'title' => 'Escalator Step Chain C1',
                'slug' => 'escalator-step-chain-c1',
                'seo_slug' => 'escalator-step-chain-c1',
            ],
        ]);

        DB::table('product_ref_categories')->insert([
            'product_id' => $productId,
            'product_category_id' => $childCatId,
        ]);

        // Seed MetaPage SEO for both locales
        MetaPage::create([
            'url' => '/tai-lieu',
            'seo_meta_title' => 'Tài liệu kỹ thuật CMS VI',
            'seo_meta_description' => 'Mô tả tài liệu từ MetaPage VI',
        ]);
        MetaPage::create([
            'url' => '/en/documents',
            'seo_meta_title' => 'Technical Documents CMS EN',
            'seo_meta_description' => 'Documents description from MetaPage EN',
        ]);

        ProductCategory::clearCategoryCaches();
        Slider::clearSliderCaches();

        // Verify VI response
        $viRes = $this->getJson('/tai-lieu');
        $viRes->assertStatus(200);
        $viJson = $viRes->json();

        $this->assertSame('TÀI LIỆU & CATALOGUE', $viJson['heroBanner']['title']);
        $this->assertSame('/assets/images/documents/document-hero-banner.png', $viJson['heroBanner']['image']['url']);
        $this->assertSame('KHÔNG TÌM THẤY TÀI LIỆU BẠN CẦN?', $viJson['ctaBanner']['title']);
        $this->assertSame('Yêu cầu tài liệu kỹ thuật', $viJson['ctaBanner']['primary_cta_text']);
        $this->assertSame('/lien-he', $viJson['ctaBanner']['primary_cta_link']);
        $this->assertSame('Gọi Hotline', $viJson['ctaBanner']['secondary_cta_text']);
        $this->assertNull($viJson['ctaBanner']['secondary_cta_link']);
        $this->assertNull($viJson['ctaBanner']['image']['url']);

        $this->assertCount(2, $viJson['product_categories']);
        $this->assertSame('/san-pham?category=linh-kien-thang-cuon', $viJson['product_categories'][0]['url']);
        $this->assertSame('/assets/images/documents/sample-chain.png', $viJson['product_categories'][0]['image']['url']);
        $this->assertSame('/san-pham', $viJson['product_categories'][1]['url']);
        $this->assertSame('Tài liệu kỹ thuật CMS VI', $viJson['seo']['title']);

        // Verify EN response
        $enRes = $this->getJson('/en/documents');
        $enRes->assertStatus(200);
        $enJson = $enRes->json();

        $this->assertSame('DOCUMENTS & CATALOGUES', $enJson['heroBanner']['title']);
        $this->assertSame('/assets/images/documents/document-hero-banner.png', $enJson['heroBanner']['image']['url']);
        $this->assertSame("CAN'T FIND THE DOCUMENT YOU NEED?", $enJson['ctaBanner']['title']);
        $this->assertSame('Request Technical Docs', $enJson['ctaBanner']['primary_cta_text']);
        $this->assertSame('/en/contact', $enJson['ctaBanner']['primary_cta_link']);
        $this->assertSame('Call Hotline', $enJson['ctaBanner']['secondary_cta_text']);
        $this->assertNull($enJson['ctaBanner']['secondary_cta_link']);
        $this->assertNull($enJson['ctaBanner']['image']['url']);

        $this->assertCount(2, $enJson['product_categories']);
        $this->assertSame('/en/products?category=escalator-spare-parts', $enJson['product_categories'][0]['url']);
        $this->assertSame(
            'Complete inventory of escalator step chains, passenger conveyor chains, main drive chains, and handrail drive chains from Universal Chains.',
            $enJson['product_categories'][0]['description']
        );
        $this->assertSame('/assets/images/documents/sample-chain.png', $enJson['product_categories'][0]['image']['url']);
        $this->assertSame('/en/products', $enJson['product_categories'][1]['url']);
        $this->assertSame('Technical Documents CMS EN', $enJson['seo']['title']);

        // Verify '#' sanitization on CTA links in DocumentController
        $ctaSlider = Slider::where('position_display', 'BANNER_DOCUMENT_CTA')->firstOrFail();
        DB::table('slider_translations')
            ->where('slider_id', $ctaSlider->id)
            ->update([
                'primary_cta_link' => '#',
                'secondary_cta_link' => ' # ',
                'link' => '#',
            ]);
        Slider::clearSliderCaches();

        $sanitizedCta = $this->getJson('/tai-lieu')->assertStatus(200)->json('ctaBanner');
        $this->assertNull($sanitizedCta['primary_cta_link']);
        $this->assertNull($sanitizedCta['secondary_cta_link']);
        $this->assertNull($sanitizedCta['link']);
    }

    public function test_documents_cache_invalidation_across_product_category_slider_and_config(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('product_ref_categories')->delete();
        DB::table('product_translations')->delete();
        DB::table('products')->delete();
        DB::table('product_category_translations')->delete();
        DB::table('product_categories')->delete();
        DB::table('settings')->delete();
        Cache::flush();

        $this->seed([
            ThanhKhangDocumentBannerSeeder::class,
        ]);

        $now = now();
        $category = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $category->id,
                'locale' => 'vi',
                'title' => 'Danh mục Ban Đầu',
                'slug' => 'danh-muc-ban-dau',
                'seo_slug' => 'danh-muc-ban-dau',
                'description' => 'Mô tả ban đầu',
            ],
            [
                'product_category_id' => $category->id,
                'locale' => 'en',
                'title' => 'Initial Category',
                'slug' => 'initial-category',
                'seo_slug' => 'initial-category',
                'description' => 'Initial description',
            ],
        ]);

        $product = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'is_featured' => 1,
            'image' => ['path' => '/assets/images/documents/initial-product.png', 'alt' => 'Initial'],
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $product->id,
                'locale' => 'vi',
                'title' => 'Sản phẩm thử nghiệm',
                'slug' => 'san-pham-thu-nghiem',
                'seo_slug' => 'san-pham-thu-nghiem',
            ],
            [
                'product_id' => $product->id,
                'locale' => 'en',
                'title' => 'Test Product',
                'slug' => 'test-product',
                'seo_slug' => 'test-product',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $product->id,
            'product_category_id' => $category->id,
        ]);

        Product::clearProductCaches($product);

        // Warm caches for both VI and EN
        $this->getJson('/tai-lieu')->assertStatus(200);
        $this->getJson('/en/documents')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_index_vi'));
        $this->assertTrue(Cache::has('documents_index_en'));

        // 1. Mutate Product -> Product::saved clears documents_index_{locale}
        $product->image = ['path' => '/assets/images/documents/updated-product.png', 'alt' => 'Updated'];
        $product->save();

        $this->assertFalse(Cache::has('documents_index_vi'));
        $this->assertFalse(Cache::has('documents_index_en'));
        $this->assertSame(
            '/assets/images/documents/updated-product.png',
            $this->getJson('/tai-lieu')->assertStatus(200)->json('product_categories.0.image.url')
        );

        // Re-warm EN cache too
        $this->getJson('/en/documents')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_index_vi'));
        $this->assertTrue(Cache::has('documents_index_en'));

        // 2. Mutate ProductCategory -> ProductCategory::saved clears documents_index_{locale}
        DB::table('product_category_translations')
            ->where('product_category_id', $category->id)
            ->where('locale', 'vi')
            ->update(['title' => 'Danh mục Đã Cập Nhật']);
        $category->position = 2;
        $category->save();

        $this->assertFalse(Cache::has('documents_index_vi'));
        $this->assertFalse(Cache::has('documents_index_en'));
        $this->assertSame(
            'Danh mục Đã Cập Nhật',
            $this->getJson('/tai-lieu')->assertStatus(200)->json('product_categories.0.title')
        );

        // Re-warm EN cache
        $this->getJson('/en/documents')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_index_vi'));
        $this->assertTrue(Cache::has('documents_index_en'));

        // 3. Mutate Slider -> Slider::saved clears documents_index_{locale}
        $heroSlider = Slider::where('position_display', 'BANNER_DOCUMENT_HERO')->firstOrFail();
        DB::table('slider_translations')
            ->where('slider_id', $heroSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'TÀI LIỆU CẬP NHẬT']);
        $heroSlider->position_sort = 20;
        $heroSlider->save();

        $this->assertFalse(Cache::has('documents_index_vi'));
        $this->assertFalse(Cache::has('documents_index_en'));
        $this->assertSame(
            'TÀI LIỆU CẬP NHẬT',
            $this->getJson('/tai-lieu')->assertStatus(200)->json('heroBanner.title')
        );

        // Re-warm EN cache
        $this->getJson('/en/documents')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_index_vi'));
        $this->assertTrue(Cache::has('documents_index_en'));

        // 4. Admin saving General Settings -> SettingController clears documents_index_{locale} via Config::clearBrandCaches()
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_documents_cms@jamstackvietnam.com'],
            [
                'name' => 'Admin Documents CMS',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $postRes = $this->actingAs($admin, 'admin')->post(
            route('admin.settings.store', ['id' => 'general']),
            [
                'id' => 'general',
                'general_business_name' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
                'general_site_name' => 'Tài liệu Kỹ thuật Thành Khang VI',
                'general_site_name_en' => 'Thanh Khang Technical Documents EN',
                'seo_meta_title' => '',
            ]
        );

        $this->assertTrue(in_array($postRes->getStatusCode(), [200, 302], true));
        $postRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('documents_index_vi'));
        $this->assertFalse(Cache::has('documents_index_en'));
        $this->assertSame('Tài liệu Kỹ thuật Thành Khang VI', $this->getJson('/tai-lieu')->assertStatus(200)->json('seo.title'));
        $this->assertSame('Thanh Khang Technical Documents EN', $this->getJson('/en/documents')->assertStatus(200)->json('seo.title'));

        // 5. Updating general.documents in settings & clearing brand caches refreshes documents list in documents_index_{locale}
        settings()->group('general')->set('documents', json_encode([
            [
                'title' => 'Catalogue Xích Công Nghiệp 2026',
                'description' => 'Thông số kỹ thuật đầy đủ',
                'file_url' => '/assets/docs/catalogue-2026.pdf',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        Config::clearBrandCaches();

        $this->assertFalse(Cache::has('documents_index_vi'));
        $this->assertFalse(Cache::has('documents_index_en'));
        $docsJson = $this->getJson('/tai-lieu')->assertStatus(200)->json('documents');
        $this->assertCount(1, $docsJson);
        $this->assertSame('Catalogue Xích Công Nghiệp 2026', $docsJson[0]['title']);
        $this->assertSame('/assets/docs/catalogue-2026.pdf', $docsJson[0]['file_url']);
    }
}
