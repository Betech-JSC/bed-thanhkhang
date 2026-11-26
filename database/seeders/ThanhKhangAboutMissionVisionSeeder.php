<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangAboutMissionVisionSeeder extends Seeder
{
    /**
     * Idempotent seeder for About Us Section 3 — "Sứ mệnh và Tầm nhìn" (Figma 979:30329).
     * - Seeds 1 BANNER_ABOUT_MISSION_VISION slider record if missing.
     * - Seeds 2 ABOUT_MISSION_VISION_ITEMS slider records (position_sort DESC: 20 = SỨ MỆNH, 10 = TẦM NHÌN) if missing.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $bannerImagePath = '/assets/images/about/about-mission-vision-bg.png';
        $bannerTranslations = [
            'vi' => [
                'title' => 'Sứ mệnh và Tầm nhìn - Thành Khang Tecsat',
                'alt' => 'Sứ mệnh và Tầm nhìn - Thành Khang Tecsat',
            ],
            'en' => [
                'title' => 'Mission and Vision - Thanh Khang Tecsat',
                'alt' => 'Mission and Vision - Thanh Khang Tecsat',
            ],
        ];

        $items = [
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => 'SỨ MỆNH',
                    'title' => 'Mang giải pháp đúng đến từng công trình',
                    'description' => 'Kết nối sản phẩm chính hãng với tư vấn kỹ thuật phù hợp, giúp cho khách hàng yên tâm trong từng quyết định lựa chọn và vận hành.',
                ],
                'en' => [
                    'subtitle' => 'MISSION',
                    'title' => 'Delivering the Right Solution to Every Project',
                    'description' => 'Connecting genuine products with tailored technical consulting, giving customers complete confidence in every selection and operational decision.',
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => 'TẦM NHÌN',
                    'title' => 'Trở thành đối tác kỹ thuật được tin chọn',
                    'description' => 'Xây dựng mối quan hệ hợp tác bền vững bằng chất lượng sản phẩm, sự minh bạch và tinh thần đồng hành lâu dài cùng khách hàng.',
                ],
                'en' => [
                    'subtitle' => 'VISION',
                    'title' => 'Becoming the Trusted Technical Partner of Choice',
                    'description' => 'Building sustainable partnerships through product quality, transparency, and a commitment to long-term companionship with every customer.',
                ],
            ],
        ];

        DB::transaction(function () use ($bannerImagePath, $bannerTranslations, $items) {
            // 1. Seed BANNER_ABOUT_MISSION_VISION (idempotent: only when 0 rows exist)
            if (Slider::query()->where('position_display', 'BANNER_ABOUT_MISSION_VISION')->count() === 0) {
                $bannerSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'BANNER_ABOUT_MISSION_VISION',
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

            // 2. Seed 2 ABOUT_MISSION_VISION_ITEMS (idempotent by position_display + position_sort)
            foreach ($items as $item) {
                $exists = Slider::query()
                    ->where('position_display', 'ABOUT_MISSION_VISION_ITEMS')
                    ->where('position_sort', $item['position_sort'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $itemSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'ABOUT_MISSION_VISION_ITEMS',
                    'position_sort' => $item['position_sort'],
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $item[$locale];

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $itemSlider->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'subtitle' => $tr['subtitle'],
                            'description' => $tr['description'],
                            'primary_cta_text' => null,
                            'primary_cta_link' => null,
                            'secondary_cta_text' => null,
                            'secondary_cta_link' => null,
                            'link' => null,
                            'target' => '_self',
                            'image' => null,
                            'image_mobile' => null,
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
