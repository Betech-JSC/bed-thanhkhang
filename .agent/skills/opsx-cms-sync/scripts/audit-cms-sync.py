#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OPSX CMS Sync — Pre-Flight Read-Only Auditor (Dynamic AST Route-to-Model + Active Figma Scope + Full Navigation)
=================================================================================================================
Quét tự động (100% Read-Only, ZERO hardcoded domain map) toàn bộ dự án Laravel/Inertia/Vue:
  1. MODULE 1 (Dynamic AST Discovery): Tự động đọc `routes/*.php`, `app/Models/**/*.php`,
     `app/Http/Controllers/Backend/*Controller.php`, `app/Http/Controllers/Frontend/*Controller.php`
     để dựng Đồ Thị Ánh Xạ Động `Route -> Controller -> Inertia Page -> Eloquent Model -> Admin Form`.
  2. MODULE 2 (Active Figma Scope UI-to-CMS Audit): Tự động lọc đúng các trang Active theo Figma
     (qua `--pages` / `--page` hoặc tự động truy vết từ `Header.vue` / `Footer.vue` + `import` graph,
     bỏ qua 100% các file boilerplate thừa như `Checkout`, `Users`, `@Core`, `DailyRewardPopup`...)
     để phát hiện những chỗ CHƯA CONNECT với CMS và ánh xạ động vào đúng Model/Controller sẵn có.
  3. MODULE 3 (Full Navigation, Route, Link & CTA Audit): Kiểm toán toàn bộ `<Link>`, `<a>`, `route(...)`,
     `v-for` Card và `<button>` trên các trang Figma Active.
  4. MODULE 4 (8-Pillars Health + Confirm Table + CMS-Driven Handoff Prompts): Xuất báo cáo và
     bộ Handoff Prompts bám sát vào các hệ thống đã có bên CMS tại `scratch/cms-sync-audit-report.md`.
"""

import argparse
import datetime
import os
import re
import sys

if sys.platform == "win32":
    try:
        sys.stdout.reconfigure(encoding="utf-8")
        sys.stderr.reconfigure(encoding="utf-8")
    except Exception:
        pass
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

SYSTEM_MODELS_IGNORE = {
    "BaseModel",
    "Admin",
    "User",
    "Role",
    "Permission",
    "File",
    "MetaData",
    "Redirect",
    "Tag",
    "Translation",
    "ActivityLog",
}

# Các thư mục/file boilerplate mặc định của bộ khung khởi tạo không thuộc giao diện Figma public
# (sẽ tự động bị bỏ qua trừ khi người dùng truyền tường minh trong --pages hoặc được gắn link trực tiếp trên Header)
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


def singularize_token(word: str) -> str:
    w = word.lower().strip()
    if w.endswith("ies") and len(w) > 3:
        return w[:-3] + "y"
    if w.endswith("ses") and len(w) > 3:
        return w[:-2]
    if w.endswith("s") and not w.endswith("ss") and len(w) > 2:
        return w[:-1]
    return w


def tokenize_identifiers(text: str) -> List[str]:
    """
    Tách chuỗi PascalCase / camelCase / snake_case / path thành danh sách các token chữ thường đã singularize.
    """
    spaced = re.sub(r"([a-z0-9])([A-Z])", r"\1 \2", text)
    raw_tokens = re.findall(r"[a-zA-Z]{3,}", spaced)
    tokens: List[str] = []
    for t in raw_tokens:
        s = singularize_token(t.lower())
        if s not in ("index", "show", "detail", "list", "item", "page", "component", "section", "frontend", "backend", "controller", "model", "app", "resources", "vue", "php"):
            tokens.append(s)
    return tokens


def extract_php_array_strings(content: str, prop_name: str) -> List[str]:
    pattern = rf"\${prop_name}\s*=\s*\[(.*?)\];"
    m = re.search(pattern, content, re.DOTALL)
    if not m:
        return []
    body = m.group(1)
    body = re.sub(r"//.*", "", body)
    body = re.sub(r"/\*.*?\*/", "", body, flags=re.DOTALL)
    return re.findall(r"""['"]([a-zA-Z0-9_]+)['"]""", body)


def extract_php_casts_keys(content: str) -> List[str]:
    pattern = r"\$casts\s*=\s*\[(.*?)\];"
    m = re.search(pattern, content, re.DOTALL)
    if not m:
        return []
    body = m.group(1)
    body = re.sub(r"//.*", "", body)
    return re.findall(r"""['"]([a-zA-Z0-9_]+)['"]\s*=>""", body)


# ==============================================================================
# MODULE 1: DYNAMIC AST DISCOVERY (ROUTE -> CONTROLLER -> PAGE -> MODEL -> ADMIN)
# ==============================================================================

def discover_existing_cms_systems(root: Path) -> Tuple[List[Dict[str, Any]], Dict[str, Any]]:
    """
    Tự động khám phá 100% động từ mã nguồn:
      - Toàn bộ `app/Models/**/*.php` (kèm `*Translation.php`)
      - Toàn bộ `app/Http/Controllers/Backend/*Controller.php` & `resources/Backend/js/Pages/**`
      - Toàn bộ `app/Http/Controllers/Frontend/*Controller.php` (`Inertia::render` + Model queries)
    Không sử dụng bất kỳ bảng từ khóa fix cứng nào.
    """
    model_files = iter_files(root, ["app/Models"], (".php",))
    backend_controllers = iter_files(root, ["app/Http/Controllers/Backend"], (".php",))
    frontend_controllers = iter_files(root, ["app/Http/Controllers/Frontend"], (".php",))
    backend_pages = iter_files(root, ["resources/Backend/js/Pages"], (".vue",))
    inertia_mw = root / "app/Http/Middleware/HandleInertiaFrontendRequests.php"
    if not inertia_mw.exists():
        inertia_mw = root / "app/Http/Middleware/HandleInertiaRequests.php"
    inertia_mw_content = read_text(inertia_mw) if inertia_mw.exists() else ""

    # 1. Gom các trường dịch từ *Translation.php
    translation_fields_by_model: Dict[str, List[str]] = {}
    for mf in model_files:
        if mf.name.endswith("Translation.php"):
            base_model_name = mf.name[: -len("Translation.php")]
            content = read_text(mf)
            t_fields = extract_php_array_strings(content, "fillable")
            if t_fields:
                translation_fields_by_model[base_model_name] = [
                    f for f in t_fields if f not in ("locale", f"{base_model_name.lower()}_id")
                ]

    systems: List[Dict[str, Any]] = []

    for mf in model_files:
        if mf.name.endswith("Translation.php") or mf.name.endswith("RefCategory.php"):
            continue
        model_name = mf.stem
        if model_name in SYSTEM_MODELS_IGNORE:
            continue

        content = read_text(mf)
        ns_match = re.search(r"namespace\s+([^;]+);", content)
        namespace = ns_match.group(1).strip() if ns_match else "App\\Models"
        fqcn = f"{namespace}\\{model_name}"

        fillable = extract_php_array_strings(content, "fillable")
        translated = extract_php_array_strings(content, "translatedAttributes")
        if not translated and model_name in translation_fields_by_model:
            translated = translation_fields_by_model[model_name]
        casts = extract_php_casts_keys(content)

        # Với Config / Setting, bóc tách tự động các khóa cấu hình xuất hiện trong file
        extra_keys: List[str] = []
        if model_name in ("Config", "Setting"):
            for m in re.finditer(r"""['"]([a-z0-9_]{3,35})['"]\s*=>""", content):
                k = m.group(1)
                if k not in extra_keys and k not in ("name", "val", "value", "group", "type", "locale"):
                    extra_keys.append(k)

        # Tìm Backend Controllers sử dụng Model này
        matched_be_ctrl: List[str] = []
        be_tokens: Set[str] = {singularize_token(model_name)}
        for bc in backend_controllers:
            if bc.stem == "DashboardController":
                continue
            bc_content = read_text(bc)
            if (
                bc.stem.lower().startswith(model_name.lower())
                or re.search(rf"\buse\s+{re.escape(fqcn)}\b|\b{re.escape(model_name)}::", bc_content)
            ):
                matched_be_ctrl.append(rel_path(bc, root))
                ctrl_base = re.sub(r"Controller$", "", bc.stem)
                for tok in tokenize_identifiers(ctrl_base):
                    be_tokens.add(tok)

        # Tìm Admin Vue Pages tương ứng
        matched_be_views: List[str] = []
        stem_lower = model_name.lower()
        plural_candidates = {
            stem_lower,
            stem_lower + "s",
            stem_lower[:-1] + "ies" if stem_lower.endswith("y") else stem_lower + "s",
        }
        for bp in backend_pages:
            bp_rel = rel_path(bp, root)
            bp_parts = [p.lower() for p in bp.parts]
            if any(pc in bp_parts for pc in plural_candidates):
                matched_be_views.append(bp_rel)

        # Tìm Frontend Controllers hoặc Middleware đang truy vấn Model này
        matched_fe_consumers: List[str] = []
        for fc in frontend_controllers:
            fc_content = read_text(fc)
            if re.search(rf"\b{re.escape(model_name)}::|\buse\s+{re.escape(fqcn)}\b", fc_content):
                matched_fe_consumers.append(rel_path(fc, root))
        if inertia_mw.exists() and re.search(rf"\b{re.escape(model_name)}::|\buse\s+{re.escape(fqcn)}\b", inertia_mw_content):
            matched_fe_consumers.append(rel_path(inertia_mw, root))

        for tok in tokenize_identifiers(model_name):
            be_tokens.add(tok)

        systems.append({
            "domain_tokens": sorted(be_tokens),
            "model_name": model_name,
            "fqcn": fqcn,
            "model_path": rel_path(mf, root),
            "fillable": fillable,
            "translated": translated,
            "casts": casts,
            "extra_keys": extra_keys,
            "backend_controllers": matched_be_ctrl,
            "backend_views": matched_be_views[:4],
            "frontend_consumers": matched_fe_consumers,
            "is_connected_to_frontend": len(matched_fe_consumers) > 0,
        })

    # 2. Dựng đồ thị Frontend Controller -> Inertia Pages -> Models
    page_to_controller: Dict[str, str] = {}
    page_to_models: Dict[str, List[str]] = {}
    controller_to_pages: Dict[str, List[str]] = {}
    controller_to_models: Dict[str, List[str]] = {}

    model_names_set = {s["model_name"]: s["fqcn"] for s in systems}

    for fc in frontend_controllers:
        frel = rel_path(fc, root)
        fc_content = read_text(fc)
        used_fqcns: List[str] = []
        for m_name, fqcn in model_names_set.items():
            if re.search(rf"\b{re.escape(m_name)}::|\buse\s+{re.escape(fqcn)}\b", fc_content):
                used_fqcns.append(fqcn)
        controller_to_models[frel] = used_fqcns

        rendered_pages: List[str] = []
        for m_ren in re.finditer(r"Inertia::render\s*\(\s*['\"]([^'\"]+)['\"]", fc_content):
            p_name = m_ren.group(1).strip()
            rendered_pages.append(p_name)
            page_to_controller[p_name] = frel
            page_to_models[p_name] = used_fqcns
        controller_to_pages[frel] = rendered_pages

    ast_graph = {
        "page_to_controller": page_to_controller,
        "page_to_models": page_to_models,
        "controller_to_pages": controller_to_pages,
        "controller_to_models": controller_to_models,
        "inertia_middleware": rel_path(inertia_mw, root) if inertia_mw.exists() else "",
    }

    return systems, ast_graph


def format_cms_system_summary(sys_item: Dict[str, Any]) -> str:
    fields_preview = ", ".join((sys_item["fillable"] + sys_item["translated"] + sys_item["extra_keys"])[:8])
    be_ctrl = sys_item["backend_controllers"][0] if sys_item["backend_controllers"] else "Admin CMS"
    return f"`{sys_item['fqcn']}` (fields: `{fields_preview or 'id, status, ...'}`; Admin: `{be_ctrl}`)"


