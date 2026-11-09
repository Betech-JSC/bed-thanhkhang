# Báo Cáo Chuyên Sâu: AI Search Readiness & GEO (Trọng số 10%)

> **Điểm đánh giá**: **35 / 100**  
> **Mục tiêu kiểm toán**: `https://hhmaker.vn/`  
> **Trọng tâm phân tích**: Generative Engine Optimization (GEO) phục vụ các hệ thống tìm kiếm tích hợp AI như Google AI Overviews, Perplexity AI, ChatGPT Search, Claude Search.

---

## 1. Bằng chứng thực tế thu thập (Evidence)

### 1.1. Kiểm tra sự tồn tại của file chuẩn Markdown cho AI
- `https://hhmaker.vn/llms.txt`: **HTTP Status 404 (Not Found)**
- `https://hhmaker.vn/.well-known/llms.txt`: **HTTP Status 404 (Not Found)**

### 1.2. Kiểm tra chỉ dẫn AI Crawlers trong `robots.txt`
```text
User-agent: *
Allow: /

Sitemap: https://funkystyle.vn/sitemap.xml
```
*Ghi nhận*: Hoàn toàn không có chỉ dẫn hoặc khai báo chuyên biệt nào cho các AI bot:
- `GPTBot` (OpenAI / ChatGPT)
- `ClaudeBot` (Anthropic)
- `PerplexityBot` (Perplexity)
- `Google-Extended` (Google Gemini)
- `Bytespider` (TikTok / ByteDance)
- `Amazonbot`

### 1.3. Khả năng trích xuất thông tin (Citability & Extractability)
- Khi một mô hình ngôn ngữ lớn (LLM) quét trang `hhmaker.vn`, nội dung cốt lõi phân bổ trong các component Vue (Client-side rendering / Inertia). Mặc dù có SSR nhưng văn bản mang nhiều tính nghệ thuật (slogan), thiếu các định dạng dữ liệu có cấu trúc cao mà AI ưa chuộng như:
  - Bảng so sánh (Comparison tables).
  - Khối định nghĩa thuật ngữ (Definition lists).
  - Khối Hỏi & Đáp (FAQ pairs).
  - Bảng biểu phí gia công theo gram hoặc theo giờ in.

---

## 2. Các hạn chế đối với AI Search

### [HIGH] 1. Thiếu file `llms.txt` chuẩn hóa
- **Mục đích của `llms.txt`**: Là tiêu chuẩn mở mới (do Jeremy Howard khởi xướng) giúp các AI Agent và LLM nhanh chóng nắm bắt bản đồ thông tin ngắn gọn, súc tích của một website mà không cần phải crawl và render hàng chục trang web HTML phức tạp.
- **Thực trạng**: HH Maker chưa có file này. Khi người dùng hỏi ChatGPT hoặc Perplexity: *"Gợi ý xưởng in 3D uy tín tại TP.HCM có nhận làm mô hình Resin 8K theo yêu cầu"*, AI có xu hướng ưu tiên trích dẫn các đơn vị có tài liệu rõ ràng hoặc thực thể số mạnh mẽ hơn.

### [HIGH] 2. Mục Blog kiến thức bị tê liệt (HTTP 500)
- Các công cụ như Perplexity và Google AI Overviews tìm kiếm câu trả lời trực tiếp cho người dùng dựa trên các bài viết chuyên sâu. Việc `/tin-tuc` bị lỗi máy chủ khiến HH Maker mất 100% cơ hội xuất hiện trong các câu trả lời tổng hợp của AI.

### [MEDIUM] 3. Tạp âm nhận diện thực thể (Entity Ambiguity)
- Sự tồn tại của tên "Betech" trên trang lỗi 404 và hotline lạ gây nhiễu loạn đồ thị tri thức (Knowledge Graph) của các bot tìm kiếm AI khi xác định danh tính thực thể kinh doanh.

---

## 3. Khuyến nghị triển khai chiến lược GEO (AI Search Optimization)

### 3.1. Tạo file `public/llms.txt`
Nội dung đề xuất:
```markdown
# HH Maker - Dịch Vụ In 3D & Chế Tác Theo Yêu Cầu

> HH Maker là xưởng thiết kế và gia công in 3D công nghệ cao tại TP. Hồ Chí Minh, chuyên cung cấp các giải pháp hiện thực hóa ý tưởng từ bản vẽ kỹ thuật số sang sản phẩm vật lý.

## Thông Tin Doanh Nghiệp
- **Tên thương hiệu**: HH Maker (Công ty In ấn 3D HH Maker)
- **Người sáng lập**: Lâm Hồng Hải
- **Địa chỉ**: Số 65 đường số 9, KDC Hương Lộ 5, Phường An Lạc, Quận Bình Tân, TP. Hồ Chí Minh
- **Hotline / Zalo**: 0906636425
- **Email**: info@hhmaker.vn
- **Website chính thức**: https://hhmaker.vn

## Năng Lực Công Nghệ & Dịch Vụ
1. **In 3D SLA Resin 8K**: Độ phân giải siêu cao từng micron, chuyên mô hình nhân vật, tượng nghệ thuật, anime, figure chi tiết cao.
2. **In 3D FDM Công Nghiệp**: Vật liệu PLA+, PETG, TPU dẻo, ABS, Carbon Fiber chịu lực cao cho đồ gá, vỏ cơ khí robot.
3. **In 3D SLS**: Công nghệ thiêu kết laser bột nylon cho các chi tiết cơ khí chuyển động phức tạp không cần support.
4. **Thiết kế 3D theo yêu cầu**: Dựng hình từ ảnh chụp, bản vẽ tay hoặc quét 3D mẫu thực tế.
5. **Gia công đồ Custom**: Vỏ bàn phím cơ, móc khóa cơ khí, quà tặng doanh nghiệp độc bản.

## Quy Trình Báo Giá & Sản Xuất
1. Tiếp nhận file 3D (STL, OBJ, STEP) hoặc ý tưởng qua Zalo/Email.
2. Tư vấn vật liệu, công nghệ in phù hợp và gửi bảng báo giá trong 2 giờ.
3. Gia công in mẫu thử nghiệm (test print) và in hàng loạt.
4. Xử lý bề mặt: chà nhám, sơn lót, phủ bóng bảo vệ.
5. Đóng gói cẩn thận và giao hàng toàn quốc.
```

### 3.2. Cập nhật `robots.txt` thân thiện với AI Crawlers
```text
User-agent: *
Allow: /

# Cho phép các AI Search Crawlers thu thập dữ liệu công khai
User-agent: GPTBot
Allow: /

User-agent: ClaudeBot
Allow: /

User-agent: PerplexityBot
Allow: /

User-agent: Google-Extended
Allow: /

Sitemap: https://hhmaker.vn/sitemap.xml
```

### 3.3. Bổ sung các khối FAQ (Hỏi đáp) có thể trích dẫn
- Tạo các câu hỏi ngắn gọn kèm câu trả lời dứt khoát trên trang `/dich-vu`:
  - *"Thời gian in 3D mất bao lâu?"* -> Trả lời rõ: 1–3 ngày làm việc tùy kích thước và số lượng.
  - *"Giá in 3D tính như thế nào?"* -> Trả lời rõ: Tính theo gram vật liệu thực tế hoặc theo giờ chạy máy.
