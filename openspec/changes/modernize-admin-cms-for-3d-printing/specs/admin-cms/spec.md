# Spec: Chuẩn hóa Admin CMS cho Xưởng In 3D HH Maker

## 1. Mục tiêu

Đặc tả các yêu cầu kỹ thuật và trải nghiệm người dùng cho hệ thống Admin CMS của xưởng in 3D HH Maker, bảo đảm:
- Tiếp nhận và hiển thị chính xác mọi yêu cầu báo giá in 3D từ các form frontend.
- Nhận diện và cho phép chuyên viên kỹ thuật tải về các file mô hình CAD 3D (.stl, .step, .obj, .3mf, .zip).
- Cung cấp thanh điều hướng (Sidebar) chuẩn xác các tính năng nghiệp vụ của xưởng in và cửa hàng.
- Cung cấp Dashboard đo lường hiệu suất báo giá, nhu cầu dịch vụ in 3D và chuyển đổi đơn hàng.

---

## 2. Yêu cầu chi tiết

### 2.1. Quản lý Yêu cầu Báo giá In 3D (`FormContacts`)

#### Danh sách Báo giá (`Index.vue`)
- Hệ thống phải hiển thị đầy đủ các cột thông tin:
  - ID
  - Họ và tên: Trích xuất thông minh từ `Họ và tên`, `Họ tên`, `name`, `full_name`. Nếu không có, hiển thị `-`.
  - Số điện thoại: Trích xuất từ `Số điện thoại`, `Số ĐT`, `phone`, `telephone`. Nếu không có, hiển thị `-`.
  - Email: Trích xuất từ `Email`, `email`, hoặc `data_contact.email`. Nếu không có, hiển thị `-`.
  - Dịch vụ: Trích xuất từ `Dịch vụ quan tâm`, `Dịch vụ`, `service`, `Service.title`. Nếu không có, hiển thị `-`.
  - Trạng thái (`status`): Nhãn trạng thái tiếng Việt theo chuẩn (`Mới`, `Đã phản hồi`, `Đã xác nhận`, `Đã xử lý`, `Đóng`, `Spam`).
  - Ngày tạo (`created_at`): Định dạng ngày giờ chuẩn.
- Cấu hình bảng: Không cho phép tạo mới thủ công từ admin (`canCreate: false`).

#### Chi tiết Báo giá (`Form.vue`)
- Hiển thị đầy đủ thông tin khách hàng và nội dung yêu cầu báo giá.
- Không hiển thị bất kỳ khối thông tin nào liên quan đến "Khóa học" hoặc "Tài nguyên giáo dục" cũ.
- Khu vực File đính kèm:
  - Nếu file là định dạng hình ảnh (`.jpg`, `.jpeg`, `.png`, `.webp`, `.svg`, `.gif`): Hiển thị ảnh xem trước (thumbnail preview) kèm liên kết phóng to.
  - Nếu file là định dạng mô hình 3D hoặc nén (`.stl`, `.obj`, `.step`, `.stp`, `.3mf`, `.zip`, `.rar`, `.7z`, `.pdf`):
    - Tuyệt đối không dùng thẻ `<img>` tránh lỗi vỡ hình ảnh.
    - Hiển thị Card định dạng file chuyên nghiệp với icon mô hình 3D / tệp tin, tên tệp, huy hiệu định dạng (ví dụ: `STL`, `STEP`, `ZIP`), kích thước nếu có, và nút bấm **"Tải file mô hình 3D"** hoặc **"Mở file"** trỏ về link download chính xác.

### 2.2. Kết nối Biểu mẫu Trang chủ (`ContactSection.vue`)
- Endpoint gửi dữ liệu: Sử dụng `route('contact.store')` với fallback `/contacts` thay cho endpoint cũ `/api/contact`.
- Payload gửi lên:
  - `type`: `'CONTACT_FORM'`
  - Hỗ trợ cả hai định dạng:
    - Thuộc tính cấp 1: `name`, `phone`, `email`
    - Cấu trúc `data`: `{ 'Họ và tên': ..., 'Số điện thoại': ..., 'Email': ..., 'Nội dung cần hỗ trợ': ... }`
- Phản hồi UX: Hiển thị trạng thái đang gửi (`isSubmitting`) và thông báo thành công sau khi gửi.

### 2.3. Chuẩn hóa Sidebar Điều hướng (`SidebarMain.vue`)
- Bổ sung:
  - **Danh mục sản phẩm**: route `admin.product-categories.index` (kèm icon category).
  - **Đơn hàng**: route `admin.orders.index` (kèm icon shopping cart).
