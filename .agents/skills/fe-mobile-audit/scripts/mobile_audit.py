#!/usr/bin/env python3
"""
FE mobile-readiness static audit + import-graph impact ranking (stdlib only).

Usage:
  python mobile_audit.py [ROOT] [--graph auto|none|PATH] [--out DIR] [--src SUBDIR ...] [--max-per-rule N]

What it does
  1. Scans FE source (html/css/scss/js/jsx/ts/tsx/vue/svelte/astro) for common mobile problems
     (Tailwind-aware, mobile-first: unprefixed utilities apply to phones).
  2. Builds an import graph from source (and merges an existing graph JSON if found/provided).
  3. Ranks files by how many pages they affect, and lists per-page risk.
Outputs DIR/mobile-audit.json and DIR/MOBILE-AUDIT.md (default DIR = docs/mobile).
These are heuristics: confirm each finding at 320/360/390 px before fixing.
"""
import os, re, sys, json, math, argparse, bisect, collections

SKIP_DIRS = {'node_modules', '.git', 'dist', 'build', '.next', '.nuxt', '.output', '.svelte-kit', 'coverage',
             '.turbo', '.cache', 'out', 'vendor', '__pycache__', '.vercel', '.expo', 'storybook-static', '.agent'}
CODE_EXT = {'.js', '.jsx', '.ts', '.tsx', '.vue', '.svelte', '.astro', '.html', '.htm', '.mdx'}
CSS_EXT = {'.css', '.scss', '.sass', '.less'}
RESOLVE_EXT = ['.tsx', '.ts', '.jsx', '.js', '.vue', '.svelte', '.astro', '.mjs', '.css', '.scss', '.html']
SEV_W = {'Critical': 5, 'High': 3, 'Medium': 2, 'Low': 1, 'Info': 0}
SEV_ORDER = {'Critical': 0, 'High': 1, 'Medium': 2, 'Low': 3, 'Info': 4}
RESP = {'sm', 'md', 'lg', 'xl', '2xl'}

# ------------------------------------------------------------------ helpers
def read(path, limit=1_500_000):
    try:
        if os.path.getsize(path) > limit: return None
        with open(path, encoding='utf-8', errors='replace') as f: return f.read()
    except Exception:
        return None

def split_variants(tok):
    parts, cur, depth = [], '', 0
    for ch in tok:
        if ch in '[(': depth += 1
        elif ch in '])': depth = max(0, depth - 1)
        if ch == ':' and depth == 0: parts.append(cur); cur = ''
        else: cur += ch
    return parts, cur.lstrip('!')

def applies_to_mobile(variants):
    """Tailwind is mobile-first: sm:/md:/min-*: only kick in on wider screens."""
    for v in variants:
        if v in RESP or v.startswith('min-') or v.startswith('@'): return False
    return True

STR_RE = re.compile(r'"([^"]*)"|\'([^\']*)\'|`([^`]*)`', re.S)
def class_tokens(text):
    toks = []
    for m in STR_RE.finditer(text):
        s = m.group(1) or m.group(2) or m.group(3) or ''
        s = re.sub(r'\$\{[^}]*\}', ' ', s)
        for t in s.split():
            if re.match(r'^[!\w\-\[\]\.\/%#:(),@&>*=_\'"+~^|]+$', t) and re.search(r'[a-zA-Z]', t):
                toks.append(t)
    return toks

TAG_RE = re.compile(
    r'<([A-Za-z][\w\.\-:]*)((?:[^<>{}"\']|"[^"]*"|\'[^\']*\'|\{(?:[^{}]|\{(?:[^{}]|\{[^{}]*\})*\})*\})*)/?>', re.S)

def num(s):
    try: return float(s)
    except Exception: return None

def tw_px(tok_base, prefix):
    """Return px value of utilities like w-[420px], w-[30rem], w-96 -> px, else None."""
    m = re.match(r'^' + prefix + r'-\[(\d+(?:\.\d+)?)(px|rem)\]$', tok_base)
    if m: return float(m.group(1)) * (16 if m.group(2) == 'rem' else 1)
    m = re.match(r'^' + prefix + r'-(\d+(?:\.\d+)?)$', tok_base)
    if m: return float(m.group(1)) * 4
    return None

# ------------------------------------------------------------------ findings
class Findings:
    def __init__(self, cap): self.items, self.seen, self.cap, self.count = [], set(), cap, collections.Counter()
    def add(self, rule, sev, file, line, msg, fix, snippet=''):
        key = (rule, file, line)
        if key in self.seen: return
        self.seen.add(key)
        self.count[rule] += 1
        if self.count[rule] > self.cap: return
        self.items.append({'rule': rule, 'severity': sev, 'file': file, 'line': line, 'message': msg,
                           'fix': fix, 'snippet': snippet.strip()[:160]})

