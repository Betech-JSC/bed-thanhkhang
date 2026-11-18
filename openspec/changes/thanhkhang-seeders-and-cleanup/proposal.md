# Proposal: Triển khai Seeders chuẩn B2B Thành Khang Tecsat & Dọn dẹp tàn dư CMS/Backend

## Why

Dự án đang chuyển dịch toàn diện sang thương hiệu **Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat** (cung ứng thiết bị phụ tùng thang cuốn, xích tải công nghiệp, băng tải hành lý sân bay, linh kiện đóng gói và giải pháp tiết kiệm điện BLDC). Tuy nhiên, cơ sở dữ liệu và mã nguồn Backend/CMS vẫn còn tồn đọng nhiều dữ liệu mẫu và cấu hình cứng của các thương hiệu cũ (HH Maker xưởng in 3D, Funkystyle, Luxi):
1. **Dữ liệu Seeders**: Các seeder hiện tại (`FigmaProductsSeeder`, `PolicySeeder`, `ProcessContentSeeder`, `DatabaseSeeder`) chứa dữ liệu mô hình in 3D FDM/Resin, linh kiện đồ chơi decor và các trường học/tài nguyên Funkystyle mồ côi.
2. **Cấu hình & Fallback Brand**: `config/app.php`, `config/services.php`, `app/Models/Config.php`, `app/Models/Service.php`, `app/Models/Post/Post.php` và các Frontend Controller (`HistoryController`, `ProductController`, `PolicyController`) còn hardcode tên "HH Maker", hotline cũ `0906636425`, email cũ `info@hhmaker.vn` và địa chỉ cũ ở TP.HCM.
3. **Giao diện Admin CMS**: Thanh điều hướng `SidebarMain.vue`, bảng điều khiển `Dashboard.vue`, và biểu mẫu sản phẩm `Form.vue` vẫn hiển thị nhãn "VẬN HÀNH XƯỞNG IN 3D", "Báo giá in 3D", và thông số in FDM/Resin.

Việc triển khai bộ 3 Seeders chuẩn B2B (`ThanhKhangSettingSeeder`, `ThanhKhangCatalogSeeder`, `ThanhKhangPolicySeeder`) kết hợp dọn dẹp sạch sẽ tàn dư Backend/CMS sẽ đồng bộ hoàn toàn dữ liệu động, ngăn ngừa lệch dữ liệu giữa 2 dự án, và tạo nền tảng vững chắc cho hệ thống quản trị và giao diện B2B chuyên nghiệp.

## What Changes

### 1. Triển khai 3 Seeder chuẩn B2B (Idempotent updateOrCreate / updateOrInsert)
- **`database/seeders/ThanhKhangSettingSeeder.php`**:
  - Cập nhật toàn bộ thông tin nhận diện thương hiệu công ty Thành Khang Tecsat:
    + Mã số thuế: `107621881`
    + Hotline / Zalo: `0967732665`
    + Email: `info@dvktthanhkhang.vn`
    + Trụ sở chính: Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội.
    + VPGD: NTT23, Khu Đô Thị Thống Nhất Complex, 82 Nguyễn Tuân, Phường Thanh Xuân Trung, Quận Thanh Xuân, Hà Nội.
    + 3 lĩnh vực hoạt động chủ đạo: Phụ tùng thang cuốn & thang máy; Hệ thống & linh kiện băng tải/xích công nghiệp/hành lý logistics; Giải pháp tiết kiệm điện công nghiệp / Quạt trần công nghiệp BLDC.
    + Quy trình 5 bước đặt hàng B2B chuẩn công nghiệp (Tiếp nhận yêu cầu kỹ thuật -> Khảo sát thông số & RFQ -> Xác nhận đơn hàng & Hợp đồng -> Kiểm định QC & Đóng gói -> Bàn giao, lắp đặt & Bảo hành).
  - Tự động xóa cache cấu hình qua `Config::clearBrandCaches()`.

- **`database/seeders/ThanhKhangCatalogSeeder.php`**:
  - Kiến tạo cây danh mục 3 tầng chuẩn B2B (5 Root Level 0 -> 8 Sub Level 1 -> 16 Leaf Level 2) hỗ trợ song ngữ `vi`/`en`.
  - Nạp hơn 100 mã SKU sản phẩm cơ khí công nghiệp chuẩn xác (TL133, 16A-2F, LT40-1, 40SR, 40HP, M20, FV40, v.v.) vào bảng `products` kèm thông số kỹ thuật `specifications` (JSON), `price = null` (chuẩn báo giá B2B RFQ), và liên kết quan hệ `product_ref_categories`.
  - Tự động xóa cache danh mục và sản phẩm qua `ProductCategory::clearCategoryCaches()`.

