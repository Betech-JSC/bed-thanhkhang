<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Agency\Models\Agency;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

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

                $faqs = Post::getFrontendFaqs();

                return [
                    'agencies' => $agencies,
                    'faqs' => $faqs,
                    'faqList' => $faqs,
                ];
            });

            if (empty($data['faqs'])) {
                $faqs = Post::getFrontendFaqs();
                $data['faqs'] = $faqs;
                $data['faqList'] = $faqs;
            } elseif (empty($data['faqList'])) {
                $data['faqList'] = $data['faqs'];
            }

            $locale = current_locale();
            $seo = [
                'title' => $locale === 'en'
                    ? 'Contact & RFQ Quotation - Thanh Khang | Industrial Engineering'
                    : 'Liên Hệ & Báo Giá Kỹ Thuật (RFQ) - Thành Khang',
                'description' => $locale === 'en'
                    ? 'Contact Thanh Khang hotline 0967 732 665 or info@dvktthanhkhang.vn for escalator spare parts, industrial chains, and energy saving consultations.'
                    : 'Liên hệ ngay với Công ty TNHH Thương mại và Dịch vụ Kỹ thuật Thành Khang qua hotline 0967 732 665 hoặc email info@dvktthanhkhang.vn để được tư vấn và nhận báo giá kỹ thuật nhanh chóng.',
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