# ------------------------------------------------------------------ scanning
NAV_FILE = re.compile(r'(nav|header|menu|topbar|appbar|sidebar)', re.I)
MOBILE_MENU = re.compile(r'(hamburger|burger|mobile-?menu|MobileMenu|MobileNav|Drawer|Sheet|Offcanvas|menu-?toggle|'
                         r'isMenuOpen|menuOpen|mobileOpen|openMenu|aria-expanded|aria-controls|lucide-react.*\bMenu\b|MenuIcon|RiMenu|FiMenu|Bars3)', re.I)
HOVER_JS = re.compile(r'(onMouseEnter|onMouseOver|onMouseLeave|@mouseenter|@mouseover|v-on:mouseenter|addEventListener\(\s*[\'"]mouse(enter|over))')
TOUCH_JS = re.compile(r'(onClick|onTouchStart|onPointerDown|onFocus|@click|v-on:click|@touchstart|focus-within|aria-expanded)')

def scan_code(rel, text, F, markers):
    line_starts = [0]
    for m in re.finditer(r'\n', text): line_starts.append(m.end())
    L = lambda pos: bisect.bisect_right(line_starts, pos)
    ext = os.path.splitext(rel)[1].lower()
    is_html = ext in ('.html', '.htm')
    layout_tokens = resp_tokens = 0
    nav_hidden_line = None

    if MOBILE_MENU.search(text): markers.add(rel)

    # --- viewport (only for standalone html entry files)
    if is_html and re.search(r'<html', text, re.I):
        vm = re.search(r'<meta[^>]+name=["\']viewport["\'][^>]*>', text, re.I)
        if not vm:
            F.add('M01', 'Critical', rel, 1, 'Missing <meta name="viewport"> - mobile browsers render at ~980px and shrink the page.',
                  'Add <meta name="viewport" content="width=device-width, initial-scale=1"> in <head>.')
        elif re.search(r'user-scalable\s*=\s*(no|0)|maximum-scale\s*=\s*1(\.0)?\b', vm.group(0), re.I):
            F.add('M02', 'High', rel, L(text.find(vm.group(0))), 'Viewport disables pinch-zoom (accessibility failure).',
                  'Remove user-scalable=no / maximum-scale=1.', vm.group(0))
    for m in re.finditer(r'(user-scalable\s*[:=]\s*(no|0)|maximumScale\s*:\s*1\b)', text):
        if not is_html:
            F.add('M02', 'High', rel, L(m.start()), 'Viewport config disables zoom.', 'Allow zoom (remove userScalable:false / maximumScale:1).', m.group(0))

    # --- per-tag analysis
    for m in TAG_RE.finditer(text):
        name, attrs = m.group(1), m.group(2)
        if not attrs.strip(): continue
        line = L(m.start())
        toks = class_tokens(attrs)
        parsed = [(t, *split_variants(t)) for t in toks]  # (raw, variants, base)
        base_mobile = [(raw, b) for raw, v, b in parsed if applies_to_mobile(v)]
        mob_bases = [b for _, b in base_mobile]
        lname = name.lower()
        snippet = text[m.start():m.start() + 140].replace('\n', ' ')

        # responsive accounting
        for raw, v, b in parsed:
            if re.match(r'^(flex-row|flex-col|grid-cols-\d+|w-\[|w-\d+/\d+|basis-|columns-\d+|hidden|block|flex|grid)', b):
                layout_tokens += 1
                if not applies_to_mobile(v): resp_tokens += 1

        # widths
        for b in mob_bases:
            for prefix in ('w', 'min-w'):
                px = tw_px(b, prefix)
                if px and px > 360 and not (prefix == 'w' and re.match(r'^w-\d+$', b) and px < 384):
                    F.add('M03', 'High' if px >= 420 else 'Medium', rel, line,
                          f'Fixed {prefix} ≈ {int(px)}px applies on mobile (phones are 320-430px wide).',
                          f'Use w-full + max-w-[{int(px)}px], or add a mobile-first width and widen with md:.', snippet)
            if b == 'w-screen':
                F.add('M04', 'Medium', rel, line, 'w-screen = 100vw ignores the scrollbar and often causes horizontal scroll.',
                      'Use w-full (or max-w-full).', snippet)
            if b in ('h-screen', 'min-h-screen', 'max-h-screen') or re.match(r'^(min-|max-)?h-\[100vh\]$', b):
                F.add('M05', 'Medium', rel, line, '100vh is taller than the visible area on mobile browsers (URL bar).',
                      'Use h-dvh / min-h-dvh (Tailwind 3.4+) or h-svh, or min-h-[100dvh].', snippet)
            mt = re.match(r'^text-\[(\d+(?:\.\d+)?)px\]$', b)
            if mt and float(mt.group(1)) < 12:
                F.add('M08', 'Medium', rel, line, f'Text {mt.group(1)}px is too small to read on phones (min ~12px).',
                      'Use text-xs (12px) or larger; body text 14-16px.', snippet)
            if mt and float(mt.group(1)) >= 40:
                F.add('M20', 'Medium', rel, line, f'Large fixed font {mt.group(1)}px on mobile may overflow.',
                      'Scale: text-3xl md:text-5xl (or clamp()).', snippet)
            if b in ('text-5xl', 'text-6xl', 'text-7xl', 'text-8xl', 'text-9xl'):
                F.add('M20', 'Medium', rel, line, f'{b} applies on mobile and may overflow narrow screens.',
                      'Use a smaller base size with a responsive step-up (text-3xl md:text-5xl).', snippet)
            mp = re.match(r'^p[xlr]-(\d+)$', b)
            if mp and int(mp.group(1)) >= 12:
                F.add('M23', 'Low', rel, line, f'{b} = {int(mp.group(1))*4}px side padding on mobile leaves little content width.',
                      'Use px-4 md:px-12.', snippet)
            mpy = re.match(r'^(?:py|pt|pb|my|mt|mb)-(\d+)$', b)
            if mpy and int(mpy.group(1)) >= 16:
                F.add('M21', 'Medium', rel, line, f'{b} = {int(mpy.group(1))*4}px vertical padding/margin on mobile creates excessive empty space.',
                      'Scale: py-8 md:py-16 or py-10 lg:py-20.', snippet)
            mgap = re.match(r'^gap-(\d+)$', b)
            if mgap and int(mgap.group(1)) >= 8:
                F.add('M24', 'Low', rel, line, f'{b} = {int(mgap.group(1))*4}px gap on mobile creates disjointed spacing between items.',
                      'Scale: gap-3 md:gap-6 or gap-4 lg:gap-8.', snippet)
            # Banner aspect-ratio check
            if any(k in lname or k in attrs.lower() for k in ('banner', 'hero', 'slider', 'carousel')):
                has_aspect = any(x.startswith('aspect-') for x in mob_bases)
                fixed_h = [x for x in mob_bases if re.match(r'^h-(\d+|\[\d+px\])$', x) and tw_px(x, 'h') and tw_px(x, 'h') >= 320]
                if fixed_h and not has_aspect:
                    F.add('M22', 'Medium', rel, line, f'Banner element has fixed height {fixed_h[0]} on mobile without aspect-ratio.',
                          'Use aspect-[W_mb/H_mb] md:aspect-[W_dt/H_dt] (e.g. aspect-[375/250] md:aspect-[1440/500]) with object-cover.', snippet)
            mo = re.match(r'^-?(left|right)-\[(-?\d+)px\]$', b)
            if mo and abs(int(mo.group(2))) >= 100:
                F.add('M14', 'Low', rel, line, f'Large absolute offset {b} can push content off-screen on mobile.',
                      'Constrain with responsive prefixes or overflow-hidden on the parent.', snippet)
            mg = re.match(r'^grid-cols-(\d+)$', b)
            if mg and int(mg.group(1)) >= 3 and 'grid-cols-1' not in mob_bases and 'grid-cols-2' not in mob_bases:
                F.add('M10', 'Medium', rel, line, f'{b} on mobile gives ~{int(320/int(mg.group(1)))}px per column at 320px.',
                      'Use grid-cols-1 sm:grid-cols-2 lg:grid-cols-' + mg.group(1) + '.', snippet)
            mc = re.match(r'^columns-(\d+)$', b)
            if mc and int(mc.group(1)) >= 3:
                F.add('M10', 'Low', rel, line, f'{b} multi-column text on mobile.', 'Use columns-1 md:columns-' + mc.group(1), snippet)

        # nowrap
        if 'whitespace-nowrap' in mob_bases and not any(x in mob_bases for x in ('truncate', 'overflow-hidden', 'text-ellipsis', 'overflow-x-auto')):
            F.add('M11', 'Low', rel, line, 'whitespace-nowrap without truncation/scroll may overflow at 320-360px.',
                  'Allow wrapping on mobile (whitespace-normal md:whitespace-nowrap) or add truncate/overflow handling.', snippet)

        # hover dropdown
        gh = [raw for raw, v, b in parsed if any(x.startswith('group-hover') or x == 'hover' for x in v) and
              re.match(r'^(block|flex|grid|visible|opacity-100|pointer-events-auto|scale-100|translate-y-0|max-h-)', b)]
        if any('group-hover' in raw for raw in gh):
            F.add('M06', 'High', rel, line, 'Content revealed only on group-hover (dropdown/tooltip) - not reachable by touch.',
                  'Add click/tap toggle (aria-expanded state) or focus-within; keep hover as enhancement for pointer devices.', snippet)

        # nav hidden on mobile
        if NAV_FILE.search(rel) or NAV_FILE.search(name):
            if 'hidden' in mob_bases and any(re.match(r'^(md|lg|xl):(flex|block|inline-flex|grid|inline-block)$', raw) for raw, v, b in parsed
                                            if len(v) == 1 and (v[0] + ':' + b) == raw):
                nav_hidden_line = nav_hidden_line or line

        # tap targets
        if lname in ('button', 'iconbutton', 'a', 'link', 'navlink', 'nuxtlink') or 'role="button"' in attrs:
            hs = [tw_px(b, 'h') for b in mob_bases]; ws = [tw_px(b, 'w') for b in mob_bases]; ss = [tw_px(b, 'size') for b in mob_bases]
            small = [x for x in hs + ws + ss if x is not None and x < 40]
            if small and lname in ('button', 'iconbutton') or (small and 'role="button"' in attrs):
                F.add('M07', 'Medium', rel, line, f'Tap target ≈ {int(min(small))}px (< 44px iOS / 48px Android guideline).',
                      'Make the hit area >= 44x44px (e.g. size-11 or p-3) - keep the icon small, enlarge the clickable box.', snippet)
        # inputs (iOS zoom)
        if lname in ('input', 'textarea', 'select', 'textfield', 'inputbase') and not re.search(r'type\s*=\s*["\'](checkbox|radio|hidden|range|file)', attrs):
            small_text = [b for b in mob_bases if b in ('text-xs', 'text-sm') or
                          (re.match(r'^text-\[(\d+(?:\.\d+)?)px\]$', b) and float(re.match(r'^text-\[(\d+(?:\.\d+)?)px\]$', b).group(1)) < 16)]
            if small_text and 'text-base' not in mob_bases:
                F.add('M09', 'High', rel, line, f'Input font {small_text[0]} (<16px) makes iOS Safari zoom the page on focus.',
                      'Use text-base on mobile (text-base md:text-sm).', snippet)
        # images
        if lname in ('img', 'image') :
            mw = re.search(r'\bwidth\s*=\s*[{"\']?\s*(\d{3,4})', attrs)
            if mw and int(mw.group(1)) > 360 and not any(re.match(r'^(w-full|max-w-|w-\[?100%)', b) for b in mob_bases) and 'fill' not in attrs:
                F.add('M16', 'Medium', rel, line, f'Image width={mw.group(1)} without max-width/w-full may overflow on phones.',
                      'Add w-full h-auto (or max-w-full) and use responsive sizes/srcset.', snippet)
        # tables
        if lname in ('table',):
            before = text[max(0, m.start() - 600):m.start()]
            if not re.search(r'overflow-(x-)?(auto|scroll)|table-responsive|overflow-x', before):
                F.add('M12', 'Medium', rel, line, '<table> without a horizontal-scroll wrapper will overflow on phones.',
                      'Wrap in <div className="overflow-x-auto"> or switch to stacked card layout on mobile.', snippet)
        # fixed bottom bars
        if 'fixed' in mob_bases and any(re.match(r'^bottom-', b) for b in mob_bases) and not re.search(r'safe-area|env\(|pb-safe|safe-bottom', text):
            F.add('M13', 'Low', rel, line, 'Fixed bottom element without safe-area padding (iPhone home indicator).',
                  'Add padding-bottom: env(safe-area-inset-bottom) and viewport-fit=cover.', snippet)

    # --- file-level
    if nav_hidden_line and not MOBILE_MENU.search(text):
        F.add('M18', 'High', rel, nav_hidden_line,
              'Navigation is hidden on mobile (hidden + md:flex) but no mobile menu trigger was found in this file.',
              'Provide a hamburger/drawer for < md. (Checked again against imported files in graph pass.)')
    if HOVER_JS.search(text) and not TOUCH_JS.search(text):
        mm = HOVER_JS.search(text)
        F.add('M06', 'Medium', rel, L(mm.start()), 'Hover handlers with no click/touch/focus alternative in this file.',
              'Add onClick/onFocus toggle so touch users can open the same UI.', mm.group(0))
    if layout_tokens >= 8 and resp_tokens == 0 and not re.search(r'useMediaQuery|matchMedia|useBreakpoint|isMobile|@media', text):
        F.add('M15', 'Low', rel, 1, f'{layout_tokens} layout utilities but no responsive variants (sm:/md:/lg:) in this file.',
              'Verify the layout at 320-390px; add mobile-first stacking or responsive variants.')

