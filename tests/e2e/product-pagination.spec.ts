import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Product Pagination Workflow & State Verification', () => {
    test.beforeAll(async () => {
        const evidenceDir = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }
    });

    test('Pagination initial state, numbered buttons, boundary lock, and min-height', async ({ page, isMobile }) => {
        // 1. Navigate to products catalog page
        await page.goto('/san-pham', { waitUntil: 'networkidle' });
        await expect(page.locator('h1')).toContainText(/Ý TƯỞNG THÀNH HÌNH|IDEAS/i);

        // 2. Check grid min-height
        const gridWrapper = page.locator('div.w-full.min-h-\\[600px\\]');
        await expect(gridWrapper).toBeVisible();
        const box = await gridWrapper.boundingBox();
        expect(box?.height).toBeGreaterThanOrEqual(600);

        // 3. Locate pagination container
        const paginationContainer = page.locator('div.flex.items-center.justify-between.w-full.h-\\[57px\\]');
        await expect(paginationContainer).toBeVisible();

        // 4. Verify Previous button on page 1 is disabled and has proper classes
        const prevBtn = paginationContainer.locator('button:has-text("Trang trước")');
        await expect(prevBtn).toBeDisabled();
        const prevClass = await prevBtn.getAttribute('class');
        expect(prevClass).toContain('opacity-40');
        expect(prevClass).toContain('cursor-not-allowed');
        expect(prevClass).toContain('pointer-events-none');

        // 5. Verify Next button on page 1 is enabled
        const nextBtn = paginationContainer.locator('button:has-text("Trang tiếp theo")');
        await expect(nextBtn).toBeEnabled();

        // 6. Verify numbered buttons
        const page1Btn = paginationContainer.locator('button:text-is("1")');
        const page2Btn = paginationContainer.locator('button:text-is("2")');
        await expect(page1Btn).toBeVisible();
        await expect(page2Btn).toBeVisible();

        // Page 1 should be active (blue tech style)
        const page1Class = await page1Btn.getAttribute('class');
        expect(page1Class).toContain('bg-[#2970FF]');
        expect(page1Class).toContain('text-white');
        expect(page1Class).toContain('shadow-[0_0_12px_rgba(41,112,255,0.4)]');

        // Page 2 should be inactive
        const page2Class = await page2Btn.getAttribute('class');
        expect(page2Class).toContain('text-[#B9C0D4]');
        expect(page2Class).not.toContain('bg-[#2970FF]');

        if (!isMobile) {
            const page1Evidence = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence', 'pagination-page-1.png');
            await paginationContainer.screenshot({ path: page1Evidence });
            console.log(`Saved screenshot to ${page1Evidence}`);
        }

        // 7. Click Page 2 button (or Next button)
        await page2Btn.click();
        await page.waitForTimeout(300);

        // Verify Page 2 is now active
        const page2ActiveClass = await page2Btn.getAttribute('class');
        expect(page2ActiveClass).toContain('bg-[#2970FF]');
        expect(page2ActiveClass).toContain('text-white');

        // Verify Page 1 is now inactive
        const page1InactiveClass = await page1Btn.getAttribute('class');
        expect(page1InactiveClass).toContain('text-[#B9C0D4]');
        expect(page1InactiveClass).not.toContain('bg-[#2970FF]');

        // Verify Previous button is now enabled
        await expect(prevBtn).toBeEnabled();

        // Verify Next button is now disabled (on last page)
        await expect(nextBtn).toBeDisabled();
        const nextClass = await nextBtn.getAttribute('class');
        expect(nextClass).toContain('opacity-40');
        expect(nextClass).toContain('cursor-not-allowed');
        expect(nextClass).toContain('pointer-events-none');

        // Verify grid wrapper still maintains min-height on page 2
        const boxPage2 = await gridWrapper.boundingBox();
        expect(boxPage2?.height).toBeGreaterThanOrEqual(600);

        if (!isMobile) {
            const page2Evidence = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence', 'pagination-page-2.png');
            await paginationContainer.screenshot({ path: page2Evidence });
            console.log(`Saved screenshot to ${page2Evidence}`);
        }

        // 8. Test boundary lock - clicking disabled Next button should NOT loop to page 1
        await nextBtn.click({ force: true });
        await page.waitForTimeout(200);
        // Page 2 should remain active
        await expect(page2Btn).toHaveClass(/bg-\[#2970FF\]/);

        // 9. Click Previous button to return to page 1
        await prevBtn.click();
        await page.waitForTimeout(300);
        await expect(page1Btn).toHaveClass(/bg-\[#2970FF\]/);
        await expect(prevBtn).toBeDisabled();
        await expect(nextBtn).toBeEnabled();

        // 10. Test boundary lock on page 1 - clicking disabled Previous button should NOT loop to page 2
        await prevBtn.click({ force: true });
        await page.waitForTimeout(200);
        await expect(page1Btn).toHaveClass(/bg-\[#2970FF\]/);
    });
});
