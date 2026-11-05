<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Keyword;
use App\Models\KeywordRefDate;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Service;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        try {
            $locale = current_locale();
            $data = Cache::remember('home_page_data_' . $locale, 3600, function () {
                try {
                    // Get Home Slider
                    $sliders = Slider::getByPosition('HOME_SLIDER');
                    $announcements = Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER');

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

                    // Fetch Active Categories with Featured Products (Strict Featured)
                    $categories = ProductCategory::query()
                        ->active()
                        ->whereParent()
                        ->sortByPosition()
                        ->with(['products' => function ($q) {
                            $q->where('status', Product::STATUS_ACTIVE)
                              ->where('is_featured', true)
                              ->ordered();
                        }])
                        ->get()
                        ->map(function ($cat) {
                            return [
                                'id' => $cat->id,
                                'title' => $cat->title,
                                'name' => $cat->title,
                                'slug' => $cat->seo_slug ?? $cat->slug,
                                'hasBestChoice' => true,
                                'items' => $cat->products->take(4)->map(fn($p) => $p->transform()),
                            ];
                        })
                        ->filter(fn($cat) => count($cat['items']) > 0)
                        ->values();

                    // Prepare Response Data
                    return [
                        'sliders' => $sliders,
                        'announcements' => $announcements,
                        'services' => $services,
                        'categories' => $categories,
                        'products' => $products,
                        'featured_posts' => $featured_posts,
                        'feedback' => $feedback,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'sliders' => [],
                        'announcements' => [],
                        'services' => [],
                        'categories' => [],
                        'products' => [],
                        'featured_posts' => [],
                        'feedback' => [],
                    ];
                }
            });

            $seo = [
                'title' => $locale === 'en'
                    ? 'HH Maker - Turning Every Idea into Reality with 3D Printing'
                    : 'HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D',
                'description' => $locale === 'en'
                    ? 'No limits to creative space, HH Maker breathes life into each creation through modern 3D printing and precise machining.'
                    : 'Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại.',
            ];

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
            $keyword = trim((string) (request()->input('keyword') ?? request()->input('search') ?? request()->input('q') ?? ''));
            $limit = (int) (request()->input('limit') ?? 8);
            if ($limit <= 0 || $limit > 20) {
                $limit = 8;
            }

            $query = Product::query()->active();

            if (!empty($keyword)) {
                $query->where(function ($q) use ($keyword) {
                    $q->whereHas('translations', function ($t) use ($keyword) {
                        $t->where('title', 'like', "%{$keyword}%")
                            ->orWhere('description', 'like', "%{$keyword}%")
                            ->orWhere('slug', 'like', "%{$keyword}%");
                    })
                    ->orWhere('sku', 'like', "%{$keyword}%")
                    ->orWhere('material', 'like', "%{$keyword}%")
                    ->orWhere('print_technology', 'like', "%{$keyword}%");
                });
            }

            $products = $query->with(['categories', 'translations'])
                ->take($limit)
                ->get()
                ->map(function ($product) {
                    $firstCat = $product->categories->first();
                    $imageDetail = $product->getImageDetail($product->image);
                    $imageUrl = $imageDetail['url'] ?? null;
                    if (empty($imageUrl)) {
                        $imageUrl = '/assets/images/products/figma-sample-1.png';
                    }

                    return [
                        'id' => $product->id,
                        'name' => $product->title,
                        'title' => $product->title,
                        'slug' => $product->seo_slug ?? $product->slug,
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
                        'material' => $product->material,
                        'print_technology' => $product->print_technology,
                        'url' => route(current_locale() . '.products.show', ['slug' => $product->seo_slug ?? $product->slug]),
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
}
