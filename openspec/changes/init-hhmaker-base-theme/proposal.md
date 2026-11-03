# Proposal: Khởi tạo Base Tokens & Theme cho HH Maker

## Why
Dự án HH Maker cần một nền tảng Design Tokens và hệ thống Styles cơ sở (Base Theme) đồng nhất trước khi tiến hành xây dựng và nâng cấp 4 trang giao diện chính: Home, Product (Listing), PDP (Product Detail Page), và About Us.
Trước đây, mã nguồn kế thừa chứa các tàn dư màu sắc của thương hiệu cũ (màu vàng nâu `#8F7153`, đỏ `red-fks: #D34000`, xanh chuối `green-fks: #CBFF00`), cũng như các phông chữ display không phù hợp định vị thương hiệu công nghệ cao (`Phudu`, `Quicksand`).
Việc thiết lập Base Tokens chuẩn mực theo nhận diện thương hiệu HH Maker (Dark Tech / Cyber hiện đại, tone màu chủ đạo Electric Blue `#0052FF`, Dark Tech Surfaces, phông chữ Sans hình học `'Be Vietnam Pro'`, `'Inter'`, `'sans-serif'`) sẽ đảm bảo tính nhất quán trên toàn bộ các components, ngăn ngừa xung đột CSS, và tạo tiền đề vững chắc cho việc dựng 4 trang theo chuẩn Quiet & High-Trust UI.

## What Changes
1. **Design Tokens & Tailwind Configuration (`resources/Frontend/tailwind.config.js`)**:
   - Cập nhật palette `primary`:
     - DEFAULT: `#0052FF`
     - hover: `#155EEF`
     - active: `#0040C1`
     - Dải sắc độ bổ sung từ 50 đến 900 phù hợp với Electric Blue.
   - Bổ sung bảng màu Dark Tech Surfaces & Backgrounds:
     - `dark-bg`: `#080C14` / `#0B0F19`
     - `dark-surface`: `#141C2E`
     - `dark-card`: `#182238`
   - Cập nhật Typography & Text colors:
     - `text-primary`: `#FFFFFF`
     - `text-secondary`: `#94A3B8`
     - `text-muted`: `#64748B`
   - Chuẩn hóa `fontFamily`:
     - Thay thế font cũ (`Phudu`, `Quicksand`) bằng font sans hình học hiện đại: `'Be Vietnam Pro'`, `'Inter'`, `sans-serif`.
   - Bổ sung border-radius tokens:
     - `rounded-btn`: `8px`
     - `rounded-card`: `16px`
2. **SCSS Core & Customization Cleanup (`resources/Frontend/scss/`)**:
   - Dọn dẹp tàn dư màu cũ (`#8F7153`, `red-fks`, `green-fks`, scrollbar đỏ cam) trong `customize/button.scss` và `app.scss`.
   - Chuẩn hóa nút bấm `.btn` và `.btn-primary`:
     - Bo góc `8px` (`rounded-[8px]`).
     - Background: `#0052FF`, hover: `#155EEF`.
     - Glow hiệu ứng công nghệ: `box-shadow: 0 4px 14px rgba(0, 82, 255, 0.4)`.
3. **Build & Quality Assurance**:
   - Kiểm tra build tĩnh và SSR qua `yarn frontend:build` đảm bảo biên dịch thành công 100% không phát sinh lỗi.

## Capabilities

### New Capabilities
- `base-theme`: Đặc tả hệ thống Design Tokens (Colors, Typography, Radii, Shadows) và Base Component Styles chuẩn nhận diện công nghệ HH Maker trong Frontend.

### Modified Capabilities
<!-- Không có capability hiện hữu nào cần sửa đổi -->

## Impact
- `resources/Frontend/tailwind.config.js`: Cập nhật bảng màu, font chữ, border radius và shadow glow.
- `resources/Frontend/scss/customize/button.scss`: Dọn sạch styles `red-fks`, `green-fks` cũ; chuẩn hóa `.btn-primary` và các class button.
- `resources/Frontend/scss/app.scss`: Cập nhật scrollbar colors, chuẩn hóa font stack.
