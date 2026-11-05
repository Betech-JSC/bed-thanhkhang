# Tasks: dynamic-service-details

## 1. Backend Service Content Resolver & Seed Data

- [x] 1.1 Cập nhật `app/Models/Service.php`: Xây dựng từ điển dữ liệu chuẩn (Knowledge Base Map) và các resolver methods (`resolveHeroData()`, `resolveTocItems()`, `resolveArticleSections()`, `resolveSteps()`, `resolveChecklistItems()`) hỗ trợ độc lập cả 3 dịch vụ (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`) trên 2 ngôn ngữ (VI, EN). Kiểm chứng hoàn thành bằng lệnh `php artisan tinker --execute="echo json_encode(App\Models\Service::whereSlug('gia-cong-3d')->first()->transformDetails(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);"` trả về đúng dữ liệu đặc thù của Gia công 3D.
- [x] 1.2 Cập nhật `app/Http/Controllers/Frontend/ServiceController.php`: Bổ sung truyền dữ liệu `hero`, `toc_items`, `sections`, `steps`, `checklist_items` vào mảng dữ liệu Inertia render cho view `Services/Show`. Kiểm chứng hoàn thành bằng cách gọi endpoint API hoặc kiểm tra payload Inertia trả về đầy đủ các trường mới.
- [x] 1.3 Tạo và thực thi Seeder an toàn `database/seeders/ServiceContentSeeder.php`: Đồng bộ dữ liệu chi tiết có cấu trúc vào bảng `service_translations` bằng `updateOrCreate` cho các service ID 1, 2, 3 ở cả 2 locale `vi` và `en`, đồng thời xóa cache qua `Service::clearServiceCaches()`. Kiểm chứng hoàn thành bằng lệnh `php artisan db:seed --class=ServiceContentSeeder` thoát với mã 0 và dữ liệu trong database được cập nhật đầy đủ.

## 2. Frontend Components Dynamic Refactoring

- [x] 2.1 Refactor `resources/Frontend/js/Pages/Services/Show.vue`: Bind các props động (`hero`, `toc_items`, `sections`, `steps`, `checklist_items`) từ Backend; tính toán `tocItems` từ dữ liệu service; truyền props xuống các component con. Kiểm chứng hoàn thành bằng lệnh `npm run build` không phát sinh lỗi biên dịch.
- [x] 2.2 Refactor `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue`: Xóa bỏ text ghép cứng `: Từ phác thảo đến hình khối rõ ràng`, bind các props `headline`, `description`, `image`, `inputItems`, `processItems` động từ cha truyền vào. Kiểm chứng bằng `npm run build` và kiểm tra giao diện Hero của cả 3 dịch vụ hiển thị headline và các gạch đầu dòng khác nhau.
- [x] 2.3 Refactor `resources/Frontend/js/Pages/Services/Components/ServiceArticleContent.vue`: Xóa bỏ toàn bộ tiêu đề H2 và văn bản mô tả hardcode của Thiết kế 3D; render động Section 01 (tiêu đề, các đoạn văn bản), Section 02 (tiêu đề, intro, danh sách steps), Section 03 (tiêu đề, intro, danh sách checklistItems), Section 04 (tiêu đề, paragraph, khối callout). Kiểm chứng bằng `npm run build` không có cảnh báo/lỗi template.
- [x] 2.4 Cập nhật và tối ưu `resources/Frontend/js/Pages/Services/Components/ServiceToc.vue`: Đảm bảo nhận danh sách `items` động, hiển thị đúng số thứ tự và tiêu đề rút gọn tương ứng với từng dịch vụ, duy trì hiệu ứng cuộn mượt và trạng thái active scrollspy. Kiểm chứng bằng `npm run build`.

## 3. Verification, Automated Testing & Visual QA

- [x] 3.1 Xây dựng bộ test Playwright E2E mới `tests/e2e/dynamic-service-details.spec.ts`: Kiểm tra chi tiết cả 3 dịch vụ (`/dich-vu/thiet-ke-3d`, `/dich-vu/gia-cong-3d`, `/dich-vu/lam-do-custom`) và bản tiếng Anh (`/en/services/...`) xác nhận Hero, TOC, và 4 sections kỹ thuật hiển thị đúng nội dung đặc thù riêng biệt, không có bất kỳ chuỗi trùng lặp sai lệch nào. Kiểm chứng bằng lệnh `npx playwright test tests/e2e/dynamic-service-details.spec.ts` cho kết quả PASS 100%.
- [x] 3.2 Chạy hồi quy các test suite hiện có: `npx playwright test tests/e2e/service-detail-figma-strict.spec.ts` và `tests/e2e/home-services-multilingual.spec.ts` đảm bảo 100% test cũ vẫn PASS hoàn toàn không bị ảnh hưởng.
- [x] 3.3 Chụp ảnh màn hình kiểm chứng trực quan (Visual QA) qua Playwright MCP hoặc script chụp ảnh cho cả 3 trang dịch vụ trên viewport Desktop (1440px) và Mobile (390px), lưu vào thư mục `docs/evidence/` làm bằng chứng nghiệm thu.
