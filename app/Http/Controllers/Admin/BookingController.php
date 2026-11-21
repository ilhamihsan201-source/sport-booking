<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class BookingController extends Controller
{
    /**
     * Daftar booking admin.
     */
    public function index(Request $request)
    {
        $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'status' => [
                'nullable',
                'in:pending_payment,confirmed,completed,cancelled,expired',
            ],
            'date' => ['nullable', 'date_format:Y-m-d'],
        ]);

        $bookings = Booking::query()
            ->leftJoin('courts', 'bookings.court_id', '=', 'courts.id')
            ->leftJoin('sports', 'courts.sport_id', '=', 'sports.id')
            ->select([
                'bookings.*',
                'courts.name as court_name',
                'sports.name as sport_name',
            ])
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->input('search');

                $query->where(function ($q) use ($search) {
                    $q->where('bookings.booking_code', 'like', "%{$search}%")
                        ->orWhere('bookings.customer_name', 'like', "%{$search}%")
                        ->orWhere('bookings.customer_phone', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('bookings.status', $request->status);
            })
            ->when($request->filled('date'), function ($query) use ($request) {
                $query->whereDate('bookings.booking_date', $request->date);
            })
            ->orderByDesc('bookings.created_at')
            ->paginate(15)
            ->withQueryString();

        return view('admin.bookings.index', compact('bookings'));
    }

    /**
     * Detail booking admin.
     */
    public function show(Booking $booking)
    {
        $booking->load([
            'court.sport',
            'court.venue',
        ]);

        $payments = Payment::query()
            ->with('reviewer')
            ->where('booking_id', $booking->id)
            ->latest()
            ->get();

        return view('admin.bookings.show', compact(
            'booking',
            'payments'
        ));
    }

    /**
     * Batalkan booking.
     */
    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $validated = $request->validate([
            'cancellation_reason' => [
                'required',
                'string',
                'min:5',
                'max:1000',
            ],
        ]);

        DB::transaction(function () use ($booking, $validated) {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if (!in_array($booking->status, [
                'pending_payment',
                'confirmed',
            ])) {
                throw ValidationException::withMessages([
                    'booking' => 'Booking ini tidak dapat dibatalkan karena statusnya sudah berubah.',
                ]);
            }

            $booking->status = 'cancelled';
            $booking->cancellation_reason =
                $validated['cancellation_reason'];

            $booking->expires_at = null;
            $booking->save();
        });

        return back()->with(
            'success',
            'Booking berhasil dibatalkan.'
        );
    }

    /**
     * Selesaikan booking setelah waktu bermain berakhir.
     */
    public function complete(Booking $booking): RedirectResponse
    {
        DB::transaction(function () use ($booking) {
            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($booking->id);

            if ($booking->status !== 'confirmed') {
                throw ValidationException::withMessages([
                    'booking' => 'Hanya booking yang sudah dikonfirmasi yang dapat diselesaikan.',
                ]);
            }

            $endDateTime = Carbon::parse(
                $booking->booking_date->format('Y-m-d')
                . ' '
                . $booking->end_time
            );

            if (now()->lt($endDateTime)) {
                throw ValidationException::withMessages([
                    'booking' => 'Booking belum bisa diselesaikan karena waktu bermain belum berakhir.',
                ]);
            }

            $booking->status = 'completed';
            $booking->save();
        });

        return back()->with(
            'success',
            'Booking berhasil ditandai selesai.'
        );
    }
}
