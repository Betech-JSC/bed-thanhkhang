# Design: dynamic-service-details

## Context
Trang Chi tiết Dịch vụ hiện tại được xây dựng theo thiết kế Figma (Node 2367:2088) với bố cục 2 cột bất đối xứng (TOC Sidebar bên trái 248px và Cột nội dung kỹ thuật 840px bên phải). Tuy nhiên, frontend components (`Show.vue`, `ServiceHero.vue`, `ServiceArticleContent.vue`) hiện đang hardcode văn bản và danh sách của dịch vụ "Thiết kế 3D". Ở backend, model `app/Models/Service.php` chỉ có các hàm `getDefaultSteps()` và `getDefaultChecklistItems()` cố định cho "Thiết kế 3D". Khi người dùng truy cập vào dịch vụ "Gia công 3D" (`/dich-vu/gia-cong-3d`) hoặc "Làm đồ custom" (`/dich-vu/lam-do-custom`), nội dung hiển thị hoàn toàn bị trùng lặp với Thiết kế 3D.

Mục tiêu thiết kế kỹ thuật là chuyển đổi toàn bộ cơ chế hiển thị sang mô hình **Data-Driven Dynamic Content** hỗ trợ đầy đủ 3 dịch vụ và 2 ngôn ngữ (VI/EN) mà không làm phá vỡ cấu trúc CSS/HTML Pixel-Perfect đã được nghiệm thu theo Figma.

## Goals / Non-Goals

**Goals:**
- Tách biệt và chuẩn hóa nội dung cho cả 3 dịch vụ chính thức:
  1. `thiet-ke-3d`: Thiết kế 3D (3D Design)
  2. `gia-cong-3d`: Gia công 3D (3D Machining & Printing)
  3. `lam-do-custom`: Làm đồ custom (Custom Bespoke Creations)
- Thiết lập **Service Content Resolver** tại Backend (`app/Models/Service.php` hoặc Service Data Provider) để phân giải tự động từ Database (`content_by_tab`, `benefits`), đồng thời có bộ fallback dự phòng chuẩn xác theo từng `slug` và `locale` (VI/EN) khi database chưa có đầy đủ dữ liệu.
- Viết `ServiceContentSeeder` để nạp dữ liệu chi tiết có cấu trúc vào bảng `service_translations` theo phương thức an toàn `updateOrCreate`.
- Refactor Frontend components (`Show.vue`, `ServiceHero.vue`, `ServiceArticleContent.vue`, `ServiceToc.vue`) để nhận toàn bộ nội dung từ props Backend:
  - Hero: `headline`, `inputItems`, `processItems`, `image`, `description`.
  - TOC: `items` (id, number, title, shortTitle).
  - Article Content: 4 sections kỹ thuật (`section_01`, `section_02`, `section_03`, `section_04`), `steps`, `checklistItems`, `callout`.
- Đảm bảo tính tương thích ngược 100% với các bài test E2E hiện có của `/dich-vu/thiet-ke-3d`.

**Non-Goals:**
- Không sửa đổi schema database (không thêm bảng mới, không chạy migration thay đổi cấu trúc bảng).
- Không can thiệp vào các luồng tính năng khác như giỏ hàng, khóa học, tài nguyên hay trang chủ ngoài phạm vi hiển thị dịch vụ.
- Không thay đổi thiết kế visual token (màu sắc, khoảng cách, font chữ, gradient) đã được định nghĩa chuẩn Figma.

## Decisions

### Decision 1: Cấu trúc Data Contract động cho Service Details
Thống nhất cấu trúc dữ liệu trả về từ `Service::transformDetails()` qua Inertia props:

```php
[
    'id' => $this->id,
    'title' => $this->title,
    'slug' => $this->seo_slug ?? $this->slug,
    'description' => $this->description,
    'hero' => [
        'headline' => '...', // Ví dụ: "Gia công 3D: Hiện thực hóa mô hình với độ chính xác cao"
        'input_items' => ['File 3D (STL/STEP/OBJ)', 'Kích thước & độ bền', 'Số lượng cần làm'],
        'process_items' => ['Công nghệ & vật liệu in', 'Độ mịn & độ dày thành', 'Phương án hậu kỳ bề mặt'],
        'image' => '...',
    ],
    'toc_items' => [
        ['id' => 'section-01', 'number' => '01', 'title' => '...', 'shortTitle' => '...'],
        ['id' => 'section-02', 'number' => '02', 'title' => '...', 'shortTitle' => '...'],
        ['id' => 'section-03', 'number' => '03', 'title' => '...', 'shortTitle' => '...'],
        ['id' => 'section-04', 'number' => '04', 'title' => '...', 'shortTitle' => '...'],
    ],
    'sections' => [
        'section_01' => [
            'id' => 'section-01',
            'title' => '...',
            'paragraphs' => ['...'],
        ],
        'section_02' => [
            'id' => 'section-02',
            'title' => '...',
            'intro' => '...',
        ],
        'section_03' => [
            'id' => 'section-03',
            'title' => '...',
            'intro' => '...',
        ],
        'section_04' => [
            'id' => 'section-04',
            'title' => '...',
            'paragraph' => '...',
            'callout' => '...',
        ],
    ],
    'steps' => [...],          // 4 bước quy trình (number, title, description)
    'checklist_items' => [...],// 4 mục checklist thông số chuẩn bị (title, description)
    'services' => [...],       // Danh sách dịch vụ liên quan
    'seo' => [...],
]
```

*Lý do lựa chọn:* Định dạng rõ ràng, tách biệt các khối nội dung của từng section, cho phép Vue template render trực tiếp bằng `v-for` hoặc props binding mà không cần can thiệp logic phân nhánh phức tạp ở phía client.

