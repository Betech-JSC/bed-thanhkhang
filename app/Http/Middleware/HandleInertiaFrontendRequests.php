<?php

namespace App\Http\Middleware;

use App\Models\Post\Post;
use Inertia\Middleware;
use Illuminate\Http\Request;
use App\Models\Policy\Policy;
use App\Models\User;
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
        $relativeUrl = str_replace(env('APP_URL'), '',  url()->current());
        $metaPage = MetaPage::where('url', $relativeUrl ?: '/')->first();

        $locale = current_locale();
        $layoutData = \Illuminate\Support\Facades\Cache::remember('inertia_layout_shared_data_' . $locale, 3600, function () {
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

            $banner_home = Slider::getByPosition('HOME_SLIDER');
            $banner_about_1 = Slider::getByPosition('BANNER_ABOUT_SECTION_1');
            $banner_about_2 = Slider::getByPosition('BANNER_ABOUT_SECTION_2');
            $banner_gallery = Slider::getByPosition('BANNER_GALLERY');
            $banner_feedback = Slider::getByPosition('BANNER_FEEDBACK');
            $banner_course = Slider::getByPosition('BANNER_COURSE');
            $banner_resource = Slider::getByPosition('BANNER_RESOURCE');
            $banner_service = Slider::getByPosition('BANNER_SERVICE');
            $banner_prize = Slider::getByPosition('BANNER_PRIZE');

            return [
                'prizes' => $prizes,
                'learning_env' => $learning_env,
                'footer_courses' => $footerCourses,
                'members' => $members,
                'policies' => $policies,
                'faqs' => $faqs,
                'banner' => [
                    'banner_home' => $banner_home,
                    'banner_about_1' => $banner_about_1,
                    'banner_about_2' => $banner_about_2,
                    'banner_gallery' => $banner_gallery,
                    'banner_feedback' => $banner_feedback,
                    'banner_course' => $banner_course,
                    'banner_resource' => $banner_resource,
                    'banner_service' => $banner_service,
                    'banner_prize' => $banner_prize,
                ]
            ];
        });

        $global = cache_response(
            'global_settings',
            function () {
                return settings()
                    ->group('general')
                    ->all();
            },
            'settings',
        );

        $user = null;

        if (Auth::guard('user')->user()) {
            $user = User::query()
                ->where('id', Auth::guard('user')->user()->id)
                ->firstOrFail()
                ->transformDetails();
        }




        $share = array_merge(parent::share($request), [
            'global' => $global,
            'locale' => [
                'current' => current_locale(),
                'default' => config('app.locale'),
                'list' => config('app.locales'),
            ],
            'route' => [
                'url' => $request->url(),
                'path' => $request->path(),
                'name' => $request->route()->getName(),
                'query' => $request->query(),
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
