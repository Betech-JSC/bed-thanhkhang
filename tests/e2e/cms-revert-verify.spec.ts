import { test, expect } from '@playwright/test';
import fs from 'fs';
import path from 'path';

test.describe('CMS Admin Language Switcher Revert Verification', () => {
    test.beforeAll(() => {
        const evidenceDir = path.resolve('storage/app/public/evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }
    });

    test('CMS Admin has [ VI ] [ EN ] buttons visible, allows switching, and displays products', async ({ page, isMobile }) => {
        // 1. Go to Admin login
        await page.goto('/admin/login');
        await page.waitForLoadState('networkidle');

        // Fill credentials if on login page
        if (await page.locator('#email').isVisible()) {
            await page.fill('#email', 'admin@gmail.com');
            await page.fill('#password', 'admin@gmail.com');
            await page.click('button[type="submit"]');
            await page.waitForLoadState('networkidle');
        }

        // Ensure we are in the admin dashboard / layout
        await expect(page).toHaveURL(/.*admin.*/);

        if (isMobile) {
            // Open mobile menu
            const openSidebarBtn = page.locator('button:has-text("Open sidebar")');
            await openSidebarBtn.click();
            await page.waitForSelector('div[role="dialog"].translate-x-0', { state: 'visible' });
            await page.waitForTimeout(300);
        }

        // 2. Locate Sidebar language buttons based on screen type
        const sidebar = isMobile ? page.locator('div[role="dialog"] nav.navs') : page.locator('div.md\\:flex nav.navs');
        const viButton = sidebar.locator('.btn-group button', { hasText: /^vi$/i });
        const enButton = sidebar.locator('.btn-group button', { hasText: /^en$/i });

        await expect(viButton).toBeVisible();
        await expect(enButton).toBeVisible();

        // 3. Take evidence screenshot of CMS Sidebar with [ VI ] [ EN ] buttons
        const suffix = isMobile ? 'mobile' : 'desktop';
        const evidencePathVi = path.resolve(`storage/app/public/evidence/cms-admin-sidebar-vi-en-${suffix}.png`);
        await page.screenshot({ path: evidencePathVi, fullPage: false });
        console.log(`Saved screenshot to ${evidencePathVi}`);

        // Also copy or save the standard named file
        if (!isMobile) {
            fs.copyFileSync(evidencePathVi, path.resolve('storage/app/public/evidence/cms-admin-sidebar-vi-en.png'));
        }

        // 4. Click [ EN ] button to switch locale
        await enButton.click();
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        if (isMobile) {
            const openSidebarBtn = page.locator('button:has-text("Open sidebar")');
            if (await openSidebarBtn.isVisible()) {
                await openSidebarBtn.click();
                await page.waitForSelector('div[role="dialog"].translate-x-0', { state: 'visible' });
                await page.waitForTimeout(300);
            }
        }

        const evidencePathEn = path.resolve(`storage/app/public/evidence/cms-admin-sidebar-en-active-${suffix}.png`);
        await page.screenshot({ path: evidencePathEn, fullPage: false });
        console.log(`Saved screenshot to ${evidencePathEn}`);

        // 5. Click [ VI ] button to switch back
        const currentSidebar = isMobile ? page.locator('div[role="dialog"] nav.navs') : page.locator('div.md\\:flex nav.navs');
        const viButtonAfter = currentSidebar.locator('.btn-group button', { hasText: /^vi$/i });
        await expect(viButtonAfter).toBeVisible();
        await viButtonAfter.click();
        await page.waitForLoadState('networkidle');
        await page.waitForTimeout(1000);

        // 6. Navigate to Products index to verify Products module works without issues
        await page.goto('/admin/products');
        await page.waitForLoadState('networkidle');
        await expect(page).toHaveURL(/.*admin\/products.*/);
        const productsHeadingOrTable = page.locator('table, h1, h2, .table-responsive, .p-datatable').first();
        await expect(productsHeadingOrTable).toBeVisible();

        const evidencePathProducts = path.resolve(`storage/app/public/evidence/cms-admin-products-list-${suffix}.png`);
        await page.screenshot({ path: evidencePathProducts, fullPage: false });
        console.log(`Saved screenshot to ${evidencePathProducts}`);
    });
});
