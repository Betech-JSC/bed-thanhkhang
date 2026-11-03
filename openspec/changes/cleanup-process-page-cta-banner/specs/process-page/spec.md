# Spec Delta: process-page

## Purpose
Đặc tả cấu trúc chuẩn của trang Quy trình chế tác (`resources/Frontend/js/Pages/Process/Index.vue`) khớp 100% với bản vẽ Figma (File `otk6uzZmitaXPdZZZiRG1K`, frame Quy trình), đảm bảo trải nghiệm liền mạch với 4 section chính và không có banner CTA thừa trước footer.

## ADDED Requirements

### Requirement: Cấu Trúc 4 Section Chuẩn Figma Không Banner CTA Chen Giữa
Trang Quy trình SHALL chứa chính xác 4 section theo thứ tự được định nghĩa trong Figma và KHÔNG chứa bất kỳ khối banner kêu gọi hành động (CTA banner) nào chen giữa FAQ và Footer:
1. Section 1: Hero Banner (Figma Node `2283:6841`, kích thước thiết kế 1440x680px)
2. Section 2: Stepper Bar 5 Điểm Quy Trình (Figma Node `2283:6851`, kích thước thiết kế 1440x176px)
3. Section 3: Chi Tiết 5 Bước Gia Công (Figma Node `2283:6882`, kích thước thiết kế 1440x1273px)
4. Section 4: FAQ Accordion (Figma Node `2334:8455`, kích thước thiết kế 1440x621px)

#### Scenario: Truy cập và cuộn trang Quy trình trên Desktop và Mobile
- **WHEN** người dùng truy cập trang `/quy-trinh` và cuộn đến cuối nội dung chính
- **THEN** sau phần giải đáp thắc mắc FAQ Accordion, trang kết nối trực tiếp với Footer của hệ thống, không hiển thị khối banner CTA phụ.

---

### Requirement: Bảo Toàn Tệp Nguồn Tuân Thủ Strict Safe Guard
Quá trình dọn dẹp giao diện SHALL chỉ điều chỉnh cấu trúc template và import trong file điều phối `Index.vue`, tuyệt đối không thực hiện xóa vật lý tệp component `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue` khỏi dự án.

#### Scenario: Kiểm tra tính nguyên vẹn của repository
- **WHEN** hoàn tất quá trình dọn dẹp trang Quy trình
- **THEN** tệp `ProcessCtaBanner.vue` vẫn tồn tại nguyên vẹn trong thư mục `Components/`, cho phép tái sử dụng an toàn khi cần.
