---
name: opsx-pixel-perfect-ui
description: Đóng gói quy trình kiểm toán và chuẩn hóa Giao diện 100% Pixel-Perfect theo Figma MCP (Box-Model & 1-Screen Top Offset Math, @tailwindcss/aspect-ratio Trap, Structural & Section Count Parity, Interactive Variant Parity, SVG Vector <path> Direction & Retina Logo/Favicon, W3C overflow-x: clip Single Scrollbar & Sticky Preservation). Hỗ trợ Chạy Song Song Toàn Bộ Các Page Cùng Lúc (100% Parallel Multi-Tab Safe với --page <Ten_Page> tự tách báo cáo riêng và cô lập ranh giới file) kèm bảng [+]/[-]/[=] chờ Confirm. Sử dụng khi người dùng gọi "/opsx-pixel-perfect" hoặc "/opsx:pixel-perfect".
---

# OPSX Pixel-Perfect UI — Hệ Thống Đóng Gói & Kiểm Toán Giao Diện 100% Chuẩn Figma (`/opsx-pixel-perfect`)

Skill này đóng gói toàn bộ 7 quy chuẩn kỹ thuật để biến một dự án Web (Laravel + Inertia + Vue 3 / Next.js + React) đạt **100% Pixel-Perfect so với thiết kế Figma**, không lệch kích thước khung (`0px`), không tự chế thêm/bớt section, không bị 2 thanh cuộn hay chết `position: sticky`, và không bị ngược icon SVG hay mờ Logo/Favicon.

Đặc biệt, `/opsx-pixel-perfect` được thiết kế **100% Parallel-Safe (Chạy Song Song Toàn Bộ Các Page Cùng Lúc)**:
- Bạn có thể mở **6–8 đoạn hội thoại (tabs) cùng một lúc**, mỗi tab phụ trách **1 Page riêng biệt** (`--page Home`, `--page About`, `--page Products/Index`, `--page Products/Show`, `--page Services`, `--page Projects`, `--page Posts`, `--page Contact`).
- Mỗi tab tự động xuất ra **1 file báo cáo riêng biệt** (`scratch/pixel-perfect-audit-<page-slug>.md`) và khóa chặt **Ranh Giới Cô Lập File (`Parallel File Isolation`)** để các tab chạy đồng thời không bao giờ ghi đè hay xung đột code của nhau!

---

## 1. Chế Độ Chạy Song Song Nhiều Đoạn Hội Thoại (`1 Conversation = 1 Page` — 100% Parallel-Safe)

### 🛡️ 3 Quy Tắc Bảo Vệ Khi Chạy Song Song Toàn Bộ Các Page Cùng Lúc:
1. **Tự động tách file báo cáo riêng theo từng `--page`**:
   - Khi chạy `scan-pixel-perfect-traps.py . --page <Ten_Page>`, script tự động ghi báo cáo ra `scratch/pixel-perfect-audit-<page-slug>.md` (ví dụ: `pixel-perfect-audit-home.md`, `pixel-perfect-audit-products-show.md`). Mở 8 tab quét cùng lúc hoàn toàn độc lập 100%.
2. **Ranh giới cô lập file tuyệt đối (`Strict Parallel File Boundary`)**:
   - Tab chạy `--page <Ten_Page>` **CHỈ ĐƯỢC PHÉP SỬA** các file nằm trong mục **`[ALLOWED TO EDIT IN THIS TAB]`** (`Pages/<Ten_Page>.vue` và các component con riêng `Components/<Nhom_Page>/*.vue`).
   - **TUYỆT ĐỐI KHÔNG SỬA** các file nền tảng dùng chung nằm trong mục **`[LOCKED FOR PARALLEL SAFETY]`** (`tailwind.config.js`, `app.scss`, `Layouts/*.vue`, `Header.vue`, `Footer.vue`) từ bên trong các tab Page con. Nếu cần chỉnh sửa nền tảng chung, hãy dùng riêng 1 tab `--page Layout`.
3. **Không chạy trùng `yarn frontend:build` cùng một giây**:
   - Trong lúc các tab đang sửa song song, từng tab kiểm tra nhanh bằng `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . --page <Ten_Page>`. Sau khi các tab sửa xong, chạy `yarn frontend:build` 1 lần để nghiệm thu toàn bộ bundle.

---

### Quy Trình 4 Bước Trong Mỗi Tab Page (`--page <Ten_Page>`)

#### Bước 1 — Quét Read-Only Riêng Page Đó & Đối Chiếu Frame Figma Của Page Đó
1. Chạy lệnh quét khoanh vùng đúng Page:
   ```bash
   python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . --page <Ten_Page>
   ```
   Đọc file báo cáo riêng `scratch/pixel-perfect-audit-<page-slug>.md`.
2. Gọi `get_figma_data` (`depth: 2` -> đi sâu từng `nodeId` section) trên đúng Frame Figma của Page đó.

#### Bước 2 — Trình Bày Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` Của Page Đó & Dừng Chờ Confirm
Hiển thị rõ ranh giới file `[ALLOWED TO EDIT IN THIS TAB]` kèm 3 bảng quyết định của riêng Page đó:
- **`[+] THÊM GÌ (ADD)`**: Khóa kích thước khung/thẻ (`width`, `height`, `aspect-square`), công thức `Section_Padding_Top = Figma_Y - Header_Height`, typography, padding/gap chuẩn Figma.
- **`[-] BỎ GÌ (REMOVE)`**: Các section/khối tự chế không có trong Frame Figma của Page đó, class `overflow-x-hidden` trên thẻ root của Page.
- **`[=] GIỮ LẠI GÌ (KEEP)`**: Các section và component con của Page đó đã khớp 100% thông số Figma.

