# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma — Active Figma Pages (`About, Contact, Home, Layout, Posts, Process, Product, Products, Services`) (`/opsx-i18n-fe`)

- **Thư mục dự án**: `/Users/macbookpro2020/bed-hhmaker`
- **Thời gian quét**: `2026-11-04 16:16:52`
- **Phạm vi kiểm toán**: `Active Figma Pages (`About, Contact, Home, Layout, Posts, Process, Product, Products, Services`)` (`55` files `.vue` thuộc Figma, đã tự động bỏ qua `162` files boilerplate không dùng tới)
- **Ranh giới chạy song song**: `[ALLOWED: resources/Frontend/js/**, resources/Frontend/lang/**]` | `[LOCKED: app/**, config/**, resources/Backend/**]`
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<template>`**: `244` vị trí
- **Tổng số chuỗi Tiếng Việt fix cứng trong `<script>`**: `328` vị trí
- **Độ phủ từ điển `en.json` cho các khóa `tt(...)` đang dùng**: `0/17` khóa (`17` khóa chưa có trong `en.json`)

---

## 1. Trạng Thái Từ Điển `vi.json` / `en.json` & Nút Chuyển Ngôn Ngữ `VI | EN` Trên Header

| Hạng mục | Tọa độ / File | Hiện trạng thực tế | Trạng thái |
| :--- | :--- | :--- | :--- |
| **Từ điển Tiếng Việt (`vi.json`)** | `resources/Frontend/lang/vi.json` | `0` khóa | ⚠️ Chưa tạo file |
| **Từ điển Tiếng Anh (`en.json`)** | `resources/Frontend/lang/en.json` | `0` khóa (`0/17` khóa đang dùng đã dịch) | ⚠️ Thiếu 17 khóa đang dùng |
| **Nút chuyển đổi `VI \| EN` trên Header** | `resources/Frontend/js/Components/Header.vue` | Đã phát hiện logic chuyển ngôn ngữ trên Header | ✅ Đã có |

---

## 2. Thống Kê Chuỗi Tiếng Việt Fix Cứng Theo Từng File Thuộc Thiết Kế Figma

| File `.vue` (Trong phạm vi Figma) | Số chuỗi Tiếng Việt cứng ở `<template>` | Số chuỗi Tiếng Việt cứng ở `<script>` | Trạng thái |
| :--- | :--- | :--- | :--- |
| `resources/Frontend/js/Pages/Product/Index.vue` | `12` | `91` | ⚠️ Cần bọc `103` chuỗi |
| `resources/Frontend/js/Pages/Product/Show.vue` | `30` | `20` | ⚠️ Cần bọc `50` chuỗi |
| `resources/Frontend/js/Pages/Contact.vue` | `27` | `18` | ⚠️ Cần bọc `45` chuỗi |
| `resources/Frontend/js/Pages/About/Index.vue` | `30` | `0` | ⚠️ Cần bọc `30` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceArticleContent.vue` | `11` | `16` | ⚠️ Cần bọc `27` chuỗi |
| `resources/Frontend/js/Pages/Process/Index.vue` | `0` | `22` | ⚠️ Cần bọc `22` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue` | `1` | `20` | ⚠️ Cần bọc `21` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/HomeFooter.vue` | `20` | `0` | ⚠️ Cần bọc `20` chuỗi |
| `resources/Frontend/js/Components/Header.vue` | `17` | `1` | ⚠️ Cần bọc `18` chuỗi |
| `resources/Frontend/js/Components/DailyRewardPopup.vue` | `17` | `0` | ⚠️ Cần bọc `17` chuỗi |
| `resources/Frontend/js/Pages/Product/Components/ProductFilter.vue` | `8` | `7` | ⚠️ Cần bọc `15` chuỗi |
| `resources/Frontend/js/Components/Footer.vue` | `12` | `1` | ⚠️ Cần bọc `13` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceHero.vue` | `3` | `10` | ⚠️ Cần bọc `13` chuỗi |
| `resources/Frontend/js/Components/Product/ProductGallery.vue` | `3` | `9` | ⚠️ Cần bọc `12` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/ProcessSection.vue` | `0` | `12` | ⚠️ Cần bọc `12` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/ContactSection.vue` | `9` | `2` | ⚠️ Cần bọc `11` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/ServicesSection.vue` | `1` | `10` | ⚠️ Cần bọc `11` chuỗi |
| `resources/Frontend/js/Pages/Services/Show.vue` | `0` | `11` | ⚠️ Cần bọc `11` chuỗi |
| `resources/Frontend/js/Components/Product/RelatedProducts.vue` | `1` | `9` | ⚠️ Cần bọc `10` chuỗi |
| `resources/Frontend/js/Components/Product/SpecTable.vue` | `0` | `9` | ⚠️ Cần bọc `9` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/BrandStorySection.vue` | `3` | `5` | ⚠️ Cần bọc `8` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue` | `0` | `8` | ⚠️ Cần bọc `8` chuỗi |
| `resources/Frontend/js/Pages/Process/Components/ProcessFaq.vue` | `2` | `6` | ⚠️ Cần bọc `8` chuỗi |
| `resources/Frontend/js/Components/Product/VariantSelector.vue` | `3` | `4` | ⚠️ Cần bọc `7` chuỗi |
| `resources/Frontend/js/Pages/Process/Components/ProcessHero.vue` | `7` | `0` | ⚠️ Cần bọc `7` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/HeroSection.vue` | `1` | `5` | ⚠️ Cần bọc `6` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceBlock.vue` | `2` | `4` | ⚠️ Cần bọc `6` chuỗi |
| `resources/Frontend/js/Pages/Home/Index.vue` | `0` | `5` | ⚠️ Cần bọc `5` chuỗi |
| `resources/Frontend/js/Pages/Process/Components/ProcessStepper.vue` | `0` | `5` | ⚠️ Cần bọc `5` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceToc.vue` | `1` | `4` | ⚠️ Cần bọc `5` chuỗi |
| `resources/Frontend/js/Pages/Services/Index.vue` | `3` | `2` | ⚠️ Cần bọc `5` chuỗi |
| `resources/Frontend/js/Pages/Home/Components/HighlightMetrics.vue` | `0` | `4` | ⚠️ Cần bọc `4` chuỗi |
| `resources/Frontend/js/Pages/Posts/Show.vue` | `1` | `3` | ⚠️ Cần bọc `4` chuỗi |
| `resources/Frontend/js/Pages/Process/Components/ProcessCtaBanner.vue` | `3` | `0` | ⚠️ Cần bọc `3` chuỗi |
| `resources/Frontend/js/Pages/Product/Components/ProductCtaBanner.vue` | `3` | `0` | ⚠️ Cần bọc `3` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceCta.vue` | `3` | `0` | ⚠️ Cần bọc `3` chuỗi |
| `resources/Frontend/js/Layouts/Guest.vue` | `0` | `2` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Layouts/MainLayout.vue` | `0` | `2` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Pages/Posts/Index.vue` | `2` | `0` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Pages/Process/Components/ProcessStepItem.vue` | `2` | `0` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Pages/Product/Components/ProductCard.vue` | `1` | `1` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Pages/Product/Components/ProductPagination.vue` | `2` | `0` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Pages/Services/Components/ServiceRelated.vue` | `2` | `0` | ⚠️ Cần bọc `2` chuỗi |
| `resources/Frontend/js/Components/Base/BaseModal.vue` | `1` | `0` | ⚠️ Cần bọc `1` chuỗi |
| `resources/Frontend/js/Components/Icon/Phone.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/Jam/Breadcrumb.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/NotificationPopup.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/Paginate.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/PreHeader.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Components/SocialFixed.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Layouts/Empty.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/About.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Posts/Category.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Products/Index.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |
| `resources/Frontend/js/Pages/Products/Show.vue` | `0` | `0` | ✅ 100% Đã bọc i18n / Động |

---

## 3. Chi Tiết Tọa Độ `File:Dòng` Các Chuỗi Tiếng Việt Còn Fix Cứng

| Mã | Loại | Tọa độ `File:Dòng` | Chuỗi Tiếng Việt đang fix cứng | Hướng xử lý |
| :--- | :--- | :--- | :--- | :--- |
| `FE-I18N-01` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Base/BaseModal.vue:36` | `Đóng` | Đổi thành `:aria-label="tt('Đóng')"` và thêm vào `en.json` |
| `FE-I18N-02` | Thuộc tính `:title` | `resources/Frontend/js/Components/DailyRewardPopup.vue:12` | `Xem lịch sử hoạt động và ghi chú của Admin` | Đổi thành `:title="tt('Xem lịch sử hoạt động và ghi chú của Admin')"` và thêm vào `en.json` |
| `FE-I18N-03` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:28` | `Hoạt động &amp; Ghi chú` | Bọc `{{ tt('Hoạt động &amp; Ghi chú...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-04` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:44` | `Thông báo &amp; Hoạt động` | Bọc `{{ tt('Thông báo &amp; Hoạt động...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-05` | Thuộc tính `:title` | `resources/Frontend/js/Components/DailyRewardPopup.vue:50` | `Tạm ẩn popup (vẫn lưu lại để xem lại bất cứ lúc nào)` | Đổi thành `:title="tt('Tạm ẩn popup (vẫn lưu lại để xem lại bất cứ lúc nào)')"` và thêm vào `en.json` |
| `FE-I18N-06` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:68` | `Ghi chú từ Ban Quản trị:` | Bọc `{{ tt('Ghi chú từ Ban Quản trị:...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-07` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:79` | `✓ Đã hiểu và đóng thông báo` | Bọc `{{ tt('✓ Đã hiểu và đóng thông báo...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-08` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:92` | `Bạn được +{{ rewardPoints }} quả thưởng` | Bọc `{{ tt('Bạn được +{{ rewardPoints }} quả thưởng...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-09` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:95` | `đăng nhập ngày hôm nay` | Bọc `{{ tt('đăng nhập ngày hôm nay...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-10` | Thuộc tính `:alt` | `resources/Frontend/js/Components/DailyRewardPopup.vue:101` | `Quả thưởng` | Đổi thành `:alt="tt('Quả thưởng')"` và thêm vào `en.json` |
| `FE-I18N-11` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:111` | `MÌNH CẢM ƠN FUNKY!` | Bọc `{{ tt('MÌNH CẢM ƠN FUNKY!...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-12` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:121` | `Hoạt động gần đây` | Bọc `{{ tt('Hoạt động gần đây...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-13` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:129` | `Xem tất cả →` | Bọc `{{ tt('Xem tất cả →...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-14` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:161` | `dư: {{ act.balance_after }}` | Bọc `{{ tt('dư: {{ act.balance_after }}...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-15` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:169` | `Chưa có hoạt động nào được ghi nhận` | Bọc `{{ tt('Chưa có hoạt động nào được ghi nhận...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-16` | Thuộc tính `:alt` | `resources/Frontend/js/Components/DailyRewardPopup.vue:181` | `Quả nạp` | Đổi thành `:alt="tt('Quả nạp')"` và thêm vào `en.json` |
| `FE-I18N-17` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:182` | `Nạp thêm Quả` | Bọc `{{ tt('Nạp thêm Quả...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-18` | Text hiển thị `<template>` | `resources/Frontend/js/Components/DailyRewardPopup.vue:189` | `Tạm ẩn` | Bọc `{{ tt('Tạm ẩn...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-19` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:17` | `CÔNG TY IN ẤN 3D HH MAKER` | Bọc `{{ tt('CÔNG TY IN ẤN 3D HH MAKER...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-20` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:28` | `Trang chủ` | Bọc `{{ tt('Trang chủ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-21` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:33` | `Sản phẩm` | Bọc `{{ tt('Sản phẩm...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-22` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:38` | `Giới thiệu` | Bọc `{{ tt('Giới thiệu...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-23` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:43` | `Quy trình` | Bọc `{{ tt('Quy trình...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-24` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:52` | `Dịch vụ` | Bọc `{{ tt('Dịch vụ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-25` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:57` | `Thiết kế 3D` | Bọc `{{ tt('Thiết kế 3D...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-26` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:62` | `Gia công 3D` | Bọc `{{ tt('Gia công 3D...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-27` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:76` | `Liên hệ tư vấn` | Bọc `{{ tt('Liên hệ tư vấn...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-28` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:117` | `Chính sách bảo mật` | Bọc `{{ tt('Chính sách bảo mật...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-29` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:120` | `Điều khoản sử dụng` | Bọc `{{ tt('Điều khoản sử dụng...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-30` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Footer.vue:123` | `Chính sách Cookie` | Bọc `{{ tt('Chính sách Cookie...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-31` | Thuộc tính `:title` | `resources/Frontend/js/Components/Header.vue:85` | `Tìm kiếm` | Đổi thành `:title="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-32` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Header.vue:86` | `Tìm kiếm` | Đổi thành `:aria-label="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-33` | Thuộc tính `:alt` | `resources/Frontend/js/Components/Header.vue:90` | `Tìm kiếm` | Đổi thành `:alt="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-34` | Thuộc tính `:title` | `resources/Frontend/js/Components/Header.vue:102` | `Tiếng Việt` | Đổi thành `:title="tt('Tiếng Việt')"` và thêm vào `en.json` |
| `FE-I18N-35` | Thuộc tính `:title` | `resources/Frontend/js/Components/Header.vue:120` | `Tiếng Việt` | Đổi thành `:title="tt('Tiếng Việt')"` và thêm vào `en.json` |
| `FE-I18N-36` | Thuộc tính `:title` | `resources/Frontend/js/Components/Header.vue:151` | `Tìm kiếm` | Đổi thành `:title="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-37` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Header.vue:152` | `Tìm kiếm` | Đổi thành `:aria-label="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-38` | Thuộc tính `:alt` | `resources/Frontend/js/Components/Header.vue:156` | `Tìm kiếm` | Đổi thành `:alt="tt('Tìm kiếm')"` và thêm vào `en.json` |
| `FE-I18N-39` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Header.vue:165` | `Mở danh mục điều hướng` | Đổi thành `:aria-label="tt('Mở danh mục điều hướng')"` và thêm vào `en.json` |
| `FE-I18N-40` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Header.vue:192` | `Menu di động` | Đổi thành `:aria-label="tt('Menu di động')"` và thêm vào `en.json` |
| `FE-I18N-41` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Header.vue:208` | `Đóng menu` | Đổi thành `:aria-label="tt('Đóng menu')"` và thêm vào `en.json` |
| `FE-I18N-42` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:236` | `Ngôn ngữ / Language:` | Bọc `{{ tt('Ngôn ngữ / Language:...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-43` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:259` | `Giỏ hàng:` | Bọc `{{ tt('Giỏ hàng:...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-44` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:268` | `{{ cartCount }} sản phẩm` | Bọc `{{ tt('{{ cartCount }} sản phẩm...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-45` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:289` | `Đăng xuất` | Bọc `{{ tt('Đăng xuất...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-46` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:297` | `🔔 Thông báo &amp; Ghi chú` | Bọc `{{ tt('🔔 Thông báo &amp; Ghi chú...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-47` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Header.vue:308` | `Đăng nhập` | Bọc `{{ tt('Đăng nhập...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-48` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Product/ProductGallery.vue:56` | `Ảnh trước` | Đổi thành `:aria-label="tt('Ảnh trước')"` và thêm vào `en.json` |
| `FE-I18N-49` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Product/ProductGallery.vue:67` | `Ảnh tiếp theo` | Đổi thành `:aria-label="tt('Ảnh tiếp theo')"` và thêm vào `en.json` |
| `FE-I18N-50` | Thuộc tính `:aria-label` | `resources/Frontend/js/Components/Product/ProductGallery.vue:86` | `Đóng phóng to` | Đổi thành `:aria-label="tt('Đóng phóng to')"` và thêm vào `en.json` |
| `FE-I18N-51` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Product/RelatedProducts.vue:5` | `Sản phẩm khác` | Bọc `{{ tt('Sản phẩm khác...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-52` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Product/VariantSelector.vue:6` | `Vật liệu gợi ý` | Bọc `{{ tt('Vật liệu gợi ý...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-53` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Product/VariantSelector.vue:12` | `Màu trong ảnh` | Bọc `{{ tt('Màu trong ảnh...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-54` | Text hiển thị `<template>` | `resources/Frontend/js/Components/Product/VariantSelector.vue:18` | `Có thể tuỳ chỉnh` | Bọc `{{ tt('Có thể tuỳ chỉnh...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-55` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:3` | `Giới thiệu - HH Maker | Đưa sáng tạo vượt ra khỏi màn hình` | Bọc `{{ tt('Giới thiệu - HH Maker | Đưa sáng tạo vượ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-56` | Thuộc tính `:alt` | `resources/Frontend/js/Pages/About/Index.vue:20` | `Xưởng chế tác 3D HH Maker` | Đổi thành `:alt="tt('Xưởng chế tác 3D HH Maker')"` và thêm vào `en.json` |
| `FE-I18N-57` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:33` | `Giới thiệu về HH Maker` | Bọc `{{ tt('Giới thiệu về HH Maker...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-58` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:37` | `ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH` | Bọc `{{ tt('ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-59` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:41` | `Chúng tôi kết nối nghệ thuật 3D với công nghệ chế tác hiện đại, để một ý tư` | Bọc `{{ tt('Chúng tôi kết nối nghệ thuật 3D với công...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-60` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:87` | `Vì sao HH Maker ra đời?` | Bọc `{{ tt('Vì sao HH Maker ra đời?...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-61` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:90` | `TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH` | Bọc `{{ tt('TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-62` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:97` | `HH Maker được thành lập từ niềm đam mê với nghệ thuật 3D và công nghệ chế t` | Bọc `{{ tt('HH Maker được thành lập từ niềm đam mê v...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-63` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:100` | `Một bản phác thảo, một hình ảnh hay một nhu cầu rất riêng đều có thể mở ra ` | Bọc `{{ tt('Một bản phác thảo, một hình ảnh hay một ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-64` | Thuộc tính `:alt` | `resources/Frontend/js/Pages/About/Index.vue:115` | `Lâm Hồng Hải - Founder HH Maker` | Đổi thành `:alt="tt('Lâm Hồng Hải - Founder HH Maker')"` và thêm vào `en.json` |
| `FE-I18N-65` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:125` | `Chia sẻ từ Nhà sáng lập HH Maker` | Bọc `{{ tt('Chia sẻ từ Nhà sáng lập HH Maker...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-66` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:128` | `Mỗi ý tưởng đều xứng đáng có một hình hài` | Bọc `{{ tt('Mỗi ý tưởng đều xứng đáng có một hình hà...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-67` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:134` | `Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, t` | Bọc `{{ tt('Xuất phát từ niềm đam mê với nghệ thuật ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-68` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:137` | `Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, l` | Bọc `{{ tt('Với mỗi dự án, tôi cùng đội ngũ luôn tỉ ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-69` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:140` | `Tôi mong HH Maker sẽ là nơi bạn có thể thoải mái chia sẻ cả những ý tưởng c` | Bọc `{{ tt('Tôi mong HH Maker sẽ là nơi bạn có thể t...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-70` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:146` | `Trân trọng từng ý tưởng - Tận tâm với từng thành phẩm.` | Bọc `{{ tt('Trân trọng từng ý tưởng - Tận tâm với từ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-71` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:159` | `Cách HH Maker thực hiện trong từng dự án` | Bọc `{{ tt('Cách HH Maker thực hiện trong từng dự án...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-72` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:162` | `CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN` | Bọc `{{ tt('CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-73` | Thuộc tính `:alt` | `resources/Frontend/js/Pages/About/Index.vue:173` | `Thấu hiểu từng ý tưởng` | Đổi thành `:alt="tt('Thấu hiểu từng ý tưởng')"` và thêm vào `en.json` |
| `FE-I18N-74` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:179` | `Thấu hiểu từng ý tưởng` | Bọc `{{ tt('Thấu hiểu từng ý tưởng...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-75` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:182` | `Chúng tôi lắng nghe câu chuyện, nhu cầu sử dụng và mong muốn của bạn để tìm` | Bọc `{{ tt('Chúng tôi lắng nghe câu chuyện, nhu cầu ...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-76` | Thuộc tính `:alt` | `resources/Frontend/js/Pages/About/Index.vue:192` | `Chăm chút từng chi tiết` | Đổi thành `:alt="tt('Chăm chút từng chi tiết')"` và thêm vào `en.json` |
| `FE-I18N-77` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:198` | `Chăm chút từng chi tiết` | Bọc `{{ tt('Chăm chút từng chi tiết...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-78` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:201` | `Từ hình khối, tỉ lệ, vật liệu đến quá trình in và hoàn thiện, sản phẩm đều ` | Bọc `{{ tt('Từ hình khối, tỉ lệ, vật liệu đến quá tr...') }}` (hoặc lấy từ prop CMS động) |
| `FE-I18N-79` | Thuộc tính `:alt` | `resources/Frontend/js/Pages/About/Index.vue:211` | `Giữ trọn dấu ấn riêng` | Đổi thành `:alt="tt('Giữ trọn dấu ấn riêng')"` và thêm vào `en.json` |
| `FE-I18N-80` | Text hiển thị `<template>` | `resources/Frontend/js/Pages/About/Index.vue:217` | `Giữ trọn dấu ấn riêng` | Bọc `{{ tt('Giữ trọn dấu ấn riêng...') }}` (hoặc lấy từ prop CMS động) |

> *(Hiển thị 80/572 vị trí đầu tiên. Xem bảng thống kê theo file ở Mục 2)*.

---

## 4. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)

- **`[+] ĐỀ XUẤT THÊM (ADD)`**: Bọc `tt('...')` cho `244` chuỗi trong `<template>` + `328` chuỗi trong `<script>`, và bổ sung `17` khóa còn thiếu vào `resources/Frontend/lang/en.json`.
- **`[-] ĐỀ XUẤT BỎ QUA (SKIP BOILERPLATE)`**: Bỏ qua `162` files `.vue` boilerplate không nằm trong thiết kế Figma (`Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`...) để tiết kiệm thời gian và chống tràn bộ nhớ.
- **`[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`**: Giữ nguyên 100% cấu trúc DOM, class TailwindCSS, hiệu ứng Animation và các dữ liệu động đang nhận từ `$page.props` / CMS.

---

## 5. Bộ Handoff Prompts Đa Ngôn Ngữ Frontend — Phạm Vi: `About, Contact, Home, Layout, Posts, Process, Product, Products, Services` (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)

> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: app/**, config/**, resources/Backend/**]`) để chạy song song 100% với `/opsx-i18n-cms` mà không đụng file.

### 🌐 GÓI 1 (FE i18n — `About, Contact, Home, Layout, Posts, Process, Product, Products, Services`): Bọc `tt('...')` Cho Toàn Bộ Chuỗi Tiếng Việt Fix Cứng & Bổ Sung Từ Điển `en.json`

```markdown
Bạn đang thực thi **GÓI FE i18n (About, Contact, Home, Layout, Posts, Process, Product, Products, Services): Đa Ngôn Ngữ Giao Diện Frontend Theo Phạm Vi Figma** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-fe`.
Hãy đọc `.agents/skills/opsx-i18n-fe/SKILL.md` trước khi thực hiện.

### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)
- ✅ `[ALLOWED TO EDIT]` (Chỉ được sửa các file thuộc phạm vi này):
  - `resources/Frontend/js/Components/Base/BaseModal.vue`
  - `resources/Frontend/js/Components/DailyRewardPopup.vue`
  - `resources/Frontend/js/Components/Footer.vue`
  - `resources/Frontend/js/Components/Header.vue`
  - `resources/Frontend/js/Components/Icon/Phone.vue`
  - `resources/Frontend/js/Components/Jam/Breadcrumb.vue`
  - `resources/Frontend/js/Components/NotificationPopup.vue`
  - `resources/Frontend/js/Components/Paginate.vue`
  - `resources/Frontend/js/Components/PreHeader.vue`
  - `resources/Frontend/js/Components/Product/ProductGallery.vue`
  - `resources/Frontend/js/Components/Product/RelatedProducts.vue`
  - `resources/Frontend/js/Components/Product/SpecTable.vue`
  - `resources/Frontend/js/Components/Product/VariantSelector.vue`
  - `resources/Frontend/js/Components/SocialFixed.vue`
  - `resources/Frontend/js/Layouts/Empty.vue`
  - `resources/Frontend/js/Layouts/Guest.vue`
  - `resources/Frontend/js/Layouts/MainLayout.vue`
  - `resources/Frontend/js/Pages/About.vue`
  - `resources/Frontend/js/Pages/About/Index.vue`
  - `resources/Frontend/js/Pages/Contact.vue`
  - *(và 37 file `.vue` con trong phạm vi Figma)*
- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `app/**`, `config/**`, `database/**`, `resources/Backend/**` (đang được xử lý song song bởi `/opsx-i18n-cms`).
- Bắt buộc gọi `invoke_subagent` (`Role: "Frontend Worker"`) để thực hiện. Cấm dùng `rm` xóa file.

### 2. Danh Sách Các Chuỗi Tiếng Việt Còn Fix Cứng Cần Bọc `tt('...')` & Dịch Sang `en.json`:
- **[FE-I18N-01] `resources/Frontend/js/Components/Base/BaseModal.vue:36`** (Thuộc tính `:aria-label`): `"Đóng"` -> Đổi thành `:aria-label="tt('Đóng')"` và thêm vào `en.json`
- **[FE-I18N-02] `resources/Frontend/js/Components/DailyRewardPopup.vue:12`** (Thuộc tính `:title`): `"Xem lịch sử hoạt động và ghi chú của Admin"` -> Đổi thành `:title="tt('Xem lịch sử hoạt động và ghi chú của Admin')"` và thêm vào `en.json`
- **[FE-I18N-03] `resources/Frontend/js/Components/DailyRewardPopup.vue:28`** (Text hiển thị `<template>`): `"Hoạt động &amp; Ghi chú"` -> Bọc `{{ tt('Hoạt động &amp; Ghi chú...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-04] `resources/Frontend/js/Components/DailyRewardPopup.vue:44`** (Text hiển thị `<template>`): `"Thông báo &amp; Hoạt động"` -> Bọc `{{ tt('Thông báo &amp; Hoạt động...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-05] `resources/Frontend/js/Components/DailyRewardPopup.vue:50`** (Thuộc tính `:title`): `"Tạm ẩn popup (vẫn lưu lại để xem lại bất cứ lúc nào)"` -> Đổi thành `:title="tt('Tạm ẩn popup (vẫn lưu lại để xem lại bất cứ lúc nào)')"` và thêm vào `en.json`
- **[FE-I18N-06] `resources/Frontend/js/Components/DailyRewardPopup.vue:68`** (Text hiển thị `<template>`): `"Ghi chú từ Ban Quản trị:"` -> Bọc `{{ tt('Ghi chú từ Ban Quản trị:...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-07] `resources/Frontend/js/Components/DailyRewardPopup.vue:79`** (Text hiển thị `<template>`): `"✓ Đã hiểu và đóng thông báo"` -> Bọc `{{ tt('✓ Đã hiểu và đóng thông báo...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-08] `resources/Frontend/js/Components/DailyRewardPopup.vue:92`** (Text hiển thị `<template>`): `"Bạn được +{{ rewardPoints }} quả thưởng"` -> Bọc `{{ tt('Bạn được +{{ rewardPoints }} quả thưởng...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-09] `resources/Frontend/js/Components/DailyRewardPopup.vue:95`** (Text hiển thị `<template>`): `"đăng nhập ngày hôm nay"` -> Bọc `{{ tt('đăng nhập ngày hôm nay...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-10] `resources/Frontend/js/Components/DailyRewardPopup.vue:101`** (Thuộc tính `:alt`): `"Quả thưởng"` -> Đổi thành `:alt="tt('Quả thưởng')"` và thêm vào `en.json`
- **[FE-I18N-11] `resources/Frontend/js/Components/DailyRewardPopup.vue:111`** (Text hiển thị `<template>`): `"MÌNH CẢM ƠN FUNKY!"` -> Bọc `{{ tt('MÌNH CẢM ƠN FUNKY!...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-12] `resources/Frontend/js/Components/DailyRewardPopup.vue:121`** (Text hiển thị `<template>`): `"Hoạt động gần đây"` -> Bọc `{{ tt('Hoạt động gần đây...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-13] `resources/Frontend/js/Components/DailyRewardPopup.vue:129`** (Text hiển thị `<template>`): `"Xem tất cả →"` -> Bọc `{{ tt('Xem tất cả →...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-14] `resources/Frontend/js/Components/DailyRewardPopup.vue:161`** (Text hiển thị `<template>`): `"dư: {{ act.balance_after }}"` -> Bọc `{{ tt('dư: {{ act.balance_after }}...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-15] `resources/Frontend/js/Components/DailyRewardPopup.vue:169`** (Text hiển thị `<template>`): `"Chưa có hoạt động nào được ghi nhận"` -> Bọc `{{ tt('Chưa có hoạt động nào được ghi nhận...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-16] `resources/Frontend/js/Components/DailyRewardPopup.vue:181`** (Thuộc tính `:alt`): `"Quả nạp"` -> Đổi thành `:alt="tt('Quả nạp')"` và thêm vào `en.json`
- **[FE-I18N-17] `resources/Frontend/js/Components/DailyRewardPopup.vue:182`** (Text hiển thị `<template>`): `"Nạp thêm Quả"` -> Bọc `{{ tt('Nạp thêm Quả...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-18] `resources/Frontend/js/Components/DailyRewardPopup.vue:189`** (Text hiển thị `<template>`): `"Tạm ẩn"` -> Bọc `{{ tt('Tạm ẩn...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-19] `resources/Frontend/js/Components/Footer.vue:17`** (Text hiển thị `<template>`): `"CÔNG TY IN ẤN 3D HH MAKER"` -> Bọc `{{ tt('CÔNG TY IN ẤN 3D HH MAKER...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-20] `resources/Frontend/js/Components/Footer.vue:28`** (Text hiển thị `<template>`): `"Trang chủ"` -> Bọc `{{ tt('Trang chủ...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-21] `resources/Frontend/js/Components/Footer.vue:33`** (Text hiển thị `<template>`): `"Sản phẩm"` -> Bọc `{{ tt('Sản phẩm...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-22] `resources/Frontend/js/Components/Footer.vue:38`** (Text hiển thị `<template>`): `"Giới thiệu"` -> Bọc `{{ tt('Giới thiệu...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-23] `resources/Frontend/js/Components/Footer.vue:43`** (Text hiển thị `<template>`): `"Quy trình"` -> Bọc `{{ tt('Quy trình...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-24] `resources/Frontend/js/Components/Footer.vue:52`** (Text hiển thị `<template>`): `"Dịch vụ"` -> Bọc `{{ tt('Dịch vụ...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-25] `resources/Frontend/js/Components/Footer.vue:57`** (Text hiển thị `<template>`): `"Thiết kế 3D"` -> Bọc `{{ tt('Thiết kế 3D...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-26] `resources/Frontend/js/Components/Footer.vue:62`** (Text hiển thị `<template>`): `"Gia công 3D"` -> Bọc `{{ tt('Gia công 3D...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-27] `resources/Frontend/js/Components/Footer.vue:76`** (Text hiển thị `<template>`): `"Liên hệ tư vấn"` -> Bọc `{{ tt('Liên hệ tư vấn...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-28] `resources/Frontend/js/Components/Footer.vue:117`** (Text hiển thị `<template>`): `"Chính sách bảo mật"` -> Bọc `{{ tt('Chính sách bảo mật...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-29] `resources/Frontend/js/Components/Footer.vue:120`** (Text hiển thị `<template>`): `"Điều khoản sử dụng"` -> Bọc `{{ tt('Điều khoản sử dụng...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-30] `resources/Frontend/js/Components/Footer.vue:123`** (Text hiển thị `<template>`): `"Chính sách Cookie"` -> Bọc `{{ tt('Chính sách Cookie...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-31] `resources/Frontend/js/Components/Header.vue:85`** (Thuộc tính `:title`): `"Tìm kiếm"` -> Đổi thành `:title="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-32] `resources/Frontend/js/Components/Header.vue:86`** (Thuộc tính `:aria-label`): `"Tìm kiếm"` -> Đổi thành `:aria-label="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-33] `resources/Frontend/js/Components/Header.vue:90`** (Thuộc tính `:alt`): `"Tìm kiếm"` -> Đổi thành `:alt="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-34] `resources/Frontend/js/Components/Header.vue:102`** (Thuộc tính `:title`): `"Tiếng Việt"` -> Đổi thành `:title="tt('Tiếng Việt')"` và thêm vào `en.json`
- **[FE-I18N-35] `resources/Frontend/js/Components/Header.vue:120`** (Thuộc tính `:title`): `"Tiếng Việt"` -> Đổi thành `:title="tt('Tiếng Việt')"` và thêm vào `en.json`
- **[FE-I18N-36] `resources/Frontend/js/Components/Header.vue:151`** (Thuộc tính `:title`): `"Tìm kiếm"` -> Đổi thành `:title="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-37] `resources/Frontend/js/Components/Header.vue:152`** (Thuộc tính `:aria-label`): `"Tìm kiếm"` -> Đổi thành `:aria-label="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-38] `resources/Frontend/js/Components/Header.vue:156`** (Thuộc tính `:alt`): `"Tìm kiếm"` -> Đổi thành `:alt="tt('Tìm kiếm')"` và thêm vào `en.json`
- **[FE-I18N-39] `resources/Frontend/js/Components/Header.vue:165`** (Thuộc tính `:aria-label`): `"Mở danh mục điều hướng"` -> Đổi thành `:aria-label="tt('Mở danh mục điều hướng')"` và thêm vào `en.json`
- **[FE-I18N-40] `resources/Frontend/js/Components/Header.vue:192`** (Thuộc tính `:aria-label`): `"Menu di động"` -> Đổi thành `:aria-label="tt('Menu di động')"` và thêm vào `en.json`
- **[FE-I18N-41] `resources/Frontend/js/Components/Header.vue:208`** (Thuộc tính `:aria-label`): `"Đóng menu"` -> Đổi thành `:aria-label="tt('Đóng menu')"` và thêm vào `en.json`
- **[FE-I18N-42] `resources/Frontend/js/Components/Header.vue:236`** (Text hiển thị `<template>`): `"Ngôn ngữ / Language:"` -> Bọc `{{ tt('Ngôn ngữ / Language:...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-43] `resources/Frontend/js/Components/Header.vue:259`** (Text hiển thị `<template>`): `"Giỏ hàng:"` -> Bọc `{{ tt('Giỏ hàng:...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-44] `resources/Frontend/js/Components/Header.vue:268`** (Text hiển thị `<template>`): `"{{ cartCount }} sản phẩm"` -> Bọc `{{ tt('{{ cartCount }} sản phẩm...') }}` (hoặc lấy từ prop CMS động)
- **[FE-I18N-45] `resources/Frontend/js/Components/Header.vue:289`** (Text hiển thị `<template>`): `"Đăng xuất"` -> Bọc `{{ tt('Đăng xuất...') }}` (hoặc lấy từ prop CMS động)
- *(Cùng 527 vị trí khác trong báo cáo `scratch/i18n-fe-audit-*.md` — hãy quét và bọc 100% trong các file `[ALLOWED TO EDIT]` ở trên)*.

### 3. Bổ Sung Bản Dịch Tiếng Anh Còn Thiếu Trong `resources/Frontend/lang/en.json` (17 khóa):
- Các khóa `tt(...)` đang gọi trên giao diện nhưng chưa có trong `resources/Frontend/lang/en.json`: `"+84 283 8722 846"`, `"842838722846"`, `"Dịch vụ"`, `"Email"`, `"Giới thiệu"`, `"Home"`, `"Hotline"`, `"Không tìm thấy kết quả cho từ khóa"`, `"Liên hệ ngay"`, `"Quy trình"`, `"Sản phẩm"`, `"Tin tức"`, `"Trang chủ"`, `"Tìm kiếm"`, `"Tìm kiếm bài viết"`...
- Hãy bổ sung đầy đủ 100% cặp key-value Tiếng Việt vào `resources/Frontend/lang/vi.json` và bản dịch Tiếng Anh chuẩn chuyên ngành vào `resources/Frontend/lang/en.json`.

### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:
- Chạy lại `python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py .` để xác nhận số chuỗi Tiếng Việt fix cứng trong `<template>` giảm về `0` và `0` khóa thiếu trong `en.json`.
- Chạy `yarn frontend:build` thành công (`exit code 0`).
```
