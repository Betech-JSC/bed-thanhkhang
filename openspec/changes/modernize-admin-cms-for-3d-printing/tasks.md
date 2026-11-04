# Tasks: Chuẩn hóa Admin CMS cho Xưởng In 3D HH Maker

## 1. Khởi tạo OpenSpec Change
- [x] Tạo change `modernize-admin-cms-for-3d-printing` <!-- id: 1.1 -->
- [x] Tạo `proposal.md` phân tích vấn đề và định hướng giải pháp <!-- id: 1.2 -->
- [x] Tạo `specs/admin-cms/spec.md` đặc tả các yêu cầu chi tiết <!-- id: 1.3 -->
- [x] Tạo `design.md` thiết kế kỹ thuật backend & frontend components <!-- id: 1.4 -->
- [x] Tạo `tasks.md` danh sách đầu việc triển khai <!-- id: 1.5 -->

## 2. Triển khai Code (Implementation)
- [x] 2.1. Cập nhật `resources/Backend/js/Pages/FormContacts/Index.vue`: Transform linh hoạt Họ tên, Số điện thoại, Email, Dịch vụ đa dạng key Việt/Anh <!-- id: 2.1 -->
- [x] 2.2. Cập nhật `resources/Backend/js/Pages/FormContacts/Form.vue`: Gỡ bỏ khóa học cũ, hiển thị chi tiết báo giá in 3D & Card tải file CAD 3D (.stl, .step, .obj, .3mf, .zip) <!-- id: 2.2 -->
- [x] 2.3. Cập nhật `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`: Sửa endpoint sang `route('contact.store')` (`/contacts`) và payload `CONTACT_FORM` <!-- id: 2.3 -->
- [x] 2.4. Cập nhật `resources/Backend/js/Components/SidebarMain.vue`: Thêm "Danh mục sản phẩm" và "Đơn hàng", xóa 5 menu tàn dư FunkyStyle / link hỏng <!-- id: 2.4 -->
- [x] 2.5. Cập nhật `app/Http/Controllers/Backend/DashboardController.php`: Bỏ logic `TYPE_CHECKOUT_COURSE`, bổ sung thống kê báo giá in 3D, cơ cấu dịch vụ và biểu đồ xu hướng <!-- id: 2.5 -->
- [x] 2.6. Cập nhật `resources/Backend/js/Pages/Dashboard.vue`: Gỡ bỏ phân tích học viên cũ, xây dựng dashboard hiện đại đo lường chỉ số in 3D HH Maker <!-- id: 2.6 -->

## 3. Xác minh & Kiểm thử (Verification)
- [x] 3.1. Chạy `yarn backend:build` xác nhận Admin CMS biên dịch không lỗi (Exit code 0) <!-- id: 3.1 -->
- [x] 3.2. Chạy `yarn frontend:build` xác nhận Frontend biên dịch không lỗi (Exit code 0) <!-- id: 3.2 -->
- [x] 3.3. Chạy `php artisan test tests/Feature/ContactFormTest.php` kiểm tra tính ổn định nghiệp vụ lưu form liên hệ <!-- id: 3.3 -->
- [x] 3.4. Đánh dấu hoàn tất toàn bộ task `[x]` trong `tasks.md` <!-- id: 3.4 -->
- [x] 3.5. Báo cáo bằng chứng terminal output và git diff cho agent điều phối <!-- id: 3.5 -->
