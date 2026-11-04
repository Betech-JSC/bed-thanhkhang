# Proposal: setup-home-and-services-multilingual-i18n

## 1. Giới thiệu & Bối cảnh
Dự án HH Maker đã xây dựng thành công nền tảng đa ngôn ngữ cốt lõi (Header capsule switcher, Footer, Catalog Sản phẩm). Tuy nhiên, hai phân hệ trọng tâm đối với khách hàng doanh nghiệp và quốc tế là:
1. **Trang chủ (`/` <-> `/en`)**: Các section gồm Hero banner, Sản phẩm nổi bật, Tổng quan dịch vụ, Quy trình chế tác 5 bước, Câu chuyện nhà sáng lập (Founder Story) và Form tư vấn nhanh (Contact Section) vẫn còn nhiều chuỗi ký tự Tiếng Việt hardcoded.
2. **Hệ thống Dịch vụ (`/dich-vu` <-> `/en/services`, `/dich-vu/{slug}` <-> `/en/services/{slug}`)**: Cả trang Danh sách Dịch vụ (Services Index) với 3 khối dịch vụ Z-pattern và trang Chi tiết Dịch vụ (Service Detail) với khung minh họa kỹ thuật, mục lục TOC sticky, bài viết 4 phần, checklist chuẩn bị và dịch vụ liên quan chưa được song ngữ hóa toàn diện.

Khi người dùng quốc tế hoặc doanh nghiệp nước ngoài chuyển sang ngôn ngữ Tiếng Anh (`ENG`), toàn bộ Trang chủ và Hệ thống Dịch vụ cần hiển thị 100% Tiếng Anh chuẩn thuật ngữ chuyên ngành in 3D (FDM, Resin, Slicing, Rapid Prototyping, Industrial Tolerances) và chế tác cơ khí chính xác.

## 2. Mục tiêu cốt lõi
1. **Mở rộng Từ Điển UI Song Ngữ (`resources/lang/{vi,en}/ui.php`)**:
   - Bổ sung toàn diện các token: Eyebrows, Slogan, Hero CTA, Section Headings, 5 bước quy trình sản xuất, Câu chuyện thương hiệu Founder, Chỉ số hoạt động, Form liên hệ tư vấn nhanh.
   - Bổ sung các token chuyên sâu về Dịch vụ in 3D: Các khối dịch vụ Z-pattern, Bảng chuẩn bị vs hoàn thiện, Mục lục TOC, Các bước thiết kế, Checklist kỹ thuật, Dịch vụ liên quan, Banner lời mời sáng tạo.
2. **Song ngữ hóa 100% Trang chủ (`Home/Index.vue` & các sub-components)**:
   - `Index.vue`: Cập nhật prop bindings qua `tt(...)`, localized pageTitle & pageDescription.
   - `HeroSection.vue`: Eyebrow, Heading, Description, CTA buttons, Text SVG xoay tròn ("FROM IDEAS TO REALITY").
   - `FeaturedProductsCarousel.vue`: Heading "FEATURED PRODUCTS", Tên sản phẩm mẫu, điều hướng carousel.
   - `ServicesSection.vue`: Heading "SERVICES OVERVIEW", 3 card dịch vụ (Thiết kế 3D, Gia công 3D, Làm đồ custom) với mô tả tiếng Anh và localized route URL.
   - `BrandStorySection.vue`: Subtitle "FROM HH MAKER FOUNDER", Title "BRINGING CREATIVITY BEYOND THE SCREEN", 2 đoạn văn chia sẻ triết lý và 3 chỉ số hoạt động.
   - `ProcessSection.vue`: Heading "STEPS IN THE PROFESSIONAL & TRANSPARENT WORKFLOW AT HH MAKER", Lời dẫn, 5 bước chi tiết dạng collapsible.
   - `ContactSection.vue`: Title "CONNECT WITH HH MAKER", Description, Labels và Placeholders của Form tư vấn nhanh.
3. **Song ngữ hóa 100% Hệ thống Dịch vụ (`Services/Index.vue` & `Services/Show.vue`)**:
   - `Services/Index.vue`: Eyebrow "HH MAKER 3D PRINTING SERVICES", Title "ONE IDEA - MULTIPLE POSSIBILITIES", Lời dẫn, Song ngữ hóa mảng `defaultServices` (cung cấp Tiếng Anh khi `currentLocale === 'en'`).
   - `ServiceBlock.vue`: Category badge, Tags kỹ thuật, Tiêu đề, Phụ đề, Mô tả, Bảng 2 cột ("YOU PREPARE" vs "WE CO-CREATE"), Buttons CTA.
   - `Services/Show.vue`: Localized pageTitle, pageDescription, Hero image mapping.
   - `ServiceHero.vue`: Breadcrumbs ("Home / Services / [Title]"), Headline, Khung minh họa kỹ thuật ("STARTING FROM" vs "CLARIFYING TOGETHER").
   - `ServiceToc.vue`: Sidebar header "IN THIS ARTICLE" / "TABLE OF CONTENTS", 4 mục điều hướng cuộn trang.
   - `ServiceArticleContent.vue`: 4 section kỹ thuật (Khi nào cần thiết kế 3D?, Từ ý tưởng tới mô hình 3D, Bạn cần chuẩn bị gì?, Cùng hoàn thiện trước khi gia công), 4 bước thiết kế, Card checklist thông số kỹ thuật, Callout lưu ý.
   - `ServiceRelated.vue`: Heading "Related Services", Card dịch vụ liên quan, Link "Explore service".
   - `ServiceCta.vue`: Title, Description, Button "Get Consultation Now".
4. **Bổ sung Dữ liệu Tiếng Anh trong Database & Controllers**:
   - Chèn an toàn bản ghi `locale = 'en'` vào bảng `service_translations` cho 3 dịch vụ ID 1, 2, 3 (Tuyệt đối KHÔNG drop/truncate/delete database).
   - Localize SEO metadata (title, description, og:tags) trong `HomeController.php` và `ServiceController.php` theo `current_locale()`.
5. **Biên Dịch & Kiểm Thử Playwright E2E**:
   - Chạy `php artisan vue-i18n:generate` & `php artisan ziggy:generate`.
   - Build frontend xanh sạch (`yarn frontend:build`).
   - Viết test Playwright E2E `tests/e2e/home-services-multilingual.spec.ts` kiểm thử chuyển đổi locale tại Trang chủ, Trang danh sách dịch vụ và Trang chi tiết dịch vụ, chụp ảnh màn hình bằng chứng thực tế.

## 3. Tác động kiến trúc
- Không phá vỡ bất kỳ schema database nào; chỉ thêm bản ghi ngôn ngữ tiếng Anh vào bảng đa ngữ `service_translations` đã có sẵn của gói Astrotomic Translatable.
- Tận dụng triệt để `tt(...)` mixin và Vue I18n đã tích hợp ở Phase 1.
- Mọi route chuyển hướng đều hỗ trợ tiền tố locale thông qua `route(...)` của Ziggy và middleware `HandleInertiaFrontendRequests`.
