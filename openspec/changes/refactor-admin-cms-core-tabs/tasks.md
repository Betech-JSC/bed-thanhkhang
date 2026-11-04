# Tasks: Refactor Admin CMS Core Tabs & Streamlined Sidebar

- [x] 1. Khởi tạo & Cấu trúc OpenSpec
  - [x] 1.1 Khởi tạo change `refactor-admin-cms-core-tabs` với đầy đủ 4 tài liệu (`proposal.md`, `specs/admin-core-tabs/spec.md`, `design.md`, `tasks.md`).

- [x] 2. Tinh giản Sidebar Admin CMS (`SidebarMain.vue` & `SidebarItems.vue`)
  - [x] 2.1 Cập nhật `resources/Backend/js/Components/SidebarMain.vue` chia thành 4 phân khu logic (Bảng điều khiển, Vận hành xưởng in 3D, Nội dung & Thương hiệu, Hệ thống & Cấu hình).
  - [x] 2.2 Loại bỏ hoàn toàn các mục menu thừa (Đơn hàng, Đội ngũ, FAQs, Hỗ trợ & Chính sách).
  - [x] 2.3 Điều chỉnh `packages/bed-package-essentials/core/resources/Backend/js/Components/Sidebar/SidebarItems.vue` để tránh lặp các menu Dashboard và Cấu hình.

- [x] 3. Hoàn thiện Tab 1: Banner & Hero Slider
  - [x] 3.1 Tạo migration bổ sung các trường subtitle và CTA vào `slider_translations` (hoặc cấu hình tương thích model Slider).
  - [x] 3.2 Cập nhật `JamstackVietnam\Slider\Models\Slider` và `SliderTranslation` hỗ trợ các trường mới.
  - [x] 3.3 Mở rộng `resources/Backend/js/Pages/Sliders/Form.vue` thêm các trường `subtitle`, `primary_cta_text`, `primary_cta_link`, `secondary_cta_text`, `secondary_cta_link`.
  - [x] 3.4 Cập nhật `app/Http/Controllers/Frontend/HomeController.php`, `resources/Frontend/js/Pages/Home/Index.vue` và `HeroSection.vue` để kết nối dữ liệu banner động với fallback an toàn.

- [x] 4. Hoàn thiện Tab 2: Quản lý Sản phẩm in 3D
  - [x] 4.1 Tạo migration bổ sung `price`, `compare_at_price`, `print_technology`, `material`, `lead_time` vào bảng `products`.
  - [x] 4.2 Cập nhật `app/Models/Product/Product.php` thêm `$fillable`, `transform()` và hook `static::saved` gọi `$model->saveCategories($model)`.
  - [x] 4.3 Cập nhật `app/Models/Product/ProductCategory.php` bổ sung method helper `getCategories` cho form selector.
  - [x] 4.4 Mở rộng `resources/Backend/js/Pages/Products/Products/Form.vue` thêm chọn danh mục (`categories`), giá bán/tham khảo và cụm thông số kỹ thuật in 3D.

- [x] 5. Hoàn thiện Tab 3: Danh mục & Bộ lọc Sản phẩm
  - [x] 5.1 Cập nhật `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue` sử dụng prop `categories` từ backend truyền xuống thay vì hardcode.
  - [x] 5.2 Cập nhật `resources/Frontend/js/Pages/Product/Index.vue` khai báo prop `categories` và truyền vào `ProductFilter`.

- [x] 6. Hoàn thiện Tab 4: Dịch vụ In 3D
  - [x] 6.1 Sửa trường `content` trong `resources/Backend/js/Pages/Services/Form.vue` từ `text` sang `richtext` (CKEditor).
  - [x] 6.2 Cập nhật `app/Http/Controllers/Frontend/HomeController.php` nạp danh sách `services` và truyền sang view trang chủ.
  - [x] 6.3 Cập nhật `resources/Frontend/js/Pages/Home/Index.vue` và `ServicesSection.vue` để hiển thị danh sách dịch vụ động.

- [x] 7. Hoàn thiện Tab 5: Báo giá & Yêu cầu in 3D
  - [x] 7.1 Cập nhật danh sách trạng thái xử lý chuẩn xưởng in 3D trong `app/Models/Contact.php` (Mới nhận, Đang kiểm tra file CAD, Đã báo giá, Đang gia công/In, Hoàn tất, Hủy/Spam).
  - [x] 7.2 Tạo migration bổ sung `internal_notes` và `estimated_price` vào bảng `contacts` và cập nhật model `Contact.php`.
  - [x] 7.3 Bổ sung ô nhập "Ghi chú nội bộ (Internal Notes)" và "Giá trị báo giá tạm tính (VNĐ)" vào `resources/Backend/js/Pages/FormContacts/Form.vue`.

- [x] 8. Xác minh & Kiểm thử Toàn diện
  - [x] 8.1 Chạy `yarn backend:build` và đảm bảo exit code 0.
  - [x] 8.2 Chạy `yarn frontend:build` và đảm bảo exit code 0.
  - [x] 8.3 Chạy `php artisan test tests/Feature/ContactFormTest.php` và đảm bảo 100% test pass.
  - [x] 8.4 Cập nhật 100% `[x]` vào `tasks.md`.
