# Spec Delta: home-and-services

## MODIFIED Requirements

### Requirement: Từ điển UI chuyên biệt cho Trang chủ và Dịch vụ in 3D
Hệ thống từ điển giao diện `resources/lang/{vi,en}/ui.php` SHALL cung cấp đầy đủ các cặp khóa - giá trị song ngữ (Tiếng Việt và Tiếng Anh) cho toàn bộ các phân hệ Trang chủ và Dịch vụ:
1. Trang chủ:
   - Eyebrows & Slogans: "ĐƯA Ý TƯỞNG RA NGOÀI THỰC TẾ" <-> "FROM IDEAS TO REALITY", "SẢN PHẨM NỔI BẬT" <-> "FEATURED PRODUCTS", "TỔNG QUAN VỀ DỊCH VỤ" <-> "SERVICES OVERVIEW", "CÂU CHUYỆN THƯƠNG HIỆU" <-> "BRAND STORY", "CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER" <-> "FROM HH MAKER FOUNDER", "HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH" <-> "BRINGING CREATIVITY BEYOND THE SCREEN", "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER" <-> "STEPS IN THE PROFESSIONAL & TRANSPARENT WORKFLOW AT HH MAKER", "KẾT NỐI VỚI HH MAKER" <-> "CONNECT WITH HH MAKER"...
   - Nút hành động & CTA: "Khám phá ngay" <-> "Explore Now", "Nhận tư vấn thiết kế" <-> "Request Design Consultation", "Xem thêm sản phẩm khác" <-> "View More Products", "Xem tất cả dịch vụ" <-> "View All Services", "Gửi thông tin" <-> "Submit"...
   - Chỉ số hoạt động: "Năm hoạt động trong lĩnh vực" <-> "Years of Industry Experience", "Mẫu mã và sản phẩm hoàn thiện" <-> "Models & Finished Products", "Khách hàng hài lòng với dịch vụ" <-> "Satisfied Clients & Partners"...
   - Dịch vụ trên Trang chủ:
     * Tiêu đề: "Gia công 3D" <-> "3D Machining & Printing", "Thiết kế 3D" <-> "3D Design", "Custom" <-> "Custom" (hoặc "Custom Bespoke Creations"), "Làm đồ custom" <-> "Custom Bespoke Creations".
     * Mô tả Gia công 3D: "Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm" <-> "Realizing models with 3D printing technology, focusing on precision and product quality."
     * Mô tả Thiết kế 3D: "Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế." <-> "Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability."
     * Mô tả Custom: "Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn." <-> "Custom design and manufacturing on demand, crafting unique pieces with your signature touch."
2. Hệ thống Dịch vụ:
   - Tiêu đề & Lời dẫn: "CÁC DỊCH VỤ IN ẤN TẠI HH MAKER" <-> "HH MAKER 3D PRINTING SERVICES", "MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG" <-> "ONE IDEA - MULTIPLE POSSIBILITIES", "Dịch vụ liên quan" <-> "Related Services", "Tìm hiểu chi tiết" <-> "Learn More", "Tìm hiểu dịch vụ" <-> "Explore Service", "Mục lục" / "TRONG BÀI VIẾT NÀY" <-> "Table of Contents" / "IN THIS ARTICLE"...
   - Các khối thông tin so sánh & Checklist: "BẠN CHUẨN BỊ" <-> "YOU PREPARE", "CÙNG HOÀN THIỆN" <-> "WE CO-CREATE", "BẮT ĐẦU TỪ" <-> "STARTING FROM", "CÙNG LÀM RÕ" <-> "CLARIFYING TOGETHER", "01 / DỊCH VỤ" <-> "01 / SERVICE", "02 / DỊCH VỤ" <-> "02 / SERVICE", "03 / DỊCH VỤ" <-> "03 / SERVICE"...

#### Scenario: Tra cứu từ điển UI khi chuyển đổi locale
- **WHEN** locale hiện tại là `en` và component gọi `tt('TỔNG QUAN VỀ DỊCH VỤ')`
- **THEN** chuỗi trả về là `SERVICES OVERVIEW`.
- **WHEN** locale hiện tại là `vi` và component gọi `tt('TỔNG QUAN VỀ DỊCH VỤ')`
- **THEN** chuỗi trả về là `TỔNG QUAN VỀ DỊCH VỤ`.
- **WHEN** component tra cứu tên dịch vụ vị trí 3 ở locale `vi` và `en`
- **THEN** hệ thống trả về `Custom` cho cả hai ngôn ngữ.

---

### Requirement: Song ngữ hóa toàn diện Trang chủ (Home Page)
Toàn bộ các phần nội dung trên Trang chủ (`Home/Index.vue`) SHALL hiển thị 100% bằng Tiếng Anh khi người dùng truy cập route `/en`:
1. **Hero Section**:
   - Subtitle: "ABOUT US" (hoặc "FROM HH MAKER").
   - Title: "TURNING EVERY IDEA INTO REALITY WITH 3D PRINTING".
   - Description: "No limits to creative space, HH Maker breathes life into each creation through modern 3D printing and precise machining."
   - Nút CTA: "Explore Now" và "Request Design Consultation".
   - Vòng tròn văn bản xoay: "FROM IDEAS TO REALITY - FROM IDEAS TO REALITY - ".
2. **Featured Products Carousel**:
   - Title: "FEATURED PRODUCTS".
   - Các slide hiển thị tên và mô tả sản phẩm bằng tiếng Anh.
