# Design: Kiến trúc & Quyết định Kỹ thuật (`fix-ui-bugs-2711`)

## Context

Xem `proposal.md - Why` để nắm động lực tổng thể của 9 nhóm vấn đề (11 hạng mục) từ báo cáo lỗi ngày 27/11.

Qua đối chiếu đồ thị AST thực tế từ `graphify-out/GRAPH_REPORT.md` và mã nguồn hiện hữu:
- **Frontend Validation & Composables**: Dự án đã có sẵn `resources/Frontend/js/lib/validator.js` (đang được gọi bởi `FormCv.vue`, `FieldSet.vue`, `FormContact.vue`) cùng `resources/Frontend/js/composables/{useQuoteModal,useTranslate,useTheme,useScrollReveal}.js`. Trong khi đó `QuotePopupModal.vue` (`L557-561`), `Contact.vue` (`L761-768`), và `Home/Components/ContactSection.vue` (`L476-485`) đang viết logic regex kiểm thực SĐT rời rạc với mức độ chặt lỏng khác nhau.
- **Backend Validation Rules**: Hệ thống đã có thư mục `app/Rules/` chứa `app/Rules/Blackwords.php` (được `Contact` sử dụng). `ContactController@store` (`app/Http/Controllers/Frontend/ContactController.php:191`) hiện dùng `preg_replace('/\D+/', '', $trimmedPhone)` khiến ký tự chữ cái bị âm thầm gọt bỏ trước khi kiểm tra độ dài `8–15`.
- **Admin CMS hai tầng**: Cấu trúc giao diện quản trị tách giữa core package `packages/bed-package-essentials/core/resources/Backend/` (`tailwind.config.js`, `scss/app.scss`, `views/app.blade.php`, `Layouts/{Guest,Authenticated}.vue`, `Pages/Auth/*`, `Components/Sidebar/*`) và app-level overrides `resources/Backend/js/` (`Components/SidebarMain.vue`, `Pages/Dashboard.vue`, `Pages/UserGuide.vue`, `Pages/Products/Products/Form.vue`). Tài nguyên `/assets/images/logo/logo-thanhkhang.png` và bộ favicon `/favicon-{16x16,32x32}.png`, `/apple-touch-icon.png`, `/favicon.ico` đã tồn tại trong `public/`.

## Goals / Non-Goals

**Goals:**
- Chuẩn hóa lại toàn bộ các sai lệch UI/UX trên Desktop (`1440×900`, `1366×768`) và Mobile (`375×812`) đối với Catalog Filter, Product Detail Tabs, Quote Popup Modal, Hero Alignment, Header Mega-Menu, Floating CTA, Policy Page và Admin CMS.
- Thống nhất một quy tắc kiểm thực SĐT duy nhất (Single Source of Truth) giữa `resources/Frontend/js/lib/validator.js` và `app/Rules/ValidPhoneNumber.php`, đảm bảo hành vi nhất quán trên cả client lẫn server.
- Tái sử dụng tối đa hạ tầng hiện có (`lib/validator.js`, `app/Rules/`, `BaseModal.vue`, `.app-container-narrow`, `/assets/images/logo/logo-thanhkhang.png`, `/favicon-*.png`) theo nguyên tắc Graphify AST First.

**Non-Goals:**
- Không đụng tới `resources/Frontend/js/Pages/Process/Index.vue` và `resources/Frontend/js/Pages/Services/*` (không thuộc menu đang hoạt động).
- Không ẩn, lọc bỏ hoặc xóa danh mục `Bánh xe thang cuốn` trong DB/Admin/Frontend dù đang có `0` danh mục con và `0` sản phẩm.
- Không thay đổi cấu trúc database, không chạy migration phá hủy hay xóa bất kỳ file nào.
- Không mở rộng thay đổi sang quy tắc kiểm thực Email trong `ContactController@store` (giữ nguyên nhánh `filter_var` để bảo toàn hợp đồng `ContactFormTest`).

## Decisions

