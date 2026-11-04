# Design: Kiến Trúc Hệ Thống Đa Ngôn Ngữ Song Ngữ (VIE / ENG)

## 1. Tổng Quan Kiến Trúc
Hệ thống đa ngôn ngữ của HH Maker kết hợp giữa backend Laravel (gói `codezero/laravel-localized-routes` và Laravel Localization) và frontend Vue 3 (gói `@intlify/unplugin-vue-i18n`, `vue-i18n` và Ziggy routing).

```mermaid
graph TD
    A[User Request] --> B{URL Prefix}
    B -->|'/' or '/san-pham'| C[Backend Locale: 'vi']
    B -->|'/en' or '/en/products'| D[Backend Locale: 'en']
    C --> E[HandleInertiaFrontendRequests Middleware]
    D --> E
    E --> F[Inertia Shared Props: locale.current, route, etc.]
    F --> G[Vue 3 App Setup]
    G --> H[createI18n with messages from unplugin-vue-i18n]
    G --> I[Ziggy Route Helper: vi.* and en.*]
    H --> J[Header.vue - Node 2380:1477 Capsule Toggle]
    H --> K[Nav Tabs tt() and Footer tt()]
    J -->|switchLocale| L[Inertia.visit to Target Locale Route]
```

## 2. Chi Tiết Thành Phần

### 2.1. Backend Localization & Routing
- **Cấu hình**: `config/app.php` thiết lập `'locales' => ['vi', 'en']`.
- **Gói `codezero/laravel-localized-routes`**:
  - `omit_url_prefix_for_locale`: lấy từ `config('app.locale_default_frontend')` tức `'vi'`.
  - Locale `'vi'` sinh các route gốc: `/`, `/san-pham`, `/dich-vu`, `/gioi-thieu`, `/quy-trinh`, `/lien-he`.
  - Locale `'en'` sinh các route có tiền tố `/en`: `/en`, `/en/products`, `/en/services`, `/en/about-us`, `/en/process`, `/en/contact`.
- **Ánh xạ Routes**:
  - `resources/lang/vi/routes.php`: Ánh xạ slug tiếng Việt.
  - `resources/lang/en/routes.php`: Ánh xạ slug tiếng Anh.

### 2.2. Bộ Từ Điển UI & Tạo Bản Dịch
- `resources/lang/vi/ui.php` & `resources/lang/en/ui.php`: Lưu trữ các cặp key-value dịch thuật giao diện.
- Lệnh Artisan `php artisan vue-i18n:generate`: Quét toàn bộ thư mục `resources/lang` để sinh file JSON/JS tương thích `public/build/locales/i18n.js`.
- Lệnh Artisan `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`: Sinh file cấu hình Ziggy routes chứa cả `vi.*` và `en.*`.

### 2.3. Helper `tt(text)` Thông Minh Trên Frontend
Cập nhật `tt(text)` trong `resources/Frontend/js/app.js` và `ssr.js` để tìm kiếm đa tầng:
1. `this.$te(text)`: Nếu khớp trực tiếp root key.
2. `this.$te('ui.' + text)`: Nếu khớp key nằm trong nhóm `ui`.
3. Kiểm tra trực tiếp trong `messages[locale].ui[text]` để chống lỗi khi key có chứa dấu cách hoặc ký tự đặc biệt.
4. Fallback về chính `text` nếu không tìm thấy bản dịch.

### 2.4. Capsule Switcher Figma Node 2380:1477
Container kích thước 96px x 34px, bo góc 8px với viền `#5D6B98`:
- Trạng thái `vi`:
  - `VIE`: nền `#FAFAFA`, chữ `#111322`, bo góc 4px, 36px x 24px, font 12px semi-bold.
  - `ENG`: chữ trắng `#FFFFFF`, font 14px semi-bold.
- Trạng thái `en`:
  - `ENG`: nền `#FAFAFA`, chữ `#111322`, bo góc 4px, 42px x 24px, font 12px semi-bold.
  - `VIE`: chữ trắng `#FFFFFF`, font 14px semi-bold.

### 2.5. Cơ Chế Chuyển Ngôn Ngữ `switchLocale(lang)`
1. Đối chiếu route name hiện tại (`vi.products.index` -> `en.products.index`).
2. Nếu có route Ziggy tương ứng, điều hướng thẳng qua `this.route(targetRouteName)`.
3. Nếu không, thực hiện thay thế đường dẫn thông minh (chuyển đổi giữa `/san-pham` và `/en/products`...) hoặc thêm/bớt tiền tố `/en`.
4. Gọi `this.$inertia.visit(newPath)` cho trải nghiệm SPA nhanh, không reload trang không cần thiết.
