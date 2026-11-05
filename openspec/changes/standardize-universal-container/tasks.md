# Tasks: Chuẩn Hóa Hệ Thống Universal Container Theo Chuẩn Figma Node 2261:5076

## 1. Hạ Tầng CSS và Utility Container

- [x] 1.1 Khai báo class `.app-container` trong `resources/Frontend/scss/app.scss` với `@apply w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8;` và kiểm tra `yarn frontend:build` biên dịch thành công không có lỗi cú pháp.
- [x] 1.2 Cập nhật component chân trang `resources/Frontend/js/Components/Footer.vue` sang class `.app-container` (thay thế `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0`), kiểm tra bằng mắt hoặc snapshot xem nội dung footer có padding 32px trên desktop, không bị dính sát mép trên viewport 1024px.

## 2. Đồng Bộ Các Section Trang Chủ (Home Page)

- [x] 2.1 Chuẩn hóa `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` sang `app-container` (thay thế `container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1240px]`), kiểm tra mép trái nội dung đạt đúng 144px trên viewport 1440px.
- [x] 2.2 Chuẩn hóa `resources/Frontend/js/Pages/Home/Components/HighlightMetrics.vue` sang `app-container` (thay thế `container mx-auto px-4 sm:px-6 lg:px-8`), kiểm tra các thẻ chỉ số gióng thẳng hàng với BrandStory.
- [x] 2.3 Chuẩn hóa `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` sang `app-container` (thay thế `container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]`), kiểm tra lưới dịch vụ đạt đúng bề rộng 1152px trên desktop.
- [x] 2.4 Chuẩn hóa `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` và `FeaturedProductsCarousel.vue` sang `app-container` (thay thế `max-w-[1180px]`), kiểm tra thanh trượt carousel sản phẩm không bị lệch mép so với section phía trên.
- [x] 2.5 Chuẩn hóa `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` sang `app-container` (thay thế `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0`), kiểm tra form liên hệ trang chủ thẳng hàng tuyệt đối với footer.

## 3. Đồng Bộ Trang Sản Phẩm và Dịch Vụ (Products & Services)

- [x] 3.1 Chuẩn hóa `resources/Frontend/js/Pages/Product/Index.vue` và `Product/Show.vue` sang `app-container` (loại bỏ hoàn toàn lớp bọc `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]`), kiểm tra trên màn hình laptop 1280px nội dung không còn bị co lại mức 992px.
- [x] 3.2 Chuẩn hóa `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue` sang `app-container` (loại bỏ lớp `max-w-[1440px] xl:px-[144px]`), kiểm tra banner CTA khớp chuẩn bề rộng nội dung 1152px.
- [x] 3.3 Chuẩn hóa `resources/Frontend/js/Pages/Services/Index.vue` và `Services/Show.vue` sang `app-container` (thay thế `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0`), kiểm tra vùng nội dung đạt đủ 1152px và loại bỏ triệt để hiện tượng lệch 128px khi chuyển đổi qua lại với trang Product.

## 4. Đồng Bộ Các Trang Thông Tin (Process, About, Contact, Policy)

- [x] 4.1 Chuẩn hóa `resources/Frontend/js/Pages/Process/Index.vue` và `ProcessCtaBanner.vue` sang `app-container` (thay thế `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0`), kiểm tra stepper quy trình và banner CTA thẳng hàng.
- [x] 4.2 Chuẩn hóa các khối nội dung body trong `resources/Frontend/js/Pages/About/Index.vue` sang `app-container` (thay thế `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0`), xác nhận khối Hero Card giữ nguyên `max-w-[1280px]`.
- [x] 4.3 Chuẩn hóa `resources/Frontend/js/Pages/Contact.vue` và `Policy.vue` sang `app-container` (thay thế `max-w-[1152px] px-8 xl:px-0` và `container mx-auto max-w-[1152px]`), kiểm tra trang điều khoản và liên hệ đồng bộ mép gióng 144px trên 1440px.

## 5. Kiểm Thử Tích Hợp và Nghiệm Thu Bằng Chứng (Verification & Visual QA)

- [x] 5.1 Biên dịch toàn bộ Frontend bằng `yarn frontend:build` và kiểm tra terminal trả về exit code 0 không có lỗi.
- [x] 5.2 Xây dựng bộ test Playwright E2E tại `tests/e2e/universal-container.spec.ts` kiểm chứng:
  - Mép trái của tất cả các section trên Trang Chủ, Trang Sản phẩm, Trang Dịch vụ tại viewport 1440px đều có tọa độ `left === 144px` (dung sai cho phép ±1px).
  - Độ rộng nội dung tại viewport 1280px của cả trang Sản phẩm và Dịch vụ đều đạt `1152px` (dung sai ±1px).
  - Kiểm tra trên viewport mobile 375px và 768px xác nhận không bị tràn ngang (`scrollWidth <= innerWidth`).
- [x] 5.3 Chạy `npx playwright test tests/e2e/universal-container.spec.ts` và đính kèm raw terminal output cùng ảnh chụp màn hình đạt 100% PASS làm bằng chứng nghiệm thu hoàn tất change.
