---
name: opsx-figma-workflow
description: Điều phối quy trình dựng giao diện chuẩn Production kết hợp Figma MCP (get_figma_data, download_figma_images) × OpenSpec (OPSX) × Graphify AST × Anti-UI-Slop × Playwright Visual QA. Hỗ trợ đa stack (Laravel + Inertia + Vue 3 và Next.js + React) qua 3 kịch bản Greenfield 6 Phases, Home-First Incremental và Batch Pages Sequential QA.
---

# OPSX × Figma MCP — Quy Trình Dựng Giao Diện Đa Stack Theo Phase

Skill này là trung tâm điều phối mọi tác vụ chuyển đổi thiết kế từ **Figma MCP** thành mã nguồn giao diện hoàn chỉnh, có kiểm soát kiến trúc bằng **Graphify AST**, quản lý đặc tả bằng **OpenSpec (`OPSX`)**, kiểm soát thẩm mỹ bằng **Anti-UI-Slop** và nghiệm thu thực tế bằng **Playwright MCP**.

## 1. Bảng Định Tuyến 3 Kịch Bản Dựng UI

Trước khi mở bất kỳ OpenSpec change nào, xác định đúng 1 trong 3 kịch bản sau và đọc file `references/` tương ứng:

| Kịch bản | Khi nào áp dụng | Tài liệu quy trình chi tiết |
| :--- | :--- | :--- |
| **1. Greenfield 6 Phases** | Dự án dựng mới từ đầu hoặc đã có trọn bộ Figma Design System & nhiều màn hình. | [greenfield-6-phases.md](./references/greenfield-6-phases.md) |
| **2. Home-First Incremental** | Figma mới chỉ chốt **Trang Home**; cần rút trích Core/Tokens từ Home rồi mở rộng cuốn chiếu từng trang sau. | [home-first-incremental.md](./references/home-first-incremental.md) |
| **3. Batch Pages + Sequential QA** | Đã có Tokens & Layout Shell; cần code gộp một lô nhiều trang trước rồi chạy Visual QA & `archive` tuần tự ở cuối. | [batch-pages-sequential-qa.md](./references/batch-pages-sequential-qa.md) |

---

## 2. Năm Quy Tắc Cứng Bắt Buộc (Non-Negotiable Rules)

1. **Graphify AST First (Chống tạo trùng Component)**:
   - Trước khi tạo bất kỳ component, composable hay layout mới nào, **BẮT BUỘC** đọc `graphify-out/graph.json` (và `GRAPH_REPORT.md` nếu có) theo skill `graphify`.
   - Nếu đã tồn tại component cùng vai trò, phải tái sử dụng hoặc mở rộng qua `props` / `variants` / `slots` (không làm gãy trang cũ), tuyệt đối không sinh file trùng lặp (`Card2`, `ButtonNew`...).
   - Ngay sau mỗi Phase/Change thêm hoặc đổi liên kết file UI, chạy lại `graphify . --code-only`.
2. **Chiến lược gọi Figma MCP chống nổ Token**:
   - Tuyệt đối không gọi `get_figma_data` full-depth lên một trang Landing/Home dài trong 1 lần duy nhất.
   - **Bước A**: Gọi `get_figma_data` với `depth: 1` hoặc `depth: 2` trên frame trang để lấy danh sách `nodeId` của từng cụm Section (`Header`, `Hero`, `Categories`, `FeaturedProducts`, `News`, `Footer`...).
   - **Bước B**: Gọi `get_figma_data` đi sâu vào từng `nodeId` Section tương ứng với task đang code.
   - **Bước C**: Dùng `download_figma_images` tải đúng file SVG icon và hình ảnh gốc từ Figma về thư mục assets của dự án; cấm tự đoán/bịa chuỗi path SVG phức tạp.
