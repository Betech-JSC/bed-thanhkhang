# product-catalog Specification

## Purpose
Cung cấp giao diện Danh sách Sản phẩm chuẩn Figma Option 3 (Node 2346:669) với bố cục Sidebar 240px gồm 2 Card lọc chuyên biệt, khu vực lưới sản phẩm 3 cột 867px kèm thanh điều hướng Breadcrumb/Sort, và Banner kêu gọi sáng tạo cuối trang.

## Requirements

### Requirement: Bố cục Sidebar 240px gồm 2 Card lọc độc lập
Hệ thống SHALL cung cấp thanh Sidebar cố định độ rộng 240px bên trái khu vực hiển thị sản phẩm trên màn hình Desktop (và dưới dạng ngăn kéo trượt trên Mobile), bao gồm 2 thẻ tách biệt với khoảng cách 12px theo chuẩn Figma Node 2346:995:
- Thẻ 1 (`2346:996`): Danh mục sản phẩm (chiều cao 464px, nền `#30374F`, bo góc 12px) gồm ô tìm kiếm bo góc 8px kèm icon kính lúp, danh sách 4 danh mục ("Tất cả sản phẩm", "Móc khoá", "Văn phòng phẩm", "Gia công theo yêu cầu"), và nhóm "LỌC THEO NHU CẦU" với nhãn văn bản bên trái và ô checkbox 16x16px bên phải.
- Thẻ 2 (`2346:998`): Tuỳ chỉnh sản phẩm (chiều cao 156px, nền `#30374F`, bo góc 12px) gồm tiêu đề 14px, phụ đề 13px, và nút CTA "Liên hệ tư vấn" dạng gradient xanh chuyển tiếp sang trang liên hệ.

#### Scenario: Hiển thị 2 card lọc độc lập trên Desktop
- **WHEN** người dùng truy cập trang `/san-pham` trên màn hình máy tính (viewport $\ge$ 1024px)
- **THEN** Sidebar bên trái hiển thị rõ ràng 2 khối card riêng biệt với nền `#30374F`, bo góc 12px và khoảng cách giữa hai card là 12px.

#### Scenario: Tương tác lọc theo nhu cầu với checkbox bên phải
- **WHEN** người dùng nhấp chọn một mục trong phần "LỌC THEO NHU CẦU"
- **THEN** ô checkbox ở góc phải chuyển trạng thái active nền xanh `#2970FF`, và danh sách sản phẩm bên phải được lọc tương ứng theo nhu cầu đã chọn.

---

### Requirement: Lưới sản phẩm 3 cột 867px kèm Toolbar và Phân trang
Hệ thống SHALL hiển thị khu vực sản phẩm bên phải với tổng độ rộng 867px trên Desktop theo chuẩn Figma Node 2346:1015, bao gồm:
- Thanh Toolbar trên cùng cao 20px với Breadcrumb ("Trang chủ > Sản phẩm (N sản phẩm)") bên trái và bộ chọn Sắp xếp bên phải.
- Lưới sản phẩm 3 cột (`grid-cols-3`, khoảng cách 24px) với mỗi thẻ sản phẩm có kích thước chuẩn 273px x 419px, bao gồm ảnh showcase 335px nền `#D1D2D2` và thanh thông tin dưới 84px nền `#30374F`.
- Thanh điều hướng phân trang ở cuối lưới hiển thị nút "Trang trước" và "Trang tiếp theo" kèm icon chevron chuẩn Figma Node 2352:9091.

#### Scenario: Hiển thị lưới sản phẩm 3 cột trên Desktop
- **WHEN** danh sách sản phẩm có dữ liệu hiển thị trên Desktop
- **THEN** các thẻ sản phẩm được sắp xếp đều đặn trên lưới 3 cột với khoảng cách giữa các thẻ là 24px, mỗi thẻ cao 419px.

#### Scenario: Chuyển trang qua thanh điều hướng
- **WHEN** người dùng nhấn vào nút "Trang tiếp theo"
- **THEN** hệ thống chuyển sang trang tiếp theo và cập nhật danh sách sản phẩm tương ứng mà không làm giật trang.

---

### Requirement: Banner CTA Lời mời sáng tạo 1152px
Hệ thống SHALL hiển thị khối Banner CTA "Lời mời sáng tạo" ở cuối trang theo chuẩn Figma Node 2346:741 với chiều rộng 1152px, chiều cao tối thiểu 183px, nền gradient xanh `#2970FF` đến `#00359E`, bo góc 16px, chứa tiêu đề 32px Bold và nút hành động "Nhận tư vấn ngay" nền trắng kem `#F5F8FF`.

#### Scenario: Hiển thị Banner CTA cuối trang sản phẩm
- **WHEN** người dùng cuộn xuống dưới cùng của trang sản phẩm
- **THEN** banner CTA hiển thị nổi bật với nền gradient xanh, tiêu đề "Tạo nên phiên bản mang dấu ấn riêng" và nút bấm dẫn đến trang `/lien-he`.
