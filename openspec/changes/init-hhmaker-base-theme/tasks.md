# Tasks: Khởi tạo Base Tokens & Theme cho HH Maker

## 1. Planning & OpenSpec Setup

- [x] 1.1 Khởi tạo change `init-hhmaker-base-theme` và sinh đầy đủ các tài liệu OpenSpec (`proposal.md`, `design.md`, `tasks.md`, `specs/base-theme/spec.md`).

## 2. Design Tokens & Tailwind Configuration

- [x] 2.1 Cập nhật bảng màu `primary` trong `resources/Frontend/tailwind.config.js`: DEFAULT `#0052FF`, hover `#155EEF`, active `#0040C1` cùng dải sắc độ 50-900.
- [x] 2.2 Bổ sung bảng màu Dark Tech trong `resources/Frontend/tailwind.config.js`: `dark-bg` (`#080C14`, `#0B0F19`), `dark-surface` (`#141C2E`), `dark-card` (`#182238`), cùng mã màu text `text-primary` (`#FFFFFF`), `text-secondary` (`#94A3B8`), `text-muted` (`#64748B`).
- [x] 2.3 Cập nhật `fontFamily`: Thay thế font cũ (`Phudu`, `Quicksand`) bằng font sans hình học hiện đại (`'Be Vietnam Pro'`, `'Inter'`, `sans-serif`).
- [x] 2.4 Bổ sung radius tokens (`rounded-btn`: `8px`, `rounded-card`: `16px`) và boxShadow glow (`glow-primary`: `0 4px 14px rgba(0, 82, 255, 0.4)`).

## 3. SCSS Cleanup & Component Standardization

- [x] 3.1 Dọn dẹp tàn dư màu cũ (`#8F7153`, `red-fks`, `green-fks`) trong `resources/Frontend/scss/customize/button.scss`.
- [x] 3.2 Chuẩn hóa `.btn` và `.btn-primary` trong `button.scss` với bo góc `8px`, nền `#0052FF`, hover `#155EEF` kèm hiệu ứng glow công nghệ `0 4px 14px rgba(0, 82, 255, 0.4)`.
- [x] 3.3 Dọn dẹp scrollbar và style cũ trong `resources/Frontend/scss/app.scss` đồng bộ tone Dark Tech & Electric Blue.

## 4. Verification & QA

- [x] 4.1 Chạy lệnh kiểm thử biên dịch `yarn frontend:build` (Client & SSR) đạt 100% PASS không lỗi CSS/JS.
- [x] 4.2 Cập nhật trạng thái hoàn tất `[x]` trong `tasks.md` và kiểm tra lại qua `openspec list`.
