# Proposal: Chuẩn Hóa Padding 80px Cho Các Khối Banner Còn Lại

## Why

Sau khi hoàn thành việc chuẩn hóa Universal Container và HeroSection trên Trang chủ theo hệ thống Design Tokens HH Maker Dark Tech, một số khối Banner và CTA tại các trang con (Quy trình - Process, Dịch vụ - Services) vẫn còn sử dụng các khoảng đệm không đồng nhất:
1. `ProcessHero.vue` đang áp dụng `lg:p-16` (64px) thay vì mức đệm chuẩn 80px của khung Hero Card.
2. `ServiceHero.vue` đang dùng giá trị tùy biến `lg:px-[64px]` ở khung bài viết nổi bật thay vì token chuẩn 80px (`lg:px-20`).
3. `ProcessCtaBanner.vue` đang có khoảng cách đệm đáy `lg:pb-24` (96px), lệch so với nhịp điệu section chuẩn 80px (`lg:pb-20`).

Việc chuẩn hóa các khối Banner còn lại về khoảng đệm 80px (Tailwind step 20 = 5rem = 80px) là cần thiết để hoàn thiện tính nhất quán thị giác 100% (Pixel-Perfect Alignment & Visual Rhythm) xuyên suốt toàn bộ website.

## What Changes

1. **Chuẩn hóa Hero Card Quy trình (`ProcessHero.vue`)**:
   - Cập nhật padding của thẻ Hero Card từ `lg:p-16` thành `lg:p-20` (80px) để nội dung bên trong có khoảng đệm 80px đối xứng, đồng bộ với nhịp điệu thẻ Hero chuẩn.
   - Đồng bộ vị trí nút cuộn xoay 360° (Rotating Badge) thành `lg:right-20 lg:bottom-20` tương ứng lề 80px.
2. **Chuẩn hóa Khung Minh Họa Dịch Vụ Nổi Bật (`ServiceHero.vue`)**:
   - Thay thế lớp tiện ích cố định `lg:px-[64px]` thành `lg:px-20` (80px) giúp cân bằng tỷ lệ giữa ảnh render 3D và các cột thông tin nội dung.
3. **Chuẩn hóa Khoảng Cách Đáy CTA Banner (`ProcessCtaBanner.vue`)**:
   - Điều chỉnh padding đáy của section từ `lg:pb-24` (96px) về `lg:pb-20` (80px), khớp với nhịp điệu spacing token `theme.extend.spacing.section` (80px) trước khối Footer.

## Capabilities

### New Capabilities
- `banner-spacing`: Định nghĩa và chuẩn hóa quy chuẩn padding/spacing 80px cho các thành phần Banner Hero và CTA Banner trên toàn bộ các trang chức năng.

### Modified Capabilities
*(Không có thay đổi yêu cầu cấp đặc tả đối với các capability hiện có)*

## Impact

- **Mã nguồn Frontend bị ảnh hưởng**:
  - `resources/Frontend/js/Pages/Process/Components/ProcessHero.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue`
  - `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue`
- **Safe Scope (Vùng an toàn không bị ảnh hưởng)**:
  - Giữ nguyên cấu trúc dữ liệu CMS dynamic và các prop truyền vào component.
  - Không thay đổi backend controller, routing, database migration hay API schema.
  - Giữ nguyên cấu trúc responsive trên màn hình nhỏ (Mobile/Tablet tiếp tục dùng `p-6 sm:p-10`, `p-6 sm:p-8`, `pb-16`).
