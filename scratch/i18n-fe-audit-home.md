# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma — Page `Home` (`/opsx-i18n-fe`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Thời gian quét**: `2026-11-04 22:33:51`
- **Phạm vi kiểm toán**: `Page `Home`` (`11` files `.vue` thuộc Figma, đã tự động bỏ qua `208` files boilerplate không dùng tới)
- **Ranh giới chạy song song**: `[ALLOWED: resources/Frontend/js/**, resources/Frontend/lang/**]` | `[LOCKED: app/**, config/**, resources/Backend/**]`
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<template>`**: `0` vị trí
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<script>`**: `0` vị trí
- **Độ phủ từ điển `en.json` cho các khóa `tt(...)` đang dùng**: `0/83` khóa (`83` khóa chưa có trong `en.json`)

---

## 1. Trạng Thái Từ Điển `vi.json` / `en.json` & Nút Chuyển Ngôn Ngữ `VI | EN` Trên Header

| Hạng mục | Tọa độ / File | Hiện trạng thực tế | Trạng thái |
| :--- | :--- | :--- | :--- |
| **Từ điển Tiếng Việt (`vi.json`)** | `resources/Frontend/lang/vi.json` | `0` khóa | ⚠️ Chưa tạo file |
| **Từ điển Tiếng Anh (`en.json`)** | `resources/Frontend/lang/en.json` | `0` khóa (`0/83` khóa đang dùng đã dịch) | ⚠️ Thiếu 83 khóa đang dùng |
| **Nút chuyển đổi `VI \| EN` trên Header** | `resources/Frontend/js/Components/Header.vue` | Đã phát hiện logic chuyển ngôn ngữ trên Header | ✅ Đã có |

---

## 2. Thống Kê Chuỗi Tiếng Việt Fix Cứng Theo Từng File Thuộc Thiết Kế Figma

| File `.vue` (Trong phạm vi Figma) | Số chuỗi Tiếng Việt cứng ở `<template>` | Số chuỗi Tiếng Việt cứng ở `<script>` | Trạng thái |
| :--- | :--- | :--- | :--- |
| `resources/Frontend/js/Pages/Home/Components/AnnouncementTicker.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/HighlightMetrics.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/HomeFooter.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Home/Index.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |

---

## 3. Chi Tiết Tọa Độ `File:Dòng` Các Chuỗi Tiếng Việt Còn Fix Cứng

> ✅ **100% SẠCH HARDCODE TIẾNG VIỆT**: Không phát hiện chuỗi Tiếng Việt fix cứng nào chưa bọc `tt(...)` trong phạm vi này!

---

## 4. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)

- **`[+] ĐỀ XUẤT THÊM (ADD)`**: Bọc `tt('...')` cho `0` chuỗi trong `<template>` + `0` chuỗi trong `<script>`, và bổ sung `83` khóa còn thiếu vào `resources/Frontend/lang/en.json`.
- **`[-] ĐỀ XUẤT BỎ QUA (SKIP BOILERPLATE)`**: Bỏ qua `208` files `.vue` boilerplate không nằm trong thiết kế Figma (`Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`...) để tiết kiệm thời gian và chống tràn bộ nhớ.
- **`[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`**: Giữ nguyên 100% cấu trúc DOM, class TailwindCSS, hiệu ứng Animation và các dữ liệu động đang nhận từ `$page.props` / CMS.

---

## 5. Bộ Handoff Prompts Đa Ngôn Ngữ Frontend — Phạm Vi: `Home` (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)

> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: app/**, config/**, resources/Backend/**]`) để chạy song song 100% với `/opsx-i18n-cms` mà không đụng file.

### 🌐 GÓI 1 (FE i18n — `Home`): Bọc `tt('...')` Cho Toàn Bộ Chuỗi Tiếng Việt Fix Cứng & Bổ Sung Từ Điển `en.json`

```markdown
Bạn đang thực thi **GÓI FE i18n (Home): Đa Ngôn Ngữ Giao Diện Frontend Theo Phạm Vi Figma** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-fe`.
Hãy đọc `.agents/skills/opsx-i18n-fe/SKILL.md` trước khi thực hiện.

### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)
- ✅ `[ALLOWED TO EDIT]` (Chỉ được sửa các file thuộc phạm vi này):
  - `resources/Frontend/js/Pages/Home/Components/AnnouncementTicker.vue`
  - `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`
  - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`
  - `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/HighlightMetrics.vue`
  - `resources/Frontend/js/Pages/Home/Components/HomeFooter.vue`
  - `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`
  - `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`
  - `resources/Frontend/js/Pages/Home/Index.vue`
  - `resources/Frontend/lang/vi.json`
  - `resources/Frontend/lang/en.json`
- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `app/**`, `config/**`, `database/**`, `resources/Backend/**` (đang được xử lý song song bởi `/opsx-i18n-cms`).
- Bắt buộc gọi `invoke_subagent` (`Role: "Frontend Worker"`) để thực hiện. Cấm dùng `rm` xóa file.

### 2. Danh Sách Các Chuỗi Tiếng Việt Còn Fix Cứng Cần Bọc `tt('...')` & Dịch Sang `en.json`:
- Toàn bộ các chuỗi hiển thị trong `<template>` và `<script>` của phạm vi này đã được bọc `tt('...')` 100%.

### 3. Bổ Sung Bản Dịch Tiếng Anh Còn Thiếu Trong `resources/Frontend/lang/en.json` (83 khóa):
- Các khóa `tt(...)` đang gọi trên giao diện nhưng chưa có trong `resources/Frontend/lang/en.json`: `"Best choice"`, `"Bàn giao tận tay khách hàng qua mạng lưới vận chuyển hỏa tốc toàn quốc, kèm chính sách bảo hành và hướng dẫn sử dụng chu đáo."`, `"BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER"`, `"Bạn cần tư vấn về in 3D? Hãy chia sẻ nhu cầu & ý tưởng của bạn với HH MAKER qua các kênh bên dưới"`, `"CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER"`, `"Chia sẻ ý tưởng của bạn đến với HH Maker"`, `"Chính sách bảo hành & Đổi trả"`, `"Chính sách bảo mật"`, `"Chính sách bảo mật dữ liệu file"`, `"Decor văn phòng"`, `"Dịch vụ"`, `"Dịch vụ quan tâm"`, `"FROM IDEAS TO REALITY - FROM IDEAS TO REALITY -"`, `"Gia công 3D"`, `"Gia công In 3D"`...
- Hãy bổ sung đầy đủ 100% cặp key-value Tiếng Việt vào `resources/Frontend/lang/vi.json` và bản dịch Tiếng Anh chuẩn chuyên ngành vào `resources/Frontend/lang/en.json`.

### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy lại `python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page Home` để xác nhận số chuỗi Tiếng Việt fix cứng trong `<template>` giảm về `0` và `0` khóa thiếu trong `en.json`.
- Chạy `yarn frontend:build` thành công (`exit code 0`).
```
