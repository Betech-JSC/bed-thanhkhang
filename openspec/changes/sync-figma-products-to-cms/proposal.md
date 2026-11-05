# Proposal: Đồng bộ 21 Sản phẩm Figma vào CMS Database (sync-figma-products-to-cms)

## Why

Hiện tại, cơ sở dữ liệu CMS của HH Maker vẫn còn tồn tại tàn dư của `ProductSeeder` cũ (dữ liệu dịch vụ logistics mẫu kế thừa từ mã nguồn tiền nhiệm như Konoike Logistics, Alpha Trans, Vận tải đường bộ...). Nguy hiểm hơn, mỗi lần chạy `DatabaseSeeder`, lệnh `truncate()` đối với các bảng `products`, `product_categories`, `product_translations` lại bị kích hoạt, làm xóa sạch toàn bộ dữ liệu mẫu in 3D thực tế của HH Maker.

Đồng thời, 21 sản phẩm mẫu chuẩn từ thiết kế Figma (Node 2261:5076) chưa được đồng bộ toàn diện vào cơ sở dữ liệu CMS, gây ra các vấn đề nghiêm trọng:
1. **Trang chủ bị khuyết thiếu thẻ**: Mỗi hàng danh mục chỉ hiển thị 1-2 thẻ thay vì 4 thẻ/hàng chuẩn Figma Node 2261:5595 do chưa đủ sản phẩm mang cờ `is_featured = 1`.
2. **Trang danh mục `/san-pham` chưa nhận diện đủ danh mục CMS**: Dữ liệu chưa liên kết đầy đủ danh mục đa ngôn ngữ (VI/EN) cho 16 sản phẩm catalog.
3. **Trang chi tiết sản phẩm (PDP) Model Cáo ID 1**: Thiếu thông số in 3D (`specifications`) chuẩn 7 hàng của Figma trong bảng `SpecTable`, và danh sách 4 sản phẩm liên quan (`related_products`) chưa được liên kết chặt chẽ qua bảng pivot CMS.

**Ràng buộc cốt lõi từ người dùng**:
"Không bổ sung giá niêm yết (Phần giá này sau này mình bổ sung sau nếu khách cần)". Toàn bộ sản phẩm phải duy trì `price = null` và `compare_at_price = null`, giữ nguyên luồng tư vấn và báo giá giải pháp in 3D tùy chỉnh theo yêu cầu.

Vì vậy, change này triển khai quy trình đồng bộ dữ liệu Idempotent an toàn tuyệt đối (Zero Data Loss, Zero Truncate), đưa trọn vẹn 21 sản phẩm Figma vào CMS, loại bỏ hoàn toàn `ProductSeeder` cũ trong `DatabaseSeeder`, kích hoạt hiển thị 4 cards/hàng trên Trang chủ và hoàn thiện thông số in 3D cho PDP.

## What Changes

- **Đồng bộ Idempotent 21 sản phẩm Figma vào CMS**:
  - Chuẩn hóa `FigmaProductsSeeder` sử dụng cơ chế `updateOrInsert` cho 4 danh mục chính (`space_decor`, `keychains`, `stationery`, `custom`) và 21 sản phẩm (1 mô hình Cáo ID 1 + 16 sản phẩm catalog Figma + 4 sản phẩm liên quan).
  - Không chạy bất kỳ lệnh `truncate` hay `drop` nào, bảo toàn 100% dữ liệu khác trong hệ thống.
- **Ràng buộc Giá niêm yết (Pricing Constraint)**:
  - Tuyệt đối không thêm giá bán cố định: cả hai trường `price` và `compare_at_price` đều để `null`/trống.
  - Trên giao diện Frontend, toàn bộ nút hành động duy trì dẫn đến luồng "Nhận tư vấn sản phẩm" hoặc modal yêu cầu báo giá kỹ thuật.
