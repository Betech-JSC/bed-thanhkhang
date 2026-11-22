# Spec: Design Tokens & Layout Shell Thành Khang (Home-First Core)

## Requirements

### Requirement 1: Hệ thống Màu sắc & Design Tokens Chuẩn Figma
Hệ thống Tailwind CSS (`resources/Frontend/tailwind.config.js`) và SCSS toàn cục (`resources/Frontend/scss/app.scss`) PHẢI định nghĩa đầy đủ các token màu sắc, bo góc và đổ bóng trích xuất từ Figma Trang Chủ Thành Khang (`595:15360`), đồng thời duy trì backward-compatible aliases cho các thành phần hiện hữu.

#### Scenario 1.1: Kiểm tra bảng màu chủ đạo và biến thể trạng thái
- **WHEN** `resources/Frontend/tailwind.config.js` được biên dịch qua `yarn frontend:build`
- **THEN** các token màu chuẩn có giá trị chính xác:
  - Primary: `DEFAULT / 500 = #005EA4`, `hover / 600 = #004D86`, `active / 700 = #003967`, `info = #0075E7`
  - Secondary: `DEFAULT / 100 = #C9E0FF`, `hover / 200 = #81BDFF`
  - Dark: `DEFAULT / 800 = #00284B`, `slate / 700 = #364152`
  - Surface: `DEFAULT = #FAFAFA`, `white = #FFFFFF`, `50 = #F8FAFC`, `100 = #EEF2F6`, `gray-light = #F1F1F1`
  - Neutral / Border / Text: `gray-600 = #4B5565`, `gray-500 = #697586`, `gray-400 = #9AA4B2`, `gray-200 = #E3E8EF`, `gray-300 = #D6D6D6`
  - Shadow: `xs = 0 1px 2px 0 rgba(16, 24, 40, 0.05)`

---

### Requirement 2: Typography `Open Sans` & Kế thừa Font toàn hệ thống
Ứng dụng Frontend PHẢI nạp font `Open Sans` (`400, 500, 600, 700`, hỗ trợ tiếng Việt) từ `resources/Frontend/views/app.blade.php`, cấu hình scale H1–H4 và Body cho Desktop & Mobile, và cưỡng chế kế thừa `font-family` trên mọi phần tử nhập liệu và nút bấm.

#### Scenario 2.1: Kiểm tra computed `font-family` trên trình duyệt thực
- **WHEN** người dùng mở trang chủ trên trình duyệt Desktop (`1440×900`) hoặc Mobile (`375×812`)
- **THEN** `getComputedStyle()` của `body`, thẻ tiêu đề `h1`–`h4`, `<button>`, `<input>`, `<select>` và `<textarea>` đều ưu tiên `Open Sans` (fallback `Inter, system-ui, sans-serif`), không rơi về font mặc định trình duyệt hoặc bị PrimeVue ghi đè.

#### Scenario 2.2: Tỷ lệ Heading và Body responsive
- **WHEN** các class heading/body được áp dụng giữa viewport Mobile (`375px`) và Desktop (`1440px`)
- **THEN** kích thước và line-height khớp thông số Figma:
  - `H1`: Desktop `48px / 1.4 / 700`, Mobile `32px / 1.3 / 700`
  - `H2`: Desktop `28px / 1.5 / 700`, Mobile `24px / 1.4 / 700`
  - `H3`: Desktop `24px / 1.6 / 700`, Mobile `20px / 1.5 / 700`
  - `H4`: Desktop `20px / 1.6 / 700`, Mobile `18px / 1.5 / 700`
  - Body: `body-xl` (`20px/1.6`), `body-lg` (`18px/1.6`), `body-md` (`16px/1.6`), `body-sm` (`14px/1.6`), `body-xs` (`12px/1.6`).

---

### Requirement 3: Container, Gutters & Chống tràn ngang
Hệ thống Layout PHẢI thiết lập container căn giữa với độ rộng chuẩn Figma (`1280px` cho Hero/Footer và hỗ trợ khung `1152px` cho Header/Content sections), padding ngang `px-4` (`16px`) trên Mobile và `px-8` (`32px`) trên Desktop, bảo đảm 0 tràn ngang trên mọi khung nhìn.

#### Scenario 3.1: Kiểm tra overflow ngang ở 1440px và 375px
- **WHEN** mở trang chủ tại viewport `1440×900` và `375×812`
- **THEN** `document.documentElement.scrollWidth <= window.innerWidth` ở cả hai kích thước, và nội dung không dính sát mép màn hình trên Mobile (`padding-left >= 16px`, `padding-right >= 16px`).

---

### Requirement 4: Header Desktop (1440px) & Sticky Behavior
`Header.vue` PHẢI hiển thị đúng cấu trúc 2 tầng theo Figma node `595:15540` (tổng chiều cao Desktop `118px`: Top bar `46px` + Main bar `72px`) và giữ trạng thái sticky mượt mà khi cuộn trang.

