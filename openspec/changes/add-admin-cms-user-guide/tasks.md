# Tasks: Trang Hướng dẫn sử dụng Hệ thống Admin CMS HH Maker 3D

## 1. Route & Điều hướng Admin

- [x] 1.1 Thêm phương thức `userGuide()` trả về `Inertia::render('UserGuide')` trong `app/Http/Controllers/Backend/DashboardController.php` và đăng ký route `GET user-guide` (`admin.user-guide.index`) trong `routes/backend.php`; kiểm chứng bằng lệnh `php artisan route:list --name=admin.user-guide.index` trả về đúng route.
- [x] 1.2 Cập nhật `resources/Backend/js/Components/SidebarMain.vue` để thêm mục menu "Hướng dẫn sử dụng" trong nhóm `1. BẢNG ĐIỀU KHIỂN` trỏ tới `route('admin.user-guide.index')` với trạng thái active `isUrl('admin.user-guide.*')`; kiểm chứng hiển thị và điều hướng trên Sidebar.
- [x] 1.3 Cập nhật `resources/Backend/js/Pages/Dashboard.vue` để bổ sung nút lối tắt "Hướng dẫn sử dụng CMS" trên phần Header trỏ tới `route('admin.user-guide.index')`; kiểm chứng nút hiển thị rõ ràng và điều hướng mượt qua Inertia.

## 2. Xây dựng trang `UserGuide.vue` & Nội dung 4 nhóm nghiệp vụ

- [x] 2.1 Khởi tạo trang `resources/Backend/js/Pages/UserGuide.vue` với Header, thanh tìm kiếm từ khóa tức thì (`searchQuery`), bộ lọc 5 Tabs (`ALL`, `DASHBOARD`, `FACTORY`, `CONTENT`, `SYSTEM`) và sơ đồ trực quan hóa quy trình 4 trạng thái xử lý Báo giá & Tệp CAD (`NEW` -> `RESPONSE` -> `CONFIRMED` -> `PROCESSED` / `CLOSE` / `IS_SPAM`); kiểm chứng thao tác gõ từ khóa và chuyển Tab lọc danh sách tức thì.
- [x] 2.2 Biên soạn đầy đủ dữ liệu hướng dẫn từng bước (Step-by-step), lưu ý vận hành và nút điều hướng nhanh (`can(item.permission)`) cho toàn bộ 13+ module thực tế thuộc 4 nhóm chức năng của HH Maker 3D cùng Bảng quy chuẩn kích thước Media, định dạng tệp CAD và Checklist Đa ngôn ngữ `VI`/`EN` + SEO; kiểm chứng các liên kết `Mở trang quản lý` hoạt động đúng phân quyền.

## 3. Build Backend Assets & Kiểm thử E2E / Playwright Visual QA

- [x] 3.1 Biên dịch tài nguyên Backend Vue 3, đồng bộ lại đồ thị kiến trúc AST bằng lệnh `graphify . --code-only` và viết/chạy Feature Test kiểm tra route `/admin/user-guide` yêu cầu `auth:admin` và trả về component Inertia `UserGuide` (`php artisan test`).
- [x] 3.2 Thực hiện kiểm thử trực quan trên trình duyệt thật qua Playwright MCP (đăng nhập Admin CMS, kiểm tra lối tắt từ `Dashboard` & `SidebarMain`, thử tìm kiếm từ khóa `CAD`, lọc theo từng Tab nghiệp vụ, kiểm tra bảng quy chuẩn Media/CAD và chụp ảnh màn hình Desktop/Mobile làm bằng chứng nghiệm thu).