# ---------------------------------------------------------------------- CSS
def css_media_spans(text):
    spans, stack, last = [], [], 0
    for i, ch in enumerate(text):
        if ch == '{':
            prelude = text[last:i].strip(); stack.append((i, prelude)); last = i + 1
        elif ch == '}':
            if stack:
                s, p = stack.pop()
                if p.startswith('@media') or p.startswith('@container'): spans.append((s, i, p))
            last = i + 1
        elif ch == ';': last = i + 1
    return spans

def scan_css(rel, text, F):
    clean = re.sub(r'/\*.*?\*/', lambda m: re.sub(r'[^\n]', ' ', m.group(0)), text, flags=re.S)
    starts = [0]
    for m in re.finditer(r'\n', clean): starts.append(m.end())
    L = lambda pos: bisect.bisect_right(starts, pos)
    spans = css_media_spans(clean)
    has_media = bool(spans)
    fixed_px = 0
    for m in re.finditer(r'([^{}]+)\{([^{}]*)\}', clean):
        sel, body, pos = m.group(1).strip(), m.group(2), m.start(2)
        if sel.startswith('@'): continue
        medias = [p for s, e, p in spans if s < pos < e]
        in_min = any('min-width' in p for p in medias)
        line = L(m.start(1) + (len(m.group(1)) - len(m.group(1).lstrip())))
        decl = dict((d.split(':', 1)[0].strip().lower(), d.split(':', 1)[1].strip().lower())
                    for d in body.split(';') if ':' in d)
        for prop in ('width', 'min-width', 'flex-basis'):
            v = decl.get(prop, '')
            mv = re.match(r'^(\d+(?:\.\d+)?)px$', v)
            if mv and float(mv.group(1)) > 360 and not in_min:
                F.add('M03', 'Medium', rel, line, f'{prop}: {v} in "{sel[:50]}" applies on mobile.',
                      'Use max-width with width:100%, or wrap in @media (min-width: 768px).', f'{prop}: {v}')
            if mv: fixed_px += 1
            if re.search(r'100vw', v):
                F.add('M04', 'Medium', rel, line, f'{prop}: {v} can cause horizontal scroll (ignores scrollbar).', 'Use 100% instead.', f'{prop}: {v}')
        for prop in ('height', 'min-height', 'max-height'):
            if '100vh' in decl.get(prop, ''):
                F.add('M05', 'Medium', rel, line, f'{prop}: 100vh is taller than visible area on mobile browsers.',
                      'Use 100dvh (with 100vh fallback) or 100svh.', f'{prop}: {decl[prop]}')
        fs = decl.get('font-size', '')
        mf = re.match(r'^(\d+(?:\.\d+)?)(px|rem|em)$', fs)
        if mf:
            px = float(mf.group(1)) * (16 if mf.group(2) in ('rem', 'em') else 1)
            if px < 12:
                F.add('M08', 'Medium', rel, line, f'font-size {fs} too small for mobile.', 'Use >= 12px (body 16px).', f'font-size: {fs}')
            if px < 16 and re.search(r'(^|[\s,>+~])(input|textarea|select)\b|input\[', sel, re.I):
                F.add('M09', 'High', rel, line, f'Form control font-size {fs} (<16px) triggers iOS zoom on focus.',
                      'Set font-size: 16px on inputs for mobile.', f'{sel[:40]} {{ font-size: {fs} }}')
        if ':hover' in sel and re.search(r'(menu|dropdown|submenu|nav|tooltip|popover|mega)', sel, re.I) and \
           re.search(r'display\s*:\s*(block|flex|grid)|visibility\s*:\s*visible|opacity\s*:\s*1', body):
            F.add('M06', 'Medium', rel, line, f'Menu revealed only via :hover ("{sel[:50]}").',
                  'Add :focus-within / JS click toggle; wrap hover styles in @media (hover: hover).', sel[:100])
        if decl.get('position') == 'fixed' and 'bottom' in decl and 'safe-area' not in clean:
            F.add('M13', 'Low', rel, line, 'position: fixed bottom element without safe-area inset.',
                  'padding-bottom: env(safe-area-inset-bottom).', sel[:80])
    if not has_media and fixed_px >= 6:
        F.add('M15', 'Low', rel, 1, f'{fixed_px} fixed px widths and no @media rules in this stylesheet.',
              'Check how this stylesheet behaves at 320-390px.')

