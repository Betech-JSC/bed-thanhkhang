---
name: opsx-bootstrap
description: Trình cài đặt & đồng bộ siêu nhẹ 1 giây (Pure 1-Second Lightweight Installer) cho Bộ Skill Chuẩn Toàn Diện của Team (/opsx-cms-sync, /opsx-pixel-perfect, /opsx-animation, /ui-skill, /opsx-audit-handoff, /opsx-figma-workflow, /opsx-deploy). Khi người dùng gọi "/opsx-bootstrap", Agent CHỈ CHẠY DUY NHẤT 1 LỆNH bootstrap.sh (hoàn tất trong 1 giây) rồi báo cáo ngay, tuyệt đối không đọc/sửa thêm file hay chạy tác vụ nặng.
---

# OPSX Bootstrap — Trình Cài Đặt & Đồng Bộ Siêu Nhẹ 1 Giây (`Pure 1-Second Installer`)

Skill này đóng vai trò thuần túy là **Trình Cài Đặt & Đồng Bộ File Siêu Tốc (`< 1 giây`)** để chép toàn bộ hệ sinh thái Skills, Workflows, Claude Commands, `AGENTS.md` và `openspec/config.yaml` sang dự án hiện tại hoặc dự án mới.

> ⚡ **QUY TẮC BẤT BIẾN (1 LỆNH — 1 GIÂY — KHÔNG LÀM THÊM)**:
> Khi người dùng gọi `/opsx-bootstrap`, mục tiêu duy nhất là **chép bộ skill vào dự án trong 1 giây**.
> - **TUYỆT ĐỐI KHÔNG** chạy `graphify . --code-only` mặc định trong lúc bootstrap.
> - **TUYỆT ĐỐI KHÔNG** đi đọc `composer.json`, `package.json` hay dùng `edit_file` sửa thủ công `openspec/config.yaml` và `AGENTS.md` sau khi chạy script (bản thân `bootstrap.sh` đã tự động đồng bộ sẵn `openspec/config.yaml` và `AGENTS.md` trong `0.1 giây`).
> - **TUYỆT ĐỐI KHÔNG** chạy gộp bất kỳ tác vụ kiểm toán UI, CMS hay build nào trong `/opsx-bootstrap`.

---

## 1. Hệ Sinh Thái Các Lệnh Chuyên Biệt Sau Khi Bootstrap (`Hub-and-Spoke`)

Sau khi chạy `/opsx-bootstrap` (mất 1 giây), người dùng sẽ gọi từng lệnh chuyên biệt ở các đoạn hội thoại riêng để không bao giờ bị tràn bộ nhớ:

| Lệnh kích hoạt (Antigravity / Claude) | Skill phụ trách | Nhiệm vụ chuyên biệt độc lập |
| :--- | :--- | :--- |
| **`/opsx-bootstrap`** | `opsx-bootstrap` | **Pure 1-Second Installer**: Chỉ copy & đồng bộ `.agents/`, `.agent/`, `.claude/commands/opsx/`, `AGENTS.md`, `openspec/config.yaml` trong **1 giây**. |
| **`/opsx-cms-sync`** (`/opsx:cms-sync`) | `opsx-cms-sync` | **Hub Điều Phối Hệ Thống CMS Dynamic A–Z**: Kiểm toán 8 trụ cột CMS đối chiếu Figma -> Bảng Confirm `[+] Thêm / [-] Bỏ / [=] Giữ` -> Xuất Handoff Prompts -> Nghiệm thu E2E. |
| **`/opsx-pixel-perfect`** (`/opsx:pixel-perfect`) | `opsx-pixel-perfect-ui` | **Hub Điều Phối Giao Diện 100% Pixel-Perfect vs Figma MCP**: Hỗ trợ kiểm toán toàn trang hoặc từng trang (`--page <TenTrang>`) -> Bảng Confirm `[+] Thêm / [-] Bỏ / [=] Giữ` -> Xuất Handoff Prompts -> Nghiệm thu đa khung nhìn. |
| **`/opsx-animation`** (`/opsx:animation` hoặc `/ui-skill`) | `opsx-animation` & `ui-skill` | **Hub Điều Phối Hệ Thống Animation Chuẩn LUXI (4 Quy Tắc Vàng)**: (1) Mỗi phần tử tối đa 1 animation, (2) Cấm `opacity: 0` trắng màn hình, (3) Nút qua lại `< / >` bắt buộc trượt ngang `translate-x`, (4) Giữ nguyên 100% `ProductGallery.vue` -> Bảng Confirm -> Xuất Handoff Prompts. |
| **`/opsx-figma-workflow`** | `opsx-figma-workflow` | **Dựng Giao Diện Mới Từ Figma MCP Theo Phase**: Điều phối 3 kịch bản (`greenfield-6-phases`, `home-first-incremental`, `batch-pages-sequential-qa`). |
| **`/opsx-audit-handoff`** (`/opsx:audit-handoff`) | `opsx-audit-handoff` | **Kiểm Toán Toàn Diện 5 Tầng Trước Bàn Giao**: Rà soát bảo mật, dọn sạch hardcode return cố định toàn bộ CMS (`scan-cms-hardcoded.py`), làm nhẹ source. |
| **`/opsx-deploy`** | `vps-cicd-deploy` | **Triển Khai VPS Production & GitHub Actions CI/CD 2-Job**: Cấu hình Nginx Subdomain, SSL Certbot, Database & SSH Rsync. |

