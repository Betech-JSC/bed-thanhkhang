# Tasks: Đồng Bộ 100% Bố Cục và Cỡ Chữ Header Menu Theo Figma (Node 2352:10717)

## 1. Khởi Tạo OpenSpec Change
- [x] 1.1 Tạo thư mục OpenSpec `sync-header-menu-exact-figma` gồm `proposal.md`, `specs/header/spec.md`, `design.md`, `tasks.md`.
- [x] 1.2 Chạy `npx openspec validate sync-header-menu-exact-figma` xác nhận delta spec hợp lệ 100%.

## 2. Cập Nhật Mã Nguồn Header.vue
- [x] 2.1 Thêm class `antialiased` vào thẻ `<header>` trong `resources/Frontend/js/Components/Header.vue` chống hiện tượng nở chữ (font blooming) trên nền tối.
- [x] 2.2 Gom cụm Nav Tabs và Right Actions vào container `<div class="hidden lg:flex items-center gap-8 xl:gap-[64px]">`, đưa khoảng cách giữa Nav và Right Actions về chuẩn 64px ở 1440px và khoảng cách từ Logo đến cụm menu về chuẩn ~256px.
- [x] 2.3 Loại bỏ `px-1` ở các tab `Link` trong `nav` để viền active underline ôm khít chiều ngang text theo Figma.
- [x] 2.4 Cập nhật class responsive height `h-[64px] lg:h-[80px] xl:h-[111px] xl:py-8` và nav gap `gap-5 xl:gap-6`.
- [x] 2.5 Bảo toàn nguyên vẹn 100% Mobile Drawer, hover dropdown, translation và switch locale.

## 3. Cập Nhật & Xác Minh Test E2E Playwright
- [x] 3.1 Cập nhật test `tests/e2e/header-strict-figma.spec.ts` để assert cấu trúc flex nhóm mới, gap 64px, antialiased, không có px-1 thừa.
- [x] 3.2 Chạy `yarn frontend:build` để biên dịch frontend thành công không lỗi (exit code 0).
- [x] 3.3 Chạy test Playwright trên cả Desktop (1440px, 1280px) và Mobile (375px), xuất ảnh chụp màn hình kiểm chứng tại `tests/e2e/screenshots/`.
- [x] 3.4 Hoàn tất các task `[x]` và báo cáo kết quả chi tiết kèm bằng chứng terminal / screenshots.
