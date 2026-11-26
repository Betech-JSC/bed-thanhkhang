# Proposal: Thiết lập Design Tokens & Layout Shell Thành Khang từ Figma Trang Chủ Official

## Why

Dự án **Công ty TNHH Dịch vụ Kỹ thuật Thành Khang Tecsat (`bed-thanhkhang`)** triển khai giao diện theo kịch bản **Home-First Incremental** từ thiết kế Figma Trang Chủ chính thức (`fileKey: Gu7RyTUDWaupomGrOH5EZO`):
- **Frame Home Desktop (official)**: `979:32231` (*"Homepage Thành Khang (official)"*, `1440×6252`, nằm trong Section `595:14086` *"Feedback lần 3 ( 25/11/2026 )"*).
- **Frame Home Mobile**: `1190:6335` (*"Homepage Thành Khang (Mobile)"*, `390×5328`, nằm trong Section `1190:6315` *"Mobile Ver"*).

Sau khi hoàn tất Bước 1 (`thanhkhang-seeders-and-cleanup`), tầng Frontend cần đồng bộ chính xác 100% với bản thiết kế Figma Official (`979:32231` & `1190:6335`):
1. Chuẩn hóa **Design Tokens** trong Tailwind CSS & SCSS (bảng màu thương hiệu `Primary #005EA4`, `Secondary #C9E0FF`, `Dark #00284B`, `Surface #FAFAFA / #FFFFFF / #EEF2F6`, typography `Open Sans` H1–H4 Desktop & Mobile `28px/20px/18px/16px`, hệ thống khung Dual-Grid `1280px` cho Hero/Footer và `1152px` cho Header/Body sections kèm gutter bắt buộc `px-4` mobile / `px-8` desktop).
2. Chuẩn hóa **Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`)** khớp tuyệt đối chi tiết Figma Official:
   - Desktop Header (`979:32411` & `979:32772`): Top bar `46px` (`#00284B`) + Main bar `72px` (`#FFFFFF`), khung `1152px`, 5 mục menu kèm Mega-menu 2 cột `SẢN PHẨM` (`856:24212`), nút Search + 2 CTA (`Nhận báo giá`, `Gọi hotline`).
   - Mobile Header (`1190:6336`): Hiển thị Top bar Mobile `31px` (`#00284B` gồm SĐT + Email) + Main Nav `61px` (`#FFFFFF`, `border-b #E3E8EF` gồm Logo `96×32`, Search `36×36`, CTA `Nhận báo giá` `100×33`, Hamburger `36×36 #EEF2F6`) + Slide-in Mobile Drawer.
   - Footer (`979:32406` / Master `217:3913` & Mobile `1193:6561`): Khối trên `#FAFAFA` (`1280px` trên Desktop) gồm cột thương hiệu `360px` + 3 cột `267px` (`Danh mục sản phẩm`, `Chính sách`, `Thông tin liên hệ` có dòng `Mã số thuế` với icon `building-07` `1110:37413`) và Bottom bar `#00284B` (`51px` Desktop / `48px` ngang trên Mobile); trên Mobile (`1193:6561`), 2 cột `Danh mục sản phẩm | Chính sách` xếp `grid-cols-2 gap-4`, ngăn cách với `Thông tin liên hệ` (icon `18×18`) bằng đường kẻ ngang `1px #E3E8EF` (`1193:6585`).

## What Changes

1. **Rút trích & lưu cục bộ tài nguyên Figma (`public/assets/`)**:
   - Sử dụng Logo thương hiệu `/assets/images/logo/logo-thanhkhang.png` (`979:32422` / `217:3857`) và trọn bộ SVG icons Header/Footer trong `/assets/svg/icons/` (bổ sung/chuẩn hóa đầy đủ các icon của bản Official như `footer-building.svg` `1110:37413`, `header-menu.svg` `1190:6355`, `header-chevron-down.svg` `979:29426`, `header-chevron-down-active.svg` `979:29431`, `header-chevron-right.svg` `689:18702`). Tuyệt đối không dùng URL tạm của Figma tại runtime.
