# Spec: Dynamic Banners & Global CTA Integration

## 1. Mục tiêu

Đặc tả kỹ thuật cho việc đồng bộ, kết nối động và cơ chế fallback an toàn của toàn bộ hệ thống Banner & CTA trên website HH Maker.

---

## 2. Vị trí Banner & Cấu hình Slider (`config/slider.php`)

Hệ thống quản lý banner sử dụng model `JamstackVietnam\Slider\Models\Slider` và cấu hình tại `config/slider.php`. Các vị trí chuẩn bao gồm:

| Mã vị trí (`value`) | Tiêu đề hiển thị Admin (`title`) | Loại (`banner`) | Phạm vi sử dụng |
|---|---|---|---|
| `HOME_SLIDER` | Slider trang chủ | `false` (Collection) | Hero Slider trên Trang chủ |
| `BANNER_ABOUT_HERO` | Hero Banner trang Giới thiệu | `true` (Single item) | Section 1 trang Giới thiệu (`About/Index.vue`) |
| `BANNER_PROCESS_HERO` | Hero Banner trang Quy trình | `true` (Single item) | Section 1 trang Quy trình (`Process/Index.vue` & `ProcessHero.vue`) |
| `BANNER_GLOBAL_CTA` | Banner CTA kêu gọi tư vấn chân trang | `true` (Single item) | Khối CTA chân trang Sản phẩm, Dịch vụ, Quy trình, Giới thiệu |
| `BANNER_HOME_FOUNDER` | Banner Câu chuyện Founder trang chủ | `true` (Single item) | Khối Câu chuyện Thương hiệu / Founder trang chủ |

Các vị trí tàn dư không dùng (`BANNER_COURSE`, `BANNER_RESOURCE`, `BANNER_PRIZE`) bị gỡ bỏ khỏi cấu hình.

---

## 3. Cấu trúc Dữ liệu Banner (Data Contract)

Phương thức `Slider::getByPosition($positionDisplay)` trả về mảng dữ liệu (hoặc `null` nếu không có banner khả dụng). Cấu trúc dữ liệu:

```json
{
  "id": 1,
  "title": "ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH",
  "subtitle": "Giới thiệu về HH Maker",
  "description": "Chúng tôi kết nối nghệ thuật 3D với công nghệ chế tác hiện đại...",
  "primary_cta_text": "Khám phá ngay",
  "primary_cta_link": "/san-pham",
  "secondary_cta_text": null,
  "secondary_cta_link": null,
  "link": "/san-pham",
  "target": null,
  "is_video": false,
  "position_display": "BANNER_ABOUT_HERO",
  "image": {
    "url": "/assets/images/about/hero-about.png",
    "alt": "Xưởng chế tác 3D HH Maker"
  },
  "image_mobile": {
    "url": null,
    "alt": null
  },
  "custom_fields": null
}
```

---

## 4. Đặc tả Hành vi & Cơ chế Fallback Figma An Toàn

### 4.1. Hero Banner Trang Giới thiệu (`BANNER_ABOUT_HERO`)
- **Backend**: `HistoryController::index` query `Slider::getByPosition('BANNER_ABOUT_HERO')` và truyền qua prop `heroBanner`.
- **Frontend**: `About/Index.vue` nhận `heroBanner`:
  - **Category / Subtitle**: `heroBanner?.subtitle || 'Giới thiệu về HH Maker'`
  - **Title / Headline**: `heroBanner?.title || 'ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH'`
  - **Description**: `heroBanner?.description || 'Chúng tôi kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tưởng sơ khai có thể trở thành sản phẩm bạn cầm trên tay.'`
  - **Image**: `heroBanner?.image?.url || '/assets/images/about/hero-about.png'`
  - **Alt Text**: `heroBanner?.image?.alt || 'Xưởng chế tác 3D HH Maker'`
  - **Badge / Stamp**: Giữ nguyên hiệu ứng xoay tròn SVG "FROM IDEAS TO REALITY".

### 4.2. Hero Banner Trang Quy trình (`BANNER_PROCESS_HERO`)
- **Backend**: `ProcessController::index` query `Slider::getByPosition('BANNER_PROCESS_HERO')` và truyền qua prop `heroBanner`.
- **Frontend**: `Process/Index.vue` truyền tiếp sang `ProcessHero.vue`:
  - **Category / Subtitle**: `banner?.subtitle || 'QUY TRÌNH LÀM VIỆC'`
  - **Title / Headline**: `banner?.title || 'RÕ RÀNG TỪNG BƯỚC\nCHỈN CHU TỪNG SẢN PHẨM'`
  - **Description**: `banner?.description || 'Từ ý tưởng ban đầu đến thành phẩm hoàn thiện, HH Maker cùng bạn làm rõ nhu cầu, thống nhất thiết kế và chi phí trước khi gia công. Mỗi giai đoạn đều được trao đổi cụ thể để bạn dễ theo dõi và chủ động trong quá trình đặt làm.'`
  - **Image**: `banner?.image?.url || '/images/process/process_hero_bg.png'`
  - **Alt Text**: `banner?.image?.alt || 'HH Maker Quy trình làm việc'`
  - **Badge 360° Rotating Button**: Giữ nguyên SVG xoay vòng và nút cuộn mượt xuống stepper.

### 4.3. Banner CTA Chân trang Toàn cục (`BANNER_GLOBAL_CTA`)
- **Backend**: Middleware `HandleInertiaFrontendRequests.php` query `Slider::getByPosition('BANNER_GLOBAL_CTA')` và share toàn cục qua:
  - `$page.props.globalCtaBanner`
  - `$page.props.data.banner.BANNER_GLOBAL_CTA`
- **Frontend**: Các component CTA (`ProductCtaBanner.vue`, `ServiceCta.vue`, `ProcessCtaBanner.vue`, `About/Index.vue` Section 5):
  - **Style Container**: Nền gradient Cobalt Blue chuẩn Figma: `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`.
  - **Title**: `cta?.title || 'Tạo nên phiên bản mang dấu ấn riêng'` (hoặc `'Ý tưởng của bạn, bước tiếp theo của chúng ta.'` cho trang About).
  - **Description**: `cta?.description || 'Gửi hình ảnh tham khảo hoặc chia sẻ ý tưởng để HH Maker cùng bạn định hình sản phẩm mong muốn. Chúng tôi sẽ tư vấn giải pháp phù hợp với phong cách, nhu cầu và ngân sách của bạn.'`
  - **Button Text**: `cta?.primary_cta_text || 'Nhận tư vấn ngay'`
  - **Button Link**: `cta?.primary_cta_link || cta?.link || route('contact')`

---

## 5. Tiêu chí Nghiệm thu (Acceptance Criteria)

1. Hệ thống không bị lỗi crash hoặc vỡ giao diện kể cả khi cơ sở dữ liệu hoàn toàn chưa có bản ghi banner nào (`null safe`).
2. Khi quản trị viên tạo hoặc cập nhật bản ghi tại vị trí tương ứng trong CMS Slider, giao diện frontend hiển thị đúng nội dung mới nhất.
3. Không làm ảnh hưởng đến các form liên hệ, routing Ziggy hoặc SEO metadata.
4. Toàn bộ các lệnh build (`yarn backend:build`, `yarn frontend:build`) và test (`php artisan test tests/Feature/ContactFormTest.php`) thành công 100%.
