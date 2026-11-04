# Kiến Trúc 8 Trụ Cột CMS Dynamic A–Z (`CMS 8-Pillars Architecture Reference`)

> Tài liệu tham chiếu kỹ thuật chuẩn cho `/opsx-cms-sync`, đúc kết từ các bản nâng cấp và sửa lỗi thực chiến trên hệ thống LUXI (`a42f960`, `4230a6f`, `780392e`, `18ad5f9`, `b84b82f`). Mọi Worker Agent khi thực thi các gói Handoff Prompts của `/opsx-cms-sync` phải tuân thủ đúng các mẫu thiết kế dưới đây.

---

## Trụ Cột 1: Admin CMS Restyling, Retina Favicon & Executive Dashboard (`Schema::hasTable` Guard)

### 1.1. Bảo vệ truy vấn Dashboard bằng `Schema::hasTable()`
Trong `app/Http/Controllers/Backend/DashboardController.php`, mọi khối thống kê KPI (`products`, `product_categories`, `contacts`, `posts`, `services`) bắt buộc phải bọc trong `Schema::hasTable(...)` và `try / catch` để trang Dashboard không bao giờ bị lỗi 500 trên môi trường mới hoặc khi một module không được bật:

```php
$totalProducts = 0;
$activeProducts = 0;

try {
    if (Schema::hasTable('products')) {
        $totalProducts = (int) Product::count();
        $activeProducts = (int) Product::where('status', Product::STATUS_ACTIVE)->count();
    }
} catch (\Throwable $e) {
    report($e);
}
```

### 1.2. Tinh gọn Menu Sidebar Admin theo đúng phạm vi Figma
Trong `resources/Backend/js/Components/SidebarMain.vue`:
- Đối chiếu danh sách module thực tế xuất hiện trên thiết kế Figma của dự án.
- Đưa vào danh sách `[-] BỎ / ẨN` các menu không dùng đến (ví dụ: Tuyển dụng `jobs`/`applies`, Môi trường học `learning_environments`, Giải thưởng `prizes`, Đơn hàng giỏ hàng `orders` nếu dự án là mô hình nhận báo giá tư vấn may đo `contacts`).

### 1.3. Đồng bộ bộ Favicon Retina & Thương hiệu Admin
- Đảm bảo `resources/Backend/views/app.blade.php` và `resources/Frontend/views/app.blade.php` nạp đầy đủ `/favicon.ico`, `/favicon-32x32.png`, `/favicon-16x16.png`, `/apple-touch-icon.png` và `/site.webmanifest` đúng nhận diện thương hiệu mới.

---

## Trụ Cột 2: Media Manager Reload Fix (`FileManager.vue` & `Thumbnail.vue`)

### 2.1. Khắc phục lỗi xóa file cuối cùng nhưng UI không làm mới trong `FileManager.vue`
Trong `packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue`:
- **Lỗi gốc thường gặp**: Khi gọi `getFiles()` ở trang 1 (`requestPage <= 1`) mà thư mục trống hoặc vừa xóa file cuối cùng (`normalizedNewFiles.length === 0`), code cũ dừng lại hoặc không gán lại `this.data.files = []` trước khi đặt `this.fetchData = false`, khiến ảnh vừa xóa vẫn hiển thị trên giao diện.
- **Mẫu code chuẩn bắt buộc**:

```javascript
getFiles(params = {}, loadTree = false) {
    if (this.fetchData) {
        const requestPage = Number(params.page ?? this.page ?? 1)
        this.$axios
            .get(
                this.route('admin.files.index', {
                    page: 1,
                    limit: this.limit,
                    search: null,
                    path: this.currentPath,
                    ...params,
                })
            )
            .then((res) => {
                let files = this.data.files
                let new_files = res.data.files ? res.data.files : null

                if (new_files && !Array.isArray(new_files)) {
                    new_files = Object.values(new_files)
                }
                if (files && !Array.isArray(files)) {
                    files = Object.values(files)
                }

                const normalizedNewFiles = Array.isArray(new_files) ? new_files : []

                if (requestPage <= 1) {
                    this.data = {
                        tree: res.data.tree,
                        directories: res.data.directories || [],
                        files: normalizedNewFiles,
                    }
                    if (!this.tree || loadTree) {
                        this.tree = res.data.tree
                    }
                    if (normalizedNewFiles.length === 0) {
                        this.fetchData = false
                    }
                } else if (normalizedNewFiles.length === 0) {
                    this.fetchData = false
                } else {
                    files = [...(files || []), ...normalizedNewFiles]
                    this.data = {
                        tree: res.data.tree,
                        directories: res.data.directories || [],
                        files: files,
                    }
                    if (!this.tree || loadTree) {
                        this.tree = res.data.tree
                    }
                }
            })
    }
},
onRemove(file) {
    this.$axios
        .post(this.route('admin.files.destroy', { id: 0 }), {
            files: [file],
        })
        .then((response) => {
            if (response.status === 200) {
                this.selectedFiles = (this.selectedFiles || []).filter(
                    (selected) =>
                        (selected.path && selected.path !== file.path) ||
                        (selected.static_url && selected.static_url !== file.static_url)
                )
                this.page = 1
                this.fetchData = true
                this.getFiles({ page: 1 }, true)
            }
        })
}
```

