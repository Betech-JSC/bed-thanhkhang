# Design: Xây dựng Trang Giới Thiệu Thành Khang (`feat-page-about`)

## Context

Xem `proposal.md` để nắm bối cảnh nghiệp vụ và lý do thực hiện. Về mặt kỹ thuật hiện tại (xác thực từ `graphify-out/graph.json` commit `746864d0` và MySQL `bed_thanhkhang`):
- **Định tuyến đã sẵn sàng**: `routes/frontend.php:49-52` định nghĩa `GET /gioi-thieu` (`vi.histories.index`) và `GET /en/about-us` (`en.histories.index`) cùng các redirect `301` từ `/about`, `/about-us`, `/ve-chung-toi` (`routes/frontend.php:23-27`), trỏ về `App\Http\Controllers\Frontend\HistoryController@index`.
- **View hiện hữu**: `HistoryController@index()` render `About/Index` (`resources/Frontend/js/Pages/About/Index.vue`), hiện đang ở trạng thái giao diện tối cũ thời HH Maker (`bg-dark-bg`, Options API, `v-scroll-reveal`).
- **Hạ tầng CMS**: Hệ thống dùng cặp `JamstackVietnam\Slider\Models\Slider` (`sliders` + `slider_translations`, quản trị tại `/admin/sliders`) và `settings` (`group = 'general'`, quản trị tại `/admin/settings/general` qua `JamstackVietnam\Core\Controllers\SettingController@store`). Đặc biệt, `SettingController@store` lọc dữ liệu đầu vào bằng `$request->validate(Setting::rules('general'))` trong `packages/bed-package-essentials/core/src/Models/Setting.php`, nên mọi key `settings` mới bắt buộc phải đăng ký đồng bộ ở cả `Setting.php`, `App\Models\Config.php`, và `General.vue`.
- **Tham chiếu thiết kế Figma**: Frame Desktop `979:30278` (`1440×3214`, `fileKey: Gu7RyTUDWaupomGrOH5EZO`). Trên Figma chưa tách frame Mobile riêng cho trang Giới thiệu, do đó toàn bộ spacing, typography scale và container mobile kế thừa đồng nhất quy chuẩn Mobile Trang Chủ Official (`1190:6335`: gutter `px-4`, section spacing `py-8`, card gap `gap-3`/`gap-4`, thang chữ H1–H4 `28px/20px/18px/16px`).

## Goals / Non-Goals

**Goals:**
- Chuyển đổi `About/Index.vue` sang bố cục sáng chuẩn Design System Thành Khang (`layout: MainLayout`, `bg-surface text-ink-body overflow-x-clip`) với 5 component section tách biệt, dễ bảo trì.
- Đảm bảo **100% CMS-First (Zero Hardcoded Mock)** song ngữ VI/EN cho cả 5 section mà không phải tạo thêm bảng database mới.
- Đóng gói dữ liệu khởi tạo theo cặp **Idempotent Seeder + Non-Destructive Migration** để CI/CD lên VPS chỉ cần chạy `php artisan migrate` là có ngay giao diện đầy đủ.
- Thực thi tuần tự nghiêm ngặt từ trên xuống dưới (`Stage 1 → Stage 2 → Stage 3 → Stage 4 → Stage 5`), khóa `aspect-ratio` mọi khung ảnh/banner để triệt tiêu CLS.

**Non-Goals:**
- Không tạo bảng database mới và không kích hoạt lại module `histories` cũ trên Admin Sidebar (do thiết kế Figma `979:30278` không dùng timeline năm cột mốc dạng bài viết lẻ).
- Không xóa các file di sản (`Pages/About.vue`, `Pages/History.vue`, `Pages/Histories/Show.vue`, hoặc các ảnh cũ trong `public/assets/images/about/`).
- Không thay đổi URL route `/gioi-thieu` và `/en/about-us` hay tên route `histories.index` đang được `Header.vue` và `ThanhKhangHomeAboutSeeder` liên kết.

## Decisions

