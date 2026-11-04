#!/usr/bin/env python3
"""
OPSX Audit — CMS Hardcoded & Fixed Return Values Scanner
Quét tự động toàn bộ mã nguồn CMS (Backend PHP/Laravel & Frontend Vue/React/Next/Blade)
để phát hiện các vị trí đang hardcode và return giá trị cố định (mock arrays, fake metrics,
fallback data giả lập, thông tin liên hệ/setting cứng) cần được liệt kê và remove.

100% Read-Only (Không sửa đổi hay xóa bất kỳ file nào).
"""

import argparse
import json
import re
import sys
from pathlib import Path
from typing import Any, Dict, List

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
    "build",
    "coverage",
    "graphify-out",
    "scratch",
    ".agent",
    ".agents",
    ".claude",
    "openspec",
    "tests",
    "database/migrations",
    "database/seeders",
    "database/factories",
}

PHP_SCAN_DIRS = ["app", "routes", "Modules", "packages"]
FE_SCAN_DIRS = ["resources", "src"]

PHP_RECORD_KEYS_RE = re.compile(
    r"""['"](?:id|title|name|slug|image|thumbnail|banner|avatar|price|old_price|total|count|views|rating|author|excerpt|description|content|question|answer|phone|hotline|email|address|facebook|zalo|youtube|tiktok|eyebrow|headline|items|solutions|products|posts)['"]\s*=>""",
    re.IGNORECASE,
)

PHP_FIXED_RETURN_RE = re.compile(
    r"""return\s+(?:\[\s*\[|collect\(\s*\[)""",
    re.IGNORECASE,
)

PHP_SCALAR_RETURN_RE = re.compile(
    r"""return\s+(?:['"][^'"]{15,}['"]|\d{2,})\s*;"""
)

FE_COMPUTED_FIXED_ARRAY_RE = re.compile(
    r"""(?:computed\s*\(\s*\(\)\s*=>\s*\[|function\s+\w+\s*\([^)]*\)\s*\{\s*return\s*\[|const\s+\w+\s*=\s*\([^)]*\)\s*=>\s*\[)""",
    re.MULTILINE,
)

FE_MOCK_ARRAY_RE = re.compile(
    r"""(?:const|let|var)\s+([a-zA-Z0-9_]+)\s*=\s*(?:ref\s*\(\s*|reactive\s*\(\s*)?\[\s*\{""",
    re.MULTILINE,
)

FE_PROP_FALLBACK_RE = re.compile(
    r"""(?:props\.[a-zA-Z0-9_.]+\s*\?\.\s*length\s*\?\s*props\.[a-zA-Z0-9_.]+|props\.[a-zA-Z0-9_.]+\s*\|\|\s*\[|default\s*:\s*\(\)\s*=>\s*\[\s*\{)""",
    re.MULTILINE,
)

FE_OBJECT_CONTENT_KEYS_RE = re.compile(
    r"""\b(?:title|name|slug|image|thumbnail|avatar|price|excerpt|description|content|question|answer|author|date|stat|number|value|hotline|phone|email|address)\s*:""",
    re.IGNORECASE,
)

HARDCODED_CONTACT_RE = re.compile(
    r"""(?:href=["'](?:tel:|mailto:)[^"'$\{]+["']|(?:\+84|0[235789]\d)[\s.\-]?\d{3}[\s.\-]?\d{3,4})"""
)


def should_skip_path(path: Path, root: Path) -> bool:
    try:
        rel = path.relative_to(root).as_posix()
    except ValueError:
        rel = path.as_posix()
    for ig in IGNORE_DIRS:
        if rel == ig or rel.startswith(ig + "/") or f"/{ig}/" in f"/{rel}/":
            return True
    return False


def find_line_number(content: str, char_index: int) -> int:
    return content.count("\n", 0, char_index) + 1


def extract_snippet(lines: List[str], line_no: int, max_len: int = 110) -> str:
    if 1 <= line_no <= len(lines):
        raw = lines[line_no - 1].strip()
        raw = re.sub(r"\s+", " ", raw)
        if len(raw) > max_len:
            return raw[: max_len - 3] + "..."
        return raw
    return ""


def find_enclosing_php_method(lines: List[str], line_no: int) -> str:
    for idx in range(line_no - 1, -1, -1):
        m = re.search(r"function\s+([a-zA-Z0-9_]+)\s*\(", lines[idx])
        if m:
            return f"{m.group(1)}()"
    return "global/closure"


