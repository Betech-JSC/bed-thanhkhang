# Design: Chuẩn hóa Trang Danh sách Sản phẩm Thành Khang (`feat-page-products`)

## Context

- **Routes & Controller**: `routes/frontend.php` định tuyến `GET /san-pham` (`vi.products.index`) và `GET /en/products` (`en.products.index`) tới `App\Http\Controllers\Frontend\ProductController@index`. Controller truy vấn động `Product`, `ProductCategory`, `Slider` (`BANNER_PRODUCT_HERO`), `MetaPage` / `Config::getGeneralSettings()` và truyền Inertia props (`products`, `categories`, `heroBanner`, `seo`) cho `resources/Frontend/js/Pages/Product/Index.vue`.
- **Layout Shell**: `resources/Frontend/js/app.js` tự động gán `MainLayout.vue` làm layout mặc định cho mọi page (kèm `<Header>` dính trên cùng và `<Footer>` bên dưới); `resources/Frontend/js/Pages/Product/Index.vue` cũng khai báo tường minh `layout: MainLayout` trong block `<script>` kết hợp `<script setup>`.
- **Figma Reference**: Frame `979:30355` (*Danh sách sản phẩm*, `1440×2318px`, `#FAFAFA`) — chi tiết các cụm con đã đo trực tiếp qua Figma MCP:
  - `979:30380` (`Product introduction`): `1440×252px`, ảnh nền `979:30381`, scrim `979:30382` (`898×252px`, `270deg`), khối chữ `979:30383` (`max-w: 400px`, `left: 144px`).
  - `979:30393` (`Catalog layout`): `1152px` (`.app-container-narrow`), `gap: 32px` giữa Sidebar `264px` (`979:30394`) và Right Column `856px` (`979:30446`).
  - `979:30394` (`Product filters`): `264px`, nền `#FAFAFA`, panel mở rộng `979:30400` nền `#EEF2F6` `padding: 24px`, `gap: 20px`. Các mục `L1` không con (`979:30401`, `979:30430`) dùng khung nội bộ `display: flex; gap: 8px; align-items: center` (`145px` / `152px`), trong khi nhóm `L2` (`979:30411`) dùng `padding: 0 20px`, `gap: 12px`, từng hàng `L2` rộng `176px` `justify-content: space-between; gap: 8px`.
  - `979:30460` (`Product grid`): `856×1161px`, 3 hàng × 3 cột, `gap-x: 32px`, `gap-y: 24px`, mỗi thẻ `979:30462` `264×371px`.
  - `979:30474` (`Page numbers`): `280×40px`, `display: flex; gap: 8px`, gồm `6` ô `40×40px` (`1`, `2`, `3`, `4`, `...`, `8`), `border-radius: 4px`, active `#005EA4`/`#FAFAFA`, inactive `#FFFFFF`/`#E3E8EF`/`#364152`, **không có nút Prev/Next**.
- **Đối chiếu Graphify AST (`graphify-out/graph.json` & `GRAPH_REPORT.md`)**:
  - Đã kiểm tra cấu trúc AST hiện hữu: `Product/Index.vue`, `ProductHeroBanner.vue`, `ProductFilter.vue`, `ProductCard.vue`, `ProductPagination.vue`, `EmptyState.vue`, `useTranslate.js` đều đã tồn tại và kết nối đúng vai trò. Không tạo thêm component UI mới để tránh trùng lặp AST.
  - Các node thuộc PDP (`Product/Show.vue`, `ProductGallery.vue`, `VariantSelector.vue`, `SpecTable.vue`, `RelatedProducts.vue`, `ProductCtaBanner.vue`, `SpecModal.vue`) đứng tách biệt cho trang Chi tiết sản phẩm và nằm ngoài phạm vi change này.

## Goals / Non-Goals

**Goals:**
1. Đồng bộ hoàn toàn `openspec/specs/product-catalog/spec.md` với thiết kế Thành Khang (`979:30355`) cho Trang Danh sách Sản phẩm.
2. Sửa dứt điểm 4 sai lệch UI/Responsive hiện hữu ở `ProductPagination.vue`, `ProductFilter.vue`, `ProductHeroBanner.vue`, và `Product/Index.vue` (chi tiết bên dưới).
3. Dọn bỏ tệp export thô `public/assets/figma/san-pham/hero-bg.png` (~2.99 MB, đã xác nhận `0` tham chiếu trong repo & DB).
4. Hoàn tất Build Gate (`yarn route`, `yarn frontend:build`), cập nhật Graphify (`graphify . --code-only`), chạy PHPUnit `ProductsPageCmsTest`, tạo & chạy Playwright E2E spec cho `/san-pham` & `/en/products` và kiểm tra regression Trang Chủ `/`.

