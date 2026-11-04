# Spec Delta: product-catalog

## Purpose
Đặc tả chi tiết các yêu cầu chức năng và giao diện đa ngôn ngữ (Tiếng Việt và Tiếng Anh) cho Hệ thống Danh mục Sản phẩm (Listing) và Chi tiết Sản phẩm (PDP) của nền tảng in 3D HH Maker.

## ADDED Requirements

### Requirement: Từ điển UI chuyên ngành in 3D và linh kiện cơ khí
Hệ thống từ điển giao diện `resources/lang/{vi,en}/ui.php` SHALL cung cấp đầy đủ các cặp khóa - giá trị song ngữ (Tiếng Việt và Tiếng Anh) cho:
1. Danh mục & Nhu cầu: Tất cả sản phẩm, Móc khoá, Văn phòng phẩm, Gia công theo yêu cầu, Trang trí không gian, Đồ dùng hằng ngày, Quà tặng cá nhân, Lọc theo nhu cầu, Bộ lọc sản phẩm, Tìm kiếm sản phẩm...
2. Thông số in 3D chuyên ngành: Công nghệ in, Vật liệu, Kích thước mẫu, Chiều cao lớp in, Đầu phun, Mật độ ruột, Vật liệu hỗ trợ, Dung sai / Độ chính xác, Năng lực sản xuất, Tiêu chuẩn kỹ thuật, Không cần, Nhựa PLA · Công nghệ FDM, Cam...
3. Điều hướng PDP & Tabs: Tổng quan, Thông số in 3D, Tuỳ chỉnh sản phẩm, Mô hình in 3D, In theo yêu cầu, Nhận tư vấn sản phẩm, Trao đổi yêu cầu với kỹ sư, Thông số in tham khảo...
4. Thao tác lọc & Sắp xếp: Nổi bật nhất, Mới nhất, Tên A-Z, Đặt lại bộ lọc, Không tìm thấy sản phẩm phù hợp...
5. Banner & Khối CTA: Tạo nên phiên bản mang dấu ấn riêng, Nhận tư vấn ngay, Sản phẩm khác, Xem tất cả...

#### Scenario: Tra cứu từ điển UI khi chuyển đổi locale
- **WHEN** locale hiện tại là `en` và component gọi `tt('Thông số in 3D')`
- **THEN** chuỗi trả về là `3D Printing Specs` (hoặc `3D Printing Specifications`).
- **WHEN** locale hiện tại là `vi` và component gọi `tt('Thông số in 3D')`
- **THEN** chuỗi trả về là `Thông số in 3D`.

---

### Requirement: Song ngữ hóa trang Danh mục Sản phẩm (Listing, Filter, Sort, Pagination)
Trang Danh mục Sản phẩm (`Product/Index.vue`) và component bộ lọc (`ProductFilter.vue`) SHALL hiển thị hoàn toàn bằng Tiếng Anh khi người dùng truy cập route `/en/products`:
1. Eyebrow "BỘ SƯU TẬP HH MAKER" hiển thị `HH MAKER COLLECTION`.
2. Tiêu đề chính "Ý TƯỞNG THÀNH HÌNH" hiển thị `BRINGING IDEAS TO LIFE`.
3. Breadcrumb hiển thị `Home > Products (X products)` thay vì `Trang chủ > Sản phẩm (X sản phẩm)`.
4. Dropdown sắp xếp hiển thị các tùy chọn tiếng Anh: `Most Popular`, `Newest`, `Name A-Z`.
5. Bộ lọc Sidebar hiển thị các tiêu đề: `PRODUCT CATEGORIES`, `FILTER BY PURPOSE`, `Reset filters`.
6. Card tư vấn hiển thị: `Custom 3D Printing`, `Colors, dimensions, and personalization tailored to your needs?`, nút `Contact for Consultation` trỏ đến localized route `contact`.
7. Trạng thái không có kết quả (Empty state) hiển thị tiêu đề và hướng dẫn hoàn toàn bằng Tiếng Anh.

#### Scenario: Người dùng duyệt Danh mục Sản phẩm ở chế độ Tiếng Anh
- **WHEN** người dùng truy cập `/en/products`
- **THEN** toàn bộ tiêu đề, breadcrumb, toolbar sắp xếp, bộ lọc danh mục và nhu cầu đều hiển thị bằng Tiếng Anh chuẩn xác, không còn chuỗi tiếng Việt bị sót.

---

