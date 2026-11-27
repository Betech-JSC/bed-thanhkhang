# Design: Kiến trúc Kỹ thuật Đồng bộ Giao diện Mobile Figma `2:9` & Tối ưu Mobile UX/A11y

## Context

Sau bước kiểm toán `graphify-out/graph.json`, `graphify-out/GRAPH_REPORT.md` và mã nguồn Frontend thực tế cho thấy toàn bộ các trang và component mục tiêu đều đã tồn tại và gắn kết qua Inertia `MainLayout.vue` (`resources/Frontend/js/Layouts/MainLayout.vue`) cùng bảng màu/spacing trong `resources/Frontend/tailwind.config.js` và `resources/Frontend/scss/app.scss`:
- **Shell & Global Overlays** (Graphify hubs `Header.vue`, `Footer.vue`, `MainLayout.vue`): `resources/Frontend/views/app.blade.php`, `resources/Frontend/scss/app.scss`, `resources/Frontend/js/Layouts/MainLayout.vue`, `resources/Frontend/js/Components/{Header,Footer,FloatingCtaWidget,QuotePopupModal,SearchModal}.vue`.
- **Trang chủ 7 Section** (Graphify hub `Home/Index.vue` & các component con): `resources/Frontend/js/Pages/Home/Components/{HeroSection,ProductsSection,BrandStorySection,ServicesSection,ProcessSection,BlogSection,ContactSection}.vue`.
- **Các trang chức năng phụ trợ**: `resources/Frontend/js/Pages/Posts/Index.vue`, `resources/Frontend/js/Pages/Documents/Components/DocumentLibrarySection.vue`, `resources/Frontend/js/Pages/Process/Index.vue` (render `Pages/Process/Components/ProcessHero.vue` + `Components/CircularBadge.vue`), và `resources/Frontend/js/Pages/Product/Components/ProductDetailTabs.vue` (đối chiếu mẫu `.article-table-wrap` từ `Pages/Posts/Show.vue`).

Do `graphify-out/graph.json` xác nhận đầy đủ hệ thống component dùng chung và tài nguyên tĩnh sẵn có trong `public/assets/{images,svg}/homepage/`, thiết kế này **không tạo thêm file Vue component, helper JS hay CSS mới**, chỉ cập nhật có kiểm soát tại các file hiện có và giữ nguyên 100% bố cục Desktop `1440px`, `ProductGallery.vue` và hợp đồng dữ liệu CMS Backend/Database.

## Goals / Non-Goals

**Goals:**
- Đồng bộ chuẩn xác 100% thiết kế Figma Mobile `2:9` (`fileKey: 9EO7DXSgsQ412ohf1Qxt4c`: `1314:51987`, `1329:52835`, `1329:53125`) cho Trang chủ, Header/Footer và QuotePopupModal trên viewport Mobile (`390×844` / `375×812`).
- Khắc phục triệt để các lỗi Mobile UX & Accessibility: viewport zoom, chống iOS auto-zoom, safe-area iOS, vùng chạm `>= 44×44px`, chống chồng lấn CTA/CircularBadge trên `/quy-trinh`, và cuộn ngang cục bộ cho bảng HTML rộng trong PDP.
- Bảo toàn tuyệt đối giao diện Desktop (`>= 1024px` / `1440×900`), không xóa file, không sửa DB/Backend API và không phá vỡ chức năng/i18n VI–EN.

**Non-Goals:**
- **KHÔNG** thêm bộ chuyển đổi ngôn ngữ `VI / EN` vào `Header.vue` hay Mobile Drawer (theo quyết định đã chốt của người dùng).
- **KHÔNG** sửa đổi `ProductGallery.vue` (giữ nguyên 100% thanh thumbnail preview ngang bên dưới), không chỉnh sửa Backend PHP controllers/models/migrations hay database seeders.
- **KHÔNG** tạo mới file component phụ hoặc cài đặt thư viện npm bên ngoài.

## Decisions

