# Specification: Admin Core Tabs & Streamlined Sidebar

## 1. Sidebar Navigation Specification (`SidebarMain.vue`)

### 1.1 Nhóm chức năng & Phân khu
Sidebar CMS được cấu trúc thành 4 nhóm rõ ràng với tiêu đề uppercase font-bold, màu xám nhạt:
1. **📊 BẢNG ĐIỀU KHIỂN**:
   - `admin.dashboard.index`: Tổng quan thống kê và lượt truy cập.
2. **🟢 VẬN HÀNH XƯỞNG IN 3D**:
   - `admin.contacts.index`: Báo giá & Yêu cầu in 3D (hiển thị badge số lượng liên hệ mới `newContact`).
   - `admin.products.index`: Quản lý Sản phẩm in 3D.
   - `admin.product-categories.index`: Danh mục Sản phẩm.
   - `admin.services.index`: Dịch vụ In & Thiết kế.
3. **🎨 NỘI DUNG & THƯƠNG HIỆU**:
   - `admin.sliders.index`: Banner & Hero Trang chủ.
   - `admin.posts.index`: Bài viết & Tin tức.
   - `admin.post-categories.index`: Danh mục bài viết.
   - `admin.policies.index`: Chính sách & Cam kết.
4. **⚙️ HỆ THỐNG & CẤU HÌNH**:
   - `admin.users.index`: Khách hàng đăng ký.
   - `admin.email-templates.index`: Mẫu Email thông báo.
   - `admin.files.index`: Quản lý Tệp media.
   - `admin.settings.form`: Cài đặt chung & Hotline.
   - `admin.admins.index`: Tài khoản Quản trị viên (nếu có quyền).
   - `admin.roles.index`: Phân quyền vai trò (nếu có quyền).

### 1.2 Các mục bị loại bỏ (Deprecated & Removed)
- `admin.orders.index` (Đơn hàng)
- `admin.members.index` (Đội ngũ thành viên)
- `admin.support-info.index` (Hỗ trợ & chính sách cũ)
- `admin.faqs.index` (Câu hỏi thường gặp FAQs)

---

## 2. Five Core Tabs Functional Specifications

### 2.1 Tab 1: Banner & Hero Slider
- **Admin Form (`Sliders/Form.vue`)**:
  - Tiêu đề (`title` - text)
  - Phụ đề (`subtitle` - text)
  - Mô tả (`description` - textarea)
  - Nút hành động chính (`primary_cta_text` - text, `primary_cta_link` - text)
  - Nút hành động phụ (`secondary_cta_text` - text, `secondary_cta_link` - text)
  - Hình desktop & mobile (`image`, `image_mobile` - file upload)
  - Trạng thái & Vị trí (`status`, `position_display`)
- **Frontend Integration (`HeroSection.vue` & `HomeController.php`)**:
  - `HomeController.php` truyền `sliders` lấy từ vị trí `HOME_SLIDER`.
  - `Home/Index.vue` và `HeroSection.vue` hiển thị dữ liệu động: nếu slider có `subtitle`, `title`, `description`, `primary_cta_text`, `primary_cta_link`, `secondary_cta_text`, `secondary_cta_link`, sử dụng các giá trị này; ngược lại fallback về giá trị mặc định của Figma.

### 2.2 Tab 2: Quản lý Sản phẩm (`Products`)
- **Admin Form (`Products/Products/Form.vue`)**:
  - Chọn danh mục sản phẩm (`categories`): Multi-select liên kết trực tiếp với model `ProductCategory`.
  - Giá bán & Giá so sánh: `price` (Giá bán/tham khảo VNĐ), `compare_at_price` (Giá niêm yết/chưa giảm VNĐ).
  - Thông số kỹ thuật in 3D:
    + Công nghệ in: Chọn FDM / SLA / SLS hoặc nhập tự do.
    + Vật liệu in: Chọn PLA / Resin / PETG / Carbon Fiber hoặc nhập tự do.
    + Lead time gia công: Thời gian hoàn thiện dự kiến (ví dụ: "1-2 ngày", "24-48 giờ").
- **Backend Model (`Product.php`)**:
  - `$fillable` bổ sung `price`, `compare_at_price`, `print_technology`, `material`, `lead_time`.
  - `transform()` trả về đầy đủ các trường thông số trên.
  - Sự kiện `saved` gọi `$model->saveCategories($model)` để lưu các liên kết danh mục.

### 2.3 Tab 3: Danh mục Sản phẩm (`Categories & ProductFilter`)
- **Frontend Component (`ProductFilter.vue`)**:
  - Nhận prop `categories` từ backend truyền xuống thay vì chỉ đọc danh sách hardcode `defaultCategories`.
  - Danh sách hiển thị có tùy chọn "Tất cả sản phẩm" ở vị trí đầu tiên, tiếp theo là các danh mục lấy từ cơ sở dữ liệu.
  - Fallback an toàn về danh sách chuẩn nếu `categories` chưa có dữ liệu.

### 2.4 Tab 4: Dịch vụ In & Thiết kế (`Services`)
- **Admin Form (`Services/Form.vue`)**:
  - Sửa trường `content` từ kiểu `text` (nhãn Link) sang kiểu `richtext` (CKEditor) với nhãn "Nội dung chi tiết".
  - Cho phép admin trình bày hình ảnh, văn bản định dạng, danh sách bullet points chuyên nghiệp.
- **Frontend Integration (`HomeController.php` & `ServicesSection.vue`)**:
  - `HomeController.php` nạp danh sách `services` kích hoạt (`Service::active()->sortByPosition()->get()`) và truyền vào view `Home/Index`.
  - `ServicesSection.vue` render các thẻ dịch vụ từ prop `services` hoặc fallback 3 thẻ mặc định theo thiết kế Figma.

### 2.5 Tab 5: Báo giá & Yêu cầu In 3D (`FormContacts`)
- **Trạng thái xử lý chuẩn xưởng in 3D (`Contact.php`)**:
  - `NEW`: Mới nhận
  - `CHECKING_CAD`: Đang kiểm tra file CAD
  - `QUOTED`: Đã báo giá
  - `PROCESSING`: Đang gia công/In
  - `COMPLETED`: Hoàn tất
  - `IS_SPAM`: Hủy/Spam
- **Admin Form (`FormContacts/Form.vue`)**:
  - Bổ sung ô nhập "Ghi chú nội bộ (Internal Notes)" để kỹ thuật viên xưởng ghi chép tiến độ, lỗi file STL/STEP, ghi chú vật liệu.
  - Bổ sung ô nhập "Giá trị báo giá tạm tính (VNĐ)" để lưu giá sau khi bóc tách khối lượng và thời gian in.
  - Lưu trữ an toàn trong các cột hoặc trường JSON `data` của liên hệ.