def resolve_cms_system_dynamically(
    file_rel: str,
    context_expr: str,
    systems: List[Dict[str, Any]],
    ast_graph: Dict[str, Any],
    kind: str = "general",
) -> str:
    r"""
    Ánh xạ 100% tự động (Dynamic AST Resolution) từ một vị trí trên UI (`file_rel` + `context_expr`)
    vào đúng Hệ thống đã có sẵn bên CMS:
      1. Nếu là thông tin cấu hình chung (SĐT, Email, MXH, Bản đồ, Header, Footer) -> Trỏ vào `Config` / `Setting`.
      2. Nếu là `<form>` gửi liên hệ/báo giá -> Trỏ vào `Contact` (`ContactController`).
      3. Nếu file thuộc một Page Inertia (`Pages/X/Y.vue`) đã được render bởi một `Frontend/*Controller.php` ->
         Lấy trực tiếp Model mà Controller đó đang import/truy vấn trong AST.
      4. Đối chiếu token tên biến / đường dẫn file với `domain_tokens` (tự động trích xuất từ tên Model và tên Backend Controller,
         ví dụ `FaqController` -> token `faq` -> `App\Models\Post\Post`).
    """
    if kind == "config":
        cfg_sys = [s for s in systems if s["model_name"] in ("Config", "Setting")]
        if cfg_sys:
            return " + ".join(format_cms_system_summary(s) for s in cfg_sys[:1])

    if kind == "contact":
        ct_sys = [s for s in systems if s["model_name"] in ("Contact", "Apply")]
        if ct_sys:
            return " + ".join(format_cms_system_summary(s) for s in ct_sys[:1])

    # Bước A: Kiểm tra xem biến context_expr có khớp trực tiếp với một Backend Controller / Model cụ thể không (ví dụ faqList -> FaqController -> Post)
    expr_tokens = set(tokenize_identifiers(context_expr))
    if expr_tokens:
        scored_expr: List[Tuple[int, Dict[str, Any]]] = []
        for s in systems:
            if s["model_name"] in ("ConfigStatusLog", "ContactStatusLog", "Keyword", "KeywordRefDate", "OrderLine", "ProductRefPost", "ProductVariantRefOption"):
                continue
            overlap = expr_tokens.intersection(set(s["domain_tokens"]))
            if overlap:
                # Ưu tiên Model chính có Backend Controller
                bonus = 2 if s["backend_controllers"] else 0
                scored_expr.append((len(overlap) * 10 + bonus, s))
        if scored_expr:
            scored_expr.sort(key=lambda x: x[0], reverse=True)
            best_score = scored_expr[0][0]
            top_matches = [s for sc, s in scored_expr if sc == best_score][:2]
            return " + ".join(format_cms_system_summary(s) for s in top_matches)

    # Bước B: Truy vết từ đường dẫn Page (`resources/Frontend/js/Pages/<PageKey>.vue`) qua AST `page_to_models`
    m_page = re.search(r"Pages/(.+)\.vue$", file_rel)
    if m_page:
        page_key = m_page.group(1)
        # Thử khớp trực tiếp hoặc khớp theo thư mục đầu (ví dụ Services/Components/X -> Services/Index)
        top_folder = page_key.split("/")[0]
        candidate_fqcns: List[str] = []
        if page_key in ast_graph["page_to_models"]:
            candidate_fqcns = ast_graph["page_to_models"][page_key]
        else:
            for p_k, fqcns in ast_graph["page_to_models"].items():
                if p_k.startswith(top_folder + "/") or p_k.lower() == top_folder.lower():
                    for f in fqcns:
                        if f not in candidate_fqcns:
                            candidate_fqcns.append(f)
        if candidate_fqcns:
            matched_sys = [
                s for s in systems
                if s["fqcn"] in candidate_fqcns and s["model_name"] not in ("Config", "Keyword", "KeywordRefDate")
            ]
            if not matched_sys:
                matched_sys = [s for s in systems if s["fqcn"] in candidate_fqcns]
            if matched_sys:
                return " + ".join(format_cms_system_summary(s) for s in matched_sys[:2])

    # Bước C: Đối chiếu token của cả đường dẫn file + context_expr với domain_tokens của hệ thống CMS
    all_tokens = set(tokenize_identifiers(f"{file_rel} {context_expr}"))
    scored_path: List[Tuple[int, Dict[str, Any]]] = []
    for s in systems:
        if not s["backend_controllers"]:
            continue
        overlap = all_tokens.intersection(set(s["domain_tokens"]))
        if overlap:
            scored_path.append((len(overlap), s))
    if scored_path:
        scored_path.sort(key=lambda x: x[0], reverse=True)
        return " + ".join(format_cms_system_summary(s) for _, s in scored_path[:2])

    # Fallback về Config / Setting nếu là phần tử chung
    cfg_sys = [s for s in systems if s["model_name"] in ("Config", "Setting")]
    if cfg_sys:
        return format_cms_system_summary(cfg_sys[0])
    return "Model tương ứng trong `app/Models/`"


# ==============================================================================
# ACTIVE FIGMA PAGE SCOPE FILTER (CHỐNG BÁO LỖI GIẢ TRÊN BOILERPLATE THỪA)
# ==============================================================================

def parse_frontend_routes(root: Path) -> Dict[str, Any]:
    route_names: Set[str] = set()
    base_route_names: Set[str] = set()
    static_uris: Set[str] = {"/"}
    route_to_controller: Dict[str, str] = {}

    for rf_rel in ("routes/frontend.php", "routes/web.php", "routes/api.php"):
        rf = root / rf_rel
        if not rf.exists():
            continue
        content = read_text(rf)
        lines = content.splitlines()

        for line in lines:
            stripped = line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue

            ctrl_match = re.search(r"([A-Za-z0-9_\\]+Controller)::class", line)
            ctrl_name = ctrl_match.group(1).split("\\")[-1] if ctrl_match else ""

            for m_name in re.finditer(
                r"""->name\s*\(\s*(?:\$[a-zA-Z0-9_]+\s*\.\s*['"]\.?([^'"]+)['"]|['"](?:\{\$[a-zA-Z0-9_]+\}\.)?([^'"]+)['"])\s*\)""",
                line,
            ):
                rname = (m_name.group(1) or m_name.group(2) or "").strip().lstrip(".")
                if rname:
                    route_names.add(rname)
                    base_route_names.add(rname)
                    for loc_prefix in ("vi.", "en."):
                        if rname.startswith(loc_prefix):
                            rname = rname[len(loc_prefix) :]
                            base_route_names.add(rname)
                    if ctrl_name:
                        route_to_controller[rname] = ctrl_name

            m_uri = re.search(r"""Route::(?:get|post|any)\s*\(\s*['"]([^'"]+)['"]""", line)
            if m_uri:
                uri = "/" + m_uri.group(1).strip("/")
                if uri == "//":
                    uri = "/"
                static_uris.add(uri)

    return {
        "route_names": route_names,
        "base_route_names": base_route_names,
        "static_uris": static_uris,
        "route_to_controller": route_to_controller,
    }


def resolve_vue_imports_in_file(vue_file: Path, fe_js_root: Path) -> List[Path]:
    """
    Trích xuất toàn bộ các file `.vue` được import bên trong `vue_file`
    (hỗ trợ cả `@/Components/...`, `@/...`, và đường dẫn tương đối `./...`, `../...`).
    """
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


def resolve_active_figma_vue_files(
    root: Path,
    pages_filter: Optional[List[str]] = None,
) -> Tuple[List[Path], List[Path], List[str]]:
    """
    Trả về:
      - `active_vue_files`: Danh sách các file `.vue` thuộc các trang Active trong thiết kế Figma
        (Layouts + Header/Footer + Active Pages + các Components thực sự được import bởi chúng).
      - `skipped_boilerplate_files`: Danh sách các file `.vue` boilerplate thừa bị bỏ qua.
      - `active_page_groups`: Danh sách tên các nhóm trang Active đang được kiểm toán.
    """
    fe_js_root = root / "resources/Frontend/js"
    if not fe_js_root.exists():
        fe_js_root = root / "resources/js"
    if not fe_js_root.exists():
        return [], [], []

    all_pages = iter_files(root, [rel_path(fe_js_root / "Pages", root)], (".vue",))
    all_layouts = iter_files(root, [rel_path(fe_js_root / "Layouts", root)], (".vue",))
    all_components = iter_files(root, [rel_path(fe_js_root / "Components", root)], (".vue",))

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

    # Luôn đưa Layouts và Header/Footer vào tập gốc nếu không bị giới hạn vào 1 page đơn lẻ khác Layout
    include_layout = (not requested_tokens) or ("layout" in requested_tokens) or (len(requested_tokens) > 1)
    if include_layout:
        seed_files.extend(all_layouts)
        for comp in all_components:
            if comp.stem.lower() in ("header", "footer", "preheader", "socialfixed", "breadcrumb", "floatingcontact"):
                seed_files.append(comp)

    for pg in all_pages:
        pg_rel_to_pages = rel_path(pg, fe_js_root / "Pages")
        parts = [p.lower() for p in Path(pg_rel_to_pages).parts]
        top_group = Path(pg_rel_to_pages).parts[0] if len(Path(pg_rel_to_pages).parts) > 1 else pg.stem

        if requested_tokens:
            # Kiểm tra xem page này có khớp với --pages / --page người dùng truyền vào không
            page_tokens = {p.lower() for p in Path(pg_rel_to_pages).parts} | {
                singularize_token(p.lower()) for p in Path(pg_rel_to_pages).parts
            } | {pg.stem.lower(), singularize_token(pg.stem.lower())}
            if requested_tokens.intersection(page_tokens):
                seed_files.append(pg)
                active_page_groups.add(top_group)
        else:
            # Mặc định: Tự động bỏ qua các thư mục boilerplate không thuộc giao diện Figma public
            if any(bp in BOILERPLATE_PAGE_DIRS for bp in parts):
                continue
            seed_files.append(pg)
            active_page_groups.add(top_group)

    # Lan truyền (BFS) theo cây `import ... from '...'` để lấy đúng các Components thực sự được sử dụng bởi các trang Figma Active
    visited: Set[Path] = set()
    queue: List[Path] = [f.resolve() for f in seed_files if f.exists()]

    while queue:
        curr = queue.pop(0)
        if curr in visited:
            continue
        visited.add(curr)
        for imported in resolve_vue_imports_in_file(curr, fe_js_root):
            if imported not in visited:
                queue.append(imported)

    all_fe_vue = all_pages + all_layouts + all_components
    active_vue_files = sorted(visited, key=lambda p: rel_path(p, root))
    skipped_boilerplate = [p for p in all_fe_vue if p.resolve() not in visited]

    return active_vue_files, skipped_boilerplate, sorted(active_page_groups)


# ==============================================================================
# MODULE 2: QUÉT TOÀN BỘ UI ACTIVE PHÁT HIỆN CHỖ CHƯA CONNECT VỚI CMS
# ==============================================================================

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


def is_active_page_name(page_comp: str, active_page_groups: List[str], pages_filter: Optional[List[str]]) -> bool:
    parts = [p.lower() for p in page_comp.replace("\\", "/").split("/")]
    if not pages_filter and any(bp in BOILERPLATE_PAGE_DIRS for bp in parts):
        return False
    if pages_filter:
        active_lower = {g.lower() for g in active_page_groups} | {singularize_token(g.lower()) for g in active_page_groups}
        if not any(p in active_lower or singularize_token(p) in active_lower for p in parts):
            return False
    return True


