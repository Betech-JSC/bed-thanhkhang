# Báo Cáo Kiểm Toán Hệ Thống CMS, Độ Phủ Kết Nối UI & Điều Hướng Toàn Diện (`/opsx-cms-sync`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Thời gian quét**: `2026-11-04 21:47:35`
- **Cơ chế ánh xạ**: `100% Dynamic AST Route-to-Model Discovery` (Không dùng từ khóa fix cứng)
- **Phạm vi các nhóm Trang Figma Active**: `About, Contact, Error, Histories, History, Home, Policy, Posts, Process, Product, Products, Search, Services` (`65` files `.vue` active, đã tự động lọc bỏ `154` files boilerplate không dùng tới)
- **Tổng số Hệ thống CMS đã có**: `20` Models
- **Số vị trí UI Active chưa connect CMS**: `0` vị trí
- **Số lỗi Điều hướng trên UI Active**: `0` vị trí

---

## 1. Bản Đồ Các Hệ Thống Đã Được Tạo Sẵn Bên CMS (`Existing CMS Systems Inventory`)

| # | Tokens nhận diện động | Model (`app/Models/`) | Các trường dữ liệu chính (`$fillable` / `$translated`) | Admin Controller & Views | Frontend Controller đã nối | Trạng thái UI |
| :--- | :--- | :--- | :--- | :--- | :--- | :--- |
| `01` | `config` | `App\Models\Config` (`app/Models/Config.php`) | `image_title`, `background_image`, `title`, `text_color`, `products`, `categories`, `time_open`, `time_closed` (+48) | `app/Http/Controllers/Backend/ConfigController.php` | `app/Http/Controllers/Frontend/ProcessController.php`, `app/Http/Middleware/HandleInertiaFrontendRequests.php` | ✅ Đã nối UI |
| `02` | `apply`, `contact` | `App\Models\Contact` (`app/Models/Contact.php`) | `data`, `type`, `status`, `estimated_price`, `internal_notes`, `images`, `ip_address`, `user_agent` | `app/Http/Controllers/Backend/ApplyController.php`, `app/Http/Controllers/Backend/ContactController.php` | `app/Http/Controllers/Frontend/ContactController.php` | ✅ Đã nối UI |
| `03` | `contact`, `contactstatuslog`, `log`, `statu` | `App\Models\ContactStatusLog` (`app/Models/ContactStatusLog.php`) | `contact_id`, `old_status`, `new_status`, `changed_by`, `admin_name`, `reason`, `note`, `ip_address` (+1) | — | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `04` | `email`, `emailtemplate`, `template` | `App\Models\EmailTemplate` (`app/Models/EmailTemplate.php`) | `type`, `status`, `position`, `subject (i18n)`, `content (i18n)` | `app/Http/Controllers/Backend/EmailTemplateController.php` | `app/Http/Controllers/Frontend/ContactController.php` | ✅ Đã nối UI |
| `05` | `history` | `App\Models\History` (`app/Models/History.php`) | `status`, `position`, `year`, `staff_quantity`, `image`, `sliders`, `view_count`, `inject_head` (+15) | `app/Http/Controllers/Backend/HistoryController.php` | `app/Http/Controllers/Frontend/HistoryController.php` | ✅ Đã nối UI |
| `06` | `keyword` | `App\Models\Keyword` (`app/Models/Keyword.php`) | `keyword`, `search_count`, `updated_at` | — | `app/Http/Controllers/Frontend/HomeController.php` | ✅ Đã nối UI |
| `07` | `date`, `keyword`, `keywordrefdate`, `ref` | `App\Models\KeywordRefDate` (`app/Models/KeywordRefDate.php`) | `keyword_id`, `created_at`, `updated_at` | — | `app/Http/Controllers/Frontend/HomeController.php` | ✅ Đã nối UI |
| `08` | `meta`, `metapage` | `App\Models\MetaPage` (`app/Models/MetaPage.php`) | `url`, `seo_meta_title`, `seo_slug`, `seo_meta_description`, `seo_meta_keywords`, `seo_meta_robots`, `seo_canonical`, `seo_image` (+4) | — | `app/Http/Middleware/HandleInertiaFrontendRequests.php` | ✅ Đã nối UI |
| `09` | `order` | `App\Models\Order` (`app/Models/Order.php`) | `id`, `order_number`, `payment_method`, `status`, `cancel_status`, `admin_note`, `status_note`, `taxes_included` (+40) | `app/Http/Controllers/Backend/OrderController.php` | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `10` | `line`, `order`, `orderline` | `App\Models\OrderLine` (`app/Models/OrderLine.php`) | `order_id`, `product_id`, `price`, `sale_price`, `is_sale`, `quantity` | — | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `11` | `service` | `App\Models\Service` (`app/Models/Service.php`) | `status`, `position`, `view_count`, `image`, `benefit_image`, `sliders`, `is_content_by_tab`, `email` (+20) | `app/Http/Controllers/Backend/ServiceController.php` | `app/Http/Controllers/Frontend/HomeController.php`, `app/Http/Controllers/Frontend/ServiceController.php` | ✅ Đã nối UI |
| `12` | `category`, `environment`, `faq`, `feedback` | `App\Models\Post\Post` (`app/Models/Post/Post.php`) | `status`, `published_at`, `is_home`, `type`, `is_featured`, `position`, `download`, `home_position` (+40) | `app/Http/Controllers/Backend/FaqController.php`, `app/Http/Controllers/Backend/FeedbackController.php`, `app/Http/Controllers/Backend/GalleryController.php`, `app/Http/Controllers/Backend/LearningEnvironmentController.php`, `app/Http/Controllers/Backend/MemberController.php`, `app/Http/Controllers/Backend/PostCategoryController.php`, `app/Http/Controllers/Backend/PostController.php`, `app/Http/Controllers/Backend/PrizeController.php`, `app/Http/Controllers/Backend/ProjectController.php` | `app/Http/Controllers/Frontend/AgencyController.php`, `app/Http/Controllers/Frontend/ContactController.php`, `app/Http/Controllers/Frontend/FeedbackController.php`, `app/Http/Controllers/Frontend/HistoryController.php`, `app/Http/Controllers/Frontend/HomeController.php`, `app/Http/Controllers/Frontend/JobController.php`, `app/Http/Controllers/Frontend/PostController.php`, `app/Http/Controllers/Frontend/PrizeController.php`, `app/Http/Controllers/Frontend/ProcessController.php`, `app/Http/Controllers/Frontend/ProductController.php`, `app/Http/Controllers/Frontend/ProjectController.php`, `app/Http/Controllers/Frontend/SitemapController.php`, `app/Http/Controllers/Frontend/Auth/UserAuthController.php`, `app/Http/Middleware/HandleInertiaFrontendRequests.php` | ✅ Đã nối UI |
| `13` | `category`, `post`, `postcategory`, `project` | `App\Models\Post\PostCategory` (`app/Models/Post/PostCategory.php`) | `parent_id`, `status`, `type`, `position`, `menu_position`, `view_count`, `image`, `icon` (+12) | `app/Http/Controllers/Backend/PostCategoryController.php`, `app/Http/Controllers/Backend/ProjectCategoryController.php` | `app/Http/Controllers/Frontend/HomeController.php`, `app/Http/Controllers/Frontend/PostController.php`, `app/Http/Controllers/Frontend/ProjectController.php`, `app/Http/Controllers/Frontend/SitemapController.php` | ✅ Đã nối UI |
| `14` | `brand` | `App\Models\Brand\Brand` (`app/Models/Brand/Brand.php`) | `status`, `position`, `image`, `created_by`, `updated_by`, `deleted_by`, `locale (i18n)`, `title (i18n)` (+11) | `app/Http/Controllers/Backend/BrandController.php` | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `15` | `category`, `product` | `App\Models\Product\Product` (`app/Models/Product/Product.php`) | `brand_id`, `status`, `is_featured`, `is_new`, `is_stock`, `view_count`, `images`, `image` (+26) | `app/Http/Controllers/Backend/ProductCategoryController.php`, `app/Http/Controllers/Backend/ProductController.php` | `app/Http/Controllers/Frontend/HomeController.php`, `app/Http/Controllers/Frontend/ProductController.php`, `app/Http/Controllers/Frontend/SitemapController.php`, `app/Http/Controllers/Frontend/Auth/UserAuthController.php`, `app/Http/Middleware/HandleInertiaFrontendRequests.php` | ✅ Đã nối UI |
| `16` | `category`, `product`, `productcategory` | `App\Models\Product\ProductCategory` (`app/Models/Product/ProductCategory.php`) | `parent_id`, `status`, `image`, `image_mobile`, `position`, `menu_position`, `footer_position`, `featured_position` (+21) | `app/Http/Controllers/Backend/ProductCategoryController.php` | `app/Http/Controllers/Frontend/ProductController.php` | ✅ Đã nối UI |
| `17` | `post`, `product`, `productrefpost`, `ref` | `App\Models\Product\ProductRefPost` (`app/Models/Product/ProductRefPost.php`) | `product_id`, `post_id` | — | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `18` | `product`, `productvariant`, `variant` | `App\Models\Product\ProductVariant` (`app/Models/Product/ProductVariant.php`) | `product_id`, `status`, `sku`, `price`, `old_price`, `sale_price`, `sale_quantity`, `flash_sale_quantity` (+21) | — | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `19` | `option`, `product`, `productvariantrefoption`, `ref` | `App\Models\Product\ProductVariantRefOption` (`app/Models/Product/ProductVariantRefOption.php`) | `product_variant_id`, `product_id`, `option_id`, `position`, `is_required` | — | ⚠️ **Chưa gọi ở Frontend** | ⚠️ Chưa nối UI |
| `20` | `policy` | `App\Models\Policy\Policy` (`app/Models/Policy/Policy.php`) | `position`, `icon`, `status`, `slug (i18n)`, `locale (i18n)`, `title (i18n)`, `content (i18n)`, `seo_meta_title (i18n)` (+7) | `app/Http/Controllers/Backend/PolicyController.php` | `app/Http/Controllers/Frontend/PolicyController.php`, `app/Http/Controllers/Frontend/SitemapController.php`, `app/Http/Middleware/HandleInertiaFrontendRequests.php` | ✅ Đã nối UI |

