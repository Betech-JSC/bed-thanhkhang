---
name: ui-skill
description: Kiểm toán toàn diện hệ thống giao diện (Strict Read-Only UI Animation & Motion Coverage Audit) kết hợp 4 Quy Tắc Vàng Animation Chuẩn LUXI (Single-Animation-Per-Element, No Opacity Blank Flash, Directional Horizontal Slide translate-x cho nút bấm qua lại < / >, Giữ nguyên 100% ProductGallery có hàng preview dưới). Vận hành theo mô hình Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke) 4 bước kèm bảng quyết định [+]/[-]/[=] chờ người dùng Confirm. Sử dụng khi người dùng gọi "/ui-skill" hoặc "/opsx-animation".
---

# UI Skill — Kiểm Toán Độ Phủ Animation & 4 Quy Tắc Vàng Chuẩn LUXI (`/ui-skill` & `/opsx-animation`)

Skill này chuẩn hóa quy trình **kiểm toán độ phủ Animation & Micro-Interaction chỉ đọc 100% (`Strict Read-Only`)** kết hợp **4 Quy Tắc Vàng Bất Biến Về Animation**, đi kèm bộ công cụ quét tự động đa nền tảng (`scan-missing-animations.sh` + `scan-animation-rules.py`) và mô hình điều phối **Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke)** 4 bước.

Hỗ trợ tự động nhận diện và quét cho mọi Tech Stack của Team:
- **Laravel + Inertia.js + Vue 3 (BED CMS)**: `resources/Frontend/js/{Layouts,Pages,Components}` + `resources/Frontend/scss` + `resources/Frontend/tailwind.config.js`
- **Laravel + Inertia.js + Vue 3 (Standard)**: `resources/js/{Layouts,Pages,Components}`
- **Next.js / React (App Router & Pages Router)**: `src/app`, `src/pages`, `src/components`, `app/`, `components/`
- **Nuxt / Vite Vue / React SPA**: `src/{layouts,pages,views,components}`, `pages/`, `components/`
- **Laravel Blade**: `resources/views/`

---

## 1. 4 Quy Tắc Vàng Bất Biến Về Animation (4 Golden Animation Rules)

Chi tiết mẫu code chuẩn xem tại [animation-4-golden-rules.md](./references/animation-4-golden-rules.md):

| Quy tắc | Tên quy chuẩn | Nội dung bắt buộc tuân thủ 100% |
| :--- | :--- | :--- |
| **Quy tắc 1** | **`Single-Animation-Per-Element` (Mỗi phần tử chỉ 1 Animation)** | Mỗi phần tử DOM chỉ được gán **tối đa 1 hiệu ứng chuyển động chủ đạo**. Cấm chồng chéo nhiều animation lên cùng 1 phần tử hoặc cha-con trực tiếp (không lồng `<TransitionGroup>` bên trong `<Transition>`, không viết trùng `transition-transform` + `transition-all`). |
| **Quy tắc 2** | **`Zero White-Out / No Opacity Blank Flash` (Cấm phủ trắng / nháy tàng hình)** | Tuyệt đối cấm hiệu ứng `opacity-0 -> opacity-100` trên các Section lớn hoặc lớp phủ trắng đè lên màn hình khi tải/cuộn trang. `useScrollReveal` (`v-scroll-reveal`) bắt buộc giữ `opacity: 1` và chỉ trượt `translate-y-4 -> translate-y-0` với `transition-transform duration-500 ease-out`. |
| **Quy tắc 3** | **`Directional Horizontal Slide (translate-x) for Prev/Next Buttons`** | Mọi khu vực có **nút bấm qua lại (`<` / `>` hoặc Dots chuyển slide/trang)** như Showcase sản phẩm, Góc nhìn/Video carousel, Hero slider, Sản phẩm liên quan, hoặc lưới phân trang sản phẩm **BẮT BUỘC phải là Animation Trượt Ngang (`translate-x`) theo hướng bấm (`next` / `prev`)**, **TUYỆT ĐỐI KHÔNG dùng `opacity-0` fade**. |
| **Quy tắc 4** | **`Preserve Product Gallery with Bottom Thumbnail Previews (KEEP AS-IS)`** | Phần chi tiết sản phẩm có nhiều cái preview/thumbnail nhỏ ở dưới (`ProductGallery.vue` với khung `688×688px` + hàng 6 ảnh preview `104×104px` ở dưới, và `ProductInfoPanel.vue`) được **GIỮ NGUYÊN 100% (`[=] KEEP AS-IS`)** như trong source hiện có. |

