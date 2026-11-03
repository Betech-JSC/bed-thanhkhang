# base-components Specification

## Purpose

Cung cấp bộ hạ tầng Base UI Components đa hình và kho SVG Icons chuẩn hóa theo phong cách Dark Tech cho toàn bộ hệ thống giao diện người dùng của HH Maker, giúp giảm trùng lặp mã và tăng tính nhất quán trong trải nghiệm người dùng.

## Requirements

### Requirement: Chuẩn hóa kho SVG Icons và BaseIcon
Hệ thống SHALL cung cấp hạ tầng kho SVG Icons thống nhất và component `BaseIcon.vue` (hoặc các component SVG chuẩn hóa) có khả năng nhận thuộc tính tên biểu tượng `name`, kích thước linh hoạt `size` (hoặc `width`/`height`), lớp tùy biến `class`, và kế thừa màu sắc thông qua `currentColor`. Hệ thống SHALL loại bỏ import stylesheet `primeicons.css` không sử dụng trong file khởi tạo ứng dụng.

#### Scenario: Hiển thị icon SVG động với kích thước và màu sắc linh hoạt
- **WHEN** một component giao diện gọi `BaseIcon` với tên icon hợp lệ, kích thước `size="24"` và lớp màu `text-primary`
- **THEN** hệ thống render thẻ `<svg>` tương ứng với kích thước 24x24 pixel và đường viền hoặc nền tô kế thừa màu sắc từ lớp `text-primary` thông qua `currentColor`.

#### Scenario: Gỡ bỏ import stylesheet PrimeIcons thừa trong app.js
- **WHEN** file ứng dụng `resources/Frontend/js/app.js` được biên dịch qua Vite
- **THEN** không còn chứa lệnh import `primeicons/primeicons.css`, giảm dung lượng bundle CSS tải về trình duyệt.

#### Scenario: Hợp nhất thư mục icon và dọn dẹp các icon rác cũ
- **WHEN** nhà phát triển import biểu tượng trong mã nguồn giao diện
- **THEN** toàn bộ biểu tượng được truy xuất từ một kho icon duy nhất, không còn sự phân mảnh giữa hai thư mục `Icon/` và `Icons/`, và các mã màu hardcode cũ (như `#2352A4` hoặc các màu sắc phong cách FunkyStyle cũ) được chuẩn hóa thành `currentColor`.

---

### Requirement: BaseButton Component hỗ trợ đa hình và trạng thái Dark Tech
Hệ thống SHALL cung cấp component `BaseButton.vue` hỗ trợ render đa hình (polymorphic) tương ứng với các phần tử `<button>`, `<Link>` (Inertia), hoặc thẻ `<a>`, hỗ trợ các biến thể giao diện chuẩn Dark Tech (`primary`, `secondary`, `outline`, `ghost`, `danger`), các kích cỡ (`sm`, `md`, `lg`), trạng thái `loading` kèm spinner hoạt họa, và các slot chèn icon.

#### Scenario: Render nút bấm tiêu chuẩn với các biến thể giao diện Dark Tech
- **WHEN** component `BaseButton` được gọi với thuộc tính `variant="primary"` và `size="md"`
- **THEN** hệ thống render phần tử `<button>` với lớp nền màu xanh công nghệ `#0052FF`, chữ màu trắng, bo góc chuẩn và hiệu ứng hover/focus tương ứng.

#### Scenario: Render nút dạng liên kết điều hướng Inertia Link hoặc thẻ neo a
- **WHEN** `BaseButton` được truyền thuộc tính `as="Link"` kèm thuộc tính `href="/san-pham"`
- **THEN** hệ thống render component `<Link>` của Inertia với kiểu dáng nút bấm hoàn chỉnh, hỗ trợ chuyển trang Single Page App mà không làm tải lại toàn bộ trang web.

#### Scenario: Hiển thị trạng thái đang xử lý loading kèm icon quay spinner
- **WHEN** thuộc tính `loading` của `BaseButton` được đặt là `true`
- **THEN** nút bấm bị vô hiệu hóa (disabled), con trỏ chuột chuyển sang dạng `not-allowed`, và một spinner loading dạng SVG xoay tròn được hiển thị thay thế hoặc kèm theo nội dung nhãn.

---

### Requirement: BaseInput Component chuẩn Dark Tech
Hệ thống SHALL cung cấp component `BaseInput.vue` chuẩn Dark Tech với nền `bg-dark-surface`, viền `border-dark-border`, hiệu ứng focus ring `#0052FF`, hỗ trợ liên kết dữ liệu hai chiều `v-model`, nhãn hiển thị `label`, thông báo lỗi xác thực `errorMessage`, cũng như các vị trí chèn biểu tượng phía trước (`leadingIcon`) và phía sau (`trailingIcon`).

#### Scenario: Nhập liệu với hai chiều v-model và hiệu ứng focus ring
- **WHEN** người dùng gõ ký tự vào trường `BaseInput`
- **THEN** giá trị được cập nhật tức thì qua sự kiện `update:modelValue` và khi ô input được focus, viền ngoài hiển thị hiệu ứng ánh sáng (glow/ring) màu xanh `#0052FF`.

#### Scenario: Hiển thị trạng thái lỗi và thông báo lỗi validation
- **WHEN** thuộc tính `errorMessage` được truyền vào `BaseInput` một chuỗi thông báo lỗi
- **THEN** viền của ô nhập chuyển sang màu đỏ cảnh báo và nội dung chuỗi thông báo lỗi xuất hiện ngay bên dưới ô nhập liệu.

