#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# VPS Provisioning Script for Laravel / BED CMS Projects
# - Non-destructive & Idempotent (never drops DBs or deletes existing files)
# - Provisions:
#     1) Deploy SSH key authorization
#     2) MySQL Database & User (if not exists)
#     3) Git Clone / Fetch into /var/www/<project-slug>
#     4) Production .env setup, Composer install, migrate, storage:link
#     5) Nginx vhost (/etc/nginx/sites-available/<subdomain>) & reload
#     6) Let's Encrypt SSL via Certbot (--nginx --redirect)
#     7) Laravel Scheduler Cron (* * * * * php artisan schedule:run)
# ==============================================================================

VPS_HOST="${VPS_HOST:-103.48.84.51}"
VPS_PORT="${VPS_PORT:-22}"
VPS_USER="${VPS_USER:-root}"
PROJECT_SLUG=""
SUBDOMAIN=""
GIT_REPO_URL=""
DB_NAME=""
DB_USER=""
DB_PASS=""
CERTBOT_EMAIL="${CERTBOT_EMAIL:-admin@betech-digital.com}"
SKIP_SSL=0

print_usage() {
  cat <<'EOF'
Cách dùng:
  ./.agents/skills/vps-cicd-deploy/scripts/provision-vps.sh \
    --project-slug bed-azurenetwork \
    --subdomain azure.betech-digital.com \
    --repo https://github.com/Betech-JSC/bed-azurenetwork.git \
    [--vps-host 103.48.84.51] \
    [--vps-port 22] \
    [--vps-user root] \
    [--db-name bed_azurenetwork] \
    [--db-user bed_azurenetwork] \
    [--db-pass <password>] \
    [--email admin@betech-digital.com] \
    [--skip-ssl]
EOF
}

while [ "$#" -gt 0 ]; do
  case "$1" in
    -h|--help)
      print_usage
      exit 0
      ;;
    --project-slug)
      PROJECT_SLUG="$2"
      shift 2
      ;;
    --subdomain)
      SUBDOMAIN="$2"
      shift 2
      ;;
    --repo)
      GIT_REPO_URL="$2"
      shift 2
      ;;
    --vps-host)
      VPS_HOST="$2"
      shift 2
      ;;
    --vps-port)
      VPS_PORT="$2"
      shift 2
      ;;
    --vps-user)
      VPS_USER="$2"
      shift 2
      ;;
    --db-name)
      DB_NAME="$2"
      shift 2
      ;;
    --db-user)
      DB_USER="$2"
      shift 2
      ;;
    --db-pass)
      DB_PASS="$2"
      shift 2
      ;;
    --email)
      CERTBOT_EMAIL="$2"
      shift 2
      ;;
    --skip-ssl)
      SKIP_SSL=1
      shift 1
      ;;
    *)
      echo "[LỖI] Tham số không hợp lệ: $1" >&2
      print_usage
      exit 1
      ;;
  esac
done

if [ -z "${PROJECT_SLUG}" ] || [ -z "${SUBDOMAIN}" ] || [ -z "${GIT_REPO_URL}" ]; then
  echo "[LỖI] Bắt buộc truyền đủ --project-slug, --subdomain và --repo." >&2
  print_usage
  exit 1
fi

SAFE_DB_SLUG="$(printf "%s" "${PROJECT_SLUG}" | tr '-' '_' | tr -cd 'a-zA-Z0-9_')"
DB_NAME="${DB_NAME:-${SAFE_DB_SLUG}}"
DB_USER="${DB_USER:-${SAFE_DB_SLUG}}"
if [ -z "${DB_PASS}" ]; then
  DB_PASS="Bt_$(openssl rand -hex 10)_9A"
fi

echo "=================================================================="
echo "VPS Provisioning — ${PROJECT_SLUG} (${SUBDOMAIN})"
echo "Target VPS: ${VPS_USER}@${VPS_HOST}:${VPS_PORT}"
echo "Target Dir: /var/www/${PROJECT_SLUG}"
echo "Database  : ${DB_NAME} (User: ${DB_USER})"
echo "=================================================================="

