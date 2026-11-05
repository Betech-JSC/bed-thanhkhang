# Tasks: Triển Khai Dropdown Menu Danh Mục Sản Phẩm (Desktop & Mobile)

## 1. Backend: Cung Cấp & Cache Danh Mục Sản Phẩm Toàn Cục

- [x] 1.1 Cập nhật `app/Http/Middleware/HandleInertiaFrontendRequests.php` để truy vấn danh mục active từ `ProductCategory::query()->active()->sortByPosition()`, cache 3600s theo locale và chia sẻ qua Inertia shared prop `product_categories`. Xác minh bằng lệnh `php artisan tinker` xác nhận mảng dữ liệu trả về đủ 4 danh mục và đường dẫn tương ứng.
- [x] 1.2 Bổ sung cache forget cho khóa `inertia_layout_shared_data_*` trong phương thức `booted()` của `app/Models/Product/ProductCategory.php` khi model được lưu hoặc xóa. Xác minh bằng lệnh kiểm tra cú pháp PHP `php -l app/Models/Product/ProductCategory.php` không phát sinh lỗi.

## 2. Frontend: Tích Hợp Dropdown Desktop & Accordion Mobile Cho Header

- [x] 2.1 Cập nhật `resources/Frontend/js/Components/Header.vue` để liên kết prop `product_categories` từ Inertia vào mảng `subMenu` của tab "Sản phẩm", bổ sung mục điều hướng "Xem tất cả sản phẩm" dẫn về trang catalog gốc. Xác minh bằng kiểm tra logic render `menus` trong Header.
- [x] 2.2 Xây dựng Popover Dropdown Desktop theo thiết kế Dark Tech (`#1A1F36`, viền `#30374F`, hover `#404968`, accent `#2970FF`) và thiết lập cấu trúc Hover Bridge `pt-2` liên tục chống chập chờn khi rê chuột từ tab xuống menu con. Xác minh bằng tương tác chuột mượt mà trên trình duyệt.
- [x] 2.3 Triển khai Accordion mở rộng cho tab "Sản phẩm" trong Mobile Drawer của `Header.vue` với state `isProductAccordionOpen`, hiệu ứng trượt mở rộng/thu gọn và touch target tối thiểu 44px theo tiêu chuẩn thiết kế. Xác minh bằng cách mở drawer và toggle accordion trên giao diện mobile.

## 3. Frontend: Đồng Bộ Hóa URL Query Param Trang Catalog (Product/Index.vue)

- [x] 3.1 Cập nhật `resources/Frontend/js/Pages/Product/Index.vue` trong `initSearchFromUrl()` và watcher `$page.url` để phân tích tham số `?category={slug}` từ URL và gán vào `filters.category`. Xác minh bằng cách truy cập `/san-pham?category=moc-khoa-phu-kien` bộ lọc tự động kích hoạt danh mục tương ứng.
- [x] 3.2 Tối ưu logic lọc `filteredProducts` trong `Product/Index.vue` và highlight trạng thái đang chọn trong `ProductFilter.vue` cho cả tiếng Việt và tiếng Anh. Xác minh danh sách sản phẩm hiển thị đúng theo danh mục được chọn.

## 4. Xác Minh E2E & Playwright Visual QA

- [x] 4.1 Biên dịch gói frontend bằng lệnh `yarn frontend:build` và xác minh tiến trình kết thúc thành công với mã thoát exit code 0 không có lỗi.
- [x] 4.2 Viết kịch bản kiểm thử E2E Playwright tại `tests/e2e/product-categories-dropdown.spec.ts` bao phủ các kịch bản: Desktop hover mở dropdown, click danh mục chuyển trang kèm query param, Mobile drawer mở accordion và điều hướng đúng danh mục.
- [x] 4.3 Thực thi test Playwright trên cả hai khung nhìn Desktop (1440px) và Mobile (375px), xuất ảnh chụp màn hình kiểm chứng tại `tests/e2e/screenshots/` và xác nhận 100% test case PASS kèm bằng chứng terminal.
