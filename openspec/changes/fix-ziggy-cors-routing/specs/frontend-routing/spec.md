# Spec Delta: Chuẩn hóa định tuyến Frontend Inertia & Ziggy (Frontend Routing)

## Purpose

Quy định đặc tả hành vi của hàm helper `route()` trong ứng dụng Inertia.js (cả trên Client và Server-Side Rendering), đảm bảo luôn sinh relative URL mặc định, đồng bộ domain động theo ngữ cảnh thực thi (`window.location.origin`), và loại bỏ hoàn toàn các lỗi hardcoded domain `localhost:8000` dẫn đến lỗi CORS trên môi trường Production.

## ADDED Requirements

### Requirement: Relative Path Default for Frontend Routing
Hệ thống SHALL tạo đường dẫn tương đối (relative path bắt đầu bằng `/`) làm mặc định khi hàm `route()` được gọi trong Vue components hoặc mixin mà không truyền tham số `absolute`.

#### Scenario: Component generates route link without specifying absolute parameter
- **WHEN** một component gọi `route('process.index')` hoặc `route('products.index')`
- **THEN** hàm `route()` trả về đường dẫn tương đối (ví dụ: `/quy-trinh`, `/san-pham` hoặc `/vi/quy-trinh`, `/vi/san-pham`) mà không chứa scheme, domain hay port (không có `http://...` hay `localhost:8000`).

### Requirement: Dynamic Ziggy Origin & Domain Sanitization
Hệ thống SHALL tự động đồng bộ hóa cấu hình Ziggy trên môi trường trình duyệt theo `window.location` và xóa bỏ mọi ràng buộc `domain` cứng trên từng route trong `Ziggy.routes`.

#### Scenario: Client initializes application in browser
- **WHEN** ứng dụng Vue/Inertia được tải trên trình duyệt ở bất kỳ tên miền nào (`https://hhmaker.betech-digital.com` hoặc môi trường dev)
- **THEN** cấu hình Ziggy tự động nhận `Ziggy.url = window.location.origin`, `Ziggy.location = window.location`, và không còn thuộc tính `domain` cứng nào sót lại trong `Ziggy.routes`, ngăn ngừa Ziggy ép tạo absolute URL về `localhost`.

### Requirement: Root Route Fallback Normalization
Hệ thống SHALL chuẩn hóa giá trị trả về của route gốc `'/'` (như `route('home')` hoặc `route('vi.home')`) khi tạo relative path, đảm bảo luôn trả về `'/'` thay vì chuỗi rỗng `""`.

#### Scenario: Component or template generates link to home page
- **WHEN** một component gọi `route('home')` hoặc `route('vi.home')` với `absolute = false`
- **THEN** hàm `route()` trả về chính xác chuỗi `'/'` để liên kết `<Link :href="route('home')">` hoạt động liền mạch, không sinh ra `<a href="">`.

### Requirement: Production URL in CI/CD Built Assets
Hệ thống CI/CD Pipeline SHALL thiết lập `APP_URL` và `FRONTEND_URL` trỏ về domain chính thức của hệ thống (`https://hhmaker.betech-digital.com`) trước khi thực thi lệnh sinh Ziggy route (`yarn route`) và đóng gói tài nguyên.

#### Scenario: GitHub Actions CI runner executes build step
- **WHEN** quy trình CI/CD chuẩn bị file `.env` và chạy `yarn route`
- **THEN** file cấu hình `public/build/ziggy.frontend.js` được tạo ra với URL production thay vì `http://localhost:8000`, đảm bảo tính nhất quán kể cả khi có mã nguồn yêu cầu absolute URL trên máy chủ.
