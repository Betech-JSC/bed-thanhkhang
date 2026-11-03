# Proposal: Chuẩn Hóa Section "Quy Trình 5 Bước" Trang Chủ Khớp 100% Theo Figma

## Why
Hiện tại Section "Quy trình làm việc" tại trang chủ (`resources/Frontend/js/Pages/Home/Components/ProcessSection.vue`) đang được hiển thị bên trong một thẻ card `rounded-3xl` bo góc thu hẹp, chia các thẻ bước nhỏ có số `01`-`05` và các khối badge riêng biệt, khác biệt hoàn toàn so với thiết kế Figma chuẩn của Homepage (Node `2261:5723` - `Testimonial / Group`).

Trong thiết kế Figma gốc:
- Toàn bộ section là một khối Full-width cao 892px với nền gradient huyền ảo `linear-gradient(0deg, #111322 20%, #2970FF 94%)`.
- Cột bên trái (chiếm từ x=0 đến x=639px): Hiển thị nền ảnh cấu trúc 3D lượn sóng (`2261:5724`) hòa quyện vào sắc xanh, phía trên là con số khổng lồ **05** (120px) và tiêu đề in hoa **"BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER"** (38px bold) căn lề 144px.
- Ở giữa có đường phân cách dọc 1px sắc nét (`borderColor: #B5B5B5` / border mờ tinh tế) ngăn giữa 2 cột.
- Cột bên phải (từ x=639px đến x=1296px, lọt lòng với lề 144px bên phải): Đoạn mô tả 16px ở trên và danh sách 5 bước quy trình tối giản ở dưới. Mỗi bước quy trình bao gồm icon mũi tên `arrow-up-right` (40x40px) và tiêu đề bước (24px), phân tách bằng các đường kẻ ngang 1px.

Cần chuẩn hóa lại component `ProcessSection.vue` để khớp 100% bản vẽ Figma, đảm bảo phong cách cao cấp, hiện đại và chuẩn Responsive từ Mobile đến Desktop 1440px/1920px.

## What Changes
1. **Bố cục Full-width và Nền Gradient Figma**:
   - Chuyển `section` sang dạng full-width, chiều cao chuẩn ~892px trên Desktop (`min-h-[892px] lg:h-[892px]`), áp dụng chính xác gradient `linear-gradient(0deg, #111322 20%, #2970FF 94%)`.
2. **Cột Trái (Đồ họa 3D + Số 05 & Tiêu đề)**:
   - Sử dụng ảnh cấu trúc 3D sóng gốc từ Figma (`process-left-graphic.png` hoặc overlay ảnh tinh chỉnh) phủ toàn bộ nửa trái, hòa quyện với gradient nền.
   - Text "05" kích thước 120px, font Inter, màu `#FAFAFA`, đậm nét.
   - Tiêu đề "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER" kích thước 38px, chữ hoa, khoảng cách dòng 53px, căn lề trái chuẩn 144px trên Desktop.
3. **Đường Phân Cách Dọc & Khung Chứa Cột Phải**:
   - Đường phân cách dọc 1px tinh tế tại vị trí x=639px trên Desktop.
   - Cột phải có padding-left 41px, cách lề phải 144px.
4. **Cột Phải (Mô tả & Danh sách 5 Bước Tối Giản)**:
   - Đoạn văn bản giới thiệu 16px, line-height 22px, màu `#FAFAFA`.
   - Danh sách 5 bước quy trình tối giản dạng hàng phẳng:
     * Mỗi hàng cao ~65px, có đường kẻ ngang 1px ở trên (`border-t border-[#B5B5B5]/40` hoặc `#B5B5B5`).
     * Icon `arrow-up-right` 40x40px bên trái tiêu đề.
     * Tiêu đề từng bước (24px) màu `#FAFAFA`.
     * Tương tác hover mượt mà với chuyển động nhẹ của icon mũi tên và làm sáng tiêu đề.
5. **Thích Ứng Đa Thiết Bị (Responsive)**:
   - Trên Mobile & Tablet: Chuyển đổi mượt mà sang dạng cột xếp chồng dọc, giữ nguyên tính nhận diện với số 05, nền gradient và các dòng bước quy trình dễ đọc, chạm tương tác thuận tiện.

## Capabilities

### New Capabilities
- `homepage-process`: Đặc tả cấu trúc và hành vi giao diện Section "Quy trình 5 bước" tại Trang chủ chuẩn 100% Figma Node 2261:5723.

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue`
- `resources/Frontend/js/Pages/Home/Index.vue`
- `public/assets/images/homepage/`
- Không ảnh hưởng đến backend hay cơ sở dữ liệu.
