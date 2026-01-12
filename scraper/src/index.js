require('dotenv').config();
const cron = require('node-cron');
const winston = require('winston');
const { scrapeHeadlines } = require('./scraper');
const { scrapeNewData } = require('./apiScraper');

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

const fs = require('fs');
const path = require('path');

const SCHEDULE = process.env.CRON_SCHEDULE || '*/10 * * * *';
const TRIGGER_FILE = path.join(__dirname, '../storage/trigger.txt');

logger.info(`Starting MRKT Scraper Service. Schedule: ${SCHEDULE}`);

const runScraper = async () => {
  logger.info('Starting scrape job...');
  try {
    // Phase 1: Headlines
    const count = await scrapeHeadlines(logger);
    logger.info(`Headlines scraped: ${count}`);

    // Phase 2: New Data Sources
    await scrapeNewData(logger);

    logger.info('Scrape job completed successfully.');
  } catch (error) {
    logger.error(`Scrape job failed: ${error.message}`);
    console.error(error);
  }
};

// Run immediately on startup
runScraper();

// Schedule
cron.schedule(SCHEDULE, () => {
  logger.info('Running scheduled scrape job...');
  runScraper();
});

// Watch for trigger file (Immediate Run)
setInterval(() => {
  if (fs.existsSync(TRIGGER_FILE)) {
    logger.info('Trigger file detected! Running scraper immediately...');
    try {
      fs.unlinkSync(TRIGGER_FILE); // Delete trigger file
      runScraper();
    } catch (err) {
      logger.error(`Failed to process trigger file: ${err.message}`);
    }
  }
}, 5000); // Check every 5 seconds

