# Proposal: setup-products-multilingual-i18n

## 1. Giới thiệu & Bối cảnh
Dự án HH Maker đã xây dựng thanh Header đa ngôn ngữ hỗ trợ chuyển đổi giữa Tiếng Việt (`VIE`) và Tiếng Anh (`ENG`). Tuy nhiên, trang Danh mục Sản phẩm (`/san-pham` / `/en/products`) và trang Chi tiết Sản phẩm (`/san-pham/{slug}` / `/en/products/{slug}`) vẫn còn chứa nhiều chuỗi giao diện, dữ liệu bộ lọc, danh mục, thông số kỹ thuật in 3D và 16 sản phẩm mẫu (mockProducts) được fix cứng bằng Tiếng Việt.

Khi người dùng chuyển sang ngôn ngữ Tiếng Anh (`ENG`), giao diện Catalog và PDP cần phản ánh 100% ngữ cảnh Tiếng Anh chuyên ngành in 3D và gia công cơ khí chính xác, bảo đảm tính nhất quán thương hiệu, trải nghiệm người dùng quốc tế mượt mà và tối ưu hóa SEO đa ngôn ngữ.

## 2. Mục tiêu cốt lõi
1. **Mở rộng từ điển UI chuyên ngành in 3D (`resources/lang/{vi,en}/ui.php`)**:
   - Bổ sung toàn diện các token: Danh mục, Nhu cầu lọc, Thuộc tính kỹ thuật (FDM, Resin, SLA, Dung sai, Độ chính xác, Mật độ ruột, Đầu phun, Vật liệu hỗ trợ...), Tab PDP, Nút hành động, Toast thông báo, Banner CTA.
2. **Song ngữ hóa 100% trang Danh mục Sản phẩm (`Product/Index.vue`)**:
   - Sử dụng hàm dịch `tt(...)` cho tiêu đề trang, eyebrow, breadcrumb, toolbar, bộ lọc, sắp xếp (Popular, Newest, Name A-Z) và empty state.
   - Cung cấp dữ liệu Tiếng Anh cho 16 sản phẩm mẫu (`mockProducts`): tên, danh mục, mục đích sử dụng, mô tả kỹ thuật chuẩn xác theo locale hiện tại (`en`).
   - Sử dụng Ziggy localized routes cho link thẻ sản phẩm (`route('products.show', { slug: ... })`).
3. **Song ngữ hóa các component liên quan đến sản phẩm**:
   - `ProductFilter.vue`: Danh mục, Lọc theo nhu cầu, Tìm kiếm placeholder, Box tư vấn tuỳ chỉnh, Nút Reset bộ lọc.
   - `ProductCard.vue`: Mô tả mặc định, Best choice badge, Link localized.
   - `Show.vue`: Breadcrumb, Badges, 3 Tabs ("Overview", "3D Printing Specs", "Product Customization"), Story blocks, 3 bước quy trình đặt in, Toast thông báo.
   - `SpecTable.vue`: Nhãn thông số kỹ thuật in 3D (Công nghệ in, Vật liệu, Kích thước mẫu, Chiều cao lớp in, Đầu phun, Mật độ ruột, Vật liệu hỗ trợ) và lưu ý kỹ thuật.
   - `VariantSelector.vue`: Nhãn vật liệu gợi ý, màu sắc, khả năng tùy chỉnh.
   - `RelatedProducts.vue` & `ProductCtaBanner.vue`: Tiêu đề, mô tả và nút CTA tư vấn.
4. **Cập nhật Backend & Database**:
   - Bổ sung an toàn bản ghi dịch Tiếng Anh (`en`) trong `product_translations` (ID 1) và `product_category_translations` (ID 1) (KHÔNG drop/truncate/delete data).
   - Localize SEO tags (Title, Description) trong `ProductController.php` theo `current_locale()`.
5. **Đồng bộ hóa & Kiểm thử chất lượng**:
   - Chạy lệnh biên dịch i18n & Ziggy routes.
   - Build frontend xanh sạch (`yarn frontend:build`).
   - Viết test Playwright E2E (`tests/e2e/products-multilingual.spec.ts`) kiểm tra toàn bộ luồng chuyển đổi VIE <-> ENG và chụp ảnh màn hình bằng chứng thực tế.

## 3. Tác động kiến trúc
- **Frontend**: Khai thác sức mạnh của `tt(...)` mixin tích hợp Vue I18n đã có trong dự án, đảm bảo reactivity khi chuyển đổi route / locale.
- **Backend**: Tận dụng cơ chế `Astrotomic/Translatable` và `Route::localized()` đã cấu hình sẵn trong Laravel, không phá vỡ bất kỳ quy ước nào.