# ------------------------------------------------------------------- graph
def collect_files(root, src_dirs):
    files = []
    bases = [os.path.join(root, s) for s in src_dirs] if src_dirs else [root]
    for base in bases:
        for dp, dns, fns in os.walk(base):
            dns[:] = [d for d in dns if d not in SKIP_DIRS and not d.startswith('.')]
            for fn in fns:
                if '.min.' in fn or fn.endswith('.d.ts') or '.test.' in fn or '.spec.' in fn or '.stories.' in fn: continue
                ext = os.path.splitext(fn)[1].lower()
                if ext in CODE_EXT or ext in CSS_EXT:
                    files.append(os.path.relpath(os.path.join(dp, fn), root).replace('\\', '/'))
    return sorted(set(files))

IMPORT_RE = re.compile(r'''(?:import\s+(?:[^'"()]*?\s+from\s+)?|export\s+[^'"()]*?\s+from\s+|require\(\s*|import\(\s*|@import\s+(?:url\()?\s*|<script[^>]+src=|<link[^>]+href=|@use\s+)['"]([^'"]+)['"]''', re.S)

def load_aliases(root):
    aliases = {}
    for cfg in ('tsconfig.json', 'jsconfig.json', 'tsconfig.app.json'):
        t = read(os.path.join(root, cfg))
        if not t: continue
        t = re.sub(r'/\*.*?\*/|(?<!:)//[^\n]*', '', t, flags=re.S)
        try: j = json.loads(t)
        except Exception: continue
        co = j.get('compilerOptions', {}); base = co.get('baseUrl', '.')
        for k, v in (co.get('paths') or {}).items():
            if v: aliases[k.rstrip('*')] = os.path.normpath(os.path.join(base, v[0].rstrip('*'))).replace('\\', '/')
        if base not in ('.', './') and base: aliases[''] = os.path.normpath(base).replace('\\', '/')
    aliases.setdefault('@/', 'src/' if os.path.isdir(os.path.join(root, 'src')) else '')
    aliases.setdefault('~/', 'src/' if os.path.isdir(os.path.join(root, 'src')) else '')
    return aliases

