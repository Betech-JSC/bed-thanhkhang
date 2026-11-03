# Tasks: Khắc phục điều hướng và định tuyến (Navigation & Routing)

## 1. Backend Controller Implementation

- [x] 1.1 Triển khai phương thức `index()` trong `app/Http/Controllers/Frontend/ProductController.php`: truy vấn `Product::query()->active()->ordered()->get()->map(fn($item) => $item->transform())`, cache 3600 giây theo locale, trả về `Inertia::render('Product/Index', $data)` với view data SEO; xác minh bằng `php artisan route:list --name=products.index` và test phản hồi JSON/Inertia.

## 2. Frontend Navigation & Components Update

- [x] 2.1 Cập nhật liên kết Breadcrumb trên trang chi tiết sản phẩm (`resources/Frontend/js/Pages/Product/Show.vue`): sửa liên kết "Sản phẩm" từ `/products` (404) thành `/san-pham`; xác minh template không còn liên kết `/products` gán cứng.
- [x] 2.2 Bọc thẻ `<Link>` quanh ảnh thumbnail trong `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` trỏ tới `product.url || '/san-pham/' + (product.slug || product.id)`; xác minh vùng ảnh có con trỏ pointer và kích hoạt chuyển hướng sang PDP khi click.
- [x] 2.3 Chuẩn hóa mảng menu trong `resources/Frontend/js/Components/Header.vue`: đổi nhãn `'Về funky'` thành `'Giới thiệu'`, đổi menu `'Sản phẩm'` sang `this.route('products.index')` (`type: 'products'`), bỏ comment và kích hoạt lại menu `'Dịch vụ'` (`slug: this.route('services.index')`, `type: 'services'`); xác minh nhãn và liên kết hiển thị chuẩn xác trên cả Desktop lẫn Mobile.

## 3. Build Verification & AST Graph Update

- [x] 3.1 Chạy lệnh đồng bộ Ziggy routes `yarn route` và biên dịch tài nguyên frontend `yarn frontend:build`; xác minh terminal trả về exit code 0 không có cảnh báo nghiêm trọng hay lỗi đóng gói.
- [x] 3.2 Cập nhật bản đồ đồ thị kiến trúc mã nguồn qua lệnh `graphify . --code-only` theo quy định Graphify AST First của dự án.

## 4. Visual Verification & End-to-End QA

- [x] 4.1 Kiểm chứng trực quan qua Playwright MCP: truy cập trực tiếp route `/san-pham` kiểm tra HTTP 200 hiển thị danh mục sản phẩm, nhấp breadcrumb trên PDP kiểm tra không lỗi 404, nhấp thumbnail sản phẩm trên ProductCard kiểm tra chuyển trang thành công, và chụp ảnh đối chiếu Header trên cả Desktop và Mobile viewport.
