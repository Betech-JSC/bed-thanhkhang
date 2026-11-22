# Tasks: Xây dựng Trang Chủ Thành Khang (`feat-page-home`)

> **Quy tắc vận hành theo xác nhận người dùng**: Thực hiện cuốn chiếu từng Stage và DỪNG báo cáo sau mỗi Stage; **hoãn `yarn frontend:build`** đến Stage 8 sau khi hoàn tất cả 7 Section.

## Stage 1 — Hero Banner (`595:15536`) & Khởi tạo OpenSpec

- [x] 1.1 Tải cục bộ 3 tài nguyên Stage 1 từ Figma vào `public/assets/images/homepage/hero-banner-1.png`, `public/assets/svg/homepage/hero-effect-top.svg`, và `public/assets/svg/icons/hero-chevron-left.svg`. *(Tiêu chí: `ls -lh` & `file` xác nhận 3/3 file tồn tại, dung lượng > 0B, đúng PNG `1450×667` / SVG)*
- [x] 1.2 Khởi tạo trọn bộ tài liệu OpenSpec `openspec/changes/feat-page-home/` (`.openspec.yaml`, `proposal.md`, `design.md`, `specs/home-page/spec.md`, `tasks.md`) đối chiếu `graphify-out/graph.json`. *(Tiêu chí: đủ 5 file chuẩn tiếng Việt theo `openspec/config.yaml`)*
- [x] 1.3 Tạo `database/seeders/ThanhKhangHomeSliderSeeder.php` & migration idempotent `database/migrations/2026_11_22_000001_seed_home_hero_sliders_data.php` nạp 3 slide `HOME_SLIDER` (VI/EN) và chuẩn hóa `HomeController::index()` trả về mảng `sliders`. *(Tiêu chí: `php -l` 0 lỗi, `php artisan migrate --path=...` thành công, `Slider::getByPosition('HOME_SLIDER')` trả đủ 3 slide — verified #24)*
- [x] 1.4 Tái cấu trúc `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` (khóa `aspect-[375/480] md:aspect-[1440/582]`, trượt ngang `translate-x`, 0 mock hardcode) và cập nhật `resources/Frontend/js/Pages/Home/Index.vue` (`layout: MainLayout`, nền `bg-surface`, bind `normalizedSliders`). *(Tiêu chí: kiểm tra cú pháp Vue sạch, không còn dữ liệu hardcode hero — verified #23)*

## Stage 2 — About Intro ("Về chúng tôi")

- [x] 2.1 Tải ảnh cục bộ Section About từ Figma (`about-gear-1.png`, `about-chain-2.png`, `about-engineer-3.png`) và bổ sung hỗ trợ CMS (`HOME_ABOUT_GALLERY`, `home_about_*`) + migration idempotent `2026_11_22_000002_seed_home_about_section_data.php`. *(Tiêu chí: `php -l` & `php artisan migrate --path=...` thành công #31, 3/3 PNG tồn tại > 0B)*
- [x] 2.2 Nâng cấp `BrandStorySection.vue` và gắn vào `Home/Index.vue` theo đúng Figma `595:15362` Desktop/Mobile. *(Tiêu chí: bind 100% dữ liệu động `aboutSection`, `@vue/compiler-sfc` OK #30)*

## Stage 3 — Product Categories ("Danh mục sản phẩm")

- [x] 3.1 Tải 8 tài nguyên cục bộ Stage 3 (`cat-*.png`, `chevron-right-{white,dark}.svg`), tạo migration `2026_11_22_000003_seed_home_categories_cta_data.php` (`BANNER_HOME_CATEGORIES_CTA`), và sửa truy vấn trong `HomeController::index()` để trả về đầy đủ **4 danh mục gốc hiện có của khách hàng** (tuyệt đối chỉ ĐỌC, không sửa/xóa `product_categories=26` hay `products=103`). *(Tiêu chí: `ls -lh`/`file` 8/8 file hợp lệ, `php -l` & `migrate` & `tinker` VI/EN xác nhận 4 danh mục gốc + `categoriesCtaBanner` #38)*
- [x] 3.2 Nâng cấp `ProductsSection.vue` theo Figma `595:15380` và gắn vào `Home/Index.vue`. *(Tiêu chí: render 100% từ dữ liệu CMS, `@vue/compiler-sfc` OK #37)*

## Stage 4 — Why Choose Us ("Vì sao khách hàng tin tưởng chọn Thành Khang?")

- [x] 4.1 Tải 11 tài nguyên cục bộ Section 4 từ Figma (`why-choose-us-bg.png`, `trust-bridge-1..3.svg`, `trust-ring-{outer,alt}.svg`, `trust-{thumbs-up,check-circle,clipboard,truck,tool}.svg`) và triển khai migration idempotent `2026_11_22_000004_seed_home_trust_section_data.php` cho `BANNER_HOME_TRUST` + `HOME_TRUST_PILLARS`. *(Tiêu chí: `ls -lh`/`file` 11/11 file hợp lệ > 0B; backend `php -l`, migration & `trustSection` VI/EN verified #44)*
- [x] 4.2 Nâng cấp `ServicesSection.vue` theo Figma `595:15394` (Desktop 5-node wave diagram `1152×260` + Mobile card stack) và gắn vào `Home/Index.vue`. *(Tiêu chí: 0 hardcode mock, `@vue/compiler-sfc` OK #45)*

## Stage 5 — Process ("Quy trình" — 4 bước chuẩn Figma)

- [x] 5.1 Tải 3 tài nguyên cục bộ Section 5 từ Figma (`process-left-bg.png`, `process-wave-bg.png`, `process-wave-mask.svg`) và triển khai migration idempotent `2026_11_22_000005_seed_home_process_section_data.php` cho `BANNER_HOME_PROCESS` + 4 bước `HOME_PROCESS_STEPS` (giữ nguyên `/quy-trinh`). *(Tiêu chí: 3/3 tài nguyên Figma verified #53; backend `php -l`, migration, idempotency & VI/EN `processSection` verified #52)*
- [x] 5.2 Nâng cấp `ProcessSection.vue` theo Figma `595:15441` (Desktop split `1440×954` + 4 thẻ xanh đậm dần `#005EA4 -> #00284B` & Mobile stack) và gắn vào `Home/Index.vue`. *(Tiêu chí: 0 hardcode mock, `@vue/compiler-sfc` OK #51)*

## Stage 6 — Blog / News ("Tin tức" — 3 thẻ bài viết)

- [x] 6.1 Tải 3 ảnh minh họa bài viết từ Figma (`blog-ansi-roller-chain.png`, `blog-escalator-chain-maintenance.png`, `blog-industrial-conveyor-chain.png`) và tạo seeder/migration idempotent `2026_11_22_000006_seed_home_sample_posts_data.php` nạp 3 bản ghi `posts` (`type='POST'`, giữ nguyên 6 `FAQ`). *(Tiêu chí: 3/3 PNG verified trên đĩa; backend `php -l`, migration, idempotency & VI/EN `featured_posts` verified #62)*
- [x] 6.2 Dựng `BlogSection.vue` theo Figma `595:15483` (3 cột Desktop `aspect-[368/350]`, badge `New` động theo `is_featured`, CTA `/tin-tuc`) và gắn vào `Home/Index.vue`. *(Tiêu chí: 0 hardcode mock, `@vue/compiler-sfc` OK #60)*

## Stage 7 — Contact & Quote Form ("KẾT NỐI & NHẬN BÁO GIÁ")

- [x] 7.1 Tải 6 tài nguyên cục bộ Section 7 (`contact-chain-deco.png`, `contact-chain-mask-{1,2}.svg`, `contact-{phone,mail,pin}.svg`), triển khai CMS `home_contact_*` (`2026_11_22_000007_seed_home_contact_section_data.php`) + validation `ContactController::store()`, nâng cấp `ContactSection.vue` theo Figma `595:15496` gắn `$page.props.global`, và dọn `Home/Index.vue` còn đúng 7 Section Figma (không xóa file cũ). *(Tiêu chí: 6/6 tài nguyên Figma verified #68; backend `php -l`, migration, idempotency & transactional `ContactController::store()` tests verified #69; `ContactSection.vue` & 7-section `Home/Index.vue` `@vue/compiler-sfc` OK #67)*

## Stage 8 — Final Build, Graphify AST Sync & Playwright Visual QA

- [x] 8.1 Chạy `npm run frontend:build` (Client + SSR trên `vite.frontend.config.js`) xác nhận **0 errors**. *(Tiêu chí: exit code 0, `app.643a26f0.css`, `app.0c90250b.js`, `bootstrap/ssr/ssr.mjs`, PWA precache 80 entries — verified #74 & post-fix #78)*
- [x] 8.2 Chạy `graphify . --code-only` cập nhật bản đồ AST `graphify-out/graph.json`. *(Tiêu chí: exit code 0, `9748 nodes, 16080 edges, 875 communities`, `7.9M`, `/graphify-out` confirmed trong `.gitignore` — verified #74 & post-fix #78)*
- [x] 8.3 Chạy Playwright Visual QA trên **Desktop (`1440×900`)**, **Mobile (`375×812`)**, và **EN (`/en`)** tại `http://127.0.0.1:8000` cho toàn bộ 7 Section Trang Chủ: `0` console errors/warnings, `0` failed network requests (`200 OK` 100%), `0` broken visible images (`56/56` Desktop, `41/41` Mobile), `0` horizontal overflow (`scrollWidth 1432 <= 1440` Desktop, `367 <= 375` Mobile), khóa tỷ lệ Hero (`1432×579` = `2.474` ≈ `1440/582`), trượt ngang Hero `< / >` (`translateX(-100%) -> -200% -> -100%`), Contact form inline validation + transactional `POST /contacts` verified (#69); 3 lỗi polish soi từ screenshot (`BrandStorySection.vue`, `ServicesSection.vue`, `ContactSection.vue`) đã sửa (#76), rebuild (#78) và re-verified bằng screenshot. *(Tiêu chí: 100% PASS + screenshots)*
