# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma — Page `Products` (`/opsx-i18n-fe`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Thời gian quét**: `2026-11-04 22:34:21`
- **Phạm vi kiểm toán**: `Page `Products`` (`12` files `.vue` thuộc Figma, đã tự động bỏ qua `207` files boilerplate không dùng tới)
- **Ranh giới chạy song song**: `[ALLOWED: resources/Frontend/js/**, resources/Frontend/lang/**]` | `[LOCKED: app/**, config/**, resources/Backend/**]`
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<template>`**: `0` vị trí
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<script>`**: `0` vị trí
- **Độ phủ từ điển `en.json` cho các khóa `tt(...)` đang dùng**: `0/151` khóa (`151` khóa chưa có trong `en.json`)

---

## 1. Trạng Thái Từ Điển `vi.json` / `en.json` & Nút Chuyển Ngôn Ngữ `VI | EN` Trên Header

| Hạng mục | Tọa độ / File | Hiện trạng thực tế | Trạng thái |
| :--- | :--- | :--- | :--- |
| **Từ điển Tiếng Việt (`vi.json`)** | `resources/Frontend/lang/vi.json` | `0` khóa | ⚠️ Chưa tạo file |
| **Từ điển Tiếng Anh (`en.json`)** | `resources/Frontend/lang/en.json` | `0` khóa (`0/151` khóa đang dùng đã dịch) | ⚠️ Thiếu 151 khóa đang dùng |
| **Nút chuyển đổi `VI \| EN` trên Header** | `resources/Frontend/js/Components/Header.vue` | Đã phát hiện logic chuyển ngôn ngữ trên Header | ✅ Đã có |

---

## 2. Thống Kê Chuỗi Tiếng Việt Fix Cứng Theo Từng File Thuộc Thiết Kế Figma

| File `.vue` (Trong phạm vi Figma) | Số chuỗi Tiếng Việt cứng ở `<template>` | Số chuỗi Tiếng Việt cứng ở `<script>` | Trạng thái |
| :--- | :--- | :--- | :--- |
| `resources/Frontend/js/Components/Product/ProductGallery.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/Product/RelatedProducts.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/Product/SpecTable.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/Product/VariantSelector.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Index.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Product/Show.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Products/Index.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Products/Show.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |

---

## 3. Chi Tiết Tọa Độ `File:Dòng` Các Chuỗi Tiếng Việt Còn Fix Cứng

> ✅ **100% SẠCH HARDCODE TIẾNG VIỆT**: Không phát hiện chuỗi Tiếng Việt fix cứng nào chưa bọc `tt(...)` trong phạm vi này!

---

## 4. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)

- **`[+] ĐỀ XUẤT THÊM (ADD)`**: Bọc `tt('...')` cho `0` chuỗi trong `<template>` + `0` chuỗi trong `<script>`, và bổ sung `151` khóa còn thiếu vào `resources/Frontend/lang/en.json`.
- **`[-] ĐỀ XUẤT BỎ QUA (SKIP BOILERPLATE)`**: Bỏ qua `207` files `.vue` boilerplate không nằm trong thiết kế Figma (`Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`...) để tiết kiệm thời gian và chống tràn bộ nhớ.
- **`[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`**: Giữ nguyên 100% cấu trúc DOM, class TailwindCSS, hiệu ứng Animation và các dữ liệu động đang nhận từ `$page.props` / CMS.

---

## 5. Bộ Handoff Prompts Đa Ngôn Ngữ Frontend — Phạm Vi: `Products` (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)

> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: app/**, config/**, resources/Backend/**]`) để chạy song song 100% với `/opsx-i18n-cms` mà không đụng file.

### 🌐 GÓI 1 (FE i18n — `Products`): Bọc `tt('...')` Cho Toàn Bộ Chuỗi Tiếng Việt Fix Cứng & Bổ Sung Từ Điển `en.json`

```markdown
Bạn đang thực thi **GÓI FE i18n (Products): Đa Ngôn Ngữ Giao Diện Frontend Theo Phạm Vi Figma** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-fe`.
Hãy đọc `.agents/skills/opsx-i18n-fe/SKILL.md` trước khi thực hiện.

### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)
- ✅ `[ALLOWED TO EDIT]` (Chỉ được sửa các file thuộc phạm vi này):
  - `resources/Frontend/js/Components/Product/ProductGallery.vue`
  - `resources/Frontend/js/Components/Product/RelatedProducts.vue`
  - `resources/Frontend/js/Components/Product/SpecTable.vue`
  - `resources/Frontend/js/Components/Product/VariantSelector.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductCard.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue`
  - `resources/Frontend/js/Pages/Product/Index.vue`
  - `resources/Frontend/js/Pages/Product/Show.vue`
  - `resources/Frontend/js/Pages/Products/Index.vue`
  - `resources/Frontend/js/Pages/Products/Show.vue`
  - `resources/Frontend/lang/vi.json`
  - `resources/Frontend/lang/en.json`
- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `app/**`, `config/**`, `database/**`, `resources/Backend/**` (đang được xử lý song song bởi `/opsx-i18n-cms`).
- Bắt buộc gọi `invoke_subagent` (`Role: "Frontend Worker"`) để thực hiện. Cấm dùng `rm` xóa file.

### 2. Danh Sách Các Chuỗi Tiếng Việt Còn Fix Cứng Cần Bọc `tt('...')` & Dịch Sang `en.json`:
- Toàn bộ các chuỗi hiển thị trong `<template>` và `<script>` của phạm vi này đã được bọc `tt('...')` 100%.

### 3. Bổ Sung Bản Dịch Tiếng Anh Còn Thiếu Trong `resources/Frontend/lang/en.json` (151 khóa):
- Các khóa `tt(...)` đang gọi trên giao diện nhưng chưa có trong `resources/Frontend/lang/en.json`: `"Best choice"`, `"Bước 01"`, `"Bước 02"`, `"Bước 03"`, `"Bạn có thể trao đổi với HH Maker để thay đổi màu sắc, điều chỉnh kích thước (tỉ lệ scale) hoặc đặt số lượng lớn làm quà tặng, decor sự kiện."`, `"BỘ SƯU TẬP HH MAKER"`, `"Bộ Cụm Bánh Răng Hành Tinh Planetary Gearbox"`, `"Bộ lọc sản phẩm"`, `"Cam"`, `"Chi tiết cánh quạt turbine khí động học có thể thổi quay mượt mà, sơn phủ titan ánh kim bắt mắt."`, `"Chi tiết cơ khí sắc sảo"`, `"Chi tiết lớp in"`, `"Chia sẻ màu sắc, kích thước và số lượng mong muốn. HH Maker sẽ tư vấn phương án và báo giá phù hợp."`, `"Chia sẻ ý tưởng, link mô hình 3D hoặc kích thước mong muốn cho HH Maker."`, `"Chiều cao lớp in"`...
- Hãy bổ sung đầy đủ 100% cặp key-value Tiếng Việt vào `resources/Frontend/lang/vi.json` và bản dịch Tiếng Anh chuẩn chuyên ngành vào `resources/Frontend/lang/en.json`.

### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy lại `python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page Products` để xác nhận số chuỗi Tiếng Việt fix cứng trong `<template>` giảm về `0` và `0` khóa thiếu trong `en.json`.
- Chạy `yarn frontend:build` thành công (`exit code 0`).
```
