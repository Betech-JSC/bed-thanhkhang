# Spec: backend-routes

## Purpose
Đặc tả việc loại bỏ 7 route backend tàn dư thuộc template trường học cũ trong `routes/backend.php` và bảo toàn 100% các route nghiệp vụ xưởng in 3D HH Maker.

## ADDED Requirements

### Requirement: Loại Bỏ Các Route Quản Trị Tàn Dư Cũ
Hệ thống SHALL loại bỏ hoàn toàn các route quản trị sau khỏi bảng định tuyến `routes/backend.php`:
1. `LearningEnvironmentController` (`admin.learning-environments.*`)
2. `MemberController` (`admin.members.*`)
3. `PrizeController` (`admin.prizes.*`)
4. `HistoryController` (`admin.histories.*`)
5. `ApplyController` (`admin.applies.*`)
6. `GalleryController` (`admin.galleries.*`)
7. `SupportInfoController` (`admin.support-info.*`)

#### Scenario: Truy cập hoặc tra cứu route tàn dư trong route:list
- **WHEN** chạy lệnh `php artisan route:list --path=admin`
- **THEN** không có bất kỳ endpoint nào thuộc 7 controller trên xuất hiện trong danh sách.

#### Scenario: Tái sinh Ziggy router
- **WHEN** thực hiện lệnh `yarn route`
- **THEN** danh sách route sinh ra không chứa các name router `admin.learning-environments.*`, `admin.members.*`, `admin.histories.*`, `admin.applies.*`, `admin.galleries.*`, `admin.support-info.*`.

---

### Requirement: Bảo Toàn Toàn Bộ Route Nghiệp Vụ Cốt Lõi Xưởng In 3D
Tệp `routes/backend.php` SHALL duy trì đầy đủ và nguyên vẹn các route phục vụ vận hành xưởng in 3D HH Maker:
1. `admin.dashboard.*`: Tổng quan thống kê
2. `admin.contacts.*`: Quản lý yêu cầu báo giá in 3D
3. `admin.products.*`, `admin.product-categories.*`, `admin.brands.*`, `admin.tags.*`: Sản phẩm in 3D & vật liệu
4. `admin.services.*`: Dịch vụ in 3D & thiết kế CAD
5. `admin.sliders.*`: Banner & Hero Slider
6. `admin.posts.*`, `admin.post-categories.*`: Tin tức và bài viết
7. `admin.policies.*`: Chính sách xưởng in
8. `admin.users.*`: Tài khoản khách hàng
9. `admin.email-templates.*`: Mẫu email thông báo
10. `admin.system-logs.*`: Nhật ký hệ thống
11. `admin.orders.*`: Quản lý đơn hàng

#### Scenario: Kiểm tra các route nghiệp vụ cốt lõi
- **WHEN** chạy kiểm tra `php artisan route:list --path=admin`
- **THEN** các route `admin.dashboard.*`, `admin.contacts.*`, `admin.products.*`, `admin.services.*`, `admin.sliders.*`, `admin.posts.*`, `admin.system-logs.*` đều tồn tại và hợp lệ.

#### Scenario: Chạy Feature tests hiện có
- **WHEN** thực hiện lệnh `php artisan test tests/Feature/BannerRouteTest.php` và `tests/Feature/ContactFormTest.php`
- **THEN** toàn bộ các assertions đều vượt qua thành công với kết quả PASS.
