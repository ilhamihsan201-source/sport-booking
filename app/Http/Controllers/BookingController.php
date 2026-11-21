<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Court;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Halaman pemilihan jadwal.
     */
    public function index()
    {
        $courts = Court::with(['sport', 'venue'])
            ->where('status', 'active')
            ->whereHas('venue', function ($query) {
                $query->where('is_active', true);
            })
            ->orderBy('name')
            ->get();

        return view('bookings.index', compact('courts'));
    }

    /**
     * Mengambil slot jadwal berdasarkan lapangan,
     * tanggal, dan durasi yang dipilih.
     */
    public function availability(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'integer', 'exists:courts,id'],
            'date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'duration' => ['required', 'integer', 'between:1,3'],
        ]);

        $court = Court::with(['venue.operatingHours'])
            ->find($validated['court_id']);

        if (!$court || $court->status !== 'active') {
            return response()->json([
                'message' => 'Lapangan sedang tidak aktif atau dalam pemeliharaan.',
                'slots' => [],
            ], 422);
        }

        if (!$court->venue || !$court->venue->is_active) {
            return response()->json([
                'message' => 'Venue sedang tidak aktif.',
                'slots' => [],
            ], 422);
        }

        $date = Carbon::createFromFormat('Y-m-d', $validated['date'])->startOfDay();
        $duration = (int) $validated['duration'];

        $operatingHour = $court->venue->operatingHours
            ->firstWhere('day_of_week', $date->dayOfWeek);

        if (
            !$operatingHour ||
            $operatingHour->is_closed ||
            !$operatingHour->opens_at ||
            !$operatingHour->closes_at
        ) {
            return response()->json([
                'message' => 'Venue tutup pada tanggal ini.',
                'slots' => [],
            ]);
        }

        $openingTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date->toDateString() . ' ' . $operatingHour->opens_at
        );

        $closingTime = Carbon::createFromFormat(
            'Y-m-d H:i:s',
            $date->toDateString() . ' ' . $operatingHour->closes_at
        );

        $bookings = Booking::query()
            ->where('court_id', $court->id)
            ->whereDate('booking_date', $date->toDateString())
            ->where(function ($query) {
                $query->where('status', 'confirmed')
                    ->orWhere(function ($pending) {
                        $pending->where('status', 'pending_payment')
                            ->whereNotNull('expires_at')
                            ->where('expires_at', '>', now());
                    });
            })
            ->get(['id', 'start_time', 'end_time', 'status', 'expires_at']);

        $slots = [];
        $cursor = $openingTime->copy();

        while ($cursor->copy()->addHours($duration)->lte($closingTime)) {
            $start = $cursor->copy();
            $end = $cursor->copy()->addHours($duration);

            $isBooked = $bookings->contains(function ($booking) use ($start, $end) {
                $bookingStart = Carbon::createFromFormat('H:i:s', $booking->start_time);
                $bookingEnd = Carbon::createFromFormat('H:i:s', $booking->end_time);

                return $start->lt($bookingEnd) && $end->gt($bookingStart);
            });

            $isPast = $date->isToday() && $start->lte(now());

            $slots[] = [
                'start_time' => $start->format('H:i'),
                'end_time' => $end->format('H:i'),
                'status' => $isPast
                    ? 'past'
                    : ($isBooked ? 'booked' : 'available'),
                'price' => (float) $court->price_per_hour * $duration,
            ];

            $cursor->addHour();
        }

        return response()->json([
            'court' => [
                'id' => $court->id,
                'name' => $court->name,
                'price_per_hour' => (float) $court->price_per_hour,
            ],
            'date' => $date->toDateString(),
            'duration' => $duration,
            'slots' => $slots,
        ]);
    }

    /**
     * Menyimpan booking baru dengan validasi ulang
     * dan perlindungan terhadap double booking.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'court_id' => ['required', 'integer', 'exists:courts,id'],
            'booking_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'duration_hours' => ['required', 'integer', 'between:1,3'],

            'customer_name' => ['required', 'string', 'min:3', 'max:100'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'customer_phone' => ['required', 'string', 'min:9', 'max:30'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $booking = DB::transaction(function () use ($validated) {

            // Kunci baris lapangan agar proses booking pada
            // lapangan yang sama berjalan bergantian.
            $court = Court::query()
                ->lockForUpdate()
                ->find($validated['court_id']);

            if (!$court || $court->status !== 'active') {
                throw ValidationException::withMessages([
                    'court_id' => 'Lapangan sedang tidak aktif atau dalam pemeliharaan.',
                ]);
            }

            $court->load('venue.operatingHours');

            if (!$court->venue || !$court->venue->is_active) {
                throw ValidationException::withMessages([
                    'court_id' => 'Venue sedang tidak aktif.',
                ]);
            }

            $date = Carbon::createFromFormat(
                'Y-m-d',
                $validated['booking_date']
            )->startOfDay();

            $start = Carbon::createFromFormat(
                'Y-m-d H:i',
                $date->toDateString() . ' ' . $validated['start_time']
            );

            $end = $start->copy()->addHours(
                (int) $validated['duration_hours']
            );

            if ($start->lte(now())) {
                throw ValidationException::withMessages([
                    'start_time' => 'Jam bermain tersebut sudah lewat.',
                ]);
            }

            // Periksa jam operasional venue.
            $hours = $court->venue->operatingHours
                ->firstWhere('day_of_week', $date->dayOfWeek);

            if (
                !$hours ||
                $hours->is_closed ||
                !$hours->opens_at ||
                !$hours->closes_at
            ) {
                throw ValidationException::withMessages([
                    'booking_date' => 'Venue tutup pada tanggal tersebut.',
                ]);
            }

            $opening = Carbon::parse(
                $date->toDateString() . ' ' . $hours->opens_at
            );

            $closing = Carbon::parse(
                $date->toDateString() . ' ' . $hours->closes_at
            );

            if ($start->lt($opening) || $end->gt($closing)) {
                throw ValidationException::withMessages([
                    'start_time' => 'Jadwal berada di luar jam operasional.',
                ]);
            }

            // Periksa benturan jadwal.
            $conflict = Booking::query()
                ->where('court_id', $court->id)
                ->whereDate('booking_date', $date->toDateString())
                ->where(function ($query) {
                    $query->where('status', 'confirmed')
                        ->orWhere(function ($pending) {
                            $pending->where('status', 'pending_payment')
                                ->whereNotNull('expires_at')
                                ->where('expires_at', '>', now());
                        });
                })
                ->where('start_time', '<', $end->format('H:i:s'))
                ->where('end_time', '>', $start->format('H:i:s'))
                ->exists();

            if ($conflict) {
                throw ValidationException::withMessages([
                    'start_time' => 'Maaf, jadwal baru saja dipesan. Silakan pilih jam lain.',
                ]);
            }

            $price = (float) $court->price_per_hour;
            $duration = (int) $validated['duration_hours'];

            return Booking::create([
                'booking_code' => 'SB-' . now()->format('ymd') . '-' . Str::upper(Str::random(8)),
                'user_id' => Auth::id(),
                'court_id' => $court->id,

                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'] ?? null,
                'customer_phone' => $validated['customer_phone'],

                'booking_date' => $date->toDateString(),
                'start_time' => $start->format('H:i:s'),
                'end_time' => $end->format('H:i:s'),
                'duration_hours' => $duration,

                'price_per_hour' => $price,
                'total_price' => $price * $duration,

                'status' => 'pending_payment',
                'customer_notes' => $validated['customer_notes'] ?? null,

                // Reservasi sementara selama 15 menit.
                'expires_at' => now()->addMinutes(15),
            ]);
        }, 3);

        session()->put('guest_booking_' . $booking->id, true);

        return redirect()
            ->route('booking.show', $booking->booking_code)
            ->with('success', 'Booking berhasil dibuat. Silakan lanjutkan pembayaran.');
    }

    /**
     * Menampilkan detail booking.
     */
    public function show(Booking $booking)
    {
        if (!$booking->isAuthorizedForAccess(auth()->user())) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses data booking ini.');
            }
            abort(403, 'Anda tidak memiliki hak akses untuk melihat booking ini.');
        }

        $booking->load(['court.sport', 'court.venue', 'payments']);

        return view('bookings.show', compact('booking'));
    }

    /**
     * Membatalkan booking oleh pelanggan jika masih menunggu pembayaran.
     */
    public function customerCancel(Request $request, Booking $booking)
    {
        if (!$booking->isAuthorizedForAccess(auth()->user())) {
            abort(403, 'Anda tidak memiliki hak akses untuk membatalkan booking ini.');
        }

        if ($booking->status !== 'pending_payment') {
            return back()->with('error', 'Hanya booking yang menunggu pembayaran yang dapat dibatalkan langsung oleh pelanggan.');
        }

        DB::transaction(function () use ($booking) {
            $locked = Booking::query()->lockForUpdate()->findOrFail($booking->id);

            if ($locked->status !== 'pending_payment') {
                return;
            }

            $locked->status = 'cancelled';
            $locked->cancellation_reason = 'Dibatalkan oleh pelanggan.';
            $locked->expires_at = null;
            $locked->save();
        });

        return back()->with('success', 'Booking berhasil dibatalkan.');
    }
}
