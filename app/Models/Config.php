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
        ], 'HH Maker');
        $enBusinessName = $pickNonEmpty([
            $raw['general_business_name_en'] ?? null,
            $raw['company_name_en'] ?? null,
        ]);
        $businessName = ($isEn && $enBusinessName !== '') ? $enBusinessName : $viBusinessName;

        $viSiteName = $pickNonEmpty([
            $raw['general_site_name_vi'] ?? null,
            $raw['general_site_name'] ?? null,
            $raw['site_name'] ?? null,
        ], 'HH Maker - Dịch vụ Thiết kế & Gia công In 3D');
        $enSiteName = $pickNonEmpty([
            $raw['general_site_name_en'] ?? null,
            $raw['site_name_en'] ?? null,
        ]);
        $siteName = ($isEn && $enSiteName !== '') ? $enSiteName : $viSiteName;

        $phone = $raw['general_phone']
            ?? $raw['phone']
            ?? $raw['hotline']
            ?? '0906636425';

        $email = $raw['general_email']
            ?? $raw['email']
            ?? 'info@hhmaker.vn';

        $zalo = $raw['general_zalo']
            ?? $raw['zalo']
            ?? $raw['social_zalo']
            ?? 'https://zalo.me/0906636425';

        $viAddress = $pickNonEmpty([
            $raw['general_address_vi'] ?? null,
            $raw['general_address'] ?? null,
            $raw['address'] ?? null,
            $raw['showroom_address'] ?? null,
        ], 'Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh');
        $enAddress = $pickNonEmpty([
            $raw['general_address_en'] ?? null,
            $raw['address_en'] ?? null,
            $raw['showroom_address_en'] ?? null,
        ]);
        $address = ($isEn && $enAddress !== '') ? $enAddress : $viAddress;

        $viWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_vi'] ?? null,
            $raw['general_working_hours'] ?? null,
            $raw['working_hours'] ?? null,
        ], 'Thứ 2 - Thứ 7: 08:00 - 18:00');
        $enWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_en'] ?? null,
            $raw['working_hours_en'] ?? null,
        ]);
        $workingHours = ($isEn && $enWorkingHours !== '') ? $enWorkingHours : $viWorkingHours;

        $facebook = $raw['facebook']
            ?? $raw['social_facebook']
            ?? $raw['general_facebook']
            ?? 'https://www.facebook.com/hhmaker';

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

    public static function getLuxiBrandSettings(): array
    {
        return self::getGeneralSettings();
    }

    public static function getProcessSteps(?array $rawSettings = null): array
    {
        $locale = function_exists('current_locale') ? current_locale() : app()->getLocale();
        $isEn = $locale === 'en';

        $defaultStepsVi = [
            [
                'number' => '01',
                'title' => 'Liên hệ',
                'subtitle' => 'Tiếp nhận & làm rõ nhu cầu',
                'description' => 'Mọi sản phẩm bắt đầu bằng một cuộc trao đổi. HH Maker tiếp nhận ý tưởng và làm rõ nhu cầu trước khi đề xuất hướng thực hiện.',
                'clientPrep' => 'Gửi ý tưởng, ảnh tham khảo hoặc file 3D; nêu mục đích sử dụng, kích thước và số lượng dự kiến.',
                'makerAction' => 'Xem xét tính khả thi, đặt câu hỏi để hiểu yêu cầu và tư vấn hướng thiết kế, vật liệu, cách gia công.',
                'deliverables' => 'Tư vấn giải pháp kỹ thuật & định hướng vật liệu',
                'quality_standard' => 'Phản hồi nhanh chóng, nắm bắt đúng trọng tâm ý tưởng',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Báo giá',
                'subtitle' => 'Phương án & chi phí minh bạch',
                'description' => 'Thống nhất những điều quan trọng trước khi bắt tay vào chế tác: phạm vi công việc, yêu cầu sản phẩm và phương án thực hiện.',
                'clientPrep' => 'Xác nhận thiết kế, kích thước, số lượng và các yêu cầu hoàn thiện; phản hồi những điểm cần điều chỉnh.',
                'makerAction' => 'Tổng hợp phương án và báo giá theo yêu cầu thực tế. Trao đổi các hạng mục trước khi khách hàng xác nhận triển khai.',
                'deliverables' => 'Bảng báo giá chi tiết & tiến độ dự kiến',
                'quality_standard' => 'Minh bạch hạng mục, tối ưu chi phí theo nhu cầu thực tế',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Gia công 3D',
                'subtitle' => 'Chế tác & hoàn thiện chi tiết',
                'description' => 'Từ mô hình số đến sản phẩm vật lý. Thiết kế, vật liệu và quy trình in được kết nối trong từng chi tiết.',
                'clientPrep' => 'Xác nhận các điều chỉnh kỹ thuật nếu có để phương án gia công phù hợp với nhu cầu đã trao đổi.',
                'makerAction' => 'Chuẩn bị mô hình, thực hiện in 3D và hoàn thiện theo phương án đã chốt; kiểm tra hình dáng, bề mặt và chi tiết sản phẩm.',
                'deliverables' => 'Sản phẩm hoàn thiện theo đúng thông số đã duyệt',
                'quality_standard' => 'Chuẩn xác kích thước, bề mặt mịn và kết cấu vững chắc',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Đóng gói',
                'subtitle' => 'Kiểm định & bảo vệ an toàn',
                'description' => 'Sản phẩm sau gia công được kiểm tra và chuẩn bị cho hành trình vận chuyển.',
                'clientPrep' => 'Cung cấp thông tin người nhận và lưu ý về việc giao nhận hoặc cách đóng gói nếu có yêu cầu riêng.',
                'makerAction' => 'Kiểm tra sản phẩm, sắp xếp các chi tiết và lựa chọn cách bảo vệ, đóng gói phù hợp với hình dáng của từng món đồ.',
                'deliverables' => 'Kiện hàng đóng gói chống sốc tiêu chuẩn',
                'quality_standard' => 'Bảo vệ tối đa các chi tiết mảnh và bề mặt hoàn thiện',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Gửi hàng',
                'subtitle' => 'Bàn giao & đồng hành sau nhận hàng',
                'description' => 'Khép lại quá trình chế tác, mở đầu trải nghiệm sử dụng sản phẩm của bạn.',
                'clientPrep' => 'Xác nhận địa chỉ, số điện thoại nhận hàng; kiểm tra sản phẩm khi nhận và trao đổi nếu cần hỗ trợ.',
                'makerAction' => 'Bàn giao kiện hàng để vận chuyển, cung cấp thông tin gửi hàng và tiếp nhận phản hồi sau khi khách hàng nhận sản phẩm.',
                'deliverables' => 'Bàn giao sản phẩm & mã vận đơn theo dõi',
                'quality_standard' => 'Giao hàng đúng hẹn, hỗ trợ tận tâm sau bàn giao',
                'isLast' => true,
            ],
        ];

        $defaultStepsEn = [
            [
                'number' => '01',
                'title' => 'Contact',
                'subtitle' => 'Initial Consultation & Requirement Clarification',
                'description' => 'Every product begins with a conversation. HH Maker receives your idea and clarifies requirements before proposing an execution direction.',
                'clientPrep' => 'Send your idea, reference images or 3D files; state intended use, dimensions, and estimated quantity.',
                'makerAction' => 'Evaluate feasibility, ask clarifying questions, and advise on design direction, materials, and fabrication methods.',
                'deliverables' => 'Technical consultation & material direction',
                'quality_standard' => 'Prompt response & accurate understanding of your concept',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Quotation',
                'subtitle' => 'Transparent Plan & Pricing',
                'description' => 'Align on key details before crafting begins: scope of work, product specifications, and execution plan.',
                'clientPrep' => 'Confirm design, dimensions, quantity, and finishing requirements; provide feedback on any needed adjustments.',
                'makerAction' => 'Compile the execution plan and quotation based on actual requirements. Discuss all items before confirming production.',
                'deliverables' => 'Detailed quotation & estimated timeline',
                'quality_standard' => 'Transparent breakdown, cost-optimized for actual needs',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => '3D Fabrication',
                'subtitle' => 'Precision Printing & Finishing',
                'description' => 'From digital model to physical product. Design, materials, and printing processes come together in every detail.',
                'clientPrep' => 'Confirm any technical adjustments so the fabrication plan matches the agreed requirements.',
                'makerAction' => 'Prepare the model, execute 3D printing and post-processing per the approved plan; inspect shape, surface, and fine details.',
                'deliverables' => 'Finished product matching approved specifications',
                'quality_standard' => 'Dimensional accuracy, smooth surface, and structural integrity',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Packaging',
                'subtitle' => 'Inspection & Protective Packaging',
                'description' => 'Fabricated products are carefully inspected and prepared for safe transit.',
                'clientPrep' => 'Provide recipient details and any special delivery or packaging notes if needed.',
                'makerAction' => 'Inspect the product, arrange components, and select protective packaging tailored to each item\'s geometry.',
                'deliverables' => 'Shock-resistant packaged parcel',
                'quality_standard' => 'Maximum protection for delicate parts and finished surfaces',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Delivery',
                'subtitle' => 'Handover & Post-Delivery Support',
                'description' => 'Completing the crafting journey and beginning your experience with the product.',
                'clientPrep' => 'Confirm shipping address and phone number; inspect the product upon arrival and reach out if support is needed.',
                'makerAction' => 'Hand over the parcel for shipping, provide tracking details, and follow up after you receive the product.',
                'deliverables' => 'Product handover & tracking information',
                'quality_standard' => 'On-time delivery & dedicated post-handover support',
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
                            return (string) ($fallback[$key] ?? ($fallbackVi[$key] ?? ''));
                        }

                        if ($viVal !== '') {
                            return $viVal;
                        }
                        return (string) ($fallbackVi[$key] ?? ($fallback[$key] ?? ''));
                    };

                    $number = $itemVi['number'] ?? $itemEn['number'] ?? ($fallback['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT));

                    return [
                        'number' => $number,
                        'title' => $resolveField('title'),
                        'subtitle' => $resolveField('subtitle'),
                        'description' => $resolveField('description'),
                        'clientPrep' => $resolveField('clientPrep', 'client_prep'),
                        'makerAction' => $resolveField('makerAction', 'maker_action'),
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
