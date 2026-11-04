---
name: opsx-audit-handoff
description: Quy trình 5 tầng Multi-Model Audit & Handoff kiểm toán toàn diện mã nguồn, kiểm toán & loại bỏ dữ liệu Hardcode / Return giá trị cố định toàn bộ CMS (CMS Hardcoded & Fixed Return Audit & Removal), tối ưu hóa làm nhẹ source (Source Slimming, Bundle Size & Performance Optimization Audit: Static & Bloat Scan, Graphify AST Cross-Module & Dead Code/Payload Suspects, Deep Runtime/Bundle Verification, Config/Cache & Safe Slimming Plan, Executive Handoff Matrix P0/P1/P2) và bàn giao hệ thống. Sử dụng khi người dùng gọi "/opsx-audit-handoff", "/opsx:audit-handoff", yêu cầu kiểm toán mã nguồn/dọn hardcode CMS/tối ưu dung lượng source trước bàn giao, hoặc muốn đồng bộ quy trình audit sang dự án khác.
---

# OPSX Audit & Handoff — Quy Trình Kiểm Toán Toàn Diện 5 Tầng, Dọn Sạch Hardcode CMS, Làm Nhẹ Source & Bàn Giao Hệ Thống

Skill này chuẩn hóa quy trình kiểm toán mã nguồn, **kiểm toán & loại bỏ các giá trị Hardcode / Return cố định toàn bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal`)**, **tối ưu hóa & làm nhẹ source (`Source Slimming, Bundle Size & Performance Optimization`)**, cấu hình môi trường, kịch bản triển khai và nghiệm thu bàn giao dự án theo mô hình **5 Tầng Multi-Model Pipeline**, đảm bảo tính khách quan, bằng chứng thực thi 100% (`Evidence-Based`) và an toàn tuyệt đối (`Strict Safe Guard`).

Hỗ trợ linh hoạt mọi kiến trúc dự án của Team:
- **Multi-Repo / Tách biệt**: Backend **Laravel 10/11** + Frontend **Next.js / React**.
- **Monolith / BED CMS**: **Laravel + Inertia.js + Vue 3** (`resources/Frontend/js`, `resources/Backend/js` hoặc `resources/js`).
- **Single-Repo**: Chỉ Backend API hoặc chỉ Frontend SPA/SSR.

---

## 1. Khi Nào Sử Dụng & Cách Kích Hoạt Nhanh

### Các tình huống kích hoạt:
- **Tình huống A — Kiểm toán toàn diện trước khi Go-Live / Bàn giao khách hàng**: Rà soát lỗ hổng bảo mật, sai lệch công thức tính toán tiền/voucher/shipping, script phá hủy DB, biến môi trường bị thiếu.
- **Tình huống B — Kiểm toán & Dọn sạch Hardcode / Return Cố định toàn bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal`)**: Khi chạy audit cần kiểm tra toàn bộ hệ thống CMS (Backend Controllers, Services, Repositories, Models, Middleware `HandleInertiaRequests`, Configs & Frontend Admin/Client Pages, Components, Layouts, Stores, Composables) xem có đang hardcode và `return` các giá trị cố định (mảng dữ liệu giả lập, fallback che giấu DB rỗng, số liệu thống kê/counter giả, thông tin liên hệ/setting gán cứng) hay không — nếu có thì **liệt kê đầy đủ tọa độ `File:Dòng`** và **tiến hành remove** để kết nối đúng dữ liệu động từ CMS/Database/Settings.
- **Tình huống C — Kiểm toán Tối ưu & Làm nhẹ Source (`Source Slimming & Performance Audit`)**: Khi source dự án bị phình to, chậm chạp, bundle JS/CSS nặng, nhiều package/file/asset thừa hoặc API query chậm (N+1, `SELECT *`).
- **Tình huống D — Nghiệm thu sau một đợt sửa lỗi lớn (`Post-Wave Audit`)**: Kiểm tra chéo xem các bản vá có tạo ra lỗi hồi quy (Regression) hay xung đột hợp đồng API giữa Backend và Frontend không.
- **Tình huống E — Đồng bộ quy trình Audit sang dự án mới**: Xuất toàn bộ Workflow, Claude Command, Skill, Script quét tự động và OpenSpec Change mẫu sang bất kỳ thư mục dự án nào chỉ với 1 lệnh.

### Cách gọi nhanh ở bất kỳ đoạn hội thoại nào:
- **Trên Google Antigravity**: Gõ `/opsx-audit-handoff` (hoặc nhắn *"Chạy quy trình /opsx-audit-handoff kiểm toán, dọn hardcode CMS và làm nhẹ dự án"*).
- **Trên Claude Code CLI**: Gõ `/opsx:audit-handoff`.

