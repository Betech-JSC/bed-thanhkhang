# Tasks

## 1. Hạ tầng Layout & Footer Component (Frontend)

- [x] 1.1 Hợp nhất `HomeFooter.vue` thành `resources/Frontend/js/Components/Footer.vue` toàn cục với lưới 4 cột Dark Tech theo Figma Node 2261:5820 và kiểm tra component render đúng cấu trúc lưới, hỗ trợ hotline, email, xưởng in linh hoạt
- [x] 1.2 Tạo component `resources/Frontend/js/Layouts/MainLayout.vue` bao bọc `<Header />`, `<main><slot /></main>`, và `<Footer />` với cấu trúc Dark Tech (`bg-dark-bg`, padding-top bù đắp fixed header) và kiểm tra file tồn tại, không lỗi cú pháp
- [x] 1.3 Cập nhật `resources/Frontend/js/app.js` thiết lập `MainLayout` làm layout mặc định của toàn bộ ứng dụng và kiểm tra không ảnh hưởng các layout tuỳ chỉnh khác

## 2. Header Dark Tech & Mobile Slide-over Drawer (Frontend)

- [x] 2.1 Tái cấu trúc thanh điều hướng máy tính trong `resources/Frontend/js/Components/Header.vue` sang nền tối kính mờ `bg-dark-bg/90 backdrop-blur-md border-b border-dark-border`, logo HH Maker, và thêm thanh gạch chân active 2px màu xanh `#0052FF` (`h-[2px] bg-primary`) tại menu đang active
- [x] 2.2 Xây dựng cụm nút chuyển đổi ngôn ngữ dạng capsule `VIE / ENG` theo chuẩn Figma Node 2380:1456 tích hợp trong `Header.vue` (và mobile drawer) với hiệu ứng highlight capsule cho ngôn ngữ active và kiểm tra chuyển đổi trạng thái click
- [x] 2.3 Tích hợp biểu tượng Giỏ hàng trên Header với badge hiển thị số lượng mặt hàng (kết nối props hoặc store giỏ hàng) và kiểm tra badge render đúng số lượng hoặc trạng thái rỗng
- [x] 2.4 Xây dựng Mobile Menu Slide-over Drawer sử dụng `<Teleport to="body">` trượt từ cạnh phải sang trái với lớp phủ làm mờ tối `bg-black/70 backdrop-blur-sm`, khóa cuộn `body` khi mở và hỗ trợ đóng bằng nút X, click ngoài hoặc phím `Escape`

## 3. Dọn dẹp trang con & Đồng bộ Build (Integration & Build)

- [x] 3.1 Dọn dẹp các lệnh import và thẻ gọi `<HomeFooter />` lặp lại tại 4 trang: `resources/Frontend/js/Pages/Home/Index.vue`, `resources/Frontend/js/Pages/About/Index.vue`, `resources/Frontend/js/Pages/Product/Index.vue`, và `resources/Frontend/js/Pages/Product/Show.vue`, xác nhận không còn bất kỳ tham chiếu `HomeFooter` nào tại 4 trang này
- [x] 3.2 Chạy lệnh biên dịch `yarn frontend:build` để xác thực toàn bộ mã nguồn Vue/Vite biên dịch thành công 100% không có lỗi cú pháp hay import gãy
- [x] 3.3 Chạy lệnh cập nhật đồ thị AST `graphify . --code-only` để đồng bộ cấu trúc component Layout, Header, Footer vào `graphify-out/graph.json`

## 4. Thiết lập hạ tầng Playwright E2E & Visual QA (Playwright QA)

- [x] 4.1 Cài đặt thư viện `@playwright/test` vào `devDependencies` và tạo file cấu hình `playwright.config.ts` hỗ trợ 2 profile `Desktop Chrome` (1440x900) và `Mobile Chrome` (375x667)
- [x] 4.2 Viết kịch bản kiểm thử E2E tại `tests/e2e/layout-shell.spec.ts` kiểm tra hiển thị Header, active underline, capsule ngôn ngữ, Footer 4 cột trên Desktop (1440px) và kiểm tra thao tác mở/đóng Mobile Drawer, khóa cuộn, cùng kiểm tra không có lỗi tràn khung ngang (`scrollWidth <= clientWidth`)
- [x] 4.3 Thực thi kiểm thử tự động `npx playwright test tests/e2e/layout-shell.spec.ts` (hoặc lệnh test tương ứng) và xác nhận 100% test cases pass với báo cáo kết quả chi tiết
