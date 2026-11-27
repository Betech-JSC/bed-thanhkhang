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

    public function test_admin_updating_product_category_invalidates_pdp_cache_and_enforces_rules_immediately(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_products_cat_cache@jamstackvietnam.com'],
            [
                'name' => 'Admin Category Cache',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $category = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $category->id,
                'locale' => 'vi',
                'title' => 'Xích Công Nghiệp Cũ',
                'slug' => 'xich-cong-nghiep-cu',
                'seo_slug' => 'xich-cong-nghiep-cu',
            ],
            [
                'product_category_id' => $category->id,
                'locale' => 'en',
                'title' => 'Old Industrial Chains',
                'slug' => 'old-industrial-chains',
                'seo_slug' => 'old-industrial-chains',
            ],
        ]);

        $otherCategory = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 2,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $otherCategory->id,
                'locale' => 'vi',
                'title' => 'Danh mục Khác',
                'slug' => 'danh-muc-khac',
                'seo_slug' => 'danh-muc-khac',
            ],
        ]);

        $product = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-CAT-01',
            'position' => 1,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $product->id,
                'locale' => 'vi',
                'title' => 'Xích Tải A1',
                'slug' => 'xich-tai-a1',
                'seo_slug' => 'xich-tai-a1',
            ],
            [
                'product_id' => $product->id,
                'locale' => 'en',
                'title' => 'Load Chain A1',
                'slug' => 'load-chain-a1',
                'seo_slug' => 'load-chain-a1',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $product->id,
            'product_category_id' => $category->id,
        ]);
        Product::clearProductCaches($product);

        // Pre-warm VI and EN PDP caches before Admin category update
        $viInitial = $this->getJson('/san-pham/xich-tai-a1')->assertStatus(200)->json();
        $enInitial = $this->getJson('/en/products/load-chain-a1')->assertStatus(200)->json();
        $this->assertSame('Xích Công Nghiệp Cũ', $viInitial['product']['category']['title']);
        $this->assertSame('Old Industrial Chains', $enInitial['product']['category']['title']);
        $this->assertTrue(Cache::has('product_show_xich-tai-a1_vi'));
        $this->assertTrue(Cache::has('product_show_load-chain-a1_en'));

        // Verify ProductCategory::rules() rejects duplicate seo_slug belonging to another category
        $dupCatRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.product-categories.store', ['id' => $category->id]),
            [
                'id' => $category->id,
                'parent_id' => 0,
                'status' => ProductCategory::STATUS_ACTIVE,
                'title' => 'Xích Công Nghiệp Mới',
                'slug' => 'xich-cong-nghiep-cu',
                'seo_slug' => 'danh-muc-khac',
            ]
        );
        $dupCatRes->assertSessionHasErrors('seo_slug');

        // Verify ProductCategory::rules() does NOT collide with product_translations.seo_slug
        // and accepts updating its own category title & seo_slug (while preserving products relation)
        $catUpdateVi = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.product-categories.store', ['id' => $category->id]),
            [
                'id' => $category->id,
                'parent_id' => 0,
                'status' => ProductCategory::STATUS_ACTIVE,
                'position' => 1,
                'title' => 'Xích Công Nghiệp Mới CMS',
                'slug' => 'xich-cong-nghiep-moi-cms',
                'seo_slug' => 'xich-tai-a1',
            ]
        );
        $this->assertTrue(in_array($catUpdateVi->getStatusCode(), [200, 302], true));
        $catUpdateVi->assertSessionHasNoErrors();

        // Both VI and EN PDP caches must be cleared immediately (0s delay)
        $this->assertFalse(Cache::has('product_show_xich-tai-a1_vi'));
        $this->assertFalse(Cache::has('product_show_load-chain-a1_en'));

        $viUpdated = $this->getJson('/san-pham/xich-tai-a1')->assertStatus(200)->json();
        $this->assertSame('Xích Công Nghiệp Mới CMS', $viUpdated['product']['category']['title']);
        $this->assertSame('xich-tai-a1', $viUpdated['product']['category']['slug']);

        // Update EN translation of the category via Admin EN route
        $this->getJson('/en/products/load-chain-a1')->assertStatus(200);
        $this->assertTrue(Cache::has('product_show_load-chain-a1_en'));

        $catUpdateEn = $this->actingAs($admin, 'admin')->post(
            route('en.admin.product-categories.store', ['id' => $category->id]),
            [
                'id' => $category->id,
                'parent_id' => 0,
                'status' => ProductCategory::STATUS_ACTIVE,
                'position' => 1,
                'title' => 'New Industrial Chains EN',
                'slug' => 'new-industrial-chains-en',
                'seo_slug' => 'new-industrial-chains-en',
            ]
        );
        $this->assertTrue(in_array($catUpdateEn->getStatusCode(), [200, 302], true));
        $catUpdateEn->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('product_show_load-chain-a1_en'));
        $enUpdated = $this->getJson('/en/products/load-chain-a1')->assertStatus(200)->json();
        $this->assertSame('New Industrial Chains EN', $enUpdated['product']['category']['title']);
        $this->assertSame('new-industrial-chains-en', $enUpdated['product']['category']['slug']);
    }

    public function test_admin_updating_product_b_invalidates_product_a_related_products_pdp_cache_immediately(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_related_cache@jamstackvietnam.com'],
            [
                'name' => 'Admin Related Cache',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $category = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $category->id,
                'locale' => 'vi',
                'title' => 'Băng Tải Công Nghiệp',
                'slug' => 'bang-tai-cong-nghiep',
                'seo_slug' => 'bang-tai-cong-nghiep',
            ],
            [
                'product_category_id' => $category->id,
                'locale' => 'en',
                'title' => 'Industrial Conveyors',
                'slug' => 'industrial-conveyors',
                'seo_slug' => 'industrial-conveyors',
            ],
        ]);

        $productA = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-REL-A',
            'price' => 100000,
            'position' => 1,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $productA->id,
                'locale' => 'vi',
                'title' => 'Sản phẩm A Gốc',
                'slug' => 'san-pham-a-goc',
                'seo_slug' => 'san-pham-a-goc',
            ],
            [
                'product_id' => $productA->id,
                'locale' => 'en',
                'title' => 'Product A Original',
                'slug' => 'product-a-original',
                'seo_slug' => 'product-a-original',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $productA->id,
            'product_category_id' => $category->id,
        ]);

        $productB = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-REL-B',
            'price' => 200000,
            'position' => 2,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $productB->id,
                'locale' => 'vi',
                'title' => 'Sản phẩm B Ban Đầu',
                'slug' => 'san-pham-b-ban-dau',
                'seo_slug' => 'san-pham-b-ban-dau',
            ],
            [
                'product_id' => $productB->id,
                'locale' => 'en',
                'title' => 'Product B Initial',
                'slug' => 'product-b-initial',
                'seo_slug' => 'product-b-initial',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $productB->id,
            'product_category_id' => $category->id,
        ]);

        Product::clearProductCaches();

        // Pre-warm Product A PDP caches (VI & EN) and Product B PDP cache (VI)
        $pdpABefore = $this->getJson('/san-pham/san-pham-a-goc')->assertStatus(200)->json();
        $this->getJson('/en/products/product-a-original')->assertStatus(200);
        $this->getJson('/san-pham/san-pham-b-ban-dau')->assertStatus(200);

        $this->assertTrue(Cache::has('product_show_san-pham-a-goc_vi'));
        $this->assertTrue(Cache::has('product_show_product-a-original_en'));
        $this->assertTrue(Cache::has('product_show_san-pham-b-ban-dau_vi'));

        $relatedBefore = collect($pdpABefore['related_products'])->firstWhere('id', $productB->id);
        $this->assertNotNull($relatedBefore);
        $this->assertSame('Sản phẩm B Ban Đầu', $relatedBefore['title']);
        $this->assertSame(200000, (int) $relatedBefore['price']);

        // Admin updates Product B (title, price, slug, seo_slug)
        $updateBRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $productB->id]),
            [
                'id' => $productB->id,
                'status' => Product::STATUS_ACTIVE,
                'title' => 'Sản phẩm B Đã Cập Nhật',
                'slug' => 'san-pham-b-da-cap-nhat',
                'seo_slug' => 'san-pham-b-da-cap-nhat',
                'sku' => 'TK-REL-B-NEW',
                'price' => 350000,
                'categories' => [['id' => $category->id]],
            ]
        );
        $this->assertTrue(in_array($updateBRes->getStatusCode(), [200, 302], true));
        $updateBRes->assertSessionHasNoErrors();

        // Product A's PDP caches AND Product B's old slug cache must be invalidated immediately
        $this->assertFalse(Cache::has('product_show_san-pham-a-goc_vi'));
        $this->assertFalse(Cache::has('product_show_product-a-original_en'));
        $this->assertFalse(Cache::has('product_show_san-pham-b-ban-dau_vi'));

        $pdpAAfter = $this->getJson('/san-pham/san-pham-a-goc')->assertStatus(200)->json();
        $relatedAfter = collect($pdpAAfter['related_products'])->firstWhere('id', $productB->id);
        $this->assertNotNull($relatedAfter);
        $this->assertSame('Sản phẩm B Đã Cập Nhật', $relatedAfter['title']);
        $this->assertSame(350000, (int) $relatedAfter['price']);
        $this->assertSame('san-pham-b-da-cap-nhat', $relatedAfter['slug']);
        $this->assertSame('/san-pham/san-pham-b-da-cap-nhat', $relatedAfter['url']);

        // Admin deactivates Product B -> Product A's cache invalidates and excludes Product B immediately
        $this->assertTrue(Cache::has('product_show_san-pham-a-goc_vi'));
        $deactivateBRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $productB->id]),
            [
                'id' => $productB->id,
                'status' => Product::STATUS_INACTIVE,
                'title' => 'Sản phẩm B Đã Cập Nhật',
                'slug' => 'san-pham-b-da-cap-nhat',
                'seo_slug' => 'san-pham-b-da-cap-nhat',
                'categories' => [['id' => $category->id]],
            ]
        );
        $this->assertTrue(in_array($deactivateBRes->getStatusCode(), [200, 302], true));
        $deactivateBRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('product_show_san-pham-a-goc_vi'));
        $pdpAAfterDeactivate = $this->getJson('/san-pham/san-pham-a-goc')->assertStatus(200)->json();
        $this->assertNull(collect($pdpAAfterDeactivate['related_products'])->firstWhere('id', $productB->id));
    }

    public function test_vietnamese_only_product_renders_valid_en_products_url_and_returns_http_200(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $category = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            [
                'product_category_id' => $category->id,
                'locale' => 'vi',
                'title' => 'Danh mục Chỉ Tiếng Việt',
                'slug' => 'danh-muc-chi-tieng-viet',
                'seo_slug' => 'danh-muc-chi-tieng-viet',
            ],
        ]);

        $viOnlyProduct = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-VI-ONLY-01',
            'position' => 1,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $viOnlyProduct->id,
                'locale' => 'vi',
                'title' => 'Sản phẩm Chỉ Có Bản Dịch Tiếng Việt',
                'slug' => 'san-pham-chi-co-ban-dich-tieng-viet',
                'seo_slug' => 'san-pham-chi-co-ban-dich-tieng-viet',
                'description' => 'Mô tả sản phẩm chỉ có tiếng Việt',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            'product_id' => $viOnlyProduct->id,
            'product_category_id' => $category->id,
        ]);

        ProductCategory::clearCategoryCaches();
        Product::clearProductCaches($viOnlyProduct);

        // Verify Model url & current_url fallback never produce /en/san-pham/{slug}
        $freshProduct = Product::with(['translations', 'categories'])->findOrFail($viOnlyProduct->id);
        $this->assertSame(
            'http://localhost/en/products/san-pham-chi-co-ban-dich-tieng-viet',
            $freshProduct->url['EN']
        );

        // 1. Verify /en/products lists the VI-only product with /en/products/{slug} URL
        $enIndexInertia = $this->get('/en/products', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ])->assertStatus(200);
        $enIndexData = $enIndexInertia->json();

        $this->assertSame('Product/Index', $enIndexData['component']);
        $this->assertCount(1, $enIndexData['props']['products']);

        $listedProduct = $enIndexData['props']['products'][0];
        $expectedPath = '/en/products/san-pham-chi-co-ban-dich-tieng-viet';
        $this->assertSame('san-pham-chi-co-ban-dich-tieng-viet', $listedProduct['slug']);
        $this->assertSame($expectedPath, parse_url($listedProduct['url'], PHP_URL_PATH));
        $this->assertStringNotContainsString('/en/san-pham/', $listedProduct['url']);

        // 2. Requesting that URL must return HTTP 200 (no 404 or redirect to /en/san-pham/{slug})
        $pdpHtml = $this->get($listedProduct['url']);
        $pdpHtml->assertStatus(200);

        $pdpInertia = $this->get($listedProduct['url'], [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ])->assertStatus(200);
        $pdpData = $pdpInertia->json();

        $this->assertSame('Product/Show', $pdpData['component']);
        $this->assertSame('Sản phẩm Chỉ Có Bản Dịch Tiếng Việt', $pdpData['props']['product']['title']);
        $this->assertSame('san-pham-chi-co-ban-dich-tieng-viet', $pdpData['props']['product']['slug']);
        $this->assertSame('Danh mục Chỉ Tiếng Việt', $pdpData['props']['product']['category']['title']);

        // Also confirm the legacy broken path /en/san-pham/{slug} is not used and 404s
        $this->get('/en/san-pham/san-pham-chi-co-ban-dich-tieng-viet')->assertStatus(404);
    }

    public function test_nav01_base_model_get_current_url_uses_config_app_url_and_handles_hosts_and_relative_paths(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $originalAppUrl = config('app.url');
        $originalLocale = app()->getLocale();

        try {
            config(['app.url' => 'https://example.test/']);
            app()->setLocale('en');

            $model = new class extends \App\Models\BaseModel {
                public array $mockUrls = [];

                public function getUrlAttribute(): array
                {
                    return $this->mockUrls;
                }
            };

            $model->mockUrls = ['VI' => 'https://example.test/tin-tuc/bai-1'];
            $this->assertSame('https://example.test/en/tin-tuc/bai-1', $model->current_url);

            $model->mockUrls = ['VI' => '/chinh-sach/bao-mat'];
            $this->assertSame('/en/chinh-sach/bao-mat', $model->current_url);

            $model->mockUrls = ['VI' => 'http://127.0.0.1:9000/tin-tuc/bai-1?ref=1'];
            $this->assertSame('http://127.0.0.1:9000/en/tin-tuc/bai-1?ref=1', $model->current_url);

            $model->mockUrls = [];
            $this->assertNull($model->current_url);
        } finally {
            config(['app.url' => $originalAppUrl]);
            app()->setLocale($originalLocale);
        }
    }

    public function test_nav02_product_empty_seo_slug_falls_back_to_slug_and_empty_both_returns_null_without_exception(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $originalLocale = app()->getLocale();

        try {
            app()->setLocale('vi');

            $productWithFallbackSlug = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'sku' => 'TK-FALLBACK-SLUG',
                'position' => 1,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $productWithFallbackSlug->id,
                    'locale' => 'vi',
                    'title' => 'Bánh răng chuẩn',
                    'slug' => 'banh-rang-chuan',
                    'seo_slug' => '',
                ],
                [
                    'product_id' => $productWithFallbackSlug->id,
                    'locale' => 'en',
                    'title' => 'Precision Gear',
                    'slug' => 'precision-gear',
                    'seo_slug' => '',
                ],
            ]);

            $freshFallback = Product::with('translations')->findOrFail($productWithFallbackSlug->id);
            $this->assertSame('banh-rang-chuan', $freshFallback->resolveProductSlug('vi'));
            $this->assertSame('precision-gear', $freshFallback->resolveProductSlug('en'));
            $this->assertSame('http://localhost/san-pham/banh-rang-chuan', $freshFallback->url['VI']);
            $this->assertSame('http://localhost/en/products/precision-gear', $freshFallback->url['EN']);
            $this->assertSame('http://localhost/san-pham/banh-rang-chuan', $freshFallback->current_url);
            $this->assertSame('banh-rang-chuan', $freshFallback->transform()['slug']);
            $this->assertSame('banh-rang-chuan', $freshFallback->transformCategory()['slug']);
            $this->assertSame('banh-rang-chuan', $freshFallback->transformDetails()['slug']);
            $this->assertSame('banh-rang-chuan', $freshFallback->transformGeneral()['slug']);

            $productWithEmptySlugs = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'sku' => 'TK-EMPTY-SLUG',
                'position' => 2,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $productWithEmptySlugs->id,
                    'locale' => 'vi',
                    'title' => 'Sản phẩm trống slug',
                    'slug' => '',
                    'seo_slug' => '',
                ],
            ]);

            $freshEmpty = Product::with('translations')->findOrFail($productWithEmptySlugs->id);
            $this->assertNull($freshEmpty->resolveProductSlug('vi'));
            $this->assertSame([], $freshEmpty->url);
            $this->assertNull($freshEmpty->current_url);
            $this->assertNull($freshEmpty->transform()['slug']);
            $this->assertNull($freshEmpty->transform()['url']);
            $this->assertNull($freshEmpty->transformCategory()['slug']);
            $this->assertNull($freshEmpty->transformDetails()['slug']);
            $this->assertNull($freshEmpty->transformGeneral()['slug']);
            $this->assertNull($freshEmpty->transformGeneral()['url']);

            $searchFallback = $this->getJson('/search-v2?keyword=TK-FALLBACK-SLUG&locale=vi')->assertStatus(200)->json();
            $this->assertTrue($searchFallback['success']);
            $this->assertSame('banh-rang-chuan', $searchFallback['products'][0]['slug']);
            $this->assertSame('http://localhost/san-pham/banh-rang-chuan', $searchFallback['products'][0]['url']);

            $searchEmpty = $this->getJson('/search-v2?keyword=TK-EMPTY-SLUG&locale=vi')->assertStatus(200)->json();
            $this->assertTrue($searchEmpty['success']);
            $this->assertNull($searchEmpty['products'][0]['slug']);
            $this->assertNull($searchEmpty['products'][0]['url']);
        } finally {
            app()->setLocale($originalLocale);
        }
    }

    public function test_nav03_brand_url_returns_empty_array_safely_without_pointing_to_products_show(): void
    {
        $this->clearAllCatalogTablesAndCaches();
        if (Schema::hasTable('brand_translations')) {
            DB::table('brand_translations')->delete();
        }
        if (Schema::hasTable('brands')) {
            DB::table('brands')->delete();
        }

        $originalLocale = app()->getLocale();

        try {
            app()->setLocale('vi');

            $brand = \App\Models\Brand\Brand::create([
                'status' => \App\Models\Brand\Brand::STATUS_ACTIVE,
                'position' => 1,
            ]);
            DB::table('brand_translations')->insert([
                [
                    'brand_id' => $brand->id,
                    'locale' => 'vi',
                    'title' => 'Thương hiệu A',
                    'slug' => 'thuong-hieu-a',
                    'seo_slug' => '',
                ],
                [
                    'brand_id' => $brand->id,
                    'locale' => 'en',
                    'title' => 'Brand A',
                    'slug' => '',
                    'seo_slug' => '',
                ],
            ]);

            $product = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'brand_id' => $brand->id,
                'sku' => 'TK-BRAND-01',
                'position' => 1,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $product->id,
                    'locale' => 'vi',
                    'title' => 'Sản phẩm Thương hiệu A',
                    'slug' => 'san-pham-thuong-hieu-a',
                    'seo_slug' => 'san-pham-thuong-hieu-a',
                ],
            ]);

            $freshProduct = Product::with('brand.translations')->findOrFail($product->id);
            $this->assertNotNull($freshProduct->brand);
            $this->assertSame([], $freshProduct->brand->url);
            $this->assertSame([], $freshProduct->brand->transform()['url']);
            $this->assertSame('thuong-hieu-a', $freshProduct->brand->transform()['slug']);
            $this->assertSame([], $freshProduct->brand->transformDetails()['url']);
            $this->assertSame([], $freshProduct->transformGeneral()['brand']['url']);
        } finally {
            app()->setLocale($originalLocale);
        }
    }

    public function test_nav04_orphaned_product_controller_galleries_method_is_removed(): void
    {
        $this->assertFalse(method_exists(\App\Http\Controllers\Frontend\ProductController::class, 'galleries'));
    }

    public function test_product_cache_invalidation_works_on_non_redis_file_cache_driver(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $originalDefaultDriver = config('cache.default');
        config(['cache.default' => 'file']);
        Cache::setDefaultDriver('file');
        Cache::forgetDriver('file');

        try {
            $category = ProductCategory::create([
                'parent_id' => 0,
                'status' => ProductCategory::STATUS_ACTIVE,
                'position' => 1,
            ]);
            DB::table('product_category_translations')->insert([
                [
                    'product_category_id' => $category->id,
                    'locale' => 'vi',
                    'title' => 'Danh mục File Cache Cũ',
                    'slug' => 'danh-muc-file-cache-cu',
                    'seo_slug' => 'danh-muc-file-cache-cu',
                ],
            ]);

            $productA = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'sku' => 'TK-FILE-A',
                'position' => 1,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $productA->id,
                    'locale' => 'vi',
                    'title' => 'Sản phẩm File A',
                    'slug' => 'san-pham-file-a',
                    'seo_slug' => 'san-pham-file-a',
                ],
            ]);
            DB::table('product_ref_categories')->insert([
                'product_id' => $productA->id,
                'product_category_id' => $category->id,
            ]);

            $productB = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'sku' => 'TK-FILE-B',
                'position' => 2,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $productB->id,
                    'locale' => 'vi',
                    'title' => 'Sản phẩm File B Cũ',
                    'slug' => 'san-pham-file-b-cu',
                    'seo_slug' => 'san-pham-file-b-cu',
                ],
            ]);
            DB::table('product_ref_categories')->insert([
                'product_id' => $productB->id,
                'product_category_id' => $category->id,
            ]);

            Product::clearProductCaches();

            // Pre-warm PDP cache on file driver
            $before = $this->getJson('/san-pham/san-pham-file-a')->assertStatus(200)->json();
            $this->assertTrue(Cache::store('file')->has('product_show_san-pham-file-a_vi'));
            $this->assertSame('Danh mục File Cache Cũ', $before['product']['category']['title']);
            $this->assertSame(
                'Sản phẩm File B Cũ',
                collect($before['related_products'])->firstWhere('id', $productB->id)['title']
            );

            // Update Product B directly via Eloquent -> invalidates Product A's file cache
            $productB = Product::with('translations')->findOrFail($productB->id);
            $productB->translate('vi')->title = 'Sản phẩm File B Mới';
            $productB->save();

            $this->assertFalse(Cache::store('file')->has('product_show_san-pham-file-a_vi'));
            $afterProdB = $this->getJson('/san-pham/san-pham-file-a')->assertStatus(200)->json();
            $this->assertSame(
                'Sản phẩm File B Mới',
                collect($afterProdB['related_products'])->firstWhere('id', $productB->id)['title']
            );

            // Update Category directly via Eloquent -> invalidates Product A's file cache
            $this->assertTrue(Cache::store('file')->has('product_show_san-pham-file-a_vi'));
            $category = ProductCategory::with('translations')->findOrFail($category->id);
            $category->translate('vi')->title = 'Danh mục File Cache Mới';
            $category->save();

            $this->assertFalse(Cache::store('file')->has('product_show_san-pham-file-a_vi'));
            $afterCat = $this->getJson('/san-pham/san-pham-file-a')->assertStatus(200)->json();
            $this->assertSame('Danh mục File Cache Mới', $afterCat['product']['category']['title']);
        } finally {
            Cache::store('file')->flush();
            config(['cache.default' => $originalDefaultDriver]);
            Cache::setDefaultDriver($originalDefaultDriver);
            Cache::forgetDriver('file');
        }
    }

    public function test_admin_product_position_ordering_and_pdp_tab2_tab3_badges_sync_and_clearing(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_pdp_tabs@jamstackvietnam.com'],
            [
                'name' => 'Admin PDP Tabs',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $category = ProductCategory::create([
            'parent_id' => 0,
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('product_category_translations')->insert([
            'product_category_id' => $category->id,
            'locale' => 'vi',
            'title' => 'Xích Công Nghiệp',
            'slug' => 'xich-cong-nghiep',
            'seo_slug' => 'xich-cong-nghiep',
        ]);

        // Pre-seed products with position null, 0, and 2 to verify position ASC ordering with null/0 trailing
        $pNull = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-POS-NULL',
            'position' => null,
        ]);
        DB::table('product_translations')->insert([
            'product_id' => $pNull->id,
            'locale' => 'vi',
            'title' => 'Sản phẩm Position Null',
            'slug' => 'san-pham-position-null',
            'seo_slug' => 'san-pham-position-null',
        ]);

        $pZero = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-POS-ZERO',
            'position' => 0,
        ]);
        DB::table('product_translations')->insert([
            'product_id' => $pZero->id,
            'locale' => 'vi',
            'title' => 'Sản phẩm Position 0',
            'slug' => 'san-pham-position-0',
            'seo_slug' => 'san-pham-position-0',
        ]);

        $pTwo = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-POS-02',
            'position' => 2,
        ]);
        DB::table('product_translations')->insert([
            'product_id' => $pTwo->id,
            'locale' => 'vi',
            'title' => 'Sản phẩm Position 2',
            'slug' => 'san-pham-position-2',
            'seo_slug' => 'san-pham-position-2',
        ]);

        foreach ([$pNull, $pZero, $pTwo] as $p) {
            DB::table('product_ref_categories')->insert([
                'product_id' => $p->id,
                'product_category_id' => $category->id,
            ]);
        }
        Product::clearProductCaches();

        // Warm index cache before Admin store
        $this->getJson('/san-pham')->assertStatus(200);
        $this->assertTrue(Cache::has('products_index_vi'));

        // Verify position validation rule rejects non-integer value
        $invalidPosRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store'),
            [
                'status' => Product::STATUS_ACTIVE,
                'title' => 'Sản phẩm Vị Trí Sai',
                'position' => 'khong-hop-le',
            ]
        );
        $invalidPosRes->assertSessionHasErrors('position');

        // 1. Admin POST stores a new product at position=1 with Tab 2, Tab 3, Badges & custom pdp_metadata key
        $storeRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store'),
            [
                'status' => Product::STATUS_ACTIVE,
                'position' => 1,
                'title' => 'Xích Băng Tải Vị Trí 1',
                'slug' => 'xich-bang-tai-vi-tri-1',
                'seo_slug' => 'xich-bang-tai-vi-tri-1',
                'sku' => 'TK-POS-01',
                'categories' => [['id' => $category->id]],
                'pdp_metadata' => [
                    'material_label' => 'Thép hợp kim 40Cr tôi cao tần',
                    'story_title' => 'Câu chuyện Tab 1',
                    'badges' => [
                        '  Chuẩn OEM / ISO  ',
                        ['label' => '  Cung ứng theo thông số  '],
                        '   ',
                    ],
                    'specs_title' => 'Thông số kỹ thuật mở rộng',
                    'specs_desc' => 'Mô tả chi tiết thông số cơ khí',
                    'specs_content' => '<p>Nội dung kỹ thuật HTML</p>',
                    'specs_cards' => [
                        [
                            'label' => '  Tải trọng  ',
                            'value' => '  120 kN  ',
                            'description' => '  Giới hạn kéo đứt tối thiểu  ',
                        ],
                        [
                            'label' => '  ',
                            'value' => '',
                            'description' => '',
                        ],
                    ],
                    'custom_title' => 'Gia công theo bản vẽ',
                    'custom_desc' => 'Quy trình tiếp nhận bản vẽ CAD/STEP',
                    'custom_content' => '<p>Hỗ trợ OEM/ODM theo yêu cầu</p>',
                    'custom_steps' => [
                        [
                            'step' => '  01  ',
                            'title' => '  Tiếp nhận bản vẽ  ',
                            'description' => '  Gửi file CAD hoặc mẫu thực tế  ',
                        ],
                        [
                            'step' => '',
                            'title' => '',
                            'description' => '',
                        ],
                    ],
                    'custom_cta_text' => 'Gửi bản vẽ nhận báo giá',
                ],
            ]
        );
        $this->assertTrue(in_array($storeRes->getStatusCode(), [200, 302], true));
        $storeRes->assertSessionHasNoErrors();

        // Index cache must be invalidated immediately
        $this->assertFalse(Cache::has('products_index_vi'));

        $created = Product::where('sku', 'TK-POS-01')->firstOrFail();
        $this->assertSame(1, (int) $created->position);

        // 2. Verify GET /admin/products/form/{id} returns position and full pdp_metadata
        $this->actingAs($admin, 'admin')->get("/admin/products/form/{$created->id}")->assertStatus(200);
        $formJson = $this->actingAs($admin, 'admin')
            ->getJson("/admin/products/form/{$created->id}")
            ->assertStatus(200)
            ->json();

        $this->assertSame(1, (int) $formJson['position']);
        $this->assertSame('Thép hợp kim 40Cr tôi cao tần', $formJson['pdp_metadata']['material_label']);
        $this->assertSame('Câu chuyện Tab 1', $formJson['pdp_metadata']['story_title']);
        $this->assertSame(['Chuẩn OEM / ISO', 'Cung ứng theo thông số'], $formJson['pdp_metadata']['badges']);
        $this->assertSame('Thông số kỹ thuật mở rộng', $formJson['pdp_metadata']['specs_title']);
        $this->assertSame('Mô tả chi tiết thông số cơ khí', $formJson['pdp_metadata']['specs_desc']);
        $this->assertSame('<p>Nội dung kỹ thuật HTML</p>', $formJson['pdp_metadata']['specs_content']);
        $this->assertSame([
            [
                'label' => 'Tải trọng',
                'value' => '120 kN',
                'description' => 'Giới hạn kéo đứt tối thiểu',
            ],
        ], $formJson['pdp_metadata']['specs_cards']);
        $this->assertSame('Gia công theo bản vẽ', $formJson['pdp_metadata']['custom_title']);
        $this->assertSame('Quy trình tiếp nhận bản vẽ CAD/STEP', $formJson['pdp_metadata']['custom_desc']);
        $this->assertSame('<p>Hỗ trợ OEM/ODM theo yêu cầu</p>', $formJson['pdp_metadata']['custom_content']);
        $this->assertSame([
            [
                'step' => '01',
                'title' => 'Tiếp nhận bản vẽ',
                'description' => 'Gửi file CAD hoặc mẫu thực tế',
            ],
        ], $formJson['pdp_metadata']['custom_steps']);
        $this->assertSame('Gửi bản vẽ nhận báo giá', $formJson['pdp_metadata']['custom_cta_text']);

        // 3. Verify /san-pham sorts by position ASC (1, 2, then null/0 trailing)
        $indexJson = $this->getJson('/san-pham')->assertStatus(200)->json();
        $orderedSlugs = array_column($indexJson['products'], 'slug');
        $this->assertSame('xich-bang-tai-vi-tri-1', $orderedSlugs[0]);
        $this->assertSame(1, (int) $indexJson['products'][0]['position']);
        $this->assertSame('san-pham-position-2', $orderedSlugs[1]);
        $this->assertSame(2, (int) $indexJson['products'][1]['position']);
        $this->assertEqualsCanonicalizing(
            ['san-pham-position-null', 'san-pham-position-0'],
            array_slice($orderedSlugs, 2)
        );

        // 4. Verify /san-pham/{slug} returns Tab 2, Tab 3, badges, position & custom key
        $showJson = $this->getJson('/san-pham/xich-bang-tai-vi-tri-1')->assertStatus(200)->json();
        $this->assertSame(1, (int) $showJson['product']['position']);
        $pdpMeta = $showJson['product']['pdp_metadata'];
        $this->assertSame('Thép hợp kim 40Cr tôi cao tần', $pdpMeta['material_label']);
        $this->assertSame('Câu chuyện Tab 1', $pdpMeta['story_title']);
        $this->assertSame(['Chuẩn OEM / ISO', 'Cung ứng theo thông số'], $pdpMeta['badges']);
        $this->assertSame('Thông số kỹ thuật mở rộng', $pdpMeta['specs_title']);
        $this->assertSame('Mô tả chi tiết thông số cơ khí', $pdpMeta['specs_desc']);
        $this->assertSame('<p>Nội dung kỹ thuật HTML</p>', $pdpMeta['specs_content']);
        $this->assertSame([
            [
                'label' => 'Tải trọng',
                'value' => '120 kN',
                'description' => 'Giới hạn kéo đứt tối thiểu',
            ],
        ], $pdpMeta['specs_cards']);
        $this->assertSame('Gia công theo bản vẽ', $pdpMeta['custom_title']);
        $this->assertSame('Quy trình tiếp nhận bản vẽ CAD/STEP', $pdpMeta['custom_desc']);
        $this->assertSame('<p>Hỗ trợ OEM/ODM theo yêu cầu</p>', $pdpMeta['custom_content']);
        $this->assertSame([
            [
                'step' => '01',
                'title' => 'Tiếp nhận bản vẽ',
                'description' => 'Gửi file CAD hoặc mẫu thực tế',
            ],
        ], $pdpMeta['custom_steps']);
        $this->assertSame('Gửi bản vẽ nhận báo giá', $pdpMeta['custom_cta_text']);
        $this->assertTrue(Cache::has('product_show_xich-bang-tai-vi-tri-1_vi'));
        $this->assertTrue(Cache::has('products_index_vi'));

        // 5. Admin clears Tab 2, Tab 3, badges & updates position=3 WITHOUT resending material_label/story_title
        $clearRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $created->id]),
            [
                'id' => $created->id,
                'status' => Product::STATUS_ACTIVE,
                'position' => 3,
                'title' => 'Xích Băng Tải Vị Trí 1',
                'slug' => 'xich-bang-tai-vi-tri-1',
                'seo_slug' => 'xich-bang-tai-vi-tri-1',
                'sku' => 'TK-POS-01',
                'categories' => [['id' => $category->id]],
                'pdp_metadata' => [
                    'specs_title' => '',
                    'specs_desc' => '   ',
                    'specs_content' => '',
                    'specs_cards' => [],
                    'custom_title' => '',
                    'custom_desc' => '',
                    'custom_content' => '',
                    'custom_steps' => [],
                    'custom_cta_text' => '',
                    'badges' => [],
                ],
            ]
        );
        $this->assertTrue(in_array($clearRes->getStatusCode(), [200, 302], true));
        $clearRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('products_index_vi'));
        $this->assertFalse(Cache::has('product_show_xich-bang-tai-vi-tri-1_vi'));

        // Verify raw DB JSON removed cleared keys completely while preserving untouched keys
        $rawMetaAfterClear = json_decode((string) DB::table('products')->where('id', $created->id)->value('pdp_metadata'), true);
        $this->assertIsArray($rawMetaAfterClear);
        foreach ([
            'specs_title',
            'specs_desc',
            'specs_content',
            'specs_cards',
            'custom_title',
            'custom_desc',
            'custom_content',
            'custom_steps',
            'custom_cta_text',
            'badges',
        ] as $clearedKey) {
            $this->assertArrayNotHasKey($clearedKey, $rawMetaAfterClear);
        }
        $this->assertSame('Thép hợp kim 40Cr tôi cao tần', $rawMetaAfterClear['material_label']);
        $this->assertSame('Câu chuyện Tab 1', $rawMetaAfterClear['story_title']);

        // Verify re-ordered index puts position=2 before position=3, and null/0 trailing
        $indexAfterUpdate = $this->getJson('/san-pham')->assertStatus(200)->json();
        $orderedAfterUpdate = array_column($indexAfterUpdate['products'], 'slug');
        $this->assertSame('san-pham-position-2', $orderedAfterUpdate[0]);
        $this->assertSame('xich-bang-tai-vi-tri-1', $orderedAfterUpdate[1]);
        $this->assertSame(3, (int) $indexAfterUpdate['products'][1]['position']);

        // Verify PDP response reflects cleared Tab 2/Tab 3/badges and preserved custom keys
        $showAfterClear = $this->getJson('/san-pham/xich-bang-tai-vi-tri-1')->assertStatus(200)->json();
        $clearedMeta = $showAfterClear['product']['pdp_metadata'];
        $this->assertNull($clearedMeta['specs_title']);
        $this->assertNull($clearedMeta['specs_desc']);
        $this->assertNull($clearedMeta['specs_content']);
        $this->assertSame([], $clearedMeta['specs_cards']);
        $this->assertNull($clearedMeta['custom_title']);
        $this->assertNull($clearedMeta['custom_desc']);
        $this->assertNull($clearedMeta['custom_content']);
        $this->assertSame([], $clearedMeta['custom_steps']);
        $this->assertNull($clearedMeta['custom_cta_text']);
        $this->assertSame([], $clearedMeta['badges']);
        $this->assertSame('Thép hợp kim 40Cr tôi cao tần', $clearedMeta['material_label']);
        $this->assertSame('Câu chuyện Tab 1', $clearedMeta['story_title']);

        // 6. Clearing all remaining keys writes NULL into products.pdp_metadata (Zero-Fallback)
        $clearAllRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $created->id]),
            [
                'id' => $created->id,
                'status' => Product::STATUS_ACTIVE,
                'position' => null,
                'title' => 'Xích Băng Tải Vị Trí 1',
                'slug' => 'xich-bang-tai-vi-tri-1',
                'seo_slug' => 'xich-bang-tai-vi-tri-1',
                'sku' => 'TK-POS-01',
                'categories' => [['id' => $category->id]],
                'pdp_metadata' => [
                    'material_label' => '',
                    'story_title' => '',
                ],
            ]
        );
        $this->assertTrue(in_array($clearAllRes->getStatusCode(), [200, 302], true));
        $clearAllRes->assertSessionHasNoErrors();

        $this->assertNull(DB::table('products')->where('id', $created->id)->value('pdp_metadata'));
        $this->assertNull(DB::table('products')->where('id', $created->id)->value('position'));
    }

    public function test_pdp_settings_defaults_breadcrumbs_faqs_related_documents_and_admin_override(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        if (!Schema::hasTable('posts')) {
            $this->artisan('migrate', [
                '--path' => 'database/migrations/blog',
                '--realpath' => false,
            ]);
        }

        if (Schema::hasTable('post_ref_categories')) {
            DB::table('post_ref_categories')->delete();
        }
        if (Schema::hasTable('product_ref_posts')) {
            DB::table('product_ref_posts')->delete();
        }
        DB::table('post_translations')->delete();
        DB::table('posts')->delete();
        if (Schema::hasTable('brand_translations')) {
            DB::table('brand_translations')->delete();
        }
        if (Schema::hasTable('brands')) {
            DB::table('brands')->delete();
        }

        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_pdp_cms@jamstackvietnam.com'],
            [
                'name' => 'Admin PDP CMS',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        // 1. Seed PDP defaults twice to verify idempotency and non-overwrite on Admin edits
        $this->seed(\Database\Seeders\ThanhKhangProductDetailSettingsSeeder::class);
        $this->seed(\Database\Seeders\ThanhKhangProductDetailSettingsSeeder::class);
        $this->assertSame(12, DB::table('settings')->where('group', 'general')->where('name', 'like', 'product_detail_%')->count());

        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'product_detail_origin')
            ->update(['val' => 'Đức']);
        $this->seed(\Database\Seeders\ThanhKhangProductDetailSettingsSeeder::class);
        $this->assertSame(
            'Đức',
            DB::table('settings')->where('group', 'general')->where('name', 'product_detail_origin')->value('val')
        );
        DB::table('settings')
            ->where('group', 'general')
            ->where('name', 'product_detail_origin')
            ->update(['val' => 'Trung Quốc']);
        Config::clearBrandCaches();

        // 2. Build 2-tier category tree + Brand + Linked Post/Document + 6 Products + 2 FAQs
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
            ],
            [
                'product_category_id' => $rootCat->id,
                'locale' => 'en',
                'title' => 'Escalator Spare Parts',
                'slug' => 'escalator-spare-parts',
                'seo_slug' => 'escalator-spare-parts',
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
            ],
            [
                'product_category_id' => $childCat->id,
                'locale' => 'en',
                'title' => 'Escalator Chains',
                'slug' => 'escalator-chains',
                'seo_slug' => 'escalator-chains',
            ],
        ]);

        $brand = \App\Models\Brand\Brand::create([
            'status' => \App\Models\Brand\Brand::STATUS_ACTIVE,
            'position' => 1,
        ]);
        DB::table('brand_translations')->insert([
            ['brand_id' => $brand->id, 'locale' => 'vi', 'title' => 'Universal Chains OEM', 'slug' => 'universal-chains-oem'],
            ['brand_id' => $brand->id, 'locale' => 'en', 'title' => 'Universal Chains OEM', 'slug' => 'universal-chains-oem'],
        ]);

        $mainProduct = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'brand_id' => $brand->id,
            'sku' => 'TK-PDP-MAIN',
            'position' => 1,
            'images' => [
                ['path' => '/images/products/main.webp', 'alt' => 'Main'],
                ['path' => '/images/products/main-drawing-vi.webp', 'alt' => 'Drawing'],
            ],
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $mainProduct->id,
                'locale' => 'vi',
                'title' => 'Xích Bậc Thang Cuốn TL133',
                'slug' => 'xich-bac-thang-cuon-tl133',
                'seo_slug' => 'xich-bac-thang-cuon-tl133',
                'description' => 'Mô tả TL133 VI',
            ],
            [
                'product_id' => $mainProduct->id,
                'locale' => 'en',
                'title' => 'Escalator Step Chain TL133',
                'slug' => 'escalator-step-chain-tl133',
                'seo_slug' => 'escalator-step-chain-tl133',
                'description' => 'TL133 EN description',
            ],
        ]);
        DB::table('product_ref_categories')->insert([
            ['product_id' => $mainProduct->id, 'product_category_id' => $rootCat->id],
            ['product_id' => $mainProduct->id, 'product_category_id' => $childCat->id],
        ]);

        // Create 6 sibling active products to confirm related_products returns > 4 (up to 8)
        for ($i = 1; $i <= 6; $i++) {
            $rel = Product::create([
                'status' => Product::STATUS_ACTIVE,
                'sku' => 'TK-REL-' . $i,
                'position' => $i + 1,
            ]);
            DB::table('product_translations')->insert([
                [
                    'product_id' => $rel->id,
                    'locale' => 'vi',
                    'title' => 'Sản phẩm liên quan ' . $i,
                    'slug' => 'san-pham-lien-quan-' . $i,
                    'seo_slug' => 'san-pham-lien-quan-' . $i,
                ],
                [
                    'product_id' => $rel->id,
                    'locale' => 'en',
                    'title' => 'Related Product ' . $i,
                    'slug' => 'related-product-' . $i,
                    'seo_slug' => 'related-product-' . $i,
                ],
            ]);
            DB::table('product_ref_categories')->insert([
                'product_id' => $rel->id,
                'product_category_id' => $childCat->id,
            ]);
        }

        // Link a DOCUMENT post to mainProduct via product_ref_posts
        $docPost = \App\Models\Post\Post::create([
            'type' => \App\Models\Post\Post::TYPE_DOCUMENT,
            'status' => \App\Models\Post\Post::STATUS_ACTIVE,
            'published_at' => now()->subMinute(),
            'position' => 1,
            'download' => '/static/docs/tl133-datasheet.pdf',
            'image' => ['path' => '/images/docs/tl133.webp', 'alt' => 'TL133 Doc'],
        ]);
        DB::table('post_translations')->insert([
            [
                'post_id' => $docPost->id,
                'locale' => 'vi',
                'title' => 'Datasheet Xích TL133',
                'slug' => 'datasheet-xich-tl133',
                'seo_slug' => 'datasheet-xich-tl133',
                'description' => 'Tài liệu kỹ thuật TL133',
            ],
            [
                'post_id' => $docPost->id,
                'locale' => 'en',
                'title' => 'TL133 Technical Datasheet',
                'slug' => 'tl133-technical-datasheet',
                'seo_slug' => 'tl133-technical-datasheet',
                'description' => 'TL133 datasheet EN',
            ],
        ]);
        DB::table('product_ref_posts')->insert([
            'product_id' => $mainProduct->id,
            'post_id' => $docPost->id,
        ]);

        // Seed 2 FAQs (second has blank EN to verify non-empty fallback)
        $faq1 = \App\Models\Post\Post::create([
            'type' => \App\Models\Post\Post::TYPE_FAQ,
            'status' => \App\Models\Post\Post::STATUS_ACTIVE,
            'published_at' => now()->subMinute(),
            'position' => 1,
        ]);
        DB::table('post_translations')->insert([
            ['post_id' => $faq1->id, 'locale' => 'vi', 'title' => 'Câu hỏi 1 VI?', 'slug' => 'cau-hoi-1-vi', 'content' => '<p>Trả lời 1 VI</p>'],
            ['post_id' => $faq1->id, 'locale' => 'en', 'title' => 'Question 1 EN?', 'slug' => 'question-1-en', 'content' => '<p>Answer 1 EN</p>'],
        ]);
        $faq2 = \App\Models\Post\Post::create([
            'type' => \App\Models\Post\Post::TYPE_FAQ,
            'status' => \App\Models\Post\Post::STATUS_ACTIVE,
            'published_at' => now()->subMinute(),
            'position' => 2,
        ]);
        DB::table('post_translations')->insert([
            ['post_id' => $faq2->id, 'locale' => 'vi', 'title' => 'Câu hỏi 2 VI?', 'slug' => 'cau-hoi-2-vi', 'description' => 'Trả lời 2 VI'],
        ]);

        Product::clearProductCaches();
        \App\Models\Post\Post::clearPostCaches();

        // 3. Verify VI response props
        $viRes = $this->get('/san-pham/xich-bac-thang-cuon-tl133', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ])->assertStatus(200)->json();

        $this->assertSame('Product/Show', $viRes['component']);
        $viProps = $viRes['props'];

        $this->assertSame([
            'brand' => 'Universal Chains',
            'origin' => 'Trung Quốc',
            'warranty' => '12 tháng',
            'condition' => 'Full box',
            'stock_status' => 'Còn hàng',
            'technical_support' => '24/7',
        ], $viProps['pdpDefaults']);

        $this->assertCount(2, $viProps['product']['breadcrumbs']);
        $this->assertSame('Linh kiện thang cuốn', $viProps['product']['breadcrumbs'][0]['title']);
        $this->assertSame('linh-kien-thang-cuon', $viProps['product']['breadcrumbs'][0]['slug']);
        $this->assertSame('Xích thang cuốn', $viProps['product']['breadcrumbs'][1]['title']);
        $this->assertSame('xich-thang-cuon', $viProps['product']['breadcrumbs'][1]['slug']);

        $this->assertSame('Universal Chains OEM', $viProps['product']['brand']['title']);
        $this->assertSame('Universal Chains OEM', $viProps['product']['brand_name']);
        $this->assertNull($viProps['product']['origin']);
        $this->assertCount(1, $viProps['product']['posts']);
        $this->assertSame('Datasheet Xích TL133', $viProps['product']['posts'][0]['title']);
        $this->assertSame('/static/docs/tl133-datasheet.pdf', $viProps['product']['posts'][0]['url']);
        $this->assertCount(6, $viProps['related_products']);

        $this->assertCount(2, $viProps['faqs']);
        $this->assertSame('Câu hỏi 1 VI?', $viProps['faqs'][0]['question']);
        $this->assertSame('Trả lời 1 VI', $viProps['faqs'][0]['answer']);
        $this->assertNull($viProps['ctaBanner']);

        // 4. Verify EN response props & FAQ fallback
        $enRes = $this->get('/en/products/escalator-step-chain-tl133', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ])->assertStatus(200)->json();
        $enProps = $enRes['props'];

        $this->assertSame([
            'brand' => 'Universal Chains',
            'origin' => 'China',
            'warranty' => '12 months',
            'condition' => 'Full box',
            'stock_status' => 'In stock',
            'technical_support' => '24/7',
        ], $enProps['pdpDefaults']);
        $this->assertSame('Escalator Spare Parts', $enProps['product']['breadcrumbs'][0]['title']);
        $this->assertSame('Escalator Chains', $enProps['product']['breadcrumbs'][1]['title']);
        $this->assertSame('Question 1 EN?', $enProps['faqs'][0]['question']);
        $this->assertSame('Câu hỏi 2 VI?', $enProps['faqs'][1]['question']);
        $this->assertSame('Trả lời 2 VI', $enProps['faqs'][1]['answer']);
        $this->assertSame('TL133 Technical Datasheet', $enProps['product']['posts'][0]['title']);

        // 5. Admin overrides pdp_metadata on product & updates General Settings
        $this->actingAs($admin, 'admin')->post(
            route('vi.admin.products.store', ['id' => $mainProduct->id]),
            [
                'id' => $mainProduct->id,
                'status' => Product::STATUS_ACTIVE,
                'title' => 'Xích Bậc Thang Cuốn TL133',
                'slug' => 'xich-bac-thang-cuon-tl133',
                'seo_slug' => 'xich-bac-thang-cuon-tl133',
                'categories' => [['id' => $childCat->id]],
                'pdp_metadata' => [
                    'brand_name' => 'Thương hiệu Riêng',
                    'origin' => 'Nhật Bản',
                    'warranty' => '24 tháng',
                    'condition' => 'Mới 100%',
                    'stock_status' => 'Sẵn kho HCM',
                    'technical_support' => 'Kỹ sư 24/7',
                ],
            ]
        )->assertSessionHasNoErrors();

        $this->actingAs($admin, 'admin')->post(
            route('admin.settings.store', ['id' => 'general']),
            [
                'id' => 'general',
                'general_business_name' => 'Thành Khang Tecsat',
                'product_detail_origin' => 'Đài Loan',
                'product_detail_origin_en' => 'Taiwan',
            ]
        )->assertSessionHasNoErrors();

        $afterOverride = $this->getJson('/san-pham/xich-bac-thang-cuon-tl133')->assertStatus(200)->json();
        $this->assertSame('Thương hiệu Riêng', $afterOverride['product']['brand_name']);
        $this->assertSame('Nhật Bản', $afterOverride['product']['origin']);
        $this->assertSame('24 tháng', $afterOverride['product']['warranty']);
        $this->assertSame('Mới 100%', $afterOverride['product']['condition']);
        $this->assertSame('Sẵn kho HCM', $afterOverride['product']['stock_status']);
        $this->assertSame('Kỹ sư 24/7', $afterOverride['product']['technical_support']);
        $this->assertSame('Đài Loan', $afterOverride['pdpDefaults']['origin']);

        // 6. When BANNER_GLOBAL_CTA exists, ctaBanner is normalized; when DB has no FAQs or settings, Zero-Fallback applies
        $ctaSlider = Slider::create([
            'status' => Slider::STATUS_ACTIVE,
            'position_display' => 'BANNER_GLOBAL_CTA',
            'position_sort' => 1,
        ]);
        DB::table('slider_translations')->insert([
            'slider_id' => $ctaSlider->id,
            'locale' => 'vi',
            'title' => 'Tư vấn kỹ thuật B2B',
            'description' => 'Liên hệ đội ngũ kỹ sư',
            'primary_cta_text' => 'Liên hệ ngay',
            'primary_cta_link' => '/lien-he',
            'image' => json_encode(['path' => '/assets/images/cta-bg.webp', 'alt' => 'CTA']),
        ]);
        Slider::clearSliderCaches();

        $withBanner = $this->getJson('/san-pham/xich-bac-thang-cuon-tl133')->assertStatus(200)->json();
        $this->assertSame('Tư vấn kỹ thuật B2B', $withBanner['ctaBanner']['title']);
        $this->assertSame('/assets/images/cta-bg.webp', $withBanner['ctaBanner']['image']['url']);

        // Clear FAQs & settings -> Zero-Fallback returns [] and nulls
        DB::table('posts')->where('type', \App\Models\Post\Post::TYPE_FAQ)->delete();
        DB::table('settings')->delete();
        Config::clearBrandCaches();
        \App\Models\Post\Post::clearPostCaches();

        $emptyExtras = $this->getJson('/san-pham/xich-bac-thang-cuon-tl133')->assertStatus(200)->json();
        $this->assertSame([], $emptyExtras['faqs']);
        $this->assertSame([
            'brand' => null,
            'origin' => null,
            'warranty' => null,
            'condition' => null,
            'stock_status' => null,
            'technical_support' => null,
        ], $emptyExtras['pdpDefaults']);
    }

    public function test_global_cta_banner_seeder_is_idempotent_preserves_admin_edits_and_serves_bilingual_pdp(): void
    {
        $this->clearAllCatalogTablesAndCaches();

        // Run seeder twice to verify strict idempotency
        $this->seed(\Database\Seeders\ThanhKhangGlobalCtaBannerSeeder::class);
        $this->seed(\Database\Seeders\ThanhKhangGlobalCtaBannerSeeder::class);
        $this->assertSame(1, Slider::where('position_display', 'BANNER_GLOBAL_CTA')->count());

        // Verify seeder does NOT overwrite Admin CMS edits
        $ctaSlider = Slider::where('position_display', 'BANNER_GLOBAL_CTA')->firstOrFail();
        DB::table('slider_translations')
            ->where('slider_id', $ctaSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'TƯ VẤN CHỈNH SỬA CMS']);
        $this->seed(\Database\Seeders\ThanhKhangGlobalCtaBannerSeeder::class);
        $this->assertSame(
            'TƯ VẤN CHỈNH SỬA CMS',
            DB::table('slider_translations')->where('slider_id', $ctaSlider->id)->where('locale', 'vi')->value('title')
        );

        // Restore seeded title for standard bilingual PDP payload assertions
        DB::table('slider_translations')
            ->where('slider_id', $ctaSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'CẦN TƯ VẤN HOẶC BÁO GIÁ NHANH ?']);
        Slider::clearSliderCaches();

        $product = Product::create([
            'status' => Product::STATUS_ACTIVE,
            'sku' => 'TK-CTA-01',
            'position' => 1,
        ]);
        DB::table('product_translations')->insert([
            [
                'product_id' => $product->id,
                'locale' => 'vi',
                'title' => 'Xích Bậc Thang Cuốn CTA',
                'slug' => 'xich-bac-thang-cuon-cta',
                'seo_slug' => 'xich-bac-thang-cuon-cta',
            ],
            [
                'product_id' => $product->id,
                'locale' => 'en',
                'title' => 'Escalator Chain CTA',
                'slug' => 'escalator-chain-cta',
                'seo_slug' => 'escalator-chain-cta',
            ],
        ]);
        Product::clearProductCaches($product);

        $viPdp = $this->getJson('/san-pham/xich-bac-thang-cuon-cta')->assertStatus(200)->json();
        $this->assertSame('CẦN TƯ VẤN HOẶC BÁO GIÁ NHANH ?', $viPdp['ctaBanner']['title']);
        $this->assertSame('Gửi yêu cầu và chúng tôi sẽ phản hồi trong vòng 2 giờ làm việc', $viPdp['ctaBanner']['description']);
        $this->assertSame('Gọi Hotline: 0967 732 665', $viPdp['ctaBanner']['primary_cta_text']);
        $this->assertSame('tel:0967732665', $viPdp['ctaBanner']['primary_cta_link']);
        $this->assertSame('Liên hệ qua Zalo', $viPdp['ctaBanner']['secondary_cta_text']);
        $this->assertSame('https://zalo.me/0967732665', $viPdp['ctaBanner']['secondary_cta_link']);
        $this->assertSame('/assets/images/banners/global-cta-desktop.webp', $viPdp['ctaBanner']['image']['url']);
        $this->assertSame('/assets/images/banners/global-cta-mobile.webp', $viPdp['ctaBanner']['image_mobile']['url']);

        $enPdp = $this->getJson('/en/products/escalator-chain-cta')->assertStatus(200)->json();
        $this->assertSame('NEED CONSULTATION OR A QUICK QUOTE?', $enPdp['ctaBanner']['title']);
        $this->assertSame('Send your request and we will respond within 2 business hours', $enPdp['ctaBanner']['description']);
        $this->assertSame('Call Hotline: 0967 732 665', $enPdp['ctaBanner']['primary_cta_text']);
        $this->assertSame('tel:0967732665', $enPdp['ctaBanner']['primary_cta_link']);
        $this->assertSame('Contact via Zalo', $enPdp['ctaBanner']['secondary_cta_text']);
        $this->assertSame('https://zalo.me/0967732665', $enPdp['ctaBanner']['secondary_cta_link']);
        $this->assertSame('/assets/images/banners/global-cta-desktop.webp', $enPdp['ctaBanner']['image']['url']);
        $this->assertSame('/assets/images/banners/global-cta-mobile.webp', $enPdp['ctaBanner']['image_mobile']['url']);
    }
}
