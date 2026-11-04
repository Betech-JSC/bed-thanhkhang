<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Cache::remember('products_index_' . current_locale(), 3600, function () {
                $products = Product::query()
                    ->active()
                    ->ordered()
                    ->get()
                    ->map(fn($item) => $item->transform());

                $categories = ProductCategory::query()
                    ->active()
                    ->sortByPosition()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'products' => $products,
                    'categories' => $categories,
                    'seo' => [
                        'title' => current_locale() === 'en'
                            ? '3D Printed Products Catalog - HH Maker | FDM, SLA, SLS Technology'
                            : 'Danh mục Sản phẩm in 3D - HH Maker | Công nghệ FDM, SLA, SLS',
                        'description' => current_locale() === 'en'
                            ? 'Explore high-tech 3D printed products by HH Maker. Diverse materials including 8K Resin, PLA+, PETG, Carbon Fiber with micron precision.'
                            : 'Khám phá bộ sưu tập sản phẩm in 3D công nghệ cao từ HH Maker. Đa dạng vật liệu Resin 8K, PLA+, PETG, Carbon Fiber chính xác từng micron.',
                    ],
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Product/Index', $data)
                ->withViewData(['seo' => $data['seo'] ?? null]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('product_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $product = Product::query()
                    ->active()
                    ->whereSlug($slug)
                    ->firstOrFail();

                return [
                    'product' => $product->transformDetails(),
                    'related_products' => $product->related(),
                    'seo' => $product->transformSeo()
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Product/Show', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }

    public function galleries()
    {
        try {
            $data = Cache::remember('galleries_index_' . current_locale(), 3600, function () {
                $banner = Slider::getByPosition('HOME_SLIDER')->first();

                $products = Product::query()
                    ->ordered()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'banner' => $banner,
                    'products' => $products
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Gallery', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
