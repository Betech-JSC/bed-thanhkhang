# Proposal: Xây dựng Trang Chủ Thành Khang Chuẩn Figma & CMS-First (`feat-page-home`)

## Why

Sau khi hoàn thiện nền tảng Design Tokens (`Open Sans`, bảng màu công nghiệp xanh/sáng `#005EA4`, `#00284B`, `#C9E0FF`, `#FAFAFA`) và Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`) trong `setup-core-from-home`, Trang Chủ (`resources/Frontend/js/Pages/Home/Index.vue`) hiện vẫn đang sử dụng bố cục và giao diện tối cũ (`bg-dark-bg`), đồng thời kết quả kiểm toán dữ liệu CMS thực tế (`database/database.sqlite`) cho thấy nhiều vùng nội dung Trang Chủ đang trống (`sliders = 0`, `posts type=POST = 0`, chưa có dữ liệu About/Why Choose Us) và `HomeController::index()` có lỗi truy vấn khiến `categories = []` (do sản phẩm gắn vào danh mục lá cấp 3 thay vì danh mục gốc cấp 1).

Cần triển khai Trang Chủ chuẩn 100% theo Figma (`fileKey: Gu7RyTUDWaupomGrOH5EZO`, frame `595:15360` — *"Homepage Thành Khang option 6"*, 1440×6478) theo chiến lược **cuốn chiếu 8 giai đoạn (Sequential Stages)**, kết nối 100% dữ liệu sống từ CMS (**CMS-First, Zero Hardcoded Mock**) và khóa tỷ lệ banner `aspect-ratio` chuẩn Desktop & Mobile.

## What Changes

Triển khai tuần tự 7 Section của Trang Chủ + 1 Giai đoạn Nghiệm thu cuối (chỉ chạy `yarn frontend:build` sau khi hoàn tất cả 7 Section theo yêu cầu người dùng):

1. **Stage 1 — Hero Banner (`595:15536`)**:
   - Tạo seeder `ThanhKhangHomeSliderSeeder` và migration idempotent `2026_11_22_000001_seed_home_hero_sliders_data.php` nạp 3 slide `HOME_SLIDER` song ngữ VI/EN trỏ về ảnh cục bộ `/assets/images/homepage/hero-banner-1.png`.
   - Chuẩn hóa `HomeController::index()` trả về mảng tuần tự `sliders`.
   - Tái cấu trúc `HeroSection.vue` (khóa tỷ lệ `aspect-[375/480] md:aspect-[1440/582]`, trượt ngang `translate-x`, zero `opacity: 0` flash) và cập nhật wrapper sáng trong `Pages/Home/Index.vue` (khai báo tường minh `layout: MainLayout`).
2. **Stage 2 — About Intro ("Về chúng tôi")**:
   - Bổ sung hỗ trợ cấu hình CMS (settings/slider) + migration idempotent cho 2 đoạn giới thiệu, 3 hình ảnh minh họa và nút CTA; nâng cấp `BrandStorySection.vue`.
3. **Stage 3 — Product Categories ("Danh mục sản phẩm")**:
   - **GIỮ NGUYÊN 100%** 4 danh mục gốc hiện có của khách hàng (`Linh kiện thang cuốn`, `Linh kiện thang nâng ô tô`, `Hệ thống băng tải hành lý`, `Linh kiện dây chuyền đóng gói`) và toàn bộ danh mục con/sản phẩm liên quan.
   - Sửa lỗi truy vấn trong `HomeController::index()` để đệ quy nhận sản phẩm ở danh mục lá thay vì trả mảng rỗng; nâng cấp giao diện lưới danh mục trong `ProductsSection.vue`.
4. **Stage 4 — Why Choose Us ("Vì sao khách hàng tin tưởng chọn Thành Khang?")**:
   - Bổ sung cấu hình CMS + migration idempotent cho 5 trụ cột giá trị cốt lõi và ảnh banner nền; tái cấu trúc `ServicesSection.vue`.
5. **Stage 5 — Process ("Quy trình")**:
   - Cập nhật hiển thị Trang Chủ theo đúng **4 bước chuẩn Figma** (đồng bộ với dữ liệu CMS/Controller) + migration idempotent ảnh banner nền quy trình; nâng cấp `ProcessSection.vue`.
6. **Stage 6 — Blog / News ("Tin tức")**:
   - Tạo seeder/migration idempotent nạp 3 bài viết mẫu chuẩn Figma (`type='POST'`) và hiển thị lưới 3 thẻ tin tức liên kết động từ `featured_posts`.
7. **Stage 7 — Contact & Quote Form ("KẾT NỐI & NHẬN BÁO GIÁ")**:
   - Nâng cấp `ContactSection.vue` liên kết thông tin từ `$page.props.global` và gửi biểu mẫu trực tiếp tới endpoint `contact.store` (`POST /contacts` / `POST /en/contacts`) kèm đầy đủ trạng thái loading/validation/success/error.
8. **Stage 8 — Final Build, AST Sync & Playwright Visual QA**:
   - Chạy `yarn frontend:build`, cập nhật `graphify . --code-only`, và nghiệm thu Playwright Visual QA trên Desktop (`1440×900`) và Mobile (`375×812`).

## Capabilities

### New Capabilities
- `home-page`: Trang Chủ Thành Khang đầy đủ 7 section chuẩn Figma `595:15360`, kết nối 100% dữ liệu động CMS qua migration idempotent, khóa tỷ lệ banner `aspect-ratio`, hoạt ảnh trượt ngang chuẩn LUXI, tương thích Desktop 1440px & Mobile 375px.

### Modified Capabilities
- `HomeController@index`: Sửa lỗi truy vấn danh mục sản phẩm (nhận cây danh mục lá) và chuẩn hóa payload `sliders`, `about`, `why_choose_us`, `process_steps`, `featured_posts`.

## Impact

- **Tài nguyên cục bộ (`public/assets/`)**:
  - `public/assets/images/homepage/hero-banner-1.png` (và ảnh các section 2–6 tải theo từng Stage)
  - `public/assets/svg/homepage/hero-effect-top.svg`, `public/assets/svg/icons/hero-chevron-left.svg`
- **Backend / Database Migrations (Idempotent)**:
  - `database/seeders/ThanhKhangHomeSliderSeeder.php` & `database/migrations/2026_11_22_000001_seed_home_hero_sliders_data.php` (và các migration idempotent tương ứng cho Stage 2, 4, 5, 6)
  - `app/Http/Controllers/Frontend/HomeController.php`
- **Frontend Vue Components**:
  - `resources/Frontend/js/Pages/Home/Index.vue`
  - `resources/Frontend/js/Pages/Home/Components/{HeroSection,BrandStorySection,ProductsSection,ServicesSection,ProcessSection,ContactSection}.vue` (tái sử dụng component có sẵn theo Graphify AST)

## Phạm vi an toàn (Strict Safe Guard)

- **Tuyệt đối KHÔNG xóa file** (không `rm` các component cũ đang được trang khác hoặc lịch sử tham chiếu).
- **Tuyệt đối KHÔNG đụng phá hủy dữ liệu khách hàng**: Cấm sửa/xóa `product_categories` (4 danh mục gốc & 22 danh mục con), `products` (103 sản phẩm), `policies` (6 chính sách), hay các bản ghi `settings` thương hiệu hiện có. Cấm chạy `migrate:fresh`, `drop`, `truncate`.
- **Dừng kiểm duyệt theo từng Stage**: Hoàn thành từng Stage độc lập, không chạy `yarn frontend:build` sớm cho đến Stage 8 theo chỉ thị người dùng.
