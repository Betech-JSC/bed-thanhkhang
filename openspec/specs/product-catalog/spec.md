# product-catalog Specification

## Purpose
Cung cấp giao diện Danh sách Sản phẩm chuẩn Figma Option 3 (Node 2346:669) và trang Chi tiết Sản phẩm (PDP) với bố cục Sidebar 240px gồm 2 Card lọc chuyên biệt, khu vực lưới sản phẩm 3 cột 867px kèm thanh điều hướng Breadcrumb/Sort, Banner kêu gọi sáng tạo cuối trang và hỗ trợ đa ngôn ngữ (Tiếng Việt và Tiếng Anh) toàn diện.

## Requirements

### Requirement: Song ngữ hóa trang Danh mục Sản phẩm (Listing, Filter, Sort, Pagination)
Trang Danh mục Sản phẩm (`Product/Index.vue`) và các component con (`ProductHeroBanner.vue`, `ProductFilter.vue`, `ProductCard.vue`, `ProductPagination.vue`) SHALL hiển thị hoàn toàn bằng Tiếng Anh khi người dùng truy cập route `/en/products` và bằng Tiếng Việt khi truy cập `/san-pham`:
1. Hero Banner (`BANNER_PRODUCT_HERO`) hiển thị breadcrumb `Home / Products` (`Trang chủ / Sản phẩm`), tiêu đề `PRODUCTS` (`SẢN PHẨM`) và mô tả song ngữ từ CMS.
2. Bộ lọc Sidebar hiển thị tiêu đề `PRODUCT CATEGORIES` (`DANH MỤC SẢN PHẨM`) cùng cây danh mục 3 tầng tiếng Anh từ bảng `product_category_translations` (`locale = 'en'`).
3. Toolbar hiển thị số lượng kết quả `Showing X–Y of Z products` (`Hiển thị X–Y trên Z sản phẩm`) hoặc `Showing 0 products` (`Hiển thị 0 sản phẩm`), nút Mobile `Filter` (`Bộ lọc`), và dropdown sắp xếp `Sort by` (`Sắp xếp theo`) với 4 tùy chọn: `Most Popular` (`Nổi bật nhất`), `Newest` (`Mới nhất`), `Name A-Z` (`Tên A-Z`), `Name Z-A` (`Tên Z-A`).
4. Hàng Active Filter Chips hiển thị nút `Reset all` (`Đặt lại tất cả`), chip `Keyword:` (`Từ khoá:`), các nhãn chip danh mục theo locale hiện tại và aria-label đóng chip `Clear search` / `Remove filter`.
5. Thẻ sản phẩm hiển thị nút `View Details` (`Xem chi tiết`) trỏ đúng URL sản phẩm theo locale (`/en/products/{slug}` hoặc `/san-pham/{slug}`).
6. Thanh phân trang hiển thị aria-label `Product pagination` (`Phân trang sản phẩm`) và `Page N` (`Trang N`).
7. Trạng thái DB trống hoặc không có kết quả lọc (`EmptyState`) hiển thị tiêu đề, mô tả và nút hành động (`Contact for Consultation`, `Reset all`) hoàn toàn bằng Tiếng Anh khi ở `/en/products`.

#### Scenario: Người dùng duyệt Danh mục Sản phẩm ở chế độ Tiếng Anh
- **WHEN** người dùng truy cập `/en/products` trên Desktop hoặc Mobile
- **THEN** toàn bộ Hero Banner, breadcrumb, thanh đếm kết quả, nút `Filter`, dropdown `Sort by`, nút `Reset all`, cây danh mục `PRODUCT CATEGORIES`, thẻ sản phẩm (`View Details`) và `EmptyState` (nếu có) đều hiển thị bằng Tiếng Anh chuẩn xác, không còn chuỗi tiếng Việt hay thuật ngữ HH Maker bị sót.

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
1. Database `product_categories`, `product_category_translations`, `products`, `product_translations` SHALL giữ nguyên bộ **4 danh mục gốc từ Bước 1** (`Linh kiện thang cuốn`, `Linh kiện thang nâng ô tô`, `Hệ thống băng tải hành lý`, `Linh kiện dây chuyền đóng gói`) cùng cây danh mục 3 tầng và tập `103` sản phẩm song ngữ (`vi`/`en`) từ `ThanhKhangProductCatalogSeeder` mà không drop hay truncate bảng.
2. `sliders` / `slider_translations` SHALL có bản ghi idempotent `BANNER_PRODUCT_HERO` từ `ThanhKhangProductHeroSeeder` sử dụng ảnh `/assets/figma/san-pham/hero-bg.webp`, không ghi đè dữ liệu nếu Admin CMS đã chỉnh sửa.
3. `ProductController@index()` và `resolveCatalogSeo()` SHALL trả về dữ liệu CMS động theo chính sách **Zero-Fallback** (khi DB trống trả `products: []`, `categories: []`, `heroBanner: null`, `seo.title: null`, `seo.description: null`), ưu tiên `MetaPage` (`/san-pham` hoặc `/en/products`) trước `Config::getGeneralSettings($locale)`, xóa cache `products_index_{locale}` ngay khi Admin cập nhật `Product`, `ProductCategory`, `Slider`, `Config` hoặc `MetaPage`, và được kiểm chứng bằng `tests/Feature/ProductsPageCmsTest.php`.

