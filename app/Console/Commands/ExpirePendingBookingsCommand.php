<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Booking;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ExpirePendingBookingsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookings:expire-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Batalkan secara otomatis booking pending yang telah melewati batas waktu expires_at';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $now = now();

        // Cari booking dengan status pending_payment yang telah melewati batas waktu
        // dan belum memiliki pembayaran terverifikasi
        $expiredCount = 0;

        $pendingBookings = Booking::query()
            ->where('status', 'pending_payment')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->whereDoesntHave('payments', function ($query) {
                $query->where('status', 'verified');
            })
            ->get();

        foreach ($pendingBookings as $booking) {
            DB::transaction(function () use ($booking, &$expiredCount, $now) {
                $locked = Booking::query()
                    ->lockForUpdate()
                    ->find($booking->id);

                if (
                    $locked &&
                    $locked->status === 'pending_payment' &&
                    $locked->expires_at !== null &&
                    $locked->expires_at < $now
                ) {
                    // Pastikan tidak ada pembayaran verified yang masuk bersamaan
                    $hasVerifiedPayment = $locked->payments()
                        ->where('status', 'verified')
                        ->exists();

                    if (!$hasVerifiedPayment) {
                        $locked->status = 'expired';
                        $locked->cancellation_reason = 'Waktu batas pembayaran telah kedaluwarsa secara otomatis.';
                        $locked->save();
                        $expiredCount++;
                    }
                }
            });
        }

        $this->info("Proses selesai: {$expiredCount} booking pending telah diubah statusnya menjadi expired.");

        return Command::SUCCESS;
    }
}
