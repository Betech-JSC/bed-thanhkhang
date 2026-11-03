# Design: Chuẩn Hóa Hệ Thống Typography Đồng Bộ Theo Figma

## Context
Xem chi tiết tại `proposal.md` và `specs/design-tokens/spec.md`.  
Dự án sử dụng Tailwind CSS 3.2.7 kết hợp Vite 3 và Vue 3. Trong `resources/Frontend/tailwind.config.js`, các phông chữ và kích thước chữ tiêu đề đang thiếu các nấc đo chính của Figma HH Maker (Canvas `2261:5076`): `38px` (H1/H2), `32px` (CTA banner), `28px` (tiêu đề bước/bài viết), `24px` (card), `42px` (số quy trình).

## Goals / Non-Goals

**Goals:**
- Cập nhật font-family ưu tiên `'Inter'` trước `'Be Vietnam Pro'`.
- Bổ sung 5 typography tokens chuẩn hóa vào `theme.extend.fontSize` trong `resources/Frontend/tailwind.config.js`.
- Rà soát và chuyển các class arbitrary hoặc lệch mắt (`text-[40px]`, `text-[42px]`) về `text-display-hero` (`38px`).
- Đảm bảo 100% build xanh (`yarn frontend:build`) và Playwright test xác thực typography.

**Non-Goals:**
- Không thay đổi cấu trúc bố cục, DOM layout hay nội dung văn bản của các trang.
- Không can thiệp vào logic backend Laravel hay cơ sở dữ liệu.

## Decisions

### 1. Quyết định đặt `'Inter'` làm Primary Sans & Display
- **Lý do**: Kiểm toán Figma MCP (`get_figma_data`) cho thấy 99.5% text nodes trên 8 trang đều sử dụng font `'Inter'`. Thẻ `<link>` Google Fonts trong `app.blade.php` đã nạp đầy đủ weights 300, 400, 500, 600, 700 của Inter.
- **Phương án thay thế**: Giữ `Be Vietnam Pro` làm mặc định -> Bị loại bỏ vì gây lệch nhịp thị giác so với thiết kế Figma.

### 2. Quyết định định danh semantic tokens cho các cỡ chữ Figma
- `display-hero`: `['38px', { lineHeight: '46px', letterSpacing: '-0.01em' }]`
- `display-banner`: `['32px', { lineHeight: '38px', letterSpacing: '-0.01em' }]`
- `display-section`: `['28px', { lineHeight: '34px', letterSpacing: '0' }]`
- `display-card`: `['24px', { lineHeight: '30px', letterSpacing: '0' }]`
- `display-step`: `['42px', { lineHeight: '51px', letterSpacing: '0' }]`
- **Lý do**: Giúp lập trình viên gọi trực tiếp `text-display-hero`, `text-display-banner`... mà không phải viết arbitrary classes `text-[38px]`, `text-[32px]`.

## Risks / Trade-offs

- **[Risk]** Đổi font-family chính có thể làm thay đổi nhẹ độ rộng văn bản trên các nút bấm.
  - **Mitigation**: Kiểm tra trực quan trên các viewport desktop 1440px và mobile qua Playwright để đảm bảo không bị vỡ dòng hay tràn container.
