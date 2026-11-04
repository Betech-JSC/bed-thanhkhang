<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Keyword;
use App\Models\KeywordRefDate;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Models\Service;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('home_page_data_' . app()->getLocale(), 3600, function () {
                // Get Home Slider
                $sliders = Slider::getByPosition('HOME_SLIDER');

                // Fetch Active Services
                $services = Service::query()
                    ->active()
                    ->sortByPosition()
                    ->get()
                    ->map(fn($service) => $service->transform());

                // Fetch Featured Products
                $products = Product::query()
                    ->where('status', Product::STATUS_ACTIVE)
                    ->orderByDesc('id')
                    ->take(20)
                    ->get()
                    ->map(fn($product) => $product->transform());

                // Fetch Featured Posts
                $featured_posts = Post::query()
                    ->active()
                    ->wherePosts()
                    ->latest('id')
                    ->take(3)
                    ->get()
                    ->map(fn($post) => $post->transform());

                // Fetch Feedback Posts
                $feedback = Post::query()
                    ->active()
                    ->whereFeedback()
                    ->latest('id')
                    ->get()
                    ->map(fn($post) => $post->transformFeedback());

                // Prepare Response Data
                return [
                    'sliders' => $sliders,
                    'services' => $services,
                    'products' => $products,
                    'featured_posts' => $featured_posts,
                    'feedback' => $feedback,
                ];
            });

            // Return JSON or Inertia View
            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Home/Index', $data);
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['error' => 'Something went wrong.'], 500);
        }
    }

    public function search()
    {
        try {
            if (request()->wantsJson()) {
                return response()->json([]);
            }

            if (request()->has('keyword') && !request()->has('page')) {

                $searchKeyword = Keyword::firstOrCreate(['keyword' => request()->input('keyword')]);
                $searchKeyword->update(['updated_at' => now()]);

                KeywordRefDate::create(['keyword_id' => $searchKeyword->id]);
            }

            return Inertia::render('Search', []);
        } catch (\Throwable $th) {
            dd($th);
        }
    }

    public function searchV2()
    {

        try {
            $filter = [
                'keyword' => request()->input('keyword'),
                'limit' => request()->input('limit') ?? 8
            ];

            $filterPost = $filter;
            $filterPost['limit'] = 4;
            $data = Cache::remember('search_page_data_' . app()->getLocale() . '_' . request()->input('keyword'), 3600, function () use ($filter, $filterPost) {
                return [
                    'products' => Product::getSearch($filter),
                    'posts' => Post::getSearch($filterPost),
                ];
            });

            return response()->json($data);
        } catch (\Throwable $th) {
            dd($th);
        }
    }
}
