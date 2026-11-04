# Tasks: Multi-Model Audit, CMS Hardcode Removal, Source Slimming & Handoff Pipeline Implementation

## Phase 1: Quy trình Kiểm toán, Quét Hardcode CMS & Đo Đạc Đa Tầng (Multi-Model Audit Execution)

- [ ] **1.1 Quét tĩnh SAST, Dependency, Secret, Bloat & CMS Hardcoded Return Scan (Tầng 1 - Haiku/Flash-Lite)** <!-- id: 1.1 -->
  - Chạy Larastan/PHPStan và `composer audit` trên Backend; ESLint, `tsc --noEmit` và `npm audit` trên Frontend.
  - Chạy `python3 .agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py .` quét toàn bộ CMS phát hiện sớm các hàm/biến hardcode và return giá trị cố định.
  - Quét secret rò rỉ trong git và chuỗi cứng trong `config/`, `app/`, `src/`, `resources/`.
  - Đo dung lượng thư mục (`du -sh *`), tìm file nặng `> 1MB` (`.sql`, `.zip`, `.tar.gz`, `storage/logs/*.log`, ảnh/video chưa nén) và kiểm tra file nặng bị commit nhầm vào Git.
  - Rà soát thư viện thừa & nặng trong `composer.json` (gói dev đặt nhầm vào `require`) và `package.json` (thư viện trùng lặp, import nguyên khối, package không có lệnh `import`).
  - Ghi nhận đầy đủ vào `scratch/step1_static_scan.md`.
- [ ] **1.2 Bản đồ AST, Kiểm toán Hardcode CMS, Nghi vấn Nghiệp vụ & Dead Code / Payload Bloat (Tầng 2 - Gemini 4 Argon)** <!-- id: 1.2 -->
  - Tra cứu AST qua `graphify-out/graph.json`, đối chiếu hợp đồng API và công thức nghiệp vụ giữa Backend và Frontend.
  - Kiểm toán sâu toàn bộ CMS (Backend Controllers/Services/Models/Middleware & Frontend Pages/Components/Layouts/Stores/Composables) để liệt kê đầy đủ các điểm đang hardcode và return giá trị cố định (`HARDCODE-01`... kèm tọa độ `File:Dòng`).
  - Dò tìm Dead Code, Component/Service/Controller/Model/Helper mồ côi (`0 inbound edges`) và file tĩnh thừa trong `public/`, `src/assets/`.
  - Phát hiện điểm nghẽn làm nặng Runtime & Payload: truy vấn N+1, `::all()` không phân trang, `SELECT *` kéo cột dài, thiếu `Cache::remember`, thiếu `next/dynamic` / `defineAsyncComponent`, truyền object khổng lồ xuống Client Component, ảnh chưa tối ưu.
  - Ghi nhận đầy đủ vào `scratch/step2_argon_suspects.md`.
- [ ] **1.3 Thẩm định chuyên sâu, Xác nhận Hardcode CMS, Phân tích Bundle & Bằng chứng Runtime 100% (Tầng 3 - Opus 4.5 / Deep Verifier)** <!-- id: 1.3 -->
  - Đọc trực tiếp code nguồn tại các vị trí nghi vấn trọng yếu, xác nhận danh sách `[CONFIRMED CMS HARDCODE / FIXED RETURN]` kèm nguồn dữ liệu động thay thế, và chạy test cô lập (`php artisan test --filter=...`).
  - Đo đạc thực tế output của `npm run build` / `yarn build` (`First Load JS` hoặc chunk `> 500 kB`), số câu query và kích thước JSON response.
  - Kiểm chứng nghi vấn Dead Code / Unused Dependency xem có gọi động hay thực sự là rác (`[CONFIRMED BLOAT / DEAD CODE]`), phân biệt rõ với `[CONFIRMED BUG]` và `[FALSE POSITIVE]`.
  - Ghi nhận đầy đủ vào `scratch/step3_opus_verified_findings.md`.
