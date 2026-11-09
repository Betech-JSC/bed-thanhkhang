# Spec Delta: seo-phase-2

## Purpose

Cung cấp chuẩn đặc tả kỹ thuật cho Giai đoạn 2 (Phase 2: High-Impact Improvements) của HH Maker, tập trung nâng cao dữ liệu có cấu trúc Schema JSON-LD (LocalBusiness, Product & Offer, BreadcrumbList), tối ưu trải nghiệm Core Web Vitals (LCP qua WebP Hero Banner, CLS qua Image Dimensions và Lazy Loading), đồng bộ hóa đa ngôn ngữ Hreflang và thiết lập hàng rào bảo mật HTTP Security Headers.

## ADDED Requirements

### Requirement: Organization and LocalBusiness Structured Data
Hệ thống SHALL nhúng thẻ `<script type="application/ld+json">` chứa schema `LocalBusiness` (hoặc `Organization`) vào mã nguồn HTML trả về của toàn bộ các trang công khai trên website `https://hhmaker.vn`, định danh đầy đủ tên thương hiệu, số điện thoại hotline, địa chỉ cơ sở tại TP.HCM, email liên hệ, tọa độ địa lý, khung giờ làm việc và mức giá.

#### Scenario: Crawler inspects business identity schema on any public page
- **WHEN** bot tìm kiếm hoặc trình duyệt tải bất kỳ trang web nào (ví dụ: Trang chủ `/`, Giới thiệu `/gioi-thieu`, Liên hệ `/lien-he`)
- **THEN** phản hồi HTML chứa một khối JSON-LD hợp lệ với `@type` là `LocalBusiness`, `@id` chứa `https://hhmaker.vn/#organization`, `name` là "Công ty In ấn 3D HH Maker" (hoặc "HH Maker"), `telephone` chứa hotline "0906636425" (hoặc "+84906636425"), `address` xác định tại Quận Bình Tân - Thành phố Hồ Chí Minh, và `email` là "info@hhmaker.vn".

### Requirement: Product and Offer E-commerce Schema
Hệ thống SHALL tự động sinh dữ liệu có cấu trúc Schema `Product` kèm theo thuộc tính `offers` chuẩn Schema.org trên mọi trang chi tiết sản phẩm (`/san-pham/{slug}` hoặc `/en/products/{slug}`) để phục vụ Google Shopping và hiển thị Rich Snippets giá/tồn kho trên SERP.

#### Scenario: Crawler views a product detail page
- **WHEN** bot tìm kiếm hoặc người dùng truy cập trang chi tiết sản phẩm `/san-pham/{slug}`
- **THEN** mã nguồn phản hồi chứa khối JSON-LD `@type: "Product"` với các trường bắt buộc:
  - `name`: Tên sản phẩm chính xác
  - `image`: Danh sách URL hình ảnh sản phẩm
  - `description`: Đoạn mô tả tóm tắt sản phẩm
  - `sku`: Mã định danh SKU sản phẩm
  - `brand`: Đối tượng `@type: "Brand"` có tên "HH Maker"
  - `offers`: Đối tượng `@type: "Offer"` chứa `price` (số tiền hợp lệ), `priceCurrency: "VND"`, `availability: "https://schema.org/InStock"`, và `url` trỏ tới trang sản phẩm.

### Requirement: Hierarchical BreadcrumbList Schema
Hệ thống SHALL tự động sinh khối dữ liệu có cấu trúc `BreadcrumbList` trên tất cả các trang nội dung có cấu trúc phân cấp (bao gồm Danh mục sản phẩm, Chi tiết sản phẩm, Tin tức, Chi tiết bài viết, Giới thiệu, Chính sách, Dịch vụ).

#### Scenario: Inspection of breadcrumb schema on product detail page
- **WHEN** client truy cập trang chi tiết sản phẩm `/san-pham/{slug}`
- **THEN** phản hồi HTML chứa khối JSON-LD `@type: "BreadcrumbList"` với mảng `itemListElement` gồm ít nhất 3 cấp:
  - Cấp 1 (`position: 1`): "Trang chủ" (hoặc "Home") trỏ về `/`
  - Cấp 2 (`position: 2`): "Sản phẩm" (hoặc "Products") trỏ về `/san-pham`
  - Cấp 3 (`position: 3`): Tên sản phẩm hiện tại trỏ về URL chi tiết sản phẩm.

