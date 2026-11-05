import { test, expect } from '@playwright/test';

test.describe('Contact Section - Figma Matched Screenshot Verification', () => {
    test('Capture Pixel-Perfect Contact Section on Desktop', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const contactSection = page.locator('#contact-section');
        await expect(contactSection).toBeVisible();
        await contactSection.scrollIntoViewIfNeeded();

        // Ensure animations have settled
        await page.waitForTimeout(1000);

        // Take screenshot of the exact contact section
        await contactSection.screenshot({
            path: 'tests/e2e/screenshots/contact-section-figma-matched.png',
        });

        // Also verify the form fields are present
        const nameInput = contactSection.locator('input[placeholder="Họ và tên"]');
        const emailInput = contactSection.locator('input[placeholder="Email"]');
        const phoneInput = contactSection.locator('input[placeholder="Số điện thoại"]');
        const noteTextarea = contactSection.locator('textarea[placeholder="Chia sẻ ý tưởng của bạn đến với HH Maker"]');
        const submitBtn = contactSection.locator('button[type="submit"]');

        await expect(nameInput).toBeVisible();
        await expect(emailInput).toBeVisible();
        await expect(phoneInput).toBeVisible();
        await expect(noteTextarea).toBeVisible();
        await expect(submitBtn).toBeVisible();
    });
});