#### Scenario: Truy vấn SEO metadata cho trang sản phẩm tiếng Anh
- **WHEN** gọi route `/en/products` và `/san-pham` với dữ liệu CMS đầy đủ
- **THEN** response trả về đúng `heroBanner` (`BANNER_PRODUCT_HERO` dùng `/assets/figma/san-pham/hero-bg.webp`), `4` danh mục gốc kèm nhánh con theo đúng `locale` và thẻ `<title>` / `<meta name="description">` phản ánh nội dung Thành Khang Tecsat theo ưu tiên `MetaPage` rồi mới tới `Config::getGeneralSettings`.

#### Scenario: Trạng thái Database trống trả về Zero-Fallback HTTP 200
- **WHEN** các bảng `sliders`, `products`, `product_categories`, `meta_pages`, `settings` trống hoàn toàn (`0` bản ghi)
- **THEN** `/san-pham` và `/en/products` vẫn trả HTTP `200` với `heroBanner: null`, `products: []`, `categories: []`, `seo.title: null` và `seo.description: null`.

---

### Requirement: Hero Banner Danh mục Sản phẩm khóa tỷ lệ (BANNER_PRODUCT_HERO, Figma Node 979:30380)
Hệ thống SHALL hiển thị Hero Banner ở đầu trang Danh sách Sản phẩm (`ProductHeroBanner.vue`) theo chuẩn Figma Node `979:30380` (`1440×252px`):
- Khung banner MUST khóa tỷ lệ `aspect-[390/240] md:aspect-[1440/252] min-h-[220px] xl:min-h-0` (không để `md:min-h-[252px]` cản tỷ lệ trên `xl`), bọc ảnh nền trong thẻ `<picture>` với `w-full h-full object-cover object-center` nạp động từ `BANNER_PRODUCT_HERO` (`/assets/figma/san-pham/hero-bg.webp`).
- Lớp phủ gradient `270deg` (`rgba(1,16,30,0)` đến `rgba(1,16,30,0.8)`) ở `62.36%` bên trái trên Desktop và scrim tối WCAG trên Mobile.
- Khối nội dung nằm trong `.app-container-narrow` (`1152px`, `px-4 md:px-8`), gồm breadcrumb (`Trang chủ / Sản phẩm`), tiêu đề `<h1>` (`36px/50px` Bold trên Desktop, `28px/38px` trên Mobile) và mô tả ngắn `14px/22px`.
- Khi `heroBanner` là `null` hoặc không có dữ liệu, section Hero Banner MUST ẩn hoàn toàn (Zero-Fallback) và không render nội dung thay thế cứng.

#### Scenario: Kiểm tra tỷ lệ Hero Banner trên Desktop (`1440×900`) và Mobile (`375×812` / `390×844`)
- **WHEN** người dùng truy cập `/san-pham` ở viewport Desktop `1440×900` và Mobile `390×844` (hoặc `375×812`) khi DB có `BANNER_PRODUCT_HERO`
- **THEN** Hero Banner hiển thị đúng `1440×252px` trên Desktop (`aspect-[1440/252]` với `xl:min-h-0`), tỷ lệ `390/240` (`>= 220px`) trên Mobile, ảnh nền load thành công (`naturalWidth > 0`) không méo hình, và chỉ có duy nhất 1 thẻ `<h1>`.

---