---

## 2. Danh Sách Các Chỗ Trên UI Active Chưa Connect Với Hệ Thống CMS (`Unconnected UI -> Existing CMS Mapping`)

> ✅ **100% ĐÃ KẾT NỐI CMS**: Toàn bộ các Frontend Controllers, Pages và Layouts thuộc phạm vi Figma Active đã nhận dữ liệu động từ các hệ thống CMS sẵn có.

---

## 3. Kiểm Toán Toàn Bộ Điều Hướng Trên Các Trang Figma Active (`Full Navigation, Route, Link & CTA Audit`)

- **Tổng số Route định danh (`->name(...)`) trong Backend**: `39` (`about, api.popup-notifications, api.search, api.support-info, auth.google, auth.google.callback, contact, contact.store, forgot.password.email, forgot.password.index, histories.index, histories.show, home, instant_search, login...`)

> ✅ **100% ĐIỀU HƯỚNG HỢP LỆ**: Không phát hiện link chết (`href="#"`), không có lệnh gọi `route('...')` sai tên, và không có nút `<button>` mồ côi thiếu sự kiện trên các trang Figma Active.

---

## 4. Tổng Quan Trạng Thái 8 Trụ Cột CMS (`8-Pillars Health Summary`)

| Trụ cột | Hạng mục kiểm toán | Trạng thái | Ghi chú thực tế |
| :--- | :--- | :--- | :--- |
| **Trụ cột 1: Admin Theme, Sidebar & Dashboard** | Kiểm toán tự động | `✅ PASS` | DashboardController có `Schema::hasTable` guard |
| **Trụ cột 2: Media Manager (`FileManager` & `Thumbnail`)** | Kiểm toán tự động | `✅ PASS` | `FileManager.vue` đã có bản vá reload; `Thumbnail.vue` đã có `@click.stop` |
| **Trụ cột 3: Schema JSON Filters & Custom Form Figma** | Kiểm toán tự động | `✅ PASS` | Model `Product` đã hỗ trợ JSON schema & đồng bộ danh mục; `Products/Form.vue` đã tinh gọn theo Figma |
| **Trụ cột 4: Chuẩn hóa Leads `Contact::normalizeLeadData`** | Kiểm toán tự động | `✅ PASS` | `Contact::normalizeLeadData()` đã hoạt động |
| **Trụ cột 5: Global Brand Settings (`props.global` & `map_iframe`)** | Kiểm toán tự động | `✅ PASS` | `Config.php` đã đồng bộ cấu hình thương hiệu & `map_iframe`; `HandleInertiaFrontendRequests.php` đã chia sẻ `props.global` |
| **Trụ cột 6: Độ phủ UI-to-CMS & Idempotent Auto-Seeder** | Kiểm toán tự động | `✅ PASS` | CI/CD an toàn (không chứa `migrate:fresh`); 16 Frontend Controllers kết nối DB động |
| **Trụ cột 7: Hợp đồng Khóa Cache & Instant Invalidation** | Kiểm toán tự động | `✅ PASS` | Đã khớp 19/19 tiền tố khóa Cache |
| **Trụ cột 8: PHPUnit CMS-to-Frontend Integration Tests** | Kiểm toán tự động | `✅ PASS` | Đã có 1 file test tích hợp CMS (CmsSyncIntegrationTest.php) |

