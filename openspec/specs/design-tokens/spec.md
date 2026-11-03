# design-tokens Specification

## Purpose

Cung cấp và chuẩn hóa toàn bộ hệ thống Design Tokens (Layout Spacing & Container, Semantic Colors, Typography Scale, Border Radii, Dark Mode Shadows & Glows) và tối ưu hóa CSS bundle theo chuẩn Figma HH-Maker Dark Tech (Node 2261:5076).

## Requirements

### Requirement: Khóa cứng Container Max-Width và Padding Responsive
Hệ thống SHALL nâng cấp cấu hình độ rộng tối đa của lớp tiện ích `.container` trong `resources/Frontend/tailwind.config.js` để hỗ trợ hiển thị tối ưu trên màn hình chuẩn 1440px:
- `sm`: `640px`
- `md`: `768px`
- `lg`: `1024px`
- `xl`: `1280px`
- `2xl`: `1440px`
Đồng thời, hệ thống SHALL bổ sung các tiện ích `max-width` độc lập phục vụ layout canvas theo chuẩn Figma:
- `max-w-canvas`: `1440px` (chiều rộng tối đa khung canvas toàn trang)
- `max-w-content`: `1152px` (chiều rộng khối nội dung tiêu chuẩn)
- `max-w-hero`: `1279px` (chiều rộng khối thẻ Hero theo Figma Node `2261:5475`)

#### Scenario: Kiểm soát max-width của container trên Desktop
- **WHEN** một phần tử sử dụng class `.container` được hiển thị trên màn hình có kích thước viewport $\ge 1280\text{px}$ và $1440\text{px}$
- **THEN** phần tử SHALL có `max-width` tương ứng (`1280px` tại viewport `xl` và `1440px` tại `2xl`) và được căn giữa tự động với margin hai bên bằng nhau.

#### Scenario: Padding responsive của container
- **WHEN** màn hình ở độ phân giải mobile ($< 768\text{px}$)
- **THEN** container SHALL có padding ngang là `1rem` (`16px`).
- **WHEN** màn hình ở độ phân giải tablet hoặc desktop ($\ge 768\text{px}$)
- **THEN** container SHALL có padding ngang là `1.5rem` (`24px`).

#### Scenario: Ứng dụng các tiện ích max-width độc lập
- **WHEN** một khối thành phần áp dụng class `max-w-canvas`, `max-w-content`, hoặc `max-w-hero`
- **THEN** phần tử SHALL có `max-width` tương ứng chính xác là `1440px`, `1152px`, hoặc `1279px`.

---

### Requirement: Token nhịp điệu khoảng cách Layout Rhythm Spacing
Hệ thống SHALL bổ sung các tokens khoảng cách nhịp điệu chuẩn hóa vào `theme.extend.spacing`:
- `section`: `5rem` (`80px`) dành cho khoảng cách chuẩn giữa các khối nội dung.
- `section-lg`: `7.5rem` (`120px`) dành cho các khối section lớn hoặc khoảng cách đầu trang.
- `gutter`: `1.5rem` (`24px`) dành cho khoảng cách rãnh lưới chuẩn giữa các cột và thẻ.

#### Scenario: Áp dụng khoảng cách section dọc
- **WHEN** một phần tử sử dụng các class tiện ích như `py-section` hoặc `my-section`
- **THEN** hệ thống SHALL áp dụng khoảng cách padding/margin đúng bằng `5rem` (`80px`).

#### Scenario: Áp dụng khoảng cách rãnh lưới gutter
- **WHEN** một phần tử layout sử dụng `gap-gutter` hoặc `px-gutter`
- **THEN** hệ thống SHALL áp dụng khoảng cách đúng bằng `1.5rem` (`24px`).

---

### Requirement: Chuẩn hóa Semantic Colors và Alias DEFAULT
Hệ thống SHALL cấu hình thuộc tính `DEFAULT` cho toàn bộ các palette trạng thái ngữ thái (Semantic Colors) trong `resources/Frontend/tailwind.config.js`:
- `success`: `DEFAULT: '#12B76A'` (kèm dải sắc độ 25–900).
- `warning`: `DEFAULT: '#F79009'` (kèm dải sắc độ 25–900).
- `error`: `DEFAULT: '#F04438'` (kèm dải sắc độ 25–900).
- `info`: `DEFAULT: '#2E90FA'` (kèm dải sắc độ đầy đủ từ 25 đến 900 phù hợp tông xanh thông tin).

