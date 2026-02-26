<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// ══════════════════════════════════════════════════════════════════
// MIGRATION 4: Add driver_shift_id to rides table
// File: database/migrations/2026_02_24_000004_add_driver_shift_id_to_rides.php
// ══════════════════════════════════════════════════════════════════

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->foreignId('driver_shift_id')
                  ->nullable()
                  ->after('ride_assign_id')
                  ->constrained('driver_shifts')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rides', function (Blueprint $table) {
            $table->dropForeign(['driver_shift_id']);
            $table->dropColumn('driver_shift_id');
        });
    }
};
