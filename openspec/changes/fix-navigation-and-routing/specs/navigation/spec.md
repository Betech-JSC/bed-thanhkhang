# Spec Delta: Chuẩn hóa điều hướng và định tuyến (Navigation & Routing)

## Purpose

Cung cấp và chuẩn hóa hành vi điều hướng toàn diện cho website HH Maker bao gồm hệ thống Breadcrumbs trên trang chi tiết sản phẩm, khả năng tương tác của thẻ sản phẩm, menu chính trên Header và định tuyến danh mục sản phẩm `/san-pham`.

## ADDED Requirements

### Requirement: PDP Breadcrumb Navigation
Hệ thống SHALL hiển thị liên kết "Sản phẩm" trên thanh Breadcrumb của trang chi tiết sản phẩm (PDP) dẫn chính xác về trang danh mục sản phẩm `/san-pham` thay vì đường dẫn không tồn tại `/products`.

#### Scenario: User clicks breadcrumb link on PDP
- **WHEN** người dùng đang ở trang chi tiết sản phẩm (`/san-pham/{slug}`) và nhấp vào liên kết "Sản phẩm" trên thanh Breadcrumb
- **THEN** hệ thống điều hướng người dùng tới trang danh mục sản phẩm `/san-pham` với HTTP status 200, hiển thị giao diện danh mục sản phẩm và không gặp lỗi 404 Not Found.

### Requirement: Interactive Product Card Thumbnail Navigation
Hệ thống SHALL cho phép người dùng nhấp vào toàn bộ ảnh thumbnail của thẻ sản phẩm (`ProductCard`) để điều hướng trực tiếp đến trang chi tiết sản phẩm tương ứng.

#### Scenario: User clicks thumbnail image on ProductCard
- **WHEN** người dùng nhấp chuột hoặc chạm vào khối ảnh thumbnail sản phẩm trên thẻ `ProductCard` (tại Trang chủ, Danh mục sản phẩm hoặc Danh sách liên quan)
- **THEN** hệ thống kích hoạt điều hướng Inertia chuyển hướng người dùng đến trang chi tiết của sản phẩm đó (`/san-pham/{slug}`).

### Requirement: Header Main Navigation Alignment
Hệ thống SHALL cập nhật thanh điều hướng Header: hiển thị nhãn "Giới thiệu" thay cho tàn dư thương hiệu cũ, liên kết menu "Sản phẩm" trỏ về route danh mục `/san-pham`, và kích hoạt hiển thị mục menu "Dịch vụ" trỏ về `/dich-vu`.

#### Scenario: User views Header navigation links
- **WHEN** người dùng tải bất kỳ trang nào trên website (cả giao diện Desktop và Mobile Menu)
- **THEN** thanh điều hướng Header hiển thị danh sách các mục: "Giới thiệu" (dẫn đến `/ve-chung-toi`), "Sản phẩm" (dẫn đến `/san-pham`), "Dịch vụ" (dẫn đến `/dich-vu`), "Giải thưởng", "Đồ án", "Feedback", "F.BLog", không còn xuất hiện từ khóa "funky".

#### Scenario: User clicks Product link on Header
- **WHEN** người dùng nhấp vào mục menu "Sản phẩm" trên thanh Header
- **THEN** hệ thống điều hướng trực tiếp đến trang danh mục sản phẩm `/san-pham` và đánh dấu trạng thái active cho menu này theo đúng đường dẫn URL hiện tại.

### Requirement: Product Catalog Index Page Resolution
Hệ thống SHALL phản hồi yêu cầu GET tại route `/san-pham` (`products.index`) bằng cách truy vấn danh sách sản phẩm kích hoạt và kết xuất component `Product/Index` qua Inertia.js kèm theo metadata SEO.

#### Scenario: User visits product catalog URL
- **WHEN** người dùng truy cập trực tiếp URL `/san-pham` hoặc nhấp vào liên kết danh mục sản phẩm từ bất kỳ vị trí nào
- **THEN** hệ thống trả về mã trạng thái HTTP 200, kết xuất trang `Product/Index` với danh sách sản phẩm in 3D, hệ thống bộ lọc thuộc tính và thông tin thẻ meta SEO đầy đủ.
