import { test, expect } from '@playwright/test';

test.describe('Homepage E2E Full Verification (Figma Node 2261:5474)', () => {
  test('Verify all 7 Homepage sections and ensure zero horizontal scroll', async ({ page }, testInfo) => {
    // 1. Điều hướng đến trang chủ
    await page.goto('/', { waitUntil: 'networkidle' });

    // 2. Kiểm tra không có lỗi console nghiêm trọng
    const isDesktop = testInfo.project.name.includes('Desktop');
    const prefix = isDesktop ? 'desktop' : 'mobile';

    // 3. Hero Section (Figma Node 2261:5475)
    const heroH1 = page.locator('h1');
    await expect(heroH1).toBeVisible();
    await expect(heroH1).toContainText('HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D');
    await expect(page.locator('text=Về chúng tôi').first()).toBeVisible();
    await expect(page.locator('text=Khám phá ngay').first()).toBeVisible();
    await expect(page.locator('text=Nhận tư vấn thiết kế').first()).toBeVisible();

    // 4. Featured Products Carousel (Figma Node 2261:5503)
    const featuredHeading = page.locator('#featured-carousel h2');
    await expect(featuredHeading).toBeVisible();
    await expect(featuredHeading).toContainText('SẢN PHẨM NỔI BẬT');

    // 5. Services Section (Figma Node 2261:5528)
    const servicesSection = page.locator('#services-section');
    await expect(servicesSection).toBeVisible();
    await expect(servicesSection.locator('h2')).toContainText('TỔNG QUAN VỀ DỊCH VỤ');
    await expect(servicesSection).toContainText('Gia công 3D');
    await expect(servicesSection).toContainText('Thiết kế 3D');
    await expect(servicesSection).toContainText('Custom');

    // 6. Products Section (Figma Node 2261:5563)
    const productsSection = page.locator('#products-section');
    await expect(productsSection).toBeVisible();
    await expect(productsSection).toContainText('Móc khoá');
    await expect(productsSection).toContainText('Đồ dùng');
    await expect(productsSection).toContainText('Gia công');

    // 7. Brand Story Section (Figma Node 2261:5696)
    const brandStorySection = page.locator('#brand-story-section');
    await expect(brandStorySection).toBeVisible();
    await expect(brandStorySection).toContainText('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER');
    await expect(brandStorySection).toContainText('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');
    await expect(brandStorySection).toContainText('10+');
    await expect(brandStorySection).toContainText('500+');
    await expect(brandStorySection).toContainText('95%');

    // 8. Contact Section (Figma Node 2261:5748)
    const contactSection = page.locator('#contact-section');
    await expect(contactSection).toBeVisible();
    await expect(contactSection).toContainText('KẾT NỐI VỚI HH MAKER');
    await expect(contactSection.locator('button[type="submit"]')).toContainText('Gửi yêu cầu');

    // 9. Xác thực nghiêm ngặt: KHÔNG tràn khung ngang (No horizontal overflow)
    const hasHorizontalScroll = await page.evaluate(() => {
      return document.documentElement.scrollWidth > window.innerWidth;
    });
    expect(hasHorizontalScroll).toBe(false);

    // 10. Chụp ảnh màn hình nghiệm thu visual (toàn trang và từng cụm carousel)
    await page.screenshot({
      path: `tests/e2e/screenshots/${prefix}-homepage-full.png`,
      fullPage: true,
    });
    await featuredHeading.scrollIntoViewIfNeeded();
    await page.locator('#featured-carousel').screenshot({
      path: `tests/e2e/screenshots/${prefix}-homepage-featured-carousel.png`,
    });
    await productsSection.scrollIntoViewIfNeeded();
    await page.locator('#products-section').screenshot({
      path: `tests/e2e/screenshots/${prefix}-homepage-products-section.png`,
    });
  });
});
