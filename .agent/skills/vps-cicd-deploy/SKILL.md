---
name: vps-cicd-deploy
description: Chuẩn hóa quy trình triển khai dự án Laravel / BED CMS (Laravel + Inertia + Vue 3 + Dual Vite Frontend/SSR & Backend) lên VPS Production (mặc định 103.48.84.51), cấu hình Nginx Subdomain, cấp chứng chỉ SSL Let's Encrypt (Certbot), khởi tạo MySQL Database & Cron Scheduler, và thiết lập GitHub Actions CI/CD 2-Job tự động qua SSH + Rsync. Sử dụng khi người dùng gọi "/opsx-deploy", yêu cầu đưa dự án lên VPS, cấu hình subdomain hoặc thiết lập CI/CD.
---

# VPS & GitHub Actions CI/CD Deployment Skill (`vps-cicd-deploy`)

Skill này đóng gói toàn bộ quy trình chuẩn của Team Betech để triển khai các dự án **BED CMS (Laravel + Inertia + Vue 3 + Vite SSR/Backend)** lên máy chủ VPS Production và thiết lập đường ống CI/CD tự động hóa 100% trên GitHub Actions.

---

## 1. Kiến Trúc Chuẩn (Tại Sao Không Build Vite Trên VPS?)

Các dự án BED CMS sử dụng kiến trúc Dual Vite Build:
- `yarn route`: Chạy `php artisan vue-i18n:generate` và `php artisan ziggy:generate`.
- `yarn frontend:build`: Chạy `vite build && vite build --ssr` với `--max-old-space-size=4096` (tạo `public/build/frontend` và `bootstrap/ssr/`).
- `yarn backend:build`: Chạy `vite build --config vite.backend.config.ts` với `--max-old-space-size=4096` (tạo `public/build/backend`).

Do VPS Production (`103.48.84.51`) chạy chia sẻ nhiều dự án và cài Node v22 global trong khi bộ Vite của dự án yêu cầu Node 18 và ngốn tới 4GB RAM khi đóng gói, **tuyệt đối không chạy `yarn build` trực tiếp trên VPS**. Thay vào đó, áp dụng kiến trúc **2-Job GitHub Actions**:
1. **Job 1 (`ci-test-and-build`)**: Chạy trên GitHub Runner (`ubuntu-latest`), cài PHP 8.3 + Node 18, biên dịch `public/build/` + `bootstrap/ssr/` trước (`yarn route`, `yarn frontend:build`, `yarn backend:build`) để có sẵn Vite `manifest.json` cho các Blade view `@vite(...)`, sau đó chạy toàn bộ PHPUnit test trên SQLite (`php artisan test`) và đóng gói thành artifact `compiled-assets`.
2. **Job 2 (`cd-deploy`)**: Chỉ chạy khi push lên nhánh `main` và Job 1 đã PASS. Tải `compiled-assets`, SSH vào VPS kéo code PHP mới nhất (`git reset --hard origin/main` với `core.fileMode false`), chạy `composer install --no-dev --ignore-platform-req=php`, dùng `rsync -avz --delete` đẩy `public/build/` và `bootstrap/ssr/` lên VPS, chạy `php artisan migrate --force`, `php artisan optimize:clear` và reload `php8.2-fpm` / `php8.3-fpm`.

---

## 2. Cấu Trúc Thư Mục Của Skill

```text
.agents/skills/vps-cicd-deploy/
├── SKILL.md
├── resources/
│   ├── ci-cd.laravel-bed.template.yml    # Template GitHub Actions 2-Job chuẩn
│   └── nginx-laravel.template.conf       # Template Nginx vhost chuẩn cho Subdomain
└── scripts/
    ├── provision-vps.sh                  # Script khởi tạo DB, Code, .env, Nginx, SSL, Cron trên VPS
    └── setup-github-secrets.sh           # Script cài đặt VPS_HOST, VPS_PORT, VPS_USER, VPS_SSH_KEY qua gh CLI
```

---

## 3. Quy Trình 4 Bước Triển Khai Dự Án Mới Lên VPS (`/opsx-deploy`)

