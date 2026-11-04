# Spec Delta: multi-model-audit-handoff

## Purpose

Thiết lập quy chuẩn kỹ thuật cho quy trình kiểm toán toàn diện 5 tầng (Multi-Model Audit Pipeline), kiểm toán & loại bỏ toàn bộ các giá trị hardcode và return cố định trong CMS (CMS Hardcoded & Fixed Return Audit & Removal), kiểm toán tối ưu hóa & làm nhẹ mã nguồn (Source Slimming, Bundle Size & Performance Optimization Audit), tiêu chuẩn bằng chứng thực thi 100% (Evidence-Based), đồng bộ hóa cấu hình môi trường (`.env.example`), triệt tiêu các script phá hủy cơ sở dữ liệu và đóng gói bàn giao hệ thống an toàn.

---

## ADDED Requirements

### Requirement: 5-Tier Multi-Model Collaborative Audit & Source Slimming Execution
Hệ thống BẮT BUỘC (MUST) phân chia quy trình kiểm toán, dọn hardcode CMS và làm nhẹ source thành 5 tầng độc lập với vai trò chuyên biệt:
1. Tầng 1 (Static, Dependency, Secret, Bloat & CMS Hardcoded Return Scanner): Quét tĩnh cú pháp, linter, typecheck, audit dependencies, quét secret, chạy `scan-cms-hardcoded.py` quét toàn bộ CMS phát hiện hardcode return cố định, đo dung lượng thư mục/file nặng (`> 1MB`) và phát hiện thư viện thừa/nặng.
2. Tầng 2 (AST Cross-Module, CMS Hardcode Audit & Dead Code / Payload Auditor): Truy vết hợp đồng dữ liệu giữa Backend và Frontend bằng AST Graph (`graphify-out/graph.json`), kiểm toán sâu toàn bộ CMS để liệt kê các hàm/component đang hardcode & return giá trị cố định (`HARDCODE-*`), tìm Dead Code / Orphaned Nodes / Asset thừa và phát hiện điểm nghẽn N+1, `SELECT *`, thiếu Lazy Load.
3. Tầng 3 (Deep Verification, Hardcode Confirmation, Bundle Analysis & Runtime Verifier): Thẩm định chuyên sâu bằng chứng thực thi thực tế, xác nhận danh sách `[CONFIRMED CMS HARDCODE / FIXED RETURN]` kèm nguồn dữ liệu động thay thế, đo kích thước build/bundle (`npm run build`), phân loại lỗi thật (`[CONFIRMED BUG]`), phình source (`[CONFIRMED BLOAT / DEAD CODE]`) và cảnh báo giả (`[FALSE POSITIVE]`).
4. Tầng 4 (CMS Hardcode Removal, Config, Env Drift, Safe Slimming Plan & OpenSpec Worker): Liệt kê đầy đủ và thực thi **remove** các giá trị hardcode/return cố định trong CMS, rà soát cấu hình, kiểm tra build/cache giảm tải, lập `Safe Source Slimming Plan` 2 nhóm, đồng bộ template môi trường và đóng gói OpenSpec.
5. Tầng 5 (Lead Architect): Tổng hợp báo cáo điều hành, ma trận ưu tiên bàn giao P0/P1/P2, bảng `CMS Hardcoded & Fixed Return Audit & Removal Summary` và bảng `Source Slimming & Performance Summary`.

#### Scenario: Kích hoạt quy trình kiểm toán tự động
- **WHEN** Quản trị viên hoặc Agent kích hoạt lệnh `/opsx-audit-handoff` hoặc `/opsx:audit-handoff`.
- **THEN** Quy trình tự động phân bổ công việc theo đúng 5 tầng, không thực hiện dồn toàn bộ tác vụ vào một mô hình duy nhất.

#### Scenario: Giao tiếp dữ liệu an toàn giữa các tầng kiểm toán
- **WHEN** Một tầng kiểm toán hoàn thành phần việc của mình.
- **THEN** Kết quả BẮT BUỘC được lưu dưới dạng file markdown chuẩn trong thư mục `scratch/` (`step1_static_scan.md`, `step2_argon_suspects.md`, `step3_opus_verified_findings.md`, `step4_sonnet_config_and_checklist.md`) để tầng tiếp theo kế thừa mà không làm tràn context window.

