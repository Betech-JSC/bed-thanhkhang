# Spec: Nhận diện Thương hiệu HH Maker và Chuẩn hóa Trang Giới thiệu

## Requirements

### Requirement: Thay thế Logo Thương hiệu Chính thức
Hệ thống PHẢI sử dụng logo chính thức HH Maker thay cho logo FunkyStyle cũ.
- `public/assets/images/header/logo.png` và `public/assets/images/logo.png` PHẢI là logo HH Maker.
- Thẻ `<img>` logo tại Header và Footer hiển thị rõ nét, tỷ lệ chuẩn, không méo hình.

### Requirement: Xóa bỏ Con trỏ Chuột Cây bút FunkyStyle
Hệ thống PHẢI gỡ bỏ hoàn toàn file styling và cấu hình con trỏ chuột cây bút.
- Không còn `@import 'customize/cursor.scss';` trong `resources/Frontend/scss/app.scss`.
- File `resources/Frontend/scss/customize/cursor.scss` PHẢI bị xóa.
- `resources/Frontend/views/app.blade.php` KHÔNG còn chứa inline style cấu hình `--cursor-url`.
- Các file SVG con trỏ `GS-pen.svg`, `GS-pen-hover.svg`, `GS-pen.min.svg` PHẢI được dọn sạch.

### Requirement: Tái cấu trúc Trang Giới thiệu Chuẩn 1:1 Figma Node 2283:7098
Trang `resources/Frontend/js/Pages/About/Index.vue` PHẢI tuân thủ chính xác 5 section theo Figma Node `2283:7098`:
1. Section 1 (Hero - Node 2283:7100): Banner xưởng chế tác thực tế, badge "Giới thiệu về HH Maker" (#84ADFF), H1 "ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH" (38px Bold #FAFAFA), đoạn giới thiệu và rotating badge tròn "FROM IDEAS TO REALITY -" + icon arrow-up-right.
2. Section 2 (Lý do khởi đầu - Node 2283:7109): Cột trái "Vì sao HH Maker ra đời? / TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH"; Cột phải: 2 đoạn văn truyền cảm hứng.
3. Section 3 (Nhà sáng lập - Node 2283:7116): Cột trái: Khung ảnh chân dung Founder Lâm Hồng Hải kèm nhãn "LÂM HỒNG HẢI - Founder HH Maker"; Cột phải: Badge "Chia sẻ từ Nhà sáng lập HH Maker" (#84ADFF), H2 "Mỗi ý tưởng đều xứng đáng có một hình hài", 3 đoạn tâm thư và chữ ký triết lý.
4. Section 4 (Triết lý chế tác - Node 2283:7129): Tiêu đề "CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN" với 3 thẻ giá trị viền #5D6B98:
   - Thấu hiểu từng ý tưởng (Icon lightbulb)
   - Chăm chút từng chi tiết (Icon scan-line)
   - Giữ trọn dấu ấn riêng (Icon fingerprint)
5. Section 5 (Kết nối dự án - Node 2283:7633): Banner gradient #2970FF -> #00359E "Ý tưởng của bạn, bước tiếp theo của chúng ta.", nút "Nhận tư vấn ngay" dẫn sang `/lien-he`.
- LOẠI BỎ hoàn toàn các components thừa: `AboutTimeline`, `MachineFarmShowcase`, và danh sách 4 nhân sự giả định.

### Requirement: Chuẩn hóa Footer Toàn cục Theo Figma Node 2283:7686
Chân trang `resources/Frontend/js/Components/Footer.vue` PHẢI hiển thị đúng thông tin:
- Tên thương hiệu: CÔNG TY IN ẤN 3D HH MAKER
- Địa chỉ: Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh
- Hotline: 0906636425 (link `tel:0906636425`)
- Email: info@hhmaker.vn và lamhonghai123@gmail.com
