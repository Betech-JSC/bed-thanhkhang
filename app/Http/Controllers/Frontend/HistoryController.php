<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Config;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use App\Models\History;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;

class HistoryController extends Controller
{
    public function index()
    {
        try {
            $locale = current_locale();

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
                    $reqHost = request()?->getHost();
                    $urlHost = parse_url($url, PHP_URL_HOST);
                    if (in_array($urlHost, array_filter([$appHost, $reqHost, 'localhost', '127.0.0.1']), true)) {
                        return $parsedPath;
                    }
                }
                return $url;
            };

            $data = Cache::remember('history_index_' . $locale, 3600, function () use ($locale, $normalizeLocalAssetUrl) {
                $posts = Post::query()
                    ->active()
                    ->wherePosts()
                    ->ordered()
                    ->take(3)
                    ->get()
                    ->map(function (Post $item) use ($normalizeLocalAssetUrl) {
                        $transformed = $item->transform();
                        if (is_array($transformed['image'] ?? null)) {
                            $transformed['image']['url'] = $normalizeLocalAssetUrl($transformed['image']['url'] ?? null);
                        }
                        return $transformed;
                    })
                    ->values()
                    ->all();

                $rawHeroBanner = Slider::getByPosition('BANNER_ABOUT_HERO');
                $heroBanner = null;
                if (is_array($rawHeroBanner) && !empty($rawHeroBanner)) {
                    $img = is_array($rawHeroBanner['image'] ?? null) ? $rawHeroBanner['image'] : [];
                    $imgMobile = is_array($rawHeroBanner['image_mobile'] ?? null) ? $rawHeroBanner['image_mobile'] : [];
                    $fallbackAlt = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($rawHeroBanner['title'] ?? ''))));
                    $desktopUrl = $normalizeLocalAssetUrl($img['url'] ?? null);
                    $mobileUrl = $normalizeLocalAssetUrl($imgMobile['url'] ?? null) ?? $desktopUrl;

