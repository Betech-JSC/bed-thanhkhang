# Checklist Kiểm Toán Toàn Diện Độ Phủ Animation & Micro-Interaction (`ui-skill`)

> **Chế độ thực thi**: **STRICT READ-ONLY** — Tuyệt đối không tự ý sửa hoặc xóa bất kỳ file mã nguồn nào trong quá trình kiểm toán. Mọi phát hiện phải đính kèm đường dẫn file tương đối và số dòng chính xác (`File:Dòng`).

---

## 1. Bảng Đối Chiếu Nhận Diện: "Đã Có Animation" vs "Chưa Có Animation (Static / Abrupt)"

| Thành phần UI | Dấu hiệu CHƯA CÓ Animation (`Static / Abrupt`) | Chuẩn ĐÃ CÓ Animation (`Smooth / High-Trust Motion`) |
| :--- | :--- | :--- |
| **1. Buttons / CTA / Links** | Chỉ có `bg-primary text-white` hoặc đổi màu `hover:bg-...` tức thời mà **không có** `transition-*` / `duration-*`; bấm vào (`:active`) không có phản hồi xúc giác. | Có `transition-all duration-200 ease-out` (hoặc `transition-colors`), `hover:bg-...`, `hover:shadow-...`, `active:scale-[0.98]`, `focus-visible:ring-2`. |
| **2. Product / Post / Service Cards** | Khối card và ảnh thumbnail tĩnh hoàn toàn khi rê chuột; chữ tiêu đề hoặc nút mũi tên không phản hồi theo nhóm (`group`). | Thẻ cha có `group transition-all duration-300 hover:-translate-y-1 hover:shadow-lg`; ảnh con có `overflow-hidden` + `transition-transform duration-500 ease-out group-hover:scale-105`. |
| **3. Modal / Popup / Drawer / Mobile Menu** | Đóng/mở trực tiếp bằng `v-if="isOpen"` / `v-show="isOpen"` / `{isOpen && <Modal />}` trần -> xuất hiện và biến mất giật cục trong `0ms`. | Bọc trong `<Transition>` (Vue 3) / `AnimatePresence` (React) / PrimeVue `<Dialog>` / `<Sidebar>` với fade backdrop (`opacity-0 -> opacity-100`) và slide/scale panel (`scale-95/translate -> scale-100/translate-0`). |
| **4. Dropdown / Select / Popover / Search Box** | Khối gợi ý tìm kiếm hoặc menu con bật/tắt tức thời bằng `v-if` / `v-show` không có hiệu ứng trượt nhẹ hay mờ dần. | Bọc `<Transition enter-active-class="transition duration-150 ease-out" enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0">`. |
| **5. Accordion / FAQ / Collapse / Tabs** | Nội dung câu trả lời hoặc tab mới nhảy bố cục đột ngột khi đổi `activeTab` / `openIndex`. | Dùng `<Transition mode="out-in">` cho Tab hoặc kỹ thuật `grid-template-rows: 0fr -> 1fr` (`transition-[grid-template-rows,opacity] duration-300`) cho Accordion. |
| **6. Danh sách động (Filter / Sort / Cart Items)** | Khi lọc sản phẩm, đổi danh mục hoặc xóa sản phẩm khỏi giỏ hàng, danh sách giật lại ngay lập tức. | Dùng `<TransitionGroup>` (Vue) hoặc layout transition + skeleton/opacity fade khi `isLoading`. |
| **7. Hero & Page Sections (Scroll Reveal)** | Toàn bộ các section từ trên xuống dưới đứng yên cứng nhắc khi người dùng cuộn trang, thiếu nhịp điệu thị giác. | Có hiệu ứng entrance tinh tế (`fade-up`, `data-aos="fade-up"`, `v-motion`, IntersectionObserver reveal hoặc CSS `@keyframes` stagger nhẹ `60ms–100ms`). |
| **8. Form Controls & Submit Feedback** | Input focus không có hiệu ứng viền mượt; nút Submit khi bấm gửi API không có spinner/pulse hoặc chuyển trạng thái mượt. | Input có `transition-colors duration-150 focus:border-primary focus:ring-2`; nút Submit có `:disabled="processing"` kèm icon `animate-spin` và `transition-opacity`. |
| **9. Accessibility (`prefers-reduced-motion`)** | Chạy animation/parallax/auto-scroll bất chấp cài đặt giảm chuyển động của hệ điều hành. | Có `@media (prefers-reduced-motion: reduce)` trong global CSS hoặc dùng prefix `motion-safe:` / `motion-reduce:` của Tailwind. |

