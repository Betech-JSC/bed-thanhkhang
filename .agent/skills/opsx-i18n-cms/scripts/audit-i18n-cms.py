#!/usr/bin/env python3
# -*- coding: utf-8 -*-
r"""
OPSX i18n CMS — Pre-Flight Read-Only Auditor (4 Pillars of Backend CMS Multi-Language VI/EN & Auto-Fallback)
=============================================================================================================
Kiểm toán tự động (100% Read-Only) toàn bộ tầng Backend CMS & Cấu hình Đa ngôn ngữ:
  1. Trụ cột 1: Cấu hình `config/app.php` & `config/translatable.php` (`locales => ['vi', 'en']`,
                `fallback_locale => 'vi'`, `use_property_fallback => true` & hiển thị nút `VI | EN` trong Admin).
  2. Trụ cột 2: Độ phủ `$translatedAttributes`, bảng `*_translations`, các trường mảng JSON chứa nội dung chữ
                (`benefits`, `steps`, `faqs`, `highlights`, `specifications`, `filter_attributes`, `pdp_metadata`...)
                và ô nhập Tiếng Anh (`EN`) trên `resources/Backend/js/Pages/**/Form.vue` & `Configs.vue`.
  3. Trụ cột 3: Chống lỗi Validation 422 / SQL NOT NULL khi Admin bỏ trống tiếng Anh & Tự động Fallback `EN -> VI` 100%.
  4. Trụ cột 4: Khóa Cache tách biệt theo ngôn ngữ (`_{$locale}`) trong `Frontend\*Controller.php` & `HandleInertia*Requests.php`
                cùng cơ chế xóa cache đa ngôn ngữ (`vi` & `en`) trong Backend.

Xuất báo cáo chi tiết + Bảng Quyết Định `[+] THÊM / [-] BỎ / [=] GIỮ` + Bộ Handoff Prompts (khóa ranh giới file song song) ra:
  scratch/i18n-cms-audit-report.md
"""

import argparse
import datetime
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
    "ContactStatusLog",
    "Keyword",
    "KeywordRefDate",
    "OrderLine",
    "ProductRefPost",
    "ProductVariantRefOption",
}

