import { test, expect, Page, Locator } from '@playwright/test';
import * as fs from 'fs';
import * as path from 'path';

const SCREENSHOT_DIR =
    'C:\\Users\\GiaKhiem\\.gemini\\antigravity\\brain\\7447bbce-8289-4ec0-9011-45b9fde21086\\scratch\\e2e-verify';

const PRODUCT_SLUG = 'xich-bac-thang-cuon-thuong-mai-tl133';

const OVERFLOW_ROUTES = [
    '/',
    '/tin-tuc',
    '/tai-lieu/thu-vien',
    '/quy-trinh',
    `/san-pham/${PRODUCT_SLUG}`,
] as const;

interface Box {
    x: number;
    y: number;
    width: number;
    height: number;
}

function ensureScreenshotDir(): void {
    fs.mkdirSync(SCREENSHOT_DIR, { recursive: true });
}

function shotPath(filename: string): string {
    ensureScreenshotDir();
    return path.join(SCREENSHOT_DIR, filename);
}

function getIntersectionArea(a: Box, b: Box): number {
    const xOverlap = Math.max(0, Math.min(a.x + a.width, b.x + b.width) - Math.max(a.x, b.x));
    const yOverlap = Math.max(0, Math.min(a.y + a.height, b.y + b.height) - Math.max(a.y, b.y));
    return xOverlap * yOverlap;
}

async function assertNoHorizontalOverflow(page: Page, label: string): Promise<void> {
    const metrics = await page.evaluate(() => ({
        scrollWidth: document.documentElement.scrollWidth,
        clientWidth: document.documentElement.clientWidth,
        innerWidth: window.innerWidth,
    }));
    expect(
        metrics.scrollWidth,
        `[${label}] scrollWidth (${metrics.scrollWidth}) must be <= innerWidth (${metrics.innerWidth})`
    ).toBeLessThanOrEqual(metrics.innerWidth);
}

async function expectTouchTargetAtLeast44(locator: Locator, label: string): Promise<Box> {
    await expect(locator, `[${label}] must be visible`).toBeVisible();
    const box = await locator.boundingBox();
    expect(box, `[${label}] must have a boundingBox`).not.toBeNull();
    expect(box!.width, `[${label}] width >= 44px`).toBeGreaterThanOrEqual(44);
    expect(box!.height, `[${label}] height >= 44px`).toBeGreaterThanOrEqual(44);
    return box!;
}

async function waitForPageReady(page: Page): Promise<void> {
    await page.waitForLoadState('domcontentloaded');
    await page.waitForLoadState('networkidle');
    await page.evaluate(() => document.fonts.ready);
}

