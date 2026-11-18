---
name: fe-mobile-audit
description: Audits a whole frontend codebase for mobile UI problems (overflow, fixed widths, 100vh, hover-only menus, small tap targets, tiny text, iOS input zoom, missing mobile nav, tables, safe-area, deep spacing audit py-16+/px-0/gap-8+, and banner aspect-ratio) and uses the project's existing dependency/code graph to rank issues by how many pages they affect. Use when the user asks to check mobile/responsive UI, find where the mobile layout is not standard, review breakpoints, check spacing/gaps/padding on mobile, or "kiểm tra giao diện mobile / responsive".
---

# FE Mobile Audit (graph-aware)

## Goal
Find where the mobile UI is not up to standard, prove it with evidence (file, line, rule, and optionally a rendered screenshot), and rank fixes by impact using the codebase graph (shared components first).

## Use this skill when
- The user wants the whole FE source scanned for mobile/responsive issues.
- A page looks broken on phones and the user wants the root cause and every place it occurs.
- Before a release, to produce a mobile-readiness report.

## Do not use this skill when
- The task is a pixel-perfect comparison with a Figma design for one component (read the design and the component directly).
- The project has no frontend code.

## Instructions

### 1. Locate the graph
Look for an existing graph of the FE source, in this order:
1. A graph/dependency JSON in the repo (for example `graph.json`, `dependency-graph.json`, `code-graph.json`, madge or dependency-cruiser output, any `*graph*.json`).
2. A graph tool or MCP server available in this workspace (query it for "importers of file X" and "pages that render component Y").
3. Generated docs/wiki that describe the component tree.
If none exists, the scanner builds an import graph from source itself. Tell the user which graph was used and its coverage (`edges mapped x/y` in the output).

### 2. Run the static scan
```bash
python .agent/skills/fe-mobile-audit/scripts/mobile_audit.py . --graph auto --out docs/mobile
```
Options: `--graph path/to/graph.json` to force a graph, `--graph none` to ignore graphs, `--src src app` to limit folders. The script is Tailwind-aware (unprefixed utilities apply to phones; `sm:`/`md:` only apply to wider screens) and also checks CSS/SCSS, Vue, Svelte, Astro and HTML. Outputs `docs/mobile/MOBILE-AUDIT.md` and `mobile-audit.json`.

### 3. Optional runtime check (when a dev server is running)
```bash
npm i -D playwright && npx playwright install chromium
node .agent/skills/fe-mobile-audit/scripts/viewport_check.mjs --base http://localhost:3000 --routes / /products --out docs/mobile/runtime
```
Captures full-page screenshots at 320, 360, 390 and 768px and reports horizontal overflow, the widest offending elements, small tap targets, tiny text and small inputs. Open the screenshots and look at them. Skip this step if no server can be started, and say so.

### 4. Triage with the graph
- Start with files that have the highest `risk` (severity x pages affected): global layout, header/nav, footer, shared cards, form inputs, global CSS.
- For each page in "Pages ranked by exposure", name which shared components cause most of its issues.
- When a finding looks like a false positive (for example a responsive override in a parent, or the mobile menu lives in an imported component), follow the graph edges and read those files before deciding.

### 5. Verify before reporting
Read the code around every Critical/High finding. Confirm the problem would occur at 320-390px given the whole cascade (parent wrappers, `overflow-hidden`, container queries, design tokens such as custom `text-*` sizes). Mark anything unproven as **Needs confirmation**. Use `references/mobile-checklist.md` for the standards and fix patterns.

### 6. Report
Write `docs/mobile/MOBILE-REPORT.md`:
1. Summary: counts by severity, top 5 problems, quick wins.
2. Priority table: file, rule, evidence (file:line), affected pages (from the graph), fix, effort (S/M/L).
3. Page-by-page exposure list.
4. What was not checked (real devices, Safari-only bugs, animations, third-party widgets).

### 7. Fix (only after the user agrees)
Fix shared components first so one change repairs many pages. Use mobile-first changes (base classes for phones, `md:`/`lg:` for larger screens). After fixing, re-run the scan (and runtime check if available) and report the before/after counts.

## Constraints
- Do not change code during the audit; propose changes first.
- Static findings are heuristics. Never present them as confirmed layout bugs without reading the code or seeing a render.
- Do not invent graph data. If the graph is missing or partially mapped, say so.
- Do not disable zoom, hide content, or remove features to "fix" mobile issues.
- Keep design tokens and the existing component library; do not introduce a new styling system.
