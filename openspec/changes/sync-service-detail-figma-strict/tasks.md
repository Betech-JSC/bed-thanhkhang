# Tasks: Đồng Bộ Giao Diện Chi Tiết Dịch Vụ Figma Chuẩn 100% Node 2367:2088 & Điều Hướng Thông Minh

## 1. Khởi Tạo OpenSpec Change
- [x] 1.1 Tạo thư mục OpenSpec `sync-service-detail-figma-strict` gồm `proposal.md`, `specs/service-detail/spec.md`, `design.md`, `tasks.md`.
- [x] 1.2 Chạy `npx openspec validate sync-service-detail-figma-strict` xác nhận spec hợp lệ 100%.

## 2. Gắn Link Điều Hướng Thông Minh Tại Trang Chủ & Trang Danh Sách Dịch Vụ
- [x] 2.1 Cập nhật `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`: Chuyển đổi 3 thẻ dịch vụ thành component `<Link>` bấm được dẫn về `/dich-vu/{slug}`.
- [x] 2.2 Cập nhật `resources/Frontend/js/Pages/Services/Index.vue`: Đảm bảo cung cấp slug chuẩn cho cả 3 dịch vụ (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`).
- [x] 2.3 Cập nhật `resources/Frontend/js/Pages/Services/Components/ServiceBlock.vue`: Gắn link vào thẻ ảnh minh họa 3D, tiêu đề H2, và thêm nút hành động phụ "Tìm hiểu chi tiết →" trỏ về `/dich-vu/{slug}`.

## 3. Hoàn Thiện & Tinh Chỉnh Trang Chi Tiết Dịch Vụ Chuẩn Figma Node 2367:2088
- [x] 3.1 Cập nhật `resources/Frontend/js/Pages/Services/Show.vue`: Đảm bảo khung lưới `max-w-[1152px] mx-auto` đối xứng 144px ở desktop 1440px, quản lý scrollspy state và dynamic SEO head.
- [x] 3.2 Tinh chỉnh `ServiceHero.vue`: Breadcrumbs chuẩn trỏ về `/dich-vu`, H1 38px/46px Bold `#FAFAFA`, khung showcase 1152x310px 3 cột chuẩn Figma.
- [x] 3.3 Tinh chỉnh `ServiceToc.vue`: Sticky top-28, 4 mục điều hướng, scrollspy active border `#2970FF`, cuộn mượt smooth scroll.
- [x] 3.4 Tinh chỉnh `ServiceArticleContent.vue`: 840px, gap 64px, 4 sections kỹ thuật chuẩn nội dung và kiểu dáng Figma Node 2367:2196.
- [x] 3.5 Tinh chỉnh `ServiceRelated.vue`: 2 card liên quan kích thước 564x220px, link chuẩn `/dich-vu/{slug}`.
- [x] 3.6 Tinh chỉnh `ServiceCta.vue`: Banner 1152x183px gradient 90deg, nút "Nhận tư vấn ngay" liên kết đến trang liên hệ.

## 4. Biên Dịch Frontend & Kiểm Thử E2E Playwright Toàn Diện
- [x] 4.1 Chạy `yarn frontend:build` để biên dịch toàn bộ frontend assets thành công không lỗi (exit code 0).
- [x] 4.2 Viết file test Playwright `tests/e2e/service-detail-figma.spec.ts` kiểm thử toàn bộ luồng điều hướng (Trang chủ -> Chi tiết, Danh sách -> Chi tiết), layout 2 cột, scrollspy và responsive (1440px, 1280px, 375px).
- [x] 4.3 Chạy test Playwright và chụp ảnh màn hình nghiệm thu tại `tests/e2e/screenshots/`.
- [x] 4.4 Đánh dấu 100% các task `[x]` và báo cáo kết quả chi tiết kèm bằng chứng terminal.