---

### Requirement: CMS Hardcoded & Fixed Return Values Audit, Inventory & Removal
Hệ thống BẮT BUỘC (MUST) kiểm tra toàn bộ hệ thống CMS (cả Backend lẫn Frontend) để phát hiện mọi vị trí đang hardcode dữ liệu và `return` các giá trị cố định, liệt kê đầy đủ tọa độ `File:Dòng` và tiến hành remove để kết nối đúng nguồn dữ liệu động của CMS.

#### Scenario: Quét và liệt kê toàn bộ các điểm Hardcode & Return cố định trong CMS
- **WHEN** Thực thi kiểm toán tại Tầng 1, Tầng 2 và Tầng 3.
- **THEN** Hệ thống phải chạy `scan-cms-hardcoded.py` kết hợp kiểm toán AST trên toàn bộ Backend (`Controllers`, `Services`, `Repositories`, `Models`, `HandleInertiaRequests`, `routes/`) và Frontend (`Pages`, `Components`, `Layouts`, `Stores`, `Composables`) để phát hiện và liệt kê chi tiết mọi hàm/method/biến đang hardcode và return mảng/object/số liệu cố định (`getDefault*`, `getFallback*`, `FALLBACK_*`, `default*`, câu hỏi FAQ, danh sách sản phẩm/bài viết/đại lý/chính sách giả lập, thông tin liên hệ cứng) kèm tọa độ `File:Dòng`.

#### Scenario: Thực thi Remove các giá trị Hardcode / Return cố định và kết nối dữ liệu động CMS
- **WHEN** Các điểm `[CONFIRMED CMS HARDCODE / FIXED RETURN]` đã được liệt kê và xác thực.
- **THEN** Hệ thống BẮT BUỘC tiến hành remove các khối dữ liệu hardcode và lệnh `return` cố định đó bằng công cụ chỉnh sửa file an toàn (`edit_file`, không xóa file vật lý bằng `rm`), kết nối về đúng nguồn dữ liệu động từ Database / Eloquent Model / CMS `Settings` / Inertia `props` (hoặc trả về mảng rỗng `[]` / `null` chuẩn khi DB chưa có dữ liệu), sau đó chạy lại `scan-cms-hardcoded.py`, `npm run build` và `php artisan test` để chứng minh hoàn tất.

---

### Requirement: Source Optimization, Bundle Analysis & Safe Source Slimming Plan
Hệ thống BẮT BUỘC (MUST) kiểm toán toàn diện các nguyên nhân làm phình to dung lượng ổ đĩa/repo, kích thước JS/CSS bundle và payload API, đồng thời lập kế hoạch làm nhẹ source tuân thủ tuyệt đối nguyên tắc không tự ý xóa file (`STRICT SAFE GUARD`).

#### Scenario: Phát hiện và thẩm định Bloat / Dead Code qua 3 tầng đầu
- **WHEN** Quá trình kiểm toán Tầng 1, Tầng 2 và Tầng 3 diễn ra.
- **THEN** Hệ thống phải đo đạc dung lượng thư mục (`du -sh`), phát hiện file nặng `> 1MB`, kiểm tra gói dev đặt nhầm vào `require`, truy vết các node có `0 inbound edges` trên `graphify-out/graph.json`, phát hiện truy vấn N+1 / `SELECT *` / thiếu `next/dynamic`, và xác thực qua output `npm run build` trước khi gắn nhãn `[CONFIRMED BLOAT / DEAD CODE]`.

#### Scenario: Phân tách 2 nhóm trong Safe Source Slimming Plan ở Tầng 4 & Tầng 5
- **WHEN** Đề xuất làm nhẹ source được lập tại Tầng 4 và tổng hợp tại Tầng 5.
- **THEN** Kế hoạch BẮT BUỘC phải tách làm 2 nhóm rõ ràng:
  1. **Nhóm Tối Ưu Trực Tiếp (Không xóa file)**: Remove dữ liệu hardcode return cố định trong code, tối ưu `select()`/`with()`, `Cache::remember`, `next/dynamic`, `optimizePackageImports`, chuyển gói dev sang `require-dev`, cập nhật `.gitignore`.
  2. **Nhóm Dọn Dẹp File/Asset/Package Thừa (Cần User Phê Duyệt)**: Liệt kê chính xác đường dẫn file/package thừa kèm dung lượng giảm được trong **Implementation Plan** và tuyệt đối không xóa bất kỳ file nào khi chưa có sự phê duyệt rõ ràng từ người dùng.

