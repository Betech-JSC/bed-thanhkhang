# Proposal: Standardize Multi-Model Audit, CMS Hardcode Removal, Source Slimming & Handoff Pipeline

## Why (Bối cảnh & Vấn đề)

Dự án đang trong giai đoạn chuẩn bị bàn giao vận hành thực tế hoặc kiểm toán sau đợt phát triển lớn. Các rủi ro hệ thống, tình trạng dữ liệu CMS bị hardcode cố định và vấn đề phình to mã nguồn thường gặp cần được kiểm soát chặt chẽ:

1. **Tình trạng Hardcode & Return Giá Trị Cố Định Toàn Bộ CMS (`CMS Hardcoded & Fixed Return Values`)**:
   - Các Controller, Service, Model, Middleware (`HandleInertiaRequests`) trên Backend hoặc các Page, Component, Layout, Composable trên Frontend vẫn đang hardcode và `return` các mảng dữ liệu cố định (`getDefault*()`, `getFallback*()`, `FALLBACK_*`, `default*`, câu hỏi FAQ, danh sách sản phẩm/bài viết/đại lý/chính sách giả lập, số liệu thống kê giả, thông tin liên hệ cứng).
   - Hệ quả: Khi quản trị viên cập nhật hoặc xóa dữ liệu trong CMS Admin, ngoài giao diện người dùng vẫn hiển thị dữ liệu cứng không thay đổi, gây sai lệch dữ liệu nghiêm trọng khi bàn giao.
2. **Rủi ro phá hủy Database từ script cấu hình (`composer.json` / `package.json`)**:
   - Các lệnh tự động như `"compile": ["@php artisan migrate:fresh --seed"]` trong `composer.json` hoặc gộp `php artisan migrate --force` vào script `npm run build` có nguy cơ làm mất dữ liệu hoặc gây deadlock khi deploy.
3. **Sai lệch cấu hình môi trường (`.env` Drift)**:
   - File `.env.example` của Backend và Frontend bị thiếu các biến cấu hình thực tế sử dụng trong `config/*.php`, `src/` hoặc `resources/`.
   - Cấu hình CORS hoặc dịch vụ thanh toán/bên thứ ba còn chứa domain dev/ngrok hoặc thông tin nhạy cảm fallback cứng.
4. **Mâu thuẫn logic nghiệp vụ xuyên module (FE <-> BE)**:
   - Sai lệch công thức tính chiết khấu giỏ hàng, phí vận chuyển, voucher / e-voucher.
   - Rò rỉ thông tin cá nhân (PII) trên API công khai, thiếu xác thực chữ ký HMAC cho Webhook, hoặc lệch URL trong `sitemap.xml`.
5. **Source dự án quá nặng, Bundle phình to & Điểm nghẽn hiệu năng (Source & Runtime Bloat)**:
   - Tồn đọng file nặng (`> 1MB`: `.sql`, `.zip`, `.tar.gz`, log phình to, ảnh/video chưa nén), dead code, component/service mồ côi (`0 inbound edges` trên AST) và package thừa hoặc đặt nhầm gói dev (`debugbar`, `ide-helper`, `faker`, `phpstan`) vào `require`.
   - Truy vấn N+1, `SELECT *` kéo cột văn bản dài ở API danh sách, thiếu `Cache::remember`, hoặc import tĩnh các component khổng lồ làm tăng kích thước `First Load JS` / chunk size.

---

## What (Giải pháp)

Triển khai quy trình kiểm toán, dọn sạch hardcode CMS & tối ưu làm nhẹ source 5 tầng (`opsx-audit-handoff`):
1. **Tầng 1 (Static, Dependency, Secret, Bloat & CMS Hardcoded Return Scanner)**: Quét tĩnh nhanh (PHPStan, ESLint, TypeScript, Composer/NPM audit, Secret scan), chạy công cụ `scan-cms-hardcoded.py` quét toàn bộ CMS phát hiện hardcode return cố định, kết hợp đo dung lượng ổ đĩa/repo (`du -sh *`, file `> 1MB`) và rà soát thư viện thừa/nặng -> `scratch/step1_static_scan.md`.
2. **Tầng 2 (Cross-Module AST, CMS Hardcode Audit, Logic & Dead Code / Payload Auditor)**: Định vị qua AST Graph (`graphify-out/graph.json`), kiểm toán sâu toàn bộ CMS (Backend & Frontend) để liệt kê mọi hàm/component đang hardcode & return giá trị cố định (`HARDCODE-*`), đối chiếu hợp đồng API FE/BE, tìm Dead Code / Orphaned Nodes / Asset thừa và phát hiện điểm nghẽn N+1, `SELECT *`, thiếu Lazy Load -> `scratch/step2_argon_suspects.md`.
3. **Tầng 3 (Deep Verification, Hardcode Confirmation, Bundle Analysis & Runtime Proof)**: Thẩm định sâu với nguyên tắc bằng chứng 100% (xác nhận danh sách `[CONFIRMED CMS HARDCODE / FIXED RETURN]` kèm nguồn dữ liệu động thay thế, test runner output thật, đo kích thước `npm run build` / chunk size, kiểm chứng gọi động trước khi kết luận `[CONFIRMED BLOAT / DEAD CODE]`) -> `scratch/step3_opus_verified_findings.md`.
4. **Tầng 4 (CMS Hardcode Removal, Config, Env Drift, Safe Slimming Plan & OpenSpec Worker)**: Liệt kê chi tiết và tiến hành **remove** toàn bộ các giá trị hardcode / return cố định trong CMS để kết nối đúng dữ liệu động (Model/DB, Settings, Props hoặc `[]`), chuẩn hóa `.env.example`, rà soát cấu hình build/cache giảm tải, lập **Kế Hoạch Làm Nhẹ Source An Toàn (`Safe Source Slimming Plan`)** chia 2 nhóm -> `scratch/step4_sonnet_config_and_checklist.md`.
5. **Tầng 5 (Lead Architect)**: Tổng hợp ma trận rủi ro P0/P1/P2, bảng **Báo Cáo Kiểm Toán & Loại Bỏ Giá Trị Hardcode / Return Cố Định Toàn Bộ CMS**, bảng **Báo Cáo Tối Ưu Hóa & Giảm Tải Source (Source Slimming & Performance Summary)** và ký duyệt bàn giao.

---

## Impact (Tác động Dự án)

- **Dữ liệu CMS 100% Động & Trung Thực**: Triệt tiêu hoàn toàn các mảng/giá trị hardcode return cố định trong Backend & Frontend; mọi thay đổi từ CMS Admin phản ánh chính xác ngay lập tức ngoài giao diện.
- **An toàn tuyệt đối**: Triệt tiêu nguy cơ mất dữ liệu production do script tự động và ngăn chặn rủi ro xóa nhầm file nhờ cơ chế tách bạch 2 nhóm trong `Safe Source Slimming Plan`.
- **Source gọn nhẹ & Tốc độ vượt trội**: Giảm mạnh dung lượng ổ đĩa/repo, thu nhỏ kích thước JS/CSS bundle (nhờ loại bỏ cả khối dữ liệu mock cứng trong bundle) và tối ưu tốc độ phản hồi API.
- **Tính sẵn sàng bàn giao**: Đội ngũ DevOps/Khách hàng có thể sao chép `.env.example` và triển khai ngay lập tức kèm bằng chứng runtime 100%.
