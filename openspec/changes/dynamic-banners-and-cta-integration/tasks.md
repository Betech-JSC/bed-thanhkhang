# Tasks: Kết Nối Động Toàn Diện Các Banner & CTA Trên Website HH Maker

## 1. Khởi tạo OpenSpec Change
- [x] 1.1 Khởi tạo change `dynamic-banners-and-cta-integration` <!-- id: 1.1 -->
- [x] 1.2 Tạo `proposal.md` phân tích yêu cầu đồng bộ banner động và fallback Figma <!-- id: 1.2 -->
- [x] 1.3 Tạo `specs/dynamic-banners/spec.md` đặc tả chi tiết các vị trí banner và data contract <!-- id: 1.3 -->
- [x] 1.4 Tạo `design.md` thiết kế kỹ thuật backend controller, middleware và frontend binding <!-- id: 1.4 -->
- [x] 1.5 Tạo `tasks.md` danh sách đầu việc chi tiết <!-- id: 1.5 -->

## 2. Triển khai Code (Implementation)
- [x] 2.1 Cập nhật `config/slider.php`: Chuẩn hóa danh mục vị trí `HOME_SLIDER`, `BANNER_ABOUT_HERO`, `BANNER_PROCESS_HERO`, `BANNER_GLOBAL_CTA`, `BANNER_HOME_FOUNDER`; dọn dẹp các vị trí cũ `BANNER_COURSE`, `BANNER_RESOURCE`, `BANNER_PRIZE` <!-- id: 2.1 -->
- [x] 2.2 Cập nhật `app/Http/Controllers/Frontend/HistoryController.php`: Query `Slider::getByPosition('BANNER_ABOUT_HERO')` và truyền `heroBanner` vào view `About/Index` <!-- id: 2.2 -->
- [x] 2.3 Tạo `app/Http/Controllers/Frontend/ProcessController.php`: Query `Slider::getByPosition('BANNER_PROCESS_HERO')` và render view `Process/Index` với `heroBanner` <!-- id: 2.3 -->
- [x] 2.4 Cập nhật `routes/frontend.php`: Gán route `quy-trinh` sang `ProcessController@index` và `ve-chung-toi` sang `HistoryController@index` <!-- id: 2.4 -->
- [x] 2.5 Cập nhật `app/Http/Middleware/HandleInertiaFrontendRequests.php`: Query và chia sẻ `BANNER_GLOBAL_CTA` vào `$page.props.globalCtaBanner` và `$page.props.data.banner` <!-- id: 2.5 -->
- [x] 2.6 Cập nhật `resources/Frontend/js/Pages/About/Index.vue`: Nhận prop `heroBanner` với fallback Figma cho Hero section và kết nối `globalCtaBanner` cho Section 5 CTA <!-- id: 2.6 -->
- [x] 2.7 Cập nhật `resources/Frontend/js/Pages/Process/Index.vue` & `ProcessHero.vue`: Nhận prop `heroBanner` và bind động tiêu đề, subtitle, mô tả, ảnh nền kèm fallback Figma <!-- id: 2.7 -->
- [x] 2.8 Cập nhật các component CTA chân trang (`ProductCtaBanner.vue`, `ServiceCta.vue`, `ProcessCtaBanner.vue`): Kết nối `globalCtaBanner` với fallback chuẩn thiết kế Cobalt Blue <!-- id: 2.8 -->

## 3. Xác minh & Kiểm thử (Verification)
- [x] 3.1 Chạy `yarn backend:build` xác nhận Admin CMS biên dịch thành công (Exit code 0) <!-- id: 3.1 -->
- [x] 3.2 Chạy `yarn frontend:build` xác nhận Frontend SSR & Client biên dịch thành công (Exit code 0) <!-- id: 3.2 -->
- [x] 3.3 Chạy `php artisan test tests/Feature/ContactFormTest.php` xác nhận tính toàn vẹn hệ thống <!-- id: 3.3 -->
- [x] 3.4 Cập nhật 100% `[x]` vào `tasks.md` <!-- id: 3.4 -->
- [x] 3.5 Báo cáo bằng chứng chi tiết raw terminal output và git diff summary cho agent điều phối <!-- id: 3.5 -->
