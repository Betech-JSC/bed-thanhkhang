#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt Global skill 'opsx-pixel-perfect-ui' vào $HOME/.gemini/config/skills/
# (100% không sử dụng lệnh xóa file)
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

GLOBAL_SKILL_TARGET="${HOME}/.gemini/config/skills/opsx-pixel-perfect-ui"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

mkdir -p "${GLOBAL_SKILL_TARGET}"
if [ "${SKILL_DIR}" != "${GLOBAL_SKILL_TARGET}" ]; then
  cp -R "${SKILL_DIR}/." "${GLOBAL_SKILL_TARGET}/"
fi
chmod +x "${GLOBAL_SKILL_TARGET}/scripts/"*.sh "${GLOBAL_SKILL_TARGET}/scripts/"*.py 2>/dev/null || true

if [ -d "${GLOBAL_SUITE_CACHE}" ]; then
  mkdir -p "${GLOBAL_SUITE_CACHE}/skills/opsx-pixel-perfect-ui" "${GLOBAL_SUITE_CACHE}/workflows" 2>/dev/null || true
  cp -R "${SKILL_DIR}/." "${GLOBAL_SUITE_CACHE}/skills/opsx-pixel-perfect-ui/" 2>/dev/null || true
  chmod +x "${GLOBAL_SUITE_CACHE}/skills/opsx-pixel-perfect-ui/scripts/"*.sh "${GLOBAL_SUITE_CACHE}/skills/opsx-pixel-perfect-ui/scripts/"*.py 2>/dev/null || true
  cp "${SKILL_DIR}/resources/workflow-opsx-pixel-perfect.md" "${GLOBAL_SUITE_CACHE}/workflows/opsx-pixel-perfect.md" 2>/dev/null || true
fi

echo "HOÀN TẤT! Skill 'opsx-pixel-perfect-ui' đã sẵn sàng ở cấp Global."
