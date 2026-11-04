import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Home Page & Services System Multilingual i18n Verification', () => {

  test('1. Verify Home Page in Vietnamese and 100% English on Language Switch', async ({ page }, testInfo) => {
    // 1.1 Điều hướng đến trang chủ mặc định (Tiếng Việt)
    await page.goto('/', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/^(https?:\/\/[^\/]+)\/?$/);

    const isDesktop = testInfo.project.name.includes('Desktop');

    // Kiểm tra Navigation tiếng Việt
    const nav = page.locator('header nav').first();
    if (isDesktop) {
      await expect(nav).toContainText('Trang chủ');
      await expect(nav).toContainText('Dịch vụ');
      await expect(nav).toContainText('Giới thiệu');
      await expect(nav).toContainText('Quy trình');
    }

    // Hero & Slogan tiếng Việt
    await expect(page.locator('h1').first()).toBeVisible();
    await expect(page.locator('section').first().getByText('VỀ CHÚNG TÔI')).toBeVisible();

    // Section Services tiếng Việt
    const servicesVi = page.locator('#services-section');
    await expect(servicesVi.getByText('TỔNG QUAN VỀ DỊCH VỤ')).toBeVisible();

    // Section Founder Story & Process tiếng Việt
    await expect(page.locator('text=CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER').first()).toBeVisible();

    // Chụp ảnh bằng chứng Home Tiếng Việt
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'home-vie.png'),
        fullPage: false,
      });
    }

    // 1.2 Chuyển sang Tiếng Anh
    if (isDesktop) {
      const switcher = page.locator('header .select-none').first();
      const engBtn = switcher.locator('button', { hasText: 'ENG' });
      await engBtn.click();
      await page.waitForURL(/\/en\/?$/, { timeout: 10000 });
    } else {
      await page.goto('/en', { waitUntil: 'networkidle' });
    }

    // Xác nhận URL có tiền tố /en
    expect(page.url()).toMatch(/\/en\/?$/);

    // Kiểm tra Navigation tiếng Anh
    if (isDesktop) {
      const navEn = page.locator('header nav').first();
      await expect(navEn).toContainText('Home');
      await expect(navEn).toContainText('Services');
      await expect(navEn).toContainText('About Us');
      await expect(navEn).toContainText('Process');
      await expect(page.locator('header')).toContainText('Contact Now');
    }

    // Kiểm tra Hero tiếng Anh 100%
    await expect(page.locator('h1').first()).toContainText('TURNING EVERY IDEA INTO REALITY WITH 3D PRINTING');
    await expect(page.locator('section').first().getByText('ABOUT US')).toBeVisible();
    await expect(page.locator('text=No limits to creative space').first()).toBeVisible();

    // Kiểm tra Services Overview tiếng Anh
    const servicesEn = page.locator('#services-section');
    await expect(servicesEn).toBeVisible();
    await expect(servicesEn.getByText('SERVICES OVERVIEW')).toBeVisible();
    await expect(servicesEn.getByText('3D Design')).toBeVisible();
    await expect(servicesEn.getByText('3D Machining & Printing')).toBeVisible();
    await expect(servicesEn.getByText('Custom Bespoke Creations')).toBeVisible();

    // Kiểm tra Brand Story & 5-Step Process tiếng Anh
    await expect(page.locator('text=FROM HH MAKER FOUNDER').first()).toBeVisible();
    await expect(page.locator('text=BRINGING CREATIVITY BEYOND THE SCREEN').first()).toBeVisible();
    await expect(page.locator('text=STEPS IN THE PROFESSIONAL & TRANSPARENT WORKFLOW AT HH MAKER').first()).toBeVisible();

    // Kiểm tra Fast Consultation (Contact form) tiếng Anh
    await expect(page.locator('text=CONNECT WITH HH MAKER').first()).toBeVisible();

    // Chụp ảnh bằng chứng Home Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'home-eng.png'),
        fullPage: false,
      });
    }
  });

  test('2. Verify Services Index Page 100% English (/en/services)', async ({ page }, testInfo) => {
    // 2.1 Điều hướng trực tiếp đến trang Dịch vụ tiếng Anh
    await page.goto('/en/services', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/en\/services\/?$/);

    const isDesktop = testInfo.project.name.includes('Desktop');

    // Kiểm tra Header Badge & H1 Headline
    await expect(page.locator('text=HH MAKER 3D PRINTING SERVICES').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('ONE IDEA - MULTIPLE POSSIBILITIES');
    await expect(page.locator('text=You can begin with a 3D file, a drawing, or simply a vision.').first()).toBeVisible();

    // Kiểm tra 3 Service Cards xuất hiện bằng tiếng Anh
    await expect(page.locator('text=3D Design').first()).toBeVisible();
    await expect(page.locator('text=3D Machining & Printing').first()).toBeVisible();
    await expect(page.locator('text=Custom Bespoke Creations').first()).toBeVisible();

    // Kiểm tra bảng Prepare & Co-create
    await expect(page.locator('text=YOU PREPARE').first()).toBeVisible();
    await expect(page.locator('text=WE CO-CREATE').first()).toBeVisible();

    // Chụp ảnh bằng chứng Services Listing Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'services-listing-eng.png'),
        fullPage: false,
      });
    }
  });

  test('3. Verify Service Detail Page 100% English (/en/services/thiet-ke-3d)', async ({ page }, testInfo) => {
    // 3.1 Điều hướng trực tiếp đến trang chi tiết Dịch vụ tiếng Anh
    await page.goto('/en/services/thiet-ke-3d', { waitUntil: 'networkidle' });

    const isDesktop = testInfo.project.name.includes('Desktop');

    // Kiểm tra Breadcrumb trên Desktop
    if (isDesktop) {
      const breadcrumbNav = page.locator('nav[aria-label="Breadcrumb"]').first();
      await expect(breadcrumbNav).toContainText('Home');
      await expect(breadcrumbNav).toContainText('Services');
      await expect(breadcrumbNav).toContainText('3D Design');
    }

    // Kiểm tra Headline & Service Hero
    await expect(page.locator('h1').first()).toContainText('3D Design');
    await expect(page.locator('text=STARTING FROM').first()).toBeVisible();
    await expect(page.locator('text=CLARIFYING TOGETHER').first()).toBeVisible();

    // Kiểm tra Sticky TOC (nếu là Desktop)
    if (isDesktop) {
      await expect(page.locator('text=IN THIS ARTICLE').first()).toBeVisible();
      await expect(page.locator('text=When do you need 3D design?').first()).toBeVisible();
    }

    // Kiểm tra 4 Technical Sections trong nội dung bài viết
    const article = page.locator('article').first();
    await expect(article.getByText('When do you need 3D design?').first()).toBeVisible();
    await expect(article.getByText('From concept to 3D model').first()).toBeVisible();
    await expect(article.getByText('What should you prepare?').first()).toBeVisible();
    await expect(article.getByText('Completing together before machining').first()).toBeVisible();

    // Kiểm tra 4 Steps kỹ thuật
    await expect(article.getByText('Discuss Ideas & Functional Goals').first()).toBeVisible();
    await expect(article.getByText('3D Modeling & Proportion Alignment').first()).toBeVisible();

    // Kiểm tra Related Services & CTA Banner
    await expect(page.getByRole('heading', { name: 'Related Services' })).toBeVisible();
    await expect(page.locator('text=3D Machining & Printing').first()).toBeVisible();
    await expect(page.locator('text=Explore Service').first()).toBeVisible();

    // Chụp ảnh bằng chứng Service Detail Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'service-detail-eng.png'),
        fullPage: false,
      });
    }
  });

});
