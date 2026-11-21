
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('booking_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('reviewed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->string('payment_method')->default('bank_transfer');

            $table->decimal('amount', 12, 2);

            $table->string('proof_path')->nullable();

            $table->string('status')->default('pending');

            $table->string('sender_name')->nullable();
            $table->string('reference_number')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamp('paid_at')->nullable();
            $table->timestamp('verified_at')->nullable();

            $table->timestamps();

            $table->index(['booking_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
