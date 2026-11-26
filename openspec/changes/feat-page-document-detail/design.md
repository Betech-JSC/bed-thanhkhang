# Technical Design: feat-page-document-detail

## 1. Khảo sát Kiến trúc hiện hữu (Graphify AST & Codebase)

Qua kiểm tra `graphify-out/GRAPH_REPORT.md` và mã nguồn thực tế:
- Hệ thống nội dung dạng bài viết dùng mô hình `Post` (`App\Models\Post\Post`) + `PostCategory` (`App\Models\Post\PostCategory`) qua bảng trung gian `post_ref_categories`, đã hỗ trợ đa ngôn ngữ (`HasTranslation` + `post_translations` / `post_category_translations`), `SeoFields` (`HasSeo`), `position`, `status`, và sẵn cột `download` trên `posts`.
- Trong Backend CMS, các controller chuẩn mở rộng bằng `App\Traits\HasCrudActions` và đăng ký qua `Route::module(...)` (`admin.<resource>.{index,form,store,destroy,restore}`), kết hợp đồng bộ quyền tự động ở `RoleSeeder::createPermissions()`.
- Ở Frontend `/tai-lieu`, `App\Http\Controllers\Frontend\DocumentController@index()` đang dùng `ProductCategory` (4 nhóm cấp 1: `linh-kien-thang-cuon`, `thang-nang-o-to`, `day-chuyen-dong-goi`, `bang-tai-hanh-ly`) và `Slider` positions `BANNER_DOCUMENT_HERO` / `BANNER_DOCUMENT_CTA`. Bộ test `tests/Feature/DocumentsPageCmsTest.php` khẳng định nghiêm ngặt:
  - Khi DB rỗng: `documents`, `product_categories`, `categories` đều là `[]`; `heroBanner`, `ctaBanner`, `seo` là `null`.
  - Khi DB có `ProductCategory`: `product_categories[0].url` PHẢI bằng `'/san-pham?category=linh-kien-thang-cuon'`.

## 2. Quyết định Thiết kế Kiến trúc

### 2.1. Tái sử dụng `Post` & `PostCategory` với `TYPE_DOCUMENT`
- Thay vì tạo thêm bảng database mới, thêm `public const TYPE_DOCUMENT = 'DOCUMENT'` vào cả `Post` và `PostCategory`.
- Ưu điểm:
  - Tái sử dụng 100% hạ tầng dịch song ngữ (`post_translations`, `post_category_translations`), SEO (`slug`, `seo_title`, `seo_description`, `seo_slug`...), quan hệ nhiều-nhiều `categories`, sắp xếp `orderByPosition()`, và cột `download` sẵn có trên bảng `posts`.
  - Hoàn toàn tương thích với `HasCrudActions` và `RuleGenerator`.
- Trong `PostCategory`, thêm `getDocuments()` trả collection danh mục `TYPE_DOCUMENT` đang `active` sắp xếp theo `position` tăng dần -> `id` tăng dần để `Field` `select_multiple` gọi qua `admin.helper.model-data`.

### 2.2. Tách biệt rạch ròi `/tai-lieu` (`index`) và `/tai-lieu/thu-vien` (`library`)
- **Bảo toàn 100% `DocumentsPageCmsTest.php`**:
  - Trong `DocumentController@index()`: đổi bộ lọc `Post` sang `->whereIn('type', ['RESOURCE', 'CATALOGUE'])` để 33 tài liệu `TYPE_DOCUMENT` của trang thư viện chi tiết không tràn vào prop `documents` của `/tai-lieu`.
  - Giữ nguyên `product_categories[i]['url'] = $productsBasePath . '?category=' . urlencode($slug)`.
  - Bổ sung `documents_library_url` (`/tai-lieu/thu-vien` hoặc `/en/documents/library`) ở top-level Inertia props và `library_url` trên từng item `product_categories[i]`.
- **Trang mới `/tai-lieu/thu-vien` & `/en/documents/library`**:
  - Named route: `vi.documents.library` và `en.documents.library` (khai báo trong `resources/lang/{vi,en}/routes.php` với key `documents_library`).
  - `DocumentController@library(Request $request)`:
    - Cache tập dữ liệu CMS gốc theo locale tại key `'documents_library_' . $locale` (gồm `heroBanner`, `ctaBanner`, `documentCategories`, `documents`).
    - Khi DB chưa có bản ghi `TYPE_DOCUMENT` hoặc `Slider`: trả `[]` / `null` (Zero-Fallback, không bịa nội dung mẫu trong controller).
    - Lọc theo `category` (slug), `search` (case-insensitive substring khớp `title` / `description` / `slug`), phân trang **10 mục / trang** (`PER_PAGE = 10`), clamp `page` hợp lệ trong `[1, last_page]`.
    - Hỗ trợ `wantsJson()` trả JSON đầy đủ hoặc `Inertia::render('Documents/Library', [...])`.
    - SEO lấy từ `MetaPage` cho `['/tai-lieu/thu-vien', 'tai-lieu/thu-vien']` (hoặc `['/en/documents/library', 'en/documents/library']`), kết hợp xử lý fallback tại `HandleInertiaFrontendRequests.php` ngoài nhánh `if ($slug)` (vì `documents.library` là static route không có `{slug}`), và bổ sung `$pageMap` + `$autoBreadcrumb` trong `resources/Frontend/views/app.blade.php`.

