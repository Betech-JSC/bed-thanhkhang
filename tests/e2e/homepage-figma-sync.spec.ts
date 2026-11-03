import { test, expect } from '@playwright/test';

test.describe('Homepage Figma 100% Sync Verification', () => {
  test('Verify ProductsSection and BrandStorySection layout and capture screenshots', async ({ page }, testInfo) => {
    await page.goto('/', { waitUntil: 'networkidle' });

    // 1. Verify ProductsSection
    const productsSection = page.locator('#products-section');
    await expect(productsSection).toBeVisible();

    // Verify main title in featured carousel (Node 2261:5504)
    await expect(page.locator('#featured-carousel h2')).toContainText('SẢN PHẨM NỔI BẬT');

    // Verify 3 rows
    await expect(productsSection).toContainText('Móc khoá');
    await expect(productsSection).toContainText('Đồ dùng/ Đồ trang trí văn phòng');
    await expect(productsSection).toContainText('Gia công theo yêu cầu');

    // Verify CTAs
    const ctas = productsSection.locator('text=Xem thêm sản phẩm khác');
    await expect(ctas).toHaveCount(3);

    // Verify BEST CHOICE badges
    const bestChoices = productsSection.locator('text=Best choice');
    await expect(bestChoices).toHaveCount(2);

    // Hide sticky header for clean section screenshots
    await page.evaluate(() => {
      const header = document.querySelector('header');
      if (header) header.style.position = 'absolute';
    });

    const isDesktop = testInfo.project.name.includes('Desktop');
    const prefix = isDesktop ? 'desktop' : 'mobile';

    // Capture Products Section Screenshot
    await productsSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-homepage-products-figma.png`,
    });

    // 2. Verify BrandStorySection
    const brandStorySection = page.locator('#brand-story-section');
    await expect(brandStorySection).toBeVisible();

    await expect(brandStorySection).toContainText('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER');
    await expect(brandStorySection).toContainText('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');
    await expect(brandStorySection).toContainText('10+');
    await expect(brandStorySection).toContainText('500+');
    await expect(brandStorySection).toContainText('95%');

    // Capture Brand Story Section Screenshot
    await brandStorySection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-homepage-brand-story-figma.png`,
    });
  });
});