**Non-Goals:**
1. Không chỉnh sửa `Product/Show.vue` hay các component riêng của PDP (sẽ thực hiện ở OpenSpec change riêng).
2. Không thêm/sửa/xóa danh mục sản phẩm ngoài **4 danh mục gốc DB Bước 1** (`ThanhKhangProductCatalogSeeder`).
3. Không chạy migration/seeder thay đổi dữ liệu thật nếu DB hiện tại (`sliders` `BANNER_PRODUCT_HERO`, `4` root categories, `103` products) đã khớp.

## Decisions

### 1. Chuẩn hóa `ProductPagination.vue` khớp Figma `979:30474` (Gỡ Prev/Next + Touch Target Mobile)
- **Lý do**: Figma `979:30474` thiết kế dãy phân trang tối giản chỉ có số trang và dấu `"..."` (`280×40px` cho 6 ô `40×40px`, `gap: 8px`), không có 2 nút chevron Prev/Next (`1329:53176` / `1329:53167` vốn thuộc thiết kế trang Tài liệu).
- **Giải pháp**:
  - Gỡ bỏ 2 `<button>` Prev/Next trong `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue`.
  - Giữ logic `pagesToShow` (`<= 6` trang liệt kê hết; `> 6` trang rút gọn còn tối đa 6 ô `1, 2, 3, 4, ..., N` khi `current <= 3` — vừa khớp chính xác 6 ô của Figma `979:30474` khi `N = 8` ở trang 1).
  - Kích thước ô: `w-11 h-11 sm:w-[40px] sm:h-[40px] rounded-[4px]` và container `gap-2` (`8px`). Trên Mobile (`< 640px`), mỗi nút đạt `44×44px` (`>= 44px`); từ `sm:` lên Desktop (`1440px`), giữ đúng `40×40px` (`280×40px` cho 6 ô).
  - Thêm `useTranslate()` hoặc đồng bộ `tt()` (giống các component khác) để đảm bảo `aria-label` đa ngôn ngữ ổn định cả khi chạy SSR lẫn CSR.
- **Phương án thay thế đã cân nhắc**: Giữ nút Prev/Next ẩn trên Desktop hoặc làm mờ — bị loại vì sai cấu trúc trực quan Figma `979:30474`.

### 2. Chuẩn hóa `ProductFilter.vue` (L1 không con `gap-2` vs L2 `justify-between` + Mobile Touch Targets)
- **Lý do**: Theo dữ liệu Figma `979:30394`:
  - Node `979:30401` (`Tay vịn thang cuốn`, `145×22px`) và `979:30430` (`Bánh xe thang cuốn`, `152×22px`) là các mục **L1 không có con**: ô checkbox `14×14px` nằm ngay sau chữ với `gap: 8px` (`gap-2`), không dạt sang lề phải của khối `216px`.
  - Node `979:30411` (`Frame 1000005954`, `216×192px`, `padding: 0 20px`, `gap: 12px`) là danh sách **L2**: mỗi hàng (`979:30412`) có nhãn bên trái và checkbox sát lề phải `176px` (`justify-between gap-2`).
- **Giải pháp**:
  - Sửa nhánh `L1` không con (`v-else` tại `ProductFilter.vue:242`) từ `justify-between gap-2` thành `justify-start gap-2` để checkbox đặt sát nhãn `8px`, phân biệt rõ về mặt thị giác giữa cấp `L1` và cấp `L2` (`px-5` + `justify-between`).
  - Nâng vùng chạm Mobile (`< lg`, trong Off-Canvas Drawer):
    - Hàng L1 (cả có con và không con): `min-h-[44px] lg:min-h-[22px]`.
    - Nút chevron toggle L1 (`979:30410`): `w-11 h-11 lg:w-5 lg:h-5` (`44×44px` trên Mobile, giữ nguyên `20×20px` trên Desktop).
    - Hàng L2: `min-h-[44px] lg:min-h-[22px]`.
- **Không thay đổi dữ liệu DB**: Component render thuần túy từ `props.categories` (`4` danh mục gốc DB Bước 1).

