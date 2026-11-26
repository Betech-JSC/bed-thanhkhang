# Proposal: Xây dựng Trang Giới Thiệu Thành Khang Chuẩn Figma & CMS-First (`feat-page-about`)

## Why

Trang Giới thiệu (`/gioi-thieu` & `/en/about-us`, điều phối bởi `App\Http\Controllers\Frontend\HistoryController@index` render `resources/Frontend/js/Pages/About/Index.vue`) hiện vẫn sử dụng giao diện nền tối cũ thời HH Maker (`bg-dark-bg text-white`, Options API, chưa khai báo tường minh `layout: MainLayout`, dùng `v-scroll-reveal`, còn chứa văn bản hardcode về in 3D và ảnh di sản), đồng thời trong database `bed_thanhkhang` vị trí `BANNER_ABOUT_HERO` đang trống (`null`) và truy vấn `$posts` trong `HistoryController@index()` thiếu scope `->wherePosts()` khiến lấy nhầm các bản ghi `FAQ`.

Cần triển khai toàn diện Trang Giới thiệu chuẩn 100% theo thiết kế Figma chính thức (`fileKey: Gu7RyTUDWaupomGrOH5EZO`, frame `979:30278` — `1440×3214`, tham chiếu quy chuẩn responsive Mobile từ Trang Chủ Official `1190:6335`), kết nối **100% dữ liệu động từ CMS (`Sliders` + `Settings` song ngữ VI/EN, Zero Hardcoded Mock)** thông qua cặp **Idempotent Seeder + Non-Destructive Migration** theo đúng mô hình Bước 2 (`feat-page-home`), và thực thi **tuần tự nghiêm ngặt từ trên xuống dưới (Stage 1 → Stage 2 → Stage 3 → Stage 4 → Stage 5 → Stage 6)**.

## What Changes

Triển khai cuốn chiếu tuần tự 5 Section của Trang Giới thiệu từ trên xuống dưới (hoàn tất trọn vẹn từng Stage trước khi chuyển sang Stage kế tiếp) + Stage 6 Build, AST Sync & Playwright Visual QA ở cuối:

1. **Khung Trang Giới thiệu (`resources/Frontend/js/Pages/About/Index.vue`)**:
   - Chuyển sang Vue 3 `<script setup>` + khai báo tường minh `layout: MainLayout` (`resources/Frontend/js/Layouts/MainLayout.vue`), nền sáng `bg-surface text-ink-body overflow-x-clip`.
   - Điều phối đúng thứ tự 5 component section (`AboutHeroSection` → `AboutStorySection` → `AboutMissionVisionSection` → `AboutStatsSection` → `AboutCompanionSection`), 0 mảng mock tĩnh trên Vue, không dùng `v-scroll-reveal` / `opacity: 0`.
2. **Stage 1 — Section 1: Hero Banner (`979:30279`, hiển thị `1440×582` dưới Header)**:
   - Tải ảnh gốc từ Figma `979:30280` lưu vào `public/assets/images/about/about-hero-banner.png`.
   - Tạo `ThanhKhangAboutHeroSeeder` + migration không phá hủy `2026_11_26_000001_seed_about_hero_banner_data.php` nạp dữ liệu song ngữ VI/EN cho vị trí có sẵn `BANNER_ABOUT_HERO` (`banner => true`): `subtitle` (*"Về Thành Khang Tecsat"* / *"About Thanh Khang Tecsat"*), `title` (*"KẾT NỐI CHẤT LƯỢNG\nĐỒNG HÀNH BỀN VỮNG"* / *"CONNECTING QUALITY\nSUSTAINABLE PARTNERSHIP"*), `description` (*"Giải pháp vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp. Cùng doanh nghiệp vận hành an toàn, hiệu quả và liên tục."* / bản EN tương ứng), `image` & `image_mobile`.
   - Dựng `resources/Frontend/js/Pages/About/Components/AboutHeroSection.vue` khóa tỷ lệ `aspect-[375/480] md:aspect-[1440/582]` + ảnh `w-full h-full object-cover object-center`, container `.app-container-wide` (`px-4 md:px-8`).
3. **Stage 2 — Section 2: Câu chuyện Thành Khang (`979:30312`, `1440×700`)**:
   - Tải 3 ảnh collage cột trái từ Figma vào `public/assets/images/about/`:
     1. `979:30318` → `about-story-chain-inspection.png` (`aspect-[358/210] md:aspect-[493/280]`)
     2. `979:30320` → `about-story-sprocket.png` (`aspect-[173/110] md:aspect-[239/146]`)
     3. `979:30321` → `about-story-roller-chains.png` (`aspect-[173/110] md:aspect-[239/146]`)
   - Khai báo vị trí Slider mới `ABOUT_STORY_GALLERY` (`banner => false`, `position_sort` `30, 20, 10`) trong `config/slider.php` và bộ Settings keys song ngữ VI/EN (`about_story_eyebrow`, `about_story_heading`, `about_story_paragraph_1`, `about_story_paragraph_2`, `about_story_paragraph_3`, `about_story_commitment` kèm hậu tố `_en`) đồng bộ tại 3 nơi: `App\Models\Config::rules('general')` (+ helper `Config::getAboutStorySettings()`), `JamstackVietnam\Core\Models\Setting::rules('general')`, và giao diện quản trị `packages/bed-package-essentials/core/resources/Backend/js/Pages/Settings/General.vue`.
   - Tạo `ThanhKhangAboutStorySeeder` + migration `2026_11_26_000002_seed_about_story_section_data.php`.
   - Dựng `resources/Frontend/js/Pages/About/Components/AboutStorySection.vue` (`py-8 md:py-16 lg:pt-[80px] lg:pb-[115px]`, `.app-container-narrow` `px-4 md:px-8`, `gap-3 md:gap-4` cho collage ảnh, `gap-6 md:gap-8` cho bố cục 2 cột: cột ảnh trái `493px`, cột nội dung phải `627px`).
