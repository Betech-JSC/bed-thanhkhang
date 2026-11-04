# Technical Design: Mechanical 3D Split-Flap Announcement Ticker with CMS

## Context
Dự án HHMaker sử dụng kiến trúc Laravel + Inertia.js (Vue 3) với hệ thống module BED CMS (`jamstackvietnam/essentials`).
- Hiện tại, quản trị nội dung trình diễn dạng banner/slider đã được chuẩn hóa qua package `JamstackVietnam\Slider\Models\Slider` và cấu hình tại `config/slider.php`.
- Trang chủ (`Home/Index.vue`) nhận dữ liệu tập trung qua `HomeController::index()` với cơ chế cache `home_page_data_{locale}`.
- Hai khối giao diện hiện tại là `HeroSection.vue` (padding dưới `pb-8 md:pb-12`) và `FeaturedProductsCarousel.vue` (padding trên `py-10 md:py-16`). Việc đặt thanh thông báo giữa hai khối đòi hỏi xử lý khoảng đệm chính xác để không phá vỡ nhịp thị giác (vertical rhythm) của thiết kế chuẩn Figma.

## Goals / Non-Goals

**Goals:**
- Tận dụng 100% cơ chế Slider CMS có sẵn để quản trị viên có thể thêm, sửa, xóa, đặt thời hạn và dịch song ngữ (VI/EN) các mẩu tin mà không phải can thiệp database schema.
- Xây dựng component Vue 3 `AnnouncementTicker.vue` mô phỏng hiệu ứng cơ học Mechanical 3D Split-Flap lật thẻ mượt mà, đạt 60fps trên GPU.
- Hỗ trợ đầy đủ các tính năng UX cao cấp: pause-on-hover, tự động điều hướng link nội bộ qua Inertia hoặc link ngoài qua tab mới an toàn, và tự động fallback sang hiệu ứng fade khi người dùng bật `prefers-reduced-motion`.
- Căn chỉnh bố cục hài hòa với container `max-w-[1279px]` và tự động thu gọn sạch sẽ (`v-if`) khi không có thông báo nào khả dụng.

**Non-Goals:**
- Không tạo bảng cơ sở dữ liệu riêng biệt cho Announcement (sử dụng trực tiếp bảng `sliders` và `slider_translations`).
- Không tạo trang xem danh sách toàn bộ lịch sử thông báo (chỉ hiển thị thanh ticker trên trang chủ).
- Không nhúng âm thanh cơ học click-clack split-flap (để đảm bảo nguyên tắc *Quiet & High-Trust UI*, tránh gây xao nhãng hoặc phiền hà cho người dùng).

## Decisions

### 1. Tận dụng Cấu hình Position trong `config/slider.php`
- **Quyết định**: Bổ sung key `HOME_ANNOUNCEMENT_TICKER` vào `config/slider.php` với `banner => false`.
  ```php
  'HOME_ANNOUNCEMENT_TICKER' => [
      'title' => 'Thanh thông báo lật trang chủ (Ticker)',
      'value' => 'HOME_ANNOUNCEMENT_TICKER',
      'banner' => false,
  ],
  ```
- **Lý do**:
  - `Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER')` đã hỗ trợ sẵn việc lọc `active()`, kiểm tra khoảng thời gian `available()`, sắp xếp `orderBy('position_sort', 'desc')`, và tự động nạp quan hệ `translations` tương ứng với ngôn ngữ hiện hành (`current_locale()`).
  - Giao diện Admin quản trị Slider của BED CMS tự động hiển thị dropdown bộ lọc vị trí mới mà không cần code thêm controller backend.
- **Giải pháp thay thế đã xem xét**:
  - Tạo bảng riêng `announcements`: Phức tạp hóa hệ thống, đòi hỏi chạy migration mới và tạo thêm CRUD UI trong admin.
  - Lưu vào `config/` hoặc file JSON tĩnh: Quản trị viên không thể tự biên tập qua giao diện web.

### 2. Kiến trúc Data Contract & Cache Flow trong `HomeController`
- **Quyết định**:
  Trong `HomeController::index()`:
  ```php
  $announcements = Slider::getByPosition('HOME_ANNOUNCEMENT_TICKER');
  ```
  Đưa `announcements` vào mảng trả về của closure `Cache::remember('home_page_data_' . $locale, 3600, ...)`.
- **Dữ liệu phân phối xuống Vue**:
  Mỗi phần tử trong `announcements` có cấu trúc:
  ```typescript
  interface AnnouncementItem {
    id: number;
    title: string;          // Nội dung thông báo chính
    subtitle?: string;       // Nhãn phân loại (e.g. "HOT", "ƯU ĐÃI", "NOTICE")
    link?: string;           // URL điều hướng
    target?: string;         // "_self" | "_blank"
    position_sort?: number;
  }
  ```
- **Lý do**: Tái sử dụng cache hiện có, không tốn thêm câu truy vấn khi người dùng truy cập trang chủ. Khi quản trị viên cập nhật slider trong Admin, cache `home_page_data_{locale}` được xóa/cập nhật tự động hoặc hết hạn theo TTL.

