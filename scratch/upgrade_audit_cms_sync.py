import re
import sys
from pathlib import Path

def upgrade_file(path_str: str):
    p = Path(path_str)
    if not p.exists():
        print(f"File not found: {path_str}")
        return

    content = p.read_text(encoding="utf-8")
    is_crlf = "\r\n" in content
    normalized = content.replace("\r\n", "\n")

    # 1. UTF-8 reconfiguration
    if "sys.stdout.reconfigure" not in normalized:
        normalized = normalized.replace(
            "import sys\n",
            "import sys\n\nif sys.platform == \"win32\":\n    try:\n        sys.stdout.reconfigure(encoding=\"utf-8\")\n        sys.stderr.reconfigure(encoding=\"utf-8\")\n    except Exception:\n        pass\n",
            1
        )

    # 2. Controller Fallback / Mock methods check (ADD-04)
    ctrl_hook = "    # 1. Kiểm tra các Frontend Controllers phục vụ các trang Active"
    ctrl_code = """    # 1B. Kiểm tra Controller có hàm fallback / mock tự sinh dữ liệu giả khi DB rỗng không (ADD-04)
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
            m_fb = re.search(r"function\\s+((?:get)?(?:Fallback|Mock|Default)[a-zA-Z0-9_]*)\\s*\\(", line, re.IGNORECASE)
            if m_fb:
                fb_name = m_fb.group(1)
                if fb_name in PHP_SAFE_DEFAULT_METHODS:
                    continue
                window = "\\n".join(c_lines[max(0, idx - 1) : min(len(c_lines), idx + 25)])
                if re.search(r"['\\\"](?:id|title|name|slug|image|price|items|solutions|products|posts)['\\\"]\\s*=>", window) or "return [" in window:
                    findings.append({
                        "severity": "HIGH (Hàm Fallback/Mock cứng trong Controller)",
                        "location": f"{frel}:{idx}",
                        "ui_element": f"Hàm `{fb_name}()` trả về dữ liệu mock cứng",
                        "issue": f"Controller `{fc.name}` chứa hàm fallback/mock `{fb_name}()` tự sinh dữ liệu giả khi Database trống, che giấu dữ liệu CMS thật.",
                        "cms_mapping": resolve_cms_system_dynamically(frel, fb_name, cms_systems, ast_graph),
                        "action": f"Xóa hàm `{fb_name}()` tại `{frel}:{idx}`, trả về 404 hoặc mảng rỗng [] khi Database trống.",
                    })
            m_call = re.search(r"->\\s*((?:get)?(?:Fallback|Mock)[a-zA-Z0-9_]*)\\s*\\(", line, re.IGNORECASE)
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
"""

    if "1B. Kiểm tra Controller có hàm fallback" not in normalized:
        # Find where fe_controllers loop ends
        target_marker = "    # 2. Kiểm tra routes/frontend.php"
        if target_marker in normalized:
            normalized = normalized.replace(target_marker, ctrl_code + "\n" + target_marker, 1)

    # 3. Vue component scanning: reverse is_fallback_named + Vue Options API + Array padding/slicing
    old_fallback_block = """            is_fallback_named = bool(
                re.match(r"^(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|mock[A-Z]|sample[A-Z])", var_name)
            )
            uses_props_for_var = bool(
                re.search(rf"props\\.{re.escape(var_name)}|props\\.\\w+\\?\\.length|\\$page\\??\\.props", script_content)
            )

            if used_directly_in_vfor and not uses_props_for_var:
                cms_target = resolve_cms_system_dynamically(frel, var_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "HIGH (Mảng UI tĩnh chưa nối CMS)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Mảng tĩnh `{var_name} = [...]` đang render trực tiếp qua `v-for`",
                    "issue": f"Giao diện đang lặp trực tiếp qua mảng dữ liệu cứng `{var_name}` trong `<script>`, chưa nhận dữ liệu động từ CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Xóa mảng tĩnh `{var_name}` tại `{frel}:{lineno}`, nhận dữ liệu qua `defineProps` từ {cms_target}.",
                })
            elif not has_props_binding and not is_fallback_named:
                cms_target = resolve_cms_system_dynamically(frel, var_name, cms_systems, ast_graph)
                findings.append({
                    "severity": "MEDIUM (Dữ liệu tĩnh trong Component)",
                    "location": f"{frel}:{lineno}",
                    "ui_element": f"Biến mảng dữ liệu tĩnh `{var_name}` trong component không có `defineProps`",
                    "issue": f"Component `{vf.name}` chứa mảng dữ liệu cứng `{var_name}` và chưa khai báo nhận `props` từ trang cha / CMS.",
                    "cms_mapping": cms_target,
                    "action": f"Chuyển `{var_name}` tại `{frel}:{lineno}` thành prop truyền từ trang cha lấy từ {cms_target}.",
                })"""

    new_fallback_block = """            is_fallback_named = bool(
                re.match(r"^(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|mock[A-Z]|sample[A-Z]|rawMock|remainingMocks)", var_name)
            )
            uses_props_for_var = bool(
                re.search(rf"props\\.{re.escape(var_name)}|props\\.\\w+\\?\\.length|\\$page\\??\\.props", script_content)
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
                })"""

    if old_fallback_block in normalized:
        normalized = normalized.replace(old_fallback_block, new_fallback_block, 1)

    # 4. Vue Options API & Array Padding scanners
    options_scanner = """        # 3A-2. Quét Vue Options API data() { return { ... } } (ADD-04)
        for s_idx, (lineno, s_line) in enumerate(script_lines):
            stripped = s_line.strip()
            if stripped.startswith("//") or stripped.startswith("*"):
                continue

            m_opt = re.match(r"^([a-zA-Z0-9_]+)\\s*:\\s*\\[(?:\\s*\\{|\\s*$)", stripped)
            if not m_opt:
                continue

            opt_name = m_opt.group(1)
            if opt_name in UI_ONLY_ARRAY_NAMES or opt_name in {"routes", "headers", "columns", "fields", "rules", "options", "breadcrumbs", "locales"}:
                continue

            lookahead = "\\n".join(
                l for _, l in script_lines[s_idx : min(s_idx + 25, len(script_lines))]
            )
            has_content_keys = bool(
                re.search(
                    r"\\b(title|name|desc|description|excerpt|content|image|img|thumbnail|slug|price|address|phone|question|answer|year)\\s*:",
                    lookahead,
                )
            )
            is_mock_name = bool(
                re.match(r"^(?:DEFAULT_|FALLBACK_|MOCK_|default[A-Z]|fallback[A-Z]|mock[A-Z]|sample[A-Z]|rawMock|remainingMocks)", opt_name)
            )

            prev_lines = "\\n".join(l for _, l in script_lines[max(0, s_idx - 30) : s_idx])
            in_data = "data()" in prev_lines or "data:" in prev_lines or "data ()" in prev_lines

            if in_data and (has_content_keys or is_mock_name):
                if "defaultItems" in lookahead or is_mock_name or re.search(r"['\\\"](?:iron-man|khung-vo|kc-|of-|cs-|thumb_|mock)", lookahead):
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
                r"\\b(defaultItems|remainingMocks|rawMockProducts|mockCatalog|fallbackItems|currentMocks|defaultServices|defaultCards)\\.slice\\s*\\(",
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
                r"\\b(?:padded|padding)\\.push\\s*\\(|\\bpush\\s*\\(\\s*(?:cat\\.)?defaultItems",
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
                r"\\[\\s*\\.\\.\\.\\w+\\s*,\\s*\\.\\.\\.(?:remainingMocks|defaultItems|rawMock|mock|fallback)\\w*",
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
"""

    if "3A-2. Quét Vue Options API" not in normalized:
        target_template_marker = "        # 3B. Quét thông tin liên hệ / thương hiệu hardcode trong <template>"
        if target_template_marker in normalized:
            normalized = normalized.replace(target_template_marker, options_scanner + "\n" + target_template_marker, 1)

    final_content = normalized.replace("\n", "\r\n") if is_crlf else normalized
    p.write_text(final_content, encoding="utf-8")
    print(f"Successfully upgraded {path_str}")

upgrade_file(".agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py")
upgrade_file(".agent/skills/opsx-cms-sync/scripts/audit-cms-sync.py")
