---
description: "Cài đặt & đồng bộ siêu tốc 1 giây (Pure 1-Second Installer) cho hệ sinh thái 4 bộ skill Hub-and-Spoke (/opsx-cms-sync, /opsx-pixel-perfect, /opsx-animation, /opsx-bootstrap)"
---

# Workflow: `/opsx-bootstrap` — Pure 1-Second Lightweight Installer

> ⚡ **QUY TẮC BẮT BUỘC (1 LỆNH — 1 GIÂY — DỪNG NGAY)**:
> Khi người dùng gọi `/opsx-bootstrap`, Agent **CHỈ CHẠY DUY NHẤT 1 LỆNH BASH DƯỚI ĐÂY** (trực tiếp, không cần spawn nhiều subagent, không đọc thêm `composer.json`/`package.json`, không sửa thủ công `openspec/config.yaml` hay `AGENTS.md`, không chạy `graphify`):

1. Chạy lệnh cài đặt siêu tốc (hoàn tất trong **1 giây**):
   ```bash
   "$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh" --export-to .
   ```
   *(Hoặc `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh` nếu chạy ngay tại repo nguồn).*
2. Ngay khi lệnh trên trả về `exit code 0`, **in báo cáo hoàn tất cho người dùng và KẾT THÚC NGAY LƯỢT PHẢN HỒI** (hướng dẫn người dùng gõ `/opsx-cms-sync`, `/opsx-pixel-perfect`, hoặc `/opsx-animation` ở đoạn hội thoại tiếp theo).
