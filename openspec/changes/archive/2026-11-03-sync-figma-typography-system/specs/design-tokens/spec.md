# Spec Delta: design-tokens

## MODIFIED Requirements

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
