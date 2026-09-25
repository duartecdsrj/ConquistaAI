import { defineConfig, devices } from '@playwright/test'

const baseURL = process.env.E2E_BASE_URL ?? 'http://nginx'

export default defineConfig({
  testDir: './tests/visual',
  outputDir: 'test-results',
  fullyParallel: false,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
  },
  projects: [
    { name: 'desktop', use: { ...devices['Desktop Chrome'], browserName: 'chromium', launchOptions: { executablePath: '/usr/bin/chromium-browser' }, viewport: { width: 1440, height: 900 } } },
    { name: 'mobile', use: { ...devices['iPhone 13'], browserName: 'chromium', launchOptions: { executablePath: '/usr/bin/chromium-browser' }, viewport: { width: 390, height: 844 } } },
  ],
})
