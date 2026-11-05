<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class CmsSyncIntegrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_config_extracts_map_iframe_src_and_normalizes_general_settings(): void
    {
        $rawIframe = '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12" width="600" height="450"></iframe>';
        $extracted = Config::extractMapIframeSrc($rawIframe);

        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $extracted);

        $normalized = Config::normalizeGeneralSettings([
            'general_business_name' => 'HH Maker Studio',
            'general_phone' => '0938039170',
            'general_email' => 'contact@hhmaker.vn',
            'map_iframe' => $rawIframe,
        ]);

        $this->assertEquals('HH Maker Studio', $normalized['company_name']);
        $this->assertEquals('0938039170', $normalized['phone']);
        $this->assertEquals('0938039170', $normalized['hotline']);
        $this->assertEquals('contact@hhmaker.vn', $normalized['email']);
        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $normalized['map_iframe']);
        $this->assertEquals('https://www.google.com/maps/embed?pb=!1m18!1m12', $normalized['general_map_iframe']);
    }

    public function test_contact_normalize_lead_data_populates_bilingual_keys_and_lead_type(): void
    {
        $normalized = Contact::normalizeLeadData([
            'name' => 'Nguyễn Văn B',
            'phone' => '0906636425',
            'email' => 'nguyenvanb@example.com',
            'product_title' => 'Mô hình cơ khí SLA',
            'service' => 'In 3D SLA',
            'message' => 'Báo giá mẫu in resin chi tiết cao',
        ], 'CONTACT_FORM');

        $this->assertEquals('Nguyễn Văn B', $normalized['Họ và tên']);
        $this->assertEquals('Nguyễn Văn B', $normalized['name']);
        $this->assertEquals('0906636425', $normalized['Số điện thoại']);
        $this->assertEquals('0906636425', $normalized['phone']);
        $this->assertEquals('nguyenvanb@example.com', $normalized['Email']);
        $this->assertEquals('In 3D SLA', $normalized['Dịch vụ quan tâm']);
        $this->assertEquals('Báo giá mẫu in resin chi tiết cao', $normalized['Nội dung cần hỗ trợ']);
        $this->assertEquals('quote', $normalized['lead_type']);
        $this->assertEquals('product_quote_modal', $normalized['form_source']);
    }

    public function test_clear_brand_caches_flushes_frontend_shared_cache_keys(): void
    {
        Cache::put('global_settings', ['test' => true], 600);
        Cache::put('inertia_layout_shared_data_vi', ['test' => true], 600);
        Cache::put('home_page_data_vi', ['test' => true], 600);

        Config::clearBrandCaches();

        $this->assertNull(Cache::get('global_settings'));
        $this->assertNull(Cache::get('inertia_layout_shared_data_vi'));
        $this->assertNull(Cache::get('home_page_data_vi'));
    }

    public function test_bilingual_general_settings_and_en_to_vi_fallback(): void
    {
        $raw = [
            'general_business_name' => 'HH Maker Việt Nam',
            'general_business_name_en' => 'HH Maker Global',
            'general_site_name' => 'HH Maker - Dịch vụ In 3D',
            'general_site_name_en' => '',
            'general_address' => '65 Đường số 9, TP.HCM',
            'general_address_en' => '65 Street 9, HCMC',
            'general_working_hours' => 'Thứ 2 - Thứ 7: 08:00 - 18:00',
            'general_working_hours_en' => null,
        ];

        $enNormalized = Config::normalizeGeneralSettings($raw, 'en');
        $this->assertEquals('HH Maker Global', $enNormalized['general_business_name']);
        $this->assertEquals('HH Maker Global', $enNormalized['company_name']);
        // Fallback EN -> VI when EN is empty
        $this->assertEquals('HH Maker - Dịch vụ In 3D', $enNormalized['general_site_name']);
        $this->assertEquals('65 Street 9, HCMC', $enNormalized['general_address']);
        $this->assertEquals('Thứ 2 - Thứ 7: 08:00 - 18:00', $enNormalized['general_working_hours']);

        $viNormalized = Config::normalizeGeneralSettings($raw, 'vi');
        $this->assertEquals('HH Maker Việt Nam', $viNormalized['general_business_name']);
        $this->assertEquals('65 Đường số 9, TP.HCM', $viNormalized['general_address']);
    }

    public function test_bilingual_process_steps_service_steps_and_faqs_with_fallback(): void
    {
        app()->setLocale('en');

        $defaultProcessEn = Config::getProcessSteps([]);
        $this->assertCount(0, $defaultProcessEn);

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
        $this->assertEquals('Consultation Contact', $customSteps[0]['title']);
        $this->assertEquals('Mô tả tiếng Việt bước 1', $customSteps[0]['description']);

        $service = new \App\Models\Service();
        $this->assertEmpty($service->resolveSteps());
        $this->assertEmpty($service->resolveChecklistItems());

        $faqsEn = \App\Models\Post\Post::getFrontendFaqs('en');
        $this->assertEmpty($faqsEn);

        Cache::put('home_page_data_vi', ['cached' => 1], 600);
        Cache::put('home_page_data_en', ['cached' => 1], 600);
        Cache::put('process_index_en', ['cached' => 1], 600);

        \JamstackVietnam\Slider\Models\Slider::clearSliderCaches();

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
        $viTranslation->title = 'Dịch vụ In 3D Công nghiệp';
        $viTranslation->slug = 'dich-vu-in-3d-cong-nghiep';
        $viTranslation->description = 'Mô tả tiếng Việt cho dịch vụ in 3D';
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

        $this->assertSame('Dịch vụ In 3D Công nghiệp', $service->title);
        $this->assertSame('Mô tả tiếng Việt cho dịch vụ in 3D', $service->description);

        $resolvedSteps = $service->resolveSteps();
        $this->assertSame('Bước 1 Tiếng Việt', $resolvedSteps[0]['title']);
        $this->assertSame('Chi tiết bước 1 Tiếng Việt', $resolvedSteps[0]['description']);

        app()->setLocale('vi');
    }
}
