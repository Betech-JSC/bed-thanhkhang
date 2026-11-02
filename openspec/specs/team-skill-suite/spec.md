# team-skill-suite Specification

## Purpose

Cung cấp bộ kỹ năng (skills), tác nhân chuyên biệt (specialist sub-agents), quy tắc dự án (`AGENTS.md`, `openspec/config.yaml`) và công cụ tự động khởi tạo (`opsx-bootstrap`) chuẩn hóa, không phụ thuộc đường dẫn máy cá nhân, được quản lý phiên bản trong Git để toàn bộ thành viên trong team khám phá tự động trên Antigravity và tái sử dụng nhất quán sang mọi dự án mới.

## Requirements

### Requirement: Khả năng tự động khám phá trọn bộ Skills và Specialist Sub-Agents trên Antigravity
Hệ thống SHALL tổ chức toàn bộ skills trong `.agents/skills/<skill-name>/SKILL.md` (có YAML frontmatter hợp lệ chứa `name` và `description`) và các specialist sub-agents trong `.agents/agents/*.md`, đồng thời bảo toàn nguyên vẹn 5 skills `openspec-*` và 5 workflows `opsx-*.md` hiện hữu.

#### Scenario: Khám phá đầy đủ 38 skills và 18 specialist sub-agents trong workspace
- **WHEN** thành viên trong team mở dự án bằng Antigravity IDE sau khi đồng bộ mã nguồn
- **THEN** Antigravity tự động nhận diện đầy đủ 5 skills `openspec-*`, 25 skills thuộc SEO Suite (`seo` + 24 `seo-*`), 4 skills chất lượng UI/copywriting/AST (`anti-ui-slop`, `web-design-guidelines`, `writing-guidelines`, `graphify`), 1 skill điều phối đa tác nhân (`multi-agent-orchestration`), 3 skills quy trình cốt lõi mới (`opsx-bootstrap`, `opsx-figma-workflow`, `opsx-ui-audit`), cùng 18 specialist sub-agents `seo-*.md` và 5 workflows `opsx-*.md`

### Requirement: Tính khả chuyển tuyệt đối (Zero Personal Path Hardcoding)
Toàn bộ nội dung tài liệu, định nghĩa skill, agent, template và shell script trong `.agents/`, `openspec/` và `AGENTS.md` SHALL NOT chứa bất kỳ đường dẫn tuyệt đối gắn với tên người dùng cục bộ nào; mọi tham chiếu thư mục người dùng hoặc dự án MUST sử dụng biến môi trường chuẩn (`$HOME`) hoặc đường dẫn tương đối tính từ gốc dự án.

#### Scenario: Quét kiểm tra đường dẫn cá nhân trước khi chia sẻ cho team
- **WHEN** chạy kiểm tra tìm kiếm chuỗi đường dẫn thư mục người dùng cá nhân trên toàn bộ `.agents/`, `openspec/` và `AGENTS.md`
- **THEN** kết quả trả về 0 dòng khớp, đảm bảo script và tài liệu vận hành ổn định trên máy của bất kỳ thành viên nào trong team

### Requirement: Tự động hóa khởi tạo và nhân bản bộ skill qua `opsx-bootstrap`
Hệ thống SHALL cung cấp skill `opsx-bootstrap` kèm script thực thi an toàn, idempotent hỗ trợ cả 2 chế độ: khởi tạo ngay tại dự án hiện tại và xuất nguyên bộ skill sang một dự án mới (`--export-to <target_project_path>`), tự nhận diện tech stack, thiết lập OpenSpec, chạy `graphify . --code-only` (hoặc hướng dẫn cài đặt rõ ràng không gây crash nếu máy chưa cài `graphify`), kiểm tra `.gitignore` và tuyệt đối không ghi đè bừa bãi `AGENTS.md` đã tồn tại ở dự án đích.

#### Scenario: Chạy bootstrap trực tiếp trên dự án hiện tại
- **WHEN** thành viên thực thi `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh` tại thư mục gốc dự án
- **THEN** script nhận diện đúng tech stack từ `composer.json` / `package.json`, đồng bộ cấu hình OpenSpec, kích hoạt `graphify . --code-only` để cập nhật `graphify-out/graph.json` và xác nhận `.gitignore` đã loại trừ `/graphify-out` nhưng không chặn `.agents` hay `/openspec`

#### Scenario: Xuất bộ skill chuẩn sang một thư mục dự án mới tinh
- **WHEN** thành viên thực thi `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --export-to <target_project_path>`
- **THEN** script sao chép trọn bộ `.agents/skills/` và `.agents/agents/` sang dự án đích, chỉ khởi tạo `AGENTS.md` từ template nếu dự án đích chưa có sẵn `AGENTS.md`, và chạy quy trình bootstrap trên dự án đích mà không xóa bất kỳ file hiện hữu nào

