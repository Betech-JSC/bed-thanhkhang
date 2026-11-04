import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('HH Maker Homepage Flip Announcement Ticker (Refined Option A: Minimalist Center-Aligned)', () => {
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

    test('1. Verify Vietnamese Homepage (/) Refined Minimalist Announcement Ticker', async ({ page }, testInfo) => {
        const isDesktop = testInfo.project.name.includes('Desktop');

        await page.goto('/', { waitUntil: 'networkidle' });
        expect(page.url()).toContain('8000');

        // Locate Announcement Ticker between Hero and Featured Products
        const tickerContainer = page.locator('.perspective-container').locator('..');
        await expect(tickerContainer).toBeVisible();

        // 1. Verify Frosted Gray & Minimalist Styling classes
        const containerClasses = await tickerContainer.getAttribute('class');
        expect(containerClasses).toContain('bg-[#202533]/85');
        expect(containerClasses).toContain('border-white/10');
        expect(containerClasses).toContain('rounded-[12px]');

        // 2. Verify Category Badge & Neon LED Pulse are completely REMOVED
        const ledPulse = tickerContainer.locator('.animate-ping');
        await expect(ledPulse).toHaveCount(0);

        // 3. Verify Navigation Arrows, Counter, and CTA button are completely REMOVED
        const nextBtn = tickerContainer.locator('button[aria-label="Next announcement"]');
        await expect(nextBtn).toHaveCount(0);
        const prevBtn = tickerContainer.locator('button[aria-label="Previous announcement"]');
        await expect(prevBtn).toHaveCount(0);
        const ctaBtn = tickerContainer.locator('a, button').filter({ hasText: /Tư vấn|Xem ngay|Contact|Explore/ });
        await expect(ctaBtn).toHaveCount(0);

        // 4. Verify Title text is displayed in Vietnamese and centered
        const titleSpan = tickerContainer.locator('.split-flap-card span');
        await expect(titleSpan).toBeVisible();
        const titleText = await titleSpan.innerText();
        expect(
            titleText.includes('Giao hàng hỏa tốc trong 24h') ||
            titleText.includes('Ưu đãi in 3D SLA')
        ).toBeTruthy();

        // 5. Verify typography classes (centered, upright, font-medium, slate-100)
        const spanClasses = await titleSpan.getAttribute('class');
        expect(spanClasses).toContain('text-center');
        expect(spanClasses).toContain('font-medium');
        expect(spanClasses).toContain('text-slate-100');

        // 6. Screenshot on Desktop
        if (isDesktop) {
            await tickerContainer.screenshot({
                path: path.join('storage', 'app', 'public', 'evidence', 'ticker-refined-desktop.png'),
            });
            await tickerContainer.screenshot({
                path: path.join('storage', 'app', 'public', 'evidence', 'ticker-vi-desktop.png'),
            });
            await page.screenshot({
                path: path.join('tests', 'e2e', 'screenshots', 'homepage-refined-desktop.png'),
                fullPage: false,
            });
        }
    });

    test('2. Verify English Homepage (/en) Refined Minimalist Announcement Ticker', async ({ page }, testInfo) => {
        const isDesktop = testInfo.project.name.includes('Desktop');

        await page.goto('/en', { waitUntil: 'networkidle' });
        expect(page.url()).toContain('/en');

        const tickerContainer = page.locator('.perspective-container').locator('..');
        await expect(tickerContainer).toBeVisible();

        // Verify Frosted Gray & Minimalist Styling
        const containerClasses = await tickerContainer.getAttribute('class');
        expect(containerClasses).toContain('bg-[#202533]/85');

        // Verify Title text is displayed in English
        const titleSpan = tickerContainer.locator('.split-flap-card span');
        await expect(titleSpan).toBeVisible();
        const titleText = await titleSpan.innerText();
        expect(
            titleText.includes('Express 24-hour fast delivery') ||
            titleText.includes('High-Precision SLA 3D Printing')
        ).toBeTruthy();

        // Ensure no badge or arrows in English version either
        await expect(tickerContainer.locator('.animate-ping')).toHaveCount(0);
        await expect(tickerContainer.locator('button[aria-label="Next announcement"]')).toHaveCount(0);

        if (isDesktop) {
            await tickerContainer.screenshot({
                path: path.join('storage', 'app', 'public', 'evidence', 'ticker-en-desktop.png'),
            });
        }
    });

    test('3. Verify Responsive & Spacing on Mobile (375x812)', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 812 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const tickerContainer = page.locator('.perspective-container').locator('..');
        await expect(tickerContainer).toBeVisible();

        // Check horizontal overflow
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > document.documentElement.clientWidth;
        });
        expect(hasHorizontalScroll).toBeFalsy();

        // Capture mobile screenshots
        await tickerContainer.screenshot({
            path: path.join('storage', 'app', 'public', 'evidence', 'ticker-refined-mobile.png'),
        });
        await tickerContainer.screenshot({
            path: path.join('storage', 'app', 'public', 'evidence', 'ticker-vi-mobile.png'),
        });
        await page.screenshot({
            path: path.join('tests', 'e2e', 'screenshots', 'homepage-refined-mobile.png'),
            fullPage: false,
        });
    });
});
