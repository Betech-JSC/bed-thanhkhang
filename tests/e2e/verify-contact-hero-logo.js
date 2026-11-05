const { chromium } = require('@playwright/test');
const fs = require('fs');
const path = require('path');

(async () => {
    console.log('=== START PLAYWRIGHT VERIFICATION FOR CONTACT HERO LOGO ===');
    const browser = await chromium.launch();
    
    // 1. Desktop 1440x900
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    await page.goto('http://127.0.0.1:8000/lien-he', { waitUntil: 'networkidle' });
    await page.waitForTimeout(1000);

    const evidenceDir = path.resolve('storage/app/public/evidence');
    if (!fs.existsSync(evidenceDir)) {
        fs.mkdirSync(evidenceDir, { recursive: true });
    }

    // Inspect logo element
    const logoInfo = await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker_monogram.png"]');
        if (!img) return null;
        const style = window.getComputedStyle(img);
        const rect = img.getBoundingClientRect();
        return {
            src: img.getAttribute('src'),
            alt: img.getAttribute('alt'),
            classes: img.className,
            opacity: style.opacity,
            pointerEvents: style.pointerEvents,
            userSelect: style.userSelect,
            rect: {
                top: rect.top,
                left: rect.left,
                width: rect.width,
                height: rect.height,
                bottom: rect.bottom,
                right: rect.right
            }
        };
    });

    console.log('Logo Info:', JSON.stringify(logoInfo, null, 2));

    // Capture required evidence screenshot
    const evidencePath = path.join(evidenceDir, 'contact-hero-faded-logo.png');
    await page.screenshot({ path: evidencePath });
    console.log('Saved evidence screenshot to:', evidencePath);

    // Also capture hero banner close-up
    const heroCropPath = path.join(evidenceDir, 'contact-hero-faded-logo-closeup.png');
    const heroSection = page.locator('section').first();
    await heroSection.screenshot({ path: heroCropPath });
    console.log('Saved hero banner closeup to:', heroCropPath);

    // 2. Mobile 375x667 verification
    await page.setViewportSize({ width: 375, height: 667 });
    await page.waitForTimeout(500);
    const mobilePath = path.join(evidenceDir, 'contact-hero-mobile.png');
    await page.screenshot({ path: mobilePath });
    console.log('Saved mobile evidence to:', mobilePath);

    // 3. Check English locale route /en/contact
    await page.setViewportSize({ width: 1440, height: 900 });
    await page.goto('http://127.0.0.1:8000/en/contact', { waitUntil: 'networkidle' });
    await page.waitForTimeout(500);
    const enPath = path.join(evidenceDir, 'contact-hero-en-1440.png');
    await page.screenshot({ path: enPath });
    console.log('Saved English locale evidence to:', enPath);

    await browser.close();
    console.log('=== PLAYWRIGHT VERIFICATION COMPLETE ===');
})().catch(err => {
    console.error('Error during verification:', err);
    process.exit(1);
});
