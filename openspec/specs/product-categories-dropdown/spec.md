# product-categories-dropdown Specification

## Purpose
Cung cấp tương tác mở rộng danh mục sản phẩm trực quan qua dropdown menu khi hover tab 'Sản phẩm' trên Desktop và accordion mở rộng trong Mobile Drawer, đồng bộ bộ lọc danh mục qua URL query parameter.

## Requirements

### Requirement: Hiển Thị Popover Dropdown Danh Mục Sản Phẩm Desktop Với Hover Bridge
Trên màn hình Desktop (viewport width >= 1024px), khi người dùng rê chuột (hover) vào tab "Sản phẩm" trên thanh điều hướng Header:
1. Hệ thống SHALL mở popover dropdown menu danh mục sản phẩm đặt ngay bên dưới tab với hiệu ứng chuyển động mượt mà.
2. Icon chevron-down (20px) tại tab "Sản phẩm" SHALL xoay 180 độ khi dropdown hiển thị và trở về góc 0 độ khi đóng dropdown.
3. Popover SHALL tuân thủ bảng màu Dark Tech của dự án: màu nền `#1A1F36`, đường viền `border-[#30374F]`, bo góc `rounded-xl`, bóng đổ `shadow-2xl`, hiệu ứng hover mục chọn `bg-[#404968]` và màu điểm nhấn active `#2970FF`.
4. Hệ thống SHALL thiết lập vùng đệm cầu nối chuột (Hover Bridge) liên tục giữa tab Header (chiều cao 47px) và popover bên dưới để ngăn chặn hiện tượng mất hover (flickering/premature close) khi con trỏ di chuyển qua lại.
5. Popover SHALL hiển thị đầy đủ các danh mục sản phẩm active trong Database (gồm 4 danh mục chủ lực: Trang trí không gian, Móc khoá & Phụ kiện, Trang trí & Đồ dùng, Gia công theo yêu cầu) kèm theo mục điều hướng "Xem tất cả sản phẩm".

#### Scenario: Rê chuột vào tab Sản phẩm trên Desktop
- **WHEN** người dùng rê chuột vào tab "Sản phẩm" trên thanh điều hướng Header ở độ phân giải 1440px
- **THEN** icon chevron xoay 180 độ và popover menu danh mục sản phẩm xuất hiện mượt mà ngay bên dưới với đầy đủ 4 danh mục và liên kết xem tất cả sản phẩm.

#### Scenario: Rê chuột từ tab Header xuống Popover qua Hover Bridge
- **WHEN** người dùng di chuyển con trỏ chuột từ tab "Sản phẩm" xuống các mục bên trong popover menu
- **THEN** popover tiếp tục duy trì trạng thái hiển thị ổn định, không bị chớp nháy hoặc biến mất đột ngột.

---

### Requirement: Điều Hướng Danh Mục Kèm Query Param ?category={slug}
Khi người dùng nhấp chọn một danh mục cụ thể từ menu dropdown (Desktop) hoặc accordion (Mobile):
1. Hệ thống SHALL điều hướng đến trang danh mục sản phẩm kèm tham số truy vấn `?category={slug}` tương ứng (ví dụ: `/san-pham?category=moc-khoa-phu-kien` ở tiếng Việt hoặc `/en/products?category=keychains-accessories` ở tiếng Anh).
2. Khi chọn mục "Xem tất cả sản phẩm" hoặc nhấp trực tiếp vào tiêu đề tab, hệ thống SHALL điều hướng đến URL gốc `/san-pham` (hoặc `/en/products`) không kèm lọc slug.
3. Trang danh mục sản phẩm (`Product/Index.vue`) SHALL tự động nhận diện tham số `category` từ URL khi khởi tạo hoặc khi URL thay đổi, đồng bộ hóa trạng thái bộ lọc trong `ProductFilter.vue`, và lọc danh sách sản phẩm hiển thị ngay lập tức mà không cần người dùng chọn lại thủ công.
4. Trạng thái danh mục đang chọn SHALL được highlight tương ứng trong bộ lọc bên trái của trang Catalog.

