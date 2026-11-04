---
description: Quy trình 5 tầng Multi-Model Audit & Handoff kiểm toán toàn diện mã nguồn, kiểm toán & loại bỏ dữ liệu Hardcode / Return giá trị cố định toàn bộ CMS, tối ưu làm nhẹ source (Source Slimming & Performance) và bàn giao hệ thống
---

# Quy trình 5 Tầng Multi-Model Audit, CMS Hardcode Removal, Source Slimming & Handoff

Quy trình chuẩn hóa kiểm toán dự án đa nền tảng (Backend Laravel + Frontend Next.js / Inertia Vue 3 / BED CMS), kết hợp sức mạnh tối ưu của 5 tầng mô hình AI để đảm bảo tính an toàn, khách quan, **kiểm tra & loại bỏ toàn bộ dữ liệu Hardcode / Return giá trị cố định trong CMS (`CMS Hardcoded & Fixed Return Audit & Removal`)**, **làm nhẹ source & tối ưu hiệu năng (`Source Slimming, Bundle Size & Performance Optimization`)**, bằng chứng 100% (Evidence-based) và sẵn sàng bàn giao vận hành.

---

## 🛑 Nguyên Tắc An Toàn Cốt Lõi (Strict Safe Guards)

1. **Tuyệt đối không tự ý xóa file vật lý**: Không dùng lệnh xóa file (`rm`, script delete) hoặc gỡ package khi chưa lập **Safe Source Slimming Plan (Implementation Plan)** và được người dùng phê duyệt tường minh. *(Việc dùng `edit_file` để remove các khối dữ liệu hardcode / return giá trị cố định bên trong file code và nối vào dữ liệu động của CMS được thực hiện trực tiếp, an toàn).*
2. **Tuyệt đối không phá hủy Database**: Nghiêm cấm chạy `migrate:fresh`, `db:seed`, `drop table`, `truncate` trên mọi môi trường có dữ liệu thật.
3. **Evidence-Based 100%**: Không kết luận phỏng đoán ("chắc là", "có lẽ"). Mọi nhận định lỗi, hardcode return cố định, dead code, dung lượng thừa hoặc an toàn phải có bằng chứng từ terminal runner, output `scan-cms-hardcoded.py`, số đo `du -sh` / `npm run build` hoặc mã nguồn đối chiếu.
4. **Phản xạ Spawn Subagents**: Chia nhỏ từng bước cho các Subagent chuyên trách theo đúng tầng vai trò.

---

## 5 Bước Thực Thi Chi Tiết

### Bước 1: Static, Dependency, Secret, Bloat & CMS Hardcoded Return Scan (Haiku 4.5 / Flash-Lite Worker)
- **Mục tiêu**: Quét nhanh toàn bộ mã nguồn, phát hiện các điểm hardcode & return giá trị cố định trong CMS, kiểm tra thư viện phụ thuộc, secret rò rỉ và dung lượng file/package phình to.
- **Hành động**:
  - **Backend**:
    - Chạy PHPStan / Larastan: `./vendor/bin/phpstan analyse --memory-limit=1G --no-progress`
    - Chạy Composer audit: `composer audit --no-interaction`
    - Rà soát git track: `git ls-files "*env*"`
  - **Frontend**:
    - Chạy ESLint: `npm run lint`
    - Chạy TypeScript typecheck: `npx tsc --noEmit`
    - Chạy NPM audit: `npm audit --omit=dev`
  - **Quét tự động CMS Hardcode & Fixed Return Values**:
    - Chạy công cụ quét tự động trên toàn bộ Backend (`app/`, `routes/`, `Modules/`, `packages/`) và Frontend (`resources/`, `src/`):
      ```bash
      python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .
      ```
  - **Secret scan**: Tìm kiếm chuỗi khóa cứng, token, private key trong `config/`, `app/`, `src/`, `resources/`.
  - **Kiểm tra dung lượng thư mục & file nặng (Disk & Repo Bloat)**:
    - Đo dung lượng tổng và từng thư mục lớn (`du -sh *`), tìm các file nặng `> 1MB` nằm trong source/public/storage (`.sql`, `.zip`, `.tar.gz`, log phình to `storage/logs/*.log`, ảnh/video gốc chưa nén trong `public/`).
    - Kiểm tra các file nặng bị commit nhầm vào Git (`git ls-files` hoặc `git rev-list --objects --all`).
  - **Kiểm tra Thư viện Thừa & Nặng (Unused & Heavy Dependencies)**:
    - Backend (`composer.json`): Phát hiện package nặng không dùng hoặc đặt nhầm gói dev (`faker`, `debugbar`, `ide-helper`, `phpstan`) vào `require` thay vì `require-dev`.
    - Frontend (`package.json`): Phát hiện thư viện trùng lặp/cồng kềnh (`moment` + `dayjs`/`date-fns`, import nguyên `lodash` thay vì `lodash-es`/hàm lẻ, bộ icon/UI thừa, các package không có lệnh `import` nào trong `src/` hoặc `resources/`).
