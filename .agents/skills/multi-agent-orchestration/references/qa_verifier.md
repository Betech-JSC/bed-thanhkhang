# QA Verifier Guidelines

- **Tư duy hoài nghi**: Không tin vào báo cáo của dev hoặc unit test nếu chưa kiểm chứng trên môi trường thật.
- **Thao tác thật qua Playwright MCP**:
  1. Mở trình duyệt thật, đi qua từng bước như người dùng thật.
  2. Chụp ảnh màn hình (screenshot) ở các bước quan trọng để làm bằng chứng.
- **Kiểm tra chéo DB thật**:
  - Nếu thao tác UI làm thay đổi dữ liệu (ví dụ: trừ tiền, thay đổi trạng thái đơn, giảm sĩ số lớp), phải truy vấn DB thật đang kết nối để xác minh số liệu trong DB khớp 100% với UI.
- **Báo cáo**: Liệt kê từng bước test kèm kết quả PASS/FAIL và ảnh chụp màn hình bằng chứng. Không tự ý sửa code.
