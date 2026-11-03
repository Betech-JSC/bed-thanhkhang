import { test, expect } from '@playwright/test';

test.describe('Header 5 Tabs Figma Verification (Node 2352:10414)', () => {
    test('Desktop: Header displays exactly 5 tabs in Figma order and highlights Trang chủ on home', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        // 1. Truy cập trang chủ
        await page.goto('/', { waitUntil: 'domcontentloaded' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        // 2. Logo HH Maker hiển thị
        const logo = header.locator('img[alt*="HH Maker"]');
        await expect(logo).toBeVisible();

        // 3. Kiểm tra thanh Navigation Desktop với đúng 5 tab
        const nav = header.locator('nav');
        await expect(nav).toBeVisible();

        const navLinks = nav.locator('> div > a');
        await expect(navLinks).toHaveCount(5);

        // Kiểm tra đúng 5 tab theo thứ tự Figma
        await expect(navLinks.nth(0)).toContainText('Trang chủ');
        await expect(navLinks.nth(1)).toContainText('Sản phẩm');
        await expect(navLinks.nth(2)).toContainText('Dịch vụ');
        await expect(navLinks.nth(3)).toContainText('Giới thiệu');
        await expect(navLinks.nth(4)).toContainText('Quy trình');

        // Đảm bảo không còn tab thừa như "Tin tức" hay "Liên hệ" trên thanh menu
        await expect(nav.getByText('Tin tức')).toHaveCount(0);
        await expect(nav.getByText('Liên hệ', { exact: true })).toHaveCount(0);

        // 4. Tab "Trang chủ" phải active khi ở "/" với viền đáy xanh #2970FF
        const homeTab = navLinks.nth(0);
        await expect(homeTab).toHaveClass(/text-\[#2970FF\]/);
        await expect(homeTab).toHaveClass(/border-\[#2970FF\]/);

        // Các tab còn lại không được active
        for (let i = 1; i < 5; i++) {
            const otherTab = navLinks.nth(i);
            await expect(otherTab).toHaveClass(/border-transparent/);
        }

        // 5. Nút CTA "Liên hệ ngay" bên phải Header hiển thị
        const contactBtn = header.getByRole('link', { name: 'Liên hệ ngay' });
        await expect(contactBtn).toBeVisible();

        // 6. Chuyển hướng sang tab khác và click lại "Trang chủ"
        const productTab = navLinks.nth(1);
        await productTab.click();
        await page.waitForURL('**/san-pham**');

        // Xác nhận tab Sản phẩm được active
        await expect(productTab).toHaveClass(/border-\[#2970FF\]/);

        // Click lại "Trang chủ"
        await homeTab.click();
        await page.waitForURL((url) => url.pathname === '/' || url.pathname === '');

        // Xác nhận tab Trang chủ lại được active
        await expect(homeTab).toHaveClass(/border-\[#2970FF\]/);

        // 7. Chụp ảnh màn hình Header lưu vào tests/e2e/screenshots/desktop-header-5tabs.png
        await header.screenshot({ path: 'tests/e2e/screenshots/desktop-header-5tabs.png' });
    });

    test('Mobile: Mobile Drawer contains exactly 5 tabs and Lien he ngay button', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        await page.goto('/', { waitUntil: 'domcontentloaded' });

        const header = page.locator('header');
        const hamburgerBtn = header.locator('button[aria-label="Mở danh mục điều hướng"]');
        await expect(hamburgerBtn).toBeVisible();

        await hamburgerBtn.click();

        const drawer = page.locator('aside[aria-label="Menu di động"]');
        await expect(drawer).toBeVisible();

        const drawerNav = drawer.locator('nav a');
        await expect(drawerNav).toHaveCount(5);

        await expect(drawerNav.nth(0)).toContainText('Trang chủ');
        await expect(drawerNav.nth(1)).toContainText('Sản phẩm');
        await expect(drawerNav.nth(2)).toContainText('Dịch vụ');
        await expect(drawerNav.nth(3)).toContainText('Giới thiệu');
        await expect(drawerNav.nth(4)).toContainText('Quy trình');

        // Tab Trang chủ active trên mobile
        await expect(drawerNav.nth(0)).toHaveClass(/bg-primary\/10/);

        // Nút Liên hệ ngay có trong Drawer
        const drawerContact = drawer.getByRole('link', { name: 'Liên hệ ngay' });
        await expect(drawerContact).toBeVisible();
    });
});
