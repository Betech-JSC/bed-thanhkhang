# Kịch Bản 3: Code Gộp Nhiều Trang & Chạy Visual QA Khóa Sổ Tuần Tự (`Batch Pages + Sequential QA`)

Áp dụng khi dự án **đã hoàn thành nền tảng Design Tokens, Base Components và Layout Shell** (ví dụ sau Phase 5 hoặc sau khi đã nghiệm thu Trang Home), và team muốn tăng tốc dựng liền mạch một lô nhiều trang con (ví dụ: Giới thiệu, Danh sách Sản phẩm, Chi tiết Sản phẩm, Danh sách Tin tức, Chi tiết Tin tức, Chính sách, Liên hệ) trước khi chạy một đợt kiểm định Visual QA toàn diện trên trình duyệt thật.

---

## Giai Đoạn 1: Dựng Gộp Các Trang Có Kiểm Soát Kiến Trúc (Batch Implementation)

Với từng trang `<slug>` trong danh sách lô cần dựng, thực hiện tuần tự vòng lặp kỹ thuật sau (tạm hoãn bước mở Playwright chụp từng trang cho tới Giai đoạn 2, nhưng **KHÔNG ĐƯỢC bỏ qua bước Build Gate và cập nhật Graphify**):

1. **Lập đặc tả gọn & chuẩn (`/opsx-propose`)**:
   - Tạo change `feat-page-<slug>` (hoặc một change theo cụm chức năng chặt chẽ như `feat-catalog-pages` gồm danh sách + chi tiết sản phẩm).
   - Quét Figma frame của trang ở `depth: 2` để liệt kê rõ từng `nodeId` section trong `design.md` và `tasks.md` (trong `tasks.md`, tách riêng nhóm task `[ ] Code & Build` và nhóm task `[ ] Playwright Visual QA`).
2. **Đối chiếu AST & Thực thi Code (`/opsx-apply` — Phần Code)**:
   - `Frontend Worker` đọc `graphify-out/graph.json` mới nhất trước khi code trang đó.
   - Đọc chi tiết từng `nodeId` section qua `get_figma_data`, tải SVG/ảnh qua `download_figma_images`.
   - Đấu nối chuẩn xác vào Route và Controller/Props thực tế của dự án (với Laravel BED CMS có dùng Ziggy/i18n nhớ chạy `yarn route` nếu có thêm hoặc cập nhật route).
   - Tuân thủ tuyệt đối `anti-ui-slop` (không thêm icon/emoji trang trí ngoài thiết kế Figma, đảm bảo đủ trạng thái empty/loading/hover/focus).
3. **Chốt chặn Build & Cập nhật Đồ thị AST Ngay Lập Tức**:
   - Chạy lệnh build thật (`yarn frontend:build` hoặc `npm run build`) đảm bảo **0 lỗi biên dịch** ngay tại trang vừa code (tránh tích tụ lỗi syntax/import sang các trang sau).
   - **BẮT BUỘC** chạy `graphify . --code-only` ngay sau mỗi trang để các Shared Components vừa được tạo hoặc mở rộng ở trang thứ `N` lập tức hiển thị trong `graphify-out/graph.json` cho trang thứ `N+1` tái sử dụng!
   - Đánh dấu `[x]` các task thuộc nhóm Code & Build trong `tasks.md`, giữ nguyên `[ ]` ở nhóm task Visual QA (tuyệt đối **chưa chạy `openspec archive`** lúc này).

---

## Giai Đoạn 2: Visual QA Bằng Playwright MCP & Khóa Sổ Tuần Tự (Sequential QA & Archive)

Sau khi toàn bộ các trang trong lô đã build xanh 100%, chuyển sang chế độ nghiệm thu từng trang một cách nghiêm ngặt qua `QA Verifier`:

### Bước 2.1: Chuẩn bị môi trường chạy thật
- Đảm bảo web server và asset build mới nhất đã sẵn sàng (chạy `yarn frontend:build` bản mới nhất hoặc dev server đang hoạt động ổn định, không bị cache cũ).

### Bước 2.2: Checklist Kiểm Định Từng Trang Qua Playwright MCP
Duyệt **tuần tự từng trang một** (không nhảy cóc):
1. **Kiểm tra Desktop (`1440x900`)**:
   - Điều hướng tới URL thật của trang.
   - Kiểm tra `browser_console_messages`: đảm bảo **0 lỗi JS / Vue / React runtime** và không có request tài nguyên tĩnh trả về `404`/`500`.
   - Kiểm tra toàn bộ thẻ `<img>` đều nạp thành công và đúng tỷ lệ khung hình (không méo ảnh).
   - Đối chiếu trực quan với thiết kế Figma: hệ phân cấp tiêu đề, font-family (đặc biệt trên `<button>`, `<input>`, `<select>`), khoảng cách section, màu nền, border, hover state.
   - Chụp ảnh màn hình Desktop làm bằng chứng.
2. **Kiểm tra Mobile (`375x812`)**:
   - Đổi kích thước trình duyệt (`browser_resize`) về `375x812`.
   - Kiểm tra không bị tràn chiều ngang (`document.documentElement.scrollWidth <= window.innerWidth`).
   - Kiểm tra bố cục xếp chồng (stacking), kích thước chữ mobile, khoảng cách chạm (tap targets >= 44px), drawer/filter mobile đóng mở mượt.
   - Chụp ảnh màn hình Mobile làm bằng chứng.
3. **Xử lý lỗi tức thì (Fix-Verify Loop)**:
   - Nếu trang `X` có bất kỳ điểm lệch nào so với Figma hoặc lỗi responsive/console, dừng ngay tại trang `X`, giao `Frontend Worker` sửa dứt điểm -> build lại -> `QA Verifier` chụp kiểm tra lại trang `X` cho tới khi PASS 100% rồi mới chuyển sang trang `X+1`.

### Bước 2.3: Cập Nhật `tasks.md` & `openspec archive` Tuần Tự
- Ngay khi trang `X` đạt PASS trên cả Desktop và Mobile:
  1. Đánh dấu `[x]` toàn bộ các task Visual QA còn lại trong `openspec/changes/<change-of-page-X>/tasks.md`.
  2. Chạy `openspec validate "<change-of-page-X>"`.
  3. Sau khi người dùng xác nhận nghiệm thu báo cáo kèm ảnh chụp màn hình thật, chạy `openspec archive "<change-of-page-X>"` để đồng bộ delta specs và khóa sổ từng trang gọn gàng.
