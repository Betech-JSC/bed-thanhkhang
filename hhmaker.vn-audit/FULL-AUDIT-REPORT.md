# BÁO CÁO KIỂM TOÁN SEO TOÀN DIỆN (FULL WEBSITE SEO AUDIT)
# MỤC TIÊU: HTTPS://HHMAKER.VN/

> **Cơ quan kiểm định**: QA Verifier & Senior SEO Technical Auditor  
> **Thời gian thực hiện**: Ngày 09 Tháng 11 Năm 2026  
> **Phiên bản chuẩn**: Google Search Essentials (2026), Core Web Vitals Standard, Schema.org v26, GEO Framework  
> **Phương pháp kiểm toán**: 100% Thu thập bằng chứng thực tế tại trình duyệt Playwright Engine (Desktop & Mobile), trích xuất DOM, Network Timing API, Header Inspection.

---

## 1. TỔNG QUAN KẾT QUẢ & ĐIỂM SỐ SEO HEALTH SCORE

### 1.1. Bảng Điểm Tổng Hợp 7 Trụ Cột SEO

| Trụ cột đánh giá | Trọng số | Điểm thành phần (0 - 100) | Điểm quy đổi | Trạng thái kỹ thuật |
| :--- | :---: | :---: | :---: | :--- |
| **1. Technical SEO** | 22% | **42 / 100** | 9.24 | 🔴 **Báo động đỏ** (Sitemap lỗi ngoại lai, 500 error, thiếu trang chủ) |
| **2. Content Quality & E-E-A-T** | 23% | **48 / 100** | 11.04 | 🟠 **Yếu** (Mục tin tức sập 500, nội dung giới thiệu mỏng, thiếu case study) |
| **3. On-Page SEO** | 20% | **52 / 100** | 10.40 | 🟠 **Trung bình** (Triplicate URL Giới thiệu, trùng lặp thẻ meta, thiếu hreflang) |
| **4. Schema & Structured Data** | 10% | **25 / 100** | 2.50 | 🔴 **Báo động đỏ** (Thiếu 100% schema Product, LocalBusiness, Breadcrumb) |
| **5. Performance & Core Web Vitals** | 10% | **58 / 100** | 5.80 | 🟡 **Khá** (TTFB rất nhanh nhưng Hero Image 2.75 MB & Logo 668 KB gây nghẽn LCP) |
| **6. AI Search Readiness (GEO)** | 10% | **35 / 100** | 3.50 | 🔴 **Báo động đỏ** (Thiếu llms.txt, thiếu FAQ có cấu trúc, không có chỉ dẫn AI bot) |
| **7. Images & Visual Assets** | 5% | **55 / 100** | 2.75 | 🟡 **Trung bình** (100% có Alt tốt, nhưng 100% thiếu width/height và định dạng WebP) |
| **TỔNG ĐIỂM SEO HEALTH SCORE** | **100%** | **45.23 / 100** | **45 / 100** | **XẾP LOẠI: YẾU (GRADE: POOR / C-)** |

---

### 1.2. Nhận Diện Loại Hình Doanh Nghiệp (Business Type Detection)
- **Mô hình hoạt động**: Hybrid B2C & B2B E-commerce kết hợp Dịch vụ Sản xuất & Gia công In 3D theo yêu cầu (Custom Manufacturing Service).
- **Thị trường mục tiêu**: Khách hàng cá nhân (mô hình decor, figure resin, phụ kiện cyberpunk) và khách hàng doanh nghiệp/kỹ thuật (khung robot carbon fiber, đồ gá, bánh răng cơ khí, tạo mẫu nhanh).
- **Phạm vi địa lý**: Xưởng sản xuất tại TP. Hồ Chí Minh, giao hàng toàn quốc.

---

### 1.3. TOP 5 LỖI KỸ THUẬT NGHIÊM TRỌNG NHẤT (CRITICAL ISSUES)

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ 1. Robots.txt trỏ Sitemap sang domain funkystyle.vn (Bot tìm kiếm bị điều hướng sai)   │
│ 2. Mục Tin tức (/tin-tuc & /en/posts) lỗi HTTP 500 nhưng vẫn submit trong Sitemap       │
│ 3. Trang chủ https://hhmaker.vn/ bị bỏ quên, KHÔNG CÓ TRONG sitemap.xml               │
│ 4. Triplicate URL Cannibalization: 3 URL Giới thiệu cùng tồn tại (/about, /gioi-thieu) │
│ 5. Thiếu 100% Schema chuyên sâu: Không có Schema Product, Offer, LocalBusiness          │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

