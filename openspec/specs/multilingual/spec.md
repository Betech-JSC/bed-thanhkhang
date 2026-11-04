# multilingual Specification

## Purpose

Đặc tả chi tiết hệ thống đa ngôn ngữ song ngữ (Tiếng Việt `vi` và Tiếng Anh `en`) cho website HH Maker, bao gồm cấu hình routing tiền tố `/en`, hệ thống dịch thuật giao diện UI thông qua Vue I18n, nút capsule toggle `[VIE | ENG]` trên Header chuẩn Figma Node 2380:1477 và cơ chế điều hướng mượt mà giữa các ngôn ngữ.

## Requirements

### Requirement: Cấu Hình Locale Song Ngữ và Routing Tiếng Anh
Hệ thống SHALL hỗ trợ đồng thời hai ngôn ngữ: Tiếng Việt (`vi`, ngôn ngữ mặc định) và Tiếng Anh (`en`). Tiếng Việt không sử dụng tiền tố URL (`omit_url_prefix_for_locale`), trong khi Tiếng Anh sử dụng tiền tố `/en`:
1. Mảng cấu hình `locales` trong `config/app.php` SHALL bao gồm `['vi', 'en']`.
2. Gói định tuyến `codezero/laravel-localized-routes` SHALL tạo đầy đủ các cặp route tương ứng cho cả hai ngôn ngữ.
3. Các route chính tiếng Anh SHALL được định nghĩa tại `resources/lang/en/routes.php`:
   - `home`: `/` (URL tiếng Anh: `/en`)
   - `contact`: `contact` (URL tiếng Anh: `/en/contact`)
   - `products`: `products` (URL tiếng Anh: `/en/products`)
   - `about-us`: `about-us` (URL tiếng Anh: `/en/about-us`)
   - `services`: `services` (URL tiếng Anh: `/en/services`)
   - `posts`: `posts` (URL tiếng Anh: `/en/posts`)
   - `quy-trinh`: `process` (URL tiếng Anh: `/en/process`)
   - `search`: `search` (URL tiếng Anh: `/en/search`)
   - `policies`: `policies` (URL tiếng Anh: `/en/policies`)

#### Scenario: Truy cập trực tiếp trang tiếng Anh
- **WHEN** người dùng gửi yêu cầu HTTP GET đến `/en`, `/en/services`, `/en/products` hoặc `/en/about-us`
- **THEN** máy chủ trả về mã trạng thái HTTP 200 OK với prop `locale.current` là `'en'`.

---

### Requirement: Bộ Từ Điển Giao Diện Đa Ngôn Ngữ (UI Translation Tokens)
Hệ thống SHALL cung cấp bộ từ điển giao diện đầy đủ tại `resources/lang/vi/ui.php` và `resources/lang/en/ui.php` cho toàn bộ các nhãn xuất hiện trên Header, Footer, CTA và các thành phần cốt lõi:
1. Từ điển tiếng Việt ánh xạ chính xác các nhãn tiếng Việt tương ứng.
2. Từ điển tiếng Anh cung cấp bản dịch chuẩn xác:
   - "Trang chủ" -> "Home"
   - "Sản phẩm" -> "Products"
   - "Dịch vụ" -> "Services"
   - "Giới thiệu" -> "About Us"
   - "Quy trình" -> "Process"
   - "Tìm kiếm" -> "Search"
   - "Liên hệ ngay" -> "Contact Now"
   - "Liên hệ" -> "Contact"
   - "Chính sách bảo mật" -> "Privacy Policy"
   - "Điều khoản sử dụng" -> "Terms of Use"
   - "Bản quyền" -> "Copyright"
   - "Tất cả quyền được bảo lưu" -> "All rights reserved"
3. Helper `tt(text)` trong Vue 3 SHALL hỗ trợ tra cứu linh hoạt qua cả khóa trực tiếp (`text`), tiền tố `ui.${text}`, và truy xuất an toàn từ kho từ điển loaded.

#### Scenario: Hiển thị thanh menu theo ngôn ngữ tương ứng
- **WHEN** người dùng đang ở giao diện tiếng Anh (`locale.current === 'en'`)
- **THEN** các mục điều hướng trên Header hiển thị lần lượt là "Home", "Products", "Services", "About Us", "Process" và nút CTA "Contact Now".

---