---

## 2. Nguyên Tắc An Toàn Cốt Lõi (STRICT SAFE GUARD)

Mọi Agent (Main Agent và tất cả Subagents) khi thực thi skill này **BẮT BUỘC** tuân thủ:
1. **CẤM TỰ Ý XÓA FILE VẬT LÝ**: Tuyệt đối không chạy `rm`, `rm -rf`, `git clean -fd` hoặc xóa bất kỳ file/thư mục/asset/package nào khi chưa lập **Implementation Plan (`Safe Source Slimming Plan`)** và được người dùng phê duyệt rõ ràng. *(Lưu ý: Việc chỉnh sửa code bên trong file để **remove các khối dữ liệu hardcode / return giá trị cố định** và thay bằng truy vấn động từ CMS/DB/Settings hoặc mảng rỗng `[]` được thực hiện qua `edit_file` an toàn, không xóa file vật lý).*
2. **CẤM PHÁ HỦY DATABASE**: Nghiêm cấm chạy `php artisan migrate:fresh`, `db:seed` đè dữ liệu thật, `DROP TABLE`, `TRUNCATE` trên môi trường đang có dữ liệu. Khi chạy test bắt buộc chỉ định rõ file test cô lập (`php artisan test --filter=...`) và kiểm tra cấu hình `phpunit.xml` trước khi chạy.
3. **BẰNG CHỨNG THẬT 100% (EVIDENCE-BASED & ZERO-HALLUCINATION)**:
   - Không dùng từ ngữ phỏng đoán (*"chắc là"*, *"có lẽ"*, *"về lý thuyết"*).
   - Mọi kết luận `Confirmed Bug`, `Confirmed CMS Hardcode / Fixed Return`, `Confirmed Bloat / Dead Code`, `False Positive`, hoặc `PASS` đều phải đính kèm đường dẫn `file:line`, dung lượng đo đạc thực tế (`du -sh`, `npm run build`), output từ script `scan-cms-hardcoded.py` và raw output thật từ terminal runner (`phpstan`, `tsc`, `eslint`, `artisan test`, `vitest`).
4. **PHẢN XẠ SPAWN SUBAGENTS (MANDATORY SUBAGENT REFLEX)**:
   - Main Agent đóng vai trò **Lead Architect (Tầng 5)** điều phối, không tự chạy toàn bộ các bước quét nặng trên luồng chính để tránh tràn context window.
   - Bắt buộc gọi `invoke_subagent` tách biệt theo từng tầng và giao tiếp qua các file báo cáo trung gian trong thư mục `scratch/`.

---

## 3. Chi Tiết 5 Tầng Thực Thi (5-Tier Multi-Model Pipeline)

```text
+---------------------------------------------------------------------------------+
| Tầng 1: Static, Secret, Bloat & CMS Hardcoded Return Scanner (Flash-Lite/Haiku) |
| -> Output: scratch/step1_static_scan.md                                         |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 2: Graphify AST, Cross-Module, CMS Hardcode & Bloat Auditor (Argon Worker) |
| -> Output: scratch/step2_argon_suspects.md                                      |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 3: Deep Verification, Hardcode Proof & Bundle Analysis (Opus / Tester)     |
| -> Output: scratch/step3_opus_verified_findings.md                              |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 4: CMS Hardcode Removal, Config, Env Drift & Safe Slimming Plan (Sonnet)   |
| -> Output: scratch/step4_sonnet_config_and_checklist.md                         |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 5: Lead Architect Synthesis & Executive Handoff Matrix (Main Agent)        |
| -> Output: Ma trận P0/P1/P2, Bảng Remove Hardcode CMS, Báo cáo Làm Nhẹ Source   |
+---------------------------------------------------------------------------------+
```

