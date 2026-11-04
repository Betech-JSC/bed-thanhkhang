import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('HH Maker Homepage Flip Announcement Ticker (CMS & Multilingual)', () => {
    test.beforeAll(() => {
        const evidenceDir = path.join('storage', 'app', 'public', 'evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }
        const screenshotDir = path.join('tests', 'e2e', 'screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('1. Verify Vietnamese Homepage (/) Announcement Ticker', async ({ page }, testInfo) => {
        const isDesktop = testInfo.project.name.includes('Desktop');

        await page.goto('/', { waitUntil: 'networkidle' });
        expect(page.url()).toContain('8000');

        // Verify Hero Section is rendered
        const hero = page.locator('section.relative').first();
        await expect(hero).toBeVisible();

        // Locate Announcement Ticker between Hero and Featured Products
        const ticker = page.locator('.perspective-container').locator('..');
        await expect(ticker).toBeVisible();

        // Verify LED Pulse is present
        const ledPulse = ticker.locator('.animate-ping');
        await expect(ledPulse).toBeVisible();

        // Verify Category Badge text in Vietnamese
        const badge = ticker.locator('span.whitespace-nowrap');
        await expect(badge).toBeVisible();
        const badgeText = await badge.innerText();
        expect(['GIAO HÀNG', 'KHUYẾN MÃI', 'HOT UPDATE']).toContain(badgeText);

        // Verify Title text is displayed in Vietnamese
        const titleEl = ticker.locator('.split-flap-card');
        await expect(titleEl).toBeVisible();
        const titleText = await titleEl.innerText();
        expect(
            titleText.includes('Giao hàng hỏa tốc trong 24h') ||
            titleText.includes('Ưu đãi in 3D SLA')
        ).toBeTruthy();

        // Verify CTA button on Desktop
        if (isDesktop) {
            const ctaBtn = ticker.locator('a, button').filter({ hasText: /Tư vấn|Xem ngay/ });
            await expect(ctaBtn.first()).toBeVisible();

            // Verify Prev / Next buttons
            const nextBtn = ticker.locator('button[aria-label="Next announcement"]');
            await expect(nextBtn).toBeVisible();
            const prevBtn = ticker.locator('button[aria-label="Previous announcement"]');
            await expect(prevBtn).toBeVisible();

            // Test clicking next button to cycle announcement
            await nextBtn.click();
            await page.waitForTimeout(600); // Wait for 3D flip animation
            const newTitleText = await titleEl.innerText();
            expect(newTitleText.length).toBeGreaterThan(5);

            // Screenshot Vietnamese Ticker
            await ticker.screenshot({
                path: path.join('storage', 'app', 'public', 'evidence', 'ticker-vi-desktop.png'),
            });
            await page.screenshot({
                path: path.join('tests', 'e2e', 'screenshots', 'homepage-vi-desktop.png'),
                fullPage: false,
            });
        }
    });

    test('2. Verify English Homepage (/en) Announcement Ticker', async ({ page }, testInfo) => {
        const isDesktop = testInfo.project.name.includes('Desktop');

        await page.goto('/en', { waitUntil: 'networkidle' });
        expect(page.url()).toContain('/en');

        const ticker = page.locator('.perspective-container').locator('..');
        await expect(ticker).toBeVisible();

        // Verify Category Badge text in English
        const badge = ticker.locator('span.whitespace-nowrap');
        await expect(badge).toBeVisible();
        const badgeText = await badge.innerText();
        expect(['DELIVERY', 'PROMOTION', 'HOT UPDATE']).toContain(badgeText);

        // Verify Title text is displayed in English
        const titleEl = ticker.locator('.split-flap-card');
        await expect(titleEl).toBeVisible();
        const titleText = await titleEl.innerText();
        expect(
            titleText.includes('Express 24-hour fast delivery') ||
            titleText.includes('High-Precision SLA 3D Printing')
        ).toBeTruthy();

        if (isDesktop) {
            const ctaBtn = ticker.locator('a, button').filter({ hasText: /Contact|Explore/ });
            await expect(ctaBtn.first()).toBeVisible();

            // Screenshot English Ticker
            await ticker.screenshot({
                path: path.join('storage', 'app', 'public', 'evidence', 'ticker-en-desktop.png'),
            });
            await page.screenshot({
                path: path.join('tests', 'e2e', 'screenshots', 'homepage-en-desktop.png'),
                fullPage: false,
            });
        }
    });

    test('3. Verify Responsive & Spacing on Mobile (375x812)', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const ticker = page.locator('.perspective-container').locator('..');
        await expect(ticker).toBeVisible();

        // Check horizontal overflow
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(hasHorizontalScroll).toBeFalsy();

        // Capture mobile screenshot
        await ticker.screenshot({
            path: path.join('storage', 'app', 'public', 'evidence', 'ticker-vi-mobile.png'),
        });
        await page.screenshot({
            path: path.join('tests', 'e2e', 'screenshots', 'homepage-vi-mobile.png'),
            fullPage: false,
        });
    });
});
