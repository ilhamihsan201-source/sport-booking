<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ArenaController;
use App\Http\Controllers\CourtController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\PaymentController as AdminPaymentController;
use App\Http\Controllers\Admin\BookingController as AdminBookingController;
use App\Http\Controllers\Admin\CourtController as AdminCourtController;
use App\Http\Controllers\Admin\CustomerController as AdminCustomerController;
use App\Http\Controllers\Admin\VenueController as AdminVenueController;
use App\Http\Controllers\Admin\FacilityController as AdminFacilityController;
use App\Http\Controllers\Admin\CourtImageController as AdminCourtImageController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use Illuminate\Support\Facades\Route;

// BERANDA
Route::get('/', [HomeController::class, 'index'])
    ->name('home');

// ARENA
Route::get('/arena', [ArenaController::class, 'index'])
    ->name('arena.index');

Route::get('/arena/{sport:slug}', [ArenaController::class, 'show'])
    ->name('arena.show');

// DETAIL LAPANGAN
Route::get('/lapangan/{court:slug}', [CourtController::class, 'show'])
    ->name('courts.show');

// JADWAL
Route::get('/jadwal', [BookingController::class, 'index'])
    ->name('booking.index');

Route::get('/jadwal/availability', [BookingController::class, 'availability'])
    ->name('booking.availability');

// PEMESANAN
Route::post('/booking', [BookingController::class, 'store'])
    ->name('booking.store');

Route::get('/booking/{booking:booking_code}', [BookingController::class, 'show'])
    ->name('booking.show');

Route::post('/booking/{booking:booking_code}/cancel', [BookingController::class, 'customerCancel'])
    ->name('booking.cancel');

// PEMBAYARAN
Route::get('/booking/{booking:booking_code}/pembayaran', [
    PaymentController::class,
    'create'
])->name('payments.create');

Route::post('/booking/{booking:booking_code}/pembayaran', [
    PaymentController::class,
    'store'
])->name('payments.store');

// ADMIN DASHBOARD
Route::middleware(['auth', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])
            ->name('dashboard');

        Route::get('/bookings', [\App\Http\Controllers\Admin\BookingController::class, 'index'])
            ->name('bookings.index');

        Route::get('/bookings/{booking}', [\App\Http\Controllers\Admin\BookingController::class, 'show'])
            ->name('bookings.show');

        Route::post(
            '/bookings/{booking}/cancel',
            [\App\Http\Controllers\Admin\BookingController::class, 'cancel']
        )->name('bookings.cancel');

        Route::post(
            '/bookings/{booking}/complete',
            [\App\Http\Controllers\Admin\BookingController::class, 'complete']
        )->name('bookings.complete');

        // DAFTAR PEMBAYARAN
        Route::get('/payments', [AdminPaymentController::class, 'index'])
            ->name('payments.index');

        Route::post(
            '/payments/{payment}/verify',
            [AdminPaymentController::class, 'verify']
        )->name('payments.verify');

        Route::post(
            '/payments/{payment}/reject',
            [AdminPaymentController::class, 'reject']
        )->name('payments.reject');

        Route::get(
            '/payments/{payment}/proof',
            [AdminPaymentController::class, 'proof']
        )->name('payments.proof');

        // MANAJEMEN LAPANGAN
        Route::post('/courts/{court}/toggle-status', [AdminCourtController::class, 'toggleStatus'])
            ->name('courts.toggle-status');
        Route::resource('courts', AdminCourtController::class)->except(['show']);

        // FOTO LAPANGAN
        Route::post('/courts/{court}/images', [AdminCourtImageController::class, 'store'])
            ->name('courts.images.store');
        Route::post('/court-images/{image}/primary', [AdminCourtImageController::class, 'setPrimary'])
            ->name('courts.images.primary');
        Route::delete('/court-images/{image}', [AdminCourtImageController::class, 'destroy'])
            ->name('courts.images.destroy');

        // MANAJEMEN VENUE
        Route::resource('venues', AdminVenueController::class)->except(['show']);

        // MANAJEMEN FASILITAS
        Route::resource('facilities', AdminFacilityController::class)->except(['show']);

        // MANAJEMEN CUSTOMER
        Route::get('/customers', [AdminCustomerController::class, 'index'])
            ->name('customers.index');

        Route::get('/customers/{customer}', [AdminCustomerController::class, 'show'])
            ->name('customers.show');
    });

// CUSTOMER DASHBOARD
Route::middleware(['auth', 'customer'])
    ->prefix('customer')
    ->name('customer.')
    ->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])
            ->name('dashboard');
    });

// SMART DASHBOARD REDIRECTOR
Route::get('/dashboard', function () {
    if (auth()->user()?->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('customer.dashboard');
})->middleware(['auth'])->name('dashboard');

// PROFILE
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])
        ->name('profile.edit');

    Route::patch('/profile', [ProfileController::class, 'update'])
        ->name('profile.update');

    Route::delete('/profile', [ProfileController::class, 'destroy'])
        ->name('profile.destroy');
});

require __DIR__.'/auth.php';
