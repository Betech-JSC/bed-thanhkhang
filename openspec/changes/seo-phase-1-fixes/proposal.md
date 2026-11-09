# Proposal: SEO Phase 1 Critical Fixes & Quick Wins

## Why

Kết quả kiểm toán SEO toàn diện tại `hhmaker.vn-audit/FULL-AUDIT-REPORT.md` cho thấy website `hhmaker.vn` đạt điểm SEO Health Score rất thấp (**45/100 - Xếp loại: Yếu / Poor**) do tồn tại các lỗi kỹ thuật nghiêm trọng (Critical Blockers):
1. File `public/robots.txt` khai báo nhầm sitemap của website khác (`funkystyle.vn/sitemap.xml`), khiến Googlebot và các công cụ tìm kiếm thu thập sai cấu trúc website.
2. Route chuyên mục tin tức (`/tin-tuc` và `/en/posts`) bị crash trả về mã lỗi HTTP 500 (`{"error":"Something went wrong."}`) do gọi method không tồn tại (`whereCourses()`) trong khi vẫn submit URL này trong XML Sitemap.
3. XML Sitemap thiếu hoàn toàn trang chủ `https://hhmaker.vn/` (priority 1.0) nhưng lại chứa nhiều route nội bộ, route xác thực/auth (`/dang-nhap`, `/dang-ky`, `/dashboard`, `/search`, `/error`).
4. Triplicate URL Cannibalization: Ba URL giới thiệu (`/about`, `/gioi-thieu`, `/ve-chung-toi`) cùng tồn tại đồng thời, tự trỏ canonical về chính mình gây ăn thịt từ khóa.
5. Chưa có cấu hình redirect 301 từ `www.hhmaker.vn` về domain chuẩn `https://hhmaker.vn` dẫn đến lỗi `ERR_NAME_NOT_RESOLVED` và phân mảnh tín hiệu xếp hạng.
6. Trang lỗi 404 và route `/error` chứa hotline cũ và thiếu chỉ dẫn `noindex`, khiến trang lỗi bị Google lập chỉ mục.
7. Khi chia sẻ link `https://hhmaker.vn` lên mạng xã hội/ứng dụng chat (Telegram, Facebook, Zalo, Twitter), preview card hiển thị đúng Title và Description nhưng Thumbnail / OG:Image lại hiển thị logo Funkystyle cũ (hình quả khổ qua màu trắng trên nền cam có chữ funkystyle) do ảnh cũ trong fallback hoặc cấu hình CMS chưa được cập nhật sang banner/logo chính thức của HH Maker.

Thực hiện Phase 1 là bước tiền đề sống còn, giúp thông suốt luồng indexation, dọn sạch lỗi crawler, đồng bộ nhận diện thương hiệu HH Maker khi chia sẻ mạng xã hội và nâng ngay Health Score từ 45 lên 65+ trước khi tiến hành tối ưu Schema và Core Web Vitals ở Phase 2.

## What Changes

Thay đổi tập trung vào 8 hạng mục cốt lõi của Phase 1:
- **Sửa Sitemap trong `public/robots.txt`**: Cập nhật URL trỏ đúng về `https://hhmaker.vn/sitemap.xml`.
- **Khắc phục lỗi HTTP 500 chuyên mục Tin tức (`/tin-tuc` & `/en/posts`)**: Gỡ bỏ gọi method không tồn tại `whereCourses()` trong `app/Http/Controllers/Frontend/PostController.php`; kiểm tra và import đầy đủ component hiển thị bài viết `CardBlog.vue` trong `resources/Frontend/js/Pages/Posts/Index.vue` và `Show.vue`.
- **Bổ sung Trang chủ vào XML Sitemap**: Đảm bảo `https://hhmaker.vn/` xuất hiện ở vị trí đầu tiên với `priority: 1.0` và `changefreq: daily`.
- **Xử lý Triplicate URL Giới thiệu**: Chọn `/gioi-thieu` (và `/en/about-us` cho locale EN) làm URL chuẩn duy nhất. Cấu hình 301 Permanent Redirect cho `/about` và `/ve-chung-toi` về `/gioi-thieu`; gỡ bỏ các route trùng lặp trong `routes/frontend.php`.
- **Lọc sạch XML Sitemap**: Loại bỏ toàn bộ các route riêng tư/redirect/lỗi (`/dang-ky`, `/dang-nhap`, `/dashboard*`, `/quen-mat-khau`, `/dat-lai-mat-khau*`, `/ho-so-ca-nhan`, `/search*`, `/tim-kiem`, `/error*`, `/robots.txt`) ra khỏi danh sách URL của Sitemap.
- **Cấu hình 301 Redirect cho `www.hhmaker.vn`**: Cung cấp cấu hình mẫu Nginx server block cho subdomain `www` và bổ sung lớp bảo vệ bằng Laravel Middleware chuyển hướng 301 tự động về `https://hhmaker.vn`.
- **Chuẩn hóa Trang 404 & Gắn `noindex` cho `/error`**: Cập nhật `resources/Frontend/js/Pages/Error.vue` với số hotline chính xác của HH Maker (`0906 636 425`), tiêu đề nhận diện thương hiệu HH Maker, và bổ sung `<Head>` meta `robots` với giá trị `noindex, nofollow`.
- **Chuẩn hóa Social Share Preview (OG:Image & Favicon)**: Cập nhật các thẻ `<meta property="og:image">`, `<meta name="twitter:image">`, `<link rel="icon">`, `<link rel="apple-touch-icon">` trỏ chính xác về bộ nhận diện HH Maker (`/og-image.jpg`, 1200x630px và `/assets/images/header/hh-maker-icon.svg`), bổ sung cache-busting `?v=20261109`, và thiết lập lớp bảo vệ không cho phép fallback về hình ảnh cũ của Funkystyle nếu database còn lưu dữ liệu cũ.

