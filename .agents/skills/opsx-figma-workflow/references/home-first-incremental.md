# Kịch Bản 2: Quy Trình Khi Figma Mới Chỉ Có Trang Home (`Home-First Incremental`)

Trong thực tế sản xuất agency/product, rất nhiều dự án bắt đầu code ngay khi khách hàng vừa duyệt xong **Trang Chủ (Home Page)** trên Figma, trong khi các trang con (Giới thiệu, Sản phẩm, Chi tiết sản phẩm, Tin tức, Liên hệ...) sẽ được thiết kế cuốn chiếu sau.

Quy trình này đảm bảo dựng Trang Home nhanh nhất nhưng vẫn thiết lập được nền móng **Design System & Shared Components** vững chắc để các trang sau kế thừa 100%, không phải đập đi xây lại.

---

## Giai Đoạn A: Rút Trích Hạt Nhân Từ Trang Home (`setup-core-from-home`)

Không lao vào code một file `Home.vue` / `page.tsx` nguyên khối dài hàng nghìn dòng. Hãy mở change đầu tiên mang tên `setup-core-from-home`:

1. **Quét tổng quan Frame Home (Desktop & Mobile)**:
   - Gọi `get_figma_data` với `depth: 2` trên frame Home Desktop và Home Mobile để lập bản đồ tất cả các `nodeId` section.
2. **Trích xuất Design Tokens Cốt Lõi**:
   - Đọc style từ các section tiêu biểu trên Home để khai báo ngay vào `tailwind.config.*` & CSS gốc: bảng màu thương hiệu, font-family (ép kế thừa cho `button/input/select/textarea`), thang đo heading `H1`–`H4`, body text, container grid và border-radius chuẩn.
3. **Tách Layout Shell & Shared Components Dùng Lại**:
   - Đối chiếu `graphify-out/graph.json` để tận dụng hạ tầng sẵn có.
   - Tách riêng `Header`, `Footer`, `MainLayout` ra tầng `Layouts/`.
   - Nhận diện các khối chắc chắn sẽ tái sử dụng ở trang con để dựng ngay thành **Shared Components** có `props` sạch sẽ:
     - `Button` / `CtaLink`
     - `SectionHeader` (eyebrow / title / subtitle / action link)
     - `ProductCard` / `CategoryCard`
     - `PostCard` / `NewsCard`
     - `ContactBanner` / `PartnerCarousel`
   - Tải toàn bộ SVG icons và hình ảnh nền chuẩn từ Figma bằng `download_figma_images`.
4. **Kiểm chứng & Ghi nhận AST**:
   - Chạy build 0 lỗi -> chạy `graphify . --code-only` để toàn bộ Tokens, Layout Shell và Shared Components xuất hiện trên `graphify-out/graph.json`.

---

## Giai Đoạn B: Hoàn Thiện & Nghiệm Thu Trang Home (`feat-page-home`)

1. Mở change `feat-page-home`.
2. Đi sâu `get_figma_data` vào từng `nodeId` section của Trang Home (`HeroBanner`, `AboutIntro`, `ProductShowcase`, `WhyChooseUs`, `LatestNews`, `QuickContact`...).
3. Lắp ráp trang Home từ các Shared Components và các Home-specific Sections gọn nhẹ, kết nối đúng dữ liệu thật từ Controller/Props.
4. Chạy build thật 0 lỗi + chạy `graphify . --code-only`.
5. Giao `QA Verifier` mở **Playwright MCP** chụp ảnh đối chiếu từng section của Trang Home ở cả Desktop (`1440x900`) và Mobile (`375x812`) so với Figma. Khi đạt độ chính xác cao và 0 lỗi console/overflow, chạy `openspec archive` cho `setup-core-from-home` và `feat-page-home`.

---

## Giai Đoạn C: Mở Rộng Cuốn Chiếu Các Trang Tiếp Theo (`feat-page-<slug>`)

Khi Figma bàn giao thêm bất kỳ trang mới nào (ví dụ `about`, `products`, `product-detail`, `posts`, `contact`):

### Nguyên Tắc Vàng: "EXTEND, NEVER DUPLICATE" (Chỉ Mở Rộng, Cấm Tạo Trùng)
1. **Đọc `graphify-out/graph.json` đầu tiên**: Liệt kê tất cả components đã dựng từ Trang Home và các trang trước đó.
2. **Phân loại UI trên trang mới thành 3 nhóm**:
   - **Nhóm 1 — Dùng lại 100%**: `Header`, `Footer`, `Button`, `Breadcrumb`, `ProductCard`, `PostCard`... -> Import trực tiếp, tuyệt đối không viết lại CSS.
   - **Nhóm 2 — Biến thể nhẹ của Component cũ**: Ví dụ `ProductCard` ở trang Danh sách có thêm dòng mã CAS hoặc nút tải COA -> Mở rộng `ProductCard` bằng optional prop (`:show-meta="true"` hoặc named `<slot>`) sao cho giá trị mặc định giữ nguyên 100% giao diện trên Trang Home.
   - **Nhóm 3 — Section/Token mới hoàn toàn**: Nếu Figma xuất hiện một token màu trạng thái mới hoặc một khối mới (ví dụ bảng thông số kỹ thuật hóa chất, bộ lọc danh mục), bổ sung token đó vào `tailwind.config.*` và tạo component mới đặt đúng chuẩn tên miền trong `Components/`.
3. **Kiểm tra hồi quy (Regression Check)**: Khi sửa đổi bất kỳ Shared Component nào ở Nhóm 2, `QA Verifier` phải mở lại cả Trang Home trên Playwright MCP để đảm bảo không phát sinh lỗi vỡ giao diện hồi quy.

---

## Mẫu Prompt Thực Chiến Giao Việc Cuốn Chiếu Cho Trang Tiếp Theo

> Sử dụng quy trình `.agents/skills/opsx-figma-workflow` (kịch bản `home-first-incremental`) để triển khai trang `<TÊN_TRANG>` từ Figma URL/nodeId: `<FIGMA_LINK_HOẶC_NODE_ID>`.
> Yêu cầu bắt buộc:
> 1. Đọc `graphify-out/graph.json` và `tailwind.config.*` trước tiên để tái sử dụng tối đa Tokens, Layout Shell và Shared Components đã dựng ở Trang Home (chỉ *extend* qua props/slots an toàn, tuyệt đối không tạo component trùng lặp).
> 2. Quét nông frame Figma ở `depth: 2` rồi mới đọc chi tiết từng `nodeId` section và tải SVG/ảnh bằng `download_figma_images`.
> 3. Tạo và thực thi OpenSpec change `feat-page-<slug>` qua Subagents (`Frontend Worker` build 0 lỗi + `graphify . --code-only` -> `QA Verifier` kiểm tra Playwright MCP Desktop & Mobile kèm kiểm tra không vỡ hồi quy Trang Home).
