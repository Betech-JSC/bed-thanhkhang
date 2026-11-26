<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Product\ProductCategory;

class ThanhKhangProductCatalogSeeder extends Seeder
{
    /**
     * Idempotent seeder for verified Thanh Khang Tecsat products & 3-tier categories.
     * Source: CSV catalog + Excel product specifications + staged local images/drawings.
     * Unmapped / empty categories (SBR, CSM, Hybrid, Bánh xe thang cuốn, Xích dẫn động, Xích inox, Quạt BLDC) are excluded.
     */
    public function run(): void
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        DB::transaction(function () {
            $now = now();
            $catMap = [];

            $rootCategories = [
                'linh-kien-thang-cuon' => [
                    'position' => 1,
                    'vi' => ['title' => 'Linh kiện thang cuốn', 'slug' => 'linh-kien-thang-cuon', 'desc' => 'Tổng kho xích bậc thang cuốn, xích băng tải hành khách, xích dẫn động và xích tay vịn chính hãng Universal Chains.'],
                    'en' => ['title' => 'Escalator Spare Parts', 'slug' => 'escalator-spare-parts', 'desc' => 'Complete inventory of escalator step chains, passenger conveyor chains, main drive chains, and handrail drive chains from Universal Chains.'],
                ],
                'linh-kien-thang-nang-o-to' => [
                    'position' => 2,
                    'vi' => ['title' => 'Linh kiện thang nâng ô tô', 'slug' => 'linh-kien-thang-nang-o-to', 'desc' => 'Xích bãi đỗ xe lập thể và tuần hoàn đứng chịu tải trọng lớn cho hệ thống đỗ xe thông minh.'],
                    'en' => ['title' => 'Car Parking Lift Components', 'slug' => 'car-parking-lift-components', 'desc' => 'Heavy-duty chains for multi-level puzzle parking and vertical rotary car parking lift systems.'],
                ],
                'he-thong-bang-tai-hanh-ly' => [
                    'position' => 3,
                    'vi' => ['title' => 'Hệ thống băng tải hành lý', 'slug' => 'he-thong-bang-tai-hanh-ly', 'desc' => 'Xích con lăn cạnh và xích con lăn trên chuyên dụng cho băng tải hành lý sân bay và kho vận logistics.'],
                    'en' => ['title' => 'Baggage Conveyor Systems', 'slug' => 'baggage-conveyor-systems', 'desc' => 'Side roller and top roller conveyor chains engineered for airport baggage handling and logistics systems.'],
                ],
                'linh-kien-day-chuyen-dong-goi' => [
                    'position' => 4,
                    'vi' => ['title' => 'Linh kiện dây chuyền đóng gói', 'slug' => 'linh-kien-day-chuyen-dong-goi', 'desc' => 'Xích chốt rỗng, xích con lăn trên, xích cong/chống cong và xích tải M/FV series cho dây chuyền đóng gói và băng tải nhẹ.'],
                    'en' => ['title' => 'Packaging Line Components', 'slug' => 'packaging-line-components', 'desc' => 'Hollow pin chains, top roller chains, side-bow/anti-bow chains, and M/FV series conveyor chains for packaging machinery and light conveyors.'],
                ],
            ];

            foreach ($rootCategories as $key => $cat) {
                $catMap[$key] = $this->upsertCategory(0, $cat['position'], $cat['vi'], $cat['en'], $now);
            }

            $subCategories = [
                'xich-thang-cuon' => [
                    'parent_key' => 'linh-kien-thang-cuon',
                    'position' => 1,
                    'vi' => ['title' => 'Xích thang cuốn', 'slug' => 'xich-thang-cuon', 'desc' => 'Xích bậc thang thương mại, công cộng, không cần bảo trì, xích băng tải hành khách, xích dẫn động chính và xích tay vịn.'],
                    'en' => ['title' => 'Escalator Chains', 'slug' => 'escalator-chains', 'desc' => 'Xích bậc thang thương mại, công cộng, không cần bảo trì, xích băng tải hành khách, xích dẫn động chính và xích tay vịn.'],
                ],
                'xich-bai-do-xe' => [
                    'parent_key' => 'linh-kien-thang-nang-o-to',
                    'position' => 1,
                    'vi' => ['title' => 'Xích bãi đỗ xe', 'slug' => 'xich-bai-do-xe', 'desc' => 'Xích bãi đỗ xe lập thể có bu lông điều chỉnh và xích bãi đỗ xe tuần hoàn đứng.'],
                    'en' => ['title' => 'Car Parking Lift Chains', 'slug' => 'car-parking-lift-chains', 'desc' => 'Xích bãi đỗ xe lập thể có bu lông điều chỉnh và xích bãi đỗ xe tuần hoàn đứng.'],
                ],
                'xich-bang-tai-hanh-ly-logistics' => [
                    'parent_key' => 'he-thong-bang-tai-hanh-ly',
                    'position' => 1,
                    'vi' => ['title' => 'Xích băng tải hành lý/logistics', 'slug' => 'xich-bang-tai-hanh-ly-logistics', 'desc' => 'Xích con lăn cạnh (SR) và xích con lăn trên (TR) cho hệ thống băng tải hành lý và logistics.'],
                    'en' => ['title' => 'Baggage & Logistics Conveyor Chains', 'slug' => 'baggage-logistics-conveyor-chains', 'desc' => 'Xích con lăn cạnh (SR) và xích con lăn trên (TR) cho hệ thống băng tải hành lý và logistics.'],
                ],
                'xich-day-chuyen-dong-goi' => [
                    'parent_key' => 'linh-kien-day-chuyen-dong-goi',
                    'position' => 1,
                    'vi' => ['title' => 'Xích dây chuyền đóng gói', 'slug' => 'xich-day-chuyen-dong-goi', 'desc' => 'Xích chốt rỗng, xích con lăn trên và xích cong/chống cong cho máy đóng gói và băng tải định hình.'],
                    'en' => ['title' => 'Packaging Line Chains', 'slug' => 'packaging-line-chains', 'desc' => 'Xích chốt rỗng, xích con lăn trên và xích cong/chống cong cho máy đóng gói và băng tải định hình.'],
                ],
                'xich-bang-tai-nhe' => [
                    'parent_key' => 'linh-kien-day-chuyen-dong-goi',
                    'position' => 2,
                    'vi' => ['title' => 'Xích băng tải nhẹ', 'slug' => 'xich-bang-tai-nhe', 'desc' => 'Xích chốt rỗng, xích con lăn trên và xích tải hệ mét M series, FV series cho băng tải nhẹ và công nghiệp.'],
                    'en' => ['title' => 'Light Conveyor Chains', 'slug' => 'light-conveyor-chains', 'desc' => 'Xích chốt rỗng, xích con lăn trên và xích tải hệ mét M series, FV series cho băng tải nhẹ và công nghiệp.'],
                ],
            ];

            foreach ($subCategories as $key => $cat) {
                $catMap[$key] = $this->upsertCategory($catMap[$cat['parent_key']], $cat['position'], $cat['vi'], $cat['en'], $now);
            }

            $leafCategories = [
                'xich-bac-thang-thuong-mai' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 1,
                    'vi' => ['title' => 'Xích bậc thang thương mại', 'slug' => 'xich-bac-thang-thuong-mai', 'desc' => 'Xích bậc thang thương mại'],
                    'en' => ['title' => 'Commercial Escalator Step Chains', 'slug' => 'commercial-escalator-step-chains', 'desc' => 'Commercial Escalator Step Chains'],
                ],
                'xich-bang-tai-hanh-khach' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 2,
                    'vi' => ['title' => 'Xích băng tải hành khách', 'slug' => 'xich-bang-tai-hanh-khach', 'desc' => 'Xích băng tải hành khách'],
                    'en' => ['title' => 'Moving Walkway Chains', 'slug' => 'moving-walkway-chains', 'desc' => 'Moving Walkway Chains'],
                ],
                'xich-khong-can-bao-tri' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 3,
                    'vi' => ['title' => 'Xích không cần bảo trì', 'slug' => 'xich-khong-can-bao-tri', 'desc' => 'Xích không cần bảo trì'],
                    'en' => ['title' => 'Maintenance-Free Escalator Chains', 'slug' => 'maintenance-free-escalator-chains', 'desc' => 'Maintenance-Free Escalator Chains'],
                ],
                'xich-bac-thang-cong-cong' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 4,
                    'vi' => ['title' => 'Xích bậc thang công cộng', 'slug' => 'xich-bac-thang-cong-cong', 'desc' => 'Xích bậc thang công cộng'],
                    'en' => ['title' => 'Public Transport Escalator Step Chains', 'slug' => 'public-transport-escalator-step-chains', 'desc' => 'Public Transport Escalator Step Chains'],
                ],
                'xich-dan-dong-chinh' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 5,
                    'vi' => ['title' => 'Xích dẫn động chính', 'slug' => 'xich-dan-dong-chinh', 'desc' => 'Xích dẫn động chính'],
                    'en' => ['title' => 'Main Drive & Precision Roller Chains', 'slug' => 'main-drive-precision-roller-chains', 'desc' => 'Main Drive & Precision Roller Chains'],
                ],
                'xich-tay-vin' => [
                    'parent_key' => 'xich-thang-cuon',
                    'position' => 6,
                    'vi' => ['title' => 'Xích tay vịn', 'slug' => 'xich-tay-vin', 'desc' => 'Xích tay vịn'],
                    'en' => ['title' => 'Handrail Driving Chains', 'slug' => 'handrail-driving-chains', 'desc' => 'Handrail Driving Chains'],
                ],
                'xich-bai-do-xe-lap-the' => [
                    'parent_key' => 'xich-bai-do-xe',
                    'position' => 1,
                    'vi' => ['title' => 'Xích bãi đỗ xe lập thể', 'slug' => 'xich-bai-do-xe-lap-the', 'desc' => 'Xích bãi đỗ xe lập thể'],
                    'en' => ['title' => 'Multi-Level Parking Chains', 'slug' => 'multi-level-parking-chains', 'desc' => 'Multi-Level Parking Chains'],
                ],
                'xich-bai-do-xe-tuan-hoan-dung' => [
                    'parent_key' => 'xich-bai-do-xe',
                    'position' => 2,
                    'vi' => ['title' => 'Xích bãi đỗ xe tuần hoàn đứng', 'slug' => 'xich-bai-do-xe-tuan-hoan-dung', 'desc' => 'Xích bãi đỗ xe tuần hoàn đứng'],
                    'en' => ['title' => 'Vertical Circulation Parking Chains', 'slug' => 'vertical-circulation-parking-chains', 'desc' => 'Vertical Circulation Parking Chains'],
                ],
                'xich-con-lan-canh' => [
                    'parent_key' => 'xich-bang-tai-hanh-ly-logistics',
                    'position' => 1,
                    'vi' => ['title' => 'Xích con lăn cạnh', 'slug' => 'xich-con-lan-canh', 'desc' => 'Xích con lăn cạnh'],
                    'en' => ['title' => 'Side Roller Conveyor Chains', 'slug' => 'side-roller-conveyor-chains', 'desc' => 'Side Roller Conveyor Chains'],
                ],
                'xich-con-lan-tren-hanh-ly' => [
                    'parent_key' => 'xich-bang-tai-hanh-ly-logistics',
                    'position' => 2,
                    'vi' => ['title' => 'Xích con lăn trên', 'slug' => 'xich-con-lan-tren-hanh-ly', 'desc' => 'Xích con lăn trên'],
                    'en' => ['title' => 'Top Roller Conveyor Chains (Logistics)', 'slug' => 'top-roller-conveyor-chains-logistics', 'desc' => 'Top Roller Conveyor Chains (Logistics)'],
                ],
                'xich-chot-rong-dong-goi' => [
                    'parent_key' => 'xich-day-chuyen-dong-goi',
                    'position' => 1,
                    'vi' => ['title' => 'Xích chốt rỗng', 'slug' => 'xich-chot-rong-dong-goi', 'desc' => 'Xích chốt rỗng'],
                    'en' => ['title' => 'Hollow Pin Chains (Packaging)', 'slug' => 'hollow-pin-chains-packaging', 'desc' => 'Hollow Pin Chains (Packaging)'],
                ],
                'xich-con-lan-tren-dong-goi' => [
                    'parent_key' => 'xich-day-chuyen-dong-goi',
                    'position' => 2,
                    'vi' => ['title' => 'Xích con lăn trên', 'slug' => 'xich-con-lan-tren-dong-goi', 'desc' => 'Xích con lăn trên'],
                    'en' => ['title' => 'Top Roller Chains (Packaging)', 'slug' => 'top-roller-chains-packaging', 'desc' => 'Top Roller Chains (Packaging)'],
                ],
                'xich-cong-chong-cong' => [
                    'parent_key' => 'xich-day-chuyen-dong-goi',
                    'position' => 3,
                    'vi' => ['title' => 'Xích cong/chống cong', 'slug' => 'xich-cong-chong-cong', 'desc' => 'Xích cong/chống cong'],
                    'en' => ['title' => 'Side Bow & Anti-Side Bow Chains', 'slug' => 'side-bow-anti-side-bow-chains', 'desc' => 'Side Bow & Anti-Side Bow Chains'],
                ],
                'xich-chot-rong-bang-tai-nhe' => [
                    'parent_key' => 'xich-bang-tai-nhe',
                    'position' => 1,
                    'vi' => ['title' => 'Xích chốt rỗng', 'slug' => 'xich-chot-rong-bang-tai-nhe', 'desc' => 'Xích chốt rỗng'],
                    'en' => ['title' => 'Hollow Pin Chains (Light Conveyor)', 'slug' => 'hollow-pin-chains-light-conveyor', 'desc' => 'Hollow Pin Chains (Light Conveyor)'],
                ],
                'xich-con-lan-tren-bang-tai-nhe' => [
                    'parent_key' => 'xich-bang-tai-nhe',
                    'position' => 2,
                    'vi' => ['title' => 'Xích con lăn trên', 'slug' => 'xich-con-lan-tren-bang-tai-nhe', 'desc' => 'Xích con lăn trên'],
                    'en' => ['title' => 'Top Roller Chains (Light Conveyor)', 'slug' => 'top-roller-chains-light-conveyor', 'desc' => 'Top Roller Chains (Light Conveyor)'],
                ],
                'xich-m-series' => [
                    'parent_key' => 'xich-bang-tai-nhe',
                    'position' => 3,
                    'vi' => ['title' => 'Xích M series', 'slug' => 'xich-m-series', 'desc' => 'Xích M series'],
                    'en' => ['title' => 'M Series Conveyor Chains', 'slug' => 'm-series-conveyor-chains', 'desc' => 'M Series Conveyor Chains'],
                ],
                'xich-fv-series' => [
                    'parent_key' => 'xich-bang-tai-nhe',
                    'position' => 4,
                    'vi' => ['title' => 'Xích FV series', 'slug' => 'xich-fv-series', 'desc' => 'Xích FV series'],
                    'en' => ['title' => 'FV Series Conveyor Chains', 'slug' => 'fv-series-conveyor-chains', 'desc' => 'FV Series Conveyor Chains'],
                ],
            ];

            foreach ($leafCategories as $key => $cat) {
                $catMap[$key] = $this->upsertCategory($catMap[$cat['parent_key']], $cat['position'], $cat['vi'], $cat['en'], $now);
            }

            $productsData = [
                [
                    'sku' => 'TL133',
                    'display_code' => 'TL133',
                    'title_vi' => 'Xích bậc thang cuốn thương mại TL133',
                    'title_en' => 'Commercial Escalator Step Chain TL133',
                    'slug_vi' => 'xich-bac-thang-cuon-thuong-mai-tl133',
                    'slug_en' => 'commercial-escalator-step-chain-tl133',
                    'categories' => ['xich-bac-thang-thuong-mai'],
                    'is_featured' => 1,
                    'desc_vi' => 'TL133 có bước xích 133.33 mm, con lăn Ø70 mm, chốt Ø14.63 mm và lực kéo đứt tối thiểu 180 kN. Xích bậc thang cuốn thương mại nối và đồng bộ chuyển động của các bậc thang, giữ tốc độ và khoảng cách giữa các bậc ổn định để thang vận hành êm, an toàn. Xích chịu tải nặng và làm việc liên tục, phù hợp thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại, siêu thị, khách sạn và tòa nhà văn phòng.',
                    'structure_vi' => 'Xích bậc thang cuốn thương mại dùng cho thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại và các nơi công cộng khác. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo loại thang của khách.
• Liên kết với bậc thang: kiểu phụ kiện D; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách giữa các bậc 400 mm.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn thương mại được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/tl133-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '70'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '14.63'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '40'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '35'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '56.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '180'],
                        ['label' => 'Kiểu phụ kiện', 'value' => 'D'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '400'],
                    ],
                ],
                [
                    'sku' => 'TL133H',
                    'display_code' => 'TL133H',
                    'title_vi' => 'Xích bậc thang cuốn thương mại TL133H',
                    'title_en' => 'Commercial Escalator Step Chain TL133H',
                    'slug_vi' => 'xich-bac-thang-cuon-thuong-mai-tl133h',
                    'slug_en' => 'commercial-escalator-step-chain-tl133h',
                    'categories' => ['xich-bac-thang-thuong-mai'],
                    'is_featured' => 1,
                    'desc_vi' => 'TL133H có bước xích 133.33 mm, con lăn Ø80 mm, chốt Ø24 mm và lực kéo đứt tối thiểu 225 kN. Xích bậc thang cuốn thương mại nối và đồng bộ chuyển động của các bậc thang, giữ tốc độ và khoảng cách giữa các bậc ổn định để thang vận hành êm, an toàn. Xích chịu tải nặng và làm việc liên tục, phù hợp thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại, siêu thị, khách sạn và tòa nhà văn phòng.',
                    'structure_vi' => 'Xích bậc thang cuốn thương mại dùng cho thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại và các nơi công cộng khác. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo loại thang của khách.
• Liên kết với bậc thang: kiểu phụ kiện D; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách giữa các bậc 400 mm.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn thương mại được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/tl133h-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '80'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '24'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '50'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '45'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '56.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '225'],
                        ['label' => 'Kiểu phụ kiện', 'value' => 'D'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '400'],
                    ],
                ],
                [
                    'sku' => 'TL135',
                    'display_code' => 'TL135',
                    'title_vi' => 'Xích bậc thang cuốn thương mại TL135',
                    'title_en' => 'Commercial Escalator Step Chain TL135',
                    'slug_vi' => 'xich-bac-thang-cuon-thuong-mai-tl135',
                    'slug_en' => 'commercial-escalator-step-chain-tl135',
                    'categories' => ['xich-bac-thang-thuong-mai'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135 có bước xích 135.47 mm, con lăn Ø76.2 mm, chốt Ø12.8 mm và lực kéo đứt tối thiểu 123 kN. Xích bậc thang cuốn thương mại nối và đồng bộ chuyển động của các bậc thang, giữ tốc độ và khoảng cách giữa các bậc ổn định để thang vận hành êm, an toàn. Xích chịu tải nặng và làm việc liên tục, phù hợp thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại, siêu thị, khách sạn và tòa nhà văn phòng.',
                    'structure_vi' => 'Xích bậc thang cuốn thương mại dùng cho thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại và các nơi công cộng khác. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo loại thang của khách.
• Liên kết với bậc thang: kiểu phụ kiện D; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách giữa các bậc 406.4 mm.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn thương mại được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/tl135-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135.47'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '76.2'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '22.3'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '12.8'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '32'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '32'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '50.3'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '123'],
                        ['label' => 'Kiểu phụ kiện', 'value' => 'D'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '406.4'],
                    ],
                ],
                [
                    'sku' => 'TL135Hx',
                    'display_code' => 'TL135Hx',
                    'title_vi' => 'Xích bậc thang cuốn thương mại TL135Hx',
                    'title_en' => 'Commercial Escalator Step Chain TL135Hx',
                    'slug_vi' => 'xich-bac-thang-cuon-thuong-mai-tl135hx',
                    'slug_en' => 'commercial-escalator-step-chain-tl135hx',
                    'categories' => ['xich-bac-thang-thuong-mai'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135Hx có bước xích 135.47 mm, con lăn Ø76.2 mm, chốt Ø25 mm và lực kéo đứt tối thiểu 200 kN. Xích bậc thang cuốn thương mại nối và đồng bộ chuyển động của các bậc thang, giữ tốc độ và khoảng cách giữa các bậc ổn định để thang vận hành êm, an toàn. Xích chịu tải nặng và làm việc liên tục, phù hợp thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại, siêu thị, khách sạn và tòa nhà văn phòng.',
                    'structure_vi' => 'Xích bậc thang cuốn thương mại dùng cho thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại và các nơi công cộng khác. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo loại thang của khách.
• Liên kết với bậc thang: kiểu phụ kiện D; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách giữa các bậc 406.4 mm.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn thương mại được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/tl135hx-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135.47'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '76.2'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '23.6'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '25'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '45'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '45'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '54.1'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '200'],
                        ['label' => 'Kiểu phụ kiện', 'value' => 'D'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '406.4'],
                    ],
                ],
                [
                    'sku' => 'TL135Hx1',
                    'display_code' => 'TL135Hx1',
                    'title_vi' => 'Xích bậc thang cuốn thương mại TL135Hx1',
                    'title_en' => 'Commercial Escalator Step Chain TL135Hx1',
                    'slug_vi' => 'xich-bac-thang-cuon-thuong-mai-tl135hx1',
                    'slug_en' => 'commercial-escalator-step-chain-tl135hx1',
                    'categories' => ['xich-bac-thang-thuong-mai'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135Hx1 có bước xích 135.47 mm, con lăn Ø76.2 mm, chốt Ø20 mm và lực kéo đứt tối thiểu 150 kN. Xích bậc thang cuốn thương mại nối và đồng bộ chuyển động của các bậc thang, giữ tốc độ và khoảng cách giữa các bậc ổn định để thang vận hành êm, an toàn. Xích chịu tải nặng và làm việc liên tục, phù hợp thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại, siêu thị, khách sạn và tòa nhà văn phòng.',
                    'structure_vi' => 'Xích bậc thang cuốn thương mại dùng cho thang cuốn tại nhà ga, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại và các nơi công cộng khác. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo loại thang của khách.
• Liên kết với bậc thang: kiểu phụ kiện D; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách giữa các bậc 406.4 mm.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn thương mại được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp', '/images/products/thanhkhang/xich-bac-thang-thuong-mai/tl135hx1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135.47'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '76.2'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '23.6'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '20'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '45'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '45'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '51.1'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '150'],
                        ['label' => 'Kiểu phụ kiện', 'value' => 'D'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '406.4'],
                    ],
                ],
                [
                    'sku' => 'RL133',
                    'display_code' => 'RL133',
                    'title_vi' => 'Xích băng tải hành khách RL133',
                    'title_en' => 'Moving Walkway Passenger Chain RL133',
                    'slug_vi' => 'xich-bang-tai-hanh-khach-rl133',
                    'slug_en' => 'moving-walkway-passenger-chain-rl133',
                    'categories' => ['xich-bang-tai-hanh-khach'],
                    'is_featured' => 1,
                    'desc_vi' => 'RL133 có bước xích 133.33 mm, con lăn Ø70 mm, chốt Ø14.63 mm và lực kéo đứt tối thiểu 130 kN (má xích chưa nhiệt luyện). Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, dùng cho băng tải người đi tại các khu vực chủ yếu nằm ngang hoặc có độ dốc thoải như sân bay lớn và siêu thị. Xích giúp băng tải vận hành êm, đồng bộ và an toàn khi chịu tải liên tục. Ngoài phiên bản tiêu chuẩn 130 kN (má xích chưa nhiệt luyện), mã RL133 còn có tùy chọn má xích nhiệt luyện đạt lực kéo đứt tối thiểu 180 kN.',
                    'structure_vi' => 'Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, gồm các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao. Má xích có thể không nhiệt luyện nếu đạt yêu cầu độ bền; khi nhiệt luyện, lực kéo đứt tối thiểu của xích tăng từ 130 kN lên 180 kN.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo yêu cầu.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Dòng xích thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp', '/images/products/thanhkhang/xich-bang-tai-hanh-khach/rl133-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '70'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '14.63'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '40'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '56.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '130 kN (chưa nhiệt luyện) / 180 kN (nhiệt luyện)'],
                    ],
                ],
                [
                    'sku' => 'RL133H',
                    'display_code' => 'RL133H',
                    'title_vi' => 'Xích băng tải hành khách RL133H',
                    'title_en' => 'Moving Walkway Passenger Chain RL133H',
                    'slug_vi' => 'xich-bang-tai-hanh-khach-rl133h',
                    'slug_en' => 'moving-walkway-passenger-chain-rl133h',
                    'categories' => ['xich-bang-tai-hanh-khach'],
                    'is_featured' => 0,
                    'desc_vi' => 'RL133H có bước xích 133.33 mm, con lăn Ø80 mm, chốt Ø24 mm và lực kéo đứt tối thiểu 220 kN. Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, dùng cho băng tải người đi tại các khu vực chủ yếu nằm ngang hoặc có độ dốc thoải như sân bay lớn và siêu thị. Xích giúp băng tải vận hành êm, đồng bộ và an toàn khi chịu tải liên tục.',
                    'structure_vi' => 'Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, gồm các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo yêu cầu.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Dòng xích thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp', '/images/products/thanhkhang/xich-bang-tai-hanh-khach/rl133h-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '80'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '24'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '50'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '50'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '56.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '220'],
                    ],
                ],
                [
                    'sku' => 'RL135',
                    'display_code' => 'RL135',
                    'title_vi' => 'Xích băng tải hành khách RL135',
                    'title_en' => 'Moving Walkway Passenger Chain RL135',
                    'slug_vi' => 'xich-bang-tai-hanh-khach-rl135',
                    'slug_en' => 'moving-walkway-passenger-chain-rl135',
                    'categories' => ['xich-bang-tai-hanh-khach'],
                    'is_featured' => 1,
                    'desc_vi' => 'RL135 có bước xích 134.5 mm, con lăn Ø70 mm, chốt Ø14.63 mm và lực kéo đứt tối thiểu 130 kN (má xích chưa nhiệt luyện). Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, dùng cho băng tải người đi tại các khu vực chủ yếu nằm ngang hoặc có độ dốc thoải như sân bay lớn và siêu thị. Xích giúp băng tải vận hành êm, đồng bộ và an toàn khi chịu tải liên tục. Ngoài phiên bản tiêu chuẩn 130 kN (má xích chưa nhiệt luyện), mã RL135 còn có tùy chọn má xích nhiệt luyện đạt lực kéo đứt tối thiểu 180 kN.',
                    'structure_vi' => 'Xích băng tải hành khách là loại xích thang cuốn không có kết cấu bậc thang, gồm các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao. Má xích có thể không nhiệt luyện nếu đạt yêu cầu độ bền; khi nhiệt luyện, lực kéo đứt tối thiểu của xích tăng từ 130 kN lên 180 kN.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích dẫn động và đĩa xích hồi; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo yêu cầu.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Dòng xích thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bang-tai-hanh-khach/moving-walkway-chain.webp', '/images/products/thanhkhang/xich-bang-tai-hanh-khach/rl135-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '134.5'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '70'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '14.63'],
                        ['label' => 'Chiều cao má trong h1 (mm)', 'value' => '40'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '56.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '130 kN (chưa nhiệt luyện) / 180 kN (nhiệt luyện)'],
                    ],
                ],
                [
                    'sku' => 'TL133c',
                    'display_code' => 'TL133c',
                    'title_vi' => 'Xích không cần bảo trì TL133c',
                    'title_en' => 'Maintenance-Free Escalator Step Chain TL133c',
                    'slug_vi' => 'xich-khong-can-bao-tri-tl133c',
                    'slug_en' => 'maintenance-free-escalator-step-chain-tl133c',
                    'categories' => ['xich-khong-can-bao-tri'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133c (kiểu I) có bước xích 133.33 mm, con lăn Ø75 mm, chốt Ø13.85 mm và lực kéo đứt tối thiểu 180 kN. Xích bậc thang không cần bảo trì có đầy đủ đặc điểm của xích thang cuốn, đồng thời không cần hoặc rất ít cần bảo trì, phù hợp với thang cuốn làm việc trong môi trường yêu cầu độ sạch cao. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách bậc 400 mm.',
                    'structure_vi' => 'Xích bậc thang không cần bảo trì có cấu tạo như Xích bậc thang cuốn thông thường, nhưng cụm quay chốt – ống lót được thiết kế riêng với vật liệu không cần bảo trì để tự bôi trơn (ổ trục tự bôi trơn), nên xích không cần hoặc rất ít cần bôi trơn bổ sung. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót tự bôi trơn: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; vật liệu tự bôi trơn giúp giảm ma sát và mòn mà không cần tra dầu thường xuyên, phù hợp thang cuốn làm việc trong môi trường yêu cầu độ sạch cao.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo yêu cầu.
• Kiểu kết cấu: có hai kiểu là kiểu I và kiểu II; mỗi bậc thang tương ứng 3 mắt xích.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Dòng xích thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Cụm chốt – ống lót tự bôi trơn nên xích không cần hoặc rất ít cần bảo trì. Vẫn nên kiểm tra định kỳ độ giãn bước; thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích) và thay đồng thời xích hai bên trái và phải.',
                    'image' => '/images/products/thanhkhang/xich-khong-can-bao-tri/no-maintenance-escalator-step-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-khong-can-bao-tri/no-maintenance-escalator-step-chain.webp', '/images/products/thanhkhang/xich-khong-can-bao-tri/tl133c-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '75'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '13.85'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '55.4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '180'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '400'],
                        ['label' => 'Thông số bổ sung', 'value' => 'Kiểu (type) = I; bề dày má T/t = 5 mm'],
                    ],
                ],
                [
                    'sku' => 'TL133Hg',
                    'display_code' => 'TL133Hg',
                    'title_vi' => 'Xích không cần bảo trì TL133Hg',
                    'title_en' => 'Maintenance-Free Escalator Step Chain TL133Hg',
                    'slug_vi' => 'xich-khong-can-bao-tri-tl133hg',
                    'slug_en' => 'maintenance-free-escalator-step-chain-tl133hg',
                    'categories' => ['xich-khong-can-bao-tri'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hg (kiểu II) có bước xích 133.33 mm, con lăn Ø80 mm, chốt Ø22 mm và lực kéo đứt tối thiểu 230 kN. Xích bậc thang không cần bảo trì có đầy đủ đặc điểm của xích thang cuốn, đồng thời không cần hoặc rất ít cần bảo trì, phù hợp với thang cuốn làm việc trong môi trường yêu cầu độ sạch cao. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn; mỗi bậc thang tương ứng 3 mắt xích, khoảng cách bậc 400 mm.',
                    'structure_vi' => 'Xích bậc thang không cần bảo trì có cấu tạo như Xích bậc thang cuốn thông thường, nhưng cụm quay chốt – ống lót được thiết kế riêng với vật liệu không cần bảo trì để tự bôi trơn (ổ trục tự bôi trơn), nên xích không cần hoặc rất ít cần bôi trơn bổ sung. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao.
• Chốt và ống lót tự bôi trơn: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; vật liệu tự bôi trơn giúp giảm ma sát và mòn mà không cần tra dầu thường xuyên, phù hợp thang cuốn làm việc trong môi trường yêu cầu độ sạch cao.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép. Đường kính con lăn có thể thiết kế riêng theo yêu cầu.
• Kiểu kết cấu: có hai kiểu là kiểu I và kiểu II; mỗi bậc thang tương ứng 3 mắt xích.
• Vật liệu và xử lý nhiệt được chọn và tối ưu để xích đạt độ bền, chống mỏi, chống mài mòn và tuổi thọ cao.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Dòng xích thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Cụm chốt – ống lót tự bôi trơn nên xích không cần hoặc rất ít cần bảo trì. Vẫn nên kiểm tra định kỳ độ giãn bước; thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích) và thay đồng thời xích hai bên trái và phải.',
                    'image' => '/images/products/thanhkhang/xich-khong-can-bao-tri/no-maintenance-escalator-step-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-khong-can-bao-tri/no-maintenance-escalator-step-chain.webp', '/images/products/thanhkhang/xich-khong-can-bao-tri/tl133hg-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Đường kính con lăn d1* (mm)', 'value' => '80'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '27'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '22'],
                        ['label' => 'Chiều cao má ngoài h2 (mm)', 'value' => '60'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '58.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '230'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '400'],
                        ['label' => 'Thông số bổ sung', 'value' => 'Kiểu (type) = II; bề dày má T/t = 5/6 mm'],
                    ],
                ],
                [
                    'sku' => 'TL135Ha',
                    'display_code' => 'TL135Ha',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL135Ha',
                    'title_en' => 'Public Transport Escalator Step Chain TL135Ha',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl135ha',
                    'slug_en' => 'public-transport-escalator-step-chain-tl135ha',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 1,
                    'desc_vi' => 'TL135Ha có bước xích 135 mm, con lăn Ø54 mm, chốt Ø40 mm và lực kéo đứt tối thiểu 320 kN. Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl135ha-hd-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '320'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '405'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '54'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '31.3'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '61.6'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '75'],
                        ['label' => 'Bề dày má T/t (mm)', 'value' => '5'],
                        ['label' => 'd3 (mm)', 'value' => '100'],
                        ['label' => 'd4 (mm)', 'value' => '19'],
                        ['label' => 'd5 (mm)', 'value' => '6.2'],
                        ['label' => 'C1 (mm)', 'value' => '25'],
                        ['label' => 'C2 (mm)', 'value' => '43'],
                        ['label' => 'C3 (mm)', 'value' => '185.6'],
                    ],
                ],
                [
                    'sku' => 'TL135Hb',
                    'display_code' => 'TL135Hb',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL135Hb',
                    'title_en' => 'Public Transport Escalator Step Chain TL135Hb',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl135hb',
                    'slug_en' => 'public-transport-escalator-step-chain-tl135hb',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135Hb có bước xích 135 mm, con lăn Ø40 mm, chốt Ø26 mm và lực kéo đứt tối thiểu 205 kN. Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl135ha-hd-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '205'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '405'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '40'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '31.3'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '26'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '57.6'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '60'],
                        ['label' => 'Bề dày má T/t (mm)', 'value' => '4.5'],
                        ['label' => 'd3 (mm)', 'value' => '100'],
                        ['label' => 'd4 (mm)', 'value' => '19'],
                        ['label' => 'd5 (mm)', 'value' => '6.2'],
                        ['label' => 'C1 (mm)', 'value' => '25'],
                        ['label' => 'C2 (mm)', 'value' => '43'],
                        ['label' => 'C3 (mm)', 'value' => '185.6'],
                    ],
                ],
                [
                    'sku' => 'TL135Hc',
                    'display_code' => 'TL135Hc',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL135Hc',
                    'title_en' => 'Public Transport Escalator Step Chain TL135Hc',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl135hc',
                    'slug_en' => 'public-transport-escalator-step-chain-tl135hc',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135Hc có bước xích 135 mm, con lăn Ø54 mm, chốt Ø32.5 mm và lực kéo đứt tối thiểu 270 kN. Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl135ha-hd-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '270'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '405'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '54'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '31.3'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '32.5'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '60'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '75'],
                        ['label' => 'Bề dày má T/t (mm)', 'value' => '5'],
                        ['label' => 'd3 (mm)', 'value' => '100'],
                        ['label' => 'd4 (mm)', 'value' => '19'],
                        ['label' => 'd5 (mm)', 'value' => '6.2'],
                        ['label' => 'C1 (mm)', 'value' => '25'],
                        ['label' => 'C2 (mm)', 'value' => '43'],
                        ['label' => 'C3 (mm)', 'value' => '185.6'],
                    ],
                ],
                [
                    'sku' => 'TL135Hd',
                    'display_code' => 'TL135Hd',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL135Hd',
                    'title_en' => 'Public Transport Escalator Step Chain TL135Hd',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl135hd',
                    'slug_en' => 'public-transport-escalator-step-chain-tl135hd',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL135Hd có bước xích 135 mm, con lăn Ø54 mm, chốt Ø40.2 mm và lực kéo đứt tối thiểu 408 kN. Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl135ha-hd-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '135'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '408'],
                        ['label' => 'Số mắt xích / bậc', 'value' => '3'],
                        ['label' => 'Khoảng cách bậc (mm)', 'value' => '405'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '54'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '31.3'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '40.2'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '65.75'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '75'],
                        ['label' => 'Bề dày má T/t (mm)', 'value' => '7/5.5'],
                        ['label' => 'd3 (mm)', 'value' => '100'],
                        ['label' => 'd4 (mm)', 'value' => '19'],
                        ['label' => 'd5 (mm)', 'value' => '6.2'],
                        ['label' => 'C1 (mm)', 'value' => '25'],
                        ['label' => 'C2 (mm)', 'value' => '43'],
                        ['label' => 'C3 (mm)', 'value' => '185.6'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqb3',
                    'display_code' => 'TL133Hqb3',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqb3',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqb3',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqb3',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqb3',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqb3 có bước xích 133.33 mm và lực kéo đứt tối thiểu 200 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '200'],
                        ['label' => 'ØD (mm)', 'value' => '20'],
                        ['label' => 'ØD1* (mm)', 'value' => '39.37'],
                        ['label' => 'ØD2 (mm)', 'value' => '26'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '5'],
                        ['label' => 'T1 (mm)', 'value' => '5'],
                        ['label' => 'H (mm)', 'value' => '45'],
                        ['label' => 'H1 (mm)', 'value' => '50'],
                        ['label' => 'L1* (mm)', 'value' => '55'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqb16',
                    'display_code' => 'TL133Hqb16',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqb16',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqb16',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqb16',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqb16',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqb16 có bước xích 133.33 mm và lực kéo đứt tối thiểu 230 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '230'],
                        ['label' => 'ØD (mm)', 'value' => '22'],
                        ['label' => 'ØD1* (mm)', 'value' => '39.37'],
                        ['label' => 'ØD2 (mm)', 'value' => '26'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '5'],
                        ['label' => 'T1 (mm)', 'value' => '6'],
                        ['label' => 'H (mm)', 'value' => '50'],
                        ['label' => 'H1 (mm)', 'value' => '50'],
                        ['label' => 'L1* (mm)', 'value' => '55'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqc1b',
                    'display_code' => 'TL133Hqc1b',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqc1b',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqc1b',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqc1b',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqc1b',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqc1b có bước xích 133.33 mm và lực kéo đứt tối thiểu 260 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '260'],
                        ['label' => 'ØD (mm)', 'value' => '22'],
                        ['label' => 'ØD1* (mm)', 'value' => '39.37'],
                        ['label' => 'ØD2 (mm)', 'value' => '30'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '5'],
                        ['label' => 'T1 (mm)', 'value' => '6'],
                        ['label' => 'H (mm)', 'value' => '50'],
                        ['label' => 'H1 (mm)', 'value' => '50'],
                        ['label' => 'L1* (mm)', 'value' => '52.5'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqc1e',
                    'display_code' => 'TL133Hqc1e',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqc1e',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqc1e',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqc1e',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqc1e',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqc1e có bước xích 133.33 mm và lực kéo đứt tối thiểu 260 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '260'],
                        ['label' => 'ØD (mm)', 'value' => '22'],
                        ['label' => 'ØD1* (mm)', 'value' => '39.37'],
                        ['label' => 'ØD2 (mm)', 'value' => '30'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '5'],
                        ['label' => 'T1 (mm)', 'value' => '6'],
                        ['label' => 'H (mm)', 'value' => '50'],
                        ['label' => 'H1 (mm)', 'value' => '50'],
                        ['label' => 'L1* (mm)', 'value' => '55'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqc1d',
                    'display_code' => 'TL133Hqc1d',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqc1d',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqc1d',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqc1d',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqc1d',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqc1d có bước xích 133.33 mm và lực kéo đứt tối thiểu 260 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '260'],
                        ['label' => 'ØD (mm)', 'value' => '22'],
                        ['label' => 'ØD1* (mm)', 'value' => '39.37'],
                        ['label' => 'ØD2 (mm)', 'value' => '30'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '5'],
                        ['label' => 'T1 (mm)', 'value' => '6'],
                        ['label' => 'H (mm)', 'value' => '50'],
                        ['label' => 'H1 (mm)', 'value' => '50'],
                        ['label' => 'L1* (mm)', 'value' => '60'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqd9a',
                    'display_code' => 'TL133Hqd9a',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqd9a',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqd9a',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqd9a',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqd9a',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqd9a có bước xích 133.33 mm và lực kéo đứt tối thiểu 350 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '350'],
                        ['label' => 'ØD (mm)', 'value' => '35'],
                        ['label' => 'ØD1* (mm)', 'value' => '63.5'],
                        ['label' => 'ØD2 (mm)', 'value' => '43'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '6'],
                        ['label' => 'T1 (mm)', 'value' => '7'],
                        ['label' => 'H (mm)', 'value' => '70'],
                        ['label' => 'H1 (mm)', 'value' => '70'],
                        ['label' => 'L1* (mm)', 'value' => '52.5'],
                    ],
                ],
                [
                    'sku' => 'TL133Hqd6a',
                    'display_code' => 'TL133Hqd6a',
                    'title_vi' => 'Xích bậc thang cuốn công cộng TL133Hqd6a',
                    'title_en' => 'Public Transport Escalator Step Chain TL133Hqd6a',
                    'slug_vi' => 'xich-bac-thang-cuon-cong-cong-tl133hqd6a',
                    'slug_en' => 'public-transport-escalator-step-chain-tl133hqd6a',
                    'categories' => ['xich-bac-thang-cong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'TL133Hqd6a có bước xích 133.33 mm và lực kéo đứt tối thiểu 400 kN (tính cho một bên xích). Xích bậc thang cuốn công cộng dành cho thang cuốn yêu cầu độ bền cao và làm việc ở độ dốc lớn, chiều cao nâng có thể đạt 30 m, thậm chí 40 m. Xích nối và đồng bộ chuyển động của các bậc thang, giúp thang vận hành êm, an toàn dưới tải nặng và hoạt động liên tục. Loại xích này được dùng cho thang cuốn ở nhà ga lớn, sân bay, bến tàu, tàu điện ngầm, trung tâm thương mại lớn và các khu du lịch nổi tiếng.',
                    'structure_vi' => 'Xích bậc thang giao thông công cộng thuộc nhóm chịu tải nặng, dùng cho thang cuốn có độ dốc lớn và lưu lượng người cao. So với xích tiêu chuẩn, nhóm này dùng má xích dày hơn và chốt lớn hơn, được xử lý nhiệt đặc biệt để chống giãn dưới tải; ứng suất được phân bố trên diện tích lớn hơn nên giảm mỏi cục bộ. Các bộ phận chính:
• Má xích (má trong và má ngoài): nối các khớp thành vòng kín liên tục và chịu lực kéo khi vận hành; làm từ thép cường độ cao, dòng chịu tải nặng thường dùng thép hợp kim tôi cảm ứng.
• Chốt và ống lót: nối các mắt xích và cho phép xích gập khi đi qua đĩa xích; thường làm từ thép tôi cứng và được nhiệt luyện để chống mòn, giảm ma sát.
• Con lăn: giúp xích chạy êm trên ray dẫn và đĩa xích, giảm ma sát và mài mòn; thường làm từ nylon hoặc thép.
• Trục bậc thang và bánh đỡ: gắn trên xích, trục nối hai dây xích trái – phải để lắp bậc thang; kích thước trục và bánh được ghi ở các cột kích thước. Các kích thước có dấu * (ØD1, W, L1) có thể thiết kế riêng theo loại thang của khách.
• Kết cấu kín tốt hơn giúp giữ dầu bôi trơn và hạn chế bụi bẩn xâm nhập.
• Bộ phận phối hợp: đĩa xích ăn khớp với mắt xích để dẫn động xích và giữ các bậc thang chuyển động đồng bộ; ray dẫn hướng giữ xích chạy đúng quỹ đạo dọc kết cấu thang.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (mòn làm xích giãn bước); con lăn mòn ở mặt ngoài và chịu mỏi do tiếp xúc với ray.',
                    'standard_vi' => 'Xích bậc thang cuốn công cộng được thiết kế theo tiêu chuẩn JB/T8545 (quy định dung sai kích thước, lực kéo và chiều dài) và sản xuất trong hệ thống quản lý chất lượng ISO 9001. Các sản phẩm được kiểm tra lực kéo và độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Tuổi thọ thông thường 10–15 năm, có thể đạt 20 năm trở lên nếu bảo trì tốt. Cần kiểm tra bôi trơn và độ giãn bước định kỳ; nên thay xích khi độ giãn bước tích lũy đạt 2–3% bước danh nghĩa (đo trên đoạn 10 mắt xích). Luôn thay đồng thời xích hai bên trái và phải, không thay riêng từng mắt xích.',
                    'image' => '/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-bac-thang-cong-cong/public-transport-chain.webp', '/images/products/thanhkhang/xich-bac-thang-cong-cong/tl133hq-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Bước xích P (mm)', 'value' => '133.33'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '400'],
                        ['label' => 'ØD (mm)', 'value' => '35'],
                        ['label' => 'ØD1* (mm)', 'value' => '63.5'],
                        ['label' => 'ØD2 (mm)', 'value' => '43'],
                        ['label' => 'W* (mm)', 'value' => '34'],
                        ['label' => 'T (mm)', 'value' => '6'],
                        ['label' => 'T1 (mm)', 'value' => '6'],
                        ['label' => 'H (mm)', 'value' => '75'],
                        ['label' => 'H1 (mm)', 'value' => '75'],
                        ['label' => 'L1* (mm)', 'value' => '52.5'],
                    ],
                ],
                [
                    'sku' => '16A-2F',
                    'display_code' => '16A-2F',
                    'title_vi' => 'Xích dẫn động chính 16A-2F',
                    'title_en' => 'Escalator Main Drive Chain 16A-2F',
                    'slug_vi' => 'xich-dan-dong-chinh-16a-2f',
                    'slug_en' => 'escalator-main-drive-chain-16a-2f',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 1,
                    'desc_vi' => '16A-2F là xích dẫn động chính thang cuốn, hai hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 111.2 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 143 kN và khối lượng khoảng 5.15 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/16a-2f-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '62.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '67.1'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24.13'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '29.29'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.26'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '111.2'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '143'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.15'],
                    ],
                ],
                [
                    'sku' => '16B-2F',
                    'display_code' => '16B-2F',
                    'title_vi' => 'Xích dẫn động chính 16B-2F',
                    'title_en' => 'Escalator Main Drive Chain 16B-2F',
                    'slug_vi' => 'xich-dan-dong-chinh-16b-2f',
                    'slug_en' => 'escalator-main-drive-chain-16b-2f',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 1,
                    'desc_vi' => '16B-2F là xích dẫn động chính thang cuốn, hai hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 106 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 126.5 kN và khối lượng khoảng 5.42 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/16b-2f-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.02'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.28'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '68'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '73.4'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21.08'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '31.88'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.09/3.10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '106'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '126.5'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.42'],
                    ],
                ],
                [
                    'sku' => '20A-2F',
                    'display_code' => '20A-2F',
                    'title_vi' => 'Xích dẫn động chính 20A-2F',
                    'title_en' => 'Escalator Main Drive Chain 20A-2F',
                    'slug_vi' => 'xich-dan-dong-chinh-20a-2f',
                    'slug_en' => 'escalator-main-drive-chain-20a-2f',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20A-2F là xích dẫn động chính thang cuốn, hai hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 173.5 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 215.6 kN và khối lượng khoảng 7.8 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20a-2f-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '76.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '83.1'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30.18'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '35.76'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '173.5'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '215.6'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '7.8'],
                    ],
                ],
                [
                    'sku' => '20A-3',
                    'display_code' => '20A-3',
                    'title_vi' => 'Xích dẫn động chính 20A-3',
                    'title_en' => 'Escalator Main Drive Chain 20A-3',
                    'slug_vi' => 'xich-dan-dong-chinh-20a-3',
                    'slug_en' => 'escalator-main-drive-chain-20a-3',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20A-3 là xích dẫn động chính thang cuốn, ba hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 265.5 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 309.6 kN và khối lượng khoảng 11.77 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20a-3-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '3'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '112.2'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '116.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '35.76'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '265.5'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '309.6'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '11.77'],
                    ],
                ],
                [
                    'sku' => '20B-3',
                    'display_code' => '20B-3',
                    'title_vi' => 'Xích dẫn động chính 20B-3',
                    'title_en' => 'Escalator Main Drive Chain 20B-3',
                    'slug_vi' => 'xich-dan-dong-chinh-20b-3',
                    'slug_en' => 'escalator-main-drive-chain-20b-3',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20B-3 là xích dẫn động chính thang cuốn, ba hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 250 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 290 kN và khối lượng khoảng 10.82 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20b-3-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '3'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '19.56'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '10.19'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '114.2'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '117.9'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '26.4'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '36.45'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.60/3.60'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '250'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '290'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '10.82'],
                    ],
                ],
                [
                    'sku' => '20AFT-2',
                    'display_code' => '20AFT-2',
                    'title_vi' => 'Xích dẫn động chính 20AFT-2',
                    'title_en' => 'Escalator Main Drive Chain 20AFT-2',
                    'slug_vi' => 'xich-dan-dong-chinh-20aft-2',
                    'slug_en' => 'escalator-main-drive-chain-20aft-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20AFT-2 là xích dẫn động chính thang cuốn, hai hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 211.68 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 262.5 kN và khối lượng khoảng 8.09 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20aft-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '76.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '80.5'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30.1'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '35.76'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '211.68'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '262.5'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '8.09'],
                    ],
                ],
                [
                    'sku' => '20AFT-3',
                    'display_code' => '20AFT-3',
                    'title_vi' => 'Xích dẫn động chính 20AFT-3',
                    'title_en' => 'Escalator Main Drive Chain 20AFT-3',
                    'slug_vi' => 'xich-dan-dong-chinh-20aft-3',
                    'slug_en' => 'escalator-main-drive-chain-20aft-3',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20AFT-3 là xích dẫn động chính thang cuốn, ba hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 330 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 395 kN và khối lượng khoảng 12.1 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20aft-3-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '3'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '112.2'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '116.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30.1'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '35.76'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '330'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '395'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '12.1'],
                    ],
                ],
                [
                    'sku' => '20B-2F',
                    'display_code' => '20B-2F',
                    'title_vi' => 'Xích dẫn động chính 20B-2F',
                    'title_en' => 'Escalator Main Drive Chain 20B-2F',
                    'slug_vi' => 'xich-dan-dong-chinh-20b-2f',
                    'slug_en' => 'escalator-main-drive-chain-20b-2f',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20B-2F là xích dẫn động chính thang cuốn, hai hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 170 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 210 kN và khối lượng khoảng 7.2 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/20b-2f-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '19.58'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '10.19'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '79.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '85.8'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '26.42'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '36.45'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.60/3.60'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '170'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '210'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '7.2'],
                    ],
                ],
                [
                    'sku' => '24A-2',
                    'display_code' => '24A-2',
                    'title_vi' => 'Xích dẫn động chính 24A-2',
                    'title_en' => 'Escalator Main Drive Chain 24A-2',
                    'slug_vi' => 'xich-dan-dong-chinh-24a-2',
                    'slug_en' => 'escalator-main-drive-chain-24a-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '24A-2 là xích dẫn động chính thang cuốn, hai hàng, bước xích 38.1 mm, đường kính con lăn tối đa 22.23 mm và lực kéo đứt tối thiểu 254 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 314.9 kN và khối lượng khoảng 11.7 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/24a-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '38.1'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '22.23'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '25.22'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '11.1'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '95.8'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '99.7'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '35.7'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '45.44'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '254'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '314.9'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '11.7'],
                    ],
                ],
                [
                    'sku' => '24B-2',
                    'display_code' => '24B-2',
                    'title_vi' => 'Xích dẫn động chính 24B-2',
                    'title_en' => 'Escalator Main Drive Chain 24B-2',
                    'slug_vi' => 'xich-dan-dong-chinh-24b-2',
                    'slug_en' => 'escalator-main-drive-chain-24b-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '24B-2 là xích dẫn động chính thang cuốn, hai hàng, bước xích 38.1 mm, đường kính con lăn tối đa 25.4 mm và lực kéo đứt tối thiểu 280 kN. Xích dẫn động chính truyền công suất trong hệ dẫn động thang cuốn; cấu hình được lựa chọn theo tải và thiết kế của thiết bị. Xích có lực kéo đứt trung bình 319.2 kN và khối lượng khoảng 13.4 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/drive-chain.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/24b-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '38.1'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '25.4'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '14.63'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '101.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '106.2'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '33.2'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '48.36'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '5.80/4.80'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '280'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '319.2'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '13.4'],
                    ],
                ],
                [
                    'sku' => '16A-1',
                    'display_code' => '16A-1',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 16A-1',
                    'title_en' => 'Short-Pitch Precision Roller Chain 16A-1',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-16a-1',
                    'slug_en' => 'short-pitch-precision-roller-chain-16a-1',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '16A-1 là xích con lăn truyền động bước ngắn chính xác, một hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 55.60/56.70 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 71.5 kN và khối lượng khoảng 2.6 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-single.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-single.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-16a-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.94'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '35.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24.13'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.2'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '55.60/56.70'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '71.5'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.6'],
                    ],
                ],
                [
                    'sku' => '16A-2',
                    'display_code' => '16A-2',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 16A-2',
                    'title_en' => 'Short-Pitch Precision Roller Chain 16A-2',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-16a-2',
                    'slug_en' => 'short-pitch-precision-roller-chain-16a-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '16A-2 là xích con lăn truyền động bước ngắn chính xác, hai hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 111.20/113.40 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 143 kN và khối lượng khoảng 5.15 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double-upper.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-16a-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '64.89'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24.13'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '29.29'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.2'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '111.20/113.40'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '143'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.15'],
                    ],
                ],
                [
                    'sku' => '20A-1',
                    'display_code' => '20A-1',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 20A-1',
                    'title_en' => 'Short-Pitch Precision Roller Chain 20A-1',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-20a-1',
                    'slug_en' => 'short-pitch-precision-roller-chain-20a-1',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20A-1 là xích con lăn truyền động bước ngắn chính xác, một hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 87.00/88.50 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 109.2 kN và khối lượng khoảng 3.91 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-single.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-single.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-20a-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '42.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30.17'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '87.00/88.50'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '109.2'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '3.91'],
                    ],
                ],
                [
                    'sku' => '20A-2',
                    'display_code' => '20A-2',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 20A-2',
                    'title_en' => 'Short-Pitch Precision Roller Chain 20A-2',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-20a-2',
                    'slug_en' => 'short-pitch-precision-roller-chain-20a-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20A-2 là xích con lăn truyền động bước ngắn chính xác, hai hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 174.00/177.00 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 204 kN và khối lượng khoảng 7.8 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-a-double-upper.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-20a-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '78.36'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '30.17'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '35.76'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '174.00/177.00'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '204'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '7.8'],
                    ],
                ],
                [
                    'sku' => '16B-1',
                    'display_code' => '16B-1',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 16B-1',
                    'title_en' => 'Short-Pitch Precision Roller Chain 16B-1',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-16b-1',
                    'slug_en' => 'short-pitch-precision-roller-chain-16b-1',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '16B-1 là xích con lăn truyền động bước ngắn chính xác, một hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 60 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 71 kN và khối lượng khoảng 2.71 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-2.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-16b-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.02'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.28'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '36.1'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '41.5'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21.08'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.09/3.10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '60'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '71'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.71'],
                    ],
                ],
                [
                    'sku' => '16B-2',
                    'display_code' => '16B-2',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 16B-2',
                    'title_en' => 'Short-Pitch Precision Roller Chain 16B-2',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-16b-2',
                    'slug_en' => 'short-pitch-precision-roller-chain-16b-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '16B-2 là xích con lăn truyền động bước ngắn chính xác, hai hàng, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm và lực kéo đứt tối thiểu 106 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 126.5 kN và khối lượng khoảng 5.42 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-1.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-2.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-16b-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.02'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.28'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '68'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '73.4'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21.08'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '31.88'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.09/3.10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '106'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '126.5'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.42'],
                    ],
                ],
                [
                    'sku' => '20B-1',
                    'display_code' => '20B-1',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 20B-1',
                    'title_en' => 'Short-Pitch Precision Roller Chain 20B-1',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-20b-1',
                    'slug_en' => 'short-pitch-precision-roller-chain-20b-1',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20B-1 là xích con lăn truyền động bước ngắn chính xác, một hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 95 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 101.8 kN và khối lượng khoảng 3.7 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-2.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-20b-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '19.58'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '10.19'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '42.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '47.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '26.42'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.60/3.60'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '95'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '101.8'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '3.7'],
                    ],
                ],
                [
                    'sku' => '20B-2',
                    'display_code' => '20B-2',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 20B-2',
                    'title_en' => 'Short-Pitch Precision Roller Chain 20B-2',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-20b-2',
                    'slug_en' => 'short-pitch-precision-roller-chain-20b-2',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '20B-2 là xích con lăn truyền động bước ngắn chính xác, hai hàng, bước xích 31.75 mm, đường kính con lăn tối đa 19.05 mm và lực kéo đứt tối thiểu 170 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 210 kN và khối lượng khoảng 7.2 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-1.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-double-2.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-20b-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '19.58'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '10.19'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '79.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '83.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '26.42'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '36.45'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.60/3.60'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '170'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '210'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '7.2'],
                    ],
                ],
                [
                    'sku' => '40B-1',
                    'display_code' => '40B-1',
                    'title_vi' => 'Xích con lăn bước ngắn chính xác 40B-1',
                    'title_en' => 'Short-Pitch Precision Roller Chain 40B-1',
                    'slug_vi' => 'xich-con-lan-buoc-ngan-chinh-xac-40b-1',
                    'slug_en' => 'short-pitch-precision-roller-chain-40b-1',
                    'categories' => ['xich-dan-dong-chinh'],
                    'is_featured' => 0,
                    'desc_vi' => '40B-1 là xích con lăn truyền động bước ngắn chính xác, một hàng, bước xích 63.5 mm, đường kính con lăn tối đa 39.37 mm và lực kéo đứt tối thiểu 355 kN. Xích con lăn truyền công suất giữa các trục qua sự ăn khớp giữa xích và đĩa xích; dòng A và dòng B có cấu hình tương ứng với hệ đĩa xích. Xích có lực kéo đứt trung bình 392 kN và khối lượng khoảng 16.35 kg/m.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài và má trong: tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt: xuyên qua ống lót, nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót: nằm giữa chốt và con lăn, chịu ma sát tại khớp xích.
• Con lăn: ăn khớp với răng đĩa xích, giúp giảm ma sát khi chuyển động.
• Phụ kiện: mắt nối dùng để nối hai đầu xích; mắt chuyển tiếp dùng khi cần số mắt lẻ.
• Vật liệu phổ biến gồm thép carbon và thép hợp kim. Chốt, ống lót và con lăn được nhiệt luyện; má xích có thể không nhiệt luyện nếu đáp ứng yêu cầu độ bền. Các vị trí cần theo dõi hao mòn gồm chốt, ống lót và bề mặt con lăn.',
                    'standard_vi' => 'Xích truyền động chính thang cuốn được thiết kế theo tiêu chuẩn ISO 606, với kích thước lắp lẫn tương đương các dòng xích con lăn A và B. Xích con lăn bước ngắn chính xác thuộc các hệ tiêu chuẩn ISO, DIN và ASME/ANSI.',
                    'maintenance_vi' => 'Bôi trơn đều bằng dầu phù hợp để giảm ma sát tại chốt và ống lót; duy trì độ căng và căn chỉnh đĩa xích. Vệ sinh xích, kiểm tra độ mòn và độ giãn bước định kỳ. Khi đo, chọn đoạn xích thẳng và đo khoảng cách qua nhiều chốt. Thay xích khi độ giãn vượt khoảng 1,5% hoặc khi phát hiện hư hỏng; kiểm tra tình trạng đĩa xích cùng lúc.',
                    'image' => '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-1.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-b-single-2.webp', '/images/products/thanhkhang/xich-dan-dong-chinh/spprc-40b-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '63.5'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '39.37'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '38.1'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '22.89'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '82.6'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '91.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '52.96'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '8.50/7.50'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '355'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '392'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '16.35'],
                    ],
                ],
                [
                    'sku' => 'HR-16B-1',
                    'display_code' => '16B-1',
                    'title_vi' => 'Xích tay vịn 16B-1',
                    'title_en' => 'Handrail Driving Chain 16B-1',
                    'slug_vi' => 'xich-tay-vin-16b-1',
                    'slug_en' => 'handrail-driving-chain-16b-1',
                    'categories' => ['xich-tay-vin'],
                    'is_featured' => 0,
                    'desc_vi' => '16B-1 là xích dẫn động tay vịn thang cuốn, một dãy, bước xích 25.40 mm, đường kính con lăn tối đa 15.88 mm, đường kính chốt tối đa 8.28 mm, lực kéo đứt tối thiểu 60 kN và khối lượng khoảng 2.71 kg/m. Xích dẫn động tay vịn truyền chuyển động đến tay vịn (băng tay vịn) của thang cuốn, giữ cho tay vịn chạy đồng bộ với chuyển động của bậc thang, êm và nhịp ndãy. Cùng với xích dẫn động chính, đây là một trong hai nhóm xích truyền động của thang cuốn. Xích có kích thước lắp lẫn với xích con lăn tiêu chuẩn dòng A và dòng B, được chọn vật liệu và tối ưu quy trình xử lý nhiệt để đạt độ bền cao, chịu mỏi và chịu mài mòn tốt, tuổi thọ dài. Khi chọn xích, dùng xích tay vịn cho hệ tay vịn và xích bậc thang cho bậc thang.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài (pin link plate) và má trong (roller link plate): tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt (pin): xuyên qua ống lót, nối mắt xích trong với mắt xích ngoài và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót (bush): lồng giữa chốt và con lăn, là chi tiết chịu ma sát với chốt ở mỗi khớp xích.
• Con lăn (roller): ăn khớp với răng đĩa xích, giúp giảm ma sát và mài mòn khi xích chuyển động.
• Mắt nối (connecting link): dùng để nối hai đầu xích; với bước xích từ 25.4 mm trở lên nên dùng loại có hai chốt chẻ.
• Mắt chuyển tiếp (offset link): dùng khi cần số mắt xích lẻ, không dùng ở điều kiện tải nặng và tốc độ cao.
• Vật liệu thường dùng của hãng cho xích con lăn: má xích dùng thép 45, 40Mn, 45Mn, 40Cr, 42CrMo, 35CrMn, 50CrMo, 50CrV hoặc thép không gỉ; ống lót dùng thép carbon thấp (10, 20, 20Mn), thép 35 hoặc 40Cr cho xích chịu tải nặng; chốt ưu tiên thép 55CrSi.
• Xử lý nhiệt: chốt, ống lót và con lăn đều được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Xích truyền động thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn ISO 606, kích thước lắp lẫn tương đương xích con lăn tiêu chuẩn dòng A và dòng B (mã xích theo hệ ISO/DIN). Hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Chưa xác định tuổi thọ và chu kỳ thay thế riêng cho xích tay vịn. Nên bôi trơn đều để giảm ma sát và ăn mòn, kiểm tra độ mòn và độ giãn bước, đồng thời làm sạch bụi bẩn bám trên xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, dùng thước cặp đo từ tâm chốt này đến tâm chốt kế tiếp và so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-tay-vin/handrail-16b-1-sheet-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-tay-vin/handrail-16b-1-sheet-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.02'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.28'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '36.1'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '37.4'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.15/3.10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '60'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '72.8'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.71'],
                    ],
                ],
                [
                    'sku' => 'HR-16B-2',
                    'display_code' => '16B-2',
                    'title_vi' => 'Xích tay vịn 16B-2',
                    'title_en' => 'Handrail Driving Chain 16B-2',
                    'slug_vi' => 'xich-tay-vin-16b-2',
                    'slug_en' => 'handrail-driving-chain-16b-2',
                    'categories' => ['xich-tay-vin'],
                    'is_featured' => 0,
                    'desc_vi' => '16B-2 là xích dẫn động tay vịn thang cuốn, hai dãy, bước xích 25.40 mm, đường kính con lăn tối đa 15.88 mm, đường kính chốt tối đa 8.28 mm, lực kéo đứt tối thiểu 106 kN và khối lượng khoảng 5.42 kg/m. Xích dẫn động tay vịn truyền chuyển động đến tay vịn (băng tay vịn) của thang cuốn, giữ cho tay vịn chạy đồng bộ với chuyển động của bậc thang, êm và nhịp ndãy. Cùng với xích dẫn động chính, đây là một trong hai nhóm xích truyền động của thang cuốn. Xích có kích thước lắp lẫn với xích con lăn tiêu chuẩn dòng A và dòng B, được chọn vật liệu và tối ưu quy trình xử lý nhiệt để đạt độ bền cao, chịu mỏi và chịu mài mòn tốt, tuổi thọ dài. Khi chọn xích, dùng xích tay vịn cho hệ tay vịn và xích bậc thang cho bậc thang.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài (pin link plate) và má trong (roller link plate): tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt (pin): xuyên qua ống lót, nối mắt xích trong với mắt xích ngoài và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót (bush): lồng giữa chốt và con lăn, là chi tiết chịu ma sát với chốt ở mỗi khớp xích.
• Con lăn (roller): ăn khớp với răng đĩa xích, giúp giảm ma sát và mài mòn khi xích chuyển động.
• Mắt nối (connecting link): dùng để nối hai đầu xích; với bước xích từ 25.4 mm trở lên nên dùng loại có hai chốt chẻ.
• Mắt chuyển tiếp (offset link): dùng khi cần số mắt xích lẻ, không dùng ở điều kiện tải nặng và tốc độ cao.
• Vật liệu thường dùng của hãng cho xích con lăn: má xích dùng thép 45, 40Mn, 45Mn, 40Cr, 42CrMo, 35CrMn, 50CrMo, 50CrV hoặc thép không gỉ; ống lót dùng thép carbon thấp (10, 20, 20Mn), thép 35 hoặc 40Cr cho xích chịu tải nặng; chốt ưu tiên thép 55CrSi.
• Xử lý nhiệt: chốt, ống lót và con lăn đều được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Xích truyền động thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn ISO 606, kích thước lắp lẫn tương đương xích con lăn tiêu chuẩn dòng A và dòng B (mã xích theo hệ ISO/DIN). Hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Chưa xác định tuổi thọ và chu kỳ thay thế riêng cho xích tay vịn. Nên bôi trơn đều để giảm ma sát và ăn mòn, kiểm tra độ mòn và độ giãn bước, đồng thời làm sạch bụi bẩn bám trên xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, dùng thước cặp đo từ tâm chốt này đến tâm chốt kế tiếp và so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-tay-vin/handrail-16b-2-sheet-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-tay-vin/handrail-16b-2-sheet-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.02'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.28'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '68'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '69.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '31.88'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '4.15/3.10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '106'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '133'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.42'],
                    ],
                ],
                [
                    'sku' => 'HR-16A-2',
                    'display_code' => '16A-2',
                    'title_vi' => 'Xích tay vịn 16A-2',
                    'title_en' => 'Handrail Driving Chain 16A-2',
                    'slug_vi' => 'xich-tay-vin-16a-2',
                    'slug_en' => 'handrail-driving-chain-16a-2',
                    'categories' => ['xich-tay-vin'],
                    'is_featured' => 0,
                    'desc_vi' => '16A-2 là xích dẫn động tay vịn thang cuốn, hai dãy, bước xích 25.40 mm, đường kính con lăn tối đa 15.88 mm, đường kính chốt tối đa 7.92 mm, lực kéo đứt tối thiểu 113.4 kN và khối lượng khoảng 5.15 kg/m. Xích dẫn động tay vịn truyền chuyển động đến tay vịn (băng tay vịn) của thang cuốn, giữ cho tay vịn chạy đồng bộ với chuyển động của bậc thang, êm và nhịp ndãy. Cùng với xích dẫn động chính, đây là một trong hai nhóm xích truyền động của thang cuốn. Xích có kích thước lắp lẫn với xích con lăn tiêu chuẩn dòng A và dòng B, được chọn vật liệu và tối ưu quy trình xử lý nhiệt để đạt độ bền cao, chịu mỏi và chịu mài mòn tốt, tuổi thọ dài. Khi chọn xích, dùng xích tay vịn cho hệ tay vịn và xích bậc thang cho bậc thang.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài (pin link plate) và má trong (roller link plate): tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt (pin): xuyên qua ống lót, nối mắt xích trong với mắt xích ngoài và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót (bush): lồng giữa chốt và con lăn, là chi tiết chịu ma sát với chốt ở mỗi khớp xích.
• Con lăn (roller): ăn khớp với răng đĩa xích, giúp giảm ma sát và mài mòn khi xích chuyển động.
• Mắt nối (connecting link): dùng để nối hai đầu xích; với bước xích từ 25.4 mm trở lên nên dùng loại có hai chốt chẻ.
• Mắt chuyển tiếp (offset link): dùng khi cần số mắt xích lẻ, không dùng ở điều kiện tải nặng và tốc độ cao.
• Vật liệu thường dùng của hãng cho xích con lăn: má xích dùng thép 45, 40Mn, 45Mn, 40Cr, 42CrMo, 35CrMn, 50CrMo, 50CrV hoặc thép không gỉ; ống lót dùng thép carbon thấp (10, 20, 20Mn), thép 35 hoặc 40Cr cho xích chịu tải nặng; chốt ưu tiên thép 55CrSi.
• Xử lý nhiệt: chốt, ống lót và con lăn đều được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Xích truyền động thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn ISO 606, kích thước lắp lẫn tương đương xích con lăn tiêu chuẩn dòng A và dòng B (mã xích theo hệ ISO/DIN). Hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Chưa xác định tuổi thọ và chu kỳ thay thế riêng cho xích tay vịn. Nên bôi trơn đều để giảm ma sát và ăn mòn, kiểm tra độ mòn và độ giãn bước, đồng thời làm sạch bụi bẩn bám trên xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, dùng thước cặp đo từ tâm chốt này đến tâm chốt kế tiếp và so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-tay-vin/handrail-16a-2-sheet-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-tay-vin/handrail-16a-2-sheet-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '62.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '64.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '29.29'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.25'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '113.4'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '141.8'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.15'],
                    ],
                ],
                [
                    'sku' => 'HR-16AFT-2',
                    'display_code' => '16AFT-2',
                    'title_vi' => 'Xích tay vịn 16AFT-2',
                    'title_en' => 'Handrail Driving Chain 16AFT-2',
                    'slug_vi' => 'xich-tay-vin-16aft-2',
                    'slug_en' => 'handrail-driving-chain-16aft-2',
                    'categories' => ['xich-tay-vin'],
                    'is_featured' => 0,
                    'desc_vi' => '16AFT-2 là xích dẫn động tay vịn thang cuốn, hai dãy, bước xích 25.40 mm, đường kính con lăn tối đa 15.88 mm, đường kính chốt tối đa 7.92 mm, lực kéo đứt tối thiểu 132.3 kN và khối lượng khoảng 5.51 kg/m. Xích dẫn động tay vịn truyền chuyển động đến tay vịn (băng tay vịn) của thang cuốn, giữ cho tay vịn chạy đồng bộ với chuyển động của bậc thang, êm và nhịp ndãy. Cùng với xích dẫn động chính, đây là một trong hai nhóm xích truyền động của thang cuốn. Xích có kích thước lắp lẫn với xích con lăn tiêu chuẩn dòng A và dòng B, được chọn vật liệu và tối ưu quy trình xử lý nhiệt để đạt độ bền cao, chịu mỏi và chịu mài mòn tốt, tuổi thọ dài. Khi chọn xích, dùng xích tay vịn cho hệ tay vịn và xích bậc thang cho bậc thang.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má ngoài (pin link plate) và má trong (roller link plate): tạo thành thân xích và chịu lực kéo khi vận hành.
• Chốt (pin): xuyên qua ống lót, nối mắt xích trong với mắt xích ngoài và cho phép xích gập khi ăn khớp với đĩa xích.
• Ống lót (bush): lồng giữa chốt và con lăn, là chi tiết chịu ma sát với chốt ở mỗi khớp xích.
• Con lăn (roller): ăn khớp với răng đĩa xích, giúp giảm ma sát và mài mòn khi xích chuyển động.
• Mắt nối (connecting link): dùng để nối hai đầu xích; với bước xích từ 25.4 mm trở lên nên dùng loại có hai chốt chẻ.
• Mắt chuyển tiếp (offset link): dùng khi cần số mắt xích lẻ, không dùng ở điều kiện tải nặng và tốc độ cao.
• Vật liệu thường dùng của hãng cho xích con lăn: má xích dùng thép 45, 40Mn, 45Mn, 40Cr, 42CrMo, 35CrMn, 50CrMo, 50CrV hoặc thép không gỉ; ống lót dùng thép carbon thấp (10, 20, 20Mn), thép 35 hoặc 40Cr cho xích chịu tải nặng; chốt ưu tiên thép 55CrSi.
• Xử lý nhiệt: chốt, ống lót và con lăn đều được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Xích truyền động thang cuốn của Universal Chains được thiết kế theo tiêu chuẩn ISO 606, kích thước lắp lẫn tương đương xích con lăn tiêu chuẩn dòng A và dòng B (mã xích theo hệ ISO/DIN). Hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Chưa xác định tuổi thọ và chu kỳ thay thế riêng cho xích tay vịn. Nên bôi trơn đều để giảm ma sát và ăn mòn, kiểm tra độ mòn và độ giãn bước, đồng thời làm sạch bụi bẩn bám trên xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, dùng thước cặp đo từ tâm chốt này đến tâm chốt kế tiếp và so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-tay-vin/handrail-16aft-2-sheet-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-tay-vin/handrail-16aft-2-sheet-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '62.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '65.8'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24'],
                        ['label' => 'Bước ngang Pt (mm)', 'value' => '29.29'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.25'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '132.3'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '165.4'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.51'],
                    ],
                ],
                [
                    'sku' => 'LT40-1',
                    'display_code' => 'LT40-1',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT40-1',
                    'title_en' => 'Multi-Level Car Parking Chain LT40-1',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt40-1',
                    'slug_en' => 'multi-level-car-parking-chain-lt40-1',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 1,
                    'desc_vi' => 'LT40-1 là xích có bu lông điều chỉnh một dãy, bước xích 12.70 mm, đường kính chốt tối đa 3.96 mm, cỡ bu lông M10, lực kéo đứt tối thiểu 16 kN và khối lượng khoảng 0.62 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt40-1-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt40-1-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt40-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '7.95'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '7.85'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.96'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '16.55'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '18.2'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '12.07'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M10'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '16'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '18.4'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.62'],
                    ],
                ],
                [
                    'sku' => 'LT80-1',
                    'display_code' => 'LT80-1',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT80-1',
                    'title_en' => 'Multi-Level Car Parking Chain LT80-1',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt80-1',
                    'slug_en' => 'multi-level-car-parking-chain-lt80-1',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 0,
                    'desc_vi' => 'LT80-1 là xích có bu lông điều chỉnh một dãy, bước xích 25.40 mm, đường kính chốt tối đa 7.92 mm, cỡ bu lông M16/M20, lực kéo đứt tối thiểu 71.55 kN và khối lượng khoảng 2.60 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt80-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '32.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '36.5'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '24'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M16/M20'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '71.55'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '81.8'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.6'],
                    ],
                ],
                [
                    'sku' => 'LT80-2',
                    'display_code' => 'LT80-2',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT80-2',
                    'title_en' => 'Multi-Level Car Parking Chain LT80-2',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt80-2',
                    'slug_en' => 'multi-level-car-parking-chain-lt80-2',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 0,
                    'desc_vi' => 'LT80-2 là xích có bu lông điều chỉnh hai dãy, bước xích 25.40 mm, đường kính chốt tối đa 7.92 mm, cỡ bu lông M20, lực kéo đứt tối thiểu 143.1 kN và khối lượng khoảng 5.15 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt80-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '62.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '65.8'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '24'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M20'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '143.1'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '163.6'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.15'],
                    ],
                ],
                [
                    'sku' => 'LT100-1',
                    'display_code' => 'LT100-1',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT100-1',
                    'title_en' => 'Multi-Level Car Parking Chain LT100-1',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt100-1',
                    'slug_en' => 'multi-level-car-parking-chain-lt100-1',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 1,
                    'desc_vi' => 'LT100-1 là xích có bu lông điều chỉnh một dãy, bước xích 31.75 mm, đường kính chốt tối đa 9.53 mm, cỡ bu lông M24, lực kéo đứt tối thiểu 107.85 kN và khối lượng khoảng 3.91 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt100-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '40.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '44.7'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '30'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M24'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '107.85'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '120'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '3.91'],
                    ],
                ],
                [
                    'sku' => 'LT100-2',
                    'display_code' => 'LT100-2',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT100-2',
                    'title_en' => 'Multi-Level Car Parking Chain LT100-2',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt100-2',
                    'slug_en' => 'multi-level-car-parking-chain-lt100-2',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 0,
                    'desc_vi' => 'LT100-2 là xích có bu lông điều chỉnh hai dãy, bước xích 31.75 mm, đường kính chốt tối đa 9.53 mm, cỡ bu lông M24, lực kéo đứt tối thiểu 215.7 kN và khối lượng khoảng 7.80 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt100-2-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '2'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '76.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '80.5'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '30'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M24'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '215.7'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '260'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '7.8'],
                    ],
                ],
                [
                    'sku' => 'LT100-3',
                    'display_code' => 'LT100-3',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT100-3',
                    'title_en' => 'Multi-Level Car Parking Chain LT100-3',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt100-3',
                    'slug_en' => 'multi-level-car-parking-chain-lt100-3',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 0,
                    'desc_vi' => 'LT100-3 là xích có bu lông điều chỉnh ba hàng, bước xích 31.75 mm, đường kính chốt tối đa 9.53 mm, cỡ bu lông M24, lực kéo đứt tối thiểu 323.55 kN và khối lượng khoảng 11.77 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt100-3-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '3'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '18.9'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '112.2'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '116.3'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '30'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M24'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '323.55'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '345'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '11.77'],
                    ],
                ],
                [
                    'sku' => 'LT120-1',
                    'display_code' => 'LT120-1',
                    'title_vi' => 'Xích bãi đỗ xe lập thể LT120-1',
                    'title_en' => 'Multi-Level Car Parking Chain LT120-1',
                    'slug_vi' => 'xich-bai-do-xe-lap-the-lt120-1',
                    'slug_en' => 'multi-level-car-parking-chain-lt120-1',
                    'categories' => ['xich-bai-do-xe-lap-the'],
                    'is_featured' => 0,
                    'desc_vi' => 'LT120-1 là xích có bu lông điều chỉnh một dãy, bước xích 38.10 mm, đường kính chốt tối đa 11.10 mm, cỡ bu lông M30, lực kéo đứt tối thiểu 154.5 kN và khối lượng khoảng 5.62 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/lt80-120-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/lt120-1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '38.1'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '22.23'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '25.22'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '11.1'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '50.3'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '54.3'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '35.7'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M30'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '154.5'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '176.6'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '5.62'],
                    ],
                ],
                [
                    'sku' => 'LTAL666',
                    'display_code' => 'LTAL666',
                    'title_vi' => 'Xích bãi đỗ xe tuần hoàn đứng LTAL666',
                    'title_en' => 'Vertical Circulation Parking Chain LTAL666',
                    'slug_vi' => 'xich-bai-do-xe-tuan-hoan-dung-ltal666',
                    'slug_en' => 'vertical-circulation-parking-chain-ltal666',
                    'categories' => ['xich-bai-do-xe-tuan-hoan-dung'],
                    'is_featured' => 1,
                    'desc_vi' => 'LTAL666 là xích có bu lông điều chỉnh một dãy, bước xích 19.05 mm, đường kính chốt tối đa 5.94 mm, cỡ bu lông M20, lực kéo đứt tối thiểu 114 kN và khối lượng khoảng 2.43 kg/m. Xích có bu lông điều chỉnh là dòng xích con lăn bước ngắn có kết cấu cho phép lắp bu lông điều chỉnh vào mắt xích, dùng trong các hệ thống nâng hạ và tuần hoàn của thiết bị đỗ xe lập thể, đặc biệt là bãi đỗ xe tuần hoàn đứng. Loại thiết bị này yêu cầu độ tin cậy và an toàn rất cao nên xích cần có độ bền cao, chống mài mòn tốt, độ chính xác cao và chịu va đập tốt. Bu lông điều chỉnh giúp cân chỉnh vị trí và độ căng khi lắp đặt, đồng thời là điểm liên kết với khay hoặc giá đỡ xe.',
                    'structure_vi' => 'Xích con lăn gồm các mắt xích trong và mắt xích ngoài nối xen kẽ thành vòng liên tục. Các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành; làm từ thép hợp kim cường độ cao.
• Chốt: nối mắt xích trong với mắt xích ngoài, cho phép xích gập khi ăn khớp với đĩa xích; chốt dài hơn xích thông thường ở các mã nhiều hàng.
• Ống lót: lồng giữa chốt và con lăn, chịu ma sát với chốt ở mỗi khớp.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Bu lông điều chỉnh: lắp trên má xích theo cỡ ren M ghi ở cột thông số, dùng để cố định và căn chỉnh chi tiết mang tải.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng mã.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chốt, chiều rộng trong) theo hệ xích con lăn bước ngắn dòng A. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001 và kiểm tra lực kéo, độ cứng để xác nhận chất lượng xử lý nhiệt.',
                    'maintenance_vi' => 'Bôi trơn đều để giảm ma sát và ăn mòn; kiểm tra định kỳ độ giãn bước, độ mòn khớp và độ siết của bu lông điều chỉnh. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Với thiết bị nâng hạ, nên thay toàn bộ vòng xích cùng lúc khi độ mòn vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-bai-do-xe/ltal666-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-bai-do-xe/ltal666-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/vertical-circulation-photo.webp', '/images/products/thanhkhang/xich-bai-do-xe/ltal666-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.94'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '32.69'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '35.1'],
                        ['label' => 'Chiều cao má h2 max (mm)', 'value' => '15.62'],
                        ['label' => 'Cỡ bu lông M nom (mm)', 'value' => 'M20'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '114'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '131'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.43'],
                    ],
                ],
                [
                    'sku' => '40-SR',
                    'display_code' => '40-SR',
                    'title_vi' => 'Xích con lăn cạnh 40-SR',
                    'title_en' => 'Side Roller Conveyor Chain 40-SR',
                    'slug_vi' => 'xich-con-lan-canh-40-sr',
                    'slug_en' => 'side-roller-conveyor-chain-40-sr',
                    'categories' => ['xich-con-lan-canh'],
                    'is_featured' => 1,
                    'desc_vi' => '40SR là Xích con lăn cạnh, một dãy, bước xích 12.7 mm, đường kính con lăn trong tối đa 7.95 mm, đường kính chốt 3.96 mm, đường kính con lăn ngoài 15.88 mm. Xích con lăn cạnh là xích con lăn bước ngắn có thêm con lăn ngoài gắn ở hai đầu chốt. Trong quá trình vận chuyển, các con lăn bên dẫn hướng cho xích, chịu tải và lăn tiếp xúc với ray dẫn tương ứng, nhờ đó xích chạy ổn định, giảm ma sát trượt và mài mòn. Dòng xích này thường dùng trong các băng tải đường cong và hệ thống băng tải cần dẫn hướng xích bằng ray. Con lăn ngoài có thể làm bằng nhựa kỹ thuật theo yêu cầu của khách hàng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt: nối các mắt xích, cho phép xích gập khi ăn khớp với đĩa xích; chốt được kéo dài về hai phía (các kích thước L1, L2, L3) để lắp con lăn ngoài.
• Ống lót và con lăn trong: lồng quanh chốt, ăn khớp với răng đĩa xích và giảm ma sát.
• Con lăn ngoài (bên): lắp ở đầu chốt, lăn trên ray dẫn để dẫn hướng và đỡ tải; kích thước theo cột Ds và Hs; có thể làm bằng nhựa kỹ thuật theo yêu cầu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở mặt lăn của con lăn ngoài.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', '/images/products/thanhkhang/xich-con-lan-canh/40-sr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '40'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '7.95'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '7.85'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '1.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '12.07'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '3.96'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '8.3'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '17.7'],
                        ['label' => 'Chiều dài chốt L3 (mm)', 'value' => '19.9'],
                        ['label' => 'Đường kính con lăn ngoài Ds (SR) (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều cao con lăn ngoài Hs (SR) (mm)', 'value' => '7.8'],
                    ],
                ],
                [
                    'sku' => '50-SR',
                    'display_code' => '50-SR',
                    'title_vi' => 'Xích con lăn cạnh 50-SR',
                    'title_en' => 'Side Roller Conveyor Chain 50-SR',
                    'slug_vi' => 'xich-con-lan-canh-50-sr',
                    'slug_en' => 'side-roller-conveyor-chain-50-sr',
                    'categories' => ['xich-con-lan-canh'],
                    'is_featured' => 0,
                    'desc_vi' => '50SR là Xích con lăn cạnh, một dãy, bước xích 15.875 mm, đường kính con lăn trong tối đa 10.16 mm, đường kính chốt 5.08 mm, đường kính con lăn ngoài 19.05 mm. Xích con lăn cạnh là xích con lăn bước ngắn có thêm con lăn ngoài gắn ở hai đầu chốt. Trong quá trình vận chuyển, các con lăn bên dẫn hướng cho xích, chịu tải và lăn tiếp xúc với ray dẫn tương ứng, nhờ đó xích chạy ổn định, giảm ma sát trượt và mài mòn. Dòng xích này thường dùng trong các băng tải đường cong và hệ thống băng tải cần dẫn hướng xích bằng ray. Con lăn ngoài có thể làm bằng nhựa kỹ thuật theo yêu cầu của khách hàng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt: nối các mắt xích, cho phép xích gập khi ăn khớp với đĩa xích; chốt được kéo dài về hai phía (các kích thước L1, L2, L3) để lắp con lăn ngoài.
• Ống lót và con lăn trong: lồng quanh chốt, ăn khớp với răng đĩa xích và giảm ma sát.
• Con lăn ngoài (bên): lắp ở đầu chốt, lăn trên ray dẫn để dẫn hướng và đỡ tải; kích thước theo cột Ds và Hs; có thể làm bằng nhựa kỹ thuật theo yêu cầu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở mặt lăn của con lăn ngoài.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', '/images/products/thanhkhang/xich-con-lan-canh/50-sr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '50'],
                        ['label' => 'Bước xích P (mm)', 'value' => '15.875'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '9.43'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '2.06'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '15.09'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '5.08'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '10.3'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '21.5'],
                        ['label' => 'Chiều dài chốt L3 (mm)', 'value' => '23.5'],
                        ['label' => 'Đường kính con lăn ngoài Ds (SR) (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều cao con lăn ngoài Hs (SR) (mm)', 'value' => '9.4'],
                    ],
                ],
                [
                    'sku' => '60-SR',
                    'display_code' => '60-SR',
                    'title_vi' => 'Xích con lăn cạnh 60-SR',
                    'title_en' => 'Side Roller Conveyor Chain 60-SR',
                    'slug_vi' => 'xich-con-lan-canh-60-sr',
                    'slug_en' => 'side-roller-conveyor-chain-60-sr',
                    'categories' => ['xich-con-lan-canh'],
                    'is_featured' => 0,
                    'desc_vi' => '60SR là Xích con lăn cạnh, một dãy, bước xích 19.05 mm, đường kính con lăn trong tối đa 11.91 mm, đường kính chốt 5.94 mm, đường kính con lăn ngoài 22.23 mm. Xích con lăn cạnh là xích con lăn bước ngắn có thêm con lăn ngoài gắn ở hai đầu chốt. Trong quá trình vận chuyển, các con lăn bên dẫn hướng cho xích, chịu tải và lăn tiếp xúc với ray dẫn tương ứng, nhờ đó xích chạy ổn định, giảm ma sát trượt và mài mòn. Dòng xích này thường dùng trong các băng tải đường cong và hệ thống băng tải cần dẫn hướng xích bằng ray. Con lăn ngoài có thể làm bằng nhựa kỹ thuật theo yêu cầu của khách hàng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt: nối các mắt xích, cho phép xích gập khi ăn khớp với đĩa xích; chốt được kéo dài về hai phía (các kích thước L1, L2, L3) để lắp con lăn ngoài.
• Ống lót và con lăn trong: lồng quanh chốt, ăn khớp với răng đĩa xích và giảm ma sát.
• Con lăn ngoài (bên): lắp ở đầu chốt, lăn trên ray dẫn để dẫn hướng và đỡ tải; kích thước theo cột Ds và Hs; có thể làm bằng nhựa kỹ thuật theo yêu cầu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở mặt lăn của con lăn ngoài.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', '/images/products/thanhkhang/xich-con-lan-canh/60-sr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '60'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '11.91'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '12.57'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '2.44'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '18.08'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '5.94'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '12.95'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '27.95'],
                        ['label' => 'Chiều dài chốt L3 (mm)', 'value' => '30.35'],
                        ['label' => 'Đường kính con lăn ngoài Ds (SR) (mm)', 'value' => '22.23'],
                        ['label' => 'Chiều cao con lăn ngoài Hs (SR) (mm)', 'value' => '12.6'],
                    ],
                ],
                [
                    'sku' => '80-SR',
                    'display_code' => '80-SR',
                    'title_vi' => 'Xích con lăn cạnh 80-SR',
                    'title_en' => 'Side Roller Conveyor Chain 80-SR',
                    'slug_vi' => 'xich-con-lan-canh-80-sr',
                    'slug_en' => 'side-roller-conveyor-chain-80-sr',
                    'categories' => ['xich-con-lan-canh'],
                    'is_featured' => 0,
                    'desc_vi' => '80SR là Xích con lăn cạnh, một dãy, bước xích 25.4 mm, đường kính con lăn trong tối đa 15.88 mm, đường kính chốt 7.92 mm, đường kính con lăn ngoài 28.58 mm. Xích con lăn cạnh là xích con lăn bước ngắn có thêm con lăn ngoài gắn ở hai đầu chốt. Trong quá trình vận chuyển, các con lăn bên dẫn hướng cho xích, chịu tải và lăn tiếp xúc với ray dẫn tương ứng, nhờ đó xích chạy ổn định, giảm ma sát trượt và mài mòn. Dòng xích này thường dùng trong các băng tải đường cong và hệ thống băng tải cần dẫn hướng xích bằng ray. Con lăn ngoài có thể làm bằng nhựa kỹ thuật theo yêu cầu của khách hàng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt: nối các mắt xích, cho phép xích gập khi ăn khớp với đĩa xích; chốt được kéo dài về hai phía (các kích thước L1, L2, L3) để lắp con lăn ngoài.
• Ống lót và con lăn trong: lồng quanh chốt, ăn khớp với răng đĩa xích và giảm ma sát.
• Con lăn ngoài (bên): lắp ở đầu chốt, lăn trên ray dẫn để dẫn hướng và đỡ tải; kích thước theo cột Ds và Hs; có thể làm bằng nhựa kỹ thuật theo yêu cầu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở mặt lăn của con lăn ngoài.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', '/images/products/thanhkhang/xich-con-lan-canh/80-sr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '80'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '15.75'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '3.26'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '24.13'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '7.92'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '16.35'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '35.05'],
                        ['label' => 'Chiều dài chốt L3 (mm)', 'value' => '37.95'],
                        ['label' => 'Đường kính con lăn ngoài Ds (SR) (mm)', 'value' => '28.58'],
                        ['label' => 'Chiều cao con lăn ngoài Hs (SR) (mm)', 'value' => '15.8'],
                    ],
                ],
                [
                    'sku' => '100-SR',
                    'display_code' => '100-SR',
                    'title_vi' => 'Xích con lăn cạnh 100-SR',
                    'title_en' => 'Side Roller Conveyor Chain 100-SR',
                    'slug_vi' => 'xich-con-lan-canh-100-sr',
                    'slug_en' => 'side-roller-conveyor-chain-100-sr',
                    'categories' => ['xich-con-lan-canh'],
                    'is_featured' => 0,
                    'desc_vi' => '100SR là Xích con lăn cạnh, một dãy, bước xích 31.75 mm, đường kính con lăn trong tối đa 19.05 mm, đường kính chốt 9.53 mm, đường kính con lăn ngoài 39.69 mm. Xích con lăn cạnh là xích con lăn bước ngắn có thêm con lăn ngoài gắn ở hai đầu chốt. Trong quá trình vận chuyển, các con lăn bên dẫn hướng cho xích, chịu tải và lăn tiếp xúc với ray dẫn tương ứng, nhờ đó xích chạy ổn định, giảm ma sát trượt và mài mòn. Dòng xích này thường dùng trong các băng tải đường cong và hệ thống băng tải cần dẫn hướng xích bằng ray. Con lăn ngoài có thể làm bằng nhựa kỹ thuật theo yêu cầu của khách hàng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt: nối các mắt xích, cho phép xích gập khi ăn khớp với đĩa xích; chốt được kéo dài về hai phía (các kích thước L1, L2, L3) để lắp con lăn ngoài.
• Ống lót và con lăn trong: lồng quanh chốt, ăn khớp với răng đĩa xích và giảm ma sát.
• Con lăn ngoài (bên): lắp ở đầu chốt, lăn trên ray dẫn để dẫn hướng và đỡ tải; kích thước theo cột Ds và Hs; có thể làm bằng nhựa kỹ thuật theo yêu cầu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở mặt lăn của con lăn ngoài.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-canh/side-roller-photo.webp', '/images/products/thanhkhang/xich-con-lan-canh/100-sr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '100'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '18.9'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '4'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '30.18'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '9.53'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '20.55'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '42.55'],
                        ['label' => 'Chiều dài chốt L3 (mm)', 'value' => '45.65'],
                        ['label' => 'Đường kính con lăn ngoài Ds (SR) (mm)', 'value' => '39.69'],
                        ['label' => 'Chiều cao con lăn ngoài Hs (SR) (mm)', 'value' => '19'],
                    ],
                ],
                [
                    'sku' => '40-TR',
                    'display_code' => '40-TR',
                    'title_vi' => 'Xích con lăn trên 40-TR',
                    'title_en' => 'Top Roller Conveyor Chain 40-TR',
                    'slug_vi' => 'xich-con-lan-tren-40-tr',
                    'slug_en' => 'top-roller-conveyor-chain-40-tr',
                    'categories' => ['xich-con-lan-tren-hanh-ly', 'xich-con-lan-tren-dong-goi', 'xich-con-lan-tren-bang-tai-nhe'],
                    'is_featured' => 1,
                    'desc_vi' => '40TR là Xích con lăn trên, một dãy, bước xích 12.7 mm, đường kính con lăn 7.94 mm, đường kính chốt 3.97 mm, đường kính con lăn trên 15.88 mm, lực kéo đứt 1,700 kgf và khối lượng khoảng 1.30 kg/m. Xích con lăn trên là xích con lăn bước ngắn có má dạng tai mang con lăn trên. Các con lăn trên đỡ trực tiếp vật được vận chuyển và lăn trên mặt ray, giúp hàng di chuyển êm, giảm ma sát; xích chủ yếu dùng để mang tải và vận chuyển vật liệu, đồng thời có thể thực hiện chức năng gom và xả hàng (tích lũy). Xích có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài, đáp ứng nhiều điều kiện làm việc.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: tạo thân xích và chịu lực kéo; má dạng tai có chiều cao h2 để lắp con lăn trên.
• Chốt, ống lót và con lăn trong: nối các mắt xích, ăn khớp với đĩa xích và giảm ma sát; chốt kéo dài ở một số cỡ để lắp bộ phận phụ.
• Con lăn trên: lắp trên tai má xích bằng chốt phụ (đường kính d3), đỡ vật được vận chuyển và lăn trên ray; kích thước theo nhóm cột Thông số con lăn trên.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải (thường dùng thép carbon hoặc thép không gỉ tùy môi trường).
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở chốt phụ, mặt lăn của con lăn trên.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-2.webp', '/images/products/thanhkhang/xich-con-lan-tren/40-tr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '40'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '7.94'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '7.95'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '1.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '12'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '3.97'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '8.2'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '9.25'],
                        ['label' => 'Lực kéo đứt Q (TR) (kgf)', 'value' => '1700'],
                        ['label' => 'Lực kéo đứt Q quy đổi (TR) (kN)', 'value' => '16.67'],
                        ['label' => 'Lực kéo trung bình Q0 (TR) (kgf)', 'value' => '370'],
                        ['label' => 'Đường kính con lăn trên DF (TR) (mm)', 'value' => '15.88'],
                        ['label' => 'Cs (TR) (mm)', 'value' => '12.7'],
                        ['label' => 'N (TR) (mm)', 'value' => '9.5'],
                        ['label' => 'Xs (TR) (mm)', 'value' => '17.45'],
                        ['label' => 'XSA (TR) (mm)', 'value' => '17.4'],
                        ['label' => 'M (TR) (mm)', 'value' => '9.5'],
                        ['label' => 'I (TR) (mm)', 'value' => '13.2'],
                        ['label' => 'Đường kính chốt phụ d3 (TR) (mm)', 'value' => '4.03'],
                        ['label' => 'Khối lượng mỗi mét q (TR) (kg/m)', 'value' => '1.3'],
                    ],
                ],
                [
                    'sku' => '50-TR',
                    'display_code' => '50-TR',
                    'title_vi' => 'Xích con lăn trên 50-TR',
                    'title_en' => 'Top Roller Conveyor Chain 50-TR',
                    'slug_vi' => 'xich-con-lan-tren-50-tr',
                    'slug_en' => 'top-roller-conveyor-chain-50-tr',
                    'categories' => ['xich-con-lan-tren-hanh-ly', 'xich-con-lan-tren-dong-goi', 'xich-con-lan-tren-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '50TR là Xích con lăn trên, một dãy, bước xích 15.875 mm, đường kính con lăn 10.16 mm, đường kính chốt 5.09 mm, đường kính con lăn trên 19.05 mm, lực kéo đứt 2,800 kgf và khối lượng khoảng 1.90 kg/m. Xích con lăn trên là xích con lăn bước ngắn có má dạng tai mang con lăn trên. Các con lăn trên đỡ trực tiếp vật được vận chuyển và lăn trên mặt ray, giúp hàng di chuyển êm, giảm ma sát; xích chủ yếu dùng để mang tải và vận chuyển vật liệu, đồng thời có thể thực hiện chức năng gom và xả hàng (tích lũy). Xích có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài, đáp ứng nhiều điều kiện làm việc.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: tạo thân xích và chịu lực kéo; má dạng tai có chiều cao h2 để lắp con lăn trên.
• Chốt, ống lót và con lăn trong: nối các mắt xích, ăn khớp với đĩa xích và giảm ma sát; chốt kéo dài ở một số cỡ để lắp bộ phận phụ.
• Con lăn trên: lắp trên tai má xích bằng chốt phụ (đường kính d3), đỡ vật được vận chuyển và lăn trên ray; kích thước theo nhóm cột Thông số con lăn trên.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải (thường dùng thép carbon hoặc thép không gỉ tùy môi trường).
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở chốt phụ, mặt lăn của con lăn trên.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-2.webp', '/images/products/thanhkhang/xich-con-lan-tren/50-tr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '50'],
                        ['label' => 'Bước xích P (mm)', 'value' => '15.875'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '9.53'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '2.06'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '15'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '5.09'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '10.3'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '11.85'],
                        ['label' => 'Lực kéo đứt Q (TR) (kgf)', 'value' => '2800'],
                        ['label' => 'Lực kéo đứt Q quy đổi (TR) (kN)', 'value' => '27.46'],
                        ['label' => 'Lực kéo trung bình Q0 (TR) (kgf)', 'value' => '650'],
                        ['label' => 'Đường kính con lăn trên DF (TR) (mm)', 'value' => '19.05'],
                        ['label' => 'Cs (TR) (mm)', 'value' => '15.9'],
                        ['label' => 'N (TR) (mm)', 'value' => '12.7'],
                        ['label' => 'Xs (TR) (mm)', 'value' => '22.25'],
                        ['label' => 'XSA (TR) (mm)', 'value' => '23.05'],
                        ['label' => 'M (TR) (mm)', 'value' => '12.7'],
                        ['label' => 'I (TR) (mm)', 'value' => '16.2'],
                        ['label' => 'Đường kính chốt phụ d3 (TR) (mm)', 'value' => '5.2'],
                        ['label' => 'Khối lượng mỗi mét q (TR) (kg/m)', 'value' => '1.9'],
                    ],
                ],
                [
                    'sku' => '60-TR',
                    'display_code' => '60-TR',
                    'title_vi' => 'Xích con lăn trên 60-TR',
                    'title_en' => 'Top Roller Conveyor Chain 60-TR',
                    'slug_vi' => 'xich-con-lan-tren-60-tr',
                    'slug_en' => 'top-roller-conveyor-chain-60-tr',
                    'categories' => ['xich-con-lan-tren-hanh-ly', 'xich-con-lan-tren-dong-goi', 'xich-con-lan-tren-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '60TR là Xích con lăn trên, một dãy, bước xích 19.05 mm, đường kính con lăn 11.91 mm, đường kính chốt 5.96 mm, đường kính con lăn trên 22.23 mm, lực kéo đứt 4,100 kgf và khối lượng khoảng 2.90 kg/m. Xích con lăn trên là xích con lăn bước ngắn có má dạng tai mang con lăn trên. Các con lăn trên đỡ trực tiếp vật được vận chuyển và lăn trên mặt ray, giúp hàng di chuyển êm, giảm ma sát; xích chủ yếu dùng để mang tải và vận chuyển vật liệu, đồng thời có thể thực hiện chức năng gom và xả hàng (tích lũy). Xích có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài, đáp ứng nhiều điều kiện làm việc.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: tạo thân xích và chịu lực kéo; má dạng tai có chiều cao h2 để lắp con lăn trên.
• Chốt, ống lót và con lăn trong: nối các mắt xích, ăn khớp với đĩa xích và giảm ma sát; chốt kéo dài ở một số cỡ để lắp bộ phận phụ.
• Con lăn trên: lắp trên tai má xích bằng chốt phụ (đường kính d3), đỡ vật được vận chuyển và lăn trên ray; kích thước theo nhóm cột Thông số con lăn trên.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải (thường dùng thép carbon hoặc thép không gỉ tùy môi trường).
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở chốt phụ, mặt lăn của con lăn trên.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-2.webp', '/images/products/thanhkhang/xich-con-lan-tren/60-tr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '60'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '11.91'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '12.7'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '2.44'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '18.1'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '5.96'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '12.75'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '14.55'],
                        ['label' => 'Lực kéo đứt Q (TR) (kgf)', 'value' => '4100'],
                        ['label' => 'Lực kéo đứt Q quy đổi (TR) (kN)', 'value' => '40.21'],
                        ['label' => 'Lực kéo trung bình Q0 (TR) (kgf)', 'value' => '900'],
                        ['label' => 'Đường kính con lăn trên DF (TR) (mm)', 'value' => '22.23'],
                        ['label' => 'Cs (TR) (mm)', 'value' => '18.3'],
                        ['label' => 'N (TR) (mm)', 'value' => '15.9'],
                        ['label' => 'Xs (TR) (mm)', 'value' => '26.25'],
                        ['label' => 'XSA (TR) (mm)', 'value' => '26.85'],
                        ['label' => 'M (TR) (mm)', 'value' => '15.9'],
                        ['label' => 'I (TR) (mm)', 'value' => '20.6'],
                        ['label' => 'Đường kính chốt phụ d3 (TR) (mm)', 'value' => '6.1'],
                        ['label' => 'Khối lượng mỗi mét q (TR) (kg/m)', 'value' => '2.9'],
                    ],
                ],
                [
                    'sku' => '80-TR',
                    'display_code' => '80-TR',
                    'title_vi' => 'Xích con lăn trên 80-TR',
                    'title_en' => 'Top Roller Conveyor Chain 80-TR',
                    'slug_vi' => 'xich-con-lan-tren-80-tr',
                    'slug_en' => 'top-roller-conveyor-chain-80-tr',
                    'categories' => ['xich-con-lan-tren-hanh-ly', 'xich-con-lan-tren-dong-goi', 'xich-con-lan-tren-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '80TR là Xích con lăn trên, một dãy, bước xích 25.4 mm, đường kính con lăn 15.88 mm, đường kính chốt 7.94 mm, đường kính con lăn trên 28.58 mm, lực kéo đứt 7,000 kgf và khối lượng khoảng 4.80 kg/m. Xích con lăn trên là xích con lăn bước ngắn có má dạng tai mang con lăn trên. Các con lăn trên đỡ trực tiếp vật được vận chuyển và lăn trên mặt ray, giúp hàng di chuyển êm, giảm ma sát; xích chủ yếu dùng để mang tải và vận chuyển vật liệu, đồng thời có thể thực hiện chức năng gom và xả hàng (tích lũy). Xích có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài, đáp ứng nhiều điều kiện làm việc.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: tạo thân xích và chịu lực kéo; má dạng tai có chiều cao h2 để lắp con lăn trên.
• Chốt, ống lót và con lăn trong: nối các mắt xích, ăn khớp với đĩa xích và giảm ma sát; chốt kéo dài ở một số cỡ để lắp bộ phận phụ.
• Con lăn trên: lắp trên tai má xích bằng chốt phụ (đường kính d3), đỡ vật được vận chuyển và lăn trên ray; kích thước theo nhóm cột Thông số con lăn trên.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải (thường dùng thép carbon hoặc thép không gỉ tùy môi trường).
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở chốt phụ, mặt lăn của con lăn trên.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-2.webp', '/images/products/thanhkhang/xich-con-lan-tren/80-tr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '80'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '15.88'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '3.26'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '24.1'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '7.94'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '16.23'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '19.82'],
                        ['label' => 'Lực kéo đứt Q (TR) (kgf)', 'value' => '7000'],
                        ['label' => 'Lực kéo đứt Q quy đổi (TR) (kN)', 'value' => '68.65'],
                        ['label' => 'Lực kéo trung bình Q0 (TR) (kgf)', 'value' => '1500'],
                        ['label' => 'Đường kính con lăn trên DF (TR) (mm)', 'value' => '28.58'],
                        ['label' => 'Cs (TR) (mm)', 'value' => '24.6'],
                        ['label' => 'N (TR) (mm)', 'value' => '19.1'],
                        ['label' => 'Xs (TR) (mm)', 'value' => '34.15'],
                        ['label' => 'XSA (TR) (mm)', 'value' => '35.45'],
                        ['label' => 'M (TR) (mm)', 'value' => '19.1'],
                        ['label' => 'I (TR) (mm)', 'value' => '25.7'],
                        ['label' => 'Đường kính chốt phụ d3 (TR) (mm)', 'value' => '8.07'],
                        ['label' => 'Khối lượng mỗi mét q (TR) (kg/m)', 'value' => '4.8'],
                    ],
                ],
                [
                    'sku' => '100-TR',
                    'display_code' => '100-TR',
                    'title_vi' => 'Xích con lăn trên 100-TR',
                    'title_en' => 'Top Roller Conveyor Chain 100-TR',
                    'slug_vi' => 'xich-con-lan-tren-100-tr',
                    'slug_en' => 'top-roller-conveyor-chain-100-tr',
                    'categories' => ['xich-con-lan-tren-hanh-ly'],
                    'is_featured' => 0,
                    'desc_vi' => '100TR là Xích con lăn trên, một dãy, bước xích 31.75 mm, đường kính con lăn 19.05 mm, đường kính chốt 9.54 mm, đường kính con lăn trên 36.69 mm, lực kéo đứt 11,000 kgf và khối lượng khoảng 7.90 kg/m. Xích con lăn trên là xích con lăn bước ngắn có má dạng tai mang con lăn trên. Các con lăn trên đỡ trực tiếp vật được vận chuyển và lăn trên mặt ray, giúp hàng di chuyển êm, giảm ma sát; xích chủ yếu dùng để mang tải và vận chuyển vật liệu, đồng thời có thể thực hiện chức năng gom và xả hàng (tích lũy). Xích có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài, đáp ứng nhiều điều kiện làm việc.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: tạo thân xích và chịu lực kéo; má dạng tai có chiều cao h2 để lắp con lăn trên.
• Chốt, ống lót và con lăn trong: nối các mắt xích, ăn khớp với đĩa xích và giảm ma sát; chốt kéo dài ở một số cỡ để lắp bộ phận phụ.
• Con lăn trên: lắp trên tai má xích bằng chốt phụ (đường kính d3), đỡ vật được vận chuyển và lăn trên ray; kích thước theo nhóm cột Thông số con lăn trên.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải (thường dùng thép carbon hoặc thép không gỉ tùy môi trường).
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước) và ở chốt phụ, mặt lăn của con lăn trên.',
                    'standard_vi' => 'Kích thước cơ bản của xích (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI cùng số hiệu (40, 50, 60, 80, 100). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước, đồng thời giữ độ căng xích phù hợp để kéo dài tuổi thọ. Kiểm tra thêm độ quay trơn và mòn của con lăn ngoài/con lăn trên và tình trạng ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-con-lan-tren/top-roller-1.webp', '/images/products/thanhkhang/xich-con-lan-tren/top-roller-2.webp', '/images/products/thanhkhang/xich-con-lan-tren/100-tr-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Số hiệu ANSI tương đương', 'value' => '100'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (SR) / d1 (TR) (mm)', 'value' => '19.05'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 (mm)', 'value' => '19.05'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '4'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '30.1'],
                        ['label' => 'Đường kính chốt d (mm)', 'value' => '9.54'],
                        ['label' => 'Chiều dài chốt L1 (mm)', 'value' => '19.8'],
                        ['label' => 'Chiều dài chốt L2 (mm)', 'value' => '23.6'],
                        ['label' => 'Lực kéo đứt Q (TR) (kgf)', 'value' => '11000'],
                        ['label' => 'Lực kéo đứt Q quy đổi (TR) (kN)', 'value' => '107.87'],
                        ['label' => 'Lực kéo trung bình Q0 (TR) (kgf)', 'value' => '2300'],
                        ['label' => 'Đường kính con lăn trên DF (TR) (mm)', 'value' => '36.69'],
                        ['label' => 'Cs (TR) (mm)', 'value' => '31.8'],
                        ['label' => 'N (TR) (mm)', 'value' => '25.4'],
                        ['label' => 'Xs (TR) (mm)', 'value' => '44.5'],
                        ['label' => 'XSA (TR) (mm)', 'value' => '44'],
                        ['label' => 'M (TR) (mm)', 'value' => '25.4'],
                        ['label' => 'I (TR) (mm)', 'value' => '31.1'],
                        ['label' => 'Đường kính chốt phụ d3 (TR) (mm)', 'value' => '9.73'],
                        ['label' => 'Khối lượng mỗi mét q (TR) (kg/m)', 'value' => '7.9'],
                    ],
                ],
                [
                    'sku' => '40HP',
                    'display_code' => '40HP',
                    'title_vi' => 'Xích chốt rỗng 40HP',
                    'title_en' => 'Hollow Pin Conveyor Chain 40HP',
                    'slug_vi' => 'xich-chot-rong-40hp',
                    'slug_en' => 'hollow-pin-conveyor-chain-40hp',
                    'categories' => ['xich-chot-rong-dong-goi', 'xich-chot-rong-bang-tai-nhe'],
                    'is_featured' => 1,
                    'desc_vi' => '40HP là xích chốt rỗng kiểu I, bước xích 12.7 mm, đường kính ống lót tối đa 7.95 mm, đường kính chốt 5.68 mm, lực kéo đứt tối thiểu 13.2 kN. Xích chốt rỗng là xích con lăn có chốt dạng ống rỗng, đối lập với xích chốt đặc. Trục phụ kiện, thanh ngang hoặc các chi tiết gắn kèm được lắp vào lòng chốt để nâng và vận chuyển vật liệu. Nhờ đó có thể thêm, tháo hoặc thay phụ kiện nhanh mà không phải tháo cả hệ thống, thuận tiện cho việc thay đổi bố trí băng tải và vệ sinh (như trong chế biến thực phẩm). Xích truyền chuyển động êm, đồng bộ cao; có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt rỗng: dạng ống, nối các mắt xích và là nơi lắp trục phụ kiện hoặc thanh ngang; kích thước chốt và chiều dài chốt ghi ở các cột d2, d3, A, B, L, Lc.
• Ống lót và con lăn: lồng quanh chốt, ăn khớp với đĩa xích và giảm ma sát.
• Phụ kiện (thanh ngang, trục): lắp vào chốt rỗng theo nhu cầu sử dụng.
• Vật liệu và xử lý nhiệt: dùng vật liệu chất lượng cao và công nghệ gia công tiên tiến; chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước).',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn/ống lót, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI/ISO cùng cỡ. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước; hạn chế bụi bẩn bám lên xích. Khi thay phụ kiện, chỉ cần tháo phụ kiện khỏi chốt rỗng mà không phải tháo toàn bộ xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp',
                    'images' => ['/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-3.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-4.webp', '/images/products/thanhkhang/xich-chot-rong/40hp-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Kiểu (Type)', 'value' => 'I'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn / ống lót d1 max (mm)', 'value' => '7.95'],
                        ['label' => 'Chiều rộng trong b1 min (mm)', 'value' => '7.95'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.68'],
                        ['label' => 'd3 min (mm)', 'value' => '4'],
                        ['label' => 'Kích thước A (mm)', 'value' => '8.1'],
                        ['label' => 'Kích thước B (mm)', 'value' => '9.4'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '16.2'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '17.5'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.7'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '13.2'],
                    ],
                ],
                [
                    'sku' => '50HP',
                    'display_code' => '50HP',
                    'title_vi' => 'Xích chốt rỗng 50HP',
                    'title_en' => 'Hollow Pin Conveyor Chain 50HP',
                    'slug_vi' => 'xich-chot-rong-50hp',
                    'slug_en' => 'hollow-pin-conveyor-chain-50hp',
                    'categories' => ['xich-chot-rong-dong-goi', 'xich-chot-rong-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '50HP là xích chốt rỗng kiểu I, bước xích 15.875 mm, đường kính ống lót tối đa 10.16 mm, đường kính chốt 7.24 mm, lực kéo đứt tối thiểu 20.6 kN. Xích chốt rỗng là xích con lăn có chốt dạng ống rỗng, đối lập với xích chốt đặc. Trục phụ kiện, thanh ngang hoặc các chi tiết gắn kèm được lắp vào lòng chốt để nâng và vận chuyển vật liệu. Nhờ đó có thể thêm, tháo hoặc thay phụ kiện nhanh mà không phải tháo cả hệ thống, thuận tiện cho việc thay đổi bố trí băng tải và vệ sinh (như trong chế biến thực phẩm). Xích truyền chuyển động êm, đồng bộ cao; có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt rỗng: dạng ống, nối các mắt xích và là nơi lắp trục phụ kiện hoặc thanh ngang; kích thước chốt và chiều dài chốt ghi ở các cột d2, d3, A, B, L, Lc.
• Ống lót và con lăn: lồng quanh chốt, ăn khớp với đĩa xích và giảm ma sát.
• Phụ kiện (thanh ngang, trục): lắp vào chốt rỗng theo nhu cầu sử dụng.
• Vật liệu và xử lý nhiệt: dùng vật liệu chất lượng cao và công nghệ gia công tiên tiến; chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước).',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn/ống lót, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI/ISO cùng cỡ. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước; hạn chế bụi bẩn bám lên xích. Khi thay phụ kiện, chỉ cần tháo phụ kiện khỏi chốt rỗng mà không phải tháo toàn bộ xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp',
                    'images' => ['/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-3.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-4.webp', '/images/products/thanhkhang/xich-chot-rong/50hp-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Kiểu (Type)', 'value' => 'I'],
                        ['label' => 'Bước xích P (mm)', 'value' => '15.875'],
                        ['label' => 'Đường kính con lăn / ống lót d1 max (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong b1 min (mm)', 'value' => '9.53'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.24'],
                        ['label' => 'd3 min (mm)', 'value' => '5.12'],
                        ['label' => 'Kích thước A (mm)', 'value' => '10.3'],
                        ['label' => 'Kích thước B (mm)', 'value' => '11.7'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '20.6'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '22'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '14.6'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '20.6'],
                    ],
                ],
                [
                    'sku' => '60HP',
                    'display_code' => '60HP',
                    'title_vi' => 'Xích chốt rỗng 60HP',
                    'title_en' => 'Hollow Pin Conveyor Chain 60HP',
                    'slug_vi' => 'xich-chot-rong-60hp',
                    'slug_en' => 'hollow-pin-conveyor-chain-60hp',
                    'categories' => ['xich-chot-rong-dong-goi', 'xich-chot-rong-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '60HP là xích chốt rỗng kiểu I, bước xích 19.05 mm, đường kính ống lót tối đa 11.91 mm, đường kính chốt 8.37 mm, lực kéo đứt tối thiểu 31.4 kN. Xích chốt rỗng là xích con lăn có chốt dạng ống rỗng, đối lập với xích chốt đặc. Trục phụ kiện, thanh ngang hoặc các chi tiết gắn kèm được lắp vào lòng chốt để nâng và vận chuyển vật liệu. Nhờ đó có thể thêm, tháo hoặc thay phụ kiện nhanh mà không phải tháo cả hệ thống, thuận tiện cho việc thay đổi bố trí băng tải và vệ sinh (như trong chế biến thực phẩm). Xích truyền chuyển động êm, đồng bộ cao; có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt rỗng: dạng ống, nối các mắt xích và là nơi lắp trục phụ kiện hoặc thanh ngang; kích thước chốt và chiều dài chốt ghi ở các cột d2, d3, A, B, L, Lc.
• Ống lót và con lăn: lồng quanh chốt, ăn khớp với đĩa xích và giảm ma sát.
• Phụ kiện (thanh ngang, trục): lắp vào chốt rỗng theo nhu cầu sử dụng.
• Vật liệu và xử lý nhiệt: dùng vật liệu chất lượng cao và công nghệ gia công tiên tiến; chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước).',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn/ống lót, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI/ISO cùng cỡ. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước; hạn chế bụi bẩn bám lên xích. Khi thay phụ kiện, chỉ cần tháo phụ kiện khỏi chốt rỗng mà không phải tháo toàn bộ xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp',
                    'images' => ['/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-3.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-4.webp', '/images/products/thanhkhang/xich-chot-rong/60hp-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Kiểu (Type)', 'value' => 'I'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn / ống lót d1 max (mm)', 'value' => '11.91'],
                        ['label' => 'Chiều rộng trong b1 min (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '8.37'],
                        ['label' => 'd3 min (mm)', 'value' => '5.99'],
                        ['label' => 'Kích thước A (mm)', 'value' => '12.9'],
                        ['label' => 'Kích thước B (mm)', 'value' => '14.3'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '25.8'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '27.2'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '17.5'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2.4'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '31.4'],
                    ],
                ],
                [
                    'sku' => '80HP',
                    'display_code' => '80HP',
                    'title_vi' => 'Xích chốt rỗng 80HP',
                    'title_en' => 'Hollow Pin Conveyor Chain 80HP',
                    'slug_vi' => 'xich-chot-rong-80hp',
                    'slug_en' => 'hollow-pin-conveyor-chain-80hp',
                    'categories' => ['xich-chot-rong-dong-goi', 'xich-chot-rong-bang-tai-nhe'],
                    'is_featured' => 0,
                    'desc_vi' => '80HP là xích chốt rỗng kiểu I, bước xích 25.4 mm, đường kính ống lót tối đa 15.88 mm, đường kính chốt 11.24 mm, lực kéo đứt tối thiểu 53.0 kN. Xích chốt rỗng là xích con lăn có chốt dạng ống rỗng, đối lập với xích chốt đặc. Trục phụ kiện, thanh ngang hoặc các chi tiết gắn kèm được lắp vào lòng chốt để nâng và vận chuyển vật liệu. Nhờ đó có thể thêm, tháo hoặc thay phụ kiện nhanh mà không phải tháo cả hệ thống, thuận tiện cho việc thay đổi bố trí băng tải và vệ sinh (như trong chế biến thực phẩm). Xích truyền chuyển động êm, đồng bộ cao; có độ bền cao, bền mỏi, chịu mài mòn và va đập tốt, tuổi thọ dài.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (trong và ngoài): tạo thân xích và chịu lực kéo khi vận hành.
• Chốt rỗng: dạng ống, nối các mắt xích và là nơi lắp trục phụ kiện hoặc thanh ngang; kích thước chốt và chiều dài chốt ghi ở các cột d2, d3, A, B, L, Lc.
• Ống lót và con lăn: lồng quanh chốt, ăn khớp với đĩa xích và giảm ma sát.
• Phụ kiện (thanh ngang, trục): lắp vào chốt rỗng theo nhu cầu sử dụng.
• Vật liệu và xử lý nhiệt: dùng vật liệu chất lượng cao và công nghệ gia công tiên tiến; chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót (làm xích giãn bước).',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn/ống lót, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn ANSI/ISO cùng cỡ. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, kiểm tra độ mòn và độ giãn bước; hạn chế bụi bẩn bám lên xích. Khi thay phụ kiện, chỉ cần tháo phụ kiện khỏi chốt rỗng mà không phải tháo toàn bộ xích. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới.',
                    'image' => '/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp',
                    'images' => ['/images/products/thanhkhang/xich-chot-rong/hollow-pin-2.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-3.webp', '/images/products/thanhkhang/xich-chot-rong/hollow-pin-4.webp', '/images/products/thanhkhang/xich-chot-rong/80hp-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Kiểu (Type)', 'value' => 'I'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn / ống lót d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong b1 min (mm)', 'value' => '15.88'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '11.24'],
                        ['label' => 'd3 min (mm)', 'value' => '8.02'],
                        ['label' => 'Kích thước A (mm)', 'value' => '16.3'],
                        ['label' => 'Kích thước B (mm)', 'value' => '17.8'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '32.6'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '34.1'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '23'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.2'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '53'],
                    ],
                ],
                [
                    'sku' => '40SB',
                    'display_code' => '40SB',
                    'title_vi' => 'Xích cong 40SB',
                    'title_en' => 'Side Bow Conveyor Chain 40SB',
                    'slug_vi' => 'xich-cong-40sb',
                    'slug_en' => 'side-bow-conveyor-chain-40sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 1,
                    'desc_vi' => '40SB là Xích cong một dãy, bước xích 12.7 mm, đường kính con lăn tối đa 7.95 mm, bán kính cong bên tối thiểu 350 mm, lực kéo đứt tối thiểu 13.8 kN và khối lượng khoảng 0.80 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/40sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '7.95'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '7.85'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.96'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '16.9'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '18.1'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.7'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.50'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '350'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '13.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '3136'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '15.2'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.8'],
                    ],
                ],
                [
                    'sku' => '43SB',
                    'display_code' => '43SB',
                    'title_vi' => 'Xích cong 43SB',
                    'title_en' => 'Side Bow Conveyor Chain 43SB',
                    'slug_vi' => 'xich-cong-43sb',
                    'slug_en' => 'side-bow-conveyor-chain-43sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '43SB là Xích cong một dãy, bước xích 12.7 mm, đường kính con lăn tối đa 7.95 mm, bán kính cong bên tối thiểu 305 mm, lực kéo đứt tối thiểu 12 kN và khối lượng khoảng 0.64 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/43sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '7.95'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '7.85'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.45'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '18.3'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '19.5'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.7'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.50'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '305'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '12'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '2727'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '13.2'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.64'],
                    ],
                ],
                [
                    'sku' => '50SB',
                    'display_code' => '50SB',
                    'title_vi' => 'Xích cong 50SB',
                    'title_en' => 'Side Bow Conveyor Chain 50SB',
                    'slug_vi' => 'xich-cong-50sb',
                    'slug_en' => 'side-bow-conveyor-chain-50sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '50SB là Xích cong một dãy, bước xích 15.875 mm, đường kính con lăn tối đa 10.16 mm, bán kính cong bên tối thiểu 400 mm, lực kéo đứt tối thiểu 20.6 kN và khối lượng khoảng 1.09 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/50sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '15.875'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '9.4'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '4.37'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '20.7'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '22.7'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '15.09'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2.00'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '400'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '20.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '4681'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '22.7'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '1.09'],
                    ],
                ],
                [
                    'sku' => '60SB',
                    'display_code' => '60SB',
                    'title_vi' => 'Xích cong 60SB',
                    'title_en' => 'Side Bow Conveyor Chain 60SB',
                    'slug_vi' => 'xich-cong-60sb',
                    'slug_en' => 'side-bow-conveyor-chain-60sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '60SB là Xích cong một dãy, bước xích 19.05 mm, đường kính con lăn tối đa 11.91 mm, bán kính cong bên tối thiểu 500 mm, lực kéo đứt tối thiểu 15.7 kN và khối lượng khoảng 1.42 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/60sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '11.91'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '12.57'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.34'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '26.6'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '28.4'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '18.1'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2.40'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '500'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '15.7'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '3568'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '17.3'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '1.42'],
                    ],
                ],
                [
                    'sku' => '63SB',
                    'display_code' => '63SB',
                    'title_vi' => 'Xích cong 63SB',
                    'title_en' => 'Side Bow Conveyor Chain 63SB',
                    'slug_vi' => 'xich-cong-63sb',
                    'slug_en' => 'side-bow-conveyor-chain-63sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '63SB là Xích cong một dãy, bước xích 19.05 mm, đường kính con lăn tối đa 11.91 mm, bán kính cong bên tối thiểu 350 mm, lực kéo đứt tối thiểu 12.5 kN và khối lượng khoảng 1.40 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/63sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '11.91'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '12.68'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.08'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '28.8'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '30.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '17.2'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2.4/2.0'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '350'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '12.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '2840'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '20'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '1.4'],
                    ],
                ],
                [
                    'sku' => '80SB',
                    'display_code' => '80SB',
                    'title_vi' => 'Xích cong 80SB',
                    'title_en' => 'Side Bow Conveyor Chain 80SB',
                    'slug_vi' => 'xich-cong-80sb',
                    'slug_en' => 'side-bow-conveyor-chain-80sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '80SB là Xích cong một dãy, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm, bán kính cong bên tối thiểu 711 mm, lực kéo đứt tối thiểu 40.9 kN và khối lượng khoảng 2.60 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/80sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '15.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.19'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '34'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '37.3'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '24'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.20'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '711'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '40.9'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '9201'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '42'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.6'],
                    ],
                ],
                [
                    'sku' => '08BSB',
                    'display_code' => '08BSB',
                    'title_vi' => 'Xích cong 08BSB',
                    'title_en' => 'Side Bow Conveyor Chain 08BSB',
                    'slug_vi' => 'xich-cong-08bsb',
                    'slug_en' => 'side-bow-conveyor-chain-08bsb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '08BSB là Xích cong một dãy, bước xích 12.7 mm, đường kính con lăn tối đa 8.51 mm, bán kính cong bên tối thiểu 400 mm, lực kéo đứt tối thiểu 14 kN và khối lượng khoảng 0.70 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/08bsb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '8.51'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '7.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.97'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '17.4'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '18.7'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.8'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.60'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '400'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '14'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '3182'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '15.4'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.7'],
                    ],
                ],
                [
                    'sku' => '08BSBF1',
                    'display_code' => '08BSBF1',
                    'title_vi' => 'Xích cong 08BSBF1',
                    'title_en' => 'Side Bow Conveyor Chain 08BSBF1',
                    'slug_vi' => 'xich-cong-08bsbf1',
                    'slug_en' => 'side-bow-conveyor-chain-08bsbf1',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '08BSBF1 là Xích cong một dãy, bước xích 12.7 mm, đường kính con lăn tối đa 8.51 mm, bán kính cong bên tối thiểu 400 mm, lực kéo đứt tối thiểu 12.8 kN và khối lượng khoảng 0.65 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/08bsbf1-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '8.51'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '7.75'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.97'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '16.3'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '17.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.8'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.6/1.2'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '400'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '12.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '2909'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '14.1'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.65'],
                    ],
                ],
                [
                    'sku' => '10BSB',
                    'display_code' => '10BSB',
                    'title_vi' => 'Xích cong 10BSB',
                    'title_en' => 'Side Bow Conveyor Chain 10BSB',
                    'slug_vi' => 'xich-cong-10bsb',
                    'slug_en' => 'side-bow-conveyor-chain-10bsb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '10BSB là Xích cong một dãy, bước xích 15.875 mm, đường kính con lăn tối đa 10.16 mm, bán kính cong bên tối thiểu 400 mm, lực kéo đứt tối thiểu 15.6 kN và khối lượng khoảng 0.93 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/10bsb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '15.875'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '9.65'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '4.5'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '20.1'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '21.5'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '14.7'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.70'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '400'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '15.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '3545'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '17.2'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.93'],
                    ],
                ],
                [
                    'sku' => '12BSB',
                    'display_code' => '12BSB',
                    'title_vi' => 'Xích cong 12BSB',
                    'title_en' => 'Side Bow Conveyor Chain 12BSB',
                    'slug_vi' => 'xich-cong-12bsb',
                    'slug_en' => 'side-bow-conveyor-chain-12bsb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '12BSB là Xích cong một dãy, bước xích 19.05 mm, đường kính con lăn tối đa 12.07 mm, bán kính cong bên tối thiểu 500 mm, lực kéo đứt tối thiểu 20.5 kN và khối lượng khoảng 1.16 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/12bsb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '19.05'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '12.07'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '11.68'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.12'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '23.1'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '24.8'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '16'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.85'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '500'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '20.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '4658'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '22.6'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '1.16'],
                    ],
                ],
                [
                    'sku' => '16BSB',
                    'display_code' => '16BSB',
                    'title_vi' => 'Xích cong 16BSB',
                    'title_en' => 'Side Bow Conveyor Chain 16BSB',
                    'slug_vi' => 'xich-cong-16bsb',
                    'slug_en' => 'side-bow-conveyor-chain-16bsb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => '16BSB là Xích cong một dãy, bước xích 25.4 mm, đường kính con lăn tối đa 15.88 mm, bán kính cong bên tối thiểu 500 mm, lực kéo đứt tối thiểu 55.6 kN và khối lượng khoảng 2.53 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/16bsb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '25.4'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '15.88'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '17.22'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '7.9'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '36.5'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '39.7'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '21'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '3.7/3.0'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '500'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '55.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '12635'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '64'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '2.53'],
                    ],
                ],
                [
                    'sku' => 'C2050SB',
                    'display_code' => 'C2050SB',
                    'title_vi' => 'Xích cong C2050SB',
                    'title_en' => 'Side Bow Conveyor Chain C2050SB',
                    'slug_vi' => 'xich-cong-c2050sb',
                    'slug_en' => 'side-bow-conveyor-chain-c2050sb',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'C2050SB là Xích cong một dãy, bước xích 31.75 mm, đường kính con lăn tối đa 10.16 mm, bán kính cong bên tối thiểu 800 mm, lực kéo đứt tối thiểu 21.8 kN và khối lượng khoảng 0.84 kg/m. Xích cong (side bow chain, còn gọi là xích uốn ngang) là xích con lăn có khả năng uốn cong sang hai bên trong mặt phẳng nằm ngang, dùng cho các băng tải phải đi theo đường cong, vòng rẽ mà xích con lăn thường không đáp ứng được. Cặp chốt và ống lót được thiết kế đặc biệt để tạo bán kính cong bên R; bán kính cong của đường ray thiết bị cần lớn hơn hoặc bằng bán kính cong bên tối thiểu R min của xích. Xích dùng trong băng tải có nhiều khúc quanh, máy quay, băng tải vít nâng, thiết bị đóng gói và băng tải thực phẩm, đồ uống. Xích có độ bền cao, bền mỏi, tuổi thọ dài và vận hành ổn định.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều dày má t/T và chiều cao má trong h2 theo bảng thông số.
• Chốt và ống lót: có khe hở thiết kế riêng giữa chốt, ống lót và má để xích uốn được sang bên; đây là bộ phận quyết định bán kính cong bên R.
• Con lăn: ăn khớp với răng đĩa xích, giảm ma sát và mài mòn.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải và môi trường.
• Vị trí hao mòn: ma sát ở khớp chốt – ống lót, và do xích uốn ngang nên mòn ở khớp và mặt bên má xích nhiều hơn xích thông thường.',
                    'standard_vi' => 'Kích thước cơ bản (bước xích, đường kính con lăn, chiều rộng trong, đường kính chốt) tương ứng dòng xích con lăn cùng cỡ (số hiệu 40, 50, 60, 80 theo ANSI; 08B, 10B, 12B, 16B theo ISO). Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Do xích uốn ngang liên tục nên mòn nhiều hơn xích thông thường: cần kiểm tra thường xuyên hơn, bôi trơn đều và giữ xích thẳng hàng với đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới. Nên thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị. Khả năng chịu tải của Xích cong thấp hơn xích con lăn tiêu chuẩn cùng cỡ.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-1.webp', '/images/products/thanhkhang/xich-cong-chong-cong/side-bow-chain-2.webp', '/images/products/thanhkhang/xich-cong-chong-cong/c2050sb-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '31.75'],
                        ['label' => 'Đường kính con lăn d1 max (mm)', 'value' => '10.16'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '9.4'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '5.08'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '21.3'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '22.6'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '15'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2.03'],
                        ['label' => 'Bán kính cong bên R min (mm)', 'value' => '800'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '21.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (lb)', 'value' => '4954'],
                        ['label' => 'Lực kéo đứt trung bình Q0 (kN)', 'value' => '24.1'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.84'],
                    ],
                ],
                [
                    'sku' => 'SPC9.525',
                    'display_code' => 'SPC9.525',
                    'title_vi' => 'Xích chống cong SPC9.525',
                    'title_en' => 'Anti-Side Bow Conveyor Chain SPC9.525',
                    'slug_vi' => 'xich-chong-cong-spc9-525',
                    'slug_en' => 'anti-side-bow-conveyor-chain-spc9-525',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 1,
                    'desc_vi' => 'SPC9.525 là xích chống cong bước xích 9.525 mm, đường kính ống lót tối đa 5.08 mm, đường kính chốt tối đa 3.58 mm, lực kéo đứt tối thiểu 10.8 kN và khối lượng khoảng 0.73 kg/m. Xích chống cong là xích có má dạng đặc biệt, cho phép xích gập theo một phương nhưng giữ cứng theo phương ngang, nên có thể làm việc như thanh đẩy trong cơ cấu đóng mở cửa sổ và các thiết bị tương tự. Xích cho truyền động ổn định, ít ồn; vật liệu có thể là thép hợp kim chất lượng cao hoặc thép không gỉ, bề mặt thường được xử lý chống ăn mòn để làm việc trong môi trường khắc nghiệt.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: dạng tấm có biên dạng đặc biệt, quyết định độ cứng vững theo phương ngang và hành trình đẩy của xích.
• Chốt (và ống lót ở mã SPC9.525): nối các mắt xích, cho phép xích gập theo một phương.
• Vật liệu: thép hợp kim hoặc thép không gỉ; mã SPC12.65 là phiên bản thép không gỉ.
• Bề mặt: thường xử lý chống ăn mòn.
• Vị trí hao mòn: ma sát ở khớp chốt và mặt trượt của má xích.',
                    'standard_vi' => 'Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001; kích thước theo bảng thông số của từng mã.',
                    'maintenance_vi' => 'Kiểm tra định kỳ độ mòn khớp, độ cứng vững của xích và tình trạng ăn mòn; bôi trơn khớp chốt theo điều kiện làm việc. Thay xích khi xích có dấu hiệu mòn, cong vênh hoặc không còn đẩy ổn định.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp', '/images/products/thanhkhang/xich-cong-chong-cong/spc9-525-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '9.525'],
                        ['label' => 'Đường kính ống lót d3 max (mm)', 'value' => '5.08'],
                        ['label' => 'Chiều rộng trong giữa má trong b1 min (mm)', 'value' => '4.68'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.58'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '13.1'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '14.6'],
                        ['label' => 'Chiều cao má H max (mm)', 'value' => '13.1'],
                        ['label' => 'Chiều cao má h (mm)', 'value' => '8.6'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '2/1.3'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min (kN)', 'value' => '10.8'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.73'],
                    ],
                ],
                [
                    'sku' => 'SPC12.65',
                    'display_code' => 'SPC12.65',
                    'title_vi' => 'Xích đẩy cửa sổ SPC12.65 (thép không gỉ)',
                    'title_en' => 'Anti-Side Bow Window Push Chain SPC12.65 (Stainless Steel)',
                    'slug_vi' => 'xich-day-cua-so-spc12-65-thep-khong-gi',
                    'slug_en' => 'anti-side-bow-window-push-chain-spc12-65-stainless-steel',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'SPC12.65 là xích chống cong bằng thép không gỉ, bước xích 12.70 mm, đường kính chốt tối đa 3.9 mm, bề dày má tối đa 1.1 mm và khối lượng khoảng 0.33 kg/m. Xích chống cong là xích có má dạng đặc biệt, cho phép xích gập theo một phương nhưng giữ cứng theo phương ngang, nên có thể làm việc như thanh đẩy trong cơ cấu đóng mở cửa sổ và các thiết bị tương tự. Xích cho truyền động ổn định, ít ồn; vật liệu có thể là thép hợp kim chất lượng cao hoặc thép không gỉ, bề mặt thường được xử lý chống ăn mòn để làm việc trong môi trường khắc nghiệt.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: dạng tấm có biên dạng đặc biệt, quyết định độ cứng vững theo phương ngang và hành trình đẩy của xích.
• Chốt (và ống lót ở mã SPC9.525): nối các mắt xích, cho phép xích gập theo một phương.
• Vật liệu: thép hợp kim hoặc thép không gỉ; mã SPC12.65 là phiên bản thép không gỉ.
• Bề mặt: thường xử lý chống ăn mòn.
• Vị trí hao mòn: ma sát ở khớp chốt và mặt trượt của má xích.',
                    'standard_vi' => 'Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001; kích thước theo bảng thông số của từng mã.',
                    'maintenance_vi' => 'Kiểm tra định kỳ độ mòn khớp, độ cứng vững của xích và tình trạng ăn mòn; bôi trơn khớp chốt theo điều kiện làm việc. Thay xích khi xích có dấu hiệu mòn, cong vênh hoặc không còn đẩy ổn định.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp', '/images/products/thanhkhang/xich-cong-chong-cong/spc12-65-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.9'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '9.8'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.95'],
                        ['label' => 'Kích thước G max (mm)', 'value' => '12.8'],
                        ['label' => 'Kích thước H (mm)', 'value' => '55 ~ 75'],
                        ['label' => 'Kích thước H2 max (mm)', 'value' => '17'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.1'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.33'],
                    ],
                ],
                [
                    'sku' => 'SPC12.65a',
                    'display_code' => 'SPC12.65a',
                    'title_vi' => 'Xích đẩy cửa sổ SPC12.65a',
                    'title_en' => 'Anti-Side Bow Window Push Chain SPC12.65a',
                    'slug_vi' => 'xich-day-cua-so-spc12-65a',
                    'slug_en' => 'anti-side-bow-window-push-chain-spc12-65a',
                    'categories' => ['xich-cong-chong-cong'],
                    'is_featured' => 0,
                    'desc_vi' => 'SPC12.65a là xích chống cong bước xích 12.70 mm, đường kính chốt tối đa 3.9 mm, bề dày má tối đa 1.1 mm và khối lượng khoảng 0.33 kg/m. Xích chống cong là xích có má dạng đặc biệt, cho phép xích gập theo một phương nhưng giữ cứng theo phương ngang, nên có thể làm việc như thanh đẩy trong cơ cấu đóng mở cửa sổ và các thiết bị tương tự. Xích cho truyền động ổn định, ít ồn; vật liệu có thể là thép hợp kim chất lượng cao hoặc thép không gỉ, bề mặt thường được xử lý chống ăn mòn để làm việc trong môi trường khắc nghiệt.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích: dạng tấm có biên dạng đặc biệt, quyết định độ cứng vững theo phương ngang và hành trình đẩy của xích.
• Chốt (và ống lót ở mã SPC9.525): nối các mắt xích, cho phép xích gập theo một phương.
• Vật liệu: thép hợp kim hoặc thép không gỉ; mã SPC12.65 là phiên bản thép không gỉ.
• Bề mặt: thường xử lý chống ăn mòn.
• Vị trí hao mòn: ma sát ở khớp chốt và mặt trượt của má xích.',
                    'standard_vi' => 'Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001; kích thước theo bảng thông số của từng mã.',
                    'maintenance_vi' => 'Kiểm tra định kỳ độ mòn khớp, độ cứng vững của xích và tình trạng ăn mòn; bôi trơn khớp chốt theo điều kiện làm việc. Thay xích khi xích có dấu hiệu mòn, cong vênh hoặc không còn đẩy ổn định.',
                    'image' => '/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp',
                    'images' => ['/images/products/thanhkhang/xich-cong-chong-cong/anti-side-bow-chain.webp', '/images/products/thanhkhang/xich-cong-chong-cong/spc12-65-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1'],
                        ['label' => 'Bước xích P (mm)', 'value' => '12.7'],
                        ['label' => 'Đường kính chốt d2 max (mm)', 'value' => '3.9'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '9.8'],
                        ['label' => 'Chiều cao má trong h2 max (mm)', 'value' => '11.95'],
                        ['label' => 'Kích thước G max (mm)', 'value' => '12.8'],
                        ['label' => 'Kích thước H (mm)', 'value' => '50 ~ 80'],
                        ['label' => 'Kích thước H2 max (mm)', 'value' => '17'],
                        ['label' => 'Bề dày má t/T (mm)', 'value' => '1.1'],
                        ['label' => 'Khối lượng mỗi mét q (kg/m)', 'value' => '0.33'],
                    ],
                ],
                [
                    'sku' => 'M20',
                    'display_code' => 'M20',
                    'title_vi' => 'Xích tải dòng M M20',
                    'title_en' => 'M Series Metric Conveyor Chain M20',
                    'slug_vi' => 'xich-tai-dong-m-m20',
                    'slug_en' => 'm-series-metric-conveyor-chain-m20',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 1,
                    'desc_vi' => 'M20 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 20 kN (một dãy) và 40 kN (hai dãy), chiều cao má 18 mm, bề dày má 2.5 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '40 · 50 · 63 · 80 · 100 · 125 · 160'],
                        ['label' => 'b1 min (mm)', 'value' => '16'],
                        ['label' => 'b2 max (mm)', 'value' => '22'],
                        ['label' => 'b3 min (mm)', 'value' => '22.2'],
                        ['label' => 'b4 max (mm)', 'value' => '15'],
                        ['label' => 'b5 max (mm)', 'value' => '14'],
                        ['label' => 'b6 max (mm)', 'value' => '24.5'],
                        ['label' => 'd1 max (mm)', 'value' => '6'],
                        ['label' => 'd2 min (mm)', 'value' => '6.1'],
                        ['label' => 'd3 max (mm)', 'value' => '9'],
                        ['label' => 'd4 max (mm)', 'value' => '12.5'],
                        ['label' => 'd5 max (mm)', 'value' => '25'],
                        ['label' => 'd6 max (mm)', 'value' => '30'],
                        ['label' => 'd7 max (mm)', 'value' => '7'],
                        ['label' => 'c (mm)', 'value' => '1'],
                        ['label' => 'f (mm)', 'value' => '3'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '18'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '49'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '35'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '2.5'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '1.32'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '20'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '40'],
                    ],
                ],
                [
                    'sku' => 'M28',
                    'display_code' => 'M28',
                    'title_vi' => 'Xích tải dòng M M28',
                    'title_en' => 'M Series Metric Conveyor Chain M28',
                    'slug_vi' => 'xich-tai-dong-m-m28',
                    'slug_en' => 'm-series-metric-conveyor-chain-m28',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M28 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 28 kN (một dãy) và 56 kN (hai dãy), chiều cao má 20 mm, bề dày má 3 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '50 · 63 · 80 · 100 · 125 · 160 · 200'],
                        ['label' => 'b1 min (mm)', 'value' => '18'],
                        ['label' => 'b2 max (mm)', 'value' => '25'],
                        ['label' => 'b3 min (mm)', 'value' => '25.2'],
                        ['label' => 'b4 max (mm)', 'value' => '17'],
                        ['label' => 'b5 max (mm)', 'value' => '16'],
                        ['label' => 'b6 max (mm)', 'value' => '28'],
                        ['label' => 'd1 max (mm)', 'value' => '7'],
                        ['label' => 'd2 min (mm)', 'value' => '7.1'],
                        ['label' => 'd3 max (mm)', 'value' => '10'],
                        ['label' => 'd4 max (mm)', 'value' => '15'],
                        ['label' => 'd5 max (mm)', 'value' => '30'],
                        ['label' => 'd6 max (mm)', 'value' => '36'],
                        ['label' => 'd7 max (mm)', 'value' => '8.5'],
                        ['label' => 'c (mm)', 'value' => '1'],
                        ['label' => 'f (mm)', 'value' => '3.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '20'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '56'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '40'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '3'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '1.75'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '28'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '56'],
                    ],
                ],
                [
                    'sku' => 'M40',
                    'display_code' => 'M40',
                    'title_vi' => 'Xích tải dòng M M40',
                    'title_en' => 'M Series Metric Conveyor Chain M40',
                    'slug_vi' => 'xich-tai-dong-m-m40',
                    'slug_en' => 'm-series-metric-conveyor-chain-m40',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M40 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 40 kN (một dãy) và 80 kN (hai dãy), chiều cao má 25 mm, bề dày má 3.5 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '63 · 80 · 100 · 125 · 160 · 200 · 250'],
                        ['label' => 'b1 min (mm)', 'value' => '20'],
                        ['label' => 'b2 max (mm)', 'value' => '28'],
                        ['label' => 'b3 min (mm)', 'value' => '28.3'],
                        ['label' => 'b4 max (mm)', 'value' => '19'],
                        ['label' => 'b5 max (mm)', 'value' => '18'],
                        ['label' => 'b6 max (mm)', 'value' => '31.5'],
                        ['label' => 'd1 max (mm)', 'value' => '8.5'],
                        ['label' => 'd2 min (mm)', 'value' => '8.6'],
                        ['label' => 'd3 max (mm)', 'value' => '12.5'],
                        ['label' => 'd4 max (mm)', 'value' => '18'],
                        ['label' => 'd5 max (mm)', 'value' => '36'],
                        ['label' => 'd6 max (mm)', 'value' => '42'],
                        ['label' => 'd7 max (mm)', 'value' => '10'],
                        ['label' => 'c (mm)', 'value' => '1'],
                        ['label' => 'f (mm)', 'value' => '3.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '25'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '63'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '45'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '3.5'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '2.38'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '40'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '80'],
                    ],
                ],
                [
                    'sku' => 'M56',
                    'display_code' => 'M56',
                    'title_vi' => 'Xích tải dòng M M56',
                    'title_en' => 'M Series Metric Conveyor Chain M56',
                    'slug_vi' => 'xich-tai-dong-m-m56',
                    'slug_en' => 'm-series-metric-conveyor-chain-m56',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M56 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 56 kN (một dãy) và 112 kN (hai dãy), chiều cao má 30 mm, bề dày má 4 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '63 · 80 · 100 · 125 · 160 · 200 · 250'],
                        ['label' => 'b1 min (mm)', 'value' => '24'],
                        ['label' => 'b2 max (mm)', 'value' => '33'],
                        ['label' => 'b3 min (mm)', 'value' => '33.3'],
                        ['label' => 'b4 max (mm)', 'value' => '23'],
                        ['label' => 'b5 max (mm)', 'value' => '22'],
                        ['label' => 'b6 max (mm)', 'value' => '36'],
                        ['label' => 'd1 max (mm)', 'value' => '10'],
                        ['label' => 'd2 min (mm)', 'value' => '10.1'],
                        ['label' => 'd3 max (mm)', 'value' => '15'],
                        ['label' => 'd4 max (mm)', 'value' => '21'],
                        ['label' => 'd5 max (mm)', 'value' => '42'],
                        ['label' => 'd6 max (mm)', 'value' => '50'],
                        ['label' => 'd7 max (mm)', 'value' => '12'],
                        ['label' => 'c (mm)', 'value' => '1.5'],
                        ['label' => 'f (mm)', 'value' => '4.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '30'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '72'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '52'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '4'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '3.3'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '56'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '112'],
                    ],
                ],
                [
                    'sku' => 'M80',
                    'display_code' => 'M80',
                    'title_vi' => 'Xích tải dòng M M80',
                    'title_en' => 'M Series Metric Conveyor Chain M80',
                    'slug_vi' => 'xich-tai-dong-m-m80',
                    'slug_en' => 'm-series-metric-conveyor-chain-m80',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M80 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 80 kN (một dãy) và 160 kN (hai dãy), chiều cao má 35 mm, bề dày má 5 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '80 · 100 · 125 · 160 · 200 · 250 · 315'],
                        ['label' => 'b1 min (mm)', 'value' => '28'],
                        ['label' => 'b2 max (mm)', 'value' => '39'],
                        ['label' => 'b3 min (mm)', 'value' => '39.4'],
                        ['label' => 'b4 max (mm)', 'value' => '27'],
                        ['label' => 'b5 max (mm)', 'value' => '26'],
                        ['label' => 'b6 max (mm)', 'value' => '43'],
                        ['label' => 'd1 max (mm)', 'value' => '12'],
                        ['label' => 'd2 min (mm)', 'value' => '12.1'],
                        ['label' => 'd3 max (mm)', 'value' => '18'],
                        ['label' => 'd4 max (mm)', 'value' => '25'],
                        ['label' => 'd5 max (mm)', 'value' => '50'],
                        ['label' => 'd6 max (mm)', 'value' => '60'],
                        ['label' => 'd7 max (mm)', 'value' => '15'],
                        ['label' => 'c (mm)', 'value' => '2'],
                        ['label' => 'f (mm)', 'value' => '5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '35'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '86'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '62'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '5'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '4.68'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '80'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '160'],
                    ],
                ],
                [
                    'sku' => 'M112',
                    'display_code' => 'M112',
                    'title_vi' => 'Xích tải dòng M M112',
                    'title_en' => 'M Series Metric Conveyor Chain M112',
                    'slug_vi' => 'xich-tai-dong-m-m112',
                    'slug_en' => 'm-series-metric-conveyor-chain-m112',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M112 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 112 kN (một dãy) và 224 kN (hai dãy), chiều cao má 40 mm, bề dày má 6 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '80 · 100 · 125 · 160 · 200 · 250 · 315'],
                        ['label' => 'b1 min (mm)', 'value' => '32'],
                        ['label' => 'b2 max (mm)', 'value' => '45'],
                        ['label' => 'b3 min (mm)', 'value' => '45.5'],
                        ['label' => 'b4 max (mm)', 'value' => '31'],
                        ['label' => 'b5 max (mm)', 'value' => '29'],
                        ['label' => 'b6 max (mm)', 'value' => '50.5'],
                        ['label' => 'd1 max (mm)', 'value' => '15'],
                        ['label' => 'd2 min (mm)', 'value' => '15.1'],
                        ['label' => 'd3 max (mm)', 'value' => '21'],
                        ['label' => 'd4 max (mm)', 'value' => '30'],
                        ['label' => 'd5 max (mm)', 'value' => '60'],
                        ['label' => 'd6 max (mm)', 'value' => '70'],
                        ['label' => 'd7 max (mm)', 'value' => '18'],
                        ['label' => 'c (mm)', 'value' => '2.5'],
                        ['label' => 'f (mm)', 'value' => '5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '101'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '73'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '6'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '6.75'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '112'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '224'],
                    ],
                ],
                [
                    'sku' => 'M224',
                    'display_code' => 'M224',
                    'title_vi' => 'Xích tải dòng M M224',
                    'title_en' => 'M Series Metric Conveyor Chain M224',
                    'slug_vi' => 'xich-tai-dong-m-m224',
                    'slug_en' => 'm-series-metric-conveyor-chain-m224',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M224 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 224 kN (một dãy) và 448 kN (hai dãy), chiều cao má 60 mm, bề dày má 8 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '125 · 160 · 200 · 250 · 315 · 400 · 500'],
                        ['label' => 'b1 min (mm)', 'value' => '43'],
                        ['label' => 'b2 max (mm)', 'value' => '60'],
                        ['label' => 'b3 min (mm)', 'value' => '60.6'],
                        ['label' => 'b4 max (mm)', 'value' => '42'],
                        ['label' => 'b5 max (mm)', 'value' => '40'],
                        ['label' => 'b6 max (mm)', 'value' => '67'],
                        ['label' => 'd1 max (mm)', 'value' => '21'],
                        ['label' => 'd2 min (mm)', 'value' => '21.2'],
                        ['label' => 'd3 max (mm)', 'value' => '30'],
                        ['label' => 'd4 max (mm)', 'value' => '42'],
                        ['label' => 'd5 max (mm)', 'value' => '85'],
                        ['label' => 'd6 max (mm)', 'value' => '100'],
                        ['label' => 'd7 max (mm)', 'value' => '25'],
                        ['label' => 'c (mm)', 'value' => '3'],
                        ['label' => 'f (mm)', 'value' => '7'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '60'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '134'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '98'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '8'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '12.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '224'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '448'],
                    ],
                ],
                [
                    'sku' => 'M315',
                    'display_code' => 'M315',
                    'title_vi' => 'Xích tải dòng M M315',
                    'title_en' => 'M Series Metric Conveyor Chain M315',
                    'slug_vi' => 'xich-tai-dong-m-m315',
                    'slug_en' => 'm-series-metric-conveyor-chain-m315',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M315 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 315 kN (một dãy) và 630 kN (hai dãy), chiều cao má 70 mm, bề dày má 10 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '160 · 200 · 250 · 315 · 400 · 500 · 630'],
                        ['label' => 'b1 min (mm)', 'value' => '48'],
                        ['label' => 'b2 max (mm)', 'value' => '70'],
                        ['label' => 'b3 min (mm)', 'value' => '70.7'],
                        ['label' => 'b4 max (mm)', 'value' => '47'],
                        ['label' => 'b5 max (mm)', 'value' => '45'],
                        ['label' => 'b6 max (mm)', 'value' => '77'],
                        ['label' => 'd1 max (mm)', 'value' => '25'],
                        ['label' => 'd2 min (mm)', 'value' => '25.2'],
                        ['label' => 'd3 max (mm)', 'value' => '36'],
                        ['label' => 'd4 max (mm)', 'value' => '50'],
                        ['label' => 'd5 max (mm)', 'value' => '100'],
                        ['label' => 'd6 max (mm)', 'value' => '120'],
                        ['label' => 'd7 max (mm)', 'value' => '30'],
                        ['label' => 'c (mm)', 'value' => '3'],
                        ['label' => 'f (mm)', 'value' => '7.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '70'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '154'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '112'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '10'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '17.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '315'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '630'],
                    ],
                ],
                [
                    'sku' => 'M450',
                    'display_code' => 'M450',
                    'title_vi' => 'Xích tải dòng M M450',
                    'title_en' => 'M Series Metric Conveyor Chain M450',
                    'slug_vi' => 'xich-tai-dong-m-m450',
                    'slug_en' => 'm-series-metric-conveyor-chain-m450',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M450 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 450 kN (một dãy) và 900 kN (hai dãy), chiều cao má 80 mm, bề dày má 12 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '200 · 250 · 315 · 400 · 500 · 630 · 800'],
                        ['label' => 'b1 min (mm)', 'value' => '56'],
                        ['label' => 'b2 max (mm)', 'value' => '82'],
                        ['label' => 'b3 min (mm)', 'value' => '82.8'],
                        ['label' => 'b4 max (mm)', 'value' => '55'],
                        ['label' => 'b5 max (mm)', 'value' => '51'],
                        ['label' => 'b6 max (mm)', 'value' => '92.5'],
                        ['label' => 'd1 max (mm)', 'value' => '30'],
                        ['label' => 'd2 min (mm)', 'value' => '30.2'],
                        ['label' => 'd3 max (mm)', 'value' => '42'],
                        ['label' => 'd4 max (mm)', 'value' => '60'],
                        ['label' => 'd5 max (mm)', 'value' => '120'],
                        ['label' => 'd6 max (mm)', 'value' => '140'],
                        ['label' => 'd7 max (mm)', 'value' => '35'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '8'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '80'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '185'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '135'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '12'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '24.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '450'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '900'],
                    ],
                ],
                [
                    'sku' => 'M630',
                    'display_code' => 'M630',
                    'title_vi' => 'Xích tải dòng M M630',
                    'title_en' => 'M Series Metric Conveyor Chain M630',
                    'slug_vi' => 'xich-tai-dong-m-m630',
                    'slug_en' => 'm-series-metric-conveyor-chain-m630',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M630 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 630 kN (một dãy) và 1260 kN (hai dãy), chiều cao má 100 mm, bề dày má 14 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '250 · 315 · 400 · 500 · 630 · 800 · 1000'],
                        ['label' => 'b1 min (mm)', 'value' => '66'],
                        ['label' => 'b2 max (mm)', 'value' => '96'],
                        ['label' => 'b3 min (mm)', 'value' => '97'],
                        ['label' => 'b4 max (mm)', 'value' => '65'],
                        ['label' => 'b5 max (mm)', 'value' => '61'],
                        ['label' => 'b6 max (mm)', 'value' => '107'],
                        ['label' => 'd1 max (mm)', 'value' => '36'],
                        ['label' => 'd2 min (mm)', 'value' => '36.2'],
                        ['label' => 'd3 max (mm)', 'value' => '50'],
                        ['label' => 'd4 max (mm)', 'value' => '70'],
                        ['label' => 'd5 max (mm)', 'value' => '140'],
                        ['label' => 'd6 max (mm)', 'value' => '170'],
                        ['label' => 'd7 max (mm)', 'value' => '42'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '11'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '100'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '214'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '154'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '14'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '34.56'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '630'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '1260'],
                    ],
                ],
                [
                    'sku' => 'M900',
                    'display_code' => 'M900',
                    'title_vi' => 'Xích tải dòng M M900',
                    'title_en' => 'M Series Metric Conveyor Chain M900',
                    'slug_vi' => 'xich-tai-dong-m-m900',
                    'slug_en' => 'm-series-metric-conveyor-chain-m900',
                    'categories' => ['xich-m-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'M900 là xích tải hệ mét dòng M, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 900 kN (một dãy) và 1800 kN (hai dãy), chiều cao má 120 mm, bề dày má 16 mm. Xích tải dòng M là xích con lăn hoặc xích ống lót bước hệ mét, kích thước theo tiêu chuẩn ISO và DIN, dùng cho thiết bị vận chuyển cơ giới hóa và bốc xếp trong các ngành luyện kim, khai thác mỏ, đường, rượu bia, gốm sứ và giấy. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng M gồm kiểu chốt đặc (M), kiểu chốt rỗng (MC) và kiểu má cao có gắn phụ kiện (MT).',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (F, G); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn ISO và DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/m1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m2-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m3-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/m-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '250 · 315 · 400 · 500 · 630 · 800 · 1000'],
                        ['label' => 'b1 min (mm)', 'value' => '78'],
                        ['label' => 'b2 max (mm)', 'value' => '112'],
                        ['label' => 'b3 min (mm)', 'value' => '113'],
                        ['label' => 'b4 max (mm)', 'value' => '76'],
                        ['label' => 'b5 max (mm)', 'value' => '70'],
                        ['label' => 'b6 max (mm)', 'value' => '127'],
                        ['label' => 'd1 max (mm)', 'value' => '44'],
                        ['label' => 'd2 min (mm)', 'value' => '44.2'],
                        ['label' => 'd3 max (mm)', 'value' => '60'],
                        ['label' => 'd4 max (mm)', 'value' => '85'],
                        ['label' => 'd5 max (mm)', 'value' => '170'],
                        ['label' => 'd6 max (mm)', 'value' => '210'],
                        ['label' => 'd7 max (mm)', 'value' => '50'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '13.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '120'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '254'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '180'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '16'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '49.28'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '900'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '1800'],
                    ],
                ],
                [
                    'sku' => 'FV40',
                    'display_code' => 'FV40',
                    'title_vi' => 'Xích tải dòng FV FV40',
                    'title_en' => 'FV Series Metric Conveyor Chain FV40',
                    'slug_vi' => 'xich-tai-dong-fv-fv40',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv40',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 1,
                    'desc_vi' => 'FV40 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 40 kN (một dãy) và 80 kN (hai dãy), chiều cao má 26 mm, bề dày má 3 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '40 · 63 · 100'],
                        ['label' => 'b1 min (mm)', 'value' => '18'],
                        ['label' => 'b2 max (mm)', 'value' => '24.5'],
                        ['label' => 'b3 min (mm)', 'value' => '25'],
                        ['label' => 'b4 max (mm)', 'value' => '17'],
                        ['label' => 'b5 max (mm)', 'value' => '16'],
                        ['label' => 'b6 max (mm)', 'value' => '22'],
                        ['label' => 'd1 max (mm)', 'value' => '10'],
                        ['label' => 'd2 min (mm)', 'value' => '10.1'],
                        ['label' => 'd3 max (mm)', 'value' => '15'],
                        ['label' => 'd4 max (mm)', 'value' => '20'],
                        ['label' => 'd5 max (mm)', 'value' => '32'],
                        ['label' => 'd6 max (mm)', 'value' => '40'],
                        ['label' => 'd7 max (mm)', 'value' => '48'],
                        ['label' => 'd8 max (mm)', 'value' => '15'],
                        ['label' => 'c (mm)', 'value' => '1'],
                        ['label' => 'f (mm)', 'value' => '3'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '26'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '44'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '37'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '3'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '2.5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '40'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '80'],
                    ],
                ],
                [
                    'sku' => 'FV63',
                    'display_code' => 'FV63',
                    'title_vi' => 'Xích tải dòng FV FV63',
                    'title_en' => 'FV Series Metric Conveyor Chain FV63',
                    'slug_vi' => 'xich-tai-dong-fv-fv63',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv63',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV63 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 63 kN (một dãy) và 126 kN (hai dãy), chiều cao má 30 mm, bề dày má 4 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '63 · 100 · 125 · 160'],
                        ['label' => 'b1 min (mm)', 'value' => '22'],
                        ['label' => 'b2 max (mm)', 'value' => '30.5'],
                        ['label' => 'b3 min (mm)', 'value' => '31'],
                        ['label' => 'b4 max (mm)', 'value' => '21'],
                        ['label' => 'b5 max (mm)', 'value' => '20'],
                        ['label' => 'b6 max (mm)', 'value' => '27.5'],
                        ['label' => 'd1 max (mm)', 'value' => '12'],
                        ['label' => 'd2 min (mm)', 'value' => '12.1'],
                        ['label' => 'd3 max (mm)', 'value' => '18'],
                        ['label' => 'd4 max (mm)', 'value' => '26'],
                        ['label' => 'd5 max (mm)', 'value' => '40'],
                        ['label' => 'd6 max (mm)', 'value' => '50'],
                        ['label' => 'd7 max (mm)', 'value' => '60'],
                        ['label' => 'd8 max (mm)', 'value' => '18'],
                        ['label' => 'c (mm)', 'value' => '1.5'],
                        ['label' => 'f (mm)', 'value' => '3.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '30'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '55'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '46'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '4'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '3.7'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '63'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '126'],
                    ],
                ],
                [
                    'sku' => 'FV90',
                    'display_code' => 'FV90',
                    'title_vi' => 'Xích tải dòng FV FV90',
                    'title_en' => 'FV Series Metric Conveyor Chain FV90',
                    'slug_vi' => 'xich-tai-dong-fv-fv90',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv90',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV90 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 90 kN (một dãy) và 180 kN (hai dãy), chiều cao má 35 mm, bề dày má 5 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '63 · 100 · 125 · 160 · 200 · 250'],
                        ['label' => 'b1 min (mm)', 'value' => '25'],
                        ['label' => 'b2 max (mm)', 'value' => '35.5'],
                        ['label' => 'b3 min (mm)', 'value' => '36'],
                        ['label' => 'b4 max (mm)', 'value' => '24'],
                        ['label' => 'b5 max (mm)', 'value' => '23'],
                        ['label' => 'b6 max (mm)', 'value' => '31'],
                        ['label' => 'd1 max (mm)', 'value' => '14'],
                        ['label' => 'd2 min (mm)', 'value' => '14.1'],
                        ['label' => 'd3 max (mm)', 'value' => '20'],
                        ['label' => 'd4 max (mm)', 'value' => '30'],
                        ['label' => 'd5 max (mm)', 'value' => '48'],
                        ['label' => 'd6 max (mm)', 'value' => '63'],
                        ['label' => 'd7 max (mm)', 'value' => '73'],
                        ['label' => 'd8 max (mm)', 'value' => '20'],
                        ['label' => 'c (mm)', 'value' => '2'],
                        ['label' => 'f (mm)', 'value' => '4.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '35'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '62'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '53'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '5'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '5'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '90'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '180'],
                    ],
                ],
                [
                    'sku' => 'FV112',
                    'display_code' => 'FV112',
                    'title_vi' => 'Xích tải dòng FV FV112',
                    'title_en' => 'FV Series Metric Conveyor Chain FV112',
                    'slug_vi' => 'xich-tai-dong-fv-fv112',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv112',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV112 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 112 kN (một dãy) và 224 kN (hai dãy), chiều cao má 40 mm, bề dày má 6 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '100 · 125 · 160 · 200 · 250'],
                        ['label' => 'b1 min (mm)', 'value' => '30'],
                        ['label' => 'b2 max (mm)', 'value' => '42.5'],
                        ['label' => 'b3 min (mm)', 'value' => '43'],
                        ['label' => 'b4 max (mm)', 'value' => '29'],
                        ['label' => 'b5 max (mm)', 'value' => '28'],
                        ['label' => 'b6 max (mm)', 'value' => '36'],
                        ['label' => 'd1 max (mm)', 'value' => '16'],
                        ['label' => 'd2 min (mm)', 'value' => '16.1'],
                        ['label' => 'd3 max (mm)', 'value' => '22'],
                        ['label' => 'd4 max (mm)', 'value' => '32'],
                        ['label' => 'd5 max (mm)', 'value' => '55'],
                        ['label' => 'd6 max (mm)', 'value' => '72'],
                        ['label' => 'd7 max (mm)', 'value' => '87'],
                        ['label' => 'd8 max (mm)', 'value' => '22'],
                        ['label' => 'c (mm)', 'value' => '2.5'],
                        ['label' => 'f (mm)', 'value' => '5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '40'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '72'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '63'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '6'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '6.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '112'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '224'],
                    ],
                ],
                [
                    'sku' => 'FV140',
                    'display_code' => 'FV140',
                    'title_vi' => 'Xích tải dòng FV FV140',
                    'title_en' => 'FV Series Metric Conveyor Chain FV140',
                    'slug_vi' => 'xich-tai-dong-fv-fv140',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv140',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV140 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 140 kN (một dãy) và 280 kN (hai dãy), chiều cao má 45 mm, bề dày má 6 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '100 · 125 · 160 · 200 · 250 · 315'],
                        ['label' => 'b1 min (mm)', 'value' => '35'],
                        ['label' => 'b2 max (mm)', 'value' => '47.5'],
                        ['label' => 'b3 min (mm)', 'value' => '48'],
                        ['label' => 'b4 max (mm)', 'value' => '34'],
                        ['label' => 'b5 max (mm)', 'value' => '32'],
                        ['label' => 'b6 max (mm)', 'value' => '40'],
                        ['label' => 'd1 max (mm)', 'value' => '18'],
                        ['label' => 'd2 min (mm)', 'value' => '18.1'],
                        ['label' => 'd3 max (mm)', 'value' => '26'],
                        ['label' => 'd4 max (mm)', 'value' => '36'],
                        ['label' => 'd5 max (mm)', 'value' => '60'],
                        ['label' => 'd6 max (mm)', 'value' => '80'],
                        ['label' => 'd7 max (mm)', 'value' => '95'],
                        ['label' => 'd8 max (mm)', 'value' => '26'],
                        ['label' => 'c (mm)', 'value' => '3'],
                        ['label' => 'f (mm)', 'value' => '6'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '45'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '80'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '68'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '6'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '8.6'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '140'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '280'],
                    ],
                ],
                [
                    'sku' => 'FV180',
                    'display_code' => 'FV180',
                    'title_vi' => 'Xích tải dòng FV FV180',
                    'title_en' => 'FV Series Metric Conveyor Chain FV180',
                    'slug_vi' => 'xich-tai-dong-fv-fv180',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv180',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV180 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 180 kN (một dãy) và 360 kN (hai dãy), chiều cao má 50 mm, bề dày má 8 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '125 · 160 · 200 · 250 · 315 · 400'],
                        ['label' => 'b1 min (mm)', 'value' => '45'],
                        ['label' => 'b2 max (mm)', 'value' => '61.5'],
                        ['label' => 'b3 min (mm)', 'value' => '62.5'],
                        ['label' => 'b4 max (mm)', 'value' => '44'],
                        ['label' => 'b5 max (mm)', 'value' => '42'],
                        ['label' => 'b6 max (mm)', 'value' => '50'],
                        ['label' => 'd1 max (mm)', 'value' => '20'],
                        ['label' => 'd2 min (mm)', 'value' => '20.2'],
                        ['label' => 'd3 max (mm)', 'value' => '30'],
                        ['label' => 'd4 max (mm)', 'value' => '42'],
                        ['label' => 'd5 max (mm)', 'value' => '70'],
                        ['label' => 'd6 max (mm)', 'value' => '100'],
                        ['label' => 'd7 max (mm)', 'value' => '120'],
                        ['label' => 'd8 max (mm)', 'value' => '30'],
                        ['label' => 'c (mm)', 'value' => '3'],
                        ['label' => 'f (mm)', 'value' => '10'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '50'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '100'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '86'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '8'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '12.3'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '180'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '360'],
                    ],
                ],
                [
                    'sku' => 'FV250',
                    'display_code' => 'FV250',
                    'title_vi' => 'Xích tải dòng FV FV250',
                    'title_en' => 'FV Series Metric Conveyor Chain FV250',
                    'slug_vi' => 'xich-tai-dong-fv-fv250',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv250',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV250 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 250 kN (một dãy) và 500 kN (hai dãy), chiều cao má 60 mm, bề dày má 8 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '125 · 160 · 200 · 250 · 315 · 400'],
                        ['label' => 'b1 min (mm)', 'value' => '55'],
                        ['label' => 'b2 max (mm)', 'value' => '72'],
                        ['label' => 'b3 min (mm)', 'value' => '73'],
                        ['label' => 'b4 max (mm)', 'value' => '54'],
                        ['label' => 'b5 max (mm)', 'value' => '50'],
                        ['label' => 'b6 max (mm)', 'value' => '57'],
                        ['label' => 'd1 max (mm)', 'value' => '26'],
                        ['label' => 'd2 min (mm)', 'value' => '26.2'],
                        ['label' => 'd3 max (mm)', 'value' => '36'],
                        ['label' => 'd4 max (mm)', 'value' => '50'],
                        ['label' => 'd5 max (mm)', 'value' => '80'],
                        ['label' => 'd6 max (mm)', 'value' => '125'],
                        ['label' => 'd7 max (mm)', 'value' => '145'],
                        ['label' => 'd8 max (mm)', 'value' => '36'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '11.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '60'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '114'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '98'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '8'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '18.7'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '250'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '500'],
                    ],
                ],
                [
                    'sku' => 'FV315',
                    'display_code' => 'FV315',
                    'title_vi' => 'Xích tải dòng FV FV315',
                    'title_en' => 'FV Series Metric Conveyor Chain FV315',
                    'slug_vi' => 'xich-tai-dong-fv-fv315',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv315',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV315 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 315 kN (một dãy) và 630 kN (hai dãy), chiều cao má 70 mm, bề dày má 10 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '160 · 200 · 250 · 315 · 400'],
                        ['label' => 'b1 min (mm)', 'value' => '65'],
                        ['label' => 'b2 max (mm)', 'value' => '86'],
                        ['label' => 'b3 min (mm)', 'value' => '87'],
                        ['label' => 'b4 max (mm)', 'value' => '64'],
                        ['label' => 'b5 max (mm)', 'value' => '60'],
                        ['label' => 'b6 max (mm)', 'value' => '66.5'],
                        ['label' => 'd1 max (mm)', 'value' => '30'],
                        ['label' => 'd2 min (mm)', 'value' => '30.2'],
                        ['label' => 'd3 max (mm)', 'value' => '42'],
                        ['label' => 'd4 max (mm)', 'value' => '60'],
                        ['label' => 'd5 max (mm)', 'value' => '90'],
                        ['label' => 'd6 max (mm)', 'value' => '140'],
                        ['label' => 'd7 max (mm)', 'value' => '170'],
                        ['label' => 'd8 max (mm)', 'value' => '42'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '14.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '70'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '133'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '117'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '10'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '25.8'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '315'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '630'],
                    ],
                ],
                [
                    'sku' => 'FV400',
                    'display_code' => 'FV400',
                    'title_vi' => 'Xích tải dòng FV FV400',
                    'title_en' => 'FV Series Metric Conveyor Chain FV400',
                    'slug_vi' => 'xich-tai-dong-fv-fv400',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv400',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV400 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 400 kN (một dãy) và 800 kN (hai dãy), chiều cao má 70 mm, bề dày má 12 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '160 · 200 · 250 · 315 · 400'],
                        ['label' => 'b1 min (mm)', 'value' => '70'],
                        ['label' => 'b2 max (mm)', 'value' => '96'],
                        ['label' => 'b3 min (mm)', 'value' => '97'],
                        ['label' => 'b4 max (mm)', 'value' => '68'],
                        ['label' => 'b5 max (mm)', 'value' => '64'],
                        ['label' => 'b6 max (mm)', 'value' => '75.5'],
                        ['label' => 'd1 max (mm)', 'value' => '32'],
                        ['label' => 'd2 min (mm)', 'value' => '32.2'],
                        ['label' => 'd3 max (mm)', 'value' => '44'],
                        ['label' => 'd4 max (mm)', 'value' => '60'],
                        ['label' => 'd5 max (mm)', 'value' => '100'],
                        ['label' => 'd6 max (mm)', 'value' => '150'],
                        ['label' => 'd7 max (mm)', 'value' => '185'],
                        ['label' => 'd8 max (mm)', 'value' => '44'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '16.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '70'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '151'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '131'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '12'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '30.7'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '400'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '800'],
                    ],
                ],
                [
                    'sku' => 'FV500',
                    'display_code' => 'FV500',
                    'title_vi' => 'Xích tải dòng FV FV500',
                    'title_en' => 'FV Series Metric Conveyor Chain FV500',
                    'slug_vi' => 'xich-tai-dong-fv-fv500',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv500',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV500 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 500 kN (một dãy) và 1000 kN (hai dãy), chiều cao má 80 mm, bề dày má 12 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '160 · 200 · 250 · 315 · 400 · 500'],
                        ['label' => 'b1 min (mm)', 'value' => '80'],
                        ['label' => 'b2 max (mm)', 'value' => '106'],
                        ['label' => 'b3 min (mm)', 'value' => '107'],
                        ['label' => 'b4 max (mm)', 'value' => '78'],
                        ['label' => 'b5 max (mm)', 'value' => '72'],
                        ['label' => 'b6 max (mm)', 'value' => '80.5'],
                        ['label' => 'd1 max (mm)', 'value' => '36'],
                        ['label' => 'd2 min (mm)', 'value' => '36.2'],
                        ['label' => 'd3 max (mm)', 'value' => '50'],
                        ['label' => 'd4 max (mm)', 'value' => '70'],
                        ['label' => 'd5 max (mm)', 'value' => '110'],
                        ['label' => 'd6 max (mm)', 'value' => '160'],
                        ['label' => 'd7 max (mm)', 'value' => '195'],
                        ['label' => 'd8 max (mm)', 'value' => '50'],
                        ['label' => 'c (mm)', 'value' => '3.5'],
                        ['label' => 'f (mm)', 'value' => '17.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '80'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '161'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '141'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '12'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '38.2'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '500'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '1000'],
                    ],
                ],
                [
                    'sku' => 'FV630',
                    'display_code' => 'FV630',
                    'title_vi' => 'Xích tải dòng FV FV630',
                    'title_en' => 'FV Series Metric Conveyor Chain FV630',
                    'slug_vi' => 'xich-tai-dong-fv-fv630',
                    'slug_en' => 'fv-series-metric-conveyor-chain-fv630',
                    'categories' => ['xich-fv-series'],
                    'is_featured' => 0,
                    'desc_vi' => 'FV630 là xích tải dòng FV, kiểu một dãy hoặc hai dãy, lực kéo đứt tối thiểu 630 kN (một dãy) và 1260 kN (hai dãy), chiều cao má 100 mm, bề dày má 12 mm. Xích tải dòng FV là xích con lăn hoặc xích ống lót bước nguyên hệ mét, theo tiêu chuẩn DIN, chủ yếu dùng trong máy vận chuyển nhiều loại vật liệu. Xích có thể chế tạo ở dạng một dãy hoặc hai dãy, kèm nhiều kiểu con lăn (không con lăn, con lăn nhỏ, con lăn lớn, con lăn có gờ) và phụ kiện phù hợp điều kiện làm việc. Dòng FV gồm kiểu chốt đặc (FV), kiểu có má gắn phụ kiện, kiểu FVT con lăn lớn với má cao và kiểu FVC chốt rỗng.',
                    'structure_vi' => 'Xích gồm các bộ phận chính:
• Má xích (má trong và má ngoài): tạo thân xích và chịu lực kéo; chiều cao má h2, bề dày T.
• Chốt: nối các mắt xích và cho phép xích gập khi ăn khớp với đĩa xích; chiều dài chốt L và Lc ghi ở các cột kích thước. Có kiểu chốt đặc và kiểu chốt rỗng để lắp trục phụ kiện.
• Ống lót (xích ống lót) hoặc ống lót kèm con lăn (xích con lăn): chịu ma sát với chốt và ăn khớp với răng đĩa xích.
• Con lăn (tùy chọn): kiểu không con lăn, con lăn nhỏ (A), con lăn lớn (B, C) hoặc con lăn có gờ (D, E); đường kính con lăn ghi ở các cột d4 đến d7.
• Phụ kiện: lắp theo yêu cầu để nâng, đẩy hoặc mang vật liệu.
• Vật liệu và xử lý nhiệt: chốt, ống lót và con lăn được nhiệt luyện để đạt độ cứng và chống mòn; vật liệu chọn theo yêu cầu chịu tải của từng cỡ.
• Vị trí hao mòn: ma sát chủ yếu ở khớp chốt – ống lót, mòn làm xích giãn bước.',
                    'standard_vi' => 'Kích thước theo tiêu chuẩn DIN cho xích tải hệ mét; lực kéo đứt tối thiểu Q ghi theo kN cho xích một dãy và hai dãy. Xích được sản xuất trong hệ thống quản lý chất lượng ISO 9001.',
                    'maintenance_vi' => 'Bôi trơn đều đặn, giữ xích sạch và kiểm tra độ mòn, độ giãn bước định kỳ. Kiểm tra thêm tình trạng con lăn, phụ kiện và độ căn chỉnh của đĩa xích, ray dẫn. Khi đo bước, chọn đoạn xích thẳng, không xoắn, đo từ tâm chốt này đến tâm chốt kế tiếp rồi so với thông số xích mới; thay xích khi độ giãn bước vượt giới hạn cho phép của nhà thiết kế thiết bị.',
                    'image' => '/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp',
                    'images' => ['/images/products/thanhkhang/xich-m-fv-series/fv-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv1-drawing-vi.webp', '/images/products/thanhkhang/xich-m-fv-series/fv-tay-ga-drawing-vi.webp'],
                    'specs' => [
                        ['label' => 'Số dãy', 'value' => '1 hoặc 2 dãy'],
                        ['label' => 'Các bước xích P có sẵn (mm)', 'value' => '200 · 250 · 315 · 400 · 500'],
                        ['label' => 'b1 min (mm)', 'value' => '90'],
                        ['label' => 'b2 max (mm)', 'value' => '116'],
                        ['label' => 'b3 min (mm)', 'value' => '117'],
                        ['label' => 'b4 max (mm)', 'value' => '88'],
                        ['label' => 'b5 max (mm)', 'value' => '80'],
                        ['label' => 'b6 max (mm)', 'value' => '86.5'],
                        ['label' => 'd1 max (mm)', 'value' => '42'],
                        ['label' => 'd2 min (mm)', 'value' => '42.2'],
                        ['label' => 'd3 max (mm)', 'value' => '56'],
                        ['label' => 'd4 max (mm)', 'value' => '80'],
                        ['label' => 'd5 max (mm)', 'value' => '120'],
                        ['label' => 'd6 max (mm)', 'value' => '170'],
                        ['label' => 'd7 max (mm)', 'value' => '210'],
                        ['label' => 'd8 max (mm)', 'value' => '56'],
                        ['label' => 'c (mm)', 'value' => '4.5'],
                        ['label' => 'f (mm)', 'value' => '17.5'],
                        ['label' => 'Chiều cao má h2 (mm)', 'value' => '100'],
                        ['label' => 'Chiều dài chốt Lc max (mm)', 'value' => '173'],
                        ['label' => 'Chiều dài chốt L max (mm)', 'value' => '153'],
                        ['label' => 'Bề dày má T (mm)', 'value' => '12'],
                        ['label' => 'Diện tích chịu lực S mỗi hàng (cm²)', 'value' => '48.7'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 1 hàng (kN)', 'value' => '630'],
                        ['label' => 'Lực kéo đứt tối thiểu Q min – 2 hàng (kN)', 'value' => '1260'],
                    ],
                ],
            ];

            $hasSkuCol = Schema::hasColumn('products', 'sku');
            $hasMaterialCol = Schema::hasColumn('products', 'material');
            $hasLeadTimeCol = Schema::hasColumn('products', 'lead_time');
            $hasSpecsCol = Schema::hasColumn('products', 'specifications');
            $hasPdpMetaCol = Schema::hasColumn('products', 'pdp_metadata');
            $hasPriceCol = Schema::hasColumn('products', 'price');
            $hasComparePriceCol = Schema::hasColumn('products', 'compare_at_price');

            $productIdsByCategory = [];

            foreach ($productsData as $index => $item) {
                $specsWithCode = array_merge(
                    [['label' => 'Mã sản phẩm', 'value' => $item['display_code']]],
                    $item['specs']
                );

                $contentVi = '<h3>Mô tả tổng quan</h3><p>' . nl2br(htmlspecialchars($item['desc_vi'])) . '</p>'
                    . '<h3>Cấu tạo &amp; Vật liệu</h3><p>' . nl2br(htmlspecialchars($item['structure_vi'])) . '</p>'
                    . '<h3>Tiêu chuẩn &amp; Chất lượng</h3><p>' . nl2br(htmlspecialchars($item['standard_vi'])) . '</p>'
                    . '<h3>Tuổi thọ, Bảo trì &amp; Thay thế</h3><p>' . nl2br(htmlspecialchars($item['maintenance_vi'])) . '</p>'
                    . '<h3>Thông số kỹ thuật chi tiết</h3><ul>';
                foreach ($specsWithCode as $sp) {
                    $contentVi .= '<li><strong>' . htmlspecialchars($sp['label']) . ':</strong> ' . htmlspecialchars($sp['value']) . '</li>';
                }
                $contentVi .= '</ul>';

                $contentEn = '<h3>Overview</h3><p>' . htmlspecialchars($item['title_en']) . ' (' . htmlspecialchars($item['display_code']) . ')</p>'
                    . '<h3>Technical Specifications</h3><ul>';
                foreach ($specsWithCode as $sp) {
                    $contentEn .= '<li><strong>' . htmlspecialchars($sp['label']) . ':</strong> ' . htmlspecialchars($sp['value']) . '</li>';
                }
                $contentEn .= '</ul>';

                $imageJson = json_encode([
                    'path' => $item['image'],
                    'alt' => $item['title_vi'],
                ], JSON_UNESCAPED_UNICODE);

                $imagesJson = json_encode(array_map(function ($path) use ($item) {
                    return ['path' => $path, 'alt' => $item['title_vi']];
                }, $item['images']), JSON_UNESCAPED_UNICODE);

                $payload = [
                    'status' => 'ACTIVE',
                    'is_featured' => $item['is_featured'],
                    'is_new' => 0,
                    'is_stock' => 1,
                    'position' => $index + 1,
                    'image' => $imageJson,
                    'images' => $imagesJson,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ];

                if ($hasSkuCol) {
                    $payload['sku'] = $item['sku'];
                }
                if ($hasPriceCol) {
                    $payload['price'] = null;
                }
                if ($hasComparePriceCol) {
                    $payload['compare_at_price'] = null;
                }
                if ($hasMaterialCol) {
                    $payload['material'] = 'Thép hợp kim / Thép carbon nhiệt luyện (Universal Chains)';
                }
                if ($hasLeadTimeCol) {
                    $payload['lead_time'] = 'Liên hệ đặt hàng & báo giá B2B';
                }
                if ($hasSpecsCol) {
                    $payload['specifications'] = json_encode($specsWithCode, JSON_UNESCAPED_UNICODE);
                }
                if ($hasPdpMetaCol) {
                    $payload['pdp_metadata'] = json_encode([
                        'story_title' => $item['title_vi'],
                        'story_intro' => $item['desc_vi'],
                        'feature1_title' => 'Cấu tạo & Vật liệu',
                        'feature1_desc' => $item['structure_vi'],
                        'feature2_title' => 'Tiêu chuẩn & Bảo trì',
                        'feature2_desc' => $item['standard_vi'] . ' ' . $item['maintenance_vi'],
                    ], JSON_UNESCAPED_UNICODE);
                }

                $existing = null;
                if ($hasSkuCol) {
                    $existing = DB::table('products')->where('sku', $item['sku'])->first();
                }
                if (!$existing) {
                    $trans = DB::table('product_translations')
                        ->where('slug', $item['slug_vi'])
                        ->orWhere('slug', $item['slug_en'])
                        ->first();
                    if ($trans) {
                        $existing = DB::table('products')->where('id', $trans->product_id)->first();
                    }
                }

                if ($existing) {
                    $productId = $existing->id;
                    DB::table('products')->where('id', $productId)->update($payload);
                } else {
                    $payload['created_at'] = $now;
                    $payload['view_count'] = 0;
                    $productId = DB::table('products')->insertGetId($payload);
                }

                DB::table('product_translations')->updateOrInsert(
                    ['product_id' => $productId, 'locale' => 'vi'],
                    [
                        'title' => $item['title_vi'],
                        'slug' => $item['slug_vi'],
                        'seo_slug' => $item['slug_vi'],
                        'description' => $item['desc_vi'],
                        'content' => $contentVi,
                        'seo_meta_title' => $item['title_vi'] . ' - Thành Khang Tecsat',
                        'seo_meta_description' => mb_substr($item['desc_vi'], 0, 160),
                    ]
                );

                DB::table('product_translations')->updateOrInsert(
                    ['product_id' => $productId, 'locale' => 'en'],
                    [
                        'title' => $item['title_en'],
                        'slug' => $item['slug_en'],
                        'seo_slug' => $item['slug_en'],
                        'description' => $item['title_en'] . ' (' . $item['display_code'] . ') - Universal Chains industrial component distributed by Thanh Khang Tecsat.',
                        'content' => $contentEn,
                        'seo_meta_title' => $item['title_en'] . ' - Thanh Khang Tecsat',
                        'seo_meta_description' => $item['title_en'] . ' (' . $item['display_code'] . ') distributed by Thanh Khang Tecsat.',
                    ]
                );

                foreach ($item['categories'] as $catKey) {
                    $catId = $catMap[$catKey] ?? null;
                    if ($catId) {
                        DB::table('product_ref_categories')->updateOrInsert(
                            ['product_id' => $productId, 'product_category_id' => $catId],
                            ['product_id' => $productId, 'product_category_id' => $catId]
                        );
                        $productIdsByCategory[$catKey][] = $productId;
                    }
                }
            }

            // Link up to 4 sibling related products per product
            foreach ($productsData as $item) {
                $primaryCat = $item['categories'][0];
                $trans = DB::table('product_translations')
                    ->where('slug', $item['slug_vi'])
                    ->where('locale', 'vi')
                    ->first();
                if (!$trans) {
                    continue;
                }
                $productId = $trans->product_id;
                $siblings = array_values(array_filter(
                    $productIdsByCategory[$primaryCat] ?? [],
                    fn ($id) => $id !== $productId
                ));
                foreach (array_slice($siblings, 0, 4) as $relId) {
                    DB::table('related_products')->updateOrInsert(
                        ['product_id' => $productId, 'related_product_id' => $relId],
                        ['product_id' => $productId, 'related_product_id' => $relId]
                    );
                }
            }
        });

        ProductCategory::clearCategoryCaches();
        Cache::flush();
    }

    protected function upsertCategory(int $parentId, int $position, array $vi, array $en, $now): int
    {
        $existingTrans = DB::table('product_category_translations')
            ->where('slug', $vi['slug'])
            ->orWhere('slug', $en['slug'])
            ->first();

        if ($existingTrans) {
            $catId = $existingTrans->product_category_id;
            DB::table('product_categories')->where('id', $catId)->update([
                'parent_id' => $parentId,
                'status' => 'ACTIVE',
                'position' => $position,
                'updated_at' => $now,
                'deleted_at' => null,
            ]);
        } else {
            $catId = DB::table('product_categories')->insertGetId([
                'parent_id' => $parentId,
                'status' => 'ACTIVE',
                'position' => $position,
                'view_count' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        DB::table('product_category_translations')->updateOrInsert(
            ['product_category_id' => $catId, 'locale' => 'vi'],
            [
                'title' => $vi['title'],
                'slug' => $vi['slug'],
                'seo_slug' => $vi['slug'],
                'description' => $vi['desc'],
                'seo_meta_title' => $vi['title'] . ' - Thành Khang Tecsat',
                'seo_meta_description' => $vi['desc'],
            ]
        );

        DB::table('product_category_translations')->updateOrInsert(
            ['product_category_id' => $catId, 'locale' => 'en'],
            [
                'title' => $en['title'],
                'slug' => $en['slug'],
                'seo_slug' => $en['slug'],
                'description' => $en['desc'],
                'seo_meta_title' => $en['title'] . ' - Thanh Khang Tecsat',
                'seo_meta_description' => $en['desc'],
            ]
        );

        return $catId;
    }
}
