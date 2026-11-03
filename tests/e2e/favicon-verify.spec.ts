import { test, expect } from '@playwright/test';

test.describe('HH Maker Favicon & Brand Icons Verification', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });
    });

    test('Favicon tags exist in DOM <head>', async ({ page }) => {
        // 1. SVG icon
        const svgIcon = page.locator('link[rel="icon"][type="image/svg+xml"]');
        await expect(svgIcon).toHaveCount(1);
        const svgHref = await svgIcon.getAttribute('href');
        expect(svgHref).toContain('/assets/images/header/hh-maker-icon.svg');

        // 2. PNG 32x32
        const icon32 = page.locator('link[rel="icon"][sizes="32x32"]');
        await expect(icon32).toHaveCount(1);
        const icon32Href = await icon32.getAttribute('href');
        expect(icon32Href).toContain('/favicon-32x32.png');

        // 3. PNG 16x16
        const icon16 = page.locator('link[rel="icon"][sizes="16x16"]');
        await expect(icon16).toHaveCount(1);
        const icon16Href = await icon16.getAttribute('href');
        expect(icon16Href).toContain('/favicon-16x16.png');

        // 4. Apple Touch Icon 180x180
        const appleIcon = page.locator('link[rel="apple-touch-icon"][sizes="180x180"]');
        await expect(appleIcon).toHaveCount(1);
        const appleHref = await appleIcon.getAttribute('href');
        expect(appleHref).toContain('/apple-touch-icon.png');

        // 5. Shortcut icon
        const shortcutIcon = page.locator('link[rel="shortcut icon"]');
        await expect(shortcutIcon).toHaveCount(1);
        const shortcutHref = await shortcutIcon.getAttribute('href');
        expect(shortcutHref).toContain('/favicon.ico');
    });

    test('All icon files return HTTP 200 OK', async ({ request }) => {
        const iconEndpoints = [
            '/favicon.ico',
            '/favicon-32x32.png',
            '/favicon-16x16.png',
            '/apple-touch-icon.png',
            '/assets/images/header/hh-maker-icon.svg',
            '/assets/images/header/hh-maker-icon.png',
        ];

        for (const endpoint of iconEndpoints) {
            const res = await request.get(endpoint);
            expect(res.status(), `Status of ${endpoint} must be 200`).toBe(200);
            const body = await res.body();
            expect(body.length, `Body length of ${endpoint} must be > 0`).toBeGreaterThan(0);
        }
    });
});
