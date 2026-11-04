# Design: Chi Tiết Gỡ Bỏ Route Tàn Dư Trong `routes/backend.php`

## 1. Hiện Trạng `routes/backend.php`

Tệp `routes/backend.php` trước khi dọn dẹp chứa 66 dòng code:
- Import các controller không sử dụng:
  - Dòng 9: `use App\Http\Controllers\Backend\ApplyController;`
  - Dòng 10: `use App\Http\Controllers\Backend\GalleryController;`
  - Dòng 13: `use App\Http\Controllers\Backend\HistoryController;`
  - Dòng 19: `use App\Http\Controllers\Backend\MemberController;`
  - Dòng 23: `use App\Http\Controllers\Backend\LearningEnvironmentController;`
  - Dòng 24: `use App\Http\Controllers\Backend\SupportInfoController;`
- Đăng ký module và route không sử dụng:
  - Dòng 39: `Route::module(HistoryController::class);`
  - Dòng 40: `Route::module(MemberController::class);`
  - Dòng 41: `Route::module(GalleryController::class);`
  - Dòng 43: `Route::module(ApplyController::class);`
  - Dòng 48: `Route::module(LearningEnvironmentController::class);`
  - Dòng 54-56:
    ```php
    // Thông tin hỗ trợ & Chính sách
    Route::get('support-info', [SupportInfoController::class, 'index'])->name('support-info.index');
    Route::post('support-info', [SupportInfoController::class, 'store'])->name('support-info.store');
    ```
- Controller `PrizeController`: Đã được gỡ bỏ từ trước, không còn xuất hiện trong import hay route group.

## 2. Kế Hoạch Chỉnh Sửa (Target State)

### 2.1 Các dòng xóa bỏ
1. Gỡ bỏ 6 câu lệnh `use` của các controller tàn dư:
   - `ApplyController`
   - `GalleryController`
   - `HistoryController`
   - `MemberController`
   - `LearningEnvironmentController`
   - `SupportInfoController`
2. Gỡ bỏ 5 câu lệnh `Route::module(...)` tương ứng:
   - `Route::module(HistoryController::class);`
   - `Route::module(MemberController::class);`
   - `Route::module(GalleryController::class);`
   - `Route::module(ApplyController::class);`
   - `Route::module(LearningEnvironmentController::class);`
3. Gỡ bỏ 2 route thủ công của `SupportInfoController`:
   - `Route::get('support-info', [SupportInfoController::class, 'index'])->name('support-info.index');`
   - `Route::post('support-info', [SupportInfoController::class, 'store'])->name('support-info.store');`

### 2.2 Mã nguồn `routes/backend.php` sau khi tối ưu hóa
```php
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
```

## 3. Đánh Giá Tác Động & Kế Hoạch An Toàn (Impact & Safety)
- **Tác động đến Frontend**: Không tác động. Frontend không sử dụng các route này (đã dọn dẹp các trang tuyển sinh, thư viện ảnh, giải thưởng ở các change trước).
- **Tác động đến Admin CMS**: Sidebar `SidebarMain.vue` đã không còn liên kết đến các route này, việc gỡ bỏ đảm bảo URL không còn có thể bị truy cập ngoài ý muốn.
- **Tác động đến Database**: 0% tác động. Không sửa đổi hay xóa bất kỳ bảng hoặc dữ liệu database nào.
- **Khả năng phục hồi**: Thay đổi hoàn toàn nằm trong git tracking của `routes/backend.php`.
