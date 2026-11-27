# Design: Thiết kế Kỹ thuật Tối ưu Responsive & Mobile UI cho Quản lý tệp

## Context

Tài liệu này mô tả giải pháp kỹ thuật thực hiện các yêu cầu tại `specs/file-manager-mobile-ui/spec.md` nhằm khắc phục 4 nhóm nguyên nhân gốc đã nêu trong `proposal.md`.

### Đối chiếu Đồ thị Kiến trúc AST (`graphify-out`)
Theo kiểm tra `graphify-out/graph.json` và `graphify-out/GRAPH_REPORT.md` (Community 33 `ref_core_components` & Community 51 `Components/FileManager.vue`), kiến trúc hiện hữu gồm 95 AST nodes thuộc cụm File Manager với các phụ thuộc thực tế sau:
- **Component lõi & Sub-components**:
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue` (`FileManager.vue`): import `Pagination.vue`, `Thumbnail.vue`, `ContextMenu` (`primevue/contextmenu`), `provide('fileManagerContext', this)`.
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue` (`Thumbnail.vue`): import `VLazyImage`, `VuePlyr`, phát sự kiện `@remove`.
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/TreeViewItem.vue` (`TreeViewItem.vue`): nhận `inject: ['fileManagerContext']` (`isSelectedNode()` kiểm tra `fileManagerContext.currentPath === element.path`), phát sự kiện `$bus.emit('treeSelectedItemFileManager', element)` được lắng nghe tại `FileManager.vue` L369.
- **Các điểm gọi (Callers) không tạo file trùng lặp**:
  - `packages/bed-package-essentials/core/resources/Backend/js/Pages/FileManager.vue`: render `<FileManager :show="true" :selectable="false" :canDeleteFolder="true" />` bên trong layout mặc định `Authenticated.vue` (được gán tại `resources/Backend/js/app.js` L54).
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Form/InputUpload.vue`: render cả `<Thumbnail>` (L13, L35) lẫn `<FileManager v-if="showMediaManager" v-model:show="showMediaManager" @onSelect="onSelect" :multiple="multiple" />` (L51–56).
  - `resources/Backend/js/Pages/Documents/Form.vue`: render `<FileManager v-if="showPdfManager" v-model:show="showPdfManager" :multiple="false" @on-select="handleSelectPdf($event, form)" />` (L70–75).
  - `packages/bed-package-essentials/core/resources/Backend/js/Components/Form/Custom/CustomEditor.vue` (TinyMCE `filemanager` button L86–96) và `resources/Backend/js/ckeditor5/src/custom-plugins/filemanager.js` (L23): mở URL `/admin/files?embed=true&select-multiple=true` qua iframe/popup.
- **Backend API Contract (giữ nguyên 100%)**:
  - `packages/bed-package-essentials/core/src/Controllers/FileController.php` & `File.php`: `admin.files.index`, `admin.files.store`, `admin.files.destroy`, `admin.files.folders.create`, `admin.files.folders.delete`, `admin.files.rename`, `admin.files.copy`, `admin.files.move`.
- **Từ điển đa ngôn ngữ sẵn có (`resources/lang/vi/models.php` L178–217)**:
  - Đã có đủ khóa: `file_manager`, `add_folder`, `delete_folder`, `select_file`, `rename`, `rename_current_folder`, `copy`, `cut`, `paste`, `paste_here`, `pasting`, `open`, `delete`, `go_up` ("Lên thư mục cha"), `current_folder`, `unchecked`, `select`, `cancel`, `save`, `new_name`.

Vì `graphify-out` xác nhận đầy đủ hệ thống component dùng chung (`FileManager.vue`, `Thumbnail.vue`, `TreeViewItem.vue`, PrimeVue `Button`/`Dialog`/`ContextMenu`), thiết kế này **không tạo thêm file component mới nào**, chỉ nâng cấp trực tiếp `FileManager.vue` và `Thumbnail.vue`.

## Goals / Non-Goals

