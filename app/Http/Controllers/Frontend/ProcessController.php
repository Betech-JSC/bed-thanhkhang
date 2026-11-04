<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;

class ProcessController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('process_index_' . current_locale(), 3600, function () {
                $heroBanner = Slider::getByPosition('BANNER_PROCESS_HERO');

                return [
                    'heroBanner' => $heroBanner,
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Process/Index', $data);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