### 1. Chiến lược lưu trữ CMS: Kết hợp `Sliders` + `Settings (general)` thay vì tạo bảng mới
- **Lựa chọn**:
  - Dùng `Sliders` (`banner => true` cho Hero/Background Banner đơn; `banner => false` kèm `position_sort` giảm dần cho danh sách ảnh collage, Sứ mệnh/Tầm nhìn, 4 chỉ số Stats, 2 thẻ Đồng hành).
  - Dùng `Settings` (`group = 'general'`, cặp key `<name>` và `<name>_en`) cho văn bản tự sự nhiều đoạn của Section 2 (`about_story_*`) và tiêu đề phân đoạn Section 5 (`about_companion_*`).
- **Lý do**: Khớp 100% kiến trúc Bước 2 (`feat-page-home`), tận dụng ngay giao diện quản trị sẵn có tại `/admin/sliders` (hỗ trợ sẵn đa ngôn ngữ `vi`/`en`, upload ảnh desktop/mobile, `subtitle`, `title`, `description`, `primary_cta_text`, `primary_cta_link`) và `/admin/settings/general` mà không phát sinh rủi ro schema migration.
- **Phương án đã loại trừ**: Lưu cục JSON lớn trong 1 trường `settings` duy nhất (khó chỉnh sửa cho Admin không rành kỹ thuật) hoặc tạo bảng `about_sections` mới (vi phạm nguyên tắc tối giản schema khi `sliders` + `settings` đã đáp ứng trọn vẹn).

### 2. Đồng bộ 3 tầng cho mọi khóa `Settings` mới
- **Lựa chọn**: Khai báo mọi key `about_story_*` và `about_companion_*` đồng thời tại:
  1. `packages/bed-package-essentials/core/src/Models/Setting.php` (`rules('general')`)
  2. `packages/bed-package-essentials/core/resources/Backend/js/Pages/Settings/General.vue`
  3. `app/Models/Config.php` (`rules('general')` + `getAboutStorySettings($locale)` & `getAboutCompanionSettings($locale)` có fallback EN → VI).
- **Lý do**: Ngăn chặn lỗi mất dữ liệu âm thầm khi Admin bấm Lưu tại `/admin/settings/general` (vốn đi qua `SettingController@store` dùng `Setting::rules('general')`).

