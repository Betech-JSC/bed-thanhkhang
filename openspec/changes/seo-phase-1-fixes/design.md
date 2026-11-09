# Design: SEO Phase 1 Critical Fixes & Quick Wins

## Context

Hệ thống HH Maker chạy trên nền tảng Laravel + Inertia.js + Vue 3 với package lõi `packages/bed-package-essentials`.
Kết quả audit tại `hhmaker.vn-audit/FULL-AUDIT-REPORT.md` phát hiện 5 lỗi kỹ thuật nghiêm trọng đang kéo tụt SEO Health Score xuống 45/100:
1. `public/robots.txt` chứa sitemap của domain cũ `funkystyle.vn`.
2. Controller `PostController` crash 500 do gọi scope `whereCourses()` không tồn tại trong Eloquent Model `Post`.
3. Package sitemap `JamstackVietnam\Sitemap\Sitemap` mặc định bỏ qua route `/` và tự động gom tất cả GET routes có middleware `frontend` (khiến các route redirect auth/search/error lọt vào sitemap).
4. `routes/frontend.php` khai báo chồng chéo giữa `Route::redirect` và các `Route::get` nội tuyến (`about`, `ve-chung-toi`), khiến cả 3 URL đều trả về HTTP 200.
5. Chưa có xử lý redirect tên miền phụ `www.hhmaker.vn` tại tầng Nginx và tầng ứng dụng.
6. Giao diện trang lỗi `resources/Frontend/js/Pages/Error.vue` thiếu thẻ meta robots `noindex` và đang hiển thị hotline của đơn vị cũ (Betech).

## Goals / Non-Goals

**Goals:**
- Đảm bảo 100% các công cụ tìm kiếm đọc đúng Sitemap URL tại `https://hhmaker.vn/sitemap.xml`.
- Khắc phục triệt để lỗi HTTP 500, đưa route `/tin-tuc` và `/en/posts` về trạng thái HTTP 200 OK ổn định.
- XML Sitemap chứa đầy đủ trang chủ `https://hhmaker.vn/` với `priority: 1.0` và loại trừ 100% các URL private/redirect/error.
- Chuẩn hóa cấu trúc URL Giới thiệu: duy nhất `/gioi-thieu` (và `/en/about-us`) trả về HTTP 200, các biến thể `/about`, `/ve-chung-toi` redirect 301.
- Hỗ trợ chuyển hướng 301 an toàn từ `www.hhmaker.vn` về `https://hhmaker.vn` qua 2 lớp (Nginx và Laravel Middleware).
- Trang 404 và `/error` hiển thị đúng thương hiệu HH Maker, hotline `0906 636 425` và gắn thẻ `noindex, nofollow`.

**Non-Goals:**
- Không triển khai các Schema JSON-LD phức tạp (Product, Offer, LocalBusiness, Breadcrumbs) trong Phase 1 (thuộc phạm vi Phase 2).
- Không tối ưu nén ảnh banner WebP hay chỉnh sửa responsive CSS (thuộc phạm vi Phase 2).
- Không sửa đổi database schema, không chạy database migrations.
- Không thay đổi logic CMS backend quản trị tin tức hay danh mục sản phẩm.

## Decisions

### 1. Phân tách và làm sạch query trong `PostController.php`
- **Hiện trạng**:
  ```php
  $courses = Post::query()->active()->whereCourses()->activeCategories()->...;
  ```
  Model `Post` chỉ hỗ trợ các types: `POST`, `FEEDBACK`, `MEMBER`, `FAQ`, `LEARNING_ENV`, `PRIZE`, `PROJECT`. Không có `whereCourses` hay `TYPE_COURSES`. Trong khi đó, component `Index.vue` không hề sử dụng prop `courses`.
