# Design: Uncoupling View Wrappers and Cleaning Legacy FunkyStyle Routes/Views

## Context

Giao diện người dùng của HH Maker đã được chuyển đổi hoàn toàn sang thiết kế Dark Tech chuẩn Production. Tuy nhiên, hệ thống routing và view resolution vẫn còn lưu giữ các tệp wrapper trung gian và các route/controller thuộc theme mẫu trước đây (FunkyStyle).

Mục tiêu thiết kế:
1. Chuẩn hóa luồng điều hướng: Controllers render trực tiếp view chuẩn (`Home/Index`, `Product/Index`, `Product/Show`, `About/Index`) mà không qua wrapper thừa.
2. Dọn sạch rác và routes không còn thuộc phạm vi HH Maker.
3. Bảo toàn 100% khả năng hoạt động của các trang hiện hành và không làm gãy router hoặc các component dùng chung.

## Architecture Decisions

### Decision 1: Direct Inertia Page Resolution
- Thay đổi `Inertia::render('Home', ...)` thành `Inertia::render('Home/Index', ...)`.
- Thay đổi `Inertia::render('Products/Show', ...)` thành `Inertia::render('Product/Show', ...)`.
- Thay đổi `Inertia::render('About', ...)` thành `Inertia::render('About/Index', ...)`.
- Cập nhật closure route `ve-chung-toi` trong `routes/frontend.php` sang `Inertia::render('About/Index')`.
- Loại bỏ các file wrapper: `Home.vue`, `About.vue`, `Products/Index.vue`, `Products/Show.vue`.

### Decision 2: Route Pruning
- Trong `routes/frontend.php`, loại bỏ các route nhóm:
  - `PrizeController`
  - `ProjectController`
  - `FeedbackController`
  - `ProductController@galleries` (`/gallery`)
- Trong `routes/backend.php`, loại bỏ các module:
  - `PrizeController`
  - `ProjectController`
  - `ProjectCategoryController`
  - `FeedbackController`
- Đồng bộ Ziggy routes qua `yarn route`.

### Decision 3: File System Deletion
- Xóa các thư mục không còn route sử dụng:
  - `resources/Frontend/js/Pages/Prizes/`
  - `resources/Frontend/js/Pages/Projects/`
  - `resources/Frontend/js/Pages/Feedback/`
  - `resources/Frontend/js/Pages/Jobs/`
- Xóa các file đơn lẻ:
  - `resources/Frontend/js/Pages/Gallery.vue`
  - `resources/Frontend/js/Pages/Route.vue`
  - `resources/Frontend/js/Pages/Users/Profile/Components/FruitJourneyBanner.vue`

## Validation Strategy
- Chạy `yarn frontend:build` đảm bảo không có bất kỳ import nào trỏ tới file bị xóa.
- Chạy Playwright E2E suite (`npx playwright test tests/e2e/`) đảm bảo toàn bộ 8 trang cốt lõi đạt pass 100%.
