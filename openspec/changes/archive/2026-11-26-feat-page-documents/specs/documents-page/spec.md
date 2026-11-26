# Spec: Trang Tài liệu - Danh mục (`documents-page`)

## Purpose

Cung cấp trang Tài liệu - Danh mục tại `/tai-lieu` (Tiếng Việt) và `/en/documents` (Tiếng Anh) khớp 100% thiết kế Figma `1264:49095`, bọc trong vỏ giao diện `Layouts/MainLayout.vue`, khóa tỷ lệ banner Desktop/Mobile, chuẩn hóa spacing mobile, kết nối dữ liệu CMS động qua `Sliders` + `ProductCategories` + `Post`/`Config` theo chính sách Zero-Fallback và khởi tạo dữ liệu tự động qua Migration/Seeder idempotent.

## ADDED Requirements

### Requirement: Bọc trang trong `Layouts/MainLayout.vue` và chuẩn hóa Vue 3 `<script setup>`
Trang `resources/Frontend/js/Pages/Documents/Index.vue` MUST sử dụng `Layouts/MainLayout.vue` (khai báo `layout: MainLayout` trong `<script>` kèm `<script setup>` với `defineProps`) để hiển thị đúng 1 Header (sticky, mục `TÀI LIỆU`/`DOCUMENTS` active) và 1 Footer mà không tạo 2 thanh cuộn (`overflow-x: clip`).

#### Scenario: Truy cập `/tai-lieu` và `/en/documents` hiển thị đủ `MainLayout`
- **WHEN** người dùng truy cập `/tai-lieu` hoặc `/en/documents` trên Desktop và Mobile
- **THEN** trang hiển thị đúng 1 Header (sticky `top: 0` khi cuộn) và 1 Footer của `MainLayout.vue`, `overflow-x: clip` trên `html`/`body`, `0` lỗi console/runtime, và `scrollWidth <= innerWidth`.

### Requirement: Khóa Banner Aspect-Ratio & Ảnh `object-cover`
Mọi banner trên trang MUST gán `aspect-[W_mb/H_mb] md:aspect-[W_dt/H_dt]` bám sát kích thước Figma `1264:49095` và ảnh nền bên trong MUST dùng `w-full h-full object-cover object-center`.

#### Scenario: Section 1 Hero Banner & Section 3 CTA Banner giữ đúng tỷ lệ trên Desktop và Mobile
- **WHEN** hiển thị trang ở Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** Hero Banner (`1264:49120`) giữ kích thước `1440×252` (`aspect-[375/280] md:aspect-[1440/252]`) với ảnh `document-hero-banner.png` (`naturalWidth > 0`, `object-cover`), và CTA Banner (`1264:49145`) giữ kích thước `1440×362` (`aspect-[375/340] md:aspect-[1440/362]`, khoảng hở đáy `80px` Desktop / `32px` Mobile) không bị cắt chữ hoặc tràn khung.

### Requirement: Spacing & Box-Model Pixel-Perfect chuẩn Figma `1264:49095`
Trang MUST áp dụng chuẩn spacing responsive (`py-8 md:py-16`, `.app-container-narrow` `px-4 md:px-8`, `gap-3 md:gap-6`) và khóa chính xác kích thước thẻ `368×161px`, thumbnail `119×119px` (`aspect-square`), gap nội dung `4px`, cùng nút CTA `"Xem tất cả tài liệu"` (`1329:53128`, `gap-6 md:gap-[40px]`).

#### Scenario: Đo kích thước hộp trên Desktop (`1440×900`) và Mobile (`375×812`)
- **WHEN** kiểm tra DOM thực tế trên `/tai-lieu` và `/en/documents`
- **THEN** trên Desktop `1440px` mỗi thẻ danh mục đạt đúng `368×161px`, thumbnail `119×119px`, gap tiêu đề–mô tả `4px`, nút `"Xem tất cả tài liệu"` căn giữa dưới lưới với khoảng cách `40px` trỏ về `/san-pham` (`/en/products`); trên Mobile `375px` Section 2 đạt `py: 32px`, `px: 16px`, `gridGap: 12px` và không tràn ngang.

### Requirement: CMS Data Binding, Migration Idempotent & Chính sách Zero-Fallback
`DocumentController@index()` và các component Vue MUST nhận dữ liệu động từ DB (`BANNER_DOCUMENT_HERO`, `BANNER_DOCUMENT_CTA`, `documents` từ `Post`/`settings`, `product_categories` kèm ảnh fallback từ sản phẩm active nhánh con cháu và mô tả EN chuẩn), tuyệt đối KHÔNG dùng `?? 'fallback text'` trong Controller, KHÔNG dùng mảng mock tĩnh hoặc fallback che giấu DB rỗng, đồng bộ Cache Invalidation trên cả `Product` và kiểm tra thẻ hreflang `tai-lieu` <-> `en/documents`.

#### Scenario: Khởi tạo banner & mô tả EN tự động qua Migration/Seeder không phá hủy
- **WHEN** chạy `php artisan migrate` trên môi trường triển khai
- **THEN** `BANNER_DOCUMENT_HERO` và `BANNER_DOCUMENT_CTA` được nạp tự động (chỉ khi `count() === 0`), 4 danh mục gốc `product_categories` có mô tả tiếng Anh chuẩn trên `/en/documents`, và việc sửa `Product`, `ProductCategory`, `Slider` hoặc `Config` lập tức xóa cache `documents_index_{locale}`.

#### Scenario: DB rỗng hoàn toàn trả về Zero-Fallback và hiển thị EmptyState
- **WHEN** `sliders`, `posts`, `product_categories`, `settings`, `meta_pages` đều trống (`0` bản ghi)
- **THEN** `DocumentController@index()` trả về `heroBanner: null`, `documents: []`, `product_categories: []`, `ctaBanner: null`, `seo: null`, và `Documents/Index.vue` ẩn các section rỗng để hiển thị `<EmptyState variant="dark" />` điều hướng tới trang Liên hệ.
