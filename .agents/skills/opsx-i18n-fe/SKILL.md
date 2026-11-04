---
name: opsx-i18n-fe
description: Hub điều phối Đa Ngôn Ngữ Giao Diện Frontend (VI / EN) — CHỈ QUÉT CÁC TRANG CÓ TRONG DESIGN FIGMA theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke). Tự động lọc đúng các trang Active theo Figma (--pages / --page), quét 100% text Tiếng Việt fix cứng trong <template> & <script> chưa bọc tt(), kiểm tra từ điển vi.json/en.json, kiểm tra nút chuyển đổi ngôn ngữ VI | EN trên Header/Mobile Menu, và sinh Handoff Prompts chạy song song độc lập với /opsx-i18n-cms.
---

# OPSX i18n FE — Đoạn Hội Thoại Trung Tâm Điều Phối Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma (`VI / EN`)

Skill này biến đoạn hội thoại hiện tại thành **Đoạn Hội Thoại Trung Tâm (Hub Conversation)** chuyên kiểm toán, lập kế hoạch và sinh **Bộ Handoff Prompts** để triển khai **Đa Ngôn Ngữ Frontend (`VI / EN`)** cho các trang có trong thiết kế Figma.

Đặc biệt:
1. **Chạy song song 100% với `/opsx-i18n-cms`**: Trong khi một đoạn hội thoại chạy `/opsx-i18n-cms` xử lý Backend (`config/`, `app/`, `database/`, `resources/Backend/`), đoạn hội thoại này chạy `/opsx-i18n-fe` chỉ xử lý `resources/Frontend/js/**` và `resources/Frontend/lang/{vi,en}.json` — **0% xung đột file**.
2. **Hỗ trợ chạy song song từng trang (`--page Layout`, `--page Home`, `--page About`...) giống hệt `/opsx-pixel-perfect`**: Tự động xuất file báo cáo riêng `scratch/i18n-fe-audit-<slug>.md` và khóa ranh giới file cô lập cho từng trang.
3. **Bỏ qua 100% các trang Boilerplate thừa không có trong Figma**: Tự động loại bỏ `Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`, `NestedPosts`... và các component không được import bởi trang Figma để tránh tràn context và không tốn công dịch code thừa.

---

## 1. Khóa Ranh Giới File Khi Chạy Song Song (`Parallel File Isolation Boundary`)

- ✅ **`[ALLOWED TO EDIT]` (Được phép chỉnh sửa)**:
  - `resources/Frontend/js/{Pages,Components,Layouts}/**/*.vue` (CHỈ trong phạm vi các trang Figma Active được chỉ định)
  - `resources/Frontend/lang/{vi,en}.json` (hoặc `lang/{vi,en}.json`)
- 🔒 **`[LOCKED FOR PARALLEL SAFETY]` (CẤM CHỈNH SỬA — Thuộc quyền của `/opsx-i18n-cms`)**:
  - `config/**`
  - `app/**`
  - `database/**`
  - `resources/Backend/**`

---

## 2. 4 Hạng Mục Kiểm Toán Đa Ngôn Ngữ Frontend (`scan-i18n-fe.py`)

1. **Giới hạn phạm vi nghiêm ngặt theo Trang Figma (`Active Figma Scope Only`)**:
   - Hỗ trợ `--pages Home,About,Products,Services,Process,Posts,Contact` (toàn bộ các trang Figma) hoặc `--page Home` (cô lập 1 trang để chạy song song nhiều hội thoại).
   - Tự động truy vết cây `import ... from '@/Components/...'` để chỉ quét các Component thực sự được gắn lên các trang Figma.
2. **Quét Text Tiếng Việt Fix Cứng (`Hardcoded Vietnamese Strings`)**:
   - Phát hiện mọi chuỗi có dấu Tiếng Việt còn fix cứng trong `<template>` (text hiển thị giữa các thẻ HTML, các thuộc tính tĩnh `placeholder="..."`, `title="..."`, `aria-label="..."`, `alt="..."`) chưa bọc qua `tt('...')` / `$t('...')`.
   - Phát hiện mọi chuỗi Tiếng Việt fix cứng trong `<script>` / `<script setup>` (nhãn tab, nhãn nút bấm, breadcrumb, thông báo validate/toast, mảng tĩnh) chưa bọc `tt('...')`.
