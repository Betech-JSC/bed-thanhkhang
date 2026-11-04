---
description: Hub điều phối Hệ thống CMS Dynamic A–Z, Kiểm toán Độ Phủ Kết Nối UI-to-CMS & Kiểm Toán Toàn Bộ Điều Hướng theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke). Tự động khám phá các hệ thống đã có bên CMS, liệt kê mọi vị trí UI chưa connect CMS + mọi lỗi điều hướng, trình Bảng Quyết Định [+] Thêm / [-] Bỏ / [=] Giữ chờ Confirm và sinh Bộ Handoff Prompts dựa trên hệ thống CMS sẵn có.
---

# Workflow `/opsx-cms-sync` — Đoạn Hội Thoại Trung Tâm Kiểm Toán Kết Nối UI-to-CMS, Điều Hướng & Sinh Handoff Prompts

Khi người dùng kích hoạt `/opsx-cms-sync`, hãy đọc `.agents/skills/opsx-cms-sync/SKILL.md` và thực thi đúng 4 bước của **Đoạn Hội Thoại Trung Tâm (Hub Conversation)**:

1. **Bước 1 — Pre-Flight Audit 4 Module (Strict Read-Only)**:
   - Chạy công cụ kiểm toán toàn diện:
     ```bash
     python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .
     ```
   - Đọc báo cáo `scratch/cms-sync-audit-report.md` chứa kết quả của 4 Module:
     - **Module 1**: Bản đồ các hệ thống đã được tạo sẵn bên CMS (`app/Models/**`, `$fillable`, `$translatedAttributes`, Admin Controllers).
     - **Module 2**: Danh sách các vị trí trên UI **CHƯA CONNECT** với CMS (Frontend Controller render tĩnh, mảng tĩnh trong `.vue`, SĐT/Email/Bản đồ/MXH hardcode, `<form>` chưa gọi `ContactController`) kèm ánh xạ vào đúng Hệ thống CMS sẵn có.
     - **Module 3**: Danh sách toàn bộ lỗi **ĐIỀU HƯỚNG** trên UI (`href="#"`, lệch tên `route(...)`, Card thiếu `<Link>`/`slug`, `<button>` thiếu `@click`).
     - **Module 4**: Trạng thái 8 Trụ cột CMS & Hợp đồng khóa Cache.

2. **Bước 2 — Trình Bày 4 Bảng Kiểm Toán + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` & Dừng Chờ Confirm**:
   - Trình bày rõ ràng:
     1. **Bản đồ Hệ thống đã có bên CMS**
     2. **Danh sách các vị trí UI chưa connect với CMS (kèm Model CMS đích)**
     3. **Danh sách lỗi điều hướng toàn trang**
     4. **Bảng Quyết Định `[+] Thêm gì / [-] Bỏ gì / [=] Giữ lại gì`**
   - **DỪNG LẠI HỎI NGƯỜI DÙNG CONFIRM** trước khi xuất Handoff Prompts hoặc sửa code.

3. **Bước 3 — Xuất Bộ "Handoff Prompts" Bám Sát Hệ Thống CMS Đã Có (Sau Khi Confirm)**:
   - Xuất các khối **Handoff Prompts (Gói 1: Đấu nối UI vào Hệ thống CMS sẵn có, Gói 2: Đấu nối toàn bộ Điều hướng & CTA, Gói 3: Đồng bộ 8 Trụ cột & Khóa Cache)** đã được sinh sẵn trong Mục 6 của `scratch/cms-sync-audit-report.md` để người dùng copy sang các đoạn hội thoại khác tiến hành fix.

4. **Bước 4 — Quay Về Hub Nghiệm Thu E2E**:
   - Khi các đoạn hội thoại con hoàn tất, chạy lại `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` để xác nhận `0 vị trí UI chưa connect CMS`, `0 lỗi điều hướng`, và `8/8 PASS`.
