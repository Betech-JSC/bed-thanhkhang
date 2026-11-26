# Spec: Design Tokens & Layout Shell Thành Khang (Official Home `979:32231` & Mobile `1190:6335`)

## Requirements

### Requirement 1: Hệ thống Màu sắc & Design Tokens Chuẩn Figma Official
Hệ thống Tailwind CSS (`resources/Frontend/tailwind.config.js`) và SCSS toàn cục (`resources/Frontend/scss/app.scss`) PHẢI định nghĩa đầy đủ các token màu sắc, bo góc và đổ bóng trích xuất trực tiếp từ Figma Trang Chủ Official (`979:32231`), Mobile (`1190:6335`) và Design System (`25:1713`), đồng thời duy trì backward-compatible aliases cho các trang hiện hữu.

#### Scenario 1.1: Kiểm tra bảng màu chủ đạo và biến thể trạng thái
- **WHEN** `resources/Frontend/tailwind.config.js` và `resources/Frontend/scss/app.scss` được biên dịch qua `yarn frontend:build`
- **THEN** các token màu chuẩn có giá trị chính xác:
  - Primary: `DEFAULT / 500 = #005EA4`, `hover / 600 = #004D86`, `active / 700 = #003967`, `info / 400 = #0075E7`, `sub-hover = #007FD9`, `accent = #009FFF`
  - Secondary: `DEFAULT / 100 = #C9E0FF`, `hover / 200 = #81BDFF`, `disabled / 50 = #EDF6FF`, `disabled-text / 300 = #5BA4FB`
  - Dark: `DEFAULT / 800 = #00284B`, `slate / 700 = #364152`
  - Surface: `DEFAULT = #FAFAFA`, `white = #FFFFFF`, `50 = #F8FAFC`, `100 = #EEF2F6`, `200 = #E3E8EF`, `300 = #CDD5DF`, `gray-light = #F1F1F1`, `light = #FCFCFC`
  - Neutral / Border / Text: `ink.heading = #00284B`, `gray-600 = #4B5565`, `gray-500 = #697586`, `gray-400 = #9AA4B2`, `gray-200 = #E3E8EF`, `gray-300 = #D6D6D6`
  - Shadow: `xs = 0 1px 2px 0 rgba(16, 24, 40, 0.05)`, `process-card = 2px 2px 6px 0px rgba(0, 0, 0, 0.25)`

---

### Requirement 2: Typography `Open Sans` & Kế thừa Font toàn hệ thống
Ứng dụng Frontend PHẢI nạp font `Open Sans` (`400, 500, 600, 700`, hỗ trợ tiếng Việt) và `Inter` (`400, 500, 600, 700`) từ `resources/Frontend/views/app.blade.php`, cấu hình thang đo H1–H4 và Body cho Desktop (`979:32231`) & Mobile (`1190:6335`), và cưỡng chế kế thừa `font-family` trên mọi phần tử nhập liệu và nút bấm.

#### Scenario 2.1: Kiểm tra computed `font-family` trên trình duyệt thực
- **WHEN** người dùng mở trang chủ trên trình duyệt Desktop (`1440×900`) hoặc Mobile (`390×844` / `375×812`)
- **THEN** `getComputedStyle()` của `body`, thẻ tiêu đề `h1`–`h4`, `<button>`, `<input>`, `<select>` và `<textarea>` đều ưu tiên `Open Sans` (fallback `Inter, system-ui, sans-serif`), không rơi về font mặc định trình duyệt.

#### Scenario 2.2: Tỷ lệ Heading và Body responsive khớp Figma Official
- **WHEN** các thẻ/class heading và body được render giữa viewport Mobile (`< 768px`) và Desktop (`>= 768px`)
- **THEN** kích thước và line-height khớp thông số Figma (`979:32231` & `1190:6335`):
  - `H1`: Desktop `48px / 67px (~1.4) / 700` (`979:32441`), Mobile `28px / 36px (~1.286) / 700` (`1190:6365`)
  - `H2`: Desktop `28px / 45px (~1.6) / 700` (`979:32319`, `979:32379`), Mobile `20px / 28px (1.4) / 700` (`1191:6451`)
  - `H3`: Desktop `24px / 38px (~1.583) / 700` (`979:32253`, `979:32298`), Mobile `18px / 27px (1.5) / 700` (`1190:6458`)
  - `H4`: Desktop `20px / 32px (1.6) / 700` (`184:1292`, `979:32326`, `353:9442`), Mobile `16px / 24px (1.5) / 700` (`1190:6395`, `1191:6392`)
  - Body: `body-xl` (`20px/1.6`), `body-lg` (`18px/29px`), `body-md` (`16px/26px`), `body-sm` (`14px/22px`), `body-sm-mobile` (`13px/20px`), `body-xs` (`12px/19px`).

