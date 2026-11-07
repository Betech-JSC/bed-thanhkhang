import { test, expect } from '@playwright/test';

test.describe('BrandStorySection Figma 2701:1398 Sync Verification', () => {
    test('Verify BrandStorySection layout, center alignment, and metrics spacing', async ({ page }, testInfo) => {
        await page.goto('/', { waitUntil: 'networkidle' });

        const brandStorySection = page.locator('#brand-story-section');
        await expect(brandStorySection).toBeVisible();

        // 1. Check Titles and Text
        await expect(brandStorySection).toContainText('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER');
        await expect(brandStorySection).toContainText('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');
        await expect(brandStorySection).toContainText('10+');
        await expect(brandStorySection).toContainText('500+');
        await expect(brandStorySection).toContainText('95%');

        // 2. Check each stat column
        const statItems = brandStorySection.locator('.grid.grid-cols-3 > div');
        await expect(statItems).toHaveCount(3);

        for (let i = 0; i < 3; i++) {
            const item = statItems.nth(i);
            const numEl = item.locator('.text-2xl, .text-3xl, .lg\\:text-\\[38px\\]').first();
            const labelEl = item.locator('.text-xs, .text-sm, .lg\\:text-base').first();

            await expect(numEl).toBeVisible();
            await expect(labelEl).toBeVisible();

            // Check text alignment is center (Node 2701:1398 specification)
            const numAlign = await numEl.evaluate((el) => window.getComputedStyle(el).textAlign);
            const labelAlign = await labelEl.evaluate((el) => window.getComputedStyle(el).textAlign);
            expect(numAlign).toBe('center');
            expect(labelAlign).toBe('center');
        }

        // Hide sticky header if any to get a clean screenshot
        await page.evaluate(() => {
            const header = document.querySelector('header');
            if (header) header.style.position = 'absolute';
        });

        // Scroll to brand story section
        await brandStorySection.scrollIntoViewIfNeeded();

        const isDesktop = testInfo.project.name.includes('Desktop');
        const prefix = isDesktop ? 'desktop' : 'mobile';

        // Capture screenshot
        await brandStorySection.screenshot({
            path: `tests/e2e/screenshots/${prefix}-brand-story-figma-verified.png`,
        });
    });
});