---

## 2. Sáu Nhóm Kiểm Toán Chi Tiết (6 Animation Audit Dimensions)

### Nhóm 1: Design Tokens & Global Motion Foundation
- **Files cần kiểm tra**:
  - `tailwind.config.js` / `tailwind.config.ts` / `resources/Frontend/tailwind.config.js`
  - `resources/Frontend/scss/**/*.scss`, `resources/css/**/*.css`, `src/app/globals.css`
  - `package.json`
- **Tiêu chí kiểm tra**:
  1. Đã định nghĩa custom `transitionDuration`, `transitionTimingFunction`, `keyframes`, `animation` trong `tailwind.config.*` chưa?
  2. Các thư viện đã cài đặt trong `package.json` (`aos`, `gsap`, `swiper`, `framer-motion`, `@vueuse/motion`, `tailwindcss-animate`) có thực sự được khởi tạo và sử dụng trong code hay chỉ cài để đó (Unused Motion Dependency)?
  3. Trong file CSS/SCSS toàn cục đã có rule `@media (prefers-reduced-motion: reduce)` để vô hiệu hóa animation dài cho người dùng cần trợ năng chưa?

### Nhóm 2: Layout Shell & Page/Route Transitions
- **Files cần kiểm tra**:
  - `Layouts/Default.vue`, `Layouts/Guest.vue`, `Components/Layout/Header*.vue`, `Footer*.vue`, `MobileMenu*.vue`, `Floating*.vue` (hoặc `src/app/layout.tsx`, `src/components/layout/*`)
- **Tiêu chí kiểm tra**:
  1. **Page Transition**: Chuyển trang giữa các route Inertia / Next.js có thanh tiến trình (`@inertiajs/progress`) hoặc hiệu ứng fade/slide nhẹ ở `<slot />` / `{children}` của Layout chính không?
  2. **Sticky Header**: Khi cuộn trang (`window.scrollY > 20`), Header đổi chiều cao, màu nền (`backdrop-blur`), hoặc đổ bóng có kèm `transition-all duration-300` không?
  3. **Mobile Navigation Drawer & Search Overlay**: Khi mở menu trên mobile hoặc ô tìm kiếm, có hiệu ứng trượt (`translate-x`) và mờ nền (`backdrop fade`) bằng `<Transition>` không?
  4. **Floating CTA / Back-to-Top**: Nút cuộn lên đầu trang hoặc hotline nổi xuất hiện/ẩn đi khi cuộn có `transition-opacity duration-300` không?

### Nhóm 3: Section Entrance & Scroll-Reveal Animations
- **Files cần kiểm tra**:
  - Toàn bộ file trong `Pages/` (`Home.vue`, `About.vue`, `Products/Index.vue`, `Products/Show.vue`, `Posts/Index.vue`, `Posts/Show.vue`, `Contact.vue`...) và các `Components/Section/*`.
- **Tiêu chí kiểm tra**:
  1. **Hero Section**: Tiêu đề, mô tả, cụm nút CTA và hình ảnh Hero có hiệu ứng xuất hiện phân tầng (staggered entrance) nhẹ nhàng khi tải trang không?
  2. **Content Sections**: Các section giới thiệu, thống kê số liệu (Counter), quy trình, lưới sản phẩm/tin tức, lời chứng thực (Testimonials), CTA cuối trang có hiệu ứng Scroll-Reveal (`IntersectionObserver`, `v-observe-visibility`, `AOS`, `GSAP` hoặc CSS reveal class) hay tĩnh 100%?
  3. **Counters / Numbers**: Các con số thống kê thành tựu có hiệu ứng đếm tăng dần (`autocounter` / count-up) khi cuộn tới không?

### Nhóm 4: Interactive Micro-Interactions (Hover / Focus / Active)
- **Files cần kiểm tra**:
  - `Components/Card/*`, `Components/Button/*`, `Components/Form/*`, `Components/V4V/*`, `Components/Navigation/*`, `Pagination*.vue`, `Breadcrumb*.vue`.
