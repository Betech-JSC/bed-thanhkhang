# Proposal: SEO Phase 2 High-Impact Improvements (CWV, Schema, Performance, Hreflang & Security)

## Why

Sau khi hoàn thành xuất sắc Phase 1 (khắc phục các lỗi nghiêm trọng về robots.txt, sitemap, 301 canonical, 500 error và bộ nhận diện thương hiệu OG Image), website HH Maker (`hhmaker.vn`) đã nâng điểm SEO Health Score từ **45/100** lên **65/100**, thông suốt toàn bộ luồng thu thập dữ liệu (crawling & indexation).

Tuy nhiên, theo kết quả phân tích chuyên sâu tại `hhmaker.vn-audit/FULL-AUDIT-REPORT.md`, `hhmaker.vn-audit/ACTION-PLAN.md` và `hhmaker.vn-audit/findings/schema.md`, website vẫn đang gặp phải các rào cản lớn ngăn cản việc bứt phá thứ hạng tìm kiếm và kích hoạt Rich Snippets trên Google:
1. **Thiếu hụt dữ liệu có cấu trúc (Schema & Structured Data)**: Điểm đánh giá chỉ đạt **25/100**. Toàn bộ website chỉ có schema `WebPage` sơ sài, thiếu hoàn toàn schema `Organization` / `LocalBusiness` để định danh thực thể doanh nghiệp tại TP.HCM, thiếu schema `Product` & `Offer` (giá, tồn kho InStock, SKU, hình ảnh) khiến sản phẩm không thể xuất hiện trong Google Shopping / Rich Results, và thiếu `BreadcrumbList` khiến URL trên SERP hiển thị dạng thô thay vì đường dẫn phân cấp rõ ràng.
2. **Hiệu năng Core Web Vitals (LCP & CLS)**: Ảnh Hero Banner trang chủ `hero-banner.png` có dung lượng lên đến **2.75 MB**, kéo dài thời gian tải LCP trên thiết bị di động. Ngoài ra, các thẻ hình ảnh trong danh sách sản phẩm thiếu thuộc tính `width`, `height` cố định và đang để `loading="eager"` khiến trình duyệt gặp hiện tượng giật cục bố cục (Cumulative Layout Shift - CLS).
3. **Đa ngôn ngữ & Hreflang**: Hệ thống có hỗ trợ 2 ngôn ngữ (Tiếng Việt và Tiếng Anh), nhưng chưa khai báo đầy đủ các thẻ `<link rel="alternate" hreflang="vi" ...>`, `<link rel="alternate" hreflang="en" ...>`, `<link rel="alternate" hreflang="x-default" ...>` và thuộc tính `<html lang="...">` cần được đồng bộ tự động theo locale thực tế của request.
4. **An toàn bảo mật & Tiêu chuẩn Web (Security Headers)**: Chưa thiết lập các HTTP Security Headers thiết yếu (`X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`) trong phản hồi của Laravel.

Triển khai Phase 2 là bước đi quyết định để nâng điểm SEO Health Score từ **65/100** lên **85+/100**, kích hoạt đầy đủ Google Rich Results, tối ưu vượt bậc trải nghiệm người dùng và củng cố vị thế thương hiệu in 3D hàng đầu tại Việt Nam.

## What Changes

Phase 2 tập trung vào 6 nhóm giải pháp kỹ thuật có tác động lớn (High-Impact Improvements):
- **Schema `Organization` & `LocalBusiness`**: Nhúng JSON-LD chuẩn vào layout gốc (`resources/Frontend/views/app.blade.php`), khai báo đầy đủ thông tin thực thể doanh nghiệp: tên "Công ty In ấn 3D HH Maker", hotline `0906636425`, địa chỉ Quận Bình Tân - TP.HCM, email `info@hhmaker.vn`, website `https://hhmaker.vn`, tọa độ địa lý, khung giờ mở cửa và priceRange.
- **Schema `Product` & `Offer`**: Tự động sinh JSON-LD `Product` chuẩn thương mại điện tử trên trang chi tiết sản phẩm (`ProductController::show` và `resources/Frontend/js/Pages/Product/Show.vue`) bao gồm: `name`, `image`, `description`, `sku`, `brand`, và khối `offers` (`price`, `priceCurrency: "VND"`, `availability: "https://schema.org/InStock"`, `url`, `priceValidUntil`).
- **Schema `BreadcrumbList`**: Tự động sinh JSON-LD `BreadcrumbList` phân cấp đa tầng cho toàn bộ các trang: Sản phẩm (`/san-pham`), Chi tiết sản phẩm (`/san-pham/{slug}`), Tin tức (`/tin-tuc`, `/tin-tuc/{slug}`), Giới thiệu (`/gioi-thieu`), Chính sách (`/chinh-sach`, `/chinh-sach/{slug}`), Dịch vụ (`/dich-vu`).
- **Hreflang & Ngôn ngữ động (`<html lang="...">`)**: Đồng bộ hóa thuộc tính `<html lang="{{ app()->getLocale() }}">` theo locale request thực tế (`vi` hoặc `en`), đồng thời tự động xuất các thẻ `<link rel="alternate" hreflang="vi" href="..." />`, `<link rel="alternate" hreflang="en" href="..." />` và `<link rel="alternate" hreflang="x-default" href="..." />`.
- **Tối ưu hình ảnh Hero Banner & Thẻ Ảnh (LCP & CLS)**:
  - Chuyển đổi và tạo phiên bản WebP chất lượng cao cho `hero-banner.png` sang `hero-banner.webp` (giảm dung lượng >90%), triển khai thẻ `<picture>` hỗ trợ WebP hiện đại kèm fallback PNG an toàn.
  - Bổ sung `width`, `height`, `loading="lazy"` và `decoding="async"` cho toàn bộ thẻ `<img>` trong danh sách sản phẩm (`ProductCard.vue`, `ProductsSection.vue`, `FeaturedProductsCarousel.vue`) để triệt tiêu hoàn toàn Layout Shift.
