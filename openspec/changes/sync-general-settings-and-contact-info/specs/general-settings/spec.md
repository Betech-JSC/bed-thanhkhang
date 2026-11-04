# Specification: Đồng bộ Cài đặt chung CMS & Thông tin liên hệ Xưởng in 3D

## 1. Yêu cầu Backend & Admin CMS (Cài đặt chung)

### 1.1. Các trường cài đặt mới trong `General.vue`
Các trường được đặt trong nhóm "Thông tin doanh nghiệp & Liên hệ":
- `general_zalo`:
  + Kiểu: `text`
  + Nhãn: `Số / Link Zalo tư vấn kỹ thuật`
  + Placeholder: `VD: 0906636425 hoặc https://zalo.me/0906636425`
  + Help text: `Khách hàng sẽ bấm vào để chuyển hướng trực tiếp đến ứng dụng Zalo chat.`
- `general_working_hours`:
  + Kiểu: `text`
  + Nhãn: `Giờ làm việc xưởng`
  + Placeholder: `VD: Thứ 2 - Thứ 7: 08:00 - 18:00`
- `general_facebook`:
  + Kiểu: `text`
  + Nhãn: `Link Fanpage Facebook`
  + Placeholder: `https://facebook.com/hhmaker3d`
- `general_tiktok`:
  + Kiểu: `text`
  + Nhãn: `Link Kênh TikTok`
  + Placeholder: `https://tiktok.com/@hhmaker3d`

### 1.2. Khai báo quy tắc lưu trữ trong `Setting.php`
- Cập nhật hàm `rules($id)` cho nhóm `'general'` bổ sung:
  + `'general_zalo' => 'nullable'`
  + `'general_working_hours' => 'nullable'`
  + `'general_facebook' => 'nullable'`
  + `'general_tiktok' => 'nullable'`

### 1.3. Ẩn tab "points" trong `setting_bar()`
- Trong `packages/bed-package-essentials/core/src/helpers.php`:
  + Cập nhật giá trị `'points' => false` trong mảng trả về của hàm `setting_bar()`.

---

## 2. Yêu cầu Phía Frontend (Đồng bộ thông tin & Fallback an toàn)

### 2.1. Footer Component (`Footer.vue`)
- ⚠️ **QUY TẮC BẢO MẬT/BẢO TOÀN DỮ LIỆU**:
  + **GIỮ NGUYÊN 100% email cá nhân `lamhonghai123@gmail.com`** ở mục Liên hệ tư vấn (`mailto:lamhonghai123@gmail.com`). Tuyệt đối không xóa, không sửa thành email khác, không ẩn.
- Kết nối dữ liệu động:
  + Hotline: `$page.props.global?.general_phone`, fallback `0906636425`.
  + Email chung: `$page.props.global?.general_email`, fallback `info@hhmaker.vn`.
  + Địa chỉ: `$page.props.global?.general_address`, fallback `Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh`.
  + Giờ làm việc: `$page.props.global?.general_working_hours`, fallback `Thứ 2 - Thứ 7: 08:00 - 18:00`.
  + Liên kết mạng xã hội Facebook & TikTok: đọc từ `$page.props.global?.general_facebook` (fallback `https://facebook.com`) và `$page.props.global?.general_tiktok` (fallback `https://tiktok.com`).

### 2.2. Trang Liên hệ (`Contact.vue`)
- Thẻ Zalo:
  + URL: Nếu `general_zalo` bắt đầu bằng `http`, dùng trực tiếp; nếu là số điện thoại, nối chuỗi `https://zalo.me/` + số. Fallback: `https://zalo.me/0906636425`.
- Thẻ Hotline:
  + URL: `tel:` + số điện thoại lọc bỏ khoảng trắng. Fallback: `0906636425`.
- Thẻ Email:
  + URL: `mailto:` + email. Fallback: `info@hhmaker.vn`.

### 2.3. Section Liên hệ Trang chủ (`ContactSection.vue`)
- Kết nối icon Facebook với `$page.props.global?.general_facebook` (fallback `https://facebook.com`).
- Kết nối icon TikTok với `$page.props.global?.general_tiktok` (fallback `https://tiktok.com`).
