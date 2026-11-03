# Tasks: Chuẩn Hóa Design Tokens, Typography Scale và Grid Container Theo Figma

## 1. Khởi Tạo OpenSpec Change
- [x] 1.1 Khởi tạo thư mục OpenSpec change `sync-figma-design-tokens-and-typography` gồm `proposal.md`, `specs/design-tokens/spec.md`, `design.md`, `tasks.md`.
- [x] 1.2 Chạy `npx openspec validate sync-figma-design-tokens-and-typography` xác nhận hợp lệ.

## 2. Cập Nhật Cấu Hình Tailwind CSS (resources/Frontend/tailwind.config.js)
- [x] 2.1 Cập nhật `container.screens`: `sm: '640px'`, `md: '768px'`, `lg: '1024px'`, `xl: '1280px'`, `'2xl': '1440px'`.
- [x] 2.2 Bổ sung các tiện ích `maxWidth`: `canvas: '1440px'`, `content: '1152px'`, `hero: '1279px'`.
- [x] 2.3 Bổ sung thang font ngữ nghĩa `fontSize`: `h1`, `h2`, `h3`, `body`, `caption`, `caption-sm`, `caption-xs`.
- [x] 2.4 Cập nhật bảng màu chuẩn Figma trong `colors`: `#111322` (Dark BG), `#171B30` (Card), `#0B0D19` (Hero Card), `#404968` & `#5D6B98` (Border), `#2970FF` (Primary Blue), và các màu chữ `#FAFAFA`, `#B9C0D4`, `#D1E0FF`.
- [x] 2.5 Đảm bảo `fontFamily` giữ ưu tiên `'Inter'` làm font chính trước `'Be Vietnam Pro'`.

## 3. Cập Nhật Layout Shell (resources/Frontend/views/app.blade.php)
- [x] 3.1 Thêm class `antialiased` vào thẻ `<body>` trong `resources/Frontend/views/app.blade.php` để khử hiện tượng nở chữ (font blooming) trên nền tối `#111322` trên toàn bộ website.

## 4. Kiểm Tra Tương Thích Header.vue & HeroSection.vue
- [x] 4.1 Kiểm tra `Header.vue` đảm bảo ăn khớp với cấu hình max-width và container mới.
- [x] 4.2 Kiểm tra `HeroSection.vue` đảm bảo ăn khớp với cấu hình max-width và container mới.

## 5. Biên Dịch Frontend & Kiểm Thử Playwright
- [x] 5.1 Chạy `yarn frontend:build` biên dịch frontend với exit code 0.
- [x] 5.2 Chạy test Playwright `tests/e2e/header-strict-figma.spec.ts` kiểm chứng layout, typography, antialiased và responsive.
- [x] 5.3 Chụp ảnh màn hình kiểm chứng kết quả.
- [x] 5.4 Đánh dấu hoàn tất toàn bộ các task `[x]` và báo cáo chi tiết cho caller agent.
