import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Header Product Dropdown & Categories Verification (Evidence-Based 100%)', () => {
    test.beforeAll(async () => {
        const evidenceDir = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }
    });

    test('1. Desktop: Hover on "Sản phẩm" shows dropdown with 4 categories and "Xem tất cả sản phẩm"', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('/', { waitUntil: 'networkidle' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        const nav = header.locator('nav');
        await expect(nav).toBeVisible();

        // Locate Tab "Sản phẩm"
        const productLink = nav.getByRole('link', { name: 'Sản phẩm' });
        await expect(productLink).toBeVisible();

        const productTabWrapper = productLink.locator('..');
        const popoverWrapper = productTabWrapper.locator('.absolute.top-full');

        // Hover over the product tab link
        await productLink.hover();
        await page.waitForTimeout(300);

        // Dropdown popover should be visible
        await expect(popoverWrapper).toBeVisible();

        // Check 4 categories and "Xem tất cả sản phẩm"
        const cat1 = popoverWrapper.getByRole('link', { name: 'Trang trí không gian' });
        const cat2 = popoverWrapper.getByRole('link', { name: 'Móc khoá & Phụ kiện' });
        const cat3 = popoverWrapper.getByRole('link', { name: 'Trang trí & Đồ dùng' });
        const cat4 = popoverWrapper.getByRole('link', { name: 'Gia công theo yêu cầu' });
        const viewAll = popoverWrapper.getByRole('link', { name: 'Xem tất cả sản phẩm' });

        await expect(cat1).toBeVisible();
        await expect(cat2).toBeVisible();
        await expect(cat3).toBeVisible();
        await expect(cat4).toBeVisible();
        await expect(viewAll).toBeVisible();

        // Capture evidence screenshot
        await page.screenshot({
            path: 'storage/app/public/evidence/desktop-product-dropdown-menu.png',
            fullPage: false,
        });
    });

    test('2. Desktop: Click "Trang trí không gian" navigates to /san-pham?category=trang-tri-khong-gian with active filter', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.goto('/', { waitUntil: 'networkidle' });

        const productLink = page.locator('header nav').getByRole('link', { name: 'Sản phẩm' });
        await productLink.hover();
        await page.waitForTimeout(300);

        const popoverWrapper = productLink.locator('..').locator('.absolute.top-full');
        await expect(popoverWrapper).toBeVisible();

        // Click "Trang trí không gian"
        const trangTriLink = popoverWrapper.getByRole('link', { name: 'Trang trí không gian' });
        await expect(trangTriLink).toBeVisible();
        await trangTriLink.click();

        // Verify URL contains category query parameter
        await page.waitForURL(/.*category=trang-tri-khong-gian.*/);
        expect(page.url()).toContain('category=trang-tri-khong-gian');

        // Verify catalog aside is visible and filter button is active
        const sidebar = page.locator('aside');
        await expect(sidebar).toBeVisible();

        const activeCatBtn = sidebar.locator('button').filter({ hasText: 'Trang trí không gian' }).first();
        await expect(activeCatBtn).toHaveClass(/bg-\[#404968\]/);

        // Capture evidence screenshot
        await page.screenshot({
            path: 'storage/app/public/evidence/desktop-category-trang-tri-khong-gian-active.png',
            fullPage: false,
        });
    });

    test('3. Mobile Drawer: Open menu, click "Sản phẩm" accordion, shows all 4 sub-categories', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        await page.goto('/', { waitUntil: 'networkidle' });

        const header = page.locator('header');

        // Click hamburger menu button
        const menuBtn = header.locator('button').filter({ has: page.locator('svg path[d*="M4 6h16"]') }).first();
        await expect(menuBtn).toBeVisible();
        await menuBtn.click();

        // Drawer dialog
        const drawer = page.locator('aside[role="dialog"]');
        await expect(drawer).toBeVisible();

        // Locate accordion "Sản phẩm" button
        const productAccordionBtn = drawer.locator('button').filter({ hasText: 'Sản phẩm' }).first();
        await expect(productAccordionBtn).toBeVisible();

        // Expand accordion
        await productAccordionBtn.click();
        await page.waitForTimeout(300);

        // Verify all 4 categories and all products link inside drawer
        await expect(drawer.getByRole('link', { name: 'Xem tất cả sản phẩm' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Trang trí không gian' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Móc khoá & Phụ kiện' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Trang trí & Đồ dùng' })).toBeVisible();
        await expect(drawer.getByRole('link', { name: 'Gia công theo yêu cầu' })).toBeVisible();

        // Capture evidence screenshot
        await page.screenshot({
            path: 'storage/app/public/evidence/mobile-drawer-product-categories.png',
            fullPage: false,
        });

        // Click "Trang trí không gian" inside mobile drawer
        const trangTriLink = drawer.getByRole('link', { name: 'Trang trí không gian' }).first();
        await trangTriLink.click();

        // Verify URL and drawer close
        await page.waitForURL(/.*category=trang-tri-khong-gian.*/);
        expect(page.url()).toContain('category=trang-tri-khong-gian');
        await expect(drawer).toBeHidden();

        // Capture evidence screenshot
        await page.screenshot({
            path: 'storage/app/public/evidence/mobile-category-trang-tri-khong-gian-active.png',
            fullPage: false,
        });
    });
});
