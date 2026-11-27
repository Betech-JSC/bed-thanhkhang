# Proposal: Đồng bộ Giao diện Mobile Figma (`2:9`) & Tối ưu Trải nghiệm Mobile Toàn Site (`sync-mobile-ui-figma-2-9`)

## Why

Sau khi hoàn thiện giao diện Desktop theo chuẩn Figma `1440px` (`979:32231`), giao diện Mobile (`390px`) của Website Thành Khang hiện vẫn tồn tại nhiều điểm sai lệch so với thiết kế Figma Mobile chính thức (`fileKey: 9EO7DXSgsQ412ohf1Qxt4c`, node `2:9` gồm `1314:51987` Homepage Mobile `390×7810`, `1329:52835` Pop up mobile `342×691` và `1329:53125` Component mobile) cũng như các lỗi trải nghiệm/truy cập Mobile UX & Accessibility trên thực tế:
- Tại Trang chủ (`Home/Components/*`) và `Footer.vue`: `ServicesSection.vue` đang dựng thẻ hộp thay vì timeline 5 nút gợn sóng dọc bên trái; `ProductsSection.vue` vẫn hiện hộp mũi tên `>` trên mobile và ô thứ 6 chưa mặc định nền `#005EA4`; `BrandStorySection.vue` xếp sai thứ tự ảnh (`topImages` nằm trên `wideImage`) và cỡ chữ mobile nhỏ hơn spec; `Footer.vue` còn hiện 2 nút CTA và đường kẻ giữa lưới 2 cột; `ProcessSection.vue` bị kẹp chiều cao header + `line-clamp` cắt chữ + thiếu nền sóng ở danh sách bước; `ContactSection.vue` còn hiện ảnh xích trang trí trên mobile và nhãn nút chưa đồng nhất.
- Trên toàn site Mobile & Touch UX/A11y: `app.blade.php` còn `maximum-scale=1` cản trở zoom trợ năng; `Header.vue` lệch chiều cao topbar (`31px` thay vì `33px`) và thiếu `safe-area-inset-bottom` ở Drawer; `FloatingCtaWidget.vue` nút `52×52px` hơi lớn và chưa tính `env(safe-area-inset-bottom)`; `QuotePopupModal.vue` bị chữ cuộn đè sau nút `X` và chưa ẩn texture xích trên `< lg`; các ô tìm kiếm ở `Posts/Index.vue` và `DocumentLibrarySection.vue` dùng `14px` gây iOS Safari auto-zoom; nhiều icon tương tác chưa đạt vùng chạm `>= 44×44px`; Hero trang `/quy-trinh` (`ProcessHero.vue`) có nguy cơ đè giữa CTA mobile và huy hiệu xoay `CircularBadge` tại `390×844`; và bảng rộng trong `ProductDetailTabs.vue` làm cuộn ngang cả khối `.pdp-richtext`.

## What Changes

1. **6 hạng mục đồng bộ chính từ Figma Mobile `2:9` (`1314:51987`)**:
   - **`ServicesSection.vue`**: Dựng timeline metaball sóng dọc 5 nút bên trái trên mobile (vòng `#C9E0FF` + 4 cầu nối sóng dọc, văn bản trắng không đóng khung hộp trực tiếp trên nền tối của banner `611px`), căn `items-start` ở cụm vuông tiêu đề section.
   - **`ProductsSection.vue`**: Ẩn hộp icon mũi tên `>` trong chân thẻ danh mục ở `< lg`; ô thứ 6 ("Khám phá toàn bộ sản phẩm") mặc định nền `#005EA4` + chữ `#FAFAFA` trên mobile (giữ nguyên hiệu ứng hover/active `#C9E0FF` → `#005EA4` trên Desktop); khóa tỷ lệ thumbnail `aspect-[173/130]` (`[173/128]` cho ô thứ 6) và chiều cao chân thẻ `min-h-[64px]` / `[66px]` trên mobile.
   - **`BrandStorySection.vue`**: Trên `< lg`, đảo thứ tự hiển thị để `wideImage` (`aspect-[358/210]`) nằm **TRÊN** cụm 2 ảnh `topImages` (`aspect-[173/148]`); nâng cỡ chữ nội dung mobile lên `16px` (`text-[16px]`) và chữ nút CTA lên `18px` (`text-[18px]`).
   - **`Footer.vue`**: Trên `< lg`, ẩn 2 nút CTA (`Nhận báo giá`, `Gọi hotline`) và ẩn đường kẻ ngang phân cách giữa lưới; chuyển lưới liên kết sang 1 cột (`grid-cols-1 lg:grid-cols-3`, `gap-[20px]`).
   - **`ProcessSection.vue`**: Đặt chiều cao header banner mobile `500px` (`min-h-[500px]` / `aspect-[390/500]` khớp Figma `1314:52099`) và căn chữ lên trên (`justify-start pt-8`) để lộ phần đồ họa bánh răng phía dưới; gỡ bỏ `line-clamp-4` (mô tả section) và `line-clamp-3` (mô tả các bước); bật lớp nền sóng trang trí nhẹ trong khung bước `#F8FAFC` trên mobile; đồng bộ bo góc thẻ `rounded-[8px]` trên mobile.
   - **`ContactSection.vue`**: Ẩn ảnh trang trí `contact-chain-deco.png` trên mobile (`hidden md:block`), cập nhật nhãn nút submit thành `"Nhận tư vấn ngay"` với cỡ chữ `18px`, và chỉnh khoảng cách giữa khối thông tin với thẻ form trên mobile thành `gap-6` (`24px`).