ssh -p "${VPS_PORT}" "${VPS_USER}@${VPS_HOST}" \
  "PROJECT_SLUG='${PROJECT_SLUG}' SUBDOMAIN='${SUBDOMAIN}' GIT_REPO_URL='${GIT_REPO_URL}' DB_NAME='${DB_NAME}' DB_USER='${DB_USER}' DB_PASS='${DB_PASS}' CERTBOT_EMAIL='${CERTBOT_EMAIL}' SKIP_SSL='${SKIP_SSL}' bash -s" <<'REMOTE_EOF'
set -euo pipefail

PROJECT_PATH="/var/www/${PROJECT_SLUG}"
PHP_BIN="$(command -v php8.3 || command -v php8.2 || command -v php)"
COMPOSER_BIN="$(command -v composer)"

echo "==> [1/6] Kiểm tra và khởi tạo MySQL Database & User (Idempotent)..."
if [ -f /etc/mysql/debian.cnf ]; then
  MYSQL_CMD=(mysql --defaults-file=/etc/mysql/debian.cnf)
else
  MYSQL_CMD=(mysql -u root)
fi
"${MYSQL_CMD[@]}" <<SQL
CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
CREATE USER IF NOT EXISTS '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'127.0.0.1';
FLUSH PRIVILEGES;
SQL
if [ ! -f "${PROJECT_PATH}/.env" ]; then
  "${MYSQL_CMD[@]}" <<SQL
ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';
ALTER USER '${DB_USER}'@'127.0.0.1' IDENTIFIED BY '${DB_PASS}';
FLUSH PRIVILEGES;
SQL
fi
echo "   - MySQL Database '${DB_NAME}' sẵn sàng."

echo "==> [2/6] Chuẩn bị mã nguồn tại ${PROJECT_PATH}..."
mkdir -p /var/www
if [ -f /root/.ssh/id_ed25519 ]; then
  export GIT_SSH_COMMAND="ssh -i /root/.ssh/id_ed25519 -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
  GIT_REPO_URL="$(printf "%s" "${GIT_REPO_URL}" | sed 's|^https://github.com/|git@github.com:|')"
fi
if [ ! -d "${PROJECT_PATH}/.git" ]; then
  git clone "${GIT_REPO_URL}" "${PROJECT_PATH}"
fi
cd "${PROJECT_PATH}"
if [ -f /root/.ssh/id_ed25519 ]; then
  git config core.sshCommand "ssh -i /root/.ssh/id_ed25519 -o IdentitiesOnly=yes -o StrictHostKeyChecking=accept-new"
  git remote set-url origin "${GIT_REPO_URL}"
fi
git config core.fileMode false
git fetch origin main
git reset --hard origin/main

echo "==> [3/6] Cấu hình .env Production & Cài đặt Composer..."
if [ ! -f "${PROJECT_PATH}/.env" ]; then
  cp "${PROJECT_PATH}/.env.example" "${PROJECT_PATH}/.env"
  sed -i "s|^APP_ENV=.*|APP_ENV=production|" "${PROJECT_PATH}/.env"
  sed -i "s|^APP_DEBUG=.*|APP_DEBUG=false|" "${PROJECT_PATH}/.env"
  sed -i "s|^APP_URL=.*|APP_URL=https://${SUBDOMAIN}|" "${PROJECT_PATH}/.env"
  sed -i "s|^DB_DATABASE=.*|DB_DATABASE=${DB_NAME}|" "${PROJECT_PATH}/.env"
  sed -i "s|^DB_USERNAME=.*|DB_USERNAME=${DB_USER}|" "${PROJECT_PATH}/.env"
  sed -i "s|^DB_PASSWORD=.*|DB_PASSWORD=${DB_PASS}|" "${PROJECT_PATH}/.env"
  OPCACHE_TOKEN="$(openssl rand -hex 24)"
  sed -i "s|^OPCACHE_SECRET=.*|OPCACHE_SECRET=${OPCACHE_TOKEN}|" "${PROJECT_PATH}/.env" || true
  echo "   - Đã tạo mới file .env Production."
