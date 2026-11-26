# Spec: Trang Giới Thiệu Thành Khang (`about-page`)

## Purpose

Cung cấp trang Giới thiệu doanh nghiệp B2B Thành Khang Tecsat tại `/gioi-thieu` (Tiếng Việt) và `/en/about-us` (Tiếng Anh) khớp 100% thiết kế Figma `979:30278`, kết nối toàn bộ nội dung và hình ảnh động từ hệ thống CMS (`Sliders` + `Settings` song ngữ) thông qua cơ chế khởi tạo dữ liệu tự động không phá hủy.

## ADDED Requirements

### Requirement: Khung trang Giới thiệu & Layout Shell chuẩn Design System
Trang Giới thiệu tại `/gioi-thieu` và `/en/about-us` MUST render bên trong `MainLayout`, sử dụng nền sáng `bg-surface text-ink-body overflow-x-clip`, hiển thị tuần tự đúng 5 section chuẩn Figma `979:30278` mà không chứa mảng dữ liệu mock tĩnh hay hiệu ứng ẩn phần tử `opacity: 0`.

#### Scenario: Tải trang Giới thiệu song ngữ không tràn ngang và không lỗi console
- **WHEN** người dùng truy cập `/gioi-thieu` hoặc `/en/about-us` trên Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** trang hiển thị đầy đủ Header và Footer của `MainLayout`, nền sáng chuẩn Design System, `document.documentElement.scrollWidth <= window.innerWidth`, và `0` console errors.

#### Scenario: Dữ liệu bài viết trên Controller không lẫn bản ghi FAQ
- **WHEN** endpoint trang Giới thiệu trả dữ liệu JSON hoặc Inertia props
- **THEN** danh sách `posts` (nếu có) chỉ chứa các bản ghi bài viết tin tức (`type = 'POST'`), tuyệt đối không chứa bản ghi câu hỏi thường gặp (`type = 'FAQ'`).

### Requirement: Section 1 — Hero Banner Giới thiệu động từ CMS (`979:30279`)
Section 1 MUST hiển thị dữ liệu động từ vị trí banner `BANNER_ABOUT_HERO` song ngữ VI/EN (phụ đề, tiêu đề chính `<h1>`, đoạn mô tả, ảnh desktop/mobile), được nạp tự động qua migration idempotent khi triển khai và khóa tỷ lệ khung hình `aspect-[375/480] md:aspect-[1440/582]`.

#### Scenario: Hiển thị Hero Banner chuẩn tỷ lệ và nội dung song ngữ từ CMS
- **WHEN** người dùng mở `/gioi-thieu` (VI) hoặc `/en/about-us` (EN) sau khi chạy `php artisan migrate`
- **THEN** vùng Hero hiển thị đúng phụ đề, tiêu đề `<h1>`, mô tả theo ngôn ngữ hiện tại từ `BANNER_ABOUT_HERO`, khung banner giữ tỷ lệ `aspect-[375/480] md:aspect-[1440/582]` trong container `.app-container-wide` (`px-4 md:px-8`), và ảnh nền `/assets/images/about/about-hero-banner.png` tải thành công (`naturalWidth > 0`) với `w-full h-full object-cover object-center`.

### Requirement: Section 2 — Câu chuyện Thành Khang & Bộ 3 ảnh Collage động (`979:30312`)
Section 2 MUST đọc nội dung văn bản song ngữ VI/EN từ cấu hình cài đặt chung CMS (`about_story_*`) và 3 hình ảnh minh họa từ vị trí `ABOUT_STORY_GALLERY` (thứ tự `position_sort` `30, 20, 10`), cho phép quản trị viên chỉnh sửa và lưu trực tiếp trong trang Cài đặt chung & Quản lý Banner mà không bị mất dữ liệu.

#### Scenario: Hiển thị bố cục 2 cột Câu chuyện Thành Khang và khóa tỷ lệ 3 ảnh collage
- **WHEN** người dùng xem Section 2 trên Desktop và Mobile
- **THEN** cột trái hiển thị 1 ảnh trên (`aspect-[358/210] md:aspect-[493/280]`) và 2 ảnh dưới (`aspect-[173/110] md:aspect-[239/146]`) với `gap-3 md:gap-4` không vỡ hình, cột phải hiển thị đầy đủ nhãn phụ, tiêu đề, 3 đoạn mô tả và câu cam kết theo ngôn ngữ VI/EN từ CMS trong khung `.app-container-narrow` (`px-4 md:px-8`).

#### Scenario: Lưu cấu hình Câu chuyện Thành Khang trong Admin CMS
- **WHEN** quản trị viên cập nhật các trường `about_story_*` tại `/admin/settings/general` và bấm Lưu
- **THEN** hệ thống xác thực hợp lệ, lưu giá trị mới vào bảng `settings`, xóa cache trang Giới thiệu và phản ánh ngay nội dung mới trên `/gioi-thieu` & `/en/about-us`.

