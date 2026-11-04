# Tasks: Sửa triệt để lỗi điều hướng localhost:8000 và CORS trên Production

## 1. OpenSpec Documentation

- [x] 1.1 Khởi tạo change `fix-ziggy-cors-routing` và tạo đầy đủ các tài liệu `proposal.md`, `specs/frontend-routing/spec.md`, `design.md`, `tasks.md`.

## 2. Frontend Client Routing Implementation

- [x] 2.1 Cập nhật `resources/Frontend/js/app.js`: Thêm khối khởi tạo đồng bộ `Ziggy.url`, `Ziggy.port`, `Ziggy.location` theo `window.location` và xóa sạch `domain` trên các route trong `Ziggy.routes`.
- [x] 2.2 Cập nhật phương thức `route()` trong mixin của `resources/Frontend/js/app.js`: Đặt `absolute = false` làm giá trị mặc định, gọi `route(this.routeLocaleName(name), params, absolute, config)`, và xử lý fallback chuỗi rỗng trả về `'/'`.

## 3. Frontend SSR Routing Implementation

- [x] 3.1 Cập nhật `resources/Frontend/js/ssr.js`: Xóa thuộc tính `domain` trên các route trong `Ziggy.routes` trên môi trường SSR.
- [x] 3.2 Cập nhật phương thức `route()` trong mixin của `resources/Frontend/js/ssr.js`: Đặt `absolute = false` làm giá trị mặc định, gọi `route(this.routeLocaleName(name), params, absolute, config)`, và xử lý fallback chuỗi rỗng trả về `'/'`.

## 4. CI/CD Pipeline Configuration

- [x] 4.1 Cập nhật `.github/workflows/ci-cd.yml` tại Step 4 ("Prepare .env & Install Composer Dependencies"): Thêm lệnh thiết lập `APP_URL=https://hhmaker.betech-digital.com` và `FRONTEND_URL=https://hhmaker.betech-digital.com`.

## 5. Verification & Testing

- [x] 5.1 Chạy `yarn route` để tái tạo cache routes và Vue i18n messages.
- [x] 5.2 Viết script kiểm tra độc lập và xác minh kết quả đầu ra:
  - `route('process.index')` trả về relative path `/quy-trinh` (hoặc `/vi/quy-trinh`)
  - `route('products.index')` trả về `/san-pham`
  - `route('home')` trả về `/`
- [x] 5.3 Chạy `yarn frontend:build` để biên dịch toàn bộ Client & SSR bundle, đảm bảo không có lỗi (exit code 0).
