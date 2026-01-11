require('dotenv').config();
const cron = require('node-cron');
const winston = require('winston');
const { scrapeHeadlines } = require('./scraper');

// Logger setup
const logger = winston.createLogger({
  level: 'info',
  format: winston.format.combine(
    winston.format.timestamp(),
    winston.format.json()
  ),
  transports: [
    new winston.transports.Console(),
    new winston.transports.File({ filename: 'scraper-error.log', level: 'error' }),
    new winston.transports.File({ filename: 'scraper-combined.log' }),
  ],
});

const SCHEDULE = process.env.CRON_SCHEDULE || '*/10 * * * *';

logger.info(`Starting MRKT Scraper Service. Schedule: ${SCHEDULE}`);

const runScraper = async () => {
  logger.info('Starting scrape job...');
  try {
    const count = await scrapeHeadlines(logger);
    logger.info(`Scrape job completed. Inserted ${count} new headlines.`);
  } catch (error) {
    logger.error(`Scrape job failed: ${error.message}`);
    console.error(error);
  }
};

// Run immediately on startup
runScraper();

// Schedule
cron.schedule(SCHEDULE, () => {
  runScraper();
});
