# Spec Delta: Design Tokens System

## Purpose

Cung cấp và chuẩn hóa toàn bộ hệ thống Design Tokens (Layout Spacing & Container, Semantic Colors, Typography Scale, Border Radii, Dark Mode Shadows & Glows) và tối ưu hóa CSS bundle theo chuẩn Figma HH-Maker Dark Tech (Node 2261:5076).

## ADDED Requirements

### Requirement: Khóa cứng Container Max-Width và Padding Responsive
Hệ thống SHALL giới hạn độ rộng tối đa (`max-width`) của lớp tiện ích `.container` ở mức `1240px` trên tất cả các màn hình desktop lớn (tránh bị kéo dãn đến `1440px`), đồng thời tự động căn giữa (`center: true`) và áp dụng padding responsive (`1rem` / `16px` cho mobile, `1.5rem` / `24px` cho tablet và desktop).

#### Scenario: Kiểm soát max-width của container trên Desktop
- **WHEN** một phần tử sử dụng class `.container` được hiển thị trên màn hình có kích thước viewport $\ge 1240\text{px}$ (bao gồm cả $1440\text{px}$)
- **THEN** phần tử SHALL có `max-width: 1240px` và được căn giữa tự động với margin hai bên bằng nhau.

#### Scenario: Padding responsive của container
- **WHEN** màn hình ở độ phân giải mobile ($< 768\text{px}$)
- **THEN** container SHALL có padding ngang là `1rem` (`16px`).
- **WHEN** màn hình ở độ phân giải tablet hoặc desktop ($\ge 768\text{px}$)
- **THEN** container SHALL có padding ngang là `1.5rem` (`24px`).

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

### Requirement: Tinh gọn Font-Face và Token hóa Thang chữ Typography Scale
Hệ thống SHALL dỡ bỏ hoàn toàn 7 khối `@font-face` không sử dụng (`Phudu` 4 khối và `BT-BeauSans` 3 khối) trong `resources/Frontend/scss/app.scss` để giảm tải kích thước CSS bundle. Đồng thời, hệ thống SHALL token hóa thang kích thước chữ chuẩn Figma vào `theme.extend.fontSize` bao gồm:
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
