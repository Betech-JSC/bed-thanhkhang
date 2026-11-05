import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Section Tổng quan về dịch vụ Reordered & Style Visual Evidence Capture', () => {

    test('1. Desktop 1440x900 Visual QA: Verify order, titles, descriptions, and Slate Dark #30374F middle card', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const servicesSection = page.locator('#services-section');
        await expect(servicesSection).toBeVisible();

        const cards = servicesSection.locator('a.group');
        await expect(cards).toHaveCount(3);

        // 1. Verify Card 0 (Gia công 3D)
        const card0 = cards.nth(0);
        await expect(card0.locator('h3')).toContainText('Gia công 3D');
        await expect(card0.locator('p')).toContainText('Hiện thực hóa mô hình bằng công nghệ in 3D');
        expect(await card0.getAttribute('href')).toMatch(/\/dich-vu\/gia-cong-3d/);

        // 2. Verify Card 1 (Thiết kế 3D - Middle card)
        const card1 = cards.nth(1);
        await expect(card1.locator('h3')).toContainText('Thiết kế 3D');
        await expect(card1.locator('p')).toContainText('Biến ý tưởng thành mô hình 3D chi tiết');
        expect(await card1.getAttribute('href')).toMatch(/\/dich-vu\/thiet-ke-3d/);

        // 3. Verify Card 2 (Custom)
        const card2 = cards.nth(2);
        await expect(card2.locator('h3')).toContainText('Custom');
        await expect(card2.locator('p')).toContainText('Thiết kế và chế tác theo yêu cầu');
        expect(await card2.getAttribute('href')).toMatch(/\/dich-vu\/lam-do-custom/);

        // Verify middle card background gradient style (Slate Dark #30374F -> rgb(48, 55, 79))
        const card1Style = await card1.getAttribute('style');
        expect(card1Style).toMatch(/(#30374F|48,\s*55,\s*79)/i);

        // Scroll into view & capture evidence
        await servicesSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        const desktopEvidencePath = path.join('docs', 'evidence', 'services-overview-reordered-desktop.png');
        const desktopScreenshotPath = path.join('tests', 'e2e', 'screenshots', 'services-overview-reordered.png');
        const desktopScreenshotPath2 = path.join('tests', 'e2e', 'screenshots', 'services-overview-reordered-desktop.png');

        await servicesSection.screenshot({ path: desktopEvidencePath });
        await servicesSection.screenshot({ path: desktopScreenshotPath });
        await servicesSection.screenshot({ path: desktopScreenshotPath2 });

        console.log(`Desktop evidence captured at ${desktopEvidencePath}`);
    });

    test('2. Mobile 375x667 Visual QA: Verify vertical layout order and evidence capture', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const servicesSection = page.locator('#services-section');
        await expect(servicesSection).toBeVisible();

        const cards = servicesSection.locator('a.group');
        await expect(cards).toHaveCount(3);

        await expect(cards.nth(0).locator('h3')).toContainText('Gia công 3D');
        await expect(cards.nth(1).locator('h3')).toContainText('Thiết kế 3D');
        await expect(cards.nth(2).locator('h3')).toContainText('Custom');

        await servicesSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        const mobileEvidencePath = path.join('docs', 'evidence', 'services-overview-reordered-mobile.png');
        const mobileScreenshotPath = path.join('tests', 'e2e', 'screenshots', 'services-overview-reordered-mobile.png');

        await servicesSection.screenshot({ path: mobileEvidencePath });
        await servicesSection.screenshot({ path: mobileScreenshotPath });

        console.log(`Mobile evidence captured at ${mobileEvidencePath}`);
    });

    test('3. Multilingual EN Visual QA (/en): Verify English labels and descriptions', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        await page.goto('/en', { waitUntil: 'networkidle' });

        const servicesSection = page.locator('#services-section');
        await expect(servicesSection).toBeVisible();

        const cards = servicesSection.locator('a.group');
        await expect(cards).toHaveCount(3);

        await expect(cards.nth(0).locator('h3')).toContainText('3D Machining & Printing');
        await expect(cards.nth(1).locator('h3')).toContainText('3D Design');
        await expect(cards.nth(2).locator('h3')).toContainText('Custom');

        expect(await cards.nth(0).getAttribute('href')).toMatch(/\/en\/services\/(gia-cong-3d|3d-machining)/);
        expect(await cards.nth(1).getAttribute('href')).toMatch(/\/en\/services\/(thiet-ke-3d|3d-design)/);
        expect(await cards.nth(2).getAttribute('href')).toMatch(/\/en\/services\/(lam-do-custom|custom)/);

        await servicesSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        const enEvidencePath = path.join('docs', 'evidence', 'services-overview-reordered-en.png');
        await servicesSection.screenshot({ path: enEvidencePath });
        console.log(`EN evidence captured at ${enEvidencePath}`);
    });
});
