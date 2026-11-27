<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
use App\Models\Post\Post;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\PostCategory;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use JamstackVietnam\MetaPage\Models\MetaPage;

class PostController extends Controller
{
    public $model = Post::class;

    public function index()
    {
        try {
            $locale = current_locale();
            $data = Cache::remember('posts_index_' . $locale, 3600, function () {
                $posts = Post::query()
                    ->active()
                    ->activeCategories()
                    ->wherePosts()
                    ->orderBy('id', 'desc')
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'posts' => $posts,
                    'courses' => [],
                ];
            });

            $seo = $this->resolvePostsSeo($locale);

            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('Posts/Index', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $th) {
            report($th);
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
            return abort(500);
        }
    }

    protected function resolvePostsSeo(string $locale): array
    {
        $metaPage = null;
        try {
            if (Schema::hasTable('meta_pages')) {
                $candidateUrls = $locale === 'en'
                    ? ['/en/posts', 'en/posts']
                    : ['/tin-tuc', 'tin-tuc'];
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

        $defaultTitle = $locale === 'en'
            ? 'News & Technical Insights - Thanh Khang Tecsat'
            : 'Tin tức & Kiến thức Kỹ thuật - Thành Khang Tecsat';
        $defaultDescription = $locale === 'en'
            ? 'Explore technical guides, escalator maintenance knowledge, and industrial automation news from Thanh Khang Tecsat.'
            : 'Tổng hợp các bài viết chuyên ngành, cẩm nang bảo trì thang cuốn, xích công nghiệp và giải pháp tiết kiệm điện từ Thành Khang Tecsat.';

        $title = $pick($metaPage?->seo_meta_title, $defaultTitle);
        $description = $pick($metaPage?->seo_meta_description, $defaultDescription);

        $seoImage = null;
        $imageCandidates = [
            $metaPage?->seo_image ?? null,
            $global['seo_image'] ?? null,
            $global['general_logo'] ?? null,
            $global['logo'] ?? null,
        ];

        foreach ($imageCandidates as $rawCandidate) {
            $resolvedUrl = Config::resolveMediaSettingUrl($rawCandidate);
            if ($resolvedUrl === '') {
                continue;
            }
            $parsedPath = parse_url($resolvedUrl, PHP_URL_PATH);
            $normalizedPath = is_string($parsedPath) ? '/' . ltrim($parsedPath, '/') : $resolvedUrl;
            if ($normalizedPath === '/storage/images/logo.png' && !file_exists(public_path('storage/images/logo.png'))) {
                continue;
            }
            $seoImage = $resolvedUrl;
            break;
        }

        if ($seoImage === null && file_exists(public_path('assets/images/logo/logo-thanhkhang.png'))) {
            $seoImage = '/assets/images/logo/logo-thanhkhang.png';
        }

        return [
            'title' => $title,
            'description' => $description,
            'image' => $seoImage,
            'seo_image' => $seoImage,
        ];
    }

    public function show($slug)
    {
        try {
            $post = $this->model::query()
                ->active()
                ->whereSlug($slug)
                ->firstOrFail();

            $post->increment('view_count');

            $locale = current_locale();

            $data = Cache::remember('post_show_' . $slug . '_' . $locale, 3600, function () use ($post) {
                $relatedPosts = collect($post->related(['limit' => 3]))->values();

                if ($relatedPosts->count() < 3) {
                    $excludeIds = $relatedPosts
                        ->pluck('id')
                        ->push($post->id)
                        ->filter()
                        ->unique()
                        ->values()
                        ->all();

                    $topUpPosts = Post::query()
                        ->active()
                        ->wherePosts()
                        ->whereNotIn('id', $excludeIds)
                        ->orderByDesc('id')
                        ->take(3 - $relatedPosts->count())
                        ->get()
                        ->map(fn($item) => $item->transform());

                    $relatedPosts = $relatedPosts->concat($topUpPosts)->values();
                }

                $popularPosts = Post::query()
                    ->active()
                    ->wherePosts()
                    ->where('id', '<>', $post->id)
                    ->orderByDesc('view_count')
                    ->orderByDesc('id')
                    ->take(3)
                    ->get()
                    ->map(fn($item) => $item->transform())
                    ->values();

                return [
                    'post' => $post->transformDetails(),
                    'related_posts' => $relatedPosts,
                    'popular_posts' => $popularPosts,
                    'seo' => $post->transformSeo()
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Posts/Show', $data)
                ->withViewData([
                    'seo' => $data['seo'],
                ]);
        } catch (\Throwable $th) {
            report($th);
            if (request()->wantsJson()) {
                return response()->json(['error' => 'Something went wrong.'], 500);
            }
            abort(404);
        }
    }

    public function relatedPosts($postId)
    {
        $items = Cache::remember('related_posts_' . $postId . '_' . app()->getLocale(), 3600, function () use ($postId) {
            $post = Post::query()
                ->setEagerLoads([])
                ->with('relatedPosts', 'categories')
                ->find($postId);

            if (!$post) return [];

            $items = $post->relatedPosts()
                ->activeLocale()
                ->get()
                ->map(fn($item) => $item->transform());

            if ($items->count() == 0) {
                $category = $post->categories
                    ->where('status', PostCategory::STATUS_ACTIVE)
                    ->values()
                    ->first();

                $items = Post::query()
                    ->activeLocale()
                    ->whereHas('categories', function ($query) use ($category) {
                        $query->where('post_categories.id', $category?->id);
                    })
                    ->orderBy('id', 'desc')
                    ->take(8)
                    ->get()
                    ->map(fn($item) => $item->transform());
            }
            return $items;
        });

        return response()->json([
            'success' => true,
            'data' => $items,
            'message' => 'OK',
        ], 200);
    }
}
