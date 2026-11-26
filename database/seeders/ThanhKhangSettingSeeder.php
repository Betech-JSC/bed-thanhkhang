<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Config;

class ThanhKhangSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe, idempotent seeder using updateOrInsert for B2B Thanh Khang Tecsat settings.
     *
     * @return void
     */
    public function run()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $now = now();

        $businessSectors = [
            [
                'id' => 1,
                'title_vi' => 'Linh kiện & Phụ tùng Thang cuốn - Thang máy',
                'title_en' => 'Escalator & Elevator Spare Parts',
                'description_vi' => 'Cung cấp toàn diện tay vịn cao su/kháng khuẩn, xích bậc thang cuốn tải nặng, bánh xe, con lăn dẫn hướng và phụ tùng cơ khí thang cuốn chính hãng.',
                'description_en' => 'Comprehensive supply of rubber/antimicrobial handrails, heavy-duty escalator step chains, rollers, guide wheels, and OEM mechanical components.',
            ],
            [
                'id' => 2,
                'title_vi' => 'Hệ thống Băng tải & Xích tải Công nghiệp',
                'title_en' => 'Industrial Conveyor & Chain Systems',
                'description_vi' => 'Xích tải hành lý sân bay, hệ thống logistics kho vận thông minh, xích kẹp màng dây chuyền đóng gói, xích tấm inox và xích con lăn chịu lực cao.',
                'description_en' => 'Airport baggage handling chains, smart warehousing logistics systems, packaging line gripper chains, stainless tabletop and heavy roller chains.',
            ],
            [
                'id' => 3,
                'title_vi' => 'Giải pháp Tiết kiệm Điện & Quạt trần Công nghiệp BLDC',
                'title_en' => 'Industrial Energy-Saving Solutions & BLDC HVLS Fans',
                'description_vi' => 'Tư vấn và lắp đặt hệ thống quạt trần công nghiệp HVLS động cơ BLDC siêu tiết kiệm điện năng cho nhà xưởng, kho bãi và trung tâm thương mại.',
                'description_en' => 'Consulting and installation of energy-efficient BLDC motor HVLS industrial ceiling fans for manufacturing plants, warehouses, and commercial spaces.',
            ],
        ];

        $stepsVi = [
            [
                'number' => '01',
                'title' => 'Tiếp nhận yêu cầu',
                'title_en' => 'Technical Intake',
                'subtitle' => 'Tiếp nhận thông số & mã phụ tùng',
                'subtitle_en' => 'Part Number & Specs Survey',
                'description' => 'Khách hàng gửi mã phụ tùng, bản vẽ kỹ thuật hoặc thông số chi tiết cần thay thế. Kỹ sư Thành Khang Tecsat tiếp nhận và phân tích yêu cầu kỹ thuật.',
                'description_en' => 'Clients provide part numbers, technical drawings, or physical dimensions. Our engineers review and verify technical specifications.',
                'client_prep' => 'Cung cấp mã phụ tùng, thông số kỹ thuật (bước xích, tải trọng, kích thước) hoặc hình ảnh linh kiện thực tế.',
                'client_prep_en' => 'Provide part numbers, dimensional drawings, pitch/tensile specs, or photos of existing components.',
                'clientPrep' => 'Cung cấp mã phụ tùng, thông số kỹ thuật (bước xích, tải trọng, kích thước) hoặc hình ảnh linh kiện thực tế.',
                'maker_action' => 'Kỹ sư kỹ thuật Thành Khang đối chiếu bản vẽ tiêu chuẩn công nghiệp, kiểm tra khả năng lắp ghép và tư vấn giải pháp tối ưu.',
                'maker_action_en' => 'Thanh Khang engineers cross-check industrial standards, verify dimensional fitment, and advise optimal part specifications.',
                'makerAction' => 'Kỹ sư kỹ thuật Thành Khang đối chiếu bản vẽ tiêu chuẩn công nghiệp, kiểm tra khả năng lắp ghép và tư vấn giải pháp tối ưu.',
                'deliverables' => 'Bản xác nhận thông số kỹ thuật & giải pháp phụ tùng phù hợp',
                'deliverables_en' => 'Technical verification datasheet & optimal part selection',
                'quality_standard' => 'Phản hồi trong 2-4 giờ làm việc, chính xác 100% theo tiêu chuẩn lắp ráp',
                'quality_standard_en' => 'Response within 2-4 hours, 100% dimensional accuracy',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Báo giá kỹ thuật',
                'title_en' => 'Technical RFQ Quotation',
                'subtitle' => 'Báo giá minh bạch & tiến độ cung ứng',
                'subtitle_en' => 'Transparent Pricing & Lead Time',
                'description' => 'Thống nhất chủng loại vật liệu, tiêu chuẩn cơ khí và phương án giao nhận trước khi triển khai đơn hàng.',
                'description_en' => 'Align on metallurgy, industrial standards, and logistics options prior to commercial confirmation.',
                'client_prep' => 'Xác nhận số lượng đặt hàng, địa điểm công trình/nhà máy nhận hàng và yêu cầu hồ sơ CO/CQ nếu có.',
                'client_prep_en' => 'Confirm required quantities, delivery site, and compliance documentation requirements (CO/CQ).',
                'clientPrep' => 'Xác nhận số lượng đặt hàng, địa điểm công trình/nhà máy nhận hàng và yêu cầu hồ sơ CO/CQ nếu có.',
                'maker_action' => 'Lập bảng báo giá chi tiết, dự toán chi phí tối ưu theo số lượng B2B và cam kết tiến độ giao hàng chuẩn xác.',
                'maker_action_en' => 'Prepare detailed commercial quotation, tiered volume pricing, and committed delivery schedules.',
                'makerAction' => 'Lập bảng báo giá chi tiết, dự toán chi phí tối ưu theo số lượng B2B và cam kết tiến độ giao hàng chuẩn xác.',
                'deliverables' => 'Bảng báo giá RFQ chính thức & tiến độ cung ứng dự kiến',
                'deliverables_en' => 'Official B2B quotation & committed fulfillment schedule',
                'quality_standard' => 'Giá gốc nhà máy cạnh tranh nhất, minh bạch chi phí vận chuyển & thuế VAT',
                'quality_standard_en' => 'Competitive factory pricing, transparent freight & VAT terms',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Ký kết hợp đồng',
                'title_en' => 'Contract & Order Confirmation',
                'subtitle' => 'Xác nhận đơn hàng & bảo lãnh cung ứng',
                'subtitle_en' => 'Formal Agreement & Procurement',
                'description' => 'Hoàn thiện thủ tục hợp đồng kinh tế, cam kết pháp lý về tiêu chuẩn chất lượng sản phẩm và bảo hành.',
                'description_en' => 'Finalize commercial sales contract with strict legal warranties on quality and delivery milestones.',
                'client_prep' => 'Cung cấp thông tin pháp nhân công ty, ký kết hợp đồng mua bán và thực hiện tạm ứng theo thỏa thuận.',
                'client_prep_en' => 'Provide legal corporate profile, execute purchase contract, and process initial deposit.',
                'clientPrep' => 'Cung cấp thông tin pháp nhân công ty, ký kết hợp đồng mua bán và thực hiện tạm ứng theo thỏa thuận.',
                'maker_action' => 'Phát hành hợp đồng mua bán B2B, lên lệnh xuất kho hoặc điều phối sản xuất theo đúng thông số đã chốt.',
                'maker_action_en' => 'Issue sales agreement, place warehouse dispatch orders or initiate specialized manufacturing.',
                'makerAction' => 'Phát hành hợp đồng mua bán B2B, lên lệnh xuất kho hoặc điều phối sản xuất theo đúng thông số đã chốt.',
                'deliverables' => 'Hợp đồng kinh tế B2B & kế hoạch giao hàng chi tiết',
                'deliverables_en' => 'Executed purchase contract & dispatch milestone schedule',
                'quality_standard' => 'Hợp đồng pháp lý rõ ràng, bảo vệ tối đa quyền lợi khách hàng doanh nghiệp',
                'quality_standard_en' => 'Transparent legal terms safeguarding customer interests',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Kiểm định & Đóng gói',
                'title_en' => 'QC & Industrial Packaging',
                'subtitle' => 'Kiểm định QC nghiêm ngặt & đóng kiện công nghiệp',
                'subtitle_en' => 'Rigorous Inspection & Industrial Crating',
                'description' => 'Toàn bộ linh kiện được kiểm tra kích thước, tải trọng thử nghiệm, tra dầu bảo quản và đóng thùng kiện gỗ an toàn.',
                'description_en' => 'All components undergo dimensional verification, proof-load testing, anti-rust oiling, and heavy-duty wooden crating.',
                'client_prep' => 'Sắp xếp nhân sự hoặc kỹ thuật viên tiếp nhận hàng hóa tại công trình/nhà xưởng.',
                'client_prep_en' => 'Arrange receiving personnel or engineering staff at the job site or warehouse.',
                'clientPrep' => 'Sắp xếp nhân sự hoặc kỹ thuật viên tiếp nhận hàng hóa tại công trình/nhà xưởng.',
                'maker_action' => 'Kiểm tra chất lượng (QC) từng chi tiết, đóng gói chống sốc chống ẩm và hoàn thiện bộ chứng chỉ chất lượng CO/CQ.',
                'maker_action_en' => 'Perform QC checks, pack with moisture and shock protection, and prepare CO/CQ certificates.',
                'makerAction' => 'Kiểm tra chất lượng (QC) từng chi tiết, đóng gói chống sốc chống ẩm và hoàn thiện bộ chứng chỉ chất lượng CO/CQ.',
                'deliverables' => 'Kiện hàng nguyên đai nguyên kiện & bộ hồ sơ CO/CQ xuất xưởng',
                'deliverables_en' => 'Factory-sealed crates & complete certification dossier',
                'quality_standard' => 'Đạt 100% tiêu chuẩn xuất xưởng, bảo vệ hoàn hảo trong vận chuyển',
                'quality_standard_en' => '100% factory inspection pass rate, zero transit damage',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Bàn giao & Bảo hành',
                'title_en' => 'Handover & Warranty Support',
                'subtitle' => 'Giao nhận tại chân công trình & hỗ trợ kỹ thuật',
                'subtitle_en' => 'Site Delivery & Technical Support',
                'description' => 'Bàn giao vật tư tại hiện trường, hướng dẫn lắp đặt căn chỉnh và kích hoạt chính sách bảo hành kỹ thuật chính hãng.',
                'description_en' => 'Site delivery, alignment and installation guidance, and activation of genuine technical warranty.',
                'client_prep' => 'Kiểm tra niêm phong, đối chiếu danh mục hàng hóa theo phiếu xuất kho và ký biên bản giao nhận.',
                'client_prep_en' => 'Inspect seals, verify packing list against delivery docket, and sign handover acceptance report.',
                'clientPrep' => 'Kiểm tra niêm phong, đối chiếu danh mục hàng hóa theo phiếu xuất kho và ký biên bản giao nhận.',
                'maker_action' => 'Giao hàng đúng hẹn, hướng dẫn kỹ thuật bảo trì định kỳ và sẵn sàng hỗ trợ kỹ thuật 24/7 khi vận hành.',
                'maker_action_en' => 'Deliver on schedule, provide preventive maintenance instructions, and deliver 24/7 engineering support.',
                'makerAction' => 'Giao hàng đúng hẹn, hướng dẫn kỹ thuật bảo trì định kỳ và sẵn sàng hỗ trợ kỹ thuật 24/7 khi vận hành.',
                'deliverables' => 'Biên bản nghiệm thu bàn giao & phiếu bảo hành chính hãng',
                'deliverables_en' => 'Acceptance handover protocol & official warranty certificate',
                'quality_standard' => 'Bảo hành 12 - 24 tháng chính hãng, hỗ trợ kỹ thuật trọn vòng đời sản phẩm',
                'quality_standard_en' => '12 to 24-month warranty, lifetime technical advisory',
                'isLast' => true,
            ],
        ];

        $stepsEn = array_map(function ($step) {
            return [
                'number' => $step['number'],
                'title' => $step['title_en'],
                'subtitle' => $step['subtitle_en'],
                'description' => $step['description_en'],
                'client_prep' => $step['client_prep_en'],
                'clientPrep' => $step['client_prep_en'],
                'maker_action' => $step['maker_action_en'],
                'makerAction' => $step['maker_action_en'],
                'deliverables' => $step['deliverables_en'],
                'quality_standard' => $step['quality_standard_en'],
                'isLast' => $step['isLast'],
            ];
        }, $stepsVi);

        $generalSettings = [
            'general_business_name' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
            'general_business_name_vi' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
            'general_business_name_en' => 'Thanh Khang Tecsat Technical Services Co., Ltd',
            'company_name' => 'Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat',
            'company_name_en' => 'Thanh Khang Tecsat Technical Services Co., Ltd',

            'general_site_name' => 'Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn',
            'general_site_name_vi' => 'Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn',
            'general_site_name_en' => 'Thanh Khang Tecsat - Industrial Equipment & Escalator Spare Parts',
            'site_name' => 'Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn',
            'site_name_en' => 'Thanh Khang Tecsat - Industrial Equipment & Escalator Spare Parts',

            'tax_code' => '107621881',
            'general_tax_code' => '107621881',

            'general_phone' => '0967732665',
            'phone' => '0967732665',
            'hotline' => '0967732665',

            'general_email' => 'info@dvktthanhkhang.vn',
            'email' => 'info@dvktthanhkhang.vn',

            'general_zalo' => 'https://zalo.me/0967732665',
            'zalo' => 'https://zalo.me/0967732665',
            'social_zalo' => 'https://zalo.me/0967732665',

            'general_address' => 'Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội',
            'general_address_vi' => 'Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội',
            'general_address_en' => '25th Floor, TNR Tower, 54A Nguyen Chi Thanh, Lang Thuong Ward, Dong Da District, Hanoi',
            'address' => 'Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội',
            'address_en' => '25th Floor, TNR Tower, 54A Nguyen Chi Thanh, Lang Thuong Ward, Dong Da District, Hanoi',
            'headquarters_address' => 'Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội',
            'headquarters_address_en' => '25th Floor, TNR Tower, 54A Nguyen Chi Thanh, Lang Thuong Ward, Dong Da District, Hanoi',

            'showroom_address' => 'NTT23, Khu Đô Thị Thống Nhất Complex, 82 Nguyễn Tuân, Phường Thanh Xuân Trung, Quận Thanh Xuân, Hà Nội',
            'showroom_address_en' => 'NTT23, Thong Nhat Complex, 82 Nguyen Tuan, Thanh Xuan Trung Ward, Thanh Xuan District, Hanoi',
            'office_address' => 'NTT23, Khu Đô Thị Thống Nhất Complex, 82 Nguyễn Tuân, Phường Thanh Xuân Trung, Quận Thanh Xuân, Hà Nội',
            'office_address_en' => 'NTT23, Thong Nhat Complex, 82 Nguyen Tuan, Thanh Xuan Trung Ward, Thanh Xuan District, Hanoi',

            'general_working_hours' => '08:00 - 17:30 (T2 - T6)',
            'general_working_hours_vi' => '08:00 - 17:30 (T2 - T6)',
            'general_working_hours_en' => '08:00 - 17:30 (Mon - Sat)',
            'working_hours' => '08:00 - 17:30 (T2 - T6)',
            'working_hours_en' => '08:00 - 17:30 (Mon - Sat)',

            'general_facebook' => 'https://www.facebook.com/thanhkhangtecsat',
            'facebook' => 'https://www.facebook.com/thanhkhangtecsat',
            'social_facebook' => 'https://www.facebook.com/thanhkhangtecsat',

            'process_steps' => json_encode($stepsVi, JSON_UNESCAPED_UNICODE),
            'process_steps_en' => json_encode($stepsEn, JSON_UNESCAPED_UNICODE),
            'steps' => json_encode($stepsVi, JSON_UNESCAPED_UNICODE),
            'steps_en' => json_encode($stepsEn, JSON_UNESCAPED_UNICODE),

            'business_sectors' => json_encode($businessSectors, JSON_UNESCAPED_UNICODE),
        ];

        $logoDefaults = [
            'general_logo' => '/assets/images/logo/logo-thanhkhang.png',
            'logo' => '/assets/images/logo/logo-thanhkhang.png',
        ];

        foreach ($logoDefaults as $logoKey => $defaultLogoPath) {
            $existingLogo = DB::table('settings')
                ->where('group', 'general')
                ->where('name', $logoKey)
                ->first();

            if (!$existingLogo || trim((string) ($existingLogo->val ?? '')) === '') {
                DB::table('settings')->updateOrInsert(
                    ['group' => 'general', 'name' => $logoKey],
                    [
                        'val' => $defaultLogoPath,
                        'updated_at' => $now,
                        'created_at' => $existingLogo->created_at ?? $now,
                    ]
                );
            }
        }

        foreach ($generalSettings as $name => $val) {
            $existing = DB::table('settings')
                ->where('group', 'general')
                ->where('name', $name)
                ->first();

            if (!$existing || trim((string) ($existing->val ?? '')) === '') {
                DB::table('settings')->updateOrInsert(
                    ['group' => 'general', 'name' => $name],
                    [
                        'val' => $val,
                        'updated_at' => $now,
                        'created_at' => $existing->created_at ?? $now,
                    ]
                );
            }
        }

        // Seed / Update 6 B2B Technical FAQs into posts & post_translations
        $faqs = [
            [
                'position' => 1,
                'vi' => [
                    'question' => 'Tôi chưa có mã phụ tùng hoặc bản vẽ chính xác, Thành Khang Tecsat có hỗ trợ khảo sát tại hiện trường không?',
                    'answer' => 'Có. Đội ngũ kỹ sư cơ khí của Thành Khang Tecsat sẵn sàng hỗ trợ khảo sát thực tế tại công trình/nhà xưởng, đo đạc bước xích, kích thước con lăn, tải trọng vận hành và đối chiếu bảng mã tương thích từ các hãng sản xuất gốc để tư vấn đúng linh kiện thay thế.',
                ],
                'en' => [
                    'question' => 'I do not have the exact part number or drawing yet, does Thanh Khang Tecsat provide on-site technical surveys?',
                    'answer' => 'Yes. Our mechanical engineering team provides on-site facility visits to inspect operating equipment, measure chain pitch, roller diameters, and dynamic loads, cross-referencing OEM catalogues to recommend the exact replacement part.',
                ],
            ],
            [
                'position' => 2,
                'vi' => [
                    'question' => 'Để nhận được bảng báo giá kỹ thuật (RFQ) nhanh và chính xác nhất, tôi cần cung cấp những thông tin gì?',
                    'answer' => 'Quý khách vui lòng cung cấp mã phụ tùng (Part Number), bản vẽ kỹ thuật (nếu có), bước xích, tải trọng làm việc, số lượng cần đặt và địa điểm giao hàng. Kỹ sư Thành Khang Tecsat sẽ phản hồi báo giá RFQ chi tiết trong vòng 2 - 4 giờ làm việc.',
                ],
                'en' => [
                    'question' => 'What information should I provide to get the fastest and most accurate RFQ quotation?',
                    'answer' => 'Please provide part numbers (OEM SKU), dimensional drawings if available, chain pitch, working tensile loads, required quantities, and destination site. Our engineers will reply with a detailed technical quotation within 2 to 4 business hours.',
                ],
            ],
            [
                'position' => 3,
                'vi' => [
                    'question' => 'Thành Khang Tecsat có cung cấp phụ tùng thang cuốn, xích tải theo quy cách phi tiêu chuẩn hoặc custom không?',
                    'answer' => 'Hoàn toàn có! Bên cạnh các quy chuẩn ISO, DIN, ANSI tiêu chuẩn, chúng tôi nhận gia công xích tải gắn tai gá đặc thù, xích lá chịu tải cực lớn, đĩa xích phi tiêu chuẩn và con lăn bọc Polyurethane theo bản vẽ kỹ thuật riêng của khách hàng.',
                ],
                'en' => [
                    'question' => 'Does Thanh Khang Tecsat supply non-standard or custom attachment chains and sprockets?',
                    'answer' => 'Absolutely! Beyond standard ISO, DIN, and ANSI specifications, we supply specialized attachment conveyor chains, heavy-duty leaf chains, custom-bored sprockets, and cast Polyurethane rollers tailored to bespoke engineering drawings.',
                ],
            ],
            [
                'position' => 4,
                'vi' => [
                    'question' => 'Thời gian giao hàng và tiến độ cung ứng phụ tùng thường mất bao lâu?',
                    'answer' => 'Đối với các mã hàng có sẵn tại kho Hà Nội, chúng tôi giao ngay trong ngày (nội thành Hà Nội) hoặc từ 24 - 48 giờ trên toàn quốc. Đối với vật tư đặc chủng nhập khẩu hoặc gia công theo đơn, tiến độ từ 7 - 25 ngày làm việc với lịch trình cam kết rõ ràng.',
                ],
                'en' => [
                    'question' => 'What is the standard delivery timeline for industrial components and spare parts?',
                    'answer' => 'In-stock inventory at our Hanoi warehouse is dispatched same-day within Hanoi and within 24 to 48 hours nationwide. Custom-machined or specialized imported items are fulfilled within 7 to 25 business days with milestone tracking.',
                ],
            ],
            [
                'position' => 5,
                'vi' => [
                    'question' => 'Sản phẩm do Thành Khang Tecsat cung ứng có đầy đủ chứng chỉ CO/CQ và hóa đơn VAT không?',
                    'answer' => '100% sản phẩm cung ứng cho khách hàng B2B đều có đầy đủ chứng nhận nguồn gốc xuất xứ (CO), chứng nhận chất lượng xuất xưởng (CQ), hóa đơn giá trị gia tăng (VAT) hợp lệ và biên bản nghiệm thu bàn giao.',
                ],
                'en' => [
                    'question' => 'Do products supplied by Thanh Khang Tecsat include complete CO/CQ certifications and VAT invoices?',
                    'answer' => '100% of our B2B commercial shipments include official Certificates of Origin (CO), factory Certificates of Quality (CQ), electronic VAT invoices, and formal acceptance handover documentation.',
                ],
            ],
            [
                'position' => 6,
                'vi' => [
                    'question' => 'Chính sách bảo hành và hỗ trợ kỹ thuật sau bàn giao như thế nào?',
                    'answer' => 'Toàn bộ linh kiện cơ khí, xích tải và quạt trần công nghiệp BLDC được bảo hành chính hãng từ 12 đến 24 tháng. Đội ngũ kỹ sư Thành Khang Tecsat luôn sẵn sàng hỗ trợ kỹ thuật 24/7, tư vấn căn chỉnh dung sai và bảo trì định kỳ trọn đời sản phẩm.',
                ],
                'en' => [
                    'question' => 'What is the warranty policy and technical support after delivery?',
                    'answer' => 'All mechanical components, conveyor chains, and BLDC industrial HVLS fans carry official 12 to 24-month manufacturer warranties. Thanh Khang Tecsat provides round-the-clock 24/7 technical advisory, alignment guidance, and preventive maintenance support.',
                ],
            ],
        ];

        foreach ($faqs as $faqItem) {
            $position = $faqItem['position'];
            $post = \App\Models\Post\Post::firstOrCreate(
                [
                    'type' => \App\Models\Post\Post::TYPE_FAQ,
                    'position' => $position,
                ],
                [
                    'status' => \App\Models\Post\Post::STATUS_ACTIVE,
                    'published_at' => $now,
                    'is_home' => true,
                    'is_featured' => false,
                ]
            );

            foreach (['vi', 'en'] as $faqLocale) {
                $existingFaqTrans = DB::table('post_translations')
                    ->where('post_id', $post->id)
                    ->where('locale', $faqLocale)
                    ->first();

                if (!$existingFaqTrans || trim((string) ($existingFaqTrans->title ?? '')) === '') {
                    DB::table('post_translations')->updateOrInsert(
                        [
                            'post_id' => $post->id,
                            'locale' => $faqLocale,
                        ],
                        [
                            'title' => $faqItem[$faqLocale]['question'],
                            'slug' => \Illuminate\Support\Str::slug('faq-b2b-' . $position . '-' . $faqItem[$faqLocale]['question']),
                            'description' => $faqItem[$faqLocale]['answer'],
                            'content' => $faqItem[$faqLocale]['answer'],
                        ]
                    );
                }
            }
        }

        Config::clearBrandCaches();
    }
}
