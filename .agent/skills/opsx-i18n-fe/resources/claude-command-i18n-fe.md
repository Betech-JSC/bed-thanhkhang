---
name: "OPSX: i18n FE"
description: "Đoạn Hội Thoại Trung Tâm kiểm toán Đa Ngôn Ngữ Giao Diện Frontend (VI / EN) chỉ trong phạm vi các trang Figma, trình Bảng Quyết Định [+] Thêm / [-] Bỏ / [=] Giữ và sinh Handoff Prompts chạy song song với /opsx-i18n-cms"
category: Workflow
tags: [openspec, i18n, frontend, figma, vue]
---

Đọc kỹ và tuân thủ 100% quy trình trong `.agents/skills/opsx-i18n-fe/SKILL.md`:

1. Chạy `python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py .` (hoặc `--page <Scope>`, `--pages <List>`) ở chế độ Strict Read-Only.
2. Đọc báo cáo `scratch/i18n-fe-audit-*.md`, trình bày thống kê các chuỗi Tiếng Việt fix cứng trong phạm vi trang Figma + trạng thái `vi.json`/`en.json` + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` và **DỪNG LẠI CHỜ USER CONFIRM**.
3. Sau khi User Confirm, xuất **Bộ Handoff Prompts** (có khóa ranh giới file `[LOCKED: app/**, config/**, resources/Backend/**]`) để đem sang đoạn hội thoại con thực thi.
4. Khi hội thoại con hoàn tất, quay lại đây chạy lại `scan-i18n-fe.py` và `yarn frontend:build` để nghiệm thu.
