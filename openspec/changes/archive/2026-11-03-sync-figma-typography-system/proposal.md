# Proposal: Chuẩn Hóa Hệ Thống Typography Đồng Bộ 100% Theo Figma

## Why
Sau đợt kiểm toán chuyên sâu `/opsx-explore` và `opsx-ui-audit` Phase 2 trên toàn bộ 8 trang thiết kế của HH Maker (Canvas `2261:5076` trên Figma MCP), kết quả chỉ ra rằng bản thiết kế Figma tuân thủ nghiêm ngặt một thang đo chữ thống nhất (Design System Type Scale) với font `Inter`, tiêu đề H1/H2 luôn là `38px` (Line-height 46px, Bold), CTA banner là `32px`, tiêu đề bài viết là `28px`, card là `24px`.
Tuy nhiên, cấu hình `tailwind.config.js` hiện tại chưa khai báo các cỡ chữ chuẩn này (đang thiếu `38px`, `32px`, `28px`, `42px`), và font stack đang ưu tiên `'Be Vietnam Pro'` thay vì `'Inter'`. Điều này dẫn đến việc mã nguồn các trang Vue phải hardcode các class rời rạc (`text-[38px]`, `text-[28px]`, `text-[32px]`) hoặc xuất hiện sai lệch do dev ước lượng mắt thường (`Product/Index.vue` dùng `text-[40px]`, `About/Index.vue` dùng `text-[42px]`).
Cần chuẩn hóa lại bộ Design Tokens cho Typography và đồng bộ các trang giao diện về đúng chuẩn Figma 100%.

## What Changes
1. **Chuẩn hóa cấu hình Font Stack trong `resources/Frontend/tailwind.config.js`**:
   - Đặt `'Inter'` làm font giao diện chính (primary sans & display font): `['Inter', 'Be Vietnam Pro', 'sans-serif']`.
2. **Bổ sung các Typography Tokens chuẩn Figma vào `theme.extend.fontSize`**:
   - `display-hero`: `['38px', { lineHeight: '46px', letterSpacing: '-0.01em' }]` (chuẩn H1/H2 của cả 8 trang).
   - `display-banner`: `['32px', { lineHeight: '38px', letterSpacing: '-0.01em' }]` (chuẩn Banner CTA).
   - `display-section`: `['28px', { lineHeight: '34px', letterSpacing: '0' }]` (chuẩn tiêu đề bước/bài viết).
   - `display-card`: `['24px', { lineHeight: '30px', letterSpacing: '0' }]` (chuẩn tiêu đề card tính năng/sản phẩm).
   - `display-step`: `['42px', { lineHeight: '51px', letterSpacing: '0' }]` (chuẩn số thứ tự quy trình).
3. **Quy hoạch và sửa các vị trí lệch kích thước chữ trong các trang Vue**:
   - Chuẩn hóa Hero title `Product/Index.vue` từ `text-[40px]` về `text-display-hero` (`38px`).
   - Chuẩn hóa Hero title `About/Index.vue` từ `text-[42px]` về `text-display-hero` (`38px`).
   - Chuẩn hóa các tiêu đề section trên `Home`, `Services`, `Process`, `Contact` dùng token đồng bộ.
4. **Kiểm tra và nghiệm thu**:
   - Build client và SSR qua `yarn frontend:build` thành công 100%.
   - Chạy test Playwright E2E xác thực typography hiển thị chính xác.

## Capabilities

### New Capabilities
<!-- Không có capability mới -->

### Modified Capabilities
- `design-tokens`: Cập nhật đặc tả thang đo cỡ chữ chuẩn Figma (bổ sung `38px`, `32px`, `28px`, `24px`, `42px`) và cấu hình font stack ưu tiên `'Inter'`.

## Impact
- `resources/Frontend/tailwind.config.js`: Cập nhật `fontFamily` và `fontSize`.
- Các component/pages: `resources/Frontend/js/Pages/Product/Index.vue`, `resources/Frontend/js/Pages/About/Index.vue`, `resources/Frontend/js/Pages/Home/Index.vue`, v.v.
- Không ảnh hưởng đến backend hay cơ sở dữ liệu.
