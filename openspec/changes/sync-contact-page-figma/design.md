# Design: Sync Contact Page with Figma Specification

## Architectural Context
Trang Contact hoạt động dưới mô hình Inertia Vue 3 (`resources/Frontend/js/Pages/Contact.vue`), nhận dữ liệu từ `ContactController` của backend và hiển thị trên layout frontend.

## Visual Design Specification (Figma Node 2283:6726)
### 1. Hero Section (Node 2308:8195)
- **Container**: Max-width 1240px (Figma frame 1440px, padding 144px).
- **Background**: `linear-gradient(0deg, #111322 20%, #2970FF 94%)`.
- **Badge**: "KẾT NỐI VỚI HH MAKER", color `#D1E0FF`, font Inter 16px/22px, tracking-wider uppercase.
- **Title**: "KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN", color `#FAFAFA`, font Inter 38px/46px (responsive 28px-38px), bold, uppercase.
- **Description**: color `#D1E0FF`, font Inter 16px/26px, max-width 519px.
- **Artwork Robot 3D**: `hh_maker-01_2.png`, width 469px (Figma), positioned top/right with proper overflow handling on smaller viewports.

### 2. Contact Channels - 3 Glassmorphism Cards (Node 2308:8241)
- **Container**: 3 columns grid, gap 24px, negative margin to overlap Hero.
- **Card Styling**:
  - `background: rgba(255, 255, 255, 0.2)` (`bg-white/20`)
  - `border: 1px solid #5D6B98`
  - `backdrop-filter: blur(10px)`
  - `border-radius: 12px`
  - `padding: 28px`
  - `min-height: 193px`
  - Hover: border primary, glow effect.
- **Content**:
  1. Zalo: Icon 28px, Title "Tư vấn qua Zalo" (24px/29px, #FAFAFA, font-weight 600), Desc "Trao đổi ý tưởng, hình ảnh và yêu cầu sản phẩm" (16px/26px, #B9C0D4).
  2. Phone: Icon 28px, Title "0906636425", Desc "Gọi HH Maker để trao đổi trực tiếp về dự án của bạn".
  3. Email: Icon 28px, Title "info@hhmaker.vn", Desc "Gửi mô tả chi tiết và tài liệu tham khảo của bạn".

### 3. Consultation Form - Asymmetric 2 Columns (Node 2283:6767)
- **Left Column (Lời mời)**:
  - Badge: "TỪ Ý TƯỞNG ĐẾN HIỆN THỰC", color `#84ADFF`, 16px/22px.
  - H2: "CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.", color `#FAFAFA`, 36px/42px, bold.
  - Mô tả: "Điền thông tin để HH Maker hiểu thêm về nhu cầu của bạn và có cơ sở trao đổi phương án phù hợp.", color `#B9C0D4`, 16px/26px.
  - Image: Artwork Điêu khắc xanh `dieu_khac_xanh.png`, 380px x 384px, border-radius 12px, border `#5D6B98`/40.
- **Right Column (Biểu mẫu)**:
  - Width 708px desktop, card container with `rgba(255, 255, 255, 0.04)` or `rgba(255, 255, 255, 0.2)` inner fields.
  - H3: "Thông tin yêu cầu" (28px/33px, bold, #FAFAFA).
  - Input fields: height 52px, `background: rgba(255, 255, 255, 0.2)`, `border: 1px solid #5D6B98`, `border-radius: 8px`, placeholder `#B9C0D4`.
  - Service selection pill buttons:
    - Active: `bg-[#243971]`, `border-[#528BFF]`, text `#FAFAFA`, 12px font size, height 39px, rounded 8px.
    - Inactive: `bg-[#1A1C32]`, `border-[#2C3049]`, text `#FAFAFA` / `#B9C0D4`.
  - Textarea: height 136px, `rgba(255, 255, 255, 0.2)`, border `#5D6B98`.
  - Submit button: height 56px, `linear-gradient(90deg, #2970FF 0%, #00359E 89%)`, rounded 8px, text 16px font-semibold #FAFAFA.

### 4. FAQ Accordion (Node 2330:8413)
- Badge: "CÂU HỎI THƯỜNG GẶP", color `#84ADFF`, 14px/20px.
- H2: "Giải đáp trước khi liên hệ", color `#FAFAFA`, 32px/37px, bold.
- FAQ cards: `background: rgba(255, 255, 255, 0.2)`, `border: 1px solid #5D6B98`, rounded 12px.
- Question row: min-height 72px, padding 24px, font 18px/22px font-medium #FAFAFA, plus icon 24px.