### 3. Hiệu ứng Mechanical 3D Split-Flap Animation Engine
- **Quyết định**: Hiện thực bằng CSS 3D Transforms thuần kết hợp Vue transition / reactive state (`currentIdx`), không dùng thư viện nặng bên thứ ba.
- **Chi tiết kỹ thuật**:
  - Khung bao ngoài áp dụng `perspective: 1000px`.
  - Mỗi thẻ hiển thị gồm 2 nửa: Nửa trên (`top-flap`) và nửa dưới (`bottom-flap`), đường phân tách ở giữa mô phỏng rãnh gập kim loại cổ điển.
  - Hoạt cảnh lật: Thẻ trước đó gập xuống `rotateX(-90deg)` với `transform-origin: bottom center`, để lộ nửa thẻ mới đang lật xuống từ `rotateX(90deg)` về `0deg`.
  - Áp dụng `backface-visibility: hidden` và `will-change: transform` để GPU tăng tốc phần cứng, đạt 60fps mượt mà trên cả iPhone và Android cấu hình vừa phải.
  - Chu kỳ: 5000ms mỗi tin (trong đó thời gian lật kéo dài 600ms với easing cubic-bezier chuẩn cơ học `cubic-bezier(0.4, 0.0, 0.2, 1)`).
- **Fallback Accessibility**:
  - Khi `@media (prefers-reduced-motion: reduce)` được kích hoạt: Tắt toàn bộ `rotateX`, chuyển sang hiệu ứng mờ dần nhẹ nhàng (`transition: opacity 0.3s ease`).

### 4. Căn chỉnh Spacing & Bố cục Thị Giác (Layout Spacing Harmony)
- **Quyết định**:
  - Vị trí: Đặt ngay giữa `<HeroSection />` và `<FeaturedProductsCarousel />` trên `Home/Index.vue`.
  - Spacing:
    * Container chính: `w-full max-w-[1279px] mx-auto px-4 sm:px-6 lg:px-0`.
    * Khoảng cách biên: `my-4 md:my-6`.
    * Chiều cao thanh thông báo: `h-[46px] sm:h-[50px]`, dạng viên thuốc (pill) hoặc bo góc mềm `rounded-[10px]`.
    * Nền: `bg-[#0E1322]/80 backdrop-blur-md border border-white/10 shadow-lg`.
    * Huy hiệu (Badge): Nằm bên trái với nền điểm nhấn (`bg-primary/20 text-primary border border-primary/30 font-semibold px-2.5 py-0.5 rounded text-xs tracking-wider`).
    * Con trỏ & Tương tác: Hiển thị icon mũi tên chuyển hướng nhỏ bên phải khi có `link`.
- **Lý do**: Kết nối tự nhiên giữa sắc tối của Hero Banner và nền của Carousel Sản phẩm nổi bật, tạo cảm giác mạch lạc công nghệ cao nhưng không lấn át cụm nội dung chính.

### 5. Graceful Degradation & Empty State
- **Quyết định**:
  - Trong `Home/Index.vue`:
    ```vue
    <AnnouncementTicker
        v-if="announcements && announcements.length > 0"
        :announcements="announcements"
    />
    ```
  - Khi mảng rỗng: Hoàn toàn không render DOM, không chiếm dụng chiều cao, không phát sinh margin thừa.

## Risks / Trade-offs

| Rủi ro (Risk) | Khả năng xảy ra | Tác động | Biện pháp giảm thiểu (Mitigation) |
| :--- | :--- | :--- | :--- |
| **Animation Jank trên thiết bị di động yếu** | Thấp | Giao diện bị khựng khi lật thẻ | Dùng `will-change: transform`, hạn chế shadow động phức tạp trong lúc lật, chỉ áp dụng CSS 3D trên container nội bộ có kích thước cố định. |
| **Cache chưa cập nhật khi sửa trong Admin** | Trung bình | Người dùng chưa thấy tin mới ngay | Khi lưu/sửa Slider tại vị trí `HOME_ANNOUNCEMENT_TICKER`, controller hoặc model trigger xóa cache `home_page_data_*`. |
| **Văn bản thông báo quá dài bị tràn chữ trên mobile** | Trung bình | Vỡ khung hiển thị hoặc che mất badge | Áp dụng `truncate` / `line-clamp-1` kèm tooltip title hoặc cho phép cuộn ngang/ngắt dòng hợp lý; giới hạn độ dài khuyến nghị trong CMS hint. |
| **Xung đột sự kiện hover trên màn hình cảm ứng** | Thấp | Ticker bị dừng mãi mãi khi người dùng vừa chạm ngón tay | Lắng nghe cả `touchstart` / `touchend` để tiếp tục chu kỳ sau 3 giây không chạm. |

## Migration Plan
1. **Zero Database Migration**: Không thay đổi schema cơ sở dữ liệu.
2. **Cấu hình**: Cập nhật file `config/slider.php`.
3. **Backend**: Cập nhật `HomeController.php` để lấy data và cache.
4. **Frontend**: Tạo component `AnnouncementTicker.vue` và tích hợp vào `Home/Index.vue`.
5. **Rollback Strategy**: Nếu cần hoàn tác, chỉ cần bỏ dòng component trong `Home/Index.vue` và key trong `config/slider.php`, không để lại tàn dư dữ liệu lỗi.
