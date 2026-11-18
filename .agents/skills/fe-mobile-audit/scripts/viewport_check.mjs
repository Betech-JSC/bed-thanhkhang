#!/usr/bin/env node
/**
 * Runtime mobile check with Playwright (optional, complements mobile_audit.py).
 * Setup (once):  npm i -D playwright && npx playwright install chromium
 * Usage: node viewport_check.mjs --base http://localhost:3000 --routes / /products /about --out docs/mobile/runtime
 * Per route x device it reports: horizontal overflow + offending elements, tap targets < 44px,
 * text < 12px, inputs < 16px, images wider than the viewport, and saves a full-page screenshot.
 */
import fs from 'node:fs';
import path from 'node:path';

const args = process.argv.slice(2);
const opt = (k, d) => { const i = args.indexOf(k); return i >= 0 ? args[i + 1] : d; };
const base = (opt('--base', 'http://localhost:3000')).replace(/\/$/, '');
const out = opt('--out', 'docs/mobile/runtime');
const ri = args.indexOf('--routes');
const routes = ri >= 0 ? args.slice(ri + 1).filter(a => !a.startsWith('--')) : ['/'];
const devices = [
  { name: 'android-360', width: 360, height: 800, mobile: true },
  { name: 'iphone-390', width: 390, height: 844, mobile: true },
  { name: 'small-320', width: 320, height: 640, mobile: true },
  { name: 'tablet-768', width: 768, height: 1024, mobile: true },
];

let chromium;
try { ({ chromium } = await import('playwright')); }
catch { console.error('Playwright not installed. Run: npm i -D playwright && npx playwright install chromium'); process.exit(2); }

fs.mkdirSync(out, { recursive: true });
const browser = await chromium.launch();
const results = [];

function inspect() {
  const vw = window.innerWidth;
  const sel = (el) => {
    if (el.id) return `${el.tagName.toLowerCase()}#${el.id}`;
    const c = (el.className && typeof el.className === 'string') ? '.' + el.className.trim().split(/\s+/).slice(0, 3).join('.') : '';
    return el.tagName.toLowerCase() + c;
  };
  const visible = (el) => { const r = el.getBoundingClientRect(); const s = getComputedStyle(el);
    return r.width > 0 && r.height > 0 && s.visibility !== 'hidden' && s.display !== 'none'; };
  const all = [...document.querySelectorAll('body *')].filter(visible);
  const doc = document.scrollingElement;
  const overflowPx = doc.scrollWidth - vw;
  const offenders = all.filter(el => { const r = el.getBoundingClientRect(); return r.right > vw + 1 || r.left < -1; })
    .map(el => ({ el: sel(el), right: Math.round(el.getBoundingClientRect().right), width: Math.round(el.getBoundingClientRect().width) }))
    .sort((a, b) => b.right - a.right).slice(0, 12);
  const taps = [...document.querySelectorAll('a[href], button, [role=button], input:not([type=hidden]), select, textarea, summary')]
    .filter(visible).map(el => ({ el, r: el.getBoundingClientRect() }))
    .filter(x => x.r.width < 44 || x.r.height < 44)
    .map(x => ({ el: sel(x.el), text: (x.el.innerText || x.el.getAttribute('aria-label') || '').trim().slice(0, 30), w: Math.round(x.r.width), h: Math.round(x.r.height) }));
  const smallText = all.filter(el => el.childNodes.length && [...el.childNodes].some(n => n.nodeType === 3 && n.textContent.trim()))
    .filter(el => parseFloat(getComputedStyle(el).fontSize) < 12)
    .map(el => ({ el: sel(el), size: parseFloat(getComputedStyle(el).fontSize), text: el.innerText.trim().slice(0, 30) }));
  const smallInputs = [...document.querySelectorAll('input:not([type=checkbox]):not([type=radio]):not([type=hidden]), textarea, select')]
    .filter(visible).filter(el => parseFloat(getComputedStyle(el).fontSize) < 16)
    .map(el => ({ el: sel(el), size: parseFloat(getComputedStyle(el).fontSize) }));
  const meta = document.querySelector('meta[name=viewport]');
  return { vw, overflowPx, offenders, tapTargets: taps.length, tapSamples: taps.slice(0, 15),
           smallTextCount: smallText.length, smallTextSamples: smallText.slice(0, 10),
           smallInputs: smallInputs.slice(0, 10), viewportMeta: meta ? meta.content : null };
}

for (const d of devices) {
  const ctx = await browser.newContext({ viewport: { width: d.width, height: d.height }, isMobile: d.mobile, hasTouch: true, deviceScaleFactor: 2 });
  for (const r of routes) {
    const page = await ctx.newPage();
    const url = base + r;
    try {
      await page.goto(url, { waitUntil: 'networkidle', timeout: 45000 });
      await page.waitForTimeout(500);
      const data = await page.evaluate(inspect);
      const shot = path.join(out, `${d.name}${r.replace(/[^\w]+/g, '_') || '_home'}.png`);
      await page.screenshot({ path: shot, fullPage: true });
      results.push({ device: d.name, route: r, shot, ...data });
    } catch (e) { results.push({ device: d.name, route: r, error: String(e).slice(0, 200) }); }
    await page.close();
  }
  await ctx.close();
}
await browser.close();

fs.writeFileSync(path.join(out, 'runtime-audit.json'), JSON.stringify(results, null, 1));
const md = ['# Runtime mobile check', '', '| Device | Route | H-overflow | Tap<44px | Text<12px | Input<16px | Notes |', '|---|---|---|---|---|---|---|'];
for (const r of results) {
  if (r.error) { md.push(`| ${r.device} | ${r.route} | ERROR | | | | ${r.error} |`); continue; }
  md.push(`| ${r.device} | ${r.route} | ${r.overflowPx > 0 ? '**' + r.overflowPx + 'px**' : 'ok'} | ${r.tapTargets} | ${r.smallTextCount} | ${r.smallInputs.length} | ${r.offenders[0] ? 'widest: ' + r.offenders[0].el : ''} |`);
}
fs.writeFileSync(path.join(out, 'RUNTIME-AUDIT.md'), md.join('\n'));
console.log(md.join('\n'));