### 2.3. Admin CMS & Xử lý Upload/Link PDF (`download`)
- Tạo `Backend\DocumentController` (`getTable() => 'documents'`, `folder() => 'Documents'`, `beforeIndex` lọc `TYPE_DOCUMENT`, `beforeStore` gán `$request->merge(['type' => Post::TYPE_DOCUMENT])`) và `Backend\DocumentCategoryController` (`getTable() => 'document-categories'`, `folder() => 'DocumentCategories'`, `beforeIndex` lọc `TYPE_DOCUMENT`, `beforeStore` gán `$request->merge(['type' => PostCategory::TYPE_DOCUMENT])`).
- Về trường `download` ở `resources/Backend/js/Pages/Documents/Form.vue`:
  - Qua kiểm tra `FileManager.vue` và `InputUpload.vue`, `FileManager.vue` hỗ trợ upload & chọn file `application/pdf`, còn `Thumbnail.vue` dùng `<v-lazy-image>` thiên về ảnh/video. Vì vậy trong `Documents/Form.vue`, `download` được thiết kế gồm ô nhập URL trực tiếp (`text`) kết hợp nút `"Chọn / Tải tệp từ FileManager"` mở modal `<FileManager v-model:show="..." :multiple="false" @on-select="..." />`, tự điền `static_url` (chuẩn hóa đường dẫn `/static/...` hoặc URL đầy đủ) kèm nút xem thử và xóa liên kết.
- Cập nhật `clearPostCaches()`, `clearPostCategoryCaches()`, `Slider::clearSliderCaches()`, và `Config::clearBrandCaches()` cùng xóa `documents_library_{locale}` để chỉnh sửa ở CMS phản ánh tức thì ngoài Frontend.

### 2.4. Seeder Idempotent & Migration Không Phá Hủy
- `ThanhKhangDocumentLibrarySeeder`:
  - Khởi tạo 6 `PostCategory` (`TYPE_DOCUMENT`) tương ứng 6 tab trên Figma `1264:49211` với đầy đủ bản dịch `vi` và `en`:
    1. `Linh kiện thang cuốn` / `Escalator Spare Parts` (`linh-kien-thang-cuon`)
    2. `Thang nâng ô tô` / `Car Parking Lifts` (`thang-nang-o-to`)
    3. `Dây chuyền đóng gói` / `Packaging Lines` (`day-chuyen-dong-goi`)
    4. `Băng tải hành lý` / `Baggage Conveyors` (`bang-tai-hanh-ly`)
    5. `Tiết kiệm điện` / `Energy Saving` (`tiet-kiem-dien`)
    6. `Xích công nghiệp` / `Industrial Chains` (`xich-cong-nghiep`)
  - Khởi tạo 33 `Post` (`TYPE_DOCUMENT`, `status = ACTIVE`, `download = null`), gán danh mục chặt chẽ để tổng số đạt đúng `33` (trang 1–3 mỗi trang 10 mục, trang 4 có 3 mục). Trong đó 10 mục đầu tiên (`position` 1..10) giữ chính xác 100% 10 tiêu đề tiếng Việt từ Figma `1264:49226` và bản dịch tiếng Anh tương ứng; 23 mục còn lại (`position` 11..33) mang nội dung kỹ thuật chuyên ngành Thành Khang Tecsat phân bổ đồng đều qua tất cả 6 danh mục (mỗi tab đều có tài liệu thực tế để lọc).
  - Guard idempotent: chỉ seed khi `PostCategory::where('type', PostCategory::TYPE_DOCUMENT)->count() === 0 && Post::where('type', Post::TYPE_DOCUMENT)->count() === 0`, bảo đảm không ghi đè dữ liệu đã chỉnh sửa trong Admin.
- Migration `2026_11_26_000008_seed_document_library_data.php`: gọi `ThanhKhangDocumentLibrarySeeder` có guard và gọi `RoleSeeder::createPermissions()` nếu bảng `permissions` tồn tại; `down()` để trống.

### 2.5. Quy chuẩn Giao diện Frontend (Sẽ dựng ở Stage 2–4)
- Trang `resources/Frontend/js/Pages/Documents/Library.vue` kế thừa `MainLayout.vue`:
  - Section 1 (`Hero Banner`): giữ nguyên pattern & aspect ratio khóa theo `BANNER_DOCUMENT_HERO` (`1440x252`).
  - Section 2 (`Technical Documents List`): tiêu đề `"Thư viện tài liệu kỹ thuật tại Thành Khang"` + ô vuông nhỏ `#005EA4`, ô tìm kiếm `296x42px` icon `search-sm`, hàng tab lọc danh mục + `"Tất cả (33)"`, lưới 2 cột card `rounded-[10px] border-[#E3E8EF]` có nút tải icon `download-01` export từ Figma (chuyển 1 cột trên Mobile với `gap-3 md:gap-6`, `py-8 md:py-16`, `px-4 md:px-8`), thanh phân trang `1..4`.
  - Section 3 (`Document Support CTA Banner`): dùng `BANNER_DOCUMENT_CTA` khóa aspect ratio chuẩn Figma (`1440x362`).
  - Nối click từ `Documents/Index.vue` tới `/tai-lieu/thu-vien` (`documents_library_url` / `library_url`).
