# Proposal: Multi-Level Flyout Menu (Dropdown 2 Cấp Bay Ngang Cho Menu Sản Phẩm Trên Header)

## Why
Hiện tại trên thanh điều hướng Header, menu "Sản phẩm" chỉ hiển thị danh mục cấp 1 ở dạng popover dọc phẳng đơn giản. Khi người dùng muốn xem hoặc duyệt các danh mục con (như "Móc khoá cơ khí", "Móc khoá thể thao", "Ly", "Lọ cắm hoa để bàn", "Đèn", "Mô hình Resin", "Linh kiện Carbon"), họ buộc phải bấm vào danh mục lớn rồi mới tiếp tục điều hướng ở trang catalog.

Việc bổ sung **Multi-Level Flyout Menu** (Dropdown 2 cấp bay ngang theo chiều ngang trên Desktop và Accordion phân cấp trên Mobile Drawer) giải quyết triệt để vấn đề này:
1. Giúp người dùng khám phá nhanh chóng các danh mục chi tiết ngay từ thanh điều hướng chính mà không phải tải lại trang.
2. Nâng cao trải nghiệm khám phá sản phẩm (Product Discovery), giữ đúng bảng màu Dark Tech hiện đại của thương hiệu HH Maker.
3. Tạo cầu nối hover (Hover Bridge) mượt mà, chống hiện tượng chớp nháy (flicker) hay mất hover khi lia chuột sang khung menu cấp 2.

## What Changes
1. **Backend (`app/Http/Middleware/HandleInertiaFrontendRequests.php`)**:
   - Cập nhật truy vấn `ProductCategory` tại dòng 99-103: bổ sung `->whereParent()->with(['children', 'nodes'])` để chỉ lấy danh mục gốc (cha) làm cấp 1, các danh mục con được eager-load trong quan hệ `children` và tự động chuyển đổi thông qua `transform()`.
   - Làm mới và xóa cache layout shared data (`inertia_layout_shared_data_{locale}`).
2. **Frontend (`resources/Frontend/js/Components/Header.vue`)**:
   - Xử lý dữ liệu đa cấp trong computed `productCategories` & `menus`:
     + Đảm bảo mỗi mục cha trong `productSubMenu` chứa danh sách con `children` / `subcategories`.
     + Phòng vệ (defensive tree grouping): Nếu Backend trả về danh mục có `parent_id > 0`, tự động gom vào đúng danh mục cha tương ứng.
   - **Giao diện Desktop (Flyout Menu 2 cấp bay ngang)**:
     + Menu cấp 1 (hộp dọc `w-[280px]`) hiển thị các danh mục cha + link "Xem tất cả sản phẩm ->".
     + Danh mục cha có con hiển thị icon chevron-right (`>`).
     + Khi hover vào danh mục cha có con, hiển thị menu cấp 2 bay sang phải (`left-full top-0 pl-1.5`), màu nền `#1A1F36]/95`, viền `#30374F`, bo góc `rounded-xl`, shadow đồng bộ.
     + Tạo vùng đệm Hover Bridge (padding `pl-1.5` và lớp liên kết) để lia chuột từ menu cha sang menu con không bị mất trạng thái hover.
   - **Giao diện Mobile Drawer (Accordion 2 cấp)**:
     + Với menu "Sản phẩm", hỗ trợ Accordion 2 cấp: Bấm vào mục cha có con để xổ/thu danh mục con bên dưới (thụt lề `pl-6`, có icon toggle mũi tên xoay trơn tru).
3. **Kiểm thử & Bằng chứng**:
   - Biên dịch `npm run build` thành công 100%.
   - E2E Playwright test kiểm tra tương tác hover flyout cấp 2 trên Desktop và tương tác accordion trên Mobile Drawer, chụp ảnh màn hình làm bằng chứng.

## Capabilities

### New Capabilities
- `header-category-flyout-menu`: Cung cấp menu đa cấp dạng Flyout 2 cấp bay ngang trên Header Desktop và Accordion 2 cấp trên Mobile Drawer cho danh mục sản phẩm, đồng bộ dữ liệu đa ngữ và bộ lọc URL.

### Modified Capabilities
<!-- Không có -->

## Impact
- `app/Http/Middleware/HandleInertiaFrontendRequests.php`: Bổ sung `whereParent()->with(['children', 'nodes'])`.
- `resources/Frontend/js/Components/Header.vue`: Nâng cấp giao diện Desktop Flyout Menu 2 cấp bay ngang và Mobile Drawer Accordion 2 cấp.
- `tests/e2e/header-category-flyout.spec.ts`: Kịch bản kiểm thử E2E tương tác trực quan bằng Playwright.
- Tuyệt đối không xóa file, không drop/truncate database.