- Loại bỏ các liên kết chết và menu tàn dư FunkyStyle:
  - Hình ảnh Môi trường học (`admin.learning-environments.index`)
  - Giải thưởng (`admin.prizes.index`)
  - Feedback (`admin.feedback.index`)
  - Dự án (`admin.projects.index`)
  - Danh mục dự án (`admin.project-categories.index`)
- Giữ nguyên các chức năng cốt lõi: Sản phẩm, Dịch vụ, Đội ngũ, Bài viết, Danh mục bài viết, Liên hệ, Hỗ trợ & Chính sách, Mẫu Email, Logs Hệ thống.

### 2.4. Bảng điều khiển HH Maker 3D Printing (`DashboardController.php` & `Dashboard.vue`)
- **Backend Controller**:
  - Gỡ bỏ toàn bộ logic truy vấn khóa học (`TYPE_CHECKOUT_COURSE`, `aggregateByCourse`, `majorYearStats`).
  - Thống kê báo giá in 3D:
    - Số lượng báo giá hôm nay (`today_quote_count`).
    - Số lượng báo giá tháng này (`this_month_quotes`), tháng trước (`last_month_quotes`), và tỷ lệ tăng trưởng MoM (`quote_growth`).
    - Thống kê theo trạng thái: Mới (`NEW`), Đang xử lý (`PROCESSING`/`RESPONSE`), Đã hoàn thành/xác nhận (`CONFIRMED`/`PROCESSED`).
  - Thống kê phân loại dịch vụ quan tâm:
    - Thiết kế 3D (Thiết kế CAD, dựng hình 3D theo yêu cầu).
    - Gia công in 3D (In sợi FDM, In nhựa SLA/DLP, gia công hoàn thiện).
    - Làm đồ custom (Mô hình quà tặng, decor, phụ kiện cá nhân hóa).
    - Tư vấn kỹ thuật / Khác.
    - Tính toán số lượng và tỷ trọng phần trăm (%) cho từng nhóm dịch vụ.
  - Biểu đồ xu hướng báo giá:
    - Trục thời gian theo 12 tháng trong năm được chọn hoặc theo quý/tháng.
  - Danh sách Báo giá in 3D gần nhất (Recent Quotes): 5-10 bản ghi mới nhất hiển thị tên khách hàng, số điện thoại, dịch vụ, trạng thái và thời gian gửi.
- **Frontend Dashboard View**:
  - Giao diện High-Tech Clean & High-Trust:
    - Thẻ Metric: Báo giá hôm nay, Tổng báo giá tháng này, Tăng trưởng báo giá (MoM), Tỷ lệ dịch vụ gia công.
    - Biểu đồ cột: Xu hướng yêu cầu báo giá in 3D qua các tháng.
    - Biểu đồ phân bổ cơ cấu dịch vụ: Hiển thị tỷ trọng Thiết kế 3D vs Gia công 3D vs Đồ custom.
    - Bảng danh sách yêu cầu báo giá mới nhất kèm nút xem nhanh chi tiết.
  - Tuyệt đối loại bỏ phần "PHÂN TÍCH ĐỐI TƯỢNG HỌC VIÊN".

---

## 3. Tiêu chí Nghiệm thu (Acceptance Criteria)

1. `FormContacts/Index.vue`:
   - Các cột Họ tên, Số điện thoại, Email, Dịch vụ hiển thị đầy đủ giá trị ngay cả khi dữ liệu lưu bằng key tiếng Việt hoặc tiếng Anh.
2. `FormContacts/Form.vue`:
   - Không xuất hiện bất kỳ chuỗi text nào về "Khóa học" hay "Tài nguyên".
   - File CAD (.stl, .obj, .step, .3mf, .zip) hiển thị nút tải file chuyên dụng, click mở/tải file đúng đường dẫn tĩnh.
3. `ContactSection.vue`:
   - Gửi yêu cầu qua endpoint `/contacts`, server trả về status 200/302 hợp lệ và lưu vào cơ sở dữ liệu `contacts` với `type = 'CONTACT_FORM'`.
4. `SidebarMain.vue`:
   - Sidebar có menu "Danh mục sản phẩm" và "Đơn hàng".
   - Không còn các mục Môi trường học, Giải thưởng, Feedback, Dự án.
5. `Dashboard`:
   - Không còn query khóa học hay biểu đồ học viên.
   - Hiển thị các chỉ số báo giá in 3D và biểu đồ cơ cấu dịch vụ.
6. Build & Test:
   - `yarn backend:build` thoát với Exit code 0.
   - `yarn frontend:build` thoát với Exit code 0.
   - `php artisan test tests/Feature/ContactFormTest.php` pass 100%.
