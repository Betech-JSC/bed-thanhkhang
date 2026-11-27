import { test, expect, Page } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const ARTIFACT_DIR =
    'C:\\Users\\tu\\.gemini\\antigravity\\brain\\d4527d62-3271-47d8-ba89-ecaa6b3aae77';

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
        if (!failure.includes('ERR_ABORTED')) {
            tracker.requestFailed.push({ url: req.url(), reason: failure });
        }
    });

    return tracker;
}

function assertCleanRun(tracker: NetworkTracker, contextLabel: string) {
    expect(tracker.consoleErrors, `[${contextLabel}] console errors`).toEqual([]);
    expect(tracker.pageErrors, `[${contextLabel}] page errors`).toEqual([]);
    expect(tracker.badResponses, `[${contextLabel}] 4xx/5xx responses`).toEqual([]);
    expect(tracker.requestFailed, `[${contextLabel}] failed requests`).toEqual([]);
}

async function verifyVisibleHeaderImages(page: Page, contextLabel: string) {
    const imgStatus = await page.evaluate(() => {
        const isVisible = (el: Element): boolean => {
            let cur: Element | null = el;
            while (cur) {
                const style = window.getComputedStyle(cur);
                if (style.display === 'none' || style.visibility === 'hidden') {
                    return false;
                }
                cur = cur.parentElement;
            }
            return el.getClientRects().length > 0;
        };

        const headerImgs = Array.from(
            document.querySelectorAll('header img, #mobile-drawer img, div.bg-dark img')
        );
        const visibleImgs = headerImgs.filter((img) => isVisible(img));
        const broken = visibleImgs
            .filter((img) => {
                const htmlImg = img as HTMLImageElement;
                return !htmlImg.complete || htmlImg.naturalWidth === 0;
            })
            .map((img) => ({
                src: img.getAttribute('src') || '',
                complete: (img as HTMLImageElement).complete,
                naturalWidth: (img as HTMLImageElement).naturalWidth,
            }));

        return {
            visibleCount: visibleImgs.length,
            broken,
        };
    });

    expect(imgStatus.visibleCount, `[${contextLabel}] visible header images count`).toBeGreaterThan(0);
    expect(imgStatus.broken, `[${contextLabel}] broken header images`).toEqual([]);
}

const DESKTOP_VIEWPORTS = [
    { width: 1440, height: 900 },
    { width: 1280, height: 800 },
    { width: 1024, height: 768 },
] as const;

const LOCALES = [
    {
        code: 'vi',
        route: '/',
        productsLabel: 'SẢN PHẨM',
        nonDropdownLabel: 'TÀI LIỆU',
        cat1Title: 'Linh kiện thang cuốn',
        cat4Title: 'Linh kiện dây chuyền đóng gói',
        hotlineLabel: 'Gọi hotline',
    },
    {
        code: 'en',
        route: '/en',
        productsLabel: 'PRODUCTS',
        nonDropdownLabel: 'DOCUMENTS',
        cat1Title: 'Escalator Spare Parts',
        cat4Title: 'Packaging Line Components',
        hotlineLabel: 'Call Hotline',
    },
] as const;

