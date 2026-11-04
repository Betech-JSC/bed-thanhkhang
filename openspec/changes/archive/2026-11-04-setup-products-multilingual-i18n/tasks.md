# Tasks: setup-products-multilingual-i18n

- [x] 1. Khởi tạo OpenSpec Change và validate cấu trúc <!-- id: task-01-init-openspec -->
- [x] 2. Mở rộng từ điển UI song ngữ trong `resources/lang/{vi,en}/ui.php` <!-- id: task-02-expand-i18n-dictionary -->
- [x] 3. Song ngữ hóa trang Danh mục Sản phẩm (`Product/Index.vue`) và 16 mockProducts <!-- id: task-03-localize-product-listing -->
- [x] 4. Song ngữ hóa bộ lọc `ProductFilter.vue` và thẻ `ProductCard.vue` <!-- id: task-04-localize-filter-and-card -->
- [x] 5. Song ngữ hóa trang Chi tiết Sản phẩm `Product/Show.vue` (3 tabs, story, process) <!-- id: task-05-localize-product-pdp -->
- [x] 6. Song ngữ hóa các component chi tiết `SpecTable.vue`, `VariantSelector.vue`, `RelatedProducts.vue`, `ProductCtaBanner.vue` <!-- id: task-06-localize-pdp-components -->
- [x] 7. Bổ sung bản ghi Tiếng Anh trong Database (`product_translations`, `product_category_translations`) và localize SEO trong `ProductController.php` <!-- id: task-07-seed-db-and-controller -->
- [x] 8. Biên dịch i18n & Ziggy routes, chạy build frontend (`yarn frontend:build`) <!-- id: task-08-generate-and-build -->
- [x] 9. Viết kịch bản kiểm thử E2E Playwright và chụp ảnh màn hình bằng chứng <!-- id: task-09-playwright-e2e-tests -->