**Goals:**
- Giải quyết triệt để lỗi lệch khung, gãy chữ thanh topbar, mất cây thư mục và mất nút upload `< 1024px` (đặc biệt trong `embed=true`), và các điểm nghẽn cảm ứng trên Mobile/Tablet.
- Giữ nguyên 100% logic nghiệp vụ, props (`show`, `multiple`, `selectable`), events (`update:show`, `on-select`), phím tắt Desktop, drag-and-drop Desktop và API backend.
- Bảo toàn tương thích ngược với `InputUpload.vue`, `Documents/Form.vue`, `Pages/FileManager.vue`, `CustomEditor.vue`, `ckeditor5/.../filemanager.js`.
- Tuân thủ *Quiet & High-Trust UI* (`anti-ui-slop`) và bộ màu `#005EA4` / `#00284B` của Backend Admin (`app.scss` & `tailwind.config.js`).

**Non-Goals:**
- Không sửa `Authenticated.vue`, `SidebarMobile.vue`, `SidebarDesktop.vue`, `TreeViewItem.vue`, `InputUpload.vue`, `Documents/Form.vue` hay `FileController.php`.
- Không thiết kế lại luồng Upload backend, không thêm tính năng chỉnh sửa ảnh hay thư viện UI ngoài PrimeVue/Tailwind/unplugin-icons sẵn có.

## Decisions

### Decision 1: Thay cấu trúc `.topbar` absolute + `.topbar + div` fixed bằng shell `flex flex-col` phân biệt ngữ cảnh
- **Hiện trạng (`FileManager.vue` L2–5, L87, L1244–1259)**: `.left-from-sidebar` và `.topbar + div` đặt `left: var(--sidebar-width)` (`16rem`) không có breakpoint, còn khối gốc ép `fixed top-0 bottom-0 right-0 z-50`.
- **Quyết định**:
  - Chuyển khối gốc thành `flex flex-col fixed right-0 bottom-0 overflow-hidden bg-white` với các lớp điều kiện:
    1. **Vị trí ngang**:
       - Khi `embed` truthy: `left-0`.
       - Khi `!embed`: `left-0 md:left-[var(--sidebar-width)]` (thay thế `.left-from-sidebar` bằng class responsive hoặc `@media (min-width: 768px)` trong `<style scoped>`). Áp dụng tương tự cho lớp phủ drag-and-drop (`L89`).
    2. **Vị trí dọc & `z-index`**:
       - Khi `selectable || embed` (modal picker hoặc iframe/popup nhúng): `top-0 z-50` để phủ toàn màn hình.
       - Khi `!selectable && !embed` (trang độc lập `/admin/files` nằm trong `Authenticated.vue`): `top-[57px] md:top-0 z-20 md:z-30`. Giá trị `57px` khớp đúng chiều cao thanh header mobile của `Authenticated.vue` (`p-2` 16px + button `p-2` 16px + svg `h-6` 24px + `border-b` 1px), và `z-20` nằm dưới backdrop `z-30` / drawer `z-40` của `SidebarMobile.vue`, giữ nguyên khả năng mở sidebar trên mobile mà không cần sửa `Authenticated.vue`.
  - Xóa hoàn toàn CSS hack `.topbar { position: absolute; height: var(--topbar-height); }` và `.topbar + div { position: fixed; ... left: var(--sidebar-width); }`. Thay bằng khối header `shrink-0` nằm tự nhiên trong luồng `flex-col` và khối thân `flex flex-1 min-h-0 overflow-hidden relative`.
- **Lý do & Phương án thay thế đã cân nhắc**: Có thể sửa `Pages/FileManager.vue` dùng layout riêng, nhưng sẽ làm mất sidebar điều hướng Admin chuẩn trên `/admin/files`. Dùng `flex-col` + `top-[57px] md:top-0` ngay trong `FileManager.vue` giải quyết dứt điểm mà giữ nguyên `Authenticated.vue` và `Pages/FileManager.vue`.

