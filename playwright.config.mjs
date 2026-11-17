import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests',
  timeout: 60_000,
  workers: 1,
  use: {
    ...devices['Desktop Chrome'],
    baseURL: process.env.WPCS_TEST_URL || 'http://localhost:8888',
    trace: 'retain-on-failure',
  },
});
