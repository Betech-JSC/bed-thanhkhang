<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangHomeCategoriesCtaSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 3 Special CTA Tile ("KHÁM PHÁ TOÀN BỘ SẢN PHẨM").
     * Only seeds when BANNER_HOME_CATEGORIES_CTA has 0 rows so existing admin edits are never overwritten.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        if (Slider::query()->where('position_display', 'BANNER_HOME_CATEGORIES_CTA')->count() > 0) {
            return;
        }

        $imagePath = '/assets/images/homepage/cat-explore-all.png';

        $translations = [
            'vi' => [
                'title' => 'KHÁM PHÁ TOÀN BỘ SẢN PHẨM',
                'subtitle' => 'Danh mục sản phẩm',
                'link' => '/san-pham',
                'primary_cta_text' => 'Khám phá toàn bộ sản phẩm',
                'primary_cta_link' => '/san-pham',
                'alt' => 'Khám phá toàn bộ sản phẩm Thành Khang Tecsat',
            ],
            'en' => [
                'title' => 'EXPLORE ALL PRODUCTS',
                'subtitle' => 'Product Categories',
                'link' => '/en/products',
                'primary_cta_text' => 'Explore all products',
                'primary_cta_link' => '/en/products',
                'alt' => 'Explore all products - Thanh Khang Tecsat',
            ],
        ];

        DB::transaction(function () use ($translations, $imagePath) {
            $slider = Slider::create([
                'status' => Slider::STATUS_ACTIVE,
                'position_display' => 'BANNER_HOME_CATEGORIES_CTA',
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
                        'description' => null,
                        'primary_cta_text' => $tr['primary_cta_text'],
                        'primary_cta_link' => $tr['primary_cta_link'],
                        'secondary_cta_text' => null,
                        'secondary_cta_link' => null,
                        'link' => $tr['link'],
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
