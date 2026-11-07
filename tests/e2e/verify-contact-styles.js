const { chromium } = require('playwright');
const path = require('path');
const fs = require('fs');

async function run() {
    const screenshotDir = path.join(__dirname, 'screenshots');
    if (!fs.existsSync(screenshotDir)) {
        fs.mkdirSync(screenshotDir, { recursive: true });
    }

    const browser = await chromium.launch({ headless: true });
    const context = await browser.newContext({
        viewport: { width: 1440, height: 900 },
        deviceScaleFactor: 1
    });
    const page = await context.newPage();

    console.log('Navigating to http://127.0.0.1:8000/lien-he ...');
    await page.goto('http://127.0.0.1:8000/lien-he', { waitUntil: 'networkidle' });

    // Wait for contact elements
    await page.waitForSelector('section');

    const results = {
        cards: [],
        inputs: [],
        faq: []
    };

    // 1. Verify 3 Contact Cards
    console.log('\n--- 1. Testing 3 Contact Cards ---');
    const cards = page.locator('section a[href*="zalo"], section a[href^="tel:"], section a[href^="mailto:"]');
    const cardCount = await cards.count();
    console.log(`Found ${cardCount} contact cards.`);

    // Scroll to cards
    await cards.first().scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);

    for (let i = 0; i < cardCount; i++) {
        const card = cards.nth(i);
        const normalBorder = await card.evaluate(el => window.getComputedStyle(el).borderColor);
        const normalBg = await card.evaluate(el => window.getComputedStyle(el).backgroundColor);

        await card.hover();
        await page.waitForTimeout(300);

        const hoverBorder = await card.evaluate(el => window.getComputedStyle(el).borderColor);
        const hoverBg = await card.evaluate(el => window.getComputedStyle(el).backgroundColor);
        const hoverShadow = await card.evaluate(el => window.getComputedStyle(el).boxShadow);

        const isNotBlue = !hoverBorder.includes('41, 112, 255') && !hoverShadow.includes('41, 112, 255');
        const isWhiteMuted = hoverBorder.includes('255, 255, 255') || hoverBorder === 'rgba(255, 255, 255, 0.5)';

        results.cards.push({
            index: i + 1,
            normalBorder,
            hoverBorder,
            hoverBg,
            hoverShadow,
            isNotBlue,
            isWhiteMuted
        });

        console.log(`Card ${i + 1}: Hover Border = ${hoverBorder}, Hover BG = ${hoverBg}, Shadow = ${hoverShadow}, No Blue = ${isNotBlue}`);
    }

    // Capture screenshot of cards with hover on Card 1 (Zalo)
    await cards.nth(0).hover();
    await page.waitForTimeout(200);
    const cardSection = cards.first().locator('xpath=ancestor::section[1]');
    await cardSection.screenshot({ path: path.join(screenshotDir, 'contact-cards-hover-zalo-verified.png') });

    // Capture screenshot with hover on Card 2 (Hotline)
    await cards.nth(1).hover();
    await page.waitForTimeout(200);
    await cardSection.screenshot({ path: path.join(screenshotDir, 'contact-cards-hover-hotline-verified.png') });

    // Capture screenshot with hover on Card 3 (Email)
    await cards.nth(2).hover();
    await page.waitForTimeout(200);
    await cardSection.screenshot({ path: path.join(screenshotDir, 'contact-cards-hover-email-verified.png') });

    // 2. Verify Form Inputs (Name, Phone, Email, Textarea)
    console.log('\n--- 2. Testing Form Inputs ---');
    const formSection = page.locator('form').locator('xpath=ancestor::section[1]');
    await formSection.scrollIntoViewIfNeeded();
    await page.waitForTimeout(500);

    const inputsToTest = [
        { name: 'name', selector: 'input[placeholder*="họ và tên" i]' },
        { name: 'phone', selector: 'input[placeholder*="số điện thoại" i]' },
        { name: 'email', selector: 'input[placeholder*="email" i]' },
        { name: 'content', selector: 'textarea[placeholder*="ý tưởng" i]' }
    ];

    for (const item of inputsToTest) {
        const input = page.locator(item.selector);
        await input.scrollIntoViewIfNeeded();

        // Normal
        const normalBorder = await input.evaluate(el => window.getComputedStyle(el).borderColor);

        // Hover
        await input.hover();
        await page.waitForTimeout(200);
        const hoverBorder = await input.evaluate(el => window.getComputedStyle(el).borderColor);

        // Focus
        await input.focus();
        await page.waitForTimeout(300);
        const focusBorder = await input.evaluate(el => window.getComputedStyle(el).borderColor);
        const focusRing = await input.evaluate(el => window.getComputedStyle(el).boxShadow);

        const isNotBlue = !focusBorder.includes('41, 112, 255') && !focusRing.includes('41, 112, 255');
        const isWhiteFocus = focusBorder === 'rgb(255, 255, 255)';
        const hasWhiteRing = focusRing.includes('255, 255, 255');

        results.inputs.push({
            name: item.name,
            normalBorder,
            hoverBorder,
            focusBorder,
            focusRing,
            isNotBlue,
            isWhiteFocus,
            hasWhiteRing
        });

        console.log(`Input [${item.name}]: Hover Border = ${hoverBorder}, Focus Border = ${focusBorder}, Ring = ${focusRing}, No Blue = ${isNotBlue}`);
    }

    // Capture screenshot with Name input focused
    await page.locator(inputsToTest[0].selector).focus();
    await page.waitForTimeout(200);
    await formSection.screenshot({ path: path.join(screenshotDir, 'contact-form-focus-name-verified.png') });

    // Capture screenshot with Textarea focused
    await page.locator(inputsToTest[3].selector).focus();
    await page.waitForTimeout(200);
    await formSection.screenshot({ path: path.join(screenshotDir, 'contact-form-focus-textarea-verified.png') });

    // 3. Verify FAQ Accordion
    console.log('\n--- 3. Testing FAQ Accordion ---');
    const faqSection = page.locator('section').filter({ hasText: 'Giải đáp trước khi liên hệ' });
    if (await faqSection.count() > 0) {
        await faqSection.scrollIntoViewIfNeeded();
        await page.waitForTimeout(500);

        const faqItems = faqSection.locator('.space-y-4 > div');
        const faqCount = await faqItems.count();
        console.log(`Found ${faqCount} FAQ items.`);

        if (faqCount > 0) {
            const firstFaq = faqItems.first();

            // Normal state
            const normalBorder = await firstFaq.evaluate(el => window.getComputedStyle(el).borderColor);
            const normalBg = await firstFaq.evaluate(el => window.getComputedStyle(el).backgroundColor);

            // Hover state
            await firstFaq.hover();
            await page.waitForTimeout(300);
            const hoverBorder = await firstFaq.evaluate(el => window.getComputedStyle(el).borderColor);
            const hoverBg = await firstFaq.evaluate(el => window.getComputedStyle(el).backgroundColor);

            await faqSection.screenshot({ path: path.join(screenshotDir, 'contact-faq-hover-verified.png') });

            // Active state (Click to expand)
            await firstFaq.locator('button').click();
            await page.waitForTimeout(400);

            const activeBorder = await firstFaq.evaluate(el => window.getComputedStyle(el).borderColor);
            const activeBg = await firstFaq.evaluate(el => window.getComputedStyle(el).backgroundColor);

            await faqSection.screenshot({ path: path.join(screenshotDir, 'contact-faq-active-verified.png') });

            results.faq.push({
                normalBorder,
                normalBg,
                hoverBorder,
                hoverBg,
                activeBorder,
                activeBg
            });

            console.log(`FAQ: Normal Border = ${normalBorder}, Hover Border = ${hoverBorder}, Active Border = ${activeBorder}`);
            console.log(`FAQ: Normal BG = ${normalBg}, Hover BG = ${hoverBg}, Active BG = ${activeBg}`);
        }
    } else {
        console.log('FAQ section not found or has 0 items.');
    }

    console.log('\n--- Test Results Summary ---');
    console.log(JSON.stringify(results, null, 2));

    await browser.close();
    console.log('Verification completed successfully!');
}

run().catch(err => {
    console.error('Test execution failed:', err);
    process.exit(1);
});
