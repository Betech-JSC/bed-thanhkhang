# Design: Design Tokens & Layout Shell Thành Khang (Home-First Core)

## 1. Đối chiếu Kiến trúc Hiện hữu từ Graphify AST (`graphify-out/graph.json`)

Kết quả tra cứu đồ thị AST (8.568 nodes) và cây thư mục dự án (`resources/Frontend/`, `app/Http/Middleware/HandleInertiaFrontendRequests.php`):

| Thành phần hiện hữu | File trong Repo | Hiện trạng & Quyết định kiến trúc |
| :--- | :--- | :--- |
| **Root Layout mặc định** | `resources/Frontend/js/Layouts/MainLayout.vue` | Đang được `app.js` & `ssr.js` gán làm layout mặc định (render `Header`, `<main><slot /></main>`, `Footer`, `NotificationPopup`). **Tái sử dụng & nâng cấp trực tiếp**, đổi nền từ `bg-dark-bg` sang surface sáng `#FAFAFA` (`bg-surface`) và cập nhật CSS variables chiều cao Header. |
| **Layout tương thích cũ** | `resources/Frontend/js/Layouts/Guest.vue` | Vẫn có node tham chiếu trong `graph.json`. **Giữ nguyên, không xóa** để tránh gãy các trang chưa chuyển đổi. |
| **Header Component** | `resources/Frontend/js/Components/Header.vue` | Đã tồn tại trong `graph.json` (tái cấu trúc trực tiếp theo Figma `595:15540`, giữ sạch các route hiện có, bỏ tàn dư theme cũ). |
| **Footer Component** | `resources/Frontend/js/Components/Footer.vue` | Đã tồn tại trong `graph.json` (tái cấu trúc trực tiếp theo Figma `595:15535`). |
| **Tailwind & SCSS** | `resources/Frontend/tailwind.config.js`<br>`resources/Frontend/scss/app.scss`<br>`resources/Frontend/scss/customize/type.scss` | Chứa các alias cũ (`primary`, `secondary`, `dark-bg`, `figma-*`, `.app-container`, `.display-*`). **Mở rộng và cập nhật chuẩn Figma**, giữ backward-compatible aliases để không làm vỡ các trang con. |
| **Blade Root Template** | `resources/Frontend/views/app.blade.php` | Đang nạp Google Fonts `Be Vietnam Pro` & `Inter`. **Bổ sung nạp `Open Sans:wght@400;500;600;700`** (hỗ trợ tiếng Việt). |
| **CMS Shared Props** | `HandleInertiaFrontendRequests.php` & `app/Models/Config.php` | Backend chia sẻ `$page.props.global` (normalized bởi `Config::normalizeGeneralSettings`), `$page.props.product_categories`, `$page.props.data.policies`, `$page.props.locale`, `$page.props.route`. Frontend đọc an toàn theo chuỗi ưu tiên `$page.props.configs || $page.props.global`. |

> Không tạo mới file Layout hay Header/Footer trùng lặp (`HeaderNew.vue`, `FooterV2.vue`...); nâng cấp trực tiếp các file cốt lõi đã có theo nguyên tắc **Graphify AST First**.

---

## 2. Nguồn thiết kế Figma & Chiến lược Mobile

- **Figma File Key**: `Gu7RyTUDWaupomGrOH5EZO`
- **Frame Trang Chủ**: `595:15360` (*"Homepage Thành Khang option 6"*, kích thước `1440×6478`)
- **Node Header**: `595:15540` (`1440×118`)
- **Node Footer**: `595:15535` (`1440×443`)
- **Chiến lược Mobile (`375×812`)**: File Figma trang `Final` hiện chưa có frame riêng cho `375px`; do đó giao diện Mobile được dẫn xuất chuẩn hóa từ bộ Design Tokens Desktop kết hợp padding chuẩn `px-4`, typography scale Mobile, và Mobile Slide-in Drawer cho điều hướng Header.

---

## 3. Bảng Design Tokens Chuẩn Figma

### 3.1. Bảng Màu (Color Palette)