1. **Robots.txt Sitemap trỏ sai domain (`funkystyle.vn`)**: File `robots.txt` khai báo `Sitemap: https://funkystyle.vn/sitemap.xml`. Toàn bộ bot tìm kiếm khi truy cập website sẽ đọc sitemap của một website thời trang khác, hoàn toàn không thu thập được sitemap thực tế của HH Maker!
2. **Chuyên mục Tin tức trả về lỗi HTTP 500 Internal Server Error**: Cả 2 URL `/tin-tuc` và `/en/posts` đều bị crash với mã lỗi 500 và trả về `{"error":"Something went wrong."}`, trong khi cả 2 URL này vẫn được submit trong `sitemap.xml`.
3. **Trang chủ `https://hhmaker.vn/` vắng mặt trong Sitemap**: Trong toàn bộ 80 URLs được khai báo trong `sitemap.xml`, không hề có URL trang chủ.
4. **Trùng lặp nội dung 3 URL Giới thiệu kèm Canonical tự trỏ**: Ba URL `/about`, `/gioi-thieu`, và `/ve-chung-toi` đều trả về HTTP 200 OK với cùng nội dung và tự trỏ canonical về chính mình, gây ra tình trạng tự ăn thịt từ khóa trầm trọng.
5. **Trang sản phẩm thiếu hoàn toàn Schema `Product` và `Offer`**: Không một sản phẩm nào có Schema Product. Google Merchant và Google Search không thể đọc được giá bán, hình ảnh và tình trạng còn hàng để hiển thị Rich Snippets.

---

### 1.4. TOP 5 THẮNG LỢI NHANH (QUICK WINS - SỬA XONG TRONG 2 GIỜ)

1. **Sửa 1 dòng trong `public/robots.txt`**: Cập nhật lại đường dẫn sitemap chính xác thành `Sitemap: https://hhmaker.vn/sitemap.xml`.
2. **Thêm Trang chủ và loại bỏ URL auth/search trong `sitemap.xml`**: Thêm `https://hhmaker.vn/` với priority 1.0; loại bỏ các trang `/dang-ky`, `/dang-nhap`, `/dashboard`, `/search`, `/error`.
3. **Cấu hình 301 Redirect trang Giới thiệu**: Redirect 301 vĩnh viễn `/about` và `/ve-chung-toi` về `/gioi-thieu`.
4. **Cấu hình DNS CNAME và Nginx redirect cho `www.hhmaker.vn`**: Khắc phục lỗi `ERR_NAME_NOT_RESOLVED` khi người dùng gõ `www`.
5. **Dọn sạch lỗi trang 404**: Sửa tiêu đề "Betech" thành "HH Maker", cập nhật đúng hotline `0906636425` và gán `<meta name="robots" content="noindex">` cho `/error`.

---

## 2. PHÂN TÍCH CHI TIẾT 7 TRỤ CỘT SEO KÈM BẰNG CHỨNG THỰC TẾ

---

### 2.1. TECHNICAL SEO (Điểm số: 42 / 100 - Trọng số 22%)

#### Bằng chứng kiểm tra kết nối & Robots.txt
- **Trạng thái URL**: `https://hhmaker.vn/` trả về HTTP/2 200 OK.
- **Nội dung thực tế `robots.txt`**:
  ```text
  User-agent: *
  Allow: /

  Sitemap: https://funkystyle.vn/sitemap.xml
  ```
  *Đánh giá*: Đây là lỗi tàn dư sao chép cấu hình từ dự án cũ `funkystyle.vn`. Lỗi này khiến các công cụ tìm kiếm tải sai sitemap của trang khác.

#### Phân tích cấu trúc Sitemap XML (`https://hhmaker.vn/sitemap.xml`)
- Tổng số URL thu thập được: **80 URLs**.
- URL trang chủ `https://hhmaker.vn/`: **HOÀN TOÀN KHÔNG CÓ TRONG SITEMAP**.
- Phát hiện các URL bị lỗi server nằm trong Sitemap:
  - `https://hhmaker.vn/tin-tuc` (HTTP status: 500)
  - `https://hhmaker.vn/en/posts` (HTTP status: 500)
- Phát hiện các URL nhạy cảm và trùng lặp nằm trong Sitemap:
  - Các trang xác thực / cá nhân: `/dang-ky`, `/dang-nhap`, `/dashboard`, `/dat-lai-mat-khau`, `/ho-so-ca-nhan`, `/quen-mat-khau`.
  - Các trang tìm kiếm nội bộ: `/search`, `/search-v2`, `/tim-kiem`.
  - Trang lỗi hệ thống: `/error` (Trả về HTTP 200 soft-404 với `<meta name="robots" content="index">`).