### Decision 2: Thiết kế lại Thanh Toolbar Responsive & Nhóm hành động Quiet UI
- **Hiện trạng (`FileManager.vue` L15–86)**: `<h1>` thiếu `whitespace-nowrap`, `<input>` cố định `w-[400px]`, và 7 nút `.btn` (`uppercase min-w-[6rem]`) xếp ngang không wrap.
- **Quyết định**:
  - Chia `.topbar` (`v-if="!embed"`) thành cấu trúc 2 lớp linh hoạt (`px-3 py-2.5 sm:px-6 lg:px-8 bg-white border-b border-gray-200 flex flex-col gap-2.5 xl:flex-row xl:items-center xl:justify-between`):
    - **Hàng tiêu đề & hành động chính**:
      - Trái: Nút quay lại `<ph-caret-left />` (khi `selectable`, kích thước chạm `min-w-[40px] min-h-[40px] -ml-2`) + `<h1 class="text-base sm:text-lg font-semibold text-gray-700 whitespace-nowrap shrink-0">`.
      - Phải (nhóm nút chính luôn sẵn sàng):
        - Nút mở cây thư mục `< lg`: `class="lg:hidden !min-w-0 px-2.5 h-9 btn-outline-primary whitespace-nowrap shrink-0"` (`<ph:folder-notch-open />` + label ngắn gọn hoặc icon + `sr-only`/text `"Thư mục"` tùy độ rộng `sm:inline`).
        - Nút `Tạo thư mục`: `whitespace-nowrap shrink-0` (trên `< sm` có thể giữ text ngắn gọn `Tạo thư mục` với padding gọn `!min-w-0 px-3` để không gãy dòng).
        - Nút `Chọn tệp`: `btn-primary whitespace-nowrap shrink-0 !min-w-0 px-3`.
    - **Hàng tìm kiếm & hành động ngữ cảnh**:
      - Ô tìm kiếm: `w-full sm:w-64 xl:w-72 py-2 px-3 border border-gray-300 rounded text-sm` (bỏ `w-[400px]`). Note: đồng bộ `v-model="searchInput"` hoặc giữ `@input="onChange"` và reset value khi `navigateToPath` / `selectedItem` reset `this.search = null`.
      - 5 hành động ngữ cảnh (`Đổi tên thư mục này`, `Đổi tên`, `Sao chép`, `Cắt`, `Dán`):
        - Trên `>= xl` (`1280px+`): hiển thị nhóm nút `flex items-center gap-2 shrink-0` với `whitespace-nowrap`.
        - Trên `< xl` (`375px`–`1279px`): gom thành một menu/popover `"Thao tác"` gọn gàng (hoặc thanh trượt/menu PrimeVue `Menu` popup anchored theo nút `Thao tác`) hiển thị số lượng clipboard nếu có (`Dán (N)`), giữ các nút luôn có `disabled` state chính xác. Điều này giúp thanh topbar trên Tablet `768px` và Mobile `375px` chỉ còn tối đa 2 hàng rất sạch sẽ, chuẩn *Quiet & High-Trust UI*, không spam icon thừa.
- **Chế độ `embed=true`**:
  - Khi `embed` truthy (`v-else` của toolbar chính), hiển thị thanh compact 1–2 hàng: nút mở Drawer cây thư mục (`lg:hidden`), nút `Chọn tệp` (`btn-primary`), nút `Tạo thư mục`, và ô tìm kiếm (`w-full sm:w-56`). Điều này sửa dứt điểm lỗi mất nút Upload & mất cây thư mục trên `< 1024px` trong TinyMCE/CKEditor.

