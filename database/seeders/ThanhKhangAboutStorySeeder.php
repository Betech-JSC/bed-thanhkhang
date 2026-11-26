<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangAboutStorySeeder extends Seeder
{
    /**
     * Idempotent seeder for About Us Section 2 — "Câu chuyện Thành Khang" (Figma 979:30312).
     * - Seeds 12 VI & EN about_story_* keys in settings (group='general') ONLY if missing or empty
     *   so existing Admin CMS edits are never overwritten.
     * - Seeds 3 ABOUT_STORY_GALLERY slides (position_sort DESC: 30, 20, 10) ONLY if missing by position_sort.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $now = now();

        $storySettings = [
            'about_story_eyebrow' => 'Câu chuyện của chúng tôi',
            'about_story_eyebrow_en' => 'Our Story',

            'about_story_heading' => "Không chỉ là sản phẩm\nLà giải pháp và sự an tâm",
            'about_story_heading_en' => "More Than Just Products\nSolutions and Peace of Mind",

            'about_story_paragraph_1' => 'Thành lập năm 2016, Công ty TNHH Thương Mại và Dịch Vụ Kỹ Thuật Thành Khang (THANH KHANG Tecsat) là nhà phân phối các sản phẩm chính hãng trong lĩnh vực vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp tại Việt Nam.',
            'about_story_paragraph_1_en' => 'Founded in 2016, Thanh Khang Trading and Technical Service Co., Ltd (THANH KHANG Tecsat) is an authorized distributor of genuine elevator – escalator spare parts, components, and industrial equipment in Vietnam.',

            'about_story_paragraph_2' => 'Thành Khang Tecsat không chỉ cung cấp sản phẩm – chúng tôi mang đến giải pháp kỹ thuật toàn diện: từ tư vấn lựa chọn đúng sản phẩm, đến hỗ trợ kỹ thuật trong quá trình sử dụng sản phẩm và dịch vụ sau bán hàng.',
            'about_story_paragraph_2_en' => 'Thanh Khang Tecsat does not merely supply products — we deliver comprehensive technical solutions: from product selection consulting to hands-on technical support during operation and reliable after-sales service.',

            'about_story_paragraph_3' => 'Với bề dày kinh nghiệm và hệ thống kho hàng có sẵn, sản phẩm của Thành Khang Tecsat đã phục vụ hàng trăm công trình, dự án trên toàn quốc với cam kết:',
            'about_story_paragraph_3_en' => 'Backed by years of engineering expertise and ready-to-ship warehouse inventory, Thanh Khang Tecsat products have powered hundreds of projects and facilities nationwide with the commitment:',

            'about_story_commitment' => '"ĐÚNG NGUỒN GỐC - ĐÚNG THÔNG SỐ - ĐÚNG TIẾN ĐỘ"',
            'about_story_commitment_en' => '"GENUINE ORIGIN - EXACT SPECIFICATIONS - ON-TIME DELIVERY"',
        ];

        $galleryItems = [
            [
                'position_sort' => 30,
                'image_path' => '/assets/images/about/about-story-chain-inspection.png',
                'vi' => [
                    'title' => 'Xích và linh kiện cơ khí - Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Industrial Chains and Mechanical Components - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 20,
                'image_path' => '/assets/images/about/about-story-sprocket.png',
                'vi' => [
                    'title' => 'Cơ cấu chuyển động - Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Precision Drive Mechanism - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 10,
                'image_path' => '/assets/images/about/about-story-roller-chains.png',
                'vi' => [
                    'title' => 'Dây xích công nghiệp - Thành Khang Tecsat',
                ],
                'en' => [
                    'title' => 'Industrial Roller Chains - Thanh Khang Tecsat',
                ],
            ],
        ];

        DB::transaction(function () use ($storySettings, $galleryItems, $now) {
            foreach ($storySettings as $name => $val) {
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

            if (Slider::query()->where('position_display', 'ABOUT_STORY_GALLERY')->count() === 0) {
                foreach ($galleryItems as $item) {
                    $slider = Slider::create([
                        'status' => Slider::STATUS_ACTIVE,
                        'position_display' => 'ABOUT_STORY_GALLERY',
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
