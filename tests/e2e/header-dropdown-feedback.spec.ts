import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Header Dropdown Feedback Verification', () => {
    test.beforeAll(async () => {
        const screenshotDir = path.join(process.cwd(), 'tests', 'e2e', 'screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('Desktop: Verify no blue left border on hover and new arrow SVG for Xem tat ca san pham', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.setViewportSize({ width: 1440, height: 900 });
        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        const productLink = header.locator('nav').getByRole('link', { name: 'Sản phẩm' });
        await expect(productLink).toBeVisible();

        // Hover over "Sản phẩm" to show dropdown
        await productLink.hover();
        await page.waitForTimeout(300);

        const productTabWrapper = productLink.locator('..');
        const dropdown = productTabWrapper.locator('.absolute.top-full');
        await expect(dropdown).toBeVisible();

        // 1. Verify Category items do NOT have border-l-2 or border-[#2970FF]
        const mocKhoaLink = dropdown.getByRole('link', { name: 'Móc khoá & Phụ kiện' });
        await expect(mocKhoaLink).toBeVisible();

        const mocKhoaClass = await mocKhoaLink.getAttribute('class');
        console.log('Móc khoá link class:', mocKhoaClass);
        expect(mocKhoaClass).not.toContain('border-l');
        expect(mocKhoaClass).not.toContain('border-[#2970FF]');
        expect(mocKhoaClass).not.toContain('hover:border-[#2970FF]');

        // Hover on "Móc khoá & Phụ kiện"
        await mocKhoaLink.hover();
        await page.waitForTimeout(300);

        // Screenshot of the dropdown with "Móc khoá & Phụ kiện" hovered
        await dropdown.screenshot({
            path: 'tests/e2e/screenshots/header-dropdown-hover-moc-khoa.png',
        });

        // 2. Verify "Xem tất cả sản phẩm" has the right SVG arrow icon
        const viewAllLink = dropdown.getByRole('link', { name: 'Xem tất cả sản phẩm' });
        await expect(viewAllLink).toBeVisible();

        const svgArrow = viewAllLink.locator('svg');
        await expect(svgArrow).toBeVisible();

        const pathElement = svgArrow.locator('path');
        await expect(pathElement).toBeVisible();

        const pathD = await pathElement.getAttribute('d');
        console.log('Arrow path d:', pathD);
        expect(pathD).toBe('M3.33334 8H12.6667M8 3.33334L12.6667 8L8 12.6667');

        const strokeWidth = await pathElement.getAttribute('stroke-width');
        console.log('Arrow stroke-width:', strokeWidth);
        expect(strokeWidth).toBe('1.5');

        // Screenshot of the entire header with dropdown open
        await page.screenshot({
            path: 'tests/e2e/screenshots/header-dropdown-final-evidence.png',
            clip: { x: 0, y: 0, width: 1440, height: 450 },
        });

        console.log('TEST COMPLETED SUCCESSFULLY: Both requirements verified with screenshots.');
    });

    test('Mobile: Verify drawer has new arrow SVG for Xem tat ca san pham', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });

        // Open mobile menu
        const openDrawerBtn = page.getByRole('button', { name: /Mở danh mục điều hướng/i });
        await openDrawerBtn.click();
        await page.waitForTimeout(300);

        // Open Sản phẩm accordion
        const productAccordionBtn = page.getByRole('button', { name: 'Sản phẩm' });
        await productAccordionBtn.click();
        await page.waitForTimeout(300);

        // Check Xem tất cả sản phẩm in drawer
        const viewAllDrawerLink = page.getByRole('link', { name: 'Xem tất cả sản phẩm' });
        await expect(viewAllDrawerLink).toBeVisible();

        const svgArrow = viewAllDrawerLink.locator('svg');
        await expect(svgArrow).toBeVisible();

        const pathElement = svgArrow.locator('path');
        const pathD = await pathElement.getAttribute('d');
        expect(pathD).toBe('M3.33334 8H12.6667M8 3.33334L12.6667 8L8 12.6667');

        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-drawer-view-all.png',
        });
        console.log('MOBILE TEST COMPLETED SUCCESSFULLY.');
    });
});