### Bước 1: Khởi tạo `.github/workflows/ci-cd.yml` tại dự án
Có thể khởi tạo nhanh thông qua `opsx-bootstrap`:
```bash
./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --with-cicd <project-slug>
```
Hoặc tạo từ template `resources/ci-cd.laravel-bed.template.yml` (thay `__PROJECT_SLUG__` bằng tên thư mục dự án trên `/var/www/<project-slug>`, ví dụ `bed-azurenetwork`, và `__TEST_COMMAND__` bằng `php artisan test`).

### Bước 2: Cấp quyền SSH Key cho VPS (`103.48.84.51`)
Đảm bảo SSH key trên máy/GitHub Secrets (`~/.ssh/id_ed25519.pub`) đã nằm trong `/root/.ssh/authorized_keys` của VPS:
```bash
ssh-copy-id -i ~/.ssh/id_ed25519.pub root@103.48.84.51
```
Kiểm tra kết nối không mật khẩu:
```bash
ssh -o BatchMode=yes root@103.48.84.51 "uname -a"
```

### Bước 3: Chạy `provision-vps.sh` để dựng Hạ tầng trên VPS (Idempotent & Safe)
Script tự động tạo MySQL Database + User, clone repo vào `/var/www/<project-slug>`, sinh `.env` Production (`APP_URL=https://<subdomain>`, `OPCACHE_SECRET`, `DB_*`), chạy `composer install --no-dev`, `key:generate`, `migrate --force`, `storage:link`, tạo Nginx vhost `/etc/nginx/sites-available/<subdomain>`, kích hoạt HTTPS qua Certbot và đăng ký Cron `schedule:run` mỗi phút:
```bash
./.agents/skills/vps-cicd-deploy/scripts/provision-vps.sh \
  --project-slug bed-azurenetwork \
  --subdomain azure.betech-digital.com \
  --repo https://github.com/Betech-JSC/bed-azurenetwork.git \
  --vps-host 103.48.84.51 \
  --email admin@betech-digital.com
```
> **Lưu ý DNS**: Trước khi chạy Certbot (hoặc bỏ cờ `--skip-ssl`), bản ghi DNS `A` của `<subdomain>` (ví dụ `azure.betech-digital.com`) phải trỏ về IP `103.48.84.51`.

### Bước 4: Cài đặt GitHub Repository Secrets & Kích hoạt CI/CD
Nạp 4 biến Secrets (`VPS_HOST`, `VPS_PORT`, `VPS_USER`, `VPS_SSH_KEY`) lên GitHub Repository bằng script:
```bash
./.agents/skills/vps-cicd-deploy/scripts/setup-github-secrets.sh \
  --repo Betech-JSC/bed-azurenetwork \
  --vps-host 103.48.84.51 \
  --vps-port 22 \
  --vps-user root \
  --ssh-key-file ~/.ssh/id_ed25519
```
Sau khi push nhánh `main` lên GitHub, theo dõi tiến trình bằng:
```bash
gh run list --limit 3
gh run watch
```

---

## 4. Nguyên Tắc An Toàn Khi Thực Thi (`STRICT SAFE GUARD`)

1. **Không bao giờ xóa hoặc ghi đè Database đang chạy**: `provision-vps.sh` chỉ dùng `CREATE DATABASE IF NOT EXISTS` và `php artisan migrate --force`, tuyệt đối cấm `migrate:fresh` hay `db:seed` ghi đè dữ liệu thật trên VPS.
2. **Không ghi đè `.env` hoặc vhost Nginx đã tùy chỉnh trên VPS**: Nếu `/var/www/<project-slug>/.env` hoặc `/etc/nginx/sites-available/<subdomain>` đã tồn tại, script giữ nguyên an toàn.
3. **Bằng chứng thực tế 100%**: Mọi báo cáo hoàn tất triển khai phải kèm output của `nginx -t`, trạng thái GitHub Actions (`gh run view`) và kết quả kiểm tra HTTP/HTTPS (`curl -I https://<subdomain>`).
