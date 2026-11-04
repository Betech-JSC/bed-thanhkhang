# Spec Delta: homepage

## Purpose

Cung cấp và chuẩn hóa toàn bộ các phân đoạn giao diện Trang chủ HH Maker theo phong cách Dark Tech từ bản thiết kế Figma (Node 2261:5474 gồm Hero 2261:5475 và Frame 236 2261:5502), đảm bảo tính thẩm mỹ công nghệ cao, khả năng tương tác mượt mà và tương thích hiển thị trên mọi thiết bị.

## ADDED Requirements

### Requirement: Hero Section chuẩn Dark Tech theo thiết kế Figma
Hệ thống SHALL hiển thị khối Hero Banner tại Trang chủ với kích thước khung thẻ tối đa `1279px x 616px` (`rounded-[16px] overflow-hidden bg-[#0B0D19] border border-white/10 shadow-2xl`), tích hợp hình nền công nghệ in 3D (`image 8`), phụ đề chữ hoa màu `#D1E0FF`, tiêu đề H1 kích thước 38px line-height 46px in hoa màu `#FAFAFA` ("HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D"), đoạn văn bản mô tả 16px line-height 26px, cụm nút bấm kép ("Khám phá ngay" và "Nhận tư vấn thiết kế"), và một huy hiệu xoay tròn SVG kích thước `114px x 114px` chạy dòng chữ "FROM IDEAS TO REALITY - FROM IDEAS TO REALITY - " bao quanh biểu tượng mũi tên 45° trong suốt.

#### Scenario: Hiển thị Hero Banner đầy đủ trên Desktop
- **WHEN** người dùng truy cập Trang chủ trên màn hình Desktop (viewport $\ge$ 1024px)
- **THEN** thẻ Hero hiển thị cân đối giữa trang với độ rộng 1279px, hình nền 3D rõ nét, tiêu đề H1 màu trắng nổi bật, cụm nút bấm phản hồi hover, và huy hiệu tròn xoay liên tục góc dưới bên phải.

#### Scenario: Hiển thị Hero Banner tối ưu trên Mobile
- **WHEN** người dùng truy cập Trang chủ trên thiết bị di động (viewport < 768px)
- **THEN** thẻ Hero tự động co giãn chiều rộng 100%, lề padding hai bên thu gọn 16px, kích thước font chữ H1 tự co về 28px - 32px vừa vặn, và huy hiệu xoay tròn được ẩn hoặc thu nhỏ thẩm mỹ mà không gây tràn khung ngang.

---

### Requirement: Featured Products Carousel với tương tác Swiper / Scrollable Track
Hệ thống SHALL cung cấp phân đoạn "SẢN PHẨM NỔI BẬT" (Figma Node `2261:5503`) với tiêu đề H2 kích thước 38px căn giữa, cùng thanh trượt các sản phẩm tiêu biểu có kích thước thẻ chuẩn `264px x 419px` bo góc `12px`. Mỗi thẻ bao gồm phần hình ảnh sản phẩm phía trên (cao 335px nền tối `#0A0D14`) và chân thẻ thông tin phía dưới (cao 84px nền `#30374F` chứa tên sản phẩm và mô tả ngắn). Hệ thống cung cấp hai nút điều hướng tròn kính mờ ở hai bên để cuộn trượt mượt mà.

#### Scenario: Cuộn xem các sản phẩm nổi bật qua nút điều hướng
- **WHEN** người dùng nhấn vào nút mũi tên trượt sang phải (Next Slide)
- **THEN** thanh trượt cuộn mượt mà sang phải hiển thị các thẻ sản phẩm tiếp theo, và nút điều hướng bên trái (Prev Slide) tự động hiển thị để hỗ trợ cuộn ngược lại.