test.describe('Figma 2:9 Mobile Sync & Full-Site Mobile UX/A11y', () => {
    test.describe('Mobile 390x844, 375x812 & Tablet 768x1024 Specs', () => {
        test.beforeEach(async ({}, testInfo) => {
            test.skip(
                testInfo.project.name !== 'Mobile Chrome',
                'Mobile & Tablet suite runs under Mobile Chrome project'
            );
        });

        test('Viewport meta & zero horizontal overflow across all key routes (390x844, 375x812, 768x1024)', async ({
            page,
        }) => {
            const viewports = [
                { width: 390, height: 844, label: '390x844' },
                { width: 375, height: 812, label: '375x812' },
                { width: 768, height: 1024, label: '768x1024' },
            ] as const;

            for (const vp of viewports) {
                await page.setViewportSize({ width: vp.width, height: vp.height });

                for (const routePath of OVERFLOW_ROUTES) {
                    const response = await page.goto(routePath, { waitUntil: 'domcontentloaded' });
                    expect(response?.status(), `GET ${routePath} HTTP status`).toBeLessThan(400);
                    await waitForPageReady(page);

                    if (vp.width === 390 && routePath === '/') {
                        const viewportContent = await page
                            .locator('meta[name="viewport"]')
                            .getAttribute('content');
                        expect(viewportContent).toBe(
                            'width=device-width, initial-scale=1, viewport-fit=cover'
                        );
                        expect(viewportContent).not.toContain('maximum-scale=1');
                        expect(viewportContent).not.toContain('user-scalable=no');
                    }

                    await assertNoHorizontalOverflow(page, `${vp.label} ${routePath}`);
                }
            }
        });

        test('Header Mobile (33px topbar + 61px nav = 94px), Touch Targets >= 44px, Drawer Safe-Area & No VI/EN switcher (390x844)', async ({
            page,
        }) => {
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            const header = page.locator('header').first();
            await expect(header).toBeVisible();

            // Mobile top info bar is the dark 33px sibling directly preceding sticky <header>
            const topbar = page.locator('div.lg\\:hidden.bg-dark').first();
            await expect(topbar).toBeVisible();

            const topbarBox = await topbar.boundingBox();
            const headerBox = await header.boundingBox();
            expect(topbarBox).not.toBeNull();
            expect(headerBox).not.toBeNull();
            expect(Math.round(topbarBox!.height)).toBe(33);
            expect(Math.round(headerBox!.height)).toBe(61);
            expect(Math.round(topbarBox!.height + headerBox!.height)).toBe(94);

            // CSS variables synchronized to 94px / 33px
            const cssVars = await page.evaluate(() => {
                const rootStyles = getComputedStyle(document.documentElement);
                const bodyStyles = getComputedStyle(document.body);
                const mainLayoutEl = document.querySelector('div[style*="--header-height-mobile"]');
                const mainLayoutStyles = mainLayoutEl ? getComputedStyle(mainLayoutEl) : null;
                return {
                    topbarCssVar: rootStyles.getPropertyValue('--header-topbar-height-mobile').trim(),
                    rootHeaderMobile: rootStyles.getPropertyValue('--header-height-mobile').trim(),
                    bodyHeaderTotalSm: bodyStyles.getPropertyValue('--header-total-sm').trim(),
                    layoutHeaderMobile: mainLayoutStyles
                        ? mainLayoutStyles.getPropertyValue('--header-height-mobile').trim()
                        : '',
                };
            });
            expect(cssVars.topbarCssVar).toBe('33px');
            expect(cssVars.rootHeaderMobile).toBe('94px');
            expect(cssVars.bodyHeaderTotalSm).toBe('94px');
            expect(cssVars.layoutHeaderMobile).toBe('94px');

            // Search & Hamburger touch targets >= 44x44
            const searchBtn = header.locator('button[aria-label="Tìm kiếm"]:visible').first();
            const hamburgerBtn = header.getByTestId('mobile-menu-toggle');
            await expectTouchTargetAtLeast44(searchBtn, 'Header Mobile Search button');
            await expectTouchTargetAtLeast44(hamburgerBtn, 'Header Mobile Hamburger button');

            // Ensure NO VI/EN switcher in Header
            const headerLangButtons = header.locator('button, a').filter({
                hasText: /^(VIE|ENG|VI|EN|VI\s*\/\s*EN)$/i,
            });
            expect(await headerLangButtons.count()).toBe(0);

            // Open #mobile-drawer
            await hamburgerBtn.click();
            const drawer = page.locator('#mobile-drawer');
            await expect(drawer).toBeVisible();
            await page.waitForTimeout(320);

            // Ensure NO VI/EN switcher inside #mobile-drawer
            const drawerLangButtons = drawer.locator('button, a').filter({
                hasText: /^(VIE|ENG|VI|EN|VI\s*\/\s*EN)$/i,
            });
            expect(await drawerLangButtons.count()).toBe(0);

            // Safe-area bottom padding applied on drawer CTA footer
            const drawerSafeAreaInfo = await drawer.evaluate((el) => {
                const bottomSection = el.querySelector('div[class*="safe-area-inset-bottom"]');
                const hasSafeAreaClass = Boolean(bottomSection);
                const computedPaddingBottom = bottomSection
                    ? parseFloat(getComputedStyle(bottomSection).paddingBottom)
                    : 0;
                let hasSafeAreaCssRule = false;
                for (const sheet of Array.from(document.styleSheets)) {
                    try {
                        for (const rule of Array.from(sheet.cssRules || [])) {
                            if (
                                rule.cssText &&
                                rule.cssText.includes('safe-area-inset-bottom')
                            ) {
                                hasSafeAreaCssRule = true;
                                break;
                            }
                        }
                    } catch {
                        // Ignore cross-origin sheets
                    }
                    if (hasSafeAreaCssRule) break;
                }
                return { hasSafeAreaClass, computedPaddingBottom, hasSafeAreaCssRule };
            });
            expect(drawerSafeAreaInfo.hasSafeAreaClass).toBe(true);
            expect(drawerSafeAreaInfo.hasSafeAreaCssRule).toBe(true);
            expect(drawerSafeAreaInfo.computedPaddingBottom).toBeGreaterThanOrEqual(20);

            // Close button >= 44x44
            const closeDrawerBtn = drawer.getByTestId('mobile-menu-close');
            await expectTouchTargetAtLeast44(closeDrawerBtn, 'Drawer close button');

            // Expand Products accordion & check accordion & leaf targets >= 44px
            const productAccordionBtn = drawer.locator(
                'button[aria-label="Mở rộng danh mục sản phẩm"]'
            );
            if ((await productAccordionBtn.count()) > 0) {
                await expectTouchTargetAtLeast44(
                    productAccordionBtn.first(),
                    'Drawer Products accordion button'
                );
                await productAccordionBtn.first().click();
                await page.waitForTimeout(280);

                const subCatButtons = drawer.locator('button[aria-label="Mở rộng danh mục con"]:visible');
                const subCount = await subCatButtons.count();
                if (subCount > 0) {
                    await expectTouchTargetAtLeast44(
                        subCatButtons.first(),
                        'Drawer category accordion toggle'
                    );
                    await subCatButtons.first().click();
                    await page.waitForTimeout(280);

                    const nestedButtons = drawer.locator(
                        'button[aria-label="Mở rộng danh mục con"]:visible'
                    );
                    const nestedCount = await nestedButtons.count();
                    for (let i = 0; i < Math.min(nestedCount, 4); i++) {
                        await expectTouchTargetAtLeast44(
                            nestedButtons.nth(i),
                            `Drawer accordion button #${i}`
                        );
                    }
                }

                // Verify visible drawer nav links meet >= 44px height (and >= 44px width)
                const navLinks = drawer.locator('nav a:visible');
                const linkCount = await navLinks.count();
                expect(linkCount).toBeGreaterThan(0);
                for (let i = 0; i < Math.min(linkCount, 8); i++) {
                    await expectTouchTargetAtLeast44(navLinks.nth(i), `Drawer nav link #${i}`);
                }
            }

            await page.screenshot({ path: shotPath('mobile-390-drawer.png') });
            await closeDrawerBtn.click();
            await expect(drawer).not.toBeVisible();
        });

        test('FloatingCtaWidget (44x44, right-3 = 12px, safe-area, Scroll-to-Top) & SearchModal clear X >= 44x44 (390x844)', async ({
            page,
        }) => {
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            // SearchModal clear X >= 44x44
            const searchBtn = page.locator('header button[aria-label="Tìm kiếm"]:visible').first();
            await searchBtn.click();
            const searchDialog = page.locator('[role="dialog"][aria-label="Tìm kiếm sản phẩm"]');
            await expect(searchDialog).toBeVisible();
            await page.waitForTimeout(250);
            const searchInput = searchDialog.locator('input[type="text"]');
            await searchInput.fill('xích tải');
            const clearSearchBtn = searchDialog.locator('button[aria-label="Xóa từ khóa"]');
            await expectTouchTargetAtLeast44(clearSearchBtn, 'SearchModal clear X button');
            await clearSearchBtn.click();
            await expect(searchInput).toHaveValue('');
            await page.keyboard.press('Escape');
            await expect(searchDialog).not.toBeVisible();

            // FloatingCtaWidget at scrollY > 300
            const floatingWidget = page.getByTestId('floating-cta-widget');
            await expect(floatingWidget).toBeVisible();
            await page.evaluate(() => window.scrollTo(0, 800));
            const scrollTopBtn = page.getByTestId('scroll-to-top-button');
            await expect(scrollTopBtn).toBeVisible({ timeout: 3000 });
            await page.waitForTimeout(250);

            const zaloBtn = floatingWidget.locator('a[aria-label="Liên hệ Zalo"]');
            const hotlineBtn = floatingWidget.locator('a[aria-label^="Gọi hotline"]');

            for (const [label, loc] of [
                ['Scroll-to-Top', scrollTopBtn],
                ['Zalo', zaloBtn],
                ['Hotline', hotlineBtn],
            ] as const) {
                const box = await expectTouchTargetAtLeast44(loc, `FloatingCta ${label}`);
                expect(Math.round(box.width), `${label} width === 44px`).toBe(44);
                expect(Math.round(box.height), `${label} height === 44px`).toBe(44);
            }

            const widgetBox = await floatingWidget.boundingBox();
            expect(widgetBox).not.toBeNull();
            const rightOffset = 390 - (widgetBox!.x + widgetBox!.width);
            expect(Math.round(rightOffset)).toBe(12); // right-3 = 12px

            const floatingClass = await floatingWidget.getAttribute('class');
            expect(floatingClass).toContain('right-3');
            expect(floatingClass).toContain('safe-area-inset-bottom');

            await page.screenshot({ path: shotPath('mobile-390-floating-cta.png') });

            // Click Scroll-to-Top and confirm window.scrollY === 0
            await scrollTopBtn.click();
            await page.waitForFunction(() => window.scrollY === 0, null, { timeout: 5000 });
            const finalScrollY = await page.evaluate(() => window.scrollY);
            expect(finalScrollY).toBe(0);
        });

        test('QuotePopupModal on Mobile 390x844: chain hidden, max-height, dedicated non-scrolling topbar close X >= 44x44', async ({
            page,
        }) => {
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            // Open QuotePopupModal from Mobile Drawer CTA
            await page.getByTestId('mobile-menu-toggle').click();
            const drawer = page.locator('#mobile-drawer');
            await expect(drawer).toBeVisible();
            await drawer.getByRole('button', { name: 'Nhận báo giá' }).click();

            const dialog = page.locator('[role="dialog"][aria-labelledby="quote-popup-title"]');
            await expect(dialog).toBeVisible();
            await page.waitForTimeout(250);

            // Chain textures hidden on < 1024px
            const chainDisplays = await dialog.locator('.quote-chain-wrap').evaluateAll((els) =>
                els.map((el) => getComputedStyle(el).display)
            );
            expect(chainDisplays.length).toBeGreaterThan(0);
            for (const d of chainDisplays) {
                expect(d).toBe('none');
            }

            // Modal container fits within calc(100dvh - 32px)
            const modalCard = dialog.locator('div[class*="max-h-[calc(100dvh-32px)]"]').first();
            const modalBox = await modalCard.boundingBox();
            expect(modalBox).not.toBeNull();
            expect(modalBox!.height).toBeLessThanOrEqual(844 - 32 + 1);

            // Close X in dedicated topbar above scrollable body
            const closeBtn = dialog.locator('button[aria-label="Đóng"]:visible').first();
            const closeBefore = await expectTouchTargetAtLeast44(
                closeBtn,
                'QuotePopupModal mobile close X'
            );

            const scrollBody = dialog.locator('div.overflow-y-auto.overscroll-contain').first();
            await expect(scrollBody).toBeVisible();
            const scrollBodyBox = await scrollBody.boundingBox();
            expect(scrollBodyBox).not.toBeNull();
            expect(scrollBodyBox!.y).toBeGreaterThanOrEqual(
                closeBefore.y + closeBefore.height - 1
            );

            await page.screenshot({ path: shotPath('mobile-390-quote-modal.png') });

            // Scroll the form body down and verify X stays stationary and above scroll body
            const scrollResult = await scrollBody.evaluate((el) => {
                const before = el.scrollTop;
                el.scrollTop = el.scrollHeight;
                return { before, after: el.scrollTop, scrollHeight: el.scrollHeight, clientHeight: el.clientHeight };
            });
            expect(scrollResult.scrollHeight).toBeGreaterThan(scrollResult.clientHeight);
            expect(scrollResult.after).toBeGreaterThan(scrollResult.before);

            const closeAfter = await closeBtn.boundingBox();
            expect(closeAfter).not.toBeNull();
            expect(Math.abs(closeAfter!.x - closeBefore.x)).toBeLessThanOrEqual(0.5);
            expect(Math.abs(closeAfter!.y - closeBefore.y)).toBeLessThanOrEqual(0.5);

            await closeBtn.click();
            await expect(dialog).not.toBeVisible();
        });

        test('Homepage 7 Sections & Footer match Figma 2:9 on Mobile 390x844', async ({ page }) => {
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            // 1. HeroSection
            const hero = page.locator('section[aria-roledescription="carousel"]').first();
            await expect(hero).toBeVisible();
            const heroChecks = await hero.evaluate((sectionEl) => {
                const activeSlide = sectionEl.querySelector('[role="group"][aria-roledescription="slide"]');
                const scrimEl = activeSlide?.querySelector('div[class*="linear-gradient(90deg"]');
                const eyebrowEl = activeSlide?.querySelector('p[class*="bg-clip-text"]');
                const descEl = activeSlide?.querySelector('p[class*="text-[#C9E0FF]"]');
                const ctaEls = Array.from(activeSlide?.querySelectorAll('a') || []);
                return {
                    scrimBg: scrimEl ? getComputedStyle(scrimEl).backgroundImage : '',
                    eyebrowFontSize: eyebrowEl ? getComputedStyle(eyebrowEl).fontSize : '',
                    descLineClamp: descEl ? getComputedStyle(descEl).webkitLineClamp : '',
                    descDisplay: descEl ? getComputedStyle(descEl).display : '',
                    descClass: descEl?.className || '',
                    ctaFontSizes: ctaEls.map((el) => getComputedStyle(el).fontSize),
                };
            });
            expect(heroChecks.scrimBg).toContain('linear-gradient');
            expect(heroChecks.scrimBg).toMatch(/90deg|to right/);
            expect(heroChecks.scrimBg).toContain('rgb(1, 16, 30)');
            expect(heroChecks.eyebrowFontSize).toBe('18px');
            expect(heroChecks.descLineClamp).toBe('none');
            expect(heroChecks.descClass).not.toContain('line-clamp-4');
            expect(heroChecks.ctaFontSizes.length).toBeGreaterThan(0);
            for (const fsVal of heroChecks.ctaFontSizes) {
                expect(fsVal).toBe('18px');
            }

            // Hero dot tabs & prev/next arrows >= 44x44 and 0 overlap
            const heroControlBoxes: Array<{ label: string; box: Box }> = [];
            const dotTabs = hero.locator('[role="tab"]');
            const dotCount = await dotTabs.count();
            expect(dotCount).toBeGreaterThan(1);
            for (let i = 0; i < dotCount; i++) {
                const box = await expectTouchTargetAtLeast44(dotTabs.nth(i), `Hero dot tab ${i}`);
                heroControlBoxes.push({ label: `dot-${i}`, box });
            }
            const prevArrow = hero.locator('button[aria-label="Slide trước"]');
            const nextArrow = hero.locator('button[aria-label="Slide tiếp theo"]');
            heroControlBoxes.push({
                label: 'prev-arrow',
                box: await expectTouchTargetAtLeast44(prevArrow, 'Hero prev arrow'),
            });
            heroControlBoxes.push({
                label: 'next-arrow',
                box: await expectTouchTargetAtLeast44(nextArrow, 'Hero next arrow'),
            });
            for (let i = 0; i < heroControlBoxes.length; i++) {
                for (let j = i + 1; j < heroControlBoxes.length; j++) {
                    const area = getIntersectionArea(
                        heroControlBoxes[i].box,
                        heroControlBoxes[j].box
                    );
                    expect(
                        area,
                        `Hero controls ${heroControlBoxes[i].label} & ${heroControlBoxes[j].label} must not overlap`
                    ).toBe(0);
                }
            }
            await page.screenshot({ path: shotPath('mobile-390-home-hero.png') });

            // 2. ProductsSection
            const products = page.locator('#product-categories-section');
            await products.scrollIntoViewIfNeeded();
            const productTiles = products.locator('div.grid > a');
            const tileCount = await productTiles.count();
            expect(tileCount).toBe(6);

            // Arrow > hidden on < 1024px across all 6 tiles
            const visibleArrows = products.locator('div.grid > a span:has(svg):visible');
            expect(await visibleArrows.count()).toBe(0);

            // Tile 6 default #005EA4 bg + #FAFAFA text on Mobile
            const tile6 = productTiles.nth(5);
            const tile6Styles = await tile6.evaluate((el) => {
                const h3 = el.querySelector('h3');
                return {
                    bg: getComputedStyle(el).backgroundColor,
                    textColor: h3 ? getComputedStyle(h3).color : '',
                };
            });
            expect(tile6Styles.bg).toBe('rgb(0, 94, 164)');
            expect(tile6Styles.textColor).toBe('rgb(250, 250, 250)');

            // 3. BrandStorySection
            const brandStory = page.locator('#about-intro-section');
            await brandStory.scrollIntoViewIfNeeded();
            const mobileWidePhoto = brandStory.locator('div.lg\\:hidden').first();
            const topTwoGrid = brandStory.locator('div.grid.grid-cols-2').first();
            const wideBox = await mobileWidePhoto.boundingBox();
            const topBox = await topTwoGrid.boundingBox();
            expect(wideBox).not.toBeNull();
            expect(topBox).not.toBeNull();
            expect(wideBox!.y).toBeLessThan(topBox!.y);

            const brandFontChecks = await brandStory.evaluate((el) => {
                const bodyP = el.querySelector('div.text-\\[16px\\] p');
                const cta = el.querySelector('a[href]');
                return {
                    bodyFontSize: bodyP ? getComputedStyle(bodyP).fontSize : '',
                    ctaFontSize: cta ? getComputedStyle(cta).fontSize : '',
                };
            });
            expect(brandFontChecks.bodyFontSize).toBe('16px');
            expect(brandFontChecks.ctaFontSize).toBe('18px');

            // 4. ServicesSection
            const services = page.locator('#why-choose-us-section');
            await services.scrollIntoViewIfNeeded();
            const servicesBanner = services.locator('div[class*="min-h-[611px]"]').first();
            const servicesBannerBox = await servicesBanner.boundingBox();
            expect(servicesBannerBox).not.toBeNull();
            expect(servicesBannerBox!.height).toBeGreaterThanOrEqual(611);

            const desktopWave = services.locator('div.hidden.lg\\:flex').first();
            await expect(desktopWave).not.toBeVisible();

            const mobileTimeline = services.locator('div.lg\\:hidden').first();
            await expect(mobileTimeline).toBeVisible();
            const timelineItems = mobileTimeline.locator('article');
            expect(await timelineItems.count()).toBe(5);
            const timelineBridges = mobileTimeline.locator('svg path[fill="#C9E0FF"]');
            expect(await timelineBridges.count()).toBe(4);
            const timelineCardsBorderless = await timelineItems.evaluateAll((els) =>
                els.every((el) => {
                    const s = getComputedStyle(el);
                    return (
                        s.borderTopWidth === '0px' &&
                        s.backgroundColor === 'rgba(0, 0, 0, 0)'
                    );
                })
            );
            expect(timelineCardsBorderless).toBe(true);
            await page.screenshot({ path: shotPath('mobile-390-services.png') });

            // 5. ProcessSection
            const processSec = page.locator('#process-section');
            await processSec.scrollIntoViewIfNeeded();
            const processCheck = await processSec.evaluate((el) => {
                const bannerCol = el.querySelector('div[class*="min-h-[500px]"]');
                const bannerStyle = bannerCol ? getComputedStyle(bannerCol) : null;
                const bannerRect = bannerCol?.getBoundingClientRect();
                const introDesc = bannerCol?.querySelector('p');
                const stepsWrapper = el.querySelector('div[class*="bg-[#F8FAFC]"]');
                const waveImg = stepsWrapper?.querySelector('img[src*="process-wave-bg.png"]');
                const waveWrap = waveImg?.closest('div');
                const stepLis = Array.from(el.querySelectorAll('ol > li'));
                const stepDescs = Array.from(el.querySelectorAll('ol > li p'));
                return {
                    bannerHeight: bannerRect ? bannerRect.height : 0,
                    bannerJustifyContent: bannerStyle ? bannerStyle.justifyContent : '',
                    introClamp: introDesc ? getComputedStyle(introDesc).webkitLineClamp : '',
                    stepClamps: stepDescs.map((p) => getComputedStyle(p).webkitLineClamp),
                    waveVisible: waveWrap ? getComputedStyle(waveWrap).display !== 'none' : false,
                    stepRadii: stepLis.map((li) => getComputedStyle(li).borderTopLeftRadius),
                };
            });
            expect(processCheck.bannerHeight).toBeGreaterThanOrEqual(500);
            expect(processCheck.bannerJustifyContent).toBe('flex-start');
            expect(processCheck.introClamp).toBe('none');
            for (const c of processCheck.stepClamps) {
                expect(c).toBe('none');
            }
            expect(processCheck.waveVisible).toBe(true);
            expect(processCheck.stepRadii.length).toBeGreaterThan(0);
            for (const r of processCheck.stepRadii) {
                expect(r).toBe('8px');
            }
            await page.screenshot({ path: shotPath('mobile-390-process.png') });

            // 6. BlogSection
            const blog = page.locator('#blog-section');
            await blog.scrollIntoViewIfNeeded();
            const blogChecks = await blog.evaluate((el) => {
                const container = el.querySelector('.app-container-narrow');
                const cta = el.querySelector('a[href$="/tin-tuc"], a[href$="/en/posts"]');
                return {
                    rowGap: container ? getComputedStyle(container).rowGap : '',
                    ctaFontSize: cta ? getComputedStyle(cta).fontSize : '',
                };
            });
            expect(blogChecks.rowGap).toBe('24px');
            expect(blogChecks.ctaFontSize).toBe('18px');

            // 7. ContactSection
            const contact = page.locator('#contact-section');
            await contact.scrollIntoViewIfNeeded();
            const contactChecks = await contact.evaluate((el) => {
                const chains = Array.from(el.querySelectorAll('img[src*="contact-chain-deco.png"]'));
                const visibleChains = chains.filter((img) => {
                    const parent = img.closest('div');
                    return parent ? getComputedStyle(parent).display !== 'none' : false;
                });
                const layoutGrid = el.querySelector('.app-container-narrow > div.flex');
                const submitBtn = el.querySelector('button[type="submit"]');
                return {
                    visibleChainCount: visibleChains.length,
                    rowGap: layoutGrid ? getComputedStyle(layoutGrid).rowGap : '',
                    submitText: submitBtn?.textContent?.trim() || '',
                    submitFontSize: submitBtn ? getComputedStyle(submitBtn).fontSize : '',
                };
            });
            expect(contactChecks.visibleChainCount).toBe(0);
            expect(contactChecks.rowGap).toBe('24px');
            expect(contactChecks.submitText).toContain('Nhận tư vấn ngay');
            expect(contactChecks.submitFontSize).toBe('18px');

            // 8. Footer
            const footer = page.locator('#footer-section');
            await footer.scrollIntoViewIfNeeded();
            const footerCtaQuote = footer.getByRole('button', { name: 'Nhận báo giá' });
            const footerCtaHotline = footer.getByRole('link', { name: 'Gọi hotline' });
            await expect(footerCtaQuote).not.toBeVisible();
            await expect(footerCtaHotline).not.toBeVisible();

            const footerChecks = await footer.evaluate((el) => {
                const visibleHrs = Array.from(el.querySelectorAll('hr, div.h-px')).filter(
                    (node) => getComputedStyle(node).display !== 'none'
                );
                const grid = el.querySelector('div.grid');
                const gridStyle = grid ? getComputedStyle(grid) : null;
                const colTracks = gridStyle
                    ? gridStyle.gridTemplateColumns.trim().split(/\s+/).filter(Boolean).length
                    : 0;
                return {
                    visibleHrCount: visibleHrs.length,
                    colTracks,
                    rowGap: gridStyle ? gridStyle.rowGap : '',
                };
            });
            expect(footerChecks.visibleHrCount).toBe(0);
            expect(footerChecks.colTracks).toBe(1);
            expect(footerChecks.rowGap).toBe('20px');

            const socialLinks = footer.locator('a[aria-label="Facebook"], a[aria-label="LinkedIn"]');
            const socialCount = await socialLinks.count();
            expect(socialCount).toBeGreaterThan(0);
            const socialBoxes: Box[] = [];
            for (let i = 0; i < socialCount; i++) {
                socialBoxes.push(
                    await expectTouchTargetAtLeast44(socialLinks.nth(i), `Footer social #${i}`)
                );
            }
            if (socialBoxes.length === 2) {
                expect(getIntersectionArea(socialBoxes[0], socialBoxes[1])).toBe(0);
            }

            await page.screenshot({ path: shotPath('mobile-390-footer.png') });
        });

        test('Inner Pages: /tin-tuc, /tai-lieu/thu-vien, /quy-trinh (390x844 & 768x1024), /san-pham/{slug} table wrap + ProductGallery', async ({
            page,
        }) => {
            // /tin-tuc at 390x844
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto('/tin-tuc', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);
            const postsSearch = page.locator('input[type="search"]').first();
            await expect(postsSearch).toBeVisible();
            const postsSearchFontSize = await postsSearch.evaluate(
                (el) => getComputedStyle(el).fontSize
            );
            expect(postsSearchFontSize).toBe('16px');
            await assertNoHorizontalOverflow(page, '390x844 /tin-tuc');

            // /tai-lieu/thu-vien at 390x844
            await page.goto('/tai-lieu/thu-vien', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);
            const docsSearch = page.locator('#documents-library-search');
            await expect(docsSearch).toBeVisible();
            const docsSearchFontSize = await docsSearch.evaluate(
                (el) => getComputedStyle(el).fontSize
            );
            expect(docsSearchFontSize).toBe('16px');
            await docsSearch.fill('TL133');
            const docsClearBtn = page.locator(
                '#documents-library-section button[aria-label="Xóa từ khóa"]'
            );
            await expectTouchTargetAtLeast44(docsClearBtn, 'DocumentLibrary clear X');
            await docsClearBtn.click();
            await expect(docsSearch).toHaveValue('');
            await assertNoHorizontalOverflow(page, '390x844 /tai-lieu/thu-vien');

            // /quy-trinh at 390x844 and 768x1024
            await page.goto('/quy-trinh', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);
            const heroCta = page.getByRole('button', { name: 'Khám phá 5 bước' });
            const circularBadge = page.locator('button[aria-label="Cuộn xuống xem 5 bước quy trình"]');
            const ctaBox390 = await expectTouchTargetAtLeast44(heroCta, 'ProcessHero mobile CTA');
            await expect(circularBadge).toBeVisible();
            const badgeBox390 = await circularBadge.boundingBox();
            expect(badgeBox390).not.toBeNull();
            expect(getIntersectionArea(ctaBox390, badgeBox390!)).toBe(0);
            await assertNoHorizontalOverflow(page, '390x844 /quy-trinh');
            await page.screenshot({ path: shotPath('mobile-390-process-hero.png') });

            // 768x1024 tablet check: text block & CircularBadge do not overlap
            await page.setViewportSize({ width: 768, height: 1024 });
            await page.waitForTimeout(150);
            await expect(circularBadge).toBeVisible();
            const textBlockBox768 = await page.locator('section h1').first().locator('..').boundingBox();
            const badgeBox768 = await circularBadge.boundingBox();
            expect(textBlockBox768).not.toBeNull();
            expect(badgeBox768).not.toBeNull();
            expect(getIntersectionArea(textBlockBox768!, badgeBox768!)).toBe(0);
            await assertNoHorizontalOverflow(page, '768x1024 /quy-trinh');
            await page.screenshot({ path: shotPath('tablet-768-process-hero.png') });

            // /san-pham/{slug} at 390x844: ProductGallery thumbnail strip + ProductDetailTabs .pdp-table-wrap
            await page.setViewportSize({ width: 390, height: 844 });
            await page.goto(`/san-pham/${PRODUCT_SLUG}`, { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            // ProductGallery.vue thumbnail strip intact
            const thumbStrip = page.locator('[role="group"][aria-label="Ảnh thu nhỏ sản phẩm"]');
            await expect(thumbStrip).toBeVisible();
            expect(await thumbStrip.locator('button').count()).toBeGreaterThan(0);
            await assertNoHorizontalOverflow(page, `390x844 /san-pham/${PRODUCT_SLUG} (clean)`);

            // Inject wide <table> via Inertia XHR on real client navigation from /san-pham
            await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            await page.route(`**/san-pham/${PRODUCT_SLUG}*`, async (route) => {
                const headers = route.request().headers();
                if (!headers['x-inertia']) {
                    await route.continue();
                    return;
                }
                const response = await route.fetch();
                const pageData = await response.json();
                const wideTableHtml =
                    '<p>Đoạn mô tả kỹ thuật phía trên bảng thông số chi tiết cho khách hàng kiểm tra độ rộng khung đọc.</p>' +
                    '<table style="min-width: 820px;">' +
                    '<thead><tr><th>Mã</th><th>Bước xích (mm)</th><th>Đường kính con lăn</th><th>Tải trọng phá hủy</th><th>Vật liệu</th><th>Tiêu chuẩn</th></tr></thead>' +
                    '<tbody><tr><td>TL133H</td><td>133.35</td><td>76.2 mm</td><td>180 kN</td><td>Thép hợp kim 40Cr</td><td>ISO 606 / DIN 8187</td></tr></tbody>' +
                    '</table>' +
                    '<p>Đoạn mô tả bổ sung phía dưới bảng.</p>';
                if (pageData?.props?.product) {
                    pageData.props.product.content = wideTableHtml;
                }
                await route.fulfill({
                    status: 200,
                    contentType: 'application/json',
                    headers: {
                        'X-Inertia': 'true',
                        Vary: 'Accept',
                    },
                    body: JSON.stringify(pageData),
                });
            });

            const pdpLink = page.locator(`a[href*="/san-pham/${PRODUCT_SLUG}"]`).first();
            await expect(pdpLink).toBeVisible();
            await pdpLink.click();
            await page.waitForURL(`**/san-pham/${PRODUCT_SLUG}`);
            await waitForPageReady(page);
            await page.unroute(`**/san-pham/${PRODUCT_SLUG}*`);

            // ProductDetailTabs .pdp-table-wrap wraps wide table cleanly
            const pdpPanel = page.locator('#pdp-panel-overview');
            await pdpPanel.scrollIntoViewIfNeeded();
            const tableWrap = pdpPanel.locator('.pdp-richtext .pdp-table-wrap').first();
            await expect(tableWrap).toBeVisible();
            await expect(tableWrap.locator('table')).toBeVisible();

            const pdpMetrics = await pdpPanel.evaluate((el) => {
                const richtext = el.querySelector('.pdp-richtext') as HTMLElement | null;
                const wrap = el.querySelector('.pdp-table-wrap') as HTMLElement | null;
                const firstP = richtext?.querySelector('p') as HTMLElement | null;
                return {
                    richtextOverflowX: richtext ? getComputedStyle(richtext).overflowX : '',
                    wrapOverflowX: wrap ? getComputedStyle(wrap).overflowX : '',
                    wrapScrollWidth: wrap ? wrap.scrollWidth : 0,
                    wrapClientWidth: wrap ? wrap.clientWidth : 0,
                    pWidth: firstP ? firstP.getBoundingClientRect().width : 0,
                };
            });
            expect(pdpMetrics.richtextOverflowX).not.toBe('auto');
            expect(pdpMetrics.wrapOverflowX).toBe('auto');
            expect(pdpMetrics.wrapScrollWidth).toBeGreaterThan(pdpMetrics.wrapClientWidth);
            expect(pdpMetrics.pWidth).toBeLessThanOrEqual(390);
            await assertNoHorizontalOverflow(page, `390x844 /san-pham/${PRODUCT_SLUG}`);
            await page.screenshot({ path: shotPath('mobile-390-pdp-table-wrap.png') });
        });
    });

    test.describe('Desktop Regression 1440x900 & 1366x768 Specs', () => {
        test.beforeEach(async ({}, testInfo) => {
            test.skip(
                testInfo.project.name !== 'Desktop Chrome',
                'Desktop regression runs under Desktop Chrome project'
            );
        });

        test('Desktop 1440x900 regression: overflow, FloatingCta 60x60, Footer 3-col + CTAs, Homepage sections, Search inputs 14px', async ({
            page,
        }) => {
            await page.setViewportSize({ width: 1440, height: 900 });

            // Check overflow across all 5 routes
            for (const routePath of OVERFLOW_ROUTES) {
                await page.goto(routePath, { waitUntil: 'domcontentloaded' });
                await waitForPageReady(page);
                await assertNoHorizontalOverflow(page, `1440x900 ${routePath}`);
            }

            // /tin-tuc & /tai-lieu/thu-vien search inputs are 14px on Desktop
            await page.goto('/tin-tuc', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);
            expect(
                await page.locator('input[type="search"]').first().evaluate((el) => getComputedStyle(el).fontSize)
            ).toBe('14px');

            await page.goto('/tai-lieu/thu-vien', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);
            expect(
                await page.locator('#documents-library-search').evaluate((el) => getComputedStyle(el).fontSize)
            ).toBe('14px');

            // Homepage 1440x900 checks
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            // ProductsSection: arrow > visible on all 6 tiles, 6th tile default #C9E0FF
            const products = page.locator('#product-categories-section');
            const tiles = products.locator('div.grid > a');
            expect(await tiles.count()).toBe(6);
            expect(await products.locator('div.grid > a span:has(svg):visible').count()).toBe(6);
            const tile6Bg = await tiles.nth(5).evaluate((el) => getComputedStyle(el).backgroundColor);
            expect(tile6Bg).toBe('rgb(201, 224, 255)');

            // BrandStorySection: Desktop Row 2 wideImage sits BELOW Row 1 topImages
            const brandStory = page.locator('#about-intro-section');
            const desktopWide = brandStory.locator('div.hidden.lg\\:block').first();
            const topImages = brandStory.locator('div.grid.grid-cols-2').first();
            await expect(desktopWide).toBeVisible();
            expect((await desktopWide.boundingBox())!.y).toBeGreaterThan(
                (await topImages.boundingBox())!.y
            );

            // ServicesSection: horizontal 5-node diagram visible, mobile vertical timeline hidden
            const services = page.locator('#why-choose-us-section');
            await expect(services.locator('div.hidden.lg\\:flex').first()).toBeVisible();
            await expect(services.locator('div.lg\\:hidden').first()).not.toBeVisible();

            // Footer: 2 CTAs visible & 3-column grid preserved
            const footer = page.locator('#footer-section');
            await footer.scrollIntoViewIfNeeded();
            await expect(footer.getByRole('button', { name: 'Nhận báo giá' })).toBeVisible();
            await expect(footer.getByRole('link', { name: 'Gọi hotline' })).toBeVisible();
            const footerColCount = await footer.locator('div.grid').evaluate(
                (el) => getComputedStyle(el).gridTemplateColumns.trim().split(/\s+/).filter(Boolean).length
            );
            expect(footerColCount).toBe(3);

            // FloatingCtaWidget at scrollY > 300 -> 60x60px
            const floatingWidget = page.getByTestId('floating-cta-widget');
            const scrollTopBtn = page.getByTestId('scroll-to-top-button');
            await expect(scrollTopBtn).toBeVisible();
            await page.waitForTimeout(250);
            for (const loc of [
                scrollTopBtn,
                floatingWidget.locator('a[aria-label="Liên hệ Zalo"]'),
                floatingWidget.locator('a[aria-label^="Gọi hotline"]'),
            ]) {
                const box = await loc.boundingBox();
                expect(box).not.toBeNull();
                expect(Math.round(box!.width)).toBe(60);
                expect(Math.round(box!.height)).toBe(60);
            }

            await page.screenshot({ path: shotPath('desktop-1440-home-full.png'), fullPage: true });
        });

        test('QuotePopupModal on Laptop 1366x768: backdrop scrollTop === 0, fits viewport, textarea resize: none', async ({
            page,
        }) => {
            await page.setViewportSize({ width: 1366, height: 768 });
            await page.goto('/', { waitUntil: 'domcontentloaded' });
            await waitForPageReady(page);

            await page.locator('header').getByRole('button', { name: 'Nhận báo giá' }).click();
            const dialog = page.locator('[role="dialog"][aria-labelledby="quote-popup-title"]');
            await expect(dialog).toBeVisible();
            await page.waitForTimeout(250);

            // Simulate trackpad wheel over backdrop
            await page.mouse.move(100, 100);
            await page.mouse.wheel(0, 400);
            await page.waitForTimeout(100);

            const modalMetrics = await dialog.evaluate((el) => {
                const style = getComputedStyle(el);
                const textarea = el.querySelector('#quote-popup-note');
                const closeBtn = el.querySelector('button[aria-label="Đóng"]:not(.lg\\:hidden button)') ||
                    Array.from(el.querySelectorAll('button[aria-label="Đóng"]')).find(
                        (b) => getComputedStyle(b).display !== 'none'
                    );
                return {
                    scrollTop: el.scrollTop,
                    overflowY: style.overflowY,
                    overscrollBehavior: style.overscrollBehavior,
                    textareaResize: textarea ? getComputedStyle(textarea).resize : '',
                    closeBtnBottom: closeBtn ? closeBtn.getBoundingClientRect().bottom : 9999,
                };
            });

            expect(modalMetrics.scrollTop).toBe(0);
            expect(modalMetrics.overflowY).toBe('hidden');
            expect(modalMetrics.textareaResize).toBe('none');
            expect(modalMetrics.closeBtnBottom).toBeLessThanOrEqual(768);
        });
    });
});
