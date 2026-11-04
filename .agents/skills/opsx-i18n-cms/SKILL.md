---
name: opsx-i18n-cms
description: Hub điều phối Đa Ngôn Ngữ Backend CMS (VI / EN) & Tự Động Fallback EN -> VI theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke). Kiểm toán 4 trụ cột Đa ngôn ngữ CMS (Config locales & Nút chuyển VI|EN trong Admin, Ô nhập liệu EN trên mọi Form.vue & Configs.vue cho cả trường thường lẫn JSON, Chống lỗi Validation 422/SQL khi bỏ trống tiếng Anh & Tự động Fallback EN -> VI 100%, và Khóa Cache tách biệt theo _{locale}). Chạy song song an toàn 100% với /opsx-i18n-fe nhờ khóa ranh giới file.
---

# OPSX i18n CMS — Đoạn Hội Thoại Trung Tâm Điều Phối Đa Ngôn Ngữ Backend CMS (`VI / EN` & Auto-Fallback `EN -> VI`)

Skill này biến đoạn hội thoại hiện tại thành **Đoạn Hội Thoại Trung Tâm (Hub Conversation)** chuyên kiểm toán, lập kế hoạch và sinh **Bộ Handoff Prompts** để triển khai **Đa Ngôn Ngữ Backend CMS (`VI / EN`)** cho dự án Laravel + Inertia.js + Vue 3 (BED CMS).

Đặc biệt, `/opsx-i18n-cms` được thiết kế để **chạy song song hoàn toàn độc lập với `/opsx-i18n-fe`** (một bên làm Backend CMS + Admin Forms, một bên làm Frontend Vue Pages + `vi.json`/`en.json`) mà **tuyệt đối không đụng chạm hay xung đột file**.

---

## 1. Khóa Ranh Giới File Khi Chạy Song Song (`Parallel File Isolation Boundary`)

Để chạy song song cùng lúc với `/opsx-i18n-fe` (hoặc `/opsx-pixel-perfect`), mọi Handoff Prompt của `/opsx-i18n-cms` **BẮT BUỘC** tuân thủ ranh giới file sau:

- ✅ **`[ALLOWED TO EDIT]` (Được phép chỉnh sửa)**:
  - `config/app.php`, `config/translatable.php`, `config/cms.php`
  - `app/Models/**/*.php`
  - `app/Http/Controllers/Backend/**/*.php`
  - `app/Http/Controllers/Frontend/**/*.php`
  - `app/Http/Middleware/HandleInertiaFrontendRequests.php`, `app/Http/Middleware/HandleInertiaRequests.php`
  - `database/migrations/**/*.php`
  - `resources/Backend/js/**/*.vue`
  - `packages/bed-package-essentials/core/**` (nếu cần tinh chỉnh trait `HasCrudActions` / `Translatable`)
- 🔒 **`[LOCKED FOR PARALLEL SAFETY]` (CẤM CHỈNH SỬA — Thuộc quyền của `/opsx-i18n-fe`)**:
  - `resources/Frontend/js/**`
  - `resources/Frontend/lang/**` (hoặc `lang/{vi,en}.json`)

---

## 2. 4 Trụ Cột Đa Ngôn Ngữ Backend CMS (`4 Pillars of CMS i18n`)

### Trụ cột 1 — Cấu Hình Locale & Hiển Thị Nút Chuyển `VI | EN` Trong Admin CMS
1. **`config/app.php`**:
   - `'locale' => 'vi'` (mặc định Tiếng Việt).
   - `'locales' => ['vi', 'en']` (**BẮT BUỘC** phải có cả `'vi'` và `'en'` để thanh công cụ chuyển đổi ngôn ngữ trên Admin Sidebar `SidebarItems.vue` / `Header.vue` tự động hiển thị nút `VI | EN`).
   - `'fallback_locale' => 'vi'` (**BẮT BUỘC** là `'vi'` để mọi bản ghi chưa dịch tiếng Anh luôn lấy dữ liệu tiếng Việt làm gốc).
2. **`config/translatable.php`**:
   - `'use_fallback' => true`
   - `'use_property_fallback' => true`
   - `'fallback_locale' => 'vi'`
   - `'use_fallback_frontend' => true`
   - `'use_fallback_backend' => true`

### Trụ cột 2 — Ô Nhập Liệu Tiếng Anh (`EN`) Trên Mọi Form Backend CMS & `Configs.vue`
1. **Model & Bảng Dịch (`*_translations` / `$translatedAttributes`)**:
   - Mọi trường văn bản hiển thị ra UI (`title`, `slug`, `description`, `excerpt`, `content`, `seo_*`) và **mọi trường mảng JSON chứa nội dung chữ** (`benefits`, `steps`, `faqs`, `highlights`, `items`, `specifications`, `operating_systems`, `filter_attributes`, `pdp_metadata`, `sliders`, `content_tabs`) trên các Model phục vụ trang Figma phải hỗ trợ lưu trữ đa ngôn ngữ (`$translatedAttributes` hoặc cấu trúc song ngữ `vi`/`en` trong JSON).
2. **Biểu mẫu Admin (`resources/Backend/js/Pages/**/Form.vue`)**:
   - Khi Admin bấm chuyển ngôn ngữ sang `EN` (hoặc chuyển tab `VI / EN` trên Form), mọi trường văn bản và mảng JSON nội dung phải có ô nhập liệu cho Tiếng Anh.
