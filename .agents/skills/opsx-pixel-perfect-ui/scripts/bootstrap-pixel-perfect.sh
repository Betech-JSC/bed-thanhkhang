#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# OPSX Pixel-Perfect UI — Multi-Project Bootstrap & Export Script
# - Non-destructive (100% no file deletion)
# - Modes:
#     1) Local sync: ./bootstrap-pixel-perfect.sh [--scan]
#     2) Export to another project: ./bootstrap-pixel-perfect.sh --export-to <dir> [--scan]
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

bootstrap_pixel_perfect() {
  local target_dir="$1"
  local run_scan="${2:-0}"

  mkdir -p "${target_dir}"
  target_dir="$(cd "${target_dir}" && pwd)"

  echo "=================================================================="
  echo "OPSX Pixel-Perfect UI — Đồng bộ Bộ Skill 100% Chuẩn Figma"
  echo "Thư mục dự án đích: ${target_dir}"
  echo "=================================================================="

  local target_agents_skill="${target_dir}/.agents/skills/opsx-pixel-perfect-ui"
  mkdir -p "${target_agents_skill}"
  if [ "${SKILL_DIR}" != "${target_agents_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agents_skill}/"
  fi
  chmod +x "${target_agents_skill}/scripts/"*.sh "${target_agents_skill}/scripts/"*.py 2>/dev/null || true

  local target_agent_skill="${target_dir}/.agent/skills/opsx-pixel-perfect-ui"
  mkdir -p "${target_agent_skill}"
  if [ "${SKILL_DIR}" != "${target_agent_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agent_skill}/"
  fi
  chmod +x "${target_agent_skill}/scripts/"*.sh "${target_agent_skill}/scripts/"*.py 2>/dev/null || true

  mkdir -p "${target_dir}/.agents/workflows" "${target_dir}/.agent/workflows" "${target_dir}/.claude/commands/opsx" "${target_dir}/scratch"
  cp "${SKILL_DIR}/resources/workflow-opsx-pixel-perfect.md" "${target_dir}/.agents/workflows/opsx-pixel-perfect.md"
  cp "${SKILL_DIR}/resources/workflow-opsx-pixel-perfect.md" "${target_dir}/.agent/workflows/opsx-pixel-perfect.md"
  cp "${SKILL_DIR}/resources/claude-command-pixel-perfect.md" "${target_dir}/.claude/commands/opsx/pixel-perfect.md"

  echo "HOÀN TẤT ĐỒNG BỘ OPSX-PIXEL-PERFECT-UI (/opsx-pixel-perfect)!"

  if [ "${run_scan}" -eq 1 ]; then
    python3 "${target_agents_skill}/scripts/scan-pixel-perfect-traps.py" "${target_dir}"
  fi
}

main() {
  local target_dir=""
  local run_scan=0

  while [ "$#" -gt 0 ]; do
    case "$1" in
      --scan)
        run_scan=1
        shift
        ;;
      --export-to)
        target_dir="${2:-}"
        shift 2
        ;;
      *)
        target_dir="$1"
        shift
        ;;
    esac
  done

  if [ -z "${target_dir}" ]; then
    target_dir="$(pwd)"
  fi

  bootstrap_pixel_perfect "${target_dir}" "${run_scan}"
}

main "$@"