#### Scenario: Nhấp chọn một danh mục sản phẩm từ Header Dropdown
- **WHEN** người dùng click vào mục "Móc khoá & Phụ kiện" trong dropdown menu
- **THEN** hệ thống điều hướng đến `/san-pham?category=moc-khoa-phu-kien`, dropdown tự động đóng lại, và trang sản phẩm hiển thị các sản phẩm thuộc danh mục móc khoá.

#### Scenario: Truy cập trực tiếp qua URL có tham số category
- **WHEN** người dùng mở trình duyệt truy cập đường dẫn `/san-pham?category=gia-cong-theo-yeu-cau`
- **THEN** trang Catalog tự động áp dụng bộ lọc cho "Gia công theo yêu cầu" và danh mục này được highlight trong thanh bộ lọc bên trái.

---

### Requirement: Tích Hợp Accordion Mở Rộng Trong Mobile Drawer
Trên màn hình thiết bị di động (viewport width < 1024px), thanh điều hướng trong Mobile Drawer SHALL hỗ trợ mở rộng danh mục theo dạng Accordion:
1. Tab "Sản phẩm" trong Mobile Drawer SHALL hiển thị kèm nút toggle hoặc vùng chạm có biểu tượng chevron.
2. Khi người dùng nhấp vào nút mở rộng, danh sách các danh mục con SHALL trượt mở ra với hiệu ứng chuyển tiếp trơn tru (`transition-all duration-300`).
3. Biểu tượng chevron SHALL xoay 180 độ tương ứng khi Accordion đang mở và quay lại khi đóng.
4. Mỗi danh mục con trong Accordion SHALL có chiều cao tối thiểu 44px (đáp ứng tiêu chuẩn touch target di động), được thụt lề (indent `pl-8`) rõ ràng so với cấp menu cha.
5. Khi người dùng nhấp vào một danh mục con, hệ thống SHALL tự động đóng Mobile Drawer và điều hướng đến trang sản phẩm đã lọc theo danh mục đó.

#### Scenario: Mở rộng danh mục sản phẩm trong Mobile Drawer
- **WHEN** người dùng mở Mobile Drawer và nhấp vào nút mở rộng tại tab "Sản phẩm"
- **THEN** Accordion trượt xuống hiển thị danh sách 4 danh mục con cùng mục "Tất cả sản phẩm", icon chevron xoay 180 độ.

#### Scenario: Nhấp chọn danh mục con từ Mobile Accordion
- **WHEN** người dùng nhấp vào mục "Trang trí không gian" trong Accordion của Mobile Drawer
- **THEN** Mobile Drawer tự động đóng lại và hệ thống chuyển hướng đến `/san-pham?category=trang-tri-khong-gian`.

---

### Requirement: Hỗ Trợ Đa Ngôn Ngữ Song Ngữ Toàn Diện (VI & EN)
Mọi nội dung văn bản và đường dẫn liên kết của dropdown menu danh mục sản phẩm SHALL đồng bộ tuyệt đối theo ngôn ngữ người dùng đang chọn:
1. Khi locale là `vi`: Tên các danh mục hiển thị bằng tiếng Việt ("Trang trí không gian", "Móc khoá & Phụ kiện", "Trang trí & Đồ dùng", "Gia công theo yêu cầu"), liên kết xem tất cả là "Xem tất cả sản phẩm", và slug tham số là tiếng Việt.
2. Khi locale là `en`: Tên các danh mục hiển thị bằng tiếng Anh ("Space Decor", "Keychains & Accessories", "Decor & Lifestyle", "Custom Manufacturing"), liên kết xem tất cả là "View All Products", và slug tham số là tiếng Anh.
3. Khi người dùng chuyển đổi ngôn ngữ qua nút Language Switcher, dữ liệu danh mục trong dropdown và Accordion SHALL được cập nhật tức thời theo ngôn ngữ mới từ bộ nhớ cache tương ứng (`inertia_layout_product_categories_{locale}`).

#### Scenario: Xem dropdown khi ở giao diện tiếng Anh
- **WHEN** người dùng đang ở giao diện tiếng Anh (`/en`) và rê chuột vào tab "Products"
- **THEN** dropdown hiển thị các tên danh mục tiếng Anh gồm "Space Decor", "Keychains & Accessories", "Decor & Lifestyle", "Custom Manufacturing" cùng liên kết "View All Products".
