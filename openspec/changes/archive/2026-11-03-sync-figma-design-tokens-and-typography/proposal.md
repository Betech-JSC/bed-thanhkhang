# Proposal: Chuẩn Hóa Design Tokens, Typography Scale và Grid Container Theo Figma

## Why
Trong quá trình chuyển đổi giao diện sang phong cách Dark Tech theo bản vẽ Figma của HH Maker (các Node `2352:10717` - Header menu & Global Canvas, `2261:5474` - Homepage Hero & Container, `2367:2088` - Typography & Cards), hệ thống tokens nền tảng cần được củng cố đồng nhất ở cấp cấu hình Tailwind CSS và Blade Layout Shell.

Cụ thể:
1. **Container Grid**: Bố cục container trên desktop lớn cần nâng cấp theo đúng chuẩn Figma Canvas 1440px (`xl: 1280px`, `2xl: 1440px`) cùng các tiện ích giới hạn bề rộng nội dung chuẩn xác: `max-w-canvas` (1440px), `max-w-content` (1152px), `max-w-hero` (1279px).
2. **Typography Scale**: Chuẩn hóa toàn bộ hệ thống semantic font tokens: H1 (38px/46px Bold), H2 (28px/36px Bold), H3 (20px/28px SemiBold), Body (16px/26px Regular), Caption (14px/20px Regular, 13px/16px SemiBold), Caption XS (12px/16px SemiBold).
3. **Font Rendering & Antialiasing**: Nền tối `#111322` trên toàn bộ website cần được kích hoạt lớp `antialiased` ở cấp thẻ `<body>` toàn cục trong `resources/Frontend/views/app.blade.php` để triệt tiêu hoàn toàn hiện tượng font blooming (nở viền chữ) trên Windows Chrome.
4. **Figma Color Palette**: Chuẩn hóa các mã màu cốt lõi: `#111322` (Dark BG), `#171B30` (Card), `#0B0D19` (Hero Card), `#404968` & `#5D6B98` (Border), `#2970FF` (Primary Blue), cùng các màu chữ `#FAFAFA`, `#B9C0D4`, `#D1E0FF`.

## What Changes
1. **Cập nhật `resources/Frontend/tailwind.config.js`**:
   - Nâng cấp `container.screens`: `sm: '640px'`, `md: '768px'`, `lg: '1024px'`, `xl: '1280px'`, `'2xl': '1440px'`.
   - Bổ sung `theme.extend.maxWidth`: `canvas: '1440px'`, `content: '1152px'`, `hero: '1279px'`.
   - Bổ sung `theme.extend.fontSize`: semantic tokens `h1`, `h2`, `h3`, `body`, `caption`, `caption-sm`, `caption-xs` bên cạnh các display tokens hiện có.
   - Chuẩn hóa `theme.extend.colors`: `primary.DEFAULT: '#2970FF'`, `dark-bg: '#111322'`, `dark-card: '#171B30'`, `dark-hero: '#0B0D19'`, `dark-border: '#404968'`, `dark-border-light: '#5D6B98'`, và các alias `figma-*`.
   - Giữ vững `fontFamily.sans` và `fontFamily.display` ưu tiên `'Inter'` hàng đầu.
2. **Cập nhật `resources/Frontend/views/app.blade.php`**:
   - Thêm class `antialiased` vào thẻ `<body>` toàn cục.
3. **Kiểm tra tương thích**:
   - Xác thực `Header.vue` và `HeroSection.vue` tương thích hoàn toàn với container grid mới.
4. **Biên dịch & Test E2E**:
   - Chạy `yarn frontend:build` thành công.
   - Chạy test Playwright `tests/e2e/header-strict-figma.spec.ts` pass 100%.

## Capabilities

### New Capabilities
- `figma-design-tokens`: Bộ Design Tokens, Typography Scale và Grid Container chuẩn hóa 100% theo bản thiết kế Figma của HH Maker.

### Modified Capabilities
- `design-tokens`: Nâng cấp Container breakpoints lên 1280px/1440px và bổ sung các max-width utilities cùng bảng màu Figma chuẩn.

## Impact
- `resources/Frontend/tailwind.config.js`: Nâng cấp container, max-width, typography, colors.
- `resources/Frontend/views/app.blade.php`: Thêm class `antialiased` cho thẻ `<body>`.
- Không xóa file, không sửa database, an toàn tuyệt đối 100%.