---

## 2. Các Chế Độ Của `scripts/bootstrap.sh` (`Hoàn Tất Trong 1 Giây`)

### Chế độ 1: Đồng bộ ngay tại dự án hiện tại
```bash
./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh
```
Hoặc nếu dự án mới chưa có `.agents/`:
```bash
"$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh" --export-to .
```

### Chế độ 2: Xuất sang một thư mục dự án khác (`--export-to`)
```bash
./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --export-to /duong/dan/du-an-moi [--with-cicd <project-slug>]
```
Script tự động thực hiện trọn gói trong **1 giây** (100% không xóa file):
1. Sao chép toàn bộ `.agents/skills/`, `.agents/agents/`, `.agents/workflows/`, `.agents/scripts/` sang dự án đích.
2. Đồng bộ song song sang `.agent/skills/`, `.agent/workflows/` và `.claude/commands/opsx/{cms-sync,pixel-perfect,animation,ui-skill,audit-handoff}.md`.
3. Tự động khởi tạo hoặc cập nhật `AGENTS.md` và `openspec/config.yaml` từ template chuẩn ngay trong bash script.
4. Tự động bảo vệ `.gitignore` (thêm `/graphify-out`).
5. **Mặc định bỏ qua quét `graphify`** để trả kết quả tức thì trong 1 giây (chỉ chạy khi truyền cờ `--with-graphify`).

---

## 3. Cài Đặt Global Cho Máy Cá Nhân (`install-global.sh`)

```bash
./.agents/skills/opsx-bootstrap/scripts/install-global.sh
```
Đồng bộ toàn bộ bộ skill mới nhất vào `"$HOME/.gemini/config/skills/"` và `"$HOME/.gemini/config/team-skill-suite/.agents"`.

---

## 4. QUY TRÌNH BẮT BUỘC DÀNH CHO AGENT KHI NGƯỜI DÙNG GỌI `/opsx-bootstrap` (`1 COMMAND ONLY`)

Khi người dùng gõ `/opsx-bootstrap`:
1. **CHỈ CHẠY DUY NHẤT 1 LỆNH SAU**:
   - Nếu workspace chưa có `.agents/skills/opsx-cms-sync`:
     ```bash
     "$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh" --export-to .
     ```
   - Nếu workspace đã có đủ `.agents/skills/opsx-cms-sync`:
     ```bash
     ./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh
     ```
2. **DỪNG LẠI NGAY VÀ BÁO CÁO KẾT QUẢ**:
   - In ngay output của `bootstrap.sh` và danh sách các lệnh Hub (`/opsx-cms-sync`, `/opsx-pixel-perfect`, `/opsx-animation`, `/opsx-audit-handoff`, `/opsx-deploy`) cho người dùng.
   - **CẤM TUYỆT ĐỐI**: Không đọc thêm `composer.json`/`package.json`, không gọi `edit_file` sửa `openspec/config.yaml` hay `AGENTS.md`, không chạy `graphify`, không chạy `build` hay `test`. Toàn bộ `/opsx-bootstrap` phải kết thúc trong vài giây!