def resolve(spec, frm, fileset, aliases):
    cands = []
    if spec.startswith('.'):
        cands.append(os.path.normpath(os.path.join(os.path.dirname(frm), spec)).replace('\\', '/'))
    else:
        for a, target in aliases.items():
            if a and spec.startswith(a):
                cands.append(os.path.normpath(target.rstrip('/') + '/' + spec[len(a):]).replace('\\', '/'))
        if '' in aliases: cands.append(os.path.normpath(aliases[''] + '/' + spec).replace('\\', '/'))
        if spec.startswith('src/'): cands.append(spec)
    for c in cands:
        if c in fileset: return c
        for e in RESOLVE_EXT:
            if c + e in fileset: return c + e
        for e in RESOLVE_EXT:
            if c + '/index' + e in fileset: return c + '/index' + e
    return None

def build_src_graph(root, files, aliases):
    fs = set(files); edges = collections.defaultdict(set)
    for f in files:
        t = read(os.path.join(root, f))
        if not t: continue
        for m in IMPORT_RE.finditer(t):
            r = resolve(m.group(1), f, fs, aliases)
            if r and r != f: edges[f].add(r)
    return edges

def suffix_index(files):
    idx = collections.defaultdict(set)
    for f in files:
        parts = f.split('/')
        for i in range(len(parts)):
            s = '/'.join(parts[i:]); idx[s].add(f); idx[os.path.splitext(s)[0]].add(f)
        stem = os.path.splitext(parts[-1])[0]
        idx['@@' + stem].add(f)
        if stem == 'index' and len(parts) > 1: idx['@@' + parts[-2]].add(f)
    return idx

