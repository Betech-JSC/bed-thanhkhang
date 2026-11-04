# Phase 1: Checklist Kiểm Toán Tàn Dư Giao Diện (`<OLD_BRAND>` -> `<NEW_BRAND>`) & Dữ Liệu Hardcode / Return Cố Định Toàn Bộ CMS

> **Nguyên tắc thực thi**: Tuyệt đối không xóa file vật lý (`rm`) khi chưa có phê duyệt. Mọi phát hiện phải kèm đường dẫn tương đối và số dòng chính xác (`File:Dòng`). Đối với các vị trí **CMS đang hardcode và return giá trị cố định**, bắt buộc liệt kê đầy đủ ra bảng và tiến hành **remove** để nối về dữ liệu động của CMS (Model/DB, Settings, Props hoặc `[]`).

---

## 1. Xác Định Bộ Từ Khóa & Dấu Vân Tay Của `<OLD_BRAND>`

Trước khi quét, lập danh sách dấu vân tay nhận diện `<OLD_BRAND>`:
- **Tên & Biến thể chuỗi**: `<OLD_BRAND>` ở mọi kiểu viết (`PascalCase`, `camelCase`, `kebab-case`, `snake_case`, `UPPERCASE`, không dấu), tên miền cũ, email/hotline/mạng xã hội cũ.
- **Từ khóa nghiệp vụ cũ không thuộc `<NEW_BRAND>`**: Ví dụ khi chuyển từ nền tảng giáo dục/khóa học sang thương mại B2B/hóa chất hoặc nội thất: `course`, `tuition`, `student`, `point`, `rank`, `khóa học`, `học viên`, `học phí`, `điểm thưởng`...
- **Bảng màu đặc trưng cũ**: Các mã HEX/RGB chủ đạo của `<OLD_BRAND>` (đọc từ lịch sử `tailwind.config.*` hoặc `variables.scss`).

---

## 2. Sáu Nhóm Quét Kiểm Toán Bắt Buộc

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

### Nhóm 6 (BẮT BUỘC): Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal`)
- Chạy script quét tự động:
  ```bash
  python3 "$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py" .
  ```
- Kiểm tra toàn diện cả 2 tầng của CMS:
  1. **Backend CMS (`app/Http/Controllers/`, `app/Services/`, `app/Repositories/`, `app/Models/`, `app/Http/Middleware/HandleInertia*.php`, `routes/`)**:
     - Phát hiện mọi hàm/method đang hardcode dữ liệu và `return` các mảng/object/số liệu cố định (`getDefault*()`, `getFallback*()`, `getContactFaqs()`, `getSolutionOptions()`, `default*Settings()`, hoặc `return [...]` chứa danh sách sản phẩm, bài viết, danh mục, chính sách, đại lý, câu hỏi FAQ, slider, số liệu thống kê Dashboard, thông tin liên hệ cứng) thay vì lấy từ Database / Settings.
     - Phát hiện các đoạn code dùng mảng fallback cố định che giấu trạng thái bảng DB đang rỗng (`if ($items->isEmpty()) return $this->getFallback...()`).
  2. **Frontend & CMS UI (`resources/Frontend/js/`, `resources/Backend/js/`, `resources/js/`, `src/`)**:
     - Phát hiện các biến mảng tĩnh (`FALLBACK_*`, `default*`, mock arrays trong `Pages/`, `Components/`, `Layouts/`, `Composables/`), các `computed()` hoặc hàm getter `return` danh sách cố định, hoặc `props` fallback (`props.items?.length ? props.items : defaultItems`).
     - Phát hiện thông tin liên hệ (`tel:`, `mailto:`, địa chỉ, link mạng xã hội) gán cứng trực tiếp trong template thay vì đọc từ `$page.props.settings` / `global`.
- **Hành động xử lý bắt buộc**:
  - **Liệt kê đầy đủ** ra bảng chi tiết (Tọa độ `File:Dòng`, tên hàm/biến, giá trị đang hardcode/return cố định).
  - **Tiến hành Remove** các khối dữ liệu hardcode và lệnh `return` cố định đó, kết nối trực tiếp vào nguồn dữ liệu động của CMS (Eloquent Model / Settings / Inertia Props) hoặc trả về mảng rỗng `[]` / `null` chuẩn khi DB chưa có dữ liệu.

