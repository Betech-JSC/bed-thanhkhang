# Tasks: Kích Hoạt & Hoàn Thiện Hệ Thống Đa Ngôn Ngữ Song Ngữ (VIE / ENG) Cho HH Maker

## 1. Khởi Tạo OpenSpec Change
- [x] 1.1 Tạo thư mục `openspec/changes/setup-multilingual-system-i18n/` chứa `proposal.md`, `specs/multilingual/spec.md`, `design.md`, `tasks.md`.
- [x] 1.2 Thực thi `npx openspec validate setup-multilingual-system-i18n` xác nhận spec hợp lệ 100%.

## 2. Cấu Hình Backend Laravel & Sinh Bản Dịch
- [x] 2.1 Cập nhật `config/app.php`: thêm `'en'` vào mảng `'locales' => ['vi', 'en']`.
- [x] 2.2 Cập nhật `resources/lang/en/routes.php` với đầy đủ các route tiếng Anh (`home`, `contact`, `products`, `about-us`, `services`, `posts`, `quy-trinh` => `process`, `search`, `policies`).
- [x] 2.3 Cập nhật `resources/lang/vi/routes.php`: bổ sung `'quy-trinh' => 'quy-trinh'` và `'policies' => 'chinh-sach'`.
- [x] 2.4 Tạo từ điển UI `resources/lang/vi/ui.php` và `resources/lang/en/ui.php` chứa bản dịch đầy đủ cho Header menu, Footer, nút CTA và các nhãn dùng chung.
- [x] 2.5 Chạy `php artisan vue-i18n:generate` để tạo `public/build/locales/i18n.js`.
- [x] 2.6 Chạy `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend` để cập nhật bảng định tuyến Ziggy có cả prefix `vi.*` và `en.*`.

## 3. Tinh Chỉnh Frontend Components & Helpers
- [x] 3.1 Cập nhật helper `tt(text)` trong `resources/Frontend/js/app.js` và `resources/Frontend/js/ssr.js` để tìm kiếm thông minh đa tầng.
- [x] 3.2 Tinh chỉnh nút chuyển đổi ngôn ngữ `[VIE | ENG]` (Figma Node 2380:1477) trong `resources/Frontend/js/Components/Header.vue` cho cả Desktop và Mobile Drawer.
- [x] 3.3 Tinh chỉnh hàm `switchLocale(lang)` và `isMenuActive(menu)` trong `Header.vue` để xử lý mượt mà cả route tiếng Việt lẫn tiếng Anh `/en/...`.
- [x] 3.4 Cập nhật `resources/Frontend/js/Components/Footer.vue` dùng `route(...)` và `tt(...)` cho các link và nhãn chân trang.

## 4. Biên Dịch Frontend & Kiểm Tra
- [x] 4.1 Chạy `yarn frontend:build` và đảm bảo exit code 0.

## 5. Viết Test Playwright E2E & Chụp Ảnh Nghiệm Thu
- [x] 5.1 Tạo file test `tests/e2e/multilingual-switch.spec.ts` kiểm tra trạng thái mặc định Tiếng Việt, chuyển sang Tiếng Anh, chuyển ngược lại Tiếng Việt và truy cập trực tiếp route tiếng Anh `/en/services`, `/en/products`.
- [x] 5.2 Chạy test Playwright và chụp ảnh màn hình lưu tại `tests/e2e/screenshots/multilingual-vie.png` và `tests/e2e/screenshots/multilingual-eng.png`.
- [x] 5.3 Đánh dấu 100% các task `[x]` trong `tasks.md` và báo cáo kết quả chi tiết kèm bằng chứng terminal log.
