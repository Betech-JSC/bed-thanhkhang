# Proposal: Khắc phục lỗi HTTP 500 trên route admin/products/form

## Why
Khi quản trị viên truy cập màn hình tạo mới hoặc cập nhật sản phẩm tại route `admin/products/form` (cũng như `admin/en/products/form`), hệ thống gặp lỗi HTTP 500 làm gián đoạn hoàn toàn luồng nhập liệu sản phẩm trên CMS. 

Nguyên nhân gốc rễ (Root Cause):
1. **Casting ảo trong Eloquent Model**: Trong `app/Models/Product/Product.php`, thuộc tính `$casts` đang ép kiểu cho các trường `filter_attributes`, `pdp_metadata`, `specifications` vốn không tồn tại trong cấu trúc bảng `products` của cơ sở dữ liệu. Khi trait `HasCrudActions` của backend nạp schema qua `getEmptyFields()`, các thuộc tính ảo này gây xung đột và lỗi thực thi.
2. **Sai namespace của class `Option`**: Trong `app/Models/Product/ProductCategory.php` (và `Product.php`), namespace đang gọi `use App\Models\Option;` - một class không tồn tại trong thư mục `app/Models/`. Class chính xác được cung cấp từ package `packages/bed-package-essentials/option` là `JamstackVietnam\Option\Models\Option`. Lỗi này kích hoạt `Class "App\Models\Option" not found` khi khởi tạo quan hệ options/danh mục trong form sản phẩm.
3. **Quy tắc kiểm thực (Validation rules) chưa chuẩn hóa**: Phương thức `rules()` trong `app/Models/Product/Product.php` chưa được cấu trúc theo key action `store` (mà `HasCrudActions::getModelRules('store')` tìm kiếm), đồng thời bị trùng lặp định nghĩa rule `seo_slug`.

Cần xử lý dứt điểm các lỗi trên và thiết lập bộ kiểm thử tự động (regression test suite) để bảo vệ luồng quản trị sản phẩm.

## What Changes
- **Chỉnh sửa `app/Models/Product/Product.php`**:
  - Dọn dẹp mảng `$casts`: Loại bỏ các thuộc tính không có cột trong database (`filter_attributes`, `pdp_metadata`, `specifications`), chỉ giữ lại các cột JSON/mảng thực tế (`images`, `image`).
  - Chuẩn hóa phương thức `rules()`: Cung cấp mảng có key `'store'` tương thích với `HasCrudActions`, sửa trùng lặp trường `seo_slug`.
  - Cập nhật import `Option`: Đảm bảo sử dụng `JamstackVietnam\Option\Models\Option;` thay vì `App\Models\Option;`.
- **Chỉnh sửa `app/Models/Product/ProductCategory.php`**:
  - Sửa dòng `use App\Models\Option;` thành `use JamstackVietnam\Option\Models\Option;` để giải quyết triệt để lỗi thiếu class khi quan hệ options được gọi.
- **Bổ sung Backend Regression Test Suite `tests/Feature/BackendProductTest.php`**:
  - Viết các test case kiểm tra:
    + Quản trị viên truy cập GET `/admin/products/form` trả về HTTP 200 (hoặc JSON hợp lệ khi có `Accept: application/json`).
    + Quản trị viên truy cập GET `/admin/products/form/{id}` với sản phẩm đã có trả về HTTP 200.
    + Quản trị viên thực hiện POST `/admin/products/store` lưu dữ liệu sản phẩm thành công (HTTP 200 hoặc HTTP 302 redirect).
    + Khách vãng lai chưa đăng nhập bị từ chối truy cập (HTTP 302/401).

## Capabilities

### New Capabilities
- `admin-products`: Quản lý biểu mẫu (form), cấu trúc schema, luật kiểm thực (validation rules) và xử lý lưu trữ/truy vấn sản phẩm trong phân hệ quản trị BED CMS.

### Modified Capabilities
<!-- Không có thay đổi requirement nào đối với các capability hiện có (product-catalog giữ nguyên frontend) -->

## Impact
- **Mã nguồn bị ảnh hưởng**:
  - `app/Models/Product/Product.php`
  - `app/Models/Product/ProductCategory.php`
- **Bộ kiểm thử**:
  - `tests/Feature/BackendProductTest.php` (tạo mới)
- **Cơ sở dữ liệu**:
  - Tuyệt đối KHÔNG thay đổi schema, KHÔNG chạy migration phá hủy, dữ liệu hiện tại an toàn 100%.
- **Giao diện & API**:
  - Khôi phục route quản trị sản phẩm `admin/products/form` và `admin/products/store` hoạt động trơn tru.
