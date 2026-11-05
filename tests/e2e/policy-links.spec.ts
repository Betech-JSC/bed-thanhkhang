import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Policy Links and Navigation E2E (sync-policy-links-and-routes)', () => {

  test('1. Verify Footer Policy Links and Direct Slug Navigation in Vietnamese', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 1.1 Mở trang chủ tiếng Việt
    await page.goto('/', { waitUntil: 'networkidle' });

    // Cuộn xuống chân trang
    const footer = page.locator('#footer-section');
    await expect(footer).toBeVisible();
    await footer.scrollIntoViewIfNeeded();

    // 1.2 Kiểm tra 3 liên kết chính sách tại Footer
    const privacyLink = footer.locator('a', { hasText: 'Chính sách bảo mật' });
    const termsLink = footer.locator('a', { hasText: 'Điều khoản dịch vụ' });
    const warrantyLink = footer.locator('a', { hasText: 'Chính sách bảo hành & đổi trả' });

    await expect(privacyLink).toBeVisible();
    await expect(termsLink).toBeVisible();
    await expect(warrantyLink).toBeVisible();

    // Xác nhận không còn xuất hiện "Chính sách Cookie" hay "Điều khoản sử dụng"
    await expect(footer.locator('text=Chính sách Cookie')).toHaveCount(0);
    await expect(footer.locator('text=Điều khoản sử dụng')).toHaveCount(0);

    // Kiểm tra href tương ứng
    await expect(privacyLink).toHaveAttribute('href', /.*\/chinh-sach\/chinh-sach-bao-mat$/);
    await expect(termsLink).toHaveAttribute('href', /.*\/chinh-sach\/dieu-khoan-dich-vu$/);
    await expect(warrantyLink).toHaveAttribute('href', /.*\/chinh-sach\/chinh-sach-bao-hanh-doi-tra$/);

    // 1.3 Nhấp vào "Chính sách bảo mật"
    await privacyLink.click();
    await page.waitForURL(/\/chinh-sach\/chinh-sach-bao-mat\/?$/);
    expect(page.url()).toMatch(/\/chinh-sach\/chinh-sach-bao-mat\/?$/);

    // Kiểm tra tiêu đề bài viết
    await expect(page.locator('article h2').first()).toContainText('Chính sách bảo mật');

    if (isDesktop) {
      // Kiểm tra Desktop Sidebar Highlight
      const activeSidebarItem = page.locator('aside nav a.bg-\\[\\#243971\\]');
      await expect(activeSidebarItem).toBeVisible();
      await expect(activeSidebarItem).toContainText('Chính sách bảo mật');

      // Chụp ảnh màn hình kiểm chứng Tiếng Việt
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'policy-vi.png'),
        fullPage: false,
      });
    }

    // 1.4 Kiểm tra nhấp vào "Điều khoản dịch vụ"
    const footerTerms = page.locator('#footer-section a', { hasText: 'Điều khoản dịch vụ' });
    await footerTerms.click();
    await page.waitForURL(/\/chinh-sach\/dieu-khoan-dich-vu\/?$/);
    expect(page.url()).toMatch(/\/chinh-sach\/dieu-khoan-dich-vu\/?$/);
    await expect(page.locator('article h2').first()).toContainText('Điều khoản dịch vụ');

    if (isDesktop) {
      const activeSidebarItem = page.locator('aside nav a.bg-\\[\\#243971\\]');
      await expect(activeSidebarItem).toBeVisible();
      await expect(activeSidebarItem).toContainText('Điều khoản dịch vụ');
    }

    // 1.5 Kiểm tra nhấp vào "Chính sách bảo hành & đổi trả"
    const footerWarranty = page.locator('#footer-section a', { hasText: 'Chính sách bảo hành & đổi trả' });
    await footerWarranty.click();
    await page.waitForURL(/\/chinh-sach\/chinh-sach-bao-hanh-doi-tra\/?$/);
    expect(page.url()).toMatch(/\/chinh-sach\/chinh-sach-bao-hanh-doi-tra\/?$/);
    await expect(page.locator('article h2').first()).toContainText('Chính sách bảo hành & đổi trả');

    if (isDesktop) {
      const activeSidebarItem = page.locator('aside nav a.bg-\\[\\#243971\\]');
      await expect(activeSidebarItem).toBeVisible();
      await expect(activeSidebarItem).toContainText('Chính sách bảo hành & đổi trả');
    }
  });

  test('2. Verify Footer Policy Links and Direct Slug Navigation in English', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 2.1 Mở trang chủ tiếng Anh
    await page.goto('/en', { waitUntil: 'networkidle' });

    // Cuộn xuống chân trang
    const footer = page.locator('#footer-section');
    await expect(footer).toBeVisible();
    await footer.scrollIntoViewIfNeeded();

    // 2.2 Kiểm tra 3 liên kết chính sách tiếng Anh tại Footer
    const privacyLinkEn = footer.locator('a', { hasText: 'Privacy Policy' });
    const termsLinkEn = footer.locator('a', { hasText: 'Terms of Service' });
    const warrantyLinkEn = footer.locator('a', { hasText: 'Warranty & Return Policy' });

    await expect(privacyLinkEn).toBeVisible();
    await expect(termsLinkEn).toBeVisible();
    await expect(warrantyLinkEn).toBeVisible();

    // Kiểm tra href tiếng Anh
    await expect(privacyLinkEn).toHaveAttribute('href', /.*\/en\/policies\/privacy-policy$/);
    await expect(termsLinkEn).toHaveAttribute('href', /.*\/en\/policies\/terms-of-service$/);
    await expect(warrantyLinkEn).toHaveAttribute('href', /.*\/en\/policies\/warranty-return-policy$/);

    // 2.3 Nhấp vào "Privacy Policy"
    await privacyLinkEn.click();
    await page.waitForURL(/\/en\/policies\/privacy-policy\/?$/);
    expect(page.url()).toMatch(/\/en\/policies\/privacy-policy\/?$/);

    // Kiểm tra tiêu đề bài viết
    await expect(page.locator('article h2').first()).toContainText('Privacy Policy');

    if (isDesktop) {
      // Kiểm tra Desktop Sidebar Highlight
      const activeSidebarItem = page.locator('aside nav a.bg-\\[\\#243971\\]');
      await expect(activeSidebarItem).toBeVisible();
      await expect(activeSidebarItem).toContainText('Privacy Policy');

      // Chụp ảnh màn hình kiểm chứng Tiếng Anh
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'policy-en.png'),
        fullPage: false,
      });
    }

    // 2.4 Kiểm tra nhấp vào "Warranty & Return Policy"
    const footerWarrantyEn = page.locator('#footer-section a', { hasText: 'Warranty & Return Policy' });
    await footerWarrantyEn.click();
    await page.waitForURL(/\/en\/policies\/warranty-return-policy\/?$/);
    expect(page.url()).toMatch(/\/en\/policies\/warranty-return-policy\/?$/);
    await expect(page.locator('article h2').first()).toContainText('Warranty & Return Policy');

    if (isDesktop) {
      const activeSidebarItem = page.locator('aside nav a.bg-\\[\\#243971\\]');
      await expect(activeSidebarItem).toBeVisible();
      await expect(activeSidebarItem).toContainText('Warranty & Return Policy');
    }
  });

  test('3. Verify Mobile Category Selector on Mobile Viewport', async ({ page }, testInfo) => {
    const isMobile = testInfo.project.name.includes('Mobile');
    if (!isMobile) return;

    // 3.1 Truy cập trực tiếp trang Chính sách bảo mật
    await page.goto('/chinh-sach/chinh-sach-bao-mat', { waitUntil: 'networkidle' });

    // Kiểm tra nút dropdown hiển thị đúng tên bài viết được chọn
    const mobileBtn = page.locator('#policy-mobile-selector-btn');
    await expect(mobileBtn).toBeVisible();
    await expect(mobileBtn).toContainText('Chính sách bảo mật');

    // Chụp ảnh màn hình kiểm chứng Mobile
    await page.screenshot({
      path: path.join('tests', 'e2e', 'screenshots', 'policy-mobile.png'),
      fullPage: false,
    });

    // 3.2 Mở dropdown và chọn "Điều khoản dịch vụ"
    await mobileBtn.click();
    const dropdownItem = page.locator('.lg\\:hidden a', { hasText: 'Điều khoản dịch vụ' });
    await expect(dropdownItem).toBeVisible();
    await dropdownItem.click();

    // Chờ chuyển trang
    await page.waitForURL(/\/chinh-sach\/dieu-khoan-dich-vu\/?$/);
    expect(page.url()).toMatch(/\/chinh-sach\/dieu-khoan-dich-vu\/?$/);

    // Nút dropdown cập nhật sang "Điều khoản dịch vụ"
    await expect(mobileBtn).toContainText('Điều khoản dịch vụ');
  });

});
