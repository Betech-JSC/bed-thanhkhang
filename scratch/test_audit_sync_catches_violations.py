import tempfile
from pathlib import Path
import sys

if sys.platform == "win32":
    try:
        sys.stdout.reconfigure(encoding="utf-8")
        sys.stderr.reconfigure(encoding="utf-8")
    except Exception:
        pass

# Test Vue file with audit-cms-sync
vue_test = """<template>
    <div v-for="item in defaultItems" :key="item.id">{{ item.title }}</div>
</template>
<script>
export default {
    data() {
        return {
            defaultItems: [
                { id: 'mock-1', title: 'Fake Product', price: 99000 }
            ]
        }
    },
    computed: {
        displayList() {
            return this.defaultItems.slice(0, 3);
        }
    }
}
</script>
"""
temp_vue = Path("resources/Frontend/js/Pages/Home/Components/TestMockComponent.vue")
temp_vue.write_text(vue_test, encoding="utf-8")

import subprocess
res = subprocess.run([sys.executable, ".agents/skills/opsx-cms-sync/scripts/audit-cms-sync.py", "."], capture_output=True, text=True, encoding="utf-8")
temp_vue.unlink(missing_ok=True)

print("Exit code:", res.returncode)
print("Output snippet:")
lines = res.stdout.splitlines()
for line in lines:
    if "TestMockComponent" in line or "defaultItems" in line or "Vị trí UI chưa nối CMS" in line:
        print(line)

report_content = Path("scratch/cms-sync-audit-report.md").read_text(encoding="utf-8")
assert "TestMockComponent" in report_content or "defaultItems" in report_content, "audit-cms-sync failed to detect TestMockComponent violation!"
print("Found in report:", [l for l in report_content.splitlines() if "TestMockComponent" in l])
print("AUDIT-CMS-SYNC VIOLATION DETECTION TEST PASSED 100%!")
