<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
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
            $data = Cache::remember('home_page_data_' . $locale, 3600, function () use ($locale) {
                try {
                    // Get Home Slider & Banners
                    $sliders = Slider::getByPosition('HOME_SLIDER');
                    $announcements = Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER');
                    $founderBanner = Slider::getByPosition('BANNER_HOME_FOUNDER');

                    // Fetch Active Services
                    $services = Service::query()
                        ->active()
                        ->sortByPosition()
                        ->get()
                        ->map(function ($service) {
                            $item = $service->transform();
                            $item['title'] = $item['home_title'] ?? $item['title'];
                            $item['description'] = $item['overview_description'] ?? $item['description'];
                            $item['image'] = ['url' => null, 'alt' => $item['title']];
                            return $item;
                        });

                    // Fetch 5 Process Steps (CMS PROCESS_STEPS posts with fallback to Config::getProcessSteps)
                    $configSteps = Config::getProcessSteps($locale);
                    $processPosts = Post::query()
                        ->where('type', 'PROCESS_STEPS')
                        ->active()
                        ->orderBy('position')
                        ->orderBy('id')
                        ->get();

                    if ($processPosts->count() >= 5) {
                        $processSteps = $processPosts->take(5)->values()->map(function ($post, $index) use ($configSteps) {
                            $fallback = $configSteps[$index] ?? [];
                            $stepNum = $fallback['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                            $rawContent = strip_tags($post->content ?? '');
                            $items = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $rawContent))));
                            if (empty($items) && !empty($post->description)) {
                                $items = [$post->description];
                            }
                            $imgDetail = $post->getImageDetail($post->image);

                            return array_merge($fallback, [
                                'step' => $stepNum,
                                'number' => $stepNum,
                                'title' => $post->title ?: ($fallback['title'] ?? ''),
                                'description' => $post->description ?: ($fallback['description'] ?? ''),
                                'detail' => $post->description ?: ($fallback['description'] ?? ($items[0] ?? '')),
                                'items' => $items,
                                'image' => $imgDetail['url'] ?? '/images/process/process_hero_bg.webp',
                            ]);
                        })->toArray();
                    } else {
                        $processSteps = collect($configSteps)
                            ->map(function ($step, $index) {
                                $stepNum = $step['number'] ?? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT);
                                return array_merge($step, [
                                    'step' => $stepNum,
                                    'number' => $stepNum,
                                    'detail' => $step['detail'] ?? $step['description'] ?? '',
                                ]);
                            })
                            ->values()
                            ->toArray();
                    }

                    // Fetch Featured Products (ưu tiên sản phẩm được tích nổi bật is_featured)
                    $products = Product::query()
                        ->where('status', Product::STATUS_ACTIVE)
                        ->orderByFeatured()
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
                        'founderBanner' => $founderBanner,
                        'services' => $services,
                        'processSteps' => $processSteps,
                        'categories' => $categories,
                        'products' => $products,
                        'featured_posts' => $featured_posts,
                        'feedback' => $feedback,
                    ];
                } catch (\Throwable $e) {
                    return [
                        'sliders' => [],
                        'announcements' => [],
                        'founderBanner' => null,
                        'services' => [],
                        'processSteps' => Config::getProcessSteps($locale),
                        'categories' => [],
                        'products' => [],
                        'featured_posts' => [],
                        'feedback' => [],
                    ];
                }
            });

            $seo = [
                'title' => $locale === 'en'
                    ? 'Thanh Khang Tecsat - Premium Industrial Spare Parts & Escalator Components'
                    : 'Thành Khang Tecsat - Phụ Tùng Thang Cuốn & Xích Tải Công Nghiệp Cao Cấp',
                'description' => $locale === 'en'
                    ? 'Thanh Khang Tecsat specializes in supplying high-durability escalator components, heavy conveyor chains, and industrial energy-saving solutions with nationwide B2B service.'
                    : 'Công ty TNHH Kỹ thuật Thành Khang Tecsat chuyên cung cấp linh kiện thang cuốn, xích tải công nghiệp, giải pháp tiết kiệm điện và dịch vụ kỹ thuật B2B toàn quốc.',
                'image' => '/og-image.jpg',
                'seo_image' => '/og-image.jpg',
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
                    if (empty($imageUrl)) {
                        $imageUrl = '/images/products/thanhkhang/xich-bac-thang-thuong-mai/commercial-type.webp';
                    }

                    $material = $this->translateTerm($product->material, $locale);
                    $printTech = $this->translateTerm($product->print_technology, $locale);

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
                        'material' => $material,
                        'print_technology' => $printTech,
                        'url' => route($locale . '.products.show', ['slug' => $product->seo_slug ?? $product->slug]),
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
        return [
            // Materials & Supply Terms
            'Thép hợp kim / Thép carbon nhiệt luyện (Universal Chains)' => 'Heat-Treated Alloy / Carbon Steel (Universal Chains)',
            'Thép hợp kim cường độ cao' => 'High-Tensile Alloy Steel',
            'Thép carbon nhiệt luyện' => 'Heat-Treated Carbon Steel',
            'Thép không gỉ công nghiệp' => 'Industrial Stainless Steel',
            'Liên hệ đặt hàng & báo giá B2B' => 'Contact for B2B Order & Quotation',
        ];
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
