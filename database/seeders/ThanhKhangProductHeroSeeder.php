<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangProductHeroSeeder extends Seeder
{
    /**
     * Idempotent seeder for Products Catalog Hero Banner (BANNER_PRODUCT_HERO, Figma 979:30380).
     * Seeds only when BANNER_PRODUCT_HERO has 0 rows so existing Admin CMS edits are never overwritten.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        if (Slider::query()->where('position_display', 'BANNER_PRODUCT_HERO')->count() > 0) {
            Slider::clearSliderCaches();
            Config::clearBrandCaches();
            return;
        }

        $imagePath = '/assets/figma/san-pham/hero-bg.webp';

        $translations = [
            'vi' => [
                'title' => 'SẢN PHẨM',
                'description' => 'Linh kiện truyền động, băng tải và thang máy chính hãng, kèm tư vấn kỹ thuật chọn đúng loại cho công trình của bạn',
                'alt' => 'Sản phẩm - Thành Khang Tecsat',
            ],
            'en' => [
                'title' => 'PRODUCTS',
                'description' => 'Genuine transmission components, conveyor systems, and escalator parts with technical selection consulting for your project',
                'alt' => 'Products - Thanh Khang Tecsat',
            ],
        ];

        DB::transaction(function () use ($translations, $imagePath) {
            $slider = Slider::create([
                'status' => Slider::STATUS_ACTIVE,
                'position_display' => 'BANNER_PRODUCT_HERO',
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
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