### Decision 1: Dựng Timeline Metaball Sóng Dọc Mobile Trong `ServicesSection.vue` Bằng Inline SVG + Flex Row
- **Hiện trạng (`ServicesSection.vue` L8–15, L161–202)**: Tiêu đề section dùng `items-center`; mobile/tablet (`< lg`) dùng lưới hộp chữ nhật có viền `rounded-[10px] bg-[#00284B]/[0.68] border border-[#C9E0FF]/[0.28]`.
- **Quyết định**:
  - Sửa cụm tiêu đề `L9` từ `items-center` sang `items-start` (kèm `mt-[7px]` trên ô vuông xanh `10×10px` / `lg:mt-[9px]` cho `12×12px` để căn thẳng hàng chữ đầu).
  - Khi `useWaveLayout` đúng (`trustItems.length <= 5`), tách riêng khối Mobile/Tablet (`lg:hidden`) khỏi lưới Desktop fallback (`trustItems.length > 5`):
    - Khung banner trên Mobile đặt `min-h-[611px] lg:h-[578px]`.
    - Bên trái hiển thị cột timeline cố định `w-[70px] shrink-0` gồm 5 nút tròn (vòng ngoài `w-[70px] h-[70px]` nền `#C9E0FF`, lõi `w-[56px] h-[56px]` hoặc `w-[54px] h-[54px]` nền `#00284B` chứa icon) nối liên tiếp bằng 4 đoạn cầu nối metaball dọc `#C9E0FF` dựng bằng inline `<svg>` (xoay dọc tương ứng cầu nối ngang `trust-bridge-*.svg` với đường cong lõm mượt mà nối mép vòng tròn trên–dưới).
    - Bên phải từng nút là khối text không viền, không nền hộp (`min-w-0 flex-1`, tiêu đề `text-[16px] font-bold leading-[1.4] text-[#FAFAFA]`, mô tả `text-[13px] font-normal leading-[20px] text-[#E3E8EF]`).
  - Khi `trustItems.length > 5` (trường hợp CMS nhập quá 5 mục), giữ fallback lưới responsive hiện có để không bị tràn chiều cao.
- **Lý do & Phương án thay thế**: Inline SVG cầu nối dọc dùng chung kích thước cố định (`width`/`height` theo tỷ lệ cột 70px) cho phép vòng tròn và cầu nối khít 100% không bị lệch khi nội dung chữ bên phải dài hơn, đồng thời không sinh thêm request ảnh/file mới.

### Decision 2: Chuẩn hóa `ProductsSection.vue` & `BrandStorySection.vue` Cho Mobile `2:9`
- **`ProductsSection.vue` (`L32–172`)**:
  - Giữ layout `grid-cols-2 lg:grid-cols-3 gap-3 lg:gap-[24px]`.
  - Ô 1–5: thumbnail đổi thành `aspect-[173/130] lg:aspect-auto lg:h-[308px]`; chân thẻ `px-3 py-2 min-h-[64px] lg:min-h-0 lg:px-[16px] lg:py-0 lg:h-[64px]`; ẩn hộp mũi tên `>` bằng `hidden lg:flex` (trên `< lg`, tiêu đề chiếm trọn bề ngang sạch sẽ khớp Figma `1314:52028`).
  - Ô thứ 6 ("Khám phá toàn bộ sản phẩm"): nền `bg-[#005EA4] lg:bg-[#C9E0FF] lg:hover:bg-[#005EA4]`, chữ `text-[#FAFAFA] lg:text-[#364152] lg:group-hover:text-[#FAFAFA]`, thumbnail `aspect-[173/128] lg:aspect-auto lg:h-[308px]`, chân thẻ `min-h-[66px] lg:min-h-0 lg:h-[64px]`, hộp mũi tên `hidden lg:flex`.
- **`BrandStorySection.vue` (`L28–117`)**:
  - Bọc `wideImage` (bản mobile `lg:hidden`) và `topImages` trong cùng khối thư viện ảnh bên mobile bằng `flex flex-col gap-3`: đặt `wideImage` (`aspect-[358/210]`) lên **TRƯỚC** khối `grid-cols-2 gap-3` của `topImages` (`aspect-[173/148]`). Nhờ `wideImage` dưới Row 2 vẫn giữ `hidden lg:block`, thứ tự Desktop 1440px không đổi một pixel nào!
  - Nâng cỡ chữ đoạn văn narrative từ `text-[14px] lg:text-[16px]` thành `text-[16px] leading-[1.6]` và nút CTA từ `text-[14px] lg:text-[18px]` thành `text-[18px]`.

### Decision 3: Chuẩn hóa `ProcessSection.vue`, `BlogSection.vue`, `HeroSection.vue`, `ContactSection.vue` & `Footer.vue`
- **`HeroSection.vue`**:
  - Scrim mobile (`L66`): đổi thành `bg-[linear-gradient(90deg,#01101E_0%,rgba(1,16,30,0)_100%)] md:bg-[linear-gradient(to_right,rgba(1,16,30,0.6)_0%,rgba(1,16,30,0)_100%)]`.
  - Eyebrow (`L84`): `text-[18px] md:text-[24px] lg:text-[28px]`.
  - Description (`L101`): bỏ `line-clamp-4 md:line-clamp-none`.
  - CTA (`L115, 123, 132, 140`): `text-[18px]` đồng nhất mobile & desktop.
