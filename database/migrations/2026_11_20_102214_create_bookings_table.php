<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();

            $table->string('booking_code')->unique();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('court_id')
                ->constrained()
                ->restrictOnDelete();

            // Informasi pelanggan
            $table->string('customer_name');
            $table->string('customer_email')->nullable();
            $table->string('customer_phone', 30);

            // Jadwal booking
            $table->date('booking_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('duration_hours');

            // Snapshot harga
            $table->decimal('price_per_hour', 12, 2);
            $table->decimal('total_price', 12, 2);

            // Status reservasi
            $table->string('status')->default('pending_payment');

            $table->text('customer_notes')->nullable();
            $table->text('cancellation_reason')->nullable();

            // Batas waktu pembayaran
            $table->timestamp('expires_at')->nullable();

            $table->timestamps();

            $table->index([
                'court_id',
                'booking_date',
                'status',
            ]);

            $table->index([
                'booking_date',
                'start_time',
            ]);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};