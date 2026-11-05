const { chromium } = require('@playwright/test');

(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 375, height: 667 } });
    await page.goto('http://127.0.0.1:8000/lien-he', { waitUntil: 'networkidle' });
    
    // Test clean3401
    await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker"]');
        if (img) {
            img.src = '/images/contact/hh_maker_monogram_clean3401.png';
            img.style.opacity = '0.20';
        }
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'storage/app/public/evidence/mobile-clean3401.png' });

    // Test crop2700
    await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker"]');
        if (img) {
            img.src = '/images/contact/hh_maker_monogram_crop2700.png';
            img.style.opacity = '0.20';
        }
    });
    await page.waitForTimeout(300);
    await page.screenshot({ path: 'storage/app/public/evidence/mobile-crop2700.png' });

    await browser.close();
    console.log('Mobile screenshots taken successfully');
})().catch(console.error);
