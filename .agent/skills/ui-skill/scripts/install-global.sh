#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt Global skill 'ui-skill' vào $HOME/.gemini/config/skills/ui-skill
# và đồng bộ vào Team Skill Suite Cache ($HOME/.gemini/config/team-skill-suite/.agents)
# giúp mọi dự án mới mở trên Antigravity đều nhận diện và gọi được /ui-skill ngay.
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

GLOBAL_SKILL_TARGET="${HOME}/.gemini/config/skills/ui-skill"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

echo "==> Đang cài đặt skill 'ui-skill' vào cấu hình Global: ${GLOBAL_SKILL_TARGET}"
mkdir -p "${GLOBAL_SKILL_TARGET}"
if [ "${SKILL_DIR}" != "${GLOBAL_SKILL_TARGET}" ]; then
  cp -R "${SKILL_DIR}/." "${GLOBAL_SKILL_TARGET}/"
fi
chmod +x "${GLOBAL_SKILL_TARGET}/scripts/"*.sh 2>/dev/null || true

if [ -d "${GLOBAL_SUITE_CACHE}" ]; then
  echo "==> Đang đồng bộ 'ui-skill' vào Team Skill Suite Cache (${GLOBAL_SUITE_CACHE})..."
  mkdir -p "${GLOBAL_SUITE_CACHE}/skills/ui-skill" "${GLOBAL_SUITE_CACHE}/workflows" 2>/dev/null || true
  cp -R "${SKILL_DIR}/." "${GLOBAL_SUITE_CACHE}/skills/ui-skill/" 2>/dev/null || true
  chmod +x "${GLOBAL_SUITE_CACHE}/skills/ui-skill/scripts/"*.sh 2>/dev/null || true
  cp "${SKILL_DIR}/resources/workflow-ui-skill.md" "${GLOBAL_SUITE_CACHE}/workflows/ui-skill.md" 2>/dev/null || true
fi

echo ""
echo "HOÀN TẤT! Skill 'ui-skill' đã sẵn sàng ở cấp Global."
echo "Từ bây giờ ở bất kỳ dự án hoặc đoạn hội thoại nào, bạn có thể:"
echo "  1. Gõ trực tiếp '/ui-skill' (Antigravity) hoặc '/opsx:ui-skill' (Claude Code)"
echo "  2. Hoặc xuất và quét ngay cho dự án mới bằng lệnh:"
echo "     \"\$HOME/.gemini/config/skills/ui-skill/scripts/bootstrap-ui-skill.sh\" --export-to <duong_dan_du_an> [--scan]"
