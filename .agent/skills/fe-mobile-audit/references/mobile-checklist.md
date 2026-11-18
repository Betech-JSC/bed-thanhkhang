# Mobile UI standards and fix patterns

## Test widths
320 (smallest), 360 (common Android), 375/390/414 (iPhone), 768 (tablet portrait). No horizontal scroll at any width.

## Rules this skill checks
| ID | Problem | Standard | Fix pattern (Tailwind / CSS) |
|---|---|---|---|
| M01 | No viewport meta | `width=device-width, initial-scale=1` | Add meta tag (Next.js adds it by default) |
| M02 | Zoom disabled | Never block pinch-zoom | Remove `user-scalable=no`, `maximum-scale=1` |
| M03 | Fixed width > 360px | Content must fit 320px | `w-full max-w-[600px]`, `md:w-[600px]` |
| M04 | `100vw` / `w-screen` | Ignores scrollbar, causes overflow | `w-full` |
| M05 | `100vh` / `h-screen` | Taller than visible area on mobile | `min-h-dvh`, `100dvh` with `100vh` fallback |
| M06 | Hover-only menu | Touch has no hover | Click toggle + `aria-expanded`; `@media (hover: hover)` for hover styles |
| M07 | Tap target < 44px | 44x44px (iOS) / 48dp (Android) | Enlarge hit area with padding (`size-11`), keep icon small |
| M08 | Text < 12px | Body 14-16px, captions >= 12px | `text-sm`/`text-base` |
| M09 | Input font < 16px | iOS zooms inputs under 16px | `text-base md:text-sm` |
| M10 | 3+ columns on phone | 1-2 columns on mobile | `grid-cols-1 sm:grid-cols-2 lg:grid-cols-4` |
| M11 | `whitespace-nowrap` | Must not overflow 320px | `whitespace-normal md:whitespace-nowrap`, `truncate` |
| M12 | Table without scroll | Tables scroll or stack | `<div class="overflow-x-auto">` or card layout |
| M13 | Fixed bottom bar | Respect iPhone home indicator | `pb-[env(safe-area-inset-bottom)]` + `viewport-fit=cover` |
| M14 | Large absolute offsets | Avoid off-screen overflow | Responsive offsets, `overflow-hidden` parent |
| M15 | No responsive handling | Layout verified at 320-390 | Mobile-first stacking |
| M16 | Wide fixed image | Images scale down | `w-full h-auto`, `sizes`/`srcset` |
| M18 | Nav hidden, no menu | Mobile must have navigation | Hamburger + drawer (`Sheet`/`Drawer`) below `md` |
| M20 | Huge text/padding on phone | Scale with breakpoints | `text-3xl md:text-5xl`, `px-4 md:px-12` |
| M21 | Section padding Y too large | Mobile max 32-40px (`py-8`/`py-10`) | `py-8 md:py-16`, `py-10 lg:py-20` (no fixed `py-16+` on mobile) |
| M22 | Banner missing aspect-ratio | Proportional height across viewports | `aspect-[375/250] md:aspect-[1440/500]` + `object-cover` |
| M23 | Container gutter anomaly | Screen edge margin 16-20px | `px-4 md:px-8` (cấm `px-0` dính kính, cấm `px-12` bóp nội dung) |
| M24 | Card/Grid gap too large | Mobile gap 12-16px | `gap-3 md:gap-6`, `gap-4 lg:gap-8` (no `gap-8+` on mobile) |

## Mobile-first reminder (Tailwind)
Unprefixed classes apply to every screen including phones. `md:` means "768px and up". `hidden md:flex` hides on phones; make sure another element shows on phones.

## Mobile nav pattern (React sketch)
```tsx
const [open, setOpen] = useState(false)
<button className="md:hidden size-11" aria-expanded={open} aria-controls="mnav" onClick={() => setOpen(!open)}><MenuIcon/></button>
<nav id="mnav" className={`${open ? 'block' : 'hidden'} md:flex`}>...</nav>
```

## Dropdown that works on touch
Use state toggled by click/tap, close on outside click and Escape. Keep `group-hover:` only as an enhancement inside `@media (hover: hover)`.

## Things static analysis cannot see
Real Safari behavior, keyboard covering inputs, scroll-jank, animation performance, third-party iframes/widgets, and content that overflows only with real data (long names, long prices). Check these with the runtime script, real devices or BrowserStack.
