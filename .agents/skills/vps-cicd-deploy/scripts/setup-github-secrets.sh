#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Thiết lập tự động GitHub Actions Secrets cho CI/CD VPS Deployment
# Yêu cầu: Đã đăng nhập GitHub CLI (`gh auth status`)
# ==============================================================================

REPO=""
VPS_HOST="${VPS_HOST:-103.48.84.51}"
VPS_PORT="${VPS_PORT:-22}"
VPS_USER="${VPS_USER:-root}"
SSH_KEY_FILE="${SSH_KEY_FILE:-${HOME}/.ssh/id_ed25519}"

print_usage() {
  cat <<'EOF'
Cách dùng:
  ./.agents/skills/vps-cicd-deploy/scripts/setup-github-secrets.sh \
    [--repo Betech-JSC/bed-azurenetwork] \
    [--vps-host 103.48.84.51] \
    [--vps-port 22] \
    [--vps-user root] \
    [--ssh-key-file ~/.ssh/id_ed25519]
EOF
}

while [ "$#" -gt 0 ]; do
  case "$1" in
    -h|--help)
      print_usage
      exit 0
      ;;
    --repo)
      REPO="$2"
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
    --ssh-key-file)
      SSH_KEY_FILE="$2"
      shift 2
      ;;
    *)
      echo "[LỖI] Tham số không hợp lệ: $1" >&2
      print_usage
      exit 1
      ;;
  esac
done

if ! command -v gh >/dev/null 2>&1; then
  echo "[LỖI] Máy chưa cài đặt GitHub CLI (gh). Cài đặt bằng: brew install gh" >&2
  exit 1
fi

if ! gh auth status >/dev/null 2>&1; then
  echo "[LỖI] GitHub CLI chưa đăng nhập. Vui lòng chạy 'gh auth login' trước." >&2
  exit 1
fi

if [ -z "${REPO}" ]; then
  REPO="$(gh repo view --json nameWithOwner -q .nameWithOwner 2>/dev/null || true)"
fi

if [ -z "${REPO}" ]; then
  echo "[LỖI] Không tự xác định được tên repository. Hãy truyền --repo <owner>/<repo>." >&2
  exit 1
fi

if [ ! -f "${SSH_KEY_FILE}" ]; then
  echo "[LỖI] Không tìm thấy file SSH private key tại: ${SSH_KEY_FILE}" >&2
  exit 1
fi

echo "==> Đang cấu hình GitHub Actions Secrets cho repository: ${REPO}"
printf "%s" "${VPS_HOST}" | gh secret set VPS_HOST --repo "${REPO}"
printf "%s" "${VPS_PORT}" | gh secret set VPS_PORT --repo "${REPO}"
printf "%s" "${VPS_USER}" | gh secret set VPS_USER --repo "${REPO}"
gh secret set VPS_SSH_KEY --repo "${REPO}" < "${SSH_KEY_FILE}"

echo "==> Danh sách Secrets hiện có trên ${REPO}:"
gh secret list --repo "${REPO}"
