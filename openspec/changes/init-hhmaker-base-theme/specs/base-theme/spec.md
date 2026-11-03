# Spec Delta

## Purpose

Thiết lập hệ thống Design Tokens và Base Styles cơ bản cho HH Maker, làm chuẩn mực nhận diện thương hiệu công nghệ (Dark Tech, Electric Blue `#0052FF`, Geometric Sans `'Be Vietnam Pro'`, `'Inter'`) và loại bỏ hoàn toàn các tàn dư màu sắc/phông chữ cũ.

## ADDED Requirements

### Requirement: Electric Blue Primary Palette Configuration
Hệ thống giao diện Frontend SHALL cấu hình bảng màu `primary` dựa trên mã màu Electric Blue `#0052FF`, hỗ trợ trạng thái hover `#155EEF` và active `#0040C1` trong Tailwind CSS.

#### Scenario: Utility class uses primary color
- **WHEN** Component sử dụng class `bg-primary`, `text-primary`, `border-primary` hoặc `hover:bg-primary-hover`
- **THEN** Trình biên dịch Tailwind CSS ánh xạ chính xác sang mã màu `#0052FF` cho DEFAULT, `#155EEF` cho hover và `#0040C1` cho active

### Requirement: Dark Tech Surface & Typography Tokens
Hệ thống Frontend SHALL định nghĩa đầy đủ bảng màu Dark Tech (`dark-bg`, `dark-surface`, `dark-card`) cùng các cấp bậc text color (`text-primary`, `text-secondary`, `text-muted`) trong cấu hình theme.

#### Scenario: Dark Tech layout background and cards
- **WHEN** Giao diện Dark Tech áp dụng các class `bg-dark-bg`, `bg-dark-surface`, hoặc `bg-dark-card`
- **THEN** Trình duyệt render màu nền tương ứng `#080C14` / `#0B0F19`, `#141C2E`, và `#182238`

#### Scenario: Dark Tech typography rendering
- **WHEN** Phần tử hiển thị văn bản sử dụng `text-text-primary`, `text-text-secondary`, hoặc `text-text-muted`
- **THEN** Văn bản hiển thị với các màu sắc chuẩn `#FFFFFF`, `#94A3B8`, và `#64748B`

### Requirement: Geometric Sans Typography Standard
Hệ thống Frontend SHALL thiết lập font sans mặc định là phông chữ sans hình học hiện đại gồm `'Be Vietnam Pro'` và `'Inter'`, thay thế cho các font cũ (`Phudu`, `Quicksand`).

#### Scenario: Text rendered with default font family
- **WHEN** Trang hoặc component hiển thị text mà không chỉ định font đặc thù
- **THEN** Trình duyệt ưu tiên sử dụng `'Be Vietnam Pro'`, sau đó là `'Inter'` và fallback `sans-serif`

### Requirement: Base Button Component Standardization
Hệ thống SCSS SHALL chuẩn hóa class `.btn` và `.btn-primary` với bo góc 8px, nền `#0052FF`, hiệu ứng hover `#155EEF` kèm hiệu ứng glow công nghệ `box-shadow: 0 4px 14px rgba(0, 82, 255, 0.4)`, đồng thời loại bỏ các class và biến màu tàn dư `red-fks` và `green-fks`.

#### Scenario: Primary button interaction
- **WHEN** Người dùng di chuột (hover) vào phần tử có class `.btn.btn-primary`
- **THEN** Nút bấm chuyển sang màu nền `#155EEF` và hiển thị viền ánh sáng xanh `0 4px 14px rgba(0, 82, 255, 0.4)`