3. **Kiểm tra Từ Điển `vi.json` / `en.json` & Cơ chế Fallback `EN -> VI`**:
   - Đối chiếu toàn bộ các khóa `tt('...')` / `$t('...')` đang gọi trong các file `.vue` của trang Figma với `en.json`: liệt kê các khóa còn thiếu bản dịch Tiếng Anh trong `en.json`.
   - Kiểm tra hàm `tt()` đảm bảo nếu thiếu key trong `en.json` thì tự động trả về câu gốc Tiếng Việt (không bao giờ hiện key rỗng hay lỗi).
4. **Kiểm tra Nút Chuyển Đổi Ngôn Ngữ `VI | EN` Trên Header & Mobile Menu**:
   - Kiểm tra `Header.vue` (và `PreHeader.vue` / `Layout/Header.vue`) đã có nút chuyển đổi `VI | EN` trên cả Desktop lẫn Mobile Drawer chưa và đã gắn đúng link chuyển đổi ngôn ngữ chưa.

---

## 3. Quy Trình 4 Giai Đoạn Của Đoạn Hội Thoại Trung Tâm (`4-Step Hub Workflow`)

### 🔍 Giai Đoạn 1 — Chạy Kiểm Toán Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma (`Strict Read-Only`)
```bash
# Chế độ 1: Quét toàn bộ các trang Figma Active
python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --pages Home,About,Products,Services,Process,Posts,Contact

# Chế độ 2: Quét cô lập từng trang để mở nhiều đoạn hội thoại chạy song song cùng lúc
python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page Layout
python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page Home
python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page About
```
Đọc báo cáo `scratch/i18n-fe-audit-report.md` (hoặc `scratch/i18n-fe-audit-<slug>.md`).

### 📋 Giai Đoạn 2 — Trình Bày Kết Quả Kiểm Toán + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` & Dừng Chờ Confirm
Trình bày cho người dùng:
1. **Bảng 1**: Trạng thái Nút chuyển ngôn ngữ `VI | EN` trên Header & Bộ từ điển `vi.json` / `en.json` (số khóa đã dịch / số khóa còn thiếu trong `en.json`).
2. **Bảng 2**: Thống kê số lượng chuỗi Tiếng Việt còn fix cứng theo từng trang Figma (`Layout`, `Home`, `About`, `Products`, `Services`, `Process`, `Posts`, `Contact`) kèm tọa độ `File:Dòng`.
3. **Bảng 3**: Bảng Quyết Định **`[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ NGUYÊN GÌ`** -> **DỪNG LẠI CHỜ USER CONFIRM**.

### 🚀 Giai Đoạn 3 — Xuất Bộ "Handoff Prompts" Chia Theo Từng Trang Figma (`Spoke Prompts`)
Sau khi người dùng Confirm, xuất các khối **Handoff Prompts** (chia theo từng trang hoặc nhóm trang Figma, có khóa ranh giới file `[LOCKED: app/**, config/**, resources/Backend/**]`) để người dùng copy sang các đoạn hội thoại con bọc `tt('...')` và bổ sung bản dịch vào `en.json`.

### ✅ Giai Đoạn 4 — Quay Về Hub Nghiệm Thu E2E
Khi các đoạn hội thoại con hoàn tất, chạy lại:
```bash
python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --pages Home,About,Products,Services,Process,Posts,Contact
yarn frontend:build
```
Đảm bảo số chuỗi Tiếng Việt fix cứng trong `<template>` giảm về `0`, `100%` khóa `tt(...)` trên các trang Figma có đủ bản dịch trong `en.json`, và `yarn frontend:build` thành công (`exit code 0`).
