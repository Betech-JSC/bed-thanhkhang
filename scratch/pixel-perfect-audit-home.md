# Báo Cáo Kiểm Toán Giao Diện Pixel-Perfect (`/opsx-pixel-perfect`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-thanhkhang`
- **Phạm vi quét**: **Page-Scoped Parallel Safe (`--page Home`)**
- **Thời gian quét**: `2026-11-22 14:18:19`
- **Chế độ**: `Strict Read-Only & 100% Parallel-Safe (Không ghi đè tab khác)`
- **Kết luận tổng thể**: **PASS 100% PIXEL-PERFECT** (`[+] ADD`: 0 | `[-] REMOVE`: 0 | `[=] KEEP`: 18)

### 🔒 Ranh Giới Cô Lập File Khi Chạy Song Song Nhiều Tab (`Parallel File Isolation`)
- ✅ **`[ALLOWED TO EDIT IN THIS TAB]` (Chỉ được phép sửa trong tab `Home` này)**: `resources/Frontend/js/Pages/Home/Components/BlogSection.vue`, `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`, `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`, `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`, `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue`, `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`, `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`, `resources/Frontend/js/Pages/Home/Index.vue`, `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`, `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`, `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`, `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`, `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue`, `resources/Frontend/js/Pages/Home/Components/BlogSection.vue`, `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`
- ⛔ **`[LOCKED FOR PARALLEL SAFETY]` (Cấm tự ý sửa để không đụng các tab Page khác đang chạy song song)**: `resources/Frontend/tailwind.config.js`, `resources/Frontend/scss/app.scss`, `resources/Frontend/js/Layouts/Default.vue`, `resources/Frontend/js/Layouts/Guest.vue`, `resources/Frontend/js/Components/Layout/Header.vue`, `resources/Frontend/js/Components/Layout/Footer.vue`

---

## 1. Bảng `[+] THÊM GÌ (ADD)` — Các quy chuẩn / thành phần cần bổ sung

> Không có hạng mục nào bị thiếu (`0` mục cần thêm).

---

## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các bẫy CSS / phần tử dư thừa cần gỡ bỏ

> Tuyệt vời! Không phát hiện bẫy CSS hay phần tử dư thừa nào (`0` mục cần bỏ).

---

## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP)` — Các thành phần đã chuẩn 100% Figma

| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 2 (Native `aspect-square` 1:1) | `resources/Frontend/tailwind.config.js` | Đã gỡ bỏ `@tailwindcss/aspect-ratio` và dùng native CSS `aspect-ratio: 1 / 1` chuẩn xác. |
| 2 | Quy tắc 6 (W3C `overflow-x: clip` Single Scrollbar) | `resources/Frontend/scss/app.scss` | Đã cấu hình `html, body { overflow-x: clip; }` bảo toàn `position: sticky` và duy nhất 1 thanh cuộn dọc. |
| 3 | Quy tắc 3 & 6 (Target `BlogSection.vue`) | `resources/Frontend/js/Pages/Home/Components/BlogSection.vue` | File `resources/Frontend/js/Pages/Home/Components/BlogSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 4 | Quy tắc 3 & 6 (Target `BrandStorySection.vue`) | `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` | File `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 5 | Quy tắc 3 & 6 (Target `ContactSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` | File `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 6 | Quy tắc 3 & 6 (Target `HeroSection.vue`) | `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` | File `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 7 | Quy tắc 3 & 6 (Target `ProcessSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` | File `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 8 | Quy tắc 3 & 6 (Target `ProductsSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` | File `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 9 | Quy tắc 3 & 6 (Target `ServicesSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` | File `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 10 | Quy tắc 3 & 6 (Target `Index.vue`) | `resources/Frontend/js/Pages/Home/Index.vue` | File `resources/Frontend/js/Pages/Home/Index.vue` không vi phạm `overflow-x-hidden` hay double top-offset. |
| 11 | Quy tắc 1 & 3 (Page Child `HeroSection.vue`) | `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` | Component riêng `HeroSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 12 | Quy tắc 1 & 3 (Page Child `BrandStorySection.vue`) | `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` | Component riêng `BrandStorySection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 13 | Quy tắc 1 & 3 (Page Child `ProductsSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` | Component riêng `ProductsSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 14 | Quy tắc 1 & 3 (Page Child `ServicesSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` | Component riêng `ServicesSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 15 | Quy tắc 1 & 3 (Page Child `ProcessSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` | Component riêng `ProcessSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 16 | Quy tắc 1 & 3 (Page Child `BlogSection.vue`) | `resources/Frontend/js/Pages/Home/Components/BlogSection.vue` | Component riêng `BlogSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 17 | Quy tắc 1 & 3 (Page Child `ContactSection.vue`) | `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` | Component riêng `ContactSection.vue` thuộc Page `Home` đã đồng bộ cấu trúc Figma. |
| 18 | Quy tắc 5 (Retina Logo & Bộ 6 Favicon) | `public/` & `public/assets/images/luxi/logo-header.png` | Đầy đủ 6 file Favicon chuẩn và Logo độ phân giải cao `@4x` (`392×264px`). |

---

## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)

> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên.
> - Khi bạn **Confirm**, Agent tại tab này chỉ sửa các file nằm trong danh sách **`[ALLOWED TO EDIT IN THIS TAB]`** để đảm bảo an toàn 100% khi bạn chạy song song nhiều tab cùng lúc!
