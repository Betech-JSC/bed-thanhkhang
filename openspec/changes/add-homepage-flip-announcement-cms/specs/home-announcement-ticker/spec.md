# Spec Delta: home-announcement-ticker

## Purpose
Cung cấp thanh thông báo chuyển động cơ học 3D (Mechanical Split-Flap Ticker) tại Trang chủ, quản trị linh hoạt qua BED CMS Slider, hỗ trợ song ngữ VI/EN và tích hợp chuyển tiếp nhịp nhàng giữa Hero Banner và Danh mục sản phẩm nổi bật.

## ADDED Requirements

### Requirement: Cấu hình Quản trị CMS Thông báo Ticker
Hệ thống SHALL cung cấp vị trí hiển thị `HOME_ANNOUNCEMENT_TICKER` trong cấu hình slider (`config/slider.php`) để quản trị viên có thể quản lý các mẩu tin thông báo trang chủ trong màn hình BED Admin:
1. Mỗi mẩu tin thông báo hỗ trợ đa ngôn ngữ (Tiếng Việt và Tiếng Anh) gồm: Tiêu đề (`title`), Phụ đề/Nhãn phân loại (`subtitle` hoặc `custom_fields.badge`), Đường dẫn liên kết (`link`), Thuộc tính đích (`target`: `_self` hoặc `_blank`), và Thứ tự sắp xếp (`position_sort`).
2. Quản trị viên có thể cấu hình trạng thái (`status: ACTIVE/INACTIVE`) và thời hạn hiển thị (`started_at`, `ended_at`).

#### Scenario: Quản trị viên tạo mới thông báo ticker song ngữ
- **WHEN** Quản trị viên truy cập màn hình Slider trong Admin, chọn vị trí `Thanh thông báo lật trang chủ (Ticker)` và nhập nội dung tiếng Việt ("Freeship đơn hàng in 3D từ 500k") cùng tiếng Anh ("Free shipping for 3D printing orders from 500k") rồi bấm lưu
- **THEN** Hệ thống lưu bản ghi vào bảng `sliders` và các bản dịch tương ứng vào `slider_translations`, sẵn sàng phân phối ra trang chủ

#### Scenario: Thông báo hết hạn tự động ẩn
- **WHEN** Một mẩu tin có trường `ended_at` trước thời điểm hiện tại (`ended_at < now()`)
- **THEN** Hệ thống loại bỏ mẩu tin khỏi danh sách hiển thị ngoài trang chủ mà không cần can thiệp thủ công

---

### Requirement: Truy vấn và Phân phối Dữ liệu Backend
`HomeController@index` SHALL truy vấn danh sách các thông báo ticker đang hoạt động thông qua `Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER')`:
1. Dữ liệu được lọc theo ngôn ngữ hiện hành (`current_locale()`) và trạng thái khả dụng (`active()` và `available()`).
2. Danh sách thông báo được đưa vào mảng dữ liệu trang chủ và lưu vào bộ nhớ đệm `home_page_data_{locale}` với thời gian sống (TTL) 3600 giây.
3. Dữ liệu được truyền xuống giao diện Inertia.js dưới dạng prop `announcements`.

#### Scenario: Truy vấn thông báo theo ngôn ngữ Tiếng Việt
- **WHEN** Người dùng truy cập Trang chủ tại đường dẫn `/` (hoặc `/vi`)
- **THEN** Controller trả về danh sách `announcements` chứa các tiêu đề và nội dung bằng Tiếng Việt

#### Scenario: Truy vấn thông báo theo ngôn ngữ Tiếng Anh
- **WHEN** Người dùng truy cập Trang chủ tại đường dẫn `/en`
- **THEN** Controller trả về danh sách `announcements` chứa các tiêu đề và nội dung tương ứng bằng Tiếng Anh

---

### Requirement: Hiển thị Giao diện Thanh Thông Báo Lật 3D Trang Chủ
Giao diện Trang chủ (`Home/Index.vue`) SHALL tích hợp component `AnnouncementTicker.vue` nằm giữa `HeroSection` và `FeaturedProductsCarousel`:
1. Bố cục thanh thông báo tuân thủ hệ màu Dark Theme của dự án (`bg-[#0B0D19]` hoặc `bg-[#111322]`, viền mảnh `border-white/10`, đổ bóng và điểm nhấn ánh kim/neon nhẹ).
2. Spacing được đồng bộ hài hòa với chiều rộng container chính (`max-w-[1279px]`), không gây đứt gãy nhịp thị giác giữa Hero và Khung sản phẩm nổi bật.

#### Scenario: Hiển thị đầy đủ vị trí trên giao diện Desktop
- **WHEN** Trang chủ được tải trên màn hình độ phân giải Desktop (>= 1024px) có ít nhất 1 thông báo khả dụng
- **THEN** Thanh thông báo hiển thị căn giữa chuẩn với kích thước container 1279px, nằm ngay dưới Hero Section và phía trên Carousel Sản phẩm nổi bật