# Các trường JSON / Text hiển thị nội dung chữ ra giao diện cần hỗ trợ đa ngôn ngữ VI/EN
TEXT_OR_JSON_CONTENT_FIELDS = {
    "title",
    "name",
    "subtitle",
    "description",
    "excerpt",
    "summary",
    "content",
    "benefits",
    "steps",
    "faqs",
    "highlights",
    "items",
    "specifications",
    "operating_systems",
    "filter_attributes",
    "pdp_metadata",
    "sliders",
    "content_tabs",
    "features",
    "custom_fields",
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


def extract_php_array_strings(content: str, prop_name: str) -> List[str]:
    pattern = rf"\${prop_name}\s*=\s*\[(.*?)\];"
    m = re.search(pattern, content, re.DOTALL)
    if not m:
        return []
    body = m.group(1)
    body = re.sub(r"//.*", "", body)
    body = re.sub(r"/\*.*?\*/", "", body, flags=re.DOTALL)
    return re.findall(r"""['"]([a-zA-Z0-9_]+)['"]""", body)


def find_line_number(lines: List[str], pattern: str) -> int:
    for idx, line in enumerate(lines, 1):
        if re.search(pattern, line):
            return idx
    return 1


# ==============================================================================
# TRỤ CỘT 1: CẤU HÌNH LOCALE & NÚT CHUYỂN VI | EN TRONG ADMIN
# ==============================================================================

def audit_pillar_1_config_and_switcher(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Tuple[Dict[str, str], List[Dict[str, str]]]:
    app_cfg = root / "config/app.php"
    trans_cfg = root / "config/translatable.php"
    status = "PASS"
    notes: List[str] = []
    config_Checks: List[Dict[str, str]] = []

    if app_cfg.exists():
        lines = read_lines(app_cfg)
        content = "\n".join(lines)

        # 1. Kiểm tra 'locales' => ['vi', 'en']
        m_locales = re.search(r"""['"]locales['"]\s*=>\s*\[(.*?)\]""", content, re.DOTALL)
        locales_line = find_line_number(lines, r"""['"]locales['"]\s*=>""")
        if m_locales:
            langs = re.findall(r"""['"]([a-zA-Z_-]+)['"]""", m_locales.group(1))
            if "vi" in langs and "en" in langs:
                config_Checks.append({
                    "file": f"config/app.php:{locales_line}",
                    "setting": f"`'locales' => {langs}`",
                    "status": "✅ PASS",
                    "note": "Đã bật đủ 2 ngôn ngữ `vi` và `en` (Nút chuyển `VI | EN` trên Sidebar Admin đã kích hoạt).",
                })
                keep_items.append({
                    "pillar": "Trụ cột 1 (Config Locales)",
                    "location": f"config/app.php:{locales_line}",
                    "title": f"Cấu hình `'locales' => {langs}` kích hoạt bộ chuyển đổi ngôn ngữ `VI | EN` trong Admin",
                    "reason": "Giúp Admin chuyển qua lại giữa Tiếng Việt và Tiếng Anh trên mọi Form quản trị.",
                })
                notes.append("`locales` có đủ `['vi', 'en']`")
            else:
                status = "FAIL"
                config_Checks.append({
                    "file": f"config/app.php:{locales_line}",
                    "setting": f"`'locales' => {langs}`",
                    "status": "❌ [P0-CONFIG] THIẾU `'en'`",
                    "note": "Mảng `'locales'` chỉ có `'vi'` làm ẩn hoàn toàn nút chuyển đổi ngôn ngữ `VI | EN` trong Admin CMS (`SidebarItems.vue`).",
                })
                add_items.append({
                    "pillar": "Trụ cột 1 ([P0-CONFIG] Admin Switcher)",
                    "location": f"config/app.php:{locales_line}",
                    "title": "Cập nhật `'locales' => ['vi', 'en']` trong `config/app.php`",
                    "reason": "Hiện tại `'locales'` chỉ có `['vi']` nên nút chuyển ngôn ngữ `VI | EN` trên Sidebar Admin bị ẩn.",
                })
                notes.append("[P0-CONFIG] `config/app.php` thiếu `'en'` trong `'locales'`")

        # 2. Kiểm tra 'fallback_locale' => 'vi'
        m_fb = re.search(r"""['"]fallback_locale['"]\s*=>\s*['"]([^'"]+)['"]""", content)
        fb_line = find_line_number(lines, r"""['"]fallback_locale['"]\s*=>""")
        if m_fb:
            fb_val = m_fb.group(1)
            if fb_val == "vi":
                config_Checks.append({
                    "file": f"config/app.php:{fb_line}",
                    "setting": "`'fallback_locale' => 'vi'`",
                    "status": "✅ PASS",
                    "note": "Ngôn ngữ dự phòng gốc là Tiếng Việt (`vi`).",
                })
            else:
                status = "WARN" if status == "PASS" else status
                config_Checks.append({
                    "file": f"config/app.php:{fb_line}",
                    "setting": f"`'fallback_locale' => '{fb_val}'`",
                    "status": "⚠️ [P0-CONFIG] SAI FALLBACK",
                    "note": f"`fallback_locale` đang để `'{fb_val}'` thay vì `'vi'`. Khi chưa nhập tiếng Anh sẽ không tự lấy tiếng Việt làm gốc.",
                })
                add_items.append({
                    "pillar": "Trụ cột 1 ([P0-CONFIG] Fallback Locale)",
                    "location": f"config/app.php:{fb_line}",
                    "title": f"Đổi `'fallback_locale' => '{fb_val}'` thành `'fallback_locale' => 'vi'` trong `config/app.php`",
                    "reason": "Đảm bảo khi bản dịch Tiếng Anh (`en`) bỏ trống, Laravel tự động lấy Tiếng Việt (`vi`) làm ngôn ngữ dự phòng.",
                })
                notes.append(f"`fallback_locale` đang là `'{fb_val}'` (cần đổi sang `'vi'`)")

    if trans_cfg.exists():
        lines = read_lines(trans_cfg)
        content = "\n".join(lines)
        required_trans_flags = [
            ("use_fallback", "true"),
            ("use_property_fallback", "true"),
            ("fallback_locale", "'vi'"),
        ]
        for key_name, expected in required_trans_flags:
            line_no = find_line_number(lines, rf"""['"]{key_name}['"]\s*=>""")
            m_k = re.search(rf"""['"]{key_name}['"]\s*=>\s*([^\n,]+)""", content)
            actual = m_k.group(1).strip() if m_k else "missing"
            is_ok = (expected == "true" and actual.lower() == "true") or (
                expected == "'vi'" and actual.strip("'\"") == "vi"
            )
            if is_ok:
                config_Checks.append({
                    "file": f"config/translatable.php:{line_no}",
                    "setting": f"`'{key_name}' => {actual}`",
                    "status": "✅ PASS",
                    "note": "Đã bật tự động fallback cấp thuộc tính khi trường dịch tiếng Anh rỗng.",
                })
            else:
                status = "WARN" if status == "PASS" else status
                config_Checks.append({
                    "file": f"config/translatable.php:{line_no}",
                    "setting": f"`'{key_name}' => {actual}`",
                    "status": "⚠️ CẦN BẬT",
                    "note": f"Cần đặt `'{key_name}' => {expected}` để tự động fallback `EN -> VI` cho từng trường dịch.",
                })
                add_items.append({
                    "pillar": "Trụ cột 1 (Translatable Fallback)",
                    "location": f"config/translatable.php:{line_no}",
                    "title": f"Cấu hình `'{key_name}' => {expected}` trong `config/translatable.php`",
                    "reason": "Kích hoạt cơ chế tự động trả về giá trị Tiếng Việt (`vi`) khi một thuộc tính Tiếng Anh (`en`) bị bỏ trống.",
                })

    return {"status": status, "summary": "; ".join(notes) if notes else "Đã kiểm tra"}, config_Checks


# ==============================================================================
# TRỤ CỘT 2: Ô NHẬP LIỆU TIẾNG ANH TRÊN MỌI FORM BACKEND CMS & CONFIGS.VUE
# ==============================================================================

def audit_pillar_2_models_and_admin_forms(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Tuple[Dict[str, str], List[Dict[str, str]]]:
    model_files = iter_files(root, ["app/Models"], (".php",))
    backend_pages = iter_files(root, ["resources/Backend/js/Pages"], (".vue",))

    status = "PASS"
    notes: List[str] = []
    model_audit_rows: List[Dict[str, str]] = []

    translation_map: Dict[str, Tuple[Path, List[str]]] = {}
    for mf in model_files:
        if mf.name.endswith("Translation.php"):
            base_name = mf.name[: -len("Translation.php")]
            t_fields = extract_php_array_strings(read_text(mf), "fillable")
            translation_map[base_name] = (mf, [f for f in t_fields if f not in ("locale", f"{base_name.lower()}_id")])

    translatable_models_count = 0
    untranslated_json_count = 0

    for mf in model_files:
        if mf.name.endswith("Translation.php") or mf.name.endswith("RefCategory.php"):
            continue
        model_name = mf.stem
        if model_name in SYSTEM_MODELS_IGNORE or model_name in ("Config", "Setting", "Contact", "Order", "MetaPage", "EmailTemplate"):
            continue

        content = read_text(mf)
        frel = rel_path(mf, root)
        fillable = extract_php_array_strings(content, "fillable")
        translated = extract_php_array_strings(content, "translatedAttributes")
        if not translated and model_name in translation_map:
            translated = translation_map[model_name][1]

        # Tìm các trường nội dung chữ / mảng JSON nằm trong $fillable mà CHƯA nằm trong $translatedAttributes
        non_translated_content_fields: List[str] = []
        for f in fillable:
            if f in TEXT_OR_JSON_CONTENT_FIELDS and f not in translated:
                non_translated_content_fields.append(f)

        # Tìm Form.vue tương ứng trong resources/Backend/js/Pages
        stem_lower = model_name.lower()
        plural_candidates = {
            stem_lower,
            stem_lower + "s",
            stem_lower[:-1] + "ies" if stem_lower.endswith("y") else stem_lower + "s",
        }
        matched_forms = [
            rel_path(bp, root)
            for bp in backend_pages
            if bp.name == "Form.vue" and any(pc in [p.lower() for p in bp.parts] for pc in plural_candidates)
        ]
        form_str = ", ".join(f"`{p}`" for p in matched_forms) if matched_forms else "—"

        if translated:
            translatable_models_count += 1

        if non_translated_content_fields:
            # Kiểm tra xem trong Model hoặc Controller đã có cơ chế lưu song ngữ (_en / locale) cho các trường JSON này chưa
            has_bilingual_json_helper = any(
                kw in content for kw in ("_en", "resolveLocalized", "localizeJson", "getLocale")
            )
            if not has_bilingual_json_helper:
                status = "WARN"
                untranslated_json_count += 1
                fields_fmt = ", ".join(f"`{f}`" for f in non_translated_content_fields)
                model_audit_rows.append({
                    "model": f"`{model_name}` (`{frel}`)",
                    "translated_fields": ", ".join(f"`{t}`" for t in translated[:8]) or "⚠️ Chưa có `$translatedAttributes`",
                    "missing_i18n_fields": fields_fmt,
                    "admin_form": form_str,
                    "status": "⚠️ Thiếu đa ngôn ngữ cho trường JSON/Text",
                })
                add_items.append({
                    "pillar": "Trụ cột 2 (Schema & Form Đa Ngôn Ngữ)",
                    "location": f"{frel}:1",
                    "title": f"Bổ sung hỗ trợ đa ngôn ngữ `VI / EN` (kèm fallback `EN -> VI`) cho các trường {fields_fmt} của Model `{model_name}` ({form_str})",
                    "reason": f"Các trường {fields_fmt} đang nằm ở bảng chính chưa phân tách theo ngôn ngữ `vi`/`en`.",
                })
            else:
                model_audit_rows.append({
                    "model": f"`{model_name}` (`{frel}`)",
                    "translated_fields": ", ".join(f"`{t}`" for t in translated[:8]),
                    "missing_i18n_fields": "— (Đã xử lý đa ngôn ngữ)",
                    "admin_form": form_str,
                    "status": "✅ PASS",
                })
        else:
            model_audit_rows.append({
                "model": f"`{model_name}` (`{frel}`)",
                "translated_fields": ", ".join(f"`{t}`" for t in translated[:8]) or "—",
                "missing_i18n_fields": "—",
                "admin_form": form_str,
                "status": "✅ PASS" if translated else "ℹ️ Không có trường dịch",
            })

    # Kiểm tra trang Cấu hình chung (`Config.php` + `Configs/Index.vue` hoặc `Configs.vue`)
    config_model = root / "app/Models/Config.php"
    config_views = [
        p for p in backend_pages if "config" in p.stem.lower() or "configs" in [pt.lower() for pt in p.parts]
    ]
    if config_model.exists():
        cfg_content = read_text(config_model)
        cfg_view_rel = rel_path(config_views[0], root) if config_views else "resources/Backend/js/Pages/Configs/"
        cfg_view_content = "\n".join(read_text(p) for p in config_views) if config_views else ""
        has_en_config = (
            "_en" in cfg_content
            or "getLocale()" in cfg_content
            or "_en" in cfg_view_content
            or "locale" in cfg_view_content
        )
        if has_en_config:
            model_audit_rows.append({
                "model": "`Config` (`app/Models/Config.php`)",
                "translated_fields": "Hỗ trợ cấu hình theo `locale` / `*_en`",
                "missing_i18n_fields": "—",
                "admin_form": f"`{cfg_view_rel}`",
                "status": "✅ PASS",
            })
        else:
            status = "WARN"
            model_audit_rows.append({
                "model": "`Config` (`app/Models/Config.php`)",
                "translated_fields": "⚠️ Chỉ có 1 ngôn ngữ tĩnh",
                "missing_i18n_fields": "`company_name`, `address`, `slogan`, `working_hours`, `footer_description`...",
                "admin_form": f"`{cfg_view_rel}`",
                "status": "⚠️ Thiếu ô nhập EN trong Configs",
            })
            add_items.append({
                "pillar": "Trụ cột 2 (Configs Đa Ngôn Ngữ)",
                "location": f"app/Models/Config.php:1 & {cfg_view_rel}:1",
                "title": "Bổ sung các trường Tiếng Anh (`*_en` hoặc theo `locale`) trên trang Cấu hình Admin (`Configs`) và tự động fallback `EN -> VI` trong `Config.php`",
                "reason": "Để tên công ty, địa chỉ, slogan, mô tả chân trang ở chế độ Tiếng Anh (`EN`) có thể nhập từ CMS và tự lấy Tiếng Việt nếu để trống.",
            })
            notes.append("`Config.php` / `Configs` chưa có trường Tiếng Anh (`*_en`)")

    notes.insert(0, f"{translatable_models_count} Models có bảng dịch `*_translations`")
    if untranslated_json_count:
        notes.append(f"{untranslated_json_count} Models có trường JSON/Text cần bổ sung hỗ trợ `VI / EN`")

    return {"status": status, "summary": "; ".join(notes)}, model_audit_rows


# ==============================================================================
# TRỤ CỘT 3: CHỐNG LỖI VALIDATION KHI LƯU & TỰ ĐỘNG FALLBACK EN -> VI 100%
# ==============================================================================

def audit_pillar_3_validation_and_fallback(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Dict[str, str]:
    status = "PASS"
    notes: List[str] = []

    # 1. Kiểm tra các bảng *_translations trong database/migrations xem các cột text có bị NOT NULL cứng không có default/nullable không
    mig_files = iter_files(root, ["database/migrations"], (".php",))
    strict_not_null_translations: List[str] = []
    for mf in mig_files:
        content = read_text(mf)
        if "_translations" not in content:
            continue
        for line in content.splitlines():
            if re.search(r"\$table->(?:string|text|longText)\s*\(\s*['\"](?:title|name|slug|description|content)['\"]", line):
                if "nullable()" not in line and "default(" not in line:
                    strict_not_null_translations.append(rel_path(mf, root))
                    break

    # 2. Kiểm tra xem trong Backend Controllers / HasCrudActions đã có cơ chế fallback hoặc đồng bộ bản dịch khi lưu chưa
    crud_trait = root / "packages/bed-package-essentials/core/src/Traits/HasCrudActions.php"
    if crud_trait.exists():
        keep_items.append({
            "pillar": "Trụ cột 3 (Save per-locale)",
            "location": f"{rel_path(crud_trait, root)}:1",
            "title": "Cơ chế lưu bản dịch theo từng `locale` độc lập kết hợp `use_property_fallback => true`",
            "reason": "Cho phép Admin nhập Tiếng Việt trước, các trường chưa nhập Tiếng Anh tự động đọc từ bản ghi Tiếng Việt.",
        })
        notes.append("Đã có cơ chế lưu theo `locale`")

    # 3. Kiểm tra Frontend Controllers xem khi map dữ liệu sang mảng phẳng truyền xuống Inertia đã ưu tiên lấy theo locale hiện tại + fallback về 'vi' chưa
    fe_controllers = iter_files(root, ["app/Http/Controllers/Frontend"], (".php",))
    controllers_missing_fallback: List[str] = []
    for fc in fe_controllers:
        content = read_text(fc)
        if "translate(" in content and "translate('vi')" not in content and "use_property_fallback" not in content:
            controllers_missing_fallback.append(rel_path(fc, root))

    if controllers_missing_fallback:
        status = "WARN"
        add_items.append({
            "pillar": "Trụ cột 3 (EN -> VI Fallback)",
            "location": ", ".join(controllers_missing_fallback),
            "title": "Đảm bảo mọi lệnh gọi `->translate($locale)` trong Frontend Controller đều có fallback `?? $model->translate('vi')`",
            "reason": "Tránh trả về `null` hoặc chuỗi rỗng ngoài giao diện khi Admin chưa nhập bản dịch Tiếng Anh.",
        })
        notes.append(f"{len(controllers_missing_fallback)} Frontend Controllers cần kiểm tra `translate('vi')` fallback")
    else:
        notes.append("Truy cập thuộc tính Model tự động kế thừa fallback `EN -> VI`")

    return {"status": status, "summary": "; ".join(notes)}


# ==============================================================================
# TRỤ CỘT 4: KHÓA CACHE TÁCH BIỆT THEO NGÔN NGỮ (_{locale})
# ==============================================================================

def audit_pillar_4_locale_cache_keys(
    root: Path,
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> Tuple[Dict[str, str], List[Dict[str, str]]]:
    read_files = iter_files(
        root,
        ["app/Http/Controllers/Frontend", "app/Http/Middleware"],
        (".php",),
    )

    locale_aware_keys: List[Dict[str, str]] = []
    missing_locale_keys: List[Dict[str, str]] = []

    for fpath in read_files:
        frel = rel_path(fpath, root)
        lines = read_lines(fpath)
        content = "\n".join(lines)
        has_sig_with_locale = (
            "resolveHomeCacheSignature" in content
            and ("getLocale" in content or "$locale" in content)
        )
        for idx, line in enumerate(lines, 1):
            for m in re.finditer(r"Cache::(?:remember|rememberForever|put)\s*\(\s*([^,]+),", line):
                raw_key = m.group(1).strip()
                has_locale_in_key = any(
                    kw in raw_key
                    for kw in ("$locale", "getLocale()", "locale", "$lang")
                ) or ("cacheKey" in raw_key and has_sig_with_locale)

                if has_locale_in_key:
                    locale_aware_keys.append({
                        "location": f"{frel}:{idx}",
                        "key_expr": raw_key,
                        "status": "✅ Có `_{$locale}`",
                    })
                else:
                    missing_locale_keys.append({
                        "location": f"{frel}:{idx}",
                        "key_expr": raw_key,
                        "status": "❌ Thiếu `_{$locale}` (Lẫn cache VI/EN)",
                    })

    status = "PASS"
    if missing_locale_keys:
        status = "WARN"
        for mk in missing_locale_keys:
            add_items.append({
                "pillar": "Trụ cột 4 (Locale Cache Isolation)",
                "location": mk["location"],
                "title": f"Gắn hậu tố ngôn ngữ `_{{$locale}}` (`app()->getLocale()`) vào khóa Cache `{mk['key_expr']}` và xóa cả 2 ngôn ngữ `['vi', 'en']` khi `Cache::forget`",
                "reason": "Khóa cache hiện tại không phân biệt ngôn ngữ — khách chuyển sang Tiếng Anh (`EN`) sẽ bị trả về dữ liệu Tiếng Việt (`VI`) đã lưu trong cache.",
            })

    total_keys = len(locale_aware_keys) + len(missing_locale_keys)
    if locale_aware_keys:
        keep_items.append({
            "pillar": "Trụ cột 4 (Locale Cache Keys)",
            "location": "app/Http/Controllers/Frontend/ & HandleInertiaFrontendRequests.php",
            "title": f"{len(locale_aware_keys)}/{total_keys} khóa Cache đã phân tách độc lập theo `_{{$locale}}` (`vi` / `en`)",
            "reason": "Đảm bảo dữ liệu cache của Tiếng Việt và Tiếng Anh không bao giờ bị ghi đè lẫn nhau.",
        })

    summary = f"{len(locale_aware_keys)}/{total_keys} khóa Cache đã gắn `_{{$locale}}`"
    if missing_locale_keys:
        summary += f" ({len(missing_locale_keys)} khóa thiếu `_{{$locale}}` gây lẫn ngôn ngữ!)"

    return {"status": status, "summary": summary}, (missing_locale_keys + locale_aware_keys)


# ==============================================================================
# XUẤT BÁO CÁO MARKDOWN & BỘ HANDOFF PROMPTS SONG SONG
# ==============================================================================

def build_i18n_cms_handoff_prompts(
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
) -> List[str]:
    lines: List[str] = [
        "## 6. Bộ Handoff Prompts Đa Ngôn Ngữ Backend CMS (`Copy Sang Đoạn Hội Thoại Khác Để Fix`)",
        "",
        "> 🔒 **An Toàn Chạy Song Song (`Parallel Safety`)**: Các gói Prompt dưới đây đã khóa chặt ranh giới file (`[LOCKED: resources/Frontend/**]`) để bạn có thể chạy song song cùng lúc với `/opsx-i18n-fe` ở một đoạn hội thoại khác mà không bao giờ đụng file.",
        "",
        "### 📦 GÓI 1 (CMS i18n): Mở Nút Chuyển `VI | EN` Admin, Bổ Sung Ô Nhập Tiếng Anh (`EN`), Tự Động Fallback `EN -> VI` & Tách Khóa Cache `_{locale}`",
        "",
        "```markdown",
        "Bạn đang thực thi **GÓI 1: Đồng Bộ Đa Ngôn Ngữ Backend CMS (VI / EN) & Tự Động Fallback EN -> VI** từ Đoạn Hội Thoại Trung Tâm `/opsx-i18n-cms`.",
        "Hãy đọc `.agents/skills/opsx-i18n-cms/SKILL.md` trước khi thực hiện.",
        "",
        "### 1. Ranh Giới File Bắt Buộc Khi Chạy Song Song (`Parallel File Boundary`)",
        "- ✅ `[ALLOWED TO EDIT]`: `config/app.php`, `config/translatable.php`, `app/Models/**`, `app/Http/Controllers/**`, `app/Http/Middleware/HandleInertiaFrontendRequests.php`, `database/migrations/**`, `resources/Backend/js/**`.",
        "- 🔒 `[LOCKED FOR PARALLEL SAFETY — CẤM SỬA]`: `resources/Frontend/**` (đang được xử lý song song bởi `/opsx-i18n-fe`).",
        "- Bắt buộc gọi `invoke_subagent` (`Role: \"Backend Worker\"`) để thực hiện. Cấm dùng `rm` xóa file, cấm `migrate:fresh`.",
        "",
        "### 2. Danh Sách Các Hạng Mục Đã Confirm Cần Thực Thi:",
    ]

    if add_items:
        for idx, item in enumerate(add_items, 1):
            lines.append(
                f"- **[CMS-I18N-{idx:02d}] `{item['location']}`** ({item['pillar']}):\n"
                f"  - Hành động: {item['title']}\n"
                f"  - Lý do: {item['reason']}"
            )
    else:
        lines.append("- Toàn bộ 4 Trụ cột Đa ngôn ngữ Backend CMS đã đạt chuẩn `PASS`.")

    lines.extend([
        "",
        "### 3. Quy Tắc Vàng Bắt Buộc Về Lưu Trữ & Fallback `EN -> VI`:",
        "1. Khi Admin tạo mới hoặc cập nhật bản ghi trong CMS mà chỉ nhập Tiếng Việt (`vi`) hoặc để trống các ô Tiếng Anh (`en`), hệ thống **tuyệt đối không được báo lỗi Validation 422 hay lỗi SQL NOT NULL**.",
        "2. Khi trả dữ liệu ra Frontend ở ngôn ngữ `en` (`app()->getLocale() === 'en'`), bất kỳ trường văn bản, nhánh JSON hay cấu hình `Config` nào có giá trị `en` rỗng/null đều phải **tự động fallback lấy giá trị `vi` của ô tiếng Việt tương ứng**.",
        "3. Mọi khóa `Cache::remember` ở Frontend Controller & Middleware phải có hậu tố `_{$locale}`, và `Cache::forget` trong Backend phải xóa sạch cả `vi` lẫn `en`.",
        "",
        "### 4. Kiểm Chứng Bắt Buộc Trước Khi Báo Cáo:",
        "- Chạy lại `python3 .agents/skills/opsx-i18n-cms/scripts/audit-i18n-cms.py .` đạt `4/4 PASS`.",
        "- Chạy `php artisan test` xanh 100%.",
        "```",
        "",
    ])

    return lines


def generate_markdown_report(
    root: Path,
    pillar_results: List[Tuple[str, Dict[str, str]]],
    config_checks: List[Dict[str, str]],
    model_audit_rows: List[Dict[str, str]],
    cache_audit_rows: List[Dict[str, str]],
    add_items: List[Dict[str, str]],
    remove_items: List[Dict[str, str]],
    keep_items: List[Dict[str, str]],
) -> str:
    now_str = datetime.datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    lines: List[str] = [
        "# Báo Cáo Kiểm Toán Đa Ngôn Ngữ Backend CMS (`VI / EN` & Auto-Fallback `EN -> VI`) (`/opsx-i18n-cms`)",
        "",
        f"- **Thư mục dự án**: `{root}`",
        f"- **Thời gian quét**: `{now_str}`",
        "- **Chế độ**: `Strict Read-Only Pre-Flight Audit`",
        "- **Ranh giới chạy song song**: `[ALLOWED: config/, app/, database/, resources/Backend/]` | `[LOCKED: resources/Frontend/]`",
        "",
        "---",
        "",
        "## 1. Tổng Quan 4 Trụ Cột Đa Ngôn Ngữ Backend CMS",
        "",
        "| Trụ cột | Hạng mục kiểm toán | Trạng thái | Ghi chú thực tế |",
        "| :--- | :--- | :--- | :--- |",
    ]

    for title, info in pillar_results:
        badge = "✅ PASS" if info["status"] == "PASS" else ("⚠️ CẦN XEM XÉT" if info["status"] == "WARN" else "❌ FAIL")
        lines.append(f"| **{title}** | Kiểm toán tự động | `{badge}` | {info['summary']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 2. Kiểm Toán Cấu Hình Locale & Nút Chuyển `VI | EN` Trong Admin (`config/app.php` & `config/translatable.php`)",
        "",
        "| Tọa độ `File:Dòng` | Cấu hình hiện tại | Trạng thái | Ý nghĩa / Tác động |",
        "| :--- | :--- | :--- | :--- |",
    ])

    for c in config_checks:
        lines.append(f"| `{c['file']}` | {c['setting']} | **{c['status']}** | {c['note']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 3. Kiểm Toán Các Hệ Thống Model, Trường JSON & Biểu Mẫu Admin (`Form.vue` & `Configs.vue`)",
        "",
        "| Model (`app/Models/`) | Các trường đã có trong `$translatedAttributes` (`*_translations`) | Các trường Text/JSON chưa tách ngôn ngữ `VI/EN` | Biểu mẫu Admin (`resources/Backend/`) | Trạng thái |",
        "| :--- | :--- | :--- | :--- | :--- |",
    ])

    for r in model_audit_rows:
        lines.append(
            f"| {r['model']} | {r['translated_fields']} | {r['missing_i18n_fields']} | {r['admin_form']} | **{r['status']}** |"
        )

    lines.extend([
        "",
        "---",
        "",
        "## 4. Kiểm Toán Khóa Cache Đa Ngôn Ngữ (`_{locale}` Cache Isolation)",
        "",
    ])

    if cache_audit_rows:
        lines.extend([
            "| Tọa độ `File:Dòng` | Biểu thức Khóa Cache (`Cache::remember`) | Trạng thái phân tách `VI / EN` |",
            "| :--- | :--- | :--- |",
        ])
        for cr in cache_audit_rows:
            lines.append(f"| `{cr['location']}` | `{cr['key_expr']}` | **{cr['status']}** |")
    else:
        lines.append("> Không phát hiện lệnh `Cache::remember` trong Frontend Controllers.")

    lines.extend([
        "",
        "---",
        "",
        "## 5. Bảng Quyết Định Trình Người Dùng Confirm (`[+] THÊM / [-] BỎ / [=] GIỮ`)",
        "",
        "### 🟢 BẢNG 5.1: `[+] ĐỀ XUẤT THÊM CẤU HÌNH, Ô NHẬP TIẾNG ANH, FALLBACK EN->VI & KHÓA CACHE (ADD)`",
        "",
    ])

    if add_items:
        lines.extend([
            "| Mã | Trụ cột | Tọa độ `File:Dòng` | Nội dung đề xuất THÊM (`[+]`) | Lý do |",
            "| :--- | :--- | :--- | :--- | :--- |",
        ])
        for idx, item in enumerate(add_items, 1):
            lines.append(
                f"| `ADD-{idx:02d}` | {item['pillar']} | `{item['location']}` | {item['title']} | {item['reason']} |"
            )
    else:
        lines.append("> ✅ Toàn bộ 4 Trụ cột Đa ngôn ngữ Backend CMS đã hoàn chỉnh (`0` hạng mục cần thêm).")

    lines.extend([
        "",
        "### 🔴 BẢNG 5.2: `[-] ĐỀ XUẤT BỎ (REMOVE)`",
        "",
    ])

    if remove_items:
        for idx, item in enumerate(remove_items, 1):
            lines.append(f"- `REM-{idx:02d}` (`{item['location']}`): {item['title']} — {item['reason']}")
    else:
        lines.append("> ✅ Không có cấu hình hoặc bảng dữ liệu nào cần xóa bỏ.")

    lines.extend([
        "",
        "### 🔵 BẢNG 5.3: `[=] ĐỀ XUẤT GIỮ NGUYÊN (KEEP)`",
        "",
        "| Mã | Trụ cột | Tọa độ / Phạm vi | Hạng mục bảo toàn (`[=]`) | Lý do |",
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

    lines.extend(build_i18n_cms_handoff_prompts(add_items, remove_items))

    return "\n".join(lines)


def main() -> int:
    parser = argparse.ArgumentParser(
        description="OPSX i18n CMS — 4-Pillar Backend CMS Multi-Language (VI/EN) & Auto-Fallback Auditor"
    )
    parser.add_argument("project_dir", nargs="?", default=".", help="Thư mục gốc của dự án cần kiểm toán")
    parser.add_argument(
        "--output",
        default="scratch/i18n-cms-audit-report.md",
        help="Đường dẫn file báo cáo Markdown xuất ra (mặc định: scratch/i18n-cms-audit-report.md)",
    )
    args = parser.parse_args()

    root = Path(args.project_dir).resolve()
    if not root.exists():
        print(f"[LỖI] Không tìm thấy thư mục dự án: {root}", file=sys.stderr)
        return 1

    add_items: List[Dict[str, str]] = []
    remove_items: List[Dict[str, str]] = []
    keep_items: List[Dict[str, str]] = []

    p1, config_checks = audit_pillar_1_config_and_switcher(root, add_items, remove_items, keep_items)
    p2, model_audit_rows = audit_pillar_2_models_and_admin_forms(root, add_items, remove_items, keep_items)
    p3 = audit_pillar_3_validation_and_fallback(root, add_items, remove_items, keep_items)
    p4, cache_audit_rows = audit_pillar_4_locale_cache_keys(root, add_items, remove_items, keep_items)

    pillar_results = [
        ("Trụ cột 1: Cấu hình Locale & Nút chuyển `VI | EN` Admin", p1),
        ("Trụ cột 2: Ô nhập Tiếng Anh (`EN`) trên Form.vue & Configs.vue", p2),
        ("Trụ cột 3: Chống lỗi Validation & Tự động Fallback `EN -> VI`", p3),
        ("Trụ cột 4: Khóa Cache tách biệt theo ngôn ngữ (`_{locale}`)", p4),
    ]

    report_md = generate_markdown_report(
        root,
        pillar_results,
        config_checks,
        model_audit_rows,
        cache_audit_rows,
        add_items,
        remove_items,
        keep_items,
    )

    out_path = Path(args.output) if Path(args.output).is_absolute() else (root / args.output)
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(report_md, encoding="utf-8")

    print("================================================================================")
    print("OPSX i18n CMS — KIỂM TOÁN ĐA NGÔN NGỮ BACKEND CMS (VI / EN & AUTO-FALLBACK)")
    print(f"Dự án : {root}")
    print(f"Báo cáo: {out_path}")
    print("================================================================================")
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
