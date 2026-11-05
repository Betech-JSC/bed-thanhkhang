# Proposal: dynamic-service-details

## Why
Hiện tại, trang Chi tiết Dịch vụ (`/dich-vu/{slug}` và `/en/services/{slug}`) của cả 3 dịch vụ (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`) đang bị trùng lặp và dùng chung một nội dung duy nhất của dịch vụ "Thiết kế 3D". Nguyên nhân do các Vue component frontend (`ServiceHero.vue`, `Show.vue`, `ServiceArticleContent.vue`) hardcode văn bản và danh sách của "Thiết kế 3D", còn backend model (`app/Models/Service.php`) chỉ cung cấp fallback mặc định cho "Thiết kế 3D" khi database chưa có dữ liệu chi tiết. Việc này khiến khách hàng không nắm được thông tin đặc thù, quy trình và danh mục chuẩn bị của "Gia công 3D" và "Làm đồ custom", làm giảm tính chuyên nghiệp và tỉ lệ chuyển đổi của website.

## What Changes
- **Backend Service Content Resolver & Seed Data**:
  - Mở rộng cơ chế phân giải nội dung động (`Service::getDefaultSteps()`, `Service::getDefaultChecklistItems()`, và bổ sung bộ dữ liệu nội dung đặc thù cho cả 3 dịch vụ: `thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`).
  - Cung cấp cấu trúc dữ liệu hoàn chỉnh cho từng dịch vụ bao gồm: Hero subtitle/headline, danh sách "Bắt đầu từ" (input_items), danh sách "Cùng làm rõ" (process_items), danh sách mục lục (toc_items), và 4 sections bài viết kỹ thuật (01 Tổng quan/Khi nào cần, 02 Quy trình các bước, 03 Danh mục chuẩn bị, 04 Hoàn thiện & lưu ý chế tác) với đầy đủ song ngữ Tiếng Việt (VI) và Tiếng Anh (EN).
  - Bổ sung Seeder an toàn (`ServiceContentSeeder.php`) sử dụng `updateOrCreate` hoặc cập nhật dữ liệu `content_by_tab`, `benefits`, `description`, `content` trong `service_translations` mà không làm mất hay xóa dữ liệu hiện có.
- **Frontend Components Refactoring**:
  - `Show.vue`: Nhận dữ liệu dịch vụ đầy đủ từ props, chuyển đổi `tocItems` thành computed động theo dữ liệu dịch vụ hoặc fallback theo slug dịch vụ tương ứng, truyền đầy đủ dữ liệu cho các component con.
  - `ServiceHero.vue`: Loại bỏ các chuỗi hardcode "Từ phác thảo đến hình khối rõ ràng", `inputItems` và `processItems`. Bind dữ liệu động từ props được truyền vào (`headline`, `inputItems`, `processItems`, `image`).
  - `ServiceArticleContent.vue`: Loại bỏ toàn bộ tiêu đề H2 và đoạn văn bản hardcode của dịch vụ Thiết kế 3D. Sử dụng cấu trúc section động hoặc props động (`sections`, `steps`, `checklistItems`, `callout`) theo từng dịch vụ.
  - `ServiceToc.vue`: Đảm bảo đồng bộ mượt mà với danh sách `tocItems` động và scrollspy chính xác theo các section id.
- **Chất lượng hiển thị & Pixel-Perfect**:
  - Giữ vững 100% chuẩn thiết kế Figma Node 2367:2088 và nguyên tắc Anti-UI-Slop (Quiet & High-Trust UI).
  - Tối ưu song ngữ mượt mà khi chuyển đổi giữa VI và EN.

## Capabilities

### New Capabilities
<!-- Không có capability hoàn toàn mới; đây là việc nâng cấp chuẩn hóa tính năng chi tiết dịch vụ đã được định nghĩa -->

### Modified Capabilities
- `service-detail-figma`: Điều chỉnh các yêu cầu (Requirements) về Hero Showcase, Sidebar Mục lục (TOC), và Nội dung kỹ thuật 4 phần để hỗ trợ hiển thị động theo từng dịch vụ (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`) và đa ngôn ngữ (VI/EN) thay vì cố định cho riêng dịch vụ Thiết kế 3D.

## Impact
- **Backend**:
  - `app/Models/Service.php`: Cập nhật các hàm `transformDetails()`, `resolveSteps()`, `resolveChecklistItems()` và bổ sung `resolveHeroData()`, `resolveArticleSections()`, `resolveTocItems()` hoặc fallback theo từng slug.
  - `app/Http/Controllers/Frontend/ServiceController.php`: Truyền đầy đủ dữ liệu động (hero_data, article_sections, toc_items, steps, checklist_items) sang Inertia view `Services/Show`.
  - Tạo mới `database/seeders/ServiceContentSeeder.php` để lưu trữ và nạp dữ liệu chi tiết chuẩn nghiệp vụ vào cơ sở dữ liệu.
- **Frontend**:
  - `resources/Frontend/js/Pages/Services/Show.vue`: Bind props động, không fallback cứng về Thiết kế 3D.
  - `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue`: Nhận props `headline`, `inputItems`, `processItems`.
  - `resources/Frontend/js/Pages/Services/Components/ServiceArticleContent.vue`: Render nội dung động từ props `sections`, `steps`, `checklistItems`.
  - `resources/Frontend/js/Pages/Services/Components/ServiceToc.vue`: Nhận items động linh hoạt.
- **Phạm vi an toàn (Safe Guard Scope)**:
  - Tuyệt đối không xóa bảng `services`, `service_translations` hay chạy `migrate:fresh`.
  - Giữ nguyên các route hiện hành: `/dich-vu/{slug}`, `/en/services/{slug}` và liên kết SEO.
