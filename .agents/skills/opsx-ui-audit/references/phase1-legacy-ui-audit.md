# Phase 1: Checklist Kiểm Toán Tàn Dư Giao Diện & Thương Hiệu Cũ (`<OLD_BRAND>` -> `<NEW_BRAND>`)

> **Chế độ thực thi**: **STRICT READ-ONLY** — Tuyệt đối không sửa hoặc xóa bất kỳ file nào trong Phase này. Mọi phát hiện phải kèm đường dẫn tương đối và số dòng chính xác (`File:Dòng`).

---

## 1. Xác Định Bộ Từ Khóa & Dấu Vân Tay Của `<OLD_BRAND>`

Trước khi quét, lập danh sách dấu vân tay nhận diện `<OLD_BRAND>`:
- **Tên & Biến thể chuỗi**: `<OLD_BRAND>` ở mọi kiểu viết (`PascalCase`, `camelCase`, `kebab-case`, `snake_case`, `UPPERCASE`, không dấu), tên miền cũ, email/hotline/mạng xã hội cũ.
- **Từ khóa nghiệp vụ cũ không thuộc `<NEW_BRAND>`**: Ví dụ khi chuyển từ nền tảng giáo dục/khóa học sang thương mại B2B/hóa chất: `course`, `tuition`, `student`, `point`, `rank`, `khóa học`, `học viên`, `học phí`, `điểm thưởng`...
- **Bảng màu đặc trưng cũ**: Các mã HEX/RGB chủ đạo của `<OLD_BRAND>` (đọc từ lịch sử `tailwind.config.*` hoặc `variables.scss`).

---

## 2. Năm Nhóm Quét Tàn Dư Bắt Buộc

### Nhóm 1: Màu Sắc & Design Tokens Cũ
- Kiểm tra `tailwind.config.*` (`colors`, `backgroundImage`, `boxShadow`, `borderColor`): còn token nào mang tên hoặc mã màu chủ đạo của `<OLD_BRAND>` không?
- Kiểm tra toàn bộ file `.scss` / `.css` (`:root` CSS variables, `$variables`, gradients, custom scrollbar, selection highlight color).
- Quét các class arbitrary hardcode trong template (`.vue`, `.tsx`, `.jsx`, `.blade.php`): `bg-[#...]`, `text-[#...]`, `border-[#...]`, `from-[#...]`, `ring-[#...]` vẫn dùng mã màu của theme cũ thay vì semantic token của `<NEW_BRAND>`.

### Nhóm 2: Cấu Trúc Layout & Component Đặc Thù Nghiệp Vụ Cũ
- Kiểm tra `Layouts/` (`Header`, `Footer`, `MobileMenu`, `FloatingAction`, `Sidebar`): còn mục điều hướng, nút CTA, widget nổi hay cấu trúc cột thuộc về `<OLD_BRAND>` không khớp với Figma của `<NEW_BRAND>` không?
- Kiểm tra các `Pages/` đang hoạt động: còn import hoặc render các section/card/badge/filter thừa từ giao diện cũ không?

### Nhóm 3: Hình Ảnh, Icons, Favicon & Static Assets Tĩnh
- Kiểm tra thư mục `public/` (`favicon.ico`, `apple-touch-icon.png`, `manifest.json` / `site.webmanifest`, ảnh OG share mặc định, logo header/footer, ảnh placeholder mặc định khi sản phẩm/bài viết thiếu ảnh).
- Kiểm tra thư mục `Components/Icon/` hoặc `assets/icons/`: các SVG logo, watermark, minh họa empty-state/404 còn mang nhận diện màu hoặc hình ảnh của `<OLD_BRAND>`.

### Nhóm 4: Copywriting, Placeholder, Đa Ngôn Ngữ (i18n) & SEO Metadata
- Kiểm tra `resources/views/app.blade.php` (hoặc `src/app/layout.tsx`): thẻ `<title>` fallback, `meta[name="application-name"]`, `meta[property="og:site_name"]`, Schema JSON-LD `Organization`.
- Kiểm tra toàn bộ tệp đa ngôn ngữ (`resources/lang/*.json`, `resources/lang/*/*.php`, `locales/*.json`): còn câu chữ, câu chào, điều khoản, placeholder form, thông báo validation nhắc tới `<OLD_BRAND>` hoặc nghiệp vụ cũ không?
- Kiểm tra dữ liệu fallback hardcode bên trong các Vue/React Pages & Components (ví dụ dòng bản quyền `© ... <OLD_BRAND>` ở Footer, địa chỉ công ty mẫu ở trang Liên hệ).

### Nhóm 5: Dead UI, Orphan Components & Legacy Imports
- Đối chiếu danh sách toàn bộ file trong `Components/` và `Pages/` với các cạnh `imports` trên `graphify-out/graph.json`:
  - Liệt kê các **Orphan Components** (không được bất kỳ Page, Layout hay Component sống nào import, cũng không được đăng ký global/auto-import sử dụng thực tế).
  - Liệt kê các file SCSS/CSS hoặc package UI cũ vẫn được `@import` trong bundle chính nhưng không còn phần tử DOM nào sử dụng (gây phình CSS và xung đột style).

---

## 3. Mẫu Báo Cáo Chuẩn Phase 1

Trình bày kết quả kiểm toán Phase 1 theo đúng 2 bảng dưới đây:

### Bảng 1.1: Tỷ Lệ % Độ Sạch Theo Từng Trang & Tầng UI
*(Độ sạch % = Số hạng mục đạt chuẩn `<NEW_BRAND>` / Tổng số hạng mục kiểm tra trên trang đó × 100%)*

| Khu vực / Trang | Đường dẫn File chính | Độ sạch (%) | Trạng thái | Tóm tắt tàn dư chính phát hiện |
| :--- | :--- | :---: | :---: | :--- |
| Design Tokens & Global CSS | `tailwind.config.*`, `scss/` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Layout Shell (Header/Footer) | `Layouts/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Trang Chủ (Home) | `Pages/Home...` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Danh sách & Chi tiết Sản phẩm | `Pages/Products/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Tin tức / Giới thiệu / Liên hệ | `Pages/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Orphan / Dead Components | `Components/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | Số lượng component mồ côi: ... |

### Bảng 1.2: Danh Sách Tọa Độ Tàn Dư Chi Tiết (`File:Dòng`)

| # | Nhóm (1–5) | Mức độ (`P0`/`P1`/`P2`) | Tọa độ `File:Dòng` | Bằng chứng thực tế trong code | Hướng xử lý đề xuất cho `/opsx-propose` |
| :---: | :---: | :---: | :--- | :--- | :--- |
| 1 | Nhóm 4 | `P0` | `resources/views/app.blade.php:12` | `<title inertia>...<OLD_BRAND>...</title>` | Đổi fallback title sang `<NEW_BRAND>` lấy từ `config('app.name')` |
| 2 | Nhóm 1 | `P1` | `.../Header.vue:48` | `class="bg-[#FF4D00] text-white"` | Thay bằng semantic token `bg-primary` của `<NEW_BRAND>` |
| 3 | Nhóm 5 | `P2` | `.../OldPromoCard.vue:1` | `0 incoming imports` trên `graph.json` | Đưa vào danh sách phê duyệt dọn dẹp có kiểm soát |

*(Lưu ý về Nhóm 5: Mọi đề xuất xóa file Dead UI bắt buộc phải tách riêng để người dùng bấm phê duyệt rõ ràng trong Implementation Plan theo đúng quy tắc **Strict Safe Guard — Cấm tự ý xóa file**).*