- **`ProcessSection.vue`**:
  - Header banner mobile (`L46`): thay `aspect-[739/954] max-h-[480px] ... justify-center` thành `min-h-[500px] aspect-[390/500] sm:aspect-auto sm:min-h-[420px] lg:min-h-0 lg:aspect-auto justify-start pt-8 lg:justify-center lg:py-[80px]` để đạt đúng chiều cao `500px` (`1314:52099`) và đẩy text lên đỉnh, chừa không gian hiển thị hình ảnh bánh răng phía dưới.
  - Bỏ `line-clamp-4` (`L81`) và `line-clamp-3` (`L116`).
  - Khối danh sách bước nền `#F8FAFC` (`L90`): thêm `relative overflow-hidden` trên wrapper này và chèn lớp nền sóng trang trí nhẹ `<img src="/assets/images/homepage/process-wave-bg.png" ...>` (kết hợp `process-wave-mask.svg` hoặc opacity dịu nhẹ `lg:hidden pointer-events-none select-none absolute inset-0`) đặt `z-0` dưới `<ol class="relative z-10 ...">`.
  - Card bước (`L100`): dùng `rounded-[8px] lg:rounded-[12px]`.
- **`BlogSection.vue`**:
  - Container (`L7`): `gap-6` (thay `gap-5 lg:gap-6`).
  - CTA (`L93`): `text-[18px]`.
- **`ContactSection.vue`**:
  - Xóa khối ảnh `contact-chain-deco.png` `md:hidden` (`L7–18`), giữ 3 khối trang trí từ `md:`/`lg:` trở lên (`hidden md:block`, `hidden lg:block`).
  - Grid cha (`L64`): `gap-6 lg:gap-[63px]`.
  - Nút submit (`L377–384`): chữ `text-[18px]` và nhãn `t('Nhận tư vấn ngay')` (cùng cặp dịch tiếng Anh `"Get Consultation Now"` đã có sẵn tại `resources/Frontend/lang/en.json:66` và `resources/lang/en/ui.php:62` — đồng thời kiểm tra/cập nhật selector test E2E nếu cần).
- **`Footer.vue`**:
  - Cụm 2 nút CTA (`L40`): thêm `hidden lg:flex`.
  - Lưới phải (`L59`): đổi từ `grid-cols-2` sang `grid-cols-1 lg:grid-cols-3 xl:grid-cols-[212px_212px_268px] gap-[20px] lg:gap-6 xl:gap-x-[20px]`.
  - Ẩn/gỡ thanh phân cách `h-px bg-[#E3E8EF] lg:hidden` (`L101–105`) và đổi Column 3 (`L108`) từ `col-span-2 lg:col-span-1` sang `col-span-1` để phù hợp lưới 1 cột.

### Decision 4: Tinh Chỉnh Header 33px/94px, Safe-Area, Viewport Meta, Overlays & iOS Zoom Prevention
- **Header Mobile `94px` (`Header.vue`, `app.scss`, `MainLayout.vue`)**:
  - `Header.vue` (`L50`): đổi `h-[31px]` thành `h-[33px]`; `handleScroll` (`L1191`) cập nhật ngưỡng mobile `31 -> 33`; biến CSS (`L1239`) `--header-total-sm: 94px`.
  - `app.scss` (`L106, 108`): `--header-topbar-height-mobile: 33px`, `--header-height-mobile: 94px`.
  - `MainLayout.vue` (`L4`): `--header-height-mobile: 94px`.
  - Drawer (`Header.vue` `L624`): đổi `p-5 pt-4` thành `p-5 pt-4 pb-[calc(1.25rem+env(safe-area-inset-bottom))]` (hoặc trên `<aside>`), bảo đảm KHÔNG thêm `VI / EN` switcher.
- **`app.blade.php` (`L14`)**:
  - Thay `<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">` thành `<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">`.
- **`FloatingCtaWidget.vue`**:
  - Container (`L5`): `right-3 lg:right-6 bottom-[calc(16px+env(safe-area-inset-bottom))] lg:bottom-[72px]`.
  - 3 nút tròn (`L22, 51, 90`): `w-11 h-11 lg:w-[60px] lg:h-[60px]` (`44×44px` trên Mobile, `60×60px` trên Desktop), giữ nguyên kích thước SVG bên trong hoặc cân chỉnh tỷ lệ hài hòa (`w-[28px] h-[14px] lg:w-[40px] lg:h-[18px]` cho Zalo, `w-5 h-5 lg:w-6 lg:h-6` cho ScrollTop, `w-5 h-5 lg:w-[28px] lg:h-[28px]` cho Phone).