- **Quyết định**: Gỡ bỏ hoàn toàn biến `$courses` trong `PostController::index()`. Trả về `posts` sạch sẽ.
- **Frontend Posts/Index.vue**: Bổ sung `import CardBlog from '@/Components/CardBlog.vue'` và đăng ký trong `components` để đảm bảo Vue render an toàn; đổi nhãn "Funky Blog" thành "HH Maker Blog".
- **Giải pháp thay thế đã cân nhắc**: Định nghĩa thêm scope `scopeWhereCourses` giả lập trả về rỗng. Tuy nhiên cách này làm tăng nợ kỹ thuật (technical debt) và query vô ích vào DB; việc loại bỏ hoàn toàn là phương án sạch nhất.

### 2. Chuẩn hóa cơ chế tạo Sitemap trong `SitemapController` và `Sitemap.php`
- **Hiện trạng**: Method `addStaticRoutes()` trong `packages/bed-package-essentials/sitemap/src/Sitemap.php` tự động thêm mọi route GET có middleware `frontend` không chứa `{}` và loại trừ `$uri === '/'`.
- **Quyết định**:
  1. Thêm danh sách đen (Blacklist patterns) vào `Sitemap.php` hoặc lọc tại `SitemapController`:
     ```php
     $excludedPatterns = [
         'dang-ky', 'dang-nhap', 'dashboard*', 'quen-mat-khau',
         'dat-lai-mat-khau*', 'ho-so-ca-nhan', 'search*', 'tim-kiem',
         'error*', 'demo', 'routes', 'robots.txt', 'about*', 've-chung-toi'
     ];
     ```
  2. Bổ sung URL trang chủ `url('/')` với cấu hình:
     ```php
     $this->add([
         'url' => url('/'),
         'name' => 'Trang chủ',
         'lastModificationDate' => now()->toAtomString(),
         'changeFrequency' => 'daily',
         'priority' => 1.0,
     ]);
     ```
- **Lý do**: Giải quyết đồng thời 2 lỗi nghiêm trọng: thiếu trang chủ và tràn lan URL 301/auth trong sitemap.

### 3. Dọn dẹp Triplicate Routing trong `routes/frontend.php`
- **Hiện trạng**: Khai báo `Route::redirect('/about', '/gioi-thieu', 301)` ở đầu file bị ghi đè bởi `Route::get('about', 'index')` và `Route::get('ve-chung-toi', ...)` bên trong `Route::localized()`.
- **Quyết định**:
  1. Xóa bỏ dòng `Route::get('about', 'index')` và `Route::get('ve-chung-toi', [HistoryController::class, 'index'])`.
  2. Chỉ định rõ ràng các cặp redirect 301:
     - `/about` -> `/gioi-thieu` (301)
     - `/about-us` -> `/gioi-thieu` (301)
     - `/ve-chung-toi` -> `/gioi-thieu` (301)
     - `/en/about` -> `/en/about-us` (301)
     - `/en/ve-chung-toi` -> `/en/about-us` (301)
  3. Giữ nguyên route chuẩn duy nhất:
     ```php
     Route::get(Lang::uri('about-us'), 'index')->name('histories.index');
     ```

### 4. Giải pháp Redirect 301 Hai Lớp cho `www.hhmaker.vn`
- **Lớp 1 (Nginx Web Server)**: Tạo file cấu hình `deploy/nginx/hhmaker-redirect-www.conf` để deploy lên VPS:
  ```nginx
  server {
      listen 80;
      listen [::]:80;
      listen 443 ssl http2;
      listen [::]:443 ssl http2;
      server_name www.hhmaker.vn;
      return 301 https://hhmaker.vn$request_uri;
  }
  ```
- **Lớp 2 (Laravel Middleware)**: Tạo middleware `App\Http\Middleware\RedirectWwwToNonWww`:
  Nếu Host header bắt đầu bằng `www.`, tự động redirect 301 về URL không có www tương ứng. Đăng ký middleware này trong nhóm `$middlewareGroups['web']`.
- **Lý do**: Lớp Nginx xử lý nhanh nhất ở tầng mạng; lớp Middleware bảo đảm an toàn kể cả khi DNS trỏ về mà cấu hình Nginx chưa kịp reload hoặc trên môi trường test/staging.

