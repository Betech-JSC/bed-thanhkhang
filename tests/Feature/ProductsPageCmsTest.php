<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Database\Seeders\ThanhKhangProductHeroSeeder;
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

class ProductsPageCmsTest extends TestCase
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
    }

    protected function clearAllCatalogTablesAndCaches(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('product_ref_categories')->delete();
        DB::table('related_products')->delete();
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
        Product::clearProductCaches();
        ProductCategory::clearCategoryCaches();
        Cache::flush();
    }

    public function test_products_empty_database_state_returns_http_200_with_zero_fallback(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        // 1. Vietnamese (/san-pham) — Inertia + JSON + HTML
        $viInertia = $this->get('/san-pham', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $viInertia->assertStatus(200);
        $viData = $viInertia->json();

        $this->assertSame('Product/Index', $viData['component']);
        $this->assertSame([], $viData['props']['products']);
        $this->assertSame([], $viData['props']['categories']);
        $this->assertNull($viData['props']['heroBanner']);
        $this->assertNull($viData['props']['seo']['title']);
        $this->assertNull($viData['props']['seo']['description']);
        $this->assertNull($viData['props']['seo']['image']);
        $this->assertNull($viData['props']['seo']['seo_meta_title']);
        $this->assertNull($viData['props']['seo']['seo_meta_description']);

        $this->get('/san-pham')->assertStatus(200);

        // 2. English (/en/products) — Inertia + JSON + HTML
        $enInertia = $this->get('/en/products', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $enInertia->assertStatus(200);
        $enData = $enInertia->json();

        $this->assertSame('Product/Index', $enData['component']);
        $this->assertSame([], $enData['props']['products']);
        $this->assertSame([], $enData['props']['categories']);
        $this->assertNull($enData['props']['heroBanner']);
        $this->assertNull($enData['props']['seo']['title']);
        $this->assertNull($enData['props']['seo']['description']);
        $this->assertNull($enData['props']['seo']['image']);
        $this->assertNull($enData['props']['seo']['seo_meta_title']);
        $this->assertNull($enData['props']['seo']['seo_meta_description']);

        $this->get('/en/products')->assertStatus(200);
    }

    public function test_product_hero_seeder_is_idempotent_and_serves_bilingual_catalog_and_seo_hierarchy(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $this->assertTrue(config('slider.positions.BANNER_PRODUCT_HERO.banner') ?? null);

        // Run seeder twice to verify strict idempotency
        $this->seed(ThanhKhangProductHeroSeeder::class);
        $this->seed(ThanhKhangProductHeroSeeder::class);
        $this->assertSame(1, Slider::where('position_display', 'BANNER_PRODUCT_HERO')->count());

        // Verify seeder does NOT overwrite Admin edit on BANNER_PRODUCT_HERO
        $heroSlider = Slider::where('position_display', 'BANNER_PRODUCT_HERO')->firstOrFail();
        DB::table('slider_translations')
            ->where('slider_id', $heroSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'SẢN PHẨM CHỈNH SỬA CMS']);
        $this->seed(ThanhKhangProductHeroSeeder::class);
        $this->assertSame(
            'SẢN PHẨM CHỈNH SỬA CMS',
            DB::table('slider_translations')->where('slider_id', $heroSlider->id)->where('locale', 'vi')->value('title')
        );

        // Restore default seeded title for standard assertion
        DB::table('slider_translations')
            ->where('slider_id', $heroSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'SẢN PHẨM']);
        Slider::clearSliderCaches();

        // Create real category tree + product
        $rootCat = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $rootCat->id,
                'locale' => 'vi',
                'title' => 'Linh kiện thang cuốn',
                'slug' => 'linh-kien-thang-cuon',
                'seo_slug' => 'linh-kien-thang-cuon',
                'description' => 'Mô tả danh mục VI',
            ],
            [
                'product_category_id' => $rootCat->id,
                'locale' => 'en',
                'title' => 'Escalator Spare Parts',
                'slug' => 'escalator-spare-parts',
                'seo_slug' => 'escalator-spare-parts',
                'description' => 'Category description EN',
            ],
        ]);

        $childCat = ProductCategory::create([
            'parent_id' => $rootCat->id,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $childCat->id,
                'locale' => 'vi',
                'title' => 'Xích thang cuốn',
                'slug' => 'xich-thang-cuon',
                'seo_slug' => 'xich-thang-cuon',
                'description' => 'Danh mục con VI',
            ],
            [
                'product_category_id' => $childCat->id,
                'locale' => 'en',
                'title' => 'Escalator Chains',
                'slug' => 'escalator-chains',
                'seo_slug' => 'escalator-chains',
                'description' => 'Child category EN',
            ],
        ]);

        $product = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-HERO-01',
            'material' => 'Thép hợp kim 40Cr',
            'image' => ['path' => '/assets/figma/san-pham/sample.webp', 'alt' => 'Sample Product'],
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $product->id,
                'locale' => 'vi',
                'title' => 'Xích bậc thang cuốn TL133',
                'slug' => 'xich-bac-thang-cuon-tl133',
                'seo_slug' => 'xich-bac-thang-cuon-tl133',
                'description' => 'Xích bậc thang cuốn chuẩn công nghiệp',
            ],
            [
                'product_id' => $product->id,
                'locale' => 'en',
                'title' => 'Escalator Step Chain TL133',
                'slug' => 'escalator-step-chain-tl133',
                'seo_slug' => 'escalator-step-chain-tl133',
                'description' => 'Industrial standard escalator step chain',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $product->id,
            'product_category_id' => $childCat->id,
        ]);

        // Seed General Settings SEO fallback
        settings()->group('general')->set([
            'seo_meta_title' => 'Sản phẩm Công nghiệp - General Settings VI',
            'seo_meta_title_en' => 'Industrial Products - General Settings EN',
            'seo_meta_description' => 'Mô tả SEO chung VI',
            'seo_meta_description_en' => 'General SEO Description EN',
        ]);
        Config::clearBrandCaches();
        Product::clearProductCaches($product);
        ProductCategory::clearCategoryCaches();

        // 1. Verify VI catalog (/san-pham)
        $viRes = $this->getJson('/san-pham')->assertStatus(200)->json();
        $this->assertSame('SẢN PHẨM', $viRes['heroBanner']['title']);
        $this->assertSame('/assets/figma/san-pham/hero-bg.webp', $viRes['heroBanner']['image']['url']);
        $this->assertSame('/assets/figma/san-pham/hero-bg.webp', $viRes['heroBanner']['image_mobile']['url']);
        $this->assertSame(
            'Linh kiện truyền động, băng tải và thang máy chính hãng, kèm tư vấn kỹ thuật chọn đúng loại cho công trình của bạn',
            $viRes['heroBanner']['description']
        );
        $this->assertCount(1, $viRes['products']);
        $this->assertSame('Xích bậc thang cuốn TL133', $viRes['products'][0]['title']);
        $this->assertCount(1, $viRes['categories']);
        $this->assertSame('Linh kiện thang cuốn', $viRes['categories'][0]['title']);
        $this->assertSame('Xích thang cuốn', $viRes['categories'][0]['children'][0]['title']);
        $this->assertSame('Sản phẩm Công nghiệp - General Settings VI', $viRes['seo']['title']);
        $this->assertSame('Mô tả SEO chung VI', $viRes['seo']['description']);

        // 2. Verify EN catalog (/en/products)
        $enRes = $this->getJson('/en/products')->assertStatus(200)->json();
        $this->assertSame('PRODUCTS', $enRes['heroBanner']['title']);
        $this->assertSame('/assets/figma/san-pham/hero-bg.webp', $enRes['heroBanner']['image']['url']);
        $this->assertSame(
            'Genuine transmission components, conveyor systems, and escalator parts with technical selection consulting for your project',
            $enRes['heroBanner']['description']
        );
        $this->assertCount(1, $enRes['products']);
        $this->assertSame('Escalator Step Chain TL133', $enRes['products'][0]['title']);
        $this->assertSame('Escalator Spare Parts', $enRes['categories'][0]['title']);
        $this->assertSame('Industrial Products - General Settings EN', $enRes['seo']['title']);
        $this->assertSame('General SEO Description EN', $enRes['seo']['description']);

        // 3. MetaPage overrides General Settings immediately
        MetaPage::create([
            'url' => '/san-pham',
            'seo_meta_title' => 'Sản phẩm MetaPage Override VI',
            'seo_meta_description' => 'Mô tả MetaPage VI',
        ]);
        MetaPage::create([
            'url' => '/en/products',
            'seo_meta_title' => 'Products MetaPage Override EN',
            'seo_meta_description' => 'MetaPage Description EN',
        ]);

        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('products_index_en'));

        $this->assertSame('Sản phẩm MetaPage Override VI', $this->getJson('/san-pham')->assertStatus(200)->json('seo.title'));
        $this->assertSame('Products MetaPage Override EN', $this->getJson('/en/products')->assertStatus(200)->json('seo.title'));
    }

    public function test_product_zero_fallback_pdp_metadata_specifications_and_schema(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        // 1. Minimal B2B product with empty specs/metadata/sku/price
        $minimal = new Product([
            'status' => Product::STATUS_ACTIVE,
            'sku' => null,
            'price' => null,
        ]);
        $minimal->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Linh kiện tối giản Zero Fallback',
            'slug' => 'linh-kien-toi-gian-zero-fallback',
            'seo_slug' => 'linh-kien-toi-gian-zero-fallback',
            'description' => null,
        ]);
        $minimal->save();

        // Ensure saving did NOT write synthesized specifications or pdp_metadata into DB
        $rawRow = DB::table('products')->where('id', $minimal->id)->first();
        $this->assertNull($rawRow->specifications);
        $this->assertNull($rawRow->pdp_metadata);

        $pdpJson = $this->getJson('/san-pham/linh-kien-toi-gian-zero-fallback')
            ->assertStatus(200)
            ->json();

        $this->assertSame([], $pdpJson['product']['specifications']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['story_title']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['story_intro']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['image_color_label']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['customization_label']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['feature1_title']);
        $this->assertNull($pdpJson['product']['pdp_metadata']['feature2_title']);
        $this->assertSame([], $pdpJson['product']['pdp_metadata']['manufacturing_info']);
        $this->assertSame([], $pdpJson['product']['pdp_metadata']['care_instructions']);

        $minimalSchema = collect($pdpJson['seo']['seo_schemas'] ?? [])->firstWhere('@type', 'Product');
        $this->assertNotNull($minimalSchema);
        $this->assertArrayNotHasKey('sku', $minimalSchema);
        $this->assertArrayNotHasKey('offers', $minimalSchema);
        $this->assertNull($minimalSchema['brand']['name']);

        // 2. Priced product with real SKU & price
        $priced = new Product([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-B2B-999',
            'price' => 450000,
            'material' => 'Thép hợp kim',
        ]);
        $priced->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Xích tải có giá báo mẫu',
            'slug' => 'xich-tai-co-gia-bao-mau',
            'seo_slug' => 'xich-tai-co-gia-bao-mau',
            'description' => 'Mô tả sản phẩm có giá',
        ]);
        $priced->save();

        $pricedJson = $this->getJson('/san-pham/xich-tai-co-gia-bao-mau')
            ->assertStatus(200)
            ->json();

        $pricedSchema = collect($pricedJson['seo']['seo_schemas'] ?? [])->firstWhere('@type', 'Product');
        $this->assertNotNull($pricedSchema);
        $this->assertSame('TK-B2B-999', $pricedSchema['sku']);
        $this->assertSame('450000', $pricedSchema['offers']['price']);
        $this->assertSame('VND', $pricedSchema['offers']['priceCurrency']);
        $this->assertArrayNotHasKey('priceValidUntil', $pricedSchema['offers']);

        // Verify real column specs are built dynamically without writing fake rows to DB
        $this->assertSame('TK-B2B-999', $pricedJson['product']['specifications'][0]['value']);
        $this->assertSame('Thép hợp kim', $pricedJson['product']['specifications'][1]['value']);
    }

    public function test_product_category_url_and_nested_helpers_work_safely(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $root = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $root->id,
                'locale' => 'vi',
                'title' => 'Linh kiện thang cuốn',
                'slug' => 'linh-kien-thang-cuon',
                'seo_slug' => 'linh-kien-thang-cuon',
            ],
            [
                'product_category_id' => $root->id,
                'locale' => 'en',
                'title' => 'Escalator Spare Parts',
                'slug' => 'escalator-spare-parts',
                'seo_slug' => 'escalator-spare-parts',
            ],
        ]);

        $child = ProductCategory::create([
            'parent_id' => $root->id,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $child->id,
                'locale' => 'vi',
                'title' => 'Xích thang cuốn',
                'slug' => 'xich-thang-cuon',
                'seo_slug' => 'xich-thang-cuon',
            ],
        ]);

        $freshRoot = ProductCategory::with(['translations', 'parent'])->findOrFail($root->id);
        $freshChild = ProductCategory::with(['translations', 'parent'])->findOrFail($child->id);

        $this->assertSame('http://localhost/san-pham?category=linh-kien-thang-cuon', $freshRoot->url['VI']);
        $this->assertSame('http://localhost/en/products?category=escalator-spare-parts', $freshRoot->url['EN']);

        // Verify transformNested works statically without $this error and getBanner handles null ancestors safely
        $nested = ProductCategory::transformNested(collect([$freshRoot]), collect([$freshChild]));
        $this->assertCount(1, $nested);
        $this->assertSame('Linh kiện thang cuốn', $nested[0]['title']);
        $this->assertCount(1, $nested[0]['nodes']);

        $banner = $freshRoot->getBanner(['banner' => true]);
        $this->assertCount(0, $banner);

        $emptySlugCat = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 2,
        ]);
        DB::table('product_category_translations')->insert([
            'product_category_id' => $emptySlugCat->id,
            'locale' => 'vi',
            'title' => 'Danh mục trống slug',
            'slug' => '',
            'seo_slug' => '',
        ]);
        $freshEmpty = ProductCategory::with('translations')->findOrFail($emptySlugCat->id);
        $this->assertSame('http://localhost/san-pham', $freshEmpty->url['VI']);
    }

    public function test_admin_post_invalidates_caches_and_updates_products_index_and_show_immediately(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_products_cms@jamstackvietnam.com'],
            [
                'name' => 'Admin Products CMS',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $this->seed(ThanhKhangProductHeroSeeder::class);
        $heroSlider = Slider::where('position_display', 'BANNER_PRODUCT_HERO')->firstOrFail();

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
            ],
            [
                'product_category_id' => $category->id,
                'locale' => 'en',
                'title' => 'Initial Category',
                'slug' => 'initial-category',
                'seo_slug' => 'initial-category',
            ],
        ]);

        $product = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-INIT-01',
            'position' => 1,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $product->id,
                'locale' => 'vi',
                'title' => 'Sản phẩm Ban Đầu',
                'slug' => 'san-pham-ban-dau',
                'seo_slug' => 'san-pham-ban-dau',
                'description' => 'Mô tả ban đầu',
            ],
            [
                'product_id' => $product->id,
                'locale' => 'en',
                'title' => 'Initial Product',
                'slug' => 'initial-product',
                'seo_slug' => 'initial-product',
                'description' => 'Initial description',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $product->id,
            'product_category_id' => $category->id,
        ]);

        settings()->group('general')->set([
            'general_business_name' => 'Công ty Ban Đầu',
            'seo_meta_title' => 'SEO Ban Đầu',
        ]);
        Config::clearBrandCaches();
        Slider::clearSliderCaches();
        Product::clearProductCaches($product);

        // Warm caches
        $this->getJson('/san-pham')->assertStatus(200);
        $this->getJson('/en/products')->assertStatus(200);
        $this->getJson('/san-pham/san-pham-ban-dau')->assertStatus(200);
        $this->assertTrue(Cache::has('products_index_vi'));
        $this->assertTrue(Cache::has('products_index_en'));
        $this->assertTrue(Cache::has('product_show_san-pham-ban-dau_vi'));

        // 1. Admin POST updates Slider (BANNER_PRODUCT_HERO)
        $sliderRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.sliders.store', ['id' => $heroSlider->id]),
            [
                'id' => $heroSlider->id,
                'status' => Slider::STATUS_ACTIVE,
                'position_display' => 'BANNER_PRODUCT_HERO',
                'position_sort' => 10,
                'is_video' => 0,
                'title' => 'SẢN PHẨM CẬP NHẬT ADMIN',
                'description' => 'Mô tả hero cập nhật từ Admin',
                'image' => ['path' => '/assets/figma/san-pham/hero-bg.webp', 'alt' => 'Updated Hero'],
                'image_mobile' => ['path' => '/assets/figma/san-pham/hero-bg.webp', 'alt' => 'Updated Hero'],
            ]
        );
        $this->assertTrue(in_array($sliderRes->getStatusCode(), [200, 302], true));
        $sliderRes->assertSessionHasNoErrors();
        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('products_index_en'));
        $this->assertSame(
            'SẢN PHẨM CẬP NHẬT ADMIN',
            $this->getJson('/san-pham')->assertStatus(200)->json('heroBanner.title')
        );

        // Re-warm EN cache
        $this->getJson('/en/products')->assertStatus(200);
        $this->assertTrue(Cache::has('products_index_vi'));
        $this->assertTrue(Cache::has('products_index_en'));

        // 2. Admin POST updates ProductCategory (VI)
        $catRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.product-categories.store', ['id' => $category->id]),
            [
                'id' => $category->id,
                'parent_id' => 0,
                'status' => ProductCategory::STATUS_ACTIVE,
                'position' => 1,
                'view_count' => 0,
                'title' => 'Danh mục Đã Cập Nhật CMS',
                'slug' => 'danh-muc-ban-dau',
                'products' => [['id' => $product->id]],
            ]
        );
        $this->assertTrue(in_array($catRes->getStatusCode(), [200, 302], true));
        $catRes->assertSessionHasNoErrors();
        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('products_index_en'));
        $this->assertSame(
            'Danh mục Đã Cập Nhật CMS',
            $this->getJson('/san-pham')->assertStatus(200)->json('categories.0.title')
        );

        // Re-warm caches including PDP
        $this->getJson('/en/products')->assertStatus(200);
        $this->getJson('/san-pham/san-pham-ban-dau')->assertStatus(200);
        $this->assertTrue(Cache::has('product_show_san-pham-ban-dau_vi'));

        // 3. Admin POST updates Product -> clears both products_index_* and product_show_*
        $prodRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $product->id]),
            [
                'id' => $product->id,
                'status' => Product::STATUS_ACTIVE,
                'title' => 'Sản phẩm Cập Nhật CMS',
                'slug' => 'san-pham-ban-dau',
                'sku' => 'TK-UPDATED-99',
                'categories' => [['id' => $category->id]],
            ]
        );
        $this->assertTrue(in_array($prodRes->getStatusCode(), [200, 302], true));
        $prodRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('products_index_en'));
        $this->assertFalse(Cache::has('product_show_san-pham-ban-dau_vi'));

        $indexAfterProd = $this->getJson('/san-pham')->assertStatus(200)->json();
        $this->assertSame('Sản phẩm Cập Nhật CMS', $indexAfterProd['products'][0]['title']);

        $showAfterProd = $this->getJson('/san-pham/san-pham-ban-dau')->assertStatus(200)->json();
        $this->assertSame('Sản phẩm Cập Nhật CMS', $showAfterProd['product']['title']);
        $this->assertSame('TK-UPDATED-99', $showAfterProd['product']['sku']);
        $this->assertNull($showAfterProd['product']['pdp_metadata']['image_color_label']);

        // 4. Admin POST updates General Settings -> clears products_index_* and updates PDP brand schema immediately
        $this->assertTrue(Cache::has('product_show_san-pham-ban-dau_vi'));
        $settingRes = $this->actingAs($admin, 'admin')->post(
            route('admin.settings.store', ['id' => 'general']),
            [
                'id' => 'general',
                'general_business_name' => 'Công ty Thành Khang Mới',
                'seo_meta_title' => 'SEO Products Mới Từ Settings',
            ]
        );
        $this->assertTrue(in_array($settingRes->getStatusCode(), [200, 302], true));
        $settingRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('products_index_en'));
        $this->assertSame('SEO Products Mới Từ Settings', $this->getJson('/san-pham')->assertStatus(200)->json('seo.title'));

        $pdpAfterBrand = $this->getJson('/san-pham/san-pham-ban-dau')->assertStatus(200)->json();
        $brandSchema = collect($pdpAfterBrand['seo']['seo_schemas'] ?? [])->firstWhere('@type', 'Product');
        $this->assertSame('Công ty Thành Khang Mới', $brandSchema['brand']['name']);
    }
}
