# Proposal: Cập Nhật Bộ Favicon Chuẩn HH Maker & Đồng Bộ Thương Hiệu

## Why
Sau khi rà soát và kiểm tra thực tế, trình duyệt web vẫn đang hiển thị icon quả mướp đắng (FunkyStyle cũ) trên tab trình duyệt do các tệp `public/favicon.ico`, `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png` chưa được cập nhật theo bộ nhận diện mới của HH Maker. Đồng thời, thẻ `<head>` trong `app.blade.php` chưa khai báo rõ ràng các thẻ `<link rel="icon">`.
Ngoài ra, file mẫu môi trường `.env.example` cần được chuẩn hóa `APP_NAME='HH Maker'` (theo yêu cầu người dùng, giữ nguyên file `.env` cá nhân của họ).

## What Changes
1. **Tạo bộ Favicon chuẩn từ biểu tượng HH Maker**:
   - Sử dụng vector logo HH Maker từ Figma (biểu tượng chữ H cách điệu) để sinh ra:
     - `public/favicon.ico` (đa kích cỡ 16x16, 32x32, 48x48)
     - `public/favicon-16x16.png`
     - `public/favicon-32x32.png`
     - `public/apple-touch-icon.png` (180x180)
     - `public/assets/images/header/hh-maker-icon.svg`
2. **Khai báo thẻ Favicon trong `resources/Frontend/views/app.blade.php`**:
   - Thêm các thẻ `<link rel="icon">` và `<link rel="apple-touch-icon">` vào `<head>`.
3. **Cập nhật `.env.example`**:
   - Đặt `APP_NAME='HH Maker'` trong `.env.example` (tuyệt đối không đụng vào file `.env` theo yêu cầu người dùng).
4. **Kiểm tra và nghiệm thu**:
   - Build client và SSR qua `yarn frontend:build`.
   - Kiểm tra request favicon và hiển thị icon tab qua Playwright test.

## Capabilities

### New Capabilities
<!-- Không có -->

### Modified Capabilities
- `layout-shell`: Bổ sung đặc tả về cấu hình thẻ Favicon và bộ tài nguyên icon chuẩn cho Layout Shell.

## Impact
- `public/favicon.ico`, `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png`.
- `resources/Frontend/views/app.blade.php`.
- `.env.example`.
- Không tác động database hay code backend logic.
