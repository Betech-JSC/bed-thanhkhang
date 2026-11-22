# Spec: Trang Chủ Thành Khang (`home-page`)

## Requirements

### Requirement 1: Khung trang chủ & Nền sáng chuẩn Design System
`resources/Frontend/js/Pages/Home/Index.vue` PHẢI khai báo tường minh `layout: MainLayout`, sử dụng nền sáng `bg-surface text-ink-body overflow-x-clip`, và render đủ 7 section theo đúng thứ tự thiết kế Figma `595:15360` mà không có dữ liệu mock hardcode.

#### Scenario 1.1: Tải Trang Chủ không tràn ngang và không có lỗi console
- **WHEN** người dùng truy cập `/` trên Desktop (`1440×900`) và Mobile (`375×812`)
- **THEN** trang render bên trong `MainLayout.vue`, nền `bg-surface` (`#FAFAFA`), `document.documentElement.scrollWidth <= window.innerWidth`, và 0 console errors.

---

### Requirement 2: Stage 1 — Hero Banner Slider (`595:15536`) & CMS `HOME_SLIDER`
Section 1 PHẢI nạp danh sách slide từ `HomeController::index()` (bảng `sliders` vị trí `HOME_SLIDER`, triển khai bằng migration idempotent), khóa tỷ lệ `aspect-[375/480] md:aspect-[1440/582]` và chuyển slide bằng dịch ngang `translate-x`.

#### Scenario 2.1: Khóa tỷ lệ Banner & dữ liệu sống từ CMS
- **WHEN** `HomeController::index()` truyền `props.sliders` chứa 3 bản ghi `HOME_SLIDER`
- **THEN** `HeroSection.vue` render trực tiếp từ `props.sliders` (không dùng mảng mock hardcode), container giữ tỷ lệ `aspect-[375/480] md:aspect-[1440/582]`, ảnh nền có class `w-full h-full object-cover object-center`, và chỉ slide đang hiển thị (hoặc slide 0) dùng thẻ `<h1>` để chuẩn SEO.

#### Scenario 2.2: Chuyển slide ngang không nhấp nháy trắng màn hình
- **WHEN** người dùng bấm nút Prev/Next (`< / >`) hoặc bấm các ô vuông pagination
- **THEN** track slide dịch chuyển mượt bằng `transition-transform` + `translateX(-index * 100%)` (tuyệt đối không dùng hiệu ứng `opacity: 0` gây chớp trắng), cập nhật đúng trạng thái active của pagination và hỗ trợ truy cập bàn phím.

---

### Requirement 3: Stage 2 — About Intro ("Về chúng tôi")
Section 2 PHẢI đọc nội dung giới thiệu doanh nghiệp (tiêu đề, 2 đoạn mô tả, 3 hình ảnh, CTA) từ cấu hình CMS (migration idempotent) và hiển thị bố cục chuẩn Figma trên Desktop/Mobile với padding responsive (`py-8 md:py-16`, `px-4 md:px-8`).

#### Scenario 3.1: Hiển thị động dữ liệu giới thiệu từ CMS
- **WHEN** mở Trang Chủ sau khi chạy migration Stage 2
- **THEN** Section "Về chúng tôi" hiển thị đầy đủ 2 đoạn nội dung, 3 ảnh cục bộ không vỡ (`naturalWidth > 0`) và nút CTA điều hướng hợp lệ sang trang Giới thiệu.

---

### Requirement 4: Stage 3 — Product Categories ("Danh mục sản phẩm") & Bảo toàn dữ liệu khách hàng
Section 3 PHẢI bảo toàn 100% 4 danh mục gốc và toàn bộ danh mục con/sản phẩm hiện có của khách hàng trong database, đồng thời sửa truy vấn trong `HomeController::index()` để không trả mảng rỗng.

#### Scenario 4.1: Truy vấn đầy đủ 4 danh mục gốc có sản phẩm ở cấp lá
- **WHEN** `HomeController::index()` truy vấn danh mục sản phẩm cho Trang Chủ
- **THEN** hệ thống trả về đầy đủ 4 danh mục gốc hiện có (`Linh kiện thang cuốn`, `Linh kiện thang nâng ô tô`, `Hệ thống băng tải hành lý`, `Linh kiện dây chuyền đóng gói`) kèm link slug hợp lệ tới trang danh mục sản phẩm, không thêm/xóa hay ghi đè danh mục khách hàng trong DB.

