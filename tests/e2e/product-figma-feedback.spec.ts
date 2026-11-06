import { test, expect } from '@playwright/test';

test.describe('Product Catalog Feedback Verification - Node 2701:5295 vs 2701:5584', () => {
    test.beforeEach(async ({ page, isMobile }) => {
        await page.goto('http://127.0.0.1:8000/san-pham');
        await page.waitForLoadState('networkidle');

        if (isMobile) {
            // Open mobile filter drawer
            const filterTrigger = page.locator('button[title="Mở bộ lọc"]');
            if (await filterTrigger.isVisible()) {
                await filterTrigger.click();
                await page.waitForTimeout(300);
            }
        }
    });

    test('1. Sidebar has exact Figma categories with chevrons and subcategories', async ({ page, isMobile }) => {
        const filterRoot = isMobile ? page.locator('.fixed.inset-y-0.left-0') : page.locator('aside');
        await expect(filterRoot).toBeVisible();

        // DANH MỤC SẢN PHẨM header
        await expect(filterRoot.locator('h4:has-text("DANH MỤC SẢN PHẨM")')).toBeVisible();

        // 4 categories match Figma Node 2701:5295 exactly
        await expect(filterRoot.locator('span:text-is("Tất cả sản phẩm")')).toBeVisible();
        await expect(filterRoot.locator('span:text-is("Móc khoá")')).toBeVisible();
        await expect(filterRoot.locator('span:text-is("Văn phòng phẩm")')).toBeVisible();
        await expect(filterRoot.locator('span:text-is("Gia công theo yêu cầu")')).toBeVisible();

        // Chevron icon is visible for categories
        const chevronButtons = filterRoot.locator('button[title="Mở rộng danh mục con"]');
        await expect(chevronButtons).toHaveCount(3);

        // Subcategories under Văn phòng phẩm (Ly, Lọ cắm hoa để bàn, Đèn)
        await expect(filterRoot.locator('button:has-text("Ly")')).toBeVisible();
        await expect(filterRoot.locator('button:has-text("Lọ cắm hoa để bàn")')).toBeVisible();
        await expect(filterRoot.locator('button:has-text("Đèn")')).toBeVisible();
    });

    test('2. Checkboxes in LỌC THEO NHU CẦU are placed BEFORE label with 12px gap', async ({ page, isMobile }) => {
        const filterRoot = isMobile ? page.locator('.fixed.inset-y-0.left-0') : page.locator('aside');
        await expect(filterRoot.locator('h4:has-text("LỌC THEO NHU CẦU")')).toBeVisible();

        const purposeBtn = filterRoot.locator('button:has-text("Trang trí không gian")').first();
        await expect(purposeBtn).toBeVisible();

        // Check DOM order: checkbox div comes BEFORE span label
        const checkboxDiv = purposeBtn.locator('div.w-4.h-4');
        const labelSpan = purposeBtn.locator('span:has-text("Trang trí không gian")');

        await expect(checkboxDiv).toBeVisible();
        await expect(labelSpan).toBeVisible();

        // Verify bounding boxes: checkbox x is to the left of label x
        const checkBBox = await checkboxDiv.boundingBox();
        const labelBBox = await labelSpan.boundingBox();
        expect(checkBBox).not.toBeNull();
        expect(labelBBox).not.toBeNull();
        expect(checkBBox!.x).toBeLessThan(labelBBox!.x);

        // Verify gap is 12px (within rounding: 10px-14px)
        const gap = labelBBox!.x - (checkBBox!.x + checkBBox!.width);
        expect(Math.round(gap)).toBe(12);
    });

    test('3. Reset filter button is REMOVED from sidebar', async ({ page, isMobile }) => {
        const filterRoot = isMobile ? page.locator('.fixed.inset-y-0.left-0') : page.locator('aside');
        await expect(filterRoot).toBeVisible();

        // Verify sidebar does NOT have "Đặt lại bộ lọc"
        await expect(filterRoot.locator('button:has-text("Đặt lại bộ lọc")')).toHaveCount(0);
    });

    test('4. Active filter chips and Reset button appear above products when filters are selected', async ({ page, isMobile }) => {
        test.skip(isMobile, 'Desktop filter chips verification');

        // Initial state: no filter chips
        await expect(page.locator('text=Đang lọc:')).toHaveCount(0);

        // Select a purpose checkbox
        const purposeBtn = page.locator('aside button:has-text("Trang trí không gian")').first();
        await purposeBtn.click();
        await page.waitForTimeout(300);

        // Active filter chips row appears above products
        await expect(page.locator('text=Đang lọc:')).toBeVisible();
        const removePurposeBtn = page.locator('button[title="Bỏ chọn nhu cầu này"]');
        await expect(removePurposeBtn).toBeVisible();

        // Subtle reset button appears beside chips
        const resetBtn = page.locator('div:has(> span:text-is("Đang lọc:")) button:has-text("Đặt lại bộ lọc")');
        await expect(resetBtn).toBeVisible();

        // Screenshot of active filter chips state
        await page.screenshot({ path: 'tests/screenshots/product-filter-chips-active.png', fullPage: false });

        // Click reset filter button
        await resetBtn.click();
        await page.waitForTimeout(300);

        // All chips should disappear
        await expect(page.locator('text=Đang lọc:')).toHaveCount(0);
    });

    test('5. Full visual comparison snapshot', async ({ page, isMobile }) => {
        if (!isMobile) {
            await page.setViewportSize({ width: 1440, height: 900 });
            await page.screenshot({ path: 'tests/screenshots/product-page-figma-5295-verified.png', fullPage: false });
        }
    });
});