### Tầng 1: Static, Dependency, Secret, Bloat & CMS Hardcoded Return Scan
- **Vai trò Subagent**: `Static, Secret, Bloat & CMS Hardcode Scanner`
- **Hành động**:
  1. **Quét Tĩnh & Bảo Mật Phụ Thuộc (SAST & Security Audit)**:
     - **Backend (Laravel)**:
       - Chạy PHPStan / Larastan: `./vendor/bin/phpstan analyse --memory-limit=1G --no-progress`
       - Chạy Composer security audit: `composer audit --no-interaction`
       - Kiểm tra file nhạy cảm bị git theo dõi: `git ls-files "*env*"`
     - **Frontend (Next.js / Vue 3 / Inertia)**:
       - Chạy Linter: `npm run lint`
       - Chạy Typecheck: `npx tsc --noEmit` (hoặc `npx vue-tsc --noEmit`)
       - Chạy NPM audit: `npm audit --omit=dev`
     - **Secret & Hardcoded Credentials Scan**:
       - Quét các khóa cứng (API key, JWT secret, mật khẩu, số tài khoản ngân hàng cá nhân fallback) trong `config/`, `app/`, `src/`, `resources/`.
  2. **Quét Tự Động Toàn Bộ CMS Phát Hiện Hardcode & Return Giá Trị Cố Định (`CMS Hardcoded & Fixed Return Scan`)**:
     - Chạy công cụ quét tự động đi kèm skill:
       ```bash
       python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .
       ```
       *(Hoặc `"$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py" .` nếu gọi từ Global).*
     - Quét toàn bộ thư mục Backend (`app/`, `routes/`, `Modules/`, `packages/`) và Frontend/CMS (`resources/`, `src/`) để phát hiện sớm mọi hàm/method/computed/component đang hardcode dữ liệu và `return` giá trị cố định.
  3. **Kiểm tra Dung lượng Thư mục & File Nặng (Disk & Repo Bloat)**:
     - Đo dung lượng tổng và từng thư mục lớn (`du -sh *`, `du -sh .[!.]*` loại trừ truy cập sâu vào `node_modules`/`vendor`), tìm các file nặng `> 1MB` nằm trong source/public/storage (`find . -maxdepth 4 -not -path '*/node_modules/*' -not -path '*/vendor/*' -not -path '*/.git/*' -size +1M -exec ls -lh {} +`): phát hiện file `.sql`, `.zip`, `.tar.gz`, backup cũ, log phình to (`storage/logs/*.log`), hoặc ảnh/video gốc chưa nén trong `public/`.
     - Kiểm tra các file nặng bị commit nhầm vào lịch sử/index Git (`git ls-files` kết hợp kiểm tra kích thước hoặc `git rev-list --objects --all`).
  4. **Kiểm tra Thư viện Thừa & Nặng (Unused & Heavy Dependencies)**:
     - **Backend (`composer.json`)**: Phát hiện package nặng không dùng hoặc đặt nhầm gói dev (`fakerphp/faker`, `barryvdh/laravel-debugbar`, `barryvdh/laravel-ide-helper`, `phpstan/phpstan`, `nunomaduro/collision`, `laravel/telescope`) vào `require` thay vì `require-dev`.
     - **Frontend (`package.json`)**: Phát hiện thư viện trùng lặp/cồng kềnh (ví dụ: cài trùng cả `moment` + `dayjs`/`date-fns`, import nguyên `lodash` thay vì `lodash-es`/hàm lẻ, nhiều bộ icon/UI thừa, các package trong `dependencies` không có bất kỳ lệnh `import`/`require` nào trong `src/` hoặc `resources/`).
- **Đầu ra bắt buộc**: Ghi đầy đủ raw log tóm tắt, kết quả `scan-cms-hardcoded.py`, thống kê dung lượng và danh sách cảnh báo vào `scratch/step1_static_scan.md`.

