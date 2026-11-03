import { test, expect } from '@playwright/test';

test.describe('HH Maker Service Detail Page Figma Node 2367:2088 Strict Verification', () => {

    test('1. Desktop 1440x900: Hero, Sticky TOC, Scrollspy, Technical Sections, Related Services, and CTA Banner', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Verify container max-w 1152px
        const mainContainer = page.locator('div.max-w-\\[1152px\\]').first();
        await expect(mainContainer).toBeVisible();

        // 1. Breadcrumbs
        const breadcrumbNav = page.locator('nav[aria-label="Breadcrumb"]').first();
        await expect(breadcrumbNav).toBeVisible();
        await expect(breadcrumbNav.locator('text=Trang chủ')).toBeVisible();
        await expect(breadcrumbNav.locator('text=Dịch vụ')).toBeVisible();
        await expect(breadcrumbNav.locator('text=Thiết kế 3D')).toBeVisible();

        // 2. Hero H1 and Description (Node 2367:2162)
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        const h1Text = await h1.innerText();
        expect(h1Text).toContain('Thiết kế 3D');

        // 3. Khung minh họa kỹ thuật (Node 2352:11858 - 1152x310px)
        const colStart = page.locator('text=BẮT ĐẦU TỪ').first();
        await expect(colStart).toBeVisible();
        await expect(page.locator('text=Ý tưởng').first()).toBeVisible();
        await expect(page.locator('text=Ảnh tham khảo').first()).toBeVisible();
        await expect(page.locator('text=Bản phác thảo').first()).toBeVisible();

        const colClarify = page.locator('text=CÙNG LÀM RÕ').first();
        await expect(colClarify).toBeVisible();
        await expect(page.locator('text=Tỉ lệ & hình khối').first()).toBeVisible();
        await expect(page.locator('text=Cấu trúc & chi tiết').first()).toBeVisible();
        await expect(page.locator('text=Khả năng chế tác').first()).toBeVisible();

        // 4. Sidebar TOC (Node 2367:2178 - 248px)
        const tocHeader = page.locator('text=TRONG BÀI VIẾT NÀY').first();
        await expect(tocHeader).toBeVisible();

        const tocButtons = page.locator('aside nav button');
        await expect(tocButtons).toHaveCount(4);

        // Verify initially section-01 is active with border-l-2 border-[#2970FF] and text-[#84ADFF]
        const firstTocBtn = tocButtons.first();
        await expect(firstTocBtn).toHaveClass(/border-l-2/);
        await expect(firstTocBtn).toHaveClass(/border-\[\#2970FF\]/);

        // 5. Technical Sections (Node 2367:2196)
        // Section 01
        const sec1 = page.locator('#section-01');
        await expect(sec1).toBeVisible();
        await expect(sec1.locator('h2:has-text("Khi nào bạn cần thiết kế 3D?")')).toBeVisible();

        // Section 02: 4 steps
        const sec2 = page.locator('#section-02');
        await expect(sec2).toBeVisible();
        await expect(sec2.locator('h2:has-text("Từ ý tưởng tới mô hình 3D")')).toBeVisible();
        const stepBadges = sec2.locator('.w-10.h-10');
        await expect(stepBadges).toHaveCount(4);

        // Section 03: Checklist
        const sec3 = page.locator('#section-03');
        await expect(sec3).toBeVisible();
        await expect(sec3.locator('h2:has-text("Bạn cần chuẩn bị gì?")')).toBeVisible();
        const checklistCard = sec3.locator('.bg-\\[\\#171B30\\]');
        await expect(checklistCard).toBeVisible();
        const checkmarks = checklistCard.locator('text=✓');
        expect(await checkmarks.count()).toBeGreaterThanOrEqual(4);

        // Section 04: Completion and Callout
        const sec4 = page.locator('#section-04');
        await expect(sec4).toBeVisible();
        await expect(sec4.locator('h2:has-text("Cùng hoàn thiện trước khi gia công")')).toBeVisible();
        const callout = sec4.locator('.border-l-2.border-\\[\\#2970FF\\]');
        await expect(callout).toBeVisible();
        expect(await callout.innerText()).toContain('Một mô hình đẹp trên màn hình');

        // 6. Section 3: Related Services (Node 2367:2270)
        const relatedH2 = page.locator('h2:has-text("Dịch vụ liên quan")').first();
        await expect(relatedH2).toBeVisible();
        const relatedCards = page.locator('section:has-text("Dịch vụ liên quan") a.group');
        expect(await relatedCards.count()).toBeGreaterThanOrEqual(2);

        // 7. Section 4: Banner CTA (Node 2352:11794)
        const ctaH2 = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")').first();
        await expect(ctaH2).toBeVisible();
        const ctaBtn = page.locator('a:has-text("Nhận tư vấn ngay")').first();
        await expect(ctaBtn).toBeVisible();

        // 8. Interaction: Click on TOC item 03 and verify scroll
        const tocBtn3 = tocButtons.nth(2);
        await tocBtn3.click();
        await page.waitForTimeout(600);
        await expect(sec3).toBeInViewport();

        // Zero horizontal scroll check
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Take Desktop 1440x900 Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-1440-service-detail-figma-strict.png',
            fullPage: true,
        });
    });

    test('2. Laptop 1280x800: Responsive Check & Zero Horizontal Overflow', async ({ page }) => {
        await page.setViewportSize({ width: 1280, height: 800 });
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Verify elements are visible
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();

        const tocHeader = page.locator('text=TRONG BÀI VIẾT NÀY').first();
        await expect(tocHeader).toBeVisible();

        // Zero horizontal scroll check
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Take Laptop 1280x800 Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/desktop-1280-service-detail-figma-strict.png',
            fullPage: true,
        });
    });

    test('3. Mobile 375x667: Mobile TOC Horizontal Scroll & Responsive Layout', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 });
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Mobile horizontal TOC pills
        const mobileTocButtons = page.locator('.overflow-x-auto button');
        await expect(mobileTocButtons).toHaveCount(4);

        // Verify zero horizontal scroll
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Take Mobile 375 Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/mobile-375-service-detail-figma-strict.png',
            fullPage: true,
        });
    });
});
