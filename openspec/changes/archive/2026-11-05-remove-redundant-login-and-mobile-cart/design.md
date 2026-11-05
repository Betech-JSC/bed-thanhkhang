# Design: Dọn dẹp Mobile Drawer & Chuyển hướng an toàn /dang-nhap

## Context
HH Maker là trang web giới thiệu năng lực sản xuất in 3D, quy trình gia công, dự án và báo giá. Toàn bộ các tương tác khách hàng chính được điều hướng qua form Báo giá/Liên hệ (`/lien-he`), hotline và email.
Việc giữ lại các tính năng giỏ hàng, thông báo người dùng, đăng nhập cá nhân trong Mobile Drawer không phù hợp với mục tiêu B2B và gây phân tán chú ý của khách hàng truy cập bằng thiết bị di động.

## Architecture & Implementation Details

### 1. Header Component Refactor (`Header.vue`)
- **Mobile Drawer DOM**:
  - Khối chứa phía dưới (Drawer Bottom Section):
    - Khối 1: Language Switcher Capsule (VIE / ENG) được giữ nguyên.
    - Khối 2: Auth Links / User Info (`v-if="$page.props.auth?.user"`, nút Đăng xuất, chuông Thông báo & Ghi chú) bị loại bỏ hoàn toàn.
    - Khối 3: Quick Contact info (Nút CTA Gradient "Liên hệ ngay", Hotline, Email) được giữ nguyên và đảm bảo khoảng cách đệm (spacing) thẩm mỹ, touch target >= 44px chuẩn di động.
- **Script Cleanup**:
  - Loại bỏ computed `cartCount`: không còn thành phần nào hiển thị giỏ hàng trên Header.
  - Loại bỏ computed `hasUnreadNotification`: không còn hiển thị badge thông báo trong drawer.
  - Loại bỏ method `openActivityPopup`: không còn sự kiện mở popup activity từ header.
  - Loại bỏ method `logout`: việc đăng xuất của user front không còn cần thiết từ Header.

### 2. Frontend Routing Refactor (`routes/frontend.php`)
- Thay thế route GET `/dang-nhap` và POST `/dang-nhap` bằng redirect 301 chuẩn SEO:
  ```php
  Route::redirect('/dang-nhap', '/', 301)->name('user.login');
  ```
  *(Lưu ý: `->name('user.login')` giúp bảo toàn tương thích nếu có code tham chiếu tên route mà không gây lỗi Ziggy / Laravel route exception).*

### 3. Verification Matrix
- **Vue Compilation**: `npm run build` chạy không có warning hoặc error liên quan đến missing props/routes.
- **Routing**: `curl -I /dang-nhap` trả về status 301 Redirect to `/`.
- **PHP Unit/Feature Tests**: `php artisan test` pass toàn bộ test suite.
