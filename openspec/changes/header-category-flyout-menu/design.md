# Design: Multi-Level Flyout Menu (Dropdown 2 Cấp Bay Ngang Cho Menu Sản Phẩm Trên Header)

## Context
Xem `proposal.md` để nắm bối cảnh và lý do thay đổi.
Hiện tại, thanh điều hướng Header (`resources/Frontend/js/Components/Header.vue`) hiển thị một dropdown popover 1 cấp đơn giản. Dữ liệu danh mục sản phẩm từ `HandleInertiaFrontendRequests.php` (dòng 99-103) đang truy vấn toàn bộ categories mà không có scope `whereParent()`, dẫn tới việc cả danh mục cha và con bị làm phẳng ngang hàng.

Hệ thống cần nâng cấp kiến trúc hiển thị phân cấp 2 tầng:
- Backend cung cấp cây danh mục phân cấp gốc (`whereParent()->with(['children', 'nodes'])`).
- Frontend cung cấp Flyout Menu 2 cấp bay ngang trên Desktop kèm Hover Bridge và Accordion 2 cấp trên Mobile Drawer.

## Goals / Non-Goals

**Goals:**
- Tối ưu hóa truy vấn `ProductCategory` trong middleware để chỉ lấy danh mục cha gốc, các danh mục con được nạp qua relation và chuyển đổi đồng bộ bởi `transform()`.
- Xây dựng giao diện Flyout Menu 2 cấp ngang trên Desktop (`w-[280px]` cho cấp 1 và flyout container `left-full top-0 pl-1.5` cho cấp 2) giữ đúng Dark Tech UI (`#1A1F36]/95`, viền `#30374F`, bo góc `rounded-xl`, shadow đồng bộ).
- Thiết lập vùng đệm Hover Bridge loại trừ hoàn toàn hiện tượng chớp nháy hoặc rớt hover khi lia chuột chéo sang menu con.
- Xây dựng Accordion 2 cấp mượt mà trên Mobile Drawer (indent `pl-6`, toggle mũi tên quay trơn tru).
- Bổ sung lớp phòng vệ dữ liệu (Defensive Tree Grouping) ở frontend để tự động ghép danh mục con vào danh mục cha tương ứng nếu backend trả về danh mục có `parent_id > 0`.

**Non-Goals:**
- Không thay đổi cấu trúc bảng cơ sở dữ liệu `product_categories` hay `product_category_translations`.
- Không thay đổi logic routing hay giao diện các trang khác ngoài Header component.
- Không drop/truncate database.

## Decisions

### 1. Phân cấp dữ liệu tại Backend & Defensive Tree Grouping tại Frontend
- **Quyết định**: Tại `HandleInertiaFrontendRequests.php`, thêm `->whereParent()->with(['children', 'nodes'])`. Đồng thời tại `Header.vue`, trong computed `productCategories`, duyệt qua mảng danh mục: nếu có phần tử nào mang `parent_id > 0`, đưa nó vào mảng `children` của danh mục cha tương ứng.
- **Lý do**: Đảm bảo an toàn 2 lớp (defense-in-depth). Ngay cả khi dữ liệu từ cache cũ hoặc API trả về mảng phẳng, frontend vẫn tự động xây dựng cây cha - con chính xác 100%.

### 2. Giao diện Desktop Flyout 2 Cấp Bay Ngang & Hover Bridge
- **Quyết định**:
  + Thẻ cha cấp 1 được bọc trong container `relative group/sub` hoặc quản lý trạng thái `activeSubMenu`.
  + Khung flyout cấp 2 nằm ở vị trí `absolute left-full top-0 pl-1.5` với chiều rộng `min-w-[220px] w-[260px]`.
  + Vùng đệm `pl-1.5` kết hợp với padding bên trong tạo thành một "Hover Bridge" liên tục, giúp trỏ chuột di chuyển từ mục cha sang mục con mà không đi qua bất kỳ khoảng trống rỗng nào ngoài DOM hover.
- **Lý do**: Tránh hiện tượng drop menu biến mất khi chuột đi qua kẽ hở giữa 2 popover.

### 3. Accordion 2 Cấp Cho Mobile Drawer
- **Quyết định**:
  + Thêm biến trạng thái `expandedParentCategory: null` (hoặc đối tượng toggle id) để kiểm soát việc mở/đóng danh mục cha trong mobile drawer.
  + Danh mục con được thụt lề `pl-6`, hiển thị rõ ràng với touch target tối thiểu 44px và chuyển động mượt mà.
- **Lý do**: Màn hình điện thoại có chiều ngang hẹp (~375px), bố cục dạng Accordion cuộn dọc là giải pháp tối ưu cho trải nghiệm chạm (touch UX), thay vì popover bay ngang.

## Risks / Trade-offs

- **[Risk] Hover flickering khi lia chuột nhanh trên Desktop**:
  - *Mitigation*: Sử dụng cấu trúc lồng nhau trực tiếp trong DOM với `group/sub` hoặc `mouseenter/mouseleave` debounce nhẹ, kết hợp padding đệm `pl-1.5` để không có khoảng trống vật lý nào làm mất hover.
- **[Risk] Cache layout cũ còn lưu dữ liệu danh mục dạng phẳng**:
  - *Mitigation*: Gọi `ProductCategory::clearCategoryCaches()` hoặc `Cache::forget('inertia_layout_shared_data_' . $locale)` khi khởi động hoặc sau khi cập nhật code.
