<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    /**
     * Tampilkan seluruh daftar pembayaran yang masuk.
     */
    public function index(Request $request): View
    {
        $query = Payment::with(['booking.court.sport', 'reviewer'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        $payments = $query->paginate(15)->withQueryString();

        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Verifikasi pembayaran dan konfirmasi booking.
     */
    public function verify(Payment $payment): RedirectResponse
    {
        DB::transaction(function () use ($payment) {
            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($payment->booking_id);

            if ($payment->status !== 'pending') {
                abort(422, 'Pembayaran ini sudah diperiksa.');
            }

            if ($booking->status !== 'pending_payment') {
                abort(422, 'Booking tidak lagi menunggu pembayaran.');
            }

            if ($booking->expires_at && $booking->expires_at->isPast()) {
                abort(422, 'Booking sudah melewati batas waktu pembayaran.');
            }

            if ((float) $payment->amount !== (float) $booking->total_price) {
                abort(422, 'Jumlah pembayaran tidak sesuai total booking.');
            }

            $payment->status = 'verified';
            $payment->reviewed_by = Auth::id();
            $payment->verified_at = now();
            $payment->rejection_reason = null;
            $payment->save();

            $booking->status = 'confirmed';
            $booking->expires_at = null;
            $booking->save();
        });

        return back()->with(
            'success',
            'Pembayaran berhasil diverifikasi. Booking telah dikonfirmasi.'
        );
    }

    /**
     * Tolak bukti pembayaran.
     */
    public function reject(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'min:5', 'max:1000'],
        ]);

        DB::transaction(function () use ($payment, $validated) {
            $payment = Payment::query()
                ->lockForUpdate()
                ->findOrFail($payment->id);

            $booking = Booking::query()
                ->lockForUpdate()
                ->findOrFail($payment->booking_id);

            if ($payment->status !== 'pending') {
                abort(422, 'Pembayaran ini sudah diperiksa.');
            }

            if ($booking->status !== 'pending_payment') {
                abort(422, 'Booking tidak lagi menunggu pembayaran.');
            }

            $payment->status = 'rejected';
            $payment->reviewed_by = Auth::id();
            $payment->verified_at = now();
            $payment->rejection_reason = $validated['rejection_reason'];
            $payment->save();
        });

        return back()->with(
            'success',
            'Bukti pembayaran ditolak. Customer dapat melihat alasan penolakan.'
        );
    }

    /**
     * Tampilkan bukti pembayaran yang tersimpan secara privat.
     */
    public function proof(Payment $payment): Response
    {
        abort_unless($payment->proof_path, 404);

        abort_unless(
            Storage::disk('local')->exists($payment->proof_path),
            404
        );

        return Storage::disk('local')->response(
            $payment->proof_path,
            basename($payment->proof_path),
            ['Cache-Control' => 'private, no-store'],
            'inline'
        );
    }
}
