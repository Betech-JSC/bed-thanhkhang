# Tasks

## 1. Khởi tạo Change OpenSpec
- [x] 1.1 Khởi tạo change `cleanup-legacy-funkystyle-views` với schema `spec-driven`
- [x] 1.2 Soạn thảo `proposal.md`, `design.md`, `specs/legacy-cleanup/spec.md` và `tasks.md` chi tiết các bước thực hiện

## 2. Chuẩn hóa Controllers & Tháo gỡ Wrapper (Uncoupling)
- [x] 2.1 Cập nhật `app/Http/Controllers/Frontend/HomeController.php` để đổi `Inertia::render('Home', ...)` thành `Inertia::render('Home/Index', ...)`
- [x] 2.2 Cập nhật `app/Http/Controllers/Frontend/ProductController.php` để đổi `Inertia::render('Products/Show', ...)` thành `Inertia::render('Product/Show', ...)`
- [x] 2.3 Cập nhật `app/Http/Controllers/Frontend/HistoryController.php` để đổi `Inertia::render('About', ...)` thành `Inertia::render('About/Index', ...)`
- [x] 2.4 Xóa bỏ 4 file wrapper trung gian thừa: `Home.vue`, `About.vue`, `Products/Index.vue`, `Products/Show.vue`

## 3. Gỡ bỏ Routes Thừa Trong `routes/frontend.php` & `routes/backend.php`
- [x] 3.1 Cập nhật `routes/frontend.php`: gỡ bỏ PrizeController, ProjectController, FeedbackController, route gallery, và cập nhật route `/ve-chung-toi` sang `About/Index`
- [x] 3.2 Cập nhật `routes/backend.php`: gỡ bỏ PrizeController, ProjectController, ProjectCategoryController, FeedbackController
- [x] 3.3 Chạy lệnh tái tạo route frontend `yarn route`

## 4. Xóa Các Thư Mục & File Di Sản Thừa
- [x] 4.1 Xóa thư mục `resources/Frontend/js/Pages/Prizes/`
- [x] 4.2 Xóa thư mục `resources/Frontend/js/Pages/Projects/`
- [x] 4.3 Xóa thư mục `resources/Frontend/js/Pages/Feedback/`
- [x] 4.4 Xóa thư mục `resources/Frontend/js/Pages/Jobs/`
- [x] 4.5 Xóa file `resources/Frontend/js/Pages/Gallery.vue`
- [x] 4.6 Xóa file `resources/Frontend/js/Pages/Route.vue`
- [x] 4.7 Xóa file `resources/Frontend/js/Pages/Users/Profile/Components/FruitJourneyBanner.vue` và xóa sạch import/sử dụng trong `Users/Profile/Index.vue`

## 5. Biên Dịch Và Xác Thực Build & Regression Test
- [x] 5.1 Chạy `yarn frontend:build` kiểm tra build Vite Client & SSR đạt exit code 0 (100% module compile sạch)
- [x] 5.2 Chạy Playwright E2E suite (`npx playwright test tests/e2e/`) đảm bảo toàn bộ 8 trang cốt lõi không bị ảnh hưởng (28 passed, 0 failed)
- [x] 5.3 Hoàn thiện cập nhật `tasks.md` và gửi báo cáo chi tiết đến Orchestrator Main Agent
