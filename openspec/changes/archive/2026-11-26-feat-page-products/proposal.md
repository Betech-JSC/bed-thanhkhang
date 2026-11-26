# Proposal: Chuẩn hóa Trang Danh sách Sản phẩm Thành Khang (`feat-page-products`)

## Why

Trang **Danh sách sản phẩm** (`/san-pham` và `/en/products`, điều phối bởi `App\Http\Controllers\Frontend\ProductController@index` render `resources/Frontend/js/Pages/Product/Index.vue`) đã được chuyển đổi bước đầu sang thiết kế Thành Khang Tecsat (`Figma Node 979:30355`) theo luồng `Home-First Incremental` Giai đoạn C, nhưng vẫn tồn tại 5 khoảng trống so với thiết kế Figma và quy chuẩn dự án cần hoàn thiện trước khi nghiệm thu:

1. **Đặc tả OpenSpec lỗi thời**: `openspec/specs/product-catalog/spec.md` hiện vẫn mô tả giao diện HH Maker Option 3 cũ (`Node 2346:669` nền tối `#30374F`, sidebar `240px` 2 card + `LỌC THEO NHU CẦU`, `16` `mockProducts`, Banner CTA cuối trang `2346:741`).
2. **Pagination chưa khớp Figma `979:30474`**: `ProductPagination.vue` đang render thêm 2 nút Prev/Next (`< / >`, `rounded-[6px]`) và kích thước mobile `w-9 h-9` (`36×36px`), trong khi Figma `979:30474` (`280×40px`) chỉ gồm tối đa 6 ô số/ellipsis `40×40px`, `gap: 8px`, `rounded-[4px]`, không có nút Prev/Next.
3. **Khoảng cách checkbox L1 không con trong `ProductFilter.vue`**: Tại Figma `979:30401` (`Tay vịn thang cuốn`) và `979:30430` (`Bánh xe thang cuốn`), nhãn văn bản và ô checkbox `14×14px` nằm liền nhau với `gap: 8px` (`gap-2`, không giãn ra 2 đầu như nhánh L2 `979:30411`), nhưng code hiện tại đang dùng `justify-between`.
4. **Hero Banner khóa tỷ lệ & Mobile Touch Targets**: `ProductHeroBanner.vue` thiếu `xl:min-h-0` trên cặp `aspect-[390/240] md:aspect-[1440/252]`, và một số nút tương tác Mobile (`< lg`) tại `Product/Index.vue`, `ProductFilter.vue`, `ProductPagination.vue` chưa đạt chuẩn vùng chạm tối thiểu `>= 44px` (`h-[40px]`, `w-5 h-5`, `min-h-[22px]`, `w-9 h-9`).
5. **Tài nguyên ảnh PNG thừa**: Tệp `public/assets/figma/san-pham/hero-bg.png` (~2.99 MB) tồn dư từ bước export ban đầu không còn được tham chiếu trong codebase hay DB (`BANNER_PRODUCT_HERO` đang dùng `/assets/figma/san-pham/hero-bg.webp`).

Phần nâng cấp trang **Chi tiết sản phẩm (PDP `Product/Show.vue`)** được tách sang một OpenSpec change riêng để giữ phạm vi change này gọn, an toàn và bám sát `Home-First Incremental`.

## What Changes

1. **Cập nhật Delta Spec `specs/product-catalog/spec.md`**:
   - Chuyển đặc tả Trang Danh sách Sản phẩm từ HH Maker Option 3 (`Node 2346:669`) sang Thành Khang Tecsat (`Node 979:30355`).
   - Xác định rõ chuẩn Hero Banner (`BANNER_PRODUCT_HERO`), Sidebar Filter 3 tầng (`264px`, giữ nguyên `4` danh mục gốc DB Bước 1, L1 không con `gap-2` vs L2 `justify-between`), Product Grid `856px` (`3×3` card `264×371px`), Pagination `979:30474` (không Prev/Next), `EmptyState` & Zero-Fallback, Mobile Touch Targets `>= 44px`, song ngữ VI/EN và dọn `hero-bg.png`.
   - Giữ nguyên đặc tả PDP `Product/Show.vue` (tách việc đồng bộ giao diện PDP sang change riêng).
