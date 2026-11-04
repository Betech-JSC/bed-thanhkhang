# Design: setup-products-multilingual-i18n

## 1. Tổng quan Kiến trúc

Giải pháp đa ngôn ngữ cho hệ thống Sản phẩm của HH Maker kết hợp 3 trụ cột vững chắc:
1. **Frontend Translation Layer (Vue I18n + Mixin `tt(...)`)**:
   - `tt(key)` là hàm dịch toàn cục đã được đăng ký trong `resources/Frontend/js/app.js`.
   - `tt(key)` tự động tra cứu từ `currentMsgs.ui[key]` được biên dịch từ `resources/lang/{vi,en}/ui.php` thông qua lệnh `php artisan vue-i18n:generate`.
   - Khi Inertia navigate sang URL mới (ví dụ `/en/products`), listener `Inertia.on('navigate')` lập tức cập nhật `i18n.global.locale` sang `en`, giúp toàn bộ component phản ứng đồng bộ và tức thì mà không cần reload trang.
2. **Dynamic Routing Layer (Ziggy localized routing)**:
   - Các route được định nghĩa bằng `Route::localized(...)` sinh ra các tên route có tiền tố locale: `vi.products.index`, `en.products.index`, `vi.products.show`, `en.products.show`.
   - Hàm mixin `route(name, params)` trong `app.js` tự động chuyển `route('products.show', { slug: ... })` thành `en.products.show` khi locale là `en`.
   - Nhờ đó, các thẻ sản phẩm và liên kết điều hướng luôn giữ đúng ngữ cảnh ngôn ngữ của người dùng.
3. **Backend & Database Model Layer (Astrotomic Translatable)**:
   - Model `Product` và `ProductCategory` sử dụng trait `Translatable` nối với bảng `product_translations` và `product_category_translations`.
   - Bổ sung bản ghi `locale = 'en'` cho các sản phẩm thật trong DB giúp các thuộc tính `title`, `description`, `content`, `slug` tự động lấy theo ngôn ngữ kích hoạt.

---

## 2. Thiết kế Cấu trúc Từ điển UI (`resources/lang/{vi,en}/ui.php`)

Từ điển được chuẩn hóa với key là nhãn Tiếng Việt nguyên bản (hoặc semantic key), value tương ứng là Tiếng Anh chuẩn chuyên ngành in 3D:

| Key (Tiếng Việt) | Tiếng Anh (`resources/lang/en/ui.php`) | Ngữ cảnh sử dụng |
|---|---|---|
| `BỘ SƯU TẬP HH MAKER` | `HH MAKER COLLECTION` | Eyebrow trang listing |
| `Ý TƯỞNG THÀNH HÌNH` | `BRINGING IDEAS TO LIFE` | Tiêu đề chính listing |
| `Tất cả sản phẩm` | `All Products` | Filter category tab |
| `Móc khoá` | `Keychains` | Category |
| `Văn phòng phẩm` | `Stationery` | Category |
| `Gia công theo yêu cầu` | `Custom Manufacturing` | Category |
| `DANH MỤC SẢN PHẨM` | `PRODUCT CATEGORIES` | Tiêu đề nhóm filter |
| `LỌC THEO NHU CẦU` | `FILTER BY PURPOSE` | Tiêu đề nhóm filter |
| `Trang trí không gian` | `Space Decor` | Purpose filter |
| `Đồ dùng hằng ngày` | `Daily Essentials` | Purpose filter |
| `Quà tặng cá nhân` | `Personal Gifts` | Purpose filter |
| `Tìm kiếm sản phẩm…` | `Search products…` | Search input placeholder |
| `Đặt lại bộ lọc` | `Reset filters` | Filter reset button |
| `Nổi bật nhất` | `Most Popular` | Sort option |
| `Mới nhất` | `Newest` | Sort option |
| `Tên A-Z` | `Name A-Z` | Sort option |
| `Tổng quan` | `Overview` | PDP Tab 1 |
| `Thông số in 3D` | `3D Printing Specs` | PDP Tab 2 |
| `Tuỳ chỉnh sản phẩm` | `Product Customization` | PDP Tab 3 |
| `Thông số in tham khảo` | `Reference Print Specifications` | SpecTable Title |
| `Công nghệ in` | `Print Technology` | SpecTable Row |
| `Vật liệu` | `Material` | SpecTable Row |
| `Kích thước mẫu` | `Model Dimensions` | SpecTable Row |
| `Chiều cao lớp in` | `Layer Height` | SpecTable Row |
| `Đầu phun` | `Nozzle Diameter` | SpecTable Row |
| `Mật độ ruột` | `Infill Density` | SpecTable Row |
| `Vật liệu hỗ trợ` | `Support Material` | SpecTable Row |
| `Không cần` | `None Required` | SpecTable Row value |
| `Vật liệu gợi ý` | `Suggested Material` | VariantSelector |
| `Màu trong ảnh` | `Sample Color` | VariantSelector |
| `Có thể tuỳ chỉnh` | `Customization Options` | VariantSelector |
| `Nhận tư vấn sản phẩm` | `Request Product Consultation` | PDP Main CTA |
| `Sản phẩm khác` | `Related Products` | PDP Related Section |
| `Xem tất cả` | `View All` | Related Products link |

