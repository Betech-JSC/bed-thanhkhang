# Tasks: Đồng bộ 21 Sản phẩm Figma vào CMS Database (sync-figma-products-to-cms)

## 1. Chuẩn hóa FigmaProductsSeeder & Model Product

- [x] 1.1 Hoàn thiện dữ liệu 21 sản phẩm và 4 danh mục trong `database/seeders/FigmaProductsSeeder.php` theo phương thức idempotent `updateOrInsert`, đảm bảo trường `price` và `compare_at_price` luôn giữ giá trị `null`, gán cờ `is_featured = 1` cho đúng 4 sản phẩm đại diện của từng danh mục, và xác minh cú pháp bằng `php -l database/seeders/FigmaProductsSeeder.php`.
- [x] 1.2 Bổ sung cấu trúc 7 thông số kỹ thuật in 3D (`specifications`) cho từng sản phẩm trong `FigmaProductsSeeder.php` và cập nhật phương thức `transformDetails()` trong `app/Models/Product/Product.php` để trả về mảng `specifications` cho `SpecTable.vue`, xác minh bằng `php artisan tinker --execute="echo json_encode(App\Models\Product\Product::first()?->transformDetails()['specifications'] ?? []);"`.
- [x] 1.3 Cập nhật logic liên kết chéo sản phẩm liên quan (`related_products`) trong `FigmaProductsSeeder.php`, đảm bảo Model Cáo ID 1 liên kết chính xác với 4 sản phẩm liên quan theo thiết kế Figma, xác minh không có lỗi cú pháp qua PHP CLI.

## 2. Thay thế Seeder trong DatabaseSeeder & Thực thi Seed

- [x] 2.1 Cập nhật `database/seeders/DatabaseSeeder.php`: Loại bỏ hoàn toàn `ProductSeeder::class` (chứa logic truncate và dữ liệu logistics cũ), thay thế bằng `FigmaProductsSeeder::class`, và xác minh cú pháp bằng `php -l database/seeders/DatabaseSeeder.php`.
- [x] 2.2 Thực thi lệnh seed dữ liệu `php artisan db:seed --class=FigmaProductsSeeder` và xác minh terminal output trả về `Database seeding completed successfully.` mà không có lỗi khóa ngoại hoặc duplicate key.
- [x] 2.3 Xóa toàn bộ cache ứng dụng bằng `php artisan cache:clear` và kiểm tra qua tinker xác nhận có đủ 21 sản phẩm active với trường `price` rỗng (`null`).

## 3. Kiểm thử Backend & Frontend Integration

- [x] 3.1 Chạy bộ kiểm thử tự động PHPUnit cho sản phẩm và danh mục bằng `php artisan test --filter=Product` để xác minh các model, quan hệ danh mục và quan hệ sản phẩm liên quan hoạt động chính xác.
- [x] 3.2 Kiểm tra dữ liệu trả về của route `/san-pham` và `/san-pham/mo-hinh-cao-trang-tri` bằng HTTP request hoặc tinker để xác nhận payload chứa đầy đủ 16 catalog products và 7 hàng specifications.

## 4. Kiểm thử Trực quan Playwright Visual QA (Evidence-Based)

- [x] 4.1 Sử dụng Playwright kiểm tra Trang chủ (`/`): Chụp ảnh màn hình khu vực "SẢN PHẨM NỔI BẬT" xác nhận mỗi hàng danh mục hiển thị đầy đủ 4 cards theo khung Figma 1116px kèm huy hiệu `Best choice` trên thẻ đầu tiên.
- [x] 4.2 Sử dụng Playwright kiểm tra Trang danh mục sản phẩm (`/san-pham`): Chụp ảnh màn hình xác nhận hiển thị đầy đủ 16 sản phẩm catalog, bộ lọc danh mục và bộ lọc nhu cầu lọc chính xác.
- [x] 4.3 Sử dụng Playwright kiểm tra Trang chi tiết Model Cáo ID 1 (`/san-pham/mo-hinh-cao-trang-tri`): Chụp ảnh màn hình xác nhận bảng `SpecTable` hiển thị đủ 7 hàng thông số in 3D, câu chuyện sản phẩm, gallery ảnh và 4 sản phẩm liên quan ở chân trang.