2. **Cập nhật Design Tokens & Global Styling (`resources/Frontend/`)**:
   - Cập nhật `resources/Frontend/tailwind.config.js`: bảng màu `primary` (`#005EA4`, hover `#004D86`, active `#003967`, info `#0075E7`, sub-hover `#007FD9`, accent `#009FFF`), `secondary`, `dark`, `surface` (`#FAFAFA`, `#FFFFFF`, `#F8FAFC`, `#EEF2F6`, `#E3E8EF`, `#CDD5DF`), `ink`, `field`; `fontFamily` (`Open Sans` chủ đạo + `Inter` phụ); thang đo `fontSize` H1–H4 Desktop (`48px / 28px / 24px / 20px`) & Mobile (`28px / 20px / 18px / 16px`) và Body (`20px / 18px / 16px / 14px / 13px / 12px`); cấu hình `container` (`center: true`, `px-4` mobile / `px-8` desktop, `screens.xl: 1344px` tương ứng `1280px` inner) và `maxWidth` (`container-wide: 1344px`, `container-narrow: 1216px`), đồng thời giữ nguyên backward-compatible aliases.
   - Cập nhật `resources/Frontend/scss/app.scss`: đồng bộ biến `:root` CSS variables, cưỡng chế kế thừa `font-family: var(--font-sans)` trên `body, button, input, select, textarea`, cập nhật base `h1`–`h4` và `.app-container`, `.app-container-wide`, `.app-container-narrow`, `.app-container-content`.
   - Đảm bảo nạp đầy đủ Google Fonts `Open Sans` & `Inter` hỗ trợ tiếng Việt trong `resources/Frontend/views/app.blade.php`.
3. **Nâng cấp Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`) & Trang Tài liệu (`Pages/Documents/Index.vue`)**:
   - Cập nhật `MainLayout.vue`, `Header.vue` (Desktop `979:32411` & `979:32772` + Mega-menu 3 cấp `856:24212` + Mobile `1190:6336` + Mobile Drawer) và `Footer.vue` (Desktop `979:32406` / `217:3913` + Mobile `1193:6561`), liên kết 100% dữ liệu động từ `$page.props.configs` (được `HandleInertiaFrontendRequests.php:281-285` chia sẻ trực tiếp gồm toàn bộ `$global` + `menus` có `TÀI LIỆU` -> `/tai-lieu` / `DOCUMENTS` -> `/en/documents` + `product_categories` 3 cấp + `policies`, kết hợp merge an toàn với `$page.props.global`, `$page.props.product_categories`, `$page.props.data?.policies`).
   - Dựng `resources/Frontend/js/Pages/Documents/Index.vue` đối chiếu trực tiếp Figma Frame `1006:2634` (*"Tài liệu - danh mục"*), nhận dữ liệu động từ `DocumentController@index` và hiển thị Empty State B2B + danh mục thiết bị thực khi chưa có bài tài liệu.

## Capabilities

### New Capabilities
- `design-tokens-layout-shell`: Hệ thống Design Tokens tập trung (màu sắc, `Open Sans` typography scale Desktop/Mobile, Dual-Grid containers `1280px`/`1152px` + `px-4`/`px-8` gutters, shadows) và Layout Shell Responsive (Desktop `1440px` + Mobile `390px`/`375px` kèm Mega-menu & Mobile Drawer) chuẩn Figma Trang Chủ Official Thành Khang, kết nối 100% dữ liệu động CMS.

### Modified Capabilities
- Không sửa đổi schema database, không sửa code Backend PHP hay Admin CMS.

## Impact

- **Tài nguyên tĩnh (`public/assets/`)**:
  - `public/assets/images/logo/logo-thanhkhang.png`
  - `public/assets/svg/icons/{header-mail,header-phone,header-chevron-down,header-chevron-down-active,header-chevron-right,header-search,header-menu,footer-phone,footer-mail,footer-building,footer-pin,footer-facebook,footer-linkedin}.svg`
- **Styling & Blade**:
  - `resources/Frontend/tailwind.config.js`
  - `resources/Frontend/scss/app.scss`
  - `resources/Frontend/views/app.blade.php`
- **Frontend Vue Components, Layouts & Pages**:
  - `resources/Frontend/js/Layouts/MainLayout.vue`
  - `resources/Frontend/js/Components/Header.vue`
  - `resources/Frontend/js/Components/Footer.vue`
  - `resources/Frontend/js/Pages/Documents/Index.vue`
- **AST Map**: `graphify-out/graph.json` & `graphify-out/GRAPH_REPORT.md` (đồng bộ qua `graphify . --code-only`).

## Phạm vi an toàn (Strict Safe Guard)

- **Tuyệt đối KHÔNG xóa file** (`rm`, `Remove-Item` lên bất kỳ file nào của dự án; giữ nguyên `Layouts/Guest.vue` và các file SCSS/icon hiện có).
- **Tuyệt đối KHÔNG thao tác Backend hoặc Database** (không sửa PHP, không chạy `migrate`, `seed`, `drop`, `truncate`).
- **Tuyệt đối KHÔNG gán cứng mock** khi đã có dữ liệu từ `$page.props.configs` / `$page.props.global` / `$page.props.product_categories` / `$page.props.data.policies`.
