#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt nhanh skill opsx-bootstrap + bộ Team Skill Suite cache vào $HOME
# Giúp thành viên mở bất kỳ folder mới nào trên Antigravity cũng gọi được
# /opsx-bootstrap ngay lập tức (100% không xóa file cũ, chỉ cập nhật an toàn).
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
REPO_AGENTS_DIR="$(cd "${SKILL_DIR}/../.." && pwd)"

GLOBAL_SKILL_TARGET="${HOME}/.gemini/config/skills/opsx-bootstrap"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

echo "==> Đang cài đặt skill 'opsx-bootstrap' vào cấu hình Global của bạn..."
mkdir -p "${GLOBAL_SKILL_TARGET}"
cp -R "${SKILL_DIR}/." "${GLOBAL_SKILL_TARGET}/"
chmod +x "${GLOBAL_SKILL_TARGET}/scripts/bootstrap.sh" "${GLOBAL_SKILL_TARGET}/scripts/install-global.sh"

if [ -d "${REPO_AGENTS_DIR}/skills" ] && [ "$(basename "${REPO_AGENTS_DIR}")" = ".agents" ]; then
  echo "==> Đang lưu bản cache Bộ Skill Chuẩn Toàn Diện của Team vào: ${GLOBAL_SUITE_CACHE}"
  mkdir -p "${GLOBAL_SUITE_CACHE}/skills" "${GLOBAL_SUITE_CACHE}/agents" "${GLOBAL_SUITE_CACHE}/workflows" "${GLOBAL_SUITE_CACHE}/scripts"
  cp -R "${REPO_AGENTS_DIR}/skills/." "${GLOBAL_SUITE_CACHE}/skills/"
  if [ -d "${REPO_AGENTS_DIR}/agents" ]; then
    cp -R "${REPO_AGENTS_DIR}/agents/." "${GLOBAL_SUITE_CACHE}/agents/"
  fi
  if [ -d "${REPO_AGENTS_DIR}/workflows" ]; then
    cp -R "${REPO_AGENTS_DIR}/workflows/." "${GLOBAL_SUITE_CACHE}/workflows/"
  fi
  if [ -d "${REPO_AGENTS_DIR}/scripts" ]; then
    cp -R "${REPO_AGENTS_DIR}/scripts/." "${GLOBAL_SUITE_CACHE}/scripts/"
  fi
fi

echo ""
echo "HOÀN TẤT! Từ bây giờ bạn có thể mở bất kỳ thư mục dự án mới nào và chạy:"
echo "  \"\$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh\" --export-to ."
echo "hoặc gõ trực tiếp '/opsx-bootstrap' trong khung chat Antigravity."
