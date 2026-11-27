# Base Components Delta Specification

## MODIFIED Requirements

### Requirement: BaseModal Component với Teleport và Transition
Hệ thống SHALL cung cấp component `BaseModal.vue` sử dụng `<Teleport to="body">` và Vue `<Transition>`, hỗ trợ thuộc tính `variant` với 2 biến thể `'dark'` (mặc định giữ tương thích ngược) và `'light'` (dùng cho giao diện sáng Thành Khang):
- Khi `variant="dark"` (mặc định): lớp phủ nền tối mờ `bg-black/80 backdrop-blur-sm`, khung hộp thoại trung tâm `bg-dark-surface border border-dark-border rounded-2xl`, chữ tiêu đề sáng `text-white`, nội dung `text-text-secondary`.
- Khi `variant="light"`: lớp phủ nền mờ `bg-[#00284B]/50 backdrop-blur-[2px]`, khung hộp thoại trung tâm nền trắng `bg-white border border-[#E3E8EF] rounded-2xl shadow-2xl`, chữ tiêu đề `#00284B`, nội dung `#4B5565`, nút đóng X sử dụng `#697586` (hover `#00284B` / `bg-[#EEF2F6]`) với focus ring `#005EA4`.
- Cả 2 biến thể MUST hỗ trợ các slots `header`, `body`, `footer`, nút đóng biểu tượng X và khả năng tự động đóng khi nhấn phím `Escape` hoặc click ra ngoài vùng backdrop.

#### Scenario: Mở hộp thoại modal được teleport ra ngoài thẻ body
- **WHEN** prop `modelValue` (hoặc `show`) của `BaseModal` chuyển thành `true`
- **THEN** mã HTML của modal được dịch chuyển ra gắn trực tiếp vào thẻ `<body>` với hiệu ứng hoạt họa fade-in, và cuộn trang của màn hình chính được khóa lại để tránh cuộn kép.

#### Scenario: Đóng modal khi nhấn nút X, click vào backdrop hoặc phím Escape
- **WHEN** người dùng nhấn vào nút X, nhấn phím `Escape`, hoặc click chuột vào vùng nền backdrop bên ngoài hộp thoại
- **THEN** modal kích hoạt sự kiện đóng, chuyển trạng thái hiển thị sang ẩn và trả lại khả năng cuộn bình thường cho trang chính.

#### Scenario: Hiển thị nội dung tùy biến qua các slot header, body và footer
- **WHEN** người dùng truyền các phần tử nội dung vào các slot `header`, `body`, và `footer` của `BaseModal`
- **THEN** các phân đoạn tương ứng được render đúng vị trí đã thiết kế trong khung modal theo biến thể màu tương ứng.

#### Scenario: Hiển thị modal giao diện sáng khi truyền variant="light"
- **WHEN** `BaseModal` được gọi với `variant="light"` và `modelValue="true"`
- **THEN** hộp thoại hiển thị nền trắng `#FFFFFF`, viền `#E3E8EF`, chữ tiêu đề/nội dung tối đạt độ tương phản WCAG trên nền sáng và nút đóng hiển thị rõ nét.