## Capabilities

### New Capabilities
- `seo-critical-fixes`: Khắc phục toàn bộ các lỗi SEO kỹ thuật khẩn cấp và thiết lập cơ chế XML Sitemap chuẩn, 301 Canonical Redirect, routing tin tức ổn định và trang lỗi có noindex.

### Modified Capabilities
*(Không có - các capability hiện có trong hệ thống không thay đổi contract)*

## Impact

- **Mã nguồn Backend**:
  - `public/robots.txt`: Cập nhật dòng khai báo Sitemap.
  - `app/Http/Controllers/Frontend/PostController.php`: Sửa query lọc bài viết không còn gọi method lỗi `whereCourses()`.
  - `routes/frontend.php`: Chuẩn hóa route giới thiệu, loại bỏ khai báo route thừa, thêm 301 redirect.
  - `app/Http/Controllers/Frontend/SitemapController.php` & `packages/bed-package-essentials/sitemap/src/Sitemap.php`: Bổ sung trang chủ và cơ chế lọc bỏ route nhạy cảm/redirect.
  - `app/Http/Middleware/RedirectWwwToNonWww.php` (hoặc trong Kernel web group): Bổ sung chuyển hướng dự phòng 301 cho `www`.
- **Mã nguồn Frontend**:
  - `resources/Frontend/js/Pages/Posts/Index.vue` & `Show.vue`: Import `CardBlog.vue`, cập nhật nhãn thương hiệu HH Maker Blog.
  - `resources/Frontend/js/Pages/Error.vue`: Cập nhật hotline `0906 636 425`, thêm Inertia Head `<meta name="robots" content="noindex, nofollow">`.
- **Hạ tầng / Vhost**:
  - Cấu hình Nginx mẫu redirect 301 vĩnh viễn cho `www.hhmaker.vn` sang `https://hhmaker.vn`.
- **Cơ sở dữ liệu (Database)**: KHÔNG THAY ĐỔI SCHEMA, KHÔNG CHẠY MIGRATION MỚI, KHÔNG DROP/TRUNCATE.
- **Tương thích ngược**: Không ảnh hưởng đến dữ liệu hay CMS Admin hiện có.

## Safe Guard Boundary

Tuân thủ nghiêm ngặt quy tắc an toàn toàn cục của dự án:
1. **CẤM TỰ Ý XÓA FILE**: Tuyệt đối không xóa bất kỳ file hoặc thư mục nào trong dự án.
2. **CẤM TỰ Ý THAY ĐỔI DATABASE**: Không chạy lệnh làm thay đổi hoặc mất mát dữ liệu (`migrate:fresh`, `db:seed`, drop/truncate).
3. **CẤM PHÁ VỠ KIẾN TRÚC HIỆN CÓ**: Giữ nguyên kiến trúc BED CMS (Laravel + Inertia + Vue 3).
4. **PLANNING BOUNDARY**: Subagent chỉ lập bộ tài liệu OpenSpec (proposal, specs, design, tasks) và thực hiện xác thực `openspec validate`. Tuyệt đối không can thiệp sửa code dự án trong bước lập kế hoạch này.
