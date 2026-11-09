# Kế Hoạch Hành Động SEO Toàn Diện (Action Plan) - HH Maker (`hhmaker.vn`)

> **Mục tiêu**: Nâng cấp SEO Health Score từ **45/100** lên **85+/100**, khắc phục 100% lỗi nghiêm trọng ảnh hưởng đến chỉ số indexation và mở rộng lưu lượng tìm kiếm tự nhiên bền vững.

---

## 🗺️ Lộ Trình 4 Giai Đoạn (4-Phase Implementation Roadmap)

```
[Phase 1: Tuần 1] ───► [Phase 2: Tuần 2-3] ───► [Phase 3: Tháng 2] ───► [Phase 4: Liên tục]
Critical Fixes         High-Impact CWV & Schema   Content & Entity GEO    Monitoring & Growth
(Sửa lỗi sitemap,      (Schema Product, Nén ảnh, (Blog in 3D, FAQ,        (GSC, Field Data,
 500 error, canonical)  Hreflang, Security)       llms.txt, Case studies)  Drift Tracking)
```

---

## 🚨 GIAI ĐOẠN 1: CRITICAL FIXES (Tuần 1 - Sửa lỗi khẩn cấp)

| STT | Nhiệm vụ | Vị trí / File can thiệp | Mức độ | Thời gian dự kiến |
| :---: | :--- | :--- | :---: | :---: |
| 1.1 | **Sửa Sitemap trong `robots.txt`**: Đổi `funkystyle.vn/sitemap.xml` thành `https://hhmaker.vn/sitemap.xml`. | `public/robots.txt` | **Critical** | 5 phút |
| 1.2 | **Khắc phục lỗi HTTP 500 tại `/tin-tuc` và `/en/posts`**: Kiểm tra controller `PostController`, model và view; sửa lỗi ngoại lệ backend. | `app/Http/Controllers/Frontend/PostController.php` | **Critical** | 2-4 giờ |
| 1.3 | **Bổ sung trang chủ vào `sitemap.xml`**: Thêm `<loc>https://hhmaker.vn/</loc>` với priority 1.0 vào sitemap generator. | `app/Http/Controllers/SitemapController.php` (hoặc view sitemap) | **Critical** | 30 phút |
| 1.4 | **Xử lý Triplicate URL Giới thiệu**: Chọn `/gioi-thieu` làm URL chuẩn, cấu hình 301 Redirect cho `/about` và `/ve-chung-toi`. | `routes/web.php` hoặc Nginx | **Critical** | 30 phút |
| 1.5 | **Lọc sạch Sitemap XML**: Loại bỏ các route `/dang-ky`, `/dang-nhap`, `/dashboard`, `/search`, `/error`, `/robots.txt` khỏi sitemap. | `app/Http/Controllers/SitemapController.php` | **High** | 1 giờ |
| 1.6 | **Cấu hình DNS và Redirect cho `www.hhmaker.vn`**: Tạo bản ghi CNAME cho subdomain `www` và thêm block redirect 301 trong Nginx. | DNS Provider / Nginx config | **High** | 30 phút |
| 1.7 | **Sửa trang 404 & Set Noindex cho `/error`**: Đổi tiêu đề "Betech" thành "HH Maker", cập nhật đúng hotline `0906636425` và gán `<meta name="robots" content="noindex">`. | `resources/views/errors/404.blade.php` | **High** | 30 phút |

---

## ⚡ GIAI ĐOẠN 2: HIGH-IMPACT IMPROVEMENTS (Tuần 2-3 - Tối ưu tác động cao)