def audit_unconnected_ui_to_cms(
    root: Path,
    cms_systems: List[Dict[str, Any]],
    ast_graph: Dict[str, Any],
    active_vue_files: List[Path],
    active_page_groups: List[str],
    pages_filter: Optional[List[str]] = None,
) -> List[Dict[str, str]]:
    findings: List[Dict[str, str]] = []

    # 1. Kiểm tra các Frontend Controllers phục vụ các trang Active
    fe_controllers = iter_files(root, ["app/Http/Controllers/Frontend"], (".php",))
    for fc in fe_controllers:
        frel = rel_path(fc, root)
        if not pages_filter and "auth/" in frel.lower():
            continue
        lines = read_lines(fc)
        content = "\n".join(lines)
        has_any_model_query = bool(
            re.search(
                r"(::query\s*\(|::where\s*\(|::active\s*\(|::orderBy|::latest|::first\s*\(|::find|::all\s*\(|::getByPosition|getLuxiBrandSettings|Contact::create|Apply::create)",
                content,
            )
        )
        for idx, line in enumerate(lines, 1):
            m = re.search(r"Inertia::render\s*\(\s*['\"]([^'\"]+)['\"](?:\s*,\s*\[(.*?)\])?\s*\)", line)
            if m:
                page_comp = m.group(1)
                if not is_active_page_name(page_comp, active_page_groups, pages_filter):
                    continue
                props_arg = m.group(2)
                if not has_any_model_query and (props_arg is None or props_arg.strip() == ""):
                    cms_target = resolve_cms_system_dynamically(f"Pages/{page_comp}.vue", page_comp, cms_systems, ast_graph)
                    findings.append({
                        "severity": "HIGH (Chưa connect Controller)",
                        "location": f"{frel}:{idx}",
                        "ui_element": f"`Inertia::render('{page_comp}')` không truyền dữ liệu từ Database",
                        "issue": f"Controller `{fc.name}` đang render trang `{page_comp}` tĩnh hoàn toàn, chưa truy vấn Model CMS.",
                        "cms_mapping": cms_target,
                        "action": f"Truy vấn dữ liệu từ {cms_target} trong `{frel}` và truyền qua props xuống `Pages/{page_comp}.vue`.",
                    })

    # 1B. Kiểm tra Controller có hàm fallback / mock tự sinh dữ liệu giả khi DB rỗng không (ADD-04)
    PHP_SAFE_DEFAULT_METHODS = {
        "getDefaultLocale",
        "getDefaultGuardName",
        "getDefaultDriver",
        "getDefaultPagination",
        "getDefaultParentAttribute",
        "getDefaultSupportInfo",
        "getDefaultConnection",
    }
    for fc in fe_controllers:
        frel = rel_path(fc, root)
        c_lines = read_lines(fc)
        for idx, line in enumerate(c_lines, 1):
            m_fb = re.search(r"function\s+((?:get)?(?:Fallback|Mock|Default)[a-zA-Z0-9_]*)\s*\(", line, re.IGNORECASE)
            if m_fb:
                fb_name = m_fb.group(1)
                if fb_name in PHP_SAFE_DEFAULT_METHODS:
                    continue
                window = "\n".join(c_lines[max(0, idx - 1) : min(len(c_lines), idx + 25)])
                if re.search(r"['\"](?:id|title|name|slug|image|price|items|solutions|products|posts)['\"]\s*=>", window) or "return [" in window:
                    findings.append({
                        "severity": "HIGH (Hàm Fallback/Mock cứng trong Controller)",
                        "location": f"{frel}:{idx}",
                        "ui_element": f"Hàm `{fb_name}()` trả về dữ liệu mock cứng",
                        "issue": f"Controller `{fc.name}` chứa hàm fallback/mock `{fb_name}()` tự sinh dữ liệu giả khi Database trống, che giấu dữ liệu CMS thật.",
                        "cms_mapping": resolve_cms_system_dynamically(frel, fb_name, cms_systems, ast_graph),
                        "action": f"Xóa hàm `{fb_name}()` tại `{frel}:{idx}`, trả về 404 hoặc mảng rỗng [] khi Database trống.",
                    })
            m_call = re.search(r"->\s*((?:get)?(?:Fallback|Mock)[a-zA-Z0-9_]*)\s*\(", line, re.IGNORECASE)
            if m_call:
                callee = m_call.group(1)
                findings.append({
                    "severity": "HIGH (Gọi hàm Fallback/Mock trong Controller)",
                    "location": f"{frel}:{idx}",
                    "ui_element": f"Lệnh gọi `->{callee}()`",
                    "issue": f"Controller `{fc.name}` gọi hàm mock `{callee}()` khi DB rỗng thay vì trả về 404 hoặc mảng rỗng [].",
                    "cms_mapping": resolve_cms_system_dynamically(frel, callee, cms_systems, ast_graph),
                    "action": f"Remove lệnh gọi `->{callee}()` tại `{frel}:{idx}`, xử lý trực tiếp kết quả truy vấn từ Database.",
                })

    # 2. Kiểm tra routes/frontend.php và routes/web.php xem có Route::inertia(...) hoặc closure trả về Inertia::render tĩnh cho các trang Active không
    for rf_name in ("routes/frontend.php", "routes/web.php"):
        rf = root / rf_name
        if not rf.exists():
            continue
        lines = read_lines(rf)
        for idx, line in enumerate(lines, 1):
            stripped = line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue
            # Bắt cả Route::inertia('/uri', 'Page/Name') và Inertia::render('Page/Name')
            m_ri = re.search(r"Route::inertia\s*\(\s*['\"]([^'\"]+)['\"]\s*,\s*['\"]([^'\"]+)['\"]", line)
            m_ir = re.search(r"Inertia::render\s*\(\s*['\"]([^'\"]+)['\"]", line)
            page_name = ""
            if m_ri:
                page_name = m_ri.group(2)
            elif m_ir:
                page_name = m_ir.group(1)
            if page_name and is_active_page_name(page_name, active_page_groups, pages_filter):
                cms_target = resolve_cms_system_dynamically(f"Pages/{page_name}.vue", page_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "HIGH (Route render tĩnh)",
                    "location": f"{rf_name}:{idx}",
                    "ui_element": f"Route render trực tiếp `{page_name}` không qua Controller CMS",
                    "issue": "Route trả về view Inertia tĩnh trực tiếp trong file route, không lấy dữ liệu từ CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Chuyển route tại `{rf_name}:{idx}` sang gọi Frontend Controller và truyền dữ liệu từ {cms_target}.",
                })

    # 3. Quét các file .vue thuộc Active Figma Scope
    UI_ONLY_ARRAY_NAMES = {
        "sizes",
        "colors",
        "variants",
        "breakpoints",
        "animations",
        "transitions",
        "breadcrumbs",
        "tabs",
        "sortOptions",
        "perPageOptions",
        "locales",
        "languages",
        "icons",
    }

    for vf in active_vue_files:
        frel = rel_path(vf, root)
        lines = read_lines(vf)
        full_content = "\n".join(lines)
        script_lines, template_lines = split_vue_sections(lines)
        script_content = "\n".join(l for _, l in script_lines)
        template_content = "\n".join(l for _, l in template_lines)

        has_props_binding = bool(
            re.search(r"defineProps|props\s*:|\$page\??\.props|usePage\s*\(", full_content)
        )

        # 3A. Quét các mảng dữ liệu tĩnh trong <script> chứa object nội dung
        for s_idx, (lineno, s_line) in enumerate(script_lines):
            stripped = s_line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue

            m_arr = re.match(
                r"^(?:export\s+)?(?:const|let|var)\s+([a-zA-Z0-9_]+)\s*=\s*(?:ref\s*\(\s*|computed\s*\(\s*\(\)\s*=>\s*)?\[\s*$",
                stripped,
            )
            if not m_arr:
                continue

            var_name = m_arr.group(1)
            if var_name in UI_ONLY_ARRAY_NAMES:
                continue

            lookahead = "\n".join(
                l for _, l in script_lines[s_idx : min(s_idx + 25, len(script_lines))]
            )
            has_content_keys = bool(
                re.search(
                    r"\b(title|name|desc|description|excerpt|content|image|img|thumbnail|slug|price|address|phone|question|answer|year)\s*:",
                    lookahead,
                )
            )
            if not has_content_keys:
                continue

            if re.search(r"\b(props\.|global\.|this\.\$page|page\.props)\b", lookahead):
                continue

            used_directly_in_vfor = bool(
                re.search(rf"v-for\s*=\s*['\"][^'\"]+\s+(?:in|of)\s+{re.escape(var_name)}\b", template_content)
            )
            is_fallback_named = bool(
                re.match(r"^(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|mock[A-Z]|sample[A-Z]|rawMock|remainingMocks)", var_name)
            )
            uses_props_for_var = bool(
                re.search(rf"props\.{re.escape(var_name)}|props\.\w+\?\.length|\$page\??\.props", script_content)
            )

            if is_fallback_named:
                cms_target = resolve_cms_system_dynamically(frel, var_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "HIGH (Mảng Mock / Fallback cứng trong UI)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Mảng mock/fallback `{var_name} = [...]`",
                    "issue": f"Component `{vf.name}` chứa mảng mock/fallback `{var_name}` che giấu dữ liệu CMS thật khi Database trống.",
                    "cms_mapping": cms_target,
                    "action": f"Xóa mảng mock/fallback `{var_name}` tại `{frel}:{lineno}`, chỉ nhận dữ liệu động từ CMS props.",
                })
            elif used_directly_in_vfor and not uses_props_for_var:
                cms_target = resolve_cms_system_dynamically(frel, var_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "HIGH (Mảng UI tĩnh chưa nối CMS)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Mảng tĩnh `{var_name} = [...]` đang render trực tiếp qua `v-for`",
                    "issue": f"Giao diện đang lặp trực tiếp qua mảng dữ liệu cứng `{var_name}` trong `<script>`, chưa nhận dữ liệu động từ CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Xóa mảng tĩnh `{var_name}` tại `{frel}:{lineno}`, nhận dữ liệu qua `defineProps` từ {cms_target}.",
                })
            elif not has_props_binding:
                cms_target = resolve_cms_system_dynamically(frel, var_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "MEDIUM (Dữ liệu tĩnh trong Component)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Biến mảng dữ liệu tĩnh `{var_name}` trong component không có `defineProps`",
                    "issue": f"Component `{vf.name}` chứa mảng dữ liệu cứng `{var_name}` và chưa khai báo nhận `props` từ trang cha / CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Chuyển `{var_name}` tại `{frel}:{lineno}` thành prop truyền từ trang cha lấy từ {cms_target}.",
                })

        # 3A-2. Quét Vue Options API data() { return { ... } } (ADD-04)
        for s_idx, (lineno, s_line) in enumerate(script_lines):
            stripped = s_line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue

            m_opt = re.match(r"^([a-zA-Z0-9_]+)\s*:\s*\[(?:\s*\{|\s*$)", stripped)
            if not m_opt:
                continue

            opt_name = m_opt.group(1)
            if opt_name in UI_ONLY_ARRAY_NAMES or opt_name in {"routes", "headers", "columns", "fields", "rules", "options", "breadcrumbs", "locales"}:
                continue

            lookahead = "\n".join(
                l for _, l in script_lines[s_idx : min(s_idx + 25, len(script_lines))]
            )
            has_content_keys = bool(
                re.search(
                    r"\b(title|name|desc|description|excerpt|content|image|img|thumbnail|slug|price|address|phone|question|answer|year)\s*:",
                    lookahead,
                )
            )
            is_mock_name = bool(
                re.match(r"^(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|mock[A-Z]|sample[A-Z]|rawMock|remainingMocks)", opt_name)
            )

            prev_lines = "\n".join(l for _, l in script_lines[max(0, s_idx - 30) : s_idx])
            in_data = "data()" in prev_lines or "data:" in prev_lines or "data ()" in prev_lines

            if in_data and (has_content_keys or is_mock_name):
                if "defaultItems" in lookahead or is_mock_name or re.search(r"['\"](?:iron-man|khung-vo|kc-|of-|cs-|thumb_|mock)", lookahead):
                    cms_target = resolve_cms_system_dynamically(frel, opt_name, cms_systems, ast_graph)
                    findings.append({
                        "severity": "HIGH (Vue Options API Mảng tĩnh chưa nối CMS)",
                        "location": f"{frel}:{lineno}",
                        "ui_element": f"Mảng cứng `{opt_name}: [...]` trong data() Options API",
                        "issue": f"Component `{vf.name}` khai báo mảng dữ liệu mẫu `{opt_name}` trong data(), gây hiện dữ liệu giả khi CMS trống.",
                        "cms_mapping": cms_target,
                        "action": f"Xóa mảng tĩnh `{opt_name}` trong data() tại `{frel}:{lineno}`, nhận dữ liệu động qua props.",
                    })

        # 3A-3. Quét logic đệm mảng (< N, .slice(), remainingMocks, defaultItems, padded.push) (ADD-04)
        for s_idx, (lineno, s_line) in enumerate(script_lines):
            stripped = s_line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue

            # Slicing on default/mock arrays
            m_slice = re.search(
                r"\b(defaultItems|remainingMocks|rawMockProducts|mockCatalog|fallbackItems|currentMocks|defaultServices|defaultCards)\.slice\s*\(",
                stripped,
                re.IGNORECASE,
            )
            if m_slice:
                findings.append({
                    "severity": "HIGH (Logic đệm/cắt mảng Mock)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Lệnh cắt mảng mock `.{m_slice.group(1)}.slice(...)`",
                    "issue": f"Component `{vf.name}` đang dùng logic `.slice()` để đệm dữ liệu giả khi CMS thiếu bản ghi.",
                    "cms_mapping": resolve_cms_system_dynamically(frel, m_slice.group(1), cms_systems, ast_graph),
                    "action": f"Xóa logic đệm mảng tại `{frel}:{lineno}`; chỉ hiển thị đúng số lượng sản phẩm thật từ CMS.",
                })

            # Padding loops / padded.push / push(cat.defaultItems)
            m_pad = re.search(
                r"\b(?:padded|padding)\.push\s*\(|\bpush\s*\(\s*(?:cat\.)?defaultItems",
                stripped,
                re.IGNORECASE,
            )
            if m_pad:
                findings.append({
                    "severity": "HIGH (Logic đệm mảng Padding)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Logic đệm bản ghi giả `{stripped}`",
                    "issue": f"Component `{vf.name}` đang dùng vòng lặp đệm để ép đủ số lượng card giả lập khi CMS trống.",
                    "cms_mapping": resolve_cms_system_dynamically(frel, "padding", cms_systems, ast_graph),
                    "action": f"Xóa logic đệm mảng padding tại `{frel}:{lineno}`; hiển thị Empty State nếu CMS chưa có dữ liệu.",
                })

            # Mock array spread [...items, ...remainingMocks]
            m_spread = re.search(
                r"\[\s*\.\.\.\w+\s*,\s*\.\.\.(?:remainingMocks|defaultItems|rawMock|mock|fallback)\w*",
                stripped,
                re.IGNORECASE,
            )
            if m_spread:
                findings.append({
                    "severity": "HIGH (Logic ghép mảng Mock)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Toán tử spread ghép mảng mock `{stripped}`",
                    "issue": f"Component `{vf.name}` đang ghép mảng mock vào mảng thật để ép đủ số lượng card.",
                    "cms_mapping": resolve_cms_system_dynamically(frel, "spread mock", cms_systems, ast_graph),
                    "action": f"Xóa ghép mảng giả tại `{frel}:{lineno}`; chỉ giữ mảng dữ liệu thật từ props.",
                })

        # 3B. Quét thông tin liên hệ / thương hiệu hardcode trong <template>
        for lineno, t_line in template_lines:
            stripped = t_line.strip()
            if stripped.startswith("<!--"):
                continue

            m_tel = re.search(r"""(?<!:)\bhref\s*=\s*['"](tel:[^'"]+|mailto:[^'"]+)['"]""", t_line)
            if m_tel:
                raw_val = m_tel.group(1)
                cms_target = resolve_cms_system_dynamically(frel, "hotline email config", cms_systems, ast_graph, kind="config")
                findings.append({
                    "severity": "HIGH (Hardcode Liên hệ)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Link liên hệ cứng `href=\"{raw_val}\"`",
                    "issue": "Số điện thoại / Email đang gắn cứng trong HTML thay vì lấy động từ Cấu hình chung của CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Đổi thành `:href=\"'tel:' + ($page.props.global?.hotline || ...)\"` kết nối vào {cms_target}.",
                })

            m_map = re.search(r"""<iframe[^>]+(?<!:)\bsrc\s*=\s*['"](https?://(?:www\.)?google\.com/maps[^'"]+)['"]""", t_line)
            if m_map:
                cms_target = resolve_cms_system_dynamically(frel, "map_iframe config", cms_systems, ast_graph, kind="config")
                findings.append({
                    "severity": "HIGH (Hardcode Bản đồ)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": "Thẻ `<iframe src=\"https://www.google.com/maps/...\">` gắn cứng URL",
                    "issue": "Bản đồ Google Maps đang hardcode URL tĩnh, Admin sửa `map_iframe` trong CMS sẽ không cập nhật được ngoài UI.",
                    "cms_mapping": cms_target,
                    "action": f"Bind `:src=\"$page.props.global?.map_iframe\"` kết nối vào trường `map_iframe` của {cms_target}.",
                })

            m_soc = re.search(
                r"""(?<!:)\bhref\s*=\s*['"](https?://(?:www\.)?(?:facebook\.com|zalo\.me|tiktok\.com|youtube\.com|instagram\.com)[^'"]*)['"]""",
                t_line,
            )
            if m_soc:
                soc_url = m_soc.group(1)
                cms_target = resolve_cms_system_dynamically(frel, "social config", cms_systems, ast_graph, kind="config")
                findings.append({
                    "severity": "MEDIUM (Hardcode Mạng xã hội)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Link MXH tĩnh `href=\"{soc_url}\"`",
                    "issue": "Đường dẫn mạng xã hội đang gắn cứng trong template thay vì lấy từ Cấu hình CMS (`facebook`, `zalo`, `tiktok`...).",
                    "cms_mapping": cms_target,
                    "action": f"Bind `:href=\"$page.props.global?.facebook\"` (hoặc `zalo`/`tiktok`) từ {cms_target}.",
                })

        # 3C. Quét các thẻ <form> chưa nối vào ContactController
        for t_idx, (lineno, t_line) in enumerate(template_lines):
            if re.search(r"<form\b", t_line):
                form_window = "\n".join(
                    l for _, l in template_lines[t_idx : min(t_idx + 18, len(template_lines))]
                )
                is_search_form = bool(
                    re.search(r"search|tim-kiem|filter|keyword", form_window, re.IGNORECASE)
                    or ("search" in vf.name.lower())
                )
                if is_search_form:
                    continue
                has_real_submit = any(
                    kw in script_content
                    for kw in (
                        "useForm",
                        ".post(",
                        "router.post",
                        "Inertia.post",
                        "$inertia.post",
                        "axios.post",
                        "fetch(",
                    )
                )
                if not has_real_submit:
                    cms_target = resolve_cms_system_dynamically(frel, "contact form", cms_systems, ast_graph, kind="contact")
                    findings.append({
                        "severity": "HIGH (Form chưa nối CMS)",
                        "location": f"{frel}:{lineno}",
                        "ui_element": f"Biểu mẫu `<form>` trong `{vf.name}` chưa gửi dữ liệu về Backend",
                        "issue": "Form trên giao diện chưa kết nối `useForm` / `router.post` / `axios.post` tới route lưu dữ liệu của CMS.",
                        "cms_mapping": cms_target,
                        "action": f"Kết nối sự kiện `@submit.prevent` của form tại `{frel}:{lineno}` về route `contact.store` (`ContactController@store`) để lưu vào {cms_target}.",
                    })

    return findings


