<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class PrizeController extends Controller
{
    public $model = Post::class;

    public function index()
    {
        try {
            $page = (int) request()->get('page', 1);
            $data = Cache::remember('prizes_index_' . current_locale() . '_page_' . $page, 3600, function () {
                $prizes = Post::query()
                    ->active()
                    ->where('type', Post::TYPE_PRIZE)
                    ->orderByDesc('id')
                    ->paginate(10)
                    ->through(fn($item) => $item->transform());

                return [
                    'prizes' => $prizes,
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Prizes/Index', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('prize_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $prize = Post::query()
                    ->active()
                    ->where('type', Post::TYPE_PRIZE)
                    ->whereSlug($slug)
                    ->firstOrFail();

                return [
                    'prize' => $prize->transformDetails(),
                    'seo' => $prize->transformSeo()
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Prizes/Show', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }
}