---

### Requirement 5: Stage 4 — Why Choose Us ("Vì sao khách hàng tin tưởng chọn Thành Khang?")
Section 4 PHẢI hiển thị 5 trụ cột giá trị cốt lõi trên nền banner có khóa tỷ lệ `aspect-ratio` / scrim tương phản cao, nạp dữ liệu từ CMS thông qua migration idempotent.

#### Scenario 5.1: Hiển thị 5 trụ cột từ CMS với độ tương phản đạt chuẩn
- **WHEN** người dùng cuộn đến Section "Vì sao khách hàng tin tưởng chọn Thành Khang?" trên Desktop và Mobile
- **THEN** cả 5 trụ cột hiển thị rõ ràng từ CMS, ảnh nền cục bộ không méo, bố cục xếp lưới sạch trên Mobile (`gap-3` / `gap-4`).

---

### Requirement 6: Stage 5 — Process ("Quy trình" — 4 bước chuẩn Figma)
Section 5 PHẢI hiển thị đúng **4 bước quy trình** khớp bố cục Figma cùng ảnh banner nền quy trình từ CMS (migration idempotent).

#### Scenario 6.1: Khớp số lượng 4 bước trên Trang Chủ
- **WHEN** người dùng xem Section "Quy trình" trên Trang Chủ
- **THEN** giao diện render đúng 4 bước từ dữ liệu CMS với số thứ tự, tiêu đề, mô tả đầy đủ và không tràn ngang trên `375px`.

---

### Requirement 7: Stage 6 — Blog / News ("Tin tức" — 3 bài viết)
Section 6 PHẢI hiển thị 3 thẻ bài viết mới nhất từ bảng `posts` (`type='POST'`, được nạp qua migration/seeder idempotent mẫu chuẩn Figma), kèm ngày đăng, mô tả ngắn và liên kết tới chi tiết bài viết.

#### Scenario 7.1: Hiển thị 3 thẻ tin tức liên kết động
- **WHEN** `featured_posts` được truyền từ `HomeController::index()`
- **THEN** Section "Tin tức" hiển thị 3 card cân đối (3 cột Desktop, 1 cột Mobile), ảnh thumbnail không vỡ, bấm vào card điều hướng đúng tới chi tiết bài viết.

---

### Requirement 8: Stage 7 — Contact & Quote Form ("KẾT NỐI & NHẬN BÁO GIÁ")
Section 8 PHẢI gắn thông tin liên hệ từ `$page.props.global` và gửi yêu cầu báo giá trực tiếp tới `contact.store` (`POST /contacts` hoặc `POST /en/contacts`) kèm kiểm tra hợp lệ và phản hồi rõ ràng.

#### Scenario 8.1: Gửi biểu mẫu yêu cầu báo giá hợp lệ
- **WHEN** người dùng điền đầy đủ thông tin bắt buộc và bấm nút gửi
- **THEN** nút chuyển trạng thái loading/disabled trong lúc gửi, gửi POST tới `contact.store`, hiển thị thông báo thành công khi HTTP 2xx và xóa trắng form; khi thiếu trường bắt buộc thì hiển thị thông báo lỗi inline rõ ràng.

---

### Requirement 9: Stage 8 — Build Gate, Graphify AST & Playwright Visual QA
Sau khi hoàn tất cả 7 Stage, toàn bộ mã nguồn Frontend PHẢI biên dịch thành công 0 lỗi, cập nhật AST và vượt qua kiểm thử hình ảnh thực tế.

#### Scenario 9.1: Kiểm chứng cuối cùng
- **WHEN** chạy `yarn frontend:build`, `graphify . --code-only` và Playwright QA tại `1440×900` & `375×812`
- **THEN** `yarn frontend:build` thoát mã 0 (0 lỗi Client + SSR), `graphify-out/graph.json` cập nhật thành công, mọi ảnh đều có `naturalWidth > 0`, 0 console errors, 0 horizontal overflow, kèm ảnh chụp bằng chứng Desktop & Mobile.