### 2.2. Chặn nổi bọt sự kiện (`Event Bubbling`) khi bấm nút Xóa trên `Thumbnail.vue`
Trong `packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue`:
- Nút xóa nằm bên trong thẻ cha có `@click="onSelect(file)"`. Bắt buộc dùng `@click.stop` để khi Admin bấm **Xóa** không bị kích hoạt nhầm hành động chọn ảnh đóng popup:

```vue
<Button
    class="absolute invisible right-1 bottom-1 group-hover:visible btn-white btn-sm"
    :label="tt('models.files.delete')"
    @click.stop="$emit('remove', file)"
/>
```

---

## Trụ Cột 3: Mở Rộng Schema JSON, Đồng Bộ `product_ref_categories` & Tùy Biến Form CMS Theo Figma

### 3.1. Đồng bộ 2 chiều giữa `category_id` và bảng trung gian `product_ref_categories`
Trong `app/Models/Product/Product.php` (hoặc model tương đương):
- Khi Admin chọn danh mục chính `category_id` hoặc mảng `categories` trên Form CMS, hook `saved` trong `booted()` phải đồng bộ cả cột `category_id` lẫn bảng pivot `product_ref_categories` để mọi câu truy vấn `where('category_id', ...)` và `whereHas('categories', ...)` trên Trang chủ / Trang danh mục đều trả về kết quả nhất quán.

### 3.2. Mở rộng thuộc tính bộ lọc (`filter_attributes`) và cấu hình trang chi tiết (`pdp_metadata`)
- Sử dụng các cột JSON có `casts` sang `array` (như `filter_attributes`, `pdp_metadata`, `space`, `operation`, `features`, `color_slugs`, `fabric_swatches`, `operating_systems`, `accessories`, `usage_guide`, `pricing_info`, `space_inspiration`) để Admin có thể chỉnh sửa toàn bộ các tab, thông số kỹ thuật, mẫu vải và câu hỏi/hướng dẫn trên trang Chi tiết sản phẩm (PDP) mà không còn bất kỳ khối văn bản nào bị hardcode trên Vue.

### 3.3. Lược bỏ các trường không có trong thiết kế Figma
- Đối chiếu Form CMS (`resources/Backend/js/Pages/Products/Products/Form.vue`, `Posts/Form.vue`...) với thiết kế Figma:
  - Nếu Figma không hiển thị giá bán lẻ cố định mà dùng nút *"Nhận báo giá theo kích thước"*, đưa trường `price` vào bảng `[-] BỎ / ẨN` trên Form Admin để tránh gây nhầm lẫn cho biên tập viên.

---

## Trụ Cột 4: Chuẩn Hóa Dữ Liệu Leads `Contact::normalizeLeadData()`

Trong `app/Models/Contact.php`:
- Mọi biểu mẫu trên Frontend (Modal báo giá nhanh PDP `product_quote_modal`, Modal đặt lịch tư vấn `home_consultation_modal`, Form trang Liên hệ `contact_page`) đều gửi dữ liệu vào cột JSON `contacts.data`.
- Hàm `Contact::normalizeLeadData(array $rawData, ?string $contactType = null): array` tự động chuẩn hóa trong `static::creating` và `static::updating`:
  1. Phân loại chính xác `lead_type` (`quote`, `consultation`, `general`) và `form_source`.
  2. Chuẩn hóa kích thước `width` × `height` thành chuỗi `dimensions` có đơn vị (`cm`).
  3. Lưu song song **khóa máy tiếng Anh** (`name`, `phone`, `email`, `address`, `product_title`, `fabric_code`, `operating_system`, `accessory`, `dimensions`, `quantity`, `note`, `lead_type_label`, `form_source_label`) VÀ **khóa hiển thị tiếng Việt** (`Loại yêu cầu`, `Nguồn gửi`, `Họ và tên`, `Số điện thoại`, `Địa chỉ lắp đặt`, `Sản phẩm`, `Mã vải`, `Kích thước`...) để cả trang danh sách Admin lẫn hệ thống gửi Email thông báo đều đọc được 100%.

