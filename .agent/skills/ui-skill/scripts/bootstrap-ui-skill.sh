#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# UI Skill — Multi-Project Bootstrap & Export Script
# - Non-destructive (100% no rm / file deletion)
# - Idempotent & Portable ($HOME & relative paths only)
# - Modes:
#     1) Local setup: ./bootstrap-ui-skill.sh [target_dir] [--scan]
#     2) Export & setup: ./bootstrap-ui-skill.sh --export-to <target_dir> [--scan]
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

print_usage() {
  cat <<'EOF'
Cách dùng:
  1. Thiết lập / đồng bộ bộ 'ui-skill' ngay tại dự án hiện tại (kèm tùy chọn quét ngay):
       ./.agents/skills/ui-skill/scripts/bootstrap-ui-skill.sh [--scan]

  2. Xuất trọn bộ 'ui-skill' (Skill + Script + Workflow + Claude Command) sang dự án khác:
       ./.agents/skills/ui-skill/scripts/bootstrap-ui-skill.sh --export-to <duong_dan_du_an_dich> [--scan]
EOF
}

bootstrap_ui_skill() {
  local target_dir="$1"
  local run_scan="${2:-0}"

  mkdir -p "${target_dir}"
  target_dir="$(cd "${target_dir}" && pwd)"

  echo "=================================================================="
  echo "UI Skill — Đồng bộ Bộ Kiểm Toán Độ Phủ Animation & Micro-Interaction"
  echo "Thư mục dự án đích: ${target_dir}"
  echo "=================================================================="

  # 1. Đồng bộ Skill vào .agents/skills/ui-skill và .agent/skills/ui-skill
  echo ""
  echo "[1/4] Đồng bộ Skill 'ui-skill'..."
  local target_agents_skill="${target_dir}/.agents/skills/ui-skill"
  mkdir -p "${target_agents_skill}"
  if [ "${SKILL_DIR}" != "${target_agents_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agents_skill}/"
  fi
  chmod +x "${target_agents_skill}/scripts/"*.sh 2>/dev/null || true
  echo "   - Đã cài đặt Skill tại: .agents/skills/ui-skill/SKILL.md"

  local target_agent_skill="${target_dir}/.agent/skills/ui-skill"
  mkdir -p "${target_agent_skill}"
  if [ "${SKILL_DIR}" != "${target_agent_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agent_skill}/"
  fi
  chmod +x "${target_agent_skill}/scripts/"*.sh 2>/dev/null || true
  echo "   - Đã đồng bộ thêm Skill tại: .agent/skills/ui-skill/SKILL.md"

  # 2. Đồng bộ Antigravity Workflow (.agents/workflows/ui-skill.md & .agent/workflows/ui-skill.md)
  echo ""
  echo "[2/4] Đồng bộ Antigravity Workflow '/ui-skill'..."
  local wf_src="${SKILL_DIR}/resources/workflow-ui-skill.md"
  mkdir -p "${target_dir}/.agents/workflows" "${target_dir}/.agent/workflows"
  cp "${wf_src}" "${target_dir}/.agents/workflows/ui-skill.md"
  cp "${wf_src}" "${target_dir}/.agent/workflows/ui-skill.md"
  echo "   - Đã cài đặt Workflow tại: .agents/workflows/ui-skill.md & .agent/workflows/ui-skill.md"

  # 3. Đồng bộ Claude Code Command (.claude/commands/opsx/ui-skill.md)
  echo ""
  echo "[3/4] Đồng bộ Claude Code Command '/opsx:ui-skill'..."
  local cmd_src="${SKILL_DIR}/resources/claude-command-ui-skill.md"
  mkdir -p "${target_dir}/.claude/commands/opsx"
  cp "${cmd_src}" "${target_dir}/.claude/commands/opsx/ui-skill.md"
  echo "   - Đã cài đặt Claude Command tại: .claude/commands/opsx/ui-skill.md"

  # 4. Chuẩn bị thư mục scratch/ lưu báo cáo
  echo ""
  echo "[4/4] Chuẩn bị thư mục báo cáo 'scratch/'..."
  mkdir -p "${target_dir}/scratch"
  echo "   - Thư mục scratch/ đã sẵn sàng cho scratch/ui-animation-audit-report.md"

  echo ""
  echo "=================================================================="
  echo "HOÀN TẤT ĐỒNG BỘ UI-SKILL!"
  echo " - Gọi trên Antigravity : /ui-skill"
  echo " - Gọi trên Claude Code : /opsx:ui-skill"
  echo " - Chạy quét trực tiếp  : ./.agents/skills/ui-skill/scripts/scan-missing-animations.sh"
  echo "=================================================================="

  if [ "${run_scan}" -eq 1 ]; then
    echo ""
    echo "==> Đang kích hoạt quét tự động 'scan-missing-animations.sh' trên: ${target_dir}"
    "${target_agents_skill}/scripts/scan-missing-animations.sh" "${target_dir}"
  fi
}

main() {
  local target_dir=""
  local run_scan=0

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -h|--help)
        print_usage
        exit 0
        ;;
      --scan)
        run_scan=1
        shift
        ;;
      --export-to)
        if [ "$#" -lt 2 ] || [ -z "${2:-}" ]; then
          echo "[LỖI] Thiếu đường dẫn dự án đích sau cờ --export-to." >&2
          print_usage
          exit 1
        fi
        target_dir="$2"
        shift 2
        ;;
      *)
        if [ -z "${target_dir}" ]; then
          target_dir="$1"
          shift
        else
          echo "[LỖI] Tham số không hợp lệ: $1" >&2
          print_usage
          exit 1
        fi
        ;;
    esac
  done

  if [ -z "${target_dir}" ]; then
    target_dir="$(pwd)"
  fi

  bootstrap_ui_skill "${target_dir}" "${run_scan}"
}

main "$@"
