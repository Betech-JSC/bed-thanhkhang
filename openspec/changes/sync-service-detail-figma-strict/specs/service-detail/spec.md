# Spec Delta: service-detail

## Purpose
Đặc tả chi tiết cấu trúc, giao diện và luồng điều hướng chuẩn 100% bản vẽ Figma Node `2367:2088` (Chi tiết dịch vụ) cùng hệ thống liên kết thông minh từ Trang chủ và Trang danh sách dịch vụ tới trang chi tiết `/dich-vu/{slug}`.

## ADDED Requirements

### Requirement: Điều Hướng Thông Minh Dịch Vụ từ Trang Chủ và Trang Danh Sách
Hệ thống SHALL cho phép người dùng điều hướng nhanh chóng, trực quan từ Trang chủ và Trang danh sách dịch vụ sang trang chi tiết từng dịch vụ `/dich-vu/{slug}`:
1. Thẻ dịch vụ trên Trang chủ (`ServicesSection.vue`) SHALL là thẻ `<Link>` có thể nhấp chuột (`cursor-pointer`) trỏ đến `/dich-vu/{slug}` (Ví dụ: `thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`).
2. Khối dịch vụ trên Trang danh sách (`ServiceBlock.vue`) SHALL gắn link trỏ đến `/dich-vu/{slug}` tại:
   - Thẻ minh họa 3D bên trái.
   - Tiêu đề H2 tên dịch vụ.
   - Nút hành động phụ "Tìm hiểu chi tiết →" đặt cạnh nút liên hệ tư vấn.

#### Scenario: Nhấp vào thẻ dịch vụ trên Trang chủ để xem chi tiết
- **WHEN** người dùng đang ở Trang chủ `/` và nhấp vào thẻ "Thiết kế 3D"
- **THEN** trình duyệt chuyển hướng đến `/dich-vu/thiet-ke-3d` hiển thị đầy đủ thông tin chi tiết dịch vụ.

#### Scenario: Nhấp vào nút "Tìm hiểu chi tiết" trên Trang danh sách dịch vụ
- **WHEN** người dùng đang ở `/dich-vu` và nhấp vào nút "Tìm hiểu chi tiết" tại khối dịch vụ "Gia công 3D"
- **THEN** trình duyệt chuyển hướng đến `/dich-vu/gia-cong-3d`.

---

### Requirement: Bố Cục Trang Chi Tiết Dịch Vụ Chuẩn Figma Node 2367:2088
Trang chi tiết dịch vụ (`Show.vue`) SHALL sử dụng nền tối `#111322`, khung lưới nội dung `max-w-[1152px] mx-auto` đối xứng 144px hai bên ở màn hình desktop chuẩn 1440px:
1. Phần mở đầu `ServiceHero.vue` (Node `2367:2156` - 1152x584px):
   - Breadcrumb định dạng: `Trang chủ` / `Dịch vụ` / `[Tên dịch vụ]` với link hợp lệ.
   - Tiêu đề H1 kích thước 38px (dòng cao 46px), font Bold màu `#FAFAFA`.
   - Lời dẫn kích thước 16px (dòng cao 26px), màu `#B9C0D4`.
   - Khung minh họa bài viết (Node `2352:11858` - 1152x310px, bo góc 16px, nền `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`) chia 3 cột:
     * Cột 1: Ảnh 3D kích thước 280x280px với đổ bóng sâu `drop-shadow-[0_16px_32px_rgba(0,0,0,0.6)]`.
     * Cột 2: Cụm "BẮT ĐẦU TỪ" màu `#D1E0FF` 12px cùng danh sách 3 mục ("Ý tưởng", "Ảnh tham khảo", "Bản phác thảo") font 20px `#FAFAFA`.
     * Cột 3: Cụm "CÙNG LÀM RÕ" màu `#D1E0FF` 12px cùng danh sách 3 mục ("Tỉ lệ & hình khối", "Cấu trúc & chi tiết", "Khả năng chế tác") font 20px `#FAFAFA`.

#### Scenario: Kiểm tra bố cục Hero trên màn hình Desktop 1440px
- **WHEN** người dùng truy cập trang `/dich-vu/thiet-ke-3d` trên viewport 1440px
- **THEN** container trang đạt max-width 1152px, Hero hiển thị đầy đủ breadcrumbs, tiêu đề H1 và thẻ showcase 3 cột với nền gradient.

---