- [ ] **1.4 Liệt kê & Remove Hardcode CMS, Rà soát cấu hình, Kế hoạch Làm nhẹ Source & Đóng gói OpenSpec (Tầng 4 - Sonnet 3.5/5.5)** <!-- id: 1.4 -->
  - Lập bảng liệt kê chi tiết toàn bộ vị trí `[CONFIRMED CMS HARDCODE / FIXED RETURN]` và tiến hành **remove** các giá trị hardcode/return cố định trên cả Backend & Frontend, nối vào dữ liệu động CMS (Model/DB, Settings, Props hoặc trả về `[]`/`null` chuẩn).
  - Đối chiếu toàn bộ biến môi trường giữa `config/*.php` / `src/` và `.env.example`, rà soát script nguy hiểm trong `composer.json` / `package.json`.
  - Kiểm tra cấu hình build & cache giảm tải (`optimizePackageImports`, `manualChunks`, `config:cache`, `route:cache`, `OPcache`, `gzip`).
  - Lập **Kế Hoạch Làm Nhẹ Source An Toàn (`Safe Source Slimming Plan`)** chia rõ 2 nhóm: Nhóm Tối Ưu Trực Tiếp (không xóa file) và Nhóm Dọn Dẹp File/Asset/Package Thừa (Implementation Plan chờ User phê duyệt).
  - Ghi nhận đầy đủ vào `scratch/step4_sonnet_config_and_checklist.md`.
- [ ] **1.5 Tổng hợp báo cáo điều hành, Bảng Remove Hardcode CMS, Bảng Giảm Tải Source & Phê duyệt bàn giao (Tầng 5 - Lead Architect)** <!-- id: 1.5 -->
  - Phân loại ma trận ưu tiên rủi ro (`P0 / P1 / P2`).
  - Lập bảng **"Báo Cáo Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Audit & Removal Summary`)"**.
  - Lập bảng **"Báo Cáo Tối Ưu Hóa & Giảm Tải Source (`Source Slimming & Performance Summary`)"** (Dung lượng ổ đĩa/repo giảm được, Kích thước JS/CSS bundle giảm được, Các API/Query giảm tải).
  - Lập lộ trình khắc phục an toàn (`Zero Regression`) và Checklist Bàn Giao Vận Hành.

---

## Phase 2: Khắc phục Nguy cơ Cấu hình & Script Triển khai (P0 Blockers)

- [ ] **2.1 Loại bỏ script phá hủy database trong `composer.json` và tách migration khỏi `package.json` build** <!-- id: 2.1 -->
- [ ] **2.2 Khử hardcoded credentials trong `config/` và chuyển sang đọc từ `env(..., null)`** <!-- id: 2.2 -->
- [ ] **2.3 Đồng bộ hóa hoàn chỉnh `.env.example` cho cả Backend và Frontend** <!-- id: 2.3 -->

---

## Phase 3: Loại bỏ Hardcode Return Cố định CMS, Khắc phục Nghiệp vụ & Làm nhẹ Source (P1 & Source Slimming)

- [ ] **3.1 Liệt kê đầy đủ và Remove toàn bộ hàm/mảng/biến đang hardcode và return giá trị cố định trong CMS (Backend & Frontend)** <!-- id: 3.1 -->
- [ ] **3.2 Đồng bộ công thức tính toán tài chính / chiết khấu / voucher giữa FE và BE** <!-- id: 3.2 -->
- [ ] **3.3 Rà soát xác thực chữ ký Webhook, bảo vệ PII và cấu hình Rate Limiting** <!-- id: 3.3 -->
- [ ] **3.4 Đồng bộ cấu trúc URL trong `sitemap.xml` với Frontend Routing** <!-- id: 3.4 -->
- [ ] **3.5 Thực thi Nhóm Tối Ưu Trực Tiếp (Không xóa file): Tối ưu `select()`/`with()`, `Cache::remember`, `next/dynamic`, `optimizePackageImports`, chuyển gói dev sang `require-dev`** <!-- id: 3.5 -->
- [ ] **3.6 Thực thi Nhóm Dọn Dẹp File/Asset/Package Thừa (SAU KHI được User phê duyệt Implementation Plan)** <!-- id: 3.6 -->

---

## Phase 4: Kiểm thử Tích hợp & Nghiệm thu Bàn giao (Verification & Acceptance)

- [ ] **4.1 Chạy kiểm chứng lại `scan-cms-hardcoded.py`, toàn bộ test suite Backend (`php artisan test`) & PHPStan** <!-- id: 4.1 -->
- [ ] **4.2 Chạy kiểm chứng tĩnh, test suite & đo lại Bundle Size Frontend (`tsc --noEmit`, `eslint`, `npm run build`)** <!-- id: 4.2 -->
- [ ] **4.3 Ký biên bản nghiệm thu kỹ thuật và bàn giao (`FINAL SIGN-OFF`)** <!-- id: 4.3 -->
