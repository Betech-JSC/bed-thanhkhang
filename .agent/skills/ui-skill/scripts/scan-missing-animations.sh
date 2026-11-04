#!/usr/bin/env bash
set -euo pipefail

# ==============================================================================
# UI Skill — Automated Missing Animation & Micro-Interaction Scanner
# - STRICT READ-ONLY on project source files (never modifies or deletes code)
# - Multi-Stack Support:
#     * Laravel + Inertia + Vue 3 (BED CMS: resources/Frontend/js)
#     * Laravel + Inertia + Vue 3 (Standard: resources/js)
#     * Next.js / React (src/app, src/pages, src/components, app/, components/)
#     * Nuxt / Vite Vue / React SPA (src/layouts, src/pages, src/components)
#     * Laravel Blade (resources/views)
# - Outputs detailed Markdown report with exact File:Line coordinates
# ==============================================================================

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
SKILL_DIR="$(cd "${SCRIPT_DIR}/.." && pwd)"

print_usage() {
  cat <<'EOF'
Cách dùng:
  1. Quét dự án hiện tại và xuất báo cáo vào scratch/ui-animation-audit-report.md:
       ./.agents/skills/ui-skill/scripts/scan-missing-animations.sh

  2. Quét một thư mục dự án cụ thể và tùy chỉnh đường dẫn file báo cáo:
       ./.agents/skills/ui-skill/scripts/scan-missing-animations.sh [project_dir] [--output <report_path>]
EOF
}

