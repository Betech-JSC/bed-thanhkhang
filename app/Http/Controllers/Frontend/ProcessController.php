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

            $locale = current_locale();
            $seo = [
                'title' => $locale === 'en'
                    ? 'Professional 3D Printing & Manufacturing Process - HH Maker'
                    : 'Quy Trình Gia Công & Chế Tác 3D Chuyên Nghiệp - HH Maker',
                'description' => $locale === 'en'
                    ? 'Explore HH Maker\'s 5-step professional 3D printing and design process: From contact, detailed quotation, precise fabrication, careful packaging to safe delivery.'
                    : 'Khám phá quy trình 5 bước in 3D và thiết kế chuyên nghiệp tại HH Maker: Từ liên hệ, báo giá chi tiết, gia công chuẩn xác, đóng gói cẩn thận đến giao hàng an toàn.',
            ];

            if (request()->wantsJson()) {
                return response()->json(array_merge($data, ['seo' => $seo]));
            }

            return Inertia::render('Process/Index', array_merge($data, ['seo' => $seo]))
                ->withViewData(['seo' => $seo]);
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }
}
