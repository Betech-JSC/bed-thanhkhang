# Proposal

## Why

Hiện tại, hệ thống layout và khung điều hướng (Navigation Shell) của HH Maker đang gặp các vấn đề lớn về tính nhất quán thẩm mỹ và kiến trúc mã nguồn:
1. **Thiết kế Header & Menu phân mảnh**: Header vẫn mang màu nền kem `#F4EFE2`, viền xám cũ và các nút màu cam `#D34000` từ theme FunkyStyle cũ, chưa được chuyển đổi sang phong cách Dark Tech theo thiết kế Figma HH-Maker (Page "A" - Node 2261:5076, 2352:10717 & 2352:10379). Menu thiếu thanh gạch chân active 2px màu xanh công nghệ `#0052FF`, thiếu nút chuyển ngữ capsule `VIE / ENG` (Figma 2380:1456) và icon giỏ hàng hiển thị badge số lượng sản phẩm.
2. **Mobile Menu trải nghiệm kém**: Trình đơn trên thiết bị di động hiện sử dụng dropdown accordion sổ dọc đẩy nội dung trang xuống, thay vì ngăn kéo trượt (Slide-over Drawer) hiện đại với nền mờ tối, gây rối mắt và khó thao tác trên màn hình điện thoại (375px/390px).
3. **Trùng lặp Footer và Layout chưa chuẩn hóa**: Footer Dark Tech mới chỉ được viết tạm thời tại `resources/Frontend/js/Pages/Home/Components/HomeFooter.vue`. Cả 4 trang (`Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, `Product/Show.vue`) đều phải tự import và gắn `<HomeFooter />` riêng lẻ. Đồng thời `resources/Frontend/js/Layouts/Guest.vue` phải sử dụng logic điều kiện phức tạp `isMigrated` để ẩn/hiện footer cũ.
4. **Thiếu hạ tầng kiểm thử giao diện tự động (E2E Testing)**: Dự án chưa có công cụ kiểm thử tự động đa khung hình (Desktop 1440px và Mobile 375px) để bảo đảm layout không bị vỡ giao diện (visual regression) sau mỗi lần cập nhật.

Việc thiết lập hạ tầng Layout Shell đồng bộ toàn site và tích hợp Playwright E2E QA là bước đi cốt lõi nhằm hoàn thiện khung sườn giao diện chuẩn Dark Tech, nâng cao trải nghiệm người dùng và thiết lập tiêu chuẩn kiểm thử chất lượng cao cho toàn bộ dự án.

## What Changes

- **Header Dark Tech toàn diện (Figma 2352:10717 & 2352:10379)**:
  - Cập nhật `resources/Frontend/js/Components/Header.vue` sang nền tối kính mờ `bg-dark-bg/90 backdrop-blur-md border-b border-dark-border`, cố định đầu trang (`fixed top-0 inset-x-0 z-[1000]`).
  - Đường gạch chân hiển thị trạng thái active của menu máy tính dày 2px màu xanh `#0052FF` (`h-[2px] bg-primary`).
  - Thêm cụm nút chuyển đổi ngôn ngữ dạng capsule `VIE / ENG` theo chuẩn Figma Node 2380:1456 với hiệu ứng active sang trọng.
  - Tích hợp biểu tượng Giỏ hàng với badge đếm số lượng mặt hàng (kết nối dữ liệu giỏ hàng hoặc store).
  - Tối ưu khu vực tài khoản/đăng nhập và thông báo theo ngôn ngữ Dark Tech.
- **Mobile Menu Slide-over Drawer hiện đại**:
  - Tái cấu trúc menu di động thành ngăn kéo trượt (Slide-over Drawer) sử dụng `<Teleport to="body">` và animation trượt từ cạnh phải sang trái.
  - Phủ lớp backdrop làm mờ tối `bg-black/70 backdrop-blur-sm`, đóng khi click ra ngoài hoặc nhấn phím `Escape`.
  - Tối ưu hóa vùng chạm cảm ứng (touch target $\ge$ 44px) và hiển thị gọn gàng trên màn hình 375px/390px.
- **Hợp nhất Footer Dark Tech dùng chung (Figma 2261:5820)**:
  - Chuyển giao diện từ `HomeFooter.vue` thành `resources/Frontend/js/Components/Footer.vue` chuẩn toàn cục của hệ thống.
  - Cấu trúc lưới 4 cột (Brand & xưởng in 3D, Dịch vụ, Sản phẩm, Hỗ trợ & Chính sách) kèm thanh bản quyền phía dưới.
  - Linh hoạt hóa thông tin liên hệ (hotline, email, địa chỉ xưởng sản xuất tại Hà Nội & TP. HCM).
- **Thiết lập MainLayout dùng chung (`resources/Frontend/js/Layouts/MainLayout.vue`)**:
  - Tạo layout mặc định bao bọc tự động `<Header />`, `<main><slot /></main>`, và `<Footer />`.
  - Cấu hình `MainLayout` làm default layout trong `resources/Frontend/js/app.js` (thay thế dần `Guest.vue` cho các trang giao diện công khai).
  - Loại bỏ các lệnh import và thẻ gọi `<HomeFooter />` lặp lại tại 4 trang: `Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, và `Product/Show.vue`.
- **Hạ tầng kiểm thử tự động Playwright E2E**:
  - Cài đặt `@playwright/test` vào `devDependencies`.
  - Khởi tạo file cấu hình `playwright.config.ts` hỗ trợ chạy headless với các project cấu hình Desktop Chrome (1440x900) và Mobile Chrome / iPhone (375x667).
  - Xây dựng kịch bản kiểm thử `tests/e2e/layout-shell.spec.ts` kiểm tra:
    + Hiển thị Header và Footer Dark Tech trên Desktop 1440px.
    + Hoạt động đóng/mở của Mobile Menu Slide-over Drawer trên Mobile 375px.
    + Chuyển đổi ngôn ngữ VIE/ENG và hiển thị badge giỏ hàng.
    + Không xuất hiện lỗi console JS nghiêm trọng hoặc tràn ngang (horizontal scrollbar overflow).

## Capabilities

### New Capabilities
- `layout-shell`: Cung cấp hạ tầng khung giao diện Layout Shell chuẩn Dark Tech đồng bộ toàn site (MainLayout, Header, Footer, Mobile Drawer) và bộ kiểm thử tự động Playwright E2E cho đa độ phân giải.

### Modified Capabilities
<!-- Không có capability hiện hữu nào bị thay đổi requirement -->

## Impact

- **Mã nguồn Frontend (`resources/Frontend/js/`)**:
  - File tạo mới: `resources/Frontend/js/Layouts/MainLayout.vue`.
  - File cập nhật: `resources/Frontend/js/Components/Header.vue`, `resources/Frontend/js/Components/Footer.vue`, `resources/Frontend/js/app.js`.
  - File dọn dẹp: Dỡ bỏ `<HomeFooter />` trong `Pages/Home/Index.vue`, `Pages/About/Index.vue`, `Pages/Product/Index.vue`, `Pages/Product/Show.vue`; đánh dấu deprecate `Pages/Home/Components/HomeFooter.vue`.
- **Hạ tầng Testing & Scripts (`package.json`)**:
  - Bổ sung `@playwright/test` vào `devDependencies`.
  - Thêm script `test:e2e` vào `package.json`.
  - Tạo mới `playwright.config.ts` và kịch bản `tests/e2e/layout-shell.spec.ts`.
- **Hệ thống & An toàn**:
  - Không thay đổi cơ sở dữ liệu, schema, backend routes hay API contracts của Laravel.
  - Tương thích hoàn toàn với Tailwind CSS Dark Tech và các Base Components (`BaseButton`, `BaseBadge`, `BaseIcon`) đã tạo trước đó.
