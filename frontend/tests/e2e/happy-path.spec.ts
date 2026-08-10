import { test, expect } from '@playwright/test';

test.describe('Flujo E2E - Nemon Energy Calculator', () => {
  test('La página carga correctamente y muestra el estado de la API', async ({ page }) => {
    // Al usar baseURL en playwright.config.ts, '/' nos lleva a localhost o a tu dominio en producción
    await page.goto('/');

    // Comprobamos que el título de la pestaña sea correcto (ajusta según tu index.html)
    await expect(page).toHaveTitle(/Nemon/i);

    // Verificamos que se muestre el header
    const header = page.locator('header');
    await expect(header).toBeVisible();

    // Verificamos que la API conecte (si la API de producción/local está funcionando, esto debería ponerse en verde)
    // El texto cambiará de "Conectando..." a "API Conectada"
    const apiStatus = page.locator('text=API Conectada');
    // Esperamos a que la petición health check termine y se ponga verde
    await expect(apiStatus).toBeVisible({ timeout: 10000 });
  });
});
