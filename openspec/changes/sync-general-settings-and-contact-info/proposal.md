# Proposal: Đồng bộ Cài đặt chung CMS & Thông tin liên hệ Xưởng in 3D HH Maker

## 1. Bối cảnh & Vấn đề (Context & Problem)
Hệ thống quản trị Cài đặt chung (General Settings) của HH Maker kế thừa từ core engine trước đây chỉ bao gồm các trường cơ bản như tên doanh nghiệp, tên website, email chung, địa chỉ và số điện thoại. Đối với một xưởng in 3D & gia công tạo mẫu nhanh công nghệ cao, các kênh liên lạc thực tế rất quan trọng nhưng đang bị thiếu trong cấu hình CMS:
1. **Thiếu trường cấu hình Zalo**: Khách hàng gia công in 3D tại Việt Nam chủ yếu gửi file 3D (STL/STEP/OBJ) và trao đổi tiến độ qua Zalo. Hiện CMS chưa có trường `general_zalo`.
2. **Thiếu trường cấu hình Giờ làm việc**: Xưởng in 3D hoạt động theo khung giờ kỹ thuật (tiếp nhận file, vận hành máy in, giao hàng). CMS chưa có trường `general_working_hours`.
3. **Thiếu cấu hình Mạng xã hội**: Chưa có cấu hình Fanpage Facebook (`general_facebook`) và Kênh TikTok (`general_tiktok`) để xưởng quảng bá video in 3D timelapse và các sản phẩm đã hoàn thiện.
4. **Tàn dư tab Cấu hình cũ ("points")**: Trong `packages/bed-package-essentials/core/src/helpers.php` (hàm `setting_bar()`), tab "points" (Cấu hình Tỷ giá 1 Quả cũ của FunkyStyle) vẫn hiển thị gây nhiễu cho quản trị viên xưởng in 3D.
5. **Đồng bộ Frontend & Bảo toàn Email cá nhân**:
   - `resources/Frontend/js/Components/Footer.vue`: Cần hiển thị linh hoạt Hotline, Địa chỉ, Giờ làm việc, Mạng xã hội từ cấu hình hệ thống. **TUYỆT ĐỐI BẢO TỒN 100% EMAIL CÁ NHÂN `lamhonghai123@gmail.com` THEO CHỈ ĐẠO CỦA NGƯỜI DÙNG**.
   - `resources/Frontend/js/Pages/Contact.vue`: 3 thẻ kính liên hệ (Zalo, Hotline, Email) đang hardcode tĩnh, cần kết nối với `$page.props.global` kèm cơ chế fallback an toàn chuẩn xưởng in 3D HH Maker.
   - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`: Các icon mạng xã hội Facebook & TikTok cần được kết nối tới `$page.props.global?.general_facebook` và `general_tiktok`.

## 2. Mục tiêu (Goals)
- Bổ sung 4 trường nhập liệu vào CMS `General.vue` và khai báo validation rules trong `Setting.php` (`general_zalo`, `general_working_hours`, `general_facebook`, `general_tiktok`).
- Ẩn hoàn toàn tab "points" khỏi danh sách tab cài đặt trong `setting_bar()`.
- Đồng bộ thông tin động ra `Footer.vue`, kết nối hotline, address, working hours, facebook, tiktok; **giữ nguyên 100% email cá nhân `lamhonghai123@gmail.com`**.
- Đồng bộ các thẻ liên hệ trong `Contact.vue` và các icon mạng xã hội trong `ContactSection.vue` với `$page.props.global` kèm fallback an toàn.
- Kiểm tra tính toàn vẹn hệ thống: biên dịch backend/frontend và kiểm thử PHPUnit thành công 100%.
