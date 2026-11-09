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

                $seoData = [
                    'title' => current_locale() === 'en'
                        ? '3D Printed Products Catalog - HH Maker | FDM, SLA, SLS Technology'
                        : 'Danh mục Sản phẩm in 3D - HH Maker | Công nghệ FDM, SLA, SLS',
                    'description' => current_locale() === 'en'
                        ? 'Explore high-tech 3D printed products by HH Maker. Diverse materials including 8K Resin, PLA+, PETG, Carbon Fiber with micron precision.'
                        : 'Khám phá bộ sưu tập sản phẩm in 3D công nghệ cao từ HH Maker. Đa dạng vật liệu Resin 8K, PLA+, PETG, Carbon Fiber chính xác từng micron.',
                    'seo_schemas' => [
                        [
                            '@context' => 'https://schema.org',
                            '@type' => 'BreadcrumbList',
                            'itemListElement' => [
                                [
                                    '@type' => 'ListItem',
                                    'position' => 1,
                                    'name' => current_locale() === 'en' ? 'Home' : 'Trang chủ',
                                    'item' => url(current_locale() === 'en' ? '/en' : '/'),
                                ],
                                [
                                    '@type' => 'ListItem',
                                    'position' => 2,
                                    'name' => current_locale() === 'en' ? 'Products' : 'Sản phẩm',
                                    'item' => url(current_locale() === 'en' ? '/en/products' : '/san-pham'),
                                ],
                            ],
                        ],
                    ],
                ];

                return [
                    'products' => $products,
                    'categories' => $categories,
                    'seo' => $seoData,
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

                $seo = $product->transformSeo();

                // Sinh dữ liệu có cấu trúc Schema Product & Offer chuẩn Schema.org
                $productUrl = url()->current();
                $productImages = [];
                if (!empty($details['images']) && is_array($details['images'])) {
                    foreach ($details['images'] as $img) {
                        $imgUrl = is_array($img) ? ($img['url'] ?? $img['path'] ?? null) : $img;
                        if ($imgUrl) {
                            $productImages[] = \Illuminate\Support\Str::startsWith($imgUrl, ['http://', 'https://']) ? $imgUrl : url($imgUrl);
                        }
                    }
                }
                if (empty($productImages)) {
                    $productImages[] = url('/images/pdp/product_main.png');
                }

                $rawPrice = $product->price ?? $details['price'] ?? 0;
                $numericPrice = preg_replace('/[^0-9]/', '', (string)$rawPrice);
                if (empty($numericPrice) || (int)$numericPrice <= 0) {
                    $numericPrice = "250000";
                }

                $productSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'Product',
                    'name' => $product->title ?? $details['title'] ?? 'Sản phẩm HH Maker',
                    'image' => $productImages,
                    'description' => strip_tags($product->description ?? $details['description'] ?? $seo['seo_meta_description'] ?? 'Sản phẩm in 3D công nghệ cao từ HH Maker.'),
                    'sku' => $product->sku ?? ('HHM-PRD-' . str_pad($product->id, 3, '0', STR_PAD_LEFT)),
                    'brand' => [
                        '@type' => 'Brand',
                        'name' => 'HH Maker',
                    ],
                    'offers' => [
                        '@type' => 'Offer',
                        'url' => $productUrl,
                        'priceCurrency' => 'VND',
                        'price' => (string) $numericPrice,
                        'priceValidUntil' => '2028-12-31',
                        'itemCondition' => 'https://schema.org/NewCondition',
                        'availability' => 'https://schema.org/InStock',
                        'seller' => [
                            '@type' => 'Organization',
                            'name' => 'HH Maker',
                        ],
                    ],
                ];

                $isEn = current_locale() === 'en';
                $breadcrumbSchema = [
                    '@context' => 'https://schema.org',
                    '@type' => 'BreadcrumbList',
                    'itemListElement' => [
                        [
                            '@type' => 'ListItem',
                            'position' => 1,
                            'name' => $isEn ? 'Home' : 'Trang chủ',
                            'item' => url($isEn ? '/en' : '/'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 2,
                            'name' => $isEn ? 'Products' : 'Sản phẩm',
                            'item' => url($isEn ? '/en/products' : '/san-pham'),
                        ],
                        [
                            '@type' => 'ListItem',
                            'position' => 3,
                            'name' => $product->title ?? $details['title'] ?? 'Chi tiết sản phẩm',
                            'item' => $productUrl,
                        ],
                    ],
                ];

                $existingSchemas = $seo['seo_schemas'] ?? [];
                if (is_string($existingSchemas)) {
                    $existingSchemas = json_decode($existingSchemas, true) ?: [];
                }
                if (!is_array($existingSchemas)) {
                    $existingSchemas = [];
                }

                $seo['seo_schemas'] = array_merge($existingSchemas, [$productSchema, $breadcrumbSchema]);

                return [
                    'product' => $details,
                    'related_products' => $product->related(['limit' => 4]),
                    'seo' => $seo,
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
