const crypto = require('crypto');

function parseHeadlines(rawData) {
    return rawData.map(item => {
        // Handle API response structure
        // item: { text: "...", date: "...", ... }

        let title = item.text || item.title || item.headline || 'No Title';
        const publishedAt = item.date || item.created_at || item.published_at || new Date().toISOString();
        const category = item.category || 'General';
        const source = item.source || 'MRKT';

        // Clean title: Remove "[Jan 11 2026, 10:27:58 EST]: " prefix if present
        // Regex looks for [Date, Time Timezone]:
        if (title) {
            title = title.replace(/^\[.*?\]:\s*/, '');
        }

        // Create hash for deduplication
        const hash = crypto.createHash('sha256')
            .update(`${title}-${publishedAt}`)
            .digest('hex');

        return {
            title,
            category,
            published_at: publishedAt,
            source,
            hash
        };
    });
}

module.exports = { parseHeadlines };
