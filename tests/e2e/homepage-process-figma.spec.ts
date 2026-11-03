import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('HH Maker Homepage Process Section (Figma Node 2261:5723) Verification', () => {
    test.beforeAll(async () => {
        const screenshotDir = path.resolve('tests/e2e/screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('1. Verify Desktop Layout (1440px) matches Figma Node 2261:5723', async ({ page, isMobile }) => {
        test.skip(isMobile, 'Desktop-only test');

        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // 1. Locate Process Section
        const processSection = page.locator('#process-section');
        await expect(processSection).toBeVisible();
        await processSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(600);

        // 2. Verify Section Box Dimensions on Desktop
        const sectionBox = await processSection.boundingBox();
        expect(sectionBox).not.toBeNull();
        expect(sectionBox!.width).toBeGreaterThanOrEqual(1400);
        // Height should be close to 892px as designed
        expect(sectionBox!.height).toBeGreaterThanOrEqual(700);

        // 3. Verify Background Gradient
        const bgStyle = await processSection.evaluate((el) => window.getComputedStyle(el).backgroundImage);
        expect(bgStyle).toContain('gradient');

        // 4. Verify Left Column: 3D Wave Graphic + Giant 05 + Headline
        const leftGraphic = processSection.locator('img[alt*="HH Maker 3D Layer Graphic"]');
        await expect(leftGraphic).toBeVisible();

        const giantNumber = processSection.locator('.process-giant-number');
        await expect(giantNumber).toBeVisible();
        const numberFontSize = await giantNumber.evaluate((el) => window.getComputedStyle(el).fontSize);
        expect(parseInt(numberFontSize, 10)).toBeGreaterThanOrEqual(72);

        const headline = processSection.locator('h2:has-text("BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER")');
        await expect(headline).toBeVisible();

        // 5. Verify Right Column: Overview Paragraph + 5 Steps List
        const desc = processSection.locator('p:has-text("Khách hàng gửi file 3D hoặc chia sẻ ý tưởng")');
        await expect(desc).toBeVisible();

        const expectedSteps = [
            'Tiếp nhận & Tư vấn',
            'Phân tích & Báo giá',
            'Gia công In 3D',
            'Đóng gói chuẩn quy cách',
            'Giao hàng tận nơi'
        ];

        for (const stepTitle of expectedSteps) {
            const stepItem = processSection.locator(`button:has-text("${stepTitle}")`);
            await expect(stepItem).toBeVisible();
            // Arrow icon inside button
            const arrowSvg = stepItem.locator('svg');
            await expect(arrowSvg).toBeVisible();
        }

        // 6. Test Accordion Click Interaction on Step 1
        const step1Button = processSection.locator(`button:has-text("${expectedSteps[0]}")`);
        await step1Button.click();
        await page.waitForTimeout(300);

        const step1Detail = processSection.locator('text=Tiếp nhận file mô hình 3D (STL, OBJ, STEP, 3MF)');
        await expect(step1Detail).toBeVisible();

        // Click again to close
        await step1Button.click();
        await page.waitForTimeout(300);

        // 7. Capture Desktop Screenshot (temporarily hide fixed header to get clean section crop)
        await page.evaluate(() => {
            const h = document.querySelector('header');
            if (h) h.style.visibility = 'hidden';
        });
        const screenshotPath = path.resolve('tests/e2e/screenshots/homepage-process-desktop.png');
        await processSection.screenshot({ path: screenshotPath });
        await page.evaluate(() => {
            const h = document.querySelector('header');
            if (h) h.style.visibility = 'visible';
        });
        expect(fs.existsSync(screenshotPath)).toBe(true);
    });

    test('2. Verify Mobile Responsive Layout (375px)', async ({ page, isMobile }) => {
        test.skip(!isMobile, 'Mobile-only test');

        const response = await page.goto('/', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        const processSection = page.locator('#process-section');
        await expect(processSection).toBeVisible();
        await processSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(600);

        // Verify elements are visible and properly stacked on Mobile
        const giantNumber = processSection.locator('.process-giant-number');
        await expect(giantNumber).toBeVisible();

        const headline = processSection.locator('h2:has-text("BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER")');
        await expect(headline).toBeVisible();

        const stepItem = processSection.locator('button:has-text("Tiếp nhận & Tư vấn")');
        await expect(stepItem).toBeVisible();

        // Capture Mobile Screenshot
        const screenshotPath = path.resolve('tests/e2e/screenshots/homepage-process-mobile.png');
        await processSection.screenshot({ path: screenshotPath });
        expect(fs.existsSync(screenshotPath)).toBe(true);
    });
});
