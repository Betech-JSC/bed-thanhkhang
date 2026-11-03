import { test, expect } from '@playwright/test';

test.describe('Layout Shell & Navigation', () => {
    test.beforeEach(async ({ page }) => {
        // Navigate to home page
        await page.goto('/', { waitUntil: 'domcontentloaded' });
    });

    test('Desktop: Header, Navigation, Language Capsule and Footer 4-col are rendered properly', async ({ page, isMobile }) => {
        // Skip for mobile project
        if (isMobile) test.skip();

        // 1. Header is fixed at top with Dark Tech styling
        const header = page.locator('header');
        await expect(header).toBeVisible();
        await expect(header).toHaveClass(/fixed/);

        // 2. Logo HH Maker is visible
        const logo = header.locator('img[alt*="HH Maker"]');
        await expect(logo).toBeVisible();

        // 3. Desktop Navigation links exist
        const nav = header.locator('nav');
        await expect(nav).toBeVisible();
        await expect(nav.getByText('Giới thiệu')).toBeVisible();
        await expect(nav.getByText('Sản phẩm')).toBeVisible();
        await expect(nav.getByText('Dịch vụ')).toBeVisible();

        // 4. Capsule Language Switcher VIE / ENG (Figma Node 2380:1456)
        const vieBtn = header.getByRole('button', { name: 'VIE' });
        const engBtn = header.getByRole('button', { name: 'ENG' });
        await expect(vieBtn).toBeVisible();
        await expect(engBtn).toBeVisible();

        // 5. Cart Icon is visible
        const cartLink = header.locator('a[title="Giỏ hàng"]');
        await expect(cartLink).toBeVisible();

        // 6. Footer 4-col (Figma Node 2261:5820)
        const footer = page.locator('footer#footer-section');
        await expect(footer).toBeVisible();
        await expect(footer.getByText('Xưởng sản xuất:')).toBeVisible();
        await expect(footer.getByText('Dịch vụ', { exact: true })).toBeVisible();
        await expect(footer.getByText('Sản phẩm', { exact: true })).toBeVisible();
        await expect(footer.getByText('Liên hệ tư vấn', { exact: true })).toBeVisible();
        await expect(footer.getByText('HH Maker. All rights reserved.')).toBeVisible();

        // Scroll into view & screenshot footer
        await footer.scrollIntoViewIfNeeded();
        await page.screenshot({ path: 'tests/e2e/screenshots/desktop-footer-4col.png', fullPage: false });

        // 7. Check no horizontal scroll overflow
        const isOverflowing = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(isOverflowing).toBe(false);

        // Visual QA Screenshot
        await page.screenshot({ path: 'tests/e2e/screenshots/desktop-1440-home.png', fullPage: false });
    });

    test('Desktop: Active menu indicator displays 2px blue underline', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        // Navigate to Products page
        await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });

        // Find product menu item in header
        const header = page.locator('header');
        const productLink = header.locator('nav a').filter({ hasText: 'Sản phẩm' });
        await expect(productLink).toBeVisible();

        // Check active 2px blue underline indicator
        const activeIndicator = productLink.locator('span.bg-primary');
        await expect(activeIndicator).toBeVisible();
        await expect(activeIndicator).toHaveClass(/h-\[2px\]/);

        // Visual QA Screenshot for Active Menu Indicator
        await page.screenshot({ path: 'tests/e2e/screenshots/desktop-active-menu.png', fullPage: false });
    });

    test('Mobile: Hamburger button toggles slide-over drawer with body scroll lock', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        const header = page.locator('header');
        await expect(header).toBeVisible();

        // Hamburger button should be visible on mobile
        const hamburgerBtn = header.locator('button[aria-label="Mở danh mục điều hướng"]');
        await expect(hamburgerBtn).toBeVisible();

        // Click hamburger to open drawer
        await hamburgerBtn.click();

        // Slide-over drawer should be visible
        const drawer = page.locator('aside[aria-label="Menu di động"]');
        await expect(drawer).toBeVisible();

        // Visual QA Screenshot for Mobile Drawer Open
        await page.screenshot({ path: 'tests/e2e/screenshots/mobile-drawer-open.png', fullPage: false });

        // Check body scroll lock
        const isBodyLocked = await page.evaluate(() => {
            return document.body.classList.contains('overflow-hidden');
        });
        expect(isBodyLocked).toBe(true);

        // Check drawer content: nav links, language capsule, close button
        await expect(drawer.getByText('Giới thiệu')).toBeVisible();
        await expect(drawer.getByText('Sản phẩm')).toBeVisible();
        await expect(drawer.getByRole('button', { name: 'VIE' })).toBeVisible();
        await expect(drawer.getByRole('button', { name: 'ENG' })).toBeVisible();

        // Click close X button
        const closeBtn = drawer.locator('button[aria-label="Đóng menu"]');
        await expect(closeBtn).toBeVisible();
        await closeBtn.click();

        // Drawer should be hidden
        await expect(drawer).not.toBeVisible();

        // Body scroll lock should be restored
        const isBodyUnlocked = await page.evaluate(() => {
            return !document.body.classList.contains('overflow-hidden');
        });
        expect(isBodyUnlocked).toBe(true);

        // Open again and test Escape key
        await hamburgerBtn.click();
        await expect(drawer).toBeVisible();
        await page.keyboard.press('Escape');
        await expect(drawer).not.toBeVisible();

        // Check mobile no horizontal overflow
        const isMobileOverflowing = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(isMobileOverflowing).toBe(false);
    });
});