#### Scenario 4.1: Hiển thị đầy đủ thành phần Desktop Header
- **WHEN** trang được hiển thị ở độ rộng `1440px`
- **THEN**:
  - **Top bar** (`46px`, nền `#00284B`, chữ `#FCFCFC` `14px Regular`): bên trái hiển thị tên doanh nghiệp từ CMS; bên phải hiển thị icon `header-mail.svg` + Email và icon `header-phone.svg` + Hotline dạng liên kết `tel:`.
  - **Main bar** (`72px`, nền `#FAFAFA`): bên trái hiển thị Logo (`120×40`); ở giữa hiển thị 5 mục `VỀ CHÚNG TÔI`, `SẢN PHẨM` (kèm `header-chevron-down.svg` mở dropdown từ `$page.props.product_categories`), `TÀI LIỆU`, `TIN TỨC`, `LIÊN HỆ` (`14px Bold`, `#00284B`); bên phải hiển thị nút tìm kiếm (`header-search.svg`) và 2 CTA `Nhận báo giá` (`bg-[#005EA4] text-[#FAFAFA] rounded-[4px]`) & `Gọi hotline` (`bg-[#C9E0FF] text-[#003967] rounded-[4px]`, `tel:`).

#### Scenario 4.2: Hành vi Sticky khi cuộn trang
- **WHEN** người dùng cuộn trang xuống quá chiều cao Top bar (`> 46px`)
- **THEN** thanh Main bar cố định ở đầu viewport (`position: sticky` hoặc `fixed` đồng bộ với padding-top của `<main>`), giữ độ tương phản rõ nét (border/shadow nhẹ) và không che khuất hay làm nhảy bố cục nội dung bên dưới.

---

### Requirement 5: Header Mobile (375px) & Slide-in Mobile Drawer
Trên màn hình `< lg` (kiểm chứng chuẩn ở `375×812`), `Header.vue` PHẢI ẩn Top bar Desktop, hiển thị thanh điều hướng gọn (Logo + hành động nhanh + nút Hamburger) và hỗ trợ Mobile Drawer đầy đủ tính năng truy cập.

#### Scenario 5.1: Mở và đóng Mobile Drawer
- **WHEN** người dùng ở viewport `375×812` bấm nút Hamburger menu
- **THEN** Mobile Drawer trượt vào hiển thị đủ 5 mục điều hướng chính, menu con `SẢN PHẨM` mở rộng được từ `$page.props.product_categories`, 2 nút CTA (`Nhận báo giá`, `Gọi hotline`), và thông tin liên hệ (Email/Hotline), đồng thời khóa cuộn `body`.
- **WHEN** người dùng bấm nút Đóng, bấm vào backdrop bên ngoài, hoặc nhấn phím `Escape`
- **THEN** Mobile Drawer đóng lại trơn tru và mở khóa cuộn `body`.

---

### Requirement 6: Footer Desktop & Mobile (Node `595:15535`)
`Footer.vue` PHẢI hiển thị đúng bố cục 2 phần (Top content nền `#FAFAFA` + Bottom bar nền `#00284B`) trên Desktop (`1440px`) và xếp chồng sạch sẽ, dễ đọc trên Mobile (`375px`).

#### Scenario 6.1: Cấu trúc Footer Desktop & Mobile
- **WHEN** người dùng cuộn tới Footer trên Desktop (`1440px`)
- **THEN** khối trên (`bg-[#FAFAFA] pt-10 pb-[60px]`) hiển thị cột thương hiệu bên trái (`lg:w-[360px]`, Logo `203×68`, tên công ty `20px Bold #00284B`, mô tả `14px Medium #4B5565`) và 3 cột bên phải: `Trang chủ` (5 links), `Chính sách` (render từ `$page.props.data?.policies`), `Thông tin liên hệ` (icon `footer-phone.svg`, `footer-mail.svg`, `footer-pin.svg` hiển thị Hotline, Email, Trụ sở & VPGD/Showroom); thanh Bottom bar (`bg-[#00284B] py-3`) hiển thị `© Copyright 2026 THÀNH KHANG` (`12px #C9E0FF`) bên trái và icon `footer-facebook.svg`, `footer-linkedin.svg` bên phải.
- **WHEN** xem Footer tại viewport `375px`
- **THEN** các cột xếp dọc gọn gàng với đệm `px-4`, không tràn ngang, không gãy chữ đè lên icon.

---

### Requirement 7: Kết nối dữ liệu động CMS & Tài nguyên cục bộ (Zero Hardcoded Runtime Figma URLs)
Layout Shell PHẢI đọc toàn bộ thông tin doanh nghiệp, liên hệ, danh mục và chính sách từ Inertia shared props (`$page.props.configs || $page.props.global`, `$page.props.product_categories`, `$page.props.data?.policies`) và chỉ tham chiếu tài nguyên ảnh/SVG đã tải về cục bộ trong `public/assets/`.

#### Scenario 7.1: Xác thực tài nguyên cục bộ và không có lỗi tải ảnh
- **WHEN** tải trang chủ trong Playwright MCP ở cả Desktop và Mobile
- **THEN** mọi thẻ `<img>` đều thỏa mãn `img.complete && img.naturalWidth > 0`, mọi SVG icon load HTTP 200 từ `/assets/svg/icons/` và logo load từ CMS hoặc fallback `/assets/images/logo/logo-thanhkhang.png`, 0 request tới `figma.com`, và 0 console errors.
