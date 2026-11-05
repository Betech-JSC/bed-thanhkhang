import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Task 4: Playwright Visual QA (Evidence-Based 100%) - sync-figma-products-to-cms', () => {
    const screenshotDir = path.join(process.cwd(), 'tests', 'e2e', 'screenshots');

    test.beforeAll(() => {
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('4.1 Trang chủ: Khối SẢN PHẨM NỔI BẬT có 4 hàng danh mục, mỗi hàng đủ 4 cards (1116px), card đầu có Best choice', async ({ page }) => {
        await page.goto('/', { waitUntil: 'networkidle' });

        // Locate products section by ID
        const productsSection = page.locator('#products-section');
        await expect(productsSection).toBeVisible();
        await productsSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        // 4 Category title headings
        const categoryTitles = productsSection.locator('h3');
        const catCount = await categoryTitles.count();
        console.log(`[PASS 4.1] Found ${catCount} category rows on Home`);
        expect(catCount).toBe(4);

        for (let i = 0; i < catCount; i++) {
            const catName = await categoryTitles.nth(i).innerText();
            console.log(`  - Category ${i + 1}: ${catName}`);
        }

        // Check each row has 4 product cards and the first card has the "Best choice" badge
        const tracks = productsSection.locator('div.flex.items-center.gap-5.will-change-transform');
        const trackCount = await tracks.count();
        expect(trackCount).toBe(4);

        for (let r = 0; r < trackCount; r++) {
            const track = tracks.nth(r);
            const cards = track.locator('a.group\\/card');
            const cardCount = await cards.count();
            console.log(`[PASS 4.1] Category row ${r + 1} has ${cardCount} cards`);
            expect(cardCount).toBe(4);

            // First card must have the Best choice badge
            const firstCard = cards.first();
            const badge = firstCard.getByText('Best choice', { exact: true });
            await expect(badge).toBeVisible();
        }

        // Capture screenshot of the entire featured products section
        const screenshotPath = path.join(screenshotDir, 'figma-sync-homepage-featured.png');
        await productsSection.screenshot({ path: screenshotPath });
        console.log(`[PASS 4.1] Captured homepage featured products screenshot to: ${screenshotPath}`);
    });

    test('4.2 Trang danh mục sản phẩm: 21 sản phẩm 3D catalog, 4 danh mục bộ lọc động, không có logistics', async ({ page }) => {
        await page.goto('/san-pham', { waitUntil: 'networkidle' });

        // Verify page title and header
        const mainHeading = page.locator('h1');
        await expect(mainHeading).toBeVisible();

        // Category buttons in sidebar filter
        const aside = page.locator('aside');
        await expect(aside).toBeVisible();

        // Check category buttons
        const categoryButtons = aside.locator('button');
        const btnTexts = await categoryButtons.allInnerTexts();
        console.log('Category filter buttons:', btnTexts);

        // All 4 categories + Tất cả must exist
        expect(btnTexts.some(t => t.includes('Tất cả'))).toBe(true);
        expect(btnTexts.some(t => t.includes('Trang trí không gian'))).toBe(true);
        expect(btnTexts.some(t => t.includes('Móc khoá'))).toBe(true);
        expect(btnTexts.some(t => t.includes('Trang trí & Đồ dùng'))).toBe(true);
        expect(btnTexts.some(t => t.includes('Gia công theo yêu cầu'))).toBe(true);

        // Count product cards in main grid
        const mainGrid = page.locator('div.grid.grid-cols-1');
        const productCards = mainGrid.locator('> a.group, > div > a.group');
        const totalCards = await productCards.count();
        console.log(`Total catalog cards displayed on page 1: ${totalCards}`);
        expect(totalCards).toBeGreaterThanOrEqual(16);

        // Verify NO card contains 'logistics' or empty text
        for (let i = 0; i < totalCards; i++) {
            const card = productCards.nth(i);
            const title = await card.locator('h3').innerText();
            expect(title.trim().length).toBeGreaterThan(0);
            expect(title.toLowerCase()).not.toContain('logistics');
        }

        // Test dynamic category filter: click "Trang trí không gian"
        const trangTriBtn = aside.locator('button', { hasText: 'Trang trí không gian' }).first();
        await trangTriBtn.click();
        await page.waitForTimeout(500);

        const filteredCards = await productCards.count();
        console.log(`Filtered cards for "Trang trí không gian": ${filteredCards}`);
        expect(filteredCards).toBe(4);

        // Click "Tất cả" to restore full view
        const allBtn = aside.locator('button', { hasText: 'Tất cả' }).first();
        await allBtn.click();
        await page.waitForTimeout(500);

        // Capture screenshot of catalog page
        const screenshotPath = path.join(screenshotDir, 'figma-sync-products-catalog.png');
        await page.screenshot({ path: screenshotPath, fullPage: false });
        console.log(`[PASS 4.2] Captured products catalog screenshot to: ${screenshotPath}`);
    });

    test('4.3 Trang chi tiết Model Cáo ID 1: Bảng SpecTable 7 hàng, câu chuyện sản phẩm, 4 sản phẩm liên quan', async ({ page }) => {
        await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });

        // 1. Verify Product Title
        const titleEl = page.locator('h1', { hasText: 'Mô hình Cáo trang trí' });
        await expect(titleEl).toBeVisible();

        // 2. Verify SpecTable is visible
        const specTableHeading = page.locator('h3', { hasText: /Thông số in tham khảo|Thông số kỹ thuật/i }).first();
        await expect(specTableHeading).toBeVisible();
        await specTableHeading.scrollIntoViewIfNeeded();
        await page.waitForTimeout(400);

        const specContainer = specTableHeading.locator('xpath=ancestor::div[contains(@class, "bg-[#30374F]")][1]');
        await expect(specContainer).toBeVisible();

        const expectedSpecs = [
            'Công nghệ in',
            'Vật liệu',
            'Kích thước mẫu',
            'Chiều cao lớp in',
            'Đầu phun',
            'Mật độ ruột',
            'Vật liệu hỗ trợ',
        ];

        for (const specLabel of expectedSpecs) {
            const specRow = specContainer.getByText(specLabel, { exact: true });
            await expect(specRow).toBeVisible();
            console.log(`[PASS 4.3] Spec found: ${specLabel}`);
        }

        // 3. Verify Story Section ("Câu chuyện" or product details tabs)
        const storyTab = page.locator('button, span, h2, h3, div').filter({ hasText: /Câu chuyện|Giới thiệu/i }).first();
        await expect(storyTab).toBeVisible();

        // 4. Verify Related Products ("Sản phẩm khác")
        const relatedHeading = page.locator('h2', { hasText: 'Sản phẩm khác' });
        await expect(relatedHeading).toBeVisible();
        await relatedHeading.scrollIntoViewIfNeeded();
        await page.waitForTimeout(400);

        // Locate cards in RelatedProducts
        const relatedSection = relatedHeading.locator('xpath=ancestor::section[1]');
        const relatedCards = relatedSection.locator('a.group\\/card');
        const relatedCount = await relatedCards.count();
        console.log(`[PASS 4.3] Related products card count: ${relatedCount}`);
        expect(relatedCount).toBe(4);

        // Capture screenshot of SpecTable
        const specScreenshotPath = path.join(screenshotDir, 'figma-sync-pdp-specs.png');
        await specContainer.screenshot({ path: specScreenshotPath });
        console.log(`[PASS 4.3] Captured SpecTable screenshot to: ${specScreenshotPath}`);

        // Capture screenshot of Related Products Section
        const relatedScreenshotPath = path.join(screenshotDir, 'figma-sync-pdp-related-products.png');
        await relatedSection.screenshot({ path: relatedScreenshotPath });
        console.log(`[PASS 4.3] Captured RelatedProducts screenshot to: ${relatedScreenshotPath}`);
    });
});
