# Spec Delta

## Purpose

Thiết lập kiến trúc lõi tinh gọn (Clean Architecture) cho hệ thống Base CMS của Saigonchem, đảm bảo loại bỏ hoàn toàn các bề mặt tấn công và mã nguồn dư thừa từ FunkyStyle trong khi duy trì tính toàn vẹn của hệ thống quản trị nội dung.

## ADDED Requirements

### Requirement: Base CMS Routing and Endpoint Integrity
Hệ thống Base CMS của Saigonchem SHALL duy trì hoạt động ổn định và trả về mã trạng thái HTTP 200 cho toàn bộ các route quản trị cốt lõi bao gồm: Trang tổng quan, Tin tức (Posts & PostCategories), Danh mục sản phẩm (Products & ProductCategories), Liên hệ (Contacts), Cấu hình hệ thống (Configs), Thư viện Media/Tập tin (File & Gallery) và Đa ngôn ngữ.

#### Scenario: Admin accesses core CMS dashboard and modules
- **WHEN** Quản trị viên đã đăng nhập truy cập vào các URL quản trị `/admin/dashboard`, `/admin/posts`, `/admin/products`, `/admin/configs`, `/admin/contacts`
- **THEN** Hệ thống phản hồi HTTP 200 với giao diện tương ứng, dữ liệu được tải chính xác và không phát sinh ngoại lệ liên quan đến các module đã bị bóc tách

#### Scenario: Public visitor accesses core content
- **WHEN** Khách truy cập duyệt các trang công khai `/`, `/posts`, `/products`, `/ve-chung-toi`, `/policies`
- **THEN** Hệ thống render giao diện Vue/Inertia với HTTP 200, hiển thị đúng nội dung và ngôn ngữ tương ứng

### Requirement: Deprecation and Removal of FunkyStyle Endpoints
Hệ thống SHALL loại bỏ hoàn toàn tất cả các endpoint API, route web và webhook đặc thù của FunkyStyle (Khóa học, Lớp học, Điểm Quả thưởng, Hạng thành viên, Tích hợp NAS Synology, Google Sheets sync, Cổng thanh toán SePay/PayPal học phí). Mọi yêu cầu gửi tới các URL này SHALL trả về mã lỗi HTTP 404 Not Found.

#### Scenario: Request to legacy course and class endpoints
- **WHEN** Người dùng hoặc bot gửi request tới `/courses`, `/courses/{slug}`, `/checkout/courses`, `/admin/courses`, `/admin/course-classes`, `/admin/students/{id}`
- **THEN** Hệ thống phản hồi HTTP 404 Not Found và không thực thi bất kỳ truy vấn hay logic đào tạo nào

#### Scenario: Request to legacy point and membership rank endpoints
- **WHEN** Người dùng hoặc bot gửi request tới `/admin/point-packages`, `/admin/point-transactions`, `/admin/user-ranks`
- **THEN** Hệ thống phản hồi HTTP 404 Not Found

#### Scenario: Request to legacy digital resources and Synology NAS endpoints
- **WHEN** Người dùng hoặc bot gửi request tới `/resources`, `/checkout/resources`, `/admin/resources`, `/admin/resources/quick-update`
- **THEN** Hệ thống phản hồi HTTP 404 Not Found

#### Scenario: Request to legacy Google Sheets sync and SePay webhook endpoints
- **WHEN** Dịch vụ bên ngoài gửi webhook hoặc request tới `/sepay/webhook`, `/paypal/webhook`, `/admin/google-sheet-import`, `/admin/google-sheet-order-sync`
- **THEN** Hệ thống phản hồi HTTP 404 Not Found và không kích hoạt bất kỳ xử lý ngầm nào

### Requirement: User Profile and Authentication Integrity
Hệ thống SHALL bảo toàn tính toàn vẹn của mô hình xác thực và phân quyền cho quản trị viên (`auth:admin`) và người dùng (`auth:user`), đồng thời loại bỏ hoàn toàn các trường dữ liệu phụ thuộc vào hệ thống điểm quả thưởng và cấp bậc học viên.

#### Scenario: User registration and authentication without reward points
- **WHEN** Khách hàng đăng ký hoặc đăng nhập tài khoản trên hệ thống Saigonchem
- **THEN** Hệ thống xác thực danh tính hợp lệ, tải thông tin profile người dùng mà không truy vấn hoặc tham chiếu đến các trường `points`, `lifetime_points`, `reward_points`, `topup_points` hay bảng `user_ranks`

### Requirement: Independent Product and Catalog Architecture
Hệ thống SHALL duy trì mô hình dữ liệu Sản phẩm (`Product`, `ProductCategory`, `ProductVariant`) độc lập hoàn toàn, không liên kết với các bài viết khóa học (`Post::TYPE_COURSE`) hay bảng trung gian liên quan đến khóa học.

#### Scenario: Querying chemical product catalog
- **WHEN** Quản trị viên hoặc khách hàng truy vấn danh sách hoặc chi tiết sản phẩm hóa chất
- **THEN** Hệ thống tải danh mục sản phẩm, biến thể và thuộc tính sản phẩm mà không thực thi quan hệ `courses()` hay kiểm tra ràng buộc loại bài viết khóa học
