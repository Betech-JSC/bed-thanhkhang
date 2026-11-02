# Design: Kiến trúc Đóng gói Bộ Skill Chuẩn Toàn Diện của Team (`package-team-skill-suite`)

## Context

Xem `proposal.md` để nắm bối cảnh và mục tiêu tổng thể. Hiện tại workspace `bed-saigonchem` đã khởi tạo khung OpenSpec (`schema: spec-driven`) với 5 skills `openspec-*` trong `.agents/skills/` và 5 workflows `opsx-*.md` trong `.agents/workflows/`, nhưng:
1. `.gitignore` đang chặn `/openspec`, `.openspec`, `.agent`, `.agents` khiến cấu hình và skills không thể chia sẻ qua Git.
2. `openspec/config.yaml` đang ở trạng thái mặc định (chưa khai báo `context` thực tế của dự án Saigonchem).
3. Các bộ skills chuyên sâu (SEO Suite 25 skills + 18 agents, UI Quality & AST Graph 4 skills, Multi-Agent Orchestration) đang nằm rải rác ở các thư mục ngoài workspace.
4. Quy trình kết hợp **OpenSpec × Figma MCP × Graphify × Anti-UI-Slop × Playwright Visual QA** và quy trình **UI/Typography Read-Only Audit 2 Phase** cần được đúc kết thành 3 skills cốt lõi độc lập, hỗ trợ đa stack để dùng chung cho cả dự án Laravel/Inertia/Vue (bao gồm cấu trúc BED CMS `resources/Frontend/js` và chuẩn Laravel `resources/js`) lẫn Next.js/React (`src/app`, `src/components`).

Ràng buộc bất biến: Tuân thủ tuyệt đối **Strict Safe Guard** — không xóa bất kỳ file nào, không chạm vào database hay code nghiệp vụ (`app/`, `resources/`, `routes/`, `database/`, `config/`), và không để lọt bất kỳ đường dẫn máy cá nhân nào.

## Goals / Non-Goals

**Goals:**
- Tổ chức `.agents/` chuẩn quy ước tự động khám phá của Antigravity: `.agents/skills/<skill-name>/SKILL.md`, `.agents/agents/<agent-name>.md`, `.agents/workflows/opsx-*.md`.
- Thiết kế kiến trúc tài liệu phân tầng (Progressive Disclosure): `SKILL.md` giữ vai trò điều phối gọn nhẹ (~80–130 dòng), tách chi tiết quy trình dài xuống `references/`, tách công cụ tự động hóa xuống `scripts/` và bản mẫu xuống `resources/`.
- Đảm bảo mọi script bash (`bootstrap.sh`, `install-global.sh`) đều chạy an toàn (`set -euo pipefail`), idempotent, tương thích macOS/Linux (`zsh`/`bash`), dùng `$HOME` và đường dẫn tương đối.
- Rà quét tự động toàn bộ file sau khi đóng gói để xác nhận 0 đường dẫn cá nhân và 100% `SKILL.md` có YAML frontmatter chuẩn.

**Non-Goals:**
- Không chỉnh sửa bất kỳ mã nguồn ứng dụng Laravel/Vue/SCSS hay cấu hình build Vite/Tailwind hiện tại của Saigonchem.
- Không cài đặt cưỡng bức package toàn cục nếu chưa cần thiết khi chỉ chạy kiểm thử đóng gói; các script bootstrap phải xử lý mềm dẻo (graceful fallback + hướng dẫn lệnh cài) khi thiếu binary tùy chọn như `graphify`.
- Không ghi đè file `AGENTS.md` sẵn có khi chạy `--export-to` sang một dự án đã có quy tắc riêng.

## Decisions

### 1. Đặt toàn bộ Skills vào `.agents/skills/` thay vì phân mảnh giữa `.agent/`, `.claude/` và global config
- **Lựa chọn**: Hợp nhất 100% skills (bao gồm cả `graphify`, `anti-ui-slop`, `web-design-guidelines`, `writing-guidelines`, `multi-agent-orchestration`, `seo*`, `opsx-*`) về duy nhất `.agents/skills/` và sub-agents về `.agents/agents/`.
- **Lý do**: Antigravity IDE tự động quét `.agents/skills/*/SKILL.md` ngay khi mở workspace, đồng thời `.agents/` đi theo Git repository giúp toàn bộ thành viên trong team có cùng phiên bản quy trình mà không cần cấu hình thủ công từng máy.
- **Phương án đã cân nhắc**: Để ở `$HOME/.gemini/config/skills/` — bị loại làm nơi lưu chính vì không đi theo `git push` của team; thay vào đó cung cấp thêm `scripts/install-global.sh` trong `opsx-bootstrap` để thành viên nào muốn gọi `/opsx-bootstrap` ở folder trống mới tinh cũng có thể cài 1 chạm.