### 3. Đối chiếu Graphify AST & Phân tách Component (`EXTEND, NEVER DUPLICATE`)
Tra cứu `graphify-out/graph.json` xác nhận chưa có các section chuyên biệt của trang Giới thiệu mới, trong khi Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`) và Design Tokens (`tailwind.config.js`, `app.scss`) đã hoàn thiện:

| Thành phần | Đường dẫn File | Quyết định kiến trúc |
| :--- | :--- | :--- |
| **Layout Shell** | `resources/Frontend/js/Layouts/MainLayout.vue` | **Tái sử dụng 100%** (khai báo tường minh `layout: MainLayout` trong `About/Index.vue`). |
| **Trang Giới thiệu Root** | `resources/Frontend/js/Pages/About/Index.vue` | **Tái cấu trúc tại chỗ** sang `<script setup>`, nhận props từ `HistoryController@index()`, lắp ghép tuần tự 5 section. |
| **Section 1 — Hero** | `resources/Frontend/js/Pages/About/Components/AboutHeroSection.vue` | Tạo mới component tĩnh 1 banner (`BANNER_ABOUT_HERO`), đồng bộ hệ màu/chữ với `HeroSection.vue` nhưng không kéo theo logic carousel thừa. |
| **Section 2 — Story** | `resources/Frontend/js/Pages/About/Components/AboutStorySection.vue` | Tạo mới cho bố cục collage 3 ảnh bên trái (`493px`) + 3 đoạn văn bên phải (`627px`) đặc thù của `979:30312` (khác bố cục 2 hàng của `BrandStorySection.vue` trên Home để không phá vỡ hồi quy Trang Chủ). |
| **Section 3 — Mission & Vision** | `resources/Frontend/js/Pages/About/Components/AboutMissionVisionSection.vue` | Tạo mới cho khối nền banner full-bleed `979:30329` + 2 cột Sứ mệnh / Tầm nhìn. |
| **Section 4 — Stats** | `resources/Frontend/js/Pages/About/Components/AboutStatsSection.vue` | Tạo mới lưới 4 chỉ số typography thuần `979:30340`. |
| **Section 5 — Companion** | `resources/Frontend/js/Pages/About/Components/AboutCompanionSection.vue` | Tạo mới lưới 2 thẻ dịch vụ đồng hành `979:30345` kèm nút CTA động. |

---

## Chi Tiết Kiến Trúc Từng Stage (Figma `979:30278`)

### Stage 1 — Section 1: Hero Banner (`979:30279`, `1440×582` dưới Header)
- **Local Asset**: Node `979:30280` → `public/assets/images/about/about-hero-banner.png`.
- **CMS Contract**:
  - Tái sử dụng vị trí có sẵn `BANNER_ABOUT_HERO` (`banner => true` trong `config/slider.php:15-19`).
  - Seeder `ThanhKhangAboutHeroSeeder` + Migration `2026_11_26_000001_seed_about_hero_banner_data.php`:
    - VI: `subtitle = "Về Thành Khang Tecsat"`, `title = "KẾT NỐI CHẤT LƯỢNG\nĐỒNG HÀNH BỀN VỮNG"`, `description = "Giải pháp vật tư, linh kiện thang máy – thang cuốn và thiết bị công nghiệp. Cùng doanh nghiệp vận hành an toàn, hiệu quả và liên tục."`.
    - EN: `subtitle = "About Thanh Khang Tecsat"`, `title = "CONNECTING QUALITY\nSUSTAINABLE PARTNERSHIP"`, `description = "Genuine elevator – escalator spare parts, components, and industrial equipment solutions. Partnering with businesses for safe, efficient, and continuous operation."`.
    - `image` & `image_mobile`: `{ "path": "/assets/images/about/about-hero-banner.png", "alt": "..." }`.
  - Prop từ `HistoryController@index()`: `heroBanner: { id, subtitle, title, description, image: { url, alt }, image_mobile: { url, alt } } | null`.
- **UI & Aspect-Ratio (`AboutHeroSection.vue`)**:
  - Khóa tỷ lệ khung ngoài: `aspect-[375/480] md:aspect-[1440/582] w-full relative overflow-hidden`.
  - Ảnh nền: `w-full h-full object-cover object-center` + lớp phủ scrim đảm bảo độ tương phản chữ sáng.
  - Nội dung đặt trong `.app-container-wide` (`max-w-[1344px] mx-auto px-4 md:px-8`), thẻ `<h1>` duy nhất của trang (`whitespace-pre-line` giữ ngắt dòng 2 vế tiêu đề).

### Stage 2 — Section 2: Câu chuyện Thành Khang (`979:30312`, `1440×700`)
- **Local Assets (3 ảnh collage cột trái)**:
  1. `979:30318` → `public/assets/images/about/about-story-chain-inspection.png` (`aspect-[358/210] md:aspect-[493/280]`)
  2. `979:30320` → `public/assets/images/about/about-story-sprocket.png` (`aspect-[173/110] md:aspect-[239/146]`)
  3. `979:30321` → `public/assets/images/about/about-story-roller-chains.png` (`aspect-[173/110] md:aspect-[239/146]`)
- **CMS Contract**:
  - Slider position mới `ABOUT_STORY_GALLERY` (`banner => false`, 3 bản ghi `position_sort` giảm dần `30, 20, 10` tương ứng ảnh 1, 2, 3).
  - Settings keys (`group = 'general'`, VI + `_en`): `about_story_eyebrow`, `about_story_heading`, `about_story_paragraph_1`, `about_story_paragraph_2`, `about_story_paragraph_3`, `about_story_commitment`.
  - Seeder `ThanhKhangAboutStorySeeder` + Migration `2026_11_26_000002_seed_about_story_section_data.php`.
  - Prop từ `HistoryController@index()`: `storySection: { eyebrow, heading, paragraphs: string[], commitment, images: Array<{ id, url, alt }> }`.
- **UI & Layout (`AboutStorySection.vue`)**:
  - Spacing: `py-8 md:py-16 lg:pt-[80px] lg:pb-[115px]`, khung `.app-container-narrow` (`max-w-[1216px] mx-auto px-4 md:px-8` → `1152px` inner width).
  - Bố cục 2 cột Desktop (`flex flex-col lg:flex-row gap-6 md:gap-8 items-start`):
    - Cột ảnh trái (`w-full lg:w-[493px] shrink-0 flex flex-col gap-3 md:gap-4`): Hàng trên 1 ảnh lớn `aspect-[358/210] md:aspect-[493/280]`, hàng dưới `grid grid-cols-2 gap-3 md:gap-4` chứa 2 ảnh nhỏ `aspect-[173/110] md:aspect-[239/146]`, mọi thẻ `<img>` đều dùng `w-full h-full object-cover object-center`.
    - Cột chữ phải (`w-full lg:w-[627px]`): Eyebrow kèm ô vuông điểm nhấn `#005EA4`, tiêu đề `<h2>`, các đoạn văn nội dung và dòng cam kết nhấn mạnh.