### 1. Đưa checkbox sang trái trên toàn bộ 4 trạng thái cây `ProductFilter.vue`
- **Quyết định**: Đổi thứ tự DOM đặt thẻ `<span>` checkbox (`14×14px`) đứng **trước** nhãn văn bản kèm class `flex items-center justify-start gap-2` trên cả 4 loại hàng:
  - `L0` không con (`L0` leaf, `root.children.length === 0`): thay `justify-between gap-4` khi là leaf bằng `justify-start gap-2` (vẫn giữ `justify-between` cho `L0` có con với chevron bên phải).
  - Nhóm `L1` có con: bên trong `<button role="checkbox">` đặt checkbox trước `{{ l1.title }}` với `justify-start gap-2`, giữ nguyên hàng bọc ngoài `flex items-center justify-between gap-2` để nút chevron toggle `L2` ở sát lề phải.
  - Mục `L1` không con (`L1` leaf): đảo thứ tự checkbox lên trước nhãn với `justify-start gap-2`.
  - Mục `L2` con (`L2` leaf): đổi `justify-between gap-2` thành `justify-start gap-2` và đảo checkbox lên trước nhãn.
- **Lựa chọn thay thế đã cân nhắc**: Chỉ sửa `L2` hoặc dùng `flex-row-reverse`. Đã loại bỏ `flex-row-reverse` vì gây đảo trật tự đọc DOM/accessibility và khó bảo trì so với thứ tự DOM tự nhiên `checkbox -> label`.

### 2. Gỡ bỏ scroll thừa ở `ProductDetailTabs.vue`
- **Quyết định**: Trên container `role="tablist"`, loại bỏ `overflow-x-auto no-scrollbar`, bổ sung `flex-wrap` cho mobile hẹp (`flex flex-wrap items-center gap-x-5 gap-y-1 md:gap-[32px]`), và xóa `-mb-[1px]` trên mỗi tab button (điều chỉnh `border-b-2` ăn khớp với viền đáy hoặc đặt viền đáy trực tiếp trên tab container).
- **Lý do kỹ thuật**: Theo đặc tả CSS Overflow, khi `overflow-x: auto` kết hợp với `margin-bottom: -1px` trên phần tử con, trình duyệt tự nâng `overflow-y` thành `auto` và kích hoạt thanh cuộn dọc/ngang dư thừa dù nội dung vừa khít.

### 3. Khóa cuộn backdrop & tối ưu chiều cao laptop trên `QuotePopupModal.vue`
- **Quyết định**:
  - Đổi backdrop `<div role="dialog">` từ `overflow-y-auto overscroll-contain` thành `overflow-hidden overscroll-none` để trackpad swipe không bao giờ đẩy khung modal trôi theo scroll.
  - Giữ lớp cuộn dự phòng bên trong thân card trên màn hình rất thấp: đổi giới hạn chiều cao card từ `max-h-[90dvh] lg:max-h-none` sang `max-h-[calc(100dvh-112px)]` (tương ứng chừa không gian cho padding viewport và nút đóng tròn `w-11 h-11` bên dưới) và giữ `overflow-y-auto overscroll-contain` ở container body nội bộ.
  - Tinh chỉnh thông số desktop (`lg:p-8` hoặc `lg:h-auto`, `lg:gap-8` ở cột trái thay cho `lg:h-[508px] lg:gap-12`, và `textarea` `min-h-[112px] lg:min-h-[124px] resize-none`) để tổng chiều cao card + close button nằm gọn trong `768px` (zero scroll trên laptop phổ thông).

### 4. Căn thẳng Hero `Posts/Index.vue` & `Contact.vue` với Logo Header + dịch `.hero-scrim`
- **Quyết định**:
  - Đổi class container Hero ở `Posts/Index.vue:25` và `Contact.vue:31` từ `.app-container` (`1344px`, lề trái `80px` ở `1440px`) sang `.app-container-narrow` (`1216px`, lề trái `144px` ở `1440px`), đồng bộ hoàn toàn với `Header.vue:97`, `ProductHeroBanner.vue:45`, và `DocumentHeroSection.vue:47`.
  - Mở rộng vùng chuyển sắc của `.hero-scrim` từ dừng ở `53%` sang `62%–65%` trên Desktop (giữ điểm tối cao nhất từ `0%` tới `45%` che trọn đoạn văn bản mới dịch vào) và dùng gradient dọc/phủ toàn chiều ngang trên Mobile (`< 768px`) tương tự chuẩn WCAG của `ProductHeroBanner.vue`.
  - **Đối chiếu `AboutHeroSection.vue`**: Ghi nhận qua kiểm tra thực tế `AboutHeroSection.vue:46` hiện dùng `.app-container-wide` (`1344px`). Trong phạm vi task này giữ trạng thái kiểm tra/ghi chú đối chiếu rõ ràng để không tự ý đổi layout trang Giới thiệu vốn đã nghiệm thu ở change `feat-page-about` trừ khi có yêu cầu mở rộng riêng.

