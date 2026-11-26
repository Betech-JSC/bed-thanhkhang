<?php

namespace Database\Seeders;

use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ThanhKhangDocumentLibrarySeeder extends Seeder
{
    public function run(): void
    {
        if (
            !Schema::hasTable('post_categories') ||
            !Schema::hasTable('post_category_translations') ||
            !Schema::hasTable('posts') ||
            !Schema::hasTable('post_translations') ||
            !Schema::hasTable('post_ref_categories')
        ) {
            return;
        }

        // Idempotent guard: do not overwrite or duplicate CMS-managed records.
        if (
            PostCategory::where('type', PostCategory::TYPE_DOCUMENT)->count() > 0 ||
            Post::where('type', Post::TYPE_DOCUMENT)->count() > 0
        ) {
            return;
        }

        DB::transaction(function (): void {
            $now = now()->subMinute();

            $categoriesSpec = [
                'linh-kien-thang-cuon' => [
                    'position' => 1,
                    'vi' => ['title' => 'Linh kiện thang cuốn', 'slug' => 'linh-kien-thang-cuon'],
                    'en' => ['title' => 'Escalator Spare Parts', 'slug' => 'escalator-spare-parts'],
                ],
                'thang-nang-o-to' => [
                    'position' => 2,
                    'vi' => ['title' => 'Thang nâng ô tô', 'slug' => 'thang-nang-o-to'],
                    'en' => ['title' => 'Car Parking Lifts', 'slug' => 'car-parking-lifts'],
                ],
                'day-chuyen-dong-goi' => [
                    'position' => 3,
                    'vi' => ['title' => 'Dây chuyền đóng gói', 'slug' => 'day-chuyen-dong-goi'],
                    'en' => ['title' => 'Packaging Lines', 'slug' => 'packaging-lines'],
                ],
                'bang-tai-hanh-ly' => [
                    'position' => 4,
                    'vi' => ['title' => 'Băng tải hành lý', 'slug' => 'bang-tai-hanh-ly'],
                    'en' => ['title' => 'Baggage Conveyors', 'slug' => 'baggage-conveyors'],
                ],
                'tiet-kiem-dien' => [
                    'position' => 5,
                    'vi' => ['title' => 'Tiết kiệm điện', 'slug' => 'tiet-kiem-dien'],
                    'en' => ['title' => 'Energy Saving', 'slug' => 'energy-saving'],
                ],
                'xich-cong-nghiep' => [
                    'position' => 6,
                    'vi' => ['title' => 'Xích công nghiệp', 'slug' => 'xich-cong-nghiep'],
                    'en' => ['title' => 'Industrial Chains', 'slug' => 'industrial-chains'],
                ],
            ];

            $categoryMap = [];
            foreach ($categoriesSpec as $key => $spec) {
                $category = PostCategory::create([
                    'status' => PostCategory::STATUS_ACTIVE,
                    'type' => PostCategory::TYPE_DOCUMENT,
                    'position' => $spec['position'],
                ]);

                foreach (['vi', 'en'] as $locale) {
                    $tr = $spec[$locale];
                    $category->translations()->create([
                        'locale' => $locale,
                        'title' => $tr['title'],
                        'slug' => $tr['slug'],
                        'seo_slug' => $tr['slug'],
                        'seo_meta_title' => $tr['title'],
                        'seo_meta_description' => $tr['title'],
                    ]);
                }

                $categoryMap[$key] = $category->id;
            }

            $documentsSpec = [
                // 1-10: Khớp chính xác thứ tự & tiêu đề Figma 1264:49226
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Catalogue tổng hợp Xích thang cuốn Universal Chain (Bậc thang thương mại & công cộng)',
                    'en_title' => 'Universal Chain Escalator Step Chain General Catalogue (Commercial & Public Duty)',
                    'vi_desc' => 'Tổng hợp thông số tải trọng, bước xích và bản vẽ kỹ thuật xích thang cuốn Universal Chain.',
                    'en_desc' => 'Technical specifications, breaking loads, and engineering drawings for Universal Chain escalator step chains.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Data sheet Xích bãi đỗ xe tuần hoàn đứng LTAL666 (Vertical circulation)',
                    'en_title' => 'Data Sheet: Vertical Circulation Parking Chain LTAL666',
                    'vi_desc' => 'Thông số kỹ thuật chi tiết xích bãi đỗ xe tuần hoàn đứng LTAL666 chịu tải nặng.',
                    'en_desc' => 'Detailed technical datasheet for heavy-duty LTAL666 vertical circulation car parking chains.',
                ],
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Thông số kỹ thuật Tay vịn thang cuốn & Bánh xe thang cuốn chính hãng',
                    'en_title' => 'Genuine Escalator Handrail & Step Roller Technical Specifications',
                    'vi_desc' => 'Bảng quy cách tay vịn thang cuốn C-type và bánh xe bậc thang độ bền cao.',
                    'en_desc' => 'Sizing tables and material specifications for C-type escalator handrails and high-durability step rollers.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Catalogue Xích bãi đỗ xe lập thể & Xích dẫn động thang nâng ô tô',
                    'en_title' => '3D Automated Parking Chain & Car Lift Drive Chain Catalogue',
                    'vi_desc' => 'Danh mục xích nâng và xích dẫn động cho hệ thống bãi đỗ xe tự động nhiều tầng.',
                    'en_desc' => 'Catalogue of lifting and drive chains for multi-level automated car parking systems.',
                ],
                [
                    'category' => 'day-chuyen-dong-goi',
                    'vi_title' => 'Data sheet Xích con lăn trên, Xích chốt rỗng & Xích inox dây chuyền đóng gói',
                    'en_title' => 'Data Sheet: Top Roller, Hollow Pin & Stainless Steel Packaging Chains',
                    'vi_desc' => 'Tài liệu kỹ thuật xích con lăn trên, xích chốt rỗng và xích inox chống ăn mòn ngành thực phẩm.',
                    'en_desc' => 'Engineering data for top roller chains, hollow pin chains, and corrosion-resistant stainless steel packaging chains.',
                ],
                [
                    'category' => 'bang-tai-hanh-ly',
                    'vi_title' => 'Catalogue Xích băng tải hành lý sân bay – M Series & FV Series',
                    'en_title' => 'Airport Baggage Conveyor Chain Catalogue – M Series & FV Series',
                    'vi_desc' => 'Catalogue xích băng tải công nghiệp chuẩn DIN M Series và FV Series cho nhà ga sân bay.',
                    'en_desc' => 'DIN M Series and FV Series conveyor chain catalogue engineered for airport baggage handling systems.',
                ],
                [
                    'category' => 'tiet-kiem-dien',
                    'vi_title' => 'Tài liệu kỹ thuật Quạt BLDC & Giải pháp tiết kiệm điện cho thang cuốn',
                    'en_title' => 'BLDC Motor Fan Technical Manual & Escalator Energy Saving Solutions',
                    'vi_desc' => 'Giải pháp tiết kiệm điện bằng động cơ quạt BLDC và điều khiển tiết kiệm năng lượng thang cuốn.',
                    'en_desc' => 'Energy efficiency guide featuring BLDC motor fans and escalator power-saving control modules.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Quy chuẩn kích thước Xích con lăn tiêu chuẩn ANSI / DIN & Bảng tra mã',
                    'en_title' => 'ANSI / DIN Standard Roller Chain Dimensions & Part Code Reference',
                    'vi_desc' => 'Bảng tra mã xích con lăn công nghiệp chuẩn ANSI B29.1 và DIN 8187 từ 25 đến 240.',
                    'en_desc' => 'Cross-reference chart and dimensional standards for ANSI B29.1 and DIN 8187 roller chains.',
                ],
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Hướng dẫn kiểm tra, đo đạc bước xích & bảo trì xích thang cuốn định kỳ',
                    'en_title' => 'Escalator Chain Pitch Inspection, Measurement & Periodic Maintenance Guide',
                    'vi_desc' => 'Quy trình đo độ giãn bước xích bằng thước chuyên dụng và lịch bảo trì định kỳ.',
                    'en_desc' => 'Step-by-step chain elongation measurement procedure and preventive maintenance checklist.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Hồ sơ năng lực & Chứng nhận đại lý ủy quyền Thành Khang Tecsat (CO/CQ)',
                    'en_title' => 'Company Profile & Authorized Distributor Certificates (CO/CQ) – Thanh Khang Tecsat',
                    'vi_desc' => 'Hồ sơ năng lực nhà phân phối chính hãng và mẫu chứng nhận xuất xứ, chất lượng CO/CQ.',
                    'en_desc' => 'Corporate capability profile and authorized distributor CO/CQ certification package.',
                ],

                // 11-33: Phân bổ đều qua 6 danh mục để tổng = 33 tài liệu (4 trang: 10+10+10+3)
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Thông số kỹ thuật Bậc thang cuốn hợp kim nhôm & Răng lược tiếp đất',
                    'en_title' => 'Die-Cast Aluminum Escalator Step & Comb Plate Technical Specifications',
                    'vi_desc' => 'Kích thước lắp đặt bậc thang 600/800/1000mm và tấm lược an toàn cho thang cuốn thương mại.',
                    'en_desc' => 'Installation dimensions for 600/800/1000mm aluminum steps and safety comb plates.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Data sheet Xích lá (Leaf Chain) AL & BL Series cho hệ thống nâng hạ thủy lực',
                    'en_title' => 'Data Sheet: AL & BL Series Leaf Chains for Hydraulic Car Lifting Systems',
                    'vi_desc' => 'Bảng tải kéo đứt tối thiểu và cấu trúc lá thép gia cường cho thang nâng ô tô.',
                    'en_desc' => 'Minimum tensile strength tables and reinforced plate geometry for hydraulic parking lifts.',
                ],
                [
                    'category' => 'day-chuyen-dong-goi',
                    'vi_title' => 'Catalogue Xích tai gắn (Attachment Chain) A1, K1, WA & WK cho máy đóng gói',
                    'en_title' => 'A1, K1, WA & WK Attachment Chain Catalogue for Automated Packaging Machines',
                    'vi_desc' => 'Bản vẽ định vị tai gắn tiêu chuẩn giúp đồng bộ hóa dây chuyền chiết rót và đóng thùng.',
                    'en_desc' => 'Standard attachment drawings for synchronized bottling and carton packaging lines.',
                ],
                [
                    'category' => 'bang-tai-hanh-ly',
                    'vi_title' => 'Hướng dẫn chọn xích băng tải bước dài C2040 – C2160H cho băng chuyền vận tải',
                    'en_title' => 'Selection Guide: Double Pitch Conveyor Chains C2040 – C2160H',
                    'vi_desc' => 'Phương pháp tính lực căng xích và chọn con lăn S/R phù hợp băng tải đường dài.',
                    'en_desc' => 'Chain tension calculation and S/R roller selection for long-span conveyor runs.',
                ],
                [
                    'category' => 'tiet-kiem-dien',
                    'vi_title' => 'Data sheet Bộ biến tần tái sinh năng lượng & Cảm biến lưu lượng thang cuốn',
                    'en_title' => 'Data Sheet: Regenerative VFD & Passenger Flow Radar Sensors for Escalators',
                    'vi_desc' => 'Cấu hình giảm tốc không tải và khởi động mềm giúp cắt giảm 35–50% điện năng tiêu thụ.',
                    'en_desc' => 'Idle speed reduction and soft-start control profiles delivering 35–50% energy savings.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Catalogue Xích tải hạng nặng (Heavy Duty Drive Chain) H Series & Super Roller',
                    'en_title' => 'Heavy Duty Drive Chain Catalogue – H Series & Super Roller Series',
                    'vi_desc' => 'Dòng xích tải chịu va đập và momen xoắn lớn cho ngành thép, xi măng và khai khoáng.',
                    'en_desc' => 'High-impact, high-torque roller chains for steel mills, cement plants, and mining.',
                ],
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Bảng mã thay thế Bánh nhông thang cuốn (Main Drive & Step Chain Sprocket)',
                    'en_title' => 'Escalator Main Drive & Step Chain Sprocket Replacement Cross-Reference',
                    'vi_desc' => 'Thông số số răng, vật liệu tôi cảm ứng và độ đảo cho phép của nhông thang cuốn.',
                    'en_desc' => 'Tooth profile, induction-hardened alloy steel grade, and runout tolerances for escalator sprockets.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Hướng dẫn cân chỉnh độ căng đôi xích nâng cho thang nâng ô tô 2 trụ & 4 trụ',
                    'en_title' => 'Dual Lifting Chain Tension Balancing Guide for 2-Post & 4-Post Car Lifts',
                    'vi_desc' => 'Quy trình đồng bộ chiều dài cặp xích nâng tránh nghiêng sàn và mòn lệch chốt xích.',
                    'en_desc' => 'Synchronization procedure to prevent platform tilt and uneven chain pin wear.',
                ],
                [
                    'category' => 'day-chuyen-dong-goi',
                    'vi_title' => 'Tài liệu kỹ thuật Xích nhựa kỹ thuật & Xích băng tải cong (Side-Bow Chain)',
                    'en_title' => 'Engineering Data: Engineered Plastic & Side-Bow Conveyor Chains',
                    'vi_desc' => 'Bán kính uốn cong tối thiểu và giới hạn nhiệt độ cho dây chuyền đóng gói nhiều góc cua.',
                    'en_desc' => 'Minimum side-flex radius and thermal ratings for curved packaging conveyors.',
                ],
                [
                    'category' => 'bang-tai-hanh-ly',
                    'vi_title' => 'Tiêu chuẩn độ ồn & Giảm chấn xích băng tải hành lý nhà ga quốc tế',
                    'en_title' => 'Low-Noise & Vibration Damping Standards for Terminal Baggage Conveyor Chains',
                    'vi_desc' => 'Giải pháp bạc nhựa tự bôi trơn và con lăn giảm ồn đạt chuẩn âm học sân bay.',
                    'en_desc' => 'Self-lubricating polymer bushings and acoustic-damped rollers for airport terminals.',
                ],
                [
                    'category' => 'tiet-kiem-dien',
                    'vi_title' => 'Báo cáo thử nghiệm hiệu suất Động cơ quạt BLDC so với động cơ AC truyền thống',
                    'en_title' => 'Performance Benchmark Report: BLDC Fan Motors vs Conventional AC Motors',
                    'vi_desc' => 'Số liệu đo công suất thực tế và nhiệt độ vận hành liên tục trong hầm máy thang cuốn.',
                    'en_desc' => 'Measured power draw and thermal stability data in continuous escalator pit operation.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Tài liệu kỹ thuật Xích chịu nhiệt & Xích không cần bôi trơn (Self-Lubricating)',
                    'en_title' => 'Technical Guide: High-Temperature & Self-Lubricating Sintered Bush Chains',
                    'vi_desc' => 'Ứng dụng bạc xích thiêu kết tẩm dầu cho môi trường sấy nhiệt hoặc hạn chế dầu mỡ.',
                    'en_desc' => 'Oil-impregnated sintered bushing chains for ovens and clean-environment lines.',
                ],
                [
                    'category' => 'linh-kien-thang-cuon',
                    'vi_title' => 'Quy trình thay thế Bàn chải an toàn (Skirt Brush) & Công tắc an toàn thang cuốn',
                    'en_title' => 'Escalator Skirt Brush & Safety Switch Replacement Manual',
                    'vi_desc' => 'Tiêu chuẩn khe hở mép bậc thang theo EN 115 và quy cách lắp thanh chổi an toàn.',
                    'en_desc' => 'EN 115 step-skirt clearance tolerances and safety brush mounting specifications.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Bảng tra tải trọng Xích bãi đỗ xe Puzzling Parking & Hệ thống xoay mâm xe',
                    'en_title' => 'Load Rating Chart: Puzzle Parking Chains & Car Turntable Drive Chains',
                    'vi_desc' => 'Hệ số an toàn tĩnh/động khi thiết kế hệ thống bãi đỗ xe xếp hình nhiều tầng.',
                    'en_desc' => 'Static and dynamic safety factors for puzzle parking and turntable drive assemblies.',
                ],
                [
                    'category' => 'day-chuyen-dong-goi',
                    'vi_title' => 'Hướng dẫn chọn Xích mạ niken (Nickel Plated) & Xích Dacromet chống gỉ',
                    'en_title' => 'Selection Guide: Nickel-Plated & Dacromet Corrosion-Resistant Chains',
                    'vi_desc' => 'So sánh thời gian thử phun muối của các lớp phủ bề mặt cho kho lạnh và xưởng đồ uống.',
                    'en_desc' => 'Salt-spray test comparisons for cold storage and beverage bottling environments.',
                ],
                [
                    'category' => 'bang-tai-hanh-ly',
                    'vi_title' => 'Catalogue Xích chốt đặc & Chốt rỗng FV/FVT dùng cho băng tải nâng pallet',
                    'en_title' => 'FV / FVT Solid Pin & Hollow Pin Chain Catalogue for Pallet Conveyors',
                    'vi_desc' => 'Thông số lắp thanh ngang xuyên suốt chốt rỗng và khả năng đồng bộ hai dãy xích.',
                    'en_desc' => 'Through-rod mounting specs for hollow pins and matched-strand conveyor chain tolerances.',
                ],
                [
                    'category' => 'tiet-kiem-dien',
                    'vi_title' => 'Hướng dẫn cải tạo hệ thống chiếu sáng LED & Quạt thông gió BLDC cho tòa nhà',
                    'en_title' => 'Retrofit Guide: Escalator LED Lighting & BLDC Ventilation Upgrades',
                    'vi_desc' => 'Giải pháp nâng cấp nhanh tại chỗ giúp trung tâm thương mại giảm chi phí vận hành.',
                    'en_desc' => 'Turnkey retrofit package helping commercial malls lower annual escalator OPEX.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Tài liệu kỹ thuật Khớp nối xích (Roller Chain Coupling) & Nhông công nghiệp',
                    'en_title' => 'Roller Chain Couplings & Industrial Sprocket Engineering Manual',
                    'vi_desc' => 'Bảng tra mã khớp nối 4012–16022, momen truyền tải và độ lệch trục cho phép.',
                    'en_desc' => 'Coupling codes 4012–16022, torque ratings, and allowable shaft misalignment limits.',
                ],
                [
                    'category' => 'thang-nang-o-to',
                    'vi_title' => 'Tiêu chuẩn bảo dưỡng & Bôi trơn xích bãi đỗ xe ngoài trời chống ăn mòn',
                    'en_title' => 'Anti-Corrosion Lubrication & Maintenance Standard for Outdoor Parking Chains',
                    'vi_desc' => 'Khuyến nghị loại mỡ bám dính chịu nước mưa và chu kỳ kiểm tra chốt xích.',
                    'en_desc' => 'Water-resistant tackified lubricant recommendations and pin inspection intervals.',
                ],
                [
                    'category' => 'day-chuyen-dong-goi',
                    'vi_title' => 'Data sheet Xích có chốt mở rộng (Extended Pin Chain) cho máy dán nhãn tự động',
                    'en_title' => 'Data Sheet: Extended Pin Chains for High-Speed Automatic Labeling Lines',
                    'vi_desc' => 'Kích thước chốt nhô tiêu chuẩn D1, D3 và độ chính xác bước xích đồng bộ.',
                    'en_desc' => 'Standard D1 and D3 extended pin dimensions with matched pitch accuracy.',
                ],
                [
                    'category' => 'bang-tai-hanh-ly',
                    'vi_title' => 'Sổ tay lắp đặt & Căn chỉnh đồng tâm băng tải hành lý tuyến dài',
                    'en_title' => 'Alignment & Installation Handbook for Long-Run Baggage Conveyor Lines',
                    'vi_desc' => 'Kiểm tra song song trục nhông, độ võng nhánh chùng và chạy rà không tải 24 giờ.',
                    'en_desc' => 'Shaft parallelism checks, catenary sag settings, and 24-hour run-in procedure.',
                ],
                [
                    'category' => 'tiet-kiem-dien',
                    'vi_title' => 'Bảng tính ROI & Thời gian hoàn vốn khi chuyển đổi Quạt BLDC Thành Khang',
                    'en_title' => 'ROI & Payback Period Calculator for Thanh Khang BLDC Fan Upgrades',
                    'vi_desc' => 'Mô hình ước tính tiết kiệm điện theo giờ vận hành thực tế tại sân bay và metro.',
                    'en_desc' => 'Electricity cost-saving model tailored to metro stations and international airports.',
                ],
                [
                    'category' => 'xich-cong-nghiep',
                    'vi_title' => 'Bảng tra nhanh Công thức tính chiều dài xích, số mắt xích & Tỉ số truyền',
                    'en_title' => 'Quick Engineering Reference: Chain Length, Links & Drive Ratio Formulas',
                    'vi_desc' => 'Công thức chuẩn tính số mắt xích theo khoảng cách tâm trục và số răng nhông.',
                    'en_desc' => 'Standard engineering formulas for calculating chain pitch count from center distance.',
                ],
            ];

            foreach ($documentsSpec as $index => $docSpec) {
                $position = $index + 1;
                $viSlug = Str::slug($docSpec['vi_title']);
                $enSlug = Str::slug($docSpec['en_title']);

                $post = Post::create([
                    'status' => Post::STATUS_ACTIVE,
                    'type' => Post::TYPE_DOCUMENT,
                    'position' => $position,
                    'download' => null,
                    'published_at' => $now,
                    'is_home' => 0,
                    'is_featured' => 0,
                    'view_count' => 0,
                    'virtual_views' => 0,
                ]);

                $post->translations()->create([
                    'locale' => 'vi',
                    'title' => $docSpec['vi_title'],
                    'slug' => $viSlug,
                    'seo_slug' => $viSlug,
                    'description' => $docSpec['vi_desc'],
                    'content' => '<p>' . e($docSpec['vi_desc']) . '</p>',
                    'seo_meta_title' => $docSpec['vi_title'],
                    'seo_meta_description' => $docSpec['vi_desc'],
                    'status_locale' => 'ACTIVE',
                ]);

                $post->translations()->create([
                    'locale' => 'en',
                    'title' => $docSpec['en_title'],
                    'slug' => $enSlug,
                    'seo_slug' => $enSlug,
                    'description' => $docSpec['en_desc'],
                    'content' => '<p>' . e($docSpec['en_desc']) . '</p>',
                    'seo_meta_title' => $docSpec['en_title'],
                    'seo_meta_description' => $docSpec['en_desc'],
                    'status_locale' => 'ACTIVE',
                ]);

                $catId = $categoryMap[$docSpec['category']] ?? null;
                if ($catId !== null) {
                    $post->categories()->sync([$catId]);
                }
            }
        });

        PostCategory::clearPostCategoryCaches();
        Post::clearPostCaches();
    }
}
