# Proposal: Thanh Thông Báo Lật 3D (Mechanical Split-Flap Ticker) Trang Chủ Tích Hợp CMS & Đa Ngôn Ngữ

## Why
Hiện tại, trên Trang chủ HH Maker (`Home/Index.vue`), luồng thị giác giữa khối Hero Section (`HeroSection.vue`) và khối Sản phẩm nổi bật (`FeaturedProductsCarousel.vue`) đang chuyển tiếp trực diện mà thiếu một điểm dừng thị giác (visual pause) để truyền tải nhanh các thông điệp cập nhật tức thời: ưu đãi in 3D theo tuần, cập nhật tiến độ công xưởng, thông báo giao hàng hỏa tốc, hay các tin tức tiêu biểu. 

Đồng thời, quản trị viên doanh nghiệp cần một công cụ CMS trực quan trong hệ thống quản trị BED Admin để chủ động tạo, sửa, sắp xếp thứ tự và đặt lịch hiển thị (started_at, ended_at) các mẩu tin ngắn dạng ticker song ngữ (Tiếng Việt & Tiếng Anh). Việc bổ sung thanh thông báo với hiệu ứng chuyển động Mechanical 3D Split-Flap (lật thẻ cơ học 3D) tinh tế, mượt mà, đúng chuẩn thẩm mỹ *Quiet & High-Trust UI* sẽ tăng tính tương tác, nâng cao sự tin cậy công nghệ của HH Maker mà không làm rối mắt hay loãng trải nghiệm người dùng.

## What Changes
- **Cấu hình CMS Slider Position mới**: Khai báo position `HOME_ANNOUNCEMENT_TICKER` trong `config/slider.php` để kích hoạt giao diện quản trị slider đa ngôn ngữ trong BED Admin, cho phép quản lý tiêu đề, phụ đề, icon/badge, link điều hướng và thời gian hiển thị.
- **Tích hợp Backend & Cache Layer**: Cập nhật `HomeController.php` để truy vấn danh sách thông báo hoạt động từ `Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER')`, đồng bộ vào bộ nhớ đệm đa ngôn ngữ `home_page_data_{locale}` (TTL 3600s), đảm bảo 0 impact đến tốc độ tải trang.
- **Xây dựng Vue 3 Component `AnnouncementTicker.vue`**: Tạo mới component độc lập tại `resources/Frontend/js/Pages/Home/Components/AnnouncementTicker.vue` (hoặc `Components/Base/AnnouncementTicker.vue`) hiện thực hiệu ứng Mechanical 3D Split-Flap lật thẻ cơ học, hỗ trợ:
  - Tự động xoay vòng tin theo chu kỳ định sẵn (mặc định 4.5s - 5s).
  - Tạm dừng chuyển động khi rê chuột (`pause-on-hover`) và hỗ trợ chạm vuốt trên mobile.
  - Tôn trọng thuộc tính hệ thống `prefers-reduced-motion` (chuyển sang fade đơn giản).
  - Điều hướng an toàn bằng liên kết nội bộ (`Link` Inertia) hoặc ngoại bộ (tab mới bảo mật `rel="noopener noreferrer"`).
- **Căn chỉnh Nhịp điệu Bố cục Trang chủ (Layout Spacing Harmony)**: Chèn thanh thông báo vào giữa `HeroSection` và `FeaturedProductsCarousel` trên `Home/Index.vue`, điều chỉnh padding dọc hài hòa (`py-3 lg:py-4`, âm hoặc dương nhịp với Hero padding) để đảm bảo không xô lệch khoảng cách chuẩn Figma.
- **Graceful Degradation (Suy giảm mượt mà)**: Khi không có thông báo nào được kích hoạt trong CMS (hoặc rỗng), component tự động ẩn hoàn toàn (v-if), trả lại bố cục chuẩn liền mạch không phát sinh khoảng trắng dư thừa.

## Capabilities

### New Capabilities
- `home-announcement-ticker`: Cung cấp thanh thông báo lật 3D (Mechanical Split-Flap Ticker) trên Trang chủ được cấp dữ liệu từ CMS BED Slider `HOME_ANNOUNCEMENT_TICKER`, hỗ trợ hiển thị song ngữ VI/EN, tương tác hover/pause, chuyển động CSS 3D chân thực và tự động ẩn khi không có dữ liệu.

### Modified Capabilities
<!-- Không có spec cũ nào bị thay đổi hành vi cốt lõi (home-and-services vẫn duy trì các section hiện hữu; announcement ticker là bổ sung mới) -->

## Impact
- **Backend & Config**:
  - `config/slider.php`: Thêm entry `HOME_ANNOUNCEMENT_TICKER` (không làm ảnh hưởng các vị trí slider hiện có).
  - `app/Http/Controllers/Frontend/HomeController.php`: Bổ sung prop `announcements` trong hàm `index()` và làm mới cache `home_page_data_{locale}` khi có cập nhật.
- **Frontend**:
  - `resources/Frontend/js/Pages/Home/Index.vue`: Nhận prop `announcements` và render component trung gian.
  - `resources/Frontend/js/Pages/Home/Components/AnnouncementTicker.vue`: Tạo mới file component Vue 3.
  - `resources/lang/{vi,en}/ui.php`: Bổ sung các nhãn UI tĩnh phụ trợ nếu cần (ví dụ nhãn badge "THÔNG BÁO" / "NOTICE").
- **Admin**:
  - Quản trị viên sử dụng trực tiếp màn hình Quản lý Slider sẵn có trong CMS (`/admin/sliders?filters[position]=HOME_ANNOUNCEMENT_TICKER`), không cần tạo thêm migration bảng database mới vì bảng `sliders` và `slider_translations` đã có đủ trường cần thiết.
- **Performance & SEO**:
  - Tối ưu GPU rendering với `transform: rotateX()` và `backface-visibility: hidden` để đạt 60fps trên cả mobile.
  - Dữ liệu được nhúng sẵn từ SSR/Inertia props, đảm bảo crawler đọc được text thông báo ngay lần đầu.
