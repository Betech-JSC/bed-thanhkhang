---
name: multi-agent-orchestration
description: Quy trình điều phối Multi-Agent Orchestration toàn diện (Orchestrator, Backend Worker, Frontend Worker, Tester, QA Verifier) với các quy tắc an toàn nghiêm ngặt (Safe Mode, Evidence-Based, Zero-Assumption). Tự động kích hoạt khi nhận task phức tạp, sửa nhiều file, hoặc rủi ro cao.
---

# Multi-Agent Orchestration Skill

Kỹ năng này hướng dẫn Antigravity vận hành quy trình điều phối đa tác nhân (Multi-Agent) chuyên nghiệp, an toàn và minh bạch tuyệt đối.

## 1. Vai trò các Subagents

| Subagent | Quyền hạn & Công cụ | Vai trò chính |
| :--- | :--- | :--- |
| **`orchestrator`** | Read tools, Planning, Subagents | Phân tích bài toán, lập kế hoạch, chia việc, kiểm tra chéo, không tự code. |
| **`backend_worker`** | Workspace-write, Terminal | Nhận diện stack backend, sửa code server-side, bắt buộc chạy test thật. |
| **`frontend_worker`** | Workspace-write, Terminal | Nhận diện stack frontend, sửa UI, build kiểm tra 0 lỗi, không bịa API. |
| **`tester`** | Workspace-write, Terminal | Viết unit/feature test độc lập, săn edge-case và race condition, báo kết quả thật. |
| **`qa_verifier`** | Workspace-write, Playwright MCP | Mở browser thật qua Playwright, thao tác UI, đối chiếu DB thật, chụp screenshot. |

## 2. Quy trình Khởi Tạo Subagent trong Antigravity

Antigravity cung cấp sẵn 2 công cụ cốt lõi:
- `define_subagent`: Định nghĩa tác nhân con mới với system prompt, role và quyền hạn.
- `invoke_subagent`: Khởi chạy tác nhân con trong background workspace (`inherit` hoặc `share`).

Khi thực thi task đa tầng hoặc task lớn, Orchestrator sẽ đọc các hướng dẫn chuyên biệt trong thư mục `references/` để khởi tạo các subagent tương ứng.

## 3. Tài liệu Tham Khảo Chi Tiết
- [Hướng dẫn Backend Worker](./references/backend_worker.md)
- [Hướng dẫn Frontend Worker](./references/frontend_worker.md)
- [Hướng dẫn Independent Tester](./references/tester.md)
- [Hướng dẫn QA Verifier](./references/qa_verifier.md)

## 4. Tích hợp Repomix — Ngữ cảnh thật, không cần LLM riêng

### Repomix là gì?
Parser tĩnh (tree-sitter), đóng gói code thành file có cấu trúc. Không LLM, không API key, chạy tức thời, giảm ~70% token.

### MCP Config (thêm vào Antigravity IDE)
```json
"repomix": {
  "command": "npx",
  "args": ["-y", "repomix", "--mcp"]
}
```

### Workflow (không cần init)
Orchestrator gọi `pack_codebase` on-demand trước mọi task.
Không cần `ag-refresh` hay bất kỳ bước init nào.

### Đóng gói thông minh
- Chỉ include thư mục liên quan đến task
- Không đóng gói toàn repo cho task nhỏ

### Tài liệu
- [Orchestrator Guidelines](./references/orchestrator.md)