#### Scenario: Sử dụng trực tiếp class semantic không cần chỉ định thang sắc độ
- **WHEN** lập trình viên sử dụng các class tiện ích như `bg-success`, `text-error`, `border-warning`, hoặc `bg-info`
- **THEN** Tailwind CSS SHALL biên dịch đúng mã màu tương ứng (`#12B76A`, `#F04438`, `#F79009`, `#2E90FA`) mà không yêu cầu hậu tố `-500`.

#### Scenario: Tính khả dụng của palette info
- **WHEN** lập trình viên sử dụng `bg-info-50`, `bg-info`, hoặc `text-info-700`
- **THEN** hệ thống SHALL phân giải thành công các mã màu trong dải sắc độ info chuẩn Dark Tech.

---

### Requirement: Phân tách và giải quyết xung đột Text Color Tokens
Hệ thống SHALL tách bạch rõ ràng giữa màu thương hiệu `primary` (`#0052FF`) và các cấp độ màu chữ giao diện Dark Tech, loại bỏ xung đột cú pháp `'text-primary': '#FFFFFF'`. Đồng thời định nghĩa các token màu chữ chuẩn:
- `text-white`: `#FFFFFF`
- `text-secondary`: `#94A3B8` (slate-400)
- `text-muted`: `#64748B` (slate-500)
- `text-dim`: `#475467` (slate-600)
Và cô lập các mã màu kế thừa cũ FunkyStyle (`red-fks`, `black-fks`, `green-fks`, `blue-fks`).

#### Scenario: Phân cấp màu chữ không gây đè class text-primary
- **WHEN** một phần tử áp dụng class `text-primary`
- **THEN** màu chữ SHALL là màu xanh thương hiệu `#0052FF`.
- **WHEN** một phần tử áp dụng class `text-white`, `text-secondary`, `text-muted`, hoặc `text-dim`
- **THEN** màu chữ SHALL phản ánh chính xác các cấp độ tương phản từ sáng nhất (`#FFFFFF`) đến mờ dần (`#94A3B8`, `#64748B`, `#475467`).

#### Scenario: Cô lập và lập lộ trình loại bỏ màu FunkyStyle
- **WHEN** kiểm tra tệp cấu hình Tailwind
- **THEN** các màu `fks` SHALL được đánh dấu deprecate và không được tham chiếu trong bất kỳ component mới nào của HH-Maker.

---

### Requirement: Tinh gọn Font-Face và Token hóa Thang chữ Typography Scale
Hệ thống SHALL dỡ bỏ hoàn toàn 7 khối `@font-face` không sử dụng (`Phudu` 4 khối và `BT-BeauSans` 3 khối) trong `resources/Frontend/scss/app.scss` để giảm tải kích thước CSS bundle. Đồng thời, hệ thống SHALL cấu hình font-family ưu tiên `'Inter'` làm phông chữ chính của giao diện (`['Inter', 'Be Vietnam Pro', 'sans-serif']`) nhằm đảm bảo đồng nhất 100% với thiết kế Figma HH Maker. Đồng thời, hệ thống SHALL token hóa đầy đủ thang kích thước chữ chuẩn Figma vào `theme.extend.fontSize` bao gồm:
- `display-hero`: `['38px', { lineHeight: '46px', letterSpacing: '-0.01em' }]` (tiêu đề Hero H1 và Section chính H2 của cả 8 trang)
- `display-banner`: `['32px', { lineHeight: '38px', letterSpacing: '-0.01em' }]` (tiêu đề banner CTA "Tạo nên phiên bản mang dấu ấn riêng")
- `display-section`: `['28px', { lineHeight: '34px', letterSpacing: '0' }]` (tiêu đề bài viết và các bước quy trình lớn)
- `display-card`: `['24px', { lineHeight: '30px', letterSpacing: '0' }]` (tiêu đề Card dịch vụ, sản phẩm và tính năng)
- `display-step`: `['42px', { lineHeight: '51px', letterSpacing: '0' }]` (số thứ tự bước quy trình lớn)
- `display-2xl`: `[72px, { lineHeight: '90px', letterSpacing: '-0.02em' }]`
- `display-xl`: `[60px, { lineHeight: '72px', letterSpacing: '-0.02em' }]`
- `display-lg`: `[48px, { lineHeight: '60px', letterSpacing: '-0.02em' }]`
- `display-md`: `[36px, { lineHeight: '44px', letterSpacing: '-0.02em' }]`
- `display-sm`: `[30px, { lineHeight: '38px', letterSpacing: '0' }]`
- `text-xl`: `[20px, { lineHeight: '30px', letterSpacing: '0' }]`
- `text-lg`: `[18px, { lineHeight: '28px', letterSpacing: '0' }]`
- `text-md`: `[16px, { lineHeight: '24px', letterSpacing: '0' }]`
- `text-sm`: `[14px, { lineHeight: '20px', letterSpacing: '0' }]`
- `text-xs`: `[12px, { lineHeight: '18px', letterSpacing: '0' }]`

