# Tasks: Toàn diện Đa ngôn ngữ Song ngữ 100% cho 4 Trang Thông tin

## 1. Thiết kế & Chuẩn bị Từ điển Song Ngữ
- [x] 1.1 Mở rộng `resources/lang/vi/ui.php` với đầy đủ tokens cho Trang Giới thiệu (About Us), Quy trình (Process), Liên hệ (Contact), và Chính sách (Policies).
- [x] 1.2 Mở rộng `resources/lang/en/ui.php` với các bản dịch tiếng Anh tự nhiên, chuẩn thuật ngữ công nghệ in 3D.
- [x] 1.3 Chạy `php artisan vue-i18n:generate` & `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`.

## 2. Song ngữ hóa Trang Giới thiệu (About Us)
- [x] 2.1 Cập nhật `resources/Frontend/js/Pages/About/Index.vue`: bọc `tt(...)` cho tiêu đề, meta tags, hero banner fallback, câu chuyện founder, 3 thẻ giá trị và CTA.
- [x] 2.2 Cập nhật `app/Http/Controllers/Frontend/HistoryController.php`: bổ sung SEO metadata localized (`title`, `description`).

## 3. Song ngữ hóa Trang Quy trình (Process)
- [x] 3.1 Cập nhật `resources/Frontend/js/Pages/Process/Index.vue`: cấu hình tiêu đề trang, meta description và mảng 5 bước với `tt(...)`.
- [x] 3.2 Cập nhật `ProcessHero.vue`: bọc `tt(...)` cho category, headline, description và button CTA.
- [x] 3.3 Cập nhật `ProcessStepper.vue`: bọc `tt(...)` cho tiêu đề các bước.
- [x] 3.4 Cập nhật `ProcessStepItem.vue`: bọc `tt(...)` cho nhãn 2 cột "BẠN CHUẨN BỊ" và "HH MAKER THỰC HIỆN".
- [x] 3.5 Cập nhật `ProcessFaq.vue`: bọc `tt(...)` cho tiêu đề và các câu hỏi, câu trả lời FAQ.
- [x] 3.6 Cập nhật `app/Http/Controllers/Frontend/ProcessController.php`: bổ sung SEO metadata localized.

## 4. Song ngữ hóa Trang Liên hệ (Contact)
- [x] 4.1 Cập nhật `resources/Frontend/js/Pages/Contact.vue`: bọc `tt(...)` cho 3 thẻ kênh liên hệ, form 6 trường, placeholder, validation messages, modal hoàn tất và FAQ.
- [x] 4.2 Cập nhật `app/Http/Controllers/Frontend/AgencyController.php`: bổ sung SEO metadata localized.

## 5. Song ngữ hóa & Tinh chỉnh Trang Chính sách (Policies)
- [x] 5.1 Cập nhật `app/Http/Controllers/Frontend/PolicyController.php`: hỗ trợ SEO localized và cung cấp bộ dữ liệu 3 chính sách chuẩn song ngữ (Điều khoản dịch vụ, Chính sách bảo mật, Bảo hành in 3D).
- [x] 5.2 Cập nhật `resources/Frontend/js/Pages/Policy.vue`: nâng cấp giao diện dark-tech HH Maker, hỗ trợ `tt(...)` cho sidebar và nội dung chính sách.

## 6. Biên dịch, Kiểm thử Playwright E2E & Nghiệm thu
- [x] 6.1 Chạy `yarn frontend:build` để biên dịch toàn bộ bundle assets.
- [x] 6.2 Viết test suite Playwright `tests/e2e/informational-pages-multilingual.spec.ts` kiểm thử 4 trang trên 2 ngôn ngữ.
- [x] 6.3 Chạy test Playwright, chụp ảnh màn hình bằng chứng và xác nhận 100% test pass.
- [x] 6.4 Đồng bộ trạng thái `tasks.md` sang hoàn tất `[x]`.
