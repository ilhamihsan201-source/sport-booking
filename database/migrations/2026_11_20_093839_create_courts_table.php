<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('courts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('venue_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('sport_id')
                ->constrained()
                ->restrictOnDelete();

            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();

            $table->decimal('price_per_hour', 12, 2);
            $table->unsignedSmallInteger('capacity')->nullable();

            $table->string('surface_type')->nullable();
            $table->string('size')->nullable();

            $table->string('status')->default('active');
            $table->boolean('is_featured')->default(false);
            $table->timestamps();

            $table->index(['venue_id', 'sport_id']);
            $table->index(['status', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('courts');
    }
};