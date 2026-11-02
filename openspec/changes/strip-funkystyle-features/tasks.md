# Tasks: Strip FunkyStyle Features

## 1. Routing Deprecation & Cleanup

- [x] 1.1 Dọn dẹp `routes/backend.php`: Loại bỏ toàn bộ routes quản trị liên quan đến Course, CourseCategory, CourseClass, ContactCheckoutCourse, ContactCheckoutResource, Resource, ResourceCategory, PointPackage, PointTransaction, UserRank, GoogleSheetImport, GoogleSheetOrderSync, School, StudentProfile, StudentRevenue, Combo. Xác minh bằng lệnh route checks và php -l.
- [x] 1.2 Dọn dẹp `routes/frontend.php`: Loại bỏ các route public về khóa học (`/courses`), tài nguyên (`/resources`), phiếu xác nhận (`/slips`), checkout học phí/tài nguyên (`/checkout/courses`, `/checkout/resources`) và API trường học (`/api/schools`). Xác minh bằng route checks và php -l.
- [x] 1.3 Dọn dẹp `routes/api.php`: Loại bỏ các endpoint webhook SePay (`/sepay/webhook`), webhook PayPal (`/paypal/webhook`), kiểm tra thanh toán realtime (`/checkout/courses/check-status/{id}`), academic options (`/academic-options`), combo endpoints (`/course/{id}/combos`) và các action course/resource trong `ProductController`. Xác minh bằng route checks và php -l.

## 2. Frontend Vue & Navigation Cleanup

- [x] 2.1 Cập nhật `resources/Backend/js/Components/SidebarMain.vue`: Loại bỏ toàn bộ menu items và badges liên quan đến Khóa học, Học viên, Lớp học, Điểm thưởng, Cấp bậc, Giao dịch điểm, Đồng bộ Google Sheet, Tài nguyên số. Xác minh sidebar không còn tham chiếu đến route FunkyStyle nào.
- [x] 2.2 Xóa các thư mục view quản trị trong `resources/Backend/js/Pages/`: Xóa `Courses/`, `CourseCategories/`, `CourseClasses/`, `Combos/`, `Schools/`, `StudentProfile/`, `StudentRevenue/`, `PointPackages/`, `PointTransactions/`, `UserRanks/`, `GoogleSheetImport/`, `GoogleSheetOrderSync/`, `Resources/`, `ResouceCategories/`, `ContactCheckoutCourse/`, `FormCheckoutCourse/`, `FormCheckoutResource/`. Xác minh các thư mục đã được xóa sạch.
- [x] 2.3 Xóa các view công khai trong `resources/Frontend/js/Pages/`: Xóa `Courses.vue`, `CourseDetail.vue`, `Courses/`, `Resources.vue`, `Resources/`, `Checkout/`, `Closed.vue`. Xác minh các thư mục đã được xóa sạch.
- [x] 2.4 Dọn dẹp liên kết và import trong các component chung (Header, Footer, Navbar, Search dropdown) của Frontend. Xác minh toàn bộ component đã được làm sạch và không còn orphan routes/imports.

## 3. Controllers & Request Handlers Cleanup

- [x] 3.1 Xóa các Controller quản trị trong `app/Http/Controllers/Backend/`: `CourseController.php`, `CourseCategoryController.php`, `CourseClassController.php`, `ContactCheckoutCourseController.php`, `ContactCheckoutResourceController.php`, `SchoolController.php`, `StudentProfileController.php`, `StudentRevenueController.php`, `ComboController.php`, `PointPackageController.php`, `PointTransactionController.php`, `UserRankController.php`, `GoogleSheetImportController.php`, `GoogleSheetOrderSyncController.php`, `ResourceController.php`, `ResourceCategoryController.php`. Xác minh bằng lệnh `ls app/Http/Controllers/Backend/`.
- [x] 3.2 Xóa các Controller Frontend trong `app/Http/Controllers/Frontend/`: `CourseController.php`, `CourseSlipController.php`, `PointController.php`, `ResourceController.php`, `SchoolController.php`. Xác minh bằng lệnh `ls app/Http/Controllers/Frontend/`.
- [x] 3.3 Xóa các Controller API trong `app/Http/Controllers/Api/`: `ComboController.php`, `SePayWebhookController.php`, và dọn dẹp các method khóa học/tài nguyên (`getCourse`, `getResource`, `getNextOpeningSchedule`) trong `ProductController.php`. Xác minh bằng lệnh `ls app/Http/Controllers/Api/`.

