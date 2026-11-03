# Tasks

## 1. Chuẩn hóa kho SVG Icons và dọn dẹp stylesheet thừa

- [x] 1.1 Khảo sát toàn bộ các icon hiện có trong `resources/Frontend/js/Components/Icon/` và `Icons/`, lập danh sách hợp nhất vào thư mục `resources/Frontend/js/Components/Icon/`, dọn dẹp các icon rác FunkyStyle không dùng và chuyển các mã màu hardcode (`#2352A4`, ...) sang `currentColor` và kiểm tra file tồn tại
- [x] 1.2 Xây dựng component điều phối `BaseIcon.vue` tại `resources/Frontend/js/Components/Base/BaseIcon.vue` hỗ trợ các prop `name`, `size`, `class`, và kiểm tra render thành công
- [x] 1.3 Cập nhật các đường dẫn import icon tại các component hiện có (`ContactMapSection.vue`, `ButtonSlider.vue`, `FormCv.vue`, `ModalSuccess.vue`, `Contact.vue`, `History.vue`) sang kho icon đã chuẩn hóa và xác nhận không còn lỗi unresolved import
- [x] 1.4 Gỡ bỏ lệnh `import 'primeicons/primeicons.css'` trong `resources/Frontend/js/app.js` và xác nhận file bundle CSS không còn tải thư viện icon ngoài

## 2. Xây dựng các Base Components cốt lõi

- [x] 2.1 Tạo component `BaseButton.vue` tại `resources/Frontend/js/Components/Base/BaseButton.vue` hỗ trợ đa hình (`<button>`, `<Link>`, `<a>`), các variant (`primary`, `secondary`, `outline`, `ghost`, `danger`), kích thước (`sm`, `md`, `lg`), spinner loading và icon slot; kiểm tra render độc lập không có lỗi
- [x] 2.2 Tạo component `BaseInput.vue` tại `resources/Frontend/js/Components/Base/BaseInput.vue` hỗ trợ liên kết hai chiều `v-model`, nhãn `label`, thông báo lỗi `errorMessage`, token màu Dark Tech (`bg-dark-surface`, `border-dark-border`, focus ring `#0052FF`), leading/trailing icons và kiểm tra tương tác input
- [x] 2.3 Tạo component `BaseSelect.vue` tại `resources/Frontend/js/Components/Base/BaseSelect.vue` hỗ trợ chọn danh sách options theo phong cách Dark Tech, hai chiều `v-model`, placeholder, trạng thái `disabled`, `errorMessage` và kiểm tra chọn lựa giá trị
- [x] 2.4 Tạo component `BaseModal.vue` tại `resources/Frontend/js/Components/Base/BaseModal.vue` sử dụng `<Teleport to="body">`, Vue `<Transition>`, backdrop mờ `bg-black/80 backdrop-blur-sm`, hộp thoại `bg-dark-surface border border-dark-border rounded-2xl`, slots `header`, `body`, `footer`, nút đóng X, tự động khóa cuộn trang và xử lý phím Escape; kiểm tra mở đóng modal an toàn

## 3. Xây dựng BaseBadge và SectionHeading

- [x] 3.1 Tạo component `BaseBadge.vue` tại `resources/Frontend/js/Components/Base/BaseBadge.vue` hỗ trợ các biến thể trạng thái (`primary`, `success`, `warning`, `info`, `neutral`), prop `dot`, bo góc chuẩn `rounded-full` và kiểm tra hiển thị nhãn
- [x] 3.2 Tạo component `SectionHeading.vue` tại `resources/Frontend/js/Components/Base/SectionHeading.vue` nhận `title`, `subtitle`, `description`, `align` (`left` hoặc `center`), và `highlightBar` (boolean); kiểm tra hiển thị đúng layout cả trên desktop lẫn mobile

## 4. Build, Kiểm toán Kiến trúc & Nghiệm thu Giao diện

- [x] 4.1 Chạy lệnh `yarn frontend:build` để biên dịch toàn bộ bundle Frontend và xác nhận 0 lỗi cú pháp hoặc import gãy
- [x] 4.2 Cập nhật bản đồ AST kiến trúc mã nguồn qua lệnh `graphify . --code-only` theo quy chuẩn Graphify của dự án
- [x] 4.3 Thực hiện kiểm chứng trực quan bằng Playwright MCP hoặc chụp snapshot trang chủ để đảm bảo các component Base hiển thị sắc nét, chuẩn Dark Tech và không làm vỡ bất kỳ giao diện hiện hữu nào
