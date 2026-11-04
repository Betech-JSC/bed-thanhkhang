# 4 Quy Tắc Vàng Bất Biến Về Animation & Mẫu Code Chuẩn (LUXI Production Standard)

Tài liệu này quy định chi tiết 4 nguyên tắc bất biến và mẫu code chuẩn cho toàn bộ hệ thống Animation & Micro-Interaction trong dự án LUXI và các dự án dùng chung bộ Skill của Team.

---

## Quy Tắc 1: `Single-Animation-Per-Element` (Mỗi Phần Tử Tối Đa 1 Animation Chủ Đạo)

### Nguyên tắc:
1. **Không chồng chéo Animation trên cùng 1 phần tử**:
   - Không viết đồng thời `transition-transform` và `transition-all` trên cùng một thẻ.
   - Luôn chỉ định rõ thuộc tính cần chuyển động: `transition-transform` (cho slide/scale), `transition-colors` (cho đổi màu chữ/viền/nền), hoặc `transition-opacity` (chỉ dành cho backdrop modal/drawer).
2. **Không lồng 2 tầng Transition cùng lúc**:
   - Cấm bọc `<TransitionGroup>` bên trong `<Transition>` cho cùng một lưới sản phẩm (`ProductGrid.vue`). Chỉ sử dụng duy nhất 1 `<Transition mode="out-in">` trượt ngang trên khung lưới.
3. **Phân tầng rõ giữa Section và Card con**:
   - Nếu thẻ `<section>` đã có `v-scroll-reveal` (trượt nhẹ `translate-y-4 -> translate-y-0` khi cuộn tới), các thẻ Card bên trong chỉ dùng hiệu ứng phản hồi khi tương tác chuột (`group-hover:scale-[1.03]`, `active:scale-95`).

---

## Quy Tắc 2: `Zero White-Out / No Opacity Blank Flash` (Cấm Hiệu Ứng Phủ Trắng Hoặc Nháy Tàng Hình Khi Tải/Cuộn Trang)

### Nguyên tắc:
- Khi người dùng mới vào trang hoặc cuộn nhanh, nếu dùng `opacity-0` cho các Section lớn, màn hình sẽ bị trắng/trống rỗng một nhịp trước khi IntersectionObserver kích hoạt.
- Do đó, `useScrollReveal.js` (`v-scroll-reveal`) **BẮT BUỘC giữ `opacity: 1` (100% hiển thị ngay từ đầu)** và chỉ dịch chuyển nhẹ bằng `transform`:
  - Trạng thái chờ (`hiddenClasses`): `translate-y-4`
  - Trạng thái hiện (`visibleClasses`): `translate-y-0`
  - Thuộc tính chuyển động (`baseClasses`): `transition-transform duration-500 ease-out`

### Mẫu code chuẩn (`useScrollReveal.js`):
```javascript
export function useScrollReveal(options = {}) {
    const {
        threshold = 0.05,
        rootMargin = '0px 0px 40px 0px',
        once = true,
        baseClasses = 'transition-transform duration-500 ease-out',
        hiddenClasses = 'translate-y-4',
        visibleClasses = 'translate-y-0',
    } = options
    // ...
}
```

---

## Quy Tắc 3: `Directional Horizontal Slide (translate-x)` Cho Mọi Khu Vực Có Nút Bấm Qua Lại (`<` / `>`)

### Nguyên tắc:
Mọi khu vực có **nút bấm qua lại (`<` / `>` hoặc chấm tròn phân trang Dots)** để chuyển đổi danh sách sản phẩm, góc nhìn, banner hoặc trang sản phẩm **BẮT BUỘC phải chạy hiệu ứng Trượt Ngang (`translate-x`) theo đúng hướng bấm**:
- Khi bấm nút **Next (`>`)**: Nội dung mới trượt từ phải sang trái (`enter-from: translate-x-6` -> `enter-to: translate-x-0`), nội dung cũ trượt sang trái (`leave-to: -translate-x-6`).
- Khi bấm nút **Prev (`<`)**: Nội dung mới trượt từ trái sang phải (`enter-from: -translate-x-6` -> `enter-to: translate-x-0`), nội dung cũ trượt sang phải (`leave-to: translate-x-6`).
- **TUYỆT ĐỐI KHÔNG có `opacity-0`** trong các class `enter-from-class` / `leave-to-class` của các slider này!

### Danh sách 5 Component Slider áp dụng chuẩn Trượt Ngang (`translate-x`):
1. `resources/Frontend/js/Components/Home/HomeProductShowcaseSection.vue` (Showcase 3 cột sản phẩm có nút `<` / `>`)
2. `resources/Frontend/js/Components/Home/HomePerspectiveSection.vue` (Carousel 3 thẻ Góc nhìn LUXI có nút `<` / `>`)
3. `resources/Frontend/js/Components/ProductDetail/RelatedProductsSection.vue` (Slider 3 sản phẩm liên quan có nút `<` / `>` và 4 Dots)
4. `resources/Frontend/js/Components/Home/HomeHeroSection.vue` (Hero Slider đầu trang chủ)
5. `resources/Frontend/js/Components/Products/ProductGrid.vue` (Lưới sản phẩm 3×3 khi bấm nút `<` / `>` phân trang)

### Mẫu code chuẩn cho Slider có nút `<` / `>`:
```vue
<template>
    <div class="overflow-hidden">
        <Transition
            mode="out-in"
            enter-active-class="transition-transform duration-300 ease-out"
            :enter-from-class="slideDirection === 'next' ? 'translate-x-6' : '-translate-x-6'"
            enter-to-class="translate-x-0"
            leave-active-class="transition-transform duration-200 ease-in"
            leave-from-class="translate-x-0"
            :leave-to-class="slideDirection === 'next' ? '-translate-x-6' : 'translate-x-6'"
        >
            <div :key="activeIndex">
                <!-- Nội dung Slide / Lưới sản phẩm -->
            </div>
        </Transition>
    </div>
</template>

<script setup>
import { ref } from 'vue'

const activeIndex = ref(0)
const slideDirection = ref('next')

function prevSlide() {
    slideDirection.value = 'prev'
    activeIndex.value = (activeIndex.value - 1 + total) % total
}

function nextSlide() {
    slideDirection.value = 'next'
    activeIndex.value = (activeIndex.value + 1) % total
}
</script>
```

---

## Quy Tắc 4: `Preserve Product Gallery with Bottom Thumbnail Previews` (`[=] GIỮ NGUYÊN 100%`)

### Nguyên tắc:
- Riêng phần **Gallery chi tiết sản phẩm có hàng 6 ảnh preview/thumbnail nhỏ ở dưới** (`resources/Frontend/js/Components/ProductDetail/ProductGallery.vue` và phần chọn mẫu vải trong `resources/Frontend/js/Components/ProductDetail/ProductInfoPanel.vue`) đã được thiết kế tối ưu cho thao tác xem chi tiết chất liệu rèm.
- Trong mọi đợt chạy `/opsx-animation` hay `/ui-skill`, `ProductGallery.vue` và `ProductInfoPanel.vue` luôn nằm trong bảng **`[=] GIỮ LẠI GÌ (KEEP AS-IS)`** — **tuyệt đối giữ nguyên 100% như source hiện tại**.
