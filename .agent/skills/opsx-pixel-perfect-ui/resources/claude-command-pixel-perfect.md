---
name: "OPSX: Pixel-Perfect UI"
description: "Kiểm toán & chuẩn hóa Giao diện 100% Pixel-Perfect theo Figma MCP (Hỗ trợ Chạy Song Song Toàn Bộ Các Page Cùng Lúc 100% Parallel-Safe với --page <Ten_Page> kèm bảng [+] THÊM / [-] BỎ / [=] GIỮ chờ Confirm)"
category: Workflow
tags: [opsx, figma, pixel-perfect, ui-audit, tailwind, parallel-safe]
---

Đọc kỹ `.agents/skills/opsx-pixel-perfect-ui/SKILL.md` và `.agents/skills/opsx-pixel-perfect-ui/references/pixel-perfect-7-golden-rules.md`, sau đó thực thi:

- **Nếu người dùng chỉ định 1 Page cụ thể (`--page <Ten_Page>` — Chạy song song nhiều tab an toàn 100%)**:
  1. Chạy `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . --page <Ten_Page>` (tự động xuất ra `scratch/pixel-perfect-audit-<page-slug>.md`).
  2. Trình bày danh sách **`[ALLOWED TO EDIT IN THIS TAB]`** + 3 bảng **`[+] THÊM GÌ (ADD)`**, **`[-] BỎ GÌ (REMOVE)`**, **`[=] GIỮ LẠI GÌ (KEEP)`** của riêng Page đó và **Dừng lại chờ người dùng Confirm**.
  3. Sau khi người dùng Confirm, chỉ sửa các file thuộc `[ALLOWED TO EDIT IN THIS TAB]` của Page đó (không đụng vào file dùng chung hay Page khác) và nghiệm thu.
- **Nếu người dùng gọi tổng thể toàn dự án**:
  1. Chạy `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py .`
  2. Trình bày 3 bảng `[+] THÊM / [-] BỎ / [=] GIỮ` chờ Confirm -> Xuất bảng lệnh chạy song song cho toàn bộ các Page.