- **`QuotePopupModal.vue`**:
  - Wrapper thẻ popup (`L24`): `max-h-[calc(100dvh-32px)] lg:max-h-[calc(100dvh-112px)]`.
  - Ẩn chain texture ở `< 1024px`: đặt `.quote-chain-wrap { display: none; }` ở base (kể cả `--top-right` và `--bottom-left`), chỉ bật `display: flex;` bên trong `@media (min-width: 1024px)`.
  - Thanh topbar đóng riêng cho Mobile (`< lg`): chuyển nút đóng `X` từ `lg:hidden absolute top-3.5 right-3.5` ra khỏi vùng cuộn thành một thanh `lg:hidden shrink-0 flex items-center justify-end px-3 pt-2.5 pb-1 bg-dark z-20` nằm **bên trên** khối thân cuộn `overflow-y-auto overscroll-contain` (`pt-1 px-5 pb-5 ... lg:p-8`), kích thước nút `X` là `w-11 h-11` (`44×44px`). Như vậy khi cuộn form, văn bản cuộn bên trong vùng thân mà không bao giờ trôi ngang phía sau nút `X`.
  - Kiểm tra đồng bộ tiêu đề fallback `displayTitle` (`L423`): giữ ưu tiên `props.quoteContent?.title || getLocalizedConfig('quote_popup_title')`, cập nhật chuỗi fallback khớp Figma `1329:52835` (`"BÁO GIÁ CÁC SẢN PHẨM TẠI THÀNH KHANG"`) và đảm bảo có khóa dịch tương ứng trong `resources/Frontend/lang/en.json` & `resources/lang/en/ui.php`.
- **Chống iOS Safari Auto-Zoom**:
  - `Posts/Index.vue` (`L195`) và `DocumentLibrarySection.vue` (`L92`): đổi cỡ chữ `<input>` tìm kiếm thành `text-base lg:text-[14px]` (`16px` trên mobile/tablet `< lg`, `14px` trên desktop `lg+`).

### Decision 5: Chiến Lược Mở Rộng Touch Target >= 44×44px Không Làm Vỡ Thẩm Mỹ
Để đạt `width >= 44px` và `height >= 44px` mà vẫn giữ hình khối thị giác thanh gọn chuẩn Figma:
1. **`Header.vue` (Mobile Topbar/Main Nav & Drawer)**:
   - Nút Search (`L349`) và Hamburger (`L367`): dùng nút `w-11 h-11` (`44×44px`) kèm bù lề nhẹ `-mr-1.5` ở Hamburger để giữ logo/navbar cân đối (hoặc bọc hộp thị giác `36×36px` bên trong nút `44×44px`).
   - Drawer accordion buttons: nâng `w-10 h-10` (`L509`) và `w-9 h-9` (`L548`) lên `w-11 h-11` (`44×44px`); hàng con `min-h-[40px]` (`L536`) và `min-h-[38px]` (`L577`) lên `min-h-[44px]`.
2. **`HeroSection.vue`**:
   - Nút dot tabs (`L169–184`): đổi button thành `min-w-[44px] min-h-[44px] inline-flex items-center justify-center -mx-2` (hoặc `w-11 h-11` trên mobile, `md:w-[10px] md:h-[10px] md:min-w-0 md:min-h-0 md:mx-0`) chứa `<span>` vuông `10×10px` ở tâm! Cách này vừa giữ các ô vuông `10×10px` giống hệt Figma `1314:52018` vừa đạt vùng chạm `44×44px` trên Mobile!
   - Hai nút Prev/Next (`L189, 204`): đổi thành `w-11 h-11 lg:w-10 lg:h-10` (`44×44px` trên Mobile, `40×40px` trên Desktop).
3. **`QuotePopupModal.vue`, `SearchModal.vue`, `DocumentLibrarySection.vue`**:
   - `QuotePopupModal.vue` close `X`: `w-11 h-11`.
   - `SearchModal.vue` clear `X` (`L42`): `w-11 h-11 -mr-2` (`44×44px` hit target, icon tròn hover bên trong hoặc bo tròn).
   - `DocumentLibrarySection.vue` clear `X` (`L100`): `w-11 h-11 -mr-2` (`44×44px` hit target, giữ form tìm kiếm `h-[42px]` gọn mắt).
4. **`Footer.vue`**:
   - Hai thẻ `<a>` social Facebook & LinkedIn (`L191, 209`): đổi `w-5 h-5` thành `min-w-[44px] min-h-[44px] -my-3 lg:min-w-0 lg:min-h-0 lg:my-0 lg:w-5 lg:h-5` trên Mobile (`gap-1 lg:gap-[24px]` để tránh 2 vùng chạm `44px` đè nhau và vẫn cân đối mép thanh đáy `48px`).

