# SportBooking

**SportBooking** is a web-based sports court booking platform built with Laravel. It allows customers to explore sports facilities, check court availability, make bookings, and submit payment proofs. Administrators can manage courts, facilities, bookings, and payment verification.

## Features

### Customer
- Customer registration, login, and logout
- Browse Padel and Mini Soccer courts
- View court details, photos, facilities, and prices
- Check court availability and booking schedules
- Create and manage bookings
- Upload payment proof
- Track booking and payment status
- View booking history
- Manage customer profile

### Administrator
- Admin dashboard
- Manage venues and operating hours
- Create, update, and manage courts
- Manage court photos and facilities
- Manage court prices and availability status
- View and filter customer bookings
- Verify or reject payment submissions
- Manage booking cancellation and completion
- Manage customer information

## Technology Stack

- PHP 8.2+
- Laravel 12
- MySQL
- Blade Templates
- Tailwind CSS
- Vite
- JavaScript

## Installation

### 1. Clone the repository

```bash
git clone https://github.com/YOUR_USERNAME/sport-booking.git
cd sport-booking
```

### 2. Install dependencies

```bash
composer install
npm install
```

### 3. Configure environment

```bash
copy .env.example .env
php artisan key:generate
```

Update your `.env` file with your database configuration.

### 4. Run database migrations and seeders

```bash
php artisan migrate --seed
```

### 5. Build frontend assets

```bash
npm run build
```

### 6. Create storage link

```bash
php artisan storage:link
```

### 7. Start the application

```bash
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser.

## Automated Tests

Run the test suite:

```bash
php artisan test
```

## Security Notes

- Environment secrets are excluded from version control.
- Payment proof files are stored privately.
- Role-based access control separates customer and administrator permissions.
- Booking creation validates availability and prevents overlapping reservations.
- Booking prices are calculated on the server.

## Project Purpose

SportBooking was developed as a portfolio project to demonstrate Laravel application development, database relationships, authentication, authorization, CRUD operations, booking workflows, payment verification, and responsive web interface design.

---

**SportBooking — Book Your Game. Play Your Best.**