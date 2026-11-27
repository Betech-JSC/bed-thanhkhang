# Proposal: Khắc phục tổng thể lỗi UI/UX, Validation số điện thoại & Đồng bộ Theme Thành Khang (`fix-ui-bugs-2711`)

## Why

Qua đợt rà soát nghiệm thu giao diện và tương tác ngày 27/11 trên website Thành Khang (`bed-thanhkhang`), hệ thống còn tồn tại 9 nhóm vấn đề ảnh hưởng trực tiếp đến độ chỉn chu B2B, khả năng sử dụng trên desktop/laptop/mobile, tính nhất quán thương hiệu và chất lượng dữ liệu lead:
1. Cây lọc danh mục `ProductFilter.vue` đang đặt checkbox ở bên phải nhãn (hoặc căn lề phải ở `L2`), gây lệch trục thị giác khi quét danh mục 3 tầng.
2. Thanh tab `ProductDetailTabs.vue` (`role="tablist"`) dùng `overflow-x-auto no-scrollbar` kết hợp `-mb-[1px]` tạo thanh cuộn ngang/dọc thừa trên tiêu đề tab.
3. Popup báo giá `QuotePopupModal.vue` dùng `overflow-y-auto` trên lớp backdrop khiến thao tác vuốt trackpad làm trôi cả khung popup, chiều cao trên màn hình laptop `768px+` dễ chạm mép trên/dưới, và ô `textarea` còn cho phép kéo dãn (`resize-y`).
4. Hero Banner của `Posts/Index.vue` và `Contact.vue` dùng `.app-container` (`1344px` — mép trái `80px` ở `1440px`) nên bị lệch `64px` sang trái so với logo `Header.vue` (`.app-container-narrow`, mép trái `144px`).
5. Mega-menu sản phẩm trên `Header.vue` dùng `grid grid-cols-2` dẫn đến khoảng trống dọc lớn khi một nhóm `L1` không có con (`Bánh xe thang cuốn` — hiện có 0 danh mục con và 0 sản phẩm trong DB) xếp cạnh nhóm `L1` có nhiều mục con `L2` (`Xích thang cuốn`).
6. Kiểm thực số điện thoại giữa `QuotePopupModal.vue`, `Contact.vue`, `ContactSection.vue` và `ContactController.php` chưa đồng nhất: PHP dùng `preg_replace('/\D+/', '', ...)` vô tình lọc bỏ chữ cái trước khi đếm độ dài, chưa hỗ trợ chuẩn hóa quy tắc VN + quốc tế `+` và chưa chặn số giả/spam (trùng chữ số, lặp đuôi, tăng/giảm tuần tự).
7. Cụm nút nổi `FloatingCtaWidget.vue` mới có Zalo và Hotline, chưa có nút Scroll-to-Top tròn đồng bộ.
8. Trang Chính sách `Policy.vue` và modal báo gửi thành công của `Contact.vue` (`BaseModal.vue`) vẫn mang giao diện nền tối Dark Tech cũ (`#111322`, `#1A1C32`, `#243971`, `#528BFF`), lạc tông hoàn toàn so với hệ thống giao diện sáng Thành Khang.
9. Phân hệ Admin CMS (`Guest.vue`, `Login.vue`, các trang Auth phụ, Sidebar, `tailwind.config.js`, `app.scss`, `app.blade.php`, `SidebarMain.vue`, `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`) vẫn còn màu `#2970FF` / `#111111` / `#0B0D19`, nền lưới 3D printer, huy hiệu `THÀNH KHANG TECSAT CMS • v2.0`, tiêu đề chứa `Tecsat` / emoji và favicon `hh-maker-icon.svg`.

## What Changes

1. **Bộ lọc danh mục 3 tầng (`ProductFilter.vue`)**:
   - Đưa ô checkbox `14×14px` sang bên **TRÁI** nhãn văn bản (`flex items-center justify-start gap-2`) đồng nhất trên tất cả các cấp (`L0` leaf, `L1` group, `L1` leaf, `L2` leaf), đồng thời giữ nguyên nút chevron đóng/mở bên phải cho nhóm `L1` có danh mục con (`justify-between` ở hàng bọc ngoài).