main() {
  local project_dir=""
  local output_path=""

  while [ "$#" -gt 0 ]; do
    case "$1" in
      -h|--help)
        print_usage
        exit 0
        ;;
      -o|--output)
        if [ "$#" -lt 2 ] || [ -z "${2:-}" ]; then
          echo "[LỖI] Thiếu đường dẫn file báo cáo sau cờ $1." >&2
          print_usage
          exit 1
        fi
        output_path="$2"
        shift 2
        ;;
      *)
        if [ -z "${project_dir}" ] && [ -d "$1" ]; then
          project_dir="$1"
          shift
        else
          echo "[LỖI] Tham số hoặc thư mục không hợp lệ: $1" >&2
          print_usage
          exit 1
        fi
        ;;
    esac
  done

  if [ -z "${project_dir}" ]; then
    project_dir="$(pwd)"
  fi
  project_dir="$(cd "${project_dir}" && pwd)"

  if [ -z "${output_path}" ]; then
    output_path="${project_dir}/scratch/ui-animation-audit-report.md"
  elif [[ "${output_path}" != /* ]]; then
    output_path="${project_dir}/${output_path}"
  fi

  mkdir -p "$(dirname "${output_path}")"

  if ! command -v python3 >/dev/null 2>&1; then
    echo "[LỖI] Cần cài đặt python3 để chạy bộ phân tích AST/UI Animation." >&2
    exit 1
  fi

  python3 - "${project_dir}" "${output_path}" <<'PYEOF'
import json
import os
import re
import sys
from collections import defaultdict
from datetime import datetime

project_dir = os.path.abspath(sys.argv[1])
output_path = os.path.abspath(sys.argv[2])


def rel_path(path):
    try:
        return os.path.relpath(path, project_dir).replace("\\", "/")
    except ValueError:
        return path


def read_text(path):
    try:
        with open(path, "r", encoding="utf-8", errors="ignore") as f:
            return f.read()
    except Exception:
        return ""


def pascal_to_kebab(name):
    s1 = re.sub(r"(.)([A-Z][a-z]+)", r"\1-\2", name)
    return re.sub(r"([a-z0-9])([A-Z])", r"\1-\2", s1).lower()


# ------------------------------------------------------------------------------
# 1. Detect Tech Stack, UI Roots, Motion Packages & Configs
# ------------------------------------------------------------------------------
pkg_json_path = os.path.join(project_dir, "package.json")
composer_json_path = os.path.join(project_dir, "composer.json")
pkg_data = {}
if os.path.isfile(pkg_json_path):
    try:
        pkg_data = json.loads(read_text(pkg_json_path))
    except Exception:
        pkg_data = {}

all_deps = {}
all_deps.update(pkg_data.get("dependencies", {}) or {})
all_deps.update(pkg_data.get("devDependencies", {}) or {})

stack_labels = []
if os.path.isfile(composer_json_path):
    comp_raw = read_text(composer_json_path)
    if "laravel/framework" in comp_raw:
        stack_labels.append("Laravel")
    if "jamstackvietnam/essentials" in comp_raw:
        stack_labels.append("BED-CMS")

for dep_key, label in [
    ("vue", "Vue 3"),
    ("@inertiajs/inertia-vue3", "Inertia.js"),
    ("@inertiajs/vue3", "Inertia.js"),
    ("next", "Next.js"),
    ("react", "React"),
    ("nuxt", "Nuxt"),
    ("tailwindcss", "TailwindCSS"),
    ("primevue", "PrimeVue"),
    ("sass", "SCSS/Sass"),
]:
    if dep_key in all_deps and label not in stack_labels:
        stack_labels.append(label)

MOTION_PACKAGES = [
    ("aos", "AOS (Animate On Scroll)", r"\b(AOS|data-aos)\b"),
    ("gsap", "GSAP", r"\b(gsap|ScrollTrigger| Tween)\b"),
    ("framer-motion", "Framer Motion", r"\b(motion\.|AnimatePresence|useAnimation|useInView)\b"),
    ("motion", "Motion One / Motion", r"\b(animate|inView|stagger)\b"),
    ("@vueuse/motion", "@vueuse/motion", r"\b(v-motion|useMotion)\b"),
    ("tailwindcss-animate", "tailwindcss-animate", r"\b(animate-in|animate-out|fade-in|zoom-in)\b"),
    ("swiper", "Swiper Carousel", r"\b(Swiper|swiper)\b"),
    ("vue3-autocounter", "vue3-autocounter", r"\b(vue3-autocounter|autocounter|AutoCounter)\b"),
    ("vue3-observe-visibility", "vue3-observe-visibility", r"\b(v-observe-visibility|ObserveVisibility)\b"),
    ("@inertiajs/progress", "@inertiajs/progress", r"InertiaProgress"),
    ("v-lazy-image", "v-lazy-image", r"v-lazy-image|VLazyImage"),
]

installed_motion_pkgs = []
for pkg_name, friendly, usage_regex in MOTION_PACKAGES:
    if pkg_name in all_deps:
        installed_motion_pkgs.append((pkg_name, str(all_deps[pkg_name]), friendly, usage_regex))

# Detect UI Root structure
ui_roots = []
ui_architecture = "Custom / Unknown"
if os.path.isdir(os.path.join(project_dir, "resources/Frontend/js")):
    ui_architecture = "Laravel + Inertia + Vue 3 (BED CMS: resources/Frontend/js)"
    ui_roots.append({
        "base": os.path.join(project_dir, "resources/Frontend/js"),
        "layouts": [os.path.join(project_dir, "resources/Frontend/js/Layouts")],
        "pages": [os.path.join(project_dir, "resources/Frontend/js/Pages")],
        "components": [os.path.join(project_dir, "resources/Frontend/js/Components")],
        "styles": [
            os.path.join(project_dir, "resources/Frontend/scss"),
            os.path.join(project_dir, "resources/Frontend/css"),
        ],
        "tailwind": [
            os.path.join(project_dir, "resources/Frontend/tailwind.config.js"),
            os.path.join(project_dir, "tailwind.config.js"),
            os.path.join(project_dir, "tailwind.config.ts"),
        ],
    })
elif os.path.isdir(os.path.join(project_dir, "resources/js")):
    ui_architecture = "Laravel + Inertia + Vue 3 / React (Standard: resources/js)"
    ui_roots.append({
        "base": os.path.join(project_dir, "resources/js"),
        "layouts": [
            os.path.join(project_dir, "resources/js/Layouts"),
            os.path.join(project_dir, "resources/js/layouts"),
        ],
        "pages": [
            os.path.join(project_dir, "resources/js/Pages"),
            os.path.join(project_dir, "resources/js/pages"),
        ],
        "components": [
            os.path.join(project_dir, "resources/js/Components"),
            os.path.join(project_dir, "resources/js/components"),
        ],
        "styles": [
            os.path.join(project_dir, "resources/css"),
            os.path.join(project_dir, "resources/scss"),
        ],
        "tailwind": [
            os.path.join(project_dir, "tailwind.config.js"),
            os.path.join(project_dir, "tailwind.config.ts"),
        ],
    })
elif os.path.isdir(os.path.join(project_dir, "src")) or os.path.isdir(os.path.join(project_dir, "app")):
    ui_architecture = "Next.js / React / Nuxt / Vite SPA (src/ & app/)"
    ui_roots.append({
        "base": os.path.join(project_dir, "src") if os.path.isdir(os.path.join(project_dir, "src")) else project_dir,
        "layouts": [
            os.path.join(project_dir, "src/layouts"),
            os.path.join(project_dir, "src/Layouts"),
            os.path.join(project_dir, "layouts"),
        ],
        "pages": [
            os.path.join(project_dir, "src/app"),
            os.path.join(project_dir, "src/pages"),
            os.path.join(project_dir, "src/views"),
            os.path.join(project_dir, "app"),
            os.path.join(project_dir, "pages"),
        ],
        "components": [
            os.path.join(project_dir, "src/components"),
            os.path.join(project_dir, "src/Components"),
            os.path.join(project_dir, "components"),
        ],
        "styles": [
            os.path.join(project_dir, "src/styles"),
            os.path.join(project_dir, "src/assets"),
            os.path.join(project_dir, "src/app"),
            os.path.join(project_dir, "styles"),
        ],
        "tailwind": [
            os.path.join(project_dir, "tailwind.config.js"),
            os.path.join(project_dir, "tailwind.config.ts"),
            os.path.join(project_dir, "tailwind.config.mjs"),
        ],
    })

if os.path.isdir(os.path.join(project_dir, "resources/views")) and not ui_roots:
    ui_architecture = "Laravel Blade (resources/views)"
    ui_roots.append({
        "base": os.path.join(project_dir, "resources/views"),
        "layouts": [os.path.join(project_dir, "resources/views/layouts"), os.path.join(project_dir, "resources/views/components/layout")],
        "pages": [os.path.join(project_dir, "resources/views/pages"), os.path.join(project_dir, "resources/views")],
        "components": [os.path.join(project_dir, "resources/views/components")],
        "styles": [os.path.join(project_dir, "resources/css"), os.path.join(project_dir, "resources/scss")],
        "tailwind": [os.path.join(project_dir, "tailwind.config.js")],
    })

UI_EXTENSIONS = (".vue", ".tsx", ".jsx", ".blade.php")
STYLE_EXTENSIONS = (".scss", ".sass", ".css")


def collect_files(dirs, exts):
    found = []
    seen = set()
    for d in dirs:
        if not os.path.isdir(d):
            continue
        for root, _, files in os.walk(d):
            if "node_modules" in root or ".git" in root or "vendor" in root:
                continue
            for fn in sorted(files):
                if any(fn.endswith(ext) for ext in exts):
                    full = os.path.abspath(os.path.join(root, fn))
                    try:
                        st = os.stat(full)
                        file_key = (st.st_dev, st.st_ino)
                    except OSError:
                        file_key = full.lower()
                    if file_key not in seen:
                        seen.add(file_key)
                        found.append(full)
    return found


layout_files = []
page_files = []
component_files = []
style_files = []
tailwind_files = []
ui_base_dirs = []

for r in ui_roots:
    ui_base_dirs.append(r["base"])
    layout_files.extend(collect_files(r["layouts"], UI_EXTENSIONS))
    page_files.extend(collect_files(r["pages"], UI_EXTENSIONS))
    component_files.extend(collect_files(r["components"], UI_EXTENSIONS))
    style_files.extend(collect_files(r["styles"], STYLE_EXTENSIONS))
    for tw in r["tailwind"]:
        if os.path.isfile(tw) and os.path.abspath(tw) not in tailwind_files:
            tailwind_files.append(os.path.abspath(tw))

# Remove any overlap if pages dir overlapped layouts/components
layout_set = set(layout_files)
component_set = set(component_files)
page_files = [p for p in page_files if p not in layout_set and p not in component_set]

# ------------------------------------------------------------------------------
# 2. Active vs Orphan Component Resolution (Graphify AST + Imports + Vue/JSX Tags)
# ------------------------------------------------------------------------------
graph_json_path = os.path.join(project_dir, "graphify-out/graph.json")
has_graphify = os.path.isfile(graph_json_path)

all_ui_files = layout_files + page_files + component_files
all_ui_js_files = list(all_ui_files)
for base_d in ui_base_dirs:
    for fn in ("app.js", "app.ts", "ssr.js", "ssr.ts", "main.js", "main.ts"):
        cand = os.path.join(base_d, fn)
        if os.path.isfile(cand):
            all_ui_js_files.append(os.path.abspath(cand))

file_contents = {f: read_text(f) for f in all_ui_js_files}

# Map component basename -> full path(s)
comp_by_name = defaultdict(list)
comp_by_kebab = defaultdict(list)
comp_by_rel_no_ext = {}

for cf in component_files:
    base = os.path.basename(cf)
    stem = re.sub(r"\.(vue|tsx|jsx|blade\.php)$", "", base)
    comp_by_name[stem].append(cf)
    comp_by_kebab[pascal_to_kebab(stem)].append(cf)
    rel_no_ext = re.sub(r"\.(vue|tsx|jsx|blade\.php)$", "", rel_path(cf))
    comp_by_rel_no_ext[rel_no_ext] = cf

# Build adjacency graph: source_file -> set of referenced component files
adj = defaultdict(set)

HTML_NATIVE_TAGS = {
    "a", "abbr", "address", "area", "article", "aside", "audio", "b", "base", "bdi", "bdo",
    "blockquote", "body", "br", "button", "canvas", "caption", "cite", "code", "col", "colgroup",
    "data", "datalist", "dd", "del", "details", "dfn", "dialog", "div", "dl", "dt", "em", "embed",
    "fieldset", "figcaption", "figure", "footer", "form", "h1", "h2", "h3", "h4", "h5", "h6",
    "head", "header", "hgroup", "hr", "html", "i", "iframe", "img", "input", "ins", "kbd", "label",
    "legend", "li", "link", "main", "map", "mark", "menu", "meta", "meter", "nav", "noscript",
    "object", "ol", "optgroup", "option", "output", "p", "param", "picture", "pre", "progress",
    "q", "rp", "rt", "ruby", "s", "samp", "script", "search", "section", "select", "slot", "small",
    "source", "span", "strong", "style", "sub", "summary", "sup", "svg", "table", "tbody", "td",
    "template", "textarea", "tfoot", "th", "thead", "time", "title", "tr", "track", "u", "ul",
    "var", "video", "wbr",
}

for src_f, content in file_contents.items():
    for cf in component_files:
        if cf == src_f:
            continue
        stem = re.sub(r"\.(vue|tsx|jsx|blade\.php)$", "", os.path.basename(cf))
        kebab = pascal_to_kebab(stem)
        # Compute relative path inside Components/ (e.g. "Card/Product" or "V4V/V4VHeader")
        comp_subpath = ""
        for r in ui_roots:
            for cdir in r["components"]:
                if cf.startswith(os.path.abspath(cdir) + os.sep):
                    comp_subpath = os.path.relpath(cf, os.path.abspath(cdir)).replace("\\", "/")
                    comp_subpath = re.sub(r"\.(vue|tsx|jsx|blade\.php)$", "", comp_subpath)

        matched = False
        # a) Explicit import path match (e.g. Components/Card/Product or ./Product)
        if comp_subpath and (f"Components/{comp_subpath}" in content or f"components/{comp_subpath}" in content):
            matched = True
        elif os.path.dirname(src_f) == os.path.dirname(cf) and (
            f"./{stem}'" in content or f'./{stem}"' in content or f"./{stem}.vue" in content
        ):
            matched = True
        # b) Explicit ES import identifier when stem is unique or path matches stem
        elif len(comp_by_name[stem]) == 1 and re.search(rf"\bimport\s+.*\b{re.escape(stem)}\b", content):
            matched = True
        # c) Template tag usage (<PascalCase or <kebab-case with hyphen, avoiding native HTML tags)
        elif len(comp_by_name[stem]) == 1:
            if re.search(rf"<\s*{re.escape(stem)}\b", content):
                matched = True
            elif "-" in kebab and kebab not in HTML_NATIVE_TAGS and re.search(rf"<\s*{re.escape(kebab)}\b", content):
                matched = True

        if matched:
            adj[src_f].add(cf)

# Also check Graphify AST directed links if available
if has_graphify:
    try:
        gdata = json.loads(read_text(graph_json_path))
        node_to_file = {}
        for n in gdata.get("nodes", []):
            sf = n.get("source_file")
            nid = n.get("id")
            if sf and nid:
                node_to_file[nid] = os.path.abspath(os.path.join(project_dir, sf))
        edges = gdata.get("links") or gdata.get("edges") or []
        for e in edges:
            u = node_to_file.get(e.get("source"))
            v = node_to_file.get(e.get("target"))
            if u and v and u != v:
                adj[u].add(v)
    except Exception:
        pass

# BFS from entry points (Layouts, Pages, app.js/ssr.js)
active_files = set(layout_files + page_files)
for base_d in ui_base_dirs:
    for fn in ("app.js", "app.ts", "ssr.js", "ssr.ts", "main.js", "main.ts"):
        cand = os.path.join(base_d, fn)
        if os.path.isfile(cand):
            active_files.add(os.path.abspath(cand))

queue = list(active_files)
while queue:
    curr = queue.pop(0)
    for nxt in adj.get(curr, ()):
        if nxt not in active_files:
            active_files.add(nxt)
            queue.append(nxt)

active_component_files = [cf for cf in component_files if cf in active_files]
orphan_component_files = [cf for cf in component_files if cf not in active_files]

# If no pages/layouts were detected (e.g. component-only library), treat all components as active
if not layout_files and not page_files and component_files:
    active_component_files = list(component_files)
    orphan_component_files = []

# ------------------------------------------------------------------------------
# 3. Analyze Global Motion Foundation (Tailwind + SCSS/CSS + Motion Libraries)
# ------------------------------------------------------------------------------
global_findings = []
global_features = {
    "tailwind_keyframes": False,
    "tailwind_animation": False,
    "tailwind_transition": False,
    "css_keyframes_count": 0,
    "css_transitions_count": 0,
    "prefers_reduced_motion": False,
    "reduced_motion_locations": [],
    "keyframes_locations": [],
}

for tw_f in tailwind_files:
    tw_txt = read_text(tw_f)
    if re.search(r"\bkeyframes\s*:", tw_txt):
        global_features["tailwind_keyframes"] = True
    if re.search(r"\banimation\s*:", tw_txt):
        global_features["tailwind_animation"] = True
    if re.search(r"\b(transitionDuration|transitionTimingFunction|transitionProperty)\s*:", tw_txt):
        global_features["tailwind_transition"] = True

for sf in style_files + all_ui_files:
    txt = read_text(sf)
    lines = txt.splitlines()
    for idx, line in enumerate(lines, 1):
        if "@keyframes" in line:
            global_features["css_keyframes_count"] += 1
            if len(global_features["keyframes_locations"]) < 10:
                global_features["keyframes_locations"].append(f"{rel_path(sf)}:{idx}")
        if "prefers-reduced-motion" in line:
            global_features["prefers_reduced_motion"] = True
            global_features["reduced_motion_locations"].append(f"{rel_path(sf)}:{idx}")
        if sf in style_files and re.search(r"\btransition(-property|-duration|-timing-function)?\s*:", line):
            global_features["css_transitions_count"] += 1

# Check motion package usage across active files
active_corpus = "\n".join(read_text(f) for f in (layout_files + page_files + active_component_files + style_files))
for base_d in ui_base_dirs:
    for fn in ("app.js", "app.ts", "ssr.js", "ssr.ts"):
        p = os.path.join(base_d, fn)
        if os.path.isfile(p):
            active_corpus += "\n" + read_text(p)

pkg_usage_summary = []
for pkg_name, pkg_ver, friendly, usage_regex in installed_motion_pkgs:
    matches = re.findall(usage_regex, active_corpus)
    used_count = len(matches)
    pkg_usage_summary.append({
        "name": pkg_name,
        "version": pkg_ver,
        "friendly": friendly,
        "used_count": used_count,
    })

# ------------------------------------------------------------------------------
# 4. Detailed File-by-File & Line-by-Line Animation Audit
# ------------------------------------------------------------------------------
RE_VUE_TRANSITION = re.compile(r"<\s*(Transition|TransitionGroup|AnimatePresence|motion\.[a-zA-Z0-9_]+)\b")
RE_DIRECTIVE_MOTION = re.compile(r"\b(v-motion|data-aos|v-observe-visibility|gsap\.|ScrollTrigger|IntersectionObserver)\b")
RE_TW_TRANSITION = re.compile(r"\b(transition(?:-[a-z]+)?|duration-\d+|ease-(?:in|out|in-out|linear)|animate-[a-z0-9_-]+)\b")
RE_TW_HOVER_MOTION = re.compile(
    r"\b(hover:(?:scale-|translate-|-translate-|shadow-|bg-|text-|border-|opacity-| brightness-)|"
    r"group-hover:(?:scale-|translate-|-translate-|opacity-|text-|bg-)|"
    r"active:scale-|focus-visible:ring|focus:ring)\b"
)
RE_CSS_INLINE_MOTION = re.compile(r"\b(transition\s*:|animation\s*:|@keyframes|transform\s*:)\b")

OVERLAY_STATE_KEYWORDS = re.compile(
    r"(\bopen|\bisOpen|\bshow|\bisShow|\bvisible|\bisVisible|\bactive|\bexpanded|\bmodal|\bpopup|\bdrawer|\bdropdown|\bmenu|\bsubmenu|\baccordion|\bcollapse|\btab|\btoast|\balert|\bfilter|\bsearchOpen|\bcartOpen|\bloading|\bsubmitting|\bcopied|\bsuccess|\berror)",
    re.IGNORECASE,
)
STATIC_NON_ANIM_TAGS = re.compile(r"<\s*(meta|title|Head|link|script|style|defs|path|circle|rect|g)\b", re.IGNORECASE)

findings = []  # List of dicts: priority, group, file_line, component, element, issue, suggestion


def add_finding(priority, group, fpath, line_no, element, issue, suggestion):
    findings.append({
        "priority": priority,
        "group": group,
        "coords": f"{rel_path(fpath)}:{line_no}",
        "file": rel_path(fpath),
        "line": line_no,
        "element": element,
        "issue": issue,
        "suggestion": suggestion,
    })


# Global findings (Group 1)
if not global_features["prefers_reduced_motion"]:
    if style_files:
        target_style = rel_path(style_files[0])
    elif "Next.js" in ui_architecture or "React" in ui_architecture:
        target_style = "src/app/globals.css"
    else:
        target_style = "resources/Frontend/scss/app.scss"
    add_finding(
        "P0",
        "Nhóm 1: Global Motion & A11y",
        os.path.join(project_dir, target_style),
        1,
        "Global CSS `@media (prefers-reduced-motion: reduce)`",
        "Chưa có rule bảo vệ người dùng nhạy cảm chuyển động (`prefers-reduced-motion: reduce`) trên toàn hệ thống",
        "Bổ sung `@media (prefers-reduced-motion: reduce)` tắt/giảm `animation-duration` & `transition-duration` trong global stylesheet",
    )

if tailwind_files and not (global_features["tailwind_keyframes"] or global_features["tailwind_animation"]):
    add_finding(
        "P2",
        "Nhóm 1: Global Motion & Tokens",
        tailwind_files[0],
        1,
        "Tailwind Motion Tokens (`keyframes` / `animation`)",
        "Cấu hình `tailwind.config` chưa định nghĩa bộ Motion Tokens (`keyframes`, `animation`, `transitionTimingFunction`) dùng chung",
        "Định nghĩa `keyframes` (`fade-up`, `fade-in`, `scale-in`) & `animation` chuẩn trong `tailwind.config` để đồng bộ toàn web",
    )

for pkg_info in pkg_usage_summary:
    if pkg_info["used_count"] == 0 and pkg_info["name"] in ("aos", "gsap", "vue3-observe-visibility", "vue3-autocounter"):
        add_finding(
            "P2",
            "Nhóm 1: Global Motion & Tokens",
            pkg_json_path,
            1,
            f"Dependency `{pkg_info['name']}` ({pkg_info['version']})",
            f"Thư viện `{pkg_info['friendly']}` đã cài trong `package.json` nhưng có `0` lượt kích hoạt trong UI đang chạy",
            f"Tận dụng `{pkg_info['name']}` cho hiệu ứng Scroll-Reveal / Counter hoặc gỡ bỏ nếu chuyển sang CSS/Tailwind thuần",
        )


def analyze_ui_file(fpath, layer_type):
    txt = read_text(fpath)
    lines = txt.splitlines()
    rel = rel_path(fpath)
    fname = os.path.basename(fpath)

    has_vue_transition = bool(RE_VUE_TRANSITION.search(txt))
    has_directive_motion = bool(RE_DIRECTIVE_MOTION.search(txt))
    tw_transitions = RE_TW_TRANSITION.findall(txt)
    tw_hovers = RE_TW_HOVER_MOTION.findall(txt)
    css_motions = RE_CSS_INLINE_MOTION.findall(txt)

    # Track line ranges inside <Transition ...> ... </Transition>
    in_transition_depth = 0
    transition_lines = set()
    for i, ln in enumerate(lines, 1):
        if re.search(r"<\s*(Transition|TransitionGroup|AnimatePresence)\b", ln):
            in_transition_depth += 1
        if in_transition_depth > 0:
            transition_lines.add(i)
        if re.search(r"</\s*(Transition|TransitionGroup|AnimatePresence)\s*>", ln):
            in_transition_depth = max(0, in_transition_depth - 1)

    abrupt_toggles = []
    buttons_without_transition = []
    cards_without_motion = []
    inputs_without_transition = []
    submit_without_loading = []

    for i, ln in enumerate(lines, 1):
        stripped = ln.strip()

        # 1) Check v-if / v-show (Vue) or {cond && <div...} (React/JSX) on overlay/modal/dropdown/tab/accordion/menu without <Transition>/<AnimatePresence>
        vue_cond_match = re.search(r"\b(v-if|v-show)\s*=\s*['\"]([^'\"]+)['\"]", stripped)
        jsx_cond_match = re.search(r"\{\s*([a-zA-Z0-9_?.!]+)\s*&&\s*(?:<|\()", stripped) if fpath.endswith((".tsx", ".jsx")) else None
        if vue_cond_match or jsx_cond_match:
            if vue_cond_match:
                directive_name = vue_cond_match.group(1)
                cond_expr = vue_cond_match.group(2)
            else:
                directive_name = "JSX &&"
                cond_expr = jsx_cond_match.group(1)
            ctx_window = "\n".join(lines[max(0, i - 6): min(len(lines), i + 6)])
            elem_window = "\n".join(lines[max(0, i - 1): min(len(lines), i + 3)])
            if not STATIC_NON_ANIM_TAGS.search(stripped):
                is_overlay_like = (
                    bool(OVERLAY_STATE_KEYWORDS.search(cond_expr))
                    or bool(re.search(r"\b(fixed|inset-0|z-40|z-50|backdrop|dropdown-menu|modal|drawer|popup)\b", elem_window))
                )
                # Exclude simple static prop existence checks like showEmblem, showSeal, showHeader
                if re.match(r"^!?(show[A-Z]\w+|props\.show[A-Z]\w+|item\.\w+|card\.\w+|post\?\.\w+|label|title|subtitle)$", cond_expr.strip()):
                    is_overlay_like = False
                has_local_anim = (
                    (i in transition_lines)
                    or bool(RE_VUE_TRANSITION.search(ctx_window))
                    or bool(re.search(r"\b(animate-|transition-all|transition-transform|transition-opacity|isTransitioning)\b", ctx_window))
                )
                if is_overlay_like and not has_local_anim:
                    abrupt_toggles.append((i, directive_name, cond_expr, stripped[:90]))

        # 2) Check interactive <button>, <Link>, <a> with class="..." or className="..." but missing transition-*
        if re.search(r"<\s*(button|Link|a| inertia-link)\b", stripped):
            tag_match = re.search(r"<\s*(button|Link|a|inertia-link)\b", stripped)
            tag_name = tag_match.group(1)
            # Read multi-line opening tag (up to 6 lines)
            tag_block = " ".join(l.strip() for l in lines[i - 1: min(len(lines), i + 5)])
            end_idx = tag_block.find(">")
            if end_idx != -1:
                tag_open = tag_block[: end_idx + 1]
            else:
                tag_open = tag_block

            has_class = ("class=" in tag_open) or ("className=" in tag_open)
            has_trans = bool(re.search(r"\b(transition|duration-|animate-|btn-|button-)\b", tag_open))
            has_hover = bool(re.search(r"\b(hover:|group-hover:|active:|focus:)\b", tag_open))

            # Check submit button without loading/disabled feedback
            if tag_name == "button" and ('type="submit"' in tag_open or "type='submit'" in tag_open or layer_type == "page"):
                if ("type=\"submit\"" in tag_open or "type='submit'" in tag_open) and not re.search(r"(:disabled|disabled|processing|loading)", tag_open):
                    submit_without_loading.append((i, tag_open[:85]))

            if has_class and not has_trans:
                buttons_without_transition.append((i, tag_name, has_hover, tag_open[:85]))

        # 3) Check Form <input>, <textarea>, <select> with class="..." or className="..." but missing transition
        if re.search(r"<\s*(input|textarea|select)\b", stripped):
            tag_block = " ".join(l.strip() for l in lines[i - 1: min(len(lines), i + 4)])
            if ("class=" in tag_block or "className=" in tag_block) and 'type="hidden"' not in tag_block:
                if not re.search(r"\b(transition|duration-|form-input|input-)\b", tag_block):
                    inputs_without_transition.append((i, stripped[:80]))

    # Determine if file is a Card component or contains card blocks without hover motion
    is_card_component = "card" in fname.lower() or "item" in fname.lower()
    if is_card_component and layer_type == "active_component":
        has_card_hover = bool(re.search(r"\b(group-hover:scale-|hover:-translate-y-|hover:shadow-|transition-transform|transition-all)\b", txt))
        if not has_card_hover:
            cards_without_motion.append((1, fname))

    # Calculate coverage status & score (0..100) for this file
    total_motion_signals = (
        (3 if has_vue_transition else 0)
        + (3 if has_directive_motion else 0)
        + min(4, len(tw_transitions))
        + min(4, len(tw_hovers))
        + min(2, len(css_motions))
    )

    issue_penalty = (
        len(abrupt_toggles) * 25
        + min(3, len(buttons_without_transition)) * 12
        + len(cards_without_motion) * 25
        + len(submit_without_loading) * 15
    )

    if total_motion_signals == 0:
        coverage_pct = 0
        status = "STATIC (0%)"
    else:
        raw_score = min(100, 45 + total_motion_signals * 8) - issue_penalty
        coverage_pct = max(15, min(100, raw_score))
        if coverage_pct >= 80 and len(abrupt_toggles) == 0:
            status = "ANIMATED"
        else:
            status = "PARTIAL"

    # Register specific findings for active files
    if layer_type in ("layout", "page", "active_component"):
        # Abrupt v-if / v-show
        for ln_no, d_name, cond_expr, snippet in abrupt_toggles[:3]:
            prio = "P0" if layer_type in ("layout", "active_component") or any(k in cond_expr.lower() for k in ("modal", "menu", "drawer", "open", "show", "popup", "cart", "search", "dropdown")) else "P1"
            add_finding(
                prio,
                "Nhóm 5: Overlays / Modals / Drawers / Tabs",
                fpath,
                ln_no,
                f"`{d_name}=\"{cond_expr}\"` trong `{fname}`",
                f"Khối UI đóng/mở trực tiếp bằng `{d_name}` không bọc `<Transition>` hoặc animation class -> bật/tắt đột ngột (0ms)",
                "Bọc trong `<Transition enter-active-class=\"transition duration-200 ease-out\" enter-from-class=\"opacity-0 translate-y-1 scale-95\" ...>`",
            )

        # Submit button without loading state
        for ln_no, snippet in submit_without_loading[:2]:
            add_finding(
                "P0",
                "Nhóm 6: Async Feedback & Submit State",
                fpath,
                ln_no,
                f"Nút `<button type=\"submit\">` trong `{fname}`",
                "Nút gửi form chưa có hiệu ứng chuyển trạng thái `:disabled` / loading spinner khi đang xử lý bất đồng bộ",
                "Thêm `:disabled=\"processing\"`, `transition-all duration-200 active:scale-[0.98]` và icon spinner `animate-spin` khi đang gửi",
            )

        # Card without hover motion
        for ln_no, card_name in cards_without_motion:
            add_finding(
                "P1",
                "Nhóm 4: Interactive Micro-Interactions (Cards)",
                fpath,
                ln_no,
                f"Component Card `{card_name}`",
                "Card sản phẩm/bài viết chưa có hiệu ứng nâng khung (`hover:-translate-y-1 hover:shadow-md`) hoặc phóng nhẹ ảnh (`group-hover:scale-105`)",
                "Thêm `group transition-all duration-300 ease-out hover:-translate-y-1 hover:shadow-lg` ở khung card và `transition-transform duration-500 group-hover:scale-105` ở ảnh",
            )

        # Interactive buttons/links without transition
        for ln_no, tag_name, has_hover, snippet in buttons_without_transition[:2]:
            if has_hover:
                issue_desc = f"Thẻ `<{tag_name}>` có class `hover:*` nhưng thiếu `transition-*` / `duration-*` làm đổi màu/trạng thái bị gắt"
            else:
                issue_desc = f"Thẻ tương tác `<{tag_name}>` tĩnh hoàn toàn, thiếu cả `transition-*` lẫn phản hồi `hover:*` / `active:*`"
            add_finding(
                "P1",
                "Nhóm 4: Interactive Micro-Interactions (Buttons/Links)",
                fpath,
                ln_no,
                f"`<{tag_name}>` trong `{fname}`",
                issue_desc,
                "Bổ sung `transition-all duration-200 ease-out hover:opacity-90 active:scale-[0.98]` (hoặc `transition-colors duration-200`)",
            )

        # Form inputs without focus transition
        if inputs_without_transition:
            ln_no, snippet = inputs_without_transition[0]
            add_finding(
                "P2",
                "Nhóm 4: Interactive Micro-Interactions (Form Controls)",
                fpath,
                ln_no,
                f"Form Control (`<input>`/`<textarea>`/`<select>`) trong `{fname}`",
                "Ô nhập liệu thay đổi viền focus tức thời, thiếu `transition-colors duration-150`",
                "Thêm `transition-colors duration-150 ease-out focus:border-primary focus:ring-2 focus:ring-primary/20`",
            )

        # Page or Section without entrance / scroll-reveal animation
        if layer_type == "page" and not has_vue_transition and not has_directive_motion and not any("animate-" in t for t in tw_transitions):
            # Find first <section or <main or <template line
            sec_line = 1
            for idx, ln in enumerate(lines, 1):
                if re.search(r"<\s*(section|main|div)\b", ln):
                    sec_line = idx
                    break
            add_finding(
                "P2",
                "Nhóm 3: Section Entrance & Scroll-Reveal",
                fpath,
                sec_line,
                f"Trang `{rel}`",
                "Trang chưa có hiệu ứng Section Entrance / Scroll-Reveal (các khối Hero, danh sách, nội dung xuất hiện tĩnh 100% khi cuộn)",
                "Bổ sung Scroll-Reveal tinh tế (`fade-up` `duration-500` stagger `80ms` qua `IntersectionObserver` / `v-observe-visibility` / `data-aos`) tôn trọng `prefers-reduced-motion`",
            )
        elif layer_type == "active_component" and ("section" in rel.lower() or "banner" in rel.lower() or "hero" in rel.lower()):
            if total_motion_signals == 0:
                add_finding(
                    "P2",
                    "Nhóm 3: Section Entrance & Scroll-Reveal",
                    fpath,
                    1,
                    f"Section/Banner `{rel}`",
                    "Component Section/Banner hiển thị tĩnh hoàn toàn (0 transition, 0 animation, 0 hover feedback)",
                    "Bổ sung hiệu ứng entrance nhẹ cho tiêu đề/CTA và micro-interaction cho nút hành động",
                )
        elif layer_type == "layout" and total_motion_signals == 0:
            add_finding(
                "P1",
                "Nhóm 2: Layout Shell & Page Transitions",
                fpath,
                1,
                f"Layout `{rel}`",
                "Layout khung chính chưa có hiệu ứng chuyển trang (`<Transition>`) hoặc transition cho Header/Navigation",
                "Bổ sung Page Transition nhẹ (`fade` 200ms) quanh `<slot />` và transition cho Sticky Header / Mobile Drawer",
            )

    return {
        "file": rel,
        "name": fname,
        "layer": layer_type,
        "has_vue_transition": has_vue_transition,
        "has_directive_motion": has_directive_motion,
        "tw_transitions_count": len(tw_transitions),
        "tw_hovers_count": len(tw_hovers),
        "abrupt_toggles_count": len(abrupt_toggles),
        "buttons_missing_count": len(buttons_without_transition),
        "coverage_pct": coverage_pct,
        "status": status,
    }


layout_results = [analyze_ui_file(f, "layout") for f in layout_files]
page_results = [analyze_ui_file(f, "page") for f in page_files]
active_comp_results = [analyze_ui_file(f, "active_component") for f in active_component_files]
orphan_comp_results = [analyze_ui_file(f, "orphan_component") for f in orphan_component_files]


def summarize_layer(results):
    total = len(results)
    if total == 0:
        return {"total": 0, "animated": 0, "partial": 0, "static": 0, "avg_pct": 0, "rating": "N/A"}
    animated = sum(1 for r in results if r["status"] == "ANIMATED")
    partial = sum(1 for r in results if r["status"] == "PARTIAL")
    static = sum(1 for r in results if r["status"].startswith("STATIC"))
    avg_pct = round(sum(r["coverage_pct"] for r in results) / total)
    if avg_pct >= 75:
        rating = "GOOD"
    elif avg_pct >= 40:
        rating = "PARTIAL"
    else:
        rating = "MISSING / LOW"
    return {
        "total": total,
        "animated": animated,
        "partial": partial,
        "static": static,
        "avg_pct": avg_pct,
        "rating": rating,
    }


sum_layouts = summarize_layer(layout_results)
sum_pages = summarize_layer(page_results)
sum_active_comps = summarize_layer(active_comp_results)
sum_orphan_comps = summarize_layer(orphan_comp_results)

# Global foundation score
global_checks_total = 4
global_checks_passed = (
    (1 if (global_features["tailwind_keyframes"] or global_features["tailwind_animation"]) else 0)
    + (1 if global_features["css_keyframes_count"] > 0 else 0)
    + (1 if global_features["css_transitions_count"] > 0 else 0)
    + (1 if global_features["prefers_reduced_motion"] else 0)
)
global_pct = round((global_checks_passed / global_checks_total) * 100)
global_rating = "GOOD" if global_pct >= 75 else ("PARTIAL" if global_pct >= 50 else "MISSING / LOW")

all_active_results = layout_results + page_results + active_comp_results
overall_pct = round(sum(r["coverage_pct"] for r in all_active_results) / len(all_active_results)) if all_active_results else 0

# Sort findings by priority (P0 -> P1 -> P2) then file
prio_order = {"P0": 0, "P1": 1, "P2": 2}
findings.sort(key=lambda x: (prio_order.get(x["priority"], 9), x["file"], x["line"]))

p0_count = sum(1 for f in findings if f["priority"] == "P0")
p1_count = sum(1 for f in findings if f["priority"] == "P1")
p2_count = sum(1 for f in findings if f["priority"] == "P2")

# ------------------------------------------------------------------------------
# 5. Generate Detailed Markdown Report
# ------------------------------------------------------------------------------
now_str = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
stack_str = ", ".join(stack_labels) if stack_labels else "Custom Web Stack"

md = []
md.append("# Báo Cáo Kiểm Toán Độ Phủ Animation & Micro-Interaction Toàn Hệ Thống (`/ui-skill`)")
md.append("")
md.append(f"- **Thư mục dự án**: `{project_dir}`")
md.append(f"- **Tech Stack nhận diện**: `{stack_str}`")
md.append(f"- **Kiến trúc UI**: `{ui_architecture}`")
md.append(f"- **Đồ thị AST Graphify (`graphify-out/graph.json`)**: `{'ĐÃ KẾT NỐI' if has_graphify else 'CHƯA CÓ (Dùng bộ phân tích Import/Tag nội tuyến)'}`")
md.append(f"- **Thời điểm quét**: `{now_str}`")
md.append(f"- **Chế độ thực thi**: `STRICT READ-ONLY (100% không chỉnh sửa mã nguồn)`")
md.append("")
md.append("---")
md.append("")
md.append("## 1. Bảng 1.1 — Tổng Quan Tỷ Lệ % Độ Phủ Animation Theo Từng Tầng Giao Diện")
md.append("")
md.append("| Tầng Giao Diện (UI Layer) | Tổng số File (`Active`) | Đạt chuẩn (`Animated`) | Có một phần (`Partial`) | Chưa có Animation (`Static 0%`) | Độ phủ trung bình (`%`) | Đánh giá |")
md.append("| :--- | :---: | :---: | :---: | :---: | :---: | :---: |")
md.append(f"| **1. Global Motion & Tokens** (`tailwind.config`, `scss/`) | `{len(tailwind_files) + len(style_files)}` | `{global_checks_passed}/4 tiêu chí` | `-` | `{4 - global_checks_passed}/4 tiêu chí` | **`{global_pct}%`** | `{global_rating}` |")
md.append(f"| **2. Layout Shell** (`Layouts/`) | `{sum_layouts['total']}` | `{sum_layouts['animated']}` | `{sum_layouts['partial']}` | `{sum_layouts['static']}` | **`{sum_layouts['avg_pct']}%`** | `{sum_layouts['rating']}` |")
md.append(f"| **3. Feature Pages** (`Pages/`) | `{sum_pages['total']}` | `{sum_pages['animated']}` | `{sum_pages['partial']}` | `{sum_pages['static']}` | **`{sum_pages['avg_pct']}%`** | `{sum_pages['rating']}` |")
md.append(f"| **4. Active UI Components** (`Components/` đang dùng) | `{sum_active_comps['total']}` | `{sum_active_comps['animated']}` | `{sum_active_comps['partial']}` | `{sum_active_comps['static']}` | **`{sum_active_comps['avg_pct']}%`** | `{sum_active_comps['rating']}` |")
md.append(f"| **Tổng hợp toàn bộ UI đang chạy (`Active UI`)** | **`{len(all_active_results)}`** | **`{sum_layouts['animated'] + sum_pages['animated'] + sum_active_comps['animated']}`** | **`{sum_layouts['partial'] + sum_pages['partial'] + sum_active_comps['partial']}`** | **`{sum_layouts['static'] + sum_pages['static'] + sum_active_comps['static']}`** | **`{overall_pct}%`** | **`{'GOOD' if overall_pct >= 75 else ('PARTIAL' if overall_pct >= 40 else 'MISSING / LOW')}`** |")
md.append(f"| *5. Orphan Components (Không được import/sử dụng)* | *`{sum_orphan_comps['total']}`* | *`{sum_orphan_comps['animated']}`* | *`{sum_orphan_comps['partial']}`* | *`{sum_orphan_comps['static']}`* | *`N/A`* | *Tách riêng để không tốn công làm animation thừa* |")
md.append("")

md.append("### Kiểm tra Hạ tầng Motion & Thư viện đã cài đặt (Nhóm 1)")
md.append("")
md.append(f"- **Rule bảo vệ trợ năng `@media (prefers-reduced-motion: reduce)`**: **{'ĐÃ CÓ (' + ', '.join(global_features['reduced_motion_locations']) + ')' if global_features['prefers_reduced_motion'] else 'CHƯA CÓ (Cần bổ sung ngay - P0)'}**")
md.append(f"- **Tailwind Custom Keyframes / Animation Tokens**: **{'ĐÃ CÓ' if (global_features['tailwind_keyframes'] or global_features['tailwind_animation']) else 'CHƯA ĐỊNH NGHĨA TRONG tailwind.config'}**")
md.append(f"- **Số lượng `@keyframes` trong SCSS/CSS**: `{global_features['css_keyframes_count']}` ({', '.join(global_features['keyframes_locations'][:6]) if global_features['keyframes_locations'] else 'Không có'})")
md.append(f"- **Số lượng thuộc tính `transition` trong SCSS/CSS**: `{global_features['css_transitions_count']}`")
if pkg_usage_summary:
    md.append("- **Thư viện liên quan đến Motion/UI trong `package.json`**:")
    for p in pkg_usage_summary:
        status_pkg = f"Đang sử dụng (`{p['used_count']}` vị trí)" if p["used_count"] > 0 else "**CHƯA SỬ DỤNG (`0` vị trí trong Active UI)**"
        md.append(f"  - `{p['name']}` (`{p['version']}`) — {p['friendly']}: {status_pkg}")
md.append("")
md.append("---")
md.append("")

md.append("## 2. Bảng 1.2 — Trạng Thái Độ Phủ Animation Chi Tiết Từng Trang & Layout (`Active`)")
md.append("")
md.append("| # | Tầng | Đường dẫn File | `<Transition>` / Motion Dir | Tailwind `transition-*` / `animate-*` | Hover / Active Micro-FX | Khối `v-if`/`v-show` giật cục | Nút/Link thiếu `transition` | Độ phủ (`%`) | Trạng thái |")
md.append("| :---: | :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |")
for idx, r in enumerate(layout_results + page_results, 1):
    layer_lbl = "Layout" if r["layer"] == "layout" else "Page"
    has_tr = "Có" if (r["has_vue_transition"] or r["has_directive_motion"]) else "**Không**"
    md.append(
        f"| {idx} | `{layer_lbl}` | `{r['file']}` | {has_tr} | `{r['tw_transitions_count']}` | `{r['tw_hovers_count']}` | `{r['abrupt_toggles_count']}` | `{r['buttons_missing_count']}` | **`{r['coverage_pct']}%`** | `{r['status']}` |"
    )
md.append("")

md.append("### Các Active Components có độ phủ Animation thấp hoặc tĩnh hoàn toàn (`Static / Partial`)")
md.append("")
md.append("| # | Đường dẫn Component (`Active`) | `<Transition>` / Motion Dir | Tailwind `transition-*` | Hover / Active Micro-FX | Khối `v-if`/`v-show` giật cục | Nút/Link thiếu `transition` | Độ phủ (`%`) | Trạng thái |")
md.append("| :---: | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |")
low_active_comps = sorted(active_comp_results, key=lambda x: (x["coverage_pct"], -x["abrupt_toggles_count"], -x["buttons_missing_count"]))
for idx, r in enumerate(low_active_comps, 1):
    has_tr = "Có" if (r["has_vue_transition"] or r["has_directive_motion"]) else "**Không**"
    md.append(
        f"| {idx} | `{r['file']}` | {has_tr} | `{r['tw_transitions_count']}` | `{r['tw_hovers_count']}` | `{r['abrupt_toggles_count']}` | `{r['buttons_missing_count']}` | **`{r['coverage_pct']}%`** | `{r['status']}` |"
    )
md.append("")
md.append("---")
md.append("")

md.append(f"## 3. Bảng 1.3 — Danh Sách Tọa Độ Chi Tiết Các Thành Phần CHƯA CÓ Animation (`{len(findings)}` phát hiện: `{p0_count} P0`, `{p1_count} P1`, `{p2_count} P2`)")
md.append("")
md.append("| # | Ưu tiên | Nhóm Kiểm Toán | Tọa độ `File:Dòng` | Thành phần / Phần tử UI | Hiện trạng thiếu Animation (`Static / Abrupt`) | Giải pháp bổ sung đề xuất (`Quiet & High-Trust Motion`) |")
md.append("| :---: | :---: | :--- | :--- | :--- | :--- | :--- |")
for idx, f in enumerate(findings, 1):
    md.append(
        f"| {idx} | **`{f['priority']}`** | {f['group']} | `{f['coords']}` | {f['element']} | {f['issue']} | {f['suggestion']} |"
    )
md.append("")
md.append("---")
md.append("")

md.append("## 4. Bảng 1.4 — Danh Sách Orphan Components (Không Được Import Trong Luồng Chạy — Tách Riêng)")
md.append("")
if orphan_comp_results:
    md.append(f"Phát hiện **{len(orphan_comp_results)}** component trong `Components/` không có liên kết import hoặc thẻ gọi từ các `Layouts/` & `Pages/` đang hoạt động. Đội ngũ **không cần tốn công viết animation** cho các file này trước khi xác nhận có tái sử dụng hay không:")
    md.append("")
    for r in orphan_comp_results:
        md.append(f"- `{r['file']}` *(Trạng thái hiện tại: `{r['status']}`)*")
else:
    md.append("- Toàn bộ Components đều đang được liên kết sử dụng (`0` Orphan Components).")
md.append("")
md.append("---")
md.append("")

md.append("## 5. Bảng 1.5 — Đề Xuất Lộ Trình OpenSpec (`/opsx-propose`) Để Bổ Sung Animation Theo Phase")
md.append("")
md.append("| Phase | Tên gói OpenSpec đề xuất | Hạng mục xử lý trọng tâm | Mức ưu tiên |")
md.append("| :---: | :--- | :--- | :---: |")
md.append("| **Phase 1** | `add-global-motion-tokens-and-shell-transitions` | 1) Bổ sung `@media (prefers-reduced-motion: reduce)` trong global SCSS/CSS.<br>2) Định nghĩa `keyframes` & `animation` chuẩn (`fade-up`, `fade-in`, `scale-in`) trong `tailwind.config`.<br>3) Bọc `<Transition>` cho toàn bộ Modal, Popup, Mobile Menu Drawer, Search Overlay, Dropdown & Accordion (`P0`). | **`P0`** |")
md.append("| **Phase 2** | `enhance-cards-buttons-micro-interactions` | 1) Chuẩn hóa hiệu ứng Hover/Active/Focus (`transition-all duration-200 ease-out active:scale-[0.98]`) cho toàn bộ Buttons, CTA Links & Form Controls.<br>2) Bổ sung hiệu ứng nâng khung & zoom ảnh mượt (`group-hover:scale-105 duration-500`) cho Product Cards, Post Cards, Category Cards.<br>3) Bổ sung trạng thái loading spinner mượt cho các nút Submit form (`P0`/`P1`). | **`P1`** |")
md.append("| **Phase 3** | `add-page-sections-scroll-reveal` | 1) Bổ sung hiệu ứng Section Entrance / Scroll-Reveal tinh tế (`fade-up`, stagger `60ms-100ms`) cho các `Pages/` đang tĩnh.<br>2) Bổ sung Skeleton shimmer (`animate-pulse`) cho các khối tải dữ liệu/ảnh trễ (`P2`). | **`P2`** |")
md.append("")

with open(output_path, "w", encoding="utf-8") as out_f:
    out_f.write("\n".join(md) + "\n")

# Print concise summary to terminal
print("==========================================================================")
print("UI SKILL — BÁO CÁO KIỂM TOÁN ĐỘ PHỦ ANIMATION & MICRO-INTERACTION")
print("==========================================================================")
print(f"Dự án           : {project_dir}")
print(f"Tech Stack      : {stack_str}")
print(f"Kiến trúc UI    : {ui_architecture}")
print(f"Graphify AST    : {'Đã kết nối' if has_graphify else 'Phân tích Import/Tag trực tiếp'}")
print("--------------------------------------------------------------------------")
print("TỔNG QUAN ĐỘ PHỦ ANIMATION THEO TẦNG GIAO DIỆN:")
print(f"  1. Global Motion & Tokens : {global_pct:3d}% ({global_checks_passed}/4 tiêu chí) | prefers-reduced-motion: {'CÓ' if global_features['prefers_reduced_motion'] else 'CHƯA CÓ (P0)'}")
print(f"  2. Layout Shell           : {sum_layouts['avg_pct']:3d}% (Tổng: {sum_layouts['total']:2d} | Animated: {sum_layouts['animated']:2d} | Partial: {sum_layouts['partial']:2d} | Static: {sum_layouts['static']:2d})")
print(f"  3. Feature Pages          : {sum_pages['avg_pct']:3d}% (Tổng: {sum_pages['total']:2d} | Animated: {sum_pages['animated']:2d} | Partial: {sum_pages['partial']:2d} | Static: {sum_pages['static']:2d})")
print(f"  4. Active UI Components   : {sum_active_comps['avg_pct']:3d}% (Tổng: {sum_active_comps['total']:2d} | Animated: {sum_active_comps['animated']:2d} | Partial: {sum_active_comps['partial']:2d} | Static: {sum_active_comps['static']:2d})")
print(f"  5. Orphan Components      : {sum_orphan_comps['total']:3d} files (Tách riêng khỏi mẫu số chấm điểm)")
print("--------------------------------------------------------------------------")
print(f"ĐỘ PHỦ ANIMATION TRUNG BÌNH (ACTIVE UI) : {overall_pct}%")
print(f"TỔNG SỐ ĐIỂM THIẾU ANIMATION PHÁT HIỆN  : {len(findings)} vị trí (P0: {p0_count} | P1: {p1_count} | P2: {p2_count})")
print(f"BÁO CÁO CHI TIẾT ĐÃ LƯU TẠI            : {rel_path(output_path)}")
print("==========================================================================")
PYEOF
}

main "$@"
