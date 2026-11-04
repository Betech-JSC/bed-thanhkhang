# Proposal: Tinh giản Sidebar Admin CMS và Hoàn thiện 5 Tabs Nghiệp vụ Trọng tâm Xưởng in 3D

## 1. Bối cảnh & Vấn đề (Context & Problem)
Hệ thống Admin CMS của HH Maker ban đầu được kế thừa từ framework đa năng có nhiều module e-commerce và giáo dục không phù hợp với mô hình xưởng in 3D & gia công cơ khí tạo mẫu nhanh (như Đơn hàng online `admin.orders.index`, Đội ngũ `admin.members.index`, FAQs `admin.faqs.index`, Hỗ trợ & Chính sách `admin.support-info.index`). Sidebar hiện tại phẳng, thiếu phân khu logic khiến quản trị viên thao tác khó khăn.

Đồng thời, 5 tab trọng tâm đóng vai trò xương sống cho hoạt động của xưởng in 3D hiện còn tồn tại nhiều thiếu sót và lỗi kỹ thuật:
1. **Tab Banner (`Sliders`)**: Form quản trị thiếu phụ đề (`subtitle`), CTA thứ cấp (`secondary_cta`) và liên kết CTA động; `HeroSection.vue` ở trang chủ chưa kết nối mượt mà với banner từ backend kèm cơ chế fallback an toàn.
2. **Tab Sản phẩm (`Products`)**: Form quản trị thiếu chọn danh mục sản phẩm (`categories`), thiếu các trường giá tham khảo/giá niêm yết (`price`, `compare_at_price`), và thiếu các trường thông số kỹ thuật cốt lõi của xưởng in 3D (Công nghệ in: FDM/SLA/SLS, Vật liệu in: PLA/Resin/PETG, Lead time gia công).
3. **Tab Danh mục (`Categories`)**: Component `ProductFilter.vue` phía frontend đang hardcode danh sách `defaultCategories` thay vì đọc dữ liệu danh mục động được truyền từ backend.
4. **Tab Dịch vụ (`Services`)**: Trường `content` trong form quản trị `Services/Form.vue` bị cấu hình sai kiểu `text` (nhãn "Link") thay vì Rich-Text (CKEditor), khiến admin không thể soạn thảo bài viết dịch vụ chi tiết. Đồng thời `HomeController.php` chưa nạp danh sách `services` để cấp sang `ServicesSection.vue` ở trang chủ.
5. **Tab Báo giá & Yêu cầu in 3D (`FormContacts`)**: Danh sách trạng thái xử lý chưa chuẩn hóa theo quy trình vận hành xưởng in 3D (Mới nhận, Đang kiểm tra file CAD, Đã báo giá, Đang gia công/In, Hoàn tất, Hủy/Spam); form xử lý thiếu ô nhập "Ghi chú nội bộ (Internal Notes)" và "Giá trị báo giá tạm tính (VNĐ)".

## 2. Mục tiêu (Goals)
- **Tinh giản Sidebar CMS**: Phân chia 4 nhóm chức năng trực quan có tiêu đề phân khu (📊 Bảng điều khiển, 🟢 Vận hành xưởng in 3D, 🎨 Nội dung & Thương hiệu, ⚙️ Hệ thống & Cấu hình). Xóa bỏ 100% các mục menu thừa.
- **Hoàn thiện Tab 1 (Banner)**: Thêm các trường `subtitle`, `primary_cta_text`, `primary_cta_link`, `secondary_cta_text`, `secondary_cta_link` vào `Sliders/Form.vue`, model `Slider`, và truyền dữ liệu banner động vào `HeroSection.vue` trang chủ kèm fallback chuẩn Figma.
- **Hoàn thiện Tab 2 (Sản phẩm)**: Bổ sung chọn `categories` (select multiple), `price`, `compare_at_price`, và bộ 3 thông số kỹ thuật in 3D (`print_technology`, `material`, `lead_time`) vào `Products/Products/Form.vue` và model `Product`.
- **Hoàn thiện Tab 3 (Danh mục)**: Cập nhật `ProductFilter.vue` và `Product/Index.vue` để đồng bộ danh mục động từ backend `ProductCategory`, giữ fallback mượt mà nếu rỗng.
- **Hoàn thiện Tab 4 (Dịch vụ)**: Sửa trường `content` trong `Services/Form.vue` thành `richtext`, nạp `services` trong `HomeController.php` và hiển thị động trên `ServicesSection.vue`.
- **Hoàn thiện Tab 5 (Báo giá & Liên hệ)**: Cập nhật quy trình 6 trạng thái xử lý chuẩn xưởng in 3D trong `Contact.php`, bổ sung ô nhập ghi chú nội bộ và giá trị báo giá tạm tính trong `FormContacts/Form.vue`.
- **Chất lượng & Kiểm thử**: 100% lệnh build `yarn backend:build`, `yarn frontend:build` và `php artisan test tests/Feature/ContactFormTest.php` chạy thành công.
