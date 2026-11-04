#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt Global skill 'opsx-cms-sync' vào $HOME/.gemini/config/skills/
# và đồng bộ vào $HOME/.gemini/config/team-skill-suite/.agents/
# (100% an toàn, không xóa file)
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

GLOBAL_SKILL_TARGET="${HOME}/.gemini/config/skills/opsx-cms-sync"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

echo "==> Đang cài đặt skill 'opsx-cms-sync' vào cấu hình Global: ${GLOBAL_SKILL_TARGET}"
mkdir -p "${GLOBAL_SKILL_TARGET}"
if [ "${SKILL_DIR}" != "${GLOBAL_SKILL_TARGET}" ]; then
  cp -R "${SKILL_DIR}/." "${GLOBAL_SKILL_TARGET}/"
fi
chmod +x "${GLOBAL_SKILL_TARGET}/scripts/"*.sh "${GLOBAL_SKILL_TARGET}/scripts/"*.py 2>/dev/null || true

echo "==> Đang đồng bộ 'opsx-cms-sync' vào Team Skill Suite Cache (${GLOBAL_SUITE_CACHE})..."
mkdir -p "${GLOBAL_SUITE_CACHE}/skills/opsx-cms-sync" "${GLOBAL_SUITE_CACHE}/workflows" 2>/dev/null || true
cp -R "${SKILL_DIR}/." "${GLOBAL_SUITE_CACHE}/skills/opsx-cms-sync/" 2>/dev/null || true
chmod +x "${GLOBAL_SUITE_CACHE}/skills/opsx-cms-sync/scripts/"*.sh "${GLOBAL_SUITE_CACHE}/skills/opsx-cms-sync/scripts/"*.py 2>/dev/null || true
cp "${SKILL_DIR}/resources/workflow-opsx-cms-sync.md" "${GLOBAL_SUITE_CACHE}/workflows/opsx-cms-sync.md" 2>/dev/null || true

echo ""
echo "HOÀN TẤT! Skill 'opsx-cms-sync' (Hub điều phối Hệ thống CMS Dynamic A–Z) đã sẵn sàng ở cấp Global."
echo "Từ bây giờ ở bất kỳ dự án hoặc đoạn hội thoại nào, bạn có thể:"
echo "  1. Gõ trực tiếp '/opsx-cms-sync' (Antigravity) hoặc '/opsx:cms-sync' (Claude Code)"
echo "  2. Chạy kiểm toán nhanh 8 trụ cột CMS bằng lệnh:"
echo "     python3 \"\$HOME/.gemini/config/skills/opsx-cms-sync/scripts/audit-cms-sync.py\" ."
echo "  3. Hoặc xuất sang dự án mới bằng lệnh:"
echo "     \"\$HOME/.gemini/config/skills/opsx-cms-sync/scripts/bootstrap-cms-sync.sh\" --export-to <duong_dan_du_an>"
