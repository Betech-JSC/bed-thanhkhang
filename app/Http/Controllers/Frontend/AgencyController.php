<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Agency\Models\Agency;
use Illuminate\Support\Facades\Cache;

class AgencyController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('agencies_index_' . current_locale(), 3600, function () {
                $agencies = Agency::query()
                    ->active()
                    ->SortByPosition()
                    ->get()
                    ->groupBy('region')
                    ->map(function ($items, $key) {
                        return [
                            'region_key' => $key,
                            'region_title' => __('agency.region.' . $key, [], current_locale()),
                            'agencies' => $items->map(function ($agency) {
                                return [
                                    'title' => $agency['title'],
                                    'phone' => $agency['info']['phone'] ?? '',
                                    'email' => $agency['info']['email'] ?? '',
                                    'location' => $agency['location'] ?? '',
                                    'link_google_map' => $agency['link_google_map'] ?? '',
                                    'code' => $agency['code'] ?? '',
                                ];
                            })
                        ];
                    })
                    ->sortBy(function ($item) {
                        $order = ['mien_nam' => 0, 'mien_trung' => 1, 'mien_bac' => 2];
                        return $order[$item['region_key']] ?? 3;
                    })
                    ->values();

                return [
                    'agencies' => $agencies
                ];
            });

            $locale = current_locale();
            $seo = [
                'title' => $locale === 'en'
                    ? 'Contact & Consultation - HH Maker | Turning Creativity Beyond the Screen'
                    : 'Liên hệ tư vấn - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình',
                'description' => $locale === 'en'
                    ? 'Get in touch with HH Maker to discuss 3D design concepts, industrial 3D printing services, or bespoke custom fabrication.'
                    : 'Liên hệ ngay với HH Maker để trao đổi ý tưởng thiết kế 3D, gia công in 3D công nghiệp hoặc chế tác sản phẩm custom theo yêu cầu.',
            ];

            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('Contact', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
