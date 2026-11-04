# Technical Design: Đồng bộ Trang chủ HH Maker chuẩn Figma Dark Tech

## Context

Trang chủ hiện tại tại `resources/Frontend/js/Pages/Home/Index.vue` được cấu thành từ 7 section con trong thư mục `resources/Frontend/js/Pages/Home/Components/`:
- `HeroSection.vue`
- `FeaturedProductsCarousel.vue`
- `ServicesSection.vue`
- `ProductsSection.vue`
- `BrandStorySection.vue`
- `ProcessSection.vue` (đã chuẩn hóa theo spec `homepage-process` tại Node `2261:5723`)
- `ContactSection.vue`

Kiến trúc hiện tại đã có layout shell `MainLayout.vue` chuẩn Dark Tech (Header cố định, Footer 4 cột), các token màu và kích thước chữ semantic trong `tailwind.config.js` (`dark-bg: #111322`, `dark-hero: #0B0D19`, `primary: #2970FF`, `text-white: #FAFAFA`, `text-secondary: #B9C0D4`, `text-blue: #D1E0FF`, `display-hero: 38px`), cùng bộ Base Components (`BaseButton`, `BaseInput`, `BaseSelect`, `BaseBadge`, `BaseIcon`).

Để đạt độ chính xác pixel-perfect so với Figma Node `2261:5474` (Hero `2261:5475` và Frame 236 `2261:5502`), thiết kế kỹ thuật này tập trung vào việc tinh chỉnh cấu trúc chi tiết, tích hợp token Figma, đồng bộ assets hình ảnh chất lượng cao và cơ chế tương tác mượt mà.

## Goals / Non-Goals

### Goals
- Chuẩn hóa layout và tỷ lệ hiển thị của từng section trên Trang chủ theo đúng thông số Figma.
- Đảm bảo hiển thị hoàn hảo ở 2 độ phân giải mốc: Desktop 1440px và Mobile 375px.
- Sử dụng trực tiếp các token màu và typography Dark Tech từ Tailwind, loại bỏ hoàn toàn các mã màu inline hardcode không theo chuẩn.
- Tối ưu hiệu ứng xoay huy hiệu `FROM IDEAS TO REALITY` bằng SVG `<textPath>` nhẹ, sắc nét ở mọi độ phân giải.
- Tối ưu băng thông ảnh banner và ảnh sản phẩm bằng định dạng WebP/PNG tối ưu tải nhanh qua Vite.
- Thiết lập kịch bản Playwright E2E tự động xác thực hiển thị và không có lỗi vỡ khung ngang trên cả Desktop lẫn Mobile.

### Non-Goals
- Không thay đổi schema Database hoặc API Controller backend (Trang chủ tiếp tục dùng `HomeController@index` và props hiện hữu).
- Không sửa đổi cấu trúc routing của hệ thống (`routes/frontend.php`).
- Không sửa đổi `MainLayout.vue` hoặc `Header`/`Footer` đã được chuẩn hóa trong spec `layout-shell`.

## Decisions

### Decision 1: Cấu trúc Component và Tái sử dụng Base UI
- **Lựa chọn:** Giữ nguyên mô hình phân tách component theo từng section trong `resources/Frontend/js/Pages/Home/Components/`, bọc bên ngoài bởi `Index.vue`. Tái sử dụng triệt để `BaseButton.vue` cho các nút bấm CTA và `BaseInput.vue` cho form liên hệ nhanh tại `ContactSection.vue`.
- **Lý do:** Giữ tính module hóa cao, dễ bảo trì, dễ viết unit test/Playwright test độc lập, tuân thủ nguyên tắc không refactor kiến trúc ngoài ý muốn.
- **Phương án thay thế đã cân nhắc:** Gộp tất cả vào 1 file `Index.vue` lớn (Bị loại bỏ vì gây khó bảo trì và vi phạm SRP).

### Decision 2: Mapping Token Figma sang Tailwind Design Tokens
- **Bảng ánh xạ Figma -> Tailwind Token:**
  - `Hero Frame` (1279x616px): `max-w-[1279px] min-h-[520px] lg:h-[616px] rounded-[16px] bg-[#0B0D19] border border-white/10`
  - `Màu nền Trang chủ`: `#111322` (`bg-dark-bg`)
  - `Màu thẻ Card / Khối nội dung`: `#171B30` (`bg-dark-surface` / `bg-dark-card`)
  - `Chân thẻ sản phẩm nổi bật`: `#30374F` (`bg-[#30374F]`)
  - `Đường viền công nghệ`: `#404968` (`border-dark-border`)
  - `Xanh thương hiệu nổi bật`: `#2970FF` (`text-primary` / `bg-primary`)
  - `Màu chữ chính`: `#FAFAFA` (`text-[#FAFAFA]` / `text-white`)
  - `Màu chữ phụ / nhãn`: `#D1E0FF` (`text-[#D1E0FF]`) và `#B9C0D4` (`text-secondary`)
  - `Typography Tiêu đề chính`: `text-display-hero` (`38px / 46px font-bold`)
  - `Typography Tiêu đề phụ / Card`: `text-display-card` (`24px / 30px`) và `text-h3` (`20px / 28px`)
