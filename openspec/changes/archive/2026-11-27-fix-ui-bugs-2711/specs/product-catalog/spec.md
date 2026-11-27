# Product Catalog Delta Specification

## MODIFIED Requirements

### Requirement: Bộ lọc danh mục 3 tầng 264px (Figma Node 979:30394)
Hệ thống SHALL cung cấp Sidebar lọc danh mục 3 tầng (`L0 -> L1 -> L2`) theo chuẩn Figma Node `979:30394` dựa trên đúng **4 danh mục gốc trong DB Bước 1** (`ProductFilter.vue`):
- Trên Desktop (`>= 1024px`), Sidebar nằm bên trái với độ rộng cố định `264px`, nền `#FAFAFA`, `sticky top-[96px]`; trên Mobile (`< 1024px`), hiển thị trong ngăn kéo off-canvas trượt từ trái sang kèm nút đóng `×`.
- Tiêu đề `DANH MỤC SẢN PHẨM` (`20px/32px` Bold `#00284B`), các hàng `L0` cao tối thiểu `51px` có viền dưới `1px solid #CDD5DF`, chữ `16px/26px` Bold `#364152` (active `#005EA4`) và icon chevron `20×20px` (hoặc checkbox `14×14px` ở bên **TRÁI** nhãn văn bản với `flex items-center justify-start gap-2` đối với nút `L0` không có danh mục con).
- Khối mở rộng `L1 + L2` dùng nền `#EEF2F6`, `padding: 24px`, `gap: 20px`:
  - **Mục `L1` không có danh mục con** (như Figma `979:30401`, `979:30430`) MUST đặt ô checkbox `14×14px` ở bên **TRÁI** nhãn văn bản (`14px/22px`) với `flex items-center justify-start gap-2`, KHÔNG đặt checkbox bên phải và KHÔNG dùng `justify-between` đẩy checkbox ra sát lề phải.
  - **Nhóm `L1` có danh mục con** (như Figma `979:30405`) MUST đặt ô checkbox `14×14px` (`tri-state`) ở bên **TRÁI** nhãn văn bản (`flex items-center justify-start gap-2`) bên trong nút chọn nhóm, và giữ nút chevron toggle `L2` (`979:30410`) ở bên phải hàng (`justify-between` trên hàng bọc ngoài).
  - **Danh sách `L2`** (`979:30411`) dùng `px-[20px] gap-[12px]`, mỗi hàng `L2` (`979:30412`) MUST đặt ô checkbox `14×14px` ở bên **TRÁI** nhãn văn bản với `flex items-center justify-start gap-2`, đồng nhất trục checkbox trên toàn bộ cây danh mục.
- Trạng thái checkbox checked dùng nền `#005EA4` bo góc `2px` kèm icon check trắng; unchecked dùng viền `1px solid #9AA4B2`.

#### Scenario: Phân biệt bố cục checkbox L1 không con và L2 trên Desktop
- **WHEN** mở nhánh `L0` đầu tiên trong Sidebar trên Desktop (`1440×900`) có cả mục `L1` không con, nhóm `L1` có con `L2` và các mục `L2` con (cũng như nhánh `L0` lá nếu có)
- **THEN** mọi cấp có checkbox (`L0` leaf, `L1` group, `L1` leaf, `L2` leaf) đều hiển thị ô checkbox `14×14px` nằm bên **TRÁI** nhãn văn bản cách đúng `8px` (`justify-start gap-2`), trong khi nút chevron mở rộng của nhóm `L1` vẫn nằm sát lề phải (`justify-between` trên hàng bọc ngoài).

#### Scenario: Lọc sản phẩm 3 tầng và đồng bộ URL query
- **WHEN** người dùng tick chọn một mục `L1`, `L2` hoặc nhóm `L1` (tri-state) trên Sidebar hoặc Mobile Drawer
- **THEN** danh sách sản phẩm cập nhật tức thì theo đúng nhánh con cháu đã chọn, URL query (`?category=`, `?subcategory=`, `?categories=`) được đồng bộ, và chip lọc tương ứng xuất hiện ở phía trên lưới sản phẩm.

## ADDED Requirements

### Requirement: Thanh tab Chi tiết sản phẩm (ProductDetailTabs) không cuộn dư thừa
Hệ thống SHALL hiển thị thanh điều hướng tab trên trang Chi tiết sản phẩm (`role="tablist"` trong `ProductDetailTabs.vue`) gọn gàng mà không phát sinh thanh cuộn ngang hoặc cuộn dọc thừa:
- Vùng `role="tablist"` MUST bỏ `overflow-x-auto no-scrollbar` và các nút tab MUST bỏ `-mb-[1px]` để tránh tạo tràn dọc/ngang do negative margin kết hợp `overflow-x-auto`.
- Trên viewport di động hẹp (`< 640px`), `role="tablist"` SHALL hỗ trợ xuống dòng an toàn (`flex-wrap`) khi tiêu đề tab vượt chiều ngang màn hình mà không làm tràn ngang khung trang.

#### Scenario: Thanh tab PDP không xuất hiện scroll ngang hoặc dọc trên Desktop và Mobile
- **WHEN** người dùng mở trang Chi tiết sản phẩm (`/san-pham/{slug}` hoặc `/en/products/{slug}`) trên Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** phần tử `role="tablist"` hiển thị đầy đủ 3 tab (`Mô tả tổng quan`, `Thông số & Cấu tạo`, `Bản vẽ & Tài liệu`) mà không có `scrollHeight > clientHeight` hay `scrollWidth > clientWidth` gây thanh cuộn thừa, và các tab có thể xuống dòng an toàn trên màn hình hẹp.
