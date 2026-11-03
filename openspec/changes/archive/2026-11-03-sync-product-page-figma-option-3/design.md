# Design: Chuẩn Hóa Trang Sản Phẩm Khớp 100% Theo Figma Option 3

## Context
Xem chi tiết tại `proposal.md` và `specs/product-catalog/spec.md`.  
Người dùng chọn **Figma Option 3 (Sidebar Layout - Node `2346:669`)** cho trang Sản phẩm.  
Bản vẽ Option 3 gồm:
- Header mở đầu (`2346:702`): Lề trái 144px, Eyebrow 14px `#84ADFF`, Tiêu đề 38px Bold.
- Sidebar Layout (`2346:994`): Lọt lòng 1152px, khoảng cách giữa Sidebar và Lưới là 48px.
  + Sidebar (`2346:995`): 240px gồm 2 card riêng biệt (464px & 156px), khoảng cách 12px.
  + Khu vực kết quả (`2346:1015`): 867px gồm Toolbar 20px, Lưới 3 cột (thẻ 273x419px, gap 24px), và Phân trang 57px.
- Banner CTA (`2346:740`): 1152x183px gradient.

## Goals / Non-Goals

**Goals:**
- Tinh chỉnh `ProductFilter.vue` để đảm bảo 2 card tách biệt rõ ràng, Card 1 cao 464px, Card 2 cao 156px, khoảng cách 12px.
- Tinh chỉnh các mục "LỌC THEO NHU CẦU": text bên trái, checkbox vuông bo góc 4px bên phải (`justify-between`), khớp 100% Figma `2390:1536`.
- Đảm bảo lọt lòng `max-w-[1152px]` chuẩn xác trên desktop 1440px.
- Giữ nguyên các chức năng tìm kiếm, lọc danh mục, lọc nhu cầu, sắp xếp và phân trang hoạt động mượt mà.

**Non-Goals:**
- Không thay đổi sang Option 1 (4 cột không sidebar).
- Không sửa backend hay cơ sở dữ liệu.

## Decisions

### 1. Giữ nguyên layout 2 cột Sidebar nhưng tinh chỉnh độ chi tiết theo Figma
- Bố trí Sidebar 240px cố định sticky trên Desktop và offcanvas drawer trên Mobile.
- Tách bạch cấu trúc 2 thẻ card (`bg-[#30374F] rounded-[12px] p-4`) với `gap-3` (12px).
- Sắp xếp checkbox nhu cầu: Nhãn text bên trái, ô checkbox 16x16px bên phải với `justify-between`.

## Risks / Trade-offs

- **[Risk]** Màn hình từ 1024px đến 1200px có thể bị chật nếu sidebar 240px + grid 867px (tổng 1155px).
  - **Mitigation**: Áp dụng responsive: trên `lg` (1024-1279px) dùng grid 2 cột linh hoạt, trên `xl` ($\ge 1280\text{px}$) đạt chuẩn 3 cột 867px hoàn chỉnh.
