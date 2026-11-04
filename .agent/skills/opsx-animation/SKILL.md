---
name: opsx-animation
description: Đóng gói Bộ Skill Animation Chuẩn LUXI (4 Quy Tắc Vàng: Single-Animation-Per-Element, Zero White-Out / No Opacity Blank Flash, Directional Horizontal Slide translate-x cho mọi nút bấm qua lại < / >, và Giữ nguyên 100% ProductGallery có hàng thumbnail preview ở dưới). Vận hành theo mô hình Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke) 4 bước kèm bảng quyết định [+]/[-]/[=] chờ người dùng Confirm. Sử dụng khi người dùng gọi "/opsx-animation", "/opsx:animation" hoặc "/ui-skill".
---

# OPSX Animation — Hệ Thống Đóng Gói & Kiểm Toán Animation Chuẩn LUXI (`/opsx-animation` & `/ui-skill`)

Skill này đóng gói **4 Quy Tắc Vàng Bất Biến Về Animation** được đúc kết thực chiến từ dự án LUXI, đi kèm trình quét tự động `scan-animation-rules.py` và quy trình **Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke)** 4 bước.

---

## 1. 4 Quy Tắc Vàng Bất Biến Về Animation (4 Golden Animation Rules)

Chi tiết mẫu code chuẩn xem tại [references/animation-4-golden-rules.md](./references/animation-4-golden-rules.md):

| Quy tắc | Tên quy chuẩn | Nội dung bắt buộc tuân thủ 100% |
| :--- | :--- | :--- |
| **Quy tắc 1** | **`Single-Animation-Per-Element` (Mỗi phần tử chỉ 1 Animation)** | Mỗi phần tử DOM chỉ được gán **tối đa 1 hiệu ứng chuyển động chủ đạo**. Nghiêm cấm chồng chéo nhiều animation lên cùng 1 phần tử hoặc cha-con trực tiếp (ví dụ: Section đã có `v-scroll-reveal` trượt `translate-y` thì các Card/Button bên trong không gắn thêm entrance animation lặp lại mà chỉ dùng hover micro-interaction khi di chuột; không lồng `<TransitionGroup>` bên trong `<Transition>`). Cấm dùng `transition-all` bừa bãi gây giật layout. |
| **Quy tắc 2** | **`Zero White-Out / No Opacity Blank Flash` (Cấm phủ trắng / nháy tàng hình)** | Tuyệt đối cấm hiệu ứng `opacity-0 -> opacity-100` trên các Section lớn hoặc lớp phủ trắng đè lên màn hình khi tải trang, chuyển trang hoặc cuộn trang. Composable `useScrollReveal` (`v-scroll-reveal`) bắt buộc giữ `opacity: 1` xuyên suốt và chỉ dùng `transition-transform duration-500 ease-out` trượt nhẹ `translate-y-4 -> translate-y-0`. |
| **Quy tắc 3** | **`Directional Horizontal Slide (translate-x) for Prev/Next Buttons`** | Mọi khu vực có **nút bấm qua lại (`<` / `>` hoặc Dots chuyển slide/trang)** như: Showcase sản phẩm (`HomeProductShowcaseSection.vue`), Góc nhìn/Video carousel (`HomePerspectiveSection.vue`), Hero banner slider (`HomeHeroSection.vue`), Sản phẩm liên quan (`RelatedProductsSection.vue`), hoặc lưới phân trang sản phẩm (`ProductGrid.vue`) **BẮT BUỘC phải dùng Animation Trượt Ngang (`translate-x`) theo hướng bấm** (`next`: trượt từ phải sang trái `translate-x-6 -> translate-x-0`, `prev`: trượt từ trái sang phải `-translate-x-6 -> translate-x-0`). **TUYỆT ĐỐI KHÔNG dùng `opacity-0` fade** khi bấm nút qua lại. |
| **Quy tắc 4** | **`Preserve Product Gallery with Bottom Thumbnail Previews (KEEP AS-IS)`** | Phần chi tiết sản phẩm có nhiều cái preview/thumbnail nhỏ ở dưới (`ProductGallery.vue` với khung ảnh chính `688×688px` + hàng 6 ảnh preview `104×104px` ở dưới, cùng bảng chọn mẫu vải/phụ kiện trong `ProductInfoPanel.vue`) được **GIỮ NGUYÊN 100% (`[=] KEEP AS-IS`)** như trong source hiện có. Tuyệt đối không tự ý thay đổi cấu trúc hay hiệu ứng của phần này. |

---

## 2. Quy Trình 4 Bước Tại Đoạn Hội Thoại Trung Tâm (Hub-and-Spoke Workflow)

### Bước 1 — Quét Kiểm Toán Read-Only (`scan-animation-rules.py`)
Khi người dùng gọi `/opsx-animation`, `/opsx:animation` hoặc `/ui-skill`:
1. Chạy script kiểm toán tự động **Read-Only**:
   ```bash
   python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .
   ```
