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
        $locale = current_locale();
        $data = Cache::remember('services_index_' . $locale, 3600, function () {
            $services = Service::query()
                ->active()
                ->sortByPosition()
                ->get()
                ->map(fn($item) => $item->transform())
                ->sortBy('listing_order')
                ->values();

            return [
                'services' => $services,
            ];
        });

        $seo = [
            'title' => $locale === 'en'
                ? 'Industrial Engineering Services & Energy Saving Solutions - Thanh Khang'
                : 'Dịch Vụ Kỹ Thuật & Giải Pháp Tiết Kiệm Điện - Thành Khang',
            'description' => $locale === 'en'
                ? 'Thanh Khang provides technical survey, precision mechanical manufacturing, escalator maintenance parts, and industrial BLDC fan energy-saving solutions.'
                : 'Thành Khang cung cấp dịch vụ khảo sát kỹ thuật, chế tạo cơ khí phụ tùng, cung ứng linh kiện thang cuốn và giải pháp tiết kiệm điện quạt BLDC công nghiệp.',
        ];

        if (request()->wantsJson()) {
            return response()->json(array_merge($data, ['seo' => $seo]));
        }

        return Inertia::render('Services/Index', array_merge($data, ['seo' => $seo]))
            ->withViewData(['seo' => $seo]);
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

                $serviceDetails = $service->transformDetails();

                return [
                    'service' => $serviceDetails,
                    'services' => $services,
                    'hero' => $serviceDetails['hero'] ?? $service->resolveHeroData(),
                    'toc_items' => $serviceDetails['toc_items'] ?? $service->resolveTocItems(),
                    'tocItems' => $serviceDetails['toc_items'] ?? $service->resolveTocItems(),
                    'sections' => $serviceDetails['sections'] ?? $service->resolveArticleSections(),
                    'steps' => $serviceDetails['steps'] ?? $service->resolveSteps(),
                    'checklist_items' => $serviceDetails['checklist_items'] ?? $service->resolveChecklistItems(),
                    'checklistItems' => $serviceDetails['checklistItems'] ?? $service->resolveChecklistItems(),
                    'seo' => $service->transformSeo(),
                ];
            });

            if (is_array($data['service'] ?? null)) {
                if (empty($data['service']['hero'])) {
                    $data['service']['hero'] = $data['hero'] ?? [];
                }
                if (empty($data['service']['toc_items'])) {
                    $data['service']['toc_items'] = $data['toc_items'] ?? [];
                }
                if (empty($data['service']['tocItems'])) {
                    $data['service']['tocItems'] = $data['service']['toc_items'];
                }
                if (empty($data['service']['sections'])) {
                    $data['service']['sections'] = $data['sections'] ?? [];
                }
                if (empty($data['service']['steps'])) {
                    $data['service']['steps'] = $data['steps'] ?? [];
                }
                if (empty($data['service']['checklist_items'])) {
                    $data['service']['checklist_items'] = $data['checklist_items'] ?? [];
                }
                if (empty($data['service']['checklistItems'])) {
                    $data['service']['checklistItems'] = $data['service']['checklist_items'];
                }
            }

            if (empty($data['hero'])) {
                $data['hero'] = $data['service']['hero'] ?? [];
            }
            if (empty($data['toc_items'])) {
                $data['toc_items'] = $data['service']['toc_items'] ?? [];
            }
            if (empty($data['tocItems'])) {
                $data['tocItems'] = $data['toc_items'];
            }
            if (empty($data['sections'])) {
                $data['sections'] = $data['service']['sections'] ?? [];
            }
            if (empty($data['steps'])) {
                $data['steps'] = $data['service']['steps'] ?? [];
            }
            if (empty($data['checklist_items'])) {
                $data['checklist_items'] = $data['service']['checklist_items'] ?? [];
            }
            if (empty($data['checklistItems'])) {
                $data['checklistItems'] = $data['checklist_items'];
            }

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