- **Đầu ra**: Lưu báo cáo vào `scratch/step1_static_scan.md`.

### Bước 2: Graphify AST, Cross-Module Logic, CMS Hardcoded Return & Dead Code / Payload Suspects (Gemini 4 Argon Worker)
- **Mục tiêu**: Sử dụng bản đồ AST (`graphify-out/graph.json`) để kiểm toán toàn bộ CMS xem có đang hardcode và return các giá trị cố định không, đối chiếu hợp đồng dữ liệu xuyên module giữa Backend và Frontend, đồng thời truy tìm Dead Code, file mồ côi và điểm nghẽn làm nặng payload/runtime.
- **Hành động**:
  - Tra cứu quan hệ hàm, class, route qua `graphify-out/graph.json`.
  - **Kiểm tra toàn bộ CMS có đang Hardcode & Return các giá trị cố định không (`CMS Hardcoded & Fixed Return Audit`)**:
    - **Backend CMS (`app/Http/Controllers/`, `app/Services/`, `app/Repositories/`, `app/Models/`, `app/Http/Middleware/HandleInertia*.php`, `routes/`)**: Kiểm tra mọi hàm/method xem có đang hardcode dữ liệu và `return` mảng/object/scalar cố định (`getDefault*()`, `getFallback*()`, `getContactFaqs()`, `getSolutionOptions()`, `default*Settings()`, hoặc `return [...]` chứa danh sách sản phẩm, bài viết, danh mục, chính sách, đại lý, FAQ, slider, số liệu thống kê Dashboard, thông tin liên hệ/setting cố định) thay vì lấy từ Database / Settings hay không.
    - **Frontend & CMS UI (`resources/Frontend/js/`, `resources/Backend/js/`, `resources/js/`, `src/`)**: Kiểm tra toàn bộ `Pages/`, `Components/`, `Layouts/`, `Stores/`, `Composables/` xem có đang khai báo mảng dữ liệu tĩnh (`FALLBACK_*`, `default*`, mock arrays), `computed()` / hàm getter return danh sách cố định, hoặc `props` fallback che giấu trạng thái DB rỗng hay không; kiểm tra hardcode `tel:`, `mailto:`, địa chỉ, mạng xã hội trong template.
    - Liệt kê chi tiết từng vị trí thành mã `HARDCODE-01`, `HARDCODE-02`... kèm tọa độ `File:Dòng`, tên hàm/component và nguồn dữ liệu CMS động thay thế.
  - **Đối chiếu logic nghiệp vụ nhạy cảm**:
    - Tính toán chiết khấu giỏ hàng, phí ship, voucher (`orderService.ts` vs `OrderService.php`, `Voucher.php`).
    - Luồng thanh toán chuyển khoản, QR inbound/outbound (`VietqrInboundController.php`, `vietqr.php`).
    - Luồng đồng bộ hóa và webhook bên thứ ba (`KiotVietWebhookController.php`).
    - Tính nhất quán giữa URL routing Frontend (`routing.ts`, `next.config.js`) và `sitemap.xml` Backend.
    - Kiểm soát truy cập (middleware `permission:*`, xác thực PII của khách hàng).
  - **Dò tìm Dead Code, File Mồ Côi & Asset Thừa qua AST (`graphify-out/graph.json`)**:
    - Xác định các Component, Hook, Service, Controller, Model, Helper không có bất kỳ liên kết gọi tới nào (`0 inbound edges` / `orphaned nodes`).
    - Xác định các file tĩnh (ảnh, icon, CSS, JS cũ) trong `public/`, `src/assets/` không còn được tham chiếu.
  - **Phát hiện Điểm Nghẽn Làm Nặng Runtime & Payload (Runtime & Payload Bloat)**:
    - Backend: Truy vấn N+1 (thiếu `with()`), lấy toàn bộ dữ liệu không phân trang (`::all()`, `->get()` trên bảng lớn), `SELECT *` kéo theo cột văn bản dài (`content`, `description`, `meta_json`) ở các API danh sách (list endpoints), thiếu `Cache::remember` cho cấu hình/menu/danh mục tĩnh.
    - Frontend: Component lớn (Modal, Chart, Editor, Swiper, Map) import tĩnh ngay ở trang chủ/layout thay vì `next/dynamic` hoặc `defineAsyncComponent` (Lazy Load); truyền nguyên object dữ liệu khổng lồ từ Server xuống Client Component làm phình HTML/RSC payload; ảnh chưa dùng `next/image` hoặc thiếu tối ưu WebP/AVIF.
