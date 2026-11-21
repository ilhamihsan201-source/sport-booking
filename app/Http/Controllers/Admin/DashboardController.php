<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | Statistik utama
        |--------------------------------------------------------------------------
        */

        $totalBookings = DB::table('bookings')->count();

        $todayBookings = DB::table('bookings')
            ->whereDate('booking_date', $today)
            ->count();

        $pendingBookings = DB::table('bookings')
            ->where('status', 'pending_payment')
            ->count();

        $confirmedBookings = DB::table('bookings')
            ->whereIn('status', ['confirmed', 'completed'])
            ->count();

        $totalCustomers = DB::table('users')
            ->where('role', 'customer')
            ->count();

        $totalCourts = DB::table('courts')->count();

        $activeCourts = DB::table('courts')
            ->where('status', 'active')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Statistik pembayaran
        |--------------------------------------------------------------------------
        */

        $totalRevenue = DB::table('payments')
            ->where('status', 'verified')
            ->sum('amount');

        $pendingPayments = DB::table('payments')
            ->where('status', 'pending')
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Grafik 6 bulan terakhir
        |--------------------------------------------------------------------------
        */

        $chartStart = Carbon::now()
            ->startOfMonth()
            ->subMonths(5);

        $chartEnd = Carbon::now()->endOfMonth();

        $driver = DB::connection()->getDriverName();
        $verifiedFormat = $driver === 'sqlite' ? "strftime('%Y-%m', verified_at)" : 'DATE_FORMAT(verified_at, "%Y-%m")';
        $bookingFormat = $driver === 'sqlite' ? "strftime('%Y-%m', booking_date)" : 'DATE_FORMAT(booking_date, "%Y-%m")';

        $revenueRows = DB::table('payments')
            ->selectRaw("{$verifiedFormat} as month_key, SUM(amount) as total")
            ->where('status', 'verified')
            ->whereNotNull('verified_at')
            ->whereBetween('verified_at', [
                $chartStart,
                $chartEnd,
            ])
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $bookingRows = DB::table('bookings')
            ->selectRaw("{$bookingFormat} as month_key, COUNT(*) as total")
            ->whereBetween('booking_date', [
                $chartStart->toDateString(),
                $chartEnd->toDateString(),
            ])
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $chart = [];

        for ($i = 0; $i < 6; $i++) {
            $month = $chartStart->copy()->addMonths($i);

            $key = $month->format('Y-m');

            $chart[] = [
                'label' => $month->translatedFormat('M'),
                'revenue' => (float) ($revenueRows[$key] ?? 0),
                'bookings' => (int) ($bookingRows[$key] ?? 0),
            ];
        }

        $maxRevenue = max(
            collect($chart)->max('revenue'),
            1
        );

        /*
        |--------------------------------------------------------------------------
        | Booking terbaru
        |--------------------------------------------------------------------------
        */

        $recentBookings = DB::table('bookings')
            ->join('courts', 'bookings.court_id', '=', 'courts.id')
            ->select(
                'bookings.id',
                'bookings.booking_code',
                'bookings.customer_name',
                'bookings.booking_date',
                'bookings.start_time',
                'bookings.end_time',
                'bookings.total_price',
                'bookings.status',
                'courts.name as court_name'
            )
            ->orderByDesc('bookings.created_at')
            ->limit(8)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | Pembayaran menunggu verifikasi
        |--------------------------------------------------------------------------
        */

        $paymentReviews = DB::table('payments')
            ->join(
                'bookings',
                'payments.booking_id',
                '=',
                'bookings.id'
            )
            ->join(
                'courts',
                'bookings.court_id',
                '=',
                'courts.id'
            )
            ->select(
                'payments.id',
                'payments.amount',
                'payments.sender_name',
                'payments.reference_number',
                'payments.paid_at',
                'bookings.booking_code',
                'bookings.customer_name',
                'courts.name as court_name'
            )
            ->where('payments.status', 'pending')
            ->orderByDesc('payments.paid_at')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'totalBookings',
            'todayBookings',
            'pendingBookings',
            'confirmedBookings',
            'totalCustomers',
            'totalCourts',
            'activeCourts',
            'totalRevenue',
            'pendingPayments',
            'chart',
            'maxRevenue',
            'recentBookings',
            'paymentReviews'
        ));
    }
}