### Requirement: Song ngữ hóa 16 sản phẩm mẫu (mockProducts) theo locale hiện tại
16 sản phẩm mẫu (`mockProducts`) hiển thị trên trang danh mục SHALL tự động cung cấp dữ liệu Tiếng Anh khi locale hiện tại là `en`:
1. Tên sản phẩm (`name` / `title`) bằng tiếng Anh (ví dụ: `Iron Man Mark 85 Model - High Detail 8K Resin`, `Mini Robot Mechanical Chassis - Impact Resistant Carbon Fiber`...).
2. Danh mục sản phẩm (`category`) bằng tiếng Anh: `Custom Manufacturing`, `Stationery`, `Keychains`...
3. Nhu cầu (`purpose`) bằng tiếng Anh: `Space Decor`, `Daily Essentials`, `Personal Gifts`...
4. Mô tả sản phẩm (`description`) bằng tiếng Anh chuẩn kỹ thuật in 3D.
5. Đường dẫn sản phẩm (`url` / `slug`) sử dụng Ziggy localized routes để khi click từ trang tiếng Anh, điều hướng chính xác đến `/en/products/{slug}`.

#### Scenario: Hiển thị thẻ sản phẩm trên lưới ở locale tiếng Anh
- **WHEN** người dùng xem danh sách sản phẩm tại `/en/products`
- **THEN** 16 thẻ sản phẩm hiển thị tên, danh mục và mô tả ngắn bằng tiếng Anh, đường dẫn chi tiết trỏ tới URL tiền tố `/en/products/...`.

---

### Requirement: Song ngữ hóa trang Chi tiết Sản phẩm (PDP 3 tabs, SpecTable, VariantSelector, CTA, Related)
Trang Chi tiết Sản phẩm (`Product/Show.vue`) và các component con SHALL hiển thị hoàn toàn bằng Tiếng Anh khi ở locale `en`:
1. Breadcrumbs hiển thị `Home > Products > [Category] > [Product Name]`.
2. Phân loại Badges: `3D Printed Model`, `On-Demand Printing`.
3. Lựa chọn tham khảo (`VariantSelector.vue`): `Suggested Material`, `Sample Color`, `Customization Options`.
4. Nút hành động chính: `Request Product Consultation` và ghi chú tư vấn tiếng Anh.
5. 3 Tabs nội dung: `Overview`, `3D Printing Specs`, `Product Customization`.
6. Bảng thông số kỹ thuật (`SpecTable.vue`): `Reference Print Specifications`, nhãn các hàng (`Print Technology`, `Material`, `Model Dimensions`, `Layer Height`, `Nozzle Diameter`, `Infill Density`, `Support Material`), và lưu ý kỹ thuật tiếng Anh.
7. Nội dung Tab 2 (Tiêu chuẩn kỹ thuật: Độ chính xác, Độ phân giải lớp, Năng lực sản xuất) và Tab 3 (3 bước đặt in tùy chỉnh và nút trao đổi với kỹ sư) chuyển ngữ 100% sang tiếng Anh.
8. Khối Sản phẩm liên quan (`RelatedProducts.vue`) và Banner CTA (`ProductCtaBanner.vue`) hiển thị tiêu đề, mô tả và nút CTA bằng tiếng Anh.
9. Toast thông báo khi bấm nút tư vấn hiển thị bằng tiếng Anh.

#### Scenario: Truy cập trang chi tiết sản phẩm ở ngôn ngữ Tiếng Anh
- **WHEN** người dùng truy cập `/en/products/{slug}`
- **THEN** tất cả 3 tabs, bảng thông số SpecTable, khối VariantSelector, các bước đặt in và nút CTA đều hiển thị bằng Tiếng Anh.

---

### Requirement: Dữ liệu dịch Tiếng Anh trong Database và Localized SEO trong ProductController
1. Database `product_translations` và `product_category_translations` SHALL được bổ sung bản ghi dịch Tiếng Anh (`locale = 'en'`) an toàn cho Sản phẩm ID 1 và Danh mục ID 1 mà không drop hay truncate bảng.
2. `ProductController.php` SHALL trả về `seo.title` và `seo.description` tương ứng theo `current_locale()` (`vi` hoặc `en`), đảm bảo thẻ meta title và description hiển thị đúng ngôn ngữ.

#### Scenario: Truy vấn SEO metadata cho trang sản phẩm tiếng Anh
- **WHEN** gọi route `/en/products`
- **THEN** thẻ `<title>` và `<meta name="description">` phản ánh nội dung tiếng Anh của HH Maker 3D Printing.