3. **Design Tokens Tập Trung & Kế Thừa Typography**:
   - Mọi màu sắc, font-family, font-size, line-height, border-radius, box-shadow từ Figma phải quy hoạch vào Design Tokens (`tailwind.config.*` / CSS variables), hạn chế tối đa hardcode giá trị rời rạc.
   - Đảm bảo các thẻ `<button>`, `<input>`, `<select>`, `<textarea>` kế thừa đúng `font-family` chủ đạo của thiết kế (tránh rơi về font mặc định trình duyệt hoặc bị thư viện UI như PrimeVue ghi đè).
4. **Anti-UI-Slop & Web Interface Guidelines**:
   - Áp dụng nghiêm ngặt `.agents/skills/anti-ui-slop/SKILL.md` (*Quiet & High-Trust UI*: cấm tự ý nhồi nhét icon trang trí vô nghĩa, cấm chèn emoji vào nút/tiêu đề, xử lý đủ trạng thái loading/empty/error/disabled/hover/focus) và `.agents/skills/web-design-guidelines/SKILL.md`.
5. **Multi-Agent Build Gate, Playwright Visual QA & Archive Safe Guard**:
   - `Frontend Worker` code + chạy lệnh build thật của dự án đảm bảo **0 lỗi biên dịch**.
   - `QA Verifier` mở trình duyệt thật qua **Playwright MCP**, kiểm tra ở cả Desktop (`1440x900`) và Mobile (`375x812`), xác nhận 0 lỗi console, 0 ảnh vỡ, 0 tràn ngang (`overflow-x`), chụp screenshot làm bằng chứng.
   - Chỉ chạy `openspec archive` sau khi có đủ bằng chứng PASS và được người dùng xác nhận.

---

## 3. Ánh Xạ Thư Mục Đa Stack (Multi-Stack Directory Mapping)

Khi đọc `composer.json` / `package.json`, ánh xạ các tầng UI vào đúng cấu trúc thực tế của dự án:

| Tầng Kiến Trúc UI | Laravel + Inertia + Vue 3 (BED CMS) | Laravel + Inertia + Vue 3 (Standard) | Next.js (App Router) + React |
| :--- | :--- | :--- | :--- |
| **Design Tokens & CSS** | `resources/Frontend/tailwind.config.js`<br>`resources/Frontend/scss/` | `tailwind.config.js`<br>`resources/css/app.css` | `tailwind.config.ts` / `@theme`<br>`src/app/globals.css` |
| **Base & Shared Components** | `resources/Frontend/js/Components/` | `resources/js/Components/` | `src/components/ui/`<br>`src/components/sections/` |
| **Layout Shell** | `resources/Frontend/js/Layouts/` | `resources/js/Layouts/` | `src/components/layout/`<br>`src/app/layout.tsx` |
| **Pages / Screens** | `resources/Frontend/js/Pages/` | `resources/js/Pages/` | `src/app/<route>/page.tsx` |
| **Routes & Ziggy / Links** | `routes/frontend.php` (`yarn route`) | `routes/web.php` | File-system routing (`src/app/`) |
| **Static Assets & Icons** | `public/assets/` hoặc `Components/Icon/` | `public/images/`, `public/icons/` | `public/images/`, `public/icons/` |
| **Lệnh Build Kiểm Chứng** | `yarn frontend:build` | `npm run build` / `yarn build` | `npm run build` / `pnpm build` |

---

## 4. Checklist Nhanh Cho Mỗi OpenSpec UI Change

1. Đọc `openspec/config.yaml` và `graphify-out/graph.json`.
2. Quét nông frame Figma (`get_figma_data` với `depth: 2`) -> lập danh sách `nodeId` và lập OpenSpec artifacts (`/opsx-propose`).
3. Giao `Frontend Worker` thực thi `/opsx-apply`: lấy chi tiết từng `nodeId`, tải SVG/ảnh qua `download_figma_images`, dựng UI tuân thủ `anti-ui-slop`, chạy lệnh build thật 0 lỗi.
4. Chạy `graphify . --code-only` cập nhật bản đồ AST.
5. Giao `QA Verifier` kiểm chứng bằng Playwright MCP (Desktop + Mobile screenshots) -> nghiệm thu -> `/opsx-archive`.
