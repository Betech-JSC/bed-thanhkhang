# Design: Design Tokens & Layout Shell Thành Khang (Official Home `979:32231` & Mobile `1190:6335`)

## 1. Đối chiếu Kiến trúc Hiện hữu từ Graphify AST (`graphify-out/graph.json`)

Kết quả quét `graphify . --code-only` (9.901 nodes, 16.034 edges, 896 communities) và khảo sát tầng Backend/Frontend hiện có sau Bước 1 (`thanhkhang-seeders-and-cleanup`):

| Thành phần hiện hữu | File trong Repo | Hiện trạng & Quyết định kiến trúc |
| :--- | :--- | :--- |
| **Root Layout mặc định** | `resources/Frontend/js/Layouts/MainLayout.vue` | Đang được `app.js` & `ssr.js` gán làm layout mặc định (`Header`, `<main><slot /></main>`, `Footer`, `NotificationPopup`, floating Zalo/Hotline). **Giữ nguyên file & tinh chỉnh đồng bộ** với chiều cao Header Desktop (`118px`) & Mobile (`92px`). |
| **Layout tương thích cũ** | `resources/Frontend/js/Layouts/Guest.vue` | Vẫn tồn tại trong `graph.json`. **Tuyệt đối giữ nguyên, không xóa**. |
| **Header Component** | `resources/Frontend/js/Components/Header.vue` | **Đã nâng cấp hoàn chỉnh ở Giai đoạn 2** theo bản Official `979:32411` + `979:32772` + Mega-menu 2 vùng 3 tầng `856:24212` + Mobile 2 tầng `1190:6336` + Slide-in Drawer (`isMounted` SSR-safe `<Teleport>`). |
| **Footer Component** | `resources/Frontend/js/Components/Footer.vue` | **Đã nâng cấp hoàn chỉnh ở Giai đoạn 2** theo bản Official Desktop `979:32406` / Master `217:3913` và Mobile `1193:6561` (Desktop Cột 1 `Danh mục sản phẩm`, dòng `Mã số thuế` kèm icon `building-07` `1110:37413` ở Cột 3 `Thông tin liên hệ`; Mobile có Brand Info `138×46` + lưới 2 cột `Danh mục sản phẩm \| Chính sách` `grid-cols-2 gap-4` + đường kẻ ngang `1px #E3E8EF` `1193:6585` + `Thông tin liên hệ` icon `18×18` + Bottom bar ngang `48px`). |
| **Tailwind & SCSS** | `resources/Frontend/tailwind.config.js`<br>`resources/Frontend/scss/app.scss`<br>`resources/Frontend/scss/customize/*.scss` | **Cập nhật ngay ở Giai đoạn 1**: đồng bộ chuẩn bảng màu `979:32231` (bao gồm `sub-hover: #007FD9` từ `689:19138`), thang chữ Mobile thực tế từ `1190:6335` (`28px / 20px / 18px / 16px`), Dual-Grid containers (`1344px` cho `1280px` inner và `1216px` cho `1152px` inner), giữ đầy đủ backward-compatible aliases. |
| **Blade Root Template** | `resources/Frontend/views/app.blade.php` | Đã nạp Google Fonts `Open Sans` & `Inter` hỗ trợ tiếng Việt (`400, 500, 600, 700`). |
| **CMS Shared Props** | `app/Http/Middleware/HandleInertiaFrontendRequests.php`<br>`app/Models/Config.php`<br>`database/seeders/ThanhKhangSettingSeeder.php` | **`HandleInertiaFrontendRequests.php:281-285` ĐÃ chia sẻ trực tiếp `$page.props.configs`** (gồm toàn bộ `$global` đã qua `Config::normalizeGeneralSettings()` + `menus` 5 mục chuẩn Figma có `TÀI LIỆU`/`DOCUMENTS` + `product_categories` cây 3 cấp từ `ProductCategory::transform()` + `policies` 6 chính sách B2B), song song với `$page.props.global`, `$page.props.product_categories` và `$page.props.data?.policies`. Frontend merge `{ ...($page.props?.global \|\| {}), ...($page.props?.configs \|\| {}) }` và ưu tiên đọc `configs.menus`, `configs.product_categories`, `configs.policies`. |

---

