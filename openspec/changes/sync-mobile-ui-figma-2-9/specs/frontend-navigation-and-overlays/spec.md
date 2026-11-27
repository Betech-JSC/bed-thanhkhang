# Spec Delta: frontend-navigation-and-overlays

## MODIFIED Requirements

### Requirement: Khóa cuộn khung nền Popup Báo giá và cố định kích thước ô nhập ghi chú
Popup Báo giá (`QuotePopupModal.vue`) SHALL giữ nguyên vị trí toàn bộ hộp thoại khi người dùng cuộn hoặc vuốt trackpad, đồng thời đảm bảo khả năng hiển thị sạch sẽ và thao tác đóng không bị chồng lấn trên Mobile theo Figma `1329:52835`:
- Lớp backdrop bao ngoài MUST dùng `overflow-hidden overscroll-none` (tuyệt đối không dùng `overflow-y-auto` trên backdrop) để thao tác vuốt trackpad không làm xê dịch cả hộp modal.
- Trên màn hình laptop tiêu chuẩn (`height >= 768px` và `>= 1024px`), toàn bộ thẻ popup kèm nút đóng phía dưới MUST vừa khít trong viewport mà không cần cuộn thanh cuộn dọc.
- Trên thiết bị Mobile (`< 1024px`), hộp thoại MUST giới hạn chiều cao trong `max-h-[calc(100dvh-32px)]` (Desktop giữ `lg:max-h-[calc(100dvh-112px)]`) với vùng cuộn gói gọn trong thân thẻ (`overflow-y-auto overscroll-contain`), ẩn toàn bộ ảnh texture xích trang trí trên `< 1024px`, và tách nút đóng `X` (kích thước chạm `>= 44×44px`) vào một thanh đầu thẻ cố định (`shrink-0`) nằm phía trên vùng cuộn để nội dung khi cuộn không bao giờ trôi ngang qua phía sau nút `X`.
- Ô `textarea` lời nhắn MUST khóa kéo kích thước (`resize-none`) để không làm vỡ bố cục khung form.

#### Scenario: Vuốt trackpad và kiểm tra vừa khung nhìn laptop trên QuotePopupModal
- **WHEN** người dùng mở Popup Báo giá trên viewport `1366×768` và thực hiện thao tác cuộn/vuốt trackpad lên xuống
- **THEN** vị trí khung hộp thoại popup đứng yên chính giữa màn hình (`scrollTop === 0` trên backdrop), toàn bộ nội dung form và nút gửi/đóng hiển thị đầy đủ không bị cắt mép, và `textarea` có CSS `resize: none`.

#### Scenario: Cuộn nội dung Popup Báo giá trên Mobile 390×844 không che đè nút đóng X
- **WHEN** người dùng mở Popup Báo giá trên viewport Mobile `390×844` và cuộn phần nội dung form xuống dưới
- **THEN** các texture xích trang trí ẩn hoàn toàn, hộp popup giới hạn trong `max-h-[calc(100dvh-32px)]`, nút đóng `X` có vùng chạm tối thiểu `44×44px` nằm cố định ở thanh trên cùng tách biệt khỏi vùng cuộn nên chữ đã cuộn không chạy qua phía sau nút `X`, và thanh CTA gửi ở đáy luôn bám cố định dễ bấm.

---

### Requirement: Nút Scroll-to-Top tròn trên cụm Floating CTA Widget
Hệ thống SHALL duy trì cụm nút nổi `FloatingCtaWidget.vue` (gồm Scroll-to-Top, Zalo và Hotline) xếp dọc ở góc phải dưới màn hình, được thu gọn hợp lý và chừa khoảng đệm safe-area trên thiết bị di động:
- Trên Mobile (`< 1024px`), các nút tròn trong cụm (`Scroll-to-Top`, `Zalo`, `Hotline`) MUST có kích thước chuẩn vùng chạm `44×44px` (`w-11 h-11 rounded-full`), và toàn cụm đặt tại `right-3 bottom-[calc(16px+env(safe-area-inset-bottom))]` để tránh che nội dung cạnh dưới và tránh thanh Home Indicator iOS.
- Trên Desktop (`>= 1024px`), các nút giữ kích thước `60×60px` (`lg:w-[60px] lg:h-[60px]`) và vị trí `lg:right-6 lg:bottom-[72px]`.
- Nút Scroll-to-Top sử dụng bảng màu thương hiệu Thành Khang (`#00284B` / `#005EA4`, chữ/icon trắng), có `aria-label` rõ ràng, focus ring chuẩn truy cập và chỉ hiển thị mượt mà khi người dùng cuộn trang xuống quá ngưỡng (`window.scrollY > 300`).
- Khi nhấn nút Scroll-to-Top, trang cuộn về đầu trang (`top: 0`) với hiệu ứng mượt (`behavior: 'smooth'`) hoặc tức thì (`behavior: 'auto'`) nếu người dùng bật `prefers-reduced-motion: reduce`.

#### Scenario: Hiển thị và thao tác nút Scroll-to-Top khi cuộn trang
- **WHEN** người dùng cuộn trang xuống quá `300px` rồi nhấn vào nút Scroll-to-Top trên `FloatingCtaWidget.vue`
- **THEN** nút Scroll-to-Top xuất hiện phía trên/cùng cụm nút nổi với kích thước tròn `44px` trên Mobile / `60px` trên Desktop không gây tràn ngang, và trang cuộn mượt về vị trí `window.scrollY === 0` sau khi nhấn.

#### Scenario: Hiển thị và thao tác cụm Floating CTA Widget trên Mobile 390×844
- **WHEN** người dùng truy cập trên Mobile `390×844` và cuộn trang xuống quá `300px`
- **THEN** cụm `FloatingCtaWidget` hiển thị ở góc phải dưới cách mép phải `12px` (`right-3`) và cách đáy `calc(16px + env(safe-area-inset-bottom))`, cả 3 nút tròn (`Scroll-to-Top`, `Zalo`, `Hotline`) có kích thước đúng `44×44px`, không gây tràn ngang và khi nhấn nút Scroll-to-Top trang cuộn về `window.scrollY === 0`.
