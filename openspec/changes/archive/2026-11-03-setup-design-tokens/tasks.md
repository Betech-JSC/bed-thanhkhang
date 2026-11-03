# Tasks: Thiết lập và Đồng bộ Hệ thống Design Tokens

## 1. Container & Layout Rhythm Spacing Tokens

- [x] 1.1 Khóa cứng max-width của `container` ở mức `1240px` và cấu hình padding responsive (`1rem` cho mobile, `1.5rem` cho tablet/desktop) trong `resources/Frontend/tailwind.config.js`. Xác minh bằng cách kiểm tra cú pháp cấu hình `container.screens.xl = '1240px'` và `container.center = true`.
- [x] 1.2 Bổ sung các tokens khoảng cách nhịp điệu vào `theme.extend.spacing`: `section: '5rem'` (`80px`), `section-lg: '7.5rem'` (`120px`), `gutter: '1.5rem'` (`24px`) trong `resources/Frontend/tailwind.config.js`. Xác minh sự tồn tại của các token trong cấu hình `spacing`.

## 2. Semantic Colors & Syntax Harmonization

- [x] 2.1 Bổ sung alias `DEFAULT` cho các palette trạng thái ngữ thái: `success: { DEFAULT: '#12B76A', ... }`, `warning: { DEFAULT: '#F79009', ... }`, `error: { DEFAULT: '#F04438', ... }` trong `resources/Frontend/tailwind.config.js`. Xác minh bằng kiểm tra object colors chứa key `DEFAULT`.
- [x] 2.2 Bổ sung đầy đủ palette `info` với `DEFAULT: '#2E90FA'` cùng dải sắc độ từ 25 đến 900 vào `resources/Frontend/tailwind.config.js`. Xác minh palette info được khai báo hoàn chỉnh và hỗ trợ `bg-info`.
- [x] 2.3 Phân tách hệ thống text tokens trong `theme.extend.colors`: khai báo `text-white: '#FFFFFF'`, `text-secondary: '#94A3B8'`, `text-muted: '#64748B'`, `text-dim: '#475467'`, và loại bỏ thuộc tính `'text-primary': '#FFFFFF'` gây xung đột class `text-primary`. Xác minh không còn xung đột phân giải màu chữ.
- [x] 2.4 Đánh dấu deprecate và cô lập các mã màu kế thừa FunkyStyle (`red-fks`, `black-fks`, `green-fks`, `blue-fks`) trong `resources/Frontend/tailwind.config.js`. Xác minh ghi chú deprecation rõ ràng trong mã nguồn cấu hình.

## 3. Font-Face Cleanup & Typography Scale Tokenization

- [x] 3.1 Dỡ bỏ hoàn toàn 7 khối `@font-face` không sử dụng (`Phudu` 4 khối và `BT-BeauSans` 3 khối) khỏi `resources/Frontend/scss/app.scss`. Xác minh không còn khai báo `@font-face` cho `Phudu` và `BT-BeauSans` trong tệp.
- [x] 3.2 Dọn dẹp alias `beau: ['BT-BeauSans', 'sans-serif']` trong `fontFamily` của `resources/Frontend/tailwind.config.js`. Xác minh `fontFamily` chỉ còn lại `display`, `sans`, và `mono`.
- [x] 3.3 Token hóa toàn bộ thang chữ chuẩn Figma (`display-2xl`, `display-xl`, `display-lg`, `display-md`, `display-sm`, `display-xs`, `text-xl`, `text-lg`, `text-md`, `text-sm`, `text-xs`) kèm `lineHeight` và `letterSpacing` vào `theme.extend.fontSize` trong `resources/Frontend/tailwind.config.js`. Xác minh thang kích thước được cấu hình dưới dạng tuple đầy đủ thuộc tính.

## 4. Extended Border Radius & Dark Mode Shadows

- [x] 4.1 Mở rộng `theme.extend.borderRadius` với các tokens hệ thống: `input: '10px'`, `panel: '20px'`, `pill: '9999px'` trong `resources/Frontend/tailwind.config.js`. Xác minh các token bo góc đã hiện diện trong tệp cấu hình.
- [x] 4.2 Bổ sung các tokens shadow nổi khối Dark Tech và border glow vào `theme.extend.boxShadow`: `card-dark`, `panel-dark`, `glow-cyan`, `glow-primary-subtle` trong `resources/Frontend/tailwind.config.js`. Xác minh các token shadow được khai báo chính xác.

## 5. Build Verification, AST Graph Update & Visual QA

- [x] 5.1 Thực thi biên dịch toàn diện giao diện `yarn frontend:build` và xác minh terminal thoát với mã lỗi 0 (không phát sinh lỗi CSS/Tailwind).
- [x] 5.2 Rà soát mã nguồn Frontend bằng script grep/Select-String để xác nhận không còn component nào gọi `font-beau` hoặc `text-text-primary`.
- [x] 5.3 Chạy cập nhật đồ thị kiến trúc mã nguồn qua lệnh `graphify . --code-only` theo quy tắc dự án và xác nhận `graphify-out/graph.json` được làm mới.
- [x] 5.4 Chạy bộ kiểm thử tự động Playwright E2E (`npx playwright test`) để kiểm chứng trực quan layout container 1240px và typography hiển thị ổn định trên cả Desktop và Mobile.
