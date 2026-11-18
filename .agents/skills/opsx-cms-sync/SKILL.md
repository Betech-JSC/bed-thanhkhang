---
name: opsx-cms-sync
description: Hub điều phối Hệ thống CMS Dynamic A–Z, Kiểm toán Độ Phủ Kết Nối UI-to-CMS & Kiểm Toán Toàn Bộ Điều Hướng theo mô hình Đoạn Hội Thoại Trung Tâm (4-Step Hub-and-Spoke Orchestration). Tự động (1) Khám phá toàn bộ các hệ thống đã được tạo bên CMS (Models, Fields, Admin Controllers), (2) Quét toàn bộ UI liệt kê những chỗ chưa connect với CMS và ánh xạ vào đúng Model CMS sẵn có, (3) Kiểm toán 100% điều hướng (Link chết href="#", lệch tên route(), Card thiếu slug, Button thiếu @click), (4) Lập Bảng Quyết Định [+] Thêm / [-] Bỏ / [=] Giữ chờ Confirm và tự động sinh Bộ Handoff Prompts dựa trên hệ thống CMS đã có để đem đi các đoạn hội thoại khác fix.
---

# OPSX CMS Sync — Đoạn Hội Thoại Trung Tâm Kiểm Toán Kết Nối UI-to-CMS, Điều Hướng & Sinh Handoff Prompts (`Hub-and-Spoke`)

Skill này biến đoạn hội thoại hiện tại thành **Đoạn Hội Thoại Trung Tâm (Hub Conversation)** chuyên thực hiện 4 nhiệm vụ cốt lõi:
1. **Khám phá toàn bộ Hệ thống đã có bên CMS (`Existing CMS Systems Inventory`)**: Tự động quét `app/Models/**`, `app/Http/Controllers/Backend/**`, `resources/Backend/js/Pages/**` để lập bản đồ các hệ thống CMS đã được tạo sẵn (`Product`, `ProductCategory`, `Service`, `Project`, `Post`, `PostCategory`, `Slider`, `History`, `Policy`, `Agency`, `Contact`, `Config`...).
2. **Kiểm tra toàn bộ UI đã Integrate với CMS hết chưa (`Unconnected UI -> Existing CMS Mapping`)**: Tự động phát hiện mọi Frontend Controller render tĩnh, mọi mảng dữ liệu cứng (`const ... = [...]`) trên các trang/component Vue, mọi SĐT/Email/Bản đồ/MXH hardcode, và mọi `<form>` chưa nối Backend -> **Tự động ánh xạ từng chỗ chưa connect vào đúng Hệ thống đã có bên CMS**.
3. **Kiểm toán toàn bộ Điều Hướng trên mọi trang UI (`Full Navigation, Route, Link & CTA Audit`)**: Đối chiếu `routes/frontend.php` & `routes/web.php` với toàn bộ `<Link>`, `<a>`, `route('...')`, `v-for` Card và `<button>` trên UI để bắt trọn mọi link chết (`href="#"`), lệch tên route, Card danh sách thiếu `<Link>`/`slug`, và nút CTA thiếu `@click`.
4. **Đóng vai trò Đoạn Hội Thoại Trung Gian sinh Handoff Prompts dựa trên Hệ thống CMS đã có**: Trình bày kế hoạch `[+] Thêm kết nối & điều hướng / [-] Bỏ dữ liệu tĩnh / [=] Giữ nguyên hệ thống CMS` chờ người dùng Confirm -> Xuất sẵn các khối **Handoff Prompts** chỉ rõ lấy từ Model CMS nào, cột nào, nối vào `Frontend/*Controller.php` nào và bind vào `Pages/*.vue` nào để đem sang các đoạn hội thoại khác fix.

---

## 1. Nguyên Tắc An Toàn Tuyệt Đối (`STRICT SAFE GUARD`)

1. **CẤM TỰ Ý XÓA FILE VẬT LÝ**: Tuyệt đối không dùng `rm`, `rm -rf` hay xóa bất kỳ file nào. Trong mọi script `.sh` đi kèm cũng tuyệt đối không có lệnh `rm`.
2. **CẤM PHÁ HỦY DATABASE**: Không chạy `php artisan migrate:fresh`, `db:seed` ghi đè dữ liệu thật, `DROP TABLE` hay `TRUNCATE`. Mọi seeder phải viết theo chuẩn **Idempotent (`updateOrCreate` / `firstOrCreate`)**.
3. **NGHIÊM CẤM TỰ Ý SỬA CODE KHI CHƯA CONFIRM**: Ở Giai đoạn 1 và Giai đoạn 2, Đoạn Hội Thoại Trung Tâm hoạt động ở chế độ **100% `Strict Read-Only`** đối với mã nguồn dự án.
4. **CHÍNH SÁCH ZERO-FALLBACK & ZERO-DEFAULT TUYỆT ĐỐI**:
   - **Backend**: Cấm các hàm `getFallback*()`, `getDefault*()` tự sinh data giả. Cấm toán tử fallback text `?? 'chuỗi mẫu'` trong Controller Inertia render (nếu thiếu data trả về `null` hoặc rỗng `[]` chuẩn). Cấm đệm mảng vòng lặp (padding/slice) để ép đủ số card.
   - **Frontend**: Cấm `props.items || defaultMock`, cấm default prop dạng mảng mock. Khi DB trống, bắt buộc hiển thị `EmptyState.vue` hoặc ẩn section tùy chọn; tuyệt đối không hiển thị dữ liệu giả che giấu CMS.

