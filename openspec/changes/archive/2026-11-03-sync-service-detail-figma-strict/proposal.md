# Proposal: Chuẩn hóa giao diện trang Chi tiết Dịch vụ theo Figma Node 2367:2088

## Why

Trang Chi tiết Dịch vụ (`resources/Frontend/js/Pages/Services/Show.vue`) là trang nội dung cốt lõi giới thiệu năng lực kỹ thuật và quy trình chế tác của HH Maker. Hiện tại trang đã có bộ khung giao diện sơ bộ nhưng còn sai lệch về typography, layout padding/gap, style active của TOC, thẻ callout và banner CTA so với bản thiết kế chính thức tại Figma Node `2367:2088`. Cần chuẩn hóa pixel-perfect trang này theo đúng thiết kế Figma, bảo đảm trải nghiệm đọc mượt mà, tính năng Scrollspy chính xác và tuân thủ tiêu chuẩn Quiet & High-Trust UI (Anti-UI-Slop).

## What Changes

- **Khung Grid & Layout Shell (Node 2367:2088)**:
  - Nền trang đồng nhất `#111322`, text `#FAFAFA`.
  - Container trung tâm tối đa `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0`.
- **Section 1: Hero Showcase (Node 2367:2156 - 1152x584px)**:
  - Breadcrumbs chuẩn: "Trang chủ / Dịch vụ / [Tên dịch vụ]" với màu `#B9C0D4`, dấu phân cách `/` và trang hiện tại `#84ADFF`.
  - Tiêu đề H1: Font Inter, 38px, font-bold 700, leading 46px, màu `#FAFAFA`.
  - Lời dẫn: 16px, leading 26px, màu `#B9C0D4`, max-width 840px.
  - Khung minh họa kỹ thuật (1152x310px, bo góc 16px, gradient `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`):
    * Ảnh render 3D: 280x280px drop shadow, lấy ảnh gốc Figma `minh_họa_thiết_kế_3d_từ_trang_dịch_vụ.png`.
    * Cột 1 "BẮT ĐẦU TỪ": nhãn 12px `#D1E0FF`, 3 mục (Ý tưởng, Ảnh tham khảo, Bản phác thảo) font 20px leading 29px `#FAFAFA`.
    * Cột 2 "CÙNG LÀM RÕ": nhãn 12px `#D1E0FF`, 3 mục (Tỉ lệ & hình khối, Cấu trúc & chi tiết, Khả năng chế tác) font 20px leading 29px `#FAFAFA`.
- **Section 2: Layout 2 cột bất đối xứng - Sticky TOC & Nội dung (Node 2367:2177 - 1152x1816px)**:
  - **Sidebar TOC (Node 2367:2178 - 248px)**:
    * Sticky top-28, nhãn "TRONG BÀI VIẾT NÀY" (13px, font 600, leading 16px, `#B9C0D4`).
    * 4 mục chuẩn: 01. "Khi nào cần thiết kế 3D?", 02. "Từ ý tưởng tới mô hình", 03. "Bạn cần chuẩn bị gì?", 04. "Kết quả cùng hoàn thiện".
    * Trạng thái Active: `border-l-2 border-[#2970FF]`, số và chữ màu `#84ADFF`, không có box-shadow nền thừa.
    * Trạng thái Inactive: `border-l border-[#404968]`, số và chữ màu `#B9C0D4`, hover `#FAFAFA`.
    * Smooth scroll khi click và đồng bộ Scrollspy tự động qua IntersectionObserver khi cuộn.
  - **Nội dung kỹ thuật (Node 2367:2196 - 840px)**:
    * Tiêu đề H2 từng mục: 28px, font-bold 700, leading 36px, `#FAFAFA`.
    * Đoạn văn: 16px, leading 26-28px, `#B9C0D4`.
    * Quy trình 4 bước thiết kế (Node 2367:2204): Viền trên `1px solid #404968`, badge số bước 40x40px nền `#30374F`, bo góc 8px, chữ `#84ADFF` 15px font 600. Tiêu đề bước 19px font 600 `#FAFAFA`.
    * Danh sách chuẩn bị (Node 2367:2232): Card nền `#171B30`, bo góc 12px, padding 28px, 4 mục checklist với dấu kiểm "✓" `#84ADFF` 16px, tiêu đề 16px font 600 `#FAFAFA`, hướng dẫn 15px `#B9C0D4`.
    * Lưu ý mô hình (Node 2367:2256): Callout viền trái `border-l-2 border-[#2970FF]`, padding-left 20px, text `#B2CCFF` 16px leading 28px (loại bỏ khung hộp thừa).
- **Section 3: Dịch vụ liên quan (Node 2367:2270 - 1152x284px)**:
  - Tiêu đề H2: "Dịch vụ liên quan" (28px font 700 `#FAFAFA`).
  - Lưới 2 card: 564x220px, bo góc 12px, border `1px solid #404968`, hover border `#2970FF`.
  - Thumbnail 180x220px gradient `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`, ảnh minh họa tải trực tiếp từ Figma.
  - Nội dung text: Tên dịch vụ 24px font 700, mô tả 14-15px `#B9C0D4`, liên kết "Tìm hiểu dịch vụ" 14px font 500 `#84ADFF`.
- **Section 4: Banner CTA "Kết nối dự án" (Node 2352:11794 - 1152x183px)**:
  - Nền gradient `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`, bo góc 16px, padding 40px.
  - Tiêu đề H2 32px font 700 `#FAFAFA`, mô tả 16px leading 26px `#FAFAFA`.
  - Nút bấm "Nhận tư vấn ngay" 175x40px, nền `#F5F8FF`, text `#111322` 16px font 500, bo góc 8px.

## Capabilities

### New Capabilities
- `service-detail-figma`: Đặc tả kỹ thuật và tiêu chuẩn giao diện của trang Chi tiết Dịch vụ theo Figma Node 2367:2088, bao gồm 4 section giao diện, hệ thống token màu/typography và tính năng Scrollspy TOC.

### Modified Capabilities
*(Không có)*

## Impact

- **Mã nguồn giao diện**:
  - `resources/Frontend/js/Pages/Services/Show.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceToc.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceArticleContent.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceRelated.vue`
  - `resources/Frontend/js/Pages/Services/Components/ServiceCta.vue`
- **Tài nguyên tĩnh**:
  - `public/assets/images/services/hero-3d-illustration.png` (đã đồng bộ từ Figma node 2352:11860)
  - `public/assets/images/services/service-gia-cong-3d.png` (đã đồng bộ từ Figma node 2367:2278)
  - `public/assets/images/services/service-lam-do-custom.png` (đã đồng bộ từ Figma node 2367:2287)
- **Database & Backend**: Tuyệt đối không thay đổi schema, migration hay dữ liệu cơ sở dữ liệu.