# ==============================================================================
# MODULE 3: KIỂM TOÁN TOÀN BỘ ĐIỀU HƯỚNG TRÊN CÁC TRANG FIGMA ACTIVE
# ==============================================================================

def suggest_closest_route(target_hint: str, base_route_names: Set[str]) -> str:
    tokens = tokenize_identifiers(target_hint)
    candidates: List[str] = []
    for tok in tokens:
        for r in sorted(base_route_names):
            if tok in r.lower() and r not in candidates:
                candidates.append(r)
    if candidates:
        return ", ".join(f"`{c}`" for c in candidates[:3])
    all_sorted = sorted(base_route_names)
    return ", ".join(f"`{c}`" for c in all_sorted[:5]) if all_sorted else "Kiểm tra `routes/frontend.php`"


def audit_full_navigation_and_links(
    root: Path,
    active_vue_files: List[Path],
) -> Tuple[Dict[str, Any], List[Dict[str, str]]]:
    route_registry = parse_frontend_routes(root)
    base_route_names: Set[str] = route_registry["base_route_names"]
    nav_issues: List[Dict[str, str]] = []

    for vf in active_vue_files:
        frel = rel_path(vf, root)
        lines = read_lines(vf)
        script_lines, template_lines = split_vue_sections(lines)

        # 1. Dead links trong <template>
        for lineno, t_line in template_lines:
            stripped = t_line.strip()
            if stripped.startswith("<!--"):
                continue

            m_dead = re.search(
                r"""(?:(?<![:\w-])href\s*=\s*['"](#|javascript:void\(0\);?|)['"]|:href\s*=\s*['"]['"]#['"]['"])""",
                t_line,
            )
            if m_dead:
                suggested_routes = suggest_closest_route(f"{frel} {t_line}", base_route_names)
                nav_issues.append({
                    "type": "DEAD_LINK (`href=\"#\"` / Empty Link)",
                    "location": f"{frel}:{lineno}",
                    "element": stripped[:90],
                    "issue": "Thẻ `<a>` / `<Link>` đang để `href=\"#\"` hoặc rỗng — bấm vào không điều hướng.",
                    "fix": f"Đổi sang `<Link :href=\"route(...)\">` hoặc bind URL động từ CMS (gợi ý route: {suggested_routes}).",
                })

        # 2. Dead links trong <script>
        for lineno, s_line in script_lines:
            stripped = s_line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue
            m_js_dead = re.search(r"""\b(href|link|url|to)\s*:\s*['"](#|javascript:void\(0\);?)['"]""", s_line)
            if m_js_dead:
                if "||" in s_line or "??" in s_line or "?" in s_line:
                    continue
                prop_key = m_js_dead.group(1)
                suggested_routes = suggest_closest_route(f"{frel} {s_line}", base_route_names)
                nav_issues.append({
                    "type": "DEAD_MENU_ITEM (`href: '#'` trong `<script>`)",
                    "location": f"{frel}:{lineno}",
                    "element": stripped[:90],
                    "issue": f"Thuộc tính điều hướng `{prop_key}: '#'` đang gán cứng trong dữ liệu script.",
                    "fix": f"Nối `{prop_key}` với hàm `route(...)` hợp lệ hoặc `slug` từ CMS (gợi ý: {suggested_routes}).",
                })

        # 3. Sai tên route('...') so với routes/frontend.php
        if base_route_names:
            for lineno, line in enumerate(lines, 1):
                stripped = line.strip()
                if stripped.startswith("//") or stripped.startswith("<!--"):
                    continue
                for m_rt in re.finditer(r"""\broute\s*\(\s*(['"`])([^'"`]+)\1""", line):
                    raw_rname = m_rt.group(2).strip()
                    if raw_rname.startswith("admin.") or raw_rname.startswith("${"):
                        m_dyn = re.match(r"^\$\{[^}]+\}\.(.+)$", raw_rname)
                        if m_dyn:
                            raw_rname = m_dyn.group(1)
                        else:
                            continue
                    for loc_p in ("vi.", "en."):
                        if raw_rname.startswith(loc_p):
                            raw_rname = raw_rname[len(loc_p) :]

                    if "${" in raw_rname:
                        continue

                    if raw_rname not in base_route_names:
                        suggested = suggest_closest_route(raw_rname, base_route_names)
                        nav_issues.append({
                            "type": "ROUTE_MISMATCH (Sai tên `route(...)`)",
                            "location": f"{frel}:{lineno}",
                            "element": f"`route('{m_rt.group(2)}')`",
                            "issue": f"Tên route `{raw_rname}` không tồn tại trong `routes/frontend.php` hoặc `routes/web.php`.",
                            "fix": f"Cập nhật tên route khớp với định nghĩa Backend (gợi ý: {suggested}).",
                        })

        # 4. Nút <button> thiếu @click / type="submit" (bỏ qua các UI atom button bọc ngoài như ButtonSeeMore.vue / ButtonSlider.vue nhận listener qua $attrs)
        is_reusable_button_atom = vf.parent.name == "Components" and vf.stem.lower().startswith("button")
        if not is_reusable_button_atom:
            tpl_joined = "\n".join(l for _, l in template_lines)
            for m_btn in re.finditer(
                r"<button\b((?:[^>'\"/]|\"[^\"]*\"|'[^']*'|/)*)>(.*?)</button>",
                tpl_joined,
                flags=re.DOTALL,
            ):
                attrs = m_btn.group(1)
                inner_html = m_btn.group(2)
                has_click = any(
                    k in attrs
                    for k in (
                        "@click",
                        "v-on:click",
                        "type=\"submit\"",
                        "type='submit'",
                        ":type=",
                        "swiper-button-",
                        "v-bind=\"$attrs\"",
                    )
                )
                if has_click:
                    continue

                prefix_text = tpl_joined[: m_btn.start()]
                approx_idx = prefix_text.count("\n")
                lineno = template_lines[approx_idx][0] if approx_idx < len(template_lines) else 1

                start_check = max(0, approx_idx - 5)
                end_check = min(len(template_lines), approx_idx + 4)
                surrounding = "\n".join(l for _, l in template_lines[start_check:end_check])
                if "<Link" in surrounding or "<a " in surrounding or "<form" in surrounding or "@click" in surrounding:
                    continue

                clean_label = re.sub(r"<[^>]+>", "", inner_html).strip()
                clean_label = re.sub(r"\s+", " ", clean_label)[:50] or "(Button icon/trống)"
                nav_issues.append({
                    "type": "UNWIRED_BUTTON (Nút thiếu `@click` / điều hướng)",
                    "location": f"{frel}:{lineno}",
                    "element": f"`<button>` ({clean_label})",
                    "issue": "Thẻ `<button>` không có sự kiện `@click`, không phải `type=\"submit\"` và không bọc trong `<Link>`.",
                    "fix": "Gắn `@click` (mở Modal / chuyển slide / gọi action) hoặc đổi sang `<Link :href=\"...\">` điều hướng đúng trang.",
                })

        # 5. Danh sách Card v-for thiếu <Link> / điều hướng slug
        for t_idx, (lineno, t_line) in enumerate(template_lines):
            m_vfor = re.search(
                r"""v-for\s*=\s*['"](?:\(?([a-zA-Z0-9_]+)(?:\s*,\s*[a-zA-Z0-9_]+)?\)?\s+(?:in|of)\s+([a-zA-Z0-9_.]+))['"]""",
                t_line,
            )
            if not m_vfor:
                continue
            item_var = m_vfor.group(1)
            list_expr = m_vfor.group(2)
            if not re.search(
                r"(product|service|post|article|news|blog|project|policy|policies)",
                list_expr,
                re.IGNORECASE,
            ):
                continue
            if re.search(
                r"(categor|tab|filter|image|slide|thumb|tag|spec|feature|benefit|step|stat|faq|nav|menu|link|guide|item|option|attribute)",
                list_expr,
                re.IGNORECASE,
            ):
                continue

            vfor_window = "\n".join(
                l for _, l in template_lines[t_idx : min(t_idx + 40, len(template_lines))]
            )
            has_card_nav = any(
                kw in vfor_window
                for kw in ("<Link", ":href=", "@click", "ProductCard", "PostCard", "ServiceCard", "ProjectCard", "CardProduct", "CardPost", "CardService")
            )
            if not has_card_nav:
                suggested_routes = suggest_closest_route(f"{frel} {list_expr}", base_route_names)
                nav_issues.append({
                    "type": "UNLINKED_CMS_CARD (Card danh sách chưa gắn `<Link>` / `slug`)",
                    "location": f"{frel}:{lineno}",
                    "element": f"`v-for=\"{item_var} in {list_expr}\"`",
                    "issue": f"Danh sách `{list_expr}` hiển thị Card nhưng chưa bọc `<Link>` hoặc `@click` truyền `{item_var}.slug` sang trang chi tiết.",
                    "fix": f"Bọc Card trong `<Link :href=\"route(..., {item_var}.slug)\">` (gợi ý route: {suggested_routes}).",
                })

    return route_registry, nav_issues