2. **Thanh Tab Chi tiết sản phẩm (`ProductDetailTabs.vue`)**:
   - Gỡ `overflow-x-auto no-scrollbar` và `-mb-[1px]` trên `role="tablist"`, cho phép xuống dòng an toàn (`flex-wrap`) ở viewport di động hẹp để loại bỏ hoàn toàn tình trạng cuộn ngang/dọc trên thanh tiêu đề tab.
3. **Popup Báo giá (`QuotePopupModal.vue`)**:
   - Đổi lớp backdrop từ `overflow-y-auto` sang `overflow-hidden overscroll-none` để khung popup không bị trượt theo trackpad; giới hạn vùng cuộn bên trong thân card trên màn hình quá thấp (`max-h-[calc(100dvh-112px)] overflow-y-auto overscroll-contain`), tinh chỉnh khoảng đệm/chiều cao desktop để hiển thị trọn vẹn không cần cuộn trên laptop `768px+`, và đổi `textarea` từ `resize-y` sang `resize-none`.
4. **Căn thẳng Hero Banner với Logo Header (`Posts/Index.vue`, `Contact.vue`)**:
   - Chuyển container Hero từ `.app-container` (`1344px`) sang `.app-container-narrow` (`1216px` / `1152px` inner) để mép chữ Hero thẳng hàng `144px` với logo `Header.vue` ở viewport `1440px`.
   - Dịch vùng phủ `.hero-scrim` sang phải (~`62%–65%`) để che mượt vùng chữ sau khi dịch vào trong, đồng thời đảm bảo scrim tương phản WCAG trên Mobile. (Ghi nhận đối chiếu `AboutHeroSection.vue` trong phần thiết kế.)
5. **Bố cục Mega-menu 2 cột cân bằng (`Header.vue`)**:
   - **Giữ nguyên** hiển thị danh mục `Bánh xe thang cuốn` dù hiện có 0 danh mục con và 0 sản phẩm trong DB (không ẩn và không xóa danh mục DB).
   - Thay `grid grid-cols-2` ở vùng bên phải Mega-menu bằng bố cục 2 cột độc lập cân bằng (hai cột flex song song: đưa các nhóm `L1` ít/không có con như `Tay vịn thang cuốn` + `Bánh xe thang cuốn` xếp chồng gọn ở cột 1, nhóm `L1` nhiều con như `Xích thang cuốn` + 6 mục `L2` ở cột 2) để triệt tiêu khoảng hở dọc thừa.
6. **Chuẩn hóa Validation Số điện thoại FE + BE (`validator.js`, `QuotePopupModal.vue`, `Contact.vue`, `ContactSection.vue`, `ValidPhoneNumber.php`, `ContactController.php`)**:
   - Trích xuất hàm/rule kiểm thực số điện thoại dùng chung ở Frontend (`resources/Frontend/js/lib/validator.js`) và Backend (`app/Rules/ValidPhoneNumber.php` tích hợp vào `ContactController@store`).
   - Từ chối ngay mọi chuỗi có ký tự chữ cái (Backend không tự ý xóa chữ trước khi đếm chữ số).
   - Ưu tiên các đầu số hợp lệ Việt Nam (`0[35789]\d{8}`, máy bàn `02\d{8,9}`, hoặc tiền tố `+84`/`84`) đồng thời hỗ trợ số quốc tế bắt đầu bằng `+` có `8–15` chữ số thực.
   - Chặn số giả/spam: toàn bộ chữ số giống nhau (`0000000000`, `1111111111`), lặp cùng chữ số phần thuê bao (`0900000000`, `0911111111`), hoặc dãy tăng/giảm liên tiếp (`0123456789`, `9876543210`).
7. **Nút Scroll-to-Top (`FloatingCtaWidget.vue`)**:
   - Bổ sung nút tròn Scroll-to-Top (`w-[52px] h-[52px] lg:w-[60px] lg:h-[60px] rounded-full`, tông `#00284B` / `#005EA4`) hiển thị mượt khi `window.scrollY > 300`, cuộn về đầu trang tôn trọng `prefers-reduced-motion` cùng cột với Zalo và Hotline.
