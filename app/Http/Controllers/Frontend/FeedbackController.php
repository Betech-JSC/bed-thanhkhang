<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Post\Post;
use Inertia\Inertia;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;

class FeedbackController extends Controller
{
    public function index()
    {
        try {
            $data = Cache::remember('feedback_index_' . current_locale(), 3600, function () {
                $feedbacks = Post::query()
                    ->active()
                    ->whereFeedback()
                    ->get()
                    ->map(fn($post) => $post->transformFeedback());

                return [
                    'feedbacks' => $feedbacks
                ];
            });

            if (request()->wantsJson()) {
                return response()->json($data);
            }

            return Inertia::render('Feedback/Index', $data);
        } catch (\Throwable $th) {
            report($th);
            abort(500);
        }
    }
}
