<?php

namespace App\Http\Middleware;

use App\Models\Config;
use App\Models\Keyword;
use App\Models\Post\Post;
use Inertia\Middleware;
use Illuminate\Http\Request;
use App\Models\Policy\Policy;
use App\Models\User;
use App\Models\Product\ProductCategory;
use JamstackVietnam\MetaPage\Models\MetaPage;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Auth;


use Illuminate\Contracts\View\Factory as ViewFactory;

class HandleInertiaFrontendRequests extends Middleware
{
    protected $rootView = 'frontend::app';
    protected const FEATURED_POSITION_POST_CATEGORIES = 1;
    protected const MENU_POSITION_POST_CATEGORIES = 2;

    protected $viewFactory;

    public function __construct(ViewFactory $viewFactory)
    {
        $this->viewFactory = $viewFactory;
    }

    public function share(Request $request)
    {
        $relativeUrl = $request->path() !== '/' ? '/' . ltrim($request->path(), '/') : '/';
        try {
            $metaPage = \Illuminate\Support\Facades\Schema::hasTable('meta_pages')
                ? MetaPage::where('url', $relativeUrl ?: '/')->first()
                : null;
        } catch (\Throwable $e) {
            $metaPage = null;
        }

        $locale = current_locale();
        $defaultLayoutData = [
            'prizes' => [],
            'learning_env' => [],
            'footer_courses' => [],
            'members' => [],
            'policies' => [],
            'faqs' => [],
            'product_categories' => [],
            'popular_keywords' => [],
            'globalCtaBanner' => null,
            'banner' => [
                'banner_home' => null,
                'BANNER_GLOBAL_CTA' => null,
                'global_cta' => null,
            ],
        ];
        try {
            $layoutData = \Illuminate\Support\Facades\Cache::remember('inertia_layout_shared_data_' . $locale, 3600, function () use ($defaultLayoutData) {
                try {
                    $footerCourses = Post::query()
                        ->active()
                        ->whereMenuFooter()
                        ->get()
                        ->map(fn($item) => $item->transform());

                    $prizes = Post::query()
                        ->active()
                        ->wherePrizes()
                        ->get()
                        ->map(fn($item) => $item->transform());

                    $learning_env = Post::query()
                        ->active()
                        ->whereLearningEnv()
                        ->get()
                        ->map(fn($item) => $item->transform());

                    $policies = Policy::query()
                        ->active()
                        ->sortByPosition()
                        ->get()
                        ->map(fn($item) => $item->transform());


                    $members = Post::query()
                        ->active()
                        ->where('type', Post::TYPE_MEMBER)
                        ->get()
                        ->map(fn($item) => $item->transformMember());

                    $faqs = Post::query()
                        ->active()
                        ->where('type', Post::TYPE_FAQ)
                        ->get()
                        ->map(fn($item) => $item->transformFaqs());

                    $productCategories = ProductCategory::query()
                        ->active()
                        ->whereParent()
                        ->sortByPosition()
                        ->with(['children', 'nodes'])
                        ->get()
                        ->map(fn($item) => $item->transform());

                    $popularKeywords = [];
                    try {
                        if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                            $popularKeywords = Config::getKeywords()
                                ->filter(fn($kw) => is_string($kw) && trim($kw) !== '')
                                ->map(fn($kw) => trim($kw))
                                ->unique()
                                ->values()
                                ->all();
                        }
                        if (empty($popularKeywords) && \Illuminate\Support\Facades\Schema::hasTable('keywords')) {
                            $popularKeywords = Keyword::query()
                                ->orderByDesc('search_count')
                                ->orderByDesc('updated_at')
                                ->limit(8)
                                ->pluck('keyword')
                                ->filter(fn($kw) => is_string($kw) && trim($kw) !== '')
                                ->map(fn($kw) => trim($kw))
                                ->unique()
                                ->values()
                                ->all();
                        }
                    } catch (\Throwable $e) {
                        $popularKeywords = [];
                    }

                    $banner_home = Slider::getByPosition('HOME_SLIDER');
                    $globalCtaBanner = Slider::getByPosition('BANNER_GLOBAL_CTA');

                    return [
                        'prizes' => $prizes,
                        'learning_env' => $learning_env,
                        'footer_courses' => $footerCourses,
                        'members' => $members,
                        'policies' => $policies,
                        'faqs' => $faqs,
                        'product_categories' => $productCategories,
                        'popular_keywords' => $popularKeywords,
                        'globalCtaBanner' => $globalCtaBanner,
                        'banner' => [
                            'banner_home' => $banner_home,
                            'BANNER_GLOBAL_CTA' => $globalCtaBanner,
                            'global_cta' => $globalCtaBanner,
                        ]
                    ];
                } catch (\Throwable $e) {
                    return $defaultLayoutData;
                }
            });
        } catch (\Throwable $e) {
            $layoutData = $defaultLayoutData;
        }

