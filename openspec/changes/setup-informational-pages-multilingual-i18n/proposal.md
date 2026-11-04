# Proposal: Toàn diện Đa ngôn ngữ Song ngữ 100% cho 4 Trang Thông tin (About Us, Process, Contact, Policies)

## Why
Sau khi hoàn tất song ngữ hóa hệ thống cốt lõi và danh mục sản phẩm (Phase 1 & Phase 2), website HH Maker vẫn còn 4 trang thông tin quan trọng chưa được chuyển ngữ toàn diện:
1. **Trang Giới thiệu (About Us)** (`/gioi-thieu` <-> `/en/about-us`): Nội dung Hero banner fallback, Lý do khởi đầu sứ mệnh, Câu chuyện nhà sáng lập Lâm Hồng Hải, 3 thẻ giá trị triết lý chế tác và thống kê năng lực vẫn còn các đoạn văn bản cứng tiếng Việt.
2. **Trang Quy trình (Process)** (`/quy-trinh` <-> `/en/process`): Stepper lộ trình 5 bước, bảng so sánh chi tiết phối hợp giữa Maker và Khách hàng (Bạn chuẩn bị vs HH Maker thực hiện), cùng các câu hỏi thường gặp FAQ chưa hỗ trợ song ngữ.
3. **Trang Liên hệ (Contact)** (`/lien-he` <-> `/en/contact`): 3 thẻ liên lạc qua kênh kính, form tư vấn 6 trường (bao gồm placeholder, thông báo lỗi validation, nút gửi và modal chúc mừng thành công), cùng khối FAQ vẫn đang hardcoded tiếng Việt.
4. **Trang Chính sách (Policies)** (`/chinh-sach` <-> `/en/policies`): Danh mục sidebar các điều khoản và nội dung chi tiết (Điều khoản dịch vụ, Chính sách bảo mật, Bảo hành in 3D) cần được song ngữ hóa đồng bộ và tương thích với giao diện chuẩn dark-tech của HH Maker.
5. **Controllers & SEO Localized**: `HistoryController`, `ContactController` / `AgencyController`, và `PolicyController` cần truyền đầy đủ metadata SEO song ngữ (`title`, `description`, OpenGraph) tương ứng với từng `current_locale()`.

Việc hoàn tất Phase 3 sẽ đưa website HH Maker đạt chuẩn 100% song ngữ (Tiếng Việt và Tiếng Anh), mang lại trải nghiệm chuyên nghiệp, mượt mà cho khách hàng và đối tác quốc tế.

## What Changes
1. **Mở rộng Bộ Từ Điển UI Song Ngữ (`resources/lang/{vi,en}/ui.php`)**:
   - Bổ sung toàn bộ các chuỗi văn bản cho:
     - About Us: Tiêu đề hero, lý do thành lập, câu chuyện founder, 3 triết lý chế tác, thống kê số liệu.
     - Process: Banner quy trình, 5 bước chế tác, các đề mục "BẠN CHUẨN BỊ" / "HH MAKER THỰC HIỆN", và danh sách FAQ.
     - Contact: Tiêu đề kết nối, 3 thẻ kênh liên hệ, form tư vấn, validation messages, modal hoàn tất.
     - Policies: Tiêu đề điều khoản, nhãn menu chính sách (Điều khoản dịch vụ, Chính sách bảo mật, Bảo hành in 3D) và nội dung tương ứng.
2. **Cập nhật Giao diện Frontend 4 Trang**:
   - `resources/Frontend/js/Pages/About/Index.vue`: Sử dụng `tt(...)` cho tiêu đề, meta tags, các đoạn văn bản founder, 3 thẻ giá trị và CTA.
   - `resources/Frontend/js/Pages/Process/Index.vue` & Components (`ProcessHero.vue`, `ProcessStepper.vue`, `ProcessStepItem.vue`, `ProcessFaq.vue`): Sử dụng `tt(...)` cho stepper 5 bước, bảng so sánh phối hợp 2 cột, và FAQ accordion.
   - `resources/Frontend/js/Pages/Contact.vue`: Sử dụng `tt(...)` cho 3 thẻ kênh liên hệ, 6 trường form, validation errors, nút submit, toast modal thành công và FAQ.
   - `resources/Frontend/js/Pages/Policy.vue`: Bọc `tt(...)` cho sidebar menu điều khoản, tiêu đề, và nội dung chính sách theo phong cách dark-tech của HH Maker.
3. **Localized SEO trong Backend Controllers**:
   - `app/Http/Controllers/Frontend/HistoryController.php`: SEO metadata song ngữ cho Trang Giới thiệu.
   - `app/Http/Controllers/Frontend/AgencyController.php` & `ContactController.php`: SEO metadata song ngữ cho Trang Liên hệ.
   - `app/Http/Controllers/Frontend/PolicyController.php`: SEO metadata song ngữ và dữ liệu chính sách fallback song ngữ khi database trống.
4. **Biên Dịch & Sinh Tài Nguyên**:
   - Thực thi `php artisan vue-i18n:generate` cập nhật `public/build/locales/i18n.js`.
   - Thực thi `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`.
   - Thực thi `yarn frontend:build` biên dịch assets frontend sạch sẽ.
5. **Kiểm Thử Playwright E2E**:
   - Viết bài test `tests/e2e/informational-pages-multilingual.spec.ts` kiểm thử 4 trang trên cả 2 ngôn ngữ và chụp ảnh màn hình bằng chứng.

## Capabilities

### New Capabilities
- `informational-pages-multilingual`: Hệ thống song ngữ toàn diện 100% cho 4 trang thông tin (About Us, Process, Contact, Policies) bao gồm từ điển UI, reactive translations, validation messages và SEO metadata.

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/lang/vi/ui.php` & `resources/lang/en/ui.php`: Bổ sung các tokens dịch thuật cho 4 trang.
- `resources/Frontend/js/Pages/About/Index.vue`: Tích hợp `tt(...)`.
- `resources/Frontend/js/Pages/Process/Index.vue` và các component con: Tích hợp `tt(...)`.
- `resources/Frontend/js/Pages/Contact.vue`: Tích hợp `tt(...)` cho form, validation, và FAQ.
- `resources/Frontend/js/Pages/Policy.vue`: Tích hợp `tt(...)` và giao diện dark-tech chuẩn.
- `app/Http/Controllers/Frontend/HistoryController.php`: SEO song ngữ.
- `app/Http/Controllers/Frontend/AgencyController.php`: SEO song ngữ.
- `app/Http/Controllers/Frontend/PolicyController.php`: SEO song ngữ và fallback chính sách.
- `tests/e2e/informational-pages-multilingual.spec.ts`: Playwright test suite cho 4 trang.
- Đảm bảo an toàn cơ sở dữ liệu (không drop, không truncate) và không xóa file.