else
  echo "   - Đã có sẵn file .env Production -> Giữ nguyên an toàn."
fi

COMPOSER_ALLOW_SUPERUSER=1 "${PHP_BIN}" "${COMPOSER_BIN}" install --no-dev --no-interaction --prefer-dist --optimize-autoloader --ignore-platform-req=php

if ! grep -Eq '^APP_KEY=base64:' "${PROJECT_PATH}/.env"; then
  "${PHP_BIN}" artisan key:generate --force
fi

"${PHP_BIN}" artisan migrate --force
"${PHP_BIN}" artisan storage:link || true
"${PHP_BIN}" artisan optimize:clear
chown -R www-data:www-data "${PROJECT_PATH}/storage" "${PROJECT_PATH}/bootstrap/cache"
chmod -R ug+rwx "${PROJECT_PATH}/storage" "${PROJECT_PATH}/bootstrap/cache"

echo "==> [4/6] Cấu hình Nginx Vhost cho ${SUBDOMAIN}..."
PHP_FPM_SOCK="/run/php/php8.3-fpm.sock"
if [ ! -S "${PHP_FPM_SOCK}" ] && [ -S "/run/php/php8.2-fpm.sock" ]; then
  PHP_FPM_SOCK="/run/php/php8.2-fpm.sock"
fi

NGINX_AVAILABLE="/etc/nginx/sites-available/${SUBDOMAIN}"
NGINX_ENABLED="/etc/nginx/sites-enabled/${SUBDOMAIN}"

if [ ! -f "${NGINX_AVAILABLE}" ]; then
  cat > "${NGINX_AVAILABLE}" <<NGINX_CONF
server {
    listen 80;
    listen [::]:80;
    server_name ${SUBDOMAIN};

    root ${PROJECT_PATH}/public;
    index index.php index.html;

    charset utf-8;
    client_max_body_size 64M;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php\$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_read_timeout 300;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
NGINX_CONF
  echo "   - Đã tạo cấu hình Nginx tại ${NGINX_AVAILABLE}."
else
  echo "   - Đã có cấu hình Nginx tại ${NGINX_AVAILABLE} -> Giữ nguyên."
fi

ln -sfn "${NGINX_AVAILABLE}" "${NGINX_ENABLED}"
nginx -t
systemctl reload nginx

echo "==> [5/6] Cài đặt chứng chỉ SSL Let's Encrypt (Certbot)..."
if [ "${SKIP_SSL}" -eq 0 ] && command -v certbot >/dev/null 2>&1; then
  certbot --nginx -d "${SUBDOMAIN}" --non-interactive --agree-tos -m "${CERTBOT_EMAIL}" --redirect || echo "   - [LƯU Ý] Certbot chưa thể cấp SSL (kiểm tra DNS A record của ${SUBDOMAIN} đã trỏ về IP VPS chưa)."
else
  echo "   - Bỏ qua Certbot SSL (SKIP_SSL=${SKIP_SSL} hoặc chưa cài certbot)."
fi

echo "==> [6/6] Đăng ký Cron Laravel Scheduler (mỗi phút)..."
CRON_LINE="* * * * * cd ${PROJECT_PATH} && ${PHP_BIN} artisan schedule:run >> /dev/null 2>&1"
CURRENT_CRON="$(crontab -l 2>/dev/null || true)"
if printf "%s\n" "${CURRENT_CRON}" | grep -Fq "${PROJECT_PATH}"; then
  echo "   - Cron schedule:run cho ${PROJECT_PATH} đã tồn tại."
else
  (printf "%s\n%s\n" "${CURRENT_CRON}" "${CRON_LINE}" | sed '/^$/d') | crontab -
  echo "   - Đã đăng ký Cron schedule:run thành công."
fi

echo "HOÀN TẤT PROVISIONING TRÊN VPS!"
REMOTE_EOF
