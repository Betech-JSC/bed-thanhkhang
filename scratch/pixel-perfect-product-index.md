# Báo Cáo Kiểm Toán Giao Diện Pixel-Perfect (`/opsx-pixel-perfect`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Phạm vi quét**: **Page-Scoped (`--page Product/Index`)**
- **Thời gian quét**: `2026-11-04 13:58:40`
- **Chế độ**: `Strict Read-Only (Không tự ý sửa/xóa file)`
- **Kết luận tổng thể**: **CẦN XỬ LÝ (3 HẠNG MỤC)** (`[+] ADD`: 1 | `[-] REMOVE`: 2 | `[=] KEEP`: 6)
- **File Page mục tiêu**: `resources/Frontend/js/Pages/Product/Index.vue`
- **Các Component con thuộc Page**: `resources/Frontend/js/Pages/Product/Components/ProductCard.vue`, `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue`, `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue`, `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`

---

## 1. Bảng `[+] THÊM GÌ (ADD)` — Các quy chuẩn / thành phần cần bổ sung

| # | Quy tắc | Tọa độ File | Chi tiết cần thêm (`[+] ADD`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 6 (W3C `overflow-x: clip`) | `resources/Frontend/scss/app.scss` | Thêm `html, body { overflow-x: clip; }` và ẩn `::-webkit-scrollbar:horizontal`. |

---

## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các bẫy CSS / phần tử dư thừa cần gỡ bỏ

| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết lỗi phát hiện | Hành động gỡ bỏ (`[-] REMOVE`) |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Quy tắc 2 (`@tailwindcss/aspect-ratio` Trap) | `resources/Frontend/tailwind.config.js:1077` | Đang bật plugin `@tailwindcss/aspect-ratio` làm ghi đè `.aspect-square` bằng `padding-bottom: 100%`. | Bỏ `require('@tailwindcss/aspect-ratio')` khỏi `plugins` và dùng `aspectRatio: { square: '1 / 1' }` native. |
| 2 | Quy tắc 6 (`overflow-x-hidden` trên Page/Layout) | `resources/Frontend/js/Pages/Product/Index.vue:2` | Phát hiện `overflow-x-hidden` tại `<div class="bg-[#111322] text-[#FAFAFA] min-h-screen overflow-x-hidden selection` dễ gây 2 thanh cuộn dọc và làm chết `sticky`. | Bỏ `overflow-x-hidden` trên wrapper của Page/Layout, sử dụng `overflow-x: clip` toàn cục. |

---

## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP)` — Các thành phần đã chuẩn 100% Figma

| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 3 & 6 (Page Structure `Index.vue`) | `resources/Frontend/js/Pages/Product/Index.vue` | Page `resources/Frontend/js/Pages/Product/Index.vue` (4 child imports) không vi phạm `overflow-x-hidden` hay double top-offset. |
| 2 | Quy tắc 1 & 3 (Child Component `ProductCard.vue`) | `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` | Component con `ProductCard.vue` thuộc Page `Product/Index` đã đồng bộ cấu trúc Figma. |
| 3 | Quy tắc 1 & 3 (Child Component `ProductFilter.vue`) | `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue` | Component con `ProductFilter.vue` thuộc Page `Product/Index` đã đồng bộ cấu trúc Figma. |
| 4 | Quy tắc 1 & 3 (Child Component `ProductPagination.vue`) | `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue` | Component con `ProductPagination.vue` thuộc Page `Product/Index` đã đồng bộ cấu trúc Figma. |
| 5 | Quy tắc 1 & 3 (Child Component `ProductCtaBanner.vue`) | `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue` | Component con `ProductCtaBanner.vue` thuộc Page `Product/Index` đã đồng bộ cấu trúc Figma. |
| 6 | Quy tắc 5 (Retina Logo & Bộ 6 Favicon) | `public/` & `public/assets/images/luxi/logo-header.png` | Đầy đủ 6 file Favicon chuẩn và Logo độ phân giải cao `@4x` (`392×264px`). |

---

## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)

> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên.
> - Nếu đang ở **Chế độ A (1 Đoạn Hội Thoại = 1 Page)**: Sau khi bạn **Confirm**, Agent sẽ tiến hành sửa và nghiệm thu Playwright trực tiếp cho Page này ngay trong đoạn hội thoại hiện tại.
> - Nếu đang ở **Chế độ B (Hub Tổng Nhiều Page)**: Sau khi bạn **Confirm**, Agent sẽ xuất bộ **Handoff Prompts** theo từng Page để đem sang các đoạn hội thoại con thực thi.
