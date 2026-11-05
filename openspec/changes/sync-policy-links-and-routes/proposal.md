# Proposal: Đồng bộ hóa 3 mục chính sách ở Chân trang (Footer) và Trang Chính Sách theo Lựa chọn A

## Why

Hiện tại, ở Chân trang (`Footer.vue`), cả 3 liên kết chính sách ("Chính sách bảo mật", "Điều khoản sử dụng", "Chính sách Cookie") đều đang gắn cứng `route('policies.index')` (trỏ về chung `/chinh-sach` hoặc `/en/policies` không kèm `slug`). Do đó, khi người dùng nhấp vào bất kỳ mục nào ở chân trang, hệ thống luôn tải bài viết đầu tiên mặc định trong danh sách ("Điều khoản dịch vụ"), gây trải nghiệm điều hướng sai lệch và không mở đúng bài viết mà người dùng mong muốn.

Đồng thời, nhãn "Chính sách Cookie" ở Footer hiện không có bài viết tương ứng và không phù hợp với đặc thù nghiệp vụ xưởng in 3D chuyên nghiệp (HH Maker). Người dùng đã chính thức phê duyệt **Lựa chọn A**: thay thế "Chính sách Cookie" bằng "Chính sách bảo hành & đổi trả", đồng thời đồng bộ hóa nhãn "Điều khoản dịch vụ" và cấu trúc URL trực tiếp theo từng slug bài viết tương ứng.

Việc đồng bộ hóa này là cần thiết ngay để hoàn thiện trải nghiệm pháp lý, điều hướng minh bạch và tăng độ tin cậy thương hiệu (High-Trust UI) cho khách hàng xưởng in 3D.

## What Changes

- **Footer Direct Routing theo Slug**: Cập nhật cả 3 liên kết ở chân trang (`Footer.vue`) để trỏ chính xác đến route chi tiết `route('policies.show', slug)` (hoặc URL đa ngữ tương ứng):
  1. "Chính sách bảo mật" (`Privacy Policy`) trỏ về `/chinh-sach/chinh-sach-bao-mat` (hoặc `/en/policies/privacy-policy`).
  2. "Điều khoản dịch vụ" (`Terms of Service`) trỏ về `/chinh-sach/dieu-khoan-dich-vu` (hoặc `/en/policies/terms-of-service`).
  3. "Chính sách bảo hành & đổi trả" (`Warranty & Return Policy`) trỏ về `/chinh-sach/chinh-sach-bao-hanh-doi-tra` (hoặc `/en/policies/warranty-return-policy`).
- **Đồng bộ nhãn hiển thị Chân trang**:
  - Đổi "Điều khoản sử dụng" thành "Điều khoản dịch vụ" cho đồng nhất 100% với tiêu đề bài viết và Sidebar.
  - Thay thế "Chính sách Cookie" thành "Chính sách bảo hành & đổi trả".
- **Đồng bộ Active Tab trên Sidebar & Mobile Dropdown**:
  - Đảm bảo logic `checkActive(slug)` trong `Policy.vue` kích hoạt chính xác trạng thái active (nền xanh `#243971`, viền viền sáng `#528BFF`) tương ứng với bài viết đang xem khi truy cập trực tiếp từ Footer hoặc chuyển đổi tab.
- **Backend Layout Data & Seeder Bảo Đảm**:
  - Bổ sung `PolicySeeder` nạp sẵn 3 chính sách cốt lõi trên vào database cho cả 2 ngôn ngữ (`vi` và `en`).
  - Đảm bảo `HandleInertiaFrontendRequests.php` cung cấp dữ liệu fallback an toàn cho mảng policies khi cơ sở dữ liệu chưa có record, giúp `Footer.vue` hoặc các view khác truy xuất dynamic slug đồng nhất.

## Capabilities

### New Capabilities
- `sync-policy-links`: Đặc tả hành vi điều hướng chi tiết theo slug cho 3 liên kết chính sách chân trang, đồng bộ nhãn hiển thị và kích hoạt active tab tương ứng trên trang Chính sách (`Policy.vue`) đa ngôn ngữ (VI/EN).

### Modified Capabilities
<!-- None -->

## Impact

- **Frontend Components**:
  - `resources/Frontend/js/Components/Footer.vue`: Cập nhật cấu trúc liên kết và nhãn hiển thị của 3 mục chính sách ở thanh bản quyền dưới cùng.
  - `resources/Frontend/js/Pages/Policy.vue`: Kiểm tra và đảm bảo khớp nối active tab, URL slug, tiêu đề trang và hỗ trợ đa ngôn ngữ.
- **Backend & Middleware**:
  - `app/Http/Middleware/HandleInertiaFrontendRequests.php`: Bổ sung fallback default policies nếu bảng `policies` chưa có dữ liệu.
  - `database/seeders/PolicySeeder.php`: Tạo mới seeder cho 3 chính sách chuẩn nghiệp vụ in 3D và đăng ký vào `database/seeders/DatabaseSeeder.php`.
- **Database & Safety Boundary**:
  - Không thay đổi cấu trúc bảng (schema migrations giữ nguyên).
  - Không drop, truncate bảng ngoài ý muốn; bảo toàn 100% dữ liệu hiện có.
