# Báo Cáo Chuyên Sâu: Technical SEO (Trọng số 22%)

> **Điểm đánh giá**: **42 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Phương pháp**: Thu thập dữ liệu thực tế bằng Playwright Browser Engine, HTTP Request Header inspection, phân tích cấu trúc DNS và XML Sitemap.

---

## 1. Bằng chứng thực tế thu thập (Raw Evidence)

### 1.1. Nội dung thực tế file `https://hhmaker.vn/robots.txt`
```text
User-agent: *
Allow: /

Sitemap: https://funkystyle.vn/sitemap.xml
```

### 1.2. Phân tích `https://hhmaker.vn/sitemap.xml`
- **Tổng số URL được liệt kê**: 80 URLs.
- **URL trang chủ `https://hhmaker.vn/`**: **KHÔNG CÓ TRONG SITEMAP** (Bị bỏ sót hoàn toàn).
- **URLs trả về HTTP 500 Server Error**:
  - `https://hhmaker.vn/tin-tuc` (HTTP status: 500, Response: `{"error":"Something went wrong."}`)
  - `https://hhmaker.vn/en/posts` (HTTP status: 500, Response: `{"error":"Something went wrong."}`)
- **URLs nhạy cảm / hệ thống / utility bị submit nhầm vào sitemap**:
  - `https://hhmaker.vn/dang-ky`
  - `https://hhmaker.vn/dang-nhap`
  - `https://hhmaker.vn/dashboard`
  - `https://hhmaker.vn/dat-lai-mat-khau`
  - `https://hhmaker.vn/ho-so-ca-nhan`
  - `https://hhmaker.vn/quen-mat-khau`
  - `https://hhmaker.vn/error` (Trả về HTTP 200 soft-404 với `<meta name="robots" content="index">`)
  - `https://hhmaker.vn/robots.txt`
  - `https://hhmaker.vn/search`, `https://hhmaker.vn/search-v2`, `https://hhmaker.vn/tim-kiem`

### 1.3. HTTP Response Headers thực tế tại `https://hhmaker.vn/`
```http
HTTP/2 200 OK
server: nginx/1.18.0 (Ubuntu)
date: Mon, 09 Nov 2026 08:18:45 GMT
content-type: text/html; charset=UTF-8
vary: X-Inertia
cache-control: no-cache, private
x-content-type-options: nosniff
x-frame-options: SAMEORIGIN
content-encoding: gzip
```

### 1.4. Kiểm tra phân giải tên miền phụ `www.hhmaker.vn`
```text
Navigate to: https://www.hhmaker.vn/
Error: net::ERR_NAME_NOT_RESOLVED
Call log: DNS lookup failed for www.hhmaker.vn
```

---

## 2. Danh sách các vấn đề kỹ thuật phát hiện

### [CRITICAL] 1. Robots.txt Sitemap trỏ nhầm sang domain bên thứ ba (`funkystyle.vn`)
- **Mức độ nghiêm trọng**: Khẩn cấp (Critical)
- **Thực trạng**: File robots.txt khai báo `Sitemap: https://funkystyle.vn/sitemap.xml`. Đây là lỗi sao chép mã nguồn từ dự án thời trang FunkyStyle trước đây.
- **Hậu quả**: Khi Googlebot, Bingbot hay bất kỳ crawler nào đọc robots.txt để tìm sitemap, bot sẽ đọc sitemap của `funkystyle.vn` và không biết đến sự tồn tại của 80 trang trên `hhmaker.vn`.
- **Giải pháp**:
  ```diff
  User-agent: *
  Allow: /

  -Sitemap: https://funkystyle.vn/sitemap.xml
  +Sitemap: https://hhmaker.vn/sitemap.xml
  ```

---

### [CRITICAL] 2. Lỗi HTTP 500 Server Error trên các trang Tin tức trong Sitemap
- **Mức độ nghiêm trọng**: Khẩn cấp (Critical)
- **Thực trạng**: Cả 2 URL `/tin-tuc` và `/en/posts` đều trả về HTTP 500 với payload lỗi JSON backend `{"error":"Something went wrong."}`.
- **Hậu quả**: Googlebot thu thập URL được chỉ định trong Sitemap nhưng gặp lỗi 500 liên tục sẽ hạ thấp Crawl Budget và xếp hạng độ tin cậy của toàn bộ domain.
- **Giải pháp**:
  1. Kiểm tra log backend Laravel (`storage/logs/laravel.log`) để xử lý nguyên nhân lỗi (thiếu view, lỗi query model Post, hoặc thiếu bảng database).
  2. Đảm bảo trang tin tức trả về HTTP 200 và giao diện HTML chuẩn.

