<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangHomeTrustSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 4 — Why Choose Us ("Vì sao khách hàng tin tưởng chọn Thành Khang?", Figma 595:15394).
     * - Seeds 1 BANNER_HOME_TRUST slider record if missing.
     * - Seeds 5 HOME_TRUST_PILLARS slider records (position_sort DESC: 50, 40, 30, 20, 10) if missing.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $bannerImagePath = '/assets/images/homepage/why-choose-us-bg.png';
        $bannerTranslations = [
            'vi' => [
                'title' => 'Vì sao khách hàng tin tưởng chọn Thành Khang?',
                'description' => '5 giá trị cốt lõi tạo nên niềm tin của các đối tác công nghiệp tại Thành Khang Tecsat',
                'alt' => 'Vì sao khách hàng tin tưởng chọn Thành Khang?',
            ],
            'en' => [
                'title' => 'Why Do Customers Trust Thanh Khang?',
                'description' => "5 core values behind industrial partners' trust in Thanh Khang Tecsat",
                'alt' => 'Why Do Customers Trust Thanh Khang?',
            ],
        ];

        $pillars = [
            [
                'position_sort' => 50,
                'icon_path' => '/assets/svg/icons/trust-thumbs-up.svg',
                'vi' => [
                    'title' => 'Uy tín tận tâm',
                    'description' => 'Đặt sự hài lòng của khách hàng về chất lượng sản phẩm và dịch vụ lên hàng đầu',
                ],
                'en' => [
                    'title' => 'Dedicated Integrity',
                    'description' => 'Putting customer satisfaction in product quality and service excellence first',
                ],
            ],
            [
                'position_sort' => 40,
                'icon_path' => '/assets/svg/icons/trust-check-circle.svg',
                'vi' => [
                    'title' => 'Hàng chính hãng',
                    'description' => 'Sản phẩm nhập khẩu có chứng từ CO/CQ minh bạch, cam kết bảo hành chính hãng',
                ],
                'en' => [
                    'title' => 'Genuine Products',
                    'description' => 'Imported products with transparent CO/CQ documentation and genuine warranty commitment',
                ],
            ],
            [
                'position_sort' => 30,
                'icon_path' => '/assets/svg/icons/trust-clipboard.svg',
                'vi' => [
                    'title' => 'Quy trình tối ưu',
                    'description' => 'Quy trình đặt hàng nhanh gọn, thuận tiện, giúp khách hàng dễ dàng quản lý dữ liệu đơn hàng',
                ],
                'en' => [
                    'title' => 'Optimized Process',
                    'description' => 'Streamlined, convenient ordering workflow helping customers manage order data effortlessly',
                ],
            ],
            [
                'position_sort' => 20,
                'icon_path' => '/assets/svg/icons/trust-truck.svg',
                'vi' => [
                    'title' => 'Giao hàng nhanh',
                    'description' => 'Giao hàng toàn quốc 24/7, kể cả các ngày cuối tuần',
                ],
                'en' => [
                    'title' => 'Fast Delivery',
                    'description' => 'Nationwide 24/7 delivery service, including weekends',
                ],
            ],
            [
                'position_sort' => 10,
                'icon_path' => '/assets/svg/icons/trust-tool.svg',
                'vi' => [
                    'title' => 'Hỗ trợ kỹ thuật',
                    'description' => 'Đội ngũ kỹ thuật tư vấn lựa chọn sản phẩm phù hợp theo dự án của khách hàng',
                ],
                'en' => [
                    'title' => 'Technical Support',
                    'description' => 'Engineering team advising optimal product selection tailored to every customer project',
                ],
            ],
        ];

        DB::transaction(function () use ($bannerImagePath, $bannerTranslations, $pillars) {
            // 1. Seed BANNER_HOME_TRUST (idempotent by position_display + position_sort)
            $existingBanner = Slider::query()
                ->where('position_display', 'BANNER_HOME_TRUST')
                ->where('position_sort', 10)
                ->first();

            if (!$existingBanner && Slider::query()->where('position_display', 'BANNER_HOME_TRUST')->count() === 0) {
                $bannerSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'BANNER_HOME_TRUST',
                    'position_sort' => 10,
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $bannerTranslations[$locale];
                    $imageJson = json_encode([
                        'path' => $bannerImagePath,
                        'alt' => $tr['alt'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $bannerSlider->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'subtitle' => null,
                            'description' => $tr['description'],
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

            // 2. Seed 5 HOME_TRUST_PILLARS (idempotent by position_display + position_sort)
            foreach ($pillars as $pillar) {
                $exists = Slider::query()
                    ->where('position_display', 'HOME_TRUST_PILLARS')
                    ->where('position_sort', $pillar['position_sort'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $pillarSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'HOME_TRUST_PILLARS',
                    'position_sort' => $pillar['position_sort'],
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $pillar[$locale];
                    $iconJson = json_encode([
                        'path' => $pillar['icon_path'],
                        'alt' => $tr['title'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $pillarSlider->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'subtitle' => null,
                            'description' => $tr['description'],
                            'primary_cta_text' => null,
                            'primary_cta_link' => null,
                            'secondary_cta_text' => null,
                            'secondary_cta_link' => null,
                            'link' => null,
                            'target' => '_self',
                            'image' => $iconJson,
                            'image_mobile' => $iconJson,
                            'custom_fields' => null,
                        ]
                    );
                }
            }
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