def map_node(name, root_abs, idx):
    if not isinstance(name, str): return None
    n = name.replace('\\', '/')
    if n.startswith(root_abs): n = n[len(root_abs):]
    n = n.lstrip('./').lstrip('/')
    for key in (n, os.path.splitext(n)[0]):
        if key in idx and len(idx[key]) == 1: return next(iter(idx[key]))
    k = '@@' + os.path.splitext(os.path.basename(n))[0]
    if k in idx and len(idx[k]) == 1: return next(iter(idx[k]))
    return None

def load_external_graph(path, root, files):
    t = read(path, 80_000_000)
    if not t: return [], 0, 0
    try: j = json.loads(t)
    except Exception: return [], 0, 0
    idx = suffix_index(files); root_abs = os.path.abspath(root).replace('\\', '/') + '/'
    raw, nodes = [], {}
    def nid(x):
        if isinstance(x, dict): return x.get('id') or x.get('path') or x.get('file') or x.get('name') or x.get('label')
        return x
    if isinstance(j, dict) and ('nodes' in j or 'links' in j or 'edges' in j):
        for n in j.get('nodes', []) or []:
            if isinstance(n, dict):
                key = n.get('id', n.get('name'))
                val = n.get('path') or n.get('file') or n.get('filePath') or n.get('source_file') or n.get('name') or n.get('label') or key
                nodes[key] = val
        for e in (j.get('edges') or j.get('links') or []):
            if isinstance(e, dict):
                a = e.get('source', e.get('from', e.get('src'))); b = e.get('target', e.get('to', e.get('dst')))
                raw.append((nid(a), nid(b)))
            elif isinstance(e, (list, tuple)) and len(e) >= 2: raw.append((e[0], e[1]))
    elif isinstance(j, dict) and 'modules' in j:
        for mdl in j['modules']:
            for d in mdl.get('dependencies', []): raw.append((mdl.get('source'), d.get('resolved') or d.get('module')))
    elif isinstance(j, dict):
        for k, v in j.items():
            if isinstance(v, list):
                for d in v: raw.append((k, d if isinstance(d, str) else (d or {}).get('path')))
    elif isinstance(j, list):
        for e in j:
            if isinstance(e, (list, tuple)) and len(e) >= 2: raw.append((e[0], e[1]))
            elif isinstance(e, dict):
                raw.append((e.get('source', e.get('from')), e.get('target', e.get('to'))))
    out, ok = [], 0
    for a, b in raw:
        a2 = map_node(nodes.get(a, a), root_abs, idx); b2 = map_node(nodes.get(b, b), root_abs, idx)
        if a2 and b2 and a2 != b2: out.append((a2, b2)); ok += 1
    return out, ok, len(raw)