### Tầng 2: Graphify AST, Cross-Module Logic, CMS Hardcoded Return & Dead Code / Payload Suspects
- **Vai trò Subagent**: `Cross-Module AST, CMS Hardcode & Bloat Auditor`
- **Hành động**:
  - Đọc bản đồ AST tại `graphify-out/graph.json` (nếu chưa có, chạy `graphify . --code-only` để khởi tạo) nhằm định vị chính xác các file nghiệp vụ cốt lõi và quan hệ phụ thuộc mà không cần `grep` toàn bộ repo.
  1. **Kiểm Toán Chuyên Sâu Toàn Bộ CMS Có Đang Hardcode & Return Giá Trị Cố Định Không (`CMS Hardcoded & Fixed Return Deep Audit`)**:
     - **Trên Backend CMS & API (`app/Http/Controllers/`, `app/Services/`, `app/Repositories/`, `app/Actions/`, `app/Models/`, `app/Http/Middleware/HandleInertia*.php`, `routes/`)**:
       - Kiểm tra tất cả các hàm/method xem có đang hardcode và `return` các mảng/object cố định hay không (ví dụ: `getDefault*()`, `getFallback*()`, `getContactFaqs()`, `getSolutionOptions()`, `default*Settings()`, hoặc `return [...]` chứa danh sách sản phẩm, bài viết, danh mục, chính sách, đại lý, câu hỏi FAQ, bước quy trình, banner/slider, số liệu thống kê Dashboard cố định).
       - Kiểm tra các Controller/Service có đang dùng dữ liệu fallback cố định đè lên hoặc che giấu việc Database/CMS chưa nhập liệu (`if ($items->isEmpty()) return $this->getFallback...()`) khiến Admin xóa/sửa trong CMS nhưng ngoài Frontend vẫn hiện dữ liệu cố định hay không.
       - Kiểm tra Middleware chia sẻ dữ liệu toàn cục (`HandleInertiaRequests` / `HandleInertiaFrontendRequests`) và Model `Config` / `Setting` xem có đang `return` thông tin công ty, hotline, email, địa chỉ, mạng xã hội, menu hay SEO meta cố định thay vì lấy từ bảng `settings` / `configs` trong DB hay không.
     - **Trên Frontend Client & CMS Admin (`resources/Frontend/js/`, `resources/Backend/js/`, `resources/js/`, `src/`)**:
       - Kiểm tra toàn bộ `Pages/`, `Components/`, `Layouts/`, `Stores/`, `Composables/`: phát hiện các biến mảng tĩnh (`const default* = [...]`, `const FALLBACK_* = [...]`), các hàm/computed return mảng cố định (`computed(() => props.items?.length ? props.items : defaultItems)`), hoặc các giá trị `default: () => [{...}]` trong `defineProps` chứa nội dung giả lập.
       - Kiểm tra các đoạn hardcode số điện thoại (`tel:...`), email (`mailto:...`), địa chỉ, link mạng xã hội, tiêu đề/mô tả cố định trực tiếp trong template `.vue` / `.tsx` / `.blade.php` thay vì đọc từ `$page.props.settings` / `props.global` của CMS.
     - Liệt kê toàn bộ vào danh sách mã định danh `HARDCODE-01`, `HARDCODE-02`... kèm tọa độ `File:Dòng`, tên hàm/biến, giá trị đang return cố định và nguồn dữ liệu CMS động tương ứng.
  2. **Đối chiếu Hợp đồng Dữ liệu & Nghiệp vụ Xuyên Module (API Contract & Business Logic)**:
     - **Công thức tài chính & Giỏ hàng**: Tính `subtotal`, chiết khấu `Voucher` / `EVOUCHER` / `Freeship`, phân hạng thành viên (`Member Tier Discount`), làm tròn số tiền.
     - **Vòng đời Đơn hàng & Hoàn trả tài nguyên**: Luồng hủy đơn (`COD` vs `TRANSFER`), hoàn lại lượt dùng voucher / số dư `EVOUCHER` / điểm thưởng khi đơn hết hạn hoặc bị hủy.
     - **Thanh toán & Webhook bên thứ ba**: Xác thực chữ ký HMAC/JWT trên Webhook (KiotViet, VietQR, Cổng thanh toán), chống Replay Attack, dung sai số tiền (`amount_tolerance`).
     - **Bảo mật & Quyền truy cập (Auth & PII)**: Middleware phân quyền (`permission:*`, `auth:sanctum`), che mờ thông tin cá nhân (`mask PII`: SĐT, email, địa chỉ) trên các API tra cứu công khai, cấu hình `throttle` rate-limit.
     - **Đồng bộ SEO & Routing**: Đối chiếu cấu trúc URL trong `sitemap.xml` của Backend với cấu trúc route thực tế của Frontend (`src/app/`, `routing.ts`, hoặc Inertia pages).
  3. **Dò tìm Dead Code, File Mồ Côi & Asset Thừa qua AST (`graphify-out/graph.json`)**:
     - Xác định các Component, Hook, Service, Controller, Model, Helper không có bất kỳ liên kết gọi tới nào (`0 inbound edges` / `orphaned nodes`).
     - Xác định các file tĩnh (ảnh, icon, font, CSS, JS cũ, file mẫu demo) trong `public/`, `src/assets/`, `resources/` không còn được tham chiếu ở bất kỳ đâu trong code.
  4. **Phát hiện Điểm Nghẽn Làm Nặng Runtime & Payload (Runtime & Payload Bloat)**:
     - **Backend**:
       - Truy vấn N+1 (thiếu eager loading `with()`).
       - Lấy toàn bộ dữ liệu không giới hạn/không phân trang (`Model::all()`, `->get()` trên bảng giao dịch/sản phẩm lớn).
       - `SELECT *` kéo theo các cột văn bản dài (`content`, `description`, `body`, `meta_json`) ở các API danh sách (`index` / list endpoints) làm phình JSON response.
       - Thiếu `Cache::remember` cho các cấu hình hệ thống, menu, danh mục tĩnh, banner được gọi lặp lại ở mọi request.
     - **Frontend**:
       - Component nặng (Modal, Chart, Rich Text Editor, Swiper/Carousel, Map, Lightbox) được import tĩnh ngay ở trang chủ hoặc Root Layout thay vì dùng `next/dynamic` hoặc `defineAsyncComponent` (Lazy Load / Code Splitting).
       - Truyền nguyên object dữ liệu khổng lồ từ Server Component / Inertia Controller xuống Client Component làm phình HTML / RSC / Inertia payload.
       - Ảnh chưa sử dụng `next/image` (thiếu `sizes`, `priority` hợp lý) hoặc dùng ảnh PNG/JPG nặng chưa tối ưu sang WebP/AVIF.
