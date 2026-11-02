# Tasks: Đóng gói Bộ Skill Chuẩn Toàn Diện của Team (`package-team-skill-suite`)

## 1. Gom & Chuẩn hóa SEO Suite, UI Quality, AST Graph và Multi-Agent Skills

- [x] 1.1 Sao chép nguyên vẹn 25 skills thuộc SEO Suite (`seo` + 24 `seo-*`, bao gồm toàn bộ `references/`, `scripts/`, `templates/` bên trong) vào `.agents/skills/` và 18 specialist sub-agents (`seo-*.md`) vào `.agents/agents/`, xác minh đủ 25 thư mục skill SEO và 18 file agent.
- [x] 1.2 Sao chép và chuẩn hóa 4 skills chất lượng UI/copywriting/AST (`anti-ui-slop`, `web-design-guidelines`, `writing-guidelines`, `graphify`) vào `.agents/skills/`, đảm bảo `anti-ui-slop` giữ nguyên mục cấm spam icon tiếng Việt và `graphify/SKILL.md` hướng dẫn đầy đủ cách đọc `graphify-out/graph.json`, `GRAPH_REPORT.md` và cập nhật bằng `graphify . --code-only`.
- [x] 1.3 Sao chép trọn bộ skill `multi-agent-orchestration` (gồm `SKILL.md` và 5 file trong `references/`) vào `.agents/skills/multi-agent-orchestration/` và xác minh không chứa đường dẫn cá nhân.

## 2. Xây dựng 3 Skills Cốt Lõi Đa Stack mới (`opsx-bootstrap`, `opsx-figma-workflow`, `opsx-ui-audit`)

- [x] 2.1 Tạo `.agents/skills/opsx-bootstrap/` gồm `SKILL.md`, `scripts/bootstrap.sh` (`chmod +x`, hỗ trợ cả chế độ chạy tại chỗ và `--export-to <target>`), `scripts/install-global.sh` (`chmod +x`), `resources/AGENTS.template.md` và `resources/openspec-config.template.yaml`; kiểm tra cú pháp shell bằng `bash -n`.
- [x] 2.2 Tạo `.agents/skills/opsx-figma-workflow/` gồm `SKILL.md` (định tuyến 3 kịch bản, quy tắc đọc `graphify-out/graph.json`, tách `nodeId` chống tràn token, ánh xạ thư mục đa stack Laravel/BED CMS & Next.js/React) cùng 3 tài liệu `references/greenfield-6-phases.md`, `references/home-first-incremental.md`, `references/batch-pages-sequential-qa.md`; xác minh YAML frontmatter và liên kết file đầy đủ.
- [x] 2.3 Tạo `.agents/skills/opsx-ui-audit/` gồm `SKILL.md` (kiểm toán chỉ đọc tham số hóa `<OLD_BRAND>` -> `<NEW_BRAND>`) cùng `references/phase1-legacy-ui-audit.md` (5 nhóm tàn dư UI cũ + % độ sạch + bảng tọa độ `File:Dòng`) và `references/phase2-typography-audit.md` (4 nhóm đối chiếu Figma Typography + bảng chuẩn & ma trận lệch chuẩn `File:Dòng`); xác minh YAML frontmatter và cấu trúc mẫu báo cáo.

## 3. Cập nhật Cấu hình Dự án Saigonchem & Khởi tạo Đồ thị AST Graphify

- [x] 3.1 Cập nhật `.gitignore`: gỡ bỏ 4 dòng chặn `/openspec`, `.openspec`, `.agent`, `.agents`, bổ sung `/graphify-out`, bảo toàn toàn bộ các dòng còn lại và xác minh qua `git check-ignore`.
- [x] 3.2 Cập nhật `openspec/config.yaml` với `schema: spec-driven` và `context:` thực tế của dự án Saigonchem (đối chiếu từ `composer.json` và `package.json`), xác minh đọc hợp lệ qua `openspec status --change "package-team-skill-suite" --json`.
- [x] 3.3 Nâng cấp `AGENTS.md`: bảo toàn nguyên vẹn 3 mục gốc (Strict Safe Guard, Evidence-Based 100%, Mandatory Subagent Reflex) và bổ sung súc tích Mục 4 (OpenSpec OPSX & Archive Safe Guard), Mục 5 (Quy trình dựng & kiểm toán UI từ Figma MCP), Mục 6 (Điều hướng kiến trúc Graphify & Bộ 24+1 Skills SEO).
- [x] 3.4 Chạy `graphify . --code-only` tại thư mục gốc dự án để sinh bản đồ AST thực tế `graphify-out/graph.json` và xác minh file JSON tồn tại, không lọt vào `git status`.

## 4. Kiểm chứng Tích hợp Toàn diện (Portability, Frontmatter & OpenSpec Validation)

- [x] 4.1 Quét kiểm tra toàn bộ `.agents/`, `openspec/` và `AGENTS.md` để xác nhận 0 đường dẫn người dùng cá nhân bị hardcode, kiểm tra 100% `SKILL.md` (38 skills) có YAML frontmatter hợp lệ và 5 skills `openspec-*` cùng 5 workflows `opsx-*.md` gốc được bảo toàn nguyên vẹn.
- [x] 4.2 Chạy thử `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh` ngay trên workspace hiện tại cùng `openspec validate "package-team-skill-suite"` và `openspec status --change "package-team-skill-suite" --json`, đảm bảo tất cả đều PASS với exit code 0.