---

### Requirement 3: Dual-Grid Container, Gutters & Chống tràn ngang
Hệ thống Layout PHẢI hỗ trợ chuẩn xác 2 khung lưới của Figma Desktop `1440px` kết hợp với gutter bắt buộc `px-4` (`16px`) trên Mobile và `px-8` (`32px`) trên Desktop:
1. **Wide Container (`1280px` inner / `1344px` max-width)**: Dùng cho Hero (`979:32435`) và Footer (`979:32406`).
2. **Narrow/Content Container (`1152px` inner / `1216px` max-width)**: Dùng cho Desktop Header (`979:32411`) và 6 section nội dung Trang Chủ (`979:32233`–`979:32367`).

#### Scenario 3.1: Kiểm tra overflow ngang ở Desktop và Mobile
- **WHEN** mở trang chủ tại viewport Desktop (`1440×900`) và Mobile (`390×844` / `375×812`)
- **THEN** `document.documentElement.scrollWidth <= window.innerWidth` ở mọi khung nhìn, và nội dung trên Mobile có khoảng đệm tối thiểu `16px` mỗi bên (`px-4`).

---

### Requirement 4: Header Desktop (`979:32411`, `979:32772`), Mega-Menu (`856:24212`) & Sticky Behavior
`Header.vue` trên Desktop (`lg+`) PHẢI hiển thị đúng cấu trúc 2 tầng theo Figma node `979:32411` (`1440×118`: Top bar `46px` + Main bar `72px`), khung nội dung `1152px` (`max-w-[1216px] px-4 md:px-8`), hỗ trợ Mega-menu 2 cột cho `SẢN PHẨM` (`856:24212`) và giữ trạng thái sticky mượt mà khi cuộn trang.

#### Scenario 4.1: Hiển thị đầy đủ thành phần Desktop Header & Mega-Menu
- **WHEN** trang được hiển thị ở độ rộng `1440px`
- **THEN**:
  - **Top bar** (`46px`, nền `#00284B`, chữ `#FCFCFC` `14px Regular`): bên trái hiển thị tên doanh nghiệp từ `$page.props.configs` / `$page.props.global`; bên phải hiển thị `header-mail.svg` (`18×18`) + `Email: ...` và `header-phone.svg` (`18×18`) + `Hotline: ...` (`tel:`).
  - **Main bar** (`72px`, nền `#FFFFFF`): bên trái Logo `120×40`; ở giữa 5 mục điều hướng đọc từ `$page.props.configs?.menus` (`14px Bold` uppercase, mặc định `#697586`, active/hover `#004D86` kèm gạch chân `3px #004D86`); khi mở `SẢN PHẨM` hiển thị Mega-menu 2 vùng (`bg-[#FAFAFA] rounded-[4px]`, không tràn ngang viewport ở `1024px–1440px`: cột trái `300px–340px bg-[#EEF2F6]` liệt kê danh mục gốc Cấp 1 từ `$page.props.configs?.product_categories` + nút `Xem tất cả sản phẩm` `#005EA4`, vùng phải liệt kê danh mục con Cấp 2 `#005EA4` hover `#007FD9` & Cấp 3 `#697586`); bên phải gồm icon Search (`20×20`) đứng trước 2 nút CTA `Nhận báo giá` (`128×38`, `#005EA4`, chữ `#FAFAFA`) và `Gọi hotline` (`113×38`, `#C9E0FF`, chữ `#003967`).

#### Scenario 4.2: Hành vi Sticky khi cuộn trang
- **WHEN** người dùng cuộn trang xuống quá chiều cao Top bar
- **THEN** thanh Main bar cố định mượt ở đầu viewport với viền dưới `#E3E8EF` / `shadow-xs`, không che khuất hay làm giật bố cục nội dung bên dưới.

---

### Requirement 5: Header Mobile (`1190:6336`) & Slide-in Mobile Drawer
Trên màn hình `< lg` (kiểm chứng chuẩn ở `390×844` / `375×812`), `Header.vue` PHẢI hiển thị đúng cấu trúc 2 tầng Mobile theo Figma node `1190:6336` (`92px`: Mobile Top Bar `31px` + Mobile Main Nav `61px`) và hỗ trợ Slide-in Mobile Drawer đầy đủ tính năng truy cập.

