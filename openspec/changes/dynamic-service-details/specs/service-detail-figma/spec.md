# Spec Delta: service-detail-figma

## MODIFIED Requirements

### Requirement: Khung Grid và Hero Showcase (Node 2367:2156)
Giao diện trang Chi tiết Dịch vụ SHALL hiển thị trong container tối đa 1152px trên nền tối `#111322`. Hero section SHALL bao gồm Breadcrumb chuẩn ("Trang chủ / Dịch vụ / [Tên dịch vụ]"), tiêu đề H1 kết hợp phụ đề đặc trưng theo từng dịch vụ (38px font-bold 700 `#FAFAFA`), đoạn dẫn nhập mô tả dịch vụ (16px, line-height 26px, `#B9C0D4`), và khung minh họa kỹ thuật kích thước 1152x310px với nền `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`, bo góc 16px. Thẻ minh họa bao gồm ảnh thumbnail dịch vụ 280x280px bên trái và 2 cột thông tin "BẮT ĐẦU TỪ" và "CÙNG LÀM RÕ" với nhãn 12px `#D1E0FF` cùng các gạch đầu dòng 20px `#FAFAFA` được truyền động theo từng dịch vụ (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`) và theo ngôn ngữ hiển thị (VI / EN).

#### Scenario: Hiển thị Breadcrumb và thông tin Hero
- **WHEN** người dùng truy cập trang chi tiết dịch vụ (ví dụ: `/services/thiet-ke-3d`)
- **THEN** hệ thống hiển thị đường dẫn Breadcrumb đúng cấu trúc với màu `#B9C0D4`, tiêu đề H1 đạt kích thước 38px `#FAFAFA` và khung minh họa kỹ thuật 1152x310px nền gradient với ảnh 3D sắc nét.

#### Scenario: Hiển thị Hero động cho dịch vụ Thiết kế 3D (thiet-ke-3d)
- **WHEN** người dùng truy cập trang `/dich-vu/thiet-ke-3d` (hoặc `/en/services/3d-design`)
- **THEN** tiêu đề H1 hiển thị đúng định dạng `Thiết kế 3D: Từ phác thảo đến hình khối rõ ràng` (EN: `3D Design: From sketch to concrete geometry`), cột "BẮT ĐẦU TỪ" hiển thị đúng 3 mục (Ý tưởng, Ảnh tham khảo, Bản phác thảo), và cột "CÙNG LÀM RÕ" hiển thị đúng 3 mục (Tỉ lệ & hình khối, Cấu trúc & chi tiết, Khả năng chế tác).

#### Scenario: Hiển thị Hero động cho dịch vụ Gia công 3D (gia-cong-3d)
- **WHEN** người dùng truy cập trang `/dich-vu/gia-cong-3d` (hoặc `/en/services/3d-machining`)
- **THEN** tiêu đề H1 hiển thị đúng định dạng `Gia công 3D: Hiện thực hóa mô hình với độ chính xác cao` (EN: `3D Machining & Printing: Bringing digital models into reality with high precision`), cột "BẮT ĐẦU TỪ" hiển thị đúng 3 mục về kỹ thuật gia công (File 3D STL/STEP, Kích thước & độ bền, Số lượng cần làm), và cột "CÙNG LÀM RÕ" hiển thị thông tin công nghệ (Công nghệ & vật liệu in, Độ mịn & độ dày thành, Phương án hậu kỳ bề mặt).

#### Scenario: Hiển thị Hero động cho dịch vụ Làm đồ custom (lam-do-custom)
- **WHEN** người dùng truy cập trang `/dich-vu/lam-do-custom` (hoặc `/en/services/custom-creations`)
- **THEN** tiêu đề H1 hiển thị đúng định dạng `Làm đồ custom: Chế tác cá nhân hóa độc bản theo yêu cầu` (EN: `Custom Bespoke Creations: Personalized one-of-a-kind fabrication`), cột "BẮT ĐẦU TỪ" hiển thị 3 mục cá nhân hóa (Câu chuyện & ý tưởng riêng, Chữ/Logo cá nhân hóa, Kỷ vật hoặc mẫu có sẵn), và cột "CÙNG LÀM RÕ" hiển thị thông tin chế tác (Chất liệu & phối màu, Chi tiết khắc / gắn logo, Phong cách hoàn thiện).

### Requirement: Sidebar Mục lục (TOC) và Scrollspy (Node 2367:2178)
Giao diện trên Desktop SHALL cung cấp một Sidebar mục lục bài viết cố định (`sticky top-28`) với chiều rộng 248px, nhãn "TRONG BÀI VIẾT NÀY" (13px, font 600, `#B9C0D4`) cùng 4 mục TOC tương ứng với 4 section kỹ thuật của từng dịch vụ cụ thể. Mỗi mục TOC bao gồm số thứ tự (01, 02, 03, 04), tiêu đề đầy đủ và tiêu đề rút gọn (shortTitle) được ánh xạ động theo dữ liệu dịch vụ hiện tại. Mục đang nằm trong viewport SHALL được kích hoạt trạng thái active với viền trái `border-l-2 border-[#2970FF]` và số/chữ màu `#84ADFF`, các mục khác có viền mờ `border-l border-[#404968]` và chữ `#B9C0D4`. Khi người dùng click vào một mục, trang SHALL cuộn mượt (smooth scroll) đến đúng vị trí section tương ứng.

#### Scenario: Tự động cập nhật mục active khi cuộn trang (Scrollspy)
- **WHEN** người dùng cuộn nội dung bài viết qua từng phần
- **THEN** mục lục tương ứng ở Sidebar tự động chuyển sang trạng thái active với viền xanh `#2970FF` và chữ `#84ADFF` mà không giật lag.

#### Scenario: Nhấp vào mục trong TOC để cuộn trang
- **WHEN** người dùng nhấp chuột vào mục "02 Từ ý tưởng tới mô hình" trên mục lục
- **THEN** trang cuộn mượt mà đưa section "Từ ý tưởng tới mô hình 3D" vào vùng quan sát và mục 02 được highlight active.

#### Scenario: Hiển thị mục lục TOC khớp với nội dung dịch vụ Gia công 3D
- **WHEN** người dùng truy cập trang `/dich-vu/gia-cong-3d`
- **THEN** TOC Sidebar hiển thị 4 mục: 01. "Khi nào bạn cần gia công 3D?", 02. "Quy trình gia công chuẩn xác", 03. "Thông số kỹ thuật cần chuẩn bị", 04. "Hậu kỳ & Tiêu chuẩn hoàn thiện", không hiển thị tiêu đề của dịch vụ Thiết kế 3D.

#### Scenario: Hiển thị mục lục TOC khớp với nội dung dịch vụ Làm đồ custom
- **WHEN** người dùng truy cập trang `/dich-vu/lam-do-custom`
- **THEN** TOC Sidebar hiển thị 4 mục: 01. "Đồ custom dành cho ai?", 02. "Các bước chế tác độc bản", 03. "Bạn chuẩn bị thông tin gì?", 04. "Bàn giao & Bảo quản sản phẩm", đồng bộ chính xác với ID của các section trên trang.

### Requirement: Nội dung kỹ thuật 4 phần (Node 2367:2196)
Cột nội dung chi tiết (rộng 840px, cách TOC 64px) SHALL trình bày 4 section kỹ thuật rõ ràng được truyền từ Backend (hoặc phân giải nội dung theo từng dịch vụ), loại bỏ hoàn toàn các chuỗi text hardcode tĩnh:
1. Section 01 (Tổng quan & Khi nào cần): Tiêu đề H2 28px font 700 `#FAFAFA`, văn bản mô tả 16px `#B9C0D4` giải thích đúng bài toán và giá trị thực tế của dịch vụ tương ứng.
2. Section 02 (Quy trình thực hiện): Tiêu đề H2 28px, đoạn dẫn nhập 16px `#B9C0D4` và danh sách 4 bước triển khai riêng biệt của dịch vụ đó, mỗi bước có viền trên `1px solid #404968`, badge số bước 40x40px nền `#30374F` chữ `#84ADFF`, tiêu đề bước 19px `#FAFAFA`, mô tả 16px `#B9C0D4`.
3. Section 03 (Danh mục chuẩn bị): Tiêu đề H2 28px, thẻ card nền `#171B30` bo góc 12px padding 28px chứa 4 mục checklist chuẩn bị đặc thù của dịch vụ với dấu kiểm "✓" `#84ADFF` 16px, tiêu đề 16px font 600 `#FAFAFA`, mô tả 15px `#B9C0D4`.
4. Section 04 (Hoàn thiện & Lưu ý chế tác): Tiêu đề H2 28px, đoạn văn giải thích kết quả bàn giao kèm khối callout lưu ý viền trái `border-l-2 border-[#2970FF]` padding-left 20px, nội dung chữ `#B2CCFF` 16px line-height 28px mang tính khuyến cáo chuyên môn cho dịch vụ đó.

#### Scenario: Hiển thị đầy đủ 4 section nội dung kỹ thuật
- **WHEN** người dùng xem toàn bộ cột nội dung bài viết
- **THEN** các section hiển thị đầy đủ, tiêu đề H2 đạt chuẩn 28px, thẻ card checklist nền `#171B30` và khối callout lưu ý hiển thị viền trái `#2970FF` trang nhã không có khung hộp xám thừa.

#### Scenario: Hiển thị 4 section kỹ thuật cho dịch vụ Gia công 3D
- **WHEN** người dùng duyệt nội dung bài viết kỹ thuật của `/dich-vu/gia-cong-3d`
- **THEN** Section 01 hiển thị tổng quan về gia công & in 3D; Section 02 hiển thị 4 bước từ kiểm tra file đến hậu kỳ; Section 03 hiển thị thẻ checklist thông số kỹ thuật (file, vật liệu, dung sai, bề mặt); Section 04 hiển thị tiêu chuẩn dung sai kèm callout lưu ý về co ngót vật liệu và hướng layer.

#### Scenario: Hiển thị 4 section kỹ thuật cho dịch vụ Làm đồ custom
- **WHEN** người dùng duyệt nội dung bài viết kỹ thuật của `/dich-vu/lam-do-custom`
- **THEN** Section 01 hiển thị đối tượng khách hàng của đồ custom; Section 02 hiển thị 4 bước chế tác độc bản; Section 03 hiển thị thẻ checklist thông tin cá nhân hóa (ý tưởng, logo, kích thước, màu sắc); Section 04 hiển thị tiêu chuẩn bàn giao kèm callout bảo quản sản phẩm thủ công.

#### Scenario: Hiển thị song ngữ Tiếng Anh đầy đủ khi truy cập qua tiền tố /en
- **WHEN** người dùng truy cập bất kỳ trang dịch vụ nào với ngôn ngữ Tiếng Anh (ví dụ: `/en/services/3d-machining` hoặc `/en/services/custom-creations`)
- **THEN** toàn bộ tiêu đề H2, văn bản mô tả, các bước trong quy trình, checklist và callout của 4 section đều được hiển thị 100% bằng Tiếng Anh chuẩn xác, không bị lẫn bất kỳ chuỗi Tiếng Việt nào.

## ADDED Requirements

### Requirement: Cơ chế phân giải dữ liệu nội dung dịch vụ (Backend Service Content Resolution)
Backend hệ thống SHALL cung cấp cơ chế phân giải dữ liệu nội dung dịch vụ đầy đủ cho cả 3 dịch vụ chính thức (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`) thông qua `app/Models/Service.php` và Controller, đảm bảo nếu trường dữ liệu trong database chưa có thì hệ thống tự động fallback về bộ nội dung chuẩn nghiệp vụ song ngữ của chính dịch vụ đó, thay vì fallback về dịch vụ Thiết kế 3D. Đồng thời, hệ thống SHALL cung cấp Seeder an toàn (`ServiceContentSeeder`) để lưu trữ dữ liệu có cấu trúc vào bảng `service_translations` phục vụ quản trị CMS.

#### Scenario: Phân giải dữ liệu fallback đúng slug khi database thiếu trường chi tiết
- **WHEN** một dịch vụ như `gia-cong-3d` hoặc `lam-do-custom` chưa được nhập `content_by_tab` hoặc `benefits` trong cơ sở dữ liệu
- **THEN** phương thức phân giải dữ liệu của Backend trả về đầy đủ các bước (`steps`), danh mục kiểm tra (`checklist_items`), dữ liệu hero và bài viết của đúng dịch vụ đó, tuyệt đối không trả về nội dung của Thiết kế 3D.
