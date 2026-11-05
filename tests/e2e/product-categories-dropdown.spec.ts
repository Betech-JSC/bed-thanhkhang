import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Product Categories Dropdown & Accordion E2E Verification', () => {
    test.beforeAll(async () => {
        const screenshotDir = path.join(process.cwd(), 'tests', 'e2e', 'screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('1. Desktop: Hover on tab "Sản phẩm" opens Popover Dark Tech with Hover Bridge & 4 Categories', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        const nav = header.locator('nav');
        await expect(nav).toBeVisible();

        // Locate Tab "Sản phẩm"
        const productLink = nav.getByRole('link', { name: 'Sản phẩm' });
        await expect(productLink).toBeVisible();

        const productTabWrapper = productLink.locator('..');
        const popoverWrapper = productTabWrapper.locator('.absolute.top-full');
        const chevron = productLink.locator('svg');

        // Hover over the product tab link
        await productLink.hover();
        await page.waitForTimeout(200);

        // Popover should appear and chevron rotate
        await expect(popoverWrapper).toBeVisible();
        await expect(chevron).toHaveClass(/rotate-180/);

        // Verify Hover Bridge (pt-2 wrapper)
        await expect(popoverWrapper).toHaveClass(/pt-2/);

        // Verify Dark Tech styling
        const popoverPanel = popoverWrapper.locator('> div');
        await expect(popoverPanel).toHaveClass(/bg-\[#1A1F36\]/);
        await expect(popoverPanel).toHaveClass(/border-\[#30374F\]/);
        await expect(popoverPanel).toHaveClass(/rounded-xl/);

        // Verify 4 Categories and "Xem tất cả sản phẩm"
        await expect(popoverWrapper.getByRole('link', { name: 'Trang trí không gian' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Móc khoá & Phụ kiện' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Trang trí & Đồ dùng' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Gia công theo yêu cầu' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Xem tất cả sản phẩm' })).toBeVisible();

        // Move mouse down into the popover panel (testing Hover Bridge continuity)
        await popoverPanel.hover();
        await page.waitForTimeout(200);
        await expect(popoverWrapper).toBeVisible();

        // Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-1440-products-dropdown-hover.png',
            fullPage: false,
        });
    });

    test('2. Desktop: Click category "Móc khoá & Phụ kiện" navigates to catalog with query parameter and highlights filter', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });

        const productLink = page.locator('header nav').getByRole('link', { name: 'Sản phẩm' });
        await productLink.hover();

        const productTabWrapper = productLink.locator('..');
        const popoverWrapper = productTabWrapper.locator('.absolute.top-full');
        await expect(popoverWrapper).toBeVisible();

        // Click "Móc khoá & Phụ kiện"
        const mocKhoaLink = popoverWrapper.getByRole('link', { name: 'Móc khoá & Phụ kiện' });
        await expect(mocKhoaLink).toBeVisible();
        await mocKhoaLink.click();

        // Verify URL updated with query parameter
        await page.waitForURL(/.*category=moc-khoa-phu-kien.*/);
        expect(page.url()).toContain('category=moc-khoa-phu-kien');

        // Verify Catalog page rendered
        const sidebar = page.locator('aside');
        await expect(sidebar).toBeVisible();

        // Verify active filter category button highlighted with #404968
        const activeCatBtn = sidebar.locator('button').filter({ hasText: 'Móc khoá' }).first();
        await expect(activeCatBtn).toHaveClass(/bg-\[#404968\]/);

        // Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-1440-products-filtered-by-category.png',
            fullPage: false,
        });
    });

    test('3. Desktop: Click "Xem tất cả sản phẩm" in dropdown navigates to base /san-pham without query', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('http://localhost:8000/san-pham?category=moc-khoa-phu-kien', { waitUntil: 'networkidle' });

        const productLink = page.locator('header nav').getByRole('link', { name: 'Sản phẩm' });
        await productLink.hover();

        const productTabWrapper = productLink.locator('..');
        const popoverWrapper = productTabWrapper.locator('.absolute.top-full');
        await expect(popoverWrapper).toBeVisible();

        const allProductsLink = popoverWrapper.getByRole('link', { name: 'Xem tất cả sản phẩm' });
        await expect(allProductsLink).toBeVisible();
        await allProductsLink.click();

        await page.waitForURL((url) => url.pathname.endsWith('/san-pham') && !url.search.includes('category='));
        expect(page.url()).not.toContain('category=');

        // Verify "Tất cả sản phẩm" filter active
        const sidebar = page.locator('aside');
        const allBtn = sidebar.locator('button').filter({ hasText: 'Tất cả sản phẩm' }).first();
        await expect(allBtn).toHaveClass(/bg-\[#404968\]/);
    });

    test('4. Mobile: Drawer Accordion expands on click, has >= 44px touch targets, navigates and closes drawer', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle' });

        const header = page.locator('header');

        // Open mobile drawer button (hamburger)
        const menuBtn = header.locator('button').filter({ has: page.locator('svg path[d*="M4 6h16"]') }).first();
        await expect(menuBtn).toBeVisible();
        await menuBtn.click();

        // Drawer panel
        const drawer = page.locator('aside[role="dialog"]');
        await expect(drawer).toBeVisible();

        // Product accordion button
        const productAccordionBtn = drawer.locator('button').filter({ hasText: 'Sản phẩm' }).first();
        await expect(productAccordionBtn).toBeVisible();

        // Chevron icon initially not rotated
        const chevron = productAccordionBtn.locator('svg');
        await expect(chevron).not.toHaveClass(/rotate-180/);

        // Click to expand accordion
        await productAccordionBtn.click();
        await page.waitForTimeout(300);

        // Chevron rotates 180 degrees
        await expect(chevron).toHaveClass(/rotate-180/);

        // Verify all 4 categories and all products link inside drawer
        await expect(drawer.getByRole('link', { name: 'Trang trí không gian' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Móc khoá & Phụ kiện' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Trang trí & Đồ dùng' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Gia công theo yêu cầu' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Xem tất cả sản phẩm' })).toBeVisible();

        // Verify touch target min-h-[44px]
        const catLinks = drawer.locator('a[class*="min-h-[44px]"]');
        const count = await catLinks.count();
        expect(count).toBeGreaterThanOrEqual(5);

        for (let i = 0; i < count; i++) {
            const item = catLinks.nth(i);
            const box = await item.boundingBox();
            if (box) {
                expect(box.height).toBeGreaterThanOrEqual(44);
            }
        }

        // Screenshot of expanded accordion
        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-375-products-accordion-open.png',
            fullPage: false,
        });

        // Click "Trang trí không gian"
        const trangTriLink = drawer.getByRole('link', { name: 'Trang trí không gian' }).first();
        await trangTriLink.click();

        // URL should have category
        await page.waitForURL(/.*category=trang-tri-khong-gian.*/);
        expect(page.url()).toContain('category=trang-tri-khong-gian');

        // Drawer should be closed
        await expect(drawer).toBeHidden();

        // Screenshot of mobile catalog
        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-375-products-filtered-by-category.png',
            fullPage: false,
        });
    });

    test('5. Multilingual EN: Dropdown on /en displays English titles and routes to /en/products?category=...', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('http://localhost:8000/en', { waitUntil: 'networkidle' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        // Locate Products Tab
        const productLink = header.locator('nav').getByRole('link', { name: 'Products' });
        await expect(productLink).toBeVisible();

        await productLink.hover();
        await page.waitForTimeout(200);

        const productTabWrapper = productLink.locator('..');
        const popoverWrapper = productTabWrapper.locator('.absolute.top-full');
        await expect(popoverWrapper).toBeVisible();

        // Check English Categories
        await expect(popoverWrapper.getByRole('link', { name: 'Space Decor' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Keychains & Accessories' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Decor & Lifestyle' })).toBeVisible();
        await expect(popoverWrapper.getByRole('link', { name: 'Custom Manufacturing' })).toBeVisible();

        // Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-1440-products-dropdown-en.png',
            fullPage: false,
        });

        // Click Space Decor
        const spaceDecorLink = popoverWrapper.getByRole('link', { name: 'Space Decor' });
        await spaceDecorLink.click();

        await page.waitForURL(/.*\/en\/products.*category=space-decor.*/);
        expect(page.url()).toContain('/en/products');
        expect(page.url()).toContain('category=space-decor');
    });
});
