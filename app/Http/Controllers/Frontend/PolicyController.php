<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Policy\Policy;
use Symfony\Component\HttpKernel\Exception\HttpException;

class PolicyController extends Controller
{
    public $model = Policy::class;

    public function index()
    {
        try {
            return $this->renderDataPage();
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            return $this->renderDataPage($slug);
        } catch (HttpException $e) {
            throw $e;
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }

    public function renderDataPage($slug = null)
    {
        try {
            $policies = Policy::query()
                ->active()
                ->sortByPosition()
                ->get()
                ->map(function ($item) {
                    $transformed = $item->transform();
                    $transformed['seo_slug'] = $item->seo_slug ?? null;
                    $transformed['seo_meta_title'] = $item->seo_meta_title ?? null;
                    $transformed['seo_meta_description'] = $item->seo_meta_description ?? null;

                    return $transformed;
                });
        } catch (\Throwable $th) {
            report($th);
            $policies = collect();
        }

        $content = null;

        if (!empty($slug)) {
            $content = $policies->first(function ($item) use ($slug) {
                return ($item['slug'] ?? '') === $slug
                    || ($item['seo_slug'] ?? '') === $slug
                    || ($item['slug_vi'] ?? '') === $slug
                    || ($item['slug_en'] ?? '') === $slug;
            });

            if (!$content) {
                return abort(404);
            }
        } else {
            $content = $policies->isNotEmpty() ? $policies->first() : null;
        }

        $seo = [
            'title' => $content['seo_meta_title'] ?? $content['title'] ?? null,
            'description' => $content['seo_meta_description'] ?? null,
        ];

        $data = [
            'list_sidebar' => $policies->values()->all(),
            'content' => $content,
            'seo' => $seo,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Policy', $data)
            ->withViewData(['seo' => $seo]);
    }
}
