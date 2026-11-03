# Tasks: Chuẩn Hóa Section "Quy Trình 5 Bước" Khớp 100% Theo Figma

## 1. Chuẩn Bị Tài Nguyên & Vector

- [x] 1.1 Kiểm tra và chuẩn bị ảnh đồ họa 3D lượn sóng `process-left-graphic.png` và vector `arrow-up-right.svg` từ Figma Node `2261:5724` và `2261:5734`.
- [x] 1.2 Đảm bảo đường dẫn tài nguyên tĩnh hoạt động tốt trên cả môi trường dev và production.

## 2. Tinh Chỉnh & Chuẩn Hóa ProcessSection.vue

- [x] 2.1 Cập nhật `ProcessSection.vue` sang bố cục Full-width, chiều cao `min-h-[720px] lg:h-[892px]` với nền `linear-gradient(0deg, #111322 20%, #2970FF 94%)`.
- [x] 2.2 Xây dựng cột trái với đồ họa sóng 3D hòa trộn ánh sáng xanh, số khổng lồ "05" (120px) và tiêu đề in hoa "BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER" (38px bold) căn lề chuẩn 144px trên Desktop.
- [x] 2.3 Xây dựng đường phân cách dọc 1px và cột phải với đoạn mô tả (16px) cùng 5 bước quy trình tối giản dạng hàng phẳng có đường kẻ ngang và icon `arrow-up-right` 40x40px bên trái tiêu đề bước.
- [x] 2.4 Bổ sung hiệu ứng tương tác hover mượt mà và đảm bảo responsive hoàn hảo trên Mobile và Tablet.

## 3. Kiểm Tra Tích Hợp & Build Kiểm Chứng

- [x] 3.1 Kiểm tra việc nhúng `ProcessSection` tại `resources/Frontend/js/Pages/Home/Index.vue` để đảm bảo truyền props và cấu trúc đồng bộ.
- [x] 3.2 Chạy `yarn frontend:build` để biên dịch cả client bundle và SSR bundle thành công với exit code 0.
- [x] 3.3 Viết và chạy test Playwright E2E chụp ảnh màn hình Desktop & Mobile section này, đối chiếu trực quan với thiết kế Figma gốc.