### Requirement: Section 3 — Sứ mệnh và Tầm nhìn trên nền Banner chuẩn tỷ lệ (`979:30329`)
Section 3 MUST hiển thị ảnh nền không dính chữ từ vị trí `BANNER_ABOUT_MISSION_VISION` và 2 khối nội dung (Sứ mệnh, Tầm nhìn) từ vị trí `ABOUT_MISSION_VISION_ITEMS` (`position_sort` `20, 10`) song ngữ VI/EN, khóa tỷ lệ `aspect-[375/560] md:aspect-[1440/460]`.

#### Scenario: Hiển thị 2 cột Sứ mệnh và Tầm nhìn trên Desktop và xếp chồng trên Mobile
- **WHEN** người dùng cuộn tới Section 3 ở cả Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** ảnh nền `/assets/images/about/about-mission-vision-bg.png` hiển thị sắc nét với `w-full h-full object-cover object-bottom`, 2 khối Sứ mệnh và Tầm nhìn hiển thị đúng phụ đề, tiêu đề, mô tả từ CMS theo bố cục 2 cột trên Desktop (`gap-6 md:gap-12 lg:gap-[120px]`) và xếp dọc không tràn khung trên Mobile.

### Requirement: Section 4 — Dấu ấn và Cam kết 4 chỉ số nổi bật (`979:30340`)
Section 4 MUST hiển thị 4 thẻ chỉ số cam kết từ vị trí `ABOUT_STATS_ITEMS` (`position_sort` `40, 30, 20, 10`: `100+`, `Toàn Quốc` / EN tương ứng, `CO/CQ`, `24/7`) bằng typography thuần trên nền `bg-[#F8FAFC]`.

#### Scenario: Hiển thị lưới 4 chỉ số cân đối trên mọi kích thước màn hình
- **WHEN** người dùng xem Section 4 trên Desktop và Mobile
- **THEN** giao diện render lưới `grid-cols-2 lg:grid-cols-4 gap-3 md:gap-6 lg:gap-8` trong `.app-container-narrow` (`px-4 md:px-8`), mỗi mục hiển thị nhãn mô tả màu `#364152` phía trên và giá trị nổi bật màu `#005EA4` phía dưới đúng thứ tự từ CMS.

### Requirement: Section 5 — Đồng hành cùng khách hàng & 2 thẻ dịch vụ có CTA động (`979:30345`)
Section 5 MUST đọc tiêu đề phân đoạn từ cài đặt chung CMS (`about_companion_*`) và 2 thẻ đồng hành từ vị trí `ABOUT_COMPANION_CARDS` (`position_sort` `20, 10`), trong đó mỗi thẻ khóa tỷ lệ ảnh `aspect-[358/180] md:aspect-[560/220]` và có nút CTA đọc đường dẫn động từ `primary_cta_link` (mặc định `/lien-he` cho VI và `/en/contact` cho EN).

#### Scenario: Hiển thị 2 thẻ Đồng hành và điều hướng nút CTA sang trang Liên hệ
- **WHEN** người dùng xem Section 5 và bấm vào nút `"Liên hệ tư vấn"` hoặc `"Liên hệ hỗ trợ"` (hoặc bản tiếng Anh tương ứng)
- **THEN** 2 ảnh card hiển thị đúng tỷ lệ `aspect-[358/180] md:aspect-[560/220]` không méo, và nút CTA điều hướng chính xác tới `/lien-he` (ở tiếng Việt) hoặc `/en/contact` (ở tiếng Anh) hoặc URL tùy chỉnh do Admin cấu hình trong CMS.

### Requirement: Tính bất biến Idempotent của Migration & Bảo toàn Dữ liệu Hệ thống
Toàn bộ 5 migration khởi tạo dữ liệu trang Giới thiệu MUST an toàn tuyệt đối khi chạy lặp lại hoặc rollback, bảo toàn 100% dữ liệu sản phẩm, danh mục, chính sách, bài viết và banner các trang khác.

#### Scenario: Chạy `php artisan migrate` trên môi trường mới hoặc chạy lại nhiều lần
- **WHEN** thực thi `php artisan migrate` trên database hiện hành hoặc VPS triển khai mới
- **THEN** đủ dữ liệu 5 section trang Giới thiệu được khởi tạo tự động mà không nhân bản dòng, không ghi đè các giá trị khác rỗng mà Admin đã chỉnh sửa, và số lượng `products`, `product_categories`, `policies`, cùng các `sliders` của Trang Chủ được giữ nguyên 100%.
