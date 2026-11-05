# cleanup-login-cart Specification

## Purpose
TBD - created by archiving change remove-redundant-login-and-mobile-cart. Update Purpose after archive.

## Requirements

### Requirement: Chuyển hướng an toàn /dang-nhap
Khi người dùng hoặc bot truy cập URL `/dang-nhap`:
1. Server SHALL chuyển hướng 301 về trang chủ `/`.
2. Hệ thống SHALL không render giao diện `Users/Login`.
3. Đảm bảo tên route `user.login` SHALL vẫn giải quyết an toàn về redirect URL nếu có tham chiếu phụ, tránh exception route không tồn tại.

#### Scenario: Truy cập URL dang-nhap
- **WHEN** người dùng truy cập /dang-nhap
- **THEN** hệ thống chuyển hướng 301 về trang chủ /

### Requirement: Dọn dẹp Mobile Drawer trong Header.vue
Khối Mobile Drawer trong `Header.vue`:
1. SHALL không hiển thị bất kỳ thông tin nào về Giỏ hàng (Cart count).
2. SHALL không hiển thị thông tin người dùng đăng nhập, nút Đăng nhập, nút Đăng xuất, hay chuông Thông báo.
3. Cấu trúc SHALL giữ nguyên: Logo & Nút Đóng X ở đầu, 5 mục menu điều hướng chính, bộ chuyển đổi ngôn ngữ dạng capsule VIE/ENG, nút CTA "Liên hệ ngay" chuẩn gradient Figma, Hotline và Email liên hệ.
4. Code Vue script SHALL không còn chứa các thuộc tính tính toán (`cartCount`, `hasUnreadNotification`) và hàm (`logout`, `openActivityPopup`) không sử dụng.

#### Scenario: Mở Mobile Drawer
- **WHEN** người dùng mở Mobile Drawer trên thiết bị di động
- **THEN** không còn thấy phần tử giỏ hàng, thông tin đăng nhập hoặc chuông thông báo

### Requirement: Tính toàn vẹn của Desktop Header và Responsive
Thanh điều hướng Desktop Header và khả năng tương thích Responsive:
1. Desktop Header SHALL giữ nguyên 100%: Logo HH Maker, 5 tab menu chính kèm active highlight chuẩn theo trang hiện tại, Icon tìm kiếm (kích hoạt Search Modal), Capsule chuyển ngôn ngữ VIE/ENG, và nút CTA Gradient "Liên hệ ngay".
2. Touch target trên Mobile Drawer SHALL đạt chuẩn >= 44px, spacing hài hòa giữa bộ chuyển ngôn ngữ và khối CTA/Contact.

#### Scenario: Kiểm tra Desktop Header
- **WHEN** người dùng xem Header trên desktop
- **THEN** Header hiển thị chuẩn 100% không bị ảnh hưởng