3. **Services Section**:
   - Heading: "SERVICES OVERVIEW" (hoặc "TỔNG QUAN VỀ DỊCH VỤ" theo locale).
   - 3 Card dịch vụ SHALL hiển thị theo đúng thứ tự:
     * Card 1 (Vị trí 1): "Gia công 3D" (`slug: gia-cong-3d`, `position: 1`) - Icon máy in 3D. Mô tả: "Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm" (VI) / "Realizing models with 3D printing technology, focusing on precision and product quality." (EN).
     * Card 2 (Vị trí 2 - thẻ giữa nền Slate Dark `#30374F` chuẩn Figma Node 2261:5549): "Thiết kế 3D" (`slug: thiet-ke-3d`, `position: 2`) - Icon cây bút 3D. Mô tả: "Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế." (VI) / "Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability." (EN).
     * Card 3 (Vị trí 3): "Custom" (`slug: lam-do-custom`, `position: 3`) - Icon đũa phép/phụ kiện 3D. Mô tả: "Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn." (VI) / "Custom design and manufacturing on demand, crafting unique pieces with your signature touch." (EN).
   - Mỗi card dịch vụ là một liên kết điều hướng hợp lệ dẫn tới localized route tương ứng (`/dich-vu/{slug}` cho VI và `/en/services/{slug}` cho EN).
4. **Process Section**:
   - Số bước "05" kèm tiêu đề: "STEPS IN THE PROFESSIONAL & TRANSPARENT WORKFLOW AT HH MAKER".
   - 5 bước chi tiết: "Receive & Consult", "Analyze & Quote", "3D Printing & Machining", "Standard Finishing & Quality Control", "Nationwide Delivery".
5. **Brand Story Section**:
   - Eyebrow: "FROM HH MAKER FOUNDER".
   - Title: "BRINGING CREATIVITY BEYOND THE SCREEN".
   - 2 đoạn văn chia sẻ triết lý và 3 chỉ số năng lực bằng Tiếng Anh.
6. **Fast Consultation (Contact Section)**:
   - Title: "CONNECT WITH HH MAKER", mô tả, và toàn bộ nhãn, placeholder, options của form bằng Tiếng Anh.

#### Scenario: Người dùng truy cập Trang chủ ở chế độ Tiếng Anh
- **WHEN** người dùng duyệt `/en`
- **THEN** toàn bộ Hero, Carousel sản phẩm, Dịch vụ tổng quan, Quy trình 5 bước, Câu chuyện sáng lập và Form tư vấn nhanh đều hiển thị bằng Tiếng Anh, không có bất kỳ chuỗi tiếng Việt bị sót.

#### Scenario: Hiển thị đúng thứ tự 3 thẻ dịch vụ trên Trang chủ
- **WHEN** người dùng truy cập Trang chủ ở cả hai ngôn ngữ (`/` hoặc `/en`)
- **THEN** thẻ thứ nhất hiển thị "Gia công 3D", thẻ thứ hai ở giữa hiển thị "Thiết kế 3D" với nền Slate Dark `#30374F`, và thẻ thứ ba hiển thị "Custom".

---

### Requirement: Dữ liệu dịch Tiếng Anh trong Database và Localized SEO trong Controllers
1. Bảng `services` SHALL lưu trữ thứ tự `position` đồng bộ:
   - Dịch vụ Gia công 3D (`slug: gia-cong-3d`): `position = 1`.
   - Dịch vụ Thiết kế 3D (`slug: thiet-ke-3d`): `position = 2`.
   - Dịch vụ Custom (`slug: lam-do-custom`): `position = 3`.
2. Bảng `service_translations` SHALL có đầy đủ các bản ghi với `locale = 'vi'` và `locale = 'en'` cho cả 3 dịch vụ chính:
   - `gia-cong-3d`:
     * VI: `title: "Gia công 3D"`, `description: "Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm"`.
     * EN: `title: "3D Machining & Printing"`, `description: "Realizing models with 3D printing technology, focusing on precision and product quality."`.
   - `thiet-ke-3d`:
     * VI: `title: "Thiết kế 3D"`, `description: "Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế."`.
     * EN: `title: "3D Design"`, `description: "Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability."`.
   - `lam-do-custom`:
     * VI: `title: "Custom"`, `description: "Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn."`.
     * EN: `title: "Custom"` (hoặc `"Custom Bespoke Creations"`), `description: "Custom design and manufacturing on demand, crafting unique pieces with your signature touch."`.
3. `HomeController.php` và `ServiceController.php` SHALL tạo metadata SEO (`title`, `description`, `og:title`, `og:description`) tương thích với `current_locale()`.
4. Bộ nhớ đệm Cache trong controllers SHALL phân biệt khóa theo ngôn ngữ (ví dụ: `home_page_data_{locale}` và `service_show_{slug}_{locale}`) và được xóa sạch sau khi seed/update.

#### Scenario: Truy vấn Service ở locale tiếng Anh
- **WHEN** ứng dụng gọi `Service::active()->whereSlug('thiet-ke-3d')->first()` trong ngữ cảnh `locale === 'en'`
- **THEN** model trả về đối tượng có `title === '3D Design'` và `description === 'Transforming ideas into detailed 3D models, blending aesthetics with practical manufacturability.'`.

#### Scenario: Truy vấn danh sách Service sắp xếp theo position trên Trang chủ
- **WHEN** `HomeController` truy vấn `Service::query()->active()->sortByPosition()->get()`
- **THEN** danh sách trả về theo đúng thứ tự: phần tử 0 là `gia-cong-3d`, phần tử 1 là `thiet-ke-3d`, phần tử 2 là `lam-do-custom`.
