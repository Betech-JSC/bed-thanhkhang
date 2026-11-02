# Phase 2: Checklist Kiểm Toán & Đối Chiếu Typography Với Figma MCP

> **Chế độ thực thi**: **STRICT READ-ONLY** — Không sửa bất kỳ file nào trong Phase này. Bắt buộc lấy thông số chuẩn trực tiếp từ **Figma MCP (`get_figma_data`)** và đối chiếu với mã nguồn thực tế (kết hợp `window.getComputedStyle` qua Playwright MCP nếu web server đang chạy).

---

## 1. Trích Xuất Bảng Chuẩn Typography Từ Figma MCP Trước Khi Quét Code

1. Gọi `get_figma_data` vào trang Style Guide / Typography hoặc các frame chuẩn (Desktop & Mobile) của `<NEW_BRAND>`.
2. Ghi nhận đầy đủ các thuộc tính gốc trên các node `TEXT` tiêu biểu (`Hero Title`, `H1`, `H2`, `H3`, `H4`, `Subtitle`, `Body Large`, `Body Default`, `Body Small`, `Caption/Badge`, `Navigation Link`, `Button Label`, `Input Value/Placeholder`):
   - `fontFamily`
   - `fontWeight` (ví dụ `400`, `500`, `600`, `700`)
   - `fontSize` trên Desktop và `fontSize` trên Mobile
   - `lineHeightPx` / `lineHeightPercentFontSize`
   - `letterSpacing`
   - `textCase` (`UPPER`, `ORIGINAL`...)

---

## 2. Bốn Nhóm Kiểm Toán Typography Bắt Buộc

### Nhóm 1: Cơ Chế Nạp Font & Mất Kế Thừa `font-family` Trên Form Controls
- **Nguồn nạp Webfont**:
  - Kiểm tra thẻ `<link>` Google Fonts, `@font-face` local trong `.scss`/`.css` hoặc `next/font` trong layout gốc: tên font có trùng khớp 100% với `fontFamily` trên Figma không?
  - Có nạp **đầy đủ các mức `font-weight`** thực tế đang dùng trên Figma (ví dụ Figma dùng `500` Medium và `600` SemiBold nhưng link Google Fonts chỉ nạp `400;700` khiến trình duyệt tự làm đậm giả — faux bold) hay không?
  - Font có hỗ trợ trọn vẹn bảng mã dấu **tiếng Việt (`vietnamese` subset)** không bị lỗi nhảy font ở các ký tự `ư`, `ơ`, `ệ`, `ẫ`, `đ` hay không?
- **Lỗi mất kế thừa `font-family` trên phần tử tương tác**:
  - Kiểm tra CSS base reset và các component `<button>`, `<input>`, `<select>`, `<textarea>`, `<option>`: theo mặc định trình duyệt hoặc một số bản reset cũ, các thẻ form control có thể không kế thừa `font-family` từ `<body>`, hoặc bị gán cứng một font cũ khác.
  - Kiểm tra các thành phần render qua Portal / Teleport (`Modal`, `Dialog`, `Drawer`, `Dropdown`, `Toast`) gắn trực tiếp vào `document.body` có bị trượt ra ngoài wrapper mang class `font-sans` hay không.

### Nhóm 2: Lệch `font-size` Desktop / Mobile & Hardcode `text-[..px]` Tùy Tiện
- Đối chiếu cấu hình `fontSize` trong `tailwind.config.*` (hoặc hệ thống utility classes `.headline-*`, `.body-*` trong SCSS) với thang đo Figma.
- Quét toàn bộ `Pages/`, `Layouts/`, `Components/` tìm các điểm lệch:
  - Sử dụng class kích thước cố định trên mọi màn hình (ví dụ `text-4xl` không có responsive scale) khiến chữ trên Mobile (`375px`) vượt quá thiết kế Mobile của Figma.
  - Lạm dụng arbitrary values rời rạc (`text-[13px]`, `text-[15px]`, `text-[17px]`, `text-[22px]`, `text-[28px]`...) lệch 1–3px so với thang đo chuẩn của Figma gây mất nhịp điệu thị giác.

### Nhóm 3: Lệch `font-weight`, `line-height` & `letter-spacing`
- **`font-weight`**: Phát hiện các tiêu đề trên Figma là `600` (`font-semibold`) hoặc `500` (`font-medium`) nhưng code đang để `700` (`font-bold` hoặc `font-extrabold`) làm giao diện bị thô nặng, mất chất *Quiet & High-Trust UI*.
- **`line-height` (`leading-*`)**:
  - So sánh tỷ lệ `lineHeight / fontSize` của Figma với class `leading-*` trong code.
  - Đặc biệt kiểm tra các tiêu đề tiếng Việt in hoa hoặc chữ lớn kết hợp `line-clamp-*` / `overflow-hidden`: `line-height` quá hẹp (`leading-none` / `1.0`–`1.1`) rất dễ cắt mất dấu sắc, hỏi, ngã phía trên chữ `Ấ`, `Ể`, `Ỗ`.
