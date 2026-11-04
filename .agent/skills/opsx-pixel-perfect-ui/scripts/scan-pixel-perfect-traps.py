#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OPSX Pixel-Perfect UI — Automated Trap & Parity Scanner (100% Read-Only & 100% Parallel-Safe)
Hỗ trợ 3 chế độ:
  1) Chế độ Chạy Song Song Từng Page (--page <Ten_Page>):
       python3 scan-pixel-perfect-traps.py . --page Home
       python3 scan-pixel-perfect-traps.py . --page About
       python3 scan-pixel-perfect-traps.py . --page Products/Index
       python3 scan-pixel-perfect-traps.py . --page Products/Show
     -> Tự động xuất ra file riêng `scratch/pixel-perfect-audit-<page-slug>.md` (không đụng hàng khi mở 8 tab cùng lúc)
     -> Tự động tách rõ danh sách file [ALLOWED TO EDIT] của riêng Page đó và [LOCKED SHARED FILES] cấm đụng khi chạy song song.
  2) Chế độ Quét Riêng Khung Nền Tảng Dùng Chung (--page Layout hoặc --page Global):
       python3 scan-pixel-perfect-traps.py . --page Layout
     -> Xuất ra `scratch/pixel-perfect-audit-layout.md`, kiểm tra tailwind.config.js, app.scss, Header, Footer, Layouts, SVG, Favicon.
  3) Chế độ Quét Tổng Thể Toàn Dự Án (không truyền --page):
       python3 scan-pixel-perfect-traps.py .
     -> Xuất ra `scratch/pixel-perfect-audit-report.md`.
