---
description: Kiểm toán và chuẩn hóa Animation theo 4 Quy Tắc Vàng (Single-Animation-Per-Element, No Opacity Blank Flash, Directional Horizontal Slide translate-x cho nút < / >, Giữ nguyên 100% ProductGallery có preview dưới) với bảng [+]/[-]/[=] tại Đoạn Hội Thoại Trung Tâm
---

# Workflow `/opsx-animation` — Kiểm Toán & Điều Phối Animation Chuẩn LUXI

Khi người dùng gọi `/opsx-animation`, hãy đọc kỹ `.agents/skills/opsx-animation/SKILL.md` và thực hiện đúng 4 bước tại **Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke)**:

1. **Bước 1 — Quét Kiểm Toán Read-Only (`scan-animation-rules.py`)**:
   - Chạy `python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .`
   - Đọc báo cáo `scratch/animation-rules-audit-report.md`.
   - Tuyệt đối không tự ý sửa hay xóa file ở bước này.
2. **Bước 2 — Xuất Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` & Dừng Chờ Confirm**:
   - Liệt kê rõ các mục `[+] ADD` (thêm trượt ngang `translate-x` cho các nút `< / >`), `[-] REMOVE` (bỏ `opacity-0` trong slider/scroll-reveal, bỏ chồng chéo animation), và `[=] KEEP AS-IS` (luôn **giữ nguyên 100% `ProductGallery.vue`** có hàng thumbnail preview ở dưới và `ProductInfoPanel.vue`).
   - **Dừng lại chờ người dùng Confirm** danh sách trước khi sang Bước 3.
3. **Bước 3 — Sinh Bộ Handoff Prompts Cho Các Đoạn Hội Thoại Con**:
   - Sau khi người dùng Confirm, sinh các gói Prompt nhỏ gọn kèm mẫu `<Transition>` trượt ngang để người dùng đem sang các đoạn hội thoại khác thực thi.
4. **Bước 4 — Nghiệm Thu Tại Đoạn Hội Thoại Trung Tâm**:
   - Chạy lại `scan-animation-rules.py` + `yarn frontend:build` + Playwright MCP để xác nhận PASS 100%.
