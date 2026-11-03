import { test, expect } from '@playwright/test';

test.describe('Header Strict Figma 100% Verification (Node 2352:10717)', () => {
    test.beforeEach(async ({ page }) => {
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        // Đợi Vue/Inertia mount hoàn tất
        await page.waitForSelector('header', { state: 'visible' });
    });

    test('Desktop: Strict Figma 100% Header Layout, Dimensions, 5 Tabs & Right Actions', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        const header = page.locator('header');
        await expect(header).toBeVisible();

        // 1. THÔNG SỐ KHUNG VÀ NỀN
        // Nền solid #111322, antialiased, không có border-b, không backdrop-blur
        await expect(header).toHaveClass(/bg-\[#111322\]/);
        await expect(header).toHaveClass(/antialiased/);
        await expect(header).not.toHaveClass(/border-b/);
        await expect(header).not.toHaveClass(/backdrop-blur/);

        // Chiều cao Desktop đúng 111px
        const headerBox = await header.boundingBox();
        expect(headerBox).not.toBeNull();
        expect(Math.round(headerBox?.height || 0)).toBe(111);

        // 2. LOGO HH MAKER (Node 2352:10264)
        const desktopLogo = header.locator('img[src*="figma-header-logo.svg"]');
        await expect(desktopLogo).toBeVisible();
        const logoBox = await desktopLogo.boundingBox();
        expect(logoBox).not.toBeNull();
        expect(Math.round(logoBox?.width || 0)).toBe(139);
        expect(Math.round(logoBox?.height || 0)).toBe(47);

        // 3. CỤM NHÓM DESKTOP NAV + ACTIONS (Node 2352:10717) VỚI GAP CHUẨN 64PX Ở 1440PX
        const desktopGroup = header.locator('div.hidden.lg\\:flex.items-center').first();
        await expect(desktopGroup).toBeVisible();
        await expect(desktopGroup).toHaveClass(/xl:gap-\[64px\]/);

        // 4. CỤM MENU 5 TABS (Node 2352:10269)
        const desktopNav = header.locator('nav');
        await expect(desktopNav).toBeVisible();
        const navLinks = desktopNav.locator('> div > a');
        await expect(navLinks).toHaveCount(5);

        // Đúng thứ tự 5 tabs
        await expect(navLinks.nth(0)).toContainText('Trang chủ');
        await expect(navLinks.nth(1)).toContainText('Sản phẩm');
        await expect(navLinks.nth(2)).toContainText('Dịch vụ');
        await expect(navLinks.nth(3)).toContainText('Giới thiệu');
        await expect(navLinks.nth(4)).toContainText('Quy trình');

        // Bỏ px-1 trên các link nav tabs để viền active underline ôm khít text chuẩn Figma
        for (let i = 0; i < 5; i++) {
            await expect(navLinks.nth(i)).not.toHaveClass(/px-1/);
        }

        // Tab Sản phẩm có icon chevron-down 20px (w-5 h-5)
        const productChevron = navLinks.nth(1).locator('svg');
        await expect(productChevron).toBeVisible();
        await expect(productChevron).toHaveClass(/w-5/);
        await expect(productChevron).toHaveClass(/h-5/);

        // Typography: font-semibold (600), text-base (16px), leading-6
        const homeTab = navLinks.nth(0);
        await expect(homeTab).toHaveClass(/text-base/);
        await expect(homeTab).toHaveClass(/font-semibold/);
        await expect(homeTab).toHaveClass(/leading-6/);

        // Trạng thái Active trên trang chủ:
        // Màu chữ #2970FF, viền đáy border-b-2 border-[#2970FF]
        await expect(homeTab).toHaveClass(/text-\[#2970FF\]/);
        await expect(homeTab).toHaveClass(/border-\[#2970FF\]/);
        await expect(homeTab).toHaveClass(/border-b-2/);

        // Các tabs còn lại ở trạng thái Inactive (#FAFAFA, border-transparent)
        for (let i = 1; i < 5; i++) {
            const inactiveTab = navLinks.nth(i);
            await expect(inactiveTab).toHaveClass(/text-\[#FAFAFA\]/);
            await expect(inactiveTab).toHaveClass(/border-transparent/);
        }

        // 5. CỤM HÀNH ĐỘNG BÊN PHẢI (Node 2380:1132): Thứ tự chuẩn Figma: Search -> Lang Switcher -> Nút CTA
        const rightActionsContainer = desktopGroup.locator('> div').last();
        await expect(rightActionsContainer).toBeVisible();

        // Kiểm tra khoảng cách gap giữa Nav và Right Actions đúng 64px ở 1440px
        const navBox = await desktopNav.boundingBox();
        const actionsBox = await rightActionsContainer.boundingBox();
        expect(navBox).not.toBeNull();
        expect(actionsBox).not.toBeNull();
        const gapNavToActions = Math.round(actionsBox!.x - (navBox!.x + navBox!.width));
        expect(gapNavToActions).toBe(64);

        // Kiểm tra khoảng cách từ Logo đến Cụm Nav + Actions (~256px ở 1440px)
        const groupBox = await desktopGroup.boundingBox();
        expect(groupBox).not.toBeNull();
        const gapLogoToGroup = Math.round(groupBox!.x - (logoBox!.x + logoBox!.width));
        expect(gapLogoToGroup).toBeGreaterThanOrEqual(250);
        expect(gapLogoToGroup).toBeLessThanOrEqual(265);

        // 5.1. Icon Tìm kiếm (Node 2352:10286)
        const searchLink = rightActionsContainer.locator('a[title="Tìm kiếm"]');
        await expect(searchLink).toBeVisible();
        const searchIcon = searchLink.locator('img[src*="figma-search-icon.svg"]');
        await expect(searchIcon).toBeVisible();
        const searchBox = await searchLink.boundingBox();
        expect(searchBox).not.toBeNull();

        // 5.2. Language Switcher (Node 2380:1477)
        const langBox = rightActionsContainer.locator('div.border-\\[\\#5D6B98\\]');
        await expect(langBox).toBeVisible();
        await expect(langBox).toHaveClass(/w-\[96px\]/);
        await expect(langBox).toHaveClass(/h-\[34px\]/);
        await expect(langBox).toHaveClass(/rounded-\[8px\]/);
        const langBoundingBox = await langBox.boundingBox();
        expect(langBoundingBox).not.toBeNull();

        // Kiểm tra nút VIE và ENG
        const vieBtn = langBox.locator('button', { hasText: 'VIE' });
        const engBtn = langBox.locator('button', { hasText: 'ENG' });
        await expect(vieBtn).toBeVisible();
        await expect(engBtn).toBeVisible();

        // Mặc định locale vi: VIE là badge trắng rounded-[4px]
        await expect(vieBtn).toHaveClass(/bg-\[#FAFAFA\]/);
        await expect(vieBtn).toHaveClass(/text-\[#111322\]/);
        await expect(vieBtn).toHaveClass(/rounded-\[4px\]/);

        // 5.3. Nút CTA "Liên hệ ngay" (Node 2352:10291) - NẰM Ở NGOÀI CÙNG BÊN PHẢI
        const ctaBtn = rightActionsContainer.getByRole('link', { name: 'Liên hệ ngay' });
        await expect(ctaBtn).toBeVisible();
        await expect(ctaBtn).toHaveClass(/rounded-\[8px\]/);
        const ctaBox = await ctaBtn.boundingBox();
        expect(ctaBox).not.toBeNull();
        expect(Math.round(ctaBox?.width || 0)).toBe(137);
        expect(Math.round(ctaBox?.height || 0)).toBe(40);

        // XÁC NHẬN THỨ TỰ TỪ TRÁI SANG PHẢI 100% THEO THIẾT KẾ FIGMA:
        // Search icon (trái) < Language switcher (giữa) < CTA "Liên hệ ngay" (phải)
        expect(searchBox!.x).toBeLessThan(langBoundingBox!.x);
        expect(langBoundingBox!.x).toBeLessThan(ctaBox!.x);

        // 5.4. STRICT FIGMA: Không có icon Giỏ hàng và nút Đăng nhập trên thanh Header Desktop
        const desktopCart = header.locator('div.hidden.lg\\:flex a[title="Giỏ hàng"]');
        await expect(desktopCart).toHaveCount(0);

        const desktopLogin = header.locator('div.hidden.lg\\:flex a[href*="dang-nhap"]');
        await expect(desktopLogin).toHaveCount(0);

        // 6. CHỤP ẢNH MÀN HÌNH DESKTOP HEADER LƯU VÀO tests/e2e/screenshots/desktop-header-figma-verified.png
        await header.screenshot({ path: 'tests/e2e/screenshots/desktop-header-figma-verified.png' });
        await header.screenshot({ path: 'tests/e2e/screenshots/desktop-header-strict-figma.png' });
        await header.screenshot({ path: 'tests/e2e/screenshots/desktop-header-1440x900.png' });
    });

    test('Desktop 1280x800: Compact Header Layout & Screenshot', async ({ page, isMobile }) => {
        if (isMobile) test.skip();
        await page.setViewportSize({ width: 1280, height: 800 });
        await page.goto('/', { waitUntil: 'domcontentloaded' });
        const header = page.locator('header');
        await expect(header).toBeVisible();
        const headerBox = await header.boundingBox();
        // Ở dải 1024px - 1280px (lg), chiều cao thu gọn thanh thoát 80px
        expect(Math.round(headerBox?.height || 0)).toBe(80);
        await header.screenshot({ path: 'tests/e2e/screenshots/desktop-header-1280x800.png' });
    });

    test('Mobile: Header is 64px, has Logo, Search & Drawer retains Cart, Auth, and 5 Tabs', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        const header = page.locator('header');
        await expect(header).toBeVisible();

        // Chiều cao mobile header là 64px
        const headerBox = await header.boundingBox();
        expect(Math.round(headerBox?.height || 0)).toBe(64);

        // Logo Mobile
        const mobileLogo = header.locator('img[alt*="HH Maker Logo"]');
        await expect(mobileLogo).toBeVisible();

        // Hamburger button
        const hamburgerBtn = header.locator('button[aria-label="Mở danh mục điều hướng"]');
        await expect(hamburgerBtn).toBeVisible();

        // Mở Drawer
        await hamburgerBtn.click();
        const drawer = page.locator('aside[aria-label="Menu di động"]');
        await expect(drawer).toBeVisible();

        // Drawer chứa đủ 5 tabs
        const drawerNav = drawer.locator('nav a');
        await expect(drawerNav).toHaveCount(5);
        await expect(drawerNav.nth(0)).toContainText('Trang chủ');
        await expect(drawerNav.nth(1)).toContainText('Sản phẩm');
        await expect(drawerNav.nth(2)).toContainText('Dịch vụ');
        await expect(drawerNav.nth(3)).toContainText('Giới thiệu');
        await expect(drawerNav.nth(4)).toContainText('Quy trình');

        // Drawer vẫn giữ Giỏ hàng và Đăng nhập cho người dùng mobile
        await expect(drawer.getByText(/Giỏ hàng/i)).toBeVisible();
        await expect(drawer.getByRole('link', { name: /Đăng nhập/i })).toBeVisible();

        // Nút CTA trong Drawer đổi sang "Liên hệ ngay"
        const drawerCta = drawer.getByRole('link', { name: 'Liên hệ ngay' });
        await expect(drawerCta).toBeVisible();
    });
});
