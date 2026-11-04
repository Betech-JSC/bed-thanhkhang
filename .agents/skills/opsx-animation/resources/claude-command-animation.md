---
name: "OPSX: Animation Standard"
description: "Kiểm toán & điều phối Animation chuẩn 4 Quy Tắc Vàng (Trượt ngang translate-x cho nút < / >, không opacity-0 flash, giữ nguyên 100% ProductGallery có preview dưới)"
category: Workflow
tags: [opsx, animation, motion, ui-skill, vue-transition]
---

Đọc kỹ `.agents/skills/opsx-animation/SKILL.md` và `.agents/skills/opsx-animation/references/animation-4-golden-rules.md`, sau đó thực thi quy trình 4 bước tại Đoạn Hội Thoại Trung Tâm:

1. Chạy `python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .` (Strict Read-Only).
2. Trình bày 3 bảng **`[+] THÊM GÌ (ADD)`**, **`[-] BỎ GÌ (REMOVE)`**, **`[=] GIỮ LẠI GÌ (KEEP AS-IS — bao gồm giữ nguyên 100% ProductGallery.vue có hàng preview dưới)`** và **Dừng lại chờ người dùng Confirm**.
3. Sau khi người dùng Confirm, sinh bộ **Handoff Prompts** để đem sang các đoạn hội thoại con giải quyết.
4. Khi hoàn tất, nghiệm thu bằng `scan-animation-rules.py` + `yarn frontend:build`.
