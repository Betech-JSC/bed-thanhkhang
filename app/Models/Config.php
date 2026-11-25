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
                'general_logo' => 'nullable',
                'logo' => 'nullable',
                'general_business_name' => 'nullable',
                'general_business_name_en' => 'nullable',
                'general_site_name' => 'nullable',
                'general_site_name_en' => 'nullable',
                'general_description' => 'nullable|string',
                'general_description_en' => 'nullable|string',
                'description' => 'nullable|string',
                'description_en' => 'nullable|string',
                'general_email' => 'nullable',
                'general_address' => 'nullable',
                'general_address_en' => 'nullable',
                'general_phone' => 'nullable',
                'general_zalo' => 'nullable',
                'general_working_hours' => 'nullable',
                'general_working_hours_en' => 'nullable',
                'general_facebook' => 'nullable',
                'general_linkedin' => 'nullable|string|max:500',
                'linkedin' => 'nullable|string|max:500',
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
                'showroom_address' => 'nullable|string|max:500',
                'showroom_address_en' => 'nullable|string|max:500',
                'working_hours' => 'nullable',
                'working_hours_en' => 'nullable',
                'zalo' => 'nullable',
                'facebook' => 'nullable',
                'tiktok' => 'nullable',
                'home_about_eyebrow' => 'nullable|string|max:255',
                'home_about_eyebrow_en' => 'nullable|string|max:255',
                'home_about_lead_highlight' => 'nullable|string|max:500',
                'home_about_lead_highlight_en' => 'nullable|string|max:500',
                'home_about_lead_rest' => 'nullable|string|max:1000',
                'home_about_lead_rest_en' => 'nullable|string|max:1000',
                'home_about_paragraph_1' => 'nullable|string',
                'home_about_paragraph_1_en' => 'nullable|string',
                'home_about_paragraph_2' => 'nullable|string',
                'home_about_paragraph_2_en' => 'nullable|string',
                'home_about_commitment_quote' => 'nullable|string|max:500',
                'home_about_commitment_quote_en' => 'nullable|string|max:500',
                'home_about_commitment_sub' => 'nullable|string|max:500',
                'home_about_commitment_sub_en' => 'nullable|string|max:500',
                'home_about_cta_text' => 'nullable|string|max:255',
                'home_about_cta_text_en' => 'nullable|string|max:255',
                'home_about_cta_link' => 'nullable|string|max:500',
                'home_about_cta_link_en' => 'nullable|string|max:500',
                'home_contact_title' => 'nullable|string|max:500',
                'home_contact_title_en' => 'nullable|string|max:500',
                'home_contact_description' => 'nullable|string',
                'home_contact_description_en' => 'nullable|string',
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

    public static function resolveMediaSettingUrl($raw): string
    {
        if (empty($raw)) {
            return '';
        }

        if (is_string($raw)) {
            $trimmed = trim($raw);
            if ($trimmed === '') {
                return '';
            }
            if (str_starts_with($trimmed, '{') || str_starts_with($trimmed, '[')) {
                $decoded = json_decode($trimmed, true);
                if (is_array($decoded)) {
                    $raw = $decoded;
                } else {
                    return '';
                }
            } else {
                return $trimmed;
            }
        }

        if (is_object($raw)) {
            $raw = (array) $raw;
        }

        if (is_array($raw)) {
            if (!empty($raw['url']) && is_string($raw['url'])) {
                return trim($raw['url']);
            }
            if (!empty($raw['static_url']) && is_string($raw['static_url'])) {
                return trim($raw['static_url']);
            }
            if (!empty($raw['path']) && is_string($raw['path'])) {
                $path = trim($raw['path']);
                if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                    return $path;
                }
                return function_exists('static_url') ? (string) static_url($path) : '/' . ltrim($path, '/');
            }
        }

        return '';
    }

    public static function normalizeGeneralSettings(array $raw = [], ?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $locale === 'en';

        $pickNonEmpty = static function (array $candidates): string {
            foreach ($candidates as $candidate) {
                if (is_string($candidate) && trim($candidate) !== '') {
                    return trim($candidate);
                }
            }
            return '';
        };

        $logo = self::resolveMediaSettingUrl($raw['general_logo'] ?? $raw['logo'] ?? null);

        $viBusinessName = $pickNonEmpty([
            $raw['general_business_name_vi'] ?? null,
            $raw['general_business_name'] ?? null,
            $raw['company_name'] ?? null,
        ]);
        $enBusinessName = $pickNonEmpty([
            $raw['general_business_name_en'] ?? null,
            $raw['company_name_en'] ?? null,
        ]);
        $businessName = ($isEn && $enBusinessName !== '') ? $enBusinessName : $viBusinessName;

        $viSiteName = $pickNonEmpty([
            $raw['general_site_name_vi'] ?? null,
            $raw['general_site_name'] ?? null,
            $raw['site_name'] ?? null,
        ]);
        $enSiteName = $pickNonEmpty([
            $raw['general_site_name_en'] ?? null,
            $raw['site_name_en'] ?? null,
        ]);
        $siteName = ($isEn && $enSiteName !== '') ? $enSiteName : $viSiteName;

        $viDescription = $pickNonEmpty([
            $raw['general_description_vi'] ?? null,
            $raw['general_description'] ?? null,
            $raw['description'] ?? null,
        ]);
        $enDescription = $pickNonEmpty([
            $raw['general_description_en'] ?? null,
            $raw['description_en'] ?? null,
        ]);
        $description = ($isEn && $enDescription !== '') ? $enDescription : $viDescription;

        $phone = $pickNonEmpty([
            $raw['general_phone'] ?? null,
            $raw['phone'] ?? null,
            $raw['hotline'] ?? null,
        ]);

        $hotline = $pickNonEmpty([
            $raw['hotline'] ?? null,
            $phone,
        ]);

        // Display VN 10-digit numbers as "0967 732 665"; anything else is left exactly as entered.
        $fmtPhone = fn ($p) => preg_match('/^0\d{9}$/', $d = preg_replace('/\D/', '', (string) $p))
            ? preg_replace('/^(\d{4})(\d{3})(\d{3})$/', '$1 $2 $3', $d)
            : $p;
        $phone = $fmtPhone($phone);
        $hotline = $fmtPhone($hotline);

        $email = $pickNonEmpty([
            $raw['general_email'] ?? null,
            $raw['email'] ?? null,
        ]);

        $zalo = $pickNonEmpty([
            $raw['general_zalo'] ?? null,
            $raw['zalo'] ?? null,
            $raw['social_zalo'] ?? null,
        ]);

        $viAddress = $pickNonEmpty([
            $raw['general_address_vi'] ?? null,
            $raw['general_address'] ?? null,
            $raw['address'] ?? null,
            $raw['showroom_address'] ?? null,
        ]);
        $enAddress = $pickNonEmpty([
            $raw['general_address_en'] ?? null,
            $raw['address_en'] ?? null,
            $raw['showroom_address_en'] ?? null,
        ]);
        $address = ($isEn && $enAddress !== '') ? $enAddress : $viAddress;

        $viShowroomAddress = $pickNonEmpty([
            $raw['showroom_address'] ?? null,
            $raw['office_address'] ?? null,
            $viAddress,
        ]);
        $enShowroomAddress = $pickNonEmpty([
            $raw['showroom_address_en'] ?? null,
            $raw['office_address_en'] ?? null,
            $enAddress,
        ]);
        $showroomAddress = ($isEn && $enShowroomAddress !== '') ? $enShowroomAddress : $viShowroomAddress;

        $viWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_vi'] ?? null,
            $raw['general_working_hours'] ?? null,
            $raw['working_hours'] ?? null,
        ]);
        $enWorkingHours = $pickNonEmpty([
            $raw['general_working_hours_en'] ?? null,
            $raw['working_hours_en'] ?? null,
        ]);
        $workingHours = ($isEn && $enWorkingHours !== '') ? $enWorkingHours : $viWorkingHours;

        $facebook = $pickNonEmpty([
            $raw['general_facebook'] ?? null,
            $raw['facebook'] ?? null,
            $raw['social_facebook'] ?? null,
        ]);

        $linkedin = $pickNonEmpty([
            $raw['general_linkedin'] ?? null,
            $raw['linkedin'] ?? null,
            $raw['social_linkedin'] ?? null,
        ]);

        $messenger = $pickNonEmpty([
            $raw['general_messenger'] ?? null,
            $raw['messenger'] ?? null,
            $raw['social_messenger'] ?? null,
        ]);

        $tiktok = $pickNonEmpty([
            $raw['general_tiktok'] ?? null,
            $raw['tiktok'] ?? null,
            $raw['social_tiktok'] ?? null,
        ]);

        $youtube = $pickNonEmpty([
            $raw['general_youtube'] ?? null,
            $raw['youtube'] ?? null,
            $raw['social_youtube'] ?? null,
        ]);

        $instagram = $pickNonEmpty([
            $raw['general_instagram'] ?? null,
            $raw['instagram'] ?? null,
            $raw['social_instagram'] ?? null,
        ]);

        $mapIframe = self::extractIframeSrc($raw['map_iframe'] ?? $raw['general_map_iframe'] ?? '');

        return array_merge($raw, [
            'general_logo' => $logo,
            'logo' => $logo,
            'general_business_name_vi' => $viBusinessName,
            'general_site_name_vi' => $viSiteName,
            'general_description_vi' => $viDescription,
            'general_address_vi' => $viAddress,
            'general_working_hours_vi' => $viWorkingHours,
            'general_business_name' => $businessName,
            'company_name' => $businessName,
            'general_site_name' => $siteName,
            'site_name' => $siteName,
            'general_description' => $description,
            'description' => $description,
            'general_phone' => $phone,
            'phone' => $phone,
            'hotline' => $hotline,
            'general_email' => $email,
            'email' => $email,
            'general_zalo' => $zalo,
            'zalo' => $zalo,
            'social_zalo' => $zalo,
            'general_address' => $address,
            'address' => $address,
            'showroom_address' => $showroomAddress,
            'general_working_hours' => $workingHours,
            'working_hours' => $workingHours,
            'general_facebook' => $facebook,
            'facebook' => $facebook,
            'social_facebook' => $facebook,
            'general_linkedin' => $linkedin,
            'linkedin' => $linkedin,
            'social_linkedin' => $linkedin,
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

    public static function getHomeAboutSettings(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $locale === 'en';

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

        $resolve = static function (string $key) use ($raw, $isEn): string {
            $viVal = trim((string) ($raw[$key] ?? ''));
            $enVal = trim((string) ($raw[$key . '_en'] ?? ''));

            if ($isEn) {
                return $enVal !== '' ? $enVal : $viVal;
            }

            return $viVal;
        };

        $paragraphs = array_values(array_filter([
            $resolve('home_about_paragraph_1'),
            $resolve('home_about_paragraph_2'),
        ], static fn(string $p): bool => $p !== ''));

        return [
            'eyebrow' => $resolve('home_about_eyebrow'),
            'lead_highlight' => $resolve('home_about_lead_highlight'),
            'lead_rest' => $resolve('home_about_lead_rest'),
            'paragraphs' => $paragraphs,
            'commitment_quote' => $resolve('home_about_commitment_quote'),
            'commitment_sub' => $resolve('home_about_commitment_sub'),
            'cta_text' => $resolve('home_about_cta_text'),
            'cta_link' => $resolve('home_about_cta_link'),
        ];
    }

    public static function getHomeContactSettings(?string $locale = null): array
    {
        $locale = $locale ?: (function_exists('current_locale') ? current_locale() : app()->getLocale());
        $isEn = $locale === 'en';

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

        $resolve = static function (string $key) use ($raw, $isEn): string {
            $viVal = trim((string) ($raw[$key] ?? ''));
            $enVal = trim((string) ($raw[$key . '_en'] ?? ''));

            if ($isEn) {
                return $enVal !== '' ? $enVal : $viVal;
            }

            return $viVal;
        };

        return [
            'title' => $resolve('home_contact_title'),
            'description' => $resolve('home_contact_description'),
        ];
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

        try {
            $general = $rawSettings;
            if ($general === null) {
                if (!\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                    return [];
                }
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
                return array_values(array_map(function ($item, $index) use ($total, $rawStepsVi, $rawStepsEn, $isEn) {
                    $itemVi = is_array($rawStepsVi[$index] ?? null) ? $rawStepsVi[$index] : (is_array($item) ? $item : []);
                    $itemEn = is_array($rawStepsEn[$index] ?? null) ? $rawStepsEn[$index] : [];

                    $resolveField = static function (string $key, ?string $snakeKey = null) use ($itemVi, $itemEn, $isEn): string {
                        $viVal = trim((string) ($itemVi[$key] ?? ($snakeKey ? ($itemVi[$snakeKey] ?? '') : '')));
                        $enVal = trim((string) (
                            $itemEn[$key]
                            ?? ($snakeKey ? ($itemEn[$snakeKey] ?? null) : null)
                            ?? $itemVi[$key . '_en']
                            ?? ($snakeKey ? ($itemVi[$snakeKey . '_en'] ?? '') : '')
                        ));

                        if ($isEn) {
                            return $enVal !== '' ? $enVal : $viVal;
                        }

                        return $viVal;
                    };

                    $number = $itemVi['number'] ?? $itemEn['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
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

        return [];
    }
}