## 2. Bản đồ Node Figma Chính thức (`fileKey: Gu7RyTUDWaupomGrOH5EZO`)

### 2.1. Frames & Sections
- **Desktop Official Frame**: `979:32231` — *"Homepage Thành Khang (official)"* (`1440×6252`, `bg: #FAFAFA`, thuộc Section `595:14086` *"Feedback lần 3 ( 25/11/2026 )"*):
  - `979:32407` Hero + Header Wrapper (`1440×700`):
    - `979:32411` Header Desktop (`1440×118`: Top bar `979:32412` `1440×46` + Main bar `979:32421` `1440×72`).
    - `979:32772` Header Desktop Active State (`1440×118`, tab `SẢN PHẨM` `979:32787` active) + Mega-menu `856:24212` (`972×396`).
    - `979:32435` Hero Content (`1280×394`, `left: 80px`).
  - `979:32232` Body + Footer Wrapper (`1440×5552`, `gap: 80px`):
    - `979:32233` Danh mục sản phẩm (`1440×953`, inner `1152px`).
    - `979:32247` Về chúng tôi (`1440×667`, inner `1152px`).
    - `979:32265` Lý do khách hàng lựa chọn (`1440×659`, title inner `1152px`).
    - `979:32312` Quy trình cung ứng (`1440×954`, left `144px`).
    - `979:32354` Tin tức (`1440×611`, inner `1152px`).
    - `979:32367` Liên hệ & Báo giá (`1440×757`, `bg: #00284B`, inner `1153px`).
    - `979:32406` Footer (`1440×471`, instance của Master `217:3913`, inner `1280px`).
  - `919:26843` Floating Hotline/Zalo Widget (`80×185`: Zalo `919:26844`, Phone `919:26850`).
- **Mobile Official Frame**: `1190:6335` — *"Homepage Thành Khang (Mobile)"* (`390×7192`, `bg: #FAFAFA`, thuộc Section `1190:6315` *"Mobile Ver"*):
  - `1190:6336` `Header Mobile` (`390×92`: `Top Bar` `1190:6337` `390×31` + `Main Nav` `1190:6346` `390×61`). *(Figma không vẽ frame mở rời cho Mobile Drawer; sử dụng Slide-in Drawer chuẩn A11y trong `Header.vue` đồng bộ Design Tokens).*
  - `1190:6357` `Hero Banner` (`390×460`), `1190:6392` `Danh mục sản phẩm` (`390×722`), `1190:6454` `Về chúng tôi` (`390×1016`), `1191:6399` `Lý do khách hàng lựa chọn` (`390×640`), `1191:6449` `Quy trình` (`390×816`), `1191:6474` `Tin tức` (`390×1546`), `1193:6488` `Contact` (`390×1062`).
  - `1193:6621` `Footer / Mobile` (`390×838`, instance của Master `1193:6561`: `Footer Top` `1193:6562` `390×790` + `Footer Bottom` `1193:6613` `390×48`).
- **Design System Frames**: `42:4045` (`Typography | PC`), `25:1713` (`↳ Buttons` & Color/Shadow Swatches), `694:19912` (`Header menu` components).

### 2.2. Toàn bộ `nodeId` Logo & SVG Icons cho Header / Footer (định dạng `X:Y` sạch cho `download_figma_images`)

