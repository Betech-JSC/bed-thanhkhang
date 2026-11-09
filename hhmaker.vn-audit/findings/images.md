# Báo Cáo Chuyên Sâu: Images & Visual Assets (Trọng số 5%)

> **Điểm đánh giá**: **55 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Tiêu chí kiểm toán**: Thẻ Alt văn bản thay thế, Kích thước thực tế vs Kích thước hiển thị, Định dạng ảnh thế hệ mới (Next-Gen), Khả năng tải lười (Lazy Loading), Tác động đến CLS.

---

## 1. Số liệu thống kê thực tế (Image Audit Statistics)

| Chỉ số kiểm toán | Giá trị ghi nhận | Đánh giá |
| :--- | :--- | :--- |
| **Tổng số hình ảnh trên trang chủ** | 46 ảnh | Khối lượng ảnh phong phú |
| **Số ảnh thiếu thẻ `alt`** | **0 ảnh (0%)** | **Xuất sắc**: 100% ảnh đều có thuộc tính alt |
| **Số ảnh có thẻ `alt` rỗng (`alt=""`)** | **0 ảnh (0%)** | Không có thẻ alt rỗng vô nghĩa |
| **Số ảnh thiếu thuộc tính `width` và `height`** | **46 / 46 ảnh (100%)** | **Báo động đỏ**: Vi phạm nghiêm trọng tiêu chuẩn CLS |
| **Số ảnh không áp dụng `loading="lazy"`** | **41 / 46 ảnh (89.1%)** | Quá nhiều ảnh dưới fold bị ép tải sớm |
| **Số ảnh sử dụng định dạng WebP hoặc AVIF** | **0 ảnh (0%)** | Hoàn toàn dùng PNG/JPG/SVG truyền thống |
| **Tổng dung lượng giải mã ảnh trên trang chủ** | **6.22 MB** | Rất nặng cho trang thương mại điện tử |

---

## 2. Chi tiết các phát hiện và phân tích kỹ thuật

### [HIGH] 1. 100% hình ảnh không có kích thước width & height cố định
- **Thực trạng**: Toàn bộ các thẻ `<img>` từ logo, banner đến danh sách sản phẩm chỉ có `src` và `alt`, không hề có thuộc tính HTML `width="xxx"` và `height="xxx"`.
- **Hậu quả**: Khi trình duyệt bắt đầu render trang và chưa tải xong file ảnh, chiều cao của khối ảnh được tính bằng 0. Khi ảnh tải xong, khung ảnh bung rộng ra đột ngột đẩy toàn bộ nội dung phía dưới dịch chuyển. Điều này dẫn tới điểm số **Cumulative Layout Shift (CLS)** kém, ảnh hưởng trực tiếp đến thứ hạng Google Page Experience.
- **Giải pháp**:
  - Khai báo rõ thuộc tính width và height gốc của ảnh:
    ```html
    <img src="/images/pdp/related_1.png" alt="Mô hình Phi hành gia" width="546" height="702" class="w-full h-auto object-cover" />
    ```

---

### [HIGH] 2. Hoàn toàn vắng bóng định dạng ảnh thế hệ mới (WebP / AVIF)
- **Thực trạng**: Tất cả các sản phẩm và ảnh bìa đều sử dụng định dạng PNG thô chưa nén:
  - `hero-banner.png`: **2.75 MB**
  - `product_main.png`: **579 KB**
  - `related_3.png`: **373 KB**
- **Hiệu quả khi chuyển đổi**:
  - Định dạng **WebP** giảm trung bình 30–50% dung lượng so với PNG/JPEG ở cùng chất lượng mắt thường.
  - Định dạng **AVIF** giảm từ 50–70% dung lượng.
  - File `hero-banner.png` 2.75 MB sau khi chuyển đổi sang WebP 85% quality chỉ còn khoảng **160 KB** (giảm tới **94% băng thông**).

---

### [MEDIUM] 3. Hiện tượng ảnh ngoại cỡ (Oversized Images)
- **Thực trạng**:
  - File `figma-product-16.png` có độ phân giải gốc là **768 x 1376 px**, nhưng trên giao diện Desktop chỉ hiển thị ở khung **262 x 335 px** (trên Mobile là khoảng 180 x 240 px).
  - Trình duyệt di động phải tải một bức ảnh có số điểm ảnh gấp hơn **12 lần** diện tích hiển thị thực tế, lãng phí bộ nhớ RAM và pin của thiết bị.
- **Giải pháp**: Tạo thumbnail tự động bằng Image Intervention trong Laravel hoặc pipeline Vite:
  - Cung cấp các phiên bản: `thumb-300w.webp`, `thumb-600w.webp` kết hợp thuộc tính `srcset`.

---

## 3. Khuyến nghị chuẩn hóa hình ảnh toàn diện

1. **Chuyển đổi toàn bộ ảnh sản phẩm sang WebP**:
   - Chạy script nén hàng loạt các thư mục `public/assets/images/` và `public/images/`.
2. **Chuẩn hóa component Image trong Vue 3**:
   - Tạo component `<AppImage>` tự động chèn `width`, `height`, `loading="lazy"` và `decoding="async"`.
3. **Giữ nguyên chất lượng thẻ Alt**:
   - Duy trì quy chuẩn đặt thẻ alt mô tả chi tiết như hiện tại (ví dụ: *"Mô hình Phi hành gia Không gian"*, *"Móc Khoá Vô Lăng F1 Quick Release Thể Thao"*).
