# Spec: Document Library Page (`document-library-page`)

## Overview
Trang **Thư viện tài liệu chi tiết** (`/tai-lieu/thu-vien` ở locale `vi` và `/en/documents/library` ở locale `en`) cùng bộ module Admin CMS quản lý Tài liệu kỹ thuật (`admin.documents.*`) và Danh mục tài liệu (`admin.document-categories.*`). Toàn bộ tiêu đề, danh mục, số lượng, tệp tải về, banner và SEO đều lấy động từ CMS với quy tắc Zero-Fallback.

---

## ADDED Requirements

### Requirement: Shared Layout Chrome
Trang Thư viện tài liệu chi tiết MUST sử dụng `MainLayout.vue` (hoặc Inertia persistent layout mặc định của Frontend) để dùng chung Header, Navigation Bar và Footer toàn hệ thống, tuyệt đối không dựng lại Header/Footer cục bộ trong page component.

#### Scenario: Render within MainLayout
- **WHEN** người dùng truy cập `/tai-lieu/thu-vien` hoặc `/en/documents/library`
- **THEN** response Inertia render component `Documents/Library` kế thừa Header, Menu điều hướng và Footer chung từ `MainLayout.vue`, đồng thời thẻ `html` và route lang switcher phản ánh đúng liên kết song ngữ (`$pageMap`) và Breadcrumb JSON-LD (`$autoBreadcrumb`).

---

### Requirement: Banner Aspect-Ratio Lock
Section 1 (Hero Banner) và Section 3 (CTA Banner) của trang `/tai-lieu/thu-vien` MUST dùng chung vị trí slider CMS `BANNER_DOCUMENT_HERO` và `BANNER_DOCUMENT_CTA` như trang `/tai-lieu`, đồng thời khóa tỷ lệ khung hình nhất quán với thiết kế Figma (`1264:49191` cho Hero và `1264:49247` cho CTA) để không bị co dãn/méo ảnh giữa Desktop và Mobile.

#### Scenario: Shared CMS banners rendered with locked aspect ratio
- **WHEN** `BANNER_DOCUMENT_HERO` và `BANNER_DOCUMENT_CTA` đang kích hoạt trong bảng `sliders`
- **THEN** `DocumentController@library()` trả về `heroBanner` và `ctaBanner` từ chính 2 position trên (không tạo slot banner dư thừa), và giao diện giữ tỷ lệ khung hiển thị chuẩn trên Desktop (`1440px`) và Mobile (`375px`) khi có dữ liệu ảnh từ CMS.

#### Scenario: Missing banners in empty DB (Zero-Fallback)
- **WHEN** bảng `sliders` không có bản ghi `BANNER_DOCUMENT_HERO` hoặc `BANNER_DOCUMENT_CTA`
- **THEN** backend trả về `heroBanner: null` và/hoặc `ctaBanner: null`, tuyệt đối không chèn URL ảnh hardcoded hay text giả.

---

### Requirement: Responsive Spacing Normalization
Khoảng cách section và lưới tài liệu trên `/tai-lieu/thu-vien` MUST chuẩn hóa responsive (`py-8 md:py-16` hoặc tương đương theo design system, padding ngang `px-4 md:px-8` trong container chuẩn, gap lưới `gap-3 md:gap-6`) để bảo đảm vừa vặn trên màn hình nhỏ (`375px`) lẫn Desktop (`1440px`) mà không tràn ngang (`scrollWidth <= innerWidth`).

#### Scenario: Mobile and Desktop spacing compliance
- **WHEN** mở `/tai-lieu/thu-vien` trên viewport `375x812` và `1440x900`
- **THEN** lưới danh sách tài liệu tự chuyển từ 1 cột (Mobile) sang 2 cột (Desktop) với khoảng cách chuẩn hóa, không có tràn ngang (`document.documentElement.scrollWidth <= window.innerWidth`) và các nút tab/phân trang có kích thước chạm tối thiểu thân thiện thiết bị di động.

---

### Requirement: Dynamic CMS Binding, Filtering, Search & Pagination
Trang Thư viện tài liệu chi tiết MUST lấy danh sách danh mục (`PostCategory::TYPE_DOCUMENT`) và danh sách tài liệu (`Post::TYPE_DOCUMENT`) trực tiếp từ Database/CMS, hỗ trợ:
1. Tab `"Tất cả (<total>)"` (hoặc `"All (<total>)"`) và 6 tab danh mục tài liệu từ CMS (mỗi tab hiển thị số lượng tài liệu đang hoạt động thuộc danh mục đó hoặc lọc chính xác theo `slug`).
2. Ô tìm kiếm theo từ khóa tiêu đề/mô tả (`search`), đồng bộ query params (`?category=...&search=...&page=...`).
3. Phân trang cố định **10 tài liệu mỗi trang** (`10 items/page`), hỗ trợ chuyển trang và tự reset về trang 1 khi đổi tab danh mục hoặc từ khóa tìm kiếm.