#### Scenario: Giảm thiểu kích thước tải CSS sau khi dọn sạch font không sử dụng
- **WHEN** tiến hành build CSS giao diện qua `yarn frontend:build`
- **THEN** tệp CSS đầu ra SHALL không còn chứa các quy tắc `@font-face` định nghĩa font-family `Phudu` hay `BT-BeauSans`.

#### Scenario: Ứng dụng thang chữ chuẩn Figma với lineHeight và letterSpacing đồng bộ
- **WHEN** một phần tử áp dụng class `text-display-lg`
- **THEN** kích thước font chữ SHALL là `48px`, `line-height: 60px`, và `letter-spacing: -0.02em`.

#### Scenario: Cấu hình font family ưu tiên Inter
- **WHEN** trình duyệt biên dịch và render giao diện người dùng
- **THEN** các phần tử sử dụng `font-sans` hoặc `font-display` SHALL áp dụng phông chữ `Inter` làm lựa chọn ưu tiên hàng đầu trước `Be Vietnam Pro`.

#### Scenario: Ứng dụng token display-hero 38px cho tiêu đề chính
- **WHEN** một phần tử áp dụng class `text-display-hero`
- **THEN** phần tử SHALL có `font-size: 38px` và `line-height: 46px`.

#### Scenario: Ứng dụng token display-banner 32px cho CTA banner
- **WHEN** một phần tử áp dụng class `text-display-banner`
- **THEN** phần tử SHALL có `font-size: 32px` và `line-height: 38px`.

#### Scenario: Ứng dụng token display-section 28px cho bài viết và bước
- **WHEN** một phần tử áp dụng class `text-display-section`
- **THEN** phần tử SHALL có `font-size: 28px` và `line-height: 34px`.

#### Scenario: Ứng dụng token display-card 24px cho card
- **WHEN** một phần tử áp dụng class `text-display-card`
- **THEN** phần tử SHALL có `font-size: 24px` và `line-height: 30px`.

### Requirement: Mở rộng Border Radius và Dark Mode Shadows
Hệ thống SHALL bổ sung các tokens bo góc và hiệu ứng đổ bóng / viền sáng chuyên biệt cho phong cách Dark Tech:
- Bo góc trong `theme.extend.borderRadius`:
  + `input`: `10px` (`0.625rem`) cho các ô nhập liệu, trường form.
  + `panel`: `20px` (`1.25rem`) cho các container nổi, modal và popup.
  + `pill`: `9999px` cho các nút bấm dạng viên thuốc, capsule badge.
- Đổ bóng trong `theme.extend.boxShadow`:
  + `card-dark`: Đổ bóng nổi khối sâu cho thẻ Dark Tech trên nền tối (`0 8px 32px 0 rgba(0, 0, 0, 0.37)`).
  + `panel-dark`: Đổ bóng đa tầng kèm viền kính mờ (`0 20px 50px rgba(0, 0, 0, 0.5)`).
  + `glow-cyan`: Hiệu ứng phát sáng màu cyan đặc trưng (`0 0 20px rgba(6, 182, 212, 0.35)`).
  + `glow-primary-subtle`: Hiệu ứng viền sáng xanh nhẹ cho card khi hover (`0 0 15px rgba(0, 82, 255, 0.25)`).

#### Scenario: Sử dụng border-radius mở rộng cho form và panel
- **WHEN** một phần tử input sử dụng class `rounded-input` hoặc một khung panel sử dụng `rounded-panel`
- **THEN** phần tử SHALL có bán kính bo góc tương ứng là `10px` và `20px`.

