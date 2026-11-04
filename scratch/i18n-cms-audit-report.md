# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Backend CMS (`VI / EN` & Auto-Fallback `EN -> VI`) (`/opsx-i18n-cms`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Thời gian quét**: `2026-11-04 16:16:14`
- **Chế độ**: `Strict Read-Only Pre-Flight Audit`
- **Ranh giới chạy song song**: `[ALLOWED: config/, app/, database/, resources/Backend/]` | `[LOCKED: resources/Frontend/]`

---

## 1. Tổng Quan 4 Trụ Cột Đa Ngôn Ngữ Backend CMS

| Trụ cột | Hạng mục kiểm toán | Trạng thái | Ghi chú thực tế |
| :--- | :--- | :--- | :--- |
| **Trụ cột 1: Cấu hình Locale & Nút chuyển `VI | EN` Admin** | Kiểm toán tự động | `❌ FAIL` | [P0-CONFIG] `config/app.php` thiếu `'en'` trong `'locales'`; `fallback_locale` đang là `'en'` (cần đổi sang `'vi'`) |
| **Trụ cột 2: Ô nhập Tiếng Anh (`EN`) trên Form.vue & Configs.vue** | Kiểm toán tự động | `✅ PASS` | 9 Models có bảng dịch `*_translations` |
| **Trụ cột 3: Chống lỗi Validation & Tự động Fallback `EN -> VI`** | Kiểm toán tự động | `✅ PASS` | Đã có cơ chế lưu theo `locale`; Truy cập thuộc tính Model tự động kế thừa fallback `EN -> VI` |
| **Trụ cột 4: Khóa Cache tách biệt theo ngôn ngữ (`_{locale}`)** | Kiểm toán tự động | `⚠️ CẦN XEM XÉT` | 17/19 khóa Cache đã gắn `_{$locale}` (2 khóa thiếu `_{$locale}` gây lẫn ngôn ngữ!) |

---

## 2. Kiểm Toán Cấu Hình Locale & Nút Chuyển `VI | EN` Trong Admin (`config/app.php` & `config/translatable.php`)

| Tọa độ `File:Dòng` | Cấu hình hiện tại | Trạng thái | Ý nghĩa / Tác động |
| :--- | :--- | :--- | :--- |
| `config/app.php:88` | `'locales' => ['vi']` | **❌ [P0-CONFIG] THIẾU `'en'`** | Mảng `'locales'` chỉ có `'vi'` làm ẩn hoàn toàn nút chuyển đổi ngôn ngữ `VI | EN` trong Admin CMS (`SidebarItems.vue`). |
| `config/app.php:105` | `'fallback_locale' => 'en'` | **⚠️ [P0-CONFIG] SAI FALLBACK** | `fallback_locale` đang để `'en'` thay vì `'vi'`. Khi chưa nhập tiếng Anh sẽ không tự lấy tiếng Việt làm gốc. |
| `config/translatable.php:53` | `'use_fallback' => true` | **✅ PASS** | Đã bật tự động fallback cấp thuộc tính khi trường dịch tiếng Anh rỗng. |
| `config/translatable.php:65` | `'use_property_fallback' => true` | **✅ PASS** | Đã bật tự động fallback cấp thuộc tính khi trường dịch tiếng Anh rỗng. |
| `config/translatable.php:81` | `'fallback_locale' => config('app.fallback_locale')` | **⚠️ CẦN BẬT** | Cần đặt `'fallback_locale' => 'vi'` để tự động fallback `EN -> VI` cho từng trường dịch. |

---

## 3. Kiểm Toán Các Hệ Thống Model, Trường JSON & Biểu Mẫu Admin (`Form.vue` & `Configs.vue`)

