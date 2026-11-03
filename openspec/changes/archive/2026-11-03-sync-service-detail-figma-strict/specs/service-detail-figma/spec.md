# Spec Delta: Chuẩn hóa trang Chi tiết Dịch vụ theo Figma Node 2367:2088

## Purpose

Cung cấp giao diện chuẩn xác pixel-perfect cho trang Chi tiết Dịch vụ theo thiết kế Figma Node 2367:2088, đảm bảo trải nghiệm tương tác trực quan với mục lục bài viết (TOC) thông minh, bố cục 2 cột bất đối xứng, khung minh họa kỹ thuật, dịch vụ liên quan và banner CTA chuyển đổi cao.

## ADDED Requirements

### Requirement: Khung Grid và Hero Showcase (Node 2367:2156)
Giao diện trang Chi tiết Dịch vụ SHALL hiển thị trong container tối đa 1152px trên nền tối `#111322`. Hero section SHALL bao gồm Breadcrumb chuẩn ("Trang chủ / Dịch vụ / [Tên dịch vụ]"), tiêu đề H1 (font Inter, 38px, font-bold 700, line-height 46px, `#FAFAFA`), đoạn dẫn nhập (16px, line-height 26px, `#B9C0D4`), và khung minh họa kỹ thuật kích thước 1152x310px với nền `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`, bo góc 16px. Thẻ minh họa bao gồm ảnh 3D 280x280px bên trái và 2 cột thông tin "BẮT ĐẦU TỪ" và "CÙNG LÀM RÕ" với nhãn 12px `#D1E0FF` và các gạch đầu dòng 20px `#FAFAFA`.

#### Scenario: Hiển thị Breadcrumb và thông tin Hero
- **WHEN** người dùng truy cập trang chi tiết dịch vụ (ví dụ: `/services/thiet-ke-3d`)
- **THEN** hệ thống hiển thị đường dẫn Breadcrumb đúng cấu trúc với màu `#B9C0D4`, tiêu đề H1 đạt kích thước 38px `#FAFAFA` và khung minh họa kỹ thuật 1152x310px nền gradient với ảnh 3D sắc nét.

### Requirement: Sidebar Mục lục (TOC) và Scrollspy (Node 2367:2178)
Giao diện trên Desktop SHALL cung cấp một Sidebar mục lục bài viết cố định (`sticky top-28`) với chiều rộng 248px, nhãn "TRONG BÀI VIẾT NÀY" (13px, font 600, `#B9C0D4`) cùng 4 mục: 01. "Khi nào cần thiết kế 3D?", 02. "Từ ý tưởng tới mô hình", 03. "Bạn cần chuẩn bị gì?", 04. "Kết quả cùng hoàn thiện". Mục đang hiển thị trên màn hình SHALL được kích hoạt trạng thái active với viền trái `border-l-2 border-[#2970FF]` và số/chữ màu `#84ADFF`, các mục khác có viền mờ `border-l border-[#404968]` và chữ `#B9C0D4`. Khi người dùng click vào một mục, trang SHALL cuộn mượt (smooth scroll) đến đúng vị trí section tương ứng.

#### Scenario: Tự động cập nhật mục active khi cuộn trang (Scrollspy)
- **WHEN** người dùng cuộn nội dung bài viết qua từng phần
- **THEN** mục lục tương ứng ở Sidebar tự động chuyển sang trạng thái active với viền xanh `#2970FF` và chữ `#84ADFF` mà không giật lag.

#### Scenario: Nhấp vào mục trong TOC để cuộn trang
- **WHEN** người dùng nhấp chuột vào mục "02 Từ ý tưởng tới mô hình" trên mục lục
- **THEN** trang cuộn mượt mà đưa section "Từ ý tưởng tới mô hình 3D" vào vùng quan sát và mục 02 được highlight active.

### Requirement: Nội dung kỹ thuật 4 phần (Node 2367:2196)
Cột nội dung chi tiết (rộng 840px, cách TOC 64px) SHALL trình bày 4 section kỹ thuật rõ ràng:
1. "Khi nào bạn cần thiết kế 3D?": Tiêu đề H2 28px font 700 `#FAFAFA`, văn bản 16px `#B9C0D4`.
2. "Từ ý tưởng tới mô hình 3D": Quy trình 4 bước thiết kế (Node 2367:2204) với viền trên `1px solid #404968`, badge số bước 40x40px nền `#30374F` chữ `#84ADFF` 15px font 600, tiêu đề bước 19px `#FAFAFA`, mô tả 16px `#B9C0D4`.
3. "Bạn cần chuẩn bị gì?": Danh sách chuẩn bị (Node 2367:2232) dạng thẻ card nền `#171B30`, bo góc 12px, padding 28px với 4 mục checklist mang dấu kiểm "✓" `#84ADFF` 16px, tiêu đề 16px font 600 `#FAFAFA`, mô tả 15px `#B9C0D4`.
4. "Cùng hoàn thiện trước khi gia công": Đoạn văn 16px `#B9C0D4` kèm khối callout lưu ý (Node 2367:2256) viền trái `border-l-2 border-[#2970FF]`, padding-left 20px, nội dung chữ `#B2CCFF` 16px line-height 28px.

#### Scenario: Hiển thị đầy đủ 4 section nội dung kỹ thuật
- **WHEN** người dùng xem toàn bộ cột nội dung bài viết
- **THEN** các section hiển thị đầy đủ, tiêu đề H2 đạt chuẩn 28px, thẻ card checklist nền `#171B30` và khối callout lưu ý hiển thị viền trái `#2970FF` trang nhã không có khung hộp xám thừa.

### Requirement: Khám phá dịch vụ liên quan (Node 2367:2270)
Giao diện trang SHALL hiển thị phần "Dịch vụ liên quan" gồm tiêu đề H2 28px `#FAFAFA` và lưới 2 cột chứa 2 card dịch vụ (kích thước 564x220px, bo góc 12px, viền `1px solid #404968`, hover chuyển sang `#2970FF`). Mỗi card gồm khung thumbnail 180x220px nền gradient `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)` với ảnh minh họa 3D tải từ Figma, tên dịch vụ 24px font 700 `#FAFAFA`, mô tả 14-15px `#B9C0D4` và đường dẫn "Tìm hiểu dịch vụ" kèm mũi tên chuyển động nhẹ khi hover.

#### Scenario: Điều hướng đến dịch vụ liên quan
- **WHEN** người dùng click vào card "Gia công 3D" trong danh sách dịch vụ liên quan
- **THEN** hệ thống điều hướng người dùng tới trang chi tiết của dịch vụ "Gia công 3D" (`/services/gia-cong-3d`).

### Requirement: Banner CTA Kết nối dự án (Node 2352:11794)
Cuối trang SHALL có một Banner CTA kích thước 1152x183px với nền gradient `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`, bo góc 16px, chứa tiêu đề H2 32px font 700 `#FAFAFA`, đoạn mô tả ngắn 16px `#FAFAFA`, và nút bấm "Nhận tư vấn ngay" (kích thước 175x40px, nền `#F5F8FF`, chữ `#111322` 16px font 500, bo góc 8px) dẫn tới trang liên hệ.

#### Scenario: Tương tác với nút kêu gọi hành động (CTA)
- **WHEN** người dùng nhấn vào nút "Nhận tư vấn ngay" trên Banner CTA
- **THEN** hệ thống chuyển hướng người dùng đến trang liên hệ tư vấn (`/contact` hoặc `/lien-he`).