2. **Quyết định UX đã chốt (Confirmed UX Decisions)**:
   - **Giữ lại `FloatingCtaWidget.vue`**: Thu gọn các nút nổi trên mobile từ `52×52px` xuống `44×44px` (`w-11 h-11 lg:w-[60px] lg:h-[60px]`) và định vị tại `right-3 bottom-[calc(16px+env(safe-area-inset-bottom))]` (Desktop giữ nguyên `lg:right-6 lg:bottom-[72px]`). Cập nhật delta spec `frontend-navigation-and-overlays`.
   - **Giữ Mobile Drawer (`Header.vue`) gọn sạch**: Tuyệt đối KHÔNG thêm bộ chuyển đổi ngôn ngữ `VI / EN` vào Header hay Mobile Drawer theo đúng quyết định của người dùng.

3. **Các hạng mục Figma `2:9` bổ sung & Sửa lỗi Mobile UX / Accessibility**:
   - **`HeroSection.vue`**: Áp dụng scrim ngang trên mobile khớp Figma `1314:52005` (`linear-gradient(90deg, #01101E 0%, rgba(1,16,30,0) 100%)`), tăng cỡ chữ eyebrow mobile lên `text-[18px] md:text-[24px] lg:text-[28px]`, cỡ chữ CTA lên `text-[18px]`, và bỏ `line-clamp-4` ở mô tả.
   - **`BlogSection.vue`**: Chuẩn hóa khoảng cách section `gap-6` (`24px`) và cỡ chữ nút CTA `18px`.
   - **`QuotePopupModal.vue`** (khớp Figma `1329:52835`): Ẩn toàn bộ texture xích trang trí ở `< lg`; tách nút đóng `X` lên hàng header cố định phía trên vùng cuộn để nội dung khi cuộn không trôi ngang qua sau nút `X`; chỉnh `max-h` mobile (`max-h-[calc(100dvh-32px)] lg:max-h-[calc(100dvh-112px)]`) và rà soát đồng bộ tiêu đề/nội dung fallback `"BÁO GIÁ CÁC SẢN PHẨM TẠI THÀNH KHANG"` nếu cấu hình CMS chưa ghi đè.
   - **`Header.vue` + `app.scss` + `MainLayout.vue`**: Cập nhật chiều cao mobile topbar từ `31px` lên `33px` (tổng chiều cao header mobile từ `92px` lên `94px` khớp `1314:51988`/`1314:51989`); bổ sung `pb-[env(safe-area-inset-bottom)]` cho Mobile Drawer.
   - **`resources/Frontend/views/app.blade.php`**: Gỡ `maximum-scale=1`, cập nhật thẻ meta viewport thành `content="width=device-width, initial-scale=1, viewport-fit=cover"`.
   - **`Posts/Index.vue` & `DocumentLibrarySection.vue`**: Cập nhật input tìm kiếm sang `text-base lg:text-[14px]` để ngăn iOS Safari tự động zoom khi chạm ô nhập.
   - **Vùng chạm tối thiểu `>= 44×44px` trên Mobile**: Mở rộng hit-area / padding đạt `>= 44×44px` cho các nút/icon tương tác trên mobile gồm nút Search & Hamburger ở `Header.vue`, các nút chấm (dot tabs) & mũi tên chuyển slide ở `HeroSection.vue`, nút đóng `X` ở `QuotePopupModal.vue`, nút xóa `X` ở `SearchModal.vue` và `DocumentLibrarySection.vue`, các liên kết icon mạng xã hội ở `Footer.vue`, và các nút mở rộng accordion danh mục ở Mobile Drawer (`Header.vue`).
   - **`Pages/Process/Index.vue` (`ProcessHero.vue`)**: Khắc phục lỗi bố cục khiến nút CTA mobile (`Khám phá 5 bước`) bị chồng lấn hoặc chen chúc với huy hiệu xoay tròn `CircularBadge` tại viewport `390×844` (và kiểm soát khoảng hở an toàn tới `sm:`).
   - **`ProductDetailTabs.vue`**: Bọc các thẻ `<table>` HTML rộng trong wrapper cuộn ngang riêng (theo mẫu `.article-table-wrap` của `Posts/Show.vue`) thay vì đặt `overflow-x: auto` lên toàn khối `.pdp-richtext`.