| Nhóm Token | Key / Alias | Mã Hex / RGBA | Phạm vi sử dụng theo Figma |
| :--- | :--- | :--- | :--- |
| **Primary (`Main blue`)** | `primary` / `primary-500` | `#005EA4` | Nút CTA chính (`Nhận báo giá`), liên kết nổi bật, trạng thái active |
| | `primary-hover` / `primary-600` | `#004D86` | Trạng thái hover của CTA chính |
| | `primary-active` / `primary-700` | `#003967` | Trạng thái active/pressed, chữ trên nút Secondary (`Gọi hotline`) |
| | `info` | `#0075E7` | Accent nhấn thông tin / liên kết phụ |
| **Secondary (`Main blue light`)** | `secondary` / `secondary-100` | `#C9E0FF` | Nền nút CTA phụ (`Gọi hotline`), chữ copyright trên nền `#00284B` |
| | `secondary-hover` / `secondary-200` | `#81BDFF` | Trạng thái hover của CTA phụ / nhấn sáng trên nền tối |
| **Dark** | `dark` / `dark-800` | `#00284B` | Header Top bar, Footer Bottom bar, màu chữ Heading & Navigation chính |
| | `slate-700` / `dark-700` | `#364152` | Chữ phụ đậm / nhãn kỹ thuật |
| **Surface** | `surface` (`Neutral/White`) | `#FAFAFA` | Nền chính trang, nền Header Main bar, nền Footer Top |
| | `white` | `#FFFFFF` | Nền Card, Dropdown, Mobile Drawer |
| | `surface-50` | `#F8FAFC` | Nền khu vực xen kẽ nhẹ |
| | `surface-100` | `#EEF2F6` | Nền chip / input / hover nhẹ |
| | `gray-light` | `#F1F1F1` | Nền phân vùng nhẹ |
| **Neutral / Text / Border** | `gray-600` | `#4B5565` | Màu chữ Body chính, mô tả công ty |
| | `gray-500` | `#697586` | Màu chữ phụ / metadata |
| | `gray-400` | `#9AA4B2` | Màu chữ muted / placeholder |
| | `gray-200` | `#E3E8EF` | Đường viền (border) chuẩn, divider |
| | `gray-300` | `#D6D6D6` | Đường viền input / khung kỹ thuật |
| | `btn-slide` | `rgba(57, 76, 107, 0.8)` | Nút điều hướng slider |
| **Shadow** | `shadow-xs` | `0 1px 2px 0 rgba(16, 24, 40, 0.05)` | Đổ bóng nhẹ cho nút, dropdown, sticky header |

### 3.2. Typography (`Open Sans`)

- **Font chính**: `'Open Sans', 'Inter', system-ui, -apple-system, sans-serif` (weights: `400, 500, 600, 700`).
- **Kế thừa bắt buộc**: Trong `@layer base` của `resources/Frontend/scss/app.scss`, áp dụng `font-family` cho `html, body, button, input, select, textarea` để ngăn fallback font hệ thống hoặc ghi đè từ PrimeVue.

| Cấp độ | Nguồn Figma Style | Desktop (`>= md/lg`) | Mobile (`< md`) | Font Weight |
| :--- | :--- | :--- | :--- | :--- |
| **H1** | `PC/Heading/H2/bold` | `48px` / `1.4` | `32px` / `1.3` | `700` (Bold) |
| **H2** | `PC/Heading/H5/bold` | `28px` / `1.5` | `24px` / `1.4` | `700` (Bold) |
| **H3** | `PC/Heading/H6/bold` | `24px` / `1.6` | `20px` / `1.5` | `700` (Bold) |
| **H4** | `PC/Body/XL/bold` | `20px` / `1.6` | `18px` / `1.5` | `700` (Bold) |
| **Body XL** | `PC/Body/XL` | `20px` / `1.6` | `18px` / `1.6` | `400 / 500 / 700` |
| **Body LG** | `PC/Body/LG` | `18px` / `1.6` | `16px` / `1.6` | `400 / 500 / 600` |
| **Body MD (Default)** | `PC/Body/MD` | `16px` / `1.6` | `15px–16px` / `1.6` | `400 / 500 / 600` |
| **Body SM** | `PC/Body/SM` | `14px` / `1.6` | `14px` / `1.6` | `400 / 500 / 700` |
| **Body XS** | `PC/Body/XS` | `12px` / `1.6` | `12px` / `1.6` | `400 / 500` |

