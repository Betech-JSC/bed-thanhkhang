<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public const FLASH_SALE = 'flash-sale';
    public const FEATURED_PRODUCT = 'featured-products';
    public const FEATURED_CATEGORY = 'featured-categories';
    public const TOP_CATEGORY = 'top-categories';
    public const CLOSED = 'closed';
    public const MENU_CATEGORY = 'menu-categories';
    public const SEARCH = 'search';
    public const SHIPPING = 'shipping';
    public const PROMOTION = 'promotion';
    public const FOOTER_REDIRECT = 'footer-redirect';
    public const VNPAY = 'vnpay';

    public const STATUS_OPEN = 'open';
    public const STATUS_CLOSED = 'closed';
    public const STATUS_TIMER = 'timer';

    public $rules = [
        'group' => 'nullable',
        'name' => 'nullable',
    ];

    public static function rules($id)
    {
        return [
            self::FLASH_SALE => [
                'image_title' => 'required',
                'background_image' => 'required',
            ],
            self::FEATURED_PRODUCT => [
                'image_title' => 'nullable',
                'title' => 'required',
                'text_color' => ['required', 'regex:/^(#(?:[0-9a-f]{2}){2,4}|#[0-9a-f]{3}\((?:\d+%?(?:deg|rad|grad|turn)?(?:,|\s)+){2,3}[\s\/]*[\d\.]+%?\))$/i'],
                'background_image' => 'required',
                'products' => 'required'
            ],
            self::FEATURED_CATEGORY => [
                'categories' => 'required'
            ],
            self::TOP_CATEGORY => [
                'categories' => 'required'
            ],
            self::CLOSED => [
                'time_open' => 'nullable',
                'time_closed' => 'nullable',
                'status' => 'nullable'
            ],
            self::MENU_CATEGORY => [
                'categories' => 'required'
            ],
            self::SEARCH => [
                'keywords' => 'nullable'
            ],
            self::SHIPPING => [
                'default' => 'required',
                'free_shipping' => 'required',
                'product_count' => 'required',
                'free_shipping_vnp' => 'required'
            ],
            self::PROMOTION => [
                'content' => 'nullable'
            ],
            self::FOOTER_REDIRECT => [
                'about' => 'nullable',
                'job' => 'nullable'
            ],
            self::VNPAY => [
                'tmn_code' => 'required',
                'hash_secret' => 'required',
                'test_mode' => 'required',
            ],
            'general' => [
                'general_business_name' => 'nullable',
                'general_business_name_en' => 'nullable',
                'general_site_name' => 'nullable',
                'general_site_name_en' => 'nullable',
                'general_email' => 'nullable',
                'general_address' => 'nullable',
                'general_address_en' => 'nullable',
                'general_phone' => 'nullable',
                'general_zalo' => 'nullable',
                'general_working_hours' => 'nullable',
                'general_working_hours_en' => 'nullable',
                'general_facebook' => 'nullable',
                'general_tiktok' => 'nullable',
                'map_iframe' => 'nullable',
                'general_map_iframe' => 'nullable',
                'company_name' => 'nullable',
                'company_name_en' => 'nullable',
                'site_name' => 'nullable',
                'site_name_en' => 'nullable',
                'hotline' => 'nullable',
                'phone' => 'nullable',
                'email' => 'nullable',
                'address' => 'nullable',
                'address_en' => 'nullable',
                'showroom_address' => 'nullable',
                'working_hours' => 'nullable',
                'working_hours_en' => 'nullable',
                'zalo' => 'nullable',
                'facebook' => 'nullable',
                'tiktok' => 'nullable',
            ],
        ][$id] ?? [];
    }

    /**
     * Bóc tách URL src="..." khi Admin dán nguyên thẻ <iframe> Google Maps
     */
    public static function extractMapIframeSrc(?string $raw): ?string
    {
        if ($raw === null) {
            return null;
        }
        $trimmed = trim($raw);
        if ($trimmed === '') {
            return '';
        }
        if (str_contains(mb_strtolower($trimmed), '<iframe') && preg_match('/src=["\']([^"\']+)["\']/i', $trimmed, $matches)) {
            return trim($matches[1]);
        }
        return $trimmed;
    }

    public static function clearBrandCaches(): void
    {
        try {
            settings()->group('general')->flushCache();
        } catch (\Throwable $e) {
            // Ignore
        }

        try {
            \Illuminate\Support\Facades\Cache::tags(['settings'])->flush();
        } catch (\Throwable $e) {
            // Ignore if cache driver does not support tags
        }

        \Illuminate\Support\Facades\Cache::forget('global_settings');

        $locales = config('app.locales', ['vi', 'en']);
        foreach ($locales as $locale) {
            \Illuminate\Support\Facades\Cache::forget('global_settings_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('inertia_layout_shared_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('home_page_data_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('history_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('agencies_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('process_index_' . $locale);
            \Illuminate\Support\Facades\Cache::forget('contact_index_' . $locale);
        }
    }

    public static function getFeaturedProduct()
    {
        $featuredProduct = settings()->group(self::FEATURED_PRODUCT)->all();

        $data = [
            'image_title' => static_url(json_decode($featuredProduct['image_title'] ?? null)?->path),
            'title' => $featuredProduct['title'] ?? null,
            'text_color' => $featuredProduct['text_color'] ?? '#000000',
            'background_image' => static_url(json_decode($featuredProduct['background_image'] ?? null)?->path),
            'product_ids' => collect(json_decode($featuredProduct['products'] ?? null))->pluck('id')
        ];

        return $data;
    }

    public static function getFlashSale()
    {
        $featuredProduct = settings()->group(self::FLASH_SALE)->all();

        $data = [
            'image_title' => static_url(json_decode($featuredProduct['image_title'] ?? null)?->path),
            'background_image' => static_url(json_decode($featuredProduct['background_image'] ?? null)?->path),
        ];

        return $data;
    }

    public static function getFeaturedCategoryIds()
    {
        $featuredCategory = settings()->group(self::FEATURED_CATEGORY)->all();

        return collect(json_decode($featuredCategory['categories'] ?? null))->pluck('id');
    }

    public static function getTopCategoryIds()
    {
        $topCategory = settings()->group(self::TOP_CATEGORY)->all();

        return collect(json_decode($topCategory['categories'] ?? null))->pluck('id');
    }

    public static function getMenuCategoryIds()
    {
        $menuCategory = settings()->group(self::MENU_CATEGORY)->all();

        return collect(json_decode($menuCategory['categories'] ?? null))->pluck('id');
    }

    public static function getKeywords()
    {
        $search = settings()->group(self::SEARCH)->all();

        return collect(json_decode($search['keywords'] ?? null))->pluck('title');
    }

    public static function getShipping()
    {
        $shipping = settings()->group(self::SHIPPING)->all();

        return [
            'default' => $shipping['default'] ?? 50000,
            'free_shipping' => $shipping['free_shipping'] ?? 500000,
            'product_count' => $shipping['product_count'] ?? 0,
            'free_shipping_vnp' => $shipping['free_shipping_vnp'] ?? false
        ];
    }

    public static function getFooterRedirect()
    {
        $footerRedirect = settings()->group(self::FOOTER_REDIRECT)->all();

        return [
            'link_about' => $footerRedirect['about'] ?? null,
            'link_job' => $footerRedirect['job'] ?? null
        ];
    }

    public static function available()
    {
        $data = settings()->group(self::CLOSED)->all();

        if (!empty($data['status'])) {
            switch ($data['status']) {
                case self::STATUS_CLOSED:
                    return false;
                    break;
                case self::STATUS_TIMER:
                    $timeOpen = !empty($data['time_open']) ? strtotime($data['time_open']) : null;
                    $timeClosed = !empty($data['time_closed']) ? strtotime($data['time_closed']) : null;
                    $now = strtotime(now());

                    if ($timeOpen == null && $timeClosed == null) {
                        return true;
                    } else if ($timeOpen != null && $timeClosed == null) {
                        return $timeOpen < $now;
                    } else if ($timeOpen == null && $timeClosed != null) {
                        return $timeClosed > $now;
                    } else {
                        return ($timeOpen < $now && $timeClosed < $now)
                            || ($timeOpen > $now && $timeClosed > $now)
                            || $timeOpen < $now
                            || $timeClosed > $now;
                    }
                    break;
                default:
                    return true;
            }
        }
        return true;
    }

    public static function getPromotion()
    {
        $promotion = settings()->group(self::PROMOTION)->all();

        return $promotion['content'] ?? null;
    }

    public static function extractIframeSrc(?string $raw): string
    {
        if (empty($raw)) {
            return '';
        }

        $trimmed = trim($raw);
        if (preg_match('/<iframe[^>]+src=["\']([^"\']+)["\']/i', $trimmed, $matches)) {
            return trim($matches[1]);
        }

        return $trimmed;
    }

    public static function normalizeGeneralSettings(array $raw = [], ?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $locale === 'en';

        $pickNonEmpty = static function (array $candidates, string $fallback = ''): string {
            foreach ($candidates as $candidate) {
                if (is_string($candidate) && trim($candidate) !== '') {
                    return trim($candidate);
                }
            }
            return $fallback;
        };

        $viBusinessName = $pickNonEmpty([
            $raw['general_business_name_vi'] ?? null,
            $raw['general_business_name'] ?? null,
            $raw['company_name'] ?? null,
        ], 'Thành Khang Tecsat');
        $enBusinessName = $pickNonEmpty([
            $raw['general_business_name_en'] ?? null,
            $raw['company_name_en'] ?? null,
        ], 'Thanh Khang Tecsat');
        $businessName = ($isEn && $enBusinessName !== '') ? $enBusinessName : $viBusinessName;

        $viSiteName = $pickNonEmpty([
            $raw['general_site_name_vi'] ?? null,
            $raw['general_site_name'] ?? null,
            $raw['site_name'] ?? null,
        ], 'Thành Khang Tecsat - Thiết bị công nghiệp & Phụ tùng thang cuốn');
        $enSiteName = $pickNonEmpty([
            $raw['general_site_name_en'] ?? null,
            $raw['site_name_en'] ?? null,
        ], '');
        $siteName = ($isEn && $enSiteName !== '') ? $enSiteName : $viSiteName;

        $phone = $raw['general_phone']
            ?? $raw['phone']
            ?? $raw['hotline']
            ?? '0967732665';

        $email = $raw['general_email']
            ?? $raw['email']
            ?? 'info@dvktthanhkhang.vn';

        $zalo = $raw['general_zalo']
            ?? $raw['zalo']
            ?? $raw['social_zalo']
            ?? 'https://zalo.me/0967732665';

        $viAddress = $pickNonEmpty([
            $raw['general_address_vi'] ?? null,
            $raw['general_address'] ?? null,
            $raw['address'] ?? null,
            $raw['showroom_address'] ?? null,
        ], 'Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội');
        $enAddress = $pickNonEmpty([
            $raw['general_address_en'] ?? null,
            $raw['address_en'] ?? null,
            $raw['showroom_address_en'] ?? null,
        ], '');
        $address = ($isEn && $enAddress !== '') ? $enAddress : ($viAddress ?: '25th Floor, TNR Tower, 54A Nguyen Chi Thanh, Lang Thuong Ward, Dong Da District, Hanoi');

        $viWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_vi'] ?? null,
            $raw['general_working_hours'] ?? null,
            $raw['working_hours'] ?? null,
        ], 'Thứ 2 - Thứ 7: 08:00 - 17:30');
        $enWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_en'] ?? null,
            $raw['working_hours_en'] ?? null,
        ], '');
        $workingHours = ($isEn && $enWorkingHours !== '') ? $enWorkingHours : ($viWorkingHours ?: 'Mon - Sat: 08:00 - 17:30');

        $facebook = $raw['facebook']
            ?? $raw['social_facebook']
            ?? $raw['general_facebook']
            ?? 'https://www.facebook.com/thanhkhangtecsat';

        $messenger = $raw['messenger']
            ?? $raw['social_messenger']
            ?? $raw['general_messenger']
            ?? 'https://m.me/2108952142469502';

        $tiktok = $raw['tiktok']
            ?? $raw['social_tiktok']
            ?? $raw['general_tiktok']
            ?? '';

        $youtube = $raw['youtube']
            ?? $raw['social_youtube']
            ?? $raw['general_youtube']
            ?? '';

        $instagram = $raw['instagram']
            ?? $raw['social_instagram']
            ?? $raw['general_instagram']
            ?? '';

        $mapIframe = self::extractIframeSrc($raw['map_iframe'] ?? $raw['general_map_iframe'] ?? '');

        return array_merge($raw, [
            'general_business_name_vi' => $viBusinessName,
            'general_site_name_vi' => $viSiteName,
            'general_address_vi' => $viAddress,
            'general_working_hours_vi' => $viWorkingHours,
            'general_business_name' => $businessName,
            'company_name' => $businessName,
            'general_site_name' => $siteName,
            'site_name' => $siteName,
            'general_phone' => $phone,
            'phone' => $phone,
            'hotline' => $raw['hotline'] ?? $phone,
            'general_email' => $email,
            'email' => $email,
            'general_zalo' => $zalo,
            'zalo' => $zalo,
            'social_zalo' => $zalo,
            'general_address' => $address,
            'address' => $address,
            'showroom_address' => ($isEn && $enAddress !== '') ? $enAddress : ($raw['showroom_address'] ?? $address),
            'general_working_hours' => $workingHours,
            'working_hours' => $workingHours,
            'general_facebook' => $facebook,
            'facebook' => $facebook,
            'social_facebook' => $facebook,
            'messenger' => $messenger,
            'social_messenger' => $messenger,
            'general_tiktok' => $tiktok,
            'tiktok' => $tiktok,
            'social_tiktok' => $tiktok,
            'youtube' => $youtube,
            'social_youtube' => $youtube,
            'instagram' => $instagram,
            'social_instagram' => $instagram,
            'map_iframe' => $mapIframe,
            'general_map_iframe' => $mapIframe,
        ]);
    }

    public static function getGeneralSettings(?string $locale = null): array
    {
        $raw = [];

        try {
            $settings = settings()->group('general')->all();
            if ($settings instanceof \Illuminate\Support\Collection) {
                $raw = $settings->toArray();
            } elseif (is_array($settings)) {
                $raw = $settings;
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return self::normalizeGeneralSettings($raw, $locale);
    }

    public static function getBrandSettings(): array
    {
        return self::getGeneralSettings();
    }

    public static function getProcessSteps($rawSettingsOrLocale = null, ?string $locale = null): array
    {
        if (is_string($rawSettingsOrLocale)) {
            $locale = $rawSettingsOrLocale;
            $rawSettings = null;
        } elseif (is_array($rawSettingsOrLocale)) {
            $rawSettings = $rawSettingsOrLocale;
        } else {
            $rawSettings = null;
        }

        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $locale === 'en';

        $defaultStepsVi = [
            [
                'number' => '01',
                'title' => 'Tiếp nhận yêu cầu',
                'subtitle' => 'Tiếp nhận thông số & mã phụ tùng',
                'description' => 'Khách hàng gửi mã phụ tùng, bản vẽ kỹ thuật hoặc thông số chi tiết cần thay thế. Kỹ sư Thành Khang Tecsat tiếp nhận và phân tích yêu cầu kỹ thuật.',
                'client_prep' => 'Cung cấp mã phụ tùng, thông số kỹ thuật (bước xích, tải trọng, kích thước) hoặc hình ảnh linh kiện thực tế.',
                'clientPrep' => 'Cung cấp mã phụ tùng, thông số kỹ thuật (bước xích, tải trọng, kích thước) hoặc hình ảnh linh kiện thực tế.',
                'maker_action' => 'Kỹ sư kỹ thuật Thành Khang đối chiếu bản vẽ tiêu chuẩn công nghiệp, kiểm tra khả năng lắp ghép và tư vấn giải pháp tối ưu.',
                'makerAction' => 'Kỹ sư kỹ thuật Thành Khang đối chiếu bản vẽ tiêu chuẩn công nghiệp, kiểm tra khả năng lắp ghép và tư vấn giải pháp tối ưu.',
                'deliverables' => 'Bản xác nhận thông số kỹ thuật & giải pháp phụ tùng phù hợp',
                'quality_standard' => 'Phản hồi trong 2-4 giờ làm việc, chính xác 100% theo tiêu chuẩn lắp ráp',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Báo giá kỹ thuật',
                'subtitle' => 'Báo giá minh bạch & tiến độ cung ứng',
                'description' => 'Thống nhất chủng loại vật liệu, tiêu chuẩn cơ khí và phương án giao nhận trước khi triển khai đơn hàng.',
                'client_prep' => 'Xác nhận số lượng đặt hàng, địa điểm công trình/nhà máy nhận hàng và yêu cầu hồ sơ CO/CQ nếu có.',
                'clientPrep' => 'Xác nhận số lượng đặt hàng, địa điểm công trình/nhà máy nhận hàng và yêu cầu hồ sơ CO/CQ nếu có.',
                'maker_action' => 'Lập bảng báo giá chi tiết, dự toán chi phí tối ưu theo số lượng B2B và cam kết tiến độ giao hàng chuẩn xác.',
                'makerAction' => 'Lập bảng báo giá chi tiết, dự toán chi phí tối ưu theo số lượng B2B và cam kết tiến độ giao hàng chuẩn xác.',
                'deliverables' => 'Bảng báo giá RFQ chính thức & tiến độ cung ứng dự kiến',
                'quality_standard' => 'Giá gốc nhà máy cạnh tranh nhất, minh bạch chi phí vận chuyển & thuế VAT',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Ký kết hợp đồng',
                'subtitle' => 'Xác nhận đơn hàng & bảo lãnh cung ứng',
                'description' => 'Hoàn thiện thủ tục hợp đồng kinh tế, cam kết pháp lý về tiêu chuẩn chất lượng sản phẩm và bảo hành.',
                'client_prep' => 'Cung cấp thông tin pháp nhân công ty, ký kết hợp đồng mua bán và thực hiện tạm ứng theo thỏa thuận.',
                'clientPrep' => 'Cung cấp thông tin pháp nhân công ty, ký kết hợp đồng mua bán và thực hiện tạm ứng theo thỏa thuận.',
                'maker_action' => 'Phát hành hợp đồng mua bán B2B, lên lệnh xuất kho hoặc điều phối sản xuất theo đúng thông số đã chốt.',
                'makerAction' => 'Phát hành hợp đồng mua bán B2B, lên lệnh xuất kho hoặc điều phối sản xuất theo đúng thông số đã chốt.',
                'deliverables' => 'Hợp đồng kinh tế B2B & kế hoạch giao hàng chi tiết',
                'quality_standard' => 'Hợp đồng pháp lý rõ ràng, bảo vệ tối đa quyền lợi khách hàng doanh nghiệp',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Kiểm định & Đóng gói',
                'subtitle' => 'Kiểm định QC nghiêm ngặt & đóng kiện công nghiệp',
                'description' => 'Toàn bộ linh kiện được kiểm tra kích thước, tải trọng thử nghiệm, tra dầu bảo quản và đóng thùng kiện gỗ an toàn.',
                'client_prep' => 'Sắp xếp nhân sự hoặc kỹ thuật viên tiếp nhận hàng hóa tại công trình/nhà xưởng.',
                'clientPrep' => 'Sắp xếp nhân sự hoặc kỹ thuật viên tiếp nhận hàng hóa tại công trình/nhà xưởng.',
                'maker_action' => 'Kiểm tra chất lượng (QC) từng chi tiết, đóng gói chống sốc chống ẩm và hoàn thiện bộ chứng chỉ chất lượng CO/CQ.',
                'makerAction' => 'Kiểm tra chất lượng (QC) từng chi tiết, đóng gói chống sốc chống ẩm và hoàn thiện bộ chứng chỉ chất lượng CO/CQ.',
                'deliverables' => 'Kiện hàng nguyên đai nguyên kiện & bộ hồ sơ CO/CQ xuất xưởng',
                'quality_standard' => 'Đạt 100% tiêu chuẩn xuất xưởng, bảo vệ hoàn hảo trong vận chuyển',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Bàn giao & Bảo hành',
                'subtitle' => 'Giao nhận tại chân công trình & hỗ trợ kỹ thuật',
                'description' => 'Bàn giao vật tư tại hiện trường, hướng dẫn lắp đặt căn chỉnh và kích hoạt chính sách bảo hành kỹ thuật chính hãng.',
                'client_prep' => 'Kiểm tra niêm phong, đối chiếu danh mục hàng hóa theo phiếu xuất kho và ký biên bản giao nhận.',
                'clientPrep' => 'Kiểm tra niêm phong, đối chiếu danh mục hàng hóa theo phiếu xuất kho và ký biên bản giao nhận.',
                'maker_action' => 'Giao hàng đúng hẹn, hướng dẫn kỹ thuật bảo trì định kỳ và sẵn sàng hỗ trợ kỹ thuật 24/7 khi vận hành.',
                'makerAction' => 'Giao hàng đúng hẹn, hướng dẫn kỹ thuật bảo trì định kỳ và sẵn sàng hỗ trợ kỹ thuật 24/7 khi vận hành.',
                'deliverables' => 'Biên bản nghiệm thu bàn giao & phiếu bảo hành chính hãng',
                'quality_standard' => 'Bảo hành 12 - 24 tháng chính hãng, hỗ trợ kỹ thuật trọn vòng đời sản phẩm',
                'isLast' => true,
            ],
        ];

        $defaultStepsEn = [
            [
                'number' => '01',
                'title' => 'Technical Intake',
                'subtitle' => 'Part Number & Specs Survey',
                'description' => 'Clients provide part numbers, technical drawings, or physical dimensions. Our engineers review and verify technical specifications.',
                'client_prep' => 'Provide part numbers, dimensional drawings, pitch/tensile specs, or photos of existing components.',
                'clientPrep' => 'Provide part numbers, dimensional drawings, pitch/tensile specs, or photos of existing components.',
                'maker_action' => 'Thanh Khang engineers cross-check industrial standards, verify dimensional fitment, and advise optimal part specifications.',
                'makerAction' => 'Thanh Khang engineers cross-check industrial standards, verify dimensional fitment, and advise optimal part specifications.',
                'deliverables' => 'Technical verification datasheet & optimal part selection',
                'quality_standard' => 'Response within 2-4 hours, 100% dimensional accuracy',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Technical RFQ Quotation',
                'subtitle' => 'Transparent Pricing & Lead Time',
                'description' => 'Align on metallurgy, industrial standards, and logistics options prior to commercial confirmation.',
                'client_prep' => 'Confirm required quantities, delivery site, and compliance documentation requirements (CO/CQ).',
                'clientPrep' => 'Confirm required quantities, delivery site, and compliance documentation requirements (CO/CQ).',
                'maker_action' => 'Prepare detailed commercial quotation, tiered volume pricing, and committed delivery schedules.',
                'makerAction' => 'Prepare detailed commercial quotation, tiered volume pricing, and committed delivery schedules.',
                'deliverables' => 'Official B2B quotation & committed fulfillment schedule',
                'quality_standard' => 'Competitive factory pricing, transparent freight & VAT terms',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Contract & Order Confirmation',
                'subtitle' => 'Formal Agreement & Procurement',
                'description' => 'Finalize commercial sales contract with strict legal warranties on quality and delivery milestones.',
                'client_prep' => 'Provide legal corporate profile, execute purchase contract, and process initial deposit.',
                'clientPrep' => 'Provide legal corporate profile, execute purchase contract, and process initial deposit.',
                'maker_action' => 'Issue sales agreement, place warehouse dispatch orders or initiate specialized manufacturing.',
                'makerAction' => 'Issue sales agreement, place warehouse dispatch orders or initiate specialized manufacturing.',
                'deliverables' => 'Executed purchase contract & dispatch milestone schedule',
                'quality_standard' => 'Transparent legal terms safeguarding customer interests',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'QC & Industrial Packaging',
                'subtitle' => 'Rigorous Inspection & Industrial Crating',
                'description' => 'All components undergo dimensional verification, proof-load testing, anti-rust oiling, and heavy-duty wooden crating.',
                'client_prep' => 'Arrange receiving personnel or engineering staff at the job site or warehouse.',
                'clientPrep' => 'Arrange receiving personnel or engineering staff at the job site or warehouse.',
                'maker_action' => 'Perform QC checks, pack with moisture and shock protection, and prepare CO/CQ certificates.',
                'makerAction' => 'Perform QC checks, pack with moisture and shock protection, and prepare CO/CQ certificates.',
                'deliverables' => 'Factory-sealed crates & complete certification dossier',
                'quality_standard' => '100% factory inspection pass rate, zero transit damage',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Handover & Warranty Support',
                'subtitle' => 'Site Delivery & Technical Support',
                'description' => 'Site delivery, alignment and installation guidance, and activation of genuine technical warranty.',
                'client_prep' => 'Inspect seals, verify packing list against delivery docket, and sign handover acceptance report.',
                'clientPrep' => 'Inspect seals, verify packing list against delivery docket, and sign handover acceptance report.',
                'maker_action' => 'Deliver on schedule, provide preventive maintenance instructions, and deliver 24/7 engineering support.',
                'makerAction' => 'Deliver on schedule, provide preventive maintenance instructions, and deliver 24/7 engineering support.',
                'deliverables' => 'Acceptance handover protocol & official warranty certificate',
                'quality_standard' => '12 to 24-month warranty, lifetime technical advisory',
                'isLast' => true,
            ],
        ];

        $defaultSteps = $isEn ? $defaultStepsEn : $defaultStepsVi;

        try {
            $general = $rawSettings;
            if ($general === null) {
                $settings = settings()->group('general')->all();
                if ($settings instanceof \Illuminate\Support\Collection) {
                    $general = $settings->toArray();
                } elseif (is_array($settings)) {
                    $general = $settings;
                } else {
                    $general = [];
                }
            }

            $decodeSteps = static function ($value): ?array {
                if (is_string($value) && trim($value) !== '') {
                    $decoded = json_decode($value, true);
                    return is_array($decoded) ? $decoded : null;
                }
                return is_array($value) ? $value : null;
            };

            $rawStepsVi = $decodeSteps($general['process_steps'] ?? $general['steps'] ?? null);
            $rawStepsEn = $decodeSteps($general['process_steps_en'] ?? $general['steps_en'] ?? null);

            $baseSteps = (is_array($rawStepsVi) && count($rawStepsVi) > 0)
                ? $rawStepsVi
                : ((is_array($rawStepsEn) && count($rawStepsEn) > 0) ? $rawStepsEn : null);

            if (is_array($baseSteps) && count($baseSteps) > 0) {
                $total = count($baseSteps);
                return array_values(array_map(function ($item, $index) use ($total, $defaultSteps, $defaultStepsVi, $rawStepsVi, $rawStepsEn, $isEn) {
                    $itemVi = is_array($rawStepsVi[$index] ?? null) ? $rawStepsVi[$index] : (is_array($item) ? $item : []);
                    $itemEn = is_array($rawStepsEn[$index] ?? null) ? $rawStepsEn[$index] : [];
                    $fallback = $defaultSteps[$index] ?? ($defaultStepsVi[$index] ?? []);
                    $fallbackVi = $defaultStepsVi[$index] ?? [];

                    $resolveField = static function (string $key, ?string $snakeKey = null) use ($itemVi, $itemEn, $fallback, $fallbackVi, $isEn): string {
                        $viVal = trim((string) ($itemVi[$key] ?? ($snakeKey ? ($itemVi[$snakeKey] ?? '') : '')));
                        $enVal = trim((string) (
                            $itemEn[$key]
                            ?? ($snakeKey ? ($itemEn[$snakeKey] ?? null) : null)
                            ?? $itemVi[$key . '_en']
                            ?? ($snakeKey ? ($itemVi[$snakeKey . '_en'] ?? '') : '')
                        ));

                        if ($isEn) {
                            if ($enVal !== '') {
                                return $enVal;
                            }
                            if ($viVal !== '') {
                                return $viVal;
                            }
                            return (string) ($fallback[$key] ?? ($fallbackVi[$key] ?? ($fallback[$snakeKey] ?? '')));
                        }

                        if ($viVal !== '') {
                            return $viVal;
                        }
                        return (string) ($fallbackVi[$key] ?? ($fallback[$key] ?? ($fallbackVi[$snakeKey] ?? '')));
                    };

                    $number = $itemVi['number'] ?? $itemEn['number'] ?? ($fallback['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT));
                    $clientPrep = $resolveField('clientPrep', 'client_prep');
                    $makerAction = $resolveField('makerAction', 'maker_action');

                    return [
                        'number' => $number,
                        'title' => $resolveField('title'),
                        'subtitle' => $resolveField('subtitle'),
                        'description' => $resolveField('description'),
                        'client_prep' => $clientPrep,
                        'clientPrep' => $clientPrep,
                        'maker_action' => $makerAction,
                        'makerAction' => $makerAction,
                        'deliverables' => $resolveField('deliverables'),
                        'quality_standard' => $resolveField('quality_standard', 'qualityStandard'),
                        'isLast' => isset($itemVi['isLast']) ? (bool) $itemVi['isLast'] : ($index === $total - 1),
                    ];
                }, $baseSteps, array_keys($baseSteps)));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $defaultSteps;
    }
}
