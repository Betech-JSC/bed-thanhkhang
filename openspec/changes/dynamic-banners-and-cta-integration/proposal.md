# Proposal: Kết Nối Động Toàn Diện Các Banner & CTA Trên Website HH Maker

## Why

Website HH Maker đã hoàn thiện giao diện chuẩn Figma cho các trang Giới thiệu, Quy trình gia công 3D, cùng các trang danh mục / chi tiết Sản phẩm và Dịch vụ. Tuy nhiên, nội dung hình ảnh, tiêu đề, mô tả và nút kêu gọi hành động (CTA) tại các vị trí quan trọng vẫn đang được hardcode tĩnh trong các file Vue:
1. **Hero Banner trang Giới thiệu (`About/Index.vue`)**: Chưa kết nối dữ liệu từ CMS Slider/Banner; nội dung, ảnh nền và nút bấm chưa thể tùy biến linh hoạt từ trang quản trị.
2. **Hero Banner trang Quy trình (`Process/Index.vue` & `ProcessHero.vue`)**: Route quy trình đang render view tĩnh qua closure `Inertia::render('Process/Index')`, chưa có Controller chuyên trách để query banner động từ database.
3. **Banner CTA Chân trang Toàn cục (Global CTA Banner)**: Các khối CTA ở chân trang sản phẩm (`ProductCtaBanner.vue`), dịch vụ (`ServiceCta.vue`), quy trình (`ProcessCtaBanner.vue`) và trang giới thiệu chưa dùng chung nguồn dữ liệu toàn cục từ middleware `HandleInertiaFrontendRequests.php`.
4. **Cấu hình Slider Positions lỗi thời (`config/slider.php`)**: Còn lưu giữ các key tàn dư từ nền tảng giáo dục cũ (`BANNER_COURSE`, `BANNER_RESOURCE`, `BANNER_PRIZE`), trong khi thiếu các danh mục vị trí chuẩn cho thương hiệu xưởng in 3D HH Maker.

Mục tiêu của change này là đồng bộ hóa toàn diện hệ thống Banner và CTA từ CMS tới Frontend, cung cấp khả năng quản trị động nhưng đảm bảo cơ chế Fallback tuyệt đối an toàn theo đúng thiết kế gốc Figma khi CMS chưa có dữ liệu.

---

## What Changes

1. **Chuẩn hóa Danh mục Vị trí Banner (`config/slider.php`)**:
   - Khai báo chuẩn hóa 5 vị trí cốt lõi:
     * `HOME_SLIDER` => 'Slider trang chủ' (`banner: false`)
     * `BANNER_ABOUT_HERO` => 'Hero Banner trang Giới thiệu' (`banner: true`)
     * `BANNER_PROCESS_HERO` => 'Hero Banner trang Quy trình' (`banner: true`)
     * `BANNER_GLOBAL_CTA` => 'Banner CTA kêu gọi tư vấn chân trang' (`banner: true`)
     * `BANNER_HOME_FOUNDER` => 'Banner Câu chuyện Founder trang chủ' (`banner: true`)
   - Dọn dẹp các vị trí cũ không dùng (`BANNER_COURSE`, `BANNER_RESOURCE`, `BANNER_PRIZE`).

2. **Backend Controllers & Shared Data**:
   - `app/Http/Controllers/Frontend/HistoryController.php`: Query `Slider::getByPosition('BANNER_ABOUT_HERO')` và truyền sang Inertia view `About/Index` dưới prop `heroBanner`.
   - `app/Http/Controllers/Frontend/ProcessController.php`: Tạo mới controller cho trang Quy trình, query `Slider::getByPosition('BANNER_PROCESS_HERO')` và truyền sang view `Process/Index` dưới prop `heroBanner`.
   - `routes/frontend.php`: Cập nhật route `quy-trinh` (`process.index`) và route `ve-chung-toi` gọi qua Controller.
   - `app/Http/Middleware/HandleInertiaFrontendRequests.php`: Query `Slider::getByPosition('BANNER_GLOBAL_CTA')` và nạp vào `$page.props.globalCtaBanner` cùng `$page.props.data.banner.BANNER_GLOBAL_CTA` để dùng toàn cục.

3. **Frontend Binding & Fallback Figma An Toàn**:
   - `resources/Frontend/js/Pages/About/Index.vue`: Nhận prop `heroBanner`, bind tiêu đề, mô tả, ảnh nền và nút bấm từ CMS; fallback chuẩn xác về thiết kế Figma nếu CMS chưa cấu hình. Kết nối khối CTA chân trang với `globalCtaBanner`.
   - `resources/Frontend/js/Pages/Process/Index.vue` & `ProcessHero.vue`: Nhận prop `heroBanner`, bind động tiêu đề, subtitle, mô tả, ảnh nền; fallback về thiết kế Figma nếu trống.
   - `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`, `resources/Frontend/js/Pages/Services/Components/ServiceCta.vue`, `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue`: Nhận dữ liệu từ `globalCtaBanner` với fallback về thiết kế Cobalt Blue chuẩn Figma.

---

## Capabilities

### New Capabilities
- `dynamic-banners`: Cơ chế nạp và hiển thị động các banner trang Giới thiệu, Quy trình và Banner CTA chân trang toàn cục từ Slider CMS với fallback an toàn.

### Modified Capabilities
- `process-page`: Chuyển đổi định tuyến trang Quy trình sang `ProcessController` và hỗ trợ hero banner động.
- `about-page`: Chuyển đổi hero banner trang Giới thiệu sang dữ liệu động từ `HistoryController`.

---

## Impact
- **Backend**:
  - `config/slider.php`
  - `app/Http/Controllers/Frontend/HistoryController.php`
  - `app/Http/Controllers/Frontend/ProcessController.php` (new)
  - `app/Http/Middleware/HandleInertiaFrontendRequests.php`
  - `routes/frontend.php`
- **Frontend**:
  - `resources/Frontend/js/Pages/About/Index.vue`
  - `resources/Frontend/js/Pages/Process/Index.vue`
  - `resources/Frontend/js/Pages/Process/Components/ProcessHero.vue`
  - `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceCta.vue`
  - `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue`