- **Security Headers Middleware**: Bổ sung Middleware `App\Http\Middleware\SecurityHeaders.php` thiết lập an toàn các headers: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`.

## Capabilities

### New Capabilities
- `seo-phase-2`: Bộ tính năng toàn diện nâng cao điểm SEO kỹ thuật Phase 2, bao gồm hệ thống dữ liệu có cấu trúc Schema JSON-LD đa tầng (LocalBusiness, Product, Offer, BreadcrumbList), tối ưu Core Web Vitals (WebP LCP, Image CLS), cơ chế đa ngôn ngữ Hreflang chuẩn quốc tế và lớp bảo vệ Security Headers.

### Modified Capabilities
*(Không có - các capability hiện có trong hệ thống không thay đổi contract)*

## Impact

- **Mã nguồn Backend**:
  - `resources/Frontend/views/app.blade.php`: Nhúng Schema LocalBusiness/Organization, thẻ Hreflang (`vi`, `en`, `x-default`), động hóa `<html lang="{{ app()->getLocale() }}">` và BreadcrumbList fallback thông minh.
  - `app/Http/Controllers/Frontend/ProductController.php`: Bổ sung sinh tự động Schema `Product`, `Offer` và `BreadcrumbList` trong dữ liệu SEO trả về view.
  - `app/Http/Middleware/SecurityHeaders.php`: Middleware mới đính kèm các HTTP security headers.
  - `app/Http/Kernel.php`: Đăng ký `SecurityHeaders` middleware vào web group.
  - `app/Http/Middleware/MetaSeo.php`: Đảm bảo đồng bộ `app()->setLocale(current_locale())` cho các request frontend.
- **Mã nguồn Frontend**:
  - `resources/Frontend/js/Pages/Product/Show.vue` & `Products/Show.vue`: Bổ sung Schema JSON-LD Product & BreadcrumbList vào Inertia Head để hỗ trợ SPA transitions.
  - `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`: Hỗ trợ định dạng WebP cho Hero Banner, thêm `width`, `height`, `fetchpriority="high"`.
  - `resources/Frontend/js/Pages/Product/Components/ProductCard.vue`: Bổ sung `width="273"`, `height="335"`, `loading="lazy"`, `decoding="async"`.
  - `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`: Bổ sung `width="264"`, `height="335"`, `loading="lazy"`, `decoding="async"`.
  - `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`: Bổ sung `width="264"`, `height="335"`, `loading="lazy"`, `decoding="async"`.
- **Tài nguyên tĩnh (Assets)**:
  - `public/assets/images/homepage/hero-banner.webp`: File WebP nén tối ưu dung lượng cao từ `hero-banner.png`.
- **Cơ sở dữ liệu (Database)**: TUYỆT ĐỐI KHÔNG SỬA SCHEMA, KHÔNG DROP/TRUNCATE BẢNG, KHÔNG MẤT DỮ LIỆU.
- **Kiểm thử tự động**:
  - `tests/Feature/SeoPhase2Test.php`: Kiểm tra toàn diện 100% các tiêu chí Phase 2.

## Safe Guard Boundary

Tuân thủ nghiêm ngặt quy tắc an toàn toàn cục của dự án:
1. **CẤM TỰ Ý XÓA FILE**: Tuyệt đối không xóa bất kỳ file hoặc thư mục nào trong dự án. Giữ nguyên toàn bộ file ảnh PNG gốc.
2. **CẤM TỰ Ý THAY ĐỔI DATABASE**: Không can thiệp DB production/staging, không drop/truncate table, không seed đè.
3. **CẤM PHÁ VỠ KIẾN TRÚC HIỆN CÓ**: Giữ nguyên kiến trúc BED CMS (Laravel + Inertia + Vue 3).
4. **100% BẰNG CHỨNG THẬT**: Mọi kết quả build và test phải có terminal raw output.
