# frontend-navigation-and-overlays Specification

## Purpose

Chuẩn hóa tương tác điều hướng, bố cục Hero và các lớp phủ toàn cục trên Website Thành Khang, gồm Mega-menu 2 cột cân bằng, căn thẳng Hero Banner theo container `.app-container-narrow`, khóa cuộn Popup Báo giá và bổ sung nút Scroll-to-Top nổi.

## Requirements

### Requirement: Mega-menu Sản phẩm 2 cột cân bằng và bảo toàn danh mục DB
Hệ thống SHALL duy trì hiển thị đầy đủ tất cả danh mục sản phẩm đang kích hoạt trong cơ sở dữ liệu (bao gồm danh mục `Bánh xe thang cuốn` dù hiện có 0 danh mục con và 0 sản phẩm) và bố trí vùng bên phải của Mega-menu (`Header.vue`) theo bố cục 2 cột độc lập cân bằng chiều cao:
- KHÔNG ẩn hay xóa bỏ danh mục `Bánh xe thang cuốn` khỏi cơ sở dữ liệu, Mega-menu hay cây lọc.
- Khi danh mục gốc đang chọn có từ `2` nhóm cấp 1 (`L1`) trở lên, vùng phải Mega-menu MUST sắp xếp các nhóm vào 2 cột dọc độc lập (nhóm ít/không có danh mục con như `Tay vịn thang cuốn` + `Bánh xe thang cuốn` xếp chồng gọn trong cột 1; nhóm nhiều mục con `L2` như `Xích thang cuốn` kèm 6 mục `L2` hiển thị ở cột 2) thay cho lưới `grid-cols-2` gây khoảng trống dọc lớn.

#### Scenario: Hover vào danh mục Linh kiện thang cuốn trong Mega-menu Desktop
- **WHEN** người dùng mở Mega-menu `SẢN PHẨM` trên Desktop (`1440×900`) và trỏ chuột vào danh mục `Linh kiện thang cuốn`
- **THEN** `Tay vịn thang cuốn` và `Bánh xe thang cuốn` hiển thị xếp chồng gọn gàng ở cột 1 không có khoảng trống dọc thừa, `Xích thang cuốn` cùng 6 danh mục con `L2` hiển thị ở cột 2, và `Bánh xe thang cuốn` vẫn truy cập được bình thường.

---

### Requirement: Căn thẳng Hero Banner Tin tức và Liên hệ theo chuẩn .app-container-narrow
Hero Banner của trang Tin tức (`Posts/Index.vue`) và trang Liên hệ (`Contact.vue`) SHALL sử dụng container `.app-container-narrow` (`1216px` max-width / `1152px` inner, `px-4 md:px-8`) để mép trái khối chữ Hero thẳng hàng chính xác (`144px` ở viewport `1440px`) với logo thương hiệu trên `Header.vue`:
- Lớp phủ `.hero-scrim` trên Desktop MUST dịch điểm dừng gradient sang phải (tới vùng ~`62%–65%`) để bao phủ hoàn toàn khối chữ đã dịch phải mà vẫn đảm bảo ảnh nền bên phải hiển thị rõ.
- Trên thiết bị Mobile (`< 768px`), lớp phủ scrim MUST bao phủ toàn chiều rộng khung Hero để giữ độ tương phản văn bản trắng đạt chuẩn WCAG.

#### Scenario: Đối chiếu mép trái Logo Header và chữ Hero trên Desktop 1440×900
- **WHEN** người dùng truy cập `/tin-tuc` hoặc `/lien-he` (và `/en/posts`, `/en/contact`) trên viewport `1440×900`
- **THEN** tọa độ `boundingBox().x` của khối tiêu đề/breadcrumb Hero khớp với tọa độ `x` của logo trong `Header.vue` (`144px`), và toàn bộ phần chữ Hero nằm trên vùng chuyển sắc tối của `.hero-scrim` với độ tương phản dễ đọc.

---

### Requirement: Khóa cuộn khung nền Popup Báo giá và cố định kích thước ô nhập ghi chú
Popup Báo giá (`QuotePopupModal.vue`) SHALL giữ nguyên vị trí toàn bộ hộp thoại khi người dùng cuộn hoặc vuốt trackpad:
- Lớp backdrop bao ngoài MUST dùng `overflow-hidden overscroll-none` (tuyệt đối không dùng `overflow-y-auto` trên backdrop) để thao tác vuốt trackpad không làm xê dịch cả hộp modal.
- Trên màn hình laptop tiêu chuẩn (`height >= 768px`), toàn bộ thẻ popup kèm nút đóng phía dưới MUST vừa khít trong viewport mà không cần cuộn thanh cuộn dọc.
- Trên màn hình có chiều cao rất thấp (`height < 768px`), vùng cuộn được giới hạn gọn bên trong thân thẻ (`max-h-[calc(100dvh-112px)] overflow-y-auto overscroll-contain`).
- Ô `textarea` lời nhắn MUST khóa kéo kích thước (`resize-none`) để không làm vỡ bố cục khung form.

#### Scenario: Vuốt trackpad và kiểm tra vừa khung nhìn laptop trên QuotePopupModal
- **WHEN** người dùng mở Popup Báo giá trên viewport `1366×768` và thực hiện thao tác cuộn/vuốt trackpad lên xuống
- **THEN** vị trí khung hộp thoại popup đứng yên chính giữa màn hình (`scrollTop === 0` trên backdrop), toàn bộ nội dung form và nút gửi/đóng hiển thị đầy đủ không bị cắt mép, và `textarea` có CSS `resize: none`.

---

### Requirement: Nút Scroll-to-Top tròn trên cụm Floating CTA Widget
Hệ thống SHALL bổ sung nút tròn Scroll-to-Top trong `FloatingCtaWidget.vue` xếp cùng cột dọc với nút Zalo và Hotline ở góc phải dưới màn hình:
- Nút có kích thước tròn đồng bộ (`w-[52px] h-[52px] lg:w-[60px] lg:h-[60px] rounded-full`), sử dụng bảng màu thương hiệu Thành Khang (`#00284B` / `#005EA4`, chữ/icon trắng), có `aria-label` rõ ràng và focus ring chuẩn truy cập.
- Nút chỉ hiển thị mượt mà khi người dùng cuộn trang xuống quá ngưỡng (`window.scrollY > 300`).
- Khi nhấn nút, trang cuộn về đầu trang (`top: 0`) với hiệu ứng mượt (`behavior: 'smooth'`) hoặc tức thì (`behavior: 'auto'`) nếu người dùng bật `prefers-reduced-motion: reduce`.

#### Scenario: Hiển thị và thao tác nút Scroll-to-Top khi cuộn trang
- **WHEN** người dùng cuộn trang xuống quá `300px` rồi nhấn vào nút Scroll-to-Top trên `FloatingCtaWidget.vue`
- **THEN** nút Scroll-to-Top xuất hiện phía trên/cùng cụm nút nổi với kích thước tròn `52px`/`60px` không gây tràn ngang, và trang cuộn mượt về vị trí `window.scrollY === 0` sau khi nhấn.
