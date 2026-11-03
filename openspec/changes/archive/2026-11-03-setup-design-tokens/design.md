# Design: Hệ thống Design Tokens và Tối ưu hóa CSS Bundle

## Context

Dự án HH Maker đang chuyển đổi giao diện sang phong cách Dark Tech / Cyber cao cấp theo thiết kế Figma (Page "A" - Node 2261:5076). Để xem chi tiết lý do và mục tiêu, tham khảo `proposal.md` và các đặc tả hành vi tại `specs/design-tokens/spec.md`.
Hiện tại, tệp cấu hình `resources/Frontend/tailwind.config.js` và tệp SCSS chính `resources/Frontend/scss/app.scss` chứa các thiết lập cơ bản nhưng chưa hoàn thiện:
- Breakpoint `xl` là `1440px`, khiến class `.container` bị bung rộng đến 1440px trên màn hình lớn thay vì neo chặt ở `1240px`.
- Thiếu các tokens khoảng cách nhịp điệu (`spacing.section`, `spacing.section-lg`, `spacing.gutter`).
- Các nhóm màu trạng thái `success`, `warning`, `error` thiếu alias `DEFAULT`, và palette `info` chưa có.
- Có sự chồng chéo giữa palette thương hiệu `primary` (`#0052FF`) và thuộc tính `'text-primary': '#FFFFFF'`.
- Tồn đọng 7 khối `@font-face` không sử dụng (`Phudu` và `BT-BeauSans`) gây phình to CSS bundle.
- Chưa có cấu hình `theme.extend.fontSize` để token hóa thang chữ chuẩn Figma.
- Thiếu các tokens bo góc (`input: 10px`, `panel: 20px`, `pill: 9999px`) và các bóng đổ viền sáng Dark Tech.

## Goals / Non-Goals

**Goals:**
- Khóa cứng `container` với max-width `1240px` và padding responsive (`1rem` mobile, `1.5rem` tablet/desktop).
- Mở rộng `spacing` nhịp điệu: `section` (`80px`), `section-lg` (`120px`), `gutter` (`24px`).
- Chuẩn hóa toàn bộ Semantic Colors với alias `DEFAULT` (`success`, `warning`, `error`, `info`).
- Phân định rõ ràng hệ thống Text Tokens (`text-white`, `text-secondary`, `text-muted`, `text-dim`), tránh đè class `text-primary`.
- Dọn sạch 7 khối `@font-face` thừa trong `app.scss`, tinh gọn CSS bundle.
- Token hóa thang chữ Figma vào `fontSize` với đầy đủ `lineHeight` và `letterSpacing`.
- Bổ sung `borderRadius` (`input`, `panel`, `pill`) và các `boxShadow` Dark Tech / Glow.
- Đảm bảo biên dịch `yarn frontend:build` xanh 100% và không phá vỡ các component hiện hữu.

**Non-Goals:**
- Không sửa đổi mã nguồn hoặc tái cấu trúc các Vue component trong phạm vi change này.
- Không xóa bỏ vĩnh viễn các class màu legacy FunkyStyle (`*-fks`) trong codebase ngay lập tức mà chỉ cô lập và đánh dấu deprecate nhằm đảm bảo an toàn backward compatibility.
- Không thay đổi cấu hình Tailwind của khu vực quản trị CMS Backend (`packages/bed-package-essentials/core/resources/Backend/tailwind.config.js`).

## Decisions

### 1. Khóa Container Max-Width qua `container.screens` thay vì thay đổi breakpoint toàn cục
- **Quyết định**: Khai báo cấu hình container trong `theme.extend.container`:
  ```javascript
  container: {
      center: true,
      padding: {
          DEFAULT: '1rem',
          sm: '1rem',
          md: '1.5rem',
          lg: '1.5rem',
          xl: '1.5rem',
      },
      screens: {
          sm: '640px',
          md: '768px',
          lg: '1024px',
          xl: '1240px',
      },
  }
  ```
