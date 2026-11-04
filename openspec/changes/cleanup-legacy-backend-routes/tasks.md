# Tasks: Cleanup Legacy Backend Routes

- [x] 1. Khởi tạo tài liệu OpenSpec
  - [x] 1.1 Tạo `proposal.md` mô tả bối cảnh và mục tiêu dọn dẹp route backend.
  - [x] 1.2 Tạo `specs/backend-routes/spec.md` đặc tả danh mục route loại bỏ và danh mục route cốt lõi giữ lại.
  - [x] 1.3 Tạo `design.md` chi tiết các dòng code cần gỡ bỏ trong `routes/backend.php`.
  - [x] 1.4 Tạo `tasks.md` theo dõi tiến độ thực hiện.

- [x] 2. Triển khai refactor `routes/backend.php`
  - [x] 2.1 Gỡ bỏ 6 câu lệnh `use` controller tàn dư (`ApplyController`, `GalleryController`, `HistoryController`, `MemberController`, `LearningEnvironmentController`, `SupportInfoController`).
  - [x] 2.2 Gỡ bỏ 5 câu lệnh `Route::module(...)` (`HistoryController`, `MemberController`, `GalleryController`, `ApplyController`, `LearningEnvironmentController`).
  - [x] 2.3 Gỡ bỏ 2 route thủ công của `SupportInfoController` (`support-info.index`, `support-info.store`).
  - [x] 2.4 Xác nhận giữ nguyên 100% các route quản trị xưởng in 3D.

- [x] 3. Tái tạo định tuyến & Cập nhật Ziggy
  - [x] 3.1 Chạy lệnh `yarn route` để cập nhật bảng Ziggy và lang i18n.

- [x] 4. Kiểm thử & Xác minh chất lượng
  - [x] 4.1 Kiểm tra bảng route admin bằng `php artisan route:list --path=admin` xác nhận không còn route tàn dư.
  - [x] 4.2 Chạy test tính năng banner: `php artisan test tests/Feature/BannerRouteTest.php`.
  - [x] 4.3 Chạy test tính năng liên hệ & báo giá: `php artisan test tests/Feature/ContactFormTest.php`.
  - [x] 4.4 Chạy build backend: `yarn backend:build` xác nhận exit code 0.

- [x] 5. Hoàn tất & Báo cáo
  - [x] 5.1 Cập nhật 100% `[x]` vào `tasks.md`.
  - [x] 5.2 Báo cáo kết quả và đính kèm raw terminal output cùng git diff.
