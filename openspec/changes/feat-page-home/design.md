# Design: Xây dựng Trang Chủ Thành Khang (`feat-page-home`)

## 1. Đối chiếu Kiến trúc Hiện hữu từ Graphify AST (`graphify-out/graph.json`)

Tra cứu `graphify-out/graph.json` (9.659 nodes) xác nhận toàn bộ các file cốt lõi của Trang Chủ đã tồn tại, do đó chiến lược kiến trúc là **tái sử dụng và tái cấu trúc trực tiếp** thay vì sinh file trùng lặp:

| Tầng / Section Figma | File hiện hữu trong Repo | Quyết định kiến trúc |
| :--- | :--- | :--- |
| **Trang Chủ Root** | `resources/Frontend/js/Pages/Home/Index.vue` | Khai báo tường minh `layout: MainLayout`, chuyển nền sang `bg-surface text-ink-body overflow-x-clip`, liên kết props từ `HomeController::index()`. |
| **Stage 1 — Hero Banner (`595:15536`)** | `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` | Refactor chuẩn Vue 3 `<script setup>` + `defineProps({ sliders })`, khóa `aspect-[375/480] md:aspect-[1440/582]`, trượt ngang `translate-x`. |
| **Stage 2 — About Intro** | `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` | Tái sử dụng & refactor theo Figma About Intro (2 đoạn văn + 3 ảnh + CTA từ CMS). |
| **Stage 3 — Product Categories** | `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` | Tái sử dụng & refactor hiển thị 4 danh mục gốc của khách hàng từ CMS (sửa query `HomeController`). |
| **Stage 4 — Why Choose Us** | `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` | Tái sử dụng & refactor hiển thị 5 trụ cột tin tưởng + ảnh banner nền từ CMS. |
| **Stage 5 — Process (4 steps)** | `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` | Tái sử dụng & refactor hiển thị đúng 4 bước quy trình + banner nền từ CMS. |
| **Stage 6 — Blog / News (3 cards)** | Component Blog trên Home (tái sử dụng `CardBlog.vue` hoặc tạo section gọn nếu chưa có file riêng trong `Home/Components/`) | Liên kết `featured_posts` từ `HomeController::index()` (3 bài viết `type='POST'`). |
| **Stage 7 — Contact & Quote Form** | `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` | Tái sử dụng & refactor gắn `$page.props.global` + submit `POST /contacts` (`contact.store`). |
| **Backend Controllers** | `app/Http/Controllers/Frontend/HomeController.php`<br>`app/Http/Controllers/Frontend/ContactController.php` | Chuẩn hóa payload trả về (đặc biệt mảng `sliders` và sửa bug lọc `categories` cấp gốc). |

> Các component cũ không dùng trên Trang Chủ mới (`AnnouncementTicker.vue`, `HighlightMetrics.vue`, `HomeFooter.vue`, `FeaturedProductsCarousel.vue`) được **giữ nguyên trên đĩa (không xóa)** theo quy tắc Strict Safe Guard.

---

## 2. Kết quả Kiểm toán CMS & Chiến lược Migration Idempotent