#### Scenario: Máy đồng đội chưa cài đặt binary `graphify`
- **WHEN** script `bootstrap.sh` chạy trên máy chưa có `graphify` trong `PATH` hoặc `$HOME/.local/bin/graphify`
- **THEN** script không bị crash (exit code 0), in thông báo hướng dẫn cài đặt `uv tool install graphifyy` rõ ràng và hoàn tất các bước kiểm tra OpenSpec/Git còn lại

### Requirement: Điều phối dựng giao diện đa stack từ Figma MCP theo Phase (`opsx-figma-workflow`)
Hệ thống SHALL cung cấp skill `opsx-figma-workflow` định tuyến rõ 3 kịch bản dựng UI từ Figma MCP (`greenfield-6-phases`, `home-first-incremental`, `batch-pages-sequential-qa`), bắt buộc truy vấn `graphify-out/graph.json` trước khi tạo component mới, tách nhỏ `nodeId` theo từng section khi trang dài để chống tràn token, tuân thủ `anti-ui-slop`, xác minh hiển thị qua Playwright MCP (`QA Verifier`) trên cả Desktop lẫn Mobile, và hỗ trợ ánh xạ thư mục cho cả Laravel + Inertia + Vue 3 lẫn Next.js / React.

#### Scenario: Dựng trang hoặc component mới từ thiết kế Figma trên dự án Laravel/Vue hoặc Next.js/React
- **WHEN** agent thực thi quy trình `opsx-figma-workflow` để chuyển đổi thiết kế từ Figma MCP sang code giao diện
- **THEN** agent đối chiếu `graphify-out/graph.json` để tái sử dụng hoặc mở rộng component sẵn có, ánh xạ đúng cấu trúc thư mục theo stack thực tế của dự án, cập nhật lại `graphify . --code-only` sau khi dựng xong và kiểm chứng giao diện thật bằng Playwright MCP trước khi đóng change

### Requirement: Kiểm toán giao diện 2 Phase chỉ đọc (`opsx-ui-audit`)
Hệ thống SHALL cung cấp skill `opsx-ui-audit` thực thi kiểm toán chỉ đọc (Strict Read-Only Audit) theo tham số hóa `<OLD_BRAND>` -> `<NEW_BRAND>` gồm Phase 1 (quét 5 nhóm tàn dư Theme/UI cũ kèm tỷ lệ % độ sạch từng trang) và Phase 2 (đối chiếu 4 nhóm lệch chuẩn Typography so với Figma MCP `get_figma_data`) với tọa độ chính xác `File:Dòng` trước khi đề xuất bất kỳ thay đổi mã nguồn nào.

#### Scenario: Kiểm toán giao diện dự án chuyển đổi thương hiệu từ nền tảng cũ sang thiết kế Figma mới
- **WHEN** người dùng yêu cầu chạy `opsx-ui-audit` với cặp thương hiệu `<OLD_BRAND>` -> `<NEW_BRAND>` và liên kết Figma
- **THEN** agent giữ nguyên 100% mã nguồn (chỉ đọc), xuất báo cáo tàn dư UI cũ theo 5 nhóm kèm % độ sạch và ma trận lệch chuẩn Typography theo 4 nhóm có đầy đủ tọa độ `File:Dòng` để làm đầu vào cho `/opsx-propose`

### Requirement: Quản lý phiên bản Git cho `.agents/` và `openspec/` đồng thời cách ly kết quả sinh AST cục bộ
Dự án SHALL theo dõi thư mục `.agents/` và `openspec/` trong Git (không bị chặn bởi `.gitignore`), loại trừ `/graphify-out` khỏi Git, và duy trì đầy đủ ngữ cảnh dự án thực tế trong `openspec/config.yaml` cùng các quy tắc điều phối trong `AGENTS.md` mà không làm mất 3 nguyên tắc an toàn gốc.

#### Scenario: Kiểm tra trạng thái Git sau khi đóng gói bộ skill và chạy Graphify
- **WHEN** chạy `graphify . --code-only` và kiểm tra `git status` tại dự án
- **THEN** các file trong `.agents/`, `openspec/` và `AGENTS.md` hiển thị sẵn sàng để commit/push cho team, trong khi thư mục `graphify-out/` tồn tại trên đĩa chứa `graph.json` nhưng hoàn toàn không xuất hiện trong danh sách untracked của Git
