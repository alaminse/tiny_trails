<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_broadcasts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')
                  ->constrained('rides')
                  ->cascadeOnDelete();
            $table->string('broadcast_area')->nullable();  // suburb/zone
            $table->timestamp('broadcasted_at')->nullable();
            $table->timestamp('expires_at')->nullable();   // auto-expire if not accepted
            $table->enum('status', [
                'open',      // waiting for driver to accept
                'filled',    // driver accepted
                'expired',   // no one accepted in time
                'cancelled', // BOH cancelled
            ])->default('open');
            $table->foreignId('broadcasted_by')            // BOH staff user id
                  ->constrained('users')
                  ->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_broadcasts');
    }
};
