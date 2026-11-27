# Spec Delta: Giao diện Responsive & Trải nghiệm Mobile cho Màn hình Quản lý tệp

## Purpose

Đặc tả hành vi hiển thị responsive, thanh công cụ thích ứng, ngăn kéo cây thư mục dưới `1024px`, chế độ nhúng `embed=true`, tương tác cảm ứng và vùng an toàn khung nhìn cho màn hình Quản lý tệp trên cả Desktop, Tablet và Mobile.

## ADDED Requirements

### Requirement: Định vị khung Quản lý tệp Responsive và Bảo toàn Điều hướng Mobile
Khung gốc của màn hình Quản lý tệp SHALL điều chỉnh vị trí và thứ tự lớp (`z-index`) theo độ rộng khung nhìn và ngữ cảnh sử dụng (trang độc lập `/admin/files`, modal chọn tệp `selectable=true`, và chế độ nhúng `embed=true`):
- Ở độ rộng `< 768px`, khung Quản lý tệp MUST trải rộng toàn chiều ngang (`left: 0; right: 0`) thay vì bị đẩy lệch `16rem` (`256px`) sang phải.
- Ở độ rộng `>= 768px` khi không ở chế độ nhúng `embed=true`, khung Quản lý tệp MUST chừa đúng khoảng trống cho thanh điều hướng bên trái (`md:left-[var(--sidebar-width)]`).
- Khi chạy dưới dạng trang độc lập (`/admin/files`), khung Quản lý tệp trên màn hình `< 768px` MUST không che khuất thanh header mobile và nút mở sidebar của Admin layout, đồng thời cho phép mở/đóng menu sidebar Admin bình thường.
- Khi chạy dưới dạng bộ chọn tệp (`selectable=true`) hoặc chế độ nhúng (`embed=true`), khung Quản lý tệp MUST phủ toàn màn hình (`top: 0`, `z-50`) và giữ nút quay lại/đóng có vùng chạm tối thiểu `40×40px` khi `selectable=true`.

#### Scenario: Mở trang độc lập /admin/files trên Mobile (375×667)
- **WHEN** quản trị viên truy cập `/admin/files` ở khung nhìn `375×667`
- **THEN** khung Quản lý tệp hiển thị vừa khít chiều ngang `375px` (`scrollWidth <= 375px`), thanh header Admin mobile và nút hamburger mở sidebar ở phía trên vẫn hiển thị và nhấn mở được drawer điều hướng Admin.

#### Scenario: Mở Quản lý tệp dạng Picker từ form sản phẩm/tài liệu trên Mobile (375×667)
- **WHEN** quản trị viên nhấn chọn ảnh hoặc tệp từ form để mở Quản lý tệp (`selectable=true`) ở khung nhìn `375×667`
- **THEN** Quản lý tệp phủ kín khung nhìn (`left: 0; top: 0; right: 0; bottom: 0`) và nút đóng/quay lại trên thanh tiêu đề đóng được bộ chọn mà không làm tràn ngang màn hình.

---

### Requirement: Thanh Công cụ & Tìm kiếm Responsive không vỡ chữ, tuân thủ Quiet UI
Thanh công cụ phía trên của Quản lý tệp SHALL hiển thị gọn gàng, không gãy chữ dọc hay tràn ngang trên tất cả kích thước màn hình (`375px`, `390px`, `768px`, `1024px`, `1440px`), tuân thủ triết lý *Quiet & High-Trust UI*:
- Tiêu đề `Quản lý tệp` MUST giữ trên một dòng (`whitespace-nowrap shrink-0`), không bị bẻ thành nhiều dòng hẹp.
- Ô tìm kiếm MUST co giãn linh hoạt (`w-full` trên mobile) thay vì cố định bề rộng `400px`.
- Các nút hành động chính (`Chọn tệp`, `Tạo thư mục`, nút mở cây thư mục dưới `1024px`) MUST giữ nhãn trên một dòng (`whitespace-nowrap`) và không tràn khỏi khung nhìn.
- Các nút thao tác ngữ cảnh (`Đổi tên thư mục này`, `Đổi tên`, `Sao chép`, `Cắt`, `Dán`) MUST được bố trí gọn gàng (nhóm hoặc menu thao tác trên màn hình nhỏ/vừa) với trạng thái `disabled` và bộ đếm clipboard chính xác, không chèn icon trang trí thừa hay emoji.

#### Scenario: Kiểm tra thanh công cụ trên Mobile (390×844) và Tablet (768×1024)
- **WHEN** quản trị viên xem màn hình Quản lý tệp tại `390×844` và `768×1024`
- **THEN** tiêu đề `Quản lý tệp` và toàn bộ nhãn nút hiển thị trên một dòng hoàn chỉnh (không gãy dọc kiểu `Quản / lý / tệp` hoặc `TẠO THƯ / MỤC`), ô tìm kiếm không tràn quá mép phải, và không xuất hiện thanh cuộn ngang trên container chính.

