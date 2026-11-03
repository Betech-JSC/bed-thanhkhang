import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Process Page CTA Banner Cleanup - Figma 4-Section Compliance Verification', () => {
    test.beforeAll(async () => {
        const screenshotDir = path.resolve('tests/e2e/screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('1. Desktop (1440px): Confirms exact 4-section structure and absence of CTA banner before Footer', async ({ page, isMobile }) => {
        test.skip(isMobile, 'Desktop-only test');

        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/quy-trinh', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Section 1: Hero Banner (Figma Node 2283:6841)
        const heroBadge = page.locator('text=QUY TRÌNH LÀM VIỆC').first();
        await expect(heroBadge).toBeVisible();
        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();

        // Section 2: Stepper Bar (Figma Node 2283:6851)
        const stepperItems = page.locator('#process-steps div[role="button"]');
        expect(await stepperItems.count()).toBe(5);

        // Section 3: 5 Steps Detail (Figma Node 2283:6882)
        for (let i = 1; i <= 5; i++) {
            const step = page.locator(`#step-0${i}`);
            await expect(step).toBeVisible();
        }

        // Section 4: FAQ Accordion (Figma Node 2334:8455)
        const faqSection = page.locator('text=CÂU HỎI THƯỜNG GẶP').first();
        await expect(faqSection).toBeVisible();
        const faqButtons = page.locator('button[aria-expanded]');
        expect(await faqButtons.count()).toBe(3);

        // Verify CTA Banner is REMOVED
        const ctaTitle = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")');
        await expect(ctaTitle).toHaveCount(0);
        const ctaBtn = page.locator('a:has-text("Nhận tư vấn ngay")');
        await expect(ctaBtn).toHaveCount(0);

        // Verify Footer is present right below FAQ
        const footer = page.locator('footer').first();
        await expect(footer).toBeVisible();

        // Check horizontal overflow
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Screenshots
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-quy-trinh-full.png',
            fullPage: true,
        });

        // Bottom view screenshot showing FAQ transitioning directly to Footer
        await faqSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(300);
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-quy-trinh-faq-to-footer.png',
            fullPage: false,
        });
    });

    test('2. Mobile: Confirms 4-section layout, responsive adaptation and direct transition to Footer', async ({ page, isMobile }) => {
        test.skip(!isMobile, 'Mobile-only test');

        const response = await page.goto('/quy-trinh', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Section 1: Hero
        const heroBadge = page.locator('text=QUY TRÌNH LÀM VIỆC').first();
        await expect(heroBadge).toBeVisible();

        // Section 2: Stepper
        const stepperItems = page.locator('#process-steps div[role="button"]');
        expect(await stepperItems.count()).toBe(5);

        // Section 3: Steps
        const step01 = page.locator('#step-01');
        await expect(step01).toBeVisible();

        // Section 4: FAQ
        const faqSection = page.locator('text=CÂU HỎI THƯỜNG GẶP').first();
        await expect(faqSection).toBeVisible();

        // Confirm CTA Banner NOT present
        const ctaTitle = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")');
        await expect(ctaTitle).toHaveCount(0);

        // Footer present
        const footer = page.locator('footer').first();
        await expect(footer).toBeVisible();

        // Horizontal overflow check
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Screenshots
        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-quy-trinh-full.png',
            fullPage: true,
        });

        await faqSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(300);
        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-quy-trinh-faq-to-footer.png',
            fullPage: false,
        });
    });
});
