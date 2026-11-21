<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Booking extends Model
{
    protected $fillable = [
        'booking_code',
        'user_id',
        'court_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'booking_date',
        'start_time',
        'end_time',
        'duration_hours',
        'price_per_hour',
        'total_price',
        'status',
        'customer_notes',
        'cancellation_reason',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'date',
            'duration_hours' => 'integer',
            'price_per_hour' => 'decimal:2',
            'total_price' => 'decimal:2',
            'expires_at' => 'datetime',
        ];
    }

    public function court(): BelongsTo
    {
        return $this->belongsTo(Court::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function payments(): HasMany
{
    return $this->hasMany(Payment::class);
}

    public function isBlocking(): bool
    {
        if (in_array($this->status, ['confirmed', 'completed'])) {
            return true;
        }

        if ($this->status === 'pending_payment') {
            return $this->expires_at !== null && $this->expires_at->isFuture();
        }

        return false;
    }

    /**
     * Memeriksa otorisasi akses customer/guest terhadap data booking.
     */
    public function isAuthorizedForAccess(?User $user = null): bool
    {
        // Administrator memiliki hak akses penuh
        if ($user && $user->isAdmin()) {
            return true;
        }

        // Jika booking terikat pada akun pengguna terdaftar
        if ($this->user_id !== null) {
            return $user !== null && (int)$user->id === (int)$this->user_id;
        }

        // Jika booking dibuat pada sesi saat ini
        if (session('guest_booking_' . $this->id)) {
            return true;
        }

        // Jika pengguna login memiliki email yang sama persis, kaitkan akun secara otomatis
        if ($user && $this->customer_email && strtolower($user->email) === strtolower($this->customer_email)) {
            $this->update(['user_id' => $user->id]);
            return true;
        }

        return false;
    }
}