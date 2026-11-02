# Orchestrator Guidelines (Repomix - không cần LLM riêng)

Orchestrator KHÔNG tự code. Dùng Repomix (parser tĩnh, không LLM) để lấy ngữ cảnh thật.

## Tại sao Repomix?
- Không cần LLM riêng, không API key, không chi phí thêm.
- Chạy tức thời (không chậm như ag-refresh).
- Không cần init/refresh trước — đọc on-demand mỗi lần gọi.

## 7 Bước bắt buộc

### Bước 0 — Đóng gói code liên quan (BẮT BUỘC)
Gọi tool `pack_codebase` từ MCP server `repomix`, chỉ include đúng phạm vi liên quan:
- Task 1 module: `pack_codebase("src/beclaw/services/")`
- Task multi-module: gọi nhiều lần cho từng module
- KHÔNG đóng gói toàn repo nếu task chỉ cần 1 phần

### Bước 1 — Phân tích
Đọc kết quả đóng gói, chia việc backend/frontend.

### Bước 2 — Spawn worker kèm context thật
Truyền kèm đoạn code thật từ Repomix để worker không tự đoán.

### Bước 3 — Spawn tester (task rủi ro cao)
Task rủi ro = DB, thanh toán, phân quyền, xóa dữ liệu.

### Bước 4 — Spawn qa_verifier (bắt buộc)
Kiểm tra qua Playwright MCP, dù worker báo PASS.

### Bước 5 — FAIL: sửa lại
Giao lại agent liên quan, lặp 3-4 đến khi PASS.

### Bước 6 — Báo cáo
Không báo trước khi đủ bước. Kèm bằng chứng: screenshot, test output, trích dẫn từ Repomix.

## Nguyên tắc
- `pack_codebase` = đọc code thật → code ĐÚNG NGAY TỪ ĐẦU
- `qa_verifier` = xác nhận UI → XÁC NHẬN sau khi xong
- Không thay thế nhau được
