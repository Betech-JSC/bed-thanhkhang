# Spec Delta: informational-pages

## Purpose
Đặc tả chi tiết giải pháp đa ngôn ngữ song ngữ (Tiếng Việt `vi` và Tiếng Anh `en`) cho 4 trang thông tin còn lại của HH Maker: Giới thiệu (About Us), Quy trình gia công (Process), Liên hệ & Báo giá (Contact), và Chính sách & Điều khoản (Policies).

## ADDED Requirements

### Requirement: Từ điển UI cho Trang Giới thiệu, Quy trình, Liên hệ và Chính sách
Hệ thống SHALL cung cấp đầy đủ các chuỗi dịch thuật tại `resources/lang/vi/ui.php` và `resources/lang/en/ui.php` cho toàn bộ các phần nội dung của 4 trang thông tin:
1. Bản dịch tiếng Anh phải đảm bảo tính tự nhiên, chuẩn thuật ngữ kỹ thuật in 3D (FDM, SLA, 3D Modeling, Mechanical Precision, Tolerance...).
2. Các chuỗi bao gồm tiêu đề, phụ đề, nội dung đoạn văn, nhãn nút bấm, placeholder form, câu hỏi FAQ và phản hồi validation.

#### Scenario: Tra cứu chuỗi dịch thuật qua helper tt()
- **WHEN** một component gọi `tt('VỀ CHÚNG TÔI')` hoặc `tt('TƯ VẤN QUA ZALO')` trong khi `locale.current === 'en'`
- **THEN** hàm trả về tương ứng `"ABOUT US"` hoặc `"Consultation via Zalo"`.

---

### Requirement: Song ngữ hóa toàn diện Trang Giới thiệu (About Us)
Trang Giới thiệu (`About/Index.vue`) SHALL hiển thị hoàn toàn bằng tiếng Anh khi người dùng truy cập `/en/about-us` hoặc chuyển sang ngôn ngữ tiếng Anh:
1. Hero banner: Tiêu đề, phụ đề và mô tả fallback hiển thị bằng tiếng Anh.
2. Lý do khởi đầu sứ mệnh: "Vì sao HH Maker ra đời?", "TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH" và các đoạn văn bản giải thích.
3. Câu chuyện nhà sáng lập: "Chia sẻ từ Nhà sáng lập HH Maker", "Mỗi ý tưởng đều xứng đáng có một hình hài", lời tâm huyết và chữ ký triết lý.
4. 3 Thẻ giá trị chế tác:
   - "Thấu hiểu từng ý tưởng" <-> "Understanding Every Idea"
   - "Chăm chút từng chi tiết" <-> "Meticulous Attention to Detail"
   - "Giữ trọn dấu ấn riêng" <-> "Preserving Your Unique Identity"
5. Khối CTA kết nối dự án cuối trang.

#### Scenario: Truy cập /en/about-us
- **WHEN** người dùng duyệt trang `/en/about-us`
- **THEN** toàn bộ tiêu đề, đoạn văn câu chuyện founder, 3 thẻ giá trị và CTA đều hiển thị bằng tiếng Anh, không còn văn bản tiếng Việt sót lại.

---

### Requirement: Song ngữ hóa toàn diện Trang Quy trình (Process)
Trang Quy trình (`Process/Index.vue` và các component) SHALL hiển thị 100% bằng tiếng Anh tại `/en/process`:
1. Hero banner: Thể hiện thông điệp quy trình rõ ràng, minh bạch bằng tiếng Anh.
2. Stepper bar 5 bước:
   - 01. Liên hệ <-> 01. Contact
   - 02. Báo giá <-> 02. Quotation
   - 03. Gia công 3D <-> 03. 3D Manufacturing
   - 04. Đóng gói <-> 04. Packaging
   - 05. Gửi hàng <-> 05. Shipping
3. Bảng so sánh phối hợp 2 cột:
   - Tiêu đề cột: "BẠN CHUẨN BỊ" <-> "YOU PREPARE", "HH MAKER THỰC HIỆN" <-> "HH MAKER EXECUTES".
   - Mô tả từng bước cho cả khách hàng và xưởng in.
4. Khối FAQ Accordion: Tiêu đề "CÂU HỎI THƯỜNG GẶP" <-> "FREQUENTLY ASKED QUESTIONS", câu hỏi và câu trả lời giải đáp thắc mắc.