Dựa trên kiểm toán thực tế `database/database.sqlite` (#18) và xác nhận của người dùng:

| Stage | Hiện trạng DB | Giải pháp Backend & CMS-First | Bảo toàn dữ liệu |
| :--- | :--- | :--- | :--- |
| **1. Hero Banner** | `sliders`: 0 rows | `ThanhKhangHomeSliderSeeder` + migration idempotent `2026_11_22_000001_seed_home_hero_sliders_data.php` nạp 3 slide `HOME_SLIDER` (VI/EN) với ảnh `/assets/images/homepage/hero-banner-1.png`. | Không chạm bảng khác; re-run an toàn. |
| **2. About Intro** | 0 rows / thiếu keys | Thêm cấu hình CMS (settings/slider) + migration idempotent nạp nội dung 2 đoạn, 3 ảnh, CTA. | Chỉ thêm keys mới bằng `updateOrCreate`. |
| **3. Product Categories** | 26 categories (4 root, 5 tier-2, 17 leaf), 103 products gắn ở leaf -> `HomeController` trả `[]` | Sửa `HomeController::index()` kiểm tra đệ quy danh mục con/lá hoặc lấy thẳng danh mục gốc active. | **GIỮ NGUYÊN 100%** 4 danh mục gốc & toàn bộ sản phẩm của khách hàng. |
| **4. Why Choose Us** | 0 rows | Bổ sung cấu hình CMS 5 trụ cột + banner nền qua migration idempotent. | Chỉ thêm dữ liệu mới. |
| **5. Process** | `settings.process_steps` có 5 bước; Figma có 4 bước | Chuẩn hóa hiển thị 4 bước chuẩn Figma + migration idempotent banner nền quy trình. | Không xóa cấu hình thương hiệu khác. |
| **6. Blog / News** | `posts (type=POST)`: 0 rows (chỉ có 6 `FAQ`) | Seeder + migration idempotent nạp 3 bài viết mẫu chuẩn Figma (`type='POST'`). | Giữ nguyên 6 bài `FAQ`. |
| **7. Contact & Quote** | `contact.store` đã sẵn sàng | Bind trực tiếp `$page.props.global` + `POST /contacts` (`vi.contact.store` / `en.contact.store`). | Không sửa bảng `contacts`. |

---

## 3. Thiết kế Chi tiết Stage 1 — Hero Banner (`595:15536`)

### 3.1. Hợp đồng Dữ liệu Inertia (`props.sliders`)
`HomeController::index()` chuẩn hóa `sliders` luôn là mảng tuần tự (`Array<Slide>`):
```json
{
  "id": 1,
  "subtitle": "Enduring motion",
  "title": "CHUYỂN ĐỘNG BỀN BỈ",
  "description": "Cung cấp giải pháp truyền động và thiết bị cơ khí chính xác...",
  "primary_cta_text": "Xem sản phẩm",
  "primary_cta_link": "/san-pham",
  "secondary_cta_text": "Liên hệ tư vấn",
  "secondary_cta_link": "#contact-section",
  "image": { "url": "/assets/images/homepage/hero-banner-1.png", "alt": "..." },
  "image_mobile": { "url": "/assets/images/homepage/hero-banner-1.png", "alt": "..." }
}
```

### 3.2. Thông số Layout & Visual chuẩn Figma (`595:15536`)
- **Aspect-Ratio Lock**: Chiều cao vùng Hero dưới Header `118px` là `582px` (`700 - 118`) trên `1440px` -> `aspect-[375/480] md:aspect-[1440/582] w-full relative overflow-hidden`.
- **Slide Background**: `w-full h-full object-cover object-center`, hỗ trợ ảnh mobile (`image_mobile?.url || image?.url`).
- **Scrim Overlay**: `bg-gradient-to-r from-[#01101E]/60 via-[#01101E]/35 to-transparent` (Desktop) kèm scrim nhẹ toàn vùng trên Mobile để đảm bảo tương phản chữ.
- **Horizontal Track (LUXI Animation Rules)**: `flex transition-transform duration-500 ease-out` với `transform: translateX(-${activeIndex * 100}%)`, tuyệt đối không dùng fade `opacity: 0` gây trắng màn hình; tôn trọng `prefers-reduced-motion`.
- **Typography & Controls**:
  - Container: `max-w-[1280px] mx-auto px-4 md:px-8 h-full flex items-end justify-between pb-6 md:pb-10 pt-8 md:pt-16`.
  - Khối chữ trái (`max-w-[542px]`):
    - Eyebrow: `bg-gradient-to-r from-[#009FFF] to-[#C9E0FF] bg-clip-text text-transparent font-normal text-lg md:text-[28px] leading-[1.5] md:leading-[1.6]`.
    - Title (`<h1>` cho slide chính, `<p>` cho slide phụ): `font-bold text-[28px] sm:text-[32px] md:text-[48px] leading-[1.25] md:leading-[1.4] text-white uppercase`.
    - Description: `mt-2 font-medium text-sm md:text-[18px] leading-[1.6] text-[#C9E0FF]`.
    - 2 nút CTA (`rounded-[4px]`): Primary `bg-[#005EA4] hover:bg-[#004D86] text-[#FAFAFA]`, Secondary `bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967]`.
    - Pagination squares: `w-[10px] h-[10px] rounded-[2px]` (active `bg-[#FAFAFA]`, inactive `border border-[#FAFAFA]`).
  - Nút mũi tên phải (`gap-3`): `w-10 h-10 rounded-[4px] bg-[#394C6B]/80 hover:bg-[#394C6B]`, dùng icon `/assets/svg/icons/hero-chevron-left.svg` (`rotate-180` cho nút Next).

### 3.3. Danh mục Tài nguyên Stage 1
| Asset | Đường dẫn Public (Runtime) | Mục đích |
| :--- | :--- | :--- |
| Hero Banner 1 | `/assets/images/homepage/hero-banner-1.png` | Ảnh nền mặc định cho 3 slide `HOME_SLIDER` |
| Hero Top Effect | `/assets/svg/homepage/hero-effect-top.svg` | Hiệu ứng ánh sáng / trang trí đỉnh Hero |
| Hero Chevron Left | `/assets/svg/icons/hero-chevron-left.svg` | Icon mũi tên Prev / Next (xoay 180°) |

---

## 4. Thiết kế Chi tiết Stage 2 — About Intro ("Về chúng tôi", Figma `595:15362`)

### 4.1. Hợp đồng Dữ liệu CMS (`aboutSection` + `HOME_ABOUT_GALLERY` + `home_about_*`)
- **Slider position mới** trong `config/slider.php`: `HOME_ABOUT_GALLERY` (3 ảnh `position_sort` DESC: `30`, `20`, `10` cho thứ tự `[gear, chain, engineer]`; giữ nguyên `BANNER_HOME_FOUNDER` tương thích ngược).
- **Settings keys (`group = 'general'`, VI + EN)** trong `app/Models/Config.php` (`Config::getHomeAboutSettings($locale)`):
  - `home_about_eyebrow` / `home_about_eyebrow_en`
  - `home_about_lead_highlight` / `home_about_lead_highlight_en`
  - `home_about_lead_rest` / `home_about_lead_rest_en`
  - `home_about_paragraph_1` / `home_about_paragraph_1_en`
  - `home_about_paragraph_2` / `home_about_paragraph_2_en`
  - `home_about_commitment_quote` / `home_about_commitment_quote_en`
  - `home_about_commitment_sub` / `home_about_commitment_sub_en`
  - `home_about_cta_text` / `home_about_cta_text_en`
  - `home_about_cta_link` / `home_about_cta_link_en`
- **Idempotent Seeder & Migration**: `database/seeders/ThanhKhangHomeAboutSeeder.php` + `database/migrations/2026_11_22_000002_seed_home_about_section_data.php` (chỉ thêm key thiếu/rỗng và chỉ seed 3 ảnh `HOME_ABOUT_GALLERY` khi đang có 0 rows).
- **Inertia Prop `aboutSection`** từ `HomeController::index()`:
```json
{
  "eyebrow": "Về chúng tôi",
  "lead_highlight": "Công ty TNHH Thương Mại và Dịch Vụ Kỹ Thuật Thành Khang (THANH KHANG Tecsat)",
  "lead_rest": "là nhà phân phối các sản phẩm chính hãng...",
  "paragraphs": ["Đoạn 1...", "Đoạn 2..."],
  "commitment_quote": "\"Đúng nguồn gốc – Đúng thông số – Đúng tiến độ\"",
  "commitment_sub": "Đó là cam kết chúng tôi mang đến cho mỗi khách hàng",
  "cta_text": "Xem thêm về chúng tôi",
  "cta_link": "/gioi-thieu",
  "images": [
    { "id": 1, "url": "/assets/images/homepage/about-gear-1.png", "alt": "..." },
    { "id": 2, "url": "/assets/images/homepage/about-chain-2.png", "alt": "..." },
    { "id": 3, "url": "/assets/images/homepage/about-engineer-3.png", "alt": "..." }
  ]
}
```

### 4.2. Thông số Layout & Visual chuẩn Figma (`595:15362`)
- **Section Wrapper**: `<section id="about-intro-section" class="bg-surface py-8 md:py-16 lg:pt-20 lg:pb-10">`, ẩn sạch với `v-if="hasContent"` nếu DB trống (0 text hardcode trong `<script>`), không dùng `opacity: 0` scroll reveal.
- **Container**: `.app-container` (`max-w-[1216px] mx-auto px-4 md:px-8` -> đúng chiều rộng nội dung `1152px` như Figma `1440 - 2×144`), `flex flex-col gap-6 md:gap-8`.
- **Row 1 (Top Row)**: `grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-stretch`:
  - Cột trái: Eyebrow (`w-3 h-3 rounded-[2px] bg-[#005EA4]` + `font-bold text-[16px] md:text-[18px] text-[#005EA4]`) + Lead `<h2>` (`font-bold text-[20px] md:text-[24px] leading-[1.55] md:leading-[1.6]`, highlight `#005EA4` + phần tiếp theo `#9AA4B2`).
  - Cột phải (`grid grid-cols-2 gap-3 md:gap-6`): 2 ảnh đầu khóa tỷ lệ `aspect-[268/272] rounded-[4px] bg-[#EEF2F6]` (`w-full h-full object-cover object-center`).
- **Row 2 (Bottom Row)**: `grid grid-cols-1 lg:grid-cols-2 gap-6 lg:gap-8 items-end`:
  - Cột trái: Ảnh thứ 3 khóa tỷ lệ `aspect-[560/363] rounded-[4px] bg-[#EEF2F6]` (`w-full h-full object-cover object-center`).
  - Cột phải (`flex flex-col items-start justify-end gap-6 md:gap-10`): 2 đoạn văn (`text-[15px] md:text-[16px] leading-[1.6] text-[#364152]`), câu cam kết in đậm `text-[#005EA4]`, và nút CTA `bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] rounded-[4px] px-6 py-[10px]`.

### 4.3. Danh mục Tài nguyên Stage 2
| Asset | Đường dẫn Public (Runtime) | Kích thước khung Figma |
| :--- | :--- | :--- |
| About Image 1 (Gear) | `/assets/images/homepage/about-gear-1.png` | `268×272` (`aspect-[268/272]`) |
| About Image 2 (Chain) | `/assets/images/homepage/about-chain-2.png` | `268×272` (`aspect-[268/272]`) |
| About Image 3 (Engineer) | `/assets/images/homepage/about-engineer-3.png` | `560×363` (`aspect-[560/363]`) |

---

## 5. Thiết kế Chi tiết Stage 3 — Product Categories ("Danh mục sản phẩm", Figma `595:15380`)

### 5.1. Bảo toàn Dữ liệu Khách hàng & Sửa Truy vấn `HomeController`
- **Strict Safe Guard**: Bảo toàn 100% (chỉ ĐỌC, tuyệt đối không sửa/thêm/xóa) toàn bộ `product_categories` (26 bản ghi: 4 danh mục gốc + 22 danh mục con/lá), `product_category_translations`, `products` (103 sản phẩm), và `product_ref_categories`.
- **Sửa truy vấn trong `HomeController::index()`**:
  - Truy vấn trực tiếp toàn bộ danh mục gốc active theo thứ tự DB: `ProductCategory::query()->active()->whereParent()->sortByPosition()->with(['children', 'nodes'])->get()` (gỡ bỏ bộ lọc đếm trực tiếp `$cat->products` gây mảng rỗng `[]` do sản phẩm nằm ở cấp lá).
  - Tạo URL bản địa hóa `($locale === 'en' ? '/en/products' : '/san-pham') . '?category=' . urlencode($slug)` và giữ nguyên cây `children` của khách hàng.
  - **Chiến lược giải quyết ảnh theo 3 tầng ưu tiên**:
    1. Ảnh khách hàng upload trong DB (`$cat->image`).
    2. Nếu DB `image` rỗng, đối chiếu tự động theo slug VI/EN với ảnh Figma cục bộ trong `public/assets/images/homepage/cat-{$slug}.png`.
    3. Nếu vẫn rỗng (danh mục mới do admin thêm), fallback sang ảnh sản phẩm active đầu tiên thuộc cây danh mục con (`$cat->allChildrenId([], $cat->id)`), hoặc `/assets/images/homepage/cat-explore-all.png`.

### 5.2. Hợp đồng Slider `BANNER_HOME_CATEGORIES_CTA` & Props
- **Slider position mới** trong `config/slider.php`: `BANNER_HOME_CATEGORIES_CTA` (`banner => true`), nạp 1 bản ghi song ngữ VI/EN qua `ThanhKhangHomeCategoriesCtaSeeder` + migration idempotent `database/migrations/2026_11_22_000003_seed_home_categories_cta_data.php` (chỉ seed khi count === 0, non-destructive `down()`).
- **Inertia Props**:
  - `categories`: `Array<{ id, title, name, slug, url, description, image: { url, alt }, children }>`
  - `categoriesCtaBanner`: `{ title, subtitle, link, image: { url, alt } } | null`

### 5.3. Thông số Hình học & Giao diện chuẩn Figma (`595:15380`)
- **Section Wrapper**: `<section id="product-categories-section" class="bg-[#00284B] py-8 md:py-16 lg:pt-10 lg:pb-20">`, container `.app-container` (`1152px` inner width, `px-4 md:px-8`), `flex flex-col gap-6 md:gap-8`, không dùng `opacity: 0` scroll reveal.
- **Section Header**: Ô vuông trắng `w-3 h-3 rounded-[2px] bg-[#FAFAFA]` + `<h2>` `font-bold text-[16px] md:text-[18px] text-[#FAFAFA]`.
- **Responsive Grid**: `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 md:gap-6`.
- **Standard Category Card** (Figma `368×394`): Nền `#FAFAFA`, `rounded-[4px]`, khung ảnh khóa tỷ lệ `aspect-[368/308]` (`w-full h-full object-cover object-center`), hàng chân thẻ `min-h-[76px] md:min-h-[86px]` gồm tiêu đề uppercase (`#364152`, `16px md:20px font-medium`) và ô mũi tên `w-9 h-9 rounded-[4px] bg-[#005EA4]` chứa `/assets/svg/icons/chevron-right-white.svg`.
- **Explore All CTA Tile** (`categoriesCtaBanner`): Nền `#005EA4` (`hover:bg-[#004D86]`), khung ảnh `aspect-[368/308]`, chữ `#FAFAFA`, ô mũi tên `bg-[#C9E0FF] border border-[#81BDFF]` chứa `/assets/svg/icons/chevron-right-dark.svg`.

### 5.4. Danh mục Tài nguyên Stage 3
| Asset | Đường dẫn Public (Runtime) | Mục đích |
| :--- | :--- | :--- |
| Linh kiện thang cuốn | `/assets/images/homepage/cat-linh-kien-thang-cuon.png` | Ảnh thẻ danh mục `linh-kien-thang-cuon` (`aspect-[368/308]`) |
| Linh kiện thang nâng ô tô | `/assets/images/homepage/cat-linh-kien-thang-nang-o-to.png` | Ảnh thẻ danh mục `linh-kien-thang-nang-o-to` (`aspect-[368/308]`) |
| Linh kiện dây chuyền đóng gói | `/assets/images/homepage/cat-linh-kien-day-chuyen-dong-goi.png` | Ảnh thẻ danh mục `linh-kien-day-chuyen-dong-goi` (`aspect-[368/308]`) |
| Hệ thống băng tải hành lý | `/assets/images/homepage/cat-he-thong-bang-tai-hanh-ly.png` | Ảnh thẻ danh mục `he-thong-bang-tai-hanh-ly` (`aspect-[368/308]`) |
| Giải pháp tiết kiệm điện | `/assets/images/homepage/cat-giai-phap-tiet-kiem-dien.png` | Ảnh thẻ danh mục `giai-phap-tiet-kiem-dien` (`aspect-[368/308]`) |
| Khám phá toàn bộ sản phẩm | `/assets/images/homepage/cat-explore-all.png` | Ảnh nền thẻ CTA `BANNER_HOME_CATEGORIES_CTA` |
| Icon Chevron Right White | `/assets/svg/icons/chevron-right-white.svg` | Icon mũi tên trên thẻ danh mục chuẩn (`20×20`) |
| Icon Chevron Right Dark | `/assets/svg/icons/chevron-right-dark.svg` | Icon mũi tên trên thẻ CTA màu xanh (`20×20`) |

---

## 6. Thiết kế Chi tiết Stage 4 — Why Choose Us ("Vì sao khách hàng tin tưởng chọn Thành Khang?", Figma `595:15394`)

### 6.1. Hợp đồng Dữ liệu CMS (`BANNER_HOME_TRUST` + `HOME_TRUST_PILLARS` + `trustSection`)
- **2 Slider positions mới** trong `config/slider.php`:
  - `BANNER_HOME_TRUST` (`banner => true`): 1 bản ghi ảnh nền (`/assets/images/homepage/why-choose-us-bg.png`) kèm tiêu đề/mô tả song ngữ VI/EN.
  - `HOME_TRUST_PILLARS` (`banner => false`): 5 bản ghi trụ cột giá trị với `position_sort` giảm dần (`50, 40, 30, 20, 10`) để trả về đúng thứ tự Figma 1..5 (`Uy tín tận tâm`, `Hàng chính hãng`, `Quy trình tối ưu`, `Giao hàng nhanh`, `Hỗ trợ kỹ thuật`), mỗi mục gắn icon SVG cục bộ tương ứng.
- **Idempotent Seeder & Migration**: `database/seeders/ThanhKhangHomeTrustSeeder.php` + `database/migrations/2026_11_22_000004_seed_home_trust_section_data.php` (kiểm tra theo `position` + `position_sort`, không bao giờ trùng dòng khi chạy lại, `down()` không phá hủy; tuyệt đối không chạm các bảng khách hàng).
- **Inertia Prop `trustSection`** từ `HomeController::index()` (giữ nguyên `services` prop tương thích ngược):
```json
{
  "heading": "Vì sao khách hàng tin tưởng chọn Thành Khang?",
  "description": "5 giá trị cốt lõi tạo nên niềm tin của các đối tác công nghiệp tại Thành Khang Tecsat",
  "banner": {
    "url": "/assets/images/homepage/why-choose-us-bg.png",
    "mobile_url": "/assets/images/homepage/why-choose-us-bg.png",
    "alt": "..."
  },
  "items": [
    { "id": 1, "title": "Uy tín tận tâm", "description": "...", "icon": { "url": "/assets/svg/icons/trust-thumbs-up.svg", "alt": "..." } }
  ]
}
```

### 6.2. Thông số Layout & Visual chuẩn Figma (`595:15394`)
- **Section Banner khóa tỷ lệ Desktop**: Khung `1440×578` trên Desktop (`lg:aspect-[1440/578]` + `w-full h-full object-cover object-center` cho ảnh nền + lớp phủ scrim tương phản cao), nội dung đặt trong `.app-container`, không dùng `v-scroll-reveal` / `opacity: 0`.
- **Desktop 5-Node Wave Diagram (`1152×260`, hiển thị ở `lg+`)**:
  - 4 thanh cầu nối SVG (`trust-bridge-1.svg`, `trust-bridge-2.svg`, `trust-bridge-3.svg`, `trust-bridge-2.svg` lật/xoay đúng góc `24.58°` / `-24.58°`).
  - 5 nút tròn `140×140px` (`trust-ring-outer.svg` / `trust-ring-alt.svg` + đĩa nền `#00284B` `120×120px` + icon `60×60px`) đặt so le theo trục sóng (`top-[120px]` / `top-0`), kèm hộp chữ (`<h3>` `24px Bold #FAFAFA`, `<p>` `14px Medium #F8FAFC`) căn trái/giữa/phải chuẩn tọa độ Figma.
- **Mobile & Tablet Responsive Stack (`< lg`)**: Lưới dọc `grid-cols-1 sm:grid-cols-2 gap-4 md:gap-6` với các thẻ nền `bg-[#00284B]/65 border border-[#C9E0FF]/25 rounded-[8px] p-4`, vòng icon `56×56px` (`w-14 h-14`) + tiêu đề `18px Bold` + mô tả `14px Medium`, không tràn ngang ở `375px`.

### 6.3. Danh mục Tài nguyên Stage 4 (11 Local Assets)
| Asset | Đường dẫn Public (Runtime) | Mục đích |
| :--- | :--- | :--- |
| Why Choose Us Background | `/assets/images/homepage/why-choose-us-bg.png` | Ảnh nền banner `BANNER_HOME_TRUST` (`1440×578`) |
| Trust Bridge 1 | `/assets/svg/homepage/trust-bridge-1.svg` | Cầu nối SVG node 1 → 2 |
| Trust Bridge 2 | `/assets/svg/homepage/trust-bridge-2.svg` | Cầu nối SVG node 2 → 3 & node 4 → 5 |
| Trust Bridge 3 | `/assets/svg/homepage/trust-bridge-3.svg` | Cầu nối SVG node 3 → 4 |
| Trust Ring Outer | `/assets/svg/homepage/trust-ring-outer.svg` | Vòng viền ngoài `140×140` (nodes 1, 3, 4, 5) |
| Trust Ring Alt | `/assets/svg/homepage/trust-ring-alt.svg` | Vòng viền ngoài `140×140` (node 2) |
| Icon Uy tín tận tâm | `/assets/svg/icons/trust-thumbs-up.svg` | Icon trụ cột 1 (`60×60`) |
| Icon Hàng chính hãng | `/assets/svg/icons/trust-check-circle.svg` | Icon trụ cột 2 (`60×60`) |
| Icon Quy trình tối ưu | `/assets/svg/icons/trust-clipboard.svg` | Icon trụ cột 3 (`60×60`) |
| Icon Giao hàng nhanh | `/assets/svg/icons/trust-truck.svg` | Icon trụ cột 4 (`60×60`) |
| Icon Hỗ trợ kỹ thuật | `/assets/svg/icons/trust-tool.svg` | Icon trụ cột 5 (`60×60`) |

---

## 7. Thiết kế Chi tiết Stage 5 — Process ("Quy trình", Figma `595:15441`, 4 bước)

### 7.1. Hợp đồng Dữ liệu CMS (`BANNER_HOME_PROCESS` + `HOME_PROCESS_STEPS` + `processSection`)
- **Bảo toàn trang `/quy-trinh`**: Tuyệt đối không sửa `ProcessController.php` hay `Config::getProcessSteps()` (trang `/quy-trinh` tiếp tục giữ nguyên quy trình 5 bước của nó).
- **2 Slider positions mới** trong `config/slider.php`:
  - `BANNER_HOME_PROCESS` (`banner => true`): 1 bản ghi ảnh nền cột trái (`/assets/images/homepage/process-left-bg.png`) kèm tiêu đề/mô tả song ngữ VI/EN.
  - `HOME_PROCESS_STEPS` (`banner => false`): 4 bản ghi quy trình với `position_sort` giảm dần (`40, 30, 20, 10`) để trả về đúng thứ tự Figma 1..4 (`1. Gửi yêu cầu`, `2. Tư vấn & Báo giá`, `3. Sản xuất & kiểm định`, `4. Giao hàng & hỗ trợ`), số thứ tự lưu trong `subtitle`.
- **Idempotent Seeder & Migration**: `database/seeders/ThanhKhangHomeProcessSeeder.php` + `database/migrations/2026_11_22_000005_seed_home_process_section_data.php` (kiểm tra theo `position` + `position_sort`, chạy lại 0 trùng dòng, `down()` không phá hủy).
- **Inertia Prop `processSection`** từ `HomeController::index()` (vẫn giữ `processSteps` tương thích ngược):
```json
{
  "heading": "QUY TRÌNH TIẾP NHẬN & LÀM VIỆC TẠI THÀNH KHANG TECSAT",
  "description": "Xích công nghiệp chính hãng, đa dạng kích thước...",
  "banner": {
    "url": "/assets/images/homepage/process-left-bg.png",
    "mobile_url": "/assets/images/homepage/process-left-bg.png",
    "alt": "..."
  },
  "steps": [
    { "id": 15, "number": "1", "title": "Gửi yêu cầu", "description": "..." },
    { "id": 16, "number": "2", "title": "Tư vấn & Báo giá", "description": "..." },
    { "id": 17, "number": "3", "title": "Sản xuất & kiểm định", "description": "..." },
    { "id": 18, "number": "4", "title": "Giao hàng & hỗ trợ", "description": "..." }
  ]
}
```

### 7.2. Thông số Layout & Visual chuẩn Figma (`595:15441`)
- **Desktop Split Layout (`1440×954`, `flex-col lg:flex-row lg:min-h-[954px]`)**:
  - **Cột trái (`lg:w-[51.32%]` = `739/1440`, bo góc phải `lg:rounded-tr-[8px] lg:rounded-br-[8px]`)**: Ảnh nền full-bleed `process-left-bg.png` (`aspect-[375/340] sm:aspect-[16/10] lg:aspect-auto lg:min-h-[954px]`, `object-cover object-center`) + scrim tương phản + khối tiêu đề `<h2>` (`28px Bold #FAFAFA uppercase`) & mô tả (`16px Medium #E3E8EF`) căn lề trái theo `.app-container` (`144px` trên `1440px`) và `lg:sticky lg:top-[104px]`.
  - **Cột phải (`lg:flex-1`, `lg:pl-[64px]`, card rộng `493px` trên `1440px`)**: Danh sách `<ol>` gồm 4 thẻ bước xếp dọc (`gap-4 md:gap-6 lg:gap-[40px]`), nền xanh đậm dần theo cấp độ:
    - Bước 1: `#005EA4`
    - Bước 2: `#004D86`
    - Bước 3: `#003967`
    - Bước 4: `#00284B`
  - Mỗi thẻ (`rounded-[12px] shadow-[2px_2px_3px_rgba(0,0,0,0.25)] min-h-[140px] md:min-h-[166px]`): số bước lớn `44px–64px Bold #C9E0FF` bên trái + tiêu đề `18px–20px Bold #C9E0FF` & mô tả `14px–16px Regular #C9E0FF` bên phải.
- **Mobile Stacked Layout (`375px`)**: Cột ảnh tiêu đề xếp trên (`aspect-[375/340]`, padding `px-4 py-8`), 4 thẻ bước xếp dọc ngay bên dưới với `px-4 py-8`, `gap-4`, 0 tràn ngang, không dùng `opacity: 0` scroll reveal.

### 7.3. Danh mục Tài nguyên Stage 5 (3 Local Assets)
| Asset | Đường dẫn Public (Runtime) | Mục đích |
| :--- | :--- | :--- |
| Process Left Sprocket Photo | `/assets/images/homepage/process-left-bg.png` | Ảnh nền cột trái `BANNER_HOME_PROCESS` (`739×954` desktop) |
| Process Wave Background | `/assets/images/homepage/process-wave-bg.png` | Họa tiết sóng nền trang trí section Quy trình |
| Process Wave Mask | `/assets/svg/homepage/process-wave-mask.svg` | Mask SVG cho lớp sóng nền Quy trình |

---

## 8. Thiết kế Chi tiết Stage 6 — Blog / News ("Tin tức", Figma `595:15483`)

### 8.1. Hợp đồng Dữ liệu CMS (`posts` `type='POST'` + `post_categories` + `featured_posts`)
- **Bảo toàn dữ liệu hiện có**: Tuyệt đối không chạm 6 bản ghi `FAQ` sẵn có trong `posts` (tổng sau migration là `9` bản ghi = `6 FAQ + 3 POST`) cùng toàn bộ `product_categories=26`, `products=103`, `policies=6`, `settings=62`, `sliders=18`.
- **Active `PostCategory`**: Tái sử dụng danh mục `type='POST'` active nếu có, ngược lại tạo 1 danh mục active song ngữ (`Kiến thức kỹ thuật` / `kien-thuc-ky-thuat` và `Technical Insights` / `technical-insights`) để đảm bảo `PostController::index()` và `Posts/Show.vue` hiển thị đầy đủ `post.category.title`.
- **Idempotent Seeder & Migration**: `database/seeders/ThanhKhangHomeSamplePostsSeeder.php` + `database/migrations/2026_11_22_000006_seed_home_sample_posts_data.php` (đối chiếu idempotent theo slug tiếng Việt trong `post_translations`, safe no-op `down()`). Nạp 3 bài viết `type='POST'` chuẩn Figma (thay thế đoạn lorem ipsum Latin trong Figma bằng tóm tắt kỹ thuật & nội dung HTML `<h2>`/`<p>`/`<ul>` thật song ngữ VI/EN):
  1. `position=1`, `is_featured=1`, `published_at='2024-10-26 09:00:00'` (`Oct 26, 2024`) — *Xích con lăn tiêu chuẩn ANSI là gì? Kiến thức cơ bản cho người mới*
  2. `position=2`, `is_featured=1`, `published_at='2024-12-12 09:00:00'` (`Dec 12, 2024`) — *Bảo trì xích thang cuốn: Lịch kiểm tra, bôi trơn và tiêu chí thay thế*
  3. `position=3`, `is_featured=0`, `published_at='2024-12-22 09:00:00'` (`Dec 22, 2024`) — *Ứng dụng xích tải công nghiệp: chọn đúng loại cho từng dây chuyền*
- **Local Asset Guard**: Trong `Post::getImageDetail()`, nếu đường dẫn bắt đầu bằng `/assets/` hoặc `/images/` thì trả về trực tiếp không gắn tiền tố `/storage/` để hiển thị đồng bộ trên Trang Chủ, `/tin-tuc`, và `/tin-tuc/{slug}`.
- **Inertia Prop `featured_posts`** từ `HomeController::index()`:
```json
[
  {
    "id": 7,
    "title": "Xích con lăn tiêu chuẩn ANSI là gì? Kiến thức cơ bản cho người mới",
    "slug": "xich-con-lan-tieu-chuan-ansi-la-gi",
    "description": "...",
    "published_at": "2024-10-26 09:00:00",
    "formatted_date": "Oct 26, 2024",
    "is_featured": true,
    "url": "/tin-tuc/xich-con-lan-tieu-chuan-ansi-la-gi",
    "image": { "url": "/assets/images/homepage/blog-ansi-roller-chain.png", "alt": "..." },
    "category": { "id": 1, "title": "Kiến thức kỹ thuật", "slug": "kien-thuc-ky-thuat" }
  }
]
```

### 8.2. Thông số Layout & Visual chuẩn Figma (`595:15483`)
- **Section Wrapper**: `<section id="blog-section" class="bg-[#FAFAFA] py-8 md:py-16 lg:py-20">`, container `.app-container flex flex-col gap-6` (`1152px` inner width trên `1440px`, `px-4 md:px-8`), ẩn sạch nếu `featured_posts` rỗng, không dùng `opacity: 0` scroll reveal.
- **Section Header (`595:15484`)**: Ô vuông xanh `w-3 h-3 rounded-[2px] bg-[#005EA4]` + `<h2>` `font-bold text-[16px] md:text-[18px] leading-[1.6] text-[#005EA4]` (VI `Tin tức` / EN `News`).
- **3-Column Blog Grid (`595:15490`)**: `grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6` (3 thẻ `368px` trên Desktop, 1 cột trên Mobile), khoảng cách từ lưới tới nút CTA `gap-8 md:gap-12 lg:gap-[64px]`:
  - **Thumbnail**: Khóa tỷ lệ `aspect-[368/350] bg-[#F1F1F1] rounded-[8px] overflow-hidden` (`w-full h-full object-cover object-center`).
  - **Badge `New` (CMS-driven)**: Hiển thị khi `post.is_featured || post.is_new` tại `top-[20px] left-[20px]`, nền `#0075E7`, chữ `#FAFAFA` `14px Medium`, `px-[12px] py-[4px] rounded-[4px]`.
  - **Nội dung thẻ**: Tiêu đề `<h3>` `18px md:20px Bold #00284B` (`line-clamp-2 md:min-h-[64px]`), mô tả `<p>` `14px md:16px Regular #697586` (`line-clamp-3`), ngày đăng `<time>` `14px Medium #005EA4` (`formatted_date` dạng `Oct 26, 2024`).
- **Bottom Centered CTA (`595:15494`)**: Nút `bg-[#005EA4] hover:bg-[#004D86] text-[#FAFAFA] font-medium text-[16px] px-[24px] py-[10px] rounded-[4px]` nhãn `Xem thêm bài viết` (`/tin-tuc`) hoặc `View more articles` (`/en/posts`).

### 8.3. Danh mục Tài nguyên Stage 6 (3 Local Assets)
| Asset | Đường dẫn Public (Runtime) | Khung Figma |
| :--- | :--- | :--- |
| Blog Card 1 (ANSI Roller Chain) | `/assets/images/homepage/blog-ansi-roller-chain.png` | `368×350` (`aspect-[368/350]`) |
| Blog Card 2 (Escalator Maintenance) | `/assets/images/homepage/blog-escalator-chain-maintenance.png` | `368×350` (`aspect-[368/350]`) |
| Blog Card 3 (Conveyor Chain) | `/assets/images/homepage/blog-industrial-conveyor-chain.png` | `368×350` (`aspect-[368/350]`) |

---

## 9. Thiết kế Chi tiết Stage 7 — Contact & Quote Form ("KẾT NỐI & NHẬN BÁO GIÁ", Figma `595:15496`)

### 9.1. Hợp đồng Dữ liệu CMS & Submit Flow (`home_contact_*` + `$page.props.global` + `contact.store`)
- **Settings keys (`group = 'general'`, VI + EN)** trong `app/Models/Config.php` (`Config::getHomeContactSettings($locale)`, EN → VI fallback, giữ nguyên toàn bộ settings thương hiệu sẵn có):
  - `home_contact_title` / `home_contact_title_en`
  - `home_contact_description` / `home_contact_description_en`
  - Đồng thời bảo toàn `showroom_address_en` trong `normalizeGeneralSettings()` (không ghi đè địa chỉ VPGD/Showroom bằng địa chỉ trụ sở trên EN).
- **Idempotent Seeder & Migration**: `database/seeders/ThanhKhangHomeContactSeeder.php` + `database/migrations/2026_11_22_000007_seed_home_contact_section_data.php` (chỉ thêm 4 key `home_contact_*` khi thiếu/rỗng, safe no-op `down()`).
- **Inertia Prop `contactSection`** từ `HomeController::index()`: `{ title, description }` kết hợp thông tin liên hệ sống từ `$page.props.global` (`hotline` / `general_phone`, `general_email`, `general_address`, `showroom_address`).
- **Lead Normalization & Validation**:
  - `app/Models/Contact.php::normalizeLeadData()` bổ sung nhãn `'home_contact_section' => 'Form báo giá trang chủ'`.
  - `app/Http/Controllers/Frontend/ContactController.php::store()` kiểm tra `name`, `phone` và (với `home_contact_section`) `email` hợp lệ; trả HTTP `422` JSON `{ success: false, message, errors }` cho AJAX/JSON request và `200` JSON `{ success: true, contact_id }` khi lưu thành công.
  - Frontend submit thật tới `/contacts` (VI) hoặc `/en/contacts` (EN) với `type: 'CONTACT_FORM'`, `form_source: 'home_contact_section'`, `lead_type: 'quote'`; tuyệt đối không giả lập thành công khi gặp lỗi mạng/422/500.

### 9.2. Thông số Layout & Visual chuẩn Figma (`595:15496`)
- **Canvas tối `#00284B` (`757px` Desktop, `overflow-x-clip`)** với 3 lớp họa tiết xích con lăn cắt mask (`contact-chain-deco.png` + `contact-chain-mask-{1,2}.svg`):
  1. Góc dưới-trái (`z-0`, nền trang trí mềm).
  2. Góc trên-phải (`z-0`, mờ phía sau thẻ form frosted glass).
  3. Góc dưới-phải (`z-20`, lớp nhấn tiền cảnh `pointer-events-none`).
- **Cột trái (`462px` Desktop)**:
  - Tiêu đề CMS `home_contact_title` (`28px Bold #FAFAFA uppercase`) + mô tả `home_contact_description` (`16px Medium #9AA4B2`).
  - Thông tin liên hệ sống từ `$page.props.global` gắn icon cục bộ: Hotline (`/assets/svg/icons/contact-phone.svg`), Email (`/assets/svg/icons/contact-mail.svg`), Trụ sở & VPGD/Showroom (`/assets/svg/icons/contact-pin.svg`), màu chữ `#EEF2F6` `14px Medium`.
- **Cột phải — Frosted Form Card**:
  - `backdrop-blur-[3px] bg-[rgba(201,224,255,0.8)] rounded-[8px] p-6 sm:p-8 lg:p-[40px]`.
  - 4 trường nhập liệu (`16px` font size chống auto-zoom trên iOS Safari, `rounded-[8px] border-[#D6D6D6] bg-white shadow-xs`): `Họ và tên*` (`name`), hàng 2 cột `Email*` (`email`) + `Số điện thoại*` (`phone`), và `Lời nhắn` (`note`, tùy chọn, `min-h-[146px]`).
  - Nút gửi căn trái (`self-start`, không full-width): `bg-[#005EA4] hover:bg-[#004c86] text-[#FAFAFA] font-medium text-[16px] px-[24px] py-[10px] rounded-[4px] min-h-[46px]` — `Nhận tư vấn ngay` / `Get Consultation Now` (hỗ trợ loading/disabled, inline error `role="alert"`, và banner phản hồi thành công/lỗi).
- **Hoàn thiện `Home/Index.vue` (đúng 7 Section Figma)**:
  - Render đúng thứ tự 7 section: `HeroSection` → `BrandStorySection` → `ProductsSection` → `ServicesSection` → `ProcessSection` → `BlogSection` → `ContactSection`.
  - Gỡ bỏ mount/import `AnnouncementTicker` và `FeaturedProductsCarousel` khỏi `Home/Index.vue`, nhưng **giữ nguyên các file cũ trên đĩa (không xóa)** theo Strict Safe Guard.

### 9.3. Danh mục Tài nguyên Stage 7 (6 Local Assets)
| Asset | Đường dẫn Public (Runtime) | Mục đích |
| :--- | :--- | :--- |
| Contact Chain Decoration | `/assets/images/homepage/contact-chain-deco.png` | Ảnh xích con lăn trang trí nền/tiền cảnh Section 7 |
| Contact Chain Mask 1 | `/assets/svg/homepage/contact-chain-mask-1.svg` | Mask SVG trang trí xích 1 |
| Contact Chain Mask 2 | `/assets/svg/homepage/contact-chain-mask-2.svg` | Mask SVG trang trí xích 2 |
| Contact Phone Icon | `/assets/svg/icons/contact-phone.svg` | Icon Hotline cột thông tin liên hệ |
| Contact Mail Icon | `/assets/svg/icons/contact-mail.svg` | Icon Email cột thông tin liên hệ |
| Contact Pin Icon | `/assets/svg/icons/contact-pin.svg` | Icon địa chỉ Trụ sở & VPGD/Showroom |
