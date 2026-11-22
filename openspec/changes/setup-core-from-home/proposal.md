# Proposal: Thiết lập Design Tokens & Layout Shell Thành Khang từ Figma Trang Chủ

## Why

Dự án **Công ty TNHH Dịch vụ Kỹ thuật Thành Khang (Thành Khang Tecsat)** sử dụng quy trình **Home-First Incremental** từ thiết kế Figma Trang Chủ (`fileKey: Gu7RyTUDWaupomGrOH5EZO`, frame `595:15360` — *"Homepage Thành Khang option 6"*, 1440×6478). Hiện tại tầng giao diện Frontend vẫn còn mang bộ Design Tokens, font chữ (`Be Vietnam Pro` / nền tối `bg-dark-bg`), và cấu trúc Header/Footer cũ của theme trước đây, chưa khớp hệ nhận diện xanh công nghiệp sáng (`#005EA4`, `#00284B`, `#C9E0FF`, nền `#FAFAFA`) và font `Open Sans` theo thiết kế Figma chốt mới nhất.

Cần thiết lập đồng bộ nền tảng **Design Tokens + Global Typography + Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`)** ngay ở Phase 1 để toàn bộ các trang/section tiếp theo kế thừa chuẩn màu sắc, typography, container/gutter và kết nối dữ liệu động CMS mà không hardcode.

## What Changes

1. **Rút trích & lưu cục bộ tài nguyên Figma (`public/assets/`)**:
   - Tải chính xác Logo thương hiệu (`Artboard 5 1`) về `public/assets/images/logo/logo-thanhkhang.png` và 9 SVG icons Header/Footer về `public/assets/svg/icons/` (tuyệt đối không sử dụng URL tạm của Figma tại runtime).
2. **Cập nhật Design Tokens & Global Styling (`resources/Frontend/`)**:
   - Cấu hình bảng màu Thành Khang (`primary: #005EA4`, `primary-hover: #004D86`, `primary-active: #003967`, `secondary: #C9E0FF`, `dark: #00284B`, `surface: #FAFAFA`, neutral/border/shadow `xs`) trong `resources/Frontend/tailwind.config.js`, đồng thời giữ backward-compatible aliases cho các trang cũ chưa refactor.
   - Tích hợp Google Font **`Open Sans`** (`400, 500, 600, 700`, subset Vietnamese) trong `resources/Frontend/views/app.blade.php` với fallback `Inter, system-ui, sans-serif`.
   - Chuẩn hóa hệ typography H1–H4 & Body scale (Desktop/Mobile), bắt buộc kế thừa `font-family` trên `body, button, input, select, textarea` trong `resources/Frontend/scss/app.scss`, và chuẩn hóa Tailwind `container` (`max-w-[1280px]`, padding `px-4` mobile / `px-8` desktop, hỗ trợ khung nội dung `1152px`).
3. **Nâng cấp Layout Shell (`MainLayout.vue`, `Header.vue`, `Footer.vue`)**:
   - Tái cấu trúc `Header.vue` theo Figma node `595:15540`: Top bar `46px` (`#00284B`) hiển thị tên công ty, Email, Hotline; Main bar `72px` (`#FAFAFA`, sticky khi cuộn) gồm Logo `120×40`, 5 mục điều hướng chính (`VỀ CHÚNG TÔI`, `SẢN PHẨM` kèm dropdown `$page.props.product_categories`, `TÀI LIỆU`, `TIN TỨC`, `LIÊN HỆ`), nút tìm kiếm và 2 CTA (`Nhận báo giá`, `Gọi hotline`).
   - Dựng **Mobile Header & Slide-in Drawer** (`375px`) với nút Hamburger truy cập được bằng bàn phím/ARIA, danh sách menu + accordion danh mục sản phẩm, CTA, khóa cuộn body và đóng bằng `Esc`/backdrop.
   - Tái cấu trúc `Footer.vue` theo Figma node `595:15535`: Khối trên nền `#FAFAFA` (`pt-10 pb-[60px]`) gồm cột thương hiệu (`lg:w-[360px]`, Logo `203×68`) + 3 cột (`Trang chủ`, `Chính sách` động từ `$page.props.data?.policies`, `Thông tin liên hệ`); thanh Bottom bar `#00284B` gồm bản quyền và icon mạng xã hội Facebook/LinkedIn.
   - Ràng buộc dữ liệu động 100% từ CMS qua `$page.props.configs || $page.props.global` (và `$page.props.product_categories`, `$page.props.data?.policies`), giữ nguyên `Layouts/Guest.vue` để tương thích ngược.

## Capabilities

### New Capabilities
- `design-tokens-layout-shell`: Hệ thống Design Tokens tập trung (màu sắc, `Open Sans` typography scale, container/gutters, shadows) và Layout Shell Responsive (Desktop 1440px + Mobile 375px Drawer) chuẩn Figma Trang Chủ Thành Khang, liên kết 100% dữ liệu động CMS.

### Modified Capabilities
- Không sửa đổi schema database, không sửa Admin CMS backend.

## Impact

- **Tài nguyên tĩnh (mới)**:
  - `public/assets/images/logo/logo-thanhkhang.png`
  - `public/assets/svg/icons/{header-mail,header-phone,header-chevron-down,header-search,footer-phone,footer-mail,footer-pin,footer-facebook,footer-linkedin}.svg`
- **Styling & Blade**:
  - `resources/Frontend/tailwind.config.js`
  - `resources/Frontend/scss/app.scss` (và `resources/Frontend/scss/customize/type.scss` nếu cần đồng bộ utility)
  - `resources/Frontend/views/app.blade.php`
- **Frontend Vue Components & Layouts**:
  - `resources/Frontend/js/Layouts/MainLayout.vue`
  - `resources/Frontend/js/Components/Header.vue`
  - `resources/Frontend/js/Components/Footer.vue`
- **AST Map**: `graphify-out/graph.json` (cập nhật bằng `graphify . --code-only` sau khi sửa component).

## Phạm vi an toàn (Strict Safe Guard)

- **Tuyệt đối KHÔNG xóa file** (không dùng `rm` hoặc xóa `Layouts/Guest.vue`, các file SCSS/icon cũ đang được trang khác tham chiếu).
- **Tuyệt đối KHÔNG thao tác Database** (không chạy migration, seeder, truncate hay sửa bảng DB).
- **Tuyệt đối KHÔNG thay đổi kiến trúc thư mục** hay sửa đổi Admin Backend (`resources/Backend/`, `app/Models/`, `routes/`).
- Giữ backward-compatible color/typography aliases để các trang con hiện có không bị vỡ giao diện trước khi refactor cuốn chiếu.
