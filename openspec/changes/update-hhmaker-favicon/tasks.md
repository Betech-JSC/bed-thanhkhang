# Tasks: Cập Nhật Bộ Favicon Chuẩn HH Maker & Đồng Bộ Thương Hiệu

## 1. Sinh Bộ Favicon Mới từ Biểu Tượng HH Maker

- [x] 1.1 Sử dụng icon HH Maker để tạo bộ file `public/favicon.ico`, `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png` và `public/assets/images/header/hh-maker-icon.svg`. Kiểm tra bằng cách xác nhận sự tồn tại và kích thước các tệp.
- [x] 1.2 Bổ sung các thẻ `<link rel="icon">` và `<link rel="apple-touch-icon">` vào thẻ `<head>` trong `resources/Frontend/views/app.blade.php`. Kiểm tra bằng cách đọc file `app.blade.php`.

## 2. Đồng Bộ Tên Thương Hiệu Mẫu trong .env.example

- [x] 2.1 Cập nhật `APP_NAME='HH Maker'` trong `.env.example` (bảo đảm giữ nguyên file `.env` theo yêu cầu của người dùng). Kiểm tra bằng cách kiểm tra file `.env.example`.

## 3. Build & Kiểm Thử Nghiệm Thu

- [x] 3.1 Chạy `yarn frontend:build` để biên dịch cả client bundle và SSR bundle thành công với exit code 0.
- [x] 3.2 Viết và chạy test Playwright xác thực các thẻ favicon và tài nguyên icon load thành công HTTP 200.
