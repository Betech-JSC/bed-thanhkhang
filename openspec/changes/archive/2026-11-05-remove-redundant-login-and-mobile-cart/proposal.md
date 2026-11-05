# Proposal: Gỡ bỏ trang ẩn /dang-nhap và dọn dẹp Mobile Drawer

## Why
Website HH Maker định hướng là nền tảng giới thiệu giải pháp, dịch vụ in 3D công nghiệp và nhận yêu cầu báo giá dự án (B2B / B2C Manufacturing & Prototyping), không vận hành hệ thống tài khoản thành viên bán lẻ đại trà hay giỏ hàng trực tuyến.
Trước đây, giao diện Mobile Drawer (`Header.vue`) vẫn còn chứa tàn dư khối Đăng nhập / Auth và các phần tử liên quan đến Giỏ hàng / Thông báo thành viên. Đồng thời, route `/dang-nhap` vẫn đang trỏ tới view `Users/Login` tạo ra trang ẩn không cần thiết trong sitemap và luồng người dùng.

Để hoàn thiện trải nghiệm thương hiệu chuyên nghiệp, tinh gọn theo đúng thỏa thuận đã duyệt:
1. Gỡ bỏ trang ẩn `/dang-nhap` và thiết lập 301/permanent redirect an toàn về trang chủ (`/`) để bot và người dùng không bao giờ gặp lỗi 404 hoặc truy cập trang đăng nhập mồ côi.
2. Dọn dẹp triệt để thanh Mobile Drawer trong `Header.vue`: loại bỏ phần Giỏ hàng, phần Đăng nhập/Auth và các helper function thừa (cartCount, logout, openActivityPopup, hasUnreadNotification).
3. Đảm bảo Desktop Header (5 menu, Search, Capsule VIE/ENG, Nút CTA Gradient "Liên hệ ngay") cùng các khối còn lại trên Mobile Drawer (Logo, 5 menu, Ngôn ngữ, Nút CTA "Liên hệ ngay", Hotline, Email) hiển thị 100% chuẩn đẹp, responsive và liền mạch.

## What Changes
- **Chỉnh sửa Routing (`routes/frontend.php`)**:
  - Gỡ bỏ `Route::inertia('/dang-nhap', 'Users/Login')->name('user.login');` và `Route::post('/dang-nhap', ...)`.
  - Thiết lập redirect an toàn 301: `Route::redirect('/dang-nhap', '/')->name('user.login');` chuyển hướng mọi request `/dang-nhap` về trang chủ `/`.
- **Chỉnh sửa Header (`resources/Frontend/js/Components/Header.vue`)**:
  - Xóa bỏ khối Auth / User Info trong Mobile Drawer.
  - Xóa bỏ các computed/methods không còn dùng: `cartCount`, `hasUnreadNotification`, `openActivityPopup`, `logout`.
  - Giữ nguyên các chức năng cốt lõi: 5 tabs navigation, Search Modal, Language Switcher Capsule, CTA Gradient "Liên hệ ngay", hotline, email, responsive drawer.
- **Xác thực Compilation & Test**:
  - `npm run build` thành công, không còn lỗi biên dịch Vue.
  - `php artisan test` 100% pass toàn bộ test suite.

## Capabilities

### Modified Capabilities
- `navigation-and-header`: Dọn dẹp Mobile Drawer, loại bỏ giỏ hàng và đăng nhập, giữ trải nghiệm tinh gọn, tập trung chuyển đổi liên hệ và báo giá.
- `frontend-routing`: Redirect 301 trang ẩn `/dang-nhap` về trang chủ `/`.

## Impact
- **Frontend**: `resources/Frontend/js/Components/Header.vue` được tối ưu hóa, gọn gàng, giảm kích thước bundle.
- **Backend Routing**: `routes/frontend.php` loại bỏ trang đăng nhập người dùng lẻ, chuyển hướng 301 an toàn.
- **SEO & UX**: Loại bỏ orphan page `/dang-nhap`, tập trung link equity về trang chủ `/`.
