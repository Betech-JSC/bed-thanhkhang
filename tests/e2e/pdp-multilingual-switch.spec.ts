import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('PDP Multilingual Switch E2E Verification (VI <-> EN)', () => {
  test('Desktop: Switch VI to EN and back to VI from Product Detail Page without 404', async ({ page }, testInfo) => {
    test.skip(!testInfo.project.name.includes('Desktop'), 'Desktop only');

    // 1. Truy cập trực tiếp trang chi tiết sản phẩm tiếng Việt
    const response = await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });
    expect(response?.status()).toBe(200);

    // Xác thực tiêu đề tiếng Việt
    const h1 = page.locator('h1').first();
    await expect(h1).toBeVisible();
    await expect(h1).toContainText('Mô hình Cáo trang trí');

    // Chụp ảnh bằng chứng 1: PDP Tiếng Việt ban đầu
    const screenshotVi = path.join('storage', 'app', 'public', 'evidence', 'pdp-switch-desktop-vi.png');
    await page.screenshot({ path: screenshotVi, fullPage: false });

    // 2. Click nút ENG trên Header Desktop
    const switcher = page.locator('header .select-none').first();
    await expect(switcher).toBeVisible();
    const engBtn = switcher.locator('button', { hasText: 'ENG' });
    await engBtn.click();

    // Chờ URL chuyển sang route tiếng Anh
    await page.waitForURL(/\/en\/products\/mo-hinh-cao-trang-tri\/?$/, { timeout: 10000 });
    expect(page.url()).toContain('/en/products/mo-hinh-cao-trang-tri');

    // Xác thực không bị 404 và tiêu đề chuyển sang tiếng Anh
    await expect(page.locator('text=404')).not.toBeVisible();
    const h1En = page.locator('h1').first();
    await expect(h1En).toBeVisible();
    await expect(h1En).toContainText('Decorative Fox Model');

    // Chụp ảnh bằng chứng 2: PDP Tiếng Anh sau khi chuyển ngữ thành công
    const screenshotEn = path.join('storage', 'app', 'public', 'evidence', 'pdp-switch-desktop-en.png');
    await page.screenshot({ path: screenshotEn, fullPage: false });

    // 3. Click nút VIE để chuyển ngược lại tiếng Việt
    const switcherEn = page.locator('header .select-none').first();
    const vieBtn = switcherEn.locator('button', { hasText: 'VIE' });
    await vieBtn.click();

    // Chờ URL chuyển ngược về route tiếng Việt
    await page.waitForURL(/\/san-pham\/mo-hinh-cao-trang-tri\/?$/, { timeout: 10000 });
    expect(page.url()).toContain('/san-pham/mo-hinh-cao-trang-tri');
    expect(page.url()).not.toContain('/en/');

    // Xác thực tiêu đề tiếng Việt trở lại bình thường
    const h1ViAgain = page.locator('h1').first();
    await expect(h1ViAgain).toBeVisible();
    await expect(h1ViAgain).toContainText('Mô hình Cáo trang trí');

    // Chụp ảnh bằng chứng 3: PDP Tiếng Việt sau khi quay lại
    const screenshotViBack = path.join('storage', 'app', 'public', 'evidence', 'pdp-switch-desktop-vi-back.png');
    await page.screenshot({ path: screenshotViBack, fullPage: false });
  });

  test('Mobile: Switch VI to EN and back to VI from Mobile Drawer without 404', async ({ page }, testInfo) => {
    test.skip(!testInfo.project.name.includes('Mobile'), 'Mobile only');

    // 1. Truy cập trực tiếp trang chi tiết sản phẩm tiếng Việt trên Mobile
    const response = await page.goto('/san-pham/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });
    expect(response?.status()).toBe(200);

    const h1 = page.locator('h1').first();
    await expect(h1).toBeVisible();
    await expect(h1).toContainText('Mô hình Cáo trang trí');

    // 2. Mở Mobile Drawer bằng nút hamburger
    const hamburgerBtn = page.locator('header button:has(svg path[d*="M4 6h16"])').first();
    await hamburgerBtn.click();

    const drawer = page.locator('aside').first();
    await expect(drawer).toBeVisible();

    // Click nút ENG trong Drawer
    const drawerSwitcher = drawer.locator('.select-none');
    const drawerEngBtn = drawerSwitcher.locator('button', { hasText: 'ENG' });
    await drawerEngBtn.click();

    // Chờ URL chuyển sang tiếng Anh
    await page.waitForURL(/\/en\/products\/mo-hinh-cao-trang-tri\/?$/, { timeout: 10000 });
    expect(page.url()).toContain('/en/products/mo-hinh-cao-trang-tri');

    // Chờ drawer đóng lại sau khi chuyển ngữ
    await expect(drawer).not.toBeVisible({ timeout: 5000 }).catch(() => {});

    // Xác thực không bị 404 và tiêu đề tiếng Anh hiển thị
    await expect(page.locator('text=404')).not.toBeVisible();
    const h1En = page.locator('h1').first();
    await expect(h1En).toBeVisible();
    await expect(h1En).toContainText('Decorative Fox Model');

    // Chụp ảnh bằng chứng 4: Mobile PDP Tiếng Anh
    const screenshotMobileEn = path.join('storage', 'app', 'public', 'evidence', 'pdp-switch-mobile-en.png');
    await page.screenshot({ path: screenshotMobileEn, fullPage: false });

    // 3. Mở Mobile Drawer lần nữa để chuyển ngược về VIE
    const hamburgerBtnEn = page.locator('header button:has(svg path[d*="M4 6h16"])').first();
    await hamburgerBtnEn.click();
    await expect(drawer).toBeVisible();

    const drawerVieBtn = drawer.locator('.select-none button', { hasText: 'VIE' });
    await drawerVieBtn.click();

    // Chờ URL quay về tiếng Việt
    await page.waitForURL(/\/san-pham\/mo-hinh-cao-trang-tri\/?$/, { timeout: 10000 });
    expect(page.url()).toContain('/san-pham/mo-hinh-cao-trang-tri');
    expect(page.url()).not.toContain('/en/');

    await expect(drawer).not.toBeVisible({ timeout: 5000 }).catch(() => {});

    const h1ViAgain = page.locator('h1').first();
    await expect(h1ViAgain).toBeVisible();
    await expect(h1ViAgain).toContainText('Mô hình Cáo trang trí');

    // Chụp ảnh bằng chứng 5: Mobile PDP Tiếng Việt trở lại
    const screenshotMobileVi = path.join('storage', 'app', 'public', 'evidence', 'pdp-switch-mobile-vi.png');
    await page.screenshot({ path: screenshotMobileVi, fullPage: false });
  });
});