- **Lý do**: Giữ nguyên breakpoint responsive `xl: '1440px'` cho các utility responsive (`xl:grid-cols-4`, `xl:flex`...), nhưng riêng tiện ích `.container` sẽ dừng độ co dãn tại đúng `1240px`, khớp 100% với canvas Figma Node 2261:5076.
- **Phương án thay thế cân nhắc**: Đổi trực tiếp `screens.xl = '1240px'` trong root config. Bị từ chối vì sẽ làm sai lệch điểm ngắt responsive của các màn hình lớn khác trên toàn bộ dự án.

### 2. Bổ sung `DEFAULT` alias và hoàn thiện Semantic Color Palettes
- **Quyết định**: Bổ sung `DEFAULT` trỏ vào shade 500 cho các nhóm màu ngữ thái:
  - `success`: `{ DEFAULT: '#12B76A', ... }`
  - `warning`: `{ DEFAULT: '#F79009', ... }`
  - `error`: `{ DEFAULT: '#F04438', ... }`
  - `info`: Bổ sung toàn bộ dải màu từ 25 đến 900 với `{ DEFAULT: '#2E90FA', ... }`
- **Lý do**: Cho phép sử dụng cú pháp gọn gàng `bg-success`, `text-error`, `border-warning`, `bg-info` cho badge, alert, toast mà không bắt buộc phải nhớ mã `-500`.
- **Phương án thay thế cân nhắc**: Chỉ định nghĩa một mã màu hex duy nhất cho mỗi semantic status. Bị từ chối vì giao diện Dark Tech cần cả các shade nhạt (shade 25, 50, 100) cho background mờ kính và shade đậm (shade 700, 800) cho viền tương phản.

### 3. Chuẩn hóa Text Tokens và tách biệt khỏi Primary Color
- **Quyết định**:
  - Giữ palette `primary` cho màu thương hiệu Electric Blue (`#0052FF`).
  - Định nghĩa nhóm màu chữ giao diện Dark Tech rõ ràng trong `theme.extend.colors`:
    ```javascript
    'text-white': '#FFFFFF',
    'text-secondary': '#94A3B8',
    'text-muted': '#64748B',
    'text-dim': '#475467',
    ```
  - Loại bỏ định nghĩa nhập nhằng `'text-primary': '#FFFFFF'` gây đè lên `primary.DEFAULT`.
- **Lý do**: Trong Tailwind CSS, nếu đặt `'text-primary': '#FFFFFF'` trong colors, Tailwind sẽ sinh class `text-text-primary` hoặc gây xung đột phân giải với `text-primary` (Electric Blue). Việc chuẩn hóa giúp `text-primary` đại diện cho màu xanh thương hiệu và `text-white` / `text-secondary` đại diện cho các mức độ tương phản chữ.

### 4. Dỡ bỏ 7 khối `@font-face` không sử dụng trong `app.scss`
- **Quyết định**: Xóa hoàn toàn 4 khối `@font-face` của font `Phudu` (Regular, Medium, SemiBold, Bold) và 3 khối `@font-face` của font `BT-BeauSans` (Regular, Medium, Bold) khỏi `resources/Frontend/scss/app.scss`. Đồng thời dọn alias `beau` khỏi `fontFamily` trong Tailwind config.
- **Lý do**: HH-Maker sử dụng font sans hình học hiện đại (`Be Vietnam Pro` và `Inter`) làm font chính (`font-display`, `font-sans`) và `VCR_OSD_MONO` cho các chi tiết số hiệu công nghệ (`font-mono`). `Phudu` và `BT-BeauSans` là tàn dư cũ, không còn xuất hiện trong thiết kế Figma, việc loại bỏ giúp giảm đáng kể thời gian tải trang và kích thước CSS.

