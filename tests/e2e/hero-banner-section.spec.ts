import { test, expect } from '@playwright/test';

test.describe('Section 0: Hero Banner (Figma Node 619:16836)', () => {
  test('Desktop & Mobile Verification of Section 0 Hero Banner', async ({ page }, testInfo) => {
    // Navigate to homepage
    await page.goto('/', { waitUntil: 'networkidle' });

    const isDesktop = testInfo.project.name.includes('Desktop');
    const prefix = isDesktop ? 'desktop' : 'mobile';

    // 1. Hero Section element exists
    const heroSection = page.locator('section[aria-roledescription="carousel"]');
    await expect(heroSection).toBeVisible();

    // 2. Aspect Ratio / Viewport
    const viewportWrapper = heroSection.locator('> div.overflow-hidden').first();
    await expect(viewportWrapper).toBeVisible();

    if (isDesktop) {
      await expect(viewportWrapper).toHaveClass(/md:aspect-\[1440\/582\]/);
      const box = await viewportWrapper.boundingBox();
      expect(box).not.toBeNull();
      // On 1440px wide screen, height should be around 582px
      if (box) {
        expect(box.width).toBeGreaterThanOrEqual(1200);
        expect(box.height).toBeGreaterThanOrEqual(500);
      }
    } else {
      await expect(viewportWrapper).toHaveClass(/aspect-\[375\/480\]/);
      const box = await viewportWrapper.boundingBox();
      expect(box).not.toBeNull();
    }

    // 3. Eyebrow subtitle (Enduring motion)
    const eyebrow = heroSection.locator('text=Enduring motion').first();
    await expect(eyebrow).toBeVisible();

    // 4. Heading (CHUYỂN ĐỘNG BỀN BỈ)
    const heading = heroSection.locator('h1').first();
    await expect(heading).toBeVisible();
    await expect(heading).toHaveText(/CHUYỂN ĐỘNG BỀN BỈ/i);
    await expect(heading).toHaveClass(/font-bold/);
    await expect(heading).toHaveClass(/text-white/);
    await expect(heading).toHaveClass(/uppercase/);

    // 5. Description
    const description = heroSection.locator('p:has-text("Cung cấp giải pháp truyền động")').first();
    await expect(description).toBeVisible();
    await expect(description).toHaveClass(/text-\[#C9E0FF\]/);

    // 6. Primary Button (Xem sản phẩm)
    const primaryBtn = heroSection.locator('a:has-text("Xem sản phẩm")').first();
    await expect(primaryBtn).toBeVisible();
    await expect(primaryBtn).toHaveAttribute('href', '/san-pham');
    await expect(primaryBtn).toHaveClass(/bg-\[#005EA4\]/);
    await expect(primaryBtn).toHaveClass(/text-\[#FAFAFA\]/);
    await expect(primaryBtn).toHaveClass(/rounded-\[4px\]/);

    // 7. Secondary Button (Liên hệ tư vấn)
    const secondaryBtn = heroSection.locator('a:has-text("Liên hệ tư vấn")').first();
    await expect(secondaryBtn).toBeVisible();
    await expect(secondaryBtn).toHaveAttribute('href', '#contact-section');
    await expect(secondaryBtn).toHaveClass(/bg-\[#C9E0FF\]/);
    await expect(secondaryBtn).toHaveClass(/text-\[#003967\]/);
    await expect(secondaryBtn).toHaveClass(/rounded-\[4px\]/);

    // 8. Pagination dots (10px x 10px rounded 2px)
    const tablist = heroSection.locator('[role="tablist"]');
    await expect(tablist).toBeVisible();
    const dots = tablist.locator('[role="tab"]');
    const dotCount = await dots.count();
    expect(dotCount).toBeGreaterThanOrEqual(1);

    const firstDot = dots.first();
    await expect(firstDot).toHaveClass(/w-\[10px\]/);
    await expect(firstDot).toHaveClass(/h-\[10px\]/);
    await expect(firstDot).toHaveClass(/rounded-\[2px\]/);
    await expect(firstDot).toHaveClass(/bg-\[#FAFAFA\]/);

    // 9. Arrow controls (40px x 40px rounded 4px)
    const prevBtn = heroSection.locator('button[aria-label="Slide trước"]');
    const nextBtn = heroSection.locator('button[aria-label="Slide tiếp theo"]');
    await expect(prevBtn).toBeVisible();
    await expect(nextBtn).toBeVisible();
    await expect(prevBtn).toHaveClass(/w-10/);
    await expect(prevBtn).toHaveClass(/h-10/);
    await expect(prevBtn).toHaveClass(/rounded-\[4px\]/);
    await expect(prevBtn).toHaveClass(/bg-\[#394C6B\]\/80/);

    // 10. Test slide transition by clicking next
    if (dotCount > 1) {
      await nextBtn.click();
      await page.waitForTimeout(600);
      const secondDot = dots.nth(1);
      await expect(secondDot).toHaveClass(/bg-\[#FAFAFA\]/);
      // Click back
      await prevBtn.click();
      await page.waitForTimeout(600);
      await expect(firstDot).toHaveClass(/bg-\[#FAFAFA\]/);
    }

    // 11. No horizontal overflow
    const hasHorizontalOverflow = await page.evaluate(() => {
      return document.documentElement.scrollWidth > window.innerWidth;
    });
    expect(hasHorizontalOverflow).toBe(false);

    // 12. Capture Evidence Screenshot
    await heroSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-hero-banner-section.png`,
    });
  });
});