### Decision 6: Sửa Lỗi Mobile Trên `ProcessHero.vue` (`/quy-trinh`) & `ProductDetailTabs.vue`
- **`Pages/Process/Components/ProcessHero.vue` (render bởi `Pages/Process/Index.vue`)**:
  - Chuyển nút Mobile CTA (`Khám phá 5 bước`) và `<CircularBadge />` ở màn hình `< sm` (`390×844`) vào chung một hàng đáy linh hoạt (`flex flex-wrap items-end justify-between gap-4 sm:block`) nằm bên dưới khối văn bản, tách hoàn toàn khỏi vùng tuyệt đối `sm:absolute`; đặt `min-h-[44px]` cho CTA button và thêm `sm:pr-[132px] lg:pr-0` lên khối văn bản (`L21`) để ở kích thước `640px–1023px` cũng không bao giờ chạm `CircularBadge` đặt `sm:absolute`.
- **`Pages/Product/Components/ProductDetailTabs.vue`**:
  - Thêm `ref="pdpRichtextRef"` vào khối `.pdp-richtext` (`L59`) và hàm `wrapPdpTables()` (chạy trong `onMounted` + `watch(sanitizedContent)` sau `nextTick()`, hoàn toàn tương đồng với `Posts/Show.vue` L593–612) để bọc từng `<table>` bằng `<div class="pdp-table-wrap">`.
  - Gỡ `overflow-x: auto;` khỏi `.pdp-richtext` (`L967`), chuyển `overflow-x: auto; -webkit-overflow-scrolling: touch;` vào `.pdp-richtext :deep(.pdp-table-wrap)` với `margin: 1rem 0; width: 100%; border: 1px solid #e3e8ef; border-radius: 4px;` và `margin: 0` trên `table`.

## Risks / Trade-offs

- **[Rủi ro] Tăng hit-area `44×44px` trên nút dot Hero hoặc icon social Footer có thể làm dịch vị trí thị giác hoặc đè vùng click lân cận** → **Mitigation**: Dùng kỹ thuật vùng bấm bao ngoài (`w-11 h-11 flex items-center justify-center`) kết hợp khoảng cách phù hợp / negative margin đối xứng ở cạnh ngoài, kiểm tra lại bounding box không chồng lấn bằng Playwright `boundingBox()`.
- **[Rủi ro] Đổi nhãn nút `ContactSection.vue` từ `"Liên hệ ngay"` sang `"Nhận tư vấn ngay"` có thể làm test E2E cũ tìm chuỗi cũ bị fail** → **Mitigation**: Rà soát các file test trong `tests/e2e/` ở giai đoạn `/opsx-apply`, cập nhật test mới cho Mobile `2:9` (`tests/e2e/mobile-figma-2-9.spec.ts`) và kiểm tra tương thích với `en.json` / `ui.php`.
- **[Rủi ro] Thay đổi `QuotePopupModal` topbar đóng hoặc `ProcessHero` mobile padding có thể ảnh hưởng ở breakpoint trung gian `640px`–`1023px`** → **Mitigation**: Viết kiểm thử Playwright tại đủ các mốc `375×812`, `390×844`, `768×1024`, `1024×768` và `1440×900` để xác nhận không chồng lấn và `scrollWidth <= innerWidth`.
- **[Rủi ro] Thay đổi fallback `QuotePopupModal` title** → **Mitigation**: Giữ nguyên ưu tiên prop/CMS (`quote_popup_title`), chỉ cập nhật fallback mặc định khi CMS chưa cấu hình và đảm bảo cả hai chuỗi đều có trong từ điển VI/EN.

## Migration Plan

1. Thực thi tuần tự theo nhóm module ở bước `/opsx-apply`: Shell/Viewport/Header/Overlays -> Trang chủ 7 Sections + Footer -> Inner pages (`Posts/Index`, `DocumentLibrarySection`, `ProcessHero`, `ProductDetailTabs`).
2. Chạy `yarn frontend:build` (CSR + SSR) kiểm tra 0 lỗi biên dịch.
3. Chạy `graphify . --code-only` nếu cấu trúc AST thay đổi.
4. Chạy Playwright E2E xác thực `390×844`, `375×812` và hồi quy Desktop `1440×900` kèm ảnh chụp màn hình bằng chứng.
5. Rollback an toàn qua `git checkout` từng file Vue/SCSS/Blade nếu cần, không ảnh hưởng DB.
