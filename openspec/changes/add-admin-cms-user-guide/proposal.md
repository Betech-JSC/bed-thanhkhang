# Proposal: Trang Hướng dẫn sử dụng Hệ thống Admin CMS HH Maker 3D

## Why

Hệ thống Admin CMS HH Maker 3D hiện vận hành nhiều module nghiệp vụ chuyên biệt (Xử lý báo giá & tệp CAD 4 trạng thái, Quản lý Sản phẩm & Dịch vụ in 3D song ngữ VI/EN với cấu trúc JSON khối động, Banner & Hero kèm Flip Announcement, cấu hình Hotline/Zalo và đồng bộ SEO/Cache), nhưng chưa có trang tài liệu hướng dẫn sử dụng tích hợp trực tiếp trong CMS. Việc bổ sung trang **Hướng dẫn sử dụng CMS** ngay trên thanh điều hướng quản trị giúp quản trị viên mới và đội ngũ vận hành tra cứu quy trình chuẩn từng bước, quy chuẩn kích thước hình ảnh/file CAD và thao tác nhanh tới đúng module mà không cần rời khỏi giao diện làm việc.

## What Changes

- **Thêm Route & Action Hướng dẫn sử dụng (`admin.user-guide.index`)**:
  - Đăng ký route `GET /admin/user-guide` (`admin.user-guide.index`) trong `routes/backend.php` trỏ tới `DashboardController@userGuide` trả về trang Inertia `UserGuide` cho mọi tài khoản đã đăng nhập (`auth:admin`), không chặn quyền lẻ để mọi nhân sự quản trị đều có thể tra cứu tài liệu.
- **Tạo trang `resources/Backend/js/Pages/UserGuide.vue`**:
  - **Thanh tìm kiếm tức thì (Instant Search) & Bộ lọc Tabs**: Tìm kiếm realtime theo tiêu đề, mô tả, bước thực hiện hoặc lưu ý; lọc theo 4 nhóm chức năng đồng bộ 100% với bố cục Sidebar (`Tất cả`, `1. Bảng điều khiển`, `2. Vận hành Xưởng In 3D`, `3. Nội dung & Thương hiệu`, `4. Hệ thống, Đa ngôn ngữ & SEO`).
  - **Hướng dẫn chi tiết từng bước (Step-by-step) cho toàn bộ các module thực tế**:
    1. **Bảng điều khiển (`Dashboard`)**: Giải thích 4 thẻ KPI (Báo giá hôm nay, Dự báo báo giá tháng, Tăng trưởng MoM, Đơn hàng & Tỉ lệ chuyển đổi), biểu đồ xu hướng theo năm, biểu đồ cơ cấu 4 nhóm dịch vụ in 3D và danh sách 8 báo giá mới nhất.
    2. **Báo giá & Yêu cầu in 3D (`Contacts`)**: Quy trình xử lý 4 trạng thái chuẩn (`NEW` -> `RESPONSE` -> `CONFIRMED` -> `PROCESSED` / `CLOSE` / `IS_SPAM`), kiểm tra và tải tệp CAD đính kèm (`.STL`, `.STEP`, `.OBJ`, `.RAR`, `.ZIP`).
    3. **Quản lý Sản phẩm & Danh mục (`Products` & `ProductCategories`)**: Cập nhật thông tin song ngữ `VI` / `EN`, quản lý ảnh đại diện, thư viện ảnh, thông số kỹ thuật, trạng thái `ACTIVE` / `INACTIVE`, thứ tự hiển thị `position` và cấu hình SEO.
    4. **Dịch vụ In & Thiết kế (`Services`)**: Hướng dẫn chỉnh sửa nội dung song ngữ `VI` / `EN` và các khối JSON động đặc thù của trang dịch vụ (Hero, Bảng thông số kỹ thuật, Quy trình 5 bước, Ưu điểm, Báo giá & FAQ).
    5. **Banner & Hero Trang chủ (`Sliders`)**: Quản lý Hero Slider chính và thanh thông báo lật trên Header (`HOME_FLIP_ANNOUNCEMENT`).
    6. **Bài viết, Danh mục Tin tức & Chính sách (`Posts`, `PostCategories`, `Policies`)**: Quy trình biên tập nội dung Rich Text, gắn danh mục, cấu hình đường dẫn tĩnh (`slug`) và thẻ Meta SEO.
    7. **Hệ thống, Đa ngôn ngữ & SEO (`Settings`, `Files`, `EmailTemplates`, `Users`, `Admins`, `Roles`, `SystemLogs`)**: Cập nhật Hotline/Zalo/Thông tin công ty, quản lý tệp Media, mẫu Email thông báo, phân quyền RBAC (`Role` & `Permission`) và kiểm tra Logs / Xóa Cache hệ thống.
  - **Bảng quy chuẩn Media, Tệp CAD & Checklist Đa ngôn ngữ/SEO**: Tổng hợp kích thước ảnh khuyến nghị (Hero Banner, Sản phẩm, Dịch vụ, Bài viết, OG Image), định dạng tệp CAD hợp lệ và danh sách kiểm tra trước khi xuất bản nội dung song ngữ `VI`/`EN`.
  - **Nút thao tác nhanh (`Quick Action Link`)**: Mỗi thẻ hướng dẫn có nút `Mở trang quản lý ->` điều hướng trực tiếp tới module tương ứng, tự động kiểm tra quyền `can(...)` để chỉ hiển thị nút khi tài khoản có quyền truy cập module đó.
- **Cập nhật Thanh điều hướng và Bảng điều khiển**:
  - Cập nhật `resources/Backend/js/Components/SidebarMain.vue`: Bổ sung mục menu **"Hướng dẫn sử dụng"** trong nhóm `1. BẢNG ĐIỀU KHIỂN`.
  - Cập nhật `resources/Backend/js/Pages/Dashboard.vue`: Bổ sung nút lối tắt **"Hướng dẫn sử dụng CMS"** trên khu vực Header để quản trị viên truy cập nhanh ngay khi đăng nhập.

## Capabilities

### New Capabilities
- `admin-user-guide`: Trang hướng dẫn sử dụng tích hợp trực tiếp trong Admin CMS HH Maker 3D với tính năng tìm kiếm từ khóa tức thì, bộ lọc theo 4 nhóm nghiệp vụ, tài liệu từng bước cho toàn bộ module quản trị, bảng quy chuẩn Media/CAD/Đa ngôn ngữ và liên kết điều hướng nhanh tôn trọng phân quyền.

### Modified Capabilities
<!-- Không thay đổi yêu cầu cấp spec của các capability hiện có -->

## Impact

- **Backend Routes & Controller**: `routes/backend.php`, `app/Http/Controllers/Backend/DashboardController.php`.
- **Backend UI (Vue 3 + Inertia.js)**: `resources/Backend/js/Pages/UserGuide.vue` (mới), `resources/Backend/js/Components/SidebarMain.vue`, `resources/Backend/js/Pages/Dashboard.vue`.
- **Phạm vi an toàn (Safe Scope)**:
  - Không tạo bảng Database mới, không chạy migration hay seeder làm biến đổi dữ liệu.
  - Không xóa bất kỳ file nào trong dự án.
  - Không ảnh hưởng tới giao diện hoặc API phía khách hàng (Frontend Public).
