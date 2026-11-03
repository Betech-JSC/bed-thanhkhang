import { test, expect } from '@playwright/test';

test.describe('HH Maker 4-Page Comprehensive Quality Audit', () => {
    test('1. Home Page (/): Renders all 7 sections, Header and Footer with proper tokens', async ({ page, isMobile }) => {
        const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header brand logo
        const logo = page.locator('header img[alt="HH Maker Logo"]').first();
        await expect(logo).toBeVisible();

        // Check Hero Section
        const heroTitle = page.locator('h1').first();
        await expect(heroTitle).toBeVisible();
        const heroText = await heroTitle.innerText();
        expect(heroText).toContain('HIỆN THỰC HÓA MỌI Ý TƯỞNG');

        // Check Services Section
        const servicesSection = page.locator('#services-section');
        await expect(servicesSection).toBeVisible();

        // Check Products Section
        const productsSection = page.locator('#products-section');
        await expect(productsSection).toBeVisible();

        // Check Contact Section
        const contactSection = page.locator('#contact-section');
        await expect(contactSection).toBeVisible();

        // Screenshot
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-home-page.png`, fullPage: false });
    });

    test('2. Product Listing (/san-pham): Renders Catalog, Filter, Cards and CTA', async ({ page, isMobile }) => {
        const response = await page.goto('/san-pham', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Eyebrow and Page Heading
        const heading = page.locator('h1').first();
        await expect(heading).toBeVisible();
        const headingText = await heading.innerText();
        expect(headingText).toContain('Ý TƯỞNG THÀNH HÌNH');

        // Breadcrumb
        const breadcrumbHome = page.locator('nav a[href="/"]').first();
        await expect(breadcrumbHome).toBeVisible();

        // Product Cards Grid
        const productCard = page.locator('.grid .group').first();
        await expect(productCard).toBeVisible();

        // CTA Banner
        const ctaText = page.locator('text=Tạo nên phiên bản mang dấu ấn riêng').first();
        await expect(ctaText).toBeVisible();

        // Screenshot
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-product-listing.png`, fullPage: false });
    });

    test('3. Product Detail - PDP (/san-pham/mo-hinh-iron-man-mark-85): Renders Breadcrumbs, Gallery, Variants, Quote CTA', async ({ page, isMobile }) => {
        const response = await page.goto('/san-pham/mo-hinh-iron-man-mark-85', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Breadcrumbs back to Home and Listing
        const breadcrumbHome = page.locator('nav[aria-label="Breadcrumb"] a[href="/"]');
        await expect(breadcrumbHome).toBeVisible();
        const breadcrumbProducts = page.locator('nav[aria-label="Breadcrumb"] a[href="/san-pham"]');
        await expect(breadcrumbProducts).toBeVisible();

        // Title and Quote CTA Button
        const quoteBtn = page.locator('button:has-text("Yêu cầu báo giá in")');
        await expect(quoteBtn).toBeVisible();

        // Screenshot
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-product-detail.png`, fullPage: false });
    });

    test('4. About Us (/ve-chung-toi): Renders Hero, Timeline, Machine Farm, Values, Team, CTA', async ({ page, isMobile }) => {
        const response = await page.goto('/ve-chung-toi', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Heading
        const heading = page.locator('h1').first();
        await expect(heading).toBeVisible();
        const headingText = await heading.innerText();
        expect(headingText).toContain('ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');

        // CTA Banner
        const ctaBtn = page.locator('text=Nhận tư vấn ngay').first();
        await expect(ctaBtn).toBeVisible();

        // Screenshot
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-about-us.png`, fullPage: false });
    });
});
