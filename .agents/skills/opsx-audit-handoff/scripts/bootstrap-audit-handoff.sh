#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# OPSX Multi-Model Audit, Source Slimming & Handoff Bootstrap Script
# - Non-destructive (100% no rm / file deletion)
# - Idempotent & Portable ($HOME & relative paths only)
# - Modes:
#     1) Local setup: ./bootstrap-audit-handoff.sh [target_dir]
#     2) Export & setup: ./bootstrap-audit-handoff.sh --export-to <target_dir>
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

print_usage() {
  cat <<'EOF'
Cách dùng:
  1. Thiết lập / đồng bộ quy trình Audit, Source Slimming & Handoff ngay tại dự án hiện tại:
       ./.agent/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh

  2. Xuất quy trình Audit, Source Slimming & Handoff sang dự án khác:
       ./.agent/skills/opsx-audit-handoff/scripts/bootstrap-audit-handoff.sh --export-to <duong_dan_du_an_dich>
EOF
}

bootstrap_audit_handoff() {
  local target_dir="$1"
  mkdir -p "${target_dir}"
  target_dir="$(cd "${target_dir}" && pwd)"

  echo "=================================================================="
  echo "OPSX Audit & Handoff — Đồng bộ Quy trình Kiểm toán, Dọn Hardcode CMS & Làm Nhẹ Source 5 Tầng"
  echo "Thư mục dự án đích: ${target_dir}"
  echo "=================================================================="

  # 1. Đồng bộ Skill vào .agent/skills/opsx-audit-handoff (và .agents/ nếu có)
  echo ""
  echo "[1/5] Đồng bộ Skill 'opsx-audit-handoff'..."
  local target_agent_skill="${target_dir}/.agent/skills/opsx-audit-handoff"
  mkdir -p "${target_agent_skill}"
  if [ "${SKILL_DIR}" != "${target_agent_skill}" ]; then
    cp -R "${SKILL_DIR}/." "${target_agent_skill}/"
  fi
  chmod +x "${target_agent_skill}/scripts/"*.sh "${target_agent_skill}/scripts/"*.py 2>/dev/null || true
  echo "   - Đã cài đặt Skill tại: .agent/skills/opsx-audit-handoff/SKILL.md"

  if [ -d "${target_dir}/.agents" ]; then
    local target_agents_skill="${target_dir}/.agents/skills/opsx-audit-handoff"
    mkdir -p "${target_agents_skill}"
    if [ "${SKILL_DIR}" != "${target_agents_skill}" ]; then
      cp -R "${SKILL_DIR}/." "${target_agents_skill}/"
    fi
    chmod +x "${target_agents_skill}/scripts/"*.sh "${target_agents_skill}/scripts/"*.py 2>/dev/null || true
    echo "   - Đã đồng bộ thêm Skill tại: .agents/skills/opsx-audit-handoff/SKILL.md"
  fi

  # 2. Đồng bộ Workflow Antigravity (.agent/workflows/opsx-audit-handoff.md)
  echo ""
  echo "[2/5] Đồng bộ Antigravity Workflow '/opsx-audit-handoff'..."
  local wf_src="${SKILL_DIR}/resources/workflow-opsx-audit-handoff.md"
  mkdir -p "${target_dir}/.agent/workflows"
  cp "${wf_src}" "${target_dir}/.agent/workflows/opsx-audit-handoff.md"
  echo "   - Đã cài đặt Workflow tại: .agent/workflows/opsx-audit-handoff.md"

  if [ -d "${target_dir}/.agents" ]; then
    mkdir -p "${target_dir}/.agents/workflows"
    cp "${wf_src}" "${target_dir}/.agents/workflows/opsx-audit-handoff.md"
    echo "   - Đã đồng bộ thêm Workflow tại: .agents/workflows/opsx-audit-handoff.md"
  fi

  # 3. Đồng bộ Claude Code Command (.claude/commands/opsx/audit-handoff.md)
  echo ""
  echo "[3/5] Đồng bộ Claude Code Command '/opsx:audit-handoff'..."
  local cmd_src="${SKILL_DIR}/resources/claude-command-audit-handoff.md"
  mkdir -p "${target_dir}/.claude/commands/opsx"
  cp "${cmd_src}" "${target_dir}/.claude/commands/opsx/audit-handoff.md"
  echo "   - Đã cài đặt Claude Command tại: .claude/commands/opsx/audit-handoff.md"

  # 4. Khởi tạo & Đồng bộ OpenSpec Change Template (Bảo toàn các task đã tick [x] hoặc main spec đã archive)
  echo ""
  echo "[4/5] Kiểm tra & Đồng bộ OpenSpec Change Template..."
  local openspec_src="${SKILL_DIR}/resources/openspec-change"
  local openspec_dest="${target_dir}/openspec/changes/opsx-audit-handoff"
  local openspec_legacy="${target_dir}/openspec/changes/add-multi-model-audit-handoff"
  local openspec_main_spec="${target_dir}/openspec/specs/multi-model-audit-handoff/spec.md"

  if [ -d "${openspec_dest}" ]; then
    mkdir -p "${openspec_dest}/specs/multi-model-audit-handoff"
    cp "${openspec_src}/proposal.md" "${openspec_dest}/proposal.md"
    cp "${openspec_src}/design.md" "${openspec_dest}/design.md"
    cp "${openspec_src}/specs/multi-model-audit-handoff/spec.md" "${openspec_dest}/specs/multi-model-audit-handoff/spec.md"
    if [ -f "${openspec_dest}/tasks.md" ] && grep -q "\- \[x\]" "${openspec_dest}/tasks.md" 2>/dev/null; then
      echo "   - Đã cập nhật proposal, design, specs tại openspec/changes/opsx-audit-handoff/ (giữ nguyên tasks.md đang có tiến độ [x])."
    else
      cp "${openspec_src}/tasks.md" "${openspec_dest}/tasks.md"
      echo "   - Đã đồng bộ bản mới nhất tại openspec/changes/opsx-audit-handoff/ (proposal, design, tasks, specs)."
    fi
  elif [ -f "${openspec_main_spec}" ]; then
    echo "   - Đã phát hiện Main Spec tại openspec/specs/multi-model-audit-handoff/spec.md (change đã được archive)."
    echo "   - Giữ sạch openspec/changes/ (bản mẫu mới nhất luôn sẵn sàng trong .agent/skills/opsx-audit-handoff/resources/openspec-change/)."
  elif [ -d "${openspec_legacy}" ]; then
    echo "   - Đã phát hiện bản OpenSpec hiện hữu tại openspec/changes/add-multi-model-audit-handoff/."
    mkdir -p "${openspec_dest}"
    cp -R "${openspec_src}/." "${openspec_dest}/"
    echo "   - Đã bổ sung thêm template chuẩn tại openspec/changes/opsx-audit-handoff/."
  else
    mkdir -p "${openspec_dest}"
    cp -R "${openspec_src}/." "${openspec_dest}/"
    echo "   - Đã khởi tạo mới openspec/changes/opsx-audit-handoff/ (proposal, design, tasks, specs)."
  fi

  # 5. Đảm bảo thư mục scratch/ sẵn sàng cho 5 tầng báo cáo
  echo ""
  echo "[5/5] Chuẩn bị thư mục trung gian 'scratch/' cho 5 tầng kiểm toán..."
  mkdir -p "${target_dir}/scratch"
  echo "   - Thư mục scratch/ đã sẵn sàng lưu trữ step1 -> step4 audit reports."

  echo ""
  echo "=================================================================="
  echo "HOÀN TẤT ĐỒNG BỘ OPSX-AUDIT-HANDOFF (KÈM SOURCE SLIMMING AUDIT)!"
  echo " - Gọi trên Antigravity : /opsx-audit-handoff"
  echo " - Gọi trên Claude Code : /opsx:audit-handoff"
  echo "=================================================================="
}

main() {
  local target_dir=""

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -h|--help)
        print_usage
        exit 0
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

  bootstrap_audit_handoff "${target_dir}"
}

main "$@"