4. **Stage 3 — Section 3: Sứ mệnh và Tầm nhìn (`979:30329`, `1440×460`)**:
   - Tải ảnh nền sạch không dính chữ từ `979:30329` (`imageRef: c04827b8b84b023ecd8589dab0ce24ed0cb4bec5`) lưu vào `public/assets/images/about/about-mission-vision-bg.png`.
   - Khai báo 2 vị trí Slider mới trong `config/slider.php`: `BANNER_ABOUT_MISSION_VISION` (`banner => true`) và `ABOUT_MISSION_VISION_ITEMS` (`banner => false`, 2 bản ghi `position_sort` `20` SỨ MỆNH, `10` TẦM NHÌN lưu `subtitle`, `title`, `description` song ngữ VI/EN).
   - Tạo `ThanhKhangAboutMissionVisionSeeder` + migration `2026_11_26_000003_seed_about_mission_vision_data.php`.
   - Dựng `resources/Frontend/js/Pages/About/Components/AboutMissionVisionSection.vue` khóa tỷ lệ `aspect-[375/560] md:aspect-[1440/460] md:min-h-[360px] lg:min-h-0` + ảnh `w-full h-full object-cover object-bottom`, container `.app-container-narrow` (`px-4 md:px-8`), `py-8 md:py-12 lg:pt-[64px] lg:pb-0`, 2 cột `gap-6 md:gap-12 lg:gap-[120px]`.
5. **Stage 4 — Section 4: Dấu ấn và Cam kết (`979:30340`, `1440×168`)**:
   - Thuần typography (không tạo ảnh/SVG riêng).
   - Khai báo vị trí Slider mới `ABOUT_STATS_ITEMS` (`banner => false`, 4 bản ghi `position_sort` `40, 30, 20, 10`: `Dự án đã đồng hành → 100+`, `Phạm vi cung ứng sản phẩm → Toàn Quốc`, `Chứng từ, nguồn gốc minh bạch → CO/CQ`, `Thời gian giao hàng → 24/7`, song ngữ VI/EN).
   - Tạo `ThanhKhangAboutStatsSeeder` + migration `2026_11_26_000004_seed_about_stats_section_data.php`.
   - Dựng `resources/Frontend/js/Pages/About/Components/AboutStatsSection.vue` nền `bg-[#F8FAFC]`, `py-8 md:py-[40px]`, `.app-container-narrow` (`px-4 md:px-8`), lưới `grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 lg:gap-8` (dòng mô tả nhỏ `#364152` ở trên, giá trị nổi bật lớn `#005EA4` ở dưới).
6. **Stage 5 — Section 5: Đồng hành cùng khách hàng (`979:30345`, `1440×715`)**:
   - Tải 2 ảnh card `560×220` từ Figma vào `public/assets/images/about/`:
     1. `I979:30352;754:20832` → `about-companion-consulting.png`
     2. `I979:30353;754:20832` → `about-companion-aftersales.png`
   - Bổ sung Settings keys song ngữ VI/EN (`about_companion_eyebrow`, `about_companion_heading` + `_en` đồng bộ tại `Config`, `Setting::rules('general')`, `General.vue`) và vị trí Slider mới `ABOUT_COMPANION_CARDS` (`banner => false`, 2 bản ghi `position_sort` `20, 10` chứa `title`, `description`, `primary_cta_text` *"Liên hệ tư vấn"* / *"Liên hệ hỗ trợ"*, `primary_cta_link` mặc định `/lien-he` cho VI và `/en/contact` cho EN, `image`).
   - Tạo `ThanhKhangAboutCompanionSeeder` + migration `2026_11_26_000005_seed_about_companion_section_data.php`.
   - Chuẩn hóa `HistoryController@index()` trả đầy đủ payload 5 section chuẩn hóa URL tài nguyên cục bộ, đồng thời sửa lỗi truy vấn `$posts` bị lẫn `FAQ` (thêm `->wherePosts()->ordered()`) và bổ sung xóa cache `history_index_{locale}` trong `Post::clearPostCaches()`.
   - Dựng `resources/Frontend/js/Pages/About/Components/AboutCompanionSection.vue` nền `bg-[#F8FAFC]`, `py-8 md:py-16 lg:py-[80px]`, `.app-container-narrow` (`px-4 md:px-8`), 2 cột `grid-cols-1 md:grid-cols-2 gap-6 md:gap-8`, ảnh card khóa `aspect-[358/180] md:aspect-[560/220]` + `w-full h-full object-cover object-center`.
