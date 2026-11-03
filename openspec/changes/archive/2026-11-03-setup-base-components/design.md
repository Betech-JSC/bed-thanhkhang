# Design

## Context

Dự án HH Maker được xây dựng trên stack Laravel 9/10 + Inertia.js (Vue 3.2, Options/Composition API) + Tailwind CSS 3.2.7. Trải qua các đợt refactor từ theme cũ FunkyStyle sang giao diện công nghệ Dark Tech (nền tối `#080C14`, bề mặt `#141C2E`, viền `#22314E`, màu chủ đạo `#0052FF`), hệ thống component cơ sở hiện vẫn chưa được chuẩn hóa:
- Kho icon SVG bị phân tán thành 2 thư mục `Components/Icon/` (9 file) và `Components/Icons/` (20 file), chứa nhiều icon rác cũ và màu sắc hardcode `#2352A4`. File `resources/Frontend/js/app.js` vẫn import `primeicons/primeicons.css` dù không còn dùng các class `pi pi-`.
- Các nút bấm hiện đang viết phân mảnh thành nhiều file riêng biệt (`ButtonPrimary.vue`, `ButtonSeeMore.vue`, `ButtonSlider.vue`, `ButtonAddToCart.vue`, v.v.) hoặc viết inline nhiều lớp Tailwind dài dòng.
- Các ô input, select và modal được viết inline hoặc chưa có component dùng chung, gây khó khăn cho việc duy trì giao diện nhất quán và bảo đảm khả năng tiếp cận (Accessibility - a11y).
- Phần tiêu đề các phân đoạn (Section Heading) trên các trang Home, About, Product đang lặp đi lặp lại cùng một khối markup HTML (subtitle uppercase tracking-widest, h2 uppercase, description text-secondary).

Xem thêm động lực và phạm vi chi tiết tại `proposal.md`.

## Goals / Non-Goals

**Goals:**
- Thiết lập bộ 6 Base Components hoàn chỉnh đặt tại `resources/Frontend/js/Components/Base/`: `BaseButton.vue`, `BaseInput.vue`, `BaseSelect.vue`, `BaseModal.vue`, `BaseBadge.vue`, `SectionHeading.vue`.
- Xây dựng component `BaseIcon.vue` linh hoạt (hoặc chuẩn hóa các SVG icon) hỗ trợ kế thừa `currentColor`, nhận `size`, `class`, và gom kho icon về một thư mục thống nhất, dọn dẹp các icon rác FunkyStyle.
- Loại bỏ triệt để việc import `primeicons.css` khỏi `resources/Frontend/js/app.js` để giảm tải dung lượng bundle tải về.
- Đảm bảo các Base Components tương thích 100% với hệ thống design token của Tailwind hiện có và có API props rõ ràng, trực quan, hỗ trợ TypeScript/JSDoc types.

**Non-Goals:**
- Không thay thế ồ ạt toàn bộ các nút bấm và input ở tất cả các trang phức tạp trong change này (việc refactor từng màn hình sẽ do các change UI theo phase tiếp theo đảm nhận; change này tập trung hoàn thiện hạ tầng Base Components và kho Icon chuẩn).
- Không thêm các thư viện bên thứ ba nặng nề (như Headless UI hay Radix Vue) để tránh xung đột với phiên bản Vue 3.2.31 hiện hành của dự án.
- Không can thiệp vào các logic backend PHP, database migrations hay Laravel routing.

## Decisions

### Decision 1: Hợp nhất kho Icon và chuẩn hóa BaseIcon với currentColor
- **Lựa chọn**: Hợp nhất toàn bộ icon cần thiết vào `resources/Frontend/js/Components/Icon/` (hoặc tạo `BaseIcon.vue` đóng gói các path SVG hoặc import dynamic). Mọi icon đều được chuẩn hóa để nhận `currentColor`, loại bỏ các mã màu hardcode như `#2352A4` hoặc các màu xanh FunkyStyle cũ.
- **Lý do**: Sử dụng `currentColor` cho phép icon tự động đổi màu theo lớp Tailwind của phần tử cha (ví dụ `text-primary`, `text-secondary`, `group-hover:text-primary`), tăng tính linh hoạt tối đa.
- **Phương án thay thế đã cân nhắc**: Giữ nguyên 2 thư mục `Icon/` và `Icons/` -> Bị loại bỏ vì gây nhầm lẫn nghiêm trọng cho các lập trình viên khi import (ví dụ cùng có `Phone.vue` và `User.vue` ở cả 2 nơi).

### Decision 2: BaseButton đa hình (Polymorphic Component)
- **Lựa chọn**: Sử dụng thẻ động `<component :is="resolvedComponent">` trong Vue 3:
  - Nếu có prop `href` và `as="Link"` (hoặc mặc định khi có `href` nội bộ) -> render `<Link :href="href">` của Inertia.
  - Nếu có prop `href` và bắt đầu bằng `http`/`mailto:`/`tel:` hoặc `as="a"` -> render thẻ `<a>`.
  - Mặc định render thẻ `<button :type="type">`.
  - Hỗ trợ các variant: `primary` (nền xanh `#0052FF`), `secondary` (nền `bg-dark-surface` viền `border-dark-border`), `outline` (viền `border-primary` chữ `text-primary`), `ghost` (trong suốt, hover sáng), `danger` (nền đỏ cảnh báo).
  - Hỗ trợ trạng thái `loading`: hiển thị spinner SVG xoay và tự động gán `disabled="true"`.
- **Lý do**: Cho phép tái sử dụng cùng một kiểu dáng nút bấm đẹp mắt cho cả nút bấm form, link điều hướng SPA, và link mở tab mới mà không phải duplicate code.

