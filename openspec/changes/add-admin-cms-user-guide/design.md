# Design: Trang Hướng dẫn sử dụng Hệ thống Admin CMS HH Maker 3D

## Context

Xem `proposal.md` để biết lý do và phạm vi nghiệp vụ.

Qua đối chiếu đồ thị kiến trúc AST (`graphify-out/graph.json`) và mã nguồn thực tế:
- `routes_backend` (`routes/backend.php`): Toàn bộ route quản trị được bọc trong `Route::localized` -> `Route::middleware(['auth:admin'])->name('admin.')->group(...)`.
- `app_http_controllers_backend_dashboardcontroller_dashboardcontroller` (`app/Http/Controllers/Backend/DashboardController.php`): Đã được import sẵn tại `routes/backend.php` (dòng 18) và xử lý màn hình tổng quan `Inertia::render('Dashboard', ...)`.
- `resources_backend_js_components_sidebarmain` (`resources/Backend/js/Components/SidebarMain.vue`): Chia thanh điều hướng bên trái thành 4 nhóm chức năng rõ rệt (`1. BẢNG ĐIỀU KHIỂN`, `2. VẬN HÀNH XƯỞNG IN 3D`, `3. NỘI DUNG & THƯƠNG HIỆU`, `4. HỆ THỐNG & CẤU HÌNH`), sử dụng helper toàn cục `can(...)`, `route(...)`, `isUrl(...)` và hệ thống icon unplugin (`<ph:...>`, `<material-symbols:...>`, `<heroicons-outline:...>`).
- `resources_backend_js_pages_dashboard` (`resources/Backend/js/Pages/Dashboard.vue`): Trang Bảng điều khiển hiện tại sử dụng thiết kế thẻ trắng bo góc (`rounded-2xl border border-slate-100 shadow-sm`), hệ màu `slate` / `blue-600` và PrimeIcons (`pi pi-*`).

## Goals / Non-Goals

**Goals:**
- Cung cấp trang `resources/Backend/js/Pages/UserGuide.vue` đồng bộ hoàn toàn với ngôn ngữ thiết kế của `Dashboard.vue` (Slate/Blue, Quiet & High-Trust UI).
- Cho phép mọi tài khoản quản trị (`auth:admin`) truy cập `/admin/user-guide` (`admin.user-guide.index`) mà không cần khai báo thêm quyền hạn trong bảng `permissions` của Database.
- Tích hợp thanh tìm kiếm từ khóa tức thì (Instant Search) và bộ lọc Tabs theo đúng 4 nhóm chức năng trên `SidebarMain.vue`.
- Tôn trọng cơ chế phân quyền `can(...)` trên từng nút điều hướng nhanh (`Mở trang quản lý ->`).

**Non-Goals:**
- Không tạo bảng Database mới hoặc CRUD chỉnh sửa tài liệu hướng dẫn trong CMS (tài liệu hướng dẫn gắn liền với cấu trúc module cố định của hệ thống HH Maker 3D).
- Không can thiệp vào bất kỳ trang hoặc API nào của Frontend khách hàng (`resources/Frontend`).

## Decisions

### 1. Lưu trữ dữ liệu hướng dẫn dưới dạng Structured Schema trong `UserGuide.vue` thay vì Database
- **Quyết định**: Đóng gói toàn bộ dữ liệu hướng dẫn (4 nhóm danh mục, 13+ thẻ module nghiệp vụ, quy trình 4 trạng thái báo giá in 3D, bảng quy chuẩn Media/CAD và checklist Đa ngôn ngữ/SEO) thành cấu trúc dữ liệu tĩnh có kiểu rõ ràng ngay trong `resources/Backend/js/Pages/UserGuide.vue`.
- **Lý do**:
  - Nội dung hướng dẫn phản ánh trực tiếp logic code và cấu trúc trường dữ liệu của CMS (như các mã trạng thái `NEW`, `RESPONSE`, `CONFIRMED`, `PROCESSED`, hoặc vị trí `HOME_FLIP_ANNOUNCEMENT`). Việc đặt trong code giúp phiên bản hóa qua Git, tìm kiếm realtime trên client với độ trễ `0ms` và tuân thủ tuyệt đối nguyên tắc không biến đổi Database khi không cần thiết.
