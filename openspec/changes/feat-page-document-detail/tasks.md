# Tasks: feat-page-document-detail

## Stage 1: OpenSpec & Backend / Admin CMS / Migration

- [x] Khởi tạo OpenSpec change `feat-page-document-detail` (`proposal.md`, `specs/document-library-page/spec.md`, `specs/documents-page/spec.md`, `design.md`, `tasks.md`) và kiểm tra hợp lệ bằng `openspec validate`.
- [x] Cập nhật `App\Models\Post\Post` (`TYPE_DOCUMENT`, `TYPE_LIST`, `clearPostCaches` xóa `documents_library_{locale}`) và `App\Models\Post\PostCategory` (`TYPE_DOCUMENT`, `TYPE_LIST`, `getDocuments()`, `clearPostCategoryCaches` xóa `documents_index_{locale}` & `documents_library_{locale}`), cùng `Slider::clearSliderCaches()` và `Config::clearBrandCaches()`.
- [x] Tạo Admin CRUD `App\Http\Controllers\Backend\DocumentController` (`admin.documents.*`) và `App\Http\Controllers\Backend\DocumentCategoryController` (`admin.document-categories.*`), đăng ký vào `routes/backend.php` và `resources/lang/vi/models.php`.
- [x] Tạo giao diện Admin Vue `resources/Backend/js/Pages/Documents/{Index,Form}.vue` (có chọn/upload file qua `FileManager` hoặc nhập link cho `download`, `categories` lọc từ `PostCategory::getDocuments()`, `SeoFields`), `DocumentCategories/{Index,Form}.vue`, và cập nhật `resources/Backend/js/Components/SidebarMain.vue`.
- [x] Khai báo route song ngữ `documents_library` (`/tai-lieu/thu-vien` & `/en/documents/library`) trong `resources/lang/{vi,en}/routes.php` và `routes/frontend.php`; cập nhật `$pageMap` & `$autoBreadcrumb` trong `resources/Frontend/views/app.blade.php` và SEO fallback trong `HandleInertiaFrontendRequests.php`.
- [x] Cập nhật `App\Http\Controllers\Frontend\DocumentController`: giữ nguyên `product_categories[i].url` ở `index()` và thêm `library_url` / `documents_library_url`; triển khai `library(Request $request)` trả dữ liệu CMS động (`heroBanner`, `ctaBanner`, `documentCategories`, `documents` phân trang 10 mục/trang, lọc danh mục + tìm kiếm, Zero-Fallback).
- [x] Tạo seeder idempotent `database/seeders/ThanhKhangDocumentLibrarySeeder.php` (6 danh mục + 33 tài liệu theo Figma `1264:49166`), đăng ký vào `DatabaseSeeder.php`, và tạo migration an toàn `database/migrations/2026_11_26_000008_seed_document_library_data.php` (`RoleSeeder::createPermissions()`, `down()` không phá hủy).
- [x] Chạy kiểm chứng Backend: `php artisan migrate --force`, `yarn route`, `yarn backend:build`, `php -l` trên toàn bộ file PHP mới/sửa, truy vấn DB xác nhận 6 danh mục + 33 tài liệu, và chạy `php artisan test tests/Feature/DocumentsPageCmsTest.php`.

## Stage 2: Frontend Section 1 — Hero Banner

- [x] Khởi tạo trang `resources/Frontend/js/Pages/Documents/Library.vue` dùng `MainLayout.vue`, dựng Section 1 (`Hero Banner`) binding `heroBanner` từ `BANNER_DOCUMENT_HERO` với aspect-ratio lock và Zero-Fallback.

## Stage 3: Frontend Section 2 — Technical Documents List

- [x] Export/chuẩn hóa SVG icons từ Figma (`search-sm`, `download-01`) vào `resources/Frontend/js/Components/Icons/` (hoặc asset dùng chung của Frontend) theo chuẩn không mất nét.
- [x] Dựng Section 2 trong `Documents/Library.vue`: Header bar + ô vuông `#005EA4` + ô tìm kiếm, hàng 6 tab danh mục + `"Tất cả (<total>)"`, lưới 2 cột desktop / 1 cột mobile (`gap-3 md:gap-6`, `py-8 md:py-16`, `px-4 md:px-8`), xử lý tải file động (`has_file`), `EmptyState` khi không có kết quả/DB trống, và thanh phân trang 10 mục/trang đồng bộ URL query (`?category=&search=&page=`).

## Stage 4: Frontend Section 3 — Document Support CTA Banner & Link Wiring

- [x] Dựng Section 3 (`Document Support CTA Banner`) dùng `ctaBanner` từ `BANNER_DOCUMENT_CTA` khóa aspect-ratio và Zero-Fallback.
- [x] Cập nhật `resources/Frontend/js/Pages/Documents/Index.vue` nối điều hướng tới trang `/tai-lieu/thu-vien` (`library_url` / `documents_library_url`).

## Stage 5: Build Gate & Graphify

- [x] Chạy `yarn frontend:build` và `yarn backend:build` thành công không lỗi.
- [x] Cập nhật bản đồ AST bằng `graphify . --code-only` sau khi thêm trang/controller/model mới.

## Stage 6: Feature Tests & Playwright Visual QA

- [x] Viết Feature Test kiểm chứng `DocumentController@library()` (dữ liệu thật, lọc danh mục, tìm kiếm, phân trang 10 mục/trang, song ngữ VI/EN, trạng thái DB trống Zero-Fallback, và kiểm tra Admin CRUD `admin.documents.*` / `admin.document-categories.*`).
- [x] Chạy Playwright Visual QA trên trình duyệt thật (Desktop `1440px`, Mobile `375px`, kiểm tra chuyển tab danh mục, search, pagination `1..4`, `/en/documents/library`, ảnh chụp màn hình đính kèm nghiệm thu).
