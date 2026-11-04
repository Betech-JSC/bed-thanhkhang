# Tasks: Đồng bộ Cài đặt chung CMS & Thông tin liên hệ Xưởng in 3D

- [x] 1. Khởi tạo tài liệu OpenSpec change `sync-general-settings-and-contact-info` <!-- id: 1-init-openspec -->
- [x] 2. Bổ sung các trường Zalo, Giờ làm việc, Mạng xã hội vào CMS General Settings (`General.vue`) <!-- id: 2-cms-general-fields -->
- [x] 3. Khai báo quy tắc lưu trữ trường mới trong model `Setting.php` <!-- id: 3-setting-model-rules -->
- [x] 4. Ẩn tab "points" khỏi danh sách tab cài đặt trong `helpers.php` (`setting_bar()`) <!-- id: 4-hide-points-tab -->
- [x] 5. Đồng bộ thông tin và giữ nguyên 100% email cá nhân trong `Footer.vue` <!-- id: 5-sync-footer -->
- [x] 6. Kết nối các thẻ liên hệ nhanh trong `Contact.vue` với `$page.props.global` <!-- id: 6-sync-contact-page -->
- [x] 7. Kết nối icon mạng xã hội trong `ContactSection.vue` với `$page.props.global` <!-- id: 7-sync-contact-section -->
- [x] 8. Xác minh biên dịch Backend (`yarn backend:build`) <!-- id: 8-verify-backend-build -->
- [x] 9. Xác minh biên dịch Frontend (`yarn frontend:build`) <!-- id: 9-verify-frontend-build -->
- [x] 10. Chạy kiểm thử tự động `php artisan test tests/Feature/ContactFormTest.php` <!-- id: 10-run-phpunit-tests -->
- [x] 11. Cập nhật 100% `tasks.md` sang trạng thái hoàn tất `[x]` <!-- id: 11-update-tasks -->