---

## 2. Quy Trình 4 Giai Đoạn Của Đoạn Hội Thoại Trung Tâm (`4-Step Hub Workflow`)

Khi người dùng gõ `/opsx-cms-sync` (trên Antigravity) hoặc `/opsx:cms-sync` (trên Claude Code), Agent thực hiện tuần tự 4 giai đoạn sau:

### 🔍 Giai Đoạn 1 — Chạy Kiểm Toán Tự Động 4 Module (`Strict Read-Only`)

1. **Chạy công cụ kiểm toán toàn diện `audit-cms-sync.py`**:
   ```bash
   python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .
   ```
2. **Đọc báo cáo `scratch/cms-sync-audit-report.md`** vừa được sinh ra (chứa đầy đủ kết quả của 4 Module):
   - **Module 1 — Bản Đồ Hệ Thống Đã Có Bên CMS (`Existing CMS Systems Inventory`)**: Liệt kê toàn bộ các Model trong `app/Models/`, danh sách trường `$fillable` & `$translatedAttributes`, Admin Controller tương ứng và trạng thái đã được gọi ở Frontend Controller hay chưa.
   - **Module 2 — Danh Sách Các Chỗ Trên UI Chưa Connect Với CMS (`Unconnected UI -> Existing CMS Mapping`)**: Liệt kê tọa độ `File:Dòng` của mọi Controller tĩnh, mảng dữ liệu tĩnh trong `<script>`, SĐT/Email/Bản đồ/MXH gắn cứng trong `<template>`, và `<form>` chưa gọi `ContactController`, kèm theo **tên Model & Admin Controller có sẵn bên CMS cần kết nối vào**.
   - **Module 3 — Danh Sách Toàn Bộ Lỗi Điều Hướng (`Full Navigation, Route, Link & CTA Audit`)**: Liệt kê tọa độ `File:Dòng` của mọi link chết (`href="#"`, `href=""`), menu tĩnh `href: '#'`, lệnh gọi `route('...')` không khớp với `routes/frontend.php`, Card danh sách (`v-for`) chưa bọc `<Link>` truyền `slug` chi tiết, và nút `<button>` mồ côi thiếu `@click`.
   - **Module 4 — Trạng Thái 8 Trụ Cột CMS (`8-Pillars Health Check`)**: Kiểm tra Dashboard `Schema::hasTable`, `FileManager.vue` + `Thumbnail.vue`, Schema JSON Product, `Contact::normalizeLeadData()`, `Config` + `HandleInertiaFrontendRequests`, CI/CD safety, đối chiếu khóa `Cache::remember` <-> `Cache::forget`, và PHPUnit E2E Tests.

---

### 📋 Giai Đoạn 2 — Trình Bày 4 Bảng Kiểm Toán + Kế Hoạch `[+] THÊM / [-] BỎ / [=] GIỮ` & Dừng Chờ Confirm

Từ kết quả ở `scratch/cms-sync-audit-report.md`, Agent **BẮT BUỘC** trình bày rõ ràng cho người dùng:

