# Proposal: Thiết lập và Đồng bộ Toàn diện Hệ thống Design Tokens từ Figma HH-Maker

## Why

Hệ thống giao diện của HH Maker đang bước vào giai đoạn chuẩn hóa Dark Tech / Cyber cao cấp theo thiết kế Figma HH-Maker (Page "A" - Node 2261:5076). Tuy nhiên, nền tảng styling hiện tại đang tồn tại một số điểm nghẽn và xung đột nghiêm trọng:
1. **Container và nhịp điệu khoảng cách (Layout Rhythm) chưa được khóa chuẩn**: Lớp `container` mặc định của Tailwind đang mở rộng theo breakpoint `xl: 1440px`, dẫn đến layout bị kéo dãn quá mức trên màn hình lớn thay vì neo chặt ở chuẩn `1240px` như thiết kế Figma. Đồng thời, dự án thiếu các token spacing có nhịp điệu chuyên biệt (`section`, `section-lg`, `gutter`), khiến các khoảng cách giữa các khối nội dung và lưới không đồng bộ.
2. **Semantic Colors thiếu alias và xung đột lớp chữ (Text Tokens)**: Các màu ngữ thái cốt lõi (`success`, `warning`, `error`) hiện chỉ có thang số từ 25–900 mà thiếu alias `DEFAULT` (ví dụ `bg-success` không hoạt động trực tiếp), và màu `info` hoàn toàn chưa được định nghĩa. Thêm vào đó, việc đặt `'text-primary': '#FFFFFF'` trong cấu hình colors dẫn đến xung đột với palette `primary` (`#0052FF`), sinh ra class dị dạng `text-text-primary` hoặc bị đè bởi `text-primary`. Ngoài ra, các mã màu cũ của theme FunkyStyle (`red-fks`, `black-fks`, `green-fks`, `blue-fks`) vẫn còn tồn đọng trong cấu hình.
3. **Phông chữ thừa gây phình tải CSS Bundle**: Tệp `resources/Frontend/scss/app.scss` đang chứa 7 khối `@font-face` không sử dụng (4 khối `Phudu` và 3 khối `BT-BeauSans`), gây lãng phí băng thông và tải CSS không cần thiết. Thang chữ (Typography Scale) Figma chưa được token hóa vào `theme.extend.fontSize` với đầy đủ `lineHeight` và `letterSpacing`.
4. **Thiếu thang bo góc mở rộng và Shadow Dark Mode**: Các token bo góc chỉ giới hạn ở `btn` (`8px`) và `card` (`16px`), thiếu các chuẩn `input` (`10px`), `panel` (`20px`), `pill` (`9999px`). Hệ thống đổ bóng (box-shadow) còn mỏng, thiếu các hiệu ứng nổi khối và ánh sáng viền (border glow) đặc trưng của Dark Tech.

Việc thiết lập và đồng bộ toàn diện hệ thống Design Tokens này sẽ giải quyết triệt để các xung đột cú pháp, tinh gọn mã nguồn SCSS, cố định layout nhịp nhàng và tạo nền tảng vững chắc cho tất cả các trang giao diện (Home, About, Listing, PDP).

## What Changes

1. **Khóa cứng Container & Thiết lập Layout Rhythm Spacing Tokens**:
   - Cấu hình lại `container` trong `resources/Frontend/tailwind.config.js`: Khóa max-width ở `1240px` trên desktop (`screens: { sm: '640px', md: '768px', lg: '1024px', xl: '1240px' }`), căn giữa `center: true`, padding responsive (`DEFAULT: '1rem'` cho mobile, `1.5rem` cho tablet/desktop).
   - Mở rộng `theme.extend.spacing`: Bổ sung `section: '5rem'` (`80px`), `section-lg: '7.5rem'` (`120px`), `gutter: '1.5rem'` (`24px`).
2. **Chuẩn hóa Semantic Colors & Phân tách Text Tokens**:
   - Bổ sung alias `DEFAULT` cho semantic status colors: `success` (`DEFAULT: '#12B76A'`), `warning` (`DEFAULT: '#F79009'`), `error` (`DEFAULT: '#F04438'`), và bổ sung palette `info` đầy đủ với `DEFAULT: '#2E90FA'`.
   - Phân tách cấu hình màu chữ: Định nghĩa nhóm text tokens rõ ràng (`text-white`, `text-secondary: '#94A3B8'`, `text-muted: '#64748B'`, `text-dim: '#475467'`) dưới dạng semantic tokens hoặc textColor chuyên biệt, loại bỏ xung đột giữa `text-primary` (`#0052FF`) và màu chữ trắng mặc định.
   - Lập lộ trình cô lập và đánh dấu deprecate các mã màu FunkyStyle (`red-fks`, `black-fks`, `green-fks`, `blue-fks`).
3. **Dọn dẹp `@font-face` thừa & Token hóa Typography Scale**:
   - Dỡ bỏ hoàn toàn 7 khối `@font-face` không sử dụng trong `resources/Frontend/scss/app.scss` (4 khối `Phudu` và 3 khối `BT-BeauSans`), chỉ giữ lại font phục vụ Dark Tech (`VCR_OSD_MONO` và font hệ thống `Be Vietnam Pro` / `Inter`).
   - Mở rộng `theme.extend.fontSize` trong `resources/Frontend/tailwind.config.js` với thang kích thước từ Figma (display-2xl, display-xl, display-lg, display-md, display-sm, text-xl, text-lg, text-md, text-sm, text-xs) đi kèm đầy đủ `lineHeight` và `letterSpacing`.
4. **Mở rộng Border Radius & Dark Mode Shadows / Glows**:
   - Bổ sung vào `theme.extend.borderRadius`: `input: '10px'`, `panel: '20px'`, `pill: '9999px'`.
   - Bổ sung các tokens `boxShadow` Dark Tech: `card-dark`, `panel-dark`, `glow-cyan`, `glow-primary-subtle`, và hiệu ứng đổ bóng viền đa lớp cho các thẻ công nghệ.

## Capabilities

### New Capabilities
- `design-tokens`: Đặc tả toàn diện hệ thống Design Tokens (Container & Spacing Rhythm, Semantic Colors, Typography Scale, Extended Radii, Dark Mode Shadows & Glows) và tối ưu hóa CSS bundle cho HH-Maker.

### Modified Capabilities
<!-- Không có capability hiện hữu nào bị thay đổi requirements -->

## Impact

- **Cấu hình Tailwind (`resources/Frontend/tailwind.config.js`)**: Cập nhật và mở rộng `container`, `spacing`, `colors`, `fontSize`, `borderRadius`, `boxShadow`.
- **Tệp SCSS chính (`resources/Frontend/scss/app.scss`)**: Loại bỏ 7 khối `@font-face` thừa (`Phudu` và `BT-BeauSans`), giảm dung lượng tải CSS bundle.
- **Tính tương thích**: Toàn bộ thay đổi là mở rộng (additive) và tương thích ngược (backward compatible) với các tiện ích Tailwind hiện có.