GRAPH_NAMES = re.compile(r'(^|[-_.])(graph|dependency-graph|dep-graph|code-graph|madge|depcruise|dependency-cruiser)[-_.\w]*\.json$|^graph\.json$', re.I)
def find_graph(root):
    hits = []
    for dp, dns, fns in os.walk(root):
        dns[:] = [d for d in dns if d not in ('node_modules', '.git', '.next', 'dist', 'build')]
        depth = dp[len(root):].count(os.sep)
        if depth > 4: dns[:] = []
        for fn in fns:
            if fn.endswith('.json') and GRAPH_NAMES.search(fn):
                p = os.path.join(dp, fn)
                if os.path.getsize(p) < 80_000_000: hits.append(p)
    return sorted(hits, key=lambda p: (p.count(os.sep), len(p)))

# ---------------------------------------------------------------- pages
def is_page(f):
    f2 = '/' + f
    if re.search(r'/app/(?:.*/)?page\.(t|j)sx?$', f2): return True
    if re.search(r'/(pages|views|routes|screens)/', f2) and re.search(r'\.(tsx|jsx|vue|svelte|astro|js|ts|html)$', f2):
        if re.search(r'/pages/(_app|_document|_error|api/)|/api/|\.d\.ts$|/components?/', f2): return False
        if os.path.basename(f).startswith('_') and 'app' in os.path.basename(f): return False
        return True
    if re.search(r'\+page\.svelte$', f2): return True
    return False

def global_seeds(f_all, page):
    seeds = set()
    for f in f_all:
        b = os.path.basename(f)
        if re.match(r'^(_app|_document|App|main|root|Layout)\.(t|j)sx?$', b) or b in ('+layout.svelte', 'App.vue', 'app.vue', 'default.vue'):
            seeds.add(f)
        if re.match(r'^(layout|template)\.(t|j)sx?$', b) and os.path.dirname(page).startswith(os.path.dirname(f)) and '/app' in '/' + f:
            seeds.add(f)
    return seeds

def reach(start, edges):
    seen, stack = set(), list(start)
    while stack:
        n = stack.pop()
        if n in seen: continue
        seen.add(n); stack.extend(edges.get(n, ()))
    return seen

# ------------------------------------------------------------------ main
def main():
    ap = argparse.ArgumentParser()
    ap.add_argument('root', nargs='?', default='.')
    ap.add_argument('--graph', default='auto', help='auto | none | path to graph JSON')
    ap.add_argument('--out', default='docs/mobile')
    ap.add_argument('--src', nargs='*', default=None, help='sub-folders to scan (default: whole repo)')
    ap.add_argument('--max-per-rule', type=int, default=200)
    a = ap.parse_args()
    root = os.path.abspath(a.root)
    files = collect_files(root, a.src)
    if not files: print('No frontend source files found under', root); sys.exit(1)
    F = Findings(a.max_per_rule); markers = set()
    for f in files:
        t = read(os.path.join(root, f))
        if t is None: continue
        ext = os.path.splitext(f)[1].lower()
        (scan_css if ext in CSS_EXT else scan_code)(*( (f, t, F) if ext in CSS_EXT else (f, t, F, markers) ))
    # graph
    edges = build_src_graph(root, files, load_aliases(root)); src_edges = sum(len(v) for v in edges.values())
    graph_note = f'source import graph: {src_edges} edges'
    ext_paths = []
    if a.graph == 'auto': ext_paths = find_graph(root)[:3]
    elif a.graph != 'none': ext_paths = [a.graph]
    for gp in ext_paths:
        ge, ok, total = load_external_graph(gp, root, files)
        for x, y in ge: edges[x].add(y)
        graph_note += f'; merged {os.path.relpath(gp, root)} ({ok}/{total} edges mapped to files)'
    # M18 downgrade using graph (mobile menu may live in an imported component)
    keep = []
    for it in F.items:
        if it['rule'] == 'M18':
            r = set(); frontier = {it['file']}
            for _ in range(2):
                nxt = set()
                for n in frontier: nxt |= edges.get(n, set())
                r |= nxt; frontier = nxt
            if r & markers or (set(reach([it['file']], edges)) & markers):
                it = dict(it, severity='Info', message=it['message'] + ' -> mobile menu found in an imported component; verify it works on touch.')
        keep.append(it)
    F.items = keep
    rev = collections.defaultdict(set)
    for s, ts in edges.items():
        for t in ts: rev[t].add(s)
    pages = [f for f in files if is_page(f)]
    page_reach = {}
    for p in pages:
        page_reach[p] = reach([p] + sorted(global_seeds(files, p)), edges)
    pages_of = collections.defaultdict(set)
    for p, rs in page_reach.items():
        for n in rs: pages_of[n].add(p)
    by_file = collections.defaultdict(list)
    for it in F.items: by_file[it['file']].append(it)
    file_rows = []
    for f, its in by_file.items():
        w = sum(SEV_W[i['severity']] for i in its)
        pa = len(pages_of.get(f, ())) if pages else 0
        file_rows.append({'file': f, 'findings': len(its), 'max_severity': min((i['severity'] for i in its), key=lambda s: SEV_ORDER[s]),
                          'importers': len(rev.get(f, ())), 'pages_affected': pa,
                          'risk': round(w * (1 + math.log2(1 + pa)), 1)})
    file_rows.sort(key=lambda r: -r['risk'])
    page_rows = []
    for p in pages:
        own = len(by_file.get(p, [])); inh = sum(len(by_file.get(n, [])) for n in page_reach[p] if n != p)
        top = sorted([n for n in page_reach[p] if by_file.get(n)], key=lambda n: -sum(SEV_W[i['severity']] for i in by_file[n]))[:3]
        page_rows.append({'page': p, 'own': own, 'inherited': inh, 'top_files': top})
    page_rows.sort(key=lambda r: -(r['own'] + r['inherited']))
    sev_count = collections.Counter(i['severity'] for i in F.items)
    rule_count = collections.Counter(i['rule'] for i in F.items)
    result = {'root': root, 'files_scanned': len(files), 'graph': graph_note, 'pages_detected': len(pages),
              'severity_counts': dict(sev_count), 'rule_counts': dict(rule_count),
              'files_ranked': file_rows, 'pages_ranked': page_rows,
              'findings': sorted(F.items, key=lambda i: (SEV_ORDER[i['severity']], i['file'], i['line']))}
    outdir = os.path.join(root, a.out); os.makedirs(outdir, exist_ok=True)
    with open(os.path.join(outdir, 'mobile-audit.json'), 'w', encoding='utf-8') as fh: json.dump(result, fh, ensure_ascii=False, indent=1)
    write_md(result, os.path.join(outdir, 'MOBILE-AUDIT.md'), pages_of)
    print(f"Scanned {len(files)} files | pages detected: {len(pages)} | {graph_note}")
    print('Findings:', dict(sev_count))
    print('Top risky files (risk = severity x pages affected):')
    for r in file_rows[:10]:
        print(f"  {r['risk']:6} {r['max_severity']:8} {r['findings']:3} findings, {r['pages_affected']:3} pages  {r['file']}")
    print('Report:', os.path.join(outdir, 'MOBILE-AUDIT.md'))