| Tài nguyên | Figma `nodeId` | Kích thước gốc | File đích cục bộ | Trạng thái trên ổ đĩa |
| :--- | :--- | :--- | :--- | :--- |
| Logo Thành Khang (`Artboard 5 1`) | `979:32422` (Header `120×40`) / `217:3857` (Footer `203×68`) / `1190:6347` (Mobile `96×32`) | PNG `1048×353` | `/assets/images/logo/logo-thanhkhang.png` | Đã có (295.786 B) |
| Header Top Bar Mail (`mail-01`) | `979:32416` (Desktop `18×18`) / `1190:6343` (Mobile `14×14`) | SVG `18×18` | `/assets/svg/icons/header-mail.svg` | Đã có (970 B) |
| Header Top Bar Phone (`phone`) | `979:32419` (Desktop `18×18`) / `1190:6339` (Mobile `14×14`) | SVG `18×18` | `/assets/svg/icons/header-phone.svg` | Đã có (1.662 B) |
| Header Nav Chevron Default | `979:29426` (`#697586`) | SVG `20×20` | `/assets/svg/icons/header-chevron-down.svg` | Đã tải & xác thực (215 B) |
| Header Nav Chevron Active | `979:29431` (`#004D86`) | SVG `20×20` | `/assets/svg/icons/header-chevron-down-active.svg` | Đã tải & xác thực (215 B) |
| Mega-menu Chevron Right | `689:18702` (`#00284B`) | SVG `20×20` | `/assets/svg/icons/header-chevron-right.svg` | Đã tải & xác thực (215 B) |
| Header Search (`search-sm`) | `979:32431` (Desktop) / `1190:6350` (Mobile) | SVG `20×20` | `/assets/svg/icons/header-search.svg` | Đã có & xác thực (510 B) |
| Mobile Hamburger (`menu-01`) | `1190:6355` (`#00284B`) | SVG `22×22` | `/assets/svg/icons/header-menu.svg` | Đã tải & xác thực (233 B) |
| Footer Phone (`phone-call-01`) | `217:3888` | SVG `24×24` | `/assets/svg/icons/footer-phone.svg` | Đã có & xác thực (1.917 B) |
| Footer Mail (`mail-01`) | `217:3891` | SVG `24×24` | `/assets/svg/icons/footer-mail.svg` | Đã có & xác thực (939 B) |
| **Footer Tax / Building (`building-07`)** | **`1110:37413`** | SVG `24×24` | `/assets/svg/icons/footer-building.svg` | **Đã tải & xác thực (724 B)** |
| Footer Pin (`marker-pin-01`) | `217:3894` (và `217:3897`) | SVG `24×24` | `/assets/svg/icons/footer-pin.svg` | Đã có & xác thực (525 B) |
| Footer Social Facebook | `353:9381` | SVG `20×20` | `/assets/svg/icons/footer-facebook.svg` | Đã có & xác thực (737 B) |
| Footer Social LinkedIn | `353:9382` | SVG `20×20` | `/assets/svg/icons/footer-linkedin.svg` | Đã có & xác thực (948 B) |

---

## 3. Bảng Design Tokens Chuẩn Figma Official (`979:32231` & `1190:6335`)

### 3.1. Bảng Màu (Color Palette)