---

### Requirement: Zero-Assumption & Evidence-Based Verification Standard
Mọi kết luận về lỗi nghiệp vụ, dữ liệu hardcode return cố định, lỗi runtime, lỗ hổng bảo mật hay phình to dung lượng BẮT BUỘC (MUST) có bằng chứng xác thực từ kết quả thực thi công cụ hoặc mã nguồn cụ thể. Tuyệt đối KHÔNG ĐƯỢC sử dụng các kết luận phỏng đoán cảm tính.

#### Scenario: Phê duyệt một phát hiện lỗi, điểm hardcode CMS hoặc điểm nghẽn dung lượng/hiệu năng
- **WHEN** Một nghi vấn được phân loại là `[CONFIRMED BUG]`, `[CONFIRMED CMS HARDCODE / FIXED RETURN]` hoặc `[CONFIRMED BLOAT / DEAD CODE]`.
- **THEN** Báo cáo BẮT BUỘC đính kèm output thực tế từ terminal test runner (`php artisan test`, `npm test`, `vitest`), output `scan-cms-hardcoded.py`, log build (`npm run build`), thống kê dung lượng (`du -sh`), hoặc kịch bản PoC đối chiếu trực tiếp trên dòng code.

#### Scenario: Phân loại cảnh báo giả (False Positive)
- **WHEN** Công cụ quét tĩnh (PHPStan, ESLint, AST Dead Code detector, Hardcode scanner) đưa ra cảnh báo nhưng logic thực tế được bảo vệ bởi middleware, database check, là hằng số kỹ thuật hợp lệ hoặc được gọi động (dynamic call / magic method).
- **THEN** Hệ thống ghi nhận rõ lý do loại trừ kèm đường dẫn file và dòng code minh chứng.

---

### Requirement: Environment Configuration Synchronization & Integrity
Hệ thống BẮT BUỘC (MUST) cung cấp file mẫu `.env.example` đầy đủ, chính xác và đồng bộ 100% với toàn bộ biến môi trường được gọi trong code và thư mục `config/`.

#### Scenario: Khởi tạo môi trường mới cho Backend & Frontend
- **WHEN** Lập trình viên sao chép `.env.example` thành `.env` trên môi trường mới.
- **THEN** File chứa đầy đủ các biến cấu hình cần thiết, không có lỗi cú pháp, và không chứa bất kỳ secret/khóa thật nào của nhà phát triển.

---

### Requirement: Prevention of Destructive Automated Database Operations
Cơ sở dữ liệu BẮT BUỘC (MUST) được bảo vệ an toàn tuyệt đối trước mọi nguy cơ xóa sạch dữ liệu do các script tự động gây ra.

#### Scenario: Triển khai ứng dụng qua trình đóng gói PaaS hoặc CI/CD
- **WHEN** Ứng dụng được build và deploy trên môi trường PaaS hoặc CI/CD pipeline.
- **THEN** Không có bất kỳ script tự động nào kích hoạt `migrate:fresh`, `db:seed`, hoặc `drop table`.

---

### Requirement: Cross-Model Workflow & CLI Tooling Standardization
Dự án BẮT BUỘC (MUST) hỗ trợ tương thích hai chiều giữa hệ sinh thái Google Antigravity và Claude Code.

#### Scenario: Thực hiện kiểm toán trên Google Antigravity & Claude Code CLI
- **WHEN** Nhà phát triển sử dụng Antigravity IDE hoặc Claude Code CLI.
- **THEN** Workflow `.agent/workflows/opsx-audit-handoff.md` và lệnh `.claude/commands/opsx/audit-handoff.md` sẵn sàng hoạt động nhất quán.
