# Design: Kiến trúc Đồng bộ 21 Sản phẩm Figma vào CMS Database (sync-figma-products-to-cms)

## Context

Hệ thống HH Maker đang hoạt động trên nền tảng Laravel + Inertia.js + Vue 3 với kiến trúc đa ngôn ngữ (`vi` và `en`). Cơ sở dữ liệu CMS quản lý sản phẩm qua các bảng:
- `products`: Chứa thông tin chung, trạng thái (`status`), cờ nổi bật (`is_featured`), vật liệu (`material`), công nghệ in (`print_technology`), thời gian gia công (`lead_time`), giá (`price`, `compare_at_price`), hình ảnh (`images`, `image`).
- `product_translations`: Chứa tiêu đề (`title`), mô tả ngắn (`description`), nội dung chi tiết (`content`), đường dẫn SEO (`slug`, `seo_slug`), siêu dữ liệu SEO.
- `product_categories` & `product_category_translations`: Quản lý 4 danh mục chính (`space_decor`, `keychains`, `stationery`, `custom`).
- `product_ref_categories`: Bảng pivot gán sản phẩm vào danh mục.
- `related_products`: Bảng pivot liên kết các sản phẩm gợi ý chéo cho trang PDP.

Trước đây, `ProductSeeder.php` cũ kế thừa từ dự án logistics chứa lệnh `truncate()` phá hủy dữ liệu và chèn các bản ghi logistics không phù hợp. Ngoài ra, việc thiếu cờ `is_featured = 1` trên các sản phẩm catalog dẫn đến việc hàng danh mục Trang chủ (`ProductsSection.vue`) không thể hiển thị đủ 4 cards theo thiết kế Figma Node 2261:5595.

Xem chi tiết động lực và bối cảnh tại `proposal.md`.

## Goals / Non-Goals

**Goals:**
- Xây dựng quy trình đồng bộ dữ liệu Idempotent an toàn tuyệt đối (Zero Data Loss, Zero Truncate) qua `FigmaProductsSeeder`.
- Đồng bộ chính xác 21 sản phẩm mẫu Figma (Node 2261:5076): 1 Model Cáo ID 1 + 16 sản phẩm catalog chính + 4 sản phẩm liên quan.
- Đảm bảo mỗi danh mục có đúng 4 sản phẩm mang cờ `is_featured = 1` để Trang chủ render đủ 4 cards/hàng.
- Bổ sung cấu trúc 7 thông số kỹ thuật in 3D (`specifications`) cho trang chi tiết sản phẩm (PDP) trên `SpecTable.vue`.
- Đảm bảo toàn bộ 21 sản phẩm đều hỗ trợ đầy đủ 2 ngôn ngữ (`vi` và `en`).
- Thay thế hoàn toàn `ProductSeeder` cũ bằng `FigmaProductsSeeder` trong `DatabaseSeeder.php`.

**Non-Goals:**
- **Tuyệt đối KHÔNG bổ sung giá niêm yết**: Không chèn số tiền vào `price` hoặc `compare_at_price` (luôn giữ giá trị `null`). Hệ thống không kích hoạt tính năng giỏ hàng/thanh toán trực tuyến mà tập trung vào luồng nhận tư vấn và báo giá tùy chỉnh.
- Không thay đổi cấu trúc bảng database (không tạo thêm migration thay đổi schema bảng `products`).
- Không tác động hoặc xóa dữ liệu của các module khác (bài viết, tuyển dụng, dịch vụ, tài khoản quản trị).

## Decisions

