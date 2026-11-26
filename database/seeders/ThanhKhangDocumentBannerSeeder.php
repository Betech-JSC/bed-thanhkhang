<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangDocumentBannerSeeder extends Seeder
{
    /**
     * Idempotent seeder for Documents & Catalogues page banners:
     * - BANNER_DOCUMENT_HERO
     * - BANNER_DOCUMENT_CTA
     * Seeds each position only when it has 0 rows so existing Admin CMS edits are never overwritten.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        DB::transaction(function () {
            if (Slider::query()->where('position_display', 'BANNER_DOCUMENT_HERO')->count() === 0) {
                $heroImagePath = '/assets/images/documents/document-hero-banner.png';

                $heroTranslations = [
                    'vi' => [
                        'title' => 'TÀI LIỆU & CATALOGUE',
                        'description' => 'Tải xuống catalogue sản phẩm, data sheet thông số kỹ thuật và hướng dẫn bảo trì chính hãng được phân phối bởi Thành Khang Tecsat',
                        'alt' => 'Tài liệu & Catalogue - Thành Khang Tecsat',
                    ],
                    'en' => [
                        'title' => 'DOCUMENTS & CATALOGUES',
                        'description' => 'Download product catalogues, technical data sheets, and official maintenance manuals distributed by Thanh Khang Tecsat.',
                        'alt' => 'Documents & Catalogues - Thanh Khang Tecsat',
                    ],
                ];

                $heroSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'BANNER_DOCUMENT_HERO',
                    'position_sort' => 10,
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $heroTranslations[$locale];
                    $imageJson = json_encode([
                        'path' => $heroImagePath,
                        'alt' => $tr['alt'],
                    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $heroSlider->id,
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

            if (Slider::query()->where('position_display', 'BANNER_DOCUMENT_CTA')->count() === 0) {
                $ctaTranslations = [
                    'vi' => [
                        'title' => 'KHÔNG TÌM THẤY TÀI LIỆU BẠN CẦN?',
                        'description' => 'Liên hệ chúng tôi để nhận bản vẽ chi tiết và tư vấn mã sản phẩm 24/7',
                        'primary_cta_text' => 'Yêu cầu tài liệu kỹ thuật',
                        'primary_cta_link' => '/lien-he',
                        'secondary_cta_text' => 'Gọi Hotline',
                        'secondary_cta_link' => null,
                    ],
                    'en' => [
                        'title' => "CAN'T FIND THE DOCUMENT YOU NEED?",
                        'description' => 'Contact us for detailed drawings and 24/7 part number consultation',
                        'primary_cta_text' => 'Request Technical Docs',
                        'primary_cta_link' => '/en/contact',
                        'secondary_cta_text' => 'Call Hotline',
                        'secondary_cta_link' => null,
                    ],
                ];

                $ctaSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'BANNER_DOCUMENT_CTA',
                    'position_sort' => 10,
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $ctaTranslations[$locale];

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $ctaSlider->id,
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