- **Tiêu chí kiểm tra**:
  1. **Buttons & Action Links**: Mọi `<button>`, `<Link>`, `<a>` đóng vai trò nút bấm có đủ bộ `transition-* duration-*` + `hover:*` + `active:*` không?
  2. **Cards (Product / News / Category / Team)**:
     - Khung card có `transition-all duration-300 hover:shadow-*` hoặc `hover:-translate-y-*` không?
     - Ảnh bên trong card có `transition-transform duration-500 group-hover:scale-*` không?
     - Nút/icon "Xem thêm" hoặc "Thêm vào giỏ" trên card có trượt nhẹ (`group-hover:translate-x-1`) hoặc hiện rõ khi hover card không?
  3. **Form Inputs / Selects / Checkboxes**: Khi `:hover` và `:focus` có chuyển màu `border-color` / `box-shadow` mượt mà (`transition-colors duration-150`) không?

### Nhóm 5: Overlays, Modals, Drawers, Dropdowns, Accordions & Tabs
- **Files cần kiểm tra**:
  - Tất cả các file có chứa `v-if`, `v-show`, `<Dialog`, `<Modal`, `<Drawer`, `dropdown`, `accordion`, `tab`, `collapse`, `popup`, `filter`.
- **Tiêu chí kiểm tra**:
  1. Tìm toàn bộ vị trí dùng `v-if` hoặc `v-show` điều khiển hiển thị khối UI tạm thời (Modal, Popup, QuickView, Cart Drawer, Dropdown ngôn ngữ/tài khoản, Bộ lọc mobile, Accordion câu hỏi thường gặp, Tab chuyển đổi nội dung).
  2. Đánh dấu **`[ABRUPT-V-IF]` / `[ABRUPT-V-SHOW]`** nếu khối đó **không** nằm trong `<Transition>`, `<TransitionGroup>`, `AnimatePresence` và cũng không có class animation đóng/mở.

### Nhóm 6: Async Feedback, Loading Skeleton & State Transitions
- **Files cần kiểm tra**:
  - Các trang danh sách có bộ lọc AJAX/Inertia, Form liên hệ/đặt hàng/đăng nhập, Giỏ hàng, Thông báo Toast/Alert.
- **Tiêu chí kiểm tra**:
  1. **Form Submission**: Khi `form.processing` hoặc `loading === true`, nút submit có hiệu ứng chuyển sang trạng thái chờ (spinner `animate-spin` + giảm opacity mượt) không?
  2. **Skeleton / Shimmer**: Khi đang tải dữ liệu hoặc ảnh (`v-lazy-image`), có hiệu ứng `animate-pulse` / blur-up placeholder không?
  3. **Toast / Inline Alert**: Khi gửi form thành công hoặc báo lỗi validation, thông báo hiện ra có `<Transition>` trượt/mờ nhẹ không?

---

## 3. Phân Loại Mức Độ Ưu Tiên (`P0` / `P1` / `P2`) Cho Kiểm Toán Animation

- **`P0 (Critical UX / Abrupt Interaction)`**:
  - Menu Mobile, Modal, Drawer, Cart Popup, Dropdown hoặc Tab chính đóng/mở giật cục (`0ms` mount/unmount do dùng `v-if`/`v-show` trần) gây cảm giác web bị lỗi hoặc đứt gãy trải nghiệm.
  - Nút Submit form quan trọng (Đặt hàng, Thanh toán, Liên hệ) không có phản hồi trạng thái loading/active khiến người dùng bấm đúp nhiều lần.
  - Thiếu hoàn toàn rule bảo vệ `@media (prefers-reduced-motion: reduce)` khi trang có dùng animation chuyển động lớn.
- **`P1 (High Impact / Core Micro-Interactions)`**:
  - Product Card, Post Card, Category Card, Header Navigation, và các nút CTA chính trên `Home` / `Listing` / `Detail` thiếu `transition-*` hoặc thiếu hiệu ứng `hover:*` / `group-hover:*`.
  - Sticky Header thay đổi trạng thái khi cuộn nhưng không có `transition` mượt.
- **`P2 (Polish / Section Scroll-Reveal & Stagger)`**:
  - Các Section nội dung trên `Pages/` chưa có hiệu ứng entrance / scroll-reveal khi cuộn trang.
  - Hiệu ứng đếm số (Counter), Breadcrumb hover, Badge micro-transition, hoặc tối ưu dọn các thư viện animation đã cài trong `package.json` nhưng chưa kích hoạt.