#### Kiểm tra phân giải tên miền phụ `www.hhmaker.vn`
- Kết quả kiểm tra mạng: `net::ERR_NAME_NOT_RESOLVED`.
- Không có bản ghi DNS (CNAME hoặc A) cho `www.hhmaker.vn`. Người dùng gõ `www` sẽ không thể truy cập website.

#### Phân tích HTTP Security Headers
```http
server: nginx/1.18.0 (Ubuntu)
vary: X-Inertia
cache-control: no-cache, private
x-content-type-options: nosniff
x-frame-options: SAMEORIGIN
```
- **Thiếu**: `Strict-Transport-Security` (HSTS).
- **Thiếu**: `Content-Security-Policy` (CSP).
- **Thiếu**: `Referrer-Policy`.
- **Cảnh báo bảo mật**: Lộ phiên bản máy chủ `nginx/1.18.0 (Ubuntu)`. Cần bật `server_tokens off;`.

---

### 2.2. CONTENT QUALITY & E-E-A-T (Điểm số: 48 / 100 - Trọng số 23%)

#### Tình trạng chuyên mục Blog / Tin tức
- Truy cập `https://hhmaker.vn/tin-tuc` nhận phản hồi:
  ```json
  {"error":"Something went wrong."}
  ```
- **Tác động tiêu cực**: Website hoàn toàn không có bài viết chia sẻ kiến thức, hướng dẫn in 3D hay case study thực tế nào có thể được lập chỉ mục trên Google. Khách hàng tiềm năng không thể tìm thấy HH Maker qua các từ khóa tìm kiếm thông tin (Informational queries).

#### Đánh giá E-E-A-T (Google Quality Rater Guidelines)
- **Experience (Trải nghiệm thực tế)**: Trang `/quy-trinh` có giải thích 5 bước gia công nhưng toàn bộ website thiếu ảnh thực tế "trên tay" sản phẩm, video kiểm tra độ bền, hình ảnh dàn máy in 3D thật tại xưởng.
- **Expertise (Chuyên môn)**: Các thuật ngữ in 3D (PLA+, Resin 8K, Carbon Fiber, SLS) được sử dụng đúng nhưng chưa có các bài viết cẩm nang chuyên sâu để chứng minh năng lực kỹ thuật.
- **Authoritativeness (Thẩm quyền)**: Chưa có liên kết từ các trang báo công nghệ, diễn đàn kỹ thuật cơ khí, hoặc profile mạng xã hội hoàn chỉnh (link Instagram và TikTok trỏ về trang chủ chung chung).
- **Trustworthiness (Độ tin cậy)**: 
  - Đã có đầy đủ 3 trang chính sách: Bảo hành & đổi trả, Bảo mật thông tin, Điều khoản dịch vụ.
  - Tuy nhiên, sự xuất hiện của tên thương hiệu cũ "Betech" và số điện thoại cố định lạ trên trang 404 làm giảm độ tin cậy nghiêm trọng.

---

### 2.3. ON-PAGE SEO (Điểm số: 52 / 100 - Trọng số 20%)

#### Tình trạng Duplicate Content & Canonical Loop
- Khảo sát 3 URL trang Giới thiệu:
  1. `https://hhmaker.vn/about` (HTTP 200, Canonical: `https://hhmaker.vn/about`)
  2. `https://hhmaker.vn/gioi-thieu` (HTTP 200, Canonical: `https://hhmaker.vn/gioi-thieu`)
  3. `https://hhmaker.vn/ve-chung-toi` (HTTP 200, Canonical: `https://hhmaker.vn/ve-chung-toi`)
- Cả 3 URL có nội dung và thẻ tiêu đề giống hệt nhau nhưng tự trỏ canonical về chính mình, khiến Google bot phải thu thập 3 lần và phân tán thứ hạng.

#### Cấu trúc Heading (H1 – H4)
- **Thẻ H1**: Mỗi trang chỉ có 1 thẻ H1 (đạt chuẩn cấu trúc), nhưng nội dung thẻ H1 mang tính khẩu hiệu tiếp thị thay vì tối ưu từ khóa SEO:
  - Trang chủ: `HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D`
  - Trang sản phẩm: `Ý TƯỞNG THÀNH HÌNH`
  - Trang dịch vụ: `MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG`
  - Trang quy trình: `RÕ RÀNG TỪNG BƯỚC / CHỈN CHU TỪNG SẢN PHẨM`
- **Thẻ H4**: Lạm dụng thẻ H4 cho các cột điều hướng ở Footer (`Trang chủ`, `Dịch vụ`, `Liên hệ tư vấn`).

