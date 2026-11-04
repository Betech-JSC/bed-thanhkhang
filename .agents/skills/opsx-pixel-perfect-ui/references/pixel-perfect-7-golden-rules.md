# 7 Quy Tắc Vàng Đạt 100% Pixel-Perfect UI Từ Figma MCP (LUXI Production Standard)

Tài liệu này đúc kết **7 quy chuẩn kỹ thuật và các bẫy CSS thực chiến** từ dự án LUXI giúp giao diện thực tế trên trình duyệt khớp 100% với bản vẽ Figma từ kích thước khung (`px`), tỷ lệ ảnh (`1:1`), vị trí trên màn hình đầu tiên (`1-Screen Viewport`), hướng mũi tên vector SVG cho đến độ nét Retina.

---

## Quy Tắc 1: Khóa Box-Model & Công Thức 1-Screen Top Offset (`Section_Padding_Top = Figma_Y - Header_Height`)

### Bẫy thường gặp:
Trong Figma, toạ độ `y` của Section đầu tiên thường được tính từ đỉnh trang `y = 0` (ví dụ `y = 136px`, trong đó `Header` cao `96px` và khoảng hở thực tế dưới Header chỉ là `40px`). Nếu lập trình viên bê nguyên `lg:pt-[136px]` vào Section đầu tiên trong khi `Default.vue` đã có spacer `pt-[88px] lg:pt-[96px]` cho Fixed Header, tổng khoảng cách bị đội lên `96px + 136px = 232px` (lệch dư `96px`), đẩy khung ảnh sản phẩm `688×688px` bị tràn khỏi màn hình đầu tiên!

### Công thức chuẩn bắt buộc:
$$\text{Section\_Padding\_Top} = \text{Figma\_Section\_Y} - \text{Fixed\_Header\_Height}$$

- Với LUXI (`Header = 96px`, `Figma_Y = 136px`):
  - Dùng `lg:pt-[40px]` cho section đầu trang (`Pages/Products/Show.vue`).
  - Khung `ProductGallery.vue` (`688×688px`) + `sticky top-[116px]` hiển thị **vừa khít 100% trong 1 màn hình Desktop** mà không bị cắt đáy.
- **Khóa kích thước cứng (`w` × `h`) trên Desktop (`lg:`)**:
  - **PLP Product Card (`ProductCard.vue`)**: Khung thẻ `lg:w-[320px] lg:min-h-[420px]`, khung ảnh vuông `aspect-square lg:w-[320px] lg:h-[320px] rounded-[8px]`.
  - **PDP Main Gallery (`ProductGallery.vue`)**: Khung chính `aspect-square lg:w-[688px] lg:h-[688px] rounded-[8px]`, dải 6 ảnh thumbnail dưới `lg:w-[104px] lg:h-[104px] rounded-[8px] gap-[12.8px]`.
  - **PDP Related Product Card (`RelatedProductsSection.vue`)**: Khung thẻ `lg:w-[368px] lg:min-h-[468px]`, khung ảnh vuông `aspect-square lg:w-[368px] lg:h-[368px] rounded-[8px]`.

---

## Quy Tắc 2: Cấm Bẫy `@tailwindcss/aspect-ratio` Làm Hỏng `aspect-square`

### Bẫy thường gặp:
Trong Tailwind CSS v3+, `aspect-square` đã hỗ trợ native CSS `aspect-ratio: 1 / 1`. Nếu trong `tailwind.config.js` vẫn giữ plugin cũ `require('@tailwindcss/aspect-ratio')`, plugin này sẽ ghi đè class `.aspect-square` thành cơ chế `padding-bottom: calc(1 / 1 * 100%); height: 0` kiểu cũ, gây sập chiều cao hoặc vỡ bố cục khi kết hợp với Flexbox/Grid và `lg:h-[320px]`.

### Giải pháp chuẩn:
1. Gỡ bỏ hoàn toàn `require('@tailwindcss/aspect-ratio')` khỏi mảng `plugins` trong `tailwind.config.js`.
2. Khai báo rõ trong `theme.extend.aspectRatio`:
   ```js
   aspectRatio: {
       square: '1 / 1',
   }
   ```

---

## Quy Tắc 3: Đối Chiếu Số Lượng Section & Khối Nội Dung (`Structural & Section Count Parity`)

### Bẫy thường gặp:
Agent tự ý thêm các khối "cho đẹp" hoặc giữ lại các khối từ template cũ không hề tồn tại trên Figma:
- Tự thêm Section 4.5 (Featured Grid phụ) trên Trang chủ trong khi Figma chỉ có 9 section.
- Tự thêm dòng mô tả `<p>` dưới tiêu đề Hero của trang Danh mục Sản phẩm (`ProductHeroBanner.vue`) trong khi Figma chỉ có Breadcrumb + H1.
- Tự thêm khối Milestones phụ bên trong Section Cột mốc (`AboutMilestonesSection.vue`) làm tổng chiều cao vượt quá `818px` của Figma.

### Giải pháp chuẩn:
- Quét `get_figma_data` (`depth: 2`) để lập bảng đối chiếu 1-1 số lượng Section và tổng chiều cao từng Section trước khi code.
- Mọi khối không có `nodeId` tương ứng trên Figma đều phải đưa vào danh sách `[-] BỎ GÌ (REMOVE)`.

---