- **Lý do:** Tận dụng 100% token đã định nghĩa trong `tailwind.config.js` theo spec `design-tokens`, giữ code sạch và dễ mở rộng.

### Decision 3: Cơ chế tương tác Trượt & Carousel (Featured Products & Product Rows)
- **Lựa chọn:** Sử dụng thanh cuộn mượt mà có kiểm soát với `overflow-x-auto scroll-smooth no-scrollbar` kết hợp `scrollLeft` và nút điều hướng Prev/Next cho `FeaturedProductsCarousel.vue`. Tích hợp Swiper cho các hàng sản phẩm nếu có yêu cầu autoplay hoặc phân trang dạng dot.
- **Lý do:** Nhẹ, không phụ thuộc nặng vào thư viện bên thứ ba cho track đơn giản, đảm bảo phản hồi tức thì với vuốt chạm tự nhiên (native touch) trên iOS và Android.
- **Phương án thay thế đã cân nhắc:** Dùng slider jQuery/Slick (Bị loại bỏ do lỗi thời và xung đột với Vue 3 SPA).

### Decision 4: Huy hiệu tròn xoay "FROM IDEAS TO REALITY"
- **Lựa chọn:** Dựng bằng thẻ `<svg>` inline sử dụng `<defs><path id="circlePath" ... /></defs>` kết hợp `<textPath>` và CSS animation `@keyframes spin` với `will-change: transform`. Icon mũi tên hướng lên bên phải 45° đặt ở tâm bằng thẻ SVG trong suốt (transparent) kế thừa màu `#FAFAFA`.
- **Lý do:** Đảm bảo độ nét tuyệt đối ở màn hình Retina/High-DPI, dung lượng < 1KB, có thể đổi chữ đa ngôn ngữ qua i18n mà không cần xuất ảnh tĩnh từ Figma.

### Decision 5: Chiến lược Tối ưu Ảnh & Asset
- **Lựa chọn:** Đặt ảnh chất lượng cao vào `public/assets/images/homepage/`:
  - `hero-banner.png`: Ảnh artwork công nghệ in 3D chính thức (tối ưu nén lossless WebP/PNG).
  - `product-sample-*.png`: Ảnh sản phẩm mẫu độ nét cao.
  - Sử dụng thuộc tính `loading="eager"` cho Hero Banner để tối ưu LCP (Largest Contentful Paint) và `loading="lazy"` cho các section phía dưới.

## Risks / Trade-offs

- **[Rủi ro] Hero Banner tải chậm ảnh hưởng chỉ số Core Web Vitals (LCP)**  
  → **Giải pháp:** Sử dụng ảnh nén tối ưu WebP/PNG, preload hoặc eager loading cho ảnh Hero, fallback màu nền `#0B0D19` ngay lập tức khi ảnh đang tải.
- **[Rủi ro] Thanh cuộn ngang ngoài ý muốn trên Mobile do kích thước cố định của carousel**  
  → **Giải pháp:** Đặt `overflow-hidden` trên container bao ngoài trang và section, dùng `w-full` kết hợp padding tương đối (`px-4 sm:px-6`).
- **[Rủi ro] Lỗi console JavaScript khi các props backend (`sliders`, `products`) trả về mảng rỗng**  
  → **Giải pháp:** Thiết lập `default: () => []` cho toàn bộ props, kèm fallback mock data thẩm mỹ đảm bảo trang không bao giờ bị trắng hoặc vỡ layout.

## Migration Plan

1. **Chuẩn bị:** Tải và kiểm tra các assets hình ảnh vào `public/assets/images/homepage/`.
2. **Thực thi:** Lần lượt cập nhật từng component từ trên xuống dưới (`HeroSection` -> `FeaturedProductsCarousel` -> `ServicesSection` -> `ProductsSection` -> `BrandStorySection` -> `ContactSection` -> `Index.vue`).
3. **Kiểm tra an toàn (Zero Breaking):** Chạy `yarn frontend:build` để xác nhận biên dịch không lỗi cú pháp.
4. **Kiểm chứng trực quan (Evidence-based):** Chạy Playwright E2E suite `tests/e2e/homepage.spec.ts` trên cả 2 viewport Desktop và Mobile, chụp ảnh màn hình lưu bằng chứng nghiệm thu.
5. **Rollback:** Nếu phát sinh lỗi ngoài ý muốn, khôi phục lại mã nguồn qua git mà không ảnh hưởng cơ sở dữ liệu.