8. **Chuyển đổi Giao diện Sáng cho Trang Chính sách & Modal Liên hệ (`Policy.vue`, `BaseModal.vue`, `Contact.vue`)**:
   - Thiết kế lại `Policy.vue` từ giao diện tối Dark Tech sang giao diện sáng B2B Thành Khang (`#FAFAFA`, `#FFFFFF`, `#E3E8EF`, `#00284B`, `#005EA4`, `.app-container-narrow`, `.policy-prose` chữ tối dễ đọc).
   - Bổ sung prop `variant` (`'dark' | 'light'`, mặc định `'dark'`) cho `BaseModal.vue` và áp dụng `variant="light"` cho modal gửi thành công tại `Contact.vue`.
   - `Process/Index.vue` và `Services/*` giữ nguyên **ngoài phạm vi** vì không nằm trong menu hoạt động hiện tại.
9. **Đồng bộ Theme Thương hiệu cho Admin CMS (`Guest.vue`, `Login.vue`, Auth pages, Sidebar, `tailwind.config.js`, `app.scss`, `app.blade.php`, `SidebarMain.vue`, `Dashboard.vue`, `UserGuide.vue`, `Products/Products/Form.vue`)**:
   - Chuyển bảng màu Admin từ `#2970FF` / `#111111` / `#0B0D19` sang `#005EA4` / `#00284B` / `#FAFAFA`.
   - Làm mới `Guest.vue`, `Login.vue` và các trang Auth phụ (`ForgotPassword.vue`, `ResetPassword.vue`, `ConfirmPassword.vue`, `VerifyEmail.vue`) sang card trắng sạch (`#FFFFFF`, `border-[#E3E8EF]`) trên nền `#FAFAFA` hoặc `#00284B`, gỡ bỏ lưới mesh 3D printer và huy hiệu cũ `THÀNH KHANG TECSAT CMS • v2.0`, dùng `/assets/images/logo/logo-thanhkhang.png` và nút CTA `#005EA4`.
   - Chuyển Sidebar (`SidebarDesktop.vue`, `SidebarMobile.vue`, `SidebarItems.vue`, `SidebarAccount.vue`, `Authenticated.vue`) sang nền Dark Navy `#00284B` (`#001C36` cho phần tài khoản chân sidebar), mục active `#005EA4` / `#C9E0FF`, đặt `/assets/images/logo/logo-thanhkhang.png` trong khung bo trắng sạch để đảm bảo độ tương phản với wordmark tối màu, đồng bộ bộ chuyển ngôn ngữ `VI`/`EN` sang `#005EA4`, gỡ bỏ chữ `Tecsat` và các ký tự emoji ở nhóm menu `SidebarMain.vue`, và thay thế favicon `hh-maker-icon.svg` trong `app.blade.php`.

## Capabilities

### New Capabilities
- `frontend-navigation-and-overlays`: Bố cục Mega-menu 2 cột cân bằng trên `Header.vue` (bảo toàn danh mục trống), căn trục Hero `.app-container-narrow` và `.hero-scrim` cho `Posts/Index.vue` & `Contact.vue`, khóa cuộn backdrop `QuotePopupModal.vue` và nút Scroll-to-Top trong `FloatingCtaWidget.vue`.
- `phone-validation`: Luật kiểm thực số điện thoại hợp nhất giữa Frontend (`validator.js` -> `QuotePopupModal.vue`, `Contact.vue`, `ContactSection.vue`) và Backend (`ValidPhoneNumber` -> `ContactController.php`), hỗ trợ định dạng VN/quốc tế `+` (`8–15` chữ số), từ chối chữ cái và chặn số giả/spam.
- `policy-light-theme`: Giao diện sáng chuẩn B2B Thành Khang cho `Policy.vue` và trạng thái modal thành công nền sáng trên `Contact.vue`.
- `admin-cms-brand-theme`: Hệ thống màu sắc, màn hình đăng nhập/xác thực, Sidebar Dark Navy `#00284B`, brand logo, favicon và nội dung giao diện Admin CMS đồng bộ thương hiệu Thành Khang.

