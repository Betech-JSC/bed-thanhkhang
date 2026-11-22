# Tasks: Thiết lập Design Tokens & Layout Shell Thành Khang từ Figma Trang Chủ

## 1. Trích xuất Figma, Tài nguyên Cục bộ & Khởi tạo OpenSpec (`figma-extractor`)

- [x] 1.1 Trích xuất Design Tokens (`get_variable_defs`), cấu trúc Header (`595:15540`) và Footer (`595:15535`) từ Figma Trang Chủ Thành Khang (`Gu7RyTUDWaupomGrOH5EZO`, frame `595:15360`). *(Tiêu chí: có đủ mã hex, font `Open Sans`, H1–H4/Body scale, container/gutters, blueprint Desktop & Mobile)*
- [x] 1.2 Tải cục bộ Logo (`logo-thanhkhang.png`) vào `public/assets/images/logo/` và 9 SVG icons Header/Footer vào `public/assets/svg/icons/`. *(Tiêu chí: `ls -lh` & `file` xác nhận 10/10 file tồn tại, dung lượng > 0B, đúng PNG/SVG)*
- [x] 1.3 Tạo trọn bộ tài liệu OpenSpec `openspec/changes/setup-core-from-home/` (`proposal.md`, `design.md`, `tasks.md`, `specs/design-tokens-layout-shell/spec.md`) đối chiếu `graphify-out/graph.json`. *(Tiêu chí: 4 file đầy đủ cấu trúc tiếng Việt chuẩn `openspec/config.yaml`)*

## 2. Design Tokens, Font Loading & Global Styling (`tokens-dev`)

- [x] 2.1 Cập nhật `resources/Frontend/views/app.blade.php` nạp Google Font `Open Sans` (`wght@400;500;600;700`, hỗ trợ tiếng Việt). *(Tiêu chí: thẻ `<link>` font `Open Sans` có mặt trong HTML head)*
- [x] 2.2 Cập nhật `resources/Frontend/tailwind.config.js` với bảng màu Thành Khang (`#005EA4`, `#004D86`, `#003967`, `#C9E0FF`, `#81BDFF`, `#00284B`, `#FAFAFA`, neutral/border, `shadow-xs`), `fontFamily.sans` ưu tiên `Open Sans`, và `container` (`center: true`, `max-w: 1280px`, `px-4` mobile / `px-8` desktop) kèm giữ backward-compatible aliases. *(Tiêu chí: config hợp lệ, không vỡ build Tailwind)*
- [x] 2.3 Cập nhật `resources/Frontend/scss/app.scss` (và `resources/Frontend/scss/customize/type.scss` nếu cần): cưỡng chế kế thừa `font-family` trên `body, button, input, select, textarea`, định nghĩa scale H1–H4 & Body Desktop/Mobile và CSS variables chiều cao Header. *(Tiêu chí: computed `font-family` là `Open Sans` trên mọi thẻ nhập liệu/nút bấm)*

## 3. Layout Shell, Header & Footer (`layout-dev`)

- [x] 3.1 Nâng cấp `resources/Frontend/js/Layouts/MainLayout.vue`: áp nền sáng `bg-surface` (`#FAFAFA`), đồng bộ `main` offset với sticky Header và giữ nguyên `Layouts/Guest.vue` để tương thích ngược. *(Tiêu chí: trang `/` render sạch trong `MainLayout.vue`, không khoảng trắng thừa hay che nội dung)*
- [x] 3.2 Nâng cấp `resources/Frontend/js/Components/Header.vue` theo Figma `595:15540`: Top bar `46px` (`#00284B`), Main bar `72px` (`#FAFAFA`, sticky), Logo `/assets/images/logo/logo-thanhkhang.png` (ưu tiên CMS), 5 nav items + dropdown `SẢN PHẨM` từ `$page.props.product_categories`, tìm kiếm, 2 CTA (`Nhận báo giá`, `Gọi hotline`), và Mobile Slide-in Drawer (`375px`, Esc/backdrop/scroll-lock). *(Tiêu chí: sử dụng 100% SVG trong `/assets/svg/icons/` và dữ liệu động `$page.props.configs || $page.props.global`)*
- [x] 3.3 Nâng cấp `resources/Frontend/js/Components/Footer.vue` theo Figma `595:15535`: Top section `bg-[#FAFAFA]` (cột thương hiệu `lg:w-[360px]` Logo `203×68` + 3 cột `Trang chủ`, `Chính sách` động từ `$page.props.data?.policies`, `Thông tin liên hệ` kèm SVG icons) và Bottom bar `#00284B` (copyright + social Facebook/LinkedIn). *(Tiêu chí: hiển thị đúng Desktop 1440px & xếp dọc gọn Mobile 375px)*

## 4. Build, Graphify AST Sync & Playwright Visual QA (`layout-dev` & `qa-verifier`)

- [x] 4.1 Chạy `yarn frontend:build` (Client + SSR) đảm bảo biên dịch thành công **0 errors**. *(Tiêu chí: raw terminal output exit code 0)*
- [x] 4.2 Chạy `graphify . --code-only` cập nhật bản đồ AST `graphify-out/graph.json`. *(Tiêu chí: lệnh kết thúc exit code 0)*
- [x] 4.3 Chạy Playwright Visual QA ở **Desktop (`1440×900`)**: kiểm tra Header top/sticky, dropdown `SẢN PHẨM`, Footer, computed `font-family` (`Open Sans`), bảng màu, 0 ảnh vỡ, 0 console errors, 0 horizontal overflow (`scrollWidth <= 1440`), kèm ảnh chụp màn hình bằng chứng. *(Tiêu chí: toàn bộ assertion PASS + screenshots)*
- [x] 4.4 Chạy Playwright Visual QA ở **Mobile (`375×812`)**: kiểm tra Header mobile, gutter `px-4`, mở/đóng Mobile Drawer, Footer stacking, 0 ảnh vỡ, 0 console errors, 0 horizontal overflow (`scrollWidth <= 375`), kèm ảnh chụp màn hình bằng chứng. *(Tiêu chí: toàn bộ assertion PASS + screenshots)*
