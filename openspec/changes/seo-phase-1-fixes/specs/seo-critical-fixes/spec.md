# Spec Delta: seo-critical-fixes

## Purpose

Cung cấp chuẩn kỹ thuật cho hệ thống điều hướng tìm kiếm (Search Engine Crawling & Indexation) của HH Maker, đảm bảo toàn bộ URL được thu thập chính xác, loại trừ các liên kết rác, giải quyết triệt để lỗi server 500, xung đột trùng lặp nội dung và chuyển hướng tên miền chuẩn.

## ADDED Requirements

### Requirement: Robots.txt Sitemap Declaration
Hệ thống SHALL khai báo đường dẫn Sitemap XML chính xác của domain `https://hhmaker.vn/sitemap.xml` trong file `public/robots.txt` và cho phép mọi User-agent hợp lệ thu thập dữ liệu công khai.

#### Scenario: Crawler reads robots.txt
- **WHEN** công cụ tìm kiếm hoặc người dùng gửi yêu cầu HTTP GET tới `/robots.txt`
- **THEN** hệ thống trả về mã trạng thái HTTP 200 với nội dung chứa dòng `Sitemap: https://hhmaker.vn/sitemap.xml` và không chứa bất kỳ tham chiếu nào tới domain `funkystyle.vn`.

### Requirement: News and Posts Route Reliability
Hệ thống SHALL phục vụ trang danh sách bài viết (`/tin-tuc` và `/en/posts`) cũng như trang chi tiết bài viết với mã phản hồi HTTP 200 OK ổn định, loại bỏ hoàn toàn các exception nội bộ do gọi method không tồn tại.

#### Scenario: User visits news listing in Vietnamese
- **WHEN** người dùng hoặc bot tìm kiếm truy cập URL `https://hhmaker.vn/tin-tuc`
- **THEN** hệ thống trả về HTTP 200 OK với giao diện danh sách bài viết Inertia Vue và danh sách bài viết hoạt động, không phát sinh lỗi HTTP 500.

#### Scenario: User visits news listing in English
- **WHEN** người dùng hoặc bot tìm kiếm truy cập URL `https://hhmaker.vn/en/posts`
- **THEN** hệ thống trả về HTTP 200 OK với tiêu đề và giao diện phù hợp với locale tiếng Anh.

#### Scenario: API request for news data
- **WHEN** client gửi HTTP GET request kèm header `Accept: application/json` tới route tin tức
- **THEN** hệ thống trả về dữ liệu JSON chứa danh sách bài viết `posts` và HTTP status 200.

### Requirement: Homepage Inclusion in XML Sitemap
Hệ thống SHALL luôn luôn đưa URL trang chủ gốc `https://hhmaker.vn/` vào file `sitemap.xml` với mức độ ưu tiên cao nhất (`priority: 1.0`) và tần suất cập nhật hàng ngày (`changefreq: daily`).

#### Scenario: Search engine requests sitemap.xml
- **WHEN** bot tìm kiếm tải file `https://hhmaker.vn/sitemap.xml`
- **THEN** nội dung XML trả về chứa node `<url>` có `<loc>https://hhmaker.vn/</loc>` (hoặc URL gốc được cấu hình qua APP_URL) với `<priority>1.0</priority>` và `<changefreq>daily</changefreq>`.

### Requirement: Canonical Redirection for About Us Page
Hệ thống SHALL chỉ định nghĩa duy nhất một URL đích chính thống cho trang Giới thiệu là `/gioi-thieu` (và `/en/about-us` đối với tiếng Anh). Mọi biến thể URL cũ bao gồm `/about`, `/about-us` (tiếng Việt), và `/ve-chung-toi` MUST được chuyển hướng bằng mã trạng thái HTTP 301 Permanent Redirect về URL chuẩn.

#### Scenario: User accesses legacy /about URL
- **WHEN** người dùng hoặc crawler truy cập URL `/about`
- **THEN** hệ thống phản hồi HTTP 301 chuyển hướng tới `/gioi-thieu`.

#### Scenario: User accesses /ve-chung-toi URL
- **WHEN** người dùng hoặc crawler truy cập URL `/ve-chung-toi`
- **THEN** hệ thống phản hồi HTTP 301 chuyển hướng tới `/gioi-thieu`.

#### Scenario: User accesses canonical /gioi-thieu URL
- **WHEN** người dùng truy cập URL `/gioi-thieu`
- **THEN** hệ thống trả về HTTP 200 OK hiển thị trang Giới thiệu của HH Maker với thẻ `<link rel="canonical" href="https://hhmaker.vn/gioi-thieu">`.

