#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# OPSX Team Skill Suite Bootstrap Script
# - Non-destructive (100% no rm / file deletion)
# - Idempotent & Portable ($HOME & relative paths only)
# - Modes:
#     1) Local bootstrap: ./bootstrap.sh [project_dir]
#     2) Export & bootstrap to new project: ./bootstrap.sh --export-to <target_dir>
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

resolve_suite_agents_dir() {
  local candidate_repo_agents
  candidate_repo_agents="$(cd "${SKILL_DIR}/../.." 2>/dev/null && pwd || true)"
  if [ -n "${candidate_repo_agents}" ] && [ -d "${candidate_repo_agents}/skills" ] && [ "$(basename "${candidate_repo_agents}")" = ".agents" ]; then
    printf "%s\n" "${candidate_repo_agents}"
    return 0
  fi

  if [ -d "${HOME}/.gemini/config/team-skill-suite/.agents/skills" ]; then
    printf "%s\n" "${HOME}/.gemini/config/team-skill-suite/.agents"
    return 0
  fi

  return 1
}

print_usage() {
  cat <<'EOF'
Cách dùng:
  1. Khởi tạo / đồng bộ ngay tại dự án hiện tại:
       ./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh

  2. Xuất trọn bộ Team Skill Suite & khởi tạo cho dự án mới:
       ./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --export-to <duong_dan_du_an_moi>
EOF
}

detect_tech_stack() {
  local project_dir="$1"
  local stack_items=()

  if [ -f "${project_dir}/composer.json" ]; then
    if grep -q '"laravel/framework"' "${project_dir}/composer.json" 2>/dev/null; then
      stack_items+=("Laravel")
    else
      stack_items+=("PHP/Composer")
    fi
    if grep -q '"jamstackvietnam/essentials"' "${project_dir}/composer.json" 2>/dev/null; then
      stack_items+=("BED-CMS-Essentials")
    fi
  fi

  if [ -f "${project_dir}/package.json" ]; then
    if grep -q '"next"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("Next.js")
    fi
    if grep -q '"react"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("React")
    fi
    if grep -q '"vue"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("Vue-3")
    fi
    if grep -q '"@inertiajs/' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("Inertia.js")
    fi
    if grep -q '"tailwindcss"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("TailwindCSS")
    fi
    if grep -q '"primevue"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("PrimeVue")
    fi
    if grep -q '"sass"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("Sass")
    fi
    if grep -q '"ziggy-js"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("Ziggy")
    fi
    if grep -q '"vue-i18n"' "${project_dir}/package.json" 2>/dev/null; then
      stack_items+=("vue-i18n")
    fi
  fi

  local ui_layout="Custom/Unknown"
  if [ -d "${project_dir}/resources/Frontend/js" ]; then
    ui_layout="Laravel BED CMS (resources/Frontend/js/{Components,Layouts,Pages})"
  elif [ -d "${project_dir}/resources/js" ]; then
    ui_layout="Laravel Standard Inertia (resources/js/{Components,Layouts,Pages})"
  elif [ -d "${project_dir}/src/app" ] || [ -d "${project_dir}/app" ]; then
    if [ -f "${project_dir}/package.json" ] && grep -q '"next"' "${project_dir}/package.json" 2>/dev/null; then
      ui_layout="Next.js App Router (src/app & src/components)"
    fi
  fi

  if [ "${#stack_items[@]}" -eq 0 ]; then
    echo "   - Tech Stack: Chưa phát hiện composer.json / package.json (Greenfield hoặc stack khác)"
  else
    echo "   - Tech Stack: ${stack_items[*]}"
  fi
  echo "   - Cấu trúc UI nhận diện: ${ui_layout}"
}

