---
description: Kiểm toán & chuẩn hóa Giao diện Mobile / Responsive toàn diện không cần Figma Mobile (Graph-Aware Static Scan + Runtime Viewport Check 320/360/390/768px cho lỗi tràn ngang, spacing to quá M20, rớt chữ M11, nhiều cột M10, tap target nhỏ M07, font nhỏ M08/M09)
---

# Workflow `/fe-mobile-audit` (`/opsx-mobile-audit`) — Kiểm Toán & Chuẩn Hóa Giao Diện Mobile / Responsive (Graph-Aware)

Khi người dùng gọi `/fe-mobile-audit` hoặc `/opsx-mobile-audit` (hoặc yêu cầu kiểm tra giao diện mobile/responsive khi dự án chỉ có Figma Desktop mà không có Figma Mobile), hãy đọc kỹ `.agents/skills/fe-mobile-audit/SKILL.md` cùng `.agents/skills/fe-mobile-audit/references/mobile-checklist.md` và thực hiện tuần tự:

## 1. Bước 1 — Quét Tĩnh Toàn Bộ Source FE Kèm Đồ Thị Phụ Thuộc (`Graph-Aware Static Scan`)
1. Chạy công cụ quét tĩnh:
   ```bash
   python3 .agents/skills/fe-mobile-audit/scripts/mobile_audit.py . --graph auto --out docs/mobile
   ```
   *(Có thể giới hạn thư mục bằng `--src resources/Frontend/js` hoặc `--src src app` tùy cấu trúc dự án).*
2. Đọc báo cáo sinh ra tại `docs/mobile/MOBILE-AUDIT.md` và `docs/mobile/mobile-audit.json`.
3. Tập trung kiểm tra các nhóm lỗi Mobile phổ biến nhất khi chuyển từ Figma Desktop sang Mobile:
   - **`M20` (Spacing / Padding / Font to quá trên Mobile)**: Các class desktop đặt thẳng ở base (`px-12`, `py-20`, `gap-12`, `text-5xl`...) chưa hạ xuống `px-4 md:px-12`, `py-10 md:py-20`, `text-2xl md:text-5xl`.
   - **`M11` (Rớt chữ / Tràn chữ do `whitespace-nowrap` hoặc chuỗi dài)**: Cần đổi sang `whitespace-normal md:whitespace-nowrap`, `break-words` hoặc `truncate`.
   - **`M03` / `M04` / `M14` / `M16` (Tràn ngang / Fixed width > 360px / `100vw` / Offset âm)**: Đổi `w-[500px]` thành `w-full max-w-[500px]` hoặc `md:w-[500px]`, thay `w-screen` bằng `w-full`.
   - **`M10` (Nhiều cột `grid-cols-3+` trên màn hình điện thoại)**: Đổi sang `grid-cols-1 sm:grid-cols-2 lg:grid-cols-3+`.
   - **`M07` (Tap target `< 44x44px`)**: Tăng vùng chạm nút/icon trên mobile tối thiểu `44x44px` (`min-h-[44px] min-w-[44px]` hoặc `size-11`).
   - **`M08` / `M09` (Chữ `< 12px` / Input font `< 16px` gây iOS auto-zoom)**: Đảm bảo input có `text-base md:text-sm`.
   - **`M05` / `M12` / `M13` / `M18` (`100vh` -> `100dvh`, bảng thiếu `overflow-x-auto`, thiếu safe-area bottom, ẩn nav desktop mà thiếu mobile drawer)**.

## 2. Bước 2 — Kiểm Chứng Thực Tế Trên Trình Duyệt Đa Khung Nhìn (`Optional Runtime Viewport Check`)
Nếu dev server hoặc bản build đang chạy (ví dụ `http://localhost:8000` hoặc `http://localhost:3000`):
```bash
node .agents/skills/fe-mobile-audit/scripts/viewport_check.mjs --base http://localhost:8000 --routes / /san-pham /ve-chung-toi /lien-he --out docs/mobile/runtime
```
- Chụp ảnh toàn trang và đo DOM thực tế ở các khung nhìn **`320px`**, **`360px`**, **`390px`** và **`768px`**.
- Phát hiện chính xác phần tử gây tràn ngang (`scrollWidth > innerWidth`), phần tử có vùng chạm nhỏ và chữ quá nhỏ.

## 3. Bước 3 — Xếp Hạng Theo Độ Ảnh Hưởng (`Graph Triage`) & Trình Bày Bảng Quyết Định Chờ Confirm
1. Đọc mã nguồn thực tế quanh các điểm báo lỗi Critical/High để loại bỏ dương tính giả (false positives).
2. Ưu tiên xử lý các **Shared Components / Layouts** (`Header`, `Footer`, `MainLayout`, `Card`, `Modal`, `Banner`...) có chỉ số `risk = severity × số trang bị ảnh hưởng` cao nhất trước.
3. Xuất báo cáo tổng hợp vào `docs/mobile/MOBILE-REPORT.md` và trình bày bảng đề xuất sửa lỗi (File:Dòng, Mã lỗi `M01..M20`, Các trang bị ảnh hưởng, Class hiện tại -> Class Mobile-first đề xuất) rồi **Dừng lại chờ người dùng Confirm**.

## 4. Bước 4 — Thực Thi Sửa Lỗi Mobile-First & Nghiệm Thu Lại
Sau khi người dùng Confirm:
1. Sửa theo nguyên tắc **Mobile-First** (class gốc dành cho màn hình `320px–390px`, tiền tố `sm:` / `md:` / `lg:` dành cho màn hình lớn), giữ nguyên 100% giao diện Desktop (`lg:`/`xl:`).
2. Chạy lại `mobile_audit.py` (và `viewport_check.mjs` + `yarn frontend:build` nếu có) để chứng minh số lượng lỗi giảm và `0px` tràn ngang.