- **Đầu ra bắt buộc**: Lập danh sách các điểm nghi vấn hardcode CMS (`HARDCODE-01`...), nghi vấn nghiệp vụ (`SUSPECT-01`...) và nghi vấn làm nặng source (`BLOAT-01`...) kèm tọa độ `file:line` vào `scratch/step2_argon_suspects.md`.

### Tầng 3: Deep Verification, Hardcode Confirmation, Bundle Analysis & Runtime Proof
- **Vai trò Subagent**: `Deep Evidence, Hardcode & Performance Verifier`
- **Hành động**:
  - Nhận danh sách nghi vấn từ `scratch/step2_argon_suspects.md` và cảnh báo từ `scratch/step1_static_scan.md`.
  1. **Kiểm chứng Danh sách Hardcode & Return Cố Định trong CMS**:
     - Mở trực tiếp từng tọa độ `File:Dòng` của các mục `HARDCODE-*` để xác minh luồng dữ liệu từ Database -> Model/Setting -> Controller -> Inertia Props / API -> Vue/React Component.
     - Xác định chính xác cách thức remove cho từng mục:
       - Nếu đã có Model / bảng DB hoặc trường `Settings` tương ứng: chuẩn bị câu truy vấn Eloquent / `settings()` động thay thế cho giá trị return cố định.
       - Nếu là hàm/biến fallback giả lập (`getFallback*`, `getDefault*`, `FALLBACK_*`, `default*` chứa dữ liệu mẫu cứng làm sai lệch dữ liệu thật của CMS): đánh dấu `[CONFIRMED CMS HARDCODE / FIXED RETURN]` cần **remove** và trả về mảng rỗng `[]` / `null` (để UI hiển thị đúng trạng thái dữ liệu thực tế trong CMS hoặc Empty State chuẩn).
  2. **Kiểm chứng Nghiệp vụ & Test Thực tế**:
     - Mở đúng các file liên quan để đọc luồng rẽ nhánh thực tế và kiểm chứng.
     - Chạy các bài test cô lập: `php artisan test --filter=<TestName>` hoặc `npx vitest run <TestFile>`.
     - Nếu phát hiện bug phức tạp, viết thêm unit/feature test chứng minh lỗi hoặc kiểm tra các kịch bản biên (Edge Cases).
  3. **Đo đạc Thực tế Kích thước Build / Bundle & Hiệu năng Runtime**:
     - Kiểm tra output thực tế của `npm run build` / `yarn build`: thống kê kích thước `First Load JS` trên từng route Next.js hoặc các chunk `> 500 kB` trên Vite/Webpack.
     - Kiểm chứng các nghi vấn Dead Code / Unused Dependency xem có được gọi động (dynamic call, `app()`, magic method, string route, dynamic import) hay thực sự là mã rác không dùng tới.
     - Đo số lượng câu query (`DB::enableQueryLog()` / test assertion) hoặc kích thước JSON response (bytes/KB) trên các API trọng yếu như trang chủ và danh sách sản phẩm.
  4. **Phân loại minh bạch từng nghi vấn thành 5 nhóm**:
     - `[CONFIRMED BUG]`: Lỗi nghiệp vụ/bảo mật thật có bằng chứng runtime/code đối chứng rõ ràng.
     - `[CONFIRMED CMS HARDCODE / FIXED RETURN]`: Hàm, method, controller, middleware, hoặc component đang hardcode và return giá trị cố định cần phải liệt kê và remove.
     - `[CONFIRMED BLOAT / DEAD CODE]`: File thừa, package thừa, bundle nặng hoặc điểm nghẽn N+1/payload phình to đã được đo đạc và xác thực 100%.
     - `[FALSE POSITIVE]`: Báo động giả (đã được bảo vệ bởi tầng khác, là enum/hằng số kỹ thuật hợp lệ hoặc được gọi động hợp lệ).
     - `[TECH DEBT]`: Mã chưa tối ưu nhưng mức độ ảnh hưởng thấp, cần ghi nhận cải thiện.
- **Đầu ra bắt buộc**: Lưu báo cáo thẩm định chi tiết kèm bảng xác nhận `[CONFIRMED CMS HARDCODE / FIXED RETURN]` và số liệu đo đạc thực tế vào `scratch/step3_opus_verified_findings.md`.

