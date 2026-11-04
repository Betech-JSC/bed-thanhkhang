---
description: "Thiết lập GitHub Actions CI/CD 2-Job và triển khai tự động dự án lên VPS Production (Nginx Subdomain, Certbot SSL, MySQL, Cron)"
---

Đọc và thực thi theo đúng hướng dẫn tại `.agents/skills/vps-cicd-deploy/SKILL.md` (hoặc `$HOME/.gemini/config/skills/vps-cicd-deploy/SKILL.md`).

1. Kiểm tra `.github/workflows/ci-cd.yml` của dự án (nếu chưa có, khởi tạo bằng `./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --with-cicd <project-slug>`).
2. Kiểm tra kết nối SSH không mật khẩu tới VPS (`ssh -o BatchMode=yes -o ConnectTimeout=5 <user>@<host> "uname -a"`). Nếu chưa có quyền SSH, dừng lại hướng dẫn hoặc yêu cầu người dùng cấp quyền nạp SSH key (`~/.ssh/id_ed25519.pub`).
3. Chạy `./.agents/skills/vps-cicd-deploy/scripts/provision-vps.sh` để khởi tạo Database, `/var/www/<project-slug>`, `.env` Production, Nginx Subdomain, Certbot SSL và Cron Scheduler.
4. Chạy `./.agents/skills/vps-cicd-deploy/scripts/setup-github-secrets.sh` (khi `gh auth status` đã đăng nhập) để cấu hình `VPS_HOST`, `VPS_PORT`, `VPS_USER`, `VPS_SSH_KEY` và kiểm chứng pipeline trên GitHub Actions.
