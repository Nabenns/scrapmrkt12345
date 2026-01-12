const { Pool } = require('pg');

const pool = new Pool({
    user: process.env.DB_USERNAME,
    host: process.env.DB_HOST,
    database: process.env.DB_DATABASE,
    password: process.env.DB_PASSWORD,
    port: process.env.DB_PORT,
});

async function saveHeadlines(headlines, logger) {
    if (headlines.length === 0) return 0;

    const client = await pool.connect();
    let inserted = 0;

    try {
        await client.query('BEGIN');

        for (const item of headlines) {
            const query = `
        INSERT INTO market_headlines (title, category, published_at, source, hash, created_at)
        VALUES ($1, $2, $3, $4, $5, NOW())
        ON CONFLICT (hash) DO NOTHING
      `;
            const values = [item.title, item.category, item.published_at, item.source, item.hash];
            const res = await client.query(query, values);
            if (res.rowCount > 0) inserted++;
        }

        await client.query('COMMIT');
    } catch (e) {
        await client.query('ROLLBACK');
        logger.error(`Database insert failed: ${e.message}`);
        throw e;
    } finally {
        client.release();
    }

    return inserted;
}

async function saveSentiment(data, logger) {
    if (!data) return;
    const client = await pool.connect();
    try {
        const query = `
            INSERT INTO market_sentiments (value, label, risk_regime, reasoning, created_at)
            VALUES ($1, $2, $3, $4, NOW())
        `;
        await client.query(query, [data.value, data.label, data.risk_regime, data.reasoning]);
        logger.info('Saved market sentiment.');
    } catch (e) {
        logger.error(`Failed to save sentiment: ${e.message}`);
    } finally {
        client.release();
    }
}

async function saveEconomicSummary(content, logger) {
    if (!content) return;
    const client = await pool.connect();
    try {
        const query = `INSERT INTO economic_summaries (content, created_at) VALUES ($1, NOW())`;
        await client.query(query, [JSON.stringify(content)]);
        logger.info('Saved economic summary.');
    } catch (e) {
        logger.error(`Failed to save economic summary: ${e.message}`);
    } finally {
        client.release();
    }
}

async function saveTrumpEvents(events, logger) {
    if (!events || events.length === 0) return;
    const client = await pool.connect();
    let inserted = 0;
    try {
        await client.query('BEGIN');
        for (const event of events) {
            // Create a hash to prevent duplicates (date + time + details)
            const hashInput = `${event.date}${event.time}${event.details}`;
            const hash = require('crypto').createHash('sha256').update(hashInput).digest('hex');

            const query = `
                INSERT INTO trump_events (date, time, type, details, location, hash, created_at)
                VALUES ($1, $2, $3, $4, $5, $6, NOW())
                ON CONFLICT (hash) DO NOTHING
            `;
            const res = await client.query(query, [event.date, event.time, event.type, event.details, event.location, hash]);
            if (res.rowCount > 0) inserted++;
        }
        await client.query('COMMIT');
        if (inserted > 0) logger.info(`Saved ${inserted} new Trump events.`);
    } catch (e) {
        await client.query('ROLLBACK');
        logger.error(`Failed to save Trump events: ${e.message}`);
    } finally {
        client.release();
    }
}

async function saveTrumpVolatility(data, logger) {
    if (!data) return;
    const client = await pool.connect();
    try {
        const query = `
            INSERT INTO trump_volatility (score, explanation, created_at)
            VALUES ($1, $2, NOW())
        `;
        await client.query(query, [data.volatilityScore, data.explanation]);
        logger.info('Saved Trump volatility score.');
    } catch (e) {
        logger.error(`Failed to save Trump volatility: ${e.message}`);
    } finally {
        client.release();
    }
}

async function saveEtfSummary(content, logger) {
    if (!content) return;
    const client = await pool.connect();
    try {
        const query = `INSERT INTO etf_summaries (content, created_at) VALUES ($1, NOW())`;
        await client.query(query, [JSON.stringify(content)]);
        logger.info('Saved ETF summary.');
    } catch (e) {
        logger.error(`Failed to save ETF summary: ${e.message}`);
    } finally {
        client.release();
    }
}

module.exports = {
    saveHeadlines,
    saveSentiment,
    saveEconomicSummary,
    saveTrumpEvents,
    saveTrumpVolatility,
    saveEtfSummary
};
