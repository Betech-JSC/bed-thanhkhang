# Spec Delta: Chuẩn hóa Mobile UX, Safe-Area, Touch Targets & Khả năng Truy cập Toàn Site

## Purpose

Đặc tả các cải tiến Mobile UX và Accessibility toàn site theo chuẩn Figma `2:9` (`1314:51988`) và tiêu chuẩn tương tác cảm ứng: chiều cao Header Mobile `94px`, safe-area iOS, bỏ khóa zoom viewport, chống iOS auto-zoom ở ô tìm kiếm, vùng chạm `>= 44×44px`, chống chồng lấn trên Hero trang Quy trình và cuộn bảng kỹ thuật an toàn trên trang Chi tiết Sản phẩm.

## ADDED Requirements

### Requirement: Header Mobile Topbar 33px (Tổng 94px), Drawer Safe-Area và Không Language Switcher
Hệ thống Header và ngăn kéo điều hướng Mobile (`Header`) SHALL đáp ứng chính xác kích thước Figma `1314:51988`–`1314:51989` và quyết định UX đã chốt:
- Thanh thông tin trên cùng ở Mobile (`< 1024px`) MUST có chiều cao `33px` (thay cho `31px`), kết hợp với thanh điều hướng chính `61px` để đạt tổng chiều cao Header Mobile là `94px` đồng bộ với biến CSS layout toàn cục.
- Ngăn kéo điều hướng Mobile (`#mobile-drawer`) MUST giữ bố cục gọn sạch, tuyệt đối KHÔNG chèn thêm bộ chuyển đổi ngôn ngữ `VI / EN`.
- Vùng đáy của ngăn kéo điều hướng Mobile MUST có khoảng đệm an toàn `env(safe-area-inset-bottom)` để các nút CTA và thông tin liên hệ không bị che bởi thanh Home Indicator trên iOS.

#### Scenario: Đo chiều cao Header Mobile và kiểm tra Drawer không có bộ chuyển ngôn ngữ
- **WHEN** người dùng truy cập website trên Mobile `390×844` và mở ngăn kéo điều hướng
- **THEN** thanh topbar Mobile cao `33px`, thanh điều hướng chính cao `61px` (tổng `94px`), ngăn kéo không chứa nút chuyển ngôn ngữ `VI / EN`, và vùng đáy ngăn kéo áp dụng đệm `env(safe-area-inset-bottom)`.

---

### Requirement: Viewport Meta Cho Phép Zoom Trợ Năng và Hỗ Trợ Safe-Area Cover
Tài liệu HTML gốc (`app.blade.php`) SHALL cấu hình thẻ `<meta name="viewport">` cho phép người dùng thu phóng trang phục vụ khả năng tiếp cận (accessibility) và phủ toàn màn hình an toàn trên thiết bị có tai thỏ:
- Thuộc tính `content` MUST là `width=device-width, initial-scale=1, viewport-fit=cover` và tuyệt đối KHÔNG chứa `maximum-scale=1` hay `user-scalable=no`.

#### Scenario: Kiểm tra thẻ meta viewport trên HTML render
- **WHEN** trình duyệt tải bất kỳ trang giao diện nào của Website Thành Khang
- **THEN** thẻ `<meta name="viewport">` có `content="width=device-width, initial-scale=1, viewport-fit=cover"` và không có chỉ thị `maximum-scale=1`.

---

### Requirement: Chống iOS Safari Auto-Zoom Trên Các Ô Tìm Kiếm
Các ô nhập liệu tìm kiếm tại Trang Tin tức (`Posts/Index`) và Thư viện Tài liệu Kỹ thuật (`DocumentLibrarySection`) SHALL có cỡ chữ tối thiểu `16px` (`text-base`) trên thiết bị Mobile (`< 1024px`) để trình duyệt iOS Safari không tự động phóng to khung nhìn khi chạm nhập từ khóa, đồng thời giữ `14px` trên Desktop (`>= 1024px`).

#### Scenario: Chạm ô tìm kiếm trên Mobile ở /tin-tuc và /tai-lieu/thu-vien
- **WHEN** kiểm tra style tính toán (`font-size`) của ô nhập tìm kiếm trên Mobile (`< 1024px`) và Desktop (`>= 1024px`) tại `/tin-tuc` và `/tai-lieu/thu-vien`
- **THEN** `font-size` trên Mobile đạt `16px` (`1rem`) và trên Desktop đạt `14px`.

---

