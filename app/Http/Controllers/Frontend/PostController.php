<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\PostCategory;
use Illuminate\Support\Facades\Cache;

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

            $seo = [
                'title' => $locale === 'en'
                    ? 'News & Articles - HH Maker | 3D Printing Insights'
                    : 'Tin tức & Bài viết - HH Maker | Kiến thức công nghệ in 3D',
                'description' => $locale === 'en'
                    ? 'Explore the latest insights, updates, and knowledge on 3D printing and custom fabrication from HH Maker.'
                    : 'Tổng hợp các bài viết, tin tức công nghệ in 3D và giải pháp chế tác cơ khí, mô hình từ HH Maker.',
                'image' => '/og-image.jpg?v=20261109',
                'seo_image' => '/og-image.jpg?v=20261109',
            ];

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

    public function show($slug)
    {
        try {
            $post = $this->model::query()
                ->active()
                ->whereSlug($slug)
                ->firstOrFail();

            $post->increment('view_count');

            $data = Cache::remember('post_show_' . $slug . '_' . app()->getLocale(), 3600, function () use ($post) {
                $relatedPosts = $post->related();

                return [
                    'post' => $post->transformDetails(),
                    'related_posts' => $relatedPosts,
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