## Quy Tắc 4: Đồng Bộ Đúng Các Figma Interactive Variants (`Header Inline Search` & `Modal`)

### Bẫy thường gặp:
Chỉ nhìn màn hình tĩnh mặc định mà bỏ sót các Frame mô tả trạng thái tương tác (Interactive State / Component Variant) nằm cạnh đó trên Figma:
- **Header Search (`Figma 597:5014`)**: Không phải modal full-screen tối đen, mà là **Inline Pill Search Bar** (`w-[520px] h-[40px] rounded-pill border-[#352D22] bg-[#151515]/90`) bung ngay trên thanh Header giữa Logo và cụm nút CTA khi bấm icon kính lúp.
- **Modal Tư Vấn (`Figma 525:3796` / `605:6314`)**: Khung `1152×684px` chia 2 cột cân xứng (ảnh trái + form phải gồm đúng 4 nhóm trường: Họ tên, Số điện thoại, Khu vực, Sản phẩm quan tâm).

### Giải pháp chuẩn:
- Khi quét Figma MCP, luôn kiểm tra các Frame Variant cùng cụm để lấy đúng kích thước và vị trí của trạng thái mở (Open/Active/Focus).

---

## Quy Tắc 5: Kiểm Tra Hướng Vector `<path>` Của Icon SVG & Độ Nét Logo Retina `@4x` + Bộ 6 Favicon

### Bẫy thường gặp:
1. **Ngược hướng mũi tên SVG**: File `arrow-circle-left.svg` và `arrow-circle-right.svg` có tên file khác nhau nhưng bên trong có cùng một tọa độ `<path d="M60 40L80 60L60 80...">` (đầu nhọn `X=80 > 60` trỏ sang phải), khiến cả 2 nút `<` và `>` trên giao diện đều trỏ sang phải!
2. **Logo mờ trên màn hình Retina**: Xuất ảnh PNG `1x` (`98×66px`) từ Figma làm logo bị rỗ trên màn hình MacBook/iPhone (`devicePixelRatio >= 2`).
3. **Favicon méo hoặc thiếu kích thước**: Dùng trực tiếp logo chữ ngang làm favicon `16×16` khiến chữ bị bệt không đọc được.

### Giải pháp chuẩn:
- **Kiểm tra toán học tọa độ `<path>`**:
  - `arrow-circle-left.svg`: Đỉnh nhọn phải có hoành độ nhỏ hơn hai cánh (`M60 40L40 60L60 80M40 60H80...`, `40 < 60`).
  - `arrow-circle-right.svg`: Đỉnh nhọn phải có hoành độ lớn hơn hai cánh (`M60 40L80 60L60 80M80 60H40...`, `80 > 60`).
- **Logo Retina `@4x`**: Xuất từ Figma ở `scale: 4` (`392×264px` cho khung hiển thị `98×66px`).
- **Bộ 6 Favicon chuẩn trên nền tròn thương hiệu**: `favicon-16x16.png`, `favicon-32x32.png`, `favicon-96x96.png`, `apple-touch-icon.png` (`180×180`), `android-chrome-192x192.png`, `android-chrome-512x512.png`.

---

## Quy Tắc 6: Chuẩn W3C `overflow-x: clip` — Duy Nhất 1 Thanh Cuộn Dọc & Bảo Toàn `position: sticky`

### Bẫy thường gặp:
Đặt `overflow-x-hidden` (tức `overflow-x: hidden`) lên thẻ `<div class="w-full overflow-x-hidden">` ở cấp Page hoặc Layout. Theo đặc tả W3C CSS Overflow, khi `overflow-x` là `hidden`, trình duyệt tự động ép `overflow-y` từ `visible` sang `auto`, biến thẻ `div` đó thành một scroll container thứ hai:
1. Xuất hiện **2 thanh cuộn dọc** song song trên màn hình.
2. Làm **chết hoàn toàn `lg:sticky lg:top-[116px]`** của `ProductGallery.vue` và Sidebar bộ lọc.

### Giải pháp chuẩn:
- Trong `resources/Frontend/scss/app.scss`:
  ```scss
  html,
  body {
      font-family: var(--font-sans);
      overflow-x: clip;
  }
  ```
- Không đặt `overflow-x-hidden` trên thẻ root của Page hay cha của phần tử `sticky`.

---

## Quy Tắc 7: Nghiệm Thu Tự Động Bằng Playwright Computed Box Verification

Mọi màn hình sau khi hoàn thiện đều phải kiểm chứng bằng Playwright MCP với đoạn script đo đạc thực tế:

```javascript
() => {
  const htmlStyle = window.getComputedStyle(document.documentElement);
  const bodyStyle = window.getComputedStyle(document.body);
  const gallery = document.querySelector('.max-w-product-gallery');
  const galleryRect = gallery ? gallery.getBoundingClientRect() : null;
  return {
    overflowX: { html: htmlStyle.overflowX, body: bodyStyle.overflowX },
    horizontalOverflowPx: document.documentElement.scrollWidth - document.documentElement.clientWidth,
    galleryBox: galleryRect ? { width: Math.round(galleryRect.width), top: Math.round(galleryRect.top), bottom: Math.round(galleryRect.bottom) } : null,
    viewportHeight: window.innerHeight,
  };
}
```
Floats/Bboxes phải đạt: `horizontalOverflowPx === 0`, `overflowX.body === 'clip'`, và `galleryBox.width === 688`.
