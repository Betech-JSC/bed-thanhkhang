# admin-cms-brand-theme Specification

## Purpose

Đồng bộ toàn diện giao diện đăng nhập, xác thực, thanh điều hướng Sidebar, bảng màu cấu hình và trang quản trị Admin CMS sang bộ nhận diện thương hiệu sáng & Dark Navy chuẩn Thành Khang.

## Requirements

### Requirement: Hệ thống Token màu và Favicon Admin CMS chuẩn Thành Khang
Cấu hình giao diện Admin CMS (`core/resources/Backend/tailwind.config.js`, `core/resources/Backend/scss/app.scss`, `core/resources/Backend/views/app.blade.php`) SHALL sử dụng bảng màu thương hiệu Thành Khang (`primary: #005EA4`, hover `#004D86`, active `#003967`, dải `#EDF6FF` đến `#001C36`) thay cho bảng màu `#2970FF` / `#111111` / `#0B0D19` cũ, và thay thế favicon `hh-maker-icon.svg` bằng favicon/logo thương hiệu Thành Khang hợp lệ.

#### Scenario: Biên dịch Admin CMS và kiểm tra favicon không còn thương hiệu cũ
- **WHEN** quản trị viên truy cập `/admin/login` hoặc `/admin` sau khi chạy `yarn backend:build`
- **THEN** biến `--primary` giải mã thành `#005EA4`, và thẻ `<link rel="icon">` trong trang Admin không còn tham chiếu `/assets/images/header/hh-maker-icon.svg`.

---

### Requirement: Màn hình Đăng nhập & Xác thực Admin giao diện sạch Thành Khang
Trang đăng nhập Admin (`Guest.vue`, `Login.vue`) và các trang xác thực phụ (`ForgotPassword.vue`, `ResetPassword.vue`, `ConfirmPassword.vue`, `VerifyEmail.vue`) SHALL hiển thị card trắng sạch (`bg-white border border-[#E3E8EF]`) trên nền `#FAFAFA` hoặc `#00284B` navy:
- Gỡ bỏ hoàn toàn họa tiết lưới 3D printer heated-bed mesh và huy hiệu `THÀNH KHANG TECSAT CMS • v2.0`.
- Hiển thị logo `/assets/images/logo/logo-thanhkhang.png` rõ nét trên nền sáng, ô nhập nền sáng viền rõ ràng và nút CTA chính màu `#005EA4`.

#### Scenario: Truy cập màn hình Đăng nhập Admin trên Desktop và Mobile
- **WHEN** người dùng truy cập `/admin/login` trên Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** màn hình hiển thị card đăng nhập nền trắng viền `#E3E8EF`, logo Thành Khang nạp thành công (`naturalWidth > 0`), không có lưới 3D printer hay huy hiệu `THÀNH KHANG TECSAT CMS • v2.0`, và nút submit dùng `#005EA4`.

---

### Requirement: Sidebar Admin Dark Navy #00284B và Rà soát Nội dung Sạch
Thanh điều hướng Sidebar Admin (`SidebarDesktop.vue`, `SidebarMobile.vue`, `SidebarItems.vue`, `SidebarAccount.vue`, `Authenticated.vue`, `SidebarMain.vue`) cùng các màn hình lõi (`Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`) SHALL đồng bộ nhận diện Thành Khang:
- Sidebar Desktop và Mobile sử dụng nền Dark Navy `#00284B` với phần chân tài khoản `#001C36`; mục đang chọn nổi bật với `#005EA4` / `#C9E0FF`.
- Logo `/assets/images/logo/logo-thanhkhang.png` tại đầu Sidebar MUST được đặt bên trong khung bo nền trắng sạch (`bg-white`) để đảm bảo độ tương phản với wordmark chữ tối trên nền `#00284B`.
- Bộ chuyển đổi ngôn ngữ `VI` / `EN` trên Sidebar áp dụng nút active màu `#005EA4`.
- Gỡ bỏ từ `Tecsat` (theo chuẩn rà soát thương hiệu hiển thị Admin) và các biểu tượng emoji trang trí trong tiêu đề nhóm menu của `SidebarMain.vue`; đồng bộ các nút/link accent `#2970FF` / `blue-600` trên `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue` sang hệ `#005EA4`.

#### Scenario: Hiển thị Sidebar Admin và kiểm tra độ tương phản Logo trên nền Dark Navy
- **WHEN** quản trị viên đã đăng nhập mở trang `/admin/dashboard`
- **THEN** Sidebar hiển thị nền `#00284B`, vùng tài khoản chân Sidebar hiển thị nền `#001C36`, logo Thành Khang nằm gọn trong khung nền trắng dễ đọc, tiêu đề nhóm menu trên `SidebarMain.vue` không chứa ký tự emoji, và nút ngôn ngữ active dùng màu `#005EA4`.
