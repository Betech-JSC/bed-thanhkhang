import { test, expect } from '@playwright/test';

test.describe('HH Maker About Page Figma 2283:7098 Strict Verification', () => {
    test('Verify CTA gradient, Founder ribbon banner, and Clean Section Titles without dash', async ({ page }) => {
        // Set viewport to 1440px desktop
        await page.setViewportSize({ width: 1440, height: 900 });

        // Navigate to About page
        const response = await page.goto('/gioi-thieu', { waitUntil: 'networkidle' });
        expect(response?.status()).toBe(200);

        // 1. VERIFY SECTION 2: "Vì sao HH Maker ra đời?"
        const categoryTitle = page.locator('text="Vì sao HH Maker ra đời?"').first();
        await expect(categoryTitle).toBeVisible();

        // Verify text does NOT contain "-"
        const categoryText = await categoryTitle.innerText();
        expect(categoryText.trim()).toBe('Vì sao HH Maker ra đời?');
        expect(categoryText.startsWith('-')).toBe(false);

        // Screenshot Section 2
        const reasonSection = page.locator('section:has-text("Vì sao HH Maker ra đời?")').first();
        await reasonSection.screenshot({ path: 'tests/e2e/screenshots/about-section2-vi-sao-ra-doi.png' });

        // 2. VERIFY SECTION 3: Founder block & Ribbon Banner
        const founderSection = page.locator('section:has-text("Chia sẻ từ Nhà sáng lập HH Maker")').first();
        await expect(founderSection).toBeVisible();

        // Check Founder name & title
        const founderName = founderSection.locator('h3:has-text("LÂM HỒNG HẢI")').first();
        await expect(founderName).toBeVisible();

        const founderRole = founderSection.locator('p:has-text("Founder HH Maker")').first();
        await expect(founderRole).toBeVisible();

        // Check ribbon banner element wrapping the founder text
        const ribbonBanner = founderSection.locator('div:has(> h3:has-text("LÂM HỒNG HẢI"))').first();
        await expect(ribbonBanner).toBeVisible();

        // Verify ribbon banner has linear-gradient in style
        const ribbonStyle = await ribbonBanner.getAttribute('style');
        expect(ribbonStyle).toMatch(/linear-gradient\(90deg,\s*(#00359E|rgb\(0,\s*53,\s*158\))\s*0%/);

        // Screenshot Founder block
        const founderCard = founderSection.locator('.aspect-\\[4\\/5\\], .aspect-\\[561\\/666\\]').first();
        await founderCard.screenshot({ path: 'tests/e2e/screenshots/about-section3-founder-ribbon.png' });

        // 3. VERIFY SECTION 5: CTA Block with Blue Gradient
        const ctaCard = page.locator('div:has(> div > h2:has-text("Ý tưởng của bạn, bước tiếp theo của chúng ta."))').first();
        await expect(ctaCard).toBeVisible();

        // Verify CTA card has linear-gradient in style
        const ctaStyle = await ctaCard.getAttribute('style');
        expect(ctaStyle).toMatch(/linear-gradient\(90deg,\s*(#2970FF|rgb\(41,\s*112,\s*255\))\s*0%/);

        const ctaTitle = ctaCard.locator('h2:has-text("Ý tưởng của bạn, bước tiếp theo của chúng ta.")').first();
        await expect(ctaTitle).toBeVisible();

        const ctaButton = ctaCard.locator('a:has-text("Nhận tư vấn ngay")').first();
        await expect(ctaButton).toBeVisible();

        // Screenshot CTA Card
        await ctaCard.screenshot({ path: 'tests/e2e/screenshots/about-section5-cta-gradient.png' });

        // Full page screenshot
        await page.screenshot({ path: 'tests/e2e/screenshots/about-page-full-1440.png', fullPage: true });
    });
});