### 1. Chiến lược Idempotent Upsert (`updateOrInsert`)
- **Lựa chọn**: Sử dụng `DB::table(...)->updateOrInsert()` theo khóa duy nhất (`slug` hoặc `id` đối với sản phẩm, `slug` đối với danh mục, và cặp khóa ngoại đối với bảng pivot).
- **Lý do**: Cho phép chạy seeder nhiều lần (trên môi trường local, staging, production) mà không sinh ra dữ liệu trùng lặp (duplicate) và không bao giờ phải chạy lệnh `truncate` hay `migrate:fresh`.
- **Phương án thay thế đã loại bỏ**: Sử dụng `truncate()` trước khi insert (đã bị cấm triệt để theo nguyên tắc an toàn dữ liệu của dự án).

### 2. Tuân thủ Ràng buộc Giá niêm yết (Zero-Pricing Constraint)
- **Lựa chọn**: Trong `FigmaProductsSeeder`, các trường `price` và `compare_at_price` được thiết lập tường minh là `null`.
- **Lý do**: Theo yêu cầu từ người dùng, xưởng in 3D HH Maker cung cấp dịch vụ sản xuất theo yêu cầu cá nhân hóa, chi phí phụ thuộc vào kích thước, loại sợi/nhựa và số lượng đặt hàng thực tế. Giá niêm yết sẽ được bổ sung sau nếu khách hàng có nhu cầu.
- **Tác động Frontend**: Các nút hành động trên trang chi tiết và danh mục tiếp tục hiển thị "Nhận tư vấn sản phẩm", mở modal hoặc chuyển tiếp sang trang liên hệ (`/lien-he` / `/en/contact`).

### 3. Phân bổ Cờ `is_featured = 1` (4 thẻ/hàng danh mục Trang chủ)
- **Lựa chọn**: Phân bổ đều mỗi danh mục đúng 4 sản phẩm có `is_featured = 1`:
  - **Móc khoá & Phụ kiện (`keychains`)**: Móc khoá trái tim cơ khí, Móc khoá turbine phản lực, Móc khoá động cơ V8, Móc khoá vô lăng F1.
  - **Trang trí & Đồ dùng (`stationery`)**: Giá đỡ tai nghe Voronoi, Hộp đựng danh thiếp Cyberpunk, Đèn ngủ mặt trăng Lithophane, Mô hình tàu vũ trụ Discovery.
  - **Gia công theo yêu cầu (`custom`)**: Mô hình Iron Man Mark 85, Khung vỏ robot Carbon Fiber, Cụm bánh răng hành tinh, Khớp nối cầu Bionic SLS.
  - **Trang trí không gian (`space_decor`)**: Mô hình Cáo trang trí, Mô hình Phi hành gia không gian, Tượng Cyber Wolf Low-Poly, Đèn bàn LED Hexagon tổ ong.
- **Lý do**: Khớp 100% với layout Figma Node 2261:5595 (Frame 253 có độ rộng 1116px chia làm 4 cards 264px, gap 20px). Thẻ đầu tiên của mỗi danh mục sẽ nhận huy hiệu `Best choice`.

### 4. Cung cấp 7 Thông số Kỹ thuật In 3D (`specifications`) cho PDP
- **Lựa chọn**:
  - Trong `FigmaProductsSeeder`, lưu trữ cấu trúc thông số kỹ thuật chuẩn vào trường `content` (HTML) và cấu hình mảng 7 thông số kỹ thuật trong phương thức `transformDetails()` của `app/Models/Product/Product.php`.
  - Bảng `SpecTable.vue` nhận mảng `specs` gồm 7 cặp `label` và `value`:
    1. Công nghệ in (`FDM High-Precision` / `SLA 8K` / `SLS`)
    2. Vật liệu (`Nhựa PLA Matte` / `PETG-CF` / `Resin 8K` / `Nylon PA12`)
    3. Kích thước mẫu (theo kích thước thực tế từng sản phẩm)
    4. Chiều cao lớp in (`0.16 mm` / `0.025 mm` / `0.20 mm`)
    5. Đầu phun (`0.40 mm Hardened Steel` / `Quang hóa SLA` / `Laser CO2`)
    6. Mật độ ruột (`20% Gyroid` / `Đặc 100%` / `15% Grid`)
    7. Vật liệu hỗ trợ (`Không cần` / `Support dễ tách` / `Zero-support`)
