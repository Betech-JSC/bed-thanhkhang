<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Cache;
use App\Models\History;

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

                return [
                    'posts' => $posts,
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('About/Index', $data);
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