def scan_php_file(file_path: Path, root: Path) -> List[Dict[str, Any]]:
    findings: List[Dict[str, Any]] = []
    try:
        content = file_path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return findings

    lines = content.splitlines()
    rel_path = file_path.relative_to(root).as_posix()

    # 1. Multi-dimensional array / collection returns in Controllers, Services, Models, Routes
    for match in PHP_FIXED_RETURN_RE.finditer(content):
        line_no = find_line_number(content, match.start())
        method_name = find_enclosing_php_method(lines, line_no)
        window = "\n".join(lines[max(0, line_no - 1) : min(len(lines), line_no + 20)])
        if PHP_RECORD_KEYS_RE.search(window):
            findings.append(
                {
                    "layer": "Backend (PHP)",
                    "category": f"Fixed Array / Collection Return ({method_name})",
                    "severity": "P1",
                    "file": rel_path,
                    "line": line_no,
                    "symbol": method_name,
                    "snippet": extract_snippet(lines, line_no),
                    "remediation": f"Remove mảng return cố định trong {method_name}; truy vấn từ Eloquent Model / Settings hoặc trả về [] chuẩn.",
                }
            )

    # 2. Direct return of hardcoded record arrays with literal strings/data
    for idx, line in enumerate(lines, start=1):
        if re.search(r"\breturn\s*\[", line):
            method_name = find_enclosing_php_method(lines, idx)
            if method_name in {"rules()", "messages()", "attributes()", "casts()", "via()", "middleware()"}:
                continue
            window = "\n".join(lines[max(0, idx - 1) : min(len(lines), idx + 20)])
            key_matches = PHP_RECORD_KEYS_RE.findall(window)
            # Check if values in window contain hardcoded string literals on RHS of =>
            literal_rhs = re.findall(r"=>\s*['\"][^'\"]{4,}['\"]", window)
            if len(key_matches) >= 2 and len(literal_rhs) >= 1 and not any(
                f["file"] == rel_path and abs(f["line"] - idx) <= 3 for f in findings
            ):
                if "$this->" not in window and "$model->" not in window and "request(" not in window:
                    findings.append(
                        {
                            "layer": "Backend (PHP)",
                            "category": f"Hardcoded Return Payload ({method_name})",
                            "severity": "P1",
                            "file": rel_path,
                            "line": idx,
                            "symbol": method_name,
                            "snippet": extract_snippet(lines, idx),
                            "remediation": f"Remove giá trị cố định trong {method_name}; lấy từ Database / Settings / Config động.",
                        }
                    )

        # 3. Hardcoded scalar return in custom getters/helpers (excluding framework getView/getModel/getTable/getResource)
        if PHP_SCALAR_RETURN_RE.search(line):
            method_name = find_enclosing_php_method(lines, idx)
            if method_name in {"getView()", "getModel()", "getTable()", "getResource()", "redirectTo()"}:
                continue
            if re.match(r"^(?:get|count|total|fetch|default|resolve|setting|contact|hotline|email)", method_name, re.IGNORECASE):
                findings.append(
                    {
                        "layer": "Backend (PHP)",
                        "category": f"Fixed Scalar Return ({method_name})",
                        "severity": "P1",
                        "file": rel_path,
                        "line": idx,
                        "symbol": method_name,
                        "snippet": extract_snippet(lines, idx),
                        "remediation": f"Remove giá trị return cố định trong {method_name}; truy vấn động từ DB/Settings hoặc trả về null/0.",
                    }
                )

    return findings