1. **Bảng 1 — Bản Đồ Các Hệ Thống Đã Tạo Sẵn Bên CMS**: Cho người dùng thấy rõ bên CMS hiện đã có những Model nào, trường dữ liệu nào và cái nào chưa được nối ra Frontend.
2. **Bảng 2 — Danh Sách Các Vị Trí Trên UI CHƯA CONNECT Với CMS** (kèm tọa độ `File:Dòng` và ánh xạ vào đúng Hệ thống CMS sẵn có).
3. **Bảng 3 — Danh Sách Toàn Bộ Lỗi ĐIỀU HƯỚNG Trên UI** (`DEAD_LINK`, `DEAD_MENU_ITEM`, `ROUTE_MISMATCH`, `UNLINKED_CMS_CARD`, `UNWIRED_BUTTON` kèm gợi ý route đích trong `routes/frontend.php`).
4. **Bảng 4 — Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ NGUYÊN GÌ`**:
   - **`[+] THÊM (ADD)`**: Các kết nối từ UI vào Model CMS sẵn có, các điều hướng `<Link :href="route(...)">` cần gắn, các khóa `Cache::forget` còn thiếu.
   - **`[-] BỎ (REMOVE)`**: Các mảng dữ liệu tĩnh (`const ... = [...]`) trên Vue đang chặn dữ liệu CMS, các hàm mock/fallback (`getFallback*()`, `getDefault*()`), các toán tử fallback string `?? 'chuỗi mẫu'` trong Controller để trả về `null`/`[]` chuẩn, các link chết `href="#"`, các trường thừa trên Form Admin.
   - **`[=] GIỮ NGUYÊN (KEEP)`**: Các hệ thống Model/Migration/Admin Controller đã có sẵn bên CMS và dữ liệu thật trong Database.

> 🛑 **ĐIỂM DỪNG BẮT BUỘC (MANDATORY CONFIRMATION GATE)**:
> Sau khi trình bày các bảng trên, Agent **BẮT BUỘC DỪNG LẠI** và hỏi người dùng:
> *"Mời bạn xem qua danh sách các chỗ UI chưa connect với CMS, danh sách lỗi điều hướng và Bảng Quyết Định `[+] Thêm / [-] Bỏ / [=] Giữ` ở trên. Bạn Confirm để mình xuất Bộ Handoff Prompts (bám sát vào các hệ thống đã có bên CMS) đem sang các đoạn hội thoại khác tiến hành fix nhé?"*

---

### 🚀 Giai Đoạn 3 — Xuất Bộ "Handoff Prompts" Dựa Trên Hệ Thống CMS Đã Có (`CMS-Driven Spoke Prompts`)

Ngay sau khi người dùng **Confirm** (hoặc báo cáo đã sinh sẵn trong Mục 6 của `scratch/cms-sync-audit-report.md`), Đoạn Hội Thoại Trung Tâm xuất các khối **Handoff Prompts** tự chứa (Self-Contained) chia theo gói để người dùng **Copy-Paste sang các đoạn hội thoại khác tiến hành fix**:

- **📦 GÓI 1 — Đấu Nối Toàn Bộ UI Chưa Connect Vào Các Hệ Thống Đã Có Bên CMS (`UI -> Existing CMS Integration`)**:
  - Đính kèm **Bản đồ các Model & trường `$fillable`/`$translatedAttributes` đã có sẵn bên CMS** để hội thoại con sử dụng đúng 100% hệ thống sẵn có (không tự bịa bảng hay tạo Model trùng lặp).
  - Chỉ rõ từng tọa độ `File:Dòng` `[UI-01]`, `[UI-02]`... cần sửa ở `Frontend/*Controller.php` nào và file `.vue` nào.
- **🧭 GÓI 2 — Đấu Nối Toàn Bộ Điều Hướng, Xóa Link Chết (`href="#"`), Sửa Lệch Route & Nút CTA (`Full Navigation Fix`)**:
  - Chỉ rõ từng tọa độ `File:Dòng` `[NAV-01]`, `[NAV-02]`... trên Header, Footer, Home, Products, Services, Posts, Projects, Contact, Policies.
  - Chỉ rõ tên route đích hợp lệ trong `routes/frontend.php` và tham số `{ slug: item.slug }` lấy từ Model CMS.
- **⚙️ GÓI 3 — Đồng Bộ 8 Trụ Cột CMS, Hợp Đồng Khóa Cache (`Cache::remember` <-> `Cache::forget`) & PHPUnit E2E Tests**:
  - Đảm bảo khi Admin sửa nội dung trong CMS, ngoài giao diện cập nhật tức thì (0 giây độ trễ) và có bài test PHPUnit chứng minh.

---

### ✅ Giai Đoạn 4 — Quay Về Đoạn Hội Thoại Trung Tâm Để Nghiệm Thu (`Hub Gate Verification`)

Khi người dùng đem các gói Prompt đi fix ở các đoạn hội thoại con xong và quay lại Đoạn Hội Thoại Trung Tâm nhắn:
> *"Nghiệm thu CMS Sync"* hoặc *"Kiểm tra lại kết nối CMS và điều hướng"*

Đoạn Hội Thoại Trung Tâm thực hiện:
1. Gọi `invoke_subagent` (`Role: "QA Verifier"` hoặc `"Independent Tester"`) chạy lại:
   ```bash
   python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .
   ```
2. Kiểm tra các chỉ số nghiệm thu cứng:
   - **Số vị trí UI chưa connect CMS**: `0` vị trí.
   - **Số lỗi Điều hướng (Dead Links / Route Mismatch / Unwired Buttons)**: `0` vị trí.
   - **Trạng thái 8 Trụ cột CMS**: `8/8 PASS`.
   - **Build & Test**: `yarn frontend:build` (`exit code 0`) và `php artisan test` (`PASS`).

---

## 3. Hướng Dẫn Sử Dụng Bộ Script Của Skill (`scripts/`)

```bash
# 1. Chạy kiểm toán toàn diện tự động phát hiện các trang Figma Active & dựng đồ thị AST (Route -> Controller -> Page -> Model)
python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .

# 2. Chỉ định rõ danh sách các trang có trong thiết kế Figma (bỏ qua 100% trang thừa boilerplate)
python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py . --pages Home,About,Products,Services,Process,Posts,Contact

# 3. Kiểm toán cô lập cho 1 trang cụ thể
python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py . --page Home

# 4. Đồng bộ skill tại chỗ hoặc xuất sang dự án khác
./.agents/skills/opsx-cms-sync/scripts/bootstrap-cms-sync.sh --export-to /duong/dan/du-an-moi
```
