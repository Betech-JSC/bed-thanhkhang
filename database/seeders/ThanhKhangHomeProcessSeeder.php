<?php

namespace Database\Seeders;

use App\Models\Config;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use JamstackVietnam\Slider\Models\Slider;

class ThanhKhangHomeProcessSeeder extends Seeder
{
    /**
     * Idempotent seeder for Homepage Section 5 — Process ("Quy trình", Figma 595:15441, 4 steps).
     * - Seeds 1 BANNER_HOME_PROCESS slider record if missing.
     * - Seeds 4 HOME_PROCESS_STEPS slider records (position_sort DESC: 40, 30, 20, 10) if missing.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $bannerImagePath = '/assets/images/homepage/process-left-bg.png';
        $bannerTranslations = [
            'vi' => [
                'title' => 'QUY TRÌNH TIẾP NHẬN & LÀM VIỆC TẠI THÀNH KHANG TECSAT',
                'description' => 'Xích công nghiệp chính hãng, đa dạng kích thước, đáp ứng nhiều điều kiện làm việc khác nhau. Đội ngũ kỹ thuật tư vấn lựa chọn đúng loại xích theo tải trọng và ứng dụng thực tế, giúp quý khách tối ưu chi phí và hạn chế gián đoạn sản xuất',
                'alt' => 'Quy trình tiếp nhận và làm việc tại Thành Khang Tecsat',
            ],
            'en' => [
                'title' => 'ORDER INTAKE & WORKFLOW PROCESS AT THANH KHANG TECSAT',
                'description' => 'Genuine industrial chains in diverse sizes for various operating conditions. Our engineering team advises the right chain selection by load capacity and real application, helping you optimize costs and minimize production downtime.',
                'alt' => 'Order Intake and Workflow Process at Thanh Khang Tecsat',
            ],
        ];

        $steps = [
            [
                'position_sort' => 40,
                'vi' => [
                    'subtitle' => '1',
                    'title' => 'Gửi yêu cầu',
                    'description' => 'Cung cấp thông tin về loại xích, thông số kỹ thuật, số lượng và ứng dụng thực tế qua biểu mẫu báo giá, email hoặc hotline',
                ],
                'en' => [
                    'subtitle' => '1',
                    'title' => 'Submit Request',
                    'description' => 'Provide chain type, technical specifications, quantity, and practical application via our quote form, email, or hotline.',
                ],
            ],
            [
                'position_sort' => 30,
                'vi' => [
                    'subtitle' => '2',
                    'title' => 'Tư vấn & Báo giá',
                    'description' => 'Kỹ thuật viên xác nhận thông số, tư vấn dòng xích phù hợp và gửi báo giá.',
                ],
                'en' => [
                    'subtitle' => '2',
                    'title' => 'Consultation & Quotation',
                    'description' => 'Our engineers verify technical specifications, recommend the right chain series, and send a detailed quotation.',
                ],
            ],
            [
                'position_sort' => 20,
                'vi' => [
                    'subtitle' => '3',
                    'title' => 'Sản xuất & kiểm định',
                    'description' => 'Kiểm tra thông số, chuẩn bị linh kiện chính hãng hoặc sản xuất theo tiêu chuẩn kỹ thuật, kiểm định chất lượng và hoàn thiện chứng từ CO/CQ.',
                ],
                'en' => [
                    'subtitle' => '3',
                    'title' => 'Manufacturing & QC Inspection',
                    'description' => 'Verify specifications, prepare genuine components or manufacture to standards, conduct strict quality inspection, and complete CO/CQ documents.',
                ],
            ],
            [
                'position_sort' => 10,
                'vi' => [
                    'subtitle' => '4',
                    'title' => 'Giao hàng & hỗ trợ',
                    'description' => 'Đóng gói cẩn thận, giao đúng tiến độ và đồng hành hỗ trợ kỹ thuật sau bán hàng.',
                ],
                'en' => [
                    'subtitle' => '4',
                    'title' => 'Delivery & Technical Support',
                    'description' => 'Careful industrial packaging, on-schedule delivery, and dedicated after-sales technical support.',
                ],
            ],
        ];

        DB::transaction(function () use ($bannerImagePath, $bannerTranslations, $steps) {
            // 1. Seed BANNER_HOME_PROCESS (idempotent by position_display + position_sort)
            $existingBanner = Slider::query()
                ->where('position_display', 'BANNER_HOME_PROCESS')
                ->where('position_sort', 10)
                ->first();

            if (!$existingBanner && Slider::query()->where('position_display', 'BANNER_HOME_PROCESS')->count() === 0) {
                $bannerSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'BANNER_HOME_PROCESS',
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

            // 2. Seed 4 HOME_PROCESS_STEPS (idempotent by position_display + position_sort)
            foreach ($steps as $step) {
                $exists = Slider::query()
                    ->where('position_display', 'HOME_PROCESS_STEPS')
                    ->where('position_sort', $step['position_sort'])
                    ->exists();

                if ($exists) {
                    continue;
                }

                $stepSlider = Slider::create([
                    'status' => Slider::STATUS_ACTIVE,
                    'position_display' => 'HOME_PROCESS_STEPS',
                    'position_sort' => $step['position_sort'],
                    'is_video' => 0,
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $step[$locale];

                    DB::table('slider_translations')->updateOrInsert(
                        [
                            'slider_id' => $stepSlider->id,
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
