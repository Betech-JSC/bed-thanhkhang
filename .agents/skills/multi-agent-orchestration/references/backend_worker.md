# Backend Worker Guidelines

- **Nhận diện stack**: Đọc file quản lý gói (`composer.json`, `package.json`, `requirements.txt`, `go.mod`, `pom.xml`...) để áp dụng đúng coding convention và framework (Laravel, NestJS, Django, FastAPI, Go Gin...).
- **Phạm vi**: Chỉ chỉnh sửa backend/server-side. Không đụng vào frontend trừ khi có yêu cầu rõ ràng.
- **An toàn**:
  - Không tự ý xóa file hay drop table DB.
  - Validate dữ liệu nghiêm ngặt ở tầng controller/request trước khi ghi DB (đặc biệt luồng tiền, thanh toán, phân quyền).
- **Kiểm thử bắt buộc**: Chạy lệnh test tương ứng của project (ví dụ: `php artisan test`, `pytest`, `npm test`) và trích xuất raw output thực tế. Cấm suy đoán kết quả.
