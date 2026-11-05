# Design: Chuẩn Hóa Hệ Thống Universal Container Toàn Diện

## Context

Xem chi tiết nguyên nhân và động lực tại `proposal.md` và các yêu cầu hành vi tại `specs/universal-container/spec.md`.

Hiện tại dự án HH-Maker đang dùng Vue 3 + Inertia.js + Tailwind CSS 3. Các trang và component được phát triển qua nhiều giai đoạn dẫn đến sự bất nhất trong khai báo vùng chứa (container):
- Một số trang dùng `max-w-[1440px] xl:px-[144px]` (như Product).
- Một số trang dùng `max-w-[1152px] px-8 xl:px-0` (như Services, Contact, Process).
- Một số section trên Trang Chủ dùng `max-w-[1180px]`, `max-w-[1240px]`, hoặc Tailwind `container mx-auto`.
- Footer dùng `max-w-[1152px] lg:px-0`.

Hệ quả là mép nội dung bị giật lệch từ 112px đến 176px trên Trang Chủ và lệch 128px khi chuyển trang giữa Product và Services trên laptop 1280px.

## Goals / Non-Goals

**Goals:**
- Thiết lập một quy chuẩn Base Container duy nhất cho 90% body content của toàn bộ các trang: `w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8` (kèm utility class `.app-container`).
- Triệt tiêu 100% hiện tượng lệch mép ziczac trên Trang Chủ và hiện tượng co giật layout giữa các trang.
- Đảm bảo độ rộng nội dung hữu dụng đạt chuẩn 1152px trên Desktop (1440px) và Laptop (1280px), khớp chuẩn Figma Node 2261:5076 với lề ngoài 144px trên Desktop.
- Bảo toàn trọn vẹn 2 vùng chứa ngoại lệ có chủ đích thiết kế: Hero Banner Card (`max-w-[1280px]`) và Header Navigation (`max-w-[1440px]`).

**Non-Goals:**
- Không can thiệp vào cấu trúc layout của Admin / Backend CMS.
- Không thay đổi thiết kế các component con bên trong (card sản phẩm, button, modal).
- Không thay đổi hành vi routing của Inertia hay dữ liệu backend.

## Decisions

### 1. Công thức toán học Base Container 1216px
- **Quyết định**: Sử dụng `max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8`.
- **Cơ sở toán học (Figma Node 2261:5076)**:
  - Ở màn hình Desktop 1440px:
    - Khoảng cách lề ngoài (margin): `(1440px - 1216px) / 2 = 112px`.
    - Khoảng đệm trong (padding `lg:px-8`): `32px` mỗi bên.
    - Tổng khoảng cách từ mép màn hình tới nội dung: `112px + 32px = 144px` (Khớp tuyệt đối lề 144px của Figma).
    - Độ rộng vùng nội dung thực tế: `1216px - (32px * 2) = 1152px` (Khớp 100% chuẩn nội dung 1152px của Figma).
  - Ở màn hình Laptop 1280px:
    - Khoảng cách lề ngoài: `(1280px - 1216px) / 2 = 32px`.
    - Khoảng đệm trong: `32px` mỗi bên.
    - Tổng khoảng cách từ mép màn hình tới nội dung: `32px + 32px = 64px`.
    - Độ rộng vùng nội dung: Giữ nguyên `1152px`, hoàn toàn không bị co giật.
  - Ở màn hình Mobile & Tablet (< 1024px):
    - Dưới 640px: `px-4` (16px lề mỗi bên).
    - Từ 640px đến 1023px: `sm:px-6` (24px lề mỗi bên).
- **Phương án thay thế đã xem xét**:
  - *Phương án A: `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8`*: Bị lỗi do `box-sizing: border-box`, vùng nội dung bị hụt còn `1152 - 64 = 1088px` (thiếu 64px so với Figma).
  - *Phương án B: `max-w-[1440px] mx-auto xl:px-[144px]`*: Trên màn hình 1280px, padding 288px làm nội dung co rúm lại còn 992px, gây giật khung hình nghiêm trọng khi chuyển giữa các trang.

### 2. Khai báo Utility Class `.app-container` trong SCSS
- **Quyết định**: Khai báo class `.app-container` trong `resources/Frontend/scss/app.scss` dưới `@layer components`:
  ```scss
  @layer components {
    .app-container {
      @apply w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8;
    }
  }
  ```
  Đồng thời hỗ trợ việc dùng trực tiếp các utility Tailwind `w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8` trong template Vue để đảm bảo tính tường minh và linh hoạt khi cần kết hợp thêm margin dọc.

### 3. Chuẩn hóa từng nhóm File giao diện
- **Nhóm 1: Chân trang dùng chung (`Footer.vue`)**:
  - Thay `w-full max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0 space-y-12` bằng `w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8 space-y-12` (hoặc `app-container space-y-12`). Khắc phục lỗi text chân trang dính sát mép màn hình trên màn hình 1024px.
