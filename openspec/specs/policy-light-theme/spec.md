# policy-light-theme Specification

## Purpose

Chuyển đổi trang Chính sách (`Policy.vue`) và hộp thoại xác nhận gửi liên hệ thành công trên `Contact.vue` sang giao diện sáng B2B chuẩn thương hiệu Thành Khang, loại bỏ hoàn toàn bảng màu tối cũ.

## Requirements

### Requirement: Trang Chính sách giao diện sáng chuẩn thương hiệu Thành Khang
Trang Chính sách (`resources/Frontend/js/Pages/Policy.vue` phục vụ `/chinh-sach/*` và `/en/policies/*`) SHALL sử dụng hoàn toàn hệ thống giao diện sáng B2B Thành Khang thay thế cho nền tối cũ (`#111322`, `#1A1C32`, `#243971`, `#528BFF`):
- Container chính sử dụng `.app-container-narrow` trên nền `#FAFAFA`.
- Khối tiêu đề đầu trang, thanh điều hướng Sidebar Desktop, bộ chọn danh mục Mobile (`min-h-[44px]`) và thẻ nội dung bài viết sử dụng nền trắng `#FFFFFF`, viền `#E3E8EF`, chữ tiêu đề `#00284B` và chữ thân `#364152` / `#4B5565`.
- Mục chính sách đang kích hoạt sử dụng nền `#EDF6FF`, viền `#005EA4` và chữ `#005EA4` đậm.
- Vùng nội dung HTML `.policy-prose` sử dụng chữ tối dễ đọc (`#364152`), tiêu đề `h3`/`h4` màu `#00284B` kèm đường viền nhấn `#005EA4`.

#### Scenario: Hiển thị trang Chính sách với giao diện sáng trên Desktop và Mobile
- **WHEN** người dùng truy cập `/chinh-sach/chinh-sach-bao-mat` hoặc `/en/policies/privacy-policy` trên Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** trang hiển thị nền `#FAFAFA`, thẻ nội dung nền `#FFFFFF` viền `#E3E8EF`, tiêu đề và nội dung `.policy-prose` màu tối độ tương phản cao, mục sidebar active nổi bật với màu `#005EA4`, không còn bất kỳ mảng nền tối `#111322` hay `#1A1C32` nào.

---

### Requirement: Modal thông báo gửi thành công trang Liên hệ sử dụng giao diện sáng
Hộp thoại thông báo gửi yêu cầu thành công trên `Contact.vue` SHALL sử dụng `<BaseModal variant="light">` với tiêu đề màu `#00284B`, mô tả màu `#4B5565` và nút đóng màu `#005EA4` chữ trắng, đồng bộ hoàn toàn với giao diện sáng của trang Liên hệ.

#### Scenario: Hiển thị hộp thoại xác nhận gửi thành công nền sáng trên trang Liên hệ
- **WHEN** người dùng gửi biểu mẫu hợp lệ thành công trên `/lien-he` hoặc `/en/contact`
- **THEN** modal thông báo xuất hiện trên nền trắng `#FFFFFF` viền `#E3E8EF`, chữ tiêu đề `"Gửi yêu cầu thành công!"` màu `#00284B` và nội dung mô tả hiển thị rõ ràng, không bị nền tối hay chữ trắng mờ.