### Requirement: Bộ lọc danh mục 3 tầng 264px (Figma Node 979:30394)
Hệ thống SHALL cung cấp Sidebar lọc danh mục 3 tầng (`L0 -> L1 -> L2`) theo chuẩn Figma Node `979:30394` dựa trên đúng **4 danh mục gốc trong DB Bước 1** (`ProductFilter.vue`):
- Trên Desktop (`>= 1024px`), Sidebar nằm bên trái với độ rộng cố định `264px`, nền `#FAFAFA`, `sticky top-[96px]`; trên Mobile (`< 1024px`), hiển thị trong ngăn kéo off-canvas trượt từ trái sang kèm nút đóng `×`.
- Tiêu đề `DANH MỤC SẢN PHẨM` (`20px/32px` Bold `#00284B`), các hàng `L0` cao tối thiểu `51px` có viền dưới `1px solid #CDD5DF`, chữ `16px/26px` Bold `#364152` (active `#005EA4`) và icon chevron `20×20px`.
- Khối mở rộng `L1 + L2` dùng nền `#EEF2F6`, `padding: 24px`, `gap: 20px`:
  - **Mục `L1` không có danh mục con** (như Figma `979:30401`, `979:30430`) MUST đặt nhãn văn bản (`14px/22px`) và ô checkbox `14×14px` liền kề nhau theo chiều ngang với `gap: 8px` (`justify-start gap-2`), KHÔNG dùng `justify-between` đẩy checkbox ra sát lề phải.
  - **Nhóm `L1` có danh mục con** (như Figma `979:30405`) MUST đặt nhãn + checkbox `tri-state` (`gap: 8px`) ở bên trái và nút chevron toggle `L2` (`979:30410`) ở bên phải (`justify-between`).
  - **Danh sách `L2`** (`979:30411`) dùng `px-[20px] gap-[12px]`, mỗi hàng `L2` (`979:30412`) đặt nhãn bên trái và ô checkbox `14×14px` sát lề phải (`justify-between gap-2`).
- Trạng thái checkbox checked dùng nền `#005EA4` bo góc `2px` kèm icon check trắng; unchecked dùng viền `1px solid #9AA4B2`.

#### Scenario: Phân biệt bố cục checkbox L1 không con và L2 trên Desktop
- **WHEN** mở nhánh `L0` đầu tiên trong Sidebar trên Desktop (`1440×900`) có cả mục `L1` không con và nhóm `L1` có con `L2`
- **THEN** mục `L1` không con hiển thị checkbox nằm cách nhãn đúng `8px` (`gap-2`), trong khi các mục `L2` bên trong khối thụt lề `px-5` hiển thị nhãn căn trái và checkbox căn sát lề phải của khung `176px`.

#### Scenario: Lọc sản phẩm 3 tầng và đồng bộ URL query
- **WHEN** người dùng tick chọn một mục `L1`, `L2` hoặc nhóm `L1` (tri-state) trên Sidebar hoặc Mobile Drawer
- **THEN** danh sách sản phẩm cập nhật tức thì theo đúng nhánh con cháu đã chọn, URL query (`?category=`, `?subcategory=`, `?categories=`) được đồng bộ, và chip lọc tương ứng xuất hiện ở phía trên lưới sản phẩm.

---

### Requirement: Lưới sản phẩm 3x3 (856px), Toolbar lọc & Active Chips (Figma Node 979:30446)
Hệ thống SHALL hiển thị khu vực sản phẩm bên phải có độ rộng `856px` trên Desktop (cách Sidebar `32px` trong `.app-container-narrow` `1152px`, tổng chiều ngang section `py-8 md:py-16 lg:pt-[48px] lg:pb-[80px]`) theo chuẩn Figma Node `979:30446`:
- **Toolbar (`979:30447`)**: Hàng trên hiển thị bộ đếm `"Hiển thị X–Y trên Z sản phẩm"` (`14px/22px` `#364152`) bên trái, nút mở `"Bộ lọc"` trên Mobile (`< lg`) và dropdown `"Sắp xếp theo"` (`979:30450`, viền `#CDD5DF`, bo góc `4px`) bên phải; hàng dưới (`979:30453`) hiển thị khi có filter đang kích hoạt gồm nút `"Đặt lại tất cả"` (`979:30457`) và các Active Filter Chips nền `#E3E8EF` bo góc `4px` kèm nút xóa `×`.
- **Lưới sản phẩm (`979:30460`)**: Hiển thị tối đa `9` sản phẩm/trang (`PAGE_SIZE = 9`) trên lưới `grid-cols-2 lg:grid-cols-3` (`gap-3 md:gap-6 lg:gap-x-[32px] lg:gap-y-[24px]`).
- **Thẻ sản phẩm (`ProductCard.vue`, Figma `979:30462`)**: Kích thước chuẩn Desktop `264×371px`, nền `#FFFFFF`, viền `1px solid #E3E8EF`, bo góc `4px`, khung ảnh `aspect-[262/185]` nền trắng dùng `object-contain`, tiêu đề `16px/26px` Bold `#00284B` (`line-clamp-3`) và nút CTA `"Xem chi tiết"` cao `38px` nền `#005EA4` chữ `#FAFAFA`.