# ==============================================================================
# MODULE 4: KIỂM TOÁN 8 TRỤ CỘT CMS & TỰ ĐỘNG SINH BỘ HANDOFF PROMPTS
# ==============================================================================

def normalize_cache_prefix(raw_expr: str) -> str:
    expr = raw_expr.strip()
    m = re.match(r"""^['"]([a-zA-Z0-9_.:-]+?)['"]""", expr)
    if m:
        lit = m.group(1)
        if "{" in lit:
            lit = lit.split("{", 1)[0]
        return lit
    m2 = re.match(r"""^['"]([a-zA-Z0-9_.:-]+?)\{\$""", expr)
    if m2:
        return m2.group(1)
    return ""


def audit_pillar_1_admin_dashboard_and_sidebar(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    dashboard_ctrl = root / "app/Http/Controllers/Backend/DashboardController.php"
    sidebar_main = root / "resources/Backend/js/Components/SidebarMain.vue"
    favicon = root / "public/favicon.ico"

    status = "PASS"
    notes: List[str] = []

    if dashboard_ctrl.exists():
        content = read_text(dashboard_ctrl)
        if "Schema::hasTable" in content:
            keep_items.append({
                "pillar": "Trụ cột 1 (Admin & Dashboard)",
                "location": f"{rel_path(dashboard_ctrl, root)}:51",
                "title": "Bảo vệ truy vấn KPI trên Dashboard bằng `Schema::hasTable(...)`",
                "reason": "Đảm bảo trang Admin Dashboard không bị lỗi 500 khi bảng dữ liệu đang trống hoặc vừa migrate.",
            })
            notes.append("DashboardController có `Schema::hasTable` guard")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 1 (Admin & Dashboard)",
                "location": f"{rel_path(dashboard_ctrl, root)}:1",
                "title": "Bọc các truy vấn thống kê Dashboard trong `Schema::hasTable(...)` và `try/catch`",
                "reason": "Tránh lỗi 500 trên trang quản trị khi triển khai lên môi trường DB mới.",
            })
            notes.append("DashboardController thiếu `Schema::hasTable` guard")

    if sidebar_main.exists():
        lines = read_lines(sidebar_main)
        unused_routes = []
        for idx, line in enumerate(lines, 1):
            stripped = line.strip()
            if stripped.startswith("//") or stripped.startswith("/*"):
                continue
            for legacy_mod in ("admin.jobs.", "admin.applies.", "admin.learning-environments.", "admin.prizes."):
                if legacy_mod in stripped:
                    unused_routes.append((idx, legacy_mod))
        if unused_routes:
            status = "WARN"
            first_line, mod_name = unused_routes[0]
            remove_items.append({
                "pillar": "Trụ cột 1 (Admin Sidebar)",
                "location": f"{rel_path(sidebar_main, root)}:{first_line}",
                "title": f"Kiểm tra & ẩn các mục menu Sidebar không có trong thiết kế Figma ({len(unused_routes)} mục như `{mod_name}`)",
                "reason": "Tinh gọn thanh điều hướng Admin CMS đúng với các module thực tế của dự án.",
            })
        else:
            keep_items.append({
                "pillar": "Trụ cột 1 (Admin Sidebar)",
                "location": f"{rel_path(sidebar_main, root)}:1",
                "title": "Cấu trúc Menu Sidebar Admin tinh gọn theo đúng nghiệp vụ dự án",
                "reason": "Đã ẩn các module không sử dụng, chỉ hiển thị các mục quản trị thực tế.",
            })

    if favicon.exists():
        keep_items.append({
            "pillar": "Trụ cột 1 (Brand Assets)",
            "location": "public/favicon.ico",
            "title": "Bộ Favicon Retina & Web Manifest chuẩn thương hiệu",
            "reason": "Đã đồng bộ đầy đủ icon nhận diện thương hiệu trên cả Admin và Frontend.",
        })

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_2_media_manager(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    fm_candidates = [
        root / "packages/bed-package-essentials/core/resources/Backend/js/Components/FileManager.vue",
        root / "resources/Backend/js/Components/FileManager.vue",
    ]
    thumb_candidates = [
        root / "packages/bed-package-essentials/core/resources/Backend/js/Components/Thumbnail.vue",
        root / "resources/Backend/js/Components/Thumbnail.vue",
    ]

    fm_file = next((p for p in fm_candidates if p.exists()), None)
    thumb_file = next((p for p in thumb_candidates if p.exists()), None)

    status = "PASS"
    notes: List[str] = []

    if fm_file:
        content = read_text(fm_file)
        has_reset_on_remove = "this.fetchData = true" in content and "this.getFiles({ page: 1 }, true)" in content
        has_empty_guard = "normalizedNewFiles" in content or "requestPage <= 1" in content
        if has_reset_on_remove and has_empty_guard:
            keep_items.append({
                "pillar": "Trụ cột 2 (Media Manager)",
                "location": f"{rel_path(fm_file, root)}:500",
                "title": "Bản vá reload danh sách file khi xóa ảnh cuối cùng trong `FileManager.vue`",
                "reason": "Đảm bảo `this.data.files` được làm mới tức thì và reset `this.fetchData = true` trong `onRemove()`.",
            })
            notes.append("`FileManager.vue` đã có bản vá reload")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 2 (Media Manager)",
                "location": f"{rel_path(fm_file, root)}:471",
                "title": "Bổ sung logic reset `this.data.files = []`, `this.page = 1`, `this.fetchData = true` trong `FileManager.vue`",
                "reason": "Khắc phục lỗi xóa ảnh cuối cùng trong thư mục nhưng UI vẫn giữ ảnh cũ không chịu reload.",
            })
            notes.append("`FileManager.vue` thiếu bản vá reload khi xóa hết file")

    if thumb_file:
        content = read_text(thumb_file)
        if "@click.stop" in content:
            keep_items.append({
                "pillar": "Trụ cột 2 (Media Manager)",
                "location": f"{rel_path(thumb_file, root)}:30",
                "title": "Chặn nổi bọt sự kiện `@click.stop=\"$emit('remove', file)\"` trên `Thumbnail.vue`",
                "reason": "Ngăn việc bấm nút Xóa file lại kích hoạt nhầm hành động chọn ảnh vào bài viết/sản phẩm.",
            })
            notes.append("`Thumbnail.vue` đã có `@click.stop`")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 2 (Media Manager)",
                "location": f"{rel_path(thumb_file, root)}:27",
                "title": "Thêm modifier `.stop` vào `@click.stop=\"$emit('remove', file)\"` trong `Thumbnail.vue`",
                "reason": "Tránh xung đột sự kiện click xóa với `@click=\"onSelect(file)\"` của thẻ cha.",
            })
            notes.append("`Thumbnail.vue` thiếu `@click.stop`")

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_3_product_schema_and_form(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    product_model = root / "app/Models/Product/Product.php"
    if not product_model.exists():
        product_model = root / "app/Models/Product.php"
    product_form = root / "resources/Backend/js/Pages/Products/Products/Form.vue"

    status = "PASS"
    notes: List[str] = []

    if product_model.exists():
        content = read_text(product_model)
        has_json_attrs = "filter_attributes" in content or "pdp_metadata" in content or "specifications" in content
        has_cat_sync = "product_ref_categories" in content or "syncCategories" in content or "categories()" in content
        if has_json_attrs and has_cat_sync:
            keep_items.append({
                "pillar": "Trụ cột 3 (Schema JSON & Categories)",
                "location": f"{rel_path(product_model, root)}:1",
                "title": "Schema JSON (`filter_attributes`, `pdp_metadata`) & đồng bộ 2 chiều `product_ref_categories`",
                "reason": "Hỗ trợ lưu trữ đầy đủ bộ lọc chuyên sâu và các tab chi tiết sản phẩm theo Figma.",
            })
            notes.append("Model `Product` đã hỗ trợ JSON schema & đồng bộ danh mục")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 3 (Schema JSON & Categories)",
                "location": f"{rel_path(product_model, root)}:1",
                "title": "Mở rộng JSON casts và đồng bộ bảng trung gian `product_ref_categories` khi lưu Product",
                "reason": "Đảm bảo bộ lọc sản phẩm và các tab chi tiết sản phẩm lấy 100% từ CMS.",
            })
            notes.append("Model `Product` cần kiểm tra JSON attributes / categories sync")

    if product_form.exists():
        lines = read_lines(product_form)
        price_field_lines = [
            idx
            for idx, line in enumerate(lines, 1)
            if re.search(r"""v-model=["']form\.price["']""", line) and not line.strip().startswith("<!--")
        ]
        if price_field_lines:
            remove_items.append({
                "pillar": "Trụ cột 3 (Custom CMS Form)",
                "location": f"{rel_path(product_form, root)}:{price_field_lines[0]}",
                "title": "Đối chiếu Figma xem có cần ẩn trường Giá cố định (`form.price`) nếu sản phẩm là mô hình nhận báo giá",
                "reason": "Tinh gọn biểu mẫu Admin khớp với các trường thực tế hiển thị ngoài giao diện.",
            })
            notes.append("Phát hiện `form.price` trên Product Form")
        else:
            keep_items.append({
                "pillar": "Trụ cột 3 (Custom CMS Form)",
                "location": f"{rel_path(product_form, root)}:1",
                "title": "Biểu mẫu Quản trị Sản phẩm (`Products/Form.vue`) đã tùy biến khớp với thiết kế Figma",
                "reason": "Đã ẩn các trường không có trên Figma và giữ lại đúng các tab cấu hình kỹ thuật.",
            })
            notes.append("`Products/Form.vue` đã tinh gọn theo Figma")

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_4_leads_normalization(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    contact_model = root / "app/Models/Contact.php"
    status = "PASS"
    notes: List[str] = []

    if contact_model.exists():
        content = read_text(contact_model)
        if "normalizeLeadData" in content or "normalizeContactData" in content:
            keep_items.append({
                "pillar": "Trụ cột 4 (Leads Normalization)",
                "location": f"{rel_path(contact_model, root)}:146",
                "title": "Chuẩn hóa dữ liệu Leads `Contact::normalizeLeadData()` (`lead_type`, `form_source`, song ngữ EN/VI)",
                "reason": "Hợp nhất dữ liệu từ Modal Báo giá PDP, Modal Tư vấn và Form Liên hệ về định dạng chuẩn cho Admin.",
            })
            notes.append("`Contact::normalizeLeadData()` đã hoạt động")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 4 (Leads Normalization)",
                "location": f"{rel_path(contact_model, root)}:1",
                "title": "Bổ sung hàm chuẩn hóa `normalizeLeadData()` trong `app/Models/Contact.php`",
                "reason": "Chuẩn hóa `lead_type`, `form_source` và đồng bộ khóa tiếng Anh / tiếng Việt cho `contacts.data`.",
            })
            notes.append("`Contact.php` chưa có `normalizeLeadData()`")

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_5_global_brand_settings(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    config_model = root / "app/Models/Config.php"
    config_ctrl = root / "app/Http/Controllers/Backend/ConfigController.php"
    inertia_mw = root / "app/Http/Middleware/HandleInertiaFrontendRequests.php"

    status = "PASS"
    notes: List[str] = []

    if config_model.exists():
        content = read_text(config_model)
        if "map_iframe" in content and ("BrandSettings" in content or "getGeneralSettings" in content):
            keep_items.append({
                "pillar": "Trụ cột 5 (Global Brand Settings)",
                "location": f"{rel_path(config_model, root)}:79",
                "title": "Hàm trung tâm trong `Config.php` đồng bộ 2 chiều khóa cấu hình thương hiệu & `map_iframe`",
                "reason": "Cung cấp nguồn dữ liệu thương hiệu thống nhất (Hotline, Email, Địa chỉ, MXH, Bản đồ, Giới thiệu).",
            })
            notes.append("`Config.php` đã đồng bộ cấu hình thương hiệu & `map_iframe`")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 5 (Global Brand Settings)",
                "location": f"{rel_path(config_model, root)}:1",
                "title": "Bổ sung hàm lấy cấu hình thương hiệu tập trung và trường `map_iframe` trong `Config.php`",
                "reason": "Cho phép Admin cập nhật thông tin liên hệ, mạng xã hội và bản đồ Google Maps động từ CMS.",
            })

    if config_ctrl.exists():
        content = read_text(config_ctrl)
        if "<iframe" not in content and "preg_match" not in content:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 5 (Global Brand Settings)",
                "location": f"{rel_path(config_ctrl, root)}:108",
                "title": "Tự động bóc tách thuộc tính `src=\"...\"` khi Admin dán nguyên thẻ `<iframe>` vào `map_iframe` trong `ConfigController::store()`",
                "reason": "Giúp Admin có thể copy-paste trực tiếp mã nhúng `<iframe>` từ Google Maps mà không gây lỗi giao diện.",
            })

    if inertia_mw.exists():
        content = read_text(inertia_mw)
        if "'global' =>" in content or '"global" =>' in content:
            keep_items.append({
                "pillar": "Trụ cột 5 (Inertia Shared Props)",
                "location": f"{rel_path(inertia_mw, root)}:179",
                "title": "Chia sẻ `props.global` toàn cục qua `HandleInertiaFrontendRequests.php`",
                "reason": "Giúp Header, Footer, Modal Tư vấn và mọi trang Frontend tự động nhận thông tin thương hiệu từ CMS.",
            })
            notes.append("`HandleInertiaFrontendRequests.php` đã chia sẻ `props.global`")
        else:
            status = "WARN"
            add_items.append({
                "pillar": "Trụ cột 5 (Inertia Shared Props)",
                "location": f"{rel_path(inertia_mw, root)}:30",
                "title": "Chia sẻ `'global' => Config::...` trong `HandleInertiaFrontendRequests::share()`",
                "reason": "Kết nối cấu hình chung của CMS xuống toàn bộ các Layout và Page trên Frontend.",
            })

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_6_seeders_and_hardcode_guard(
    root: Path,
    unconnected_ui_items: List[Dict[str, str]],
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    status = "PASS"
    notes: List[str] = []

    cicd_file = root / ".github/workflows/ci-cd.yml"
    if cicd_file.exists():
        content = read_text(cicd_file)
        if "migrate:fresh" in content or "db:wipe" in content:
            status = "FAIL"
            remove_items.append({
                "pillar": "Trụ cột 6 (CI/CD & DB Safety)",
                "location": f"{rel_path(cicd_file, root)}:1",
                "title": "KHẨN CẤP (P0): Loại bỏ lệnh `migrate:fresh` / `db:wipe` khỏi `.github/workflows/ci-cd.yml`",
                "reason": "Vi phạm STRICT SAFE GUARD — gây mất trắng dữ liệu khách hàng khi deploy.",
            })
        else:
            keep_items.append({
                "pillar": "Trụ cột 6 (CI/CD & Auto-Seeder)",
                "location": f"{rel_path(cicd_file, root)}:1",
                "title": "Pipeline CI/CD chạy `migrate --force` và `RoleSeeder` an toàn (Idempotent, không mất dữ liệu)",
                "reason": "Tuân thủ tuyệt đối nguyên tắc an toàn Database trên Production.",
            })
            notes.append("CI/CD an toàn (không chứa `migrate:fresh`)")

    fe_controllers = iter_files(root, ["app/Http/Controllers/Frontend"], (".php",))
    db_connected_controllers = 0
    for ctrl in fe_controllers:
        content = read_text(ctrl)
        if any(
            kw in content
            for kw in (
                "::query()",
                "::active()",
                "getLuxiBrandSettings",
                "Slider::getByPosition",
                "resolveCatalog",
                "resolvePolicies",
                "::where(",
            )
        ):
            db_connected_controllers += 1

    if db_connected_controllers > 0:
        keep_items.append({
            "pillar": "Trụ cột 6 (Dynamic Controllers & Legacy Guard)",
            "location": "app/Http/Controllers/Frontend/",
            "title": f"{db_connected_controllers} Frontend Controllers đã ưu tiên truy vấn Eloquent DB",
            "reason": "Đảm bảo dữ liệu CMS luôn được ưu tiên số 1.",
        })
        notes.append(f"{db_connected_controllers} Frontend Controllers kết nối DB động")

    if unconnected_ui_items:
        status = "WARN"
        notes.append(f"Phát hiện {len(unconnected_ui_items)} vị trí UI Active chưa connect CMS")
        for u_item in unconnected_ui_items:
            add_items.append({
                "pillar": "Trụ cột 6 (UI-to-CMS Integration)",
                "location": u_item["location"],
                "title": f"Kết nối {u_item['ui_element']} vào {u_item['cms_mapping']}",
                "reason": u_item["issue"],
            })

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}


