<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_acceptances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shift_broadcast_id')
                  ->constrained('shift_broadcasts')
                  ->cascadeOnDelete();
            $table->foreignId('driver_id')
                  ->constrained('drivers')
                  ->cascadeOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->enum('status', [
                'accepted',   // driver took the job
                'cancelled',  // driver cancelled after accepting
            ])->default('accepted');
            $table->timestamps();

            // ✅ One acceptance per broadcast — prevents double booking
            $table->unique('shift_broadcast_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_acceptances');
    }
};