| Model (`app/Models/`) | Các trường đã có trong `$translatedAttributes` (`*_translations`) | Các trường Text/JSON chưa tách ngôn ngữ `VI/EN` | Biểu mẫu Admin (`resources/Backend/`) | Trạng thái |
| :--- | :--- | :--- | :--- | :--- |
| `History` (`app/Models/History.php`) | `slug`, `locale`, `title`, `description`, `content`, `seo_meta_title`, `seo_slug`, `seo_meta_description` | — (Đã xử lý đa ngôn ngữ) | `resources/Backend/js/Pages/Histories/Form.vue` | **✅ PASS** |
| `Service` (`app/Models/Service.php`) | `slug`, `locale`, `title`, `description`, `content`, `title_content`, `content_by_tab`, `benefits` | — (Đã xử lý đa ngôn ngữ) | `resources/Backend/js/Pages/Services/Form.vue` | **✅ PASS** |
| `Post` (`app/Models/Post/Post.php`) | `slug`, `locale`, `title`, `author`, `description`, `content`, `sliders`, `take_note` | — | `resources/Backend/js/Pages/Posts/Form.vue` | **✅ PASS** |
| `PostCategory` (`app/Models/Post/PostCategory.php`) | `slug`, `locale`, `title`, `sliders`, `seo_meta_title`, `seo_slug`, `seo_meta_description`, `seo_meta_keywords` | — | `resources/Backend/js/Pages/PostCategories/Form.vue` | **✅ PASS** |
| `Brand` (`app/Models/Brand/Brand.php`) | `locale`, `title`, `slug`, `summary`, `description`, `seo_meta_title`, `seo_slug`, `seo_meta_description` | — | `resources/Backend/js/Pages/Products/Brands/Form.vue` | **✅ PASS** |
| `Product` (`app/Models/Product/Product.php`) | `slug`, `locale`, `title`, `content`, `description`, `seo_meta_title`, `seo_slug`, `seo_meta_description` | — | `resources/Backend/js/Pages/Products/Options/Form.vue`, `resources/Backend/js/Pages/Products/Products/Form.vue`, `resources/Backend/js/Pages/Products/ProductVariants/Form.vue`, `resources/Backend/js/Pages/Products/Brands/Form.vue`, `resources/Backend/js/Pages/Products/Ratings/Form.vue`, `resources/Backend/js/Pages/Products/Tags/Form.vue`, `resources/Backend/js/Pages/Products/Orders/Form.vue`, `resources/Backend/js/Pages/Products/Origins/Form.vue` | **✅ PASS** |
| `ProductCategory` (`app/Models/Product/ProductCategory.php`) | `locale`, `title`, `slug`, `banner`, `description`, `seo_meta_title`, `seo_slug`, `seo_meta_description` | — | — | **✅ PASS** |
| `ProductVariant` (`app/Models/Product/ProductVariant.php`) | `locale`, `title`, `seo_meta_title`, `seo_slug`, `seo_meta_description`, `seo_meta_keywords`, `seo_meta_robots`, `seo_canonical` | — | `resources/Backend/js/Pages/Products/ProductVariants/Form.vue` | **✅ PASS** |
| `Policy` (`app/Models/Policy/Policy.php`) | `slug`, `locale`, `title`, `content`, `seo_meta_title`, `seo_slug`, `seo_meta_description`, `seo_meta_keywords` | — | `resources/Backend/js/Pages/Policies/Form.vue` | **✅ PASS** |
| `Config` (`app/Models/Config.php`) | Hỗ trợ cấu hình theo `locale` / `*_en` | — | `resources/Backend/js/Pages/Configs.vue` | **✅ PASS** |

---

## 4. Kiểm Toán Khóa Cache Đa Ngôn Ngữ (`_{locale}` Cache Isolation)

