const axios = require('axios');
const fs = require('fs');
const path = require('path');

const API_URL = 'https://mrkt-server-a240deff7152.herokuapp.com/equities/headlines?page=1&limit=5';

async function getAccessToken() {
    const lsPath = path.join(__dirname, '../storage/local_storage.json');
    const lsData = JSON.parse(fs.readFileSync(lsPath, 'utf8'));
    const tokenKey = Object.keys(lsData).find(k => k.includes('::openid profile email offline_access'));
    return lsData[tokenKey].body.access_token;
}

async function run() {
    try {
        const token = await getAccessToken();
        console.log('Token:', token.substring(0, 20) + '...');

        const response = await axios.get(API_URL, {
            headers: {
                'Authorization': `Bearer ${token}`,
                'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
                'Accept': 'application/json'
            }
        });

        console.log('Response Status:', response.status);
        console.log('Response Data:', JSON.stringify(response.data, null, 2));
    } catch (e) {
        console.error('Error:', e.message);
        if (e.response) {
            console.log('Response Data:', JSON.stringify(e.response.data, null, 2));
        }
    }
}

run();
