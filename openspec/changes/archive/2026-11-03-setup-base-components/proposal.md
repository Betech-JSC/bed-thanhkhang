# Proposal

## Why

Giao diện người dùng của HH Maker hiện đang phân mảnh với nhiều component nút bấm, trường nhập liệu và modal viết rời rạc theo các kiểu dáng cũ (FunkyStyle), trùng lặp mã HTML heading trên nhiều trang (Home, About, Product), đồng thời kho SVG icon bị chia rẽ giữa hai thư mục `Icon/` và `Icons/` với các mã màu và kích thước hardcode không nhất quán. Ngoài ra, file `app.js` đang import file stylesheet thừa `primeicons.css` gây lãng phí băng thông tải trang.

Việc thiết lập bộ hạ tầng Base Components và chuẩn hóa kho SVG Icon chuẩn Dark Tech (`#080C14`, `#141C2E`, `#22314E`, `#0052FF`) là bước đi nền tảng cấp thiết để thống nhất ngôn ngữ thiết kế, đẩy nhanh tốc độ triển khai giao diện các trang tiếp theo và đảm bảo mã nguồn tinh gọn, dễ bảo trì.

## What Changes

- **Hợp nhất và chuẩn hóa kho SVG Icons**:
  - Gộp 2 thư mục `resources/Frontend/js/Components/Icon/` và `resources/Frontend/js/Components/Icons/` thành một cấu trúc đồng nhất tại `Components/Icon/` hoặc component điều phối `BaseIcon.vue`.
  - Chuẩn hóa các file icon SVG nhận thuộc tính kích thước linh hoạt (`size` hoặc `width`/`height`), màu sắc kế thừa qua `currentColor`, loại bỏ các mã màu hardcode cũ (như `#2352A4` hoặc màu FunkyStyle).
  - Loại bỏ các icon rác hoặc demo không còn sử dụng thuộc theme cũ.
  - Gỡ bỏ import thừa `import 'primeicons/primeicons.css'` trong `resources/Frontend/js/app.js`.
- **Xây dựng 5 Base Components cốt lõi trong `resources/Frontend/js/Components/Base/`**:
  - `BaseButton.vue`: Nút bấm đa hình (polymorphic) hỗ trợ render dưới dạng `<button>`, `<Link>` (Inertia), hoặc `<a>`, hỗ trợ các biến thể (`primary`, `secondary`, `outline`, `ghost`, `danger`), các kích thước (`sm`, `md`, `lg`), trạng thái `loading` kèm spinner xoay, và slot icon.
  - `BaseInput.vue`: Ô nhập liệu chuẩn Dark Tech (nền `bg-dark-surface`, viền `border-dark-border`, focus ring `#0052FF`), hỗ trợ `v-model`, nhãn `label`, thông báo lỗi `errorMessage`, icon đầu (`leadingIcon`) và icon cuối (`trailingIcon`).
  - `BaseSelect.vue`: Menu thả chọn (dropdown select) tùy biến theo theme Dark Tech, hỗ trợ `v-model`, placeholder, danh sách options, trạng thái disabled và error.
  - `BaseModal.vue`: Hộp thoại modal sử dụng `<Teleport to="body">` và hiệu ứng chuyển cảnh Vue `<Transition>`, lớp nền mờ `bg-black/80 backdrop-blur-sm`, khung hộp thoại `bg-dark-surface border border-dark-border rounded-2xl`, slots `header`, `body`, `footer`, và nút đóng X chuẩn kèm phím tắt `Escape`.
  - `BaseBadge.vue`: Huy hiệu nhãn trạng thái với các biến thể màu (`primary`, `success`, `warning`, `info`, `neutral`), bo góc chuẩn và kích thước nhỏ gọn.
- **Xây dựng SectionHeading component dùng chung**:
  - `SectionHeading.vue`: Đặt tại `resources/Frontend/js/Components/Base/`, nhận các thuộc tính `title`, `subtitle`, `description`, `align` (`left` hoặc `center`), và thanh điểm nhấn (`highlightBar`) để chuẩn hóa phần tiêu đề các phân đoạn trên toàn bộ website.

## Capabilities

### New Capabilities
- `base-components`: Cung cấp hạ tầng Base UI Components đa hình (`BaseButton`, `BaseInput`, `BaseSelect`, `BaseModal`, `BaseBadge`, `SectionHeading`) và chuẩn hóa kho SVG Icons (`BaseIcon`) chuẩn Dark Tech.

### Modified Capabilities
<!-- Không có capability hiện hữu nào bị thay đổi requirement -->

## Impact

- **Mã nguồn Frontend**:
  - Thêm thư mục mới: `resources/Frontend/js/Components/Base/` chứa 6 component nền tảng (`BaseButton.vue`, `BaseInput.vue`, `BaseSelect.vue`, `BaseModal.vue`, `BaseBadge.vue`, `SectionHeading.vue`, `BaseIcon.vue`).
  - Dọn dẹp kho icon trong `resources/Frontend/js/Components/Icon/` & `Icons/`, cập nhật các đường dẫn import icon tương thích trong các component đang dùng.
  - Tối ưu hóa file bundle tại `resources/Frontend/js/app.js` bằng cách gỡ bỏ `primeicons.css`.
- **Hệ thống Design System & Build**:
  - Tương thích 100% với cấu hình Tailwind hiện tại (`resources/Frontend/tailwind.config.js`) và bộ token Dark Tech (`dark-bg`, `dark-surface`, `dark-border`, `primary-600`).
  - Không tác động tiêu cực đến backend, cơ sở dữ liệu, API contracts hay routing của Laravel.
