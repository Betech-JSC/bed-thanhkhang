---
description: Hub điều phối Đa Ngôn Ngữ Giao Diện Frontend (VI / EN) — CHỈ QUÉT CÁC TRANG CÓ TRONG DESIGN FIGMA theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke). Quét text Tiếng Việt fix cứng trong <template> & <script>, kiểm tra vi.json/en.json và nút chuyển ngôn ngữ VI | EN trên Header. Chạy song song an toàn với /opsx-i18n-cms.
---

# Workflow `/opsx-i18n-fe` — Đoạn Hội Thoại Trung Tâm Điều Phối Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma (`VI / EN`)

Khi người dùng kích hoạt `/opsx-i18n-fe`, hãy đọc `.agents/skills/opsx-i18n-fe/SKILL.md` và thực thi đúng 4 bước của **Đoạn Hội Thoại Trung Tâm**:

1. **Bước 1 — Pre-Flight Audit Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma (Strict Read-Only)**:
   - Chạy công cụ quét tự động:
     ```bash
     # Quét toàn bộ các trang Figma Active (tự động bỏ qua boilerplate thừa)
     python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py .

     # Hoặc quét cô lập cho 1 trang cụ thể để chạy song song nhiều hội thoại
     python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page Home
     ```
   - Đọc báo cáo `scratch/i18n-fe-audit-report.md` (hoặc `scratch/i18n-fe-audit-<slug>.md`).

2. **Bước 2 — Trình Bày Kết Quả Kiểm Toán + Bảng Quyết Định `[+] THÊM / [-] BỎ QUA / [=] GIỮ` & Dừng Chờ Confirm**:
   - Trình bày thống kê chuỗi Tiếng Việt fix cứng theo từng file Figma, trạng thái `vi.json` / `en.json`, trạng thái nút `VI | EN` trên Header và **DỪNG LẠI CHỜ USER CONFIRM**.

3. **Bước 3 — Xuất Bộ "Handoff Prompts" Có Khóa Ranh Giới File Song Song (Sau Khi Confirm)**:
   - Xuất khối Handoff Prompt trong Mục 5 của báo cáo (khóa chặt `[LOCKED FOR PARALLEL SAFETY: app/**, config/**, resources/Backend/**]` để chạy song song với `/opsx-i18n-cms`).

4. **Bước 4 — Quay Về Hub Nghiệm Thu**:
   - Khi đoạn hội thoại con hoàn tất, chạy lại `python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py .` và `yarn frontend:build` để xác nhận `0` chuỗi Tiếng Việt fix cứng trong `<template>` và `0` khóa thiếu trong `en.json`.
