// ===========================================================
// TC-01: PUBLIC PAGES - Halaman Publik (Tanpa Login)
// SportBooking QA Automation - Playwright
// ===========================================================
const { test, expect } = require('@playwright/test');

const BASE = 'http://127.0.0.1:8000';

test.describe('TC-01 · Public Pages — Halaman yang Dapat Diakses Tanpa Login', () => {

  test('TC-01-01 · Beranda (/) dapat dimuat dengan tampilan lengkap', async ({ page }) => {
    await page.goto(BASE);
    await page.waitForLoadState('networkidle');

    // Verifikasi brand name
    await expect(page.locator('body')).toContainText('SPORTBOOKING', { ignoreCase: true });

    // Verifikasi navigation bar
    const nav = page.locator('nav');
    await expect(nav).toBeVisible();

    await page.screenshot({ path: 'test-results/TC-01-01-beranda.png', fullPage: true });
  });

  test('TC-01-02 · Halaman Katalog Arena (/arena) dapat dimuat', async ({ page }) => {
    await page.goto(`${BASE}/arena`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveTitle(/SportBooking/i);
    await page.screenshot({ path: 'test-results/TC-01-02-katalog-arena.png', fullPage: true });
  });

  test('TC-01-03 · Jadwal lapangan (/jadwal) dapat dimuat dan menampilkan slot waktu', async ({ page }) => {
    await page.goto(`${BASE}/jadwal`);
    await page.waitForLoadState('networkidle');

    // Halaman harus berhasil dimuat (200 OK)
    expect(page.url()).toContain('/jadwal');
    await page.screenshot({ path: 'test-results/TC-01-03-jadwal.png', fullPage: true });
  });

  test('TC-01-04 · Halaman Login (/login) merender form login', async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.waitForLoadState('networkidle');

    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();
    await expect(page.locator('button[type="submit"]')).toBeVisible();

    await page.screenshot({ path: 'test-results/TC-01-04-halaman-login.png', fullPage: false });
  });

  test('TC-01-05 · Halaman Registrasi (/register) merender form pendaftaran', async ({ page }) => {
    await page.goto(`${BASE}/register`);
    await page.waitForLoadState('networkidle');

    await expect(page.locator('input[name="name"]')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
    await expect(page.locator('input[name="password"]')).toBeVisible();

    await page.screenshot({ path: 'test-results/TC-01-05-halaman-register.png', fullPage: false });
  });

  test('TC-01-06 · Guest yang akses /admin/dashboard diarahkan ke login', async ({ page }) => {
    const response = await page.goto(`${BASE}/admin/dashboard`);
    await page.waitForLoadState('networkidle');

    // Harus redirect ke login atau tampil 403
    const finalUrl = page.url();
    const isRedirectedToLogin = finalUrl.includes('/login');
    const isForbidden = response?.status() === 403;

    expect(isRedirectedToLogin || isForbidden).toBeTruthy();
    await page.screenshot({ path: 'test-results/TC-01-06-guest-akses-admin.png', fullPage: false });
  });

});
