# Spec Delta: sync-policy-links

## Purpose

Cung cấp cơ chế điều hướng trực tiếp theo slug cho 3 liên kết chính sách ở Chân trang (Footer), đồng bộ hóa tiêu đề thống nhất với thanh bên (Sidebar) và đảm bảo hiển thị đúng nội dung cùng trạng thái kích hoạt (active tab) trên cả hai ngôn ngữ Tiếng Việt và Tiếng Anh.

## ADDED Requirements

### Requirement: Direct Slug Routing from Footer
Hệ thống PHẢI (SHALL) định tuyến từng liên kết chính sách tại Chân trang (`Footer.vue`) trực tiếp đến URL chi tiết chứa slug tương ứng của bài viết thay vì trỏ chung về trang danh mục gốc. Cụ thể:
1. "Chính sách bảo mật" PHẢI trỏ đến `/chinh-sach/chinh-sach-bao-mat` (VI) hoặc `/en/policies/privacy-policy` (EN).
2. "Điều khoản dịch vụ" PHẢI trỏ đến `/chinh-sach/dieu-khoan-dich-vu` (VI) hoặc `/en/policies/terms-of-service` (EN).
3. "Chính sách bảo hành & đổi trả" PHẢI trỏ đến `/chinh-sach/chinh-sach-bao-hanh-doi-tra` (VI) hoặc `/en/policies/warranty-return-policy` (EN).

#### Scenario: User clicks Privacy Policy link in Footer
- **WHEN** người dùng nhấp vào "Chính sách bảo mật" tại chân trang trên giao diện tiếng Việt
- **THEN** trình duyệt chuyển hướng đến `/chinh-sach/chinh-sach-bao-mat` và tải nội dung chính sách bảo mật

#### Scenario: User clicks Terms of Service link in Footer
- **WHEN** người dùng nhấp vào "Điều khoản dịch vụ" tại chân trang trên giao diện tiếng Việt
- **THEN** trình duyệt chuyển hướng đến `/chinh-sach/dieu-khoan-dich-vu` và tải nội dung điều khoản dịch vụ

#### Scenario: User clicks Warranty and Return Policy link in Footer
- **WHEN** người dùng nhấp vào "Chính sách bảo hành & đổi trả" tại chân trang trên giao diện tiếng Việt
- **THEN** trình duyệt chuyển hướng đến `/chinh-sach/chinh-sach-bao-hanh-doi-tra` và tải nội dung chính sách bảo hành & đổi trả

### Requirement: Consistency of Policy Titles across Footer and Sidebar
Hệ thống PHẢI (SHALL) đồng bộ 100% nhãn văn bản hiển thị giữa Chân trang (`Footer.vue`) và Danh mục chính sách trên Sidebar (`Policy.vue`), loại bỏ hoàn toàn nhãn "Chính sách Cookie" không có nội dung và đổi nhãn "Điều khoản sử dụng" thành "Điều khoản dịch vụ".

#### Scenario: Verify Footer policy labels in Vietnamese
- **WHEN** người dùng xem phần bản quyền ở chân trang với ngôn ngữ Tiếng Việt
- **THEN** 3 nhãn chính sách hiển thị chính xác là "Chính sách bảo mật", "Điều khoản dịch vụ" và "Chính sách bảo hành & đổi trả", không còn xuất hiện "Chính sách Cookie" hay "Điều khoản sử dụng"

#### Scenario: Verify Footer policy labels in English
- **WHEN** người dùng chuyển sang ngôn ngữ Tiếng Anh (`en`) và quan sát chân trang
- **THEN** 3 nhãn chính sách hiển thị chính xác là "Privacy Policy", "Terms of Service" và "Warranty & Return Policy"

### Requirement: Active Tab Highlight on Sidebar and Mobile Selector
Trang Chính sách (`Policy.vue`) PHẢI (SHALL) làm nổi bật (highlight active state) đúng mục chính sách đang hiển thị trên thanh bên máy tính (Desktop Sidebar) và bộ chọn danh mục di động (Mobile Selector) dựa trên `slug` bài viết hiện tại.

#### Scenario: Active tab highlight on Desktop Sidebar
- **WHEN** người dùng truy cập trang `/chinh-sach/chinh-sach-bao-hanh-doi-tra`
- **THEN** mục "Chính sách bảo hành & đổi trả" trên Sidebar máy tính có class nền `#243971`, viền sáng `#528BFF` và hiệu ứng đổ bóng, trong khi 2 mục còn lại ở trạng thái mặc định

#### Scenario: Active item display on Mobile Selector
- **WHEN** người dùng truy cập trang `/chinh-sach/chinh-sach-bao-mat` trên thiết bị di động
- **THEN** nút mở danh mục hiển thị tiêu đề "Chính sách bảo mật" và khi mở menu, mục này được đánh dấu viền sáng và nền xanh tích cực

### Requirement: Bilingual Support for Vietnamese and English Policy Routes
Hệ thống PHẢI (SHALL) hỗ trợ đầy đủ song ngữ cho cấu trúc URL và nội dung chính sách. Khi người dùng đang ở ngữ cảnh tiếng Anh (`en`), các liên kết chân trang và sidebar PHẢI tự động trỏ về tiền tố `/en/policies/{slug_en}` và tải bản dịch tiếng Anh tương ứng.

#### Scenario: Navigation in English locale
- **WHEN** người dùng đang ở trang `/en` và bấm vào "Warranty & Return Policy" ở chân trang
- **THEN** hệ thống chuyển đến `/en/policies/warranty-return-policy`, nội dung hiển thị bằng tiếng Anh, thẻ `<title>` và meta description phản ánh chuẩn SEO tiếng Anh
