# product-catalog Specification

## Purpose
Cung cấp giao diện Danh sách Sản phẩm chuẩn Figma Option 3 (Node 2346:669) và trang Chi tiết Sản phẩm (PDP) với bố cục Sidebar 240px gồm 2 Card lọc chuyên biệt, khu vực lưới sản phẩm 3 cột 867px kèm thanh điều hướng Breadcrumb/Sort, Banner kêu gọi sáng tạo cuối trang và hỗ trợ đa ngôn ngữ (Tiếng Việt và Tiếng Anh) toàn diện.

## Requirements

### Requirement: Bố cục Sidebar 240px gồm 2 Card lọc độc lập
Hệ thống SHALL cung cấp thanh Sidebar cố định độ rộng 240px bên trái khu vực hiển thị sản phẩm trên màn hình Desktop (và dưới dạng ngăn kéo trượt trên Mobile), bao gồm 2 thẻ tách biệt với khoảng cách 12px theo chuẩn Figma Node 2346:995:
- Thẻ 1 (`2346:996`): Danh mục sản phẩm (chiều cao 464px, nền `#30374F`, bo góc 12px) gồm ô tìm kiếm bo góc 8px kèm icon kính lúp, danh sách 4 danh mục ("Tất cả sản phẩm", "Móc khoá", "Văn phòng phẩm", "Gia công theo yêu cầu"), và nhóm "LỌC THEO NHU CẦU" với nhãn văn bản bên trái và ô checkbox 16x16px bên phải.
- Thẻ 2 (`2346:998`): Tuỳ chỉnh sản phẩm (chiều cao 156px, nền `#30374F`, bo góc 12px) gồm tiêu đề 14px, phụ đề 13px, và nút CTA "Liên hệ tư vấn" dạng gradient xanh chuyển tiếp sang trang liên hệ.

#### Scenario: Hiển thị 2 card lọc độc lập trên Desktop
- **WHEN** người dùng truy cập trang `/san-pham` trên màn hình máy tính (viewport $\ge$ 1024px)
- **THEN** Sidebar bên trái hiển thị rõ ràng 2 khối card riêng biệt với nền `#30374F`, bo góc 12px và khoảng cách giữa hai card là 12px.

#### Scenario: Tương tác lọc theo nhu cầu với checkbox bên phải
- **WHEN** người dùng nhấp chọn một mục trong phần "LỌC THEO NHU CẦU"
- **THEN** ô checkbox ở góc phải chuyển trạng thái active nền xanh `#2970FF`, và danh sách sản phẩm bên phải được lọc tương ứng theo nhu cầu đã chọn.

---

### Requirement: Lưới sản phẩm 3 cột 867px kèm Toolbar và Phân trang
Hệ thống SHALL hiển thị khu vực sản phẩm bên phải với tổng độ rộng 867px trên Desktop theo chuẩn Figma Node 2346:1015, bao gồm:
- Thanh Toolbar trên cùng cao 20px với Breadcrumb ("Trang chủ > Sản phẩm (N sản phẩm)") bên trái và bộ chọn Sắp xếp bên phải.
- Lưới sản phẩm 3 cột (`grid-cols-3`, khoảng cách 24px) với mỗi thẻ sản phẩm có kích thước chuẩn 273px x 419px, bao gồm ảnh showcase 335px nền `#D1D2D2` và thanh thông tin dưới 84px nền `#30374F`.
- Thanh điều hướng phân trang ở cuối lưới hiển thị nút "Trang trước" và "Trang tiếp theo" kèm icon chevron chuẩn Figma Node 2352:9091.

#### Scenario: Hiển thị lưới sản phẩm 3 cột trên Desktop
- **WHEN** danh sách sản phẩm có dữ liệu hiển thị trên Desktop
- **THEN** các thẻ sản phẩm được sắp xếp đều đặn trên lưới 3 cột với khoảng cách giữa các thẻ là 24px, mỗi thẻ cao 419px.

#### Scenario: Chuyển trang qua thanh điều hướng
- **WHEN** người dùng nhấn vào nút "Trang tiếp theo"
- **THEN** hệ thống chuyển sang trang tiếp theo và cập nhật danh sách sản phẩm tương ứng mà không làm giật trang.

---

### Requirement: Banner CTA Lời mời sáng tạo 1152px
Hệ thống SHALL hiển thị khối Banner CTA "Lời mời sáng tạo" ở cuối trang theo chuẩn Figma Node 2346:741 với chiều rộng 1152px, chiều cao tối thiểu 183px, nền gradient xanh `#2970FF` đến `#00359E`, bo góc 16px, chứa tiêu đề 32px Bold và nút hành động "Nhận tư vấn ngay" nền trắng kem `#F5F8FF`.

#### Scenario: Hiển thị Banner CTA cuối trang sản phẩm
- **WHEN** người dùng cuộn xuống dưới cùng của trang sản phẩm
- **THEN** banner CTA hiển thị nổi bật với nền gradient xanh, tiêu đề "Tạo nên phiên bản mang dấu ấn riêng" và nút bấm dẫn đến trang `/lien-he`.

---

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
