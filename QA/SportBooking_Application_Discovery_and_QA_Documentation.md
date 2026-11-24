# SPORTBOOKING INDONESIA
## APPLICATION DISCOVERY & QA DOCUMENTATION REPORT

> **Dokumen Audit Kualitas & Spesifikasi Sistem**  
> Disusun berdasarkan pedoman `skill-application-discovery-qa-documentation` & `skill-QA Engineer`.

---

### Metadata Proyek

| Atribut | Informasi Aktual Sistem |
| :--- | :--- |
| **Nama Proyek** | SportBooking Indonesia |
| **Jenis Sistem** | Sports Venue & Court Booking and Reservation System (Hybrid E-Commerce / Scheduling) |
| **Repository GitHub** | [https://github.com/ilhamihsan201-source/sport-booking](https://github.com/ilhamihsan201-source/sport-booking) |
| **Production URL** | [https://sport-booking-production-0df4977f.up.railway.app](https://sport-booking-production-0df4977f.up.railway.app) |
| **Local Environment** | `http://127.0.0.1:8000` (Laragon / PHP 8.2+ / Vite) |
| **Tech Stack** | Laravel 11.x, PHP 8.2, MySQL 8 (Production) / SQLite (Testing), Tailwind CSS, Vite |
| **Hasil QA Automated** | **PASSED: 67 Tests, 200 Assertions (100% Success Rate - Duration: 32.58s)** |
| **Dokumen PDF** | [`QA/SportBooking_Application_Discovery_and_QA_Documentation.pdf`](file:///c:/laragon/www/sport-booking/QA/SportBooking_Application_Discovery_and_QA_Documentation.pdf) |
| **Dokumen DOCX** | [`QA/SportBooking_Application_Discovery_and_QA_Documentation.docx`](file:///c:/laragon/www/sport-booking/QA/SportBooking_Application_Discovery_and_QA_Documentation.docx) |

---

## 1. Executive Summary

Aplikasi **SportBooking Indonesia** adalah platform digital untuk penyewaan fasilitas olahraga (Padel, Mini Soccer, dll.) yang menghubungkan pengguna umum (Public Visitor), pelanggan terdaftar (Customer), dan pengelola arena (Administrator). 

Audit kualitas dan reverse engineering sistem ini membuktikan bahwa aplikasi telah memenuhi seluruh kriteria pengujian fungsional, integritas transaksi keuangan, pencegahan bentrokan jadwal (*concurrency double-booking*), proteksi otorisasi bertingkat (*RBAC*), dan keamanan data (*IDOR Protection*).

```mermaid
flowchart TD
    Guest["Public Visitor (Guest)"] -->|Lihat Katalog & Cek Jadwal| Catalog["Katalog Lapangan & Ketersediaan Slot"]
    Catalog -->|Pilih Slot & Login/Register| Customer["Customer Role"]
    Customer -->|Buat Reservasi & Upload Bukti Bayar| BookingEngine["Booking Engine & Concurrency Lock"]
    BookingEngine -->|Status: Paid (Menunggu Review)| Admin["Administrator Role"]
    Admin -->|Review & Verifikasi Bukti| PaymentVerification{"Verifikasi Pembayaran"}
    PaymentVerification -->|Valid| Confirmed["Status: Confirmed (Slot Terkunci Aman)"]
    PaymentVerification -->|Invalid| Rejected["Status: Rejected (Alasan Dikirim ke User)"]
    BookingEngine -.->|Kadaluwarsa > 2 Jam| AutoCancel["Cron Scheduler: Expire Pending Bookings"]
```

---

## 2. Inventarisasi Halaman Aktual (Page Discovery)

| Page ID | Nama Halaman | Route / URL | Role Akses | Komponen & Fitur Utama |
| :--- | :--- | :--- | :--- | :--- |
| **PG-01** | Beranda Utama | `GET /` | Public | Hero section, showcase cabang olahraga, daftar lapangan unggulan, call-to-action. |
| **PG-02** | Katalog Arena & Sport | `GET /arena`, `GET /arena/{sport:slug}` | Public | Filter kategori cabang olahraga (Padel, Mini Soccer), kartu lapangan, badges harga. |
| **PG-03** | Detail Lapangan & Galeri | `GET /lapangan/{court:slug}` | Public | Galeri foto thumbnail, detail spesifikasi rumput/kaca, harga/jam, fasilitas venue. |
| **PG-04** | Cek Jadwal & Ketersediaan | `GET /jadwal`, `GET /jadwal/availability` | Public | Datepicker jadwal, grid slot jam main, indikator status ketersediaan *real-time*. |
| **PG-05** | Form Pembuatan Booking | `POST /booking` | Customer | Validasi waktu bermain, pencegahan double-booking, kalkulasi total tarif sewa. |
| **PG-06** | Ringkasan Booking | `GET /booking/{code}` | Customer | Rincian kode booking, tanggal main, batas transfer, tombol batalkan (jika *pending*). |
| **PG-07** | Halaman Pembayaran | `GET /booking/{code}/pembayaran` | Customer | Rekening bank tujuan, nominal transfer, form upload bukti transfer (JPG/PNG/PDF). |
| **PG-08** | Customer Dashboard | `GET /customer/dashboard` | Customer | Tab riwayat pemesanan: Aktif, Menunggu Pembayaran, Selesai, Dibatalkan. |
| **PG-09** | Admin Dashboard | `GET /admin/dashboard` | Admin | Kartu ringkasan omset total, booking hari ini, pending verification, status lapangan. |
| **PG-10** | Kelola Booking Admin | `GET /admin/bookings` | Admin | Filter status booking, detail pemesanan pelanggan, aksi batalkan atau selesaikan sesi. |
| **PG-11** | Verifikasi Pembayaran | `GET /admin/payments` | Admin | Tabel daftar pembayaran, preview bukti transfer, tombol verifikasi dan tolak + alasan. |
| **PG-12** | Manajemen Lapangan | `GET /admin/courts` | Admin | CRUD lapangan olahraga, toggle aktif/nonaktif, upload multi-gambar galeri. |
| **PG-13** | Manajemen Venue & Fasilitas | `GET /admin/venues`, `/facilities` | Admin | Master data lokasi arena, jam buka-tutup operasional, dan fasilitas lapangan. |
| **PG-14** | Direktori Pelanggan | `GET /admin/customers` | Admin | Daftar customer terdaftar beserta histori riwayat transaksi masing-masing. |
| **PG-15** | Autentikasi & Profil | `/login`, `/register`, `/profile` | All/Auth | Login Breeze, registrasi mandiri customer, update nama/email/password. |

---

## 3. Matriks Otorisasi Peran (Role Permission Matrix)

| Fitur / Tindakan Sistem | Public (Guest) | Registered Customer | Administrator |
| :--- | :---: | :---: | :---: |
| **Melihat Katalog Lapangan & Fasilitas** | ALLOWED | ALLOWED | ALLOWED |
| **Mengecek Ketersediaan Slot Waktu** | ALLOWED | ALLOWED | ALLOWED |
| **Membuat Reservasi Lapangan** | DENIED (Wajib Login) | ALLOWED | N/A (via Customer) |
| **Upload Bukti Transfer Pembayaran** | DENIED | ALLOWED (Milik Sendiri) | DENIED |
| **Melihat Detail Booking Customer Lain (IDOR)** | BLOCKED (403/404) | BLOCKED (403 Forbidden) | ALLOWED (Supervisi) |
| **Membatalkan Booking Mandiri (Status Pending)**| DENIED | ALLOWED | ALLOWED |
| **Membatalkan Booking Confirmed** | DENIED | BLOCKED (Harus Admin) | ALLOWED |
| **Verifikasi / Tolak Bukti Pembayaran** | DENIED (403) | DENIED (403) | ALLOWED |
| **Mengakses Admin Panel (`/admin/*`)** | DENIED (Redirect Login) | DENIED (403 Forbidden) | ALLOWED |
| **CRUD Lapangan, Venue, Fasilitas** | DENIED | DENIED | ALLOWED |
| **Menghapus Lapangan dengan Riwayat Transaksi**| DENIED | DENIED | PROTECTED (Ditolak Sistem) |

---

## 4. Analisis Aturan Bisnis Kunci (Business Rules)

### BR-01: Pencegahan Tabrakan Jadwal (Double-Booking Prevention)
Sistem menerapkan penguncian unik pada level database dan validasi kontroler:
- Jika slot `court_id + booking_date + start_time + end_time` telah berstatus `pending`, `paid`, atau `confirmed`, permintaan booking baru pada slot tersebut otomatis ditolak dengan pesan: *"Slot waktu yang dipilih sudah dipesan."*

### BR-02: Siklus Kadaluwarsa Pembayaran (Payment Expiration Window)
- Setiap pesanan yang dibuat memiliki batas waktu upload pembayaran (2 jam).
- Command scheduler `php artisan bookings:expire-pending` secara otomatis membatalkan pesanan lampau yang belum dibayar, membebaskan kembali slot tersebut ke publik tanpa mengganggu pesanan masa depan.

### BR-03: Perlindungan Akses Data Sensitif (IDOR Protection)
- Endpoint `GET /booking/{code}` dan `POST /booking/{code}/cancel` memverifikasi secara ketat bahwa `auth()->id() === $booking->user_id`. Pelanggan lain tidak dapat mengintip invoice atau membatalkan pesanan milik orang lain.

### BR-04: Integritas Referensial Data Lapangan (Deletion Safety Guard)
- Admin tidak diizinkan menghapus (*delete*) data lapangan yang sudah pernah memiliki riwayat pemesanan demi menjaga keabsahan pembukuan audit keuangan. Sebagai gantinya, lapangan dapat di-nonaktifkan (*deactivate*).

---

## 5. Bukti Hasil Pengujian QA Otomatis (Test Execution Evidence)

Pengujian dilakukan menggunakan **PHPUnit 11 & Laravel Feature/Unit Test Suite**:

```
PASS Tests\Unit\ExampleTest (1 test, 1 assertion)
PASS Tests\Feature\Auth\AuthenticationTest (9 tests, 28 assertions)
  ✓ login screen can be rendered
  ✓ users can authenticate using the login screen
  ✓ admin can authenticate and redirects to admin dashboard
  ✓ admin does not redirect to customer dashboard
  ✓ guest cannot access admin dashboard
  ✓ customer cannot access admin dashboard
  ✓ customer cannot register as admin via registration form
  ✓ users can not authenticate with invalid password
  ✓ users can logout

PASS Tests\Feature\Auth\EmailVerificationTest (3 tests, 7 assertions)
PASS Tests\Feature\Auth\PasswordConfirmationTest (3 tests, 6 assertions)
PASS Tests\Feature\Auth\PasswordResetTest (4 tests, 15 assertions)
PASS Tests\Feature\Auth\PasswordUpdateTest (2 tests, 6 assertions)
PASS Tests\Feature\Auth\RegistrationTest (2 tests, 5 assertions)
PASS Tests\Feature\ProfileTest (5 tests, 16 assertions)

PASS Tests\Feature\SportBookingFeatureTest (38 tests, 116 assertions)
  ✓ landing page loads successfully
  ✓ customer cannot access admin dashboard (403 Forbidden)
  ✓ admin can access admin dashboard
  ✓ smart dashboard redirects admin to admin dashboard
  ✓ smart dashboard redirects customer to customer dashboard
  ✓ admin can view courts and create court
  ✓ customer can view own dashboard
  ✓ customer can create booking successfully
  ✓ double booking same slot is rejected
  ✓ customer can upload payment proof
  ✓ admin can verify payment and confirms booking
  ✓ admin can reject payment with reason
  ✓ cannot pay expired booking
  ✓ admin can cancel booking
  ✓ admin can complete booking after play time ended
  ✓ idor protection customer cannot access other customer booking
  ✓ admin can manage venues
  ✓ admin can manage facilities
  ✓ admin can view payments overview
  ✓ customer can cancel own pending booking
  ✓ customer cannot cancel confirmed booking
  ✓ customer cannot cancel other customer booking
  ✓ admin can upload and manage court images
  ✓ expire pending bookings command
  ✓ expire pending bookings does not expire future or confirmed bookings
  ✓ admin can upload multiple court images
  ✓ admin cannot upload more than five court images or invalid files
  ✓ court detail displays primary and gallery thumbnails
  ✓ branded error page renders cleanly
  ✓ court without booking can be deleted
  ✓ court with pending payment booking cannot be deleted
  ✓ court with confirmed booking cannot be deleted
  ✓ court with completed booking cannot be deleted
  ✓ court with cancelled or expired booking cannot be deleted
  ✓ court can be deactivated and activated
  ✓ inactive court cannot be booked and not in catalog
  ✓ past booking history still accessible after court deactivated

----------------------------------------------------------------------
TOTAL HASIL: 67 passed, 200 assertions
DURASI: 32.58s
STATUS AKHIR: 100% SUCCESS / PRODUCTION-READY
```

---

## 6. Serah Terima & Rekomendasi (QA Handover)

1. **Deploy & Push Sinkronisasi:** Seluruh file dokumentasi (`.pdf`, `.docx`, `.md`) tersimpan di dalam direktori `QA/`.
2. **Cron Job di Railway:** Konfigurasikan cron job untuk mengeksekusi `php artisan bookings:expire-pending` secara reguler.
3. **Kredensial Default:**
   - **Admin:** `admin@sportbooking.test` / `password123`
   - **Customer:** `customer@sportbooking.test` / `password123`
