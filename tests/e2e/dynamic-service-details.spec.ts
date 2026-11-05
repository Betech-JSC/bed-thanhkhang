import { test, expect } from '@playwright/test';
import * as path from 'path';

test.describe('Dynamic Service Details Multi-Service Verification (/dich-vu/{slug})', () => {

    test('1. Verify 3D Design (thiet-ke-3d): Hero, TOC, and 4 Technical Sections', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // 1. Hero Title & Headline
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        await expect(h1).toContainText('Thiết kế 3D: Từ phác thảo đến hình khối rõ ràng');

        // Hero Input & Process items
        const colStart = page.locator('text=BẮT ĐẦU TỪ').first();
        await expect(colStart).toBeVisible();
        await expect(page.locator('text=Ý tưởng').first()).toBeVisible();
        await expect(page.locator('text=Ảnh tham khảo').first()).toBeVisible();
        await expect(page.locator('text=Bản phác thảo').first()).toBeVisible();

        const colClarify = page.locator('text=CÙNG LÀM RÕ').first();
        await expect(colClarify).toBeVisible();
        await expect(page.locator('text=Tỉ lệ & hình khối').first()).toBeVisible();
        await expect(page.locator('text=Cấu trúc & chi tiết').first()).toBeVisible();
        await expect(page.locator('text=Khả năng chế tác').first()).toBeVisible();

        // 2. TOC Sidebar
        const tocButtons = page.locator('aside nav button');
        await expect(tocButtons).toHaveCount(4);
        await expect(tocButtons.nth(0)).toContainText('Khi nào bạn cần thiết kế 3D?');
        await expect(tocButtons.nth(1)).toContainText('Từ ý tưởng tới mô hình 3D');
        await expect(tocButtons.nth(2)).toContainText('Bạn cần chuẩn bị gì?');
        await expect(tocButtons.nth(3)).toContainText('Cùng hoàn thiện trước khi gia công');

        // 3. Technical Sections
        const sec1 = page.locator('#section-01');
        await expect(sec1.locator('h2')).toContainText('Khi nào bạn cần thiết kế 3D?');
        await expect(sec1).toContainText('Bạn đã hình dung một món đồ nhưng chưa có mô hình để triển khai?');

        const sec2 = page.locator('#section-02');
        await expect(sec2.locator('h2')).toContainText('Từ ý tưởng tới mô hình 3D');
        const sec2Badges = sec2.locator('.w-10.h-10');
        await expect(sec2Badges).toHaveCount(4);
        await expect(sec2).toContainText('Trao đổi ý tưởng và mục đích sử dụng');
        await expect(sec2).toContainText('Dựng hình khối và xác định tỉ lệ');

        const sec3 = page.locator('#section-03');
        await expect(sec3.locator('h2')).toContainText('Bạn cần chuẩn bị gì?');
        await expect(sec3).toContainText('Ý tưởng & mục đích sử dụng');
        await expect(sec3).toContainText('Ảnh tham khảo hoặc phác thảo');

        const sec4 = page.locator('#section-04');
        await expect(sec4.locator('h2')).toContainText('Cùng hoàn thiện trước khi gia công');
        const callout = sec4.locator('.border-l-2');
        await expect(callout).toContainText('Một mô hình đẹp trên màn hình');

        // Visual screenshot Desktop
        await page.screenshot({
            path: 'docs/evidence/service-thiet-ke-3d-desktop.png',
            fullPage: true,
        });
    });

    test('2. Verify 3D Machining (gia-cong-3d): Unique Hero, TOC, and 4 Technical Sections', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu/gia-cong-3d', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // 1. Hero Title & Headline
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        await expect(h1).toContainText('Gia công 3D: Hiện thực hóa mô hình với độ chính xác cao');

        // Hero Input & Process items (Must be unique for Machining)
        await expect(page.locator('text=BẮT ĐẦU TỪ').first()).toBeVisible();
        await expect(page.locator('text=File 3D (STL/STEP/OBJ)').first()).toBeVisible();
        await expect(page.locator('text=Kích thước & độ bền').first()).toBeVisible();
        await expect(page.locator('text=Số lượng cần làm').first()).toBeVisible();

        await expect(page.locator('text=CÙNG LÀM RÕ').first()).toBeVisible();
        await expect(page.locator('text=Công nghệ & vật liệu in').first()).toBeVisible();
        await expect(page.locator('text=Độ mịn & độ dày thành').first()).toBeVisible();
        await expect(page.locator('text=Phương án hậu kỳ bề mặt').first()).toBeVisible();

        // 2. TOC Sidebar (Must reflect 3D Machining sections)
        const tocButtons = page.locator('aside nav button');
        await expect(tocButtons).toHaveCount(4);
        await expect(tocButtons.nth(0)).toContainText('Khi nào bạn cần gia công 3D?');
        await expect(tocButtons.nth(1)).toContainText('Quy trình gia công chuẩn xác');
        await expect(tocButtons.nth(2)).toContainText('Thông số kỹ thuật cần chuẩn bị');
        await expect(tocButtons.nth(3)).toContainText('Hậu kỳ & Tiêu chuẩn hoàn thiện');

        // 3. Technical Sections
        const sec1 = page.locator('#section-01');
        await expect(sec1.locator('h2')).toContainText('Khi nào bạn cần gia công 3D?');
        await expect(sec1).toContainText('Bạn đã có sẵn file thiết kế 3D');

        const sec2 = page.locator('#section-02');
        await expect(sec2.locator('h2')).toContainText('Quy trình gia công chuẩn xác');
        await expect(sec2).toContainText('Kiểm tra và chuẩn hóa file 3D');
        await expect(sec2).toContainText('Lựa chọn công nghệ & vật liệu');
        await expect(sec2).toContainText('Tiến hành in & giám sát quá trình');
        await expect(sec2).toContainText('Xử lý hậu kỳ & kiểm định dung sai');

        const sec3 = page.locator('#section-03');
        await expect(sec3.locator('h2')).toContainText('Thông số kỹ thuật cần chuẩn bị');
        await expect(sec3).toContainText('Định dạng file 3D');
        await expect(sec3).toContainText('Yêu cầu vật liệu & môi trường làm việc');
        await expect(sec3).toContainText('Dung sai kích thước & độ mịn');
        await expect(sec3).toContainText('Số lượng & thời gian bàn giao');

        const sec4 = page.locator('#section-04');
        await expect(sec4.locator('h2')).toContainText('Hậu kỳ & Tiêu chuẩn hoàn thiện');
        const callout = sec4.locator('.border-l-2');
        await expect(callout).toContainText('co ngót vật liệu');

        // Assert that 3D Design specific headline does not leak into Machining
        await expect(page.locator('body')).not.toContainText('Từ phác thảo đến hình khối rõ ràng');

        // Visual screenshot Desktop
        await page.screenshot({
            path: 'docs/evidence/service-gia-cong-3d-desktop.png',
            fullPage: true,
        });
    });

    test('3. Verify Custom Creations (lam-do-custom): Unique Hero, TOC, and 4 Technical Sections', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });
        const response = await page.goto('/dich-vu/lam-do-custom', { waitUntil: 'domcontentloaded' });
        expect(response?.status()).toBe(200);

        // 1. Hero Title & Headline
        const h1 = page.locator('h1').first();
        await expect(h1).toBeVisible();
        await expect(h1).toContainText('Làm đồ custom: Chế tác cá nhân hóa độc bản theo yêu cầu');

        // Hero Input & Process items (Must be unique for Custom Creations)
        await expect(page.locator('text=BẮT ĐẦU TỪ').first()).toBeVisible();
        await expect(page.locator('text=Câu chuyện & ý tưởng riêng').first()).toBeVisible();
        await expect(page.locator('text=Chữ/Logo cá nhân hóa').first()).toBeVisible();
        await expect(page.locator('text=Kỷ vật hoặc mẫu có sẵn').first()).toBeVisible();

        await expect(page.locator('text=CÙNG LÀM RÕ').first()).toBeVisible();
        await expect(page.locator('text=Chất liệu & phối màu').first()).toBeVisible();
        await expect(page.locator('text=Chi tiết khắc / gắn logo').first()).toBeVisible();
        await expect(page.locator('text=Phong cách hoàn thiện').first()).toBeVisible();

        // 2. TOC Sidebar (Must reflect Custom Creations sections)
        const tocButtons = page.locator('aside nav button');
        await expect(tocButtons).toHaveCount(4);
        await expect(tocButtons.nth(0)).toContainText('Đồ custom dành cho ai?');
        await expect(tocButtons.nth(1)).toContainText('Các bước chế tác độc bản');
        await expect(tocButtons.nth(2)).toContainText('Bạn chuẩn bị thông tin gì?');
        await expect(tocButtons.nth(3)).toContainText('Bàn giao & Bảo quản sản phẩm');

        // 3. Technical Sections
        const sec1 = page.locator('#section-01');
        await expect(sec1.locator('h2')).toContainText('Đồ custom dành cho ai?');
        await expect(sec1).toContainText('món đồ kỷ niệm độc nhất vô nhị');

        const sec2 = page.locator('#section-02');
        await expect(sec2.locator('h2')).toContainText('Các bước chế tác độc bản');
        await expect(sec2).toContainText('Lắng nghe câu chuyện & định hình ý tưởng');
        await expect(sec2).toContainText('Phác thảo 3D & phối màu thử nghiệm');
        await expect(sec2).toContainText('Gia công tạo hình & đắp thủ công');
        await expect(sec2).toContainText('Đóng gói sang trọng & bàn giao');

        const sec3 = page.locator('#section-03');
        await expect(sec3.locator('h2')).toContainText('Bạn chuẩn bị thông tin gì?');
        await expect(sec3).toContainText('Nội dung cá nhân hóa');
        await expect(sec3).toContainText('Kích thước & không gian trưng bày');
        await expect(sec3).toContainText('Tone màu & chất liệu chủ đạo');
        await expect(sec3).toContainText('Ngân sách & thời điểm cần có');

        const sec4 = page.locator('#section-04');
        await expect(sec4.locator('h2')).toContainText('Bàn giao & Bảo quản sản phẩm');
        const callout = sec4.locator('.border-l-2');
        await expect(callout).toContainText('Mẹo bảo quản');

        // Assert that 3D Design specific headline does not leak into Custom
        await expect(page.locator('body')).not.toContainText('Từ phác thảo đến hình khối rõ ràng');

        // Visual screenshot Desktop
        await page.screenshot({
            path: 'docs/evidence/service-lam-do-custom-desktop.png',
            fullPage: true,
        });
    });

    test('4. Verify Multilingual English Pages (/en/services/{slug}) for all 3 Services', async ({ page }) => {
        await page.setViewportSize({ width: 1440, height: 900 });

        // 4.1 3D Design EN
        const res1 = await page.goto('/en/services/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        expect(res1?.status()).toBe(200);
        await expect(page.locator('h1').first()).toContainText('3D Design: From sketch to concrete geometry');
        await expect(page.locator('#section-01 h2')).toContainText('When do you need 3D design?');
        await expect(page.locator('#section-02 h2')).toContainText('From concept to 3D model');

        // 4.2 3D Machining EN
        const res2 = await page.goto('/en/services/gia-cong-3d', { waitUntil: 'domcontentloaded' });
        expect(res2?.status()).toBe(200);
        await expect(page.locator('h1').first()).toContainText('3D Machining & Printing: Bringing digital models into reality with high precision');
        await expect(page.locator('#section-01 h2')).toContainText('When do you need 3D machining & printing?');
        await expect(page.locator('#section-02 h2')).toContainText('Precision fabrication workflow');
        await expect(page.locator('#section-02')).toContainText('File Inspection & Normalization');
        await expect(page.locator('#section-03 h2')).toContainText('Technical specifications to prepare');
        await expect(page.locator('#section-04 h2')).toContainText('Post-processing & finishing standards');

        // 4.3 Custom Bespoke EN
        const res3 = await page.goto('/en/services/lam-do-custom', { waitUntil: 'domcontentloaded' });
        expect(res3?.status()).toBe(200);
        await expect(page.locator('h1').first()).toContainText('Custom Bespoke Creations: Personalized one-of-a-kind fabrication');
        await expect(page.locator('#section-01 h2')).toContainText('Who is custom fabrication for?');
        await expect(page.locator('#section-02 h2')).toContainText('Bespoke creation process');
        await expect(page.locator('#section-02')).toContainText('Story Discovery & Concept Definition');
        await expect(page.locator('#section-03 h2')).toContainText('What information to prepare?');
        await expect(page.locator('#section-04 h2')).toContainText('Delivery & product care');
    });

    test('5. Mobile Responsive Verification (375x667) and Visual Evidence Capture', async ({ page }) => {
        await page.setViewportSize({ width: 375, height: 667 });

        // Mobile 1: thiet-ke-3d
        await page.goto('/dich-vu/thiet-ke-3d', { waitUntil: 'domcontentloaded' });
        const mobileToc1 = page.locator('.overflow-x-auto button');
        await expect(mobileToc1).toHaveCount(4);
        await page.screenshot({
            path: 'docs/evidence/service-thiet-ke-3d-mobile.png',
            fullPage: true,
        });

        // Mobile 2: gia-cong-3d
        await page.goto('/dich-vu/gia-cong-3d', { waitUntil: 'domcontentloaded' });
        const mobileToc2 = page.locator('.overflow-x-auto button');
        await expect(mobileToc2).toHaveCount(4);
        await expect(mobileToc2.first()).toContainText('Khi nào cần gia công?');
        await page.screenshot({
            path: 'docs/evidence/service-gia-cong-3d-mobile.png',
            fullPage: true,
        });

        // Mobile 3: lam-do-custom
        await page.goto('/dich-vu/lam-do-custom', { waitUntil: 'domcontentloaded' });
        const mobileToc3 = page.locator('.overflow-x-auto button');
        await expect(mobileToc3).toHaveCount(4);
        await expect(mobileToc3.first()).toContainText('Đồ custom cho ai?');
        await page.screenshot({
            path: 'docs/evidence/service-lam-do-custom-mobile.png',
            fullPage: true,
        });
    });

});
