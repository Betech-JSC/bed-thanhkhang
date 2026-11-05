<?php

namespace App\Http\Controllers\Frontend;

use Inertia\Inertia;
use Illuminate\Routing\Controller;
use App\Models\Policy\Policy;
use Illuminate\Support\Facades\Cache;

class PolicyController extends Controller
{
    public $model = Policy::class;

    public function index()
    {
        try {
            return $this->renderDataPage();
        } catch (\Throwable $th) {
            report($th);
            return abort(500);
        }
    }

    public function show($slug)
    {
        try {
            return $this->renderDataPage($slug);
        } catch (\Throwable $th) {
            report($th);
            return abort(404);
        }
    }

    public function renderDataPage($slug = null)
    {
        $locale = current_locale();

        $policies = Policy::query()
            ->active()
            ->sortByPosition()
            ->get()
            ->map(fn($item) => $item->transform());

        if (!empty($slug)) {
            $content = $policies->first(function ($item) use ($slug) {
                return ($item['slug'] ?? '') === $slug
                    || ($item['slug_vi'] ?? '') === $slug
                    || ($item['slug_en'] ?? '') === $slug;
            });

            if (!$content) {
                return abort(404);
            }
        } else {
            $content = $policies->first();
        }

        $title = $content['title'] ?? ($locale === 'en' ? 'Policies & Terms' : 'Chính sách & Điều khoản');
        $seo = [
            'title' => $locale === 'en'
                ? $title . ' - HH Maker | 3D Printing Workshop'
                : $title . ' - HH Maker | Xưởng in 3D',
            'description' => $locale === 'en'
                ? 'Official service policies, warranty terms, and privacy commitments for 3D printing and custom modeling at HH Maker.'
                : 'Thông tin chính sách dịch vụ, cam kết bảo mật và quy định bảo hành in 3D chuyên nghiệp tại HH Maker.',
        ];

        $data = [
            'list_sidebar' => $policies->values(),
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
