<?php

use App\Http\Controllers\Api\PopupNotificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Backend\SupportInfoController;
use Illuminate\Support\Facades\Route;

// Popup thông báo hoạt động gần nhất (thật / ảo)
Route::get('/popup-notifications', [PopupNotificationController::class, 'getNotifications'])
    ->name('api.popup-notifications');

// Thông tin hỗ trợ - public, không cần auth
Route::get('/support-info', [SupportInfoController::class, 'api'])
    ->name('api.support-info');

Route::localized(function () {
    Route::controller(PostController::class)->group(function () {
        Route::get(Lang::uri('instant-search') . '/{keyword}', 'instantSearch')->name('instant_search');
    });
});
