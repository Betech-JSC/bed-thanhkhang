# Design: Cập Nhật Bộ Favicon Chuẩn HH Maker & Đồng Bộ Thương Hiệu

## Context
Xem chi tiết tại `proposal.md` và `specs/layout-shell/spec.md`.  
Người dùng phát hiện icon quả khổ qua (FunkyStyle) vẫn hiển thị trên tab trình duyệt khi mở web.  
Nguyên nhân: `public/favicon.ico`, `public/favicon-16x16.png`, `public/favicon-32x32.png`, `public/apple-touch-icon.png` là các file di sản của FunkyStyle, và `app.blade.php` chưa khai báo `<link rel="icon">`.

## Goals / Non-Goals

**Goals:**
- Tạo bộ Favicon chuẩn từ biểu tượng HH Maker (node `2352:10265` trên Figma):
  - `public/favicon.ico` (16x16, 32x32, 48x48)
  - `public/favicon-16x16.png`
  - `public/favicon-32x32.png`
  - `public/apple-touch-icon.png` (180x180)
  - `public/assets/images/header/hh-maker-icon.svg`
- Bổ sung các thẻ `<link rel="icon">` vào `<head>` trong `resources/Frontend/views/app.blade.php`.
- Cập nhật `APP_NAME='HH Maker'` trong `.env.example`.
- Tuân thủ nghiêm ngặt yêu cầu của người dùng: **KHÔNG CHỈNH SỬA FILE `.env`**.

**Non-Goals:**
- Không sửa file `.env` cá nhân của môi trường local.
- Không thay đổi các cấu hình cơ sở dữ liệu hay backend logic.

## Decisions

### 1. Sinh Favicon từ biểu tượng chữ "H" của Figma Header
- Sử dụng script Python với thư viện `Pillow` để resize ảnh biểu tượng HH Maker thành các kích cỡ icon chuẩn: 16x16, 32x32, 48x48, 180x180 và đóng gói vào file `favicon.ico`.

### 2. Khai báo rõ ràng trong `app.blade.php`
- Đặt các thẻ link favicon trong `<head>`:
  ```html
  <link rel="icon" type="image/svg+xml" href="/assets/images/header/hh-maker-icon.svg">
  <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
  <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
  ```

## Risks / Trade-offs

- **[Risk]** Trình duyệt có thể lưu cache favicon cũ (`favicon.ico` thường được Chrome cache rất lâu).
  - **Mitigation**: Thêm `<link rel="icon" type="image/svg+xml" href="/assets/images/header/hh-maker-icon.svg?v=1">` để ép trình duyệt nhận diện ngay icon mới mà không bị dính cache.
