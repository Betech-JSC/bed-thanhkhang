# Spec Delta: layout-shell

## ADDED Requirements

### Requirement: Bộ nhận diện Favicon và Thẻ Icon Layout Shell
Hệ thống SHALL cung cấp bộ tài nguyên Favicon đồng bộ theo nhận diện thương hiệu HH Maker trong thư mục `public/` (bao gồm `favicon.ico`, `favicon-16x16.png`, `favicon-32x32.png`, `apple-touch-icon.png` và `hh-maker-icon.svg`), đồng thời khai báo đầy đủ các thẻ `<link rel="icon">` và `<link rel="apple-touch-icon">` trong thẻ `<head>` của `resources/Frontend/views/app.blade.php`.

#### Scenario: Trình duyệt tải Favicon chuẩn HH Maker
- **WHEN** người dùng truy cập bất kỳ trang nào của website HH Maker trên trình duyệt
- **THEN** tab trình duyệt hiển thị biểu tượng chữ H công nghệ của thương hiệu HH Maker thay vì biểu tượng cũ của FunkyStyle.

#### Scenario: Thẻ meta link icon trong mã HTML
- **WHEN** kiểm tra mã nguồn HTML của trang web
- **THEN** thẻ `<head>` chứa các thẻ `<link rel="icon">` liên kết đến các tệp icon chuẩn của HH Maker.
