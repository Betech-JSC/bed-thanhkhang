# Proposal: Dựng Trang Tài liệu - Danh mục Chuẩn Figma & CMS-First (`feat-page-documents`)

## Why

Trang **Tài liệu - Danh mục** (`/tai-lieu` và `/en/documents`, điều phối bởi `App\Http\Controllers\Frontend\DocumentController@index` render `resources/Frontend/js/Pages/Documents/Index.vue`) hiện mới ở trạng thái bản nháp từ `setup-core-from-home` (tham chiếu Figma cũ `1006:2634`), tồn tại các sai lệch kỹ thuật cần chuẩn hóa theo thiết kế Figma mới `1037:33817` (`Tài liệu - danh mục`):
1. **Kiến trúc Component & Layout**: Chưa tách thành các component section độc lập (`<script setup>` + `defineProps`) theo chuẩn `feat-page-about`; đang khai báo Options API thay vì `<script>` (`layout: MainLayout`) + `<script setup>`.
2. **Banner Aspect-Ratio & Ảnh Figma mới**: Hero Banner hiện dùng `min-h-[212px] md:h-[252px]` và ảnh dự phòng cũ `/assets/images/homepage/hero-banner.webp` của HH Maker thay vì ảnh gốc Figma `1037:33843` và chưa khóa `aspect-[W_mb/H_mb] md:aspect-[W_dt/H_dt]` kèm `object-cover`. Banner CTA Section 3 chưa khóa `aspect-ratio` theo tỷ lệ `1440×362` trên Desktop và dùng sai hệ màu 2 nút CTA (đang dùng `#005EA4` / `#C9E0FF` thay vì `#FAFAFA` nền trắng chữ xanh và viền `#FAFAFA` trong Figma `1059:34497`–`1059:34498`).
3. **Spacing Mobile**: Section 2 đang dùng `py-10 md:py-16` và `gap-5 md:gap-6` thay vì chuẩn `py-8 md:py-16`, `px-4 md:px-8`, `gap-3 md:gap-6`.
4. **CMS Data Binding (Read-Only, Không sửa DB khi chưa confirm)**:
   - DB hiện có **4 danh mục gốc `product_categories`** từ Bước 1 (`image = null` ở bảng `product_categories`), trong khi `DocumentController@index()` chưa có cơ chế đọc ảnh fallback từ sản phẩm con cháu đang hoạt động (như `HomeController@index()`), và giao diện nháp đang chèn thêm hộp `EmptyState` lớn phía trên lưới danh mục dù Figma `1037:33853` không có khối này.
   - Các dữ liệu chưa có trong DB (`BANNER_DOCUMENT_HERO`, `BANNER_DOCUMENT_CTA`, bài viết `type = DOCUMENT/RESOURCE/CATALOGUE`, danh mục thứ 5–6 `Quạt BLDC` & `Xích công nghiệp`, bản dịch EN cho `description` của 4 `product_categories`) sẽ được **ghi nhận rõ ràng để người dùng xác nhận** trước khi quyết định viết migration/seeder hoặc bổ sung module CMS.

## What Changes

Triển khai tuần tự **từ trên xuống dưới (Stage 1 → Stage 2 → Stage 3 → Stage 4 → Stage 5)** — hoàn tất trọn vẹn export tài nguyên + code của Section phía trên trước khi chuyển sang Section tiếp theo bên dưới:

1. **Stage 1 — Section 1: Hero Banner (`1037:33842`, `1440×252`)**:
   - Export ảnh nền gốc Figma `1037:33843` vào `public/assets/images/documents/document-hero-banner.png`.
   - Dựng `resources/Frontend/js/Pages/Documents/Components/DocumentHeroSection.vue` khóa tỷ lệ `aspect-[375/280] min-h-[260px] md:aspect-[1440/252] md:min-h-[220px] xl:min-h-0` với `<picture>` + `w-full h-full object-cover object-center`, lớp phủ gradient `270deg`, container `.app-container-narrow` (`px-4 md:px-8`), breadcrumb `Trang chủ / Tài liệu`, `<h1>` và mô tả.
   - Chuyển đổi khung `resources/Frontend/js/Pages/Documents/Index.vue` sang `<script>` (`layout: MainLayout`) + `<script setup>` (`defineProps`), giữ cấu trúc nền sáng `bg-[#FAFAFA] text-[#4B5565] overflow-x-clip`.
