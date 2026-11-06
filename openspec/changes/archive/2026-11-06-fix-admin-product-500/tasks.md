# Tasks: Khắc phục lỗi HTTP 500 trên route admin/products/form

## 1. Sửa đổi Eloquent Models

- [x] 1.1 Cập nhật `app/Models/Product/Product.php`: Dọn dẹp mảng `$casts`, loại bỏ các trường ảo không có trong database (`filter_attributes`, `pdp_metadata`, `specifications`), chỉ giữ lại các cột JSON/mảng thực tế (`images`, `image`). Kiểm chứng không có lỗi cú pháp bằng `php -l app/Models/Product/Product.php`.
- [x] 1.2 Cập nhật `app/Models/Product/Product.php`: Chuẩn hóa phương thức `rules()` sang cấu trúc key `'store'` tương thích với `HasCrudActions::getModelRules('store')` và loại bỏ định nghĩa trùng lặp `'seo_slug'`. Kiểm chứng không có lỗi cú pháp bằng `php -l app/Models/Product/Product.php`.
- [x] 1.3 Cập nhật `app/Models/Product/ProductCategory.php` và `app/Models/Product/Product.php`: Sửa namespace import từ `use App\Models\Option;` thành `use JamstackVietnam\Option\Models\Option;`. Kiểm chứng cú pháp bằng `php -l app/Models/Product/ProductCategory.php` và `php -l app/Models/Product/Product.php`.

## 2. Bổ sung Backend Regression Test Suite

- [x] 2.1 Tạo file test `tests/Feature/BackendProductTest.php` bao gồm các kịch bản kiểm thử:
  - Quản trị viên đã xác thực gửi `GET /admin/products/form` trả về HTTP 200.
  - Quản trị viên đã xác thực gửi `GET /admin/products/form/{id}` cho sản phẩm hiện hữu trả về HTTP 200.
  - Quản trị viên gửi `POST /admin/products/store` lưu dữ liệu sản phẩm thành công trả về HTTP 302 hoặc HTTP 200.
  - Khách vãng lai chưa đăng nhập truy cập form bị chuyển hướng (HTTP 302).
- [x] 2.2 Thực thi bộ kiểm thử bằng lệnh `php artisan test --filter=BackendProductTest` và xác nhận tất cả test case đều đạt kết quả PASS (100% xanh lá).

## 3. Nghiệm thu Toàn diện & Xác thực Luồng Admin

- [x] 3.1 Xác thực trực tiếp endpoint `GET /admin/products/form` và `GET /admin/en/products/form` trong phiên đăng nhập quản trị viên, đảm bảo trả về HTTP 200 và schema form đầy đủ, không còn mã lỗi 500.
- [x] 3.2 Chạy toàn bộ test suite dự án bằng lệnh `php artisan test` để đảm bảo hệ thống an toàn tuyệt đối và không phát sinh bất kỳ lỗi hồi quy nào.