#### Vấn đề đa ngôn ngữ (International SEO)
- Trang tiếng Anh `https://hhmaker.vn/en` có thẻ HTML là `<html lang="vi">` (sai mã ngôn ngữ tài liệu).
- Hoàn toàn không có thẻ `<link rel="alternate" hreflang="...">` liên kết giữa các phiên bản ngôn ngữ.

#### Trùng lặp thẻ Meta do Inertia.js Hydration
- Phát hiện thẻ `<meta name="description">`, `<meta property="og:title">`, `<meta property="og:description">` xuất hiện 2 lần trong mã nguồn HTML (1 lần từ Blade layout và 1 lần từ Inertia client-side với thuộc tính `inertia=""`).

---

### 2.4. SCHEMA & STRUCTURED DATA (Điểm số: 25 / 100 - Trọng số 10%)

#### Khảo sát Schema thực tế trên các trang
- Hiện tại, website chỉ có duy nhất schema cơ bản:
  ```json
  {
    "@context": "https://schema.org",
    "@type": "WebPage",
    "name": "...",
    "description": "..."
  }
  ```

#### Bảng đối chiếu Schema thiếu hụt
| Loại Schema | Trang cần có | Hiện trạng | Tác động |
| :--- | :--- | :---: | :--- |
| **`Product`** | Chi tiết sản phẩm (`/san-pham/*`) | ❌ Thiếu | Mất hiển thị giá, đánh giá sao trên SERP |
| **`Offer`** | Chi tiết sản phẩm (`/san-pham/*`) | ❌ Thiếu | Không thể xuất hiện trong Google Shopping tự nhiên |
| **`LocalBusiness`** | Trang chủ & Liên hệ | ❌ Thiếu | Không hiển thị Knowledge Panel doanh nghiệp địa phương |
| **`BreadcrumbList`** | Toàn bộ trang con | ❌ Thiếu | Google hiển thị URL thô thay vì thanh điều hướng đẹp |
| **`ItemList`** | Danh mục sản phẩm (`/san-pham`) | ❌ Thiếu | Mất Carousel sản phẩm trên mobile search |
| **`FAQPage`** | Dịch vụ & Hướng dẫn | ❌ Thiếu | Mất khối câu hỏi thả xuống trực tiếp trên Google |

---

### 2.5. PERFORMANCE & CORE WEB VITALS (Điểm số: 58 / 100 - Trọng số 10%)

#### Chỉ số đo đạc tại trình duyệt
- **TTFB (Time to First Byte)**: 77ms (Rất nhanh, máy chủ Nginx phản hồi tốt).
- **FCP (First Contentful Paint)**: 256ms.
- **Tổng dung lượng tài nguyên giải mã**: **7.78 MB**.
- **Dung lượng hình ảnh**: **6.22 MB** (Chiếm xấp xỉ 80% tải trọng trang).

#### Các điểm nghẽn nghiêm trọng (Bottlenecks)
1. **Ảnh Hero Banner quá nặng**: File `hero-banner.png` có dung lượng giải mã lên tới **2,748 KB (2.75 MB)** định dạng PNG thuần. Đây là phần tử LCP chính, gây nguy cơ trễ LCP nghiêm trọng trên mạng 3G/4G di động.
2. **File Vector Logo quá nặng**: File `figma-header-logo.svg` chiếm tới **668 KB**, lớn gấp 40 lần tiêu chuẩn của một logo web thông thường (< 15 KB).
3. **41 / 46 ảnh thiếu Lazy Loading**: Các ảnh sản phẩm phía dưới màn hình vẫn bị tải sớm cùng lúc với hero banner.
4. **100% ảnh thiếu thuộc tính `width` và `height`**: Gây ra hiện tượng Cumulative Layout Shift (CLS) khi nạp trang.

---

### 2.6. AI SEARCH READINESS (GEO) (Điểm số: 35 / 100 - Trọng số 10%)

#### Khảo sát sự sẵn sàng cho AI Search Engines
- `https://hhmaker.vn/llms.txt`: **404 Not Found**.
- `https://hhmaker.vn/.well-known/llms.txt`: **404 Not Found**.
- `robots.txt`: Không có chỉ dẫn nào cho các bot AI (`GPTBot`, `ClaudeBot`, `PerplexityBot`, `Google-Extended`).
- **Khả năng trích xuất thông tin (Citability)**: Thiếu các bảng so sánh vật liệu, thiếu bảng báo giá tham khảo, thiếu khối FAQ có cấu trúc câu hỏi - câu trả lời ngắn gọn.
- **Độ nhiễu thực thể**: Tên thương hiệu cũ "Betech" xuất hiện trên trang 404 làm sai lệch việc AI định danh thực thể HH Maker.

