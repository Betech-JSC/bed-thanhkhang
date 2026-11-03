# Tasks: Dọn Dẹp Banner CTA Thừa Trang Quy Trình Khớp 100% Figma

## 1. Khảo Sát & Xác Thực Thiết Kế Figma

- [x] 1.1 Khảo sát Figma qua MCP `get_figma_data` (file `otk6uzZmitaXPdZZZiRG1K`, nodes `2283:6841`, `2283:6851`, `2283:6882`, `2334:8455`, `2283:7686`) xác nhận trang Quy trình chỉ có 4 section chính và nối thẳng vào Footer.
- [x] 1.2 Rà soát mã nguồn trang Quy trình tại `resources/Frontend/js/Pages/Process/Index.vue`.

## 2. Gỡ Bỏ Banner CTA Khỏi Trang Quy Trình

- [x] 2.1 Gỡ bỏ lệnh `import ProcessCtaBanner from './Components/ProcessCtaBanner.vue'` trong `resources/Frontend/js/Pages/Process/Index.vue`.
- [x] 2.2 Gỡ bỏ thẻ `<ProcessCtaBanner />` và comment Section 5 trong template của `Index.vue`.
- [x] 2.3 Bảo toàn nguyên vẹn file vật lý `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue` trên ổ đĩa tuân thủ Strict Safe Guard.

## 3. Kiểm Chứng Bằng Chứng (Evidence-Based QA)

- [x] 3.1 Chạy lệnh `yarn frontend:build` xác nhận client và SSR bundle biên dịch không lỗi (exit code 0).
- [x] 3.2 Viết và chạy test Playwright E2E chụp ảnh màn hình Desktop (1440px) và Mobile (390px) trang `/quy-trinh` chứng minh không còn banner CTA thừa.
- [x] 3.3 Chạy `openspec validate cleanup-process-page-cta-banner` đảm bảo đặc tả hợp lệ 100%.
