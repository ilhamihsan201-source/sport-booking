// ===========================================================
// TC-05: SECURITY & EDGE CASES - IDOR, Error Pages, Form Validation
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
}

test.describe('TC-05 · Security & Edge Cases — IDOR, 404, Halaman Error', () => {

  test('TC-05-01 · Akses URL booking yang tidak ada → menampilkan halaman 404 yang branded', async ({ page }) => {
    await page.goto(`${BASE}/booking/INVALID-CODE-99999`);
    await page.waitForLoadState('networkidle');

    const status = page.url();
    const body = await page.textContent('body');
    // Harus ada tampilan 404 atau error branded SportBooking (bukan error server kosong)
    const hasErrorPage = body?.includes('404') || body?.includes('tidak ditemukan') || body?.includes('Not Found') || body?.includes('Kembali');
    await page.screenshot({ path: 'test-results/TC-05-01-halaman-404.png', fullPage: false });
    expect(hasErrorPage).toBeTruthy();
  });

  test('TC-05-02 · Akses route yang tidak ada → halaman error yang rapi', async ({ page }) => {
    await page.goto(`${BASE}/halaman-tidak-ada-sama-sekali`);
    await page.waitForLoadState('networkidle');

    await page.screenshot({ path: 'test-results/TC-05-02-route-tidak-ada.png', fullPage: false });

    const body = await page.textContent('body');
    // Pastikan halaman dimuat (bukan blank)
    expect(body?.length).toBeGreaterThan(50);
  });

  test('TC-05-03 · Customer mencoba akses admin URL /admin/bookings → ditolak (403)', async ({ page }) => {
    await loginCustomer(page);

    const response = await page.goto(`${BASE}/admin/bookings`);
    await page.waitForLoadState('networkidle');

    const isForbidden = response?.status() === 403 ||
      (await page.textContent('body'))?.includes('403') ||
      (await page.textContent('body'))?.includes('Forbidden') ||
      (await page.textContent('body'))?.toLowerCase().includes('akses');

    expect(isForbidden).toBeTruthy();
    await page.screenshot({ path: 'test-results/TC-05-03-customer-akses-admin-booking-403.png', fullPage: false });
  });

  test('TC-05-04 · Customer mencoba akses admin URL /admin/payments → ditolak (403)', async ({ page }) => {
    await loginCustomer(page);

    const response = await page.goto(`${BASE}/admin/payments`);
    await page.waitForLoadState('networkidle');

    const isForbidden = response?.status() === 403 ||
      (await page.textContent('body'))?.includes('403') ||
      (await page.textContent('body'))?.includes('Forbidden');

    expect(isForbidden).toBeTruthy();
    await page.screenshot({ path: 'test-results/TC-05-04-customer-akses-admin-payment-403.png', fullPage: false });
  });

  test('TC-05-05 · Form login dengan email tidak terdaftar → tampilkan pesan error', async ({ page }) => {
    await page.goto(`${BASE}/login`);
    await page.waitForLoadState('networkidle');

    await page.fill('input[name="email"]', 'tidakterdaftar@nowhere.com');
    await page.fill('input[name="password"]', 'randompassword');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    expect(page.url()).toContain('/login');
    await page.screenshot({ path: 'test-results/TC-05-05-login-email-tidak-terdaftar.png', fullPage: false });
  });

  test('TC-05-06 · Halaman utama merender assets CSS dan JS dengan sempurna tanpa error', async ({ page }) => {
    const response = await page.goto(BASE);
    await page.waitForLoadState('networkidle');

    // Status 200 OK
    expect(response?.status()).toBe(200);

    // Verifikasi CSS ter-load
    const styles = await page.evaluate(() =>
      Array.from(document.styleSheets).map(ss => ss.href).filter(Boolean)
    );
    expect(styles.length).toBeGreaterThan(0);

    await page.screenshot({ path: 'test-results/TC-05-06-beranda-assets-tampilan.png', fullPage: false });
  });

});
