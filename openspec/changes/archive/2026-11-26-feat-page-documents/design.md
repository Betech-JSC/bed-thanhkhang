# Design: Dựng Trang Tài liệu - Danh mục (`feat-page-documents`)

## Context

- **Route**: `routes/frontend.php:73` (`GET /tai-lieu` `vi.documents.index`, `GET /en/documents` `en.documents.index`) → `App\Http\Controllers\Frontend\DocumentController@index`.
- **Layout Shell**: `resources/Frontend/js/Layouts/MainLayout.vue` (đã hoàn thiện ở Bước 2, `Header.vue` đã highlight mục `TÀI LIỆU` khi route là `documents.index` hoặc path `/tai-lieu`, `/en/documents`; `app.js` tự fallback `MainLayout`, nhưng `Documents/Index.vue` sẽ khai báo tường minh `layout: MainLayout` trong block `<script>` kết hợp `<script setup>` theo chuẩn `About/Index.vue`).
- **Figma Desktop**: `1037:33817` (`Tài liệu - danh mục`, `1440×1818`). Chưa có Frame Mobile riêng trên Figma nên áp dụng quy chuẩn Mobile Trang Chủ Official (`1190:6335`) và yêu cầu spacing mobile của dự án (`py-8 md:py-16`, `px-4 md:px-8`, `gap-3 md:gap-6`).

## Đối chiếu Graphify AST & Phân tách Component

Kiểm tra `graphify-out/graph.json`:
- Tái sử dụng 100%: `MainLayout.vue`, `Header.vue`, `Footer.vue`, `EmptyState.vue`.
- Không tái sử dụng `ProductCtaBanner.vue` / `ServiceHero.vue` (thuộc giao diện tối `#111322` cũ) hoặc `ContactSection.vue` (form 2 cột `757px`).
- Tái cấu trúc `Pages/Documents/Index.vue` thành 3 component section theo đúng cấu trúc `feat-page-about`:

| Thành phần | File | Figma Node | Tỷ lệ / Spacing |
| :--- | :--- | :--- | :--- |
| **Root Page** | `resources/Frontend/js/Pages/Documents/Index.vue` | `1037:33817` | Bọc `layout: MainLayout`, `<script setup>` + `defineProps` |
| **Section 1: Hero Banner** | `resources/Frontend/js/Pages/Documents/Components/DocumentHeroSection.vue` | `1037:33842` | `aspect-[375/280] min-h-[260px] md:aspect-[1440/252] md:min-h-[220px] xl:min-h-0`, `.app-container-narrow` (`px-4 md:px-8`) |
| **Section 2: Catalogue Directory** | `resources/Frontend/js/Pages/Documents/Components/DocumentDirectorySection.vue` | `1037:33853` | `py-8 md:py-16`, `.app-container-narrow` (`px-4 md:px-8`), `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6`, thumbnail `aspect-square w-[96px] sm:w-[119px]` |
| **Section 3: Support CTA Banner** | `resources/Frontend/js/Pages/Documents/Components/DocumentCtaBanner.vue` | `1037:33867` | Outer `pb-8 md:pb-16 lg:pb-[80px]`, Banner `aspect-[375/340] min-h-[320px] md:aspect-[1440/362] md:min-h-[300px] xl:min-h-0` |

## Decisions

### 1. Không tự ý sửa DB / chạy Migration khi chưa được người dùng xác nhận
- Theo yêu cầu ở mục 4 (`note lại những cái chưa có trong DB và confirm việc đẩy ảnh banner vào migration`), Change này **chỉ nâng cấp phần đọc dữ liệu an toàn** trong `DocumentController@index()` và dựng đầy đủ giao diện 3 section Vue 3:
  - Giữ nguyên truy vấn `heroBanner` (`Slider::getByPosition('BANNER_DOCUMENT_HERO')`), `documents` (`Post` / `settings.documents`), `product_categories` (`ProductCategory::active()->whereParent()`).
  - Bổ sung đọc ảnh sản phẩm đại diện (first active descendant product) khi `ProductCategory->image` trong DB đang `null` (khớp 100% logic của `HomeController.php:290-312`), chuẩn hóa URL cục bộ `normalizeLocalAssetUrl`, và thêm `ctaBanner` (`BANNER_DOCUMENT_CTA` / `BANNER_GLOBAL_CTA` nếu có trong DB) + `MetaPage` SEO lookup.
- Với phần Banner Hero (`1037:33842`) và CTA Banner (`1037:33867`), component vẫn ưu tiên bind prop `heroBanner` / `ctaBanner` từ `defineProps`; khi DB chưa có row slider tương ứng (`null`), fallback sang ảnh đã export cục bộ (`/assets/images/documents/document-hero-banner.png` & `/assets/images/homepage/contact-chain-deco.png`) và chuỗi `tt(...)` đơn lẻ (tuyệt đối không tạo mảng mock danh mục).

### 2. Thực thi tuần tự từng Section (Stage 1 → Stage 2 → Stage 3)
- Bắt buộc hoàn tất export tài nguyên + code của Section trên trước khi bắt đầu export/code Section tiếp theo bên dưới.
- Chỉ chạy `yarn frontend:build`, `graphify . --code-only` và Playwright Visual QA sau khi hoàn tất cả 3 Section.
