# Phone Validation Specification

## Purpose

Chuẩn hóa bộ quy tắc kiểm thực số điện thoại đồng bộ giữa Frontend và Backend cho toàn bộ các biểu mẫu liên hệ/báo giá Thành Khang, đảm bảo tiếp nhận đúng số Việt Nam hoặc quốc tế hợp lệ và ngăn chặn dữ liệu giả/spam.

## ADDED Requirements

### Requirement: Bộ quy tắc kiểm thực Số điện thoại đồng bộ Frontend & Backend
Tất cả biểu mẫu thu thập liên hệ và báo giá (`QuotePopupModal.vue`, `Contact.vue`, `Home/Components/ContactSection.vue`, và endpoint `POST /contacts` tại `ContactController@store`) SHALL áp dụng cùng một bộ quy tắc kiểm thực số điện thoại dùng chung:
1. **Từ chối ký tự chữ cái**: Bất kỳ chuỗi đầu vào nào chứa chữ cái (ví dụ `09012abc67`, `0987654321a`) MUST bị từ chối ngay lập tức ở cả Frontend và Backend (Backend TUYỆT ĐỐI không tự ý xóa chữ cái bằng regex trước khi kiểm tra độ dài). Chỉ cho phép chữ số, dấu `+` ở đầu, khoảng trắng và các dấu phân tách phổ biến (`-`, `.`, `(`, `)`).
2. **Định dạng Việt Nam & Quốc tế**:
   - Ưu tiên định dạng Việt Nam hợp lệ: di động 10 số (`0[35789]\d{8}`), máy bàn (`02\d{8,9}`), hoặc biến thể mang đầu mã quốc gia `+84` / `84`.
   - Hỗ trợ số điện thoại quốc tế bắt đầu bằng dấu `+` (ngoài `+84`) có tổng cộng từ `8` đến `15` chữ số thực (`+[1-9]\d{7,14}`).
3. **Chặn số giả / spam**: Từ chối mọi số mà toàn bộ chữ số giống hệt nhau (`0000000000`, `1111111111`), số lặp cùng một chữ số ở phần thuê bao (`0900000000`, `0911111111`), hoặc chuỗi số tăng/giảm tuần tự liên tiếp (`0123456789`, `9876543210`).
4. **Thông báo lỗi song ngữ**: Trả về lỗi cụ thể theo trường `phone` bằng Tiếng Việt hoặc Tiếng Anh tùy locale hiện tại (HTTP `422` cho AJAX/JSON hoặc validation errors cho Inertia).

#### Scenario: Chấp nhận số điện thoại Việt Nam và quốc tế hợp lệ
- **WHEN** người dùng nhập `0901 234 567`, `+84 967 732 665`, `024 3856 1234` hoặc `+1 415 555 2671` và gửi qua các form hoặc API `/contacts`
- **THEN** kiểm thực Frontend và Backend đều cho phép đi qua và lưu lead hợp lệ vào database.

#### Scenario: Từ chối số có chứa chữ cái trên cả Frontend và Backend
- **WHEN** người dùng hoặc request gửi số điện thoại chứa chữ cái như `090123abc7` hoặc `abc0901234567`
- **THEN** Frontend chặn submit kèm thông báo lỗi ngay dưới ô SĐT, và nếu gửi thẳng API `POST /contacts` thì Backend trả về HTTP `422` (hoặc redirect kèm lỗi validation `phone`) mà không tạo bản ghi `contacts`.

#### Scenario: Từ chối số điện thoại giả hoặc tuần tự spam
- **WHEN** người dùng hoặc request gửi số `0000000000`, `1111111111`, `0900000000`, `0911111111`, `0123456789`, hoặc `9876543210`
- **THEN** cả Frontend lẫn Backend đều từ chối với lỗi validation trường `phone` và không ghi nhận lead giả vào hệ thống.
