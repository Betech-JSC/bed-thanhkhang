import { test, expect } from '@playwright/test';

test.describe('Figma Typography System Sync Verification', () => {
    test('1. Verify Font Stack prioritizes Inter over Be Vietnam Pro', async ({ page }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });

        const bodyFont = await page.evaluate(() => {
            return window.getComputedStyle(document.body).fontFamily;
        });

        // Inter should be the primary font in font-family
        expect(bodyFont).toContain('Inter');
        expect(bodyFont).not.toContain('Phudu');
        expect(bodyFont).not.toContain('BT-BeauSans');

        // Check H1 font-family
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        const h1Font = await h1.evaluate((el) => {
            return window.getComputedStyle(el).fontFamily;
        });
        expect(h1Font).toContain('Inter');
    });

    test('2. Verify Home Page Hero & Section Headings apply 38px (display-hero) on Desktop', async ({ page, isMobile }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });

        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();
        const heroFontSize = await heroH1.evaluate((el) => window.getComputedStyle(el).fontSize);

        if (!isMobile) {
            expect(heroFontSize).toBe('38px');

            // Services H2 (TỔNG QUAN VỀ DỊCH VỤ)
            const servicesH2 = page.locator('#services-section h2').first();
            await expect(servicesH2).toBeVisible();
            const servicesFontSize = await servicesH2.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(servicesFontSize).toBe('38px');

            // Products H2 (DANH MỤC SẢN PHẨM)
            const productsH2 = page.locator('#products-section h2').first();
            await expect(productsH2).toBeVisible();
            const productsFontSize = await productsH2.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(productsFontSize).toBe('38px');

            // Process H2 (QUY TRÌNH HỢP TÁC 5 BƯỚC)
            const processH2 = page.locator('#process-section h2').first();
            await expect(processH2).toBeVisible();
            const processFontSize = await processH2.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(processFontSize).toBe('38px');

            await page.screenshot({ path: 'tests/e2e/screenshots/desktop-home-typography-hero.png', fullPage: false });
        } else {
            // Mobile viewport scales nicely without overflowing
            const isOverflowing = await page.evaluate(() => document.documentElement.scrollWidth > document.documentElement.clientWidth);
            expect(isOverflowing).toBe(false);
            await page.screenshot({ path: 'tests/e2e/screenshots/mobile-home-typography.png', fullPage: false });
        }
    });

    test('3. Verify Product Page (/san-pham) Hero Title is 38px and CTA is 32px on Desktop', async ({ page, isMobile }) => {
        await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });

        const productH1 = page.locator('h1:has-text("Ý TƯỞNG THÀNH HÌNH")').first();
        await expect(productH1).toBeVisible();
        const productFontFamily = await productH1.evaluate((el) => window.getComputedStyle(el).fontFamily);
        expect(productFontFamily).toContain('Inter');

        if (!isMobile) {
            const productFontSize = await productH1.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(productFontSize).toBe('38px');

            // Product CTA Banner "Tạo nên phiên bản mang dấu ấn riêng" (32px display-banner)
            const ctaHeading = page.locator('h3:has-text("Tạo nên phiên bản mang dấu ấn riêng")').first();
            await expect(ctaHeading).toBeVisible();
            const ctaFontSize = await ctaHeading.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(ctaFontSize).toBe('32px');

            await page.screenshot({ path: 'tests/e2e/screenshots/desktop-product-typography-hero.png', fullPage: false });
        } else {
            await page.screenshot({ path: 'tests/e2e/screenshots/mobile-product-typography.png', fullPage: false });
        }
    });

    test('4. Verify About Page (/gioi-thieu) Hero Title is 38px (display-hero) on Desktop', async ({ page, isMobile }) => {
        await page.goto('/gioi-thieu', { waitUntil: 'domcontentloaded' });

        const aboutH1 = page.locator('h1:has-text("ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH")').first();
        await expect(aboutH1).toBeVisible();
        const aboutFontFamily = await aboutH1.evaluate((el) => window.getComputedStyle(el).fontFamily);
        expect(aboutFontFamily).toContain('Inter');

        if (!isMobile) {
            const aboutFontSize = await aboutH1.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(aboutFontSize).toBe('38px');
            await page.screenshot({ path: 'tests/e2e/screenshots/desktop-about-typography-hero.png', fullPage: false });
        } else {
            await page.screenshot({ path: 'tests/e2e/screenshots/mobile-about-typography.png', fullPage: false });
        }
    });

    test('5. Verify Process Page (/quy-trinh) Step Numbers are 42px and Titles are 28px on Desktop', async ({ page, isMobile }) => {
        await page.goto('/quy-trinh', { waitUntil: 'domcontentloaded' });

        const processHeroH1 = page.locator('h1:has-text("RÕ RÀNG TỪNG BƯỚC")').first();
        await expect(processHeroH1).toBeVisible();

        if (!isMobile) {
            const heroFontSize = await processHeroH1.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(heroFontSize).toBe('38px');

            // Step number "01" (display-step 42px)
            const stepNum = page.locator('#step-01 span:has-text("01")').first();
            await expect(stepNum).toBeVisible();
            const stepNumFontSize = await stepNum.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(stepNumFontSize).toBe('42px');

            // Step title "Liên hệ" (display-section 28px)
            const stepTitle = page.locator('#step-01 h2:has-text("Liên hệ")').first();
            await expect(stepTitle).toBeVisible();
            const stepTitleFontSize = await stepTitle.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(stepTitleFontSize).toBe('28px');

            // Process CTA Banner (display-banner 32px)
            const ctaBanner = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")').first();
            await expect(ctaBanner).toBeVisible();
            const ctaBannerFontSize = await ctaBanner.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(ctaBannerFontSize).toBe('32px');

            await page.screenshot({ path: 'tests/e2e/screenshots/desktop-process-typography.png', fullPage: false });
        } else {
            await page.screenshot({ path: 'tests/e2e/screenshots/mobile-process-typography.png', fullPage: false });
        }
    });

    test('6. Verify Contact Page (/lien-he) Hero Title is 38px and Section is 28px on Desktop', async ({ page, isMobile }) => {
        await page.goto('/lien-he', { waitUntil: 'domcontentloaded' });

        const contactH1 = page.locator('h1:has-text("KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN")').first();
        await expect(contactH1).toBeVisible();

        if (!isMobile) {
            const contactH1FontSize = await contactH1.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(contactH1FontSize).toBe('38px');

            // Form Title "Thông tin yêu cầu" (display-section 28px)
            const formTitle = page.locator('h3:has-text("Thông tin yêu cầu")').first();
            await expect(formTitle).toBeVisible();
            const formTitleFontSize = await formTitle.evaluate((el) => window.getComputedStyle(el).fontSize);
            expect(formTitleFontSize).toBe('28px');

            await page.screenshot({ path: 'tests/e2e/screenshots/desktop-contact-typography.png', fullPage: false });
        } else {
            await page.screenshot({ path: 'tests/e2e/screenshots/mobile-contact-typography.png', fullPage: false });
        }
    });
});
