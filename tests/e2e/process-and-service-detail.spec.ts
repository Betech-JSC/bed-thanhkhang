import { test, expect } from '@playwright/test';

test.describe('HH Maker Process & Service Detail E2E Visual & Functional Quality Audit', () => {

    test('1. Process Page (/quy-trinh): Renders Hero, Stepper, 5 Steps with 2-Column Coordination, FAQ, and CTA', async ({ page, isMobile }) => {
        const response = await page.goto('/quy-trinh', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header check
        const header = page.locator('header').first();
        await expect(header).toBeVisible();

        // Check Header navigation has "Quy trình"
        if (!isMobile) {
            const processMenuLink = page.locator('header nav a:has-text("Quy trình")').first();
            await expect(processMenuLink).toBeVisible();
            const href = await processMenuLink.getAttribute('href');
            expect(href).toContain('quy-trinh');
        }

        // Section 1: Hero Section (Figma Node 2283:6841)
        const heroBadge = page.locator('text=QUY TRÌNH LÀM VIỆC').first();
        await expect(heroBadge).toBeVisible();

        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();
        const h1Text = await heroH1.innerText();
        expect(h1Text).toContain('RÕ RÀNG TỪNG BƯỚC');
        expect(h1Text).toContain('CHỈN CHU TỪNG SẢN PHẨM');

        // Section 2: Stepper Bar (Figma Node 2283:6851)
        const stepperItems = page.locator('#process-steps div[role="button"]');
        expect(await stepperItems.count()).toBe(5);

        // Section 3: 5 Steps Detail (Figma Node 2283:6882)
        const step01 = page.locator('#step-01');
        await expect(step01).toBeVisible();
        await expect(step01.locator('h2:has-text("Liên hệ")')).toBeVisible();

        const step02 = page.locator('#step-02');
        await expect(step02).toBeVisible();
        await expect(step02.locator('h2:has-text("Báo giá")')).toBeVisible();

        const step03 = page.locator('#step-03');
        await expect(step03).toBeVisible();
        await expect(step03.locator('h2:has-text("Gia công 3D")')).toBeVisible();

        const step04 = page.locator('#step-04');
        await expect(step04).toBeVisible();
        await expect(step04.locator('h2:has-text("Đóng gói")')).toBeVisible();

        const step05 = page.locator('#step-05');
        await expect(step05).toBeVisible();
        await expect(step05.locator('h2:has-text("Gửi hàng")')).toBeVisible();

        // Check 2-Column Coordination for all 5 steps ("BẠN CHUẨN BỊ" & "HH MAKER THỰC HIỆN")
        const clientPrepColumns = page.locator('h3:has-text("BẠN CHUẨN BỊ")');
        expect(await clientPrepColumns.count()).toBe(5);

        const makerActionColumns = page.locator('h3:has-text("HH MAKER THỰC HIỆN")');
        expect(await makerActionColumns.count()).toBe(5);

        // Section 4: FAQ Accordion (Figma Node 2334:8455)
        const faqSection = page.locator('text=CÂU HỎI THƯỜNG GẶP').first();
        await expect(faqSection).toBeVisible();

        const faqSubtitle = page.locator('h2:has-text("Giải đáp trước khi liên hệ")').first();
        await expect(faqSubtitle).toBeVisible();

        const faqButtons = page.locator('button[aria-expanded]');
        expect(await faqButtons.count()).toBe(3);

        // Verify Figma 4-section compliance: No CTA Banner between FAQ and Footer
        const ctaTitle = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")');
        await expect(ctaTitle).toHaveCount(0);

        // Check Footer follows FAQ
        const footer = page.locator('footer').first();
        await expect(footer).toBeVisible();

        // Check zero horizontal scroll
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Screenshots
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-process-page.png`, fullPage: false });
    });

    test('2. Service Detail Page (/dich-vu/thiet-ke-3d): Renders Hero, TOC Sticky, Checklist, Callout Card, Related Services & CTA', async ({ page, isMobile }) => {
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header check
        const header = page.locator('header').first();
        await expect(header).toBeVisible();

        // Breadcrumb check
        const breadcrumbNav = page.locator('nav[aria-label="Breadcrumb"]').first();
        await expect(breadcrumbNav).toBeVisible();

        // Section 1: Hero Section (Figma Node 2367:2156)
        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();
        const heroTitleText = await heroH1.innerText();
        expect(heroTitleText).toContain('Thiết kế 3D');

        // Hero illustration card with 2 columns
        const colStart = page.locator('text=BẮT ĐẦU TỪ').first();
        await expect(colStart).toBeVisible();
        const colClarify = page.locator('text=CÙNG LÀM RÕ').first();
        await expect(colClarify).toBeVisible();

        // Section 2: Table of Contents (TOC - Node 2367:2178)
        if (!isMobile) {
            const tocHeading = page.locator('text=TRONG BÀI VIẾT NÀY').first();
            await expect(tocHeading).toBeVisible();

            const tocItems = page.locator('aside nav button');
            expect(await tocItems.count()).toBe(4);
        } else {
            const mobileTocButtons = page.locator('.overflow-x-auto button');
            expect(await mobileTocButtons.count()).toBe(4);
        }

        // Section 3: Technical Content (Node 2367:2196)
        // 3.1 Section 01: Khi nào cần thiết kế 3D?
        const sec1 = page.locator('#section-01');
        await expect(sec1).toBeVisible();
        await expect(sec1.locator('h2:has-text("Khi nào bạn cần thiết kế 3D?")')).toBeVisible();

        // 3.2 Section 02: Từ ý tưởng tới mô hình (4 steps)
        const sec2 = page.locator('#section-02');
        await expect(sec2).toBeVisible();
        await expect(sec2.locator('h2:has-text("Từ ý tưởng tới mô hình 3D")')).toBeVisible();
        const stepBadges = sec2.locator('.w-10.h-10');
        expect(await stepBadges.count()).toBe(4);

        // 3.3 Section 03: Checklist Thông Số Kỹ Thuật (Node 2367:2232)
        const sec3 = page.locator('#section-03');
        await expect(sec3).toBeVisible();
        await expect(sec3.locator('h2:has-text("Bạn cần chuẩn bị gì?")')).toBeVisible();
        const checklistCard = sec3.locator('.bg-\\[\\#171B30\\]');
        await expect(checklistCard).toBeVisible();
        const checkmarks = checklistCard.locator('text=✓');
        expect(await checkmarks.count()).toBeGreaterThanOrEqual(4);

        // 3.4 Section 04: Cùng hoàn thiện + Callout Card viền xanh (Node 2367:2256)
        const sec4 = page.locator('#section-04');
        await expect(sec4).toBeVisible();
        await expect(sec4.locator('h2:has-text("Cùng hoàn thiện trước khi gia công")')).toBeVisible();
        const calloutCard = sec4.locator('.border-l-2.border-\\[\\#2970FF\\], .border-l-4.border-\\[\\#2970FF\\]');
        await expect(calloutCard).toBeVisible();
        const calloutText = await calloutCard.innerText();
        expect(calloutText).toContain('Một mô hình đẹp trên màn hình vẫn cần được xem xét');

        // Section 4: Related Services (Figma Node 2367:2270)
        const relatedHeading = page.locator('h2:has-text("Dịch vụ liên quan")').first();
        await expect(relatedHeading).toBeVisible();
        const relatedCards = page.locator('.grid a.group');
        expect(await relatedCards.count()).toBeGreaterThanOrEqual(2);

        // Section 5: Banner CTA "Lời mời sáng tạo" (Figma Node 2352:11794)
        const ctaSection = page.locator('h2:has-text("Tạo nên phiên bản mang dấu ấn riêng")').first();
        await expect(ctaSection).toBeVisible();

        // Check zero horizontal scroll
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // Screenshots
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-service-detail.png`, fullPage: false });
    });

    test('3. Cross-page Linking: Header navigation links to Process and Services correctly', async ({ page, isMobile }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });

        if (isMobile) {
            // Open mobile drawer
            const hamburgerBtn = page.locator('button[aria-label="Mở danh mục điều hướng"]').first();
            await expect(hamburgerBtn).toBeVisible();
            await hamburgerBtn.click();

            // Click "Quy trình" in drawer
            const processLink = page.locator('aside nav a:has-text("Quy trình")').first();
            await expect(processLink).toBeVisible();
            await processLink.click();
            await page.waitForURL(/quy-trinh/);
            expect(page.url()).toContain('/quy-trinh');
        } else {
            // Desktop nav link click
            const processLink = page.locator('header nav a:has-text("Quy trình")').first();
            await expect(processLink).toBeVisible();
            await processLink.click();
            await page.waitForURL(/quy-trinh/);
            expect(page.url()).toContain('/quy-trinh');
        }

        // Check that page is rendered
        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();
        expect(await heroH1.innerText()).toContain('RÕ RÀNG TỪNG BƯỚC');
    });
});
