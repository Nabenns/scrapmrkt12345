const fs = require('fs');
const path = require('path');

const COOKIE_PATH = path.join(__dirname, '../storage/mrkt.json');
const LS_PATH = path.join(__dirname, '../storage/local_storage.json');
const OUTPUT_PATH = path.join(__dirname, '../storage/storage_state.json');

function convert() {
    let state = {
        cookies: [],
        origins: []
    };

    // 1. Process Cookies
    if (fs.existsSync(COOKIE_PATH)) {
        try {
            const rawCookies = JSON.parse(fs.readFileSync(COOKIE_PATH, 'utf8'));
            // Handle if it's an array or object
            const cookiesArray = Array.isArray(rawCookies) ? rawCookies : (rawCookies.cookies || []);

            state.cookies = cookiesArray.map(c => {
                // Fix SameSite
                let sameSite = 'None';
                if (c.sameSite === 'lax' || c.sameSite === 'Lax') sameSite = 'Lax';
                if (c.sameSite === 'strict' || c.sameSite === 'Strict') sameSite = 'Strict';
                if (c.sameSite === 'no_restriction') sameSite = 'None';

                // Fix Expiration
                const expires = c.expirationDate || c.expires || -1;

                return {
                    name: c.name,
                    value: c.value,
                    domain: c.domain,
                    path: c.path,
                    expires: expires,
                    httpOnly: c.httpOnly,
                    secure: c.secure,
                    sameSite: sameSite
                };
            });
            console.log(`Processed ${state.cookies.length} cookies.`);
        } catch (e) {
            console.error('Error processing cookies:', e);
        }
    }

    // 2. Process LocalStorage
    if (fs.existsSync(LS_PATH)) {
        try {
            const lsData = JSON.parse(fs.readFileSync(LS_PATH, 'utf8'));
            const lsEntries = [];

            for (const [key, value] of Object.entries(lsData)) {
                lsEntries.push({
                    name: key,
                    value: JSON.stringify(value) // LocalStorage values are strings
                });
            }

            if (lsEntries.length > 0) {
                state.origins.push({
                    origin: 'https://app.mrktedge.ai', // Target origin
                    localStorage: lsEntries
                });
            }
            console.log(`Processed ${lsEntries.length} LocalStorage entries.`);
        } catch (e) {
            console.error('Error processing LocalStorage:', e);
        }
    }

    // 3. Save
    fs.writeFileSync(OUTPUT_PATH, JSON.stringify(state, null, 2));
    console.log(`Saved storage state to ${OUTPUT_PATH}`);
}

convert();
