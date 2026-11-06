import { test, expect } from '@playwright/test';
import path from 'path';
import fs from 'fs';

test.describe('Brand Story Section /n Formatting Verification', () => {
  test('Verify BrandStorySection formats /n into 2 distinct paragraphs with proper spacing and responsive layout', async ({ page }, testInfo) => {
    // 1. Navigate to homepage
    await page.goto('/', { waitUntil: 'networkidle' });

    // 2. Locate Brand Story Section
    const brandStorySection = page.locator('#brand-story-section');
    await expect(brandStorySection).toBeVisible();

    // 3. Verify Subtitle and Title
    await expect(brandStorySection.locator('text=CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER')).toBeVisible();
    await expect(brandStorySection.locator('text=HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH')).toBeVisible();

    // 4. Verify Column Container Classes (lg:col-span-6 space-y-6 sm:space-y-4)
    const storyColumn = brandStorySection.locator('.lg\\:col-span-6').nth(1);
    await expect(storyColumn).toHaveClass(/lg:col-span-6/);
    await expect(storyColumn).toHaveClass(/space-y-6/);
    await expect(storyColumn).toHaveClass(/sm:space-y-4/);

    // 5. Verify Paragraphs are split into 2 distinct <p> tags
    const paragraphs = brandStorySection.locator('.space-y-4 p');
    await expect(paragraphs).toHaveCount(2);

    const textP1 = await paragraphs.nth(0).innerText();
    const textP2 = await paragraphs.nth(1).innerText();

    // Verify content of Paragraph 1
    expect(textP1.trim()).toBe(
      'Xuất phát từ niềm đam mê với nghệ thuật 3D và công nghệ chế tác hiện đại, tôi thành lập HH Maker với sứ mệnh rút ngắn khoảng cách giữa ý tưởng sơ khai và sản phẩm vật lý.'
    );

    // Verify content of Paragraph 2
    expect(textP2.trim()).toBe(
      'Với mỗi dự án, tôi cùng đội ngũ luôn tỉ mỉ trong từng đường nét thiết kế, lựa chọn vật liệu và kiểm soát quy trình in để mang lại những sản phẩm sắc nét, chuẩn xác và mang đậm dấu ấn cá nhân nhất.'
    );

    // Verify that NO literal "/n" or "\n" appears in the rendered paragraphs
    expect(textP1).not.toContain('/n');
    expect(textP1).not.toContain('\\n');
    expect(textP2).not.toContain('/n');
    expect(textP2).not.toContain('\\n');

    // 6. Verify 3 metrics are intact
    await expect(brandStorySection).toContainText('10+');
    await expect(brandStorySection).toContainText('500+');
    await expect(brandStorySection).toContainText('95%');

    // 7. Capture Screenshot for Evidence
    const isDesktop = testInfo.project.name.includes('Desktop');
    const screenshotDir = path.resolve(__dirname, '../screenshots');
    if (!fs.existsSync(screenshotDir)) {
      fs.mkdirSync(screenshotDir, { recursive: true });
    }
    const screenshotPath = path.join(screenshotDir, `brand-story-${isDesktop ? 'desktop' : 'mobile'}-formatted.png`);
    await brandStorySection.screenshot({ path: screenshotPath });

    if (!isDesktop) {
      const textColPath = path.join(screenshotDir, 'brand-story-mobile-text-column.png');
      await storyColumn.screenshot({ path: textColPath });
      console.log(`Saved mobile text column screenshot to ${textColPath}`);
    }
    console.log(`Saved screenshot to ${screenshotPath}`);
  });
});
