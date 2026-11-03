# Spec Delta: header

## Purpose
Đặc tả cấu trúc chuẩn 100% từng pixel cho thanh điều hướng Header Menu theo bản vẽ Figma (Node `2352:10717` - Component Set "Header menu"), đảm bảo bố cục flex gom nhóm bên phải, gap 64px giữa 5 Nav Tabs và Right Actions, underline active vừa khít text và typography mượt mà chống blooming.

## ADDED Requirements

### Requirement: Bố Cục Khung Header và Gom Nhóm Flex Desktop Chuẩn Figma
Header Desktop SHALL sử dụng nền solid `#111322` với `antialiased`, chiều cao `h-[111px]` `py-8` ở màn hình chuẩn 1440px (`xl`) và `h-[80px]` ở laptop nhỏ (`lg`). Cụm 5 Nav Tabs và cụm Right Actions SHALL được nhóm chung vào một flex container bên phải với `gap-[64px]` ở 1440px:
1. Logo HH Maker (Node `2352:10264`, kích thước 139px x 47px) nằm ở bên trái container `justify-between`.
2. Cụm nhóm Desktop gom chung 5 Nav Tabs (Frame 261, 484px x 47px) và Right Actions (Frame 1000005802, 337px x 40px) nằm ở bên phải.
3. Khoảng cách giữa 5 Nav Tabs và Right Actions cố định chính xác 64px (`gap-[64px]`), triệt tiêu khoảng giãn cách 160px do `justify-between` gây ra.
4. Khoảng cách tự nhiên từ Logo đến cụm nhóm đạt ~256px ở viewport 1440px (1280px nội dung - 139px Logo - 885px cụm menu = 256px).

#### Scenario: Hiển thị thanh Header trên màn hình Desktop 1440px
- **WHEN** người dùng truy cập trang chủ trên màn hình Desktop độ phân giải 1440px
- **THEN** Header hiển thị với nền `#111322`, chiều cao 111px, Logo kích thước 139x47px ở bên trái, khoảng cách giữa 5 Nav Tabs và Right Actions đúng 64px, và khoảng cách giữa Logo với cụm menu đạt ~256px.

---

### Requirement: Tinh Chỉnh 5 Nav Tabs và Underline Active Khớp Kích Thước Text
Cụm 5 Nav Tabs SHALL hiển thị đúng 5 tab theo thứ tự ("Trang chủ", "Sản phẩm", "Dịch vụ", "Giới thiệu", "Quy trình") với font Inter 16px font-semibold leading-6, và KHÔNG chứa padding ngang `px-1` để đường viền `border-b-2` active ôm khít 100% độ rộng văn bản:
1. Tab "Trang chủ" active hiển thị chữ màu `#2970FF` cùng viền đáy `border-[#2970FF] border-b-2` có chiều rộng khớp chính xác với độ rộng text.
2. Tab "Sản phẩm" có biểu tượng chevron-down 20px (`w-5 h-5`).
3. Các tab còn lại ở trạng thái inactive với chữ màu `#FAFAFA` và viền trong suốt `border-transparent`.

#### Scenario: Kiểm tra độ rộng đường viền active tab Trang chủ
- **WHEN** người dùng đang ở trang chủ `/`
- **THEN** tab "Trang chủ" active với màu chữ và viền xanh `#2970FF`, các tab link không có class `px-1` thừa, đường viền active không bị dư 4px ở hai bên.

---

### Requirement: Cụm Right Actions Chuẩn 3 Thành Phần Figma
Cụm Right Actions SHALL hiển thị đúng 3 thành phần theo thứ tự từ trái sang phải với kích thước 337px x 40px:
1. Icon Tìm kiếm (Node `2352:10286`): 24px x 24px (`w-6 h-6`).
2. Language Switcher (Node `2380:1477`): 96px x 34px, viền `#5D6B98`, chuyển đổi giữa VIE và ENG.
3. Nút CTA "Liên hệ ngay" (Node `2352:10291`): 137px x 40px, font Inter 16px font-medium, nền gradient `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`.
4. Không có icon giỏ hàng và nút đăng nhập trên thanh Header Desktop.

#### Scenario: Kiểm tra thứ tự và hiển thị cụm Right Actions trên Desktop
- **WHEN** quan sát cụm chức năng bên phải thanh Header
- **THEN** icon Tìm kiếm nằm ở bên trái, Language Switcher ở giữa, và nút CTA "Liên hệ ngay" nằm ở ngoài cùng bên phải.
