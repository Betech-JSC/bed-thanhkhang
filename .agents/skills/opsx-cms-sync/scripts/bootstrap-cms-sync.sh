#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# OPSX CMS Sync Bootstrap & Export Script
# - Non-destructive & Safe (100% khong xoa file)
# - Idempotent & Portable ($HOME & relative paths only)
# - Modes:
#     1) Local setup : ./bootstrap-cms-sync.sh [target_dir] [--audit]
#     2) Export setup: ./bootstrap-cms-sync.sh --export-to <target_dir> [--audit]
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

print_usage() {
  cat <<'EOF'
Cách dùng:
  1. Đồng bộ bộ skill '/opsx-cms-sync' ngay tại dự án hiện tại:
       ./.agents/skills/opsx-cms-sync/scripts/bootstrap-cms-sync.sh [--audit]

  2. Xuất bộ skill '/opsx-cms-sync' sang dự án khác:
       ./.agents/skills/opsx-cms-sync/scripts/bootstrap-cms-sync.sh --export-to <duong_dan_du_an_dich> [--audit]
EOF
}

bootstrap_cms_sync() {
  local target_dir="$1"
  local run_audit="$2"

  mkdir -p "${target_dir}"
  target_dir="$(cd "${target_dir}" && pwd)"

  echo "=================================================================="
  echo "OPSX CMS Sync — Đồng Bộ Bộ Skill Điều Phối Hệ Thống CMS Dynamic A–Z"
  echo "Thư mục dự án đích: ${target_dir}"
  echo "=================================================================="

  # 1. Đồng bộ vào .agents/skills/opsx-cms-sync và .agent/skills/opsx-cms-sync
  echo ""
  echo "[1/4] Đồng bộ Skill 'opsx-cms-sync'..."
  local target_agents_skill="${target_dir}/.agents/skills/opsx-cms-sync"
  local target_agent_skill="${target_dir}/.agent/skills/opsx-cms-sync"

  mkdir -p "${target_agents_skill}" "${target_agent_skill}"
  if [ "${SKILL_DIR}" != "${target_agents_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agents_skill}/"
  fi
  if [ "${SKILL_DIR}" != "${target_agent_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agent_skill}/"
  fi

  chmod +x "${target_agents_skill}/scripts/"*.sh "${target_agents_skill}/scripts/"*.py 2>/dev/null || true
  chmod +x "${target_agent_skill}/scripts/"*.sh "${target_agent_skill}/scripts/"*.py 2>/dev/null || true
  echo "   - Đã cài đặt Skill tại: .agents/skills/opsx-cms-sync/SKILL.md"
  echo "   - Đã cài đặt Skill tại: .agent/skills/opsx-cms-sync/SKILL.md"

  # 2. Đồng bộ Antigravity Workflow (.agents/workflows/opsx-cms-sync.md & .agent/workflows/opsx-cms-sync.md)
  echo ""
  echo "[2/4] Đồng bộ Antigravity Workflow '/opsx-cms-sync'..."
  local wf_src="${SKILL_DIR}/resources/workflow-opsx-cms-sync.md"
  mkdir -p "${target_dir}/.agents/workflows" "${target_dir}/.agent/workflows"
  cp "${wf_src}" "${target_dir}/.agents/workflows/opsx-cms-sync.md"
  cp "${wf_src}" "${target_dir}/.agent/workflows/opsx-cms-sync.md"
  echo "   - Đã cài đặt Workflow tại: .agents/workflows/opsx-cms-sync.md"
  echo "   - Đã cài đặt Workflow tại: .agent/workflows/opsx-cms-sync.md"

  # 3. Đồng bộ Claude Code Command (.claude/commands/opsx/cms-sync.md)
  echo ""
  echo "[3/4] Đồng bộ Claude Code Command '/opsx:cms-sync'..."
  local cmd_src="${SKILL_DIR}/resources/claude-command-cms-sync.md"
  mkdir -p "${target_dir}/.claude/commands/opsx"
  cp "${cmd_src}" "${target_dir}/.claude/commands/opsx/cms-sync.md"
  echo "   - Đã cài đặt Claude Command tại: .claude/commands/opsx/cms-sync.md"

  # 4. Đảm bảo thư mục scratch/ sẵn sàng và tùy chọn chạy audit
  echo ""
  echo "[4/4] Chuẩn bị thư mục báo cáo 'scratch/'..."
  mkdir -p "${target_dir}/scratch"
  echo "   - Thư mục scratch/ đã sẵn sàng lưu trữ cms-sync-audit-report.md."

  if [ "${run_audit}" = "true" ]; then
    echo ""
    echo "==> Đang chạy kiểm toán chỉ đọc audit-cms-sync.py..."
    python3 "${target_agents_skill}/scripts/audit-cms-sync.py" "${target_dir}"
  fi

  echo ""
  echo "=================================================================="
  echo "HOÀN TẤT ĐỒNG BỘ OPSX-CMS-SYNC!"
  echo " - Gọi trên Antigravity : /opsx-cms-sync"
  echo " - Gọi trên Claude Code : /opsx:cms-sync"
  echo "=================================================================="
}

main() {
  local target_dir=""
  local run_audit="false"

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -h|--help)
        print_usage
        exit 0
        ;;
      --audit|--scan)
        run_audit="true"
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

  bootstrap_cms_sync "${target_dir}" "${run_audit}"
}

main "$@"
