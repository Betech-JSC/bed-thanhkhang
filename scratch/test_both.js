const { chromium } = require('@playwright/test');
const fs = require('fs');

(async () => {
    const browser = await chromium.launch();
    const page = await browser.newPage({ viewport: { width: 1440, height: 900 } });
    
    // Navigate to /lien-he
    await page.goto('http://127.0.0.1:8000/lien-he', { waitUntil: 'networkidle' });
    await page.waitForTimeout(500);

    // Test with crop2700
    await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker"]');
        if (img) {
            img.src = '/images/contact/hh_maker_monogram_crop2700.png';
            img.style.opacity = '0.20';
        }
    });
    await page.waitForTimeout(500);
    await page.screenshot({ path: 'storage/app/public/evidence/test-crop2700.png' });
    console.log('Saved test-crop2700.png');

    // Test with clean3401
    await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker"]');
        if (img) {
            img.src = '/images/contact/hh_maker_monogram_clean3401.png';
            img.style.opacity = '0.20';
        }
    });
    await page.waitForTimeout(500);
    await page.screenshot({ path: 'storage/app/public/evidence/test-clean3401.png' });
    console.log('Saved test-clean3401.png');

    // Also test with tight crop
    await page.evaluate(() => {
        const img = document.querySelector('img[src*="hh_maker"]');
        if (img) {
            img.src = '/images/contact/hh_maker_monogram_tight.png';
            img.style.opacity = '0.20';
        }
    });
    await page.waitForTimeout(500);
    await page.screenshot({ path: 'storage/app/public/evidence/test-tight.png' });
    console.log('Saved test-tight.png');

    await browser.close();
})().catch(err => {
    console.error('Error running test_both.js:', err);
    process.exit(1);
});
