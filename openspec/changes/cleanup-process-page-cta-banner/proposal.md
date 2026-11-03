# Proposal: Dọn Dẹp Banner CTA Thừa Trang Quy Trình Khớp 100% Figma

## Why
Trang Quy trình (`resources/Frontend/js/Pages/Process/Index.vue`) hiện tại đang chứa component `<ProcessCtaBanner />` chen giữa phần FAQ Accordion và Footer. 

Đối chiếu với thiết kế Figma chuẩn (File key: `otk6uzZmitaXPdZZZiRG1K`, frame Quy trình), cấu trúc trang chỉ bao gồm đúng 4 section:
1. Section 1: Hero Banner (Figma Node `2283:6841`, 1440x680px)
2. Section 2: Stepper Bar 5 Điểm Quy Trình (Figma Node `2283:6851`, 1440x176px)
3. Section 3: Chi Tiết 5 Bước Gia Công (Figma Node `2283:6882`, 1440x1273px)
4. Section 4: FAQ Accordion (Figma Node `2334:8455`, 1440x621px)

Ngay sau FAQ Accordion là Footer chung của website. Khối CTA banner ("Lời mời sáng tạo", Node `2352:11795`) là thành phần dư thừa, làm phá vỡ nhịp điệu thị giác và cấu trúc 4 section đã được duyệt. Cần gỡ bỏ khối này để trang Quy trình đạt độ khớp 100% thiết kế.

## What Changes
1. Gỡ bỏ việc nhúng `<ProcessCtaBanner />` và câu lệnh `import ProcessCtaBanner` trong `resources/Frontend/js/Pages/Process/Index.vue`.
2. Đảm bảo cấu trúc trang kết thúc tại `<ProcessFaq />` và chuyển tiếp mượt mà sang `Footer`.
3. Tuân thủ tuyệt đối quy tắc An Toàn (Strict Safe Guard): Cấm xóa file vật lý `ProcessCtaBanner.vue` khỏi ổ đĩa.
4. Chạy `yarn frontend:build` và kiểm thử tự động với Playwright chụp ảnh Desktop & Mobile để nghiệm thu bằng chứng.

## Capabilities

### New Capabilities
- `process-page`: Đặc tả cấu trúc 4 section chuẩn Figma của trang Quy trình gia công 3D (Hero Banner, Stepper Bar, Chi tiết 5 bước, FAQ Accordion) không có CTA banner thừa.

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/Frontend/js/Pages/Process/Index.vue`: Gỡ bỏ import và thẻ component CTA banner.
- Không ảnh hưởng đến backend hay cơ sở dữ liệu.
