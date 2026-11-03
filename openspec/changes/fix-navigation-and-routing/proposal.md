# Proposal: Khắc phục khiếm khuyết điều hướng và định tuyến (Navigation & Routing)

## Why

Trong đợt nghiệm thu và kiểm thử giao diện cuối kỳ (Final QA), hệ thống đã phát hiện 4 khiếm khuyết điều hướng và định tuyến gây ảnh hưởng nghiêm trọng đến trải nghiệm duyệt sản phẩm của người dùng và tính toàn vẹn SEO:
1. Breadcrumb trên trang chi tiết sản phẩm (PDP) chứa liên kết gán cứng `/products` dẫn đến lỗi 404 Not Found (đường dẫn chuẩn của hệ thống là `/san-pham`).
2. Khối ảnh thumbnail trên thẻ sản phẩm (`ProductCard.vue`) chưa được bọc thẻ liên kết `<Link>`, buộc người dùng phải click chính xác vào tiêu đề chữ hoặc nút "Chi tiết", làm giảm đáng kể khả năng tương tác trên thiết bị di động và desktop.
3. Thanh điều hướng chính (`Header.vue`) vẫn sót tàn dư nhãn thương hiệu cũ ("Về funky"), mục menu "Sản phẩm" đang trỏ nhầm sang trang thư viện (`galleries.index`) thay vì danh mục sản phẩm thương mại (`products.index` - `/san-pham`), và menu "Dịch vụ" (`services.index`) đang bị tạm ẩn (comment out).
4. Hàm xử lý danh mục sản phẩm `ProductController@index` đang bị bỏ trống (`{}`), khiến route `/san-pham` trả về phản hồi rỗng thay vì kết xuất trang danh sách sản phẩm (`Product/Index`).

Việc giải quyết dứt điểm 4 vấn đề này là điều kiện tiên quyết để đảm bảo luồng trải nghiệm khách hàng liền mạch từ Trang chủ -> Danh mục sản phẩm/Dịch vụ -> Chi tiết sản phẩm -> Báo giá in 3D.

## What Changes

- **Sửa PDP Breadcrumb**: Cập nhật liên kết mục "Sản phẩm" trong breadcrumb tại `resources/Frontend/js/Pages/Product/Show.vue` từ `/products` sang `/san-pham` (hoặc `route('products.index')`).
- **Bọc liên kết ảnh Thumbnail ProductCard**: Bao bọc vùng ảnh đại diện sản phẩm trong `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` bằng thẻ `<Link :href="product.url || '/san-pham/' + (product.slug || product.id)">`, duy trì hiệu ứng hover mượt mà và đảm bảo người dùng click ảnh chuyển thẳng đến trang chi tiết.
- **Chuẩn hóa Header Navigation**: Cập nhật cấu hình mảng menu trong `resources/Frontend/js/Components/Header.vue`:
  - Đổi nhãn `"Về funky"` thành `"Giới thiệu"`.
  - Đổi đích đến của menu `"Sản phẩm"` từ `galleries.index` sang `products.index` (`/san-pham`) với `type: 'products'`.
  - Mở lại mục menu `"Dịch vụ"` (`services.index`, `type: 'services'`) để hiển thị đầy đủ năng lực in 3D theo yêu cầu.
- **Hoàn thiện Controller Danh mục Sản phẩm**: Bổ sung logic xử lý cho method `index()` trong `app/Http/Controllers/Frontend/ProductController.php`:
  - Truy vấn danh sách sản phẩm kích hoạt (`Product::query()->active()->ordered()->get()`), chuyển đổi qua `transform()`.
  - Áp dụng bộ nhớ đệm `Cache::remember` theo locale tương tự các action khác trong controller.
  - Kết xuất giao diện Inertia `Product/Index` với đầy đủ dữ liệu `$data` (`products`, `seo`) và `withViewData(['seo' => $data['seo']])`.

## Capabilities

### New Capabilities
- `navigation`: Chuẩn hóa luồng điều hướng danh mục, menu header, breadcrumbs và liên kết thẻ sản phẩm trên toàn bộ ứng dụng frontend HH Maker.

### Modified Capabilities
*(Không có thay đổi requirement đối với các spec hiện có)*

## Impact

- **Mã nguồn bị tác động**:
  - `resources/Frontend/js/Pages/Product/Show.vue` (PDP breadcrumbs)
  - `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` (Card thumbnail link)
  - `resources/Frontend/js/Components/Header.vue` (Desktop & Mobile navigation menus)
  - `app/Http/Controllers/Frontend/ProductController.php` (`index()` method)
- **APIs & Database**:
  - Không thay đổi cấu trúc cơ sở dữ liệu (Database Schema giữ nguyên 100%).
  - Tái sử dụng scope `active()`, `ordered()` và hàm `transform()` sẵn có của Model `Product`.
- **Breaking Changes**: Không có breaking change. Tương thích hoàn toàn với hệ thống route hiện hành của Laravel và Ziggy.
