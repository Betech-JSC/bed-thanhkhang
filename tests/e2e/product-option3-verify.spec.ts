import { test, expect } from '@playwright/test';

test.describe('HH Maker Product Page Option 3 (Sidebar Layout) Verification', () => {
    test('1. Verify Option 3 Desktop Layout (Sidebar 240px, 2 Cards, 3-column Grid, CTA Banner)', async ({ page, isMobile }) => {
        // Skip on mobile for desktop-specific assertions
        test.skip(isMobile, 'Desktop-only test');

        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // 1. Header Intro
        const eyebrow = page.locator('span:has-text("BỘ SƯU TẬP HH MAKER")').first();
        await expect(eyebrow).toBeVisible();
        const mainTitle = page.locator('h1:has-text("Ý TƯỞNG THÀNH HÌNH")').first();
        await expect(mainTitle).toBeVisible();

        // 2. Desktop Sidebar
        const sidebar = page.locator('aside').first();
        await expect(sidebar).toBeVisible();
        const sidebarBox = await sidebar.boundingBox();
        expect(sidebarBox?.width).toBeGreaterThanOrEqual(238);
        expect(sidebarBox?.width).toBeLessThanOrEqual(245);

        // 2 Cards in Sidebar
        const cards = sidebar.locator('> div > div');
        await expect(cards).toHaveCount(2);

        // Card 1: Danh mục sản phẩm & Lọc theo nhu cầu
        const card1 = cards.nth(0);
        await expect(card1).toBeVisible();
        await expect(card1.locator('h4:has-text("DANH MỤC SẢN PHẨM")')).toBeVisible();
        await expect(card1.locator('h4:has-text("LỌC THEO NHU CẦU")')).toBeVisible();

        // Check Card 1 Background Color (#30374F => rgb(48, 55, 79))
        const card1Bg = await card1.evaluate((el) => window.getComputedStyle(el).backgroundColor);
        expect(card1Bg).toBe('rgb(48, 55, 79)');

        // Verify Search Input in Card 1
        const searchInput = card1.locator('input[placeholder*="Tìm kiếm"]');
        await expect(searchInput).toBeVisible();

        // Verify "LỌC THEO NHU CẦU" text is on LEFT and checkbox is on RIGHT
        const purposeButtons = card1.locator('button:has-text("Trang trí không gian"), button:has-text("Đồ dùng hằng ngày"), button:has-text("Quà tặng cá nhân")');
        const count = await purposeButtons.count();
        expect(count).toBe(3);

        for (let i = 0; i < count; i++) {
            const btn = purposeButtons.nth(i);
            const textSpan = btn.locator('span').first();
            const checkboxDiv = btn.locator('div').first();

            const textBox = await textSpan.boundingBox();
            const checkboxBox = await checkboxDiv.boundingBox();

            expect(textBox).not.toBeNull();
            expect(checkboxBox).not.toBeNull();

            // Text on LEFT (smaller X coordinate)
            expect(textBox!.x).toBeLessThan(checkboxBox!.x);
        }

        // Card 2: Tuỳ chỉnh sản phẩm
        const card2 = cards.nth(1);
        await expect(card2).toBeVisible();
        await expect(card2.locator('h4:has-text("Tuỳ chỉnh sản phẩm")')).toBeVisible();
        await expect(card2.locator('p:has-text("Màu sắc, kích thước và cá nhân hoá")')).toBeVisible();

        const card2Bg = await card2.evaluate((el) => window.getComputedStyle(el).backgroundColor);
        expect(card2Bg).toBe('rgb(48, 55, 79)');

        const ctaConsultBtn = card2.locator('a[href="/lien-he"]:has-text("Liên hệ tư vấn")');
        await expect(ctaConsultBtn).toBeVisible();

        // 3. Right Column: Toolbar & Grid
        const breadcrumb = page.locator('span:has-text("Sản phẩm (")').first();
        await expect(breadcrumb).toBeVisible();

        const sortSelect = page.locator('select#sort-select');
        await expect(sortSelect).toBeVisible();

        // Products Grid (3 columns on Desktop)
        const productCards = page.locator('a[href*="/san-pham/"]');
        const productCount = await productCards.count();
        expect(productCount).toBeGreaterThanOrEqual(3);

        // Check 3-column row alignment: First 3 cards should have the same top Y coordinate
        const card0Box = await productCards.nth(0).boundingBox();
        const card1Box = await productCards.nth(1).boundingBox();
        const card2Box = await productCards.nth(2).boundingBox();

        expect(card0Box?.y).toBeCloseTo(card1Box?.y || 0, 1);
        expect(card1Box?.y).toBeCloseTo(card2Box?.y || 0, 1);

        // Card width ~273px
        expect(card0Box?.width).toBeGreaterThanOrEqual(265);
        expect(card0Box?.width).toBeLessThanOrEqual(285);
        // Card height ~419px
        expect(card0Box?.height).toBeGreaterThanOrEqual(415);
        expect(card0Box?.height).toBeLessThanOrEqual(425);

        // 4. Bottom CTA Banner
        const bannerHeading = page.locator('h3:has-text("Tạo nên phiên bản mang dấu ấn riêng")');
        await expect(bannerHeading).toBeVisible();
        const bannerBtn = page.locator('section a[href="/lien-he"]:has-text("Nhận tư vấn ngay")');
        await expect(bannerBtn).toBeVisible();

        // Screenshot desktop
        await page.screenshot({ path: 'tests/e2e/screenshots/product-option3-desktop.png', fullPage: true });
    });

    test('2. Verify Option 3 Mobile Drawer and Responsive Flow', async ({ page, isMobile }) => {
        test.skip(!isMobile, 'Mobile-only test');

        await page.setViewportSize({ width: 375, height: 667 });
        const response = await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Desktop aside should be hidden
        const desktopSidebar = page.locator('aside.hidden.lg\\:block');
        await expect(desktopSidebar).toBeHidden();

        // Open filter button should be visible
        const mobileFilterBtn = page.locator('button[title="Mở bộ lọc"]');
        await expect(mobileFilterBtn).toBeVisible();

        // Click to open filter drawer
        await mobileFilterBtn.click();

        // Offcanvas filter should appear
        const drawer = page.locator('.fixed.inset-0.z-50.lg\\:hidden');
        await expect(drawer).toBeVisible();

        const drawerTitle = drawer.locator('span:has-text("Bộ lọc sản phẩm")');
        await expect(drawerTitle).toBeVisible();

        // Screenshot mobile
        await page.screenshot({ path: 'tests/e2e/screenshots/product-option3-mobile.png', fullPage: true });
    });
});