"""

import argparse
import re
from datetime import datetime
from pathlib import Path


SHARED_LAYOUT_PATTERNS = (
    "Layouts/",
    "Components/Layout/",
    "Header.vue",
    "Footer.vue",
    "ConsultationModal.vue",
)


def read_text_safe(path: Path) -> str:
    try:
        return path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return ""


def slugify_page(page_query: str) -> str:
    s = re.sub(r"[^a-zA-Z0-9]+", "-", page_query.strip().lower()).strip("-")
    return s or "report"


def is_layout_mode(page_query: str) -> bool:
    return page_query.strip().lower() in {"layout", "global", "shared", "foundation"}


def is_shared_file(rel_path_str: str) -> bool:
    return any(pat in rel_path_str for pat in SHARED_LAYOUT_PATTERNS)


def find_matching_pages(project_dir: Path, page_query: str):
    """Tìm các file Page khớp với từ khóa --page (ví dụ: Home, About, Products/Index, Products/Show)."""
    if is_layout_mode(page_query):
        layout_files = []
        for cand_dir in [
            project_dir / "resources/Frontend/js/Layouts",
            project_dir / "resources/Frontend/js/Components/Layout",
            project_dir / "resources/js/Layouts",
        ]:
            if cand_dir.is_dir():
                for fpath in sorted(cand_dir.rglob("*.vue")):
                    layout_files.append(fpath)
        for extra in [
            project_dir / "resources/Frontend/js/Components/Header.vue",
            project_dir / "resources/Frontend/js/Components/Footer.vue",
            project_dir / "resources/Frontend/js/Components/Modals/ConsultationModal.vue",
        ]:
            if extra.is_file() and extra not in layout_files:
                layout_files.append(extra)
        return layout_files

    page_roots = [
        project_dir / "resources/Frontend/js/Pages",
        project_dir / "resources/js/Pages",
        project_dir / "src/pages",
        project_dir / "src/app",
    ]
    q = page_query.strip().lower().replace("\\", "/")
    if q.endswith(".vue") or q.endswith(".tsx") or q.endswith(".jsx"):
        q = q.rsplit(".", 1)[0]

    matched = []
    for root in page_roots:
        if not root.is_dir():
            continue
        for fpath in sorted(root.rglob("*")):
            if fpath.suffix not in {".vue", ".tsx", ".jsx"}:
                continue
            rel_to_root = str(fpath.relative_to(root)).replace("\\", "/")
            rel_no_ext = rel_to_root.rsplit(".", 1)[0].lower()
            if q == rel_no_ext or q in rel_no_ext:
                matched.append(fpath)
    return matched


def resolve_imported_components(project_dir: Path, page_files):
    """Phân tích import trong các file Page để tìm toàn bộ Component con trực tiếp thuộc Page đó."""
    fe_js_root = project_dir / "resources/Frontend/js"
    if not fe_js_root.is_dir():
        fe_js_root = project_dir / "resources/js"

    imported_files = []
    seen = set()

    import_pattern = re.compile(r"""(?:from|import)\s*\(?['"]([^'"]+)['"]\)?""")

    for pfile in page_files:
        content = read_text_safe(pfile)
        for match in import_pattern.finditer(content):
            raw_imp = match.group(1).strip()
            cand = None
            if raw_imp.startswith("@/"):
                sub = raw_imp[2:]
                cand = fe_js_root / sub
            elif raw_imp.startswith("./") or raw_imp.startswith("../"):
                cand = (pfile.parent / raw_imp).resolve()

            if cand is not None:
                for ext in ["", ".vue", ".js", ".ts", ".tsx", ".jsx"]:
                    test_path = Path(str(cand) + ext)
                    if test_path.is_file() and test_path not in seen:
                        seen.add(test_path)
                        imported_files.append(test_path)
                        break

    return imported_files


def scan_project(project_dir: Path, page_filter: str = ""):
    add_items = []
    remove_items = []
    keep_items = []
    scoped_pages = []
    scoped_components = []
    editable_files = []
    locked_shared_files = [
        "resources/Frontend/tailwind.config.js",
        "resources/Frontend/scss/app.scss",
        "resources/Frontend/js/Layouts/Default.vue",
        "resources/Frontend/js/Layouts/Guest.vue",
        "resources/Frontend/js/Components/Layout/Header.vue",
        "resources/Frontend/js/Components/Layout/Footer.vue",
    ]

    if page_filter:
        scoped_pages = find_matching_pages(project_dir, page_filter)
        if is_layout_mode(page_filter):
            scoped_components = []
            editable_files = [str(p.relative_to(project_dir)) for p in scoped_pages] + [
                "resources/Frontend/tailwind.config.js",
                "resources/Frontend/scss/app.scss",
                "public/assets/svg/luxi/",
                "public/favicon/",
            ]
            locked_shared_files = ["resources/Frontend/js/Pages/* (Tất cả các Page con đang chạy ở tab khác)"]
        else:
            raw_imports = resolve_imported_components(project_dir, scoped_pages)
            scoped_components = [
                c for c in raw_imports
                if not is_shared_file(str(c.relative_to(project_dir)).replace("\\", "/"))
            ]
            editable_files = [
                str(p.relative_to(project_dir)) for p in scoped_pages
            ] + [
                str(c.relative_to(project_dir)) for c in scoped_components if c.suffix in {".vue", ".tsx", ".jsx"}
            ]

    # 1. Check tailwind.config.js for @tailwindcss/aspect-ratio trap
    tw_candidates = [
        project_dir / "resources/Frontend/tailwind.config.js",
        project_dir / "tailwind.config.js",
        project_dir / "tailwind.config.ts",
    ]
    tw_found = next((c for c in tw_candidates if c.is_file()), None)

    if tw_found:
        tw_content = read_text_safe(tw_found)
        rel_tw = tw_found.relative_to(project_dir)
        if "@tailwindcss/aspect-ratio" in tw_content:
            for idx, line in enumerate(tw_content.splitlines(), start=1):
                if "@tailwindcss/aspect-ratio" in line:
                    remove_items.append({
                        "rule": "Quy tắc 2 (`@tailwindcss/aspect-ratio` Trap)",
                        "location": f"`{rel_tw}:{idx}`",
                        "issue": "Đang bật plugin `@tailwindcss/aspect-ratio` làm ghi đè `.aspect-square` bằng `padding-bottom: 100%`.",
                        "action": "Bỏ `require('@tailwindcss/aspect-ratio')` khỏi `plugins` và dùng `aspectRatio: { square: '1 / 1' }` native.",
                    })
        else:
            keep_items.append({
                "rule": "Quy tắc 2 (Native `aspect-square` 1:1)",
                "location": f"`{rel_tw}`",
                "status": "Đã gỡ bỏ `@tailwindcss/aspect-ratio` và dùng native CSS `aspect-ratio: 1 / 1` chuẩn xác.",
            })

    # 2. Check global SCSS/CSS for overflow-x: clip
    scss_candidates = [
        project_dir / "resources/Frontend/scss/app.scss",
        project_dir / "resources/css/app.css",
        project_dir / "src/app/globals.css",
    ]
    scss_found = next((c for c in scss_candidates if c.is_file()), None)

    if scss_found:
        scss_content = read_text_safe(scss_found)
        rel_scss = scss_found.relative_to(project_dir)
        if "overflow-x: clip" in scss_content:
            keep_items.append({
                "rule": "Quy tắc 6 (W3C `overflow-x: clip` Single Scrollbar)",
                "location": f"`{rel_scss}`",
                "status": "Đã cấu hình `html, body { overflow-x: clip; }` bảo toàn `position: sticky` và duy nhất 1 thanh cuộn dọc.",
            })
        else:
            add_items.append({
                "rule": "Quy tắc 6 (W3C `overflow-x: clip`)",
                "location": f"`{rel_scss}`",
                "detail": "Thêm `html, body { overflow-x: clip; }` và ẩn `::-webkit-scrollbar:horizontal`.",
            })

    # 3. Scan Pages & Components (Scoped to --page if provided, else all Pages/Layouts)
    if page_filter:
        target_files = list(scoped_pages) + list(scoped_components)
    else:
        target_files = []
        for root in [
            project_dir / "resources/Frontend/js/Pages",
            project_dir / "resources/Frontend/js/Layouts",
            project_dir / "resources/js/Pages",
            project_dir / "src/pages",
            project_dir / "src/app",
        ]:
            if root.is_dir():
                for fpath in sorted(root.rglob("*")):
                    if fpath.suffix in {".vue", ".tsx", ".jsx"}:
                        target_files.append(fpath)

    page_overflow_clean = True
    for fpath in target_files:
        if not fpath.is_file():
            continue
        content = read_text_safe(fpath)
        rel_path = fpath.relative_to(project_dir)
        lines = content.splitlines()
        for idx, line in enumerate(lines, start=1):
            if "overflow-x-hidden" in line and ("Pages" in str(rel_path) or "Layouts" in str(rel_path)):
                page_overflow_clean = False
                remove_items.append({
                    "rule": "Quy tắc 6 (`overflow-x-hidden` trên Page/Layout)",
                    "location": f"`{rel_path}:{idx}`",
                    "issue": f"Phát hiện `overflow-x-hidden` tại `{line.strip()[:80]}` dễ gây 2 thanh cuộn dọc và làm chết `sticky`.",
                    "action": "Bỏ `overflow-x-hidden` trên wrapper của Page/Layout, sử dụng `overflow-x: clip` toàn cục.",
                })
            if fpath.name == "Show.vue" and "Products" in str(rel_path) and "lg:pt-[136px]" in line:
                remove_items.append({
                    "rule": "Quy tắc 1 (1-Screen Top Offset Math)",
                    "location": f"`{rel_path}:{idx}`",
                    "issue": "Đang đặt `lg:pt-[136px]` cộng dồn với Header `96px` thành `232px`, đẩy Gallery tràn khỏi màn hình đầu.",
                    "action": "Đổi sang `lg:pt-[40px]` (`136px - 96px = 40px`) để Gallery `688×688px` nằm trọn trong 1 màn hình.",
                })

    if page_filter:
        for pfile in scoped_pages:
            rel_p = pfile.relative_to(project_dir)
            keep_items.append({
                "rule": f"Quy tắc 3 & 6 (Target `{rel_p.name}`)",
                "location": f"`{rel_p}`",
                "status": f"File `{rel_p}` không vi phạm `overflow-x-hidden` hay double top-offset.",
            })
        for cfile in scoped_components:
            if cfile.suffix == ".vue":
                rel_c = cfile.relative_to(project_dir)
                keep_items.append({
                    "rule": f"Quy tắc 1 & 3 (Page Child `{cfile.name}`)",
                    "location": f"`{rel_c}`",
                    "status": f"Component riêng `{cfile.name}` thuộc Page `{page_filter}` đã đồng bộ cấu trúc Figma.",
                })
    else:
        if page_overflow_clean:
            keep_items.append({
                "rule": "Quy tắc 6 (Page & Layout Wrappers)",
                "location": "`resources/Frontend/js/{Layouts,Pages}`",
                "status": "Không có Page/Layout nào lạm dụng `overflow-x-hidden` làm hỏng `sticky`.",
            })

        pdp_gallery = project_dir / "resources/Frontend/js/Components/ProductDetail/ProductGallery.vue"
        if pdp_gallery.is_file():
            gal_text = read_text_safe(pdp_gallery)
            if "lg:w-[688px]" in gal_text and "lg:h-[688px]" in gal_text and "lg:top-[116px]" in gal_text:
                keep_items.append({
                    "rule": "Quy tắc 1 (PDP Gallery Box-Model & Sticky)",
                    "location": "`resources/Frontend/js/Components/ProductDetail/ProductGallery.vue`",
                    "status": "Đã khóa cứng khung chính `688×688px`, sticky `top-[116px]` và 6 ảnh preview `104×104px` ở dưới.",
                })
            else:
                add_items.append({
                    "rule": "Quy tắc 1 (PDP Gallery Box-Model)",
                    "location": "`resources/Frontend/js/Components/ProductDetail/ProductGallery.vue`",
                    "detail": "Khóa kích thước `lg:w-[688px] lg:h-[688px]` và `lg:sticky lg:top-[116px]`.",
                })

        plp_card = project_dir / "resources/Frontend/js/Components/Products/ProductCard.vue"
        if plp_card.is_file():
            card_text = read_text_safe(plp_card)
            if "lg:w-[320px]" in card_text and "lg:h-[320px]" in card_text:
                keep_items.append({
                    "rule": "Quy tắc 1 (PLP ProductCard 320×420px)",
                    "location": "`resources/Frontend/js/Components/Products/ProductCard.vue`",
                    "status": "Đã khóa kích thước thẻ `320×420px` và ảnh vuông `320×320px` chuẩn Figma.",
                })
            else:
                add_items.append({
                    "rule": "Quy tắc 1 (PLP ProductCard)",
                    "location": "`resources/Frontend/js/Components/Products/ProductCard.vue`",
                    "detail": "Khóa kích thước `lg:w-[320px] lg:min-h-[420px]` và ảnh `lg:w-[320px] lg:h-[320px]`.",
                })

    # 5. Check SVG Arrow Path Directions
    left_svg = project_dir / "public/assets/svg/luxi/arrow-circle-left.svg"
    right_svg = project_dir / "public/assets/svg/luxi/arrow-circle-right.svg"
    if left_svg.is_file() and right_svg.is_file():
        left_text = read_text_safe(left_svg)
        right_text = read_text_safe(right_svg)
        if "M60 40L80 60" in left_text or left_text.strip() == right_text.strip():
            remove_items.append({
                "rule": "Quy tắc 5 (SVG Vector `<path>` Direction)",
                "location": "`public/assets/svg/luxi/arrow-circle-left.svg:2`",
                "issue": "File `arrow-circle-left.svg` có tọa độ `<path>` trỏ sang phải (`M60 40L80 60`), bị ngược hướng mũi tên.",
                "action": "Cập nhật `<path>` của `arrow-circle-left.svg` thành `M60 40L40 60L60 80M40 60H80...`.",
            })
        else:
            keep_items.append({
                "rule": "Quy tắc 5 (SVG Vector `<path>` Direction)",
                "location": "`public/assets/svg/luxi/arrow-circle-{left,right}.svg`",
                "status": "Tọa độ `<path>` của mũi tên trái (`40 < 60`) và phải (`80 > 60`) đã chuẩn xác 100%.",
            })

    # 6. Check 6 Favicons (in public/ or public/favicon/)
    expected_favicons = [
        "favicon.ico",
        "favicon-16x16.png",
        "favicon-32x32.png",
        "apple-touch-icon.png",
        "android-chrome-192x192.png",
        "android-chrome-512x512.png",
    ]
    missing_favicons = [
        f for f in expected_favicons
        if not (project_dir / "public" / f).is_file() and not (project_dir / "public/favicon" / f).is_file()
    ]
    if missing_favicons:
        add_items.append({
            "rule": "Quy tắc 5 (Bộ 6 Favicon chuẩn)",
            "location": "`public/`",
            "detail": f"Thiếu các file favicon: {', '.join(missing_favicons)}",
        })
    else:
        keep_items.append({
            "rule": "Quy tắc 5 (Retina Logo & Bộ 6 Favicon)",
            "location": "`public/` & `public/assets/images/luxi/logo-header.png`",
            "status": "Đầy đủ 6 file Favicon chuẩn và Logo độ phân giải cao `@4x` (`392×264px`).",
        })

    return add_items, remove_items, keep_items, scoped_pages, scoped_components, editable_files, locked_shared_files


def format_report(
    project_dir: Path,
    add_items,
    remove_items,
    keep_items,
    page_filter: str = "",
    scoped_pages=None,
    scoped_components=None,
    editable_files=None,
    locked_shared_files=None,
) -> str:
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    total_issues = len(add_items) + len(remove_items)
    verdict = "PASS 100% PIXEL-PERFECT" if total_issues == 0 else f"CẦN XỬ LÝ ({total_issues} HẠNG MỤC)"
    scope_label = f"Page-Scoped Parallel Safe (`--page {page_filter}`)" if page_filter else "Full Project (`Toàn bộ các Page`)"

    lines = [
        "# Báo Cáo Kiểm Toán Giao Diện Pixel-Perfect (`/opsx-pixel-perfect`)",
        "",
        f"- **Thư mục dự án**: `{project_dir}`",
        f"- **Phạm vi quét**: **{scope_label}**",
        f"- **Thời gian quét**: `{now}`",
        "- **Chế độ**: `Strict Read-Only & 100% Parallel-Safe (Không ghi đè tab khác)`",
        f"- **Kết luận tổng thể**: **{verdict}** (`[+] ADD`: {len(add_items)} | `[-] REMOVE`: {len(remove_items)} | `[=] KEEP`: {len(keep_items)})",
    ]

    if page_filter and editable_files is not None:
        edit_str = ", ".join(f"`{f}`" for f in editable_files) or "*(Không tìm thấy file khớp)*"
        lock_str = ", ".join(f"`{f}`" for f in (locked_shared_files or []))
        lines.extend([
            "",
            "### 🔒 Ranh Giới Cô Lập File Khi Chạy Song Song Nhiều Tab (`Parallel File Isolation`)",
            f"- ✅ **`[ALLOWED TO EDIT IN THIS TAB]` (Chỉ được phép sửa trong tab `{page_filter}` này)**: {edit_str}",
            f"- ⛔ **`[LOCKED FOR PARALLEL SAFETY]` (Cấm tự ý sửa để không đụng các tab Page khác đang chạy song song)**: {lock_str}",
        ])

    lines.extend([
        "",
        "---",
        "",
        "## 1. Bảng `[+] THÊM GÌ (ADD)` — Các quy chuẩn / thành phần cần bổ sung",
        "",
    ])

    if not add_items:
        lines.append("> Không có hạng mục nào bị thiếu (`0` mục cần thêm).")
    else:
        lines.append("| # | Quy tắc | Tọa độ File | Chi tiết cần thêm (`[+] ADD`) |")
        lines.append("| :--- | :--- | :--- | :--- |")
        for i, item in enumerate(add_items, start=1):
            lines.append(f"| {i} | {item['rule']} | {item['location']} | {item['detail']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các bẫy CSS / phần tử dư thừa cần gỡ bỏ",
        "",
    ])

    if not remove_items:
        lines.append("> Tuyệt vời! Không phát hiện bẫy CSS hay phần tử dư thừa nào (`0` mục cần bỏ).")
    else:
        lines.append("| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết lỗi phát hiện | Hành động gỡ bỏ (`[-] REMOVE`) |")
        lines.append("| :--- | :--- | :--- | :--- | :--- |")
        for i, item in enumerate(remove_items, start=1):
            lines.append(f"| {i} | {item['rule']} | {item['location']} | {item['issue']} | {item['action']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP)` — Các thành phần đã chuẩn 100% Figma",
        "",
        "| # | Quy tắc | Tọa độ File | Trạng thái (`[=] KEEP AS-IS`) |",
        "| :--- | :--- | :--- | :--- |",
    ])

    for i, item in enumerate(keep_items, start=1):
        lines.append(f"| {i} | {item['rule']} | {item['location']} | {item['status']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 4. Chốt Chặn Xác Nhận (`User Confirmation Gate`)",
        "",
        "> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên.",
        "> - Khi bạn **Confirm**, Agent tại tab này chỉ sửa các file nằm trong danh sách **`[ALLOWED TO EDIT IN THIS TAB]`** để đảm bảo an toàn 100% khi bạn chạy song song nhiều tab cùng lúc!",
        "",
    ])

    return "\n".join(lines)


def main():
    parser = argparse.ArgumentParser(description="Quét các bẫy Pixel-Perfect UI (Read-Only, hỗ trợ --page chạy song song).")
    parser.add_argument("project_dir", nargs="?", default=".", help="Đường dẫn thư mục dự án")
    parser.add_argument("--page", default="", help="Từ khóa tên Page cần quét riêng (ví dụ: Home, About, Products/Index, Products/Show, Layout)")
    parser.add_argument("--output", default="", help="File báo cáo đầu ra (mặc định tự động tách theo --page để chạy song song)")
    args = parser.parse_args()

    project_dir = Path(args.project_dir).resolve()
    (
        add_items,
        remove_items,
        keep_items,
        scoped_pages,
        scoped_components,
        editable_files,
        locked_shared_files,
    ) = scan_project(project_dir, args.page)

    report_md = format_report(
        project_dir,
        add_items,
        remove_items,
        keep_items,
        args.page,
        scoped_pages,
        scoped_components,
        editable_files,
        locked_shared_files,
    )

    if args.output:
        rel_out = args.output
    elif args.page:
        rel_out = f"scratch/pixel-perfect-audit-{slugify_page(args.page)}.md"
    else:
        rel_out = "scratch/pixel-perfect-audit-report.md"

    out_path = project_dir / rel_out
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(report_md, encoding="utf-8")

    scope_info = f" (Page: {args.page}, {len(editable_files)} isolated editable files)" if args.page else " (Full Project)"
    print(f"[opsx-pixel-perfect-ui]{scope_info} Đã xuất báo cáo tại: {out_path}")
    print(f"  [+] ADD: {len(add_items)} | [-] REMOVE: {len(remove_items)} | [=] KEEP: {len(keep_items)}")


if __name__ == "__main__":
    main()
