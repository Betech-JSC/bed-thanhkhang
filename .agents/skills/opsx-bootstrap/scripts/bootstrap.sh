#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# OPSX Team Skill Suite Bootstrap Script (Pure 1-Second Lightweight Installer)
# - Non-destructive (100% khong xoa file)
# - Instant execution (< 1 second): NO heavy AST scan or network calls by default
# - Modes:
#     1) Local bootstrap: ./bootstrap.sh [project_dir] [--with-cicd <slug>] [--with-graphify]
#     2) Export & bootstrap: ./bootstrap.sh --export-to <target_dir> [--with-cicd <slug>] [--with-graphify]
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
  1. Khởi tạo / đồng bộ siêu tốc (1 giây) ngay tại dự án hiện tại:
       ./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh [--with-cicd <project-slug>]

  2. Xuất trọn bộ Team Skill Suite & khởi tạo cho dự án mới (1 giây):
       ./.agents/skills/opsx-bootstrap/scripts/bootstrap.sh --export-to <duong_dan_du_an_moi> [--with-cicd <project-slug>]
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
    echo "   - Tech Stack: Greenfield / Custom"
  else
    echo "   - Tech Stack: ${stack_items[*]}"
  fi
  echo "   - Cấu trúc UI: ${ui_layout}"
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
  else
    echo "   - .gitignore hợp lệ: .agents/ và openspec/ được phép theo dõi qua Git."
  fi

  if ! grep -Eq '^[[:space:]]*(/)?graphify-out(/)?[[:space:]]*$' "${gitignore_file}"; then
    printf "\n# Graphify local AST output\n/graphify-out\n" >> "${gitignore_file}"
    echo "   - Đã bổ sung /graphify-out vào cuối .gitignore."
  fi
}

setup_openspec_and_agents_files() {
  local project_dir="$1"
  local project_name
  project_name="$(basename "${project_dir}")"

  mkdir -p "${project_dir}/openspec/changes" "${project_dir}/openspec/specs"

  local config_file="${project_dir}/openspec/config.yaml"
  local config_template="${SKILL_DIR}/resources/openspec-config.template.yaml"
  if [ ! -f "${config_file}" ] || ! grep -q 'opsx-cms-sync' "${config_file}" 2>/dev/null; then
    if [ -f "${config_template}" ]; then
      sed -e "s|<PROJECT_NAME>|${project_name}|g" \
          -e "s|<DOMAIN_DESCRIPTION>|Hệ thống Website & CMS doanh nghiệp|g" \
          "${config_template}" > "${config_file}"
      echo "   - Đã tự động đồng bộ openspec/config.yaml chuẩn cho dự án '${project_name}'."
    fi
  else
    echo "   - openspec/config.yaml đã có sẵn cấu hình chuẩn 4 bộ skill Hub-and-Spoke."
  fi

  local agents_file="${project_dir}/AGENTS.md"
  local agents_template="${SKILL_DIR}/resources/AGENTS.template.md"
  if [ ! -f "${agents_file}" ] || ! grep -q 'opsx-cms-sync' "${agents_file}" 2>/dev/null; then
    if [ -f "${agents_template}" ]; then
      cp "${agents_template}" "${agents_file}"
      echo "   - Đã tự động đồng bộ AGENTS.md chuẩn (kèm 4 bộ skill Hub-and-Spoke)."
    fi
  else
    echo "   - AGENTS.md đã có sẵn quy chuẩn 4 bộ skill Hub-and-Spoke."
  fi
}

