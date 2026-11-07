# Design: Khắc phục lỗi HTTP 500 trên route admin/products/form

## Context
Khi quản trị viên truy cập màn hình thêm mới hoặc chỉnh sửa sản phẩm tại route `GET /admin/products/form` hoặc nộp dữ liệu tại `POST /admin/products/store`, ứng dụng gặp lỗi HTTP 500.

Phân tích mã nguồn và dấu vết lỗi:
1. **Lỗi `RuleGenerator` do casting thuộc tính ảo**:
   - `ProductController` sử dụng trait `HasCrudActions`. Trong phương thức `form()`, controller gọi `$this->getSchema()`, kích hoạt `RuleGenerator::getRules($table)`.
   - Trong `packages/bed-package-essentials/rule-generator/src/Services/Generator.php` (dòng 46–52):
     ```php
     $instance = $this->getModelInstance($table);
     foreach ($instance->getCasts() as $column => $type) {
         if ($type === 'array') {
             $uniqueRules[$column] = str_replace('json', 'array', $uniqueRules[$column]);
             $uniqueRules[$column] = str_replace('text', 'array', $uniqueRules[$column]);
         }
     }
     ```
   - Trong `app/Models/Product/Product.php`, biến `$casts` khai báo:
     ```php
     protected $casts = [
         'images' => 'array',
         'image' => 'array',
         'filter_attributes' => 'array',
         'pdp_metadata' => 'array',
         'specifications' => 'array',
     ];
     ```
   - Các trường `filter_attributes`, `pdp_metadata`, `specifications` không tồn tại dưới dạng cột trong bảng `products` của cơ sở dữ liệu. Do đó, `$uniqueRules` không chứa các key này. Trên môi trường PHP 8.1+, biểu thức `$uniqueRules[$column]` lập tức ném ra ngoại lệ `ErrorException: Undefined array key "filter_attributes"`, dẫn tới lỗi HTTP 500 toàn hệ thống.
2. **Lỗi `Class "App\Models\Option" not found`**:
   - Trong `app/Models/Product/ProductCategory.php` (dòng 10) và `app/Models/Product/Product.php` (dòng 12) tồn tại lệnh import:
     ```php
     use App\Models\Option;
     ```
   - Tuy nhiên, class `Option` thực tế nằm trong package Essentials tại namespace `JamstackVietnam\Option\Models\Option`. Khi nạp danh mục hoặc quan hệ options trong form, việc gọi sai namespace dẫn tới fatal error `Class "App\Models\Option" not found`.
3. **Chưa chuẩn hóa `rules()['store']`**:
   - Trong `HasCrudActions::getModelRules($action, $id = null)`:
     ```php
     if (method_exists($this->model, 'rules')) {
         $allRules = $this->model()->rules();
         if (isset($this->model()->rules()[$action])) {
             $rules = $allRules[$action];
         }
     }
     return $rules ?? $this->getRules($id);
     ```
   - `Product.php` trả về mảng phẳng không có key `'store'`, khiến hàm kiểm thực rơi về fallback DB column rules thay vì luật nghiệp vụ sản phẩm, đồng thời tồn tại khai báo trùng key `'seo_slug'`.

## Goals / Non-Goals

**Goals:**
- Loại bỏ hoàn toàn lỗi HTTP 500 khi quản trị viên truy cập `GET /admin/products/form` (tạo mới) và `GET /admin/products/form/{id}` (chỉnh sửa).
- Đảm bảo endpoint `POST /admin/products/store` xử lý lưu trữ/cập nhật sản phẩm thành công, trả về HTTP 302 redirect (hoặc HTTP 200 JSON khi request bằng AJAX/Inertia).
- Chuẩn hóa namespace import `Option` về `JamstackVietnam\Option\Models\Option` trên các model liên quan.
- Thiết lập bộ kiểm thử tính năng Backend (`BackendProductTest.php`) kiểm chứng 100% tự động các luồng này.

**Non-Goals:**
- Không sửa đổi schema bảng `products`, không chạy migration phá hủy dữ liệu, không drop/truncate table (tuân thủ Strict Safe Guard).
- Không can thiệp vào giao diện Vue 3 của trang admin (`resources/Frontend/js/...` hoặc views admin) vì giao diện Vue đã sẵn sàng.
- Không thay đổi luồng frontend khách hàng (`/san-pham`, `/en/products`).

## Decisions

### Quyết định 1: Dọn dẹp mảng `$casts` trong `Product.php`
- **Lựa chọn**: Chỉ giữ lại các trường có cột thật trong bảng `products` là `images` và `image`:
  ```php
  protected $casts = [
      'images' => 'array',
      'image' => 'array',
  ];
  ```
