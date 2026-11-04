# Proposal: Kích Hoạt & Hoàn Thiện Hệ Thống Đa Ngôn Ngữ Song Ngữ (VIE / ENG) Cho HH Maker

## Why
Hiện tại website HH Maker chỉ đang bật locale mặc định là Tiếng Việt (`'vi'`). Cấu hình `config/app.php` chỉ chứa `'locales' => ['vi']`, dẫn tới hệ thống routing song ngữ của gói `codezero/laravel-localized-routes` chỉ sinh ra các route tiếng Việt (ví dụ: `vi.home`, `vi.products.index`). Người dùng quốc tế hoặc đối tác khi muốn xem nội dung tiếng Anh không có đường dẫn riêng (`/en/...`) và nút chuyển đổi ngôn ngữ `[VIE | ENG]` trên Header (Figma Node 2380:1477) chưa thể chuyển qua lại giữa hai ngôn ngữ một cách mượt mà và toàn diện.

Để đáp ứng yêu cầu kinh doanh, nâng cao trải nghiệm người dùng toàn cầu và chuẩn hóa thiết kế theo Figma Node 2380:1477, việc kích hoạt hệ thống đa ngôn ngữ song ngữ (VIE / ENG) là vô cùng cấp thiết:
1. Cho phép truy cập cả tiếng Việt (`/`, `/san-pham`, `/dich-vu`, `/gioi-thieu`, `/quy-trinh`, `/lien-he`) và tiếng Anh (`/en`, `/en/products`, `/en/services`, `/en/about-us`, `/en/process`, `/en/contact`).
2. Tinh chỉnh capsule toggle language switcher `[VIE | ENG]` trên Header chuẩn thiết kế Figma Node 2380:1477 với badge sáng `#FAFAFA`, chữ tối `#111322` cho locale đang chọn và chữ trắng tương phản cho locale còn lại.
3. Cung cấp từ điển UI song ngữ chuẩn xác (`resources/lang/vi/ui.php` và `resources/lang/en/ui.php`), đồng bộ các nhãn menu, footer, CTA và các thành phần giao diện.
4. Tích hợp biên dịch và kiểm thử tự động E2E với Playwright để đảm bảo độ tin cậy 100%.

## What Changes
1. **Cấu hình Backend Laravel**:
   - `config/app.php`: Bổ sung `'en'` vào mảng `'locales' => ['vi', 'en']`.
   - `resources/lang/en/routes.php`: Cập nhật route translation tiếng Anh chuẩn (`home`, `contact`, `products`, `about-us`, `services`, `posts`, `quy-trinh` => `process`, `search`, `policies`).
   - `resources/lang/vi/routes.php`: Bổ sung `'quy-trinh' => 'quy-trinh'` và `'policies' => 'chinh-sach'`.
   - Tạo bộ từ điển `resources/lang/vi/ui.php` và `resources/lang/en/ui.php` hỗ trợ đầy đủ các khóa giao diện (Header menu, Footer, CTA, Auth...).
   - Chạy lệnh sinh bản dịch và Ziggy routes:
     `php artisan vue-i18n:generate`
     `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`
2. **Cập nhật Frontend Header Component (`Header.vue`)**:
   - Tinh chỉnh nút chuyển đổi ngôn ngữ `[VIE | ENG]` theo chuẩn Figma Node 2380:1477:
     * Locale `vi`: Badge `#FAFAFA` ôm `VIE` chữ `#111322`, `ENG` chữ trắng.
     * Locale `en`: Badge `#FAFAFA` ôm `ENG` chữ `#111322`, `VIE` chữ trắng.
   - Cập nhật hàm `switchLocale(lang)`:
     * Chuyển đổi linh hoạt giữa `/` và `/en` hoặc theo URL ánh xạ tương ứng.
     * Tận dụng route name Ziggy và Inertia visit mượt mà, fallback sang `window.location.href`.
   - Đảm bảo 5 nav tabs và nút CTA sử dụng `tt(...)` hiển thị đúng ngôn ngữ theo locale.
   - Cập nhật helper `tt(text)` trong `app.js` và `ssr.js` để tìm kiếm thông minh cả khóa root, khóa `ui.*` và dictionary trực tiếp.
3. **Cập nhật Footer (`Footer.vue`)**:
   - Chuyển đổi các link cố định sang `route(...)` có hỗ trợ locale (`route('products.index')`, `route('services.index')`, `route('histories.index')`, `route('process.index')`, `route('policies.index')`).
   - Bổ sung `tt(...)` cho các tiêu đề mục và liên kết chính sách.
4. **Biên dịch Frontend**:
   - Thực thi `yarn frontend:build` thành công với exit code 0.
5. **Kiểm thử E2E Playwright**:
   - Tạo bài test `tests/e2e/multilingual-switch.spec.ts` kiểm thử chuyển đổi qua lại giữa VIE và ENG, xác nhận URL, trạng thái active badge, nội dung menu và chụp ảnh màn hình lưu vào `tests/e2e/screenshots/`.

## Capabilities

### New Capabilities
- `multilingual-system-i18n`: Hệ thống đa ngôn ngữ song ngữ Việt - Anh toàn diện cho website HH Maker, kết hợp Laravel routing, Ziggy, Vue I18n và Figma Header capsule switcher.

### Modified Capabilities
<!-- Không có -->

## Impact
- `config/app.php`: Thêm `'en'` vào `'locales'`.
- `resources/lang/en/routes.php` & `resources/lang/vi/routes.php`: Cập nhật ánh xạ route song ngữ.
- `resources/lang/en/ui.php` & `resources/lang/vi/ui.php`: Bộ từ điển giao diện song ngữ.
- `public/build/locales/i18n.js`: Tự động tái tạo qua `php artisan vue-i18n:generate`.
- `public/build/ziggy.frontend.js`: Tự động tái tạo qua `php artisan ziggy:generate`.
- `resources/Frontend/js/app.js` & `resources/Frontend/js/ssr.js`: Nâng cấp helper `tt(text)`.
- `resources/Frontend/js/Components/Header.vue`: Hoàn thiện capsule switcher Node 2380:1477 và `switchLocale(lang)`.
- `resources/Frontend/js/Components/Footer.vue`: Chuẩn hóa link và text đa ngôn ngữ.
- `tests/e2e/multilingual-switch.spec.ts`: Test Playwright kiểm chứng toàn bộ luồng.
- Tuyệt đối không xóa file, không thay đổi database.
