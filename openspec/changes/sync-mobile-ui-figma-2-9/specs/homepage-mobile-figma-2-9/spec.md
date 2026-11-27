# Spec Delta: Giao diện Mobile Trang chủ & Chân trang theo Figma `2:9`

## Purpose

Đặc tả hành vi hiển thị và bố cục giao diện Mobile (`390px`) của các phân hệ Trang chủ (`HeroSection`, `ProductsSection`, `BrandStorySection`, `ServicesSection`, `ProcessSection`, `BlogSection`, `ContactSection`) và `Footer` khớp chuẩn thiết kế Figma `2:9` (`1314:51987`) mà không làm thay đổi bố cục Desktop `1440px` đã nghiệm thu.

## ADDED Requirements

### Requirement: Hero Banner Mobile Scrim Ngang và Phân cấp Chữ Chuẩn Figma 2:9
Hero Banner trên Trang chủ (`HeroSection`) SHALL hiển thị trên thiết bị di động (`< 768px`) khớp thiết kế Figma `1314:52003`–`1314:52005`:
- Lớp phủ tối (scrim) trên Mobile MUST chuyển sang gradient ngang từ trái sang phải (`linear-gradient(90deg, #01101E 0%, rgba(1, 16, 30, 0) 100%)`), trong khi Desktop (`>= 768px`) giữ nguyên scrim hiện hữu.
- Nhãn dẫn (eyebrow) trên Mobile MUST có kích thước chữ `18px` (`text-[18px] md:text-[24px] lg:text-[28px]`).
- Văn bản mô tả trên Mobile MUST hiển thị trọn vẹn nội dung, không bị cắt ngắn bằng `line-clamp-4`.
- Nhãn chữ trên các nút CTA của Hero Banner MUST sử dụng cỡ chữ `18px` trên cả Mobile và Desktop.

#### Scenario: Kiểm tra Hero Banner Trang chủ trên Mobile 390×844
- **WHEN** người dùng truy cập Trang chủ trên viewport `390×844`
- **THEN** scrim phủ trên Hero Banner chạy gradient ngang từ `#01101E` bên trái mờ dần sang phải, eyebrow có `font-size` `18px`, mô tả hiển thị đầy đủ không bị dấu ba chấm cắt dòng (`line-clamp`), và nút CTA có `font-size` `18px`.

---

