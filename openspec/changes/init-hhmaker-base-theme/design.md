# Design: Khởi tạo Base Tokens & Theme cho HH Maker

## Context
Dự án HH Maker cần một nền tảng Design System vững chắc để phục vụ việc tái thiết kế và xây dựng 4 trang chính (Home, Product, PDP, About Us).
Mã nguồn hiện tại kế thừa các biến màu, cấu hình Tailwind và file SCSS từ dự án mẫu FunkyStyle (`#8F7153`, `#D34000`, `#CBFF00`, các phông `Phudu`, `Quicksand`).
Để chuyển dịch sang nhận diện thương hiệu HH Maker theo phong cách Dark Tech / Cyber và Quiet & High-Trust UI, cần chuẩn hóa toàn bộ Design Tokens và Base Components Styles ở mức độ nền tảng (Phase 0).

## Goals / Non-Goals

**Goals:**
- Cập nhật dải màu `primary` sang Electric Blue (`#0052FF`, hover: `#155EEF`, active: `#0040C1`) trong Tailwind.
- Định nghĩa bảng màu Dark Tech: `dark-bg` (`#080C14` / `#0B0F19`), `dark-surface` (`#141C2E`), `dark-card` (`#182238`).
- Chuẩn hóa typography colors: `text-primary` (`#FFFFFF`), `text-secondary` (`#94A3B8`), `text-muted` (`#64748B`).
- Chuẩn hóa font families sang font sans hình học hiện đại (`'Be Vietnam Pro'`, `'Inter'`, `'sans-serif'`).
- Bổ sung tokens bo góc (`rounded-btn: 8px`, `rounded-card: 16px`) và shadow glow công nghệ (`0 4px 14px rgba(0, 82, 255, 0.4)`).
- Dọn dẹp tàn dư màu và class cũ trong `resources/Frontend/scss/customize/button.scss` và `app.scss`.
- Đảm bảo lệnh build `yarn frontend:build` thành công 100%.

**Non-Goals:**
- Không viết lại toàn bộ nội dung HTML/Vue của 4 trang (sẽ thực hiện trong các phase tiếp theo).
- Không can thiệp vào Backend hay Database Schema trong change này.

## Decisions

### Decision 1: Token Architecture trong `resources/Frontend/tailwind.config.js`
Thay vì định nghĩa rời rạc, các tokens được cấu hình trực tiếp vào `theme.extend` để tương thích hoàn toàn với hệ thống Tailwind utility classes:
- `colors.primary`:
  - `DEFAULT`: `#0052FF`
  - `hover`: `#155EEF`
  - `active`: `#0040C1`
  - Shades `50`: `#EFF4FF`, `100`: `#D1E0FF`, `200`: `#B2CCFF`, `300`: `#84ADFF`, `400`: `#528BFF`, `500`: `#2E68FF`, `600`: `#0052FF`, `700`: `#0040C1`, `800`: `#003194`, `900`: `#002266`
- `colors.dark`:
  - `bg`: `#080C14`
  - `bg-alt`: `#0B0F19`
  - `surface`: `#141C2E`
  - `surface-light`: `#1E2B45`
  - `card`: `#182238`
  - `border`: `#22314E`
  - `text-primary`: `#FFFFFF`
  - `text-secondary`: `#94A3B8`
  - `text-muted`: `#64748B`
- `fontFamily`:
  - `sans`: `['Be Vietnam Pro', 'Inter', 'ui-sans-serif', 'system-ui', 'sans-serif']`
  - `display`: `['Be Vietnam Pro', 'Inter', 'sans-serif']`
- `borderRadius`:
  - `btn`: `8px`
  - `card`: `16px`
- `boxShadow`:
  - `glow-primary`: `0 4px 14px rgba(0, 82, 255, 0.4)`
  - `glow-primary-lg`: `0 8px 24px rgba(0, 82, 255, 0.5)`

### Decision 2: Chuẩn hóa Base Button SCSS
Trong `resources/Frontend/scss/customize/button.scss`:
- Cập nhật `.btn` và `.btn-primary`:
  - Border radius: `8px` (`rounded-[8px]`).
  - Màu nền: `#0052FF`, border: `#0052FF`, text: `#FFFFFF`.
  - Hiệu ứng hover: nền `#155EEF`, border `#155EEF`, shadow glow `0 4px 14px rgba(0, 82, 255, 0.4)`.
  - Gỡ bỏ hoàn toàn `red-fks`, `green-fks`, và `#8F7153`.
- Chuẩn hóa scrollbar trong `app.scss`:
  - Thay màu đỏ cam `#D34000` / `#b23600` bằng tone Dark Tech & Electric Blue (`#1E2B45`, thumb: `#0052FF`).