## Capabilities

### New Capabilities
- `homepage-mobile-figma-2-9`: Đồng bộ giao diện Mobile (`390px`) của Trang chủ (`HeroSection`, `ProductsSection`, `BrandStorySection`, `ServicesSection`, `ProcessSection`, `BlogSection`, `ContactSection`) và `Footer.vue` theo bản thiết kế Figma `2:9` (`1314:51987`).
- `mobile-ux-accessibility`: Chuẩn hóa Mobile UX & Accessibility toàn site gồm Header `33px`/`94px` + safe-area Drawer, thẻ meta viewport `viewport-fit=cover` không khóa zoom, chống iOS auto-zoom ở ô tìm kiếm, vùng chạm `>= 44×44px`, chống chồng lấn CTA/badge trên `ProcessHero.vue`, và wrapper cuộn bảng riêng cho `.pdp-richtext` trong `ProductDetailTabs.vue`.

### Modified Capabilities
- `frontend-navigation-and-overlays`: Cập nhật yêu cầu cho `FloatingCtaWidget.vue` (thu nhỏ nút mobile xuống `44×44px` và đệm `env(safe-area-inset-bottom)` với `right-3 bottom-[calc(16px+env(safe-area-inset-bottom))]`) và `QuotePopupModal.vue` (ẩn texture xích ở `< lg`, thanh topbar riêng cho nút `X` không bị chữ cuộn đè, và `max-h-[calc(100dvh-32px)]` trên mobile).

## Impact

- **Mã nguồn Frontend dự kiến chỉnh sửa (ở giai đoạn `/opsx-apply`)**:
  - `resources/Frontend/views/app.blade.php`
  - `resources/Frontend/scss/app.scss`
  - `resources/Frontend/js/Layouts/MainLayout.vue`
  - `resources/Frontend/js/Components/{Header,Footer,FloatingCtaWidget,QuotePopupModal,SearchModal}.vue`
  - `resources/Frontend/js/Pages/Home/Components/{HeroSection,ProductsSection,BrandStorySection,ServicesSection,ProcessSection,BlogSection,ContactSection}.vue`
  - `resources/Frontend/js/Pages/Posts/Index.vue`
  - `resources/Frontend/js/Pages/Documents/Components/DocumentLibrarySection.vue`
  - `resources/Frontend/js/Pages/Process/Components/ProcessHero.vue` (được render bởi `resources/Frontend/js/Pages/Process/Index.vue`)
  - `resources/Frontend/js/Pages/Product/Components/ProductDetailTabs.vue`
  - Từ điển i18n (`resources/Frontend/lang/{vi,en}.json` hoặc `resources/lang/{vi,en}/ui.php`) nếu bổ sung/đối chiếu chuỗi nhãn mới (như tiêu đề fallback QuotePopupModal nếu cần) và cập nhật/bổ sung Playwright E2E test cho Mobile `390×844` / `375×812` & Desktop `1440×900`.

## Phạm vi an toàn (Strict Safe Guard)

- **KHÔNG** chỉnh sửa, tạo mới hoặc xóa bất kỳ tệp mã nguồn, cấu hình, giao diện, asset hay CSDL nào ngoài `openspec/changes/sync-mobile-ui-figma-2-9/` trong bước lập đặc tả này.
- **KHÔNG** thêm bộ chuyển đổi ngôn ngữ `VI / EN` vào Header hoặc Mobile Drawer (`Header.vue`).
- **KHÔNG** xóa file (`rm`), không thay đổi database schema (`migrate:fresh`, `drop`, `truncate`), không thay đổi backend API contracts hay routing (`routes/frontend.php`, Controllers, Models).
- **KHÔNG** chỉnh sửa hay thay thế cấu trúc ảnh preview của `ProductGallery.vue` và giữ nguyên logic/hiển thị Desktop `1440px` đã nghiệm thu.