### Requirement: XML Sitemap Sanitization and Private Route Exclusion
Trình tạo Sitemap XML SHALL tự động lọc bỏ toàn bộ các route nội bộ, route chuyển hướng 301, route xác thực khách hàng, route tìm kiếm động và route báo lỗi hệ thống, nhằm tránh lãng phí ngân sách thu thập dữ liệu (Crawl Budget) và cảnh báo Google Search Console.

#### Scenario: Inspection of generated sitemap URLs
- **WHEN** hệ thống kết xuất nội dung XML tại `/sitemap.xml`
- **THEN** trong toàn bộ danh sách thẻ `<loc>` tuyệt đối KHÔNG chứa bất kỳ URL nào sau đây: `/dang-ky`, `/dang-nhap`, `/dashboard`, `/quen-mat-khau`, `/dat-lai-mat-khau`, `/ho-so-ca-nhan`, `/search`, `/search-v2`, `/tim-kiem`, `/error`, `/demo`, `/routes`, `/robots.txt`, `/about`, `/ve-chung-toi`.

### Requirement: Non-WWW to WWW Canonical Domain Redirection
Hệ thống SHALL đảm bảo mọi truy cập thông qua tên miền phụ `www.hhmaker.vn` đều được chuyển hướng 301 vĩnh viễn về tên miền gốc không có www (`https://hhmaker.vn`), giữ nguyên toàn bộ đường dẫn URI và query parameters.

#### Scenario: User requests URL via www subdomain
- **WHEN** client gửi yêu cầu tới `https://www.hhmaker.vn/san-pham`
- **THEN** hệ thống phản hồi HTTP 301 Redirect với header `Location: https://hhmaker.vn/san-pham`.

### Requirement: Error Page Standardization and Robots Noindex
Trang hiển thị lỗi 404 và trang tĩnh `/error` SHALL hiển thị thông tin thương hiệu HH Maker cùng hotline chính thức `0906 636 425`, đồng thời MUST luôn chèn chỉ thị robots meta `noindex, nofollow` để ngăn bot tìm kiếm đưa trang lỗi vào chỉ mục.

#### Scenario: Client encounters 404 Not Found
- **WHEN** người dùng truy cập một URL không tồn tại trên website
- **THEN** hệ thống trả về mã lỗi HTTP 404, hiển thị giao diện báo lỗi có hotline `0906 636 425`, tiêu đề chứa "HH Maker", và mã nguồn trang chứa thẻ `<meta name="robots" content="noindex, nofollow">`.

#### Scenario: Client accesses /error route directly
- **WHEN** người dùng hoặc bot truy cập trực tiếp route `/error`
- **THEN** mã nguồn HTML rendered MUST chứa thẻ `<meta name="robots" content="noindex, nofollow">`.

### Requirement: Social Share Thumbnail (OG Image) and Favicon HH Maker Branding
Hệ thống SHALL cung cấp đầy đủ thông tin metadata OpenGraph và Twitter Card với hình ảnh đại diện (thumbnail / cover image) chuẩn thương hiệu HH Maker (`/og-image.jpg`, kích thước 1200x630px, định dạng JPG chất lượng cao) và Favicon chính thức của HH Maker (`/assets/images/header/hh-maker-icon.svg`, `/favicon-32x32.png`), đồng thời TUYỆT ĐỐI KHÔNG fallback về hình ảnh cũ của Funkystyle khi chia sẻ link lên Telegram, Zalo, Facebook hoặc các mạng xã hội.

#### Scenario: Link sharing preview on social media
- **WHEN** người dùng gửi link `https://hhmaker.vn/` lên Telegram, Facebook, Zalo, Twitter hoặc LinkedIn
- **THEN** crawler của các nền tảng nhận được thẻ `<meta property="og:image" content="https://hhmaker.vn/og-image.jpg?v=20261109">` và `<meta name="twitter:image" content="https://hhmaker.vn/og-image.jpg?v=20261109">` với kích thước 1200x630px mang thương hiệu HH Maker, không còn hiển thị hình ảnh quả khổ qua hay chữ Funkystyle.

#### Scenario: Stale database setting protection
- **WHEN** trường `seo_image` trong bảng `settings` database lưu đường dẫn ảnh Funkystyle cũ (chứa `funkystyle`, `khoqua`, hoặc `copy.jpg`)
- **THEN** hệ thống tự động loại bỏ giá trị cũ này và sử dụng ảnh mặc định `/og-image.jpg` của HH Maker cho thẻ `og:image`.