- **`letter-spacing` (`tracking-*`)**: Kiểm tra các nhãn eyebrow/badge viết hoa (`uppercase`) hoặc heading lớn có khớp `letterSpacing` trên Figma hay không.

### Nhóm 4: Xung Đột Độ Ưu Tiên CSS Cũ / SCSS / Thư Viện UI (PrimeVue...) Ghi Đè Tailwind
- **Thứ tự nạp stylesheet**: Kiểm tra file entry (`resources/Frontend/js/app.js`, `resources/Frontend/scss/app.scss`, `resources/css/app.css`, `src/app/globals.css`) xem thứ tự `@tailwind base; @tailwind components; @tailwind utilities;` có bị các file SCSS cũ hoặc theme CSS của thư viện bên thứ ba nạp **sau** rồi ghi đè không.
- **Thư viện UI Component (đặc biệt là PrimeVue / CKEditor content / Typography plugin)**:
  - Trong các dự án dùng **PrimeVue** (`primevue/resources/themes/...`), class `.p-component`, `.p-button`, `.p-inputtext`, `.p-dropdown-label`, `.p-dialog`, `.p-paginator` thường tự khai báo `font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto...` và `font-size: 1rem`, đè trực tiếp lên font thương hiệu của Tailwind!
  - Trong vùng nội dung bài viết CMS (`v-html` / `.prose` / `.ck-content`), kiểm tra xem style heading/paragraph bên trong bài viết có đồng bộ với font và thang chữ của Figma hay vẫn mang font cũ.
- **Đối chứng thực nghiệm trên trình duyệt thật (khi server khả dụng)**:
  - Dùng Playwright MCP mở trang và chạy `browser_evaluate` đọc `getComputedStyle(el)` (`fontFamily`, `fontSize`, `fontWeight`, `lineHeight`, `letterSpacing`) trên `body`, `h1`, `h2`, `p`, `button`, `input`, `.p-component` để có bằng chứng không thể chối cãi.

---

## 3. Mẫu Báo Cáo Chuẩn Phase 2

### Bảng 2.1: Bảng Chuẩn Typography Trích Xuất Từ Figma MCP (`<NEW_BRAND>`)

| Vai trò (Text Role) | Figma Node mẫu (`nodeId`) | Font Family | Weight | Size (Desktop -> Mobile) | Line Height | Letter Spacing | Token / Class chuẩn đề xuất |
| :--- | :--- | :--- | :---: | :---: | :---: | :---: | :--- |
| Display / Hero H1 | `...` | `...` | `600` | `48px -> 32px` | `1.2` (`58px`) | `-0.02em` | `text-display` / `text-3xl lg:text-5xl font-semibold` |
| Section H2 | `...` | `...` | `600` | `36px -> 24px` | `1.25` | `-0.01em` | `...` |
| Card Title H3 | `...` | `...` | `600` | `20px -> 18px` | `1.4` | `0` | `...` |
| Body Default | `...` | `...` | `400` | `16px -> 15px` | `1.6` | `0` | `...` |
| Button / Input | `...` | `...` | `500` | `15px -> 14px` | `1.4` | `0` | `...` |

### Bảng 2.2: Ma Trận Lệch Chuẩn Typography (`File:Dòng`)

| # | Nhóm (1–4) | Phần tử / Khu vực | Chuẩn Figma MCP | Thực tế trong Code / Computed | Tọa độ `File:Dòng` | Giải pháp chuẩn hóa cho `/opsx-propose` |
| :---: | :---: | :--- | :--- | :--- | :--- | :--- |
| 1 | Nhóm 1 | `<button>`, `<input>` toàn cục | `Inter / Be Vietnam Pro, 500` | Rơi về `Arial` / font hệ thống | `.../base.scss:14` | Thêm `font-family: inherit` trong `@layer base` cho `button, input, select, textarea` |
| 2 | Nhóm 4 | PrimeVue `.p-component` | Font thương hiệu `<NEW_BRAND>` | PrimeVue theme ép `Roboto, sans-serif` | `.../app.js:19` | Ghi đè `--font-family` và `.p-component { font-family: theme('fontFamily.sans'); }` sau import PrimeVue |
| 3 | Nhóm 2 & 3 | Tiêu đề Section Trang Chủ | `36px/44px, weight 600` | `text-[34px] font-bold leading-none` | `.../Home.vue:82` | Chuẩn hóa về token typography chuẩn, tăng `leading-tight` để không cắt dấu tiếng Việt |
