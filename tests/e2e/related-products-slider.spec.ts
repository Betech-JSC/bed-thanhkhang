import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('PDP Related Products Carousel E2E Verification', () => {
  test('Desktop: Verify Related Products slider header, GPU track transform, and navigation', async ({ page }, testInfo) => {
    test.skip(!testInfo.project.name.includes('Desktop'), 'Desktop only');

    await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });

    // 1. Locate the Related Products section
    const relatedHeading = page.locator('h2', { hasText: 'Sản phẩm khác' });
    await expect(relatedHeading).toBeVisible();

    // Verify the header parent has no small navigation buttons next to the title
    const headerParent = relatedHeading.locator('..');
    const headerButtons = headerParent.locator('button');
    await expect(headerButtons).toHaveCount(0);

    // 2. Scroll to the Related Products section
    await relatedHeading.scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);

    // 3. Locate the slider track and cards
    const track = page.locator('div.will-change-transform').last();
    await expect(track).toBeVisible();

    // Verify track initially has transform translateX(0px) or translateX(-0px)
    const initialStyle = await track.getAttribute('style');
    expect(initialStyle).toMatch(/translateX\(-?0px\)/);

    // Verify cards inside track
    const cards = track.locator('a.group\\/card');
    const cardCount = await cards.count();
    expect(cardCount).toBeGreaterThanOrEqual(6);

    // 4. Locate the 2 round navigation buttons
    const prevBtn = page.locator('button[aria-label="Previous Slide"]').last();
    const nextBtn = page.locator('button[aria-label="Next Slide"]').last();
    await expect(prevBtn).toBeVisible();
    await expect(nextBtn).toBeVisible();

    // 5. Test clicking Next Button
    await nextBtn.click();
    await page.waitForTimeout(600);

    const nextStyle = await track.getAttribute('style');
    expect(nextStyle).toContain('translateX(-293px)');

    // 6. Test clicking Prev Button
    await prevBtn.click();
    await page.waitForTimeout(600);

    const backStyle = await track.getAttribute('style');
    expect(backStyle).toMatch(/translateX\(-?0px\)/);

    // 7. Capture screenshot of Related Products Section
    const section = relatedHeading.locator('xpath=ancestor::section[1]');
    const screenshotPath = path.join('storage', 'app', 'public', 'evidence', 'related-products-slider-desktop.png');
    await section.screenshot({ path: screenshotPath });
  });

  test('Mobile: Verify Related Products slider rendering on mobile', async ({ page }, testInfo) => {
    test.skip(!testInfo.project.name.includes('Mobile'), 'Mobile only');

    await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });

    const relatedHeading = page.locator('h2', { hasText: 'Sản phẩm khác' });
    await expect(relatedHeading).toBeVisible();
    await relatedHeading.scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);

    const track = page.locator('div.will-change-transform').last();
    await expect(track).toBeVisible();

    const section = relatedHeading.locator('xpath=ancestor::section[1]');
    const screenshotMobile = path.join('storage', 'app', 'public', 'evidence', 'related-products-slider-mobile.png');
    await section.screenshot({ path: screenshotMobile });
  });
});
