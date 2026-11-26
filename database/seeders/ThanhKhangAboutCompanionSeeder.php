<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangAboutCompanionSeeder extends Seeder
{
    /**
     * Idempotent seeder for About Us Section 5 — "Đồng hành cùng khách hàng" (Figma 979:30345).
     * - Seeds 4 VI & EN about_companion_* keys in settings (group='general') ONLY if missing or empty.
     * - Seeds 2 ABOUT_COMPANION_CARDS slides (position_sort DESC: 20, 10) ONLY if missing by position_sort.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $now = now();

        $companionSettings = [
            'about_companion_eyebrow' => 'Đồng hành cùng khách hàng',
            'about_companion_eyebrow_en' => 'Partnering With Our Customers',

            'about_companion_heading' => 'Từ lựa chọn ban đầu đến vận hành dài lâu',
            'about_companion_heading_en' => 'From Initial Selection to Long-Term Operation',
        ];

        $cards = [
            [
                'position_sort' => 20,
                'image_path' => '/assets/images/about/about-companion-consulting.png',
                'vi' => [
                    'title' => 'Tư vấn & Cung ứng',
                    'description' => 'Tìm đúng sản phẩm cho đúng ứng dụng. Đội ngũ kỹ thuật tiếp nhận thông số, tư vấn lựa chọn và báo giá phù hợp với yêu cầu của bạn.',
                    'primary_cta_text' => 'Liên hệ tư vấn',
                    'primary_cta_link' => '/lien-he',
                    'link' => '/lien-he',
                    'alt' => 'Tư vấn & Cung ứng - Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Consulting & Supply',
                    'description' => 'Finding the right product for the right application. Our engineering team reviews your specifications, advises optimal selection, and provides tailored quotations.',
                    'primary_cta_text' => 'Contact for Consultation',
                    'primary_cta_link' => '/en/contact',
                    'link' => '/en/contact',
                    'alt' => 'Consulting & Supply - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 10,
                'image_path' => '/assets/images/about/about-companion-aftersales.png',
                'vi' => [
                    'title' => 'Hỗ trợ kỹ thuật & hậu mãi',
                    'description' => 'Không dừng lại ở giao hàng. Chúng tôi tiếp tục hỗ trợ trong quá trình sử dụng, bảo hành và giải đáp các vấn đề kỹ thuật phát sinh.',
                    'primary_cta_text' => 'Liên hệ hỗ trợ',
                    'primary_cta_link' => '/lien-he',
                    'link' => '/lien-he',
                    'alt' => 'Hỗ trợ kỹ thuật & hậu mãi - Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Technical Support & After-Sales',
                    'description' => 'Going beyond delivery. We continue to support you throughout operation, warranty coverage, and resolving any technical issues that arise.',
                    'primary_cta_text' => 'Contact for Support',
                    'primary_cta_link' => '/en/contact',
                    'link' => '/en/contact',
                    'alt' => 'Technical Support & After-Sales - Thanh Khang Tecsat',
                ],
            ],
        ];

        DB::transaction(function () use ($companionSettings, $cards, $now) {
            foreach ($companionSettings as $name => $val) {
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

            foreach ($cards as $card) {
                $exists = Slider::query()
                    ->where('position_display', 'ABOUT_COMPANION_CARDS')
                    ->where('position_sort', $card['position_sort'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $slider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'ABOUT_COMPANION_CARDS',
                    'position_sort' => $card['position_sort'],
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $card[$locale];
                    $imageJson = json_encode([
                        'path' => $card['image_path'],
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
            }
        });

        Slider::clearSliderCaches();
        Config::clearBrandCaches();
    }
}