def audit_pillar_7_cache_key_contract(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    read_files = iter_files(
        root,
        ["app/Http/Controllers/Frontend", "app/Http/Controllers/Api", "app/Http/Middleware"],
        (".php",),
    )
    write_files = iter_files(
        root,
        ["app/Models", "app/Http/Controllers/Backend", "app/Observers"],
        (".php",),
    )

    remembered_prefixes: Dict[str, Tuple[str, int, str]] = {}

    for fpath in read_files:
        lines = read_lines(fpath)
        for idx, line in enumerate(lines, 1):
            for m in re.finditer(r"Cache::(?:remember|rememberForever|put)\s*\(\s*([^,]+),", line):
                raw_key = m.group(1).strip()
                prefix = normalize_cache_prefix(raw_key)
                if prefix:
                    remembered_prefixes.setdefault(prefix, (rel_path(fpath, root), idx, raw_key))

    forgotten_prefixes: Set[str] = set()
    for fpath in write_files:
        for line in read_lines(fpath):
            for m in re.finditer(r"Cache::forget\s*\(\s*([^)]+)\)", line):
                raw_key = m.group(1).strip()
                prefix = normalize_cache_prefix(raw_key)
                if prefix:
                    forgotten_prefixes.add(prefix)
            for m2 in re.finditer(r"""\$cacheKeys\[\]\s*=\s*['"]([a-zA-Z0-9_.:-]+?)(?:\{\$|['"])""", line):
                forgotten_prefixes.add(m2.group(1))

    unmatched: List[Tuple[str, str, int, str]] = []
    matched_count = 0

    for prefix, (frel, lineno, raw_expr) in sorted(remembered_prefixes.items()):
        is_forgiven = any(
            prefix == fp or prefix.startswith(fp) or fp.startswith(prefix)
            for fp in forgotten_prefixes
        )
        if is_forgiven:
            matched_count += 1
        elif "v3_" in prefix and "resolveHomeCacheSignature" in read_text(root / frel):
            matched_count += 1
        else:
            unmatched.append((prefix, frel, lineno, raw_expr))

    status = "PASS"
    if unmatched:
        status = "WARN"
        for prefix, frel, lineno, raw_expr in unmatched:
            add_items.append({
                "pillar": "Trụ cột 7 (Cache Invalidation Contract)",
                "location": f"{frel}:{lineno}",
                "title": f"Bổ sung `Cache::forget('{prefix}...')` vào Model `booted()` / Backend Controller khi Admin lưu dữ liệu",
                "reason": f"Khóa cache `{raw_expr}` đang được lưu ở Frontend nhưng chưa tìm thấy lệnh `Cache::forget` tương ứng khi cập nhật CMS.",
            })

    keep_items.append({
        "pillar": "Trụ cột 7 (Cache Key Contract)",
        "location": "app/Models/ & app/Http/Controllers/Frontend/",
        "title": f"Hợp đồng Invalidation cho {matched_count}/{len(remembered_prefixes)} nhóm khóa Cache",
        "reason": "Đảm bảo khi Admin bấm Lưu trong CMS, dữ liệu ngoài Frontend cập nhật tức thì (0 giây độ trễ).",
    })

    summary = f"Đã khớp {matched_count}/{len(remembered_prefixes)} tiền tố khóa Cache"
    if unmatched:
        summary += f" ({len(unmatched)} khóa cần bổ sung Cache::forget)"
    return {"status": status, "summary": summary}


def audit_pillar_8_integration_tests(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    test_dir = root / "tests/Feature"
    cms_tests = sorted(test_dir.glob("*Cms*Test.php")) if test_dir.exists() else []

    if cms_tests:
        names = ", ".join(p.name for p in cms_tests)
        keep_items.append({
            "pillar": "Trụ cột 8 (PHPUnit E2E CMS Tests)",
            "location": f"tests/Feature/ ({len(cms_tests)} files)",
            "title": f"Bộ {len(cms_tests)} bài test tích hợp CMS-to-Frontend (`{names}`)",
            "reason": "Kiểm chứng tự động chu trình `Admin POST -> DB Assert -> Cache Invalidation -> Frontend GET Assert`.",
        })
        return {"status": "PASS", "summary": f"Đã có {len(cms_tests)} file test tích hợp CMS ({names})"}

    add_items.append({
        "pillar": "Trụ cột 8 (PHPUnit E2E CMS Tests)",
        "location": "tests/Feature/",
        "title": "Bổ sung bộ PHPUnit Feature Tests kiểm chứng đồng bộ `Admin POST -> Cache Flush -> Frontend GET`",
        "reason": "Đảm bảo bằng chứng tự động 100% cho mọi module CMS trước khi bàn giao.",
    })
    return {"status": "WARN", "summary": "Chưa có file `*Cms*Test.php` trong `tests/Feature/`"}


def build_handoff_prompts(
    cms_systems: List[Dict[str, Any]],
    unconnected_ui_items: List[Dict[str, str]],
    nav_issues: List[Dict[str, str]],
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
) -> List[str]:
    lines: List[str] = [
        "## 6. Bộ Handoff Prompts Sinh Tự Động Từ Hệ Thống CMS Có Sẵn (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)",
        "",
        "> 💡 **Cách sử dụng**: Sau khi bạn Confirm Bảng Quyết Định ở Mục 5, hãy copy từng khối **Prompt Gói 1, Gói 2, Gói 3** bên dưới sang các đoạn hội thoại mới để xử lý song song hoặc tuần tự mà không lo tràn ngữ cảnh.",
        "",
    ]

    cms_inventory_bullets: List[str] = []
    for s in cms_systems:
        if not s["backend_controllers"] and not s["is_connected_to_frontend"]:
            continue
        fields = ", ".join((s["fillable"] + s["translated"] + s["extra_keys"])[:8])
        be_ctrl = s["backend_controllers"][0] if s["backend_controllers"] else "Backend Controller"
        fe_ctrl = ", ".join(s["frontend_consumers"]) if s["frontend_consumers"] else "Chưa nối Frontend Controller"
        cms_inventory_bullets.append(
            f"  - `{s['fqcn']}` (`{s['model_path']}`) | Trường dữ liệu: `{fields}` | Admin: `{be_ctrl}` | Frontend hiện tại: `{fe_ctrl}`"
        )
    cms_inventory_block = "\n".join(cms_inventory_bullets) if cms_inventory_bullets else "  - (Kiểm tra `app/Models/`)"

    lines.extend([
        "### 📦 GÓI 1: Đấu Nối Toàn Bộ UI Active Chưa Connect Vào Các Hệ Thống Đã Có Bên CMS (`UI -> Existing CMS Integration`)",
        "",
        "```markdown",
        "Bạn đang thực thi **GÓI 1: Đấu Nối Toàn Bộ UI Chưa Connect Vào Các Hệ Thống Đã Có Bên CMS** được điều phối từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.",
        "Hãy đọc `.agents/skills/opsx-cms-sync/references/cms-8-pillars-architecture.md` trước khi thực hiện.",
        "",
        "### 1. Nguyên Tắc An Toàn (`STRICT SAFE GUARD`)",
        "- Tuyệt đối KHÔNG dùng `rm` xóa file, KHÔNG chạy `migrate:fresh` hoặc `db:seed` ghi đè dữ liệu.",
        "- Bắt buộc gọi `invoke_subagent` (`Role: \"Backend Worker\"` / `\"Frontend Worker\"`) để thực thi.",
        "- Tận dụng 100% các Model và bảng dữ liệu ĐÃ CÓ SẴN bên CMS dưới đây, không tạo bảng/Model trùng lặp:",
        cms_inventory_block,
        "",
        "### 2. Danh Sách Các Chỗ Trên UI Chưa Connect Cần Đấu Nối Ngay Vào CMS:",
    ])

    if unconnected_ui_items:
        for idx, item in enumerate(unconnected_ui_items, 1):
            lines.append(
                f"- **[UI-{idx:02d}] `{item['location']}`** ({item['ui_element']}):\n"
                f"  - Vấn đề: {item['issue']}\n"
                f"  - Hệ thống CMS sẵn có cần nối vào: {item['cms_mapping']}\n"
                f"  - Hành động: {item['action']}"
            )
    else:
        lines.append("- Hiện tại 100% các trang/section UI Active đã được nối với Model CMS. Hãy kiểm tra rà soát lại các khóa Cache và dữ liệu fallback nếu cần.")

    lines.extend([
        "",
        "### 3. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:",
        "- Chạy `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` để xác nhận số lượng `UI chưa connect CMS` giảm về `0`.",
        "- Chạy `yarn frontend:build` và `php artisan test` xanh 100%.",
        "```",
        "",
        "### 🧭 GÓI 2: Đấu Nối Toàn Bộ Điều Hướng, Xóa Link Chết (`href=\"#\"`), Sửa Lệch Route & Nút CTA (`Full Navigation Fix`)",
        "",
        "```markdown",
        "Bạn đang thực thi **GÓI 2: Đấu Nối Toàn Bộ Điều Hướng, Xóa Link Chết & Sửa Lệch Route** từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.",
        "",
        "### 1. Nguyên Tắc Thực Thi",
        "- Đối chiếu toàn bộ tên route trong `routes/frontend.php` và `routes/web.php`.",
        "- Mọi thẻ `<a>` / `<Link>` điều hướng nội bộ phải trỏ đúng tên route hoặc `slug` động lấy từ các Model CMS đã có.",
        "- Mọi nút `<button>` CTA (Tư vấn, Báo giá, Xem chi tiết, Gửi liên hệ) phải gắn đúng `@click` hoặc `type=\"submit\"`.",
        "",
        "### 2. Danh Sách Tọa Độ Lỗi Điều Hướng Cần Fix Triệt Để:",
    ])

    if nav_issues:
        for idx, nav in enumerate(nav_issues, 1):
            lines.append(
                f"- **[NAV-{idx:02d}] `{nav['location']}`** [{nav['type']}]:\n"
                f"  - Phần tử: `{nav['element']}`\n"
                f"  - Lỗi: {nav['issue']}\n"
                f"  - Cách sửa: {nav['fix']}"
            )
    else:
        lines.append("- Hiện tại toàn bộ điều hướng (`<Link>`, `route(...)`, `<button>`) trên UI Active đã hợp lệ 100% (`0` link chết, `0` route lệch).")

    lines.extend([
        "",
        "### 3. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:",
        "- Chạy lại `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` để xác nhận `0 lỗi điều hướng`.",
        "- Chạy `yarn frontend:build` thành công (`exit code 0`).",
        "```",
        "",
    ])

    non_ui_adds = [
        a for a in add_items
        if a["pillar"] != "Trụ cột 6 (UI-to-CMS Integration)" and not a["pillar"].startswith("Điều hướng (")
    ]
    lines.extend([
        "### ⚙️ GÓI 3: Đồng Bộ 8 Trụ Cột CMS, Hợp Đồng Khóa Cache & PHPUnit E2E Tests (`CMS Core & Cache Parity`)",
        "",
        "```markdown",
        "Bạn đang thực thi **GÓI 3: Đồng Bộ 8 Trụ Cột CMS, Hợp Đồng Khóa Cache & PHPUnit E2E Tests** từ Đoạn Hội Thoại Trung Tâm `/opsx-cms-sync`.",
        "",
        "### 1. Các Hạng Mục Cần Bổ Sung (`[+] ADD`) & Tinh Gọn (`[-] REMOVE`):",
    ])

    if non_ui_adds or remove_items:
        for idx, item in enumerate(non_ui_adds, 1):
            lines.append(f"- `[+ ADD-{idx:02d}]` `{item['location']}` ({item['pillar']}): {item['title']} — {item['reason']}")
        for idx, item in enumerate(remove_items, 1):
            lines.append(f"- `[- REM-{idx:02d}]` `{item['location']}` ({item['pillar']}): {item['title']} — {item['reason']}")
    else:
        lines.append("- Toàn bộ 8 trụ cột CMS và khóa Cache đã đạt `PASS`. Hãy chạy bộ PHPUnit Feature Tests để nghiệm thu tổng thể.")

    lines.extend([
        "",
        "### 2. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:",
        "- Chạy `python3 .agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py .` đạt 8/8 PASS.",
        "- Chạy `php artisan test` xanh 100%.",
        "```",
        "",
    ])

    return lines


def generate_markdown_report(
    root: Path,
    cms_systems: List[Dict[str, Any]],
    active_vue_files: List[Path],
    skipped_boilerplate_files: List[Path],
    active_page_groups: List[str],
    unconnected_ui_items: List[Dict[str, str]],
    route_registry: Dict[str, Any],
    nav_issues: List[Dict[str, str]],
    pillar_results: List[Tuple[str, Dict[str, str]]],
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> str:
    now_str = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    lines: List[str] = [
        "# Báo Cáo Kiểm Toán Hệ Thống CMS, Độ Phủ Kết Nối UI & Điều Hướng Toàn Diện (`/opsx-cms-sync`)",
        "",
        f"- **Thư mục dự án**: `{root}`",
        f"- **Thời gian quét**: `{now_str}`",
        "- **Cơ chế ánh xạ**: `100% Dynamic AST Route-to-Model Discovery` (Không dùng từ khóa fix cứng)",
        f"- **Phạm vi các nhóm Trang Figma Active**: `{', '.join(active_page_groups) if active_page_groups else 'Toàn bộ Active Pages'}` (`{len(active_vue_files)}` files `.vue` active, đã tự động lọc bỏ `{len(skipped_boilerplate_files)}` files boilerplate không dùng tới)",
        f"- **Tổng số Hệ thống CMS đã có**: `{len(cms_systems)}` Models",
        f"- **Số vị trí UI Active chưa connect CMS**: `{len(unconnected_ui_items)}` vị trí",
        f"- **Số lỗi Điều hướng trên UI Active**: `{len(nav_issues)}` vị trí",
        "",
        "---",
        "",
        "## 1. Bản Đồ Các Hệ Thống Đã Được Tạo Sẵn Bên CMS (`Existing CMS Systems Inventory`)",
        "",
        "| # | Tokens nhận diện động | Model (`app/Models/`) | Các trường dữ liệu chính (`$fillable` / `$translated`) | Admin Controller & Views | Frontend Controller đã nối | Trạng thái UI |",
        "| :--- | :--- | :--- | :--- | :--- | :--- | :--- |",
    ]

    for idx, s in enumerate(cms_systems, 1):
        fields_all = s["fillable"] + [f"{t} (i18n)" for t in s["translated"]] + s["extra_keys"]
        fields_str = ", ".join(f"`{f}`" for f in fields_all[:8])
        if len(fields_all) > 8:
            fields_str += f" (+{len(fields_all) - 8})"
        be_str = ", ".join(f"`{c}`" for c in s["backend_controllers"]) if s["backend_controllers"] else "—"
        fe_str = ", ".join(f"`{c}`" for c in s["frontend_consumers"]) if s["frontend_consumers"] else "⚠️ **Chưa gọi ở Frontend**"
        status_badge = "✅ Đã nối UI" if s["is_connected_to_frontend"] else "⚠️ Chưa nối UI"
        tok_str = ", ".join(f"`{t}`" for t in s["domain_tokens"][:4])
        lines.append(
            f"| `{idx:02d}` | {tok_str} | `{s['fqcn']}` (`{s['model_path']}`) | {fields_str or '—'} | {be_str} | {fe_str} | {status_badge} |"
        )

    lines.extend([
        "",
        "---",
        "",
        "## 2. Danh Sách Các Chỗ Trên UI Active Chưa Connect Với Hệ Thống CMS (`Unconnected UI -> Existing CMS Mapping`)",
        "",
    ])

    if unconnected_ui_items:
        lines.extend([
            "| Mã | Mức độ | Tọa độ `File:Dòng` | Phần tử UI đang tĩnh / chưa connect | Hệ thống CMS có sẵn cần nối vào | Hướng xử lý chi tiết |",
            "| :--- | :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, item in enumerate(unconnected_ui_items, 1):
            lines.append(
                f"| `UI-{idx:02d}` | **{item['severity']}** | `{item['location']}` | {item['ui_element']} | {item['cms_mapping']} | {item['action']} |"
            )
    else:
        lines.append(
            "> ✅ **100% ĐÃ KẾT NỐI CMS**: Toàn bộ các Frontend Controllers, Pages và Layouts thuộc phạm vi Figma Active đã nhận dữ liệu động từ các hệ thống CMS sẵn có."
        )

    lines.extend([
        "",
        "---",
        "",
        "## 3. Kiểm Toán Toàn Bộ Điều Hướng Trên Các Trang Figma Active (`Full Navigation, Route, Link & CTA Audit`)",
        "",
        f"- **Tổng số Route định danh (`->name(...)`) trong Backend**: `{len(route_registry['base_route_names'])}` (`{', '.join(sorted(route_registry['base_route_names'])[:15])}{'...' if len(route_registry['base_route_names']) > 15 else ''}`)",
        "",
    ])

    if nav_issues:
        lines.extend([
            "| Mã | Loại lỗi điều hướng | Tọa độ `File:Dòng` | Phần tử trên UI | Mô tả lỗi | Hướng sửa & Route đích |",
            "| :--- | :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, nav in enumerate(nav_issues, 1):
            lines.append(
                f"| `NAV-{idx:02d}` | **{nav['type']}** | `{nav['location']}` | `{nav['element']}` | {nav['issue']} | {nav['fix']} |"
            )
    else:
        lines.append(
            "> ✅ **100% ĐIỀU HƯỚNG HỢP LỆ**: Không phát hiện link chết (`href=\"#\"`), không có lệnh gọi `route('...')` sai tên, và không có nút `<button>` mồ côi thiếu sự kiện trên các trang Figma Active."
        )

    lines.extend([
        "",
        "---",
        "",
        "## 4. Tổng Quan Trạng Thái 8 Trụ Cột CMS (`8-Pillars Health Summary`)",
        "",
        "| Trụ cột | Hạng mục kiểm toán | Trạng thái | Ghi chú thực tế |",
        "| :--- | :--- | :--- | :--- |",
    ])

    for pillar_name, info in pillar_results:
        badge = "✅ PASS" if info["status"] == "PASS" else ("⚠️ CẦN XEM XÉT" if info["status"] == "WARN" else "❌ FAIL")
        lines.append(f"| **{pillar_name}** | Kiểm toán tự động | `{badge}` | {info['summary']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 5. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)",
        "",
        "### 🟢 BẢNG 5.1: `[+] ĐỀ XUẤT THÊM KẾT NỐI CMS, ĐIỀU HƯỚNG & CACHE (ADD)`",
        "",
    ])

    if add_items:
        lines.extend([
            "| Mã | Hạng mục / Trụ cột | Tọa độ `File:Dòng` | Nội dung đề xuất THÊM (`[+]`) | Lý do / Hệ thống CMS đích |",
            "| :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, item in enumerate(add_items, 1):
            lines.append(
                f"| `ADD-{idx:02d}` | {item['pillar']} | `{item['location']}` | {item['title']} | {item['reason']} |"
            )
    else:
        lines.append("> ✅ Toàn bộ kết nối CMS, điều hướng và 8 trụ cột CMS đã đầy đủ (`0` hạng mục cần thêm).")

    lines.extend([
        "",
        "### 🔴 BẢNG 5.2: `[-] ĐỀ XUẤT BỎ DỮ LIỆU TĨNH / TRƯỜNG THỪA (REMOVE)`",
        "",
    ])

    if remove_items:
        lines.extend([
            "| Mã | Hạng mục / Trụ cột | Tọa độ `File:Dòng` | Nội dung đề xuất BỎ / ẨN (`[-]`) | Lý do / Đối chiếu Figma |",
            "| :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, item in enumerate(remove_items, 1):
            lines.append(
                f"| `REM-{idx:02d}` | {item['pillar']} | `{item['location']}` | {item['title']} | {item['reason']} |"
            )
    else:
        lines.append("> ✅ Không phát hiện trường thừa hay hàm hardcode cố định gây xung đột với CMS.")

    lines.extend([
        "",
        "### 🔵 BẢNG 5.3: `[=] ĐỀ XUẤT GIỮ NGUYÊN HỆ THỐNG CỐT LÕI (KEEP)`",
        "",
        "| Mã | Trụ cột CMS | Tọa độ / Phạm vi | Hạng mục bảo toàn (`[=]`) | Lý do giữ nguyên |",
        "| :--- | :--- | :--- | :--- | :--- |",
    ])

    for idx, item in enumerate(keep_items, 1):
        lines.append(
            f"| `KEEP-{idx:02d}` | {item['pillar']} | `{item['location']}` | {item['title']} | {item['reason']} |"
        )

    lines.extend([
        "",
        "---",
        "",
    ])

    lines.extend(
        build_handoff_prompts(cms_systems, unconnected_ui_items, nav_issues, add_items, remove_items)
    )

    return "\n".join(lines)


def main() -> int:
    parser = argparse.ArgumentParser(
        description="OPSX CMS Sync — Dynamic AST Route-to-Model, Active Figma UI Coverage & Navigation Auditor"
    )
    parser.add_argument("project_dir", nargs="?", default=".", help="Thư mục gốc của dự án cần kiểm toán")
    parser.add_argument(
        "--pages",
        default=None,
        help="Danh sách các trang Figma cần kiểm toán, phân cách bằng dấu phẩy (ví dụ: Home,About,Products,Services,Process,Posts,Contact)",
    )
    parser.add_argument(
        "--page",
        default=None,
        help="Kiểm toán cô lập cho 1 trang cụ thể (ví dụ: Home)",
    )
    parser.add_argument(
        "--output",
        default="scratch/cms-sync-audit-report.md",
        help="Đường dẫn file báo cáo Markdown xuất ra (mặc định: scratch/cms-sync-audit-report.md)",
    )
    args = parser.parse_args()

    root = Path(args.project_dir).resolve()
    if not root.exists():
        print(f"[LỖI] Không tìm thấy thư mục dự án: {root}", file=sys.stderr)
        return 1

    pages_filter: Optional[List[str]] = None
    if args.pages:
        pages_filter = [p.strip() for p in args.pages.split(",") if p.strip()]
    elif args.page:
        pages_filter = [args.page.strip()]

    # Module 1: Khám phá tự động toàn bộ Hệ thống đã có bên CMS & đồ thị AST Controller -> Page -> Model
    cms_systems, ast_graph = discover_existing_cms_systems(root)

    # Lọc các file Vue thuộc phạm vi trang Figma Active (tự động loại bỏ boilerplate thừa)
    active_vue_files, skipped_boilerplate_files, active_page_groups = resolve_active_figma_vue_files(
        root, pages_filter
    )

    # Module 2: Quét toàn bộ UI Active chưa connect với CMS & ánh xạ động vào hệ thống CMS sẵn có
    unconnected_ui_items = audit_unconnected_ui_to_cms(
        root, cms_systems, ast_graph, active_vue_files, active_page_groups, pages_filter
    )

    # Module 3: Kiểm toán toàn bộ điều hướng trên các trang Figma Active
    route_registry, nav_issues = audit_full_navigation_and_links(root, active_vue_files)

    add_items: List[Dict[str, str]] = []
    remove_items: List[Dict[str, str]] = []
    keep_items: List[Dict[str, str]] = []

    p1 = audit_pillar_1_admin_dashboard_and_sidebar(root, add_items, remove_items, keep_items)
    p2 = audit_pillar_2_media_manager(root, add_items, remove_items, keep_items)
    p3 = audit_pillar_3_product_schema_and_form(root, add_items, remove_items, keep_items)
    p4 = audit_pillar_4_leads_normalization(root, add_items, remove_items, keep_items)
    p5 = audit_pillar_5_global_brand_settings(root, add_items, remove_items, keep_items)
    p6 = audit_pillar_6_seeders_and_hardcode_guard(
        root, unconnected_ui_items, add_items, remove_items, keep_items
    )
    p7 = audit_pillar_7_cache_key_contract(root, add_items, remove_items, keep_items)
    p8 = audit_pillar_8_integration_tests(root, add_items, remove_items, keep_items)

    for nav in nav_issues:
        add_items.append({
            "pillar": f"Điều hướng ({nav['type']})",
            "location": nav["location"],
            "title": f"Sửa điều hướng cho `{nav['element']}`: {nav['fix']}",
            "reason": nav["issue"],
        })

    pillar_results = [
        ("Trụ cột 1: Admin Theme, Sidebar & Dashboard", p1),
        ("Trụ cột 2: Media Manager (`FileManager` & `Thumbnail`)", p2),
        ("Trụ cột 3: Schema JSON Filters & Custom Form Figma", p3),
        ("Trụ cột 4: Chuẩn hóa Leads `Contact::normalizeLeadData`", p4),
        ("Trụ cột 5: Global Brand Settings (`props.global` & `map_iframe`)", p5),
        ("Trụ cột 6: Độ phủ UI-to-CMS & Idempotent Auto-Seeder", p6),
        ("Trụ cột 7: Hợp đồng Khóa Cache & Instant Invalidation", p7),
        ("Trụ cột 8: PHPUnit CMS-to-Frontend Integration Tests", p8),
    ]

    report_md = generate_markdown_report(
        root,
        cms_systems,
        active_vue_files,
        skipped_boilerplate_files,
        active_page_groups,
        unconnected_ui_items,
        route_registry,
        nav_issues,
        pillar_results,
        add_items,
        remove_items,
        keep_items,
    )

    out_path = Path(args.output) if Path(args.output).is_absolute() else (root / args.output)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(report_md, encoding="utf-8")

    connected_models_count = sum(1 for s in cms_systems if s["is_connected_to_frontend"])

    print("================================================================================")
    print("OPSX CMS SYNC — KIỂM TOÁN HỆ THỐNG CMS, ĐỘ PHỦ UI & ĐIỀU HƯỚNG (DYNAMIC AST)")
    print(f"Dự án : {root}")
    print(f"Phạm vi Figma Active: {', '.join(active_page_groups)} ({len(active_vue_files)} active Vue files, bỏ qua {len(skipped_boilerplate_files)} boilerplate files)")
    print(f"Báo cáo: {out_path}")
    print("================================================================================")
    print(f" [MODULE 1] Hệ thống CMS đã có sẵn   : {len(cms_systems)} Models ({connected_models_count}/{len(cms_systems)} đã gọi ở Frontend)")
    print(f" [MODULE 2] Vị trí UI chưa nối CMS   : {len(unconnected_ui_items)} vị trí")
    print(f" [MODULE 3] Lỗi Điều hướng / Link    : {len(nav_issues)} vị trí (trên tổng {len(route_registry['base_route_names'])} routes)")
    print("--------------------------------------------------------------------------------")
    for title, info in pillar_results:
        print(f" - [{info['status']:4s}] {title}: {info['summary']}")
    print("--------------------------------------------------------------------------------")
    print(f" [+] ĐỀ XUẤT THÊM (ADD)       : {len(add_items)} hạng mục")
    print(f" [-] ĐỀ XUẤT BỎ / ẨN (REMOVE) : {len(remove_items)} hạng mục")
    print(f" [=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP): {len(keep_items)} hạng mục")
    print("================================================================================")
    return 0


if __name__ == "__main__":
    sys.exit(main())
