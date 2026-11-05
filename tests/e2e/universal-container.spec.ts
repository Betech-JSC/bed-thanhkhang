import { test, expect } from '@playwright/test';

test.describe('Universal Container Standardization (Figma Node 2261:5076)', () => {

  test('Homepage Sections Left Edge & Width Alignment on 1440px Desktop', async ({ page }) => {
    test.setTimeout(60000);
    // Set exact 1440x900 viewport
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });

    // Wait for main sections to be visible
    await expect(page.locator('#brand-story-section')).toBeVisible({ timeout: 15000 });
    await expect(page.locator('#services-section')).toBeVisible();
    await expect(page.locator('#products-section')).toBeVisible();
    await expect(page.locator('#contact-section')).toBeVisible();
    await expect(page.locator('#footer-section')).toBeVisible();

    const sectionsToCheck = [
      { name: 'BrandStorySection', selector: '#brand-story-section .app-container' },
      { name: 'HighlightMetrics', selector: 'section:has(.app-container) .app-container', index: 0 },
      { name: 'ServicesSection', selector: '#services-section .app-container' },
      { name: 'FeaturedCarousel', selector: '#featured-carousel .app-container' },
      { name: 'ProductsSection', selector: '#products-section .app-container' },
      { name: 'ContactSection', selector: '#contact-section .app-container' },
      { name: 'Footer', selector: '#footer-section .app-container' },
    ];

    for (const item of sectionsToCheck) {
      const loc = item.index !== undefined
        ? page.locator(item.selector).nth(item.index)
        : page.locator(item.selector);

      await expect(loc).toBeVisible();

      const metrics = await loc.evaluate((node) => {
        const rect = node.getBoundingClientRect();
        const style = window.getComputedStyle(node);
        const pl = parseFloat(style.paddingLeft);
        const pr = parseFloat(style.paddingRight);
        return {
          rectLeft: Math.round(rect.left),
          rectWidth: Math.round(rect.width),
          contentLeft: Math.round(rect.left + pl),
          contentWidth: Math.round(rect.width - pl - pr),
        };
      });

      console.log(`[1440px] ${item.name}: rectLeft=${metrics.rectLeft}, contentLeft=${metrics.contentLeft}, contentWidth=${metrics.contentWidth}`);

      // Base Container: rect.left = 112px, contentLeft = 144px (±2px), contentWidth = 1152px (±2px)
      expect(Math.abs(metrics.contentLeft - 144)).toBeLessThanOrEqual(2);
      expect(Math.abs(metrics.contentWidth - 1152)).toBeLessThanOrEqual(2);
    }

    // Verify intentional exceptions are preserved
    // 1. Hero Card: max-w-[1280px] / max-w-[1279px] (margin ~80px on 1440px)
    const heroCard = page.locator('section:has(h1) > div').first();
    const heroLeft = await heroCard.evaluate((node) => Math.round(node.getBoundingClientRect().left));
    console.log(`[1440px] Hero Card left=${heroLeft} (intentional ~80px exception)`);
    expect(Math.abs(heroLeft - 80)).toBeLessThanOrEqual(3);

    // 2. Header Bar: max-w-[1440px] px-20
    const headerContainer = page.locator('header > div').first();
    const headerMetrics = await headerContainer.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const style = window.getComputedStyle(node);
      return {
        rectLeft: Math.round(rect.left),
        paddingLeft: Math.round(parseFloat(style.paddingLeft)),
      };
    });
    console.log(`[1440px] Header: rectLeft=${headerMetrics.rectLeft}, paddingLeft=${headerMetrics.paddingLeft}`);
    expect(headerMetrics.paddingLeft).toBe(80); // lg:px-20 = 80px
  });

  test('Cross-Page Consistency between Products and Services on 1280px Laptop', async ({ page }) => {
    test.setTimeout(60000);
    await page.setViewportSize({ width: 1280, height: 800 });

    // 1. Check Product Catalog Page (/san-pham)
    await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });
    const productContainer = page.locator('.app-container').first();
    await expect(productContainer).toBeVisible({ timeout: 15000 });

    const productMetrics = await productContainer.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const style = window.getComputedStyle(node);
      const pl = parseFloat(style.paddingLeft);
      const pr = parseFloat(style.paddingRight);
      return {
        rectLeft: Math.round(rect.left),
        contentLeft: Math.round(rect.left + pl),
        contentWidth: Math.round(rect.width - pl - pr),
      };
    });
    console.log(`[1280px Product] contentLeft=${productMetrics.contentLeft}, contentWidth=${productMetrics.contentWidth}`);

    // On 1280px: (1280 - 1216)/2 = 32px margin + 32px padding = 64px contentLeft; contentWidth = 1152px
    expect(Math.abs(productMetrics.contentLeft - 64)).toBeLessThanOrEqual(2);
    expect(Math.abs(productMetrics.contentWidth - 1152)).toBeLessThanOrEqual(2);

    // 2. Check Services Page (/dich-vu)
    await page.goto('/dich-vu', { waitUntil: 'domcontentloaded' });
    const serviceContainer = page.locator('.app-container').first();
    await expect(serviceContainer).toBeVisible({ timeout: 15000 });

    const serviceMetrics = await serviceContainer.evaluate((node) => {
      const rect = node.getBoundingClientRect();
      const style = window.getComputedStyle(node);
      const pl = parseFloat(style.paddingLeft);
      const pr = parseFloat(style.paddingRight);
      return {
        rectLeft: Math.round(rect.left),
        contentLeft: Math.round(rect.left + pl),
        contentWidth: Math.round(rect.width - pl - pr),
      };
    });
    console.log(`[1280px Services] contentLeft=${serviceMetrics.contentLeft}, contentWidth=${serviceMetrics.contentWidth}`);

    // Verify Services matches Product exactly at 1280px (0px shift!)
    expect(Math.abs(serviceMetrics.contentLeft - 64)).toBeLessThanOrEqual(2);
    expect(Math.abs(serviceMetrics.contentWidth - 1152)).toBeLessThanOrEqual(2);

    expect(Math.abs(productMetrics.contentLeft - serviceMetrics.contentLeft)).toBeLessThanOrEqual(1);
    expect(Math.abs(productMetrics.contentWidth - serviceMetrics.contentWidth)).toBeLessThanOrEqual(1);
  });

  test('Informational Pages (Process, About, Contact, Policy) 1440px Container Alignment', async ({ page }) => {
    test.setTimeout(60000);
    await page.setViewportSize({ width: 1440, height: 900 });

    const pages = [
      { url: '/quy-trinh', name: 'Process' },
      { url: '/gioi-thieu', name: 'About' },
      { url: '/lien-he', name: 'Contact' },
      { url: '/chinh-sach/chinh-sach-bao-mat', name: 'Policy' },
    ];

    for (const p of pages) {
      await page.goto(p.url, { waitUntil: 'domcontentloaded' });
      const container = page.locator('.app-container').first();
      await expect(container).toBeVisible({ timeout: 15000 });

      const metrics = await container.evaluate((node) => {
        const rect = node.getBoundingClientRect();
        const style = window.getComputedStyle(node);
        const pl = parseFloat(style.paddingLeft);
        const pr = parseFloat(style.paddingRight);
        return {
          contentLeft: Math.round(rect.left + pl),
          contentWidth: Math.round(rect.width - pl - pr),
        };
      });

      console.log(`[1440px ${p.name}] contentLeft=${metrics.contentLeft}, contentWidth=${metrics.contentWidth}`);
      expect(Math.abs(metrics.contentLeft - 144)).toBeLessThanOrEqual(2);
      expect(Math.abs(metrics.contentWidth - 1152)).toBeLessThanOrEqual(2);
    }
  });

  test('Mobile & Tablet Responsiveness: Zero Horizontal Scroll and Visual Evidence Screenshots', async ({ page }) => {
    test.setTimeout(60000);
    // 1. Mobile 375px
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#brand-story-section')).toBeVisible({ timeout: 15000 });

    const mobileOverflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth);
    expect(mobileOverflow).toBe(false);

    // Save Mobile visual screenshot evidence
    await page.screenshot({
      path: 'docs/evidence/universal-container-mobile.png',
      fullPage: true,
    });

    // 2. Tablet 768px
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#brand-story-section')).toBeVisible({ timeout: 15000 });

    const tabletOverflow = await page.evaluate(() => document.documentElement.scrollWidth > window.innerWidth);
    expect(tabletOverflow).toBe(false);

    // 3. Desktop 1440px Screenshot
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('/', { waitUntil: 'domcontentloaded' });
    await expect(page.locator('#brand-story-section')).toBeVisible({ timeout: 15000 });

    await page.screenshot({
      path: 'docs/evidence/universal-container-desktop.png',
      fullPage: true,
    });
  });

});
