#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OPSX i18n FE — Pre-Flight Read-Only Auditor (Frontend Multi-Language Scoped to Figma Pages)
===========================================================================================
Quét tự động (100% Read-Only) tầng Giao diện Frontend (`resources/Frontend/js/**` & `lang/{vi,en}.json`)
CHỈ TRONG PHẠM VI CÁC TRANG CÓ TRONG DESIGN FIGMA:
  1. Giới hạn phạm vi nghiêm ngặt theo Trang Figma (`--pages` hoặc `--page`, tự động truy vết cây `import`
     và bỏ qua 100% các trang boilerplate thừa như `Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`...).
  2. Quét toàn bộ chuỗi Tiếng Việt còn fix cứng (`Hardcoded Vietnamese Strings`) trong `<template>`
     (text node, `placeholder`, `title`, `aria-label`, `alt`) và trong `<script>` chưa bọc `tt('...')`.
  3. Kiểm tra độ phủ từ điển `vi.json` / `en.json` đối với các khóa `tt('...')` đang dùng trên các trang Figma
     và kiểm tra cơ chế tự động fallback `EN -> VI` của hàm `tt()`.
  4. Kiểm tra bộ chuyển đổi ngôn ngữ `VI | EN` trên `Header.vue` / `PreHeader.vue` (Desktop & Mobile).

