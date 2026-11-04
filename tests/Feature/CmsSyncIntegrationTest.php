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
}
