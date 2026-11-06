# admin-products Specification

## Purpose
Cung cấp khả năng hiển thị biểu mẫu nhập liệu (form), cấu trúc schema, luật kiểm thực (validation rules) và xử lý lưu trữ/cập nhật sản phẩm ổn định, không lỗi 500 trong phân hệ quản trị BED CMS.

## Requirements

### Requirement: Hiển thị form thêm mới và chỉnh sửa sản phẩm admin không phát sinh lỗi
Hệ thống SHALL trả về HTTP 200 và dữ liệu schema form quản trị sản phẩm khi quản trị viên đã đăng nhập truy cập route `admin/products/form` (tạo mới) hoặc `admin/products/form/{id}` (chỉnh sửa), không bị gián đoạn bởi các trường casting ảo hoặc lỗi thiếu class quan hệ.

#### Scenario: Quản trị viên mở form tạo mới sản phẩm
- **WHEN** Quản trị viên đã xác thực truy cập route `GET /admin/products/form`
- **THEN** Hệ thống phản hồi mã trạng thái HTTP 200 và nạp giao diện form cùng dữ liệu schema khởi tạo rỗng hợp lệ.

#### Scenario: Quản trị viên mở form chỉnh sửa sản phẩm hiện hữu
- **WHEN** Quản trị viên đã xác thực truy cập route `GET /admin/products/form/{id}` với ID sản phẩm hợp lệ
- **THEN** Hệ thống phản hồi mã trạng thái HTTP 200 và nạp đầy đủ thông tin sản phẩm cùng các quan hệ categories, brand, translations mà không gây lỗi Class not found.

### Requirement: Xử lý lưu trữ và kiểm thực dữ liệu sản phẩm chuẩn hóa
Hệ thống SHALL kiểm thực hợp lệ dữ liệu gửi lên endpoint `POST /admin/products/store` hoặc `POST /admin/products/store/{id}` theo bộ quy tắc `rules()['store']`, xử lý lưu trữ thành công và chuyển hướng (redirect) hoặc phản hồi JSON hợp lệ.

#### Scenario: Quản trị viên gửi form tạo sản phẩm hợp lệ
- **WHEN** Quản trị viên gửi yêu cầu `POST /admin/products/store` với dữ liệu hợp lệ (tối thiểu trường `title`)
- **THEN** Hệ thống lưu trữ sản phẩm mới vào cơ sở dữ liệu và trả về mã trạng thái HTTP 302 chuyển hướng về form sản phẩm kèm thông báo thành công (hoặc HTTP 200 đối với JSON request).

#### Scenario: Khách chưa đăng nhập bị từ chối truy cập
- **WHEN** Người dùng chưa đăng nhập hoặc không có quyền admin truy cập `GET /admin/products/form` hoặc `POST /admin/products/store`
- **THEN** Hệ thống từ chối truy cập và chuyển hướng về trang đăng nhập với mã trạng thái HTTP 302.

### Requirement: Bộ kiểm thử hồi quy Backend tự động
Hệ thống SHALL có test suite kiểm thử tính năng Backend (`tests/Feature/BackendProductTest.php`) kiểm chứng các endpoint quản trị sản phẩm chạy thành công trong môi trường kiểm thử tự động.

#### Scenario: Chạy test kiểm tra toàn bộ luồng admin products
- **WHEN** Thực thi lệnh `php artisan test --filter=BackendProductTest`
- **THEN** Toàn bộ các test case liên quan đến form và store sản phẩm đều vượt qua (PASS) với 0 lỗi phát sinh.
