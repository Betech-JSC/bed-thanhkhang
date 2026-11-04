# Proposal: Dọn Dẹp Các Route Backend Tàn Dư Cũ Thuộc Template Trường Học

## 1. Bối cảnh & Vấn đề (Context & Problem)
Hệ thống Admin CMS của HH Maker ban đầu được kế thừa từ framework giáo dục/đa năng có nhiều controller và route không phù hợp với mô hình xưởng in 3D & gia công chế tạo mẫu nhanh. Ở các bước tối ưu trước đó, giao diện Sidebar (`SidebarMain.vue`) đã được tinh giản về 4 nhóm chức năng cốt lõi (Bảng điều khiển, Vận hành xưởng in 3D, Nội dung & Thương hiệu, Hệ thống & Cấu hình).

Tuy nhiên, trong tệp định tuyến quản trị `routes/backend.php`, một số route tàn dư cũ vẫn còn tồn tại và đang được nạp vào bảng định tuyến admin:
1. `LearningEnvironmentController`: Quản lý môi trường học tập (trường học cũ).
2. `MemberController`: Quản lý đội ngũ giảng viên/thành viên trường học.
3. `PrizeController`: Quản lý danh hiệu/giải thưởng trường học (đã gỡ trước đó ở change cleanup-legacy-funkystyle-views, cần kiểm tra đảm bảo sạch 100%).
4. `HistoryController`: Quản lý lịch sử phát triển trường học.
5. `ApplyController`: Quản lý đơn ứng tuyển/tuyển sinh.
6. `GalleryController`: Quản lý thư viện ảnh hoạt động học sinh.
7. `SupportInfoController`: Module thông tin hỗ trợ cũ (đã được thay thế bởi Chính sách & Settings).

Sự tồn tại của các route không dùng đến này gây ra:
- Tăng diện tích tấn công (attack surface) cho các controller không còn được kiểm thử hay bảo trì.
- Làm phình to bảng route của Laravel và file cache route / Ziggy route Javascript bundle.
- Gây nhiễu và nhầm lẫn cho nhà phát triển khi tra cứu bảng route hệ thống (`php artisan route:list`).

## 2. Mục tiêu (Goals)
- **Gỡ bỏ an toàn 7 route tàn dư**: Loại bỏ hoàn toàn khai báo và import của `LearningEnvironmentController`, `MemberController`, `HistoryController`, `ApplyController`, `GalleryController`, `SupportInfoController` (và xác nhận không có `PrizeController`) trong `routes/backend.php`.
- **Bảo toàn 100% nghiệp vụ xưởng in 3D**: Giữ nguyên vẹn tất cả route cần thiết:
  - Bảng điều khiển (`admin.dashboard.*`)
  - Báo giá & Yêu cầu in 3D (`admin.contacts.*`)
  - Quản lý sản phẩm in 3D (`admin.products.*`, `admin.product-categories.*`, `admin.brands.*`, `admin.tags.*`)
  - Quản lý dịch vụ gia công & in 3D (`admin.services.*`)
  - Banner & Hero Trang chủ (`admin.sliders.*`)
  - Tin tức & Bài viết kiến thức (`admin.posts.*`, `admin.post-categories.*`)
  - Chính sách xưởng in (`admin.policies.*`)
  - Khách hàng & Phân quyền quản trị (`admin.users.*`, `admin.admins.*`)
  - Cấu hình & Mẫu email thông báo (`admin.configs.*`, `admin.email-templates.*`)
  - Nhật ký hệ thống & Đơn hàng (`admin.system-logs.*`, `admin.orders.*`)
- **Tái tạo định tuyến**: Chạy `yarn route` để cập nhật bảng định tuyến Ziggy frontend/backend.
- **Xác minh & Kiểm thử (Evidence-based)**:
  - Kiểm tra bảng route admin bằng `php artisan route:list --path=admin`.
  - Chạy toàn bộ test nghiệp vụ: `tests/Feature/BannerRouteTest.php` và `tests/Feature/ContactFormTest.php`.
  - Kiểm tra build thành công: `yarn backend:build` exit code 0.