### Requirement: Sidebar Mục Lục Sticky và Cuộn Scrollspy Tự Động
Khu vực nội dung bài viết SHALL chia thành 2 cột bất đối xứng theo Figma Node `2367:2177`:
1. Cột trái `ServiceToc.vue` (Node `2367:2178` - rộng 248px):
   - Giữ vị trí sticky (`sticky top-28`) khi cuộn trang.
   - Tiêu đề "TRONG BÀI VIẾT NÀY" (13px, font-semibold, `#B9C0D4`).
   - Danh sách 4 mục điều hướng: 01 "Khi nào cần thiết kế 3D?", 02 "Từ ý tưởng tới mô hình", 03 "Bạn cần chuẩn bị gì?", 04 "Kết quả cùng hoàn thiện".
   - Mục đang xem (Active) hiển thị viền xanh `border-l-2 border-[#2970FF]` và chữ màu `#84ADFF`.
   - Các mục chưa active hiển thị viền `border-l border-[#404968]` và chữ `#B9C0D4`.
   - Nhấp vào mục trong TOC kích hoạt cuộn mượt (`scroll-behavior: smooth`) đến đúng vị trí thẻ heading tương ứng.
2. Trên màn hình Mobile, TOC hiển thị thanh tab ngang dạng pill cuộn ngang mượt mà.

#### Scenario: Cuộn trang kích hoạt Scrollspy trên TOC
- **WHEN** người dùng cuộn trang xuống phần "Bạn cần chuẩn bị gì?"
- **THEN** mục số 03 trên TOC tự động chuyển sang trạng thái active với viền xanh `#2970FF` và chữ `#84ADFF`.

---

### Requirement: Nội Dung Kỹ Thuật 4 Khối Chuẩn Figma Node 2367:2196
Cột phải `ServiceArticleContent.vue` (rộng 840px, cách TOC 64px ở màn hình chuẩn) SHALL hiển thị chính xác 4 khối nội dung:
1. Khối 1 (Node `2367:2197`): "Khi nào bạn cần thiết kế 3D?" với tiêu đề H2 28px/36px Bold `#FAFAFA` và 2 đoạn văn bản làm rõ giá trị.
2. Khối 2 (Node `2367:2201`): "Từ ý tưởng tới mô hình 3D" gồm 4 bước quy trình (01 Trao đổi ý tưởng, 02 Dựng hình khối, 03 Hoàn thiện cấu trúc, 04 Thống nhất phương án), mỗi bước có huy hiệu số bước bo góc 8px nền `#30374F` chữ `#84ADFF`.
3. Khối 3 (Node `2367:2229`): "Bạn cần chuẩn bị gì?" với khung nền card `#171B30`, bo góc 12px, padding 28px, chứa 4 mục checklist kèm dấu tích xanh `✓` (`#84ADFF`).
4. Khối 4 (Node `2367:2253`): "Cùng hoàn thiện trước khi gia công" kèm khối callout viền trái 2px `#2970FF` chứa lưu ý quan trọng về khả năng chế tác thực tế.

#### Scenario: Hiển thị 4 bước quy trình và checklist chuẩn bị
- **WHEN** người dùng đọc nội dung bài viết thiết kế 3D
- **THEN** 4 bước quy trình hiển thị đầy đủ từ 01 đến 04 và bảng checklist chuẩn bị hiển thị nền `#171B30` với 4 tiêu chí rõ ràng.

---

### Requirement: Khối Dịch Vụ Liên Quan và Banner CTA Kết Nối Dự Án
Trang chi tiết SHALL cung cấp đầy đủ liên kết tới các dịch vụ khác và lời kêu gọi hành động:
1. Khối Dịch vụ liên quan `ServiceRelated.vue` (Node `2367:2270`):
   - Tiêu đề H2 "Dịch vụ liên quan" 28px/36px Bold `#FAFAFA`.
   - 2 thẻ dịch vụ (rộng 564px, cao 220px, bo góc 12px, viền `#404968`, hover `#2970FF`).
   - Cột trái card là ảnh thumbnail 180x220px nền gradient `#101121` đến `#2D4BE2`.
   - Cột phải là tên dịch vụ (24px Bold), mô tả tóm tắt và link "Tìm hiểu dịch vụ" kèm icon mũi tên chuyển hướng đúng sang `/dich-vu/{slug}` của dịch vụ tương ứng.
2. Khối Banner CTA `ServiceCta.vue` (Node `2352:11794`):
   - Khung kích thước 1152x183px, nền gradient `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`, bo góc 16px.
   - Tiêu đề "Tạo nên phiên bản mang dấu ấn riêng" 32px Bold `#FAFAFA`.
   - Nút "Nhận tư vấn ngay" 175x40px bo góc 8px, nền `#F5F8FF` chữ `#111322` trỏ về trang liên hệ.

#### Scenario: Nhấp vào dịch vụ liên quan để chuyển đổi qua lại
- **WHEN** người dùng đang ở trang `/dich-vu/thiet-ke-3d` và nhấp vào thẻ "Gia công 3D" trong mục Dịch vụ liên quan
- **THEN** trang chuyển hướng tới `/dich-vu/gia-cong-3d`.
