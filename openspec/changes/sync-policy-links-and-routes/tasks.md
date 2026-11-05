# Tasks: sync-policy-links-and-routes

## 1. Backend & Data Seeder

- [x] 1.1 Khởi tạo `database/seeders/PolicySeeder.php` nạp 3 chính sách chuẩn nghiệp vụ in 3D (Điều khoản dịch vụ, Chính sách bảo mật, Chính sách bảo hành & đổi trả) song ngữ VI/EN, đăng ký vào `database/seeders/DatabaseSeeder.php`, và xác thực bằng `php artisan db:seed --class=PolicySeeder`.
- [x] 1.2 Cập nhật `app/Http/Middleware/HandleInertiaFrontendRequests.php` bổ sung fallback default policies khi bảng `policies` chưa có dữ liệu, xác thực bằng `php artisan tinker --execute="echo count(app(App\Http\Middleware\HandleInertiaFrontendRequests::class)->share(request())['data']['policies']);"`.
- [x] 1.3 Kiểm tra endpoint `policies.show` trong `app/Http/Controllers/Frontend/PolicyController.php` đảm bảo resolve chính xác cả 3 cặp slug `chinh-sach-bao-mat` / `privacy-policy`, `dieu-khoan-dich-vu` / `terms-of-service`, `chinh-sach-bao-hanh-doi-tra` / `warranty-return-policy`.

## 2. Frontend Footer Component (Footer.vue)

- [x] 2.1 Cập nhật nhãn hiển thị tại `resources/Frontend/js/Components/Footer.vue`: đổi "Điều khoản sử dụng" thành "Điều khoản dịch vụ", thay thế "Chính sách Cookie" thành "Chính sách bảo hành & đổi trả" có bọc `tt()`.
- [x] 2.2 Xây dựng computed `policyLinks` và phương thức `getPolicyUrl(slug)` trong `Footer.vue` trỏ chính xác đến `route('policies.show', slug)` cho từng bài viết tương ứng với locale hiện tại (`vi` / `en`).
- [x] 2.3 Kiểm tra trực quan layout chân trang ở chế độ Desktop và Mobile, xác nhận không bị xô lệch grid hay vỡ dòng bản quyền.

## 3. Frontend Policy Detail & Sidebar Navigation (Policy.vue)

- [x] 3.1 Kiểm tra và củng cố logic `checkActive(slug)` cùng `getPolicyUrl(policy)` trong `resources/Frontend/js/Pages/Policy.vue` để đảm bảo làm nổi bật đúng tab khi truy cập trực tiếp từ link chân trang.
- [x] 3.2 Kiểm tra bộ chọn danh mục di động (`isOpenMobile`) trong `Policy.vue` đảm bảo hiển thị đúng tên bài viết được chọn và đóng dropdown khi bấm chuyển tab.
- [x] 3.3 Xác thực hiển thị thẻ `<title>`, description và cập nhật ngày tháng (`formatted_updated_at`) cho từng bài viết chính sách.

## 4. Verification & Playwright Visual QA

- [x] 4.1 Biên dịch assets frontend bằng `npm run build` và xác nhận không phát sinh cảnh báo hay lỗi cú pháp Vue/Vite.
- [x] 4.2 Kiểm thử luồng nhấp chuột từ Chân trang trên trình duyệt: xác nhận click "Chính sách bảo mật" mở `/chinh-sach/chinh-sach-bao-mat`, click "Điều khoản dịch vụ" mở `/chinh-sach/dieu-khoan-dich-vu`, click "Chính sách bảo hành & đổi trả" mở `/chinh-sach/chinh-sach-bao-hanh-doi-tra`.
- [x] 4.3 Kiểm thử đa ngữ: chuyển đổi sang tiếng Anh (`/en`), xác nhận các link chân trang trỏ tới `/en/policies/privacy-policy`, `/en/policies/terms-of-service`, `/en/policies/warranty-return-policy` và highlight đúng active tab tiếng Anh.
- [x] 4.4 Ghi nhận ảnh chụp màn hình kiểm chứng (Playwright visual proof) trên Desktop và Mobile để hoàn thiện hồ sơ nghiệm thu.