def scan_fe_file(file_path: Path, root: Path) -> List[Dict[str, Any]]:
    findings: List[Dict[str, Any]] = []
    try:
        content = file_path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return findings

    lines = content.splitlines()
    rel_path = file_path.relative_to(root).as_posix()

    # 1. Computed / Function returning fixed literal array
    for match in FE_COMPUTED_FIXED_ARRAY_RE.finditer(content):
        line_no = find_line_number(content, match.start())
        window = "\n".join(lines[max(0, line_no - 1) : min(len(lines), line_no + 20)])
        if FE_OBJECT_CONTENT_KEYS_RE.search(window):
            findings.append(
                {
                    "layer": "Frontend / CMS UI",
                    "category": "Computed / Function Fixed Array Return",
                    "severity": "P1",
                    "file": rel_path,
                    "line": line_no,
                    "symbol": "computed/getter",
                    "snippet": extract_snippet(lines, line_no),
                    "remediation": "Remove mảng return cố định trong computed/getter; đọc từ props CMS/API hoặc trả về [].",
                }
            )

    # 2. Props fallback to hardcoded mock items (e.g. props.items?.length ? props.items : mockItems)
    for match in FE_PROP_FALLBACK_RE.finditer(content):
        line_no = find_line_number(content, match.start())
        findings.append(
            {
                "layer": "Frontend / CMS UI",
                "category": "Hardcoded Fallback Masking Empty CMS Data",
                "severity": "P1",
                "file": rel_path,
                "line": line_no,
                "symbol": "props fallback",
                "snippet": extract_snippet(lines, line_no),
                "remediation": "Remove dữ liệu fallback giả lập; chỉ hiển thị dữ liệu thật từ CMS props hoặc Empty State chuẩn.",
            }
        )

    # 3. Hardcoded mock arrays of content objects in Pages/Components/Layouts
    for match in FE_MOCK_ARRAY_RE.finditer(content):
        var_name = match.group(1)
        line_no = find_line_number(content, match.start())
        window = "\n".join(lines[max(0, line_no - 1) : min(len(lines), line_no + 25)])
        key_matches = FE_OBJECT_CONTENT_KEYS_RE.findall(window)
        if len(key_matches) >= 2:
            if not re.search(r"(?:columns|headers|fields|rules|toolbar|options|routes|tabs|breadcrumbs)", var_name, re.IGNORECASE):
                findings.append(
                    {
                        "layer": "Frontend / CMS UI",
                        "category": f"Hardcoded Static Data Array ({var_name})",
                        "severity": "P1",
                        "file": rel_path,
                        "line": line_no,
                        "symbol": var_name,
                        "snippet": extract_snippet(lines, line_no),
                        "remediation": f"Remove mảng cố định '{var_name}'; truyền dữ liệu động từ CMS Controller/Settings qua props.",
                    }
                )

    # 4. Hardcoded tel:/mailto: or phone numbers in templates
    for idx, line in enumerate(lines, start=1):
        if HARDCODED_CONTACT_RE.search(line):
            if not any(tok in line for tok in [":href", "v-bind", "${", "{{", "props.", "settings.", "global."]):
                findings.append(
                    {
                        "layer": "Frontend / CMS UI",
                        "category": "Hardcoded Contact / Hotline / Email",
                        "severity": "P2",
                        "file": rel_path,
                        "line": idx,
                        "symbol": "template contact",
                        "snippet": extract_snippet(lines, idx),
                        "remediation": "Remove thông tin liên hệ gán cứng; đọc từ CMS General Settings ($page.props.settings / global).",
                    }
                )

    return findings


def scan_project(root: Path) -> List[Dict[str, Any]]:
    all_findings: List[Dict[str, Any]] = []

    for dir_name in PHP_SCAN_DIRS:
        target = root / dir_name
        if not target.exists():
            continue
        for file_path in sorted(target.rglob("*.php")):
            if should_skip_path(file_path, root):
                continue
            all_findings.extend(scan_php_file(file_path, root))

    for dir_name in FE_SCAN_DIRS:
        target = root / dir_name
        if not target.exists():
            continue
        for ext in ("*.vue", "*.tsx", "*.jsx", "*.ts", "*.js", "*.blade.php"):
            for file_path in sorted(target.rglob(ext)):
                if should_skip_path(file_path, root):
                    continue
                if file_path.name.endswith(".d.ts") or file_path.name.endswith(".min.js"):
                    continue
                all_findings.extend(scan_fe_file(file_path, root))

    return all_findings


def print_markdown_report(findings: List[Dict[str, Any]], root: Path) -> None:
    print("# Báo Cáo Quét Tự Động: CMS Hardcoded & Fixed Return Values")
    print(f"- **Thư mục dự án**: `{root}`")
    print(f"- **Tổng số điểm nghi vấn phát hiện**: **{len(findings)}**")
    print()

    if not findings:
        print("✅ **CLEAN (0 phát hiện)**: Không phát hiện hàm/component nào đang hardcode và return giá trị cố định.")
        return

    print("| # | Tầng | Mức độ | Phân loại & Hàm/Biến | Tọa độ `File:Dòng` | Code Snippet | Hướng xử lý (Remove & Dynamic Bind) |")
    print("| :---: | :--- | :---: | :--- | :--- | :--- | :--- |")
    for i, item in enumerate(findings, start=1):
        snippet_safe = item["snippet"].replace("|", "\\|").replace("`", "'")
        print(
            f"| {i} | {item['layer']} | `{item['severity']}` | {item['category']} | "
            f"`{item['file']}:{item['line']}` | `{snippet_safe}` | {item['remediation']} |"
        )


def main() -> None:
    parser = argparse.ArgumentParser(
        description="Quét toàn bộ CMS phát hiện các vị trí hardcode và return giá trị cố định."
    )
    parser.add_argument(
        "project_dir",
        nargs="?",
        default=".",
        help="Đường dẫn thư mục gốc của dự án (mặc định: thư mục hiện tại).",
    )
    parser.add_argument(
        "--format",
        choices=["markdown", "json"],
        default="markdown",
        help="Định dạng xuất báo cáo (markdown hoặc json).",
    )
    args = parser.parse_args()

    root = Path(args.project_dir).resolve()
    if not root.exists():
        print(f"[LỖI] Thư mục không tồn tại: {root}", file=sys.stderr)
        sys.exit(1)

    findings = scan_project(root)

    if args.format == "json":
        print(json.dumps({"project": str(root), "total": len(findings), "findings": findings}, ensure_ascii=False, indent=2))
    else:
        print_markdown_report(findings, root)


if __name__ == "__main__":
    main()