- **Thay thế hoàn toàn ProductSeeder trong DatabaseSeeder**:
  - Gỡ bỏ lệnh gọi `ProductSeeder::class` (chứa logic truncate bảng và dữ liệu logistics cũ) khỏi `DatabaseSeeder.php`.
  - Thay thế bằng `FigmaProductsSeeder::class` để đảm bảo khi chạy `php artisan db:seed`, dữ liệu sản phẩm in 3D được cập nhật mượt mà và an toàn.
- **Cấu hình 4 sản phẩm nổi bật/danh mục (`is_featured = 1`)**:
  - Gán cờ `is_featured = 1` cho đúng 4 sản phẩm đại diện tiêu biểu của từng danh mục.
  - Đảm bảo hàng băng chuyền sản phẩm trên Trang chủ (`ProductsSection.vue`) render trọn vẹn 4 cards/hàng theo đúng khung Figma 1116px (Frame 253).
- **Khắc phục liên kết danh mục & sản phẩm liên quan cho Model Cáo ID 1**:
  - Đảm bảo Model Cáo ID 1 (`mo-hinh-cao-trang-tri`) liên kết chính xác với danh mục `Trang trí không gian` (`space_decor`).
  - Thiết lập liên kết chéo 4 sản phẩm gợi ý liên quan (`related_products`) trong cơ sở dữ liệu: Phi hành gia, Chậu cây đa giác, Đế đỡ điện thoại Mecha, Robot khớp cử động.
- **Bổ sung specifications (thông số in 3D) cho trang PDP**:
  - Cung cấp cấu trúc 7 thông số in 3D tiêu chuẩn Figma (Công nghệ in, Vật liệu, Kích thước mẫu, Chiều cao lớp in, Đầu phun, Mật độ ruột, Vật liệu hỗ trợ) vào dữ liệu trả về của Model `Product` (`transformDetails()`) để bảng `SpecTable.vue` hiển thị đầy đủ.

## Capabilities

### Modified Capabilities
- `product-catalog`: Cập nhật yêu cầu đồng bộ 21 sản phẩm in 3D Figma vào CMS DB thông qua Idempotent Seeder (không giá niêm yết), đảm bảo Trang chủ render đủ 4 cards/hàng với cờ `is_featured = 1`, trang danh mục `/san-pham` lọc đúng 16 sản phẩm catalog chính, và trang PDP hiển thị đầy đủ 7 thông số kỹ thuật in 3D cùng 4 sản phẩm liên quan.

## Impact

- **Backend Seeders & Models**:
  - `database/seeders/FigmaProductsSeeder.php`: Hoàn thiện logic `updateOrInsert`, cấu hình `is_featured = 1` cho 4 item/danh mục, giữ `price` / `compare_at_price` là `null`, bổ sung mảng `specifications` cho PDP.
  - `database/seeders/DatabaseSeeder.php`: Loại bỏ `ProductSeeder::class`, đăng ký `FigmaProductsSeeder::class`.
  - `app/Models/Product/Product.php`: Cung cấp trường `specifications` trong `transformDetails()` phục vụ `SpecTable.vue`.
  - `app/Http/Controllers/Frontend/ProductController.php`: Xóa cache sản phẩm sau khi seed để dữ liệu cập nhật tức thì.
- **Frontend Pages & Components**:
  - `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`: Render đủ 4 cards/hàng danh mục trên Trang chủ.
  - `resources/Frontend/js/Pages/Product/Index.vue`: Hiển thị danh mục CMS động và 16 sản phẩm catalog.
  - `resources/Frontend/js/Pages/Product/Show.vue`: Render thông số in 3D đầy đủ trên `SpecTable` và 4 cards trên `RelatedProducts`.
- **Database Tables**: Ghi đè hoặc thêm mới an toàn (idempotent) vào `products`, `product_translations`, `product_categories`, `product_category_translations`, `product_ref_categories`, `related_products`. Tuyệt đối không xóa bảng hay làm mất dữ liệu.