- **Đầu ra**: Lập danh mục các điểm nghi vấn hardcode CMS (`HARDCODE-*`), nghi vấn nghiệp vụ và làm nặng source vào `scratch/step2_argon_suspects.md`.

### Bước 3: Deep Verification, Hardcode Confirmation, Bundle Analysis & Runtime Proof (Opus 4.5 / Deep Verifier)
- **Mục tiêu**: Thẩm định chuyên sâu từng nghi vấn của Bước 1 & 2 với bằng chứng thực tế, xác nhận danh sách `[CONFIRMED CMS HARDCODE / FIXED RETURN]` cần remove, đo đạc bundle size/query thật, phân loại lỗi thật (`[CONFIRMED BUG]`), phình source (`[CONFIRMED BLOAT / DEAD CODE]`) và báo động giả (`[FALSE POSITIVE]`).
- **Hành động**:
  - Đọc trực tiếp các dòng mã liên quan để lần theo luồng rẽ nhánh thực tế và xác định nguồn dữ liệu động CMS (Model / Setting / Props / Empty `[]`) sẽ thay thế cho từng điểm `[CONFIRMED CMS HARDCODE / FIXED RETURN]`.
  - Chạy các bài test đơn vị hoặc test tính năng liên quan: `php artisan test --filter=...` (chạy trên DB test hoặc môi trường giả lập an toàn).
  - Phân tích rủi ro khai thác bảo mật (PoC logic) đối với các webhook thiếu chữ ký hoặc endpoint thiếu rate-limit.
  - **Đo đạc thực tế kích thước Build / Bundle & Hiệu năng**:
    - Kiểm tra output thực tế của `npm run build` / `yarn build` (`First Load JS` trên Next.js hoặc các chunk `> 500 kB` trên Vite).
    - Kiểm chứng các nghi vấn Dead Code / Unused Dependency xem có được gọi động (dynamic call / magic method / string route) hay thực sự là rác (`[CONFIRMED BLOAT / DEAD CODE]`).
    - Đo số lượng câu query hoặc kích thước JSON response trên các API trang chủ/danh sách sản phẩm.
- **Đầu ra**: Báo cáo thẩm định có bằng chứng 100% tại `scratch/step3_opus_verified_findings.md`.

