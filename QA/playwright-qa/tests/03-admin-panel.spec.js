// ===========================================================
// TC-03: ADMIN PANEL - Manajemen Lapangan, Venue, Fasilitas
// SportBooking QA Automation - Playwright
// ===========================================================
const { test, expect } = require('@playwright/test');

const BASE = 'http://127.0.0.1:8000';

async function loginAdmin(page) {
  await page.goto(`${BASE}/login`);
  await page.waitForLoadState('networkidle');
  await page.fill('input[name="email"]', 'admin@sportbooking.test');
  await page.fill('input[name="password"]', 'password123');
  await page.click('button[type="submit"]');
  await page.waitForLoadState('networkidle');
  await expect(page).toHaveURL(/admin\/dashboard/);
}

test.describe('TC-03 · Admin Panel — Dashboard, Lapangan, Venue, Fasilitas', () => {

  test.beforeEach(async ({ page }) => {
    await loginAdmin(page);
  });

  test('TC-03-01 · Admin Dashboard menampilkan statistik booking dan omset', async ({ page }) => {
    await page.screenshot({ path: 'test-results/TC-03-01-admin-dashboard-statistik.png', fullPage: true });

    // Dashboard harus memuat dengan sukses
    await expect(page).toHaveURL(/admin\/dashboard/);
    const body = await page.textContent('body');
    // Dashboard harus ada teks terkait booking/statistik
    const hasStats = body?.toLowerCase().includes('booking') || body?.toLowerCase().includes('lapangan') || body?.toLowerCase().includes('pembayaran');
    expect(hasStats).toBeTruthy();
  });

  test('TC-03-02 · Admin dapat melihat daftar lapangan di /admin/courts', async ({ page }) => {
    await page.goto(`${BASE}/admin/courts`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/courts/);
    await page.screenshot({ path: 'test-results/TC-03-02-admin-daftar-lapangan.png', fullPage: true });

    const body = await page.textContent('body');
    const hasCourt = body?.toLowerCase().includes('lapangan') || body?.toLowerCase().includes('court') || body?.toLowerCase().includes('padel');
    expect(hasCourt).toBeTruthy();
  });

  test('TC-03-03 · Form tambah lapangan baru dapat ditampilkan (/admin/courts/create)', async ({ page }) => {
    await page.goto(`${BASE}/admin/courts/create`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/courts\/create/);
    // Form harus berisi field input (pakai first() karena ada navbar form logout juga)
    await expect(page.locator('form').last()).toBeVisible();
    await page.screenshot({ path: 'test-results/TC-03-03-admin-form-tambah-lapangan.png', fullPage: true });
  });

  test('TC-03-04 · Admin dapat melihat daftar venue di /admin/venues', async ({ page }) => {
    await page.goto(`${BASE}/admin/venues`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/venues/);
    await page.screenshot({ path: 'test-results/TC-03-04-admin-daftar-venue.png', fullPage: true });
  });

  test('TC-03-05 · Admin dapat melihat daftar fasilitas di /admin/facilities', async ({ page }) => {
    await page.goto(`${BASE}/admin/facilities`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/facilities/);
    await page.screenshot({ path: 'test-results/TC-03-05-admin-daftar-fasilitas.png', fullPage: true });
  });

  test('TC-03-06 · Admin dapat melihat daftar booking di /admin/bookings', async ({ page }) => {
    await page.goto(`${BASE}/admin/bookings`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/bookings/);
    await page.screenshot({ path: 'test-results/TC-03-06-admin-daftar-booking.png', fullPage: true });
  });

  test('TC-03-07 · Admin dapat melihat daftar pembayaran di /admin/payments', async ({ page }) => {
    await page.goto(`${BASE}/admin/payments`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/payments/);
    await page.screenshot({ path: 'test-results/TC-03-07-admin-daftar-pembayaran.png', fullPage: true });
  });

  test('TC-03-08 · Admin dapat melihat daftar pelanggan di /admin/customers', async ({ page }) => {
    await page.goto(`${BASE}/admin/customers`);
    await page.waitForLoadState('networkidle');

    await expect(page).toHaveURL(/admin\/customers/);
    await page.screenshot({ path: 'test-results/TC-03-08-admin-daftar-pelanggan.png', fullPage: true });
  });

});
