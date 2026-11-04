---
description: Hub điều phối Đa Ngôn Ngữ Backend CMS (VI / EN) & Tự Động Fallback EN -> VI theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke). Kiểm toán config/app.php, config/translatable.php, $translatedAttributes, Form.vue, Configs.vue và khóa Cache _{locale}. Chạy song song an toàn với /opsx-i18n-fe.
---

# Workflow `/opsx-i18n-cms` — Đoạn Hội Thoại Trung Tâm Điều Phối Đa Ngôn Ngữ Backend CMS (`VI / EN` & Auto-Fallback `EN -> VI`)

Khi người dùng kích hoạt `/opsx-i18n-cms`, hãy đọc `.agents/skills/opsx-i18n-cms/SKILL.md` và thực thi đúng 4 bước của **Đoạn Hội Thoại Trung Tâm**:

1. **Bước 1 — Pre-Flight Audit 4 Trụ Cột Đa Ngôn Ngữ CMS (Strict Read-Only)**:
   - Chạy công cụ kiểm toán:
     ```bash
     python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .
     ```
   - Đọc báo cáo `scratch/i18n-cms-audit-report.md` (kiểm tra `config/app.php`, `config/translatable.php`, các Model & `Form.vue` / `Configs.vue`, cơ chế lưu không lỗi Validation 422 + Fallback `EN -> VI`, và khóa Cache `_{$locale}`).

2. **Bước 2 — Trình Bày Kết Quả Kiểm Toán + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` & Dừng Chờ Confirm**:
   - Trình bày 4 bảng trong báo cáo và **DỪNG LẠI CHỜ USER CONFIRM**.

3. **Bước 3 — Xuất Bộ "Handoff Prompts" Có Khóa Ranh Giới File Song Song (Sau Khi Confirm)**:
   - Xuất khối Handoff Prompt trong Mục 6 của `scratch/i18n-cms-audit-report.md` (khóa chặt `[LOCKED FOR PARALLEL SAFETY: resources/Frontend/**]` để chạy song song với `/opsx-i18n-fe`).

4. **Bước 4 — Quay Về Hub Nghiệm Thu**:
   - Khi đoạn hội thoại con hoàn tất, chạy lại `python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .` để xác nhận `4/4 PASS`.
