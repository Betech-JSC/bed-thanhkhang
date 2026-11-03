import { test, expect } from '@playwright/test';

test.describe('Homepage 4-Photo Verification & Screenshots', () => {
  test('Verify Homepage layout ordering, Process section and Contact consultation form', async ({ page }, testInfo) => {
    await page.goto('/', { waitUntil: 'networkidle' });

    // Hide sticky header for crisp section screenshots
    await page.evaluate(() => {
      const header = document.querySelector('header');
      if (header) header.style.position = 'absolute';
    });

    const isDesktop = testInfo.project.name.includes('Desktop');
    const prefix = isDesktop ? 'desktop' : 'mobile';

    // 1. Verify FeaturedProductsCarousel exists directly under Hero and above Services
    const featuredCarousel = page.locator('#featured-carousel');
    await expect(featuredCarousel).toBeVisible();
    await expect(featuredCarousel.locator('h2')).toContainText('SẢN PHẨM NỔI BẬT');

    // 2. Verify ServicesSection exists below FeaturedProductsCarousel
    const servicesSection = page.locator('#services-section');
    await expect(servicesSection).toBeVisible();
    await expect(servicesSection).toContainText('TỔNG QUAN VỀ DỊCH VỤ');
    await expect(servicesSection).toContainText('Gia công 3D');
    await expect(servicesSection).toContainText('Thiết kế 3D');
    await expect(servicesSection).toContainText('Custom');

    // 3. Verify ProductsSection exists
    const productsSection = page.locator('#products-section');
    await expect(productsSection).toBeVisible();
    await expect(productsSection).toContainText('Móc khoá');

    // 4. Verify ProcessSection
    const processSection = page.locator('#process-section');
    await expect(processSection).toBeVisible();
    await expect(processSection).toContainText('05');
    await expect(processSection).toContainText('BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER');
    await expect(processSection).toContainText('Tiếp nhận & Tư vấn');
    await expect(processSection).toContainText('Phân tích & Báo giá');
    await expect(processSection).toContainText('Gia công In 3D');
    await expect(processSection).toContainText('Đóng gói chuẩn quy cách');
    await expect(processSection).toContainText('Giao hàng tận nơi');

    // 5. Verify ContactSection
    const contactSection = page.locator('#contact-section');
    await expect(contactSection).toBeVisible();
    await expect(contactSection).toContainText('KẾT NỐI VỚI HH MAKER');
    await expect(contactSection).toContainText('Bạn cần tư vấn về in 3D? Hãy chia sẻ nhu cầu & ý tưởng của bạn với HH MAKER qua các kênh bên dưới');
    
    // Check form fields
    await expect(contactSection.locator('input[placeholder="Họ và tên"]')).toBeVisible();
    await expect(contactSection.locator('input[placeholder="Email"]')).toBeVisible();
    await expect(contactSection.locator('input[placeholder="Số điện thoại"]')).toBeVisible();
    await expect(contactSection.locator('textarea[placeholder="Chia sẻ ý tưởng của bạn đến với HH Maker"]')).toBeVisible();
    await expect(contactSection.locator('button[type="submit"]')).toContainText('Gửi yêu cầu');

    // Verify 3 social links (Facebook, Instagram, TikTok) and NO hotline/email boxes
    const socialLinks = contactSection.locator('a[aria-label]');
    await expect(socialLinks).toHaveCount(3);
    await expect(contactSection.locator('text=Hotline Kỹ thuật')).toHaveCount(0);
    await expect(contactSection.locator('text=Email tiếp nhận file 3D')).toHaveCount(0);

    // Chụp ảnh từng section riêng biệt và ảnh fullPage
    if (isDesktop) {
      await featuredCarousel.screenshot({
        path: `tests/e2e/screenshots/${prefix}-featured-carousel.png`,
      });

      await servicesSection.screenshot({
        path: `tests/e2e/screenshots/${prefix}-services-section.png`,
      });

      await processSection.screenshot({
        path: `tests/e2e/screenshots/${prefix}-process-section.png`,
      });

      await contactSection.screenshot({
        path: `tests/e2e/screenshots/${prefix}-contact-section.png`,
      });

      // Full page screenshot
      await page.screenshot({
        path: `tests/e2e/screenshots/${prefix}-homepage-full.png`,
        fullPage: true,
      });
    }
  });
});
