<?php
// ══════════════════════════════════════════════════════════════════
// MIGRATION 2: Create driver_shifts table
// File: database/migrations/2026_02_24_000002_create_driver_shifts_table.php
// ══════════════════════════════════════════════════════════════════

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('driver_shifts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')
                  ->constrained('drivers')
                  ->cascadeOnDelete();

            $table->date('date');

            // 1 = Morning, 2 = Midday, 3 = Afternoon
            $table->unsignedTinyInteger('shift_number');
            $table->string('shift_label', 20)->nullable(); // "Morning" etc

            $table->time('start_time');   // 07:00
            $table->time('end_time');     // 09:00

            // Seat tracking — vehicle_type.max_capacity থেকে নেওয়া হবে
            $table->unsignedTinyInteger('max_seats')->default(4);
            $table->unsignedTinyInteger('booked_seats')->default(0);
            $table->unsignedTinyInteger('instant_seats')->default(0); // instant এর জন্য reserve

            $table->enum('status', [
                'draft',      // তৈরি কিন্তু confirm হয়নি
                'confirmed',  // confirm — driver কে জানানো হয়েছে
                'active',     // চলছে এখন
                'completed',  // শেষ
                'cancelled',  // বাতিল
            ])->default('draft');

            $table->text('notes')->nullable();

            $table->foreignId('created_by')
                  ->constrained('users')
                  ->cascadeOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // একজন driver একদিনে একটি shift_number একবারই পাবে
            $table->unique(['driver_id', 'date', 'shift_number'], 'uniq_driver_date_shift');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('driver_shifts');
    }
};