#### Scenario: Tự động co giãn tối ưu trên giao diện Mobile
- **WHEN** Trang chủ được tải trên màn hình thiết bị di động (< 768px)
- **THEN** Thanh thông báo giữ padding lề an toàn (`px-4`), cỡ chữ co giãn vừa vặn (text-sm/xs) và không bị tràn khung nhìn (no horizontal overflow)

---

### Requirement: Hiệu ứng Chuyển Động Cơ Học Split-Flap và Chu kỳ Tự Động Xoay Vòng
Component `AnnouncementTicker.vue` SHALL triển khai hiệu ứng hoạt họa lật thẻ cơ học 3D (Mechanical 3D Split-Flap):
1. Khi có từ 2 thông báo trở lên, component tự động lật sang thông báo kế tiếp theo chu kỳ định sẵn (mặc định 4.5 đến 5.0 giây).
2. Quá trình lật sử dụng kỹ thuật CSS 3D Transforms (`perspective`, `rotateX`, `backface-visibility: hidden`) mô phỏng cơ chế lật nửa trên/nửa dưới của bảng lật sân bay/nhà ga cổ điển.
3. Khi người dùng di chuột lên thanh thông báo (`mouseenter`), chu kỳ tự động lật SHALL tạm dừng; khi rời chuột (`mouseleave`), chu kỳ tự động tiếp tục đếm.
4. Khi người dùng kích hoạt tùy chọn giảm chuyển động trên hệ điều hành (`prefers-reduced-motion: reduce`), hiệu ứng lật 3D SHALL tự động suy giảm thành chuyển đổi mờ dần (opacity crossfade) nhẹ nhàng.

#### Scenario: Chu kỳ tự động lật khi có nhiều mẩu tin
- **WHEN** Trang chủ hiển thị danh sách gồm 3 mẩu tin thông báo
- **THEN** Cứ sau 5 giây, mẩu tin hiện tại thực hiện hiệu ứng lật 3D để chuyển sang mẩu tin tiếp theo theo thứ tự vòng lặp vô tận

#### Scenario: Tạm dừng khi người dùng rê chuột
- **WHEN** Người dùng rê con trỏ chuột vào vùng thanh thông báo đang hiển thị
- **THEN** Bộ đếm thời gian tạm dừng và thông báo giữ nguyên vị trí để người dùng đọc chi tiết hoặc bấm liên kết

#### Scenario: Giữ tĩnh khi chỉ có duy nhất 1 mẩu tin
- **WHEN** Danh sách chỉ có duy nhất 1 mẩu tin hoạt động
- **THEN** Thanh thông báo hiển thị tĩnh mẩu tin đó và không kích hoạt vòng lặp lật thẻ

---

### Requirement: Điều Hướng và Khả Năng Tương Tác
Mỗi mẩu tin trong thanh thông báo SHALL hỗ trợ điều hướng nếu có khai báo trường `link`:
1. Nếu `link` là đường dẫn nội bộ (bắt đầu bằng `/`), hệ thống sử dụng component `Link` của Inertia để điều hướng mượt mà không tải lại toàn trang.
2. Nếu `link` là đường dẫn bên ngoài (bắt đầu bằng `http://` hoặc `https://`), hệ thống mở liên kết trong tab mới kèm các thuộc tính bảo mật `rel="noopener noreferrer"`.
3. Nếu không có `link`, thanh thông báo hiển thị dạng văn bản thông tin thuần túy và không tạo con trỏ tay trỏ link.

#### Scenario: Bấm vào thông báo có link điều hướng nội bộ
- **WHEN** Người dùng nhấp chuột vào một thông báo có đường dẫn `/services`
- **THEN** Trình duyệt chuyển hướng mượt mà đến trang Dịch vụ thông qua Inertia Router mà không tải lại trang

#### Scenario: Bấm vào thông báo có link bên ngoài
- **WHEN** Người dùng nhấp chuột vào thông báo có đường dẫn `https://zalo.me/...` với thuộc tính `target="_blank"`
- **THEN** Trình duyệt mở tab mới an toàn tới địa chỉ tương ứng

---

### Requirement: Xử Lý Trạng Thái Rỗng và Suy Giảm Mượt Mà (Graceful Degradation)
Khi không có thông báo nào được cấu hình hoặc tất cả thông báo đều ở trạng thái ẩn/hết hạn:
1. Component `AnnouncementTicker.vue` SHALL tự động ẩn hoàn toàn (sử dụng `v-if` dựa trên độ dài mảng `announcements`).
2. Trang chủ SHALL duy trì khoảng cách đệm tự nhiên giữa Hero Section và Featured Products Carousel, không để lại bất kỳ khoảng trống rỗng, viền thừa hoặc khung vỡ nào.

#### Scenario: Không có thông báo hoạt động trong hệ thống
- **WHEN** Danh sách `announcements` trả về từ Backend là mảng rỗng `[]` hoặc `null`
- **THEN** Component `AnnouncementTicker.vue` không render bất kỳ phần tử DOM nào và khoảng cách giữa Hero và Carousel Sản phẩm nổi bật hiển thị tự nhiên
