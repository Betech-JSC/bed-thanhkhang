# Tasks: Chuẩn Hóa Hệ Thống Typography Đồng Bộ Theo Figma

## 1. Cấu hình Typography Tokens & Font Stack trong Tailwind

- [x] 1.1 Cập nhật `fontFamily` trong `resources/Frontend/tailwind.config.js` để ưu tiên `'Inter'` làm phông chữ hàng đầu cho `sans` và `display`. Kiểm tra bằng cách kiểm tra file cấu hình.
- [x] 1.2 Bổ sung các tokens `display-hero` (38px/46px), `display-banner` (32px/38px), `display-section` (28px/34px), `display-card` (24px/30px), `display-step` (42px/51px) vào `theme.extend.fontSize` trong `resources/Frontend/tailwind.config.js`.

## 2. Chuẩn hóa Typography trên các Trang Giao diện Vue

- [x] 2.1 Chuẩn hóa Hero title trong `resources/Frontend/js/Pages/Product/Index.vue` từ `text-[40px]` về `text-display-hero` (`38px`) chuẩn Figma. Kiểm tra bằng cách kiểm tra code `Product/Index.vue`.
- [x] 2.2 Chuẩn hóa Hero title trong `resources/Frontend/js/Pages/About/Index.vue` từ `text-[42px]` về `text-display-hero` (`38px`) chuẩn Figma. Kiểm tra bằng cách kiểm tra code `About/Index.vue`.
- [x] 2.3 Rà soát và cập nhật các tiêu đề section/banner trên `Pages/Home/Index.vue`, `Pages/Services/Index.vue`, `Pages/Process/Index.vue`, `Pages/Contact.vue` sử dụng các token chuẩn.

## 3. Build & Verification

- [x] 3.1 Chạy `yarn frontend:build` để biên dịch cả client bundle và SSR bundle thành công với exit code 0.
- [x] 3.2 Chạy test Playwright xác thực font-family Inter và font-size 38px trên các trang chính với kết quả 100% Passed.
