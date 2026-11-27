<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Policy\Policy;
use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\Slider\Models\Slider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DatabaseSeederAutomationTest extends TestCase
{
    use RefreshDatabase;

    protected const EXPECTED_DATABASE_SEEDER_SLIDERS = [
        'BANNER_PRODUCT_HERO' => 1,
        'HOME_SLIDER' => 3,
        'HOME_ABOUT_GALLERY' => 3,
        'BANNER_HOME_CATEGORIES_CTA' => 1,
        'BANNER_HOME_TRUST' => 1,
        'HOME_TRUST_PILLARS' => 5,
        'BANNER_HOME_PROCESS' => 1,
        'HOME_PROCESS_STEPS' => 4,
        'BANNER_ABOUT_HERO' => 1,
        'ABOUT_STORY_GALLERY' => 3,
        'BANNER_ABOUT_MISSION_VISION' => 1,
        'ABOUT_MISSION_VISION_ITEMS' => 2,
        'ABOUT_STATS_ITEMS' => 4,
        'ABOUT_COMPANION_CARDS' => 2,
        'BANNER_DOCUMENT_HERO' => 1,
        'BANNER_DOCUMENT_CTA' => 1,
        'BANNER_GLOBAL_CTA' => 1,
    ];

    protected function assertStrictSqliteInMemoryGuard(): void
    {
        $defaultConnection = config('database.default');
        $sqliteDatabase = config('database.connections.sqlite.database');
        $driverName = DB::connection()->getDriverName();
        $databaseName = DB::connection()->getDatabaseName();

        if (
            $defaultConnection !== 'sqlite'
            || $sqliteDatabase !== ':memory:'
            || $driverName !== 'sqlite'
            || $databaseName !== ':memory:'
        ) {
            $this->fail(sprintf(
                'SAFETY ABORT: DatabaseSeederAutomationTest is strictly restricted to sqlite :memory: (got default=%s, sqlite.database=%s, driver=%s, db=%s).',
                (string) $defaultConnection,
                (string) $sqliteDatabase,
                (string) $driverName,
                (string) $databaseName
            ));
        }
    }

    protected function refreshDatabase()
    {
        $this->assertStrictSqliteInMemoryGuard();

        $this->usingInMemoryDatabase()
            ? $this->refreshInMemoryDatabase()
            : $this->refreshTestDatabase();
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->assertStrictSqliteInMemoryGuard();

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

    protected function wipeSeededTablesInSqliteMemory(): void
    {
        $this->assertStrictSqliteInMemoryGuard();

        $tables = [
            'model_has_permissions',
            'model_has_roles',
            'role_has_permissions',
            'permissions',
            'roles',
            'admins',
            'users',
            'settings',
            'policy_translations',
            'policies',
            'related_products',
            'product_ref_categories',
            'product_translations',
            'products',
            'product_category_translations',
            'product_categories',
            'slider_translations',
            'sliders',
            'post_ref_categories',
            'post_translations',
            'posts',
            'post_category_translations',
            'post_categories',
        ];

        Schema::disableForeignKeyConstraints();
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                DB::table($table)->delete();
            }
        }
        Schema::enableForeignKeyConstraints();

        Cache::flush();
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
    }

    protected function snapshotCounts(): array
    {
        return [
            'admins' => Admin::count(),
            'users' => User::count(),
            'roles' => Role::count(),
            'permissions' => Permission::count(),
            'settings' => Setting::count(),
            'policies' => Policy::count(),
            'product_categories' => ProductCategory::count(),
            'products' => Product::count(),
            'sliders' => Slider::count(),
            'slider_positions' => Slider::query()->distinct()->count('position_display'),
            'posts_total' => Post::count(),
            'posts_post' => Post::where('type', Post::TYPE_POST)->count(),
            'posts_faq' => Post::where('type', Post::TYPE_FAQ)->count(),
            'posts_document' => Post::where('type', Post::TYPE_DOCUMENT)->count(),
        ];
    }

    public function test_database_seeder_from_clean_tables_populates_exact_counts_and_is_idempotent_on_rerun(): void
    {
        $this->assertStrictSqliteInMemoryGuard();

        // Reset data seeded by migrations so DatabaseSeeder itself is verified from empty tables
        $this->wipeSeededTablesInSqliteMemory();

        // 1st run of DatabaseSeeder
        $this->seed(DatabaseSeeder::class);

        $firstCounts = $this->snapshotCounts();

        $this->assertSame(1, $firstCounts['admins'], 'Expected 1 admin');
        $this->assertSame(2, $firstCounts['users'], 'Expected 2 users');
        $this->assertSame(2, $firstCounts['roles'], 'Expected 2 roles (Super Admin, Editor)');
        $this->assertGreaterThan(0, $firstCounts['permissions'], 'Expected admin permissions to be populated');
        $this->assertSame(96, $firstCounts['settings'], 'Expected 96 settings rows');
        $this->assertSame(6, $firstCounts['policies'], 'Expected 6 policies rows');
        $this->assertSame(26, $firstCounts['product_categories'], 'Expected 26 product_categories rows');
        $this->assertSame(103, $firstCounts['products'], 'Expected 103 products rows');
        $this->assertSame(35, $firstCounts['sliders'], 'Expected 35 sliders rows from DatabaseSeeder');
        $this->assertSame(17, $firstCounts['slider_positions'], 'Expected 17 slider position_display keys from DatabaseSeeder');
        $this->assertSame(42, $firstCounts['posts_total'], 'Expected 42 total posts (3 POST + 6 FAQ + 33 DOCUMENT)');
        $this->assertSame(3, $firstCounts['posts_post'], 'Expected 3 POST articles');
        $this->assertSame(6, $firstCounts['posts_faq'], 'Expected 6 FAQ posts');
        $this->assertSame(33, $firstCounts['posts_document'], 'Expected 33 DOCUMENT posts');

        $actualPositions = Slider::query()
            ->select('position_display', DB::raw('COUNT(*) as total'))
            ->groupBy('position_display')
            ->pluck('total', 'position_display')
            ->map(fn ($v) => (int) $v)
            ->all();

        ksort($actualPositions);
        $expectedPositions = self::EXPECTED_DATABASE_SEEDER_SLIDERS;
        ksort($expectedPositions);
        $this->assertSame($expectedPositions, $actualPositions);

        // 2nd run of DatabaseSeeder (Idempotency check — must succeed without exceptions or duplicates)
        $this->seed(DatabaseSeeder::class);

        $secondCounts = $this->snapshotCounts();
        $this->assertSame($firstCounts, $secondCounts, 'Second DatabaseSeeder run altered row counts');
    }

    public function test_fresh_migration_plus_database_seeder_is_idempotent_including_product_hero_migration_banner(): void
    {
        $this->assertStrictSqliteInMemoryGuard();

        Cache::flush();

        // Run DatabaseSeeder directly after fresh RefreshDatabase migrations (1st run)
        $this->seed(DatabaseSeeder::class);
        $firstCounts = $this->snapshotCounts();

        $this->assertSame(1, $firstCounts['admins']);
        $this->assertSame(2, $firstCounts['users']);
        $this->assertSame(2, $firstCounts['roles']);
        $this->assertGreaterThan(0, $firstCounts['permissions']);
        $this->assertSame(96, $firstCounts['settings']);
        $this->assertSame(6, $firstCounts['policies']);
        $this->assertSame(26, $firstCounts['product_categories']);
        $this->assertSame(103, $firstCounts['products']);
        $this->assertSame(42, $firstCounts['posts_total']);
        $this->assertSame(3, $firstCounts['posts_post']);
        $this->assertSame(6, $firstCounts['posts_faq']);
        $this->assertSame(33, $firstCounts['posts_document']);

        // Verify all 17 position_display groups (total 35 sliders) remain idempotent across migration + DatabaseSeeder
        $actualPositions = Slider::query()
            ->select('position_display', DB::raw('COUNT(*) as total'))
            ->groupBy('position_display')
            ->pluck('total', 'position_display')
            ->map(fn ($v) => (int) $v)
            ->all();

        ksort($actualPositions);
        $expectedPositions = self::EXPECTED_DATABASE_SEEDER_SLIDERS;
        ksort($expectedPositions);
        $this->assertSame($expectedPositions, $actualPositions);
        $this->assertSame(35, $firstCounts['sliders']);
        $this->assertSame(17, $firstCounts['slider_positions']);

        // 2nd run of DatabaseSeeder on top of fresh migration + 1st run (Idempotency check)
        $this->seed(DatabaseSeeder::class);
        $secondCounts = $this->snapshotCounts();
        $this->assertSame($firstCounts, $secondCounts, 'Second DatabaseSeeder run altered row counts');
    }

    public function test_seeded_frontend_routes_return_http_200_with_populated_inertia_props(): void
    {
        $this->assertStrictSqliteInMemoryGuard();

        Cache::flush();
        $this->seed(DatabaseSeeder::class);
        Config::clearBrandCaches();
        Slider::clearSliderCaches();
        ProductCategory::clearCategoryCaches();
        Post::clearPostCaches();
        Policy::clearPolicyCaches();

        $inertiaHeaders = [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ];

        // 1. GET /
        $homeRes = $this->get('/', $inertiaHeaders);
        $homeRes->assertStatus(200);
        $homeJson = $homeRes->json();
        $this->assertSame('Home/Index', $homeJson['component']);
        $this->assertCount(3, $homeJson['props']['sliders']);
        $this->assertCount(4, $homeJson['props']['categories']);
        $this->assertCount(3, $homeJson['props']['featured_posts']);
        $this->assertNotEmpty($homeJson['props']['aboutSection']['eyebrow']);
        $this->assertCount(3, $homeJson['props']['aboutSection']['images']);
        $this->assertCount(5, $homeJson['props']['trustSection']['items']);
        $this->assertCount(4, $homeJson['props']['processSection']['steps']);
        $this->assertNotEmpty($homeJson['props']['contactSection']['title']);
        $this->assertCount(6, $homeJson['props']['configs']['policies']);

        // 2. GET /san-pham
        $productsViRes = $this->get('/san-pham', $inertiaHeaders);
        $productsViRes->assertStatus(200);
        $productsViJson = $productsViRes->json();
        $this->assertSame('Product/Index', $productsViJson['component']);
        $this->assertNotNull($productsViJson['props']['heroBanner']);
        $this->assertCount(103, $productsViJson['props']['products']);
        $this->assertCount(4, $productsViJson['props']['categories']);
        $this->assertCount(6, $productsViJson['props']['configs']['policies']);

        // 3. GET /en/products
        $productsEnRes = $this->get('/en/products', $inertiaHeaders);
        $productsEnRes->assertStatus(200);
        $productsEnJson = $productsEnRes->json();
        $this->assertSame('Product/Index', $productsEnJson['component']);
        $this->assertNotNull($productsEnJson['props']['heroBanner']);
        $this->assertCount(103, $productsEnJson['props']['products']);
        $this->assertCount(4, $productsEnJson['props']['categories']);
        $this->assertCount(6, $productsEnJson['props']['configs']['policies']);

        // 4. GET /tai-lieu
        $docsViRes = $this->get('/tai-lieu', $inertiaHeaders);
        $docsViRes->assertStatus(200);
        $docsViJson = $docsViRes->json();
        $this->assertSame('Documents/Index', $docsViJson['component']);
        $this->assertNotNull($docsViJson['props']['heroBanner']);
        $this->assertNotNull($docsViJson['props']['ctaBanner']);
        $this->assertCount(4, $docsViJson['props']['product_categories']);

        // 5. GET /en/documents
        $docsEnRes = $this->get('/en/documents', $inertiaHeaders);
        $docsEnRes->assertStatus(200);
        $docsEnJson = $docsEnRes->json();
        $this->assertSame('Documents/Index', $docsEnJson['component']);
        $this->assertNotNull($docsEnJson['props']['heroBanner']);
        $this->assertNotNull($docsEnJson['props']['ctaBanner']);
        $this->assertCount(4, $docsEnJson['props']['product_categories']);

        // 6. GET /gioi-thieu
        $aboutRes = $this->get('/gioi-thieu', $inertiaHeaders);
        $aboutRes->assertStatus(200);
        $aboutJson = $aboutRes->json();
        $this->assertSame('About/Index', $aboutJson['component']);
        $this->assertNotNull($aboutJson['props']['heroBanner']);
        $this->assertNotNull($aboutJson['props']['storySection']);
        $this->assertCount(3, $aboutJson['props']['storySection']['images']);
        $this->assertNotNull($aboutJson['props']['missionVisionSection']);
        $this->assertCount(2, $aboutJson['props']['missionVisionSection']['items']);
        $this->assertCount(4, $aboutJson['props']['statsItems']);
        $this->assertNotNull($aboutJson['props']['companionSection']);
        $this->assertCount(2, $aboutJson['props']['companionSection']['cards']);
        $this->assertCount(3, $aboutJson['props']['posts']);

        // 7. GET /quy-trinh
        $processRes = $this->get('/quy-trinh', $inertiaHeaders);
        $processRes->assertStatus(200);
        $processJson = $processRes->json();
        $this->assertSame('Process/Index', $processJson['component']);
        $this->assertCount(5, $processJson['props']['steps']);
        $this->assertCount(6, $processJson['props']['faqs']);
        $this->assertCount(6, $processJson['props']['configs']['policies']);

        // 8. GET /dich-vu
        $servicesRes = $this->get('/dich-vu', $inertiaHeaders);
        $servicesRes->assertStatus(200);
        $servicesJson = $servicesRes->json();
        $this->assertSame('Services/Index', $servicesJson['component']);
        $this->assertIsArray($servicesJson['props']['services']);
        $this->assertNotEmpty($servicesJson['props']['global']['general_business_name']);
        $this->assertCount(6, $servicesJson['props']['configs']['policies']);
    }
}
