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
                    ? 'About Us - Thanh Khang Tecsat | Industrial Equipment & Escalator Spare Parts'
                    : 'Giới thiệu - Thành Khang Tecsat | Thiết bị công nghiệp & Phụ tùng thang cuốn',
                'description' => $locale === 'en'
                    ? 'Thanh Khang Tecsat specializes in supplying genuine escalator spare parts, airport baggage conveyors, industrial chains, and energy-saving BLDC fans.'
                    : 'Thành Khang Tecsat chuyên cung ứng phụ tùng thang cuốn, xích tải công nghiệp, băng chuyền hành lý sân bay và giải pháp quạt trần công nghiệp BLDC.',
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
