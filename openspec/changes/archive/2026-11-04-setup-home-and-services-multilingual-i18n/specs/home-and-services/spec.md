# Spec Delta: home-and-services

## Purpose
Đặc tả chi tiết các yêu cầu chức năng và giao diện đa ngôn ngữ song ngữ (Tiếng Việt và Tiếng Anh) cho Trang chủ (Home Page) và Hệ thống Dịch vụ (Services Index & Detail) của nền tảng chế tác và in 3D HH Maker.

## ADDED Requirements

### Requirement: Từ điển UI chuyên biệt cho Trang chủ và Dịch vụ in 3D
Hệ thống từ điển giao diện `resources/lang/{vi,en}/ui.php` SHALL cung cấp đầy đủ các cặp khóa - giá trị song ngữ (Tiếng Việt và Tiếng Anh) cho toàn bộ các phân hệ Trang chủ và Dịch vụ:
1. Trang chủ:
   - Eyebrows & Slogans: "ĐƯA Ý TƯỞNG RA NGOÀI THỰC TẾ" <-> "FROM IDEAS TO REALITY", "SẢN PHẨM NỔI BẬT" <-> "FEATURED PRODUCTS", "TỔNG QUAN VỀ DỊCH VỤ" <-> "SERVICES OVERVIEW", "CÂU CHUYỆN THƯƠNG HIỆU" <-> "BRAND STORY", "CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER" <-> "FROM HH MAKER FOUNDER", "HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH" <-> "BRINGING CREATIVITY BEYOND THE SCREEN", "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER" <-> "STEPS IN THE PROFESSIONAL & TRANSPARENT WORKFLOW AT HH MAKER", "KẾT NỐI VỚI HH MAKER" <-> "CONNECT WITH HH MAKER"...
   - Nút hành động & CTA: "Khám phá ngay" <-> "Explore Now", "Nhận tư vấn thiết kế" <-> "Request Design Consultation", "Xem thêm sản phẩm khác" <-> "View More Products", "Xem tất cả dịch vụ" <-> "View All Services", "Gửi thông tin" <-> "Submit"...
   - Chỉ số hoạt động: "Năm hoạt động trong lĩnh vực" <-> "Years of Industry Experience", "Mẫu mã và sản phẩm hoàn thiện" <-> "Models & Finished Products", "Khách hàng hài lòng với dịch vụ" <-> "Satisfied Clients & Partners"...
2. Hệ thống Dịch vụ:
   - Tiêu đề & Lời dẫn: "CÁC DỊCH VỤ IN ẤN TẠI HH MAKER" <-> "HH MAKER 3D PRINTING SERVICES", "MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG" <-> "ONE IDEA - MULTIPLE POSSIBILITIES", "Dịch vụ liên quan" <-> "Related Services", "Tìm hiểu chi tiết" <-> "Learn More", "Tìm hiểu dịch vụ" <-> "Explore Service", "Mục lục" / "TRONG BÀI VIẾT NÀY" <-> "Table of Contents" / "IN THIS ARTICLE"...
   - Các khối thông tin so sánh & Checklist: "BẠN CHUẨN BỊ" <-> "YOU PREPARE", "CÙNG HOÀN THIỆN" <-> "WE CO-CREATE", "BẮT ĐẦU TỪ" <-> "STARTING FROM", "CÙNG LÀM RÕ" <-> "CLARIFYING TOGETHER", "01 / DỊCH VỤ" <-> "01 / SERVICE", "02 / DỊCH VỤ" <-> "02 / SERVICE", "03 / DỊCH VỤ" <-> "03 / SERVICE"...

#### Scenario: Tra cứu từ điển UI khi chuyển đổi locale
- **WHEN** locale hiện tại là `en` và component gọi `tt('TỔNG QUAN VỀ DỊCH VỤ')`
- **THEN** chuỗi trả về là `SERVICES OVERVIEW`.
- **WHEN** locale hiện tại là `vi` và component gọi `tt('TỔNG QUAN VỀ DỊCH VỤ')`
- **THEN** chuỗi trả về là `TỔNG QUAN VỀ DỊCH VỤ`.

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
   - Heading: "SERVICES OVERVIEW".
   - 3 Card dịch vụ: "3D Design", "3D Machining & Printing", "Custom Bespoke Creations" kèm mô tả tiếng Anh và đường dẫn tới localized routes (`/en/services/...`).
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

---

### Requirement: Song ngữ hóa Trang Danh sách Dịch vụ (Services Index)
Trang Danh sách Dịch vụ (`Services/Index.vue`) và component thẻ dịch vụ (`ServiceBlock.vue`) SHALL hiển thị hoàn toàn bằng Tiếng Anh khi người dùng truy cập route `/en/services`:
1. Badge chuyên mục: `HH MAKER 3D PRINTING SERVICES`.
2. Tiêu đề H1: `ONE IDEA - MULTIPLE POSSIBILITIES`.
3. Đoạn dẫn: `You can begin with a 3D file, a rough sketch, or simply a vision. HH Maker accompanies you from clarifying concepts to choosing the ideal fabrication method.`
4. 3 Khối Dịch Vụ Xen Kẽ (Z-Pattern):
   - Service 01: `3D Design` - `From sketch to concrete geometry.`
     * Tags: `Concept Development`, `3D Modeling`, `Print Optimization`
     * Prep: `YOU PREPARE` - `Ideas, reference images, intended dimensions, and functional requirements.`
     * Comp: `WE CO-CREATE` - `3D model blueprint and technical details aligned before production.`
     * CTA: `Consult 3D Design` và `Learn More`.
   - Service 02: `3D Machining & Printing` - `Bringing digital designs into the physical world.`
     * Tags: `Model Verification`, `Material Consultation`, `Printing & Finishing`
     * Prep: `YOU PREPARE` - `3D files, quantities, dimensions, and surface or assembly tolerances.`
     * Comp: `WE CO-CREATE` - `Physical parts produced to agreed specifications and machining standards.`
     * CTA: `Consult 3D Machining` và `Learn More`.
   - Service 03: `Custom Bespoke Creations` - `Not just different. Exclusively yours.`
     * Tags: `Personal Gifts`, `Accessories`, `Spatial Decor`
     * Prep: `YOU PREPARE` - `Story or requirements, sample models you wish to personalize.`
     * Comp: `WE CO-CREATE` - `Unique signature product tailored to the discussed concept.`
     * CTA: `Consult Custom Creations` và `Learn More`.
