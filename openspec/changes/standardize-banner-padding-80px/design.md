# Design: Chuẩn Hóa Padding 80px Cho Các Khối Banner

## Context

Xem chi tiết động lực và bối cảnh tại `proposal.md`.
Sau các đợt đồng bộ giao diện Dark Tech từ Figma và chuẩn hóa Universal Container, các thành phần Banner Hero và CTA Banner tại các trang `Process` và `Services` vẫn còn một số sai lệch cục bộ về padding (64px, 96px). Việc chuẩn hóa này tập trung hoàn thiện các giá trị padding desktop về mốc chuẩn 80px (Tailwind step 20 = 5rem = 80px).

## Goals / Non-Goals

**Goals:**
- Chuẩn hóa toàn bộ padding ngang và nhịp điệu của các banner hero/CTA còn lại về mức 80px trên Desktop (viewport $\ge$ 1024px).
- Giữ nguyên 100% khả năng tương thích responsive trên Mobile và Tablet.
- Duy trì tính năng xoay badge 360° và cuộn mượt đến các section mục tiêu.

**Non-Goals:**
- Không can thiệp vào logic xử lý dữ liệu CMS dynamic hay i18n translation.
- Không thay đổi cấu trúc DOM cơ bản của các section.
- Không sửa đổi backend PHP hay cơ sở dữ liệu.

## Decisions

### 1. Sử dụng Tailwind scale chuẩn `20` (`lg:p-20`, `lg:px-20`, `lg:pb-20`)
- **Lựa chọn**: Sử dụng các lớp tiện ích chuẩn của Tailwind CSS (`p-20`, `px-20`, `pb-20` đại diện cho `5rem = 80px`).
- **Lý do**: Tailwind CSS 3 mặc định hỗ trợ step `20` bằng `5rem` (`80px`), đồng thời trùng khớp với token `theme.extend.spacing.section` (80px). Việc này giúp CSS bundle gọn nhẹ và tuân thủ chặt chẽ design tokens.
- **Phương án thay thế đã cân nhắc**: Dùng arbitrary values dạng `lg:px-[80px]` hoặc `lg:pb-[80px]`. Tuy kết quả render tương đương nhưng kém nhất quán với hệ thống class chuẩn.

### 2. Căn chỉnh vị trí Rotating Badge của ProcessHero theo lề 80px
- **Lựa chọn**: Đổi `lg:right-16 lg:bottom-16` sang `lg:right-20 lg:bottom-20`.
- **Lý do**: Khi đệm của Hero Card được nâng từ 64px (`p-16`) lên 80px (`p-20`), badge xoay 360° cần được dời tương ứng vào vị trí cách viền phải và viền dưới 80px để giữ khoảng cách lề song song với nội dung văn bản bên trái.

## Risks / Trade-offs

- **[Rủi ro]**: Thay đổi padding ngang có thể làm co hẹp bề rộng hiển thị của ảnh render 3D hoặc khối văn bản trên màn hình laptop 1024px - 1280px.
  - **Giảm thiểu**: Bề rộng card và container đã được kiểm chứng trên Figma (1279px và 1152px), với padding 80px vùng hiển thị nội dung vẫn đảm bảo tối thiểu > 992px, dư thừa không gian cho cả 3 cột thông tin mà không gây gãy dòng ngoài ý muốn.
