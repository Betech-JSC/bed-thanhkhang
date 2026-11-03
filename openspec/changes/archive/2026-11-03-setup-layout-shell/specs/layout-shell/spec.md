# Spec Delta

## Purpose

Cung cấp hạ tầng Layout Shell đồng bộ toàn site chuẩn Dark Tech theo thiết kế Figma HH-Maker bao gồm MainLayout, Header, Footer, Mobile Drawer, và bộ kiểm thử tự động Playwright E2E cho toàn bộ hệ thống giao diện người dùng của HH Maker.

## ADDED Requirements

### Requirement: Header Dark Tech toàn diện chuẩn thiết kế Figma
Hệ thống SHALL cung cấp component Header chuẩn Dark Tech cố định ở đầu trang (`fixed top-0 inset-x-0 z-[1000]`) với nền tối kính mờ `bg-dark-bg/90 backdrop-blur-md border-b border-dark-border`, logo thương hiệu HH Maker, danh sách liên kết điều hướng máy tính, và thanh chỉ báo trạng thái active 2px màu xanh `#0052FF` (`h-[2px] bg-primary`) tại mục menu tương ứng với đường dẫn hiện tại.

#### Scenario: Hiển thị Header cố định với nền Dark Tech kính mờ
- **WHEN** người dùng truy cập bất kỳ trang nào trên website HH Maker trên màn hình máy tính
- **THEN** Header hiển thị cố định ở cạnh trên màn hình với nền tối mờ nhìn xuyên thấu nhẹ, đường viền phân cách dưới màu `dark-border`, và logo HH Maker hiển thị rõ ràng.

#### Scenario: Hiển thị thanh gạch chân active 2px màu xanh cho menu hiện tại
- **WHEN** đường dẫn URL hiện tại trùng khớp với mục menu trong danh sách điều hướng (ví dụ `/san-pham` hoặc `/gioi-thieu`)
- **THEN** mục menu tương ứng hiển thị thanh gạch chân dày 2px với mã màu xanh công nghệ `#0052FF`, và chữ hiển thị độ sáng màu trắng nổi bật.

---

### Requirement: Bộ chuyển đổi ngôn ngữ Capsule VIE / ENG
Hệ thống SHALL cung cấp cụm nút bấm chuyển đổi ngôn ngữ dạng capsule bo tròn (`VIE / ENG`) theo chuẩn Figma Node 2380:1456, hiển thị trên thanh Header máy tính và bên trong ngăn kéo Mobile Drawer, cho phép người dùng chuyển đổi qua lại giữa tiếng Việt và tiếng Anh với hiệu ứng nổi bật (highlight capsule) tại ngôn ngữ đang chọn.

#### Scenario: Hiển thị trạng thái ngôn ngữ được kích hoạt
- **WHEN** ngôn ngữ hiện tại của ứng dụng là tiếng Việt (`vi`) hoặc tiếng Anh (`en`)
- **THEN** nút capsule tương ứng hiển thị trạng thái active với nền xanh công nghệ hoặc nền sáng nổi bật, trong khi ngôn ngữ còn lại hiển thị màu chữ mờ hơn.

#### Scenario: Chuyển đổi ngôn ngữ khi người dùng tương tác
- **WHEN** người dùng nhấp chọn vào mã ngôn ngữ đối diện (ví dụ đang ở `VIE` và bấm sang `ENG`)
- **THEN** hệ thống kích hoạt cơ chế chuyển ngữ thông qua i18n/Inertia mà không gây lỗi giao diện.

---

### Requirement: Biểu tượng Giỏ hàng với huy hiệu đếm số lượng
Hệ thống SHALL cung cấp biểu tượng Giỏ hàng tại khu vực bên phải Header với huy hiệu (badge) hiển thị tổng số lượng sản phẩm hiện có trong giỏ hàng của người dùng.

#### Scenario: Hiển thị số lượng mặt hàng trên huy hiệu giỏ hàng
- **WHEN** giỏ hàng của người dùng có chứa sản phẩm (số lượng $\ge$ 1)
- **THEN** biểu tượng giỏ hàng hiển thị một huy hiệu nhỏ màu đỏ hoặc xanh công nghệ ở góc trên bên phải với số lượng sản phẩm tương ứng.

#### Scenario: Ẩn hoặc hiển thị trạng thái 0 khi giỏ hàng trống
- **WHEN** giỏ hàng chưa có sản phẩm nào
- **THEN** badge hiển thị số 0 hoặc ẩn đi một cách thẩm mỹ, không làm xô lệch kích thước của nút giỏ hàng.

---

### Requirement: Footer Dark Tech dùng chung toàn hệ thống
Hệ thống SHALL cung cấp component chân trang `Footer.vue` dùng chung chuẩn Dark Tech tại `resources/Frontend/js/Components/Footer.vue` (kế thừa từ `HomeFooter.vue` theo Figma Node 2261:5820) với bố cục lưới 4 cột đáp ứng (Thông tin thương hiệu & xưởng in 3D, Dịch vụ, Sản phẩm, Hỗ trợ khách hàng) và thanh bản quyền chân trang.

