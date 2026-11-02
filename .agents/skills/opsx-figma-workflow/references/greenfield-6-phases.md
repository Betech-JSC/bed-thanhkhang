# Kịch Bản 1: Lộ Trình 6 Phase Dựng UI Từ Con Số 0 (`Greenfield 6 Phases`)

Áp dụng khi dự án khởi tạo giao diện mới hoàn toàn hoặc đã có file Figma đầy đủ (Design System / Style Guide + Layout + các màn hình chức năng). Tuyệt đối không gộp toàn bộ dự án vào một change khổng lồ — hãy chia đúng 6 Phase tuần tự dưới đây để kiểm soát chất lượng 100%.

---

## Tổng Quan 6 Phase

```
Phase 1-2: Infra & OPSX Bootstrap
   └──> Phase 3: OpenSpec Change "setup-design-tokens"
           └──> Phase 4: OpenSpec Change "setup-base-components" (+ SVG Icons)
                   └──> Phase 5: OpenSpec Change "setup-layout-shell" (Header/Footer/Container)
                           └──> Phase 6: Chuỗi Changes "feat-screen-<name>" + Playwright QA + Graphify
```

---

## Chi Tiết Thực Thi Từng Phase

### Phase 1–2: Hạ Tầng Nền & Khởi Tạo Quy Chuẩn (`Infra & OPSX Bootstrap`)
- **Mục tiêu**: Đảm bảo project build/chạy dev server thành công, Git theo dõi `.agents/` và `openspec/`, loại trừ `/graphify-out`, và có bản đồ AST ban đầu.
- **Các bước**:
  1. Chạy `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh`.
  2. Điền đầy đủ `context:` thực tế của dự án vào `openspec/config.yaml`.
  3. Kiểm tra lệnh build cơ bản (`yarn frontend:build` hoặc `npm run build`) chạy xanh trước khi can thiệp UI.

---

### Phase 3: OpenSpec Change `setup-design-tokens`
- **Mục tiêu**: Chuyển hóa 100% hệ thống biến thiết kế từ Figma thành Design Tokens tập trung trước khi viết bất kỳ thẻ HTML/Component nào.
- **Thực thi với Figma MCP**:
  1. Gọi `get_figma_data` vào trang/frame **Style Guide / Foundations** (hoặc các frame tiêu biểu nếu Figma không tách trang riêng).
  2. Trích xuất chính xác:
     - **Color Palette**: Brand primary/secondary, neutral surface/border/text, semantic (`success`, `warning`, `danger`, `info`).
     - **Typography**: Nạp đúng webfont (Google Fonts / local `@font-face` hỗ trợ đầy đủ dấu tiếng Việt), cấu hình `fontFamily.sans` / `display`, thang đo `fontSize` kèm `lineHeight`, `letterSpacing` và `fontWeight` cho cả Desktop & Mobile.
     - **Layout & Spacing**: Container `max-width`, `padding` theo từng breakpoint (`sm`, `md`, `lg`, `xl`, `2xl`), `borderRadius`, `boxShadow`.
  3. Cấu hình quy tắc CSS nền (`@layer base`) ép `<button>`, `<input>`, `<select>`, `<textarea>` kế thừa `font-family: inherit` để không bao giờ bị rơi về font mặc định trình duyệt.
- **Kiểm chứng**: Build CSS/Frontend thành công 0 lỗi; tạo `openspec archive "setup-design-tokens"` sau khi kiểm tra biến sinh ra chuẩn xác.

---