                    $heroBanner = [
                        'id' => $rawHeroBanner['id'] ?? null,
                        'subtitle' => $rawHeroBanner['subtitle'] ?? null,
                        'title' => $rawHeroBanner['title'] ?? '',
                        'description' => $rawHeroBanner['description'] ?? null,
                        'primary_cta_text' => $rawHeroBanner['primary_cta_text'] ?? null,
                        'primary_cta_link' => $rawHeroBanner['primary_cta_link'] ?? ($rawHeroBanner['link'] ?? null),
                        'secondary_cta_text' => $rawHeroBanner['secondary_cta_text'] ?? null,
                        'secondary_cta_link' => $rawHeroBanner['secondary_cta_link'] ?? null,
                        'link' => $rawHeroBanner['link'] ?? null,
                        'target' => $rawHeroBanner['target'] ?? null,
                        'is_video' => (bool) ($rawHeroBanner['is_video'] ?? false),
                        'position_display' => $rawHeroBanner['position_display'] ?? null,
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

                // Section 2 — Câu chuyện Thành Khang (about_story_* settings + 3 gallery images from ABOUT_STORY_GALLERY)
                $rawStoryGallery = Slider::getByPosition('ABOUT_STORY_GALLERY');
                if ($rawStoryGallery instanceof \Illuminate\Support\Collection) {
                    $rawStoryGalleryList = $rawStoryGallery->values()->all();
                } elseif (is_array($rawStoryGallery)) {
                    $rawStoryGalleryList = array_is_list($rawStoryGallery) ? array_values($rawStoryGallery) : [$rawStoryGallery];
                } else {
                    $rawStoryGalleryList = [];
                }

                $storyImages = array_values(array_map(function ($item) use ($normalizeLocalAssetUrl) {
                    $itemArr = is_array($item) ? $item : (array) $item;
                    $img = is_array($itemArr['image'] ?? null) ? $itemArr['image'] : [];
                    $fallbackAlt = trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($itemArr['title'] ?? ''))));

                    return [
                        'id' => $itemArr['id'] ?? null,
                        'url' => $normalizeLocalAssetUrl($img['url'] ?? null),
                        'alt' => $img['alt'] ?? $fallbackAlt,
                    ];
                }, array_filter($rawStoryGalleryList)));

                $storySettings = Config::getAboutStorySettings($locale);
                $hasStoryContent = ($storySettings['eyebrow'] ?? '') !== ''
                    || ($storySettings['heading'] ?? '') !== ''
                    || !empty($storySettings['paragraphs'])
                    || ($storySettings['commitment'] ?? '') !== ''
                    || !empty($storyImages);
                $storySection = $hasStoryContent
                    ? array_merge($storySettings, [
                        'images' => $storyImages,
                    ])
                    : null;

                // Section 3 — Sứ mệnh và Tầm nhìn (BANNER_ABOUT_MISSION_VISION + 2 ABOUT_MISSION_VISION_ITEMS)
                $rawMvBanner = Slider::getByPosition('BANNER_ABOUT_MISSION_VISION');
                $mvBannerArr = is_array($rawMvBanner) ? $rawMvBanner : [];
                $mvBannerImg = is_array($mvBannerArr['image'] ?? null) ? $mvBannerArr['image'] : [];
                $mvBannerMobileImg = is_array($mvBannerArr['image_mobile'] ?? null) ? $mvBannerArr['image_mobile'] : [];
                $mvBannerDesktopUrl = $normalizeLocalAssetUrl($mvBannerImg['url'] ?? null);
                $mvBannerMobileUrl = $normalizeLocalAssetUrl($mvBannerMobileImg['url'] ?? null) ?? $mvBannerDesktopUrl;
                $mvBannerAlt = $mvBannerImg['alt'] ?? trim((string) preg_replace('/\s+/', ' ', strip_tags((string) ($mvBannerArr['title'] ?? ''))));

                $rawMvItems = Slider::getByPosition('ABOUT_MISSION_VISION_ITEMS');
                if ($rawMvItems instanceof \Illuminate\Support\Collection) {
                    $rawMvItemList = $rawMvItems->values()->all();
                } elseif (is_array($rawMvItems)) {
                    $rawMvItemList = array_is_list($rawMvItems) ? array_values($rawMvItems) : [$rawMvItems];
                } else {
                    $rawMvItemList = [];
                }

                $mvItems = array_values(array_map(function ($item) {
                    $itemArr = is_array($item) ? $item : (array) $item;
                    return [
                        'id' => $itemArr['id'] ?? null,
                        'subtitle' => (string) ($itemArr['subtitle'] ?? ''),
                        'title' => (string) ($itemArr['title'] ?? ''),
                        'description' => (string) ($itemArr['description'] ?? ''),
                    ];
                }, array_filter($rawMvItemList)));

                $missionVisionSection = (!empty($mvBannerDesktopUrl) || !empty($mvItems))
                    ? [
                        'banner' => [
                            'url' => $mvBannerDesktopUrl,
                            'mobile_url' => $mvBannerMobileUrl,
                            'alt' => $mvBannerAlt,
                        ],
                        'items' => $mvItems,
                    ]
                    : null;

                // Section 4 — Dấu ấn và Cam kết (4 ABOUT_STATS_ITEMS)
                $rawStatsItems = Slider::getByPosition('ABOUT_STATS_ITEMS');
                if ($rawStatsItems instanceof \Illuminate\Support\Collection) {
                    $rawStatsList = $rawStatsItems->values()->all();
                } elseif (is_array($rawStatsItems)) {
                    $rawStatsList = array_is_list($rawStatsItems) ? array_values($rawStatsItems) : [$rawStatsItems];
                } else {
                    $rawStatsList = [];
                }

                $statsItems = array_values(array_map(function ($item) {
                    $itemArr = is_array($item) ? $item : (array) $item;
                    $label = trim((string) ($itemArr['subtitle'] ?? ''));
                    if ($label === '') {
                        $label = trim((string) ($itemArr['description'] ?? ''));
                    }
                    $value = trim((string) ($itemArr['title'] ?? ''));

                    return [
                        'id' => $itemArr['id'] ?? null,
                        'label' => $label,
                        'value' => $value,
                        'subtitle' => $label,
                        'title' => $value,
                    ];
                }, array_filter($rawStatsList)));

                // Section 5 — Đồng hành cùng khách hàng (about_companion_* settings + 2 ABOUT_COMPANION_CARDS)
                $rawCompanionCards = Slider::getByPosition('ABOUT_COMPANION_CARDS');
                if ($rawCompanionCards instanceof \Illuminate\Support\Collection) {
                    $rawCompanionCardList = $rawCompanionCards->values()->all();
                } elseif (is_array($rawCompanionCards)) {
                    $rawCompanionCardList = array_is_list($rawCompanionCards) ? array_values($rawCompanionCards) : [$rawCompanionCards];
                } else {
                    $rawCompanionCardList = [];
                }

                $companionCards = array_values(array_map(function ($item) use ($normalizeLocalAssetUrl) {
                    $itemArr = is_array($item) ? $item : (array) $item;
                    $img = is_array($itemArr['image'] ?? null) ? $itemArr['image'] : [];
                    $title = (string) ($itemArr['title'] ?? '');
                    $fallbackAlt = trim((string) preg_replace('/\s+/', ' ', strip_tags($title)));
                    $ctaText = trim((string) ($itemArr['primary_cta_text'] ?? ''));
                    $rawCtaLink = trim((string) ($itemArr['primary_cta_link'] ?? ''));
                    if ($rawCtaLink === '' || $rawCtaLink === '#') {
                        $rawCtaLink = trim((string) ($itemArr['link'] ?? ''));
                    }
                    $ctaLink = ($rawCtaLink === '' || $rawCtaLink === '#') ? '' : $rawCtaLink;

                    return [
                        'id' => $itemArr['id'] ?? null,
                        'title' => $title,
                        'description' => (string) ($itemArr['description'] ?? ''),
                        'cta_text' => $ctaText,
                        'cta_link' => $ctaLink,
                        'primary_cta_text' => $ctaText,
                        'primary_cta_link' => $ctaLink !== '' ? $ctaLink : null,
                        'target' => $itemArr['target'] ?? null,
                        'image' => [
                            'url' => $normalizeLocalAssetUrl($img['url'] ?? null),
                            'alt' => $img['alt'] ?? $fallbackAlt,
                        ],
                    ];
                }, array_filter($rawCompanionCardList)));

                $companionSettings = Config::getAboutCompanionSettings($locale);
                $companionSection = (!empty($companionSettings['eyebrow']) || !empty($companionSettings['heading']) || !empty($companionCards))
                    ? array_merge($companionSettings, [
                        'cards' => $companionCards,
                    ])
                    : null;

                return [
                    'posts' => $posts,
                    'heroBanner' => $heroBanner,
                    'storySection' => $storySection,
                    'missionVisionSection' => $missionVisionSection,
                    'statsItems' => $statsItems,
                    'companionSection' => $companionSection,
                ];
            });

            $seo = $this->resolveAboutSeo($locale);

            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            $response = Inertia::render('About/Index', array_merge($data, ['seo' => $seo]));

            return $seo !== null
                ? $response->withViewData(['seo' => $seo])
                : $response;
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    protected function resolveAboutSeo(string $locale): ?array
    {
        $metaPage = null;
        try {
            if (Schema::hasTable('meta_pages')) {
                $candidateUrls = $locale === 'en'
                    ? ['/en/about-us', 'en/about-us']
                    : ['/gioi-thieu', 'gioi-thieu'];
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
            $locale === 'en' ? ($global['seo_meta_title_en'] ?? null) : null,
            $global['seo_meta_title'] ?? null,
            $global['general_site_name'] ?? null
        );
        $description = $pick(
            $metaPage?->seo_meta_description,
            $locale === 'en' ? ($global['seo_meta_description_en'] ?? null) : null,
            $global['seo_meta_description'] ?? null,
            $global['general_description'] ?? null
        );
        $keywords = $pick(
            $metaPage?->seo_meta_keywords,
            $locale === 'en' ? ($global['seo_meta_keywords_en'] ?? null) : null,
            $global['seo_meta_keywords'] ?? null
        );
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

    public function show($slug)
    {
        try {
            $data = Cache::remember('history_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $history = History::query()
                    ->active()
                    ->whereSlug($slug)
                    ->firstOrFail();

                $histories = History::query()
                    ->active()
                    ->sortByYear()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'history' => $history->transformDetails(),
                    'histories' => $histories,
                    'seo' => $history->transformSeo(),
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('History', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }
}
