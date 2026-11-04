---
name: opsx-ui-audit
description: Thực thi Kiểm toán Giao diện & Dữ liệu CMS 2 Phase (Strict Read-Only UI, CMS Hardcoded Return & Typography Audit) trước khi mở OpenSpec change dọn giao diện và dữ liệu cứng. Bao gồm Phase 1 quét sạch tàn dư Theme/Thương hiệu cũ (<OLD_BRAND> -> <NEW_BRAND>) + kiểm tra toàn bộ CMS có đang hardcode và return các giá trị cố định không để liệt kê & remove, và Phase 2 đối chiếu chuẩn hóa Typography toàn diện với Figma MCP (get_figma_data).
---

# OPSX UI Audit — Kiểm Toán Giao Diện, Dữ Liệu Hardcode CMS & Typography 2 Phase

Skill này cung cấp quy trình kiểm toán giao diện và dữ liệu CMS dành cho các dự án chuyển đổi từ một Base CMS / dự án cũ sang thương hiệu mới (ví dụ `<OLD_BRAND>` -> `<NEW_BRAND>`: `FunkyStyle` -> `Saigonchem`, `Funky` -> `Luxi`...), kiểm tra toàn bộ CMS xem có đang **hardcode và return các giá trị cố định** không để liệt kê & remove, đồng thời rà soát độ khớp Typography giữa mã nguồn thực tế với thiết kế chuẩn trên **Figma MCP**.

---

## 1. Nguyên Tắc Bất Biến Khi Chạy `opsx-ui-audit`

1. **KIỂM TOÁN CÓ BẰNG CHỨNG & LIỆT KÊ TỌA ĐỘ `File:Dòng`**:
   - Trong bước quét kiểm toán (Phase 1 & Phase 2), Agent không tự ý xóa file vật lý (`rm`).
   - Đầu ra bắt buộc là **Báo Cáo Kiểm Toán Có Bằng Chứng** với tọa độ chính xác đến từng `File:Dòng` (kèm output từ công cụ quét hardcode `scan-cms-hardcoded.py` và giá trị `getComputedStyle` thực tế từ Playwright MCP nếu môi trường web đang bật).
   - Đối với các vị trí **CMS đang hardcode và return giá trị cố định**: sau khi liệt kê đầy đủ danh sách tọa độ `File:Dòng`, tiến hành **remove** các khối dữ liệu/lệnh return cố định đó để kết nối đúng về nguồn dữ liệu động của CMS (Eloquent Model / Settings / Inertia Props hoặc mảng rỗng `[]` chuẩn).
2. **Tham Số Hóa Rõ Ràng Đầu Vào**:
   - `<OLD_BRAND>`: Tên thương hiệu cũ, tiền tố class/biến cũ và danh sách từ khóa nghiệp vụ đặc thù cũ cần quét sạch.
   - `<NEW_BRAND>`: Tên thương hiệu mới và định hướng nhận diện mới.
   - `<FIGMA_SOURCE>`: Liên kết Figma hoặc `fileKey` + `nodeId` chuẩn dùng để trích xuất thông số thật qua `get_figma_data` (không suy đoán font/màu bằng mắt thường).
3. **Khai Thác Đồ Thị AST `graphify-out/graph.json` & Script Quét Hardcode CMS**:
   - Đọc `graphify-out/graph.json` đầu tiên để lập danh sách đầy đủ 100% `Controllers`, `Pages`, `Layouts`, `Components` và phát hiện các component mồ côi (`0 incoming imports`).
   - Chạy script quét tự động:
     ```bash
     python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .
     ```
     *(Hoặc `"$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py" .`)* để phát hiện toàn bộ các hàm/biến đang hardcode và return giá trị cố định trong CMS.

---

## 2. Định Tuyến 2 Phase Kiểm Toán

Có thể chạy tuần tự cả 2 Phase trong một đợt tổng kiểm tra giao diện & CMS, hoặc chạy độc lập từng Phase theo yêu cầu của người dùng:

| Phase | Mục tiêu trọng tâm | Checklist & Mẫu báo cáo chi tiết |
| :--- | :--- | :--- |
| **Phase 1: Legacy UI, Brand & CMS Hardcoded Return Audit** | Quét 6 nhóm tàn dư giao diện/thương hiệu `<OLD_BRAND>` **VÀ kiểm tra toàn bộ CMS (Backend & Frontend) có đang hardcode và return các giá trị cố định không**, lập danh sách tọa độ `File:Dòng` và tiến hành remove hardcode return cố định. | [phase1-legacy-ui-audit.md](./references/phase1-legacy-ui-audit.md) |
| **Phase 2: Figma Typography Audit** | Đối chiếu 4 nhóm Typography với Figma MCP (`get_figma_data`), bắt lỗi mất kế thừa `font-family` trên form controls, lệch size/weight/line-height và xung đột CSS/PrimeVue. | [phase2-typography-audit.md](./references/phase2-typography-audit.md) |

---

## 3. Quy Trình Phối Hợp Multi-Agent Cho Đợt Kiểm Toán

1. **Bước 1 — Khảo sát AST, Cấu hình & Quét Hardcode CMS**:
   - Đọc `graphify-out/graph.json`, `tailwind.config.*`, các file entry CSS/SCSS (`app.scss`, `app.css`, `globals.css`), file layout gốc (`app.blade.php` / `layout.tsx`) và thư mục ngôn ngữ (`resources/lang` / `locales`).
   - Chạy `python3 "$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py" .` để thu thập toàn bộ vị trí nghi vấn hardcode và return giá trị cố định trên cả Backend và Frontend.
2. **Bước 2 — Thực thi Phase 1 ([phase1-legacy-ui-audit.md](./references/phase1-legacy-ui-audit.md))**:
   - Quét đủ 6 nhóm (5 nhóm tàn dư từ `<OLD_BRAND>` sang `<NEW_BRAND>` + **Nhóm 6: CMS Hardcoded & Fixed Return Values**), chấm điểm `% độ sạch` cho từng trang/tầng giao diện và ghi nhận từng vị trí `File:Dòng`.
3. **Bước 3 — Thực thi Phase 2 ([phase2-typography-audit.md](./references/phase2-typography-audit.md))**:
   - Gọi `get_figma_data` trích xuất bảng chuẩn Typography từ Figma của `<NEW_BRAND>`.
   - Đối chiếu với cấu hình Tailwind/SCSS/Components thực tế (và đối chứng thêm `window.getComputedStyle` bằng Playwright MCP nếu dev server đang chạy).
4. **Bước 4 — Xuất Báo Cáo, Remove Hardcoded Returns & Đề Xuất Chia OpenSpec Changes**:
   - Trình bày báo cáo theo đúng mẫu bảng chuẩn của Phase 1 (kèm bảng liệt kê đầy đủ các điểm CMS Hardcode & Fixed Return) & Phase 2.
   - Tiến hành **remove** các giá trị hardcode/return cố định để kết nối đúng nguồn dữ liệu động CMS (hoặc đóng gói thành OpenSpec change `remove-cms-hardcoded-returns`, `clean-legacy-ui-remnants`, `sync-figma-typography-system` theo yêu cầu của người dùng).