---

## 4. Mẫu Báo Cáo Chuẩn Của `ui-skill` (3 Bảng Bắt Buộc)

### Bảng 1.1: Tổng Quan Tỷ Lệ % Độ Phủ Animation Toàn Hệ Thống

| Tầng Giao Diện (UI Layer) | Tổng số File (`Active`) | Đã có Animation / Transition | Chưa có hoặc Thiếu (`Static / Partial`) | Độ phủ Animation (`%`) | Đánh giá |
| :--- | :---: | :---: | :---: | :---: | :--- |
| **1. Global Motion & Tokens** (`tailwind.config`, `scss/`) | `...` | `...` | `...` | `..%` | `GOOD / PARTIAL / MISSING` |
| **2. Layout Shell** (`Layouts/`, Header, Footer, Nav) | `...` | `...` | `...` | `..%` | `GOOD / PARTIAL / MISSING` |
| **3. Feature Pages** (`Pages/*`) | `...` | `...` | `...` | `..%` | `GOOD / PARTIAL / MISSING` |
| **4. Active UI Components** (`Components/*`) | `...` | `...` | `...` | `..%` | `GOOD / PARTIAL / MISSING` |
| **5. Orphan Components** *(Tham khảo, không tính điểm)* | `...` | `...` | `...` | `N/A` | Tách riêng để tránh làm lãng phí công sức |

### Bảng 1.2: Danh Sách Chi Tiết Các Thành Phần CHƯA CÓ Animation (`File:Dòng`)

| # | Nhóm (1–6) | Ưu tiên (`P0`/`P1`/`P2`) | Tọa độ `File:Dòng` | Thành phần / Phần tử UI | Hiện trạng trong code (`Static / Abrupt`) | Giải pháp Animation đề xuất (`Clean & Quiet Motion`) |
| :---: | :---: | :---: | :--- | :--- | :--- | :--- |
| 1 | Nhóm 5 | `P0` | `.../Header.vue:84` | Mobile Menu Drawer (`v-if="isOpen"`) | Mở/đóng tức thời không có `<Transition>` | Bọc `<Transition>` với slide `translate-x-full -> translate-x-0 duration-300 ease-out` + backdrop fade |
| 2 | Nhóm 4 | `P1` | `.../ProductCard.vue:12` | Thẻ sản phẩm & ảnh thumbnail | Thẻ `<a>` và `<img>` tĩnh, không có `group-hover` | Thêm `group transition-all duration-300 hover:-translate-y-1 hover:shadow-md` và `group-hover:scale-105` cho ảnh |
| 3 | Nhóm 3 | `P2` | `.../Pages/Home.vue:45` | Các section nội dung trang chủ | Toàn bộ 6 section hiển thị tĩnh hoàn toàn | Bổ sung directive/component Scroll-Reveal (`fade-up`, `duration-500`, stagger `80ms`) có hỗ trợ `prefers-reduced-motion` |

### Bảng 1.3: Quy Chuẩn Motion Tokens & `prefers-reduced-motion` Đề Xuất

Khi lập `/opsx-propose` để bổ sung animation từ kết quả kiểm toán, bắt buộc áp dụng bộ quy chuẩn sau để chống **UI Slop**:

```css
/* Global Safe Motion Rule (bắt buộc có trong app.scss / globals.css) */
@media (prefers-reduced-motion: reduce) {
  *,
  *::before,
  *::after {
    animation-duration: 0.01ms !important;
    animation-iteration-count: 1 !important;
    transition-duration: 0.01ms !important;
    scroll-behavior: auto !important;
  }
}
```

- **Micro-interaction chuẩn (Button / Link / Input)**: `transition-all duration-200 ease-out active:scale-[0.98]`
- **Card Hover chuẩn (Product / Post Card)**:
  - Khung Card: `group transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg`
  - Ảnh trong Card: `transition-transform duration-500 ease-out group-hover:scale-105`
- **Overlay / Modal / Dropdown chuẩn (Vue `<Transition>`)**:
  - Enter: `transition ease-out duration-200` (`opacity-0 scale-95 translate-y-1` -> `opacity-100 scale-100 translate-y-0`)
  - Leave: `transition ease-in duration-150` (`opacity-100 scale-100 translate-y-0` -> `opacity-0 scale-95 translate-y-1`)