### Decision 3: Drawer Cây Thư mục `< 1024px` (Tái sử dụng `TreeViewItem.vue`)
- **Hiện trạng**: `<aside class="hidden p-8 pb-16 overflow-y-auto bg-white border-l border-r border-gray-200 w-80 lg:block">` (`L100`).
- **Quyết định**:
  - Thêm state `showFolderDrawer: false` trong `FileManager.vue`.
  - Khi `< lg`, hiển thị slide-over drawer khi `showFolderDrawer === true`:
    - Backdrop `fixed inset-0 z-40 bg-black/40 lg:hidden` (`@click="showFolderDrawer = false"`).
    - Panel `fixed inset-y-0 left-0 z-50 w-72 max-w-[85vw] bg-white shadow-xl flex flex-col lg:hidden` (hoặc `top-[57px]` nếu `!selectable && !embed`? Thực tế `z-50` drawer nằm trên `FileManager` rất trực quan như modal drawer tạm thời, có header `"Thư mục"` + nút đóng `×` kích thước `40×40px`).
    - Tái sử dụng đúng `<Field v-if="tree && tree.length" :field="{ key: 'FileManager', ... }" />` để giữ nguyên đồng bộ `$bus.on('treeSelectedItemFileManager')` và `fileManagerContext`.
    - Trong `selectedItem(item)` (`L459`), set thêm `this.showFolderDrawer = false` để drawer tự đóng mượt mà ngay sau khi người dùng chọn thư mục trên Mobile/Tablet.
    - Trên `>= lg`, `<aside class="hidden lg:block w-72 xl:w-80 shrink-0 p-5 pb-16 overflow-y-auto bg-white border-r border-gray-200">` hiển thị cố định như trước (bỏ `embed` upload button lặp thừa ở `<aside>` hoặc giữ nếu muốn tương thích Desktop, vì thanh compact `embed` phía trên đã có nút `Chọn tệp` dùng chung cho mọi màn hình!).

### Decision 4: Lưới 2 cột Mobile, Tương tác Cảm ứng & `Thumbnail.vue`
- **Hiện trạng**:
  - `FileManager.vue` L167: `grid-cols-3 gap-4 lg:grid-cols-4 2xl:grid-cols-6`.
  - `FileManager.vue` L172, L190: thao tác menu chỉ có `@contextmenu.prevent`, mở thư mục chỉ có `@dblclick`, không có nút quay lên thư mục cha trong Breadcrumb.
  - `Thumbnail.vue` L20–33: link xem ảnh `w-[20px] h-[20px]` + nút xóa đều dùng `invisible group-hover:visible`.
- **Quyết định**:
  - **Grid**: Đổi sang `grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 2xl:grid-cols-6 gap-3 sm:gap-4`.
  - **Breadcrumb**: Thêm `flex-wrap gap-x-2 gap-y-1` cho `<nav>`, và thêm nút quay lên thư mục cha (`v-if="currentPath !== '/'"`, `@click="navigateUp"`, `:title="tt('models.files.go_up')"`, icon `<ph:arrow-bend-up-left />` + text ngắn `"Quay lại"`/`go_up` hoặc pill button gọn `min-h-[32px]`).
  - **Mở thư mục & Menu hành động trên từng thẻ**:
    - Thêm nút trigger menu ngữ cảnh (`ph:dots-three-vertical`) ở góc trên-trái mỗi thẻ (`absolute top-1.5 left-1.5 z-10 w-9 h-9 min-w-[36px] min-h-[36px] rounded-md bg-white/90 hover:bg-white text-gray-700 shadow-sm border border-gray-200/80 flex items-center justify-center opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100`, `@click.stop="openContextMenu($event, file)"`).
    - Đối với thẻ thư mục (`file.type === 'dir'`):
      - Bổ sung nút `"Mở"` nhỏ gọn ở góc dưới-trái (`@click.stop="navigateToFolder(file)"`, `min-h-[36px] px-2.5 opacity-100 lg:opacity-0 lg:group-hover:opacity-100`) để người dùng cảm ứng mở thư mục bằng 1 chạm mà không xung đột với chọn thư mục để Đổi tên/Sao chép/Cắt.
      - Riêng trong `onSelect(file)` (`L555–558`): khi `this.embed && !this.canSelectMultiple && file.type === 'dir'`, gọi `this.navigateToFolder(file)` thay vì `return` vô tác dụng.
  - **`Thumbnail.vue`**:
    - Thay `invisible ... group-hover:visible w-[20px] h-[20px]` trên thẻ `<a>` bằng `opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity w-9 h-9 min-w-[36px] min-h-[36px] rounded-md bg-black/75 hover:bg-black text-white flex items-center justify-center top-1.5 right-1.5` (giữ `@click.stop`).
    - Thay `invisible ... group-hover:visible` trên nút Xóa (`<Button>`) bằng `opacity-100 lg:opacity-0 lg:group-hover:opacity-100 lg:group-focus-within:opacity-100 transition-opacity right-1.5 bottom-1.5 !min-w-0 min-h-[36px] px-2.5 btn-white btn-sm shadow-sm` (giữ `@click.stop="$emit('remove', file)"`).
    - Do `Thumbnail.vue` cũng được dùng trong `InputUpload.vue`, góc `top-1.5 right-1.5` và `bottom-1.5 right-1.5` giữ bố cục hoàn toàn sạch sẽ trong cả `InputUpload.vue` lẫn `FileManager.vue` (không đè lên nút `top-1.5 left-1.5` của `FileManager.vue`).
  - **Bottom Selection Bar & Modals**:
    - Thanh chọn nhiều (`L213–221`): `absolute bottom-0 left-0 right-0 z-20 flex items-center justify-between sm:justify-center gap-3 w-full min-h-[4rem] px-4 pt-2.5 pb-[calc(0.625rem+env(safe-area-inset-bottom))] bg-white border-t border-gray-200 shadow-sm`, kèm `pb-24` trên `<main>` khi thanh này hiển thị.
    - Scroll listener (`L373–394`): chuyển `document.querySelector('.group-image-admin')` sang `this.$refs.scrollContainer` có guard `if (scrollContainer)` để tránh lỗi null và tránh đụng độ DOM nếu nhiều instance cùng mount.
    - Dialogs (`L222–271`): dùng `:header="tt('models.files.add_folder')"`, `:modal="true"`, `:draggable="false"`, `:style="{ width: '32rem', maxWidth: '95vw' }"`, `:breakpoints="{ '640px': '95vw' }"`, kiểm tra `folderForm.name?.trim()` trước khi gửi `createFolder`.

