# Proposal: Đồng bộ Trang chủ HH Maker theo chuẩn thiết kế Figma

## Why

Trang chủ (Homepage) là điểm chạm thương hiệu quan trọng nhất của HH Maker, đại diện cho năng lực công nghệ và dịch vụ chế tác in 3D cao cấp. Sau các đợt tái cấu trúc gỡ bỏ tính năng cũ của FunkyStyle và chuẩn hóa Base Layout Shell, Trang chủ hiện tại cần được đồng bộ triệt để theo đúng bản thiết kế chính thức từ Figma (Frame `2261:5474` gồm Hero `2261:5475` và Frame 236 `2261:5502`).

Việc này giúp hoàn thiện nhận diện Dark Tech hiện đại, làm nổi bật năng lực thiết bị in 3D (FDM, SLA), danh mục sản phẩm mẫu, câu chuyện thương hiệu và biểu mẫu tư vấn, đồng thời mang lại trải nghiệm thị giác mượt mà với hiệu ứng Swiper/GSAP trên cả Desktop lẫn Mobile.

## What Changes

- **Đồng bộ Hero Section (`2261:5475`)**: Khung thẻ 1279x616px chuẩn Figma, hình nền artwork in 3D công nghệ cao, tiêu đề H1 38px in hoa ("HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D"), phụ đề, mô tả và cụm CTA kép (Khám phá ngay + Nhận tư vấn thiết kế). Đồng bộ huy hiệu xoay tròn dạng đường cong SVG `FROM IDEAS TO REALITY - ` với icon mũi tên 45° trong suốt.
- **Đồng bộ Featured Products Carousel (`2261:5503`)**: Tiêu đề phân đoạn 38px, hệ thống thẻ trượt kích thước chuẩn 264x419px nền `#182238` kèm chân thẻ `#30374F`, nút điều hướng hai bên với hiệu ứng kính mờ và tương tác vuốt chạm trên thiết bị di động.
- **Đồng bộ Showcase Dịch Vụ & Thiết Bị In 3D (`2261:5528`)**: Tích hợp các khối giới thiệu dịch vụ in 3D chủ lực (In sợi FDM, In nhựa lỏng SLA, Gia công hoàn thiện bề mặt) với hiệu ứng kính mờ, viền `dark-border` và dải màu gradient xanh thương hiệu `#2970FF`.
- **Đồng bộ Danh Mục Sản Phẩm Mẫu 3 Hàng (`2261:5563`)**: Cấu trúc 3 hàng carousel hiển thị sản phẩm theo phân loại: Móc khoá - Phụ kiện, Đồ decor - Quà tặng, Gia công kỹ thuật số công nghiệp.
- **Đồng bộ Khối Câu Chuyện Thương Hiệu Brand Story (`2261:5696`)**: Bố cục 2 cột thể hiện thông điệp "Hành trình đưa sáng tạo vượt ra khỏi màn hình", ảnh nhà sáng lập và chứng nhận xưởng in 3D.
- **Đồng bộ Khối Kết Nối & Biểu Mẫu Tư Vấn Nhanh (`2261:5748`)**: Form liên hệ báo giá in 3D nhanh gọn, tối ưu trải nghiệm nhập liệu bằng các Base Components (`BaseInput`, `BaseButton`, `BaseSelect`).
- **Tối ưu Responsive & Animation**: Kiểm soát 100% không tràn khung ngang (No horizontal overflow) từ viewport 375px đến 1440px+, tích hợp hiệu ứng chuyển động GSAP mượt mà và Swiper Touch Navigation.
- **Tự động hóa kiểm thử Playwright Visual QA**: Viết kịch bản E2E kiểm tra toàn bộ các section trên Trang chủ ở độ phân giải Desktop (1440x900) và Mobile (375x667).

## Capabilities

### New Capabilities

- `homepage`: Đồng bộ toàn diện hệ thống giao diện và trải nghiệm tương tác Trang chủ HH Maker chuẩn phong cách Dark Tech theo bản thiết kế Figma Node `2261:5474` (Hero `2261:5475` và Frame 236 `2261:5502`), bao gồm Hero Banner, Featured Carousel, Service Showcase, Product Categories, Brand Story và Fast Contact.

### Modified Capabilities

*(Không có. Toàn bộ các yêu cầu của Layout Shell, Base Components và Process 5 bước kế thừa nguyên vẹn từ các spec trước đó).*

## Impact

- **Mã nguồn Frontend bị tác động**:
  - `resources/Frontend/js/Pages/Home/Index.vue`: Điều phối thứ tự và dữ liệu các section.
  - `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`: Tinh chỉnh thẻ Hero, text và huy hiệu xoay tròn SVG.
  - `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`: Chuẩn hóa kích thước thẻ 264x419px, hiệu ứng cuộn Swiper/Track.
  - `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`: Chuẩn hóa card dịch vụ in 3D theo tông Dark Tech.
  - `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`: Chuẩn hóa 3 hàng carousel danh mục sản phẩm.
  - `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`: Hoàn thiện bố cục câu chuyện thương hiệu.
  - `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`: Chuẩn hóa form liên hệ nhanh và thông tin xưởng in.
- **Thư viện & Assets**:
  - Tải và đồng bộ ảnh gốc từ Figma (`image 8` hoặc asset webp tối ưu) vào thư mục `public/assets/images/homepage/`.
  - Tận dụng Swiper và GSAP đã có sẵn trong `package.json`.
- **Hệ thống Backend & Database**: Không thay đổi API routes hay Database schema. Trang chủ sử dụng controller `HomeController@index` và các props dữ liệu có sẵn.
