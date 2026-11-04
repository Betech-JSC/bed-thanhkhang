---
description: Kiểm toán toàn diện độ phủ Animation & Micro-Interaction trên hệ thống giao diện (Strict Read-Only UI Animation Audit) và liệt kê đầy đủ các thành phần chưa có hiệu ứng kèm tọa độ File:Dòng
---

# Lệnh /opsx:ui-skill — UI Animation & Micro-Interaction Coverage Audit

Lệnh này hướng dẫn Claude Code thực hiện quy trình kiểm toán toàn diện các thành phần giao diện chưa có animation hoặc thiếu micro-interaction theo tiêu chuẩn bằng chứng 100% (`File:Dòng`) và quy tắc **Strict Read-Only**.

## Các bước thực hiện:

1. **Bước 1: Chạy Script Quét Tự Động Toàn Hệ Thống (`scan-missing-animations.sh`)**
   - Nếu dự án chưa có `.agents/skills/ui-skill/scripts/scan-missing-animations.sh`, chạy lệnh đồng bộ từ bản Global:
     `"$HOME/.gemini/config/skills/ui-skill/scripts/bootstrap-ui-skill.sh" --export-to . --scan`
   - Hoặc thực thi trực tiếp:
     `./.agents/skills/ui-skill/scripts/scan-missing-animations.sh`
   - Đọc kết quả vừa sinh tại `scratch/ui-animation-audit-report.md`.

2. **Bước 2: Rà Soát Sâu 6 Nhóm Kiểm Toán Animation (`6 Animation Audit Dimensions`)**
   - **Nhóm 1 (Global Motion & Tokens)**: Kiểm tra `tailwind.config.*`, `@keyframes` trong SCSS/CSS, các thư viện motion trong `package.json` và rule bảo vệ `@media (prefers-reduced-motion: reduce)`.
   - **Nhóm 2 (Layout Shell & Page Transitions)**: Kiểm tra chuyển trang, Sticky Header khi cuộn, Mobile Navigation Drawer, Search Popup, Floating CTA.
   - **Nhóm 3 (Section Entrance & Scroll-Reveal)**: Kiểm tra các `Pages/` và Section lớn đang hiển thị tĩnh hoàn toàn (`0%` entrance/reveal).
   - **Nhóm 4 (Interactive Micro-Interactions)**: Kiểm tra Buttons, Links, Product Cards, Post Cards, Badges, Pagination, Form Controls thiếu `transition-*`, `duration-*`, `hover:*`, `group-hover:scale-*`, `active:scale-*`.
   - **Nhóm 5 (Overlays, Modals, Drawers, Dropdowns, Accordions & Tabs)**: Phát hiện các khối dùng `v-if` / `v-show` / conditional render trần mà thiếu `<Transition>`, `<TransitionGroup>`, `AnimatePresence`.
   - **Nhóm 6 (Async Feedback & Skeletons)**: Kiểm tra nút Submit form thiếu loading state transition, thiếu Skeleton `animate-pulse`, Toast/Alert.

3. **Bước 3: Trình Bày Báo Cáo 3 Bảng Chuẩn & Đề Xuất Lộ Trình OpenSpec**
   - **Bảng 1**: Tỷ lệ `% độ phủ Animation` theo từng tầng (`Global Motion & Tokens`, `Layouts`, `Pages`, `Active Components`, và tách riêng `Orphan Components`).
   - **Bảng 2**: Danh sách chi tiết các vị trí **CHƯA CÓ Animation** kèm tọa độ `File:Dòng`, mức ưu tiên (`P0` / `P1` / `P2`), hiện trạng và giải pháp đề xuất tuân thủ `anti-ui-slop` + `prefers-reduced-motion`.
   - **Bảng 3**: Lộ trình đề xuất `/opsx-propose` theo 3 Phase (`P0` -> `P1` -> `P2`).

## ⚠️ Cảnh báo an toàn:
- **STRICT READ-ONLY**: Không tự ý sửa hoặc xóa bất kỳ file mã nguồn nào khi đang chạy kiểm toán `/opsx:ui-skill`.
- Chỉ tiến hành sửa code sau khi người dùng đã xem báo cáo và phê duyệt kế hoạch OpenSpec.
