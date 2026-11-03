# Proposal: Hoàn thiện Dứt điểm Nhận diện Thương hiệu HH Maker và Chuẩn hóa Trang Giới thiệu (Figma Node 2283:7098)

## Why

Sau khi hoàn tất việc dọn dẹp các view và route di sản từ FunkyStyle, giao diện HH Maker vẫn còn 4 điểm cần xử lý dứt điểm theo thiết kế Figma Canvas "A":
1. **Logo thương hiệu**: Hệ thống vẫn dùng logo quả khổ qua / FunkyStyle cũ tại header và footer thay vì logo HH Maker chính thức đã được thiết kế tại Node 2352:10264.
2. **Con trỏ chuột tùy biến**: Mã nguồn vẫn cài đặt con trỏ chuột cây bút xanh `GS-pen.svg` từ FunkyStyle trong `cursor.scss`, `app.scss`, `app.blade.php`, gây xung đột thẩm mỹ nghiêm trọng với phong cách Dark Tech / Cyber cao cấp của HH Maker.
3. **Trang Giới thiệu (`About/Index.vue`)**: Hiện tại đang chứa các khối không có trong Figma (Timeline hành trình, Machine Farm Showcase, danh sách 4 nhân sự giả định) và chưa khớp 100% bố cục 5 section chuẩn theo Figma Node `2283:7098`.
4. **Chân trang (`Footer.vue`)**: Thông tin liên hệ và nhận diện thương hiệu chưa được đồng bộ chuẩn với Figma Node `2283:7686` (CÔNG TY IN ẤN 3D HH MAKER, địa chỉ tại Số 65 đường số 9 Hương Lộ 5 An Lạc HCM, Hotline 0906636425, email info@hhmaker.vn và lamhonghai123@gmail.com).

## What Changes

1. **Thay thế Logo HH Maker chính thức**:
   - Ghi đè file logo HH Maker từ Figma (`logo-hhmaker.png`) vào:
     * `public/assets/images/header/logo.png`
     * `public/assets/images/logo.png`
   - Đảm bảo thẻ `<img>` logo tại `Header.vue` (cả desktop và mobile drawer) và `Footer.vue` hiển thị sắc nét, `h-8 lg:h-9` và `h-10`, không bị méo tỷ lệ.

2. **Xóa triệt để con trỏ chuột FunkyStyle**:
   - Xóa dòng `@import 'customize/cursor.scss';` trong `resources/Frontend/scss/app.scss`.
   - Xóa file `resources/Frontend/scss/customize/cursor.scss`.
   - Xóa khối style `--cursor-url` và `--cursor-hover-url` trong `resources/Frontend/views/app.blade.php`.
   - Xóa các file SVG con trỏ không dùng: `GS-pen.svg`, `GS-pen-hover.svg`, `GS-pen.min.svg` trong `public/assets/svg/`.

3. **Dựng lại 100% trang Giới thiệu (`About/Index.vue`) chuẩn 1:1 Figma Node `2283:7098`**:
   - Section 1 (Hero - Node 2283:7100): Banner xưởng chế tác thực tế, badge "Giới thiệu về HH Maker" (#84ADFF), H1 "ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH" (38px Bold #FAFAFA), đoạn mô tả và rotating badge tròn "FROM IDEAS TO REALITY -" + icon arrow-up-right.
   - Section 2 (Lý do khởi đầu - Node 2283:7109): 2 cột đối xứng: Cột trái "Vì sao HH Maker ra đời? / TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH"; Cột phải: 2 đoạn văn truyền cảm hứng về sứ mệnh rút ngắn khoảng cách giữa ý tưởng và sản phẩm vật lý.
   - Section 3 (Nhà sáng lập - Node 2283:7116):
     * Cột trái: Khung chân dung Founder Lâm Hồng Hải với gradient frame và nhãn "LÂM HỒNG HẢI - Founder HH Maker".
     * Cột phải: Badge "Chia sẻ từ Nhà sáng lập HH Maker" (#84ADFF), H2 "Mỗi ý tưởng đều xứng đáng có một hình hài", 3 đoạn tâm thư truyền cảm hứng và chữ ký triết lý "Trân trọng từng ý tưởng - Tận tâm với từng thành phẩm."
   - Section 4 (Triết lý chế tác - Node 2283:7129): Tiêu đề "CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN" với 3 thẻ giá trị viền #5D6B98:
     * 1) Thấu hiểu từng ý tưởng (Icon `lightbulb.svg`)
     * 2) Chăm chút từng chi tiết (Icon `scan-line.svg`)
     * 3) Giữ trọn dấu ấn riêng (Icon `fingerprint.svg`)
   - Section 5 (Kết nối dự án - Node 2283:7633): Banner gradient #2970FF -> #00359E "Ý tưởng của bạn, bước tiếp theo của chúng ta.", nút "Nhận tư vấn ngay" dẫn sang `route('contact')` hoặc `/lien-he`.
   - Gỡ bỏ hoàn toàn các component thừa: `AboutTimeline`, `MachineFarmShowcase`, và danh sách nhân sự giả.

4. **Chuẩn hóa thông tin Footer (`Footer.vue`) theo Figma Node `2283:7686`**:
   - Tên thương hiệu: CÔNG TY IN ẤN 3D HH MAKER
   - Giới thiệu: "Hiện thực hóa mọi ý tưởng với công nghệ in 3D hiện đại. Rút ngắn khoảng cách giữa thiết kế số và sản phẩm vật lý chuẩn xác."
   - Địa chỉ xưởng: "Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh"
   - Hotline: "0906636425" (link `tel:0906636425`)
   - Email: "info@hhmaker.vn" & "lamhonghai123@gmail.com"

5. **Biên dịch và Xác thực**:
   - Build Vite: `yarn frontend:build` thành công 100%.
   - Chạy Playwright test chụp ảnh màn hình nghiệm thu `/gioi-thieu`, Header và Footer trên Desktop & Mobile.

## Capabilities

### New / Modified Capabilities
- `brand-identity`: Nhận diện thương hiệu HH Maker chính thức (logo, bỏ con trỏ bút, chuẩn hóa Footer).
- `about-page`: Trang Giới thiệu chuẩn 1:1 Figma 5 sections.

## Impact

- Logo và Footer hiển thị đồng bộ toàn trang.
- Con trỏ chuột trở về hệ thống con trỏ mặc định chuẩn UX, không còn bút xanh FunkyStyle.
- Trang Giới thiệu tinh gọn, đúng thiết kế Figma, loại bỏ code thừa và dữ liệu giả.
