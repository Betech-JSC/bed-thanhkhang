# Báo Cáo Chuyên Sâu: On-Page SEO (Trọng số 20%)

> **Điểm đánh giá**: **52 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Trụ cột phân tích**: Cấu trúc thẻ tiêu đề (Title), Thẻ mô tả (Meta Description), Cấu trúc phân cấp tiêu đề (H1–H6), Xung đột Canonical, Đa ngôn ngữ (Hreflang) và Liên kết nội bộ (Internal Links).

---

## 1. Bằng chứng thực tế thu thập (Evidence)

### 1.1. Khảo sát Title & Meta Description các trang chủ đạo
| URL | Title Tag (Độ dài ký tự) | Meta Description (Độ dài ký tự) | Nhận xét |
| :--- | :--- | :--- | :--- |
| `https://hhmaker.vn/` | `HH Maker - Hiện Thực Hóa Mọi Ý Tưởng Với Công Nghệ In 3D` (56 ký tự) | `Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại.` (116 ký tự) | Title chuẩn, Meta Description hơi ngắn (khuyến nghị 140–160 ký tự) |
| `https://hhmaker.vn/san-pham` | `Danh mục Sản phẩm in 3D - HH Maker \| Công nghệ FDM, SLA, SLS` (61 ký tự) | `Khám phá bộ sưu tập sản phẩm in 3D công nghệ cao từ HH Maker. Đa dạng vật liệu Resin 8K, PLA+, PETG, Carbon Fiber chính xác từng micron.` (138 ký tự) | Tốt, đầy đủ từ khóa và công nghệ |
| `https://hhmaker.vn/dich-vu` | `Dịch Vụ In Ấn & Thiết Kế 3D Theo Yêu Cầu - HH Maker` (50 ký tự) | `HH Maker đồng hành từ làm rõ ý tưởng đến lựa chọn cách tạo ra sản phẩm: Thiết kế 3D, Gia công 3D, và Làm đồ custom với tiêu chuẩn hoàn thiện cao nhất.` (148 ký tự) | Rất tốt |
| `https://hhmaker.vn/san-pham/mo-hinh-phi-hanh-gia` | `Mô hình Phi hành gia Không gian - HH Maker \| Dịch Vụ In 3D Chuyên Nghiệp` (71 ký tự) | `Tạo hình sắc nét, vật liệu PLA matte chống bám vân tay, độ bền màu cao.` (73 ký tự) | Meta Description quá ngắn, thiếu từ khóa mua hàng |
| `https://hhmaker.vn/gioi-thieu` | `Giới thiệu - HH Maker \| Đưa sáng tạo vượt ra khỏi màn hình` (58 ký tự) | Đầy đủ thông điệp sáng lập | Tốt |

---

## 2. Các vấn đề On-Page SEO nghiêm trọng phát hiện

### [CRITICAL] 1. Vấn nạn tự ăn thịt từ khóa (Triplicate URL Cannibalization) trên trang Giới thiệu
- **Thực trạng**: Website đang mở cùng lúc 3 URL với nội dung và thẻ Title giống hệt nhau:
  - `https://hhmaker.vn/about` (HTTP 200, Canonical trỏ về `/about`)
  - `https://hhmaker.vn/gioi-thieu` (HTTP 200, Canonical trỏ về `/gioi-thieu`)
  - `https://hhmaker.vn/ve-chung-toi` (HTTP 200, Canonical trỏ về `/ve-chung-toi`)
- **Hậu quả**: Thay vì gom toàn bộ sức mạnh liên kết (link juice) vào 1 URL duy nhất, website chia nhỏ sức mạnh làm 3 phần. Google không biết nên ưu tiên URL nào và có thể luân phiên hoán đổi hoặc không xếp hạng cả 3.
- **Giải pháp**:
  - Chọn URL tiếng Việt chuẩn mực: `https://hhmaker.vn/gioi-thieu`.
  - Cấu hình Nginx hoặc Laravel Route chuyển hướng 301 vĩnh viễn:
    - `/about` -> 301 -> `/gioi-thieu`
    - `/ve-chung-toi` -> 301 -> `/gioi-thieu`
    - `/about-us` -> 301 -> `/gioi-thieu` (Hiện tại đã redirect)

---

