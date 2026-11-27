# Proposal: Tối ưu Giao diện Responsive & Trải nghiệm Mobile cho Màn hình Quản lý tệp (`FileManager`)

## Why

Màn hình Quản lý tệp (`packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue` và `Thumbnail.vue`) hiện được thiết kế cố định cho Desktop, gây vỡ bố cục và mất khả năng thao tác trên màn hình Mobile (`375px` / `390px`), Tablet (`768px` / `1024px`) và chế độ nhúng (`embed=true`) qua 4 nguyên nhân gốc đã xác minh trong mã nguồn:

1. **Định vị `left: var(--sidebar-width)` (`16rem` = `256px`) vô điều kiện ở mọi độ phân giải**:
   - Class `.left-from-sidebar` (`FileManager.vue` L1245–1247) và rule `.topbar + div` (`FileManager.vue` L1253–1258) gán cứng `left: var(--sidebar-width)` không có media query `@media (min-width: 768px)` (`md:`), khiến toàn bộ khung `FileManager` bị đẩy lệch sang phải `256px` và bị ép bề rộng còn `119px` trên màn hình `375px` (cũng như che mất thanh header mobile chứa nút mở sidebar của `Authenticated.vue` tại `top: 0`, `z-50` ở chế độ trang độc lập `/admin/files`).
2. **Thanh `.topbar` chiều cao cố định, ô tìm kiếm `w-[400px]` và 7 nút in hoa không co giãn/xuống dòng**:
   - `.topbar` (`FileManager.vue` L1248–1251) dùng `position: absolute`, `height: var(--topbar-height)` (`4rem`), bên trong là `<h1>` không có `whitespace-nowrap`/`shrink-0`, ô `<input>` cố định `w-[400px]` (L30), cùng 7 nút `<Button>` in hoa (`Tạo thư mục`, `Chọn tệp`, `Đổi tên thư mục này`, `Đổi tên`, `Sao chép`, `Cắt`, `Dán` — L33–84) không có `flex-wrap`/`whitespace-nowrap`. Trên Tablet và Mobile, tiêu đề bị gãy dọc (`Quản / lý / tệp`), chữ trong nút gãy đôi (`TẠO THƯ / MỤC`), và thanh công cụ tràn ngang ra ngoài khung nhìn.
3. **Cây thư mục `<aside>` ẩn hoàn toàn dưới `1024px` (`< lg`) và làm mất nút Upload trong `embed=true`**:
   - `<aside class="hidden ... w-80 lg:block">` (`FileManager.vue` L100) bị ẩn dưới `1024px` mà không có nút bật drawer cây thư mục cho Mobile/Tablet. Đặc biệt ở chế độ `embed=true` (iframe TinyMCE từ `CustomEditor.vue` hoặc popup CKEditor), thanh `.topbar` bị ẩn (`v-if="!embed"`) và nút `Chọn tệp` duy nhất lại nằm bên trong `<aside>` (L101–107), khiến người dùng `< 1024px` vừa mất cây thư mục vừa mất hoàn toàn nút tải tệp lên.
4. **Thiếu tương tác cảm ứng (Touch Usability) và an toàn khung nhìn Mobile**:
   - Lưới `grid-cols-3` (`FileManager.vue` L167) quá chật trên `< 640px`; điều hướng thư mục phụ thuộc vào `@dblclick` (L190); các nút hành động trên thẻ ở `Thumbnail.vue` (L20–33) bị ẩn bằng `invisible group-hover:visible` (và link mở tab mới chỉ có kích thước `20×20px`); menu thao tác (`Đổi tên`, `Sao chép`, `Cắt`, `Dán vào đây`, `Xóa`) chỉ mở qua chuột phải `@contextmenu` (L172); Breadcrumb không `flex-wrap` và thiếu nút quay lên thư mục cha cho màn hình cảm ứng; thanh chọn nhiều ở đáy (`FileManager.vue` L213–221) thiếu đệm `env(safe-area-inset-bottom)` và dễ che hàng item cuối.

## What Changes

Chỉ chỉnh sửa **duy nhất màn hình Quản lý tệp** để đảm bảo hiển thị gọn gàng, đầy đủ thao tác trên Desktop (`1440×900`), Tablet (`768×1024`, `1024×768`) và Mobile (`375×667`, `390×844`) ở cả 3 ngữ cảnh (trang độc lập `/admin/files`, modal chọn tệp `selectable=true` từ `InputUpload.vue` / `Documents/Form.vue`, và chế độ nhúng `embed=true` từ `CustomEditor.vue`):

- **Chuẩn hóa khung định vị Responsive (`FileManager.vue`)**:
  - Thay cặp hack `.topbar` `position: absolute` + `.topbar + div` `position: fixed; left: var(--sidebar-width)` bằng shell `flex flex-col` responsive:
    - Dưới `768px` (`< md`): tràn `left-0 right-0`; từ `md` trở lên (`>= 768px`): `md:left-[var(--sidebar-width)]` (khi không `embed`).
    - Ở chế độ trang độc lập `/admin/files` (`!selectable && !embed`): đặt `top-[57px] md:top-0` và `z-20 md:z-30` (hoặc tương đương dưới `z-30`/`z-40` của `SidebarMobile.vue`) để giữ nguyên thanh header mobile & nút hamburger mở menu Admin của `Authenticated.vue`.
    - Ở chế độ picker (`selectable=true`) và nhúng (`embed=true`): giữ overlay toàn màn hình `top-0 z-50` cùng nút đóng `<ph-caret-left />` (khi `selectable=true`).
