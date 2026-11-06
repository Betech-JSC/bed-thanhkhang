<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Policy\Policy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class PolicyController extends Controller
{
    public $model = Policy::class;

    public function index()
    {
        try {
            return $this->renderDataPage();
        } catch (\Throwable $th) {
            report($th);
            return $this->renderDataPage();
        }
    }

    public function show($slug)
    {
        try {
            return $this->renderDataPage($slug);
        } catch (\Throwable $th) {
            report($th);
            return $this->renderDataPage($slug);
        }
    }

    public function renderDataPage($slug = null)
    {
        $locale = current_locale();

        try {
            $policies = Policy::query()
                ->active()
                ->sortByPosition()
                ->get()
                ->map(fn($item) => $item->transform());
        } catch (\Throwable $th) {
            report($th);
            $policies = collect();
        }

        $fallbacks = $this->getFallbackPolicies($locale);

        if ($policies->isEmpty()) {
            $policies = $fallbacks;
        }

        $content = null;

        if (!empty($slug)) {
            // 1. Try finding in loaded policies
            $content = $policies->first(function ($item) use ($slug) {
                return ($item['slug'] ?? '') === $slug
                    || ($item['slug_vi'] ?? '') === $slug
                    || ($item['slug_en'] ?? '') === $slug;
            });

            // 2. If not found, try searching in Figma fallback policies
            if (!$content) {
                $content = $fallbacks->first(function ($item) use ($slug) {
                    return ($item['slug'] ?? '') === $slug
                        || ($item['slug_vi'] ?? '') === $slug
                        || ($item['slug_en'] ?? '') === $slug;
                });
            }
        }

        // 3. Graceful fallback: If not found or slug is null, default to first policy
        if (!$content) {
            $content = $policies->first() ?: $fallbacks->first();
        }

        $title = $content['title'] ?? ($locale === 'en' ? 'Policies & Terms' : 'Chính sách & Điều khoản');
        $seo = [
            'title' => $locale === 'en'
                ? $title . ' - HH Maker | 3D Printing Workshop'
                : $title . ' - HH Maker | Xưởng in 3D',
            'description' => $locale === 'en'
                ? 'Official service policies, warranty terms, and privacy commitments for 3D printing and custom modeling at HH Maker.'
                : 'Thông tin chính sách dịch vụ, cam kết bảo mật và quy định bảo hành in 3D chuyên nghiệp tại HH Maker.',
        ];

        $data = [
            'list_sidebar' => $policies->values(),
            'content' => $content,
            'seo' => $seo,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Policy', $data)
            ->withViewData(['seo' => $seo]);
    }

    /**
     * Get Figma standard fallback policies (Bảo mật, Điều khoản dịch vụ, Bảo hành & đổi trả).
     */
    protected function getFallbackPolicies(?string $locale = null): Collection
    {
        $locale = $locale ?: current_locale();
        $isEn = ($locale === 'en');

        return collect([
            [
                'id' => 1,
                'title' => $isEn ? 'Terms of Service' : 'Điều khoản dịch vụ',
                'slug' => $isEn ? 'terms-of-service' : 'dieu-khoan-dich-vu',
                'slug_vi' => 'dieu-khoan-dich-vu',
                'slug_en' => 'terms-of-service',
                'content' => $isEn
                    ? '<h3>1. Scope of 3D Printing Services</h3><p>HH Maker provides professional 3D printing services, CAD design, digital sculpting, and bespoke custom fabrication. All manufacturing activities strictly adhere to agreed specifications, engineering drawings, or 3D digital models provided by clients or mutually finalized during consultation.</p><h3>2. Quotations and Ordering Process</h3><p>Project quotations are valid for 15 business days from issuance. Production commences upon official client confirmation and completion of required initial deposit terms. Technical revisions requested after slicing or fabrication commencement may incur additional scheduling or material costs.</p><h3>3. Intellectual Property and Confidentiality</h3><p>HH Maker honors and protects customer intellectual property. Client 3D models (STL, OBJ, STEP, 3MF) and proprietary design files will never be shared, commercialized, or reproduced for third parties without explicit prior written authorization.</p><h3>4. Lead Times and Delivery</h3><p>Turnaround times are calculated based on model volume, printing technology (FDM, SLA, SLS), and post-processing complexity. Single prototype parts typically complete within 24 to 48 hours. Express nationwide logistics partners facilitate swift and insured delivery.</p>'
                    : '<h3>1. Phạm vi dịch vụ in và chế tác 3D</h3><p>HH Maker cung cấp dịch vụ in 3D chuyên nghiệp, thiết kế CAD, dựng hình kỹ thuật số và làm đồ custom theo yêu cầu. Tất cả quy trình gia công đều tuân thủ chặt chẽ các thông số kỹ thuật, bản vẽ hoặc file 3D do khách hàng cung cấp hoặc đã thống nhất trong quá trình tư vấn.</p><h3>2. Quy trình báo giá và đặt hàng</h3><p>Bảng báo giá dự án có hiệu lực trong vòng 15 ngày làm việc kể từ thời điểm gửi. Đơn hàng được chính thức đưa vào sản xuất sau khi hai bên thống nhất phương án kỹ thuật và hoàn tất thủ tục đặt cọc theo thỏa thuận.</p><h3>3. Bản quyền tác giả & Quyền sở hữu trí tuệ</h3><p>HH Maker cam kết tôn trọng tuyệt đối quyền sở hữu trí tuệ của khách hàng đối với các mô hình 3D (STL, OBJ, STEP, 3MF). Chúng tôi không sao chép, phát tán hay thương mại hóa bất kỳ thiết kế nào khi chưa có sự đồng ý bằng văn bản của khách hàng.</p><h3>4. Tiến độ hoàn thiện và bàn giao</h3><p>Thời gian gia công được tính toán dựa trên khối lượng mô hình, công nghệ in (FDM, SLA, SLS) và yêu cầu xử lý bề mặt. Các mẫu thử đơn chiếc thường hoàn thiện trong vòng 24 - 48 giờ làm việc.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/terms-of-service' : '/chinh-sach/dieu-khoan-dich-vu',
            ],
            [
                'id' => 2,
                'title' => $isEn ? 'Privacy Policy' : 'Chính sách bảo mật',
                'slug' => $isEn ? 'privacy-policy' : 'chinh-sach-bao-mat',
                'slug_vi' => 'chinh-sach-bao-mat',
                'slug_en' => 'privacy-policy',
                'content' => $isEn
                    ? '<h3>1. Collection of Information</h3><p>We collect essential contact information (name, telephone number, email address, delivery location) and technical project briefs solely to fulfill consultation, quoting, manufacturing, and order delivery.</p><h3>2. Security of 3D Files and CAD Data</h3><p>All 3D CAD assets and confidential project documents uploaded to HH Maker are encrypted and accessible strictly by authorized engineering personnel. Files are automatically archived or permanently deleted 30 days after successful delivery unless a continuous retention agreement is requested.</p><h3>3. Data Disclosure</h3><p>We do not sell, rent, or trade customer information to any third parties. Data is shared exclusively with certified logistics partners for courier purposes.</p><h3>4. Inquiries and Contact</h3><p>For any privacy inquiries or requests to remove design data, please contact our data compliance officer at info@hhmaker.vn or hotline 0906636425.</p>'
                    : '<h3>1. Thu thập và sử dụng thông tin</h3><p>Chúng tôi chỉ thu thập các thông tin liên lạc cần thiết (họ tên, số điện thoại, email, địa chỉ giao hàng) và tệp mô hình 3D nhằm phục vụ việc tư vấn báo giá, gia công sản xuất và giao vận đơn hàng.</p><h3>2. Bảo mật tệp thiết kế 3D</h3><p>Tất cả tệp CAD và tài liệu kỹ thuật gửi tới HH Maker được lưu trữ bảo mật trên hệ thống máy chủ nội bộ. Dữ liệu sẽ tự động xóa sau 30 ngày kể từ khi đơn hàng giao thành công, trừ trường hợp khách hàng có yêu cầu lưu trữ để tái đặt hàng.</p><h3>3. Cam kết không chia sẻ dữ liệu</h3><p>HH Maker không bán, trao đổi hoặc chia sẻ thông tin cá nhân hay bản vẽ của bạn cho bất kỳ bên thứ ba nào, ngoại trừ đơn vị vận chuyển đối tác phục vụ giao nhận tận nơi.</p><h3>4. Liên hệ giải đáp</h3><p>Mọi thắc mắc về chính sách bảo mật hoặc yêu cầu xóa dữ liệu thiết kế, xin vui lòng gửi email về info@hhmaker.vn hoặc hotline 0906636425.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/privacy-policy' : '/chinh-sach/chinh-sach-bao-mat',
            ],
            [
                'id' => 3,
                'title' => $isEn ? 'Warranty & Return Policy' : 'Chính sách bảo hành & đổi trả',
                'slug' => $isEn ? 'warranty-return-policy' : 'chinh-sach-bao-hanh-doi-tra',
                'slug_vi' => 'chinh-sach-bao-hanh-doi-tra',
                'slug_en' => 'warranty-return-policy',
                'content' => $isEn
                    ? '<h3>1. Technical Tolerance Standards</h3><p>HH Maker maintains stringent industrial standards: standard FDM tolerance is +/- 0.2mm (or 0.5%), and high-precision SLA/Resin tolerance is +/- 0.05mm. Dimensional tolerances conform strictly to agreed design slicer parameters.</p><h3>2. 100% Free Reprint Guarantee</h3><p>HH Maker provides full, free-of-charge reprints under the following conditions:</p><ul><li>Physical cracking or breakage sustained during courier transit with valid packaging unboxing evidence.</li><li>Critical printing defects such as severe layer shifts, delamination, or incomplete curing.</li><li>Deviations exceeding agreed dimensional tolerances rendering components functionally unusable.</li></ul><h3>3. Claim Submission Period</h3><p>Please inspect received items and notify our technical support team within 48 hours of parcel arrival via Zalo OA or email info@hhmaker.vn with unboxing photos/videos for immediate expedited processing.</p>'
                    : '<h3>1. Tiêu chuẩn dung sai kỹ thuật</h3><p>Sản phẩm in 3D tại HH Maker tuân thủ dung sai cơ khí chuẩn: công nghệ FDM đạt +/- 0.2mm (hoặc 0.5%), công nghệ Resin/SLA đạt +/- 0.05mm theo thông số file slicer đã được duyệt.</p><h3>2. Cam kết in lại miễn phí 100%</h3><p>HH Maker thực hiện in lại hoàn toàn miễn phí cho khách hàng trong các trường hợp:</p><ul><li>Sản phẩm bị nứt vỡ, hư hỏng trong quá trình vận chuyển (có video/ảnh chụp mở hộp xác nhận).</li><li>Lỗi gia công từ xưởng in như tách lớp nghiêm trọng, lệch layer hoặc biến dạng nhiệt vượt chuẩn.</li><li>Kích thước sai lệch vượt mức dung sai kỹ thuật đã cam kết khiến chi tiết không thể lắp ghép.</li></ul><h3>3. Thời hạn tiếp nhận yêu cầu</h3><p>Khách hàng vui lòng kiểm tra sản phẩm và gửi phản hồi cho HH Maker trong vòng 48 giờ kể từ khi nhận kiện hàng để được hỗ trợ xử lý đổi trả hoặc in bù nhanh chóng nhất.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/warranty-return-policy' : '/chinh-sach/chinh-sach-bao-hanh-doi-tra',
            ],
        ]);
    }
}
