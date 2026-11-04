# Proposal: Đồng bộ Nhận diện Thương hiệu HH Maker trong Admin CMS & Thiết kế lại Trang Đăng nhập

## Why

Trong quá trình chuẩn hóa toàn bộ nền tảng số của Xưởng in 3D **HH Maker**, hệ thống Admin CMS vẫn còn tồn đọng một số tàn dư nhận diện của thương hiệu cũ:
1. **Logo Funkystyle cũ trên Sidebar Admin**: Tệp `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue` vẫn đang liên kết trực tiếp tới `/assets/svg/logo-footer.svg`, chứa logo Funkystyle của hệ thống cũ thay vì logo HH Maker 3D.
2. **Tên ứng dụng cấu hình sai lệch (`Betech`)**: Trong `.env` và fallback của `config/app.php`, tên ứng dụng vẫn là `Betech`, dẫn tới tiêu đề trang web, tab trình duyệt và tiêu đề email gửi đi chưa đúng định danh `HH Maker`.
3. **Trang Đăng nhập Admin chưa đạt chuẩn Dark Tech**: Giao diện trang Đăng nhập (`Guest.vue` và `Login.vue`) hiện tại đang sử dụng giao diện mặc định nền sáng xám (`bg-gray-100`) đơn điệu, tương phản mạnh với phong cách công nghệ cao của xưởng in 3D HH Maker (Additive Manufacturing & Rapid Prototyping Workshop OS).

Việc thực hiện change này sẽ triệt tiêu hoàn toàn nhận diện cũ, mang lại trải nghiệm đồng nhất, chuyên nghiệp và đậm chất công nghệ cho đội ngũ kỹ thuật và quản trị viên HH Maker.

## What Changes

- **Chuẩn hóa Tên trang & Cấu hình thương hiệu**:
  - Cập nhật `.env`: Đổi `APP_NAME='HH Maker'`, `MAIL_FROM_NAME="${APP_NAME}"`, `VITE_APP_NAME="${APP_NAME}"`.
  - Cập nhật `config/app.php`: Đổi fallback `'name' => env('APP_NAME', 'HH Maker')`.
- **Đổi Logo trên Sidebar Admin CMS**:
  - Trong `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue`: Thay thế logo cũ bằng logo HH Maker 3D (`/assets/images/header/figma-header-logo.svg`), kích thước chuẩn cân đối `max-h-10 w-auto object-contain`.
  - Cập nhật vector nội dung tệp `public/assets/svg/logo-footer.svg` thành logo HH Maker để triệt tiêu mọi rủi ro cache/fallback.
- **Thiết kế lại Layout Khách (`Guest.vue`)**:
  - Chuyển sang nền Dark Tech xưởng in 3D chuyên sâu (`#0B0D19`).
  - Tích hợp hiệu ứng lưới tọa độ bàn in gia nhiệt (Heated Bed Mesh Grid `rgba(64, 73, 104, 0.15)`).
- **Thiết kế lại Trang Đăng nhập Admin (`Login.vue`)**:
  - Card Glassmorphism công nghệ cao (`#111322`, viền `border-[#404968]/50`, shadow sâu).
  - Logo HH Maker 3D phát sáng tinh tế.
  - Badge trạng thái `HH MAKER WORKSHOP OS • v2.0` kèm đèn LED xanh nhấp nháy.
  - Tiêu đề "ĐĂNG NHẬP HỆ THỐNG" & phụ đề "Hệ thống Quản trị & Điều hành Xưởng in 3D".
  - Trường Email & Password chuẩn Dark Theme (nền `#0F121F`, viền `#2B334E`, focus `#2970FF`, kèm icon trực quan).
  - Checkbox "Ghi nhớ đăng nhập" chuẩn dark theme.
  - Nút CTA "ĐĂNG NHẬP HỆ THỐNG" gradient xanh công nghệ (`from-[#2970FF] to-[#1E56CC]`), có spinner loading khi submit.
  - Footer bảo mật: "Authorized Personnel Only • Additive Manufacturing & Rapid Prototyping System".

## Capabilities

### Modified Capabilities
- `admin-cms-branding`: Đồng bộ logo HH Maker, tên thương hiệu hệ thống và trang Đăng nhập Dark Tech cho Admin CMS.

## Impact

- **Cấu hình & Môi trường**:
  - `.env`
  - `config/app.php`
- **Tài nguyên Vector & Media**:
  - `public/assets/svg/logo-footer.svg`
- **Giao diện Admin CMS**:
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue`
  - `packages/bed-package-essentials/core/resources/Backend/js/Layouts/Guest.vue`
  - `packages/bed-package-essentials/core/resources/Backend/js/Pages/Auth/Login.vue`
- **Khả năng tương thích**: Không ảnh hưởng đến luồng xác thực backend (`admin.login` route) hay cấu trúc database.
