<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    /**
     * Tampilkan daftar seluruh pelanggan terdaftar.
     */
    public function index(Request $request): View
    {
        $query = User::where('role', 'customer')
            ->withCount('bookings')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->query('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate(12)->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Tampilkan detail pelanggan dan riwayat aktivitas pemesanannya.
     */
    public function show(User $customer): View
    {
        abort_unless($customer->role === 'customer', 404);

        $customer->loadCount('bookings');

        $bookings = $customer->bookings()
            ->with(['court.sport', 'court.venue', 'payments'])
            ->latest()
            ->paginate(10);

        return view('admin.customers.show', compact('customer', 'bookings'));
    }
}