### Bước 4: CMS Hardcode Removal, Config, Env Drift, Safe Slimming Plan & OpenSpec Packaging (Sonnet 3.5/5.5 Worker)
- **Mục tiêu**: Liệt kê đầy đủ và thực thi **remove** toàn bộ các giá trị hardcode / return cố định trong CMS, rà soát cấu hình triển khai, cấu hình build/cache giảm tải, lập kế hoạch làm nhẹ source an toàn (`Safe Source Slimming Plan`) và đóng gói bộ OpenSpec change.
- **Hành động**:
  - **Liệt kê & Remove toàn bộ giá trị Hardcode / Return cố định trong CMS (`CMS Hardcode Inventory & Removal`)**:
    - Lập bảng liệt kê chi tiết toàn bộ vị trí `[CONFIRMED CMS HARDCODE / FIXED RETURN]` (Tọa độ `File:Dòng`, Hàm/Component, Giá trị cố định đang return, Nguồn dữ liệu động thay thế).
    - Dùng `edit_file` **remove** các hàm/mảng/giá trị hardcode và lệnh `return` cố định trên cả Backend (Controllers, Services, Models, Middleware) và Frontend (Pages, Components, Layouts, Stores, Composables), kết nối về dữ liệu động của CMS (Eloquent Models, Settings, Inertia Props) hoặc trả về mảng rỗng `[]` / `null` chuẩn.
    - Chạy lại `python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .`, `npm run build` và `php artisan test` để xác nhận đã remove sạch sẽ và build xanh.
  - **Đối chiếu biến môi trường & Script nguy hiểm**:
    - So sánh `config/*.php` với `.env.example` của Backend để bổ sung các biến còn thiếu.
    - So sánh `next.config.js`, `process.env.*` của Frontend và chuẩn hóa file mẫu `.env.example` cho Frontend.
    - Rà soát các script triển khai nguy hiểm trong `composer.json` (`compile` chạy `migrate:fresh`) và `package.json`.
  - **Kiểm tra cấu hình Build & Cache giúp giảm tải**:
    - Frontend: Kiểm tra `next.config.js` / `vite.config.ts` (`optimizePackageImports`, `modularizeImports`, `swcMinify`, nén ảnh, tách `manualChunks`).
    - Backend: Kiểm tra cấu hình cache production (`config:cache`, `route:cache`, `view:cache`, `event:cache`, `OPcache`, `compression/gzip`).
  - **Lập Kế Hoạch Làm Nhẹ Source An Toàn (`Safe Source Slimming Plan`)**:
    - **Nhóm Tối Ưu Trực Tiếp (Không xóa file)**: Remove dữ liệu hardcode return cố định trong code, tối ưu `select()` / `with()`, bật lazy-load `dynamic()`, bật `optimizePackageImports`, chuyển gói dev sang `require-dev`, cập nhật `.gitignore`.
    - **Nhóm Dọn Dẹp File/Asset/Package Thừa (Cần User Phê Duyệt)**: Liệt kê chính xác đường dẫn file/package thừa + dung lượng giảm được, lập thành **Implementation Plan** để người dùng phê duyệt trước khi xóa (tuân thủ tuyệt đối `STRICT SAFE GUARD: CẤM TỰ Ý XÓA FILE`).
  - Đóng gói quy trình thành Workflow Antigravity, Command Claude Code và OpenSpec Change chuẩn (`specs/`, `proposal.md`, `design.md`, `tasks.md`).
- **Đầu ra**: Báo cáo kết quả remove hardcode CMS, checklist cấu hình & kế hoạch làm nhẹ source tại `scratch/step4_sonnet_config_and_checklist.md`.

### Bước 5: Lead Architect Synthesis & Executive Handoff Matrix (Main Agent)
- **Mục tiêu**: Tổng hợp toàn bộ phát hiện, báo cáo danh sách các điểm hardcode / return cố định trong CMS đã được liệt kê và remove, đánh giá tác động kinh doanh & hiệu năng, lập kế hoạch làm nhẹ source và bàn giao chi tiết cho khách hàng.
- **Hành động**:
  - Phân loại lỗi theo ma trận ưu tiên: P0 (Blocker/Lỗ hổng bảo mật nghiêm trọng), P1 (High/Hardcode return cố định làm sai lệch dữ liệu CMS, sai lệch tài chính, voucher, nghẽn hiệu năng nghiêm trọng), P2 (Medium/Cảnh báo chất lượng code, dead code, thư viện thừa).
  - Lập bảng **"Báo Cáo Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal Summary`)"** (liệt kê rõ từng tọa độ `File:Dòng`, giá trị hardcode cũ vs nguồn dữ liệu động mới sau khi remove).
  - Lập bảng **"Báo Cáo Tối Ưu Hóa & Giảm Tải Source (`Source Slimming & Performance Summary`)"** bên cạnh ma trận lỗi P0/P1/P2 (gồm: Dung lượng ổ đĩa/repo có thể giảm, Kích thước JS/CSS bundle giảm được, Các API/Query đã hoặc cần giảm tải).
  - Lập lộ trình khắc phục & dọn dẹp theo thứ tự ưu tiên an toàn nhất (Zero Regression) và trình bày tài liệu bàn giao rõ ràng cho khách hàng.
