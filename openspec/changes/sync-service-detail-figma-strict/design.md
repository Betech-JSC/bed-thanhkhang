# Design: Cấu Trúc Bố Cục Trang Chi Tiết Dịch Vụ Figma Chuẩn Node 2367:2088

## 1. Phân Tích Hệ Thống Khung Lưới (Grid & Spacing)
Trang chi tiết dịch vụ HH Maker tuân thủ nghiêm ngặt hệ thống layout Figma Desktop 1440px:
- **Canvas Width**: 1440px
- **Content Container**: `max-w-[1152px] mx-auto`
- **Lề trái/phải đối xứng**: `(1440 - 1152) / 2 = 144px`
- **Màu nền chủ đạo**: `#111322` (Dark blue / dark navy)
- **Màu chữ chính**: `#FAFAFA` (Heading), `#B9C0D4` (Body/Lead), `#84ADFF` (Accent/Links), `#2970FF` (Primary brand blue)

## 2. Thiết Kế Component Chi Tiết

### 2.1. Hero Showcase Section (`ServiceHero.vue` - Node 2367:2156)
- Kích thước khối: `1152px x 584px`
- Breadcrumb: `Trang chủ` / `Dịch vụ` / `[Tên dịch vụ]`
  * `Dịch vụ` trỏ về link danh sách dịch vụ `route('services.index')` (`/dich-vu`).
- Tiêu đề H1: `38px/46px Bold` font Inter, `#FAFAFA`.
- Lời dẫn: `16px/26px` font Inter, `#B9C0D4`, max-width 838px.
- Khung Banner Showcase (Node 2352:11858):
  * Kích thước: `1152px x 310px`, bo góc `16px`.
  * Nền: `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`.
  * Cột 1 (Node 2352:11860): Hình minh họa 3D `280px x 280px`, đổ bóng sâu `drop-shadow-[0_16px_32px_rgba(0,0,0,0.6)]`.
  * Cột 2 (Node 2352:11861): "BẮT ĐẦU TỪ" (`#D1E0FF`, 12px), danh sách: "Ý tưởng", "Ảnh tham khảo", "Bản phác thảo" (`#FAFAFA`, 20px).
  * Cột 3 (Node 2352:11867): "CÙNG LÀM RÕ" (`#D1E0FF`, 12px), danh sách: "Tỉ lệ & hình khối", "Cấu trúc & chi tiết", "Khả năng chế tác" (`#FAFAFA`, 20px).

### 2.2. Bố Cục 2 Cột: Sticky TOC & Nội Dung Kỹ Thuật (`ServiceToc.vue` & `ServiceArticleContent.vue`)
- Phân chia kích thước:
  * Cột trái: `ServiceToc.vue` rộng `248px`
  * Khoảng cách (Gap): `gap-16` (64px trên desktop)
  * Cột phải: `ServiceArticleContent.vue` rộng `840px`
  * Tổng chiều rộng: `248px + 64px + 840px = 1152px` (Khớp 100% với container).
- Cơ chế Sticky & Scrollspy:
  * `ServiceToc.vue` sử dụng `sticky top-28 self-start`.
  * `IntersectionObserver` lắng nghe tọa độ các section `#section-01` đến `#section-04`.
  * Trạng thái active: viền trái `border-l-2 border-[#2970FF]` và chữ `#84ADFF`.
  * Click vào mục: `scrollTo` với offset trừ đi header cố định (`headerOffset = 110px`) kèm hiệu ứng `behavior: 'smooth'`.

### 2.3. Khối 4 Nội Dung Kỹ Thuật (Node 2367:2196)
- **Mục 01**: "Khi nào bạn cần thiết kế 3D?" (H2 28px/36px Bold `#FAFAFA`).
- **Mục 02**: "Từ ý tưởng tới mô hình 3D" (H2 28px/36px), 4 bước quy trình với badge số bo góc 8px nền `#30374F`, số `#84ADFF` 15px font-semibold.
- **Mục 03**: "Bạn cần chuẩn bị gì?" (H2 28px/36px), card nền `#171B30`, bo góc 12px, padding 28px, 4 checklist item kèm `✓` xanh `#84ADFF`.
- **Mục 04**: "Cùng hoàn thiện trước khi gia công" (H2 28px/36px), callout viền xanh `#2970FF` 2px bên trái, text `#B2CCFF`.

### 2.4. Dịch Vụ Liên Quan (`ServiceRelated.vue` - Node 2367:2270)
- Grid 2 cột: `2 x 564px = 1128px + 24px gap = 1152px`.
- Chiều cao card: `220px`, bo góc `12px`, viền `#404968`, hover viền `#2970FF`.
- Nền thumbnail: `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`.
- Link card dẫn trực tiếp đến `/dich-vu/{slug}`.

### 2.5. CTA Banner (`ServiceCta.vue` - Node 2352:11794)
- Kích thước: `1152px x 183px`, nền `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`, bo góc `16px`.
- Nút CTA: 175px x 40px bo góc 8px, `#F5F8FF`, hover glow trắng.

## 3. Điều Hướng Liên Kết Toàn Cục
1. **Trang Chủ (`ServicesSection.vue`)**:
   - Thẻ dịch vụ bọc trong `<Link :href="getServiceUrl(service)">` dẫn về `/dich-vu/{slug}`.
2. **Trang Danh Sách (`Index.vue` & `ServiceBlock.vue`)**:
   - Khung minh họa card là `<Link :href="detailUrl">`.
   - Tiêu đề H2 là `<Link :href="detailUrl">`.
   - Nút phụ "Tìm hiểu chi tiết →" bên cạnh nút liên hệ.