### 3.3. Layout Metrics, Container & Gutters

- **Tailwind `container`**: `center: true`, `max-width: 1280px` (khớp khung Hero/Footer `1280px`, `px-[80px]` trên 1440px), padding `DEFAULT: '1rem'` (`px-4` mobile) và `md/lg: '2rem'` (`px-8` desktop).
- **Content Inner Width (`1152px`)**: Hỗ trợ utility/wrapper `max-w-[1152px]` (khớp khung Header/About/Categories/Blog/Contact `px-[144px]` trên 1440px), đồng thời bảo toàn `.app-container` cho các trang cũ.
- **Border Radius**: Nút CTA chính/phụ chuẩn `rounded-[4px]`; dropdown/card `rounded-lg` (`8px`).

---

## 4. Blueprint Chi tiết Header & Footer

### 4.1. Header (`resources/Frontend/js/Components/Header.vue` — Figma `595:15540`)

1. **Top Bar (`46px`, ẩn trên `< lg`)**:
   - Nền `#00284B`, chữ `#FCFCFC`, `14px Regular`.
   - Khung nội dung căn giữa (`max-w-[1152px]` / `container px-4 lg:px-8`).
   - **Trái**: Tên công ty (`global.general_business_name || global.company_name`).
   - **Phải**: Flex ngang gồm:
     - Icon `/assets/svg/icons/header-mail.svg` (`18×18`) + `Email: {{ email }}` (`mailto:`).
     - Icon `/assets/svg/icons/header-phone.svg` (`18×18`) + `Hotline: {{ phone }}` (`tel:`).
2. **Main Bar (`72px` Desktop / `64px` Mobile, `bg-[#FAFAFA]`, sticky khi cuộn)**:
   - Khi cuộn (`scrollY > 46`), thêm `border-b border-[#E3E8EF] shadow-xs` để tách biệt rõ với nội dung trang.
   - **Trái**: Logo `<Link href="/">` với `<img :src="logoUrl" alt="..." class="h-9 lg:h-10 w-auto object-contain" />` (Desktop `120×40`), ưu tiên `$page.props.configs?.logo || $page.props.global?.general_logo || '/assets/images/logo/logo-thanhkhang.png'`.
   - **Giữa (Desktop `lg+`)**: 5 mục điều hướng `gap-8`, `14px Bold`, `text-[#00284B]`, hover `text-[#005EA4]`:
     - `VỀ CHÚNG TÔI`
     - `SẢN PHẨM` + icon `/assets/svg/icons/header-chevron-down.svg` (`24×24`) mở dropdown danh mục đa cấp từ `$page.props.product_categories` (hỗ trợ hover + click + đóng khi click outside / `Esc`).
     - `TÀI LIỆU`
     - `TIN TỨC`
     - `LIÊN HỆ`
   - **Phải (Desktop `lg+`, `gap-8`)**:
     - Nút mở hộp tìm kiếm với icon `/assets/svg/icons/header-search.svg` (`20×20`, `aria-label="Tìm kiếm"`).
     - Nhóm 2 CTA (`gap-3`):
       - `Nhận báo giá`: `bg-[#005EA4] hover:bg-[#004D86] active:bg-[#003967] text-[#FAFAFA] rounded-[4px] px-5 py-2 text-sm font-medium` (điều hướng tới trang Liên hệ / mở form báo giá).
       - `Gọi hotline`: `bg-[#C9E0FF] hover:bg-[#81BDFF] text-[#003967] rounded-[4px] px-5 py-2 text-sm font-medium` (`<a :href="'tel:' + cleanPhone">`).
   - **Mobile (`< lg`, 375px)**:
     - Thanh bar gọn: Logo bên trái; bên phải gồm nút Tìm kiếm, nút gọi nhanh hoặc icon phone, và nút Hamburger (`min-w-[44px] min-h-[44px]`, `aria-expanded`, `aria-controls="mobile-drawer"`).
     - **Mobile Slide-in Drawer**: Nền `#FFFFFF`, rộng `w-[85vw] max-w-[340px]`, chứa header Logo + nút Đóng (`×`), 5 mục nav + accordion danh mục `SẢN PHẨM`, 2 nút CTA xếp dọc full-width, và block thông tin Email/Hotline từ CMS. Đóng bằng nút Đóng, backdrop click, phím `Esc`, hoặc đổi route; tự khóa cuộn `body` khi mở.