#### Scenario: Hiển thị lưới thông tin chân trang 4 cột trên Desktop
- **WHEN** người dùng cuộn xuống chân trang trên màn hình máy tính (viewport $\ge$ 1024px)
- **THEN** Footer hiển thị đầy đủ 4 cột thông tin với logo, mô tả, thông tin liên hệ (hotline, email, xưởng in tại Hà Nội và TP. HCM), các danh mục liên kết, và dải bản quyền chân trang trên nền `bg-dark-bg`.

#### Scenario: Hiển thị Footer co giãn thân thiện trên Mobile
- **WHEN** người dùng truy cập website trên thiết bị di động (viewport 375px - 768px)
- **THEN** các cột Footer tự động xếp dọc thành 1 cột hoặc 2 cột linh hoạt, khoảng cách và cỡ chữ được tối ưu, không xuất hiện hiện tượng tràn ngang màn hình.

---

### Requirement: Mobile Menu Slide-over Drawer với Teleport to body
Hệ thống SHALL cung cấp ngăn kéo trượt điều hướng di động (Slide-over Drawer) sử dụng `<Teleport to="body">`, mở từ cạnh phải màn hình với nền tối mờ `bg-black/70 backdrop-blur-sm`, hiển thị đầy đủ danh sách menu, cụm chuyển đổi ngôn ngữ `VIE / ENG`, liên kết đăng nhập/tài khoản, và nút đóng X chuẩn. Khi mở ngăn kéo, cuộn trang của thẻ `<body>` SHALL bị khóa.

#### Scenario: Mở ngăn kéo trượt khi nhấn nút Hamburger
- **WHEN** người dùng nhấn vào nút Hamburger trên thanh Header ở giao diện di động
- **THEN** ngăn kéo trượt mượt mà từ cạnh phải sang trái với nền mờ tối bao phủ màn hình chính, thẻ `<body>` bị khóa cuộn, và danh sách menu hiển thị rõ ràng với vùng chạm $\ge$ 44px.

#### Scenario: Đóng ngăn kéo trượt khi click nút đóng, backdrop hoặc phím Escape
- **WHEN** người dùng nhấn vào nút đóng X, nhấp chuột vào vùng backdrop làm mờ bên ngoài ngăn kéo, hoặc nhấn phím `Escape` trên bàn phím
- **THEN** ngăn kéo trượt thụt về bên phải và ẩn đi, lớp backdrop mờ biến mất, và khả năng cuộn trang của thẻ `<body>` được khôi phục ngay lập tức.

---

### Requirement: MainLayout dùng chung và loại bỏ trùng lặp cấu trúc
Hệ thống SHALL cung cấp layout `MainLayout.vue` tại `resources/Frontend/js/Layouts/MainLayout.vue` tự động bọc `<Header />`, phân vùng nội dung chính `<main><slot /></main>`, và `<Footer />`. Hệ thống SHALL thiết lập `MainLayout` làm layout mặc định trong `resources/Frontend/js/app.js`, đồng thời dọn dẹp các lệnh import và thẻ `<HomeFooter />` lặp lại tại các trang con (`Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, `Product/Show.vue`).

#### Scenario: Tự động kế thừa Header và Footer trên các trang con
- **WHEN** người dùng điều hướng qua các trang như Trang chủ, Giới thiệu, hay Sản phẩm
- **THEN** trang tự động kế thừa Header Dark Tech và Footer toàn cục thông qua `MainLayout` mà không cần từng trang con tự import Header/Footer riêng.

#### Scenario: Dọn dẹp sạch sẽ mã nguồn import HomeFooter lặp lại
- **WHEN** kiểm tra mã nguồn tại 4 trang `Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, và `Product/Show.vue`
- **THEN** không còn chứa lệnh `import HomeFooter` hay thẻ `<HomeFooter />` cục bộ, mã nguồn mỗi trang tập trung hoàn toàn vào nội dung nghiệp vụ riêng của trang đó.

---

### Requirement: Bộ kiểm thử tự động Playwright E2E cho Layout Shell
Hệ thống SHALL cung cấp cấu hình kiểm thử tự động Playwright E2E (`playwright.config.ts`) và kịch bản kiểm thử `tests/e2e/layout-shell.spec.ts` cho phép kiểm chứng tự động giao diện và tương tác của Layout Shell trên Desktop (1440x900) và Mobile (375x667), xác nhận không có lỗi tràn khung ngang (`document.documentElement.scrollWidth > window.innerWidth`) và không có lỗi console JavaScript nghiêm trọng.

#### Scenario: Chạy kiểm thử Desktop 1440px xác thực Header và Footer
- **WHEN** kịch bản Playwright E2E chạy ở chế độ Desktop Chrome (1440x900)
- **THEN** bài test kiểm tra Header hiển thị cố định ở đầu trang, menu máy tính và nút capsule ngôn ngữ hiển thị đầy đủ, Footer hiển thị lưới 4 cột ở đáy trang, và không có thanh cuộn ngang ngoài ý muốn.

#### Scenario: Chạy kiểm thử Mobile 375px xác thực ngăn kéo trượt Mobile Drawer
- **WHEN** kịch bản Playwright E2E chạy ở chế độ Mobile Chrome / Viewport 375px
- **THEN** bài test kiểm tra nút Hamburger hiển thị, nhấn mở Mobile Drawer thành công, xác nhận các liên kết menu và nút đóng hoạt động chính xác, đóng drawer thành công và cuộn trang được trả về bình thường.