#### Scenario: Hiển thị lưới sản phẩm 3×3 và bộ lọc chip trên Desktop (`1440×900`)
- **WHEN** trang `/san-pham` hiển thị ở Desktop `1440×900` với bộ dữ liệu `103` sản phẩm
- **THEN** Sidebar rộng `264px`, khoảng cách sang cột phải là `32px`, cột phải rộng `856px`, lưới hiển thị `9` thẻ sản phẩm theo bố cục `3×3` với khoảng cách ngang `32px`, khoảng cách dọc `24px`, mỗi thẻ có viền `#E3E8EF` và chiều rộng xấp xỉ `264px`.

---

### Requirement: Thanh phân trang số chuẩn Figma 979:30474 (không Prev/Next)
Hệ thống SHALL hiển thị thanh phân trang (`ProductPagination.vue`) ở cuối lưới sản phẩm khi `totalPages > 1` theo đúng chuẩn Figma Node `979:30474`:
- Thanh phân trang MUST chỉ gồm các nút số trang và ký hiệu lược bỏ `"..."` (khi `totalPages > 6`), tuyệt đối KHÔNG render nút mũi tên Prev (`<`) và Next (`>`).
- Các nút xếp ngang căn giữa với khoảng cách `gap-2` (`8px`). Với trạng thái mặc định `totalPages = 8` ở trang `1` (tương ứng `1, 2, 3, 4, ..., 8`), thanh phân trang hiển thị đúng `6` ô đạt tổng chiều rộng `280px` (`6 × 40px + 5 × 8px`) trên Desktop (`>= 640px`).
- Mỗi ô có bo góc `4px` (`rounded-[4px]`), chữ `Open Sans` Medium `16px/26px` (trên `sm:`):
  - Ô đang active: nền `#005EA4`, viền `1px solid #005EA4`, chữ `#FAFAFA`.
  - Ô không active và ô `"..."`: nền `#FFFFFF`, viền `1px solid #E3E8EF`, chữ `#364152`.

#### Scenario: Kiểm tra cấu trúc thanh phân trang không có Prev/Next và kích thước 280×40px trên Desktop
- **WHEN** danh sách sản phẩm có nhiều hơn `1` trang (`totalPages > 1`) và người dùng xem trang `/san-pham` trên Desktop (`1440×900`)
- **THEN** thanh phân trang KHÔNG chứa nút `Trang trước` (`Prev`) hay `Trang tiếp theo` (`Next`), mỗi ô trang có kích thước `40×40px`, khoảng cách giữa các ô là `8px`, ô trang hiện tại có nền `#005EA4` chữ `#FAFAFA`, ô thường có nền `#FFFFFF` viền `#E3E8EF`, và tổng chiều rộng của dãy `6` ô là `280px`.

---

### Requirement: Mobile Touch Targets >= 44px & Trạng thái EmptyState Zero-Fallback
1. Trên thiết bị Mobile (`< 1024px` / `< 640px`), mọi nút và phần tử tương tác chính trên Trang Danh sách Sản phẩm MUST đạt kích thước vùng chạm tối thiểu `44×44px` (`>= 44px`), bao gồm: nút mở `"Bộ lọc"`, nút đóng drawer `×`, các nút checkbox danh mục `L1`/`L2` và nút chevron mở rộng `L1` trong `ProductFilter.vue`, nút `"Sắp xếp theo"` và các lựa chọn sort, nút `"Đặt lại tất cả"`, nút xóa `×` trên từng filter chip, và các nút số phân trang (`w-11 h-11 sm:w-[40px] sm:h-[40px]`).
2. Khi danh mục sản phẩm trong DB trống (`allProducts.length === 0` và không có filter) hoặc khi bộ lọc/tìm kiếm hiện tại không có sản phẩm khớp (`filteredProducts.length === 0`), hệ thống MUST hiển thị component `<EmptyState variant="light" />` rõ ràng kèm nút điều hướng trang Liên hệ (và nút `"Đặt lại tất cả"` khi đang lọc), không dùng dữ liệu giả thay thế và không gây tràn ngang (`scrollWidth <= innerWidth`).