RULE_TITLES = {
    'M01': 'Missing viewport meta', 'M02': 'Zoom disabled', 'M03': 'Fixed width wider than phone', 'M04': '100vw / w-screen overflow',
    'M05': '100vh on mobile', 'M06': 'Hover-only interaction', 'M07': 'Small tap target', 'M08': 'Tiny text', 'M09': 'Input font < 16px (iOS zoom)',
    'M10': 'Multi-column grid on mobile', 'M11': 'nowrap overflow risk', 'M12': 'Table without scroll wrapper', 'M13': 'Fixed bottom without safe-area',
    'M14': 'Large absolute offset', 'M15': 'No responsive handling', 'M16': 'Image may overflow', 'M18': 'Nav hidden on mobile, no menu',
    'M20': 'Large font / padding on mobile'}

def write_md(r, path, pages_of):
    L = ['# Mobile UI audit (static)', '',
         f"- Files scanned: **{r['files_scanned']}** | Pages detected: **{r['pages_detected']}**", f"- Graph: {r['graph']}",
         '- Severity: ' + ', '.join(f"{k} {v}" for k, v in sorted(r['severity_counts'].items(), key=lambda kv: SEV_ORDER[kv[0]])),
         '- Heuristic scan: confirm each item at 320 / 360 / 390 px (and 768) before fixing.', '']
    L += ['## 1. Fix first (shared files hit the most pages)', '', '| Risk | Severity | Findings | Pages affected | Importers | File |', '|---|---|---|---|---|---|']
    for x in r['files_ranked'][:15]:
        L.append(f"| {x['risk']} | {x['max_severity']} | {x['findings']} | {x['pages_affected']} | {x['importers']} | `{x['file']}` |")
    L += ['', '## 2. Pages ranked by exposure', '', '| Page | Own | Inherited from components | Worst files |', '|---|---|---|---|']
    for x in r['pages_ranked'][:20]:
        L.append(f"| `{x['page']}` | {x['own']} | {x['inherited']} | " + ', '.join(f"`{t}`" for t in x['top_files']) + ' |')
    L += ['', '## 3. Findings by rule', '']
    byrule = collections.defaultdict(list)
    for it in r['findings']: byrule[it['rule']].append(it)
    for rule in sorted(byrule, key=lambda k: min(SEV_ORDER[i['severity']] for i in byrule[k])):
        its = byrule[rule]
        L += [f"### {rule} - {RULE_TITLES.get(rule, rule)} ({len(its)})", f"Fix: {its[0]['fix']}", '']
        for it in its[:40]:
            L.append(f"- [{it['severity']}] `{it['file']}:{it['line']}` - {it['message']}" + (f"  `{it['snippet'][:90]}`" if it['snippet'] else ''))
        if len(its) > 40: L.append(f"- ... {len(its) - 40} more (see mobile-audit.json)")
        L.append('')
    with open(path, 'w', encoding='utf-8') as fh: fh.write('\n'.join(L))

if __name__ == '__main__': main()
