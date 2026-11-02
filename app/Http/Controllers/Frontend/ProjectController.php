<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use App\Models\Post\PostCategory;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class ProjectController extends Controller
{
    public $model = Post::class;

    public function index()
    {
        try {
            $data = Cache::remember('projects_index_' . current_locale(), 3600, function () {
                $categories = PostCategory::query()
                    ->active()
                    ->where('type', PostCategory::TYPE_PROJECT)
                    ->get()
                    ->map(fn($item) => $item->transform());

                $projects = Post::query()
                    ->active()
                    ->where('type', Post::TYPE_PROJECT)
                    ->orderByDesc('id')
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'categories' => $categories,
                    'projects' => $projects,
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Projects/Index', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('project_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $project = Post::query()
                    ->active()
                    ->where('type', Post::TYPE_PROJECT)
                    ->whereSlug($slug)
                    ->firstOrFail();

                $categories = PostCategory::query()
                    ->active()
                    ->where('type', PostCategory::TYPE_PROJECT)
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'categories' => $categories,
                    'project' => $project->transformDetails(),
                    'related_projects' => $project->related(),
                    'seo' => $project->transformSeo()
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Projects/Show', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }
}