#### Scenario: Initial load with seeded 6 categories and 33 documents
- **WHEN** DB đã có 6 `PostCategory` (`TYPE_DOCUMENT`, status active) và 33 `Post` (`TYPE_DOCUMENT`, status active)
- **THEN** trang `/tai-lieu/thu-vien` hiển thị tab `"Tất cả (33)"` đang active mặc định, 6 tab danh mục theo thứ tự `position`, trang 1 hiển thị tối đa 10 tài liệu đúng thứ tự Figma/position, và thanh phân trang hiển thị 4 trang (`1..4`).

#### Scenario: Filtering by category slug and keyword search
- **WHEN** người dùng chọn một tab danh mục (hoặc truy cập `/tai-lieu/thu-vien?category=<slug>`) và/hoặc nhập từ khóa vào ô tìm kiếm
- **THEN** danh sách tài liệu được lọc chính xác theo `category` và từ khóa `search`, tổng số trang và danh sách hiển thị được cập nhật tương ứng, nếu chuyển đổi bộ lọc thì `page` quay về `1`.

#### Scenario: JSON / Inertia parity and pagination clamp
- **WHEN** client gửi `page` vượt quá `last_page` hoặc gửi request với `Accept: application/json`
- **THEN** `DocumentController@library()` trả đầy đủ cấu trúc dữ liệu `documentCategories`, `documents` (hoặc `paginatedDocuments`), `filters`/`initialFilters`, `pagination` (`current_page`, `last_page`, `per_page`, `total`) với `current_page` nằm trong `[1, last_page]` khi có dữ liệu.

---

### Requirement: Document Download State Handling
Mỗi thẻ tài liệu MUST chỉ hiển thị liên kết/hành động tải thực tế khi tài liệu có `download` (`file_url` / `download_url`) hợp lệ trong CMS (`has_file === true`). Khi `download` đang là `null` hoặc rỗng, không được giả lập file PDF dummy.

#### Scenario: Document with file vs without file
- **WHEN** tài liệu có `download` hợp lệ trong CMS (ví dụ `/static/files/catalogue.pdf` hoặc URL HTTPS)
- **THEN** `has_file` là `true`, `file_url` và `download_url` trả URL chuẩn hóa cho phép người dùng tải/mở tài liệu.
- **WHEN** tài liệu có `download` là `null` hoặc chuỗi rỗng
- **THEN** `has_file` là `false`, `file_url` và `download_url` là `null`, UI hiển thị trạng thái rõ ràng hoặc vô hiệu hóa hành động tải (không tải tệp lỗi 404 hay URL giả).

---

### Requirement: Zero-Fallback and EmptyState
`DocumentController@library()` MUST NOT trả dữ liệu hardcode hay tiêu đề giả khi database không có tài liệu hoặc danh mục `TYPE_DOCUMENT`. Frontend MUST hiển thị `EmptyState` rõ ràng khi danh sách tài liệu rỗng (do DB trống hoặc không khớp từ khóa tìm kiếm/bộ lọc).

#### Scenario: Empty database state
- **WHEN** bảng `posts` và `post_categories` không có bản ghi `type = 'DOCUMENT'` nào
- **THEN** `DocumentController@library()` trả `documentCategories: []`, `documents: []`, `total: 0`, `heroBanner: null`, `ctaBanner: null`, `seo: null` (trừ SEO global nếu không tìm thấy theo design), và giao diện render component/khối `EmptyState` không lỗi JS.

#### Scenario: No search results state
- **WHEN** người dùng tìm kiếm một từ khóa không khớp với bất kỳ tài liệu nào trong CMS
- **THEN** vùng danh sách hiển thị `EmptyState` kèm gợi ý/nút xóa bộ lọc để quay về toàn bộ danh sách.

---

### Requirement: Admin CMS CRUD for Technical Documents and Document Categories
Quản trị viên MUST có module riêng trong Backend (`/admin/documents` và `/admin/document-categories`) để Thêm, Sửa, Xóa, Sắp xếp (`position`), Bật/Tắt (`status`), gán nhiều danh mục tài liệu, nhập SEO đa ngôn ngữ và tải lên tệp PDF (`FileManager`) hoặc nhập URL tải xuống cho trường `download`.

#### Scenario: Admin creates or updates a Technical Document
- **WHEN** Admin truy cập `/admin/documents` hoặc `/admin/document-categories` thông qua Sidebar `"NỘI DUNG & THƯƠNG HIỆU"`
- **THEN** danh sách chỉ hiển thị bản ghi có `type = 'DOCUMENT'`; khi lưu form (`store`), controller tự gán `type = 'DOCUMENT'`, đồng bộ `categories` (`PostCategory::getDocuments()`), và xóa cache `documents_library_{locale}` / `documents_index_{locale}` để Frontend cập nhật tức thì.
