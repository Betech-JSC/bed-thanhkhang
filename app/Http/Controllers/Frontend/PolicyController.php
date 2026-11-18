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
                ? $title . ' - Thanh Khang Tecsat | B2B Industrial Policies'
                : $title . ' - Thành Khang Tecsat | Chính sách B2B',
            'description' => $locale === 'en'
                ? 'Official B2B service policies, sales terms, warranty, and technical commitments of Thanh Khang Tecsat.'
                : 'Thông tin chính sách bán hàng, bảo hành kỹ thuật, vận chuyển và cam kết chất lượng B2B của Thành Khang Tecsat.',
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
     * Get B2B standard fallback policies for Thanh Khang Tecsat.
     */
    protected function getFallbackPolicies(?string $locale = null): Collection
    {
        $locale = $locale ?: current_locale();
        $isEn = ($locale === 'en');

        return collect([
            [
                'id' => 1,
                'title' => $isEn ? 'B2B Sales & Quotation Policy' : 'Chính sách bán hàng & Báo giá B2B',
                'slug' => $isEn ? 'sales-policy' : 'chinh-sach-ban-hang',
                'slug_vi' => 'chinh-sach-ban-hang',
                'slug_en' => 'sales-policy',
                'content' => $isEn
                    ? '<h3>1. Scope of Supply & Target Customers</h3><p>Thanh Khang Tecsat specializes in supplying genuine escalator components, baggage handling systems, industrial conveyor chains, and energy-saving BLDC fans nationwide.</p><h3>2. Technical RFQ Quotation Procedure</h3><p>Submit RFQ inquiries via Hotline/Zalo (+84 967 732 665) or Email (info@dvktthanhkhang.vn). Quotes are provided within 2 to 4 business hours.</p><h3>3. Quotation Validity & Commercial Terms</h3><p>Formal quotations remain valid for 30 business days with tiered project volume discounts and complete VAT invoices, CO, and CQ certificates.</p>'
                    : '<h3>1. Phạm vi cung ứng & Đối tượng phục vụ</h3><p>Thành Khang Tecsat chuyên cung ứng vật tư, linh kiện cơ khí chính xác cho thang cuốn, thang nâng ô tô tự động, hệ thống băng tải hành lý sân bay, dây chuyền đóng gói công nghiệp và quạt trần công nghiệp BLDC trên toàn quốc.</p><h3>2. Quy trình báo giá kỹ thuật (RFQ)</h3><p>Mọi yêu cầu báo giá được tiếp nhận qua Hotline/Zalo 0967732665 hoặc Email info@dvktthanhkhang.vn với thời gian phản hồi chi tiết trong vòng 2 – 4 giờ làm việc.</p><h3>3. Hiệu lực báo giá & Chứng từ xuất xưởng</h3><p>Báo giá có hiệu lực trong 30 ngày. 100% đơn hàng B2B có đầy đủ hóa đơn VAT, chứng chỉ xuất xưởng, chứng nhận CO và CQ chính hãng.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/sales-policy' : '/chinh-sach/chinh-sach-ban-hang',
            ],
            [
                'id' => 2,
                'title' => $isEn ? 'Corporate Privacy Policy' : 'Chính sách bảo mật thông tin doanh nghiệp',
                'slug' => $isEn ? 'privacy-policy' : 'chinh-sach-bao-mat',
                'slug_vi' => 'chinh-sach-bao-mat',
                'slug_en' => 'privacy-policy',
                'content' => $isEn
                    ? '<h3>1. Business Data Collection</h3><p>We collect essential corporate information (company name, tax code, technical CAD drawings) strictly to prepare engineering quotes and execute delivery contracts.</p><h3>2. Confidentiality & Non-Disclosure</h3><p>All drawings and project specifications are treated as confidential trade secrets. We never disclose or commercialize customer design assets to third parties.</p><h3>3. Contact & Compliance</h3><p>For inquiries regarding privacy, please contact our compliance desk at info@dvktthanhkhang.vn or hotline +84 967 732 665.</p>'
                    : '<h3>1. Thu thập dữ liệu doanh nghiệp</h3><p>Chúng tôi chỉ thu thập các thông tin liên lạc và bản vẽ kỹ thuật phục vụ việc tư vấn báo giá, giao nhận vật tư và ký kết hợp đồng kinh tế.</p><h3>2. Bảo mật bản vẽ kỹ thuật</h3><p>Mọi bản vẽ cơ khí và tài liệu dự án được bảo mật tuyệt đối trên máy chủ nội bộ. Thành Khang Tecsat cam kết không tiết lộ hay chuyển giao dữ liệu cho bên thứ ba.</p><h3>3. Liên hệ giải đáp</h3><p>Mọi thắc mắc về chính sách bảo mật, xin vui lòng gửi email về info@dvktthanhkhang.vn hoặc hotline 0967732665.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/privacy-policy' : '/chinh-sach/chinh-sach-bao-mat',
            ],
            [
                'id' => 3,
                'title' => $isEn ? 'Warranty & Technical Services Policy' : 'Chính sách bảo hành & Dịch vụ kỹ thuật',
                'slug' => $isEn ? 'warranty-policy' : 'chinh-sach-bao-hanh',
                'slug_vi' => 'chinh-sach-bao-hanh',
                'slug_en' => 'warranty-policy',
                'content' => $isEn
                    ? '<h3>1. Standard Warranty Coverage</h3><p>All precision escalator spare parts, conveyor chains, and BLDC fans carry official 12 to 24-month warranties against manufacturing and material defects.</p><h3>2. 100% Free 1:1 Replacement Guarantee</h3><p>Immediate 1:1 replacement is provided for dimension mismatch, transit damage, or material flaws detected upon handover.</p><h3>3. Technical Advisory Support</h3><p>Our engineering team provides 24/7 technical advisory on alignment, chain elongation tracking, and preventive maintenance.</p>'
                    : '<h3>1. Thời hạn bảo hành tiêu chuẩn</h3><p>Toàn bộ linh kiện cơ khí, xích tải thang cuốn và quạt trần công nghiệp BLDC do Thành Khang Tecsat cung ứng được bảo hành chính hãng từ 12 đến 24 tháng.</p><h3>2. Cam kết đổi mới 1:1 miễn phí</h3><p>Đổi mới 100% không phát sinh chi phí nếu sản phẩm sai quy cách, lỗi gia công hoặc nứt vỡ trong quá trình vận chuyển.</p><h3>3. Hỗ trợ kỹ thuật 24/7</h3><p>Kỹ sư Thành Khang Tecsat đồng hành trọn đời sản phẩm, tư vấn đo độ dão mòn xích và phương án bảo trì phòng ngừa sự cố.</p>',
                'formatted_updated_at' => null,
                'icon' => null,
                'url' => $isEn ? '/en/policies/warranty-policy' : '/chinh-sach/chinh-sach-bao-hanh',
            ],
        ]);
    }
}
