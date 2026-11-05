import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Product Detail Page - Breadcrumb Category Link & Filter Sync', () => {
  test('Verify category in breadcrumb is an <a> tag and navigates to filtered catalog with active category', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 1. Visit Product Detail Page for "Mô hình Cáo trang trí"
    await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });

    // Ensure breadcrumb is visible
    const breadcrumb = page.locator('nav[aria-label="Breadcrumb"]');
    await expect(breadcrumb).toBeVisible();

    // Check Home link
    const homeLink = breadcrumb.locator('a', { hasText: 'Trang chủ' });
    await expect(homeLink).toBeVisible();

    // Check Products index link
    const productsLink = breadcrumb.locator('a', { hasText: 'Sản phẩm' });
    await expect(productsLink).toBeVisible();

    // Check Category Breadcrumb Link
    const categoryLink = breadcrumb.locator('a', { hasText: 'Trang trí không gian' });
    await expect(categoryLink).toBeVisible();

    const categoryHref = await categoryLink.getAttribute('href');
    console.log('Category Link Href:', categoryHref);
    expect(categoryHref).toContain('/san-pham');
    expect(categoryHref).toContain('category=trang-tri-khong-gian');

    // Take screenshot of Product Detail Page with breadcrumb
    await page.screenshot({
      path: path.join('storage', 'app', 'public', 'evidence', `pdp-breadcrumb-category-link-${testInfo.project.name}.png`),
      fullPage: false,
    });

    // 2. Click on the category breadcrumb link
    await categoryLink.click();

    // Wait for navigation to /san-pham?category=trang-tri-khong-gian
    await page.waitForURL(/category=trang-tri-khong-gian/, { timeout: 10000 });
    expect(page.url()).toContain('category=trang-tri-khong-gian');

    // Wait for the catalog to load
    await page.waitForSelector('h1', { state: 'visible' });

    // Verify filter active category
    if (isDesktop) {
      const activeCategoryButton = page.locator('aside button', { hasText: 'Trang trí không gian' }).first();
      await expect(activeCategoryButton).toBeVisible();
      const activeClass = await activeCategoryButton.getAttribute('class');
      console.log('Active Category Button Class:', activeClass);
      expect(activeClass).toContain('bg-[#404968]');
    } else {
      // Mobile: Open drawer to verify
      const mobileFilterBtn = page.locator('button[title*="bộ lọc"], button[title*="filter"]').first();
      await mobileFilterBtn.click();
      const activeCategoryButton = page.locator('.fixed button', { hasText: 'Trang trí không gian' }).first();
      await expect(activeCategoryButton).toBeVisible();
      const activeClass = await activeCategoryButton.getAttribute('class');
      expect(activeClass).toContain('bg-[#404968]');
    }

    // Verify products are rendered
    const productCards = page.locator('.grid a, .grid .group');
    await expect(productCards.first()).toBeVisible();

    // Take screenshot of filtered catalog
    await page.screenshot({
      path: path.join('storage', 'app', 'public', 'evidence', `products-catalog-filtered-trang-tri-khong-gian-${testInfo.project.name}.png`),
      fullPage: false,
    });
  });

  test('Verify breadcrumb category link for "Gia công theo yêu cầu" (Iron Man Mark 85)', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 1. Visit Product Detail Page for "iron-man-mark-85-resin-8k"
    await page.goto('/san-pham/iron-man-mark-85-resin-8k', { waitUntil: 'networkidle' });

    // Ensure breadcrumb is visible
    const breadcrumb = page.locator('nav[aria-label="Breadcrumb"]');
    await expect(breadcrumb).toBeVisible();

    // Check Category Breadcrumb Link
    const categoryLink = breadcrumb.locator('a', { hasText: 'Gia công theo yêu cầu' });
    await expect(categoryLink).toBeVisible();

    const categoryHref = await categoryLink.getAttribute('href');
    console.log('Category Link Href (Iron Man):', categoryHref);
    expect(categoryHref).toContain('/san-pham');
    expect(categoryHref).toContain('category=gia-cong-theo-yeu-cau');

    // Take screenshot
    await page.screenshot({
      path: path.join('storage', 'app', 'public', 'evidence', `pdp-breadcrumb-custom-manufacturing-${testInfo.project.name}.png`),
      fullPage: false,
    });

    // 2. Click category link
    await categoryLink.click();

    // Wait for navigation
    await page.waitForURL(/category=gia-cong-theo-yeu-cau/, { timeout: 10000 });
    expect(page.url()).toContain('category=gia-cong-theo-yeu-cau');

    // Verify "Gia công theo yêu cầu" is active in filter
    if (isDesktop) {
      const activeCatBtn = page.locator('aside button', { hasText: 'Gia công theo yêu cầu' }).first();
      await expect(activeCatBtn).toBeVisible();
      const activeClass = await activeCatBtn.getAttribute('class');
      expect(activeClass).toContain('bg-[#404968]');
    } else {
      const mobileFilterBtn = page.locator('button[title*="bộ lọc"], button[title*="filter"]').first();
      await mobileFilterBtn.click();
      const activeCatBtn = page.locator('.fixed button', { hasText: 'Gia công theo yêu cầu' }).first();
      await expect(activeCatBtn).toBeVisible();
      const activeClass = await activeCatBtn.getAttribute('class');
      expect(activeClass).toContain('bg-[#404968]');
    }

    // Take screenshot of filtered catalog
    await page.screenshot({
      path: path.join('storage', 'app', 'public', 'evidence', `products-catalog-filtered-gia-cong-theo-yeu-cau-${testInfo.project.name}.png`),
      fullPage: false,
    });
  });
});
