# Design: SEO Phase 2 Technical Architecture & Implementation Details

## Overview

Tài liệu thiết kế kỹ thuật cho Giai đoạn 2 (Phase 2: High-Impact Improvements) của HH Maker. Mục tiêu là triển khai đồng bộ các giải pháp tối ưu SEO kỹ thuật có tầm ảnh hưởng lớn nhất nhằm đưa SEO Health Score từ 65 lên 85+/100:
1. **Hệ thống dữ liệu có cấu trúc (Schema & Structured Data)**: Định danh thực thể doanh nghiệp với `LocalBusiness`/`Organization`, thương mại điện tử với `Product` & `Offer`, và cấu trúc phân cấp với `BreadcrumbList`.
2. **Tối ưu Core Web Vitals (LCP & CLS)**: Chuyển đổi định dạng WebP cho Hero Banner, bổ sung kích thước tĩnh `width`/`height` và kích hoạt `loading="lazy"` cho toàn bộ thẻ ảnh danh sách sản phẩm.
3. **Cơ chế Đa ngôn ngữ Hreflang**: Đồng bộ hóa thuộc tính `<html lang="...">` động theo `app()->getLocale()`, tự động xuất 3 thẻ `<link rel="alternate" hreflang="...">` (`vi`, `en`, `x-default`).
4. **Hạ tầng An ninh Web (Security Headers)**: Triển khai Laravel Middleware tự động gắn các HTTP Security Headers chuẩn.

---

## 1. Kiến Trúc Dữ Liệu Có Cấu Trúc (Structured Data Architecture)

### 1.1. Schema `LocalBusiness` / `Organization`
Được nhúng trực tiếp vào view gốc `resources/Frontend/views/app.blade.php`. Cấu trúc JSON-LD được chuẩn hóa:
```json
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://hhmaker.vn/#organization",
  "name": "Công ty In ấn 3D HH Maker",
  "alternateName": "HH Maker 3D Printing",
  "url": "https://hhmaker.vn",
  "logo": "https://hhmaker.vn/assets/images/header/figma-header-logo.svg",
  "image": "https://hhmaker.vn/og-image.jpg?v=20261109",
  "description": "Xưởng thiết kế và gia công in 3D công nghệ FDM, SLA Resin 8K, SLS chuyên nghiệp tại TP.HCM.",
  "telephone": "+84906636425",
  "email": "info@hhmaker.vn",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "Số 65 đường số 9, KDC Hương Lộ 5, Phường An Lạc",
    "addressLocality": "Quận Bình Tân",
    "addressRegion": "Thành phố Hồ Chí Minh",
    "postalCode": "700000",
    "addressCountry": "VN"
  },
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 10.74123,
    "longitude": 106.60845
  },
  "openingHoursSpecification": {
    "@type": "OpeningHoursSpecification",
    "dayOfWeek": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"],
    "opens": "08:30",
    "closes": "18:00"
  },
  "priceRange": "$$",
  "sameAs": [
    "https://www.facebook.com/hhmaker"
  ]
}
```

### 1.2. Schema `Product` & `Offer`
Được xây dựng tự động từ `ProductController::show` và nhúng vào cả 2 luồng:
1. **Server-Side Render (Blade)**: Chèn vào `$seo['seo_schemas']` truyền qua `withViewData(['seo' => $seo])` để Googlebot và công cụ thu thập thông tin nhận diện tức thì trong lần tải đầu tiên.
2. **Client-Side SPA Transition (Inertia Head)**: Trong `resources/Frontend/js/Pages/Product/Show.vue` (và `Products/Show.vue`), component `<Head>` chứa `<component :is="'script'" type="application/ld+json">` để khi người dùng điều hướng SPA qua lại giữa các sản phẩm, Schema JSON-LD trong DOM tự động cập nhật tương ứng.

Cấu trúc:
```json
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "...",
  "image": ["..."],
  "description": "...",
  "sku": "HHM-PRD-...",
  "brand": {
    "@type": "Brand",
    "name": "HH Maker"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://hhmaker.vn/san-pham/...",
    "priceCurrency": "VND",
    "price": "...",
    "priceValidUntil": "2028-12-31",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "HH Maker"
    }
  }
}
```

### 1.3. Schema `BreadcrumbList` Tự Động Toàn Hệ Thống
Triển khai theo mô hình 2 tầng:
- **Tầng 1 (Controller-specific)**: `ProductController::show` tạo BreadcrumbList 3 cấp: Trang chủ -> Sản phẩm -> Tên sản phẩm.
- **Tầng 2 (Smart Blade Auto-Fallback)**: Trong `resources/Frontend/views/app.blade.php`, nếu trang chưa có schema `BreadcrumbList` từ controller, layout sẽ tự động phân tích URI hiện tại và sinh ra BreadcrumbList chính xác:
  - Trang `/san-pham` / `/en/products`: Trang chủ -> Danh mục Sản phẩm.
  - Trang `/tin-tuc` / `/en/posts`: Trang chủ -> Tin tức & Kiến thức.
  - Trang `/gioi-thieu` / `/en/about-us`: Trang chủ -> Giới thiệu.
  - Trang `/chinh-sach` / `/en/policies`: Trang chủ -> Chính sách & Quy định.
  - Trang `/dich-vu` / `/en/services`: Trang chủ -> Dịch vụ in 3D.
  - Trang `/quy-trinh`: Trang chủ -> Quy trình gia công 3D.

