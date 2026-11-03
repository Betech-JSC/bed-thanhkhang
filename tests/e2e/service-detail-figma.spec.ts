import { test, expect } from '@playwright/test';

test.describe('HH Maker Service Navigation & Service Detail Figma Node 2367:2088 Strict Verification', () => {

    test('1. Homepage: Smart Navigation Links to Service Detail Pages', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        const servicesSection = page.locator('#services-section');
        await expect(servicesSection).toBeVisible();

        // 3 service cards should all be clickable links (<a> tags)
        const serviceLinks = servicesSection.locator('a.group');
        await expect(serviceLinks).toHaveCount(3);

        // Verify hrefs
        const href0 = await serviceLinks.nth(0).getAttribute('href');
        const href1 = await serviceLinks.nth(1).getAttribute('href');
        const href2 = await serviceLinks.nth(2).getAttribute('href');

        expect(href0).toMatch(/\/dich-vu\/gia-cong-3d/);
        expect(href1).toMatch(/\/dich-vu\/thiet-ke-3d/);
        expect(href2).toMatch(/\/dich-vu\/lam-do-custom/);

        // Click on "Thiết kế 3D"
        await serviceLinks.nth(1).click();
        await page.waitForURL('**/dich-vu/thiet-ke-3d');
        expect(page.url()).toContain('/dich-vu/thiet-ke-3d');

        // Screenshot homepage navigation proof
        await page.screenshot({
            path: 'tests/e2e/screenshots/homepage-services-navigation.png',
        });
    });

    test('2. Services List Page: Links on Title, Illustration, and Secondary Action Button', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Find service block for "thiet-ke-3d"
        const designBlock = page.locator('[data-service-slug="thiet-ke-3d"]').first();
        await expect(designBlock).toBeVisible();

        // Verify illustration card inside designBlock is a link to /dich-vu/thiet-ke-3d
        const illustrationLink = designBlock.locator('a[href*="/dich-vu/thiet-ke-3d"]').first();
        await expect(illustrationLink).toBeVisible();

        // Verify title link to /dich-vu/thiet-ke-3d
        const titleLink = designBlock.locator('a:has(h2:has-text("Thiết kế 3D"))').first();
        await expect(titleLink).toBeVisible();
        expect(await titleLink.getAttribute('href')).toMatch(/\/dich-vu\/thiet-ke-3d/);

        // Verify secondary action button "Tìm hiểu chi tiết"
        const detailButton = designBlock.locator('a:has-text("Tìm hiểu chi tiết")').first();
        await expect(detailButton).toBeVisible();
        expect(await detailButton.getAttribute('href')).toMatch(/\/dich-vu\/thiet-ke-3d/);

        // Verify second service block ("gia-cong-3d")
        const printBlock = page.locator('[data-service-slug="gia-cong-3d"]').first();
        await expect(printBlock).toBeVisible();

        const printDetailButton = printBlock.locator('a:has-text("Tìm hiểu chi tiết")').first();
        await expect(printDetailButton).toBeVisible();
        expect(await printDetailButton.getAttribute('href')).toMatch(/\/dich-vu\/gia-cong-3d/);

        await printDetailButton.click();
        await page.waitForURL('**/dich-vu/gia-cong-3d');
        expect(page.url()).toContain('/dich-vu/gia-cong-3d');

        // Screenshot services list navigation
        await page.screenshot({
            path: 'tests/e2e/screenshots/services-list-detail-links.png',
        });
    });

    test('3. Desktop 1440x900: Service Detail 100% Figma Node 2367:2088 (Hero, TOC Scrollspy, 4 Sections, Related, CTA)', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Main Container: max-w 1152px
        const mainContainer = page.locator('div.max-w-\\[1152px\\]').first();
        await expect(mainContainer).toBeVisible();

        // 1. Breadcrumbs
        const breadcrumbNav = page.locator('nav[aria-label="Breadcrumb"]').first();
        await expect(breadcrumbNav).toBeVisible();
        await expect(breadcrumbNav.locator('text=Trang chủ')).toBeVisible();
        const dichVuBreadcrumb = breadcrumbNav.locator('a:has-text("Dịch vụ")');
        await expect(dichVuBreadcrumb).toBeVisible();
        expect(await dichVuBreadcrumb.getAttribute('href')).toMatch(/\/dich-vu/);
        await expect(breadcrumbNav.locator('text=Thiết kế 3D')).toBeVisible();

        // 2. Hero H1 and Lead
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        const h1Text = await h1.innerText();
        expect(h1Text).toContain('Thiết kế 3D');

        // 3. Showcase Banner Card (Node 2352:11858 - 1152x310px, 3 columns)
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

        // 4. Sticky Sidebar TOC (Node 2367:2178 - 248px)
        const tocHeader = page.locator('text=TRONG BÀI VIẾT NÀY').first();
        await expect(tocHeader).toBeVisible();

        const tocButtons = page.locator('aside nav button');
        await expect(tocButtons).toHaveCount(4);

        // First TOC button is active with blue border and blue text
        const firstTocBtn = tocButtons.first();
        await expect(firstTocBtn).toHaveClass(/border-l-2/);
        await expect(firstTocBtn).toHaveClass(/border-\[\#2970FF\]/);

        // 5. 4 Technical Sections (Node 2367:2196 - 840px)
        // Section 01: Khi nào bạn cần thiết kế 3D?
        const sec1 = page.locator('#section-01');
        await expect(sec1).toBeVisible();
        await expect(sec1.locator('h2:has-text("Khi nào bạn cần thiết kế 3D?")')).toBeVisible();

        // Section 02: Từ ý tưởng tới mô hình 3D (4 steps)
        const sec2 = page.locator('#section-02');
        await expect(sec2).toBeVisible();
        await expect(sec2.locator('h2:has-text("Từ ý tưởng tới mô hình 3D")')).toBeVisible();
        const stepBadges = sec2.locator('.w-10.h-10');
        await expect(stepBadges).toHaveCount(4);

        // Section 03: Bạn cần chuẩn bị gì? (Checklist card #171B30)
        const sec3 = page.locator('#section-03');
        await expect(sec3).toBeVisible();
        await expect(sec3.locator('h2:has-text("Bạn cần chuẩn bị gì?")')).toBeVisible();
        const checklistCard = sec3.locator('.bg-\\[\\#171B30\\]');
        await expect(checklistCard).toBeVisible();
        const checkmarks = checklistCard.locator('text=✓');
        expect(await checkmarks.count()).toBeGreaterThanOrEqual(4);

        // Section 04: Cùng hoàn thiện trước khi gia công (Callout border-l-2)
        const sec4 = page.locator('#section-04');
        await expect(sec4).toBeVisible();
        await expect(sec4.locator('h2:has-text("Cùng hoàn thiện trước khi gia công")')).toBeVisible();
        const callout = sec4.locator('.border-l-2.border-\\[\\#2970FF\\]');
        await expect(callout).toBeVisible();

        // 6. Section 3: Related Services (Node 2367:2270)
        const relatedH2 = page.locator('h2:has-text("Dịch vụ liên quan")').first();
        await expect(relatedH2).toBeVisible();
        const relatedCards = page.locator('section:has-text("Dịch vụ liên quan") a.group');
        expect(await relatedCards.count()).toBeGreaterThanOrEqual(2);

        // Verify related service links point to /dich-vu/{slug}
        const relHref0 = await relatedCards.nth(0).getAttribute('href');
        const relHref1 = await relatedCards.nth(1).getAttribute('href');
        expect(relHref0).toMatch(/\/dich-vu\//);
        expect(relHref1).toMatch(/\/dich-vu\//);

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

        // Capture Desktop Screenshot
        await page.screenshot({
            path: 'tests/e2e/screenshots/service-detail-1440-figma.png',
            fullPage: true,
        });
    });

    test('4. Laptop 1280x800 & Mobile 375x667: Responsive Verification', async ({ page }) => {
        // Test Laptop 1280x800
        await page.setViewportSize({ width: 1280, height: 800 });
        let response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        let hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        await page.screenshot({
            path: 'tests/e2e/screenshots/service-detail-1280-figma.png',
            fullPage: true,
        });

        // Test Mobile 375x667
        await page.setViewportSize({ width: 375, height: 667 });
        response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        const mobileTocButtons = page.locator('.overflow-x-auto button');
        await expect(mobileTocButtons).toHaveCount(4);

        hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        await page.screenshot({
            path: 'tests/e2e/screenshots/service-detail-375-figma.png',
            fullPage: true,
        });
    });
});
