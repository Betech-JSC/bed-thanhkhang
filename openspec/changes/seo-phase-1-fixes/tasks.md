# Tasks: SEO Phase 1 Critical Fixes & Quick Wins

## 1. Robots.txt & Routing Configuration

- [x] 1.1 Sửa file `public/robots.txt`: thay thế dòng `Sitemap: https://funkystyle.vn/sitemap.xml` thành `Sitemap: https://hhmaker.vn/sitemap.xml`. Xác minh bằng lệnh `grep "hhmaker.vn" public/robots.txt` không còn tham chiếu tới `funkystyle.vn`.
- [x] 1.2 Dọn dẹp triplicate routing trong `routes/frontend.php`: gỡ bỏ các khai báo route dư thừa `Route::get('about', ...)` và `Route::get('ve-chung-toi', ...)`. Cấu hình redirect 301 rõ ràng cho `/about`, `/about-us` và `/ve-chung-toi` về `/gioi-thieu`. Xác minh bằng lệnh `php artisan route:list | grep -E "about|gioi-thieu|ve-chung-toi"`.

## 2. News/Posts 500 Bugfix & Component Integrity

- [x] 2.1 Khắc phục lỗi 500 trong `app/Http/Controllers/Frontend/PostController.php`: gỡ bỏ đoạn query `$courses` gọi method không tồn tại `whereCourses()`, bảo toàn logic lấy danh sách bài viết `$posts`. Xác minh bằng lệnh `php -l app/Http/Controllers/Frontend/PostController.php` không có lỗi cú pháp.
- [x] 2.2 Chuẩn hóa frontend component `resources/Frontend/js/Pages/Posts/Index.vue` và `Show.vue`: import `CardBlog` từ `@/Components/CardBlog.vue` vào danh sách components, cập nhật tiêu đề/nhãn thương hiệu từ "Funky Blog" thành "HH Maker Blog". Xác minh bằng `npm run build` (hoặc kiểm tra component compile không có warning thiếu component).

## 3. XML Sitemap Sanitization & Homepage Inclusion

- [x] 3.1 Bổ sung URL trang chủ `https://hhmaker.vn/` vào Sitemap generator trong `packages/bed-package-essentials/sitemap/src/Sitemap.php` (hoặc `app/Http/Controllers/Frontend/SitemapController.php`) với `priority = 1.0` và `changeFrequency = 'daily'`. Xác minh bằng cách gọi render sitemap và kiểm tra thẻ `<loc>` trang chủ xuất hiện đầu tiên.
- [x] 3.2 Lọc bỏ các route riêng tư/redirect/lỗi khỏi Sitemap: bổ sung danh sách loại trừ các URI mẫu (`dang-ky`, `dang-nhap`, `dashboard*`, `quen-mat-khau`, `dat-lai-mat-khau*`, `ho-so-ca-nhan`, `search*`, `tim-kiem`, `error*`, `demo`, `routes`, `robots.txt`, `about*`, `ve-chung-toi`). Xác minh bằng test/curl tới `/sitemap.xml` không còn chứa các URL này.

## 4. Subdomain www 301 Redirection

- [x] 4.1 Tạo cấu hình Nginx mẫu redirect 301 tại `deploy/nginx/hhmaker-redirect-www.conf` chuyển hướng mọi request từ `www.hhmaker.vn` sang `https://hhmaker.vn$request_uri`. Xác minh cú pháp file cấu hình Nginx hợp lệ.
- [x] 4.2 Tạo Laravel Middleware dự phòng `App\Http\Middleware\RedirectWwwToNonWww.php` và đăng ký vào nhóm `web` middleware: tự động chuyển hướng 301 các request có host bắt đầu bằng `www.`. Xác minh bằng unit/feature test gửi request với header `Host: www.hhmaker.vn` nhận phản hồi 301 về URL không có www.

## 5. 404 Error Page Standardization & Noindex

- [x] 5.1 Cập nhật `resources/Frontend/js/Pages/Error.vue`: thêm `<Head>` từ `@inertiajs/vue3` với `<meta name="robots" content="noindex, nofollow" head-key="robots" />` và title `{{ status || 404 }} - {{ tt('Không tìm thấy trang') }} | HH Maker`.
- [x] 5.2 Sửa đổi hotline trong `resources/Frontend/js/Pages/Error.vue` từ số Betech cũ `+84 283 8722 846` thành hotline HH Maker `0906 636 425`. Xác minh bằng `grep` kiểm tra chuỗi `0906 636 425` trong `resources/Frontend/js/Pages/Error.vue`.

## 6. Social Share / OG Image & Favicon HH Maker Branding

- [x] 6.1 Cập nhật `resources/Frontend/views/app.blade.php`: chuẩn hóa fallback `$rawImage` sang `/og-image.jpg?v=20261109`, lọc bỏ giá trị cũ từ database nếu chứa `funkystyle`, `khoqua`, hoặc `copy.jpg`, và bổ sung đầy đủ thẻ meta Open Graph & Twitter (`og:image:width`, `og:image:height`, `twitter:card="summary_large_image"`).
- [x] 6.2 Cập nhật `app/Http/Controllers/Frontend/HomeController.php`: thêm `'image' => '/og-image.jpg?v=20261109'` và `'seo_image' => '/og-image.jpg?v=20261109'` vào mảng `$seo`.
- [x] 6.3 Cập nhật `resources/Frontend/js/Layouts/MainLayout.vue` và `Guest.vue`: đổi fallback sang `/og-image.jpg?v=20261109`.

## 7. Full Phase 1 Verification & Quality Gate

- [x] 7.1 Chạy toàn bộ test suites backend (`php artisan test`) và PHP syntax check (`php -l`): xác nhận `/robots.txt` trả về 200, `/tin-tuc` trả về 200, `/about` trả về 301, `/sitemap.xml` hợp lệ chứa trang chủ.
- [ ] 7.2 Chạy Playwright QA Verifier trên trình duyệt thật kiểm tra hiển thị trang `/tin-tuc`, trang `/error` (xác nhận thẻ meta robots có `noindex, nofollow`, hotline `0906 636 425`) và xác nhận thẻ `og:image` mang thương hiệu HH Maker.
