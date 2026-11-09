# Tasks: Multi-Level Flyout Menu (Dropdown 2 Cấp Bay Ngang Cho Menu Sản Phẩm Trên Header)

## 1. Khởi Tạo & Chuẩn Bị OpenSpec

- [x] 1.1 Khởi tạo change OpenSpec `header-category-flyout-menu` gồm `proposal.md`, `specs/header-category-flyout-menu/spec.md`, `design.md`, `tasks.md`, xác minh qua `openspec validate header-category-flyout-menu`.

## 2. Triển Khai Backend (HandleInertiaFrontendRequests.php)

- [x] 2.1 Cập nhật truy vấn `ProductCategory` trong `app/Http/Middleware/HandleInertiaFrontendRequests.php` (dòng 99-103) bổ sung `->whereParent()->with(['children', 'nodes'])`, đảm bảo chỉ lấy danh mục cha gốc và nạp sẵn mảng con.
- [x] 2.2 Xóa cache shared layout data (`inertia_layout_shared_data_{locale}`) và kiểm tra qua php script xác nhận cấu trúc danh mục cha - con chính xác.

## 3. Triển Khai Frontend Header Flyout Menu (Header.vue)

- [x] 3.1 Cập nhật computed `productCategories` & `menus` trong `Header.vue` với logic phòng vệ (defensive tree grouping) gom danh mục có `parent_id > 0` vào đúng danh mục cha.
- [x] 3.2 Xây dựng giao diện Desktop Flyout Menu 2 cấp bay ngang: Danh mục cha có con hiển thị icon chevron-right `>`; hover mở flyout cấp 2 bên phải (`left-full top-0 pl-1.5`) với bảng màu Dark Tech đồng bộ và vùng đệm Hover Bridge chống mất hover.
- [x] 3.3 Xây dựng giao diện Mobile Drawer Accordion 2 cấp: Bấm vào danh mục cha có con để xổ/thu danh mục con bên dưới (thụt lề `pl-6`, icon chevron xoay mượt mà).
- [x] 3.4 Chạy `npm run build` xác nhận Vue/Vite biên dịch thành công 100% không có lỗi cú pháp.

## 4. Kiểm Thử Trực Quan E2E Playwright (Evidence-Based)

- [x] 4.1 Tạo test E2E Playwright `tests/e2e/header-category-flyout.spec.ts` kiểm thử tương tác hover menu Sản phẩm -> hover mục cha ra menu cấp 2 trên Desktop (1440px) và Accordion trên Mobile (375px).
- [x] 4.2 Chạy test Playwright, xuất ảnh chụp màn hình bằng chứng thực tế tại `tests/e2e/screenshots/`.
- [x] 4.3 Hoàn thiện báo cáo nghiệm thu chi tiết gửi về caller agent kèm raw terminal output và đường dẫn ảnh chụp.
