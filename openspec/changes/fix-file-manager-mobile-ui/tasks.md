# Tasks: Tối ưu Giao diện Responsive & Trải nghiệm Mobile cho Màn hình Quản lý tệp

## 1. Khung định vị Responsive & Shell Flexbox (`FileManager.vue`)

- [x] 1.1 Thay thế `.left-from-sidebar` và cặp hack `.topbar` absolute + `.topbar + div` fixed trong `packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue` bằng shell `flex flex-col` responsive (`left-0 right-0 md:left-[var(--sidebar-width)]` khi `!embed`; `top-[57px] md:top-0 z-20 md:z-30` cho trang độc lập `/admin/files` và `top-0 z-50` cho `selectable || embed`); cập nhật lớp phủ kéo thả và ref `scrollContainer` có guard kiểm tra tồn tại. Kiểm chứng: ở `375×667` trên `/admin/files`, container không còn lệch phải `256px` và nút mở sidebar của `Authenticated.vue` vẫn nhấn được.
- [x] 1.2 Bảo toàn nút đóng `<ph-caret-left />` với vùng chạm tối thiểu `40×40px` khi `selectable=true` và kiểm tra phím `Escape` đóng modal picker đúng hành vi. Kiểm chứng: mở picker từ `InputUpload.vue` ở `375×667` đóng được bằng nút quay lại và phím `Escape`.

## 2. Thanh Toolbar Responsive & Chế độ Nhúng `embed=true` (`FileManager.vue`)

- [x] 2.1 Thiết kế lại `.topbar` (`v-if="!embed"`) để tiêu đề `Quản lý tệp` có `whitespace-nowrap shrink-0`, ô tìm kiếm co giãn linh hoạt (`w-full sm:w-64 xl:w-72` thay cho `w-[400px]`), và các nút chính (`Chọn tệp`, `Tạo thư mục`, nút mở cây thư mục `< lg`) luôn nằm trên một dòng (`whitespace-nowrap`), tuân thủ *Quiet & High-Trust UI* không thêm emoji/icon thừa. Kiểm chứng: `scrollWidth <= innerWidth` ở `375×667`, `390×844`, `768×1024`, `1024×768`, `1440×900`, không có chữ gãy dọc trong tiêu đề hay nút.
- [x] 2.2 Tổ chức nhóm hành động ngữ cảnh (`Đổi tên thư mục này`, `Đổi tên`, `Sao chép`, `Cắt`, `Dán (N)`) dạng menu/popover `"Thao tác"` gọn gàng trên `< xl` và thanh nút chuẩn trên `>= xl`, bảo toàn chính xác logic `disabled` và bộ đếm clipboard. Kiểm chứng: mọi hành động Copy/Cut/Paste/Rename đều thực thi được trên Mobile `375×667` và Desktop `1440×900`.
- [x] 2.3 Bổ sung thanh điều khiển compact cho chế độ `embed=true` chứa nút mở Drawer cây thư mục (`lg:hidden`), nút `Chọn tệp` (upload), nút `Tạo thư mục` và ô tìm kiếm, đồng bộ trạng thái tìm kiếm khi chuyển thư mục. Kiểm chứng: tại `/admin/files?embed=true&select-multiple=true` ở `375×667` và `768×1024`, nút tải tệp và cây thư mục đều hiển thị và upload thành công.

## 3. Ngăn kéo Cây Thư mục dưới `1024px` (`FileManager.vue`)

- [x] 3.1 Thêm state `showFolderDrawer` và slide-over Drawer dưới `lg` (`< 1024px`) tái sử dụng `<Field :field="{ key: 'FileManager', type: 'tree', ... }" />` (`TreeViewItem.vue`), có backdrop đóng khi chạm ngoài, nút đóng `40×40px`, và tự động đóng drawer trong `selectedItem(item)` sau khi chọn thư mục. Kiểm chứng: mở drawer ở `375×667` và `768×1024`, chọn thư mục con chuyển danh sách tệp đúng thư mục và drawer tự đóng.