| Nhóm Token | Key / Alias | Mã Hex / RGBA | Bằng chứng Node Figma |
| :--- | :--- | :--- | :--- |
| **Primary (`Main blue`)** | `primary.DEFAULT` / `500` | `#005EA4` | CTA `Nhận báo giá` (`979:32433`, `1190:6352`), Process Card 1 (`979:32323`), Button Default (`25:4183`) |
| | `primary.hover` / `600` | `#004D86` | Nav Active/Hover (`979:32787`), Process Card 2 (`979:32331`), Button Hover (`25:4186`) |
| | `primary.active` / `700` | `#003967` | Chữ trên CTA Secondary (`979:32434`), Process Card 3 (`979:32339`) |
| | `primary.info` / `400` / `sub-hover` | `#0075E7` / `#007FD9` | Nhãn thông tin / Mega-menu subcategory hover (`689:19138`) |
| | `primary.accent` | `#009FFF` | Điểm đầu gradient Hero Eyebrow (`#009FFF -> #C9E0FF`, `979:32440`) |
| **Secondary (`Main blue light`)** | `secondary.DEFAULT` / `100` | `#C9E0FF` | Nền CTA `Gọi hotline` (`979:32434`), Category Card (`75:3015`), Hero Subtext (`979:32442`), Copyright (`217:3900`) |
| | `secondary.hover` / `200` | `#81BDFF` | Secondary Button Hover (`30:4340`) |
| | `secondary.disabled` / `50` | `#EDF6FF` | Secondary Button Disabled bg (`30:4336`, text `#5BA4FB`) |
| **Dark** | `dark.DEFAULT` / `800` | `#00284B` | Header Top Bar (`979:32412`, `1190:6337`), Contact Section (`979:32368`), Process Card 4 (`979:32347`), Footer Bottom (`217:3899`), Headings (`184:1292`, `217:3868`) |
| | `dark.slate` / `700` | `#364152` | `Gray modern/700` (`42:3689`) |
| **Surface** | `surface.DEFAULT` | `#FAFAFA` | Nền Canvas Trang Chủ (`979:32231`, `1190:6335`), nền Footer Top (`979:32406`), Mega-menu phải (`856:24212`) |
| | `surface.white` | `#FFFFFF` | Nền Header Main Bar (`979:32421`, `1190:6346`), nền Cards (`754:22351`), Contact Inputs (`979:32726`) |
| | `surface.50` | `#F8FAFC` | `Gray modern/50` (`42:3682`), Mobile Process Steps bg (`1191:6453`) |
| | `surface.100` | `#EEF2F6` | `Gray modern/100` (`42:3683`), Mega-menu cột trái (`856:24213`), Mobile Hamburger bg (`1190:6354`) |
| | `surface.200` | `#E3E8EF` | `Gray modern/200` (`42:3684`), Contact Form Card (`979:32725`), Mega-menu hover |
| | `surface.300` | `#CDD5DF` | `Gray modern/300` (`42:3685`), Mega-menu active category (`856:24216`) |
| | `surface.gray-light` | `#F1F1F1` | Khung nền ảnh Blog/Category (`184:1338`) |
| | `surface.light` | `#FCFCFC` | Chữ sáng trên Top bar (`979:32413`) |
| **Neutral / Border / Semantic** | `ink.body` / `gray.600` | `#4B5565` | Chữ nội dung chính, mô tả Footer (`217:3859`) |
| | `ink.subtle` / `gray.500` | `#697586` | Chữ Nav mặc định (`979:32424`), mô tả phụ |
| | `ink.muted` / `gray.400` | `#9AA4B2` | Metadata / ngày đăng |
| | `border` / `gray.200` | `#E3E8EF` | Viền dưới Mobile Header (`1190:6346`), viền Card (`754:22351`) |
| | `field.border` / `gray.300` | `#D6D6D6` | Viền ô input form (`979:32726`) |
| | `error` / `warning` / `success` | `#F04438` / `#F79009` / `#12B76A` | Semantic states (`42:3730`–`42:3775`) |
| | `zalo` / `hotline` | `#0068FF` / `#008910` | Floating Hotline widget (`919:26844`, `919:26850`) |

### 3.2. Typography (`Open Sans` + `Inter`)

| Cấp độ | Desktop (`979:32231`) | Mobile (`1190:6335`) | Weight | Bằng chứng Node Figma |
| :--- | :--- | :--- | :--- | :--- |
| **H1** | `48px` / `67px` (`~1.4`) | `28px` / `36px` (`~1.286`) | `700` | Desktop Hero `979:32441`, Mobile Hero `1190:6365` |
| **H2** | `28px` / `45px` (`~1.6`) | `20px` / `28px` (`1.4`) | `700` | Desktop Process `979:32319` & Contact `979:32379`, Mobile Process `1191:6451` |
| **H3** | `24px` / `38px` (`~1.583`) | `18px` / `27px` (`1.5`) | `700` | Desktop About `979:32253` & Trust `979:32298`, Mobile About `1190:6458` |
| **H4** | `20px` / `32px` (`1.6`) | `16px` / `24px` (`1.5`) | `700` | Desktop Card `184:1292` & Footer Brand `353:9442`, Mobile Section/Card `1190:6395` / `1191:6392` |
| **Section Eyebrow** | `18px` / `29px` (`1.6`) | `16px` / `24px` (`1.5`) | `700` | Desktop `DANH MỤC SẢN PHẨM` `979:32237`, Mobile `1190:6395` |
| **Body XL** | `20px` / `32px` (`1.6`) | `18px` / `28px` (`1.55`) | `400–700` | Design System `42:4120` |
| **Body LG** | `18px` / `29px` (`1.6`) | `16px` / `26px` (`1.6`) | `400–600` | Hero subtext `979:32442` |
| **Body MD (Default)** | `16px` / `26px` (`1.6`) | `14px` / `22px` (`1.57`) | `400–600` | Desktop About/Blog `979:32254`, Mobile About `1190:6464` |
| **Body SM** | `14px` / `22px` (`1.57`) | `13px–14px` / `20px–22px` | `400–700` | Nav, Buttons, Footer links (`217:3870`), Mobile Cards (`1191:6398`) |
| **Body XS** | `12px` / `19px` (`1.58`) | `12px` / `18px` (`1.5`) | `400–500` | Footer Copyright (`217:3900`), Mobile Top Bar (`1190:6341`) |

