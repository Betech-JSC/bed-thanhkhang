<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Post\Post;
use Database\Seeders\ThanhKhangAboutCompanionSeeder;
use Database\Seeders\ThanhKhangAboutHeroSeeder;
use Database\Seeders\ThanhKhangAboutMissionVisionSeeder;
use Database\Seeders\ThanhKhangAboutStatsSeeder;
use Database\Seeders\ThanhKhangAboutStorySeeder;
use Database\Seeders\ThanhKhangHomeSamplePostsSeeder;
use Database\Seeders\ThanhKhangSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\Admin;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AboutPageCmsTest extends TestCase
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
    }

    public function test_about_settings_keys_and_slider_positions_are_synchronized_across_cms_layers(): void
    {
        $aboutKeys = [
            'about_story_eyebrow',
            'about_story_eyebrow_en',
            'about_story_heading',
            'about_story_heading_en',
            'about_story_paragraph_1',
            'about_story_paragraph_1_en',
            'about_story_paragraph_2',
            'about_story_paragraph_2_en',
            'about_story_paragraph_3',
            'about_story_paragraph_3_en',
            'about_story_commitment',
            'about_story_commitment_en',
            'about_companion_eyebrow',
            'about_companion_eyebrow_en',
            'about_companion_heading',
            'about_companion_heading_en',
        ];

        $settingRules = Setting::rules('general');
        $configRules = Config::rules('general');

        foreach ($aboutKeys as $key) {
            $this->assertArrayHasKey($key, $settingRules, "Setting::rules('general') missing key: {$key}");
            $this->assertArrayHasKey($key, $configRules, "Config::rules('general') missing key: {$key}");
        }

        $positions = config('slider.positions');
        $this->assertTrue($positions['BANNER_ABOUT_HERO']['banner'] ?? null);
        $this->assertFalse($positions['ABOUT_STORY_GALLERY']['banner'] ?? null);
        $this->assertTrue($positions['BANNER_ABOUT_MISSION_VISION']['banner'] ?? null);
        $this->assertFalse($positions['ABOUT_MISSION_VISION_ITEMS']['banner'] ?? null);
        $this->assertFalse($positions['ABOUT_STATS_ITEMS']['banner'] ?? null);
        $this->assertFalse($positions['ABOUT_COMPANION_CARDS']['banner'] ?? null);
    }

    public function test_about_seeders_are_idempotent_and_serve_all_five_sections_in_vi_and_en_without_faq_leakage(): void
    {
        // Seed FAQs + Blog Posts + all 5 About Page sections
        $this->seed([
            ThanhKhangSettingSeeder::class,
            ThanhKhangHomeSamplePostsSeeder::class,
            ThanhKhangAboutHeroSeeder::class,
            ThanhKhangAboutStorySeeder::class,
            ThanhKhangAboutMissionVisionSeeder::class,
            ThanhKhangAboutStatsSeeder::class,
            ThanhKhangAboutCompanionSeeder::class,
        ]);

        // Re-run all 5 About seeders a second time to verify strict idempotency
        $this->seed([
            ThanhKhangAboutHeroSeeder::class,
            ThanhKhangAboutStorySeeder::class,
            ThanhKhangAboutMissionVisionSeeder::class,
            ThanhKhangAboutStatsSeeder::class,
            ThanhKhangAboutCompanionSeeder::class,
        ]);

        $this->assertSame(1, Slider::where('position_display', 'BANNER_ABOUT_HERO')->count());
        $this->assertSame(3, Slider::where('position_display', 'ABOUT_STORY_GALLERY')->count());
        $this->assertSame(1, Slider::where('position_display', 'BANNER_ABOUT_MISSION_VISION')->count());
        $this->assertSame(2, Slider::where('position_display', 'ABOUT_MISSION_VISION_ITEMS')->count());
        $this->assertSame(4, Slider::where('position_display', 'ABOUT_STATS_ITEMS')->count());
        $this->assertSame(2, Slider::where('position_display', 'ABOUT_COMPANION_CARDS')->count());

        // Verify 6 FAQs exist in DB so we prove HistoryController@index filters them out of posts
        $this->assertSame(6, Post::where('type', Post::TYPE_FAQ)->count());
        $this->assertSame(3, Post::where('type', Post::TYPE_POST)->count());

        // 1. Verify Vietnamese Inertia response (/gioi-thieu)
        $viRes = $this->get('/gioi-thieu', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $viRes->assertStatus(200);
        $viData = $viRes->json();

        $this->assertSame('About/Index', $viData['component']);
        $viProps = $viData['props'];

        // Section 1: Hero Banner
        $this->assertSame('Về Thành Khang Tecsat', $viProps['heroBanner']['subtitle']);
        $this->assertSame("KẾT NỐI CHẤT LƯỢNG\nĐỒNG HÀNH BỀN VỮNG", $viProps['heroBanner']['title']);
        $this->assertSame('/assets/images/about/about-hero-banner.png', $viProps['heroBanner']['image']['url']);

        // Section 2: Story + 3 Gallery Images
        $this->assertSame('Câu chuyện của chúng tôi', $viProps['storySection']['eyebrow']);
        $this->assertSame("Không chỉ là sản phẩm\nLà giải pháp và sự an tâm", $viProps['storySection']['heading']);
        $this->assertCount(3, $viProps['storySection']['paragraphs']);
        $this->assertSame('"ĐÚNG NGUỒN GỐC - ĐÚNG THÔNG SỐ - ĐÚNG TIẾN ĐỘ"', $viProps['storySection']['commitment']);
        $this->assertCount(3, $viProps['storySection']['images']);
        $this->assertSame('/assets/images/about/about-story-chain-inspection.png', $viProps['storySection']['images'][0]['url']);
        $this->assertSame('/assets/images/about/about-story-sprocket.png', $viProps['storySection']['images'][1]['url']);
        $this->assertSame('/assets/images/about/about-story-roller-chains.png', $viProps['storySection']['images'][2]['url']);

        // Section 3: Mission & Vision
        $this->assertSame('/assets/images/about/about-mission-vision-bg.png', $viProps['missionVisionSection']['banner']['url']);
        $this->assertCount(2, $viProps['missionVisionSection']['items']);
        $this->assertSame('SỨ MỆNH', $viProps['missionVisionSection']['items'][0]['subtitle']);
        $this->assertSame('TẦM NHÌN', $viProps['missionVisionSection']['items'][1]['subtitle']);

        // Section 4: Stats
        $this->assertCount(4, $viProps['statsItems']);
        $this->assertSame(['100+', 'Toàn Quốc', 'CO/CQ', '24/7'], array_column($viProps['statsItems'], 'value'));

        // Section 5: Companion
        $this->assertSame('Đồng hành cùng khách hàng', $viProps['companionSection']['eyebrow']);
        $this->assertSame('Từ lựa chọn ban đầu đến vận hành dài lâu', $viProps['companionSection']['heading']);
        $this->assertCount(2, $viProps['companionSection']['cards']);
        $this->assertSame('Liên hệ tư vấn', $viProps['companionSection']['cards'][0]['cta_text']);
        $this->assertSame('/lien-he', $viProps['companionSection']['cards'][0]['cta_link']);
        $this->assertSame('Liên hệ hỗ trợ', $viProps['companionSection']['cards'][1]['cta_text']);
        $this->assertSame('/lien-he', $viProps['companionSection']['cards'][1]['cta_link']);

        // Posts prop must contain ONLY the 3 blog POST articles (never FAQ records)
        $this->assertCount(3, $viProps['posts']);
        $faqIds = Post::where('type', Post::TYPE_FAQ)->pluck('id')->all();
        foreach ($viProps['posts'] as $postItem) {
            $this->assertNotContains($postItem['id'], $faqIds, 'FAQ record leaked into About page posts prop');
        }

        // 2. Verify English JSON response (/en/about-us)
        $enRes = $this->getJson('/en/about-us');
        $enRes->assertStatus(200);
        $enProps = $enRes->json();

        $this->assertSame('About Thanh Khang Tecsat', $enProps['heroBanner']['subtitle']);
        $this->assertSame("CONNECTING QUALITY\nSUSTAINABLE PARTNERSHIP", $enProps['heroBanner']['title']);
        $this->assertSame('Our Story', $enProps['storySection']['eyebrow']);
        $this->assertCount(3, $enProps['storySection']['paragraphs']);
        $this->assertSame('MISSION', $enProps['missionVisionSection']['items'][0]['subtitle']);
        $this->assertSame('VISION', $enProps['missionVisionSection']['items'][1]['subtitle']);
        $this->assertSame(['100+', 'Nationwide', 'CO/CQ', '24/7'], array_column($enProps['statsItems'], 'value'));
        $this->assertSame('Partnering With Our Customers', $enProps['companionSection']['eyebrow']);
        $this->assertSame('/en/contact', $enProps['companionSection']['cards'][0]['cta_link']);
        $this->assertSame('/en/contact', $enProps['companionSection']['cards'][1]['cta_link']);
    }

    public function test_admin_saving_general_settings_persists_about_fields_and_invalidates_about_cache(): void
    {
        $role = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        Gate::before(fn () => true);

        $admin = Admin::firstOrCreate(
            ['email' => 'admin_about_cms@jamstackvietnam.com'],
            [
                'name' => 'Admin About CMS',
                'password' => bcrypt('secret123'),
                'status' => Admin::STATUS_ACTIVE,
            ]
        );
        $admin->assignRole($role);

        // Warm up stale cache for About page
        Cache::put('history_index_vi', ['storySection' => ['heading' => 'STALE']], 600);
        Cache::put('history_index_en', ['storySection' => ['heading' => 'STALE_EN']], 600);

        $postRes = $this->actingAs($admin, 'admin')->post(
            route('admin.settings.store', ['id' => 'general']),
            [
                'id' => 'general',
                'general_business_name' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
                'about_story_eyebrow' => 'Hành trình phát triển CMS',
                'about_story_eyebrow_en' => '',
                'about_story_heading' => "Giải pháp đồng bộ\nVận hành tin cậy",
                'about_story_heading_en' => "Integrated Solutions\nReliable Operation",
                'about_story_paragraph_1' => 'Đoạn câu chuyện số 1 cập nhật từ Admin.',
                'about_story_paragraph_2' => 'Đoạn câu chuyện số 2 cập nhật từ Admin.',
                'about_story_paragraph_3' => '',
                'about_story_commitment' => '"CHUẨN XÁC - BỀN BỈ"',
                'about_companion_eyebrow' => 'Hợp tác lâu dài',
                'about_companion_heading' => 'Đồng hành kỹ thuật trọn vòng đời',
                'about_companion_heading_en' => 'Full-Lifecycle Technical Partnership',
            ]
        );

        $this->assertTrue(in_array($postRes->getStatusCode(), [200, 302], true));
        $postRes->assertSessionHasNoErrors();

        // Cache keys for About page must be cleared immediately
        $this->assertNull(Cache::get('history_index_vi'));
        $this->assertNull(Cache::get('history_index_en'));

        // VI request reflects newly saved Admin settings
        $viRes = $this->getJson('/gioi-thieu');
        $viRes->assertStatus(200);
        $this->assertSame('Hành trình phát triển CMS', $viRes->json('storySection.eyebrow'));
        $this->assertSame("Giải pháp đồng bộ\nVận hành tin cậy", $viRes->json('storySection.heading'));
        $this->assertSame([
            'Đoạn câu chuyện số 1 cập nhật từ Admin.',
            'Đoạn câu chuyện số 2 cập nhật từ Admin.',
        ], $viRes->json('storySection.paragraphs'));
        $this->assertSame('"CHUẨN XÁC - BỀN BỈ"', $viRes->json('storySection.commitment'));
        $this->assertSame('Đồng hành kỹ thuật trọn vòng đời', $viRes->json('companionSection.heading'));

        // EN request uses EN values when set, and falls back to VI when EN is empty
        $enRes = $this->getJson('/en/about-us');
        $enRes->assertStatus(200);
        $this->assertSame('Hành trình phát triển CMS', $enRes->json('storySection.eyebrow'));
        $this->assertSame("Integrated Solutions\nReliable Operation", $enRes->json('storySection.heading'));
        $this->assertSame('Full-Lifecycle Technical Partnership', $enRes->json('companionSection.heading'));
    }

    public function test_about_empty_database_state_returns_http_200_with_null_sections_and_zero_fake_data(): void
    {
        DB::table('slider_translations')->delete();
        DB::table('sliders')->delete();
        DB::table('post_translations')->delete();
        DB::table('posts')->delete();
        if (Schema::hasTable('meta_pages')) {
            DB::table('meta_pages')->delete();
        }
        DB::table('settings')->delete();

        $this->assertSame(0, DB::table('sliders')->count());
        $this->assertSame(0, DB::table('posts')->count());
        $this->assertSame(0, DB::table('settings')->count());
        if (Schema::hasTable('meta_pages')) {
            $this->assertSame(0, DB::table('meta_pages')->count());
        }

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
        Post::clearPostCaches();
        Cache::flush();

        // 1. Vietnamese (/gioi-thieu) — Inertia + JSON + HTML
        $viInertiaRes = $this->get('/gioi-thieu', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $viInertiaRes->assertStatus(200);
        $viData = $viInertiaRes->json();

        $this->assertSame('About/Index', $viData['component']);
        $this->assertNull($viData['props']['heroBanner']);
        $this->assertNull($viData['props']['storySection']);
        $this->assertNull($viData['props']['missionVisionSection']);
        $this->assertSame([], $viData['props']['statsItems']);
        $this->assertNull($viData['props']['companionSection']);
        $this->assertSame([], $viData['props']['posts']);
        $this->assertNull($viData['props']['seo']);

        $this->get('/gioi-thieu')->assertStatus(200);

        // 2. English (/en/about-us) — Inertia + JSON + HTML
        $enInertiaRes = $this->get('/en/about-us', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $enInertiaRes->assertStatus(200);
        $enData = $enInertiaRes->json();

        $this->assertSame('About/Index', $enData['component']);
        $this->assertNull($enData['props']['heroBanner']);
        $this->assertNull($enData['props']['storySection']);
        $this->assertNull($enData['props']['missionVisionSection']);
        $this->assertSame([], $enData['props']['statsItems']);
        $this->assertNull($enData['props']['companionSection']);
        $this->assertSame([], $enData['props']['posts']);
        $this->assertNull($enData['props']['seo']);

        $this->get('/en/about-us')->assertStatus(200);

        // Verify About EmptyState i18n dictionary keys are synchronized across PHP & Frontend JSON
        $this->assertSame(
            'About Us content is being updated',
            __('ui.Nội dung giới thiệu đang được cập nhật', [], 'en')
        );
        $this->assertSame(
            'Please check back soon or contact us directly for detailed assistance from our engineering team.',
            __('ui.Quý khách vui lòng quay lại sau hoặc liên hệ trực tiếp để được đội ngũ kỹ thuật hỗ trợ thông tin chi tiết.', [], 'en')
        );
        $this->assertSame('Contact Us', __('ui.Liên hệ với chúng tôi', [], 'en'));
    }

    public function test_about_dynamic_seo_hierarchy_and_companion_card_zero_cta_fallback(): void
    {
        $this->seed([
            ThanhKhangSettingSeeder::class,
            ThanhKhangAboutCompanionSeeder::class,
        ]);

        if (Schema::hasTable('meta_pages')) {
            DB::table('meta_pages')->delete();
        }
        Config::clearBrandCaches();
        Slider::clearSliderCaches();

        // 1. Without MetaPage: falls back to General Settings SEO
        $viFallbackSeo = $this->getJson('/gioi-thieu')->assertStatus(200)->json('seo');
        $enFallbackSeo = $this->getJson('/en/about-us')->assertStatus(200)->json('seo');
        $this->assertIsArray($viFallbackSeo);
        $this->assertNotEmpty($viFallbackSeo['title']);
        $this->assertIsArray($enFallbackSeo);
        $this->assertNotEmpty($enFallbackSeo['title']);

        // 2. With MetaPage: overrides General Settings immediately (outside history_index_{locale} cache)
        MetaPage::create([
            'url' => '/gioi-thieu',
            'seo_meta_title' => 'SEO Động Giới Thiệu VI - MetaPage',
            'seo_meta_description' => 'Mô tả SEO động từ bảng meta_pages cho trang Giới thiệu.',
            'seo_meta_robots' => 'index, follow',
        ]);
        MetaPage::create([
            'url' => '/en/about-us',
            'seo_meta_title' => 'Dynamic About Us SEO EN - MetaPage',
            'seo_meta_description' => 'Dynamic SEO description from meta_pages table for About Us.',
            'seo_meta_robots' => 'index, follow',
        ]);

        $viMetaSeo = $this->getJson('/gioi-thieu')->assertStatus(200)->json('seo');
        $enMetaSeo = $this->getJson('/en/about-us')->assertStatus(200)->json('seo');
        $this->assertSame('SEO Động Giới Thiệu VI - MetaPage', $viMetaSeo['title']);
        $this->assertSame('Mô tả SEO động từ bảng meta_pages cho trang Giới thiệu.', $viMetaSeo['description']);
        $this->assertSame('Dynamic About Us SEO EN - MetaPage', $enMetaSeo['title']);
        $this->assertSame('Dynamic SEO description from meta_pages table for About Us.', $enMetaSeo['description']);

        // 3. Clearing primary_cta_link & link on ABOUT_COMPANION_CARDS must return cta_link === '' (zero hardcoded /lien-he)
        $companionSliderIds = Slider::where('position_display', 'ABOUT_COMPANION_CARDS')->pluck('id')->all();
        $this->assertCount(2, $companionSliderIds);

        DB::table('slider_translations')
            ->where('slider_id', $companionSliderIds[0])
            ->update([
                'primary_cta_link' => null,
                'link' => '#',
            ]);
        DB::table('slider_translations')
            ->where('slider_id', $companionSliderIds[1])
            ->update([
                'primary_cta_link' => '',
                'link' => null,
            ]);
        Slider::clearSliderCaches();

        $viCards = $this->getJson('/gioi-thieu')->assertStatus(200)->json('companionSection.cards');
        $enCards = $this->getJson('/en/about-us')->assertStatus(200)->json('companionSection.cards');

        $this->assertCount(2, $viCards);
        $this->assertSame('', $viCards[0]['cta_link']);
        $this->assertNull($viCards[0]['primary_cta_link']);
        $this->assertSame('', $viCards[1]['cta_link']);
        $this->assertNull($viCards[1]['primary_cta_link']);

        $this->assertCount(2, $enCards);
        $this->assertSame('', $enCards[0]['cta_link']);
        $this->assertNull($enCards[0]['primary_cta_link']);
        $this->assertSame('', $enCards[1]['cta_link']);
        $this->assertNull($enCards[1]['primary_cta_link']);

        // 4. Clearing both MetaPage and General Settings returns seo === null (Zero-Fallback)
        DB::table('meta_pages')->delete();
        DB::table('settings')->delete();
        Config::clearBrandCaches();

        $this->assertNull($this->getJson('/gioi-thieu')->assertStatus(200)->json('seo'));
        $this->assertNull($this->getJson('/en/about-us')->assertStatus(200)->json('seo'));
    }
}
