<?php
// ══════════════════════════════════════════════════════════════════
// MIGRATION 3: driver_shift_rides pivot table
// File: database/migrations/2026_02_24_000003_create_driver_shift_rides_table.php
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_shift_rides', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_shift_id')
                  ->constrained('driver_shifts')
                  ->cascadeOnDelete();

            $table->foreignId('ride_id')
                  ->constrained('rides')
                  ->cascadeOnDelete();

            $table->unsignedTinyInteger('seat_number');

            $table->enum('type', [
                'scheduled', // subscription থেকে pre-assign
                'instant',   // real-time request
            ])->default('scheduled');

            $table->timestamp('assigned_at')->useCurrent();
            $table->timestamps();

            // একটি ride শুধু একটি shift এ থাকবে
            $table->unique('ride_id', 'uniq_ride_in_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_shift_rides');
    }
};
