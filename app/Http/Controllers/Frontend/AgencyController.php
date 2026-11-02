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

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Contact', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
