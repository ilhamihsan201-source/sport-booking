<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class PaymentController extends Controller
{
    /**
     * Halaman checkout pembayaran.
     */
    public function create(Booking $booking)
    {
        if (!$booking->isAuthorizedForAccess(auth()->user())) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses pembayaran booking ini.');
            }
            abort(403, 'Anda tidak memiliki hak akses ke halaman pembayaran booking ini.');
        }

        $booking->load(['court.sport', 'court.venue', 'payments']);

        return view('payments.create', compact('booking'));
    }

    /**
     * Upload bukti transfer pelanggan.
     */
    public function store(Request $request, Booking $booking)
    {
        if (!$booking->isAuthorizedForAccess(auth()->user())) {
            if (!auth()->check()) {
                return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu untuk mengakses pembayaran booking ini.');
            }
            abort(403, 'Anda tidak memiliki hak akses ke halaman pembayaran booking ini.');
        }

        $validated = $request->validate([
            'sender_name' => ['required', 'string', 'min:3', 'max:100'],
            'reference_number' => ['nullable', 'string', 'max:100'],
            'proof' => [
                'required',
                'file',
                'mimes:jpg,jpeg,png,pdf',
                'max:5120',
            ],
        ]);

        // Simpan secara privat di storage/app/private.
        $path = $request->file('proof')
            ->store('payment-proofs', 'local');

        try {
            DB::transaction(function () use ($booking, $validated, $path) {

                $lockedBooking = Booking::query()
                    ->lockForUpdate()
                    ->findOrFail($booking->id);

                if ($lockedBooking->status !== 'pending_payment') {
                    throw ValidationException::withMessages([
                        'proof' => 'Booking ini tidak sedang menunggu pembayaran.',
                    ]);
                }

                if (
                    !$lockedBooking->expires_at ||
                    !$lockedBooking->expires_at->isFuture()
                ) {
                    throw ValidationException::withMessages([
                        'proof' => 'Masa berlaku booking sudah habis. Silakan buat booking baru.',
                    ]);
                }

                $latestPayment = $lockedBooking->payments()
                    ->latest()
                    ->lockForUpdate()
                    ->first();

                if ($latestPayment && in_array(
                    $latestPayment->status,
                    ['pending', 'verified']
                )) {
                    throw ValidationException::withMessages([
                        'proof' => 'Bukti pembayaran sebelumnya masih menunggu pemeriksaan atau sudah diverifikasi.',
                    ]);
                }

                Payment::create([
                    'booking_id' => $lockedBooking->id,
                    'payment_method' => 'bank_transfer',
                    'amount' => $lockedBooking->total_price,
                    'proof_path' => $path,
                    'status' => 'pending',
                    'sender_name' => $validated['sender_name'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'paid_at' => now(),
                ]);
            }, 3);
        } catch (Throwable $exception) {
            Storage::disk('local')->delete($path);

            throw $exception;
        }

        return redirect()
            ->route('payments.create', $booking->booking_code)
            ->with('success', 'Bukti pembayaran berhasil dikirim dan sedang menunggu verifikasi admin.');
    }
}