---

### [CRITICAL] 3. Trang chủ `https://hhmaker.vn/` vắng mặt trong `sitemap.xml`
- **Mức độ nghiêm trọng**: Khẩn cấp (Critical)
- **Thực trạng**: Script sinh sitemap tự động chỉ quét các model con mà quên đưa Root URL (`/`) vào danh sách.
- **Giải pháp**: Bổ sung node sau vào vị trí đầu tiên của `sitemap.xml`:
  ```xml
  <url>
      <loc>https://hhmaker.vn/</loc>
      <lastmod>2026-11-09T15:00:00+07:00</lastmod>
      <changefreq>daily</changefreq>
      <priority>1.0</priority>
  </url>
  ```

---

### [HIGH] 4. Sitemap chứa URL quản trị, xác thực và tìm kiếm nội bộ
- **Mức độ nghiêm trọng**: Cao (High)
- **Thực trạng**: Các trang như `/dang-ky`, `/dang-nhap`, `/dashboard`, `/search` được đưa vào Sitemap. Các trang này đều có canonical trỏ về trang chủ hoặc là trang private của người dùng.
- **Hậu quả**: Lãng phí crawl budget của công cụ tìm kiếm và tạo ra xung đột tín hiệu (URL nằm trong sitemap nhưng canonical lại trỏ về URL khác).
- **Giải pháp**:
  1. Lọc bỏ toàn bộ các route auth, user dashboard, utility và search khỏi generator sitemap.
  2. Khai báo thẻ `<meta name="robots" content="noindex, nofollow">` trên các trang auth và dashboard.

---

### [HIGH] 5. Thiếu cấu hình DNS cho subdomain `www.hhmaker.vn`
- **Mức độ nghiêm trọng**: Cao (High)
- **Thực trạng**: Người dùng hoặc đối tác gõ `www.hhmaker.vn` sẽ nhận lỗi trắng màn hình `ERR_NAME_NOT_RESOLVED`.
- **Giải pháp**:
  1. Thêm bản ghi DNS CNAME: `www` -> `hhmaker.vn` (hoặc bản ghi A cùng địa chỉ IP máy chủ).
  2. Cấu hình server block Nginx chuyển hướng 301 vĩnh viễn:
     ```nginx
     server {
         listen 80;
         listen 443 ssl;
         server_name www.hhmaker.vn;
         return 301 https://hhmaker.vn$request_uri;
     }
     ```

---

### [MEDIUM] 6. Thiếu các Security Headers thiết yếu & Lộ phiên bản Web Server
- **Mức độ nghiêm trọng**: Trung bình (Medium)
- **Thực trạng**:
  - Thiếu `Strict-Transport-Security` (HSTS).
  - Thiếu `Content-Security-Policy` (CSP).
  - Thiếu `Referrer-Policy`.
  - Header Server trả về `nginx/1.18.0 (Ubuntu)` tiết lộ chính xác phiên bản web server và hệ điều hành máy chủ.
- **Giải pháp**: Bổ sung vào Nginx config:
  ```nginx
  server_tokens off;
  add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
  add_header Referrer-Policy "strict-origin-when-cross-origin" always;
  add_header X-Content-Type-Options "nosniff" always;
  add_header X-Frame-Options "SAMEORIGIN" always;
  ```

---

### [MEDIUM] 7. Viewport hạn chế thu phóng của người dùng (`maximum-scale=1`)
- **Mức độ nghiêm trọng**: Trung bình (Medium)
- **Thực trạng**: Thẻ `<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">` ngăn cản người dùng dùng 2 ngón tay thu phóng trang trên thiết bị di động.
- **Hậu quả**: Vi phạm tiêu chuẩn khả năng tiếp cận WCAG 2.1 (Tiêu chí 1.4.4 Resize Text) và bị Google Lighthouse đánh tụt điểm Mobile Usability.
- **Giải pháp**: Đổi thành:
  ```html
  <meta name="viewport" content="width=device-width, initial-scale=1">
  ```