### Tầng 4: CMS Hardcode Removal, Config, Env Drift, Slimming Plan & OpenSpec Packaging
- **Vai trò Subagent**: `CMS Hardcode Removal, Config, Slimming & Packaging Worker`
- **Hành động**:
  1. **Liệt Kê & Thực Thi Remove Toàn Bộ Giá Trị Hardcode / Return Cố Định Trong CMS (`CMS Hardcoded & Fixed Return Removal`)**:
     - **Bước 1.1 — Xuất Bảng Liệt Kê Đầy Đủ (`CMS Hardcoded & Fixed Return Inventory`)**:
       - Lập bảng thống kê toàn bộ các mục `[CONFIRMED CMS HARDCODE / FIXED RETURN]` từ Tầng 3 với đầy đủ cột: `Mã (#)`, `Tầng (Backend/Frontend)`, `Tọa độ File:Dòng`, `Hàm / Method / Biến`, `Giá trị cố định đang Return / Hardcode`, `Nguồn dữ liệu CMS động thay thế (Model / Setting / Props / Empty [])`.
     - **Bước 1.2 — Tiến Hành Remove & Kết Nối Dữ Liệu Động (`Remediate & Remove Hardcoded Returns`)**:
       - Dùng `edit_file` **remove** toàn bộ các mảng/giá trị hardcode và lệnh `return` cố định đã xác nhận trong:
         - **Backend (`app/Http/Controllers/`, `app/Services/`, `app/Models/`, `app/Http/Middleware/`)**: Xóa bỏ các hàm/mảng `getFallback*()`, `getDefault*()` trả về dữ liệu giả lập cố định; kết nối trực tiếp vào Eloquent Model (`Post`, `Product`, `Category`, `Slider`, `Policy`, `Agency`, `Project`, `Service`, `Config`/`Setting`...) hoặc trả về mảng rỗng `[]` / `null` khi DB chưa có dữ liệu.
         - **Frontend (`resources/Frontend/js/`, `resources/Backend/js/`, `resources/js/`, `src/`)**: Remove các biến mảng hardcode (`FALLBACK_*`, `default*`, mock arrays trong `Pages/` & `Components/`), sửa các `computed()` hoặc `props` fallback để lấy 100% từ dữ liệu động CMS truyền xuống (`props.*`, `$page.props.settings` / `global`), hiển thị Empty State gọn sạch nếu mảng rỗng.
       - **Bước 1.3 — Kiểm Chứng Sau Khi Remove**:
         - Chạy lại `python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .` để xác nhận các điểm hardcode return cố định đã được dọn sạch.
         - Chạy build (`npm run build` / `yarn build`) và test (`php artisan test`, `phpstan`) để đảm bảo không phát sinh lỗi cú pháp hay lỗi runtime.
  2. **Rà soát Script Phá Hủy Dữ Liệu (P0 Check)**:
     - Kiểm tra `composer.json` (tuyệt đối không để `"compile"` hay `post-*` chứa `migrate:fresh` hoặc `--seed`).
     - Kiểm tra `package.json` (tách biệt lệnh build assets `vite build` / `next build` khỏi lệnh `php artisan migrate --force`).
  3. **Kiểm toán Độ lệch Biến Môi Trường (`.env Drift`)**:
     - So sánh toàn bộ lệnh gọi `env(...)` trong `config/*.php` với `.env.example` của Backend. Liệt kê mọi biến bị thiếu hoặc lỗi cú pháp.
     - So sánh toàn bộ `process.env.NEXT_PUBLIC_*` / `import.meta.env.VITE_*` trên Frontend với `.env.example` của Frontend.
     - Kiểm tra `config/cors.php` xem có bị hardcode domain ngrok/dev thay vì đọc từ `FRONTEND_URL` / `CORS_ALLOWED_ORIGINS` hay không.
  4. **Kiểm tra Cấu hình Build & Cache Giúp Giảm Tải (Build & Cache Optimization)**:
     - **Frontend**: Kiểm tra `next.config.js` / `next.config.mjs` / `vite.config.ts` (`optimizePackageImports`, `modularizeImports`, `swcMinify`, cấu hình nén ảnh `images.formats: ['image/avif', 'image/webp']`, `removeConsole` trên production, tách `manualChunks` trên Vite).
     - **Backend**: Kiểm tra cấu hình tối ưu production (`php artisan optimize`, `config:cache`, `route:cache`, `view:cache`, `event:cache`, `composer install --no-dev --optimize-autoloader`, cấu hình `OPcache` và nén `gzip`/`brotli` trên Nginx).
  5. **Lập Kế Hoạch Làm Nhẹ Source An Toàn (`Safe Source Slimming Plan`)**:
     - Bắt buộc chia rõ thành 2 nhóm để đảm bảo an toàn tuyệt đối:
       - **Nhóm A — Tối Ưu Trực Tiếp (Safe In-Place Optimization — Không xóa file)**: Remove dữ liệu hardcode return cố định trong code (giảm ngay dung lượng bundle JS/PHP), tối ưu câu query `select()` / `with()`, bổ sung `Cache::remember()`, bật lazy-load `next/dynamic` / `defineAsyncComponent`, bật `optimizePackageImports`, chuyển các gói dev (`debugbar`, `ide-helper`, `faker`, `phpstan`) từ `require` sang `require-dev`, cập nhật `.gitignore` để chặn file rác/log/backup.
       - **Nhóm B — Dọn Dẹp File / Asset / Package Thừa (Requires User Approval)**: Liệt kê chính xác đường dẫn từng file/thư mục/asset mồ côi và tên các package không sử dụng kèm **dung lượng ước tính giảm được (MB/KB)**, lập thành **Implementation Plan** để người dùng phê duyệt trước khi chạy lệnh gỡ/xóa (tuân thủ tuyệt đối `STRICT SAFE GUARD: CẤM TỰ Ý XÓA FILE`).
  6. **Đồng bộ OpenSpec Change**:
     - Khởi tạo hoặc cập nhật gói OpenSpec tại `openspec/changes/opsx-audit-handoff/` (hoặc `add-multi-model-audit-handoff`) gồm `proposal.md`, `design.md`, `tasks.md` và `specs/`.
