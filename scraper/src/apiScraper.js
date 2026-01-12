const axios = require('axios');
const fs = require('fs');
const path = require('path');
const db = require('./db');

const BASE_URL = 'https://mrkt-server-a240deff7152.herokuapp.com';

async function getAccessToken() {
    const lsPath = path.join(__dirname, '../storage/local_storage.json');
    if (!fs.existsSync(lsPath)) return null;

    try {
        const lsData = JSON.parse(fs.readFileSync(lsPath, 'utf8'));
        const tokenKey = Object.keys(lsData).find(k => k.includes('::openid profile email offline_access'));
        return tokenKey ? lsData[tokenKey].body.access_token : null;
    } catch (e) {
        return null;
    }
}

async function fetchData(endpoint, token, logger) {
    try {
        const response = await axios.get(`${BASE_URL}${endpoint}`, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept': 'application/json'
            }
        });
        return response.data;
    } catch (e) {
        logger.error(`Failed to fetch ${endpoint}: ${e.message}`);
        return null;
    }
}

async function scrapeNewData(logger) {
    const token = await getAccessToken();
    if (!token) {
        logger.error('No access token found for API scraping.');
        return;
    }

    logger.info('Starting Phase 2 API Scrape...');

    // 1. Market Sentiment
    const sentimentData = await fetchData('/mrkt-ai/mrkt-sentiment', token, logger);
    if (sentimentData && sentimentData.mrkt_ai_index) {
        await db.saveSentiment(sentimentData.mrkt_ai_index, logger);
    }

    // 2. Economic Data Summary
    const economicData = await fetchData('/mrkt-ai/economic-data-summary', token, logger);
    if (economicData) {
        await db.saveEconomicSummary(economicData, logger);
    }

    // 3. Trump Tracker
    const trumpEvents = await fetchData('/calendar/trump-tracker', token, logger);
    if (trumpEvents && Array.isArray(trumpEvents)) {
        await db.saveTrumpEvents(trumpEvents, logger);
    }

    // 4. Trump Volatility
    const volatilityData = await fetchData('/mrkt-ai/trump-volatility', token, logger);
    if (volatilityData) {
        await db.saveTrumpVolatility(volatilityData, logger);
    }

    // 5. ETF Summary
    const etfData = await fetchData('/mrkt-ai/etf-summary', token, logger);
    if (etfData) {
        await db.saveEtfSummary(etfData, logger);
    }

    logger.info('Phase 2 API Scrape completed.');
}

module.exports = { scrapeNewData };
