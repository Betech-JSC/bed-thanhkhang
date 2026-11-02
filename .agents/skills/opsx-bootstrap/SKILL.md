---
name: opsx-bootstrap
description: Khởi tạo dự án mới hoặc đồng bộ Bộ Skill Chuẩn Toàn Diện của Team (OpenSpec OPSX, Graphify AST, Figma MCP Phased Workflow, UI Audit, Anti-UI-Slop, Multi-Agent Orchestration & SEO Suite 25+18) sang bất kỳ dự án Laravel/Inertia/Vue hoặc Next.js/React nào. Sử dụng khi người dùng gọi "/opsx-bootstrap", muốn thiết lập quy chuẩn team cho dự án mới, hoặc muốn đồng bộ bộ skill sang thư mục dự án khác.
---

# OPSX Bootstrap — Khởi Tạo & Nhân Bản Bộ Skill Chuẩn Của Team

Skill này tự động hóa việc thiết lập hạ tầng phát triển chuẩn của Team (OpenSpec `OPSX` + Đồ thị kiến trúc `Graphify` + Bộ quy tắc an toàn `AGENTS.md` + 38 Skills & 18 Specialist Sub-Agents) cho dự án hiện tại hoặc nhân bản sang một dự án mới tinh chỉ với 1 lệnh.

## 1. Khi Nào Sử Dụng

- **Tình huống A — Đồng đội vừa clone dự án về máy mới**: Chạy bootstrap tại chỗ để kiểm tra `openspec` CLI, đồng bộ instruction files, sinh đồ thị AST `graphify-out/graph.json` trên máy local và kiểm tra `.gitignore`.
- **Tình huống B — Bắt đầu một dự án mới tinh (Greenfield hoặc Base CMS mới)**: Xuất nguyên bộ `.agents/` (skills, specialist agents, workflows) cùng bản mẫu `AGENTS.md` và `openspec/config.yaml` sang thư mục dự án mới.
- **Tình huống C — Cài đặt Global trên máy cá nhân**: Cài `opsx-bootstrap` vào `$HOME/.gemini/config/skills/opsx-bootstrap` để mở bất kỳ thư mục dự án nào trên Antigravity cũng gọi được `/opsx-bootstrap` ngay.

---

## 2. Hai Chế Độ Vận Hành Của `scripts/bootstrap.sh`

### Chế độ 1: Chạy trực tiếp trên dự án hiện tại
```bash
./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh
```
Script thực hiện tuần tự, an toàn và idempotent (chạy lại nhiều lần không gây lỗi, không xóa bất kỳ file nào):
1. **Kiểm tra OpenSpec CLI**: Xác minh `openspec` đã cài đặt (nếu thiếu sẽ hướng dẫn cài `npm install -g @fission-ai/openspec@latest`), tự chạy `openspec init --tools antigravity --no-animation` nếu chưa có `openspec/` và chạy `openspec update`.
2. **Tự động nhận diện Tech Stack**: Đọc `composer.json`, `package.json` và cấu trúc thư mục để xác định chính xác kiến trúc dự án:
   - *Laravel + Inertia + Vue 3 (BED CMS)* (`resources/Frontend/js`)
   - *Laravel + Inertia + Vue 3 (Standard)* (`resources/js`)
   - *Next.js / React* (`src/app`, `src/components` hoặc `app/`)
   - Ghi nhận các thư viện UI/CSS đi kèm (Tailwind CSS, PrimeVue, Sass, Ziggy, vue-i18n, TypeScript...).
3. **Khởi tạo Đồ thị AST với Graphify**: Tự dò binary `graphify` (`command -v graphify` hoặc `"$HOME/.local/bin/graphify"`) và chạy `graphify . --code-only` để sinh `graphify-out/graph.json`. Nếu máy chưa cài `graphify`, script in hướng dẫn `uv tool install graphifyy` rõ ràng mà **không làm crash** quy trình.
4. **Kiểm tra an toàn `.gitignore`**:
   - Cảnh báo rõ nếu `.gitignore` đang chặn `.agents`, `.agent`, `/openspec` hoặc `.openspec` (để team không vô tình bỏ sót bộ skill khi `git push`).
   - Tự động bổ sung `/graphify-out` vào cuối `.gitignore` nếu chưa có.

### Chế độ 2: Xuất bộ Skill chuẩn sang dự án mới (`--export-to`)
```bash
./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --export-to /duong/dan/toi/du-an-moi
```
1. Sao chép đầy đủ `.agents/skills/`, `.agents/agents/`, `.agents/workflows/` và `.agents/scripts/` (trọn bộ script Python hỗ trợ SEO Suite) sang dự án đích.
2. Nếu dự án đích **chưa có** `AGENTS.md`, tự tạo từ `resources/AGENTS.template.md` (tuyệt đối không ghi đè nếu dự án đích đã có `AGENTS.md`).
3. Tự động kích hoạt Chế độ 1 ngay trên thư mục dự án đích.
4. Nếu `openspec/config.yaml` ở dự án đích chưa khai báo `context:`, gợi ý Agent hoặc lập trình viên điền ngữ cảnh dự án dựa trên `resources/openspec-config.template.yaml`.

---

## 3. Cài Đặt Global Tùy Chọn Cho Từng Thành Viên (`install-global.sh`)

Để gọi được `/opsx-bootstrap` ngay cả khi mở một thư mục dự án mới chưa có `.agents/`, thành viên chỉ cần chạy 1 lần trên máy của mình:

```bash
./.agents/skills/opsx-bootstrap/scripts/install-global.sh
```

Script sẽ đồng bộ `opsx-bootstrap` vào `"$HOME/.gemini/config/skills/opsx-bootstrap"` kèm bản lưu bộ skills chuẩn của team tại `"$HOME/.gemini/config/team-skill-suite/.agents"` để lệnh `--export-to .` luôn sẵn sàng ở mọi workspace mới.

---

## 4. Quy Trình Dành Cho Agent Khi Người Dùng Gọi `/opsx-bootstrap`

1. Xác định mục tiêu của người dùng:
   - Nếu khởi tạo ngay tại workspace đang mở: chạy `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh` (hoặc `"$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh" --export-to .` nếu workspace mới chưa có `.agents/`).
   - Nếu xuất sang thư mục khác: chạy với cờ `--export-to "<target_path>"`.
2. Sau khi script chạy xong, đọc `composer.json` / `package.json` của dự án đích và kiểm tra `openspec/config.yaml`:
   - Nếu `openspec/config.yaml` chưa có mục `context:` thực tế, hãy cập nhật `context:` dựa trên bản mẫu [openspec-config.template.yaml](./resources/openspec-config.template.yaml) khớp 100% với tech stack vừa nhận diện.
3. Kiểm tra `AGENTS.md` của dự án đích đối chiếu với [AGENTS.template.md](./resources/AGENTS.template.md) để đảm bảo đủ 6 nguyên tắc vận hành của team.
4. Báo cáo kết quả kèm raw terminal output của script, tech stack nhận diện được, số lượng skills/agents hiện diện và trạng thái `graphify-out/graph.json`.
