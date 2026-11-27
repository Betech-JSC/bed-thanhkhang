<?php

use App\Models\Config;
use App\Models\Policy\Policy;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $this->syncSettings($now);
        $this->syncSliders($now);
        $this->syncPolicies($now);
        $this->syncHomePosts($now);
        $this->syncProductCatalog($now);
        $this->scrubTecsatEverywhere();
        $this->clearAllCaches();
    }

    public function down(): void
    {
        // Non-destructive rollback to protect CMS-managed content and catalog data.
    }

    protected function cleanTecsat(?string $value): ?string
    {
        if ($value === null || stripos($value, 'tecsat') === false) {
            return $value;
        }

        $cleaned = preg_replace('/\s*\(\s*THANH\s+KHANG\s+Tecsat\s*\)/iu', '', $value);
        $cleaned = preg_replace('/thanhkhangtecsat/iu', 'dvktthanhkhang', $cleaned);
        $cleaned = preg_replace('/(Thành\s+Khang)\s+Tecsat/u', '$1', $cleaned);
        $cleaned = preg_replace('/(THÀNH\s+KHANG)\s+TECSAT/u', '$1', $cleaned);
        $cleaned = preg_replace('/(Thanh\s+Khang)\s+Tecsat/iu', '$1', $cleaned);
        $cleaned = preg_replace('/\s*Tecsat\b/iu', '', $cleaned);
        $cleaned = preg_replace('/[ \t]{2,}/', ' ', $cleaned);

        return trim($cleaned);
    }

    protected function upsertSetting(string $group, string $name, ?string $value, $now): void
    {
        $existing = DB::table('settings')
            ->where('group', $group)
            ->where('name', $name)
            ->first();

        if ($existing) {
            DB::table('settings')
                ->where('id', $existing->id)
                ->update([
                    'val' => $value,
                    'updated_at' => $now,
                ]);
        } else {
            DB::table('settings')->insert([
                'group' => $group,
                'name' => $name,
                'val' => $value,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    protected function syncSettings($now): void
    {
        if (!Schema::hasTable('settings')) {
            return;
        }

        $logoPath = '/assets/images/logo/logo-thanhkhang.png';
        $businessNameVi = 'Công ty TNHH Thương mại và Dịch vụ Kỹ thuật Thành Khang';
        $businessNameEn = 'Thanh Khang Trading and Technical Services Co., Ltd';
        $descriptionVi = 'nhà phân phối các sản phẩm chính hãng trong lĩnh vực vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp tại Việt Nam';
        $descriptionEn = 'authorized distributor of genuine elevator – escalator spare parts, components, and industrial equipment in Vietnam';
        $hqVi = 'Tầng 16, toà nhà TNR, số 54A Nguyễn Chí Thanh, phường Láng, thành phố Hà Nội, Việt Nam';
        $hqEn = '16th Floor, TNR Tower, 54A Nguyen Chi Thanh, Lang Ward, Hanoi, Vietnam';
        $showroomVi = 'NTT23 Thống Nhất Complex, 82 Nguyễn Tuân, phường Thanh Xuân, thành phố Hà Nội, Việt Nam';
        $showroomEn = 'NTT23 Thong Nhat Complex, 82 Nguyen Tuan, Thanh Xuan Ward, Hanoi, Vietnam';
        $facebookUrl = 'https://www.facebook.com/dvktthanhkhang';
        $linkedinUrl = 'https://www.linkedin.com/company/thanh-khang-engineering';

        $generalSettings = [
            'general_logo' => $logoPath,
            'logo' => $logoPath,

            'general_business_name' => $businessNameVi,
            'general_business_name_vi' => $businessNameVi,
            'company_name' => $businessNameVi,
            'general_business_name_en' => $businessNameEn,
            'company_name_en' => $businessNameEn,

            'copyright_name' => 'THÀNH KHANG',
            'copyright_name_en' => 'THANH KHANG',
            'general_site_name' => 'THÀNH KHANG',
            'general_site_name_vi' => 'THÀNH KHANG',
            'site_name' => 'THÀNH KHANG',
            'general_site_name_en' => 'THANH KHANG',
            'site_name_en' => 'THANH KHANG',

            'general_description' => $descriptionVi,
            'general_description_vi' => $descriptionVi,
            'description' => $descriptionVi,
            'general_description_en' => $descriptionEn,
            'description_en' => $descriptionEn,

            'general_phone' => '0967 732 665',
            'phone' => '0967 732 665',
            'hotline' => '0967 732 665',
            'general_email' => 'info@dvktthanhkhang.vn',
            'email' => 'info@dvktthanhkhang.vn',
            'general_tax_code' => '107621881',
            'tax_code' => '107621881',

            'general_address' => $hqVi,
            'general_address_vi' => $hqVi,
            'address' => $hqVi,
            'headquarters_address' => $hqVi,
            'general_address_en' => $hqEn,
            'address_en' => $hqEn,
            'headquarters_address_en' => $hqEn,

            'showroom_address' => $showroomVi,
            'office_address' => $showroomVi,
            'showroom_address_en' => $showroomEn,
            'office_address_en' => $showroomEn,

            'general_facebook' => $facebookUrl,
            'facebook' => $facebookUrl,
            'social_facebook' => $facebookUrl,
            'general_linkedin' => $linkedinUrl,
            'linkedin' => $linkedinUrl,
            'social_linkedin' => $linkedinUrl,
            'general_zalo' => 'https://zalo.me/0967732665',
            'zalo' => 'https://zalo.me/0967732665',
            'social_zalo' => 'https://zalo.me/0967732665',

            'seo_meta_title' => 'Thành Khang - Linh kiện thang cuốn, thang nâng ô tô & thiết bị công nghiệp',
            'seo_meta_title_en' => 'Thanh Khang - Escalator Parts, Car Lift Chains & Industrial Equipment',
            'seo_meta_description' => $businessNameVi . ' — ' . $descriptionVi . '.',
            'seo_meta_description_en' => $businessNameEn . ' — ' . $descriptionEn . '.',

            // Section 3: Home About Us (exact Figma structure, zero Tecsat)
            'home_about_eyebrow' => 'Về chúng tôi',
            'home_about_eyebrow_en' => 'About Us',
            'home_about_lead_highlight' => 'Công ty TNHH Thương Mại và Dịch Vụ Kỹ Thuật Thành Khang',
            'home_about_lead_highlight_en' => 'Thanh Khang Trading and Technical Services Co., Ltd',
            'home_about_lead_rest' => 'là nhà phân phối các sản phẩm chính hãng trong lĩnh vực vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp tại Việt Nam.',
            'home_about_lead_rest_en' => 'is an authorized distributor of genuine elevator – escalator spare parts, components, and industrial equipment in Vietnam.',
            'home_about_paragraph_1' => 'Thành Khang không chỉ cung cấp sản phẩm – chúng tôi mang đến giải pháp kỹ thuật toàn diện: từ tư vấn lựa chọn đúng sản phẩm, đến hỗ trợ kỹ thuật trong quá trình sử dụng sản phẩm và dịch vụ sau bán hàng.',
            'home_about_paragraph_1_en' => 'Thanh Khang does not merely supply products — we deliver comprehensive technical solutions: from product selection consulting to hands-on technical support during operation and reliable after-sales service.',
            'home_about_paragraph_2' => 'Với bề dày kinh nghiệm và hệ thống kho hàng có sẵn, sản phẩm của Thành Khang đã phục vụ hàng trăm công trình, dự án trên toàn quốc.',
            'home_about_paragraph_2_en' => 'Backed by years of engineering expertise and ready-to-ship warehouse inventory, Thanh Khang products have powered hundreds of projects and facilities nationwide.',
            'home_about_commitment_quote' => '"Đúng nguồn gốc – Đúng thông số – Đúng tiến độ"',
            'home_about_commitment_quote_en' => '"Genuine Origin – Exact Specifications – On-Time Delivery"',
            'home_about_commitment_sub' => 'Đó là cam kết chúng tôi mang đến cho mỗi khách hàng',
            'home_about_commitment_sub_en' => 'That is the commitment we deliver to every customer',
            'home_about_cta_text' => 'Xem thêm về chúng tôi',
            'home_about_cta_text_en' => 'Learn more about us',
            'home_about_cta_link' => '/gioi-thieu',
            'home_about_cta_link_en' => '/en/about-us',

            // Section 7: Home Contact / Quote Form
            'home_contact_title' => 'KẾT NỐI & NHẬN BÁO GIÁ CÁC SẢN PHẨM TẠI THÀNH KHANG',
            'home_contact_title_en' => 'CONNECT & REQUEST A QUOTE FOR PRODUCTS AT THANH KHANG',
            'home_contact_description' => 'Xích công nghiệp chính hãng, đa dạng kích thước, đáp ứng nhiều điều kiện làm việc khác nhau. Đội ngũ kỹ thuật tư vấn lựa chọn đúng loại xích theo tải trọng và ứng dụng thực tế, giúp quý khách tối ưu chi phí và hạn chế gián đoạn sản xuất',
            'home_contact_description_en' => 'Genuine industrial chains in a wide range of sizes to suit diverse operating conditions. Our engineering team advises on selecting the right chain for your actual load and application, helping optimize costs and minimize production downtime.',
        ];

        foreach ($generalSettings as $key => $value) {
            $this->upsertSetting('general', $key, $value, $now);
        }
    }

    protected function upsertSliderGroup(string $positionDisplay, array $items, $now): void
    {
        $existingSliders = DB::table('sliders')
            ->whereNull('deleted_at')
            ->where('position_display', $positionDisplay)
            ->orderByDesc('position_sort')
            ->orderBy('id')
            ->get()
            ->values();

        foreach ($items as $idx => $item) {
            $slider = $existingSliders->get($idx);
            $sort = $item['position_sort'];

            if ($slider) {
                $sliderId = $slider->id;
                DB::table('sliders')
                    ->where('id', $sliderId)
                    ->update([
                        'status' => 'ACTIVE',
                        'position_sort' => $sort,
                        'updated_at' => $now,
                    ]);
            } else {
                $sliderId = DB::table('sliders')->insertGetId([
                    'status' => 'ACTIVE',
                    'position_display' => $positionDisplay,
                    'position_sort' => $sort,
                    'is_video' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            foreach (['vi', 'en'] as $locale) {
                if (!isset($item[$locale])) {
                    continue;
                }
                $transData = $item[$locale];
                $existingTrans = DB::table('slider_translations')
                    ->where('slider_id', $sliderId)
                    ->where('locale', $locale)
                    ->first();

                // Preserve existing image if present and valid, otherwise apply default
                $imageJson = null;
                if ($existingTrans && !empty($existingTrans->image)) {
                    $decodedImg = json_decode((string) $existingTrans->image, true);
                    if (is_array($decodedImg) && !empty($decodedImg['path'])) {
                        if (isset($decodedImg['alt']) && is_string($decodedImg['alt'])) {
                            $decodedImg['alt'] = $this->cleanTecsat($decodedImg['alt']);
                        }
                        $imageJson = json_encode($decodedImg, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    }
                }
                if ($imageJson === null && !empty($transData['image'])) {
                    $imageJson = json_encode($transData['image'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }

                $payload = [
                    'title' => $transData['title'],
                    'subtitle' => $transData['subtitle'] ?? null,
                    'description' => $transData['description'] ?? null,
                    'link' => $transData['link'] ?? null,
                    'primary_cta_text' => $transData['primary_cta_text'] ?? null,
                    'primary_cta_link' => $transData['primary_cta_link'] ?? null,
                    'secondary_cta_text' => $transData['secondary_cta_text'] ?? null,
                    'secondary_cta_link' => $transData['secondary_cta_link'] ?? null,
                    'image' => $imageJson,
                ];

                if ($existingTrans) {
                    DB::table('slider_translations')
                        ->where('id', $existingTrans->id)
                        ->update($payload);
                } else {
                    DB::table('slider_translations')->insert(array_merge($payload, [
                        'slider_id' => $sliderId,
                        'locale' => $locale,
                    ]));
                }
            }
        }
    }

    protected function syncSliders($now): void
    {
        if (!Schema::hasTable('sliders') || !Schema::hasTable('slider_translations')) {
            return;
        }

        // Section 1B: Hero Banner (3 slides, sort DESC: 30, 20, 10)
        $this->upsertSliderGroup('HOME_SLIDER', [
            [
                'position_sort' => 30,
                'vi' => [
                    'subtitle' => 'Enduring motion',
                    'title' => 'CHUYỂN ĐỘNG BỀN BỈ',
                    'description' => 'Cung cấp giải pháp truyền động và thiết bị cơ khí chính xác, tối ưu hóa hiệu suất vận hành và đảm bảo độ tin cậy vượt trội cho mọi dây chuyền sản xuất công nghiệp',
                    'primary_cta_text' => 'Xem sản phẩm',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Liên hệ tư vấn',
                    'secondary_cta_link' => '/lien-he',
                    'link' => '/san-pham',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'CHUYỂN ĐỘNG BỀN BỈ - Thành Khang'],
                ],
                'en' => [
                    'subtitle' => 'Enduring motion',
                    'title' => 'ENDURING MOTION',
                    'description' => 'Delivering precision power transmission solutions and industrial mechanical equipment that optimize operational performance and ensure superior reliability for every production line',
                    'primary_cta_text' => 'View products',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Contact consultation',
                    'secondary_cta_link' => '/en/contact',
                    'link' => '/en/products',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'ENDURING MOTION - Thanh Khang'],
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => 'Precision engineering',
                    'title' => 'PHỤ TÙNG THANG CUỐN & THANG NÂNG Ô TÔ',
                    'description' => 'Tổng kho xích bậc thang cuốn, xích bãi đỗ xe lập thể và linh kiện cơ khí chuẩn OEM với đầy đủ chứng chỉ xuất xứ CO/CQ cho các tòa nhà và trung tâm thương mại',
                    'primary_cta_text' => 'Xem sản phẩm',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Liên hệ tư vấn',
                    'secondary_cta_link' => '/lien-he',
                    'link' => '/san-pham',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'Phụ tùng thang cuốn và thang nâng ô tô - Thành Khang'],
                ],
                'en' => [
                    'subtitle' => 'Precision engineering',
                    'title' => 'ESCALATOR & CAR PARKING LIFT PARTS',
                    'description' => 'Comprehensive stock of OEM-grade escalator step chains, multi-level car parking chains, and precision mechanical components with complete CO/CQ certifications',
                    'primary_cta_text' => 'View products',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Contact consultation',
                    'secondary_cta_link' => '/en/contact',
                    'link' => '/en/products',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'Escalator and car parking lift parts - Thanh Khang'],
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => 'Industrial reliability',
                    'title' => 'XÍCH TẢI CÔNG NGHIỆP UNIVERSAL CHAINS',
                    'description' => 'Giải pháp xích băng tải hành lý sân bay, dây chuyền đóng gói và xích tải hạng nặng đạt chuẩn ISO/DIN/ANSI, đồng hành cùng vận hành liên tục 24/7 của doanh nghiệp',
                    'primary_cta_text' => 'Xem sản phẩm',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Liên hệ tư vấn',
                    'secondary_cta_link' => '/lien-he',
                    'link' => '/san-pham',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'Xích tải công nghiệp Universal Chains - Thành Khang'],
                ],
                'en' => [
                    'subtitle' => 'Industrial reliability',
                    'title' => 'UNIVERSAL CHAINS INDUSTRIAL CONVEYOR CHAINS',
                    'description' => 'ISO/DIN/ANSI compliant airport baggage conveyor chains, packaging line chains, and heavy-duty roller chains engineered for 24/7 continuous industrial operation',
                    'primary_cta_text' => 'View products',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Contact consultation',
                    'secondary_cta_link' => '/en/contact',
                    'link' => '/en/products',
                    'image' => ['path' => '/assets/images/homepage/hero-banner-1.png', 'alt' => 'Universal Chains industrial chains - Thanh Khang'],
                ],
            ],
        ], $now);

        // Section 2 Tile 6: Explore All Products CTA Banner
        $this->upsertSliderGroup('BANNER_HOME_CATEGORIES_CTA', [
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => 'Danh mục sản phẩm',
                    'title' => 'KHÁM PHÁ TOÀN BỘ SẢN PHẨM',
                    'link' => '/san-pham',
                    'primary_cta_link' => '/san-pham',
                    'image' => ['path' => '/assets/images/homepage/cat-explore-all.png', 'alt' => 'Khám phá toàn bộ sản phẩm Thành Khang'],
                ],
                'en' => [
                    'subtitle' => 'Product Categories',
                    'title' => 'EXPLORE ALL PRODUCTS',
                    'link' => '/en/products',
                    'primary_cta_link' => '/en/products',
                    'image' => ['path' => '/assets/images/homepage/cat-explore-all.png', 'alt' => 'Explore all Thanh Khang products'],
                ],
            ],
        ], $now);

        // Section 3: Home About Gallery (3 images)
        $this->upsertSliderGroup('HOME_ABOUT_GALLERY', [
            [
                'position_sort' => 30,
                'vi' => [
                    'title' => 'Bánh răng truyền động chính xác Thành Khang',
                    'image' => ['path' => '/assets/images/homepage/about-gear-1.png', 'alt' => 'Bánh răng truyền động chính xác Thành Khang'],
                ],
                'en' => [
                    'title' => 'Thanh Khang precision transmission gears',
                    'image' => ['path' => '/assets/images/homepage/about-gear-1.png', 'alt' => 'Thanh Khang precision transmission gears'],
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'title' => 'Xích tải công nghiệp chính hãng Universal Chains',
                    'image' => ['path' => '/assets/images/homepage/about-chain-2.png', 'alt' => 'Xích tải công nghiệp chính hãng Universal Chains'],
                ],
                'en' => [
                    'title' => 'Genuine Universal Chains industrial conveyor chains',
                    'image' => ['path' => '/assets/images/homepage/about-chain-2.png', 'alt' => 'Genuine Universal Chains industrial conveyor chains'],
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'title' => 'Kỹ sư Thành Khang lắp đặt và kiểm định băng tải công nghiệp',
                    'image' => ['path' => '/assets/images/homepage/about-engineer-3.png', 'alt' => 'Kỹ sư Thành Khang lắp đặt và kiểm định băng tải công nghiệp'],
                ],
                'en' => [
                    'title' => 'Thanh Khang engineers installing and inspecting industrial conveyors',
                    'image' => ['path' => '/assets/images/homepage/about-engineer-3.png', 'alt' => 'Thanh Khang engineers installing and inspecting industrial conveyors'],
                ],
            ],
        ], $now);

        // Section 4: Why Choose Us Banner + 5 Pillars
        $this->upsertSliderGroup('BANNER_HOME_TRUST', [
            [
                'position_sort' => 10,
                'vi' => [
                    'title' => 'Vì sao khách hàng tin tưởng chọn Thành Khang?',
                    'description' => '5 giá trị cốt lõi tạo nên niềm tin của các đối tác công nghiệp tại Thành Khang',
                    'image' => ['path' => '/assets/images/homepage/why-choose-us-bg.png', 'alt' => 'Vì sao khách hàng tin tưởng chọn Thành Khang?'],
                ],
                'en' => [
                    'title' => 'Why do customers trust Thanh Khang?',
                    'description' => '5 core values that earn the trust of industrial partners at Thanh Khang',
                    'image' => ['path' => '/assets/images/homepage/why-choose-us-bg.png', 'alt' => 'Why do customers trust Thanh Khang?'],
                ],
            ],
        ], $now);

        $this->upsertSliderGroup('HOME_TRUST_PILLARS', [
            [
                'position_sort' => 50,
                'vi' => [
                    'title' => 'Uy tín tận tâm',
                    'description' => 'Đặt sự hài lòng của khách hàng về chất lượng sản phẩm và dịch vụ lên hàng đầu',
                    'image' => ['path' => '/assets/svg/icons/trust-thumbs-up.svg', 'alt' => 'Uy tín tận tâm'],
                ],
                'en' => [
                    'title' => 'Dedicated reliability',
                    'description' => 'Placing customer satisfaction in product quality and service above all else',
                    'image' => ['path' => '/assets/svg/icons/trust-thumbs-up.svg', 'alt' => 'Dedicated reliability'],
                ],
            ],
            [
                'position_sort' => 40,
                'vi' => [
                    'title' => 'Hàng chính hãng',
                    'description' => 'Sản phẩm nhập khẩu có chứng từ CO/CQ minh bạch, cam kết bảo hành chính hãng',
                    'image' => ['path' => '/assets/svg/icons/trust-check-circle.svg', 'alt' => 'Hàng chính hãng'],
                ],
                'en' => [
                    'title' => 'Genuine products',
                    'description' => 'Imported products with transparent CO/CQ certificates and official warranty',
                    'image' => ['path' => '/assets/svg/icons/trust-check-circle.svg', 'alt' => 'Genuine products'],
                ],
            ],
            [
                'position_sort' => 30,
                'vi' => [
                    'title' => 'Quy trình tối ưu',
                    'description' => 'Quy trình đặt hàng nhanh gọn, thuận tiện, giúp khách hàng dễ dàng quản lý dữ liệu đơn hàng',
                    'image' => ['path' => '/assets/svg/icons/trust-clipboard.svg', 'alt' => 'Quy trình tối ưu'],
                ],
                'en' => [
                    'title' => 'Streamlined process',
                    'description' => 'Fast and convenient ordering workflow helping clients track orders effortlessly',
                    'image' => ['path' => '/assets/svg/icons/trust-clipboard.svg', 'alt' => 'Streamlined process'],
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'title' => 'Giao hàng nhanh',
                    'description' => 'Giao hàng toàn quốc 24/7, kể cả các ngày cuối tuần',
                    'image' => ['path' => '/assets/svg/icons/trust-truck.svg', 'alt' => 'Giao hàng nhanh'],
                ],
                'en' => [
                    'title' => 'Fast delivery',
                    'description' => 'Nationwide 24/7 delivery, including weekends',
                    'image' => ['path' => '/assets/svg/icons/trust-truck.svg', 'alt' => 'Fast delivery'],
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'title' => 'Hỗ trợ kỹ thuật',
                    'description' => 'Đội ngũ kỹ thuật tư vấn lựa chọn sản phẩm phù hợp theo dự án của khách hàng',
                    'image' => ['path' => '/assets/svg/icons/trust-tool.svg', 'alt' => 'Hỗ trợ kỹ thuật'],
                ],
                'en' => [
                    'title' => 'Technical support',
                    'description' => 'Engineering specialists advising the exact components for every project',
                    'image' => ['path' => '/assets/svg/icons/trust-tool.svg', 'alt' => 'Technical support'],
                ],
            ],
        ], $now);

        // Section 5: Process Banner + 4 Steps (exact Figma concise Step 3 copy for 3 lines / 166px)
        $this->upsertSliderGroup('BANNER_HOME_PROCESS', [
            [
                'position_sort' => 10,
                'vi' => [
                    'title' => 'QUY TRÌNH TIẾP NHẬN & LÀM VIỆC TẠI THÀNH KHANG',
                    'description' => 'Xích công nghiệp chính hãng, đa dạng kích thước, đáp ứng nhiều điều kiện làm việc khác nhau. Đội ngũ kỹ thuật tư vấn lựa chọn đúng loại xích theo tải trọng và ứng dụng thực tế, giúp quý khách tối ưu chi phí và hạn chế gián đoạn sản xuất',
                    'image' => ['path' => '/assets/images/homepage/process-left-bg.png', 'alt' => 'Quy trình tiếp nhận và làm việc tại Thành Khang'],
                ],
                'en' => [
                    'title' => 'ORDERING & WORKFLOW PROCESS AT THANH KHANG',
                    'description' => 'Genuine industrial chains in a wide range of sizes to meet diverse working conditions. Our technical team helps select the right chain for your load and application, optimizing costs and minimizing downtime.',
                    'image' => ['path' => '/assets/images/homepage/process-left-bg.png', 'alt' => 'Ordering and workflow process at Thanh Khang'],
                ],
            ],
        ], $now);

        $this->upsertSliderGroup('HOME_PROCESS_STEPS', [
            [
                'position_sort' => 40,
                'vi' => [
                    'subtitle' => '1',
                    'title' => 'Gửi yêu cầu',
                    'description' => 'Cung cấp thông tin về loại xích, thông số kỹ thuật, số lượng và ứng dụng thực tế qua biểu mẫu báo giá, email hoặc hotline',
                ],
                'en' => [
                    'subtitle' => '1',
                    'title' => 'Submit request',
                    'description' => 'Share chain type, technical specifications, quantities, and application via quote form, email, or hotline.',
                ],
            ],
            [
                'position_sort' => 30,
                'vi' => [
                    'subtitle' => '2',
                    'title' => 'Tư vấn & Báo giá',
                    'description' => 'Kỹ thuật viên xác nhận thông số, tư vấn dòng xích phù hợp và gửi báo giá.',
                ],
                'en' => [
                    'subtitle' => '2',
                    'title' => 'Consultation & Quote',
                    'description' => 'Engineers verify specifications, recommend the right chain series, and issue a detailed quotation.',
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => '3',
                    'title' => 'Sản xuất & kiểm định',
                    'description' => 'Cung cấp thông tin về loại xích, thông số kỹ thuật, số lượng và ứng dụng thực tế. Bạn có thể gửi qua biểu mẫu báo giá',
                ],
                'en' => [
                    'subtitle' => '3',
                    'title' => 'Production & QC',
                    'description' => 'Precision manufacturing and strict quality inspection with complete CO/CQ certification prior to dispatch.',
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => '4',
                    'title' => 'Giao hàng & hỗ trợ',
                    'description' => 'Đóng gói cẩn thận, giao đúng tiến độ và đồng hành hỗ trợ kỹ thuật sau bán hàng.',
                ],
                'en' => [
                    'subtitle' => '4',
                    'title' => 'Delivery & Support',
                    'description' => 'Careful industrial packaging, on-time delivery, and ongoing after-sales technical support.',
                ],
            ],
        ], $now);
    }

    protected function syncPolicies($now): void
    {
        if (!Schema::hasTable('policies') || !Schema::hasTable('policy_translations')) {
            return;
        }

        $policies = [
            [
                'position' => 1,
                'vi_slug' => 'chinh-sach-ban-hang',
                'vi_title' => 'Chính sách bán hàng',
                'en_slug' => 'sales-policy',
                'en_title' => 'Sales Policy',
                'vi_content' => '<p>Công ty TNHH Thương mại và Dịch vụ Kỹ thuật Thành Khang cam kết cung cấp sản phẩm chính hãng, đầy đủ chứng từ CO/CQ, báo giá minh bạch và hỗ trợ kỹ thuật tận tâm cho mọi khách hàng doanh nghiệp.</p>',
                'en_content' => '<p>Thanh Khang Trading and Technical Services Co., Ltd is committed to supplying genuine products with complete CO/CQ documentation, transparent quotations, and dedicated engineering support.</p>',
            ],
            [
                'position' => 2,
                'vi_slug' => 'chinh-sach-thanh-toan',
                'vi_title' => 'Chính sách thanh toán',
                'en_slug' => 'payment-policy',
                'en_title' => 'Payment Policy',
                'vi_content' => '<p>Hỗ trợ thanh toán linh hoạt bằng chuyển khoản doanh nghiệp, xuất hóa đơn VAT hợp lệ theo từng đơn hàng và hợp đồng cung ứng.</p>',
                'en_content' => '<p>Flexible corporate bank transfer payment options with valid VAT invoicing for every commercial order and supply contract.</p>',
            ],
            [
                'position' => 3,
                'vi_slug' => 'chinh-sach-van-chuyen',
                'vi_title' => 'Chính sách vận chuyển',
                'en_slug' => 'shipping-policy',
                'en_title' => 'Shipping Policy',
                'vi_content' => '<p>Giao hàng toàn quốc 24/7 tận chân công trình và nhà máy, đóng gói theo tiêu chuẩn công nghiệp chống ẩm, chống va đập.</p>',
                'en_content' => '<p>Nationwide 24/7 delivery directly to job sites and factories with shockproof and moisture-resistant industrial packaging.</p>',
            ],
            [
                'position' => 4,
                'vi_slug' => 'chinh-sach-doi-tra',
                'vi_title' => 'Chính sách đổi trả hàng',
                'en_slug' => 'return-exchange-policy',
                'en_title' => 'Return & Exchange Policy',
                'vi_content' => '<p>Hỗ trợ đổi trả nhanh chóng đối với sản phẩm lỗi kỹ thuật từ nhà sản xuất hoặc sai khác thông số so với đơn đặt hàng đã xác nhận.</p>',
                'en_content' => '<p>Prompt replacement or return for manufacturing defects or any specification discrepancy against confirmed purchase orders.</p>',
            ],
            [
                'position' => 5,
                'vi_slug' => 'chinh-sach-bao-hanh',
                'vi_title' => 'Chính sách bảo hành',
                'en_slug' => 'warranty-policy',
                'en_title' => 'Warranty Policy',
                'vi_content' => '<p>Tất cả sản phẩm phân phối bởi Thành Khang được bảo hành chính hãng 12–24 tháng kèm hỗ trợ kỹ thuật chuyên sâu trong suốt quá trình vận hành.</p>',
                'en_content' => '<p>All products distributed by Thanh Khang carry an official 12–24 month manufacturer warranty backed by continuous technical support.</p>',
            ],
            [
                'position' => 6,
                'vi_slug' => 'chinh-sach-bao-mat',
                'vi_title' => 'Chính sách bảo mật thông tin',
                'en_slug' => 'privacy-policy',
                'en_title' => 'Privacy Policy',
                'vi_content' => '<p>Thành Khang cam kết bảo mật tuyệt đối thông tin doanh nghiệp, bản vẽ kỹ thuật và dữ liệu giao dịch của khách hàng.</p>',
                'en_content' => '<p>Thanh Khang strictly safeguards corporate profiles, engineering drawings, and commercial transaction data of all clients.</p>',
            ],
        ];

        foreach ($policies as $item) {
            $viTrans = DB::table('policy_translations')
                ->where('locale', 'vi')
                ->where('slug', $item['vi_slug'])
                ->first();

            $policyId = $viTrans ? $viTrans->policy_id : null;

            if ($policyId) {
                DB::table('policies')
                    ->where('id', $policyId)
                    ->update([
                        'position' => $item['position'],
                        'status' => 'ACTIVE',
                        'deleted_at' => null,
                        'updated_at' => $now,
                    ]);
            } else {
                $policyId = DB::table('policies')->insertGetId([
                    'position' => $item['position'],
                    'status' => 'ACTIVE',
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // VI translation: update exact Figma title while preserving existing rich content
            if ($viTrans) {
                DB::table('policy_translations')
                    ->where('id', $viTrans->id)
                    ->update([
                        'title' => $item['vi_title'],
                        'content' => !empty($viTrans->content) ? $this->cleanTecsat($viTrans->content) : $item['vi_content'],
                        'seo_meta_title' => $item['vi_title'] . ' - Thành Khang',
                    ]);
            } else {
                DB::table('policy_translations')->insert([
                    'policy_id' => $policyId,
                    'locale' => 'vi',
                    'title' => $item['vi_title'],
                    'slug' => $item['vi_slug'],
                    'content' => $item['vi_content'],
                    'seo_meta_title' => $item['vi_title'] . ' - Thành Khang',
                ]);
            }

            // EN translation
            $enTrans = DB::table('policy_translations')
                ->where('policy_id', $policyId)
                ->where('locale', 'en')
                ->first();

            if ($enTrans) {
                DB::table('policy_translations')
                    ->where('id', $enTrans->id)
                    ->update([
                        'title' => $item['en_title'],
                        'content' => !empty($enTrans->content) ? $this->cleanTecsat($enTrans->content) : $item['en_content'],
                        'seo_meta_title' => $item['en_title'] . ' - Thanh Khang',
                    ]);
            } else {
                DB::table('policy_translations')->insert([
                    'policy_id' => $policyId,
                    'locale' => 'en',
                    'title' => $item['en_title'],
                    'slug' => $item['en_slug'],
                    'content' => $item['en_content'],
                    'seo_meta_title' => $item['en_title'] . ' - Thanh Khang',
                ]);
            }
        }
    }

    protected function upsertPostCategory(string $viSlug, string $viTitle, string $enSlug, string $enTitle, int $position, $now): int
    {
        $existingVi = DB::table('post_category_translations')
            ->where('locale', 'vi')
            ->where('slug', $viSlug)
            ->first();

        if ($existingVi) {
            $catId = (int) $existingVi->post_category_id;
            DB::table('post_categories')
                ->where('id', $catId)
                ->update([
                    'status' => 'ACTIVE',
                    'position' => $position,
                    'deleted_at' => null,
                    'updated_at' => $now,
                ]);
            DB::table('post_category_translations')
                ->where('id', $existingVi->id)
                ->update(['title' => $viTitle]);
        } else {
            $catId = DB::table('post_categories')->insertGetId([
                'parent_id' => 0,
                'position' => $position,
                'status' => 'ACTIVE',
                'type' => 'POST',
                'view_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
            DB::table('post_category_translations')->insert([
                'post_category_id' => $catId,
                'locale' => 'vi',
                'title' => $viTitle,
                'slug' => $viSlug,
            ]);
        }

        $existingEn = DB::table('post_category_translations')
            ->where('post_category_id', $catId)
            ->where('locale', 'en')
            ->first();

        if ($existingEn) {
            DB::table('post_category_translations')
                ->where('id', $existingEn->id)
                ->update(['title' => $enTitle]);
        } else {
            DB::table('post_category_translations')->insert([
                'post_category_id' => $catId,
                'locale' => 'en',
                'title' => $enTitle,
                'slug' => $enSlug,
            ]);
        }

        return $catId;
    }

    protected function syncHomePosts($now): void
    {
        if (
            !Schema::hasTable('posts')
            || !Schema::hasTable('post_translations')
            || !Schema::hasTable('post_categories')
            || !Schema::hasTable('post_category_translations')
            || !Schema::hasTable('post_ref_categories')
        ) {
            return;
        }

        $techCatId = $this->upsertPostCategory(
            'kien-thuc-ky-thuat',
            'Kiến thức kỹ thuật',
            'technical-insights',
            'Technical Insights',
            1,
            $now
        );
        $maintCatId = $this->upsertPostCategory(
            'bao-tri-va-van-hanh',
            'Bảo trì & Vận hành',
            'maintenance-and-operations',
            'Maintenance & Operations',
            2,
            $now
        );

        $figmaPosts = [
            [
                'position' => 1,
                'is_featured' => 1,
                'published_at' => '2026-11-18 09:00:00',
                'category_id' => $techCatId,
                'image' => '/assets/images/homepage/blog-ansi-roller-chain.png',
                'vi_slug' => 'xich-con-lan-tieu-chuan-ansi-la-gi-kien-thuc-co-ban-cho-nguoi-moi',
                'vi_title' => 'Xích con lăn tiêu chuẩn ANSI là gì? Kiến thức cơ bản cho người mới',
                'vi_desc' => 'Tìm hiểu cấu tạo, ký hiệu bước xích, cách đọc thông số kỹ thuật và ứng dụng phổ biến của xích con lăn tiêu chuẩn ANSI trong công nghiệp.',
                'en_slug' => 'what-is-ansi-standard-roller-chain-basics-for-beginners',
                'en_title' => 'What is an ANSI Standard Roller Chain? Essential Basics for Beginners',
                'en_desc' => 'Understand roller chain anatomy, pitch designations, sizing standards, and key industrial applications of ANSI roller chains.',
            ],
            [
                'position' => 2,
                'is_featured' => 1,
                'published_at' => '2026-11-18 09:00:00',
                'category_id' => $maintCatId,
                'image' => '/assets/images/homepage/blog-escalator-chain-maintenance.png',
                'vi_slug' => 'bao-tri-xich-thang-cuon-lich-kiem-tra-boi-tron-va-tieu-chi-thay-the',
                'vi_title' => 'Bảo trì xích thang cuốn: Lịch kiểm tra, bôi trơn và tiêu chí thay thế',
                'vi_desc' => 'Hướng dẫn chi tiết quy trình kiểm tra độ giãn bước xích, chế độ bôi trơn định kỳ và tiêu chuẩn thay thế an toàn cho xích bậc thang cuốn.',
                'en_slug' => 'escalator-chain-maintenance-inspection-lubrication-replacement',
                'en_title' => 'Escalator Chain Maintenance: Inspection Schedule, Lubrication & Replacement Criteria',
                'en_desc' => 'Step-by-step guide to measuring pitch elongation, proper lubrication intervals, and safety replacement criteria for escalator step chains.',
            ],
            [
                'position' => 3,
                'is_featured' => 0,
                'published_at' => '2026-11-18 09:00:00',
                'category_id' => $techCatId,
                'image' => '/assets/images/homepage/blog-industrial-conveyor-chain.png',
                'vi_slug' => 'ung-dung-xich-tai-cong-nghiep-chon-dung-loai-cho-tung-day-chuyen',
                'vi_title' => 'Ứng dụng xích tải công nghiệp: chọn đúng loại cho từng dây chuyền',
                'vi_desc' => 'Phân tích đặc tính các dòng xích tải M/FV series, xích chốt rỗng và xích con lăn trên giúp lựa chọn tối ưu cho từng dây chuyền sản xuất.',
                'en_slug' => 'industrial-conveyor-chain-applications-selecting-right-chain',
                'en_title' => 'Industrial Conveyor Chain Applications: Selecting the Right Chain for Every Line',
                'en_desc' => 'Compare M/FV series, hollow pin, and top roller conveyor chains to specify the optimal drive solution for your production line.',
            ],
        ];

        foreach ($figmaPosts as $item) {
            $viTrans = DB::table('post_translations')
                ->where('locale', 'vi')
                ->where(function ($q) use ($item) {
                    $q->where('slug', $item['vi_slug'])
                        ->orWhere('title', $item['vi_title']);
                })
                ->first();

            if ($viTrans) {
                $postId = (int) $viTrans->post_id;
                $existingPost = DB::table('posts')->where('id', $postId)->first();
                $updatePost = [
                    'position' => $item['position'],
                    'is_featured' => $item['is_featured'],
                    'published_at' => $item['published_at'],
                    'status' => 'ACTIVE',
                    'deleted_at' => null,
                    'updated_at' => $now,
                ];
                if ($existingPost && empty($existingPost->image)) {
                    $updatePost['image'] = json_encode(['path' => $item['image'], 'alt' => $item['vi_title']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                }
                DB::table('posts')->where('id', $postId)->update($updatePost);
                DB::table('post_translations')
                    ->where('id', $viTrans->id)
                    ->update([
                        'title' => $item['vi_title'],
                        'status_locale' => 'ACTIVE',
                    ]);
            } else {
                $postId = DB::table('posts')->insertGetId([
                    'image' => json_encode(['path' => $item['image'], 'alt' => $item['vi_title']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                    'published_at' => $item['published_at'],
                    'is_home' => 1,
                    'is_featured' => $item['is_featured'],
                    'position' => $item['position'],
                    'status' => 'ACTIVE',
                    'type' => 'POST',
                    'view_count' => 0,
                    'price' => 0,
                    'virtual_views' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

                DB::table('post_translations')->insert([
                    'post_id' => $postId,
                    'locale' => 'vi',
                    'title' => $item['vi_title'],
                    'slug' => $item['vi_slug'],
                    'description' => $item['vi_desc'],
                    'content' => '<p>' . $item['vi_desc'] . '</p>',
                    'status_locale' => 'ACTIVE',
                ]);
            }

            // Ensure EN translation exists
            $enTrans = DB::table('post_translations')
                ->where('post_id', $postId)
                ->where('locale', 'en')
                ->first();
            if (!$enTrans) {
                DB::table('post_translations')->insert([
                    'post_id' => $postId,
                    'locale' => 'en',
                    'title' => $item['en_title'],
                    'slug' => $item['en_slug'],
                    'description' => $item['en_desc'],
                    'content' => '<p>' . $item['en_desc'] . '</p>',
                    'status_locale' => 'ACTIVE',
                ]);
            }

            // Sync exact Figma category tag for this post
            DB::table('post_ref_categories')->where('post_id', $postId)->delete();
            DB::table('post_ref_categories')->insert([
                'post_id' => $postId,
                'post_category_id' => $item['category_id'],
            ]);
        }
    }

    protected function upsertCategory(array $spec, int $parentId, $now): int
    {
        $existingVi = DB::table('product_category_translations')
            ->join('product_categories', 'product_categories.id', '=', 'product_category_translations.product_category_id')
            ->where('product_category_translations.locale', 'vi')
            ->where('product_category_translations.slug', $spec['vi_slug'])
            ->select('product_category_translations.*', 'product_categories.image as cat_image')
            ->first();

        if ($existingVi) {
            $catId = (int) $existingVi->product_category_id;
            $update = [
                'parent_id' => $parentId,
                'position' => $spec['position'],
                'status' => 'ACTIVE',
                'deleted_at' => null,
                'updated_at' => $now,
            ];
            // Preserve existing category image if already populated; set Figma asset if empty
            if (empty($existingVi->cat_image) && !empty($spec['image'])) {
                $update['image'] = json_encode([
                    'path' => $spec['image'],
                    'alt' => $spec['vi_title'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            DB::table('product_categories')->where('id', $catId)->update($update);

            $viUpdate = ['title' => $spec['vi_title']];
            if (empty($existingVi->description) && !empty($spec['vi_desc'])) {
                $viUpdate['description'] = $spec['vi_desc'];
            }
            DB::table('product_category_translations')->where('id', $existingVi->id)->update($viUpdate);
        } else {
            $catId = DB::table('product_categories')->insertGetId([
                'parent_id' => $parentId,
                'position' => $spec['position'],
                'status' => 'ACTIVE',
                'image' => !empty($spec['image'])
                    ? json_encode(['path' => $spec['image'], 'alt' => $spec['vi_title']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                    : null,
                'view_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('product_category_translations')->insert([
                'product_category_id' => $catId,
                'locale' => 'vi',
                'title' => $spec['vi_title'],
                'slug' => $spec['vi_slug'],
                'description' => $spec['vi_desc'] ?? null,
            ]);
        }

        $existingEn = DB::table('product_category_translations')
            ->where('product_category_id', $catId)
            ->where('locale', 'en')
            ->first();

        if ($existingEn) {
            $enUpdate = ['title' => $spec['en_title']];
            if (empty($existingEn->description) && !empty($spec['en_desc'])) {
                $enUpdate['description'] = $spec['en_desc'];
            }
            DB::table('product_category_translations')->where('id', $existingEn->id)->update($enUpdate);
        } else {
            DB::table('product_category_translations')->insert([
                'product_category_id' => $catId,
                'locale' => 'en',
                'title' => $spec['en_title'],
                'slug' => $spec['en_slug'],
                'description' => $spec['en_desc'] ?? null,
            ]);
        }

        return $catId;
    }

    protected function upsertProductAndLink(array $spec, int $categoryId, int $position, $now): int
    {
        $sku = $spec['sku'];
        $altSkus = $spec['alt_skus'] ?? [];
        $viSlug = $spec['vi_slug'];

        // 1. Try match by exact VI slug
        $existingTrans = DB::table('product_translations')
            ->where('locale', 'vi')
            ->where('slug', $viSlug)
            ->first();

        $productId = $existingTrans ? (int) $existingTrans->product_id : null;

        // 2. Fallback match by SKU / alternate SKUs
        if (!$productId) {
            $candidateSkus = array_values(array_unique(array_merge([$sku], $altSkus)));
            $existingProduct = DB::table('products')
                ->whereNull('deleted_at')
                ->whereIn('sku', $candidateSkus)
                ->first();
            if ($existingProduct) {
                $productId = (int) $existingProduct->id;
            }
        }

        if ($productId) {
            $existingProduct = DB::table('products')->where('id', $productId)->first();
            $update = [
                'status' => 'ACTIVE',
                'deleted_at' => null,
                'updated_at' => $now,
            ];
            if ($existingProduct && empty($existingProduct->image) && !empty($spec['image'])) {
                $update['image'] = json_encode([
                    'path' => $spec['image'],
                    'alt' => $spec['vi_title'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
            }
            DB::table('products')->where('id', $productId)->update($update);
        } else {
            $imgJson = !empty($spec['image'])
                ? json_encode(['path' => $spec['image'], 'alt' => $spec['vi_title']], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
                : null;

            $productId = DB::table('products')->insertGetId([
                'status' => 'ACTIVE',
                'is_featured' => 0,
                'is_new' => 0,
                'is_stock' => 1,
                'position' => $position,
                'view_count' => 0,
                'sku' => $sku,
                'image' => $imgJson,
                'images' => $imgJson ? json_encode([json_decode($imgJson, true)], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('product_translations')->insert([
                'product_id' => $productId,
                'locale' => 'vi',
                'title' => $spec['vi_title'],
                'slug' => $viSlug,
                'description' => $spec['vi_desc'],
                'content' => '<p>' . e($spec['vi_desc']) . '</p>',
                'seo_meta_title' => $spec['vi_title'] . ' - Thành Khang',
                'seo_meta_description' => $spec['vi_desc'],
            ]);
        }

        // Ensure VI & EN translations exist
        if (!DB::table('product_translations')->where('product_id', $productId)->where('locale', 'vi')->exists()) {
            DB::table('product_translations')->insert([
                'product_id' => $productId,
                'locale' => 'vi',
                'title' => $spec['vi_title'],
                'slug' => $viSlug,
                'description' => $spec['vi_desc'],
                'content' => '<p>' . e($spec['vi_desc']) . '</p>',
            ]);
        }
        if (!DB::table('product_translations')->where('product_id', $productId)->where('locale', 'en')->exists()) {
            DB::table('product_translations')->insert([
                'product_id' => $productId,
                'locale' => 'en',
                'title' => $spec['en_title'],
                'slug' => $spec['en_slug'],
                'description' => $spec['en_desc'],
                'content' => '<p>' . e($spec['en_desc']) . '</p>',
            ]);
        }

        // Attach M:N category-product pivot idempotently
        $linkExists = DB::table('product_ref_categories')
            ->where('product_id', $productId)
            ->where('product_category_id', $categoryId)
            ->exists();
        if (!$linkExists) {
            DB::table('product_ref_categories')->insert([
                'product_id' => $productId,
                'product_category_id' => $categoryId,
            ]);
        }

        return $productId;
    }

    protected function makeProductSpec(
        string $sku,
        string $viPrefix,
        string $enPrefix,
        string $viSlugPrefix,
        string $enSlugPrefix,
        string $image,
        array $altSkus = [],
        ?string $customViTitle = null,
        ?string $customViSlug = null
    ): array {
        $slugSuffix = Str::slug(str_replace('.', '-', $sku));
        $viTitle = $customViTitle ?? ($viPrefix . ' ' . $sku);
        $enTitle = $enPrefix . ' ' . $sku;
        $viSlug = $customViSlug ?? ($viSlugPrefix . '-' . $slugSuffix);
        $enSlug = $enSlugPrefix . '-' . $slugSuffix;

        return [
            'sku' => $sku,
            'alt_skus' => $altSkus,
            'vi_title' => $viTitle,
            'en_title' => $enTitle,
            'vi_slug' => $viSlug,
            'en_slug' => $enSlug,
            'vi_desc' => $viTitle . ' chính hãng do Thành Khang phân phối, đầy đủ chứng từ CO/CQ và hỗ trợ kỹ thuật toàn quốc.',
            'en_desc' => 'Genuine ' . $enTitle . ' distributed by Thanh Khang with full CO/CQ documentation and nationwide technical support.',
            'image' => $image,
        ];
    }

    protected function syncProductCatalog($now): void
    {
        if (
            !Schema::hasTable('product_categories')
            || !Schema::hasTable('product_category_translations')
            || !Schema::hasTable('products')
            || !Schema::hasTable('product_translations')
            || !Schema::hasTable('product_ref_categories')
        ) {
            return;
        }

        // =====================================================================
        // 5 ROOT CATEGORIES — EXACT FIGMA ORDER (1..5)
        // =====================================================================
        $rootEscalator = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'linh-kien-thang-cuon',
            'vi_title' => 'Linh kiện thang cuốn',
            'vi_desc' => 'Tổng kho tay vịn thang cuốn, xích bậc thang cuốn, xích băng tải hành khách, xích dẫn động, xích tay vịn và bánh xe thang cuốn chính hãng.',
            'en_slug' => 'escalator-spare-parts',
            'en_title' => 'Escalator Spare Parts',
            'en_desc' => 'Complete inventory of escalator handrails, step chains, passenger conveyor chains, main drive chains, handrail drive chains, and rollers.',
            'image' => '/assets/images/homepage/cat-linh-kien-thang-cuon.png',
        ], 0, $now);

        $rootCarLift = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'linh-kien-thang-nang-o-to',
            'vi_title' => 'Linh kiện thang nâng ô tô',
            'vi_desc' => 'Xích bãi đỗ xe lập thể, tuần hoàn đứng và xích dẫn động chịu tải trọng lớn cho hệ thống đỗ xe thông minh.',
            'en_slug' => 'car-parking-lift-components',
            'en_title' => 'Car Parking Lift Components',
            'en_desc' => 'Heavy-duty chains for multi-level puzzle parking, vertical rotary car parking lifts, and drive assemblies.',
            'image' => '/assets/images/homepage/cat-linh-kien-thang-nang-o-to.png',
        ], 0, $now);

        $rootPackaging = $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'linh-kien-day-chuyen-dong-goi',
            'vi_title' => 'Linh kiện dây chuyền đóng gói',
            'vi_desc' => 'Xích chốt rỗng, xích con lăn trên, xích cong/chống cong, xích inox và xích tải M/FV series cho dây chuyền đóng gói và băng tải nhẹ.',
            'en_slug' => 'packaging-line-components',
            'en_title' => 'Packaging Line Components',
            'en_desc' => 'Hollow pin chains, top roller chains, side-bow/anti-bow chains, stainless steel chains, and M/FV series conveyor chains.',
            'image' => '/assets/images/homepage/cat-linh-kien-day-chuyen-dong-goi.png',
        ], 0, $now);

        $rootBaggage = $this->upsertCategory([
            'position' => 4,
            'vi_slug' => 'he-thong-bang-tai-hanh-ly',
            'vi_title' => 'Hệ thống băng tải hành lý',
            'vi_desc' => 'Xích con lăn cạnh và xích con lăn trên chuyên dụng cho băng tải hành lý sân bay và kho vận logistics.',
            'en_slug' => 'baggage-conveyor-systems',
            'en_title' => 'Baggage Conveyor Systems',
            'en_desc' => 'Side roller and top roller conveyor chains engineered for airport baggage handling and logistics systems.',
            'image' => '/assets/images/homepage/cat-he-thong-bang-tai-hanh-ly.png',
        ], 0, $now);

        $rootEnergy = $this->upsertCategory([
            'position' => 5,
            'vi_slug' => 'giai-phap-tiet-kiem-dien',
            'vi_title' => 'Giải pháp tiết kiệm điện',
            'vi_desc' => 'Giải pháp tiết kiệm điện năng cho nhà xưởng, trung tâm thương mại và kho vận với công nghệ quạt công nghiệp động cơ BLDC hiệu suất cao.',
            'en_slug' => 'energy-saving-solutions',
            'en_title' => 'Energy Saving Solutions',
            'en_desc' => 'High-efficiency industrial energy-saving solutions featuring BLDC HVLS fans for factories, warehouses, and commercial buildings.',
            'image' => '/assets/images/homepage/cat-giai-phap-tiet-kiem-dien.png',
        ], 0, $now);

        // =====================================================================
        // ROOT 1: LINH KIỆN THANG CUỐN (3 L1 + 6 L2 + 47 Products)
        // =====================================================================
        $l1Handrail = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'tay-vin-thang-cuon',
            'vi_title' => 'Tay vịn thang cuốn',
            'vi_desc' => 'Tay vịn thang cuốn cao su SBR, CSM và Hybrid chịu mài mòn cao, vận hành êm ái và bền bỉ.',
            'en_slug' => 'escalator-handrails',
            'en_title' => 'Escalator Handrails',
            'en_desc' => 'High-durability SBR, CSM, and Hybrid escalator handrails engineered for smooth, reliable operation.',
            'image' => '/assets/images/homepage/cat-linh-kien-thang-cuon.png',
        ], $rootEscalator, $now);

        $l1EscalatorChains = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-thang-cuon',
            'vi_title' => 'Xích thang cuốn',
            'en_slug' => 'escalator-chains',
            'en_title' => 'Escalator Chains',
        ], $rootEscalator, $now);

        $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'banh-xe-thang-cuon',
            'vi_title' => 'Bánh xe thang cuốn',
            'vi_desc' => 'Bánh xe bậc thang cuốn, bánh xe xích và con lăn dẫn hướng chịu tải cao, giảm ồn hiệu quả.',
            'en_slug' => 'escalator-rollers-and-wheels',
            'en_title' => 'Escalator Rollers & Wheels',
            'en_desc' => 'High-load escalator step rollers, chain rollers, and low-noise guide wheels.',
        ], $rootEscalator, $now);

        // L1 Tay vịn thang cuốn -> SBR, CSM, Hybrid (3)
        foreach (['SBR', 'CSM', 'Hybrid'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec(
                    $sku,
                    'Tay vịn thang cuốn',
                    'Escalator Handrail',
                    'tay-vin-thang-cuon',
                    'escalator-handrail',
                    '/assets/images/homepage/cat-linh-kien-thang-cuon.png'
                ),
                $l1Handrail,
                $idx + 1,
                $now
            );
        }

        // L2 under Xích thang cuốn
        $l2CommercialStep = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-bac-thang-thuong-mai',
            'vi_title' => 'Xích bậc thang thương mại',
            'en_slug' => 'commercial-escalator-step-chains',
            'en_title' => 'Commercial Escalator Step Chains',
        ], $l1EscalatorChains, $now);

        $l2PassengerConveyor = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-bang-tai-hanh-khach',
            'vi_title' => 'Xích băng tải hành khách',
            'en_slug' => 'moving-walkway-chains',
            'en_title' => 'Moving Walkway Chains',
        ], $l1EscalatorChains, $now);

        $l2MaintenanceFree = $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'xich-khong-can-bao-tri',
            'vi_title' => 'Xích không cần bảo trì',
            'en_slug' => 'maintenance-free-escalator-chains',
            'en_title' => 'Maintenance-Free Escalator Chains',
        ], $l1EscalatorChains, $now);

        $l2PublicStep = $this->upsertCategory([
            'position' => 4,
            'vi_slug' => 'xich-bac-thang-cong-cong',
            'vi_title' => 'Xích bậc thang công cộng',
            'en_slug' => 'public-transport-escalator-step-chains',
            'en_title' => 'Public Transport Escalator Step Chains',
        ], $l1EscalatorChains, $now);

        $l2MainDrive = $this->upsertCategory([
            'position' => 5,
            'vi_slug' => 'xich-dan-dong-chinh',
            'vi_title' => 'Xích dẫn động chính',
            'en_slug' => 'main-drive-and-precision-roller-chains',
            'en_title' => 'Main Drive & Precision Roller Chains',
        ], $l1EscalatorChains, $now);

        $l2HandrailChain = $this->upsertCategory([
            'position' => 6,
            'vi_slug' => 'xich-tay-vin',
            'vi_title' => 'Xích tay vịn',
            'en_slug' => 'handrail-driving-chains',
            'en_title' => 'Handrail Driving Chains',
        ], $l1EscalatorChains, $now);

        // Xích bậc thang thương mại (5)
        foreach (['TL133', 'TL133H', 'TL135', 'TL135Hx', 'TL135Hx1'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích bậc thang cuốn thương mại', 'Commercial Escalator Step Chain', 'xich-bac-thang-cuon-thuong-mai', 'commercial-escalator-step-chain', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp'),
                $l2CommercialStep,
                $idx + 1,
                $now
            );
        }

        // Xích băng tải hành khách (3)
        foreach (['RL133', 'RL133H', 'RL135'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích băng tải hành khách', 'Moving Walkway Chain', 'xich-bang-tai-hanh-khach', 'moving-walkway-chain', '/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp'),
                $l2PassengerConveyor,
                $idx + 1,
                $now
            );
        }

        // Xích không cần bảo trì (2)
        foreach (['TL133c', 'TL133Hg'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích không cần bảo trì', 'Maintenance-Free Escalator Chain', 'xich-khong-can-bao-tri', 'maintenance-free-escalator-chain', '/images/products/thanhkhang/xich-khong-can-bao-tri/no-maintenance-escalator-step-chain.webp'),
                $l2MaintenanceFree,
                $idx + 1,
                $now
            );
        }

        // Xích bậc thang công cộng (11)
        foreach (['TL135Ha', 'TL135Hb', 'TL135Hc', 'TL135Hd', 'TL133Hqb3', 'TL133Hqb16', 'TL133Hqc1b', 'TL133Hqc1e', 'TL133Hqc1d', 'TL133Hqd9a', 'TL133Hqd6a'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích bậc thang cuốn công cộng', 'Public Transport Escalator Step Chain', 'xich-bac-thang-cuon-cong-cong', 'public-transport-escalator-step-chain', '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp'),
                $l2PublicStep,
                $idx + 1,
                $now
            );
        }

        // Xích dẫn động chính (19)
        $mainDriveDirect = ['16A-2F', '16B-2F', '20A-2F', '20A-3', '20B-3', '20AFT-2', '20AFT-3', '20B-2F', '24A-2', '24B-2'];
        foreach ($mainDriveDirect as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích dẫn động chính', 'Main Drive Chain', 'xich-dan-dong-chinh', 'main-drive-chain', '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp'),
                $l2MainDrive,
                $idx + 1,
                $now
            );
        }
        $mainDrivePrecision = ['16A-1', '16A-2', '20A-1', '20A-2', '16B-1', '16B-2', '20B-1', '20B-2', '40B-1'];
        foreach ($mainDrivePrecision as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích con lăn bước ngắn chính xác', 'Short Pitch Precision Roller Chain', 'xich-con-lan-buoc-ngan-chinh-xac', 'short-pitch-precision-roller-chain', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-single.webp'),
                $l2MainDrive,
                count($mainDriveDirect) + $idx + 1,
                $now
            );
        }

        // Xích tay vịn (4) — preserve existing HR-* SKUs to avoid collision with main drive 16B-1/16B-2/16A-2
        $handrailChains = [
            ['HR-16B-1', '16B-1', '/images/products/thanhkhang/xich-tay-vin/handrail-16b-1-sheet-vi.webp'],
            ['HR-16B-2', '16B-2', '/images/products/thanhkhang/xich-tay-vin/handrail-16b-2-sheet-vi.webp'],
            ['HR-16A-2', '16A-2', '/images/products/thanhkhang/xich-tay-vin/handrail-16a-2-sheet-vi.webp'],
            ['HR-16AFT-2', '16AFT-2', '/images/products/thanhkhang/xich-tay-vin/handrail-16aft-2-sheet-vi.webp'],
        ];
        foreach ($handrailChains as $idx => [$sku, $code, $img]) {
            $this->upsertProductAndLink(
                $this->makeProductSpec(
                    $sku,
                    'Xích tay vịn',
                    'Handrail Driving Chain',
                    'xich-tay-vin',
                    'handrail-driving-chain',
                    $img,
                    [],
                    'Xích tay vịn ' . $code,
                    'xich-tay-vin-' . Str::slug($code)
                ),
                $l2HandrailChain,
                $idx + 1,
                $now
            );
        }

        // =====================================================================
        // ROOT 2: LINH KIỆN THANG NÂNG Ô TÔ (1 L1 + 3 L2 + 8 Products)
        // =====================================================================
        $l1Parking = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-bai-do-xe',
            'vi_title' => 'Xích bãi đỗ xe',
            'en_slug' => 'car-parking-lift-chains',
            'en_title' => 'Car Parking Lift Chains',
        ], $rootCarLift, $now);

        $l2MultiLevelParking = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-bai-do-xe-lap-the',
            'vi_title' => 'Xích bãi đỗ xe lập thể',
            'en_slug' => 'multi-level-parking-chains',
            'en_title' => 'Multi-Level Parking Chains',
        ], $l1Parking, $now);

        $l2VerticalParking = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-bai-do-xe-tuan-hoan-dung',
            'vi_title' => 'Xích bãi đỗ xe tuần hoàn đứng',
            'en_slug' => 'vertical-circulation-parking-chains',
            'en_title' => 'Vertical Circulation Parking Chains',
        ], $l1Parking, $now);

        $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'xich-dan-dong-bai-do-xe',
            'vi_title' => 'Xích dẫn động',
            'vi_desc' => 'Xích dẫn động chuyên dụng chịu lực kéo cao cho hệ thống thang nâng và bãi đỗ xe tự động.',
            'en_slug' => 'parking-lift-drive-chains',
            'en_title' => 'Drive Chains',
            'en_desc' => 'High-tensile drive chains engineered for automated car parking lifts and elevators.',
        ], $l1Parking, $now);

        // Xích bãi đỗ xe lập thể (7)
        foreach (['LT40-1', 'LT80-1', 'LT80-2', 'LT100-1', 'LT100-2', 'LT100-3', 'LT120-1'] as $idx => $sku) {
            $img = $sku === 'LT40-1'
                ? '/images/products/thanhkhang/xich-bai-do-xe/lt40-1-photo.webp'
                : '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp';
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích bãi đỗ xe lập thể', 'Multi-Level Parking Chain', 'xich-bai-do-xe-lap-the', 'multi-level-parking-chain', $img),
                $l2MultiLevelParking,
                $idx + 1,
                $now
            );
        }

        // Xích bãi đỗ xe tuần hoàn đứng (1)
        $this->upsertProductAndLink(
            $this->makeProductSpec('LTAL666', 'Xích bãi đỗ xe tuần hoàn đứng', 'Vertical Circulation Parking Chain', 'xich-bai-do-xe-tuan-hoan-dung', 'vertical-circulation-parking-chain', '/images/products/thanhkhang/xich-bai-do-xe/ltal666-photo.webp'),
            $l2VerticalParking,
            1,
            $now
        );

        // =====================================================================
        // ROOT 3: LINH KIỆN DÂY CHUYỀN ĐÓNG GÓI (2 L1 + 8 L2)
        // =====================================================================
        $l1PackagingChains = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-day-chuyen-dong-goi',
            'vi_title' => 'Xích dây chuyền đóng gói',
            'en_slug' => 'packaging-line-chains',
            'en_title' => 'Packaging Line Chains',
        ], $rootPackaging, $now);

        $l1LightConveyor = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-bang-tai-nhe',
            'vi_title' => 'Xích băng tải nhẹ',
            'en_slug' => 'light-conveyor-chains',
            'en_title' => 'Light Conveyor Chains',
        ], $rootPackaging, $now);

        $l2PkgHollowPin = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-chot-rong-dong-goi',
            'vi_title' => 'Xích chốt rỗng',
            'en_slug' => 'hollow-pin-chains-packaging',
            'en_title' => 'Hollow Pin Chains (Packaging)',
        ], $l1PackagingChains, $now);

        $l2PkgTopRoller = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-con-lan-tren-dong-goi',
            'vi_title' => 'Xích con lăn trên',
            'en_slug' => 'top-roller-chains-packaging',
            'en_title' => 'Top Roller Chains (Packaging)',
        ], $l1PackagingChains, $now);

        $l2PkgSideBow = $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'xich-cong-chong-cong',
            'vi_title' => 'Xích cong/chống cong',
            'en_slug' => 'side-bow-and-anti-side-bow-chains',
            'en_title' => 'Side Bow & Anti-Side Bow Chains',
        ], $l1PackagingChains, $now);

        $this->upsertCategory([
            'position' => 4,
            'vi_slug' => 'xich-inox-dong-goi',
            'vi_title' => 'Xích inox',
            'vi_desc' => 'Xích thép không gỉ SUS304/SUS316 chống ăn mòn cho dây chuyền đóng gói thực phẩm, dược phẩm và môi trường ẩm ướt.',
            'en_slug' => 'stainless-steel-packaging-chains',
            'en_title' => 'Stainless Steel Chains',
            'en_desc' => 'Corrosion-resistant SUS304/SUS316 stainless steel conveyor chains for food, pharmaceutical, and wet packaging lines.',
        ], $l1PackagingChains, $now);

        $l2LightHollowPin = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-chot-rong-bang-tai-nhe',
            'vi_title' => 'Xích chốt rỗng',
            'en_slug' => 'hollow-pin-chains-light-conveyor',
            'en_title' => 'Hollow Pin Chains (Light Conveyor)',
        ], $l1LightConveyor, $now);

        $l2LightTopRoller = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-con-lan-tren-bang-tai-nhe',
            'vi_title' => 'Xích con lăn trên',
            'en_slug' => 'top-roller-chains-light-conveyor',
            'en_title' => 'Top Roller Chains (Light Conveyor)',
        ], $l1LightConveyor, $now);

        $l2LightMSeries = $this->upsertCategory([
            'position' => 3,
            'vi_slug' => 'xich-m-series',
            'vi_title' => 'Xích M series',
            'en_slug' => 'm-series-conveyor-chains',
            'en_title' => 'M Series Conveyor Chains',
        ], $l1LightConveyor, $now);

        $l2LightFVSeries = $this->upsertCategory([
            'position' => 4,
            'vi_slug' => 'xich-fv-series',
            'vi_title' => 'Xích FV series',
            'en_slug' => 'fv-series-conveyor-chains',
            'en_title' => 'FV Series Conveyor Chains',
        ], $l1LightConveyor, $now);

        // =====================================================================
        // ROOT 4: HỆ THỐNG BĂNG TẢI HÀNH LÝ (1 L1 + 2 L2)
        // =====================================================================
        $l1BaggageChains = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-bang-tai-hanh-ly-logistics',
            'vi_title' => 'Xích băng tải hành lý/logistics',
            'en_slug' => 'baggage-logistics-conveyor-chains',
            'en_title' => 'Baggage & Logistics Conveyor Chains',
        ], $rootBaggage, $now);

        $l2SideRoller = $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'xich-con-lan-canh',
            'vi_title' => 'Xích con lăn cạnh',
            'en_slug' => 'side-roller-conveyor-chains',
            'en_title' => 'Side Roller Conveyor Chains',
        ], $l1BaggageChains, $now);

        $l2BaggageTopRoller = $this->upsertCategory([
            'position' => 2,
            'vi_slug' => 'xich-con-lan-tren-hanh-ly',
            'vi_title' => 'Xích con lăn trên',
            'en_slug' => 'top-roller-conveyor-chains-logistics',
            'en_title' => 'Top Roller Conveyor Chains (Logistics)',
        ], $l1BaggageChains, $now);

        // Side Roller (5): 40-SR..100-SR
        foreach (['40-SR', '50-SR', '60-SR', '80-SR', '100-SR'] as $idx => $sku) {
            $alt = str_replace('-', '', $sku);
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích con lăn cạnh', 'Side Roller Conveyor Chain', 'xich-con-lan-canh', 'side-roller-conveyor-chain', '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', [$alt]),
                $l2SideRoller,
                $idx + 1,
                $now
            );
        }

        // Top Roller (5 shared across Baggage + Packaging + Light Conveyor via M:N pivot)
        foreach (['40-TR', '50-TR', '60-TR', '80-TR', '100-TR'] as $idx => $sku) {
            $alt = str_replace('-', '', $sku);
            $spec = $this->makeProductSpec($sku, 'Xích con lăn trên', 'Top Roller Conveyor Chain', 'xich-con-lan-tren', 'top-roller-conveyor-chain', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', [$alt]);
            $this->upsertProductAndLink($spec, $l2BaggageTopRoller, $idx + 1, $now);
            if ($sku !== '100-TR') {
                $this->upsertProductAndLink($spec, $l2PkgTopRoller, $idx + 1, $now);
                $this->upsertProductAndLink($spec, $l2LightTopRoller, $idx + 1, $now);
            }
        }

        // Hollow Pin (4 shared across Packaging + Light Conveyor via M:N pivot)
        foreach (['40HP', '50HP', '60HP', '80HP'] as $idx => $sku) {
            $spec = $this->makeProductSpec($sku, 'Xích chốt rỗng', 'Hollow Pin Chain', 'xich-chot-rong', 'hollow-pin-chain', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp');
            $this->upsertProductAndLink($spec, $l2PkgHollowPin, $idx + 1, $now);
            $this->upsertProductAndLink($spec, $l2LightHollowPin, $idx + 1, $now);
        }

        // Side Bow / Anti-Side Bow (15)
        foreach (['40SB', '43SB', '50SB', '60SB', '63SB', '80SB', '08BSB', '08BSBF1', '10BSB', '12BSB', '16BSB', 'C2050SB'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích cong', 'Side Bow Chain', 'xich-cong', 'side-bow-chain', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp'),
                $l2PkgSideBow,
                $idx + 1,
                $now
            );
        }
        $this->upsertProductAndLink(
            $this->makeProductSpec('SPC9.525', 'Xích chống cong', 'Anti-Side Bow Chain', 'xich-chong-cong', 'anti-side-bow-chain', '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp', ['SPC9.525 (Anti-side bow)']),
            $l2PkgSideBow,
            13,
            $now
        );
        $this->upsertProductAndLink(
            $this->makeProductSpec('SPC12.65a', 'Xích đẩy cửa sổ', 'Anti-Side Bow Window Chain', 'xich-day-cua-so', 'anti-side-bow-window-chain', '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp'),
            $l2PkgSideBow,
            14,
            $now
        );
        $this->upsertProductAndLink(
            $this->makeProductSpec('SPC12.65', 'Xích đẩy cửa sổ', 'Anti-Side Bow Window Chain (Stainless Steel)', 'xich-day-cua-so', 'anti-side-bow-window-chain', '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp', [], 'Xích đẩy cửa sổ SPC12.65 (thép không gỉ)', 'xich-day-cua-so-spc12-65-thep-khong-gi'),
            $l2PkgSideBow,
            15,
            $now
        );

        // M Series (11)
        foreach (['M20', 'M28', 'M40', 'M56', 'M80', 'M112', 'M224', 'M315', 'M450', 'M630', 'M900'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích tải dòng M', 'M Series Conveyor Chain', 'xich-tai-dong-m', 'm-series-conveyor-chain', '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp'),
                $l2LightMSeries,
                $idx + 1,
                $now
            );
        }

        // FV Series (11)
        foreach (['FV40', 'FV63', 'FV90', 'FV112', 'FV140', 'FV180', 'FV250', 'FV315', 'FV400', 'FV500', 'FV630'] as $idx => $sku) {
            $this->upsertProductAndLink(
                $this->makeProductSpec($sku, 'Xích tải dòng FV', 'FV Series Conveyor Chain', 'xich-tai-dong-fv', 'fv-series-conveyor-chain', '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp'),
                $l2LightFVSeries,
                $idx + 1,
                $now
            );
        }

        // =====================================================================
        // ROOT 5: GIẢI PHÁP TIẾT KIỆM ĐIỆN (1 L1: Quạt BLDC)
        // =====================================================================
        $this->upsertCategory([
            'position' => 1,
            'vi_slug' => 'quat-bldc',
            'vi_title' => 'Quạt BLDC',
            'vi_desc' => 'Quạt trần công nghiệp HVLS động cơ BLDC tiết kiệm điện năng vượt trội cho nhà xưởng, kho bãi và không gian lớn.',
            'en_slug' => 'bldc-industrial-fans',
            'en_title' => 'BLDC Fans',
            'en_desc' => 'Energy-saving BLDC motor HVLS industrial fans for factories, logistics warehouses, and large commercial spaces.',
            'image' => '/assets/images/homepage/cat-giai-phap-tiet-kiem-dien.png',
        ], $rootEnergy, $now);
    }

    protected function scrubTecsatEverywhere(): void
    {
        $targets = [
            'settings' => ['val'],
            'slider_translations' => ['title', 'subtitle', 'description', 'image', 'image_mobile'],
            'post_translations' => ['title', 'description', 'content', 'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'],
            'policy_translations' => ['title', 'content', 'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'],
            'product_category_translations' => ['title', 'description', 'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'],
            'product_translations' => ['title', 'description', 'content', 'seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'],
            'meta_pages' => ['seo_meta_title', 'seo_meta_description', 'seo_meta_keywords'],
        ];

        foreach ($targets as $table => $columns) {
            if (!Schema::hasTable($table)) {
                continue;
            }
            $existingCols = array_values(array_filter($columns, static fn (string $c): bool => Schema::hasColumn($table, $c)));
            if (empty($existingCols)) {
                continue;
            }

            $rows = DB::table($table)
                ->where(function ($q) use ($existingCols) {
                    foreach ($existingCols as $col) {
                        $q->orWhere($col, 'like', '%tecsat%');
                    }
                })
                ->get(array_merge(['id'], $existingCols));

            foreach ($rows as $row) {
                $updates = [];
                foreach ($existingCols as $col) {
                    $orig = $row->{$col};
                    if (is_string($orig) && stripos($orig, 'tecsat') !== false) {
                        $updates[$col] = $this->cleanTecsat($orig);
                    }
                }
                if (!empty($updates)) {
                    DB::table($table)->where('id', $row->id)->update($updates);
                }
            }
        }
    }

    protected function clearAllCaches(): void
    {
        try {
            if (class_exists(Config::class)) {
                Config::clearBrandCaches();
            }
            if (class_exists(ProductCategory::class)) {
                ProductCategory::clearCategoryCaches();
            }
            if (class_exists(Product::class)) {
                Product::clearProductCaches();
            }
            if (class_exists(Policy::class)) {
                Policy::clearPolicyCaches();
            }
            Cache::flush();
        } catch (\Throwable $e) {
            // Safe fallback if cache store is unavailable during CLI migration
        }
    }
};
