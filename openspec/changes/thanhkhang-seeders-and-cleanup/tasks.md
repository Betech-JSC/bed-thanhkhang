# Tasks: Triển khai Seeders chuẩn B2B Thành Khang Tecsat & Dọn dẹp tàn dư CMS/Backend

## 1. Triển khai 3 Seeder chuẩn B2B Thành Khang Tecsat

- [x] 1.1 Tạo `database/seeders/ThanhKhangSettingSeeder.php`: Cập nhật thông tin công ty Thành Khang Tecsat (MST 107621881, Hotline/Zalo 0967732665, Email info@dvktthanhkhang.vn, Trụ sở TNR 54A Nguyễn Chí Thanh, VPGD NTT23 Thống Nhất Complex, 3 lĩnh vực hoạt động, quy trình 5 bước B2B) và gọi `Config::clearBrandCaches()`.
- [x] 1.2 Tạo `database/seeders/ThanhKhangCatalogSeeder.php`: Tạo cây danh mục 3 tầng (5 Root -> 8 Sub -> 16 Leaf) và nạp hơn 100 mã SKU sản phẩm B2B (TL133, 16A-2F, LT40-1, 40SR, 40HP, M20, FV40...) kèm thông số kỹ thuật JSON, `price = null`, gắn quan hệ `product_ref_categories` và gọi `ProductCategory::clearCategoryCaches()`.
- [x] 1.3 Tạo `database/seeders/ThanhKhangPolicySeeder.php`: Tạo đủ 6 chính sách dịch vụ B2B chuẩn Sitemap (bán hàng, thanh toán, vận chuyển, đổi trả, bảo hành, bảo mật) song ngữ VI/EN và gọi `Policy::clearPolicyCaches()`.

## 2. Dọn dẹp Backend, Config & Seeders cũ

- [x] 2.1 Cập nhật `database/seeders/DatabaseSeeder.php`: Gỡ bỏ seeder mồ côi (`SchoolSeeder`, `PointPackageSeeder`, `SynologyResourceSeeder`, `UserRankSeeder`, `AgencySeeder`, `FigmaProductsSeeder`, `ServiceContentSeeder` và fake data course/resource/prize/project) và đăng ký gọi 3 seeder Thành Khang mới.
- [x] 2.2 Vô hiệu hóa/dọn dẹp các console command mồ côi: `app/Console/Commands/SyncCourseClassesCommand.php` và `SyncSynologyLinks.php`.
- [x] 2.3 Cập nhật `config/app.php`: Đổi fallback name thành `'THANH KHANG Tecsat'`.
- [x] 2.4 Cập nhật `config/services.php`: Gỡ bỏ cấu hình thừa `synology`.
- [x] 2.5 Cập nhật `app/Models/Config.php`: Xóa method thừa `getLuxiBrandSettings()`, cập nhật fallback `normalizeGeneralSettings()` và `getProcessSteps()` sang thông tin Thành Khang Tecsat và quy trình 5 bước B2B.
- [x] 2.6 Dọn dẹp hardcoded FDM/Resin/in 3D: Cập nhật `app/Models/Service.php`, `app/Models/Post/Post.php` và các SEO fallback trong `HistoryController.php`, `ProductController.php`, `PolicyController.php`, `HomeController.php`, `ProcessController.php`, `ServiceController.php`, `AgencyController.php`, `PostController.php`.

## 3. Dọn dẹp giao diện Admin CMS (`resources/Backend/js/`)

- [x] 3.1 Cập nhật `resources/Backend/js/Components/SidebarMain.vue`: Đổi `🟢 VẬN HÀNH XƯỞNG IN 3D` thành `QUẢN LÝ SẢN PHẨM & BÁO GIÁ`, `Báo giá & Yêu cầu in 3D` thành `Yêu cầu báo giá kỹ thuật (RFQ)`.
- [x] 3.2 Cập nhật `resources/Backend/js/Pages/Dashboard.vue`: Đổi các nhãn HH Maker 3D sang Thành Khang Tecsat B2B (Tiêu đề, chú thích, biểu đồ RFQ & cơ cấu phụ tùng).
- [x] 3.3 Cập nhật `resources/Backend/js/Pages/Products/Products/Form.vue`: Chuẩn hóa tab thông số kỹ thuật sang đặc tính cơ khí công nghiệp thay cho các nhãn FDM/Resin cứng.
- [x] 3.4 Cập nhật `resources/Backend/js/Pages/UserGuide.vue` & `FormContacts/Form.vue` & `Sliders/Form.vue`: Loại bỏ toàn bộ nhãn và tiêu đề cũ.

## 4. Verification & Testing

- [x] 4.1 Chạy thực thi 3 Seeders: `php artisan db:seed --class=ThanhKhangSettingSeeder`, `php artisan db:seed --class=ThanhKhangPolicySeeder`, `php artisan db:seed --class=ThanhKhangCatalogSeeder` và toàn bộ qua `DatabaseSeeder`.
- [x] 4.2 Kiểm tra CSDL qua script PHP: Xác thực số lượng danh mục (40), sản phẩm (133 sản phẩm, 115 B2B TK SKUs), thông số kỹ thuật, và settings đã được lưu đúng và đầy đủ.
- [x] 4.3 Biên dịch Frontend / Backend: Chạy `npm run backend:build` và `npm run frontend:build` đảm bảo không có bất kỳ lỗi cú pháp Vue / Vite / SSR nào (exit code 0).
- [x] 4.4 Báo cáo kết quả chi tiết kèm bằng chứng execution terminal.
