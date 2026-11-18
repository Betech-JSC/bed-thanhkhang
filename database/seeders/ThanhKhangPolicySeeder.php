<?php

namespace Database\Seeders;

use App\Models\Policy\Policy;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ThanhKhangPolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Safe, idempotent seeder using updateOrCreate for 6 B2B Policies (VI / EN).
     *
     * @return void
     */
    public function run()
    {
        error_reporting(E_ALL & ~E_DEPRECATED);

        $policies = [
            [
                'position' => 1,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách bán hàng & Báo giá B2B',
                    'slug' => 'chinh-sach-ban-hang',
                    'seo_slug' => 'chinh-sach-ban-hang',
                    'seo_meta_title' => 'Chính sách bán hàng & Báo giá B2B - Thành Khang Tecsat',
                    'seo_meta_description' => 'Chính sách bán hàng, quy trình tiếp nhận RFQ, báo giá theo khối lượng dự án và hợp đồng cung ứng phụ tùng công nghiệp tại Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Phạm vi cung ứng & Đối tượng phục vụ</h3>
                        <p>Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat chuyên cung ứng vật tư, linh kiện cơ khí chính xác cho thang cuốn, thang nâng ô tô tự động, hệ thống băng tải hành lý sân bay, dây chuyền đóng gói công nghiệp và các giải pháp quạt trần công nghiệp BLDC tiết kiệm điện. Khách hàng mục tiêu của chúng tôi bao gồm các ban quản lý tòa nhà thương mại, nhà thầu cơ điện (M&E), cảng hàng không, nhà máy sản xuất và trung tâm logistics trên toàn quốc.</p>
                        
                        <h3>2. Quy trình báo giá kỹ thuật (RFQ)</h3>
                        <p>Mọi yêu cầu báo giá được tiếp nhận qua Hotline/Zalo: <strong>0967732665</strong> hoặc Email: <strong>info@dvktthanhkhang.vn</strong>. Đội ngũ kỹ sư Thành Khang cam kết phản hồi báo giá chi tiết trong vòng 2 – 4 giờ làm việc kể từ khi nhận đủ thông số kỹ thuật (mã phụ tùng, bản vẽ, bước xích, tải trọng đứt, điều kiện môi trường làm việc).</p>

                        <h3>3. Hiệu lực báo giá & Chính sách giá theo số lượng</h3>
                        <p>Bảng báo giá RFQ có hiệu lực trong vòng 30 ngày làm việc. Thành Khang Tecsat áp dụng chính sách chiết khấu bậc thang hấp dẫn cho các đơn hàng mua sỉ, đơn hàng dự án quy mô lớn và các hợp đồng bảo trì định kỳ dài hạn.</p>

                        <h3>4. Hợp đồng kinh tế & Hóa đơn chứng từ</h3>
                        <p>100% đơn hàng B2B đều được xác lập bằng hợp đồng kinh tế hoặc đơn đặt hàng (PO) hợp pháp. Chúng tôi cung cấp đầy đủ hóa đơn GTGT (VAT 8% hoặc 10% theo quy định hiện hành) và bộ chứng chỉ xuất xưởng, chứng nhận nguồn gốc xuất xứ (CO) và chứng nhận chất lượng (CQ) của nhà sản xuất.</p>
                    ',
                ],
                'en' => [
                    'title' => 'B2B Sales & Quotation Policy',
                    'slug' => 'sales-policy',
                    'seo_slug' => 'sales-policy',
                    'seo_meta_title' => 'B2B Sales & Quotation Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => 'Official B2B sales policies, RFQ intake guidelines, volume pricing tiers, and commercial supply contracts at Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. Scope of Supply & Target Customers</h3>
                        <p>Thanh Khang Tecsat Technical Services Co., Ltd specializes in supplying precision mechanical parts and engineered components for escalators, automated car parking lifts, airport baggage handling systems, industrial packaging lines, and energy-saving BLDC HVLS ceiling fans. We proudly serve commercial facility managers, M&E contractors, airport authorities, industrial plants, and logistics hubs nationwide.</p>
                        
                        <h3>2. Technical RFQ Quotation Procedure</h3>
                        <p>All quotation requests submitted via Hotline/Zalo (+84 967 732 665) or Email (info@dvktthanhkhang.vn) receive detailed technical responses within 2 to 4 business hours once part numbers, engineering drawings, chain pitch, and operating conditions are verified.</p>

                        <h3>3. Quotation Validity & Tiered Volume Pricing</h3>
                        <p>Official quotations are valid for 30 business days. We provide competitive tiered discounts for wholesale purchases, infrastructure project packages, and annual framework maintenance agreements.</p>

                        <h3>4. Commercial Contracts & Documentation</h3>
                        <p>100% of B2B transactions are formalized via commercial contracts or formal Purchase Orders (PO). Deliveries are accompanied by full Value Added Tax (VAT) invoices and complete Certificates of Origin (CO) and Certificates of Quality (CQ).</p>
                    ',
                ],
            ],
            [
                'position' => 2,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách thanh toán B2B',
                    'slug' => 'chinh-sach-thanh-toan',
                    'seo_slug' => 'chinh-sach-thanh-toan',
                    'seo_meta_title' => 'Chính sách thanh toán B2B - Thành Khang Tecsat',
                    'seo_meta_description' => 'Hình thức thanh toán chuyển khoản doanh nghiệp, điều khoản đặt cọc và hạn mức công nợ B2B tại Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Hình thức thanh toán</h3>
                        <p>Thành Khang Tecsat chấp nhận thanh toán qua tài khoản ngân hàng chính thức của công ty. Toàn bộ thông tin chuyển khoản được ghi rõ trên hợp đồng kinh tế và hóa đơn điện tử.</p>

                        <h3>2. Tiến độ thanh toán tiêu chuẩn cho đơn hàng thương mại</h3>
                        <ul>
                            <li><strong>Đợt 1 (Tạm ứng):</strong> Thanh toán 30% - 50% giá trị hợp đồng ngay sau khi hai bên ký kết hợp đồng kinh tế hoặc xác nhận PO.</li>
                            <li><strong>Đợt 2 (Tất toán):</strong> Thanh toán 50% - 70% giá trị còn lại ngay khi nhận thông báo giao hàng và trước khi bàn giao vật tư tại kho/công trình.</li>
                        </ul>

                        <h3>3. Chính sách công nợ & Hạn mức tín dụng cho đối tác chiến lược</h3>
                        <p>Đối với các đối tác nhà thầu M&E, ban quản lý tòa nhà, sân bay và khách hàng thân thiết có lịch sử giao dịch uy tín, Thành Khang Tecsat hỗ trợ chính sách công nợ linh hoạt từ 15 đến 30 ngày kể từ ngày ký biên bản bàn giao và xuất hóa đơn VAT.</p>
                    ',
                ],
                'en' => [
                    'title' => 'B2B Payment Policy',
                    'slug' => 'payment-policy',
                    'seo_slug' => 'payment-policy',
                    'seo_meta_title' => 'B2B Payment Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => 'Corporate bank transfer methods, advance deposit terms, and flexible credit payment terms for strategic B2B partners at Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. Payment Methods</h3>
                        <p>Thanh Khang Tecsat conducts transactions exclusively via official corporate bank accounts. Banking details are explicitly documented in commercial agreements and official electronic VAT invoices.</p>

                        <h3>2. Standard Payment Milestones</h3>
                        <ul>
                            <li><strong>Milestone 1 (Advance Deposit):</strong> 30% to 50% of the total contract value upon contract execution or PO confirmation.</li>
                            <li><strong>Milestone 2 (Balance Settlement):</strong> 50% to 70% upon delivery dispatch notice or prior to physical site handover.</li>
                        </ul>

                        <h3>3. Credit Terms for Strategic Corporate Partners</h3>
                        <p>For verified long-term corporate partners, M&E contractors, and airport facilities with strong credit histories, we offer flexible 15-day to 30-day payment credit terms upon delivery acceptance and VAT invoice issuance.</p>
                    ',
                ],
            ],
            [
                'position' => 3,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách vận chuyển & Giao nhận công nghiệp',
                    'slug' => 'chinh-sach-van-chuyen',
                    'seo_slug' => 'chinh-sach-van-chuyen',
                    'seo_meta_title' => 'Chính sách vận chuyển công nghiệp - Thành Khang Tecsat',
                    'seo_meta_description' => 'Quy chuẩn vận chuyển vật tư nặng, đóng kiện gỗ pallet chống va đập và thời gian giao nhận hàng toàn quốc tại Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Phạm vi giao hàng toàn quốc</h3>
                        <p>Thành Khang Tecsat giao hàng trực tiếp đến tận chân công trình, nhà máy, kho bãi hoặc cảng hàng không trên phạm vi toàn quốc (Hà Nội, TP.HCM, Đà Nẵng, Hải Phòng, Cần Thơ và các khu công nghiệp trọng điểm).</p>

                        <h3>2. Quy chuẩn đóng gói bảo vệ linh kiện cơ khí</h3>
                        <p>Các phụ tùng tải nặng như xích bậc thang cuốn, xích lá bãi đỗ xe và xích băng tải được bôi dầu bảo quản chống gỉ sét, bọc màng co PE chống ẩm và cố định chắc chắn trong thùng kiện gỗ pallet tiêu chuẩn xuất khẩu, đảm bảo an toàn tuyệt đối trong quá trình bốc xếp và vận tải.</p>

                        <h3>3. Thời gian giao hàng cam kết</h3>
                        <ul>
                            <li><strong>Hàng có sẵn tại kho (Hà Nội):</strong> Giao ngay trong ngày tại nội thành Hà Nội hoặc từ 24 - 48 giờ đối với các tỉnh lân cận và TP. Hồ Chí Minh.</li>
                            <li><strong>Hàng đặt sản xuất hoặc nhập khẩu đặc chủng:</strong> Tiến độ từ 7 - 25 ngày làm việc tùy thuộc vào quy mô đơn hàng và thông số kỹ thuật đặc thù.</li>
                        </ul>
                    ',
                ],
                'en' => [
                    'title' => 'Industrial Shipping & Delivery Policy',
                    'slug' => 'shipping-policy',
                    'seo_slug' => 'shipping-policy',
                    'seo_meta_title' => 'Industrial Shipping & Delivery Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => 'Nationwide delivery logistics, heavy-duty pallet crating standards, and transit lead times at Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. Nationwide Logistics Coverage</h3>
                        <p>Thanh Khang Tecsat delivers directly to job sites, manufacturing facilities, airport maintenance hangars, and central logistics hubs across Vietnam (Hanoi, Ho Chi Minh City, Da Nang, Hai Phong, and major industrial zones).</p>

                        <h3>2. Industrial Packaging & Protection Standards</h3>
                        <p>Heavy-duty escalator chains, car lift leaf chains, and conveyor components are coated with anti-corrosion lubricants, wrapped in protective moisture-barrier PE film, and securely crated on export-standard wooden pallets to withstand freight stresses.</p>

                        <h3>3. Committed Delivery Timelines</h3>
                        <ul>
                            <li><strong>In-stock Warehouse Items (Hanoi):</strong> Same-day dispatch within Hanoi; 24 to 48 hours for nationwide destinations including Ho Chi Minh City.</li>
                            <li><strong>Custom Fabricated / Imported Items:</strong> 7 to 25 business days depending on engineering specifications and order volume.</li>
                        </ul>
                    ',
                ],
            ],
            [
                'position' => 4,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách đổi trả & Xử lý sự cố kỹ thuật',
                    'slug' => 'chinh-sach-doi-tra',
                    'seo_slug' => 'chinh-sach-doi-tra',
                    'seo_meta_title' => 'Chính sách đổi trả & Xử lý sự cố - Thành Khang Tecsat',
                    'seo_meta_description' => 'Điều kiện đổi trả linh kiện, cam kết đổi mới 1:1 đối với lỗi sản xuất và quy trình xử lý khiếu nại kỹ thuật tại Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Cam kết đổi mới 1:1 miễn phí</h3>
                        <p>Thành Khang Tecsat cam kết đổi mới 100% không phát sinh bất kỳ chi phí nào đối với các trường hợp:</p>
                        <ul>
                            <li>Sản phẩm giao sai quy cách, sai mã SKU, sai bước xích hoặc sai thông số kỹ thuật so với hợp đồng đã ký.</li>
                            <li>Sản phẩm phát sinh lỗi vật liệu, nứt vỡ hoặc biến dạng cơ học do lỗi từ nhà máy sản xuất.</li>
                            <li>Hàng hóa bị hư hại vật lý trong quá trình vận chuyển do Thành Khang chịu trách nhiệm giao nhận.</li>
                        </ul>

                        <h3>2. Thời hạn tiếp nhận và thời gian xử lý</h3>
                        <p>Khách hàng vui lòng kiểm tra và gửi văn bản thông báo sự cố trong vòng 03 ngày làm việc kể từ thời điểm nhận hàng. Đội ngũ kỹ sư Thành Khang sẽ có mặt kiểm tra hiện trường (hoặc thẩm định hồ sơ kỹ thuật) trong vòng 24 giờ và tiến hành xuất đổi vật tư mới trong vòng 2 - 5 ngày làm việc.</p>

                        <h3>3. Điều kiện từ chối đổi trả</h3>
                        <p>Không áp dụng đổi trả đối với sản phẩm bị biến dạng do lắp đặt sai kỹ thuật, vận hành quá tải trọng cho phép, bảo dưỡng sai cách hoặc không sử dụng chất bôi trơn theo khuyến cáo của hãng.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Return, Replacement & Technical Claims Policy',
                    'slug' => 'return-refund-policy',
                    'seo_slug' => 'return-refund-policy',
                    'seo_meta_title' => 'Return, Replacement & Claims Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => 'Component return eligibility, 1:1 replacement guarantees for manufacturing defects, and claim resolution lead times at Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. 100% Free 1:1 Replacement Guarantee</h3>
                        <p>Thanh Khang Tecsat provides immediate 1:1 replacements free of charge under the following circumstances:</p>
                        <ul>
                            <li>Components delivered with incorrect specifications, mismatched part numbers, wrong chain pitches, or deviations from confirmed drawings.</li>
                            <li>Manufacturing flaws, material defects, or structural failure detected prior to commissioning.</li>
                            <li>Physical transit damage sustained during transportation arranged by Thanh Khang Tecsat.</li>
                        </ul>

                        <h3>2. Claim Submission & Resolution Window</h3>
                        <p>Clients are requested to submit technical inspection notices within 3 business days of delivery. Our technical engineers will conduct site inspections or remote technical evaluations within 24 hours and dispatch replacement units within 2 to 5 business days.</p>

                        <h3>3. Ineligibility Exclusions</h3>
                        <p>Returns are not accepted for components subjected to incorrect installation alignment, operating beyond rated tensile/load capacities, or inadequate lubrication contrary to manufacturer guidelines.</p>
                    ',
                ],
            ],
            [
                'position' => 5,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách bảo hành & Dịch vụ kỹ thuật',
                    'slug' => 'chinh-sach-bao-hanh',
                    'seo_slug' => 'chinh-sach-bao-hanh',
                    'seo_meta_title' => 'Chính sách bảo hành & Dịch vụ kỹ thuật - Thành Khang Tecsat',
                    'seo_meta_description' => 'Thời hạn bảo hành 12 - 24 tháng, dịch vụ hỗ trợ kỹ thuật bảo trì định kỳ và cam kết chất lượng của Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Thời hạn bảo hành tiêu chuẩn</h3>
                        <p>Toàn bộ linh kiện cơ khí, xích tải công nghiệp, tay vịn thang cuốn và hệ thống quạt trần công nghiệp BLDC do Thành Khang Tecsat cung ứng được bảo hành chính hãng từ <strong>12 đến 24 tháng</strong> (tùy theo từng nhóm chủng loại sản phẩm và điều khoản hợp đồng).</p>

                        <h3>2. Phạm vi bảo hành hợp lệ</h3>
                        <p>Bảo hành áp dụng cho mọi lỗi kỹ thuật phát sinh từ vật liệu, quá trình gia công nhiệt luyện, độ dão đứt gãy xích bất thường hoặc lỗi hỏng hóc động cơ điện BLDC trong điều kiện vận hành bình thường theo đúng khuyến cáo kỹ thuật của nhà sản xuất.</p>

                        <h3>3. Dịch vụ hỗ trợ kỹ thuật đồng hành trọn đời</h3>
                        <p>Bên cạnh bảo hành theo hợp đồng, Thành Khang Tecsat duy trì kênh hỗ trợ kỹ thuật 24/7. Chúng tôi sẵn sàng hỗ trợ kiểm tra độ mòn xích, đo độ căng tay vịn, tư vấn dung sai lắp ráp và đề xuất phương án bảo trì phòng ngừa sự cố cho hệ thống thiết bị của quý khách hàng.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Warranty & Technical Services Policy',
                    'slug' => 'warranty-policy',
                    'seo_slug' => 'warranty-policy',
                    'seo_meta_title' => 'Warranty & Technical Services Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => '12 to 24-month comprehensive product warranties, preventive maintenance support, and engineering advisory from Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. Standard Warranty Coverage Period</h3>
                        <p>All precision mechanical spare parts, industrial conveyor chains, escalator handrails, and BLDC HVLS fans supplied by Thanh Khang Tecsat carry official manufacturer warranties ranging from <strong>12 to 24 months</strong> depending on component classification.</p>

                        <h3>2. Warranty Eligibility</h3>
                        <p>Warranties cover material fatigue, heat-treatment anomalies, abnormal chain elongation, or BLDC motor drive failures occurring during normal operation under recommended manufacturer working parameters.</p>

                        <h3>3. Lifetime Technical Advisory & Site Support</h3>
                        <p>In addition to contractual warranties, Thanh Khang Tecsat provides round-the-clock technical advisory. We assist with chain elongation inspections, handrail tension tuning, alignment clearances, and predictive maintenance planning throughout the operational lifecycle.</p>
                    ',
                ],
            ],
            [
                'position' => 6,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách bảo mật thông tin doanh nghiệp',
                    'slug' => 'chinh-sach-bao-mat',
                    'seo_slug' => 'chinh-sach-bao-mat',
                    'seo_meta_title' => 'Chính sách bảo mật thông tin doanh nghiệp - Thành Khang Tecsat',
                    'seo_meta_description' => 'Cam kết bảo mật tuyệt đối bản vẽ kỹ thuật, hồ sơ dự án và thông tin thương mại B2B tại Thành Khang Tecsat.',
                    'content' => '
                        <h3>1. Thu thập & Mục đích sử dụng dữ liệu doanh nghiệp</h3>
                        <p>Thành Khang Tecsat chỉ thu thập các thông tin cần thiết phục vụ giao dịch B2B, bao gồm: tên công ty, mã số thuế, địa chỉ trụ sở, thông tin người liên hệ, bản vẽ kỹ thuật CAD/PDF và thông số vận hành của công trình/nhà xưởng.</p>

                        <h3>2. Cam kết bảo mật bản vẽ kỹ thuật & Bí mật thương mại</h3>
                        <p>Mọi bản vẽ kỹ thuật, sơ đồ bố trí mặt bằng băng tải và thông tin dự án do khách hàng cung cấp đều được coi là tài sản sở hữu trí tuệ mật. Thành Khang Tecsat cam kết không tiết lộ, sao chép hoặc chuyển giao cho bất kỳ bên thứ ba nào khi chưa có sự chấp thuận bằng văn bản của khách hàng (sẵn sàng ký thỏa thuận bảo mật NDA trước khi tiếp nhận dự án).</p>

                        <h3>3. Lưu trữ an toàn & Xử lý dữ liệu</h3>
                        <p>Dữ liệu hợp đồng và tài liệu kỹ thuật được lưu trữ an toàn trên hệ thống máy chủ nội bộ được mã hóa. Chúng tôi cam kết không kinh doanh hoặc khai thác dữ liệu khách hàng cho bất kỳ mục đích thương mại ngoài luồng nào khác.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Corporate Privacy & Data Protection Policy',
                    'slug' => 'privacy-policy',
                    'seo_slug' => 'privacy-policy',
                    'seo_meta_title' => 'Corporate Privacy Policy - Thanh Khang Tecsat',
                    'seo_meta_description' => 'Strict confidentiality guarantees regarding technical drawings, CAD specifications, project files, and B2B commercial data at Thanh Khang Tecsat.',
                    'content' => '
                        <h3>1. Data Collection & Business Purpose</h3>
                        <p>Thanh Khang Tecsat collects corporate data strictly for executing B2B transactions: legal business names, tax IDs, operational facility addresses, representative contacts, and engineering CAD/PDF drawings.</p>

                        <h3>2. Strict Non-Disclosure & Intellectual Property Protection</h3>
                        <p>All engineering schematics, conveyor layout designs, and project files provided by clients are treated as strictly confidential trade secrets. Thanh Khang Tecsat commits to signing formal Non-Disclosure Agreements (NDA) and will never disclose or share design data with unauthorized third parties.</p>

                        <h3>3. Secure Storage & Integrity</h3>
                        <p>Technical dossiers and commercial records are archived within encrypted internal systems. We never sell, lease, or commercially exploit client data.</p>
                    ',
                ],
            ],
        ];

        foreach ($policies as $data) {
            $slugVi = $data['vi']['slug'];
            $slugEn = $data['en']['slug'];

            // Find existing policy by translation slug
            $existingTrans = DB::table('policy_translations')
                ->where('slug', $slugVi)
                ->orWhere('slug', $slugEn)
                ->first();

            if ($existingTrans) {
                $policyId = $existingTrans->policy_id;
                DB::table('policies')->where('id', $policyId)->update([
                    'position' => $data['position'],
                    'status' => $data['status'],
                    'updated_at' => now(),
                ]);
            } else {
                $policyId = DB::table('policies')->insertGetId([
                    'position' => $data['position'],
                    'status' => $data['status'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Upsert VI translation
            DB::table('policy_translations')->updateOrInsert(
                ['policy_id' => $policyId, 'locale' => 'vi'],
                [
                    'title' => $data['vi']['title'],
                    'slug' => $slugVi,
                    'seo_slug' => $data['vi']['seo_slug'],
                    'seo_meta_title' => $data['vi']['seo_meta_title'],
                    'seo_meta_description' => $data['vi']['seo_meta_description'],
                    'content' => $data['vi']['content'],
                ]
            );

            // Upsert EN translation
            DB::table('policy_translations')->updateOrInsert(
                ['policy_id' => $policyId, 'locale' => 'en'],
                [
                    'title' => $data['en']['title'],
                    'slug' => $slugEn,
                    'seo_slug' => $data['en']['seo_slug'],
                    'seo_meta_title' => $data['en']['seo_meta_title'],
                    'seo_meta_description' => $data['en']['seo_meta_description'],
                    'content' => $data['en']['content'],
                ]
            );
        }

        Policy::clearPolicyCaches();
    }
}
