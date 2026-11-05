# Proposal: Đồng bộ thứ tự và nội dung Section "Tổng quan về dịch vụ" Trang chủ

## Why

Hiện tại, section "TỔNG QUAN VỀ DỊCH VỤ" trên Trang chủ (`ServicesSection.vue`) và dữ liệu mặc định/seeder trong database đang có sự lệch thứ tự và nội dung so với thiết kế Figma chuẩn (Hình 2 / Node 2261:5549):
1. Thẻ giữa có màu nền Slate Dark đặc trưng (`#30374F`) theo thiết kế Figma là dịch vụ **"Thiết kế 3D"** (Vị trí 2), nhưng trước đây trong code fallback `defaultServices` lại đặt thẻ "Thiết kế 3D" ở vị trí 1 và "Gia công 3D" ở vị trí 2.
2. Thứ tự hiển thị chuẩn của Figma và các kịch bản kiểm thử E2E (`service-detail-figma.spec.ts`, `homepage.spec.ts`) đều kỳ vọng:
   - Thẻ 1: **Gia công 3D** (`position: 1`) - Icon máy in 3D.
   - Thẻ 2 (thẻ giữa nền tối `#30374F`): **Thiết kế 3D** (`position: 2`) - Icon cây bút 3D.
   - Thẻ 3: **Custom** (`position: 3`) - Icon đũa phép/phụ kiện 3D.
3. Nội dung văn bản mô tả của cả 3 thẻ dịch vụ trên Trang chủ cần được cập nhật theo đúng bản duyệt Hình 2, đồng thời tên hiển thị của dịch vụ 3 được rút gọn từ "Làm đồ custom" thành "Custom" để tinh gọn, hiện đại và chuẩn Figma.
4. Cần đồng bộ toàn bộ từ Frontend fallback, Backend database/seeder (`position`, `title`, `description`), Từ điển i18n (`vi`, `en`) và kiểm thử tự động E2E.

## What Changes

1. **Frontend Vue Component (`ServicesSection.vue`)**:
   - Cập nhật mảng `defaultServices` theo đúng thứ tự:
     - Index 0: `title: 'Gia công 3D'`, `slug: 'gia-cong-3d'`, mô tả: `"Hiện thực hóa mô hình bằng công nghệ in 3D, chú trọng độ chính xác và chất lượng sản phẩm"` (VI), icon máy in 3D, gradient nền viền xanh.
     - Index 1: `title: 'Thiết kế 3D'`, `slug: 'thiet-ke-3d'`, mô tả: `"Biến ý tưởng thành mô hình 3D chi tiết, kết hợp tính thẩm mỹ và khả năng chế tác thực tế."` (VI), icon cây bút 3D, gradient nền Slate Dark `#30374F`.
     - Index 2: `title: 'Custom'`, `slug: 'lam-do-custom'`, mô tả: `"Thiết kế và chế tác theo yêu cầu, tạo nên những món đồ độc đáo mang dấu ấn riêng của bạn."` (VI), icon phụ kiện 3D, gradient nền viền xanh.
   - Giữ nguyên cơ chế render linh hoạt và bộ ánh xạ ảnh/slug an toàn.

2. **Backend Database & Seeder**:
   - Cập nhật `database/seeders/ServiceContentSeeder.php`: Đồng bộ thứ tự `$services` ánh xạ ID và `position`:
     - ID 1: `gia-cong-3d` (`position = 1`).
     - ID 2: `thiet-ke-3d` (`position = 2`).
     - ID 3: `lam-do-custom` (`position = 3`).
   - Cập nhật `app/Models/Service.php`: Bổ sung mô tả ngắn gọn cho section Tổng quan dịch vụ trong Knowledge Base và điều chỉnh hàm `getServiceKey()` / fallback tương thích an toàn.
   - Chạy seeder chế độ cập nhật an toàn (`updateOrCreate` / `updateOrInsert`), tuyệt đối không xóa bảng hay làm mất dữ liệu.

3. **Từ điển đa ngôn ngữ (i18n Translation)**:
   - Cập nhật `resources/lang/vi/ui.php` và `resources/lang/en/ui.php` với các cặp khóa - giá trị mới:
     - `'Custom' => 'Custom'` (VI) / `'Custom'` (EN).
     - Mô tả tiếng Việt và bản dịch tiếng Anh chuẩn mực tương ứng cho cả 3 dịch vụ.

4. **Kiểm thử E2E Playwright**:
   - Kiểm tra và đảm bảo toàn bộ test suites (`homepage.spec.ts`, `service-detail-figma.spec.ts`, `home-services-multilingual.spec.ts`) vượt qua 100% với bằng chứng ảnh chụp thật.

## Capabilities

### Modified Capabilities
- `home-and-services`: Đồng bộ thứ tự hiển thị 3 dịch vụ trên Trang chủ thành Gia công 3D (vị trí 1), Thiết kế 3D (vị trí 2 - thẻ giữa Slate Dark #30374F), Custom (vị trí 3, đổi tên hiển thị từ "Làm đồ custom" thành "Custom") và cập nhật mô tả song ngữ mới theo Figma Hình 2.

## Safe Guard & Impact Boundaries

### Strict Safe Guard (Phạm vi an toàn)
- **Slug bảo tồn 100%**: Giữ nguyên toàn bộ slug kỹ thuật (`gia-cong-3d`, `thiet-ke-3d`, `lam-do-custom`) để không làm gãy URL router, SEO indexation hoặc các liên kết nội bộ.
- **Không phá hủy Database**: Cấm chạy `migrate:fresh`, `truncate`, hoặc `drop table`. Chỉ cập nhật trường `position`, `title`, `description` trên các bản ghi dịch vụ sẵn có.
- **Không sửa kiến trúc**: Không thay đổi layout, routing hay các section khác ngoài phạm vi `ServicesSection` và dữ liệu Dịch vụ liên quan.

### Impact Analysis
- **Codebase bị ảnh hưởng**:
  - `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`
  - `database/seeders/ServiceContentSeeder.php`
  - `app/Models/Service.php`
  - `resources/lang/vi/ui.php`
  - `resources/lang/en/ui.php`
  - `tests/e2e/home-services-multilingual.spec.ts` (nếu cần điều chỉnh chuỗi kỳ vọng khớp từ khóa mới)
- **Hệ thống liên đới**: Trang chủ (`/` và `/en`), Trang danh sách dịch vụ (`/dich-vu` và `/en/services`), API / props Inertia truyền xuống từ `HomeController`.
