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

module.exports = { saveHeadlines };
