---
name: "OPSX: CMS Sync"
description: "Đoạn Hội Thoại Trung Tâm kiểm toán độ phủ kết nối UI-to-CMS, kiểm toán toàn bộ điều hướng, trình Bảng Quyết Định [+] Thêm / [-] Bỏ / [=] Giữ và sinh Handoff Prompts dựa trên các hệ thống đã có bên CMS"
category: Workflow
tags: [openspec, cms, figma, audit, navigation, handoff]
---

Đọc kỹ và tuân thủ 100% quy trình trong `.agents/skills/opsx-cms-sync/SKILL.md`:

1. Chạy `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` (Strict Read-Only) để quét:
   - **Module 1**: Bản đồ các hệ thống đã có bên CMS (`app/Models/`, `$fillable`, `$translatedAttributes`, Admin Controllers).
   - **Module 2**: Toàn bộ các chỗ trên UI **CHƯA CONNECT** với CMS và ánh xạ vào đúng Hệ thống CMS sẵn có.
   - **Module 3**: Toàn bộ lỗi **ĐIỀU HƯỚNG** trên UI (`href="#"`, lệch tên `route(...)`, Card thiếu `<Link>`/`slug`, `<button>` thiếu `@click`).
   - **Module 4**: 8 Trụ cột CMS & Hợp đồng khóa Cache.
2. Trình bày 4 bảng trên kèm Bảng Quyết Định **`[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ`** và **DỪNG LẠI CHỜ USER CONFIRM**.
3. Sau khi User Confirm, xuất **Bộ Handoff Prompts (Gói 1, Gói 2, Gói 3)** dựa sát vào các Model đã có bên CMS để đem sang các đoạn hội thoại khác tiến hành fix.
4. Khi hội thoại con hoàn tất, quay lại đây chạy lại `audit-cms-sync.py` và `php artisan test` để nghiệm thu.