- **`database/seeders/ThanhKhangPolicySeeder.php`**:
  - Cung cấp đầy đủ 6 chính sách B2B chuẩn Sitemap: Chính sách bán hàng & báo giá, Chính sách thanh toán B2B, Chính sách vận chuyển công nghiệp, Chính sách đổi trả hàng & khiếu nại kỹ thuật, Chính sách bảo hành bảo trì thiết bị, và Chính sách bảo mật thông tin doanh nghiệp.
  - Tự động xóa cache chính sách qua `Policy::clearPolicyCaches()`.

### 2. Dọn dẹp Backend, Config & Console Commands
- **`database/seeders/DatabaseSeeder.php`**:
  - Gỡ bỏ toàn bộ các seeder mồ côi (`SchoolSeeder`, `PointPackageSeeder`, `SynologyResourceSeeder`, `UserRankSeeder`, `AgencySeeder`, `FigmaProductsSeeder`, `ServiceContentSeeder`) và dữ liệu fake khóa học/tài nguyên.
  - Đăng ký gọi chính thức bộ 3 seeder Thành Khang mới: `ThanhKhangSettingSeeder::class`, `ThanhKhangPolicySeeder::class`, `ThanhKhangCatalogSeeder::class`.
- **`app/Console/Commands/`**: Vô hiệu hóa/ẩn các console command mồ côi `SyncCourseClassesCommand` và `SyncSynologyLinks`.
- **`config/app.php`**: Đổi fallback `'name' => env('APP_NAME', 'THANH KHANG Tecsat')`.
- **`config/services.php`**: Gỡ bỏ cấu hình thừa `synology`.
- **`app/Models/Config.php`**:
  - Xóa bỏ phương thức thừa `getLuxiBrandSettings()`.
  - Chuẩn hóa fallback trong `normalizeGeneralSettings()` và `getProcessSteps()` theo thông tin và quy trình B2B Thành Khang Tecsat.
- **Dọn dẹp hardcoded FDM/Resin**:
  - `app/Models/Service.php`: Thay thế dữ liệu fallback in 3D sang dịch vụ kỹ thuật phụ tùng công nghiệp.
  - `app/Models/Post/Post.php`: Cập nhật bộ FAQ mặc định trong `getFrontendFaqs()` sang câu hỏi thường gặp B2B phụ tùng thang cuốn & băng tải.
  - `HistoryController.php`, `ProductController.php`, `PolicyController.php`: Chuẩn hóa fallback SEO metadata sang Thành Khang Tecsat.

### 3. Dọn dẹp giao diện Admin CMS (`resources/Backend/js/`)
- `Components/SidebarMain.vue`: Đổi nhóm `🟢 VẬN HÀNH XƯỞNG IN 3D` thành `QUẢN LÝ SẢN PHẨM & BÁO GIÁ`, đổi mục `Báo giá & Yêu cầu in 3D` thành `Yêu cầu báo giá kỹ thuật (RFQ)`.
- `Pages/Dashboard.vue`: Đổi toàn bộ nhãn tiêu đề, chú thích và thẻ biểu đồ từ HH Maker 3D sang Thành Khang Tecsat B2B (Yêu cầu báo giá kỹ thuật RFQ, Cơ cấu thiết bị & phụ tùng công nghiệp).
- `Pages/Products/Products/Form.vue`: Chuẩn hóa tab thông số kỹ thuật sang đặc tính cơ khí công nghiệp thay cho nhãn in 3D FDM/Resin cứng.

## Capabilities

### New Capabilities
- `thanhkhang-b2b-catalog`: Cấu trúc cây danh mục 3 tầng và cơ sở dữ liệu hơn 100 mã SKU phụ tùng cơ khí công nghiệp B2B với thông số kỹ thuật chuẩn hóa, giá để trống (RFQ).
- `thanhkhang-brand-settings`: Cấu hình nhận diện thương hiệu công ty Thành Khang Tecsat với thông tin pháp lý (MST), 2 địa chỉ văn phòng, thông tin liên hệ và quy trình 5 bước B2B.
- `thanhkhang-b2b-policies`: Hệ thống 6 chính sách dịch vụ công nghiệp B2B đầy đủ song ngữ VI/EN.

### Modified Capabilities
- `admin-cms-terminology`: Đồng bộ thuật ngữ giao diện quản trị Admin CMS từ mô hình xưởng in 3D sang nhà cung ứng thiết bị công nghiệp B2B.

## Impact
- **Database**: Bảng `settings`, `product_categories`, `product_category_translations`, `products`, `product_translations`, `product_ref_categories`, `policies`, `policy_translations`. Thao tác an toàn thông qua `updateOrCreate` / `updateOrInsert`, tuyệt đối không drop table hay truncate làm mất tài khoản quản trị và dữ liệu người dùng.
- **Config & Models**: `config/app.php`, `config/services.php`, `app/Models/Config.php`, `app/Models/Service.php`, `app/Models/Post/Post.php`.
- **Controllers**: `app/Http/Controllers/Frontend/{HistoryController,ProductController,PolicyController}.php`.
- **Frontend / Backend Assets**: `resources/Backend/js/{Components/SidebarMain.vue,Pages/Dashboard.vue,Pages/Products/Products/Form.vue}`.
