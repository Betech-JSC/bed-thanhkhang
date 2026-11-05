import { test, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

test.describe('Quy trình Full Page & FAQ Accordion Tests', () => {
    test('Verify all 4 sections (Hero, Stepper, 5 Steps Details, FAQ Accordion) are displayed and FAQ toggles correctly', async ({ page }) => {
        const evidenceDir = path.resolve(process.cwd(), 'storage/app/public/evidence');
        if (!fs.existsSync(evidenceDir)) {
            fs.mkdirSync(evidenceDir, { recursive: true });
        }

        // 1. Truy cập trang /quy-trinh
        await page.goto('/quy-trinh', { waitUntil: 'networkidle' });

        // SECTION 1: Hero Banner
        const heroBadge = page.locator('text=QUY TRÌNH LÀM VIỆC').first();
        await expect(heroBadge).toBeVisible();

        // SECTION 2: Stepper Bar (5 steps)
        const stepper = page.locator('#process-steps');
        await expect(stepper).toBeVisible();
        const stepperItems = stepper.locator('div[role="button"]');
        await expect(stepperItems).toHaveCount(5);

        // SECTION 3: Chi Tiết 5 Bước Gia Công (step-01 to step-05)
        for (let i = 1; i <= 5; i++) {
            const stepId = `#step-0${i}`;
            const stepElem = page.locator(stepId);
            await expect(stepElem).toBeVisible();
        }

        // SECTION 4: FAQ Accordion Section
        const faqSection = page.locator('section').filter({ hasText: 'CÂU HỎI THƯỜNG GẶP' });
        await expect(faqSection).toBeVisible();

        // Scroll FAQ section into view
        await faqSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        // Locate all FAQ buttons (should be 6 FAQs)
        const faqButtons = faqSection.locator('button[aria-expanded]');
        const count = await faqButtons.count();
        expect(count).toBe(6);

        // Xác nhận khi tải trang: tất cả 6 câu hỏi FAQ đều ở trạng thái đóng
        for (let i = 0; i < count; i++) {
            const btn = faqButtons.nth(i);
            await expect(btn).toHaveAttribute('aria-expanded', 'false');
            // Kiểm tra plus indicator hiển thị
            const plusIcon = btn.locator('svg path');
            await expect(plusIcon).toBeVisible();
            // Minus indicator (rect) không hiển thị
            const minusIcon = btn.locator('svg rect');
            await expect(minusIcon).toHaveCount(0);
        }

        // Không có câu trả lời nào đang hiển thị
        const answers = faqSection.locator('.text-sm.sm\\:text-base.text-\\[\\#FCFCFC\\]');
        await expect(answers).toHaveCount(0);

        // Chụp ảnh màn hình: Mặc định tất cả đóng
        await page.screenshot({
            path: path.join(evidenceDir, 'process-faq-1-all-closed-default.png'),
            fullPage: false,
        });

        // 2. Nhấp vào câu hỏi số 1 -> Mở ra thành công
        const firstFaqBtn = faqButtons.nth(0);
        await firstFaqBtn.click();
        await page.waitForTimeout(300);

        // Xác nhận câu 1 mở
        await expect(firstFaqBtn).toHaveAttribute('aria-expanded', 'true');
        const firstMinusIcon = firstFaqBtn.locator('svg rect');
        await expect(firstMinusIcon).toBeVisible();

        // Câu trả lời của câu 1 hiển thị
        await expect(answers).toHaveCount(1);
        await expect(answers.first()).toBeVisible();

        // Các câu hỏi còn lại vẫn đóng
        for (let i = 1; i < count; i++) {
            await expect(faqButtons.nth(i)).toHaveAttribute('aria-expanded', 'false');
        }

        // Chụp ảnh màn hình: Câu 1 mở
        await page.screenshot({
            path: path.join(evidenceDir, 'process-faq-2-first-question-opened.png'),
            fullPage: false,
        });

        // 3. Nhấp lại câu hỏi số 1 -> Đóng lại mượt mà
        await firstFaqBtn.click();
        await page.waitForTimeout(300);

        // Xác nhận câu 1 đã đóng lại
        await expect(firstFaqBtn).toHaveAttribute('aria-expanded', 'false');
        await expect(firstFaqBtn.locator('svg path')).toBeVisible();
        await expect(answers).toHaveCount(0);

        // Chụp ảnh màn hình: Câu 1 đóng lại
        await page.screenshot({
            path: path.join(evidenceDir, 'process-faq-3-first-question-reclosed.png'),
            fullPage: false,
        });

        // 4. Kiểm tra nhấp mở câu 2
        const secondFaqBtn = faqButtons.nth(1);
        await secondFaqBtn.click();
        await page.waitForTimeout(300);
        await expect(secondFaqBtn).toHaveAttribute('aria-expanded', 'true');
        await expect(answers).toHaveCount(1);
        await expect(firstFaqBtn).toHaveAttribute('aria-expanded', 'false');

        // Scroll back to top and take full-page screenshot proving all 4 sections restored
        await page.evaluate(() => window.scrollTo(0, 0));
        await page.waitForTimeout(500);

        await page.screenshot({
            path: path.join(evidenceDir, 'process-page-restored.png'),
            fullPage: true,
        });
    });
});