---

### Requirement: Ngăn kéo Cây Thư mục dưới 1024px và Thanh điều khiển Chế độ Nhúng (`embed=true`)
Màn hình Quản lý tệp SHALL bảo đảm người dùng vẫn truy cập được cây thư mục và chức năng tải tệp lên trên màn hình dưới `1024px` (`< lg`), bao gồm cả chế độ nhúng `embed=true`:
- Trên màn hình `< 1024px`, MUST có nút mở ngăn kéo (drawer) cây thư mục; khi nhấn vào nút này, ngăn kéo trượt ra hiển thị cây thư mục đầy đủ và tự động đóng sau khi người dùng chọn một thư mục.
- Trên màn hình `>= 1024px`, cột cây thư mục bên trái MUST hiển thị cố định như bố cục Desktop chuẩn.
- Trong chế độ nhúng `embed=true` (iframe trình soạn thảo Rich Text), trên màn hình `< 1024px` MUST hiển thị thanh điều khiển tối giản chứa nút mở ngăn kéo cây thư mục và nút `Chọn tệp` (upload), bảo đảm không bị mất nút tải tệp lên khi `<aside>` ẩn.

#### Scenario: Chuyển thư mục và tải tệp trong chế độ embed=true trên Mobile (375×667)
- **WHEN** quản trị viên mở `/admin/files?embed=true&select-multiple=true` ở khung nhìn `375×667`
- **THEN** nút `Chọn tệp` (upload) và nút mở cây thư mục đều hiển thị khả dụng; khi nhấn mở cây thư mục, drawer liệt kê đầy đủ thư mục và tự đóng sau khi chọn một thư mục đích.

---

### Requirement: Lưới Tệp, Tương tác Cảm ứng, Thanh Chọn Nhiều và Hộp thoại Thân thiện Mobile
Khu vực danh sách tệp, Breadcrumb, thanh chọn nhiều và các hộp thoại trong Quản lý tệp SHALL hỗ trợ đầy đủ thao tác cảm ứng trên thiết bị không có hover hoặc chuột phải:
- Lưới hiển thị thư mục và tệp MUST dùng 2 cột trên `< 640px`, 3 cột từ `640px`, 4 cột từ `1024px`, và 6 cột từ `1536px` (`grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6`).
- Breadcrumb MUST tự động xuống dòng khi đường dẫn dài (`flex-wrap`) và cung cấp nút quay lên thư mục cha khi đang ở thư mục con (`currentPath !== '/'`).
- Mỗi thẻ thư mục/tệp MUST có nút menu hành động (`...`) kích thước chạm thoải mái (`>= 36×36px`) để mở danh sách thao tác (`Mở`, `Dán vào đây`, `Đổi tên`, `Sao chép`, `Cắt`, `Xóa`) mà không phụ thuộc chuột phải (`@contextmenu`), đồng thời thẻ thư mục MUST cung cấp cách mở trực tiếp bằng một chạm (nút `Mở` hoặc chạm mở trực tiếp ở chế độ chèn đơn `embed`) bên cạnh `@dblclick` trên Desktop.
- Nút xem trước tệp ở tab mới và nút xóa trên thẻ thumbnail MUST hiển thị khả dụng và đủ vùng chạm trên màn hình `< 1024px` (không bị ẩn hoàn toàn bởi `hover`), đồng thời tương thích ngược với các form đang sử dụng thumbnail.
- Thanh chọn nhiều ở cạnh dưới khi `selectedFiles.length > 0` MUST có khoảng đệm `env(safe-area-inset-bottom)` và vùng cuộn chính MUST có padding đáy đủ lớn để không che khuất hàng tệp cuối hoặc phân trang.
- Các hộp thoại `Tạo thư mục` và `Đổi tên` MUST hiển thị tiêu đề đã bản địa hóa (`Tạo thư mục` / `Đổi tên`), kích thước tối đa `95vw` trên màn hình nhỏ và có lớp nền modal che phủ đúng chuẩn.

#### Scenario: Thao tác mở menu ngữ cảnh, mở thư mục và xóa trên Mobile cảm ứng
- **WHEN** quản trị viên mở Quản lý tệp trên thiết bị/khung nhìn Mobile (`375×667`), chạm vào nút menu hành động trên một thẻ tệp hoặc nút mở thư mục trên thẻ thư mục
- **THEN** menu ngữ cảnh (`Đổi tên`, `Sao chép`, `Cắt`, `Xóa`) mở ra ngay lập tức mà không cần chuột phải, thao tác mở thư mục điều hướng được vào thư mục con, nút xem/xóa trên thumbnail nhìn thấy và chạm được, và khi chọn nhiều tệp thì thanh hành động đáy không che mất hàng tệp cuối cùng.
