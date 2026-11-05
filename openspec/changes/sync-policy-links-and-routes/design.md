# Design: sync-policy-links-and-routes

## Context

Tham khảo bối cảnh và động lực tại `proposal.md`.
Hiện trạng hệ thống:
1. Tuyến đường (Routes) tại `routes/frontend.php` đã hỗ trợ đầy đủ `policies.index` (`/chinh-sach` hoặc `/en/policies`) và `policies.show` (`/chinh-sach/{slug}` hoặc `/en/policies/{slug}`).
2. `PolicyController.php` đã có sẵn phương thức `renderDataPage($slug = null)` và mảng dữ liệu mẫu `getDefaultPolicies($locale)` chứa đúng 3 bài viết chuyên ngành in 3D:
   - Điều khoản dịch vụ (`dieu-khoan-dich-vu` / `terms-of-service`)
   - Chính sách bảo mật (`chinh-sach-bao-mat` / `privacy-policy`)
   - Chính sách bảo hành & đổi trả (`chinh-sach-bao-hanh-doi-tra` / `warranty-return-policy`)
3. Bảng cơ sở dữ liệu `policies` hiện có 0 bản ghi (chưa được nạp từ seeder).
4. `HandleInertiaFrontendRequests.php` truy vấn `Policy::query()->active()->sortByPosition()->get()` trả về mảng rỗng `[]` khi chưa có dữ liệu DB.
5. Tại `Footer.vue`, 3 liên kết chính sách đang bị gán cứng `route('policies.index')` không có tham số `slug`, đồng thời hiển thị nhãn "Điều khoản sử dụng" và "Chính sách Cookie" lệch chuẩn.

## Goals / Non-Goals

**Goals:**
- Tái cấu trúc thanh liên kết chính sách ở Chân trang (`Footer.vue`) để điều hướng trực tiếp bằng slug chi tiết:
  - `route('policies.show', 'chinh-sach-bao-mat')` (VI) / `route('policies.show', 'privacy-policy')` (EN)
  - `route('policies.show', 'dieu-khoan-dich-vu')` (VI) / `route('policies.show', 'terms-of-service')` (EN)
  - `route('policies.show', 'chinh-sach-bao-hanh-doi-tra')` (VI) / `route('policies.show', 'warranty-return-policy')` (EN)
- Đồng bộ nhãn hiển thị Chân trang thành "Chính sách bảo mật", "Điều khoản dịch vụ" và "Chính sách bảo hành & đổi trả" (song ngữ VI/EN).
- Đảm bảo `Policy.vue` kích hoạt chính xác active state cho Sidebar và Mobile Category Selector khi người dùng truy cập trực tiếp từ Chân trang.
- Xây dựng `PolicySeeder.php` nạp 3 chính sách chuẩn vào database và đăng ký vào `DatabaseSeeder.php`.
- Bổ sung fallback trong `HandleInertiaFrontendRequests.php` để chia sẻ mảng policies chuẩn ngay cả khi cơ sở dữ liệu chưa chạy seeder.

**Non-Goals:**
- Không thay đổi cấu trúc bảng cơ sở dữ liệu (giữ nguyên schema của `policies` và `policy_translations`).
- Không tác động đến các cột thông tin khác ở Footer (Sản phẩm, Dịch vụ, Giới thiệu, Quy trình, Hotline, Email).
- Không can thiệp vào các controller hoặc trang nội dung khác ngoài phạm vi Chính sách.

## Decisions

### 1. Cơ chế sinh liên kết tại `Footer.vue`: Hybrid Approach (Computed + Safe Route Fallback)
- **Quyết định**: Xây dựng computed property `policyLinks` trong `Footer.vue` trả về danh sách 3 mục chính sách tương ứng với locale hiện tại. Mỗi mục chứa `title`, `slug`, và `url`.
  - URL được tạo thông qua phương thức helper an toàn:
    ```js
    getPolicyUrl(slug) {
        if (typeof this.route === 'function') {
            try {
                return this.route('policies.show', slug)
            } catch (e) {
                // Fallback nếu Ziggy gặp vấn đề
            }
        }
        return this.currentLocale === 'en' ? `/en/policies/${slug}` : `/chinh-sach/${slug}`
    }
    ```