2. Đọc báo cáo tại `scratch/animation-rules-audit-report.md`.
3. **Tuyệt đối không sửa hay xóa file ở Bước 1**.

---

### Bước 2 — Trình Bày Bảng Quyết Định `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` & Dừng Chờ Người Dùng Confirm

Trình bày rõ ràng 3 bảng quyết định ngay trên đoạn hội thoại trung tâm:

1. **Bảng `[+] THÊM GÌ (ADD)`**:
   - Những slider/carousel có nút bấm `<` / `>` cần bổ sung biến hướng trượt `slideDirection = ref('next')` và hiệu ứng trượt ngang `transition-transform duration-300 ease-out` (`translate-x-6` / `-translate-x-6` -> `translate-x-0`).
   - Những section/card/button tĩnh hoàn toàn cần bổ sung scroll-reveal (`v-scroll-reveal` chỉ dùng `translate-y-4 -> translate-y-0`) hoặc micro-interaction (`hover:scale-[1.03]`, `active:scale-95`).
2. **Bảng `[-] BỎ GÌ (REMOVE)`**:
   - Gỡ bỏ `opacity-0` trong `<Transition>` của các slider có nút bấm qua lại `<` / `>`.
   - Gỡ bỏ `opacity-0` trong scroll-reveal hoặc các lớp phủ làm trắng màn hình khi tải/cuộn trang.
   - Gỡ bỏ các animation bị gán chồng chéo (`<Transition>` lồng `<TransitionGroup>`, hoặc gán đồng thời `transition-transform` và `transition-all` trên cùng 1 phần tử).
3. **Bảng `[=] GIỮ LẠI GÌ (KEEP AS-IS)`**:
   - **Giữ nguyên 100% `ProductGallery.vue`** (khung ảnh chi tiết sản phẩm `688×688px` có hàng 6 ảnh preview `104×104px` ở dưới) và `ProductInfoPanel.vue`.
   - Giữ nguyên `useScrollReveal.js` (`translate-y-4 -> translate-y-0` không `opacity-0`) và các component đã đạt chuẩn.

> **🛑 CHỐT CHẶN BẮT BUỘC TẠI BƯỚC 2**:
> Sau khi hiển thị 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ`, Agent **BẮT BUỘC DỪNG LẠI** hỏi người dùng:
> *"Bạn hãy xem qua bảng `[+] Thêm / [-] Bỏ / [=] Giữ lại` phía trên (đặc biệt phần `ProductGallery.vue` có hàng preview nhỏ ở dưới được giữ nguyên 100%). Bạn Confirm để mình xuất bộ Handoff Prompts chia sang các đoạn hội thoại con thực hiện nhé?"*

---

### Bước 3 — Sinh Bộ "Handoff Prompts" Đem Đi Các Đoạn Hội Thoại Khác (Worker Conversations)
Sau khi người dùng **Confirm**, Agent sinh các gói **Handoff Prompts** theo từng cụm file (ví dụ: *Gói 1: Home Sliders (`HomeHeroSection`, `HomeProductShowcaseSection`, `HomePerspectiveSection`)*, *Gói 2: Catalog & Related Sliders (`ProductGrid`, `RelatedProductsSection`)*) kèm mẫu code `<Transition>` trượt ngang chuẩn để người dùng copy sang đoạn hội thoại khác thực thi.

---

### Bước 4 — Quay Về Đoạn Hội Thoại Trung Tâm Để Nghiệm Thu (Hub Gate Verification)
Khi các đoạn hội thoại con hoàn tất, quay về đoạn hội thoại trung tâm:
1. Chạy lại `python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py .` -> Đảm bảo `0` vi phạm (`[-] REMOVE = 0`).
2. Chạy `yarn frontend:build` -> Đảm bảo build xanh `0 errors`.
3. Kiểm tra trên trình duyệt thật qua Playwright MCP.

---

## 3. Bộ Công Cụ Đi Kèm (`scripts/` & `references/`)

- **Tài liệu 4 Quy Tắc Vàng & Mẫu Code**: [references/animation-4-golden-rules.md](./references/animation-4-golden-rules.md)
- **Script quét kiểm toán 4 quy tắc Animation (Read-Only)**:
  ```bash
  python3 .agents/skills/opsx-animation/scripts/scan-animation-rules.py [project_dir] [--output scratch/animation-rules-audit-report.md]
  ```
- **Script Bootstrap & Xuất sang dự án khác (100% không dùng `rm`)**:
  ```bash
  ./.agents/skills/opsx-animation/scripts/bootstrap-animation.sh [--export-to /duong/dan/du-an] [--scan]
  ```
- **Script cài đặt Global**:
  ```bash
  ./.agents/skills/opsx-animation/scripts/install-global.sh
  ```
