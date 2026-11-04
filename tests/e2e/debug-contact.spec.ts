import { test, expect } from '@playwright/test';

test('Debug form submission on /lien-he', async ({ page }) => {
    page.on('console', msg => console.log('PAGE LOG:', msg.type(), msg.text()));
    page.on('response', async resp => {
        if (resp.url().includes('contacts')) {
            console.log('RESPONSE STATUS:', resp.status(), resp.url());
            try {
                const text = await resp.text();
                console.log('RESPONSE BODY:', text.slice(0, 500));
            } catch (e) {}
        }
    });

    await page.goto('/lien-he', { waitUntil: 'networkidle' });

    // Fill form
    await page.locator('input[placeholder="Nhập họ và tên"]').fill('Nguyễn Hoàng Nam');
    await page.locator('input[placeholder="Nhập số điện thoại"]').fill('0906636425');
    await page.locator('input[placeholder="Nhập địa chỉ email"]').fill('nam.nguyen@example.com');
    await page.locator('button:has-text("Gia công 3D")').first().click();
    await page.locator('textarea[placeholder="Chia sẻ ý tưởng, hình ảnh hoặc bản phác thảo của bạn..."]').fill('Tôi cần báo giá gia công in 3D 15 chi tiết vỏ robot bằng công nghệ SLA.');

    // Click submit
    console.log('CLICKING SUBMIT...');
    await page.locator('button:has-text("Gửi yêu cầu tư vấn")').click();

    // Wait 3 seconds
    await page.waitForTimeout(3000);
});
