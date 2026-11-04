# Proposal: Chuẩn hóa Admin CMS cho Xưởng In 3D HH Maker

## Why

Trong quá trình chuyển đổi toàn diện từ nền tảng giáo dục FunkyStyle sang xưởng dịch vụ in 3D công nghệ cao HH Maker, hệ thống quản trị nội dung (Admin CMS) vẫn còn tồn đọng nhiều mã nguồn và thiết kế nghiệp vụ của hệ thống cũ:
1. **Lệch cấu trúc dữ liệu Báo giá in 3D (`FormContacts`)**: Dữ liệu gửi từ các biểu mẫu frontend và API lưu trữ dưới dạng key tiếng Việt (`Họ và tên`, `Số điện thoại`, `Email`, `Dịch vụ quan tâm`, `Nội dung cần hỗ trợ`) hoặc key tiếng Anh phẳng (`name`, `phone`, `email`), trong khi màn hình danh sách Admin `FormContacts/Index.vue` chỉ đọc trường cứng `data.data.name` và `data.data.phone`, khiến các cột Họ tên, Số điện thoại và Email bị trống hoặc hiển thị lỗi.
2. **Thiếu hỗ trợ file mô hình 3D (CAD / 3D Print Files)**: Màn hình chi tiết liên hệ `FormContacts/Form.vue` cố tình render toàn bộ file tải lên bằng thẻ `<img>`. Khi khách hàng đính kèm file mô hình CAD (.stl, .obj, .step, .3mf, .zip), ảnh preview bị vỡ (`broken image`) và chuyên viên kỹ thuật không thể tải hay xem trực tiếp file 3D để tiến hành báo giá.
3. **Lỗi endpoint submit ở Trang chủ**: Thành phần `ContactSection.vue` ở trang chủ gửi dữ liệu tới `/api/contact` (route không tồn tại hoặc không khớp quy chuẩn), thay vì gửi tới endpoint chính thức `route('contact.store')` (`/contacts`).
4. **Sidebar Menu tàn dư FunkyStyle & thiếu menu nghiệp vụ In 3D**: Menu điều hướng hiển thị các mục của trung tâm đào tạo cũ như "Hình ảnh Môi trường học", "Giải thưởng", "Feedback học viên", "Dự án", "Danh mục dự án", trong khi thiếu vắng các menu thiết yếu cho xưởng in 3D: "Danh mục sản phẩm" (`admin.product-categories.index`) và "Đơn hàng" (`admin.orders.index`).
5. **Dashboard lệch định hướng**: Bảng điều khiển vẫn truy vấn thống kê `TYPE_CHECKOUT_COURSE` (đơn mua khóa học) và hiển thị biểu đồ phân tích đối tượng sinh viên theo ngành học/năm học, hoàn toàn không phản ánh bức tranh kinh doanh của xưởng in 3D (số lượng yêu cầu báo giá in 3D mới, tỷ lệ các dịch vụ Thiết kế 3D / Gia công 3D / Làm đồ custom, và biểu đồ tăng trưởng RFQ).

Việc chuẩn hóa Admin CMS giúp đội ngũ vận hành HH Maker tiếp nhận, xử lý báo giá và tải file CAD tức thì, đồng thời theo dõi chính xác các chỉ số tăng trưởng kinh doanh cốt lõi.

## What Changes

- **Chuẩn hóa Danh sách Báo giá (`FormContacts/Index.vue`)**:
  - Viết lại hàm `transform` đa tầng cho các cột Họ và tên, Số điện thoại, Email, Dịch vụ quan tâm.
  - Hỗ trợ fallback linh hoạt giữa tiếng Việt (`Họ và tên`, `Số điện thoại`, `Email`, `Dịch vụ quan tâm`) và tiếng Anh (`name`, `phone`, `email`, `service`, `note`).