### 3. Khóa tỷ lệ Hero Banner `ProductHeroBanner.vue` với `xl:min-h-0`
- **Lý do**: Class hiện tại `aspect-[390/240] md:aspect-[1440/252] min-h-[220px] md:min-h-[252px]` giữ cứng `252px` từ breakpoint `md` trở lên thay vì giao cho `aspect-[1440/252]` kiểm soát tỷ lệ ở màn hình rộng `xl` (`>= 1280px`), khác với quy chuẩn `DocumentHeroSection.vue`.
- **Giải pháp**: Cập nhật class thành `aspect-[390/240] md:aspect-[1440/252] min-h-[220px] md:min-h-[220px] xl:min-h-0` (giữ `min-h-[220px]` trên `md`/`lg` để nội dung text không bị chèn ép tại `768px`, và nhả `xl:min-h-0` trên `>= 1280px` để khóa chuẩn tỷ lệ `1440/252` = `252px` tại `1440px`).

### 4. Nâng Mobile Touch Targets trên `Product/Index.vue`
- **Các điểm kiểm toán hiện `< 44px` trên Mobile (`< lg`)**:
  - Nút mở `"Bộ lọc"` (`lg:hidden`): từ `h-[40px]` nâng lên `min-h-[44px]` (`h-[44px]`).
  - Nút `"Sắp xếp theo"`: từ `h-[40px]` nâng lên `min-h-[44px] lg:min-h-0 lg:h-[40px]`; các mục `role="option"` trong dropdown dùng `min-h-[44px] lg:min-h-0 py-2.5 lg:py-2`.
  - Nút `"Đặt lại tất cả"`: từ `h-[40px]` nâng lên `min-h-[44px] lg:min-h-0 lg:h-[40px]`.
  - Active Filter Chips (cả search chip lẫn category chips): nâng chiều cao Mobile `min-h-[44px] lg:min-h-0 lg:h-[38px]` và nút đóng `×` dùng `w-9 h-9 -mr-1.5 lg:w-5 lg:h-5 lg:mr-0` (hoặc `min-w-[44px] min-h-[44px] -mr-2 lg:min-w-0 lg:min-h-0 lg:mr-0 lg:w-5 lg:h-5`) để đảm bảo vùng chạm nút `×` đạt `>= 44×44px` trên Mobile mà giữ nguyên `38px` / `20×20px` trên Desktop.

### 5. Dọn tệp thừa `public/assets/figma/san-pham/hero-bg.png`
- Đã kiểm tra toàn repo (`git grep`, `Select-String`) và bảng `sliders` / `slider_translations`: chỉ dùng `/assets/figma/san-pham/hero-bg.webp`. Tệp `public/assets/figma/san-pham/hero-bg.png` (~2.99 MB) là ảnh PNG export thô chưa nén từ Figma trước khi chuyển sang `.webp`.
- Xóa duy nhất tệp `public/assets/figma/san-pham/hero-bg.png` trong bước thực thi `/opsx-apply`; giữ nguyên `hero-bg.webp` và các tệp SVG trong `public/assets/figma/san-pham/`.

## Risks / Trade-offs

- `[Risk] Các test Playwright E2E cũ của HH Maker Option 3 (như product-option3-verify.spec.ts, product-pagination.spec.ts) kỳ vọng giao diện HH Maker cũ hoặc nút Prev/Next` → `[Mitigation]` Viết một Playwright E2E spec mới chuyên kiểm chứng Trang Danh sách Sản phẩm Thành Khang (`Node 979:30355`) cho `/san-pham` và `/en/products` ở cả Desktop (`1440×900`) & Mobile (`375×812` & `390×844`) kèm kiểm tra regression Trang Chủ `/`, không trộn lẫn với selector HH Maker cũ.
- `[Risk] Việc nhả xl:min-h-0 trên ProductHeroBanner có thể khiến chiều cao ở 1280px là 224px` → `[Mitigation]` Nội dung foreground của `BANNER_PRODUCT_HERO` tối đa ~`148px` (< `224px`), vẫn có padding trên dưới rộng rãi; xác minh trực tiếp bằng Playwright ở viewport `1280px` và `1440px`.
- `[Risk] Tăng kích thước nút phân trang Mobile lên 44×44px (w-11 h-11) khi hiển thị 6 ô trên màn hình 375px` → `[Mitigation]` Tính toán độ rộng: `6 × 44px + 5 × 8px = 264 + 40 = 304px` < `343px` (`375px - 32px` padding ngang `px-4`), vừa khít trên 1 hàng ở màn hình `375px` mà không bị xuống dòng hay tràn ngang; kiểm chứng bằng Playwright `375×812`.
