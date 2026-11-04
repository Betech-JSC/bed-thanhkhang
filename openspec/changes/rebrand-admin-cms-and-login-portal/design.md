# Design: Kiến trúc Kỹ thuật & Giao diện Admin Rebranding & Login Portal

## 1. Tổng quan Kiến trúc

Tài liệu này mô tả chi tiết thiết kế kỹ thuật và các thay đổi mã nguồn nhằm chuyển đổi nhận diện Admin CMS sang chuẩn xưởng in 3D HH Maker:
1. Cấu hình môi trường (`.env`, `config/app.php`).
2. Giao diện điều hướng Sidebar (`SidebarItems.vue`, `logo-footer.svg`).
3. Layout nền tảng khách (`Guest.vue`).
4. Trang xác thực quản trị (`Login.vue`).

---

## 2. Chi tiết Thành phần Kỹ thuật

### 2.1. Cấu hình Thương hiệu (`.env` & `config/app.php`)

```env
APP_NAME='HH Maker'
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
VITE_APP_NAME="${APP_NAME}"
```

Trong `config/app.php`:
```php
'name' => env('APP_NAME', 'HH Maker'),
```

### 2.2. SidebarItems Admin CMS (`SidebarItems.vue`)

- **Thẻ liên kết Logo**:
```vue
<div class="flex items-center flex-shrink-0 p-5 border-b border-gray-800">
    <Link href="/" class="flex items-center w-full p-2 space-x-2">
        <img
            src="/assets/images/header/figma-header-logo.svg"
            alt="HH Maker Logo"
            class="max-h-10 w-auto object-contain"
        />
    </Link>
</div>
```
- **Fallback Vector**:
Sao chép nội dung vector từ `public/assets/images/header/figma-header-logo.svg` sang `public/assets/svg/logo-footer.svg` để chống các điểm gọi cũ bị cache.

### 2.3. Layout Khách (`Guest.vue`)

Sử dụng phong cách Dark Tech xưởng in 3D với hiệu ứng Heated Bed Grid:
```vue
<template>
    <div
        class="relative flex flex-col items-center justify-center min-h-screen p-4 overflow-hidden bg-[#0B0D19]"
    >
        <!-- Heated Bed Mesh Grid Background Pattern -->
        <div
            class="absolute inset-0 pointer-events-none opacity-40"
            :style="{
                backgroundImage: `
                    linear-gradient(to right, rgba(64, 73, 104, 0.15) 1px, transparent 1px),
                    linear-gradient(to bottom, rgba(64, 73, 104, 0.15) 1px, transparent 1px)
                `,
                backgroundSize: '40px 40px',
            }"
        ></div>

        <!-- Ambient Radial Glows -->
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-[#2970FF]/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-[#1E56CC]/15 rounded-full blur-3xl pointer-events-none"></div>

        <!-- Slot Content -->
        <div class="relative z-10 w-full max-w-md">
            <slot />
        </div>
    </div>
</template>
```

### 2.4. Trang Đăng nhập Admin (`Login.vue`)

Xây dựng giao diện Glassmorphism với cấu trúc:
1. **Header**:
   - Logo HH Maker 3D phát sáng tinh tế (`drop-shadow-[0_0_12px_rgba(41,112,255,0.3)]`).
   - Badge `HH MAKER WORKSHOP OS • v2.0` với LED xanh nhấp nháy (`animate-pulse`).
   - Tiêu đề in hoa `ĐĂNG NHẬP HỆ THỐNG`.
   - Phụ đề `Hệ thống Quản trị & Điều hành Xưởng in 3D`.
2. **Form Fields**:
   - Trường Email: Gồm label trắng sáng, container input chứa icon email SVG ở vị trí `absolute left-3.5`, thẻ input có `pl-11` trên nền `#0F121F` và viền `#2B334E`, chuyển màu `#2970FF` khi focus.
   - Trường Mật khẩu: Gồm label, icon ổ khóa SVG ở vị trí `absolute left-3.5`, nút bấm toggle ẩn/hiện mật khẩu hoặc hiển thị trực quan.
   - Hiển thị lỗi xác thực nếu có `form.errors.email` hoặc `form.errors.password`.
3. **Remember Me**:
   - Custom checkbox với nền `#0F121F` và màu tích `#2970FF`.
4. **Submit Button**:
   - Nút gradient `bg-gradient-to-r from-[#2970FF] to-[#1E56CC]` với hiệu ứng shadow xanh công nghệ `shadow-[0_4px_16px_rgba(41,112,255,0.35)]`.
   - Spinner loading hiển thị khi `form.processing`.
5. **Footer**:
   - Dòng ghi chú bảo mật `Authorized Personnel Only • Additive Manufacturing & Rapid Prototyping System`.
