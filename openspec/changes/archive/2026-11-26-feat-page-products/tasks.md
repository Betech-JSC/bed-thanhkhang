# Tasks: Chuẩn hóa Trang Danh sách Sản phẩm Thành Khang (`feat-page-products`)

> **Lưu ý phạm vi**: Giữ nguyên `4` danh mục gốc DB Bước 1 và tách phần nâng cấp trang Chi tiết Sản phẩm (PDP `Product/Show.vue`) sang một OpenSpec change riêng. Tuyệt đối không chạy `/opsx-archive` khi chưa có đủ bằng chứng PASS và xác nhận nghiệm thu của người dùng.

## 1. Chuẩn hóa UI & Mobile Touch Targets Trang Danh sách Sản phẩm

- [x] 1.1 Cập nhật `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue` theo Figma `979:30474`: gỡ 2 nút Prev/Next (`< / >`), chuẩn hóa kích thước nút `w-11 h-11 sm:w-[40px] sm:h-[40px]` (`44×44px` trên Mobile, `40×40px` trên `sm:`/Desktop), `gap-2` (`8px`), `rounded-[4px]`, active `#005EA4` / `#FAFAFA`, inactive & `"..."` `#FFFFFF` / `#E3E8EF` / `#364152` (tổng `280px` cho 6 ô `1, 2, 3, 4, ..., 8`), kiểm chứng qua inspect DOM Desktop `40×40px` không có Prev/Next và Mobile `>= 44×44px`.
- [x] 1.2 Cập nhật `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue` theo Figma `979:30394`: giữ nguyên `4` danh mục gốc DB Bước 1; sửa nhánh `L1` không có con (`979:30401`, `979:30430`) sang `justify-start gap-2` (checkbox `14×14px` cách nhãn `8px`), giữ `justify-between` cho danh sách `L2` (`979:30411` trong `px-5 gap-3`); nâng vùng chạm Mobile (`< lg`) đạt `min-h-[44px] lg:min-h-[22px]` cho nút `L1`/`L2` và `w-11 h-11 lg:w-5 lg:h-5` cho nút chevron toggle (`979:30410`), kiểm chứng khoảng cách checkbox và bounding box Mobile `>= 44px`.
- [x] 1.3 Cập nhật `resources/Frontend/js/Pages/Product/Components/ProductHeroBanner.vue` theo Figma `979:30380`: chuẩn hóa class khóa tỷ lệ `aspect-[390/240] md:aspect-[1440/252] min-h-[220px] xl:min-h-0` với `<picture>` + `w-full h-full object-cover object-center` nạp động `BANNER_PRODUCT_HERO` và Zero-Fallback khi `heroBanner` rỗng, kiểm chứng chiều cao `252px` tại `1440×900` và tỷ lệ `390/240` trên Mobile.
- [x] 1.4 Cập nhật `resources/Frontend/js/Pages/Product/Index.vue`: nâng vùng chạm Mobile (`< lg`) đạt `>= 44px` cho nút mở `"Bộ lọc"` (`min-h-[44px]`), nút `"Sắp xếp theo"` (`min-h-[44px] lg:min-h-0 lg:h-[40px]`) & các `<button role="option">`, nút `"Đặt lại tất cả"` (`min-h-[44px] lg:min-h-0 lg:h-[40px]`), và các Active Filter Chips kèm nút xóa `×` (`min-h-[44px] lg:min-h-0 lg:h-[38px]`, nút `×` `min-w-[44px] min-h-[44px] lg:min-w-0 lg:min-h-0 lg:w-5 lg:h-5`); kiểm chứng `<EmptyState variant="light" />` hiển thị đúng ở cả DB trống và kết quả lọc rỗng.

## 2. Dọn dẹp Asset thừa, Build Gate & Graphify AST

- [x] 2.1 Xóa tệp ảnh thừa `public/assets/figma/san-pham/hero-bg.png` (~2.99 MB), bảo toàn `public/assets/figma/san-pham/hero-bg.webp` và các tệp SVG icon; kiểm chứng bằng `git grep` và tìm kiếm toàn repo cho `hero-bg.png` trả về `0` kết quả và tệp không còn trên đĩa.
- [x] 2.2 Chạy `yarn route` và `yarn frontend:build` (Client + SSR) xác nhận hoàn tất với exit code `0` và `0` lỗi biên dịch.
- [x] 2.3 Chạy `graphify . --code-only` cập nhật `graphify-out/graph.json` và `graphify-out/GRAPH_REPORT.md`; xác nhận `/graphify-out` nằm trong `.gitignore`.

## 3. Kiểm chứng Backend CMS, Playwright E2E & Regression Trang Chủ

- [x] 3.1 Chạy `php artisan test tests/Feature/ProductsPageCmsTest.php` xác nhận toàn bộ test cases PASS (Zero-Fallback, `ThanhKhangProductHeroSeeder` idempotent, song ngữ VI/EN, `MetaPage` override, Cache Invalidation).
- [x] 3.2 Tạo mới và chạy Playwright E2E spec kiểm chứng Trang Danh sách Sản phẩm mới (`/san-pham` và `/en/products`) trên Desktop (`1440×900`) và Mobile (`375×812` & `390×844`): xác nhận Hero Banner `BANNER_PRODUCT_HERO`, `4` danh mục gốc DB Bước 1, L1 không con `gap-2` vs L2 `justify-between`, Grid `3×3` (`264×371px`), Pagination `979:30474` (`280×40px`, không Prev/Next), Mobile touch targets `>= 44px`, `EmptyState` khi lọc không khớp, `0` lỗi console/network, `0px` tràn ngang (`scrollWidth <= innerWidth`), và lưu screenshots bằng chứng.
- [x] 3.3 Chạy kiểm tra regression Trang Chủ `/` (Desktop `1440×900` & Mobile `375×812`) bằng Playwright xác nhận Trang Chủ hoạt động bình thường, `0` lỗi console, `0` ảnh vỡ, `0px` tràn ngang.
- [x] 3.4 Trình bày đầy đủ raw output build/test/Playwright và screenshots bằng chứng để người dùng xác nhận nghiệm thu trước khi chạy `/opsx-archive`.
