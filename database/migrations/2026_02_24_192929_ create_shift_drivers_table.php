<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_drivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_shift_id')->constrained('driver_shifts')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->enum('status', ['assigned', 'confirmed', 'completed', 'absent'])->default('assigned');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['driver_shift_id', 'driver_id'], 'uniq_shift_driver');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_drivers');
    }
};