> **🛑 CHỐT CHẶN BẮT BUỘC TẠI BƯỚC 2**:
> Dừng lại chờ người dùng **Confirm** danh sách `[+] Thêm / [-] Bỏ / [=] Giữ` của Page đó trước khi sửa file.

#### Bước 3 & 4 — Thực Thi Sửa Trong Phạm Vi Cô Lập & Nghiệm Thu
Ngay sau khi người dùng **Confirm**:
1. Chỉ sửa các file thuộc **`[ALLOWED TO EDIT IN THIS TAB]`** của Page đó.
2. Chạy lại `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . --page <Ten_Page>` -> Xác nhận `0` lỗi (`[+] ADD: 0 | [-] REMOVE: 0`).
3. Kiểm tra `yarn frontend:build` và Playwright MCP (`getBoundingClientRect` & `getComputedStyle`) trên Desktop (`1440×900`) & Mobile (`390×844`).

---

### 📋 Bảng Lệnh Copy-Paste Để Mở Song Song Cùng Lúc Tất Cả Các Tab
Bạn có thể mở cùng lúc các đoạn hội thoại và dán từng lệnh tương ứng dưới đây:

| Tab Hội Thoại | Lệnh gõ vào khung Chat (Chạy song song an toàn 100%) | File báo cáo riêng tự sinh | Phạm vi file được sửa (`Isolated Scope`) |
| :--- | :--- | :--- | :--- |
| **Tab 0 (Nền tảng chung - chạy trước nếu cần)** | `/opsx-pixel-perfect --page Layout <link Figma Header/Footer>` | `scratch/pixel-perfect-audit-layout.md` | `tailwind.config.js`, `app.scss`, `Layouts/*`, `Header.vue`, `Footer.vue`, SVG, Favicon |
| **Tab 1 (Trang chủ)** | `/opsx-pixel-perfect --page Home <link Figma Home>` | `scratch/pixel-perfect-audit-home.md` | `Pages/Home.vue`, `Components/Home/*.vue` |
| **Tab 2 (Về chúng tôi)** | `/opsx-pixel-perfect --page About <link Figma About>` | `scratch/pixel-perfect-audit-about.md` | `Pages/About.vue`, `Components/About/*.vue` |
| **Tab 3 (Danh sách SP - PLP)** | `/opsx-pixel-perfect --page Products/Index <link Figma PLP>` | `scratch/pixel-perfect-audit-products-index.md` | `Pages/Products/Index.vue`, `Components/Products/*.vue` |
| **Tab 4 (Chi tiết SP - PDP)** | `/opsx-pixel-perfect --page Products/Show <link Figma PDP>` | `scratch/pixel-perfect-audit-products-show.md` | `Pages/Products/Show.vue`, `Components/ProductDetail/*.vue` |
| **Tab 5 (Dịch vụ)** | `/opsx-pixel-perfect --page Services <link Figma Services>` | `scratch/pixel-perfect-audit-services.md` | `Pages/Services/*.vue`, `Components/Services/*.vue` |
| **Tab 6 (Dự án)** | `/opsx-pixel-perfect --page Projects <link Figma Projects>` | `scratch/pixel-perfect-audit-projects.md` | `Pages/Projects/*.vue`, `Components/Projects/*.vue` |
| **Tab 7 (Bài viết / Tin tức)** | `/opsx-pixel-perfect --page Posts <link Figma Posts>` | `scratch/pixel-perfect-audit-posts.md` | `Pages/Posts/*.vue`, `Components/Posts/*.vue` |
| **Tab 8 (Liên hệ)** | `/opsx-pixel-perfect --page Contact <link Figma Contact>` | `scratch/pixel-perfect-audit-contact.md` | `Pages/Contact.vue`, `Components/Contact/*.vue` |

---

## 2. Chế Độ Hub Tổng Điều Phối Toàn Dự Án (Khi Gọi `/opsx-pixel-perfect` Không Kèm `--page`)

Khi người dùng gõ `/opsx-pixel-perfect` không truyền tên Page:
1. **Bước 1**: Chạy `python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py .` (xuất báo cáo tổng `scratch/pixel-perfect-audit-report.md`).
2. **Bước 2**: Xuất bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` toàn dự án và **Dừng chờ người dùng Confirm**.
3. **Bước 3**: Sinh bộ **Parallel Handoff Prompts** theo bảng 8 Tab phía trên để người dùng mở chạy song song cùng lúc.
4. **Bước 4**: Nghiệm thu tổng thể bằng `scan-pixel-perfect-traps.py .` + `yarn frontend:build`.

---

## 3. Bộ Công Cụ Đi Kèm (`scripts/` & `references/`)

- **Tài liệu 7 Quy Tắc Vàng**: [references/pixel-perfect-7-golden-rules.md](./references/pixel-perfect-7-golden-rules.md)
- **Script quét tự động Read-Only & Parallel-Safe**:
  ```bash
  python3 .agents/skills/opsx-pixel-perfect-ui/scripts/scan-pixel-perfect-traps.py . [--page Home|About|Products/Index|Products/Show|Layout]
  ```
- **Script Bootstrap & Xuất sang dự án khác (100% không dùng `rm`)**:
  ```bash
  ./.agents/skills/opsx-pixel-perfect-ui/scripts/bootstrap-pixel-perfect.sh [--export-to /duong/dan/du-an] [--scan]
  ```
- **Script cài đặt Global**:
  ```bash
  ./.agents/skills/opsx-pixel-perfect-ui/scripts/install-global.sh
  ```
