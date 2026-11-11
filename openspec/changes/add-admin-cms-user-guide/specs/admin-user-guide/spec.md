# Spec Delta: Admin CMS User Guide

## Purpose

Cung cấp trang Hướng dẫn sử dụng tích hợp trực tiếp trong Admin CMS HH Maker 3D để quản trị viên tra cứu quy trình vận hành từng bước cho toàn bộ 4 nhóm module nghiệp vụ, tìm kiếm từ khóa tức thì, đối chiếu quy chuẩn kích thước Media/tệp CAD/Đa ngôn ngữ và điều hướng nhanh tới đúng trang quản lý theo phân quyền.

## ADDED Requirements

### Requirement: Truy cập trang Hướng dẫn sử dụng từ Sidebar và Dashboard

Hệ thống SHALL cung cấp trang Hướng dẫn sử dụng tại đường dẫn `/admin/user-guide` (route `admin.user-guide.index`) cho mọi tài khoản quản trị đã đăng nhập (`auth:admin`), đồng thời hiển thị lối tắt truy cập trực tiếp tại nhóm `1. BẢNG ĐIỀU KHIỂN` trên thanh menu bên trái (`SidebarMain`) và nút lối tắt trên phần Header của trang `Dashboard`.

#### Scenario: Truy cập từ menu Hướng dẫn sử dụng trên Sidebar
- **WHEN** một quản trị viên đã đăng nhập vào Admin CMS và nhấn vào mục "Hướng dẫn sử dụng" trong nhóm "1. BẢNG ĐIỀU KHIỂN" trên Sidebar
- **THEN** hệ thống điều hướng tới trang `/admin/user-guide` (`admin.user-guide.index`), đánh dấu trạng thái active trên mục menu tương ứng và hiển thị đầy đủ giao diện Hướng dẫn sử dụng CMS mà không yêu cầu quyền lẻ đặc thù

#### Scenario: Truy cập từ nút lối tắt trên Header của trang Bảng điều khiển
- **WHEN** quản trị viên đang ở trang Bảng điều khiển (`/admin/dashboard`) và nhấn vào nút "Hướng dẫn sử dụng CMS" trên khu vực Header
- **THEN** hệ thống chuyển hướng sang trang `/admin/user-guide` thông qua Inertia navigation mà không tải lại toàn bộ trang

#### Scenario: Chặn truy cập khi chưa đăng nhập Admin
- **WHEN** người dùng chưa đăng nhập (`guest`) truy cập trực tiếp đường dẫn `/admin/user-guide`
- **THEN** middleware `auth:admin` SHALL chuyển hướng người dùng về trang đăng nhập quản trị (`/admin/login`)

### Requirement: Tìm kiếm từ khóa tức thì và lọc theo 4 nhóm nghiệp vụ CMS

Trang Hướng dẫn sử dụng SHALL cung cấp thanh tìm kiếm từ khóa tức thì (Instant Search) và bộ lọc Tabs tương ứng với 4 nhóm chức năng trên Sidebar (`Tất cả`, `1. Bảng điều khiển`, `2. Vận hành Xưởng In 3D`, `3. Nội dung & Thương hiệu`, `4. Hệ thống, Đa ngôn ngữ & SEO`).

#### Scenario: Lọc mục hướng dẫn theo nhóm chức năng
- **WHEN** quản trị viên chọn một Tab nhóm chức năng (ví dụ: `2. Vận hành Xưởng In 3D`)
- **THEN** danh sách chỉ hiển thị các thẻ hướng dẫn thuộc nhóm đó (Báo giá & Yêu cầu in 3D, Quản lý Sản phẩm, Danh mục Sản phẩm, Dịch vụ In & Thiết kế) và cập nhật số lượng kết quả tương ứng

#### Scenario: Tìm kiếm từ khóa tức thì trên nội dung hướng dẫn
- **WHEN** quản trị viên nhập từ khóa (ví dụ: `CAD`, `CONFIRMED`, `Flip Announcement`, hoặc `Hotline`) vào ô tìm kiếm
- **THEN** hệ thống lọc realtime các mục hướng dẫn có tiêu đề, mô tả, các bước thực hiện hoặc ghi chú chứa từ khóa (không phân biệt chữ hoa/thường), kết hợp đồng thời với bộ lọc Tab đang chọn

