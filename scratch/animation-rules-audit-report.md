# Báo Cáo Kiểm Toán 4 Quy Tắc Vàng Animation (`/opsx-animation` & `/ui-skill`)

- **Thư mục dự án**: `D:\bed-hhmaker`
- **Thời gian quét**: `2026-11-06 18:42:19`
- **Chế độ**: `Strict Read-Only (Không tự ý sửa/xóa file)`
- **Kết luận tổng thể**: **CẦN XỬ LÝ (4 HẠNG MỤC)** (`[+] ADD`: 3 | `[-] REMOVE`: 1 | `[=] KEEP`: 6)

---

## 1. Bảng `[+] THÊM GÌ (ADD)` — Các hiệu ứng trượt ngang / micro-interaction cần bổ sung

| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết cần thêm (`[+] ADD`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 3 (`Directional Horizontal Slide`) | `resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue:1` | Thêm `slideDirection = ref('next')` và đổi `<Transition>` của Carousel 4 thẻ Sản phẩm nổi bật Trang chủ (nút `<` / `>`) sang trượt ngang `translate-x`. |
| 2 | Quy tắc 3 (`Directional Horizontal Slide`) | `resources/Frontend/js/Pages/Home/Components/ProductsSection.vue:1` | Thêm `slideDirection = ref('next')` và đổi `<Transition>` của 3 hàng Carousel Danh mục sản phẩm Trang chủ (nút `<` / `>`) sang trượt ngang `translate-x`. |
| 3 | Quy tắc 3 (`Directional Horizontal Slide`) | `resources/Frontend/js/Components/Product/RelatedProducts.vue:1` | Thêm `slideDirection = ref('next')` và đổi `<Transition>` của Slider 4 Sản phẩm liên quan trang Chi tiết (nút `<` / `>`) sang trượt ngang `translate-x`. |

---

## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các hiệu ứng `opacity-0` / chồng chéo animation cần gỡ bỏ

| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết vi phạm | Hành động gỡ bỏ (`[-] REMOVE`) |
| :--- | :--- | :--- | :--- | :--- |
| 1 | Quy tắc 3 (`Directional Horizontal Slide`) | `resources/Frontend/js/Pages/Product/Index.vue:46` | Lưới phân trang Danh mục sản phẩm kèm `ProductCard.vue` & `ProductPagination.vue` (nút `<` / `>`) đang dùng `opacity-0` fade khi bấm chuyển slide/trang. | Bỏ hoàn toàn `opacity-0` trong `<Transition>` của slider. |

---

## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP AS-IS)` — Các thành phần giữ nguyên 100% theo quy chuẩn

| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 4 (`Preserve Product Gallery — KEEP AS-IS`) | `resources/Frontend/js/Components/Product/ProductGallery.vue` | GIỮ NGUYÊN 100%: Gallery chi tiết sản phẩm `624×600px` kèm hàng ảnh preview nhỏ ở dưới. |
| 2 | Quy tắc 4 (`Preserve Product Gallery — KEEP AS-IS`) | `resources/Frontend/js/Components/Product/VariantSelector.vue` | GIỮ NGUYÊN 100%: Bảng lựa chọn tham khảo chất liệu, màu sắc & tùy chỉnh sản phẩm. |
| 3 | Quy tắc 4 (`Preserve Product Gallery — KEEP AS-IS`) | `resources/Frontend/js/Components/Product/SpecTable.vue` | GIỮ NGUYÊN 100%: Bảng thông số kỹ thuật sản phẩm chi tiết. |
| 4 | Quy tắc 2 (`Zero White-Out Scroll Reveal`) | `resources\Frontend\js\Composables\useScrollReveal.js` | Chuẩn 100%: Chỉ trượt nhẹ `translate-y-4 -> translate-y-0` với `transition-transform`, không dùng `opacity-0`. |
| 5 | Quy tắc 3 (`Directional Horizontal Slide translate-x`) | `resources/Frontend/js/Pages/Product/Show.vue` | Chuẩn 100%: Tabs Chi tiết & Thông số sản phẩm (trượt ngang `tabSlideDirection`) đã trượt ngang `translate-x` theo hướng bấm (`next`/`prev`), không dùng `opacity-0`. |
| 6 | Quy tắc 1 (`Single-Animation-Per-Element`) | `resources/Frontend/js/{Layouts,Pages,Components}` | Chuẩn 100%: Mỗi phần tử chỉ có duy nhất 1 animation chủ đạo, không lồng `<TransitionGroup>` kép. |

---

## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)

> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên (đặc biệt `ProductGallery.vue` có hàng preview nhỏ ở dưới luôn nằm trong mục `[=] GIỮ LẠI GÌ`).
> Sau khi bạn **Confirm**, đoạn hội thoại trung tâm sẽ xuất bộ **Handoff Prompts** để đem sang các đoạn hội thoại con thực thi.
