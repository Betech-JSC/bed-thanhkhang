<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()
            ->active()
            ->sortByPosition()
            ->get()
            ->map(fn($item) => $item->transform());

        $data = [
            'services' => $services,
        ];

        if (request()->wantsJson()) {
            return response()->json($data);
        }

        return Inertia::render('Services/Index', $data);
    }

    public function show($slug)
    {
        try {
            $data = Cache::remember('service_show_' . $slug . '_' . current_locale(), 3600, function () use ($slug) {
                $service = Service::query()
                    ->active()
                    ->whereSlug($slug)
                    ->firstOrFail();

                $services = Service::query()
                    ->active()
                    ->sortByPosition()
                    ->get()
                    ->map(fn($item) => $item->transform());

                return [
                    'service' => $service->transformDetails(),
                    'services' => $services,
                    'seo' => $service->transformSeo(),
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Services/Show', $data)
                ->withViewData(['seo' => $data['seo']]);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }
}
