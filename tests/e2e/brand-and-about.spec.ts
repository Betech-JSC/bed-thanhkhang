import { test, expect } from '@playwright/test';

test.describe('HH Maker Brand Identity & About Page Verification', () => {
    test('1. Verify Header Logo and Footer Brand Info', async ({ page, isMobile }) => {
        const prefix = isMobile ? 'mobile' : 'desktop';
        const response = await page.goto('/gioi-thieu', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header Logo
        const headerLogo = page.locator('header img[alt="HH Maker Logo"]').first();
        await expect(headerLogo).toBeVisible();

        // Check Cursor is default (not custom GS-pen.svg)
        const bodyCursor = await page.evaluate(() => {
            return window.getComputedStyle(document.body).cursor;
        });
        expect(bodyCursor).not.toContain('GS-pen');

        // Footer Brand Logo & Info
        const footerLogo = page.locator('footer img[alt="HH Maker Logo"]').first();
        await expect(footerLogo).toBeVisible();

        const footerCompany = page.locator('footer:has-text("CÔNG TY IN ẤN 3D HH MAKER")');
        await expect(footerCompany).toBeVisible();

        const footerAddress = page.locator('footer:has-text("Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh")');
        await expect(footerAddress).toBeVisible();

        const footerHotline = page.locator('footer a[href="tel:0906636425"]');
        await expect(footerHotline).toBeVisible();

        const footerEmail1 = page.locator('footer a[href="mailto:info@hhmaker.vn"]');
        await expect(footerEmail1).toBeVisible();

        const footerEmail2 = page.locator('footer a[href="mailto:lamhonghai123@gmail.com"]');
        await expect(footerEmail2).toBeVisible();

        // Screenshot Header & Footer
        const headerElement = page.locator('header').first();
        await headerElement.screenshot({ path: `tests/e2e/screenshots/${prefix}-header-logo.png` });

        const footerElement = page.locator('footer#footer-section').first();
        await footerElement.screenshot({ path: `tests/e2e/screenshots/${prefix}-footer.png` });
    });

    test('2. Verify About Page 5 Sections (Figma 2283:7098)', async ({ page, isMobile }) => {
        const prefix = isMobile ? 'mobile' : 'desktop';
        const response = await page.goto('/gioi-thieu', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);

        // Section 1: Hero Banner (2283:7100)
        const heroBadge = page.locator('text=Giới thiệu về HH Maker').first();
        await expect(heroBadge).toBeVisible();

        const heroH1 = page.locator('h1:has-text("ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH")').first();
        await expect(heroH1).toBeVisible();

        const rotatingStamp = page.locator('text=FROM IDEAS TO REALITY').first();
        await expect(rotatingStamp).toBeVisible();

        // Section 2: Lý do khởi đầu (2283:7109)
        const reasonBadge = page.locator('text=Vì sao HH Maker ra đời?').first();
        await expect(reasonBadge).toBeVisible();

        const reasonH2 = page.locator('h2:has-text("TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH")').first();
        await expect(reasonH2).toBeVisible();

        const reasonStory = page.locator('text=HH Maker được thành lập từ niềm đam mê').first();
        await expect(reasonStory).toBeVisible();

        // Section 3: Nhà sáng lập (2283:7116)
        const founderName = page.locator('h3:has-text("LÂM HỒNG HẢI")').first();
        await expect(founderName).toBeVisible();

        const founderRole = page.locator('text=Founder HH Maker').first();
        await expect(founderRole).toBeVisible();

        const founderHeading = page.locator('h2:has-text("Mỗi ý tưởng đều xứng đáng có một hình hài")').first();
        await expect(founderHeading).toBeVisible();

        const founderQuote = page.locator('text=Trân trọng từng ý tưởng - Tận tâm với từng thành phẩm.').first();
        await expect(founderQuote).toBeVisible();

        // Section 4: Triết lý chế tác (2283:7129)
        const philosophySub = page.locator('text=Cách HH Maker thực hiện trong từng dự án').first();
        await expect(philosophySub).toBeVisible();

        const philosophyH2 = page.locator('h2:has-text("CÔNG NGHỆ TẠO HÌNH - SỰ TỈ MỈ TẠO DẤU ẤN")').first();
        await expect(philosophyH2).toBeVisible();

        const card1 = page.locator('h3:has-text("Thấu hiểu từng ý tưởng")').first();
        await expect(card1).toBeVisible();

        const card2 = page.locator('h3:has-text("Chăm chút từng chi tiết")').first();
        await expect(card2).toBeVisible();

        const card3 = page.locator('h3:has-text("Giữ trọn dấu ấn riêng")').first();
        await expect(card3).toBeVisible();

        // Section 5: Kết nối dự án (2283:7633)
        const ctaTitle = page.locator('h2:has-text("Ý tưởng của bạn, bước tiếp theo của chúng ta.")').first();
        await expect(ctaTitle).toBeVisible();

        const ctaBtn = page.locator('text=Nhận tư vấn ngay').first();
        await expect(ctaBtn).toBeVisible();

        // Verify Removed Components & Fake Data are NOT present
        await expect(page.locator('text=ĐỘI NGŨ KỸ SƯ & CHUYÊN GIA TẠO TÁC')).toHaveCount(0);
        await expect(page.locator('text=Nguyễn Minh Tuấn')).toHaveCount(0);
        await expect(page.locator('text=Trần Hoàng Nam')).toHaveCount(0);
        await expect(page.locator('text=HÀNH TRÌNH PHÁT TRIỂN')).toHaveCount(0);

        // Take Full Page Screenshot
        await page.screenshot({
            path: `tests/e2e/screenshots/${prefix}-about-page-full.png`,
            fullPage: true,
        });
    });
});
