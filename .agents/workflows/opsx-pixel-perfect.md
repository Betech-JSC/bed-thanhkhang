---
description: Kiểm toán và chuẩn hóa Giao diện 100% Pixel-Perfect theo Figma MCP (Hỗ trợ Chạy Song Song Toàn Bộ Các Page Cùng Lúc 100% Parallel-Safe với --page <Ten_Page> kèm bảng [+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ chờ Confirm)
---

# Workflow `/opsx-pixel-perfect` — Kiểm Toán & Chuẩn Hóa Giao Diện 100% Pixel-Perfect (Parallel-Safe)

Khi người dùng gọi `/opsx-pixel-perfect` (có thể mở nhiều đoạn hội thoại chạy song song cho từng Page như `/opsx-pixel-perfect --page Home`, `/opsx-pixel-perfect --page Products/Show`, `/opsx-pixel-perfect --page Layout` hoặc gọi tổng thể), hãy đọc kỹ `.agents/skills/opsx-pixel-perfect-ui/SKILL.md` và thực hiện:

## 1. Chế Độ Chạy Song Song Theo Từng Page (`--page <Ten_Page>` — 100% Parallel-Safe)
Khi người dùng chỉ định tên Page (`Home`, `About`, `Products/Index`, `Products/Show`, `Services`, `Projects`, `Posts`, `Contact`, hoặc `Layout`):
1. **Bước 1 — Quét Read-Only Ra File Báo Cáo Riêng Của Page Đó**:
   - Chạy `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . --page <Ten_Page>`
   - Đọc báo cáo riêng tại `scratch/pixel-perfect-audit-<page-slug>.md` (không bao giờ đụng hàng với các tab Page khác đang chạy song song).
   - Gọi `get_figma_data` (`depth: 2` -> đi sâu từng section `nodeId`) chỉ trên đúng Frame Figma của Page đó.
2. **Bước 2 — Xuất Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` & Ranh Giới File Cô Lập**:
   - Hiển thị rõ danh sách file **`[ALLOWED TO EDIT IN THIS TAB]`** và **`[LOCKED FOR PARALLEL SAFETY]`**.
   - Xuất 3 bảng `[+] ADD`, `[-] REMOVE`, `[=] KEEP` của riêng Page đó và **Dừng lại chờ người dùng Confirm**.
3. **Bước 3 & 4 — Sửa Trong Phạm Vi Cô Lập & Nghiệm Thu**:
   - Sau khi người dùng Confirm, **chỉ sửa các file trong `[ALLOWED TO EDIT IN THIS TAB]`** (tuyệt đối không chạm vào `tailwind.config.js`, `app.scss`, `Header.vue`, `Footer.vue` hay file của Page khác trừ khi đang ở tab `--page Layout`).
   - Chạy lại `scan-pixel-perfect-traps.py . --page <Ten_Page>` để xác nhận PASS 100%.

## 2. Chế Độ Hub Tổng Điều Phối Toàn Dự Án (Không truyền `--page`)
1. Chạy `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py .`
2. Xuất bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` tổng thể chờ Confirm -> Xuất bảng lệnh 8 Tab để người dùng mở chạy song song cùng lúc.