### Modified Capabilities
- `product-catalog`: Cập nhật Requirement `Bộ lọc danh mục 3 tầng 264px (Figma Node 979:30394)` đưa checkbox `14×14px` sang bên TRÁI chữ ở mọi cấp (`L0` leaf, `L1` group, `L1` leaf, `L2` leaf), và bổ sung Requirement `Thanh tab Chi tiết sản phẩm (ProductDetailTabs) không cuộn dư thừa`.
- `base-components`: Cập nhật Requirement `BaseModal Component với Teleport và Transition` bổ sung prop `variant` (`'dark' | 'light'`) giữ tương thích ngược mặc định `'dark'`.

## Impact

- **Frontend Components & Pages**:
  - `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductDetailTabs.vue`
  - `resources/Frontend/js/Components/QuotePopupModal.vue`
  - `resources/Frontend/js/Pages/Posts/Index.vue`
  - `resources/Frontend/js/Pages/Contact.vue`
  - `resources/Frontend/js/Components/Header.vue`
  - `resources/Frontend/js/Components/FloatingCtaWidget.vue`
  - `resources/Frontend/js/Pages/Policy.vue`
  - `resources/Frontend/js/Components/Base/BaseModal.vue`
  - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`
  - `resources/Frontend/js/lib/validator.js`
- **Backend PHP & Tests**:
  - `app/Rules/ValidPhoneNumber.php` (mới, cạnh `app/Rules/Blackwords.php`)
  - `app/Http/Controllers/Frontend/ContactController.php`
  - `tests/Feature/ContactFormTest.php` (cập nhật case spam từ `0900000000` sang SĐT hợp lệ và bổ sung test cases cho luật SĐT mới)
- **Admin CMS (Project + Core Package)**:
  - `packages/bed-package-essentials/core/resources/Backend/tailwind.config.js`
  - `packages/bed-package-essentials/core/resources/Backend/scss/app.scss`
  - `packages/bed-package-essentials/core/resources/Backend/views/app.blade.php`
  - `packages/bed-package-essentials/core/resources/Backend/js/Layouts/{Guest,Authenticated}.vue`
  - `packages/bed-package-essentials/core/resources/Backend/js/Pages/Auth/{Login,ForgotPassword,ResetPassword,ConfirmPassword,VerifyEmail}.vue`
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/{SidebarDesktop,SidebarMobile,SidebarItems,SidebarAccount}.vue`
  - `resources/Backend/js/Components/SidebarMain.vue`
  - `resources/Backend/js/Pages/{Dashboard,UserGuide}.vue`
  - `resources/Backend/js/Pages/Products/Products/Form.vue`
- **Kiểm thử E2E & Build**:
  - `yarn frontend:build`, `yarn backend:build`, PHPUnit (`ContactFormTest`, `PolicyTest`, v.v.) và Playwright Visual QA (`tests/e2e/`).

## Phạm vi an toàn (Strict Safe Guard)

- **Tuyệt đối KHÔNG xóa file** (`rm`, `Remove-Item`) dưới bất kỳ hình thức nào.
- **Tuyệt đối KHÔNG thao tác phá hủy Database**: không `migrate:fresh`, `drop`, `truncate`, không sửa dữ liệu hay ẩn/xóa danh mục `Bánh xe thang cuốn` trong DB.
- **Giữ nguyên ngoài phạm vi**: Không chỉnh sửa `resources/Frontend/js/Pages/Process/Index.vue` và `resources/Frontend/js/Pages/Services/*` vì không thuộc các menu đang kích hoạt.
- **Bảo toàn backward compatibility**: `BaseModal.vue` giữ mặc định `variant="dark"` để không phá vỡ contract của `base-components`; kiểm tra `ContactFormTest.php` để đảm bảo các luồng tương thích payload cũ không bị gãy ngoài quy tắc SĐT mới.
