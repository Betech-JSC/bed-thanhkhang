## 1. Frontend Checkout Recalculation & VietQR Sync

- [x] 1.1 Cập nhật `handleTotalPrice` và `fetchCourse` trong `resources/Frontend/js/Pages/Checkout/Checkout.vue` để tính toán lại học phí từ `schedule.price` và ưu đãi đã chọn thay vì nhận mù quáng `price` từ URL.
- [x] 1.2 Đảm bảo mã VietQR ACB và ô hiển thị số tiền chuyển khoản luôn sử dụng học phí đã tính toán lại chuẩn xác.

## 2. Frontend Course Show State Alignment

- [x] 2.1 Cập nhật `resources/Frontend/js/Pages/Courses/Show.vue` đảm bảo khi chuyển đổi giữa các lịch học Offline và Online, biến `price` và ưu đãi luôn được đồng bộ đúng theo lịch học đang chọn.

## 3. Backend Server-side Price Verification

- [x] 3.1 Cập nhật `app/Http/Controllers/Frontend/ContactController.php` để đối soát và chuẩn hóa `data.course.price` theo giá gốc lịch học và ưu đãi thực tế từ CSDL trước khi lưu đơn.

## 4. Testing & Verification

- [x] 4.1 Viết unit/feature test hoặc test thủ công kiểm tra luồng tạo đơn và tính toán giá tiền giữa lịch Offline (2.600k -> 2.470k) và Online (2.400k -> 2.280k).
