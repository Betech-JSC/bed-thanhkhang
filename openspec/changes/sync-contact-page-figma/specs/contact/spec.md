# Spec: Contact Page UI & Interaction

## Purpose
Đảm bảo trang Liên hệ tư vấn đáp ứng tiêu chuẩn Figma Node 2283:6726 về mặt thẩm mỹ, kích thước, màu sắc và chức năng biểu mẫu.

## Requirements
### R1: Hero Section Match
- Gradient nền chính xác `linear-gradient(0deg, #111322 20%, #2970FF 94%)`.
- Typography chuẩn Figma: Badge 16px `#D1E0FF`, H1 38px/46px `#FAFAFA`, Subtitle 16px/26px `#D1E0FF`.
- Artwork robot 3D hiển thị cân đối.

### R2: 3 Contact Channel Cards
- Nền `rgba(255, 255, 255, 0.2)` (`bg-white/20`), viền `#5D6B98`, bo góc 12px, padding 28px.
- Nội dung Zalo, Hotline, Email đầy đủ link tương tác thực tế (`https://zalo.me/...`, `tel:...`, `mailto:...`).

### R3: Consultation Form
- Input fields chiều cao 52px, nền `rgba(255, 255, 255, 0.2)`, viền `#5D6B98`, bo góc 8px.
- 3 Pill buttons chọn dịch vụ: "Thiết kế 3D", "Gia công 3D", "Làm đồ custom". Active: nền `#243971`, viền `#528BFF`.
- Textarea chiều cao 136px.
- Nút submit 56px gradient `#2970FF` -> `#00359E`.
- Validation client-side & xử lý Inertia post kèm modal thông báo thành công.

### R4: FAQ Accordion
- Nền mỗi card `rgba(255, 255, 255, 0.2)`, viền `#5D6B98`, bo góc 12px, chiều cao tối thiểu 72px.
- 3 câu hỏi thường gặp khớp Figma với icon plus xoay mở nội dung.

### R5: Responsive & Verification
- Tương thích tốt trên Desktop (1440px), Tablet, Mobile (375px).
- Zero legacy brand / error.
