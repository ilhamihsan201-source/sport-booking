<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('court_facility', function (Blueprint $table) {
            $table->id();

            $table->foreignId('court_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('facility_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique(['court_id', 'facility_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('court_facility');
    }
};