- **Nâng cấp Chi tiết Báo giá & Tải file CAD (`FormContacts/Form.vue`)**:
  - Gỡ bỏ hoàn toàn logic hiển thị khóa học và tài nguyên giáo dục (`form.data_contact.course`, `form.data_contact.resource`).
  - Trình bày thông tin liên hệ và báo giá rõ ràng, chuyên nghiệp (Họ tên, SĐT, Email, Dịch vụ yêu cầu, Nội dung chi tiết).
  - Phân loại file đính kèm: nếu là file ảnh thông thường (.jpg, .png, .webp) thì hiển thị thumbnail preview; nếu là file mô hình 3D / tài liệu CAD (.stl, .obj, .step, .stp, .3mf, .zip, .rar, .7z, .pdf...) thì hiển thị Card tải file chuyên nghiệp với icon 3D model, tên file, định dạng và nút Download trực tiếp.
- **Sửa Endpoint Liên hệ Trang chủ (`ContactSection.vue`)**:
  - Cập nhật URL gửi form từ `/api/contact` sang `route('contact.store')` (`/contacts`).
  - Định dạng payload chuẩn `CONTACT_FORM` tương thích cả cấu trúc phẳng lẫn cấu trúc lồng `data`.
- **Chuẩn hóa Sidebar Điều hướng (`SidebarMain.vue`)**:
  - Bổ sung menu: "Danh mục sản phẩm" (`admin.product-categories.index`) và "Đơn hàng" (`admin.orders.index`).
  - Loại bỏ các menu tàn dư và link hỏng: "Hình ảnh Môi trường học", "Giải thưởng", "Feedback", "Dự án", "Danh mục dự án".
- **Chuyển đổi Dashboard sang HH Maker 3D Printing (`DashboardController.php` & `Dashboard.vue`)**:
  - Thay thế toàn bộ logic truy vấn khóa học cũ bằng thống kê Báo giá in 3D (`CONTACT_FORM`).
  - Thống kê tỷ lệ phân bổ các dịch vụ in 3D chủ lực: Thiết kế 3D, Gia công in 3D (FDM/SLA), Gia công custom / Quà tặng decor, và Tư vấn khác.
  - Cập nhật biểu đồ xu hướng báo giá theo thời gian (tháng/quý/năm) và danh sách các yêu cầu báo giá gần nhất.
  - Gỡ bỏ hoàn toàn phần "PHÂN TÍCH ĐỐI TƯỢNG HỌC VIÊN", thay thế bằng giao diện High-Tech Dark/Tech UI hiển thị các thẻ Metric hiệu suất xưởng in 3D.

## Capabilities

### New Capabilities
- `admin-cms-3d-printing`: Bộ tính năng quản trị CMS chuyên biệt cho xưởng in 3D HH Maker, bao gồm xử lý báo giá đa kênh, xem & tải file mô hình CAD 3D (.stl, .step, .obj, .3mf), theo dõi chuyển đổi đơn hàng và báo cáo phân tích dịch vụ in 3D.

### Modified Capabilities
- `contact-form`: Biểu mẫu tiếp nhận yêu cầu báo giá in 3D từ trang chủ được đồng bộ gửi trực tiếp vào hệ thống quản trị `CONTACT_FORM`.

## Impact

- **Backend Controllers**:
  - `app/Http/Controllers/Backend/DashboardController.php`: Tái cấu trúc logic tổng hợp số liệu báo giá in 3D thay thế khóa học.
- **Frontend / Backend Vue Components**:
  - `resources/Backend/js/Pages/FormContacts/Index.vue`: Cải tiến hiển thị danh sách yêu cầu báo giá.
  - `resources/Backend/js/Pages/FormContacts/Form.vue`: Nâng cấp giao diện chi tiết báo giá và hỗ trợ tải file CAD.
  - `resources/Backend/js/Components/SidebarMain.vue`: Tinh gọn menu điều hướng chuẩn cho xưởng in.
  - `resources/Backend/js/Pages/Dashboard.vue`: Giao diện dashboard hiện đại với metric in 3D.
  - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`: Sửa endpoint và payload gửi form.
- **Database**: Không thay đổi schema DB; khai thác hiệu quả bảng `contacts` và `orders` hiện có.