#### Scenario: Tương tác vuốt chạm trên màn hình cảm ứng di động
- **WHEN** người dùng vuốt ngón tay sang trái hoặc phải trên thanh trượt ở thiết bị cảm ứng
- **THEN** danh sách sản phẩm trượt theo quán tính tự nhiên với cơ chế native touch scroll / Swiper, không giật lag.

---

### Requirement: Showcase Năng Lực & Dịch Vụ In 3D
Hệ thống SHALL hiển thị khối tổng quan năng lực và dịch vụ gia công in 3D (Figma Node `2261:5528`) với tiêu đề "TỔNG QUAN VỀ DỊCH VỤ", bố cục lưới các gói dịch vụ cốt lõi (In sợi nhựa FDM, In nhựa lỏng SLA độ nét cao, Hoàn thiện & Sơn bề mặt, Thiết kế mô hình 3D). Mỗi thẻ dịch vụ áp dụng hiệu ứng kính mờ Dark Tech, viền mảnh `dark-border`, điểm nhấn dải màu xanh thương hiệu `#2970FF`, danh sách ưu điểm kỹ thuật và nút liên kết trực tiếp đến trang chi tiết dịch vụ.

#### Scenario: Xem chi tiết dịch vụ từ khối Showcase Trang chủ
- **WHEN** người dùng nhấp vào một thẻ dịch vụ hoặc nút "Tìm hiểu thêm"
- **THEN** hệ thống điều hướng trực tiếp sang trang chi tiết dịch vụ tương ứng (`/services/{slug}`) qua Inertia mà không tải lại toàn trang.

#### Scenario: Hiệu ứng hover nổi khối thẻ dịch vụ
- **WHEN** người dùng di chuột qua một thẻ dịch vụ trên Desktop
- **THEN** thẻ dịch vụ nâng nhẹ lên (`-translate-y-1.5`) kèm ánh sáng viền xanh công nghệ (`shadow-glow-primary-subtle`), icon dịch vụ biến đổi ánh sáng nhẹ.

---

### Requirement: Danh Mục Sản Phẩm Mẫu Phân Tầng 3 Hàng
Hệ thống SHALL hiển thị phân đoạn danh mục sản phẩm (Figma Node `2261:5563`) gồm 3 hàng carousel đại diện cho 3 nhóm sản phẩm chủ lực: Móc khoá & Phụ kiện công nghệ, Đồ Decor & Quà tặng chế tác, và Gia công kỹ thuật số công nghiệp. Mỗi hàng có tiêu đề nhóm riêng biệt, liên kết "Xem tất cả" sang trang sản phẩm (`/san-pham`), và danh sách thẻ sản phẩm hiển thị ảnh chụp sắc nét kèm giá thành / thông số vật liệu.

#### Scenario: Hiển thị 3 hàng danh mục sản phẩm độc lập
- **WHEN** người dùng cuộn tới phân đoạn danh mục sản phẩm tại Trang chủ
- **THEN** 3 hàng sản phẩm hiển thị rõ ràng theo từng nhóm phân loại, mỗi hàng có thanh cuộn hoặc carousel độc lập không ảnh hưởng lẫn nhau.

#### Scenario: Điều hướng đến trang bộ lọc danh mục
- **WHEN** người dùng nhấp vào nút "Xem tất cả" của một hàng danh mục
- **THEN** hệ thống điều hướng sang trang danh mục sản phẩm với tham số lọc danh mục tương ứng được kích hoạt.

---

### Requirement: Khối Câu Chuyện Thương Hiệu Brand Story & Năng Lực Chế Tác
Hệ thống SHALL hiển thị phân đoạn Brand Story (Figma Node `2261:5696`) với bố cục 2 cột đáp ứng: cột hình ảnh bên trái thể hiện chân dung nhà sáng lập / kỹ thuật viên bên dàn máy in 3D hiện đại, và cột nội dung bên phải mang tiêu đề "HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH", phụ đề "CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER", các đoạn văn tự sự truyền cảm hứng và các chỉ số cam kết chất lượng (độ chính xác sai số < 0.1mm, thời gian giao hàng 24-48h, vật liệu nhập khẩu chính hãng).

