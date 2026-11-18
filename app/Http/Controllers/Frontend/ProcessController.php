<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Config;
use App\Models\Post\Post;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use JamstackVietnam\Slider\Models\Slider;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class ProcessController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('process_index_' . current_locale(), 3600, function () {
                $heroBanner = Slider::getByPosition('BANNER_PROCESS_HERO');
                $steps = Config::getProcessSteps();
                $faqs = [];

                try {
                    if (Schema::hasTable('posts')) {
                        $faqs = Post::query()
                            ->active()
                            ->where('type', Post::TYPE_FAQ)
                            ->orderByPosition()
                            ->orderBy('id', 'desc')
                            ->get()
                            ->map(fn(Post $post) => $post->transformFaqs())
                            ->values()
                            ->toArray();
                    }
                } catch (\Throwable $e) {
                    report($e);
                }

                if (empty($faqs)) {
                    $faqs = Post::getFrontendFaqs();
                }

                return [
                    'heroBanner' => $heroBanner,
                    'steps' => $steps,
                    'processSteps' => $steps,
                    'faqs' => $faqs,
                ];
            });

            if (empty($data['steps'])) {
                $steps = Config::getProcessSteps();
                $data['steps'] = $steps;
                $data['processSteps'] = $steps;
            } elseif (empty($data['processSteps'])) {
                $data['processSteps'] = $data['steps'];
            }

            if (empty($data['faqs'])) {
                $data['faqs'] = Post::getFrontendFaqs();
            }

            $locale = current_locale();
            $seo = [
                'title' => $locale === 'en'
                    ? 'B2B Technical Ordering & Supply Process - Thanh Khang Tecsat'
                    : 'Quy Trình Cung Cấp Phụ Tùng B2B Chuyên Nghiệp - Thành Khang Tecsat',
                'description' => $locale === 'en'
                    ? 'Explore Thanh Khang Tecsat\'s 5-step professional industrial supply process: Technical consultation, RFQ quotation, manufacturing & quality control, packing, and safe delivery.'
                    : 'Khám phá quy trình 5 bước cung cấp thiết bị và phụ tùng công nghiệp chuyên nghiệp tại Thành Khang Tecsat: Từ tiếp nhận yêu cầu kỹ thuật, báo giá RFQ, kiểm soát chất lượng, đóng gói đến giao hàng an toàn.',
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