### Decision 3: BaseInput và BaseSelect chuẩn Dark Tech và a11y
- **Lựa chọn**:
  - `BaseInput.vue`: Bọc quanh một container có nhãn `<label>`, khung nhập liệu với nền `bg-dark-surface`, viền `border-dark-border`, chữ trắng `text-white`, placeholder `text-text-muted`, khi focus kích hoạt `focus:border-primary focus:ring-1 focus:ring-primary outline-none`. Hỗ trợ các prop `leadingIcon`, `trailingIcon` và slot icon linh hoạt. Khi có `errorMessage`, viền chuyển sang `border-red-500` và hiển thị dòng lỗi màu đỏ bên dưới.
  - `BaseSelect.vue`: Áp dụng cùng ngôn ngữ kiểu dáng với `BaseInput`, hiển thị icon mũi tên dropdown ở góc phải, hỗ trợ truyền danh sách options dạng mảng object `{ value, label }` hoặc mảng chuỗi.
- **Lý do**: Đảm bảo form nhập liệu tuân thủ chuẩn Dark Tech thống nhất và chuẩn bị sẵn sàng cho các form liên hệ, form đăng ký, checkout.

### Decision 4: BaseModal với `<Teleport to="body">` và Scroll Locking
- **Lựa chọn**: Đưa markup của modal ra ngoài thẻ `<body>` bằng `<Teleport to="body">`, sử dụng `<Transition>` với hiệu ứng mờ dần (opacity) và phóng nhẹ (scale). Lớp nền phủ là `bg-black/80 backdrop-blur-sm`. Khi modal mở, tự động khóa cuộn trang chính (`document.body.style.overflow = 'hidden'`) và mở khóa khi đóng. Lắng nghe phím `Escape` trên `window` để đóng modal an toàn.
- **Lý do**: Giải quyết triệt để vấn đề z-index bị che khuất bởi các container cha có `overflow: hidden` hoặc `transform`, đồng thời mang lại trải nghiệm người dùng mượt mà và chuẩn mực.

### Decision 5: SectionHeading tách biệt typography và layout logic
- **Lựa chọn**: `SectionHeading.vue` nhận các props `title` (bắt buộc), `subtitle` (tùy chọn), `description` (tùy chọn), `align` (`left` hoặc `center`), và `highlightBar` (boolean).
  - Căn trái (`left`): Subtitle và Title nằm bên trái, Description nằm bên phải (trên màn hình lớn `md:flex-row md:items-end justify-between`) hoặc xếp tầng theo thiết kế.
  - Căn giữa (`center`): Toàn bộ text căn giữa, kèm thanh gạch ngang màu xanh công nghệ phía dưới `w-16 h-1 bg-primary mx-auto rounded-full`.
- **Lý do**: Thay thế ít nhất 5-8 đoạn code lặp lại ở các trang `Home/Components/ServicesSection.vue`, `Home/Components/ProductsSection.vue`, `Home/Components/BrandStorySection.vue`, `Home/Components/ContactSection.vue`, và các trang About/Product.

### Decision 6: BaseBadge cho các trạng thái và nhãn sản phẩm
- **Lựa chọn**: `BaseBadge.vue` hỗ trợ các variant `primary`, `success`, `warning`, `info`, `neutral`, nhận prop `size` (`sm`, `md`), và prop `dot` (boolean để hiển thị chấm tròn phát sáng phía trước).
- **Lý do**: Chuẩn hóa các nhãn như "Best choice", "Còn hàng", "Hot", "Mới" trên các thẻ sản phẩm và bảng dữ liệu.

## Risks / Trade-offs

- **[Risk] Các import icon cũ bị lỗi đường dẫn khi hợp nhất `Icons/` vào `Icon/`**:
  → **Mitigation**: Kiểm tra toàn bộ các file đang import từ `@/Components/Icons/` (ContactMapSection, ButtonSlider, FormCv, ModalSuccess, Contact, History). Sau khi chuyển file, giữ lại một file re-export hoặc cập nhật đồng thời các import này để đảm bảo lệnh `yarn frontend:build` không bao giờ bị gãy.
- **[Risk] Gỡ bỏ `primeicons.css` có thể ảnh hưởng nếu có component nào bí mật dùng**:
  → **Mitigation**: Đã thực hiện kiểm toán tìm kiếm toàn bộ từ khóa `pi pi-` và không tìm thấy bất kỳ nơi nào trong `resources/Frontend/js/` sử dụng PrimeIcons. Việc gỡ bỏ là an toàn 100%.
- **[Risk] Teleport modal có thể gặp vấn đề SSR nếu render phía máy chủ**:
  → **Mitigation**: Đảm bảo Teleport chỉ kích hoạt khi component đã mounted trên client (hoặc kiểm tra `typeof window !== 'undefined'`) để giữ cho Inertia SSR chạy trơn tru.

## Migration Plan

1. Tạo thư mục `resources/Frontend/js/Components/Base/`.
2. Tạo các Base Components (`BaseButton.vue`, `BaseInput.vue`, `BaseSelect.vue`, `BaseModal.vue`, `BaseBadge.vue`, `SectionHeading.vue`, `BaseIcon.vue`).
3. Chuẩn hóa kho icon tại `resources/Frontend/js/Components/Icon/`, cập nhật các file đang import icon cũ.
4. Gỡ bỏ `import 'primeicons/primeicons.css'` trong `resources/Frontend/js/app.js`.
5. Chạy build kiểm tra `yarn frontend:build` để xác thực toàn bộ cú pháp và bundling.
