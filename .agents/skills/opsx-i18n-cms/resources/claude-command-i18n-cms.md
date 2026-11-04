---
name: "OPSX: i18n CMS"
description: "Đoạn Hội Thoại Trung Tâm kiểm toán Đa Ngôn Ngữ Backend CMS (VI / EN) & Tự động Fallback EN -> VI, trình Bảng Quyết Định [+] Thêm / [-] Bỏ / [=] Giữ và sinh Handoff Prompts chạy song song với /opsx-i18n-fe"
category: Workflow
tags: [openspec, i18n, cms, backend, fallback]
---

Đọc kỹ và tuân thủ 100% quy trình trong `.agents/skills/opsx-i18n-cms/SKILL.md`:

1. Chạy `python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .` (Strict Read-Only).
2. Đọc `scratch/i18n-cms-audit-report.md`, trình bày kết quả 4 Trụ cột Đa ngôn ngữ Backend CMS + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` và **DỪNG LẠI CHỜ USER CONFIRM**.
3. Sau khi User Confirm, xuất **Bộ Handoff Prompts** (có khóa ranh giới file `[LOCKED: resources/Frontend/**]`) để đem sang đoạn hội thoại con thực thi.
4. Khi hội thoại con hoàn tất, quay lại đây chạy lại `audit-i18n-cms.py` để nghiệm thu.
