# Design

## Context

Hệ thống giao diện khách hàng của HH Maker đang trong giai đoạn chuyển mình từ theme cũ (FunkyStyle nền kem `#F4EFE2`, cam `#D34000`) sang phong cách Dark Tech theo ngôn ngữ thiết kế Figma HH-Maker (`#080C14`, `#141C2E`, `#22314E`, `#0052FF`).

Hiện trạng các thành phần Layout và Navigation (đối chiếu mã nguồn thực tế và đồ thị `graphify-out/graph.json`):
- `resources/Frontend/js/Components/Header.vue`: Vẫn mang nền kem `#F4EFE2`, viền xám `border-gray-300`, nút hamburger màu cam `#D34000`, chữ đỏ `text-red-fks`. Menu điều hướng chưa có thanh chỉ báo active 2px màu xanh `#0052FF`, thiếu cụm nút chuyển ngữ capsule `VIE / ENG` (Figma 2380:1456) và biểu tượng giỏ hàng kèm badge số lượng. Menu di động đang sử dụng accordion sổ dọc chiếm chỗ với tính toán chiều cao bằng JavaScript thủ công (`elementHeight`), dễ lỗi hiển thị và không đạt chuẩn thẩm mỹ di động hiện đại.
- `resources/Frontend/js/Components/Footer.vue`: File chân trang toàn cục cũ vẫn chứa mã nguồn rác chưa được quy hoạch. Trong khi đó, phiên bản chân trang Dark Tech mới chuẩn Figma 2261:5820 lại đang được viết tạm thời tại `resources/Frontend/js/Pages/Home/Components/HomeFooter.vue` và bị import lặp lại tại 4 trang con (`Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, `Product/Show.vue`).
- `resources/Frontend/js/Layouts/Guest.vue`: Đang đóng vai trò layout mặc định trong `resources/Frontend/js/app.js`, phải sử dụng logic điều kiện phức tạp `isMigrated` để ẩn/hiện chân trang cũ.
- `package.json` & `tests/`: Chưa có hạ tầng kiểm thử giao diện tự động đa thiết bị (E2E Testing với Playwright).

Xem thêm động lực chi tiết tại `proposal.md` và các yêu cầu hành vi tại `specs/layout-shell/spec.md`.

## Goals / Non-Goals

**Goals:**
- Dựng `resources/Frontend/js/Layouts/MainLayout.vue` chuẩn mực, tự động bọc `<Header />`, phân vùng nội dung `<main><slot /></main>`, và `<Footer />` cho toàn bộ các trang giao diện công khai.
- Tái cấu trúc `Header.vue` sang phong cách Dark Tech kính mờ (`bg-dark-bg/90 backdrop-blur-md border-b border-dark-border`), tích hợp thanh gạch chân active 2px màu xanh `#0052FF`, cụm chuyển ngữ capsule `VIE / ENG` (Figma 2380:1456), và icon giỏ hàng kèm badge số lượng sản phẩm.
- Xây dựng Mobile Slide-over Drawer hiện đại với `<Teleport to="body">`, hiệu ứng trượt từ cạnh phải sang, lớp phủ làm mờ tối `bg-black/70 backdrop-blur-sm`, khóa cuộn `body` khi mở và hỗ trợ đầy đủ phím tắt `Escape` / click ra ngoài để đóng.
- Hợp nhất `HomeFooter.vue` thành `resources/Frontend/js/Components/Footer.vue` dùng chung toàn hệ thống, tuân thủ đúng lưới 4 cột Figma 2261:5820.
- Dọn dẹp sạch sẽ các lệnh import và thẻ `<HomeFooter />` lặp lại tại 4 trang con (`Home/Index.vue`, `About/Index.vue`, `Product/Index.vue`, `Product/Show.vue`).
- Thiết lập hạ tầng Playwright E2E (`playwright.config.ts`, `tests/e2e/layout-shell.spec.ts`) kiểm thử tự động layout trên Desktop (1440px) và Mobile (375px), bảo đảm không vỡ giao diện và không xuất hiện lỗi tràn cuộn ngang.

**Non-Goals:**
- Không can thiệp hay thay đổi layout quản trị CMS tại `resources/Backend/`.
- Không thay đổi backend controllers, schema cơ sở dữ liệu hay API contracts của Laravel.
- Không refactor nội dung bên trong các trang con ngoài việc dọn dẹp các thẻ/lệnh import `HomeFooter` cục bộ.

## Decisions

### 1. Kiến trúc Layout: Tạo mới `MainLayout.vue` làm Default Layout trong `app.js`
- **Lựa chọn**: Tạo component mới `resources/Frontend/js/Layouts/MainLayout.vue` và cập nhật hàm `resolve` trong `resources/Frontend/js/app.js` để đặt `module.default.layout = module.default.layout || MainLayout`. Giữ `Guest.vue` như một fallback cho các trang auth hoặc layout đặc biệt nếu cần.
- **Lý do**:
  - `Guest.vue` hiện đang gánh logic điều kiện `isMigrated` cùng một số popup cũ. Tách `MainLayout.vue` mang lại cấu trúc layout trong sạch, đúng chuẩn Inertia.js:
    ```vue
    <template>
        <div class="min-h-screen flex flex-col bg-dark-bg text-secondary font-sans selection:bg-primary selection:text-white">
            <Header />
            <main class="flex-grow pt-[var(--header-height-md)] lg:pt-[var(--header-height-lg)] xl:pt-[var(--header-height-xl)]">
                <slot />
            </main>
            <Footer />
        </div>
    </template>
    ```
  - Giúp loại bỏ hoàn toàn mã kiểm tra `isMigrated` và giúp các trang mới tự động thừa hưởng toàn bộ khung shell Dark Tech mà không cần khai báo lặp lại.
- **Phương án thay thế cân nhắc**: Sửa trực tiếp bên trong `Guest.vue`. *Nhược điểm*: Rủi ro cao gây hồi quy (regression) với các trang chưa chuyển đổi hoặc các popup đang phụ thuộc vào `Guest.vue`.

### 2. Header Dark Tech & Active Menu Indicator
- **Lựa chọn**: Cập nhật `Header.vue` sử dụng các class Tailwind chuẩn:
  - Vỏ bọc header: `fixed top-0 inset-x-0 z-[1000] bg-dark-bg/90 backdrop-blur-md border-b border-dark-border transition-colors duration-200`.
  - Thanh active menu: Đối với liên kết đang active (so sánh với `$page.url` hoặc tên route), hiển thị thanh gạch chân tuyệt đối:
    ```html
    <span v-if="isActive(menu)" class="absolute bottom-0 left-0 right-0 h-[2px] bg-primary rounded-full"></span>
    ```
  - Màu chữ: Menu active hiển thị màu trắng sáng `text-white font-semibold`, menu không active hiển thị `text-secondary hover:text-white transition-colors`.
- **Lý do**: Tái hiện chuẩn xác thiết kế Figma 2352:10717 & 2352:10379, mang lại cảm giác công nghệ cao (tech-forward) và phản hồi thị giác tức thì cho người dùng.

### 3. Cụm chuyển đổi ngôn ngữ Capsule `VIE / ENG` (Figma 2380:1456)
- **Lựa chọn**: Xây dựng cụm nút capsule bo tròn `rounded-full border border-dark-border bg-dark-surface/60 p-0.5 inline-flex items-center text-xs font-semibold`:
  - Nút active (tiếng Việt hoặc tiếng Anh): `px-2.5 py-1 rounded-full bg-primary text-white shadow-xs transition-all`.
  - Nút inactive: `px-2.5 py-1 rounded-full text-secondary hover:text-white transition-colors`.
  - Tích hợp điều hướng chuyển locale qua Inertia / i18n hiện có trong dự án.
- **Lý do**: Đây là điểm nhấn thiết kế đặc trưng của thương hiệu HH Maker theo Figma Node 2380:1456, trực quan hơn nhiều so với dropdown chọn ngôn ngữ thông thường.

### 4. Biểu tượng Giỏ hàng & Badge số lượng
- **Lựa chọn**: Sử dụng icon giỏ hàng chuẩn SVG `BaseIcon` / SVG tối ưu, bọc trong nút có nền mờ nhẹ `p-2 rounded-xl text-secondary hover:text-white hover:bg-dark-surface transition-colors relative`:
  - Badge: Thẻ `span` nằm ở góc trên bên phải `absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 bg-primary text-white text-[10px] font-bold rounded-full flex items-center justify-center ring-2 ring-dark-bg`.
  - Lấy số lượng từ prop `$page.props.cart_count` hoặc store giỏ hàng (Pinia).
- **Lý do**: Tạo lối tắt mua sắm thuận tiện, giúp khách hàng nắm bắt nhanh số lượng sản phẩm đang chọn in hoặc đặt hàng.

### 5. Mobile Menu: Slide-over Drawer với `<Teleport to="body">`
- **Lựa chọn**:
  - Khi người dùng click nút Hamburger trên mobile, chuyển trạng thái `isDrawerOpen = true`.
  - Giao diện drawer được teleport ra ngoài thẻ `<body>`:
    ```vue
    <Teleport to="body">
        <Transition name="drawer-fade">
            <div v-if="isDrawerOpen" class="fixed inset-0 z-[1040] bg-black/70 backdrop-blur-sm" @click="closeDrawer" />
        </Transition>
        <Transition name="drawer-slide">
            <aside v-if="isDrawerOpen" class="fixed top-0 right-0 bottom-0 z-[1050] w-[85%] max-w-[360px] bg-dark-bg border-l border-dark-border p-6 flex flex-col justify-between overflow-y-auto shadow-2xl">
                <!-- Header Drawer: Logo & Close X Button -->
                <!-- Nav Links with touch target >= 44px -->
                <!-- Bottom Drawer: Language Switch Capsule & Auth/Cart -->
            </aside>
        </Transition>
    </Teleport>
    ```
  - Đồng thời thêm lớp `overflow-hidden` vào thẻ `<body>` để ngăn hiện tượng cuộn kép (double scrolling).
- **Lý do**: Đáp ứng tiêu chuẩn Web Interface Guidelines và Anti-UI-Slop; mang lại trải nghiệm mượt mà, định hướng ngón cái tự nhiên và không gây nhảy layout trang chính.

### 6. Hợp nhất `HomeFooter.vue` thành `Components/Footer.vue`
- **Lựa chọn**:
  - Ghi đè mã nguồn của `resources/Frontend/js/Components/Footer.vue` bằng nội dung chuẩn Dark Tech từ `HomeFooter.vue` (Figma Node 2261:5820).
  - Tách các thông tin liên hệ tĩnh (hotline, email, xưởng sản xuất) thành props hoặc fallback dữ liệu linh hoạt.
  - Xóa bỏ việc import và thẻ `<HomeFooter />` tại 4 trang: `Pages/Home/Index.vue`, `Pages/About/Index.vue`, `Pages/Product/Index.vue`, `Pages/Product/Show.vue`.
- **Lý do**: Triệt tiêu trùng lặp mã nguồn theo nguyên lý DRY, giúp mọi trang tự động nhận diện footer chuẩn mà không cần lập trình viên phải nhớ import thủ công.

### 7. Thiết lập Playwright E2E QA
- **Lựa chọn**: Cài đặt `@playwright/test`, tạo `playwright.config.ts` với cấu hình:
  - `webServer` hoặc `baseURL` trỏ tới máy chủ phát triển cục bộ (`http://127.0.0.1:8000` hoặc port Vite tương ứng).
  - Hai cấu hình thiết bị chính:
    * `Desktop Chrome`: Viewport 1440x900.
    * `Mobile Chrome`: Viewport 375x667 (iPhone SE / mobile standard).
  - Kịch bản `tests/e2e/layout-shell.spec.ts`:
    * Kiểm tra sự hiện diện của Header, Logo, Navigation trên Desktop 1440px.
    * Kiểm tra active indicator thanh gạch chân xanh 2px.
    * Kiểm tra sự hiện diện của Footer lưới 4 cột ở đáy trang.
    * Kiểm tra trên Mobile 375px: Hamburger mở Mobile Drawer, xác nhận backdrop làm mờ, click đóng drawer.
    * Kiểm tra tự động không có tràn khung ngang: `expect(scrollWidth).toBeLessThanOrEqual(clientWidth)`.
- **Lý do**: Đảm bảo quy tắc Evidence-Based 100% của dự án, phát hiện ngay các lỗi vỡ giao diện trước khi bàn giao cho người dùng.

## Risks / Trade-offs

- **[Risk 1: Giao diện các trang đặc thù bị ảnh hưởng khi đổi default layout]**
  $\rightarrow$ *Mitigation*: `MainLayout` chỉ bọc các trang công khai chưa định nghĩa layout riêng (`module.default.layout = module.default.layout || MainLayout`). Các trang có layout riêng (như auth hoặc admin) sẽ không bị ảnh hưởng.
- **[Risk 2: Xung đột z-index của Mobile Drawer với Modal hệ thống]**
  $\rightarrow$ *Mitigation*: Phân tầng z-index minh bạch:
    * Header: `z-[1000]`
    * Backdrop Drawer: `z-[1040]`
    * Slide-over Drawer: `z-[1050]`
    * Modals / Alerts hệ thống (`BaseModal`): `z-[2000]`
- **[Risk 3: Kích thước bundle tăng khi thêm Playwright]**
  $\rightarrow$ *Mitigation*: `@playwright/test` chỉ được cài đặt vào `devDependencies`, hoàn toàn không ảnh hưởng đến bundle production của frontend.

## Migration Plan

1. **Giai đoạn 1**: Chuẩn hóa `Footer.vue` và xây dựng `MainLayout.vue`.
2. **Giai đoạn 2**: Nâng cấp `Header.vue` với Dark Tech theme, active 2px underline, capsule language switch và mobile slide-over drawer.
3. **Giai đoạn 3**: Cập nhật `app.js` đặt `MainLayout` làm layout mặc định; dọn dẹp các import `<HomeFooter />` tại 4 trang con.
4. **Giai đoạn 4**: Cài đặt Playwright, viết kịch bản `tests/e2e/layout-shell.spec.ts` và chạy kiểm thử trên Desktop & Mobile.
5. **Rollback Strategy**: Nếu xảy ra sự cố không mong muốn, chỉ cần khôi phục lại giá trị `module.default.layout` trong `app.js` về `LayoutGuest` là toàn bộ hệ thống quay về trạng thái cũ ngay lập tức.