## 4. Lưới Tệp, Tương tác Cảm ứng, `Thumbnail.vue`, Thanh Đáy & Hộp thoại

- [x] 4.1 Đổi lưới tệp/thư mục sang `grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3 sm:gap-4`, thêm `flex-wrap` và nút quay lên thư mục cha (`tt('models.files.go_up')`) trên Breadcrumb khi `currentPath !== '/'`, và căn giữa có padding cho trạng thái thư mục rỗng (`canDeleteFolder`). Kiểm chứng: lưới hiển thị 2 cột cân đối trên `375×667`, đường dẫn dài tự xuống dòng và quay lên thư mục cha thành công.
- [x] 4.2 Bổ sung nút menu hành động (`ph:dots-three-vertical`, `min-w-[36px] min-h-[36px]`) trên từng thẻ để mở `ContextMenu` bằng một chạm không cần chuột phải, thêm nút `Mở` một chạm cho thẻ thư mục, và cho phép `onSelect` điều hướng vào thư mục khi ở chế độ chèn đơn `embed`. Kiểm chứng: trên `375×667`, chạm mở thư mục con và chạm nút `...` mở menu `Đổi tên / Sao chép / Cắt / Dán vào đây / Xóa` hoạt động đầy đủ.
- [x] 4.3 Cập nhật `packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue` để link xem tệp (`top-1.5 right-1.5`, `36×36px`) và nút `Xóa` (`bottom-1.5 right-1.5`, `min-h-[36px]`) luôn hiển thị chạm được trên `< lg` (`opacity-100 lg:opacity-0 lg:group-hover:opacity-100`), kiểm tra tương thích ngược tại `InputUpload.vue` và `Documents/Form.vue`. Kiểm chứng: nút xem và xóa hiển thị rõ trên mobile ở cả `FileManager` và form có `InputUpload`.
- [x] 4.4 Chuẩn hóa thanh chọn nhiều ở đáy (`pb-[calc(0.625rem+env(safe-area-inset-bottom))]` + `pb-24` cho `<main>`) và nâng cấp 2 PrimeVue `Dialog` Tạo thư mục (`:header="tt('models.files.add_folder')"`, kiểm tra tên không rỗng) và Đổi tên (`:modal="true"`, `maxWidth: '95vw'`). Kiểm chứng: chọn nhiều tệp trên `375×667` không che hàng tệp cuối, mở modal tạo thư mục/đổi tên hiển thị tiêu đề tiếng Việt và không tràn màn hình.

## 5. Build & Nghiệm thu Đa Khung nhìn (Verification)

- [x] 5.1 Chạy biên dịch Admin CMS: `yarn backend:build` và xác nhận Exit code `0`, không phát sinh lỗi Vite/Tailwind/SCSS.
- [x] 5.2 Nếu có thay đổi import/cấu trúc component, chạy `graphify . --code-only` để đồng bộ `graphify-out/graph.json`.
- [x] 5.3 Thực hiện Playwright Visual QA & kiểm tra chức năng thực tế trên trình duyệt thật tại 5 viewport (`1440×900`, `1024×768`, `768×1024`, `390×844`, `375×667`) cho cả 3 luồng:
  - Trang độc lập `/admin/files` (kiểm tra layout, hamburger sidebar mobile, toolbar, drawer cây thư mục, menu thẻ, breadcumb, tạo/đổi tên thư mục).
  - Modal picker `selectable=true` trong một form dùng `InputUpload` (kiểm tra mở/đóng picker, chọn ảnh, kiểm tra `Thumbnail.vue`).
  - Chế độ nhúng `/admin/files?embed=true&select-multiple=true` (kiểm tra nút Upload & Drawer `< 1024px`, chọn nhiều ảnh & thanh đáy safe-area).
  - Thu thập ảnh chụp màn hình Playwright và kiểm tra `document.documentElement.scrollWidth <= window.innerWidth` ở mọi viewport; chỉ cập nhật `CustomEditor.vue` nếu bằng chứng Playwright trên `375×667` phát hiện lỗi khung `.tox-dialog` trực tiếp ảnh hưởng tới iframe File Manager.