- **Nhóm 2: Các Section trên Trang Chủ (`Home/Components/`)**:
  - `BrandStorySection.vue`: Thay `container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1240px]` thành `app-container` (hoặc `w-full max-w-[1216px] mx-auto px-4 sm:px-6 lg:px-8`).
  - `HighlightMetrics.vue`: Thay `container mx-auto px-4 sm:px-6 lg:px-8` thành `app-container`.
  - `ServicesSection.vue`: Thay `container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]` thành `app-container`.
  - `ProductsSection.vue`: Thay `max-w-[1180px] mx-auto px-4 sm:px-6 lg:px-8` thành `app-container`.
  - `FeaturedProductsCarousel.vue`: Thay `max-w-[1180px] mx-auto px-4 sm:px-6 lg:px-8` thành `app-container`.
  - `ContactSection.vue`: Thay `w-full max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0` thành `app-container`.
- **Nhóm 3: Trang Sản phẩm (`Product/`)**:
  - `Product/Index.vue`: Thay `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]` thành `app-container`.
  - `Product/Show.vue`: Thay `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]` thành `app-container`.
  - `Product/Components/ProductCtaBanner.vue`: Thay `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-[144px]` thành `app-container`.
- **Nhóm 4: Trang Dịch vụ (`Services/`)**:
  - `Services/Index.vue`: Thay `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0` thành `app-container`.
  - `Services/Show.vue`: Thay `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0` thành `app-container`.
- **Nhóm 5: Các trang Thông tin & Nội dung (`Process/`, `About/`, `Contact.vue`, `Policy.vue`)**:
  - `Process/Index.vue` & `ProcessCtaBanner.vue`: Thay `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0` thành `app-container`.
  - `About/Index.vue`: Các khối body section thay `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-0` thành `app-container`. Khối Hero Banner giữ `max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-0`.
  - `Contact.vue`: Thay `max-w-[1152px] mx-auto px-4 sm:px-6 lg:px-8 xl:px-0` thành `app-container`.
  - `Policy.vue`: Thay `container mx-auto px-4 sm:px-6 lg:px-8 max-w-[1152px]` thành `app-container`.

### 4. Quy tắc bảo tồn ngoại lệ có chủ đích
- **Hero Banner Card**: Khối banner nổi bo góc trên Home (`HeroSection.vue`) và About (`About/Index.vue`) giữ nguyên `w-full max-w-[1280px] mx-auto px-4 sm:px-6 lg:px-0` vì đây là card đồ họa nổi riêng biệt, có lề 80px trên 1440px theo đúng chủ đích nghệ thuật của Figma.
- **Header Bar**: Header cố định (`Header.vue`) giữ nguyên `w-full max-w-[1440px] mx-auto px-4 sm:px-6 lg:px-20` để trải dài thanh menu điều hướng sát hai bên mép theo đúng thiết kế.

## Risks / Trade-offs

- **[Risk] Mở rộng vùng nội dung từ 1088px lên 1152px (+64px) có thể làm vỡ khoảng cách các thẻ con**:
  - *Mitigation*: Các lưới con (như lưới 4 cột dịch vụ, lưới 4 thẻ sản phẩm) đều dùng CSS Grid (`grid-cols-1 md:grid-cols-2 lg:grid-cols-4`) hoặc Flexbox với `gap`. Khi container mở rộng đúng 1152px, các thẻ sẽ có thêm khoảng thở thẩm mỹ đúng tỷ lệ Figma, không gây tràn màn hình.
- **[Risk] Nguy cơ sót các container lồng nhau (nested containers)**:
  - *Mitigation*: Khi chuyển đổi các trang, kiểm tra kỹ để không lồng class `.app-container` hai lần trong cùng một cấu trúc cha-con, loại bỏ các lớp bọc thừa như `max-w-[1116px]` nếu không cần thiết.
- **[Risk] Tràn khung ngang trên Mobile**:
  - *Mitigation*: Mọi container đều duy trì `w-full` và `px-4 sm:px-6`, kiểm thử tự động với Playwright trên viewport 375x667 để xác nhận `scrollWidth <= innerWidth`.

## Migration Plan

1. **Bước 1**: Bổ sung class `.app-container` vào `resources/Frontend/scss/app.scss`.
2. **Bước 2**: Cập nhật Footer (`Footer.vue`).
3. **Bước 3**: Cập nhật các section Trang Chủ (`Home/Components/*`).
4. **Bước 4**: Cập nhật các trang Sản phẩm (`Product/*`).
5. **Bước 5**: Cập nhật các trang Dịch vụ (`Services/*`).
6. **Bước 6**: Cập nhật các trang Quy trình, Giới thiệu, Liên hệ, Chính sách (`Process/*`, `About/*`, `Contact.vue`, `Policy.vue`).
7. **Bước 7**: Chạy `yarn frontend:build` để biên dịch tài nguyên và chạy Playwright test để nghiệm thu bằng chứng.