- **Đầu ra bắt buộc**: Lưu bảng kết quả Liệt kê & Remove Hardcode CMS, báo cáo cấu hình, kế hoạch làm nhẹ source 2 nhóm & checklist triển khai vào `scratch/step4_sonnet_config_and_checklist.md`.

### Tầng 5: Lead Architect Synthesis & Executive Handoff Matrix
- **Vai trò**: `Main Agent (Lead Architect)`
- **Hành động**:
  - Đọc tổng hợp cả 4 file báo cáo trong `scratch/`.
  - Lập **Ma Trận Ưu Tiên Rủi Ro (Executive Risk Matrix)**:
    - **P0 (Critical / Blocker)**: Nguy cơ mất dữ liệu DB, rò rỉ secret/STK thật, bypass xác thực webhook, lỗi 500 làm đứt gãy luồng đặt hàng/thanh toán, file dump DB/secret nặng bị lộ trong `public/`. -> *Bắt buộc sửa ngay trước khi bàn giao.*
    - **P1 (High Priority)**: Các hàm/component trong CMS đang hardcode và return giá trị cố định làm sai lệch dữ liệu quản trị thực tế, sai lệch công thức tính voucher/freeship, không hoàn lại voucher khi hủy đơn, lộ PII khách hàng, lệch link `sitemap.xml`, lỗi N+1 hoặc bundle phình to nghiêm trọng. -> *Thực thi remove hardcode & vá lỗi có kèm test.*
    - **P2 (Medium / Polish)**: Cảnh báo PHPStan/ESLint không gây crash, tối ưu hóa cấu hình `.env.example`, dọn dead code, gom/nén asset và gỡ các thư viện thừa.
  - Lập bảng **"Báo Cáo Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal Report`)"**:
    - Liệt kê đầy đủ từng tọa độ `File:Dòng` phát hiện hardcode / return cố định trên cả Backend và Frontend.
    - Trạng thái đã **Remove** và nguồn dữ liệu động thay thế (Model/DB, Settings, Props hoặc Empty State `[]`), kèm bằng chứng chạy lại `scan-cms-hardcoded.py` và log `npm run build` / `php artisan test`.
  - Lập bảng **"Báo Cáo Tối Ưu Hóa & Giảm Tải Source (`Source Slimming & Performance Summary`)"** đặt song song với Ma trận P0/P1/P2:
    - **Dung lượng ổ đĩa / Repo có thể giảm**: Chi tiết dung lượng file rác, log, backup, asset thừa, `vendor`/`node_modules` giảm được (Trước vs Sau dự kiến).
    - **Kích thước JS / CSS Bundle giảm được**: Chi tiết `First Load JS` / chunk size giảm được nhờ remove dữ liệu hardcode trong bundle, code splitting (`dynamic`), `optimizePackageImports` và loại bỏ thư viện trùng lặp.
    - **Các API / Query đã hoặc cần giảm tải**: Danh sách endpoint giảm số câu query (khử N+1), giảm kích thước JSON payload (bỏ `SELECT *`) và áp dụng caching.
    - **Danh sách chờ Phê duyệt Xóa File Vật Lý (Approval Queue)**: Trình bày rõ các hạng mục thuộc Nhóm B để người dùng bấm phê duyệt.
  - Trình bày báo cáo tổng hợp và **Checklist Bàn Giao Vận Hành (Go-Live Checklist)** cho người dùng.

