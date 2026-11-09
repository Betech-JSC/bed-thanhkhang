# Báo Cáo Chuyên Sâu: Performance & Core Web Vitals (Trọng số 10%)

> **Điểm đánh giá**: **58 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Chỉ số trọng tâm**: LCP (Largest Contentful Paint), CLS (Cumulative Layout Shift), TTFB (Time to First Byte), Resource Payload.

---

## 1. Dữ liệu đo đạc thực tế tại trình duyệt (Performance Timings)

### 1.1. Navigation Timing Metrics (Desktop)
```json
{
  "ttfb": 77,
  "domInteractive": 100,
  "domContentLoaded": 128,
  "loadEventEnd": 132,
  "fcp": 256
}
```
*Nhận xét*: Thời gian phản hồi máy chủ ban đầu (TTFB = 77ms) và First Contentful Paint (FCP = 256ms) trên đường truyền nội bộ là rất ấn tượng. Máy chủ Nginx xử lý dynamic response nhanh chóng.

### 1.2. Phân tích tài nguyên mạng (Resource Budget)
- **Tổng số tài nguyên nạp trên trang chủ**: 47 requests.
- **Tổng dung lượng tài nguyên sau giải mã**: **7.78 MB**.
- **Trong đó tài nguyên hình ảnh chiếm**: **6.22 MB** (**79.9%** tổng dung lượng trang).
- **Số lượng file JavaScript**: 12 files (sử dụng ES Modules / Vite).
- **Số lượng file CSS**: 6 files.

### 1.3. Top các tài nguyên "nặng" nhất làm chậm trang (Heavy Assets)
| Tên file / URL | Loại | Dung lượng giải mã | Vấn đề phát hiện |
| :--- | :--- | :--- | :--- |
| `assets/images/homepage/hero-banner.png` | Image (PNG) | **2,748 KB (2.75 MB)** | File PNG thô cực nặng làm chậm LCP trên mạng di động |
| `assets/images/header/figma-header-logo.svg` | Vector (SVG) | **668 KB** | File SVG chứa vector rườm rà hoặc data nhúng, quá nặng đối với một logo |
| `images/pdp/product_main.png` | Image (PNG) | **579 KB** | Chưa được nén hoặc chuyển đổi WebP |
| `images/pdp/related_3.png` | Image (PNG) | **373 KB** | Ảnh phụ nhưng dung lượng lớn |
| `images/pdp/related_1.png` | Image (PNG) | **272 KB** | Chưa nén |

---

## 2. Các điểm nghẽn hiệu năng cốt lõi (Bottlenecks)

### 2.1. Nguy cơ LCP trên thiết bị di động (Mobile LCP Risk)
- Phần tử LCP (Largest Contentful Paint) chính là bức ảnh `hero-banner.png`.
- Với dung lượng **2.75 MB**, trên kết nối di động 3G/4G tại Việt Nam (băng thông trung bình 15–30 Mbps), bức ảnh này sẽ mất từ **1.5 đến 3.2 giây** chỉ để tải xong dữ liệu ảnh, khiến chỉ số LCP dễ dàng rơi vào vùng cảnh báo đỏ (> 2.5s) trong báo cáo thực tế Google Search Console (CrUX field data).

### 2.2. Nguy cơ CLS do thiếu kích thước width/height (Layout Shifts)
- Toàn bộ 46/46 thẻ `<img>` trên trang chủ không có thuộc tính `width` và `height`.
- Trình duyệt không thể dự trù trước khoảng trống (aspect ratio box) khi đang tải ảnh, dẫn đến việc khi các khối ảnh carousel và danh sách sản phẩm load xong, nội dung trang bị đẩy giật xuống dưới liên tục.

### 2.3. Lãng phí tài nguyên do tải trước (Eager loading) ảnh ngoài màn hình
- Có tới **41 / 46 hình ảnh** nằm sâu dưới màn hình cuộn (below-the-fold) bị gán thuộc tính `loading="eager"` hoặc không có `loading="lazy"`.
- Hậu quả: Trình duyệt phải tải đồng thời hàng chục hình ảnh sản phẩm phía dưới cùng một lúc, cạnh tranh băng thông và CPU với ảnh Hero Banner trên màn hình đầu tiên.

---

## 3. Khuyến nghị giải pháp tối ưu hóa hiệu năng

1. **Nén và chuyển đổi ảnh Hero Banner**:
   - Sử dụng định dạng **WebP** hoặc **AVIF**.
   - Xuất các kích cỡ responsive (Desktop: 1600px ~150KB; Mobile: 800px ~60KB) bằng thẻ `<picture>`:
     ```html
     <picture>
       <source srcset="/assets/images/homepage/hero-banner.webp" type="image/webp">
       <img src="/assets/images/homepage/hero-banner.png" alt="HH Maker 3D Printing" width="1280" height="616" fetchpriority="high">
     </picture>
     ```
2. **Tối ưu file SVG Logo**:
   - Chạy công cụ `svgo` để loại bỏ metadata thừa, đưa dung lượng logo từ 668 KB về dưới 15 KB (giảm 97%).
3. **Kích hoạt Lazy Loading chuẩn xác**:
   - Chỉ đặt `fetchpriority="high"` và `loading="eager"` cho duy nhất 1 ảnh Hero Banner.
   - Toàn bộ các ảnh carousel và sản phẩm bên dưới đặt `loading="lazy" decoding="async"`.
4. **Khai báo width/height rõ ràng**:
   - Khai báo thuộc tính `width` và `height` trên từng thẻ `<img>` hoặc thiết lập thuộc tính CSS `aspect-ratio: 16/9;` / `aspect-ratio: 1/1;` để trình duyệt giữ chỗ trước.
