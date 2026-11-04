# Tasks

## 1. Backend & CMS Slider Configuration

- [x] 1.1 Khai báo position `HOME_ANNOUNCEMENT_TICKER` trong `config/slider.php` với `banner => false` và xác nhận cấu hình được nạp chuẩn bằng lệnh `php artisan config:clear`.
- [x] 1.2 Cập nhật `app/Http/Controllers/Frontend/HomeController.php`: truy vấn `Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER')`, đóng gói vào mảng cache `home_page_data_{locale}` (TTL 3600s), truyền prop `announcements` xuống Inertia view, và xác nhận qua command line hoặc endpoint JSON response `curl -s http://127.0.0.1:8000 -H "Accept: application/json"`.
- [x] 1.3 Kiểm tra dữ liệu mẫu trong DB hoặc seeder/tinker: đảm bảo có ít nhất 2 mẩu tin thông báo song ngữ (VI/EN) với đầy đủ tiêu đề, phụ đề, link và trạng thái `ACTIVE` để phục vụ kiểm thử giao diện.

## 2. Frontend Vue 3 Component Implementation

- [x] 2.1 Tạo mới component `resources/Frontend/js/Pages/Home/Components/AnnouncementTicker.vue` nhận prop `announcements` (Array), cấu trúc HTML thẻ lật (split-top, split-bottom) và badge thể loại.
- [x] 2.2 Xây dựng hiệu ứng chuyển động Mechanical 3D Split-Flap bằng CSS 3D Transforms (`perspective`, `rotateX`, `backface-visibility: hidden`) và chu kỳ tự động xoay vòng 5.0 giây.
- [x] 2.3 Hiện thực cơ chế tương tác: tạm dừng khi hover chuột (`pause-on-hover`), hỗ trợ thao tác touch trên mobile, và fallback về fade đơn giản khi `@media (prefers-reduced-motion: reduce)`.
- [x] 2.4 Xử lý liên kết điều hướng thông minh: sử dụng `Link` của Inertia cho đường dẫn nội bộ và thẻ `<a>` kèm `rel="noopener noreferrer"` cho liên kết ngoài.

## 3. Homepage Integration & Layout Spacing Harmony

- [x] 3.1 Nhập và khai báo `AnnouncementTicker` trong `resources/Frontend/js/Pages/Home/Index.vue`, đặt tại vị trí trung gian giữa `<HeroSection />` và `<FeaturedProductsCarousel />`.
- [x] 3.2 Căn chỉnh khoảng đệm (spacing): đảm bảo chiều rộng chuẩn container `max-w-[1279px]`, margin dọc hài hòa (`my-4 md:my-6`), không gây xô lệch hoặc giật layout khi tải trang.
- [x] 3.3 Hiện thực Graceful Degradation: kiểm chứng khi `announcements` rỗng hoặc không có tin hợp lệ, component hoàn toàn không render (`v-if`) và bố cục trang chủ giữ khoảng cách tự nhiên.

## 4. Build Verification & Graphify AST Update

- [x] 4.1 Chạy lệnh kiểm tra build frontend `yarn frontend:build` (hoặc `npm run build`), xác nhận không có cảnh báo hoặc lỗi TypeScript/Vue template compilation.
- [x] 4.2 Cập nhật bản đồ đồ thị kiến trúc mã nguồn qua lệnh `graphify . --code-only` theo đúng quy định dự án tại `AGENTS.md`.

## 5. Automated Testing & Visual QA Verification

- [x] 5.1 Khởi động dev server và kiểm tra trực quan giao diện trang chủ trên trình duyệt: xác thực hiển thị thanh thông báo lật 3D ở cả 2 ngôn ngữ (Tiếng Việt `/` và Tiếng Anh `/en`).
- [x] 5.2 Kiểm tra đa màn hình (Desktop 1920x1080, Laptop 1280x800, Mobile 375x812): xác nhận không bị tràn viền (overflow-x), chữ không bị đè lên badge, và hoạt cảnh lật 60fps mượt mà.
- [x] 5.3 Chụp ảnh màn hình bằng chứng (evidence screenshot) xác nhận hiệu ứng và báo cáo kết quả hoàn tất theo tiêu chuẩn Evidence-Based 100%.