- **Lý do**: Khớp chính xác với thiết kế Figma Node 2358:3071 / 2390:5393, thay thế hoàn toàn dòng chữ "Thông số kỹ thuật đang được cập nhật".

### 5. Thay thế Hoàn toàn ProductSeeder trong DatabaseSeeder
- **Lựa chọn**: Trong `database/seeders/DatabaseSeeder.php`, xóa bỏ `ProductSeeder::class` và thay thế bằng `FigmaProductsSeeder::class`.
- **Lý do**: Ngăn chặn vĩnh viễn nguy cơ developer hoặc pipeline CI/CD chạy `php artisan db:seed` làm kích hoạt lệnh `truncate()` phá hủy database sản phẩm in 3D.

## Risks / Trade-offs

- **[Rủi ro Cache cũ]**: Dữ liệu sản phẩm cũ có thể bị lưu trong Redis/File Cache (`products_index_*`, `home_page_data_*`, `product_show_*`), khiến thay đổi không hiển thị ngay sau khi chạy seeder.
  - *Biện pháp giảm thiểu*: Tự động xóa sạch các khóa cache liên quan đến sản phẩm và trang chủ ngay cuối phương thức `run()` của `FigmaProductsSeeder`, đồng thời chạy `php artisan cache:clear` trong quy trình triển khai.
- **[Rủi ro trùng lặp bản ghi nếu slug bị thay đổi]**: Nếu một sản phẩm đổi slug, `updateOrInsert` theo slug có thể tạo thêm bản ghi mới.
  - *Biện pháp giảm thiểu*: Đối với Model Cáo ID 1, khóa tìm kiếm ưu tiên theo `target_id = 1`. Đối với 20 sản phẩm còn lại, khóa tìm kiếm kết hợp giữa `target_id` (nếu có) và `slug` chuẩn trong `product_translations`.
- **[Rủi ro thiếu ảnh trên môi trường local]**: Các file ảnh `/assets/images/products/figma-product-*.png` hoặc `/images/pdp/*` có thể chưa tồn tại đầy đủ trên môi trường mới.
  - *Biện pháp giảm thiểu*: Các component Frontend (`ProductCard.vue`, `RelatedProducts.vue`, `ProductsSection.vue`) đều đã tích hợp hàm `handleImageError` và fallback sang ảnh placeholder an toàn.

## Migration Plan

1. **Chuẩn bị mã nguồn Seeder**:
   - Cập nhật `database/seeders/FigmaProductsSeeder.php` hoàn thiện 21 sản phẩm, gán `is_featured = 1` cho 4 item/danh mục, đảm bảo `price` và `compare_at_price` là `null`, bổ sung 7 thông số kỹ thuật in 3D.
2. **Cập nhật DatabaseSeeder**:
   - Thay thế `ProductSeeder::class` bằng `FigmaProductsSeeder::class` trong `database/seeders/DatabaseSeeder.php`.
3. **Cập nhật Model Product**:
   - Bổ sung accessor hoặc key `specifications` trong `transformDetails()` tại `app/Models/Product/Product.php` để truyền dữ liệu xuống `SpecTable.vue`.
4. **Thực thi Seed & Xóa Cache**:
   - Chạy lệnh `php artisan db:seed --class=FigmaProductsSeeder`.
   - Chạy lệnh `php artisan cache:clear`.
5. **Kiểm tra & Nghiệm thu (Verification)**:
   - Chạy PHPUnit test kiểm tra API/Model sản phẩm.
   - Kiểm tra trực quan bằng Playwright Visual QA: Trang chủ (4 cards/hàng), Trang danh mục (lọc 16 SP), Trang PDP Cáo ID 1 (SpecTable 7 hàng và 4 Related Products).
