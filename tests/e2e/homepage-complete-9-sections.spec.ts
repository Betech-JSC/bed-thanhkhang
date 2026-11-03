import { test, expect } from '@playwright/test';

test.describe('HH Maker Homepage 100% Figma Node 2261:5474 Verification', () => {
  test('Audit all 9 sections and capture screenshots at 1440px desktop', async ({ page }, testInfo) => {
    // Navigate to homepage
    await page.goto('/', { waitUntil: 'networkidle' });

    // Set viewport to exact 1440px
    await page.setViewportSize({ width: 1440, height: 1080 });

    const isDesktop = testInfo.project.name.includes('Desktop');
    const prefix = isDesktop ? 'desktop-1440px' : 'mobile';

    // ----------------------------------------------------
    // Section 1: Header / Navigation (Node 2352:10414)
    // ----------------------------------------------------
    const header = page.locator('header');
    await expect(header).toBeVisible();
    await expect(header.locator('img[alt="HH Maker Logo"]')).toBeVisible();

    // 5 menu tabs
    await expect(header.locator('nav')).toContainText('Trang chủ');
    await expect(header.locator('nav')).toContainText('Sản phẩm');
    await expect(header.locator('nav')).toContainText('Dịch vụ');
    await expect(header.locator('nav')).toContainText('Giới thiệu');
    await expect(header.locator('nav')).toContainText('Quy trình');

    // Language switcher
    await expect(header).toContainText('VIE');
    await expect(header).toContainText('ENG');

    // CTA button
    await expect(header).toContainText('Liên hệ ngay');

    // Screenshot Section 1: Header
    await header.screenshot({
      path: `tests/e2e/screenshots/${prefix}-01-header.png`,
    });

    // ----------------------------------------------------
    // Section 2: Hero Section (Node 2261:5475)
    // ----------------------------------------------------
    const heroSection = page.locator('section').first();
    await expect(heroSection).toBeVisible();
    await expect(heroSection).toContainText(/Về chúng tôi/i);
    await expect(heroSection.locator('h1')).toContainText('HIỆN THỰC HÓA MỌI Ý TƯỞNG VỚI CÔNG NGHỆ IN 3D');
    await expect(heroSection).toContainText('Không giới hạn không gian sáng tạo, HH Maker thổi hồn vào từng sản phẩm thông qua công nghệ in và gia công 3D hiện đại');
    await expect(heroSection.locator('text=Khám phá ngay')).toBeVisible();
    await expect(heroSection.locator('text=Nhận tư vấn thiết kế')).toBeVisible();
    await expect(heroSection).toContainText('FROM IDEAS TO REALITY');

    // Screenshot Section 2: Hero
    await heroSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-02-hero.png`,
    });

    // ----------------------------------------------------
    // Section 3: Sản phẩm nổi bật Carousel (Node 2261:5503)
    // ----------------------------------------------------
    const featuredCarousel = page.locator('#featured-carousel');
    await expect(featuredCarousel).toBeVisible();
    await expect(featuredCarousel.locator('h2')).toContainText('SẢN PHẨM NỔI BẬT');
    await expect(featuredCarousel.locator('h3').first()).toContainText('Tên sản phẩm');
    await expect(featuredCarousel.locator('p').first()).toContainText('This awesome element has more variants and fully');

    // Screenshot Section 3: Featured Products
    await featuredCarousel.screenshot({
      path: `tests/e2e/screenshots/${prefix}-03-featured-carousel.png`,
    });

    // ----------------------------------------------------
    // Section 4: Tổng quan về dịch vụ (Node 2261:5528)
    // ----------------------------------------------------
    const servicesSection = page.locator('#services-section');
    await expect(servicesSection).toBeVisible();
    await expect(servicesSection.locator('h2')).toContainText('TỔNG QUAN VỀ DỊCH VỤ');
    await expect(servicesSection).toContainText('Gia công 3D');
    await expect(servicesSection).toContainText('Thiết kế 3D');
    await expect(servicesSection).toContainText('Custom');

    // Screenshot Section 4: Services
    await servicesSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-04-services.png`,
    });

    // ----------------------------------------------------
    // Section 5: Sản phẩm theo danh mục (Node 2261:5563)
    // ----------------------------------------------------
    const productsSection = page.locator('#products-section');
    await expect(productsSection).toBeVisible();
    await expect(productsSection).toContainText('Móc khoá');
    await expect(productsSection).toContainText('Đồ dùng/ Đồ trang trí văn phòng');
    await expect(productsSection).toContainText('Gia công theo yêu cầu');

    const viewMoreButtons = productsSection.locator('text=Xem thêm sản phẩm khác');
    await expect(viewMoreButtons).toHaveCount(3);

    // Screenshot Section 5: Products by Category
    await productsSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-05-category-products.png`,
    });

    // ----------------------------------------------------
    // Section 6: Câu chuyện thương hiệu (Node 2261:5696)
    // ----------------------------------------------------
    const brandStorySection = page.locator('#brand-story-section');
    await expect(brandStorySection).toBeVisible();
    await expect(brandStorySection).toContainText('CHIA SẺ TỪ NHÀ SÁNG LẬP HH MAKER');
    await expect(brandStorySection).toContainText('HÀNH TRÌNH ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');
    await expect(brandStorySection).toContainText('10+');
    await expect(brandStorySection).toContainText('500+');
    await expect(brandStorySection).toContainText('95%');

    // Screenshot Section 6: Brand Story
    await brandStorySection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-06-brand-story.png`,
    });

    // ----------------------------------------------------
    // Section 7: Quy trình làm việc (Node 2261:5723)
    // ----------------------------------------------------
    const processSection = page.locator('#process-section');
    await expect(processSection).toBeVisible();
    await expect(processSection).toContainText('05');
    await expect(processSection).toContainText('BƯỚC TRONG QUY TRÌNH LÀM VIỆC CHUYÊN NGHIỆP & MINH BẠCH TẠI HH MAKER');
    await expect(processSection).toContainText('Tiếp nhận & Tư vấn');
    await expect(processSection).toContainText('Phân tích & Báo giá');
    await expect(processSection).toContainText('Gia công In 3D');
    await expect(processSection).toContainText('Đóng gói chuẩn quy cách');
    await expect(processSection).toContainText('Giao hàng tận nơi');

    // Screenshot Section 7: Process
    await processSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-07-process.png`,
    });

    // ----------------------------------------------------
    // Section 8: Form kết nối tư vấn (Node 2261:5749)
    // ----------------------------------------------------
    const contactSection = page.locator('#contact-section');
    await expect(contactSection).toBeVisible();
    await expect(contactSection).toContainText('KẾT NỐI VỚI HH MAKER');
    await expect(contactSection.locator('input[placeholder="Họ và tên"]')).toBeVisible();
    await expect(contactSection.locator('input[placeholder="Email"]')).toBeVisible();
    await expect(contactSection.locator('input[placeholder="Số điện thoại"]')).toBeVisible();
    await expect(contactSection.locator('textarea[placeholder="Chia sẻ ý tưởng của bạn đến với HH Maker"]')).toBeVisible();
    await expect(contactSection.locator('button[type="submit"]')).toContainText('Gửi yêu cầu');

    // Screenshot Section 8: Contact
    await contactSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-08-contact.png`,
    });

    // ----------------------------------------------------
    // Section 9: Footer (Node 2261:5820)
    // ----------------------------------------------------
    const footerSection = page.locator('#footer-section');
    await expect(footerSection).toBeVisible();
    await expect(footerSection).toContainText('CÔNG TY IN ẤN 3D HH MAKER');
    await expect(footerSection).toContainText('Số 65 đường số 9');
    await expect(footerSection).toContainText('Trang chủ');
    await expect(footerSection).toContainText('Dịch vụ');
    await expect(footerSection).toContainText('Liên hệ tư vấn');
    await expect(footerSection).toContainText('© Copyright 2026 HH MAKER');
    await expect(footerSection).toContainText('Chính sách bảo mật');
    await expect(footerSection).toContainText('Điều khoản sử dụng');
    await expect(footerSection).toContainText('Chính sách Cookie');

    // Screenshot Section 9: Footer
    await footerSection.screenshot({
      path: `tests/e2e/screenshots/${prefix}-09-footer.png`,
    });

    // ----------------------------------------------------
    // Full Page Screenshot at 1440px
    // ----------------------------------------------------
    // Hide sticky header momentarily for crisp fullpage shot
    await page.evaluate(() => {
      const h = document.querySelector('header');
      if (h) h.style.position = 'absolute';
    });

    await page.screenshot({
      path: `tests/e2e/screenshots/${prefix}-00-homepage-full-1440px.png`,
      fullPage: true,
    });
  });
});