- **Lý do lựa chọn**: Tránh việc phụ thuộc hoàn toàn vào database hoặc API khi render Chân trang (vốn xuất hiện ở mọi trang trên website). Nếu database chưa có data, chân trang vẫn hoạt động hoàn hảo 100%. Nếu có data động, có thể ưu tiên lấy từ `$page.props.data.policies`.
- **Phương án thay thế đã cân nhắc**: Hardcode chuỗi href tĩnh `<a href="/chinh-sach/...">`. Bị loại bỏ vì không tận dụng được Inertia SPA link và không tự động thích ứng khi đổi locale hoặc tiền tố route.

### 2. Chuẩn hóa cặp Slug song ngữ (VI / EN)
- **Quyết định**: Đồng bộ hóa tuyệt đối 3 cặp slug trên toàn hệ thống:
  1. Bảo mật: `chinh-sach-bao-mat` (VI) <-> `privacy-policy` (EN)
  2. Dịch vụ: `dieu-khoan-dich-vu` (VI) <-> `terms-of-service` (EN)
  3. Bảo hành & đổi trả: `chinh-sach-bao-hanh-doi-tra` (VI) <-> `warranty-return-policy` (EN)
- **Lý do lựa chọn**: Đây là các slug ngữ nghĩa chuẩn SEO, đã được triển khai trong `PolicyController.php` và phù hợp với tiêu chuẩn URL tiếng Việt không dấu.

### 3. Khởi tạo `PolicySeeder.php` & Cơ chế Fallback Backend
- **Quyết định**:
  - Tạo `PolicySeeder.php` kế thừa từ `getDefaultPolicies()` của `PolicyController` hoặc nạp trực tiếp vào model `Policy` với 2 ngôn ngữ `vi` và `en`.
  - Trong `HandleInertiaFrontendRequests.php`, khi `$policies->isEmpty()`, kích hoạt fallback sang danh sách mặc định để `$page.props.data.policies` luôn có đủ 3 bản ghi.
- **Lý do lựa chọn**: Bảo đảm tính đồng nhất dữ liệu giữa Frontend và Backend, sẵn sàng cho việc quản trị CMS sau này qua Admin Portal.

### 4. Kiểm chứng Active State trên `Policy.vue`
- **Quyết định**: Giữ nguyên và củng cố logic `checkActive(slug)` trong `Policy.vue`. Logic này so sánh cả `content.slug` và `currentPath.endsWith('/' + slug)`. Khi truy cập từ Footer, URL chứa đúng slug sẽ ngay lập tức match và kích hoạt màu nền cùng viền nổi bật.

## Risks / Trade-offs

- **[Rủi ro] Lệch slug giữa database thực tế và link chân trang**
  - *Giải pháp*: Seeder sử dụng hằng số slug cố định khớp 100% với `Footer.vue` và `PolicyController`.
- **[Rủi ro] Lỗi Ziggy khi chuyển đổi nhanh ngôn ngữ**
  - *Giải pháp*: Helper `getPolicyUrl(slug)` luôn có khối `try/catch` fallback sang đường dẫn URL chuẩn tương ứng với locale hiện tại.
- **[Rủi ro] Xung đột dữ liệu khi chạy Seeder nhiều lần**
  - *Giải pháp*: `PolicySeeder` sử dụng `updateOrCreate` theo slug hoặc truncate có kiểm soát chỉ riêng dữ liệu policy mẫu.

## Migration Plan

1. Tạo seeder `database/seeders/PolicySeeder.php`.
2. Đăng ký `PolicySeeder::class` vào `DatabaseSeeder.php`.
3. Chạy `php artisan db:seed --class=PolicySeeder` để nạp dữ liệu chính sách ban đầu.
4. Cập nhật `HandleInertiaFrontendRequests.php` bổ sung fallback an toàn.
5. Cập nhật `Footer.vue` với cấu trúc liên kết và nhãn mới.
6. Xác thực trải nghiệm click và active tab bằng Playwright / Browser test.