### 5. Bố cục Mega-menu 2 cột cân bằng trên `Header.vue` (giữ `Bánh xe thang cuốn`)
- **Quyết định**:
  - Giữ nguyên dữ liệu 4 danh mục gốc từ DB/Inertia shared props (`productCategories`), tuyệt đối không lọc ẩn `Bánh xe thang cuốn` (`children.length === 0`).
  - Khi `activeLevel2Groups.length >= 2`, thay `grid grid-cols-2 ...` bằng một `computed` helper phân bổ `activeLevel2Groups` thành 2 mảng `[col1, col2]` render bằng 2 cột flex dọc độc lập (`<div class="grid grid-cols-2 gap-x-3 xl:gap-x-4 items-start">` bọc 2 `<div class="flex flex-col gap-5">`):
    - Cột 1 nhận các nhóm ít/không có mục con (hoặc phân bổ cân bằng theo trọng số `1 + (sub.children?.length || 0)`), ví dụ với `Linh kiện thang cuốn`: `Tay vịn thang cuốn` + `Bánh xe thang cuốn` xếp sát nhau ở cột 1; `Xích thang cuốn` (kèm 6 mục `L2`) nằm gọn ở cột 2.
- **Lý do**: Dùng CSS Grid trực tiếp lên từng mục `sub` khiến item ở cột 1 bị kéo chiều cao hàng ngang bằng toàn bộ chiều cao của `Xích thang cuốn` ở cột 2, tạo ra khoảng trống trắng lớn phía dưới mục đầu tiên. Tách thành 2 cột flex độc lập triệt tiêu khoảng trống này với 0 thay đổi DB.

### 6. Kiến trúc Phone Validation hợp nhất FE (`lib/validator.js`) + BE (`app/Rules/ValidPhoneNumber.php`)
- **Quyết định**:
  - **Frontend**: Bổ sung module/hàm dùng chung `validatePhoneNumber(rawValue, options)` và `isValidPhoneNumber(rawValue)` trong `resources/Frontend/js/lib/validator.js` (nơi đã được Graphify ghi nhận là bộ validate chuẩn của Frontend) và cập nhật `case 'phone'` gọi về cùng logic cốt lõi. Import và dùng lại trong `QuotePopupModal.vue`, `Contact.vue`, và `Home/Components/ContactSection.vue` — loại bỏ 3 đoạn regex rời rạc hiện có.
  - **Backend**: Tạo `app/Rules/ValidPhoneNumber.php` (đặt cùng `app/Rules/Blackwords.php`), đồng thời cung cấp phương thức tĩnh `ValidPhoneNumber::validateValue(?string $raw): array` để `ContactController@store` kiểm tra trực tiếp và trả về lỗi song ngữ theo `current_locale()`.
  - **Thuật toán chuẩn hóa & kiểm thực (giống hệt nhau giữa JS & PHP)**:
    1. Trim đầu cuối; nếu rỗng -> lỗi thiếu SĐT.
    2. Kiểm tra ký tự hợp lệ thô: từ chối ngay nếu tồn tại bất kỳ chữ cái (`/[a-zA-Z\p{L}]/u`) hoặc ký tự ngoài `[0-9+\s\-\.\(\)]`, hoặc dấu `+` xuất hiện ở vị trí khác đầu chuỗi / nhiều hơn 1 lần.
    3. Rút trích `digits` và trạng thái `hasPlus = str_starts_with($trimmed, '+')`.
    4. Phân nhánh định dạng:
       - **Số Việt Nam** (nếu `!hasPlus && (startsWith('0') || startsWith('84'))`, hoặc `hasPlus && startsWith('84')`): quy đổi đầu `84` -> `0` thành `vnLocal`. Chấp nhận khi khớp di động VN (`/^0[35789]\d{8}$/`, 10 số) hoặc máy bàn VN (`/^02\d{8,9}$/`, 10–11 số). (Không chấp nhận số bắt đầu `0` nhưng sai độ dài/đầu số như `0123456` hoặc `0000000000`).
       - **Số Quốc tế** (bắt đầu `+` với mã vùng khác `84`): yêu cầu khớp `/^\+[1-9]\d{7,14}$/` (`8–15` chữ số thực).
       - Các chuỗi không bắt đầu `0`, `84`, hay `+`: từ chối để tránh số nội bộ/rác thiếu mã vùng.
    5. **Chặn số giả / spam** (áp dụng lên `digits` và phần `subscriberDigits` sau mã đầu mạng/quốc gia):
       - Toàn bộ chữ số trùng nhau (`/^(\d)\1+$/`, VD `0000000000`, `1111111111`).
       - Phần thuê bao lặp cùng một chữ số (với VN bỏ 2 số đầu `0x` hoặc `02x` còn lại toàn giống nhau `/^(\d)\1{5,}$/`, hoặc có từ `7` chữ số cuối giống hệt nhau — chặn triệt để `0900000000`, `0911111111`, `+84900000000`, `+19999999999`).
       - Chuỗi tuần tự tăng hoặc giảm liên tiếp (`0123456789`, `9876543210`, hoặc `subscriberDigits` / `digits` chứa/trùng dãy tăng/giảm >= 7 ký số liên tục như `0912345678`, `0987654321`).