### Decision 2: Backend Content Resolver Pattern
Trong `app/Models/Service.php`, xây dựng các resolver method:
- `resolveHeroData(?string $locale = null): array`
- `resolveTocItems(?string $locale = null): array`
- `resolveArticleSections(?string $locale = null): array`
- Cập nhật `resolveSteps()` và `resolveChecklistItems()` để nhận diện `slug` của service hiện tại (`thiet-ke-3d`, `gia-cong-3d`, `lam-do-custom`).

Khi trường `content_by_tab` hoặc `benefits` trong DB chứa JSON hợp lệ, hệ thống sẽ ưu tiên parse dữ liệu từ DB. Nếu thiếu hoặc trống, hệ thống sẽ fallback về từ điển nội dung chuẩn nghiệp vụ (Dictionary Fallback Map) được định nghĩa sẵn cho từng slug và từng locale (`vi`, `en`).

*Lý do lựa chọn:* Tính sẵn sàng cao (High Resilience) - Trang web sẽ không bao giờ bị vỡ giao diện hoặc hiển thị trống nội dung ngay cả khi cơ sở dữ liệu chưa được seed hoặc admin mới chỉ tạo bản ghi cơ bản.

### Decision 3: Seed dữ liệu an toàn (Safe Idempotent Seeding)
Tạo `database/seeders/ServiceContentSeeder.php`:
- Sử dụng phương thức `updateOrCreate` trên model `Service` hoặc bảng `service_translations` dựa trên `service_id` và `locale`.
- Tuyệt đối không xóa records hoặc drop bảng.
- Đồng bộ dữ liệu chi tiết cho cả 3 dịch vụ ở cả 2 ngôn ngữ VI và EN.
- Tự động xóa cache tương ứng sau khi seed bằng `Service::clearServiceCaches()`.

### Decision 4: Refactor Frontend Components sang Dynamic Props Binding
- **`Show.vue`**:
  - Không gán cứng danh sách `tocItems` chỉ cho Thiết kế 3D.
  - Sử dụng `computed` trỏ đến `props.service.toc_items`, fallback theo slug nếu props chưa có.
  - Truyền đầy đủ props cho `ServiceHero`, `ServiceToc`, `ServiceArticleContent`.
- **`ServiceHero.vue`**:
  - Nhận props: `headline`, `description`, `image`, `inputItems`, `processItems`.
  - Bỏ đoạn code tự ghép chuỗi `: Từ phác thảo đến hình khối rõ ràng`.
  - Hiển thị danh sách `inputItems` và `processItems` động qua `v-for`.
- **`ServiceArticleContent.vue`**:
  - Nhận props: `sections`, `steps`, `checklistItems`, `dynamicHtml`.
  - Section 01: Hiển thị `sections.section_01.title` và các đoạn văn bản trong `sections.section_01.paragraphs`.
  - Section 02: Hiển thị `sections.section_02.title`, `sections.section_02.intro` và danh sách `steps`.
  - Section 03: Hiển thị `sections.section_03.title`, `sections.section_03.intro` và danh sách `checklistItems`.
  - Section 04: Hiển thị `sections.section_04.title`, `sections.section_04.paragraph` và khối callout `sections.section_04.callout`.
  - Giữ nguyên các ID `#section-01`, `#section-02`, `#section-03`, `#section-04` để bảo toàn tính năng cuộn mượt và TOC Scrollspy.

## Risks / Trade-offs

- **[Risk] Xung đột bộ nhớ đệm (Cache stale)**:
  - Cache Laravel (`service_show_{slug}_{locale}`) có thời gian lưu 3600 giây. Nếu không xóa cache, người dùng sẽ tiếp tục thấy nội dung cũ.
  - *Giải pháp*: `Service::clearServiceCaches()` được gọi tự động khi lưu model; bổ sung lệnh `php artisan cache:clear` trong task hướng dẫn triển khai.
- **[Risk] Phá vỡ bài test E2E kiểm tra text cứng của Thiết kế 3D**:
  - Các bài test Playwright hiện hữu (`service-detail-figma-strict.spec.ts`) kiểm tra sự xuất hiện của chuỗi "Từ phác thảo đến hình khối rõ ràng", "Khi nào bạn cần thiết kế 3D?".
  - *Giải pháp*: Toàn bộ chuỗi nội dung của dịch vụ `thiet-ke-3d` được giữ nguyên vẹn 100% so với bản hiện tại; chỉ áp dụng nội dung mới cho `gia-cong-3d` và `lam-do-custom`.
- **[Risk] Lệch ngôn ngữ khi chuyển đổi qua lại giữa VI và EN**:
  - Nếu Frontend không cập nhật props khi chuyển locale hoặc backend trả về locale không đúng.
  - *Giải pháp*: Resolver ở backend luôn dựa vào `current_locale()`, đồng thời fallback dictionary hỗ trợ song ngữ toàn diện cho cả 3 dịch vụ.

## Migration Plan
1. **Bước 1**: Cập nhật Model `app/Models/Service.php` bổ sung bộ dữ liệu nội dung fallback cho cả 3 dịch vụ và các resolver methods.
2. **Bước 2**: Cập nhật `app/Http/Controllers/Frontend/ServiceController.php` để truyền toàn diện các đối tượng `hero`, `toc_items`, `sections`, `steps`, `checklist_items` vào Inertia view.
3. **Bước 3**: Viết và chạy `database/seeders/ServiceContentSeeder.php` để nạp dữ liệu vào cơ sở dữ liệu.
4. **Bước 4**: Refactor các components Vue: `Show.vue`, `ServiceHero.vue`, `ServiceArticleContent.vue`, `ServiceToc.vue`.
5. **Bước 5**: Chạy `npm run build` và kiểm thử toàn bộ các bài test E2E (cũ và mới) qua Playwright.
