<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangGlobalCtaBannerSeeder extends Seeder
{
    /**
     * Idempotent seeder for Global Technical Consultation CTA Banner (BANNER_GLOBAL_CTA, Figma 1264:49400).
     * Seeds only when BANNER_GLOBAL_CTA has 0 rows so existing Admin CMS edits are never overwritten.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        if (!Schema::hasTable('sliders') || !Schema::hasTable('slider_translations')) {
            return;
        }

        if (Slider::query()->where('position_display', 'BANNER_GLOBAL_CTA')->count() > 0) {
            Slider::clearSliderCaches();
            Config::clearBrandCaches();
            return;
        }

        $desktopImagePath = '/assets/images/banners/global-cta-desktop.webp';
        $mobileImagePath = '/assets/images/banners/global-cta-mobile.webp';

        $translations = [
            'vi' => [
                'title' => 'CẦN TƯ VẤN HOẶC BÁO GIÁ NHANH ?',
                'description' => 'Gửi yêu cầu và chúng tôi sẽ phản hồi trong vòng 2 giờ làm việc',
                'primary_cta_text' => 'Gọi Hotline: 0967 732 665',
                'primary_cta_link' => 'tel:0967732665',
                'secondary_cta_text' => 'Liên hệ qua Zalo',
                'secondary_cta_link' => 'https://zalo.me/0967732665',
                'alt' => 'Cần tư vấn hoặc báo giá nhanh - Thành Khang Tecsat',
            ],
            'en' => [
                'title' => 'NEED CONSULTATION OR A QUICK QUOTE?',
                'description' => 'Send your request and we will respond within 2 business hours',
                'primary_cta_text' => 'Call Hotline: 0967 732 665',
                'primary_cta_link' => 'tel:0967732665',
                'secondary_cta_text' => 'Contact via Zalo',
                'secondary_cta_link' => 'https://zalo.me/0967732665',
                'alt' => 'Need consultation or a quick quote - Thanh Khang Tecsat',
            ],
        ];

        DB::transaction(function () use ($translations, $desktopImagePath, $mobileImagePath) {
            $slider = Slider::create([
                'status' => Slider::STATUS_ACTIVE,
                'position_display' => 'BANNER_GLOBAL_CTA',
                'position_sort' => 10,
                'is_video' => 0,
            ]);

            foreach (['vi', 'en'] as $locale) {
                $tr = $translations[$locale];

                $desktopImageJson = json_encode([
                    'path' => $desktopImagePath,
                    'alt' => $tr['alt'],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                $mobileImageJson = json_encode([
                    'path' => $mobileImagePath,
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
                        'primary_cta_text' => $tr['primary_cta_text'],
                        'primary_cta_link' => $tr['primary_cta_link'],
                        'secondary_cta_text' => $tr['secondary_cta_text'],
                        'secondary_cta_link' => $tr['secondary_cta_link'],
                        'link' => null,
                        'target' => '_self',
                        'image' => $desktopImageJson,
                        'image_mobile' => $mobileImageJson,
                        'custom_fields' => null,
                    ]
                );
            }
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