---

## Trụ Cột 5: Cấu Hình Thương Hiệu Toàn Cục `Config::getLuxiBrandSettings()` & Bóc Tách `map_iframe` -> `props.global`

### 5.1. Đồng bộ 2 chiều khóa cấu hình trong `Config` & `ConfigController`
Trong `app/Models/Config.php` và `app/Http/Controllers/Backend/ConfigController.php`:
- Cung cấp hàm trung tâm (như `Config::getLuxiBrandSettings()`) hợp nhất giữa giá trị trong bảng `settings` (`group = 'general'`) và bộ giá trị mặc định chuẩn của thương hiệu.
- Đồng bộ 2 chiều các cặp khóa alias:
  - `company_name` <-> `general_business_name`
  - `site_name` <-> `general_site_name`
  - `address` / `showroom_address` <-> `general_address`
  - `phone` / `hotline` <-> `general_phone`
  - `email` <-> `general_email`
  - `facebook`, `zalo`, `tiktok`, `instagram`, `youtube`, `messenger` <-> `social_*`
- **Xử lý thông minh trường `map_iframe`**: Khi Admin dán nguyên thẻ `<iframe src="https://www.google.com/maps/embed?..." ...></iframe>` hoặc dán URL trực tiếp, hệ thống (ở Controller hoặc Component `Contact.vue`/`Footer.vue`) phải tự động bóc tách lấy URL trong `src="..."` để tránh lỗi render iframe lồng nhau.

### 5.2. Chia sẻ toàn cục qua `HandleInertiaFrontendRequests.php`
Trong `app/Http/Middleware/HandleInertiaFrontendRequests.php`:
- Chia sẻ `'global' => Config::getLuxiBrandSettings()` vào Inertia shared props để Header, Footer, Floating Contact Buttons, Modal Tư vấn, Trang Giới thiệu và Trang Liên hệ luôn nhận được dữ liệu mới nhất ngay khi Admin bấm Lưu.

---

## Trụ Cột 6: Idempotent Auto-Seeder & Bộ Lọc Dữ Liệu Mẫu Ngành Cũ (`Legacy Sample Guard`)

### 6.1. Nguyên tắc Auto-Seed An Toàn (100% Idempotent)
- Tuyệt đối không dùng `truncate()` hay `migrate:fresh` trong Seeder hoặc pipeline CI/CD.
- Sử dụng `updateOrCreate` hoặc `firstOrCreate` theo `slug` / `sku` / `email` kết hợp cờ bảo vệ tĩnh `protected static bool $isSeeding = false;` để tránh vòng lặp sự kiện `saved` khi khởi tạo dữ liệu lần đầu (`RoleSeeder`, `LuxiProductCatalogSeeder`, `Post::ensureLuxiPostsSeeded()`, `PostCategory::ensureDefaultLuxiCategories()`, `Policy::ensureDefaultLuxiPoliciesSeeded()`, `Config::seedDefaultLuxiSettings()`).

### 6.2. Bộ lọc chặn dữ liệu mẫu ngành cũ (`Legacy Sample Guard`)
- Khi tái sử dụng hoặc nâng cấp từ một database base CMS cũ, cần có bộ lọc kiểm tra từ khóa tàn dư của các dự án mẫu cũ (ví dụ: `/vận chuyển|vận tải|kho lạnh|hải quan|container|logistics|funky|utrend|hiphop|dance|khóa học|one piece/iu`) nhưng **tuyệt đối không chặn các tiêu đề hợp lệ mới do Admin tạo** (chỉ chặn đúng từ khóa rác ngành cũ, không ép bài viết mới phải chứa từ khóa cố định).

---

## Trụ Cột 7: Hợp Đồng Khóa Cache & Invalidation Tức Thì (`Cache Key Contract`)

> ⚠️ **ĐÂY LÀ LỖI PHỔ BIẾN NHẤT KHI ĐỒNG BỘ CMS (điển hình từ commit `b84b82f`)**:
> Frontend Controller lưu cache bằng khóa `posts_index_vi` và `post_show_{slug}_vi`, nhưng hàm xóa cache trong Model chỉ xóa `posts_index_v3_vi` hoặc quên gọi hàm xóa cache khi lưu danh mục `PostCategory` -> Dẫn đến Admin sửa trong CMS xong, DB đã đổi nhưng ngoài giao diện khách hàng vẫn hiện dữ liệu cũ suốt 1 giờ!