#### Scenario 5.1: Hiển thị Mobile Header 2 tầng và đóng/mở Mobile Drawer
- **WHEN** người dùng mở trang trên viewport Mobile (`390×844` / `375×812`)
- **THEN** hiển thị **Mobile Top Bar** (`31px`, nền `#00284B`, `px-4`, trái: icon phone `14×14` + số Hotline `12px #FCFCFC`, phải: icon mail `14×14` + Email `12px #FCFCFC`) và **Mobile Main Nav** (`61px`, nền `#FFFFFF`, `border-b #E3E8EF`, `px-4`, trái: Logo `96×32`, phải `gap-2`: nút Search `36×36`, CTA `Nhận báo giá` `100×33 bg-[#005EA4] text-[12px]`, nút Hamburger `36×36 bg-[#EEF2F6]`).
- **WHEN** người dùng bấm nút Hamburger menu
- **THEN** Mobile Drawer trượt vào hiển thị đủ các mục điều hướng, accordion danh mục `SẢN PHẨM` từ `$page.props.product_categories`, 2 nút CTA và thông tin liên hệ, khóa cuộn `body`, và đóng lại trơn tru khi bấm nút Đóng, backdrop hoặc phím `Escape`.

---

### Requirement 6: Footer Desktop (Node `979:32406` / Master `217:3913`) & Mobile (Node `1193:6561`)
`Footer.vue` PHẢI hiển thị đúng bố cục 2 phần (Top content nền `#FAFAFA` + Bottom bar `51px` Desktop / `48px` Mobile nền `#00284B`) theo Figma node `979:32406` (`1440×471`, khung trong `1280px`) trên Desktop và khớp bố cục node `1193:6561` (`390×838`) trên Mobile.

#### Scenario 6.1: Cấu trúc Footer Desktop & Mobile
- **WHEN** người dùng cuộn tới Footer trên Desktop (`1440px`)
- **THEN** khối trên (`bg-[#FAFAFA] pt-10 pb-[60px]`, khung `1280px`) hiển thị cột thương hiệu bên trái (`lg:w-[360px]`, Logo `203×68`, tên công ty uppercase `20px/32px Bold #00284B`, mô tả `14px/22px Medium #4B5565`) và 3 cột bên phải (`~267px`/cột):
  1. `Danh mục sản phẩm` (`font-inter`, `16px Bold #00284B`, danh sách danh mục gốc từ `$page.props.configs?.product_categories`, `14px Medium #4B5565`)
  2. `Chính sách` (`font-inter`, `16px Bold #00284B`, danh sách chính sách từ `$page.props.configs?.policies`, `14px Medium #4B5565`)
  3. `Thông tin liên hệ` (`16px Bold #00284B`, 5 dòng kèm icon `24×24`: `footer-phone.svg` Hotline, `footer-mail.svg` Email, `footer-building.svg` `Mã số thuế`, `footer-pin.svg` Trụ sở, `footer-pin.svg` VPGD & Showroom)
  và thanh Bottom bar (`bg-[#00284B] min-h-[51px]`) hiển thị `© Copyright {{ currentYear }} ...` (`12px #C9E0FF`) bên trái cùng `footer-facebook.svg`, `footer-linkedin.svg` (`20×20`, `gap-6`) bên phải.
- **WHEN** xem Footer tại viewport Mobile (`390px` / `375px`)
- **THEN** hiển thị khớp Figma `1193:6561` với đệm `px-4`: Brand Info (Logo `138×46`, tên công ty `14px/21px Bold`, mô tả `13px/21px`), lưới 2 cột ngang `grid-cols-2 gap-4` cho `Danh mục sản phẩm | Chính sách` (`14px` heading, `13px` links), đường kẻ ngang `1px #E3E8EF` (`1193:6585`), khối `Thông tin liên hệ` bên dưới với icon `18×18` + chữ `13px/20px #364152`, và Bottom bar `min-h-[48px]` xếp ngang (`Copyright` bên trái, `Social icons` `gap-4` bên phải), không tràn ngang.

---

### Requirement 7: Kết nối dữ liệu động CMS & Tài nguyên cục bộ (Zero Hardcoded Runtime Figma URLs)
Layout Shell PHẢI đọc toàn bộ thông tin doanh nghiệp, menu điều hướng, liên hệ, mã số thuế, địa chỉ, mạng xã hội, danh mục 3 cấp và chính sách từ Inertia shared props (`$page.props.configs` được `HandleInertiaFrontendRequests.php:281-285` chia sẻ trực tiếp, kết hợp merge fallback với `$page.props.global`, `$page.props.product_categories`, `$page.props.data?.policies`) và chỉ tham chiếu tài nguyên ảnh/SVG cục bộ trong `public/assets/`.

#### Scenario 7.1: Xác thực tài nguyên cục bộ và không có lỗi tải ảnh
- **WHEN** tải trang chủ trong Playwright MCP ở cả Desktop và Mobile
- **THEN** mọi thẻ `<img>` đều thỏa mãn `img.complete && img.naturalWidth > 0`, mọi SVG icon load HTTP 200 từ `/assets/svg/icons/`, 0 request tới `figma.com`, và 0 console errors.