## 4. Services & Domain Models Refactoring

- [x] 4.1 Xóa bỏ 11 Services nghiệp vụ FunkyStyle trong `app/Services/`: `ClassAllocationService.php`, `ContactWorkflowService.php`, `CourseClassOrderingService.php`, `CourseConfirmationSlipService.php`, `GoogleSheetService.php`, `GoogleSheetSyncService.php`, `PayPalService.php`, `PointService.php`, `SePayService.php`, `SynologyService.php`, `VNPayService.php`. Xác minh thư mục `app/Services/` không còn các file này.
- [x] 4.2 Xóa các Model đặc thù FunkyStyle trong `app/Models/`: `CourseClass.php`, `CourseConfirmationSlip.php`, `Combo.php`, `ComboCourse.php`, `School.php`, `PointPackage.php`, `PointTransaction.php`, `UserRank.php`, `GoogleSheetSyncLog.php`. Xác minh thư mục `app/Models/`.
- [x] 4.3 Refactor Core Model `Product.php`: Gỡ bỏ quan hệ `courses()`, phương thức `saveCourses()`, scope lọc khóa học và các hằng số liên quan. Xác minh không còn syntax error hay missing relation.
- [x] 4.4 Refactor Core Model `Post.php`: Gỡ bỏ các hằng số `TYPE_COURSE`, `TYPE_RESOURCE`, `TYPE_RESOURCE_FREE`, `TYPE_RESOURCE_PAY`, các trường `points_cost`, `download_count` và các scope liên quan. Xác minh không còn syntax error.
- [x] 4.5 Refactor Core Model `User.php`: Gỡ bỏ quan hệ `userRank()`, các trường điểm thưởng `points`, `lifetime_points`, `reward_points`, `topup_points`, `reward_locked`, và các helper tính điểm. Xác minh người dùng hoạt động bình thường và php -l clean.
- [x] 4.6 Refactor Core Model `Contact.php`: Gỡ bỏ logic phân bổ lớp học, tracking khóa học và liên kết phiếu xác nhận. Xác minh tính năng gửi liên hệ hoạt động bình thường và php -l clean.

## 5. Safe Database Cleanup Migration

- [x] 5.1 Tạo file migration dọn dẹp `database/migrations/2026_10_05_000000_cleanup_funkystyle_legacy_tables_and_columns.php` tích hợp tắt/bật kiểm tra foreign key và kiểm tra an toàn `hasTable`/`hasColumn`. Xác minh file migration cú pháp hợp lệ.
- [x] 5.2 Cấu hình xóa các bảng dữ liệu cũ trong migration: `course_classes`, `course_confirmation_slips`, `combos`, `combo_course`, `schools`, `point_packages`, `point_transactions`, `user_ranks`, `google_sheet_sync_logs`. Xác minh tên bảng chính xác với database hiện hữu.
- [x] 5.3 Cấu hình xóa các cột mồ côi trong migration trên bảng `users` (các trường points, ranks), `contacts` (class tracking), `posts` (points cost, download counts). Xác minh tên cột tồn tại trong schema.
- [ ] 5.4 Chạy migration dọn dẹp cơ sở dữ liệu và kiểm tra trạng thái bảng qua `php artisan migrate:status` (Yêu cầu sự phê duyệt từ người dùng theo Safe Guard Policy).

## 6. System Verification & Integrity Check

- [x] 6.1 Kiểm tra tính tương thích và toàn vẹn của mã nguồn PHP qua `php -l` toàn diện. Xác minh không có lỗi class not found hay syntax error.
- [x] 6.2 Kiểm tra bảng route và controller references. Xác minh không có route lỗi hoặc missing controller.
- [x] 6.3 Kiểm tra toàn diện import và components Frontend Vue. Không còn tham chiếu đến bất kỳ route hay component FunkyStyle nào. (Tuân thủ chỉ thị không chạy lệnh build local).
- [x] 6.4 Xác minh các tính năng Base CMS cốt lõi (Posts, Products, Configs, Auth, Contacts) hoạt động chuẩn mực.
