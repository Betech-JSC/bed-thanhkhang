<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class PopupNotificationController extends Controller
{
    /**
     * Lấy danh sách hoạt động thật gần nhất cho popup thông báo.
     */
    public function getNotifications(): JsonResponse
    {
        return response()->json([
            'notifications' => [],
        ]);
    }
}