### 2. Kiến trúc Progressive Disclosure cho 3 Skills Cốt Lõi mới (`opsx-bootstrap`, `opsx-figma-workflow`, `opsx-ui-audit`)
- **Lựa chọn**: Giữ `SKILL.md` ngắn gọn, tập trung vào điều kiện kích hoạt, bảng định tuyến kịch bản, ánh xạ thư mục đa stack và quy tắc cứng; đẩy checklist/lộ trình chi tiết vào `references/*.md`.
- **Lý do**: Tránh làm phình context window của agent khi chỉ cần tra cứu 1 trong 3 kịch bản dựng UI (ví dụ dự án chỉ dùng `home-first-incremental` thì không phải nạp toàn bộ `greenfield-6-phases.md`).

### 3. Bảng ánh xạ Đa Stack 3 cấu trúc trong `opsx-figma-workflow` và `opsx-bootstrap`
- **Lựa chọn**: Hỗ trợ nhận diện tự động 3 kiến trúc phổ biến nhất của team:
  1. **BED / JamstackVietnam Laravel + Inertia + Vue 3**: `resources/Frontend/js/{Components,Layouts,Pages}`, `resources/Frontend/scss`, `resources/Frontend/tailwind.config.js`.
  2. **Standard Laravel + Inertia + Vue 3**: `resources/js/{Components,Layouts,Pages}`, `resources/css`, `tailwind.config.js`.
  3. **Next.js (App Router) + React + Tailwind**: `src/components`, `src/app`, `src/app/globals.css` (hoặc `tailwind.config.ts`).
- **Lý do**: Dự án thực tế `bed-saigonchem` sử dụng cấu trúc `resources/Frontend/js/...` trong khi các dự án Laravel chuẩn hoặc Next.js mới dùng cấu trúc khác; khai báo tường minh cả 3 giúp bộ skill dùng lại ngay mà không phải sửa câu chữ.

### 4. Thiết kế `bootstrap.sh` an toàn tuyệt đối (Non-destructive & Idempotent)
- **Lựa chọn**:
  - Không bao giờ dùng lệnh xóa (`rm`).
  - Khi kiểm tra `.gitignore`, chỉ cảnh báo rõ cho người dùng nếu phát hiện dòng chặn `.agents` hoặc `/openspec`, và chỉ nối thêm `/graphify-out` (append) nếu chưa tồn tại.
  - Khi chạy `--export-to <target>`, dùng `cp -R` để sao chép `.agents/skills/` và `.agents/agents/`, chỉ tạo `AGENTS.md` hoặc `openspec/config.yaml` từ bản mẫu trong `resources/` khi dự án đích chưa có các file đó.
  - Tự dò `graphify` qua `command -v graphify` rồi fallback sang `"$HOME/.local/bin/graphify"`; nếu chưa cài thì in hướng dẫn `uv tool install graphifyy` và tiếp tục hoàn tất không lỗi.

## Risks / Trade-offs

- **[Rủi ro] Lọt đường dẫn cá nhân từ các file skill sao chép từ repo khác** → **Giảm thiểu**: Chạy lệnh quét `grep -rn` kiểm tra tên user cá nhân trên toàn bộ `.agents/`, `openspec/`, `AGENTS.md` ngay sau khi copy và chuẩn hóa bằng `$HOME` hoặc đường dẫn tương đối nếu phát hiện.
- **[Rủi ro] File `graphify-out/graph.json` sinh ra có dung lượng lớn làm bẩn lịch sử Git** → **Giảm thiểu**: Bổ sung `/graphify-out` vào `.gitignore` trước khi chạy `graphify . --code-only` và kiểm chứng bằng `git status --short`.
- **[Rủi ro] Nổ context token khi gọi `get_figma_data` trên trang Landing/Home dài** → **Giảm thiểu**: Đưa quy tắc bắt buộc tách truy vấn Figma theo `depth` nông trước rồi gọi từng cụm `nodeId` section vào ngay phần Quy tắc Cứng của `opsx-figma-workflow/SKILL.md`.

## Migration Plan

1. Khởi tạo đầy đủ 4 artifacts của change `package-team-skill-suite` (`proposal.md`, `specs/team-skill-suite/spec.md`, `design.md`, `tasks.md`).
2. Sao chép nguyên vẹn các nhóm skills & specialist agents có sẵn vào `.agents/skills/` và `.agents/agents/` (không đụng tới 5 skills `openspec-*` và 5 workflows `opsx-*.md`).
3. Soạn thảo mới 3 bộ skills cốt lõi (`opsx-bootstrap`, `opsx-figma-workflow`, `opsx-ui-audit`) kèm đầy đủ `references/`, `scripts/` (`chmod +x`), `resources/`.
4. Cập nhật `.gitignore`, `openspec/config.yaml`, `AGENTS.md` và chạy `graphify . --code-only`.
5. Quét kiểm chứng portability, chạy thử `bootstrap.sh`, chạy `openspec validate "package-team-skill-suite"` và cập nhật trạng thái `tasks.md`.
