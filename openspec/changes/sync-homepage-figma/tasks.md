# Tasks: Đồng bộ Trang chủ HH Maker chuẩn Figma Dark Tech

## 1. Chuẩn Bị Assets & Xác Minh Design Tokens

- [x] 1.1 Kiểm tra và chuẩn bị đầy đủ các tệp hình ảnh đồ họa chất lượng cao trong `public/assets/images/homepage/` (bao gồm `hero-banner.png` từ Figma Node `2261:5478` / `image 8`, `product-sample-warm.png`, `founder-composite.png`). Xác minh bằng lệnh kiểm tra tệp `Test-Path`.
- [x] 1.2 Rà soát và xác minh các lớp tiện ích Tailwind Dark Tech trong `resources/Frontend/tailwind.config.js` (`dark-bg`, `dark-hero`, `dark-surface`, `dark-card`, `dark-border`, `primary`, `text-white`, `text-secondary`, `text-blue`). Xác minh không còn xung đột token.

## 2. Hoàn Thiện Hero Section Chuẩn Figma

- [x] 2.1 Cập nhật `resources/Frontend/js/Pages/Home/Components/HeroSection.vue`: Khóa cứng khung thẻ Hero chuẩn 1279x616px (`bg-[#0B0D19] border border-white/10 rounded-[16px]`), hiển thị ảnh nền artwork in 3D công nghệ cao với `loading="eager"`, fallback nền tối khi tải ảnh.
- [x] 2.2 Đồng bộ cụm nội dung văn bản Hero (Figma Node `2261:5481`): Subtitle "VỀ CHÚNG TÔI" màu `#D1E0FF`, tiêu đề H1 kích thước chuẩn 38px font-bold màu `#FAFAFA` ("HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D"), đoạn mô tả 16px line-height 26px màu `#D1E0FF`.
- [x] 2.3 Đồng bộ cụm nút bấm kép (Figma Node `2261:5486`): Nút chính "Khám phá ngay" bo góc 8px nền `#F5F8FF` chữ `#111322` hover sáng, nút phụ "Nhận tư vấn thiết kế" viền mờ `#F5F8FF`.
- [x] 2.4 Tinh chỉnh huy hiệu xoay tròn SVG `FROM IDEAS TO REALITY - ` kích thước 114x114px (Figma Node `2261:5499`): Sử dụng SVG `<textPath>` nhẹ và sắc nét kèm icon mũi tên 45° màu `#FAFAFA` ở tâm trong suốt, hoạt họa quay tròn mượt mà và tự động ẩn gọn gàng trên mobile nhỏ.

## 3. Đồng Bộ Các Content Sections

- [x] 3.1 Cập nhật `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue`: Khóa cứng kích thước thẻ sản phẩm chuẩn Figma 264x419px bo góc 12px (ảnh trên 335px nền `#0A0D14`, chân thẻ dưới 84px nền `#30374F`), tích hợp 2 nút điều hướng trượt Prev/Next kính mờ và vuốt chạm cảm ứng mượt mà.
- [x] 3.2 Cập nhật `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`: Chuẩn hóa các thẻ dịch vụ in 3D (FDM, SLA, Hoàn thiện chế tác) theo phong cách Dark Tech, hiệu ứng kính mờ `backdrop-blur-sm`, viền `border-dark-border` và dải màu gradient xanh `#2970FF`.
- [x] 3.3 Cập nhật `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue`: Đồng bộ 3 hàng carousel danh mục sản phẩm mẫu (Móc khoá & Phụ kiện, Đồ decor & Quà tặng, Gia công kỹ thuật số), tiêu đề nhóm rõ ràng và liên kết "Xem tất cả" sang danh mục sản phẩm.
- [x] 3.4 Cập nhật `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue`: Đồng bộ bố cục 2 cột (ảnh nhà sáng lập/xưởng in 3D bên trái, bài tự sự "Hành trình đưa sáng tạo vượt ra khỏi màn hình" kèm các chỉ số cam kết độ chính xác < 0.1mm bên phải).
- [x] 3.5 Cập nhật `resources/Frontend/js/Pages/Home/Components/ContactSection.vue`: Chuẩn hóa form liên hệ báo giá nhanh sử dụng các component `BaseInput` và `BaseButton`, hiển thị thông báo phản hồi thành công và kiểm tra hợp lệ thông tin liên lạc.

## 4. Tích Hợp Điều Phối Trang Chủ & Props

- [x] 4.1 Cập nhật `resources/Frontend/js/Pages/Home/Index.vue`: Điều phối thứ tự hiển thị tuần tự 7 section, xử lý sanitize URL ảnh slider từ backend, cấu hình đầy đủ thẻ SEO Meta tags (Title, Description, OG tags).
- [x] 4.2 Rà soát và dọn dẹp các import thừa hoặc thẻ lặp lại (như `HomeFooter`) để đảm bảo Trang chủ kế thừa trọn vẹn `MainLayout.vue` toàn cục.

## 5. Build, Graphify & Playwright Visual QA

- [x] 5.1 Thực thi biên dịch toàn diện giao diện Frontend qua lệnh `yarn frontend:build` để xác nhận mã nguồn Vue 3 và Vite build thành công 100% không phát sinh lỗi cú pháp hay thiếu import.
- [x] 5.2 Chạy cập nhật bản đồ cây cú pháp AST kiến trúc dự án qua lệnh `graphify . --code-only`.
- [x] 5.3 Tạo kịch bản kiểm thử tự động Playwright E2E tại `tests/e2e/homepage.spec.ts` kiểm chứng toàn bộ các section trên Trang chủ ở độ phân giải Desktop (1440x900) và Mobile (375x667).
- [x] 5.4 Chạy kiểm thử Playwright và chụp ảnh màn hình nghiệm thu visual evidence, xác nhận không có hiện tượng vỡ layout hay thanh cuộn ngang ngoài ý muốn (`document.documentElement.scrollWidth <= window.innerWidth`).
