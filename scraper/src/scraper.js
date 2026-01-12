const axios = require('axios');
const fs = require('fs');
const path = require('path');
const { parseHeadlines } = require('./parser');
const { saveHeadlines } = require('./db');
const { randomDelay } = require('./antiBan');

const API_URL = 'https://mrkt-server-a240deff7152.herokuapp.com/equities/headlines?page=1&limit=50';

async function getAccessToken(logger) {
    const lsPath = path.join(__dirname, '../storage/local_storage.json');
    if (!fs.existsSync(lsPath)) {
        throw new Error('local_storage.json not found. Cannot authenticate.');
    }

    try {
        const lsData = JSON.parse(fs.readFileSync(lsPath, 'utf8'));

        // Robust search: Find ANY key that has body.access_token
        const tokenKey = Object.keys(lsData).find(k =>
            lsData[k] &&
            lsData[k].body &&
            lsData[k].body.access_token
        );

        if (!tokenKey) {
            // Fallback: Try to find by the old method if the structure is slightly different but key matches
            const legacyKey = Object.keys(lsData).find(k => k.includes('::openid profile email offline_access'));
            if (legacyKey && lsData[legacyKey] && lsData[legacyKey].body && lsData[legacyKey].body.access_token) {
                return lsData[legacyKey].body.access_token;
            }
            throw new Error('Access token not found in local_storage.json');
        }

        return lsData[tokenKey].body.access_token;
    } catch (err) {
        throw new Error(`Failed to parse local_storage.json: ${err.message}`);
    }
}

async function scrapeHeadlines(logger) {
    try {
        logger.info('Starting API scrape...');

        const token = await getAccessToken(logger);
        logger.info('Access token retrieved.');

        await randomDelay(1000, 3000); // Slight delay to be polite

        const response = await axios.get(API_URL, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept': 'application/json'
            },
            timeout: 30000
        });

        const data = response.data;

        // API returns { data: [...], pagination: {...} } OR { headlines: [...], ... }
        // So data.data OR data.headlines is the array of items

        let items = data.data || data.headlines;

        if (!items || !Array.isArray(items)) {
            // Fallback: maybe it's just an array?
            if (Array.isArray(data)) {
                items = data;
            } else {
                logger.error(`Unexpected API response structure. Keys: ${Object.keys(data || {})}`);
                logger.error('Raw Data Start:', JSON.stringify(data).substring(0, 500));
                throw new Error('Invalid API response format');
            }
        }

        logger.info(`Received ${items.length} items from API.`);

        const headlines = parseHeadlines(items);
        const insertedCount = await saveHeadlines(headlines, logger);

        return insertedCount;

    } catch (error) {
        logger.error(`API Scrape failed: ${error.message}`);
        if (error.response) {
            logger.error(`Status: ${error.response.status}, Data: ${JSON.stringify(error.response.data)}`);
        }
        throw error;
    }
}

module.exports = { scrapeHeadlines };
