const fs = require('fs');
const path = require('path');

const COOKIE_PATH = path.join(__dirname, '../storage/mrkt.json');

async function loadAuth(context, logger) {
    if (fs.existsSync(COOKIE_PATH)) {
        try {
            const rawCookies = JSON.parse(fs.readFileSync(COOKIE_PATH, 'utf8'));
            const cookiesList = rawCookies.cookies || rawCookies;

            const validCookies = cookiesList.map(c => {
                const cookie = { ...c };
                if (cookie.expirationDate) {
                    cookie.expires = cookie.expirationDate;
                    delete cookie.expirationDate;
                }
                if (cookie.sameSite) {
                    if (cookie.sameSite === 'no_restriction') {
                        cookie.sameSite = 'None';
                    } else if (cookie.sameSite === 'unspecified') {
                        delete cookie.sameSite;
                    } else {
                        // Capitalize first letter (lax -> Lax, strict -> Strict)
                        cookie.sameSite = cookie.sameSite.charAt(0).toUpperCase() + cookie.sameSite.slice(1);
                    }
                }

                // Remove fields Playwright doesn't like
                delete cookie.hostOnly;
                delete cookie.session;
                delete cookie.storeId;
                delete cookie.id;

                return cookie;
            });

            if (validCookies.length > 0) {
                logger.info(`First cookie sample: ${JSON.stringify(validCookies[0])}`);
            }

            await context.addCookies(validCookies);
            logger.info(`Loaded ${validCookies.length} session cookies.`);
        } catch (err) {
            logger.error(`Failed to load cookies: ${err.message}`);
        }
    } else {
        logger.warn('No cookie file found at storage/mrkt.json. Scraping might fail if auth is required.');
    }
}

module.exports = { loadAuth };
