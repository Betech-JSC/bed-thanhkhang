import { test, expect, Page } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

const ARTIFACT_DIR =
    'C:\\Users\\GiaKhiem\\.gemini\\antigravity\\brain\\34570d4c-ef72-4fe3-bf46-547ee585a04f';

const EXPECTED_ROOT_CATEGORIES_VI = [
    'Linh kiện thang cuốn',
    'Linh kiện thang nâng ô tô',
    'Hệ thống băng tải hành lý',
    'Linh kiện dây chuyền đóng gói',
];

const EXPECTED_ROOT_CATEGORIES_EN = [
    'Escalator Spare Parts',
    'Car Parking Lift Components',
    'Baggage Conveyor Systems',
    'Packaging Line Components',
];

interface NetworkTracker {
    consoleErrors: string[];
    pageErrors: string[];
    badResponses: Array<{ url: string; status: number }>;
    requestFailed: Array<{ url: string; reason: string }>;
}

function attachTrackers(page: Page): NetworkTracker {
    const tracker: NetworkTracker = {
        consoleErrors: [],
        pageErrors: [],
        badResponses: [],
        requestFailed: [],
    };

    page.on('console', (msg) => {
        if (msg.type() === 'error') {
            tracker.consoleErrors.push(msg.text());
        }
    });

    page.on('pageerror', (err) => {
        tracker.pageErrors.push(err.message || String(err));
    });

    page.on('response', (res) => {
        if (res.status() >= 400) {
            tracker.badResponses.push({ url: res.url(), status: res.status() });
        }
    });

    page.on('requestfailed', (req) => {
        const failure = req.failure()?.errorText || 'unknown';
        // Ignore cancelled requests triggered by client navigation
        if (!failure.includes('ERR_ABORTED')) {
            tracker.requestFailed.push({ url: req.url(), reason: failure });
        }
    });

    return tracker;
}

async function scrollEntirePageForLazyAssets(page: Page): Promise<void> {
    // Trigger any lazy-loaded hero carousel slides to ensure all slide images load cleanly
    const slideTabs = page.locator('section[aria-roledescription="carousel"] [role="tab"]');
    const slideCount = await slideTabs.count();
    if (slideCount > 1) {
        for (let i = 1; i < slideCount; i++) {
            await slideTabs.nth(i).click();
            await page.waitForTimeout(200);
        }
        await slideTabs.nth(0).click();
        await page.waitForTimeout(200);
    }

    await page.evaluate(async () => {
        const totalHeight = document.documentElement.scrollHeight;
        const step = Math.max(300, Math.floor(window.innerHeight * 0.7));
        for (let y = 0; y <= totalHeight; y += step) {
            window.scrollTo(0, y);
            await new Promise((r) => setTimeout(r, 120));
        }
        window.scrollTo(0, totalHeight);
        await new Promise((r) => setTimeout(r, 200));
        window.scrollTo(0, 0);
        await new Promise((r) => setTimeout(r, 150));
    });
    await page.waitForLoadState('networkidle');
    await page.evaluate(() => document.fonts.ready);
}

async function inspectImagesAndOverflow(page: Page) {
    return page.evaluate(() => {
        const isElementHidden = (el: Element): boolean => {
            let cur: Element | null = el;
            while (cur) {
                const s = window.getComputedStyle(cur);
                if (s.display === 'none' || s.visibility === 'hidden') {
                    return true;
                }
                cur = cur.parentElement;
            }
            return el.getClientRects().length === 0;
        };

        const imgs = Array.from(document.querySelectorAll('img'));
        const visibleOrLoadedImgs = imgs.map((img) => {
            const rect = img.getBoundingClientRect();
            const isHidden = isElementHidden(img);
            return {
                src: img.currentSrc || img.getAttribute('src') || '',
                alt: img.getAttribute('alt') || '',
                complete: img.complete,
                naturalWidth: img.naturalWidth,
                naturalHeight: img.naturalHeight,
                width: Math.round(rect.width * 100) / 100,
                height: Math.round(rect.height * 100) / 100,
                isHidden,
            };
        });

        const brokenImages = visibleOrLoadedImgs.filter(
            (img) => !img.isHidden && (!img.complete || img.naturalWidth === 0)
        );

        const scrollWidth = document.documentElement.scrollWidth;
        const innerWidth = window.innerWidth;

        return {
            totalImages: imgs.length,
            visibleImagesChecked: visibleOrLoadedImgs.filter((i) => !i.isHidden).length,
            brokenImages,
            scrollWidth,
            innerWidth,
            horizontalOverflowPx: Math.max(0, scrollWidth - innerWidth),
        };
    });
}

