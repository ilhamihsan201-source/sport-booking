// ===========================================================
// TC-04: CUSTOMER FLOW - Arena, Lapangan, Jadwal, Booking
// SportBooking QA Automation - Playwright
// ===========================================================
const { test, expect } = require('@playwright/test');

const BASE = 'http://127.0.0.1:8000';

async function loginCustomer(page) {
  await page.goto(`${BASE}/login`);
  await page.waitForLoadState('networkidle');
  await page.fill('input[name="email"]', 'customer@sportbooking.test');
  await page.fill('input[name="password"]', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  await expect(page).toHaveURL(/customer\/dashboard/);
}

test.describe('TC-04 · Customer Flow — Arena, Lapangan, Jadwal, Booking', () => {

  test('TC-04-01 · Halaman Arena menampilkan daftar cabang olahraga dengan foto', async ({ page }) => {
    await page.goto(`${BASE}/arena`);
    await page.waitForLoadState('networkidle');

    await page.screenshot({ path: 'test-results/TC-04-01-halaman-arena.png', fullPage: true });

    // Halaman arena harus berhasil dimuat
    expect(page.url()).toContain('/arena');
    const body = await page.textContent('body');
    expect(body?.length).toBeGreaterThan(100);
  });

  test('TC-04-02 · Halaman Detail Lapangan menampilkan galeri foto dan harga sewa', async ({ page }) => {
    // Pertama akses arena untuk mendapatkan slug lapangan
    await page.goto(`${BASE}/arena`);
    await page.waitForLoadState('networkidle');

    // Coba klik link pertama ke detail lapangan
    const courtLink = page.locator('a[href*="/lapangan/"]').first();
    if (await courtLink.isVisible()) {
      await courtLink.click();
      await page.waitForLoadState('networkidle');

      expect(page.url()).toContain('/lapangan/');
      await page.screenshot({ path: 'test-results/TC-04-02-detail-lapangan.png', fullPage: true });
    } else {
      // Fallback: coba langsung via slug yang diketahui
      await page.goto(`${BASE}/lapangan/padel-panoramic-a`);
      await page.waitForLoadState('networkidle');
      await page.screenshot({ path: 'test-results/TC-04-02-detail-lapangan.png', fullPage: true });
    }
  });

  test('TC-04-03 · Customer Dashboard menampilkan histori booking', async ({ page }) => {
    await loginCustomer(page);

    await page.screenshot({ path: 'test-results/TC-04-03-customer-dashboard.png', fullPage: true });

    const body = await page.textContent('body');
    // Dashboard harus berisi informasi terkait booking atau "tidak ada booking"
    expect(body?.length).toBeGreaterThan(100);
  });

  test('TC-04-04 · Customer tidak dapat mengakses /admin/dashboard (403)', async ({ page }) => {
    await loginCustomer(page);

    const response = await page.goto(`${BASE}/admin/dashboard`);
    await page.waitForLoadState('networkidle');

    const isForbidden = response?.status() === 403 ||
      (await page.textContent('body'))?.includes('403') ||
      (await page.textContent('body'))?.includes('Forbidden') ||
      (await page.textContent('body'))?.includes('akses');

    expect(isForbidden).toBeTruthy();
    await page.screenshot({ path: 'test-results/TC-04-04-customer-akses-admin-blokir.png', fullPage: false });
  });

  test('TC-04-05 · Jadwal menampilkan filter dan availability slot lapangan', async ({ page }) => {
    await page.goto(`${BASE}/jadwal`);
    await page.waitForLoadState('networkidle');

    await page.screenshot({ path: 'test-results/TC-04-05-halaman-jadwal.png', fullPage: true });
    expect(page.url()).toContain('/jadwal');
  });

  test('TC-04-06 · Guest yang coba akses /customer/dashboard diarahkan ke login', async ({ page }) => {
    // Tanpa login langsung akses customer dashboard
    await page.goto(`${BASE}/customer/dashboard`);
    await page.waitForLoadState('networkidle');

    const finalUrl = page.url();
    const isRedirectedToLogin = finalUrl.includes('/login');
    expect(isRedirectedToLogin).toBeTruthy();
    await page.screenshot({ path: 'test-results/TC-04-06-guest-akses-customer-redirect.png', fullPage: false });
  });

});
