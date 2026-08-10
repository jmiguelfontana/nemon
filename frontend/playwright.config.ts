import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  
  use: {
    trace: 'on-first-retry',
  },

  projects: [
    {
      name: 'local',
      use: { 
        ...devices['Desktop Chrome'],
        baseURL: 'http://localhost:5173',
      },
    },
    {
      name: 'production',
      use: { 
        ...devices['Desktop Chrome'],
        baseURL: 'https://nemon.robbytherobot.tech',
      },
    },
  ],
});
