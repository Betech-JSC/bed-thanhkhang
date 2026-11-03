# Proposal: Sync Contact Page with Figma Design (Node 2283:6726)

## Problem Statement
Trang liên hệ tư vấn (`/lien-he` / `resources/Frontend/js/Pages/Contact.vue`) cần được chuẩn hóa đồng bộ 100% theo bản thiết kế Figma (File Key: `otk6uzZmitaXPdZZZiRG1K`, Node `2283:6726` "Liên hệ tư vấn").

Các điểm cần rà soát và tinh chỉnh:
1. Hero Section (Node 2308:8195): Gradient `linear-gradient(0deg, #111322 20%, #2970FF 94%)`, padding, typography Inter (38px/46px), badge `#D1E0FF`, dẫn nhập `#D1E0FF`, căn chỉnh artwork robot 3D (`hh_maker-01_2.png`).
2. 3 Thẻ Kính kênh liên hệ (Node 2308:8241): Chuẩn hóa nền glassmorphism `rgba(255, 255, 255, 0.2)` (`bg-white/20`), viền `#5D6B98`, backdrop blur 10px, padding 28px, icon 28px (Zalo, Hotline, Email), typography title 24px/29px, subtitle 16px/26px.
3. Section Form Tư vấn 2 Cột Bất Đối Xứng (Node 2283:6767):
   - Cột trái: Lời mời (Node 2283:6768), badge `#84ADFF`, H2 "CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI." 36px/42px, mô tả 16px/26px, artwork điêu khắc xanh 380x384px bo góc 12px.
   - Cột phải: Form tư vấn 708px (Node 2283:6775), input height 52px, nền `rgba(255, 255, 255, 0.2)`, viền `#5D6B98`, 3 pill buttons dịch vụ ("Thiết kế 3D", "Gia công 3D", "Làm đồ custom"), textarea 136px, nút Submit 56px gradient `#2970FF` -> `#00359E`.
4. FAQ Accordion (Node 2330:8413): Căn giữa 1152px, background thẻ câu hỏi `rgba(255, 255, 255, 0.2)`, viền `#5D6B98`, icon plus xoay toggle mượt mà.
5. Bảo đảm Zero-Legacy brand leak và responsive hoàn hảo (1440px Desktop, Tablet, 375px Mobile).

## Success Criteria
- [x] Giao diện khớp chính xác 100% thông số Figma MCP.
- [x] `yarn frontend:build` thành công cả client & ssr.
- [x] Playwright E2E test `tests/e2e/contact-page.spec.ts` pass 100%.
