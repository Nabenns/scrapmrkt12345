const axios = require('axios');
const fs = require('fs');
const path = require('path');

const BASE_URL = 'https://mrkt-server-a240deff7152.herokuapp.com';
'mrkt-ai/mrkt-sentiment',
    'mrkt-ai/economic-data-summary',
    'calendar/trump-tracker',
    'mrkt-ai/trump-volatility',
    'mrkt-ai/etf-summary'
];

const PREFIXES = ['']; // No prefixes needed, full paths provided

async function getAccessToken() {
    const lsPath = path.join(__dirname, '../storage/local_storage.json');
    if (!fs.existsSync(lsPath)) {
        console.error('Local storage file not found!');
        return null;
    }
    const lsData = JSON.parse(fs.readFileSync(lsPath, 'utf8'));
    const tokenKey = Object.keys(lsData).find(k => k.includes('::openid profile email offline_access'));
    if (!tokenKey) {
        console.error('Token key not found in local storage!');
        return null;
    }
    return lsData[tokenKey].body.access_token;
}

async function probe() {
    const token = await getAccessToken();
    if (!token) return;

    console.log(`Probing ${BASE_URL} with token...`);

    for (const endpoint of ENDPOINTS_TO_TEST) {
        for (const prefix of PREFIXES) {
            const path = `${prefix}/${endpoint}`;
            try {
                const url = `${BASE_URL}${path}`;
                const response = await axios.get(url, {
                    headers: {
                        'Authorization': `Bearer ${token}`,
                        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                        'Accept': 'application/json'
                    },
                    validateStatus: () => true
                });

                if (response.status === 200) {
                    console.log(`[SUCCESS] ${path} - ${response.headers['content-type']}`);
                    const preview = JSON.stringify(response.data).substring(0, 200);
                    console.log(`    Data: ${preview}...`);
                } else {
                    console.log(`[${response.status}] ${path}`);
                }
            } catch (e) {
                console.log(`[ERR] ${path} - ${e.message}`);
                if (e.response) {
                    console.log(`    Status: ${e.response.status}`);
                }
            }
        }
    }
}

probe();
