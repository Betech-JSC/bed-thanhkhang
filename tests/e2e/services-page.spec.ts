import { test, expect } from '@playwright/test';

test.describe('HH Maker Services Page Quality & Interaction Audit', () => {
    test('1. Services Page (/dich-vu): Renders Hero, 3 Service Blocks with Z-pattern and CTAs', async ({ page, isMobile }) => {
        const response = await page.goto('/dich-vu', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header check
        const header = page.locator('header').first();
        await expect(header).toBeVisible();

        // 1. Hero Section Check
        const badge = page.locator('text=CÁC DỊCH VỤ IN ẤN TẠI HH MAKER').first();
        await expect(badge).toBeVisible();

        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        const h1Text = await h1.innerText();
        expect(h1Text).toContain('MỘT Ý TƯỞNG - NHIỀU KHẢ NĂNG');

        // 2. Service 01: Thiết kế 3D
        const service1Category = page.locator('text=01 / DỊCH VỤ').first();
        await expect(service1Category).toBeVisible();

        const service1Title = page.locator('h2:has-text("Thiết kế 3D")').first();
        await expect(service1Title).toBeVisible();

        const service1Subtitle = page.locator('text=Từ phác thảo đến hình khối rõ ràng.').first();
        await expect(service1Subtitle).toBeVisible();

        const service1Cta = page.locator('a:has-text("Liên hệ tư vấn thiết kế 3D")').first();
        await expect(service1Cta).toBeVisible();

        // 3. Service 02: Gia công 3D
        const service2Category = page.locator('text=02 / DỊCH VỤ').first();
        await expect(service2Category).toBeVisible();

        const service2Title = page.locator('h2:has-text("Gia công 3D")').first();
        await expect(service2Title).toBeVisible();

        const service2Subtitle = page.locator('text=Đưa bản thiết kế ra ngoài màn hình.').first();
        await expect(service2Subtitle).toBeVisible();

        const service2Cta = page.locator('a:has-text("Liên hệ tư vấn gia công 3D")').first();
        await expect(service2Cta).toBeVisible();

        // 4. Service 03: Làm đồ custom
        const service3Category = page.locator('text=03 / DỊCH VỤ').first();
        await expect(service3Category).toBeVisible();

        const service3Title = page.locator('h2:has-text("Làm đồ custom")').first();
        await expect(service3Title).toBeVisible();

        const service3Subtitle = page.locator('text=Không chỉ khác biệt. Là của riêng bạn.').first();
        await expect(service3Subtitle).toBeVisible();

        const service3Cta = page.locator('a:has-text("Liên hệ tư vấn làm đồ custom")').first();
        await expect(service3Cta).toBeVisible();

        // 5. Check 2-Column Comparison Tables (Bạn chuẩn bị / Cùng hoàn thiện)
        const prepTitles = page.locator('text=BẠN CHUẨN BỊ');
        expect(await prepTitles.count()).toBeGreaterThanOrEqual(3);

        const compTitles = page.locator('text=CÙNG HOÀN THIỆN');
        expect(await compTitles.count()).toBeGreaterThanOrEqual(3);

        // 6. Zero horizontal scroll check
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // 7. Footer check
        const footer = page.locator('footer').first();
        await expect(footer).toBeVisible();

        // 8. Screenshot for report
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-services-page.png`, fullPage: false });
    });

    test('2. CTA Navigation: Clicking "Liên hệ tư vấn thiết kế 3D" navigates to Contact page', async ({ page }) => {
        await page.goto('/dich-vu', { waitUntil: 'domcontentloaded' });

        const service1Cta = page.locator('a:has-text("Liên hệ tư vấn thiết kế 3D")').first();
        await expect(service1Cta).toBeVisible();

        const href = await service1Cta.getAttribute('href');
        console.log('service1Cta href:', href);

        await service1Cta.click();
        await page.waitForURL(/lien-he/);

        // Expect to be on Contact page
        expect(page.url()).toContain('/lien-he');
        const contactH1 = page.locator('h1').first();
        await expect(contactH1).toBeVisible();
        const contactH1Text = await contactH1.innerText();
        expect(contactH1Text).toContain('KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN');
    });
});
