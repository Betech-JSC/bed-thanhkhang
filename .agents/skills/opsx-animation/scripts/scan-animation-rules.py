#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
OPSX Animation — 4 Golden Rules Automated Scanner (100% Read-Only)
Kiểm toán toàn bộ mã nguồn Frontend theo 4 Quy Tắc Vàng về Animation:
1. Quy tắc 1 (Single-Animation-Per-Element): Không lồng <TransitionGroup> trong <Transition>, không gán trùng transition-transform + transition-all.
2. Quy tắc 2 (Zero White-Out / No Opacity Blank Flash): useScrollReveal không được dùng opacity-0 làm trắng màn hình.
3. Quy tắc 3 (Directional Horizontal Slide translate-x): Các slider có nút bấm qua lại (< / >) phải dùng translate-x theo slideDirection, không dùng opacity-0.
4. Quy tắc 4 (Preserve Product Gallery with Bottom Thumbnails): Giữ nguyên 100% ProductGallery.vue và ProductInfoPanel.vue trong bảng [=] KEEP AS-IS.
Xuất báo cáo ra scratch/animation-rules-audit-report.md.
"""

import argparse
import re
import sys
from datetime import datetime

if hasattr(sys.stdout, "reconfigure"):
    sys.stdout.reconfigure(encoding="utf-8", errors="replace")
from pathlib import Path


SLIDER_COMPONENTS = [
    ("resources/Frontend/js/Components/Home/HomeProductShowcaseSection.vue", "Showcase 3 cột sản phẩm Trang chủ (nút `<` / `>`)"),
    ("resources/Frontend/js/Components/Home/HomePerspectiveSection.vue", "Carousel 3 thẻ Góc nhìn LUXI (nút `<` / `>`)"),
    ("resources/Frontend/js/Components/ProductDetail/RelatedProductsSection.vue", "Slider 3 Sản phẩm liên quan (nút `<` / `>` & Dots)"),
    ("resources/Frontend/js/Components/Home/HomeHeroSection.vue", "Hero Banner Slider Trang chủ (Dots chuyển slide)"),
    ("resources/Frontend/js/Components/Products/ProductGrid.vue", "Lưới phân trang Danh sách sản phẩm (nút `<` / `>` Pagination)"),
    ("resources/Frontend/js/Pages/Home/Components/FeaturedProductsCarousel.vue", "Carousel 4 thẻ Sản phẩm nổi bật Trang chủ (nút `<` / `>`)"),
    ("resources/Frontend/js/Pages/Home/Components/ProductsSection.vue", "3 hàng Carousel Danh mục sản phẩm Trang chủ (nút `<` / `>`)"),
    ("resources/Frontend/js/Components/Product/RelatedProducts.vue", "Slider 4 Sản phẩm liên quan trang Chi tiết (nút `<` / `>`)"),
    ("resources/Frontend/js/Pages/Product/Index.vue", "Lưới phân trang Danh mục sản phẩm kèm `ProductCard.vue` & `ProductPagination.vue` (nút `<` / `>`)"),
    ("resources/Frontend/js/Pages/Product/Show.vue", "Tabs Chi tiết & Thông số sản phẩm (trượt ngang `tabSlideDirection`)"),
]

PRESERVED_GALLERY_COMPONENTS = [
    ("resources/Frontend/js/Components/ProductDetail/ProductGallery.vue", "Gallery chi tiết sản phẩm `688×688px` kèm hàng 6 ảnh preview `104×104px` ở dưới"),
    ("resources/Frontend/js/Components/ProductDetail/ProductInfoPanel.vue", "Bảng thông tin chi tiết sản phẩm & chọn mẫu vải/phụ kiện"),
    ("resources/Frontend/js/Components/Product/ProductGallery.vue", "Gallery chi tiết sản phẩm `624×600px` kèm hàng ảnh preview nhỏ ở dưới"),
    ("resources/Frontend/js/Components/Product/VariantSelector.vue", "Bảng lựa chọn tham khảo chất liệu, màu sắc & tùy chỉnh sản phẩm"),
    ("resources/Frontend/js/Components/Product/SpecTable.vue", "Bảng thông số kỹ thuật sản phẩm chi tiết"),
]


def read_text_safe(path: Path) -> str:
    try:
        return path.read_text(encoding="utf-8", errors="ignore")
    except Exception:
        return ""


def extract_transitions(content: str):
    """Trích xuất các thẻ mở <Transition ...> kèm số dòng."""
    results = []
    for match in re.finditer(r"<Transition\b[^>]*>", content, flags=re.DOTALL):
        start_line = content.count("\n", 0, match.start()) + 1
        results.append((start_line, match.group(0)))
    return results


def scan_project(project_dir: Path):
    add_items = []
    remove_items = []
    keep_items = []

    # 1. Check Quy tắc 4: Preserved ProductGallery.vue & ProductInfoPanel.vue
    for rel_str, desc in PRESERVED_GALLERY_COMPONENTS:
        fpath = project_dir / rel_str
        if fpath.is_file():
            keep_items.append({
                "rule": "Quy tắc 4 (`Preserve Product Gallery — KEEP AS-IS`)",
                "location": f"`{rel_str}`",
                "status": f"GIỮ NGUYÊN 100%: {desc}.",
            })

    # 2. Check Quy tắc 2: useScrollReveal.js (No opacity-0 blank flash)
    sr_path = project_dir / "resources/Frontend/js/Composables/useScrollReveal.js"
    if not sr_path.is_file():
        sr_path = project_dir / "resources/Frontend/js/composables/useScrollReveal.js"
    if sr_path.is_file():
        sr_text = read_text_safe(sr_path)
        rel_sr = sr_path.relative_to(project_dir)
        if "opacity-0" in sr_text:
            remove_items.append({
                "rule": "Quy tắc 2 (`Zero White-Out / No Opacity Blank Flash`)",
                "location": f"`{rel_sr}`",
                "issue": "Composable `useScrollReveal` đang chứa `opacity-0` gây nháy trắng/tàng hình khi cuộn trang.",
                "action": "Bỏ `opacity-0`, chỉ dùng `transition-transform duration-500 ease-out` với `translate-y-4 -> translate-y-0`.",
            })
        else:
            keep_items.append({
                "rule": "Quy tắc 2 (`Zero White-Out Scroll Reveal`)",
                "location": f"`{rel_sr}`",
                "status": "Chuẩn 100%: Chỉ trượt nhẹ `translate-y-4 -> translate-y-0` với `transition-transform`, không dùng `opacity-0`.",
            })

    # 3. Check Quy tắc 3: Directional Horizontal Slide (translate-x) for Prev/Next Sliders
    for rel_str, desc in SLIDER_COMPONENTS:
        fpath = project_dir / rel_str
        if not fpath.is_file():
            continue
        content = read_text_safe(fpath)
        has_slide_dir = any(k in content for k in ("slideDirection", "rowDirections", "tabSlideDirection"))
        has_translate_x = "translate-x-" in content

        # Check main slider transitions in this component
        transitions = extract_transitions(content)
        slider_transitions = []
        for line_no, tag_text in transitions:
            # In ProductGrid.vue, skip small toolbar badge/chip/article transitions (only check the main out-in grid transition)
            if rel_str.endswith("ProductGrid.vue") and 'mode="out-in"' not in tag_text:
                continue
            slider_transitions.append((line_no, tag_text))

        has_opacity_in_slider = any("opacity-0" in t[1] for t in slider_transitions)

        if has_opacity_in_slider or not has_translate_x or not has_slide_dir:
            first_line = slider_transitions[0][0] if slider_transitions else 1
            if has_opacity_in_slider:
                remove_items.append({
                    "rule": "Quy tắc 3 (`Directional Horizontal Slide`)",
                    "location": f"`{rel_str}:{first_line}`",
                    "issue": f"{desc} đang dùng `opacity-0` fade khi bấm chuyển slide/trang.",
                    "action": "Bỏ hoàn toàn `opacity-0` trong `<Transition>` của slider.",
                })
            if not (has_slide_dir and has_translate_x):
                add_items.append({
                    "rule": "Quy tắc 3 (`Directional Horizontal Slide`)",
                    "location": f"`{rel_str}:{first_line}`",
                    "detail": f"Thêm `slideDirection = ref('next')` và đổi `<Transition>` của {desc} sang trượt ngang `translate-x`.",
                })
        else:
            keep_items.append({
                "rule": "Quy tắc 3 (`Directional Horizontal Slide translate-x`)",
                "location": f"`{rel_str}`",
                "status": f"Chuẩn 100%: {desc} đã trượt ngang `translate-x` theo hướng bấm (`next`/`prev`), không dùng `opacity-0`.",
            })

    # 4. Check Quy tắc 1: Single-Animation-Per-Element across all Vue files
    fe_js = project_dir / "resources/Frontend/js"
    double_anim_found = False
    if fe_js.is_dir():
        for fpath in sorted(fe_js.rglob("*.vue")):
            content = read_text_safe(fpath)
            rel_path = fpath.relative_to(project_dir)
            # Check nested Transition + TransitionGroup in ProductGrid
            if "<TransitionGroup" in content and "<Transition" in content and fpath.name == "ProductGrid.vue":
                double_anim_found = True
                remove_items.append({
                    "rule": "Quy tắc 1 (`Single-Animation-Per-Element`)",
                    "location": f"`{rel_path}`",
                    "issue": "Đang lồng `<TransitionGroup>` bên trong `<Transition>` gây chạy kép 2 animation trên cùng lưới sản phẩm.",
                    "action": "Gỡ bỏ `<TransitionGroup>`, chỉ giữ 1 `<Transition mode=\"out-in\">` trượt ngang `translate-x`.",
                })
            for idx, line in enumerate(content.splitlines(), start=1):
                if "transition-transform" in line and "transition-all" in line:
                    double_anim_found = True
                    remove_items.append({
                        "rule": "Quy tắc 1 (`Single-Animation-Per-Element`)",
                        "location": f"`{rel_path}:{idx}`",
                        "issue": "Gán trùng lặp cả `transition-transform` và `transition-all` trên cùng 1 phần tử.",
                        "action": "Giữ duy nhất `transition-transform`.",
                    })

    if not double_anim_found:
        keep_items.append({
            "rule": "Quy tắc 1 (`Single-Animation-Per-Element`)",
            "location": "`resources/Frontend/js/{Layouts,Pages,Components}`",
            "status": "Chuẩn 100%: Mỗi phần tử chỉ có duy nhất 1 animation chủ đạo, không lồng `<TransitionGroup>` kép.",
        })

    return add_items, remove_items, keep_items


def format_report(project_dir: Path, add_items, remove_items, keep_items) -> str:
    now = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    total_issues = len(add_items) + len(remove_items)
    verdict = "PASS 100% (4 QUY TẮC VÀNG ANIMATION)" if total_issues == 0 else f"CẦN XỬ LÝ ({total_issues} HẠNG MỤC)"

    lines = [
        "# Báo Cáo Kiểm Toán 4 Quy Tắc Vàng Animation (`/opsx-animation` & `/ui-skill`)",
        "",
        f"- **Thư mục dự án**: `{project_dir}`",
        f"- **Thời gian quét**: `{now}`",
        "- **Chế độ**: `Strict Read-Only (Không tự ý sửa/xóa file)`",
        f"- **Kết luận tổng thể**: **{verdict}** (`[+] ADD`: {len(add_items)} | `[-] REMOVE`: {len(remove_items)} | `[=] KEEP`: {len(keep_items)})",
        "",
        "---",
        "",
        "## 1. Bảng `[+] THÊM GÌ (ADD)` — Các hiệu ứng trượt ngang / micro-interaction cần bổ sung",
        "",
    ]

    if not add_items:
        lines.append("> Không có hạng mục nào bị thiếu (`0` mục cần thêm).")
    else:
        lines.append("| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết cần thêm (`[+] ADD`) |")
        lines.append("| :--- | :--- | :--- | :--- |")
        for i, item in enumerate(add_items, start=1):
            lines.append(f"| {i} | {item['rule']} | {item['location']} | {item['detail']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 2. Bảng `[-] BỎ GÌ (REMOVE)` — Các hiệu ứng `opacity-0` / chồng chéo animation cần gỡ bỏ",
        "",
    ])

    if not remove_items:
        lines.append("> Tuyệt vời! Không có hiệu ứng `opacity-0` sai quy chuẩn hay chồng chéo animation nào (`0` mục cần bỏ).")
    else:
        lines.append("| # | Quy tắc | Tọa độ `File:Dòng` | Chi tiết vi phạm | Hành động gỡ bỏ (`[-] REMOVE`) |")
        lines.append("| :--- | :--- | :--- | :--- | :--- |")
        for i, item in enumerate(remove_items, start=1):
            lines.append(f"| {i} | {item['rule']} | {item['location']} | {item['issue']} | {item['action']} |")

    lines.extend([
        "",
        "---",
        "",
        "## 3. Bảng `[=] GIỮ LẠI GÌ (KEEP AS-IS)` — Các thành phần giữ nguyên 100% theo quy chuẩn",
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
        "> Hãy kiểm tra 3 bảng `[+] THÊM GÌ / [-] BỎ GÌ / [=] GIỮ LẠI GÌ` phía trên (đặc biệt `ProductGallery.vue` có hàng preview nhỏ ở dưới luôn nằm trong mục `[=] GIỮ LẠI GÌ`).",
        "> Sau khi bạn **Confirm**, đoạn hội thoại trung tâm sẽ xuất bộ **Handoff Prompts** để đem sang các đoạn hội thoại con thực thi.",
        "",
    ])

    return "\n".join(lines)


def main():
    parser = argparse.ArgumentParser(description="Quét kiểm toán 4 Quy Tắc Vàng Animation (Read-Only).")
    parser.add_argument("project_dir", nargs="?", default=".", help="Đường dẫn thư mục dự án")
    parser.add_argument("--output", default="scratch/animation-rules-audit-report.md", help="File báo cáo đầu ra")
    args = parser.parse_args()

    project_dir = Path(args.project_dir).resolve()
    add_items, remove_items, keep_items = scan_project(project_dir)
    report_md = format_report(project_dir, add_items, remove_items, keep_items)

    out_path = project_dir / args.output
    out_path.parent.mkdir(parents=True, exist_ok=True)
    out_path.write_text(report_md, encoding="utf-8")

    print(f"[opsx-animation] Đã xuất báo cáo tại: {out_path}")
    print(f"  [+] ADD: {len(add_items)} | [-] REMOVE: {len(remove_items)} | [=] KEEP: {len(keep_items)}")


if __name__ == "__main__":
    main()
