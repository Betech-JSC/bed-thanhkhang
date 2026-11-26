<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;

class DocumentController extends Controller
{
    public function index(Request $request)
    {
        try {
            $locale = current_locale();
            $isEn = $locale === 'en';
            $libraryBasePath = $isEn ? '/en/documents/library' : '/tai-lieu/thu-vien';

            $normalizeLocalAssetUrl = fn(?string $url): ?string => $this->normalizeLocalAssetUrl($url);
            $resolveFileUrl = fn($rawValue): ?string => $this->resolveFileUrl($rawValue);

            $data = Cache::remember('documents_index_' . $locale, 3600, function () use ($locale, $isEn, $libraryBasePath, $normalizeLocalAssetUrl, $resolveFileUrl) {
                $banners = $this->resolveDocumentBanners();
                $heroBanner = $banners['heroBanner'];
                $ctaBanner = $banners['ctaBanner'];

                $documents = [];
                try {
                    if (Schema::hasTable('posts')) {
                        $documents = Post::query()
                            ->active()
                            ->whereIn('type', ['RESOURCE', 'CATALOGUE'])
                            ->orderByPosition()
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(function (Post $post) use ($normalizeLocalAssetUrl, $resolveFileUrl) {
                                $transformed = $post->transform();
                                $img = is_array($transformed['image'] ?? null) ? $transformed['image'] : [];
                                $transformed['image'] = [
                                    'url' => $normalizeLocalAssetUrl($img['url'] ?? null),
                                    'alt' => $img['alt'] ?? ($transformed['title'] ?? $post->title ?? ''),
                                ];

                                $fileUrl = $resolveFileUrl($post->download ?? $post->link ?? ($transformed['link'] ?? null));

                                return array_merge($transformed, [
                                    'file_url' => $fileUrl,
                                    'download_url' => $fileUrl,
                                ]);
                            })
                            ->values()
                            ->all();
                    }

                    if (empty($documents) && Schema::hasTable('settings')) {
                        $general = settings()->group('general')->all();
                        $generalArr = $general instanceof \Illuminate\Support\Collection ? $general->toArray() : (is_array($general) ? $general : []);
                        $candidates = $isEn
                            ? [$generalArr['documents_en'] ?? null, $generalArr['documents'] ?? null]
                            : [$generalArr['documents'] ?? null];

                        foreach ($candidates as $rawDocs) {
                            $parsedDocs = null;
                            if (is_string($rawDocs) && trim($rawDocs) !== '') {
                                $decoded = json_decode($rawDocs, true);
                                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded) && count($decoded) > 0) {
                                    $parsedDocs = array_values($decoded);
                                }
                            } elseif (is_array($rawDocs) && count($rawDocs) > 0) {
                                $parsedDocs = array_values($rawDocs);
                            }

                            if (!empty($parsedDocs)) {
                                $documents = array_values(array_map(function ($item) use ($normalizeLocalAssetUrl, $resolveFileUrl) {
                                    $itemArr = is_array($item) ? $item : (array) $item;
                                    $title = (string) ($itemArr['title'] ?? $itemArr['name'] ?? '');
                                    $rawImage = $itemArr['image'] ?? null;
                                    if (is_array($rawImage)) {
                                        $rawImgUrl = $rawImage['url'] ?? (isset($rawImage['path']) ? static_url($rawImage['path']) : null);
                                        $image = [
                                            'url' => $normalizeLocalAssetUrl(is_string($rawImgUrl) ? $rawImgUrl : null),
                                            'alt' => $rawImage['alt'] ?? $title,
                                        ];
                                    } else {
                                        $image = [
                                            'url' => $normalizeLocalAssetUrl(is_string($rawImage) ? $rawImage : null),
                                            'alt' => $title,
                                        ];
                                    }

                                    $fileUrl = $resolveFileUrl(
                                        $itemArr['file_url']
                                        ?? $itemArr['download_url']
                                        ?? $itemArr['download']
                                        ?? $itemArr['link']
                                        ?? $itemArr['url']
                                        ?? null
                                    );

                                    return array_merge($itemArr, [
                                        'image' => $image,
                                        'file_url' => $fileUrl,
                                        'download_url' => $resolveFileUrl($itemArr['download_url'] ?? null) ?? $fileUrl,
                                    ]);
                                }, $parsedDocs));
                                break;
                            }
                        }
                    }
                } catch (\Throwable $e) {
                    report($e);
                }

                $productsBasePath = $isEn ? '/en/products' : '/san-pham';
                $productCategories = [];
                try {
                    if (Schema::hasTable('product_categories')) {
                        $productCategories = ProductCategory::query()
                            ->active()
                            ->whereParent()
                            ->sortByPosition()
                            ->with(['children', 'nodes'])
                            ->get()
                            ->map(function (ProductCategory $cat) use ($normalizeLocalAssetUrl, $productsBasePath, $libraryBasePath, $locale) {
                                $slug = trim((string) ($cat->seo_slug ?? $cat->slug ?? ''));
                                $url = $slug !== '' ? ($productsBasePath . '?category=' . urlencode($slug)) : $productsBasePath;
                                $librarySlug = $this->resolveDocumentCategoryAlias($slug, $locale);
                                $libraryUrl = $librarySlug !== '' ? ($libraryBasePath . '?category=' . urlencode($librarySlug)) : $libraryBasePath;

                                // Priority 1: Customer-uploaded category image in DB
                                $catImageDetail = $cat->getImageDetail($cat->image);
                                $resolvedImageUrl = $normalizeLocalAssetUrl($catImageDetail['url'] ?? null);

                                // Priority 2: First active descendant product's image in DB
                                if (empty($resolvedImageUrl) && Schema::hasTable('products')) {
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
                                        if (empty($resolvedImageUrl) && is_array($sampleProduct->images) && !empty($sampleProduct->images[0])) {
                                            $resolvedImageUrl = $normalizeLocalAssetUrl(
                                                $sampleProduct->getImageDetail($sampleProduct->images[0])['url'] ?? null
                                            );
                                        }
                                    }
                                }

                                $transformed = $cat->transform();
                                $childrenArray = array_values(array_map(function ($child) use ($productsBasePath, $normalizeLocalAssetUrl) {
                                    $childSlug = trim((string) ($child['slug'] ?? ''));
                                    $childImg = is_array($child['image'] ?? null) ? $child['image'] : [];
                                    return [
                                        'id' => $child['id'] ?? null,
                                        'parent_id' => $child['parent_id'] ?? null,
                                        'title' => $child['title'] ?? '',
                                        'name' => $child['name'] ?? ($child['title'] ?? ''),
                                        'slug' => $childSlug,
                                        'url' => $childSlug !== '' ? ($productsBasePath . '?category=' . urlencode($childSlug)) : $productsBasePath,
                                        'description' => $child['description'] ?? null,
                                        'image' => [
                                            'url' => $normalizeLocalAssetUrl($childImg['url'] ?? null),
                                            'alt' => $childImg['alt'] ?? ($child['title'] ?? ''),
                                        ],
                                    ];
                                }, $transformed['children'] ?? []));

                                return [
                                    'id' => $cat->id,
                                    'parent_id' => (int) ($cat->parent_id ?? 0),
                                    'title' => $cat->title,
                                    'name' => $cat->title,
                                    'slug' => $slug,
                                    'url' => $url,
                                    'library_url' => $libraryUrl,
                                    'description' => $cat->description,
                                    'position' => $cat->position,
                                    'image' => [
                                        'url' => $resolvedImageUrl,
                                        'alt' => $catImageDetail['alt'] ?? $cat->title,
                                    ],
                                    'children' => $childrenArray,
                                ];
                            })
                            ->values()
                            ->all();
                    }
                } catch (\Throwable $e) {
                    report($e);
                }

                return [
                    'heroBanner' => $heroBanner,
                    'documents' => $documents,
                    'product_categories' => $productCategories,
                    'categories' => $productCategories,
                    'documents_library_url' => $libraryBasePath,
                    'ctaBanner' => $ctaBanner,
                ];
            });

            $seo = $this->resolveDocumentsSeo($locale, $normalizeLocalAssetUrl);

            if ($request->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            $response = Inertia::render('Documents/Index', array_merge($data, ['seo' => $seo]));

            return $seo !== null
                ? $response->withViewData(['seo' => $seo])
                : $response;
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function library(Request $request)
    {
        try {
            $locale = current_locale();
            $isEn = $locale === 'en';
            $librarySeoUrls = $isEn
                ? ['/en/documents/library', 'en/documents/library']
                : ['/tai-lieu/thu-vien', 'tai-lieu/thu-vien'];

            $cached = Cache::remember('documents_library_' . $locale, 3600, function () {
                $banners = $this->resolveDocumentBanners();

                $documents = [];
                if (Schema::hasTable('posts')) {
                    try {
                        $documents = Post::query()
                            ->active()
                            ->where('type', Post::TYPE_DOCUMENT)
                            ->with([
                                'categories' => function ($query) {
                                    $query->active()
                                        ->where('type', PostCategory::TYPE_DOCUMENT)
                                        ->orderByPosition()
                                        ->orderBy('id', 'asc');
                                },
                            ])
                            ->orderByPosition()
                            ->orderBy('id', 'asc')
                            ->get()
                            ->map(function (Post $post) {
                                $fileUrl = $this->resolveFileUrl($post->download ?? null);
                                $docCategories = $post->categories
                                    ->map(function (PostCategory $cat) {
                                        $catSlug = trim((string) ($cat->seo_slug ?: $cat->slug ?: ''));
                                        return [
                                            'id' => $cat->id,
                                            'title' => (string) ($cat->title ?? ''),
                                            'slug' => $catSlug,
                                        ];
                                    })
                                    ->values()
                                    ->all();

                                $categorySlugs = array_values(array_filter(array_column($docCategories, 'slug')));

                                return [
                                    'id' => $post->id,
                                    'title' => (string) ($post->title ?? ''),
                                    'slug' => trim((string) ($post->seo_slug ?: $post->slug ?: '')),
                                    'description' => $post->description,
                                    'file_url' => $fileUrl,
                                    'download_url' => $fileUrl,
                                    'has_file' => $fileUrl !== null,
                                    'position' => (int) ($post->position ?? 0),
                                    'categories' => $docCategories,
                                    'category_slugs' => $categorySlugs,
                                ];
                            })
                            ->values()
                            ->all();
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                $countsByCategoryId = [];
                foreach ($documents as $doc) {
                    foreach ($doc['categories'] as $docCategory) {
                        $catId = (int) ($docCategory['id'] ?? 0);
                        if ($catId > 0) {
                            $countsByCategoryId[$catId] = ($countsByCategoryId[$catId] ?? 0) + 1;
                        }
                    }
                }

                $documentCategories = [];
                if (Schema::hasTable('post_categories')) {
                    try {
                        $documentCategories = PostCategory::query()
                            ->active()
                            ->where('type', PostCategory::TYPE_DOCUMENT)
                            ->orderByPosition()
                            ->orderBy('id', 'asc')
                            ->get()
                            ->map(function (PostCategory $cat) use ($countsByCategoryId) {
                                $slug = trim((string) ($cat->seo_slug ?: $cat->slug ?: ''));
                                return [
                                    'id' => $cat->id,
                                    'title' => (string) ($cat->title ?? ''),
                                    'slug' => $slug,
                                    'position' => (int) ($cat->position ?? 0),
                                    'documents_count' => $countsByCategoryId[$cat->id] ?? 0,
                                ];
                            })
                            ->values()
                            ->all();
                    } catch (\Throwable $e) {
                        report($e);
                    }
                }

                return [
                    'heroBanner' => $banners['heroBanner'],
                    'ctaBanner' => $banners['ctaBanner'],
                    'documentCategories' => $documentCategories,
                    'documents' => $documents,
                ];
            });

            $categoryFilter = trim((string) $request->query('category', ''));
            if (strtolower($categoryFilter) === 'all') {
                $categoryFilter = '';
            }
            if ($categoryFilter !== '') {
                $availableCategorySlugs = array_values(array_filter(array_column($cached['documentCategories'], 'slug')));
                if (!in_array($categoryFilter, $availableCategorySlugs, true)) {
                    $categoryFilter = $this->resolveDocumentCategoryAlias($categoryFilter, $locale);
                }
            }
            $searchFilter = trim((string) $request->query('search', ''));
            $requestedPage = max(1, (int) $request->query('page', 1));
            $perPage = 10;

            $filteredDocuments = array_values(array_filter(
                $cached['documents'],
                static function (array $doc) use ($categoryFilter, $searchFilter): bool {
                    if ($categoryFilter !== '' && !in_array($categoryFilter, $doc['category_slugs'], true)) {
                        return false;
                    }
                    if ($searchFilter !== '') {
                        $haystacks = [
                            $doc['title'] ?? '',
                            (string) ($doc['description'] ?? ''),
                            $doc['slug'] ?? '',
                        ];
                        foreach ($haystacks as $haystack) {
                            if ($haystack !== '' && mb_stripos($haystack, $searchFilter) !== false) {
                                return true;
                            }
                        }
                        return false;
                    }
                    return true;
                }
            ));

            $total = count($filteredDocuments);
            $lastPage = max(1, (int) ceil($total / $perPage));
            $currentPage = $total > 0 ? min($requestedPage, $lastPage) : 1;
            $paginatedDocuments = array_values(array_slice($filteredDocuments, ($currentPage - 1) * $perPage, $perPage));

            $seo = $this->resolveDocumentsSeo(
                $locale,
                fn(?string $url): ?string => $this->normalizeLocalAssetUrl($url),
                $librarySeoUrls
            );

            $payload = [
                'heroBanner' => $cached['heroBanner'],
                'ctaBanner' => $cached['ctaBanner'],
                'documentCategories' => $cached['documentCategories'],
                'totalDocumentsCount' => count($cached['documents']),
                'documents' => $paginatedDocuments,
                'pagination' => [
                    'current_page' => $currentPage,
                    'last_page' => $lastPage,
                    'per_page' => $perPage,
                    'total' => $total,
                ],
                'initialFilters' => [
                    'category' => $categoryFilter,
                    'search' => $searchFilter,
                    'page' => $currentPage,
                ],
                'documents_index_url' => $isEn ? '/en/documents' : '/tai-lieu',
                'seo' => $seo,
            ];

            if ($request->wantsJson()) {
                return response()->json($payload);
            }

            $response = Inertia::render('Documents/Library', $payload);

            return $seo !== null
                ? $response->withViewData(['seo' => $seo])
                : $response;
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

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

        $sanitizeCtaLink = static function ($value): ?string {
            if (!is_string($value)) {
                return null;
            }
            $trimmed = trim($value);
            if ($trimmed === '' || $trimmed === '#') {
                return null;
            }
            return $trimmed;
        };

        $img = is_array($rawBanner['image'] ?? null) ? $rawBanner['image'] : [];
        $imgMobile = is_array($rawBanner['image_mobile'] ?? null) ? $rawBanner['image_mobile'] : [];
        $fallbackAlt = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($rawBanner['title'] ?? ''))));
        $desktopUrl = $this->normalizeLocalAssetUrl($img['url'] ?? null);
        $mobileUrl = $this->normalizeLocalAssetUrl($imgMobile['url'] ?? null) ?? $desktopUrl;

        $link = $sanitizeCtaLink($rawBanner['link'] ?? null);
        $primaryCtaLink = $sanitizeCtaLink($rawBanner['primary_cta_link'] ?? null) ?? $link;
        $secondaryCtaLink = $sanitizeCtaLink($rawBanner['secondary_cta_link'] ?? null);

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

    protected function resolveDocumentBanners(): array
    {
        $heroBanner = null;
        $ctaBanner = null;

        try {
            if (Schema::hasTable('sliders')) {
                $heroBanner = $this->normalizeSliderBanner(Slider::getByPosition('BANNER_DOCUMENT_HERO'));
                $ctaBanner = $this->normalizeSliderBanner(Slider::getByPosition('BANNER_DOCUMENT_CTA'))
                    ?? $this->normalizeSliderBanner(Slider::getByPosition('BANNER_GLOBAL_CTA'));
            }
        } catch (\Throwable $e) {
            report($e);
        }

        return [
            'heroBanner' => $heroBanner,
            'ctaBanner' => $ctaBanner,
        ];
    }

    protected function resolveFileUrl($rawValue): ?string
    {
        if (is_string($rawValue) && trim($rawValue) !== '') {
            $trimmed = trim($rawValue);
            $decoded = json_decode($trimmed, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $path = $decoded['url'] ?? $decoded['path'] ?? null;
                if (is_string($path) && trim($path) !== '') {
                    $path = trim($path);
                    $resolved = str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : static_url($path);
                    return $this->normalizeLocalAssetUrl($resolved);
                }
                return null;
            }
            if (
                str_starts_with($trimmed, 'http') ||
                str_starts_with($trimmed, '/') ||
                str_starts_with($trimmed, 'assets/') ||
                str_starts_with($trimmed, 'images/') ||
                str_starts_with($trimmed, 'static/')
            ) {
                return $this->normalizeLocalAssetUrl($trimmed);
            }
            return $this->normalizeLocalAssetUrl(static_url($trimmed));
        }

        if (is_array($rawValue)) {
            $path = $rawValue['url'] ?? $rawValue['path'] ?? null;
            if (is_string($path) && trim($path) !== '') {
                $path = trim($path);
                $resolved = str_starts_with($path, 'http') || str_starts_with($path, '/') ? $path : static_url($path);
                return $this->normalizeLocalAssetUrl($resolved);
            }
        }

        return null;
    }

    protected function resolveDocumentsSeo(string $locale, callable $normalizeLocalAssetUrl, ?array $candidateUrls = null): ?array
    {
        $isEn = $locale === 'en';
        $metaPage = null;
        try {
            if (Schema::hasTable('meta_pages')) {
                $urls = $candidateUrls ?? ($isEn
                    ? ['/en/documents', 'en/documents']
                    : ['/tai-lieu', 'tai-lieu']);
                $metaPage = MetaPage::whereIn('url', $urls)->first();
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
        $schemas = $metaPage?->seo_schemas ?? null;

        if ($title === null && $description === null && $keywords === null && $image === null && $robots === null && $canonical === null && empty($schemas)) {
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
            'seo_schemas' => $schemas,
        ];
    }

    protected function resolveDocumentCategoryAlias(string $slug, string $locale): string
    {
        $trimmed = trim($slug);
        if ($trimmed === '') {
            return '';
        }

        $normalized = strtolower($trimmed);
        $isEn = $locale === 'en';

        $canonicalSlugs = [
            'escalator' => ['vi' => 'linh-kien-thang-cuon', 'en' => 'escalator-spare-parts'],
            'parking' => ['vi' => 'thang-nang-o-to', 'en' => 'car-parking-lifts'],
            'packaging' => ['vi' => 'day-chuyen-dong-goi', 'en' => 'packaging-lines'],
            'baggage' => ['vi' => 'bang-tai-hanh-ly', 'en' => 'baggage-conveyors'],
            'energy' => ['vi' => 'tiet-kiem-dien', 'en' => 'energy-saving'],
            'chain' => ['vi' => 'xich-cong-nghiep', 'en' => 'industrial-chains'],
        ];

        $aliasToTopic = [
            // ProductCategory VI slugs
            'linh-kien-thang-cuon' => 'escalator',
            'linh-kien-thang-nang-o-to' => 'parking',
            'linh-kien-day-chuyen-dong-goi' => 'packaging',
            'he-thong-bang-tai-hanh-ly' => 'baggage',
            // ProductCategory EN slugs
            'escalator-spare-parts' => 'escalator',
            'car-parking-lift-components' => 'parking',
            'packaging-line-components' => 'packaging',
            'baggage-conveyor-systems' => 'baggage',
            // Document PostCategory VI slugs
            'thang-nang-o-to' => 'parking',
            'day-chuyen-dong-goi' => 'packaging',
            'bang-tai-hanh-ly' => 'baggage',
            'tiet-kiem-dien' => 'energy',
            'xich-cong-nghiep' => 'chain',
            // Document PostCategory EN slugs
            'car-parking-lifts' => 'parking',
            'packaging-lines' => 'packaging',
            'baggage-conveyors' => 'baggage',
            'energy-saving' => 'energy',
            'industrial-chains' => 'chain',
        ];

        if (isset($aliasToTopic[$normalized])) {
            $topic = $aliasToTopic[$normalized];
            return $canonicalSlugs[$topic][$isEn ? 'en' : 'vi'];
        }

        return $trimmed;
    }
}
