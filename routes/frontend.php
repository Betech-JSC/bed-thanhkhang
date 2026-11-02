<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Frontend\PrizeController;
use App\Http\Controllers\Frontend\PostController;
use App\Http\Controllers\Frontend\AgencyController;
use App\Http\Controllers\Frontend\Auth\UserAuthController;
use App\Http\Controllers\Frontend\HistoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ContactController;
use App\Http\Controllers\Frontend\FeedbackController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\ServiceController;
use App\Http\Controllers\Frontend\SitemapController;
use App\Http\Controllers\Frontend\PolicyController;
use App\Http\Controllers\Frontend\ProjectController;
use Inertia\Inertia;

Route::get('sitemap.xml', [SitemapController::class, 'index']);

// Hỗ trợ truy cập /ho-so-ca-nhan theo yêu cầu người dùng
Route::redirect('/ho-so-ca-nhan', '/dashboard/update-profile');

if (app()->environment('local')) {
    Route::get('/dev-login-user/{id}', function ($id) {
        $user = \App\Models\User::findOrFail($id);
        auth()->guard('user')->login($user);
        request()->session()->regenerate();
        return redirect()->route('users.profile.index');
    });
}

Route::get(Lang::uri('search-v2'), [HomeController::class, 'searchV2'])->name('api.search');

// Guest routes
Route::middleware('guest:user')->group(function () {
    Route::inertia('/dang-nhap', 'Users/Login')->name('user.login');
    Route::inertia('/dang-ky', 'Users/Register')->name('user.register');

    // Form gửi email khôi phục
    Route::inertia('/quen-mat-khau', 'Users/ForgotPassword')->name('forgot.password.index');

    Route::post('/quen-mat-khau', [UserAuthController::class, 'sendResetPass'])->name('forgot.password.email');

    // Hiển thị form nhập lại mật khẩu mới
    Route::get('/dat-lai-mat-khau/{token}', [UserAuthController::class, 'showResetForm'])->name('password.reset');

    // Gửi form cập nhật mật khẩu mới
    Route::post('/dat-lai-mat-khau', [UserAuthController::class, 'resetPass'])->name('password.update');

    Route::post('/dang-nhap', [UserAuthController::class, 'login'])->name('login');
    Route::post('/dang-ky', [UserAuthController::class, 'register'])->name('register');

    Route::get('/auth/google', [UserAuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [UserAuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

// Authenticated user routes
Route::middleware('auth:user')->prefix('dashboard')->name('users.')->group(function () {
    // Profile
    Route::get('update-profile', [UserAuthController::class, 'showProfile'])->name('profile.index');
    Route::get('profile', [UserAuthController::class, 'showProfile'])->name('profile');
    Route::post('update-profile', [UserAuthController::class, 'updateProfile'])->name('profile.update');
    Route::post('upload-avatar', [UserAuthController::class, 'uploadAvatar'])->name('profile.upload-avatar');
    Route::get('tin-yeu-thich', [UserAuthController::class, 'getWishlist'])->name('profile.wishlist');
    Route::get('update-profile/change-password', fn() => Inertia::render('Users/Profile/ResetPassword'))->name('profile.change-password');
    Route::post('reset-password', [UserAuthController::class, 'resetPassword'])->name('reset.submit');

    // Logout
    Route::post('dang-xuat', [UserAuthController::class, 'logout'])->name('logout');
});

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

            Route::get(Lang::uri('gallery'), 'galleries')->name('galleries.index');
        });

        Route::controller(FeedbackController::class)->group(function () {
            Route::get(Lang::uri('feedbacks'), 'index')->name('feedbacks.index');
        });

        // giải thưởng
        Route::controller(PrizeController::class)->group(function () {
            Route::get(Lang::uri('prizes'), 'index')->name('prizes.index');
            Route::get(Lang::uri('prizes') . '/{slug}', 'show')->name('prizes.show');
        });

        // đồ án/ dự án
        Route::controller(ProjectController::class)->group(function () {
            Route::get(Lang::uri('projects'), 'index')->name('projects.index');
            Route::get(Lang::uri('projects') . '/{slug}', 'show')->name('projects.show');
        });

        Route::get('ve-chung-toi', function () {
            return Inertia::render('About');
        });

        Route::post(Lang::uri('contacts'), [ContactController::class, 'store'])->name('contact.store');

        Route::controller(PolicyController::class)->group(function () {
            Route::get(Lang::uri('policies'), 'index')->name('policies.index');
            Route::get(Lang::uri('policies') . '/{slug}', 'show')->name('policies.show');
        });
    });
});
Route::dynamicRedirect();