#### Scenario: Hiển thị trạng thái rỗng khi không khớp từ khóa
- **WHEN** quản trị viên nhập một từ khóa không khớp với bất kỳ mục hướng dẫn nào
- **THEN** hệ thống hiển thị thông báo trạng thái rỗng rõ ràng cùng nút "Xóa bộ lọc tìm kiếm" để khôi phục danh sách ban đầu trong 1 cú nhấp chuột

### Requirement: Nội dung hướng dẫn chi tiết từng bước và Bảng quy chuẩn Media/CAD/Đa ngôn ngữ

Trang Hướng dẫn sử dụng SHALL trình bày đầy đủ quy trình thao tác từng bước (Step-by-step), lưu ý vận hành thực tế cho toàn bộ các module đang active trên Sidebar của HH Maker 3D, kèm theo bảng quy chuẩn kích thước hình ảnh/Media, định dạng tệp CAD và checklist Đa ngôn ngữ `VI`/`EN` + SEO.

#### Scenario: Xem quy trình xử lý Báo giá & Tệp CAD in 3D
- **WHEN** quản trị viên mở mục hướng dẫn "Báo giá & Yêu cầu in 3D"
- **THEN** hệ thống hiển thị rõ quy trình chuyển đổi 4 trạng thái xử lý chính (`NEW` -> `RESPONSE` -> `CONFIRMED` -> `PROCESSED`) cùng 2 trạng thái kết thúc/lọc (`CLOSE`, `IS_SPAM`), và hướng dẫn kiểm tra/tải tệp CAD đính kèm của khách hàng (`.STL`, `.STEP`, `.STP`, `.OBJ`, `.IGES`, `.ZIP`, `.RAR`)

#### Scenario: Xem hướng dẫn quản lý Sản phẩm, Dịch vụ khối động & Nội dung song ngữ VI/EN
- **WHEN** quản trị viên xem các mục hướng dẫn thuộc nhóm "Vận hành Xưởng In 3D" và "Nội dung & Thương hiệu"
- **THEN** hệ thống hiển thị hướng dẫn chuyển đổi ngôn ngữ dữ liệu `Tiếng Việt (VI)` / `English (EN)` khi nhập liệu, cách cấu hình các khối dữ liệu JSON động của trang Dịch vụ (Hero, Bảng thông số, Quy trình 5 bước, Ưu điểm, Báo giá, FAQ), và cách quản lý Hero Banner cùng thanh thông báo `HOME_FLIP_ANNOUNCEMENT`

#### Scenario: Tra cứu Bảng quy chuẩn Media, Tệp CAD và Checklist SEO
- **WHEN** quản trị viên cuộn tới khu vực "Quy chuẩn Hình ảnh, Tệp CAD & Checklist Xuất bản" trên trang Hướng dẫn sử dụng
- **THEN** hệ thống hiển thị bảng kích thước khuyến nghị (Hero Banner Desktop/Mobile, Ảnh Sản phẩm 1:1, Ảnh Dịch vụ, Ảnh Bài viết 16:9, OG Image `1200x630px`), dung lượng tối đa khuyến nghị, định dạng ưu tiên (`.WEBP`, `.PNG`, `.JPG`) và checklist kiểm tra song ngữ `VI`/`EN` + làm mới Cache hệ thống

### Requirement: Liên kết điều hướng nhanh tôn trọng phân quyền quản trị

Mỗi thẻ hướng dẫn module trên trang Hướng dẫn sử dụng SHALL cung cấp nút thao tác nhanh (`Mở trang quản lý ->`) dẫn tới route quản trị của module đó và MUST kiểm tra quyền truy cập (`can(...)`) của tài khoản hiện tại.

#### Scenario: Hiển thị nút điều hướng nhanh khi quản trị viên có quyền truy cập module
- **WHEN** quản trị viên có quyền truy cập một module (ví dụ: `can('admin.products.index') === true`) xem thẻ hướng dẫn "Quản lý Sản phẩm"
- **THEN** thẻ hướng dẫn hiển thị nút "Mở trang quản lý" và khi nhấn vào sẽ điều hướng tới `route('admin.products.index')`

#### Scenario: Ẩn nút điều hướng nhanh khi quản trị viên không có quyền truy cập module
- **WHEN** quản trị viên bị giới hạn phân quyền đối với một module (ví dụ: `can('admin.roles.index') === false`) xem thẻ hướng dẫn "Phân quyền vai trò"
- **THEN** quản trị viên vẫn đọc được nội dung hướng dẫn nhưng nút "Mở trang quản lý" được ẩn hoặc hiển thị nhãn thông báo giới hạn quyền để tránh điều hướng vào trang lỗi `403 Forbidden`
