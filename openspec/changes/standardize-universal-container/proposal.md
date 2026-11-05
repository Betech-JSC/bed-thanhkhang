# Proposal: Chuẩn Hóa Hệ Thống Universal Container Theo Chuẩn Figma Node 2261:5076

## Why

Hiện tại, giao diện toàn bộ các trang trên hệ thống HH-Maker (Home, Services, Products, Process, About, Contact, Policy, Footer) đang bị phân mảnh với ít nhất 5 quy cách container khác nhau:
1. `max-w-7xl` (1280px), `max-w-6xl` (1152px), `max-w-[1200px]`, `max-w-[1180px]`, `max-w-[1240px]`, `max-w-[1440px] xl:px-[144px]`, và Tailwind `container mx-auto`.
2. Trên Trang Chủ, mép trái nội dung nhảy ziczac giữa các section kế tiếp nhau (độ lệch từ 112px đến 176px trên viewport 1440px).
3. Trên màn hình Laptop chuẩn 1280px, chuyển đổi giữa trang Sản phẩm và Dịch vụ gây hiện tượng giật lệch layout tới 128px (Trang Sản phẩm bung 1216px trong khi Trang Dịch vụ co về 1088px).
4. Khái niệm `box-sizing: border-box` kết hợp với `max-w-[1152px] px-8` khiến vùng nội dung thực tế bị hụt chỉ còn 1088px (1152 - 64) thay vì đạt chuẩn 1152px như thiết kế Figma.

Việc chuẩn hóa một hệ thống Universal Container duy nhất là cấp thiết để mang lại trải nghiệm thị giác đồng nhất (Pixel-Perfect alignment), xóa bỏ hoàn toàn hiện tượng layout shift/giật khung hình khi chuyển trang, và khớp chính xác 100% với lưới thiết kế Figma Node 2261:5076.

## What Changes

1. **Chuẩn hóa Container Cơ Sở (Base Container)**:
   - Áp dụng công thức chuẩn: `w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8` (hoặc class tiện ích `.app-container`) cho 90% body content trên toàn bộ các trang và section:
     + Trang Chủ: `BrandStorySection`, `HighlightMetrics`, `ServicesSection`, `ProductsSection`, `FeaturedProductsCarousel`, `ContactSection`.
     + Trang Sản phẩm: `Product/Index.vue`, `Product/Show.vue`, `ProductCtaBanner.vue`.
     + Trang Dịch vụ: `Services/Index.vue`, `Services/Show.vue`.
     + Trang Quy trình & Giới thiệu & Liên hệ & Chính sách: `Process/Index.vue`, `About/Index.vue`, `Contact.vue`, `Policy.vue`.
     + Toàn bộ Chân trang: `Footer.vue`.
2. **Đối chiếu toán học Figma Node 2261:5076**:
   - Ở Viewport Desktop chuẩn 1440px:
     + Lề ngoài container: `(1440 - 1216) / 2 = 112px`.
     + Khoảng đệm `lg:px-8` (32px mỗi bên).
     + Tổng khoảng cách từ mép màn hình đến mép nội dung: `112px + 32px = 144px` (Khớp 100% lề 144px của Figma Node 2261:5076).
     + Độ rộng vùng nội dung thực tế: `1216px - 64px = 1152px` (Khớp 100% độ rộng nội dung 1152px của Figma).
   - Ở Viewport Laptop chuẩn 1280px:
     + Lề ngoài container: `(1280 - 1216) / 2 = 32px`.
     + Khoảng đệm `lg:px-8` (32px mỗi bên).
     + Tổng lề từ mép màn hình đến mép nội dung: `32px + 32px = 64px`.
     + Độ rộng vùng nội dung thực tế: Giữ nguyên `1152px`, hoàn toàn không bị co giật khi chuyển qua lại giữa các trang Sản phẩm và Dịch vụ.
   - Ở Viewport Tablet / Mobile (< 1024px):
     + Tự động thích ứng an toàn với `px-4` (16px) và `sm:px-6` (24px), đảm bảo không tràn ngang màn hình.
3. **Quy định vùng chứa ngoại lệ có chủ đích (Intentional Exceptions)**:
   - **Hero Banner Card**: Giữ `w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-0` (lề 80px trên 1440px: `(1440 - 1280) / 2 = 80px`) để duy trì tỷ lệ card hero bo góc dạng nổi.
   - **Header Bar**: Giữ `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20` (lề 80px) để bao quát thanh điều hướng trên toàn chiều rộng viewport.
4. **Định nghĩa Utility Class `.app-container`**:
   - Khai báo class `.app-container` tại `resources/Frontend/scss/app.scss` (@apply hoặc CSS thuần) và cập nhật cấu hình Tailwind để lập trình viên có thể tái sử dụng dễ dàng, nhất quán.

## Capabilities

### New Capabilities
- `universal-container`: Chuẩn hóa hệ thống Container Layout thống nhất cho toàn bộ các trang và thành phần giao diện theo chuẩn Figma Node 2261:5076, xóa bỏ tình trạng lệch mép nội dung ziczac và hiện tượng co giật layout giữa các trang.

### Modified Capabilities
*(Không có thay đổi yêu cầu cấp đặc tả đối với các capability hiện có; các capability khác chỉ kế thừa quy chuẩn container này)*

## Impact

- **Mã nguồn Frontend bị ảnh hưởng**:
  - `resources/Frontend/scss/app.scss` (Thêm class `.app-container` dùng chung).
  - `resources/Frontend/js/Components/Footer.vue`
  - `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`
  - `resources/Frontend/js/Pages/Home/Components/HighlightMetrics.vue`
  - `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`
  - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`
  - `resources/Frontend/js/Pages/Product/Index.vue`
  - `resources/Frontend/js/Pages/Product/Show.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`
  - `resources/Frontend/js/Pages/Services/Index.vue`
  - `resources/Frontend/js/Pages/Services/Show.vue`
  - `resources/Frontend/js/Pages/Process/Index.vue`
  - `resources/Frontend/js/Pages/About/Index.vue`
  - `resources/Frontend/js/Pages/Contact.vue`
  - `resources/Frontend/js/Pages/Policy.vue`
- **Không gây ảnh hưởng (Safe Scope)**:
  - Cấu trúc Backend, Database, Migration, API Controller không bị thay đổi.
  - Các logic nghiệp vụ (Inertia routing, Pinia store, i18n localization) được giữ nguyên 100%.
  - Không phá vỡ các responsive rules trên di động (Mobile/Tablet tiếp tục dùng `px-4`/`sm:px-6`).