check_and_update_gitignore() {
  local project_dir="$1"
  local gitignore_file="${project_dir}/.gitignore"

  if [ ! -f "${gitignore_file}" ]; then
    printf "/graphify-out\n" > "${gitignore_file}"
    echo "   - Đã tạo mới .gitignore với mục /graphify-out"
    return 0
  fi

  if grep -Eq '^[[:space:]]*(/)?(\.agents|\.agent|openspec|\.openspec)(/)?[[:space:]]*$' "${gitignore_file}"; then
    echo "   - [CẢNH BÁO] .gitignore đang chặn theo dõi .agents hoặc openspec!"
    echo "     Hãy gỡ các dòng '/openspec', '.openspec', '.agent', '.agents' trong .gitignore để git push bộ skill cho cả team."
  else
    echo "   - .gitignore hợp lệ: .agents/ và openspec/ được phép theo dõi qua Git."
  fi

  if grep -Eq '^[[:space:]]*(/)?graphify-out(/)?[[:space:]]*$' "${gitignore_file}"; then
    echo "   - .gitignore đã có mục loại trừ /graphify-out."
  else
    printf "\n# Graphify local AST output\n/graphify-out\n" >> "${gitignore_file}"
    echo "   - Đã bổ sung /graphify-out vào cuối .gitignore."
  fi
}

run_openspec_setup() {
  local project_dir="$1"

  if ! command -v openspec >/dev/null 2>&1; then
    echo "   - [CẢNH BÁO] Máy chưa cài đặt 'openspec' CLI."
    echo "     Cài đặt ngay bằng lệnh: npm install -g @fission-ai/openspec@latest"
    return 0
  fi

  local os_version
  os_version="$(openspec --version 2>/dev/null || echo "unknown")"
  echo "   - Đã tìm thấy openspec CLI (phiên bản ${os_version})."

  if [ ! -d "${project_dir}/openspec" ]; then
    echo "   - Chưa có thư mục openspec/ -> Đang chạy 'openspec init --tools antigravity --no-animation'..."
    (cd "${project_dir}" && openspec init --tools antigravity --no-animation)
  else
    echo "   - Đã có thư mục openspec/ -> Đang chạy 'openspec update' để đồng bộ..."
    (cd "${project_dir}" && openspec update)
  fi
}

run_graphify_index() {
  local project_dir="$1"
  local graphify_bin=""

  if command -v graphify >/dev/null 2>&1; then
    graphify_bin="$(command -v graphify)"
  elif [ -x "${HOME}/.local/bin/graphify" ]; then
    graphify_bin="${HOME}/.local/bin/graphify"
  fi

  if [ -z "${graphify_bin}" ]; then
    echo "   - [LƯU Ý] Máy chưa cài đặt binary 'graphify' (bỏ qua bước sinh AST mà không dừng script)."
    echo "     Đồng đội cài đặt nhanh bằng lệnh: uv tool install graphifyy"
    echo "     (Hoặc: pipx install graphifyy)"
    return 0
  fi

  echo "   - Đang chạy '${graphify_bin} . --code-only'..."
  if (cd "${project_dir}" && "${graphify_bin}" . --code-only); then
    if [ -f "${project_dir}/graphify-out/graph.json" ]; then
      echo "   - Đã sinh thành công bản đồ AST tại graphify-out/graph.json."
    fi
  else
    echo "   - [CẢNH BÁO] Lệnh graphify gặp sự cố nhỏ trên dự án này, vui lòng kiểm tra log phía trên."
  fi
}

export_suite_to_target() {
  local target_dir="$1"
  local suite_agents_dir=""

  if ! suite_agents_dir="$(resolve_suite_agents_dir)"; then
    echo "[LỖI] Không tìm thấy thư mục nguồn .agents/ của bộ Team Skill Suite để xuất." >&2
    exit 1
  fi

  mkdir -p "${target_dir}/.agents/skills" "${target_dir}/.agents/agents" "${target_dir}/.agents/workflows" "${target_dir}/.agents/scripts"

  echo "==> [Export] Đang sao chép bộ Skills, Specialist Sub-Agents, Workflows và Scripts sang: ${target_dir}"
  cp -R "${suite_agents_dir}/skills/." "${target_dir}/.agents/skills/"

  if [ -d "${suite_agents_dir}/agents" ]; then
    cp -R "${suite_agents_dir}/agents/." "${target_dir}/.agents/agents/"
  fi

  if [ -d "${suite_agents_dir}/workflows" ]; then
    cp -R "${suite_agents_dir}/workflows/." "${target_dir}/.agents/workflows/"
  fi

  if [ -d "${suite_agents_dir}/scripts" ]; then
    cp -R "${suite_agents_dir}/scripts/." "${target_dir}/.agents/scripts/"
  fi

  local agents_template="${SKILL_DIR}/resources/AGENTS.template.md"
  if [ ! -f "${target_dir}/AGENTS.md" ] && [ -f "${agents_template}" ]; then
    cp "${agents_template}" "${target_dir}/AGENTS.md"
    echo "   - Đã tạo mới AGENTS.md chuẩn từ template tại dự án đích."
  else
    echo "   - Dự án đích đã có sẵn AGENTS.md -> Giữ nguyên an toàn (không ghi đè)."
  fi
}

