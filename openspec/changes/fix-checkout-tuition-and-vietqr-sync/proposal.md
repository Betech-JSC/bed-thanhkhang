## Why

Hệ thống ghi nhận lỗi lệch học phí và mã VietQR thanh toán (học viên thấy mã QR báo 2.280.000 đ và chuyển khoản nhầm, trong khi Admin và học phí thực tế của lớp Offline là 2.470.000 đ).
Nguyên nhân xuất phát từ việc sau khi revert luồng thanh toán tự động về bản chuyển khoản thủ công, trang Checkout phụ thuộc 100% vào tham số lộ thiên `?price=` trên URL do `Courses/Show.vue` truyền sang mà không tính toán lại từ dữ liệu API của lịch học, dẫn đến việc xung đột giữa mã QR (lấy giá Online 2.400k - 5% = 2.280k) và bảng đơn hàng (lấy lịch Offline 2.600k - 5% = 2.470k). Đồng thời Backend chưa đối soát lại giá tiền trước khi lưu DB.

## What Changes

- **Frontend Checkout (`Checkout.vue`):** Không phụ thuộc vào tham số `price` trên URL để quyết định số tiền thanh toán. Luôn tính toán lại số tiền thực tế (`final_price`) dựa trên giá gốc của lịch học (`schedule.price`) và ưu đãi đã chọn từ API, đảm bảo mã VietQR và cột chi tiết đơn hàng luôn đồng nhất 100%.
- **Frontend Chi tiết khóa học (`Courses/Show.vue`):** Chuẩn hóa việc chọn lịch học và ưu đãi, loại bỏ hoàn toàn khả năng desync giữa radio lịch học và dropdown ưu đãi.
- **Backend Controller (`ContactController.php` & `ContactCheckoutCourseController.php`):** Thêm bước xác thực và tính toán lại giá tiền hợp lệ trên server-side khi tạo đơn (`POST /contacts`), ngăn chặn việc ghi nhận sai số tiền từ client.
- **Giao diện & Trải nghiệm (UI/UX):** Hiển thị rõ ràng phân loại hình thức học `[Online]` hoặc `[Offline]` tại ô lịch học trang Checkout và bảng quản trị đơn hàng CMS.

## Capabilities

### Modified Capabilities
- `course-checkout-pricing`: Đồng bộ và tính toán chuẩn xác học phí, ưu đãi và mã VietQR trên trang Checkout và Backend.