---

## 2. Tối Ưu Hóa Core Web Vitals (CWV: LCP & CLS)

### 2.1. Nén Hero Banner sang WebP (LCP)
- File gốc: `public/assets/images/homepage/hero-banner.png` (2.75 MB).
- Sử dụng PHP GD thư viện đồ họa tích hợp sẵn (`imagecreatefrompng` + `imagewebp` với chất lượng 85) để sinh ra `public/assets/images/homepage/hero-banner.webp` (< 200 KB, giảm ~93% dung lượng).
- Áp dụng nguyên tắc an toàn: **TUYỆT ĐỐI GIỮ NGUYÊN FILE PNG GỐC**.
- Cập nhật `HeroSection.vue`:
  - Dùng thẻ `<picture>` bọc `<source srcset="/assets/images/homepage/hero-banner.webp" type="image/webp">` và thẻ `<img>` dự phòng PNG.
  - Bổ sung `width="1279"`, `height="616"`, `loading="eager"`, `decoding="async"`, và `fetchpriority="high"`.

### 2.2. Khắc Phục CLS cho Danh Sách Sản Phẩm
Trong các component:
- `resources/Frontend/js/Pages/Product/Components/ProductCard.vue`
- `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`
- `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`

Thực hiện:
1. Đặt kích thước cố định cho thẻ `<img>`: `width="273" height="335"` (hoặc `width="264" height="335"` tương ứng container).
2. Chuyển đổi từ `loading="eager"` sang `loading="lazy"`.
3. Chuyển đổi từ `decoding="sync"` sang `decoding="async"`.
Điều này cho phép trình duyệt cấp phát không gian bố cục ngay lập tức trước khi tải ảnh, loại bỏ 100% hiện tượng dịch chuyển giao diện (CLS = 0).

---

## 3. Đa Ngôn Ngữ Hreflang & Dynamic Language Attribute

### 3.1. Đồng bộ `app()->getLocale()`
Trong `app/Http/Middleware/MetaSeo.php`, gọi:
```php
app()->setLocale(current_locale());
```
Đảm bảo khi truy cập prefix `/en/*`, hàm `app()->getLocale()` luôn trả về `'en'`, và khi ở route tiếng Việt mặc định trả về `'vi'`.

### 3.2. Thuộc tính `<html lang="...">`
Trong `resources/Frontend/views/app.blade.php`:
```blade
<html lang="{{ app()->getLocale() }}">
```
Hiển thị `<html lang="vi">` hoặc `<html lang="en">` chuẩn theo chuẩn W3C và Google Search.

### 3.3. Thẻ Hreflang
Trong `<head>` của `app.blade.php`:
Tự động sinh bộ 3 thẻ alternate hreflang:
```blade
<link rel="alternate" hreflang="vi" href="{{ $hreflangVi }}" />
<link rel="alternate" hreflang="en" href="{{ $hreflangEn }}" />
<link rel="alternate" hreflang="x-default" href="{{ $hreflangDefault }}" />
```
Thuật toán ánh xạ URL đối ứng giữa 2 ngôn ngữ:
- `/` <-> `/en`
- `/san-pham` <-> `/en/products`
- `/gioi-thieu` <-> `/en/about-us`
- `/tin-tuc` <-> `/en/posts`
- `/lien-he` <-> `/en/contact`
- `/chinh-sach` <-> `/en/policies`
- `/dich-vu` <-> `/en/services`
- `/san-pham/{slug}` <-> `/en/products/{slug}`
Và `x-default` trỏ về phiên bản tiếng Việt chính.

---

## 4. HTTP Security Headers Middleware

Tạo Middleware mới: `App\Http\Middleware\SecurityHeaders.php`:
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        return $response;
    }
}
```
Đăng ký vào `$middleware` trong `app/Http/Kernel.php` để bảo vệ toàn bộ request.

---

## 5. Chiến Lược Kiểm Thử Tự Động (Verification Strategy)

Tạo test suite Feature chuyên biệt: `tests/Feature/SeoPhase2Test.php`:
1. `test_organization_and_local_business_schema_exists`: Kiểm tra schema LocalBusiness tồn tại trong HTML, có tên HH Maker, hotline 0906636425, địa chỉ Bình Tân, email info@hhmaker.vn.
2. `test_product_and_offer_schema_exists_on_product_show`: Kiểm tra route sản phẩm chứa JSON-LD Product & Offer với price, VND, InStock, SKU.
3. `test_breadcrumb_list_schema_exists`: Kiểm tra BreadcrumbList xuất hiện hợp lệ trên trang sản phẩm, tin tức, giới thiệu.
4. `test_dynamic_html_lang_and_hreflang_tags`: Kiểm tra `<html lang="vi">` cho tiếng Việt, `<html lang="en">` cho tiếng Anh, và các thẻ `<link rel="alternate" hreflang="...">`.
5. `test_security_headers_are_present`: Kiểm tra các header `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`.
6. `test_hero_banner_webp_exists_and_optimized`: Kiểm tra file `hero-banner.webp` tồn tại trên đĩa và có dung lượng tối ưu dưới 300KB.
