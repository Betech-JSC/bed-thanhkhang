<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangHomeSliderSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Hero Banner slides (HOME_SLIDER).
     * Uses descending position_sort (30, 20, 10) because Slider::getByPosition()
     * orders by position_sort DESC, id DESC so Slide 1 always appears first.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $imagePath = '/assets/images/homepage/hero-banner-1.png';

        $slides = [
            [
                'position_sort' => 30,
                'vi' => [
                    'subtitle' => 'Enduring motion',
                    'title' => 'CHUYỂN ĐỘNG BỀN BỈ',
                    'description' => 'Cung cấp giải pháp truyền động và thiết bị cơ khí chính xác, tối ưu hóa hiệu suất vận hành và đảm bảo độ tin cậy vượt trội cho mọi dây chuyền sản xuất công nghiệp',
                    'primary_cta_text' => 'Xem sản phẩm',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Liên hệ tư vấn',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/san-pham',
                    'alt' => 'CHUYỂN ĐỘNG BỀN BỈ - Thành Khang Tecsat',
                ],
                'en' => [
                    'subtitle' => 'Enduring motion',
                    'title' => 'ENDURING MOTION',
                    'description' => 'Delivering precision drive solutions and industrial mechanical components that maximize operational efficiency and reliability across production lines.',
                    'primary_cta_text' => 'View Products',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Request Consultation',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/en/products',
                    'alt' => 'ENDURING MOTION - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => 'Precision engineering',
                    'title' => 'PHỤ TÙNG THANG CUỐN & THANG NÂNG Ô TÔ',
                    'description' => 'Tổng kho xích bậc thang cuốn, xích bãi đỗ xe lập thể và linh kiện cơ khí chuẩn OEM với đầy đủ chứng chỉ xuất xứ CO/CQ cho các tòa nhà và trung tâm thương mại',
                    'primary_cta_text' => 'Khám phá danh mục',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Nhận báo giá nhanh',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/san-pham',
                    'alt' => 'Phụ tùng thang cuốn và thang nâng ô tô - Thành Khang Tecsat',
                ],
                'en' => [
                    'subtitle' => 'Precision engineering',
                    'title' => 'ESCALATOR & CAR LIFT SPARE PARTS',
                    'description' => 'Comprehensive supply of OEM-grade escalator step chains, multi-level parking lift chains, and precision mechanical components backed by full CO/CQ certification.',
                    'primary_cta_text' => 'Explore Catalog',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Request a Quote',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/en/products',
                    'alt' => 'Escalator and Car Lift Spare Parts - Thanh Khang Tecsat',
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => 'Industrial reliability',
                    'title' => 'XÍCH TẢI CÔNG NGHIỆP UNIVERSAL CHAINS',
                    'description' => 'Giải pháp xích băng tải hành lý sân bay, dây chuyền đóng gói và xích tải hạng nặng đạt chuẩn ISO/DIN/ANSI, đồng hành cùng vận hành liên tục 24/7 của doanh nghiệp',
                    'primary_cta_text' => 'Xem sản phẩm',
                    'primary_cta_link' => '/san-pham',
                    'secondary_cta_text' => 'Liên hệ kỹ sư',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/san-pham',
                    'alt' => 'Xích tải công nghiệp Universal Chains - Thành Khang Tecsat',
                ],
                'en' => [
                    'subtitle' => 'Industrial reliability',
                    'title' => 'UNIVERSAL CHAINS & CONVEYOR SYSTEMS',
                    'description' => 'ISO/DIN/ANSI certified airport baggage conveyor chains, packaging line chains, and heavy-duty industrial drive systems engineered for 24/7 continuous operation.',
                    'primary_cta_text' => 'View Products',
                    'primary_cta_link' => '/en/products',
                    'secondary_cta_text' => 'Talk to an Engineer',
                    'secondary_cta_link' => '#contact-section',
                    'link' => '/en/products',
                    'alt' => 'Universal Chains and Conveyor Systems - Thanh Khang Tecsat',
                ],
            ],
        ];

        DB::transaction(function () use ($slides, $imagePath) {
            foreach ($slides as $slideData) {
                $slider = Slider::query()
                    ->where('position_display', 'HOME_SLIDER')
                    ->where('position_sort', $slideData['position_sort'])
                    ->first();

                if (!$slider) {
                    $slider = Slider::create([
                        'status' => Slider::STATUS_ACTIVE,
                        'position_display' => 'HOME_SLIDER',
                        'position_sort' => $slideData['position_sort'],
                        'is_video' => 0,
                    ]);
                } else {
                    $slider->update([
                        'status' => Slider::STATUS_ACTIVE,
                        'is_video' => 0,
                    ]);
                }

                foreach (['vi', 'en'] as $locale) {
                    $tr = $slideData[$locale];
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
                            'primary_cta_text' => $tr['primary_cta_text'],
                            'primary_cta_link' => $tr['primary_cta_link'],
                            'secondary_cta_text' => $tr['secondary_cta_text'],
                            'secondary_cta_link' => $tr['secondary_cta_link'],
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
