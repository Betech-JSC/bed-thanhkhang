# Design: Chuẩn Hóa Design Tokens, Typography Scale và Grid Container Theo Figma

## 1. Tổng Quan Kiến Trúc
Hệ thống giao diện người dùng HH Maker được xây dựng trên nền tảng:
- Laravel 10 + Inertia.js + Vue 3
- Tailwind CSS v3 với phong cách Dark Tech (Figma Dark BG `#111322`)
- Font chính: `Inter` kết hợp `Be Vietnam Pro` (fallback sans-serif)

Change này nâng cấp tầng nền tảng (foundation layer) bao gồm:
1. Breakpoints của `.container` và các lớp tiện ích `max-w-*` độc lập.
2. Hệ thống token cỡ chữ ngữ nghĩa (Semantic Typography Scale).
3. Khử răng cưa và nở chữ toàn diện bằng `antialiased` tại thẻ `<body>` gốc.
4. Bảng màu Figma Dark Tech chuẩn hóa (`#111322`, `#171B30`, `#0B0D19`, `#404968`, `#5D6B98`, `#2970FF`, `#FAFAFA`, `#B9C0D4`, `#D1E0FF`).

---

## 2. Chi Tiết Cấu Hình Tokens

### 2.1. Container Grid & Max-Width Utilities
Trong `resources/Frontend/tailwind.config.js`:
```javascript
container: {
    center: true,
    padding: {
        DEFAULT: '1rem',
        md: '1.5rem',
        lg: '2rem',
        xl: '2rem',
        '2xl': '2rem',
    },
    screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1440px',
    },
},
maxWidth: {
    canvas: '1440px',
    content: '1152px',
    hero: '1279px',
    '60vw': '60vw',
    '70vw': '70vw',
    '80vw': '80vw',
    '90vw': '90vw',
}
```

### 2.2. Semantic Typography Scale
Phân cấp font chuẩn Figma:
| Token Class | Font Size | Line Height | Letter Spacing | Font Weight | Figma Usage |
|---|---|---|---|---|---|
| `text-h1` | 38px | 46px | -0.01em | 700 (Bold) | Hero H1, Section H2 chính |
| `text-h2` | 28px | 36px | 0 | 700 (Bold) | Tiêu đề bài viết, bước quy trình |
| `text-h3` | 20px | 28px | 0 | 600 (SemiBold) | Phân nhóm, card title |
| `text-body` | 16px | 26px | 0 | 400 (Regular) | Đoạn văn bản mô tả chính |
| `text-caption` | 14px | 20px | 0 | 400 (Regular) | Nhãn phụ, meta, tab |
| `text-caption-sm` | 13px | 16px | 0 | 600 (SemiBold) | Badge, chip, nhãn nút nhỏ |
| `text-caption-xs` | 12px | 16px | 0 | 600 (SemiBold) | Chú thích nhỏ nhất |

Vẫn giữ nguyên các token display đã có (`display-hero`, `display-banner`, `display-section`, v.v.) để duy trì tương thích 100% không làm gãy các component đã áp dụng.

### 2.3. Bảng Màu Figma Dark Tech
Định nghĩa trong `theme.extend.colors`:
- Nền dark chính: `'dark-bg': '#111322'`
- Thẻ card dark: `'dark-card': '#171B30'`
- Khung Hero Card: `'dark-hero': '#0B0D19'`
- Viền công nghệ: `'dark-border': '#404968'`, `'dark-border-light': '#5D6B98'`
- Xanh thương hiệu: `'primary.DEFAULT': '#2970FF'`, `'brand-blue': '#2970FF'`
- Text Figma:
  + `'text-white': '#FAFAFA'`
  + `'text-secondary': '#B9C0D4'`
  + `'text-blue': '#D1E0FF'`

### 2.4. Global Antialiasing Tại app.blade.php
Tại `resources/Frontend/views/app.blade.php`:
```html
<body class="antialiased">
```
Class `antialiased` của Tailwind tương ứng với:
```css
-webkit-font-smoothing: antialiased;
-moz-osx-font-smoothing: grayscale;
```
Giúp font chữ hiển thị sắc nét trên nền tối `#111322`, khử triệt để hiện tượng nở chữ (font blooming) trên trình duyệt Chromium/Windows.

---

## 3. Chiến Lược Kiểm Thử (Verification Strategy)
1. **Kiểm tra cú pháp cấu hình**:
   - Chạy `yarn frontend:build` đảm bảo quá trình biên dịch Vite/PostCSS/Tailwind thành công (Exit code 0).
2. **Kiểm thử E2E Playwright**:
   - Chạy test bộ `tests/e2e/header-strict-figma.spec.ts` kiểm tra nền `#111322`, `antialiased`, kích thước logo, và hành vi trên Desktop/Mobile.
   - Xuất screenshots xác thực.