### 5. Chuẩn hóa `resources/Frontend/js/Pages/Error.vue`
- Bổ sung component `<Head>` từ `@inertiajs/vue3`.
- Gắn thẻ meta `<meta name="robots" content="noindex, nofollow" head-key="robots" />`.
- Cập nhật title động theo mã status: `<title>{{ status || 404 }} - {{ tt('Không tìm thấy trang') }} | HH Maker</title>`.
- Đổi số hotline thành `0906 636 425`.

### 6. Chuẩn hóa Social Share Preview (OG:Image & Favicon)
- **Cập nhật `resources/Frontend/views/app.blade.php`**:
  + Xử lý `$rawImage = $seo['seo_image'] ?? $seo['image'] ?? null;`
  + Thiết lập lớp phòng thủ: Nếu `$rawImage` rỗng, kiểm tra `$global['seo_image']`. Nếu giá trị này chứa `funkystyle`, `khoqua`, hoặc `copy.jpg` từ dữ liệu cũ, tự động loại bỏ và fallback về `/og-image.jpg?v=20261109`.
  + Nối domain bằng `request()->getSchemeAndHttpHost()` để đảm bảo luôn dùng đúng domain thực tế (`https://hhmaker.vn`).
  + Bổ sung đầy đủ các thẻ meta kích thước và loại hình ảnh cho Open Graph và Twitter: `og:image`, `og:image:secure_url`, `og:image:width` (1200), `og:image:height` (630), `twitter:card` (`summary_large_image`), `twitter:image`.
- **Cập nhật `app/Http/Controllers/Frontend/HomeController.php`**:
  + Thiết lập rõ ràng `'image' => '/og-image.jpg?v=20261109'` và `'seo_image' => '/og-image.jpg?v=20261109'` trong mảng `$seo` trả về.
- **Cập nhật `resources/Frontend/js/Layouts/MainLayout.vue` và `Guest.vue`**:
  + Chuyển fallback image từ `/cover.jpg?v=2` sang `/og-image.jpg?v=20261109`.

## Risks / Trade-offs

- **[Risk] Chuyển hướng 301 cache trên trình duyệt người dùng**: Trình duyệt có thể cache vĩnh viễn redirect 301 nếu cấu hình sai.
  - *Mitigation*: Kiểm tra kỹ lưỡng status code bằng curl (`curl -I`) và test trên incognito browser trước khi áp dụng.
- **[Risk] Gỡ bỏ `$courses` ảnh hưởng trang khác**: Có trang nào khác dùng chung `PostController::index` không?
  - *Mitigation*: Đã đối chiếu toàn bộ `resources/Frontend/` và `routes/frontend.php`, chỉ có route `/tin-tuc` gọi `PostController@index`, và component `Index.vue` hoàn toàn không render `courses`.
- **[Risk] File package sitemap nằm trong `packages/`**: Việc sửa trực tiếp trong package có thể bị xung đột nếu update composer?
  - *Mitigation*: `packages/bed-package-essentials` là thư mục mã nguồn nội bộ của dự án (path repository), không phải vendor bên ngoài, đã được quản lý trong git repository của dự án.

## Migration Plan

1. Tạo các file cấu hình và cập nhật mã nguồn theo danh sách tasks.
2. Kiểm tra syntax PHP bằng `php -l`.
3. Kiểm tra build frontend bằng `npm run build` hoặc kiểm tra component Vue.
4. Chạy test curl kiểm tra header status của các routes:
   - `/robots.txt` -> HTTP 200, chứa `https://hhmaker.vn/sitemap.xml`.
   - `/tin-tuc` -> HTTP 200.
   - `/en/posts` -> HTTP 200.
   - `/about` -> HTTP 301 Location `/gioi-thieu`.
   - `/ve-chung-toi` -> HTTP 301 Location `/gioi-thieu`.
   - `/sitemap.xml` -> HTTP 200, có trang chủ priority 1.0, không có route auth/error.
   - `/error` -> Có thẻ `noindex, nofollow`.
5. Rollback: Nếu phát sinh lỗi, revert các commit tương ứng (không có rủi ro database).
