# Proposal: Chuẩn Hóa Trang Sản Phẩm Khớp 100% Theo Figma Option 3 (Sidebar Layout)

## Why
Người dùng yêu cầu thực hiện chuẩn hóa trang Danh sách Sản phẩm (`resources/Frontend/js/Pages/Product/Index.vue`) bám sát 100% theo bản thiết kế **Figma Option 3 (Sidebar Layout - Node `2346:669` / `2346:994`)**.
Cần tinh chỉnh và chuẩn hóa cấu trúc component, kích thước lọt lòng 1152px (lề 144px), tách bạch 2 Card trong Sidebar ("Danh mục" 464px và "Tuỳ chỉnh sản phẩm" 156px), định dạng Checkbox lọc theo nhu cầu đúng vị trí bên phải nhãn, và hiển thị lưới 3 cột 867px đồng bộ với Figma.

## What Changes
1. **Chuẩn hóa Khung chứa và Căn lề Desktop**:
   - Khung chứa chính đạt chuẩn 1152px lọt lòng (`max-w-[1152px]` hoặc padding 144px tại viewport 1440px).
   - Phần Intro `Mở đầu` (`2346:702`) căn lề trái chuẩn 144px, Eyebrow "BỘ SƯU TẬP HH MAKER" 14px `#84ADFF`, Tiêu đề "Ý TƯỞNG THÀNH HÌNH" 38px Bold.
2. **Chuẩn hóa Sidebar 240px (`ProductFilter.vue`)**:
   - Tách biệt rõ ràng 2 Card chuẩn Figma:
     * **Card 1 (`2346:996`)**: "DANH MỤC SẢN PHẨM" (chiều cao 464px, bg `#30374F`, bo góc 12px), ô tìm kiếm bo góc 8px kèm icon kính lúp bên phải, 4 danh mục với chevron-right, và 3 mục "LỌC THEO NHU CẦU" với text bên trái và checkbox vuông bên phải chuẩn Figma `2390:1536`.
     * **Card 2 (`2346:998`)**: "Tuỳ chỉnh sản phẩm" (chiều cao 156px, bg `#30374F`, bo góc 12px), tiêu đề 14px, phụ đề 13px, và nút CTA "Liên hệ tư vấn" gradient xanh.
3. **Chuẩn hóa Khu vực Hiển thị Sản phẩm (867px)**:
   - Thanh Toolbar: Breadcrumb bên trái (`Trang chủ > Sản phẩm (16 sản phẩm)`) và Sort Dropdown bên phải (`Sắp xếp: Nổi bật nhất`).
   - Lưới sản phẩm: Chia 3 cột (`grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6`), mỗi card chuẩn 273px x 419px, ảnh 335px, thanh info dưới 84px bg `#30374F`.
   - Phân trang: Điều hướng "Trang trước" và "Trang tiếp theo" với chevron chuẩn Figma `2352:9091`.
4. **Banner CTA Cuối Trang (`ProductCtaBanner.vue`)**:
   - Khớp 100% kích thước 1152px x 183px, gradient `#2970FF` đến `#00359E`, tiêu đề 32px Bold, nút "Nhận tư vấn ngay" 175px x 40px bg `#F5F8FF` chữ đen `#111322`.

## Capabilities

### New Capabilities
- `product-catalog`: Đặc tả cấu trúc và hành vi giao diện trang Danh mục Sản phẩm chuẩn Figma Option 3 (Sidebar Layout `2346:669`).

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/Frontend/js/Pages/Product/Index.vue`
- `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue`
- `resources/Frontend/js/Pages/Product/Components/ProductCard.vue`
- `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue`
- Không ảnh hưởng đến dữ liệu backend hay các trang khác.
