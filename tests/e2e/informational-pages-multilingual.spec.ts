import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Informational Pages Multilingual (Phase 3 i18n Verification)', () => {

  test('1. Verify About Us Page in Vietnamese (/gioi-thieu) and 100% English (/en/about-us)', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 1.1 Kiểm tra trang Tiếng Việt (/gioi-thieu)
    await page.goto('/gioi-thieu', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/gioi-thieu\/?$/);

    await expect(page.locator('h1').first()).toContainText('ĐƯA SÁNG TẠO VƯỢT RA KHỎI MÀN HÌNH');
    await expect(page.locator('text=Vì sao HH Maker ra đời?').first()).toBeVisible();
    await expect(page.locator('text=TỪ ĐAM MÊ ĐẾN MỘT SỨ MỆNH').first()).toBeVisible();
    await expect(page.locator('text=Chia sẻ từ Nhà sáng lập HH Maker').first()).toBeVisible();
    await expect(page.locator('text=Mỗi ý tưởng đều xứng đáng có một hình hài').first()).toBeVisible();
    await expect(page.locator('text=Thấu hiểu từng ý tưởng').first()).toBeVisible();
    await expect(page.locator('text=Chăm chút từng chi tiết').first()).toBeVisible();
    await expect(page.locator('text=Giữ trọn dấu ấn riêng').first()).toBeVisible();

    // 1.2 Kiểm tra trang Tiếng Anh (/en/about-us)
    await page.goto('/en/about-us', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/en\/about-us\/?$/);

    await expect(page.locator('h1').first()).toContainText('TURNING CREATIVITY BEYOND THE SCREEN');
    await expect(page.locator('text=About HH Maker').first()).toBeVisible();
    await expect(page.locator('text=Why HH Maker Was Born').first()).toBeVisible();
    await expect(page.locator('text=FROM PASSION TO A MISSION').first()).toBeVisible();
    await expect(page.locator('text=Message from the Founder of HH Maker').first()).toBeVisible();
    await expect(page.locator('text=Every idea deserves a physical form').first()).toBeVisible();
    await expect(page.locator('text=Understanding Every Idea').first()).toBeVisible();
    await expect(page.locator('text=Meticulous Attention to Detail').first()).toBeVisible();
    await expect(page.locator('text=Preserving Your Unique Identity').first()).toBeVisible();

    // Chụp ảnh màn hình bằng chứng About Us Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'about-eng.png'),
        fullPage: false,
      });
    }
  });

  test('2. Verify Process Page in Vietnamese (/quy-trinh) and 100% English (/en/process)', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 2.1 Kiểm tra trang Tiếng Việt (/quy-trinh)
    await page.goto('/quy-trinh', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/quy-trinh\/?$/);

    const heroH1Vi = page.locator('h1').first();
    await expect(heroH1Vi).toBeVisible();
    await expect(heroH1Vi).toContainText('RÕ RÀNG TỪNG BƯỚC');
    await expect(heroH1Vi).toContainText('CHỈN CHU TỪNG SẢN PHẨM');

    const footerVi = page.locator('#footer-section');
    await expect(footerVi).toBeVisible();
    await expect(footerVi).toContainText('Số 65 đường số 9');

    const stepperVi = page.locator('#process-steps');
    await expect(stepperVi.getByText('Liên hệ').first()).toBeVisible();
    await expect(stepperVi.getByText('Báo giá').first()).toBeVisible();
    await expect(stepperVi.getByText('Gia công 3D').first()).toBeVisible();
    await expect(stepperVi.getByText('Đóng gói').first()).toBeVisible();
    await expect(stepperVi.getByText('Gửi hàng').first()).toBeVisible();
    await expect(page.locator('text=BẠN CHUẨN BỊ').first()).toBeVisible();
    await expect(page.locator('text=HH MAKER THỰC HIỆN').first()).toBeVisible();
    await expect(page.locator('text=CÂU HỎI THƯỜNG GẶP').first()).toBeVisible();

    // 2.2 Kiểm tra trang Tiếng Anh (/en/process)
    await page.goto('/en/process', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/en\/process\/?$/);

    // Hero title & eyebrow check in English
    await expect(page.locator('text=WORKFLOW PROCESS').first()).toBeVisible();
    const heroH1En = page.locator('h1').first();
    await expect(heroH1En).toBeVisible();
    await expect(heroH1En).toContainText('CLEAR IN EVERY STEP');
    await expect(heroH1En).toContainText('METICULOUS IN EVERY PRODUCT');
    await expect(heroH1En).not.toContainText('RÕ RÀNG TỪNG BƯỚC');

    // Footer address check in English
    const footerEn = page.locator('#footer-section');
    await expect(footerEn).toBeVisible();
    await expect(footerEn).toContainText('HH MAKER 3D PRINTING COMPANY');
    await expect(footerEn).toContainText('No. 65, Street 9');
    await expect(footerEn).not.toContainText('Số 65 đường số 9');

    const stepperEn = page.locator('#process-steps');
    await expect(stepperEn.getByText('Contact').first()).toBeVisible();
    await expect(stepperEn.getByText('Quotation').first()).toBeVisible();
    await expect(stepperEn.getByText('3D Manufacturing').first()).toBeVisible();
    await expect(stepperEn.getByText('Packaging').first()).toBeVisible();
    await expect(stepperEn.getByText('Shipping').first()).toBeVisible();

    await expect(page.locator('text=YOU PREPARE').first()).toBeVisible();
    await expect(page.locator('text=HH MAKER EXECUTES').first()).toBeVisible();
    await expect(page.locator('text=FREQUENTLY ASKED QUESTIONS').first()).toBeVisible();
    await expect(page.locator('text=Answers before reaching out').first()).toBeVisible();

    // Chụp ảnh màn hình bằng chứng Process Tiếng Anh & Footer Tiếng Anh
    if (isDesktop) {
      const heroSection = page.locator('section').first();
      await heroSection.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'process-hero-eng.png'),
      });
      await footerEn.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'footer-eng.png'),
      });
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'process-eng.png'),
        fullPage: false,
      });
    }
  });

  test('3. Verify Contact Page in Vietnamese (/lien-he) and 100% English (/en/contact)', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 3.1 Kiểm tra trang Tiếng Việt (/lien-he)
    await page.goto('/lien-he', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/lien-he\/?$/);

    await expect(page.locator('text=KẾT NỐI VỚI HH MAKER').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('KỂ CHÚNG TÔI NGHE Ý TƯỞNG CỦA BẠN');
    await expect(page.locator('text=Tư vấn qua Zalo').first()).toBeVisible();
    await expect(page.locator('text=CÙNG BẮT ĐẦU MỘT SẢN PHẨM MỚI.').first()).toBeVisible();
    await expect(page.locator('text=Thông tin yêu cầu').first()).toBeVisible();
    await expect(page.locator('button[type="submit"]').first()).toContainText('Gửi yêu cầu tư vấn');

    // 3.2 Kiểm tra trang Tiếng Anh (/en/contact)
    await page.goto('/en/contact', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/en\/contact\/?$/);

    await expect(page.locator('text=CONNECT WITH HH MAKER').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('TELL US ABOUT YOUR IDEA');
    await expect(page.locator('text=Consultation via Zalo').first()).toBeVisible();
    await expect(page.locator('text=Exchange ideas, reference photos, and product requirements').first()).toBeVisible();
    await expect(page.locator('text=Inquiry Information').first()).toBeVisible();
    await expect(page.locator('button[type="submit"]').first()).toContainText('Submit Consultation Request');

    // Kiểm tra validation errors bằng Tiếng Anh
    await page.locator('button[type="submit"]').first().click();
    await expect(page.locator('text=Please enter your full name (minimum 2 characters).').first()).toBeVisible();
    await expect(page.locator('text=Please enter your phone number.').first()).toBeVisible();

    // Chụp ảnh màn hình bằng chứng Contact Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'contact-eng.png'),
        fullPage: false,
      });
    }
  });

  test('4. Verify Policies Page in Vietnamese (/chinh-sach) and 100% English (/en/policies)', async ({ page }, testInfo) => {
    const isDesktop = testInfo.project.name.includes('Desktop');

    // 4.1 Kiểm tra trang Tiếng Việt (/chinh-sach)
    await page.goto('/chinh-sach', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/chinh-sach\/?$/);

    await expect(page.locator('text=CHÍNH SÁCH & ĐIỀU KHOẢN').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('Chính sách & Cam kết xưởng in 3D HH Maker');

    if (isDesktop) {
      await expect(page.locator('aside').getByText('Danh mục chính sách').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Điều khoản dịch vụ').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Chính sách bảo mật').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Chính sách bảo hành & đổi trả').first()).toBeVisible();
    }
    await expect(page.locator('article h2').first()).toContainText('Điều khoản dịch vụ');

    // 4.2 Kiểm tra trang Tiếng Anh (/en/policies)
    await page.goto('/en/policies', { waitUntil: 'networkidle' });
    expect(page.url()).toMatch(/\/en\/policies\/?$/);

    await expect(page.locator('text=POLICIES & TERMS').first()).toBeVisible();
    await expect(page.locator('h1').first()).toContainText('HH Maker 3D Printing Policies & Commitments');

    if (isDesktop) {
      await expect(page.locator('aside').getByText('Policy Categories').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Terms of Service').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Privacy Policy').first()).toBeVisible();
      await expect(page.locator('aside').getByText('Warranty & Return Policy').first()).toBeVisible();
    }
    await expect(page.locator('article h2').first()).toContainText('Terms of Service');
    await expect(page.locator('text=Last updated:').first()).toBeVisible();

    // Chụp ảnh màn hình bằng chứng Policies Tiếng Anh
    if (isDesktop) {
      await page.screenshot({
        path: path.join('tests', 'e2e', 'screenshots', 'policy-eng.png'),
        fullPage: false,
      });
    }
  });

});