### Requirement: Vùng Chạm (Touch Target) Tối Thiểu 44×44px Trên Thiết Bị Di Động
Mọi nút biểu tượng và điều khiển tương tác chính trên Mobile (`< 1024px`) SHALL bảo đảm vùng chạm hiệu dụng tối thiểu `44×44px` (`min-width >= 44px`, `min-height >= 44px`) mà không phá vỡ thẩm mỹ khung nhìn:
- Header Mobile: nút Tìm kiếm và nút Hamburger; các nút mở rộng accordion cấp danh mục và liên kết danh mục lá bên trong Mobile Drawer.
- Hero Carousel (`HeroSection`): các nút chấm chuyển slide (dot tabs) và 2 nút mũi tên Prev/Next.
- Modal & Tìm kiếm: nút đóng `X` trên `QuotePopupModal`, nút xóa từ khóa `X` trong `SearchModal` và `DocumentLibrarySection`.
- Footer Mobile: các liên kết icon mạng xã hội (`Facebook`, `LinkedIn`).

#### Scenario: Đo kích thước boundingBox vùng chạm các phần tử tương tác trên Mobile 390×844
- **WHEN** đo `width` và `height` của các nút Search/Hamburger trên Header, các nút accordion trong Mobile Drawer, các nút dot tabs & mũi tên Hero Carousel, nút xóa `X` trên SearchModal & DocumentLibrarySection, nút đóng `X` trên QuotePopupModal và các nút mạng xã hội Footer ở viewport `390×844`
- **THEN** mỗi phần tử tương tác đều có chiều rộng hiệu dụng `>= 44px` và chiều cao hiệu dụng `>= 44px` (sai số layout bằng `0`, không gây tràn ngang).

---

### Requirement: Hero Trang Quy Trình Không Chồng Lấn Giữa CTA Mobile và Huy Hiệu Xoay 360°
Khối Hero trên trang Quy trình (`/quy-trinh`, component `ProcessHero` trong `Pages/Process/Index`) SHALL bố trí nút CTA Mobile (`"Khám phá 5 bước"`) và huy hiệu xoay tròn (`CircularBadge`) tách biệt rõ ràng tại viewport `390×844` (cũng như không để huy hiệu vị trí tuyệt đối đè lên văn bản ở khoảng tablet `sm:`):
- Nút CTA Mobile MUST đạt vùng chạm `min-h-[44px]` và có khoảng cách an toàn với `CircularBadge`, tuyệt đối không để bounding box của nút CTA giao cắt (`overlap`) với bounding box của `CircularBadge`.

#### Scenario: Kiểm tra không chồng lấn Hero CTA và CircularBadge tại 390×844
- **WHEN** người dùng truy cập `/quy-trinh` trên viewport `390×844`
- **THEN** nút CTA `"Khám phá 5 bước"` và `CircularBadge` hiển thị đầy đủ với `height >= 44px` cho CTA, vùng bao hình học (`boundingBox`) của hai phần tử hoàn toàn tách rời nhau (`0px` chồng lấn), và không có tràn ngang.

---

### Requirement: Bọc Cuộn Ngang Riêng Cho Bảng Rộng Trong Chi Tiết Sản Phẩm
Phần nội dung mô tả chi tiết HTML (`.pdp-richtext`) trong trang Chi tiết Sản phẩm (`ProductDetailTabs`) SHALL bọc từng thẻ `<table>` rộng bên trong một wrapper cuộn ngang cục bộ riêng biệt (tương đồng `.article-table-wrap` của trang bài viết), thay vì đặt `overflow-x: auto` lên toàn bộ container `.pdp-richtext`:
- Khi bảng thông số HTML rộng hơn màn hình Mobile (`390px`), chỉ duy nhất khung bọc bảng cho phép vuộn/cuộn ngang, còn các đoạn văn bản, tiêu đề và danh sách xung quanh giữ nguyên bề rộng khớp khung đọc, không bị trượt ngang theo bảng và không gây tràn ngang toàn trang.

#### Scenario: Hiển thị bảng HTML rộng trong tab Mô tả tổng quan trên Mobile 390×844
- **WHEN** mở trang Chi tiết Sản phẩm (`/san-pham/{slug}`) có bảng `<table>` rộng trong nội dung HTML trên viewport `390×844`
- **THEN** thẻ `<table>` nằm gọn trong wrapper cuộn ngang độc lập (`overflow-x: auto`), đoạn văn bản `<p>` và danh sách phía trên/dưới không cuộn ngang theo bảng, và toàn trang giữ `scrollWidth <= innerWidth`.