#### Scenario: Duyệt 5 bước quy trình trên trang tiếng Anh
- **WHEN** người dùng xem trang `/en/process`
- **THEN** stepper 5 bước, tiêu đề 2 cột phối hợp và các câu hỏi thường gặp hiển thị chuẩn tiếng Anh.

---

### Requirement: Song ngữ hóa toàn diện Trang Liên hệ (Contact)
Trang Liên hệ (`Contact.vue`) SHALL hỗ trợ song ngữ toàn diện tại `/en/contact`:
1. Hero Banner: Tiêu đề "KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN" <-> "TELL US ABOUT YOUR IDEA" và mô tả.
2. 3 Thẻ liên lạc kính:
   - Zalo: "Tư vấn qua Zalo" <-> "Consultation via Zalo"
   - Hotline: Tiêu đề số điện thoại kèm mô tả tiếng Anh.
   - Email: Tiêu đề email kèm mô tả tiếng Anh.
3. Form tư vấn:
   - Nhãn và placeholder cho: Họ và tên (Full Name), Số điện thoại (Phone Number), Email (Email Address), Dịch vụ quan tâm (Services of Interest: 3D Design, 3D Manufacturing, Custom Items), Mô tả ý tưởng (Idea Description).
   - Nút submit: "Gửi yêu cầu tư vấn" <-> "Submit Consultation Request" (hoặc "Đang gửi yêu cầu..." <-> "Submitting request...").
   - Các thông báo lỗi validation hiển thị theo ngôn ngữ hiện tại.
4. Modal chúc mừng thành công: Tiêu đề, thông điệp cảm ơn và nút "Đóng thông báo" được dịch sang tiếng Anh.
5. Khối FAQ Accordion: Toàn bộ câu hỏi và câu trả lời hiển thị tiếng Anh.

#### Scenario: Gửi form và xác thực lỗi trên trang /en/contact
- **WHEN** người dùng bấm submit form rỗng trên `/en/contact`
- **THEN** các thông báo lỗi hiển thị bằng tiếng Anh (ví dụ: "Please enter your full name (minimum 2 characters).").

---

### Requirement: Song ngữ hóa toàn diện Trang Chính sách (Policies)
Trang Chính sách (`Policy.vue`) SHALL hỗ trợ song ngữ và giao diện dark-tech tại `/chinh-sach` và `/en/policies`:
1. Tiêu đề trang: "CHÍNH SÁCH & ĐIỀU KHOẢN" <-> "POLICIES & TERMS".
2. Sidebar menu điều khoản: Hiển thị danh mục các chính sách chính:
   - Điều khoản dịch vụ <-> Terms of Service
   - Chính sách bảo mật <-> Privacy Policy
   - Chính sách bảo hành & đổi trả <-> Warranty & Return Policy
3. Nội dung chính sách: Hiển thị tương ứng theo ngôn ngữ được chọn.
4. Cơ chế fallback an toàn: Nếu cơ sở dữ liệu chưa có bản ghi chính sách, hệ thống tự động cung cấp dữ liệu chính sách mặc định đầy đủ song ngữ để trang không bị trống rỗng.

#### Scenario: Truy cập /en/policies
- **WHEN** người dùng truy cập `/en/policies`
- **THEN** sidebar danh mục và nội dung chính sách hiển thị bằng tiếng Anh với định dạng chuẩn dark-tech của HH Maker.

---

### Requirement: Localized SEO trong Controllers
Các controller phụ trách 4 trang thông tin (`HistoryController`, `AgencyController`, `PolicyController`, `ProcessController`) SHALL cung cấp metadata SEO được bản địa hóa theo `current_locale()`:
1. `HistoryController`: Tiêu đề và mô tả SEO cho Trang Giới thiệu (About Us).
2. `AgencyController` (hoặc `ContactController`): Tiêu đề và mô tả SEO cho Trang Liên hệ (Contact).
3. `ProcessController`: Tiêu đề và mô tả SEO cho Trang Quy trình (Process).
4. `PolicyController`: Tiêu đề và mô tả SEO cho Trang Chính sách (Policies).

#### Scenario: Kiểm tra thẻ <title> và <meta name="description">
- **WHEN** trang `/en/about-us` hoặc `/en/process` được tải
- **THEN** mã HTML trả về chứa thẻ `<title>` và `<meta name="description">` bằng tiếng Anh phù hợp với ngữ cảnh trang.
