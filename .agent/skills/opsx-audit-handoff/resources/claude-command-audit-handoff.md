---
description: Thực hiện quy trình 5 tầng kiểm toán toàn diện mã nguồn, kiểm tra & loại bỏ dữ liệu Hardcode / Return giá trị cố định toàn bộ CMS, tối ưu làm nhẹ source (Source Slimming & Performance), cấu hình và chuẩn bị bàn giao dự án
---

# Lệnh /opsx:audit-handoff - Multi-Model Audit, CMS Hardcode Removal, Source Slimming & Handoff Pipeline

Lệnh này hướng dẫn Claude Code thực hiện quy trình kiểm toán toàn diện, dọn sạch hardcode CMS & làm nhẹ source 5 tầng theo tiêu chuẩn bằng chứng 100% (Evidence-based) và quy tắc an toàn nghiêm ngặt (Safe Guard Policy).

## Các bước thực hiện:

1. **Bước 1: Quét Tĩnh, Phụ Thuộc, Secret, Dung Lượng Phình To & CMS Hardcoded Return (Static, Dependency, Secret, Bloat & Hardcode Scan)**
   - **Backend**: Chạy PHPStan (`./vendor/bin/phpstan analyse --memory-limit=1G --no-progress`) và `composer audit`.
   - **Frontend**: Chạy `npm run lint`, `npx tsc --noEmit` và `npm audit --omit=dev`.
   - **CMS Hardcoded & Fixed Return Scan**: Chạy `python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .` để quét tự động toàn bộ Backend (`app/`, `routes/`, `Modules/`, `packages/`) và Frontend (`resources/`, `src/`) phát hiện các hàm/method/computed/component đang hardcode và return giá trị cố định.
   - **Secret Scan**: Quét secret rò rỉ và hardcoded credentials trong `config/`, `app/`, `src/`, `resources/`.
   - **Disk & Repo Bloat**: Đo dung lượng tổng và từng thư mục lớn (`du -sh *`), tìm các file nặng `> 1MB` trong source/public/storage (`.sql`, `.zip`, `.tar.gz`, `storage/logs/*.log`, ảnh/video chưa nén trong `public/`) và file nặng bị commit nhầm vào Git (`git ls-files` / `git rev-list --objects --all`).
   - **Unused & Heavy Dependencies**:
     - Backend (`composer.json`): Phát hiện package nặng không dùng hoặc đặt nhầm gói dev (`faker`, `debugbar`, `ide-helper`, `phpstan`) vào `require` thay vì `require-dev`.
     - Frontend (`package.json`): Phát hiện thư viện trùng lặp/cồng kềnh (`moment` + `dayjs`/`date-fns`, import nguyên `lodash` thay vì `lodash-es`, bộ icon/UI thừa, package không có lệnh `import` trong `src/` hoặc `resources/`).
   - Ghi kết quả vào `scratch/step1_static_scan.md`.

2. **Bước 2: Phân Tích AST, Kiểm Toán Hardcode CMS, Hợp Đồng Dữ Liệu & Dead Code / Điểm Nghẽn Payload (Graphify AST, CMS Hardcoded Return, Cross-Module Logic & Bloat Suspects)**
   - Tra cứu quan hệ hàm/module qua `graphify-out/graph.json`.
   - **Kiểm Toán Toàn Bộ CMS Có Đang Hardcode & Return Giá Trị Cố Định Không (`CMS Hardcoded & Fixed Return Deep Audit`)**:
     - Backend (`Controllers`, `Services`, `Repositories`, `Models`, `HandleInertiaRequests`, `routes/`): Kiểm tra mọi hàm/method đang hardcode và `return` mảng/object/số liệu cố định (`getDefault*()`, `getFallback*()`, `getContactFaqs()`, `getSolutionOptions()`, `default*Settings()`, `return [...]` chứa danh sách sản phẩm/bài viết/danh mục/chính sách/đại lý/FAQ/slider/thống kê giả lập) thay vì lấy từ Database / Settings.
     - Frontend (`Pages`, `Components`, `Layouts`, `Stores`, `Composables`): Kiểm tra các mảng tĩnh (`FALLBACK_*`, `default*`, mock arrays), `computed()` / hàm getter return danh sách cố định, `props` fallback che giấu DB rỗng, và thông tin liên hệ (`tel:`, `mailto:`, địa chỉ) gán cứng trong template.
     - Liệt kê chi tiết vào danh sách `HARDCODE-01`, `HARDCODE-02`... kèm tọa độ `File:Dòng`.
   - **Nghi vấn Nghiệp vụ & Bảo mật**: Chiết khấu voucher/freeship, luồng hủy đơn hàng, chữ ký webhook, PII, cấu hình URL `sitemap.xml` vs Frontend routing.
   - **Dead Code, File Mồ Côi & Asset Thừa**: Xác định Component, Hook, Service, Controller, Model, Helper không có liên kết gọi tới (`0 inbound edges` / `orphaned nodes`) và file tĩnh (ảnh, icon, CSS, JS cũ) trong `public/`, `src/assets/` không còn được tham chiếu.
   - **Điểm Nghẽn Runtime & Payload Bloat**:
     - Backend: Truy vấn N+1 (thiếu `with()`), lấy toàn bộ dữ liệu không phân trang (`::all()`, `->get()` trên bảng lớn), `SELECT *` kéo theo cột văn bản dài (`content`, `description`, `meta_json`) ở các API danh sách, thiếu `Cache::remember` cho cấu hình/menu/danh mục tĩnh.
     - Frontend: Component lớn (Modal, Chart, Editor, Swiper, Map) import tĩnh ở trang chủ/layout thay vì `next/dynamic` hoặc `defineAsyncComponent` (Lazy Load); truyền nguyên object khổng lồ từ Server xuống Client Component làm phình HTML/RSC payload; ảnh chưa dùng `next/image` hoặc thiếu tối ưu WebP/AVIF.
   - Ghi kết quả vào `scratch/step2_argon_suspects.md`.