| Tọa độ `File:Dòng` | Biểu thức Khóa Cache (`Cache::remember`) | Trạng thái phân tách `VI / EN` |
| :--- | :--- | :--- |
| `app/Http/Controllers/Frontend/Auth/UserAuthController.php:756` | `'otp_' . $phone` | **❌ Thiếu `_{$locale}` (Lẫn cache VI/EN)** |
| `app/Http/Controllers/Frontend/Auth/UserAuthController.php:774` | `'otp_' . $phone` | **❌ Thiếu `_{$locale}` (Lẫn cache VI/EN)** |
| `app/Http/Controllers/Frontend/AgencyController.php:16` | `'agencies_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/FeedbackController.php:15` | `'feedback_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/HistoryController.php:16` | `'history_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/HistoryController.php:44` | `'history_show_' . $slug . '_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/HomeController.php:20` | `'home_page_data_' . app()->getLocale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/PostController.php:18` | `'posts_index_' . app()->getLocale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/PostController.php:63` | `'post_show_' . $slug . '_' . app()->getLocale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/PostController.php:89` | `'related_posts_' . $postId . '_' . app()->getLocale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/PrizeController.php:18` | `'prizes_index_' . current_locale() . '_page_' . $page` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/PrizeController.php:45` | `'prize_show_' . $slug . '_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ProductController.php:19` | `'products_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ProductController.php:57` | `'product_show_' . $slug . '_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ProductController.php:85` | `'galleries_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ProjectController.php:18` | `'projects_index_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ProjectController.php:52` | `'project_show_' . $slug . '_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Controllers/Frontend/ServiceController.php:34` | `'service_show_' . $slug . '_' . current_locale()` | **✅ Có `_{$locale}`** |
| `app/Http/Middleware/HandleInertiaFrontendRequests.php:36` | `'inertia_layout_shared_data_' . $locale` | **✅ Có `_{$locale}`** |

---

## 5. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)

### 🟢 BẢNG 5.1: `[+] ĐỀ XUẤT THÊM CẤU HÌNH, Ô NHẬP TIẾNG ANH, FALLBACK EN->VI & KHÓA CACHE (ADD)`

| Mã | Trụ cột | Tọa độ `File:Dòng` | Nội dung đề xuất THÊM (`[+]`) | Lý do |
| :--- | :--- | :--- | :--- | :--- |
| `ADD-01` | Trụ cột 1 ([P0-CONFIG] Admin Switcher) | `config/app.php:88` | Cập nhật `'locales' => ['vi', 'en']` trong `config/app.php` | Hiện tại `'locales'` chỉ có `['vi']` nên nút chuyển ngôn ngữ `VI | EN` trên Sidebar Admin bị ẩn. |
| `ADD-02` | Trụ cột 1 ([P0-CONFIG] Fallback Locale) | `config/app.php:105` | Đổi `'fallback_locale' => 'en'` thành `'fallback_locale' => 'vi'` trong `config/app.php` | Đảm bảo khi bản dịch Tiếng Anh (`en`) bỏ trống, Laravel tự động lấy Tiếng Việt (`vi`) làm ngôn ngữ dự phòng. |
| `ADD-03` | Trụ cột 1 (Translatable Fallback) | `config/translatable.php:81` | Cấu hình `'fallback_locale' => 'vi'` trong `config/translatable.php` | Kích hoạt cơ chế tự động trả về giá trị Tiếng Việt (`vi`) khi một thuộc tính Tiếng Anh (`en`) bị bỏ trống. |
| `ADD-04` | Trụ cột 4 (Locale Cache Isolation) | `app/Http/Controllers/Frontend/Auth/UserAuthController.php:756` | Gắn hậu tố ngôn ngữ `_{$locale}` (`app()->getLocale()`) vào khóa Cache `'otp_' . $phone` và xóa cả 2 ngôn ngữ `['vi', 'en']` khi `Cache::forget` | Khóa cache hiện tại không phân biệt ngôn ngữ — khách chuyển sang Tiếng Anh (`EN`) sẽ bị trả về dữ liệu Tiếng Việt (`VI`) đã lưu trong cache. |
| `ADD-05` | Trụ cột 4 (Locale Cache Isolation) | `app/Http/Controllers/Frontend/Auth/UserAuthController.php:774` | Gắn hậu tố ngôn ngữ `_{$locale}` (`app()->getLocale()`) vào khóa Cache `'otp_' . $phone` và xóa cả 2 ngôn ngữ `['vi', 'en']` khi `Cache::forget` | Khóa cache hiện tại không phân biệt ngôn ngữ — khách chuyển sang Tiếng Anh (`EN`) sẽ bị trả về dữ liệu Tiếng Việt (`VI`) đã lưu trong cache. |

### 🔴 BẢNG 5.2: `[-] ĐỀ XUẤT BỎ (REMOVE)`

> ✅ Không có cấu hình hoặc bảng dữ liệu nào cần xóa bỏ.

### 🔵 BẢNG 5.3: `[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`

| Mã | Trụ cột | Tọa độ / Phạm vi | Hạng mục bảo toàn (`[=]`) | Lý do |
| :--- | :--- | :--- | :--- | :--- |
| `KEEP-01` | Trụ cột 3 (Save per-locale) | `packages/bed-package-essentials/core/src/Traits/HasCrudActions.php:1` | Cơ chế lưu bản dịch theo từng `locale` độc lập kết hợp `use_property_fallback => true` | Cho phép Admin nhập Tiếng Việt trước, các trường chưa nhập Tiếng Anh tự động đọc từ bản ghi Tiếng Việt. |
| `KEEP-02` | Trụ cột 4 (Locale Cache Keys) | `app/Http/Controllers/Frontend/ & HandleInertiaFrontendRequests.php` | 17/19 khóa Cache đã phân tách độc lập theo `_{$locale}` (`vi` / `en`) | Đảm bảo dữ liệu cache của Tiếng Việt và Tiếng Anh không bao giờ bị ghi đè lẫn nhau. |

---

## 6. Bộ Handoff Prompts Đa Ngôn Ngữ Backend CMS (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)

> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Các gói Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: resources/Frontend/**]`) để bạn có thể chạy song song cùng lúc với `/opsx-i18n-fe` ở một đoạn hội thoại khác mà không bao giờ đụng file.

### 📦 GÓI 1 (CMS i18n): Mở Nút Chuyển `VI | EN` Admin, Bổ Sung Ô Nhập Tiếng Anh (`EN`), Tự Động Fallback `EN -> VI` & Tách Khóa Cache `_{locale}`

