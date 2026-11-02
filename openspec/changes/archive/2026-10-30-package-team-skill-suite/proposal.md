# Proposal: Đóng gói Bộ Skill Chuẩn Toàn Diện của Team (`package-team-skill-suite`)

## Why

Hiện tại các bộ kỹ năng cốt lõi (OpenSpec OPSX, SEO Suite 25 skills + 18 specialist sub-agents, kiểm soát chất lượng UI `anti-ui-slop` / `web-design-guidelines`, điều hướng đồ thị AST `graphify`, điều phối đa tác nhân `multi-agent-orchestration`, cùng quy trình dựng & kiểm toán giao diện theo Figma MCP) đang nằm phân tán trên nhiều repository và cấu hình cá nhân khác nhau, đồng thời `.gitignore` của dự án `bed-saigonchem` lại đang chặn theo dõi `.agents` và `/openspec`. Cần tập hợp, chuẩn hóa không phụ thuộc đường dẫn máy cá nhân (100% portable) và đóng gói toàn bộ thành một **Bộ Skill Chuẩn Toàn Diện của Team** ngay trong repository để mọi thành viên chỉ cần `git pull` hoặc chạy script `opsx-bootstrap` là có thể tái sử dụng tức thì trên mọi dự án mới (Laravel/Inertia/Vue maupun Next.js/React).

## What Changes

1. **Tích hợp & chuẩn hóa trọn bộ Skills + Specialist Sub-Agents trong `.agents/`** (bảo toàn nguyên vẹn 5 skills `openspec-*` và 5 workflows `opsx-*.md` hiện hữu):
   - **SEO Suite (25 skills + 18 specialist agents)**: Đóng gói đầy đủ 1 orchestrator `seo` và 24 sub-skills `seo-*` trong `.agents/skills/` kèm 18 định nghĩa sub-agents chuyên biệt `seo-*.md` trong `.agents/agents/`.
   - **UI Quality, Copywriting & AST Graph Navigation (4 skills)**: Đóng gói `anti-ui-slop` (triết lý *Quiet & High-Trust UI* kèm quy tắc cấm spam icon), `web-design-guidelines`, `writing-guidelines`, và `graphify` (hướng dẫn truy vấn đồ thị AST `graphify-out/graph.json` và `GRAPH_REPORT.md` thay cho tìm kiếm mù quáng).
   - **Multi-Agent Orchestration (1 skill + 5 references)**: Đóng gói `multi-agent-orchestration` cùng 5 hướng dẫn vai trò (`orchestrator.md`, `backend_worker.md`, `frontend_worker.md`, `tester.md`, `qa_verifier.md`) đảm bảo hoàn toàn portable.
   - **3 Skills Cốt Lõi Đa Stack mới (Production-grade)**:
     - `opsx-bootstrap`: Tự động hóa khởi tạo dự án mới hoặc xuất bộ skill chuẩn sang dự án khác (`scripts/bootstrap.sh`, `scripts/install-global.sh`, `resources/AGENTS.template.md`, `resources/openspec-config.template.yaml`).
     - `opsx-figma-workflow`: Điều phối quy trình dựng UI từ Figma MCP × OpenSpec × Graphify × Anti-UI-Slop × Playwright Visual QA theo 3 kịch bản (`greenfield-6-phases`, `home-first-incremental`, `batch-pages-sequential-qa`), hỗ trợ song song cả Laravel/Inertia/Vue 3 (chuẩn BED CMS `resources/Frontend/js` & chuẩn Laravel `resources/js`) lẫn Next.js/React (`src/app`, `src/components`).
     - `opsx-ui-audit`: Quy trình kiểm toán giao diện 2 Phase chỉ đọc (Strict Read-Only Audit) để quét sạch tàn dư Theme/Brand cũ (`<OLD_BRAND>` -> `<NEW_BRAND>`) và đối chiếu chuẩn hóa Typography với Figma MCP trước khi mở change sửa code.

2. **Chuẩn hóa cấu hình Git, OpenSpec Context, `AGENTS.md` và Đồ thị AST của dự án Saigonchem**:
   - Cập nhật `.gitignore`: Gỡ bỏ 4 dòng chặn `/openspec`, `.openspec`, `.agent`, `.agents` để Git theo dõi trọn bộ quy chuẩn và skills cho cả team; bổ sung `/graphify-out` để không đẩy kết quả sinh AST cục bộ lên Git.
   - Cập nhật `openspec/config.yaml`: Khai báo đầy đủ `context` thực tế của dự án Saigonchem (Clean Base CMS thương mại hóa chất, stack Laravel/Inertia/Vue 3.2/Vite 3/Tailwind CSS 3.2.7/PrimeVue 3/Sass/Ziggy/vue-i18n và các quy tắc an toàn bắt buộc).
   - Nâng cấp `AGENTS.md`: Bảo toàn 100% 3 nguyên tắc gốc (Strict Safe Guard, Evidence-Based 100%, Mandatory Subagent Reflex) và bổ sung Mục 4 (OpenSpec OPSX & Archive Safe Guard), Mục 5 (Quy trình dựng & kiểm toán UI từ Figma MCP), Mục 6 (Điều hướng kiến trúc Graphify & SEO Suite).
   - Chạy `graphify . --code-only` để sinh bản đồ kiến trúc thực tế `graphify-out/graph.json` cho dự án.

## Capabilities

### New Capabilities
- `team-skill-suite`: Đặc tả kiến trúc, tính khả chuyển (portability không chứa đường dẫn cá nhân), khả năng tự động khám phá của Antigravity, quy trình bootstrap dự án mới, quy trình dựng/kiểm toán UI đa stack theo Figma MCP, và cấu hình quản lý phiên bản Git cho trọn bộ Skill & Specialist Sub-Agents của Team.

### Modified Capabilities
<!-- Không có spec hiện hữu nào trong openspec/specs/ bị thay đổi yêu cầu -->

## Impact

- **Thư mục `.agents/`**: Bổ sung 33 skills mới vào `.agents/skills/` (nâng tổng số lên 38 skills khi tính cả 5 skills `openspec-*` sẵn có) và 18 specialist agents vào `.agents/agents/`; giữ nguyên 5 workflows trong `.agents/workflows/`.
- **Cấu hình dự án**: Cập nhật an toàn `.gitignore`, `openspec/config.yaml`, `AGENTS.md`, và khởi tạo thư mục cục bộ `graphify-out/` (được ignore khỏi Git).
- **Mã nguồn nghiệp vụ & Cơ sở dữ liệu**: **0 tác động** — tuyệt đối không chỉnh sửa bất kỳ file nào trong `app/`, `resources/`, `routes/`, `database/`, `config/`, và không xóa bất kỳ file hiện hữu nào.