---

### 2.7. IMAGES & VISUAL ASSETS (Điểm số: 55 / 100 - Trọng số 5%)

#### Thống kê kiểm toán hình ảnh trên trang chủ
- **Tổng số hình ảnh**: 46 ảnh.
- **Tỷ lệ ảnh có thẻ `alt`**: **100% (46 / 46 ảnh)**. Thẻ alt được viết mô tả rõ ràng, không có thẻ rỗng.
- **Tỷ lệ ảnh thiếu thuộc tính `width`/`height`**: **100% (46 / 46 ảnh)**.
- **Tỷ lệ sử dụng định dạng ảnh thế hệ mới (WebP/AVIF)**: **0% (0 / 46 ảnh)**. Toàn bộ là PNG/JPG.
- **Hiện tượng ảnh ngoại cỡ**: Ảnh sản phẩm gốc có kích thước 768x1376 px nhưng chỉ hiển thị trong khung 262x335 px trên giao diện thực tế mà không có thẻ `srcset` co giãn theo màn hình.

---

## 3. LỘ TRÌNH THỰC THI KHUYẾN NGHỊ (ACTION ROADMAP)

Chi tiết các bước thực hiện được phân kỳ cụ thể trong file:
👉 **[hhmaker.vn-audit/ACTION-PLAN.md](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/ACTION-PLAN.md)**

### Bảng tóm tắt phân kỳ:
- **Phase 1: Critical Fixes (Tuần 1)**: Sửa Sitemap robots.txt, khôi phục lỗi HTTP 500 trang tin tức, thêm trang chủ vào sitemap, 301 redirect trang giới thiệu, cấu hình DNS www, dọn sạch trang 404.
- **Phase 2: High-Impact Improvements (Tuần 2–3)**: Nén ảnh Hero Banner & Logo SVG, triển khai bộ Schema (Product, LocalBusiness, Breadcrumbs), bổ sung width/height và lazy loading, sửa duplicate meta tags, cấu hình hreflang vi/en.
- **Phase 3: Content & Authority (Tháng 2)**: Viết 10 bài viết kiến thức in 3D, mở rộng trang Giới thiệu (800+ từ, ảnh xưởng thật), bổ sung tab thông số kỹ thuật sản phẩm, tạo file `llms.txt`, thêm FAQ.
- **Phase 4: Monitoring & Iteration (Liên tục)**: Theo dõi Google Search Console, theo dõi Core Web Vitals thực tế (CrUX), kiểm toán SEO định kỳ.

---

## 4. HỒ SƠ TÀI LIỆU MINH CHỨNG (AUDIT ARTIFACTS INDEX)

Toàn bộ dữ liệu kiểm toán thực tế và báo cáo chuyên sâu theo từng mảng đã được khởi tạo đầy đủ:

1. **Dữ liệu cấu trúc JSON chuẩn Envelope**:
   - [`hhmaker.vn-audit/audit-data.json`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/audit-data.json)
2. **Kế hoạch hành động chi tiết**:
   - [`hhmaker.vn-audit/ACTION-PLAN.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/ACTION-PLAN.md)
3. **Báo cáo chuyên sâu 7 trụ cột (Specialist Findings)**:
   - Technical SEO: [`hhmaker.vn-audit/findings/technical.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/technical.md)
   - Content Quality & E-E-A-T: [`hhmaker.vn-audit/findings/content.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/content.md)
   - Schema & Structured Data: [`hhmaker.vn-audit/findings/schema.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/schema.md)
   - Performance & Core Web Vitals: [`hhmaker.vn-audit/findings/performance.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/performance.md)
   - On-Page SEO: [`hhmaker.vn-audit/findings/on-page.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/on-page.md)
   - Images & Visual Assets: [`hhmaker.vn-audit/findings/images.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/images.md)
   - AI Search Readiness (GEO): [`hhmaker.vn-audit/findings/ai-search.md`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/findings/ai-search.md)
4. **Ảnh chụp màn hình thực tế (Screenshots)**:
   - Desktop Viewport (1440x900): [`hhmaker.vn-audit/screenshots/desktop-home.png`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/screenshots/desktop-home.png)
   - Mobile Viewport (390x844): [`hhmaker.vn-audit/screenshots/mobile-home.png`](file:///Users/macbookpro2020/bed-hhmaker/hhmaker.vn-audit/screenshots/mobile-home.png)