### 3.3. Dual-Grid Container & Gutter
- **Mobile (`390px` / `375px`)**: Gutter bắt buộc `px-4` (`16px` mỗi bên -> `358px` trên khung `390px`, khớp `1190:6361`, `1190:6396`).
- **Desktop (`1440px`)**: Gutter bắt buộc `md:px-8` (`32px` mỗi bên = `64px` tổng):
  - **`.container` / `.app-container` / `.app-container-wide`**: `max-width: 1344px` (`1344 - 64 = 1280px` inner width) -> dùng cho **Hero Content (`979:32435`)** và **Footer (`979:32406`)**.
  - **`.app-container-narrow` / `.app-container-content`**: `max-width: 1216px` (`1216 - 64 = 1152px` inner width) -> dùng cho **Header Desktop (`979:32411`)** và **6 section nội dung Trang Chủ (`979:32233`–`979:32367`)**.

---

## 4. Blueprint Chi tiết Header & Footer cho Giai đoạn 2

### 4.1. Header (`resources/Frontend/js/Components/Header.vue`)
1. **Desktop (`lg+`, tổng cao `118px` — Figma `979:32411` & `979:32772`)**:
   - **Top Bar (`46px`, `bg-[#00284B]`, khung `.app-container-narrow` `1152px` inner)**:
     - Trái: Tên công ty (`14px Regular #FCFCFC` từ `global.general_business_name || global.company_name`).
     - Phải (`gap-6`): `header-mail.svg` (`18×18`) + `Email: {{ email }}` và `header-phone.svg` (`18×18`) + `Hotline: {{ phone }}`.
   - **Main Bar (`72px`, `bg-[#FFFFFF]`, khung `.app-container-narrow` `1152px` inner, sticky khi cuộn)**:
     - Trái: Logo `120×40` (`h-10 w-[120px] object-contain`).
     - Giữa: 5 mục điều hướng đọc trực tiếp từ `$page.props.configs?.menus` (`14px Bold` uppercase, mặc định `text-[#697586]`, hover/active `text-[#004D86]` kèm thanh gạch chân `3px bg-[#004D86]` sát đáy thanh `72px`):
       1. `VỀ CHÚNG TÔI` (`/gioi-thieu`)
       2. `SẢN PHẨM` (`/san-pham`, kèm chevron `20×20` mở **Mega-menu 2 vùng** `856:24212`: cột trái `340px bg-[#EEF2F6] p-5` chứa danh mục gốc Cấp 1 từ `configs.product_categories` `min-h-[46px]` `#00284B` bold + chevron-right, active `bg-[#CDD5DF]`, hover `bg-[#E3E8EF]`, đáy cột có nút `Xem tất cả sản phẩm` `h-[38px] bg-[#005EA4] text-[#FAFAFA] rounded-[4px]`; vùng phải `bg-[#FAFAFA] p-5` rộng động 1–2 cột hiển thị nhóm danh mục Cấp 2 `14px Bold #005EA4` hover `#007FD9` & danh mục Cấp 3 `14px Medium #697586`, chống tràn ngang ở mọi viewport `1024px–1440px`)
       3. `TÀI LIỆU` (từ `configs.menus[2]`: kết nối chính thức tới `/tai-lieu` & `/en/documents` qua route `documents.index` -> `Pages/Documents/Index.vue` dựng theo Figma `1006:2634`, `type: 'documents'`)
       4. `TIN TỨC` (`/tin-tuc`)
       5. `LIÊN HỆ` (`/lien-he`)
     - Phải: Nút Search (`20×20` `header-search.svg`) đặt bên trái, cách `32px` tới cụm 2 nút CTA (`gap-3`):
       - `Nhận báo giá`: `h-[38px] px-5 rounded-[4px] bg-[#005EA4] hover:bg-[#004D86] text-[#FAFAFA] text-[14px] font-medium`.
       - `Gọi hotline`: `h-[38px] px-5 rounded-[4px] bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] text-[14px] font-medium`.