### 5. Token hóa Figma Typography Scale theo chuẩn Tailwind Tuple
- **Quyết định**: Định nghĩa trong `theme.extend.fontSize`:
  ```javascript
  fontSize: {
      'display-2xl': ['4.5rem', { lineHeight: '5.625rem', letterSpacing: '-0.02em' }], // 72px / 90px
      'display-xl': ['3.75rem', { lineHeight: '4.5rem', letterSpacing: '-0.02em' }],   // 60px / 72px
      'display-lg': ['3rem', { lineHeight: '3.75rem', letterSpacing: '-0.02em' }],      // 48px / 60px
      'display-md': ['2.25rem', { lineHeight: '2.75rem', letterSpacing: '-0.02em' }],   // 36px / 44px
      'display-sm': ['1.875rem', { lineHeight: '2.375rem', letterSpacing: '0' }],       // 30px / 38px
      'text-xl': ['1.25rem', { lineHeight: '1.875rem', letterSpacing: '0' }],           // 20px / 30px
      'text-lg': ['1.125rem', { lineHeight: '1.75rem', letterSpacing: '0' }],           // 18px / 28px
      'text-md': ['1rem', { lineHeight: '1.5rem', letterSpacing: '0' }],                 // 16px / 24px
      'text-sm': ['0.875rem', { lineHeight: '1.25rem', letterSpacing: '0' }],           // 14px / 20px
      'text-xs': ['0.75rem', { lineHeight: '1.125rem', letterSpacing: '0' }],           // 12px / 18px
  }
  ```
- **Lý do**: Đồng bộ chặt chẽ với hệ thống chữ của Figma. Khi áp dụng class `text-display-lg`, trình duyệt tự động nhận cả `font-size`, `line-height` và `letter-spacing`, ngăn ngừa việc lệch nhịp dòng văn bản.

### 6. Mở rộng Border Radius & Dark Mode Shadows / Glows
- **Quyết định**:
  - `theme.extend.borderRadius`: bổ sung `input: '10px'`, `panel: '20px'`, `pill: '9999px'`.
  - `theme.extend.boxShadow`: bổ sung các hiệu ứng đổ bóng cho giao diện nền tối:
    + `card-dark`: `0 8px 32px 0 rgba(0, 0, 0, 0.37)`
    + `panel-dark`: `0 20px 50px rgba(0, 0, 0, 0.5)`
    + `glow-cyan`: `0 0 20px rgba(6, 182, 212, 0.35)`
    + `glow-primary-subtle`: `0 0 15px rgba(0, 82, 255, 0.25)`

## Risks / Trade-offs

- **[Risk: Component hiện hữu có thể bị co hẹp nếu trước đó trông cậy vào container 1440px]**  
  $\rightarrow$ *Mitigation*: Thiết kế Figma HH-Maker chuẩn hóa độ rộng nội dung tối đa ở 1200px – 1240px. Việc khóa container 1240px giúp bảo vệ bố cục khỏi bị biến dạng trên màn hình siêu rộng (2K/4K). Chạy kiểm thử Playwright sau khi cấu hình để nghiệm thu trực quan.
- **[Risk: Lỗi biên dịch nếu một component vô tình gọi font `font-beau`]**  
  $\rightarrow$ *Mitigation*: Quét toàn bộ thư mục `resources/Frontend/` để xác nhận không còn file nào sử dụng `font-beau`. Nếu có, chuyển đổi sang `font-display` hoặc `font-sans`.
- **[Risk: Xung đột khi dọn dẹp các mã màu `fks`]**  
  $\rightarrow$ *Mitigation*: Không xóa các key `fks` ngay lập tức mà giữ lại dưới dạng deprecated alias để bảo đảm an toàn cho các trang chưa migrate, chuyển dần sang các token chuẩn `primary`, `error`, `dark-surface`.

## Migration Plan

1. **Bước 1**: Cập nhật tệp cấu hình `resources/Frontend/tailwind.config.js` với các tokens mới (Container, Spacing, Semantic Colors, Text Tokens, Typography Scale, Border Radius, Dark Mode Shadows).
2. **Bước 2**: Dọn dẹp 7 khối `@font-face` không sử dụng trong `resources/Frontend/scss/app.scss`.
3. **Bước 3**: Kiểm tra toàn bộ mã nguồn Frontend để đảm bảo không còn tàn dư tham chiếu `font-beau` hay `text-text-primary`.
4. **Bước 4**: Chạy lệnh build tĩnh `yarn frontend:build` để xác nhận biên dịch thành công 100%.
5. **Bước 5**: Chạy kiểm thử tự động Playwright E2E (`npx playwright test`) để kiểm chứng giao diện không bị gián đoạn hay vỡ layout.
