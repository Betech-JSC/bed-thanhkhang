<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\AgencyController;
use App\Http\Controllers\Frontend\Auth\UserAuthController;
use App\Http\Controllers\Frontend\HistoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ProcessController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\PolicyController;
use Inertia\Inertia;

Route::get('sitemap.xml', [SitemapController::class, 'index']);

// Chuyển hướng 301 an toàn các route thành viên cũ về trang chủ /
Route::redirect('/ho-so-ca-nhan', '/', 301);
Route::redirect('/about', '/gioi-thieu', 301);
Route::redirect('/about-us', '/gioi-thieu', 301);
Route::redirect('/ve-chung-toi', '/gioi-thieu', 301);
Route::redirect('/en/about', '/en/about-us', 301);
Route::redirect('/en/ve-chung-toi', '/en/about-us', 301);
Route::redirect('/search', '/tim-kiem', 301);
Route::redirect('/dang-nhap', '/', 301)->name('user.login');
Route::redirect('/dang-ky', '/', 301)->name('user.register');
Route::redirect('/quen-mat-khau', '/', 301)->name('forgot.password.index');
Route::redirect('/dashboard', '/', 301)->name('user.dashboard');
Route::redirect('/dashboard/{any}', '/', 301)->where('any', '.*');
Route::redirect('/dat-lai-mat-khau', '/', 301);
Route::redirect('/dat-lai-mat-khau/{token}', '/', 301)->name('password.reset');

Route::get(Lang::uri('search-v2'), [HomeController::class, 'searchV2'])->name('api.search');

// Tắt hoàn toàn các route đăng nhập / đăng ký / đổi mật khẩu khách hàng cũ
// Route::middleware('guest:user')->group(...)
// Route::middleware('auth:user')->group(...)

Route::middleware(['meta_seo', 'opening'])->group(function () {
    Route::localized(function () {
        Route::get(Lang::uri('/'), [HomeController::class, 'index'])->name('home');
        Route::get(Lang::uri('search'), [HomeController::class, 'search'])->name('search');
        Route::get(Lang::uri('/contact'), [AgencyController::class, 'index'])->name('contact');

        Route::controller(HistoryController::class)->group(function () {
            Route::get(Lang::uri('about-us'), 'index')->name('histories.index');
            Route::get(Lang::uri('histories') . '/{slug}', 'show')->name('histories.show');
        });

        Route::controller(ServiceController::class)->group(function () {
            Route::get(Lang::uri('services'), 'index')->name('services.index');
            Route::get(Lang::uri('services') . '/{slug}', 'show')->name('services.show');
        });

        Route::controller(PostController::class)->group(function () {
            Route::get(Lang::uri('posts'), 'index')->name('posts');
            Route::get(Lang::uri('posts') . '/{slug}', 'show')->name('posts.show');
        });

        Route::controller(ProductController::class)->group(function () {
            Route::get(Lang::uri('products'), 'index')->name('products.index');
            Route::get(Lang::uri('products') . '/{slug}', 'show')->name('products.show');
        });

        // quy trình cung ứng & hỗ trợ kỹ thuật
        Route::get(Lang::uri('quy-trinh'), [ProcessController::class, 'index'])->name('process.index');

        Route::post(Lang::uri('contacts'), [ContactController::class, 'store'])->name('contact.store');

        Route::controller(PolicyController::class)->group(function () {
            Route::get(Lang::uri('policies'), 'index')->name('policies.index');
            Route::get(Lang::uri('policies') . '/{slug}', 'show')->name('policies.show');
        });
    });
});
Route::dynamicRedirect();