2. **Mobile (`< lg`, tổng cao `92px` — Figma `1190:6336`)**:
   - **Mobile Top Bar (`31px`, `bg-[#00284B]`, `px-4`, flex justify-between items-center)**:
     - Trái: `header-phone.svg` (`14×14`) + `{{ phone }}` (`12px/17px #FCFCFC`).
     - Phải: `header-mail.svg` (`14×14`) + `{{ email }}` (`12px/17px #FCFCFC`, truncate an toàn).
   - **Mobile Main Nav (`61px`, `bg-[#FFFFFF] border-b border-[#E3E8EF]`, `px-4`, flex justify-between items-center)**:
     - Trái: Logo `96×32` (`h-8 w-24 object-contain`).
     - Phải (`gap-2`): Search `36×36 rounded-[4px] bg-white`, CTA `Nhận báo giá` `h-[33px] px-3 rounded-[4px] bg-[#005EA4] text-[#FAFAFA] text-[12px] leading-[17px] font-medium`, Hamburger `36×36 rounded-[4px] bg-[#EEF2F6]` chứa `header-menu.svg` (`22×22`).
   - **Mobile Slide-in Drawer**: Nền `#FFFFFF`, chứa Logo + nút Đóng, 5 mục menu từ `configs.menus` (kèm accordion 3 cấp `SẢN PHẨM`), 2 nút CTA full-width và thông tin liên hệ CMS; khóa cuộn `body` và đóng bằng `Esc`/backdrop.

### 4.2. Footer (`resources/Frontend/js/Components/Footer.vue` — Desktop `979:32406` / `217:3913` & Mobile `1193:6561`)
1. **Top Section (`bg-[#FAFAFA] pt-9 pb-8 lg:pt-[40px] lg:pb-[60px]`, khung `.app-container-wide` `1280px` inner)**:
   - Cột trái thương hiệu (`w-full lg:w-[360px] shrink-0`): Logo `138×46` (Mobile) / `203×68` (Desktop), Tên công ty uppercase (`14px/21px` Mobile, `20px/32px` Desktop Bold `#00284B`), Mô tả công ty (`13px/21px` Mobile, `14px/22px` Desktop `#4B5565`).
   - Cụm 3 cột phải (`w-full lg:max-w-[800px]`, Mobile `grid-cols-2 gap-x-4 gap-y-6`, Desktop `lg:grid-cols-3 lg:gap-8` ~`267px`/cột):
     - **Cột 1 (`col-span-1`) — `Danh mục sản phẩm`** (`font-inter`, `14px/20px` Mobile / `16px/22px` Desktop Bold `#00284B`): danh mục gốc từ `configs.product_categories` (`13px/20px` Mobile / `14px/20px` Desktop Medium `#4B5565`).
     - **Cột 2 (`col-span-1`) — `Chính sách`** (`font-inter`, `14px/20px` Mobile / `16px/22px` Desktop Bold `#00284B`): 6 chính sách từ `configs.policies` (`13px/20px` Mobile / `14px/20px` Desktop Medium `#4B5565`).
     - **Đường kẻ ngang Mobile (`1193:6585`)**: `col-span-2 h-px bg-[#E3E8EF] lg:hidden` ngăn cách 2 cột liên kết với `Thông tin liên hệ`.
     - **Cột 3 (`col-span-2 lg:col-span-1`) — `Thông tin liên hệ`** (`14px/20px` Mobile / `16px/22px` Desktop Bold `#00284B`, `space-y-3`, icon `18×18` Mobile / `24×24` Desktop):
       1. `footer-phone.svg` + `Hotline: {{ phone }}`
       2. `footer-mail.svg` + `Email: {{ email }}`
       3. `footer-building.svg` (node `1110:37413` / `1193:6600`) + `Mã số thuế: {{ taxCode }}`
       4. `footer-pin.svg` + `Trụ sở: {{ headquartersAddress }}`
       5. `footer-pin.svg` + `VPGD & Showroom: {{ showroomAddress }}`
2. **Bottom Bar (`bg-[#00284B] min-h-[48px] lg:min-h-[51px] py-3`, khung `.app-container-wide` `1280px` inner, `flex-row justify-between items-center`)**:
   - Trái: `© Copyright {{ currentYear }} ...` (`12px/18px` Mobile / `12px/19px` Desktop Regular `#C9E0FF`).
   - Phải (`gap-4 lg:gap-6`): `footer-facebook.svg` (`20×20`) & `footer-linkedin.svg` (`20×20`).