### 7. Bổ sung Scroll-to-Top tròn trên `FloatingCtaWidget.vue`
- **Quyết định**: Thêm biến trạng thái `showScrollTop` (`window.scrollY > 300`), gắn listener `scroll` (`{ passive: true }`) trong `mounted()` và dọn dẹp trong `beforeUnmount()`. Mở rộng điều kiện hiển thị khối container ngoài thành `v-if="zaloHref || phoneHref || showScrollTop"`. Nút Scroll-to-Top có class `w-[52px] h-[52px] lg:w-[60px] lg:h-[60px] rounded-full bg-[#00284B] hover:bg-[#005EA4] text-white shadow-lg`, và hàm `scrollToTop()` kiểm tra `window.matchMedia?.('(prefers-reduced-motion: reduce)').matches` trước khi gọi `window.scrollTo({ top: 0, behavior })`.

### 8. Light Theme cho `Policy.vue` & `BaseModal.vue` (`variant="light"`)
- **Quyết định**:
  - Trong `BaseModal.vue`, dùng một `computed` object ánh xạ token class theo `props.variant` (`dark` mặc định giữ nguyên để không làm gãy contract của `base-components`, và `light` dùng `bg-white border-[#E3E8EF]`, backdrop `bg-[#00284B]/50`, title `#00284B`, body `#4B5565`, close button `#697586` hover `#00284B` / `#EEF2F6`).
  - Trong `Contact.vue:431`, truyền `variant="light"` cho `<BaseModal>` và đổi `text-white` / `text-[#B9C0D4]` sang `text-[#00284B]` / `text-[#4B5565]`.
  - Trong `Policy.vue`, tái cấu trúc giao diện sang nền `#FAFAFA`, `.app-container-narrow`, card trắng `#FFFFFF` viền `#E3E8EF`, active tab/link `#EDF6FF` viền `#005EA4` chữ `#005EA4`, `.policy-prose` màu `#364152` và heading `#00284B` viền trái `#005EA4`.

