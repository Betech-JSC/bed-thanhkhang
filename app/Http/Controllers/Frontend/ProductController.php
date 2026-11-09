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
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        try {
            $data = Cache::remember('products_index_' . current_locale(), 3600, function () {
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
                $cleanSlug = trim(urldecode($slug));

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
                    'related_products' => $product->related(['limit' => 4]),
                    'seo' => $product->transformSeo(),
                ];
            });

            if (!$data) {
                Cache::forget('product_show_' . $slug . '_' . current_locale());
                return abort(404);
            }

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

    public function galleries()
    {
        try {
            $data = Cache::remember('galleries_index_' . current_locale(), 3600, function () {
                $banner = Slider::getByPosition('HOME_SLIDER')->first();

                $products = Product::query()
                    ->active()
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
