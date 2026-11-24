// @ts-check
const { defineConfig, devices } = require('@playwright/test');

module.exports = defineConfig({
  testDir: './tests',
  fullyParallel: false,
  forbidOnly: false,
  retries: 0,
  workers: 1,
  timeout: 30000,
  use: {
    baseURL: 'http://127.0.0.1:8000',
    headless: true,
    viewport: { width: 1280, height: 800 },
    screenshot: 'on',
    video: 'off',
    trace: 'off',
    ignoreHTTPSErrors: true,
  },
  reporter: [
    ['html', { outputFolder: 'playwright-report', open: 'never' }],
    ['json', { outputFile: 'test-results/results.json' }],
    ['list'],
  ],
  outputDir: 'test-results/attachments',
  projects: [
    {
      name: 'SportBooking QA - Chromium',
      use: { ...devices['Desktop Chrome'] },
    },
  ],
});
