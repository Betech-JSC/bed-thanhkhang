# Tasks: Remove Redundant Login and Mobile Cart

## Tasks

- [x] 1. Scaffolding & OpenSpec spec setup <!-- id: 1-scaffolding -->
  - [x] Tạo proposal.md, design.md, specs/cleanup-login-cart/spec.md và tasks.md <!-- id: 1.1-create-docs -->
  - [x] Kiểm tra openspec validate status <!-- id: 1.2-validate-openspec -->

- [x] 2. Chỉnh sửa Header.vue <!-- id: 2-header-refactor -->
  - [x] Cắt bỏ khối Auth / User Info trong Mobile Drawer <!-- id: 2.1-remove-auth-drawer -->
  - [x] Dọn dẹp computed `cartCount`, `hasUnreadNotification` <!-- id: 2.2-clean-computed -->
  - [x] Dọn dẹp methods `logout`, `openActivityPopup` <!-- id: 2.3-clean-methods -->
  - [x] Đảm bảo spacing khối ngôn ngữ và CTA Liên hệ ngay chuẩn đẹp <!-- id: 2.4-verify-mobile-layout -->

- [x] 3. Chỉnh sửa routes/frontend.php <!-- id: 3-routes-refactor -->
  - [x] Gỡ bỏ route `/dang-nhap` dẫn vào view Inertia Users/Login <!-- id: 3.1-remove-login-inertia -->
  - [x] Thiết lập redirect 301 an toàn `Route::redirect('/dang-nhap', '/', 301)->name('user.login');` <!-- id: 3.2-add-redirect -->

- [x] 4. Build & Test kiểm chứng <!-- id: 4-build-and-test -->
  - [x] Chạy `npm run build` / `yarn frontend:build` xác nhận Vue compilation sạch sẽ <!-- id: 4.1-npm-build -->
  - [x] Chạy `php artisan test` xác nhận 100% test suite pass <!-- id: 4.2-artisan-test -->
  - [x] Kiểm tra redirect HTTP bằng curl hoặc artisan <!-- id: 4.3-verify-redirect -->
