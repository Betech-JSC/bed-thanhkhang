# Implementation Tasks: setup-home-and-services-multilingual-i18n

- [x] 1. Khởi tạo OpenSpec Change và Đặc tả Delta <!-- id: task-01-openspec-init -->
  - [x] Tạo `openspec/changes/setup-home-and-services-multilingual-i18n/proposal.md`
  - [x] Tạo `openspec/changes/setup-home-and-services-multilingual-i18n/specs/home-and-services/spec.md`
  - [x] Tạo `openspec/changes/setup-home-and-services-multilingual-i18n/design.md`
  - [x] Tạo `openspec/changes/setup-home-and-services-multilingual-i18n/tasks.md`
  - [x] Chạy `npx openspec validate setup-home-and-services-multilingual-i18n`

- [x] 2. Mở rộng Bộ Từ Điển UI Song Ngữ (`resources/lang/{vi,en}/ui.php`) <!-- id: task-02-dict-extension -->
  - [x] Bổ sung các token Trang chủ: Slogan, Hero, Featured Products, Services Overview, Process 5 steps, Founder Story, Contact Form
  - [x] Bổ sung các token Dịch vụ: Danh sách dịch vụ, 3 khối Z-pattern, Tags, Chuẩn bị vs Hoàn thiện, Sticky TOC, 4 Bước thiết kế, Checklist kỹ thuật, Dịch vụ liên quan, Banner CTA

- [x] 3. Cập nhật Giao diện Frontend Trang chủ (`Home/Index.vue` & Components) <!-- id: task-03-frontend-home -->
  - [x] `Home/Index.vue`: Localize pageTitle, pageDescription, dynamic props
  - [x] `HeroSection.vue`: Bọc `tt(...)` cho subtitle, title, description, buttons, text xoay tròn
  - [x] `FeaturedProductsCarousel.vue`: Bọc `tt(...)` cho tiêu đề và tên sản phẩm mẫu
  - [x] `ServicesSection.vue`: Bọc `tt(...)` cho heading, mô tả, fallback data và localized link
  - [x] `BrandStorySection.vue`: Bọc `tt(...)` cho story text, subtitle, title và các chỉ số hoạt động
  - [x] `ProcessSection.vue`: Bọc `tt(...)` cho tiêu đề, mô tả và 5 bước quy trình
  - [x] `ContactSection.vue`: Bọc `tt(...)` cho tiêu đề, mô tả, form labels, placeholders và thông báo

- [x] 4. Cập nhật Giao diện Frontend Hệ thống Dịch vụ (`Services/Index.vue` & `Services/Show.vue`) <!-- id: task-04-frontend-services -->
  - [x] `Services/Index.vue`: Localize badge, H1, description, song ngữ hóa mảng `defaultServices`
  - [x] `ServiceBlock.vue`: Localize category, tags, prep/comp comparison, CTA button, detail link
  - [x] `Services/Show.vue`: Localize SEO metadata và hero image mapping
  - [x] `ServiceHero.vue`: Localize breadcrumbs, H1, description, khung minh họa kỹ thuật
  - [x] `ServiceToc.vue`: Localize TOC header ("Trong bài viết này" / "Mục lục") và các mục TOC
  - [x] `ServiceArticleContent.vue`: Localize 4 section kỹ thuật, 4 bước thiết kế, checklist và callout
  - [x] `ServiceRelated.vue`: Localize tiêu đề và card dịch vụ liên quan
  - [x] `ServiceCta.vue`: Localize banner CTA "Tạo nên phiên bản mang dấu ấn riêng"

- [x] 5. Bổ sung Dữ liệu Tiếng Anh trong Database & Localized SEO trong Controllers <!-- id: task-05-backend-db-seo -->
  - [x] An toàn chèn bản ghi `locale = 'en'` vào `service_translations` cho ID 1, 2, 3 (không drop/truncate)
  - [x] Cập nhật `HomeController.php` và `ServiceController.php` để localize SEO metadata theo `current_locale()`

- [x] 6. Biên dịch và Kiểm thử Playwright E2E <!-- id: task-06-build-and-test -->
  - [x] Chạy `php artisan vue-i18n:generate` & `php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`
  - [x] Chạy `yarn frontend:build` kiểm tra build xanh
  - [x] Viết test Playwright E2E `tests/e2e/home-services-multilingual.spec.ts`
  - [x] Chụp ảnh màn hình bằng chứng thực tế tại `tests/e2e/screenshots/`
