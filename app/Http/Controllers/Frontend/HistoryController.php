<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Cache;
use App\Models\History;
use JamstackVietnam\Slider\Models\Slider;

class HistoryController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('history_index_' . current_locale(), 3600, function () {
                $posts = Post::query()
                    ->active()
                    ->orderByHomePosition()
                    ->orderBy('id', 'desc')
                    ->take(3)
                    ->get()
                    ->map(fn($item) => $item->transform());

                $heroBanner = Slider::getByPosition('BANNER_ABOUT_HERO');

                return [
                    'posts' => $posts,
                    'heroBanner' => $heroBanner,
                ];
            });

            $locale = current_locale();
            $seo = [
                'title' => $locale === 'en'
                    ? 'About Us - HH Maker | Turning Creativity Beyond the Screen'
                    : 'Giới thiệu - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình',
                'description' => $locale === 'en'
                    ? 'HH Maker connects 3D art with modern manufacturing technology, transforming initial ideas into physical products you can hold in your hands.'
                    : 'HH Maker kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm vật lý bạn cầm trên tay.',
            ];

            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('About/Index', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('history_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $history = History::query()
                    ->active()
                    ->whereSlug($slug)
                    ->firstOrFail();

                $histories = History::query()
                    ->active()
                    ->sortByYear()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'history' => $history->transformDetails(),
                    'histories' => $histories,
                    'seo' => $history->transformSeo(),
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('History', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }
}
