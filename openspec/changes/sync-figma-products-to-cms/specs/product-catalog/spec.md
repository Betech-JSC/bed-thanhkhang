# Spec Delta: Đồng bộ Sản phẩm Figma vào CMS (product-catalog)

## ADDED Requirements

### Requirement: Đồng bộ Idempotent 21 sản phẩm Figma vào CMS Database
Hệ thống SHALL cung cấp seeder `FigmaProductsSeeder` để nạp và đồng bộ 21 sản phẩm chuẩn từ thiết kế Figma (Node 2261:5076) cùng 4 danh mục chính (`space_decor`, `keychains`, `stationery`, `custom`) vào cơ sở dữ liệu CMS theo phương thức Idempotent (`updateOrInsert`), đảm bảo an toàn tuyệt đối không sử dụng lệnh `truncate` hay `drop` bảng:
1. Danh sách sản phẩm bao gồm: 1 sản phẩm Model Cáo ID 1 (`mo-hinh-cao-trang-tri`), 16 sản phẩm catalog chính từ Figma, và 4 sản phẩm liên quan (`related_products`).
2. Mọi sản phẩm SHALL được khởi tạo với trạng thái kích hoạt (`status = 'ACTIVE'`), hỗ trợ đầy đủ 2 ngôn ngữ (`vi` và `en`) trong `product_translations`.
3. Tuân thủ nghiêm ngặt chỉ thị người dùng: Tất cả sản phẩm SHALL có trường giá niêm yết và giá so sánh ở trạng thái rỗng (`price = null`, `compare_at_price = null`). Hệ thống không được tự ý gán giá số giả định.
4. Hệ thống `DatabaseSeeder.php` SHALL thay thế hoàn toàn `ProductSeeder::class` cũ bằng `FigmaProductsSeeder::class` để ngăn chặn hành vi xóa sạch dữ liệu sản phẩm khi chạy seed.

#### Scenario: Chạy seeder nạp 21 sản phẩm Figma an toàn không truncate
- **WHEN** Quản trị viên hoặc lệnh triển khai thực thi `php artisan db:seed --class=FigmaProductsSeeder` hoặc `php artisan db:seed`
- **THEN** Hệ thống cập nhật hoặc thêm mới 21 sản phẩm cùng 4 danh mục vào database mà không phát sinh lỗi trùng khóa hay xóa sạch bảng dữ liệu hiện có, đồng thời các trường `price` và `compare_at_price` của 21 sản phẩm đều nhận giá trị `NULL`.

### Requirement: Hiển thị 4 cards trên mỗi hàng danh mục tại Trang chủ
Hệ thống SHALL đảm bảo mỗi hàng danh mục sản phẩm trên Trang chủ (`ProductsSection.vue`) hiển thị tối thiểu và đủ 4 thẻ sản phẩm đại diện theo đúng khung Figma Node 2261:5595 (Frame 253: 264x419px, gap 20px, tổng độ rộng 1116px):
1. `FigmaProductsSeeder` SHALL gán cờ `is_featured = 1` cho đúng 4 sản phẩm tiêu biểu thuộc từng danh mục:
   - Danh mục Móc khoá & Phụ kiện (`keychains`): 4 sản phẩm (`moc-khoa-trai-tim-co-khi`, `moc-khoa-turbine-phan-luc`, `moc-khoa-dong-co-v8-piston`, `moc-khoa-vo-lang-f1-quick-release`).
   - Danh mục Trang trí & Đồ dùng (`stationery`): 4 sản phẩm (`gia-do-tai-nghe-voronoi`, `hop-dung-danh-thiep-cyberpunk`, `den-ngu-mat-trang-lithophane`, `mo-hinh-tau-vu-tru-discovery`).
   - Danh mục Gia công theo yêu cầu (`custom`): 4 sản phẩm (`iron-man-mark-85-resin-8k`, `khung-vo-robot-carbon-fiber`, `cum-banh-rang-hanh-tinh`, `khop-noi-cau-bionic-sls`).
   - Danh mục Trang trí không gian (`space_decor`): 4 sản phẩm (`mo-hinh-cao-trang-tri`, `mo-hinh-phi-hanh-gia`, `tuong-cyber-wolf-low-poly`, `den-ban-led-hexagon-to-ong`).
2. Component `ProductsSection.vue` và `HomeController.php` SHALL truy vấn và hiển thị đủ 4 thẻ sản phẩm nổi bật trên đường ray trượt của mỗi danh mục, với thẻ đầu tiên hiển thị huy hiệu `Best choice`.

