# Báo Cáo Kiểm Toán 4 Quy Tắc Vàng Animation (`/opsx-animation` & `/ui-skill`)

- **Thư mục dự án**: `D:\bed-hhmaker`
- **Thời gian quét**: `2026-11-05 14:19:25`
- **Chế độ**: `Strict Read-Only (Không tự ý sửa/xóa file)`
- **Kết luận tổng thể**: **PASS 100% (4 QUY TẮC VÀNG ANIMATION)** (`[+] ADD`: 0 | `[-] REMOVE`: 0 | `[=] KEEP`: 2)

---

## 1. Bảng `[+] THÊM GÌ (ADD)` — Các hiệu ứng trượt ngang / micro-interaction cần bổ sung

> Không có hạng mục nào bị thiếu (`0` mục cần thêm).

---

## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các hiệu ứng `opacity-0` / chồng chéo animation cần gỡ bỏ

> Tuyệt vời! Không có hiệu ứng `opacity-0` sai quy chuẩn hay chồng chéo animation nào (`0` mục cần bỏ).

---

## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP AS-IS)` — Các thành phần giữ nguyên 100% theo quy chuẩn

| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |
| :--- | :--- | :--- | :--- |
| 1 | Quy tắc 2 (`Zero White-Out Scroll Reveal`) | `resources\Frontend\js\Composables\useScrollReveal.js` | Chuẩn 100%: Chỉ trượt nhẹ `translate-y-4 -> translate-y-0` với `transition-transform`, không dùng `opacity-0`. |
| 2 | Quy tắc 1 (`Single-Animation-Per-Element`) | `resources/Frontend/js/{Layouts,Pages,Components}` | Chuẩn 100%: Mỗi phần tử chỉ có duy nhất 1 animation chủ đạo, không lồng `<TransitionGroup>` kép. |

---

## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)

> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên (đặc biệt `ProductGallery.vue` có hàng preview nhỏ ở dưới luôn nằm trong mục `[=] GIỮ LẠI GÌ`).
> Sau khi bạn **Confirm**, đoạn hội thoại trung tâm sẽ xuất bộ **Handoff Prompts** để đem sang các đoạn hội thoại con thực thi.