- **Phương án thay thế đã cân nhắc**: Lưu trong bảng `posts` hoặc tạo bảng `user_guides` trong MySQL — bị loại bỏ vì làm phát sinh migration/seeder và có nguy cơ bị quản trị viên vô tình xóa mất tài liệu chuẩn của hệ thống.

### 2. Đăng ký Route qua `DashboardController@userGuide` thay vì Route Closure
- **Quyết định**: Thêm phương thức `public function userGuide()` trong `App\Http\Controllers\Backend\DashboardController` trả về `Inertia::render('UserGuide')`, và đăng ký `Route::get('user-guide', [DashboardController::class, 'userGuide'])->name('user-guide.index');` trong `routes/backend.php`.
- **Lý do**:
  - Giữ khả năng tương thích 100% với lệnh `php artisan route:cache` trên môi trường Production/CI (Route Closure có thể gây lỗi khi serialize route cache trên một số cấu hình).
  - Không áp dụng kiểm tra `current_admin()->can(...)` bên trong `userGuide()` để mọi nhân sự có tài khoản `auth:admin` (dù chỉ được phân quyền xem Báo giá hoặc viết Bài) đều mở được trang Hướng dẫn sử dụng.

### 3. Bố cục Giao diện `UserGuide.vue` & Cơ chế Lọc Kết hợp (Tab + Search)
- **Quyết định**:
  - Thiết kế trang `UserGuide.vue` gồm 4 khu vực:
    1. **Header & Search Bar**: Tiêu đề trang, mô tả ngắn, ô tìm kiếm từ khóa tức thì (`v-model="searchQuery"`) có nút xóa nhanh và bộ lọc 5 Tabs (`ALL`, `DASHBOARD`, `FACTORY`, `CONTENT`, `SYSTEM`).
    2. **Quick Workflow Banner (Quy trình Xử lý Báo giá & File CAD 4 bước)**: Sơ đồ trực quan hóa luồng `NEW (Mới)` -> `RESPONSE (Đã phản hồi)` -> `CONFIRMED (Đã xác nhận)` -> `PROCESSED (Đã xử lý)` kèm `CLOSE` / `IS_SPAM` để nhân viên xưởng nắm bắt ngay quy trình cốt lõi.
    3. **Module Guide Cards Grid**: Danh sách các thẻ hướng dẫn theo từng module. Mỗi thẻ gồm: Icon, Tên module, Nhãn nhóm, Mô tả mục đích, Các bước thao tác chuẩn (Step-by-step), Lưu ý quan trọng (Tips/Warnings) và nút `Mở trang quản lý` (kiểm tra `can(item.permission)` trước khi hiển thị `<Link>`).
    4. **Bảng Quy chuẩn Media, Tệp CAD & Checklist Đa ngôn ngữ VI/EN**: Bảng tra cứu nhanh kích thước ảnh (Hero Banner, Sản phẩm, Dịch vụ, Tin tức, OG Image), định dạng file CAD được phép tải lên và 5 bước kiểm tra trước khi xuất bản.

## Risks / Trade-offs

- **[Risk] Tài khoản quản trị viên bị giới hạn quyền nhấn vào liên kết của module không được cấp quyền sẽ gặp lỗi 403** → **Mitigation**: Mỗi mục hướng dẫn khai báo rõ thuộc tính `permission` và `routeName` (kèm `routeParams` nếu có, ví dụ `{ id: 'general' }` cho `admin.settings.form`). Trên giao diện, kiểm tra `can(module.permission)` trước khi render nút `<Link>`; nếu không có quyền thì hiển thị badge trạng thái *"Yêu cầu quyền truy cập module"* thay cho liên kết.
- **[Risk] Thay đổi tài nguyên Vue Backend cần biên dịch lại bundle** → **Mitigation**: Chạy `npm run build` (hoặc build backend theo cấu hình Vite của dự án) và đồng bộ lại `graphify . --code-only` sau khi thêm trang `UserGuide.vue`.
