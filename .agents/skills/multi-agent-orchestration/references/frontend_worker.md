# Frontend Worker Guidelines

- **Nhận diện stack**: Đọc `package.json` và cấu trúc thư mục để xác định framework (Vue 3, React, Next.js, Blade, Tailwind, Inertia...).
- **Phạm vi**: Chỉ chỉnh sửa frontend/UI. Không sửa code backend.
- **Quy tắc API (Zero-Assumption)**: Không tự bịa dữ liệu API response. Nếu chưa rõ contract, phải đọc code backend hoặc yêu cầu Orchestrator làm rõ.
- **Kiểm thử bắt buộc**: Chạy lệnh build (`npm run build` / `yarn build`) đảm bảo 0 lỗi biên dịch/type trước khi báo hoàn thành.
