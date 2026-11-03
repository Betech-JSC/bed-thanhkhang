# Tasks: Chuẩn hóa trang Chi tiết Dịch vụ theo Figma Node 2367:2088

## 1. Khảo sát & Chuẩn bị Assets

- [x] 1.1 Trích xuất thông số chi tiết từ Figma MCP (Node `2367:2088`, `2367:2156`, `2367:2178`, `2367:2196`, `2367:2270`, `2352:11794`) và đối chiếu với code hiện có
- [x] 1.2 Tải và đồng bộ ảnh minh họa gốc từ Figma MCP (`minh_họa_thiết_kế_3d_từ_trang_dịch_vụ.png`, `service-gia-cong-3d.png`, `service-lam-do-custom.png`) vào `public/assets/images/services/`

## 2. Chuẩn hóa Code Component Frontend

- [x] 2.1 Chuẩn hóa `ServiceHero.vue`: Breadcrumbs màu `#B9C0D4` / `#84ADFF`, tiêu đề H1 38px/46px `#FAFAFA`, khung minh họa kỹ thuật 1152x310px với gradient 180deg `#101121` -> `#2D4BE2`, ảnh 3D 280x280px, 2 cột nội dung "BẮT ĐẦU TỪ" và "CÙNG LÀM RÕ" với typography chuẩn
- [x] 2.2 Chuẩn hóa `ServiceToc.vue`: Sidebar 248px, nhãn "TRONG BÀI VIẾT NÀY" (13px, font 600, `#B9C0D4`), 4 mục với active border-l-2 `#2970FF` và text `#84ADFF` (loại bỏ box-shadow thừa), inactive border-l-1 `#404968`
- [x] 2.3 Chuẩn hóa `ServiceArticleContent.vue`: Tiêu đề H2 28px/36px `#FAFAFA`, văn bản 16px `#B9C0D4`, quy trình 4 bước thiết kế (badge số 40x40px `#30374F`, số `#84ADFF` 15px), card checklist chuẩn bị nền `#171B30` bo góc 12px padding 28px, khối callout lưu ý viền trái `border-l-2 border-[#2970FF]` và text `#B2CCFF`
- [x] 2.4 Chuẩn hóa `ServiceRelated.vue`: Lưới 2 card 564x220px bo góc 12px viền `#404968` (hover `#2970FF`), thumbnail 180x220px gradient, tên dịch vụ 24px font 700 `#FAFAFA`, link "Tìm hiểu dịch vụ" `#84ADFF`
- [x] 2.5 Chuẩn hóa `ServiceCta.vue`: Banner 1152x183px gradient 90deg `#2970FF` -> `#00359E`, bo góc 16px, tiêu đề H2 32px `#FAFAFA`, nút "Nhận tư vấn ngay" 175x40px nền `#F5F8FF` chữ `#111322`
- [x] 2.6 Đồng bộ và tích hợp trên `Show.vue`: Container `max-w-[1152px] mx-auto`, thiết lập Scrollspy IntersectionObserver chính xác và smooth scroll offset 110px

## 3. Build & Kiểm thử Playwright E2E

- [x] 3.1 Biên dịch toàn bộ tài nguyên giao diện bằng lệnh `yarn frontend:build` đảm bảo 0 lỗi
- [x] 3.2 Xây dựng test Playwright E2E kiểm tra toàn bộ luồng trang chi tiết dịch vụ, tính năng Scrollspy TOC và tương tác
- [x] 3.3 Chạy kiểm thử Playwright E2E, chụp ảnh màn hình làm bằng chứng tại Desktop (1440px) và Laptop (1280px)
- [x] 3.4 Cập nhật bản đồ kiến trúc AST bằng lệnh `graphify . --code-only`