### [HIGH] 2. Hiện tượng Duplicate Meta Description & OpenGraph do Inertia.js Hydration
- **Thực trạng**: Khi phân tích mã HTML trả về từ server, xuất hiện 2 cặp thẻ meta mô tả:
  ```html
  <!-- Thẻ 1 do Blade Layout render -->
  <meta name="description" content="Không giới hạn không gian sáng tạo...">
  <meta property="og:title" content="HH Maker - Hiện Thực Hóa...">
  <meta property="og:description" content="Không giới hạn không gian sáng tạo...">

  <!-- Thẻ 2 do Inertia client-side chèn vào khi mount -->
  <meta name="description" content="Không giới hạn..." inertia="">
  <meta property="og:title" content="HH Maker - Hiện Thực Hóa..." inertia="">
  <meta property="og:description" content="Không giới hạn..." inertia="">
  ```
- **Hậu quả**: Một số crawler cổ điển hoặc social crawler (Facebook Debugger, Zalo) có thể bị nhầm lẫn hoặc lấy sai thẻ meta.
- **Giải pháp**: Đặt thuộc tính `head-key` đồng nhất trong component `<Head>` của Inertia Vue 3:
  ```vue
  <Head>
    <meta name="description" head-key="description" :content="description" />
    <meta property="og:title" head-key="og:title" :content="title" />
  </Head>
  ```

---

### [HIGH] 3. Thẻ H1 mang nặng tính khẩu hiệu tiếp thị (Slogan), thiếu từ khóa tìm kiếm
- **Khảo sát H1 trên toàn site**:
  - Trang chủ: `HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D` (Hơi chung chung, thiếu địa danh hoặc loại hình dịch vụ mục tiêu)
  - Trang `/san-pham`: `Ý TƯỞNG THÀNH HÌNH` (Thiếu hẳn từ khóa "Sản phẩm in 3D")
  - Trang `/dich-vu`: `MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG` (Thiếu từ khóa "Dịch vụ in 3D theo yêu cầu")
  - Trang `/quy-trinh`: `RÕ RÀNG TỪNG BƯỚC / CHỈN CHU TỪNG SẢN PHẨM`
  - Trang `/lien-he`: `KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN`
- **Khuyến nghị**:
  - Giữ lại phần text nghệ thuật làm Subtitle/Kicker, đưa từ khóa SEO giá trị cao vào H1 chính thức:
    - Trang `/san-pham`: H1: `Sản Phẩm In 3D Độc Bản & Phụ Kiện Cơ Khí Cao Cấp`
    - Trang `/dich-vu`: H1: `Dịch Vụ In 3D & Gia Công Thiết Kế Theo Yêu Cầu Tại TP.HCM`

---

### [HIGH] 4. Lỗi ngôn ngữ HTML và thiếu thẻ Hreflang cho phiên bản tiếng Anh
- **Thực trạng**:
  - Tại trang tiếng Anh `https://hhmaker.vn/en`, thẻ HTML root vẫn là `<html lang="vi">`.
  - Toàn bộ website hoàn toàn không có thẻ `<link rel="alternate" hreflang="...">`.
- **Hậu quả**: Trình duyệt Chrome và Google Search hiểu nhầm trang tiếng Anh là tài liệu tiếng Việt, ảnh hưởng đến khả năng phân phối kết quả tìm kiếm cho người dùng quốc tế tại Việt Nam hoặc nước ngoài.
- **Giải pháp**:
  - Cập nhật dynamic language attribute: `<html :lang="currentLocale">`.
  - Bổ sung cụm thẻ Hreflang:
    ```html
    <link rel="alternate" hreflang="vi" href="https://hhmaker.vn/" />
    <link rel="alternate" hreflang="en" href="https://hhmaker.vn/en" />
    <link rel="alternate" hreflang="x-default" href="https://hhmaker.vn/" />
    ```

---

### [MEDIUM] 5. Lạm dụng thẻ Heading (H4) cho cột điều hướng Footer
- **Thực trạng**: Các tiêu đề cột ở cuối chân trang như *"Trang chủ"*, *"Dịch vụ"*, *"Liên hệ tư vấn"* đang được bọc trong thẻ `<h4>`.
- **Khuyến nghị**: Chuyển các thẻ này sang thẻ `<span>` hoặc `<p class="font-semibold text-sm">` để tránh làm nhiễu sơ đồ cấu trúc Heading của tài liệu.
