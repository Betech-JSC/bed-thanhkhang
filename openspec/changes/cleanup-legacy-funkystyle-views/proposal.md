# Proposal: Safe Cleanup of Legacy FunkyStyle Views and Uncoupling Wrappers

## Why

Dự án HH Maker đã hoàn tất triển khai giao diện mới và các trang cốt lõi (`Home`, `About`, `Product`, `Services`, `Process`, `Contact`, `Policies`, `Dashboard`). Tuy nhiên, trong mã nguồn vẫn tồn đọng các file wrapper trung gian thừa (`Pages/Home.vue`, `Pages/About.vue`, `Pages/Products/Index.vue`, `Pages/Products/Show.vue`) kế thừa từ theme cũ FunkyStyle. Các Controllers trước đây trỏ tới các wrapper này thay vì trực tiếp vào views chuẩn (`Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, `Product/Show.vue`).

Đồng thời, hệ thống routes (`routes/frontend.php` và `routes/backend.php`) vẫn còn khai báo các route không thuộc phạm vi HH Maker (`prizes`, `projects`, `feedbacks`, `gallery`) và các thư mục view di sản (`Pages/Prizes/`, `Pages/Projects/`, `Pages/Feedback/`, `Pages/Jobs/`, `Pages/Gallery.vue`, `Pages/Route.vue`, `FruitJourneyBanner.vue`) không còn bất kỳ route nào sử dụng.

Việc dọn dẹp an toàn các file và route di sản này theo quy chuẩn OpenSpec giúp tinh gọn codebase, giảm dung lượng build bundle frontend, loại bỏ technical debt và chuẩn hóa theo quy tắc Clean Architecture của dự án.

## What Changes

1. **Uncoupling Controllers & Tháo dỡ Wrapper Components**:
   - Trong `app/Http/Controllers/Frontend/HomeController.php`: Đổi `Inertia::render('Home', ...)` sang `Inertia::render('Home/Index', ...)`.
   - Trong `app/Http/Controllers/Frontend/ProductController.php`: Đổi `Inertia::render('Products/Show', ...)` sang `Inertia::render('Product/Show', ...)`.
   - Trong `app/Http/Controllers/Frontend/HistoryController.php`: Đổi `Inertia::render('About', ...)` sang `Inertia::render('About/Index', ...)`.
   - Trong `routes/frontend.php`: Chuẩn hóa closure route `/ve-chung-toi` trỏ trực tiếp sang `Inertia::render('About/Index')`.
   - Xóa bỏ 4 file wrapper trung gian thừa:
     * `resources/Frontend/js/Pages/Home.vue`
     * `resources/Frontend/js/Pages/About.vue`
     * `resources/Frontend/js/Pages/Products/Index.vue`
     * `resources/Frontend/js/Pages/Products/Show.vue`

2. **Gỡ bỏ routes thừa trong `routes/frontend.php` & `routes/backend.php`**:
   - Trong `routes/frontend.php`:
     * Gỡ bỏ import và group của `PrizeController` (`prizes.index`, `prizes.show`).
     * Gỡ bỏ import và group của `ProjectController` (`projects.index`, `projects.show`).
     * Gỡ bỏ import và group của `FeedbackController` (`feedbacks.index`).
     * Gỡ bỏ route `galleries.index` (`/gallery`).
   - Trong `routes/backend.php`:
     * Gỡ bỏ import và `Route::module(...)` của `PrizeController`, `ProjectController`, `ProjectCategoryController`, `FeedbackController`.
   - Chạy lệnh cập nhật route Ziggy `yarn route`.

3. **Xóa các thư mục & files di sản thừa**:
   - `resources/Frontend/js/Pages/Prizes/`
   - `resources/Frontend/js/Pages/Projects/`
   - `resources/Frontend/js/Pages/Feedback/`
   - `resources/Frontend/js/Pages/Jobs/`
   - `resources/Frontend/js/Pages/Gallery.vue`
   - `resources/Frontend/js/Pages/Route.vue`
   - `resources/Frontend/js/Pages/Users/Profile/Components/FruitJourneyBanner.vue`

4. **Xác thực Build và Regression Testing**:
   - Kiểm tra build Vite Client và SSR: `yarn frontend:build` đạt 100% exit code 0.
   - Chạy lại toàn bộ test suite Playwright E2E (`npx playwright test tests/e2e/`) đảm bảo 8 trang cốt lõi của HH Maker không bị ảnh hưởng.

## Capabilities

### Modified Capabilities
- `legacy-cleanup`: Dọn dẹp routes và views thừa của FunkyStyle, uncouple các view wrapper để Controller trỏ trực tiếp vào view chuẩn.

## Impact

- **Controllers**:
  - `HomeController`, `ProductController`, `HistoryController` kết nối trực tiếp đến các trang chuẩn (`Home/Index`, `Product/Show`, `About/Index`).
- **Routes**:
  - `routes/frontend.php` và `routes/backend.php` loại bỏ hoàn toàn các controller/module không dùng.
- **Frontend Pages**:
  - Giảm thiểu số lượng file trong `resources/Frontend/js/Pages/`, loại bỏ các file wrapper và thư mục thừa.
- **Tính an toàn**:
  - Không phá vỡ bất kỳ route nào của HH Maker (`/`, `/san-pham`, `/ve-chung-toi`, `/dich-vu`, `/quy-trinh`, `/lien-he`, `/dashboard`, `/policies`).
