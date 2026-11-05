import tempfile
from pathlib import Path
import sys

# Import scanners
sys.path.insert(0, str(Path(".agents/skills/opsx-audit-handoff/scripts").resolve()))
import importlib.util

spec = importlib.util.spec_from_file_location("scan_cms", ".agents/skills/opsx-audit-handoff/scripts/scan-cms-hardcoded.py")
scan_cms = importlib.util.module_from_spec(spec)
spec.loader.exec_module(scan_cms)

root = Path(".").resolve()

# Test 1: PHP Fallback Method Detection
php_test = """<?php
namespace App\\Http\\Controllers\\Frontend;
class TestController {
    public function getFallbackProduct($slug) {
        $catalog = [
            ['id' => 1, 'title' => 'Iron Man Mark 85', 'price' => 100000]
        ];
        return $catalog;
    }
}
"""
with tempfile.NamedTemporaryFile("w", suffix=".php", delete=False, dir="app/Http/Controllers/Frontend") as f:
    f.write(php_test)
    temp_php = Path(f.name)

try:
    findings = scan_cms.scan_php_file(temp_php, root)
    print("PHP Fallback Detection Test:", len(findings), "finding(s)")
    for finding in findings:
        print(" - Found:", finding["category"], finding["symbol"])
    assert any("getFallbackProduct" in f["symbol"] for f in findings), "Failed to detect getFallbackProduct!"
finally:
    temp_php.unlink()

# Test 2: Vue Options API data() & Padding logic
vue_test = """<template>
    <div v-for="item in allProducts" :key="item.id">{{ item.title }}</div>
</template>
<script>
export default {
    data() {
        return {
            rawMockProducts: [
                { id: 'mock-1', title: 'Fake Product', price: 99000 }
            ]
        }
    },
    computed: {
        allProducts() {
            if (this.products.length < 16) {
                return [...this.products, ...this.remainingMocks.slice(0, 5)];
            }
            return this.products;
        }
    }
}
</script>
"""
with tempfile.NamedTemporaryFile("w", suffix=".vue", delete=False, dir="resources/Frontend/js/Pages") as f:
    f.write(vue_test)
    temp_vue = Path(f.name)

try:
    fe_findings = scan_cms.scan_fe_file(temp_vue, root)
    print("Vue Options API & Padding Detection Test:", len(fe_findings), "finding(s)")
    for f in fe_findings:
        print(" - Found:", f["category"], f["symbol"])
    assert any("rawMockProducts" in f["symbol"] or "rawMockProducts" in f["category"] for f in fe_findings), "Failed to detect rawMockProducts in data()!"
    assert any("padding" in f["symbol"] or "Padding" in f["category"] for f in fe_findings), "Failed to detect array padding logic!"
finally:
    temp_vue.unlink()

print("ALL SCANNER DETECTION TESTS PASSED 100%!")
