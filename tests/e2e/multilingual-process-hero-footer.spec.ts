import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('HH Maker Process Hero & Footer Multilingual Verification', () => {

  test('1. Verify English /en/process Hero and Footer are 100% English without Vietnamese leftovers', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    await page.goto('/en/process', { waitUntil: 'networkidle' });
    expect(page.url()).toContain('/en/process');

    // 1.1 Process Hero Verification
    const heroCategory = page.locator('text=WORKFLOW PROCESS').first();
    await expect(heroCategory).toBeVisible();

    const heroH1 = page.locator('h1').first();
    await expect(heroH1).toBeVisible();
    const heroH1Text = await heroH1.innerText();
    expect(heroH1Text).toContain('CLEAR IN EVERY STEP');
    expect(heroH1Text).toContain('METICULOUS IN EVERY PRODUCT');
    expect(heroH1Text).not.toContain('RÕ RÀNG TỪNG BƯỚC');
    expect(heroH1Text).not.toContain('CHỈN CHU TỪNG SẢN PHẨM');

    // 1.2 Footer Verification
    const footer = page.locator('#footer-section');
    await expect(footer).toBeVisible();

    // Company Name
    await expect(footer.locator('text=HH MAKER 3D PRINTING COMPANY')).toBeVisible();
    await expect(footer).not.toContainText('CÔNG TY IN ẤN 3D HH MAKER');

    // Company Address
    await expect(footer).toContainText('No. 65, Street 9, Huong Lo 5 Residential Area, An Lac Ward, Ho Chi Minh City, Vietnam');
    await expect(footer).not.toContainText('Số 65 đường số 9');

    // Footer Copyright
    await expect(footer).toContainText('Copyright');
    await expect(footer).not.toContainText('Bản quyền');

    // Footer Policies
    await expect(footer.locator('text=Privacy Policy')).toBeVisible();
    await expect(footer.locator('text=Terms of Use')).toBeVisible();
    await expect(footer.locator('text=Cookie Policy')).toBeVisible();

    // Capture English Screenshots
    if (isDesktop) {
      const heroContainer = page.locator('section').first();
      await heroContainer.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'process-hero-en-verified.png'),
      });
      await footer.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'footer-en-verified.png'),
      });
    }
  });

  test('2. Verify Vietnamese /quy-trinh Hero and Footer remain 100% Vietnamese', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    await page.goto('/quy-trinh', { waitUntil: 'networkidle' });
    expect(page.url()).toContain('/quy-trinh');

    // 2.1 Process Hero Verification
    const heroCategory = page.locator('text=QUY TRÌNH LÀM VIỆC').first();
    await expect(heroCategory).toBeVisible();

    const heroH1 = page.locator('h1').first();
    await expect(heroH1).toBeVisible();
    const heroH1Text = await heroH1.innerText();
    expect(heroH1Text).toContain('RÕ RÀNG TỪNG BƯỚC');
    expect(heroH1Text).toContain('CHỈN CHU TỪNG SẢN PHẨM');

    // 2.2 Footer Verification
    const footer = page.locator('#footer-section');
    await expect(footer).toBeVisible();

    // Company Name
    await expect(footer.locator('text=CÔNG TY IN ẤN 3D HH MAKER')).toBeVisible();

    // Company Address
    await expect(footer).toContainText('Số 65 đường số 9, khu dân cư Hương Lộ 5, Phường An Lạc, Thành phố Hồ Chí Minh');

    // Footer Copyright
    await expect(footer).toContainText('Bản quyền');

    // Footer Policies
    await expect(footer.locator('text=Chính sách bảo mật')).toBeVisible();
    await expect(footer.locator('text=Điều khoản sử dụng')).toBeVisible();
    await expect(footer.locator('text=Chính sách Cookie')).toBeVisible();

    // Capture Vietnamese Screenshots
    if (isDesktop) {
      const heroContainer = page.locator('section').first();
      await heroContainer.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'process-hero-vi-verified.png'),
      });
      await footer.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'footer-vi-verified.png'),
      });
    }
  });

});