#### Scenario: Inspection of breadcrumb schema on news or about page
- **WHEN** client truy cập trang Giới thiệu `/gioi-thieu` hoặc Tin tức `/tin-tuc`
- **THEN** phản hồi HTML chứa khối JSON-LD `@type: "BreadcrumbList"` thể hiện đường dẫn điều hướng phân cấp chuẩn.

### Requirement: Internationalization Hreflang and Dynamic Language Attribute
Hệ thống SHALL tự động thiết lập thuộc tính `<html lang="...">` dựa trên locale thực tế của request (`vi` hoặc `en`), đồng thời xuất đầy đủ 3 thẻ `<link rel="alternate" hreflang="...">` gồm `vi`, `en` và `x-default` để chỉ dẫn công cụ tìm kiếm phục vụ đúng phiên bản ngôn ngữ cho người dùng toàn cầu.

#### Scenario: User visits Vietnamese version
- **WHEN** người dùng hoặc bot truy cập URL tiếng Việt (ví dụ `/` hoặc `/san-pham`)
- **THEN** thẻ html có thuộc tính `lang="vi"` và phần `<head>` chứa:
  - `<link rel="alternate" hreflang="vi" href="..." />`
  - `<link rel="alternate" hreflang="en" href="..." />`
  - `<link rel="alternate" hreflang="x-default" href="..." />`

#### Scenario: User visits English version
- **WHEN** người dùng hoặc bot truy cập URL tiếng Anh (ví dụ `/en` hoặc `/en/products` hoặc `/en/posts`)
- **THEN** thẻ html có thuộc tính `lang="en"`.

### Requirement: Image Optimization for Core Web Vitals (LCP and CLS)
Hệ thống SHALL tối ưu hóa hình ảnh để cải thiện triệt để chỉ số Core Web Vitals:
1. Cung cấp phiên bản định dạng WebP hiện đại cho Hero Banner trang chủ (`hero-banner.webp`) với dung lượng giảm tối thiểu 80% so với ảnh PNG gốc, sử dụng thẻ `<picture>` hoặc logic tải ưu tiên (`fetchpriority="high"`).
2. Toàn bộ hình ảnh trong danh sách sản phẩm (`ProductCard.vue`, `ProductsSection.vue`, `FeaturedProductsCarousel.vue`) MUST có thuộc tính `width`, `height` cố định, `loading="lazy"` và `decoding="async"` để loại bỏ hiện tượng giật gióng bố cục (CLS = 0).

#### Scenario: Inspection of Hero Banner image elements
- **WHEN** client tải trang chủ HH Maker
- **THEN** ảnh Hero Banner được ưu tiên tải phiên bản WebP `hero-banner.webp` kèm thuộc tính kích thước rõ ràng (`width="1279"` và `height="616"` hoặc tương đương theo tỷ lệ khung hình), không gây trễ LCP.

#### Scenario: Inspection of Product Card image tags
- **WHEN** client hiển thị danh sách sản phẩm tại trang chủ hoặc danh mục
- **THEN** các thẻ `<img>` của từng sản phẩm chứa thuộc tính `width`, `height`, `loading="lazy"` và `decoding="async"`.

### Requirement: HTTP Security Headers Enforcement
Hệ thống SHALL tự động đính kèm các HTTP Security Headers tiêu chuẩn vào mọi phản hồi HTTP để bảo vệ ứng dụng khỏi các cuộc tấn công Clickjacking, MIME-sniffing và rò rỉ referrer.

#### Scenario: Client sends HTTP request to application
- **WHEN** bất kỳ HTTP request nào được gửi tới ứng dụng
- **THEN** phản hồi HTTP trả về MUST chứa:
  - `X-Content-Type-Options: nosniff`
  - `X-Frame-Options: SAMEORIGIN`
  - `Referrer-Policy: strict-origin-when-cross-origin`
