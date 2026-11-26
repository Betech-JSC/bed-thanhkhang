<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangAboutHeroSeeder extends Seeder
{
    /**
     * Idempotent seeder for About Us Section 1 — Hero Banner (BANNER_ABOUT_HERO, Figma 979:30279).
     * Seeds only when BANNER_ABOUT_HERO has 0 rows so existing Admin CMS edits are never overwritten.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        if (Slider::query()->where('position_display', 'BANNER_ABOUT_HERO')->count() > 0) {
            Slider::clearSliderCaches();
            Config::clearBrandCaches();
            return;
        }

        $imagePath = '/assets/images/about/about-hero-banner.png';

        $translations = [
            'vi' => [
                'subtitle' => 'Về Thành Khang Tecsat',
                'title' => "KẾT NỐI CHẤT LƯỢNG\nĐỒNG HÀNH BỀN VỮNG",
                'description' => 'Giải pháp vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp. Cùng doanh nghiệp vận hành an toàn, hiệu quả và liên tục.',
                'alt' => 'Về Thành Khang Tecsat - Kết nối chất lượng, đồng hành bền vững',
            ],
            'en' => [
                'subtitle' => 'About Thanh Khang Tecsat',
                'title' => "CONNECTING QUALITY\nSUSTAINABLE PARTNERSHIP",
                'description' => 'Genuine elevator – escalator spare parts, components, and industrial equipment solutions. Partnering with businesses for safe, efficient, and continuous operation.',
                'alt' => 'About Thanh Khang Tecsat - Connecting Quality, Sustainable Partnership',
            ],
        ];

        DB::transaction(function () use ($translations, $imagePath) {
            $slider = Slider::create([
                'status' => Slider::STATUS_ACTIVE,
                'position_display' => 'BANNER_ABOUT_HERO',
                'position_sort' => 10,
                'is_video' => 0,
            ]);

            foreach (['vi', 'en'] as $locale) {
                $tr = $translations[$locale];
                $imageJson = json_encode([
                    'path' => $imagePath,
                    'alt' => $tr['alt'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                DB::table('slider_translations')->updateOrInsert(
                    [
                        'slider_id' => $slider->id,
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
                        'image' => $imageJson,
                        'image_mobile' => $imageJson,
                        'custom_fields' => null,
                    ]
                );
            }
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