---

## 4. Đồng Bộ & Cài Đặt Cho Mọi Dự Án (`scripts/`)

### 4.1. Cài đặt Global một lần trên máy (`install-global.sh`)
Chạy lệnh sau để cài `opsx-audit-handoff` (kèm script `scan-cms-hardcoded.py`) vào `$HOME/.gemini/config/skills/opsx-audit-handoff` và `$HOME/.gemini/config/team-skill-suite/.agents`, giúp mọi đoạn hội thoại mới trên Antigravity ở bất kỳ dự án nào cũng nhận diện và sử dụng được ngay:
```bash
./.agent/skills/opsx-audit-handoff/scripts/install-global.sh
```

### 4.2. Chạy riêng công cụ Quét Hardcode & Fixed Return CMS (`scan-cms-hardcoded.py`)
Có thể chạy độc lập ở bất kỳ dự án nào để kiểm tra nhanh toàn bộ CMS:
```bash
python3 "$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py" .
```

### 4.3. Khởi tạo tại chỗ hoặc Xuất sang dự án khác (`bootstrap-audit-handoff.sh`)
1. **Cài đặt đầy đủ Workflow + Claude Command + Script + OpenSpec Template vào dự án hiện tại**:
   ```bash
   "$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh"
   ```
2. **Xuất quy trình Audit sang một dự án khác (`--export-to`)**:
   ```bash
   "$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh" --export-to /duong/dan/du-an-dich
   ```
   Script sẽ tự động sao chép an toàn (100% không xóa file, tự động cập nhật OpenSpec template chuẩn nếu chưa có task đang chạy):
   - `.agent/skills/opsx-audit-handoff/` (và `.agents/skills/opsx-audit-handoff/` nếu dự án dùng `.agents/`)
   - `.agent/workflows/opsx-audit-handoff.md` (và `.agents/workflows/opsx-audit-handoff.md`)
   - `.claude/commands/opsx/audit-handoff.md`
   - `openspec/changes/opsx-audit-handoff/` (`proposal.md`, `design.md`, `tasks.md`, `specs/multi-model-audit-handoff/spec.md`)

---

## 5. Mẫu Prompt Bàn Giao Nhanh (Copy-Paste Cho Mọi Đoạn Hội Thoại Mới)

Khi mở một đoạn hội thoại mới hoàn toàn (trên dự án này hoặc dự án khác), bạn chỉ cần gõ `/opsx-audit-handoff` hoặc dán đoạn prompt dưới đây:

```markdown
Thực hiện quy trình kiểm toán toàn diện, dọn sạch hardcode CMS & làm nhẹ source 5 tầng `/opsx-audit-handoff` (theo skill `opsx-audit-handoff`):
1. Tuân thủ tuyệt đối STRICT SAFE GUARD: Không tự ý xóa file vật lý (`rm`), không chạy lệnh phá hủy DB (`migrate:fresh`, `db:seed`), báo cáo kèm bằng chứng terminal thật 100%.
2. Nếu dự án hiện tại chưa có `.agent/workflows/opsx-audit-handoff.md` hoặc `.claude/commands/opsx/audit-handoff.md`, hãy chạy `"$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh" --export-to .` trước tiên.
3. Điều phối Subagents thực thi lần lượt 4 tầng chuyên trách:
   - Quét toàn bộ CMS (Backend Controllers/Services/Models/Middleware & Frontend Pages/Components/Layouts/Composables) bằng `scan-cms-hardcoded.py` kết hợp kiểm toán AST để phát hiện mọi vị trí đang hardcode và return các giá trị cố định -> Liệt kê chi tiết tọa độ `File:Dòng` và tiến hành REMOVE các giá trị hardcode/return cố định đó để kết nối đúng dữ liệu động của CMS (hoặc trả về `[]`/`null` chuẩn).
   - Kiểm toán nghiệp vụ, bảo mật VÀ tối ưu làm nhẹ source (Disk/Repo Bloat, Unused/Heavy Dependencies, AST Dead Code & Orphaned Assets, N+1/Payload Bloat, Bundle Size & Safe Source Slimming Plan).
   - Xuất báo cáo trung gian vào `scratch/step1_static_scan.md`, `scratch/step2_argon_suspects.md`, `scratch/step3_opus_verified_findings.md`, `scratch/step4_sonnet_config_and_checklist.md`.
4. Tổng hợp báo cáo Tầng 5 (Lead Architect) gồm: Ma trận P0/P1/P2, Bảng Liệt Kê & Kết Quả Remove Hardcode / Return Cố Định Toàn Bộ CMS, Bảng Báo Cáo Tối Ưu Hóa & Giảm Tải Source, và Checklist Bàn Giao Vận Hành.
```