### Requirement: Lưới Danh mục Sản phẩm Mobile 2 Cột Không Mũi tên và Ô Khám phá Nổi bật
Phân đoạn Danh mục Sản phẩm (`ProductsSection`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52024`:
- Khung ảnh thumbnail của các thẻ danh mục 1–5 MUST khóa tỷ lệ `aspect-[173/130]` trên Mobile (`aspect-[173/128]` cho thẻ thứ 6 "Khám phá toàn bộ sản phẩm") và chân thẻ giữ chiều cao tối thiểu `min-h-[64px]` (`min-h-[66px]` cho thẻ thứ 6), còn trên Desktop (`>= 1024px`) giữ nguyên `lg:h-[308px]` và `lg:h-[64px]`.
- Hộp icon mũi tên `>` bên phải tiêu đề trong chân thẻ danh mục MUST bị ẩn trên Mobile (`< 1024px`) và chỉ hiển thị từ Desktop (`>= 1024px`).
- Thẻ thứ 6 ("Khám phá toàn bộ sản phẩm") trên Mobile MUST mặc định sử dụng nền xanh thương hiệu `#005EA4` và chữ trắng `#FAFAFA`, trong khi trên Desktop (`>= 1024px`) giữ nguyên trạng thái mặc định `#C9E0FF` (chữ `#364152`) chuyển sang `#005EA4` (chữ `#FAFAFA`) khi hover/active.

#### Scenario: Hiển thị 6 ô danh mục sản phẩm trên Mobile 390×844 và Desktop 1440×900
- **WHEN** người dùng xem phân đoạn Danh mục Sản phẩm trên Mobile (`390×844`)
- **THEN** các ô 1–5 không hiển thị hộp icon mũi tên `>` trong chân thẻ, thumbnail có tỷ lệ `173/130` (`173/128` cho ô 6), và ô thứ 6 hiển thị nền mặc định `#005EA4` với chữ `#FAFAFA` rõ nét.
- **WHEN** người dùng xem lại phân đoạn này trên Desktop (`1440×900`)
- **THEN** hộp icon mũi tên `>` hiển thị đầy đủ trên cả 6 ô và ô thứ 6 giữ trạng thái mặc định `#C9E0FF` chuyển `#005EA4` khi hover.

---

### Requirement: Thứ tự Ảnh Gallery & Kích thước Chữ Câu chuyện Thương hiệu Mobile
Phân đoạn Câu chuyện Thương hiệu (`BrandStorySection`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52038`:
- Cụm tiêu đề section có ô vuông điểm nhấn căn thẳng hàng đầu trên (`items-start`) khi tiêu đề có nhiều dòng.
- Thứ tự hiển thị thư viện 3 ảnh trên Mobile (`< 1024px`) MUST đặt ảnh rộng toàn chiều ngang (`wideImage`, tỷ lệ `aspect-[358/210]`) ở phía **TRÊN** hàng lưới 2 cột của 2 ảnh vuông/dọc (`topImages`, tỷ lệ `aspect-[173/148]`). Bố cục 2 hàng trên Desktop (`>= 1024px`) giữ nguyên không đổi.
- Đoạn văn tự sự và cam kết trên Mobile MUST sử dụng cỡ chữ `16px` (`leading-[1.6]`), và nút CTA `"Xem thêm về chúng tôi"` MUST sử dụng cỡ chữ `18px`.

#### Scenario: Đối chiếu vị trí ảnh rộng và cỡ chữ BrandStory trên Mobile 390×844
- **WHEN** người dùng cuộn đến phân đoạn Câu chuyện Thương hiệu trên viewport `390×844`
- **THEN** ảnh rộng `aspect-[358/210]` nằm ngay phía trên 2 ảnh cột đôi `aspect-[173/148]` (tọa độ `y` của ảnh rộng nhỏ hơn tọa độ `y` của 2 ảnh cột đôi), văn bản nội dung có `font-size` `16px`, và nút CTA có `font-size` `18px`.

---

### Requirement: Timeline Metaball Sóng Dọc 5 Nút Không Đóng Khung Hộp trên Mobile ServicesSection
Phân đoạn Lý do Khách hàng Lựa chọn (`ServicesSection`) trên Mobile (`< 1024px`) khi số lượng mục `<= 5` SHALL khớp thiết kế Figma `1314:52050`:
- Cụm vuông xanh đầu tiêu đề section MUST căn `items-start` theo dòng đầu của tiêu đề.
- Khung banner nền tối (`#00284B` kèm ảnh nền và lớp phủ tối) đạt chiều cao tối thiểu `611px` trên Mobile (`min-h-[611px] lg:h-[578px]`).
- Trên Mobile (`< 1024px`), thay vì hiển thị 5 thẻ đóng khung hộp chữ nhật có viền, hệ thống MUST hiển thị sơ đồ timeline metaball sóng dọc ở bên trái gồm 5 vòng tròn `#C9E0FF` (lõi `#00284B` chứa icon trắng/nhạt) được liên kết bởi 4 đoạn cầu nối sóng dọc màu `#C9E0FF`, nằm ngang hàng với 5 cụm tiêu đề và mô tả văn bản trắng hiển thị trực tiếp (borderless) trên nền banner tối.
- Sơ đồ sóng ngang 5 nút trên Desktop (`>= 1024px`) và lưới fallback khi `items.length > 5` được bảo toàn nguyên vẹn.

#### Scenario: Hiển thị timeline metaball dọc trên Mobile 390×844
- **WHEN** người dùng xem phân đoạn Lý do Khách hàng Lựa chọn có 5 mục trên Mobile `390×844`
- **THEN** 5 nút tròn viền `#C9E0FF` xếp thành cột dọc bên trái nối nhau bằng 4 cầu nối sóng dọc `#C9E0FF`, văn bản tiêu đề và mô tả nằm bên phải trực tiếp trên nền banner tối không có khung viền hộp thẻ, và khối sóng ngang Desktop không bị ảnh hưởng ở `1440×900`.

---

### Requirement: Banner Quy trình 500px Căn Đỉnh, Bỏ Cắt Chữ và Nền Sóng Mobile ProcessSection
Phân đoạn Quy trình (`ProcessSection`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52098`:
- Khối banner đầu phân đoạn trên Mobile (`1314:52099`) MUST có chiều cao `500px` (`min-h-[500px]`, tỷ lệ `aspect-[390/500]` trên màn hình nhỏ) và căn cụm tiêu đề + mô tả lên phía trên (`justify-start pt-8`) để phần đồ họa bánh răng phía dưới hiển thị rõ ràng, không bị văn bản che giữa ảnh.
- Mô tả đầu phân đoạn và mô tả trong từng thẻ bước MUST bỏ `line-clamp-4` và `line-clamp-3` để hiển thị trọn vẹn nội dung trên Mobile.
- Khung chứa danh sách các bước nền `#F8FAFC` (`1314:52102`) trên Mobile MUST bật lớp nền sóng trang trí nhẹ phía sau các thẻ bước, và mỗi thẻ bước trên Mobile sử dụng bo góc `8px` (`rounded-[8px] lg:rounded-[12px]`).

#### Scenario: Hiển thị Banner 500px và các thẻ bước không cắt dòng trên Mobile 390×844
- **WHEN** người dùng cuộn đến phân đoạn Quy trình trên Mobile `390×844`
- **THEN** banner trên cao `500px` với tiêu đề/mô tả căn trên đỉnh làm lộ rõ ảnh bánh răng ở nửa dưới, toàn bộ mô tả phần đầu và mô tả các bước hiển thị đầy đủ không bị cắt dòng (`line-clamp`), vùng `#F8FAFC` có lớp sóng trang trí tinh tế phía sau các thẻ bước bo góc `8px`.

---

### Requirement: Blog Section Spacing và Nút CTA 18px trên Mobile
Phân đoạn Tin tức (`BlogSection`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52110`:
- Khoảng cách dọc giữa tiêu đề section, danh sách bài viết và nút CTA MUST thống nhất `24px` (`gap-6`).
- Nhãn nút CTA `"Xem thêm bài viết"` MUST sử dụng cỡ chữ `18px` trên cả Mobile và Desktop.

#### Scenario: Kiểm tra spacing và nút CTA của BlogSection trên Mobile 390×844
- **WHEN** người dùng xem phân đoạn Tin tức trên viewport `390×844`
- **THEN** khoảng cách dọc giữa phần đầu section và lưới bài viết là `24px`, và nút `"Xem thêm bài viết"` có `font-size` `18px`.

---

### Requirement: Contact Section Ẩn Xích Trang trí Mobile, Spacing 24px và Nút "Nhận tư vấn ngay"
Phân đoạn Kết nối & Báo giá (`ContactSection`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52167`:
- Ảnh trang trí xích công nghiệp (`contact-chain-deco.png`) MUST bị ẩn hoàn toàn trên Mobile (`hidden md:block`) để giữ bề mặt tối `#00284B` sạch sẽ, dễ đọc.
- Khoảng cách giữa khối thông tin bên trên và thẻ form bên dưới trên Mobile MUST là `24px` (`gap-6 lg:gap-[63px]`).
- Nút gửi biểu mẫu (`button[type="submit"]`) MUST hiển thị nhãn `"Nhận tư vấn ngay"` (được bản địa hóa sang tiếng Anh tương ứng ở `/en`) với cỡ chữ `18px` trên cả Mobile và Desktop.

#### Scenario: Kiểm tra ContactSection trên Mobile 390×844 và Desktop 1440×900
- **WHEN** người dùng xem phân đoạn Liên hệ trên Mobile `390×844`
- **THEN** không có ảnh `contact-chain-deco.png` hiển thị trong viewport Mobile, khoảng cách giữa khối thông tin và thẻ form là `24px`, và nút submit hiển thị `"Nhận tư vấn ngay"` với `font-size` `18px`.

---

### Requirement: Footer Mobile 1 Cột Gọn Gàng, Ẩn CTA và Đường Kẻ Giữa
Chân trang (`Footer`) trên Mobile (`< 1024px`) SHALL khớp thiết kế Figma `1314:52197`:
- Cụm 2 nút CTA (`Nhận báo giá`, `Gọi hotline`) ở cột thương hiệu MUST ẩn trên Mobile (`hidden lg:flex`) và chỉ hiển thị từ Desktop (`>= 1024px`).
- Đường kẻ ngang phân cách giữa nhóm cột liên kết và thông tin liên hệ MUST bị ẩn trên Mobile.
- Lưới các cột liên kết (`Danh mục sản phẩm`, `Chính sách`, `Thông tin liên hệ`) trên Mobile MUST xếp thành 1 cột duy nhất (`grid-cols-1 lg:grid-cols-3`) với khoảng cách `20px` (`gap-[20px]`), không tạo 2 cột song song hẹp trên `390px`.

#### Scenario: Kiểm tra bố cục Footer trên Mobile 390×844 và Desktop 1440×900
- **WHEN** người dùng cuộn xuống chân trang trên Mobile `390×844`
- **THEN** 2 nút CTA ở cột thương hiệu không hiển thị, không có đường kẻ ngang phân cách giữa lưới liên kết, và 3 nhóm (`Danh mục sản phẩm`, `Chính sách`, `Thông tin liên hệ`) xếp dọc theo bố cục 1 cột cách nhau `20px`.
- **WHEN** người dùng xem chân trang trên Desktop `1440×900`
- **THEN** 2 nút CTA ở cột thương hiệu và lưới 3 cột ngang (`212px / 212px / 268px`) bên cạnh cột thương hiệu vẫn hiển thị đầy đủ như thiết kế chuẩn.
