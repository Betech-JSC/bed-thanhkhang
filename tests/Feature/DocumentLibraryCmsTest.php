<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Database\Seeders\RoleSeeder;
use Database\Seeders\ThanhKhangDocumentBannerSeeder;
use Database\Seeders\ThanhKhangDocumentLibrarySeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Slider\Models\Slider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentLibraryCmsTest extends TestCase
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

    protected function clearAllDocumentLibraryTablesAndCaches(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('post_ref_categories')->delete();
        DB::table('post_translations')->delete();
        DB::table('posts')->delete();
        DB::table('post_category_translations')->delete();
        DB::table('post_categories')->delete();
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
        PostCategory::clearPostCategoryCaches();
        Cache::flush();
    }

    public function test_document_library_empty_database_state_returns_http_200_with_zero_fallback(): void
    {
        $this->clearAllDocumentLibraryTablesAndCaches();

        // 1. Vietnamese (/tai-lieu/thu-vien) — Inertia + HTML hreflang
        $viInertia = $this->get('/tai-lieu/thu-vien', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $viInertia->assertStatus(200);
        $viData = $viInertia->json();

        $this->assertSame('Documents/Library', $viData['component']);
        $this->assertNull($viData['props']['heroBanner']);
        $this->assertNull($viData['props']['ctaBanner']);
        $this->assertSame([], $viData['props']['documentCategories']);
        $this->assertSame(0, $viData['props']['totalDocumentsCount']);
        $this->assertSame([], $viData['props']['documents']);
        $this->assertNull($viData['props']['seo']);
        $this->assertSame([
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 10,
            'total' => 0,
        ], $viData['props']['pagination']);

        $viHtml = $this->get('/tai-lieu/thu-vien');
        $viHtml->assertStatus(200);
        $viHtml->assertSee('<link rel="alternate" hreflang="vi" href="http://localhost/tai-lieu/thu-vien"', false);
        $viHtml->assertSee('<link rel="alternate" hreflang="en" href="http://localhost/en/documents/library"', false);
        $viHtml->assertSee('<link rel="alternate" hreflang="x-default" href="http://localhost/tai-lieu/thu-vien"', false);

        // 2. English (/en/documents/library) — Inertia + HTML hreflang
        $enInertia = $this->get('/en/documents/library', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $enInertia->assertStatus(200);
        $enData = $enInertia->json();

        $this->assertSame('Documents/Library', $enData['component']);
        $this->assertNull($enData['props']['heroBanner']);
        $this->assertNull($enData['props']['ctaBanner']);
        $this->assertSame([], $enData['props']['documentCategories']);
        $this->assertSame(0, $enData['props']['totalDocumentsCount']);
        $this->assertSame([], $enData['props']['documents']);
        $this->assertNull($enData['props']['seo']);
        $this->assertSame([
            'current_page' => 1,
            'last_page' => 1,
            'per_page' => 10,
            'total' => 0,
        ], $enData['props']['pagination']);

        $enHtml = $this->get('/en/documents/library');
        $enHtml->assertStatus(200);
        $enHtml->assertSee('<link rel="alternate" hreflang="vi" href="http://localhost/tai-lieu/thu-vien"', false);
        $enHtml->assertSee('<link rel="alternate" hreflang="en" href="http://localhost/en/documents/library"', false);
        $enHtml->assertSee('<link rel="alternate" hreflang="x-default" href="http://localhost/tai-lieu/thu-vien"', false);
    }

    public function test_document_library_seeded_data_idempotency_and_pagination(): void
    {
        $this->clearAllDocumentLibraryTablesAndCaches();

        // Run both seeders twice consecutively to prove strict idempotency
        $this->seed([
            ThanhKhangDocumentBannerSeeder::class,
            ThanhKhangDocumentLibrarySeeder::class,
        ]);
        $this->seed([
            ThanhKhangDocumentBannerSeeder::class,
            ThanhKhangDocumentLibrarySeeder::class,
        ]);

        $this->assertSame(1, Slider::where('position_display', 'BANNER_DOCUMENT_HERO')->count());
        $this->assertSame(1, Slider::where('position_display', 'BANNER_DOCUMENT_CTA')->count());
        $this->assertSame(6, PostCategory::where('type', PostCategory::TYPE_DOCUMENT)->count());
        $this->assertSame(33, Post::where('type', Post::TYPE_DOCUMENT)->count());

        // Page 1 (/tai-lieu/thu-vien)
        $page1 = $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json();

        $this->assertSame('TÀI LIỆU & CATALOGUE', $page1['heroBanner']['title']);
        $this->assertSame('KHÔNG TÌM THẤY TÀI LIỆU BẠN CẦN?', $page1['ctaBanner']['title']);
        $this->assertSame(33, $page1['totalDocumentsCount']);
        $this->assertCount(6, $page1['documentCategories']);
        $this->assertCount(10, $page1['documents']);
        $this->assertSame([
            'current_page' => 1,
            'last_page' => 4,
            'per_page' => 10,
            'total' => 33,
        ], $page1['pagination']);

        $expectedPage1FigmaTitles = [
            'Catalogue tổng hợp Xích thang cuốn Universal Chain (Bậc thang thương mại & công cộng)',
            'Data sheet Xích bãi đỗ xe tuần hoàn đứng LTAL666 (Vertical circulation)',
            'Thông số kỹ thuật Tay vịn thang cuốn & Bánh xe thang cuốn chính hãng',
            'Catalogue Xích bãi đỗ xe lập thể & Xích dẫn động thang nâng ô tô',
            'Data sheet Xích con lăn trên, Xích chốt rỗng & Xích inox dây chuyền đóng gói',
            'Catalogue Xích băng tải hành lý sân bay – M Series & FV Series',
            'Tài liệu kỹ thuật Quạt BLDC & Giải pháp tiết kiệm điện cho thang cuốn',
            'Quy chuẩn kích thước Xích con lăn tiêu chuẩn ANSI / DIN & Bảng tra mã',
            'Hướng dẫn kiểm tra, đo đạc bước xích & bảo trì xích thang cuốn định kỳ',
            'Hồ sơ năng lực & Chứng nhận đại lý ủy quyền Thành Khang Tecsat (CO/CQ)',
        ];
        $this->assertSame($expectedPage1FigmaTitles, array_column($page1['documents'], 'title'));

        foreach ($page1['documents'] as $doc) {
            $this->assertFalse($doc['has_file']);
            $this->assertNull($doc['download_url']);
        }

        // Page 4 (/tai-lieu/thu-vien?page=4) -> 3 items, last_page = 4
        $page4 = $this->getJson('/tai-lieu/thu-vien?page=4')->assertStatus(200)->json();

        $this->assertSame(33, $page4['totalDocumentsCount']);
        $this->assertCount(3, $page4['documents']);
        $this->assertSame([
            'current_page' => 4,
            'last_page' => 4,
            'per_page' => 10,
            'total' => 33,
        ], $page4['pagination']);
        $this->assertSame(4, $page4['initialFilters']['page']);
    }

    public function test_document_library_filter_search_and_slug_alias_mapping(): void
    {
        $this->clearAllDocumentLibraryTablesAndCaches();
        $this->seed([
            ThanhKhangDocumentBannerSeeder::class,
            ThanhKhangDocumentLibrarySeeder::class,
        ]);

        // 1. VI category filter: ?category=tiet-kiem-dien -> 5 documents
        $viEnergy = $this->getJson('/tai-lieu/thu-vien?category=tiet-kiem-dien')
            ->assertStatus(200)
            ->json();

        $this->assertSame('tiet-kiem-dien', $viEnergy['initialFilters']['category']);
        $this->assertSame(5, $viEnergy['pagination']['total']);
        $this->assertCount(5, $viEnergy['documents']);
        $this->assertSame(33, $viEnergy['totalDocumentsCount']);
        foreach ($viEnergy['documents'] as $doc) {
            $this->assertContains('tiet-kiem-dien', $doc['category_slugs']);
        }

        // 2. VI ProductCategory alias: ?category=linh-kien-thang-nang-o-to -> thang-nang-o-to (total = 6)
        $viAlias = $this->getJson('/tai-lieu/thu-vien?category=linh-kien-thang-nang-o-to')
            ->assertStatus(200)
            ->json();

        $this->assertSame('thang-nang-o-to', $viAlias['initialFilters']['category']);
        $this->assertSame(6, $viAlias['pagination']['total']);
        $this->assertCount(6, $viAlias['documents']);
        foreach ($viAlias['documents'] as $doc) {
            $this->assertContains('thang-nang-o-to', $doc['category_slugs']);
        }

        // 3. EN category filter: /en/documents/library?category=energy-saving -> 5 EN documents
        $enEnergy = $this->getJson('/en/documents/library?category=energy-saving')
            ->assertStatus(200)
            ->json();

        $this->assertSame('energy-saving', $enEnergy['initialFilters']['category']);
        $this->assertSame(5, $enEnergy['pagination']['total']);
        $this->assertCount(5, $enEnergy['documents']);
        $this->assertContains(
            'BLDC Motor Fan Technical Manual & Escalator Energy Saving Solutions',
            array_column($enEnergy['documents'], 'title')
        );
        foreach ($enEnergy['documents'] as $doc) {
            $this->assertContains('energy-saving', $doc['category_slugs']);
        }

        // 4. EN ProductCategory alias: car-parking-lift-components -> car-parking-lifts (total = 6)
        $enAlias = $this->getJson('/en/documents/library?category=car-parking-lift-components')
            ->assertStatus(200)
            ->json();

        $this->assertSame('car-parking-lifts', $enAlias['initialFilters']['category']);
        $this->assertSame(6, $enAlias['pagination']['total']);
        $this->assertCount(6, $enAlias['documents']);
        foreach ($enAlias['documents'] as $doc) {
            $this->assertContains('car-parking-lifts', $doc['category_slugs']);
        }

        // 5. Search filter: ?search=BLDC -> 4 documents
        $searchOnly = $this->getJson('/tai-lieu/thu-vien?search=BLDC')
            ->assertStatus(200)
            ->json();

        $this->assertSame('BLDC', $searchOnly['initialFilters']['search']);
        $this->assertSame(4, $searchOnly['pagination']['total']);
        $this->assertCount(4, $searchOnly['documents']);

        // 6. Combined category + search: ?category=tiet-kiem-dien&search=BLDC -> 4 documents (excludes non-BLDC item #15)
        $combined = $this->getJson('/tai-lieu/thu-vien?category=tiet-kiem-dien&search=BLDC')
            ->assertStatus(200)
            ->json();

        $this->assertSame('tiet-kiem-dien', $combined['initialFilters']['category']);
        $this->assertSame('BLDC', $combined['initialFilters']['search']);
        $this->assertSame(4, $combined['pagination']['total']);
        $this->assertCount(4, $combined['documents']);

        // Combined mismatched category + search -> 0 documents
        $emptyCombined = $this->getJson('/tai-lieu/thu-vien?category=xich-cong-nghiep&search=BLDC')
            ->assertStatus(200)
            ->json();

        $this->assertSame(0, $emptyCombined['pagination']['total']);
        $this->assertSame(1, $emptyCombined['pagination']['last_page']);
        $this->assertCount(0, $emptyCombined['documents']);
    }

    public function test_document_library_file_download_binding_and_cache_invalidation(): void
    {
        $this->clearAllDocumentLibraryTablesAndCaches();
        $this->seed([
            ThanhKhangDocumentBannerSeeder::class,
            ThanhKhangDocumentLibrarySeeder::class,
        ]);

        // Warm caches for both locales
        $this->getJson('/tai-lieu/thu-vien')->assertStatus(200);
        $this->getJson('/en/documents/library')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 1. Update download column on a TYPE_DOCUMENT Post -> clears both caches & sets has_file/download_url
        $document = Post::where('type', Post::TYPE_DOCUMENT)
            ->orderBy('position', 'asc')
            ->firstOrFail();
        $document->download = '/static/files/universal-chain-catalogue-2026.pdf';
        $document->save();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));

        $viAfterPost = $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json();
        $this->assertTrue($viAfterPost['documents'][0]['has_file']);
        $this->assertSame('/static/files/universal-chain-catalogue-2026.pdf', $viAfterPost['documents'][0]['download_url']);
        $this->assertSame('/static/files/universal-chain-catalogue-2026.pdf', $viAfterPost['documents'][0]['file_url']);

        $enAfterPost = $this->getJson('/en/documents/library')->assertStatus(200)->json();
        $this->assertTrue($enAfterPost['documents'][0]['has_file']);
        $this->assertSame('/static/files/universal-chain-catalogue-2026.pdf', $enAfterPost['documents'][0]['download_url']);
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 2. Update PostCategory (TYPE_DOCUMENT) -> clears documents_library_vi & documents_library_en
        $docCategory = PostCategory::where('type', PostCategory::TYPE_DOCUMENT)
            ->orderBy('position', 'asc')
            ->firstOrFail();
        DB::table('post_category_translations')
            ->where('post_category_id', $docCategory->id)
            ->where('locale', 'vi')
            ->update(['title' => 'Linh kiện thang cuốn Cập Nhật']);
        $docCategory->position = 10;
        $docCategory->save();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));

        $viAfterCategory = $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json();
        $this->assertContains('Linh kiện thang cuốn Cập Nhật', array_column($viAfterCategory['documentCategories'], 'title'));

        // Re-warm EN cache
        $this->getJson('/en/documents/library')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 3. Update Slider -> clears documents_library_vi & documents_library_en
        $heroSlider = Slider::where('position_display', 'BANNER_DOCUMENT_HERO')->firstOrFail();
        DB::table('slider_translations')
            ->where('slider_id', $heroSlider->id)
            ->where('locale', 'vi')
            ->update(['title' => 'THƯ VIỆN TÀI LIỆU CẬP NHẬT']);
        $heroSlider->position_sort = 25;
        $heroSlider->save();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));
        $this->assertSame(
            'THƯ VIỆN TÀI LIỆU CẬP NHẬT',
            $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json('heroBanner.title')
        );

        // Re-warm EN cache
        $this->getJson('/en/documents/library')->assertStatus(200);
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 4. Update Config / Settings -> Config::clearBrandCaches() clears documents_library_vi & documents_library_en
        RoleSeeder::createPermissions();
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $admin = Admin::firstOrCreate(
            ['email' => 'admin_library_cache@jamstackvietnam.com'],
            [
                'name' => 'Admin Library Cache',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        $settingRes = $this->actingAs($admin, 'admin')->post(
            route('admin.settings.store', ['id' => 'general']),
            [
                'id' => 'general',
                'general_business_name' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
                'general_site_name' => 'Thư viện Tài liệu Thành Khang VI',
                'general_site_name_en' => 'Thanh Khang Document Library EN',
                'seo_meta_title' => '',
            ]
        );
        $this->assertTrue(in_array($settingRes->getStatusCode(), [200, 302], true));
        $settingRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));
        $this->assertSame('Thư viện Tài liệu Thành Khang VI', $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json('seo.title'));
        $this->assertSame('Thanh Khang Document Library EN', $this->getJson('/en/documents/library')->assertStatus(200)->json('seo.title'));
    }

    public function test_document_library_admin_crud_assigns_document_type_and_invalidates_caches(): void
    {
        $this->clearAllDocumentLibraryTablesAndCaches();

        RoleSeeder::createPermissions();
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_documents_crud@jamstackvietnam.com'],
            [
                'name' => 'Admin Documents CRUD',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        // Guest cannot access admin routes
        $this->get('/admin/documents')->assertStatus(302);
        $this->get('/admin/document-categories')->assertStatus(302);

        // 1. Authenticated Super Admin GET index & form pages return HTTP 200
        $this->actingAs($admin, 'admin')->get('/admin/documents')->assertStatus(200);
        $this->actingAs($admin, 'admin')->getJson('/admin/documents')->assertStatus(200);
        $this->actingAs($admin, 'admin')->get('/admin/documents/form')->assertStatus(200);
        $this->actingAs($admin, 'admin')->getJson('/admin/documents/form')->assertStatus(200);

        $this->actingAs($admin, 'admin')->get('/admin/document-categories')->assertStatus(200);
        $this->actingAs($admin, 'admin')->getJson('/admin/document-categories')->assertStatus(200);
        $this->actingAs($admin, 'admin')->get('/admin/document-categories/form')->assertStatus(200);
        $this->actingAs($admin, 'admin')->getJson('/admin/document-categories/form')->assertStatus(200);

        // Warm library caches before POST store actions (reset app locale to default 'vi' to mirror fresh HTTP request)
        $this->getJson('/en/documents/library')->assertStatus(200);
        $this->getJson('/tai-lieu/thu-vien')->assertStatus(200);
        app()->setLocale('vi');
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 2. POST admin.document-categories.store automatically forces type = 'DOCUMENT' & clears caches
        $catStoreRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.document-categories.store'),
            [
                'title' => 'Tài liệu Kỹ thuật Mới',
                'status' => PostCategory::STATUS_ACTIVE,
                'position' => 1,
                'type' => 'POST',
            ]
        );
        $this->assertTrue(in_array($catStoreRes->getStatusCode(), [200, 302], true));
        $catStoreRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));

        $createdCategory = PostCategory::orderBy('id', 'desc')->firstOrFail();
        $this->assertSame(PostCategory::TYPE_DOCUMENT, $createdCategory->type);
        $this->assertSame('Tài liệu Kỹ thuật Mới', $createdCategory->title);

        // Re-warm library caches before storing a document
        $this->getJson('/en/documents/library')->assertStatus(200);
        $this->getJson('/tai-lieu/thu-vien')->assertStatus(200);
        app()->setLocale('vi');
        $this->assertTrue(Cache::has('documents_library_vi'));
        $this->assertTrue(Cache::has('documents_library_en'));

        // 3. POST admin.documents.store automatically forces type = 'DOCUMENT' & clears caches
        $docStoreRes = $this->actingAs($admin, 'admin')->post(
            route('vi.admin.documents.store'),
            [
                'title' => 'Catalogue Xích Tải Admin Test 2026',
                'description' => 'Mô tả tài liệu tạo mới từ Admin CMS',
                'download' => '/static/files/admin-test-catalogue-2026.pdf',
                'status' => Post::STATUS_ACTIVE,
                'position' => 1,
                'type' => 'POST',
                'categories' => [
                    ['id' => $createdCategory->id],
                ],
            ]
        );
        $this->assertTrue(in_array($docStoreRes->getStatusCode(), [200, 302], true));
        $docStoreRes->assertSessionHasNoErrors();

        $this->assertFalse(Cache::has('documents_library_vi'));
        $this->assertFalse(Cache::has('documents_library_en'));

        $createdDocument = Post::orderBy('id', 'desc')->firstOrFail();
        $this->assertSame(Post::TYPE_DOCUMENT, $createdDocument->type);
        $this->assertSame('/static/files/admin-test-catalogue-2026.pdf', $createdDocument->download);
        $this->assertSame('Catalogue Xích Tải Admin Test 2026', $createdDocument->title);
        $this->assertDatabaseHas('post_ref_categories', [
            'post_id' => $createdDocument->id,
            'post_category_id' => $createdCategory->id,
        ]);

        // Verify edit forms and index endpoints filter & expose DOCUMENT records cleanly
        $this->actingAs($admin, 'admin')
            ->get("/admin/document-categories/form/{$createdCategory->id}")
            ->assertStatus(200);
        $this->actingAs($admin, 'admin')
            ->get("/admin/documents/form/{$createdDocument->id}")
            ->assertStatus(200);

        $docsTableJson = $this->actingAs($admin, 'admin')
            ->getJson('/admin/documents')
            ->assertStatus(200)
            ->json('data');
        $this->assertCount(1, $docsTableJson);
        $this->assertSame(Post::TYPE_DOCUMENT, $docsTableJson[0]['type']);

        $catsTableJson = $this->actingAs($admin, 'admin')
            ->getJson('/admin/document-categories')
            ->assertStatus(200)
            ->json('data');
        $this->assertCount(1, $catsTableJson);
        $this->assertSame(PostCategory::TYPE_DOCUMENT, $catsTableJson[0]['type']);

        $libraryJson = $this->getJson('/tai-lieu/thu-vien')->assertStatus(200)->json();
        $this->assertSame(1, $libraryJson['totalDocumentsCount']);
        $this->assertCount(1, $libraryJson['documentCategories']);
        $this->assertSame('Tài liệu Kỹ thuật Mới', $libraryJson['documentCategories'][0]['title']);
        $this->assertCount(1, $libraryJson['documents']);
        $this->assertSame('Catalogue Xích Tải Admin Test 2026', $libraryJson['documents'][0]['title']);
        $this->assertTrue($libraryJson['documents'][0]['has_file']);
        $this->assertSame('/static/files/admin-test-catalogue-2026.pdf', $libraryJson['documents'][0]['download_url']);
    }
}
