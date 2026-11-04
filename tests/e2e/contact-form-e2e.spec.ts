import { test, expect } from '@playwright/test';

test.describe('HH Maker - E2E Test Suite: Form Liên Hệ & Báo Giá In 3D', () => {

    test('1. Trang /lien-he: Kiểm tra Client-Side Validation khi để trống hoặc sai định dạng', async ({ page }) => {
        await page.goto('/lien-he', { waitUntil: 'domcontentloaded' });

        const submitBtn = page.locator('button:has-text("Gửi yêu cầu tư vấn")');
        await expect(submitBtn).toBeVisible();

        // 1.1 Submit khi để trống toàn bộ các trường bắt buộc
        await submitBtn.click();

        // Bắt các thông báo lỗi validation
        const nameError = page.locator('text=Vui lòng nhập họ và tên (tối thiểu 2 ký tự).');
        await expect(nameError).toBeVisible();

        const phoneError = page.locator('text=Vui lòng nhập số điện thoại.');
        await expect(phoneError).toBeVisible();

        const contentError = page.locator('text=Vui lòng mô tả ý tưởng hoặc yêu cầu của bạn (tối thiểu 5 ký tự).');
        await expect(contentError).toBeVisible();

        // 1.2 Nhập email sai định dạng (thiếu @ hoặc domain)
        const emailInput = page.locator('input[placeholder="Nhập địa chỉ email"]');
        await emailInput.fill('khachhang-sai-email');
        await submitBtn.click();

        const emailError = page.locator('text=Địa chỉ email không đúng định dạng.');
        await expect(emailError).toBeVisible();

        // 1.3 Nhập số điện thoại sai định dạng (ít hơn 10 chữ số)
        const phoneInput = page.locator('input[placeholder="Nhập số điện thoại"]');
        await phoneInput.fill('0123');
        await submitBtn.click();

        const phoneInvalidError = page.locator('text=Số điện thoại không hợp lệ');
        await expect(phoneInvalidError).toBeVisible();
    });

    test('2. Trang /lien-he: Gửi thành công yêu cầu báo giá in 3D với dữ liệu hợp lệ (Full E2E Flow)', async ({ page }) => {
        await page.goto('/lien-he', { waitUntil: 'domcontentloaded' });

        // 2.1 Điền họ và tên
        const nameInput = page.locator('input[placeholder="Nhập họ và tên"]');
        await nameInput.fill('Nguyễn Hoàng Nam');

        // 2.2 Điền số điện thoại hợp lệ
        const phoneInput = page.locator('input[placeholder="Nhập số điện thoại"]');
        await phoneInput.fill('0906636425');

        // 2.3 Điền email
        const emailInput = page.locator('input[placeholder="Nhập địa chỉ email"]');
        await emailInput.fill('nam.nguyen@example.com');

        // 2.4 Chọn dịch vụ in 3D: "Gia công 3D"
        const servicePill = page.locator('button:has-text("Gia công 3D")').first();
        await expect(servicePill).toBeVisible();
        await servicePill.click();

        // 2.5 Điền nội dung yêu cầu báo giá in 3D
        const contentArea = page.locator('textarea[placeholder="Chia sẻ ý tưởng, hình ảnh hoặc bản phác thảo của bạn..."]');
        await contentArea.fill('Tôi cần báo giá gia công in 3D 15 chi tiết vỏ robot bằng công nghệ SLA độ mịn cao, vật liệu chịu nhiệt.');

        // 2.6 Submit Form
        const submitBtn = page.locator('button:has-text("Gửi yêu cầu tư vấn")');
        await submitBtn.click();

        // 2.7 Kiểm tra Modal thông báo thành công xuất hiện
        const successTitle = page.locator('h3:has-text("Gửi yêu cầu thành công!")');
        await expect(successTitle).toBeVisible({ timeout: 10000 });

        const successDesc = page.locator('text=Hệ thống đã nhận được thông tin yêu cầu của bạn.');
        await expect(successDesc).toBeVisible();

        // 2.8 Chụp ảnh minh chứng E2E Flow thành công
        await page.screenshot({ path: 'tests/e2e/screenshots/e2e-contact-quote-success-modal.png' });

        // 2.9 Đóng modal và kiểm tra form đã được reset
        const closeBtn = page.locator('button:has-text("Đóng thông báo")');
        await expect(closeBtn).toBeVisible();
        await closeBtn.click();

        await expect(successTitle).not.toBeVisible();
        await expect(nameInput).toHaveValue('');
        await expect(phoneInput).toHaveValue('');
        await expect(contentArea).toHaveValue('');
    });

    test('3. Trang chủ (/): Kiểm tra Form liên hệ nhanh ở ContactSection', async ({ page }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });

        // Cuộn xuống ContactSection
        const contactSection = page.locator('#contact-section');
        await expect(contactSection).toBeVisible();
        await contactSection.scrollIntoViewIfNeeded();

        // Điền thông tin form liên hệ nhanh
        const nameInput = contactSection.locator('input[placeholder="Họ và tên"]');
        await nameInput.fill('Trần Bảo Long');

        const emailInput = contactSection.locator('input[placeholder="Email"]');
        await emailInput.fill('baolong@example.com');

        const phoneInput = contactSection.locator('input[placeholder="Số điện thoại"]');
        await phoneInput.fill('0918273645');

        const noteInput = contactSection.locator('textarea[placeholder="Chia sẻ ý tưởng của bạn đến với HH Maker"]');
        await noteInput.fill('Cần tư vấn dịch vụ in 3D tượng custom kích thước 25cm.');

        // Submit form
        const submitBtn = contactSection.locator('button[type="submit"]');
        await submitBtn.click();

        // Kiểm tra thông báo thành công xuất hiện
        const successNotice = contactSection.locator('text=Gửi yêu cầu thành công! HH Maker sẽ liên hệ hỗ trợ bạn.');
        await expect(successNotice).toBeVisible({ timeout: 10000 });

        // Chụp ảnh minh chứng
        await contactSection.screenshot({ path: 'tests/e2e/screenshots/e2e-homepage-contact-success.png' });
    });

});
