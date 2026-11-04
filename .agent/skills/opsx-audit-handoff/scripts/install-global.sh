#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt Global skill 'opsx-audit-handoff' vào $HOME/.gemini/config/skills/
# giúp mọi đoạn hội thoại trên Antigravity ở bất kỳ dự án nào cũng nhận diện
# và thực thi được /opsx-audit-handoff ngay lập tức.
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

GLOBAL_SKILL_TARGET="${HOME}/.gemini/config/skills/opsx-audit-handoff"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

echo "==> Đang cài đặt skill 'opsx-audit-handoff' vào cấu hình Global: ${GLOBAL_SKILL_TARGET}"
mkdir -p "${GLOBAL_SKILL_TARGET}"
if [ "${SKILL_DIR}" != "${GLOBAL_SKILL_TARGET}" ]; then
  cp -R "${SKILL_DIR}/." "${GLOBAL_SKILL_TARGET}/"
fi
chmod +x "${GLOBAL_SKILL_TARGET}/scripts/"*.sh "${GLOBAL_SKILL_TARGET}/scripts/"*.py 2>/dev/null || true

if [ -d "${GLOBAL_SUITE_CACHE}" ]; then
  echo "==> Đang đồng bộ 'opsx-audit-handoff' vào Team Skill Suite Cache (${GLOBAL_SUITE_CACHE})..."
  mkdir -p "${GLOBAL_SUITE_CACHE}/skills/opsx-audit-handoff" "${GLOBAL_SUITE_CACHE}/workflows" 2>/dev/null || true
  cp -R "${SKILL_DIR}/." "${GLOBAL_SUITE_CACHE}/skills/opsx-audit-handoff/" 2>/dev/null || true
  chmod +x "${GLOBAL_SUITE_CACHE}/skills/opsx-audit-handoff/scripts/"*.sh "${GLOBAL_SUITE_CACHE}/skills/opsx-audit-handoff/scripts/"*.py 2>/dev/null || true
  cp "${SKILL_DIR}/resources/workflow-opsx-audit-handoff.md" "${GLOBAL_SUITE_CACHE}/workflows/opsx-audit-handoff.md" 2>/dev/null || true
fi

echo ""
echo "HOÀN TẤT! Skill 'opsx-audit-handoff' (kèm CMS Hardcoded Return Scanner) đã sẵn sàng ở cấp Global."
echo "Từ bây giờ ở bất kỳ dự án hoặc đoạn hội thoại nào, bạn có thể:"
echo "  1. Gõ trực tiếp '/opsx-audit-handoff' (Antigravity) hoặc '/opsx:audit-handoff' (Claude Code)"
echo "  2. Chạy quét nhanh Hardcode & Fixed Return CMS bằng lệnh:"
echo "     python3 \"\$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py\" ."
echo "  3. Hoặc xuất sang dự án mới bằng lệnh:"
echo "     \"\$HOME/.gemini/config/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh\" --export-to <duong_dan_du_an>"
