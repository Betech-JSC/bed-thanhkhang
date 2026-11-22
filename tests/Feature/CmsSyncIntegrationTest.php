<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Contact;
use App\Models\Keyword;
use App\Models\Policy\Policy;
use App\Models\Post\Post;
use App\Models\Product\ProductCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Core\Models\Setting;
use JamstackVietnam\Slider\Models\Slider;
use Tests\TestCase;

class CmsSyncIntegrationTest extends TestCase
{
    use RefreshDatabase;

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

    public function test_config_extracts_map_iframe_src_and_normalizes_general_settings_with_zero_fallback(): void
    {
        $rawIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12" width="600" height="450"></iframe>';
        $extracted = Config::extractMapIframeSrc($rawIframe);

        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $extracted);

        // Zero-Fallback when DB settings are empty
        $emptyNormalized = Config::normalizeGeneralSettings([], 'vi');
        $this->assertSame('', $emptyNormalized['general_business_name']);
        $this->assertSame('', $emptyNormalized['general_logo']);
        $this->assertSame('', $emptyNormalized['logo']);
        $this->assertSame('', $emptyNormalized['general_phone']);
        $this->assertSame('', $emptyNormalized['general_email']);
        $this->assertSame('', $emptyNormalized['general_address']);
        $this->assertSame('', $emptyNormalized['showroom_address']);
        $this->assertSame('', $emptyNormalized['general_description']);
        $this->assertSame('', $emptyNormalized['general_facebook']);
        $this->assertSame('', $emptyNormalized['general_linkedin']);
        $this->assertSame('', $emptyNormalized['linkedin']);
        $this->assertSame('', $emptyNormalized['map_iframe']);

        // Normalized when DB values exist
        $normalized = Config::normalizeGeneralSettings([
            'general_logo' => json_encode(['path' => '/storage/logo-test.png']),
            'general_business_name' => 'Thanh Khang Tecsat',
            'general_description' => 'Nhà cung cấp thiết bị công nghiệp B2B',
            'showroom_address' => '54A Nguyễn Chí Thanh, Hà Nội',
            'general_phone' => '0967732665',
            'general_email' => 'info@dvktthanhkhang.vn',
            'general_facebook' => 'https://facebook.com/thanhkhangtecsat',
            'general_linkedin' => 'https://linkedin.com/company/thanhkhangtecsat',
            'map_iframe' => $rawIframe,
        ], 'vi');