5. Đường dẫn của thẻ dịch vụ và nút chi tiết dẫn chính xác tới `/en/services/{slug}`.

#### Scenario: Kiểm tra trang danh sách dịch vụ tiếng Anh
- **WHEN** người dùng truy cập `/en/services`
- **THEN** toàn bộ tiêu đề, 3 khối dịch vụ Z-pattern, các tags, bảng chuẩn bị vs hoàn thiện và nút CTA đều hiển thị bằng Tiếng Anh.

---

### Requirement: Song ngữ hóa Trang Chi tiết Dịch vụ (Service Detail)
Trang Chi tiết Dịch vụ (`Services/Show.vue`) cùng các component con SHALL hiển thị 100% bằng Tiếng Anh khi người dùng truy cập route `/en/services/{slug}`:
1. **Hero & Khung Minh Họa**:
   - Breadcrumb: `Home / Services / [Service Title]`.
   - Headline & Description bằng Tiếng Anh.
   - Khung minh họa kỹ thuật: `STARTING FROM` (Idea, Reference photos, Rough sketches) vs `CLARIFYING TOGETHER` (Proportions & Geometry, Structure & Details, Machining Feasibility).
2. **Sticky TOC Sidebar**:
   - Tiêu đề mục lục: `IN THIS ARTICLE` hoặc `TABLE OF CONTENTS`.
   - 4 mục: `01 When do you need 3D design?`, `02 From concept to 3D model`, `03 What should you prepare?`, `04 Completing together before machining`.
3. **Nội dung kỹ thuật 4 phần (`ServiceArticleContent.vue`)**:
   - Mục 01: Câu hỏi dẫn nhập và lý do cần thiết kế 3D.
   - Mục 02: 4 bước thiết kế (badge số bước, tiêu đề bước và mô tả chi tiết bằng Tiếng Anh).
   - Mục 03: Checklist thông số kỹ thuật (Ý tưởng, Ảnh tham khảo, Kích thước dự kiến, Chi tiết ưu tiên bằng Tiếng Anh).
   - Mục 04: Thống nhất hoàn thiện trước khi gia công kèm khối callout lưu ý kỹ thuật.
4. **Dịch vụ liên quan (`ServiceRelated.vue`)**:
   - Tiêu đề: `Related Services`.
   - Các card dịch vụ liên quan hiển thị tên tiếng Anh, mô tả tiếng Anh và nút `Explore Service` trỏ tới `/en/services/{slug}`.
5. **Banner CTA Lời mời sáng tạo (`ServiceCta.vue`)**:
   - Tiêu đề: `Create your own signature version`.
   - Mô tả: `Send reference photos or share your ideas so HH Maker can shape your desired product. We will consult tailored solutions for your style, needs, and budget.`
   - Nút hành động: `Get Consultation Now` trỏ tới `/en/contact`.

#### Scenario: Kiểm tra trang chi tiết dịch vụ tại /en/services/thiet-ke-3d
- **WHEN** người dùng truy cập `/en/services/thiet-ke-3d`
- **THEN** toàn bộ Breadcrumb, Hero, Sticky TOC, bài viết kỹ thuật 4 phần, dịch vụ liên quan và Banner CTA đều hiển thị bằng Tiếng Anh.

---

### Requirement: Dữ liệu dịch Tiếng Anh trong Database và Localized SEO trong Controllers
1. Bảng `service_translations` SHALL có đầy đủ các bản ghi với `locale = 'en'` cho cả 3 dịch vụ chính:
   - Dịch vụ ID 1: `title: "3D Design"`, `description: "An idea does not need to be complete to begin."`, `slug: "thiet-ke-3d"`.
   - Dịch vụ ID 2: `title: "3D Machining & Printing"`, `description: "Bringing digital designs into the physical world."`, `slug: "gia-cong-3d"`.
   - Dịch vụ ID 3: `title: "Custom Bespoke Creations"`, `description: "Not just different. Exclusively yours."`, `slug: "lam-do-custom"`.
2. `HomeController.php` và `ServiceController.php` SHALL tạo metadata SEO (`title`, `description`, `og:title`, `og:description`) tương thích với `current_locale()`.
3. Bộ nhớ đệm Cache trong controllers SHALL phân biệt khóa theo ngôn ngữ (ví dụ: `home_page_data_{locale}` và `service_show_{slug}_{locale}`).

#### Scenario: Truy vấn Service ở locale tiếng Anh
- **WHEN** ứng dụng gọi `Service::active()->whereSlug('thiet-ke-3d')->first()` trong ngữ cảnh `locale === 'en'`
- **THEN** model trả về đối tượng có `title === '3D Design'` và `description === 'An idea does not need to be complete to begin.'`.
