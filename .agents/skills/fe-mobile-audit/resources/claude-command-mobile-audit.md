---
name: "OPSX: FE Mobile & Responsive UI Audit"
description: "Kiểm toán & chuẩn hóa Giao diện Mobile / Responsive toàn diện không cần Figma Mobile (Graph-Aware Static Scan + Runtime Viewport Check 320/360/390/768px cho lỗi tràn ngang M03/M04, spacing to quá M20, rớt chữ M11, nhiều cột M10, tap target nhỏ M07, font nhỏ M08/M09)"
category: Workflow
tags: [opsx, mobile-audit, responsive, tailwind, graph-aware, viewport]
---

Đọc kỹ `.agents/skills/fe-mobile-audit/SKILL.md` và `.agents/skills/fe-mobile-audit/references/mobile-checklist.md`, sau đó thực thi quy trình kiểm toán giao diện Mobile / Responsive:

1. **Bước 1 — Quét Tĩnh Kết Hợp Đồ Thị Phụ Thuộc (`Graph-Aware Static Scan`)**:
   - Chạy `python3 .agents/skills/fe-mobile-audit/scripts/mobile_audit.py . --graph auto --out docs/mobile`
   - Đọc `docs/mobile/MOBILE-AUDIT.md` và kiểm tra kỹ các lỗi khi đưa giao diện từ Figma Desktop xuống Mobile: spacing/padding/font quá lớn trên mobile (`M20`), rớt chữ/tràn chữ (`M11`), tràn ngang/fixed width (`M03`/`M04`/`M14`/`M16`), nhiều cột trên điện thoại (`M10`), tap target `< 44px` (`M07`), chữ/input quá nhỏ (`M08`/`M09`), bảng không cuộn ngang (`M12`), thiếu mobile menu (`M18`).
2. **Bước 2 — Kiểm Tra Runtime Đa Khung Nhìn (Nếu server đang chạy)**:
   - Chạy `node .agents/skills/fe-mobile-audit/scripts/viewport_check.mjs --base <URL> --routes / ... --out docs/mobile/runtime` để kiểm tra DOM thực tế ở `320px`, `360px`, `390px`, `768px`.
3. **Bước 3 — Xác Thực Mã Nguồn & Báo Cáo Chờ Confirm**:
   - Đối chiếu code thực tế của các Shared Components (`Header`, `Footer`, `Layout`, `Card`...) có độ ảnh hưởng cao nhất trên đồ thị, lập báo cáo `docs/mobile/MOBILE-REPORT.md` kèm bảng đề xuất sửa Mobile-First (giữ nguyên giao diện Desktop `md:`/`lg:`) và **Dừng lại chờ người dùng Confirm**.
4. **Bước 4 — Sửa Lỗi & Nghiệm Thu**:
   - Sau khi người dùng Confirm, tiến hành cập nhật class responsive Mobile-First, chạy lại `mobile_audit.py` và build FE để nghiệm thu.
