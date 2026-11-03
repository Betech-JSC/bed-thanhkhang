# Technical Design: Khắc phục điều hướng và định tuyến (Navigation & Routing)

## Context

Hệ thống HH Maker vận hành trên nền tảng Laravel kết hợp Inertia.js (Vue 3) và Ziggy routing. Hiện tại:
- Route `products.index` đã được khai báo tại `routes/frontend.php` với URI cục bộ hóa `/san-pham` (thông qua `Lang::uri('products')`).
- File `ProductController.php` đã có phương thức `index()`, tuy nhiên phần thân hàm đang trống (`{}`), chưa kết xuất giao diện Inertia.
- Giao diện danh mục sản phẩm hoàn chỉnh đã được xây dựng sẵn tại `resources/Frontend/js/Pages/Product/Index.vue` (có kèm wrapper `resources/Frontend/js/Pages/Products/Index.vue`).
- Trang chi tiết sản phẩm (`Product/Show.vue`) vẫn giữ breadcrumb gán cứng `/products` (trả về 404).
- Component `ProductCard.vue` chỉ gắn `<Link>` ở tiêu đề và nút "Chi tiết", vùng ảnh thumbnail 240px chưa có thẻ link bọc ngoài.
- Thanh Header (`Header.vue`) chứa nhãn tàn dư "Về funky", menu "Sản phẩm" dẫn về `galleries.index`, và menu "Dịch vụ" bị comment out.

## Goals / Non-Goals

**Goals:**
- Đảm bảo 100% các điểm tiếp xúc danh mục sản phẩm (Header menu, Breadcrumb trên PDP, Thumbnail trên ProductCard) đều dẫn chính xác về trang danh mục hoặc trang chi tiết tương ứng mà không gặp lỗi 404.
- Kết xuất hoàn chỉnh trang PLP (`Product/Index`) từ Controller với danh sách sản phẩm kích hoạt, hỗ trợ bộ nhớ đệm và SEO metadata.
- Chuẩn hóa thông điệp thương hiệu trên Header ("Giới thiệu", kích hoạt menu "Dịch vụ").
- Giữ nguyên hiệu ứng UI/UX, không làm xô lệch bố cục CSS Tailwind hiện có.

**Non-Goals:**
- Không sửa đổi cấu trúc bảng cơ sở dữ liệu hoặc can thiệp migration.
- Không thay đổi cấu trúc URL hiện hành tại `routes/frontend.php`.
- Không can thiệp vào các component nội bộ của trang sản phẩm như `ProductFilter.vue`, `ProductPagination.vue` hay modal báo giá.

## Decisions

### Decision 1: Hoàn thiện `ProductController@index` đồng bộ chuẩn kiến trúc controller
- **Lựa chọn**: Triển khai `ProductController@index` tương tự `galleries()` và `show()`:
  - Sử dụng `Cache::remember('products_index_' . current_locale(), 3600, ...)` để tối ưu hóa hiệu năng truy vấn.
  - Lấy danh sách sản phẩm đang hoạt động: `Product::query()->active()->ordered()->get()->map(fn($item) => $item->transform())`.
  - Kết xuất `Inertia::render('Product/Index', $data)->withViewData(['seo' => $data['seo']])`.
- **Lý do**: Component `resources/Frontend/js/Pages/Product/Index.vue` nhận prop `products: { type: Array, default: () => [] }`. Khi danh sách rỗng, component đã có sẵn fallback `mockProducts` an toàn; khi có sản phẩm trong DB, dữ liệu sẽ được hiển thị mượt mà.
- **Phương án thay thế cân nhắc**: Render `Products/Index` (wrapper). Tuy nhiên, render trực tiếp `Product/Index` ngắn gọn, minh bạch và khớp trực tiếp với chỉ đạo kỹ thuật.

### Decision 2: Bọc thẻ `<Link>` quanh ảnh thumbnail của `ProductCard.vue`
- **Lựa chọn**: Bọc thẻ `<Link :href="product.url || '/san-pham/' + (product.slug || product.id)" class="block w-full h-full">` trực tiếp quanh phần tử ảnh hoặc container ảnh (Tier 1 Image Container), giữ nguyên các badge thông số và overlay hiệu ứng hover bên trong.
- **Lý do**: Tăng diện tích tương tác (hit target) cho người dùng mà không tạo ra thẻ link lồng nhau (nested anchor tags), tuân thủ tiêu chuẩn HTML5 và Accessibility (a11y).
- **Phương án thay thế cân nhắc**: Bọc thẻ `<Link>` quanh toàn bộ card. Bị loại vì bên dưới card có nút bấm "Chi tiết" và các action phụ, nếu bọc toàn card sẽ dẫn đến lỗi thẻ `<a>` lồng `<a>`.

### Decision 3: Chuẩn hóa mảng cấu hình `menus` trong `Header.vue`
- **Lựa chọn**:
  - `title: this.tt('Giới thiệu')`: trỏ tới `histories.index` (URL `/ve-chung-toi`).
  - `title: this.tt('Sản phẩm')`: đổi slug sang `this.route('products.index')`, type: `'products'`.
  - Mở lại menu `title: this.tt('Dịch vụ')`: slug `this.route('services.index')`, type: `'services'`.
- **Lý do**: Khớp hoàn toàn với hệ thống route frontend hiện hành tại `routes/frontend.php` và xóa bỏ triệt để tàn dư "funky".

## Risks / Trade-offs

- **[Risk: Thẻ `Link` thumbnail làm mất hiệu ứng hover zoom]** → **Mitigation**: Giữ nguyên class `group-hover:scale-105 transition-transform duration-500 ease-out` trên thẻ `<img>` và đảm bảo `<Link>` có class `block overflow-hidden`.
- **[Risk: Cache của Laravel lưu dữ liệu cũ]** → **Mitigation**: Chạy lệnh `php artisan cache:clear` sau khi cập nhật controller.
- **[Risk: Build frontend lỗi do import sai]** → **Mitigation**: Kiểm chứng bằng `yarn frontend:build` sau khi chỉnh sửa.