3. **Bước 3: Thẩm Định Chuyên Sâu, Xác Nhận Hardcode CMS, Phân Tích Bundle & Bằng Chứng Runtime (Deep Verification, Hardcode Confirmation, Bundle Analysis & Runtime Proof)**
   - Đọc trực tiếp code nguồn tại các vị trí nghi vấn, xác nhận danh sách `[CONFIRMED CMS HARDCODE / FIXED RETURN]` kèm nguồn dữ liệu động CMS thay thế, và chạy test kiểm chứng an toàn: `php artisan test --filter=...`.
   - **Đo đạc thực tế kích thước Build / Bundle & Hiệu năng**:
     - Kiểm tra output thực tế của `npm run build` / `yarn build` (`First Load JS` trên Next.js hoặc các chunk `> 500 kB` trên Vite).
     - Kiểm chứng các nghi vấn Dead Code / Unused Dependency xem có được gọi động (dynamic call / magic method / string route) hay thực sự là rác (`[CONFIRMED BLOAT / DEAD CODE]`).
     - Đo số lượng câu query hoặc kích thước JSON response trên các API trang chủ/danh sách sản phẩm.
   - Phân biệt rõ ràng giữa `[CONFIRMED BUG]`, `[CONFIRMED CMS HARDCODE / FIXED RETURN]`, `[CONFIRMED BLOAT / DEAD CODE]`, `[FALSE POSITIVE]` và `[TECH DEBT]`.
   - Ghi kết quả vào `scratch/step3_opus_verified_findings.md`.

4. **Bước 4: Liệt Kê & Remove Hardcode CMS, Rà Soát Cấu Hình, Kế Hoạch Làm Nhẹ Source & Đóng Gói Bàn Giao (CMS Hardcode Removal, Config, Env Drift, Slimming Plan & OpenSpec Packaging)**
   - **Liệt kê & Remove toàn bộ giá trị Hardcode / Return cố định trong CMS**:
     - Xuất bảng danh sách đầy đủ tọa độ `File:Dòng`, tên hàm/component và giá trị cố định đang return.
     - Tiến hành **remove** các giá trị hardcode/return cố định đó trên cả Backend & Frontend, kết nối đúng về nguồn dữ liệu động của CMS (Eloquent Model / Settings / Inertia Props) hoặc trả về mảng rỗng `[]` / `null` chuẩn.
     - Chạy lại `scan-cms-hardcoded.py`, `npm run build` và `php artisan test` để xác nhận đã dọn sạch và không lỗi.
   - Đối chiếu `.env.example` với `config/*.php` (Backend) và `next.config.js` / `src/` (Frontend).
   - Rà soát các script nguy hiểm trong `composer.json` (`compile` chạy `migrate:fresh`) và `package.json`.
   - **Kiểm tra cấu hình Build & Cache giảm tải**:
     - Frontend: Kiểm tra `next.config.js` / `vite.config.ts` (`optimizePackageImports`, `modularizeImports`, `swcMinify`, nén ảnh, tách `manualChunks`).
     - Backend: Kiểm tra cấu hình cache production (`config:cache`, `route:cache`, `view:cache`, `event:cache`, `OPcache`, `compression/gzip`).
   - **Lập Kế Hoạch Làm Nhẹ Source An Toàn (`Safe Source Slimming Plan`)**:
     - **Nhóm Tối Ưu Trực Tiếp (Không xóa file)**: Remove dữ liệu hardcode return cố định trong code, tối ưu `select()` / `with()`, bật lazy-load `dynamic()`, bật `optimizePackageImports`, chuyển gói dev sang `require-dev`, cập nhật `.gitignore`.
     - **Nhóm Dọn Dẹp File/Asset/Package Thừa (Cần User Phê Duyệt)**: Liệt kê chính xác đường dẫn file/package thừa + dung lượng giảm được, lập thành **Implementation Plan** để người dùng phê duyệt trước khi xóa.
   - Cập nhật checklist bàn giao tại `scratch/step4_sonnet_config_and_checklist.md`.

5. **Bước 5: Báo Cáo Tổng Hợp, Bảng Remove Hardcode CMS, Bảng Làm Nhẹ Source & Phê Duyệt Bàn Giao (Lead Architect Synthesis & Executive Handoff Matrix)**
   - Phân loại lỗi theo ma trận P0 / P1 / P2.
   - Bổ sung bảng **"Báo Cáo Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal Summary`)"** (liệt kê đầy đủ tọa độ `File:Dòng` đã phát hiện và remove).
   - Bổ sung bảng **"Báo Cáo Tối Ưu Hóa & Giảm Tải Source (`Source Slimming & Performance Summary`)"** bên cạnh ma trận lỗi P0/P1/P2 (gồm: Dung lượng ổ đĩa/repo có thể giảm, Kích thước JS/CSS bundle giảm được, Các API/Query đã hoặc cần giảm tải).
   - Lập kế hoạch vá lỗi, kế hoạch làm nhẹ source và bàn giao hoàn tất cho khách hàng.

## ⚠️ Cảnh báo an toàn:
- KHÔNG tự ý xóa file vật lý (`rm`) hoặc gỡ package khi chưa có phê duyệt của người dùng trên Implementation Plan.
- KHÔNG chạy các lệnh phá hủy DB (`migrate:fresh`, `db:seed`, `drop`, `truncate`).
- KHÔNG kết luận cảm tính khi chưa có output terminal runner / build log thật.
