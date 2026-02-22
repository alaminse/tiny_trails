<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timesheets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')
                  ->constrained('drivers')
                  ->cascadeOnDelete();
            $table->foreignId('ride_id')
                  ->constrained('rides')
                  ->cascadeOnDelete();
            $table->date('date');
            $table->time('shift_start')->nullable();
            $table->time('shift_end')->nullable();
            $table->decimal('hours_worked', 5, 2)->nullable(); // e.g. 3.50
            $table->enum('status', [
                'pending',    // auto-created when ride completes
                'approved',   // super admin approved
                'rejected',   // super admin rejected
            ])->default('pending');
            $table->foreignId('approved_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timesheets');
    }
};