---

## 3. Thiết kế Song ngữ Hóa 16 mockProducts

Trong `Product/Index.vue`, 16 sản phẩm mẫu được trang bị song song dữ liệu Tiếng Việt và Tiếng Anh. Dựa vào `currentLocale` (lấy từ `$page.props.locale.current` hoặc `$i18n.locale`):
- `name` / `title`: Tiếng Anh chuyên nghiệp.
- `category`: `Custom Manufacturing`, `Stationery`, `Keychains`.
- `purpose`: `Space Decor`, `Daily Essentials`, `Personal Gifts`.
- `description`: Diễn giải kỹ thuật in FDM/Resin chuẩn xác bằng tiếng Anh.

---

## 4. Thiết kế Cập nhật Dữ liệu Database an toàn

Không dùng drop/truncate/seed đè toàn bộ DB. Sử dụng script Eloquent / Query Builder an toàn với `updateOrInsert`:
- Bảng `product_category_translations`: chèn bản ghi `product_category_id = 1, locale = 'en'` với `title = 'Space Decor', slug = 'space-decor'`.
- Bảng `product_translations`: chèn bản ghi `product_id = 1, locale = 'en'` với `title = 'Decorative Fox Model', slug = 'decorative-fox-model'`.

---

## 5. Chiến lược Kiểm thử Playwright E2E

Viết kịch bản kiểm thử tự động `tests/e2e/products-multilingual.spec.ts`:
1. **Test Listing**:
   - Truy cập `/san-pham`, kiểm tra các phần tử hiển thị tiếng Việt.
   - Bấm nút chuyển đổi `ENG` trên Header (hoặc truy cập trực tiếp `/en/products`).
   - Kiểm tra URL chuyển thành `/en/products`.
   - Kiểm tra tiêu đề "BRINGING IDEAS TO LIFE", toolbar "Most Popular", bộ lọc "PRODUCT CATEGORIES", "FILTER BY PURPOSE", thẻ sản phẩm hiển thị tiếng Anh.
   - Chụp ảnh `tests/e2e/screenshots/products-listing-vie.png` và `tests/e2e/screenshots/products-listing-eng.png`.
2. **Test PDP**:
   - Truy cập `/en/products/mo-hinh-cao-trang-tri` hoặc `/en/products/decorative-fox-model`.
   - Kiểm tra 3 tabs hiển thị: "Overview", "3D Printing Specs", "Product Customization".
   - Kiểm tra bảng thông số in hiển thị: "Reference Print Specifications", "Print Technology", "Material", v.v.
   - Kiểm tra nút CTA "Request Product Consultation".
   - Chụp ảnh `tests/e2e/screenshots/products-detail-eng.png`.