test.describe('Header Products Mega-Menu Alignment & Regression', () => {
    test.beforeAll(() => {
        if (!fs.existsSync(ARTIFACT_DIR)) {
            fs.mkdirSync(ARTIFACT_DIR, { recursive: true });
        }
    });

    for (const localeCfg of LOCALES) {
        for (const vp of DESKTOP_VIEWPORTS) {
            test(`Desktop ${vp.width}x${vp.height} [${localeCfg.code.toUpperCase()}]: Mega-Menu aligns left-0 with ${localeCfg.productsLabel}, fits header/viewport for 1-col & 2-col categories`, async ({
                page,
            }, testInfo) => {
                test.skip(testInfo.project.name !== 'Desktop Chrome', 'Desktop test only');

                await page.setViewportSize({ width: vp.width, height: vp.height });
                const tracker = attachTrackers(page);

                const response = await page.goto(localeCfg.route, { waitUntil: 'networkidle' });
                expect(response?.status()).toBe(200);
                await page.evaluate(() => document.fonts.ready);

                const contextLabel = `${vp.width}x${vp.height}-${localeCfg.code}`;
                await verifyVisibleHeaderImages(page, contextLabel);

                const header = page.locator('header');
                await expect(header).toBeVisible();

                const container = header.locator('.app-container-narrow');
                await expect(container).toBeVisible();
                const containerBox = await container.boundingBox();
                expect(containerBox).not.toBeNull();
                const headerContentRight = containerBox!.x + containerBox!.width - 32;

                // Measure rightmost visible action element inside header .app-container-narrow
                const hotlineAction = container
                    .locator(`a[href^="tel:"]:has-text("${localeCfg.hotlineLabel}")`)
                    .first();
                let rightmostActionBox = await hotlineAction.boundingBox().catch(() => null);
                if (!rightmostActionBox) {
                    rightmostActionBox = await container
                        .locator('a:visible, button:visible')
                        .last()
                        .boundingBox();
                }
                expect(rightmostActionBox).not.toBeNull();
                const rightmostActionRight =
                    rightmostActionBox!.x + rightmostActionBox!.width;

                // Locate Products nav item & its .group/navitem wrapper
                const nav = header.locator('nav');
                await expect(nav).toBeVisible();
                const productsLink = nav.getByRole('link', {
                    name: localeCfg.productsLabel,
                    exact: true,
                });
                await expect(productsLink).toBeVisible();
                const productsNavGroup = productsLink.locator('..');
                await expect(productsNavGroup).toHaveClass(/group\/navitem/);

                const productsLinkBox = await productsLink.boundingBox();
                const productsItemBox = await productsNavGroup.boundingBox();
                expect(productsLinkBox).not.toBeNull();
                expect(productsItemBox).not.toBeNull();

                // Hover Products nav item to open desktop mega-menu
                const megaMenu = page.locator('[data-testid="desktop-mega-menu"]');
                await productsLink.hover();
                await expect(megaMenu).toBeVisible();
                await page.waitForTimeout(250);

                const megaMenuCard = megaMenu.locator('> div').first();
                await expect(megaMenuCard).toBeVisible();

                // 1-column category check (Category 1)
                const cat1Link = megaMenuCard
                    .getByRole('link', { name: localeCfg.cat1Title })
                    .first();
                await expect(cat1Link).toBeVisible();
                await cat1Link.hover();
                await page.waitForTimeout(150);

                const cardBox1 = await megaMenuCard.boundingBox();
                expect(cardBox1).not.toBeNull();

                const leftDeltaCat1 = Math.abs(cardBox1!.x - productsItemBox!.x);
                const linkLeftDeltaCat1 = Math.abs(cardBox1!.x - productsLinkBox!.x);

                const overflowCat1 = await page.evaluate(() => ({
                    scrollWidth: document.documentElement.scrollWidth,
                    innerWidth: window.innerWidth,
                }));

                // Project Tailwind config (resources/Frontend/tailwind.config.js) defines xl: '1440px'
                // So >=1440px uses xl (300+320=620px), while 1024px-1439px uses lg (240+280=520px)
                const expectedCat1Width = vp.width >= 1440 ? 620 : 520;
                console.log(
                    `[${contextLabel}][Cat1] productsItemBox.x=${productsItemBox!.x.toFixed(2)}, productsLinkBox.x=${productsLinkBox!.x.toFixed(2)}, cardBox1.x=${cardBox1!.x.toFixed(2)}, cardBox1.width=${cardBox1!.width.toFixed(2)}, cardBox1.right=${(cardBox1!.x + cardBox1!.width).toFixed(2)}, leftDelta=${leftDeltaCat1.toFixed(2)}, headerContentRight=${headerContentRight.toFixed(2)}, rightmostActionRight=${rightmostActionRight.toFixed(2)}, scrollWidth=${overflowCat1.scrollWidth}, innerWidth=${overflowCat1.innerWidth}`
                );

                expect(leftDeltaCat1).toBeLessThanOrEqual(2);
                expect(linkLeftDeltaCat1).toBeLessThanOrEqual(2);
                expect(Math.abs(cardBox1!.width - expectedCat1Width)).toBeLessThanOrEqual(8);
                expect(cardBox1!.x + cardBox1!.width).toBeLessThanOrEqual(vp.width);
                expect(overflowCat1.scrollWidth).toBeLessThanOrEqual(overflowCat1.innerWidth);

                if (vp.width === 1440) {
                    const cat1ScreenshotPath = path.join(
                        ARTIFACT_DIR,
                        `qa-header-${vp.width}-${localeCfg.code}-cat1.png`
                    );
                    await page.screenshot({
                        path: cat1ScreenshotPath,
                        clip: { x: 0, y: 0, width: vp.width, height: 600 },
                    });
                }

                // 2-column category check (Category 4)
                const cat4Link = megaMenuCard
                    .getByRole('link', { name: localeCfg.cat4Title })
                    .first();
                await expect(cat4Link).toBeVisible();

                const cat4Visibility = await cat4Link.evaluate((el) => {
                    const htmlEl = el as HTMLElement;
                    const textSpan = htmlEl.querySelector('span') as HTMLElement | null;
                    return {
                        offsetWidth: htmlEl.offsetWidth,
                        offsetHeight: htmlEl.offsetHeight,
                        textScrollHeight: textSpan?.scrollHeight ?? 0,
                        textClientHeight: textSpan?.clientHeight ?? 0,
                        textScrollWidth: textSpan?.scrollWidth ?? 0,
                        textClientWidth: textSpan?.clientWidth ?? 0,
                    };
                });
                expect(cat4Visibility.offsetWidth).toBeGreaterThan(0);
                expect(cat4Visibility.offsetHeight).toBeGreaterThan(0);
                expect(cat4Visibility.textScrollWidth).toBeLessThanOrEqual(
                    cat4Visibility.textClientWidth + 1
                );
                expect(cat4Visibility.textScrollHeight).toBeLessThanOrEqual(
                    cat4Visibility.textClientHeight + 2
                );

                await cat4Link.hover();
                await page.waitForTimeout(150);

                // Ensure right region renders 2-column grid for Category 4
                await expect(megaMenuCard.locator('.grid.grid-cols-2')).toBeVisible();

                const cardBox2 = await megaMenuCard.boundingBox();
                expect(cardBox2).not.toBeNull();

                const leftDeltaCat4 = Math.abs(cardBox2!.x - productsItemBox!.x);
                const horizontalShift = Math.abs(cardBox2!.x - cardBox1!.x);
                const overflowCat4 = await page.evaluate(() => ({
                    scrollWidth: document.documentElement.scrollWidth,
                    innerWidth: window.innerWidth,
                }));

                console.log(
                    `[${contextLabel}][Cat4] productsItemBox.x=${productsItemBox!.x.toFixed(2)}, cardBox2.x=${cardBox2!.x.toFixed(2)}, cardBox2.width=${cardBox2!.width.toFixed(2)}, cardBox2.right=${(cardBox2!.x + cardBox2!.width).toFixed(2)}, leftDelta=${leftDeltaCat4.toFixed(2)}, horizontalShift=${horizontalShift.toFixed(2)}, headerContentRight=${headerContentRight.toFixed(2)}, rightmostActionRight=${rightmostActionRight.toFixed(2)}, scrollWidth=${overflowCat4.scrollWidth}, innerWidth=${overflowCat4.innerWidth}`
                );

                expect(leftDeltaCat4).toBeLessThanOrEqual(2);
                expect(horizontalShift).toBeLessThanOrEqual(1);
                expect(cardBox2!.x + cardBox2!.width).toBeLessThanOrEqual(vp.width);
                expect(overflowCat4.scrollWidth).toBeLessThanOrEqual(overflowCat4.innerWidth);

                if (vp.width === 1440) {
                    expect(cardBox2!.x + cardBox2!.width).toBeLessThanOrEqual(
                        headerContentRight + 2
                    );
                }

                const cat4ScreenshotPath = path.join(
                    ARTIFACT_DIR,
                    `qa-header-${vp.width}-${localeCfg.code}-cat4.png`
                );
                await page.screenshot({
                    path: cat4ScreenshotPath,
                    clip: { x: 0, y: 0, width: vp.width, height: 600 },
                });

                await verifyVisibleHeaderImages(page, `${contextLabel}-end`);
                assertCleanRun(tracker, contextLabel);
            });
        }
    }

    test('Desktop 1440x900 [/]: Interactive open/close behavior (hover non-dropdown, chevron toggle, Escape, click outside)', async ({
        page,
    }, testInfo) => {
        test.skip(testInfo.project.name !== 'Desktop Chrome', 'Desktop test only');

        await page.setViewportSize({ width: 1440, height: 900 });
        const tracker = attachTrackers(page);

        const response = await page.goto('/', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);
        await page.evaluate(() => document.fonts.ready);

        const header = page.locator('header');
        const nav = header.locator('nav');
        const megaMenu = page.locator('[data-testid="desktop-mega-menu"]');
        const productsLink = nav.getByRole('link', { name: 'SẢN PHẨM', exact: true });
        const documentsLink = nav.getByRole('link', { name: 'TÀI LIỆU', exact: true });
        const chevronBtn = header.locator('button[aria-label="Mở danh mục sản phẩm"]');

        // 1. Hover Products -> opens mega-menu
        await productsLink.hover();
        await expect(megaMenu).toBeVisible();
        await page.waitForTimeout(200);

        // 2. Hover non-dropdown tab (TÀI LIỆU) -> closes mega-menu
        await documentsLink.hover();
        await page.waitForTimeout(250);
        await expect(megaMenu).toBeHidden();

        // 3. Clicking Products chevron toggles it open; pressing Escape closes it
        // Move pointer onto chevron (mouseenter opens it), click closes it, second click toggles it open
        await chevronBtn.click();
        await page.waitForTimeout(220);
        await expect(megaMenu).toBeHidden();

        await chevronBtn.click();
        await page.waitForTimeout(250);
        await expect(megaMenu).toBeVisible();

        await page.keyboard.press('Escape');
        await page.waitForTimeout(250);
        await expect(megaMenu).toBeHidden();

        // 4. Re-open and click outside (main) -> closes mega-menu
        await chevronBtn.click();
        await page.waitForTimeout(250);
        await expect(megaMenu).toBeVisible();

        const main = page.locator('main');
        await expect(main).toBeVisible();
        await main.click({ position: { x: 40, y: 200 } });
        await page.waitForTimeout(250);
        await expect(megaMenu).toBeHidden();

        await verifyVisibleHeaderImages(page, 'desktop-interactive-close');
        assertCleanRun(tracker, 'desktop-interactive-close');
    });

    test('Mobile 375x812 [/]: Desktop mega-menu hidden, mobile drawer & product accordion work without horizontal overflow', async ({
        page,
    }, testInfo) => {
        test.skip(testInfo.project.name !== 'Mobile Chrome', 'Mobile test only');

        await page.setViewportSize({ width: 375, height: 812 });
        const tracker = attachTrackers(page);

        const response = await page.goto('/', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);
        await page.evaluate(() => document.fonts.ready);

        // Desktop mega-menu must stay hidden on mobile
        const desktopMegaMenu = page.locator('[data-testid="desktop-mega-menu"]');
        await expect(desktopMegaMenu).toBeHidden();

        const initialOverflow = await page.evaluate(() => ({
            scrollWidth: document.documentElement.scrollWidth,
            innerWidth: window.innerWidth,
        }));
        expect(initialOverflow.scrollWidth).toBeLessThanOrEqual(initialOverflow.innerWidth);

        // Open mobile drawer
        const mobileMenuToggle = page.locator('[data-testid="mobile-menu-toggle"]');
        await expect(mobileMenuToggle).toBeVisible();
        await mobileMenuToggle.click();
        await page.waitForTimeout(300);

        const mobileDrawer = page.locator('#mobile-drawer');
        await expect(mobileDrawer).toBeVisible();

        // Open product category accordion inside drawer
        const accordionToggle = mobileDrawer.locator(
            'button[aria-label="Mở rộng danh mục sản phẩm"]'
        );
        await expect(accordionToggle).toBeVisible();
        await accordionToggle.click();
        await page.waitForTimeout(300);

        const viewAllProductsLink = mobileDrawer.getByRole('link', {
            name: 'Xem tất cả sản phẩm',
        });
        await expect(viewAllProductsLink).toBeVisible();

        const drawerOverflow = await page.evaluate(() => ({
            scrollWidth: document.documentElement.scrollWidth,
            innerWidth: window.innerWidth,
        }));
        console.log(
            `[Mobile 375x812] initialScrollWidth=${initialOverflow.scrollWidth}/${initialOverflow.innerWidth}, drawerScrollWidth=${drawerOverflow.scrollWidth}/${drawerOverflow.innerWidth}`
        );
        expect(drawerOverflow.scrollWidth).toBeLessThanOrEqual(drawerOverflow.innerWidth);

        await verifyVisibleHeaderImages(page, 'mobile-375x812');
        assertCleanRun(tracker, 'mobile-375x812');
    });
});
