# Tasks: Chuẩn Hóa Trang Sản Phẩm Khớp 100% Theo Figma Option 3

## 1. Tinh Chỉnh Sidebar 2 Card trong ProductFilter.vue

- [x] 1.1 Tách rõ ràng 2 Card trong `ProductFilter.vue` (Card 1 cao ~464px, Card 2 cao ~156px với khoảng cách 12px `gap-3`). Kiểm tra bằng cách đọc cấu trúc template trong file.
- [x] 1.2 Cập nhật cụm "LỌC THEO NHU CẦU": Đặt nhãn text ở bên trái và ô checkbox 16x16px ở bên phải với `justify-between` theo đúng Figma Node `2390:1536`.

## 2. Chuẩn Hóa Khung Chứa và Lưới Sản Phẩm trong Product/Index.vue

- [x] 2.1 Căn chỉnh container chính đạt chuẩn 1152px lọt lòng trên màn hình 1440px desktop (`max-w-[1152px] mx-auto` hoặc `max-w-[1440px] px-4 sm:px-6 xl:px-[144px]`).
- [x] 2.2 Đảm bảo lưới sản phẩm 3 cột 867px (`gap-6`) và thẻ sản phẩm 273px x 419px hiển thị chuẩn xác và đều đặn.

## 3. Build & Visual Verification

- [x] 3.1 Chạy `yarn frontend:build` để biên dịch cả client bundle và SSR bundle thành công với exit code 0.
- [x] 3.2 Viết và chạy test Playwright E2E `tests/e2e/product-option3-verify.spec.ts` xác thực bố cục Option 3: Sidebar 240px, 2 card lọc, lưới sản phẩm 3 cột và phân trang.