---

## 5. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)

### 🟢 BẢNG 5.1: `[+] ĐỀ XUẤT THÊM KẾT NỐI CMS, ĐIỀU HƯỚNG & CACHE (ADD)`

> ✅ Toàn bộ kết nối CMS, điều hướng và 8 trụ cột CMS đã đầy đủ (`0` hạng mục cần thêm).

### 🔴 BẢNG 5.2: `[-] ĐỀ XUẤT BỎ DỮ LIỆU TĨNH / TRƯỜNG THỪA (REMOVE)`

> ✅ Không phát hiện trường thừa hay hàm hardcode cố định gây xung đột với CMS.

### 🔵 BẢNG 5.3: `[=] ĐỀ XUẤT GIỮ NGUYÊN HỆ THỐNG CỐT LÕI (KEEP)`

| Mã | Trụ cột CMS | Tọa độ / Phạm vi | Hạng mục bảo toàn (`[=]`) | Lý do giữ nguyên |
| :--- | :--- | :--- | :--- | :--- |
| `KEEP-01` | Trụ cột 1 (Admin & Dashboard) | `app/Http/Controllers/Backend/DashboardController.php:51` | Bảo vệ truy vấn KPI trên Dashboard bằng `Schema::hasTable(...)` | Đảm bảo trang Admin Dashboard không bị lỗi 500 khi bảng dữ liệu đang trống hoặc vừa migrate. |
| `KEEP-02` | Trụ cột 1 (Admin Sidebar) | `resources/Backend/js/Components/SidebarMain.vue:1` | Cấu trúc Menu Sidebar Admin tinh gọn theo đúng nghiệp vụ dự án | Đã ẩn các module không sử dụng, chỉ hiển thị các mục quản trị thực tế. |
| `KEEP-03` | Trụ cột 1 (Brand Assets) | `public/favicon.ico` | Bộ Favicon Retina & Web Manifest chuẩn thương hiệu | Đã đồng bộ đầy đủ icon nhận diện thương hiệu trên cả Admin và Frontend. |
| `KEEP-04` | Trụ cột 2 (Media Manager) | `packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue:500` | Bản vá reload danh sách file khi xóa ảnh cuối cùng trong `FileManager.vue` | Đảm bảo `this.data.files` được làm mới tức thì và reset `this.fetchData = true` trong `onRemove()`. |
| `KEEP-05` | Trụ cột 2 (Media Manager) | `packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue:30` | Chặn nổi bọt sự kiện `@click.stop="$emit('remove', file)"` trên `Thumbnail.vue` | Ngăn việc bấm nút Xóa file lại kích hoạt nhầm hành động chọn ảnh vào bài viết/sản phẩm. |
| `KEEP-06` | Trụ cột 3 (Schema JSON & Categories) | `app/Models/Product/Product.php:1` | Schema JSON (`filter_attributes`, `pdp_metadata`) & đồng bộ 2 chiều `product_ref_categories` | Hỗ trợ lưu trữ đầy đủ bộ lọc chuyên sâu và các tab chi tiết sản phẩm theo Figma. |
| `KEEP-07` | Trụ cột 3 (Custom CMS Form) | `resources/Backend/js/Pages/Products/Products/Form.vue:1` | Biểu mẫu Quản trị Sản phẩm (`Products/Form.vue`) đã tùy biến khớp với thiết kế Figma | Đã ẩn các trường không có trên Figma và giữ lại đúng các tab cấu hình kỹ thuật. |
| `KEEP-08` | Trụ cột 4 (Leads Normalization) | `app/Models/Contact.php:146` | Chuẩn hóa dữ liệu Leads `Contact::normalizeLeadData()` (`lead_type`, `form_source`, song ngữ EN/VI) | Hợp nhất dữ liệu từ Modal Báo giá PDP, Modal Tư vấn và Form Liên hệ về định dạng chuẩn cho Admin. |
| `KEEP-09` | Trụ cột 5 (Global Brand Settings) | `app/Models/Config.php:79` | Hàm trung tâm trong `Config.php` đồng bộ 2 chiều khóa cấu hình thương hiệu & `map_iframe` | Cung cấp nguồn dữ liệu thương hiệu thống nhất (Hotline, Email, Địa chỉ, MXH, Bản đồ, Giới thiệu). |
| `KEEP-10` | Trụ cột 5 (Inertia Shared Props) | `app/Http/Middleware/HandleInertiaFrontendRequests.php:179` | Chia sẻ `props.global` toàn cục qua `HandleInertiaFrontendRequests.php` | Giúp Header, Footer, Modal Tư vấn và mọi trang Frontend tự động nhận thông tin thương hiệu từ CMS. |
| `KEEP-11` | Trụ cột 6 (CI/CD & Auto-Seeder) | `.github/workflows/ci-cd.yml:1` | Pipeline CI/CD chạy `migrate --force` và `RoleSeeder` an toàn (Idempotent, không mất dữ liệu) | Tuân thủ tuyệt đối nguyên tắc an toàn Database trên Production. |
| `KEEP-12` | Trụ cột 6 (Dynamic Controllers & Legacy Guard) | `app/Http/Controllers/Frontend/` | 16 Frontend Controllers đã ưu tiên truy vấn Eloquent DB | Đảm bảo dữ liệu CMS luôn được ưu tiên số 1. |
| `KEEP-13` | Trụ cột 7 (Cache Key Contract) | `app/Models/ & app/Http/Controllers/Frontend/` | Hợp đồng Invalidation cho 19/19 nhóm khóa Cache | Đảm bảo khi Admin bấm Lưu trong CMS, dữ liệu ngoài Frontend cập nhật tức thì (0 giây độ trễ). |
| `KEEP-14` | Trụ cột 8 (PHPUnit E2E CMS Tests) | `tests/Feature/ (1 files)` | Bộ 1 bài test tích hợp CMS-to-Frontend (`CmsSyncIntegrationTest.php`) | Kiểm chứng tự động chu trình `Admin POST -> DB Assert -> Cache Invalidation -> Frontend GET Assert`. |