### Stage 3 — Section 3: Sứ mệnh và Tầm nhìn (`979:30329`, `1440×460`)
- **Local Asset**: Ảnh nền gốc sạch không dính chữ từ `979:30329` (`imageRef: c04827b8b84b023ecd8589dab0ce24ed0cb4bec5`) → `public/assets/images/about/about-mission-vision-bg.png`.
- **CMS Contract**:
  - `BANNER_ABOUT_MISSION_VISION` (`banner => true`): lưu ảnh nền desktop/mobile và alt text song ngữ.
  - `ABOUT_MISSION_VISION_ITEMS` (`banner => false`): 2 bản ghi `position_sort` `20` (Sứ mệnh / Mission) và `10` (Tầm nhìn / Vision), lưu `subtitle` (nhãn phụ nếu có), `title`, `description` song ngữ VI/EN.
  - Seeder `ThanhKhangAboutMissionVisionSeeder` + Migration `2026_11_26_000003_seed_about_mission_vision_data.php`.
  - Prop từ `HistoryController@index()`: `missionVisionSection: { banner: { url, mobile_url, alt }, items: Array<{ id, subtitle, title, description }> }`.
- **UI & Aspect-Ratio (`AboutMissionVisionSection.vue`)**:
  - Khóa tỷ lệ: `aspect-[375/560] md:aspect-[1440/460] md:min-h-[360px] lg:min-h-0 w-full relative overflow-hidden`.
  - Ảnh nền: `w-full h-full object-cover object-bottom`.
  - Nội dung: `.app-container-narrow` (`px-4 md:px-8`), `py-8 md:py-12 lg:pt-[64px] lg:pb-0`, lưới 2 cột `grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-12 lg:gap-[120px]`.

### Stage 4 — Section 4: Dấu ấn và Cam kết (`979:30340`, `1440×168`)
- **Local Assets**: Không có (pure typography).
- **CMS Contract**:
  - `ABOUT_STATS_ITEMS` (`banner => false`): 4 bản ghi `position_sort` `40, 30, 20, 10`, lưu nhãn mô tả nhỏ vào `subtitle` (hoặc `description`) và giá trị nổi bật lớn vào `title` song ngữ VI/EN:
    1. `sort = 40`: `Dự án đã đồng hành` → `100+` (EN: `Projects Supported` → `100+`)
    2. `sort = 30`: `Phạm vi cung ứng sản phẩm` → `Toàn Quốc` (EN: `Supply Coverage` → `Nationwide`)
    3. `sort = 20`: `Chứng từ, nguồn gốc minh bạch` → `CO/CQ` (EN: `Transparent Origin & Certificates` → `CO/CQ`)
    4. `sort = 10`: `Thời gian giao hàng` → `24/7` (EN: `Delivery Readiness` → `24/7`)
  - Seeder `ThanhKhangAboutStatsSeeder` + Migration `2026_11_26_000004_seed_about_stats_section_data.php`.
  - Prop từ `HistoryController@index()`: `statsItems: Array<{ id, label, value }>`.
- **UI & Layout (`AboutStatsSection.vue`)**:
  - Nền `bg-[#F8FAFC]`, `py-8 md:py-[40px]`, khung `.app-container-narrow` (`px-4 md:px-8`).
  - Lưới `grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 lg:gap-8`: dòng nhãn mô tả nhỏ màu `#364152` ở trên, điểm nhấn giá trị lớn màu `#005EA4` ở dưới.

### Stage 5 — Section 5: Đồng hành cùng khách hàng (`979:30345`, `1440×715`)
- **Local Assets (2 ảnh card `560×220`)**:
  1. `I979:30352;754:20832` → `public/assets/images/about/about-companion-consulting.png`
  2. `I979:30353;754:20832` → `public/assets/images/about/about-companion-aftersales.png`
