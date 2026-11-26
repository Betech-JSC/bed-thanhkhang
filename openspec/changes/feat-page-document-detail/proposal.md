# Proposal: feat-page-document-detail

## Why

Trang Danh mục tài liệu hiện tại (`/tai-lieu` và `/en/documents`) đã hiển thị Banner đầu trang, lưới 4 nhóm tài liệu kỹ thuật theo danh mục sản phẩm và Banner CTA hỗ trợ kỹ thuật, nhưng chưa có trang **Thư viện tài liệu chi tiết** (`/tai-lieu/thu-vien` và `/en/documents/library`) theo thiết kế Figma (`nodeId: 1264:49166` — *Tài liệu - chi tiết*).

Ngoài ra, hệ thống Admin CMS hiện mới có module `Posts` (`POST`) và `PostCategories` (`POST`), chưa tách riêng module quản trị cho **Tài liệu kỹ thuật** (`TYPE_DOCUMENT`) và **Danh mục tài liệu** (`TYPE_DOCUMENT`) với trường tệp PDF / liên kết tải về (`download`), cũng như chưa có dữ liệu mẫu 6 danh mục + 33 tài liệu theo thiết kế Figma để vận hành end-to-end không hardcode.

## What Changes

1. **Backend Models & Cache Invalidation**:
   - Thêm `Post::TYPE_DOCUMENT = 'DOCUMENT'` và `PostCategory::TYPE_DOCUMENT = 'DOCUMENT'` cùng khai báo trong `TYPE_LIST`.
   - Thêm `PostCategory::getDocuments()` trả về danh sách danh mục tài liệu đang kích hoạt (phục vụ `select_multiple` ở Admin Form).
   - Cập nhật `clearPostCaches()`, `clearPostCategoryCaches()`, `Slider::clearSliderCaches()`, và `Config::clearBrandCaches()` để xóa cache `documents_library_{locale}` và `documents_index_{locale}` mỗi khi CMS thay đổi dữ liệu.

2. **Admin CMS CRUD Module**:
   - Tạo `App\Http\Controllers\Backend\DocumentController` (`admin.documents.*`, lọc & gán `type = Post::TYPE_DOCUMENT`) và `App\Http\Controllers\Backend\DocumentCategoryController` (`admin.document-categories.*`, lọc & gán `type = PostCategory::TYPE_DOCUMENT`) dùng `HasCrudActions`.
   - Đăng ký `Route::module(...)` trong `routes/backend.php` và bổ sung nhãn bảng trong `resources/lang/vi/models.php`.
   - Tạo giao diện Admin Vue `resources/Backend/js/Pages/Documents/{Index,Form}.vue` (gồm `title`, `description`, `content`, `categories`, trường `download` hỗ trợ chọn/upload PDF qua `FileManager` hoặc dán URL, `status`, `position`, `image`, `SeoFields`) và `DocumentCategories/{Index,Form}.vue`.
   - Thêm menu `"Tài liệu kỹ thuật"` và `"Danh mục tài liệu"` vào `resources/Backend/js/Components/SidebarMain.vue` (nhóm `"NỘI DUNG & THƯƠNG HIỆU"`).

3. **Frontend Routes, SEO & Controller**:
   - Khai báo route `documents_library` trong `resources/lang/vi/routes.php` (`tai-lieu/thu-vien`) và `resources/lang/en/routes.php` (`documents/library`), đăng ký `documents.library` trong `routes/frontend.php`.
   - Cập nhật `resources/Frontend/views/app.blade.php` (`$pageMap` hreflang & `$autoBreadcrumb`) và `app/Http/Middleware/HandleInertiaFrontendRequests.php` (SEO fallback cho route `documents.library`).
   - Cập nhật `App\Http\Controllers\Frontend\DocumentController`:
     - Ở `index()`: tách `whereIn('type', ['RESOURCE', 'CATALOGUE'])`, giữ nguyên `product_categories[i].url` để bảo toàn `DocumentsPageCmsTest.php`, đồng thời bổ sung `library_url` và `documents_library_url`.
     - Thêm `library(Request $request)`: đọc dữ liệu động từ CMS (`BANNER_DOCUMENT_HERO`, `BANNER_DOCUMENT_CTA`, 6 danh mục tài liệu kèm `documents_count`, toàn bộ tài liệu đang hoạt động, phân trang 10 mục/trang, lọc theo `category` và `search`), áp dụng Zero-Fallback khi DB trống.

