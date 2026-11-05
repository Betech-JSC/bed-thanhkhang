# Proposal: Dropdown Menu Danh Mục Sản Phẩm Khi Hover Tab "Sản Phẩm" (Desktop) & Accordion (Mobile)

## Why
Hiện tại, thanh điều hướng Header hiển thị tab **"Sản phẩm"** kèm biểu tượng mũi tên chevron (`rotate-180` khi hover), nhưng danh sách menu con bên trong đang là mảng rỗng (`subMenu: []`), dẫn đến tình trạng "chevron rỗng" (phantom chevron). Khi người dùng hover trên Desktop, chevron xoay nhưng không có bất kỳ popover nào mở ra; trên Mobile Drawer, tab này chỉ là một liên kết tĩnh thông thường, không thể mở rộng để khám phá các dòng sản phẩm.

Việc bổ sung menu thả xuống hiển thị trực quan 4 danh mục sản phẩm chủ lực (Trang trí không gian, Móc khoá & Phụ kiện, Trang trí & Đồ dùng, Gia công theo yêu cầu) được lấy động từ Database sẽ:
1. Giải quyết triệt để lỗi trải nghiệm chevron rỗng.
2. Rút ngắn hành trình tìm kiếm cho khách hàng B2B / B2C bằng cách cho phép đi thẳng vào danh mục mong muốn từ bất kỳ trang nào.
3. Đồng bộ tương tác giữa Header và trang Danh mục sản phẩm (`/san-pham?category={slug}`).

## What Changes
1. **Backend - Chia sẻ danh mục sản phẩm toàn cục (Global Shared Data)**:
   - Cập nhật `app/Http/Middleware/HandleInertiaFrontendRequests.php` để chia sẻ `product_categories` thông qua Inertia props.
   - Dữ liệu được truy vấn từ `App\Models\Product\ProductCategory::query()->active()->sortByPosition()`, định dạng gồm `id`, `title`, `slug`, `url` và được cache 1 giờ (`Cache::remember('inertia_layout_product_categories_' . $locale, 3600, ...)`).
   - Tự động xóa cache khi danh mục cập nhật thông qua `ProductCategory::booted()`.

2. **Frontend - Header Desktop Hover Bridge & Popover**:
   - Tích hợp Dropdown Popover chuẩn thiết kế Dark Tech palette (`bg-[#1A1F36]`, `border-[#30374F]`, hover `bg-[#404968]`, accent `#2970FF`, text `#FAFAFA`).
   - Thiết lập vùng **Hover Bridge** (vùng đệm tàng hình giữa tab Header 47px và Popover bên dưới) đảm bảo con trỏ chuột di chuyển mượt mà không bị ngắt quãng sự kiện hover.
   - Hiển thị danh sách 4 danh mục sản phẩm từ backend kèm liên kết "Tất cả sản phẩm" dẫn về trang catalog tổng quan.

3. **Frontend - Header Mobile Drawer Accordion**:
   - Cung cấp nút toggle Accordion tại tab "Sản phẩm" trong Mobile Drawer.
   - Hỗ trợ mở rộng / thu gọn êm ái (`transition-all duration-300`) với biểu tượng chevron xoay 180°.
   - Đảm bảo touch target chuẩn di động (chiều cao tối thiểu 44px mỗi mục) theo Web Interface Guidelines.

4. **Frontend - Đồng bộ URL Query Param (`?category={slug}`)**:
   - Cập nhật `resources/Frontend/js/Pages/Product/Index.vue` để đọc `category` từ URL query parameters khi tải trang hoặc khi URL thay đổi (`$page.url` watcher).
   - Tự động kích hoạt bộ lọc tương ứng trong `ProductFilter.vue`.

5. **Đa ngôn ngữ (i18n)**:
   - Hỗ trợ trọn vẹn cả tiếng Việt (`vi`) và tiếng Anh (`en`) theo hệ thống hiện tại của dự án.

## Capabilities

### New Capabilities
- `product-categories-dropdown`: Đặc tả hành vi mở rộng dropdown menu danh mục sản phẩm khi hover trên Desktop (kèm hover bridge), chuyển đổi Accordion trên Mobile Drawer, truyền query param `?category={slug}` và hỗ trợ song ngữ VI/EN.

### Modified Capabilities
<!-- Không có - Tính năng mở rộng khả năng điều hướng của Header và lọc danh mục sản phẩm -->

## Impact
- **Backend**:
  - `app/Http/Middleware/HandleInertiaFrontendRequests.php`: Bổ sung shared prop `product_categories`.
  - `app/Models/Product/ProductCategory.php`: Bổ sung cơ chế làm mới cache layout shared data khi lưu/xóa category.
- **Frontend**:
  - `resources/Frontend/js/Components/Header.vue`: Cập nhật cấu trúc menu tab "Sản phẩm", dropdown popover với hover bridge cho Desktop, và accordion menu cho Mobile Drawer.
  - `resources/Frontend/js/Pages/Product/Index.vue`: Cập nhật `initSearchFromUrl` để phân tích query param `category`.
- **E2E Testing**:
  - `tests/e2e/product-categories-dropdown.spec.ts`: Kịch bản kiểm thử Playwright tự động trên cả Desktop (hover mở dropdown, click chuyển trang kèm query param) và Mobile (click toggle accordion trong drawer).
- **Phạm vi an toàn**:
  - Tuyệt đối không xóa file, không đổi schema database, không drop/truncate bảng và không phá vỡ logic các tab điều hướng khác.