bootstrap_project() {
  local project_dir="$1"
  project_dir="$(cd "${project_dir}" && pwd)"

  echo "=================================================================="
  echo "OPSX Bootstrap — Khởi tạo & Đồng bộ Chuẩn Kỹ Thuật Team"
  echo "Thư mục dự án: ${project_dir}"
  echo "=================================================================="

  echo ""
  echo "[1/4] Nhận diện Tech Stack & Kiến trúc UI..."
  detect_tech_stack "${project_dir}"

  echo ""
  echo "[2/4] Kiểm tra & bảo vệ .gitignore..."
  check_and_update_gitignore "${project_dir}"

  echo ""
  echo "[3/4] Khởi tạo / Đồng bộ OpenSpec (OPSX)..."
  run_openspec_setup "${project_dir}"

  local config_template="${SKILL_DIR}/resources/openspec-config.template.yaml"
  if [ -f "${project_dir}/openspec/config.yaml" ] && ! grep -q '^context:' "${project_dir}/openspec/config.yaml" 2>/dev/null; then
    echo "   - [GỢI Ý] openspec/config.yaml chưa khai báo 'context:' thực tế."
    echo "     Hãy tham chiếu bản mẫu tại: .agents/skills/opsx-bootstrap/resources/openspec-config.template.yaml"
  fi

  echo ""
  echo "[4/4] Khởi tạo Đồ thị Kiến trúc AST (Graphify)..."
  run_graphify_index "${project_dir}"

  local skill_count=0
  local agent_count=0
  local script_count=0
  if [ -d "${project_dir}/.agents/skills" ]; then
    skill_count="$(find "${project_dir}/.agents/skills" -mindepth 2 -maxdepth 2 -name "SKILL.md" | wc -l | tr -d ' ')"
  fi
  if [ -d "${project_dir}/.agents/agents" ]; then
    agent_count="$(find "${project_dir}/.agents/agents" -mindepth 1 -maxdepth 1 -name "*.md" | wc -l | tr -d ' ')"
  fi
  if [ -d "${project_dir}/.agents/scripts" ]; then
    script_count="$(find "${project_dir}/.agents/scripts" -mindepth 1 -maxdepth 1 -name "*.py" | wc -l | tr -d ' ')"
  fi

  echo ""
  echo "=================================================================="
  echo "HOÀN TẤT OPSX BOOTSTRAP!"
  echo " - Tổng số Skills sẵn sàng (.agents/skills): ${skill_count}"
  echo " - Tổng số Specialist Agents (.agents/agents): ${agent_count}"
  echo " - Tổng số Python Scripts (.agents/scripts): ${script_count}"
  echo "=================================================================="
}

main() {
  if [ "$#" -eq 0 ]; then
    bootstrap_project "$(pwd)"
    exit 0
  fi

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
      local target_path="$2"
      mkdir -p "${target_path}"
      target_path="$(cd "${target_path}" && pwd)"
      export_suite_to_target "${target_path}"
      echo ""
      bootstrap_project "${target_path}"
      ;;
    *)
      if [ -d "$1" ]; then
        bootstrap_project "$1"
      else
        echo "[LỖI] Tham số hoặc thư mục không hợp lệ: $1" >&2
        print_usage
        exit 1
      fi
      ;;
  esac
}

main "$@"
