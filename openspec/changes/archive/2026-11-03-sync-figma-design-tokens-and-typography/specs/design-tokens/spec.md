# Spec Delta: design-tokens

## Purpose
Nâng cấp và chuẩn hóa toàn diện hệ thống Design Tokens (Container Grid 1440px/1152px, Semantic Typography Scale, Bảng màu Figma Dark Tech và Antialiasing toàn cục) bám sát các Node thiết kế Figma chuẩn của HH Maker (Nodes `2352:10717`, `2261:5474`, `2367:2088`).

## MODIFIED Requirements

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

## ADDED Requirements

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
