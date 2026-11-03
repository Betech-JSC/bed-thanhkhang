# Proposal: Đồng Bộ Giao Diện Chi Tiết Dịch Vụ Figma Chuẩn 100% Node 2367:2088 & Điều Hướng Thông Minh

## Why
Hiện tại, hệ thống dịch vụ HH Maker gặp 2 vấn đề lớn:
1. **Thiếu liên kết điều hướng đồng bộ (Navigation Disconnect)**:
   - Trang chủ (`ServicesSection.vue`): 3 thẻ dịch vụ ("Gia công 3D", "Thiết kế 3D", "Custom") chỉ là khối tĩnh, người dùng click chuột không chuyển sang trang chi tiết dịch vụ.
   - Trang danh sách dịch vụ (`Services/Index.vue` & `ServiceBlock.vue`): Tiêu đề và hình ảnh minh họa 3D chưa được gắn link, chỉ có 1 nút liên hệ duy nhất mà thiếu nút/link xem trang chi tiết `/dich-vu/{slug}`.
2. **Cần chuẩn hóa giao diện Chi tiết Dịch vụ theo Figma Node 2367:2088**:
   - Trang chi tiết dịch vụ (`/dich-vu/{slug}`) cần đạt chuẩn 100% Figma: Khung lưới 1152px đối xứng 144px ở 1440px desktop.
   - Hero showcase card (1152x310px, background `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`) hiển thị 3 cột: ảnh 3D 280px đổ bóng sâu, cột giữa "BẮT ĐẦU TỪ", cột phải "CÙNG LÀM RÕ".
   - Sticky TOC bên trái (248px, `sticky top-28`), 4 mục điều hướng có scrollspy tự bắt vị trí cuộn và viền xanh active `border-l-2 border-[#2970FF]`, click cuộn mượt.
   - Khung bài viết kỹ thuật (840px, cách TOC 64px): 4 khối nội dung chuẩn Figma (Khi nào cần thiết kế 3D, Quy trình 4 bước 01-04, Card checklist 4 nhóm `#171B30`, Callout viền xanh `#2970FF`).
   - Khối dịch vụ liên quan (2 card 564x220px, bo góc 12px, viền `#404968`, hover `#2970FF`) trỏ link đúng slug.
   - Banner CTA kết nối dự án (1152x183px `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`) với nút "Nhận tư vấn ngay".

## What Changes
1. **Gắn link thông minh tại Trang Chủ (`ServicesSection.vue`)**:
   - Biến 3 thẻ dịch vụ thành component `<Link :href="getServiceUrl(service)">` bấm được trỏ về `/dich-vu/{slug}` tương ứng (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`).
2. **Gắn link tại Trang Danh Sách Dịch Vụ (`Index.vue` & `ServiceBlock.vue`)**:
   - Gắn link trỏ về `/dich-vu/{slug}` vào ảnh minh họa 3D card.
   - Gắn link vào tiêu đề H2 dịch vụ.
   - Bổ sung nút liên kết phụ "Tìm hiểu chi tiết →" bên cạnh nút liên hệ.
3. **Chuẩn hóa Giao Diện Chi Tiết Dịch Vụ (`Show.vue` & Components)**:
   - `Show.vue`: Layout max-width 1152px căn giữa hoàn hảo.
   - `ServiceHero.vue`: Breadcrumb Trang chủ / Dịch vụ / [Tên dịch vụ], H1 38px/46px Bold `#FAFAFA`, showcase card 1152x310px 3 cột.
   - `ServiceToc.vue`: TOC 248px sticky top-28, active border-l-2 `#2970FF`, scrollspy IntersectionObserver mượt mà.
   - `ServiceArticleContent.vue`: 840px, gap 64px, 4 sections chuẩn xác theo Figma Node 2367:2196.
   - `ServiceRelated.vue`: 2 card 564px x 220px, viền `#404968`, hover xanh `#2970FF`, link chính xác tới các dịch vụ khác.
   - `ServiceCta.vue`: CTA banner 1152x183px gradient xanh, nút tư vấn chuẩn Figma.
4. **Kiểm thử E2E với Playwright**:
   - Viết kịch bản test `tests/e2e/service-detail-figma.spec.ts` kiểm chứng toàn diện từ luồng click ở Trang chủ, Trang danh sách đến toàn bộ tương tác trên trang chi tiết dịch vụ.

## Capabilities

### New Capabilities
- `service-detail`: Đặc tả chuẩn 100% Figma Node 2367:2088 cho trang chi tiết dịch vụ (Hero 3 cột, Sticky TOC scrollspy, Bài viết kỹ thuật 4 khối, Dịch vụ liên quan và Banner CTA).

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue`: Thẻ dịch vụ thành Link trỏ về `/dich-vu/{slug}`.
- `resources/Frontend/js/Pages/Services/Index.vue`: Cung cấp slug đầy đủ cho các dịch vụ.
- `resources/Frontend/js/Pages/Services/Components/ServiceBlock.vue`: Thêm link ảnh, link tiêu đề và nút "Tìm hiểu chi tiết".
- `resources/Frontend/js/Pages/Services/Show.vue`: Tinh chỉnh kết nối và dữ liệu cho trang chi tiết.
- `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue`: Chuẩn hóa breadcrumbs URL và 3 cột showcase.
- `resources/Frontend/js/Pages/Services/Components/ServiceRelated.vue`: Chuẩn hóa URL sang `/dich-vu/{slug}`.
- `tests/e2e/service-detail-figma.spec.ts`: File test E2E kiểm chứng luồng và giao diện.
- Tuân thủ nghiêm ngặt: Tuyệt đối không xóa file, không sửa database.
