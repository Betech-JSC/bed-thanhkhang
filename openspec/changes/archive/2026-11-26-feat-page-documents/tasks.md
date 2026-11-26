# Tasks: Dựng Trang Tài liệu - Danh mục (`feat-page-documents`)

> **Quy tắc bắt buộc**: Thực thi **tuần tự nghiêm ngặt từ trên xuống dưới** (`Stage 1 → Stage 2 → Stage 3 → Stage 4 → Stage 5 → Stage 6`). Xong trọn vẹn từng section (export + code) rồi mới làm section kế tiếp bên dưới.

## 1. Stage 1 — Section 1: Hero Banner (`1037:33842` / `1264:49120`) & Khung `Documents/Index.vue`

- [x] 1.1 Dùng `download_figma_images` xuất ảnh nền Hero Banner Figma node `1037:33843` lưu vào `public/assets/images/documents/document-hero-banner.png`.
- [x] 1.2 Tạo `resources/Frontend/js/Pages/Documents/Components/DocumentHeroSection.vue` (khóa tỷ lệ `aspect-[375/280] min-h-[260px] md:aspect-[1440/252] md:min-h-[220px] xl:min-h-0`, `<picture>` + `w-full h-full object-cover object-center`, `.app-container-narrow` `px-4 md:px-8`) và chuyển `resources/Frontend/js/Pages/Documents/Index.vue` sang `<script>` (`layout: MainLayout`) + `<script setup>` (`defineProps`).

## 2. Stage 2 — Section 2: Catalogue Category Directory (`1037:33853` / `1264:49131`) & Controller Binding

- [x] 2.1 Kiểm tra trực quan Section 2 trên Figma và nâng cấp `app/Http/Controllers/Frontend/DocumentController.php` (chỉ đọc DB): chuẩn hóa URL cục bộ `normalizeLocalAssetUrl`, fallback ảnh danh mục từ sản phẩm active nhánh con cháu, sinh `url` `/san-pham?category=<slug>`, đọc `ctaBanner`, và đồng bộ `MetaPage` SEO.
- [x] 2.2 Tạo `resources/Frontend/js/Pages/Documents/Components/DocumentDirectorySection.vue` (`py-8 md:py-16`, `.app-container-narrow` `px-4 md:px-8`, `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3 md:gap-6`, thumbnail `aspect-square` + `object-cover`, render thẳng từ `documents` hoặc `product_categories` qua `defineProps`, `EmptyState` khi rỗng, CẤM mảng mock tĩnh) và gắn vào `Documents/Index.vue`.

## 3. Stage 3 — Section 3: Document Support CTA Banner (`1037:33867` / `1264:49145`) & i18n

- [x] 3.1 Xác thực tài nguyên họa tiết xích tải cục bộ `public/assets/images/homepage/contact-chain-deco.png`, tạo `resources/Frontend/js/Pages/Documents/Components/DocumentCtaBanner.vue` (khóa tỷ lệ `aspect-[375/340] min-h-[320px] md:aspect-[1440/362] md:min-h-[300px] xl:min-h-0`, `pb-8 md:pb-16 lg:pb-[80px]`, 2 nút chuẩn Figma `#FAFAFA`/`#005EA4` và viền `#FAFAFA`, liên kết động `/lien-he` & `tel:${hotline}`), gắn vào `Documents/Index.vue`, và bổ sung bản dịch EN vào `resources/Frontend/lang/en.json`.

## 4. Stage 4 — Build Gate & Graphify AST Sync

- [x] 4.1 Chạy `yarn frontend:build` (Client + SSR) và `php -l app/Http/Controllers/Frontend/DocumentController.php` đảm bảo `0` lỗi biên dịch.
- [x] 4.2 Chạy `graphify . --code-only` cập nhật `graphify-out/graph.json` & `GRAPH_REPORT.md` và xác thực `/graphify-out` nằm trong `.gitignore`.

## 5. Stage 5 — CMS Sync, Banner Migration/Seeder, Zero-Fallback & Pixel-Perfect (`1264:49095`)

- [x] 5.1 Đăng ký `BANNER_DOCUMENT_HERO` và `BANNER_DOCUMENT_CTA` trong `config/slider.php`; tạo `ThanhKhangDocumentBannerSeeder` + migration `2026_11_26_000006_seed_document_banners_data.php`; cập nhật mô tả EN cho 4 `product_categories` gốc trong `ThanhKhangProductCatalogSeeder` + migration `2026_11_26_000007_update_root_product_categories_en_descriptions.php` và chạy migrate idempotent an toàn.
- [x] 5.2 Triệt tiêu fallback text trong `DocumentController::resolveDocumentsSeo()`, sanitize `'#'` ở PHP, bổ sung `Cache::forget('documents_index_' . $locale)` trong `Product::clearProductCaches()`, thêm hreflang `tai-lieu` <-> `en/documents` trong `app.blade.php`, hoàn thiện Zero-Fallback + `EmptyState` tổng trên `Documents/Index.vue` & các section con, sửa lỗi `tt` shadowing trên Vite dev mode, và viết `tests/Feature/DocumentsPageCmsTest.php` (`27/27` tests PASS).
- [x] 5.3 Chuẩn hóa Pixel-Perfect theo Figma `1264:49095`: sửa `md:p-[20px]` cho card đạt đúng `368×161px`, `gap-1` (`4px`), `gap-1 md:gap-0` CTA Banner (`71px`), bỏ `actionLabel` thừa, bổ sung nút CTA `"Xem tất cả tài liệu"` (`1329:53128`, `gap-6 md:gap-[40px]`, `184×46px` VI Desktop) + khóa EN trong `en.json`, build xanh (`yarn frontend:build`), xác nhận `scan-pixel-perfect-traps.py` (`[+] ADD: 0 | [-] REMOVE: 0`) & `audit-cms-sync.py` (`8/8 PASS`).

## 6. Stage 6 — Nghiệm thu cuối bằng Playwright Visual QA (Desktop + Mobile)

- [x] 6.1 Kiểm chứng trên trình duyệt thật bằng Playwright tại `/tai-lieu` và `/en/documents` ở Desktop (`1440×900`) và Mobile (`375×812` & `390×844`): `0` lỗi console/network, `overflowX: clip` (`0px` tràn ngang), sticky header hoạt động, box-model `368×161px` & nút `184×46px` (`gap: 40px`) khớp 100% Figma `1264:49095`, 4 card EN hiển thị mô tả tiếng Anh chuẩn, và đính kèm screenshots bằng chứng.