2. **Hoàn thiện UI & Responsive (`ProductHeroBanner.vue`, `ProductFilter.vue`, `ProductPagination.vue`, `Product/Index.vue`)**:
   - `ProductPagination.vue`: Gỡ 2 nút Prev/Next, giữ chuẩn Figma `979:30474` trên Desktop (`40×40px`, `gap-2`, `rounded-[4px]`, tối đa 6 ô `280px`), nâng kích thước vùng chạm Mobile (`< sm`) lên `44×44px` (`w-11 h-11 sm:w-[40px] sm:h-[40px]`).
   - `ProductFilter.vue`: Chỉnh nhánh L1 không con (`979:30401`, `979:30430`) sang `justify-start gap-2` (checkbox cách text `8px`), giữ `justify-between` cho L2 (`979:30411`); nâng vùng chạm Mobile (`< lg`) lên `>= 44px` cho L1/L2 checkboxes và nút chevron toggle (`w-11 h-11 lg:w-5 lg:h-5`).
   - `ProductHeroBanner.vue`: Chuẩn hóa class khóa tỷ lệ `aspect-[390/240] md:aspect-[1440/252] min-h-[220px] xl:min-h-0`.
   - `Product/Index.vue`: Nâng vùng chạm Mobile (`< lg`) lên `>= 44px` cho nút `"Bộ lọc"`, nút `"Sắp xếp theo"` & dropdown options, nút `"Đặt lại tất cả"`, và nút đóng `×` trên Active Filter Chips; kiểm chứng `EmptyState` cho cả DB trống và kết quả lọc rỗng.
3. **Dọn dẹp asset thừa & Kiểm chứng toàn diện**:
   - Xóa tệp `public/assets/figma/san-pham/hero-bg.png` (giữ `hero-bg.webp` và các icon SVG).
   - Chạy `yarn route`, `yarn frontend:build`, `graphify . --code-only` + cập nhật `GRAPH_REPORT.md`, PHPUnit `ProductsPageCmsTest`, tạo & chạy Playwright E2E spec cho `/san-pham` & `/en/products` (Desktop `1440×900`, Mobile `375×812` / `390×844`) và kiểm tra regression Trang Chủ `/`.

## Capabilities

### New Capabilities
<!-- Không thêm capability mới; cập nhật trực tiếp trên capability product-catalog -->

### Modified Capabilities
- `product-catalog`: Chuyển đổi đặc tả Trang Danh sách Sản phẩm từ HH Maker Option 3 (`Node 2346:669`) sang Thành Khang Tecsat (`Node 979:30355`) với Hero Banner `BANNER_PRODUCT_HERO` (`aspect-[390/240] md:aspect-[1440/252] xl:min-h-0`), Sidebar Filter 3 tầng (`264px`, giữ `4` danh mục DB Bước 1, L1 không con `gap-2` vs L2 `justify-between`), Lưới 3×3 (`856px`, card `264×371px`), Pagination `979:30474` (`280×40px` không Prev/Next), Mobile Touch Targets `>= 44px`, `EmptyState` & Zero-Fallback, gỡ bỏ `mockProducts` và Banner CTA cuối trang cũ của listing.

## Impact

- **Frontend Vue**:
  - `resources/Frontend/js/Pages/Product/Index.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductHeroBanner.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue`
- **Tài nguyên tĩnh**: Xóa `public/assets/figma/san-pham/hero-bg.png` trong bước `/opsx-apply` (giữ nguyên `hero-bg.webp` và toàn bộ file SVG).
- **Tests & Kiến trúc AST**:
  - `tests/Feature/ProductsPageCmsTest.php`
  - Playwright E2E spec mới cho Trang Danh sách Sản phẩm Thành Khang + regression Trang Chủ `/`
  - `graphify-out/graph.json`, `graphify-out/GRAPH_REPORT.md`
- **OpenSpec**: Đồng bộ vào `openspec/specs/product-catalog/spec.md` khi `/opsx-archive`.

## Phạm vi an toàn (Strict Safe Guard)

- **Tách biệt PDP (`Product/Show.vue`)**: KHÔNG chỉnh sửa `resources/Frontend/js/Pages/Product/Show.vue` hay các component dành riêng cho PDP (`ProductGallery.vue`, `VariantSelector.vue`, `SpecTable.vue`, `RelatedProducts.vue`, `ProductCtaBanner.vue`, `SpecModal.vue`) trong phạm vi change này.
- **Bảo toàn Database & Seeder**: KHÔNG chạy `migrate:fresh`, `DROP`, `TRUNCATE`; giữ nguyên `4` danh mục gốc và `103` sản phẩm hiện có từ `ThanhKhangProductCatalogSeeder` (Bước 1) cùng bản ghi `BANNER_PRODUCT_HERO` của `ThanhKhangProductHeroSeeder`.
- **An toàn xóa file**: Chỉ xóa duy nhất `public/assets/figma/san-pham/hero-bg.png` khi thực thi checklist trong `/opsx-apply` (tuyệt đối không xóa ở bước lập OpenSpec và không đụng `hero-bg.webp` hay bất kỳ file SVG nào).
- **Cô lập Layout & Trang Chủ**: KHÔNG chỉnh sửa `MainLayout.vue`, `Header.vue`, `Footer.vue`, `tailwind.config.js`, `app.scss` hay các component thuộc `Pages/Home/`.
