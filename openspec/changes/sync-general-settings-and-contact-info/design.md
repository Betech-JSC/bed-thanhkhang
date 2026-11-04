# Technical Design: Đồng bộ Cài đặt chung CMS & Thông tin liên hệ Xưởng in 3D

## 1. Kiến trúc luồng dữ liệu (Data Flow)

```
[Quản trị viên]
       │
       ▼ Nhập thông tin (Zalo, Giờ làm việc, Facebook, TikTok)
[General.vue (Admin CMS)]
       │
       ▼ POST /admin/settings/general
[SettingController::store()]
       │
       ▼ Validate qua Setting::rules('general')
[settings table (MySQL - group: 'general')]
       │
       ▼ Middleware HandleInertiaFrontendRequests::share()
[$page.props.global (Inertia Frontend Props)]
       │
       ├─────────────────────────┬───────────────────────────┐
       ▼                         ▼                           ▼
[Footer.vue]              [Contact.vue]            [ContactSection.vue]
- Giữ nguyên email        - Thẻ Zalo               - Icon Facebook
  lamhonghai123@gmail.com - Thẻ Hotline            - Icon TikTok
- Hotline, Địa chỉ        - Thẻ Email
- Giờ làm việc
- Facebook, TikTok
```

## 2. Chi tiết các thành phần

### 2.1. Admin CMS Setting Page & Controller
- `packages/bed-package-essentials/core/resources/Backend/js/Pages/Settings/General.vue`:
  + Sử dụng component `<Field>` chuẩn của Jamstack Core để render 4 trường text mới.
  + Nhóm hiển thị: đặt ngay sau `general_phone` trong `card-body` đầu tiên.
- `packages/bed-package-essentials/core/src/Models/Setting.php`:
  + Cung cấp validation rule cho 4 trường mới để hàm `store()` không loại bỏ chúng khỏi payload.
- `packages/bed-package-essentials/core/src/helpers.php`:
  + Trong hàm `setting_bar()`, đổi `'points' => true` thành `'points' => false`.

### 2.2. Frontend Integration & Safety Guards
- `resources/Frontend/js/Components/Footer.vue`:
  + Bổ sung computed properties:
    * `displayWorkingHours`: lấy từ `this.$page.props.global?.general_working_hours` hoặc fallback `'Thứ 2 - Thứ 7: 08:00 - 18:00'`.
    * `facebookUrl`: lấy từ `this.$page.props.global?.general_facebook` hoặc fallback `'https://facebook.com'`.
    * `tiktokUrl`: lấy từ `this.$page.props.global?.general_tiktok` hoặc fallback `'https://tiktok.com'`.
  + Giữ nguyên thẻ liên kết:
    ```vue
    <a href="mailto:lamhonghai123@gmail.com" class="group flex items-center gap-2.5 ...">
        ...
        <span>lamhonghai123@gmail.com</span>
    </a>
    ```
- `resources/Frontend/js/Pages/Contact.vue`:
  + Bổ sung computed properties:
    * `displayZaloUrl`: chuẩn hóa link Zalo từ số hoặc link đầy đủ.
    * `displayPhone`: lấy từ `this.$page.props.global?.general_phone` hoặc `'0906636425'`.
    * `displayEmail`: lấy từ `this.$page.props.global?.general_email` hoặc `'info@hhmaker.vn'`.
- `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`:
  + Chuyển mảng `socialLinks` từ data tĩnh sang computed property đọc từ `$page.props.global`.

## 3. Chiến lược kiểm thử & Xác thực (Verification Strategy)
- Build Backend Assets: `yarn backend:build`
- Build Frontend Assets: `yarn frontend:build`
- PHPUnit Feature Tests: `php artisan test tests/Feature/ContactFormTest.php`
- Kiểm tra tính bảo toàn của email cá nhân `lamhonghai123@gmail.com` trong source code Footer.
