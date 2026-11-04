#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# Cài đặt nhanh hệ sinh thái skill chuẩn của Team:
#   - opsx-bootstrap (Pure Lightweight Installer)
#   - opsx-cms-sync (Hub điều phối CMS Dynamic A-Z)
#   - opsx-pixel-perfect-ui (Hub điều phối UI 100% Pixel-Perfect vs Figma MCP)
#   - opsx-animation & ui-skill (Hub điều phối Animation 4 Quy Tắc Vàng)
#   - opsx-audit-handoff, opsx-ui-audit, vps-cicd-deploy
# vào $HOME/.gemini/config/skills/ và $HOME/.gemini/config/team-skill-suite/.agents
# (100% an toàn, không xóa file)
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"
REPO_AGENTS_DIR="$(cd "${SKILL_DIR}/../.." && pwd)"

GLOBAL_SKILLS_ROOT="${HOME}/.gemini/config/skills"
GLOBAL_SUITE_CACHE="${HOME}/.gemini/config/team-skill-suite/.agents"

echo "==> Đang cài đặt hệ sinh thái Skills cốt lõi vào cấu hình Global (${GLOBAL_SKILLS_ROOT})..."
mkdir -p "${GLOBAL_SKILLS_ROOT}"

CORE_SKILLS=(
  "opsx-bootstrap"
  "opsx-cms-sync"
  "opsx-pixel-perfect-ui"
  "opsx-animation"
  "ui-skill"
  "opsx-i18n-cms"
  "opsx-i18n-fe"
  "opsx-audit-handoff"
  "opsx-ui-audit"
  "opsx-figma-workflow"
  "vps-cicd-deploy"
)

for skill_name in "${CORE_SKILLS[@]}"; do
  src_dir="${REPO_AGENTS_DIR}/skills/${skill_name}"
  dest_dir="${GLOBAL_SKILLS_ROOT}/${skill_name}"
  if [ -d "${src_dir}" ]; then
    mkdir -p "${dest_dir}"
    if [ "${src_dir}" != "${dest_dir}" ]; then
      cp -R "${src_dir}/." "${dest_dir}/"
    fi
    chmod +x "${dest_dir}/scripts/"*.sh "${dest_dir}/scripts/"*.py 2>/dev/null || true
    echo "   - Đã đồng bộ Global Skill: ${skill_name}"
  fi
done

if [ -d "${REPO_AGENTS_DIR}/skills" ] && [ "$(basename "${REPO_AGENTS_DIR}")" = ".agents" ]; then
  echo "==> Đang lưu bản cache Bộ Skill Chuẩn Toàn Diện của Team vào: ${GLOBAL_SUITE_CACHE}"
  mkdir -p "${GLOBAL_SUITE_CACHE}/skills" "${GLOBAL_SUITE_CACHE}/agents" "${GLOBAL_SUITE_CACHE}/workflows" "${GLOBAL_SUITE_CACHE}/scripts"
  if [ "${REPO_AGENTS_DIR}" != "${GLOBAL_SUITE_CACHE}" ]; then
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
fi

echo ""
echo "HOÀN TẤT! Từ bây giờ bạn có thể mở bất kỳ thư mục dự án mới nào và chạy:"
echo "  \"\$HOME/.gemini/config/skills/opsx-bootstrap/scripts/bootstrap.sh\" --export-to . [--with-cicd <project-slug>]"
echo "hoặc gõ trực tiếp '/opsx-bootstrap', '/opsx-cms-sync', '/opsx-pixel-perfect', '/opsx-animation', '/opsx-audit-handoff' và '/opsx-deploy'."
