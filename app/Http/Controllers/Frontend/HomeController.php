<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
use App\Models\Keyword;
use App\Models\KeywordRefDate;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $locale = current_locale();
            $data = Cache::remember('home_page_data_' . $locale, 3600, function () use ($locale) {
                try {
                    // Get Home Slider & Banners (normalize HOME_SLIDER to sequential array of slide contracts)
                    $rawSliders = Slider::getByPosition('HOME_SLIDER');
                    if ($rawSliders instanceof \Illuminate\Support\Collection) {
                        $rawSliderList = $rawSliders->values()->all();
                    } elseif (is_array($rawSliders)) {
                        $rawSliderList = array_is_list($rawSliders) ? array_values($rawSliders) : [$rawSliders];
                    } else {
                        $rawSliderList = [];
                    }

                    $normalizeLocalAssetUrl = static function (?string $url): ?string {
                        if (empty($url)) {
                            return null;
                        }
                        if (str_starts_with($url, '/')) {
                            return $url;
                        }
                        $parsedPath = parse_url($url, PHP_URL_PATH);
                        if (is_string($parsedPath) && (str_starts_with($parsedPath, '/assets/') || str_starts_with($parsedPath, '/images/'))) {
                            $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
                            $urlHost = parse_url($url, PHP_URL_HOST);
                            if (in_array($urlHost, array_filter([$appHost, 'localhost', '127.0.0.1']), true)) {
                                return $parsedPath;
                            }
                        }
                        return $url;
                    };

                    $sliders = array_values(array_map(function ($slide) use ($normalizeLocalAssetUrl) {
                        $slideArr = is_array($slide) ? $slide : (array) $slide;
                        $img = is_array($slideArr['image'] ?? null) ? $slideArr['image'] : [];
                        $imgMobile = is_array($slideArr['image_mobile'] ?? null) ? $slideArr['image_mobile'] : [];
                        $fallbackAlt = strip_tags((string) ($slideArr['title'] ?? ''));
                        $desktopUrl = $normalizeLocalAssetUrl($img['url'] ?? null);
                        $mobileUrl = $normalizeLocalAssetUrl($imgMobile['url'] ?? null) ?? $desktopUrl;

                        return [
                            'id' => $slideArr['id'] ?? null,
                            'subtitle' => $slideArr['subtitle'] ?? null,
                            'title' => $slideArr['title'] ?? '',
                            'description' => $slideArr['description'] ?? null,
                            'primary_cta_text' => $slideArr['primary_cta_text'] ?? null,
                            'primary_cta_link' => $slideArr['primary_cta_link'] ?? ($slideArr['link'] ?? null),
                            'secondary_cta_text' => $slideArr['secondary_cta_text'] ?? null,
                            'secondary_cta_link' => $slideArr['secondary_cta_link'] ?? null,
                            'link' => $slideArr['link'] ?? null,
                            'target' => $slideArr['target'] ?? null,
                            'is_video' => (bool) ($slideArr['is_video'] ?? false),
                            'image' => [
                                'url' => $desktopUrl,
                                'alt' => $img['alt'] ?? $fallbackAlt,
                            ],
                            'image_mobile' => [
                                'url' => $mobileUrl,
                                'alt' => $imgMobile['alt'] ?? ($img['alt'] ?? $fallbackAlt),
                            ],
                        ];
                    }, array_filter($rawSliderList)));

                    // Home About Intro section (settings + 3 gallery images from HOME_ABOUT_GALLERY)
                    $rawAboutGallery = Slider::getByPosition('HOME_ABOUT_GALLERY');
                    if ($rawAboutGallery instanceof \Illuminate\Support\Collection) {
                        $rawAboutGalleryList = $rawAboutGallery->values()->all();
                    } elseif (is_array($rawAboutGallery)) {
                        $rawAboutGalleryList = array_is_list($rawAboutGallery) ? array_values($rawAboutGallery) : [$rawAboutGallery];
                    } else {
                        $rawAboutGalleryList = [];
                    }

                    $aboutImages = array_values(array_map(function ($item) use ($normalizeLocalAssetUrl) {
                        $itemArr = is_array($item) ? $item : (array) $item;
                        $img = is_array($itemArr['image'] ?? null) ? $itemArr['image'] : [];
                        $fallbackAlt = strip_tags((string) ($itemArr['title'] ?? ''));

                        return [
                            'id' => $itemArr['id'] ?? null,
                            'url' => $normalizeLocalAssetUrl($img['url'] ?? null),
                            'alt' => $img['alt'] ?? $fallbackAlt,
                        ];
                    }, array_filter($rawAboutGalleryList)));

                    $aboutSettings = Config::getHomeAboutSettings($locale);
                    $aboutSection = array_merge($aboutSettings, [
                        'images' => $aboutImages,
                    ]);

                    // Home Why Choose Us section (BANNER_HOME_TRUST + 5 HOME_TRUST_PILLARS)
                    $rawTrustBanner = Slider::getByPosition('BANNER_HOME_TRUST');
                    $trustBannerArr = is_array($rawTrustBanner) ? $rawTrustBanner : [];
                    $trustBannerImg = is_array($trustBannerArr['image'] ?? null) ? $trustBannerArr['image'] : [];
                    $trustBannerMobileImg = is_array($trustBannerArr['image_mobile'] ?? null) ? $trustBannerArr['image_mobile'] : [];
                    $trustHeading = (string) ($trustBannerArr['title'] ?? '');
                    $trustDescription = (string) ($trustBannerArr['description'] ?? '');
                    $trustBannerDesktopUrl = $normalizeLocalAssetUrl($trustBannerImg['url'] ?? null);
                    $trustBannerMobileUrl = $normalizeLocalAssetUrl($trustBannerMobileImg['url'] ?? null) ?? $trustBannerDesktopUrl;
                    $trustBannerAlt = $trustBannerImg['alt'] ?? strip_tags($trustHeading);

                    $rawTrustPillars = Slider::getByPosition('HOME_TRUST_PILLARS');
                    if ($rawTrustPillars instanceof \Illuminate\Support\Collection) {
                        $rawTrustPillarList = $rawTrustPillars->values()->all();
                    } elseif (is_array($rawTrustPillars)) {
                        $rawTrustPillarList = array_is_list($rawTrustPillars) ? array_values($rawTrustPillars) : [$rawTrustPillars];
                    } else {
                        $rawTrustPillarList = [];
                    }

                    $trustItems = array_values(array_map(function ($item) use ($normalizeLocalAssetUrl) {
                        $itemArr = is_array($item) ? $item : (array) $item;
                        $iconImg = is_array($itemArr['image'] ?? null) ? $itemArr['image'] : [];
                        $title = (string) ($itemArr['title'] ?? '');

                        return [
                            'id' => $itemArr['id'] ?? null,
                            'title' => $title,
                            'description' => (string) ($itemArr['description'] ?? ''),
                            'icon' => [
                                'url' => $normalizeLocalAssetUrl($iconImg['url'] ?? null),
                                'alt' => $iconImg['alt'] ?? strip_tags($title),
                            ],
                        ];
                    }, array_filter($rawTrustPillarList)));

                    $trustSection = (!empty($trustHeading) || !empty($trustItems) || !empty($trustBannerDesktopUrl))
                        ? [
                            'heading' => $trustHeading,
                            'description' => $trustDescription,
                            'banner' => [
                                'url' => $trustBannerDesktopUrl,
                                'mobile_url' => $trustBannerMobileUrl,
                                'alt' => $trustBannerAlt,
                            ],
                            'items' => $trustItems,
                        ]
                        : null;

                    // Home Process section (BANNER_HOME_PROCESS + 4 HOME_PROCESS_STEPS)
                    $rawProcessBanner = Slider::getByPosition('BANNER_HOME_PROCESS');
                    $processBannerArr = is_array($rawProcessBanner) ? $rawProcessBanner : [];
                    $processBannerImg = is_array($processBannerArr['image'] ?? null) ? $processBannerArr['image'] : [];
                    $processBannerMobileImg = is_array($processBannerArr['image_mobile'] ?? null) ? $processBannerArr['image_mobile'] : [];
                    $processHeading = (string) ($processBannerArr['title'] ?? '');
                    $processDescription = (string) ($processBannerArr['description'] ?? '');
                    $processBannerDesktopUrl = $normalizeLocalAssetUrl($processBannerImg['url'] ?? null);
                    $processBannerMobileUrl = $normalizeLocalAssetUrl($processBannerMobileImg['url'] ?? null) ?? $processBannerDesktopUrl;
                    $processBannerAlt = $processBannerImg['alt'] ?? strip_tags($processHeading);

                    $rawHomeProcessSteps = Slider::getByPosition('HOME_PROCESS_STEPS');
                    if ($rawHomeProcessSteps instanceof \Illuminate\Support\Collection) {
                        $rawHomeProcessStepList = $rawHomeProcessSteps->values()->all();
                    } elseif (is_array($rawHomeProcessSteps)) {
                        $rawHomeProcessStepList = array_is_list($rawHomeProcessSteps) ? array_values($rawHomeProcessSteps) : [$rawHomeProcessSteps];
                    } else {
                        $rawHomeProcessStepList = [];
                    }

                    $homeProcessSteps = [];
                    foreach (array_values(array_filter($rawHomeProcessStepList)) as $idx => $stepItem) {
                        $stepArr = is_array($stepItem) ? $stepItem : (array) $stepItem;
                        $stepNumber = trim((string) ($stepArr['subtitle'] ?? ''));
                        if ($stepNumber === '') {
                            $stepNumber = (string) ($idx + 1);
                        }
                        $homeProcessSteps[] = [
                            'id' => $stepArr['id'] ?? null,
                            'number' => $stepNumber,
                            'title' => (string) ($stepArr['title'] ?? ''),
                            'description' => (string) ($stepArr['description'] ?? ''),
                        ];
                    }

                    $processSection = (!empty($processHeading) || !empty($processDescription) || !empty($processBannerDesktopUrl) || !empty($homeProcessSteps))
                        ? [
                            'heading' => $processHeading,
                            'description' => $processDescription,
                            'banner' => [
                                'url' => $processBannerDesktopUrl,
                                'mobile_url' => $processBannerMobileUrl,
                                'alt' => $processBannerAlt,
                            ],
                            'steps' => $homeProcessSteps,
                        ]
                        : null;

                    // Fetch Featured Posts (Section 6 Blog / News — ordered by position ASC for exact Figma order)
                    $postsBasePath = $locale === 'en' ? '/en/posts' : '/tin-tuc';
                    $featured_posts = Post::query()
                        ->active()
                        ->wherePosts()
                        ->ordered()
                        ->take(3)
                        ->get()
                        ->map(function (Post $post) use ($locale, $postsBasePath, $normalizeLocalAssetUrl) {
                            $transformed = $post->transform();
                            $slug = (string) ($transformed['slug'] ?? $post->seo_slug ?? $post->slug ?? '');
                            $routeName = $locale . '.posts.show';
                            $resolvedUrl = ($slug !== '' && \Illuminate\Support\Facades\Route::has($routeName))
                                ? route($routeName, ['slug' => $slug], false)
                                : ($slug !== '' ? $postsBasePath . '/' . $slug : $postsBasePath);

                            $formattedDate = null;
                            if (!empty($post->published_at)) {
                                try {
                                    $formattedDate = \Carbon\Carbon::parse($post->published_at)
                                        ->locale('en')
                                        ->format('M d, Y');
                                } catch (\Throwable $e) {
                                    $formattedDate = null;
                                }
                            }

                            $img = is_array($transformed['image'] ?? null) ? $transformed['image'] : [];

                            return [
                                'id' => $post->id,
                                'title' => (string) ($transformed['title'] ?? $post->title ?? ''),
                                'slug' => $slug,
                                'description' => (string) ($transformed['description'] ?? $post->description ?? ''),
                                'published_at' => $post->published_at,
                                'formatted_date' => $formattedDate,
                                'is_featured' => (bool) $post->is_featured,
                                'url' => $resolvedUrl,
                                'image' => [
                                    'url' => $normalizeLocalAssetUrl($img['url'] ?? null),
                                    'alt' => $img['alt'] ?? ($transformed['title'] ?? $post->title ?? ''),
                                ],
                                'category' => $transformed['category'] ?? null,
                            ];
                        })
                        ->values()
                        ->all();

                    // Fetch Product Categories CTA Tile Banner (BANNER_HOME_CATEGORIES_CTA)
                    $rawCategoriesCta = Slider::getByPosition('BANNER_HOME_CATEGORIES_CTA');
                    $categoriesCtaBanner = null;
                    if (is_array($rawCategoriesCta) && !empty($rawCategoriesCta)) {
                        $ctaImg = is_array($rawCategoriesCta['image'] ?? null) ? $rawCategoriesCta['image'] : [];
                        $ctaTitle = (string) ($rawCategoriesCta['title'] ?? '');
                        $categoriesCtaBanner = [
                            'id' => $rawCategoriesCta['id'] ?? null,
                            'title' => $ctaTitle,
                            'subtitle' => $rawCategoriesCta['subtitle'] ?? null,
                            'link' => $rawCategoriesCta['primary_cta_link']
                                ?? $rawCategoriesCta['link']
                                ?? ($locale === 'en' ? '/en/products' : '/san-pham'),
                            'image' => [
                                'url' => $normalizeLocalAssetUrl($ctaImg['url'] ?? null),
                                'alt' => $ctaImg['alt'] ?? strip_tags($ctaTitle),
                            ],
                        ];
                    }

                    // Fetch ALL Active Root Product Categories (READ-ONLY — preserve all customer DB categories & children)
                    $productsBasePath = $locale === 'en' ? '/en/products' : '/san-pham';

                    $categories = ProductCategory::query()
                        ->active()
                        ->whereParent()
                        ->sortByPosition()
                        ->with(['children', 'nodes'])
                        ->get()
                        ->map(function (ProductCategory $cat) use ($normalizeLocalAssetUrl, $productsBasePath) {
                            $slug = (string) ($cat->seo_slug ?? $cat->slug ?? '');
                            $url = $productsBasePath . '?category=' . urlencode($slug);

                            // Priority 1: Customer-uploaded category image in DB
                            $resolvedImageUrl = $normalizeLocalAssetUrl($cat->getImageDetail($cat->image)['url'] ?? null);

                            // Priority 2: First active descendant product's image in DB
                            if (empty($resolvedImageUrl)) {
                                $descendantIds = array_values(array_unique(array_merge(
                                    [$cat->id],
                                    $cat->allChildrenId([], $cat->id)
                                )));
                                $sampleProduct = Product::query()
                                    ->where('status', Product::STATUS_ACTIVE)
                                    ->whereHas('categories', function ($q) use ($descendantIds) {
                                        $q->whereIn('product_categories.id', $descendantIds);
                                    })
                                    ->orderByFeatured()
                                    ->first();

                                if ($sampleProduct) {
                                    $resolvedImageUrl = $normalizeLocalAssetUrl(
                                        $sampleProduct->getImageDetail($sampleProduct->image)['url'] ?? null
                                    );
                                }
                            }

                            $transformed = $cat->transform();
                            $childrenArray = array_values(array_map(function ($child) use ($productsBasePath, $normalizeLocalAssetUrl) {
                                $childSlug = (string) ($child['slug'] ?? '');
                                $childImg = is_array($child['image'] ?? null) ? $child['image'] : [];
                                return [
                                    'id' => $child['id'] ?? null,
                                    'parent_id' => $child['parent_id'] ?? null,
                                    'title' => $child['title'] ?? '',
                                    'name' => $child['name'] ?? ($child['title'] ?? ''),
                                    'slug' => $childSlug,
                                    'url' => $productsBasePath . '?category=' . urlencode($childSlug),
                                    'description' => $child['description'] ?? null,
                                    'image' => [
                                        'url' => $normalizeLocalAssetUrl($childImg['url'] ?? null),
                                        'alt' => $childImg['alt'] ?? ($child['title'] ?? ''),
                                    ],
                                ];
                            }, $transformed['children'] ?? []));

                            return [
                                'id' => $cat->id,
                                'title' => $cat->title,
                                'name' => $cat->title,
                                'slug' => $slug,
                                'url' => $url,
                                'description' => $cat->description,
                                'image' => [
                                    'url' => $resolvedImageUrl,
                                    'alt' => $cat->title,
                                ],
                                'children' => $childrenArray,
                            ];
                        })
                        ->values()
                        ->all();

                    // Prepare Response Data
                    return [
                        'sliders' => $sliders,
                        'aboutSection' => $aboutSection,
                        'categoriesCtaBanner' => $categoriesCtaBanner,
                        'trustSection' => $trustSection,
                        'processSection' => $processSection,
                        'contactSection' => Config::getHomeContactSettings($locale),
                        'categories' => $categories,
                        'featured_posts' => $featured_posts,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'sliders' => [],
                        'aboutSection' => null,
                        'categoriesCtaBanner' => null,
                        'trustSection' => null,
                        'processSection' => null,
                        'contactSection' => null,
                        'categories' => [],
                        'featured_posts' => [],
                    ];
                }
            });

            $seo = $this->resolveHomeSeo($locale);

            // Return JSON or Inertia View
            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('Home/Index', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    protected function resolveHomeSeo(string $locale): ?array
    {
        $metaPage = null;
        try {
            if (Schema::hasTable('meta_pages')) {
                $targetUrl = $locale === 'en' ? '/en' : '/';
                $metaPage = MetaPage::where('url', $targetUrl)->first();
                if (!$metaPage && $locale === 'en') {
                    $metaPage = MetaPage::where('url', '/')->first();
                }
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

        $title = $pick($metaPage?->seo_meta_title, $global['seo_meta_title'] ?? null);
        $description = $pick($metaPage?->seo_meta_description, $global['seo_meta_description'] ?? null);
        $keywords = $pick($metaPage?->seo_meta_keywords, $global['seo_meta_keywords'] ?? null);
        $robots = $pick($metaPage?->seo_meta_robots, $global['seo_meta_robots'] ?? null);
        $canonical = $pick($metaPage?->seo_canonical, $global['seo_canonical'] ?? null);

        $rawImage = $metaPage?->seo_image ?? ($global['seo_image'] ?? null);
        $image = Config::resolveMediaSettingUrl($rawImage);
        $image = $image !== '' ? $image : null;

        if ($title === null && $description === null && $keywords === null && $image === null && $robots === null && $canonical === null) {
            return null;
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
        ];
    }

    public function search()
    {
        try {
            $keyword = trim((string) (request()->input('keyword') ?? request()->input('search') ?? request()->input('q') ?? ''));

            if (!empty($keyword)) {
                try {
                    $searchKeyword = Keyword::firstOrCreate(['keyword' => $keyword]);
                    $searchKeyword->update(['updated_at' => now()]);
                    KeywordRefDate::create(['keyword_id' => $searchKeyword->id]);
                } catch (\Throwable $e) {
                    // Ignore keyword logging error
                }

                return redirect()->route(current_locale() . '.products.index', ['search' => $keyword]);
            }

            return redirect()->route(current_locale() . '.products.index');
        } catch (\Throwable $th) {
            report($th);
            return redirect()->route(current_locale() . '.products.index');
        }
    }

    public function searchV2()
    {
        try {
            $locale = request()->input('locale') ?? request()->input('use_locale') ?? request()->header('X-Locale') ?? current_locale();
            if (!in_array($locale, config('app.locales', ['vi', 'en']))) {
                $locale = config('app.locale', 'vi');
            }
            app()->setLocale($locale);
            request()->merge(['use_locale' => $locale]);

            $keyword = trim((string) (request()->input('keyword') ?? request()->input('search') ?? request()->input('q') ?? ''));
            $limit = (int) (request()->input('limit') ?? 8);
            if ($limit <= 0 || $limit > 20) {
                $limit = 8;
            }

            $query = Product::query()->active();

            if (!empty($keyword)) {
                $viKeyword = $this->reverseTranslateKeyword($keyword);
                $query->where(function ($q) use ($keyword, $viKeyword) {
                    $q->whereHas('translations', function ($t) use ($keyword, $viKeyword) {
                        $t->where('title', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%")
                            ->orWhere('slug', 'like', "%{$keyword}%");
                        if ($viKeyword) {
                            $t->orWhere('title', 'like', "%{$viKeyword}%")
                                ->orWhere('description', 'like', "%{$viKeyword}%");
                        }
                    })
                    ->orWhereHas('categories', function ($c) use ($keyword, $viKeyword) {
                        $c->whereHas('translations', function ($ct) use ($keyword, $viKeyword) {
                            $ct->where('title', 'like', "%{$keyword}%")
                                ->orWhere('slug', 'like', "%{$keyword}%");
                            if ($viKeyword) {
                                $ct->orWhere('title', 'like', "%{$viKeyword}%");
                            }
                        });
                    })
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('material', 'like', "%{$keyword}%")
                    ->orWhere('print_technology', 'like', "%{$keyword}%");

                    if ($viKeyword) {
                        $q->orWhere('material', 'like', "%{$viKeyword}%")
                            ->orWhere('print_technology', 'like', "%{$viKeyword}%");
                    }
                });
            }

            $products = $query->with(['categories', 'translations'])
                ->take($limit)
                ->get()
                ->map(function ($product) use ($locale) {
                    $firstCat = $product->categories->first();
                    $imageDetail = $product->getImageDetail($product->image);
                    $imageUrl = $imageDetail['url'] ?? null;

                    $material = $this->translateTerm($product->material, $locale);
                    $printTech = $this->translateTerm($product->print_technology, $locale);
                    $productSlug = $product->resolveProductSlug($locale);
                    $routeName = $locale . '.products.show';
                    $productUrl = (filled($productSlug) && \Illuminate\Support\Facades\Route::has($routeName))
                        ? route($routeName, ['slug' => $productSlug])
                        : null;

                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'title' => $product->title,
                        'slug' => $productSlug,
                        'image' => [
                            'url' => $imageUrl,
                            'alt' => $product->title,
                        ],
                        'price' => $product->price ? (float)$product->price : null,
                        'compare_at_price' => $product->compare_at_price ? (float)$product->compare_at_price : null,
                        'category_id' => $firstCat?->id,
                        'category' => $firstCat ? [
                            'id' => $firstCat->id,
                            'name' => $firstCat->title,
                            'title' => $firstCat->title,
                            'slug' => $firstCat->slug,
                        ] : null,
                        'category_name' => $firstCat?->title ?? '',
                        'material' => $material,
                        'print_technology' => $printTech,
                        'url' => $productUrl,
                    ];
                });

            return response()->json([
                'success' => true,
                'products' => $products->values(),
            ]);
        } catch (\Throwable $th) {
            report($th);
            return response()->json([
                'success' => false,
                'products' => [],
                'message' => $th->getMessage(),
            ], 500);
        }
    }

    protected function getTermDictionary(): array
    {
        $dict = trans('ui', [], 'en');

        return is_array($dict)
            ? array_filter($dict, static fn($v, $k) => is_string($k) && is_string($v), ARRAY_FILTER_USE_BOTH)
            : [];
    }

    protected function translateTerm(?string $term, string $locale): string
    {
        if (empty($term)) {
            return '';
        }
        if ($locale === 'vi') {
            return $term;
        }

        $uiKey = 'ui.' . $term;
        $translated = __($uiKey, [], $locale);
        if ($translated !== $uiKey) {
            return $translated;
        }

        $directTrans = __($term, [], $locale);
        if ($directTrans !== $term) {
            return $directTrans;
        }

        $dict = $this->getTermDictionary();
        return $dict[$term] ?? $term;
    }

    protected function reverseTranslateKeyword(string $keyword): ?string
    {
        $keywordLower = mb_strtolower(trim($keyword));
        if (empty($keywordLower)) {
            return null;
        }

        $dict = $this->getTermDictionary();
        foreach ($dict as $vi => $en) {
            if (mb_stripos($en, $keywordLower) !== false) {
                return $vi;
            }
        }

        return null;
    }
}