---

## 2. Quy Trình 4 Bước Tại Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke Workflow)

Khi người dùng gọi `/ui-skill` hoặc `/opsx-animation`:

### Bước 1 — Chạy Script Quét Read-Only (`scan-animation-rules.py` & `scan-missing-animations.sh`)
```bash
python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .
./.agents/skills/ui-skill/scripts/scan-missing-animations.sh .
```
- Hoàn toàn **Read-Only** đối với mã nguồn dự án (không tự ý sửa hay xóa file).
- Đọc báo cáo tại `scratch/animation-rules-audit-report.md` và `scratch/ui-animation-audit-report.md`.

### Bước 2 — Trình Bày Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` & Dừng Chờ Người Dùng Confirm
- **Bảng `[+] THÊM GÌ (ADD)`**: Các slider có nút `< / >` cần bổ sung trượt ngang `translate-x` theo `slideDirection`, hoặc các card/button thiếu hover micro-interaction.
- **Bảng `[-] BỎ GÌ (REMOVE)`**: Các hiệu ứng `opacity-0` trong slider/scroll-reveal, hoặc các chỗ lồng chồng chéo animation (`<TransitionGroup>` trong `<Transition>`).
- **Bảng `[=] GIỮ LẠI GÌ (KEEP AS-IS)`**: **Giữ nguyên 100% `ProductGallery.vue` (có hàng 6 ảnh preview ở dưới)**, `ProductInfoPanel.vue`, và các thành phần đã đạt chuẩn.
- **Dừng lại chờ người dùng Confirm** danh sách Thêm / Bỏ / Giữ trước khi sang Bước 3.

### Bước 3 — Sinh Bộ "Handoff Prompts" Đem Đi Các Đoạn Hội Thoại Khác (Worker Conversations)
- Sau khi người dùng Confirm, đóng gói thành các **Handoff Prompts** nhỏ gọn theo từng cụm component để người dùng đem sang đoạn hội thoại khác giải quyết, giữ cho đoạn hội thoại trung tâm luôn nhẹ và sạch.

### Bước 4 — Quay Về Đoạn Hội Thoại Trung Tâm Để Nghiệm Thu (Hub Gate Verification)
- Chạy lại `python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .` + `yarn frontend:build` + Playwright MCP để xác nhận PASS 100%.

---

## 3. 6 Nhóm Kiểm Toán Độ Phủ Animation Toàn Hệ Thống

Chi tiết tiêu chuẩn đánh giá và mẫu code chuẩn xem tại [animation-audit-checklist.md](./references/animation-audit-checklist.md) và [animation-4-golden-rules.md](./references/animation-4-golden-rules.md):

| Nhóm | Tên hạng mục kiểm toán | Trọng tâm phát hiện thiếu sót |
| :--- | :--- | :--- |
| **Nhóm 1** | **Design Tokens & Global Motion Foundation** | Kiểm tra `tailwind.config.*`, `.scss`/`.css` và rule `@media (prefers-reduced-motion: reduce)`. |
| **Nhóm 2** | **Layout Shell & Directional Sliders (`<` / `>`)** | Kiểm tra các slider có nút bấm qua lại đã trượt ngang `translate-x` (không `opacity-0`) chưa, Header sticky, Mobile Drawer. |
| **Nhóm 3** | **Section Entrance & Scroll-Reveal (`translate-y` only)** | Kiểm tra `v-scroll-reveal` chỉ trượt `translate-y-4 -> translate-y-0` (giữ `opacity: 1`, không phủ trắng). |
| **Nhóm 4** | **Interactive Micro-Interactions (Hover / Focus / Active)** | Kiểm tra Buttons, Links, Cards có `group-hover:scale-[1.03]`, `active:scale-95`, `transition-transform`. |
| **Nhóm 5** | **Overlays, Modals, Drawers, Dropdowns & Accordions** | Kiểm tra Modal/Drawer/Accordion đóng mở mượt mà. |
| **Nhóm 6** | **Preserved Galleries & Async Feedback** | Giữ nguyên 100% `ProductGallery.vue` có hàng thumbnail preview dưới; kiểm tra trạng thái loading button. |
