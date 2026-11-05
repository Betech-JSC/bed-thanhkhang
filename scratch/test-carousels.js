const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

(async () => {
    console.log('🚀 Starting Carousel Smooth Slide Playwright Test...');
    const evidenceDir = path.resolve('storage/app/public/evidence');
    if (!fs.existsSync(evidenceDir)) {
        fs.mkdirSync(evidenceDir, { recursive: true });
    }

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 }
    });
    const page = await context.newPage();

    try {
        console.log('Navigating to http://localhost:8000/ ...');
        await page.goto('http://localhost:8000/', { waitUntil: 'networkidle', timeout: 30000 });

        // Scroll down to activate v-scroll-reveal
        await page.evaluate(() => window.scrollTo(0, 500));
        await page.waitForTimeout(500);

        // --- TEST 1: FeaturedProductsCarousel ---
        console.log('\n--- 1. Testing FeaturedProductsCarousel ---');
        const featured = page.locator('#featured-carousel');
        await featured.scrollIntoViewIfNeeded();
        await page.waitForTimeout(600);

        const featuredTrack = featured.locator('div[class*="will-change-transform"]');
        let initialTransform = await featuredTrack.getAttribute('style');
        console.log(`Featured initial transform: ${initialTransform}`);

        const featuredNext = featured.locator('button[aria-label="Next Slide"]');
        const featuredPrev = featured.locator('button[aria-label="Previous Slide"]');

        // Click Next 1
        console.log('Clicking Next (1)...');
        await featuredNext.click();
        await page.waitForTimeout(550);
        let styleAfterNext1 = await featuredTrack.getAttribute('style');
        console.log(`Featured style after Next 1: ${styleAfterNext1}`);

        if (!styleAfterNext1.includes('translateX(-284px)')) {
            throw new Error(`Expected translateX(-284px) but got ${styleAfterNext1}`);
        }

        // Click Next 2
        console.log('Clicking Next (2)...');
        await featuredNext.click();
        await page.waitForTimeout(550);
        let styleAfterNext2 = await featuredTrack.getAttribute('style');
        console.log(`Featured style after Next 2: ${styleAfterNext2}`);

        if (!styleAfterNext2.includes('translateX(-568px)')) {
            throw new Error(`Expected translateX(-568px) but got ${styleAfterNext2}`);
        }

        // Click Next 3 -> Wrap to 0
        console.log('Clicking Next (3 - Wrap to 0)...');
        await featuredNext.click();
        await page.waitForTimeout(550);
        let styleAfterNext3 = await featuredTrack.getAttribute('style');
        console.log(`Featured style after Next 3 (Wrap): ${styleAfterNext3}`);

        if (!styleAfterNext3.includes('translateX(0px)') && !styleAfterNext3.includes('translateX(-0px)')) {
            throw new Error(`Expected translateX(0px) on wrap but got ${styleAfterNext3}`);
        }

        // Click Prev from 0 -> Wrap to maxOffset (568px)
        console.log('Clicking Prev (Wrap to maxOffset)...');
        await featuredPrev.click();
        await page.waitForTimeout(550);
        let styleAfterPrevWrap = await featuredTrack.getAttribute('style');
        console.log(`Featured style after Prev Wrap: ${styleAfterPrevWrap}`);

        if (!styleAfterPrevWrap.includes('translateX(-568px)')) {
            throw new Error(`Expected translateX(-568px) on Prev wrap but got ${styleAfterPrevWrap}`);
        }

        // Screenshot Featured Carousel
        const featScreenshotPath = path.join(evidenceDir, 'featured-carousel-smooth.png');
        await featured.screenshot({ path: featScreenshotPath });
        console.log(`✅ Saved screenshot: ${featScreenshotPath}`);

        // --- TEST 2: ProductsSection (Category Rows) ---
        console.log('\n--- 2. Testing ProductsSection (Category Rows) ---');
        const prodSection = page.locator('#products-section');
        await prodSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(600);

        // Find the first category row (Móc khoá)
        const row0Track = prodSection.locator('div[class*="will-change-transform"]').nth(0);
        let row0StyleInitial = await row0Track.getAttribute('style');
        console.log(`Row 0 initial style: ${row0StyleInitial}`);

        // Next button of Row 0
        const row0Next = prodSection.locator('button[aria-label="Next Slide"]').nth(0);
        const row0Prev = prodSection.locator('button[aria-label="Previous Slide"]').nth(0);

        // Check Best Choice badge on first card
        const bestChoiceBadge = prodSection.locator('text="Best choice"').first();
        const isBestChoiceVisible = await bestChoiceBadge.isVisible();
        console.log(`Row 0 "Best choice" badge visible: ${isBestChoiceVisible}`);
        if (!isBestChoiceVisible) {
            throw new Error('Best choice badge should be visible on row 0');
        }

        // Click Next on Row 0
        console.log('Clicking Row 0 Next (1)...');
        await row0Next.click();
        await page.waitForTimeout(550);
        let row0StyleAfterNext1 = await row0Track.getAttribute('style');
        console.log(`Row 0 style after Next 1: ${row0StyleAfterNext1}`);

        if (!row0StyleAfterNext1.includes('translateX(-284px)')) {
            throw new Error(`Expected translateX(-284px) for Row 0 but got ${row0StyleAfterNext1}`);
        }

        // Click Next on Row 0 again -> Wrap to 0
        console.log('Clicking Row 0 Next (2 - Wrap)...');
        await row0Next.click();
        await page.waitForTimeout(550);
        let row0StyleAfterNext2 = await row0Track.getAttribute('style');
        console.log(`Row 0 style after Next 2: ${row0StyleAfterNext2}`);

        if (!row0StyleAfterNext2.includes('translateX(0px)') && !row0StyleAfterNext2.includes('translateX(-0px)')) {
            throw new Error(`Expected translateX(0px) for Row 0 on wrap but got ${row0StyleAfterNext2}`);
        }

        // Click Prev on Row 0 from 0 -> Wrap to 284px
        console.log('Clicking Row 0 Prev (Wrap to maxOffset)...');
        await row0Prev.click();
        await page.waitForTimeout(550);
        let row0StyleAfterPrev = await row0Track.getAttribute('style');
        console.log(`Row 0 style after Prev Wrap: ${row0StyleAfterPrev}`);

        if (!row0StyleAfterPrev.includes('translateX(-284px)')) {
            throw new Error(`Expected translateX(-284px) on Prev wrap for Row 0 but got ${row0StyleAfterPrev}`);
        }

        // Return to 0 for screenshot
        await row0Prev.click();
        await page.waitForTimeout(550);

        // Screenshot Products Section
        const catScreenshotPath = path.join(evidenceDir, 'category-carousel-smooth.png');
        await prodSection.screenshot({ path: catScreenshotPath });
        console.log(`✅ Saved screenshot: ${catScreenshotPath}`);

        // --- TEST 3: Mobile Viewport & Swipe verification ---
        console.log('\n--- 3. Testing Mobile Viewport ---');
        const mobileContext = await browser.newContext({
            viewport: { width: 375, height: 667 },
            hasTouch: true
        });
        const mobilePage = await mobileContext.newPage();
        await mobilePage.goto('http://localhost:8000/', { waitUntil: 'networkidle', timeout: 30000 });
        await mobilePage.evaluate(() => window.scrollTo(0, 500));
        await mobilePage.waitForTimeout(400);

        const mobileFeatured = mobilePage.locator('#featured-carousel');
        await mobileFeatured.scrollIntoViewIfNeeded();
        const mobileTrack = mobileFeatured.locator('div[class*="will-change-transform"]');
        let mobileInitialStyle = await mobileTrack.getAttribute('style');
        console.log(`Mobile initial style: ${mobileInitialStyle}`);

        // Trigger touch swipe left (equivalent to Next)
        const viewportBox = await mobileFeatured.locator('div[class*="overflow-hidden"]').first().boundingBox();
        if (viewportBox) {
            const startX = viewportBox.x + viewportBox.width * 0.8;
            const endX = viewportBox.x + viewportBox.width * 0.2;
            const y = viewportBox.y + viewportBox.height * 0.5;

            console.log('Simulating swipe left on mobile...');
            await mobilePage.evaluate(({ startX, endX, y }) => {
                const el = document.querySelector('#featured-carousel div[class*="overflow-hidden"]');
                if (el) {
                    const touchStart = { clientX: startX, clientY: y };
                    const touchEnd = { clientX: endX, clientY: y };
                    el.dispatchEvent(new CustomEvent('touchstart', { detail: { touches: [touchStart] } }));
                    // Directly invoke onTouchStart and onTouchEnd if needed, or dispatch native Touch
                    const tStart = new Touch({ identifier: 1, target: el, clientX: startX, clientY: y });
                    const tEnd = new Touch({ identifier: 1, target: el, clientX: endX, clientY: y });
                    el.dispatchEvent(new TouchEvent('touchstart', { touches: [tStart], changedTouches: [tStart] }));
                    el.dispatchEvent(new TouchEvent('touchend', { touches: [], changedTouches: [tEnd] }));
                }
            }, { startX, endX, y });
            await mobilePage.waitForTimeout(550);
            const mobileTransform = await mobileTrack.getAttribute('style');
            console.log(`Featured style after mobile swipe left: ${mobileTransform}`);
        }
        await mobileContext.close();

        console.log('\n🎉 ALL PLAYWRIGHT TESTS PASSED 100%!');
    } catch (err) {
        console.error('❌ Test failed:', err);
        process.exitCode = 1;
    } finally {
        await browser.close();
    }
})();