        try {
            $global = cache_response(
                'global_settings_' . $locale,
                function () use ($locale) {
                    if (method_exists(Config::class, 'getGeneralSettings')) {
                        return Config::getGeneralSettings($locale);
                    }

                    return \Illuminate\Support\Facades\Schema::hasTable('settings')
                        ? settings()
                            ->group('general')
                            ->all()
                        : [];
                },
                'settings',
            );
            if (method_exists(Config::class, 'normalizeGeneralSettings')) {
                $global = Config::normalizeGeneralSettings(is_array($global) ? $global : [], $locale);
            } else {
                $global = is_array($global) ? $global : [];
            }
        } catch (\Throwable $e) {
            $global = [];
        }

        $user = null;

        if (Auth::guard('user')->user()) {
            $user = User::query()
                ->where('id', Auth::guard('user')->user()->id)
                ->firstOrFail()
                ->transformDetails();
        }




        $share = array_merge(parent::share($request), [
            'global' => $global,
            'configs' => $global,
            'globalCtaBanner' => $layoutData['globalCtaBanner'] ?? null,
            'product_categories' => $layoutData['product_categories'] ?? [],
            'popular_keywords' => $layoutData['popular_keywords'] ?? [],
            'locale' => [
                'current' => current_locale(),
                'default' => config('app.locale'),
                'list' => config('app.locales'),
            ],
            'route' => [
                'url' => $request->url(),
                'path' => $request->path(),
                'name' => optional($request->route())->getName(),
                'query' => $request->query(),
                'params' => optional($request->route())->parameters() ?? [],
            ],
            'auth' => [
                'user' => $user ?? null,
            ],
            'data' => [
                'prizes' => $layoutData['prizes'],
                'learning_env' => $layoutData['learning_env'],
                'footer_courses' => $layoutData['footer_courses'],
                'members' => $layoutData['members'],
                'policies' => $layoutData['policies'],
                'faqs' => $layoutData['faqs'],
                'product_categories' => $layoutData['product_categories'] ?? [],
                'banner' => $layoutData['banner']
            ],
            'flash' => [
                'success' => fn() => session('success'),
                'error' => fn() => session('error'),
            ],
        ]);

        if (!$metaPage) {
            $route = $request->route();
            if ($route) {
                $routeName = $route->getName();
                $slug = $route->parameter('slug');

                if ($slug) {
                    $locale = current_locale();

                    if (str_contains($routeName, 'projects.show')) {
                        $metaPage = \Illuminate\Support\Facades\Cache::get('project_show_' . $slug . '_' . $locale)['seo'] ?? null;
                        if (!$metaPage) {
                            $post = Post::active()->where('type', Post::TYPE_PROJECT)->whereSlug($slug)->first();
                            if ($post) $metaPage = $post->transformSeo();
                        }
                    } elseif (str_contains($routeName, 'posts.show')) {
                        $metaPage = \Illuminate\Support\Facades\Cache::get('post_show_' . $slug . '_' . $locale)['seo'] ?? null;
                        if (!$metaPage) {
                            $post = Post::active()->where('type', Post::TYPE_POST)->whereSlug($slug)->first();
                            if ($post) $metaPage = $post->transformSeo();
                        }
                    } elseif (str_contains($routeName, 'prizes.show')) {
                        $metaPage = \Illuminate\Support\Facades\Cache::get('prize_show_' . $slug . '_' . $locale)['seo'] ?? null;
                        if (!$metaPage) {
                            $post = Post::active()->where('type', Post::TYPE_PRIZE)->whereSlug($slug)->first();
                            if ($post) $metaPage = $post->transformSeo();
                        }
                    } elseif (str_contains($routeName, 'products.show')) {
                        $metaPage = \Illuminate\Support\Facades\Cache::get('product_show_' . $slug . '_' . $locale)['seo'] ?? null;
                        if (!$metaPage) {
                            $product = \App\Models\Product\Product::active()->whereSlug($slug)->first();
                            if ($product) $metaPage = $product->transformSeo();
                        }
                    } elseif (str_contains($routeName, 'policies.show')) {
                        $policy = Policy::active()->whereSlug($slug)->first();
                        if ($policy) $metaPage = $policy->transformSeo();
                    }
                }
            }
        }

        if ($metaPage) {
            $share['seo'] = $metaPage;
            $this->viewFactory->share('seo', $metaPage);
        }

        return $share;
    }
}