### Phase 4: OpenSpec Change `setup-base-components` (+ Bộ SVG Icons Chuẩn)
- **Mục tiêu**: Xây dựng thư viện Atomic / Shared UI Components dùng lại xuyên suốt toàn dự án.
- **Thực thi**:
  1. Đọc `graphify-out/graph.json` để xem Base CMS đã có sẵn các component nào (ví dụ `Picture`, `Link`, `Field`, `Pagination`, `Breadcrumb`...), quyết định nâng cấp styling hay tạo mới có kiểm soát.
  2. Dùng `download_figma_images` tải toàn bộ SVG icons chuẩn từ Figma (mũi tên, kính lúp, giỏ hàng, liên hệ, social, trạng thái...). Không tự gõ mò tọa độ `<path d="...">` và tuân thủ `anti-ui-slop` (không nhồi icon thừa).
  3. Dựng các Base Components cốt lõi với đầy đủ trạng thái (`default`, `hover`, `focus-visible`, `active`, `disabled`, `loading`, `error`):
     - `BaseButton` / `Button` (các variants: `primary`, `secondary`, `outline`, `ghost`; sizes: `sm`, `md`, `lg`)
     - Form Controls (`Input`, `Select`, `Textarea`, `Checkbox`, `Radio`, `FormField`)
     - Feedback & Display (`Badge`, `SectionHeading`, `Modal`/`Dialog`, `EmptyState`, `Skeleton`)
  4. Chạy build thật 0 lỗi và chạy `graphify . --code-only` để ghi nhận toàn bộ Base Components vào đồ thị AST.

---

### Phase 5: OpenSpec Change `setup-layout-shell`
- **Mục tiêu**: Hoàn thiện bộ khung trang dùng chung (`Header`, `Footer`, `MainLayout`/`AppLayout`, Mobile Navigation Drawer, Floating Contact/Back-to-top nếu có trong Figma).
- **Thực thi**:
  1. Gọi `get_figma_data` riêng trên `nodeId` của `Header` (Desktop + Mobile menu state) và `Footer`.
  2. Tái sử dụng Base Components & SVG Icons từ Phase 4 (kiểm tra qua `graphify-out/graph.json`).
  3. Xử lý chuẩn hành vi sticky header, active link, đa ngôn ngữ (nếu có `vue-i18n` / locale switcher), và khả năng truy cập bàn phím (`Escape` đóng menu, `aria-expanded`).
- **Kiểm chứng bắt buộc**:
  - Build frontend 0 lỗi + chạy `graphify . --code-only`.
  - Giao `QA Verifier` dùng **Playwright MCP** chụp ảnh `Header` + `Footer` ở `1440x900` và `375x812` (bao gồm cả trạng thái mở Mobile Menu).

---

### Phase 6: Chuỗi OpenSpec Changes `feat-screen-<name>`
- **Mục tiêu**: Triển khai từng màn hình nghiệp vụ (`feat-screen-home`, `feat-screen-products`, `feat-screen-product-detail`, `feat-screen-about`, `feat-screen-news`, `feat-screen-contact`...).
- **Quy trình chuẩn cho mỗi màn hình**:
  1. **Khảo sát**: Đọc `graphify-out/graph.json` + đọc Controller/Route backend liên quan để nắm chính xác cấu trúc `props` dữ liệu thực tế (Zero-Assumption — nếu thiếu API/props phải hỏi người dùng hoặc mở task `Backend Worker` bổ sung, cấm bịa mock cấu trúc sai lệch).
  2. **Tách Section Figma**: Gọi `get_figma_data` ở `depth: 2` trên frame màn hình để lấy danh sách `nodeId` các section con.
  3. **OpenSpec**: Tạo `/opsx-propose` cho `feat-screen-<name>` liệt kê rõ từng section, danh sách component tái sử dụng từ `graph.json` và danh sách component mới cần tạo.
  4. **Code & Build (`Frontend Worker`)**: Gọi `get_figma_data` từng `nodeId` section, tải ảnh minh họa qua `download_figma_images`, lắp ráp giao diện sạch sẽ (tuân thủ `anti-ui-slop`), chạy lệnh build 0 lỗi.
  5. **Cập nhật AST**: Chạy `graphify . --code-only`.
  6. **Visual QA (`QA Verifier`) & Khóa sổ**: Mở trình duyệt thật qua Playwright MCP kiểm tra Desktop (`1440x900`) + Mobile (`375x812`), kiểm tra tương tác thật (filter, tab, form validation, link điều hướng), đính kèm screenshots bằng chứng rồi mới chạy `openspec archive "feat-screen-<name>"`.
