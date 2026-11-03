import { test, expect } from '@playwright/test';

test.describe('HH Maker About Page Routing & Navigation Verification', () => {
    test('1. Verify /about renders About page directly with HTTP 200', async ({ page }) => {
        const response = await page.goto('/about', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);

        const heroH1 = page.locator('h1:has-text("ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH")').first();
        await expect(heroH1).toBeVisible();

        const founderHeading = page.locator('h2:has-text("Mỗi ý tưởng đều xứng đáng có một hình hài")').first();
        await expect(founderHeading).toBeVisible();
    });

    test('2. Verify Header navigation tab "Giới thiệu" is active and clickable', async ({ page, isMobile }) => {
        await page.goto('/', { waitUntil: 'networkidle' });

        if (!isMobile) {
            const aboutLink = page.locator('header nav a:has-text("Giới thiệu")').first();
            await expect(aboutLink).toBeVisible();
            await aboutLink.click();
            await page.waitForURL('**/gioi-thieu');

            const activeTab = page.locator('header nav a.text-\\[\\#2970FF\\]:has-text("Giới thiệu")').first();
            await expect(activeTab).toBeVisible();
        } else {
            // Open mobile menu
            const menuBtn = page.locator('header button[aria-label="Mở danh mục điều hướng"]').first();
            await expect(menuBtn).toBeVisible();
            await menuBtn.click();

            const drawerAboutLink = page.locator('aside nav a:has-text("Giới thiệu")').first();
            await expect(drawerAboutLink).toBeVisible();
            await drawerAboutLink.click();
            await page.waitForURL('**/gioi-thieu');
        }

        const heroH1 = page.locator('h1:has-text("ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH")').first();
        await expect(heroH1).toBeVisible();
    });

    test('3. Verify Footer link "Giới thiệu" navigates to /gioi-thieu', async ({ page }) => {
        await page.goto('/', { waitUntil: 'networkidle' });
        const footerAboutLink = page.locator('footer a[href="/gioi-thieu"]').first();
        await expect(footerAboutLink).toBeVisible();
        await footerAboutLink.click();
        await page.waitForURL('**/gioi-thieu');

        const heroH1 = page.locator('h1:has-text("ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH")').first();
        await expect(heroH1).toBeVisible();
    });
});