---

## 6. Bộ Handoff Prompts Sinh Tự Động Từ Hệ Thống CMS Có Sẵn (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)

> 💡 **Cách sử dụng**: Sau khi bạn Confirm Bảng Quyết Định ở Mục 5, hãy copy từng khối **Prompt Gói 1, Gói 2, Gói 3** bên dưới sang các đoạn hội thoại mới để xử lý song song hoặc tuần tự mà không lo tràn ngữ cảnh.

### 📦 GÓI 1: Đấu Nối Toàn Bộ UI Active Chưa Connect Vào Các Hệ Thống Đã Có Bên CMS (`UI -> Existing CMS Integration`)

```markdown
Bạn đang thực thi **GÓI 1: Đấu Nối Toàn Bộ UI Chưa Connect Vào Các Hệ Thống Đã Có Bên CMS** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.
Hãy đọc `.agents/skills/opsx-cms-sync/references/cms-8-pillars-architecture.md` trước khi thực hiện.

### 1. Nguyên Tắc An Toàn (`STRICT SAFE GUARD`)
- Tuyệt đối KHÔNG dùng `rm` xóa file, KHÔNG chạy `migrate:fresh` hoặc `db:seed` ghi đè dữ liệu.
- Bắt buộc gọi `invoke_subagent` (`Role: "Backend Worker"` / `"Frontend Worker"`) để thực thi.
- Tận dụng 100% các Model và bảng dữ liệu ĐÃ CÓ SẴN bên CMS dưới đây, không tạo bảng/Model trùng lặp:
  - `App\Models\Config` (`app/Models/Config.php`) | Trường dữ liệu: `image_title, background_image, title, text_color, products, categories, time_open, time_closed` | Admin: `app/Http/Controllers/Backend/ConfigController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/ProcessController.php, app/Http/Middleware/HandleInertiaFrontendRequests.php`
  - `App\Models\Contact` (`app/Models/Contact.php`) | Trường dữ liệu: `data, type, status, estimated_price, internal_notes, images, ip_address, user_agent` | Admin: `app/Http/Controllers/Backend/ApplyController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/ContactController.php`
  - `App\Models\EmailTemplate` (`app/Models/EmailTemplate.php`) | Trường dữ liệu: `type, status, position, subject, content` | Admin: `app/Http/Controllers/Backend/EmailTemplateController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/ContactController.php`
  - `App\Models\History` (`app/Models/History.php`) | Trường dữ liệu: `status, position, year, staff_quantity, image, sliders, view_count, inject_head` | Admin: `app/Http/Controllers/Backend/HistoryController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/HistoryController.php`
  - `App\Models\Keyword` (`app/Models/Keyword.php`) | Trường dữ liệu: `keyword, search_count, updated_at` | Admin: `Backend Controller` | Frontend hiện tại: `app/Http/Controllers/Frontend/HomeController.php`
  - `App\Models\KeywordRefDate` (`app/Models/KeywordRefDate.php`) | Trường dữ liệu: `keyword_id, created_at, updated_at` | Admin: `Backend Controller` | Frontend hiện tại: `app/Http/Controllers/Frontend/HomeController.php`
  - `App\Models\MetaPage` (`app/Models/MetaPage.php`) | Trường dữ liệu: `url, seo_meta_title, seo_slug, seo_meta_description, seo_meta_keywords, seo_meta_robots, seo_canonical, seo_image` | Admin: `Backend Controller` | Frontend hiện tại: `app/Http/Middleware/HandleInertiaFrontendRequests.php`
  - `App\Models\Order` (`app/Models/Order.php`) | Trường dữ liệu: `id, order_number, payment_method, status, cancel_status, admin_note, status_note, taxes_included` | Admin: `app/Http/Controllers/Backend/OrderController.php` | Frontend hiện tại: `Chưa nối Frontend Controller`
  - `App\Models\Service` (`app/Models/Service.php`) | Trường dữ liệu: `status, position, view_count, image, benefit_image, sliders, is_content_by_tab, email` | Admin: `app/Http/Controllers/Backend/ServiceController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/HomeController.php, app/Http/Controllers/Frontend/ServiceController.php`
  - `App\Models\Post\Post` (`app/Models/Post/Post.php`) | Trường dữ liệu: `status, published_at, is_home, type, is_featured, position, download, home_position` | Admin: `app/Http/Controllers/Backend/FaqController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/AgencyController.php, app/Http/Controllers/Frontend/ContactController.php, app/Http/Controllers/Frontend/FeedbackController.php, app/Http/Controllers/Frontend/HistoryController.php, app/Http/Controllers/Frontend/HomeController.php, app/Http/Controllers/Frontend/JobController.php, app/Http/Controllers/Frontend/PostController.php, app/Http/Controllers/Frontend/PrizeController.php, app/Http/Controllers/Frontend/ProcessController.php, app/Http/Controllers/Frontend/ProductController.php, app/Http/Controllers/Frontend/ProjectController.php, app/Http/Controllers/Frontend/SitemapController.php, app/Http/Controllers/Frontend/Auth/UserAuthController.php, app/Http/Middleware/HandleInertiaFrontendRequests.php`
  - `App\Models\Post\PostCategory` (`app/Models/Post/PostCategory.php`) | Trường dữ liệu: `parent_id, status, type, position, menu_position, view_count, image, icon` | Admin: `app/Http/Controllers/Backend/PostCategoryController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/HomeController.php, app/Http/Controllers/Frontend/PostController.php, app/Http/Controllers/Frontend/ProjectController.php, app/Http/Controllers/Frontend/SitemapController.php`
  - `App\Models\Brand\Brand` (`app/Models/Brand/Brand.php`) | Trường dữ liệu: `status, position, image, created_by, updated_by, deleted_by, locale, title` | Admin: `app/Http/Controllers/Backend/BrandController.php` | Frontend hiện tại: `Chưa nối Frontend Controller`
  - `App\Models\Product\Product` (`app/Models/Product/Product.php`) | Trường dữ liệu: `brand_id, status, is_featured, is_new, is_stock, view_count, images, image` | Admin: `app/Http/Controllers/Backend/ProductCategoryController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/HomeController.php, app/Http/Controllers/Frontend/ProductController.php, app/Http/Controllers/Frontend/SitemapController.php, app/Http/Controllers/Frontend/Auth/UserAuthController.php, app/Http/Middleware/HandleInertiaFrontendRequests.php`
  - `App\Models\Product\ProductCategory` (`app/Models/Product/ProductCategory.php`) | Trường dữ liệu: `parent_id, status, image, image_mobile, position, menu_position, footer_position, featured_position` | Admin: `app/Http/Controllers/Backend/ProductCategoryController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/ProductController.php`
  - `App\Models\Policy\Policy` (`app/Models/Policy/Policy.php`) | Trường dữ liệu: `position, icon, status, slug, locale, title, content, seo_meta_title` | Admin: `app/Http/Controllers/Backend/PolicyController.php` | Frontend hiện tại: `app/Http/Controllers/Frontend/PolicyController.php, app/Http/Controllers/Frontend/SitemapController.php, app/Http/Middleware/HandleInertiaFrontendRequests.php`

### 2. Danh Sách Các Chỗ Trên UI Chưa Connect Cần Đấu Nối Ngay Vào CMS:
- Hiện tại 100% các trang/section UI Active đã được nối với Model CMS. Hãy kiểm tra rà soát lại các khóa Cache và dữ liệu fallback nếu cần.

### 3. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` để xác nhận số lượng `UI chưa connect CMS` giảm về `0`.
- Chạy `yarn frontend:build` và `php artisan test` xanh 100%.
```

### 🧭 GÓI 2: Đấu Nối Toàn Bộ Điều Hướng, Xóa Link Chết (`href="#"`), Sửa Lệch Route & Nút CTA (`Full Navigation Fix`)

```markdown
Bạn đang thực thi **GÓI 2: Đấu Nối Toàn Bộ Điều Hướng, Xóa Link Chết & Sửa Lệch Route** từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.

