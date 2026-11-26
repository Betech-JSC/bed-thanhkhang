<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangAboutStatsSeeder extends Seeder
{
    /**
     * Idempotent seeder for About Us Section 4 — "Dấu ấn và Cam kết" (Figma 979:30340).
     * Seeds 4 ABOUT_STATS_ITEMS slider records (position_sort DESC: 40, 30, 20, 10) if missing.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $items = [
            [
                'position_sort' => 40,
                'vi' => [
                    'subtitle' => 'Dự án đã đồng hành',
                    'title' => '100+',
                ],
                'en' => [
                    'subtitle' => 'Projects Supported',
                    'title' => '100+',
                ],
            ],
            [
                'position_sort' => 30,
                'vi' => [
                    'subtitle' => 'Phạm vi cung ứng sản phẩm',
                    'title' => 'Toàn Quốc',
                ],
                'en' => [
                    'subtitle' => 'Product Supply Coverage',
                    'title' => 'Nationwide',
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => 'Chứng từ, nguồn gốc minh bạch',
                    'title' => 'CO/CQ',
                ],
                'en' => [
                    'subtitle' => 'Transparent Origin & Certificates',
                    'title' => 'CO/CQ',
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => 'Thời gian giao hàng',
                    'title' => '24/7',
                ],
                'en' => [
                    'subtitle' => 'Delivery Readiness',
                    'title' => '24/7',
                ],
            ],
        ];

        DB::transaction(function () use ($items) {
            foreach ($items as $item) {
                $exists = Slider::query()
                    ->where('position_display', 'ABOUT_STATS_ITEMS')
                    ->where('position_sort', $item['position_sort'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $slider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'ABOUT_STATS_ITEMS',
                    'position_sort' => $item['position_sort'],
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $item[$locale];

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $slider->id,
                            'locale' => $locale,
                        ],
                        [
                            'title' => $tr['title'],
                            'subtitle' => $tr['subtitle'],
                            'description' => null,
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
