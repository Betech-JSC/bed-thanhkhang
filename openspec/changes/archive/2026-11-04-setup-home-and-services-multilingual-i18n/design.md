# Design Architecture: setup-home-and-services-multilingual-i18n

## 1. Tổng quan Kiến trúc

Giải pháp đa ngôn ngữ toàn diện cho Trang chủ và Hệ thống Dịch vụ vận hành trên sự kết hợp đồng bộ giữa 3 lớp:
1. **Lớp Định tuyến & SEO (Backend Laravel)**:
   - Sử dụng `codezero/laravel-localized-routes` để phân nhánh URL theo locale (`vi` không tiền tố, `en` có tiền tố `/en`).
   - `HomeController` và `ServiceController` lưu cache phân biệt theo `current_locale()`.
   - Localized SEO metadata tự động sinh tiêu đề và mô tả tiếng Anh/Việt.
2. **Lớp Dữ liệu Đa ngữ (Database & Eloquent)**:
   - Tận dụng `Astrotomic\Translatable\Translatable` trait trên model `Service`.
   - Bản ghi `service_translations` lưu trữ bản dịch `title`, `description`, `slug`, `content` theo từng locale.
   - Cơ chế fallback an toàn: nếu thiếu bản dịch, trả về bản dịch mặc định của hệ thống.
3. **Lớp Giao diện & Trực quan (Vue 3 + Vue I18n + Ziggy)**:
   - Helper toàn cục `tt(text)` tích hợp trong Vue mixin tra cứu tức thì qua dictionary `resources/lang/{locale}/ui.php`.
   - Cơ chế phát hiện locale động qua Inertia Page Props (`this.$page.props.locale.current`).
   - `route(...)` tự động gắn tiền tố locale thông qua `routeLocaleName(name)`.

## 2. Luồng Xử lý Dữ liệu

```
[Request: /en/services]
       │
       ▼
[Laravel Localized Routes] -> Đặt current_locale() = 'en'
       │
       ▼
[ServiceController@index]
       │
       ├── Service::active()->sortByPosition()->get()
       │     └─ Eloquent nạp service_translations WHERE locale = 'en'
       │
       ├── Cache::remember('services_index_en', ...)
       │
       ▼
[Inertia::render('Services/Index', ['services' => $services])]
       │
       ▼
[Vue 3 Component: Services/Index.vue]
       ├── currentLocale = computed(() => page.props.value?.locale?.current || 'vi')
       ├── defaultServices có nhánh song ngữ cho vi & en
       ├── tt('CÁC DỊCH VỤ IN ẤN TẠI HH MAKER') -> 'HH MAKER 3D PRINTING SERVICES'
       └── ServiceBlock hiển thị thẻ dịch vụ và link tới /en/services/{slug}
```

## 3. Quản lý Từ Điển UI (`resources/lang/{vi,en}/ui.php`)
Các chuỗi UI tĩnh được phân thành 2 nhóm lớn trong từ điển:
- **Nhóm Trang chủ**: Eyebrow, Hero Slogan, CTAs, Founder Story, 5 Bước quy trình, Form liên hệ tư vấn.
- **Nhóm Dịch vụ**: Badge, Tiêu đề, Tags kỹ thuật, Bảng 2 cột Chuẩn bị vs Hoàn thiện, Mục lục TOC, 4 Bước thiết kế, Checklist thông số, Dịch vụ liên quan, Banner CTA.

## 4. An Toàn Dữ Liệu (Strict Safe Guard)
- Tuyệt đối KHÔNG xóa, drop, truncate bảng `services` hay `service_translations`.
- Sử dụng phương thức `updateOrInsert` hoặc tinker an toàn với điều kiện `['service_id' => $id, 'locale' => 'en']`.
