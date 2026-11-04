<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\PostController;
use App\Http\Controllers\Backend\PostCategoryController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\AgencyController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\SliderController;
use App\Http\Controllers\Backend\ProductCategoryController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ServiceController;
use App\Http\Controllers\Backend\PolicyController;
use App\Http\Controllers\Backend\EmailTemplateController;
use App\Http\Controllers\Backend\SystemLogController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\ConfigController;

Route::localized(function () {
    Route::middleware(['auth:admin'])->name('admin.')->group(function () {
        Route::module(PostController::class);
        Route::module(ProductController::class);
        Route::module(BrandController::class);
        Route::module(PostCategoryController::class);
        Route::module(TagController::class);
        Route::module(AgencyController::class);
        Route::module(ContactController::class);

        Route::module(SliderController::class);
        Route::module(ServiceController::class);
        Route::module(ProductCategoryController::class);
        Route::module(PolicyController::class);

        Route::module(FaqController::class);
        Route::module(EmailTemplateController::class);
        Route::get('users/search', [UserController::class, 'searchUsers'])->name('users.search');
        Route::module(UserController::class);
        Route::module(ConfigController::class);

        // Quản lý Logs hệ thống
        Route::get('system-logs', [SystemLogController::class, 'index'])->name('system-logs.index');
        Route::post('system-logs/clear/{file}', [SystemLogController::class, 'clear'])->name('system-logs.clear');
        Route::delete('system-logs/{file}', [SystemLogController::class, 'destroy'])->name('system-logs.destroy');

        // Quản lý đơn hàng
        Route::module(\App\Http\Controllers\Backend\OrderController::class);
    });
});