### Requirement: Capsule Switcher Ngôn Ngữ Trên Header Chuẩn Figma Node 2380:1477
Cụm Language Switcher trên Header Desktop (Node 2380:1477) SHALL tuân thủ chính xác quy cách thiết kế:
1. Khung container có kích thước cố định `w-[96px] h-[34px]`, bo góc `rounded-[8px]`, viền `border-[#5D6B98]`, padding `p-1`, chống chọn văn bản `select-none`.
2. Khi ngôn ngữ đang chọn là Tiếng Việt (`currentLocale === 'vi'`):
   - Nút `VIE` có badge sáng nền `#FAFAFA`, chữ tối `#111322`, bo góc `rounded-[4px]`, kích thước `w-[36px] h-[24px]`, font 12px `font-semibold`, căn giữa và có shadow nhẹ `shadow-xs`.
   - Nút `ENG` là chữ màu trắng `#FFFFFF`, font 14px `font-semibold`, hover có hiệu ứng mờ nhẹ `hover:text-[#FAFAFA]/80`.
3. Khi ngôn ngữ đang chọn là Tiếng Anh (`currentLocale === 'en'`):
   - Nút `ENG` có badge sáng nền `#FAFAFA`, chữ tối `#111322`, bo góc `rounded-[4px]`, kích thước `w-[42px] h-[24px]`, font 12px `font-semibold`, căn giữa và có shadow nhẹ `shadow-xs`.
   - Nút `VIE` là chữ màu trắng `#FFFFFF`, font 14px `font-semibold`, hover có hiệu ứng mờ nhẹ `hover:text-[#FAFAFA]/80`.
4. Trên ngăn kéo điều hướng Mobile Drawer, capsule switcher tương ứng cũng áp dụng kiểu nền sáng `#FAFAFA` cho ngôn ngữ được chọn và màu chữ trắng cho ngôn ngữ chưa chọn.

#### Scenario: Kiểm tra kiểu dáng nút ngôn ngữ khi chuyển đổi
- **WHEN** người dùng ở locale `vi`, nút `VIE` mang class `bg-[#FAFAFA] text-[#111322]` và nút `ENG` mang class `text-white`
- **WHEN** người dùng click chuyển sang locale `en`, nút `ENG` chuyển thành `bg-[#FAFAFA] text-[#111322]` và nút `VIE` chuyển thành `text-white`.

---

### Requirement: Chuyển Đổi Đường Dẫn Mượt Mà Khi Đổi Locale (switchLocale)
Hàm `switchLocale(lang)` trong `Header.vue` SHALL xử lý chuyển hướng người dùng an toàn, chính xác và không gây gián đoạn:
1. Nếu người dùng chọn ngôn ngữ đang hiện diện (`this.currentLocale === lang`), hàm KHÔNG thực hiện hành động thừa.
2. Khi đổi từ `vi` sang `en`:
   - Nếu đang ở trang chủ (`/`), chuyển hướng đến `/en`.
   - Nếu đang ở route con, chuyển đổi đường dẫn sang route tiếng Anh tương ứng (ví dụ: `/san-pham` sang `/en/products`, `/dich-vu` sang `/en/services`).
3. Khi đổi từ `en` sang `vi`:
   - Nếu đang ở `/en`, chuyển hướng về `/`.
   - Nếu đang ở route con tiếng Anh, chuyển đổi về đường dẫn tiếng Việt tương ứng (ví dụ: `/en/products` sang `/san-pham`, `/en/services` sang `/dich-vu`).
4. Quá trình chuyển đổi ưu tiên sử dụng `this.$inertia.visit(targetUrl)` nhằm giữ trải nghiệm SPA nhanh chóng, và fallback sang `window.location.href = targetUrl` khi cần thiết.

#### Scenario: Người dùng click vào nút ENG từ trang chủ tiếng Việt
- **GIVEN** người dùng đang ở đường dẫn `/` (Tiếng Việt)
- **WHEN** người dùng click vào nút `ENG`
- **THEN** trình duyệt chuyển sang đường dẫn `/en`, giao diện tải phiên bản tiếng Anh và nút `ENG` trở thành active badge.

---

### Requirement: Đồng Bộ Hóa Đa Ngôn Ngữ Chân Trang (Footer)
Component `Footer.vue` SHALL sử dụng các hàm điều hướng có nhận biết locale (`route(...)`) và hàm dịch `tt(...)` cho các tiêu đề và đường dẫn:
1. Các đường dẫn chân trang đến Sản phẩm, Dịch vụ, Giới thiệu, Quy trình và Chính sách SHALL sử dụng các route động có locale của Ziggy thay vì đường dẫn cứng tiếng Việt.
2. Tiêu đề các cột và đường dẫn chính sách SHALL hiển thị theo ngôn ngữ hiện tại của người dùng.

#### Scenario: Kiểm tra đường dẫn chân trang khi ở tiếng Anh
- **WHEN** người dùng ở giao diện tiếng Anh (`/en`)
- **THEN** các liên kết ở chân trang tự động trỏ đến các route tiếng Anh có tiền tố `/en/`.