### 4.2. Footer (`resources/Frontend/js/Components/Footer.vue` — Figma `595:15535`)

1. **Khối Trên (`bg-[#FAFAFA] pt-10 pb-[60px] border-t border-[#E3E8EF]`)**:
   - Container `max-w-[1280px] mx-auto px-4 md:px-8`.
   - Flex/Grid responsive: cột thương hiệu bên trái (`lg:w-[360px] shrink-0`) + 3 cột bên phải (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 xl:gap-16`):
     - **Cột thương hiệu**: Logo (`h-[56px] lg:h-[68px] w-auto`, Figma `203×68`), Tên công ty (`20px Bold #00284B`), Mô tả ngắn (`14px Medium #4B5565`).
     - **Cột 1 — `Trang chủ`**: `Sản phẩm`, `Giới thiệu`, `Tài liệu`, `Tin tức`, `Liên hệ`.
     - **Cột 2 — `Chính sách`**: Render động từ `$page.props.data?.policies` (nếu rỗng hiển thị fallback 6 mục chuẩn: Chính sách bán hàng, Chính sách thanh toán, Chính sách vận chuyển, Chính sách đổi trả hàng, Chính sách bảo hành, Chính sách bảo mật thông tin).
     - **Cột 3 — `Thông tin liên hệ`**:
       - `/assets/svg/icons/footer-phone.svg` (`24×24`) + Hotline (`tel:`)
       - `/assets/svg/icons/footer-mail.svg` (`24×24`) + Email (`mailto:`)
       - `/assets/svg/icons/footer-pin.svg` (`24×24`) + Địa chỉ Trụ sở & VPGD/Showroom (`global.general_address` / `showroom_address`).
2. **Bottom Bar (`bg-[#00284B] py-3`)**:
   - Container `max-w-[1280px] mx-auto px-4 md:px-8`, flex responsive (`flex-col sm:flex-row items-center justify-between gap-3`).
   - **Trái**: `© Copyright 2026 THÀNH KHANG` (`12px text-[#C9E0FF]`).
   - **Phải**: Icon mạng xã hội `20×20`: `/assets/svg/icons/footer-facebook.svg` (gắn link Facebook từ CMS) và `/assets/svg/icons/footer-linkedin.svg` (gắn link LinkedIn từ CMS).

---

## 5. Danh mục Tài nguyên đã Tải cục bộ (Verified on Disk)

Tất cả 10 tài nguyên gốc từ Figma đã được tải về cục bộ và xác thực khác 0 byte, đúng định dạng PNG/SVG:

| Asset | Đường dẫn Public (Runtime) | Định dạng & Kích thước |
| :--- | :--- | :--- |
| Logo Thành Khang (`Artboard 5 1`) | `/assets/images/logo/logo-thanhkhang.png` | PNG RGBA `1048×353` (289 KB) |
| Header Top Bar Mail (`mail-01`) | `/assets/svg/icons/header-mail.svg` | SVG (970 B) |
| Header Top Bar Phone (`phone`) | `/assets/svg/icons/header-phone.svg` | SVG (1.6 KB) |
| Header Nav Chevron (`chevron-down`) | `/assets/svg/icons/header-chevron-down.svg` | SVG (319 B) |
| Header Search (`search-sm`) | `/assets/svg/icons/header-search.svg` | SVG (505 B) |
| Footer Contact Phone (`phone-call-01`) | `/assets/svg/icons/footer-phone.svg` | SVG (1.9 KB) |
| Footer Contact Mail (`mail-01`) | `/assets/svg/icons/footer-mail.svg` | SVG (934 B) |
| Footer Contact Pin (`marker-pin-01`) | `/assets/svg/icons/footer-pin.svg` | SVG (520 B) |
| Footer Social Facebook | `/assets/svg/icons/footer-facebook.svg` | SVG (727 B) |
| Footer Social LinkedIn | `/assets/svg/icons/footer-linkedin.svg` | SVG (943 B) |
