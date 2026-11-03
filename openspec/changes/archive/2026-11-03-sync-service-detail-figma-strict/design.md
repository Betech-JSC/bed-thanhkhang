# Design: Kiến trúc và chuẩn hóa giao diện trang Chi tiết Dịch vụ theo Figma Node 2367:2088

## Context

Trang Chi tiết Dịch vụ (`resources/Frontend/js/Pages/Services/Show.vue`) đóng vai trò trình bày chuyên sâu từng dịch vụ (ví dụ: Thiết kế 3D, Gia công 3D, Làm đồ Custom) với cấu trúc phân rã gồm 5 component:
- `ServiceHero.vue`: Breadcrumbs, H1, mô tả và khung minh họa kỹ thuật 1152x310px.
- `ServiceToc.vue`: Sidebar mục lục bài viết dạng sticky có tính năng Scrollspy.
- `ServiceArticleContent.vue`: 4 section kỹ thuật chi tiết với danh sách các bước, card checklist và callout.
- `ServiceRelated.vue`: Lưới 2 card giới thiệu dịch vụ liên quan.
- `ServiceCta.vue`: Banner kêu gọi hành động với gradient thương hiệu.

Bản thiết kế Figma Node `2367:2088` định hình ngôn ngữ giao diện sắc sảo, tối giản, độ tương phản cao với nền `#111322`, text `#FAFAFA`, phụ chú `#B9C0D4`, màu điểm nhấn xanh `#2970FF` và `#84ADFF`.

## Goals / Non-Goals

**Goals:**
- Tinh chỉnh toàn diện `Show.vue` và 5 component con bám sát 100% thông số kích thước, màu sắc, font chữ từ Figma MCP (Node `2367:2088`, `2367:2156`, `2367:2177`, `2367:2178`, `2367:2196`, `2367:2270`, `2352:11794`).
- Tối ưu cơ chế Scrollspy và Smooth Scroll cho Sidebar TOC (248px), đồng bộ trạng thái active viền trái `border-l-2 border-[#2970FF]` và text `#84ADFF` chuẩn xác khi cuộn trang.
- Đảm bảo hiển thị hoàn hảo ở cả màn hình Desktop (1440px -> 1152px) lẫn Mobile (375px/390px) mà không có lỗi tràn ngang.
- Tải và đồng bộ ảnh minh họa chuẩn từ Figma MCP về thư mục `public/assets/images/services/`.
- Tuân thủ nghiêm ngặt Anti-UI-Slop: không biểu tượng cảm xúc tùy tiện, không viền rác, giữ thẩm mỹ Quiet & High-Trust UI.

**Non-Goals:**
- Không sửa đổi database, migrations hay API backend Laravel.
- Không sửa đổi trang danh sách dịch vụ `Services/Index.vue` hay component `ServiceBlock.vue`.

## Decisions

### 1. Kiến trúc Component & State Management
- Giữ vững cấu trúc phân tách mô-đun hoá hiện có của `Show.vue` và thư mục `Components/`:
  - `Show.vue`: Quản lý state `activeSection`, khởi tạo `IntersectionObserver` lắng nghe 4 section (`section-01` -> `section-04`), tính toán SEO meta và truyền props xuống các component con.
  - `ServiceHero.vue`: Tiếp nhận `title`, `description`, `image`, render breadcrumbs và khung minh họa kỹ thuật (1152x310px).
  - `ServiceToc.vue`: Hiển thị danh sách 4 mục bài viết; trên Desktop là sticky sidebar 248px, trên Mobile là thanh cuộn ngang mượt mà.
  - `ServiceArticleContent.vue`: Chứa 4 section bài viết có `id` tương ứng với `tocItems`, hỗ trợ cả nội dung cố định chuẩn Figma lẫn nội dung động `dynamicHtml` từ CMS.
  - `ServiceRelated.vue`: Lưới 2 card dịch vụ với tỉ lệ chuẩn 564x220px và thumbnail 180x220px.
  - `ServiceCta.vue`: Banner gradient 1152x183px bo góc 16px.

### 2. Thiết kế Token & Màu sắc Chuẩn Figma
- **Nền chính**: `#111322`
- **Văn bản**:
  - H1: `#FAFAFA`, Inter 38px, font-bold 700, leading 46px
  - H2: `#FAFAFA`, Inter 28px, font-bold 700, leading 36px
  - H3: `#FAFAFA`, Inter 19px, font-semibold 600, leading 27px
  - Body text: `#B9C0D4`, Inter 16px, regular 400, leading 26-28px
  - Small labels/badges: `#D1E0FF` / `#84ADFF`, Inter 12-15px
- **Màu tương tác & Điểm nhấn**:
  - Primary Accent: `#2970FF` (viền active TOC, callout border, gradient CTA)
  - Secondary Accent / Link: `#84ADFF` (active text TOC, badge số bước, dấu checkmark)
  - Viền phân cách / Inactive border: `#404968`
  - Nền phụ / Card background: `#171B30` (checklist card), `#30374F` (badge 40x40px)
- **Gradients**:
  - Banner Showcase & Thumbnails: `linear-gradient(180deg, #101121 0%, #2D4BE2 100%)`
  - Banner CTA: `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`

### 3. Tối ưu Scrollspy & Smooth Scroll
- Khởi tạo `IntersectionObserver` với cấu hình:
  ```javascript
  const options = {
      root: null,
      rootMargin: '-100px 0px -60% 0px',
      threshold: 0,
  }
  ```
- Khi người dùng click vào mục TOC, thực hiện smooth scroll bù trừ thanh điều hướng chính (`headerOffset = 110px`):
  ```javascript
  const elementPosition = targetEl.getBoundingClientRect().top
  const offsetPosition = elementPosition + window.pageYOffset - headerOffset
  window.scrollTo({ top: offsetPosition, behavior: 'smooth' })
  ```

## Risks / Trade-offs

| Rủi ro (Risk) | Giải pháp kiểm soát (Mitigation) |
| :--- | :--- |
| **Scrollspy nhảy sai khi cuộn nhanh** | Áp dụng `rootMargin: '-100px 0px -60% 0px'` để xác định chính xác section đang chiếm vị trí 1/3 phía trên màn hình; kết hợp cập nhật trực tiếp `activeSection` ngay khi click mục TOC. |
| **Ảnh render 3D không sắc nét hoặc vỡ liên kết** | Tải trực tiếp ảnh gốc chất lượng cao từ Figma MCP qua tool `download_figma_images` lưu vào `public/assets/images/services/`. |
| **Tràn ngang trên thiết bị di động** | Kiểm soát chặt `max-w-[1152px]` và `w-full`, trên mobile chuyển TOC sang chế độ horizontal overflow với thanh cuộn ẩn (`no-scrollbar`), các thẻ card và cột được chuyển thành `flex-col`. |