---

## 3. Mẫu Báo Cáo Chuẩn Phase 1

Trình bày kết quả kiểm toán Phase 1 theo đúng 3 bảng dưới đây:

### Bảng 1.1: Tỷ Lệ % Độ Sạch Theo Từng Trang & Tầng UI/CMS
*(Độ sạch % = Số hạng mục đạt chuẩn `<NEW_BRAND>` & 100% Dynamic CMS / Tổng số hạng mục kiểm tra trên trang đó × 100%)*

| Khu vực / Trang | Đường dẫn File chính | Độ sạch (%) | Trạng thái | Tóm tắt tàn dư / hardcode chính phát hiện |
| :--- | :--- | :---: | :---: | :--- |
| Design Tokens & Global CSS | `tailwind.config.*`, `scss/` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Backend Controllers & Middleware | `app/Http/Controllers/...` | `..%` | `CLEAN / PARTIAL / HARDCODED` | Số hàm return mảng cố định: ... |
| Layout Shell (Header/Footer) | `Layouts/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | ... |
| Trang Chủ (Home) | `Pages/Home...` | `..%` | `CLEAN / PARTIAL / HARDCODED` | ... |
| Danh sách & Chi tiết Sản phẩm | `Pages/Products/...` | `..%` | `CLEAN / PARTIAL / HARDCODED` | ... |
| Tin tức / Giới thiệu / Liên hệ | `Pages/...` | `..%` | `CLEAN / PARTIAL / HARDCODED` | ... |
| Orphan / Dead Components | `Components/...` | `..%` | `CLEAN / PARTIAL / LEGACY` | Số lượng component mồ côi: ... |

### Bảng 1.2: Danh Sách Tọa Độ Tàn Dư & Hardcode Return Cố Định Chi Tiết (`File:Dòng`)

| # | Nhóm (1–6) | Mức độ (`P0`/`P1`/`P2`) | Tọa độ `File:Dòng` | Hàm / Biến / Phần tử | Bằng chứng thực tế trong code | Hành động Remove / Chuẩn hóa |
| :---: | :---: | :---: | :--- | :--- | :--- | :--- |
| 1 | Nhóm 4 | `P0` | `resources/views/app.blade.php:12` | `<title>` | `<title inertia>...<OLD_BRAND>...</title>` | Đổi fallback title sang `<NEW_BRAND>` lấy từ `config('app.name')` |
| 2 | Nhóm 6 | `P1` | `app/Http/Controllers/Frontend/AgencyController.php:157` | `getContactFaqs()` | `return [ ['number' => '01', 'question' => ...] ]` | Remove mảng return cố định; lấy từ CMS Settings / Model hoặc trả về `[]` |
| 3 | Nhóm 6 | `P1` | `resources/Frontend/js/Pages/Posts/Index.vue:420` | `FALLBACK_FIGMA_POSTS` | `const FALLBACK_FIGMA_POSTS = [ ... ]` | Remove mảng cố định `FALLBACK_FIGMA_POSTS`; dùng 100% `props.posts` từ CMS |
| 4 | Nhóm 1 | `P1` | `.../Header.vue:48` | Header wrapper | `class="bg-[#FF4D00] text-white"` | Thay bằng semantic token `bg-primary` của `<NEW_BRAND>` |
| 5 | Nhóm 5 | `P2` | `.../OldPromoCard.vue:1` | Orphan Component | `0 incoming imports` trên `graph.json` | Đưa vào danh sách phê duyệt dọn dẹp có kiểm soát |

*(Lưu ý về Nhóm 5 vs Nhóm 6: Mọi đề xuất xóa file vật lý ở Nhóm 5 bắt buộc phải tách riêng chờ người dùng phê duyệt theo quy tắc **Strict Safe Guard — Cấm tự ý xóa file**. Còn việc **remove các giá trị hardcode / return cố định** bên trong file code ở Nhóm 6 được thực thi bằng `edit_file` và kiểm chứng qua `scan-cms-hardcoded.py` + `npm run build`).*
