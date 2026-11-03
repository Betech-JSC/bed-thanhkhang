import { test, expect } from '@playwright/test';

test.describe('Design Tokens System & Visual QA', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });
    });

    test('Desktop: Container max-width locks at 1240px and center aligned', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        // Find container elements on page
        const container = page.locator('.container').first();
        await expect(container).toBeVisible();

        const box = await container.boundingBox();
        expect(box).not.toBeNull();
        if (box) {
            // Container width on 1440px desktop should be <= 1240px
            expect(box.width).toBeLessThanOrEqual(1240);
        }

        // Check computed max-width
        const maxWidth = await container.evaluate((el) => {
            return window.getComputedStyle(el).maxWidth;
        });
        expect(maxWidth).toBe('1240px');

        // Check margin left and right are equal (centered)
        const styles = await container.evaluate((el) => {
            const computed = window.getComputedStyle(el);
            return {
                marginLeft: computed.marginLeft,
                marginRight: computed.marginRight,
                paddingLeft: computed.paddingLeft,
                paddingRight: computed.paddingRight,
            };
        });
        expect(styles.marginLeft).toBe(styles.marginRight);

        // Visual screenshot
        await page.screenshot({ path: 'tests/e2e/screenshots/desktop-container-1240.png', fullPage: false });
    });

    test('Desktop: Typography uses Inter / Be Vietnam Pro and sans-serif (no Phudu / BT-BeauSans)', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        const bodyFont = await page.evaluate(() => {
            return window.getComputedStyle(document.body).fontFamily;
        });
        expect(bodyFont).toContain('Inter');
        expect(bodyFont).not.toContain('Phudu');
        expect(bodyFont).not.toContain('BT-BeauSans');

        // Check heading fonts
        const heading = page.locator('h1, h2, h3').first();
        if (await heading.isVisible()) {
            const headingFont = await heading.evaluate((el) => {
                return window.getComputedStyle(el).fontFamily;
            });
            expect(headingFont).toContain('Inter');
            expect(headingFont).not.toContain('Phudu');
            expect(headingFont).not.toContain('BT-BeauSans');
        }
    });

    test('Mobile: Responsive container padding and no horizontal overflow', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        const container = page.locator('.container').first();
        await expect(container).toBeVisible();

        const isOverflowing = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(isOverflowing).toBe(false);

        await page.screenshot({ path: 'tests/e2e/screenshots/mobile-container-375.png', fullPage: false });
    });
});
