# Báo Cáo Kiểm Toán Giao Diện Pixel-Perfect (`/opsx-pixel-perfect`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Phạm vi quét**: **Full Project (`Toàn bộ các Page`)**
- **Thời gian quét**: `2026-11-04 14:09:04`
- **Chế độ**: `Strict Read-Only (Không tự ý sửa/xóa file)`
- **Kết luận tổng thể**: **CẦN XỬ LÝ (5 HẠNG MỤC)** (`[+] ADD`: 1 | `[-] REMOVE`: 4 | `[=] KEEP`: 1)

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
| 2 | Quy tắc 6 (`overflow-x-hidden` trên Page/Layout) | `resources/Frontend/js/Pages/About/Index.vue:10` | Phát hiện `overflow-x-hidden` tại `<div class="bg-dark-bg text-white overflow-x-hidden">` dễ gây 2 thanh cuộn dọc và làm chết `sticky`. | Bỏ `overflow-x-hidden` trên wrapper của Page/Layout, sử dụng `overflow-x: clip` toàn cục. |
| 3 | Quy tắc 6 (`overflow-x-hidden` trên Page/Layout) | `resources/Frontend/js/Pages/Contact.vue:10` | Phát hiện `overflow-x-hidden` tại `<div class="bg-[#111322] text-white min-h-screen overflow-x-hidden selection:bg-` dễ gây 2 thanh cuộn dọc và làm chết `sticky`. | Bỏ `overflow-x-hidden` trên wrapper của Page/Layout, sử dụng `overflow-x: clip` toàn cục. |
| 4 | Quy tắc 6 (`overflow-x-hidden` trên Page/Layout) | `resources/Frontend/js/Pages/Product/Index.vue:2` | Phát hiện `overflow-x-hidden` tại `<div class="bg-[#111322] text-[#FAFAFA] min-h-screen overflow-x-hidden selection` dễ gây 2 thanh cuộn dọc và làm chết `sticky`. | Bỏ `overflow-x-hidden` trên wrapper của Page/Layout, sử dụng `overflow-x: clip` toàn cục. |

---

## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP)` — Các thành phần đã chuẩn 100% Figma

| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 5 (Retina Logo & Bộ 6 Favicon) | `public/` & `public/assets/images/luxi/logo-header.png` | Đầy đủ 6 file Favicon chuẩn và Logo độ phân giải cao `@4x` (`392×264px`). |

---

## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)

> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên.
> - Nếu đang ở **Chế độ A (1 Đoạn Hội Thoại = 1 Page)**: Sau khi bạn **Confirm**, Agent sẽ tiến hành sửa và nghiệm thu Playwright trực tiếp cho Page này ngay trong đoạn hội thoại hiện tại.
> - Nếu đang ở **Chế độ B (Hub Tổng Nhiều Page)**: Sau khi bạn **Confirm**, Agent sẽ xuất bộ **Handoff Prompts** theo từng Page để đem sang các đoạn hội thoại con thực thi.