- **Thiết kế lại Thanh Toolbar & Tìm kiếm Responsive (`FileManager.vue`)**:
  - Giữ tiêu đề `Quản lý tệp` (`whitespace-nowrap shrink-0`), bỏ `w-[400px]` cứng trên `<input>` tìm kiếm (`w-full sm:w-64 lg:w-80`).
  - Sắp xếp nhóm nút gọn theo triết lý *Quiet & High-Trust UI* (chỉ giữ hành động chính `Chọn tệp` và `Tạo thư mục` cùng nút bật cây thư mục `< lg` ở hàng thao tác chính; gom nhóm thao tác ngữ cảnh `Đổi tên thư mục này`, `Đổi tên`, `Sao chép`, `Cắt`, `Dán` vào thanh phụ gọn hoặc menu `"Thao tác"` trên màn hình nhỏ/vừa, đảm bảo `whitespace-nowrap`, không vỡ chữ, không spam icon/emoji).
- **Thêm Drawer Cây Thư mục cho `< 1024px` & Header tối giản cho `embed=true` (`FileManager.vue`)**:
  - Bổ sung thanh điều khiển compact khi `embed=true` (hoặc hiện nút mở Drawer cây thư mục + nút `Chọn tệp` ở khu vực toolbar/breadcrumb `< lg`) và slide-over Drawer chứa `Field[type="tree"]` (`TreeViewItem.vue`) dưới `1024px`, tự đóng drawer khi chọn thư mục.
- **Tối ưu Lưới, Thao tác Cảm ứng & Hộp thoại (`FileManager.vue`, `Thumbnail.vue`)**:
  - Đổi lưới item sang `grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6`.
  - Hỗ trợ điều hướng thư mục một chạm thân thiện trên mobile/touch (nút mở thư mục / hành vi tap rõ ràng kết hợp giữ `@dblclick` trên desktop) và thêm nút trigger menu hành động (`ph:dots-three-vertical` vùng chạm tối thiểu `36×36px`–`44×44px`) trên từng item để gọi `openContextMenu` trên thiết bị cảm ứng.
  - Cập nhật `Thumbnail.vue` để các nút xem mở rộng và xóa luôn hiển thị/chạm được trên thiết bị không có hover (`opacity-100 lg:opacity-0 lg:group-hover:opacity-100` hoặc `visible lg:invisible lg:group-hover:visible`) với vùng chạm đạt chuẩn, tương thích ngược hoàn toàn với `InputUpload.vue`.
  - Bổ sung `flex-wrap` + nút "Lên thư mục cha" (`go_up`) cho Breadcrumb; bổ sung `padding-bottom: calc(0.75rem + env(safe-area-inset-bottom))` cho thanh chọn nhiều ở đáy; hoàn thiện Dialog Tạo thư mục / Đổi tên (tiêu đề đa ngôn ngữ `tt('models.files.add_folder')`, modal backdrop, width `95vw` trên mobile).

## Capabilities

### New Capabilities
- `file-manager-mobile-ui`: Đặc tả hành vi responsive, thanh công cụ, ngăn kéo cây thư mục `< lg`, chế độ nhúng `embed=true`, tương tác cảm ứng và an toàn bố cục cho màn hình Quản lý tệp (`FileManager.vue` và `Thumbnail.vue`).

### Modified Capabilities
<!-- Không có spec hiện hữu nào thay đổi requirement; toàn bộ phạm vi nằm gọn trong capability mới file-manager-mobile-ui. -->

## Impact

- **Tệp dự kiến chỉnh sửa (chỉ trong phạm vi Quản lý tệp)**:
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue`
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue`
  - (Tuỳ chọn chỉ nếu kiểm chứng thực tế thấy iframe TinyMCE `< 640px` cần tinh chỉnh CSS dialog trực tiếp cho File Manager): `packages/bed-package-essentials/core/resources/Backend/js/Components/Form/Custom/CustomEditor.vue`
- **Các điểm tích hợp được kiểm tra bảo toàn tương thích**:
  - `packages/bed-package-essentials/core/resources/Backend/js/Pages/FileManager.vue` (trang độc lập `/admin/files`)
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Form/InputUpload.vue` (dùng chung `FileManager.vue` và `Thumbnail.vue`)
  - `resources/Backend/js/Pages/Documents/Form.vue` (dùng `FileManager.vue` chọn PDF)
  - `resources/Backend/js/ckeditor5/src/custom-plugins/filemanager.js` (mở `/admin/files?embed=true&select-multiple=true`)

## Phạm vi An toàn (Safety Scope)

- **KHÔNG** tạo hoặc chạy bất kỳ database migration, seeder hay câu lệnh can thiệp dữ liệu (`migrate:fresh`, `drop`, `truncate`).
- **KHÔNG** xóa tệp tin hoặc thay đổi cấu trúc thư mục hiện hữu của dự án.
- **KHÔNG** thay đổi backend API routes, controllers hoặc models (`packages/bed-package-essentials/core/src/Controllers/FileController.php`, `packages/bed-package-essentials/core/src/Models/File.php`, `packages/bed-package-essentials/core/routes/backend.php`).
- **KHÔNG** chỉnh sửa các trang, bố cục hay component Admin CMS ngoài phạm vi Quản lý tệp (`Authenticated.vue`, `SidebarMobile.vue`, `SidebarDesktop.vue`, `Table.vue`, `Form.vue`, `Field.vue`, `Dashboard.vue`, các file Frontend).
