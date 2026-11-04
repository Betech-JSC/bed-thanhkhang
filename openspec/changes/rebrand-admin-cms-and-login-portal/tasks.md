# Tasks: Đồng bộ Nhận diện Thương hiệu HH Maker trong Admin CMS & Thiết kế lại Trang Đăng nhập

## 1. Khởi tạo OpenSpec Change
- [x] 1.1. Tạo change `rebrand-admin-cms-and-login-portal` <!-- id: 1.1 -->
- [x] 1.2. Tạo `proposal.md` phân tích tàn dư thương hiệu cũ và định hướng giải pháp <!-- id: 1.2 -->
- [x] 1.3. Tạo `specs/admin-branding/spec.md` đặc tả chi tiết 3 yêu cầu cốt lõi <!-- id: 1.3 -->
- [x] 1.4. Tạo `design.md` thiết kế kỹ thuật components và cấu hình <!-- id: 1.4 -->
- [x] 1.5. Tạo `tasks.md` danh sách đầu việc cần thực thi <!-- id: 1.5 -->

## 2. Triển khai Code (Implementation)
- [x] 2.1. Cập nhật `.env`: Đổi `APP_NAME='HH Maker'`, `MAIL_FROM_NAME="${APP_NAME}"`, `VITE_APP_NAME="${APP_NAME}"` <!-- id: 2.1 -->
- [x] 2.2. Cập nhật `config/app.php`: Đổi fallback `'name' => env('APP_NAME', 'HH Maker')` <!-- id: 2.2 -->
- [x] 2.3. Cập nhật `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue`: Thay logo cũ thành logo HH Maker 3D `/assets/images/header/figma-header-logo.svg` với class `max-h-10 w-auto object-contain` <!-- id: 2.3 -->
- [x] 2.4. Cập nhật `public/assets/svg/logo-footer.svg`: Đồng bộ nội dung vector của HH Maker logo để triệt tiêu cache/fallback <!-- id: 2.4 -->
- [x] 2.5. Cập nhật `packages/bed-package-essentials/core/resources/Backend/js/Layouts/Guest.vue`: Thiết kế lại nền Dark Tech xưởng in 3D (`#0B0D19`) kèm hiệu ứng Heated Bed Mesh Grid `rgba(64, 73, 104, 0.15)` <!-- id: 2.5 -->
- [x] 2.6. Cập nhật `packages/bed-package-essentials/core/resources/Backend/js/Pages/Auth/Login.vue`: Thiết kế lại card đăng nhập Glassmorphism công nghệ cao, logo phát sáng, status badge kèm LED, dark inputs có icon, checkbox và CTA gradient xanh có loading spinner <!-- id: 2.6 -->

## 3. Xác minh & Biên dịch (Verification)
- [x] 3.1. Chạy `yarn backend:build` kiểm tra biên dịch Admin CMS thành công (Exit code 0) <!-- id: 3.1 -->
- [x] 3.2. Chạy `yarn frontend:build` kiểm tra Frontend không bị ảnh hưởng (Exit code 0) <!-- id: 3.2 -->
- [x] 3.3. Cập nhật toàn bộ trạng thái `[x]` trong `tasks.md` <!-- id: 3.3 -->
- [x] 3.4. Báo cáo bằng chứng chi tiết (terminal output, diff summary) <!-- id: 3.4 -->