#### Scenario: Xác minh vùng chạm Mobile >= 44px và không tràn ngang trên Mobile (`375×812` & `390×844`)
- **WHEN** mở trang `/san-pham` trên viewport Mobile `375×812` (hoặc `390×844`), mở drawer `"Bộ lọc"`, tương tác filter chip và kiểm tra thanh phân trang
- **THEN** nút `"Bộ lọc"`, nút `"Sắp xếp theo"`, các nút `L1`/`L2`/chevron trong drawer, nút `"Đặt lại tất cả"`, nút xóa chip `×` và các nút phân trang đều có chiều cao hoặc kích thước hit-area `>= 44px`, toàn trang có `scrollWidth <= innerWidth` (`0px` tràn ngang).

#### Scenario: Hiển thị EmptyState khi tìm kiếm không khớp hoặc DB trống
- **WHEN** người dùng nhập từ khóa `?search=khong-ton-tai-xyz-999` hoặc truy cập `/san-pham` khi DB không có sản phẩm
- **THEN** lưới sản phẩm và thanh phân trang ẩn đi, `<EmptyState variant="light" />` hiển thị thông báo rõ ràng kèm nút `"Liên hệ tư vấn"` (và `"Đặt lại tất cả"` khi có bộ lọc đang kích hoạt).

---

### Requirement: Từ điển UI đa ngôn ngữ ngành thiết bị công nghiệp & phụ tùng Thành Khang
Hệ thống từ điển giao diện Frontend (`resources/Frontend/lang/en.json`, `resources/Frontend/lang/vi.json`) cùng `resources/lang/{vi,en}/ui.php` SHALL cung cấp đầy đủ các khóa dịch song ngữ Việt–Anh cho toàn bộ chuỗi giao diện Trang Danh sách Sản phẩm Thành Khang Tecsat, bao gồm:
- Nhãn bộ lọc & danh mục: `DANH MỤC SẢN PHẨM`, `Bộ lọc`, `Bộ lọc sản phẩm`, `Đóng bộ lọc`, `Mở rộng danh mục con`.
- Toolbar & Chips: `Hiển thị`, `trên`, `sản phẩm`, `Sắp xếp theo`, `Nổi bật nhất`, `Mới nhất`, `Tên A-Z`, `Tên Z-A`, `Đặt lại tất cả`, `Từ khoá:`, `Xoá tìm kiếm`, `Xoá bộ lọc`.
- Card, Phân trang & EmptyState: `Xem chi tiết`, `Phân trang sản phẩm`, `Trang`, `Danh mục sản phẩm đang được cập nhật`, `Không tìm thấy sản phẩm phù hợp`, `Liên hệ tư vấn`.

#### Scenario: Đầy đủ khóa dịch tiếng Anh cho giao diện Product Index
- **WHEN** kiểm tra toàn bộ khóa `tt(...)` / `translate(...)` sử dụng trong `Product/Index.vue`, `ProductHeroBanner.vue`, `ProductFilter.vue`, `ProductCard.vue` và `ProductPagination.vue` đối chiếu với `resources/Frontend/lang/en.json`
- **THEN** 100% khóa dịch đều tồn tại bản dịch Tiếng Anh tương ứng (`0` khóa thiếu) và hiển thị đúng ngôn ngữ trên `/en/products`.

---

### Requirement: Dọn dẹp tài nguyên ảnh export thừa public/assets/figma/san-pham/hero-bg.png
Hệ thống SHALL loại bỏ tệp ảnh export thô dư thừa `public/assets/figma/san-pham/hero-bg.png` (~2.99 MB, không được tham chiếu trong mã nguồn hay cơ sở dữ liệu) và giữ nguyên tệp tối ưu `/assets/figma/san-pham/hero-bg.webp` cùng các tệp SVG icon đang phục vụ Trang Danh sách Sản phẩm.

#### Scenario: Xóa hero-bg.png mà không ảnh hưởng hiển thị Hero Banner
- **WHEN** tệp `public/assets/figma/san-pham/hero-bg.png` được gỡ khỏi repository và build lại Frontend
- **THEN** lệnh tìm kiếm toàn repo cho `hero-bg.png` trả về `0` tham chiếu, tệp `public/assets/figma/san-pham/hero-bg.webp` vẫn tồn tại và ảnh Hero Banner trên `/san-pham` & `/en/products` tải với HTTP `200` (`naturalWidth > 0`).