## Risks / Trade-offs

- **Chiều cao thanh Header Mobile của `Authenticated.vue` cố định `57px`** → Nếu sau này `Authenticated.vue` thay đổi padding header mobile, `top-[57px]` có thể lệch vài pixel. Mitigation: ghi rõ giá trị khớp với `Authenticated.vue` L8–15 (`p-2` + `p-2` + `h-6` + `border-b`) và dùng `min-h-0` bên trong `flex-col` để không tràn khung; kiểm chứng trực tiếp bằng Playwright ở `375×667` và `390×844`.
- **Hiển thị nút hành động trực tiếp trên Thumbnail (`opacity-100` dưới `< lg`)** → Trên màn hình nhỏ, nút Xóa/Mở tab mới chiếm một góc nhỏ của ảnh xem trước. Mitigation: giữ kích thước gọn `32px` với nền mờ nhẹ (`bg-white/95` / `bg-black/75`), đặt sát góc ngoài để không che tâm ảnh, và giữ `lg:opacity-0 lg:group-hover:opacity-100` trên Desktop như thiết kế gốc.
- **Thao tác trong iframe TinyMCE (`CustomEditor.vue`) ở màn hình `< 640px`** → TinyMCE đã đặt `.tox-dialog { width: 90vw !important; height: 90vh !important; }`. Nếu kiểm tra Playwright cho thấy thanh footer/nội dung iframe bị hẹp hoặc cần tinh chỉnh CSS `.tox-dialog` trên mobile, chỉ điều chỉnh cục bộ CSS dialog trong `CustomEditor.vue` khi có bằng chứng đo được; mặc định ưu tiên giữ nguyên `CustomEditor.vue`.

## Migration Plan

- Không có migration DB hoặc thay đổi dữ liệu.
- Triển khai code ở phase `/opsx-apply` -> biên dịch Backend (`yarn backend:build`) -> kiểm chứng Playwright Visual QA trên `/admin/files`, modal picker trong form (`InputUpload`), và `/admin/files?embed=true&select-multiple=true` tại các viewport `1440×900`, `1024×768`, `768×1024`, `390×844`, `375×667`.
- Rollback chỉ cần hoàn nguyên 2 file `FileManager.vue` và `Thumbnail.vue` rồi chạy lại `yarn backend:build`.
