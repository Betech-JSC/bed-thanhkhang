# Design: Thiết kế kỹ thuật sửa lỗi Ziggy CORS Routing & Relative URLs

## 1. Overview & Context

Ứng dụng HH Maker sử dụng kiến trúc Single Page Application (SPA) xây dựng trên nền tảng **Laravel 9/10 + Inertia.js Vue 3 + Ziggy.js**.
Trên môi trường Production VPS (`https://hhmaker.betech-digital.com`), người dùng gặp lỗi CORS nghiêm trọng khi click vào các liên kết menu Header (chẳng hạn mục Quy trình, Sản phẩm) do Ziggy sinh ra liên kết tuyệt đối trỏ về `http://localhost:8000/...`.
Mục tiêu thiết kế:
- Triệt tiêu hoàn toàn sự phụ thuộc vào cấu hình domain tĩnh trong `ziggy.frontend.js`.
- Đưa mặc định của hàm `route()` về relative path (`absolute = false`).
- Khắc phục lỗi edge-case khi Ziggy sinh relative path cho trang chủ trả về chuỗi rỗng `""`.
- Cấu hình chuẩn xác `APP_URL` và `FRONTEND_URL` trong quy trình CI/CD runner.

## 2. Technical Architecture & Component Changes

```
                     ┌───────────────────────────────────────────────┐
                     │           Browser / Client Context            │
                     └──────────────────────┬────────────────────────┘
                                            │
                                            ▼
                             window.location.origin detected
                             (https://hhmaker.betech-digital.com)
                                            │
                                            ▼
                     ┌───────────────────────────────────────────────┐
                     │ Ziggy Sanitization & Dynamic Sync             │
                     │ - Ziggy.url = window.location.origin          │
                     │ - Ziggy.location = window.location            │
                     │ - delete Ziggy.routes[*].domain               │
                     └──────────────────────┬────────────────────────┘
                                            │
                                            ▼
                     ┌───────────────────────────────────────────────┐
                     │ Vue Mixin route(name, params, absolute=false) │
                     │ - routeLocaleName(name) resolution            │
                     │ - Ziggy relative URL generation               │
                     │ - Root fallback: if url === '' return '/'     │
                     └──────────────────────┬────────────────────────┘
                                            │
                    ┌───────────────────────┴───────────────────────┐
                    ▼                                               ▼
         <Link :href="'/quy-trinh'">                     <Link :href="'/'">
         No localhost:8000, No CORS!                     Valid root link
```

### 2.1. Client-Side: `resources/Frontend/js/app.js`

1. **Khởi tạo làm sạch động (Sanitization Block)**:
   Thực thi ngay sau khi import `Ziggy`:
   ```javascript
   if (typeof window !== 'undefined') {
       Ziggy.url = window.location.origin
       Ziggy.port = window.location.port ? Number(window.location.port) : null
       Ziggy.location = window.location
       if (Ziggy.routes) {
           for (const name in Ziggy.routes) {
               delete Ziggy.routes[name].domain
           }
       }
   }
   ```
2. **Cập nhật Vue mixin `route`**:
   ```javascript
   route(
       name,
       params,
       absolute = false,
       config = {
           ...Ziggy,
           location: typeof window !== 'undefined' ? window.location : new URL(Ziggy.url),
       }
   ) {
       try {
           const url = route(this.routeLocaleName(name), params, absolute, config)
           return url === '' ? '/' : url
       } catch (e) {
           console.error('Ziggy route error for: ' + name, e)
           return '#'
       }
   },
   ```

### 2.2. Server-Side Rendering: `resources/Frontend/js/ssr.js`

1. **Khởi tạo làm sạch trên Node.js SSR**:
   ```javascript
   if (Ziggy.routes) {
       for (const name in Ziggy.routes) {
           delete Ziggy.routes[name].domain
       }
   }
   ```
2. **Cập nhật SSR mixin `route`**:
   ```javascript
   route(
       name,
       params,
       absolute = false,
       config = {
           ...Ziggy,
           location: new URL(Ziggy.url),
       }
   ) {
       try {
           const url = route(this.routeLocaleName(name), params, absolute, config)
           return url === '' ? '/' : url
       } catch (e) {
           console.error('Ziggy route error for: ' + name, e)
           return '#'
       }
   },
   ```

### 2.3. CI/CD GitHub Actions: `.github/workflows/ci-cd.yml`

Tại bước **Step 4: Prepare .env & Install Composer Dependencies**, bổ sung cấu hình:
```bash
sed -i 's|^APP_URL=.*|APP_URL=https://hhmaker.betech-digital.com|' .env || echo "APP_URL=https://hhmaker.betech-digital.com" >> .env
sed -i 's|^FRONTEND_URL=.*|FRONTEND_URL=https://hhmaker.betech-digital.com|' .env || echo "FRONTEND_URL=https://hhmaker.betech-digital.com" >> .env
```
Nhờ đó, khi Step 5 thực hiện `yarn route` (`php artisan ziggy:generate public/build/ziggy.frontend.js --group=frontend`), Ziggy sẽ ghi nhận `https://hhmaker.betech-digital.com` là URL gốc dự phòng.

## 3. Verification Plan

1. **Unit/CLI Verification**:
   - Chạy `yarn route`
   - Dùng script Node.js kiểm thử `route('process.index')`, `route('products.index')`, `route('home')`:
     + `route('process.index')` -> trả về `/quy-trinh` (hoặc `/vi/quy-trinh`)
     + `route('products.index')` -> trả về `/san-pham`
     + `route('home')` -> trả về `/`
2. **Bundle Build Verification**:
   - Chạy `yarn frontend:build` để biên dịch cả client bundle và SSR bundle (`bootstrap/ssr/ssr.mjs`). Đảm bảo exit code 0.