#### Scenario: Kiểm tra số lượng thẻ hiển thị trên Trang chủ
- **WHEN** Người dùng truy cập Trang chủ (`/` hoặc `/en`)
- **THEN** Mỗi hàng danh mục sản phẩm trong khu vực "SẢN PHẨM NỔI BẬT" hiển thị chính xác 4 thẻ sản phẩm với hình ảnh, tiêu đề, mô tả và nút điều hướng mượt mà, không bị khuyết thẻ hoặc lệch layout.

### Requirement: Trang danh sách sản phẩm (/san-pham) lọc đúng danh mục và hiển thị đủ 16 sản phẩm
Hệ thống SHALL đồng bộ danh mục CMS và 16 sản phẩm catalog chính từ Figma để trang danh sách `/san-pham` và `/en/products` hoạt động chính xác:
1. Sidebar bộ lọc (`ProductFilter.vue`) SHALL hiển thị danh mục động lấy từ CMS DB: "Tất cả sản phẩm" và các danh mục "Móc khoá & Phụ kiện", "Trang trí & Đồ dùng", "Gia công theo yêu cầu", "Trang trí không gian".
2. Khi chọn "Tất cả sản phẩm", trang SHALL hiển thị đầy đủ 16 sản phẩm catalog in 3D theo lưới 3 cột.
3. Khi người dùng bấm chọn một danh mục cụ thể hoặc chọn nhiều nhóm nhu cầu, hệ thống SHALL lọc và hiển thị chính xác các sản phẩm tương ứng mà không gặp lỗi dữ liệu rỗng.

#### Scenario: Lọc sản phẩm theo danh mục trên trang danh mục
- **WHEN** Người dùng truy cập `/san-pham` và chọn tab danh mục "Móc khoá & Phụ kiện"
- **THEN** Lưới sản phẩm chỉ hiển thị các sản phẩm thuộc danh mục móc khoá, số lượng đếm trên breadcrumb và thanh toolbar cập nhật chính xác tương ứng.

### Requirement: Trang chi tiết sản phẩm (/san-pham/mo-hinh-cao-trang-tri) hiển thị đầy đủ thông số in 3D, câu chuyện và 4 sản phẩm liên quan
Trang chi tiết sản phẩm (PDP) của Model Cáo ID 1 (`/san-pham/mo-hinh-cao-trang-tri`) và các sản phẩm khác trong catalog SHALL hiển thị đầy đủ nội dung kỹ thuật in 3D theo đúng thiết kế Figma (Node 2358:2979):
1. **Thông số in 3D (Specifications)**: Bảng `SpecTable.vue` trong tab "Tổng quan" SHALL hiển thị đầy đủ 7 hàng thông số kỹ thuật in 3D chuẩn xác:
   - Công nghệ in: FDM High-Precision (hoặc SLA 8K theo từng sản phẩm).
   - Vật liệu: Nhựa PLA Matte cao cấp (hoặc PETG-CF, 8K Resin).
   - Kích thước mẫu: 120 x 85 x 140 mm.
   - Chiều cao lớp in: 0.16 mm.
   - Đầu phun: 0.40 mm Hardened Steel.
   - Mật độ ruột: 20% Gyroid chịu lực.
   - Vật liệu hỗ trợ: Không cần / Dễ tách.
2. **Câu chuyện & Giới thiệu**: Hiển thị tiêu đề câu chuyện, đoạn giới thiệu ngắn và các đặc điểm nổi bật từ `description` và `content` của CMS.
3. **Bộ sưu tập hình ảnh (Gallery)**: Khối `ProductGallery.vue` hiển thị ảnh chính và hàng thumbnail xem trước sắc nét, không bị lỗi vỡ ảnh.
4. **Sản phẩm liên quan (Related Products)**: Khối `RelatedProducts.vue` ở cuối trang hiển thị chính xác 4 sản phẩm gợi ý được liên kết trong database CMS (`mo-hinh-phi-hanh-gia`, `chau-cay-da-giac-mini`, `de-do-dien-thoai-mecha`, `robot-khop-cu-dong`), với đầy đủ thumbnail và liên kết chuyển trang mượt mà.

#### Scenario: Truy cập trang chi tiết Model Cáo ID 1
- **WHEN** Người dùng truy cập đường dẫn `/san-pham/mo-hinh-cao-trang-tri` (hoặc `/en/products/mo-hinh-cao-trang-tri`)
- **THEN** Trang PDP hiển thị đầy đủ tiêu đề "Mô hình Cáo trang trí", bảng thông số kỹ thuật 7 hàng trong SpecTable, gallery ảnh sắc nét, nút "Nhận tư vấn sản phẩm", và khối 4 sản phẩm liên quan ở chân trang với đầy đủ dữ liệu động từ CMS.