#### Scenario: Render biểu tượng dẫn hướng leadingIcon và trailingIcon
- **WHEN** `BaseInput` được cấu hình với slot hoặc prop biểu tượng đầu/cuối
- **THEN** biểu tượng được căn chỉnh chính xác bên trong ô nhập với khoảng đệm phù hợp, không làm chữ nhập bị đè lên biểu tượng.

---

### Requirement: BaseSelect Component chuẩn Dark Tech
Hệ thống SHALL cung cấp component `BaseSelect.vue` phong cách Dark Tech hỗ trợ hiển thị danh sách các tùy chọn, hỗ trợ liên kết dữ liệu hai chiều qua `v-model`, văn bản gợi ý `placeholder`, trạng thái vô hiệu hóa `disabled` và thông báo lỗi `errorMessage`.

#### Scenario: Lựa chọn phần tử từ danh sách tùy chọn
- **WHEN** người dùng tương tác và chọn một mục trong `BaseSelect`
- **THEN** giá trị được chọn được cập nhật qua `v-model` và giao diện phản ánh nhãn hiển thị tương ứng trên nền tối Dark Tech.

#### Scenario: Hiển thị trạng thái vô hiệu hóa disabled
- **WHEN** thuộc tính `disabled` của `BaseSelect` được thiết lập là `true`
- **THEN** menu chọn bị khóa tương tác, màu sắc mờ đi và con trỏ chuột hiển thị trạng thái cấm thao tác.

---

### Requirement: BaseModal Component với Teleport và Transition
Hệ thống SHALL cung cấp component `BaseModal.vue` sử dụng `<Teleport to="body">` và Vue `<Transition>`, có lớp phủ nền tối mờ `bg-black/80 backdrop-blur-sm`, khung hộp thoại trung tâm với kiểu dáng `bg-dark-surface border border-dark-border rounded-2xl`, hỗ trợ các slots `header`, `body`, `footer`, nút đóng biểu tượng X và khả năng tự động đóng khi nhấn phím `Escape` hoặc click ra ngoài vùng backdrop.

#### Scenario: Mở hộp thoại modal được teleport ra ngoài thẻ body
- **WHEN** prop `modelValue` (hoặc `show`) của `BaseModal` chuyển thành `true`
- **THEN** mã HTML của modal được dịch chuyển ra gắn trực tiếp vào thẻ `<body>` với hiệu ứng hoạt họa fade-in, và cuộn trang của màn hình chính được khóa lại để tránh cuộn kép.

#### Scenario: Đóng modal khi nhấn nút X, click vào backdrop hoặc phím Escape
- **WHEN** người dùng nhấn vào nút X, nhấn phím `Escape`, hoặc click chuột vào vùng nền backdrop bên ngoài hộp thoại
- **THEN** modal kích hoạt sự kiện đóng, chuyển trạng thái hiển thị sang ẩn và trả lại khả năng cuộn bình thường cho trang chính.

#### Scenario: Hiển thị nội dung tùy biến qua các slot header, body và footer
- **WHEN** người dùng truyền các phần tử nội dung vào các slot `header`, `body`, và `footer` của `BaseModal`
- **THEN** các phân đoạn tương ứng được render đúng vị trí đã thiết kế trong khung modal Dark Tech.

---

### Requirement: BaseBadge Component cho các nhãn trạng thái
Hệ thống SHALL cung cấp component `BaseBadge.vue` hiển thị nhãn và huy hiệu trạng thái với các biến thể màu sắc chuẩn Dark Tech (`primary`, `success`, `warning`, `info`, `neutral`), hỗ trợ hiển thị chấm báo hiệu tròn (`dot`) và bo góc chuẩn `rounded-full` hoặc `rounded-md`.

#### Scenario: Hiển thị các nhãn trạng thái với biến thể màu tương ứng
- **WHEN** `BaseBadge` được gọi với thuộc tính `variant="primary"` hoặc `variant="success"`
- **THEN** huy hiệu hiển thị với nền và viền màu phù hợp với ngữ cảnh ngữ nghĩa (xanh công nghệ cho primary, xanh lá cho success) và độ tương phản chữ đạt chuẩn hiển thị trên nền tối.

---

### Requirement: SectionHeading Component chuẩn hóa tiêu đề phân đoạn
Hệ thống SHALL cung cấp component `SectionHeading.vue` dùng chung tại `resources/Frontend/js/Components/Base/`, nhận các thuộc tính `title`, `subtitle`, `description`, `align` (`left` hoặc `center`), và `highlightBar` (boolean) để thay thế hoàn toàn các đoạn mã HTML tiêu đề lặp lại trên các trang Home, About, và Product.

#### Scenario: Render tiêu đề phân đoạn căn trái kèm subtitle và description
- **WHEN** `SectionHeading` được gọi với `title="TỔNG QUAN VỀ DỊCH VỤ"`, `subtitle="GIẢI PHÁP & NĂNG LỰC"`, `align="left"`, và một đoạn mô tả
- **THEN** hệ thống render phân đoạn tiêu đề căn đều bên trái với subtitle chữ hoa màu xanh `text-primary`, tiêu đề chính nổi bật màu trắng `text-white font-sans`, và đoạn văn bản mô tả màu `text-secondary`.

#### Scenario: Render tiêu đề phân đoạn căn giữa kèm thanh điểm nhấn
- **WHEN** `SectionHeading` được gọi với `align="center"` và `highlightBar="true"`
- **THEN** toàn bộ nội dung phụ đề, tiêu đề chính và mô tả được căn giữa trang, đồng thời một thanh gạch ngang điểm nhấn màu `bg-primary` xuất hiện phía dưới để tạo điểm nhấn thị giác chuẩn Dark Tech.
