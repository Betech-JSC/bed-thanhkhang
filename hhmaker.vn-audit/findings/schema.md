# Báo Cáo Chuyên Sâu: Schema & Structured Data (Trọng số 10%)

> **Điểm đánh giá**: **25 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Hiện trạng**: Website chỉ có duy nhất 1 schema loại `WebPage` cơ bản trên các trang, thiếu hoàn toàn các schema thương mại điện tử và nhận diện thực thể kinh doanh.

---

## 1. Bằng chứng thực tế thu thập (Evidence)

### 1.1. Schema hiện tại trên Trang chủ & Danh mục sản phẩm
```json
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D",
  "description": "Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại."
}
```

### 1.2. Schema hiện tại trên Trang chi tiết sản phẩm (`/san-pham/mo-hinh-phi-hanh-gia`)
```json
{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "Mô hình Phi hành gia Không gian - HH Maker | Dịch Vụ In 3D Chuyên Nghiệp",
  "description": "Tạo hình sắc nét, vật liệu PLA matte chống bám vân tay, độ bền màu cao."
}
```

---

## 2. Các thiếu sót Schema nghiêm trọng

| Loại Schema còn thiếu | Vị trí áp dụng | Tác động SEO khi thiếu | Mức độ ưu tiên |
| :--- | :--- | :--- | :--- |
| **`Product` & `Offer`** | Trang chi tiết sản phẩm (`/san-pham/*`) | Không hiển thị giá bán, tình trạng còn hàng (InStock), hình ảnh sản phẩm trong Google Shopping và kết quả tìm kiếm tự nhiên | **Critical** |
| **`LocalBusiness` / `Organization`** | Toàn bộ website (Layout chung) | Google không hiểu được địa chỉ doanh nghiệp tại TP.HCM, số hotline, logo chính thức để kích hoạt Knowledge Panel bên phải kết quả tìm kiếm | **High** |
| **`BreadcrumbList`** | Tất cả trang con (Sản phẩm, Dịch vụ, Chính sách) | Kết quả tìm kiếm hiển thị URL thô thay vì chuỗi điều hướng danh mục phân cấp rõ ràng | **High** |
| **`ItemList`** | Trang danh mục sản phẩm (`/san-pham`) | Không kích hoạt Carousel kết quả tìm kiếm danh mục sản phẩm trên thiết bị di động | **High** |
| **`Service`** | Trang dịch vụ (`/dich-vu`, `/dich-vu/*`) | Không định danh được các loại hình dịch vụ chính (Gia công 3D, Thiết kế 3D, Custom) | **Medium** |
| **`WebSite` + `SearchAction`** | Trang chủ | Không kích hoạt được ô tìm kiếm nhanh (Sitelinks Searchbox) trực tiếp trên Google | **Medium** |
| **`FAQPage`** | Trang Dịch vụ & Sản phẩm | Bỏ lỡ Rich Snippet hỏi đáp trực tiếp dạng dropdown trên SERP | **Medium** |

---

## 3. Bộ Schema JSON-LD đề xuất triển khai chuẩn

### 3.1. Schema `Organization` & `LocalBusiness` (Nhúng vào layout chính)
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "@id": "https://hhmaker.vn/#organization",
  "name": "Công ty In ấn 3D HH Maker",
  "alternateName": "HH Maker 3D Printing",
  "url": "https://hhmaker.vn",
  "logo": "https://hhmaker.vn/assets/images/header/figma-header-logo.svg",
  "image": "https://hhmaker.vn/cover.jpg",
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
</script>
```

### 3.2. Schema `Product` & `Offer` (Nhúng vào từng trang sản phẩm `/san-pham/{slug}`)
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "Mô hình Phi hành gia Không gian",
  "image": [
    "https://hhmaker.vn/images/pdp/related_1.png"
  ],
  "description": "Mô hình phi hành gia in 3D công nghệ cao, vật liệu PLA matte chống bám vân tay, độ bền màu cao.",
  "sku": "HHM-FIG-001",
  "brand": {
    "@type": "Brand",
    "name": "HH Maker"
  },
  "offers": {
    "@type": "Offer",
    "url": "https://hhmaker.vn/san-pham/mo-hinh-phi-hanh-gia",
    "priceCurrency": "VND",
    "price": "250000",
    "priceValidUntil": "2027-12-31",
    "itemCondition": "https://schema.org/NewCondition",
    "availability": "https://schema.org/InStock",
    "seller": {
      "@type": "Organization",
      "name": "HH Maker"
    }
  }
}
</script>
```

### 3.3. Schema `BreadcrumbList` (Áp dụng cho mọi trang phân cấp)
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Trang chủ",
      "item": "https://hhmaker.vn"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Sản phẩm",
      "item": "https://hhmaker.vn/san-pham"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Mô hình Phi hành gia Không gian",
      "item": "https://hhmaker.vn/san-pham/mo-hinh-phi-hanh-gia"
    }
  ]
}
</script>
```