        $this->assertEquals('/storage/logo-test.png', $normalized['general_logo']);
        $this->assertEquals('/storage/logo-test.png', $normalized['logo']);
        $this->assertEquals('Thanh Khang Tecsat', $normalized['company_name']);
        $this->assertEquals('Nhà cung cấp thiết bị công nghiệp B2B', $normalized['general_description']);
        $this->assertEquals('54A Nguyễn Chí Thanh, Hà Nội', $normalized['showroom_address']);
        $this->assertEquals('0967732665', $normalized['phone']);
        $this->assertEquals('0967732665', $normalized['hotline']);
        $this->assertEquals('info@dvktthanhkhang.vn', $normalized['email']);
        $this->assertEquals('https://facebook.com/thanhkhangtecsat', $normalized['facebook']);
        $this->assertEquals('https://linkedin.com/company/thanhkhangtecsat', $normalized['linkedin']);
        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $normalized['map_iframe']);
        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $normalized['general_map_iframe']);
    }

    public function test_admin_setting_general_rules_include_home_and_brand_fields(): void
    {
        $rules = Setting::rules('general');

        $requiredKeys = [
            'general_logo',
            'general_description',
            'general_description_en',
            'showroom_address',
            'showroom_address_en',
            'general_linkedin',
            'map_iframe',
            'general_map_iframe',
            'home_about_eyebrow',
            'home_about_eyebrow_en',
            'home_about_lead_highlight',
            'home_about_lead_highlight_en',
            'home_about_lead_rest',
            'home_about_lead_rest_en',
            'home_about_paragraph_1',
            'home_about_paragraph_1_en',
            'home_about_paragraph_2',
            'home_about_paragraph_2_en',
            'home_about_commitment_quote',
            'home_about_commitment_quote_en',
            'home_about_commitment_sub',
            'home_about_commitment_sub_en',
            'home_about_cta_text',
            'home_about_cta_text_en',
            'home_about_cta_link',
            'home_about_cta_link_en',
            'home_contact_title',
            'home_contact_title_en',
            'home_contact_description',
            'home_contact_description_en',
        ];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $rules, "Setting::rules('general') missing key: {$key}");
        }
    }

    public function test_contact_normalize_lead_data_populates_bilingual_keys_and_lead_type(): void
    {
        $normalized = Contact::normalizeLeadData([
            'name' => 'Nguyễn Văn B',
            'phone' => '0906636425',
            'email' => 'nguyenvanb@example.com',
            'product_title' => 'Xích thang cuốn bước lớn TL133',
            'service' => 'Cung cấp phụ tùng thang cuốn',
            'message' => 'Báo giá lô 20 bộ xích thang cuốn tiêu chuẩn OEM',
        ], 'CONTACT_FORM');

        $this->assertEquals('Nguyễn Văn B', $normalized['Họ và tên']);
        $this->assertEquals('Nguyễn Văn B', $normalized['name']);
        $this->assertEquals('0906636425', $normalized['Số điện thoại']);
        $this->assertEquals('0906636425', $normalized['phone']);
        $this->assertEquals('nguyenvanb@example.com', $normalized['Email']);
        $this->assertEquals('Cung cấp phụ tùng thang cuốn', $normalized['Dịch vụ quan tâm']);
        $this->assertEquals('Báo giá lô 20 bộ xích thang cuốn tiêu chuẩn OEM', $normalized['Nội dung cần hỗ trợ']);
        $this->assertEquals('quote', $normalized['lead_type']);
        $this->assertEquals('product_quote_modal', $normalized['form_source']);
    }

    public function test_clear_brand_caches_flushes_frontend_shared_cache_keys(): void
    {
        Cache::put('global_settings', ['test' => true], 600);
        Cache::put('global_settings_vi', ['test' => true], 600);
        Cache::put('inertia_layout_shared_data_vi', ['test' => true], 600);
        Cache::put('home_page_data_vi', ['test' => true], 600);

        Config::clearBrandCaches();

        $this->assertNull(Cache::get('global_settings'));
        $this->assertNull(Cache::get('global_settings_vi'));
        $this->assertNull(Cache::get('inertia_layout_shared_data_vi'));
        $this->assertNull(Cache::get('home_page_data_vi'));
    }

    public function test_bilingual_general_settings_and_en_to_vi_fallback(): void
    {
        $raw = [
            'general_business_name' => 'Thành Khang Tecsat Việt Nam',
            'general_business_name_en' => 'Thanh Khang Tecsat Global',
            'general_site_name' => 'Thành Khang - Thiết bị công nghiệp',
            'general_site_name_en' => '',
            'general_address' => '54A Nguyễn Chí Thanh, Hà Nội',
            'general_address_en' => '54A Nguyen Chi Thanh, Hanoi',
            'general_working_hours' => 'Thứ 2 - Thứ 7: 08:00 - 17:30',
            'general_working_hours_en' => null,
        ];

        $enNormalized = Config::normalizeGeneralSettings($raw, 'en');
        $this->assertEquals('Thanh Khang Tecsat Global', $enNormalized['general_business_name']);
        $this->assertEquals('Thanh Khang Tecsat Global', $enNormalized['company_name']);
        // Fallback EN -> VI when EN is empty and VI exists in DB
        $this->assertEquals('Thành Khang - Thiết bị công nghiệp', $enNormalized['general_site_name']);
        $this->assertEquals('54A Nguyen Chi Thanh, Hanoi', $enNormalized['general_address']);
        $this->assertEquals('Thứ 2 - Thứ 7: 08:00 - 17:30', $enNormalized['general_working_hours']);

        $viNormalized = Config::normalizeGeneralSettings($raw, 'vi');
        $this->assertEquals('Thành Khang Tecsat Việt Nam', $viNormalized['general_business_name']);
        $this->assertEquals('54A Nguyễn Chí Thanh, Hà Nội', $viNormalized['general_address']);
    }

    public function test_bilingual_process_steps_service_steps_and_cache_invalidation(): void
    {
        app()->setLocale('en');

        // Zero-Fallback: empty DB returns []
        $emptyProcessEn = Config::getProcessSteps('en');
        $this->assertSame([], $emptyProcessEn);

        // Custom DB steps resolve EN -> VI fallback
        $customSteps = Config::getProcessSteps([
            'process_steps' => [
                [
                    'number' => '01',
                    'title' => 'Liên hệ tư vấn',
                    'title_en' => 'Consultation Contact',
                    'description' => 'Mô tả tiếng Việt bước 1',
                    'description_en' => '',
                ],
            ],
        ]);
        $this->assertCount(1, $customSteps);
        $this->assertEquals('Consultation Contact', $customSteps[0]['title']);
        $this->assertEquals('Mô tả tiếng Việt bước 1', $customSteps[0]['description']);

        $service = new \App\Models\Service();
        $this->assertEmpty($service->resolveSteps());
        $this->assertEmpty($service->resolveChecklistItems());

        Cache::put('home_page_data_vi', ['cached' => 1], 600);
        Cache::put('home_page_data_en', ['cached' => 1], 600);
        Cache::put('process_index_en', ['cached' => 1], 600);

        Slider::clearSliderCaches();

        $this->assertNull(Cache::get('home_page_data_vi'));
        $this->assertNull(Cache::get('home_page_data_en'));
        $this->assertNull(Cache::get('process_index_en'));

        app()->setLocale('vi');
    }

    public function test_fallback_locale_configuration_and_translatable_model_en_to_vi_fallback(): void
    {
        $this->assertSame('vi', config('app.fallback_locale'));
        $this->assertSame('vi', config('translatable.fallback_locale'));
        $this->assertSame(['vi', 'en'], config('translatable.locales'));
        $this->assertTrue((bool) config('translatable.use_fallback'));
        $this->assertTrue((bool) config('translatable.use_property_fallback'));

        $service = new \App\Models\Service([
            'status' => \App\Models\Service::STATUS_ACTIVE,
        ]);

        $viTranslation = $service->translateOrNew('vi');
        $viTranslation->locale = 'vi';
        $viTranslation->title = 'Dịch vụ bảo trì thang cuốn công nghiệp';
        $viTranslation->slug = 'dich-vu-bao-tri-thang-cuon';
        $viTranslation->description = 'Mô tả tiếng Việt cho dịch vụ kỹ thuật';
        $viTranslation->content_by_tab = [
            [
                'number' => '01',
                'title' => 'Bước 1 Tiếng Việt',
                'description' => 'Chi tiết bước 1 Tiếng Việt',
            ],
        ];

        $enTranslation = $service->translateOrNew('en');
        $enTranslation->locale = 'en';
        $enTranslation->title = '';
        $enTranslation->slug = '';
        $enTranslation->description = null;
        $enTranslation->content_by_tab = null;

        app()->setLocale('en');

        $this->assertSame('Dịch vụ bảo trì thang cuốn công nghiệp', $service->title);
        $this->assertSame('Mô tả tiếng Việt cho dịch vụ kỹ thuật', $service->description);

        $resolvedSteps = $service->resolveSteps();
        $this->assertSame('Bước 1 Tiếng Việt', $resolvedSteps[0]['title']);
        $this->assertSame('Chi tiết bước 1 Tiếng Việt', $resolvedSteps[0]['description']);

        app()->setLocale('vi');
    }

    public function test_home_cms_sync_end_to_end_updates_inertia_props_after_cache_invalidation(): void
    {
        // 1. Seed stale caches to verify invalidation works end-to-end
        Cache::put('home_page_data_vi', ['sliders' => [['title' => 'STALE']]], 600);
        Cache::put('home_page_data_en', ['sliders' => [['title' => 'STALE']]], 600);
        Cache::put('inertia_layout_shared_data_vi', ['policies' => []], 600);
        Cache::put('inertia_layout_shared_data_en', ['policies' => []], 600);

        // 2. Save General Settings (brand + Home About + Home Contact + SEO)
        settings()->group('general')->set([
            'general_logo' => json_encode(['path' => '/storage/cms-logo.png']),
            'general_business_name' => 'Công ty Kỹ thuật CMS Sync',
            'general_business_name_en' => 'CMS Sync Industrial Co.',
            'general_description' => 'Mô tả doanh nghiệp từ CMS',
            'general_description_en' => 'Company description from CMS',
            'showroom_address' => 'Showroom Hà Nội CMS',
            'general_facebook' => 'https://facebook.com/cms-sync-test',
            'general_linkedin' => 'https://linkedin.com/company/cms-sync-test',
            'map_iframe' => 'https://www.google.com/maps/embed?pb=cms-sync',
            'home_about_eyebrow' => 'VỀ CHÚNG TÔI CMS',
            'home_about_eyebrow_en' => 'ABOUT US CMS',
            'home_about_lead_highlight' => 'Dẫn đầu linh kiện thang cuốn',
            'home_about_paragraph_1' => 'Đoạn giới thiệu 1 từ CMS.',
            'home_contact_title' => 'Nhận báo giá kỹ thuật CMS',
            'home_contact_title_en' => 'Request Technical Quote CMS',
            'home_contact_description' => 'Mô tả liên hệ từ CMS.',
            'seo_meta_title' => 'SEO Title Từ CMS',
            'seo_meta_description' => 'SEO Description Từ CMS',
        ]);
        Config::clearBrandCaches();

        // 3. Create Slider (HOME_SLIDER) with highest position_sort
        $slider = new Slider([
            'status' => Slider::STATUS_ACTIVE,
            'position_display' => 'HOME_SLIDER',
            'position_sort' => 999,
        ]);
        $slider->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Hero Slide CMS VI',
            'primary_cta_text' => 'Xem sản phẩm',
            'primary_cta_link' => '/san-pham',
            'image' => ['path' => '/storage/hero-cms.webp', 'alt' => 'Hero VI'],
        ]);
        $slider->translateOrNew('en')->fill([
            'locale' => 'en',
            'title' => 'Hero Slide CMS EN',
            'primary_cta_text' => 'Explore Products',
            'primary_cta_link' => '/en/products',
            'image' => ['path' => '/storage/hero-cms.webp', 'alt' => 'Hero EN'],
        ]);
        $slider->save();

        // 4. Create ProductCategory
        $category = new ProductCategory([
            'status' => ProductCategory::STATUS_ACTIVE,
            'position' => 1,
            'image' => ['path' => '/storage/cat-cms.webp', 'alt' => 'Cat CMS'],
        ]);
        $category->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Danh mục Xích tải CMS',
            'slug' => 'danh-muc-xich-tai-cms',
        ]);
        $category->translateOrNew('en')->fill([
            'locale' => 'en',
            'title' => 'CMS Conveyor Chains',
            'slug' => 'cms-conveyor-chains',
        ]);
        $category->save();

        // 5. Create Featured Post (position 1 + highest id so scopeOrdered ranks it first)
        $post = new Post([
            'status' => Post::STATUS_ACTIVE,
            'type' => Post::TYPE_POST,
            'position' => 1,
            'is_featured' => 1,
            'published_at' => now()->subMinute(),
            'image' => ['path' => '/storage/post-cms.webp', 'alt' => 'Post CMS'],
        ]);
        $post->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Bài viết kỹ thuật CMS VI',
            'slug' => 'bai-viet-ky-thuat-cms-vi',
            'description' => 'Tóm tắt bài viết CMS VI',
            'content' => '<p>Nội dung bài viết CMS</p>',
        ]);
        $post->translateOrNew('en')->fill([
            'locale' => 'en',
            'title' => 'CMS Technical Article EN',
            'slug' => 'cms-technical-article-en',
            'description' => 'Article summary CMS EN',
            'content' => '<p>Article body CMS</p>',
        ]);
        $post->save();

        // 6. Create Policy (Footer)
        $policy = new Policy([
            'status' => Policy::STATUS_ACTIVE,
            'position' => 1,
        ]);
        $policy->translateOrNew('vi')->fill([
            'locale' => 'vi',
            'title' => 'Chính sách bảo hành CMS',
            'slug' => 'chinh-sach-bao-hanh-cms',
            'content' => '<p>Nội dung bảo hành</p>',
        ]);
        $policy->translateOrNew('en')->fill([
            'locale' => 'en',
            'title' => 'CMS Warranty Policy',
            'slug' => 'cms-warranty-policy',
            'content' => '<p>Warranty body</p>',
        ]);
        $policy->save();

        // 7. Create popular search Keyword
        Keyword::create([
            'keyword' => 'xích thang cuốn TL133',
            'search_count' => 42,
        ]);
        Config::clearBrandCaches();

        // 8. Request VI Home via Inertia
        $resVi = $this->get('/', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $resVi->assertStatus(200);
        $viData = $resVi->json();

        $this->assertSame('Home/Index', $viData['component']);
        $this->assertSame('Công ty Kỹ thuật CMS Sync', $viData['props']['global']['general_business_name']);
        $this->assertSame('/storage/cms-logo.png', $viData['props']['global']['general_logo']);
        $this->assertSame('/storage/cms-logo.png', $viData['props']['global']['logo']);
        $this->assertSame('https://linkedin.com/company/cms-sync-test', $viData['props']['global']['linkedin']);
        $this->assertSame('VỀ CHÚNG TÔI CMS', $viData['props']['aboutSection']['eyebrow']);
        $this->assertSame('Nhận báo giá kỹ thuật CMS', $viData['props']['contactSection']['title']);
        $this->assertContains('Hero Slide CMS VI', array_column($viData['props']['sliders'], 'title'));
        $this->assertContains('Danh mục Xích tải CMS', array_column($viData['props']['categories'], 'title'));
        $this->assertContains('Bài viết kỹ thuật CMS VI', array_column($viData['props']['featured_posts'], 'title'));
        $this->assertContains('xích thang cuốn TL133', $viData['props']['popular_keywords']);
        $viPolicy = collect($viData['props']['data']['policies'])->firstWhere('slug', 'chinh-sach-bao-hanh-cms');
        $this->assertNotNull($viPolicy);
        $this->assertSame('/chinh-sach/chinh-sach-bao-hanh-cms', $viPolicy['url']);
        $this->assertSame('SEO Title Từ CMS', $viData['props']['seo']['title']);
        $this->assertSame('SEO Description Từ CMS', $viData['props']['seo']['description']);

        // 9. Request EN Home via Inertia
        $resEn = $this->get('/en', [
            'X-Inertia' => 'true',
            'X-Inertia-Version' => '',
        ]);
        $resEn->assertStatus(200);
        $enData = $resEn->json();

        $this->assertSame('Home/Index', $enData['component']);
        $this->assertSame('CMS Sync Industrial Co.', $enData['props']['global']['general_business_name']);
        $this->assertSame('ABOUT US CMS', $enData['props']['aboutSection']['eyebrow']);
        $this->assertSame('Request Technical Quote CMS', $enData['props']['contactSection']['title']);
        $this->assertContains('Hero Slide CMS EN', array_column($enData['props']['sliders'], 'title'));
        $this->assertContains('CMS Conveyor Chains', array_column($enData['props']['categories'], 'title'));
        $this->assertContains('CMS Technical Article EN', array_column($enData['props']['featured_posts'], 'title'));
        $enPolicy = collect($enData['props']['data']['policies'])->firstWhere('slug', 'cms-warranty-policy');
        $this->assertNotNull($enPolicy);
        $this->assertSame('/en/policies/cms-warranty-policy', $enPolicy['url']);
    }
}
