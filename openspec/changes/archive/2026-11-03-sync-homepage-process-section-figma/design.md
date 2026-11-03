# Design: Chuẩn Hóa Section "Quy Trình 5 Bước" Khớp 100% Bản Vẽ Figma

## 1. Phân Tích Thông Số Thiết Kế Figma (Node `2261:5723`)

Bản vẽ thiết kế Figma có các thông số chính xác như sau:

- **Section Container (`2261:5723`)**:
  * Tên: `Testimonial / Group`
  * Chiều rộng: 1440px (Full-width responsive)
  * Chiều cao: 892px
  * Nền: `linear-gradient(0deg, #111322 20%, #2970FF 94%)`
  * Vị trí: Đặt ngay sau BrandStorySection và trước ContactSection.

- **Cột Trái - Đồ Họa & Banner 05 (`2261:5724` & `2261:5727`)**:
  * Vị trí: Tọa độ x = 0px đến 639px (~44.4% chiều rộng màn hình).
  * Ảnh nền 3D dạng sóng: Node `2261:5726` (`image 47`, masked). Kích thước 639px x 890px.
  * Khối chữ (`2261:5727` - `Frame 241`):
    - Tọa độ x = 144px, y = 242px.
    - Kích thước: Rộng 435px, cao 409px.
    - Chữ số khổng lồ "05": Kích thước font 120px, font Inter, weight 700 (Bold), line-height 144px, màu `#FAFAFA`.
    - Tiêu đề quy trình: "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI\nHH MAKER", font Inter, weight 700, font-size 38px, line-height 53px, chữ in hoa, màu `#FAFAFA`.

- **Đường Phân Cách Dọc (`2261:5730` border-left)**:
  * Vị trí: x = 639px.
  * Border: `1px solid rgba(181, 181, 181, 0.4)` (hoặc `#B5B5B5`), chạy suốt chiều cao khối nội dung phải (cao ~652px).

- **Cột Phải - Nội Dung & 5 Bước Quy Trình (`2261:5730`)**:
  * Tọa độ x = 639px, y = 120px. Tổng chiều rộng 657px, kết thúc tại 1296px (cách lề phải đúng 144px).
  * Khối nội dung lọt lòng có padding-left 41px (chiều rộng khả dụng 616px):
    - **Đoạn mô tả giới thiệu (`2261:5731`)**:
      * Font Inter, size 16px, line-height 22px, màu `#FAFAFA`.
      * Chiều cao ~88px.
    - **Khối danh sách 5 bước (`2261:5732` - `Frame 245`)**:
      * Tọa độ y tương đối = 287px, chiều cao ~365px, chiều rộng 616px.
      * Gồm 5 hàng tương ứng 5 bước:
        1. Tiếp nhận & Tư vấn (`2261:5735`)
        2. Phân tích & Báo giá (`2261:5738`)
        3. Gia công In 3D (`2261:5741`)
        4. Đóng gói chuẩn quy cách (`2261:5744`)
        5. Giao hàng tận nơi (`2261:5747`)
      * Mỗi hàng có:
        - Chiều cao ~65px.
        - Đường kẻ ngang phía trên: `1px solid rgba(181, 181, 181, 0.4)`.
        - Icon mũi tên góc phải `arrow-up-right` 40x40px (stroke `#FCFCFC`, width 17px/40px).
        - Tiêu đề bước: Font Inter, size 24px, line-height 34px, màu `#FAFAFA`.
        - Khoảng cách giữa icon và tiêu đề: 24px (`gap-6`).

## 2. Giải Pháp Kỹ Thuật

- **Bố cục Tailwind / CSS**:
  * Nền: `bg-[linear-gradient(0deg,#111322_20%,#2970FF_94%)]`
  * Khung bao ngoài Desktop: `w-full relative overflow-hidden min-h-[720px] lg:h-[892px] flex items-center`
  * Bên trong chia thành 2 phân vùng theo tỷ lệ grid hoặc flex:
    - Bên trái (639px): Chứa ảnh nền 3D đặt tuyệt đối `absolute inset-0 lg:w-[639px] h-full object-cover mix-blend-luminosity` (hoặc đồ họa masking trực tiếp từ Figma), và lớp nội dung căn chuẩn lề `max-w-[1440px] mx-auto px-6 xl:px-[144px]`.
    - Đường chia dọc: `border-l border-white/20 lg:border-[#B5B5B5]/40`.
    - Bên phải: `flex-1 pl-0 lg:pl-10 space-y-8 lg:space-y-12`.
- **Icon Vector SVG**:
  * Sử dụng file SVG `arrow-up-right.svg` trực tiếp tải từ Figma hoặc component inline SVG chuẩn tỉ lệ để tối ưu hóa hiệu năng render.
- **Tương Tác Hover**:
  * Mỗi hàng bước quy trình có `group cursor-pointer transition-all duration-300`. Khi hover:
    - Mũi tên dịch chuyển nhẹ `translate-x-1 -translate-y-1`.
    - Màu chữ sáng rực hoặc ánh xanh primary nhẹ nhàng.

## 3. Responsive Strategy

- **Mobile (< 768px)**:
  * Section chuyển sang dạng dọc (stacked column).
  * Khối 05 và tiêu đề hiển thị nổi bật ở trên với font-size điều chỉnh (số 05: 72px-80px, tiêu đề: 24px-28px).
  * Ảnh nền chìm mờ phía sau để đảm bảo độ tương phản chữ.
  * Các hàng bước quy trình xếp dọc, co giãn theo chiều rộng màn hình, icon và chữ rõ ràng, dễ chạm bấm.
- **Tablet (768px - 1023px)**:
  * Khoảng cách padding linh hoạt, cỡ chữ 05 ~90px, tiêu đề ~30px.
- **Desktop (1024px - 1440px+)**:
  * Căn chỉnh khớp 100% tọa độ và kích thước từ Figma: lề 144px, phân chia 639px/657px, chiều cao 892px.
