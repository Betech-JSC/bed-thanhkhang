# Proposal: Sửa triệt để lỗi điều hướng localhost:8000 và lỗi CORS trên Production (Fix Ziggy CORS Routing)

## Why

Trên môi trường Production (`https://hhmaker.betech-digital.com`), khi người dùng tương tác với thanh menu Header hoặc các liên kết điều hướng trong ứng dụng Inertia SPA, hệ thống bị điều hướng hoặc gửi request về `http://localhost:8000/...` thay vì duy trì domain hoặc relative path của trang hiện tại. Điều này gây ra lỗi trình duyệt nghiêm trọng:
- **CORS Blocked**: Trình duyệt chặn yêu cầu do vi phạm Same-Origin Policy (`Cross-Origin Request Blocked: The Same Origin Policy disallows reading the remote resource at http://localhost:8000/...`).
- **Trải nghiệm đứt gãy**: Người dùng không thể chuyển trang trên thanh menu (Quy trình `/quy-trinh`, Sản phẩm `/san-pham`, v.v.), hoặc trang web cố gắng kết nối tới `localhost:8000` vốn không tồn tại trên máy client.

### Phân tích nguyên nhân gốc rễ (Root Cause Analysis):
1. **Mặc định absolute URL trong Ziggy helper**: Trong Vue mixin của `resources/Frontend/js/app.js` và `resources/Frontend/js/ssr.js`, phương thức `route(name, params, absolute, config)` không gán giá trị mặc định cho tham số `absolute`. Mặc định của thư viện `ziggy-js` là `absolute = true`, khiến mọi lệnh gọi thông thường như `route('process.index')` luôn tạo ra full URL tuyệt đối (chứa giao thức, domain và port).
2. **Domain gán cứng `localhost` trong Ziggy asset**: File `public/build/ziggy.frontend.js` được biên dịch bởi Laravel Artisan `ziggy:generate` đọc từ cấu hình `.env` cục bộ, dẫn đến `Ziggy.url = "http://localhost:8000"` và mọi route trong `Ziggy.routes` đều mang thuộc tính `"domain": "localhost"`. Khi client tải về, Ziggy ưu tiên trường `domain` của route để ép tạo absolute URL dẫn về `localhost`.
3. **Lỗi chuỗi rỗng của route gốc `'/'`**: Khi thiết lập `absolute = false`, Ziggy loại bỏ leading/trailing slash khiến `route('home', undefined, false)` trả về chuỗi rỗng `""` thay vì `'/'`. Kết quả thẻ `<Link :href="route('home')">` tạo ra `<a href="">`, gây hành vi reload trang hiện tại hoặc lỗi điều hướng.
4. **CI/CD Pipeline chưa cấu hình URL Production khi build**: Trong `.github/workflows/ci-cd.yml` (Step 4), file `.env` được tạo bằng lệnh `cp .env.example .env` nhưng chưa được thiết lập `APP_URL` và `FRONTEND_URL` sang `https://hhmaker.betech-digital.com`. Khi runner chạy lệnh `yarn route` ở Step 5, file `ziggy.frontend.js` được đóng gói với `localhost:8000` rồi tải lên artifact và triển khai thẳng lên máy chủ VPS.

## What Changes

1. **Khởi tạo và làm sạch Ziggy động trên Client (`resources/Frontend/js/app.js`)**:
   - Khi chạy trên trình duyệt (`typeof window !== 'undefined'`), đồng bộ động `Ziggy.url`, `Ziggy.port`, và `Ziggy.location` theo `window.location`.
   - Lặp qua `Ziggy.routes` và xóa triệt để thuộc tính `domain` trên từng route để loại bỏ ràng buộc `localhost`.
   - Cập nhật hàm mixin `route(name, params, absolute = false, config = { ...Ziggy })`:
     + Đặt `absolute = false` làm mặc định để các thẻ điều hướng Inertia SPA luôn dùng relative path (`/quy-trinh`, `/san-pham`).
     + Bổ sung xử lý fallback: nếu kết quả trả về là chuỗi rỗng (`""`), tự động trả về `'/'`.
2. **Đồng bộ hóa hàm `route()` trên môi trường SSR (`resources/Frontend/js/ssr.js`)**:
   - Xóa thuộc tính `domain` trong `Ziggy.routes` nếu tồn tại.
   - Cập nhật hàm `route(name, params, absolute = false, config)` với xử lý fallback chuỗi rỗng trả về `'/'`.
3. **Cập nhật quy trình CI/CD Workflow (`.github/workflows/ci-cd.yml`)**:
   - Tại Step 4 ("Prepare .env & Install Composer Dependencies"), thêm các lệnh `sed` để thiết lập `APP_URL=https://hhmaker.betech-digital.com` và `FRONTEND_URL=https://hhmaker.betech-digital.com`.
   - Đảm bảo khi `yarn route` chạy trên GitHub Actions runner, file `ziggy.frontend.js` mang đúng URL production chính thức.
4. **Kiểm thử độc lập và xác minh**:
   - Chạy `yarn route` tái tạo route cache.
   - Dùng script kiểm tra Node.js xác minh `route('process.index')` -> `/quy-trinh`, `route('products.index')` -> `/san-pham`, `route('home')` -> `'/'`.
   - Chạy `yarn frontend:build` để xác minh bundle Client & SSR biên dịch thành công 100%.

## Capabilities

### New Capabilities
- `frontend-routing`: Cơ chế định tuyến linh hoạt cho Inertia.js SPA, tự động sinh relative path chuẩn SEO, đồng bộ origin động theo môi trường duyệt và loại bỏ hoàn toàn nguy cơ sai lệch domain giữa Client, Server và CI/CD.

### Modified Capabilities
- Không có thay đổi requirement đối với các spec nghiệp vụ khác.

## Impact

- **Mã nguồn bị tác động**:
  - `resources/Frontend/js/app.js`: Khởi tạo Ziggy động và cập nhật `route()` mixin.
  - `resources/Frontend/js/ssr.js`: Cập nhật `route()` mixin cho SSR.
  - `.github/workflows/ci-cd.yml`: Thêm biến môi trường production cho bước build assets.
- **APIs & Database**:
  - Không thay đổi cơ sở dữ liệu hay backend controllers.
- **Breaking Changes**:
  - Hoàn toàn không có breaking change. Nếu component nào cần absolute URL (ví dụ chia sẻ liên kết mạng xã hội), lập trình viên chỉ cần truyền rõ ràng tham số thứ ba: `route('posts.show', post.slug, true)`.
