# Spec Delta: header-category-flyout-menu

## Purpose

Cung cấp cấu trúc menu điều hướng đa cấp (Multi-Level Flyout Menu) bay ngang cho danh mục sản phẩm trên Desktop Header và hệ thống Accordion 2 cấp mượt mà trên Mobile Drawer, hỗ trợ trải nghiệm khám phá sản phẩm tối ưu.

## ADDED Requirements

### Requirement: Multi-Level Flyout Menu Cấp 2 Bay Ngang Trên Desktop
Trên màn hình Desktop (viewport width >= 1024px), thanh điều hướng Header SHALL hỗ trợ menu đa cấp 2 tầng bay ngang cho danh mục sản phẩm:
1. Menu cấp 1 hiển thị hộp dọc chiều rộng chuẩn 280px (`w-[280px]`), nền `#1A1F36]/95` với `backdrop-blur-md`, viền `border-[#30374F]`, bo góc `rounded-xl`, shadow `shadow-[0_16px_40px_rgba(0,0,0,0.6)]`.
2. Menu cấp 1 CHỈ hiển thị các danh mục cha gốc (`parent_id = 0` hoặc null) và link "Xem tất cả sản phẩm".
3. Bất kỳ danh mục cha nào có danh mục con (`children.length > 0`) SHALL hiển thị biểu tượng mũi tên sang phải (chevron-right `>`) ở phía bên phải thẻ mục.
4. Khi người dùng rê chuột (hover) vào danh mục cha có con, hệ thống SHALL bung mở khung menu cấp 2 bay ngang sang bên phải (`left-full top-0 pl-1.5`) với cùng phong cách Dark Tech (nền `#1A1F36]/95`, viền `#30374F`, bo góc `rounded-xl`, shadow đồng bộ).
5. Menu cấp 2 hiển thị đầy đủ các danh mục con trực thuộc danh mục cha đó. Nhấp vào danh mục con SHALL điều hướng đến URL danh mục tương ứng kèm query parameter category slug.

#### Scenario: Rê chuột vào danh mục cha có danh mục con trên Desktop
- **WHEN** người dùng rê chuột vào tab "Sản phẩm" rồi rê chuột vào mục "Móc khoá & Phụ kiện" hoặc "Trang trí & Đồ dùng"
- **THEN** menu cấp 2 bay ngang bung mở sang bên phải hiển thị danh sách các mục con tương ứng, có icon chevron-right định hướng và hiệu ứng hover đổi màu nền sang `#222842`.

---

### Requirement: Hover Bridge Vùng Đệm Chống Đứt Đoạn Hover Giữa Cấp 1 và Cấp 2
Hệ thống SHALL thiết lập vùng đệm cầu nối chuột (Hover Bridge) liên tục giữa khung menu cấp 1 và khung flyout cấp 2:
1. Khoảng cách giữa menu cấp 1 và cấp 2 sử dụng padding đệm `pl-1.5` (hoặc lớp pseudo liên kết vô hình), đảm bảo con trỏ chuột khi di chuyển xiên hoặc chéo từ item cấp 1 sang container cấp 2 không bị lọt ra ngoài vùng hover.
2. Trạng thái hover của menu cấp 2 không bị chớp nháy (flicker) hay tự động đóng đột ngột khi người dùng di chuyển chuột từ danh mục cha sang bảng chọn danh mục con.

#### Scenario: Di chuyển chuột từ menu cấp 1 sang menu cấp 2
- **WHEN** người dùng di chuyển con trỏ chuột từ item "Móc khoá & Phụ kiện" sang khung menu con bên phải
- **THEN** menu cấp 2 giữ nguyên trạng thái hiển thị mở, không bị mất hover hay biến mất đột ngột.

---

### Requirement: Accordion 2 Cấp Danh Mục Sản Phẩm Trên Mobile Drawer
Trên màn hình thiết bị di động (viewport width < 1024px), thanh điều hướng trong Mobile Drawer SHALL hỗ trợ phân cấp Accordion 2 tầng:
1. Khi mở menu "Sản phẩm" (cấp 1), danh sách hiển thị các danh mục cha kèm liên kết "Xem tất cả sản phẩm".
2. Danh mục cha nào có danh mục con SHALL có nút hoặc vùng bấm toggle mở rộng với icon chevron xuống/lên.
3. Khi người dùng nhấp vào mục cha có con, danh sách danh mục con bên dưới SHALL trượt mở ra với hiệu ứng chuyển tiếp trơn tru, được thụt lề thụt dòng (indent `pl-6`) và phân tách rõ ràng.
4. Nhấp vào danh mục con SHALL đóng Mobile Drawer và điều hướng đến trang sản phẩm đã lọc theo danh mục đó.

#### Scenario: Mở Accordion danh mục con trên Mobile Drawer
- **WHEN** người dùng mở Mobile Drawer, nhấn vào tab "Sản phẩm" và nhấn vào danh mục cha "Trang trí & Đồ dùng"
- **THEN** danh sách 3 danh mục con ("Ly", "Lọ cắm hoa để bàn", "Đèn") trượt xổ xuống với thụt lề `pl-6`, icon chevron xoay tương ứng và nhấp chọn sẽ điều hướng đúng danh mục.

---

### Requirement: Đồng Bộ Dữ Liệu Cây Danh Mục Đa Cấp Từ Backend Inertia Shared Props
Hệ thống Backend và Frontend SHALL đồng bộ cấu trúc cây danh mục cha - con chặt chẽ:
1. Backend `HandleInertiaFrontendRequests.php` truy vấn `ProductCategory` chỉ lấy danh mục cha gốc (`->whereParent()`) và nạp sẵn quan hệ con (`->with(['children', 'nodes'])`), chuyển đổi qua `transform()`.
2. Frontend `Header.vue` cung cấp cơ chế phòng vệ (defensive grouping): Nếu dữ liệu truyền xuống có bất kỳ danh mục nào mang `parent_id > 0`, Frontend tự động gom danh mục đó vào mảng `children` của danh mục cha tương ứng.
3. Mọi văn bản tiêu đề, mô tả và slug đều được dịch tự động theo locale đang kích hoạt (`vi` hoặc `en`).

#### Scenario: Kiểm tra dữ liệu danh mục phân cấp đa ngôn ngữ
- **WHEN** người dùng tải trang web ở locale `vi` hoặc chuyển sang `en`
- **THEN** dữ liệu menu cấp 1 chỉ chứa các danh mục cha, các danh mục con nằm trong mảng `children` và hiển thị đúng ngôn ngữ tương ứng.
