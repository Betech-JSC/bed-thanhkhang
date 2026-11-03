# Design: Dọn Dẹp Banner CTA Thừa Trang Quy Trình Khớp 100% Figma

## 1. Bối Cảnh & Kiến Trúc Hiện Tại

Trang Quy trình (`resources/Frontend/js/Pages/Process/Index.vue`) trước đó được tổ chức thành 5 section con:
- `ProcessHero.vue`: Hero banner với gradient xanh điện tử và thông điệp mở đầu.
- `ProcessStepper.vue`: Thanh 5 điểm đánh dấu lộ trình quy trình (Liên hệ, Báo giá, Gia công 3D, Đóng gói, Gửi hàng).
- `ProcessStepItem.vue`: Chi tiết 5 bước gia công xen kẽ chuẩn thiết kế.
- `ProcessFaq.vue`: Khối câu hỏi thường gặp FAQ Accordion (3 câu hỏi có thể đóng mở).
- `ProcessCtaBanner.vue`: Banner kêu gọi hành động bổ sung (Node `2352:11795`, "Lời mời sáng tạo").

Sau khi khảo sát lại tọa độ và cây phân cấp Figma frame Quy trình (`2283:6841` -> `2283:6851` -> `2283:6882` -> `2334:8455` -> `2283:7686` Footer), component `ProcessCtaBanner.vue` hoàn toàn không có trong thiết kế Figma gốc.

## 2. Quyết Định Kỹ Thuật

1. **Gỡ bỏ Component Khỏi `Index.vue`**:
   - Loại bỏ câu lệnh import `import ProcessCtaBanner from './Components/ProcessCtaBanner.vue'`
   - Loại bỏ thẻ `<ProcessCtaBanner />` ở cuối template.
2. **Khoảng Đệm Thị Giác Tự Nhiên**:
   - `ProcessFaq.vue` đã có sẵn khoảng đệm padding chuẩn Figma (`padding: 80px 144px 120px`), do đó phần chân của FAQ tạo ra khoảng trống thở 120px rất tự nhiên và thoải mái trước khi tiếp xúc với thanh Footer `#0C0E1A`.
3. **Nguyên Tắc An Toàn (Strict Safe Guard)**:
   - File `ProcessCtaBanner.vue` KHÔNG bị xóa khỏi repository nhằm đảm bảo an toàn tuyệt đối, tuân thủ nghiêm ngặt Điều 1 trong `AGENTS.md`.

## 3. Kế Hoạch Kiểm Thử

- Chạy `yarn frontend:build` để biên dịch thành công cả Client & SSR bundle.
- Chạy Playwright E2E test để chụp ảnh màn hình toàn trang trên cả Desktop (1440px) và Mobile (390px), kiểm chứng rằng chân trang kết thúc ngay sau khối FAQ và chuyển sang Footer.
