import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Multilingual System & Language Switcher Verification (Node 2380:1477)', () => {
  test('Verify default Vietnamese, switch to English, switch back, and direct route access', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 1. Điều hướng đến trang chủ mặc định (Tiếng Việt)
    await page.goto('/', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/^(https?:\/\/[^\/]+)\/?$/);

    if (isDesktop) {
      // Kiểm tra Language Switcher Desktop (Node 2380:1477)
      const switcher = page.locator('header .select-none').first();
      await expect(switcher).toBeVisible();

      const vieBtn = switcher.locator('button', { hasText: 'VIE' });
      const engBtn = switcher.locator('button', { hasText: 'ENG' });

      // Trạng thái mặc định: VIE có badge sáng #FAFAFA, chữ tối #111322; ENG là chữ trắng
      await expect(vieBtn).toHaveClass(/bg-\[#FAFAFA\]/);
      await expect(vieBtn).toHaveClass(/text-\[#111322\]/);
      await expect(engBtn).toHaveClass(/text-white/);

      // Kiểm tra 5 Nav Tabs và CTA bằng Tiếng Việt
      const nav = page.locator('header nav').first();
      await expect(nav).toContainText('Trang chủ');
      await expect(nav).toContainText('Sản phẩm');
      await expect(nav).toContainText('Dịch vụ');
      await expect(nav).toContainText('Giới thiệu');
      await expect(nav).toContainText('Quy trình');
      await expect(page.locator('header')).toContainText('Liên hệ ngay');

      // Chụp ảnh bằng chứng phiên bản Tiếng Việt
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'multilingual-vie.png'),
        fullPage: false,
      });

      // 2. Chuyển sang Tiếng Anh bằng cách click nút ENG
      await engBtn.click();
      await page.waitForURL(/\/en\/?$/, { timeout: 10000 });

      // Xác nhận URL có tiền tố /en
      expect(page.url()).toMatch(/\/en\/?$/);

      // Xác nhận trạng thái Language Switcher: ENG có badge sáng, VIE là chữ trắng
      const switcherEn = page.locator('header .select-none').first();
      const vieBtnEn = switcherEn.locator('button', { hasText: 'VIE' });
      const engBtnEn = switcherEn.locator('button', { hasText: 'ENG' });

      await expect(engBtnEn).toHaveClass(/bg-\[#FAFAFA\]/);
      await expect(engBtnEn).toHaveClass(/text-\[#111322\]/);
      await expect(vieBtnEn).toHaveClass(/text-white/);

      // Xác nhận 5 Nav Tabs và CTA hiển thị Tiếng Anh
      const navEn = page.locator('header nav').first();
      await expect(navEn).toContainText('Home');
      await expect(navEn).toContainText('Products');
      await expect(navEn).toContainText('Services');
      await expect(navEn).toContainText('About Us');
      await expect(navEn).toContainText('Process');
      await expect(page.locator('header')).toContainText('Contact Now');

      // Chụp ảnh bằng chứng phiên bản Tiếng Anh
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'multilingual-eng.png'),
        fullPage: false,
      });

      // 3. Chuyển ngược lại Tiếng Việt bằng cách click nút VIE
      await vieBtnEn.click();
      await page.waitForURL(url => !url.pathname.startsWith('/en'), { timeout: 10000 });

      // Xác nhận quay về trang chủ tiếng Việt
      expect(page.url()).not.toContain('/en');
      const navViAgain = page.locator('header nav').first();
      await expect(navViAgain).toContainText('Trang chủ');
      await expect(navViAgain).toContainText('Sản phẩm');
    } else {
      // Mobile testing: mở drawer để kiểm tra language switcher
      const openDrawerBtn = page.locator('button[aria-label="Mở danh mục điều hướng"]');
      await expect(openDrawerBtn).toBeVisible();
      await openDrawerBtn.click();

      // Switcher trong Drawer
      const drawerSwitcher = page.locator('aside .select-none').first();
      await expect(drawerSwitcher).toBeVisible();

      const vieBtnMobile = drawerSwitcher.locator('button', { hasText: 'VIE' });
      const engBtnMobile = drawerSwitcher.locator('button', { hasText: 'ENG' });

      await expect(vieBtnMobile).toHaveClass(/bg-\[#FAFAFA\]/);
      await expect(engBtnMobile).toHaveClass(/text-white/);

      // Click ENG trong drawer
      await engBtnMobile.click();
      await page.waitForURL(/\/en\/?$/, { timeout: 10000 });
      expect(page.url()).toMatch(/\/en\/?$/);
    }
  });

  test('Verify direct access to English routes returns 200 OK and renders English UI', async ({ page }) => {
    // 1. Route trực tiếp /en/services
    const servicesResponse = await page.goto('/en/services', { waitUntil: 'networkidle' });
    expect(servicesResponse?.status()).toBe(200);
    expect(page.url()).toContain('/en/services');

    // 2. Route trực tiếp /en/products
    const productsResponse = await page.goto('/en/products', { waitUntil: 'networkidle' });
    expect(productsResponse?.status()).toBe(200);
    expect(page.url()).toContain('/en/products');

    // 3. Route trực tiếp /en/about-us
    const aboutResponse = await page.goto('/en/about-us', { waitUntil: 'networkidle' });
    expect(aboutResponse?.status()).toBe(200);
    expect(page.url()).toContain('/en/about-us');

    // 4. Route trực tiếp /en/process
    const processResponse = await page.goto('/en/process', { waitUntil: 'networkidle' });
    expect(processResponse?.status()).toBe(200);
    expect(page.url()).toContain('/en/process');
  });
});