| STT | Nhiệm vụ | Vị trí / File can thiệp | Mức độ | Thời gian dự kiến |
| :---: | :--- | :--- | :---: | :---: |
| 2.1 | **Triển khai Schema `Product` & `Offer`**: Chèn JSON-LD schema chi tiết (sku, price, availability, image) vào trang chi tiết sản phẩm. | `resources/js/Pages/Frontend/Products/Show.vue` | **High** | 1 ngày |
| 2.2 | **Triển khai Schema `Organization` / `LocalBusiness`**: Nhúng thông tin doanh nghiệp, hotline, địa chỉ Bình Tân vào root layout. | `resources/views/app.blade.php` hoặc Root Layout | **High** | 2 giờ |
| 2.3 | **Triển khai Schema `BreadcrumbList`**: Tự động sinh breadcrumb theo cấu trúc cây thư mục. | `resources/js/Components/Breadcrumb.vue` | **High** | 4 giờ |
| 2.4 | **Nén và chuyển đổi ảnh Hero Banner**: Nén `hero-banner.png` (2.75 MB) sang WebP (<200 KB) và hỗ trợ responsive srcset. | `public/assets/images/homepage/` | **High** | 1 giờ |
| 2.5 | **Tối ưu file SVG Logo**: Chạy SVGO nén `figma-header-logo.svg` từ 668 KB xuống dưới 15 KB. | `public/assets/images/header/` | **High** | 30 phút |
| 2.6 | **Khắc phục thuộc tính width/height & Lazy Loading**: Bổ sung width/height và `loading="lazy"` cho toàn bộ danh sách sản phẩm. | `resources/js/Pages/Frontend/Home/` & `Products/` | **High** | 1 ngày |
| 2.7 | **Sửa Duplicate Meta Tags trong Inertia**: Thêm thuộc tính `head-key` vào các thẻ `<meta>` trong component `<Head>`. | `resources/js/Pages/**/*.vue` | **Medium** | 4 giờ |
| 2.8 | **Cấu hình Đa ngôn ngữ Hreflang**: Sửa `<html lang="en">` cho trang `/en` và khai báo các thẻ `<link rel="alternate" hreflang="...">`. | `resources/views/app.blade.php` | **High** | 2 giờ |
| 2.9 | **Bổ sung Security Headers Nginx**: Kích hoạt HSTS, Referrer-Policy, tắt `server_tokens`. | `/etc/nginx/sites-available/` | **Medium** | 30 phút |

---

## 📚 GIAI ĐOẠN 3: CONTENT & AUTHORITY (Tháng 2 - Xây dựng nội dung & Thực thể)

| STT | Nhiệm vụ | Chi tiết thực hiện | Mức độ |
| :---: | :--- | :--- | :---: |
| 3.1 | **Xây dựng Hub Kiến Thức In 3D (Blog)** | Viết 10 bài viết Pillar & Cluster chuyên sâu: cẩm nang in 3D FDM vs SLA, hướng dẫn chuẩn bị file 3D, báo giá gia công theo gram, vật liệu Carbon Fiber... | **High** |
| 3.2 | **Nâng cấp trang Giới thiệu (About Us)** | Mở rộng bài viết đạt 800+ từ, thêm hình ảnh thực tế xưởng máy in 3D, dàn máy, khu vực hoàn thiện sản phẩm và giấy phép kinh doanh. | **High** |
| 3.3 | **Bổ sung Tab Thông số kỹ thuật cho Sản phẩm** | Điền đầy đủ kích thước mm, trọng lượng, thời gian in, infill, vật liệu cho toàn bộ 16+ sản phẩm. | **Medium** |
| 3.4 | **Tạo file `public/llms.txt`** | Tạo file tóm tắt năng lực in 3D, vật liệu và liên hệ chuẩn Markdown để các mô hình AI (ChatGPT, Perplexity) dễ trích dẫn. | **High** |
| 3.5 | **Thêm khối FAQ có Schema `FAQPage`** | Thêm 5–7 câu hỏi thường gặp về thời gian gia công, cách đặt hàng, vận chuyển và bảo hành trên trang Dịch vụ và Sản phẩm. | **Medium** |
| 3.6 | **Chuẩn hóa Social Links & Aria-label** | Cập nhật link chính xác trang Facebook, Instagram, TikTok của HH Maker và thêm thuộc tính `aria-label`. | **Low** |

---

## 📈 GIAI ĐOẠN 4: MONITORING & ITERATION (Liên tục hàng tháng)

1. **Google Search Console**:
   - Theo dõi trạng thái nộp Sitemap mới (`https://hhmaker.vn/sitemap.xml`).
   - Kiểm tra báo cáo Lập chỉ mục trang (Page Indexing): đảm bảo không còn lỗi 500 hay URL 404 bị gửi trong sitemap.
   - Kiểm tra Rich Results Report: xác nhận Google đã nhận diện hợp lệ các Schema `Product`, `Breadcrumbs`, `LocalBusiness`.
2. **Core Web Vitals Field Data (CrUX)**:
   - Theo dõi chỉ số LCP thực tế trên Mobile giảm xuống dưới 2.0s sau khi nén ảnh Hero Banner và Logo SVG.
   - Đảm bảo điểm CLS giữ ở mức an toàn (< 0.05).
3. **Kiểm toán SEO định kỳ (SEO Drift)**:
   - Chạy lại bộ script kiểm toán trước và sau mỗi đợt deploy tính năng lớn để ngăn ngừa tái phát lỗi kỹ thuật.