```markdown
Bạn đang thực thi **GÓI 1: Đồng Bộ Đa Ngôn Ngữ Backend CMS (VI / EN) & Tự Động Fallback EN -> VI** từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-cms`.
Hãy đọc `.agents/skills/opsx-i18n-cms/SKILL.md` trước khi thực hiện.

### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)
- ✅ `[ALLOWED TO EDIT]`: `config/app.php`, `config/translatable.php`, `app/Models/**`, `app/Http/Controllers/**`, `app/Http/Middleware/HandleInertiaFrontendRequests.php`, `database/migrations/**`, `resources/Backend/js/**`.
- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `resources/Frontend/**` (đang được xử lý song song bởi `/opsx-i18n-fe`).
- Bắt buộc gọi `invoke_subagent` (`Role: "Backend Worker"`) để thực hiện. Cấm dùng `rm` xóa file, cấm `migrate:fresh`.

### 2. Danh Sách Các Hạng Mục Đã Confirm Cần Thực Thi:
- **[CMS-I18N-01] `config/app.php:88`** (Trụ cột 1 ([P0-CONFIG] Admin Switcher)):
  - Hành động: Cập nhật `'locales' => ['vi', 'en']` trong `config/app.php`
  - Lý do: Hiện tại `'locales'` chỉ có `['vi']` nên nút chuyển ngôn ngữ `VI | EN` trên Sidebar Admin bị ẩn.
- **[CMS-I18N-02] `config/app.php:105`** (Trụ cột 1 ([P0-CONFIG] Fallback Locale)):
  - Hành động: Đổi `'fallback_locale' => 'en'` thành `'fallback_locale' => 'vi'` trong `config/app.php`
  - Lý do: Đảm bảo khi bản dịch Tiếng Anh (`en`) bỏ trống, Laravel tự động lấy Tiếng Việt (`vi`) làm ngôn ngữ dự phòng.
- **[CMS-I18N-03] `config/translatable.php:81`** (Trụ cột 1 (Translatable Fallback)):
  - Hành động: Cấu hình `'fallback_locale' => 'vi'` trong `config/translatable.php`
  - Lý do: Kích hoạt cơ chế tự động trả về giá trị Tiếng Việt (`vi`) khi một thuộc tính Tiếng Anh (`en`) bị bỏ trống.
- **[CMS-I18N-04] `app/Http/Controllers/Frontend/Auth/UserAuthController.php:756`** (Trụ cột 4 (Locale Cache Isolation)):
  - Hành động: Gắn hậu tố ngôn ngữ `_{$locale}` (`app()->getLocale()`) vào khóa Cache `'otp_' . $phone` và xóa cả 2 ngôn ngữ `['vi', 'en']` khi `Cache::forget`
  - Lý do: Khóa cache hiện tại không phân biệt ngôn ngữ — khách chuyển sang Tiếng Anh (`EN`) sẽ bị trả về dữ liệu Tiếng Việt (`VI`) đã lưu trong cache.
- **[CMS-I18N-05] `app/Http/Controllers/Frontend/Auth/UserAuthController.php:774`** (Trụ cột 4 (Locale Cache Isolation)):
  - Hành động: Gắn hậu tố ngôn ngữ `_{$locale}` (`app()->getLocale()`) vào khóa Cache `'otp_' . $phone` và xóa cả 2 ngôn ngữ `['vi', 'en']` khi `Cache::forget`
  - Lý do: Khóa cache hiện tại không phân biệt ngôn ngữ — khách chuyển sang Tiếng Anh (`EN`) sẽ bị trả về dữ liệu Tiếng Việt (`VI`) đã lưu trong cache.

### 3. Quy Tắc Vàng Bắt Buộc Về Lưu Trữ & Fallback `EN -> VI`:
1. Khi Admin tạo mới hoặc cập nhật bản ghi trong CMS mà chỉ nhập Tiếng Việt (`vi`) hoặc để trống các ô Tiếng Anh (`en`), hệ thống **tuyệt đối không được báo lỗi Validation 422 hay lỗi SQL NOT NULL**.
2. Khi trả dữ liệu ra Frontend ở ngôn ngữ `en` (`app()->getLocale() === 'en'`), bất kỳ trường văn bản, nhánh JSON hay cấu hình `Config` nào có giá trị `en` rỗng/null đều phải **tự động fallback lấy giá trị `vi` của ô tiếng Việt tương ứng**.
3. Mọi khóa `Cache::remember` ở Frontend Controller & Middleware phải có hậu tố `_{$locale}`, và `Cache::forget` trong Backend phải xóa sạch cả `vi` lẫn `en`.

### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy lại `python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .` đạt `4/4 PASS`.
- Chạy `php artisan test` xanh 100%.
```
