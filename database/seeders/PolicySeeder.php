<?php

namespace Database\Seeders;

use App\Models\Policy\Policy;
use Illuminate\Database\Seeder;

class PolicySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $policies = [
            [
                'position' => 1,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Điều khoản dịch vụ',
                    'slug' => 'dieu-khoan-dich-vu',
                    'seo_slug' => 'dieu-khoan-dich-vu',
                    'seo_meta_title' => 'Điều khoản dịch vụ - HH Maker | Xưởng in 3D',
                    'seo_meta_description' => 'Điều khoản dịch vụ, quy trình đặt hàng, phạm vi gia công in 3D và cam kết bảo vệ sở hữu trí tuệ tại HH Maker.',
                    'content' => '
                        <h3>1. Phạm vi dịch vụ in và chế tác 3D</h3>
                        <p>HH Maker cung cấp dịch vụ in 3D chuyên nghiệp, thiết kế CAD, dựng hình kỹ thuật số và làm đồ custom theo yêu cầu. Tất cả quy trình gia công đều tuân thủ chặt chẽ các thông số kỹ thuật, bản vẽ hoặc file 3D do khách hàng cung cấp hoặc đã thống nhất trong quá trình tư vấn.</p>
                        
                        <h3>2. Quy trình báo giá và đặt hàng</h3>
                        <p>Bảng báo giá dự án có hiệu lực trong vòng 15 ngày làm việc kể từ thời điểm gửi. Đơn hàng được chính thức đưa vào sản xuất sau khi hai bên thống nhất phương án kỹ thuật và hoàn tất thủ tục đặt cọc theo thỏa thuận.</p>

                        <h3>3. Bản quyền tác giả & Quyền sở hữu trí tuệ</h3>
                        <p>HH Maker cam kết tôn trọng tuyệt đối quyền sở hữu trí tuệ của khách hàng đối với các mô hình 3D (STL, OBJ, STEP, 3MF). Chúng tôi không sao chép, phát tán hay thương mại hóa bất kỳ thiết kế nào khi chưa có sự đồng ý bằng văn bản của khách hàng.</p>

                        <h3>4. Tiến độ hoàn thiện và bàn giao</h3>
                        <p>Thời gian gia công được tính toán dựa trên khối lượng mô hình, công nghệ in (FDM, SLA, SLS) và yêu cầu xử lý bề mặt. Các mẫu thử đơn chiếc thường hoàn thiện trong vòng 24 - 48 giờ làm việc.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Terms of Service',
                    'slug' => 'terms-of-service',
                    'seo_slug' => 'terms-of-service',
                    'seo_meta_title' => 'Terms of Service - HH Maker | 3D Printing Workshop',
                    'seo_meta_description' => 'Official service policies, ordering process, scope of 3D printing and custom fabrication at HH Maker.',
                    'content' => '
                        <h3>1. Scope of 3D Printing Services</h3>
                        <p>HH Maker provides professional 3D printing services, CAD design, digital sculpting, and bespoke custom fabrication. All manufacturing activities strictly adhere to agreed specifications, engineering drawings, or 3D digital models provided by clients or mutually finalized during consultation.</p>
                        
                        <h3>2. Quotations and Ordering Process</h3>
                        <p>Project quotations are valid for 15 business days from issuance. Production commences upon official client confirmation and completion of required initial deposit terms. Technical revisions requested after slicing or fabrication commencement may incur additional scheduling or material costs.</p>

                        <h3>3. Intellectual Property and Confidentiality</h3>
                        <p>HH Maker honors and protects customer intellectual property. Client 3D models (STL, OBJ, STEP, 3MF) and proprietary design files will never be shared, commercialized, or reproduced for third parties without explicit prior written authorization.</p>

                        <h3>4. Lead Times and Delivery</h3>
                        <p>Turnaround times are calculated based on model volume, printing technology (FDM, SLA, SLS), and post-processing complexity. Single prototype parts typically complete within 24 to 48 hours. Express nationwide logistics partners facilitate swift and insured delivery.</p>
                    ',
                ],
            ],
            [
                'position' => 2,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách bảo mật',
                    'slug' => 'chinh-sach-bao-mat',
                    'seo_slug' => 'chinh-sach-bao-mat',
                    'seo_meta_title' => 'Chính sách bảo mật - HH Maker | Xưởng in 3D',
                    'seo_meta_description' => 'Cam kết bảo mật thông tin khách hàng và tệp thiết kế CAD 3D độc quyền tại xưởng in 3D HH Maker.',
                    'content' => '
                        <h3>1. Thu thập và sử dụng thông tin</h3>
                        <p>Chúng tôi chỉ thu thập các thông tin liên lạc cần thiết (họ tên, số điện thoại, email, địa chỉ giao hàng) và tệp mô hình 3D nhằm phục vụ việc tư vấn báo giá, gia công sản xuất và giao vận đơn hàng.</p>

                        <h3>2. Bảo mật tệp thiết kế 3D</h3>
                        <p>Tất cả tệp CAD và tài liệu kỹ thuật gửi tới HH Maker được lưu trữ bảo mật trên hệ thống máy chủ nội bộ. Dữ liệu sẽ tự động xóa sau 30 ngày kể từ khi đơn hàng giao thành công, trừ trường hợp khách hàng có yêu cầu lưu trữ để tái đặt hàng.</p>

                        <h3>3. Cam kết không chia sẻ dữ liệu</h3>
                        <p>HH Maker không bán, trao đổi hoặc chia sẻ thông tin cá nhân hay bản vẽ của bạn cho bất kỳ bên thứ ba nào, ngoại trừ đơn vị vận chuyển đối tác phục vụ giao nhận tận nơi.</p>

                        <h3>4. Liên hệ giải đáp</h3>
                        <p>Mọi thắc mắc về chính sách bảo mật hoặc yêu cầu xóa dữ liệu thiết kế, xin vui lòng gửi email về info@hhmaker.vn hoặc hotline 0906636425.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Privacy Policy',
                    'slug' => 'privacy-policy',
                    'seo_slug' => 'privacy-policy',
                    'seo_meta_title' => 'Privacy Policy - HH Maker | 3D Printing Workshop',
                    'seo_meta_description' => 'Customer information security and proprietary CAD file confidentiality policy at HH Maker.',
                    'content' => '
                        <h3>1. Collection of Information</h3>
                        <p>We collect essential contact information (name, telephone number, email address, delivery location) and technical project briefs solely to fulfill consultation, quoting, manufacturing, and order delivery.</p>

                        <h3>2. Security of 3D Files and CAD Data</h3>
                        <p>All 3D CAD assets and confidential project documents uploaded to HH Maker are encrypted and accessible strictly by authorized engineering personnel. Files are automatically archived or permanently deleted 30 days after successful delivery unless a continuous retention agreement is requested.</p>

                        <h3>3. Data Disclosure</h3>
                        <p>We do not sell, rent, or trade customer information to any third parties. Data is shared exclusively with certified logistics partners for courier purposes.</p>

                        <h3>4. Inquiries and Contact</h3>
                        <p>For any privacy inquiries or requests to remove design data, please contact our data compliance officer at info@hhmaker.vn or hotline 0906636425.</p>
                    ',
                ],
            ],
            [
                'position' => 3,
                'status' => Policy::STATUS_ACTIVE,
                'vi' => [
                    'title' => 'Chính sách bảo hành & đổi trả',
                    'slug' => 'chinh-sach-bao-hanh-doi-tra',
                    'seo_slug' => 'chinh-sach-bao-hanh-doi-tra',
                    'seo_meta_title' => 'Chính sách bảo hành & đổi trả - HH Maker | Xưởng in 3D',
                    'seo_meta_description' => 'Quy định tiêu chuẩn dung sai cơ khí, chính sách in lại miễn phí 100% và đổi trả sản phẩm in 3D tại HH Maker.',
                    'content' => '
                        <h3>1. Tiêu chuẩn dung sai kỹ thuật</h3>
                        <p>Sản phẩm in 3D tại HH Maker tuân thủ dung sai cơ khí chuẩn: công nghệ FDM đạt +/- 0.2mm (hoặc 0.5%), công nghệ Resin/SLA đạt +/- 0.05mm theo thông số file slicer đã được duyệt.</p>

                        <h3>2. Cam kết in lại miễn phí 100%</h3>
                        <p>HH Maker thực hiện in lại hoàn toàn miễn phí cho khách hàng trong các trường hợp:</p>
                        <ul>
                            <li>Sản phẩm bị nứt vỡ, hư hỏng trong quá trình vận chuyển (có video/ảnh chụp mở hộp xác nhận).</li>
                            <li>Lỗi gia công từ xưởng in như tách lớp nghiêm trọng, lệch layer hoặc biến dạng nhiệt vượt chuẩn.</li>
                            <li>Kích thước sai lệch vượt mức dung sai kỹ thuật đã cam kết khiến chi tiết không thể lắp ghép.</li>
                        </ul>

                        <h3>3. Thời hạn tiếp nhận yêu cầu</h3>
                        <p>Khách hàng vui lòng kiểm tra sản phẩm và gửi phản hồi cho HH Maker trong vòng 48 giờ kể từ khi nhận kiện hàng để được hỗ trợ xử lý đổi trả hoặc in bù nhanh chóng nhất.</p>
                    ',
                ],
                'en' => [
                    'title' => 'Warranty & Return Policy',
                    'slug' => 'warranty-return-policy',
                    'seo_slug' => 'warranty-return-policy',
                    'seo_meta_title' => 'Warranty & Return Policy - HH Maker | 3D Printing Workshop',
                    'seo_meta_description' => '3D printing mechanical tolerances, free re-print guarantee, and warranty claim procedures at HH Maker.',
                    'content' => '
                        <h3>1. Technical Tolerance Standards</h3>
                        <p>HH Maker maintains stringent industrial standards: standard FDM tolerance is +/- 0.2mm (or 0.5%), and high-precision SLA/Resin tolerance is +/- 0.05mm. Dimensional tolerances conform strictly to agreed design slicer parameters.</p>

                        <h3>2. 100% Free Reprint Guarantee</h3>
                        <p>HH Maker provides full, free-of-charge reprints under the following conditions:</p>
                        <ul>
                            <li>Physical cracking or breakage sustained during courier transit with valid packaging unboxing evidence.</li>
                            <li>Critical printing defects such as severe layer shifts, delamination, or incomplete curing.</li>
                            <li>Deviations exceeding agreed dimensional tolerances rendering components functionally unusable.</li>
                        </ul>

                        <h3>3. Claim Submission Period</h3>
                        <p>Please inspect received items and notify our technical support team within 48 hours of parcel arrival via Zalo OA or email info@hhmaker.vn with unboxing photos/videos for immediate expedited processing.</p>
                    ',
                ],
            ],
        ];

        foreach ($policies as $data) {
            $slugVi = $data['vi']['slug'];
            $slugEn = $data['en']['slug'];

            // Find existing policy by translation slug
            $policy = Policy::whereHas('translations', function ($q) use ($slugVi, $slugEn) {
                $q->whereIn('slug', [$slugVi, $slugEn]);
            })->first();

            if (!$policy) {
                $policy = new Policy();
            }

            $policy->status = $data['status'];
            $policy->position = $data['position'];
            $policy->save();

            // Save VI translation
            $transVi = $policy->translateOrNew('vi');
            $transVi->fill($data['vi']);
            $transVi->save();

            // Save EN translation
            $transEn = $policy->translateOrNew('en');
            $transEn->fill($data['en']);
            $transEn->save();
        }

        Policy::clearPolicyCaches();
    }
}
