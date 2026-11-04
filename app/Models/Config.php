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
                'general_site_name' => 'nullable',
                'general_email' => 'nullable',
                'general_address' => 'nullable',
                'general_phone' => 'nullable',
                'general_zalo' => 'nullable',
                'general_working_hours' => 'nullable',
                'general_facebook' => 'nullable',
                'general_tiktok' => 'nullable',
                'map_iframe' => 'nullable',
                'general_map_iframe' => 'nullable',
                'company_name' => 'nullable',
                'site_name' => 'nullable',
                'hotline' => 'nullable',
                'phone' => 'nullable',
                'email' => 'nullable',
                'address' => 'nullable',
                'showroom_address' => 'nullable',
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

    public static function normalizeGeneralSettings(array $raw = []): array
    {
        $businessName = $raw['general_business_name']
            ?? $raw['company_name']
            ?? 'HH Maker';

        $siteName = $raw['general_site_name']
            ?? $raw['site_name']
            ?? 'HH Maker - Dịch vụ Thiết kế & Gia công In 3D';

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

        $address = $raw['general_address']
            ?? $raw['address']
            ?? $raw['showroom_address']
            ?? 'Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh';

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
            'showroom_address' => $raw['showroom_address'] ?? $address,
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

    public static function getGeneralSettings(): array
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

        return self::normalizeGeneralSettings($raw);
    }

    public static function getBrandSettings(): array
    {
        return self::getGeneralSettings();
    }

    public static function getLuxiBrandSettings(): array
    {
        return self::getGeneralSettings();
    }

    public static function getProcessSteps(): array
    {
        $defaultSteps = [
            [
                'number' => '01',
                'title' => 'Liên hệ',
                'description' => 'Mọi sản phẩm bắt đầu bằng một cuộc trao đổi. HH Maker tiếp nhận ý tưởng và làm rõ nhu cầu trước khi đề xuất hướng thực hiện.',
                'clientPrep' => 'Gửi ý tưởng, ảnh tham khảo hoặc file 3D; nêu mục đích sử dụng, kích thước và số lượng dự kiến.',
                'makerAction' => 'Xem xét tính khả thi, đặt câu hỏi để hiểu yêu cầu và tư vấn hướng thiết kế, vật liệu, cách gia công.',
                'isLast' => false,
            ],
            [
                'number' => '02',
                'title' => 'Báo giá',
                'description' => 'Thống nhất những điều quan trọng trước khi bắt tay vào chế tác: phạm vi công việc, yêu cầu sản phẩm và phương án thực hiện.',
                'clientPrep' => 'Xác nhận thiết kế, kích thước, số lượng và các yêu cầu hoàn thiện; phản hồi những điểm cần điều chỉnh.',
                'makerAction' => 'Tổng hợp phương án và báo giá theo yêu cầu thực tế. Trao đổi các hạng mục trước khi khách hàng xác nhận triển khai.',
                'isLast' => false,
            ],
            [
                'number' => '03',
                'title' => 'Gia công 3D',
                'description' => 'Từ mô hình số đến sản phẩm vật lý. Thiết kế, vật liệu và quy trình in được kết nối trong từng chi tiết.',
                'clientPrep' => 'Xác nhận các điều chỉnh kỹ thuật nếu có để phương án gia công phù hợp với nhu cầu đã trao đổi.',
                'makerAction' => 'Chuẩn bị mô hình, thực hiện in 3D và hoàn thiện theo phương án đã chốt; kiểm tra hình dáng, bề mặt và chi tiết sản phẩm.',
                'isLast' => false,
            ],
            [
                'number' => '04',
                'title' => 'Đóng gói',
                'description' => 'Sản phẩm sau gia công được kiểm tra và chuẩn bị cho hành trình vận chuyển.',
                'clientPrep' => 'Cung cấp thông tin người nhận và lưu ý về việc giao nhận hoặc cách đóng gói nếu có yêu cầu riêng.',
                'makerAction' => 'Kiểm tra sản phẩm, sắp xếp các chi tiết và lựa chọn cách bảo vệ, đóng gói phù hợp với hình dáng của từng món đồ.',
                'isLast' => false,
            ],
            [
                'number' => '05',
                'title' => 'Gửi hàng',
                'description' => 'Khép lại quá trình chế tác, mở đầu trải nghiệm sử dụng sản phẩm của bạn.',
                'clientPrep' => 'Xác nhận địa chỉ, số điện thoại nhận hàng; kiểm tra sản phẩm khi nhận và trao đổi nếu cần hỗ trợ.',
                'makerAction' => 'Bàn giao kiện hàng để vận chuyển, cung cấp thông tin gửi hàng và tiếp nhận phản hồi sau khi khách hàng nhận sản phẩm.',
                'isLast' => true,
            ],
        ];

        try {
            $general = settings()->group('general')->all();
            $rawSteps = $general['process_steps'] ?? $general['steps'] ?? null;

            if (is_string($rawSteps) && !empty($rawSteps)) {
                $decoded = json_decode($rawSteps, true);
                if (is_array($decoded)) {
                    $rawSteps = $decoded;
                }
            }

            if (is_array($rawSteps) && count($rawSteps) > 0) {
                $total = count($rawSteps);
                return array_values(array_map(function ($item, $index) use ($total, $defaultSteps) {
                    $fallback = $defaultSteps[$index] ?? [];
                    $number = $item['number'] ?? ($fallback['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT));
                    return [
                        'number' => $number,
                        'title' => $item['title'] ?? ($fallback['title'] ?? ''),
                        'description' => $item['description'] ?? ($fallback['description'] ?? ''),
                        'clientPrep' => $item['clientPrep'] ?? $item['client_prep'] ?? ($fallback['clientPrep'] ?? ''),
                        'makerAction' => $item['makerAction'] ?? $item['maker_action'] ?? ($fallback['makerAction'] ?? ''),
                        'isLast' => isset($item['isLast']) ? (bool) $item['isLast'] : ($index === $total - 1),
                    ];
                }, $rawSteps, array_keys($rawSteps)));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return $defaultSteps;
    }
}
