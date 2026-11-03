# Spec Delta: homepage-process

## Purpose

Chuẩn hóa Section "Quy trình làm việc 5 bước" tại Trang chủ theo đúng bản vẽ thiết kế Figma (Node `2261:5723` - `Testimonial / Group`), mang lại trải nghiệm thị giác chuyên nghiệp, hiện đại, tối giản và đồng bộ hệ thống nhận diện thương hiệu HH Maker.

## ADDED Requirements

### Requirement: Bố cục Full-width và Nền Gradient Xanh Điện Tử
Hệ thống SHALL hiển thị Section Quy trình làm việc ở dạng trải rộng toàn chiều ngang (Full-width), chiều cao xấp xỉ 892px trên màn hình Desktop (viewport $\ge$ 1024px/1440px), với nền gradient chuyển màu từ `#111322` (20% ở dưới) lên `#2970FF` (94% ở trên) theo chuẩn Figma Node 2261:5723.

#### Scenario: Hiển thị nền gradient trên Desktop
- **WHEN** người dùng cuộn đến section Quy trình trên Trang chủ ở viewport desktop
- **THEN** section bao phủ toàn bộ chiều rộng khung nhìn với gradient chuyển sắc xanh sang đen navy sâu thẳm, tạo sự liền mạch với các section lân cận.

---

### Requirement: Cột Trái Đồ Họa 3D, Số Khổng Lồ 05 và Tiêu Đề Quy Trình
Hệ thống SHALL hiển thị cột bên trái chiếm ~44% bề ngang (tương ứng 639px trên layout 1440px), bao gồm:
- Lớp nền ảnh cấu trúc 3D lượn sóng (`2261:5724`) với hiệu ứng hòa trộn ánh sáng xanh từ nền gradient.
- Cụm nội dung văn bản (`2261:5727`) lọt lòng với khoảng cách lề trái 144px trên Desktop, bao gồm số khổng lồ "05" kích thước 120px font Inter Bold và tiêu đề quy trình "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER" kích thước 38px, in hoa, màu `#FAFAFA`.

#### Scenario: Căn lề và hiển thị khối 05 bên trái
- **WHEN** màn hình hiển thị ở kích thước Desktop 1440px
- **THEN** số 05 và tiêu đề in hoa nằm gọn gàng bên trái với lề cách mép màn hình 144px, hiển thị sắc nét trên nền cấu trúc 3D lượn sóng.

---

### Requirement: Cột Phải Danh Sách 5 Bước Tối Giản Kèm Icon Mũi Tên và Đường Kẻ Ngang
Hệ thống SHALL hiển thị cột bên phải được ngăn cách với cột trái bởi một đường phân chia dọc 1px tinh tế tại vị trí 639px trên Desktop, với cấu trúc gồm:
- Đoạn văn bản giới thiệu quy trình (16px, line-height 22px, màu `#FAFAFA`).
- Khối danh sách 5 bước quy trình (`2261:5732`) gồm 5 dòng: "Tiếp nhận & Tư vấn", "Phân tích & Báo giá", "Gia công In 3D", "Đóng gói chuẩn quy cách", "Giao hàng tận nơi".
- Mỗi dòng có chiều cao ~65px, đường kẻ ngang phân tách trên đỉnh (`border-t border-[#B5B5B5]/40` hoặc `#B5B5B5`), icon mũi tên hướng lên góc phải (`arrow-up-right` 40x40px) đặt bên trái tiêu đề bước (24px).

#### Scenario: Hiển thị 5 bước quy trình tối giản
- **WHEN** người dùng quan sát cột bên phải của Section Quy trình
- **THEN** 5 bước hiển thị dưới dạng các dòng phân cách ngang thanh mảnh, chữ trắng nổi bật, icon mũi tên đồng nhất.

#### Scenario: Tương tác rê chuột (hover) trên từng bước
- **WHEN** người dùng rê chuột vào một dòng bước quy trình
- **THEN** icon mũi tên chuyển động nhẹ hướng lên phải và tiêu đề phản hồi đổi màu hoặc tăng cường độ sáng để báo hiệu trạng thái tương tác.
