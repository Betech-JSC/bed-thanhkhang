# Design: Kiến Trúc Dropdown Danh Mục Sản Phẩm (Desktop Popover & Mobile Accordion)

## Context
Thanh điều hướng Header (`Header.vue`) hiện tại quản lý 5 tab thông qua thuộc tính `menus`. Tab "Sản phẩm" được gán `type: 'products'`, có biểu tượng chevron xoay 180° khi hover, nhưng mảng `subMenu` bên trong đang rỗng (`[]`). Hệ quả là không có danh mục nào được hiển thị khi người dùng tương tác.
Dữ liệu danh mục sản phẩm đã có sẵn trong bảng `product_categories` với 4 danh mục active chuẩn hóa song ngữ (Space Decor, Keychains & Accessories, Decor & Lifestyle, Custom Manufacturing), được quản lý bởi model `App\Models\Product\ProductCategory`.

Tài liệu này xác định giải pháp kỹ thuật kết nối backend và frontend nhằm mang lại trải nghiệm mở rộng danh mục mượt mà, chuẩn Dark Tech trên cả Desktop và Mobile.

## Goals / Non-Goals

**Goals:**
- Chia sẻ động danh sách 4 danh mục sản phẩm từ Database qua Inertia shared data, cache 1 giờ và tự động làm mới khi có thay đổi trong CMS.
- Thiết kế Popover Dropdown Desktop với kỹ thuật **Hover Bridge** chống chập chờn (flickering), tuân thủ bảng màu Dark Tech (#1A1F36, #30374F, #2970FF).
- Triển khai Accordion mở rộng trong Mobile Drawer với hiệu ứng trượt êm và touch target tối thiểu 44px.
- Đồng bộ hóa 2 chiều giữa liên kết danh mục và trang Catalog (`Product/Index.vue`) thông qua URL query parameter `?category={slug}`.
- Hỗ trợ đa ngôn ngữ hoàn chỉnh (VI & EN).

**Non-Goals:**
- Không sửa đổi schema database hay chạy migration mới (tận dụng nguyên vẹn cấu trúc `product_categories`).
- Không làm thay đổi giao diện hoặc logic của 4 tab điều hướng còn lại ("Trang chủ", "Dịch vụ", "Giới thiệu", "Quy trình").
- Không cài đặt thêm thư viện UI bên ngoài (chỉ dùng Tailwind CSS và Vue Transitions sẵn có).

## Decisions

### 1. Cơ Chế Chia Sẻ Danh Mục Sản Phẩm Qua Middleware (Backend)
- **Vị trí**: `app/Http/Middleware/HandleInertiaFrontendRequests.php`.
- **Giải pháp**: Tích hợp truy vấn danh mục sản phẩm vào khối `Cache::remember('inertia_layout_shared_data_' . $locale, 3600, ...)`:
  ```php
  $productCategories = ProductCategory::query()
      ->active()
      ->sortByPosition()
      ->get()
      ->map(function ($cat) {
          return [
              'id' => $cat->id,
              'title' => $cat->title,
              'slug' => $cat->seo_slug ?? $cat->slug,
              'url' => route('products.index', ['category' => $cat->seo_slug ?? $cat->slug]),
          ];
      });
  ```
- **Shared Props**: Trả về trực tiếp trong mảng `$share`:
  ```php
  'product_categories' => $layoutData['product_categories'] ?? [],
  ```
- **Lý do chọn**: Header là thành phần xuất hiện ở tất cả các trang. Chia sẻ qua Inertia Middleware đảm bảo dữ liệu luôn sẵn sàng ngay khi render trang đầu tiên (SSR/Client), không phát sinh request AJAX phụ gây giật lag giao diện.
- **Cache Invalidation**: Bổ sung xóa cache `inertia_layout_shared_data_vi` và `_en` trong sự kiện `booted()` của `ProductCategory`.

---

### 2. Kỹ Thuật Hover Bridge & Popover Giao Diện Dark Tech (Desktop)
- **Cấu trúc vùng đệm Hover Bridge**:
  Để tránh việc chuột di chuyển từ tab Header (h-47px) xuống Popover bị rớt qua khe hở làm mất trạng thái hover, cấu trúc DOM sử dụng container bao bọc với lớp đệm tàng hình:
  ```html
  <div
      class="relative h-[47px] flex items-center"
      @mouseenter="menuSelected = menu"
      @mouseleave="menuSelected = null"
  >
      <!-- Nav Tab Link -->
      <Link :href="menu.slug" class="...">...</Link>

      <!-- Hover Bridge Wrapper & Popover -->
      <transition name="dropdown-fade">
          <div
              v-show="menuSelected === menu && menu.subMenu.length > 0"
              class="absolute top-full left-0 pt-2 z-[1010]"
          >
              <!-- Panel Dark Tech chính -->
              <div class="w-[280px] bg-[#1A1F36] border border-[#30374F] rounded-xl shadow-2xl p-2 flex flex-col gap-1 backdrop-blur-md">
                  <!-- Category Items -->
                  <Link
                      v-for="sub in menu.subMenu"
                      :key="sub.id"
                      :href="sub.url"
                      class="px-3 py-2.5 rounded-lg text-sm font-medium text-[#B9C0D4] hover:text-[#FAFAFA] hover:bg-[#404968] flex items-center justify-between transition-colors group"
                  >
                      <span>{{ sub.title }}</span>
                      <svg class="w-4 h-4 text-[#84ADFF] opacity-0 group-hover:opacity-100 transition-opacity" ... />
                  </Link>
                  <!-- Divider & View All Link -->
                  <div class="pt-1 mt-1 border-t border-[#30374F]">
                      <Link
                          :href="route('products.index')"
                          class="px-3 py-2 rounded-lg text-xs font-semibold text-[#84ADFF] hover:text-[#FAFAFA] hover:bg-[#404968]/60 flex items-center justify-between transition-colors"
                      >
                          <span>{{ tt('Xem tất cả sản phẩm') }}</span>
                          <span>&rarr;</span>
                      </Link>
                  </div>
              </div>
          </div>
      </transition>
  </div>
  ```
- **Bảng màu Dark Tech**:
  - Nền Popover: `#1A1F36` (nổi bật trên nền Header `#111322`).
  - Viền khung: `#30374F`.
  - Item Hover: `#404968` với chữ sáng `#FAFAFA`.
  - Accent / Icon: `#84ADFF` và `#2970FF`.

---

### 3. Tương Tác Accordion Trong Mobile Drawer
- **State**: Bổ sung `isProductAccordionOpen: false` trong `data()` của `Header.vue`.
- **Cấu trúc Accordion**:
  - Tiêu đề tab "Sản phẩm" hiển thị flex row: nhấp vào dòng chính chuyển hướng đến Catalog, hoặc nhấp vào nút icon chevron bên phải để toggle mở rộng/thu gọn Accordion.
  - Danh sách con trượt xuống với hiệu ứng CSS mượt mà:
    ```html
    <div v-show="isProductAccordionOpen" class="pl-4 pr-1 py-1 space-y-1">
        <Link
            v-for="sub in productCategories"
            :key="sub.id"
            :href="sub.url"
            @click="closeDrawer"
            class="min-h-[44px] px-3 py-2.5 rounded-lg flex items-center text-sm font-normal text-[#B9C0D4] hover:text-white hover:bg-dark-surface transition-colors"
        >
            {{ sub.title }}
        </Link>
    </div>
    ```
  - Đáp ứng chuẩn touch target tối thiểu 44px theo Web Interface Guidelines.

---

### 4. Đồng Bộ Hóa Query Param Với Bộ Lọc Trang Sản Phẩm (`Product/Index.vue`)
- **Khởi tạo và lắng nghe URL**:
  Mở rộng phương thức `initSearchFromUrl()` trong `Product/Index.vue`:
  ```javascript
  initSearchFromUrl() {
      if (typeof window !== 'undefined') {
          const params = new URLSearchParams(window.location.search);
          const catVal = params.get('category');
          if (catVal && catVal.trim()) {
              this.filters.category = catVal.trim();
          }
          const searchVal = params.get('search') || params.get('keyword') || params.get('q');
          if (searchVal && searchVal.trim()) {
              this.filters.search = searchVal.trim();
          }
      }
  }
  ```
- **Khớp danh mục**:
  Đảm bảo `filteredProducts` đối chiếu giá trị `filters.category` với cả `slug`, `seo_slug`, `id` của sản phẩm và danh mục để bộ lọc hoạt động chính xác 100%.

## Risks / Trade-offs

| Rủi ro tiềm ẩn (Risk) | Giải pháp kiểm soát (Mitigation) |
|---|---|
| **Hover flickering**: Người dùng di chuyển chuột từ tab xuống popover bị đóng đột ngột do khoảng trống 4px giữa tab và panel. | Sử dụng container bao bọc `pt-2` liên tục tạo Hover Bridge tàng hình nối liền 2 khối. |
| **Cache tồn đọng dữ liệu cũ**: Admin đổi tên danh mục trong CMS nhưng ngoài Header không đổi. | Tự động gọi `Cache::forget('inertia_layout_shared_data_' . $locale)` trong `ProductCategory::booted()` khi model được saved hoặc deleted. |
| **Lệch ngôn ngữ giữa VI và EN**: Query param slug tiếng Anh không lọc được sản phẩm trên giao diện tiếng Việt và ngược lại. | Truyền đúng slug theo locale hiện tại (`$cat->seo_slug ?? $cat->slug`) và hàm lọc trên frontend so sánh theo cả danh mục cha/con tương ứng. |
| **Trùng lặp sự kiện đóng drawer**: Nhấp vào danh mục con nhưng drawer không đóng. | Đính kèm `@click="closeDrawer"` vào tất cả các liên kết bên trong Mobile Drawer. |

## Migration Plan
1. Không yêu cầu chạy migration cơ sở dữ liệu.
2. Cập nhật mã nguồn PHP Middleware và Vue components.
3. Chạy `yarn frontend:build` để biên dịch gói frontend production.
4. Chạy kiểm thử E2E Playwright để xác thực toàn bộ luồng Desktop và Mobile.
