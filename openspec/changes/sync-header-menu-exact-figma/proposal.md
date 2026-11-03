# Proposal: Đồng Bộ 100% Bố Cục và Cỡ Chữ Header Menu Theo Figma (Node 2352:10717)

## Why
Thanh điều hướng Desktop Header hiện tại phân bổ 3 phần tử rời rạc trực tiếp vào container `justify-between` (Logo, Nav Tabs, Right Actions). Khi render trên màn hình 1440px, khoảng cách giữa cụm 5 Nav Tabs và cụm Right Actions bị đẩy dãn ra tới ~160px, sai lệch so với thiết kế Figma chuẩn (Node `2352:10717` - Component Set "Header menu"), nơi khoảng cách này được cố định chính xác là **64px** (`gap-[64px]`).

Ngoài ra:
1. Các tab menu đang có padding ngang `px-1`, làm cho viền active underline (xanh `#2970FF`) bị tràn ra 2 bên mỗi bên 4px thay vì vừa khít chiều rộng văn bản như Figma.
2. Nền tối `#111322` trên Windows Chrome có hiện tượng font nở nét (font blooming). Cần bổ sung `antialiased` vào header để typography hiển thị thanh mảnh, sắc nét.
3. Ở dải màn hình `lg` (1024px - 1279px), chiều cao header cần thanh thoát linh hoạt `h-[80px]`, trong khi ở `xl` (1280px / 1440px+) đạt chuẩn `h-[111px]` `py-8`.

## What Changes
1. **Gom cụm Nav Tabs và Right Actions**: Đặt Nav Tabs và Right Actions vào chung một container flex bên phải:
   `<div class="hidden lg:flex items-center gap-8 xl:gap-[64px]">`
   Nhờ đó, khoảng cách giữa 5 Tab và cụm Action đúng 64px ở 1440px. Khoảng trống giữa Logo và cụm này tự động đạt 256px theo đúng Figma Node 2352:10717 (1280px content - 139px Logo - 885px Cụm Menu = 256px).
2. **Loại bỏ `px-1` ở các tab menu**: Giúp viền `border-b-2` ôm khít chiều ngang chữ.
3. **Bổ sung `antialiased`**: Thêm class `antialiased` vào thẻ `<header>`.
4. **Tối ưu responsive height**: Thiết lập `h-[64px] lg:h-[80px] xl:h-[111px] xl:py-8` để mobile đạt 64px, tablet desktop nhỏ 80px, và desktop chuẩn 111px.
5. **Giữ nguyên 100% Mobile Drawer & logic nghiệp vụ**: Toàn bộ logic drawer mobile, hover dropdown, translation, locale switch được bảo toàn nguyên vẹn.

## Capabilities

### New Capabilities
- `header-strict-figma`: Đặc tả chuẩn 100% từng pixel cho Header Desktop và Mobile theo Component Set "Header menu" (Node `2352:10717`).

### Modified Capabilities
<!-- Không có -->

## Impact
- `resources/Frontend/js/Components/Header.vue`: Tinh chỉnh bố cục flex desktop, xóa `px-1`, thêm `antialiased`, responsive height.
- `tests/e2e/header-strict-figma.spec.ts`: Xác thực cấu trúc flex và kích thước chính xác bằng Playwright.
- Tuyệt đối không xóa file, không sửa database hay ảnh hưởng backend.
