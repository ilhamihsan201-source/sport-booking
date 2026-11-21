<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Dashboard customer: menampilkan ringkasan akun dan riwayat booking.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        // Ambil booking milik user
        $query = $user->bookings()
            ->with(['court.sport', 'court.venue', 'payments' => function ($q) {
                $q->latest();
            }])
            ->latest();

        $allBookings = (clone $query)->get();

        // Statistik ringkas
        $stats = [
            'total' => $allBookings->count(),
            'pending' => $allBookings->where('status', 'pending_payment')->count(),
            'confirmed' => $allBookings->where('status', 'confirmed')->count(),
            'completed' => $allBookings->where('status', 'completed')->count(),
        ];

        // Filter status jika ada tab yang dipilih
        $status = $request->query('status');
        if ($status && in_array($status, ['pending_payment', 'confirmed', 'completed', 'cancelled', 'expired'])) {
            $query->where('status', $status);
        }

        $bookings = $query->paginate(10)->withQueryString();

        return view('customer.dashboard', compact('user', 'stats', 'bookings', 'status'));
    }
}
