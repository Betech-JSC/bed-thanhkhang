import { test, expect } from '@playwright/test';

test.describe('HH Maker Contact Page Quality & Interaction Audit', () => {
    test('1. Contact Page (/lien-he): Renders Hero, 3 Contact Cards, 2-Col Form, FAQ Accordion and Footer without overflow', async ({ page, isMobile }) => {
        const response = await page.goto('/lien-he', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // Header check
        const header = page.locator('header').first();
        await expect(header).toBeVisible();

        // 1. Hero Section Check
        const heroBadge = page.locator('text=KẾT NỐI VỚI HH MAKER').first();
        await expect(heroBadge).toBeVisible();

        const heroH1 = page.locator('h1').first();
        await expect(heroH1).toBeVisible();
        const heroH1Text = await heroH1.innerText();
        expect(heroH1Text).toContain('KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN');

        // 2. Section 2: 3 Glassmorphism Quick Contact Cards
        const zaloCard = page.locator('text=Tư vấn qua Zalo').first();
        await expect(zaloCard).toBeVisible();

        const hotlineCard = page.locator('text=0906636425').first();
        await expect(hotlineCard).toBeVisible();

        const emailCard = page.locator('text=info@hhmaker.vn').first();
        await expect(emailCard).toBeVisible();

        // 3. Section 3: Asymmetric Consultation Form
        const formH2 = page.locator('h2:has-text("CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.")').first();
        await expect(formH2).toBeVisible();

        const formTitle = page.locator('text=Thông tin yêu cầu').first();
        await expect(formTitle).toBeVisible();

        // Check Form inputs
        const nameInput = page.locator('input[placeholder="Nhập họ và tên"]');
        await expect(nameInput).toBeVisible();

        const phoneInput = page.locator('input[placeholder="Nhập số điện thoại"]');
        await expect(phoneInput).toBeVisible();

        const emailInput = page.locator('input[placeholder="Nhập địa chỉ email"]');
        await expect(emailInput).toBeVisible();

        // Check 3 Pill buttons for Service
        const servicePill1 = page.locator('button:has-text("Thiết kế 3D")').first();
        const servicePill2 = page.locator('button:has-text("Gia công 3D")').first();
        const servicePill3 = page.locator('button:has-text("Làm đồ custom")').first();

        await expect(servicePill1).toBeVisible();
        await expect(servicePill2).toBeVisible();
        await expect(servicePill3).toBeVisible();

        // Test clicking pill button
        await servicePill2.click();
        await servicePill3.click();

        // Textarea
        const contentArea = page.locator('textarea[placeholder="Chia sẻ ý tưởng, hình ảnh hoặc bản phác thảo của bạn..."]');
        await expect(contentArea).toBeVisible();

        // Submit button
        const submitBtn = page.locator('button:has-text("Gửi yêu cầu tư vấn")');
        await expect(submitBtn).toBeVisible();

        // 4. Section 4: FAQ Accordion
        const faqHeading = page.locator('text=Giải đáp trước khi liên hệ').first();
        await expect(faqHeading).toBeVisible();

        const faq1 = page.locator('text=Tôi chưa có sẵn file thiết kế 3D, bên mình có hỗ trợ dựng file không?').first();
        await expect(faq1).toBeVisible();

        // Click to toggle FAQ 1
        await faq1.click();
        const faqAnswer1 = page.locator('text=Có, HH Maker hỗ trợ trọn gói từ khâu phác thảo ý tưởng').first();
        await expect(faqAnswer1).toBeVisible();

        // 5. Zero horizontal scroll check
        const hasHorizontalScroll = await page.evaluate(() => {
            return document.documentElement.scrollWidth > window.innerWidth;
        });
        expect(hasHorizontalScroll).toBe(false);

        // 6. Screenshot for report (scroll to top first so fixed header stays at top)
        await page.evaluate(() => window.scrollTo(0, 0));
        await page.waitForTimeout(500);
        const prefix = isMobile ? 'mobile' : 'desktop';
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-contact-page-full.png`, fullPage: true });
        await page.screenshot({ path: `tests/e2e/screenshots/${prefix}-contact-page.png`, fullPage: false });

        // Capture isolated section screenshots for Figma audit
        const heroSection = page.locator('section').first();
        await heroSection.screenshot({ path: `tests/e2e/screenshots/${prefix}-contact-hero-verified.png` });

        const formSection = page.locator('section:has-text("CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.")').first();
        await formSection.screenshot({ path: `tests/e2e/screenshots/${prefix}-contact-form-artwork-verified.png` });
    });

    test('2. Form Validation: Shows error messages when required fields are empty', async ({ page }) => {
        await page.goto('/lien-he', { waitUntil: 'domcontentloaded' });

        const submitBtn = page.locator('button:has-text("Gửi yêu cầu tư vấn")');
        await submitBtn.click();

        // Expect validation errors
        const nameError = page.locator('text=Vui lòng nhập họ và tên');
        await expect(nameError).toBeVisible();

        const phoneError = page.locator('text=Vui lòng nhập số điện thoại');
        await expect(phoneError).toBeVisible();

        const contentError = page.locator('text=Vui lòng mô tả ý tưởng');
        await expect(contentError).toBeVisible();
    });
});
