# Design Document: Multi-Model Audit, CMS Hardcode Removal, Source Slimming & Handoff Pipeline

## 1. Kiến Trúc Tổng Quan (System Architecture)

Hệ thống kiểm toán, dọn sạch dữ liệu hardcode return cố định trong CMS, tối ưu hóa làm nhẹ source và bàn giao được thiết kế theo mô hình **Pipeline 5 Tầng Phân Tách Trách Nhiệm (Separation of Concerns)**, giao tiếp phi tập trung qua các tệp trung gian trong thư mục `scratch/`:

```text
+---------------------------------------------------------------------------------+
| Tầng 1: Static, Secret, Bloat & CMS Hardcoded Return Scanner (Haiku/Flash-Lite) |
| -> Lệnh thực thi: phpstan, composer/npm audit, eslint, tsc, scan-cms-hardcoded, |
|                   du -sh, quét file > 1MB, kiểm tra package thừa / require-dev  |
| -> Output: scratch/step1_static_scan.md                                         |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 2: Graphify AST, CMS Hardcode, Cross-Module & Bloat Auditor (Argon)        |
| -> Công cụ: AST Graph (graphify-out/graph.json), kiểm toán toàn bộ CMS tìm các  |
|             hàm/component hardcode & return cố định (HARDCODE-*), đối chiếu     |
|             FE/BE API contracts, dò Orphaned Nodes, phát hiện N+1, SELECT *     |
| -> Output: scratch/step2_argon_suspects.md                                      |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 3: Deep Verification, Hardcode Proof & Bundle Verifier (Opus 4.5)          |
| -> Công cụ: Đọc code chi tiết, xác nhận [CONFIRMED CMS HARDCODE / FIXED RETURN] |
|             kèm nguồn dữ liệu động thay thế, chạy artisan test, đo npm run build|
| -> Output: scratch/step3_opus_verified_findings.md                              |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 4: CMS Hardcode Removal, Config, Env Drift & Safe Slimming Plan (Sonnet)   |
| -> Công cụ: Liệt kê & Remove toàn bộ giá trị hardcode/return cố định trong CMS, |
|             Config Diff (.env vs config/*), lập Safe Source Slimming Plan       |
| -> Output: scratch/step4_sonnet_config_and_checklist.md                         |
+---------------------------------------------------------------------------------+
                                       |
                                       v
+---------------------------------------------------------------------------------+
| Tầng 5: Lead Architect Synthesis & Handoff Sign-off (Main Agent)                |
| -> Công cụ: Ma trận ưu tiên rủi ro (P0/P1/P2), Bảng CMS Hardcoded Return        |
|             Removal Summary, Bảng Source Slimming & Performance Summary         |
| -> Output: Executive Handoff Report cho khách hàng                              |
+---------------------------------------------------------------------------------+
```

---

## 2. Thiết Kế Kiểm Toán & Loại Bỏ Hardcode / Return Cố Định Toàn Bộ CMS

1. **Cơ chế phát hiện đa tầng (`scan-cms-hardcoded.py` + AST Deep Audit)**:
   - **Backend (`app/`, `routes/`, `Modules/`, `packages/`)**: Phát hiện mọi method trong Controller, Service, Repository, Action, Model, Middleware (`HandleInertiaRequests`) đang `return` mảng dữ liệu cố định (`getDefault*`, `getFallback*`, `getContactFaqs`, `getSolutionOptions`, `default*Settings`...) hoặc chuỗi/số liệu cố định thay vì truy vấn từ Eloquent Model / `Settings`.
   - **Frontend (`resources/`, `src/`)**: Phát hiện mọi biến mảng tĩnh (`FALLBACK_*`, `default*`, mock arrays), `computed()` / getter return mảng cố định, `props` fallback che giấu trạng thái DB rỗng, và thông tin liên hệ (`tel:`, `mailto:`, địa chỉ) gán cứng trong template.
2. **Cơ chế Liệt Kê & Remove An Toàn (Non-Destructive File Edit)**:
   - Lập bảng tọa độ `File:Dòng` đầy đủ trước khi sửa.
   - Sử dụng `edit_file` để **remove** các khối dữ liệu hardcode và lệnh `return` cố định bên trong file (không xóa file vật lý bằng `rm`), kết nối về đúng nguồn dữ liệu động của CMS (Model query, `Setting`, `Inertia::share`, `props`) hoặc trả về mảng rỗng `[]` / `null` chuẩn khi DB chưa có dữ liệu.
   - Chạy lại `scan-cms-hardcoded.py`, `npm run build` và `php artisan test` để xác nhận sạch 100% và không gây lỗi hồi quy.

---

## 3. Thiết Kế Chuẩn Hóa Cấu Hình, Làm Nhẹ Source & Kịch Bản Triển Khai

1. **Đồng bộ `.env.example` (Backend & Frontend)**:
   - Mọi khóa `env()` trong `config/*.php` đều phải có mặt trong `.env.example` của Backend kèm giá trị mẫu an toàn (tuyệt đối không để lộ secret thật).
   - Mọi khóa `NEXT_PUBLIC_*` hoặc `VITE_*` trên Frontend đều phải được khai báo trong `.env.example` của Frontend.
2. **Loại bỏ Script Phá Hủy Dữ Liệu**:
   - Không đặt `migrate:fresh`, `db:seed` vào các hook tự động trong `composer.json` (`compile`, `post-install-cmd`, `post-update-cmd`).
   - Tách biệt quá trình build giao diện (`vite build` / `next build`) với quá trình chạy migration cơ sở dữ liệu (`php artisan migrate --force`).
3. **Cơ Chế Làm Nhẹ Source An Toàn 2 Nhóm (`Safe Source Slimming Plan`)**:
   - **Nhóm 1 — Tối Ưu Trực Tiếp (Không xóa file)**: Remove dữ liệu hardcode return cố định trong code, tối ưu truy vấn `select()` / `with()`, bật `Cache::remember`, áp dụng `next/dynamic` / `defineAsyncComponent`, cấu hình `optimizePackageImports` / `manualChunks`, chuyển gói dev sang `require-dev`, cập nhật `.gitignore`.
   - **Nhóm 2 — Dọn Dẹp File / Asset / Package Thừa (Cần User Phê Duyệt)**: Liệt kê chính xác đường dẫn file/asset mồ côi, file log/backup nặng và các package thừa kèm dung lượng giảm được, đóng gói thành **Implementation Plan** chờ người dùng phê duyệt trước khi xóa (tuân thủ nghiêm ngặt `STRICT SAFE GUARD: CẤM TỰ Ý XÓA FILE`).
4. **Tích hợp Đa Công Cụ**:
   - Workflow Antigravity: `.agent/workflows/opsx-audit-handoff.md`
   - Claude Code Command: `.claude/commands/opsx/audit-handoff.md`
   - Skill chuẩn: `.agent/skills/opsx-audit-handoff/SKILL.md`
   - Script quét tự động: `.agent/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py`
