const { chromium } = require('playwright');
const fs = require('fs');
const path = require('path');
const { loadAuth } = require('./auth');

async function discover() {
    const browser = await chromium.launch({ headless: true }); // Headless for VPS
    const context = await browser.newContext();

    // Mock logger
    const logger = {
        info: (msg) => console.log(`[INFO] ${msg}`),
        error: (msg) => console.error(`[ERROR] ${msg}`),
        warn: (msg) => console.warn(`[WARN] ${msg}`)
    };

    // Load auth (cookies)
    await loadAuth(context, logger);

    // Load LocalStorage (Auth0 tokens)
    const lsPath = path.join(__dirname, '../storage/local_storage.json');
    if (fs.existsSync(lsPath)) {
        try {
            const lsData = JSON.parse(fs.readFileSync(lsPath, 'utf8'));

            // Inject LocalStorage before page load
            await context.addInitScript(data => {
                // Only inject on the target domain to avoid leaking tokens
                if (window.location.hostname.includes('mrktedge.ai')) {
                    for (const [key, value] of Object.entries(data)) {
                        // Values in the file are objects, but LS expects strings.
                        // Auth0 SDK expects the value to be a JSON string.
                        window.localStorage.setItem(key, JSON.stringify(value));
                    }
                    console.log('Injected LocalStorage keys: ' + Object.keys(data).join(', '));
                }
            }, lsData);
            logger.info(`Prepared LocalStorage injection for ${Object.keys(lsData).length} keys.`);
        } catch (e) {
            logger.error(`Failed to load/inject LocalStorage: ${e.message}`);
        }
    } else {
        logger.warn('No local_storage.json found. Authentication might fail.');
    }

    const page = await context.newPage();
    const endpoints = new Set();

    // Log all requests
    page.on('request', request => {
        const url = request.url();
        if (url.includes('mrkt-server') || url.includes('api')) {
            // Simplify URL to find unique endpoints (remove query params)
            const cleanUrl = url.split('?')[0];
            if (!endpoints.has(cleanUrl)) {
                endpoints.add(cleanUrl);
                console.log(`[DISCOVERED] ${request.method()} ${cleanUrl}`);
            }
        }
    });

    try {
        console.log("Navigating to dashboard...");
        await page.goto('https://app.mrktedge.ai/dashboard', { waitUntil: 'networkidle' });

        console.log("Waiting for data to load...");
        await page.waitForTimeout(5000); // Wait for extra requests

        // Maybe navigate to another page if possible?
        // await page.click('text=News'); 
        // await page.waitForTimeout(3000);

    } catch (e) {
        console.error("Error during discovery:", e.message);
    } finally {
        await browser.close();
        console.log("\n--- Discovery Complete ---");
        console.log("Unique Endpoints Found:");
        endpoints.forEach(e => console.log(e));
    }
}

discover();
