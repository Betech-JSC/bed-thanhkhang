## Context & Current Flow

Hiện tại:
1. `Courses/Show.vue` tính giá `handleTotalPrice()` và nhét vào URL: `route('checkout.course.index', { ..., price: price })`.
2. `Checkout.vue` đọc `const price = params.get('price')` và gán thẳng `this.course.price = Number(price)`.
3. Mã VietQR:
   `<img :src="`https://img.vietqr.io/image/ACB-19568367-compact.jpg?amount=${course.price}&accountName=TRAN%20ANH%20DUY`" />`
4. Cột đơn hàng bên phải hiển thị `toMoney(course.price_original)` (lấy từ API schedule) và giảm giá.
5. Khi người dùng bấm tạo đơn, client gửi nguyên vẹn JSON chứa `price` lên `ContactController@store` và server lưu thẳng vào database mà không tính toán lại.

## Proposed Design & Architecture

### 1. Client-side Synchronization in `Checkout.vue`
- Khi `fetchCourse()` nhận dữ liệu từ `/api/courses/{id}`, tìm đúng lịch học `schedule` theo `schedule_id`.
- Tính toán lại giá chuẩn xác:
  - `originalPrice = Number(schedule.price || 0)`
  - Xác định ưu đãi theo `discount_id` (nếu có trong `schedule.schedule_discount` hoặc ưu đãi số lượng/combo):
    `discountPercent = Number(schedule.schedule_discount?.[discount_id]?.price || 0)`
  - `computedPrice = originalPrice * (1 - discountPercent / 100)`
- Luôn gán `this.course.price = computedPrice` thay vì phụ thuộc vào `params.get('price')`.
- Cập nhật cả mã VietQR và nhãn số tiền bằng `computedPrice`.

### 2. State Alignment in `Courses/Show.vue`
- Đảm bảo khi người dùng chuyển đổi giữa các lịch học (Radio), `selectedSchedule` luôn đồng bộ với cấu hình ưu đãi tương ứng của lịch đó, không bao giờ dùng ưu đãi của lịch Online áp dụng cho giá của lịch Offline hoặc ngược lại.

### 3. Server-side Validation in `ContactController.php`
- Khi nhận request `POST /contacts`, nếu có `course.id` và `course.schedule.id`:
  - Truy vấn thông tin khóa học và lịch học từ CSDL.
  - Tính toán `serverFinalPrice` theo quy tắc ưu đãi.
  - Cập nhật `data['course']['price'] = $serverFinalPrice` trước khi lưu vào CSDL, đảm bảo dữ liệu ghi vào bảng `contacts` luôn chuẩn xác.