### 9. Đồng bộ Brand Theme Admin CMS
- **Quyết định**:
  - `core/resources/Backend/tailwind.config.js`: đổi `primary` từ `#2970FF` sang dải `#005EA4` (`DEFAULT: '#005EA4'`, `hover/dark/600: '#004D86'`, `active/darker/700: '#003967'`, `50: '#EDF6FF'`, `100: '#C9E0FF'`, `800: '#00284B'`, `900: '#001C36'`).
  - `core/resources/Backend/views/app.blade.php`: gỡ `<link rel="icon" ... href="/assets/images/header/hh-maker-icon.svg?v=2">` và dùng bộ icon Thành Khang đã tồn tại ở `public/` (`/favicon-32x32.png?v=20261127`, `/favicon-16x16.png?v=20261127`, `/apple-touch-icon.png?v=20261127`, `/favicon.ico?v=20261127`).
  - `Guest.vue` & Auth Pages (`Login.vue`, `ForgotPassword.vue`, `ResetPassword.vue`, `ConfirmPassword.vue`, `VerifyEmail.vue`):
    - `Guest.vue` bỏ grid heated-bed và blur glow `#2970FF`, dùng nền `#FAFAFA` (kết hợp dải nhấn hoặc thanh viền trên `#00284B`/`#005EA4` tinh tế), card bọc trắng sạch `bg-white border border-[#E3E8EF] shadow-lg rounded-2xl`.
    - `Login.vue` thay khung chữ `THÀNH KHANG TECSAT` + badge `THANH KHANG TECSAT CMS • v2.0` bằng ảnh `/assets/images/logo/logo-thanhkhang.png` rõ nét trên nền trắng, ô input sáng viền `#D6D6D6` focus `#005EA4`, nút submit `#005EA4` hover `#004D86`.
    - Các trang `ForgotPassword.vue`, `ResetPassword.vue`, `ConfirmPassword.vue`, `VerifyEmail.vue` đặt nội dung bên trong card trắng đồng bộ (lưu ý sửa lỗi tag `<GuestLayout>` thừa/không khai báo trong `VerifyEmail.vue` nếu cần vì `layout: Guest` đã bọc sẵn bên ngoài!).
  - Sidebar & Admin Pages (`SidebarDesktop.vue`, `SidebarMobile.vue`, `SidebarItems.vue`, `SidebarAccount.vue`, `Authenticated.vue`, `SidebarMain.vue`, `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`):
    - Nền Sidebar `bg-[#00284B]` (thay `bg-gray-900` / `bg-gray-800`), thanh mobile topbar `bg-[#00284B]`, chân Sidebar `SidebarAccount.vue` dùng `bg-[#001C36]`.
    - Trong `SidebarItems.vue`, đặt `/assets/images/logo/logo-thanhkhang.png` bên trong khung bo nền trắng (`bg-white rounded-lg px-3 py-2 flex items-center justify-center`) để wordmark tối màu hiển thị sắc nét trên nền `#00284B`.
    - Trạng thái menu `.item.active` dùng `bg-[#005EA4] text-[#FAFAFA] rounded-lg` (hoặc highlight `#C9E0FF`), switcher ngôn ngữ `VI`/`EN` dùng `btn-primary` (`#005EA4`).
    - `SidebarMain.vue`: gỡ emoji (`📊`, `🎨`, `⚙️`) khỏi `.sidebar-section-header`, đổi màu chữ tiêu đề nhóm sang `text-[#81BDFF]/80` hoặc `text-[#C9E0FF]/70` tương phản tốt trên `#00284B`.
    - `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`: thay màu nhấn `blue-600` / `#2970FF` sang `primary` (`#005EA4`) và rà soát loại bỏ chữ `Tecsat` khỏi tiêu đề/nhãn hiển thị người dùng Admin để đồng bộ thương hiệu Thành Khang.

## Risks / Trade-offs

- **Xung đột dữ liệu test SĐT giả trong `ContactFormTest.php`** → `test_contact_with_blackwords_marked_as_spam_and_soft_deleted` (`tests/Feature/ContactFormTest.php:280`) đang truyền SĐT `'0900000000'`. Sau khi áp dụng `ValidPhoneNumber`, payload này sẽ bị chặn ở bước validation `422` trước khi chạy tới logic `Blackwords`. Mitigation: cập nhật SĐT của test case `Blackwords` sang một số hợp lệ thật (như `'0901234568'`) và bổ sung các test case mới kiểm chứng riêng cho SĐT chữ cái, SĐT lặp `0900000000`/`0911111111`, SĐT tuần tự `0123456789`/`9876543210` và SĐT quốc tế `+`.
- **Chỉnh sửa trực tiếp trong `packages/bed-package-essentials/core/resources/Backend/`** → Đây là package nội bộ được theo dõi trong repo (`vite.backend.config.ts` alias `@Core`). Mitigation: kiểm chứng lại bằng `yarn backend:build` và Playwright Admin UI sau khi sửa để chắc chắn CSS/JS được compile đầy đủ.
- **Nhãn `Tecsat` ở các trang phụ khác ngoài phạm vi (nếu có)** → Chỉ rà soát sạch ở các file thuộc phạm vi (`Login.vue`, `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`, `SidebarItems.vue`, `SidebarMain.vue`) để tránh thay đổi tràn lan ngoài scope.
