<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangHomeAboutSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 2 ("Về chúng tôi", Figma 595:15362).
     * - Seeds VI & EN home_about_* keys in settings (group='general') ONLY if missing or empty
     *   so existing admin edits are never overwritten.
     * - Seeds 3 HOME_ABOUT_GALLERY slides (position_sort DESC: 30, 20, 10) ONLY when
     *   HOME_ABOUT_GALLERY has 0 rows.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $now = now();

        $aboutSettings = [
            'home_about_eyebrow' => 'Về chúng tôi',
            'home_about_eyebrow_en' => 'About Us',

            'home_about_lead_highlight' => 'Công ty TNHH Thương Mại và Dịch Vụ Kỹ Thuật Thành Khang (THANH KHANG Tecsat)',
            'home_about_lead_highlight_en' => 'Thanh Khang Trading and Technical Service Co., Ltd (THANH KHANG Tecsat)',

            'home_about_lead_rest' => 'là nhà phân phối các sản phẩm chính hãng trong lĩnh vực vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp tại Việt Nam.',
            'home_about_lead_rest_en' => 'is an authorized distributor of genuine elevator – escalator spare parts, components, and industrial equipment in Vietnam.',

            'home_about_paragraph_1' => 'Thành Khang Tecsat không chỉ cung cấp sản phẩm – chúng tôi mang đến giải pháp kỹ thuật toàn diện: từ tư vấn lựa chọn đúng sản phẩm, đến hỗ trợ kỹ thuật trong quá trình sử dụng sản phẩm và dịch vụ sau bán hàng.',
            'home_about_paragraph_1_en' => 'Thanh Khang Tecsat does not merely supply products — we deliver comprehensive technical solutions: from product selection consulting to hands-on technical support during operation and reliable after-sales service.',

            'home_about_paragraph_2' => 'Với bề dày kinh nghiệm và hệ thống kho hàng có sẵn, sản phẩm của Thành Khang Tecsat đã phục vụ hàng trăm công trình, dự án trên toàn quốc.',
            'home_about_paragraph_2_en' => 'Backed by years of engineering expertise and ready-to-ship warehouse inventory, Thanh Khang Tecsat products have powered hundreds of projects and facilities nationwide.',

            'home_about_commitment_quote' => '"Đúng nguồn gốc – Đúng thông số – Đúng tiến độ"',
            'home_about_commitment_quote_en' => '"Genuine Origin – Exact Specifications – On-Time Delivery"',

            'home_about_commitment_sub' => 'Đó là cam kết chúng tôi mang đến cho mỗi khách hàng',
            'home_about_commitment_sub_en' => 'That is the commitment we deliver to every customer',

            'home_about_cta_text' => 'Xem thêm về chúng tôi',
            'home_about_cta_text_en' => 'Learn more about us',

            'home_about_cta_link' => '/gioi-thieu',
            'home_about_cta_link_en' => '/en/about-us',
        ];

        $galleryItems = [
            [
                'position_sort' => 30,
                'image_path' => '/assets/images/homepage/about-gear-1.png',
                'vi' => [
                    'title' => 'Bánh răng truyền động chính xác Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Precision Transmission Gears - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 20,
                'image_path' => '/assets/images/homepage/about-chain-2.png',
                'vi' => [
                    'title' => 'Xích tải công nghiệp chính hãng Universal Chains',
                ],
                'en' => [
                    'title' => 'Genuine Industrial Conveyor Chains',
                ],
            ],
            [
                'position_sort' => 10,
                'image_path' => '/assets/images/homepage/about-engineer-3.png',
                'vi' => [
                    'title' => 'Kỹ sư Thành Khang lắp đặt và kiểm định băng tải công nghiệp',
                ],
                'en' => [
                    'title' => 'Thanh Khang Engineer Inspecting Industrial Conveyor Chain',
                ],
            ],
        ];

        DB::transaction(function () use ($aboutSettings, $galleryItems, $now) {
            foreach ($aboutSettings as $name => $val) {
                $existing = DB::table('settings')
                    ->where('group', 'general')
                    ->where('name', $name)
                    ->first();

                if (!$existing) {
                    DB::table('settings')->insert([
                        'group' => 'general',
                        'name' => $name,
                        'val' => $val,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                } elseif (trim((string) $existing->val) === '') {
                    DB::table('settings')
                        ->where('id', $existing->id)
                        ->update([
                            'val' => $val,
                            'updated_at' => $now,
                        ]);
                }
            }

            if (Slider::query()->where('position_display', 'HOME_ABOUT_GALLERY')->count() === 0) {
                foreach ($galleryItems as $item) {
                    $slider = Slider::create([
                        'status' => Slider::STATUS_ACTIVE,
                        'position_display' => 'HOME_ABOUT_GALLERY',
                        'position_sort' => $item['position_sort'],
                        'is_video' => 0,
                    ]);

                    foreach (['vi', 'en'] as $locale) {
                        $title = $item[$locale]['title'];
                        $imageJson = json_encode([
                            'path' => $item['image_path'],
                            'alt' => $title,
                        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                        DB::table('slider_translations')->updateOrInsert(
                            [
                                'slider_id' => $slider->id,
                                'locale' => $locale,
                            ],
                            [
                                'title' => $title,
                                'subtitle' => null,
                                'description' => null,
                                'primary_cta_text' => null,
                                'primary_cta_link' => null,
                                'secondary_cta_text' => null,
                                'secondary_cta_link' => null,
                                'link' => null,
                                'target' => '_self',
                                'image' => $imageJson,
                                'image_mobile' => $imageJson,
                                'custom_fields' => null,
                            ]
                        );
                    }
                }
            }
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
