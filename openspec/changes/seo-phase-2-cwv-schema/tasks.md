# Tasks: SEO Phase 2 High-Impact Improvements (CWV, Schema, Performance, Hreflang & Security)

## 1. Schema Organization & LocalBusiness
- [x] 1.1 Khai báo cấu trúc Schema JSON-LD `LocalBusiness` / `Organization` vào `resources/Frontend/views/app.blade.php` định danh đầy đủ: Tên doanh nghiệp "Công ty In ấn 3D HH Maker", hotline `0906636425`, địa chỉ Bình Tân - TP.HCM, email `info@hhmaker.vn`, website `https://hhmaker.vn`, giờ mở cửa và priceRange.
- [x] 1.2 Đảm bảo schema được kết xuất hợp lệ ở mọi route công khai trong thẻ `<script type="application/ld+json">`.

## 2. Schema Product & Offer
- [x] 2.1 Cập nhật `app/Http/Controllers/Frontend/ProductController.php`: tự động sinh mảng schema `Product` và `Offer` (name, image, description, sku, offers: price, priceCurrency: "VND", availability: InStock, url, brand) vào `$seo['seo_schemas']`.
- [x] 2.2 Cập nhật `resources/Frontend/js/Pages/Product/Show.vue` (và `Products/Show.vue`): bổ sung Schema JSON-LD Product & BreadcrumbList vào component `<Head>` để đồng bộ khi chuyển trang bằng Inertia SPA.

## 3. Schema BreadcrumbList
- [x] 3.1 Bổ sung schema `BreadcrumbList` 3 cấp cho sản phẩm trong `ProductController::show` và `Product/Show.vue`.
- [x] 3.2 Bổ sung cơ chế tự động sinh BreadcrumbList schema trong `resources/Frontend/views/app.blade.php` cho các trang phân cấp: Danh mục sản phẩm (`/san-pham`), Tin tức (`/tin-tuc`), Giới thiệu (`/gioi-thieu`), Chính sách (`/chinh-sach`), Dịch vụ (`/dich-vu`).

## 4. Hreflang & Dynamic HTML Lang
- [x] 4.1 Cập nhật `app/Http/Middleware/MetaSeo.php`: gọi `app()->setLocale(current_locale())` để đồng bộ locale của framework với prefix đường dẫn.
- [x] 4.2 Cập nhật `resources/Frontend/views/app.blade.php`: sửa thành `<html lang="{{ app()->getLocale() }}">` và xuất 3 thẻ `<link rel="alternate" hreflang="vi" ...>`, `<link rel="alternate" hreflang="en" ...>`, `<link rel="alternate" hreflang="x-default" ...>`.

## 5. Image Optimization (Core Web Vitals - LCP & CLS)
- [x] 5.1 Nén chuyển đổi `public/assets/images/homepage/hero-banner.png` thành `hero-banner.webp` chất lượng cao, giảm >80% dung lượng mà không xóa file PNG gốc.
- [x] 5.2 Cập nhật `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`: hỗ trợ định dạng WebP với thẻ `<picture>`, bổ sung `width="1279"`, `height="616"`, `loading="eager"`, `decoding="async"`, `fetchpriority="high"`.
- [x] 5.3 Bổ sung `width`, `height`, `loading="lazy"`, `decoding="async"` cho các thẻ `<img>` trong danh sách sản phẩm tại `ProductCard.vue`, `ProductsSection.vue`, `FeaturedProductsCarousel.vue`.

## 6. Security Headers Middleware
- [x] 6.1 Tạo Middleware `App\Http\Middleware\SecurityHeaders.php` đính kèm các HTTP header: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy`.
- [x] 6.2 Đăng ký `SecurityHeaders` vào `$middleware` trong `app/Http/Kernel.php`.

## 7. Automated Testing & Verification Gate
- [x] 7.1 Tạo Feature Test `tests/Feature/SeoPhase2Test.php` kiểm tra toàn bộ 6 nhóm tiêu chí kỹ thuật: LocalBusiness schema, Product/Offer schema, BreadcrumbList schema, dynamic lang & hreflang, security headers, webp asset optimization.
- [x] 7.2 Chạy kiểm thử tự động `php artisan test --filter=SeoPhase2Test` và kiểm tra 100% test passed.
- [x] 7.3 Chạy kiểm thử toàn bộ dự án `php artisan test` đảm bảo không phát sinh hồi quy.
- [x] 7.4 Cập nhật checklist `tasks.md` thành `[x]`.