7. **Stage 6 — Build Gate, Graphify AST Sync, Tests & Playwright Visual QA**:
   - Chạy `yarn frontend:build` (Client + SSR), `php artisan test` (bao gồm `CmsSyncIntegrationTest`), đồng bộ `graphify . --code-only`, và nghiệm thu trên trình duyệt thật bằng Playwright MCP ở Desktop (`1440×900`) & Mobile (`375×812`) cho cả `/gioi-thieu` và `/en/about-us` (kèm kiểm tra hồi quy Trang Chủ `/`).

## Capabilities

### New Capabilities
- `about-page`: Hệ thống Trang Giới thiệu Thành Khang (`/gioi-thieu` & `/en/about-us`) đầy đủ 5 section chuẩn Figma `979:30278`, quản trị động 100% qua CMS (`Sliders` + `Settings` song ngữ VI/EN), khởi tạo dữ liệu tự động qua cặp Idempotent Seeder & Non-Destructive Migration, khóa tỷ lệ `aspect-ratio` chuẩn Desktop/Mobile và tương thích song ngữ.

### Modified Capabilities
<!-- Không thay đổi requirement cấp đặc tả của các main specs hiện hữu trong openspec/specs/ -->

## Impact

- **Tài nguyên tĩnh cục bộ (`public/assets/images/about/`)**:
  - `about-hero-banner.png` (`979:30280`)
  - `about-story-chain-inspection.png` (`979:30318`), `about-story-sprocket.png` (`979:30320`), `about-story-roller-chains.png` (`979:30321`)
  - `about-mission-vision-bg.png` (`979:30329`)
  - `about-companion-consulting.png` (`I979:30352;754:20832`), `about-companion-aftersales.png` (`I979:30353;754:20832`)
- **Backend, Cấu hình CMS & Migrations/Seeders**:
  - `config/slider.php` (thêm 5 vị trí: `ABOUT_STORY_GALLERY`, `BANNER_ABOUT_MISSION_VISION`, `ABOUT_MISSION_VISION_ITEMS`, `ABOUT_STATS_ITEMS`, `ABOUT_COMPANION_CARDS`; giữ nguyên `BANNER_ABOUT_HERO` và toàn bộ vị trí hiện có)
  - `app/Models/Config.php` (`rules('general')` + `getAboutStorySettings()`, `getAboutCompanionSettings()`)
  - `packages/bed-package-essentials/core/src/Models/Setting.php` (`rules('general')`)
  - `packages/bed-package-essentials/core/resources/Backend/js/Pages/Settings/General.vue` (thêm thẻ cấu hình Trang Giới thiệu song ngữ VI/EN)
  - `app/Http/Controllers/Frontend/HistoryController.php` & `app/Models/Post/Post.php`
  - `database/seeders/ThanhKhangAbout{Hero,Story,MissionVision,Stats,Companion}Seeder.php`
  - `database/migrations/2026_11_26_00000{1..5}_seed_about_*_data.php`
- **Frontend Vue 3 Components**:
  - `resources/Frontend/js/Pages/About/Index.vue`
  - `resources/Frontend/js/Pages/About/Components/{AboutHeroSection,AboutStorySection,AboutMissionVisionSection,AboutStatsSection,AboutCompanionSection}.vue`
- **AST & Kiểm thử**:
  - `graphify-out/graph.json`, `graphify-out/GRAPH_REPORT.md`, `tests/Feature/CmsSyncIntegrationTest.php` (nếu cần bổ sung assert mới).

## Phạm vi an toàn (Strict Safe Guard)

- **Tuyệt đối KHÔNG xóa file** (`rm` / `Remove-Item`): Giữ nguyên `Pages/About.vue`, `Pages/History.vue`, `Pages/Histories/Show.vue` và toàn bộ các ảnh cũ trong `public/assets/images/about/` để tránh ảnh hưởng lịch sử hoặc tham chiếu khác.
- **Tuyệt đối KHÔNG tạo bảng DB mới, KHÔNG chạy lệnh phá hủy dữ liệu** (`migrate:fresh`, `migrate:reset`, `drop`, `truncate`): Bảo toàn nguyên vẹn 100% dữ liệu hiện có trong DB (`products=103`, `product_categories=26`, `policies=6`, `posts=9`, `sliders=18` của Trang Chủ, `settings=66`). Mọi `down()` trong migration đều không xóa dữ liệu để bảo vệ chỉnh sửa của Admin CMS.
- **Tuân thủ thực thi tuần tự nghiêm ngặt**: Phải hoàn tất export tài nguyên + backend/seeder/migration + component UI của Section trên rồi mới chuyển sang Section kế tiếp bên dưới (`Stage 1 → Stage 2 → Stage 3 → Stage 4 → Stage 5`), chỉ chạy `yarn frontend:build` ở `Stage 6`.
