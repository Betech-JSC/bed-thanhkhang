---
name: opsx-ui-audit
description: Thực thi Kiểm toán Giao diện 2 Phase chỉ đọc (Strict Read-Only UI & Typography Audit) trước khi mở OpenSpec change dọn giao diện. Bao gồm Phase 1 quét sạch tàn dư Theme/Thương hiệu cũ (<OLD_BRAND> -> <NEW_BRAND>) và Phase 2 đối chiếu chuẩn hóa Typography toàn diện với Figma MCP (get_figma_data).
---

# OPSX UI Audit — Kiểm Toán Giao Diện & Typography 2 Phase (Strict Read-Only)

Skill này cung cấp quy trình kiểm toán giao diện **chỉ đọc 100% (Strict Read-Only)** dành cho các dự án chuyển đổi từ một Base CMS / dự án cũ sang thương hiệu mới (ví dụ `<OLD_BRAND>` -> `<NEW_BRAND>`: `FunkyStyle` -> `Saigonchem`, `Funky` -> `4V`...) hoặc cần rà soát độ khớp Typography giữa mã nguồn thực tế với thiết kế chuẩn trên **Figma MCP**.

---

## 1. Nguyên Tắc Bất Biến Khi Chạy `opsx-ui-audit`

1. **STRICT READ-ONLY (Cấm Sửa Code Trong Lúc Audit)**:
   - Trong suốt quá trình chạy Phase 1 và Phase 2, Agent **TUYỆT ĐỐI KHÔNG** chỉnh sửa, xóa hay ghi đè bất kỳ file mã nguồn nào của dự án.
   - Đầu ra duy nhất của skill này là **Báo Cáo Kiểm Toán Có Bằng Chứng** với tọa độ chính xác đến từng `File:Dòng` (kèm giá trị `getComputedStyle` thực tế từ Playwright MCP nếu môi trường web đang bật).
   - Chỉ sau khi người dùng duyệt báo cáo kiểm toán mới chuyển sang bước lập `/opsx-propose` để sửa code có kiểm soát.
2. **Tham Số Hóa Rõ Ràng Đầu Vào**:
   - `<OLD_BRAND>`: Tên thương hiệu cũ, tiền tố class/biến cũ và danh sách từ khóa nghiệp vụ đặc thù cũ cần quét sạch.
   - `<NEW_BRAND>`: Tên thương hiệu mới và định hướng nhận diện mới.
   - `<FIGMA_SOURCE>`: Liên kết Figma hoặc `fileKey` + `nodeId` chuẩn dùng để trích xuất thông số thật qua `get_figma_data` (không suy đoán font/màu bằng mắt thường).
3. **Khai Thác Đồ Thị AST `graphify-out/graph.json`**:
   - Đọc `graphify-out/graph.json` đầu tiên để lập danh sách đầy đủ 100% `Pages`, `Layouts`, `Components` và phát hiện các component mồ côi (0 incoming imports) trước khi đi sâu đọc từng file.

---

## 2. Định Tuyến 2 Phase Kiểm Toán

Có thể chạy tuần tự cả 2 Phase trong một đợt tổng kiểm tra giao diện, hoặc chạy độc lập từng Phase theo yêu cầu của người dùng:

| Phase | Mục tiêu trọng tâm | Checklist & Mẫu báo cáo chi tiết |
| :--- | :--- | :--- |
| **Phase 1: Legacy UI & Brand Audit** | Quét 5 nhóm tàn dư giao diện/thương hiệu `<OLD_BRAND>`, đo tỷ lệ `% độ sạch` từng trang và lập danh sách tọa độ `File:Dòng` cần dọn dẹp. | [phase1-legacy-ui-audit.md](./references/phase1-legacy-ui-audit.md) |
| **Phase 2: Figma Typography Audit** | Đối chiếu 4 nhóm Typography với Figma MCP (`get_figma_data`), bắt lỗi mất kế thừa `font-family` trên form controls, lệch size/weight/line-height và xung đột CSS/PrimeVue. | [phase2-typography-audit.md](./references/phase2-typography-audit.md) |

---

## 3. Quy Trình Phối Hợp Multi-Agent Cho Đợt Kiểm Toán

1. **Bước 1 — Khảo sát AST & Cấu hình**:
   - Đọc `graphify-out/graph.json`, `tailwind.config.*`, các file entry CSS/SCSS (`app.scss`, `app.css`, `globals.css`), file layout gốc (`app.blade.php` / `layout.tsx`) và thư mục ngôn ngữ (`resources/lang` / `locales`).
2. **Bước 2 — Thực thi Phase 1 ([phase1-legacy-ui-audit.md](./references/phase1-legacy-ui-audit.md))**:
   - Quét đủ 5 nhóm tàn dư từ `<OLD_BRAND>` sang `<NEW_BRAND>`, chấm điểm `% độ sạch` cho từng trang/tầng giao diện và ghi nhận từng vị trí `File:Dòng`.
3. **Bước 3 — Thực thi Phase 2 ([phase2-typography-audit.md](./references/phase2-typography-audit.md))**:
   - Gọi `get_figma_data` trích xuất bảng chuẩn Typography từ Figma của `<NEW_BRAND>`.
   - Đối chiếu với cấu hình Tailwind/SCSS/Components thực tế (và đối chứng thêm `window.getComputedStyle` bằng Playwright MCP nếu dev server đang chạy).
4. **Bước 4 — Xuất Báo Cáo & Đề Xuất Chia OpenSpec Changes**:
   - Trình bày báo cáo theo đúng mẫu bảng chuẩn của Phase 1 & Phase 2.
   - Đề xuất chia các OpenSpec changes nhỏ gọn, an toàn để xử lý tiếp theo (ví dụ: `clean-legacy-ui-remnants`, `sync-figma-typography-system`), rồi dừng lại chờ người dùng phê duyệt trước khi gọi `/opsx-propose`.
