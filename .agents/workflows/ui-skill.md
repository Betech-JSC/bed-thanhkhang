---
description: Kiểm toán toàn diện độ phủ Animation & Micro-Interaction trên hệ thống giao diện (Strict Read-Only UI Animation Audit) và liệt kê đầy đủ các thành phần chưa có hiệu ứng kèm tọa độ File:Dòng
---

# Quy Trình Kiểm Toán Độ Phủ Animation & Micro-Interaction Toàn Hệ Thống (`/ui-skill`)

Quy trình chuẩn hóa rà soát toàn diện các thành phần giao diện chưa có animation hoặc thiếu micro-interaction trên đa nền tảng (Laravel + Inertia + Vue 3, Next.js + React, Nuxt, Blade), tuân thủ nghiêm ngặt nguyên tắc **Strict Read-Only**, **Evidence-Based 100%** và chuẩn thẩm mỹ **Quiet & High-Trust Motion (`anti-ui-slop`)**.

---

## 🛑 Nguyên Tắc An Toàn Bất Biến (Strict Safe Guards)

1. **STRICT READ-ONLY KHI KIỂM TOÁN**: Tuyệt đối không tự ý sửa hoặc xóa bất kỳ file mã nguồn nào trong quá trình chạy `/ui-skill`.
2. **BẰNG CHỨNG THẬT 100% (`File:Dòng`)**: Mọi vị trí được báo cáo là "chưa có animation" hoặc "đóng/mở giật cục" phải đi kèm tọa độ `File:Dòng` chính xác từ script quét `scan-missing-animations.sh` và đối chiếu mã nguồn thật.
3. **PHÂN BIỆT RÕ `ACTIVE` VS `ORPHAN`**: Kết hợp `graphify-out/graph.json` và phân tích import/tag để tách riêng các Orphan Components (không được sử dụng trên web), giúp đội ngũ chỉ tập trung bổ sung animation cho các trang và component đang chạy thực tế.
4. **TÔN TRỌNG `prefers-reduced-motion`**: Mọi giải pháp đề xuất bổ sung animation đều phải đi kèm cơ chế giảm chuyển động cho người dùng nhạy cảm.

---

## 3 Bước Thực Thi Chi Tiết

### Bước 1: Quét Tự Động Toàn Hệ Thống (`scan-missing-animations.sh`)
- **Mục tiêu**: Thu thập số liệu định lượng về tỷ lệ `% độ phủ Animation` trên từng tầng giao diện và danh sách tọa độ `File:Dòng` thiếu animation.
- **Hành động**:
  - Nếu dự án chưa cài đặt bộ `ui-skill`, đồng bộ nhanh từ bản Global:
    ```bash
    "$HOME/.gemini/config/skills/ui-skill/scripts/bootstrap-ui-skill.sh" --export-to . --scan
    ```
  - Hoặc chạy trực tiếp script quét tại dự án:
    ```bash
    ./.agents/skills/ui-skill/scripts/scan-missing-animations.sh
    ```
- **Đầu ra**: Báo cáo chi tiết tự động sinh tại `scratch/ui-animation-audit-report.md`.

### Bước 2: Thẩm Định Sâu Theo 6 Nhóm Kiểm Toán Animation
- **Mục tiêu**: Đối chiếu kết quả quét tại `scratch/ui-animation-audit-report.md` với [animation-audit-checklist.md](../skills/ui-skill/references/animation-audit-checklist.md) trên 6 nhóm:
  1. **Nhóm 1 — Design Tokens & Global Motion Foundation**: `tailwind.config.*`, `@keyframes` trong SCSS/CSS, các thư viện motion trong `package.json` và rule `@media (prefers-reduced-motion: reduce)`.
  2. **Nhóm 2 — Layout Shell & Page/Route Transitions**: Chuyển trang Inertia/Next.js, Sticky Header khi cuộn, Mobile Navigation Drawer, Search Overlay, Floating CTA.
  3. **Nhóm 3 — Section Entrance & Scroll-Reveal Animations**: Các `Pages/` và Section lớn (Hero, Features, Stats Counter, Product/Post Grid, CTA Banner) đang hiển thị tĩnh hoàn toàn.
  4. **Nhóm 4 — Interactive Micro-Interactions (Hover / Focus / Active)**: Buttons, Links, Product Cards, Post Cards, Category Badges, Pagination, Form Controls thiếu `transition-*`, `duration-*`, `hover:*`, `group-hover:scale-*`, `active:scale-*`.
  5. **Nhóm 5 — Overlays, Modals, Drawers, Dropdowns, Accordions & Tabs**: Các khối đóng/mở bằng `v-if` / `v-show` / conditional render trần không bọc `<Transition>`, `<TransitionGroup>`, `AnimatePresence` gây bật/tắt đột ngột (`0ms`).
  6. **Nhóm 6 — Async Feedback, Loading Skeleton & State Transitions**: Nút Submit form thiếu trạng thái loading/spinner mượt, thiếu Skeleton `animate-pulse` khi chờ dữ liệu, Toast/Alert xuất hiện gắt.

### Bước 3: Xuất Báo Cáo Tổng Hợp 3 Bảng Chuẩn & Lộ Trình OpenSpec
- **Mục tiêu**: Trình bày bức tranh toàn cảnh rõ ràng, có thứ tự ưu tiên (`P0` / `P1` / `P2`) để người dùng quyết định triển khai.
- **Đầu ra bắt buộc**:
  - **Bảng 1**: Tổng quan Tỷ lệ `% độ phủ Animation` theo từng tầng (`Global Motion & Tokens`, `Layout Shell`, `Feature Pages`, `Active UI Components`, `Orphan Components`).
  - **Bảng 2**: Danh sách chi tiết các thành phần **CHƯA CÓ Animation** kèm tọa độ `File:Dòng`, mức độ ưu tiên (`P0`/`P1`/`P2`), hiện trạng code và class/component mẫu đề xuất.
  - **Bảng 3**: Đề xuất lộ trình chia Phase qua `/opsx-propose` (Phase 1: `P0` Global Tokens & Overlays/Drawers; Phase 2: `P1` Cards, Buttons & Form Micro-interactions; Phase 3: `P2` Page Sections Scroll-Reveal & Skeletons) và chờ người dùng phê duyệt.