- **Lý do**: `RuleGenerator` quét qua toàn bộ `$casts` và đối chiếu với danh sách cột thực tế của bảng. Việc loại bỏ các trường ảo triệt tiêu hoàn toàn `ErrorException: Undefined array key`.
- **Phương án thay thế đã cân nhắc**: Thêm cột `filter_attributes`, `pdp_metadata`, `specifications` vào database bằng migration mới. *Bác bỏ vì dự án đang trong pha ổn định CMS, thêm cột thừa không cần thiết và tiềm ẩn rủi ro phá vỡ schema hiện hữu.*

### Quyết định 2: Sửa đổi Namespace Import `Option`
- **Lựa chọn**: Thay thế `use App\Models\Option;` bằng `use JamstackVietnam\Option\Models\Option;` trong cả `ProductCategory.php` và `Product.php`.
- **Lý do**: Đây là vị trí chính xác của model `Option` trong `packages/bed-package-essentials/option/src/Models/Option.php` (tương tự như cách `ProductVariant.php` đang sử dụng).
- **Phương án thay thế đã cân nhắc**: Tạo alias class `class Option extends \JamstackVietnam\Option\Models\Option {}` tại `app/Models/Option.php`. *Bác bỏ vì tạo thêm file trung gian dư thừa, việc sửa trực tiếp namespace import clean và minh bạch hơn.*

### Quyết định 3: Chuẩn hóa phương thức `rules()` trong `Product.php`
- **Lựa chọn**: Cấu trúc mảng trả về có key `'store'` và sửa lỗi trùng lặp `'seo_slug'`:
  ```php
  public function rules(): array
  {
      return [
          'store' => [
              'title' => 'required|string|max:255',
              'slug' => 'nullable|string|max:255',
              'seo_slug' => 'nullable|string|max:255',
              'sku' => 'nullable|string|max:255',
              'price' => 'nullable|numeric|min:0',
              'compare_at_price' => 'nullable|numeric|min:0',
              'status' => 'nullable|string|in:ACTIVE,INACTIVE',
          ],
      ];
  }
  ```
- **Lý do**: Tương thích hoàn hảo với cơ chế `getModelRules('store')` của `HasCrudActions`.

### Quyết định 4: Thiết kế Backend Regression Test `BackendProductTest.php`
- **Lựa chọn**: Tạo file test mới tại `tests/Feature/BackendProductTest.php` kế thừa `Tests\TestCase` với các kịch bản:
  1. `test_admin_can_access_product_create_form`: Đăng nhập bằng tài khoản admin (`actingAs($user, 'admin')`), gửi `GET /admin/products/form`, kiểm tra status HTTP 200.
  2. `test_admin_can_access_product_edit_form`: Lấy 1 sản phẩm mẫu từ DB, gửi `GET /admin/products/form/{id}`, kiểm tra status HTTP 200.
  3. `test_admin_can_store_new_product`: Gửi payload hợp lệ đến `POST /admin/products/store`, kiểm tra HTTP 302 hoặc 200 và dữ liệu được ghi vào DB.
  4. `test_guest_cannot_access_product_form`: Không đăng nhập, gửi `GET /admin/products/form`, kiểm tra HTTP 302 chuyển hướng về trang login.
- **Lý do**: Cung cấp bằng chứng thực thi 100% bằng lệnh `php artisan test` mà không suy đoán cảm tính.

## Risks / Trade-offs

- **[Risk]** Người dùng nộp form sản phẩm có chứa dữ liệu `specifications` hoặc `pdp_metadata` mà frontend gửi lên:
  - **Mitigation**: Các thuộc tính này nếu không có cột trong DB sẽ bị Eloquent tự động bỏ qua khi gọi `create($request->all())` nhờ cơ chế `$fillable` bảo vệ. Khi cần lưu trữ thông số in 3D, hệ thống đã có sẵn các cột chuẩn: `print_technology`, `material`, `lead_time`.
- **[Risk]** Test chạy trong môi trường sqlite vs mysql:
  - **Mitigation**: Trong test suite, sử dụng dữ liệu mẫu từ database hiện tại hoặc dùng `Product::first()` nếu có sẵn data, hoặc tạo bản ghi an toàn trong transaction để không ảnh hưởng dữ liệu thật.

## Migration Plan
- Bước 1: Áp dụng sửa đổi cho `Product.php` và `ProductCategory.php`.
- Bước 2: Tạo test file `tests/Feature/BackendProductTest.php`.
- Bước 3: Chạy test kiểm chứng và xác nhận HTTP 200 trên endpoint `admin/products/form`.
- Rollback: Do chỉ chỉnh sửa model logic và thêm test file, rollback hoàn toàn bằng `git checkout` nếu có bất kỳ vấn đề bất thường.