test.describe('Thành Khang Products Catalog & Home Regression QA (feat-page-products)', () => {
    test.beforeAll(() => {
        if (!fs.existsSync(ARTIFACT_DIR)) {
            fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
        }
    });

    test('1. Desktop /san-pham (1440x900): Hero, 4 Roots, L1/L2 Checkboxes, 3x3 Grid, Pagination 979:30474, Search EmptyState & Reset', async ({
        page,
    }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const tracker = attachTrackers(page);

        const response = await page.goto('/san-pham', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);

        await scrollEntirePageForLazyAssets(page);

        // 1.1 Check H1 & Hero Banner (#product-hero-banner)
        const hero = page.locator('#product-hero-banner');
        await expect(hero).toBeVisible();
        const heroH1 = hero.locator('h1');
        await expect(heroH1).toBeVisible();
        const heroH1Text = (await heroH1.innerText()).trim();
        expect(heroH1Text).toBe('SẢN PHẨM');

        const heroImg = hero.locator('picture img');
        await expect(heroImg).toBeVisible();
        const heroImgInfo = await heroImg.evaluate((el: HTMLImageElement) => ({
            src: el.getAttribute('src'),
            currentSrc: el.currentSrc,
            complete: el.complete,
            naturalWidth: el.naturalWidth,
            naturalHeight: el.naturalHeight,
        }));
        expect(heroImgInfo.currentSrc).toContain('/assets/figma/san-pham/hero-bg.webp');
        expect(heroImgInfo.complete).toBe(true);
        expect(heroImgInfo.naturalWidth).toBeGreaterThan(0);

        const heroBox = await hero.boundingBox();
        expect(heroBox).not.toBeNull();
        expect(Math.abs((heroBox?.height ?? 0) - 252)).toBeLessThanOrEqual(1);
        expect(Math.round(heroBox?.width ?? 0)).toBe(1440);

        // Capture full initial desktop screenshot
        const desktopScreenshotPath = path.join(ARTIFACT_DIR, 'qa-products-desktop-1440.png');
        await page.screenshot({ path: desktopScreenshotPath, fullPage: true });

        // 1.2 Sidebar Filter (ProductFilter): exactly 4 DB Bước 1 roots
        const aside = page.locator('section aside').first();
        await expect(aside).toBeVisible();
        const asideBox = await aside.boundingBox();
        expect(Math.round(asideBox?.width ?? 0)).toBe(264);

        const rootButtons = aside.locator('> div > div.w-full.flex.flex-col > div > button');
        const rootCount = await rootButtons.count();
        expect(rootCount).toBe(4);
        const rootTitles: string[] = [];
        for (let i = 0; i < rootCount; i++) {
            rootTitles.push((await rootButtons.nth(i).innerText()).trim());
        }
        expect(rootTitles).toEqual(EXPECTED_ROOT_CATEGORIES_VI);

        // Expand Root 4 ("Linh kiện dây chuyền đóng gói") as well so multiple roots & L1/L2 are measurable
        const root4Btn = rootButtons.nth(3);
        await root4Btn.click();
        await page.waitForTimeout(350);

        // Measure L1 checkboxes (gap 6-12px next to text, NOT > 40px) & L2 checkboxes (justify-between, right-aligned)
        const filterBoxMetrics = await aside.evaluate((asideEl) => {
            const l1Checkboxes = Array.from(
                asideEl.querySelectorAll('button[role="checkbox"]')
            ).filter((btn) => !btn.closest('.px-5'));

            const l2Checkboxes = Array.from(
                asideEl.querySelectorAll('.px-5 button[role="checkbox"]')
            );

            const measureGap = (btn: Element) => {
                const spans = Array.from(btn.children).filter(
                    (el) => el.tagName.toLowerCase() === 'span'
                );
                const labelEl = spans[0];
                const boxEl = spans[1];
                if (!labelEl || !boxEl) return null;
                const btnRect = btn.getBoundingClientRect();
                const labelRect = labelEl.getBoundingClientRect();
                const boxRect = boxEl.getBoundingClientRect();
                return {
                    text: (labelEl.textContent || '').trim(),
                    btnWidth: Math.round(btnRect.width * 100) / 100,
                    btnHeight: Math.round(btnRect.height * 100) / 100,
                    gapPx: Math.round((boxRect.left - labelRect.right) * 100) / 100,
                    rightEdgeDeltaPx: Math.round((btnRect.right - boxRect.right) * 100) / 100,
                    checkboxSize: `${Math.round(boxRect.width)}x${Math.round(boxRect.height)}`,
                    justifyContent: window.getComputedStyle(btn).justifyContent,
                    visible: btnRect.height > 0 && btnRect.width > 0,
                };
            };

            return {
                l1Items: l1Checkboxes.map(measureGap).filter((x) => x && x.visible),
                l2Items: l2Checkboxes.map(measureGap).filter((x) => x && x.visible),
            };
        });

        expect(filterBoxMetrics.l1Items.length).toBeGreaterThanOrEqual(2);
        expect(filterBoxMetrics.l2Items.length).toBeGreaterThanOrEqual(6);

        for (const item of filterBoxMetrics.l1Items) {
            expect(item!.gapPx).toBeGreaterThanOrEqual(6);
            expect(item!.gapPx).toBeLessThanOrEqual(12);
        }
        for (const item of filterBoxMetrics.l2Items) {
            expect(item!.justifyContent).toBe('space-between');
            expect(item!.rightEdgeDeltaPx).toBeLessThanOrEqual(1);
        }
        const wideGapL2 = filterBoxMetrics.l2Items.filter((item) => item!.gapPx > 20);
        expect(wideGapL2.length).toBeGreaterThanOrEqual(3);

        // 1.3 Product Grid: 3 columns, 9 cards on page 1, card rect ~264x371px
        const grid = page.locator('#product-catalog-section div.grid').first();
        await expect(grid).toBeVisible();
        const gridStats = await grid.evaluate((gridEl) => {
            const cs = window.getComputedStyle(gridEl);
            const cols = cs.gridTemplateColumns.trim().split(/\s+/).length;
            const cards = Array.from(gridEl.querySelectorAll('a.group'));
            const firstCardRect = cards[0]?.getBoundingClientRect();
            const cardRects = cards.map((c) => {
                const r = c.getBoundingClientRect();
                return {
                    title: (c.querySelector('h3')?.textContent || '').trim(),
                    width: Math.round(r.width * 100) / 100,
                    height: Math.round(r.height * 100) / 100,
                };
            });
            return {
                gridWidth: Math.round(gridEl.getBoundingClientRect().width * 100) / 100,
                columnCount: cols,
                cardCount: cards.length,
                firstCard: firstCardRect
                    ? {
                          width: Math.round(firstCardRect.width * 100) / 100,
                          height: Math.round(firstCardRect.height * 100) / 100,
                      }
                    : null,
                cardRects,
            };
        });

        expect(gridStats.columnCount).toBe(3);
        expect(gridStats.cardCount).toBe(9);
        expect(Math.abs((gridStats.firstCard?.width ?? 0) - 264)).toBeLessThanOrEqual(2);
        expect(Math.abs((gridStats.firstCard?.height ?? 0) - 371)).toBeLessThanOrEqual(5);

        // 1.4 Pagination (Figma 979:30474): NO Prev/Next, 40x40px cells, ~280px container for 6 items
        const paginationNav = page.locator('nav[aria-label="Phân trang sản phẩm"]');
        await expect(paginationNav).toBeVisible();

        const paginationMetrics = await paginationNav.evaluate((navEl) => {
            const container = navEl.querySelector('div.flex');
            const items = Array.from(container?.children || []);
            const texts = items.map((el) => (el.textContent || '').trim());
            const rects = items.map((el) => {
                const r = el.getBoundingClientRect();
                const cs = window.getComputedStyle(el);
                return {
                    tag: el.tagName.toLowerCase(),
                    text: (el.textContent || '').trim(),
                    width: Math.round(r.width * 100) / 100,
                    height: Math.round(r.height * 100) / 100,
                    top: Math.round(r.top * 100) / 100,
                    bg: cs.backgroundColor,
                    color: cs.color,
                    borderRadius: cs.borderRadius,
                    ariaCurrent: el.getAttribute('aria-current'),
                    ariaLabel: el.getAttribute('aria-label'),
                };
            });
            const rowTops = new Set(rects.map((r) => Math.round(r.top / 4)));
            const containerRect = container?.getBoundingClientRect();
            const hasPrevNext = items.some((el) => {
                const text = (el.textContent || '').trim().toLowerCase();
                const label = (el.getAttribute('aria-label') || '').toLowerCase();
                return (
                    text.includes('trước') ||
                    text.includes('tiếp') ||
                    text.includes('prev') ||
                    text.includes('next') ||
                    label.includes('prev') ||
                    label.includes('next') ||
                    label.includes('trước') ||
                    label.includes('tiếp') ||
                    el.querySelector('svg, img') !== null
                );
            });
            return {
                itemCount: items.length,
                texts,
                containerWidth: containerRect ? Math.round(containerRect.width * 100) / 100 : 0,
                containerHeight: containerRect ? Math.round(containerRect.height * 100) / 100 : 0,
                wrappedRowCount: rowTops.size,
                hasPrevNext,
                rects,
            };
        });

        expect(paginationMetrics.hasPrevNext).toBe(false);
        expect(paginationMetrics.itemCount).toBe(6);
        expect(paginationMetrics.texts).toEqual(['1', '2', '3', '4', '...', '12']);
        expect(paginationMetrics.wrappedRowCount).toBe(1);
        expect(Math.abs(paginationMetrics.containerWidth - 280)).toBeLessThanOrEqual(2);
        expect(Math.abs(paginationMetrics.containerHeight - 40)).toBeLessThanOrEqual(1);
        for (const r of paginationMetrics.rects) {
            expect(Math.abs(r.width - 40)).toBeLessThanOrEqual(1);
            expect(Math.abs(r.height - 40)).toBeLessThanOrEqual(1);
            expect(r.borderRadius).toBe('4px');
        }
        expect(paginationMetrics.rects[0].ariaCurrent).toBe('page');
        expect(paginationMetrics.rects[0].bg).toBe('rgb(0, 94, 164)');

        // Click Page 2 and verify pagination switches & products change
        const page1FirstTitle = gridStats.cardRects[0]?.title;
        await paginationNav.locator('button:text-is("2")').click();
        await page.waitForTimeout(300);

        await expect(paginationNav.locator('button:text-is("2")')).toHaveAttribute('aria-current', 'page');
        await expect(page.locator('#product-catalog-section p').first()).toContainText('Hiển thị 10–18 trên 103 sản phẩm');
        expect(page.url()).toContain('page=2');

        const page2FirstTitle = (await grid.locator('a.group h3').first().innerText()).trim();
        expect(page2FirstTitle).not.toBe(page1FirstTitle);

        // Scroll to show sidebar filter + grid + pagination clearly, capture screenshot
        await paginationNav.scrollIntoViewIfNeeded();
        await page.waitForTimeout(250);
        const filterPaginationScreenshotPath = path.join(
            ARTIFACT_DIR,
            'qa-products-desktop-filter-pagination.png'
        );
        await page.screenshot({ path: filterPaginationScreenshotPath, fullPage: false });

        // 1.5 Search non-matching query via Header SearchModal -> <EmptyState variant="light" /> -> Reset
        await page.evaluate(() => window.scrollTo(0, 0));
        await page.waitForTimeout(150);
        const searchBtn = page.locator('header button[aria-label="Tìm kiếm"]:visible').first();
        await searchBtn.click();

        const searchDialog = page.locator('[role="dialog"][aria-label="Tìm kiếm sản phẩm"]');
        await expect(searchDialog).toBeVisible();
        const searchInput = searchDialog.locator('input[type="text"]');
        await searchInput.fill('zzz-khong-ton-tai-qa-999');

        // Wait for search-v2 empty state inside modal so Enter triggers viewAllResults()
        await expect(searchDialog.getByText('Không tìm thấy sản phẩm phù hợp')).toBeVisible({
            timeout: 8000,
        });
        await searchInput.press('Enter');

        await expect(page).toHaveURL(/search=zzz-khong-ton-tai-qa-999/);
        const catalogEmptyHeading = page
            .locator('#product-catalog-section h3')
            .filter({ hasText: 'Không tìm thấy sản phẩm phù hợp' });
        await expect(catalogEmptyHeading).toBeVisible();
        await expect(page.locator('#product-catalog-section p').first()).toContainText('Hiển thị 0 sản phẩm');

        // Click "Đặt lại tất cả" and verify restoration
        const resetBtn = page.locator('#product-catalog-section button:has-text("Đặt lại tất cả")').first();
        await expect(resetBtn).toBeVisible();
        await resetBtn.click();
        await page.waitForTimeout(250);

        await expect(page.locator('#product-catalog-section p').first()).toContainText('Hiển thị 1–9 trên 103 sản phẩm');
        expect(await grid.locator('a.group').count()).toBe(9);

        // Final integrity checks
        const domCheck = await inspectImagesAndOverflow(page);

        console.log('=== [METRICS] DESKTOP /san-pham (1440x900) ===');
        console.log(
            JSON.stringify(
                {
                    heroBox,
                    heroImgInfo,
                    asideWidth: asideBox?.width,
                    rootCategories: rootTitles,
                    filterBoxMetrics,
                    gridStats: {
                        gridWidth: gridStats.gridWidth,
                        columnCount: gridStats.columnCount,
                        cardCount: gridStats.cardCount,
                        firstCard: gridStats.firstCard,
                    },
                    paginationMetrics: {
                        itemCount: paginationMetrics.itemCount,
                        texts: paginationMetrics.texts,
                        containerWidth: paginationMetrics.containerWidth,
                        containerHeight: paginationMetrics.containerHeight,
                        wrappedRowCount: paginationMetrics.wrappedRowCount,
                        hasPrevNext: paginationMetrics.hasPrevNext,
                        cellSizes: paginationMetrics.rects.map((r) => `${r.text}:${r.width}x${r.height}`),
                    },
                    domCheck,
                    consoleErrors: tracker.consoleErrors,
                    pageErrors: tracker.pageErrors,
                    badResponses: tracker.badResponses,
                },
                null,
                2
            )
        );

        expect(tracker.consoleErrors).toEqual([]);
        expect(tracker.pageErrors).toEqual([]);
        expect(tracker.badResponses).toEqual([]);
        expect(tracker.requestFailed).toEqual([]);
        expect(domCheck.brokenImages).toEqual([]);
        expect(domCheck.horizontalOverflowPx).toBe(0);
    });

    test('2. L1 without L2 children (v-else branch in ProductFilter.vue) vs L2 justify-between layout verification', async ({
        page,
    }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const tracker = attachTrackers(page);

        // Intercept initial HTML of /san-pham to inject an L1 leaf (no children) alongside an L1 with L2 children inside Root 1
        // so both branches of ProductFilter.vue are verified in real rendered DOM without modifying the DB.
        await page.route('**/san-pham', async (route, request) => {
            if (request.resourceType() !== 'document') {
                await route.continue();
                return;
            }
            const response = await route.fetch();
            let body = await response.text();
            const match = body.match(/data-page="([^"]+)"/);
            if (match) {
                const decoded = match[1]
                    .replace(/&quot;/g, '"')
                    .replace(/&amp;/g, '&')
                    .replace(/&#039;/g, "'")
                    .replace(/&lt;/g, '<')
                    .replace(/&gt;/g, '>');
                const pageData = JSON.parse(decoded);
                if (Array.isArray(pageData?.props?.categories) && pageData.props.categories[0]) {
                    pageData.props.categories[0].children = [
                        ...(pageData.props.categories[0].children || []),
                        {
                            id: 99901,
                            parent_id: pageData.props.categories[0].id,
                            slug: 'tay-vi-thang-cuon-qa',
                            title: 'Tay vịn thang cuốn',
                            name: 'Tay vịn thang cuốn',
                            children: [],
                            nodes: [],
                            subcategories: [],
                        },
                    ];
                }
                const reencoded = JSON.stringify(pageData)
                    .replace(/&/g, '&amp;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;');
                body = body.replace(match[0], `data-page="${reencoded}"`);
            }
            await route.fulfill({ response, body });
        });

        await page.goto('/san-pham', { waitUntil: 'networkidle' });
        const aside = page.locator('section aside').first();
        await expect(aside).toBeVisible();

        const metrics = await aside.evaluate((asideEl) => {
            const buttons = Array.from(asideEl.querySelectorAll('button[role="checkbox"]'));
            const l1LeafBtn = buttons.find((b) =>
                (b.textContent || '').includes('Tay vịn thang cuốn')
            );
            const l2Btn = asideEl.querySelector('.px-5 button[role="checkbox"]');

            const measure = (btn: Element | null | undefined) => {
                if (!btn) return null;
                const spans = Array.from(btn.children).filter(
                    (el) => el.tagName.toLowerCase() === 'span'
                );
                const labelRect = spans[0].getBoundingClientRect();
                const boxRect = spans[1].getBoundingClientRect();
                const btnRect = btn.getBoundingClientRect();
                return {
                    text: (spans[0].textContent || '').trim(),
                    btnWidth: Math.round(btnRect.width * 100) / 100,
                    gapPx: Math.round((boxRect.left - labelRect.right) * 100) / 100,
                    rightEdgeDeltaPx: Math.round((btnRect.right - boxRect.right) * 100) / 100,
                    justifyContent: window.getComputedStyle(btn).justifyContent,
                };
            };

            return {
                l1Leaf: measure(l1LeafBtn),
                l2Child: measure(l2Btn),
            };
        });

        console.log('=== [METRICS] L1 LEAF (no children) vs L2 CHILD ===');
        console.log(JSON.stringify(metrics, null, 2));

        expect(metrics.l1Leaf).not.toBeNull();
        expect(metrics.l1Leaf!.justifyContent).toBe('flex-start');
        expect(metrics.l1Leaf!.gapPx).toBeGreaterThanOrEqual(6);
        expect(metrics.l1Leaf!.gapPx).toBeLessThanOrEqual(12);
        expect(metrics.l1Leaf!.rightEdgeDeltaPx).toBeGreaterThan(40);

        expect(metrics.l2Child).not.toBeNull();
        expect(metrics.l2Child!.justifyContent).toBe('space-between');
        expect(metrics.l2Child!.rightEdgeDeltaPx).toBeLessThanOrEqual(1);

        // Also verify a short-label L2 child ("Xích tay vịn") has wide gap (> 40px) pushing checkbox flush right
        const shortL2Gap = await aside.evaluate((asideEl) => {
            const btn = Array.from(asideEl.querySelectorAll('.px-5 button[role="checkbox"]')).find((b) =>
                (b.textContent || '').includes('Xích tay vịn')
            );
            if (!btn) return null;
            const spans = Array.from(btn.children).filter((el) => el.tagName.toLowerCase() === 'span');
            const labelRect = spans[0].getBoundingClientRect();
            const boxRect = spans[1].getBoundingClientRect();
            const btnRect = btn.getBoundingClientRect();
            return {
                text: (spans[0].textContent || '').trim(),
                gapPx: Math.round((boxRect.left - labelRect.right) * 100) / 100,
                rightEdgeDeltaPx: Math.round((btnRect.right - boxRect.right) * 100) / 100,
                justifyContent: window.getComputedStyle(btn).justifyContent,
            };
        });
        expect(shortL2Gap).not.toBeNull();
        expect(shortL2Gap!.justifyContent).toBe('space-between');
        expect(shortL2Gap!.rightEdgeDeltaPx).toBeLessThanOrEqual(1);
        expect(shortL2Gap!.gapPx).toBeGreaterThan(40);

        expect(tracker.consoleErrors).toEqual([]);
        expect(tracker.pageErrors).toEqual([]);
        expect(tracker.badResponses).toEqual([]);
    });

    test('3. Desktop /en/products (1440x900): Status 200, H1 PRODUCTS, 4 EN Root Categories, 0 errors/overflow', async ({
        page,
    }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const tracker = attachTrackers(page);

        const response = await page.goto('/en/products', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);

        await scrollEntirePageForLazyAssets(page);

        const hero = page.locator('#product-hero-banner');
        await expect(hero).toBeVisible();
        await expect(hero.locator('h1')).toHaveText('PRODUCTS');

        const heroBox = await hero.boundingBox();
        expect(Math.abs((heroBox?.height ?? 0) - 252)).toBeLessThanOrEqual(1);

        const aside = page.locator('section aside').first();
        await expect(aside).toBeVisible();
        const rootButtons = aside.locator('> div > div.w-full.flex.flex-col > div > button');
        const rootCount = await rootButtons.count();
        expect(rootCount).toBe(4);
        const enRootTitles: string[] = [];
        for (let i = 0; i < rootCount; i++) {
            enRootTitles.push((await rootButtons.nth(i).innerText()).trim());
        }
        expect(enRootTitles).toEqual(EXPECTED_ROOT_CATEGORIES_EN);

        const gridCardsCount = await page.locator('#product-catalog-section div.grid a.group').count();
        expect(gridCardsCount).toBe(9);

        const domCheck = await inspectImagesAndOverflow(page);

        console.log('=== [METRICS] DESKTOP /en/products (1440x900) ===');
        console.log(
            JSON.stringify(
                {
                    heroBox,
                    enRootTitles,
                    gridCardsCount,
                    domCheck,
                    consoleErrors: tracker.consoleErrors,
                    pageErrors: tracker.pageErrors,
                    badResponses: tracker.badResponses,
                },
                null,
                2
            )
        );

        expect(tracker.consoleErrors).toEqual([]);
        expect(tracker.pageErrors).toEqual([]);
        expect(tracker.badResponses).toEqual([]);
        expect(tracker.requestFailed).toEqual([]);
        expect(domCheck.brokenImages).toEqual([]);
        expect(domCheck.horizontalOverflowPx).toBe(0);
    });

    test('4. Mobile /san-pham (375x812 & 390x844): Aspect 390/240, 2-col Grid, 1-row Pagination >=44px, Touch Targets >=44px', async ({
        browser,
    }) => {
        const context = await browser.newContext({
            viewport: { width: 375, height: 812 },
            hasTouch: true,
            isMobile: true,
            deviceScaleFactor: 2,
        });
        const page = await context.newPage();
        const tracker = attachTrackers(page);

        try {
            const response = await page.goto('/san-pham', { waitUntil: 'networkidle' });
            expect(response?.status()).toBe(200);

            await scrollEntirePageForLazyAssets(page);

            // 4.1 Hero Banner at 375x812 (~230.77px)
            const hero = page.locator('#product-hero-banner');
            await expect(hero).toBeVisible();
            const heroBox375 = await hero.boundingBox();
            expect(Math.round(heroBox375?.width ?? 0)).toBe(375);
            expect(Math.abs((heroBox375?.height ?? 0) - (375 * 240) / 390)).toBeLessThanOrEqual(2);

            // 4.2 Grid 2 columns at 375x812
            const grid = page.locator('#product-catalog-section div.grid').first();
            const gridStats375 = await grid.evaluate((el) => ({
                cols: window.getComputedStyle(el).gridTemplateColumns.trim().split(/\s+/).length,
                cardCount: el.querySelectorAll('a.group').length,
            }));
            expect(gridStats375.cols).toBe(2);
            expect(gridStats375.cardCount).toBe(9);

            // 4.3 Pagination 1 single row, no Prev/Next, all items >= 44x44px
            const paginationNav = page.locator('nav[aria-label="Phân trang sản phẩm"]');
            await expect(paginationNav).toBeVisible();
            const pagination375 = await paginationNav.evaluate((navEl) => {
                const container = navEl.querySelector('div.flex');
                const items = Array.from(container?.children || []);
                const rects = items.map((el) => {
                    const r = el.getBoundingClientRect();
                    return {
                        text: (el.textContent || '').trim(),
                        width: Math.round(r.width * 100) / 100,
                        height: Math.round(r.height * 100) / 100,
                        top: Math.round(r.top * 100) / 100,
                    };
                });
                const rowTops = new Set(rects.map((r) => Math.round(r.top / 4)));
                const hasPrevNext = items.some((el) => {
                    const text = (el.textContent || '').trim().toLowerCase();
                    const label = (el.getAttribute('aria-label') || '').toLowerCase();
                    return (
                        text.includes('trước') ||
                        text.includes('tiếp') ||
                        label.includes('prev') ||
                        label.includes('next') ||
                        el.querySelector('svg, img') !== null
                    );
                });
                return {
                    itemCount: items.length,
                    wrappedRowCount: rowTops.size,
                    hasPrevNext,
                    rects,
                };
            });

            expect(pagination375.hasPrevNext).toBe(false);
            expect(pagination375.wrappedRowCount).toBe(1);
            for (const r of pagination375.rects) {
                expect(r.width).toBeGreaterThanOrEqual(44);
                expect(r.height).toBeGreaterThanOrEqual(44);
            }

            // Capture full mobile screenshot at 375x812
            const mobile375Path = path.join(ARTIFACT_DIR, 'qa-products-mobile-375.png');
            await page.screenshot({ path: mobile375Path, fullPage: true });

            // 4.4 Mobile Touch Targets (>= 44px) at 375x812
            const filterOpenBtn = page.locator('#product-catalog-section button:has-text("Bộ lọc")').first();
            await expect(filterOpenBtn).toBeVisible();
            const filterOpenBox = await filterOpenBtn.boundingBox();
            expect(filterOpenBox?.height ?? 0).toBeGreaterThanOrEqual(44);

            const sortBtn = page.locator('#product-catalog-section button[aria-haspopup="listbox"]').first();
            await expect(sortBtn).toBeVisible();
            const sortBtnBox = await sortBtn.boundingBox();
            expect(sortBtnBox?.height ?? 0).toBeGreaterThanOrEqual(44);

            // Open Sort Dropdown & check all options height >= 44px
            await sortBtn.click();
            const sortOptionLocator = page.locator('#product-catalog-section [role="listbox"] button[role="option"]');
            await expect(sortOptionLocator.first()).toBeVisible();
            const sortOptionHeights = await sortOptionLocator.evaluateAll((els) =>
                els.map((el) => ({
                    label: (el.textContent || '').trim(),
                    height: Math.round(el.getBoundingClientRect().height * 100) / 100,
                }))
            );
            expect(sortOptionHeights.length).toBe(4);
            for (const opt of sortOptionHeights) {
                expect(opt.height).toBeGreaterThanOrEqual(44);
            }
            // Close sort dropdown
            await sortBtn.click();

            // Open Mobile Filter Drawer
            await filterOpenBtn.click();
            const drawer = page.locator('aside[role="dialog"][aria-label="Bộ lọc sản phẩm"]');
            await expect(drawer).toBeVisible();
            await page.waitForTimeout(350);

            // Measure L1, Chevron Toggle, L2 touch targets inside Mobile Drawer
            const drawerTargets = await drawer.evaluate((drawerEl) => {
                const l1Checkboxes = Array.from(
                    drawerEl.querySelectorAll('button[role="checkbox"]')
                ).filter((btn) => !btn.closest('.px-5'));
                const l2Checkboxes = Array.from(
                    drawerEl.querySelectorAll('.px-5 button[role="checkbox"]')
                );
                const chevrons = Array.from(
                    drawerEl.querySelectorAll('button[aria-label="Mở rộng danh mục con"]')
                );

                const toMetrics = (els: Element[]) =>
                    els
                        .map((el) => {
                            const r = el.getBoundingClientRect();
                            return {
                                text: (el.textContent || '').trim() || el.getAttribute('aria-label') || 'chevron',
                                width: Math.round(r.width * 100) / 100,
                                height: Math.round(r.height * 100) / 100,
                            };
                        })
                        .filter((m) => m.width > 0 && m.height > 0);

                return {
                    l1: toMetrics(l1Checkboxes),
                    l2: toMetrics(l2Checkboxes),
                    chevrons: toMetrics(chevrons),
                };
            });

            expect(drawerTargets.l1.length).toBeGreaterThanOrEqual(1);
            expect(drawerTargets.l2.length).toBeGreaterThanOrEqual(1);
            expect(drawerTargets.chevrons.length).toBeGreaterThanOrEqual(1);

            for (const t of drawerTargets.l1) expect(t.height).toBeGreaterThanOrEqual(44);
            for (const t of drawerTargets.l2) expect(t.height).toBeGreaterThanOrEqual(44);
            for (const t of drawerTargets.chevrons) {
                expect(t.width).toBeGreaterThanOrEqual(44);
                expect(t.height).toBeGreaterThanOrEqual(44);
            }

            // Screenshot open Mobile Drawer at 375x812
            const mobileDrawerPath = path.join(ARTIFACT_DIR, 'qa-products-mobile-drawer-375.png');
            await page.screenshot({ path: mobileDrawerPath, fullPage: false });

            // Select an L2 filter inside drawer, close drawer, verify active chip & reset touch targets >= 44px
            await drawer.locator('.px-5 button[role="checkbox"]').first().click();
            await drawer.locator('button[aria-label="Đóng bộ lọc"]').click();
            await page.waitForTimeout(300);

            const resetAllMobileBtn = page
                .locator('#product-catalog-section button:has-text("Đặt lại tất cả")')
                .first();
            await expect(resetAllMobileBtn).toBeVisible();
            const resetAllBox = await resetAllMobileBtn.boundingBox();
            expect(resetAllBox?.height ?? 0).toBeGreaterThanOrEqual(44);

            const removeChipBtn = page
                .locator('#product-catalog-section button[aria-label^="Xoá bộ lọc"]')
                .first();
            await expect(removeChipBtn).toBeVisible();
            const removeChipBox = await removeChipBtn.boundingBox();
            expect(removeChipBox?.width ?? 0).toBeGreaterThanOrEqual(44);
            expect(removeChipBox?.height ?? 0).toBeGreaterThanOrEqual(44);

            await resetAllMobileBtn.click();
            await page.waitForTimeout(200);

            const domCheck375 = await inspectImagesAndOverflow(page);
            expect(domCheck375.brokenImages).toEqual([]);
            expect(domCheck375.horizontalOverflowPx).toBe(0);

            // 4.5 Check 390x844 viewport as well
            await page.setViewportSize({ width: 390, height: 844 });
            await page.waitForTimeout(250);

            const heroBox390 = await hero.boundingBox();
            expect(Math.round(heroBox390?.width ?? 0)).toBe(390);
            expect(Math.abs((heroBox390?.height ?? 0) - 240)).toBeLessThanOrEqual(1);

            const gridCols390 = await grid.evaluate(
                (el) => window.getComputedStyle(el).gridTemplateColumns.trim().split(/\s+/).length
            );
            expect(gridCols390).toBe(2);

            const pagination390 = await paginationNav.evaluate((navEl) => {
                const container = navEl.querySelector('div.flex');
                const items = Array.from(container?.children || []);
                const rects = items.map((el) => {
                    const r = el.getBoundingClientRect();
                    return {
                        text: (el.textContent || '').trim(),
                        width: Math.round(r.width * 100) / 100,
                        height: Math.round(r.height * 100) / 100,
                        top: Math.round(r.top * 100) / 100,
                    };
                });
                const rowTops = new Set(rects.map((r) => Math.round(r.top / 4)));
                return { wrappedRowCount: rowTops.size, rects };
            });
            expect(pagination390.wrappedRowCount).toBe(1);
            for (const r of pagination390.rects) {
                expect(r.width).toBeGreaterThanOrEqual(44);
                expect(r.height).toBeGreaterThanOrEqual(44);
            }

            const domCheck390 = await inspectImagesAndOverflow(page);
            expect(domCheck390.brokenImages).toEqual([]);
            expect(domCheck390.horizontalOverflowPx).toBe(0);

            console.log('=== [METRICS] MOBILE /san-pham (375x812 & 390x844) ===');
            console.log(
                JSON.stringify(
                    {
                        heroBox375,
                        heroBox390,
                        gridStats375,
                        gridCols390,
                        pagination375,
                        pagination390,
                        mobileTouchTargets: {
                            filterOpenBtn: filterOpenBox,
                            sortBtn: sortBtnBox,
                            sortOptions: sortOptionHeights,
                            drawerTargets,
                            resetAllBtn: resetAllBox,
                            removeChipBtn: removeChipBox,
                        },
                        domCheck375,
                        domCheck390,
                        consoleErrors: tracker.consoleErrors,
                        pageErrors: tracker.pageErrors,
                        badResponses: tracker.badResponses,
                    },
                    null,
                    2
                )
            );

            expect(tracker.consoleErrors).toEqual([]);
            expect(tracker.pageErrors).toEqual([]);
            expect(tracker.badResponses).toEqual([]);
            expect(tracker.requestFailed).toEqual([]);
        } finally {
            await context.close();
        }
    });

    test('5. Home Regression / (Desktop 1440x900 & Mobile 375x812): Status 200, 0 errors, 0 broken images, 0px overflow', async ({
        browser,
    }) => {
        // 5A. Desktop Home (1440x900)
        const desktopContext = await browser.newContext({
            viewport: { width: 1440, height: 900 },
        });
        const desktopPage = await desktopContext.newPage();
        const desktopTracker = attachTrackers(desktopPage);

        let desktopCheck: Awaited<ReturnType<typeof inspectImagesAndOverflow>>;
        try {
            const res = await desktopPage.goto('/', { waitUntil: 'networkidle' });
            expect(res?.status()).toBe(200);
            await scrollEntirePageForLazyAssets(desktopPage);

            await expect(desktopPage.locator('header').first()).toBeVisible();
            await expect(desktopPage.locator('h1').first()).toBeVisible();
            await expect(desktopPage.locator('footer').first()).toBeVisible();

            desktopCheck = await inspectImagesAndOverflow(desktopPage);
            await desktopPage.screenshot({
                path: path.join(ARTIFACT_DIR, 'qa-home-regression-desktop.png'),
                fullPage: true,
            });
        } finally {
            await desktopContext.close();
        }

        // 5B. Mobile Home (375x812)
        const mobileContext = await browser.newContext({
            viewport: { width: 375, height: 812 },
            hasTouch: true,
            isMobile: true,
            deviceScaleFactor: 2,
        });
        const mobilePage = await mobileContext.newPage();
        const mobileTracker = attachTrackers(mobilePage);

        let mobileCheck: Awaited<ReturnType<typeof inspectImagesAndOverflow>>;
        try {
            const res = await mobilePage.goto('/', { waitUntil: 'networkidle' });
            expect(res?.status()).toBe(200);
            await scrollEntirePageForLazyAssets(mobilePage);

            await expect(mobilePage.locator('header').first()).toBeVisible();
            await expect(mobilePage.locator('h1').first()).toBeVisible();
            await expect(mobilePage.locator('footer').first()).toBeVisible();

            mobileCheck = await inspectImagesAndOverflow(mobilePage);
            await mobilePage.screenshot({
                path: path.join(ARTIFACT_DIR, 'qa-home-regression-mobile.png'),
                fullPage: true,
            });
        } finally {
            await mobileContext.close();
        }

        console.log('=== [METRICS] HOME REGRESSION / (Desktop 1440x900 & Mobile 375x812) ===');
        console.log(
            JSON.stringify(
                {
                    desktop: {
                        domCheck: desktopCheck,
                        consoleErrors: desktopTracker.consoleErrors,
                        pageErrors: desktopTracker.pageErrors,
                        badResponses: desktopTracker.badResponses,
                    },
                    mobile: {
                        domCheck: mobileCheck,
                        consoleErrors: mobileTracker.consoleErrors,
                        pageErrors: mobileTracker.pageErrors,
                        badResponses: mobileTracker.badResponses,
                    },
                },
                null,
                2
            )
        );

        expect(desktopTracker.consoleErrors).toEqual([]);
        expect(desktopTracker.pageErrors).toEqual([]);
        expect(desktopTracker.badResponses).toEqual([]);
        expect(desktopTracker.requestFailed).toEqual([]);
        expect(desktopCheck.brokenImages).toEqual([]);
        expect(desktopCheck.horizontalOverflowPx).toBe(0);

        expect(mobileTracker.consoleErrors).toEqual([]);
        expect(mobileTracker.pageErrors).toEqual([]);
        expect(mobileTracker.badResponses).toEqual([]);
        expect(mobileTracker.requestFailed).toEqual([]);
        expect(mobileCheck.brokenImages).toEqual([]);
        expect(mobileCheck.horizontalOverflowPx).toBe(0);
    });
});
