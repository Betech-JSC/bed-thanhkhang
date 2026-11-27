<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends Controller
{
    protected function normalizeLocalAssetUrl(?string $url): ?string
    {
        if (!is_string($url) || trim($url) === '') {
            return null;
        }
        $url = trim($url);
        if (str_starts_with($url, '/')) {
            return $url;
        }
        $parsedPath = parse_url($url, PHP_URL_PATH);
        if (
            is_string($parsedPath) &&
            (str_starts_with($parsedPath, '/assets/') ||
                str_starts_with($parsedPath, '/images/') ||
                str_starts_with($parsedPath, '/static/'))
        ) {
            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
            $reqHost = request()?->getHost();
            $urlHost = parse_url($url, PHP_URL_HOST);
            if (in_array($urlHost, array_filter([$appHost, $reqHost, 'localhost', '127.0.0.1']), true)) {
                return $parsedPath;
            }
        }
        if (str_starts_with($url, 'assets/') || str_starts_with($url, 'images/') || str_starts_with($url, 'static/')) {
            return '/' . $url;
        }
        return $url;
    }

    protected function sanitizeBannerLink($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }
        $trimmed = trim($value);
        if ($trimmed === '' || $trimmed === '#') {
            return null;
        }
        return $trimmed;
    }

    protected function normalizeSliderBanner($rawBanner): ?array
    {
        if ($rawBanner instanceof \Illuminate\Support\Collection) {
            $rawBanner = $rawBanner->first();
        } elseif (is_array($rawBanner) && array_is_list($rawBanner)) {
            $rawBanner = $rawBanner[0] ?? null;
        }

        if (!is_array($rawBanner) || empty($rawBanner)) {
            return null;
        }

        $img = is_array($rawBanner['image'] ?? null) ? $rawBanner['image'] : [];
        $imgMobile = is_array($rawBanner['image_mobile'] ?? null) ? $rawBanner['image_mobile'] : [];
        $fallbackAlt = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($rawBanner['title'] ?? ''))));
        $desktopUrl = $this->normalizeLocalAssetUrl($img['url'] ?? null);
        $mobileUrl = $this->normalizeLocalAssetUrl($imgMobile['url'] ?? null) ?? $desktopUrl;
        $link = $this->sanitizeBannerLink($rawBanner['link'] ?? null);
        $primaryCtaLink = $this->sanitizeBannerLink($rawBanner['primary_cta_link'] ?? null) ?? $link;
        $secondaryCtaLink = $this->sanitizeBannerLink($rawBanner['secondary_cta_link'] ?? null);

        return [
            'id' => $rawBanner['id'] ?? null,
            'subtitle' => $rawBanner['subtitle'] ?? null,
            'title' => $rawBanner['title'] ?? '',
            'description' => $rawBanner['description'] ?? null,
            'primary_cta_text' => $rawBanner['primary_cta_text'] ?? null,
            'primary_cta_link' => $primaryCtaLink,
            'secondary_cta_text' => $rawBanner['secondary_cta_text'] ?? null,
            'secondary_cta_link' => $secondaryCtaLink,
            'link' => $link,
            'target' => $rawBanner['target'] ?? null,
            'is_video' => (bool) ($rawBanner['is_video'] ?? false),
            'position_display' => $rawBanner['position_display'] ?? null,
            'custom_fields' => $rawBanner['custom_fields'] ?? null,
            'image' => [
                'url' => $desktopUrl,
                'alt' => $img['alt'] ?? $fallbackAlt,
            ],
            'image_mobile' => [
                'url' => $mobileUrl,
                'alt' => $imgMobile['alt'] ?? ($img['alt'] ?? $fallbackAlt),
            ],
        ];
    }

    public function index(Request $request)
    {
        try {
            $locale = current_locale();

            $data = Cache::remember('products_index_' . $locale, 3600, function () {
                $products = Product::query()
                    ->active()
                    ->with('categories')
                    ->ordered()
                    ->get()
                    ->map(fn($item) => $item->transform());

                $categories = ProductCategory::query()
                    ->active()
                    ->whereParent()
                    ->with(['children', 'nodes'])
                    ->sortByPosition()
                    ->get()
                    ->map(fn($item) => $item->transform());

                $heroBanner = null;
                try {
                    if (Schema::hasTable('sliders')) {
                        $heroBanner = $this->normalizeSliderBanner(Slider::getByPosition('BANNER_PRODUCT_HERO'));
                    }
                } catch (\Throwable $e) {
                    $heroBanner = null;
                }

                return [
                    'products' => $products,
                    'categories' => $categories,
                    'heroBanner' => $heroBanner,
                ];
            });

            $seoData = $this->resolveCatalogSeo($locale, fn(?string $url): ?string => $this->normalizeLocalAssetUrl($url));
            $payload = array_merge($data, ['seo' => $seoData]);

            if (request()->wantsJson()) {
                return response()->json($payload);
            }

            return Inertia::render('Product/Index', $payload)
                ->withViewData(['seo' => $seoData]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    protected function resolveCatalogSeo(string $locale, callable $normalizeLocalAssetUrl): array
    {
        $isEn = $locale === 'en';
        $metaPage = null;
        try {
            if (Schema::hasTable('meta_pages')) {
                $candidateUrls = $isEn
                    ? ['/en/products', 'en/products']
                    : ['/san-pham', 'san-pham'];
                $metaPage = MetaPage::whereIn('url', $candidateUrls)->first();
            }
        } catch (\Throwable $e) {
            $metaPage = null;
        }

        $global = [];
        try {
            if (Schema::hasTable('settings')) {
                $global = Config::getGeneralSettings($locale);
            }
        } catch (\Throwable $e) {
            $global = [];
        }

        $pick = static function (...$values): ?string {
            foreach ($values as $value) {
                if (is_string($value) && trim($value) !== '') {
                    return trim($value);
                }
            }
            return null;
        };

        $title = $pick(
            $metaPage?->seo_meta_title,
            $isEn ? ($global['seo_meta_title_en'] ?? null) : null,
            $global['seo_meta_title'] ?? null,
            $global['general_site_name'] ?? null
        );
        $description = $pick(
            $metaPage?->seo_meta_description,
            $isEn ? ($global['seo_meta_description_en'] ?? null) : null,
            $global['seo_meta_description'] ?? null,
            $global['general_description'] ?? null
        );
        $keywords = $pick(
            $metaPage?->seo_meta_keywords,
            $isEn ? ($global['seo_meta_keywords_en'] ?? null) : null,
            $global['seo_meta_keywords'] ?? null
        );
        $robots = $pick($metaPage?->seo_meta_robots, $global['seo_meta_robots'] ?? null);
        $canonical = $pick($metaPage?->seo_canonical, $global['seo_canonical'] ?? null);

        $rawImage = $metaPage?->seo_image ?? ($global['seo_image'] ?? null);
        $resolvedImage = Config::resolveMediaSettingUrl($rawImage);
        $image = $resolvedImage !== '' ? $normalizeLocalAssetUrl($resolvedImage) : null;

        $schemas = [
            [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $isEn ? 'Home' : 'Trang chủ',
                        'item' => url($isEn ? '/en' : '/'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $isEn ? 'Products' : 'Sản phẩm',
                        'item' => url($isEn ? '/en/products' : '/san-pham'),
                    ],
                ],
            ],
        ];

        if (!empty($metaPage?->seo_schemas)) {
            $metaSchemas = is_string($metaPage->seo_schemas)
                ? json_decode($metaPage->seo_schemas, true)
                : $metaPage->seo_schemas;
            if (is_array($metaSchemas) && !empty($metaSchemas)) {
                $schemas = $metaSchemas;
            }
        }

        return [
            'title' => $title,
            'description' => $description,
            'keywords' => $keywords,
            'image' => $image,
            'robots' => $robots,
            'canonical' => $canonical,
            'seo_meta_title' => $title,
            'seo_meta_description' => $description,
            'seo_meta_keywords' => $keywords,
            'seo_image' => $image,
            'seo_meta_robots' => $robots,
            'seo_canonical' => $canonical,
            'seo_schemas' => $schemas,
        ];
    }

    public function show($slug)
    {
        try {
            $locale = current_locale();
            $cleanSlug = trim(urldecode((string) $slug));
            $cacheKey = 'product_show_' . $cleanSlug . '_' . $locale;

            $cached = Cache::remember($cacheKey, 3600, function () use ($cleanSlug) {
                $product = Product::query()
                    ->active()
                    ->whereSlug($cleanSlug)
                    ->first();

                if (!$product && is_numeric($cleanSlug)) {
                    $product = Product::query()
                        ->active()
                        ->where('id', (int) $cleanSlug)
                        ->first();
                }

                if (!$product) {
                    $product = Product::query()
                        ->where('status', Product::STATUS_ACTIVE)
                        ->whereSlug($cleanSlug)
                        ->first();
                }

                if (!$product) {
                    return null;
                }

                $details = $product->transformDetails();
                $details['material'] = $product->material ?? null;
                $details['print_technology'] = $product->print_technology ?? null;
                $details['lead_time'] = $product->lead_time ?? null;

                return [
                    'product' => $details,
                    'related_products' => $product->related(['limit' => 8]),
                    'seo' => $product->transformSeo(),
                ];
            });

            if (!$cached) {
                Cache::forget($cacheKey);
                return abort(404);
            }

            $details = $cached['product'] ?? [];
            $seo = is_array($cached['seo'] ?? null) ? $cached['seo'] : [];

            $productUrl = url()->current();
            $productImages = [];
            if (!empty($details['images']) && is_iterable($details['images'])) {
                foreach ($details['images'] as $img) {
                    $imgUrl = is_array($img) ? ($img['url'] ?? $img['path'] ?? null) : $img;
                    if ($imgUrl) {
                        $productImages[] = \Illuminate\Support\Str::startsWith($imgUrl, ['http://', 'https://']) ? $imgUrl : url($imgUrl);
                    }
                }
            }
            if (empty($productImages) && !empty($details['image']['url'])) {
                $mainImgUrl = $details['image']['url'];
                $productImages[] = \Illuminate\Support\Str::startsWith($mainImgUrl, ['http://', 'https://']) ? $mainImgUrl : url($mainImgUrl);
            }

            $generalSettings = Schema::hasTable('settings')
                ? Config::getGeneralSettings($locale)
                : [];
            $brandName = !empty($generalSettings['general_business_name'])
                ? $generalSettings['general_business_name']
                : null;

            $rawPrice = $details['price'] ?? null;
            $hasPrice = $rawPrice !== null && is_numeric($rawPrice) && (float) $rawPrice > 0;
            $formattedPrice = null;
            if ($hasPrice) {
                $floatPrice = (float) $rawPrice;
                $formattedPrice = fmod($floatPrice, 1.0) === 0.0
                    ? (string) (int) round($floatPrice)
                    : rtrim(rtrim(number_format($floatPrice, 2, '.', ''), '0'), '.');
            }

            $sku = isset($details['sku']) && is_string($details['sku']) && trim($details['sku']) !== ''
                ? trim($details['sku'])
                : null;

            $rawDesc = $details['description'] ?? $seo['seo_meta_description'] ?? null;
            $productSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'Product',
                'name' => $details['title'] ?? null,
                'image' => $productImages,
                'description' => $rawDesc ? strip_tags((string) $rawDesc) : '',
                'brand' => [
                    '@type' => 'Brand',
                    'name' => $brandName,
                ],
            ];

            if ($sku !== null) {
                $productSchema['sku'] = $sku;
            }

            if ($hasPrice && $formattedPrice !== null) {
                $productSchema['offers'] = [
                    '@type' => 'Offer',
                    'url' => $productUrl,
                    'priceCurrency' => 'VND',
                    'price' => $formattedPrice,
                    'itemCondition' => 'https://schema.org/NewCondition',
                    'availability' => 'https://schema.org/InStock',
                    'seller' => [
                        '@type' => 'Organization',
                        'name' => $brandName,
                    ],
                ];
            }

            $isEn = $locale === 'en';
            $breadcrumbSchema = [
                '@context' => 'https://schema.org',
                '@type' => 'BreadcrumbList',
                'itemListElement' => [
                    [
                        '@type' => 'ListItem',
                        'position' => 1,
                        'name' => $isEn ? 'Home' : 'Trang chủ',
                        'item' => url($isEn ? '/en' : '/'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 2,
                        'name' => $isEn ? 'Products' : 'Sản phẩm',
                        'item' => url($isEn ? '/en/products' : '/san-pham'),
                    ],
                    [
                        '@type' => 'ListItem',
                        'position' => 3,
                        'name' => $details['title'] ?? null,
                        'item' => $productUrl,
                    ],
                ],
            ];

            $existingSchemas = $seo['seo_schemas'] ?? [];
            if (is_string($existingSchemas)) {
                $existingSchemas = json_decode($existingSchemas, true) ?: [];
            }
            if (!is_array($existingSchemas)) {
                $existingSchemas = [];
            }

            $seo['seo_schemas'] = array_merge($existingSchemas, [$productSchema, $breadcrumbSchema]);

            $faqs = $this->resolveProductFaqs($locale);

            $ctaBanner = null;
            try {
                if (Schema::hasTable('sliders')) {
                    $ctaBanner = $this->normalizeSliderBanner(Slider::getByPosition('BANNER_GLOBAL_CTA'));
                }
            } catch (\Throwable $e) {
                $ctaBanner = null;
            }

            $pdpDefaults = Schema::hasTable('settings')
                ? Config::getProductDetailDefaults($locale)
                : [
                    'brand' => null,
                    'origin' => null,
                    'warranty' => null,
                    'condition' => null,
                    'stock_status' => null,
                    'technical_support' => null,
                ];

            $data = [
                'product' => $details,
                'related_products' => $cached['related_products'] ?? [],
                'faqs' => $faqs,
                'ctaBanner' => $ctaBanner,
                'pdpDefaults' => $pdpDefaults,
                'seo' => $seo,
            ];

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Product/Show', $data)
                ->withViewData(['seo' => $data['seo'] ?? null]);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }

    protected function resolveProductFaqs(string $locale): array
    {
        try {
            if (!Schema::hasTable('posts') || !Schema::hasTable('post_translations')) {
                return [];
            }

            $defaultLocale = config('app.locale_default_frontend') ?: config('app.fallback_locale') ?: 'vi';
            $useFallback = (bool) config('app.use_default_locale', true);

            return Post::query()
                ->where('type', Post::TYPE_FAQ)
                ->where('status', Post::STATUS_ACTIVE)
                ->where(function ($query) {
                    $query->whereNull('published_at')
                        ->orWhere('published_at', '<=', now());
                })
                ->whereHas('translations', function ($query) use ($locale, $defaultLocale, $useFallback) {
                    $query->where('locale', $locale);
                    if ($useFallback && $defaultLocale !== $locale) {
                        $query->orWhere('locale', $defaultLocale);
                    }
                })
                ->orderByPosition()
                ->orderBy('id', 'asc')
                ->with('translations')
                ->get()
                ->map(function (Post $faqPost) use ($locale, $defaultLocale) {
                    $translations = $faqPost->relationLoaded('translations')
                        ? $faqPost->translations
                        : $faqPost->translations()->get();
                    $trans = $translations->firstWhere('locale', $locale);
                    $fallbackTrans = $locale !== $defaultLocale ? $translations->firstWhere('locale', $defaultLocale) : null;

                    $question = trim((string) ($trans?->title ?: $fallbackTrans?->title ?: $faqPost->title ?: ''));
                    $rawAnswer = $trans?->content
                        ?: ($trans?->description
                        ?: ($fallbackTrans?->content
                        ?: ($fallbackTrans?->description
                        ?: ($faqPost->content ?: ($faqPost->description ?: '')))));
                    $cleanAnswer = trim(strip_tags((string) $rawAnswer));
                    $answer = $cleanAnswer !== '' ? $cleanAnswer : trim((string) $rawAnswer);

                    if ($question === '' || $answer === '') {
                        return null;
                    }

                    return [
                        'id' => $faqPost->id,
                        'question' => $question,
                        'answer' => $answer,
                    ];
                })
                ->filter()
                ->values()
                ->all();
        } catch (\Throwable $e) {
            return [];
        }
    }
}
