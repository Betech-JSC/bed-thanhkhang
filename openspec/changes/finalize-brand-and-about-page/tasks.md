# Tasks

## 1. Khởi tạo Change OpenSpec
- [x] 1.1 Khởi tạo change `finalize-brand-and-about-page` với đầy đủ `proposal.md`, `design.md`, `specs/brand-and-about/spec.md` và `tasks.md`

## 2. Thay thế Logo HH Maker Chuẩn Figma
- [x] 2.1 Copy đè `logo-hhmaker.png` vào `public/assets/images/header/logo.png` và `public/assets/images/logo.png`
- [x] 2.2 Kiểm tra và căn chỉnh thẻ `<img>` logo trong `Header.vue` và `Footer.vue` sắc nét, không méo tỷ lệ

## 3. Xóa Triệt để Con trỏ Chuột Cây bút FunkyStyle
- [x] 3.1 Gỡ bỏ `@import 'customize/cursor.scss';` trong `resources/Frontend/scss/app.scss`
- [x] 3.2 Xóa file `resources/Frontend/scss/customize/cursor.scss`
- [x] 3.3 Xóa khối style `--cursor-url` trong `resources/Frontend/views/app.blade.php`
- [x] 3.4 Xóa các file SVG `GS-pen.svg`, `GS-pen-hover.svg`, `GS-pen.min.svg` trong `public/assets/svg/`

## 4. Dựng Lại Trang Giới thiệu (`About/Index.vue`) Chuẩn 1:1 Figma Node 2283:7098
- [x] 4.1 Triển khai Section 1 Hero (Node 2283:7100): Banner, Badge, H1 "ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH", rotating circular stamp
- [x] 4.2 Triển khai Section 2 Lý do khởi đầu (Node 2283:7109): 2 cột đối xứng Chủ đề và Câu chuyện sứ mệnh
- [x] 4.3 Triển khai Section 3 Nhà sáng lập (Node 2283:7116): Frame ảnh Founder Lâm Hồng Hải và 3 đoạn tâm thư chia sẻ kèm chữ ký
- [x] 4.4 Triển khai Section 4 Triết lý chế tác (Node 2283:7129): Tiêu đề và 3 card giá trị viền #5D6B98 (Bóng đèn, Scan line, Vân tay)
- [x] 4.5 Triển khai Section 5 Kết nối dự án (Node 2283:7633): Banner gradient với nút "Nhận tư vấn ngay" dẫn sang `/lien-he`
- [x] 4.6 Gỡ bỏ hoàn toàn `AboutTimeline`, `MachineFarmShowcase`, `TeamMemberCard` và dữ liệu 4 nhân viên giả

## 5. Chuẩn Hóa Footer (`Footer.vue`) Theo Figma Node 2283:7686
- [x] 5.1 Cập nhật tên thương hiệu "CÔNG TY IN ẤN 3D HH MAKER"
- [x] 5.2 Cập nhật địa chỉ "Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh"
- [x] 5.3 Cập nhật hotline "0906636425" và 2 email ("info@hhmaker.vn", "lamhonghai123@gmail.com")

## 6. Biên Dịch Build & Playwright Test QA
- [x] 6.1 Chạy `yarn frontend:build` đảm bảo 100% compile thành công (Client & SSR exit code 0)
- [x] 6.2 Chạy Playwright test chụp ảnh màn hình Desktop & Mobile trang Giới thiệu, Header và Footer (12/12 passed)
- [x] 6.3 Hoàn thiện cập nhật `tasks.md` và gửi báo cáo chi tiết cho Main Agent