#### Scenario: Hiệu ứng ánh sáng nổi khối Dark Tech
- **WHEN** một thẻ giao diện sử dụng class `shadow-card-dark` hoặc `hover:shadow-glow-primary-subtle`
- **THEN** trình duyệt SHALL hiển thị chính xác hiệu ứng shadow nổi khối và ánh sáng viền xanh công nghệ trên nền tối.

---

### Requirement: Thang Chữ Semantic Typography Scale Chuẩn Figma
Hệ thống SHALL định nghĩa đầy đủ các token kích thước chữ semantic trong `theme.extend.fontSize` phản ánh đúng hệ thống phân cấp font của thiết kế Figma:
- `h1`: `['38px', { lineHeight: '46px', letterSpacing: '-0.01em', fontWeight: '700' }]` (tiêu đề Hero & Section cấp 1)
- `h2`: `['28px', { lineHeight: '36px', letterSpacing: '0', fontWeight: '700' }]` (tiêu đề đề mục bài viết và bước quy trình)
- `h3`: `['20px', { lineHeight: '28px', letterSpacing: '0', fontWeight: '600' }]` (tiêu đề phân nhóm card và khối phụ)
- `body`: `['16px', { lineHeight: '26px', letterSpacing: '0' }]` (nội dung đoạn văn chính)
- `caption`: `['14px', { lineHeight: '20px', letterSpacing: '0' }]` (chú thích, metadata thẻ)
- `caption-sm`: `['13px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '600' }]` (nhãn nhỏ, badge, nút bấm phụ)
- `caption-xs`: `['12px', { lineHeight: '16px', letterSpacing: '0', fontWeight: '600' }]` (chú thích nhỏ nhất)

#### Scenario: Sử dụng class typography semantic h1
- **WHEN** một phần tử áp dụng class `text-h1`
- **THEN** phần tử SHALL có kích thước `38px`, `line-height: 46px`, và `letter-spacing: -0.01em`.

#### Scenario: Sử dụng class typography semantic body
- **WHEN** một đoạn văn bản áp dụng class `text-body`
- **THEN** phần tử SHALL có kích thước `16px` và `line-height: 26px`.

---

### Requirement: Khử Nở Chữ Antialiasing Cấp Toàn Cục
Hệ thống SHALL áp dụng class `antialiased` trực tiếp vào thẻ `<body>` trong `resources/Frontend/views/app.blade.php`.

#### Scenario: Khử răng cưa và blooming trên nền tối
- **WHEN** trình duyệt tải trang giao diện người dùng trên bất kỳ trang nào của hệ thống
- **THEN** thẻ `<body>` SHALL có class `antialiased` và áp dụng `-webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale;` lên toàn bộ văn bản.

---

### Requirement: Bảng Màu Figma HH Maker Dark Tech
Hệ thống SHALL đồng bộ các mã màu Dark Tech chính thức từ Figma vào `theme.extend.colors`:
- Nền tối: `dark-bg: '#111322'`, `figma-bg: '#111322'`
- Thẻ Card: `dark-card: '#171B30'`, `figma-card: '#171B30'`
- Khối Hero: `dark-hero: '#0B0D19'`, `figma-hero: '#0B0D19'`
- Viền công nghệ: `dark-border: '#404968'`, `dark-border-light: '#5D6B98'`, `figma-border: '#404968'`, `figma-border-light: '#5D6B98'`
- Xanh thương hiệu: `primary.DEFAULT: '#2970FF'`, `brand-blue: '#2970FF'`, `figma-blue: '#2970FF'`
- Màu văn bản Figma: `text-white: '#FAFAFA'`, `text-secondary: '#B9C0D4'`, `text-blue: '#D1E0FF'`

#### Scenario: Áp dụng màu nền Dark Tech Figma
- **WHEN** phần tử sử dụng class `bg-dark-bg` hoặc `bg-figma-bg`
- **THEN** màu nền SHALL là `#111322`.

#### Scenario: Áp dụng màu viền công nghệ Figma
- **WHEN** phần tử sử dụng class `border-dark-border` hoặc `border-figma-border`
- **THEN** màu đường viền SHALL là `#404968`.
