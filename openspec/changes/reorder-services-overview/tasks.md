# Tasks: Đồng bộ thứ tự và nội dung Section "Tổng quan về dịch vụ" Trang chủ

## 1. i18n & Từ điển ngôn ngữ (Language Dictionaries)

- [x] 1.1 Bổ sung khóa dịch 'Custom' => 'Custom' và các cặp khóa mô tả tiếng Việt mới vào `resources/lang/vi/ui.php`, kiểm chứng cú pháp bằng lệnh `php -l resources/lang/vi/ui.php`.
- [x] 1.2 Bổ sung khóa dịch 'Custom' => 'Custom' và các cặp khóa mô tả tiếng Anh mới vào `resources/lang/en/ui.php`, kiểm chứng cú pháp bằng lệnh `php -l resources/lang/en/ui.php`.

## 2. Backend Model & Database Seeder (Service Content & Position Sync)

- [x] 2.1 Cập nhật `app/Models/Service.php`: bổ sung mô tả ngắn gọn cho section Tổng quan dịch vụ trong `getKnowledgeBaseMap()` và điều chỉnh helper nhận diện slug 'custom', kiểm chứng cú pháp bằng `php -l app/Models/Service.php`.
- [x] 2.2 Cập nhật `database/seeders/ServiceContentSeeder.php`: thiết lập thứ tự `$services` chuẩn (1: `gia-cong-3d`, 2: `thiet-ke-3d`, 3: `lam-do-custom`), gán đúng `position` 1, 2, 3 và cập nhật tiêu đề/mô tả theo Hình 2, kiểm chứng cú pháp bằng `php -l database/seeders/ServiceContentSeeder.php`.
- [x] 2.3 Thực thi Seeder an toàn bằng lệnh `php artisan db:seed --class=ServiceContentSeeder` và xóa sạch cache bằng `php artisan cache:clear`, kiểm chứng log thành công và xác nhận query `Service::query()->active()->sortByPosition()->pluck('slug', 'position')` trả về đúng thứ tự: 1 => 'gia-cong-3d', 2 => 'thiet-ke-3d', 3 => 'lam-do-custom'.

## 3. Frontend Vue Component (ServicesSection.vue Fallback & Style Sync)

- [x] 3.1 Cập nhật mảng `defaultServices` trong `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`:
  - Thẻ 1 (index 0): Gia công 3D (`slug: 'gia-cong-3d'`, icon máy in 3D, mô tả Hình 2).
  - Thẻ 2 (index 1): Thiết kế 3D (`slug: 'thiet-ke-3d'`, icon cây bút 3D, mô tả Hình 2, nền Slate Dark `#30374F`).
  - Thẻ 3 (index 2): Custom (`slug: 'lam-do-custom'`, icon phụ kiện 3D, mô tả Hình 2).
- [x] 3.2 Cập nhật hàm `getServiceUrl()`, `getServiceImage()` và `handleImageError()` trong `ServicesSection.vue` để nhận diện linh hoạt nhãn "Custom" và ánh xạ đúng ảnh/router.
- [x] 3.3 Chạy lệnh build frontend `npm run build` để biên dịch Vite assets, đảm bảo không có cảnh báo hay lỗi cú pháp Vue/JavaScript nào.

## 4. Kiểm thử tự động E2E & Visual QA (Playwright Verification)

- [x] 4.1 Chạy kiểm thử điều hướng dịch vụ bằng `npx playwright test tests/e2e/service-detail-figma.spec.ts`, xác nhận kiểm tra thẻ 0 là `gia-cong-3d`, thẻ 1 là `thiet-ke-3d`, thẻ 2 là `lam-do-custom` PASS 100%.
- [x] 4.2 Chạy kiểm thử Trang chủ bằng `npx playwright test tests/e2e/homepage.spec.ts` và `tests/e2e/home-services-multilingual.spec.ts`, xác nhận các thẻ dịch vụ hiển thị chuẩn song ngữ (VI & EN) PASS 100%.
- [x] 4.3 Chụp ảnh màn hình đối chiếu section "TỔNG QUAN VỀ DỊCH VỤ" trên Desktop và Mobile bằng Playwright lưu vào `tests/e2e/screenshots/services-overview-reordered.png`, kiểm chứng trực quan thẻ giữa đạt màu Slate Dark `#30374F` chuẩn Figma Node 2261:5549.