3. **Trang Cấu Hình Chung (`resources/Backend/js/Pages/Configs/Index.vue` / `Configs.vue` & `app/Models/Config.php`)**:
   - Các trường văn bản thương hiệu (`company_name`, `site_name`, `slogan`, `address`, `working_hours`, `footer_description`, `copyright`, `about_title`, `about_description`...) phải có ô nhập Tiếng Anh (`*_en` hoặc phân nhánh theo locale) trong trang Cấu hình Admin.

### Trụ cột 3 — Chống Lỗi Validation Khi Lưu & Tự Động Fallback `EN -> VI` 100%
1. **Khi Admin bấm Lưu (Create / Update)**:
   - Nếu Admin chỉ nhập Tiếng Việt (`vi`) mà chưa nhập Tiếng Anh (`en`), hoặc đang ở tab `EN` mà để trống một số ô: hệ thống **tuyệt đối không được báo lỗi Validation 422 hay lỗi SQL `NOT NULL`**.
   - Tự động điền hoặc fallback giá trị từ bản dịch `vi` sang `en` (hoặc cho phép `nullable` và dùng `use_property_fallback` về `vi`).
2. **Khi Trả Dữ Liệu Ra Frontend (`Frontend\*Controller.php` & `HandleInertiaFrontendRequests.php`)**:
   - Mọi trường thuộc tính đơn lẻ, mọi phần tử trong mảng JSON (`benefits`, `steps`, `faqs`, `pdp_metadata`...) và mọi khóa trong `Config::get*BrandSettings()` khi ở ngôn ngữ `en` nếu giá trị `en` rỗng/null thì **tự động fallback 100% về giá trị `vi` của ô tiếng Việt tương ứng** (không bao giờ hiện trắng hay `null` ngoài giao diện).

### Trụ cột 4 — Khóa Cache Tách Biệt Theo Ngôn Ngữ (`_{locale}`)
1. **Khi Lưu Cache (`Cache::remember` / `Cache::put`)**:
   - Mọi khóa cache trong `Frontend\*Controller.php` và `HandleInertiaFrontendRequests.php` **BẮT BUỘC** phải gắn hậu tố `_{$locale}` (ví dụ: `"home_data_{$locale}"`, `"posts_index_{$locale}"`, `"inertia_layout_shared_data_{$locale}"`).
2. **Khi Xóa Cache (`Cache::forget`)**:
   - Khi Admin bấm Lưu trong CMS, hàm xóa cache trong `Model::booted()` và `Backend\*Controller` phải lặp qua toàn bộ `['vi', 'en']` (`config('app.locales', ['vi', 'en'])`) để xóa sạch cache của cả 2 ngôn ngữ tức thì.

---

## 3. Quy Trình 4 Giai Đoạn Của Đoạn Hội Thoại Trung Tâm (`4-Step Hub Workflow`)

### 🔍 Giai Đoạn 1 — Chạy Kiểm Toán Tự Động Đa Ngôn Ngữ CMS (`Strict Read-Only`)
```bash
# Quét toàn bộ các Model phục vụ các trang Figma Active
python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .

# Hoặc chỉ định rõ danh sách các trang Figma của dự án
python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py . --pages Home,About,Products,Services,Process,Posts,Contact
```
Đọc báo cáo `scratch/i18n-cms-audit-report.md` để nắm toàn bộ hiện trạng 4 Trụ cột Đa ngôn ngữ CMS.

### 📋 Giai Đoạn 2 — Trình Bày Kết Quả Kiểm Toán + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` & Dừng Chờ Confirm
Trình bày cho người dùng:
1. **Bảng 1**: Trạng thái `config/app.php` & `config/translatable.php` (Nút chuyển `VI | EN` trong Admin & cấu hình Fallback).
2. **Bảng 2**: Danh sách các Model, trường JSON/văn bản và `Form.vue` / `Configs.vue` còn thiếu ô nhập Tiếng Anh (`EN`) hoặc thiếu cơ chế Fallback `EN -> VI`.
3. **Bảng 3**: Danh sách các khóa `Cache::remember` trong Frontend Controller còn thiếu hậu tố `_{$locale}`.
4. **Bảng 4**: Bảng Quyết Định **`[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ NGUYÊN GÌ`** -> **DỪNG LẠI CHỜ USER CONFIRM**.

### 🚀 Giai Đoạn 3 — Xuất Bộ "Handoff Prompts" Có Khóa Ranh Giới File (`Spoke Prompts`)
Sau khi người dùng Confirm, xuất các khối **Handoff Prompts (Gói 1, Gói 2, Gói 3)** đã được sinh sẵn trong Mục 6 của `scratch/i18n-cms-audit-report.md` (có ghi rõ `[ALLOWED TO EDIT]` và `[LOCKED FOR PARALLEL SAFETY: resources/Frontend/**]`) để đem sang các đoạn hội thoại con thực thi.

### ✅ Giai Đoạn 4 — Quay Về Hub Nghiệm Thu E2E
Khi các đoạn hội thoại con hoàn tất, chạy lại:
```bash
python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .
php artisan test
```
Đảm bảo 4/4 Trụ cột đạt `PASS`, `0` khóa Cache thiếu `_{$locale}`, và lưu dữ liệu Admin CMS hoạt động trơn tru trên cả `VI` lẫn `EN`.
