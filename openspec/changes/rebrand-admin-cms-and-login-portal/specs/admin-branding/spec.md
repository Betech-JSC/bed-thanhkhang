# Spec: Đồng bộ Nhận diện Thương hiệu HH Maker & Trang Đăng nhập Admin

## 1. Mục tiêu

Đặc tả 3 yêu cầu cốt lõi nhằm xóa bỏ hoàn toàn tàn dư nhận diện cũ và chuẩn hóa nhận diện thương hiệu HH Maker trong Admin CMS:
1. **Admin Sidebar Logo Rebranding**: Hiển thị logo HH Maker 3D chuẩn xác trên thanh điều hướng bên trái của Admin CMS.
2. **App Title Normalization**: Đồng bộ tên hiển thị hệ thống thành `HH Maker` trong tệp cấu hình và môi trường.
3. **Dark Tech Login Portal Design**: Thiết kế lại trang Đăng nhập theo phong cách Dark Tech xưởng in 3D hiện đại, bảo mật.

---

## 2. Yêu cầu chi tiết

### 2.1. Admin Sidebar Logo Rebranding

- **Vị trí**: `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue`.
- **Yêu cầu hiển thị**:
  - Gỡ bỏ liên kết tới logo cũ `/assets/svg/logo-footer.svg`.
  - Sử dụng logo chính thức của HH Maker: `/assets/images/header/figma-header-logo.svg`.
  - Áp dụng class định dạng: `max-h-10 w-auto object-contain` đảm bảo logo không bị méo, sắc nét và hài hòa với không gian thanh sidebar nền tối (`border-b border-gray-800`).
  - Liên kết bọc quanh logo trỏ về `/` hoặc `route('admin.dashboard.index')` hỗ trợ điều hướng nhanh.
- **Triệt tiêu Cache/Fallback**:
  - Cập nhật nội dung tệp `public/assets/svg/logo-footer.svg` bằng mã vector của logo HH Maker để bảo đảm nếu bất kỳ thành phần nào còn tham chiếu đến tệp này, logo HH Maker vẫn được hiển thị thay vì logo cũ.

### 2.2. App Title Normalization

- **Tệp `.env`**:
  - Đổi biến `APP_NAME` từ `'Betech'` thành `'HH Maker'`.
  - Bảo đảm các biến liên quan như `MAIL_FROM_NAME="${APP_NAME}"` và `VITE_APP_NAME="${APP_NAME}"` kế thừa đồng bộ.
- **Tệp `config/app.php`**:
  - Đổi fallback tên ứng dụng: `'name' => env('APP_NAME', 'HH Maker')`.

### 2.3. Dark Tech Login Portal Design

- **Layout Khách (`Guest.vue`)**:
  - Màu nền chính: Dark Tech `#0B0D19`.
  - Hiệu ứng bàn in gia nhiệt (Heated Bed Mesh Grid): Sử dụng background gradient/pattern dạng lưới tọa độ `linear-gradient` hoặc SVG mesh grid với màu đường kẻ `rgba(64, 73, 104, 0.15)` kích thước ô `40px x 40px` hoặc `24px x 24px`.
  - Căn giữa nội dung toàn màn hình (flexbox căn giữa cả chiều ngang lẫn chiều dọc, `min-h-screen`).
- **Trang Đăng nhập (`Login.vue`)**:
  - Cấu trúc Card:
    - Bo góc `rounded-2xl`, nền Glassmorphism tối `#111322` hoặc `rgba(17, 19, 34, 0.9)`.
    - Viền công nghệ: `border border-[#404968]/50`.
    - Đổ bóng sâu công nghệ (`shadow-2xl shadow-black/80`).
  - Đầu trang (Header Card):
    - Logo HH Maker 3D (`/assets/images/header/figma-header-logo.svg`) hiển thị trang trọng, có hiệu ứng drop-shadow phát sáng nhẹ (`drop-shadow-[0_0_12px_rgba(41,112,255,0.3)]`).
    - Badge trạng thái công nghệ: `HH MAKER WORKSHOP OS • v2.0` với nền `#1A2035`, chữ `#8B9FC7`, kèm chấm LED xanh `#2970FF` có animation nhấp nháy (`animate-pulse`).
    - Tiêu đề chính: `ĐĂNG NHẬP HỆ THỐNG` (chữ in hoa, màu trắng sáng, font chữ hiện đại).
    - Phụ đề: `Hệ thống Quản trị & Điều hành Xưởng in 3D` (màu `#8B9FC7`).
  - Các trường nhập liệu (Inputs):
    - Trường Email: Nền `#0F121F`, viền `#2B334E`, chữ `#F3F4F6`, placeholder `#64748B`. Focus có viền sáng `#2970FF` và shadow nhẹ. Kèm icon bì thư (Email SVG) trực quan.
    - Trường Mật khẩu: Tương tự, kèm icon ổ khóa (Password SVG) trực quan.
    - Hiển thị thông báo lỗi xác thực đỏ công nghệ (`#EF4444`) khi nhập sai hoặc thiếu.
  - Tùy chọn đăng nhập:
    - Checkbox "Ghi nhớ đăng nhập" (Dark theme checkbox với màu active `#2970FF`).
  - Nút bấm gửi (CTA Button):
    - Tiêu đề: `ĐĂNG NHẬP HỆ THỐNG`.
    - Nền: Gradient xanh công nghệ `from-[#2970FF] to-[#1E56CC]` với hiệu ứng hover sáng và active bóng bẩy.
    - Trạng thái loading: Hiển thị spinner SVG khi đang submit và vô hiệu hóa nút (`disabled` khi `form.processing`).
  - Chân trang Card (Footer):
    - Dòng thông tin bảo mật xưởng: `Authorized Personnel Only • Additive Manufacturing & Rapid Prototyping System` (font chữ nhỏ, màu `#64748B`, căn giữa).