### 1. Nguyên Tắc Thực Thi
- Đối chiếu toàn bộ tên route trong `routes/frontend.php` và `routes/web.php`.
- Mọi thẻ `<a>` / `<Link>` điều hướng nội bộ phải trỏ đúng tên route hoặc `slug` động lấy từ các Model CMS đã có.
- Mọi nút `<button>` CTA (Tư vấn, Báo giá, Xem chi tiết, Gửi liên hệ) phải gắn đúng `@click` hoặc `type="submit"`.

### 2. Danh Sách Tọa Độ Lỗi Điều Hướng Cần Fix Triệt Để:
- Hiện tại toàn bộ điều hướng (`<Link>`, `route(...)`, `<button>`) trên UI Active đã hợp lệ 100% (`0` link chết, `0` route lệch).

### 3. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy lại `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` để xác nhận `0 lỗi điều hướng`.
- Chạy `yarn frontend:build` thành công (`exit code 0`).
```

### ⚙️ GÓI 3: Đồng Bộ 8 Trụ Cột CMS, Hợp Đồng Khóa Cache & PHPUnit E2E Tests (`CMS Core & Cache Parity`)

```markdown
Bạn đang thực thi **GÓI 3: Đồng Bộ 8 Trụ Cột CMS, Hợp Đồng Khóa Cache & PHPUnit E2E Tests** từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.

### 1. Các Hạng Mục Cần Bổ Sung (`[+] ADD`) & Tinh Gọn (`[-] REMOVE`):
- Toàn bộ 8 trụ cột CMS và khóa Cache đã đạt `PASS`. Hãy chạy bộ PHPUnit Feature Tests để nghiệm thu tổng thể.

### 2. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` đạt 8/8 PASS.
- Chạy `php artisan test` xanh 100%.
```