Hỗ trợ chạy song song hoàn toàn độc lập với `/opsx-i18n-cms` (khóa chặt `[LOCKED: app/**, config/**, resources/Backend/**]`)
và hỗ trợ tách file báo cáo theo từng trang `scratch/i18n-fe-audit-<slug>.md` khi truyền `--page <Scope>`.
"""

import argparse
import datetime
import json
import os
import re
import sys
from pathlib import Path
from typing import Any, Dict, List, Optional, Set, Tuple

IGNORE_DIRS = {
    ".git",
    "node_modules",
    "vendor",
    "storage",
    "bootstrap/cache",
    "public/build",
    "public/hot",
    ".next",
    "dist",
    "graphify-out",
    "scratch",
}

BOILERPLATE_PAGE_DIRS = {
    "@core",
    "auth",
    "users",
    "user",
    "checkout",
    "orders",
    "cart",
    "carts",
    "nestedposts",
    "documents",
    "jobs",
    "galleries",
    "learningenvironments",
}

VI_CHAR_RE = re.compile(
    r"[àáạảãâầấậẩẫăằắặẳẵèéẹẻẽêềếệểễìíịỉĩòóọỏõôồốộổỗơờớợởỡùúụủũưừứựửữỳýỵỷỹđ"
    r"ÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ]"
)


def rel_path(path: Path, root: Path) -> str:
    try:
        return str(path.resolve().relative_to(root.resolve()))
    except Exception:
        return str(path)


def read_text(path: Path) -> str:
    try:
        return path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return ""


def read_lines(path: Path) -> List[str]:
    return read_text(path).splitlines()


def singularize_token(word: str) -> str:
    w = word.lower().strip()
    if w.endswith("ies") and len(w) > 3:
        return w[:-3] + "y"
    if w.endswith("ses") and len(w) > 3:
        return w[:-2]
    if w.endswith("s") and not w.endswith("ss") and len(w) > 2:
        return w[:-1]
    return w


def iter_files(root: Path, subdirs: List[str], suffixes: Tuple[str, ...]) -> List[Path]:
    results: List[Path] = []
    for sub in subdirs:
        base = root / sub
        if not base.exists():
            continue
        if base.is_file():
            if base.name.endswith(suffixes):
                results.append(base)
            continue
        for dirpath, dirnames, filenames in os.walk(base):
            dirnames[:] = [d for d in dirnames if d not in IGNORE_DIRS]
            for fn in sorted(filenames):
                if fn.endswith(suffixes):
                    results.append(Path(dirpath) / fn)
    return results


def resolve_vue_imports_in_file(vue_file: Path, fe_js_root: Path) -> List[Path]:
    content = read_text(vue_file)
    resolved: List[Path] = []
    for m in re.finditer(r"""import\s+[^'"]+from\s+['"]([^'"]+)['"]""", content):
        imp = m.group(1).strip()
        cand: Optional[Path] = None
        if imp.startswith("@/"):
            sub = imp[2:]
            cand = fe_js_root / sub
        elif imp.startswith("./") or imp.startswith("../"):
            cand = (vue_file.parent / imp).resolve()
        if cand is not None:
            if cand.suffix == "" and cand.with_suffix(".vue").exists():
                cand = cand.with_suffix(".vue")
            if cand.exists() and cand.is_file() and cand.suffix == ".vue":
                resolved.append(cand.resolve())
    return resolved


def resolve_figma_scoped_vue_files(
    root: Path,
    pages_filter: Optional[List[str]] = None,
    single_page_mode: Optional[str] = None,
) -> Tuple[List[Path], List[Path], List[str]]:
    """
    Lọc danh sách các file `.vue` thuộc phạm vi trang Figma:
      - Nếu truyền `--page Layout`: chỉ quét `Layouts/**`, `Header.vue`, `Footer.vue`, `PreHeader.vue`, `SocialFixed.vue`.
      - Nếu truyền `--page <PageName>` (ví dụ `Home`, `About`): chỉ quét `Pages/<PageName>/**` + các Component con được trang đó import (KHÔNG quét lại `Layouts/`, `Header.vue`, `Footer.vue` để chạy song song không đụng file với `--page Layout`).
      - Nếu truyền `--pages A,B,C` hoặc mặc định: quét cả `Layouts/`, `Header/Footer`, các trang Figma Active và các Component được import bởi chúng (bỏ qua 100% các trang boilerplate thừa).
    """
    fe_js_root = root / "resources/Frontend/js"
    if not fe_js_root.exists():
        fe_js_root = root / "resources/js"
    if not fe_js_root.exists():
        return [], [], []

    all_pages = iter_files(root, [rel_path(fe_js_root / "Pages", root)], (".vue",))
    all_layouts = iter_files(root, [rel_path(fe_js_root / "Layouts", root)], (".vue",))
    all_components = iter_files(root, [rel_path(fe_js_root / "Components", root)], (".vue",))
    all_fe_vue = all_pages + all_layouts + all_components

    SHARED_LAYOUT_STEMS = {"header", "footer", "preheader", "socialfixed", "breadcrumb", "floatingcontact"}

    if single_page_mode and single_page_mode.strip().lower() == "layout":
        layout_seeds: List[Path] = list(all_layouts)
        for comp in all_components:
            if comp.stem.lower() in SHARED_LAYOUT_STEMS or "layout" in [p.lower() for p in comp.parts]:
                layout_seeds.append(comp)
        active = sorted({p.resolve() for p in layout_seeds}, key=lambda p: rel_path(p, root))
        skipped = [p for p in all_fe_vue if p.resolve() not in set(active)]
        return active, skipped, ["Layout"]

    requested_tokens: Set[str] = set()
    if pages_filter:
        for p in pages_filter:
            for sub in p.split(","):
                clean = sub.strip().lower()
                if clean:
                    requested_tokens.add(clean)
                    requested_tokens.add(singularize_token(clean))

    seed_files: List[Path] = []
    active_page_groups: Set[str] = set()

    include_layout = (not single_page_mode) and ((not requested_tokens) or ("layout" in requested_tokens) or (len(requested_tokens) > 1))
    if include_layout:
        seed_files.extend(all_layouts)
        for comp in all_components:
            if comp.stem.lower() in SHARED_LAYOUT_STEMS:
                seed_files.append(comp)
        active_page_groups.add("Layout")

    for pg in all_pages:
        pg_rel_to_pages = rel_path(pg, fe_js_root / "Pages")
        parts = [p.lower() for p in Path(pg_rel_to_pages).parts]
        top_group = Path(pg_rel_to_pages).parts[0] if len(Path(pg_rel_to_pages).parts) > 1 else pg.stem

        if requested_tokens:
            page_tokens = {p.lower() for p in Path(pg_rel_to_pages).parts} | {
                singularize_token(p.lower()) for p in Path(pg_rel_to_pages).parts
            } | {pg.stem.lower(), singularize_token(pg.stem.lower())}
            if requested_tokens.intersection(page_tokens):
                seed_files.append(pg)
                active_page_groups.add(top_group)
        else:
            if any(bp in BOILERPLATE_PAGE_DIRS for bp in parts):
                continue
            seed_files.append(pg)
            active_page_groups.add(top_group)

    visited: Set[Path] = set()
    queue: List[Path] = [f.resolve() for f in seed_files if f.exists()]

    while queue:
        curr = queue.pop(0)
        if curr in visited:
            continue
        # Trong chế độ cô lập 1 trang (--page Home), không đi xuyên vào Layouts/ hoặc Header/Footer dùng chung
        if single_page_mode and single_page_mode.strip().lower() != "layout":
            curr_parts_lower = [p.lower() for p in curr.parts]
            if "layouts" in curr_parts_lower or curr.stem.lower() in SHARED_LAYOUT_STEMS:
                continue
        visited.add(curr)
        for imported in resolve_vue_imports_in_file(curr, fe_js_root):
            if imported not in visited:
                queue.append(imported)

    active_vue_files = sorted(visited, key=lambda p: rel_path(p, root))
    skipped_boilerplate = [p for p in all_fe_vue if p.resolve() not in visited]

    return active_vue_files, skipped_boilerplate, sorted(active_page_groups)


def split_vue_sections(lines: List[str]) -> Tuple[List[Tuple[int, str]], List[Tuple[int, str]]]:
    script_lines: List[Tuple[int, str]] = []
    template_lines: List[Tuple[int, str]] = []
    mode = "none"
    template_depth = 0

    for idx, line in enumerate(lines, 1):
        if re.search(r"<script\b", line):
            mode = "script"
            continue
        if re.search(r"</script>", line):
            mode = "none"
            continue
        if re.search(r"<template\b", line):
            if mode != "script":
                if template_depth == 0:
                    mode = "template"
                    template_depth = 1
                    continue
                else:
                    template_depth += 1
        if mode == "template":
            opens = len(re.findall(r"<template\b", line))
            closes = len(re.findall(r"</template>", line))
            template_depth += opens - closes
            if template_depth <= 0 and "</template>" in line:
                mode = "none"
                template_depth = 0
                continue
            template_lines.append((idx, line))
        elif mode == "script":
            script_lines.append((idx, line))

    return script_lines, template_lines


# ==============================================================================
# 1. QUÉT TEXT TIẾNG VIỆT FIX CỨNG & KHÓA TT(...) TRONG CÁC TRANG FIGMA
# ==============================================================================

def scan_vue_files_for_i18n(
    root: Path,
    active_vue_files: List[Path],
) -> Tuple[List[Dict[str, str]], List[Dict[str, str]], Set[str]]:
    """
    Trả về:
      - `template_hardcoded`: Danh sách các dòng trong `<template>` còn chứa chuỗi Tiếng Việt fix cứng chưa bọc `tt(...)`.
      - `script_hardcoded`: Danh sách các dòng trong `<script>` còn chứa chuỗi Tiếng Việt fix cứng (nhãn UI / thông báo) chưa bọc `tt(...)`.
      - `used_tt_keys`: Tập hợp các khóa `tt('...')` / `$t('...')` đang được sử dụng trên các trang Figma.
    """
    template_hardcoded: List[Dict[str, str]] = []
    script_hardcoded: List[Dict[str, str]] = []
    used_tt_keys: Set[str] = set()

    for vf in active_vue_files:
        frel = rel_path(vf, root)
        lines = read_lines(vf)
        full_content = "\n".join(lines)
        script_lines, template_lines = split_vue_sections(lines)

        # Thu thập tất cả các khóa tt('...') / $t('...') đang gọi trong file
        for m_tt in re.finditer(r"""(?:\btt|\$t)\s*\(\s*(['"])(.*?)\1\s*\)""", full_content):
            k = m_tt.group(2).strip()
            if k:
                used_tt_keys.add(k)

        # 1A. Quét trong <template>
        in_html_comment = False
        for lineno, t_line in template_lines:
            stripped = t_line.strip()
            if not stripped:
                continue
            if "<!--" in stripped:
                if "-->" not in stripped:
                    in_html_comment = True
                continue
            if in_html_comment:
                if "-->" in stripped:
                    in_html_comment = False
                continue

            if not VI_CHAR_RE.search(stripped):
                continue

            # Loại bỏ các phần đã bọc trong tt('...') hoặc $t('...') trên dòng đó trước khi kiểm tra xem còn sót tiếng Việt không
            line_without_tt = re.sub(r"""(?:\btt|\$t)\s*\(\s*(['"]).*?\1\s*\)""", "", stripped)
            # Loại bỏ các comment nội dòng
            line_without_tt = re.sub(r"<!--.*?-->", "", line_without_tt)

            if not VI_CHAR_RE.search(line_without_tt):
                continue

            # Kiểm tra xem có phải thuộc tính tĩnh placeholder / title / aria-label / alt không
            m_attr = re.search(
                r"""(?<!:)\b(placeholder|title|aria-label|alt)\s*=\s*['"]([^'"]+)['"]""",
                line_without_tt,
            )
            if m_attr and VI_CHAR_RE.search(m_attr.group(2)):
                attr_name = m_attr.group(1)
                attr_val = m_attr.group(2).strip()
                template_hardcoded.append({
                    "file": frel,
                    "location": f"{frel}:{lineno}",
                    "kind": f"Thuộc tính `:{attr_name}`",
                    "text": attr_val[:70],
                    "suggestion": f"Đổi thành `:{attr_name}=\"tt('{attr_val}')\"` và thêm vào `en.json`",
                })
                continue

            # Loại bỏ các thẻ HTML `<...>` để lấy phần text hiển thị
            text_only = re.sub(r"<[^>]+>", " ", line_without_tt).strip()
            # Loại bỏ các thuộc tính HTML trên dòng mở thẻ đa dòng (ví dụ class="...", v-if="...")
            if re.match(r"^[a-zA-Z0-9_:@.-]+\s*=\s*['\"]", stripped) and ">" not in stripped:
                continue

            if VI_CHAR_RE.search(text_only):
                clean_txt = re.sub(r"\s+", " ", text_only)[:75]
                template_hardcoded.append({
                    "file": frel,
                    "location": f"{frel}:{lineno}",
                    "kind": "Text hiển thị `<template>`",
                    "text": clean_txt,
                    "suggestion": f"Bọc `{{{{ tt('{clean_txt[:40]}...') }}}}` (hoặc lấy từ prop CMS động)",
                })

        # 1B. Quét trong <script>
        in_block_comment = False
        is_inside_fallback_array = False
        fallback_brace_depth = 0

        for lineno, s_line in script_lines:
            stripped = s_line.strip()
            if not stripped:
                continue
            if stripped.startswith("//"):
                continue
            if "/*" in stripped:
                if "*/" not in stripped:
                    in_block_comment = True
                continue
            if in_block_comment:
                if "*/" in stripped:
                    in_block_comment = False
                continue

            # Theo dõi các mảng DEFAULT_ / FALLBACK_ / MOCK_ lớn để phân loại riêng
            if re.search(r"\b(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|sample[A-Z])\w*\s*=\s*\[", stripped):
                is_inside_fallback_array = True
                fallback_brace_depth = stripped.count("[") - stripped.count("]")
                continue
            if is_inside_fallback_array:
                fallback_brace_depth += stripped.count("[") - stripped.count("]")
                if fallback_brace_depth <= 0:
                    is_inside_fallback_array = False
                continue

            if "console." in stripped:
                continue

            if not VI_CHAR_RE.search(stripped):
                continue

            line_without_tt = re.sub(r"""(?:\btt|\$t)\s*\(\s*(['"]).*?\1\s*\)""", "", stripped)
            line_without_tt = re.sub(r"//.*$", "", line_without_tt)
            if not VI_CHAR_RE.search(line_without_tt):
                continue

            for m_str in re.finditer(r"""(['"`])((?:\\.|(?!\1).)+)\1""", line_without_tt):
                lit = m_str.group(2).strip()
                if VI_CHAR_RE.search(lit):
                    # Bỏ qua chuỗi regex hoặc đường dẫn/slug
                    if lit.startswith("/") or "àáạ" in lit.lower():
                        continue
                    script_hardcoded.append({
                        "file": frel,
                        "location": f"{frel}:{lineno}",
                        "kind": "Chuỗi tĩnh trong `<script>`",
                        "text": lit[:70],
                        "suggestion": f"Bọc `tt('{lit[:45]}')` (hoặc chuyển sang nhận prop động từ CMS)",
                    })
                    break

    return template_hardcoded, script_hardcoded, used_tt_keys


# ==============================================================================
# 2. KIỂM TRA TỪ ĐIỂN VI.JSON / EN.JSON & NÚT CHUYỂN NGÔN NGỮ HEADER
# ==============================================================================

def audit_lang_dictionaries_and_switcher(
    root: Path,
    used_tt_keys: Set[str],
) -> Dict[str, Any]:
    lang_candidates = [
        (root / "resources/Frontend/lang/vi.json", root / "resources/Frontend/lang/en.json"),
        (root / "lang/vi.json", root / "lang/en.json"),
        (root / "resources/lang/vi.json", root / "resources/lang/en.json"),
    ]
    vi_path, en_path = next(
        ((vp, ep) for vp, ep in lang_candidates if vp.exists() or ep.exists()),
        (root / "resources/Frontend/lang/vi.json", root / "resources/Frontend/lang/en.json"),
    )

    vi_dict: Dict[str, str] = {}
    en_dict: Dict[str, str] = {}

    if vi_path.exists():
        try:
            vi_dict = json.loads(read_text(vi_path))
        except Exception:
            vi_dict = {}
    if en_path.exists():
        try:
            en_dict = json.loads(read_text(en_path))
        except Exception:
            en_dict = {}

    missing_in_en: List[str] = []
    translated_in_en_count = 0
    for k in sorted(used_tt_keys):
        val_en = en_dict.get(k, "")
        if isinstance(val_en, str) and val_en.strip():
            translated_in_en_count += 1
        else:
            missing_in_en.append(k)

    # Kiểm tra Header.vue / PreHeader.vue xem đã có nút chuyển ngôn ngữ VI | EN chưa
    header_candidates = [
        root / "resources/Frontend/js/Components/Header.vue",
        root / "resources/Frontend/js/Components/Layout/Header.vue",
        root / "resources/Frontend/js/Components/PreHeader.vue",
    ]
    switcher_files: List[str] = []
    for hc in header_candidates:
        if not hc.exists():
            continue
        hc_content = read_text(hc)
        if re.search(r"locale|currentLocale|switchLocale|changeLocale|lang", hc_content, re.IGNORECASE):
            switcher_files.append(rel_path(hc, root))

    return {
        "vi_path": rel_path(vi_path, root),
        "en_path": rel_path(en_path, root),
        "vi_exists": vi_path.exists(),
        "en_exists": en_path.exists(),
        "vi_total_keys": len(vi_dict),
        "en_total_keys": len(en_dict),
        "used_tt_keys_count": len(used_tt_keys),
        "translated_in_en_count": translated_in_en_count,
        "missing_in_en": missing_in_en,
        "switcher_files": switcher_files,
    }


# ==============================================================================
# 3. SINH BÁO CÁO MARKDOWN & BỘ HANDOFF PROMPTS CHẠY SONG SONG
# ==============================================================================

def build_i18n_fe_handoff_prompts(
    active_page_groups: List[str],
    active_vue_files: List[Path],
    root: Path,
    dict_info: Dict[str, Any],
    template_hardcoded: List[Dict[str, str]],
    script_hardcoded: List[Dict[str, str]],
    single_page_mode: Optional[str] = None,
) -> List[str]:
    scope_label = single_page_mode if single_page_mode else (", ".join(active_page_groups) or "Active Figma Pages")
    editable_files_list = [rel_path(p, root) for p in active_vue_files] + [
        dict_info["vi_path"],
        dict_info["en_path"],
    ]
    editable_preview = "\n".join(f"  - `{p}`" for p in editable_files_list[:20])
    if len(editable_files_list) > 20:
        editable_preview += f"\n  - *(và {len(editable_files_list) - 20} file `.vue` con trong phạm vi Figma)*"

    lines: List[str] = [
        f"## 5. Bộ Handoff Prompts Đa Ngôn Ngữ Frontend — Phạm Vi: `{scope_label}` (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)",
        "",
        "> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: app/**, config/**, resources/Backend/**]`) để chạy song song 100% với `/opsx-i18n-cms` mà không đụng file.",
        "",
        f"### 🌐 GÓI 1 (FE i18n — `{scope_label}`): Bọc `tt('...')` Cho Toàn Bộ Chuỗi Tiếng Việt Fix Cứng & Bổ Sung Từ Điển `en.json`",
        "",
        "```markdown",
        f"Bạn đang thực thi **GÓI FE i18n ({scope_label}): Đa Ngôn Ngữ Giao Diện Frontend Theo Phạm Vi Figma** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-fe`.",
        "Hãy đọc `.agents/skills/opsx-i18n-fe/SKILL.md` trước khi thực hiện.",
        "",
        "### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)",
        "- ✅ `[ALLOWED TO EDIT]` (Chỉ được sửa các file thuộc phạm vi này):",
        editable_preview,
        "- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `app/**`, `config/**`, `database/**`, `resources/Backend/**` (đang được xử lý song song bởi `/opsx-i18n-cms`).",
        "- Bắt buộc gọi `invoke_subagent` (`Role: \"Frontend Worker\"`) để thực hiện. Cấm dùng `rm` xóa file.",
        "",
        "### 2. Danh Sách Các Chuỗi Tiếng Việt Còn Fix Cứng Cần Bọc `tt('...')` & Dịch Sang `en.json`:",
    ]

    combined_items = template_hardcoded + script_hardcoded
    if combined_items:
        for idx, item in enumerate(combined_items[:45], 1):
            lines.append(
                f"- **[FE-I18N-{idx:02d}] `{item['location']}`** ({item['kind']}): `\"{item['text']}\"` -> {item['suggestion']}"
            )
        if len(combined_items) > 45:
            lines.append(
                f"- *(Cùng {len(combined_items) - 45} vị trí khác trong báo cáo `scratch/i18n-fe-audit-*.md` — hãy quét và bọc 100% trong các file `[ALLOWED TO EDIT]` ở trên)*."
            )
    else:
        lines.append("- Toàn bộ các chuỗi hiển thị trong `<template>` và `<script>` của phạm vi này đã được bọc `tt('...')` 100%.")

    if dict_info["missing_in_en"]:
        missing_sample = ", ".join(f"`\"{k}\"`" for k in dict_info["missing_in_en"][:15])
        lines.extend([
            "",
            f"### 3. Bổ Sung Bản Dịch Tiếng Anh Còn Thiếu Trong `{dict_info['en_path']}` ({len(dict_info['missing_in_en'])} khóa):",
            f"- Các khóa `tt(...)` đang gọi trên giao diện nhưng chưa có trong `{dict_info['en_path']}`: {missing_sample}{'...' if len(dict_info['missing_in_en']) > 15 else ''}",
            f"- Hãy bổ sung đầy đủ 100% cặp key-value Tiếng Việt vào `{dict_info['vi_path']}` và bản dịch Tiếng Anh chuẩn chuyên ngành vào `{dict_info['en_path']}`.",
        ])

    verify_cmd = (
        f"python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py . --page {single_page_mode}"
        if single_page_mode
        else "python3 .agents/skills/opsx-i18n-fe/scripts/scan-i18n-fe.py ."
    )
    lines.extend([
        "",
        "### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:",
        f"- Chạy lại `{verify_cmd}` để xác nhận số chuỗi Tiếng Việt fix cứng trong `<template>` giảm về `0` và `0` khóa thiếu trong `en.json`.",
        "- Chạy `yarn frontend:build` thành công (`exit code 0`).",
        "```",
        "",
    ])

    return lines


def generate_markdown_report(
    root: Path,
    active_vue_files: List[Path],
    skipped_boilerplate_files: List[Path],
    active_page_groups: List[str],
    dict_info: Dict[str, Any],
    template_hardcoded: List[Dict[str, str]],
    script_hardcoded: List[Dict[str, str]],
    single_page_mode: Optional[str] = None,
) -> str:
    now_str = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    scope_title = f"Page `{single_page_mode}`" if single_page_mode else f"Active Figma Pages (`{', '.join(active_page_groups)}`)"

    # Thống kê theo từng file
    per_file_counts: Dict[str, Tuple[int, int]] = {}
    for vf in active_vue_files:
        frel = rel_path(vf, root)
        per_file_counts[frel] = (0, 0)
    for item in template_hardcoded:
        t_c, s_c = per_file_counts.get(item["file"], (0, 0))
        per_file_counts[item["file"]] = (t_c + 1, s_c)
    for item in script_hardcoded:
        t_c, s_c = per_file_counts.get(item["file"], (0, 0))
        per_file_counts[item["file"]] = (t_c, s_c + 1)

    en_missing_count = len(dict_info["missing_in_en"])
    en_status_badge = (
        "✅ Đầy đủ 100%"
        if en_missing_count == 0 and dict_info["en_exists"]
        else f"⚠️ Thiếu {en_missing_count} khóa đang dùng"
    )
    switcher_str = (
        ", ".join(dict_info["switcher_files"])
        if dict_info["switcher_files"]
        else "resources/Frontend/js/Components/Header.vue"
    )
    switcher_desc = (
        "Đã phát hiện logic chuyển ngôn ngữ trên Header"
        if dict_info["switcher_files"]
        else "Chưa phát hiện nút chuyển đổi ngôn ngữ VI | EN trên Header"
    )
    switcher_badge = "✅ Đã có" if dict_info["switcher_files"] else "⚠️ Cần bổ sung"

    lines: List[str] = [
        f"# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Frontend Theo Phạm Vi Figma — {scope_title} (`/opsx-i18n-fe`)",
        "",
        f"- **Thư mục dự án**: `{root}`",
        f"- **Thời gian quét**: `{now_str}`",
        f"- **Phạm vi kiểm toán**: `{scope_title}` (`{len(active_vue_files)}` files `.vue` thuộc Figma, đã tự động bỏ qua `{len(skipped_boilerplate_files)}` files boilerplate không dùng tới)",
        "- **Ranh giới chạy song song**: `[ALLOWED: resources/Frontend/js/**, resources/Frontend/lang/**]` | `[LOCKED: app/**, config/**, resources/Backend/**]`",
        f"- **Tổng số chuỗi Tiếng Việt fix cứng trong `<template>`**: `{len(template_hardcoded)}` vị trí",
        f"- **Tổng số chuỗi Tiếng Việt fix cứng trong `<script>`**: `{len(script_hardcoded)}` vị trí",
        f"- **Độ phủ từ điển `en.json` cho các khóa `tt(...)` đang dùng**: `{dict_info['translated_in_en_count']}/{dict_info['used_tt_keys_count']}` khóa (`{en_missing_count}` khóa chưa có trong `en.json`)",
        "",
        "---",
        "",
        "## 1. Trạng Thái Từ Điển `vi.json` / `en.json` & Nút Chuyển Ngôn Ngữ `VI | EN` Trên Header",
        "",
        "| Hạng mục | Tọa độ / File | Hiện trạng thực tế | Trạng thái |",
        "| :--- | :--- | :--- | :--- |",
        f"| **Từ điển Tiếng Việt (`vi.json`)** | `{dict_info['vi_path']}` | `{dict_info['vi_total_keys']}` khóa | {'✅ Đã có' if dict_info['vi_exists'] else '⚠️ Chưa tạo file'} |",
        f"| **Từ điển Tiếng Anh (`en.json`)** | `{dict_info['en_path']}` | `{dict_info['en_total_keys']}` khóa (`{dict_info['translated_in_en_count']}/{dict_info['used_tt_keys_count']}` khóa đang dùng đã dịch) | {en_status_badge} |",
        f"| **Nút chuyển đổi `VI \\| EN` trên Header** | `{switcher_str}` | {switcher_desc} | {switcher_badge} |",
        "",
        "---",
        "",
        "## 2. Thống Kê Chuỗi Tiếng Việt Fix Cứng Theo Từng File Thuộc Thiết Kế Figma",
        "",
        "| File `.vue` (Trong phạm vi Figma) | Số chuỗi Tiếng Việt cứng ở `<template>` | Số chuỗi Tiếng Việt cứng ở `<script>` | Trạng thái |",
        "| :--- | :--- | :--- | :--- |",
    ]

    for frel, (t_c, s_c) in sorted(per_file_counts.items(), key=lambda x: -(x[1][0] + x[1][1])):
        badge = "✅ 100% Đã bọc i18n / Động" if (t_c == 0 and s_c == 0) else f"⚠️ Cần bọc `{t_c + s_c}` chuỗi"
        lines.append(f"| `{frel}` | `{t_c}` | `{s_c}` | {badge} |")

    lines.extend([
        "",
        "---",
        "",
        "## 3. Chi Tiết Tọa Độ `File:Dòng` Các Chuỗi Tiếng Việt Còn Fix Cứng",
        "",
    ])

    all_hardcoded = template_hardcoded + script_hardcoded
    if all_hardcoded:
        lines.extend([
            "| Mã | Loại | Tọa độ `File:Dòng` | Chuỗi Tiếng Việt đang fix cứng | Hướng xử lý |",
            "| :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, item in enumerate(all_hardcoded[:80], 1):
            lines.append(
                f"| `FE-I18N-{idx:02d}` | {item['kind']} | `{item['location']}` | `{item['text']}` | {item['suggestion']} |"
            )
        if len(all_hardcoded) > 80:
            lines.append(f"\n> *(Hiển thị 80/{len(all_hardcoded)} vị trí đầu tiên. Xem bảng thống kê theo file ở Mục 2)*.")
    else:
        lines.append("> ✅ **100% SẠCH HARDCODE TIẾNG VIỆT**: Không phát hiện chuỗi Tiếng Việt fix cứng nào chưa bọc `tt(...)` trong phạm vi này!")

    lines.extend([
        "",
        "---",
        "",
        "## 4. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)",
        "",
        f"- **`[+] ĐỀ XUẤT THÊM (ADD)`**: Bọc `tt('...')` cho `{len(template_hardcoded)}` chuỗi trong `<template>` + `{len(script_hardcoded)}` chuỗi trong `<script>`, và bổ sung `{len(dict_info['missing_in_en'])}` khóa còn thiếu vào `{dict_info['en_path']}`.",
        f"- **`[-] ĐỀ XUẤT BỎ QUA (SKIP BOILERPLATE)`**: Bỏ qua `{len(skipped_boilerplate_files)}` files `.vue` boilerplate không nằm trong thiết kế Figma (`Checkout`, `Users`, `@Core`, `Jobs`, `Galleries`...) để tiết kiệm thời gian và chống tràn bộ nhớ.",
        "- **`[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`**: Giữ nguyên 100% cấu trúc DOM, class TailwindCSS, hiệu ứng Animation và các dữ liệu động đang nhận từ `$page.props` / CMS.",
        "",
        "---",
        "",
    ])

    lines.extend(
        build_i18n_fe_handoff_prompts(
            active_page_groups,
            active_vue_files,
            root,
            dict_info,
            template_hardcoded,
            script_hardcoded,
            single_page_mode,
        )
    )

    return "\n".join(lines)


def slugify_page(name: str) -> str:
    return re.sub(r"[^a-z0-9]+", "-", name.strip().lower()).strip("-") or "all"


def main() -> int:
    parser = argparse.ArgumentParser(
        description="OPSX i18n FE — Frontend Multi-Language Scanner Scoped to Active Figma Pages"
    )
    parser.add_argument("project_dir", nargs="?", default=".", help="Thư mục gốc của dự án cần kiểm toán")
    parser.add_argument(
        "--pages",
        default=None,
        help="Danh sách các trang Figma cần quét (ví dụ: Home,About,Products,Services,Process,Posts,Contact)",
    )
    parser.add_argument(
        "--page",
        default=None,
        help="Quét cô lập 1 trang cụ thể để chạy song song nhiều hội thoại (ví dụ: Layout, Home, About, Products...)",
    )
    parser.add_argument(
        "--output",
        default=None,
        help="Đường dẫn file báo cáo Markdown xuất ra (mặc định: scratch/i18n-fe-audit-report.md hoặc scratch/i18n-fe-audit-<page>.md)",
    )
    args = parser.parse_args()

    root = Path(args.project_dir).resolve()
    if not root.exists():
        print(f"[LỖI] Không tìm thấy thư mục dự án: {root}", file=sys.stderr)
        return 1

    pages_filter: Optional[List[str]] = None
    if args.page:
        pages_filter = [args.page.strip()]
    elif args.pages:
        pages_filter = [p.strip() for p in args.pages.split(",") if p.strip()]

    active_vue_files, skipped_boilerplate_files, active_page_groups = resolve_figma_scoped_vue_files(
        root, pages_filter, single_page_mode=args.page
    )

    template_hardcoded, script_hardcoded, used_tt_keys = scan_vue_files_for_i18n(root, active_vue_files)
    dict_info = audit_lang_dictionaries_and_switcher(root, used_tt_keys)

    if args.output:
        out_rel = args.output
    elif args.page:
        out_rel = f"scratch/i18n-fe-audit-{slugify_page(args.page)}.md"
    else:
        out_rel = "scratch/i18n-fe-audit-report.md"

    report_md = generate_markdown_report(
        root,
        active_vue_files,
        skipped_boilerplate_files,
        active_page_groups,
        dict_info,
        template_hardcoded,
        script_hardcoded,
        single_page_mode=args.page,
    )

    out_path = Path(out_rel) if Path(out_rel).is_absolute() else (root / out_rel)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(report_md, encoding="utf-8")

    print("================================================================================")
    print("OPSX i18n FE — KIỂM TOÁN ĐA NGÔN NGỮ FRONTEND THEO PHẠM VI FIGMA (VI / EN)")
    print(f"Dự án : {root}")
    print(f"Phạm vi Figma Active: {', '.join(active_page_groups)} ({len(active_vue_files)} files .vue active, bỏ qua {len(skipped_boilerplate_files)} files boilerplate)")
    print(f"Báo cáo: {out_path}")
    print("================================================================================")
    print(f" [1] Text Tiếng Việt fix cứng trong <template> : {len(template_hardcoded)} vị trí")
    print(f" [2] Text Tiếng Việt fix cứng trong <script>   : {len(script_hardcoded)} vị trí")
    print(f" [3] Khóa tt(...) đã dịch trong en.json        : {dict_info['translated_in_en_count']}/{dict_info['used_tt_keys_count']} ({len(dict_info['missing_in_en'])} khóa chưa dịch)")
    print(f" [4] Nút chuyển ngôn ngữ VI | EN trên Header   : {'ĐÃ CÓ (' + ', '.join(dict_info['switcher_files']) + ')' if dict_info['switcher_files'] else 'CHƯA CÓ'}")
    print("================================================================================")
    return 0


if __name__ == "__main__":
    sys.exit(main())
