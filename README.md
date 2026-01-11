# MRKT Headline Scraper & Internal News API

This project scrapes market news headlines from `app.mrktedge.ai` and serves them via an internal REST API.

## Architecture

- **Scraper**: Node.js + Playwright (Headless Browser). Runs on a schedule (default: every 10 mins).
- **API**: Laravel 10 (PHP 8.4). Serves headlines via JSON API.
- **Database**: PostgreSQL. Stores headlines with deduplication.
- **Cache**: Redis. Caches API responses.
- **Proxy**: Nginx. Handles HTTP requests to the API.

## Prerequisites

- Docker & Docker Compose
- `scraper/storage/mrkt.json`: **Required** for authentication. Export this from your browser after logging into MRKT.

## Setup & Run

1. **Clone & Configure**
   ```bash
   git clone <repo>
   cd mrkt-scraper
   cp .env.example .env
   ```

2. **Add Cookie File**
   Place your exported `mrkt.json` cookie file into `scraper/storage/`.

3. **Start Services**
   ```bash
   docker-compose up -d --build
   ```

4. **Verify**
   - API: `http://localhost:8000/api/headlines`
   - Scraper Logs: `docker-compose logs -f scraper`

## Deployment

To deploy this project to a VPS, please refer to the [Deployment Guide](DEPLOYMENT.md).

## API Documentation

### Get Headlines
`GET /api/headlines`

**Parameters:**
- `limit` (int, default: 10): Number of items to return.
- `category` (string, optional): Filter by category.
- `page` (int, default: 1): Pagination page.

**Response:**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Bitcoin breaks $100k",
            "category": "Crypto",
            "published_at": "2026-01-05T10:00:00.000000Z",
            "source": "MRKT",
            "hash": "..."
# MRKT Headline Scraper & Internal News API

This project scrapes market news headlines from `app.mrktedge.ai` and serves them via an internal REST API.

## Architecture

- **Scraper**: Node.js + Playwright (Headless Browser). Runs on a schedule (default: every 10 mins).
- **API**: Laravel 10 (PHP 8.4). Serves headlines via JSON API.
- **Database**: PostgreSQL. Stores headlines with deduplication.
- **Cache**: Redis. Caches API responses.
- **Proxy**: Nginx. Handles HTTP requests to the API.

## Prerequisites

- Docker & Docker Compose
- `scraper/storage/mrkt.json`: **Required** for authentication. Export this from your browser after logging into MRKT.

## Setup & Run

1. **Clone & Configure**
   ```bash
   git clone <repo>
   cd mrkt-scraper
   cp .env.example .env
   ```

2. **Add Cookie File**
   Place your exported `mrkt.json` cookie file into `scraper/storage/`.

3. **Start Services**
   ```bash
   docker-compose up -d --build
   ```

4. **Verify**
   - API: `http://localhost:8000/api/headlines`
   - Scraper Logs: `docker-compose logs -f scraper`

## API Documentation

### Get Headlines
`GET /api/headlines`

**Parameters:**
- `limit` (int, default: 10): Number of items to return.
- `category` (string, optional): Filter by category.
- `page` (int, default: 1): Pagination page.

**Response:**
```json
{
    "current_page": 1,
    "data": [
        {
            "id": 1,
            "title": "Bitcoin breaks $100k",
            "category": "Crypto",
            "published_at": "2026-01-05T10:00:00.000000Z",
            "source": "MRKT",
            "hash": "..."
        }
    ],
    ...
}
```

## Authentication & Token Updates

The scraper now uses direct API access for reliability. It requires a valid Bearer Token from `app.mrktedge.ai`.

### How to Update the Token
If the scraper starts failing with 401/403 errors, the token has likely expired.

1.  Login to [app.mrktedge.ai](https://app.mrktedge.ai).
2.  Open Developer Tools (F12) -> Application -> Local Storage.
3.  Find the key that looks like `@@auth0spajs@@::...::@@user@@`.
4.  Copy the full JSON value.
5.  Paste it into `scraper/storage/local_storage.json` (replacing the existing content).
    *   *Note: Ensure the file contains the full JSON object with the `body.access_token` field.*
6.  Restart the scraper:
    ```bash
    docker-compose restart scraper
    ```

## Troubleshooting

### Scraper Issues
- **Logs**: Check logs with `docker-compose logs -f scraper`.
- **Token**: If logs show "Invalid API response" or 401 errors, update the token as described above.

### API Issues
- **No Data**: Ensure the scraper has run at least once. Check DB with:
  ```bash
  docker-compose exec db psql -U postgres -d mrkt_news -c "SELECT count(*) FROM market_headlines;"
  ```
- **Cache**: The API caches results for 60 seconds. Changes might not be immediate.
- **Permissions**: Ensure permissions are set: `docker-compose exec api chmod -R 777 storage`.