### Quy tắc bắt buộc cho Hợp đồng Khóa Cache:
1. **Liệt kê đầy đủ cặp khóa Đọc/Ghi (`Cache::remember` / `Cache::put`) và Xóa (`Cache::forget`)**:
   - **Bài viết (`Post` & `PostCategory`)**:
     - Khi ghi ở `Frontend\PostController`: `posts_index_{locale}`, `posts_index_v3_{locale}`, `post_show_{slug}_{locale}`, `related_posts_{id}_{locale}`.
     - Khi xóa ở `Post::clearPostCaches()` và `PostCategory::clearPostCategoryCaches()`: Bắt buộc `Cache::forget` toàn bộ các khóa trên cho mọi `locale` (`vi`, `en`) và cho cả `slug` cũ lẫn `slug` mới.
   - **Chính sách (`Policy`)**:
     - Khi xóa ở `Policy::clearPolicyCaches()`: Bắt buộc xóa `inertia_layout_shared_data_{locale}`, `policies_index_{locale}`, `luxi_fe_policies_list_{locale}`, `policies_list_{locale}`, `luxi_fe_policies_list_v1`.
   - **Cấu hình thương hiệu (`Config` / `Setting`)**:
     - Khi xóa ở `ConfigController::clearBrandCaches()`: Bắt buộc xóa `settings()->group('general')->flushCache()`, `inertia_layout_shared_data_{locale}`, `luxi_home_page_data_{locale}`, `luxi_home_page_data_v2_{locale}`, `history_index_{locale}`, `agencies_index_v3_{locale}`, `agencies_index_v4_{locale}`.
2. **Sử dụng Chữ Ký Động (`Dynamic Cache Signature`) cho Trang Chủ**:
   - Trong `HomeController::resolveHomeCacheSignature()`, tạo mã băm `md5` ngắn (12 ký tự) từ `max(updated_at)` + `count()` của `ProductCategory`, `Product`, `Slider` và các trường `brandSettings` trọng yếu. Nhờ đó, bất kỳ thay đổi nào trong DB cũng tự động tạo cache key mới ngay lập tức mà không sợ sót lệnh `Cache::forget`.

---

## Trụ Cột 8: Mẫu PHPUnit CMS-to-Frontend Integration Test Chuẩn

Mỗi module trong CMS sau khi đồng bộ phải có bài test Feature tự động kiểm chứng trọn vẹn 5 bước (không phá hủy dữ liệu hiện có nhờ khối `try / finally` khôi phục nguyên trạng):

```php
public function test_cms_module_update_syncs_immediately_to_frontend(): void
{
    // Bước 1: Làm nóng (Warm) cache ngoài Frontend
    $this->get('/goc-chia-se')->assertStatus(200);
    $this->assertTrue(Cache::has('posts_index_vi'));

    try {
        // Bước 2: Giả lập Admin gửi POST cập nhật tại /admin/<module>/store/{id}
        $updatedTitle = 'Tiêu Đề Cập Nhật Trực Tiếp Từ CMS ' . uniqid();
        $this->actingAs($admin, 'admin')
            ->post("/admin/posts/store/{$targetPost->id}", [
                'title' => $updatedTitle,
                'description' => $originalDescription,
                'content' => $originalContent,
                'status' => Post::STATUS_ACTIVE,
                'type' => Post::TYPE_POST,
            ])
            ->assertStatus(302);

        // Bước 3: Xác minh Cache đã bị xóa tức thì (Instant Cache Invalidation)
        $this->assertFalse(Cache::has('posts_index_vi'));

        // Bước 4: Xác minh Database và Frontend Inertia/JSON trả về dữ liệu mới ngay lập tức
        $this->assertDatabaseHas('post_translations', [
            'post_id' => $targetPost->id,
            'title' => $updatedTitle,
        ]);

        $frontendJson = $this->getJson('/goc-chia-se')->assertStatus(200)->json();
        $this->assertSame($updatedTitle, $frontendJson['posts']['data'][0]['title'] ?? null);
    } finally {
        // Bước 5: Khôi phục dữ liệu gốc an toàn tuyệt đối
        $this->actingAs($admin, 'admin')->post("/admin/posts/store/{$targetPost->id}", [
            'title' => $originalTitle,
            'description' => $originalDescription,
            'content' => $originalContent,
            'status' => Post::STATUS_ACTIVE,
            'type' => Post::TYPE_POST,
        ]);
    }
}
```