run_graphify_index() {
  local project_dir="$1"
  local force_graphify="${2:-0}"

  if [ "${force_graphify}" -eq 0 ]; then
    echo "   - Bỏ qua quét AST Graphify mặc định để hoàn tất cài đặt trong 1 giây (dùng --with-graphify khi muốn sinh AST)."
    return 0
  fi

  local graphify_bin=""
  if command -v graphify >/dev/null 2>&1; then
    graphify_bin="$(command -v graphify)"
  elif [ -x "${HOME}/.local/bin/graphify" ]; then
    graphify_bin="${HOME}/.local/bin/graphify"
  fi

  if [ -z "${graphify_bin}" ]; then
    echo "   - [LƯU Ý] Máy chưa cài đặt binary 'graphify' (uv tool install graphifyy)."
    return 0
  fi

  echo "   - Đang chạy '${graphify_bin} . --code-only'..."
  (cd "${project_dir}" && "${graphify_bin}" . --code-only) || true
}

sync_hub_skills_and_commands() {
  local project_dir="$1"

  mkdir -p "${project_dir}/.agent/skills" "${project_dir}/.agent/workflows" "${project_dir}/.agents/workflows" "${project_dir}/.claude/commands/opsx" "${project_dir}/scratch"

  # Đồng bộ các Hub Skills cốt lõi giữa .agents/skills và .agent/skills
  local hub_skills=("opsx-bootstrap" "opsx-figma-workflow" "opsx-cms-sync" "opsx-pixel-perfect-ui" "opsx-animation" "ui-skill" "opsx-i18n-cms" "opsx-i18n-fe" "opsx-audit-handoff" "fe-mobile-audit" "vps-cicd-deploy")
  for sk in "${hub_skills[@]}"; do
    if [ -d "${project_dir}/.agents/skills/${sk}" ]; then
      mkdir -p "${project_dir}/.agent/skills/${sk}"
      cp -R "${project_dir}/.agents/skills/${sk}/." "${project_dir}/.agent/skills/${sk}/"
      chmod +x "${project_dir}/.agents/skills/${sk}/scripts/"*.sh "${project_dir}/.agents/skills/${sk}/scripts/"*.py 2>/dev/null || true
      chmod +x "${project_dir}/.agent/skills/${sk}/scripts/"*.sh "${project_dir}/.agent/skills/${sk}/scripts/"*.py 2>/dev/null || true
    elif [ -d "${project_dir}/.agent/skills/${sk}" ]; then
      mkdir -p "${project_dir}/.agents/skills/${sk}"
      cp -R "${project_dir}/.agent/skills/${sk}/." "${project_dir}/.agents/skills/${sk}/"
      chmod +x "${project_dir}/.agents/skills/${sk}/scripts/"*.sh "${project_dir}/.agents/skills/${sk}/scripts/"*.py 2>/dev/null || true
      chmod +x "${project_dir}/.agent/skills/${sk}/scripts/"*.sh "${project_dir}/.agent/skills/${sk}/scripts/"*.py 2>/dev/null || true
    fi
  done

  # Đồng bộ Workflows từ resources của từng skill
  if [ -f "${project_dir}/.agents/skills/opsx-cms-sync/resources/workflow-opsx-cms-sync.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-cms-sync/resources/workflow-opsx-cms-sync.md" "${project_dir}/.agents/workflows/opsx-cms-sync.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-pixel-perfect-ui/resources/workflow-opsx-pixel-perfect.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-pixel-perfect-ui/resources/workflow-opsx-pixel-perfect.md" "${project_dir}/.agents/workflows/opsx-pixel-perfect.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-animation/resources/workflow-opsx-animation.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-animation/resources/workflow-opsx-animation.md" "${project_dir}/.agents/workflows/opsx-animation.md"
  fi
  if [ -f "${project_dir}/.agents/skills/ui-skill/resources/workflow-ui-skill.md" ]; then
    cp "${project_dir}/.agents/skills/ui-skill/resources/workflow-ui-skill.md" "${project_dir}/.agents/workflows/ui-skill.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-i18n-cms/resources/workflow-opsx-i18n-cms.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-i18n-cms/resources/workflow-opsx-i18n-cms.md" "${project_dir}/.agents/workflows/opsx-i18n-cms.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-i18n-fe/resources/workflow-opsx-i18n-fe.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-i18n-fe/resources/workflow-opsx-i18n-fe.md" "${project_dir}/.agents/workflows/opsx-i18n-fe.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-audit-handoff/resources/workflow-opsx-audit-handoff.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-audit-handoff/resources/workflow-opsx-audit-handoff.md" "${project_dir}/.agents/workflows/opsx-audit-handoff.md"
  fi

  # Đồng bộ toàn bộ Workflows giữa .agents/workflows và .agent/workflows
  local wf_files=("opsx-cms-sync.md" "opsx-pixel-perfect.md" "opsx-animation.md" "ui-skill.md" "opsx-i18n-cms.md" "opsx-i18n-fe.md" "opsx-audit-handoff.md" "opsx-bootstrap.md" "opsx-deploy.md")
  for wf in "${wf_files[@]}"; do
    if [ -f "${project_dir}/.agents/workflows/${wf}" ]; then
      cp "${project_dir}/.agents/workflows/${wf}" "${project_dir}/.agent/workflows/${wf}"
    elif [ -f "${project_dir}/.agent/workflows/${wf}" ]; then
      cp "${project_dir}/.agent/workflows/${wf}" "${project_dir}/.agents/workflows/${wf}"
    fi
  done

  # Đồng bộ Claude Code Commands (.claude/commands/opsx/) từ resources của từng skill
  if [ -f "${project_dir}/.agents/skills/opsx-cms-sync/resources/claude-command-cms-sync.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-cms-sync/resources/claude-command-cms-sync.md" "${project_dir}/.claude/commands/opsx/cms-sync.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-pixel-perfect-ui/resources/claude-command-pixel-perfect.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-pixel-perfect-ui/resources/claude-command-pixel-perfect.md" "${project_dir}/.claude/commands/opsx/pixel-perfect.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-animation/resources/claude-command-animation.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-animation/resources/claude-command-animation.md" "${project_dir}/.claude/commands/opsx/animation.md"
  fi
  if [ -f "${project_dir}/.agents/skills/ui-skill/resources/claude-command-ui-skill.md" ]; then
    cp "${project_dir}/.agents/skills/ui-skill/resources/claude-command-ui-skill.md" "${project_dir}/.claude/commands/opsx/ui-skill.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-i18n-cms/resources/claude-command-i18n-cms.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-i18n-cms/resources/claude-command-i18n-cms.md" "${project_dir}/.claude/commands/opsx/i18n-cms.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-i18n-fe/resources/claude-command-i18n-fe.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-i18n-fe/resources/claude-command-i18n-fe.md" "${project_dir}/.claude/commands/opsx/i18n-fe.md"
  fi
  if [ -f "${project_dir}/.agents/skills/opsx-audit-handoff/resources/claude-command-audit-handoff.md" ]; then
    cp "${project_dir}/.agents/skills/opsx-audit-handoff/resources/claude-command-audit-handoff.md" "${project_dir}/.claude/commands/opsx/audit-handoff.md"
  fi

  echo "   - Đã đồng bộ Hub Skills & Workflows: /opsx-bootstrap, /opsx-figma-workflow, /opsx-cms-sync, /opsx-pixel-perfect, /opsx-animation, /ui-skill, /opsx-i18n-cms, /opsx-i18n-fe, /opsx-audit-handoff, /fe-mobile-audit, /opsx-deploy"
  echo "   - Đã đồng bộ Claude Commands: .claude/commands/opsx/{cms-sync,pixel-perfect,animation,ui-skill,i18n-cms,i18n-fe,audit-handoff}.md"
}

setup_or_check_cicd() {
  local project_dir="$1"
  local cicd_slug="${2:-}"
  local workflow_file="${project_dir}/.github/workflows/ci-cd.yml"
  local template_file="${project_dir}/.agents/skills/vps-cicd-deploy/resources/ci-cd.laravel-bed.template.yml"

  if [ ! -f "${template_file}" ] && [ -f "${HOME}/.gemini/config/skills/vps-cicd-deploy/resources/ci-cd.laravel-bed.template.yml" ]; then
    template_file="${HOME}/.gemini/config/skills/vps-cicd-deploy/resources/ci-cd.laravel-bed.template.yml"
  fi

  if [ -n "${cicd_slug}" ]; then
    if [ -f "${workflow_file}" ]; then
      echo "   - Đã có sẵn .github/workflows/ci-cd.yml -> Giữ nguyên an toàn."
    elif [ -f "${template_file}" ]; then
      mkdir -p "${project_dir}/.github/workflows"
      sed -e "s|__PROJECT_SLUG__|${cicd_slug}|g" \
          -e "s|__TEST_COMMAND__|php artisan test|g" \
          "${template_file}" > "${workflow_file}"
      echo "   - Đã khởi tạo .github/workflows/ci-cd.yml cho dự án '${cicd_slug}'."
    fi
  else
    if [ -f "${workflow_file}" ]; then
      echo "   - Đã phát hiện pipeline CI/CD tại: .github/workflows/ci-cd.yml"
    else
      echo "   - Chưa có .github/workflows/ci-cd.yml (gọi '/opsx-deploy' hoặc '--with-cicd <slug>' khi cần)."
    fi
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

  echo "==> [Export] Đang sao chép siêu tốc bộ Skills, Sub-Agents, Workflows & Scripts sang: ${target_dir}"
  if [ "${suite_agents_dir}/skills" != "${target_dir}/.agents/skills" ]; then
    cp -R "${suite_agents_dir}/skills/." "${target_dir}/.agents/skills/"
  fi

  if [ -d "${suite_agents_dir}/agents" ] && [ "${suite_agents_dir}/agents" != "${target_dir}/.agents/agents" ]; then
    cp -R "${suite_agents_dir}/agents/." "${target_dir}/.agents/agents/"
  fi

  if [ -d "${suite_agents_dir}/workflows" ] && [ "${suite_agents_dir}/workflows" != "${target_dir}/.agents/workflows" ]; then
    cp -R "${suite_agents_dir}/workflows/." "${target_dir}/.agents/workflows/"
  fi

  if [ -d "${suite_agents_dir}/scripts" ] && [ "${suite_agents_dir}/scripts" != "${target_dir}/.agents/scripts" ]; then
    cp -R "${suite_agents_dir}/scripts/." "${target_dir}/.agents/scripts/"
  fi
}

bootstrap_project() {
  local project_dir="$1"
  local cicd_slug="${2:-}"
  local force_graphify="${3:-0}"
  project_dir="$(cd "${project_dir}" && pwd)"

  echo "=================================================================="
  echo "OPSX Bootstrap — Pure 1-Second Lightweight Installer"
  echo "Thư mục dự án: ${project_dir}"
  echo "=================================================================="

  echo "[1/5] Nhận diện Tech Stack & Kiến trúc UI..."
  detect_tech_stack "${project_dir}"

  echo "[2/5] Đồng bộ Hệ Sinh Thái Hub Skills, Workflows & Claude Commands..."
  sync_hub_skills_and_commands "${project_dir}"

  echo "[3/5] Tự động đồng bộ AGENTS.md, openspec/config.yaml & .gitignore..."
  setup_openspec_and_agents_files "${project_dir}"
  check_and_update_gitignore "${project_dir}"

  echo "[4/5] Kiểm tra trạng thái AST (Graphify)..."
  run_graphify_index "${project_dir}" "${force_graphify}"

  echo "[5/5] Kiểm tra CI/CD Pipeline & VPS Deployment Skill..."
  setup_or_check_cicd "${project_dir}" "${cicd_slug}"

  local skill_count=0
  local agent_count=0
  local workflow_count=0
  if [ -d "${project_dir}/.agents/skills" ]; then
    skill_count="$(find "${project_dir}/.agents/skills" -mindepth 2 -maxdepth 2 -name "SKILL.md" | wc -l | tr -d ' ')"
  fi
  if [ -d "${project_dir}/.agents/agents" ]; then
    agent_count="$(find "${project_dir}/.agents/agents" -mindepth 1 -maxdepth 1 -name "*.md" | wc -l | tr -d ' ')"
  fi
  if [ -d "${project_dir}/.agents/workflows" ]; then
    workflow_count="$(find "${project_dir}/.agents/workflows" -mindepth 1 -maxdepth 1 -name "*.md" | wc -l | tr -d ' ')"
  fi

  echo ""
  echo "=================================================================="
  echo "HOÀN TẤT OPSX BOOTSTRAP TRONG 1 GIÂY!"
  echo " - Tổng số Skills sẵn sàng (.agents/skills)     : ${skill_count}"
  echo " - Tổng số Specialist Agents (.agents/agents)   : ${agent_count}"
  echo " - Tổng số Workflows (.agents/workflows)        : ${workflow_count}"
  echo "------------------------------------------------------------------"
  echo " CÁC LỆNH ĐIỀU PHỐI CHUYÊN BIỆT (GÕ Ở ĐOẠN HỘI THOẠI MỚI):"
  echo "   1. /opsx-cms-sync      : Đồng bộ CMS Dynamic A-Z, Quét UI chưa connect & Điều hướng"
  echo "   2. /opsx-pixel-perfect : Chuẩn hóa Giao diện 100% Pixel-Perfect vs Figma MCP (--page)"
  echo "   3. /opsx-animation     : Điều phối Animation chuẩn 4 Quy Tắc Vàng (/ui-skill)"
  echo "   4. /opsx-i18n-cms      : Đa ngôn ngữ Backend CMS (VI/EN) & Auto-Fallback EN->VI"
  echo "   5. /opsx-i18n-fe       : Đa ngôn ngữ Frontend theo Trang Figma (--pages / --page)"
  echo "   6. /opsx-audit-handoff : Kiểm toán 5 tầng, dọn Hardcode CMS & làm nhẹ source"
  echo "   7. /opsx-deploy        : Triển khai VPS Production & GitHub Actions CI/CD"
  echo "=================================================================="
}

main() {
  local target_dir=""
  local do_export=0
  local cicd_slug=""
  local force_graphify=0

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -h|--help)
        print_usage
        exit 0
        ;;
      --with-graphify)
        force_graphify=1
        shift
        ;;
      --export-to)
        if [ "$#" -lt 2 ] || [ -z "${2:-}" ]; then
          echo "[LỖI] Thiếu đường dẫn dự án đích sau cờ --export-to." >&2
          print_usage
          exit 1
        fi
        do_export=1
        target_dir="$2"
        shift 2
        ;;
      --with-cicd)
        if [ "$#" -lt 2 ] || [ -z "${2:-}" ]; then
          echo "[LỖI] Thiếu project-slug sau cờ --with-cicd." >&2
          print_usage
          exit 1
        fi
        cicd_slug="$2"
        shift 2
        ;;
      *)
        if [ -z "${target_dir}" ] && [ -d "$1" ]; then
          target_dir="$1"
          shift
        else
          echo "[LỖI] Tham số hoặc thư mục không hợp lệ: $1" >&2
          print_usage
          exit 1
        fi
        ;;
    esac
  done

  if [ -z "${target_dir}" ]; then
    target_dir="$(pwd)"
  fi

  if [ "${do_export}" -eq 1 ]; then
    mkdir -p "${target_dir}"
    target_dir="$(cd "${target_dir}" && pwd)"
    export_suite_to_target "${target_dir}"
  fi

  bootstrap_project "${target_dir}" "${cicd_slug}" "${force_graphify}"
}

main "$@"
