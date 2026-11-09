import { test, expect } from '@playwright/test';
import * as path from 'path';
import * as fs from 'fs';

test.describe('Header Multi-Level Category Flyout Menu & Mobile Accordion', () => {
    test.beforeAll(async () => {
        const screenshotDir = path.join(process.cwd(), 'tests', 'e2e', 'screenshots');
        if (!fs.existsSync(screenshotDir)) {
            fs.mkdirSync(screenshotDir, { recursive: true });
        }
    });

    test('Desktop: Verify 2-level flyout menu for categories with children', async ({ page, isMobile }) => {
        if (isMobile) test.skip();

        await page.setViewportSize({ width: 1440, height: 900 });
        await page.goto('/', { waitUntil: 'networkidle' });

        const header = page.locator('header');
        await expect(header).toBeVisible();

        const productTab = header.locator('nav').getByRole('link', { name: 'Sản phẩm' });
        await expect(productTab).toBeVisible();

        // 1. Hover on tab "Sản phẩm" to show level 1 dropdown
        await productTab.hover();
        await page.waitForTimeout(400);

        const productTabWrapper = productTab.locator('..');
        const level1Dropdown = productTabWrapper.locator('.absolute.top-full');
        await expect(level1Dropdown).toBeVisible();

        // Verify parent categories exist in Level 1 dropdown
        const catMocKhoa = level1Dropdown.getByRole('link', { name: 'Móc khoá & Phụ kiện' });
        const catTrangTri = level1Dropdown.getByRole('link', { name: 'Trang trí & Đồ dùng' });
        const catGiaCong = level1Dropdown.getByRole('link', { name: 'Gia công theo yêu cầu' });
        const viewAllLink = level1Dropdown.getByRole('link', { name: 'Xem tất cả sản phẩm' });

        await expect(catMocKhoa).toBeVisible();
        await expect(catTrangTri).toBeVisible();
        await expect(catGiaCong).toBeVisible();
        await expect(viewAllLink).toBeVisible();

        // 2. Hover on "Móc khoá & Phụ kiện" -> Level 2 flyout appears
        await catMocKhoa.hover();
        await page.waitForTimeout(400);

        const mocKhoaParentWrapper = catMocKhoa.locator('..');
        const mocKhoaFlyout = mocKhoaParentWrapper.locator('.absolute.left-full');
        await expect(mocKhoaFlyout).toBeVisible();

        // Verify child items in Móc khoá flyout
        const subMocKhoaCoKhi = mocKhoaFlyout.getByRole('link', { name: 'Móc khoá cơ khí' });
        const subMocKhoaTheThao = mocKhoaFlyout.getByRole('link', { name: 'Móc khoá thể thao & phụ kiện' });
        await expect(subMocKhoaCoKhi).toBeVisible();
        await expect(subMocKhoaTheThao).toBeVisible();

        // Hover directly onto child item across the bridge to verify seamless hover transition
        await subMocKhoaCoKhi.hover();
        await page.waitForTimeout(200);
        await expect(mocKhoaFlyout).toBeVisible();

        // Screenshot of Móc khoá flyout
        await page.screenshot({
            path: 'tests/e2e/screenshots/header-flyout-moc-khoa.png',
            clip: { x: 300, y: 50, width: 750, height: 450 },
        });

        // 3. Hover on "Trang trí & Đồ dùng" -> Level 2 flyout appears with 3 items
        await catTrangTri.hover();
        await page.waitForTimeout(400);

        const trangTriParentWrapper = catTrangTri.locator('..');
        const trangTriFlyout = trangTriParentWrapper.locator('.absolute.left-full');
        await expect(trangTriFlyout).toBeVisible();

        const subLy = trangTriFlyout.getByRole('link', { name: 'Ly' });
        const subLoHoa = trangTriFlyout.getByRole('link', { name: 'Lọ cắm hoa để bàn' });
        const subDen = trangTriFlyout.getByRole('link', { name: 'Đèn' });

        await expect(subLy).toBeVisible();
        await expect(subLoHoa).toBeVisible();
        await expect(subDen).toBeVisible();

        // Hover directly onto child item across the bridge to verify seamless hover transition
        await subLy.hover();
        await page.waitForTimeout(200);
        await expect(trangTriFlyout).toBeVisible();

        // Screenshot of Trang trí flyout
        await page.screenshot({
            path: 'tests/e2e/screenshots/header-flyout-trang-tri-do-dung.png',
            clip: { x: 300, y: 50, width: 750, height: 450 },
        });

        // 4. Full Header screenshot with flyout open
        await page.screenshot({
            path: 'tests/e2e/screenshots/header-flyout-desktop-full.png',
            clip: { x: 0, y: 0, width: 1440, height: 450 },
        });

        console.log('DESKTOP TEST PASSED: Flyout 2-level dropdown verified and captured.');
    });

    test('Mobile: Verify 2-level accordion in Mobile Drawer', async ({ page, isMobile }) => {
        if (!isMobile) test.skip();

        await page.setViewportSize({ width: 375, height: 667 });
        await page.goto('/', { waitUntil: 'networkidle' });

        // Open mobile drawer
        const openDrawerBtn = page.getByRole('button', { name: 'Mở danh mục điều hướng' });
        await expect(openDrawerBtn).toBeVisible();
        await openDrawerBtn.click();
        await page.waitForTimeout(300);

        const drawer = page.getByRole('dialog', { name: 'Menu di động' });
        await expect(drawer).toBeVisible();

        // Click "Sản phẩm" accordion toggle
        const productAccordionBtn = drawer.getByRole('button', { name: 'Sản phẩm' });
        await expect(productAccordionBtn).toBeVisible();
        await productAccordionBtn.click();
        await page.waitForTimeout(300);

        // Verify parent categories visible in Mobile Drawer
        const catMocKhoa = drawer.getByRole('link', { name: 'Móc khoá & Phụ kiện' });
        const catTrangTri = drawer.getByRole('link', { name: 'Trang trí & Đồ dùng' });
        await expect(catMocKhoa).toBeVisible();
        await expect(catTrangTri).toBeVisible();

        // Expand "Móc khoá & Phụ kiện" sub-accordion
        const mocKhoaRow = catMocKhoa.locator('..');
        const mocKhoaToggle = mocKhoaRow.getByRole('button', { name: 'Mở rộng danh mục con' });
        await expect(mocKhoaToggle).toBeVisible();
        await mocKhoaToggle.click();
        await page.waitForTimeout(300);

        // Verify subcategories of Móc khoá are visible
        const subMocKhoaCoKhi = drawer.getByRole('link', { name: 'Móc khoá cơ khí' });
        const subMocKhoaTheThao = drawer.getByRole('link', { name: 'Móc khoá thể thao & phụ kiện' });
        await expect(subMocKhoaCoKhi).toBeVisible();
        await expect(subMocKhoaTheThao).toBeVisible();

        // Screenshot of Mobile Drawer with Móc khoá sub-accordion expanded
        await drawer.screenshot({
            path: 'tests/e2e/screenshots/header-mobile-accordion-moc-khoa.png',
        });

        // Expand "Trang trí & Đồ dùng" sub-accordion
        const trangTriRow = catTrangTri.locator('..');
        const trangTriToggle = trangTriRow.getByRole('button', { name: 'Mở rộng danh mục con' });
        await expect(trangTriToggle).toBeVisible();
        await trangTriToggle.click();
        await page.waitForTimeout(300);

        const subLy = drawer.getByRole('link', { name: 'Ly' });
        const subLoHoa = drawer.getByRole('link', { name: 'Lọ cắm hoa để bàn' });
        const subDen = drawer.getByRole('link', { name: 'Đèn' });
        await expect(subLy).toBeVisible();
        await expect(subLoHoa).toBeVisible();
        await expect(subDen).toBeVisible();

        // Screenshot of Mobile Drawer with both sub-accordions expanded
        await drawer.screenshot({
            path: 'tests/e2e/screenshots/header-mobile-drawer-full.png',
        });

        console.log('MOBILE TEST PASSED: 2-level accordion verified and captured.');
    });
});