4. **Seeder & Migration An Toàn**:
   - Tạo `ThanhKhangDocumentLibrarySeeder` (idempotent) seed 6 danh mục tài liệu (`TYPE_DOCUMENT`) và 33 tài liệu kỹ thuật (`TYPE_DOCUMENT`, song ngữ VI/EN; 10 mục đầu khớp chính xác tiêu đề Figma `1264:49226`, `download = null`).
   - Tạo migration `2026_11_26_000008_seed_document_library_data.php` gọi seeder an toàn (chỉ seed khi chưa có bản ghi `TYPE_DOCUMENT`) và đồng bộ `RoleSeeder::createPermissions()`; hàm `down()` để trống (non-destructive).

5. **Frontend Page & QA (các Stage tuần tự tiếp theo)**:
   - Dựng `resources/Frontend/js/Pages/Documents/Library.vue` theo từng section từ trên xuống dưới (Hero Banner -> Technical Documents List -> Document Support CTA Banner) sử dụng `MainLayout.vue`, export SVG icon chuẩn, Zero-Fallback & `EmptyState`.
   - Nối link từ `Documents/Index.vue` sang `/tai-lieu/thu-vien` (`library_url` / `documents_library_url`).
   - Chạy Build Gate (`yarn frontend:build`, `yarn backend:build`), cập nhật Graphify AST, chạy Feature Tests và Playwright Visual QA đa khung nhìn (Desktop 1440px, Mobile 375px, VI/EN).

## Capabilities

### New Capabilities
- `document-library-page`: Trang Thư viện tài liệu chi tiết (`/tai-lieu/thu-vien` & `/en/documents/library`), bộ lọc danh mục, tìm kiếm trực tiếp, phân trang 10 mục/trang, tải tài liệu động từ CMS và module Admin CMS `admin.documents.*` / `admin.document-categories.*`.

### Modified Capabilities
- `documents-page`: Mở rộng response props `DocumentController@index()` với `documents_library_url` và `product_categories[i].library_url` (giữ nguyên `product_categories[i].url`) để liên kết mượt từ `/tai-lieu` sang `/tai-lieu/thu-vien` mà không phá vỡ contract kiểm thử hiện hữu.

## Impact

- **Backend**: `app/Models/Post/Post.php`, `app/Models/Post/PostCategory.php`, `packages/bed-package-essentials/slider/src/Models/Slider.php`, `app/Models/Config.php`, `app/Http/Controllers/Backend/{DocumentController,DocumentCategoryController}.php`, `app/Http/Controllers/Frontend/DocumentController.php`, `app/Http/Middleware/HandleInertiaFrontendRequests.php`, `routes/{backend,frontend}.php`, `resources/lang/{vi,en}/routes.php`, `resources/lang/vi/models.php`.
- **Admin UI**: `resources/Backend/js/Pages/Documents/{Index,Form}.vue`, `resources/Backend/js/Pages/DocumentCategories/{Index,Form}.vue`, `resources/Backend/js/Components/SidebarMain.vue`.
- **Database**: `database/seeders/ThanhKhangDocumentLibrarySeeder.php`, `database/seeders/DatabaseSeeder.php`, `database/migrations/2026_11_26_000008_seed_document_library_data.php`.
- **Frontend UI & Views**: `resources/Frontend/views/app.blade.php`, `resources/Frontend/js/Pages/Documents/{Index,Library}.vue`, SVG assets.
- **Tests**: `tests/Feature/DocumentsPageCmsTest.php`, bổ sung Feature test cho trang `/tai-lieu/thu-vien` và Playwright Visual QA.

## Phạm vi an toàn (Strict Safe Guard)

- Không xóa bất kỳ file nào trong workspace.
- Không chạy `migrate:fresh`, `migrate:reset`, `DROP`, hay `TRUNCATE`. Hàm `down()` của migration mới giữ nguyên trạng dữ liệu hiện hữu.
- Seeder chạy có điều kiện guard (`count() === 0` với `TYPE_DOCUMENT`) để tuyệt đối không ghi đè dữ liệu Admin đã chỉnh sửa.
- Không hardcode nội dung fallback ở Backend/Frontend khi DB trống; mọi trạng thái không có dữ liệu đều render `EmptyState` rõ ràng.
