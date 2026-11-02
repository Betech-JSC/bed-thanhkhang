---
name: graphify
description: Truy vấn nhanh đồ thị kiến trúc AST của mã nguồn qua Graphify (graphify-out/graph.json và GRAPH_REPORT.md) để tra cứu classes, components, routes, dependencies và cập nhật lại đồ thị sau khi thêm/sửa cấu trúc file mà không cần grep mù quáng toàn dự án.
---

# Graphify — Điều Hướng Đồ Thị Kiến Trúc AST (Codebase Knowledge Graph)

Skill này hướng dẫn Agent khai thác đồ thị tri thức AST được lập chỉ mục sẵn bởi **Graphify** nhằm nắm bắt chính xác kiến trúc dự án, quan hệ kế thừa/import giữa các module, và chống tạo trùng component/helper.

## 1. Nguyên Tắc Bắt Buộc Trước Khi Grep Hoặc Tạo File Mới

1. **Ưu tiên đọc đồ thị AST trước**:
   - Đọc `graphify-out/GRAPH_REPORT.md` (nếu có) để nắm tổng quan cụm kiến trúc, điểm nút trung tâm (god nodes) và luồng phụ thuộc chính.
   - Truy vấn `graphify-out/graph.json` để kiểm tra danh sách `nodes` (file, class, component, function) và `links`/`edges` (imports, calls, extends) liên quan trực tiếp đến tính năng đang xử lý.
2. **Không tìm kiếm mù quáng (No Blind Grep)**:
   - Không chạy `grep` quét diện rộng toàn bộ repository khi chưa kiểm tra cấu trúc trên `graphify-out/graph.json`.
   - Chỉ đọc chi tiết (`read_file`) đúng các file đích đã xác định từ đồ thị AST để tiết kiệm token và giữ ngữ cảnh sạch.
3. **Chống tạo trùng Component / Service**:
   - Trước khi dựng UI component mới (từ Figma MCP) hoặc viết helper/service mới, bắt buộc đối chiếu `graphify-out/graph.json` xem đã có base component/service tương đương chưa. Nếu có, ưu tiên tái sử dụng hoặc mở rộng (`props`/`slots`/`variants`) thay vì tạo file trùng lặp.

## 2. Khởi Tạo & Cập Nhật Đồ Thị (`--code-only`)

Khi dự án chưa có `graphify-out/graph.json`, hoặc ngay sau khi hoàn thành một Phase/Change có thêm mới, đổi tên hay thay đổi liên kết giữa các file/components/routes, hãy cập nhật lại đồ thị AST bằng chế độ tĩnh siêu tốc (không tốn LLM API):

```bash
# Tự động dùng binary graphify trong PATH hoặc $HOME/.local/bin/graphify
if command -v graphify >/dev/null 2>&1; then
  graphify . --code-only
elif [ -x "$HOME/.local/bin/graphify" ]; then
  "$HOME/.local/bin/graphify" . --code-only
else
  echo "Chưa cài đặt graphify. Cài đặt bằng: uv tool install graphifyy"
fi
```

## 3. Quy Tắc Quản Lý Phiên Bản Git

- Thư mục `graphify-out/` là dữ liệu AST sinh tự động trên từng máy local.
- **BẮT BUỘC** đảm bảo `/graphify-out` nằm trong `.gitignore` của dự án để không đẩy file JSON dung lượng lớn lên Git repository của team.
