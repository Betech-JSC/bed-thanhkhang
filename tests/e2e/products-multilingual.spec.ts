import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Products Multilingual i18n & Catalog PDP Verification', () => {
  test('Verify Products Listing in Vietnamese and 100% English on Language Switch', async ({ page }, testInfo) => {
    // 1. Truy cập trang danh mục sản phẩm mặc định (Tiếng Việt)
    await page.goto('/san-pham', { waitUntil: 'networkidle' });

    // Đợi layout hoàn tất
    await page.waitForSelector('h1', { state: 'visible' });

    const isDesktop = testInfo.project.name.includes('Desktop');

    // Kiểm tra các phần tử Tiếng Việt
    await expect(page.locator('text=BỘ SƯU TẬP HH MAKER').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('Ý TƯỞNG THÀNH HÌNH');
    await expect(page.locator('text=Nổi bật nhất').first()).toBeVisible();

    if (isDesktop) {
      await expect(page.locator('text=DANH MỤC SẢN PHẨM').first()).toBeVisible();
      await expect(page.locator('text=LỌC THEO NHU CẦU').first()).toBeVisible();
    }

    // Chụp ảnh bằng chứng Listing Tiếng Việt
    await page.screenshot({
      path: path.join('tests', 'e2e', 'screenshots', 'products-listing-vie.png'),
      fullPage: false,
    });

    // 2. Chuyển sang Tiếng Anh
    if (testInfo.project.name.includes('Desktop')) {
      const switcher = page.locator('header .select-none').first();
      const engBtn = switcher.locator('button', { hasText: 'ENG' });
      await engBtn.click();
      await page.waitForURL(/\/en\/products\/?$/, { timeout: 10000 });
    } else {
      await page.goto('/en/products', { waitUntil: 'networkidle' });
    }

    // Xác nhận URL là /en/products
    expect(page.url()).toMatch(/\/en\/products\/?$/);

    // Kiểm tra toàn bộ UI Listing chuyển sang Tiếng Anh 100%
    await expect(page.locator('text=HH MAKER COLLECTION').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('BRINGING IDEAS TO LIFE');
    await expect(page.locator('text=Most Popular').first()).toBeVisible();

    if (isDesktop) {
      await expect(page.locator('text=PRODUCT CATEGORIES').first()).toBeVisible();
      await expect(page.locator('text=FILTER BY PURPOSE').first()).toBeVisible();
      await expect(page.locator('text=Product Customization').first()).toBeVisible();
      await expect(page.locator('text=Contact for Consultation').first()).toBeVisible();
    }

    // Kiểm tra sản phẩm mẫu hiển thị tiếng Anh
    await expect(page.locator('text=Iron Man Mark 85 Model - High Detail 8K Resin').first()).toBeVisible();

    // Chụp ảnh bằng chứng Listing Tiếng Anh
    await page.screenshot({
      path: path.join('tests', 'e2e', 'screenshots', 'products-listing-eng.png'),
      fullPage: false,
    });
  });

  test('Verify Product Detail Page (PDP) 100% English: 3 Tabs, SpecTable, VariantSelector, CTA', async ({ page }) => {
    // Điều hướng trực tiếp đến trang chi tiết sản phẩm Tiếng Anh
    await page.goto('/en/products/mo-hinh-cao-trang-tri', { waitUntil: 'networkidle' });

    // 1. Kiểm tra Tiêu đề & Badges & Breadcrumb
    await expect(page.locator('h1').first()).toContainText('Decorative Fox Model');
    await expect(page.locator('text=3D Printed Model').first()).toBeVisible();
    await expect(page.locator('text=On-Demand Printing').first()).toBeVisible();

    // 2. Kiểm tra VariantSelector
    await expect(page.locator('text=Suggested Material').first()).toBeVisible();
    await expect(page.locator('text=Sample Color').first()).toBeVisible();
    await expect(page.locator('text=Customization Options').first()).toBeVisible();

    // 3. Kiểm tra Nút CTA chính
    await expect(page.locator('button:has-text("Request Product Consultation")').first()).toBeVisible();

    // 4. Kiểm tra 3 Tabs
    await expect(page.locator('button:has-text("Overview")').first()).toBeVisible();
    await expect(page.locator('button:has-text("3D Printing Specs")').first()).toBeVisible();
    await expect(page.locator('button:has-text("Product Customization")').first()).toBeVisible();

    // 5. Kiểm tra Bảng SpecTable (Tab 1)
    await expect(page.locator('text=Reference Print Specifications').first()).toBeVisible();
    await expect(page.locator('text=Print Technology').first()).toBeVisible();
    await expect(page.locator('text=Layer Height').first()).toBeVisible();
    await expect(page.locator('text=Nozzle Diameter').first()).toBeVisible();
    await expect(page.locator('text=Infill Density').first()).toBeVisible();
    await expect(page.locator('text=Support Material').first()).toBeVisible();

    // 6. Chuyển sang Tab 2: 3D Printing Specs
    await page.locator('button:has-text("3D Printing Specs")').first().click();
    await expect(page.locator('text=FDM & Resin 3D Printing Technical Standards').first()).toBeVisible();
    await expect(page.locator('span:has-text("Precision")').first()).toBeVisible();
    await expect(page.locator('span:has-text("Layer Resolution")').first()).toBeVisible();
    await expect(page.locator('h4:has-text("Over 50+ 3D Printers")').first()).toBeVisible();

    // 7. Chuyển sang Tab 3: Product Customization
    await page.locator('button:has-text("Product Customization")').first().click();
    await expect(page.locator('text=On-Demand Custom 3D Printing Process').first()).toBeVisible();
    await expect(page.locator('span:has-text("Step 01")').first()).toBeVisible();
    await expect(page.locator('h4:has-text("Submit Request & 3D File")').first()).toBeVisible();
    await expect(page.locator('button:has-text("Consult Custom Requirements with Engineers")').first()).toBeVisible();

    // Quay lại Tab 1 để chụp ảnh trọn vẹn
    await page.locator('button:has-text("Overview")').first().click();

    // Chụp ảnh bằng chứng PDP Tiếng Anh
    await page.screenshot({
      path: path.join('tests', 'e2e', 'screenshots', 'products-detail-eng.png'),
      fullPage: false,
    });
  });
});