#### Scenario: Hiển thị khối Brand Story trên màn hình lớn
- **WHEN** người dùng xem khối Brand Story trên màn hình máy tính
- **THEN** hình ảnh và khối nội dung phân bố cân xứng ở 2 bên với khoảng cách rãnh 48px, các chỉ số cam kết nổi bật với màu chữ xanh `#2970FF`.

#### Scenario: Hiển thị khối Brand Story trên màn hình di động
- **WHEN** người dùng xem khối Brand Story trên thiết bị di động
- **THEN** bố cục tự động chuyển sang dạng xếp chồng dọc 1 cột, hình ảnh nằm trên và nội dung tự sự nằm dưới, khoảng cách lề thu hẹp hợp lý.

---

### Requirement: Khối Kết Nối Tư Vấn & Báo Giá Nhanh
Hệ thống SHALL cung cấp phân đoạn kết nối tư vấn (Figma Node `2261:5748`) ở cuối Trang chủ trước chân trang, gồm tiêu đề "KẾT NỐI VỚI HH MAKER", đoạn mô tả ngắn, danh sách các kênh kết nối trực tiếp (Hotline, Zalo, Email, Fanpage) và biểu mẫu gửi yêu cầu báo giá nhanh (Họ tên, Số điện thoại, Email, Mô tả yêu cầu, đính kèm nhu cầu in 3D) sử dụng các component chuẩn `BaseInput` và `BaseButton`.

#### Scenario: Gửi yêu cầu tư vấn thành công từ Trang chủ
- **WHEN** người dùng điền đầy đủ thông tin hợp lệ vào form và nhấn "Gửi yêu cầu"
- **THEN** hệ thống kích hoạt gửi dữ liệu đến backend qua Inertia POST, nút bấm chuyển sang trạng thái loading với spinner quay tròn, và hiển thị thông báo phản hồi thành công sau khi hoàn tất.

#### Scenario: Báo lỗi khi thiếu thông tin bắt buộc
- **WHEN** người dùng bấm gửi khi chưa nhập Số điện thoại hoặc Họ tên
- **THEN** các trường nhập liệu tương ứng hiển thị viền đỏ cảnh báo và thông báo lỗi hợp lệ hóa bên dưới trường nhập.

---

### Requirement: Tối Ưu Đáp Ứng Responsive & Kiểm Thử Playwright Visual QA
Toàn bộ các phân đoạn trên Trang chủ SHALL hiển thị chuẩn mực trên mọi dải màn hình từ 375px đến 1440px+, hoàn toàn không xuất hiện lỗi thanh cuộn ngang ngoài ý muốn (`document.documentElement.scrollWidth <= window.innerWidth`). Tất cả các nút bấm và liên kết điều hướng SHALL có vùng chạm tối thiểu 44x44px trên thiết bị cảm ứng. Hệ thống SHALL đi kèm kịch bản kiểm thử tự động Playwright E2E (`tests/e2e/homepage.spec.ts`) xác thực cấu trúc DOM, hiển thị ảnh banner và không có lỗi console JavaScript.

#### Scenario: Kiểm thử E2E Trang chủ trên Desktop 1440px
- **WHEN** kịch bản Playwright E2E chạy ở chế độ Desktop Chrome (1440x900)
- **THEN** bài test kiểm tra Hero Banner, Featured Carousel, Service Showcase, Product Categories, Brand Story và Contact Form hiển thị đầy đủ, không có lỗi console nghiêm trọng.

#### Scenario: Kiểm thử E2E Trang chủ trên Mobile 375px
- **WHEN** kịch bản Playwright E2E chạy ở chế độ Mobile Chrome (375x667)
- **THEN** bài test xác nhận trang hiển thị mượt mà, không có hiện tượng vỡ khung hay tràn chiều ngang, và các nút thao tác dễ bấm.
