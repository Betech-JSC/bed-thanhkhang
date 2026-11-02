# Proposal: Strip FunkyStyle Features

## Why
Mã nguồn hiện tại kế thừa từ dự án FunkyStyle chứa nhiều nghiệp vụ đặc thù cho trung tâm đào tạo đồ họa / âm nhạc (quản lý Khóa học, Lớp học, Điểm Quả thưởng, Hạng thành viên, tích hợp NAS Synology để tải tài nguyên số, thanh toán học phí qua SePay/PayPal và đồng bộ hai chiều với Google Sheets). Đối với dự án Saigonchem (doanh nghiệp thương mại hóa chất & phụ gia công nghiệp), các tính năng này hoàn toàn dư thừa, gây phình to mã nguồn, xung đột logic nghiệp vụ sản phẩm, làm chậm hệ thống và tăng rủi ro bảo mật. 

Cần tiến hành lược bỏ triệt để và an toàn toàn bộ các module đặc thù trên, trả mã nguồn về một khung Base CMS tinh gọn, chuẩn mực (Clean Architecture) phục vụ phát triển danh mục hóa chất, bài viết tin tức và quản trị doanh nghiệp Saigonchem.

## What Changes
Lược bỏ toàn diện 5 nhóm module nghiệp vụ FunkyStyle và chuẩn hóa Base CMS:

1. **Loại bỏ Module Khóa học & Lớp học (Courses & Classes)**:
   - **BREAKING**: Hủy bỏ các model, controller, route và view liên quan đến Course, CourseCategory, CourseClass, CourseConfirmationSlip, Combo, School, StudentProfile, StudentRevenue.
   - Loại bỏ quan hệ `courses()` và `Post::TYPE_COURSE` trong `Product` và `Post`.
   - Xóa bỏ giao diện đăng ký học, quản lý lớp học, phân bổ học viên và xuất phiếu xác nhận học phí.

2. **Loại bỏ Module Điểm Quả & Hạng Thành Viên (Points & Member Ranks)**:
   - **BREAKING**: Hủy bỏ các bảng, model, service và controller liên quan đến PointPackage, PointTransaction, UserRank.
   - Loại bỏ các trường điểm thưởng (`points`, `lifetime_points`, `reward_points`, `topup_points`, `reward_locked`) trong bảng `users` và các API/view nạp/trừ/tặng điểm.

3. **Loại bỏ Module Tài nguyên số & Tích hợp NAS Synology (Resources & Synology NAS)**:
   - **BREAKING**: Hủy bỏ SynologyService, ResourceController, ResourceCategoryController, ContactCheckoutResourceController.
   - Loại bỏ các kiểu post tài nguyên số (`TYPE_RESOURCE`, `TYPE_RESOURCE_FREE`, `TYPE_RESOURCE_PAY`), các trường đếm lượt download và API phân quyền truy cập NAS.

4. **Loại bỏ Module Cổng thanh toán riêng cho khóa học & nạp điểm (SePay & PayPal course/point gateways)**:
   - **BREAKING**: Hủy bỏ SePayWebhookController, SePayService, PayPalService, VNPayService phục vụ thanh toán học phí và nạp điểm.
   - Xóa bỏ các route webhook `/sepay/webhook`, `/paypal/webhook` và route kiểm tra trạng thái thanh toán đơn học phí.

5. **Loại bỏ Module Đồng bộ Google Sheets (Sheet Sync & Import)**:
   - **BREAKING**: Hủy bỏ GoogleSheetImportController, GoogleSheetOrderSyncController, GoogleSheetService, GoogleSheetSyncService, GoogleSheetSyncLog.
   - Xóa bỏ các route và view liên quan đến việc import/export danh sách học viên và đơn hàng qua Google Sheets.

6. **Giữ lại và bảo toàn Khung Base CMS cho Saigonchem**:
   - Khung cốt lõi Laravel 10 và các gói thư viện chuẩn (Inertia.js, Vue 3, Spatie Permission).
   - Hệ thống Đa ngôn ngữ (Localization: `Route::localized`, `Lang::uri`, các bảng `*translations`).
   - Quản lý Media & Tệp tải lên (`File.php`, Gallery, Sliders).
   - Cấu hình hệ thống chung (`Config.php`, `ConfigController.php`, `Configs.vue`).
   - Quản trị tài khoản & Phân quyền cơ bản (Admin guard `auth:admin`, Base User guard `auth:user` đã lược bỏ các trường point/rank).
   - Module Tin tức (`Post`, `PostCategory`) và Chính sách (`Policy`).
   - Module Sản phẩm & Danh mục sản phẩm (`Product`, `ProductCategory`) làm nền tảng cho danh mục hóa chất Saigonchem.

## Capabilities

### New Capabilities
- `clean-architecture`: Đặc tả kiến trúc lõi tinh gọn (Clean Base CMS) cho dự án Saigonchem sau khi bóc tách toàn bộ 5 module FunkyStyle, đảm bảo hệ thống vận hành ổn định, không còn route rác, không còn dependency mồ côi và cơ sở dữ liệu sạch sẽ.

### Modified Capabilities
<!-- Không có capability nào hiện hữu cần sửa đổi do dự án chưa có spec chính thức trước đó -->

## Impact
- **Mã nguồn Backend**: Xóa bỏ ~25 controllers, ~10 services, ~12 models và ~15 migrations đặc thù FunkyStyle. Dọn sạch `routes/backend.php`, `routes/frontend.php`, `routes/api.php`.
- **Mã nguồn Frontend (Vue/Inertia)**: Xóa bỏ các thư mục trang Backend (`Courses`, `CourseClasses`, `CourseCategories`, `Combos`, `PointPackages`, `PointTransactions`, `UserRanks`, `GoogleSheetImport`, `GoogleSheetOrderSync`, `Schools`, `StudentProfile`, `Resources`, v.v.) và các trang Frontend (`Courses.vue`, `CourseDetail.vue`, `Resources.vue`, `Checkout/`, v.v.).
- **Cơ sở dữ liệu**: Dọn bỏ các bảng đặc thù (`course_classes`, `course_confirmation_slips`, `combos`, `combo_course`, `point_packages`, `point_transactions`, `user_ranks`, `schools`, `google_sheet_sync_logs`) và các cột phụ thuộc trong `users`, `contacts`, `posts` thông qua migration dọn dẹp có kiểm soát an toàn.
- **Dependencies**: Có thể gỡ bỏ các thư viện bên ngoài không còn sử dụng như Google Client API / Sheets SDK, Synology API client nếu có trong `composer.json` / `package.json`.
