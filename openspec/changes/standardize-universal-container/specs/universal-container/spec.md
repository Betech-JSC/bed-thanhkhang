# Spec Delta: universal-container

## Purpose

Cung cấp quy chuẩn Universal Container thống nhất toàn diện cho hệ thống website HH-Maker theo chuẩn thiết kế Figma Node 2261:5076, khắc phục triệt để hiện tượng mép nội dung ziczac và hiện tượng co giật layout giữa các trang.

## ADDED Requirements

### Requirement: Universal Base Container Sizing and Padding
Hệ thống SHALL cung cấp và áp dụng cấu trúc Universal Base Container với kích thước tối đa `max-w-[1216px]`, căn giữa `mx-auto`, và khoảng đệm phản hồi `px-4 sm:px-6 lg:px-8` (hoặc class tiện ích `.app-container`) cho 90% các khối nội dung chính trên toàn bộ các trang giao diện (Trang Chủ, Sản phẩm, Dịch vụ, Quy trình, Giới thiệu, Liên hệ, Chính sách, và Chân trang Footer).

#### Scenario: Viewport Desktop 1440px đối chiếu Figma Node 2261:5076
- **WHEN** người dùng truy cập bất kỳ trang nào sử dụng Base Container trên màn hình độ phân giải 1440px
- **THEN** khoảng cách từ mép màn hình đến mép nội dung thực tế đo được chính xác là 144px (gồm lề ngoài container 112px cộng khoảng đệm padding 32px), và độ rộng vùng nội dung thực tế đạt chuẩn chính xác 1152px (`1216px - 64px = 1152px`) khớp 100% với Figma.

#### Scenario: Viewport Laptop 1280px triệt tiêu co giật layout
- **WHEN** người dùng truy cập trang hoặc chuyển hướng giữa các trang (như từ `/products` sang `/services`) trên màn hình Laptop 1280px
- **THEN** khoảng cách từ mép màn hình đến mép nội dung là 64px (lề ngoài 32px cộng padding 32px), độ rộng vùng nội dung giữ ổn định 1152px, hoàn toàn không xảy ra hiện tượng co giật hoặc lệch layout 128px.

#### Scenario: Viewport Di động và Máy tính bảng dưới 1024px
- **WHEN** người dùng xem trang trên màn hình di động (< 640px) hoặc máy tính bảng (640px - 1023px)
- **THEN** container co giãn linh hoạt 100% bề ngang với khoảng đệm an toàn `px-4` (16px) hoặc `sm:px-6` (24px), đảm bảo không phát sinh thanh cuộn ngang ngoài ý muốn (`document.documentElement.scrollWidth === window.innerWidth`).

### Requirement: Homepage Sections Vertical Alignment
Hệ thống SHALL áp dụng cấu trúc Universal Base Container đồng bộ cho tất cả các section nội dung trên Trang Chủ bao gồm: `BrandStorySection`, `HighlightMetrics`, `ServicesSection`, `ProductsSection`, `FeaturedProductsCarousel`, và `ContactSection`. Mép trái của tất cả các khối nội dung này SHALL thẳng hàng tuyệt đối theo một đường gióng dọc duy nhất.

#### Scenario: Kiểm chứng gióng thẳng hàng mép trái Trang Chủ
- **WHEN** người dùng cuộn xem qua các section trên Trang Chủ ở độ phân giải 1440px
- **THEN** tọa độ mép trái (`getBoundingClientRect().left`) của vùng nội dung trong `BrandStorySection`, `HighlightMetrics`, `ServicesSection`, `ProductsSection`, `FeaturedProductsCarousel`, và `ContactSection` đều trùng khớp ở mức 144px, loại bỏ hoàn toàn hiện tượng lệch ziczac từ 112px đến 176px.

### Requirement: Products and Services Cross-Page Consistency
Hệ thống SHALL chuẩn hóa vùng chứa container trên các trang danh mục và chi tiết sản phẩm (`Product/Index.vue`, `Product/Show.vue`, `ProductCtaBanner.vue`) cùng các trang danh mục và chi tiết dịch vụ (`Services/Index.vue`, `Services/Show.vue`), thay thế toàn bộ các cấu hình phân mảnh cũ (`max-w-[1440px] xl:px-[144px]`, `max-w-[1152px] xl:px-0`).

#### Scenario: Đồng bộ khung nhìn khi chuyển trang Sản phẩm và Dịch vụ
- **WHEN** người dùng điều hướng qua lại giữa trang Danh mục sản phẩm `/products` và Danh mục dịch vụ `/services` trên màn hình máy tính
- **THEN** vị trí bắt đầu của tiêu đề, bộ lọc, lưới thẻ và banner CTA của cả hai trang đều nằm trên cùng một hệ tọa độ mép ngoài, không bị giật lùi hay bung nở khung nhìn.

### Requirement: Intentional Exception Containers Preservation
Hệ thống SHALL bảo toàn các vùng chứa đặc thù có chủ đích thiết kế gồm: Hero Banner Card và Header Navigation Bar, không ép buộc áp dụng Base Container 1216px lên các thành phần này.

#### Scenario: Hiển thị Hero Banner Card dạng nổi
- **WHEN** khối Hero Banner Card hiển thị trên Trang Chủ hoặc Trang Giới thiệu
- **THEN** vùng chứa Hero Card duy trì kích thước `max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-0` với góc bo tròn và lề 80px đối xứng ở màn hình 1440px theo đúng ý đồ thiết kế card banner dạng nổi.

#### Scenario: Hiển thị Thanh điều hướng Header cố định
- **WHEN** thanh Header hiển thị ở đầu trang
- **THEN** thanh điều hướng duy trì kích thước tối đa `max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20` (hoặc lề ngoài 80px) để bao quát thanh điều hướng trên toàn chiều ngang màn hình.

### Requirement: Footer and Content Pages Alignment
Hệ thống SHALL áp dụng Universal Base Container cho component chân trang `Footer.vue` và các trang thông tin nội dung gồm `Process/Index.vue`, `About/Index.vue`, `Contact.vue`, và `Policy.vue`.

#### Scenario: Chân trang Footer gióng thẳng với nội dung trang
- **WHEN** người dùng xem chân trang `Footer.vue` ở cuối bất kỳ trang nào
- **THEN** lưới 4 cột của Footer và dòng bản quyền chân trang gióng thẳng hàng với mép trái (144px) và mép phải (1296px) của các khối nội dung phía trên.