2. **Stage 2 — Section 2: Catalogue Category Directory (`1037:33853`, `1440×535`)**:
   - Nâng cấp `app/Http/Controllers/Frontend/DocumentController.php` (chỉ đọc DB, không sửa schema/data): bổ sung chuẩn hóa URL cục bộ (`normalizeLocalAssetUrl`), đọc ảnh đại diện danh mục theo cơ chế `HomeController` (ưu tiên `cat->image`, fallback sang ảnh sản phẩm active đầu tiên thuộc nhánh con cháu), sinh URL `/san-pham?category=<slug>` / `/en/products?category=<slug>`, và đọc `ctaBanner` (`BANNER_DOCUMENT_CTA` / `BANNER_GLOBAL_CTA`) + `MetaPage` nếu có.
   - Kiểm tra trực quan header section `1037:33855` trên Figma và dựng `resources/Frontend/js/Pages/Documents/Components/DocumentDirectorySection.vue` (`py-8 md:py-16`, `.app-container-narrow` `px-4 md:px-8`, lưới `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6`), bind dữ liệu thật `documents` hoặc `product_categories` qua `defineProps` (tuyệt đối 0 mảng mock tĩnh).
3. **Stage 3 — Section 3: Document Support CTA Banner (`1037:33867`, `1440×442`) & i18n**:
   - Tái sử dụng tài nguyên họa tiết xích tải cục bộ `public/assets/images/homepage/contact-chain-deco.png` (trùng `imageRef: 5166b1ede703948c920eb896238db7d5d78b34b2`).
   - Dựng `resources/Frontend/js/Pages/Documents/Components/DocumentCtaBanner.vue` khóa tỷ lệ `aspect-[375/340] min-h-[320px] md:aspect-[1440/362] md:min-h-[300px] xl:min-h-0`, hiển thị 2 nút chuẩn Figma `1059:34497` (`bg-[#FAFAFA] text-[#005EA4]`) và `1059:34498` (`border-[#FAFAFA] text-[#FAFAFA]`), liên kết động tới `/lien-he` (`/en/contact`) và `tel:${hotline}` từ `$page.props.global`.
   - Bổ sung từ khóa dịch tiếng Anh còn thiếu vào `resources/Frontend/lang/en.json`.
4. **Stage 4 — Build Gate & Graphify AST Sync**:
   - Chạy `yarn frontend:build`, kiểm tra cú pháp PHP (`php -l`) và chạy `graphify . --code-only`.
5. **Stage 5 — Playwright Visual QA**:
   - Kiểm tra thực tế `/tai-lieu` và `/en/documents` trên Desktop (`1440×900`) và Mobile (`375×812`): 0 lỗi console, 0 ảnh vỡ, 0 tràn ngang, kiểm tra tỷ lệ banner và chụp screenshots bằng chứng.

## Capabilities

### New Capabilities
- `documents-page`: Hệ thống Trang Tài liệu - Danh mục (`/tai-lieu` & `/en/documents`) chuẩn Figma `1037:33817`, bọc trong `Layouts/MainLayout.vue`, khóa banner `aspect-ratio` Desktop & Mobile, chuẩn spacing mobile (`py-8 md:py-16`, `px-4 md:px-8`, `gap-3 md:gap-6`), và bind 100% dữ liệu động từ `DocumentController@index()` qua `defineProps` (zero mock arrays).

### Modified Capabilities
<!-- Không chỉnh sửa capability hiện có trong openspec/specs/ -->

## Impact

- **Tài nguyên tĩnh**: `public/assets/images/documents/document-hero-banner.png` (từ Figma `1037:33843`), tái sử dụng `public/assets/images/homepage/contact-chain-deco.png`.
- **Backend Controller (chỉ đọc)**: `app/Http/Controllers/Frontend/DocumentController.php`.
- **Frontend Vue & i18n**:
  - `resources/Frontend/js/Pages/Documents/Index.vue`
  - `resources/Frontend/js/Pages/Documents/Components/{DocumentHeroSection,DocumentDirectorySection,DocumentCtaBanner}.vue`
  - `resources/Frontend/lang/en.json`
- **AST**: `graphify-out/graph.json`, `graphify-out/GRAPH_REPORT.md`.

## Phạm vi an toàn (Strict Safe Guard)

- **KHÔNG xóa file** (`rm` / `Remove-Item`), giữ nguyên các file hiện hữu khác trong dự án.
- **KHÔNG sửa database, KHÔNG viết/chạy migration hoặc seeder** khi chưa có xác nhận của người dùng về việc đẩy ảnh banner vào migration và phương án xử lý các module/dữ liệu CMS còn thiếu.
- **Thực thi tuần tự nghiêm ngặt**: Xong hoàn toàn Stage 1 (export + code Hero) mới sang Stage 2 (Directory), xong Stage 2 mới sang Stage 3 (CTA Banner).
