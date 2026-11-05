import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Product Filter & Product Card Verification', () => {
    test.beforeAll(async () => {
        const evidenceDir = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }
    });

    test('1. Sidebar/Drawer category buttons have no chevron arrows and Card hover micro-interaction works', async ({ page, isMobile }) => {
        // Navigate to product catalog page
        await page.goto('/san-pham', { waitUntil: 'networkidle' });

        // If redirected or path is /products, wait for content
        await expect(page.locator('h1')).toContainText(/Ý TƯỞNG THÀNH HÌNH|IDEAS/i);

        // --- PART 1: Product Filter Categories Check ---
        let filterContainer;
        if (isMobile) {
            // Open mobile filter drawer
            const openFilterBtn = page.locator('button:has-text("Mở bộ lọc"), button[title="Mở bộ lọc"]');
            await openFilterBtn.click();
            filterContainer = page.locator('.fixed.inset-0.z-50');
            await expect(filterContainer).toBeVisible();
        } else {
            filterContainer = page.locator('aside');
            await expect(filterContainer).toBeVisible();
        }

        // Locate category buttons inside the filter container
        const categoryButtons = filterContainer.locator('button:has-text("Tất cả"), button:has-text("Trang trí"), button:has-text("Móc khoá"), button:has-text("Gia công")');
        const count = await categoryButtons.count();
        expect(count).toBeGreaterThan(0);

        // Verify that NONE of the category buttons have any svg chevron arrow inside
        for (let i = 0; i < count; i++) {
            const btn = categoryButtons.nth(i);
            const svgInBtn = btn.locator('svg');
            const svgCount = await svgInBtn.count();
            expect(svgCount).toBe(0);
        }

        if (!isMobile) {
            // Capture screenshot of sidebar filter
            const evidenceFilterPath = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence', 'product-filter-no-arrows.png');
            await filterContainer.screenshot({ path: evidenceFilterPath });
            console.log(`Saved screenshot to ${evidenceFilterPath}`);
        } else {
            // Close mobile filter drawer
            const closeBtn = filterContainer.locator('button:has(svg)').first();
            await closeBtn.click({ force: true });
            await page.waitForTimeout(300);
        }

        // --- PART 2: Product Cards Micro-interaction ---
        // Locate product cards in the grid
        const productCards = page.locator('div.grid a.group');
        const cardCount = await productCards.count();
        expect(cardCount).toBeGreaterThan(0);

        const firstCard = productCards.first();
        await expect(firstCard).toBeVisible();

        // Verify class list contains the micro-interaction classes
        const cardClass = await firstCard.getAttribute('class');
        expect(cardClass).toContain('hover:-translate-y-1.5');
        expect(cardClass).toContain('hover:border-[#2970FF]/50');
        expect(cardClass).toContain('hover:shadow-2xl');
        expect(cardClass).toContain('hover:shadow-[#2970FF]/20');
        expect(cardClass).toContain('border-white/5');
        expect(cardClass).toContain('transition-all');

        if (!isMobile) {
            // Hover over the first card
            await firstCard.hover();
            await page.waitForTimeout(300);

            // Capture screenshot of the product grid / hovered card
            const evidenceCardPath = path.join(process.cwd(), 'storage', 'app', 'public', 'evidence', 'product-card-hover.png');
            const gridContainer = page.locator('.w-full.grid').first();
            await gridContainer.screenshot({ path: evidenceCardPath });
            console.log(`Saved screenshot to ${evidenceCardPath}`);
        }
    });
});