- **CMS Contract**:
  - Settings keys (`group = 'general'`, VI + `_en`): `about_companion_eyebrow`, `about_companion_heading`.
  - `ABOUT_COMPANION_CARDS` (`banner => false`): 2 bản ghi `position_sort` `20, 10` lưu `title`, `description`, `primary_cta_text` (*"Liên hệ tư vấn"* & *"Liên hệ hỗ trợ"* / EN tương ứng), `primary_cta_link` mặc định `/lien-he` (VI) và `/en/contact` (EN), `image`.
  - Seeder `ThanhKhangAboutCompanionSeeder` + Migration `2026_11_26_000005_seed_about_companion_section_data.php`.
  - Chuẩn hóa cuối trong `HistoryController@index()` & `Post::clearPostCaches()`: sửa truy vấn `$posts` thêm `->wherePosts()->ordered()` để không lẫn `FAQ`, bổ sung `Cache::forget('history_index_' . $locale)` trong `Post::clearPostCaches()`, và hỗ trợ `MetaPage` override cho `seo` của `/gioi-thieu` & `/en/about-us`.
  - Prop từ `HistoryController@index()`: `companionSection: { eyebrow, heading, cards: Array<{ id, title, description, cta_text, cta_link, image: { url, alt } }> }`.
- **UI & Aspect-Ratio (`AboutCompanionSection.vue`)**:
  - Nền `bg-[#F8FAFC]`, `py-8 md:py-16 lg:py-[80px]`, khung `.app-container-narrow` (`px-4 md:px-8`).
  - Lưới 2 cột `grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8`.
  - Mỗi thẻ khóa tỷ lệ khung ảnh `aspect-[358/180] md:aspect-[560/220]` + `w-full h-full object-cover object-center`, tiêu đề, mô tả và nút CTA liên kết động tới `card.cta_link`.

---

## Risks / Trade-offs

- **[Risk] Mất các trường `about_*` khi Admin lưu Cài đặt chung** → **Mitigation**: Khai báo đầy đủ toàn bộ key `about_story_*` và `about_companion_*` trong `JamstackVietnam\Core\Models\Setting::rules('general')` song song với `App\Models\Config::rules('general')` và `General.vue`, kiểm chứng bằng feature test.
- **[Risk] Lệch URL ảnh cục bộ giữa môi trường `localhost` và domain production khi SSR** → **Mitigation**: Áp dụng cùng hàm chuẩn hóa `$normalizeLocalAssetUrl()` như `HomeController@index()` để giữ đường dẫn tương đối `/assets/images/about/...` cho tài nguyên tĩnh cục bộ.
- **[Risk] Tràn chữ trên khung `aspect-[375/560]` của Section 3 ở màn hình rất hẹp (`375px`) khi Admin nhập văn bản dài** → **Mitigation**: Sử dụng `min-h` kết hợp `aspect-[375/560] md:aspect-[1440/460]` trên Mobile để ảnh nền luôn phủ kín (`inset-0 w-full h-full object-cover object-bottom`) ngay cả khi văn bản CMS dài hơn mặc định, không bị cắt chữ (`overflow-hidden` không cắt cụt nội dung).
- **[Risk] Cache cũ `history_index_{locale}` giữ dữ liệu trước khi chạy migration** → **Mitigation**: Cuối mỗi Seeder luôn gọi `Slider::clearSliderCaches()` và `Config::clearBrandCaches()` (đã xóa `history_index_vi` & `history_index_en`).

## Migration Plan

1. Thực thi tuần tự từ `Stage 1` đến `Stage 5`: ở mỗi Stage, tải đúng ảnh Figma của Stage đó vào `public/assets/images/about/`, tạo Seeder + Migration `2026_11_26_00000X_seed_about_*_data.php`, chạy `php artisan migrate --path=...` nạp dữ liệu vào DB local và dựng component Vue tương ứng.
2. Khi deploy lên VPS qua CI/CD: GitHub Actions build sẵn `public/build` & `bootstrap/ssr` (sau khi thêm `General.vue` cũng kiểm tra build backend nếu cần) và chạy `php artisan migrate --force` trên VPS — toàn bộ 5 migration tự động gọi 5 Seeder nạp dữ liệu song ngữ VI/EN mà không cần chạy lệnh seed thủ công.
3. Rollback: Các hàm `down()` được thiết kế không xóa dữ liệu (`no-op`) để đảm bảo an toàn tuyệt đối cho dữ liệu CMS đang vận hành.
