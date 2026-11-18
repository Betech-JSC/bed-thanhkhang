# Spec: Triển khai Seeders chuẩn B2B Thành Khang Tecsat & Dọn dẹp tàn dư CMS/Backend

## ADDED REQUIREMENTS

### Requirement: Cấu hình thương hiệu và thông tin pháp lý Thành Khang Tecsat
Hệ thống BẮT BUỘC cung cấp cấu hình thương hiệu chính thức cho Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat thông qua `ThanhKhangSettingSeeder.php` và fallback trong `Config::normalizeGeneralSettings()`.
- **Thông tin công ty**:
  - Tên doanh nghiệp: "Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat" (Tiếng Anh: "Thanh Khang Tecsat Technical Services Co., Ltd").
  - Mã số thuế (MST): "107621881".
  - Hotline / Zalo: "0967732665".
  - Email: "info@dvktthanhkhang.vn".
  - Trụ sở chính: "Tầng 25, Tòa nhà TNR, 54A Nguyễn Chí Thanh, Phường Láng Thượng, Quận Đống Đa, Hà Nội".
  - VPGD: "NTT23, Khu Đô Thị Thống Nhất Complex, 82 Nguyễn Tuân, Phường Thanh Xuân Trung, Quận Thanh Xuân, Hà Nội".
  - Thời gian làm việc: "Thứ 2 - Thứ 7: 08:00 - 17:30".
- **Lĩnh vực hoạt động**: 3 lĩnh vực chủ đạo (Phụ tùng thang cuốn & thang máy, Hệ thống & phụ tùng băng tải công nghiệp / xích tải hành lý sân bay, Giải pháp tiết kiệm điện công nghiệp / Quạt trần BLDC).
- **Quy trình 5 bước B2B**:
  - Bước 1: Tiếp nhận yêu cầu kỹ thuật & mã phụ tùng.
  - Bước 2: Khảo sát thông số & Báo giá kỹ thuật (RFQ).
  - Bước 3: Xác nhận đơn hàng & Ký kết hợp đồng cung ứng.
  - Bước 4: Kiểm định chất lượng (QC) & Đóng gói công nghiệp.
  - Bước 5: Bàn giao, lắp đặt & Bảo hành chính hãng.
- **Cache Invalidation**: Gọi `Config::clearBrandCaches()` sau khi nạp dữ liệu.

### Requirement: Cây danh mục sản phẩm 3 tầng và cơ sở dữ liệu SKU công nghiệp
Hệ thống BẮT BUỘC triển khai seeder `ThanhKhangCatalogSeeder.php` tạo cây danh mục chuẩn 3 tầng và nạp trên 100 mã SKU sản phẩm cơ khí công nghiệp:
- **Cây danh mục 3 tầng (Parent-Child Hierarchy)**:
  - **Level 0 (5 Root Categories)**:
    1. Linh kiện thang cuốn (`linh-kien-thang-cuon`)
    2. Linh kiện thang nâng ô tô (`linh-kien-thang-nang-o-to`)
    3. Hệ thống băng tải hành lý (`he-thong-bang-tai-hanh-ly`)
    4. Linh kiện dây chuyền đóng gói (`linh-kien-day-chuyen-dong-goi`)
    5. Giải pháp tiết kiệm điện (`giai-phap-tiet-kiem-dien`)
  - **Level 1 (8 Subcategories)**:
    1. Tay vịn thang cuốn (`tay-vin-thang-cuon`) -> thuộc Root 1
    2. Xích thang cuốn (`xich-thang-cuon`) -> thuộc Root 1
    3. Bánh xe thang cuốn (`banh-xe-thang-cuon`) -> thuộc Root 1
    4. Xích bãi đỗ xe (`xich-bai-do-xe`) -> thuộc Root 2
    5. Xích băng tải hành lý/logistics (`xich-bang-tai-hanh-ly-logistics`) -> thuộc Root 3
    6. Xích dây chuyền đóng gói (`xich-day-chuyen-dong-goi`) -> thuộc Root 4
    7. Xích băng tải nhẹ (`xich-bang-tai-nhe`) -> thuộc Root 4
    8. Quạt BLDC (`quat-bldc`) -> thuộc Root 5
  - **Level 2 (16 Leaf Categories)**: 16 danh mục nhánh lá trực tiếp chứa sản phẩm.
- **Sản phẩm & Thông số kỹ thuật (SKU Products)**:
  - Hơn 100 sản phẩm cơ khí công nghiệp chuẩn xác (ví dụ: TL133, 16A-2F, LT40-1, 40SR, 40HP, M20, FV40...).
  - Mỗi sản phẩm có đầy đủ mã `sku`, `price = null`, `compare_at_price = null`, `status = ACTIVE`, mảng `specifications` dạng JSON (bước xích, tải đứt, vật liệu, tiêu chuẩn cơ khí...).
  - Gắn liên kết vào bảng `product_ref_categories`.
  - Hỗ trợ đa ngôn ngữ đầy đủ trong bảng `product_translations` (`vi` và `en`).
  - Gọi `ProductCategory::clearCategoryCaches()` sau khi hoàn tất.

### Requirement: 6 Chính sách dịch vụ B2B chuẩn SEO & Sitemap
Hệ thống BẮT BUỘC triển khai seeder `ThanhKhangPolicySeeder.php` cung cấp đủ 6 chính sách dịch vụ B2B:
1. Chính sách bán hàng & báo giá (`chinh-sach-ban-hang` / `sales-policy`).
2. Chính sách thanh toán B2B (`chinh-sach-thanh-toan` / `payment-policy`).
3. Chính sách vận chuyển công nghiệp (`chinh-sach-van-chuyen` / `shipping-policy`).
4. Chính sách đổi trả & khiếu nại kỹ thuật (`chinh-sach-doi-tra` / `return-refund-policy`).
5. Chính sách bảo hành & bảo trì thiết bị (`chinh-sach-bao-hanh` / `warranty-policy`).
6. Chính sách bảo mật thông tin doanh nghiệp (`chinh-sach-bao-mat` / `privacy-policy`).
- Gọi `Policy::clearPolicyCaches()` sau khi hoàn tất.

### Requirement: Dọn dẹp tàn dư Backend & Giao diện Admin CMS
- `DatabaseSeeder.php` chỉ gọi các seeder cần thiết và 3 seeder Thành Khang mới, loại bỏ toàn bộ seeder mồ côi và dữ liệu fake.
- Ẩn/vô hiệu hóa các console command mồ côi `SyncCourseClassesCommand` và `SyncSynologyLinks`.
- `config/app.php` đổi fallback name thành `THANH KHANG Tecsat`.
- `config/services.php` gỡ bỏ cấu hình `synology`.
- `app/Models/Config.php` gỡ bỏ `getLuxiBrandSettings()`, đồng bộ hóa các fallback `normalizeGeneralSettings()` và `getProcessSteps()`.
- Dọn dẹp các đoạn mã hardcoded FDM/Resin trong `Service.php`, `Post.php` và SEO fallback trong `HistoryController`, `ProductController`, `PolicyController`.
- Cập nhật giao diện Admin CMS (`SidebarMain.vue`, `Dashboard.vue`, `Form.vue`) sang các nhãn và thuật ngữ B2B Thành Khang Tecsat.
