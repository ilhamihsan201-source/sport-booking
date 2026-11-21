<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sport;
use App\Models\Venue;
use App\Models\Court;
use App\Models\Facility;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SportBookingFeatureTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected User $customer;
    protected User $otherCustomer;
    protected Court $court;
    protected Venue $venue;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin Test',
            'email' => 'admin@test.com',
            'role' => 'admin',
        ]);

        $this->customer = User::factory()->create([
            'name' => 'Customer Test',
            'email' => 'customer@test.com',
            'role' => 'customer',
        ]);

        $this->otherCustomer = User::factory()->create([
            'name' => 'Other Customer',
            'email' => 'other@test.com',
            'role' => 'customer',
        ]);

        $sport = Sport::create([
            'name' => 'Padel',
            'slug' => 'padel',
            'description' => 'Olahraga Padel',
            'is_active' => true,
        ]);

        $this->venue = Venue::create([
            'name' => 'Sport Center Jakarta',
            'slug' => 'sport-center-jakarta',
            'address' => 'Jl. Sudirman No. 1',
            'city' => 'Jakarta Selatan',
            'phone' => '081234567890',
            'is_active' => true,
        ]);

        for ($d = 0; $d <= 6; $d++) {
            $this->venue->operatingHours()->create([
                'day_of_week' => $d,
                'opens_at' => '06:00:00',
                'closes_at' => '23:00:00',
                'is_closed' => false,
            ]);
        }

        $this->court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $sport->id,
            'name' => 'Padel Court 1',
            'slug' => 'padel-court-1',
            'price_per_hour' => 200000,
            'status' => 'active',
            'capacity' => 4,
        ]);
    }

    public function test_landing_page_loads_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('SportBooking');
    }

    public function test_customer_cannot_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get('/admin/dashboard');
        $response->assertStatus(403);
    }

    public function test_admin_can_access_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/admin/dashboard');
        $response->assertStatus(200);
        $response->assertSee('Dashboard Administrator');
    }

    public function test_smart_dashboard_redirects_admin_to_admin_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get('/dashboard');
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_smart_dashboard_redirects_customer_to_customer_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get('/dashboard');
        $response->assertRedirect(route('customer.dashboard'));
    }

    public function test_admin_can_view_courts_and_create_court(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.courts.index'));
        $response->assertStatus(200);
        $response->assertSee('Padel Court 1');

        $response = $this->actingAs($this->admin)->post(route('admin.courts.store'), [
            'venue_id' => $this->court->venue_id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Mini Soccer Arena Test',
            'price_per_hour' => 350000,
            'status' => 'active',
            'capacity' => 14,
        ]);

        $response->assertRedirect(route('admin.courts.index'));
        $this->assertDatabaseHas('courts', ['name' => 'Mini Soccer Arena Test']);
    }

    public function test_customer_can_view_own_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get(route('customer.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Dashboard Saya');
    }

    public function test_customer_can_create_booking_successfully(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        $response = $this->actingAs($this->customer)->post(route('booking.store'), [
            'court_id' => $this->court->id,
            'booking_date' => $tomorrow,
            'start_time' => '10:00',
            'duration_hours' => 2,
            'customer_name' => 'Customer Test',
            'customer_email' => 'customer@test.com',
            'customer_phone' => '081234567890',
        ]);

        $this->assertDatabaseHas('bookings', [
            'court_id' => $this->court->id,
            'start_time' => '10:00:00',
            'status' => 'pending_payment',
            'customer_email' => 'customer@test.com',
        ]);
    }

    public function test_double_booking_same_slot_is_rejected(): void
    {
        $tomorrow = now()->addDay()->toDateString();

        // Booking 1 dibuat dari jam 14:00 - 16:00
        Booking::create([
            'booking_code' => 'SB-EXISTING-01',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => 'Existing Customer',
            'customer_phone' => '0811111111',
            'booking_date' => $tomorrow,
            'start_time' => '14:00:00',
            'end_time' => '16:00:00',
            'duration_hours' => 2,
            'price_per_hour' => 200000,
            'total_price' => 400000,
            'status' => 'confirmed',
        ]);

        // Booking 2 mencoba memesan overlap: jam 15:00 - 16:00
        $response = $this->actingAs($this->otherCustomer)->post(route('booking.store'), [
            'court_id' => $this->court->id,
            'booking_date' => $tomorrow,
            'start_time' => '15:00',
            'duration_hours' => 1,
            'customer_name' => 'Other Customer',
            'customer_phone' => '0822222222',
        ]);

        $response->assertSessionHasErrors(['start_time']);
    }

    public function test_customer_can_upload_payment_proof(): void
    {
        Storage::fake('local');

        $booking = Booking::create([
            'booking_code' => 'SB-UPLOAD-TEST',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
        ]);

        $file = UploadedFile::fake()->image('transfer_receipt.jpg');

        $response = $this->actingAs($this->customer)->post(route('payments.store', $booking->booking_code), [
            'sender_name' => 'Customer Test',
            'reference_number' => 'REF123456',
            'proof' => $file,
        ]);

        $response->assertRedirect(route('payments.create', $booking->booking_code));

        $this->assertDatabaseHas('payments', [
            'booking_id' => $booking->id,
            'sender_name' => 'Customer Test',
            'status' => 'pending',
        ]);
    }

    public function test_admin_can_verify_payment_and_confirms_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-TEST-001',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'duration_hours' => 2,
            'price_per_hour' => 200000,
            'total_price' => 400000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'bank_transfer',
            'amount' => 400000,
            'status' => 'pending',
            'sender_name' => 'Customer Test',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.verify', $payment));
        $response->assertSessionHas('success');

        $this->assertEquals('verified', $payment->fresh()->status);
        $this->assertEquals('confirmed', $booking->fresh()->status);
        $this->assertNull($booking->fresh()->expires_at);
    }

    public function test_admin_can_reject_payment_with_reason(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-REJECT-TEST',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
        ]);

        $payment = Payment::create([
            'booking_id' => $booking->id,
            'payment_method' => 'bank_transfer',
            'amount' => 200000,
            'status' => 'pending',
            'sender_name' => 'Customer Test',
            'paid_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.payments.reject', $payment), [
            'rejection_reason' => 'Bukti transfer tidak terbaca / buram.',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('rejected', $payment->fresh()->status);
        $this->assertEquals('Bukti transfer tidak terbaca / buram.', $payment->fresh()->rejection_reason);
        // Booking tetap pending_payment sehingga customer dapat upload ulang
        $this->assertEquals('pending_payment', $booking->fresh()->status);
    }

    public function test_cannot_pay_expired_booking(): void
    {
        Storage::fake('local');

        $booking = Booking::create([
            'booking_code' => 'SB-EXPIRED-TEST',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
            'expires_at' => now()->subMinutes(10), // Sudah kedaluwarsa
        ]);

        $file = UploadedFile::fake()->image('receipt.jpg');

        $response = $this->actingAs($this->customer)->post(route('payments.store', $booking->booking_code), [
            'sender_name' => 'Customer Test',
            'proof' => $file,
        ]);

        $response->assertSessionHasErrors(['proof']);
    }

    public function test_admin_can_cancel_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-TEST-002',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.bookings.cancel', $booking), [
            'cancellation_reason' => 'Permintaan pembatalan dari customer melalui telepon.',
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('Permintaan pembatalan dari customer melalui telepon.', $booking->fresh()->cancellation_reason);
    }

    public function test_admin_can_complete_booking_after_play_time_ended(): void
    {
        // Booking kemarin yang sudah confirmed
        $booking = Booking::create([
            'booking_code' => 'SB-PAST-001',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->subDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '12:00:00',
            'duration_hours' => 2,
            'price_per_hour' => 200000,
            'total_price' => 400000,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.bookings.complete', $booking));
        $response->assertSessionHas('success');
        $this->assertEquals('completed', $booking->fresh()->status);
    }

    public function test_idor_protection_customer_cannot_access_other_customer_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-IDOR-TEST',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
        ]);

        // Customer lain mencoba mengakses detail booking milik customer utama
        $response = $this->actingAs($this->otherCustomer)->get(route('booking.show', $booking->booking_code));
        $response->assertStatus(403);

        // Customer lain mencoba mengakses halaman pembayaran booking milik customer utama
        $response = $this->actingAs($this->otherCustomer)->get(route('payments.create', $booking->booking_code));
        $response->assertStatus(403);
    }

    public function test_admin_can_manage_venues(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.venues.store'), [
            'name' => 'Surabaya Arena Padel',
            'city' => 'Surabaya',
            'address' => 'Jl. Pemuda No. 10',
            'phone' => '0819999999',
            'is_active' => true,
        ]);

        $response->assertRedirect(route('admin.venues.index'));
        $this->assertDatabaseHas('venues', ['name' => 'Surabaya Arena Padel']);
    }

    public function test_admin_can_manage_facilities(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.facilities.store'), [
            'name' => 'Penyewaan Raket & Bola',
            'description' => 'Tersedia raket padel standar turnamen',
        ]);

        $response->assertRedirect(route('admin.facilities.index'));
        $this->assertDatabaseHas('facilities', ['name' => 'Penyewaan Raket & Bola']);
    }

    public function test_admin_can_view_payments_overview(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.payments.index'));
        $response->assertStatus(200);
        $response->assertSee('Verifikasi Pembayaran');
    }

    public function test_customer_can_cancel_own_pending_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-CANCEL-OWN',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
        ]);

        $response = $this->actingAs($this->customer)->post(route('booking.cancel', $booking->booking_code));
        $response->assertSessionHas('success');

        $this->assertEquals('cancelled', $booking->fresh()->status);
        $this->assertEquals('Dibatalkan oleh pelanggan.', $booking->fresh()->cancellation_reason);
    }

    public function test_customer_cannot_cancel_confirmed_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-NO-CANCEL-CONFIRMED',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->customer)->post(route('booking.cancel', $booking->booking_code));
        $response->assertSessionHas('error');

        $this->assertEquals('confirmed', $booking->fresh()->status);
    }

    public function test_customer_cannot_cancel_other_customer_booking(): void
    {
        $booking = Booking::create([
            'booking_code' => 'SB-NO-CANCEL-OTHER',
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => '08123456789',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 200000,
            'total_price' => 200000,
            'status' => 'pending_payment',
        ]);

        $response = $this->actingAs($this->otherCustomer)->post(route('booking.cancel', $booking->booking_code));
        $response->assertStatus(403);
    }

    public function test_admin_can_upload_and_manage_court_images(): void
    {
        Storage::fake('public');

        $imageFile = UploadedFile::fake()->image('court_photo.jpg');

        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.store', $this->court), [
            'image' => $imageFile,
            'alt_text' => 'Foto Lapangan 1',
            'is_primary' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('court_images', [
            'court_id' => $this->court->id,
            'alt_text' => 'Foto Lapangan 1',
            'is_primary' => true,
        ]);

        $image = $this->court->images()->first();

        // Admin set primary
        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.primary', $image));
        $response->assertSessionHas('success');

        // Admin delete image
        $response = $this->actingAs($this->admin)->delete(route('admin.courts.images.destroy', $image));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('court_images', ['id' => $image->id]);
    }

    public function test_expire_pending_bookings_command(): void
    {
        $expiredBooking = Booking::create([
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'booking_code' => 'SB-TEST-EXP-1',
            'customer_name' => 'John Expired',
            'customer_phone' => '0812999991',
            'booking_date' => now()->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 100000,
            'total_price' => 100000,
            'status' => 'pending_payment',
            'expires_at' => now()->subMinutes(10),
        ]);

        $this->artisan('bookings:expire-pending')
            ->assertExitCode(0);

        $expiredBooking->refresh();
        $this->assertEquals('expired', $expiredBooking->status);
    }

    public function test_expire_pending_bookings_does_not_expire_future_or_confirmed_or_verified_bookings(): void
    {
        // 1. Future booking
        $futureBooking = Booking::create([
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'booking_code' => 'SB-TEST-FUT-1',
            'customer_name' => 'John Future',
            'customer_phone' => '0812999992',
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 100000,
            'total_price' => 100000,
            'status' => 'pending_payment',
            'expires_at' => now()->addMinutes(15),
        ]);

        // 2. Confirmed booking
        $confirmedBooking = Booking::create([
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'booking_code' => 'SB-TEST-CONF-1',
            'customer_name' => 'John Confirmed',
            'customer_phone' => '0812999993',
            'booking_date' => now()->toDateString(),
            'start_time' => '16:00:00',
            'end_time' => '17:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 100000,
            'total_price' => 100000,
            'status' => 'confirmed',
            'expires_at' => now()->subMinutes(10),
        ]);

        // 3. Pending booking but has verified payment
        $verifiedPaymentBooking = Booking::create([
            'user_id' => $this->customer->id,
            'court_id' => $this->court->id,
            'booking_code' => 'SB-TEST-VER-1',
            'customer_name' => 'John Verified Pay',
            'customer_phone' => '0812999994',
            'booking_date' => now()->toDateString(),
            'start_time' => '18:00:00',
            'end_time' => '19:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 100000,
            'total_price' => 100000,
            'status' => 'pending_payment',
            'expires_at' => now()->subMinutes(10),
        ]);

        $verifiedPaymentBooking->payments()->create([
            'payment_method' => 'bank_transfer',
            'amount' => 100000,
            'proof_path' => 'payment-proofs/test.jpg',
            'status' => 'verified',
            'sender_name' => 'John Verified Pay',
            'paid_at' => now(),
        ]);

        $this->artisan('bookings:expire-pending')
            ->assertExitCode(0);

        $futureBooking->refresh();
        $confirmedBooking->refresh();
        $verifiedPaymentBooking->refresh();

        $this->assertEquals('pending_payment', $futureBooking->status);
        $this->assertEquals('confirmed', $confirmedBooking->status);
        $this->assertEquals('pending_payment', $verifiedPaymentBooking->status);
    }

    public function test_admin_can_upload_multiple_court_images(): void
    {
        Storage::fake('public');

        $images = [
            UploadedFile::fake()->image('court_1.jpg'),
            UploadedFile::fake()->image('court_2.png'),
            UploadedFile::fake()->image('court_3.webp'),
        ];

        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.store', $this->court), [
            'images' => $images,
            'alt_text' => 'Batch Image Upload',
            'is_primary' => true,
        ]);

        $response->assertSessionHas('success');
        $this->assertCount(3, $this->court->images);
        $this->assertTrue($this->court->images()->where('is_primary', true)->exists());
    }

    public function test_admin_cannot_upload_more_than_five_court_images_or_invalid_files(): void
    {
        Storage::fake('public');

        // Test > 5 images
        $tooManyImages = [];
        for ($i = 0; $i < 6; $i++) {
            $tooManyImages[] = UploadedFile::fake()->image("img_{$i}.jpg");
        }

        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.store', $this->court), [
            'images' => $tooManyImages,
        ]);
        $response->assertSessionHasErrors('images');

        // Test invalid MIME (pdf)
        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.store', $this->court), [
            'images' => [UploadedFile::fake()->create('document.pdf', 100, 'application/pdf')],
        ]);
        $response->assertSessionHasErrors('images.0');

        // Test oversized file (> 5120 KB)
        $response = $this->actingAs($this->admin)->post(route('admin.courts.images.store', $this->court), [
            'images' => [UploadedFile::fake()->image('huge.jpg')->size(6000)],
        ]);
        $response->assertSessionHasErrors('images.0');
    }

    public function test_court_detail_displays_primary_and_gallery_thumbnails(): void
    {
        $this->court->images()->create([
            'image_path' => 'courts/photo1.jpg',
            'alt_text' => 'Photo 1',
            'is_primary' => true,
            'sort_order' => 1,
        ]);

        $this->court->images()->create([
            'image_path' => 'courts/photo2.jpg',
            'alt_text' => 'Photo 2',
            'is_primary' => false,
            'sort_order' => 2,
        ]);

        $response = $this->get(route('courts.show', $this->court->slug));
        $response->assertOk();
        $response->assertSee('main-court-photo');
        $response->assertSee('court-thumb');
        $response->assertSee('switchCourtPhoto');
    }

    public function test_branded_error_page_renders_cleanly(): void
    {
        $response404 = $this->get('/halaman-yang-pasti-tidak-ada-404');
        $response404->assertStatus(404);
        $response404->assertSee('404');
        $response404->assertSee('Halaman Tidak Tersedia');
        $response404->assertSee('SPORT');
        $response404->assertSee('BOOKING');
        $response404->assertSee('Kembali ke Beranda');
    }

    public function test_court_without_booking_can_be_deleted(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court To Delete',
            'slug' => 'court-to-delete',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        $facility = \App\Models\Facility::firstOrCreate(['name' => 'Test Locker', 'icon' => 'lock']);
        $court->facilities()->attach($facility->id);
        $court->images()->create([
            'image_path' => 'courts/temp_test.jpg',
            'alt_text' => 'Temp Image',
            'is_primary' => true,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courts.destroy', $court));

        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('courts', ['id' => $court->id]);
        $this->assertDatabaseMissing('court_facility', ['court_id' => $court->id]);
        $this->assertDatabaseMissing('court_images', ['court_id' => $court->id]);
    }

    public function test_court_with_pending_payment_booking_cannot_be_deleted(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court With Pending Booking',
            'slug' => 'court-with-pending-booking',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        Booking::create([
            'booking_code' => 'SB-TEST-PENDING-DEL',
            'user_id' => $this->customer->id,
            'court_id' => $court->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(2)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 150000,
            'total_price' => 150000,
            'status' => 'pending_payment',
            'expires_at' => now()->addHours(2),
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courts.destroy', $court));

        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('error', 'Lapangan tidak dapat dihapus karena memiliki riwayat booking. Silakan nonaktifkan lapangan jika tidak ingin menerima pemesanan baru.');
        $this->assertDatabaseHas('courts', ['id' => $court->id]);
    }

    public function test_court_with_confirmed_booking_cannot_be_deleted(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court With Confirmed Booking',
            'slug' => 'court-with-confirmed-booking',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        Booking::create([
            'booking_code' => 'SB-TEST-CONF-DEL',
            'user_id' => $this->customer->id,
            'court_id' => $court->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'booking_date' => now()->addDays(3)->toDateString(),
            'start_time' => '14:00:00',
            'end_time' => '15:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 150000,
            'total_price' => 150000,
            'status' => 'confirmed',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courts.destroy', $court));

        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('courts', ['id' => $court->id]);
    }

    public function test_court_with_completed_booking_cannot_be_deleted(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court With Completed Booking',
            'slug' => 'court-with-completed-booking',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        Booking::create([
            'booking_code' => 'SB-TEST-COMP-DEL',
            'user_id' => $this->customer->id,
            'court_id' => $court->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'booking_date' => now()->subDays(2)->toDateString(),
            'start_time' => '09:00:00',
            'end_time' => '10:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 150000,
            'total_price' => 150000,
            'status' => 'completed',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courts.destroy', $court));

        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('courts', ['id' => $court->id]);
    }

    public function test_court_with_cancelled_or_expired_booking_cannot_be_deleted(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court With Cancelled Booking',
            'slug' => 'court-with-cancelled-booking',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        Booking::create([
            'booking_code' => 'SB-TEST-CANC-DEL',
            'user_id' => $this->customer->id,
            'court_id' => $court->id,
            'customer_name' => 'Customer Test',
            'customer_phone' => '081234567890',
            'booking_date' => now()->subDay()->toDateString(),
            'start_time' => '16:00:00',
            'end_time' => '17:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 150000,
            'total_price' => 150000,
            'status' => 'cancelled',
            'cancellation_reason' => 'Customer cancelled',
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.courts.destroy', $court));

        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('courts', ['id' => $court->id]);
    }

    public function test_court_can_be_deactivated_and_activated(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court Toggle Status',
            'slug' => 'court-toggle-status',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        // Nonaktifkan
        $response = $this->actingAs($this->admin)->post(route('admin.courts.toggle-status', $court));
        $response->assertRedirect(route('admin.courts.index'));
        $response->assertSessionHas('success');
        $this->assertEquals('inactive', $court->fresh()->status);

        // Aktifkan kembali
        $response2 = $this->actingAs($this->admin)->post(route('admin.courts.toggle-status', $court));
        $response2->assertRedirect(route('admin.courts.index'));
        $response2->assertSessionHas('success');
        $this->assertEquals('active', $court->fresh()->status);
    }

    public function test_inactive_court_cannot_be_booked_and_not_in_catalog(): void
    {
        $inactiveCourt = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Inactive Court Secret',
            'slug' => 'inactive-court-secret',
            'price_per_hour' => 150000,
            'status' => 'inactive',
        ]);

        // Tidak muncul di katalog arena
        $responseArena = $this->get(route('arena.index'));
        $responseArena->assertOk();
        $responseArena->assertDontSee('Inactive Court Secret');

        // Tidak dapat diakses detailnya (404)
        $responseShow = $this->get(route('courts.show', $inactiveCourt->slug));
        $responseShow->assertStatus(404);

        // Availability API menolak dengan status 422
        $responseAvail = $this->getJson(route('booking.availability', [
            'court_id' => $inactiveCourt->id,
            'date' => now()->addDay()->toDateString(),
            'duration' => 1,
        ]));
        $responseAvail->assertStatus(422);

        // Booking submission menolak dengan validasi 422
        $responseBook = $this->post(route('booking.store'), [
            'court_id' => $inactiveCourt->id,
            'booking_date' => now()->addDay()->toDateString(),
            'start_time' => '10:00',
            'duration_hours' => 1,
            'customer_name' => 'Budi Santoso',
            'customer_phone' => '081234567890',
        ]);
        $responseBook->assertSessionHasErrors('court_id');
    }

    public function test_past_booking_history_still_accessible_after_court_deactivated(): void
    {
        $court = Court::create([
            'venue_id' => $this->venue->id,
            'sport_id' => $this->court->sport_id,
            'name' => 'Court With Historical Booking',
            'slug' => 'court-with-historical-booking',
            'price_per_hour' => 150000,
            'status' => 'active',
        ]);

        $booking = Booking::create([
            'booking_code' => 'SB-HIST-TEST-1',
            'user_id' => $this->customer->id,
            'court_id' => $court->id,
            'customer_name' => 'Customer Budi',
            'customer_phone' => '081234567890',
            'booking_date' => now()->subDays(5)->toDateString(),
            'start_time' => '10:00:00',
            'end_time' => '11:00:00',
            'duration_hours' => 1,
            'price_per_hour' => 150000,
            'total_price' => 150000,
            'status' => 'completed',
        ]);

        // Lapangan sekarang dinonaktifkan
        $court->update(['status' => 'inactive']);

        // Customer masih bisa melihat detail bookingnya
        $responseCust = $this->actingAs($this->customer)->get(route('booking.show', $booking->booking_code));
        $responseCust->assertOk();
        $responseCust->assertSee('SB-HIST-TEST-1');
        $responseCust->assertSee('Court With Historical Booking');

        // Admin masih bisa melihat detail booking tersebut
        $responseAdmin = $this->actingAs($this->admin)->get(route('admin.bookings.show', $booking));
        $responseAdmin->assertOk();
        $responseAdmin->assertSee('SB-HIST-TEST-1');
        $responseAdmin->assertSee('Court With Historical Booking');
    }
}
